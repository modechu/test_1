(function($){

	$(document).ready(function(){

		$('td[id^=LHT-]').hover(function(){
			$(this).addClass("m_p_line");
		},function(){
            $(this).removeClass("m_p_line");
		});
        
		$('td[id^=LHT-]').live('click', function(){
            $id = $(this).attr('id').split("-");
            $id = $id[1];
            if( $('td[id^=LH-'+$id+']').is(':visible') ){
                $('td[id^=LH-'+$id+']').hide();
                $('img[id^=LHT-'+$id+']').attr({src:'./js/src/css/img/nav-right.gif'});
                $('td[id^=LHT-'+$id+']').removeClass("f_6100BE");
                $('td[id^=LHT-'+$id+']').addClass("ListName");
            } else {
                $('td[id^=LH-'+$id+']').show();
                $('img[id^=LHT-'+$id+']').attr({src:'./js/src/css/img/nav-left.gif'});
                $('td[id^=LHT-'+$id+']').addClass("f_6100BE");
                $('td[id^=LHT-'+$id+']').removeClass("ListName");
            }
		});
        
		$('td[id^=HT-]').hover(function(){
			$(this).addClass("m_p_line");
		},function(){
            $(this).removeClass("m_p_line");
		});
        
		$('td[id^=HT-]').live('click', function(){
            $id = $(this).attr('id').split("-");
            $id = $id[1];
            if( $('td[id^=H-'+$id+']').is(':visible') ){
                $('td[id^=H-'+$id+']').hide();
                $('img[id^=HT-'+$id+']').attr({src:'./js/src/css/img/nav-right.gif'});
                $('td[id^=HT-'+$id+']').removeClass("f_62B50F");
                $('td[id^=HT-'+$id+']').addClass("BomName");
            } else {
                $('td[id^=H-'+$id+']').show();
                $('img[id^=HT-'+$id+']').attr({src:'./js/src/css/img/nav-left.gif'});
                $('td[id^=HT-'+$id+']').addClass("f_62B50F");
                $('td[id^=HT-'+$id+']').removeClass("BomName");
            }
		});
        
		// <TR> MOUSEOVER
		$('tr[id^=List_TR]').hover(function(){
			$(this).addClass("List_TR");
		},function(){
			$(this).removeClass("List_TR");  
		});
        
        $('.title_submit').hover(function(){
            $(this).animate({ paddingLeft: '+=10' }, 200);
            $(this).addClass("title_submit_over");
        },function(){
            $(this).animate({ paddingLeft: '-=10' }, 200);
            $(this).removeClass("title_submit_over");  
        });
        
        $('.title_submit').click(function(){
            $.blockUI({ message: ' Loding ...'});
            $("#"+$(this).attr('id')).submit();
        });

		// $('tr #notice_adjust_link').hover(function(){
			// $(this).addClass("b_add");
		// },function(){
			// $(this).removeClass("submit_over"); 
		// }).bind('selectstart', function(){
			// return false; 
		// }).css('MozUserSelect','none');

	});
})(jQuery);