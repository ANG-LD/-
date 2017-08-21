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
	<?php echo W('public/breadcrumbs',[ [ ['href'=>'','text'=>'客服聊天'] ] ]);?>
<link rel="stylesheet" href="/assets/chat/css/style.css">
<script type="text/javascript" src="/assets/chat/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/assets/webim/webim.config.js"></script>
<script type="text/javascript" src="/assets/webim/strophe-1.2.8.js"></script>
<script type="text/javascript" src="/assets/webim/websdk-1.4.11.js"></script>
<!--艺名聊天-->
<div class="tstart_toolbar">
  <div class="ico_nav">
  <div class="item_box item_box_01">
  <a href="javascript:void(0);" class="speak_btn" id="person_show_btn">
	  <i class="i_ico"></i>
  </a>
  </div>
  <div class="item_box">
  <a href="javascript:void(0);" class="goto_top" id="gototop_btn">
	  <i class="i_ico"></i>
  </a>
  </div>
  </div>
</div>
<!--客服列表-->
<div class="speak_person_list_box">
  <div class="speak_person_hd" id="speak_person_hd">
    <div class="personlist_title">
		<a href="javascript:void(0);" class="i_ico close_btn" id="close_personlist_btn"></a>
		<a href="javascript:void(0)" class="fr mr5 hs_news_go" onclick="HsNewLook()">
		<img src="/assets/chat/images/xx.png" />
		</a>最近联系人
	</div>
    <div class="speak_personlist">
      <ul id="MessageerList">
      </ul>
    </div>
  </div>
</div>
<!--聊天框-->
<div class="tool_speak_box">
  <div class="personlist_title" id="personlist_title_top" onselectstart="return false">
	  <i class="i_ico person_ico"></i>
	  <a href="javascript:void(0);" class="i_ico close_btn close_btn2" id="tool_speak_box_close"></a>
	  <span class="person_name" id="person_name_id">luoyiming</span><em class="zt_txt">-在线</em>
  </div>
  <div class="tool_s_box_content">
    <div class="tool_s_b_l">
      <div class="tool_talkhistory">
        <ul>
          <li class="item_li">
            <p class="p_tit"><span class="talk_name">旗舰店商家</span><span class="talk_time">(2014-12-14 12:51:27):</span></p>
            <p class="p_content">亲，欢迎光临【点点滴滴】。进店不要错过我们的美味手工硬质沙琪玛哦，一份17.5元，两份包邮</p>
          </li>
          <li class="item_li">
            <p class="p_tit"><span class="talk_name">luoyiming</span><span class="talk_time">(2014-12-14 12:51:27):</span></p>
            <p class="p_content">你好，我想知道这个价格是多少？</p>
          </li>
          <li class="item_li">
            <p class="p_tit"><span class="talk_name">旗舰店商家</span><span class="talk_time">(2014-12-14 12:51:27):</span></p>
            <p class="p_content">亲，欢迎光临【点点滴滴】。进店不要错过我们的美味手工硬质沙琪玛哦，一份17.5元，两份包邮</p>
          </li>
          <li class="item_li">
            <p class="p_tit"><span class="talk_name">luoyiming</span><span class="talk_time">(2014-12-14 12:51:27):</span></p>
            <p class="p_content">你好，我想知道这个价格是多少？</p>
          </li>
        </ul>
      </div>
      <div class="tool_talkbar"><span class="talk_more"><a>聊天记录</a></span><a href="javascript:void(0);" id="biaoqing_more_btn"><i class="i_ico i_biaoqing"></i></a>
        <div class="biaoqing_box" id="biaoqing_box">
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/1.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/2.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/3.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/4.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/5.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/6.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/7.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/8.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/9.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/10.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/11.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/12.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/13.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/14.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/15.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/16.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/17.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/18.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/19.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/20.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/21.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/22.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/23.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/24.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/25.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/26.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/27.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/28.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/29.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/30.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/31.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/32.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/33.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/34.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/35.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/36.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/37.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/38.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/39.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/40.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/41.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/42.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/43.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/44.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/45.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/46.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/47.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/48.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/49.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/50.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/51.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/52.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/53.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/54.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/55.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/56.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/57.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/58.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/59.png" /></i>
			<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/60.png" /></i>
		</div>
      </div>
      <div class="tool_talkinput" id="tool_talkinput">
        <div contentEditable ="true" class="textarea" data-content="" onclick="javascript:getCursortPosition(this)"></div>
        <!-- <textarea></textarea>--> 
      </div>
      <div class="tool_talkfoot"> <span><a href="#">有问题请联系整案客服</a></span> <a href="javascript:void(0);" class="tool_talkbar goto_talk" id="goto_talk_btn">发送</a> </div>
    </div>
    <div class="tool_s_b_r">
      <div class="tool_s_b_r_box">
        <p><b>jingqixin</b><br />
          来自：襄阳<br />
          性别：保密<br />
          注册时间：2010-6-12<br />
          上次登录：2014-12-24<br />
          卖家：卖家信用积分<br />
          买家：买家信用积分</p>
      </div>
    </div>
  </div>
