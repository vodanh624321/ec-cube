/**
 * common.js
 *
 *  version --- 1.0
 *  updated --- 2017/9/3
 */


/* !stack ------------------------------------------------------------------- */
jQuery(document).ready(function($) {
	pageScroll();
	rollover();
	common();
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
	
	$(window).on('load resize',function(){
		var scrolltop = $('#headerIn').height(); //header fixed
		$('a.scroll, .scroll a').each(function(){
			$(this).unbind('click').bind("click keypress",function(e){
				e.preventDefault();
				var target  = $(this).attr('href');
				var targetY = $(target).offset().top-scrolltop; //header fixed
				//var targetY = $(target).offset().top;
				var parent  = ( isUA.opera() )? (document.compatMode == 'BackCompat') ? 'body': 'html' : 'html,body';
				$(parent).animate(
					{scrollTop: targetY },
					400
				);
				return false;
			});
		});
	});
	
	$('.pageTop a').click(function(){
		$('html,body').animate({scrollTop: 0}, 'slow','swing');
		return false;
	});
}



/* !common --------------------------------------------------- */
var common = (function(){

	// グローバルナビ 該当コーナーのov
	if ($('#pageID').length == 1) {
		var ides = $('#pageID').val().split(',');
		for (var idx = 0; idx < ides.length; idx++) {
			var id = '#' + ides[idx];
			
			if ($(id).not('a').length == 1)
				$(id).addClass('selected');
			
		}
	}
	
	$('.btnMenu a').on('click',function(){
		if($(this).hasClass("on")){
			$('#sNavi').stop().slideUp(200);
			$(this).removeClass("on");
		}else{
			$('#sNavi').stop().slideDown(200);
			$(this).addClass("on");
		}
		
	});
	$(window).resize(function (event) {
		if($(window).width() > 959){
			$('#sNavi').hide();
			$('.btnMenu a').removeClass("on");
		}
	});
	$(window).on('load resize',function(){
			$('#sNavi').height($(window).height()-50);
		});
	
	$('#calendar').DatePicker({
		flat: true,
		date: ['2017-11-03', '2017-11-04', '2017-11-18', '2017-11-23', '2017-12-01', '2017-12-08', '2017-12-28'],
		format: 'Y-m-d',
		calendars: 2,
		mode: 'multiple',
		starts: 0
	});
	
	if($('.biggerlink').length > 0){
		$('.biggerlink').biggerlink();
	}




	
	$('.imgBG').each(function(){
		var img = $(this).find('img'),
			imgW = img.width(),
			imgH = img.height();
		$(this).css({backgroundImage:'url('+img.attr('src')+')'});
		if((imgW/imgH) < 1)
			$(this).addClass('vertical');
		else
			$(this).addClass('horizontal');
	
	});
	
		$(window).on('load resize',function(){
			if($(window).width() > 959){
				$('.noClick').unbind('click').click(function(){
					return false;
				});
			}else{
				$('.noClick').unbind('click').click(function(){
					return true;
				});
			}
		});
	
	$(window).resize(function(){
		$('.datepicker').height($('.datepickerContainer table').height() + 20);
	});
	
	
	$('#lNavi a.parents').each(function(){
		$(this).click(function(){
			var $parent = $(this).parent();
			if($parent.find('.subNavi').length > 0){
				if($(this).hasClass('on')){
					$(this).next().stop().slideUp(200);
					$(this).removeClass('on');
				}else{
					$(this).next().stop().slideDown(200);
					$(this).addClass('on');
				}
				return false;
			}
		});
		
	});
});



