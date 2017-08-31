/*
* @Author: hepeng
* @Date:   2017-08-28 10:57:26
* @Last Modified by:   hepeng
* @Last Modified time: 2017-08-29 12:04:10
*/
function plays(param) {
	var player = document.getElementById("player");
	test(param);
	if(param) {
		player.play();
	}else {
		player.pause();
	}
}
function test(param){
	console.log(param);
	$('.spanContant').remove()
	if(param+""==="true"){
		alert(1);
		$('.VideoBody').append('<span class="spanContant" onClick="plays(false)"><</span>')
	}else{
		alert(2);
		$('.VideoBody').append('<span class="spanContant" onClick="plays(true)"><</span>')
	}
}

function geturl(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }//geturl('video')
$(function(){
	$.ajax({
		url: '../../api.php/Home/video_detail',
		//url: 'http://bs.tstmobile.com/api.php/Home/video_detail',
		type: 'POST',
		dataType: 'JSON',
		data: {video_id:geturl('video_id')},
		success:function(data){
			console.log(data);
			var url = data.data.url;
			$('.VideoPlayer').append('<source src='+url+' type="video/mp4">');
			if(data.data.sex === 1){
				$('.HeadDetail').append('<img src='+data.data.img+' class="headImg"><p class="greatContant">'+data.data.username+'</p><img src="images/manImg.png" class="manImg">');
			}else{
				$('.HeadDetail').append('<img src='+data.data.img+' class="headImg"><p class="greatContant">'+data.data.username+'</p><img src="images/manImg.png" class="manImg">');
			}
			$('.DetailContant').prepend('<div class="DetailTitle">'+data.data.title+'</div><p class="p_contant">'+data.data.content+'</p>');
			var dataContant = [];
			$('.zanData').html(data.data.zan);
			$('.VideoData').html(data.data.watch_nums);
			for(var i=0;i<data.data.comment.length;i++){
				for(var j=0;j<data.data.comment[i].response.length;j++){
					dataContant.push('<div class="InformationContant"><span>'+data.data.comment[i].response[j].username+'</span> : '+data.data.comment[i].response[j].content+'</div>');
				};
				var dataContantJoin = dataContant.join('');
				$('.commentaries').append('<div class="commentariesContant"><div class="headContantShow"><img src='+data.data.comment[i].img+' class="headImgMin"><p class="greatContantMin">'+data.data.comment[i].username+'</p><img src="images/manImg.png" class="manImgMin"></div><div class="headContant"><p class="commentariesPosted">'+data.data.comment[i].content+'</p><div class="TimeContant">'+data.data.comment[i].intime+'<span class="revertContant">回复</span></div><div class="revertContantInformation">'+dataContantJoin+'</div></div></div>')

			}


			/**/
		},
		error:function(error){
			alert(error)
			console.log(error);
		}
	})
	
})