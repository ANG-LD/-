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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Auth/index'),'text'=>'权限管理'], ['href'=>'','text'=>'规则分组'] ] ]);?>
<div class="page-container">
    <div class="cl pd-5 bg-1 bk-gray">
        <span class="l">
            <a href="javascript:;" onclick="datadel()" class="btn btn-danger radius">
                <i class="Hui-iconfont">&#xe6e2;</i> 批量删除
            </a>
            <a class="btn btn-primary radius"  href="<?php echo U('Auth/editAuthGroup');?>">
                <i class="Hui-iconfont">&#xe600;</i> 添加分组
            </a>
        </span>
        <span class="r">共有数据：<strong><?php echo ($count); ?></strong> 条</span>
    </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-bg table-hover table-sort">
            <thead>
            <tr class="text-c">
                <th width="40"><input name="" type="checkbox" value=""></th>
                <th class="center">ID</th>
                <th>名称</th>
                <th>状态</th>
                <th>规则列表</th>
                <th>操作 /
                    <a class=""  href="<?php echo U('Auth/editAuthGroup');?>" thtle="添加节点">
                    <i class="Hui-iconfont">&#xe600;</i>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr class="text-c">
                <td><input name="checkbox" type="checkbox" value="<?php echo ($v["id"]); ?>"></td>
                <td><?php echo ($v["id"]); ?></td>
                <td><?php echo ($v["title"]); ?></td>
                <td><?php echo ($v['status']?'开启':'关闭'); ?></td>
                <td>
                    <u><a onclick="category_edit('','<?php echo U('Auth/editGroupRule',['id'=>$v['id'],'title'=>$v['title']]);?>','4','','510')" href="javascript:;;">查看权限</a></u>
                </td>
                <td class="td-manage">
                    <a style="text-decoration:none" class="ml-5"  href="<?php echo U('Auth/editAuthGroup',['id'=>$v['id']]);?>" title="编辑分组">
                        <i class="Hui-iconfont">&#xe6df;</i>
                    </a>
                    <a style="text-decoration:none" class="ml-5" onClick="picture_del(this,<?php echo ($v["id"]); ?>)" href="javascript:;" title="删除分组">
                        <i class="Hui-iconfont">&#xe6e2;</i>
                    </a>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <?php echo ($page); ?>
    </div>
</div>
<script type="text/javascript">
    function getChecked() {
        var gids = new Array();
        $.each($('input[name="checkbox"]:checked'), function(i, n){
            gids.push( $(n).val() );
        });
        return gids;
    }
    function datadel(kid){
        kid = kid ? kid : getChecked();
        kid = kid.toString();
        if(kid == ''){
            layer.msg('你没有选择任何选项！', {offset: 95,shift: 6});
            return false;
        }
        layer.confirm('确认要删除吗？',function(index){
        $.post("<?php echo U('Auth/del_group');?>", {ids:kid}, function(data){
            if( data.status == 'ok' ){
                layer.msg(data.info,{icon:1,time:1000});
                window.location.href = data.url;
            }else{
                layer.msg(data.info,{icon:1,time:1000});
            }
        },'json');
    })
    }
    /*图片-添加*/
    function picture_add(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*图片-查看*/
    function picture_show(title,url,id){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*图片-编辑*/
    function picture_edit(title,url,id){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*图片-删除*/
    function picture_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.post("<?php echo U('Auth/del_group');?>", {ids:id}, function(data){
                if( data.status == 'ok' ){
                    $(obj).parents("tr").remove();
                    layer.msg('已删除!',{icon:1,time:1000});
                }else{
                    layer.msg(data.info,{icon:1,time:1000});
                }
        },'json');
        });
    }
    function category_edit(title,url,id,w,h){
        layer_show(title,url,w,h);
    }
</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>