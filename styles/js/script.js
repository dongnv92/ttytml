(function($){
    "use strict"; // Start of use strict  

    /************** FUNCTION ****************/ 

    // Menu fixed
    function fixed_header(){
    	var menu_element;
    	menu_element = $('.main-nav:not(.menu-fixed-content)').closest('.vc_row');
    	if($('.menu-fixed-enable').length > 0 && $(window).width()>1024){           
    		var menu_class = $('.main-nav').attr('class');
    		var header_height = $("#header").height()+100;
    		var ht = header_height + 150;
    		var st = $(window).scrollTop();

    		if(!menu_element.hasClass('header-fixed') && menu_element.attr('data-vc-full-width') == 'true') menu_element.addClass('header-fixed');
    		if(st>header_height){               
    			if(menu_element.attr('data-vc-full-width') == 'true'){
    				if(st > ht) menu_element.addClass('active');
    				else menu_element.removeClass('active');
    				menu_element.addClass('fixed-header');
    			}
    			else{
    				if(st > ht) menu_element.parent().parent().addClass('active');
    				else menu_element.parent().parent().removeClass('active');
    				if(!menu_element.parent().parent().hasClass('fixed-header')){
    					menu_element.wrap( "<div class='menu-fixed-content fixed-header "+menu_class+"'><div class='container'></div></div>" );
    				}
    			}
    		}else{
    			menu_element.removeClass('active');
    			if(menu_element.attr('data-vc-full-width') == 'true') menu_element.removeClass('fixed-header');
    			else{
    				if(menu_element.parent().parent().hasClass('fixed-header')){
    					menu_element.unwrap();
    					menu_element.unwrap();
    				}
    			}
    		}
    	}
    	else{
    		menu_element.removeClass('active');
    		if(menu_element.attr('data-vc-full-width') == 'true') menu_element.removeClass('fixed-header');
    		else{
    			if(menu_element.parent().parent().hasClass('fixed-header')){
    				menu_element.unwrap();
    				menu_element.unwrap();
    			}
    		}
    	}
    }
    //Menu Responsive
    function rep_menu(){
    	$('.toggle-mobile-menu').on('click',function(event){
    		event.preventDefault();
    		$(this).parents('.main-nav').toggleClass('active');
    	});
    	$('.main-nav li.menu-item-has-children>a').on('click',function(event){
    		if($(window).width()<768){
    			event.preventDefault();
    			$(this).next().stop(true,false).slideToggle();
    		}else{
				// return false;
			}
		});
    }

    function background(){
    	$('.bg-slider .item-slider').each(function(){
    		var src=$(this).find('.banner-thumb a img').attr('src');
    		$(this).css('background-image','url("'+src+'")');
    	});	
    }

    function tool_panel(){
        $('.dm-open').on('click',function(){
            $('#widget_indexdm').toggleClass('active');
            $('#indexdm_img').toggleClass('background');

            $('.dm-content .item-content').hover(
                function(){
                    $('#indexdm_img').addClass('active');
                    var img_src = $(this).find('img').attr('data-src');
                    $('.img-demo').css('display','block');
                    $('.img-demo').css('background-image','url('+img_src+')');
                },
                function(){
                    $('#indexdm_img').removeClass('active');
                    $('.img-demo').attr('style','');
                }
            );
            return false;
        })
    }

    /************ END FUNCTION **************/  
    $(document).ready(function(){
        tool_panel();
        if($(".main-nav").length>0){
            $('.main-nav>ul li.menu-item-has-children>a:after').on('click',function(event){
                if($(window).width()<992){
                    alert('test');   
                }
            });
        }
		//Fix mailchimp
		$('.newsletter-form').each(function(){
			var placeholder = $(this).attr('data-placeholder');
			var submit = $(this).attr('data-submit');
			if(placeholder) $(this).find('input[name="EMAIL"]').attr('placeholder',placeholder);
			if(submit) $(this).find('input[type="submit"]').val(submit);
		})		
    });

    $(window).load(function(){
		// menu fixed onload
		$("#header").css('min-height','');
		if($(window).width()>1024){
			$("#header").css('min-height',$("#header").height());
			fixed_header();
		}
		else{
			$("#header").css('min-height','');
		}
        // rtl-enable
        if($('.rtl-enable').length > 0){
        	$('.vc_row[data-vc-full-width="true"]').each(function(){
        		var style = $(this).attr('style');
        		style = style.replace("left","right");
        		$(this).attr('style',style);
        	})
        }
		//menu fix
		if($(window).width() >= 768){
			var c_width = $(window).width();
			$('.main-nav ul ul ul.sub-menu').each(function(){
				var left = $(this).offset().left;
				if(c_width - left < 200){
					$(this).css({"left": "-100%"})
				}
				if(left < 200){
					$(this).css({"left": "100%"})
				}
			})
		}
	});// End load

	/* ---------------------------------------------
     Scripts resize
     --------------------------------------------- */
     var w_width = $(window).width();
     $(window).resize(function(){
		$("#header").css('min-height','');
		var c_width = $(window).width();
		setTimeout(function() {
			if($('.rtl-enable').length > 0 && c_width != w_width){
				$('.vc_row[data-vc-full-width="true"]').each(function(){
					var style = $(this).attr('style');
					style = style.replace(" left:"," right:");
					$(this).attr('style',style);
				})
				w_width = c_width;
			}
		}, 3000);
	});

     jQuery(window).scroll(function(){
     	fixed_header();
     	if($(window).width()>1024){
     		$("#header").css('min-height',$("#header").height());
     		fixed_header();
     	}
     	else{
     		$("#header").css('min-height','');
     	}
		//Scroll Top
		if($(this).scrollTop()>$(this).height()){
			$('.scroll-top').addClass('active');
		}else{
			$('.scroll-top').removeClass('active');
		}
	});// End Scroll
 })(jQuery);