</div>

<!--艺名聊天结束--> 

<!--信息展示-->
<div class="hs_newslist">
  <div class="personlist_title" id="HsNew_title_top" onselectstart="return false">
	  <a href="javascript:void(0);" class="i_ico close_btn close_btn2" id="HsNewlist_box_close"></a><a href="javascript:void(0);" class="i_ico close_btn quanbing_btn" ></a><a href="javascript:void(0);" class="i_ico close_btn fangxiao_btn" ></a>
	  <span class="person_name" ID="HsNewsType"></span>
	  <span class="xiaoxi_title" id="HsNew_title"></span>
  </div>
  <div class="hs_new_box">
    <div class="hs_new_type">
      <ul id="HsNewType_ul">
        <li><a class="select_a">业务消息</a></li>
        <li><a>艺名消息</a></li>
      </ul>
    </div>
    <div class="hs_new_content">
      
      <div class="hs_new_content_box">
      <div class="p20 hsk_bd_html">
		<div class="hsk_title"><span class="f16 fb cl-red2"></span><span class="time_span fr cl-gray">2015-03-19 15:15</span></div>
			<div class="hsk_bd_box p20">
			
			</div>
		</div>
      </div>
      
    </div>
    <div class="hs_new_list">
      <div id="HsNewList">
        <div class="type_title">艺名业务列表</div>
        <ul>
         
        </ul>
        <ul class="none">
         
        </ul>
      </div>
      <div class="hshewlist_page"><p>
      <a class="pageFirst"><i class="i_ico"></i>首页</a>
      <a class="pagePrev"><i class="i_ico"></i>上一页</a>
      <a class="pageNext">下一页<i class="i_ico"></i></a>
      <a class="pageLast">尾页<i class="i_ico"></i></a>
      </p></div>
    </div>
  </div>
</div>
<script>
//数据 分页

var newlist1={
	id:Array("1","2","3","4","5","6","7","8","9","10","11","12","13","14","15"),
	tiele:Array("1新闻标题","2新闻标题","3新闻标题","4新闻标题","5新闻标题","6新闻标题","7新闻标题","8新闻标题","9新闻标题","10新闻标题","11新闻标题","12新闻标题","13新闻标题","14新闻标题","15新闻标题"),
	content:Array("1内容","2内容","3内容","4内容","5内容","6内容","7内容","8内容","9内容","10内容","11内容","12内容","13内容","14内容","15内容")
	}
	
var newlist2={
	id:Array("1","2","3","4","5","6","7","8","9","10","11","12","13","14","15"),
	tiele:Array("1艺名消息","2艺名消息","3艺名消息","4艺名消息","5艺名消息","6艺名消息","7艺名消息","8艺名消息","9艺名消息","10艺名消息","11艺名消息","12艺名消息","13艺名消息","14艺名消息","15艺名消息"),
	content:Array("1内容","2内容","3内容","4内容","5内容","6内容","7内容","8内容","9内容","10内容","11内容","12内容","13内容","14内容","15内容")
	}



var newlist=eval("newlist1");

