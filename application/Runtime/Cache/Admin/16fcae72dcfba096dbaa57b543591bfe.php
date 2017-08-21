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
	<!--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">-->
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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Event/index'),'text'=>'转盘奖品'], ] ]);?>
</if>
<script type="text/javascript" src="/public/admin/js/jquery-1.7.min.js"></script>
<script type="text/javascript" src="/public/admin/layer/layer.js"></script>
<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <form class="search" action="/system.php/Event/index.html" method="get">
                <input name="m" value="<?php echo MODULE_NAME;?>" hidden>
                <input name="c" value="<?php echo CONTROLLER_NAME;?>" hidden>
                <input name="a" value="<?php echo ACTION_NAME;?>" hidden>
                <input type="text" name="go_city" value="<?php echo ($_GET['go_city']); ?>" id="go_city" placeholder="搜索奖品名称" size="30" style="margin-right: 20px;">
                类别：<select name="status" id="status">
                <option value="">请选择状态</option>
                <option value="1" <?php if( $_GET['status'] == 1): ?>selected<?php else: endif; ?>>实物</option>
                <option value="2" <?php if( $_GET['status'] == 2): ?>selected<?php else: endif; ?>>积分</option>
                <option value="3" <?php if( $_GET['status'] == 2): ?>selected<?php else: endif; ?>>无奖励</option>
                </select>
                <input type="submit"  class="btn" value="搜索">
                
                <span style="float:right;padding-right:30px;padding-top:10px">
		           <a href="<?php echo U('Event/add_prize');?>"  style="text-decoration:none;color:#000000">
                       <span style="padding:10px 10px 10px 10px;background-color:#DDDDDD;">添加奖品</span>
                   </a>
		        </span>
            </form>

            <table width="100%" class="tab" style="margin-top:10px;">
                <thead>
                <tr><td colspan="12" align="left">
                <span style="float:left;padding-right:30px;padding-top:8px;padding-bottom:18px">
	              <a href="javascript:void(0);" style="text-decoration:none;color:#000000">
                      <span onclick="del();" style="padding:10px 10px 10px 10px;background-color:#DDDDDD;">删除</span></a>
	            </span>
                <span style="float:left;padding-top:8px;">每页显示
				<select id="nus" onchange="getnums();">
                    <?php if(is_array($nums)): $i = 0; $__LIST__ = $nums;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><option value="<?php echo ($l); ?>" <?php if( $l == $nus ): ?>selected<?php else: endif; ?>><?php echo ($l); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
				条</span>
                </td></td>
                </tr>
                <tr>
                    <td><a href="javascript:;" id="selall">全选</a></td>
                    <td>奖品名称</td>
                    <td></td>
                    <td>类型</td>
                    <td>抽中概率</td>
                    <td width="190">操作</td>
                </tr>
                </thead>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr align="center" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#FFFFBB';">
                        <td align="center"><input type="checkbox" name="ids" id="ids"  value="<?php echo ($vo["prize_id"]); ?>"/>
                            <?php if($_GET['num'])$num=$_GET['num'];else $num ='10'; if($_GET['p'] > 0){ echo $key+1+($_GET['p']-1)*$num; }else{ echo $key+1; } ?>
                        </td>
                        <td><?php echo ($vo["name"]); ?></td>
                        <td><img src="<?php echo ($vo["img"]); ?>" style="width:50px; height:50px; border-radius:25px;"></td>
                        <td><?php $vo['type'] == 1 && print "实物"; $vo['type'] == 2 && print "积分"; $vo['type'] == 3 && print "无奖励"; ?></td>
                        <td>
                            <?php echo sprintf("%.2f",$vo['chance']/$count_chance)*100; ?>%
                        </td>
                        <!--<td><?php echo ($vo["intime"]); ?></td>-->
                        <td>
                            [ <a href="<?php echo U('Event/edit_prize',array('id'=>$vo['prize_id'], 'str'=>CONTROLLER_NAME.'/'.ACTION_NAME));?>">编辑</a> ]
                            [ <a href="javascript:;"  onclick="del(<?php echo ($vo["prize_id"]); ?>);">删除</a> ]

                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <!--<tr><td colspan="7" align="right"><?php echo ($page); ?></td></td></tr>-->
            </table>
            <?php echo ($page); ?>
        </div>
    </div>
</div>
<!--<script src="/public/admin/js/common.js"></script>-->
<script type="text/javascript">
    $(function() {
        $("#selall").toggle(function() {
            $("input[name='ids']").each(function() {
                $(this).attr("checked", true);
            });
        },function(){
            $("input[name='ids']").each(function() {
                $(this).attr("checked", false);
            });
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
        $.post("<?php echo U('Event/del_prize');?>", {ids:kid}, function(data){
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
        window.location.href="/system.php/Event/index.html&act="+download;
    }
    function getnums(){
        var num = $("#nus").val();
        window.location.href="/system.php/Event/index.html&num="+num;
    }
    function change_status(id,v){
        var id = id;
        $.post("<?php echo U('Flight/change_ticket_status');?>",{id:id},function(data){
            if(data['status'] == 'ok'){
                $(v).html(data['info']);
            }else{
                alert(data['info']);
            }
        },'json');
        return false;
    }

    function change_tuijian(id,v){
        var id = id;
        $.post("<?php echo U('Flight/change_ticket_istuijian');?>",{id:id},function(data){
            if(data['status'] == 'ok'){
                $(v).html(data['info']);
            }else{
                alert(data['info']);
            }
        },'json');
        return false;
    }

    //$('.check_auth').click(function(){
    //    var url = $(this).attr('data-action');
    //    console.log(url+'&chek=1');
    //    $.get(url+'&chek=1',function(data){
    //
    //    })
    //})
</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>