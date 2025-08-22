<?php


namespace shiyunWechat\weixin_gzh;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\libs\WeixinCache;
use shiyunWechat\libs\Prpcrypt;
use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;

class Other extends WechatCommon
{
    /**
     * 语义理解接口
     * @param String $uid 用户唯一id（非开发者id），用户区分公众号下的不同用户（建议填入用户openid）
     * @param String $query 输入文本串
     * @param String $category 需要使用的服务类型，多个用“，”隔开，不能为空
     * @param Float $latitude 纬度坐标，与经度同时传入；与城市二选一传入
     * @param Float $longitude 经度坐标，与纬度同时传入；与城市二选一传入
     * @param String $city 城市名称，与经纬度二选一传入
     * @param String $region 区域名称，在城市存在的情况下可省略；与经纬度二选一传入
     * @return boolean|array
     */
    public function querySemantic($uid, $query, $category, $latitude = 0, $longitude = 0, $city = "", $region = "")
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'query' => $query,
            'category' => $category,
            'appid' => $this->_appID,
            'uid' => ''
        );
        // 地理坐标或城市名称二选一
        if ($latitude) {
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;
        } elseif ($city) {
            $data['city'] = $city;
        } elseif ($region) {
            $data['region'] = $region;
        }

        $url = WechatConst::WX_API_BASE_PREFIX .  '/semantic/semproxy/search?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
}