function Datatypelist(send){
	newlist=(eval(send));
	Datalist(count,zise,page);
}

var count=newlist.id.length-1;//总条数
var zise=4;//分页条数
var page=0;//页面索引

Datalist(count,zise,page);

//首页
$(".pageFirst").click(function(){
	page=0;
	Datalist(count,zise,page);
});
//上一页
$(".pagePrev").click(function(){
	page--;
	if(page<0)
	{page=0}
	Datalist(count,zise,page);
	});

//下一页
$(".pageNext").click(function(){
	page++;
	if(page>count/zise)
	{
		page=parseInt(count/zise);
		}
	Datalist(count,zise,page);
	});

//下一页
$(".pageLast").click(function(){
	page=parseInt(count/zise);
	Datalist(count,zise,page);
	});
	
	
//分页函数
function Datalist(count,zise,page)
{
	
	if(page==0)
	{
		$(".pageFirst").addClass("pageFirst2");
		$(".pagePrev").addClass("pagePrev2");
	}
	else
	{
		$(".pageFirst").removeClass("pageFirst2");
		$(".pagePrev").removeClass("pagePrev2");
	}
	if(page==parseInt(count/zise))
	{
		$(".pageNext").addClass("pageNext2");
		$(".pageLast").addClass("pageLast2");
	}
	else
	{
		$(".pageNext").removeClass("pageNext2");
		$(".pageLast").removeClass("pageLast2");
	}
	
	var newliststr="";
	//alert(newlist.id.length);
	for(var i=page*zise; i<page*zise+zise;i++)
	{
		if(newlist.id[i]!=undefined)
		{
			newliststr+="<li><a href=\"javascript:void(0);\" data-id='"+newlist.id[i]+"'>"+newlist.tiele[i]+"</a></li>";
		}
	}
	$("#HsNewList ul").first().html(newliststr);
}
//luoyiming


    var ww=$(window).width();
	var wh=$(window).height();
	
	//返回顶部
	$("#gototop_btn").click(function(){
		$("html,body").animate({scrollTop:"0px"},400);
		});
	
	//艺名业务
	
	$(".quanbing_btn").click(function(){
		$(".hs_newslist").css({"width":ww-2,"height":wh,"top":0,"left":0});
		$(".hs_new_content").width(ww-4-141-230);
		$(".hs_new_type").height(wh-32);
		$(".hs_new_list").height(wh-32);
		$(".hs_new_content_box").height(wh-32);
		$(this).hide();
		$(".fangxiao_btn").show();
	});
	
	$(".fangxiao_btn").click(function(){
		var lt=ww*0.5-490; //326为聊天框宽度的一半  
			var tp=wh*0.5-215;//210为聊天框高度的一半
			if(tp<0)
			{tp=0}
		$(".hs_newslist").css({"width":980,"height":432,"top":lt,"left":tp});
		$(".hs_new_content").width(608);
		$(".hs_new_type").height(400);
		$(".hs_new_list").height(400);
		$(".hs_new_content_box").height(380);
		$(this).hide();
		$(".quanbing_btn").show();
	});
	//
	$("#HsNewType_ul li a").click(function(){
		 page=0;
		Datatypelist("newlist"+($(this).parent("li").index()+1));
		//alert(newlist2);
		$(this).addClass("select_a").parent().siblings("li").children("a").removeClass("select_a");
		var num=$(this).parent("li").index();
		//$("#HsNewList ul").hide().eq(num).show();
		$("#HsNewList .type_title").text($(this).text()+"列表");
		$("#HsNewsType").text($(this).text()+"-");
		$("#HsNew_title").text($("#HsNewList ul").eq(num).find("li").first().children("a").text());
		});
	
	//右边新闻列表
	$(document).on("click","#HsNewList ul li a",function(){
		
		$(this).addClass("now_news").parent().siblings("li").children("a").removeClass("now_news");
		$("#HsNew_title").text($(this).text());
		var listid=$(this).parent().index();
		$(".hsk_title .f16").html($(this).text());
		$(".hsk_bd_box").html(newlist1.content[listid]);
		});
		
	//关闭新闻框
	$("#HsNewlist_box_close").click(function(){
		$(".hs_newslist").hide();
		});	
	function HsNewLook()
		{
			var lt=ww*0.5-490; //326为聊天框宽度的一半  
			var tp=wh*0.5-215;//210为聊天框高度的一半
			if(tp<0)
			{tp=0}
			$(".hs_newslist").show();
			$(".hs_newslist").css({"left":lt+"px","top":tp+"px"});
			$("#HsNewsType").text($("#HsNewType_ul li a").first().text()+"-");
		    $("#HsNew_title").text($("#HsNewList ul").first().find("li").first().children("a").text());
			$(".hsk_title .f16").html(newlist1.tiele[0]);
			$(".hsk_bd_box").html(newlist1.content[0]);
		}
	//
	var nx,ny;
	var ndraging=false;
	$("#HsNew_title_top").mousedown(function(e){
		ndraging=true;
		e=e||window.event;
		nx=e.clientX-$(".hs_newslist").position().left;
		ny=e.clientY-$(".hs_newslist").position().top;
		return false;
		});
	
	//表情区域
	$("#biaoqing_more_btn").click(function(){
		
		$("#biaoqing_box").show();
	});
	
	$("#tool_talkinput .textarea").click(function(){
		$("#biaoqing_box").hide();
		});	
	$("#biaoqing_box i").click(function(){
		var num=$(this).index()+1;
		
		//var clsname=$(this).attr("class");
		var str=$(this).html();
		var textareastr=$("#tool_talkinput .textarea").html();
		
		$("#tool_talkinput .textarea").html(textareastr+str);
		$("#biaoqing_box").hide();
	});	
		//表情区域
		
	//拖动
	var msx, msy;
	 var dragging = false;

	$("#personlist_title_top").mousedown(function(e){
		dragging = true;
		e = e||window.event; 
		 msx=e.clientX-$(".tool_speak_box").position().left;
		 msy=e.clientY-$(".tool_speak_box").position().top;
		 return false;
		}).mouseup(function(e){
		dragging = false;
		e = e||window.event;
		msx=e.clientX-$(".tool_speak_box").position().left;
		msy=e.clientY-$(".tool_speak_box").position().top;
		return false;

	});
		
	document.onmousemove = function(e)
     {
		 	if(ndraging)
			{	
				 var xx=e.clientX-nx;
				 var yy=e.clientY-ny;
				 if(xx<-960)
				 {
					 xx=-960;
				 }
				 if(xx>ww-30)
				 {
					 xx=ww-30;
				 }
				 if(yy<0)
				 {
					 yy=0;
				 }
				 if(yy>wh-30)
				 {
					yy=wh-30;
				 }
				 $(".hs_newslist").css({"left":xx+"px","top":yy+"px"});
				 return false;
			}
			if (dragging) {
				 var xx=e.clientX-msx;
				 var yy=e.clientY-msy;
				 if(xx<-562)
				 {
					 xx=-562;
				 }
				 if(xx>ww-30)
				 {
					 xx=ww-30;
				 }
				 if(yy<0)
				 {
					 yy=0;
				 }
				 if(yy>wh-30)
				 {
					yy=wh-30;
				 }
				 $(".tool_speak_box").css({"left":xx+"px","top":yy+"px"});
				return false;
			//alert("e.clientX="+e.clientX);
			 }
     };
