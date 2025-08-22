<?php

namespace shiyunWechat\weixin_qyh;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\WechatConst;

trait TraitMsgMedia
{
    /**
     * 上传多媒体文件 (只有三天的有效期，过期自动被删除)
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 媒体文件类型:图片（image）、语音（voice）、视频（video），普通文件(file)
     * @return boolean|array
     * {
     *    "type": "image",
     *    "media_id": "0000001",
     *    "created_at": "1380000000"
     * }
     */
    public function uploadMedia($data, $type)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_API_QY_CGI_PREFIX . self::MEDIA_UPLOAD . 'access_token=' . $wxAccToken . '&type=' . $type;
        $result = HelperCurl::curlHttpPost($url, $data, true);
        return $result;
    }
    /**
     * 根据媒体文件ID获取媒体文件
     * @param string $media_id 媒体文件id
     * @return raw data
     */
    public function getMedia($media_id)
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_API_QY_CGI_PREFIX . '/media/get', [
            'access_token' => $wxAccToken,
            'media_id' => $media_id,
        ]);
        return $result;
    }
}
