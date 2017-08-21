<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<meta http-equiv="Cache-Control" content="no-siteapp" />
	<LINK rel="Bookmark" href="/favicon.ico" >
	<LINK rel="Shortcut Icon" href="/favicon.ico" />
	<!--[if lt IE 9]>
	<script type="text/javascript" src="/lib/html5.js"></script>
	<script type="text/javascript" src="/lib/respond.min.js"></script>
	<script type="text/javascript" src="/lib/PIE_IE678.js"></script>
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="/static/h-ui/css/H-ui.min.css" />
	<link rel="stylesheet" type="text/css" href="/Public/admin/css/base.css" />
	<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/H-ui.admin.css" />
	<link rel="stylesheet" type="text/css" href="/lib/Hui-iconfont/1.0.7/iconfont.css" />
	<link rel="stylesheet" type="text/css" href="/lib/icheck/icheck.css" />
	<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/skin/default/skin.css" id="skin" />
	<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/style.css" />
	<link rel="stylesheet" type="text/css" href="/assets/css/base.css" />
	<link rel="stylesheet" href="/assets/js/asyncbox/skins/default.css" />
	<link rel="stylesheet" type="text/css" href="/assets/js/bootstrap-datepicker/css/bootstrap-datetimepicker.css"/>
	<script src="https://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src="/assets/js/bootstrap-datepicker/bootstrap-datetimepicker.js"></script>
	<script src="/assets/js/bootstrap-datepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>

	<!--[if IE 6]>
	<script>DD_belatedPNG.fix('*');</script>
	<![endif]-->
	<title><?php echo ($system['title']); ?>-后台管理系统</title>
</head>
<body>
<?php echo W('Public/head');?>
<?php echo W('Public/menu');?>
<div class="ml200" id="page-content">
	<?php echo W('public/breadcrumbs',[ [ ['href'=>'','text'=>'已完成订单'] ] ]);?>
