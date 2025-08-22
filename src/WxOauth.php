<?php

namespace shiyunWechat;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\WechatConst;
use Wechat;

class WxOauth extends WechatCommon
{
    /**
     * 刷新access token并续期
     * @param string $refresh_token
     * @return boolean|mixed
     */
    public function getOauthRefreshToken($refresh_token)
    {
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_OAUTH_REFRESH, [
            'appid' => $this->_appID,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
        ]);
        $this->access_token = $result['access_token'];
        // $this->user_token = $json['access_token'];
        return $result;
    }
    /**
     * --------------------
     * 微信通过OAUTH返回页面中获取AT信息
     * --------------------
     * 通过code获取Access Token
     * @return array {access_token,expires_in,refresh_token,openid,scope}
     */
    public function getOauthAccessToken($code = '')
    {
        if (empty($code)) {
            $code = request()->get('code', '');
        }
        if (!$code) {
            throw new \shiyunWechat\exception\WeixinException('getOauthAccessToken code 不存在');
            return false;
        }
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_OAUTH_TOKEN, [
            'appid' => $this->_appID,
            'secret' => $this->_appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);
        $this->access_token = $result['access_token'];
        return $result;
    }
    /**
     * --------------------
     * 微信OAUTH跳转指定URL
     * --------------------
     */
    public function wxHeader($url)
    {
        header("location:" . $url);
    }
    /**
     * --------------------
     * 微信通过OAUTH的Access_Token的信息获取当前用户信息 // 只执行在snsapi_userinfo模式运行
     * --------------------
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function getOauthUserInfo($wxAccToken, $openId)
    {
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_OAUTH_USERINFO, [
            'access_token' => $wxAccToken,
            'openid' => $openId,
            'lang' => 'zh_CN',
        ]);
        return $this->wxRequestRes($result);
    }
    /**
     * 检验授权凭证是否有效
     * @param string $access_token
     * @param string $openid
     * @return boolean 是否有效
     */
    public function getOauthAuth($access_token, $openid)
    {
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_OAUTH_AUTH, [
            'access_token' => $access_token,
            'openid' => $openid,
        ]);
        return $this->wxRequestRes($result);
    }
    /**
     * --------------------
     * 微信设置OAUTH跳转URL，返回字符串信息 - SCOPE = snsapi_base //验证时不返回确认页面，只能获取OPENID
     * --------------------
     * oauth 授权跳转接口
     * @param string $redirectUrl 回调URI
     * @param string $state 重定向后会带上state参数，企业可以填写a-zA-Z0-9的参数值
     * @return string
     */
    public function wxOauthRedirectBase($redirectUrl, $state = "")
    {
        // $redirectUrl =  urlencode($redirectUrl) ;
        $url = WechatConst::WX_OAUTH_AUTHORIZE
            . "?appid=" . $this->_appID
            . "&redirect_uri=" . $redirectUrl
            . "&response_type=code&scope=snsapi_base&state=" . $state . "#wechat_redirect";
        return $url;
    }
    /**
     * --------------------
     * 微信设置OAUTH跳转URL，返回字符串信息 - SCOPE = snsapi_userinfo //获取用户完整信息
     * --------------------
     */
    public function wxOauthRedirectUserinfo($redirectUrl, $state = "")
    {
        $redirectUrl =  urlencode($redirectUrl);
        $url = WechatConst::WX_OAUTH_AUTHORIZE
            . "?appid=" . $this->_appID
            . "&redirect_uri=" . $redirectUrl
            . "&response_type=code"
            . "&scope=snsapi_userinfo"
            . "&state=" . $state . "#wechat_redirect";
        return $url;
    }
    /**
     * oauth 授权跳转接口
     * @param string $callback 回调URI
     * @return string
     */
    public function getOauthRedirect($callback, $state = '', $scope = 'snsapi_userinfo')
    {
        return WechatConst::WX_OAUTH_AUTHORIZE
            . '?appid=' . $this->appid
            . '&redirect_uri=' . urlencode($callback)
            . '&response_type=code'
            . '&scope=' . $scope
            . '&state=' . $state . '#wechat_redirect';
    }
}
