<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

/**
 * 微信 - 公众号 - 客服消息
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Forwarding_of_messages_to_service_center.html
 */
class Customer_Service extends WechatCommon
{

    const EVENT_KF_SEESION_CREATE = 'kfcreatesession'; // 多客服 - 接入会话
    const EVENT_KF_SEESION_CLOSE = 'kfclosesession'; // 多客服 - 关闭会话
    const EVENT_KF_SEESION_SWITCH = 'kfswitchsession'; // 多客服 - 转接会话


    /** 
     * ========== ========== ==========  客服消息 ========== ========== ==========
     * https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Service_Center_messages.html
     */
    /**
     * --------------------
     * 客服消息 / 客服接口-发消息
     * --------------------
     * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
     * @return boolean|array
     */
    public function wxServiceSend($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . "/message/custom/send?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        // $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 客服消息 / 客服输入状态
     * --------------------
     * @param string $touser 普通用户（openid）
     * @param string $command 指令 
     */
    public function wxServiceState(string $touser = '')
    {
        $data = [
            'touser' => $touser,
            'command' => 'Typing'
        ];
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . "/message/custom/typing?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $data);
        return $result;
    }
    /** 
     * ========== ========== ==========  客服管理 ========== ========== ==========
     * https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     */
    /**
     * --------------------
     * PC 客服能力 / 客服管理 / 获取客服基本信息
     * --------------------
     * @return boolean|array
     */
    public function wxServiceList()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/customservice/getkflist', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }
    /**
     * --------------------
     * PC 客服能力 / 客服管理 / 获取在线客服接待信息
     * --------------------
     * @return boolean|array
     */
    public function wxServiceOnlineList()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/customservice/getonlinekflist', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }

    /**
     * --------------------
     * PC 客服能力 / 客服管理 / 添加客服账号
     * --------------------
     * @param string $account  完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @param string $nickname  客服昵称，最长6个汉字或12个英文字符
     * @param string $password  客服账号明文登录密码，会自动加密
     * @return boolean|array
     */
    public function wxServiceAdd(string $account = '', string $nickname = '', string $password = '')
    {
        $data = array(
            "kf_account" => $account,
            "nickname" => $nickname,
            "password" => md5($password)
        );
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_API_BASE_PREFIX . "/customservice/kfaccount/add?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * --------------------
     * PC 客服能力 / 邀请绑定客服账号
     * --------------------
     */
    public function wxServiceInvite() {}

    /**
     * --------------------
     * PC 客服能力 / 客服管理 / 设置客服信息
     * --------------------
     * @param string $account 完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @param string $nickname 客服昵称，最长6个汉字或12个英文字符
     * @param string $password 客服账号明文登录密码，会自动加密
     * @return boolean|array
     */
    public function wxServiceUpdate($account, $nickname, $password)
    {
        $data = array(
            "kf_account" => $account,
            "nickname" => $nickname,
            "password" => md5($password)
        );
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_API_BASE_PREFIX . "/customservice/kfaccount/update?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * --------------------
     * PC 客服能力 / 客服管理 / 上传客服头像
     * --------------------
     * @param string $account  完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @param string $imgfile  头像文件完整路径,如：'D:\user.jpg'。头像文件必须JPG格式，像素建议640*640
     * @return boolean|array
     */
    public function wxServiceUpdateCover(string $kf_account = '', string $media = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data = [];
        $data['media'] = str_starts_with("@", $media)  ? $media : '@' . $media;
        $url = WechatConst::WX_API_BASE_PREFIX . "/customservice/kfaccount/uploadheadimg?access_token={$wxAccToken}&kf_account=" . $kf_account;
        $result = HelperCurl::curlHttpPost($url, $data);
        // $result = HelperCurl::curlHttpPost($url, $data, true);
        return $result;
    }
    /**
     * --------------------
     * PC 客服能力 / 客服管理 / 删除客服账号
     * --------------------
     * @param string $account 完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @return boolean|array
     */
    public function wxServiceDelete(string $kf_account = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data =  [
            'access_token' => $wxAccToken,
            'kf_account' => $kf_account,
        ];
        $url = WechatConst::WX_API_BASE_PREFIX . "/customservice/kfaccount/del?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $data);
        return $result;
    }

    /** 
     * ========== ========== ==========  会话控制 ========== ========== ==========
     * https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Session_control.html
     */


    /**
     * --------------------
     * PC 客服能力 / 会话控制 / 创建会话
     * --------------------
     * @param string $openid 用户openid
     * @param string $kf_account 客服账号
     * @param string $text 附加信息，文本会展示在客服人员的多客服客户端，可为空
     * @return boolean|array    
     */
    public function wxServiceSessionAdd(string $openid = '', string $kf_account = '', string $text = '')
    {
        $data = array(
            "openid" => $openid,
            "kf_account" => $kf_account
        );
        if (!empty($text))
            $data["text"] = $text;
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_API_BASE_PREFIX . "/customservice/kfsession/create?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * PC 客服能力 / 会话控制 / 关闭会话
     * --------------------
     * @param string $openid 用户openid
     * @param string $kf_account 客服账号
     * @param string $text 附加信息，文本会展示在客服人员的多客服客户端，可为空
     * @return boolean|array
     */
    public function wxServiceSessionClose(string $openid = '', string $kf_account = '', string $text = '')
    {
        $data = array(
            "openid" => $openid,
            "nickname" => $kf_account
        );
        if (!empty($text))
            $data["text"] = $text;

        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_API_BASE_PREFIX . '/customservice/kfsession/close?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * PC 客服能力 / 会话控制 / 获取客户会话状态
     * --------------------
     * 获取用户会话状态
     * @param string $openid 粉丝的openid
     * @return boolean|array 成功返回json数组
     */
    public function wxServiceSessionGet(string $openId = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_API_BASE_PREFIX . '/customservice/kfsession/getsession', [
            'access_token' => $wxAccToken,
            'openid' => $openId,
        ]);
        return $result;
    }
    /**
     * --------------------
     * PC 客服能力 / 会话控制 / 获取客服会话列表
     * --------------------
     * @param string $openid 用户openid
     * @return boolean|array 成功返回json数组
     */
    public function wxServiceSessionList(string $kf_account = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_API_BASE_PREFIX . '/customservice/kfsession/getsessionlist', [
            'access_token' => $wxAccToken,
            'kf_account' => $kf_account,
        ]);
        return $result;
    }
    /**
     * --------------------
     * PC 客服能力 / 会话控制 / 获取未接入会话列表
     * --------------------
     * @param string $openid 用户openid
     * @return boolean|array 成功返回json数组
     */
    public function wxServiceSessionWaitCase()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_API_BASE_PREFIX . '/customservice/kfsession/getwaitcase', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }
    /** 
     * ========== ========== ==========  获取聊天记录 ========== ========== ==========
     * https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Obtain_chat_transcript.html
     */

    /**
     * --------------------
     * PC 客服能力 / 获取聊天记录 / 获取聊天记录
     * --------------------
     * @param array $data 数据结构
     * @return boolean|array
     */
    public function getCustomServiceMessage(int $starttime = 0, int $endtime = 0, int $msgid = 0, int $number = 10)
    {
        $data = [
            'starttime' => $starttime,
            'endtime' => $endtime,
            'msgid' => $msgid,
            'number' => $number,
        ];
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::URL_API_CGI_PREFIX . '/customservice/msgrecord/getmsglist?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
}
