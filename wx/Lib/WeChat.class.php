<?php
class WeChat
{
	//客户端的openId
	protected $fromUsername;
	//服务器的id
	protected $toUsername;
	//客户端上传的信息
	protected $keyword;
	//客户端上传的类型
	protected $sendType;
	//订阅类型或者菜单CLICK事件推送
	protected $Event;
	//菜单事件推送的EventKey
	protected $EventKey;
	//语音内容
	protected $Recognition;
    //地理位置的纬度
	protected $lat;
    //地理位置的经度
	protected $lng;
	protected $time;
    //使用curl请求
	public function CurlRequest($url,$data=null){
        //初始化虚拟浏览器
        $ch = curl_init();
        #设置浏览器
        #false表示不需要验证就可以安全上传,true表示非安全需要验证
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        #设置要访问的url地址,把url的参数传值过来
        curl_setopt($ch, CURLOPT_URL, $url);
        #接收返回的数据为文本流(text/html,text/json,text/xml)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        #告诉服务端当前没有ssl认证服务器,一般固定为0
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        #告诉服务端当前没有可以验证的ssl证书地址,一般固定为false
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //data不为空,则使用post请求
        if ( !empty($data) ) {
            //post方式curl在php5.6以后会抛出提示,所以@屏蔽
            @curl_setopt($ch, CURLOPT_POST, true);      //设置请求方式为post
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);       //设置数据包
        }

