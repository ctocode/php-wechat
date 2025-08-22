<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

/**
 * 微信 - 公众号 - 基础消息能力 /群发接口和原创校验
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.htm
 */
class Message_Mass extends WechatCommon
{
    const EVENT_SEND_MASS = 'MASSSENDJOBFINISH'; // 发送结果 - 高级群发完成
    const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH'; // 发送结果 - 模板消息发送结果

    /**
     * 高级群发消息, 根据OpenID列表群发图文消息(订阅号不可用)
     *    注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function sendMassMessage($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . '/message/mass/send?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 高级群发消息, 根据群组id群发图文消息(认证后的订阅号可用)
     *    注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function sendGroupMassMessage($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . '/message/mass/sendall?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 高级群发消息, 删除群发图文消息(认证后的订阅号可用)
     * @param int $msg_id 消息id
     * @return boolean|array
     */
    public function deleteMassMessage($msg_id)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'msg_id' => $msg_id
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/message/mass/delete?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return true;
        return $result;
    }

    /**
     * 高级群发消息, 预览群发消息(认证后的订阅号可用)
     *    注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function previewMassMessage($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . '/message/mass/preview?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 高级群发消息, 查询群发消息发送状态(认证后的订阅号可用)
     * @param int $msg_id 消息id
     * @return boolean|array
     */
    public function queryMassMessage($msg_id)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'msg_id' => $msg_id
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/message/mass/get?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
}
