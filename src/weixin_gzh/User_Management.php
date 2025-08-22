<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\exception\WeixinException;

/**
 * 微信 - 公众号 - 用户管理
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
 */
class User_Management extends WechatCommon
{
    /** 
     * ========== ========== ==========  用户标签管理 ========== ========== ==========
     * https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     */

    /**
     * --------------------
     * 用户管理 / 用户标签管理 / 创建标签
     * --------------------
     * @param string $name 标签名（30个字符以内）
     * @return boolean|array
     */
    public function wxUserTagsCreate(string $name = '')
    {
        if (empty($name) || strlen($name) > 30) {
            throw new WeixinException('参数错误');
        }
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'tag' => array(
                'name' => $name
            )
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/tags/create?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 用户管理 / 用户标签管理 / 获取公众号已创建的标签
     * --------------------
     * @return boolean|array
     */
    public function wxUserTagsGet()
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/tags/get', [
            'access_token' => $wxAccToken,
        ]);
        return $result;
    }
    /**
     * --------------------
     * 用户管理 / 用户标签管理 / 编辑标签
     * --------------------
     * @param int $tagid 分组id
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function wxUserTagsUpdate(int $tagid = 0, string $name = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'tag' => array(
                'id' => $tagid,
                'name' => $name
            )
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/tags/update?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 用户管理 / 用户标签管理 / 删除标签
     * --------------------
     * @param int $tagid 分组id
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function wxUserTagsDelete(int $tagid = 0)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'tag' => array(
                'id' => $tagid,
            )
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/tags/delete?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 用户管理 / 用户标签管理 / 获取标签下粉丝列表
     * --------------------
     * @param int $tagid 分组id
     * @param string $next_openid 第一个拉取的OPENID，不填默认从头开始拉取
     * @return boolean|array
     */
    public function wxUserTagsFansGet(int $tagid = 0, string $next_openid = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'tagid' => $tagid,
            'next_openid' => $next_openid,
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/user/tag/get?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /** 
     * ========== ========== ==========  用户管理 ========== ========== ==========
     */

    /**
     * --------------------
     * 批量为用户打标签
     * --------------------
     * @param string $openid_list 用户openid数组,一次不能超过20个
     * @param int    $tagid 分组id
     * @return boolean|array
     */
    public function wxUserAddTags(array $openid_list = [], int $tagid)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'openid_list' => $openid_list,
            'tagid' => $tagid
        );
        $url = WechatConst::URL_API_CGI_PREFIX .  '/tags/members/batchtagging?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 批量为用户取消标签
     * --------------------
     * @param string $openid_list 用户openid数组,一次不能超过20个
     * @param int    $tagid 分组id
     * @return boolean|array
     */
    public function wxUserRemoveTags(array $openid_list = [], int $tagid)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'openid_list' => $openid_list,
            'tagid' => $tagid
        );
        $url = WechatConst::URL_API_CGI_PREFIX .  '/tags/members/batchuntagging?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 获取用户身上的标签列表
     * --------------------
     * @param string $openid
     * @return boolean|int 成功则返回用户分组id
     */
    public function wxUserGetTags(string $openid = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'openid' => $openid
        );
        $url = WechatConst::URL_API_CGI_PREFIX . '/tags/getidlist?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        if (isset($result['tagid_list']))
            return $result['tagid_list'];
        return $result;
    }



    /** 
     * ========== ========== ==========  设置用户备注名 ========== ========== ==========
     * https://developers.weixin.qq.com/doc/offiaccount/User_Management/Configuring_user_notes.html
     */

    /**
     * --------------------
     * 用户管理 / 设置用户备注名
     * --------------------
     * @param string $openid
     * @param string $remark 备注名
     * @return boolean|array
     */
    public function wxUserUpdateRemark(string $openid = '', string $remark = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'openid' => $openid,
            'remark' => $remark
        );
        $url = WechatConst::URL_API_CGI_PREFIX . "/user/info/updateremark?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 用户管理 / 获取用户基本信息(UnionID机制)
     * --------------------
     * @param string $openid
     * @return array
     */
    public function wxUserInfo(string $openId = '')
    {
        if (empty($openId)) {
            return [];
        }
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/user/info', [
            'access_token' => $wxAccToken,
            'openid' => $openId,
            'lang' => 'zh_CN'
        ]);
        return $result;
    }

    /**
     * --------------------
     * 用户管理 / 获取用户列表
     * --------------------
     * @param string $next_openid 第一个拉取的OPENID
     */
    public function wxUserGet(string $next_openid = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/user/get', [
            'access_token' => $wxAccToken,
            'next_openid' => $next_openid,
        ]);
        return $result;
    }
}