        $result = curl_exec($ch);       //执行curl会话
        curl_close($ch);        //关闭会话
        return $result;
	}
    /**
     * 使用curl请求,参数格式为json
     * @param string $url  请求地址
     * @param array $data post过去的数据包
     */
    public function CurlPostJson($url,$data){
        //初始化虚拟浏览器
        $ch = curl_init();
        #设置浏览器
        #false表示不需要验证就可以安全上传,true表示非安全需要验证
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        #设置要访问的url地址,把url的参数传值过来
        curl_setopt($ch, CURLOPT_URL, $url);
        #接收返回的数据为文本流(text/html,text/json,text/xml)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        #告诉服务端当前没有ssl认证服务器,一般固定为0
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        #告诉服务端当前没有可以验证的ssl证书地址,一般固定为false
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        #设置当前的请求为post,设置为post后,有些php会抛出advice,因此要临时屏蔽
        @curl_setopt($ch, CURLOPT_POST, true);
        #要post过去的数据包是数组,然后需要把数组变成json
        $data = json_encode($data);
        $length = strlen($data);        //获取json的长度
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);       //设置数据包
        #告诉curl当前post的数据为json数据
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-type: application/json',       //告诉curl的http头部是json
                "Content-length: {$length}"     //json数据的长度
            )
        );
        $result = curl_exec($ch);       //执行curl会话
        curl_close($ch);        //关闭会话
        return $result;

    }
    #获取access_token
	// public function GetAccessToken(){
 //        $api = WeChatApi::getApiUrl('api_access_token');
 //        $res = $this->CurlRequest($api);
 //        $json = json_decode($res);
 //        return $json->access_token;
	// }
    #获取access_token,使用memcached
    // public function GetAccessToken(){
    //     $memcached = new memcached();
    //     //定义memcached的分布式服务器
    //     $servers = array(
    //         ['localhost','11211',100],
    //     );
    //     //连接服务器
    //     $memcached -> addServers($servers);
    //     if ($memcached -> get('access_token')) {
    //         //如果有缓存则直接返回access_token
    //         return $memcached -> get('access_token');
    //     }else{
    //         $api = WeChatApi::getApiUrl('api_access_token');
    //         $res = $this -> CurlRequest($api);
    //         $json = json_decode($res);
    //         $access_token = $json -> access_token;      //取得access_token
    //         $memcached -> set('access_token', $access_token, 3600);
    //         return $access_token;
    //     }
    // }
    #获取access_token,使用redis
    public function GetAccessToken(){
        $redis = new Redis();
        $redis -> connect('localhost', 6379);
        $redis -> auth('qiujunbo');     //密码验证
        $redis -> select(0);        //选择redis的0号数据库(redis有0~15共16个数据库)
        if ($redis -> get('access_token')) {
            return $redis -> get('access_token');       //有redis缓存则直接使用
        }else{
            $api = WeChatApi::getApiUrl('api_access_token');
            $res = $this ->CurlRequest($api);
            $json = json_decode($res);
            $access_token = $json -> access_token;      //获取access_token
            //设置redis缓存到string类型
            $redis -> set('access_token', $access_token);
            //设置有效期,,expire access_token 秒数
            $redis -> setTimeout('access_token', 3600);
            return $access_token;
        }
    }
	//自动回复(此方法必须覆盖)
	public function responseMsg(){
        //让api兼容高版本的PHP
        $dataFromClient = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");

		if (!empty($dataFromClient)){
			$postObj = simplexml_load_string($dataFromClient, 'SimpleXMLElement', LIBXML_NOCDATA);
            #fromUsername为微信客户端的openId,注册时自动生成
            $this -> fromUsername = $postObj->FromUserName;  
            #toUserName为服务器id,是动态改变由腾讯负责
            $this -> toUsername = $postObj->ToUserName;
            #keyword为微信客户端输入的内容
            $this -> keyword = trim($postObj->Content);
            $this -> sendType = trim($postObj->MsgType);    //客户端上传的类型
            $this -> Event = trim($postObj->MsgType)=='event' ? $postObj->Event : '';
                     
            $this -> Recognition = trim($postObj->MsgType)=='voice' ? $postObj->Recognition : '语音内容无法识别';
            $this -> EventKey = $postObj->Event=='CLICK' ? $postObj->EventKey : '';
            #地理位置的纬度
   			$this -> lat = trim($postObj->MsgType)=='location' ? $postObj->Location_X : '';
            #地理位置的经度
     		$this -> lng = trim($postObj->MsgType)=='location' ? $postObj->Location_Y : ''; 
            $this -> time = time();
		}
	}
    //文本回复接口
	protected function reText( $contentStr ){
		$resultStr = sprintf(WeChatApi::getMsgTpl('text'), $this->fromUsername, $this->toUsername, $this->time, 'text', $contentStr);
		echo $resultStr;	
	}
    //图片回复接口
	protected function reImage( $MediaId ){
		$resultStr = sprintf(WeChatApi::getMsgTpl('image'), $this->fromUsername, $this->toUsername, $this->time, 'image', $MediaId );
		echo $resultStr;
	}
    /**
     * 音乐回复接口
     * @param  string $title 标题
     * @param  string $desc  描述
     * @param  string $url   有损音乐链接地址
     * @param  string $hqurl 无损音乐链接地址
     */
	protected function reMusic( $title,$desc,$url,$hqurl ){
		$resultStr = sprintf(WeChatApi::getMsgTpl('music'), $this->fromUsername, $this->toUsername, $this->time, 'music', $title, $desc, $url, $hqurl);
        echo $resultStr;
	}
    /**
     * 视频回复接口
     * @param  string $MediaId 平台获取的media_id
     * @param  string $title   标题
     * @param  string $desc    描述
     */
    protected function reVideo($MediaId,$title,$desc){
        $resultStr = sprintf(WeChatApi::getMsgTpl('video'), $this->fromUsername, $this->toUsername, $this->time, 'video', $MediaId,$title,$desc);
        echo $resultStr;      
    }
    //图文回复接口
	protected function reNews($items){
		$count = count( $items );
		$item = $this -> createNewsItems($items);
		$resultStr = sprintf(WeChatApi::getMsgTpl('news'), $this->fromUsername, $this->toUsername, $this->time, 'news', $count,$item);
        echo $resultStr;
	}
    /*  
    图文回复接口用到的方法
    $items为数组,下标为
    Title           标题
    Desc    描述
    PicUrl          图片地址
    Url             连接地址
     */
	private function createNewsItems($items){
		foreach ($items as $data ) {
			$item .= "<item>
			<Title><![CDATA[{$data['Title']}]]></Title> 
			<Description><![CDATA[{$data['Desc']}]]></Description>
			<PicUrl><![CDATA[{$data['PicUrl']}]]></PicUrl>
			<Url><![CDATA[{$data['Url']}]]></Url>
			</item>";			
		}
		return $item;
	}
	protected function reSubscribe( $contentStr ){
		$this -> reText( $contentStr );
	}
    private function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
    }
    //客服消息发送接口
    protected function CustomerReText( $Text ){
    	$access_token = $this -> GetAccessToken();
    	$fromUsername = $this -> fromUsername;
    	$url = WeChatApi::getApiUrl('api_customer_send');
    	$url .= $access_token;
    	$content  = urlencode($Text);
        $data = array(
                "touser" => "{$fromUsername}" ,
                "msgtype"=>"text",
                "text" => array(
                    "content"=> $content,
                ),
        );
        $data = json_encode($data);
        $data = urldecode($data);
        $this -> CurlRequest( $url , $data );
        exit();
    }
    //图文回复接口
    protected function CustomerReImgText( $ImgText ){
     	$access_token = $this -> GetAccessToken();
    	$fromUsername = $this -> fromUsername;
    	$url = WeChatApi::getApiUrl('api_customer_send');
    	$url .= $access_token; 
    	$set = array();
        foreach ($ImgText as $rs){
            $content = null;
            $content = array(
                "title"=>urlencode($rs['title']),
                "description"=>urlencode($rs['desc']),
                "url"=>$rs['url'],
                "picurl"=>$rs['picurl'],
            );          
            $set[] = $content;           
        }
        $data = array(
            "touser"=>"{$fromUsername}",
            "msgtype"=>"news",
            "news" => array(
                "articles" => $set,
            ),
        );
        $data = json_encode($data);   
        $data = urldecode($data);    
        $this -> CurlRequest( $url , $data );
        exit();     	
    }
    //使用code换区access_token和openId
    public function codeTransAccessInfo($code=null){
    	if( isset($code) ){
    		$url = WeChatApi::getApiUrl('api_get_access_info');
    		$url .= $code;
			$str = $this -> CurlRequest( $url );
			$access_info = json_decode($str,true);
			return $access_info;
    	}else{
			exit("Error:must TransCode.");
    	}
    }
    public function SendMass($data){
    	$access_token = $this -> GetAccessToken();
    	$url = WeChatApi::getApiUrl('api_send_mass');
    	$url .= $access_token;
    	return $this -> CurlRequest( $url,$data );
    }
    public function vailAccessInfo($openId,$web_access_token)
    {
		$url = WeChatApi::getApiUrl('web_access_auth');
		$url .= "access_token={$web_access_token}&openid={$openId}";
		$str = $this -> CurlRequest( $url );
		$validInfo = json_decode($str,true);
		return $validInfo;
    }
    public function getUserInfo($web_access_token,$openId){
        $url = WeChatApi::getApiUrl('api_get_userinfo');
        $url .= "access_token={$web_access_token}&openid={$openId}&lang=zh_CN";
        $str = $this->CurlRequest( $url );
        $userInfo = json_decode($str,true);
        return $userInfo;
    }
    //媒体上传接口
    public function UploadMedia($media_data){
    	$access_token = $this -> GetAccessToken();
    	$url = WeChatApi::getApiUrl('api_upload_media');
    	$url .= $access_token;
    	$data['media'] = $media_data;
    	return $this -> CurlRequest($url,$data);
    }
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
           echo $echoStr;
           exit;
        }
    }
}