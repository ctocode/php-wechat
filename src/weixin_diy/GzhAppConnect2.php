<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\libs\HelperCurl;
// 1.官方微信接口（全接口） - 微信小店接口/JSAPI
// http://www.oschina.net/code/snippet_2276613_47422
// 微信入口绑定，微信事件处理，微信API全部操作包含在这些文件中。 微信小店。
// 2.

/*
 * 调用方式简单说明：
 * $arr = array(
 * 'account' => '公众平台帐号',
 * 'password' => '密码'
 * );
 * $w = new Weixin($arr);
 * $w->getAllUserInfo();//获取所有用户信息
 * $w->getUserInfo($groupid, $fakeid);//获取所有用户信息，如果默认分组，则$groupid传0
 * $w->sendMessage('群发内容'); //群发给所有用户
 * $w->sendMessage('群发内容',$userId); //群发给特定用户,这里的$userId就是用户的fakeid，数组方式传递
 */
use shiyunWechat\WechatConst;

class GzhPush2
{
    public $userFakeid; // 所有粉丝的fakeid
    private $_account; // 用户名
    private $_password; // 密码
    private $url; // 请求的网址
    private $getHeader = 0; // 是否显示Header信息
    private $token; // 公共帐号TOKEN
    private $_TOKEN = "weixin"; // 公共帐号TOKEN
    private $host = 'mp.weixin.qq.com'; // 主机
    private $origin = 'https://mp.weixin.qq.com';
    private $referer; // 引用地址
    private $cookie;
    private $pageSize = 100000; // 每页用户数（用于读取所有用户）
    private $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0';

    public function __construct($options)
    {
        $this->_account = isset($options['account']) ? $options['account'] : '';
        $this->_password = isset($options['password']) ? $options['password'] : '';
        $this->login();
    }
    protected function showMessage($status, $errCode, $msg = '') {}
    // 登录
    private function login()
    {
        $url = WechatConst::URL_MP_CGI_PREFIX . '/login?lang=zh_CN';
        $sendData = array(
            'username' => $this->_account,
            'pwd' => md5($this->_password),
            'f' => 'json'
        );
        $this->referer = WechatConst::URL_MP_BASE_PREFIX;
        $this->getHeader = 1;
        $result = explode("\n", HelperCurl::curlHttpPost($url, $sendData));


        $EnumStatus = [
            "-1" => "系统错误",
            "-2" => "帐号或密码错误",
            // -3=>"密码错误",
            -4 => "不存在该帐户",
            -5 => "访问受限",
            -6 => "需要输入验证码",
            -7 => "此帐号已绑定私人微信号，不可用于公众平台登录",
            -8 => "邮箱已存在",
            -32 => "验证码输入错误",
            -200 => "因频繁提交虚假资料，该帐号被拒绝登录",
            -94 => "请使用邮箱登陆",
            10 => "该公众会议号已经过期，无法再登录使用",
        ];

        foreach ($result as $key => $value) {
            $value = trim($value);
            if (preg_match('/token=(\d+)/i', $value, $match)) { // 获取token
                $this->token = trim($match[1]);
            }
            if (preg_match('/"ret":(.*)/i', $value, $match)) { // 获取token
                $errCode = $match[1];
                if ($errCode == 0) {
                    $this->userFakeid = $this->getUserFakeid();
                } else if ($errCode == -3) {
                    die(urldecode(json_encode(array(
                        'status' => 1,
                        'errCode' => $errCode,
                        'msg' => urlencode("密码错误")
                    ))));
                } else  if (!empty($EnumStatus[$errCode])) {
                    $errMsg = $EnumStatus[$errCode];
                    die(json_encode(array(
                        'status' => 1,
                        'errCode' => $errCode,
                        'msg' => $errMsg
                    )));
                    // $this->showMessage()
                }
            }
            if (preg_match('/^set-cookie:[\s]+([^=]+)=([^;]+)/i', $value, $match)) { // 获取cookie
                $this->cookie .= $match[1] . '=' . $match[2] . '; ';
            }
        }
    }
    // 群发消息
    public function sendMessage($content = '', $userId = '')
    {
        if (is_array($userId) && !empty($userId)) {
            foreach ($userId as $v) {
                $json = json_decode($this->send($v, $content));
                if ($json->ret != 0) {
                    $errUser[] = $v;
                }
            }
        } else {
            foreach ($this->userFakeid as $v) {
                $json = json_decode($this->send($v['fakeid'], $content));
                if ($json->ret != 0) {
                    $errUser[] = $v['fakeid'];
                }
            }
        }
        // 共发送用户数
        $count = count($this->userFakeid);
        // 发送失败用户数
        $errCount = count($errUser);
        // 发送成功用户数
        $succeCount = $count - $errCount;
        $data = array(
            'status' => 0,
            'count' => $count,
            'succeCount' => $succeCount,
            'errCount' => $errCount,
            'errUser' => $errUser
        );
        return json_encode($data);
    }
    // 获取所有用户信息
    public function getAllUserInfo()
    {
        foreach ($this->userFakeid as $v) {
            $info[] = $this->getUserInfo($v['groupid'], $v['fakeid']);
        }
        return $info;
    }
    // 获取用户信息
    public function getUserInfo($groupId, $fakeId)
    {
        $this->getHeader = 0;
        $this->referer = WechatConst::URL_MP_CGI_PREFIX . '/contactmanagepage?token=' . $this->token . '&t=wxm-friend&lang=zh_CN&pagesize=' . $this->pageSize . '&pageidx=0&type=0&groupid=' . $groupId;
        $sendData = array(
            'token' => $this->token,
            'ajax' => 1
        );
        $url = WechatConst::URL_MP_CGI_PREFIX . " /getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&fakeid={$fakeId}";
        $message_opt = HelperCurl::curlHttpPost($url, $sendData);
        return $message_opt;
    }

    // 获取所有用户fakeid
    private function getUserFakeid()
    {
        ini_set('max_execution_time', 600);
        $pageSize = 1000000;
        $this->referer = WechatConst::URL_MP_CGI_PREFIX . "/home?t=home/index&lang=zh_CN&token={$this->token}";
        $user = HelperCurl::curlHttpParamGet(WechatConst::URL_MP_CGI_PREFIX . '/contactmanage', [
            't' => 'user/index',
            'pagesize' => $pageSize,
            'pageidx' => 0,
            'type' => 0,
            'groupid' => 0,
            'token' => $this->token,
            'lang' => 'zh_CN',
        ]);
        $preg = "/\"id\":(\d+),\"nick_name\"/";
        preg_match_all($preg, $user, $b);
        $i = 0;
        foreach ($b[1] as $v) {
            $arr[$i]['fakeid'] = $v;
            $arr[$i]['groupid'] = 0;
            $i++;
        }
        return $arr;
    }
}
