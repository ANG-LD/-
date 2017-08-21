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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Live/index'),'text'=>'分类列表'], ] ]);?>
</if>
<style type="text/css">
    .modal-sm{width:510px;}
    .layui-layer-btn {
        text-align: center!important;
        padding: 0 10px 12px;
        pointer-events: auto;
    }
    #big{
        position:absolute;
        left:490px;
        top:0px;
        z-index:999;
        display:none;
    }
</style>
<script type="text/javascript" src="/public/admin/js/jquery-1.7.min.js"></script>
<script type="text/javascript" src="/public/admin/layer/layer.js"></script>
<script src="/assets/js/jquery.form.js"></script>
<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <form class="search" action="/system.php/Live/index.html" method="get">
                <span style="float:right;padding:10px 30px 20px 0">
		          <a href="#"  data-toggle="modal" data-target="#popup" data-action="<?php echo U('User/add_user');?>" style="text-decoration:none;color:#000000">
                       <span style="padding:10px 10px 10px 10px;background-color:#DDDDDD;">添加分类</span>
                   </a>
		        </span>
            </form>

            <!--分类弹出框-->
            <div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div id="big"></div>
                <div id="big2"></div>
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button"  class="close" data-dismiss="modal" aria-label="Close">
                                <span  aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" style="text-align: center" id="myModalLabel">添加选项</h4>
                        </div>
                        <div class="modal-body">
                    <form method="post" class="ajax-frm">
                    <table class="table table-striped table-hover">
                    <tr>
                        <td style="vertical-align: middle;">选项名称</td>
                        <td><input type="text" name="category" value="" id="category" style="width:270px;"/>
                            <span class="yz name" style="color:red"> * </span>
                            </td>
                        <td></td>
                        </tr>
                    <tr>
                        <td style="vertical-align: middle;">分类图片</td>
                        <td style="text-align:left">
                            <div class="droparea spot" id="image1" style="background-image: url('');background-size: 220px 160px;" >
                                <div class="instructions" onclick="del_image(1)">删除</div>
                                <div id="uparea1"></div>
                                <input type="hidden" name="picture" id="image_1" value="" />
                                </div>
                            <span class="yz picture" id="picture" style="color:red"> * </span>
                            </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="hidden" name="id" id="data" value="" />
                            <input type="hidden" name="cate_id" id="cate_id" value="0" />
                            <input type="hidden" name="type" id="type" value="3" />
                            <button type="submit" style="margin-left:50px;padding:10px 30px 10px 30px;background-color:#DDDDDD;" >保存</button></td>
                        <td></td>
                    </tr>
                    </table>
                    </form>
                    </div>
                </div>
                </div>
            </div>

            <table width="100%" class="tab" style="margin-top:10px;">
                <thead>
                <tr><td colspan="6" align="left">
                <span style="float:left;padding-right:30px;padding-top:8px;padding-bottom:18px">
	              <a href="javascript:void(0);" style="text-decoration:none;color:#000000">
                      <span onclick="del();" style="padding:10px 10px 10px 10px;background-color:#DDDDDD;">删除</span>
                  </a>
	            </span>
                </td></td>
                </tr>
                <tr>
                    <td><a href="javascript:;" id="selall">全选</a></td>
                    <td width="20%">名称</td>
                    <td width="35%">图片</td>
                    <td width="15%">创建时间</td>
                    <td width="20%">操作</td>
                </tr>
                </thead>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr align="center" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#FFFFBB';">
                        <td align="center"><input type="checkbox" name="ids" id="ids"  value="<?php echo ($vo["id"]); ?>"/>
                        </td>
                        <td><?php echo ($vo['category']); ?></td>
                        <td width="400px"><img src="<?php echo ($vo["picture"]); ?>" style="width:80px; height:60px;"></td>
                        <td><?php echo ($vo["intime"]); ?></td>
                        <td>
                            [ <a href="#" data-toggle="modal"  onclick="edit_value(<?php echo ($vo["id"]); ?>)" data-target="#popup">编辑</a> ]
                            [ <a href="<?php echo U('Live/goods',['cate_id'=>$vo['id']]);?>">商品设置</a> ]
                            [ <a href="javascript:;"  onclick="del(<?php echo ($vo["id"]); ?>);">删除</a> ]
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
    </div>
