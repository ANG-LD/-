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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>U('Charts/day'),'text'=>'日活跃度'], ] ]);?>
<div class="page-container">
    <div class="text-l">
        <input type="text" class="input-text" name="start_time" value="<?php echo ($month); ?>" id="start_time" placeholder="日期时间"  style="width:250px">
        <button type="submit" type="submit"  class="btn btn-success radius" onclick="code($('#start_time').val())"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
    </div>
    <div class="cl pd-5 bg-1 bk-gray">
        <div id="container" style="min-width:700px;height:400px"></div>
    </div>
    <div>当日总活跃：<span id="count"></span>人</div>
</div>

<script type="text/javascript" src="/lib/layui/lay/dest/layui.all.js"></script>
<script type="text/javascript">
    $(function () {
        var url = "<?php echo U('Charts/day_code');?>";
        function code(e) {
            $.get(url, {code: e}, function (data) {
                if (data['status'] = 'ok') {
                    var a = data.data.a;
                    var b = data.data.b;
                    $("#count").html(data.data.c);
                    $('#container').highcharts({
                        title: {
                            text: "平台日活跃度统计",
                            x: -20 //center
                        },
                        subtitle: {
                            text: '',
                            x: -20
                        },
                        xAxis: {
                            categories: b
                        },
                        yAxis: {
                            title: {
                                text: '人数(人)'
                            },
                            plotLines: [{
                                value: 0,
                                width: 1,
                                color: '#808080'
                            }]
                        },
                        tooltip: {
                            valueSuffix: '人数'
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'middle',
                            borderWidth: 0
                        }, series: [{
                            name: '活跃度',
                            data: a
                        }]
                    });
                }
            }, 'json');
        }

        code();
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            var start = {
                elem: '#start_time',
                event: 'click', //触发事件
                format: 'YYYY-MM-DD', //日期格式
                istime: false, //是否开启时间选择
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
                format: 'YYYY-MM-DD', //日期格式
                istime: false, //是否开启时间选择
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
//        document.getElementById('end_time').onclick = function(){
//            end.elem = this
//            laydate(end);
//        }
        });
    });
</script>
</div>
</body>
<?php echo W('Public/foot');?>
</html>