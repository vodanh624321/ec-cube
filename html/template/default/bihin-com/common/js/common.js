/**
 * common.js
 *
 *  version --- 3.6
 *  updated --- 2011/09/06
 */


/* !stack ------------------------------------------------------------------- */
/* 全てのスマホで幅320px(iphone)相当に見えるようにdpiを調整 */
jQuery(document).ready(function($) {
	pageScroll();
	rollover();
	common();
});

$(function() { //IE8のalpha使用時に発生の黒枠を消す
    if(navigator.userAgent.indexOf("MSIE") != -1) {
        $('img').each(function() {
            if($(this).attr('src').indexOf('.png') != -1) {
                $(this).css({
                    'filter': 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' +
                    $(this).attr('src') +
                    '", sizingMethod="scale");'
                });
            }
        });
    }
});	

/* !isUA -------------------------------------------------------------------- */
var isUA = (function(){
	var ua = navigator.userAgent.toLowerCase();
	indexOfKey = function(key){ return (ua.indexOf(key) != -1)? true: false;}
	var o = {};
	o.ie      = function(){ return indexOfKey("msie"); }
	o.fx      = function(){ return indexOfKey("firefox"); }
	o.chrome  = function(){ return indexOfKey("chrome"); }
	o.opera   = function(){ return indexOfKey("opera"); }
	o.android = function(){ return indexOfKey("android"); }
	o.ipad    = function(){ return indexOfKey("ipad"); }
	o.ipod    = function(){ return indexOfKey("ipod"); }
	o.iphone  = function(){ return indexOfKey("iphone"); }
	return o;
})();
/* !init Smart Devices ------------------------------------------------------*/ 
(function (){
	var parentNode = document.getElementsByTagName('head')[0];
	var viewport = {
		withzoom:'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no',
		android : 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no',
		ipad    : 'width=1200  user-scalable=no',
		//iphonescale1  : 'width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0'
		iphone  : 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'
	}
	meta = document.createElement('meta');
	meta.setAttribute('name','viewport');

	if( isUA.android() ){
		meta.setAttribute('content',viewport.android);
		parentNode.appendChild(meta);
	}else if( isUA.ipad() ){
		meta.setAttribute('content',viewport.ipad);
		parentNode.appendChild(meta);
	}else if( isUA.ipod() || isUA.iphone() ){
		meta.setAttribute('content',viewport.iphone);
		parentNode.appendChild(meta);
		window.addEventListener('load', function(){ setTimeout(scrollTo, 100, 0, 1);}, false);
	}else{
	}
})();

/* !rollover ---------------------------------------------------------------- */
var rollover = function(){
	var suffix = { normal : '_no.', over   : '_on.'}
	$('a.over, img.over, input.over').each(function(){
		var a = null;
		var img = null;

		var elem = $(this).get(0);
		if( elem.nodeName.toLowerCase() == 'a' ){
			a = $(this);
			img = $('img',this);
		}else if( elem.nodeName.toLowerCase() == 'img' || elem.nodeName.toLowerCase() == 'input' ){
			img = $(this);
		}

		var src_no = img.attr('src');
		var src_on = src_no.replace(suffix.normal, suffix.over);

		if( elem.nodeName.toLowerCase() == 'a' ){
			a.bind("mouseover focus",function(){ img.attr('src',src_on); })
			 .bind("mouseout blur",  function(){ img.attr('src',src_no); });
		}else if( elem.nodeName.toLowerCase() == 'img' ){
			img.bind("mouseover",function(){ img.attr('src',src_on); })
			   .bind("mouseout", function(){ img.attr('src',src_no); });
		}else if( elem.nodeName.toLowerCase() == 'input' ){
			img.bind("mouseover focus",function(){ img.attr('src',src_on); })
			   .bind("mouseout blur",  function(){ img.attr('src',src_no); });
		}

		var cacheimg = document.createElement('img');
		cacheimg.src = src_on;
	});
};
/* !pageScroll -------------------------------------------------------------- */
var pageScroll = function(){
	jQuery.easing.easeInOutCubic = function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	}; 
	$('a.scroll, .scroll a').each(function(){
		$(this).bind("click keypress",function(e){
			e.preventDefault();
			var target  = $(this).attr('href');
			var targetY = $(target).offset().top;
			var parent  = ( isUA.opera() )? (document.compatMode == 'BackCompat') ? 'body': 'html' : 'html,body';
			$(parent).animate(
				{scrollTop: targetY },
				400
			);
			return false;
		});
	});
	$('.pageTop a').click(function(){
		$('html,body').animate({scrollTop: 0}, 'slow','swing');
		return false;
	});
}



