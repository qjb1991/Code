<?php
error_reporting( E_ALL || ~E_NOTICE );
define("TOKEN", "weixin");
include dirname(__FILE__)."/Lib/WeChatApi.class.php";
include dirname(__FILE__)."/Lib/WeChat.class.php";
$code = $_GET['code'];
$wechat = new WeChat();
//使用code获取网页access_to和openId
$data = $wechat->codeTransAccessInfo($code);
var_dump($data);
// $access_token = $data['access_token'];
// $openId = $data['openid'];
// //在网页中拉取用户信息
// $userInfo = $wechat->getUserInfo($access_token,$openid);
// var_dump($userInfo);