/*	$(document).mouseup(function(e) {
                dragging = false;
				ndraging = false;
                $(".tool_speak_box")[0].releaseCapture();
				$(".hs_newslist")[0].releaseCapture();
                e.cancelBubble = true;
            })*/

	//层级
	$(".tool_speak_box").click(function(){
		$(this).css({"z-index":99});
		$(".hs_newslist").css({"z-index":98});
		});
	$(".hs_newslist").click(function(){
		$(this).css({"z-index":99});
		$(".tool_speak_box").css({"z-index":98});
		});

	//拖动 结束
	
	//用户区域
	$(".speak_person_list_box").show();
	$("#speak_person_hd").show();
	$("#speak_person_hd").animate({"bottom":0,"left":0},400)
	$("#person_show_btn").click(function(){
		if($(".speak_person_list_box").is(":hidden"))
		{
			$(".speak_person_list_box").show();
			$("#speak_person_hd").show();
			$("#speak_person_hd").animate({"bottom":0,"left":0},400);
		}
		else
		{
			$("#speak_person_hd").animate({"bottom":-302+"px","left":0},400,function(){$(".speak_person_list_box").hide();});

        }
			
			
		});
		
	$("#close_personlist_btn").click(function(){
		$("#speak_person_hd").animate({"bottom":-302+"px","left":0},400,function(){$(".speak_person_list_box").hide();});
		});
		
	$(".speak_personlist ul li").click(function(){
			var person=$(this).find("span").text();
			$("#person_name_id").text(person);
			var lt=ww*0.5-326; //326为聊天框宽度的一半  
			var tp=wh*0.5-210;//210为聊天框高度的一半
			if(tp<0)
			{tp=0}
			$(".tool_speak_box").show();
			$(".tool_speak_box").css({"left":lt+"px","top":tp+"px"});
			
			});
	//用户区域
	
	//聊天区域
	$("#tool_speak_box_close").click(function(){
		
		$(this).parents(".tool_speak_box").hide();
		
		});
		
	//字符串替换为图片函数
	function Replace_img(str)
	{
		var num=str.replace(/\b(0+)/gi,"");
		//var reg =new RegExp("\\[(.| )+?\\]","igm");
		var s = '<i class="i_biaoqing"><img src="/assets/chat/images/biaoqing/'+num+'.png"></i>';
		//var reg = /\[[^\)]+\]/g;
		return s;
		
	}
		
	
		
	$("#goto_talk_btn").click(function(){
		
		var imgnum;
	    var talktxt=$("#tool_talkinput").find(".textarea").html();
		var myname=$("#person_name_id").text();
		var zz=/<IMG src=\"([^\"]*?)\">/gi;
		//var reg =new RegExp("\\<(.| )+?\\>","igm");
		var arr= talktxt.match(zz);
		
		if(arr!=null)
		{
			for(i=0;i<arr.length;i++)
			{
				
				var src="src=\"";
				var name=".png";
				var s=arr[i].indexOf(src)+5;
				var s2=arr[i].indexOf(name)+4;
				var fullpath = arr[i].substring(s,s2);//talktxt2.replace(arr[i],"["+i+"]");
                var filename = fullpath.match(/\/(\w+\.(?:png|jpg|gif|bmp))$/i)[1];
				//var test = /\/([^\/]*?\.(jpg|bmp|gif))"\/>/;
				var num=filename.replace(".png","");
				if(num.toString().length==1)
				{
				   num="00"+num;
				}
				else if(num.toString().length==2)
				{
					num="0"+num;
				}
				var talktxt2="["+num+"]";
				talktxt=talktxt.replace(arr[i],talktxt2);
			}
			
			//alert(talktxt);//图片转成数字数据
		}
		else
		{
			//alert(talktxt);
		}
	
	
     if($("#tool_talkinput .textarea").html()!="")
	   {
		 talktxt=$("#tool_talkinput").find(".textarea").html();
		 var talk_li= "<li class=\"item_li\">"+
           "<p class=\"p_tit\"><span class=\"talk_name\">"+myname+"</span><span class=\"talk_time\">"+currentTime()+"         </span></p>"+
           "<p class=\"p_content\">"+talktxt+"</p></li>";
		
		$(".tool_talkhistory ul").append(talk_li);
		$("#tool_talkinput .textarea").html("");
	  }
		
	   $(".tool_talkhistory").scrollTop($(".tool_talkhistory ul").height());
	});
		
		//当前时间
	 function currentTime(){
           var d = new Date(),str = '';
           str += d.getFullYear()+'-';
           str  += d.getMonth() + 1+'-';
           str  += d.getDate()+' ';
           str += d.getHours()+':'; 
           str  += d.getMinutes()+':'; 
           str+= d.getSeconds()+''; 
           return str;
           }
	function sortObjDesc(obj) {
		var arr = [];
		for (var i in obj) {
			arr.push([obj[i],i]);
		};
		arr.reverse();
		var len = arr.length;
		var obj = {};
		for (var i = 0; i < len; i++) {
			obj[arr[i][1]] = arr[i][0];
		}
		return obj;
	}
	//

