<?php

namespace shiyunWechat\exception;

use Exception;

/**
 * 微信 异常处理类
 */
class WeixinException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        // 可以自定义异常类的构造函数
        parent::__construct($message, $code, $previous);
    }
}
