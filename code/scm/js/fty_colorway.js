(function($){

	$(document).ready(function(){
    
        commafy = function(num) {
            num = num + "";
            var re = /(-?\d+)(\d{3})/
            while (re.test(num)) {
                num = num.replace(re, "$1,$2");
            }
            return num;
        }

		cal_fty_colorway = function( i , w_id, color , p_id ){

            if( isNaN($(i).val()) ){
                alert('½Ð¿é¤J¼Æ¦r!');
                return false;
            }
        
            var qsum = 0;
            $('input[xname^=PHP_qty_'+w_id+']').each(function(i,elm){
                var qty = 0;
                qty = Number($(elm).val().replace(/[,]+/g,""));
                qsum += qty;
            });
            $("td[xname=PHP_sum_"+w_id+"]").html(commafy(qsum));
            
            
            
            // TOTAL
            var qsum = 0;
            $("input[yname=PHP_qty_"+p_id+"_"+color+"]").each(function(i,elm){
                var qty = 0;
                qty = Number($(elm).val().replace(/[,]+/g,""));
                qsum += qty;
            });
            $("td[yname=PHP_sum_"+p_id+"_"+color+"]").html(commafy(qsum));
            // TOTAL
            var qsum = 0;
            $("td[yname^=PHP_sum_"+p_id+"_]").each(function(i,elm){
                var qty = 0;
                qty = Number($(elm).html().replace(/[,]+/g,""));
                qsum += qty;
                
            });
            $("td[xyname=PHP_sum_"+p_id+"]").html(commafy(qsum));
            
            
            
            // G-TOTAL
            var qsum = 0;
            $("td[xxname^=PHP_sum_"+color+"]").each(function(i,elm){
                var qty = 0;
                qty = Number($(elm).html().replace(/[,]+/g,""));
                if( $(elm).attr('xxname') == 'PHP_sum_'+color )
                qsum += qty;
            });
            $("td[yname=PHP_gtotal_"+color+"]").html(commafy(qsum));
            // G-TOTAL
            var qsum = 0;
            $("td[yname^=PHP_gtotal_]").each(function(i,elm){
                var qty = 0;
                qty = Number($(elm).html().replace(/[,]+/g,""));
                qsum += qty;
                
            });
            $("td[xyname=PHP_gtotal]").html(commafy(qsum));
		}
    
    
		$('input[class=submit_ajax],img[name=submit_ajax]').click(function(){
			$.blockUI();
            var SubName = $(this).attr('id');

            switch( SubName ){

				case 'update_colorway':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
				case 'submit_colorway':
                if(confirm("Are you sure to submit!")){
                    $('form[id="'+SubName+'"]').submit();
                    return true;
                }
                $.unblockUI();
                return false;
				break;
                
				case 'revise_colorway':
                if(confirm("Are you sure to revise!")){
                    $('form[id="'+SubName+'"]').submit();
                    return true;
                }
                $.unblockUI();
                return false;
				break;
                
				case 'edit_colorway':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
				case 'view_colorway':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
				case 'search_colorway':
                var factory = $('form[id="'+SubName+'"] select[name="PHP_factory"] :selected').html();
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                var PHP_goback = $('form[id="'+SubName+'"] input[name="PHP_goback"]').val();
                if( factory == 'SELECT' && !PHP_ord_num && !PHP_goback ){
                    alert('Please choice Factory type OR input Order Number!');
                    $.unblockUI();
                    return false; 
                } else {
                    $('form[id="'+SubName+'"]').submit();
                    return true;
                }
				break;

				case 'search_storage':
                var factory = $('form[id="'+SubName+'"] select[name="PHP_factory_to"] :selected').html();
                if( !factory || factory == 'SELECT' ){
                    alert('Please choice Factory type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_line = $('form[id="'+SubName+'"] select[name="PHP_line_to"] :selected').html();
                if( !PHP_line || PHP_line == 'SELECT' ){
                    alert('Please choice Line type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_zone = $('form[id="'+SubName+'"] select[name="PHP_zone_to"] :selected').html();
                if( !PHP_zone || PHP_zone == 'SELECT' ){
                    alert('Please choice Zone type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                if(!PHP_ord_num){
                    alert('Please input Order Number');
                    $.unblockUI();
                    return false; 
                }
				break;
			}
		});

		$('input[class=submit_ajax]').hover(function(){
			$(this).animate({ fontSize: '+=3' }, 200);
			$(this).addClass("submit_over");
		},function(){
			$(this).removeClass("submit_over"); 
			$(this).animate({ fontSize: '-=3' }, 200);
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');

		$('img[name=submit_ajax]').hover(function(){
			$(this).addClass("m_p");
		},function(){
			$(this).removeClass("m_p"); 
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');

        $('.list_txt').hover(function(){
            $(this).addClass("list_txt_bg");
        },function(){
            $(this).removeClass("list_txt_bg");  
        });

	});
})(jQuery);

