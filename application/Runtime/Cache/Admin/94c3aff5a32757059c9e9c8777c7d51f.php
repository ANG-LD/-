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
	<?php if($_GET['id'] == ''): echo W('public/breadcrumbs',[ [ ['href'=>U('Auth/authGroup'),'text'=>'规则分组'], ['href'=>'','text'=>'添加分组规则'] ] ]);?>
<?php else: ?>
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Auth/authGroup'),'text'=>'规则分组'], ['href'=>'','text'=>'添加分组规则'] ] ]); endif; ?>
<div class="page-content">
	<div class="row">
		<div class="col-xs-12">
			<form  class="form-horizontal ajax-form" role="form" dit-action="<?php echo U('Auth/editAuthGroup');?>">
				<div class="space-10"></div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="title">分组规则名称:</label>
					<div class="col-sm-9">
						<input type="text" id="title" name="title" placeholder="分组名称" class="col-xs-10 col-sm-5" value="<?php echo ($d['title']); ?>">
						<span class="help-inline col-xs-12 col-sm-7">
							<strong class="red">*</strong>
							<span class="middle"></span>
						</span>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-3 control-label" for="status">状态:</label>
					<div class="col-sm-9">
						<select class="col-xs-10 col-sm-5" name="status">
							<option value="1" <?php if($d['status'] == '1'): ?>selected<?php endif; ?>>开启</option>
							<option value="0" <?php if($d['status'] == '0'): ?>selected<?php endif; ?>>关闭</option>
						</select>
						<span class="help-inline col-xs-12 col-sm-7">
							<span class="middle"></span>
						</span>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-3 control-label" for="rules">规则列表:</label>
					<div class="col-sm-9">
						<textarea id="rules" name="rules" class="col-xs-10 col-sm-5" rows="3"><?php echo ($d['rules']); ?></textarea>
						<span class="help-inline col-xs-12 col-sm-7">
							<a class="btn btn-default btn-xs" data-toggle="modal" data-target="#rules-modal" dit-params="">获取权限列表</a>
						</span>
					</div>
				</div>
				
				<div class="form-tip claerfix col-md-offset-3 red"></div>
				<div class="clearfix form-actions">
					<div class="col-md-offset-3 col-md-9">
						<input type="hidden" name="id" value="<?php echo ($_GET['id']); ?>" />
						<button class="btn btn-info" type="submit">
							<i class="icon-ok bigger-110"></i><?php echo ($_GET['id']?'修改':'添加'); ?>分组规则
						</button>
						<a class="btn btn-info" id="backward">返回</a>
					</div>
				</div>
			</form>
			<!-- ./ end form -->
		</div>
		<!-- ./ end col-xs-12 -->
	</div>
	<!-- ./ end row -->
	<?php echo W('Unit/rulesModal');?>
</div>
</div>
</body>
<?php echo W('Public/foot');?>
</html>