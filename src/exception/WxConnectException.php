<?php

namespace shiyunWechat\exception;

use Exception;

/**
 * 微信 接入异常处理类
 */
class WxConnectException extends Exception
{
    // 可以自定义异常类的构造函数
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
