<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\exception\WeixinException;
use shiyunWechat\libs\Prpcrypt;

class Message_Reply extends WechatCommon
{
    protected $_text_filter = true;
    protected $encrypt_type = 'aes';
    protected $_receive;

    public static function xmlSafeStr($str)
    {
        return '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str) . ']]>';
    }
    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data)
    {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml    .=  "<$key>";
            $xml    .=  (is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
            list($key,) = explode(' ', $key);
            $xml    .=  "</$key>";
        }
        return $xml;
    }
    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    function xml_encode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data_to_xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }
    /**
     * 
     * 回复微信服务器, 此函数支持链式操作
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function reply($msg = array(), $return = false)
    {
        // 防止不先设置回复内容，直接调用reply方法导致异常
        if (empty($msg)) {
            throw new WeixinException('reply 消息未设置');
        }
        $xmldata =  $this->xml_encode($msg);
        /**
         * aes加密
         */
        if ($this->encrypt_type == 'aes') {
            // 如果来源消息为加密方式
            $pc = new Prpcrypt($this->encodingAesKey);
            $array = $pc->encrypt($xmldata, $this->_appID);
            $ret = $array[0];
            if ($ret != 0) {
                $this->log('encrypt err!');
                return false;
            }
            $timestamp = time();
            $nonce = rand(77, 999) * rand(605, 888) * rand(11, 99);
            $encrypt = $array[1];
            $tmpArr = array(
                $this->token,
                $timestamp,
                $nonce,
                $encrypt
            );
            // 比普通公众平台多了一个加密的密文
            sort($tmpArr, SORT_STRING);
            $signature = implode($tmpArr);
            $signature = sha1($signature);
            $xmldata = $this->generate($encrypt, $signature, $timestamp, $nonce);
        }
        if ($return)
            return $xmldata;
        elseif ($xmldata) {
            echo $xmldata;
            return true;
        } else
            return false;
    }
    /**
     * 获取微信服务器发来的信息
     */
    public function getRevPost()
    {
        // 拿到数据后，可能是由于不同的环境
        $postObj = file_get_contents("php://input");
        // 解压缩后的数据
        $postArr = simplexml_load_string($postObj, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->_receive = (array) $postArr;
        if (empty($postArr)) {
            throw new WeixinException('post 消息为空');
        }
        return $postArr;
        // if ($this->encrypt_type == 'aes') {
        //     $encryptStr = $postArr->Encrupt;
        //     $pc = new Prpcrypt($this->encodingAesKey);
        //     $array = $pc->decrypt($encryptStr, $this->_appID);
        // }
    }
    public function getRevData()
    {
        return $this->_receive;

        // $receiveKeyArr = array_keys($this->_receive);
        // foreach ($receiveKeyArr as $key => $val) {
        //     // 获取二维码的场景值
        //     if ($key == 'EventKey' && isset($this->_receive['EventKey'])) {
        //         $array['EventKey'] = str_replace('qrscene_', '', $this->_receive['EventKey']);
        //     }
        //     if ($key == 'PicUrl' && isset($this->_receive['PicUrl'])) {
        //         // 防止picurl为空导致解析出错
        //         $this->_receive['PicUrl'] = (string) $this->_receive['PicUrl'];
        //     }
        //     if (isset($this->_receive[$key])) {
        //         $array[$key] = $this->_receive[$key];
        //     }
        // }
        // if (isset($array) && count($array) > 0) {
        //     return $array;
        // } else {
        //     return false;
        // }
        // $fromUserName = $object->FromUserName; // 关注者的openID
        // $toUserName = $object->ToUserName;
    }
    /**
     * 响应消息
     */
    public function setMessageHandler(callable $callResponse = null)
    {
        if (empty($callResponse)) {
            $this->defMessageHandler();
        } else {
            $postArr = $this->getRevPost();
            $callResponse($postArr);
        }
    }
    protected function defMessageHandler()
    {
        $postArr = $this->getRevPost();
        $RX_TYPE = trim($postArr->MsgType);
        $result = '';
        // 消息类型分离
        switch ($RX_TYPE) {
            case 'event':
                $result = $this->receiveEvent($postArr);
                break;
            case 'text':
                // 文本格式
                $result = $this->transmitText($postArr, '类型为text');
                break;
            case 'image':
                // 图片格式 
                $result = $this->transmitImage($postArr);
                break;
            case 'voice':
                /**
                 * 声音相关,接收语音消息
                 */
                if (isset($postArr->Recognition) && !empty($postArr->Recognition)) {
                    $content = '你刚才说的是：' . $postArr->Recognition;
                    $result = $this->transmitText($postArr, $content);
                } else {
                    $result = $this->transmitVoice($postArr);
                }
                break;
            case 'video':
                /**
                 * 视频相关,接收视频消息
                 */
                $content = array(
                    'MediaId' => $postArr->MediaId,
                    'ThumbMediaId' => $postArr->ThumbMediaId,
                    'Title' => '',
                    'Description' => ''
                );
                $result = $this->transmitVideo($postArr, $content);
                break;
            case 'shortvideo': // 小视频
                break;
            case 'location':
                /**
                 * 上传地理位置,接收位置消息
                 */
                $content = '你发送的是位置，纬度为：' . $postArr->Location_X
                    . '；经度为：' . $postArr->Location_Y
                    . '；缩放级别为：' . $postArr->Scale
                    . '；位置为：' . $postArr->Label;
                $result = $this->transmitText($postArr, $content);
                break;
            case 'link':
                /**
                 * 链接相关
                 * 接收链接消息
                 */
                $content = '你发送的是链接，标题为：' . $postArr->Title
                    . '；内容为：' . $postArr->Description
                    . '；链接地址为：' . $postArr->Url;
                $result = $this->transmitText($postArr, $content);
                break;
            default:
                $result = 'unknown msg type: ' . $RX_TYPE;
                break;
        }
        return $result;
    }
    // 接收事件消息
    private function receiveEvent($object)
    {
        // $uername=json_decode($object,true);
        // 当用户订阅后，需要存储下所有用户的信息，openid，昵称，地址等等；
        // 调用存储 函数，需要新创建。。。。
        $openID = $object->FromUserName;
        $EventKey = $object->EventKey;
        $content = "";
        switch ($object->Event) {
            case "subscribe": // 关注
                $content = "欢迎您,关注[十云]公众号\n十云提供技术支持\n ";
                $content .= (!empty($EventKey)) ? ("\n来自二维码场景 " . str_replace("qrscene_", "", $EventKey)) : "";
                break;
            case "unsubscribe": // 取消关注
                $content = "取消关注";
                break;
            case "SCAN": // 扫描
                $content = "扫描场景 " . $object->EventKey;
                break;
            case "LOCATION": // 地址
                $content = "上传位置：纬度 " . $object->Latitude . ";经度 " . $object->Longitude;
                break;
            case "CLICK": // 点击事件
                switch ($object->EventKey) {
                    default:
                        $content = "你点击了菜单: " . $object->EventKey;
                        break;
                }
                break;
            case "VIEW": // 跳转
                $content = "跳转链接 " . $object->EventKey;
                break;
            case "MASSSENDJOBFINISH":
                $content = "消息ID：" . $object->MsgID .
                    "，结果：" . $object->Status .
                    "，粉丝数：" . $object->TotalCount .
                    "，过滤：" . $object->FilterCount .
                    "，发送成功：" . $object->SentCount .
                    "，发送失败：" . $object->ErrorCount;
                break;
            case 'card_pass_check': // 卡券审核通过
                break;
            case 'card_not_pass_check': // 卡券审核失败
                break;
            case 'user_get_card': // 用户领取卡券
                break;
            case 'user_del_card': // 用户删除卡券
                break;
            case 'user_view_card': // 用户浏览会员卡
                break;
            case 'user_consume_card': // 用户核销卡券
                break;
            case 'kf_create_session': // 创建会话
                break;
            case 'kf_close_session': // 关闭会话
                break;
            case 'kf_switch_session': // 转接会话
                break;
            default:
                $content = "receive a new event: " . $object->Event;
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }
    /**
     * ==============================回复==============================
     */
    /**
     * 回复文本消息
     */
    public function transmitText($object, $content)
    {
        $msgArr = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' =>  $this->autoTextFilter($content),
        );
        $result = $this->arrayToXml($msgArr);
        return $result;
    }
    /**
     * 回复图片消息
     */
    public function transmitImage($object, $mediaid = '')
    {
        $msgArr = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'image',
            'Image' => [
                'MediaId' => $mediaid,
            ]
        );
        $result = $this->arrayToXml($msgArr);
        return $result;
    }
    /**
     * 回复语音消息
     * @param string $mediaid
     */
    public function transmitVoice($object, $mediaid = '')
    {
        $msgArr = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'voice',
            'Voice' => [
                'MediaId' => $mediaid,
            ]
        );
        $result = $this->arrayToXml($msgArr);
        return $result;
    }
    /**
     * 回复视频消息
     */
    public function transmitVideo($object, $videoArray)
    {
        $msgArr = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'video',
            'Video' => [
                'MediaId' =>  $videoArray['MediaId'],
                'Title' => $videoArray['Title'],
                'Description' => $videoArray['Description'],
            ]
        );
        $result = $this->arrayToXml($msgArr);
        return $result;
    }
    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     */
    public function transmitMusic2($title, $desc, $musicurl, $hgmusicurl = '', $thumbmediaid = '') {}
    // 回复音乐消息
    public function transmitMusic($object, $musicArray, $thumbmediaid)
    {
        $msgArr = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'music',
            'Music' => array(
                'Title' =>  $musicArray['Title'],
                'Description' => $musicArray['Description'],
                'MusicUrl' =>  $musicArray['MusicUrl'],
                'HQMusicUrl' =>   $musicArray['HQMusicUrl']
            ),
        );
        if ($thumbmediaid) {
            $msgArr['Music']['ThumbMediaId'] = $thumbmediaid;
        }
        $result = $this->arrayToXml($msgArr);
        return $result;
    }
    /**
     * 回复图文消息
     */
    public function transmitNews($object, $newsArray)
    {
        if (!is_array($newsArray)) {
            return;
        }
        $Articles = [];
        $Articles['item'] = [];
        foreach ($newsArray as $item) {
            $Articles['item']['Title'] = $item['Title'] ?? '';
            $Articles['item']['Description'] = $item['Description'] ?? '';
            $Articles['item']['PicUrl'] = $item['PicUrl'] ?? '';
            $Articles['item']['Url'] = $item['Url'] ?? '';
        }
        $msgArr = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'news',
            'ArticleCount' => count($newsArray),
            'Articles' => $Articles
        );
        $result = $this->arrayToXml($msgArr);
        return $result;
    }

    /**
     * ==============================客服相关==============================
     */

    /**
     * 转发 回复多客服消息
     * @param string $customer_account 转发到指定客服帐号：test1@test
     */
    public function transmitService($object, $customer_account = '')
    {
        $msgArr = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'transfer_customer_service',
        );
        if (!empty($customer_account)) {
            $msgArr['TransInfo'] = array(
                'KfAccount' => $customer_account
            );
        }
        $result = $this->arrayToXml($msgArr);
        return $result;
    }

    /**
     * 获取消息发送者
     */
    public function getRevFrom()
    {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }
    /**
     * 获取消息接受者
     */
    public function getRevTo()
    {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }
    public function arrayToXml($array = [], $xml = '', $isDeep = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $xml .= '<' . $key . '>';
                $xml = $this->arrayToXml($value, $xml, true);
                $xml .= '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
                // $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
            }
        }
        if (!$isDeep) {
            $xml = '<xml>' . $xml . '</xml>';
        }
        return $xml;
    }
    /**
     * 过滤文字回复\r\n换行符
     * @param string $text
     * @return string|mixed
     */
    protected function autoTextFilter($text)
    {
        if (!$this->_text_filter)
            return $text;
        return str_replace("\r\n", "\n", $text);
    }
    /**
     * xml格式加密，仅请求为加密方式时再用
     */
    private function generate($encrypt, $signature, $timestamp, $nonce)
    {
        // 格式化加密信息
        $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }
}
