<?php

namespace shiyunWechat\weixin_qyh;

use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

trait TraitTags
{
    /**
     * 创建标签
     * @param array $data 结构体为:
     * array(
     *    "tagname" => "UI"
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "created",  //对返回码的文本描述内容
     *   "tagid": "1"
     * }
     */
    public function createTag($data = [])
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_QYH_TAG_CREATE . '?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 更新标签
     * @param array $data 	结构体为:
     * array(
     *    "tagid" => "1",
     *    "tagname" => "UI design"
     * )
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "updated"  //对返回码的文本描述内容
     * }
     */
    public function updateTag($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_QYH_TAG_UPDATE . '?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 删除标签
     * @param $tagid  标签TagID
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "deleted"  //对返回码的文本描述内容
     * }
     */
    public function deleteTag($tagid)
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_QYH_TAG_DEL, [
            'access_token' => $wxAccToken,
            'tagid' => $tagid,
        ]);
        return $result;
    }

    /**
     * 获取标签成员
     * @param $tagid  标签TagID
     * @return boolean|array	 成功返回结果
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "userlist": [
     *          {
     *              "userid": "zhangsan",
     *              "name": "李四"
     *          }
     *      ]
     * }
     */
    public function getTag($tagid)
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_QYH_TAG_GET, [
            'access_token' => $wxAccToken,
            'tagid' => $tagid,
        ]);
        return $result;
    }

    /**
     * 增加标签成员
     * @param array $data 	结构体为:
     * array (
     *    "tagid" => "1",
     *    "userlist" => array(    //企业员工ID列表
     *         "user1",
     *         "user2"
     *     )
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "ok",  //对返回码的文本描述内容
     *   "invalidlist"："usr1|usr2|usr"     //若部分userid非法，则会有此段。不在权限内的员工ID列表，以“|”分隔
     * }
     */
    public function addTagUser($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_QYH_TAG_USER_ADD . '?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 删除标签成员
     * @param array $data 	结构体为:
     * array (
     *    "tagid" => "1",
     *    "userlist" => array(    //企业员工ID列表
     *         "user1",
     *         "user2"
     *     )
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "deleted",  //对返回码的文本描述内容
     *   "invalidlist"："usr1|usr2|usr"     //若部分userid非法，则会有此段。不在权限内的员工ID列表，以“|”分隔
     * }
     */
    public function delTagUser($data)
    {
        $wxAccToken = $this->wxAccessToken();

        $url = WechatConst::WX_QYH_TAG_USER_DEL . '?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 获取标签列表
     * @return boolean|array	 成功返回数组结果，这里附上json样例
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "taglist":[
     *       {"tagid":1,"tagname":"a"},
     *       {"tagid":2,"tagname":"b"}
     *    ]
     * }
     */
    public function getTagList()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::WX_QYH_TAG_LIST, [
            'access_token' => $wxAccToken
        ]);
        return $result;
    }
}
