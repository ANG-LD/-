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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Goods/index'),'text'=>'商品分类'] ] ]);?>
<div class="page-container">
    <div class="text-l">
        <form class="search" action="/system.php/Goods/index.html"  method="get">
            <input type="text" class="input-text" style="width:250px" placeholder="搜索分类名称" value="<?php echo ($_GET['title']); ?>" name="title">
            <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
            <span style="float:right;padding:0px 30px 20px 0" >
                <a href="javascript:;;" class="btn btn-primary radius" onclick="category_edit('添加分类（First）','<?php echo U('Goods/edit_first_category');?>','4','','510')" >
                    <i class="Hui-iconfont">&#xe600;</i>添加分类名
                </a>
            </span>
        </form>
    </div>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
		<span class="l">
			<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius">
                <i class="Hui-iconfont">&#xe6e2;</i> 批量删除
            </a>
		</span>
        <span class="r">共有数据：<strong><?php echo ((isset($count) && ($count !== ""))?($count):0); ?></strong> 条</span> </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-bg table-hover table-sort">
            <thead>
            <tr class="text-c">
                <th width="40"><input name="" type="checkbox" value=""></th>
                <th width="40">ID</th>
                <th width="150">名称</th>
                <th width="100">图片</th>
                <!--<th width="150">跳转值</th>-->
                <th width="60">发布状态</th>
                <th width="150">更新时间</th>
                <th width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
                <td><input name="checkbox" type="checkbox" value="<?php echo ($vo["id"]); ?>"></td>
                <td><?php echo ($vo["id"]); ?></td>
                <td><a href="javascript:;;" onclick="category_edit('添加分类（First）','<?php echo U('Goods/edit_first_category');?>','4','','510')"><u><?php echo ($vo["category"]); ?></u></a></td>
                <td><a href="javascript:;"><img width="100" class="picture-thumb" src="<?php echo ($vo["picture"]); ?>"></a></td>
                <td class="td-status">
                    <?php if($vo['status'] == 1): ?><span class="label label-defaunt radius">已下架</span>
                        <?php else: ?>
                        <span class="label label-success radius">已发布</span><?php endif; ?>
                    </span></td>
                <td><?php echo ($vo["intime"]); ?></td>
                <td class="td-manage">
                    <?php if($vo['status'] == 1): ?><a style="text-decoration:none" onClick="picture_start(this,<?php echo ($vo["id"]); ?>)" href="javascript:;" title="发布">
                        <i class="Hui-iconfont">&#xe603;</i>
                    </a>
                    <?php else: ?>
                    <a style="text-decoration:none" onClick="picture_stop(this,<?php echo ($vo["id"]); ?>)" href="javascript:;" title="下架">
                        <i class="Hui-iconfont">&#xe6de;</i>
                    </a><?php endif; ?>
                    <a style="text-decoration:none" class="ml-5"  href="<?php echo U('Goods/second',['cate_id'=>$vo['id']]);?>" title="下级分类">
                        <i class="Hui-iconfont">&#xe681;</i>
                    </a>
                    <a style="text-decoration:none" class="ml-5"  href="javascript:;;" onclick="category_edit('编辑分类（First）','<?php echo U('Goods/edit_first_category',['id'=>$vo['id']]);?>','4','','510')" title="编辑">
                        <i class="Hui-iconfont">&#xe6df;</i>
                    </a>
                    <a style="text-decoration:none" class="ml-5" onClick="del(this,<?php echo ($vo["id"]); ?>)" href="javascript:;" title="删除">
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
        $.post("<?php echo U('Goods/del_first_category');?>", {ids:kid}, function(data){
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
    /*图片-审核*/
    function picture_shenhe(obj,id){
        layer.confirm('审核文章？', {
                    btn: ['通过','不通过'],
                    shade: false
                },
                function(){
                    $(obj).parents("tr").find(".td-manage").prepend('<a class="c-primary" onClick="picture_start(this,id)" href="javascript:;" title="申请上线">申请上线</a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
                    $(obj).remove();
                    layer.msg('已发布', {icon:6,time:1000});
                },
                function(){
                    $(obj).parents("tr").find(".td-manage").prepend('<a class="c-primary" onClick="picture_shenqing(this,id)" href="javascript:;" title="申请上线">申请上线</a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-danger radius">未通过</span>');
                    $(obj).remove();
                    layer.msg('未通过', {icon:5,time:1000});
                });
    }
    /*图片-下架*/
    function picture_stop(obj,id){
        layer.confirm('确认要下架吗？',function(index){
            $.post("<?php echo U('Goods/change_category_status');?>",{id:id},function(data){
                console.log(data);
                if(data.info==1){
                    $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_start(this,'+id+')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已下架</span>');
                    $(obj).remove();
                    layer.msg('已下架!',{icon: 5,time:1000});
                }
            },'json')
        });
    }

    /*图片-发布*/
    function picture_start(obj,id){
        layer.confirm('确认要发布吗？',function(index){
            $.post("<?php echo U('Goods/change_category_status');?>",{id:id},function(data){
                console.log(data);
                if(data.info==2){
                    $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_stop(this,'+id+')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
                    $(obj).remove();
                    layer.msg('已发布!',{icon: 6,time:1000});
                }
            },'json')
        });
    }
    /*图片-申请上线*/
    function picture_shenqing(obj,id){
        $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">待审核</span>');
        $(obj).parents("tr").find(".td-manage").html("");
        layer.msg('已提交申请，耐心等待审核!', {icon: 1,time:2000});
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
    function del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.post("<?php echo U('Goods/del_first_category');?>", {ids:id}, function(data){
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