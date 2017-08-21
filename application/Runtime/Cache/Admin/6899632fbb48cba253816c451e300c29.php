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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('User/index'),'text'=>'普通用户'], ] ]);?>
<div class="page-container">
	<div class="text-l">
		<form class="search"  method="get">
		<input type="text" class="input-text" style="width:250px" placeholder="输入会员名称、电话、邮箱" value="<?php echo ($_GET['username']); ?>" name="username">
		<button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
		</form>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20">
		<span class="l">
			<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius">
				<i class="Hui-iconfont">&#xe6e2;</i> 批量删除
			</a>
			<a href="<?php echo U('User/add_user');?>"  class="btn btn-primary radius">
				<i class="Hui-iconfont">&#xe600;</i> 添加普通用户
			</a>
		</span>
		<span class="r">共有数据：<strong><?php echo ($count); ?></strong> 条</span> </div>
	<div class="mt-20">
	<table class="table table-border table-bordered table-hover table-bg table-sort">
		<thead>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="80">ID</th>
				<th width="100">头像</th>
				<th width="100">昵称</th>
				<th width="40">性别</th>
				<th width="90">手机</th>
				<th width="150">别名</th>
				<th width="">地址</th>
				<th width="130">加入时间</th>
				<th width="70">状态</th>
				<th width="100">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
				<td><input type="checkbox" value="1" name="checkbox"></td>
				<td><?php echo ($vo["user_id"]); ?></td>
				<td><img src="<?php echo ($vo["img"]); ?>" style="width:50px; height:50px; border-radius:25px;"></td>
				<td><u style="cursor:pointer" class="text-primary" onclick="member_show('<?php echo ($vo["username"]); ?>','<?php echo U('User/user_show');?>','<?php echo ($vo["user_id"]); ?>','360','400')"><?php echo ($vo["username"]); ?></u></td>
				<td>
					<?php $vo['sex']==1 && print "男"; $vo['sex']==2 && print "女"; $vo['sex']==3 && print "保密"; ?>
				</td>
				<td><?php echo ($vo["phone"]); ?></td>
				<td><?php echo ($vo["alias"]); ?></td>
				<td class="text-l"><?php echo ($vo["province"]); echo ($vo["city"]); echo ($vo["area"]); echo ($vo["address"]); ?></td>
				<td><?php echo (date("y-m-d H:i:S",$vo["intime"])); ?></td>
				<td class="td-status">
					<?php if($vo['is_stop'] == 2): ?><span class="label label-success radius">已启用</span>
					<?php else: ?>
					<span class="label label-defaunt radius">已停用</span><?php endif; ?>
				</td>
				<td class="td-manage">
					<?php if($vo['is_stop'] == 2): ?><a style="text-decoration:none" onClick="member_stop(this,'<?php echo ($vo["user_id"]); ?>')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
					<?php else: ?>
					<a style="text-decoration:none" onClick="member_start(this,'<?php echo ($vo["user_id"]); ?>')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a><?php endif; ?>
					<a title="编辑" href="<?php echo U('User/edit_user',['uid'=>$vo['user_id']]);?>"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
					<a style="text-decoration:none" class="ml-5"  href="<?php echo U('User/change_account'),['uid'=>$vo['user_id']];?>" title="账户安全"><i class="Hui-iconfont">&#xe63f;</i></a>
					<a title="删除" href="javascript:;" onclick="del(this,'<?php echo ($vo["user_id"]); ?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
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
		console.log(kid);
		if(kid == ''){
			layer.msg('你没有选择任何选项！', {offset: 95,shift: 6});
			return false;
		}
		layer.confirm('确认要删除吗？',function(index){
			$.post("<?php echo U('User/del_user');?>", {ids:kid}, function(data){
				if( data.status == 'ok' ){
					layer.msg(data.info,{icon:1,time:1000});
					window.location.href = data.url;
				}else{
					layer.msg(data.info,{icon:1,time:1000});
				}
			},'json');
		})
	}
	/*用户-添加*/
	function member_add(title,url,w,h){
		layer_show(title,url,w,h);
	}
	/*用户-查看*/
	function member_show(title,url,id,w,h){
		layer_show(title,url,w,h);
	}
	/*用户-停用*/
	function member_stop(obj,id){
		layer.confirm('确认要停用吗？',function(index){
			$.post("<?php echo U('User/change_stop_status');?>",{id:id},function(data){
				if(data.info == 1){
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,'+id+')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
					$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
					$(obj).remove();
					layer.msg('已停用!',{icon: 5,time:1000});
				}
			},'json')
		});
	}

	/*用户-启用*/
	function member_start(obj,id){
		layer.confirm('确认要启用吗？',function(index){
			$.post("<?php echo U('User/change_stop_status');?>",{id:id},function(data){
				if(data.info == 2){
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,'+id+')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>');
					$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
					$(obj).remove();
					layer.msg('已启用!',{icon: 6,time:1000});
				}
			},'json');

		});
	}
	/*用户-编辑*/
	function member_edit(title,url,id,w,h){
		layer_show(title,url,w,h);
	}
	/*密码-修改*/
	function change_password(title,url,id,w,h){
		layer_show(title,url,w,h);
	}
	/*用户-删除*/
	function del(obj,id){
		function picture_del(obj,id){
			layer.confirm('确认要删除吗？',function(index){
				$.post("<?php echo U('Home/del_carousel');?>", {ids:kid}, function(data){
					if( data.status == 'ok' ){
						$(obj).parents("tr").remove();
						layer.msg('已删除!',{icon:1,time:1000});
					}else{
						layer.msg(data.info,{icon:1,time:1000});
					}
				});
			},'json');
		}
	}
</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>