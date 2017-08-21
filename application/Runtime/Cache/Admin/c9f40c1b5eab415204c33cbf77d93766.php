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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Goods/goods_list'),'text'=>'商品列表'], ] ]);?>
<div class="page-container">
	<div class="text-l">
		<form class="search"  method="get">
			<input type="text" class="input-text" style="width:250px" placeholder="搜索商品名称、编号" value="<?php echo ($_GET['username']); ?>" name="username">
			<select name="status"  class="select select-box inlin" style="width:120px" id="status">
				<option value="">商品状态</option>
				<option value="2" <?php if( $_GET['status'] == 2): ?>selected<?php else: endif; ?>>上架状态</option>
				<option value="1" <?php if( $_GET['status'] == 1): ?>selected<?php else: endif; ?>>下架状态</option>
			</select>
			<select name="first_category" id="first_category" onclick="change_category(this.value)" class="select select-box inlin" style="width:120px">
				<option value="">一级分类</option>
				<?php if(is_array($first_category)): foreach($first_category as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>" <?php if( $_GET['first_category'] == $v['id']): ?>selected<?php else: endif; ?>><?php echo ($v["category"]); ?></option><?php endforeach; endif; ?>
			</select>
			<select name="second_category" id="second_category" class="select select-box inlin" style="width:120px">
				<option value="">二级分类</option>
				<?php if(!empty($_GET['second_category'])): if(is_array($second_category)): foreach($second_category as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>" <?php if( $_GET['second_category'] == $v['id']): ?>selected<?php else: endif; ?>><?php echo ($v["category"]); ?></option><?php endforeach; endif; endif; ?>
			</select>
			<button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜商品</button>
			<span style="float:right;padding:0px 10px 10px 0" >
                <a href="javascript:void(0)"  data-action="<?php echo U('Goods/add_goods');?>" class="check_auth btn btn-primary radius" >
					<i class="Hui-iconfont">&#xe600;</i>添加商品
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
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="40">ID</th>
				<th width="100">图片</th>
				<th width="200">商品名称</th>
				<th width="70">编码</th>
				<th width="50">原价</th>
				<th width="50">售价</th>
				<th width="50">总销量</th>
				<th width="50">库存</th>
				<th width="60">排序</th>
				<th width="60">置顶</th>
				<th width="60">状态</th>
				<th width="100">创建时间</th>
				<th width="120">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
					<td><input type="checkbox" value="<?php echo ($vo["goods_id"]); ?>" name="checkbox"></td>
					<td><?php echo ($vo["goods_id"]); ?></td>
					<td><img src="<?php echo ($vo["img"]); ?>" style="width:50px; height:50px; border-radius:25px;"></td>
					<td><a href="<?php echo U('Goods/edit_goods',['id'=>$vo['goods_id']]);?>"><u style="cursor:pointer" class="text-primary" ><?php echo ($vo["name"]); ?></u></a></td>
					<td><?php echo ($vo['code']); ?></td>
					<td><?php echo ($vo['price']); ?></td>
					<td><?php echo ($vo['sale_price']); ?></td>
					<td><?php echo ($vo['sale_number']); ?></td>
					<td><?php echo ($vo['number']); ?></td>
					<td>
						<a href="javascript:void(0)" onclick="plus(<?php echo ($vo['goods_id']); ?>)" title="上移">
							<i class="Hui-iconfont">&#xe679;</i>
						</a>
						<a href="javascript:void(0)" onclick="minus(<?php echo ($vo['goods_id']); ?>)" title="下移">
							<i class="Hui-iconfont">&#xe674</i>
						</a>
					</td>
					<td>
						<a href="javascript:void(0)" style="display: block" onclick="go_top(<?php echo ($vo['goods_id']); ?>)" title="置顶"><i class="Hui-iconfont">&#xe699;</i></a>
						<a href="javascript:void(0)" style="display: block" onclick="go_after(<?php echo ($vo['goods_id']); ?>)" title="置后"><i class="Hui-iconfont">&#xe698;</i></a>
					</td>
					<td class="td-status">
						<?php if($vo['status'] == 1): ?><span class="label label-defaunt radius">已下架</span>
							<?php else: ?>
							<span class="label label-success radius">已发布</span><?php endif; ?>
					</td>
					<td><?php echo ($vo["intime"]); ?></td>
					<td class="td-manage">
						<?php if($vo['status'] == 2): ?><a style="text-decoration:none"  onClick="member_stop(this,'<?php echo ($vo["goods_id"]); ?>')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>
							<?php else: ?>
							<a style="text-decoration:none"  onClick="member_start(this,'<?php echo ($vo["goods_id"]); ?>')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a><?php endif; ?>
						<a title="型号库存" href="<?php echo U('Goods/kinds_param',['id'=>$vo['goods_id']]);?>"  class="ml-5" style="text-decoration:none">
							<i class="Hui-iconfont">&#xe623;</i>
						</a>
						<a title="编辑" href="<?php echo U('Goods/edit_goods',['id'=>$vo['goods_id']]);?>"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
						<a title="复制" href="javascript:void(0)" onclick="copy(<?php echo ($vo['goods_id']); ?>,this)"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6ea;</i></a>
						<a title="删除" href="javascript:;" onclick="del(this,'<?php echo ($vo["goods_id"]); ?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
		</table>
		<?php echo ($page); ?>
	</div>
</div>
<script type="text/javascript">
/*	$(document).ready(function(){
		$('.table-sort').dataTable({
			"aaSorting": [[ 1, "desc" ]],//默认第几个排序
			"bStateSave": true,//状态保存
			"aoColumnDefs": [
				{"orderable":false,"aTargets":[0,7]}// 制定列不参与排序
			]
		});
	});*/

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
			$.post("<?php echo U('Goods/del_goods');?>", {ids:kid}, function(data){
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
		console.log(1);
		layer.confirm('确认要下架吗？',function(index){
			$.post("<?php echo U('Goods/change_goods_status');?>",{id:id},function(data){
				if(data.info == 1){
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none"  onClick="member_start(this,'+id+')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>');
					$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已下架</span>');
					$(obj).remove();
					layer.msg('已下架!',{icon: 5,time:1000});
				}
			},'json')
		});
	}

	/*用户-启用*/
	function member_start(obj,id){
		layer.confirm('确认要上架吗？',function(index){
			$.post("<?php echo U('Goods/change_goods_status');?>",{id:id},function(data){
				if(data.info == 2){
					$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="ml-5" onClick="member_stop(this,'+id+')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
					$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
					$(obj).remove();
					layer.msg('已发布!',{icon: 6,time:1000});
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
	layer.confirm('确认要删除吗？',function(index){
		$.post("<?php echo U('Goods/del_goods');?>", {ids:id}, function(data){
			if( data.status == 'ok' ){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}else{
				layer.msg(data.info,{icon:5,time:1000});
			}
		},'json');
	});
}

	/*商品-复制*/
function copy(id,v){
	var id = id;
	$.post("<?php echo U('Goods/copy_goods');?>",{id:id},function(data){
		if(data['status'] == 'ok'){
			layer.msg('复制成功',{icon:1,time:1000});
			window.location.href = window.location.href;
		}else{
			alert(data['info']);
		}
	},'json');
	return false;
}

function plus(v){
	var name = "<?php echo ($_GET['name']); ?>";
	var status = "<?php echo ($_GET['status']); ?>";
	var first_category = "<?php echo ($_GET['first_category']); ?>";
	var second_category = "<?php echo ($_GET['second_category']); ?>";
	$.post("<?php echo U('Goods/plus_goods_sort');?>",{goods_id:v,status:status,first_category:first_category,second_category:second_category},function(data){
		if(data['status'] == 'ok'){
			layer.msg(data.info,{icon:1,time:1000})
			window.location.href = window.location.href;
		}else{
			layer.msg(data.info,{icon:5,time:1000})
		}
	},'json');
}

function minus(v){
	var name = "<?php echo ($_GET['name']); ?>";
	var status = "<?php echo ($_GET['status']); ?>";
	var first_category = "<?php echo ($_GET['first_category']); ?>";
	var second_category = "<?php echo ($_GET['second_category']); ?>";
	$.post("<?php echo U('Goods/minus_goods_sort');?>",{goods_id:v,name:name,status:status,first_category:first_category,second_category:second_category},function(data){
		if(data['status'] == 'ok'){
			layer.msg(data.info,{icon:1,time:1000})
			window.location.href = window.location.href;
		}else{
			layer.msg(data.info,{icon:5,time:1000})
		}
	},'json');
}

function go_top(v){
	$.post("<?php echo U('Goods/top');?>",{goods_id:v},function(data){
		if(data['status'] == 'ok'){
			layer.msg(data.info,{icon:1,time:1000})
			window.location.href = window.location.href;
		}else{
			layer.msg(data.info,{icon:5,time:1000})
		}
	},'json');
};

function go_after(v){
	$.post("<?php echo U('Goods/after');?>",{goods_id:v},function(data){
		if(data['status'] == 'ok'){
			layer.msg(data.info,{icon:1,time:1000})
			window.location.href = window.location.href;
		}else{
			layer.msg(data.info,{icon:5,time:1000})
		}
	},'json');
};
function change_category(e){
	if(!e || e==''){
		return false;
	}
	var url = "<?php echo U('Goods/get_son_category');?>";
	$.post(url,{first:e},function(data){
		$("#second_category").html(data);
	})
};
</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>