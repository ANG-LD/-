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
	<?php $uid = $_GET['id']; $_GET['id']?$text='积分':$text='积分'; ?><?php echo W('public/breadcrumbs',[ [ ['href'=>U('Marketing/integral'),'text'=>'积分设置'], ['href'=>'','text'=>$text] ] ]);?><style>	.ibutton { padding: 3px 15px; *padding: 0 15px; *height: 24px;  font-size: 12px; text-align: center; text-shadow: #CF510B 0 1px 0; border:1px solid #ec5c0d; border-radius: 2px; background: #FC750A; background-image: -webkit-linear-gradient(top, #fc8746, #ec5d0e); color:#FFF; cursor: pointer; display: inline-block; }</style><script type="text/javascript" src="/public/upload/jquery.uploadify.v2.1.4.min.js"></script><style>    #big{       position:absolute;       left:490px;       top:0px;	   z-index:999;	   display:none;    }</style><div class="page-content">    <div id="big"></div>	<div id="big2"></div>	<div class="row">			<div class="col-xs-12"><br/>				     <div>		        <span style="padding-top:20px;font-weight:bold;">					<?php echo $text ?>				</span>		     </div>		     <hr/>			<form  onsubmit="return checked()">				<table width="100%" border="0" class="all">					<tr><td width="5%">&emsp;</td><td width="65%">&emsp;</td></tr>					<tr>						<td width="10%" align="center">消费积分值</td>						<td width="65%"><input value="<?php echo ($re['score']); ?>" name="score" id="score" style="width: 545px;" type="text" size="50"></td>					</tr>					<tr><td width="5%">&emsp;</td><td width="65%">&emsp;</td></tr>					<tr>						<td width="10%" align="center">抵扣金额值</td>						<td width="65%"><input value="<?php echo ($re['money']); ?>" name="money" id="money" style="width: 545px;" type="text" size="50"></td>					</tr>					<tr><td width="5%">&emsp;</td><td width="65%">&emsp;</td></tr>					<tr>						<td width="10%" align="center" style="font-size:16px;">发放规则</td>						<td width="65%"></td>					</tr>					<tr><td width="5%">&emsp;</td><td width="65%">&emsp;</td></tr>					<tr>						<td width="10%" align="center">消费金额满</td>						<td width="65%">							<input onkeydown="onlyNum(this);" value="<?php echo ($re['consumer']); ?>" name="consumer" id="consumer" type="text" style="width: 250px;" size="5" maxlength="11">							<span style="margin-left: -50px;">元</span><span style="margin:0 12px 0 50px;">送</span>							<input onkeydown="onlyNum(this)"  value="<?php echo ($re['give_score']); ?>" name="give_score" id="give_score" type="text" style="width: 250px;" size="5" maxlength="11" />							<span style="margin-left: -50px;">积分</span>						</td>					</tr>					<tr><td width="5%">&emsp;</td><td width="65%">&emsp;</td></tr>				</table>				<button class="ibutton submit" style="margin:20px 0 0 100px;" type="button">&emsp;保&emsp;存&emsp;</button>			</form>		</div>	</div></div><script type="text/javascript">	$(function(){		var  content ;		KindEditor.ready(function(K) {			content = K.create('#content',{				allowFileManager : true,				uploadJson:'system.php/tools/upload?dirname=banner'			});		});		KindEditor.ready(function(K) {			K.create();			var editor = K.editor({				allowFileManager : true,				uploadJson:'system.php/tools/upload_tx?dirname=banner'				//sdl:false			});			K('#uparea1').click(function() {				editor.loadPlugin('image', function() {					editor.plugin.imageDialog({						imageUrl : K('#image_1').val(),						clickFn : function(url, title, width, height, border, align) {							console.log(url);							$('#image1').css('background-image','url('+url+')').css('background-size','220px 160px');							K('#image_1').val(url);							// K('#getImgUrl').val(url);							editor.hideDialog();						}					});				});			});		});		$(".submit").click(function(){			commonAjaxSubmit();			return false;		});		$("#uparea1").mouseover(function(e){			$("#big").css({top:e.pageY,right:e.pageX});//鼠标定位一个点			var img = $('#image_1').val();			if(img.length !== 0) {				$("#big").html('<img src="' + $('#image_1').val() + '" width=380 height=300>');				$("#big").show();        //show：显示			}		});		$("#uparea1").mouseout(function(){			$("#big").hide();		});		$(".pic3").mouseover(function(e){			$("#big2").css({top:e.pageY,right:e.pageX});//鼠标定位一个点			$("#big2").html('<img src="'+this.src+'" width=380 height=300>');			$("#big2").show();        //show：显示		});		$(".pic3").mouseout(function(){			$("#big2").hide();		});	});	function changeMk(v){		$('.tag').hide();		$('#tag'+v).show();	}	$("#start_time").datetimepicker({		format: 'yyyy-mm-dd',		weekStart: 1,		autoclose: true,		todayBtn: 'linked',		language: 'zh-CN',		minView: 2	}).on('changeDate', function(ev) {		$("#end_time").datetimepicker('setStartDate', $(this).val());	});	$("#end_time").datetimepicker({		format: 'yyyy-mm-dd',		weekStart: 1,		autoclose: true,		todayBtn: 'linked',		language: 'zh-CN',		minView: 2	});	function onlyNum(v){		if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))			if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105)))//				event.returnValue = false;  //执行至该语句时，阻止输入；可类比阻止冒泡原理或者禁止右键功能；			alert("请填写数字类型的值");	}</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>