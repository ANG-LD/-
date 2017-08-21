<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <title><?php echo ($re["title"]); ?></title>
    <link rel="stylesheet" href="/public/share/css/reset.css">
    <link rel="stylesheet" href="/public/share/css/style.css">
    <script src="/public/share/js/phlice.js"></script>
    <script type="text/javascript" src="/public/admin/js/jquery-1.7.min.js"></script>
    <style>
        .dow{width:100%;height:1rem;text-align: center;position: fixed;bottom:0;left:0;background:#f0f0f0;}
        .dows{margin:auto;width:60%;overflow: hidden;}
        .logos{height:.7rem;display: block;float: left;margin-top:.15rem;}
        .button{float: right;width:1.5rem;height:.5rem;line-height: .5rem;text-align:center;background:#ce1939;border-radius:5px;text-align: center;display: block;color:#fff;margin-top:.2rem;}
    </style>
</head>
<body>
<div class="user_xx">
    <div class="user_img">
        <img src="<?php echo ($re["head_img"]); ?>" alt="">
    </div>
    <div class="user_names">
        <div class="user_name <?php if($re["sex"] == 2): ?>woman<?php else: ?>man<?php endif; ?>"><?php echo ($re["username"]); ?></div>
        <div class="Speak_time"><?php echo ($re["date_value"]); ?></div>
    </div>
</div>
<div class="title"><?php echo ($re["title"]); ?></div>
<div class="text"><?php echo ($re["content"]); ?></div>
<div class="imgboxs">
    <?php if(is_array($re["img"])): $i = 0; $__LIST__ = $re["img"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><img src="<?php echo ($l['img']); ?>" alt=""><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div class="Speakbox">
    <div class="Speaktop"></div>
    <div class="Speak">
        <ul>
            <?php if(is_array($re["comment"])): $i = 0; $__LIST__ = $re["comment"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                <div class="user_box">
                    <div class="user_img">
                        <img src="<?php echo ($vo["img"]); ?>" alt="">
                    </div>
                    <div class="user_names">
                        <div class="user_name <?php if($vo["sex"] == 2): ?>woman<?php else: ?>man<?php endif; ?>"><?php echo ($vo["username"]); ?></div>
                    </div>
                    <div class="report tz">举报</div>
                </div>
                <div class="Speak_content"><?php echo ($vo["content"]); ?></div>
                <div class="Speaks_time">
                    <div class="tz"><?php echo ($vo["date_value"]); ?></div>
                    <p class="">
                        <span class="tz">回复</span>
                        <span class="tz">0</span>
                    </p>
                </div>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div>
<div class="foodts"></div>
<div class="dow">
    <div class="dows">
        <img class="logos" src="/public/Img/60pt@2x.png" alt="" class="">
        <a href="<?php echo ($url); ?>" class="button">立即下载</a>
    </div>
</div>
<script>
    $(".tz").click(function(){
        console.log(1);
        window.location.href = "<?php echo ($url); ?>";
    })
</script>
</body>
</html>