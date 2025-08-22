<?php
    require_once 'lib.inc.php';
    $wx = new \shiyunWechat\WxJsSdk();
     
    if(!isset($_GET['code'])){
        header("location:{WechatConst::WX_OAUTH_AUTHORIZE}?appid=".WxApi::appId."&redirect_uri=http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."&response_type=code&scope=snsapi_base&state=1#wechat_redirect");
    }
    else{
        $CODE =  $_GET['code'];
        $Info = $wx->getOauthAccessToken($CODE);
        //print_r($Info);
        $openId = $Info['openid'];
         
    }
    $signPackage = $wx->wxJsApiPackage();
?>
<html>

<head>
    <title>JSAPI接口测试</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        wx.config({
            debug: true,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: <? php echo $signPackage["timestamp"];?>,
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            'onMenuShareTimeline',
            'onMenuShareAppMessage'
        ]
            });

        wx.ready(function () {
            // 在这里调用 API
            wx.onMenuShareAppMessage({
                title: '互联网之子',
                desc: '在长大的过程中，我才慢慢发现，我身边的所有事，别人跟我说的所有事，那些所谓本来如此，注定如此的事，它们其实没有非得如此，事情是可以改变的。更重要的是，有些事既然错了，那就该做出改变。',
                link: 'http://movie.douban.com/subject/25785114/',
                imgUrl: 'http://demo.open.weixin.qq.com/jssdk/images/p2166127561.jpg',
                trigger: function (res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                    alert('用户点击发送给朋友');
                },
                success: function (res) {
                    alert('已分享');
                },
                cancel: function (res) {
                    alert('已取消');
                },
                fail: function (res) {
                    alert(JSON.stringify(res));
                }
            });

            wx.onMenuShareTimeline({
                title: '互联网之子',
                link: 'http://movie.douban.com/subject/25785114/',
                imgUrl: 'http://demo.open.weixin.qq.com/jssdk/images/p2166127561.jpg',
                trigger: function (res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                    alert('用户点击分享到朋友圈');
                },
                success: function (res) {
                    alert('已分享');
                },
                cancel: function (res) {
                    alert('已取消');
                },
                fail: function (res) {
                    alert(JSON.stringify(res));
                }
            });
        });

        var readyFunc = function onBridgeReady() {
            document.querySelector('#batchAddCard').addEventListener('click', function (e) {
                WeixinJSBridge.invoke('batchAddCard', {
                    "card_list": [<? php echo $kqInfo;?>]
                            },
                function (res) {
                    alert(res.err_msg + "  _  " + res.card_list);
                });
        }); 
            }
        if (typeof WeixinJSBridge === "undefined") {
            document.addEventListener('WeixinJSBridgeReady', readyFunc, false);
        }
        else {
            readyFunc();
        }
    </script>
</head>

<body>
    <div>
        <input type="button" id="batchAddCard" name="batchAddCard" value="添加卡券">
    </div>
</body>

</html>