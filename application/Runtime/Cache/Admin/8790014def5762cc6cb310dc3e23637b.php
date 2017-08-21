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
	<link rel="stylesheet" type="text/css" href="/lib/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/skin/default/skin.css" id="skin" />
	<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/style.css" />
	<link rel="stylesheet" type="text/css" href="/assets/css/base.css" />
	<link rel="stylesheet" href="/assets/js/asyncbox/skins/default.css" />
	<link rel="stylesheet" type="text/css" href="/assets/js/bootstrap-datepicker/css/bootstrap-datetimepicker.css"/>
	<script src="https://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

	<!--[if IE 6]>
	<script>DD_belatedPNG.fix('*');</script>
	<![endif]-->
	<title><?php echo ($system['title']); ?>-后台管理系统</title>
</head>
<body>
<?php echo W('Public/head');?>
<?php echo W('Public/menu');?>
<div class="ml170" id="page-content" style="overflow:auto;min-height:100%">
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Home/xieyi'),'text'=>'相关协议'], ] ]);?>
<div class="page-container">
    <!--    <div class="text-c">
            <input type="text" name="" id="" placeholder=" 图片名称" style="width:250px" class="input-text">
            <button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> 搜图片</button>
        </div>-->
    <div class="cl pd-5 bg-1 bk-gray">
        <span class="r">共有数据：<strong><?php echo ($count); ?></strong> 条</span>
    </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-bg table-hover table-sort">
            <thead>
            <tr class="text-c">
                <th width="40"><input name="" type="checkbox" value=""></th>
                <th width="40">ID</th>
                <th width="150">名称</th>
                <!--<th width="150">跳转值</th>-->
                <th width="150">更新时间</th>
                <th width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
                    <td><input name="checkbox" type="checkbox" value="<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td><a href="<?php echo U('Home/edit_xieyi',['id'=>$vo['id']]);?>"><u><?php echo ($vo["title"]); ?></u></a></td>
                    <td><?php !empty($vo['uptime']) ? print $vo['uptime'] : print $vo['intime'] ?></td>
                    <td class="td-manage">
                        <a style="text-decoration:none" class="ml-5"  href="<?php echo U('Home/edit_xieyi',['id'=>$vo['id']]);?>" title="编辑">
                            <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <?php echo ($page); ?>
    </div>
</div>
<!--<script src="/assets/js/ZeroClipboard.js"></script>-->
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
        if(!confirm('删除后无法恢复，确定删除？'))
            return false;
        $.post("<?php echo U('Home/del_xieyi');?>", {ids:kid}, function(data){
            if( data.status == 'ok' ){
                popup.success(data.info);
                setTimeout(function(){
                    popup.close("asyncbox_success");
                },2000);
                window.location.href = data.url;
            }else{
                popup.error(data.info);
                setTimeout(function(){
                    popup.close("asyncbox_error");
                },2000);
            }
        },'json');
    }
    function getChecked() {
        var gids = new Array();
        $.each($('input:checked'), function(i, n){
            gids.push( $(n).val() );
        });
        return gids;
    }

    function send_notice(id,v){
        var url = $(v).attr('data-action');
        var id = id;
        $.post(url,{id:id},function(data){
            console.log(data);
            if( data.status == 'ok' ){
                popup.success(data.info);
                setTimeout(function(){
                    popup.close("asyncbox_success");
                },2000);
                window.location.href = window.location.href;
            }else{
                popup.error(data.info);
                setTimeout(function(){
                    popup.close("asyncbox_error");
                },2000);
            }
        },'json');
        return false;
    }

    function change_istop(id,v){
        var id = id;
        $.post("<?php echo U('Home/change_notice_top');?>",{id:id},function(data){
            if(data['status'] == 'ok'){
                $(v).html(data['info']);
            }else{
                alert(data['info']);
            }
        },'json');
        return false;
    }

    function change_state(id,v){
        var id = id;
        $.post("<?php echo U('Home/change_notice_state');?>",{id:id},function(data){
            if(data['status'] == 'ok'){
                $(v).html(data['info']);
            }else{
                alert(data['info']);
            }
        },'json');
        return false;
    }
    function send(id,v){
        var id = id;
        $.post("<?php echo U('Home/send_notice');?>",{id:id},function(data){
            if(data['status'] == 'ok'){
                $(v).html(data['info']);
            }else{
                alert(data['info']);
            }
        },'json');
        return false;
    }

    function copy(v)
    {
        var Url2=document.getElementById("copy"+v);
        Url2.select(); // 选择对象
        document.execCommand("Copy");  // 执行浏览器复制命令
    }

</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>