<?php

namespace shiyunWechat\weixin_qyh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\libs\WeixinCache;
use shiyunWechat\libs\Prpcrypt;
use shiyunWechat\exception\WeixinException;

/**
 *	微信公众平台企业号PHP-SDK, 官方API类库
 *  @version 1.0
 *  usage:
 *   $options = array(
 *			'token'=>'tokenaccesskey', //填写应用接口的Token
 *			'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 *			'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *			'_appSecret'=>'xxxxxxxxxxxxxxxxxxx', //填写高级调用功能的密钥
 *			'agentid'=>'1', //应用的id
 *			'debug'=>false, //调试开关
 *			'_logcallback'=>'logg', //调试输出方法，需要有一个string类型的参数
 *		);
 *
 */
class QyhBase extends WechatCommon
{

    private $token;
    private $encodingAesKey;
    private $_appID; // 也就是企业号的CorpID
    private $_appSecret;
    private $access_token;
    protected $_token_cache_key = 'qywechat_access_token';
    private $agentID; // 应用id agentID
    private $postxml;
    private $agentidxml; // 接收的应用id AgentID
    private $_msg;
    private $_sendmsg; // 主动发送消息的内容

    /**
     * 企业号 获取 access_token
     */
    public function wxAccessToken()
    {
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/gettoken', [
            'corpid' => $this->_appID,
            'corpsecret' => $this->_appSecret,
        ]);
        $accessToken = '';
        return $accessToken;
    }
    public function setAgentID($agentID = '')
    {
        $this->agentID = $agentID;
        return $this;
    }
    public function setEncodingAesKey($key = '')
    {
        $this->encodingAesKey = $key;
        return $this;
    }
    /**
     * For weixin server validation
     */
    private function checkSignature($str)
    {
        $signature = isset($_GET["msg_signature"]) ? $_GET["msg_signature"] : '';
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';
        $tmpArr = array(
            $str,
            $this->token,
            $timestamp,
            $nonce
        ); // 比普通公众平台多了一个加密的密文
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $shaStr = sha1($tmpStr);
        if ($shaStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 微信验证，包括post来的xml解密
     * @param bool $return 是否返回
     */
    public function valid($return = false)
    {
        $encryptStr = "";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $postStr = file_get_contents("php://input");
            $array = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (isset($array['Encrypt'])) {
                $encryptStr = $array['Encrypt'];
                $this->agentidxml = isset($array['AgentID']) ? $array['AgentID'] : '';
            }
        } else {
            $encryptStr = isset($_GET["echostr"]) ? $_GET["echostr"] : '';
        }
        if ($encryptStr) {
            $ret = $this->checkSignature($encryptStr);
        }
        if (!isset($ret) || !$ret) {
            if (!$return) {
                die('no access');
            } else {
                return false;
            }
        }
        $pc = new Prpcrypt($this->encodingAesKey);
        $array = $pc->decrypt($encryptStr, $this->_appID);
        if (!isset($array[0]) || ($array[0] != 0)) {
            if (!$return) {
                die('解密失败！');
            } else {
                return false;
            }
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->postxml = $array[1];
            // $this->log($array[1]);
            return ($this->postxml != "");
        } else {
            $echoStr = $array[1];
            if ($return) {
                return $echoStr;
            } else {
                die($echoStr);
            }
        }
        return false;
    }


    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    static function json_encode($arr)
    {
        $parts = array();
        $is_list = false;
        // Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] === 0) && ($keys[$max_length] === $max_length)) { // See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { // See if each key correspondes to its position
                if ($i != $keys[$i]) { // A key fails at position check.
                    $is_list = false; // It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { // Custom handling for arrays
                if ($is_list)
                    $parts[] = self::json_encode($value); /* :RECURSION: */
                else
                    $parts[] = '"' . $key . '":' . self::json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                // Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; // Numbers
                elseif ($value === false)
                    $str .= 'false'; // The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; // All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; // Return numerical JSON
        return '{' . $json . '}'; // Return associative JSON
    }


    /**
     * 主动发送信息接口
     * @param array $data 	结构体为:
     * array(
     *         "touser" => "UserID1|UserID2|UserID3",
     *         "toparty" => "PartyID1|PartyID2 ",
     *         "totag" => "TagID1|TagID2 ",
     *         "safe":"0"			//是否为保密消息，对于news无效
     *         "agentid" => "001",	//应用id
     *         "msgtype" => "text",  //根据信息类型，选择下面对应的信息结构体
     * 
     *         "text" => array(
     *                 "content" => "Holiday Request For Pony(http://xxxxx)"
     *         ),
     *         "image" => array(
     *                 "media_id" => "MEDIA_ID"
     *         ),
     *         "voice" => array(
     *                 "media_id" => "MEDIA_ID"
     *         ),
     *         " video" => array(
     *                 "media_id" => "MEDIA_ID",
     *                 "title" => "Title",
     *                 "description" => "Description"
     *         ),
     *         "file" => array(
     *                 "media_id" => "MEDIA_ID"
     *         ),
     *         "news" => array(			//不支持保密
     *                 "articles" => array(    //articles  图文消息，一个图文消息支持1到10个图文
     *                     array(
     *                         "title" => "Title",             //标题
     *                         "description" => "Description", //描述
     *                         "url" => "URL",                 //点击后跳转的链接。可根据url里面带的code参数校验员工的真实身份。
     *                         "picurl" => "PIC_URL",          //图文消息的图片链接,支持JPG、PNG格式，较好的效果为大图640*320，
     *                                                         //小图80*80。如不填，在客户端不显示图片
     *                     ),
     *                 )
     *         ),
     *         "mpnews" => array(
     *                 "articles" => array(    //articles  图文消息，一个图文消息支持1到10个图文
     *                     array(
     *                         "title" => "Title",             //图文消息的标题
     *                         "thumb_media_id" => "id",       //图文消息缩略图的media_id
     *                         "author" => "Author",           //图文消息的作者(可空)
     *                         "content_source_url" => "URL",  //图文消息点击“阅读原文”之后的页面链接(可空)
     *                         "content" => "Content"          //图文消息的内容，支持html标签
     *                         "digest" => "Digest description",   //图文消息的描述
     *                         "show_cover_pic" => "0"         //是否显示封面，1为显示，0为不显示(可空)
     *                     ),
     *                 )
     *         )
     * )
     * 请查看官方开发文档中的 发送消息 -> 消息类型及数据格式
     * 
     * @return boolean|array
     * 如果对应用或收件人、部门、标签任何一个无权限，则本次发送失败；
     * 如果收件人、部门或标签不存在，发送仍然执行，但返回无效的部分。
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "invaliduser": "UserID1",
     *    "invalidparty":"PartyID1",
     *    "invalidtag":"TagID1"
     * }
     */
    public function sendMessage($data)
    {
        $wxAccToken = $this->wxAccessToken();

        $url = WechatConst::WX_QYH_MESSAGE_SEND .  '?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
}
