<?php

namespace shiyunWechat;

class WechatConst
{
    // 以下API接口URL需要使用此前缀
    /**
     * api
     */
    const WX_API_BASE_PREFIX = 'https://api.weixin.qq.com';
    const URL_API_CGI_PREFIX = 'https://api.weixin.qq.com/cgi-bin';



    /**
     * 公众号
     */
    const URL_MP_BASE_PREFIX = 'https://mp.weixin.qq.com';
    const URL_MP_CGI_PREFIX = 'https://mp.weixin.qq.com/cgi-bin';
    const URL_UPLOAD_MEDIA = 'http://file.api.weixin.qq.com/cgi-bin';


    /**
     * 公众号 - 摇一摇
     */


    /**
     * 公众号 - 摇一摇
     */
    // 其他
    const WX_GZH_YYY_COUNT_DEVI = self::WX_API_BASE_PREFIX . '/shakearound/statistics/device';
    const WX_GZH_YYY_COUNT_PAGE = self::WX_API_BASE_PREFIX . '/shakearound/statistics/page';
    const WX_GZH_YYY_ADD_MATERIAL = self::WX_API_BASE_PREFIX . '/shakearound/material/add';
    const WX_GZH_YYY_GET_GEO_INFO = self::WX_API_BASE_PREFIX . '/shakearound/user/getshakeinfo';
    // 设备
    const WX_GZH_YYY_DEVI_APPLY = self::WX_API_BASE_PREFIX . '/shakearound/device/applyid';
    const WX_GZH_YYY_DEVI_UPDATE = self::WX_API_BASE_PREFIX . '/shakearound/device/update';
    const WX_GZH_YYY_DEVI_SEARCH = self::WX_API_BASE_PREFIX . '/shakearound/device/search';
    const WX_GZH_YYY_DEVI_BIND_INFO = self::WX_API_BASE_PREFIX . '/shakearound/device/bindlocation';
    const WX_GZH_YYY_DEVI_BIND_PAGE = self::WX_API_BASE_PREFIX . '/shakearound/device/bindpage';
    // 页面
    const WX_GZH_YYY_PAGE_ADD = self::WX_API_BASE_PREFIX . '/shakearound/page/add';
    const WX_GZH_YYY_PAGE_UPDATE = self::WX_API_BASE_PREFIX . '/shakearound/page/update';
    const WX_GZH_YYY_PAGE_SEARCH = self::WX_API_BASE_PREFIX . '/shakearound/page/search';
    const WX_GZH_YYY_PAGE_DEL = self::WX_API_BASE_PREFIX . '/shakearound/page/delete';



    /**
     * 企业微信
     */
    const WX_API_QY_CGI_PREFIX = 'https://qyapi.weixin.qq.com/cgi-bin';
    // 其他
    const WX_QYH_MESSAGE_SEND = self::URL_API_CGI_PREFIX . '/message/send';
    // 菜单
    const WX_QYH_MENU_GET = self::URL_API_CGI_PREFIX . '/menu/get';
    const WX_QYH_MENU_DEL = self::URL_API_CGI_PREFIX . '/menu/delete';
    // 部门
    const WX_QYH_DEPT_CREATE = self::WX_API_QY_CGI_PREFIX . '/department/create';
    const WX_QYH_DEPT_UPDATE = self::WX_API_QY_CGI_PREFIX . '/department/update';
    const WX_QYH_DEPT_DEL = self::WX_API_QY_CGI_PREFIX . '/department/delete';
    const WX_QYH_DEPT_MOVE = self::WX_API_QY_CGI_PREFIX .  '/department/move';
    const WX_QYH_DEPT_LIST = self::WX_API_QY_CGI_PREFIX . '/department/list';
    // 员工
    const WX_QYH_STAFF_CREATE = self::WX_API_QY_CGI_PREFIX .  '/user/create';
    const WX_QYH_STAFF_DEL = self::WX_API_QY_CGI_PREFIX . '/user/delete';
    const WX_QYH_STAFF_GET = self::WX_API_QY_CGI_PREFIX .  '/user/get';
    const WX_QYH_STAFF_UPDATE = self::WX_API_QY_CGI_PREFIX . '/user/update';
    const WX_QYH_STAFF_LIST = self::WX_API_QY_CGI_PREFIX . '/user/simplelist';
    const WX_QYH_STAFF_INFO = self::WX_API_QY_CGI_PREFIX . '/user/getuserinfo';
    const WX_QYH_STAFF_AUTH = self::WX_API_QY_CGI_PREFIX . '/user/authsucc';
    // 标签
    const WX_QYH_TAG_CREATE = self::WX_API_QY_CGI_PREFIX .  '/tag/create';
    const WX_QYH_TAG_UPDATE = self::WX_API_QY_CGI_PREFIX . '/tag/update';
    const WX_QYH_TAG_DEL = self::WX_API_QY_CGI_PREFIX . '/tag/delete';
    const WX_QYH_TAG_GET = self::WX_API_QY_CGI_PREFIX .  '/tag/get';
    const WX_QYH_TAG_LIST = self::WX_API_QY_CGI_PREFIX . '/tag/list';
    const WX_QYH_TAG_USER_ADD = self::WX_API_QY_CGI_PREFIX . '/tag/addtagusers';
    const WX_QYH_TAG_USER_DEL = self::WX_API_QY_CGI_PREFIX . '/tag/deltagusers';


    /**
     * 小程序
     */
    const WX_XCX_CREATE_QRCODE = self::WX_API_BASE_PREFIX . '/wxa/getwxacodeunlimit';




    /**
     * oauth
     */
    const WX_OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const WX_OAUTH_AUTHORIZE = self::WX_OAUTH_PREFIX . '/authorize';
    const WX_OAUTH_USERINFO = self::WX_API_BASE_PREFIX . '/sns/userinfo';
    // const WX_OAUTH_USERINFO = '/sns/oauth2/sns/userinfo';
    const WX_OAUTH_AUTH = self::WX_API_BASE_PREFIX . '/sns/auth';
    const WX_OAUTH_TOKEN = self::WX_API_BASE_PREFIX . '/sns/oauth2/access_token';
    const WX_OAUTH_REFRESH = self::WX_API_BASE_PREFIX . '/sns/oauth2/refresh_token';
}
