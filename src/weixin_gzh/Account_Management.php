<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

/**
 * 微信 - 公众号 - 账号管理
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/Account_Management/Generating_a_Parametric_QR_Code.html
 */
class Account_Management extends WechatCommon
{
    const QR_SCENE = 0;
    const QR_LIMIT_SCENE = 1;
    /**
     * -------------------- 
     * 创建二维码ticket
     * -------------------- 
     * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
     * @param int $expire 临时二维码有效期，最大不超过2592000（即30天）
     * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
     * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800,'url'=>'二维码图片解析后的地址')
     */
    public function wxQrCodeCreate(string|int $scene_id = '', int $expire = 1800, int $type = 0)
    {
        $unique = md5(password_hash(uniqid(true), PASSWORD_BCRYPT));
        $scene_id =  !empty($scene_id) ? $scene_id : $unique;

        $data = [];
        $data['expire_seconds'] = $expire;
        // 是否是字符串ID
        if (is_string($scene_id)) {
            $data['action_name'] = !empty($type) ? 'QR_LIMIT_STR_SCENE' : 'QR_STR_SCENE';
            $data['action_info']['scene']['scene_str'] = $scene_id;
        } else if (is_int($scene_id)) {
            $data['action_name'] = !empty($type) ? 'QR_LIMIT_SCENE' : 'QR_SCENE';
            $data['action_info']['scene']['scene_id'] = $scene_id;
        }
        if (!empty($type)) {
            unset($data['expire_seconds']);
        }
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . "/qrcode/create?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        $result = $this->wxRequestRes($result);
        return $result;
    }
    /**
     * -------------------- 
     * 通过ticket换取二维码
     * -------------------- 
     * @param string $ticket 传入由 wxQrCodeCreate 方法生成的ticket参数
     * @return string url 返回http地址
     */
    public function wxQrCodeShow(string $ticket = ''): string
    {
        $url = WechatConst::URL_MP_CGI_PREFIX . "/showqrcode?ticket=" . urlencode($ticket);
        return $url;
    }
}