</script>
<script>
	var formatDateTime = function (date) {
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		m = m < 10 ? ('0' + m) : m;
		var d = date.getDate();
		d = d < 10 ? ('0' + d) : d;
		var h = date.getHours();
		h=h < 10 ? ('0' + h) : h;
		var minute = date.getMinutes();
		minute = minute < 10 ? ('0' + minute) : minute;
		var second=date.getSeconds();
		second=second < 10 ? ('0' + second) : second;
		return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;
	};
	var conn = new WebIM.connection({
		https: WebIM.config.https,
		url: WebIM.config.xmppURL,
		isAutoLogin: WebIM.config.isAutoLogin,
		isMultiLoginSessions: WebIM.config.isMultiLoginSessions
	});
    var msgU_hx = '';
	var options = {
		apiUrl: WebIM.config.apiURL,
		user: "<?php echo ($kefu["hx_username"]); ?>",
		pwd: "<?php echo ($kefu["hx_password"]); ?>",
		appKey: WebIM.config.appkey,
		success:function(e){
			console.log('登录成功！');
		},
		error: function(){
			console.log("登录失败！")
		}
	};
	conn.open(options);
	/*判断本地有没有这个数据*/
	function getMsgArr() {
		if (localStorage.getItem("msgArr")) { //有，将数据赋值给对象，
			msgArr = JSON.parse(localStorage.getItem("msgArr"));
			msgArr = sortObjDesc(msgArr);
			for (var i in msgArr) {
				$("#MessageerList").append('<li onclick="msgUserListClick(\'' + msgArr[i]['hx_username'] + "','" + msgArr[i]['img'] + "','" + msgArr[i]['username'] + '\')"><img style="width:20px;height:20px;" src=' + msgArr[i]['img'] + ' /><span class="ml2">' + msgArr[i]['username'] + '</span></li>');
			}
		} else {  //没有，新建一个对象
			msgArr = {};
		}
	}
	getMsgArr();
	//mLen = msgArr.hx_username["msgData"].length;
	/*用户列表点击事件*/
	function msgUserListClick(hx_username,img,username){ //点击的用户的环信用户名,头像,用户名
		var msgU_hx = hx_username;
		var u_txImg = img;
		var u_name = username;
		$("#append").empty();//清空DOM节点
		var mLen = msgArr[hx_username]["msgData"].length;
		for(var i=0;i<mLen;i++){
			var onDate = new Date();
			//$scope.onNewsTime = $filter('date')($scope.onDate, "yyyy-MM-dd hh:mm:ss");
			if(msgArr[hx_username]["msgData"][i].to == hx_username){ //自己发送的信息
				console.log(msgArr[hx_username]["msgData"][i].to);

			}else{ //收到的消息

			}
		}
	}
	/*删除聊天*/
	 function delMesListClick(hx_username){ //环信用户名
		delete msgArr[hx_username];
		var msgU_hx = null;
		localStorage.setItem("msgArr",JSON.stringify(msgArr));//将消息存储到本地
	}
	/*根据环信账号获取信息*/
	function getUserInfo(hx_username){
		$.ajax({
			url:"/api.php/Index/get_user_info",
			type: 'POST',
			data: {hx_username:hx_username},// 环信用户名
			async: true,
			cache: false,
			processData: true,
			dataType:"JSON",
			success: function (data) {
				if (data['status'] == 'ok') {
					//hxuInfo = data['data'];
					localStorage.setItem("msgArr",JSON.stringify(msgArr));//将消息存到本地
					return  data['data'];
				}else if (data['status'] == 'error'){
					return false;
				}
			},
			error:function(data){
			}
		})
	}
	/*环信回调函数*/
	conn.listen({
		/*连接成功回调*/
		onOpened:function(message){
			console.log("连接成功")
// 如果config.js里面isAutoLogin设置为false，那么必须手动设置上线，否则无法收消息
			conn.setPresence();//手动上线
		},
		/*连接失败回调*/
		onClosed:function(message){
			console.log("连接失败");
			window.location.href = window.location.href;
		},
		/*接收文本消息的回调函数*/
		onTextMessage:function(message){
			if(message.type=='chat'&&message.to=="<?php echo ($kefu["hx_username"]); ?>"){ //单聊
				/*判断对象中是否有这个元素*/
				if(msgArr[message.from]){
					msgArr[message.from].msgData.push(message);
					localStorage.setItem("msgArr",JSON.stringify(msgArr));//将消息存储到本地
				}else{
					/*根据环信用户名查找用户信息*/
					var hxuInfo = getUserInfo
					msgArr[message.from] = {
						hx_username:message.from,
						user_id:hxuInfo.user_id,
						img:hxuInfo.img,
						username:hxuInfo.username,
						msgData:[message]
					};
				};
				if(msgU_hx==message.from){
					console.log(1);
					var onDate = new Date();
					var onNewsTime = formatDateTime;
					//appendDom.append('<div class="newsListBox pt20"><div class="newsTime f12 col_b7 mb5">'+ $scope.onNewsTime + '</div><div class="clearfix"><div class="fl chat_userImg">< img src="'+ $scope.msgArr[message.from].img +'" alt=""></div><div class="fl newsTxtBox bor back_fff f12">'+ message.data +'</div></div></div>');
				}
			}
			//chat_info.scrollTop = newsBottom.offsetTop; //消息显示在最底部
		},
		/*本机网络掉线*/
		onOffline: function () {
//建议写一个断线重连的处理
		},
		/*失败回调*/
		onError : function(message){
			console.log(message)
		}
	})
