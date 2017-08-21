//切换效果
function bookmarksite(title, url){
if (document.all){window.external.AddFavorite(url, title);}
else if (window.sidebar){window.sidebar.addPanel(title, url, "")}
}
$(document).ready(function() {
   //导航距离屏幕顶部距离
	var _defautlTop = jQuery(".menu").offset().top; 
	//导航距离屏幕左侧距离
	var _defautlLeft = jQuery(".menu").offset().left;
	//导航默认样式记录，还原初始样式时候需要
	var _position = jQuery(".menu").css('position');
	var _top = jQuery(".menu").css('top');
	var _left = jQuery(".menu").css('left');
	var _zIndex = jQuery(".menu").css('z-index');
	
	if(document.getElementById("quick") != null){
		var _defautlBanner = jQuery(".bannerbg").offset().top; 
	}

  
  

  //首页专家效果
  if(document.getElementById("capslide_img_cont1") != null){
  $("#capslide_img_cont1").capslide({
	  caption_color	: 'white',
	  border			: '',
	  showcaption	    : true
  });
  $("#capslide_img_cont2").capslide({
	  caption_color	: 'white',
	  border			: '',
	  showcaption	    : true
  });
  $("#capslide_img_cont3").capslide({
	  caption_color	: 'white',
	  border			: '',
	  showcaption	    : true
  });
  $("#capslide_img_cont4").capslide({
	  caption_color	: 'white',
	  border			: '',
	  showcaption	    : true
  });
  $("#capslide_img_cont5").capslide({
	  caption_color	: 'white',
	  border			: '',
	  showcaption	    : true
  }); 
  }
  if(document.getElementById("capslide_img_cont6") != null){
  $("#capslide_img_cont6").capslide({
	  caption_color	: '#white',
	  border			: '',
	  showcaption	    : true
  });  
  $("#capslide_img_cont7").capslide({
	  caption_color	: '#white',
	  border			: '',
	  showcaption	    : true
  });  
  $("#capslide_img_cont8").capslide({
	  caption_color	: '#white',
	  border			: '',
	  showcaption	    : true
  });  
  $("#capslide_img_cont9").capslide({
	  caption_color	: '#white',
	  border			: '',
	  showcaption	    : true
  }); 
  }
  //结束

});

/*end*/