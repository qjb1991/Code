<?php
error_reporting( E_ALL || ~E_NOTICE );
define("TOKEN", "weixin");
include dirname(__FILE__)."/Lib/WeChatApi.class.php";
include dirname(__FILE__)."/Lib/WeChat.class.php";
$url = "http://www.eaimin.com/wx/test.php";
echo WeChatApi::setAccess($url);