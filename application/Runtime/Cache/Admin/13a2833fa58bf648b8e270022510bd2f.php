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
	<!--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">-->
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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Circle/index'),'text'=>'社区资讯'], ] ]);?>
</if>
<script type="text/javascript" src="/public/admin/layer/layer.js"></script>
<script type="text/javascript" src="/public/admin/js/jquery-1.7.min.js"></script>
<div class="page-content">
<div class="row">
<div class="col-xs-12">
        <span style="float:left;padding-right:30px;padding-top:8px;padding-bottom:18px">
        <a href="javascript:void(0);" style="text-decoration:none;color:#000000"><span onclick="del();" style="padding:10px 10px 10px 10px;background-color:#DDDDDD;">删除</span></a>
         </span>
    <form class="search" action="/system.php/Circle/index.html" method="get">
        <input name="m" value="<?php echo MODULE_NAME;?>" hidden>
        <input name="a" value="<?php echo ACTION_NAME;?>" hidden>
        <input name="c" value="<?php echo CONTROLLER_NAME;?>" hidden>
        <span style="float:right;padding-right:30px;padding-top:8px;padding-bottom:18px">
        <a href="<?php echo U('Circle/add_article');?>" style="text-decoration:none;color:#000000">
            <span style="padding:10px 10px 10px 10px;background-color:#DDDDDD;">添加社区资讯</span>
        </a>
       </span>
    </form>
<table width="100%" class="tab">
    <thead>
    <tr>
        <td><a href="javascript:;" id="selall">全选</a></td>
        <td>标题</td>
        <td>图片</td>
        <td>创建时间</td>
        <td>发布状态</td>
        <td width="250">操作</td>
    </tr>
    </thead>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr align="center" id="<?php echo ($vo["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#FFFFBB';"> 
           <td><input type="checkbox" name="ids" id="ids"  value="<?php echo ($vo["id"]); ?>"/>
              <?php if($_GET['p'] > 0): echo ($key+1+($_GET['p']-1)*15); ?>
                <?php else: ?>
                <?php echo ($key+1); endif; ?>
           </td>
            <td><?php echo ($vo["title"]); ?></td>
           <td><img src="<?php echo ($vo["img"]); ?>" style="width:80px;height:60px"></td>
            <td><?php echo ($vo["intime"]); ?></td>
            <td>
                <a href="javascript:void(0)" onclick="change_status(<?php echo ($vo["id"]); ?>,this)" <?php if($vo['status'] == 2): ?>style="color:red"<?php endif; ?>>
                    <?php $vo['status'] == '1' && print "默认状态"; $vo['status'] == '2' && print "头条状态"; ?>
                </a>
            </td>
            <td>
                [ <a href="<?php echo U('Circle/edit_article',array('id'=>$vo[id]));?>">编辑 </a> ]
                <!--[ <a href="javascript:;;" onclick="copy(<?php echo ($vo["id"]); ?>)">复制链接 </a> ]-->
                [ <a href="javascript:;" onclick="del(<?php echo ($vo["id"]); ?>)">删除 </a> ]
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
    <?php echo ($page); ?>
<script type="text/javascript">   
$(function() {   
$("#selall").toggle(function() {
     $("input[name='ids']").each(function() {   
     $(this).attr("checked", true); 
     });
},function(){
	 $("input[name='ids']").each(function() {   
     $(this).attr("checked", false); 
     }); 
});

});

function del(kid){
    kid = kid ? kid : getChecked();
    kid = kid.toString();
    if(kid == ''){
        layer.msg('你没有选择任何选项！', {offset: 95,shift: 6});
        return false;
    }
    if(!confirm('删除后无法恢复，确定删除？'))
        return false;
    $.post("<?php echo U('Circle/del_article');?>", {ids:kid}, function(data){
        if( data.status == 'ok' ){
            alert(data.info);
            window.location.href = data.url;
        }else{
            alert(data.info);
        }
    },'json');
}
function getChecked() {
var gids = new Array();
$.each($('input:checked'), function(i, n){
	gids.push( $(n).val() );
});
return gids;
}

function change_status(id,v){
    var id = id;
    $.post("<?php echo U('Circle/change_article_status');?>",{id:id},function(data){
        if(data['status'] == 'ok'){
            $(v).html(data['info']);
        }else{
            alert(data['info']);
        }
    },'json');
    return false;
}

function copy(v)
{
    var Url2=document.getElementById("copy"+v);
    Url2.select(); // 选择对象
    document.execCommand("Copy");  // 执行浏览器复制命令
}

</script>
</div>
</div>
</div>
</div>
</body>
<?php echo W('Public/foot');?>
</html>