<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="/js/jquery/jquery-1.12.4.min.js"></script>
    <title>Document</title>
</head>
<body>
<table class="layui-table">
    <colgroup>
        <col width="150">
        <col width="200">
        <col>
    </colgroup>
    <thead>
    <tr>
        <th></th>
        <th>Openid</th>
        <th>昵称</th>

    </tr>
    </thead>
    <tbody>
    @foreach($data as $k=>$v)
    <tr openid="{{$v['openid']}}">
        <td><input type="checkbox" class="box"></td>
        <td>{{$v['openid']}}</td>
        <td>{{$v['nickname']}}</td>
    </tr>
        @endforeach

    </tbody>
</table>

    <div class="layui-form-item">
        <label class="layui-form-label">内容</label>
        <div class="layui-input-block">
            <input type="text" name="title" required  lay-verify="required" id="text" placeholder="请输入文本" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">立即发送</button>

        </div>
    </div>

</body>
</html>
<link rel="stylesheet" href="/js/layui/css/layui.css">
<script>
    $(function() {
            $('.layui-btn').click(function () {
                var _box=$('.box');
                var openid='';
                _box.each(function(index){
                    if($(this).prop('checked')==true){
                        openid += $(this).parents('tr').attr('openid')+',';
                    }
                })
                openid=openid.substr(0,openid.length-1);
                var text=$('#text').val();
                $.post(
                    '/admin/user/allsenddo',
                    {openid:openid,text:text},
                    function(res){
                        if(res==1){
                            alert('发送成功');
                        }
                    }
                )
            })
    });
</script>