<?php

namespace shiyunWechat\weixin_qyh;

use shiyunWechat\exception\WeixinException;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\WechatConst;

trait TraitMenu
{
    /**
     * 获取菜单
     */
    public function getMenu($agentid = '')
    {
        if ($agentid == '') {
            $agentid = $this->agentid;
        }
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_QYH_MENU_GET, [
            'access_token' => $wxAccToken,
            'agentid' => $agentid
        ]);
        return $result;
    }
    /**
     * 删除菜单
     */
    public function deleteMenu($agentid = '')
    {
        if ($agentid == '') {
            $agentid = $this->agentid;
        }
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_QYH_MENU_DEL, [
            'access_token' => $wxAccToken,
            'agentid' => $agentid
        ]);
        return true;
        return $result;
    }
}
