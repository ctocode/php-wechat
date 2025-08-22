<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

/**
 * 微信 - 公众号 - 图文消息留言管理
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_materials_list.html
 * https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
 */
class Comments_management extends WechatCommon
{
    /**
     * --------------------
     * 评论能力管理 / 新增永久素材
     * --------------------
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function wxMaterialAdd($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . '/material/add_news?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * --------------------
     * 评论能力管理 / 获取永久素材
     * --------------------
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return boolean|array|raw data
     */
    public function wxMaterialGet($media_id, $is_video = false)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'media_id' => $media_id
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/material/get_material?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 评论能力管理 / 修改永久图文素材
     * --------------------
     * @param string $media_id 图文素材id
     * @param array $data 消息结构
     * @param int $index 更新的文章在图文素材的位置，第一篇为0，仅多图文使用
     * @return boolean|array
     */
    public function wxMaterialUpdate($media_id = 0, $data, $index = 0)
    {
        $wxAccToken = $this->wxAccessToken();
        if (!isset($data['media_id']))
            $data['media_id'] = $media_id;
        if (!isset($data['index']))
            $data['index'] = $index;
        $url = WechatConst::URL_API_CGI_PREFIX . '/material/update_news?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }


    /**
     * 删除永久素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @return boolean
     */
    public function delForeverMedia($media_id)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'media_id' => $media_id
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/material/del_material?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return true;
        return $result;
    }

    /**
     * 获取永久素材列表(认证后的订阅号可用)
     * @param string $type 素材的类型,图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 全部素材的偏移位置，0表示从第一个素材
     * @param int $count 返回素材的数量，取值在1到20之间
     * @return boolean|array 
     */
    public function getForeverList($type, $offset, $count)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'type' => $type,
            'offset' => $offset,
            'count' => $count
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/material/batchget_material?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 获取永久素材总数(认证后的订阅号可用)
     * @return boolean|array 
     */
    public function getForeverCount()
    {
        $wxAccToken = $this->wxAccessToken();

        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/material/get_materialcount', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }

    /**
     * 上传图文消息素材，用于群发(认证后的订阅号可用)
     * @param array $data 消息结构{"articles":[{...}]}
     * @return boolean|array
     */
    public function uploadArticles($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . '/media/uploadnews?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
}
