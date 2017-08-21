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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Horder/index'),'text'=>'今日订单'], ] ]);?>
<div class="page-container">
	<div class="text-l">
		<form class="search" action="/system.php/Horder/index.html" method="get">
			<input name="p" value="" hidden>
			<select name="state"  class="select select-box inlin" id="state" style="width:120px">
				<option value="">请选择状态</option>
				<option value="1" <?php if( $_GET['state'] == 1): ?>selected<?php else: endif; ?>>待支付</option>
				<option value="2" <?php if( $_GET['state'] == 2): ?>selected<?php else: endif; ?>>待发货</option>
				<option value="3" <?php if( $_GET['state'] == 3): ?>selected<?php else: endif; ?>>待收获</option>
				<option value="4" <?php if( $_GET['state'] == 4): ?>selected<?php else: endif; ?>>待评价</option>
				<option value="5" <?php if( $_GET['state'] == 5): ?>selected<?php else: endif; ?>>已完成</option>
				<option value="6" <?php if( $_GET['state'] == 6): ?>selected<?php else: endif; ?>>已取消</option>
				<option value="7" <?php if( $_GET['state'] == 7): ?>selected<?php else: endif; ?>>退换货</option>
			</select>
			<input type="text" class="input-text" style="width:250px" name="order_no" value="<?php echo ($_GET['order_no']); ?>" id="order_no" placeholder="搜索订单号/下单会员/收件人">
			<button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
			<span style="float:right;padding:0px 10px 10px 0" >
                <a href="javascript:void(0)" title="导出Excl"  onclick="xiazai()" class="check_auth btn btn-default radius" >
					<i class="Hui-iconfont">&#xe644;</i>导出
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
				<th width="120">订单号</th>
				<th width="70">订单收件人</th>
				<th width="70">收件人电话</th>
				<th width="50">总金额</th>
				<th width="50">实付金额</th>
				<th width="50">成本价</th>
				<th width="50">下单会员</th>
				<th width="80">会员账号</th>
				<th width="60">订单状态</th>
				<th width="70">取货方式</th>
				<th width="120">下单时间</th>
				<th width="80">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
					<td><input type="checkbox" value="<?php echo ($vo["id"]); ?>" name="checkbox"></td>
					<td><?php echo ($vo["id"]); ?></td>
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
					<td class="td-manage">
						<a title="订单详情" class="check_lock" data="<?php echo ($vo["id"]); ?>" data-action="<?php echo U('Horder/order_view',array('id'=>$vo['id'], 'str'=>CONTROLLER_NAME.ACTION_NAME));?>"   style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
						<a title="订单删除" href="javascript:;" onclick="del(this,<?php echo ($vo["id"]); ?>);" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
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
			$.post("<?php echo U('Horder/del_order');?>", {ids:kid}, function(data){
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


	/*用户-删除*/
	function del(obj,id){
		layer.confirm('确认要删除吗？',function(index){
			$.post("<?php echo U('Horder/del_order');?>", {ids:id}, function(data){
				if( data.status == 'ok' ){
					$(obj).parents("tr").remove();
					layer.msg('已删除!',{icon:1,time:1000});
				}else{
					layer.msg(data.info,{icon:5,time:1000});
				}
			},'json');
		});
	}
	function xiazai(){
		var download = 'download';
		var url = "/system.php/Horder/index.html";
		if(url.split("?")[1]){
			window.location.href="/system.php/Horder/index.html&act="+download;
		}else{
			window.location.href="/system.php/Horder/index.html?act="+download;
		}
	}
	function getnums(){
		var num = $("#nus").val();
		window.location.href="/system.php/Horder/index.html&num="+num;
	}

	function lock(v) {
		$.post("<?php echo U('Horder/lock_order');?>", {id: v}, function (data) {
			console.log(data);
			if (data['status'] == 'ok') {
				alert(data.info);
				window.location.href = window.location.href;
			} else {
				alert(data.info);
			}
		}, 'json');
	}


</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>