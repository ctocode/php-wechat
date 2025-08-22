<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\exception\WeixinException;

/**
 * 微信 - 公众号 - 接入
 * --------------------
 * @link  文档地址
 * https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Access_Overview.html
 */
class Server_Connect extends WechatCommon
{
    // Token(令牌) 
    protected $server_url;
    protected $server_token;
    protected $server_encodingAesKey;
    protected $server_encrypt_type;

    public function setServerUrl($str = '')
    {
        $this->server_url = $str;
        return $this;
    }
    public function setServerToken($str = '')
    {
        $this->server_token = $str;
        return $this;
    }
    public function setServerEncodingAesKey($str = '')
    {
        $this->server_encodingAesKey = $str;
        return $this;
    }
    /**
     * 用于微信服务器验证
     * 验证签名 , 定义微信的回调方法
     * @param bool $return 是否返回
     * https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Access_Overview.html
     */
    public function checkSignature($str = '')
    {
        $signature = isset($_GET['signature']) ? $_GET['signature'] : '';
        // 如果存在加密验证则用加密验证段
        $signature = isset($_GET['msg_signature']) ? $_GET['msg_signature'] : $signature;
        $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
        $nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';
        $echoStr = !empty($_GET['echostr']) ? addslashes($_GET['echostr']) : '';
        if (empty($this->server_token)) {
            throw new WeixinException('Server TOKEN is not defined!');
        }
        $token = $this->server_token;
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce,
            $str
        );
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return !empty($echoStr) ? $echoStr : true;
        } else {
            return false;
        }
    }


    /**
     * 获取微信服务器IP地址列表
     * @return array('127.0.0.1','127.0.0.1')
     */
    public function getServerIp()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/getcallbackip', [
            'access_token' => $wxAccToken
        ]);
        return $result['ip_list'];
        return $result;
    }
    /**
     * 长链接转短链接接口
     * @param string $long_url 传入要转换的长url
     * @return boolean|string url 成功则返回转换后的短url
     */
    public function getShortUrl($long_url)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'action' => 'long2short',
            'long_url' => $long_url
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/shorturl?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url,  self::json_encode($data));
        return $result['short_url'];
        return $result;
    }
}
