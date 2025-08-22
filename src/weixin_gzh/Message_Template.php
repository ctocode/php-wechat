<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

/**
 * 微信 - 公众号 - 模板消息接口
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html
 */
class Message_Template extends WechatCommon
{
    /**
     * --------------------
     * 设置所属行业
     * --------------------
     * 设置公众号所属行业【每月可更改1次所选行业】
     * @param int $id1 公众号模板消息所属行业编号，参看官方开发文档 行业代码
     * @param int $id2 同$id1。但如果只有一个行业，此参数可省略
     * @return boolean|array
     */
    public function wxTemplateIndustrySet($id1, $id2 = '')
    {
        $data = [];
        if ($id1)
            $data['industry_id1'] = $id1;
        if ($id2)
            $data['industry_id2'] = $id2;
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . "/template/api_set_industry?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 获取设置的行业信息
     * --------------------
     */
    public function wxTemplateGetIndustry()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/template/get_industry', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }
    /**
     * --------------------
     * 获得模板ID
     * --------------------
     * @param string $tpl_id 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return boolean|string 成功返回消息模板的调用id
     */
    public function wxTemplateAdd($template_id_short = '', $keyword_name_list = [])
    {
        if (empty($template_id_short)) {
            return null;
        }
        $data = array(
            'template_id_short' => $template_id_short,
            'keyword_name_list' => $keyword_name_list
        );
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . "/template/api_add_template?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 获取模板列表
     * --------------------
     */
    public function wxTemplateGet($access_token)
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/template/get_all_private_template', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }
    /**
     * --------------------
     *     删除模板
     * --------------------
     */
    public function wxTemplateDelete($template_id)
    {
        if (empty($template_id)) {
            return NULL;
        }
        $data = array(
            'template_id' => $template_id
        );
        $jsonData = json_encode($data);
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . "/template/del_private_template?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        return $result;
    }
    /**
     * --------------------
     * 发送模板消息
     * --------------------
     * 通过指定模板信息发送给指定用户，发送完成后返回指定JSON数据
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function wxTemplateSendMsg(
        string $touser = '',
        string $template_id = '',
        string $url = '',
        array $data = [],
        array $miniprogram = null
    ) {
        $template = array(
            'touser' => $touser,
            'template_id' => $template_id,
            'url' => $url,
            'data' => $data
        );
        if (!empty($miniprogram)) {
            $template['miniprogram'] = $template;
        }
        $jsonData = urldecode(json_encode($template));
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . "/message/template/send?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        // $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
}