//	$scope.$on('newsFinished', function (ngRepeatFinishedEvent) {
//		console.log('消息加载完毕');
//		$scope.chat_info.scrollTop =$scope.newsBottom.offsetTop; //消息显示在最底部
//	});
	/*单聊发送文本消息*/
	chat_info = document.getElementById("chat_info");//聊天框
	//newsBox = angular.element("#newsBox");//聊天信息内容框
	newsBottom = document.getElementById("newsBottom");
	sendNewsObj = {};
	 function sendPrivateText(n) { // n消息内容
		var id = conn.getUniqueId();             // 生成本地消息id
		var msg = new WebIM.message('txt', id);      // 创建文本消息
		var sendDate = new Date();
		var newsTime = formatDateTime;
		var txtMsg = n;
		msg.set({
			msg: txtMsg,     // 消息内容
			to: msgU_hx,   // 接收消息对象（环信用户id/环信用户名）
			roomType: false,
			ext :{ //用户自扩展的消息内容（群聊用法相同）
				username: "<?php echo ($kefu["username"]); ?>",
				newsTime: formatDateTime
			},
			success: function () {
				console.log('成功发送消息');
				sendNewsObj = {
					data:txtMsg,
					from:"<?php echo ($ke["hx_username"]); ?>",
					to:msgU_hx,
				}
				msgArr[msgU_hx].msgData.push(sendNewsObj);//将消息加入到数组中
				localStorage.setItem("msgArr",JSON.stringify(msgArr));//将消息存储到本地
				//appendDom.append('<div class="newsListBox pt20"><div class="newsTime f12 col_b7 mb5">'+ $scope.newsTime + '</div><div class="clearfix"><div class="fr chat_userImg">< img src="'+ $scope.userInfo.img +'" alt=""></div><div class="fr newsTxtBox bor back_fff f12">'+ $scope.txtMsg +'</div></div></div>');
				chat_info.scrollTop =$scope.newsBottom.offsetTop; //消息显示在最底部
			},
			error:function(){
				console.log("消息发送失败")
			}
		});
		nesTxt = null;
		msg.body.chatType = 'singleChat';
		conn.send(msg.body);
	};
	 function newSend(n){
		if(n == null){
			return false;
		}
		sendPrivateText(n);
	}

</script>

</div>
</body>
<?php echo W('Public/foot');?>
</html>