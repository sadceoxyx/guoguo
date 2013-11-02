<?php
class Douban extends smcHttp{
	public $oauth_1=true;
	public $client_id;
	public $json=false;
	public $client_secret;
	public $access_token;
	public $refresh_token;
	public $requestoken_url='http://www.douban.com/service/auth/request_token';
	public $authorize_url='http://www.douban.com/service/auth/authorize';
	public $accesstoken_url='http://www.douban.com/service/auth/access_token';
	function __construct($client_id,$client_secret,$access_token=NULL,$refresh_token=NULL){
		$this->client_id=$client_id;
		$this->client_secret=$client_secret;
		$this->access_token=$access_token;
		$this->refresh_token=$refresh_token;
	}
	function getAuthorizeURL($callback_url=''){
		$params = array();
		$resp=$this->oAuthRequest($this->requestoken_url,'GET',$params);
		if(is_string($resp))parse_str($resp,&$resp);
		if(!is_array($resp)||!$resp['oauth_token'])throw new smcException('Oauth Token获取失败');
		setcookie('smcOauth_secret_'.COOKIEHASH,$resp['oauth_token_secret'],0,COOKIEPATH,COOKIE_DOMAIN);
		$params['oauth_token']=$resp['oauth_token'];
		$params['oauth_callback']=$callback_url;
		return $this->authorize_url."?".http_build_query($params);
	}
	function getAccessToken($code='',$callback_url=''){
		$this->refresh_token=$_COOKIE['smcOauth_secret_'.COOKIEHASH];
		$params = array();
		$resp=$this->oAuthRequest($this->accesstoken_url, 'GET', $params);
		if(is_string($resp))parse_str($resp,&$resp);
		if($resp['oauth_token']){
			setcookie('smcOauth_secret_'.COOKIEHASH,'',time()-1,COOKIEPATH,COOKIE_DOMAIN);
			return array('access_token'=>$resp['oauth_token'],'refresh_token'=>$resp['oauth_token_secret'],'expires_in'=>'','access_time'=>time());
		}else{
			return array('error'=>'access token获取失败！');	
		}
	}
	function verify_credentials(){
		$params=array();$params['oauth_signature']='';
		$resp=$this->oAuthRequest('http://api.douban.com/people/%40me','GET',$params);
		$resp=@simplexml_load_string($resp);
		if(!$resp->id){
			throw new smcException('获取用户信息失败！');
		}
		$uid=str_replace('http://api.douban.com/people/','',$resp->id);
		$name=(string)$resp->title;
		$user_login=$name?$name:$uid;
		$r=array(
			'profile_image_url'=>'http://img3.douban.com/icon/u'.$uid.'.jpg',
			'user_login'=>$user_login,
			'uid'=>$uid,
			'display_name'=>$name,
			'url'=>'http://www.douban.com/people/'.$uid,
			'access_token'=>$this->access_token,
			'refresh_token'=>$this->refresh_token,
			'description'=>(string)$resp->content,
			'statuses_count'=>0,
			'email'=>$uid.'@'.'douban.com',
			'weibo_slug'=>'douban'
		);
		return $r;
	}
	function publish_post($weibo_data){
		if(is_array($weibo_data)){
			$weibo_data['tags']=smcHttp::convtags($weibo_data['tags'],true);
			$text=smcHttp::format_post_data($weibo_data,140,false);
		}else{
			$text=$weibo_data;
		}
		$this->oauth_1_header=true;
		$content='<?xml version="1.0" encoding="UTF-8"?>'.
			'<entry xmlns:ns0="http://www.w3.org/2005/Atom" xmlns:db="http://www.douban.com/xmlns/">'.
			'<content>'.htmlspecialchars($text).'</content>'.
			'</entry>';
		$headers=array("Content-Type"=>"application/atom+xml");
		$resp=$this->oAuthRequest('http://api.douban.com/miniblog/saying','POST',$content,false,$headers);
		$resp=@simplexml_load_string($resp);
		if($resp->id){
			return preg_replace('#^http.*/#','',$resp->id);
		}else{
			return false;
		}
	}
}