</div>
<!--<script src="/public/admin/js/common.js"></script>-->
<script type="text/javascript">
    $(function() {
        var  content ;
        KindEditor.ready(function(K) {
            content = K.create('#content',{
                allowFileManager : true,
                uploadJson:"<?php echo U('Tools/upload_tx',['dirname'=>goods]);?>"
            });
        });

        KindEditor.ready(function(K) {
            K.create();
            var editor = K.editor({
                allowFileManager : true,
                uploadJson:"<?php echo U('Tools/upload_tx',['dirname'=>goods]);?>"
                //sdl:false
            });
            K('#uparea1').click(function() {
                editor.loadPlugin('image', function() {
                    editor.plugin.imageDialog({
                        imageUrl : K('#image_1').val(),
                        clickFn : function(url, title, width, height, border, align) {
                            $('#image1').css('background-image','url('+url+')').css('background-size','220px 160px');
                            K('#image_1').val(url);
                            // K('#getImgUrl').val(url);
                            editor.hideDialog();
                        }
                    });
                });
            });

            K('#uparea2').click(function() {
                editor.loadPlugin('image', function() {
                    editor.plugin.imageDialog({
                        imageUrl : K('#image_2').val(),
                        clickFn : function(url, title, width, height, border, align) {
                            $('#image2').css('background-image','url('+url+')').css('background-size','220px 160px');
                            K('#image_2').val(url);
                            // K('#getImgUrl').val(url);
                            editor.hideDialog();
                        }
                    });
                });
            });

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

        $("#uparea2").mouseover(function(e){
            $("#big").css({top:e.pageY,right:e.pageX});//鼠标定位一个点
            var img = $('#image_2').val();
            if(img.length !== 0) {
                $("#big").html('<img src="' + $('#image_2').val() + '" width=380 height=300>');
                $("#big").show();        //show：显示
            }
        });
        $("#uparea2").mouseout(function(){
            $("#big").hide();
        });

        $("#selall").toggle(function() {
            $("input[name='ids']").each(function() {
                $(this).attr("checked", true);
            });
        },function(){
            $("input[name='ids']").each(function() {
                $(this).attr("checked", false);
            });
        });

        $(".dClick").click(function(){
            var num=$(this).attr("class").substring(14);
            console.log(num);
            for(var i=1;i<$(".fl-list"+num).length;i++){
                if($(".fl-list"+num)[i].style.display=="none"){
                    $(".fl-list"+num)[i].style.display="";
                    $(this).children("i").attr("class","glyphicon glyphicon-triangle-bottom");
                }else{
                    $(".fl-list"+num)[i].style.display="none";
                    $(this).children("i").attr("class","glyphicon glyphicon-triangle-right");
                }
            }
        })

        $(".ajax-frm").submit(function(){
            var url = "<?php echo U('Live/add_class');?>";
            $.post(url,$(this).serializeArray(),function(data){
                if(data.status=='ok'){
                    popup.success(data.info);
                    setTimeout(function(){
                        popup.close("asyncbox_success");
                    },2000);
                    window.location.href = data.url;
                }else {
                    if (data['class'].length > 0) {
                        $('.yz').html('');
                        $('.' + data.class).html(data.info);
                        $('#' + data.class).focus();
                    } else {
                        popup.error(data.info);
                        setTimeout(function () {
                            popup.close("asyncbox_error");
                        }, 2000);
                    }
                }
            },'json');
            return false;
        })


    });


    function del(kid){

        kid = kid ? kid : getChecked();
        kid = kid.toString();
        if(kid == ''){
            layer.msg('你没有选择任何选项！', {offset: 95,shift: 6});
            return false;
        }
        if(!confirm('确定要删除该记录？\n删除一级分类，相应的下级分类也会被删除'))
            return false;
        $.post("<?php echo U('Goods/del_first_category');?>", {ids:kid}, function(data){
            if( data['status'] == 'ok' ){
                alert(data.info);
                window.location.href = data.url;
            }else{
                alert(data.info);
            }
        },'json');
        return false;
    }
    function getChecked() {
        var gids = new Array();
        $.each($('input:checked'), function(i, n){
            gids.push( $(n).val() );
        });
        return gids;
    }

    function edit_value(e){
        var url = "<?php echo U('Live/add_class');?>";
        $.get(url,{id:e},function(data){
            $("#data").val(data.info.id);
            $("#category").val(data.info.category);
            $('#image1').css('background-image','url('+data.info.picture+')').css('background-size','220px 160px');
            $('#image_1').val(data.info.picture);
            $(".cate_id"+data.info.cate_id).attr('selected',true);
        },'json');
    }

</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>