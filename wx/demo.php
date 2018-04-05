<?php
//声明页面字符集
header("Content-type:text/html;charset=utf-8");
ini_set('display_errors','On');		//开发环境下开启错误回显,否则不会报错(白屏现象)
error_reporting( E_ALL || ~E_NOTICE );		//屏蔽notice级别错误
$buffer = ini_get('output_buffering');		 //获取output_buffering配置选项的值
if ($buffer) {
	ob_end_flush();		//输出缓冲区内容并关闭缓冲
}
define("TOKEN", "weixin");
include dirname(__FILE__)."/Lib/WeChatApi.class.php";
include dirname(__FILE__)."/Lib/WeChat.class.php";
$wechat = new WeChat();

// $data = [
// 	'username'	=>	'qjb',
// 	'age'	=> 18
// ];
// echo $wechat->CurlRequest('https://www.eaimin.com/wx/curl/post.php',$data);



// $url = "http://api.map.baidu.com/geocoder/v2/?location=23.123613,113.416482&output=json&pois=1&ak=D6yXzoeNTujsoVKo4yrI4U9gSjvGFqLp";
// $json = $wechat->CurlRequest($url);
// echo "<pre>";
// $adr = json_decode($json,true);
// print_r($adr);

// $url = "http://openapi.tuling123.com/openapi/api";
// $key = "9a8308f296874a3b9a6559f5795c0367";
// $data = [
// 	"info"	=>	"你是谁",		//用户输入的问题
// 	"key"	=>	$key,
// 	'userid'	=>	'12345678'	//测试号为12345678
// ];
// $json = $wechat->CurlPostJson($url,$data);
// $result = json_decode($json,true);	//转化为数组
// echo "<pre>";
// print_r($result);

// var_dump($wechat->GetAccessToken());

// $dsn = "mysql:host=localhost;dbname=wx;charset=utf8";
// $pdo = new PDO($dsn,'root','qiujunBO.000.');
// $stmt = $pdo -> prepare("select * from wx_user where openId=:openId");
// $openId = 'orQID1scUOT_1wYO4Bnb8hoCjrZM';
// $stmt -> execute([':openId'=>$openId]);
// $data = $stmt -> fetch( PDO::FETCH_ASSOC );
// if (empty($data)) {
// 	$createTime = time();
// 	$stmt = $pdo -> prepare("insert into wx_user (openId,createTime) value (:openId,:createTime)");
// 	$stmt -> execute([':openId'=>$openId,':createTime'=>$createTime]);
// 	echo 'register';
// }else{
// 	var_dump($data) ;
// }


$mongo = new MongoClient('mongodb://root:qiujunbo@localhost:27017/admin');
$db = $mongo -> SelectDb('wx');
$openId = 'aaaaa';
$data = $db -> wx_user -> findOne(['openId'=>$openId]);
if ($data) {
	var_dump($data) ;
}else{
	$createTime = time();
	$db -> wx_user -> insert(['openId'=>$openId, 'createTime'=>$createTime]);
	echo 'register';
}