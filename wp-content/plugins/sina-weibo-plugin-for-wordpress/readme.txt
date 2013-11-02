=== Sina Weibo Plugin for WordPress ===
Contributors: zhlwish
Donate link: http://www.zhlwish.com/wp-plugin-for-sina-weibo/
Tags: sina, weibo, sns, 新浪微博, 新浪
Requires at least: 2.7
Tested up to: 3.2.1
Stable tag: 0.3.1


A very simple plugin shows the recent tweets from your Sina Weibo.


== Description ==

一个很简单的在Wordpress首页显示你的最近的新浪微博的插件

= The features in English =
* It has a widget, you can drag it drop it where you want in the side.Also you can set it how many tweetes you want to show.
* You do not need to input you Sina account and password, you just tell Sina that you want to use this plugin by click a link.
* It automaticly generate the link of the urls and @xxx in tweets.

If any help is needed, please checkout my tech blog: http://www.zhlwish.com

= The features in Chinese =
* 自带一个WordPress"小工具", 你可使用拖拽的方式直接指定插件显示的位置
* 使用新浪微博的授权, 不需要用户名和密码, 保证了你帐号的安全
* 自动转换微博中的URL和@符号

如果使用上需要什么帮助请, 查看我的博客: http://www.zhlwish.com

== Installation ==

= In English =
1. Upload to /wp-content/plugins/ directory;
1. Go to wordpress plugin page,active "新浪微博" plugin;
1. Go to "新浪微博" page under "Plugin" menu, click on "授权" link, you will be automaticly redirect to the authorize page of Sina Weibo, please follow the instructions of Sina Weibo;
1. Go to "Widget" page, you will find a widget named "新浪微博", drag it and drop it to the sidebar on right;
1. Set the "标题" and "显示微博的条数", and then go to the home page and find out what supprises you.

= In Chinese =
1. 上传到 /wp-content/plugins/ 目录;
1. 在Wordpress后台控制面板"插件"菜单下激活"新浪微博"插件;
1. 点击Wordpress后台控制面板"新浪微博"菜单, 点击"授权"链接, 页面将会转向到新浪微博授权页面, 请按照新浪微博的提示进行授权;
1. 在Wordpress后台控制面板"外观"菜单, 点击"小工具", 找到一个名叫"新浪微博"的小工具, 拖到到"侧边栏"中;
1. 设置标题和显示微博的条数, 然后到首页查看效果.

== Upgrade Notice ==

Nothing here

== Frequently Asked Questions ==

= What could I Do if I met an error message "Call to undefined function curl_init()" =

Please install or enable curl extension in PHP

== Screenshots ==

1. WordPress后台管理界面小工具菜单中拖放到侧边栏
2. 在WordPress默认模板下的首页显示效果

== Changelog ==

= 0.1 =
* 启动项目, 发布内测版本

= 0.2 =
* 修复了当用户还没有获得授权时首页报错
* 修复了当用户没有微博内容时首页保错

= 0.2.1 =
* 针对使用比较多的LightWord主题显示位置偏移做了修改

= 0.2.2 =
* 修正"关注我吧"链接

= 0.2.3 =
* 修正"*s秒前"多了个s的问题

= 0.2.4 =
* 修正了"*天前"链接无法访问的问题

= 0.3 =
* 修正32位服务器上新浪微博id长度显示为科学计数法形式，感谢yuanwencong.com网友提出此问题
* 修正部分主题css样式优先级高导致微博中的链接显示不正常的问题

= 0.3.1 =
* 修正当微博设置中取消授权后不能重新授权的错误，感谢justintseng.com网友提出此问题
