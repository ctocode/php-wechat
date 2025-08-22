<?php

namespace shiyunWechat\weixin_xcx;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\WechatConst;

/**
 * TODO 统一服务消息
 * @Author sxd
 * @Date 2019-08-06 10:02
 */
trait TraitMessage
{
    /**
     * 发送统一服务消息 （不可用）
     * 官方文档 https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/uniform-message/uniformMessage.send.html#method-http
     * @description
     * @example
     * @author LittleMo
     * @param array $config
     * @param array $template
     * @param string $touser
     * @param string $type
     * @return void
     */
    public function messageSend($config = array(), $template = array(), $touser = '', $type = 'weapp_template_msg')
    {
        // var_dump($config);
        // var_dump($template);
        // var_dump($touser);
        // var_dump($type);
        //         属性	类型	默认值	必填	说明
        // access_token	string		是	接口调用凭证
        // touser	string		是	用户openid，可以是小程序的openid，也可以是mp_template_msg.appid对应的公众号的openid
        // weapp_template_msg	Object		否	小程序模板消息相关的信息，可以参考小程序模板消息接口; 有此节点则优先发送小程序模板消息
        // mp_template_msg	Object		是	公众号模板消息相关的信息，可以参考公众号模板消息接口；有此节点并且没有weapp_template_msg节点时，发送公众号模板消息

        $data = array();
        $access_token = $this->setAppId($config['appid'])->setAppSecret($config['appsecret'])->wxAccessToken();
        $data['touser'] = $touser;

        if ($type == 'weapp_template_msg') {
            $data['weapp_template_msg'] = [
                'template_id' => $template['template_id'],
                'page' => $template['page'],
                'form_id' => $template['form_id'],
                'data' => array(),
            ];

            foreach ($template['data'] as $key => $val) {
                $data['weapp_template_msg']['data']['keyword' . ($key + 1)] = array(
                    "value" => $val
                );
            }
            $data['weapp_template_msg']['emphasis_keyword'] = $template['emphasis_keyword'] ?? '';
        } else if ($type == 'mp_template_msg') {
        }


        // dd(json_encode($data[']));
        // \shiyun\libs\LibLogger::getInstance()->setGroup('weixin_xcx')->writeDebug(($data, 'UniformMessage' . date("Y-m-d"));
        // $url =  "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$access_token}";
        $url =  "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token={$access_token}";
        $result = HelperCurl::curlHttpPost($url, json_encode($data['weapp_template_msg']));
        // \shiyun\libs\LibLogger::getInstance()->setGroup('weixin_xcx')->writeDebug(($jsoninfo, 'xcxTemplateMessage' . date("Y-m-d"));
        return $result;
    }

    /**
     *  发送小程序订阅消息
     * 官方文档 https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html
     * @description
     * @example
     * @author LittleMo
     * @param array $config
     * @param array $template
     * @param string $touser
     * @return void
     */
    public function messageSendSubscribe($config = array(), $template = array(), $touser = '')
    {

        //         属性	类型	默认值	必填	说明
        // access_token	string		是	接口调用凭证
        // touser	string		是	用户openid，可以是小程序的openid，也可以是mp_template_msg.appid对应的公众号的openid
        // weapp_template_msg	Object		否	小程序模板消息相关的信息，可以参考小程序模板消息接口; 有此节点则优先发送小程序模板消息
        // mp_template_msg	Object		是	公众号模板消息相关的信息，可以参考公众号模板消息接口；有此节点并且没有weapp_template_msg节点时，发送公众号模板消息

        $data = array();
        $access_token = $this->setAppId($config['appid'])->setAppSecret($config['appsecret'])->wxAccessToken();
        $data['touser'] = $touser;

        $data['template_id'] = $template['template_id'];
        $data['page'] = $template['page'];
        $data['form_id'] = $template['form_id'];
        $data['data'] = array();

        foreach ($template['data'] as $key => $val) {
            $data['data'][$key] = array(
                "value" => $val
            );
        }
        $data['emphasis_keyword'] = $template['emphasis_keyword'] ?? '';

        // dd(json_encode($data));

        // \shiyun\libs\LibLogger::getInstance()->setGroup('weixin_xcx')->writeDebug(($data, 'UniformMessage' . date("Y-m-d"));
        $url = WechatConst::URL_API_CGI_PREFIX . "/message/subscribe/send?access_token={$access_token}";
        $result = HelperCurl::curlHttpPost($url, json_encode($data));
        // \shiyun\libs\LibLogger::getInstance()->setGroup('weixin_xcx')->writeDebug(($jsoninfo, 'xcxTemplateMessage/sendXcxSubscribe' . date("Y-m-d"));
        return $result;
    }
}
