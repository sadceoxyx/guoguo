<?php
class sohuWeibo extends smcHttp{
	public $oauth_1=true;
	public $client_id;
	public $json=false;
	public $client_secret;
	public $access_token;
	public $refresh_token;
	public $requestoken_url='http://api.t.sohu.com/oauth/request_token';
	public $authorize_url='http://api.t.sohu.com/oauth/authorize';
	public $accesstoken_url='http://api.t.sohu.com/oauth/access_token';
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
		$this->json=true;
		$params=array();
		$resp=$this->oAuthRequest('http://api.t.sohu.com/account/verify_credentials.json','GET',$params);
		if($resp['code']){
			throw new smcException($resp['error'].'（'.$resp['error_code'].'）');
		}
		$user_login=$resp['screen_name']?$resp['screen_name']:$resp['id'];
		$r=array(
			'profile_image_url'=>$resp['profile_image_url'],
			'user_login'=>$user_login,
			'uid'=>$resp['id'],
			'display_name'=>$resp['screen_name'],
			'url'=>'http://t.sohu.com/u/'.$resp['id'],
			'access_token'=>$this->access_token,
			'refresh_token'=>$this->refresh_token,
			'description'=>$resp['description'],
			'statuses_count'=>$resp['statuses_count'],
			'email'=>$resp['id'].'@'.'t.sohu.com',
			'weibo_slug'=>'sohuweibo'
		);
		return $r;
	}
	function publish_post($weibo_data){
		$this->json=true;
		if(is_array($weibo_data)){
			$weibo_data['tags']=smcHttp::convtags($weibo_data['tags'],true);
			$text=smcHttp::format_post_data($weibo_data,240,true);
		}else{
			$text=$weibo_data;
		}
		$params=array();
		$params['status']=$text;
		if(is_array($weibo_data) && $weibo_data['pic']){
			$params['pic']='@'.$weibo_data['pic'];$params['status']=urlencode($params['status']);
			$resp=$this->oAuthRequest('http://api.t.sohu.com/statuses/upload.json','POST',$params,true);
			if($resp['error']){
				return $this->publish_post($text);
			}
		}else $resp=$this->oAuthRequest('http://api.t.sohu.com/statuses/update.json','POST',$params);
		if($resp['id']){
			return $resp['id'];
		}else{
			return false;
		}
	}
}