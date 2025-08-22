<?php

namespace shiyunWechat\weixin_xcx;

use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\exception\XcxException;

/**
 * 小程序接口
 * @author ctocode-zhw
 * @version 2023-09-09
 */
trait TraitService
{
    /**
     * @param string $code code码
     * @param $encryptedData 加密参数
     * @param string $iv 偏移量
     */
    public function xcxDoGetData($code = '', string $encryptedData = '', $iv = '')
    {
        if (empty($wxConfig)) {
            throw new XcxException('WEIXIN_4000');
        }
        // var_dump($code , $encryptedData, $iv);
        $this->checkConfig();
        if (empty($code)) {
            throw new XcxException('WEIXIN_4001');
        }
        /**
         *  开发者使用登陆凭证 code 
         *  获取 session_key、openid、unionid
         */
        $XcxResultArr = HelperCurl::curlHttpParamGet(WechatConst::WX_API_BASE_PREFIX . '/sns/jscode2session', [
            'appid' => $this->_appID,
            'secret' => $this->_appSecret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ]);
        // 获取用户基本信息
        if (!empty($encryptedData) && !empty($iv) && !empty($XcxResultArr['session_key'])) {
            $XcxResultArr1 = $this->xcxGetUserInfo($wxConfig['appid'], $XcxResultArr['session_key'], $encryptedData, $iv);
            if ($XcxResultArr1['status'] == 200) {
                $XcxResultArr = array_merge($XcxResultArr, $XcxResultArr1);
                // return array(
                // 'status' => 404,
                // 'msg' => "揭秘失败，请重试～",
                // 'info' => $XcxResultArr
                // );
            }
            if (!empty($XcxResultArr['openId'])) {
                $XcxResultArr['openid'] = $XcxResultArr['openId'];
            }
            if (!empty($XcxResultArr['unionId'])) {
                $XcxResultArr['unionid'] = $XcxResultArr['unionId'];
            }
        }
        if (empty($XcxResultArr['openid'])) {
            return array(
                'status' => 404,
                'msg' => "openid 为空，可能code已经消耗",
                'info' => $XcxResultArr
            );
        }
        return array_merge(array(
            'status' => 200
        ), $XcxResultArr);
    }
    /**
     *  获取用户 信息
     */
    //
    public function xcxGetUserInfo($APPID = '', $session_key = '', $encryptedData = '', $iv = '')
    {
        include __DIR__ . '/XcxBizDataCrypt.php';
        $pc = new XcxBizDataCrypt($APPID, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data); // 其中$data包含用户的所有数据
        if ($errCode == 0) {
            $data = json_decode($data, true);
            $data['status'] = 200;
            return $data;
        }
        return array(
            'status' => 500,
            'msg' => $errCode
        );
    }
    /**
     * 小程序生成二维码
     * @param string $page 已经发布的小程序存在的页面,如：pages/index/index
     * @param string $scene 场景值，用于存参数
     * @return mixed
     */
    public function getWXACodeUnlimit($page, $scene = '')
    {
        $data = [];
        $data['page'] = $page;
        $data['scene'] = $scene ?: 'default';
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_XCX_CREATE_QRCODE . "?access_token={$wxAccToken}";
        return HelperCurl::curlHttpPost($url, json_encode($data));
    }
}
