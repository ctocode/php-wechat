<?php

namespace shiyunWechat\weixin_gzh;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\WechatConst;

/**
 * 【ctocode】 微信 - 摇一摇类、
 * --------------------
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */
trait IntfShakearound
{
    /**
     * --------------------
     * 微信摇一摇 - 申请设备ID
     * --------------------
     * 申请配置设备所需的UUID、Major、Minor。
     * 申请成功后返回批次ID，可用返回的批次ID通过“查询设备ID申请状态”接口查询目前申请的审核状态。
     * @param array $data
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Devices_management/Apply_device_ID.html
     */
    public function wxDeviceApply($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_GZH_YYY_DEVI_APPLY . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * --------------------
     * 微信摇一摇 - 编辑设备ID
     * --------------------
     * 编辑设备信息
     * [updateShakeAroundDevice 编辑设备的备注信息。可用设备ID或完整的UUID、Major、Minor指定设备，二者选其一。]
     * @param array $data
     * @return boolean 
     */
    public function wxDeviceUpdate($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_GZH_YYY_DEVI_UPDATE . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return true;
        return $result;
    }

    /**
     * --------------------
     * 微信摇一摇 - 本店关联设备
     * --------------------
     * 配置设备与门店的关联关系
     * @param string $device_id 设备编号，若填了UUID、major、minor，则可不填设备编号，若二者都填，则以设备编号为优先
     * @param int $poi_id 待关联的门店ID
     * @param string $uuid UUID、major、minor，三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Devices_management/Configure_the_connected_relationship_between_the_device_and_the_store.html
     */
    public function wxDeviceBindLocation($device_id, $poi_id, $uuid = '', $major = 0, $minor = 0)
    {
        $wxAccToken = $this->wxAccessToken();
        if (!$device_id) {
            if (!$uuid || !$major || !$minor) {
                return false;
            }
            $device_identifier = array(
                'uuid' => $uuid,
                'major' => $major,
                'minor' => $minor
            );
        } else {
            $device_identifier = array(
                'device_id' => $device_id
            );
        }
        $data = array(
            'device_identifier' => $device_identifier,
            'poi_id' => $poi_id
        );
        $url = WechatConst::WX_GZH_YYY_DEVI_BIND_INFO . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return true;
        return $result;
    }

    /**
     * --------------------
     * 微信摇一摇 - 查询设备列表
     * --------------------
     * 查询设备列表
     * 查询已有的设备ID、UUID、Major、Minor、激活状态、备注信息、关联门店、关联页面等信息。
     * 可指定设备ID或完整的UUID、Major、Minor查询，也可批量拉取设备信息列表。查询所返回的设备列表按设备ID正序排序。
     * @param array $data
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Devices_management/Query_device_list.html
     */
    public function wxDeviceSearch($data)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_GZH_YYY_DEVI_SEARCH . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 配置设备与页面的关联关系
     * --------------------
     * 配置设备与页面的关联关系。
     * 支持建立或解除关联关系，也支持新增页面或覆盖页面等操作。
     * 配置完成后，在此设备的信号范围内，即可摇出关联的页面信息。
     * 若设备配置多个页面，则随机出现页面信息]
     * @param string $device_id 设备编号，若填了UUID、major、minor，则可不填设备编号，若二者都填，则以设备编号为优先
     * @param array $page_ids 待关联的页面列表
     * @param number $bind 关联操作标志位， 0 为解除关联关系，1 为建立关联关系
     * @param number $append 新增操作标志位， 0 为覆盖，1 为新增
     * @param string $uuid UUID、major、minor，三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Relationship_between_pages_and_devices/Device_settings_and_page_associations.html
     */
    public function wxDeviceBindPage($device_id, $page_ids = array(), $bind = 1, $append = 1, $uuid = '', $major = 0, $minor = 0)
    {
        $wxAccToken = $this->wxAccessToken();
        if (!$device_id) {
            if (!$uuid || !$major || !$minor) {
                return false;
            }
            $device_identifier = array(
                'uuid' => $uuid,
                'major' => $major,
                'minor' => $minor
            );
        } else {
            $device_identifier = array(
                'device_id' => $device_id
            );
        }
        $data = array(
            'device_identifier' => $device_identifier,
            'page_ids' => $page_ids,
            'bind' => $bind,
            'append' => $append
        );
        $url = WechatConst::WX_GZH_YYY_DEVI_BIND_PAGE . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 新增页面
     * --------------------
     * 新增摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * 其中，图片必须为用素材管理接口上传至微信侧服务器后返回的链接。
     * @param string $title 在摇一摇页面展示的主标题，不超过6 个字
     * @param string $description 在摇一摇页面展示的副标题，不超过7 个字
     * @param sting $icon_url 在摇一摇页面展示的图片， 格式限定为：jpg,jpeg,png,gif; 建议120*120 ， 限制不超过200*200
     * @param string $page_url 跳转链接
     * @param string $comment 页面的备注信息，不超过15 个字,可不填
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Pages_management/Page_management.html
     */
    public function addShakeAroundPage($title, $description, $icon_url, $page_url, $comment = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            "title" => $title,
            "description" => $description,
            "icon_url" => $icon_url,
            "page_url" => $page_url,
            "comment" => $comment
        );
        $url = WechatConst::WX_GZH_YYY_PAGE_ADD . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 编辑页面
     * --------------------
     * 编辑摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。]
     * @param int $page_id
     * @param string $title 在摇一摇页面展示的主标题，不超过6 个字
     * @param string $description 在摇一摇页面展示的副标题，不超过7 个字
     * @param sting $icon_url 在摇一摇页面展示的图片， 格式限定为：jpg,jpeg,png,gif; 建议120*120 ， 限制不超过200*200
     * @param string $page_url 跳转链接
     * @param string $comment 页面的备注信息，不超过15 个字,可不填
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Pages_management/Edit_page_information.html
     */
    public function wxPageUpdate($page_id, $title, $description, $icon_url, $page_url, $comment = '')
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            "page_id" => $page_id,
            "title" => $title,
            "description" => $description,
            "icon_url" => $icon_url,
            "page_url" => $page_url,
            "comment" => $comment
        );
        $url = WechatConst::WX_GZH_YYY_PAGE_UPDATE . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 查询页面
     * --------------------
     * 查询已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * 提供两种查询方式，①可指定页面ID 查询，②也可批量拉取页面列表。]
     * @param array $page_ids
     * @param int $begin
     * @param int $count
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Pages_management/Query_page_list.html
     */
    public function searchShakeAroundPage($page_ids = array(), $begin = 0, $count = 1)
    {
        $wxAccToken = $this->wxAccessToken();
        if (!empty($page_ids)) {
            $data = array(
                'page_ids' => $page_ids
            );
        } else {
            $data = array(
                'begin' => $begin,
                'count' => $count
            );
        }
        $url = WechatConst::WX_GZH_YYY_PAGE_SEARCH . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 删除页面
     * --------------------
     * 删除已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * 只有页面与设备没有关联关系时，才可被删除。
     * @param array $page_ids
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Pages_management/Delete_page.html
     */
    public function wxPageDelete($page_ids = array())
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'page_ids' => $page_ids
        );
        $url = WechatConst::WX_GZH_YYY_PAGE_DEL . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 上传图片素材
     * --------------------
     * 上传在摇一摇页面展示的图片素材
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * @param array $data 
     * @return boolean|array
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Uploading_Image_Assets.html
     */
    public function wxMaterialAdd($media = '')
    {
        $wxAccToken = $this->wxAccessToken();
        // $data['access_token'] = $wxAccToken;
        $data['media'] = str_starts_with('@', $media) ? $media : '@' . $media;
        $url = WechatConst::WX_GZH_YYY_ADD_MATERIAL . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $data, true);
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 获取摇周边的设备及用户信息
     * --------------------
     * 获取设备信息，包括UUID、major、minor，以及距离、openID等信息
     * @param string $ticket 摇周边业务的ticket，可在摇到的URL 中得到，ticket生效时间为30 分钟
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Getting_Device_and_User_Information.html
     */
    public function wxGetShakeInfo($ticket)
    {
        $data = array(
            'ticket' => $ticket,
            'need_poi' => 1
        );
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_GZH_YYY_GET_GEO_INFO . "?access_token={$wxAccToken}";
        // $result = HelperCurl::curlHttpPost($url, $jsonData);
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
    /**
     * --------------------
     * 微信摇一摇 - 以设备为维度的数据统计接口
     * --------------------
     * 查询单个设备进行摇周边操作的人数、次数，点击摇周边消息的人数、次数；  
     * 查询的最长时间跨度为30天。只能查询最近90天的数据。  
     * 此接口无法获取当天的数据，最早只能获取前一天的数据。  
     * 由于系统在凌晨处理前一天的数据，太早调用此接口可能获取不到数据，建议在早上8：00之后调用此接口。  
     * 
     * @param int $device_id 设备编号，若填了UUID、major、minor，即可不填设备编号，二者选其一
     * @param int $begin_date 起始日期时间戳，最长时间跨度为30 天
     * @param int $end_date 结束日期时间戳，最长时间跨度为30 天
     * @param string $uuid UUID、major、minor，三个信息需填写完成，若填了设备编辑，即可不填此信息，二者选其一
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Analytics/Using_devices_as_a_dimension_for_the_data_statistics_interface.html
     */
    public function wxGetShakeStatistics($device_id, $begin_date, $end_date, $uuid = '', $major = 0, $minor = 0)
    {
        if (!$device_id) {
            if (!$uuid || !$major || !$minor) {
                return false;
            }
            $device_identifier = array(
                'uuid' => $uuid,
                'major' => $major,
                'minor' => $minor
            );
        } else {
            $device_identifier = array(
                'device_id' => $device_id
            );
        }
        $data = array(
            'device_identifier' => $device_identifier,
            'begin_date' => $begin_date,
            'end_date' => $end_date
        );
        $wxAccToken = $this->wxAccessToken();
        $url = WechatConst::WX_GZH_YYY_COUNT_DEVI . "?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }

    /**
     * 查询单个页面通过摇周边摇出来的人数、次数，点击摇周边页面的人数、次数；
     * 查询的最长时间跨度为30天。只能查询最近90天的数据。  
     * 此接口无法获取当天的数据，最早只能获取前一天的数据。由于系统在凌晨处理前一天的数据，
     * 太早调用此接口可能获取不到数据，建议在早上8：00之后调用此接口。  
     * @param int $page_id 指定页面的ID
     * @param int $begin_date 起始日期时间戳，最长时间跨度为30 天
     * @param int $end_date 结束日期时间戳，最长时间跨度为30 天
     * @return boolean|mixed
     * https://developers.weixin.qq.com/doc/offiaccount/Shake_Nearby/Analytics/Using_pages_as_a_dimension_for_the_data_statistics_interface.html
     */
    public function pageShakeAroundStatistics($page_id, $begin_date, $end_date)
    {
        $wxAccToken = $this->wxAccessToken();
        $data = array(
            'page_id' => $page_id,
            'begin_date' => $begin_date,
            'end_date' => $end_date
        );
        $url = WechatConst::WX_GZH_YYY_COUNT_PAGE . '?access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return $result;
    }
}
