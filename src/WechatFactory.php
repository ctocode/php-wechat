<?php

namespace shiyunWechat;

use shiyunWechat\weixin_gzh\Application as GzhApplication;
use shiyunWechat\weixin_gzh\Server_Connect;
use shiyunWechat\weixin_xcx\Application as XcxApplication;

/**
 * 微信sdk
 */
class WechatFactory
{
    /**
     * 单例实例
     */
    protected static $instance;
    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    public function oauthApp($config = [])
    {
        return new WxOauth($config);
    }
    /**
     * 公众号
     */
    public function gzhApp($config = [])
    {
        return new GzhApplication($config);
    }
    /**
     * 小程序
     */
    public function xcxApp($config = [])
    {
        return new XcxApplication($config);
    }
}
