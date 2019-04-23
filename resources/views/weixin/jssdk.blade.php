<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<button id="btn">选择照片</button>
<button id="btn2">分享</button>
<img src="" alt="" id="img0" width="300">
<img src="" alt="" id="img1" width="300">
<img src="" alt="" id="img2" width="300">
<script src="/js/jquery/jquery-1.12.4.min.js"></script>
<script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script>
    wx.config({
        // debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "{{$jsconfig['appId']}}", // 必填，公众号的唯一标识
        timestamp:"{{$jsconfig['timestamp']}}" , // 必填，生成签名的时间戳
        nonceStr:"{{$jsconfig['nonceStr']}}", // 必填，生成签名的随机串
        signature:"{{$jsconfig['signature']}}",// 必填，签名
        jsApiList: ['chooseImage','uploadImage','downloadImage','updateAppMessageShareData'] // 必填，需要使用的JS接口列表
    });

    wx.ready(function(){
        $('#btn').click(function(){
            wx.chooseImage({
                count: 3, // 默认9
                sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    var img='';
                    $.each(localIds,function(i,val){
                        img+=val+','
                        var node = "#img"+i;
                        $(node).attr('src',val);

                        wx.uploadImage({
                            localId: val, // 需要上传的图片的本地ID，由chooseImage接口获得
                            isShowProgressTips: 1, // 默认为1，显示进度提示
                            success: function (res) {
                                var serverId = res.serverId; // 返回图片的服务器端ID
                                wx.downloadImage({
                                    serverId: serverId, // 需要下载的图片的服务器端ID，由uploadImage接口获得
                                    isShowProgressTips: 1, // 默认为1，显示进度提示
                                    success: function (res) {
                                        var localId = res.localId; // 返回图片下载后的本地ID
                                        alert(localId);
                                    }
                                });
                                $.ajax({
                                    url:'/weixin/upload?media_id='+serverId,
                                    type:'get',
                                    success:function(res){
                                        // alert(res);
                                    }
                                })
                            }
                        });
                    })

                    $.ajax({
                        url:'/weixin/getImg?img='+img,     //将上传的照片id发送给后端
                        type:'get',
                        success:function(res){
                            // alert(res);
                        }
                    })
                }
            });
        })
        $('#btn2').click(function(){
            wx.updateAppMessageShareData({
                title: '哈喽', // 分享标题
                desc: '你好', // 分享描述
                link: '1809liuminghao.comcto.com', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '', // 分享图标
                success: function () {
                    // 设置成功
                }
            })
        })
    })
</script>
</body>
</html>