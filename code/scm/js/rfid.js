// 取出上傳的檔名
// var  LSA  =  $(file).val().split(".")[0].split("\\");
// var  CFN  =  LSA[LSA.length-1];
// var now = new Date();
// alert(now.getFullYear() + '-' + (now.getMonth()+1) + '-' + (now.getDate()) + ' ' + now.getHours()+':'+now.getMinutes()+':'+now.getSeconds());
// location.href = "arrival_time.php?PHP_action=get_carrier_num&carrier_num="+$('#carrier_num').val();

(function($){

	$(document).ready(function(){

		$('input[class=submit_ajax]').hover(function(){
			$(this).animate({ fontSize: '+=3' }, 200);
			$(this).addClass("submit_over");
		},function(){
			$(this).removeClass("submit_over"); 
			$(this).animate({ fontSize: '-=3' }, 200);
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');

		// Submit setting
		$('input[class=submit_ajax]').click(function(){
			$.blockUI({ message: ' Loding ... '});
			var SubName = $(this).attr('id');
			switch( SubName ){
			



				case 'arrival_time_search':
				var bl_num = $('#bl_num').val();
                var carrier_num = $('#carrier_num').val();
				if(!bl_num && !carrier_num){
					alert('Please input B.L. number or carrier number!!');
					$.unblockUI();
					return false; 
				}
				location.href = "arrival_time.php?PHP_action=arrival_time_search&bl_num="+bl_num+"&carrier_num="+carrier_num;
				break;



				case 'append_arrival':
				if (!confirm('Are you sure to append arrival ?')) {
					$.unblockUI();
					return false; 
				}
				var id = $('#ship_id').val();
				var carrier_num = $('#carrier_num').val();
				$.ajax({
					url: 'arrival_time.php?PHP_action=append_arrival',
					type: 'GET',
					data: {
						ship_id : id ,
						carrier_num : carrier_num 
					},
					error: function(xhr) {
						alert('Ajax request 發生錯誤'+xhr);
					},
					
					success: function(response) {
						var arr = response.split("@"); 
						if ( arr[0] == 'ok' ) {
							alert(arr[1]);
							$('#reply').show();
							$('#append').hide();
						} else {
							// availableTags = arr[1];
						}
						$.unblockUI();
					}
				});
				break;

				
				
				case 'reply_arrival':
				if (!confirm('Are you sure to reply arrival ?')) {
					$.unblockUI();
					return false; 
				}
				var id = $('#ship_id').val();
				//var carrier_num = $('#carrier_num').val();
				$.ajax({
					url: 'arrival_time.php?PHP_action=reply_arrival',
					type: 'GET',
					data: {
						ship_id : id
					},
					error: function(xhr) {
						alert('Ajax request 發生錯誤'+xhr);
					},
					success: function(response) {
						var arr = response.split("@"); 
						if ( arr[0] == 'ok' ) {
							alert(arr[1]);
							$('#reply').hide();
							$('#append').show();
						} else {
							// availableTags = arr[1];
						}
						$.unblockUI();
					}
				});
				break;



				case 'incoming_material_search':
				var bl_num = $('#bl_num').val();
                var carrier_num = $('#carrier_num').val();
				if(!bl_num && !carrier_num){
					alert('Please input B.L. number or carrier number!!');
					$.unblockUI();
					return false; 
				}
				location.href = "incoming_material.php?PHP_action=incoming_material_search&bl_num="+bl_num+"&carrier_num="+carrier_num;
				break;



				case 'append_incoming_qty':
				
				var chk_qty = 0;

				$('tr[id^=List_TR]').each(function(i,elm) {
					var mid = $(elm).attr("mid");
					var incoming_qty = Number($("#po_ship_link_qty_"+mid).val());
					// if ( incoming_qty == 0 ) chk_qty++;
				});

				if ( chk_qty > 0 ) {
					$.unblockUI();
					alert('Please input Incoming qty!');
					return false;
					
				}
				

				if ( confirm('Are you sure to append Incoming qty ?') ){
					$.submit();
				} else {
					$.unblockUI();
					return false;
				}
				
				break;

                

				case 'update_incoming_qty':

				var chk_qty = 0;

				$('tr[id^=List_TR]').each(function(i,elm) {
					var mid = $(elm).attr("mid");
					var incoming_qty = Number($("#stock_i_qty_"+mid).val());
					var del_mk = $("#del_mk_"+mid+':checked').val() == 'y' ? 'y' : 'n';
					if ( incoming_qty == 0 && del_mk == 'n' ) chk_qty++;
				});

				if ( chk_qty > 0 ) {
					$.unblockUI();
					alert('Please input Incoming qty!');
					return false;
				}
				

				if ( confirm('Are you sure to update Incoming qty ?') ){
					$.submit();
				} else {
					$.unblockUI();
					return false;
				}
				
				break;



				case 'receiving_to_get_material_search':
				var num = $('#order_num').val();
				var notice_num = $('#notice_num').val();
				
				if(!num){
					if(!notice_num){
						alert('Please input order# or notice#');
						$.unblockUI();
						return false; 
					}
				}
				
				location.href = "receiving_to_get_material.php?PHP_action=receiving_to_get_material_search&order_num="+num+"&notice_num="+notice_num;
				break;



				case 'append_send_qty':
				
				var acpt_user = $("#PHP_acpt_user").val();
				if(!acpt_user){
					$.unblockUI();
					alert('Please input User');
					return false;
				}
				
				var chk = false;
                var chk_qty = 0;
				$('tr[id^=List_TR]').each(function(i,elm) {

					var mid = $(elm).attr("mid");
					var collar_qty = Number($("#send_qty_"+mid).val());
					var stock_qty = Number($("#stock_qty"+mid).val());
					
					if ( stock_qty > 0 ) {
						if ( stock_qty < collar_qty ) {
							$("#send_qty_"+mid).val(stock_qty);
							$(elm).css( "background-color" , "#FEFFCD" );
							chk = true;
						} else {
							$(elm).css( "background-color" , "#FFFFFF" );
						}
					} else if ( stock_qty == collar_qty )  {
						$(elm).css( "background-color" , "#FFFFFF" );
					} else {
						if ( collar_qty > 0 ) {
							$(elm).css( "background-color" , "#FFCDFE" );
							chk = true;
						} else {
							$(elm).css( "background-color" , "#FFFFFF" );
						}
					}

					if ( collar_qty == 0 ) chk_qty = chk_qty++;
				});

				if ( chk_qty > 0 ) {
					$.unblockUI();
					alert('Please input collar qty!');
					return false;
					
				}
				
				return true;
				
				break;



				case 'returned_material_adjustment_search_incoming':
				var bl_num = $('#bl_num').val();
                var carrier_num = $('#carrier_num').val();
				if(!bl_num && !carrier_num){
					alert('Please input B.L. number or carrier number!!');
					$.unblockUI();
					return false; 
				}
				location.href = "returned_material_adjustment.php?PHP_action=returned_material_adjustment_search_incoming&bl_num="+bl_num+"&carrier_num="+carrier_num;
				break;



				case 'returned_material_adjustment_search':
				var rn_num = $('#rn_num').val();
				var ord_num = $('#ord_num').val();
				
				if(!rn_num && !ord_num){
					alert('Please input number or Order number!');
					$.unblockUI();
					return false; 
				}
				
				location.href = "returned_material_adjustment.php?PHP_action=returned_material_adjustment_search&rn_num="+rn_num+"&ord_num="+ord_num;
				break;
				
				
				
				case 'returned_material_adjustment_search_receiving':
				var rn_num = $('#rn_num').val();
				
				if(!rn_num){
					alert('Please input B.L. or Carrier number!');
					$.unblockUI();
					return false; 
				}
				
				location.href = "returned_material_adjustment.php?PHP_action=returned_material_adjustment_search_receiving&bl_num="+bl_num+"&carrier_num="+carrier_num;
				break;



				case 'append_retreat_qty':
				var chk = 0;
				$('tr[id^=List_TR]').each(function(i,elm) {
					var qty = Number($("#po_ship_link_qty_"+mid).val());
					if ( qty > 0 ) {
						chk ++;
					}
				});
				
				if( chk == 0 ) {
					alert("Please input retreat qty!");
					$.unblockUI();
					return false;
				}
				
				if ( confirm('Are you sure input retreat qty ?') ){
					$.submit();
				} else {
					$.unblockUI();
					return false;
				}

				break;



				case 'append_return_send_qty':
				var chk = 0;
				
				$('input[id^=return_send_qty_]').each(function(i,elm) {
					var qty = Number($(elm).val());
					if ( qty > 0 ) {
						chk ++;
					}
				});
				
				if( chk == 0 ) {
					alert("Please input retreat qty!");
					$.unblockUI();
					return false;
				}
				$.submit();
				// location.href = "returned_material_adjustment.php?PHP_action=append_retreat_qty";
				break;
				
				
				
				case 'append_inventory':
				var item = $('input[name=mat]:checked').val();
				//$('input[name=mat]:checked').alert($('input[name=mat]:checked').val());
				//alert(item);
				if( !item ){
					
					alert('Please choice material type!');
					$.unblockUI();
					return false; 
				}

				var dept = $('#PHP_dept').val();
				if ( !dept ) {
					$.unblockUI();
					alert('Please choice Factory !');
					return false;
				}
				var order = $('#PHP_order').val();
				//alert(item+";"+dept);
				
				location.href = "inventory.php?PHP_action=append_inventory&item="+item+"&PHP_dept="+dept+"&PHP_order="+order;
				break;

				

				case 'update_stock_mat_sub_qty':
				var chk = 0;
				$('input[id^=count_qty]').each(function(i,elm) {
					var qty = $(elm).val();
					if ( qty != '' ) {
						chk ++;
					}
					/* if ( qty > 0 ) {
						chk ++;
					} */
				});
				
				if( chk == 0 ) {
					alert("Please input count qty!");
					$.unblockUI();
					return false;
				} 
				
				if ( confirm('Are you sure input count qty ?') ){
					$.submit();
				} else {
					$.unblockUI();
					return false;
				}
				break;



				case 'update_stock_mat_cfm_qty':
				$("#PHP_action").val('update_stock_mat_cfm_qty');
				if ( confirm('Are you sure Confirm count qty ?') ){
					$.submit();
				} else {
					$.unblockUI();
					return false;
				}
				// location.href = "incoming_material.php?PHP_action=incoming_material_search&carrier_num="+num;
				break;
				
				
				
				
				case 'inventory_finish':
					var item = $('input[name=mat]:checked').val();
					//$('input[name=mat]:checked').alert($('input[name=mat]:checked').val());
					
					if( !item ){
						alert('Please choice material type!');
						$.unblockUI();
						return false; 
					}

					var dept = $('#PHP_dept').val();
					if ( !dept ) {
						$.unblockUI();
						alert('Please choice Factory !');
						return false;
					}
					
					//alert(item+";"+dept);
					if ( confirm('Are you sure to finish inventory ?') ){
						location.href = "inventory.php?PHP_action=inventory_stock&item="+item+"&dept="+dept+"&PHP_finish=1";
					} else {
						$.unblockUI();
						return false;
					}
					
				break;

			}
		});



		// get_bl_num
		var availableTags = '';
		$('#bl_num').live('keyup', function(){
			$.ajax({
				url: 'arrival_time.php?PHP_action=get_bl_num',
				type: 'GET',
				data: {
					bl_num : $('#bl_num').val(),
					contentType: "application/x-www-form-urlencoded; charset=Big5"
				},
				error: function(xhr) {
					alert('Ajax request 發生錯誤'+xhr);
				},
				success: function(response) {
					var arr = decodeURI(response);
					arr = arr.split("@"); 
					if ( arr[0] == 'ok' ) {
						availableTags = arr[1];
						$( "#bl_num" ).autocomplete({
							source: availableTags.split(",")
						});
					} else {
						// availableTags = arr[1];
					}
				}
			});
		});


		// autocomplete share
		var availableTags = '';
		$('#carrier_num').live('keyup', function(){
			$.ajax({
				url: 'arrival_time.php?PHP_action=get_carrier_num',
				type: 'GET',
				data: {
					carrier_num : $('#carrier_num').val(),
					contentType: "application/x-www-form-urlencoded; charset=Big5"
				},
				error: function(xhr) {
					alert('Ajax request 發生錯誤'+xhr);
				},
				success: function(response) {
					var arr = decodeURI(response);
					arr = arr.split("@"); 
					if ( arr[0] == 'ok' ) {
						availableTags = arr[1];
						$( "#carrier_num" ).autocomplete({
							source: availableTags.split(",")
						});
					} else {
						// availableTags = arr[1];
					}
				}
			});
		});



		// autocomplete share
		$('#order_num').live('keyup', function(){
			$.ajax({
				url: 'receiving_to_get_material.php?PHP_action=get_order_num',
				type: 'GET',
				data: {
					order_num : $('#order_num').val(),
					contentType: "application/x-www-form-urlencoded; charset=Big5"
				},
				error: function(xhr) {
					alert('Ajax request 發生錯誤'+xhr);
				},
				success: function(response) {
					var arr = decodeURI(response);
					arr = arr.split("@"); 
					if ( arr[0] == 'ok' ) {
						availableTags = arr[1];
						$( "#order_num" ).autocomplete({
							source: availableTags.split(",")
						});
					} else {
						// availableTags = arr[1];
					}
				}
			});
		});

		
		// <TR> MOUSEOVER
		$('tr[id^=List_TR]').hover(function(){
			$(this).addClass("List_TR");
		},function(){
			$(this).removeClass("List_TR");  
		});
		
		// <TR> MOUSEOVER
		$('tr[id^=List_TR_link]').hover(function(){
			$(this).addClass("List_TR_link");
		},function(){
			$(this).removeClass("List_TR_link");  
		});
		// <TR> CLICK
		$('#List_TR_link').live('click', function(){
			if( $(this).attr('link') ){
				$.blockUI({ message: ' Loding ...'});
				location.href = "incoming_material.php?PHP_action=incoming_material_search&carrier_num="+$(this).attr('link');
				$.unblockUI();
			}
		});
		
		// view stock
		$('#List_TR_link_inventory').live('click', function(){
			if( $(this).attr('ver') ){
				$.blockUI({ message: ' Loding ...'});
				location.href = "inventory.php?PHP_action=inventory_stock&ver="+$(this).attr('ver')+"&dept="+$(this).attr('fty')+"&item="+$(this).attr('item')+"&order="+$(this).attr('order');
				$.unblockUI();
			}
		});
		
		// edit remark
		$('#stock_remark').live('click', function(){
			var ver = $(this).attr('ver');
			var fty = $(this).attr('fty');
			var item = $(this).attr('item');

			$("#b_"+ver+fty+item).hide();
			$("#e_"+ver+fty+item).show();

		});
		
		// update remark
		$('#inventory_edit').live('click', function(){
			var ver = $(this).attr('ver');
			var fty = $(this).attr('fty');
			var item = $(this).attr('item');
			var text = $("#text_"+ver+fty+item).val();

			if( confirm(text) ){
				$.blockUI({ message: ' Loding ...'});
				$("#b_"+ver+fty+item).show();
				$("#e_"+ver+fty+item).hide();
			// var val = $(this).val();
			// if( ver ){
				// $.blockUI({ message: ' Loding ...'});
				location.href = "inventory.php?PHP_action=inventory_edit&ver="+ver+"&dept="+fty+"&item="+item+"&text="+escape(text);
				// $.unblockUI();
			// }
			}
		});
		
		// OPEN CLOSE
		$('#DO').click(function(){
			$.blockUI({ message: ' Loding ...'});
			$("#SH").toggle("slow");
			if( $('#DO').html() == 'OPEN' ) {
				$('#DO').html('CLOSE');
			} else {
				$('#DO').html('OPEN');
			}
			$.unblockUI();
		});
		
		// DETAIL
		select_checkbox = function(status){
			if ( status == 'Reply' ) {
				$("input[id^=po_ship_link_qty]").each(function(i, elm) {
					$(elm).val($("#MK_"+$(elm).attr("id")).val());
					
					re_avg_acpt_qty($(elm).attr('mid'), $(elm).val(), $("#unit_"+$(elm).attr('mid')).val());
					
				});
			} else {
				$("input[id^=po_ship_link_qty]").each(function(i, elm) {
					$(elm).val('0');
				});
				$("input[id^=order_qty_]").each(function(i, elm) {
					$(elm).val('0');
				});
			}
		}
		qPass = function(id){
			$("#po_ship_link_qty_"+id).val($("#MK_po_ship_link_qty_"+id).val());
		}
		qClear = function(id){
			$("#po_ship_link_qty_"+id).val(0);
		}
		
		incoming_chg = function(dept){
			$.blockUI({ message: ' Loding ...'});
			location.href = "incoming_material.php?PHP_action=incoming_material&PHP_dept="+$(dept).val()+"&active=1";
		}	
		
		chk_inventory = function(){
			//alert($('input[name=mat]:checked').val());
			var order = "";
			var item = "";
			var dept = "";
			if(typeof($('input[name=mat]:checked').val()) == "undefined")
			{
				order = $('#PHP_order').val();
				item = $('input[name=mat]:checked').val();
				dept = $('#PHP_dept').val();
			}
			else
			{
				$.blockUI({ message: ' Loding ...'});
				order = $('#PHP_order').val();
				item = $('input[name=mat]:checked').val();
				dept = $('#PHP_dept').val();
			}
			
			
			location.href = "inventory.php?PHP_action=inventory&item="+item+"&PHP_dept="+dept+"&PHP_order="+order;
		}
	
		// search notice notice_link
        $("tr #notice_link").live("click", function () {
           window.location = "?PHP_action=receiving_to_get_material_view&M_num="+$(this).attr('notice_num');
		});
		$('tr #notice_link').hover(function(){
			$(this).addClass("submit_over");
		},function(){
			$(this).removeClass("submit_over"); 
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');
		
		// adjustment notice notice_adjust_link
        $("tr #notice_adjust_link").live("click", function () {
           window.location = "?PHP_action=returned_material_adjustment_send_search&M_num="+$(this).attr('rn_num');
		});
		$('tr #notice_adjust_link').hover(function(){
			$(this).addClass("submit_over");
		},function(){
			$(this).removeClass("submit_over"); 
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');

		send_detail = function(bl_num, l_no, r_no, mat_id, color, size){
			var url = "returned_material_adjustment.php?PHP_action=send_detail&bl_num="+bl_num+"&l_no="+l_no+"&r_no="+r_no+"&mat_id="+mat_id+"&color="+color+"&size="+size;
			window.open(url,'send_detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=700,height=600 top=50, left=100');
		}
		
		chk_send_stock_qty = function(r,id){
			var stock_qty = Number($("#stock_qty"+id).val());
			var send_qty = Number(r.value);
			if(send_qty > stock_qty){
				r.value = stock_qty;
				alert("Qty[ " + send_qty + " ] > Stock Qty[ " + stock_qty + " ]")
			}
		}
		
		chk_return_qty = function(r,id){
			var o_send_qty = Number($("#o_send_qty_"+id).val());
			if(r.value > o_send_qty){
				r.value = o_send_qty;
				alert("進料數量[ " + r.value + " ] > 領料數量[ " + o_send_qty + " ]")
			}
		}
		
		send_detail_view = function(ord_num){
			var url = "bom_of_materials.php?PHP_action=send_detail_view&PHP_ord_num=" + ord_num;
			window.open2(url,"send_detail",'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=900,height=600 top=50, left=100');
		}
		
		chk_link_acpt_qty = function(id,link_id,val){
			ttl_qty = new Number(0.00);
			$('input[id^=link_order_qty_'+id+'_]').each(function(i,elm) {
				ttl_qty += Number($(elm).val())
			});
			ttl_qty = ttl_qty.toFixed(2);
			$("#po_ship_link_qty_" + id).val(ttl_qty);
			chk_acpt_qty(id);
			re_avg_link_acpt_qty(id, link_id, val, $("#unit_"+id).val());
		}
		
		re_avg_link_acpt_qty = function(id, link_id, i_qty, unit){
			var tmp_qty = 0;
			var avg_qty = new Number(0.00);
			var final_avg_qty = new Number(0.00);
			$('input[id^=percent_'+id+'_]').each(function(i,elm) {
				if($(elm).val()){
					avg_qty = i_qty * $(elm).val();
					avg_qty = (unit == 'pc' ? avg_qty.toFixed(0) : avg_qty.toFixed(2));
					$("#link_det_order_qty_"+id+"_link_det_"+link_id+"_"+i).val(avg_qty);
					tmp_qty += Number(avg_qty);
				}else{
					final_avg_qty = i_qty - tmp_qty;
					final_avg_qty = final_avg_qty.toFixed(2);
					$("#link_det_order_qty_"+id+"_link_det_"+link_id+"_"+i).val(final_avg_qty);
				}
			});
			//chk_acpt_avg_qty(id);
			re_avg_acpt_qty(id, $("#po_ship_link_qty_"+id).val(), $("#unit_"+id).val());
		}
		
		chk_acpt_qty = function(id){
			acpt_qty = Number($("#po_ship_link_qty_"+id).val());
			acpted_qty = Number($("#acpted_qty_"+id).val());
			ship_qty = Number($("#ship_qty_"+id).val());
			
			if( acpt_qty + acpted_qty > ship_qty ){
				alert(acpt_qty + " + " + acpted_qty + " > " + ship_qty);
				$("#po_ship_link_qty_"+id).val(ship_qty - acpted_qty);
			}
			
			re_avg_acpt_qty(id, $("#po_ship_link_qty_"+id).val(), $("#unit_"+id).val());
		}
		
		var qty_error_ary = new Array();
		chk_acpt_avg_qty = function(id){
			var remain_qty = new Number($("#po_ship_link_qty_"+id).val());
			var ttl_avg_qty = new Number(0);
			$('input[id^=order_qty_'+id+'_]').each(function(i,elm) {
				ttl_avg_qty += Number($(elm).val());
			});
			
			var rtn = $.inArray(id, qty_error_ary);
			if(ttl_avg_qty.toFixed(2) != remain_qty.toFixed(2)){
				if(rtn == -1){
					qty_error_ary.push(id);
				}
				$("#append_incoming_qty").attr("disabled",true);
				$("#td_avg_"+id).css("background-color","#77FF00");
			}else{
				qty_error_ary.splice(rtn, 1);
				$("#td_avg_"+id).css("background-color","#EEEEEE");
				if(qty_error_ary.length == 0){
					$("#append_incoming_qty").attr("disabled",false);
				}
			}
		}
		
		re_avg_acpt_qty = function(id, i_qty, unit){
			var tmp_qty = 0;
			var avg_qty = new Number(0.00);
			var final_avg_qty = new Number(0.00);
			$('input[id^=percent_'+id+'_]').each(function(i,elm) {
				if($(elm).val()){
					avg_qty = i_qty * $(elm).val();
					avg_qty = (unit == 'pc' ? avg_qty.toFixed(0) : avg_qty.toFixed(2));
					$("#order_qty_"+id+"_"+i).val(avg_qty);
					tmp_qty += Number(avg_qty);
				}else{
					final_avg_qty = i_qty - tmp_qty;
					final_avg_qty = final_avg_qty.toFixed(2);
					$("#order_qty_"+id+"_"+i).val( final_avg_qty );
				}
			});
			chk_acpt_avg_qty(id);
		}
		
		var adjust_qty_error_ary = new Array();
		chk_adjust_avg_qty = function(id,send_qty, obj){
			if( Number(send_qty) > Number(obj.value)){
				alert( "Already send Qty : " + send_qty + "\n Input Qty must >= " + send_qty );
				obj.value = send_qty;
				chk_adjust_avg_qty(id, Number(send_qty), obj);
				return false;
			}
			var i_qty = new Number($("#stock_i_qty_"+id).val());
			var ttl_avg_qty = new Number(0);
			$('input[id^=order_qty_'+id+'_]').each(function(i,elm) {
				ttl_avg_qty += Number($(elm).val());
			});
			
			var rtn = $.inArray(id, adjust_qty_error_ary);
			if(ttl_avg_qty.toFixed(2) != i_qty.toFixed(2)){
				if(rtn == -1){
					adjust_qty_error_ary.push(id);
				}
				$("#update_incoming_qty").attr("disabled",true);
				$("#td_avg_"+id).css("background-color","#77FF00");
			}else{
				adjust_qty_error_ary.splice(rtn, 1);
				$("#td_avg_"+id).css("background-color","#EEEEEE");
				if(adjust_qty_error_ary.length == 0){
					$("#update_incoming_qty").attr("disabled",false);
				}
			}
		}
	});
})(jQuery);