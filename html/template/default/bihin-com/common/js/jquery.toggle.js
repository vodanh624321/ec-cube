$(function(){
	$('.toggle dt').click(function(){
		if($(this).hasClass("on")){
			$(this).next().stop().slideUp(200);
			$(this).removeClass("on");
		}else{
			$(this).next().stop().slideDown(200);
			$(this).addClass("on");
		}
		
	});
});
$(function(){
	$('.customBtn p').click(function(){
		if($(this).hasClass("on")){
			$(this).next().stop().slideUp(200);
			$(this).removeClass("on");
		}else{
			$(this).next().stop().slideDown(200);
			$(this).addClass("on");
		}
		
	});
});
$(function(){
	$('.navbarPanel .navbarTit.hasChild').click(function(){
		if($(this).hasClass("on")){
			$(this).next().stop().slideUp(200);
			$(this).removeClass("on");
		}else{
			$(this).next().stop().slideDown(200);
			$(this).addClass("on");
		}
		
	});
});
$(function(){
	$('.navbarBox .navpullDown').click(function(){
		if($(this).hasClass("on")){
			$(this).next().stop().slideUp(200);
			$(this).removeClass("on");
		}else{
			$(this).next().stop().slideDown(200);
			$(this).addClass("on");
		}
		
	});
});
$(function(){
	$('.productCond .productCondBtn').click(function(){
		if($(this).hasClass("on")){
			$(this).next().stop().slideUp(200);
			$(this).removeClass("on");
		}else{
			$(this).next().stop().slideDown(200);
			$(this).addClass("on");
		}
		
	});
});
$(function(){
	$('#current').click(function(){
		if($(this).hasClass("on")){
			$(this).next().stop().slideUp(200);
			$(this).removeClass("on");
		}else{
			$(this).next().stop().slideDown(200);
			$(this).addClass("on");
		}
		
	});
});



