/* !common --------------------------------------------------- */
var common = (function(){
	
	
	
	
	$('#gNavi .parent').hover(function(){
		if($(this).has('.pulldownmenu'))
			$(this).find('.parent_a').addClass('on');
			$(this).find('.pulldownmenu').stop().slideDown(100);
	},function(){
		if($(this).has('.pulldownmenu'))
			$(this).find('.parent_a').removeClass('on');
			$(this).find('.pulldownmenu').stop().slideUp(100);
	});
	$('.pulldownmenu li').hover(function(){
		if($(this).has('.pullBox'))
			$(this).find('.parent_b').addClass('on');
			$(this).find('.pullBox').stop().slideDown(100);
	},function(){
		if($(this).has('.pullBox'))
			$(this).find('.parent_b').removeClass('on');
			$(this).find('.pullBox').stop().slideUp(100);
	});
	$('.pulldownmenu li').hover(function(){
		if($(this).has('.pullIn'))
			$(this).find('.parent_b').addClass('on');
			$(this).find('.pullIn').stop().slideDown(100);
	},function(){
		if($(this).has('.pullIn'))
			$(this).find('.parent_b').removeClass('on');
			$(this).find('.pullIn').stop().slideUp(100);
	});
	$('.proSearIn02 .proConv').hover(function(){
		if($(this).has('.proConvBox'))
			$(this).find('.proConvBox').stop().slideDown(100);
	},function(){
		if($(this).has('.proConvBox'))
			$(this).find('.proConvBox').stop().slideUp(100);
	});
	$('.navbarToggle').on('click',function(){
		var target = $(this).data('target');
		var closeTarget = $('.navInst').data('target');
		if($(target).hasClass("on")){
			$(target).stop().slideUp(200).removeClass("on");
			$(this).removeClass("on");
		}else{
			$('.navInst').removeClass("on");
			$(closeTarget).hide().removeClass("on");
			$(target).stop().slideDown(200).addClass("on");
			$(this).addClass("on");
		}
	});
	var winH = $(window).height();
	$('.navbarCollapse').height(300);
	$('.navInst').on('click',function(){
		var closeTarget = $('.navbarToggle').data('target');
		var target = $(this).data('target');
		if($(target).hasClass("on")){
			$(target).stop().slideUp(200).removeClass("on");
			$(this).removeClass("on");
		}else{
			$('.navbarToggle').removeClass("on");
			$(closeTarget).hide().removeClass("on");
			$(target).stop().slideDown(200).addClass("on");
			$(this).addClass("on");
		}
		
	});
	
	
	$('.proConv').on('click',function(){
		var target = $(this).data('target');
		if($(target).hasClass("on")){
			$(target).stop().slideUp(200).removeClass("on");
			$(this).removeClass("on");
		}else{
			$(target).stop().slideDown(200).addClass("on");
			$(this).addClass("on");
		}
		
	});
	
	
	$(window).resize(function (event) {
		if($('.visibleTS').css('display') == 'none') {
			var target = $('.navbarToggle').data('target');
			$(target).hide().removeClass("on");
			$('.navbarToggle').removeClass("on");
		}
	});
	// グローバルナビ 該当コーナーのov
	if ($('#pageID').length == 1) {
		var ides = $('#pageID').val().split(',');
		for (var idx = 0; idx < ides.length; idx++) {
			var id = '#' + ides[idx];
			
			if ($(id).not('a').length == 1)
				$(id).addClass('selected');
			
		}
	}
	$(function(){
		if($('.biggerlink').length > 0){
			$('.biggerlink').biggerlink();
		}
	});
	$(window).resize(function (event) {
	  switchImage($('.visibleTS').css('display') == 'block');
	 });
	 switchImage($('.visibleTS').css('display') == 'block');
	 function switchImage(isVisible_header) {
	  $('img').each(function (index) {
	   var pc = $(this).attr('src').replace('_sp.', '_pc.');
	   var ts = $(this).attr('src').replace('_pc.', '_sp.');
	   if (!isVisible_header) {
		$(this).attr("src",pc);
	   }else {
		
		$(this).attr("src",ts);
	   }
	  });
	 }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
});



