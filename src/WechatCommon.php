<?php

namespace shiyunWechat;

use shiyunWechat\exception\WeixinException;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\libs\WeixinCache;

/**
 * 【ctocode】      微信 - 常用类
 * --------------------
 * @author       作者      ctocode
 * @version 	 版本	   v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */
class WechatCommon
{
    // 原始ID 申请公共号时系统给你的唯一编号，有此 i
    protected $_originalID = ''; // 微信号 original
    protected $_appID = ''; // 开通 api 服务时，系统给你的唯一编号
    protected $_appSecret = ''; // 开通 api 服务时你注册的用户名

    protected $_token_cache_key = 'wechat_access_token';
    public $access_token = ''; // 调用接口凭证


    // encodingAesKey AES 加密时的密钥
    protected $privatekey = ''; // 私钥
    protected $parameters = array();
    // 构造函数
    public function __construct($options = array())
    {
        $this->_appID = !empty($options['appid']) ? $options['appid'] : '';
        $this->_appSecret = !empty($options['appsecret']) ? $options['appsecret'] : '';
        $this->access_token = !empty($options['access_token']) ? $options['access_token'] : '';
    }

    public function checkConfig()
    {
        if (empty($this->_appID) || empty($this->_appSecret)) {
            throw new WeixinException('配置错误');
        }
    }
    public function setAppId(string $str = '')
    {
        $this->_appID = $str;
        return $this;
    }
    public function setAppSecret(string $str = '')
    {
        $this->_appSecret = $str;
        return $this;
    }
    public function setAccessToken(string $accessToken = '')
    {
        $this->access_token = $accessToken;
        return $this;
    }
    public function getAccessToken()
    {
        $this->access_token = $this->wxAccessToken();
        return $this->access_token;
        return $this;
    }
    public function getAppId()
    {
        return $this->_appID;
    }
    public function wxRequestRes($result)
    {

        // if ($result) {
        //     $json = json_decode($result, true);
        //     if (!$json || !empty($json['errcode'])) {
        //         $this->errCode = $json['errcode'];
        //         $this->errMsg = $json['errmsg'];
        //         return false;
        //     } else {
        //         if ($json['errcode'] == 0) return true;
        //     }
        //     return $json;
        // }

        if (!empty($result['errcode'])) {
            // 微信获取信息错误
            throw new \shiyunWechat\exception\WeixinException(
                "【{$result['errcode']}】" .
                    $result['errmsg']
            );
        }
        return $result;
    }
    public function wxLogAdd($info = '')
    {
        \shiyun\libs\LibLogger::getInstance()
            ->setGroup('weixin_gzh')
            ->writeDebug($info);
    }
    /**
     * 获取access_token
     * 【请勿时时调用】;上层应用通过该接口来获取wxAccessToken,
     * 并将其保存到数据库中,仅当该access_token失效时去获取.
     * @param string $token 手动指定access_token，非必要情况不建议用
     */
    /**
     * --------------------
     * 微信获取AccessToken 返回指定微信公众号的at信息
     * --------------------
     */
    public function wxAccessToken()
    {
        if (empty($this->_appID) && empty($this->_appSecret)) {
            throw new WeixinException('appID、appSecret 有误');
        }
        // $cacheKey = "{$this->_token_cache_key}_{$this->_appID}";
        // $cache_arr = WeixinCache::getCache($cacheKey);
        // if ($cache_arr['access_token']) {
        //     return $cache_arr['access_token'];
        // }
        // 如果是企业号用以下URL获取access_token
        // $url = WechatConst::URL_API_CGI_PREFIX ."/gettoken?corpid={$this->_appID}&corpsecret={$this->_appSecret}";

        $cacheKey = "weixin:gzh_token:wxAccessToken_{$this->_appID}";
        $access_token = frameCacheGet('CACHE_STORES_REDIS', $cacheKey, []);
        if (empty($access_token)) {
            $httpRes = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/token', [
                'grant_type' => 'client_credential',
                'appid' => $this->_appID,
                'secret' => $this->_appSecret,
            ]);
            $access_token = isset($httpRes["access_token"]) ? $httpRes["access_token"] : null;
            if (!empty($access_token)) {
                // 有效时间，单位：秒(安全起见，提前10分钟)
                // $expires_in = isset($httpRes["expires_in"]) ? intval($httpRes['expires_in']) - 600 : 3600;
                frameCacheSet('CACHE_STORES_REDIS', $cacheKey, $access_token, 7200 - 200);
            }
        }

        $this->wxLogAdd($access_token);
        // 将$access_token存缓存，设置有效期
        $this->access_token = $access_token;
        return $this->access_token;
    }

    /**
     * -------------------- 
     * 微信格式化数组变成参数格式 - 支持url加密
     * -------------------- 
     */
    public function wxSetParam($parameters)
    {
        if (is_array($parameters) && !empty($parameters)) {
            $this->parameters = $parameters;
            return $this->parameters;
        } else {
            return array();
        }
    }

    /**
     * --------------------
     * 微信格式化数组变成参数格式 - 支持url加密
     * --------------------
     */
    public function wxFormatArray($parameters = NULL, $urlencode = FALSE)
    {
        if (is_null($parameters)) {
            $parameters = $this->parameters;
        }
        $restr = ""; // 初始化空
        ksort($parameters); // 排序参数
        foreach ($parameters as $k => $v) { // 循环定制参数
            if (null != $v && "null" != $v && "sign" != $k) {
                if ($urlencode) { // 如果参数需要增加URL加密就增加，不需要则不需要
                    $v = urlencode($v);
                }
                $restr .= $k . "=" . $v . "&"; // 返回完整字符串
            }
        }
        if (strlen($restr) > 0) { // 如果存在数据则将最后“&”删除
            $restr = substr($restr, 0, strlen($restr) - 1);
        }
        return $restr; // 返回字符串
    }

    /**
     * --------------------
     * 微信MD5签名生成器 - 需要将参数数组转化成为字符串[wxFormatArray方法]
     * --------------------
     */
    public function wxMd5Sign($content, $privatekey)
    {
        if (is_null($privatekey)) {
            throw new \Exception("财付通签名key不能为空！");
        }
        if (is_null($content)) {
            throw new \Exception("财付通签名内容不能为空");
        }
        $signStr = $content . "&key=" . $privatekey;
        return strtoupper(md5($signStr));
    }

    /**
     * --------------------
     * 微信Sha1签名生成器 - 需要将参数数组转化成为字符串[wxFormatArray方法]
     * --------------------
     */
    public function wxSha1Sign($content)
    {
        if (is_null($content)) {
            throw new \Exception("签名内容不能为空");
        }
        // $signStr = $content;
        return sha1($content);
    }
    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    static function json_encode($arr)
    {
        $parts = array();
        $is_list = false;
        // Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] === 0) && ($keys[$max_length] === $max_length)) { // See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { // See if each key correspondes to its position
                if ($i != $keys[$i]) { // A key fails at position check.
                    $is_list = false; // It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { // Custom handling for arrays
                if ($is_list)
                    $parts[] = self::json_encode($value); /* :RECURSION: */
                else
                    $parts[] = '"' . $key . '":' . self::json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                // Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; // Numbers
                elseif ($value === false)
                    $str .= 'false'; // The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; // All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; // Return numerical JSON
        return '{' . $json . '}'; // Return associative JSON
    }
}
