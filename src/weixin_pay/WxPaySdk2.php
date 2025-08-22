<?php

namespace shiyunWechat;

class WxPaySdk extends WechatCommon
{
    // openID
    const mchid = ""; // 商户号
    /**
     * --------------------
     * 微信商户订单号 - 最长28位字符串
     * --------------------
     */
    public function wxMchBillno($mchid = NULL)
    {
        if (is_null($mchid)) {
            if (self::mchid == "" || is_null(self::mchid)) {
                $mchid = time();
            } else {
                $mchid = self::mchid;
            }
        } else {
            $mchid = substr(addslashes($mchid), 0, 10);
        }
        return date("Ymd", time()) . time() . $mchid;
    }
}
