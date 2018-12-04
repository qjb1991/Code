<?php
class pay {

    public function test(){
        $config = [
            'app_id' => '',
            'mch_id' => '',
            'key' => '',
            'notify_url' => '',
            'scene_info' => '{"store_info":{"id": "门店ID","name": "名称","area_code": "编码","address": "地址" }}',
            'unifiedorder_url' => 'https://api.mch.weixin.qq.com/pay/unifiedorder'
        ];
        $url = $config['unifiedorder_url'];     //统一下单地址
        //1.获取调用统一下单接口所需必备参数
        $appid = $config['app_id'];     //微信公众号appid
        $mch_id = $config['mch_id'];    //微信支付商户号
        $key = $config['key'];          //自己设置的微信商家key
        $out_trade_no = 'a1231654113132151'; //平台内部订单号
        $nonce_str = MD5($out_trade_no);        //随机字符串
        $body = '4盒子-充值';           //商品描述
        $total_fee = 100;             //付款金额，单位为分
        $spbill_create_ip = '218.19.205.169'; //获得用户设备IP
        $attach = 'weixin_H5';      //附加数据（自定义，在支付通知中原样返回）
        $notify_url = $config['notify_url']; //异步回调地址，需外网可以直接访问
        $trade_type = 'MWEB';       //交易类型，微信H5支付时固定为MWEB
        $scene_info = $config['scene_info'];//场景信息
        //2.将参数按照key=value的格式，并按照参数名ASCII字典序排序生成字符串
        $signA ="appid=$appid&attach=$attach&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
        //3.拼接字符串
        $strSignTmp = $signA."&key=$key";
        //4.MD5加密后转换成大写
        $sign = strtoupper(MD5($strSignTmp));
        //5.拼接成所需XML格式
        $post_data = "<xml> 
            <appid>$appid</appid> 
            <attach>$attach</attach> 
            <body>$body</body> 
            <mch_id>$mch_id</mch_id> 
            <nonce_str>$nonce_str</nonce_str> 
            <notify_url>$notify_url</notify_url> 
            <out_trade_no>$out_trade_no</out_trade_no> 
            <scene_info>$scene_info</scene_info>
            <spbill_create_ip>$spbill_create_ip</spbill_create_ip> 
            <total_fee>$total_fee</total_fee> 
            <trade_type>$trade_type</trade_type>
            <sign>$sign</sign> 
          </xml>";
        //6.以POST方式向微信传参，并取得微信返回的支付参数
//        Log::write('xml ---> '.$post_data);die;
        $dataxml = self::httpRequest($url, 'POST', $post_data);
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML转换成数组
        return $objectxml;
    }

    public static function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) {
        $method = strtoupper($method);
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
        curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case "POST":
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
                }
                break;
            default:
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
                break;
        }
        $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
        curl_setopt($ci, CURLOPT_URL, $url);
        if($ssl){
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($ci);
        $requestinfo = curl_getinfo($ci);
        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);
            echo "=====info===== \r\n";
            print_r($requestinfo);
            echo "=====response=====\r\n";
            print_r($response);
        }
        curl_close($ci);
        return $response;
    }

    public function getPay(){
        //1.引入支付类文件
        include_once "plugins/Payment/weixin/Weixin.class.php";
        $payment = new \Weixin();
        $order_id = I('order_id');
        //2.判断参数是否为空
        if (!empty($order_id)){
            //3.根据订单id查询订单是否存在
            $order = M('Order')->where(array('id'=>$order_id))->find();
            if ($order){//订单存在
                //4.判断该笔订单是否已经支付，如已支付则返回支付失败并给出相应提示
                if ($order['pay_status'] == '1'){
                    exit(json_encode(array('status'=>'205','msg'=>'该订单已支付，请勿重复提交！')));
                }
                $bodys = '订单：'.$order['order_sn'] . '支付';
                //5.调用支付类中封装的支付方法并对应传参
                $result = $payment->getCode($order,$bodys);
                //6.当return_code和result_code均为SUCCESS，代表下单成功，将支付参数返回
                if($result['return_code'] == 'SUCCESS'){
                    if($result['result_code'] == 'SUCCESS'){
                        exit(json_encode(array('status'=>'0','msg'=>'下单成功，请支付！','result'=>$result['mweb_url'])));
                    }elseif($result['result_code'] == 'FAIL'){
                        exit(json_encode(array('status'=>'-201','msg'=>$result['err_code_des'])));
                    }
                }else{
                    exit(json_encode(array('status'=>'-1','msg'=>'未知错误，请稍后重试！')));
                }
            }else{
                //报错:数据不存在
                exit(json_encode(array('status'=>'-200','msg'=>'订单不存在，请核实后再提交！')));
            }
        }else{
            //报错:缺少参数
            exit(json_encode(array('status'=>'-204','msg'=>'参数缺失，请核实！')));
        }
    }
}
