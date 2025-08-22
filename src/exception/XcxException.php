<?php

namespace shiyunWechat\exception;

use Exception;

/**
 * 小程序 异常处理类
 */
class XcxException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        // 可以自定义异常类的构造函数
        parent::__construct($message, $code, $previous);
    }
}
