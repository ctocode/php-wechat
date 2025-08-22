<?php

namespace shiyunWechat\libs;

/**
 * error code
 * 仅用作类内部使用，不用于官方API接口的errCode码
 */
class ErrorCode
{
    public static $OK = 0;
    public static $ValidateSignatureError = 40001;
    public static $ParseXmlError = 40002;
    public static $ComputeSignatureError = 40003;
    public static $IllegalAesKey = 40004;
    public static $ValidateAppidError = 40005;
    public static $EncryptAESError = 40006;
    public static $DecryptAESError = 40007;
    public static $IllegalBuffer = 40008;
    public static $EncodeBase64Error = 40009;
    public static $DecodeBase64Error = 40010;
    public static $GenReturnXmlError = 40011;

    // public $errCode = 40001;
    // public $errMsg = "no access";

    public static $errCode = array(
        '0' => '处理成功',
        '40001' => '校验签名失败',
        '40002' => '解析xml失败',
        '40003' => '计算签名失败',
        '40004' => '不合法的AESKey',
        '40005' => '校验AppID失败',
        '40006' => 'AES加密失败',
        '40007' => 'AES解密失败',
        '40008' => '公众平台发送的xml不合法',
        '40009' => 'Base64编码失败',
        '40010' => 'Base64解码失败',
        '40011' => '公众帐号生成回包xml失败'
    );
    public static $errCode2 = array(
        '0' => '无问题',
        '40001' => '签名验证错误',
        '40002' => 'xml解析失败',
        '40003' => 'sha加密生成签名失败',
        '40004' => 'encodingAesKey 非法',
        '40005' => 'appid 校验错误',
        '40006' => 'aes 加密失败',
        '40007' => 'aes 解密失败',
        '40008' => '解密后得到的buffer非法',
        '40009' => 'base64加密失败',
        '40010' => 'base64解密失败',
        '40011' => '生成xml失败'
    );
    public static function getErrText($err)
    {
        if (isset(self::$errCode[$err])) {
            return self::$errCode[$err];
        } else {
            return false;
        };
    }
}
