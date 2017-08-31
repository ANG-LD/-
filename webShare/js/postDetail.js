/*
* @Author: hepeng
* @Date:   2017-08-28 10:57:26
* @Last Modified by:   hepeng
* @Last Modified time: 2017-08-29 16:00:19
*/
function geturl(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }//geturl('video')
$(function(){
	$.ajax({
		url: 'http://bs.tstmobile.com/api.php?m=Api&c=Topical&a=posts_view',
		type: 'POST',
		dataType: 'JSON',
		data: {post_id:geturl('post_id')},
		success:function(data){
			$(".greatContantMin").html(data.data.username);
			$(".commentariesPosted").html(data.data.content);
			$(".TimeContant").html(data.data.date_value);
			$(".headImgMin").attr("src",data.data.head_img);
			if(data.data.sex==2){
                $(".manImgMin").attr("src",'images/manImg.png');
			}else{
                $(".manImgMin").attr("src",'images/ssjg_woman@2x.png');
			}
			var img = data.data.img;
			if(img != undefined) {
                for (var i = 0; i < img.length; i++) {
                    $(".revertContantInformation").append('<div class="InformationContant"><img src="'+img[i].img+'"class="informatingImg"> </div>');
                }
            }
			var dataContant = [];
			for(var i=0;i<data.data.comment.length;i++){
				for(var j=0;j<data.data.comment[i].response.length;j++){
					dataContant.push('<div class="InformationContant"><span>'+data.data.comment[i].response[j].username+'</span> : '+data.data.comment[i].response[j].content+'</div>');
				};
				console.log(dataContant);
				var dataContantJoin = dataContant.join('');
				if (data.data.comment[i].response.sex === 1) {
					$('.commentaries').append('<div class="commentariesContant"><div class="headContantShow"><img src='+data.data.comment[i].img+' class="headImgMin"><p class="greatContantMin">'+data.data.comment[i].username+'</p><img src="images/ssjg_woman@2x.png" class="manImgMin"></div><div class="headContant"><p class="commentariesPosted">'+data.data.comment[i].content+'</p><div class="TimeContant">'+data.data.comment[i].intime+'<span class="revertContant">回复</span></div><div class="revertContantInformation">'+dataContantJoin+'</div></div></div>')
				}else{
					$('.commentaries').append('<div class="commentariesContant"><div class="headContantShow"><img src='+data.data.comment[i].img+' class="headImgMin"><p class="greatContantMin">'+data.data.comment[i].username+'</p><img src="images/manImg.png" class="manImgMin"></div><div class="headContant"><p class="commentariesPosted">'+data.data.comment[i].content+'</p><div class="TimeContant">'+data.data.comment[i].intime+'<span class="revertContant">回复</span></div><div class="revertContantInformation">'+dataContantJoin+'</div></div></div>')
				}
			}
		},
		error:function(error){
			alert(error)
			console.log(error);
		}
	})
	
})