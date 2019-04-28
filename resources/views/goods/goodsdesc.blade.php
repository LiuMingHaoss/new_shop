<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
        .full-height {
            height: 100vh;
        }
        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }
        .position-ref {
            position: relative;
        }
        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }
        .content {
            text-align: center;
        }
        .title {
            font-size: 84px;
        }
        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">

        </div>
        <table>
           <tr>
               <td>商品名称：</td>
               <td>商品价格：</td>
               <td>浏览次数：</td>
           </tr>
            <tr>
                <td>{{$data['goods_name']}}</td>
                <td>{{$data['goods_price']}}</td>
                <td>{{$view}}</td>

            </tr>

        </table>
        <hr>


            <table>
                <tr>
                    <td>浏览排行</td>
                    <td>浏览次数</td>
                </tr>
                @foreach($goodsInfo as $k=>$v)
                <tr>
                    <td width="150">{{$v['goods_name']}}</td>
                    <td>{{$v['view']}}</td>
                </tr>
                @endforeach
            </table>

        <hr>
        <table>
            <tr>
                <td>浏览历史</td>
                <td>最后一次浏览时间</td>
            </tr>
            @foreach($last_look as $k=>$v)
                <tr>
                    <td width="150">{{$v['goods_name']}}</td>
                    <td>{{date('Y-m-d H:i:s',$v['last_time'])}}</td>
                </tr>
            @endforeach
        </table>
        <div id="qrcode"></div>
    </div>
</div>

<script src="/js/jquery/jquery-1.12.4.min.js"></script>
<script src="/js/weixin/qrcode.js"></script>
<script type="text/javascript">
    new QRCode(document.getElementById("qrcode"), "{{$url}}");
</script>
</body>
</html>