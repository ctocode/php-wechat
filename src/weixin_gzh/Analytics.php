<?php

namespace shiyunWechat\weixin_gzh;

// use shiyunWechat\docs\annotation\DocsMdTable;
use shiyunWechat\WechatCommon;
use shiyunWechat\WechatConst;
use shiyunWechat\libs\HelperCurl;

/**
 * 微信 - 公众号 - 数据分析
 * --------------------
 * @link 文档地址 
 * https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html
 */
// #[DocsMdTable(
//     title: '数据分析',
//     desc: '公众号 - 数据分析',
//     link: 'https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html',
// )]
class Analytics extends WechatCommon
{
    /**
     * 获取统计数据
     * @param string $type 数据分类(user|article|upstreammsg|interface)分别为(用户分析|图文分析|消息分析|接口分析)
     * @param string $subtype 数据子分类，参考 DATACUBE_URL_ARR 常量定义部分 或者README.md说明文档
     * @param string $begin_date 开始时间
     * @param string $end_date 结束时间
     * @return boolean|array 成功返回查询结果数组，其定义请看官方文档
     */
    public function getDatacube($type, $subtype, $begin_date, $end_date = '')
    {
        $wxAccToken = $this->wxAccessToken();

        // /数据分析接口
        $DATACUBE_URL_ARR = array( // 用户分析
            'user' => array(
                'summary' => '/datacube/getusersummary?', // 获取用户增减数据（getusersummary）
                'cumulate' => '/datacube/getusercumulate?' // 获取累计用户数据（getusercumulate）
            ),
            'article' => array( // 图文分析
                'summary' => '/datacube/getarticlesummary?', // 获取图文群发每日数据（getarticlesummary）
                'total' => '/datacube/getarticletotal?', // 获取图文群发总数据（getarticletotal）
                'read' => '/datacube/getuserread?', // 获取图文统计数据（getuserread）
                'readhour' => '/datacube/getuserreadhour?', // 获取图文统计分时数据（getuserreadhour）
                'share' => '/datacube/getusershare?', // 获取图文分享转发数据（getusershare）
                'sharehour' => '/datacube/getusersharehour?' // 获取图文分享转发分时数据（getusersharehour）
            ),
            'upstreammsg' => array( // 消息分析
                'summary' => '/datacube/getupstreammsg?', // 获取消息发送概况数据（getupstreammsg）
                'hour' => '/datacube/getupstreammsghour?', // 获取消息分送分时数据（getupstreammsghour）
                'week' => '/datacube/getupstreammsgweek?', // 获取消息发送周数据（getupstreammsgweek）
                'month' => '/datacube/getupstreammsgmonth?', // 获取消息发送月数据（getupstreammsgmonth）
                'dist' => '/datacube/getupstreammsgdist?', // 获取消息发送分布数据（getupstreammsgdist）
                'distweek' => '/datacube/getupstreammsgdistweek?', // 获取消息发送分布周数据（getupstreammsgdistweek）
                'distmonth' => '/datacube/getupstreammsgdistmonth?' // 获取消息发送分布月数据（getupstreammsgdistmonth）
            ),
            'interface' => array( // 接口分析
                'summary' => '/datacube/getinterfacesummary?', // 获取接口分析数据（getinterfacesummary）
                'summaryhour' => '/datacube/getinterfacesummaryhour?' // 获取接口分析分时数据（getinterfacesummaryhour）
            )
        );

        if (!isset($DATACUBE_URL_ARR[$type]) || !isset($DATACUBE_URL_ARR[$type][$subtype]))
            return false;
        $data = array(
            'begin_date' => $begin_date,
            'end_date' => $end_date ? $end_date : $begin_date
        );
        $url = WechatConst::WX_API_BASE_PREFIX . $DATACUBE_URL_ARR[$type][$subtype] . 'access_token=' . $wxAccToken;
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        return isset($result['list']) ? $result['list'] : $result;
        return $result;
    }
}
