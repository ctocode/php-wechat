<?php

namespace shiyunWechat\weixin_pay;


/**
 * App专属微信支付类
 */
class WxPaySdk
{
    const UFORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder'; // 获取预支付URL,prepayid.
    private $mchid; // 微信支付商户号
    private $mchkey; // 微信支付商户KEY
    private $openid; // 微信支付用户号
    private $_appID;
    private $_appSecret;
    private $out_trade_no;
    private $total_fee; // 总金额
    private $notify_url; // ND地址
    private $trade_type; // JSAPI

    // 微信参数设置
    public $set;

    private $curl_timeout;
    public $data; // 接收到的数据，类型为关联数组
    // 动态参数,返回参数，类型为关联数组
    private $parameters;
    // 非必填参数，商户可根据实际情况选填
    // $unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
    // $unifiedOrder->setParameter("device_info","XXXX");//设备号
    // $unifiedOrder->setParameter("attach","XXXX");//附加数据
    // $unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
    // $unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
    // $unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
    // $unifiedOrder->setParameter("openid","XXXX");//用户标识
    // $unifiedOrder->setParameter("product_id","XXXX");//商品ID

    // 微信CURL响应
    public $response; // 微信返回的响应
    public $result; // 返回参数，类型为关联数组

    //
    private $prepay_id; // 获取prepay_id
    public function __construct($options)
    {
        // $this->set = M('set')->find();

        $this->mchid = isset($options['mchid']) ? $options['mchid'] : '';
        $this->mchkey = isset($options['mchkey']) ? $options['mchkey'] : '';
        $this->openid = isset($options['openid']) ? $options['openid'] : '';
        $this->_appID = isset($options['appid']) ? $options['appid'] : '';
        $this->_appSecret = isset($options['appsecret']) ? $options['appsecret'] : '';
        // $this->access_token = isset($options['access_token'])?$options['access_token']:'';
        $this->out_trade_no = isset($options['out_trade_no']) ? $options['out_trade_no'] : '';
        $this->total_fee = isset($options['total_fee']) ? $options['total_fee'] : '';
        $this->notify_url = isset($options['notify_url']) ? $options['notify_url'] : '';
        $this->trade_type = isset($options['trade_type']) ? $options['trade_type'] : 'JSAPI';
        $this->curl_timeout = isset($options['curl_timeout']) ? $options['curl_timeout'] : '30';
    }

    /**
     * 获取prepay_id
     */
    function getPrepayId()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $this->postXml($url); // 追入获取链接
        $this->result = cc_xml_to_arr($this->response);
        $prepay_id = $this->result["prepay_id"];
        return $prepay_id;
    }

    /**
     * 	作用：设置JSAPI_prepay_id
     */
    function setPrepayId($prepayId)
    {
        $this->prepay_id = $prepayId;
    }

    /**
     * 	作用：设置jsapi的参数
     */
    public function getJSAPI()
    {
        // return $wOpt;
        // $this->parameters = json_encode($wOpt);
        // return $this->parameters;
        $timeStamp = time();
        $wOpt['appId'] = $this->_appID;
        $wOpt['timeStamp'] = "$timeStamp";
        // $wOpt["nonceStr"] = cc_random_lowernum(32);
        $wOpt['nonceStr'] = cc_random_lowernum(8);
        $wOpt['package'] = 'prepay_id=' . $this->prepay_id;
        $wOpt['signType'] = 'MD5';
        ksort($wOpt, SORT_STRING);
        $string = '';
        foreach ($wOpt as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $string .= "key=" . $this->mchkey;
        // $wOpt["paySign"] = $this->getSign($wOpt);
        $wOpt['paySign'] = strtoupper(md5($string));
        return $wOpt;
    }


    /**
     * 	作用：生成签名
     */
    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        // 签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        // echo '【string1】'.$String.'</br>';
        // 签名步骤二：在string后加入KEY
        // $String = $String . "&key=13903072727139030727271390307272";
        // $String = $String . "&key=" . $this->mchkey;
        $String = $String . "&key=" . $this->set['wxmchkey'];

        // echo "【string2】".$String."</br>";
        // 签名步骤三：MD5加密
        $String = md5($String);
        // echo "【string3】 ".$String."</br>";
        // 签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        // echo "【result】 ".$result_."</br>";
        return $result_;
    }
    /**
     * 	作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            // $buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
    function checkSign()
    {
        $tmpData = $this->data;
        unset($tmpData['sign']);
        $sign = $this->getSign($tmpData); // 本地签名
        if ($this->data['sign'] == $sign) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 	作用：设置请求参数
     */
    /**
     * 设置返回微信的xml数据
     */
    function setParameter($parameter, $parameterValue)
    {
        $str_1 = $this->trimString($parameter);
        $str_2 = $this->trimString($parameterValue);
        $this->parameters[$str_1] = $str_2;
    }
    // 取出
    public static function trimString($value)
    {
        $ret = null;
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }
    /**
     * 	作用：设置标配的请求参数，生成签名，生成接口参数xml
     */
    function createXml()
    {
        $this->parameters["appid"] = $this->_appID; // 公众账号ID
        $this->parameters["mch_id"] = $this->mchid; // 商户号
        $this->parameters["nonce_str"] = cc_random_lowernum(32); // 随机字符串
        $this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR']; // 终端ip
        $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
        return cc_arr_to_xml($this->parameters);
    }

    /**
     * 生成接口参数xml
     */
    function createXml2()
    {
        return cc_arr_to_xml($this->parameters);
    }
    /**
     * 将xml数据返回微信
     */
    function returnXml()
    {
        $returnXml = $this->createXml();
        return $returnXml;
    }
    /**
     * 	作用：post请求xml
     */
    function postXml($url)
    {
        $xml = $this->createXml();
        // dump($xml);
        $this->response = $this->postXmlCurl($xml, $url, $this->curl_timeout);
        // dump($this->response);
        return $this->response;
    }

    /**
     * 	作用：以post方式提交xml到对应的接口url
     */
    public function postXmlCurl($xml, $url, $second = 30)
    {
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOP_TIMEOUT, $second);
        // 这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        // 运行curl
        $data = curl_exec($ch);
        curl_close($ch);
        // 返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error" . "<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }
    /**
     * 	作用：使用证书，以post方式提交xml到对应的接口url
     */
    function postXmlSSLCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        // 超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        // 这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // 设置证书
        // 使用证书：cert 与 key 分别属于两个.pem文件
        // 默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, WxPayConf_pub::SSLCERT_PATH);
        // 默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, WxPayConf_pub::SSLKEY_PATH);
        // post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        // 返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error" . "<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }
}
