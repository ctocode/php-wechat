


### wxDeviceUpdate 参数

- $data
```php
array(
    "device_identifier" => array(
        "device_id" => 10011,   //当提供了device_id则不需要使用uuid、major、minor，反之亦然
        "uuid" => "FDA50693-A4E2-4FB1-AFCF-C6EB07647825",
        "major" => 1002,
        "minor" => 1223
    ),
    "comment" => "测试专用", //备注(非必填)
)

{
    "data": {},
    "errcode": 0,
    "errmsg": "success."
}
