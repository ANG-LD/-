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
	<?php $text='帖子详情' ?>
<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Topical/posts_list'),'text'=>'论贴列表'], ['href'=>'','text'=>$text] ] ]);?>
<div class="page-container">
	<div id="big"></div>
	<div id="big2"></div>
	<form class="form form-horizontal" id="form" method="post">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-1">缩略图：</label>
			<div class="formControls col-xs-8 col-sm-11">
				<?php if(is_array($re['thumb'])): $k = 0; $__LIST__ = $re['thumb'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="droparea spot preview1" id="t_image<?php echo ($k); ?>" data="<?php echo ($k); ?>" style="background-image: url('<?php echo ($vo['img']); ?>');background-size: 220px 160px;" >
						<div class="instructions"><span onclick="recovery1(<?php echo ($k); ?>)">一键还原</span><span onclick="tihuan1(<?php echo ($k); ?>)" style="margin-left: 50px">一键替换</span></div>
						<div id=""></div>
						<input type="hidden" name="t_imgs[]" id="t_image_<?php echo ($k); ?>" data-img="<?php echo ($vo['img']); ?>" data-weigui="/Uploads/weigui.png" value="<?php echo ($vo['img']); ?>" />
						<input type="hidden" name="t_width[]" id="t_width_<?php echo ($k); ?>" data-img="<?php echo ($vo['width']); ?>" data-weigui="480" value="<?php echo ($vo['width']); ?>" />
						<input type="hidden" name="t_height[]" id="t_height_<?php echo ($k); ?>" data-img="<?php echo ($vo['height']); ?>" data-weigui="360" value="<?php echo ($vo['height']); ?>" />
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-1">原图：</label>
			<div class="formControls col-xs-8 col-sm-11">
				<?php if(is_array($re['img'])): $k = 0; $__LIST__ = $re['img'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="droparea spot preview" id="image<?php echo ($k); ?>" data="<?php echo ($k); ?>" style="background-image: url('<?php echo ($vo['img']); ?>');background-size: 220px 160px;" >
						<div class="instructions"><span onclick="recovery(<?php echo ($k); ?>)">一键还原</span><span onclick="tihuan(<?php echo ($k); ?>)" style="margin-left: 50px">一键替换</span></div>
						<div id=""></div>
						<input type="hidden" name="imgs[]" id="image_<?php echo ($k); ?>" data-img="<?php echo ($vo['img']); ?>" data-weigui="/Uploads/weigui.png" value="<?php echo ($vo['img']); ?>" />
						<input type="hidden" name="width[]" id="width_<?php echo ($k); ?>" data-img="<?php echo ($vo['width']); ?>" data-weigui="480" value="<?php echo ($vo['width']); ?>" />
						<input type="hidden" name="height[]" id="height_<?php echo ($k); ?>" data-img="<?php echo ($vo['height']); ?>" data-weigui="360" value="<?php echo ($vo['height']); ?>" />
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
		<div class="row cl" style="margin-bottom: 50px;">
			<label class="form-label col-xs-2 col-sm-2">产品简介：</label>
			<div class="formControls col-xs-8 col-sm-8">
				<textarea name="content" cols="" rows="" class="textarea"  ><?php echo ($re["content"]); ?></textarea>
				<p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
			</div>
		</div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<button  class="submit btn btn-primary radius"  type="button"><i class="Hui-iconfont">&#xe632;</i> 保存并提交</button>
				<!--<button onClick="article_save();" class="btn btn-secondary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> 保存草稿</button>-->
				<button onClick="del(<?php echo ($re["post_id"]); ?>)" class="btn btn-default radius" type="button">&nbsp;&nbsp;删除&nbsp;&nbsp;</button>
				<input type="hidden" class="input-text" value="<?php echo ($_GET['id']); ?>" placeholder=""  name="id">
			</div>
		</div>
	</form>
</div>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript">

	$(function() {
		var content;
		KindEditor.ready(function (K) {
			content = K.create('#content', {
				allowFileManager: true,
				uploadJson: 'system.php/tools/upload?dirname=product'
			});
		});

		KindEditor.ready(function (K) {
			K.create();
			var editor = K.editor({
				allowFileManager: true,
				uploadJson: 'system.php/tools/upload?dirname=product'
				//sdl:false
			});
			K('#uparea1').click(function () {
				editor.loadPlugin('image', function () {
					editor.plugin.imageDialog({
						imageUrl: K('#image_1').val(),
						clickFn: function (url, title, width, height, border, align) {
							console.log(url);
							$('#image1').css('background-image', 'url(' + url + ')').css('background-size', '220px 160px');
							K('#image_1').val(url);
							// K('#getImgUrl').val(url);
							editor.hideDialog();
						}
					});
				});
			});

			$(".submit").click(function () {
				commonAjaxSubmit();
				return false;
			});

			$(".preview").mouseover(function (e) {
				$("#big").css({top: e.pageY, right: e.pageX});//鼠标定位一个点
				var data = $(this).attr('data');
				var img = $('#image_'+data).val();
				if (img.length !== 0) {
					$("#big").html('<img src="' + img + '" width=380 height=300>');
					$("#big").show();        //show：显示
				}
			});
			$(".preview").mouseout(function () {
				$("#big").hide();
			});

			$(".preview1").mouseover(function (e) {
				$("#big").css({top: e.pageY, right: e.pageX});//鼠标定位一个点
				var data = $(this).attr('data');
				var img = $('#t_image_'+data).val();
				if (img.length !== 0) {
					$("#big").html('<img src="' + img + '" width=380 height=300>');
					$("#big").show();        //show：显示
				}
			});
			$(".preview1").mouseout(function () {
				$("#big").hide();
			});
		});
	});

	function recovery(v){
		var img = $('#image_'+v).attr("data-img");
		$('#image'+v).css('background-image', 'url(' + img + ')').css('background-size', '220px 160px');
		var width = $('#width_'+v).attr('data-img');
		var height = $('#height_'+v).attr('data-img');
		$('#image_'+v).val(img);
		$('#width_'+v).val(width);
		$('#height_'+v).val(height);
	}
	function recovery1(v){
		var img = $('#t_image_'+v).attr("data-img");
		$('#t_image'+v).css('background-image', 'url(' + img + ')').css('background-size', '220px 160px');
		var width = $('#t_width_'+v).attr('data-img');
		var height = $('#t_height_'+v).attr('data-img');
		$('#t_image_'+v).val(img);
		$('#t_width_'+v).val(width);
		$('#t_height_'+v).val(height);
	}

	function  tihuan(v){
		var img = $('#image_'+v).attr("data-weigui");
		$('#image'+v).css('background-image', 'url(' + img + ')').css('background-size', '220px 160px');
		var width = $('#width_'+v).attr('data-weigui');
		var height = $('#height_'+v).attr('data-weigui');
		$('#image_'+v).val(img);
		$('#width_'+v).val(width);
		$('#height_'+v).val(height);
	}

	function  tihuan1(v){
		var img = $('#t_image_'+v).attr("data-weigui");
		$('#t_image'+v).css('background-image', 'url(' + img + ')').css('background-size', '220px 160px');
		var width = $('#t_width_'+v).attr('data-weigui');
		var height = $('#t_height_'+v).attr('data-weigui');
		$('#t_image_'+v).val(img);
		$('#t_width_'+v).val(width);
		$('#t_height_'+v).val(height);
	}

	function del(kid){
		var url = "<?php echo U('Topical/del_post');?>";
		$.post(url,{ids:kid},function(data){
			if( data.status == 'ok' ){
				layer.msg('已删除!',{icon:1,time:1000});
				window.location.href = data.url;
			}else{
				layer.msg(data.info,{icon:5,time:1000});
			}
		},'json');
	}


</script>
<!--/请在上方写此页面业务相关的脚本-->
</div>
</body>
<?php echo W('Public/foot');?>
</html>