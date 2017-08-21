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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Class/online'),'text'=>'线上班级'], ] ]);?>
<div class="page-container">
    <div class="text-l">
        <form class="search"  method="get">
            <input name="p" type="hidden" value="1" />
            <input type="text" class="input-text" style="width:250px" placeholder="搜索班级名称、编号ID、名师" value="<?php echo ($_GET['username']); ?>" name="username">
            <input type="text" class="input-text "  id="start_time" style="width:190px" name="start_time" value="<?php echo urldecode($_GET['start_time']) ?>"  placeholder="开始时间" readonly>
            <input type="text" class="input-text "  id="end_time" style="width:190px" name="end_time" value="<?php echo urldecode($_GET['end_time']) ?>"  placeholder="结束时间" readonly>
            <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
        	<span style="float:right;padding:0px 10px 10px 0" >
                <a href="javascript:void(0)"  data-action="<?php echo U('Class/add_online');?>" class="check_auth btn btn-primary radius" >
                    <i class="Hui-iconfont">&#xe600;</i>添加班级
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
                <th width="40">班级id</th>
                <th width="100">班级标题</th>
                <th width="100">名师昵称</th>
                <th width="80">名师ID</th>
                <th width="40">开始时间</th>
                <th width="40">结束时间</th>
                <th width="40">状态</th>
                <th width="40">创建时间</th>
                <th width="120">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="text-c">
                    <td><input type="checkbox" value="<?php echo ($l["class_id"]); ?>" name="checkbox"></td>
                    <td><?php echo ($l["class_id"]); ?></td>
                    <!--<td align="center" class="onerow"><img src="<?php echo ($l["img"]); ?>" style="width: 50px;height: 50px;border-radius:50%"></td>-->
                    <td><?php echo ($l["name"]); ?></td>
                    <td><?php echo ($l["username"]); ?></td>
                    <td><?php echo ($l["id"]); ?></td>
                    <td><?php echo ($l["start_time"]); ?></td>
                    <td><?php echo ($l["end_time"]); ?></td>
                    <td><?php if($l['is_end'] == 1): ?><span class="label label-defaunt radius">已结束</span>
                        <?php else: ?>
                        <span class="label label-success radius">开班中</span><?php endif; ?></td>
                    <td><?php echo ($l["intime"]); ?></td>
                    <td class="td-manage" style="text-align: center">
                        <a title="编辑" href="<?php echo U('Class/edit_online',['id'=>$l['class_id']]);?>"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                        <!--<a title="举报列表" href="<?php echo U('Live/report',['id'=>$l['live_id']]);?>"  class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe692;</i></a>-->
                        <a title="删除" href="javascript:;" onclick="del(this,'<?php echo ($l["class_id"]); ?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <?php echo ($page); ?>
    </div>
    <div id="made" class="hide" style="display: none;">
        <img style="width:100%" id="zhubo" src="">
    </div>
</div>
<script src="/public/admin/js/layer/layer.js"></script>
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
            $.post("<?php echo U('Class/del_online_class');?>", {ids:kid}, function(data){
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
        layer.confirm('确认要下架吗？',function(index){
            $.post("<?php echo U('Live/change_video_shenhe');?>",{id:id},function(data){
                if(data.info == 1){
                    $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none"  onClick="member_start(this,'+id+')" href="javascript:;" title="审核"><i class="Hui-iconfont">&#xe603;</i></a>');
                    $(obj).remove();
                    layer.msg('已下架成功!',{icon: 5,time:1000});
                }
            },'json')
        });
    }

    /*用户-启用*/
    function member_start(obj,id){
        layer.confirm('确认要审核吗？',function(index){
            $.post("<?php echo U('Live/change_video_shenhe');?>",{id:id},function(data){
                if(data.info == 2){
                    $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="ml-5" onClick="member_stop(this,'+id+')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
                    $(obj).remove();
                    layer.msg('已审核成功!',{icon: 6,time:1000});
                }
            },'json');

        });
    }
    /*用户-删除*/
    function del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.post("<?php echo U('Class/del_online_class');?>", {ids:id}, function(data){
                if( data.status == 'ok' ){
                    $(obj).parents("tr").remove();
                    layer.msg('已删除!',{icon:1,time:1000});
                }else{
                    layer.msg(data.info,{icon:5,time:1000});
                }
            },'json');
        });
    }

    function offline(id) {
        layer.confirm('确定强制下线？',function(index){
            $.post("<?php echo U('Live/offline');?>", {id:id}, function(v){
                if( v == 1 ){
                    layer.msg('已强制下线！',{icon:1,time:1000});
                    window.location.href = window.location.href;

                }else{
                    layer.msg('强制下线失败！',{icon:5,time:1000});
                }
            });
        });
    }

    function view_url(v){
        layer.open({
            type: 2,
            title: false,
            area: ['800px', '500px'],
            shade: 0.8,
            closeBtn: 0,
            shadeClose: true,
            content: v
        });
    }

    function view_play_img(v){
        $("#zhubo").attr('src',v);
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            area: '516px',
            skin: 'layui-layer-nobg', //没有背景色
            shadeClose: true,
            content: $('#made')
        });
    }

    function view_img(v,id){
        var url = "<?php echo U('Live/check_img');?>";
        $.post(url,{img:v},function(data){
            if(data.status == 'ok'){
                $("#zhubo").attr('src',v);
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 0,
                    area: '384px',
                    skin: 'layui-layer-nobg', //没有背景色
                    shadeClose: true,
                    content: $('#zhubo')
                });
            }else{
                layer.msg('该直播已经结束!',{icon:5,time:1000});
                window.location.href = window.location.href;
            }
        },'json');
        return false;
    }

    function sel(id) {
        layer.open({
            type: 2,
            title: false,
            area: ['1020px', '587px'],
            shade: 0.1,
            closeBtn: 1,
            shadeClose: false,
            content: id,
        });
    }
</script>
<script type="text/javascript" src="/lib/layui/lay/dest/layui.all.js"></script>
<script>
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        var start = {
            elem: '#start_time',
            event: 'click', //触发事件
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式
            istime: true, //是否开启时间选择
            isclear: true, //是否显示清空
            istoday: true, //是否显示今天
            issure: true, //是否显示确认
            festival: true,//是否显示节日
            min: '1900-01-01 00:00:00', //最小日期
            max: '2099-12-31 23:59:59', //最大日期
            choose: function(datas){
                $("#start_time").attr("value",datas);
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end_time',
            event: 'click', //触发事件
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式
            istime: true, //是否开启时间选择
            isclear: true, //是否显示清空
            istoday: true, //是否显示今天
            issure: true, //是否显示确认
            festival: true,//是否显示节日
            min: '1900-01-01 00:00:00', //最小日期
            max: '2099-12-31 23:59:59', //最大日期
            choose: function(datas){
                $("#end_time").attr("value",datas);
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        document.getElementById('start_time').onclick = function(){
            start.elem = this;
            laydate(start);
        }
        document.getElementById('end_time').onclick = function(){
            end.elem = this
            laydate(end);
        }
    });
</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>