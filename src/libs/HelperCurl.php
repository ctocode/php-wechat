<?php

namespace shiyunWechat\libs;

use shiyunWechat\exception\WeixinException;
use Exception;

/**
 * 微信通用 curl 常用方法
 *
 */
class LibsCurlException extends Exception
{
}
class HelperCurl
{
    /**
     * --------------------
     * 微信提交API方法，返回微信指定JSON
     * 通用请求微信接口 [ 微信通讯 Communication ]
     * --------------------
     */
    public static function wxHttpsRequest($url, $data = null)
    {
        $chObj = curl_init();
        curl_setopt($chObj, CURLOPT_URL, $url);
        // curl_setopt($chObj,CURLOPT_HEADER,0);
        curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($chObj, CURLOPT_POST, 1);
            curl_setopt($chObj, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );

        /**
         * 执行请求并获取响应
         */
        $curlResponse = curl_exec($chObj);
        $curlInfo = curl_getinfo($chObj);
        if ($curlResponse === false || $curlInfo['http_code'] != 200) {
            $curlError = curl_error($chObj);
            throw new Exception($curlError);
        }
        curl_close($chObj);
        if (is_string($curlResponse) && json_validate($curlResponse)) {
            return json_decode($curlResponse, true);
        }
        return $curlResponse;
    }

    public static function curlHttpParamGet($url, $param = [])
    {
        $url_join = $url . '?' . http_build_query($param);
        $curlResult = self::curlHttpGet($url_join);
        if (is_string($curlResult)) {
            // 尝试解析 JSON 数据
            if (json_validate($curlResult)) {
                // JSON 数据有效
                $jsonData = json_decode($curlResult, true);
                if (!$jsonData || isset($jsonData['errcode'])) {
                    throw new WeixinException($jsonData['errmsg'], $jsonData['errcode']);
                }
                return $jsonData;
            } else {
                // JSON 无效
                return $curlResult;
            }
        } else {
            return $curlResult;
        }
    }
    public static function curlHttpGet($url)
    {
        $chObj = curl_init();
        curl_setopt($chObj, CURLOPT_URL, $url);
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true); // true或者1
        curl_setopt($chObj, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        // curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, 2);

        // 无证书
        if (stripos($url, "https://") !== false) {
            curl_setopt($chObj, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1); // CURL_SSLVERSION_TLSv1 或者1
            curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, false);

        /**
         * 执行请求并获取响应
         */
        $curlResponse = curl_exec($chObj);
        $curlInfo = curl_getinfo($chObj);
        if ($curlResponse === false || $curlInfo['http_code'] != 200) {
            $curlError = curl_error($chObj);
            throw new Exception($curlError);
        }
        curl_close($chObj);
        if (is_string($curlResponse) && json_validate($curlResponse)) {
            return json_decode($curlResponse, true);
        }
        return $curlResponse;
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    public static function curlHttpPost($url, $param, $post_file = false)
    {
        // $guzzHttp = new \GuzzleHttp\Client();
        // $response = $http->get($apiUrl);
        // $result = json_decode($response->getBody(), true);
        $chObj = curl_init();
        $chObj = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($chObj, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        // 设置url
        curl_setopt($chObj, CURLOPT_URL, $url);
        // TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
        // 设置发送方式：post
        curl_setopt($chObj, CURLOPT_POST, true);
        curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, "POST");
        // 设置发送数据
        curl_setopt($chObj, CURLOPT_POSTFIELDS, $strPOST);

        // curl_setopt($chObj, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: application/json'
        // ));

        /**
         * 执行请求并获取响应
         */
        $curlResponse = curl_exec($chObj);
        $curlInfo = curl_getinfo($chObj);
        if ($curlResponse === false || $curlInfo['http_code'] != 200) {
            $curlError = curl_error($chObj);
            throw new Exception($curlError);
        }
        curl_close($chObj);
        if (is_string($curlResponse) && json_validate($curlResponse)) {
            return json_decode($curlResponse, true);
        }
        return $curlResponse;
    }
}
