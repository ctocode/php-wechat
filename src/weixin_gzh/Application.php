<?php

namespace shiyunWechat\weixin_gzh;

/**
 * 【ctocode】      微信 - 公众号 - 基础类
 * --------------------
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://docs.10yun.com/php/
 * @contact      联系方式   QQ:343196936
 */
class Application
{
    protected $config = [];
    public function __construct($config = [])
    {
        $this->config = $config;
    }
    public function Server_Connect()
    {
        return new Server_Connect($this->config);
    }
    public function Message_Reply()
    {
        return new Message_Reply($this->config);
    }
    public function Account_Management()
    {
        return new Account_Management($this->config);
    }
    public function Custom_Menus()
    {
        return new Custom_Menus($this->config);
    }
    public function Customer_Service()
    {
        return new Customer_Service($this->config);
    }
    public function User_Management()
    {
        return new User_Management($this->config);
    }
    public function Analytics()
    {
        return new Analytics($this->config);
    }
    public function Cards_and_Offer()
    {
        return new Cards_and_Offer($this->config);
    }
    public function Message_Template()
    {
        return new Message_Template($this->config);
    }
}
