<?php

namespace shiyunWechat;

use think\facade\Cache;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\libs\WeixinCache;
use shiyunWechat\WechatConst;

/**
 * App优化 
 */
class WxJsSdk extends WechatCommon
{
    protected $jsApiTicket = NULL;
    protected $jsApiTime = NULL;
    protected $_jsapi_cache_key = 'wechat_jsapi_ticket';
    private $path;
    private $url;

    public function setPath()
    {
        $this->path = __DIR__ . '/';
        return $this;
    }
    public function setUrl()
    {
        $this->url = isset($options['url']) ? $options['url'] : '';
        return $this;
    }
    /**
     * --------------------
     * 微信jsApi整合方法 - 通过调用此方法获得jsapi数据
     * --------------------
     */
    /**
     * 获取JsApi使用签名
     * @param string $url 网页的URL，自动处理#及其后面部分
     * @param string $timestamp 当前时间戳 (为空则自动生成)
     * @param string $noncestr 随机串 (为空则自动生成)
     * @param string $appid 用于多个appid时使用,可空
     * @return array|bool 返回签名字串
     */
    public function wxJsApiPackage()
    {
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if (empty($url)) {
            return false;
        }
        $jsapi_ticket = $this->wxVerifyJsApiTicket();
        if (empty($jsapiTicket)) {
            return false;
        }

        if (empty($timestamp)) {
            $timestamp = time();
        }
        if (empty($noncestr)) {
            $nonceStr = cc_random_lowernum(16);
        }
        $signPackage = array(
            "jsapi_ticket" => $jsapi_ticket,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url
        );
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $rawString = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        // $rawString = $this->wxFormatArray($signPackage);
        $signature = $this->wxSha1Sign($rawString);
        if (!$signature)
            return false;

        $signPackage['signature'] = $signature;
        $signPackage['rawString'] = $rawString;
        $signPackage['appId'] = $this->_appID;

        return $signPackage;
    }
    protected function wxVerifyJsApiTicket($appId = NULL, $appSecret = NULL)
    {
        if (!empty($this->jsApiTime) && intval($this->jsApiTime) > time() && !empty($this->jsApiTicket)) {
            $ticket = $this->jsApiTicket;
        } else {
            $ticket = $this->getJsApiTicket($appId, $appSecret);
            $this->jsApiTicket = $ticket;
            $this->jsApiTime = time() + 7200;
        }
        return $ticket;
    }
    /**
     * --------------------
     * 微信获取ApiTicket 返回指定微信公众号的at信息
     * --------------------
     */
    /**
     * 获取JSAPI授权TICKET
     * @param string $appid 用于多个appid时使用,可空
     * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
     */
    public function getJsApiTicket()
    {
        // 手动指定token，优先使用
        if (!empty($this->jsApiTicket)) {
            return $this->jsApiTicket;
        }
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $cache_name = $this->_jsapi_cache_key . $this->_appID;
        $cache_data = WeixinCache::getCache($cache_name);
        if ($cache_data['jsapi_expire_time'] > time()) {
            return $cache_data['jsapi_ticket'];
        }
        $wxAccToken = $this->wxAccessToken();
        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$wxAccToken";
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/ticket/getticket', [
            'access_token' => $wxAccToken,
            'type' => 'jsapi'
        ]);
        if (!empty($result['jsapi_ticket'])) {
            $data['jsapi_expire_time'] = time() + 7000;
            $data['jsapi_ticket'] = $result['jsapi_ticket'];
            $expire = $result['expires_in'] ? intval($result['expires_in']) - 100 : 3600;
            WeixinCache::setCache($cache_name, $data, $expire);
        } else {
            die('no ticket!');
        }
        return $result['jsapi_ticket'];
    }
    // 直接优先设置
    public function setJsApiTicket($new_str)
    {
        $this->jsApiTicket = $new_str;
        return $this;
    }
    /**
     * 删除JSAPI授权TICKET
     * @param string $appid 用于多个appid时使用
     */
    public function resetJsTicket($appid = '')
    {
        if (!$appid)
            $appid = $this->_appID;
        $this->jsApiTicket = '';
        WeixinCache::removeCache($this->_jsapi_cache_key  . $appid);
        return true;
    }
}
