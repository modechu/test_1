(function($){

	$(document).ready(function(){
    
		$('input[class=submit_ajax],img[name=submit_ajax]').click(function(){
			$.blockUI();
            var SubName = $(this).attr('id');

            switch( SubName ){

				case 'do_send_material_delete':
                var rn_num = $(this).attr('rn_num');
                if(confirm("Are you sure to Delete #RN"+rn_num+"!")){
                    location.href = "?PHP_action="+SubName+"&PHP_rn_num="+rn_num;
                }else{
                    $.unblockUI();
                }
				break;
                
                
				case 'search_bom_of_materials':
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
                
				case 'view_bom_of_materials':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
				case 'edit_bom_of_materials':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
				case 'confirm_bom_of_materials':
                if(confirm("Are you sure to submit!")){
                    $('form[id="'+SubName+'"]').submit();
                    return true;
                }
                $.unblockUI();
                return true;
				break;
                
				case 'do_bom_update':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                case 'revise_bom_of_materials':
                if(confirm("Are you sure to revise!")){
                    $('form[id="'+SubName+'"]').submit();
                    return true;
                }
                $.unblockUI();
                return false;
				break;
                
                
				case 'bom_mat_release_append_list':
                var factory = $('form[id="'+SubName+'"] select[name="PHP_factory"] :selected').html();
                if( !factory || factory == 'SELECT' ){
                    alert('Please choice Factory type!');
                    $.unblockUI();
                    return false; 
                }
                
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                if(!PHP_ord_num){
                    alert('Please input Order Number!');
                    $.unblockUI();
                    return false; 
                }
                $('form[id="'+SubName+'"]').submit();
				break;
                
                
				case 'bom_mat_release_list':
                var factory = $('form[id="'+SubName+'"] select[name="PHP_factory"] :selected').html();
                if( !factory || factory == 'SELECT' ){
                    alert('Please choice Factory type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                if(!PHP_ord_num){
                    alert('Please input Order Number!');
                    $.unblockUI();
                    return false; 
                }
                var mat = $('form[id="'+SubName+'"] input[id^=checkbox]:checked').val();
                if( !mat ){
                    alert('Please choice type : Lots or Acc!');
                    $.unblockUI();
                    return false; 
                }
                $('form[id="'+SubName+'"]').submit();
				break;
                
                
				case 'bom_mat_release_search':
                var factory = $('form[id="'+SubName+'"] select[name="PHP_factory"] :selected').html();
                if( !factory || factory == 'SELECT' ){
                    alert('Please choice Factory type!');
                    $.unblockUI();
                    return false; 
                }
                // var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                // if(!PHP_ord_num){
                    // alert('Please input Order Number!');
                    // $.unblockUI();
                    // return false; 
                // }
                // var mat = $('form[id="'+SubName+'"] input[id^=checkbox]:checked').val();
                // if( !mat ){
                    // alert('Please choice type : Lots or Acc!');
                    // $.unblockUI();
                    // return false; 
                // }
                $('form[id="'+SubName+'"]').submit();
				break;
                
                case 'do_added_send_material':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                case 'do_edit_send_material':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                case 'do_send_material_submit':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                case 'do_send_material_confirm':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                case 'do_send_material_revise':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                
                case 'list':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
				case 'do_bom_requi_append':
                var PHP_line = $('form[id="'+SubName+'"] select[name="PHP_line"] :selected').html();
                if( !PHP_line || PHP_line == 'SELECT' ){
                    alert('Please choice line type!');
                    $.unblockUI();
                    return false; 
                }
                var factory = $('form[id="'+SubName+'"] input[name="PHP_factory"]').val();
                if( !factory || factory == 'SELECT' ){
                    alert('Please input Factory type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                if(!PHP_ord_num){
                    alert('Please input Order Number!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_id = $('form[id="'+SubName+'"] input[name="PHP_id"]').val();
                if( !PHP_id ){
                    alert('Please input id Number!');
                    $.unblockUI();
                    return false; 
                }
                // var PHP_mat = $('form[id="'+SubName+'"] input[name="PHP_mat"]').val();
                // if( !PHP_mat ){
                    // alert('Please choice type : Lots or Acc!');
                    // $.unblockUI();
                    // return false; 
                // }
                $('form[id="'+SubName+'"]').submit();
				break;
                
                
                
				case 'do_bom_requi_edit_material':
                var PHP_line = $('form[id="'+SubName+'"] select[name="PHP_line"] :selected').html();
                if( !PHP_line || PHP_line == 'SELECT' ){
                    alert('Please choice line type!');
                    $.unblockUI();
                    return false; 
                }
                var factory = $('form[id="'+SubName+'"] input[name="PHP_factory"]').val();
                if( !factory || factory == 'SELECT' ){
                    alert('Please input Factory type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                if(!PHP_ord_num){
                    alert('Please input Order Number!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_rn_num = $('form[id="'+SubName+'"] input[name="PHP_rn_num"]').val();
                if(!PHP_rn_num){
                    alert('Please input RN Number!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_id = $('form[id="'+SubName+'"] input[name="PHP_id"]').val();
                if( !PHP_id ){
                    alert('Please input id Number!');
                    $.unblockUI();
                    return false; 
                }
                $('form[id="'+SubName+'"]').submit();
				break;
                
                
                
                case 'bom_requi_view_material':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                case 'bom_requi_edit_material':
                $('form[id="'+SubName+'"]').submit();
                return true;
				break;
                
                
                
			}
		});
    
        cell_loss = function(cat,id) {
            var consump = parseFloat($("#PHP_"+cat+"_consump_"+id).val());
            var loss = parseFloat($("#PHP_"+cat+"_loss_"+id).val());
            var o_qty = Math.round($("#PHP_"+cat+"_o_qty_"+id).val());
            final_qty = Math.round(consump * (1 + loss / 100 ) * o_qty);
            $("#PHP_"+cat+"_qty_"+id).val( final_qty );
        }
        
        commafy = function(num) {
            num = num + "";
            var re = /(-?\d+)(\d{3})/
            while (re.test(num)) {
                num = num.replace(re, "$1,$2");
            }
            return num;
        }
        
        // set_line = function(action){
            // var PHP_ord_num = $('input[name="PHP_ord_num"]').val();
            // var PHP_id = $('input[name="PHP_id"]').val();
            // var PHP_mat = $('input[name="PHP_mat"]').val();
            // var PHP_line = $('select[name="PHP_line"] :selected').val();
            // alert("?PHP_action="+action+"&PHP_ord_num="+PHP_ord_num+"&PHP_id="+PHP_id+"&PHP_mat="+PHP_mat+"&PHP_line="+PHP_line);
            // location.href="?PHP_action="+action+"&PHP_ord_num="+PHP_ord_num+"&PHP_id="+PHP_id+"&PHP_mat="+PHP_mat+"&PHP_line="+PHP_line;
        // }

		cal_send_material = function( i , w_id, color , p_id ){
        
            // var PHP_line = $('select[name="PHP_line"] :selected').html();
            // if( !PHP_line || PHP_line == 'SELECT' ){
                // alert('Please choice line type!');
                // $.unblockUI();
                // return false; 
            // }

            if( isNaN($(i).val()) ){
                alert('½Ð¿é¤J¼Æ¦r!');
                return false;
            }
        
            var o_qsum = qsum = 0;
            $('input[xname^=PHP_qty_'+w_id+']').each(function(i,elm){
                var o_qty = qty = 0;
                
                qty = Number($(elm).val().replace(/[,]+/g,""));
                qsum += qty;
                
                o_qty = Number($(elm).attr('o_qty')) - qty ;
                o_qsum += o_qty;
                $("td[x="+$(elm).attr('xname')+"]").html(commafy(o_qty));
            });
            $("td[xname=PHP_sum_"+w_id+"]").html(commafy(qsum));
            $("td[oname=PHP_sum_"+w_id+"]").html(commafy(o_qsum));

            // TOTAL
            var o_qsum = qsum = 0;
            $("input[yname=PHP_qty_"+p_id+"_"+color+"]").each(function(i,elm){
                var o_qty = qty = 0;
                
                qty = Number($(elm).val().replace(/[,]+/g,""));
                qsum += qty;
                
                o_qty = Number($("td[x="+$(elm).attr('xname')+"]").html().replace(/[,]+/g,""));
                o_qsum += o_qty;
            });
            $("td[yname=PHP_sum_"+p_id+"_"+color+"]").html(commafy(qsum));
            $("td[xsname=PHP_sum_"+p_id+"_"+color+"]").html(commafy(o_qsum));
            
            // TOTAL
            var o_qsum = qsum = 0;
            $("td[yname^=PHP_sum_"+p_id+"_]").each(function(i,elm){
                var o_qty = qty = 0;
                
                qty = Number($(elm).html().replace(/[,]+/g,""));
                qsum += qty;
                
                o_qty = Number($("td[xsname="+$(elm).attr('yname')+"]").html().replace(/[,]+/g,""));
                o_qsum += o_qty;
            });
            $("td[xyname=PHP_sum_"+p_id+"]").html(commafy(qsum));
            $("td[xysname=PHP_sum_"+p_id+"]").html(commafy(o_qsum));

            // G-TOTAL
            var o_qsum = qsum = 0;
            $("td[xxname^=PHP_sum_"+color+"]").each(function(i,elm){
                var o_qty = qty = 0;

                qty = Number($(elm).html().replace(/[,]+/g,""));
                if( $(elm).attr('xxname') == 'PHP_sum_'+color ){
                    qsum += qty;
                    
                    o_qty = Number($("td[xsname="+$(elm).attr('yname')+"]").html().replace(/[,]+/g,""));
                    o_qsum += o_qty;
                    $("td[o=PHP_gtotal_"+color+"]").html(commafy(o_qsum));
                }
            });
            $("td[yname=PHP_gtotal_"+color+"]").html(commafy(qsum));

            // G-TOTAL
            var o_qsum = qsum = 0;
            $("td[yname^=PHP_gtotal_]").each(function(i,elm){
                var o_qty = qty = 0;

                qty = Number($(elm).html().replace(/[,]+/g,""));
                qsum += qty;

                o_qty = Number($("td[o="+$(elm).attr('yname')+"]").html().replace(/[,]+/g,""));
                o_qsum += o_qty;
            });
            $("td[xyname=PHP_gtotal]").html(commafy(qsum));
            $("td[o=PHP_gtotal]").html(commafy(o_qsum));
		}

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

