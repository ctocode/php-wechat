# 菜单


- 功能

| 方法         | 说明     | 详情 |
| :----------- | :------- | :--- |
| wxMenuGet    | 获取菜单 |      |
| wxMenuCreate | 创建菜单 |      |
| wxMenuDelete | 删除菜单 |      |
| wxMenuDelete | 删除菜单 |      |

- 使用
- 
```php

use shiyunSdk\wechatApi\wxMenu;

$wxObj = new wxMenu();

$wxObj->setAppId()->setAppSecret()->getToken()->方法();


```
## wxMenuCreate($jsonData)

- 功能说明
~~~
创建菜单(认证后的订阅号可用)
@param array $data 菜单数组数据
type可以选择为以下几种，会收到相应类型的事件推送。请注意，3到8的所有事件，仅支持微信iPhone5.4.1以上版本，
和Android5.4以上版本的微信用户，旧版本微信用户点击后将没有回应，开发者也不能正常接收到事件推送。
type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
1、click：点击推事件
2、view：跳转URL
3、scancode_push：扫码推事件
4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
5、pic_sysphoto：弹出系统拍照发图
6、pic_photo_or_album：弹出拍照或者相册发图
7、pic_weixin：弹出微信相册发图器
8、location_select：弹出地理位置选择器
~~~

- $jsonData 格式说明
  
```php

array (
    'button' => array (
      0 => array (
        'name' => '扫码',
        'sub_button' => array (
            0 => array (
              'type' => 'scancode_waitmsg',
              'name' => '扫码带提示',
              'key' => 'rselfmenu_0_0',
            ),
            1 => array (
              'type' => 'scancode_push',
              'name' => '扫码推事件',
              'key' => 'rselfmenu_0_1',
            ),
        ),
      ),
      1 => array (
        'name' => '发图',
        'sub_button' => array (
            0 => array (
              'type' => 'pic_sysphoto',
              'name' => '系统拍照发图',
              'key' => 'rselfmenu_1_0',
            ),
            1 => array (
              'type' => 'pic_photo_or_album',
              'name' => '拍照或者相册发图',
              'key' => 'rselfmenu_1_1',
            )
        ),
      ),
      2 => array (
        'type' => 'location_select',
        'name' => '发送位置',
        'key' => 'rselfmenu_2_0'
      ),
    ),
)

```