(function($){

	$(document).ready(function(){

		$('input[class=submit_ajax]').click(function(){
			$.blockUI();
            var SubName = $(this).attr('id');

            switch( SubName ){

				case 'append_storage':
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

                
                
				case 'transfer_qty':
                var in_out = $('form[id="'+SubName+'"] input[id^=checkbox]:checked').val();
                if( !in_out ){
                    alert('Please choice type : In or Out!');
                    $.unblockUI();
                    return false; 
                }
                
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                if(!PHP_ord_num){
                    alert('Please input Order Number');
                    $.unblockUI();
                    return false; 
                }
                
                var factory = $('form[id="'+SubName+'"] select[name="PHP_factory"] :selected').html();
                if( !factory || factory == 'SELECT' ){
                    alert('Please choice From Factory type!');
                    $.unblockUI();
                    return false; 
                }
                var factory = $('form[id="'+SubName+'"] select[name="PHP_factory_to"] :selected').html();
                if( !factory || factory == 'SELECT' ){
                    alert('Please choice To Factory type!');
                    $.unblockUI();
                    return false; 
                }
                
                var PHP_line = $('form[id="'+SubName+'"] select[name="PHP_line"] :selected').html();
                if( !PHP_line || PHP_line == 'SELECT' ){
                    alert('Please choice From Line type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_line = $('form[id="'+SubName+'"] select[name="PHP_line_to"] :selected').html();
                if( !PHP_line || PHP_line == 'SELECT' ){
                    alert('Please choice To Line type!');
                    $.unblockUI();
                    return false; 
                }
                
                var PHP_zone = $('form[id="'+SubName+'"] select[name="PHP_zone"] :selected').html();
                if( !PHP_zone || PHP_zone == 'SELECT' ){
                    alert('Please choice From Zone type!');
                    $.unblockUI();
                    return false; 
                }
                var PHP_zone = $('form[id="'+SubName+'"] select[name="PHP_zone_to"] :selected').html();
                if( !PHP_zone || PHP_zone == 'SELECT' ){
                    alert('Please choice To Zone type!');
                    $.unblockUI();
                    return false; 
                }
				break;
                
                
                case 'transfer_search':
                
                var PHP_storage_num = $('form[id="'+SubName+'"] input[name="PHP_storage_num"]').val();
                var PHP_ord_num = $('form[id="'+SubName+'"] input[name="PHP_ord_num"]').val();
                
                if( !PHP_storage_num && !PHP_ord_num ){
                    alert('Please input Storage Number OR Order Number!');
                    $.unblockUI();
                    return false; 
                }
                break;
                
                
                case 'transfer_update':
                
                var PHP_factory = $('form[id="'+SubName+'"] select[name="PHP_factory"]').val();
                var PHP_factory_to = $('form[id="'+SubName+'"] select[name="PHP_factory_to"]').val();
                var PHP_line = $('form[id="'+SubName+'"] select[name="PHP_line"]').val();
                var PHP_line_to = $('form[id="'+SubName+'"] select[name="PHP_line_to"]').val();
                var PHP_zone = $('form[id="'+SubName+'"] select[name="PHP_zone"]').val();
                var PHP_zone_to = $('form[id="'+SubName+'"] select[name="PHP_zone_to"]').val();
                
                if( !PHP_factory_to || PHP_factory_to == 'SELECT' ){
                    alert('Please choice To Factory type!');
                    $.unblockUI();
                    return false; 
                }
                
                if( !PHP_line_to || PHP_line_to == 'SELECT' ){
                    alert('Please choice To Line type!');
                    $.unblockUI();
                    return false; 
                }
                
                if( !PHP_zone_to || PHP_zone_to == 'SELECT' ){
                    alert('Please choice To Zone type!');
                    $.unblockUI();
                    return false; 
                }
                
                if( PHP_factory == PHP_factory_to && PHP_line == PHP_line_to && PHP_zone == PHP_zone_to ){
                    alert('ERROR!');
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

		$('tr[id^=G_partial_span_]').hover(function(){
			$(this).addClass("Bom_TR_hover");
		},function(){
			$(this).removeClass("Bom_TR_hover");  
		});

		$('#delete').hover(function(){
			$(this).addClass("m_p");
		},function(){
			$(this).removeClass("m_p");  
		});

		$('tr[id^=List_TR]').hover(function(){
			$(this).addClass("storage_list_TR");
		},function(){
			$(this).removeClass("storage_list_TR");  
		});

        $('.list_txt').hover(function(){
            $(this).addClass("list_txt_bg");
        },function(){
            $(this).removeClass("list_txt_bg");  
        });

        // <TR> CLICK
		$('tr[id^=G_partial_tr_]').live('click', function(){
            span_id = $(this).attr("span_id");
            $("tr[id^=G_partial_span_]").hide();
            $("tr[id^="+span_id+"]").show();
		});

        // IMG DELETE
		$('img[id^=delete]').live('click', function(){
            $.blockUI();
            sid = $(this).attr("sid");
            if(confirm("Delete this record? ID:"+sid)){
                location.href = "storage.php?PHP_action=transfer_delete&PHP_sid="+sid;
            } else {
                $.unblockUI();
            }
		});

		cal_end_line_color_qty = function(mks, color_idx, size_idx){
        
			// 當日
			var T_ttl_qty = 0;
			$("input[id^=T_qty_"+mks+"_"+color_idx+"_]").each(function(i,elm){
				T_ttl_qty += Number($(elm).val());
			});
			
			$("#T_color_ttl_"+mks+"_"+color_idx).html(T_ttl_qty);
			
			// Grand Total
			var G_o_qty = Number($("#G_o_qty_"+mks+"_"+color_idx+"_"+size_idx).val());
			var T_qty = Number($("#T_qty_"+mks+"_"+color_idx+"_"+size_idx).val());
			$("#G_qty_"+mks+"_"+color_idx+"_"+size_idx).val( G_o_qty + T_qty );
			
			var G_ttl_qty = 0;
			$("input[id^=G_qty_"+mks+"_"+color_idx+"_]").each(function(i,elm){
				G_ttl_qty += Number($(elm).val());
			});
			$("#G_color_ttl_"+mks+"_"+color_idx).html(G_ttl_qty);
			
		}

		del_storage_qty = function(det_id, ord_num, fty, line, zone , today , status ){
			if(confirm("Delete this record?")){
				$.blockUI();
                location.href = "storage.php?PHP_action=delete_storage_qty&det_id="+det_id+"&PHP_ord_num="+ord_num+"&PHP_facroty="+fty+"&PHP_line="+line+"&PHP_zone="+zone+"&PHP_pt_date="+today+"&PHP_status="+status;
			}
		}

		del_transfer_qty = function(det_id, fty, fty_to, line, line_to, zone, zone_to, ord_num, in_out){
			if(confirm("Delete this record?")){
				$.blockUI();
				location.href = "storage.php?PHP_action=delete_transfer_qty&det_id="+det_id+"&PHP_ord_num="+ord_num+"&PHP_factory="+fty+"&PHP_factory_to="+fty_to+"&PHP_line="+line+"&PHP_line_to="+line_to+"&PHP_zone="+zone+"&PHP_zone_to="+zone_to+"&PHP_in_out="+in_out;
			}
		}

        get_full_line = function(fty,SubName){

            $.blockUI();
            
            $('form[id="'+SubName+'"] select[name="PHP_line_to"]').empty();
            $('form[id="'+SubName+'"] select[name="PHP_zone_to"]').empty();
            
			$.ajax({
					url: 'storage.php?PHP_action=get_fty_line',
					type: 'GET',
					data: {
						PHP_factory : fty,
					},
					error: function(xhr) {
						alert('Ajax request 發生錯誤'+xhr);
					},
					
					success: function(response) {
                        if( response == 'nodata' ){
                            alert('No data!');
                        } else {
                            arr = response.split(","); 
                            var opts = '<option value="">SELECT</option>';
                            var count = arr.length -1;
                            for (i=0; i<=count; i=i+1)
                            {
                                opts += '<option value="'+arr[i]+'">'+arr[i]+'</option>';
                            }

                            $('form[id="'+SubName+'"] select[name="PHP_line_to"]').html(opts);
                        }
						$.unblockUI();
					}
			});
		}

        get_full_zone = function(line,SubName){
           
			$.blockUI();

            $('form[id="'+SubName+'"] select[name="PHP_zone_to"]').empty();
            
			$.ajax({
					url: 'storage.php?PHP_action=get_line_zone',
					type: 'GET',
					data: {
						PHP_factory : $('form[id="'+SubName+'"] select[name="PHP_factory_to"] :selected').html(),
                        PHP_zoen : 'full',
					},
					error: function(xhr) {
						alert('Ajax request 發生錯誤'+xhr);
					},
					
					success: function(response) {
                        if( response == 'nodata' ){
                            alert('No data!');
                        } else {
                            arr = response.split(","); 
                            var opts = '<option value="">SELECT</option>';
                            var count = arr.length -1;
                            for (i=0; i<=count; i=i+1)
                            {
                                opts += '<option value="'+arr[i]+'">'+arr[i]+'</option>';
                            }

                            $('form[id="'+SubName+'"] select[name="PHP_zone_to"]').html(opts);
                            
                        }
						$.unblockUI();
					}
			});
		}

        get_fty_line = function(fty,name,whares){

            $.blockUI();
            if( whares == 'from' ){
                $("#PHP_line").empty();
                $("#PHP_zone").empty();
            } else {
                $("#PHP_line_to").empty();
                $("#PHP_zone_to").empty();
            }
			$.ajax({
					url: 'storage.php?PHP_action=get_fty_line',
					type: 'GET',
					data: {
						PHP_factory : fty,
						PHP_name : name,
						PHP_whare : whares,
					},
					error: function(xhr) {
						alert('Ajax request 發生錯誤'+xhr);
					},
					
					success: function(response) {
                        if( response == 'nodata' ){
                            alert('No data!');
                        } else {
                            arr = response.split(","); 
                            var opts = '<option value="">SELECT</option>';
                            var count = arr.length -1;
                            for (i=0; i<=count; i=i+1)
                            {
                                opts += '<option value="'+arr[i]+'">'+arr[i]+'</option>';
                            }

                            if( whares == 'from' ){
                                $("#PHP_line").html(opts);
                            } else {
                                $("#PHP_line_to").html(opts);
                            }
                        }
						$.unblockUI();
					}
			});
		}

        get_line_zone = function(line,name,whares){
            // var in_out = $('input[id^=checkbox]:checked').val();
           
			$.blockUI();
            if( whares == 'from' ){
                $("#PHP_zone").empty();
            } else {
                $("#PHP_zone_to").empty();
            }
            
			$.ajax({
					url: 'storage.php?PHP_action=get_line_zone',
					type: 'GET',
					data: {
						PHP_factory : $('#PHP_factory'+(whares=='from'?'':'_to')).val(),
						PHP_line : line,
						PHP_name : name,
                        PHP_whare : whares,
                        PHP_zoen : 'full',
					},
					error: function(xhr) {
						alert('Ajax request 發生錯誤'+xhr);
					},
					
					success: function(response) {
                        if( response == 'nodata' ){
                            alert('No data!');
                        } else {
                            arr = response.split(","); 
                            var opts = '<option value="">SELECT</option>';
                            var count = arr.length -1;
                            for (i=0; i<=count; i=i+1)
                            {
                                opts += '<option value="'+arr[i]+'">'+arr[i]+'</option>';
                            }

                            if( whares == 'from' ){
                                $("#PHP_zone").html(opts);
                            } else {
                                $("#PHP_zone_to").html(opts);
                            }
                        }
						$.unblockUI();
					}
			});
		}

		$('form[id="transfer_qty"] input[id^=checkbox]').bind('click', function(e){
            if( $(this).val() == 'in' ){
                $('form[id="transfer_qty"] table[id=tb_bg_to]').css({"background-color" : "#CDFFCE"});
                $('form[id="transfer_qty"] table[id=tb_bg_from]').css({"background-color" : "#FFFFFF"});
            } else {
                $('form[id="transfer_qty"] table[id=tb_bg_to]').css({"background-color" : "#FFFFFF"});
                $('form[id="transfer_qty"] table[id=tb_bg_from]').css({"background-color" : "#CDFFCE"});
            }
		});

		check_transfer =  function(id, fty, fty_to, line, line_to, zone, zone_to, check_flag){
			if(confirm("Are you sure to " + (check_flag?"Cfm?":"Reject?"))){
				$.blockUI();
                location.href = "storage.php?PHP_action=check_transfer&id="+id+"&PHP_flag="+check_flag+"&PHP_factory="+fty+"&PHP_factory_to="+fty_to+"&PHP_line="+line+"&PHP_line_to="+line_to+"&PHP_zone="+zone+"&PHP_zone_to="+zone_to;
			}
        }

		cal_storage_color_qty = function(mks, color_idx, size_idx){

			var G_o_qty = Number($("#G_o_qty_"+mks+"_"+color_idx+"_"+size_idx).val());
			var T_qty = Number($("#T_qty_"+mks+"_"+color_idx+"_"+size_idx).val());
			if( T_qty > G_o_qty ){
				alert("Qty error");
				$("#T_qty_"+mks+"_"+color_idx+"_"+size_idx).val(G_o_qty);
				T_qty = G_o_qty;
			}
			// 當日
			var T_ttl_qty = 0;
			$("input[id^=T_qty_"+mks+"_"+color_idx+"_]").each(function(i,elm){
				T_ttl_qty += Number($(elm).val());
			});
			
			$("#T_color_ttl_"+mks+"_"+color_idx).html(T_ttl_qty);
			
			// Grand Total
			$("#G_qty_"+mks+"_"+color_idx+"_"+size_idx).val( G_o_qty - T_qty );
			
			var G_ttl_qty = 0;
			$("input[id^=G_qty_"+mks+"_"+color_idx+"_]").each(function(i,elm){
				G_ttl_qty += Number($(elm).val());
			});
			$("#G_color_ttl_"+mks+"_"+color_idx).html(G_ttl_qty);
		}

        re_cal_storage = function(f){
            $('tr[name^=re_cal_storage]').each(function(i,elm){
                $(elm).find('input[id^=G_qty_]').each(function(o,elms) {
                    var re_id = $(elms).attr('id');
                    var id_arr = re_id.split("_");
                    var mks = id_arr[2];
                    var wqty = id_arr[3];
                    var ts = id_arr[4];
                    cal_storage_color_qty(mks,wqty,ts);
                });
            });
        }

	});
})(jQuery);

