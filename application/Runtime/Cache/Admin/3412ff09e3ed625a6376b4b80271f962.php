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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Goods/notice'),'text'=>'商品列表'], ['href'=>U('Goods/edit_goods',['id'=>$re['goods_id']]),'text'=>$re['name']], ['href'=>'','text'=>'型号库存'] ] ]);?>
<style>
    #big{
        position:absolute;
        left:490px;
        top:0px;
        z-index:999;
        display:none;
    }
    .tab{width:75%}
    .layui-layer-btn {
        text-align: center!important;
        padding: 0 10px 12px;
        pointer-events: auto;
    }
    #menu{overflow:hidden; padding-top:10px}
    #menu #nav {display:block;width:100%;padding:0;margin:0;list-style:none;}
    #menu #nav li {float:left;width:10%;}
    #menu #nav li a {display:block;line-height:27px;text-decoration:none;padding:0 0 0 5px; text-align:center; color:#333;}
    #menu_con{ width:100%;padding-top:50px}
    .selected{background:#C5A069;height:30px; color:#fff;}
    .tabs .tbas td{width:18%;border-bottom:1px dashed;line-height:35px}
    .aa td{border-bottom: 1px dotted #000;padding-top:10px;padding-bottom:10px;width:20% }
    table.Height0,div.Height0{height: 0px;overflow: hidden;}
</style>
<div class="page-container">
    <div id="big"></div>
    <div id="big2"></div>
    <form class="form form-horizontal" id="form" method="post">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red"></span>产品型号一：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" name="kinds[]" class="input-text" id="param1" value="<?php echo ($re['kinds'][0]); ?>" placeholder="产品型号一" />
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">型号参数：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <table class="table table-border table-bordered table-bg table-hover table-sort">
                    <thead>
                    <tr class="text-c">
                        <th width="50">型号名称</th>
                        <th width="50">原价</th>
                        <th width="50">售价</th>
                        <th width="50">操作 / <a href="javascript:void(0)" onclick="add_kinds(0,<?php echo ($re["goods_id"]); ?>,'1')"  title="添加型号"><u><i class="Hui-iconfont">&#xe600;</i> 型号</u></a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($kinds_detail)): $i = 0; $__LIST__ = $kinds_detail;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
                            <td><?php echo ($vo["kinds_detail"]); ?></td>
                            <td><?php echo ($vo['price']); ?></td>
                            <td><?php echo ($vo['sale_price']); ?></td>
                            <td class="td-manage">
                                <a title="编辑" href="javascript:;" onclick="add_kinds(<?php echo ($vo['kind_id']); ?>,<?php echo ($re['goods_id']); ?>,'1')"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                                <a title="删除" href="javascript:;" onclick="del_kinds(<?php echo ($vo['kind_id']); ?>,<?php echo ($re['goods_id']); ?>,'1')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品型号二：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" name="kinds[]" class="input-text" id="param2" value="<?php echo ($re['kinds'][1]); ?>" placeholder="产品型号二"  />
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">型号参数：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <table class="table table-border table-bordered table-bg table-hover table-sort">
                    <thead>
                    <tr class="text-c">
                        <th width="50">型号名称</th>
                        <th width="50">原价</th>
                        <th width="50">售价</th>
                        <th width="50">操作 / <a href="javascript:void(0)" onclick="add_kinds(0,<?php echo ($re["goods_id"]); ?>,'2')"  title="添加型号"><u><i class="Hui-iconfont">&#xe600;</i> 型号</u></a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($kinds_detail2)): $i = 0; $__LIST__ = $kinds_detail2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
                            <td><?php echo ($vo["kinds_detail"]); ?></td>
                            <td><?php echo ($vo['price']); ?></td>
                            <td><?php echo ($vo['sale_price']); ?></td>
                            <td class="td-manage">
                                <a title="编辑" href="javascript:;" onclick="add_kinds(<?php echo ($vo['kind_id']); ?>,<?php echo ($re['goods_id']); ?>,'2')"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                                <a title="删除" href="javascript:;" onclick="del_kinds(<?php echo ($vo['kind_id']); ?>,<?php echo ($re['goods_id']); ?>,'1')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row cl" style="margin-bottom: 50px;">
            <label class="form-label col-xs-4 col-sm-2">型号库存：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <table class="table table-border table-bordered table-bg table-hover table-sort">
                    <thead>
                    <tr class="text-c">
                        <th width="50">型号一</th>
                        <th width="50">型号二</th>
                        <th width="50">库存量</th>
                        <th width="50">已售</th>
                        <th width="50">操作 / <a href="javascript:void(0)" onclick="add_stock(0,<?php echo ($re["goods_id"]); ?>)"  title="添加型号库存"><u><i class="Hui-iconfont">&#xe600;</i> 库存</u></a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($stock)): $i = 0; $__LIST__ = $stock;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
                            <td>
                                <?php $kinds = explode(',',$vo['kinds']); if(!empty($kinds[0])){ echo M('GoodsKinds')->where(['kind_id'=>$kinds[0]])->getField('kinds_detail'); } ?>
                            </td>
                            <td>
                                <?php $kinds = explode(',',$vo['kinds']); if(!empty($kinds[1])){ echo M('GoodsKinds')->where(['kind_id'=>$kinds[1]])->getField('kinds_detail'); } ?>
                            </td>
                            </td>
                            <td><?php echo ($vo['number']); ?></td>
                            <td><?php echo ($vo['sale_number']); ?></td>
                            <td class="td-manage">
                                <a title="编辑" href="javascript:;" onclick="add_stock(<?php echo ($vo['stock_id']); ?>,<?php echo ($re['goods_id']); ?>)"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                                <a title="删除" href="javascript:;" onclick="del_stock(<?php echo ($vo['stock_id']); ?>,<?php echo ($re['goods_id']); ?>)" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button  onClick="javascript:history.back(-1);" class="btn btn-primary radius"  type="button"><i class="Hui-iconfont">&#xe632;</i> 保存并提交</button>
                <!--<button onClick="article_save();" class="btn btn-secondary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> 保存草稿</button>-->
                <button onClick="javascript:history.back(-1);" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
                <input type="hidden" class="input-text" value="<?php echo ($_GET['id']); ?>" placeholder=""  name="id">
            </div>
        </div>
    </form>
</div>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript">
    $(document).ready(function() {
        var content;
        KindEditor.ready(function (K) {
            content = K.create('#content', {
                allowFileManager: true,
                uploadJson: "<?php echo U('Tools/upload',['dirname'=>'banner']);?>"
            });
        });

        KindEditor.ready(function (K) {
            K.create();
            var editor = K.editor({
                allowFileManager: true,
                uploadJson: "<?php echo U('Tools/upload',['dirname'=>'banner']);?>"
                //sdl:false
            });
            K('#uparea1').click(function () {
                console.log(1);
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

        });

        $(".submit").click(function () {
            commonAjaxSubmit('', 'form');
            return false;
        });

        $("#uparea1").mouseover(function (e) {
            $("#big").css({top: e.pageY, right: e.pageX});//鼠标定位一个点
            var img = $('#image_1').val();
            if (img.length !== 0) {
                $("#big").html('<img src="' + $('#image_1').val() + '" width=380 height=300>');
                $("#big").show();        //show：显示
            }
        });
        $("#uparea1").mouseout(function () {
            $("#big").hide();
        });


    })

    function add_kinds(id,v,e){
        var url = "<?php echo U('Goods/edit_kinds');?>";
        if(id != null || id != undefined || id != ' ' || id != '0') {
            $.ajax({
                type: "GET",
                url: url,
                dataType: 'json',
                data: {id: id},
                success: function (data) {
                    console.log(data);
                    if (data['status'] == 'ok') {
                        if(data.data != null){
                            $("#kinds_detail").val(data.data.kinds_detail);
                            $("#price").val(data.data.price);
                            $("#sale_price").val(data.data.sale_price);
                        }
                    }
                }
            })
        }
        var html='<table class="table table-striped table-hover">' +
                '<tr>' +
                '<td style="vertical-align: middle;">型号名称</td>' +
                '<td><input type="text" name="kinds_detail" class="input-text" value="" id="kinds_detail" style="width:320px;" placeholder="型号名称" />' +
                '<span class="red important"> * </span> ' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '<tr>' +
                '<td style="vertical-align: middle;">原价</td>' +
                '<td><input type="text" name="price"  id="price" class="input-text" style="width:320px;" value="0"  placeholder="商品售价"/>' +
                '<span class="red important"> * </span> ' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '<tr>' +
                '<td style="vertical-align: middle;">售价</td>' +
                '<td><input type="text" name="sale_price"  id="sale_price" class="input-text" style="width:320px;" value="0"  placeholder="商品售价"/>' +
                '<span class="red important"> * </span> ' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '<tr>' +
                '<tr>' +
                '<td></td>' +
                '<td>' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '</table>'
        layer.open({
            type: 1,
            title: '商品型号',
            closeBtn: 0,
            area: ['500px','240px'],
            skin: '', //没有背景色
            shadeClose: true,
            content: html,
            btn:['保存'],
            yes:function(){
                var kinds_detail = $("#kinds_detail").val();
                var sale_price = $("#sale_price").val();
                var price = $("#price").val();
                var kinds1 = $('#param1').val();
                var kinds2 = $('#param2').val();
                var arr = new Array();
                arr.push(kinds1,kinds2);
                $.ajax({
                    url:url,
                    type:'post',
                    data:{id:id,goods_id:v,kinds_detail:kinds_detail,price:price,sale_price:sale_price,kinds:arr,type:e},
                    dataType:'json',
                    success:function(data){
                        if(data['status'] == 'ok'){
                            layer.msg(data.info,{icon:1,time:1000})
                            window.location.href = window.location.href;
                        }else{
                            layer.msg(data.info,{icon:5,time:1000})
                        }
                    }
                })
            }
        });

    };

    function del_kinds(id,v,e){
        var url = "<?php echo U('Goods/del_kinds');?>";
        layer.confirm('确认要删除吗？',function(index){
            $.post(url, {id:id}, function(data){
                if(data['status'] == 'ok'){
                    layer.msg(data.info,{icon:1,time:1000})
                    window.location.href = window.location.href;
                }else{
                    layer.msg(data.info,{icon:5,time:1000})
                }
            },'json');
        });

    };

    function add_stock(id,v){
        var url = "<?php echo U('Goods/edit_goods_stock');?>";
        if(id != null || id != undefined || id != ' ' || id != '0') {
            $.ajax({
                type: "GET",
                url: url,
                dataType: 'json',
                data: {id: id},
                success: function (data) {
                    console.log(data);
                    if (data['status'] == 'ok') {
                        if(data.data != null){
                            console.log(data);
                            $('#kinds_one'+data.data.kinds1).attr("selected",true);
                            $('#kinds_twe'+data.data.kinds2).attr("selected",true);
                            $("#kinds_number").val(data.data.number);
                            $("#kinds_sale_number").val(data.data.sale_number);
                            $("#template_id2").val(data.data.template_id);
                        }
                    }
                }
            })
        }
        var html='<table class="table table-striped table-hover">' +
                '<tr>' +
                '<td style="vertical-align: middle;">型号一</td>' +
                '<td><select name="kinds_id1"  class="select select-box inlin" value="" id="kinds_id1" style="width:320px;">' +
                '<option value="">请选择型号</option>' +
                '<?php if(is_array($kinds_detail)): $i = 0; $__LIST__ = $kinds_detail;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>' +
                '<option value="<?php echo ($vo["kind_id"]); ?>" id="kinds_one<?php echo ($vo["kind_id"]); ?>" ><?php echo ($vo["kinds_detail"]); ?></option>' +
                '<?php endforeach; endif; else: echo "" ;endif; ?>' +
                '</select>' +
                '<span class="red important"> * </span> ' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '<tr>' +
                '<td style="vertical-align: middle;">型号二</td>' +
                '<td><select  name="kinds_id2"  class="select select-box inlin" value="" id="kinds_id2" style="width:320px;">' +
                '<option value="">请选择型号</option>' +
                '<?php if(is_array($kinds_detail2)): $i = 0; $__LIST__ = $kinds_detail2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>' +
                '<option value="<?php echo ($vo["kind_id"]); ?>" id="kinds_twe<?php echo ($vo["kind_id"]); ?>" ><?php echo ($vo["kinds_detail"]); ?></option>' +
                '<?php endforeach; endif; else: echo "" ;endif; ?>' +
                '</select>' +
                '<span class="red important"> * </span> ' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '<tr>' +
                '<td style="vertical-align: middle;">库存量</td>' +
                '<td><input type="text" name="kinds_number" class="input-text" value="" id="kinds_number" style="width:320px;" placeholder="商品库存"/>' +
                '<span class="red important"> * </span> ' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '<tr>' +
                '<td style="vertical-align: middle;">已售数量</td>' +
                '<td><input type="text" name="kinds_sale_number" class="input-text" value="" id="kinds_sale_number" style="width:320px;" placeholder="商品出售量"/>' +
                '<span class="red important"> * </span> ' +
                '</td>' +
                '<td></td> ' +
                '</tr>' +
                '</table>'
        layer.open({
            type: 1,
            title: '库存设置',
            closeBtn: 0,
            area: ['500px','290px'],
            skin: '', //没有背景色
            shadeClose: true,
            content: html,
            btn:['保存'],
            yes:function(){
                var kinds_id1 = $('#kinds_id1').val();
                var kinds_id2 = $('#kinds_id2').val();
                var number = $('#kinds_number').val();
                var sale_number = $('#kinds_sale_number').val();
                var template_id = $('#template_id2').val();
                $.ajax({
                    url:url,
                    type:'post',
                    data:{id:id,goods_id:v,kinds_id1:kinds_id1,kinds_id2:kinds_id2,
                        number:number,sale_number:sale_number},
                    dataType:'json',
                    success:function(data){
                        console.log(data);
                        if(data['status'] == 'ok'){
                            layer.msg(data.info,{icon:1,time:1000})
                            window.location.href = window.location.href;
                        }else{
                            layer.msg(data.info,{icon:5,time:1000})
                        }
                    }
                })
            }
        });

    };

    function del_stock(id,v){
        var url = "<?php echo U('Goods/del_goods_stock');?>";
        layer.confirm('确认要删除吗？',function(index){
            $.post(url, {id:id}, function(data){
                if(data['status'] == 'ok'){
                    layer.msg(data.info,{icon:1,time:1000})
                    window.location.href = window.location.href;
                }else{
                    layer.msg(data.info,{icon:5,time:1000})
                }
            },'json');
        });
    }

</script>
<!--/请在上方写此页面业务相关的脚本-->
</div>
</body>
<?php echo W('Public/foot');?>
</html>