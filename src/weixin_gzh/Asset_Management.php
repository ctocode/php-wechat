<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

trait TraitAssetManage
{
    /**
     * 上传临时素材，有效期为3天(认证后的订阅号可用)
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * 注意：临时素材的media_id是可复用的！
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
     * @return boolean|array
     */
    public function uploadMedia($data, $type)
    {
        $wxAccToken = $this->wxAccessToken();
        // 原先的上传多媒体文件接口使用 WechatConst::URL_UPLOAD_MEDIA 前缀
        $url = WechatConst::URL_API_CGI_PREFIX .  '/media/upload?access_token=' . $wxAccToken . '&type=' . $type;
        $result = HelperCurl::curlHttpPost($url, $data, true);
        return $result;
    }
    /**
     * 上传视频素材(认证后的订阅号可用)
     * @param array $data 消息结构
     * {
     *     "media_id"=>"",     //通过上传媒体接口得到的MediaId
     *     "title"=>"TITLE",    //视频标题
     *     "description"=>"Description"        //视频描述
     * }
     * @return boolean|array
     * {
     *     "type":"video",
     *     "media_id":"mediaid",
     *     "created_at":1398848981
     *  }
     */

    public function uploadMpVideo($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_UPLOAD_MEDIA .  '/media/uploadvideo?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }


    /**
     * 获取临时素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return raw data
     */
    public function getMedia($media_id, $is_video = false)
    {
        $wxAccToken = $this->wxAccessToken();
        // 原先的上传多媒体文件接口使用 WechatConst::URL_UPLOAD_MEDIA 前缀
        // 如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        $url_prefix = $is_video ? str_replace('https', 'http', WechatConst::URL_API_CGI_PREFIX) : WechatConst::URL_API_CGI_PREFIX;

        $result = HelperCurl::curlHttpParamGet($url_prefix . '/media/get', [
            'access_token' => $wxAccToken,
            'media_id' => $media_id,
        ]);
        return $result;
    }

    /**
     * 上传永久素材(认证后的订阅号可用)
     * 新增的永久素材也可以在公众平台官网素材管理模块中看到
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
     * @param boolean $is_video 是否为视频文件，默认为否
     * @param array $video_info 视频信息数组，非视频素材不需要提供 array('title'=>'视频标题','introduction'=>'描述')
     * @return boolean|array
     */
    public function uploadForeverMedia($data, $type, $is_video = false, $video_info = array())
    {
        $wxAccToken = $this->wxAccessToken();
        // #TODO 暂不确定此接口是否需要让视频文件走http协议
        // 如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        // $url_prefix = $is_video?str_replace('https','http',WechatConst::URL_API_CGI_PREFIX):WechatConst::URL_API_CGI_PREFIX;
        // 当上传视频文件时，附加视频文件信息
        if ($is_video)
            $data['description'] = self::json_encode($video_info);
        $url = WechatConst::URL_API_CGI_PREFIX . '/material/add_material?access_token=' . $wxAccToken . '&type=' . $type;
        $result = HelperCurl::curlHttpPost($url, $data, true);
        return $result;
    }
}
