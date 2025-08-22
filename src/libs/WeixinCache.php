<?php

namespace shiyunWechat\libs;

class WeixinCache
{
    public $cacheType = 'file'; // 缓存类型
    /**
     * 设置缓存，按需重载
     * @param string $cacheName
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    public static function setCache($cacheName, $value, $expired)
    {
        // TODO: set cache implementation
        $where = array();
        $where['access_token'] = $value;
        $where['expire_time'] = time();
        $where['expire_in'] = $expired;
        $apiResultData = ctoHttpCurl(_URL_API_ . "wx/opt", array(
            'type' => 'updateSet',
            'wx_id' => _TOOL_WX_SETT_ID_,
            'data' => $where
        ));
        $set = json_decode($apiResultData, true);
        if ($set) {
            return true;
        } else {
            return false;
        }
        switch ($this->cacheType) {
            case 'cache':
                Cache::set('jsapi_ticket', $value, 110); // jsapi_ticket有效期2小时，提前10分钟获取
                break;
            case 'curl':
                ctoHttpCurl(_URL_API_ . "wx/opt", array(
                    'type' => 'updateSet',
                    'wx_id' => _TOOL_WX_SETT_ID_,
                    'data' => $value
                ));
            case 'file':
                // ==== 文件存储方式
                self::set_php_file("/{$cacheName}.php", json_encode($value));
                break;

            default:;
                break;
        }
    }

    /**
     * 获取缓存，按需重载
     * @param string $cacheName
     * @return mixed
     */
    public static function getCache($cacheName)
    {
        // TODO: get cache implementation
        $apiResultData = ctoHttpCurl(_URL_API_ . "wx/opt", array(
            'type' => 'getSet',
            'wx_id' => _TOOL_WX_SETT_ID_
        ));
        $set = json_decode($apiResultData, true)['data'];
        if ((time() - $set['expire_in']) > $set['expire_time']) {
            return false;
        } else {
            return $set['access_token'];
        }
        switch ($this->cacheType) {
            case 'cache':
                $data = Cache::get($cacheName);
                break;
            case 'curl':
                $cacheData = ctoHttpCurl(_URL_API_ . "wx/opt", array(
                    'type' => 'getSet',
                    'wx_id' => _TOOL_WX_SETT_ID_
                ));
                $data = json_decode($cacheData, true)['data'];
                break;
            case 'file':
                // ==== 文件存储方式
                $cacheData = trim(substr(file_get_contents(__DIR__ . "/{$cacheName}.php"), 15));
                $data = json_decode($cacheData);
                break;
            default:;
                break;
        }
        return $data;
    }

    /**
     * 清除缓存，按需重载
     * @param string $cacheName
     * @return boolean
     */
    public static function removeCache($cacheName)
    {
        // TODO: remove cache implementation
        return false;
    }
    private function setFileCache()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $cacheData = trim(substr(file_get_contents(__DIR__ . '/access_token.php'), 15));
        $jsonInfo = json_decode($cacheData);
        if ($jsonInfo->expire_time > time()) {
            $access_token = $jsonInfo->access_token;
            return $access_token;
        }
        $access_token = $res->access_token;
        if ($access_token) {
            $jsonInfo->expire_time = time() + 7000;
            $jsonInfo->access_token = $access_token;
            // Cache::set ( 'access_token', $access_token, 10 );
            $this->set_php_file("access_token.php", json_encode($jsonInfo));
        }
        return $access_token;
    }
    private function set_php_file($filename, $content)
    {
        // file_put_contents("./Data/jsapi_ticket.json", json_encode($data));
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        // fwrite($fp, json_encode($data));
        fclose($fp);
    }
    /**
     * 把cookie写入缓存
     * @param  string $filename 缓存文件名
     * @param  string $content  文件内容
     * @return bool
     */
    public function saveCookie($filename, $content)
    {
        // return S($filename, $content, $this->_cookieexpired);
    }
    /**
     * 读取cookie缓存内容
     * @param  string $filename 缓存文件名
     * @return string cookie
     */
    public function getCookie($filename)
    {
        // $data = S($filename);
        if ($data) {
            $login = json_decode($data, true);
            return $cacheData;
        } else {
            return false;
        }
    }
}
