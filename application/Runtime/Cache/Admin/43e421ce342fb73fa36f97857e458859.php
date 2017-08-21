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
	<?php $uid = $_GET['id']; $_GET['id']?$text='编辑协议':$text='添加协议'; ?>
<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Home/xieyi'),'text'=>'相关协议'], ['href'=>'','text'=>$text] ] ]);?>
<article class="page-container">
	<div id="big"></div>
	<div id="big2"></div>
	<form class="form form-horizontal" id="form" method="post">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>标题：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" name="title" class="input-text" value="<?php echo ($re['title']); ?>" placeholder="协议标题" id="title" />
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">协议内容：</label>
			<div class="formControls col-xs-8 col-sm-9">
					<?php echo W('Unit/ueditor',['content',$re['content']]);?>
			</div>
		</div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<button  class="submit btn btn-primary radius"  type="button"><i class="Hui-iconfont">&#xe632;</i> 保存并提交</button>
				<!--<button onClick="article_save();" class="btn btn-secondary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> 保存草稿</button>-->
				<button onClick="removeIframe();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
				<input type="hidden" class="input-text" value="<?php echo ($_GET['id']); ?>" placeholder=""  name="id">
			</div>
		</div>
	</form>
</article>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript">
		$(document).ready(function(){
			var  content ;
			KindEditor.ready(function(K) {
				content = K.create('#content',{
					allowFileManager : true,
					uploadJson:"<?php echo U('Tools/upload',['dirname'=>'banner']);?>"
				});
			});

			KindEditor.ready(function(K) {
				K.create();
				var editor = K.editor({
					allowFileManager : true,
					uploadJson:"<?php echo U('Tools/upload',['dirname'=>'banner']);?>"
					//sdl:false
				});
				K('#uparea1').click(function() {
					console.log(1);
					editor.loadPlugin('image', function() {
						editor.plugin.imageDialog({
							imageUrl : K('#image_1').val(),
							clickFn : function(url, title, width, height, border, align) {
								console.log(url);
								$('#image1').css('background-image','url('+url+')').css('background-size','220px 160px');
								K('#image_1').val(url);
								// K('#getImgUrl').val(url);
								editor.hideDialog();
							}
						});
					});
				});

			});

			$(".submit").click(function(){
				commonAjaxSubmit('','form');
				return false;
			});

			$("#uparea1").mouseover(function(e){
				$("#big").css({top:e.pageY,right:e.pageX});//鼠标定位一个点
				var img = $('#image_1').val();
				if(img.length !== 0) {
					$("#big").html('<img src="' + $('#image_1').val() + '" width=380 height=300>');
					$("#big").show();        //show：显示
				}
			});
			$("#uparea1").mouseout(function(){
				$("#big").hide();
			});


			$('.skin-minimal input').iCheck({
				checkboxClass: 'icheckbox-blue',
				radioClass: 'iradio-blue',
				increaseArea: '20%'
			});
		})

		function changeMk(v){
			$('.tag').hide();
			$('#tag'+v).show();
		}



</script>
<!--/请在上方写此页面业务相关的脚本-->
</div>
</body>
<?php echo W('Public/foot');?>
</html>