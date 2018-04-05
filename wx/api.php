<?php
error_reporting( E_ALL || ~E_NOTICE );
define("TOKEN", "weixin");
include dirname(__FILE__)."/Lib/WeChatApi.class.php";
include dirname(__FILE__)."/Lib/WeChat.class.php";
class WxApi extends Wechat
{
	public function responseMsg(){
		parent::responseMsg();
		//订阅自动回复接口
		if ($this->sendType=='event' && $this->Event=='subscribe') {
			$openId = $this->fromUsername;		//获取用户openId
			
			$res = $this->registerDB($openId);		//调用注册方法
			if ($res == 'register') {
				$content = "感谢关注!";
			}else{
				$content = "欢迎回来!";
			}
			$this->CustomerReText($content);
			exit;
		}
		if ($this->keyword == 'xxx') {
			$this -> CustomerReText("bbb");
			exit;
		}
		//菜单项相关功能
		if ($this->sendType=='event' && $this->Event=='CLICK') {
			if ($this->EventKey=='menus1') {
				$this->CustomerReText('菜单1');
				exit();
			}
			if ($this->EventKey=='sub1') {
				$this->CustomerReText('子菜单1');
				exit();
			}
		}

		// if ( !empty($this->keyword) ) {
		// 	$url = "http://openapi.tuling123.com/openapi/api";
		// 	$key = "9a8308f296874a3b9a6559f5795c0367";
		// 	$data = [
		// 		"info"	=>	$this->keyword,		//用户输入的问题
		// 		"key"	=>	$key,
		// 		'userid'	=>	'12345678'	//测试号为12345678
		// 	];
		// 	$json = $this->CurlPostJson($url,$data);
		// 	$result = json_decode($json,true);	//转化为数组
		// 	$this->reText($result['text']);		//返回图灵的文本结果
		// 	// $this->reText($result);		//返回图灵的文本结果

		// }

		// if ($this->sendType == 'location') {
		// 	$lat = $this->lat;
		// 	$lng = $this->lng;
		// 	$api = "http://api.map.baidu.com/geocoder/v2/?location={$lat},{$lng}&output=json&pois=1&ak=D6yXzoeNTujsoVKo4yrI4U9gSjvGFqLp";
		// 	$json = $this->CurlRequest($api);
		// 	$adr = json_decode($json,true);
		// 	$str = "您当前结构化地址为:\n".$adr['result']['formatted_address']."\n周边信息如下:\n";
		// 	foreach ($adr['result']['pois'] as $value) {
		// 		$str .= "【{$value['poiType']}】 {$value['name']}\n";
		// 	}
		// 	$this->reText($str);
		// }
		
		// if ($this->keyword == '比博') {
		// 	$this->reText("帅锅");
		// 	exit;
		// }
		// if ($this->keyword == '秋秋') {
		// 	$this->reImage("dsMLZler28Ynex2_g4nT218uSUeQJu-86LEGTdqXx2-dg2jHPpXRJ62gEsdYqy2a");
		// 	exit;
		// }
		// if ($this->keyword == '女神') {
		// 	$MediaId = "ScNDACqQq_HU5rgtUqDZdqKBUD_Z3VnYerw0A3QfxLjDa8vfjKSGsDi9Zhwn7TF4";
		// 	$title = "女神在这";
		// 	$desc = "真·女神";
		// 	$this->reVideo($MediaId,$title,$desc);
		// 	exit;
		// }
		// if ($this->keyword== '音乐') {
		// 	$title = 'boom';
		// 	$desc ='核爆神曲';
		// 	$url = 'http://eaimin.com/wx/music/boom.mp3';
		// 	$hqurl = 'http://eaimin.com/wx/music/boom.mp3';
		// 	$this->reMusic( $title,$desc,$url,$hqurl );
		// 	exit;
		// }

		// if ($this->sendType == 'voice') {
		// 	$content = $this->Recognition;
		// 	$this->reText("你发送的内容是".$content);
		// 	exit;
		// }

		// if ($this->keyword == '图文') {
		// 	$items = array(
		// 		[
		// 			'Title'		=>	'切格瓦拉',
		// 			'Desc'		=>	'医生、革命家、政治家、作家',
		// 			'PicUrl'	=>	'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1733943371,831132344&fm=58',
		// 			'Url'		=>	'https://baike.baidu.com/item/%E5%88%87%C2%B7%E6%A0%BC%E7%93%A6%E6%8B%89/7390?fr=aladdin&fromid=169236&fromtitle=%E5%88%87%E6%A0%BC%E7%93%A6%E6%8B%89'
		// 		],
		// 		[
		// 			'Title'		=>	'切格瓦拉',
		// 			'Desc'		=>	'医生、革命家、政治家、作家',
		// 			'PicUrl'	=>	'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1733943371,831132344&fm=58',
		// 			'Url'		=>	'https://baike.baidu.com/item/%E5%88%87%C2%B7%E6%A0%BC%E7%93%A6%E6%8B%89/7390?fr=aladdin&fromid=169236&fromtitle=%E5%88%87%E6%A0%BC%E7%93%A6%E6%8B%89'
		// 		],
		// 		[
		// 			'Title'		=>	'切格瓦拉',
		// 			'Desc'		=>	'医生、革命家、政治家、作家',
		// 			'PicUrl'	=>	'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1733943371,831132344&fm=58',
		// 			'Url'		=>	'https://baike.baidu.com/item/%E5%88%87%C2%B7%E6%A0%BC%E7%93%A6%E6%8B%89/7390?fr=aladdin&fromid=169236&fromtitle=%E5%88%87%E6%A0%BC%E7%93%A6%E6%8B%89'
		// 		],
		// 		[
		// 			'Title'		=>	'切格瓦拉',
		// 			'Desc'		=>	'医生、革命家、政治家、作家',
		// 			'PicUrl'	=>	'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1733943371,831132344&fm=58',
		// 			'Url'		=>	'https://baike.baidu.com/item/%E5%88%87%C2%B7%E6%A0%BC%E7%93%A6%E6%8B%89/7390?fr=aladdin&fromid=169236&fromtitle=%E5%88%87%E6%A0%BC%E7%93%A6%E6%8B%89'
		// 		]
		// 	);
		// 	$this->reNews($items);
		// 	exit;
		// }
	}
	//注册入库(MySQL)
	private function registerDB($openId){
		$dsn = "mysql:host=localhost;dbname=wx;charset=utf8";
		$pdo = new PDO($dsn,'root','qiujunBO.000.');
		$stmt = $pdo -> prepare("select * from wx_user where openId=:openId");
		$stmt -> execute([':openId'=>$openId]);
		$data = $stmt -> fetch( PDO::FETCH_ASSOC );
		if (empty($data)) {
			$createTime = time();
			$stmt = $pdo -> prepare("insert into wx_user (openId,createTime) value (:openId,:createTime)");
			$stmt -> execute([':openId'=>$openId,':createTime'=>$createTime]);
			return 'register';
		}else{
			return $data;
		}
	}
	//注册入库(MongoDB)
	// private function registerMG($openId){
	// 	$mongo = new MongoClient('mongodb://root:qiujunbo@localhost:27017/admin');
	// 	$db = $mongo -> SelectDb('wx');
	// 	$data = $db -> wx_user -> findOne(['openId'=>$openId]);
	// 	if ($data) {
	// 		return $data;
	// 	}else{
	// 		$createTime = time();
	// 		$db -> wx_user -> insert(['openId'=>$openId, 'createTime'=>$createTime]);
	// 		return 'register';
	// 	}
	// }
	
}
$WxApi = new WxApi();
//验证token,注释该代码表示打开自动回复功能,注释的话无法修改接口配置信息
//$WxApi ->valid();
$WxApi -> responseMsg();