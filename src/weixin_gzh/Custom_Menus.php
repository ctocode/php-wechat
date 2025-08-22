<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

/**
 * 微信 - 公众号 - 自定义菜单
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Creating_Custom-Defined_Menu.html
 */
class Custom_Menus extends WechatCommon
{
    const EVENT_MENU_VIEW = 'VIEW'; // 菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK'; // 菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push'; // 菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; // 菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto'; // 菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album'; // 菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin'; // 菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select'; // 菜单 - 弹出地理位置选择器

    /**
     * --------------------
     * 自定义菜单 /创建接口
     * --------------------
     */
    public function wxMenuCreate(array $data = [], $agentid = '')
    {
        $wxAccToken = $this->wxAccessToken();

        // if ($agentid == '') {
        //     $agentid = $this->agentid;
        // }
        // if (!empty($agentid)) {
        //     $url = WechatConst::URL_API_CGI_PREFIX . '/menu/create?access_token=' . $wxAccToken . '&agentid=' . $agentid;
        // }
        $url = WechatConst::URL_API_CGI_PREFIX . "/menu/create?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
        return true;
    }
    /**
     * --------------------
     * 自定义菜单 /查询接口
     * --------------------
     */
    public function wxMenuGetInfo()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/get_current_selfmenu_info', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }

    /**
     * --------------------
     * 自定义菜单 /删除接口
     * --------------------
     * @return boolean
     */
    public function wxMenuDelete()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/menu/delete', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }
    /**
     * --------------------
     * 自定义菜单 /获取自定义菜单配置
     * --------------------
     * @return array 
     */
    public function wxMenuGetConfig()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/menu/get', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }
}