</if>
<!--<script type="text/javascript" src="/public/admin/js/jquery-1.7.min.js"></script>-->
<script type="text/javascript" src="/public/admin/layer/layer.js"></script>
<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <form class="search" action="/system.php/Horder/complete.html" method="get">
                <input name="m" value="<?php echo MODULE_NAME;?>" hidden>
                <input name="c" value="<?php echo CONTROLLER_NAME;?>" hidden>
                <input name="a" value="<?php echo ACTION_NAME;?>" hidden>
                <input type="text" name="order_no" value="<?php echo ($_GET['order_no']); ?>" id="order_no" placeholder="搜索订单号/下单会员/收件人" size="25" style="margin-right: 15px;">
                <input type="text" name="start_time" value="<?php echo ($_GET['start_time']); ?>" id="start_time" placeholder="开始时间" size="25" style="margin-right: 15px;">
                <input type="text" name="end_time" value="<?php echo ($_GET['end_time']); ?>" id="end_time" placeholder="结束时间" size="25" style="margin-right: 15px;">
                <input type="submit"  class="btn" value="搜索">

                <span style="float:right;padding-right:30px;padding-top:10px">
		           <a href="javascript:;" style="text-decoration:none;color:#000000"><span style="padding:10px 10px 10px 10px;background-color:#DDDDDD;" onclick="xiazai()">导出excl</span></a>
		        </span>
            </form>
            <table width="100%" class="tab" style="margin-top:10px;">
                <thead>
                <tr>
                    <td><a href="javascript:;" id="selall">全选</a></td>
                    <td>订单号</td>
                    <td>订单收件人</td>
                    <td>收件人电话</td>
                    <td>订单总金额</td>
                    <td>实付金额</td>
                    <td>成本价</td>
                    <td>下单会员</td>
                    <td>会员账号</td>
                    <td>订单状态</td>
                    <td>取货方式</td>
                    <td>下单时间</td>
                    <td width="200">操作</td>
                </tr>
                </thead>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr align="center" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#FFFFBB';">
                        <td align="center"><input type="checkbox" name="ids" id="ids"  value="<?php echo ($vo["id"]); ?>"/>
                            <?php if($_GET['num'])$num=$_GET['num'];else $num ='10'; if($_GET['p'] > 0){ echo $key+1+($_GET['p']-1)*$num; }else{ echo $key+1; } ?>
                        </td>
                        <td><?php echo ($vo["order_no"]); ?></td>
                        <td><?php echo ($vo["name"]); ?></td>
                        <td><?php echo ($vo["phone"]); ?></td>
                        <td><?php echo ($vo['amount']); ?></td>
                        <td><?php echo ($vo['paid']); ?></td>
                        <td><?php echo ($vo['cost']); ?></td>
                        <td><?php echo ($vo['nickname']); ?></td>
                        <td><?php echo ($vo['m_phone']); ?></td>
                        <td>
                            <?php $vo['state'] == '1' && print "待支付"; $vo['state'] == '2' && print "待发货"; $vo['state'] == '3' && print "待收货"; $vo['state'] == '4' && print "待评价"; $vo['state'] == '5' && print "已完成"; $vo['state'] == '6' && print "已取消"; $vo['state'] == '7' && print "退换货"; ?>
                        </td>
                        <td>
                            <?php $re['is_take'] == '1' ? print "景点自取" : print "快递寄送"; ?>
                        </td>
                        <td><?php echo ($vo["intime"]); ?></td>
                        <td>
                            [ <a href='javascript:;' class="check_lock" data="<?php echo ($vo["id"]); ?>" data-action="<?php echo U('Horder/order_view',array('id'=>$vo['id'], 'str'=>CONTROLLER_NAME.'/'.ACTION_NAME));?>">订单详情</a> ]
                            [ <a href="javascript:;"  onclick="del(<?php echo ($vo["id"]); ?>);">删除</a> ]
                            [ <a href="<?php echo ($vo['down_url']); ?>"  title="下载pdf生产文件">下载pdf <i class="glyphicon glyphicon-download-alt"></i></a> ]

                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <!--<tr><td colspan="7" align="right"><?php echo ($page); ?></td></td></tr>-->
            </table>
            <?php echo ($page); ?>

        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $("#selall").click(function() {
            $(this).toggleClass("xuan");
            if($(this).is(".xuan")){
                $("input[name='ids']").each(function(){
                    $(this).prop("checked", true);
                })
            }else{
                $("input[name='ids']").each(function(){
                    $(this).prop("checked", false);
                })
            }
        });

    });

    function del(kid){

        kid = kid ? kid : getChecked();
        kid = kid.toString();
        if(kid == ''){
            layer.msg('你没有选择任何选项！', {offset: 95,shift: 6});
            return false;
        }
        if(!confirm('确定要删除该记录？'))
            return false;
        $.post("<?php echo U('Horder/del_order');?>", {ids:kid}, function(data){
            if( data['status'] == 'ok' ){
                alert(data.info);
                window.location.href = data.url;
            }else{
                alert(data.info);
            }
        },'json');
        return false;
    }
    function getChecked() {
        var gids = new Array();
        $.each($('input:checked'), function(i, n){
            gids.push( $(n).val() );
        });
        return gids;
    }

    function xiazai(){
        var download = 'download';
        window.location.href="/system.php/Horder/complete.html&act="+download;
    }
    function getnums(){
        var num = $("#nus").val();
        window.location.href="/system.php/Horder/complete.html&num="+num;
    }

    function lock(v){
        console.log(1);
        $.post("<?php echo U('Horder/lock_order');?>",{id:v},function(data){
            console.log(data);
            if(data['status'] == 'ok'){
                alert(data.info);
                window.location.href = window.location.href;
            }else{
                alert(data.info);
            }
        },'json');
    }

    $("#start_time").datetimepicker({
        format: 'yyyy-mm-dd',
        weekStart: 1,
        autoclose: true,
        todayBtn: 'linked',
        language: 'zh-CN',
        minView: 2
    }).on('changeDate', function(ev) {
        $("#end_time").datetimepicker('setStartDate', $(this).val());
    });

    $("#end_time").datetimepicker({
        format: 'yyyy-mm-dd',
        weekStart: 1,
        autoclose: true,
        todayBtn: 'linked',
        language: 'zh-CN',
        minView: 2
    });
</script>

</div>
</body>
<?php echo W('Public/foot');?>
</html>