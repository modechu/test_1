var keyctrl = false ;
var keyshift = false;
var keyalt = false;
var drag_bar_status = false;
var dragline = '';
var dragline_id = '';
var line_down = '';
var line_up = '';
var line_id_down = '';
var line_id_up = '';
var event_table = 'bar_main';
var order_now;
var order_down;
var order_up;
var s_id_down;
var s_id_up;
var p_id_down;
var p_id_up;
var available;
var availableTags;

var add_order;
var add_s_qty;
var add_qty;

var add_line_id;
var add_line;

var auto_order = false;
var ms = false;

var ms_status;
var ms_statusd;

function mask(){
    $("body").append('<div id="mask"></div>').css({'overflow-y':'hidden','overflow-x':'hidden'});
    $("#mask").css({
        "background-color" : "#000000" ,  
        "width" : $(document).width() , 
        "height" : $(document).height() , 
        "position": "absolute" , 
        "opacity": 0.6 , 
        "top": -1 , 
        "left": -1 , 
        "z-index": "2" 
    });
}





(function($){

	$(document).ready(function(){
        var enter_excute = false;
        //if($.browser.msie) 
        //{
        //    $('#main_order').keydown(function(event){   
        //    if(event.keyCode==13){  //如果按 enter    
        //        alert('Enter Event');
        //        enter_excute = true;
        //    }
        //    });
        //}
		$(".cls_static_IntValidate").keyup(function(){    
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		}).bind("paste",function(){  //CTR+V事件處理    
			$(this).val($(this).val().replace(/\D|^0/g,''));     
		}).css("ime-mode", "disabled"); //CSS設置輸入法不可用    
			
		$('#po_carton_begin').blur(function(){
			var start = $(this).val();
			var end = $('#po_carton_end').val();
			/* var po = $('#main_order').val();
			var shipdate = $('#po_calendar-inputField').val(); */
			if($.trim($('#po_carton_begin').val())!='' && $.trim($('#po_carton_end').val())!='')
			{
				if(Number(start) > Number(end))
				{
					alert('[Carton NO. End] must be greater [Carton NO. Start]');
				}
				
				/* if($.trim($('#main_order').val())!='')
				{
					$.post("./packing_edit.php?PHP_action=check_carton",{PHP_po:po,PHP_shipdate:shipdate,PHP_carton_start:start,PHP_carton_end:end},function(data) {
						alert(data);
					});
				} */
			}
		});
		$('#po_carton_end').blur(function(){
			var end = $(this).val();
			var start =$('#po_carton_begin').val();
			/* var po = $('#main_order').val();
			var shipdate = $('#po_calendar-inputField').val(); */
			if($.trim($('#po_carton_begin').val())!='' && $.trim($('#po_carton_end').val())!='')
			{
				if(Number(start) > Number(end))
				{
					alert('[Carton NO. End] must be greater [Carton NO. Start]');
				}
				/* if($.trim($('#main_order').val())!='')
				{
					$.post("./packing_edit.php?PHP_action=check_carton",{PHP_po:po,PHP_shipdate:shipdate,PHP_carton_start:start,PHP_carton_end:end},function(data) {
						alert(data);
					});
				} */
			}
			
		});
		$('#scm_carton_begin').blur(function(){
			var start = $(this).val();
			var end = $('#scm_carton_end').val();
			if($.trim($('#scm_carton_begin').val())!='' && $.trim($('#scm_carton_end').val())!='')
			{
				if(Number(start) > Number(end))
				{
					alert('[Carton NO. End] must be greater [Carton NO. Start]');
				}
				
			}
			
		});
		$('#scm_carton_end').blur(function(){
			var end = $(this).val();
			var start = $('#scm_carton_begin').val();
			if($.trim($('#scm_carton_begin').val())!='' && $.trim($('#scm_carton_end').val())!='')
			{
				if(Number(start) > Number(end))
				{
					alert('[Carton NO. End] must be greater [Carton NO. Start]');
				}
			}
		});
		$('.single_save').click(function() { //_save

			if($('#main_option').val()=='PO')
			{
				//尚未完成，檢查箱號是否為數字，將訂單號轉成大寫
				var carton_start = $('#po_carton_begin').val();
				var carton_end = $('#po_carton_end').val();
				var packstyle = $("#po_packstyle").find(":selected").val();
				var hanger = "";
				if($('#po_hanger').is(":checked")){
					hanger = "checked";
				}
				else
				{
					hanger = "unchecked";
				} 
				var shipdate = $('#po_calendar-inputField').val();
				var shipto = $("#po_consignee").find(":selected").val();
				
				var cust = $("#PHP_cust").find(":selected").val();
				var option = 'PO';
				var po_order = $('#main_order').val();
				var scm_order = $("#po_ord1_order").val();
				//var ucc = $("#po_ord1_ucc").val();
				var ucc = "";
				var prepack = $("#po_ord1_prepack").val();
				var partial = $("#po_ord1_partial_select").find(":selected").val();
				var shipway = $("#PHP_shipway").find(":selected").val();
				var sizecolor = $("#po_ord1_colorsize").val();
				var sizecolor_array = sizecolor.split("_");
				var color_length = sizecolor_array[0];
				var size_length = sizecolor_array[1];
				var colorsize = new Array(color_length);
				for(colori=0;colori<color_length;colori++){
					colorsize[colori]= new Array();
					for(sizei=0;sizei<size_length;sizei++){
						colorsize[colori][sizei]="";
					}
				}
				var colorint = 0; 
				var sizeint = 0; 
				
				if($("#PHP_cust").find(":selected").val() == '')
				{
					alert('customer can not empty!!');
				}
				else
				{
					if($("#main_order").val() == '')
					{
						alert('Order NO. can not empty!!');
					}
					else
					{
						if($("#po_carton_begin").val() == '' || $("#po_carton_end").val() == ''  )
						{
							alert('Carton NO. can not empty!!');
						}
						else
						{
							
							
							var start = $("#po_carton_begin").val();
							var end =$("#po_carton_end").val();
							if(Number(start) > Number(end)){
								alert("[Carton NO. End] must be greater [Carton NO. Start]");
							} else {

								if($("#consignee_select").find(":selected").val() == '')
								{
									alert('[SHIP To] can not empty!!');
								}
								else
								{
									
									if($("#PHP_shipway").find(":selected").val() == '')
									{
										alert('[SHIP Way] can not empty!!');
									}
									else
									{
									
									
									
									
									
									
									
										
										if($("#po_ord1_partial_select").find(":selected").val() == '')
										{
											alert('[Partial] can not empty!!');
										}
										else
										{
											var totalqty=0;
											$("input[name^='po[1]']").each(function(){ 
												if( colorint < color_length){
													if( sizeint < size_length){
														if($(this).val() == ""){
															colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";
														} else {
															colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();
														}
														sizeint++;
													} else {
														colorint++;
														sizeint=0;
														if($(this).val() == ""){
															colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";
														} else {
															colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();
														}
														sizeint++;
													}
												}
												totalqty = totalqty + Number(($(this).val()==''?0:$(this).val()));
												
											});
											//alert(colorsize);
											if(totalqty > 0)
											{
												$.post("./packing_edit.php?PHP_action=packlist_save_one",{PHP_cust:cust,PHP_option:option,PHP_po_order:po_order,PHP_scm_order:scm_order,PHP_ucc:ucc,PHP_prepack:prepack,PHP_partial:partial,PHP_sizebreakdown:colorsize,PHP_carton_start:carton_start,PHP_carton_end:carton_end,PHP_packstyle:packstyle,PHP_hanger:hanger,PHP_shipdate:shipdate,PHP_shipto:shipto,PHP_shipway:shipway},function(data) {
													//alert(data);
													
													if(data=='finish')
													{
														$("table#tbl_po_carton_edit tr#po_ord1_tr").remove();
														//alert(data);//將剛剛存入的,以PO為主帶出呈現
														if($.trim($('#main_order').val()) != ''){
															html_existpo_view($.trim($('#main_order').val()));
														}
													}
													else
													{
														alert(data);
													}
												});
											}
											else
											{
												 alert("No qty in this row,please check again!!");
											}
										}
									}
							
							
								}
							
							}
							
						}
					}
				} 
				
				
			}
			else
			{
				//alert($(this).attr('idx'));
				//PHP_cust:cust,PHP_option:option,PHP_po_order:po_order,PHP_scm_order:scm_order,PHP_ucc:ucc,PHP_prepack:prepack,PHP_partial:partial,PHP_sizebreakdown:colorsize,PHP_carton_start:carton_start,PHP_carton_end:carton_end,PHP_packstyle:packstyle,PHP_hanger:hanger,PHP_shipdate:shipdate,PHP_shipto:shipto
				var cust = $("#PHP_cust").find(":selected").val();
				var option = 'SCM';
				var scm_order = $('#main_order').val();
				var po_order = $("#scm_ord1_order").val();
				var ucc = $("#scm_ord1_ucc").val();
				var prepack = $("#scm_ord1_prepack").val();
				var partial = $("#scm_ord1_partial_select").find(":selected").val();
				var shipway = $("#scm_ord1_shipway_select").find(":selected").val();
				var sizecolor = $("#scm_ord1_colorsize").val();
				var packstyle = $("#scm_ord1_packstyle").find(":selected").val();
				var hanger = "";
				var shipto = $("#scm_ord1_shipto").find(":selected").val();
				var shipdate = $("#scm_ord_calendar-inputField").val();
				if($('#scm_ord1_hanger').is(":checked")){
					hanger = "checked";
				}
				else
				{
					hanger = "unchecked";
				}
				var sizecolor_array = sizecolor.split("_");
				var color_length = sizecolor_array[0];
				var size_length = sizecolor_array[1]; 
				var colorsize = new Array(color_length);
				for(colori=0;colori<color_length;colori++){
					colorsize[colori]= new Array();
					for(sizei=0;sizei<size_length;sizei++){
						colorsize[colori][sizei]="";
					}
				}
				var carton_start = $('#scm_carton_begin').val();
				var carton_end = $('#scm_carton_end').val();
				var colorint = 0; 
				var sizeint = 0; 
				
				
				if($("#PHP_cust").find(":selected").val() == '')
				{
					alert('customer can not empty!!');
				}
				else
				{
					if($("#main_order").val() == '')
					{
						alert('[Order NO.] can not empty!!');
					}
					else
					{
						if($("#scm_carton_begin").val() == '' || $("#scm_carton_end").val() == ''  )
						{
							alert('[Carton NO.] can not empty!!');
						}
						else
						{
							
							var start = $("#scm_carton_begin").val();
							var end =$("#scm_carton_end").val();
							if(Number(start) > Number(end)){
								alert("[Carton NO. End] must be greater [Carton NO. Start]");
							} else {
							
							
							
							
							
							
								if($("#scm_ord1_order").val() == '')
								{
									alert('[P.O.] can not empty!!');
								}
								else
								{
								
									if($("#scm_ord1_shipway_select").find(":selected").val() == '')
									{
										alert('[SHIP Way] can not empty!!');
									}
									else
									{
								
								
								
								
								
										if($("#scm_ord1_shipto").find(":selected").val() == '')
										{
											alert('[SHIP To] can not empty!!');
										}
										else
										{
											if($("#scm_ord1_partial_select").find(":selected").val() == '')
											{
												alert('[Partial] can not empty!!');
											}
											else
											{
												var totalqty=0;
												$("input[name^=\'scm[1]\']").each(function(){ 
													if( colorint < color_length){
														if( sizeint < size_length){
															if($(this).val() == ""){
																colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";
															} else {
																colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();
															}
															sizeint++;
														} else {
															colorint++;
															sizeint=0;
															if($(this).val() == ""){
																colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";
															} else {
																colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();
															}
															sizeint++;
														}
													}
													totalqty = totalqty + Number(($(this).val()==''?0:$(this).val()));												
												});
												/* $.post("./packing_edit.php?PHP_action=packlist_save_one",{PHP_cust:cust,PHP_option:option,PHP_po_order:po_order,PHP_scm_order:scm_order,PHP_ucc:ucc,PHP_prepack:prepack,PHP_partial:partial,PHP_sizebreakdown:colorsize,PHP_carton_start:carton_start,PHP_carton_end:carton_end,PHP_packstyle:packstyle,PHP_hanger:hanger,PHP_shipdate:shipdate,PHP_shipto:shipto},function(data) {
													alert(data);
												}); */
												
												
												if(totalqty > 0)
												{
													$.post("./packing_edit.php?PHP_action=packlist_save_one",{PHP_cust:cust,PHP_option:option,PHP_po_order:po_order,PHP_scm_order:scm_order,PHP_ucc:ucc,PHP_prepack:prepack,PHP_partial:partial,PHP_sizebreakdown:colorsize,PHP_carton_start:carton_start,PHP_carton_end:carton_end,PHP_packstyle:packstyle,PHP_hanger:hanger,PHP_shipdate:shipdate,PHP_shipto:shipto,PHP_shipway:shipway},function(data) {
														//alert(data);
														
														if(data=='finish')
														{
															$("table#tbl_scm_carton_edit tr#scm_ord1_tr").remove();
														}
														else
														{
															alert(data);
														}
													});
												}
												else
												{
													 alert("No qty in this row,please check again!!");
												}
											}
										}
									}
								}							
							}
						}
					}
				} 
				
			}
		});
		$('.single_delete').click(function() {
			//alert($(this).attr('idx'));
			if($('#main_option').val()=='PO')
			{
				$("table#tbl_po_carton_edit tr#po_ord1_tr").remove();
			}
			else
			{
				$("table#tbl_scm_carton_edit tr#scm_ord1_tr").remove();
			}
		});
		//var ikeypress=0;
		$('.po_order').bind('change keypress blur',function(e){
			if(e.type=='keypress')
			{
				if(e.keyCode == 13)
				{
					cust=$("#PHP_cust").find(":selected").val();
					change_val=$(this).val();
					iOrder = $(this).val();
					iIndex = $("#scm_ord1_tr").attr("idx");
					$.post("./packing_edit.php?PHP_action=get_partial",{PHP_order:iOrder,PHP_rowindex:iIndex,PHP_order_type:'PO'},function(data) {
						$("#po_ord1_partial").html(data);
						$("#po_ord1_sizebreakdown").html("");
					});
					if(cust != '')
					{
						$.post("./packing_edit.php?PHP_action=consignee",{cust_id:cust,PHP_order:iOrder},function(data) {
							$("#po_consignee").html(data);
						});
					}
					
				}
			}

		});

		$('.scm_order').bind('change keypress',function(e){
			if(e.type=='keypress')
			{
				if(e.keyCode == 13)
				{
					change_val=$(this).val();
					alert('13');
				}
			}

		});
		function html_cust_consignee(cust,trans_index,scm_order)
		{
			$.post('./packing_edit.php?PHP_action=consignee',{cust_id:cust,rowindex:trans_index,PHP_order_type:'SCM',PHP_order:scm_order},function(data) {
				//_shipto
				$('#scm_ord'+trans_index+'_shipto').html(data);
			}); 
		}
		$("#PHP_cust").change(function() {
			$('#po_consignee').html("");
			cust = $("#PHP_cust").find(":selected").val();
			scm_order = $("#main_order").val();
			if($('#main_option').val()=='PO')
			{
				//PO為主可能有多訂單
				var trlength = $('#tbl_po_carton_edit tr').length;
				//alert(trlength);
				if(trlength > 1)
				{
					myidx = $('#tbl_po_carton_edit').find('tr:eq(1)').attr('idx');
					myord = $.trim($('#po_ord'+myidx+'_order').val());
					if(myord != '')
					{
						$.post('./packing_edit.php?PHP_action=consignee',{cust_id:cust,PHP_order:myord},function(data) {
							$('#po_consignee').html(data);
							//html_shipway()
						}); 
					}
				}
				
			}
			else
			{
				/**查詢訂單的Partial**/
				firstidx = $('#tbl_scm_carton_edit').find('tr:eq(1)').attr('idx');
				//lastidx = $('#tbl_scm_carton_edit').find('tr:last').attr('idx');
				trlength = $('#tbl_scm_carton_edit tr').length;
				nowidx = "";
				if(cust != '' && scm_order !='')
				{
					for(iIndex=firstidx;iIndex<=trlength;iIndex++ )
					{
						if(nowidx != $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') || $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') != "nouse")
						{
							nowidx = $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx');
							
							html_cust_consignee(cust,$('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx'),scm_order);
							html_shipway($('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx'));
						}
						
					}
				}
				
			}
		});
		function html_partial(myOrder,trans_index)
		{
			//alert(trans_index);
			$.post("./packing_edit.php?PHP_action=get_partial",{PHP_order:myOrder,PHP_rowindex:trans_index,PHP_order_type:'SCM'},function(data) {
				$('#scm_ord'+trans_index+'_partial').html(data);
				$('#scm_ord'+trans_index+'_sizebreakdown').html("");
				//alert(data);
			});
			//alert(trans_index);
		}
		function html_shipway(trans_index)
		{
			$.post("./packing_edit.php?PHP_action=get_shipway",{PHP_rowindex:trans_index,PHP_order_type:'SCM'},function(data) {
				$('#scm_ord'+trans_index+'_shipway').html(data);
				//alert(data);
			});
		}
		
		function html_existpo_view(po_no)
		{
			//alert('test');
			$.post("./packing_edit.php?PHP_action=get_packing_po_view",{PHP_po_order:po_no},function(data) {
				//$('#scm_ord'+trans_index+'_shipway').html(data);
				//alert(data);
				$('#carton_list').html(data);
				//alert(data);
			}); 
		}
		/* function html_existpo_view(po_no)
		{
	
			 $.post("./packing_edit.php?PHP_action=get_packing_po_view",{PHP_po_order:po_no},function(data) {
				//$('#scm_ord'+trans_index+'_shipway').html(data);
				alert(data);
			}); 
		} */
		/* function html_existpo4scmno_view('PO')
		{
			
		} */
		
		$('#main_order').keypress(function(e){
			var code=e.keyCode;
			
			if(code==13)
			{
			
				if($('#main_option').val()=='PO')
				{
					$('#carton_standard_info_4PO').css('display','block');
					$('#carton_standard_info_4SCM').css('display','none');
					$('#tbl_po_carton_edit').css('display','block');
					$('#tbl_scm_carton_edit').css('display','none');
					$('#btn_new_record').css('width',$('#tbl_po_carton_edit').width()+8);
					$('#btn_save').css('width',$('#tbl_po_carton_edit').width()+8);
					
					$('#td_btn_new_record').css('display','block');
					$('#td_btn_save').css('display','block');
					//帶出資料庫已存在此PO的資料
					//html_existpo_view('PO')
					//if($.trim($('#main_order').val()) != '')
					//{
						//html_existpo_view($.trim($('#main_order').val()));
					//}
					if($.trim($('#main_order').val()) != ''){
					html_existpo_view($.trim($('#main_order').val()));
					}
				}
				else
				{
					if($.trim($('#main_order').val()) == '')
					{
						alert('Can not empty!!');
					}
					else
					{
						$('#carton_standard_info_4PO').css('display','none');
						$('#carton_standard_info_4SCM').css('display','block');
						$('#tbl_po_carton_edit').css('display','none');
						$('#tbl_scm_carton_edit').css('display','block');
						$('#btn_new_record').css('width',$('#tbl_scm_carton_edit').width()+8);
						$('#btn_save').css('width',$('#tbl_scm_carton_edit').width()+8);
						/**查詢訂單的Partial**/
						firstidx = $('#tbl_scm_carton_edit').find('tr:eq(1)').attr('idx');
						lastidx = $('#tbl_scm_carton_edit').find('tr:last').attr('idx');
						trlength = $('#tbl_scm_carton_edit tr').length;//alert($('#tbl_scm_carton_edit tr').length);
						nowidx="";
						for(iIndex=firstidx;iIndex<=trlength;iIndex++ )
						{
							if(nowidx != $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') || $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') != "nouse")
							{
								nowidx = $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx');
								html_partial($('#main_order').val(),$('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx'));
							}
						}
						$('#td_btn_new_record').css('display','block');
						$('#td_btn_save').css('display','block');
					}
					//帶出資料庫以存在此訂單的所有PO資料
					//html_existpo4scmno_view('PO')
					
				}
			}
		});
		$('#main_option').change(function() {
			if($('#main_option').find(":selected").val() == 'PO')
			{
				
				//function html_cust_consignee(cust,trans_index)
				cust = $("#PHP_cust").find(":selected").val();
				if(cust != '')
				{
					var trlength = $('#tbl_po_carton_edit tr').length;
					if(trlength > 1)
					{
						myidx = $('#tbl_po_carton_edit').find('tr:eq(1)').attr('idx');
						myord = $.trim($('#po_ord'+myidx+'_order').val());
						if(myord !='')
						{
							$.post('./packing_edit.php?PHP_action=consignee',{cust_id:cust,PHP_order:myord},function(data) {
								$('#po_consignee').html(data);
							}); 
						}
					}
				}
				$('#carton_standard_info_4PO').css('display','block');
				$('#carton_standard_info_4SCM').css('display','none');
				$('#tbl_po_carton_edit').css('display','block');
				$('#tbl_scm_carton_edit').css('display','none');
				//alert($('#tbl_po_carton_edit').width());
				$('#btn_new_record').css('width',$('#tbl_po_carton_edit').width()+8);
				$('#btn_save').css('width',$('#tbl_po_carton_edit').width()+8);
			}
			else
			{	
				cust = $("#PHP_cust").find(":selected").val();
				scm_order = $("#main_order").val();
				if(cust != '' && scm_order !='')
				{
					firstidx = $('#tbl_scm_carton_edit').find('tr:eq(1)').attr('idx');
					//lastidx = $('#tbl_scm_carton_edit').find('tr:last').attr('idx');
					trlength = $('#tbl_scm_carton_edit tr').length;
					nowidx = "";
					for(iIndex=firstidx;iIndex<=trlength;iIndex++ )
					{
						if(nowidx != $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') || $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') != "nouse")
						{
							nowidx = $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx');
							//trans_index = $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx');
							html_cust_consignee(cust,nowidx,scm_order)
							html_shipway(nowidx);
						}
					}
				}
				
				scm_order = $('#main_order').val();
				if($.trim(scm_order) != '')
				{
					firstidx = $('#tbl_scm_carton_edit').find('tr:eq(1)').attr('idx');
					//lastidx = $('#tbl_scm_carton_edit').find('tr:last').attr('idx');
					trlength = $('#tbl_scm_carton_edit tr').length;
					nowidx = "";
					for(iIndex=firstidx;iIndex<=trlength;iIndex++ )
					{
						if(nowidx != $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') || $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx') != "nouse")
						{
							nowidx = $('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx');
							html_partial($('#main_order').val(),$('#tbl_scm_carton_edit').find('tr:eq('+iIndex+')').attr('idx'));
						}
					}
				}
				$('#carton_standard_info_4PO').css('display','none');
				$('#carton_standard_info_4SCM').css('display','block');
				$('#tbl_po_carton_edit').css('display','none');
				$('#tbl_scm_carton_edit').css('display','block');
				//alert($('#tbl_scm_carton_edit').width());
				$('#btn_new_record').css('width',$('#tbl_scm_carton_edit').width()+8);
				$('#btn_save').css('width',$('#tbl_scm_carton_edit').width()+8);
			}
			$('#td_btn_new_record').css('display','block');
			$('#td_btn_save').css('display','block');
			
		});
		$('#btn_new_record').click(function(){
			//newrow_index = parseInt($('#order_table tr').index()) + 1;
			//alert($('#tbl_scm_carton_edit').find('tr:eq(1)').attr('idx'));//SCM這塊的shipto要作迴圈塞入,第一筆資料的index
			//alert($('#tbl_po_carton_edit').find('tr:last').attr('idx'));//SCM這塊的shipto要作迴圈塞入,最後一筆資料的index
			//alert($('#tbl_po_carton_edit tr').index());
			var newindex = 0;
			
			if($('#main_option').val()=='PO')
			{
				if(isNaN($('#tbl_po_carton_edit').find('tr:last').attr('idx')))
				{
					newindex = 1;
				}
				else
				{
					newindex = Number($('#tbl_po_carton_edit').find('tr:last').attr('idx'))+1 ;
				}
			}
			else
			{
				if(isNaN($('#tbl_scm_carton_edit').find('tr:last').attr('idx')))
				{
					newindex = 1;
				}
				else
				{
					newindex = Number($('#tbl_scm_carton_edit').find('tr:last').attr('idx'))+1 ;
				}
			}
			var newtr='';
			var newjquery='';
			
			/*******日期格式化Function*******/
			/********************************/
			 Date.prototype.pattern=function(fmt) {         
				var o = {         
				"M+" : this.getMonth()+1, //月份         
				"d+" : this.getDate(), //日         
				"h+" : this.getHours()%12 == 0 ? 12 : this.getHours()%12, //小?         
				"H+" : this.getHours(), //小?         
				"m+" : this.getMinutes(), //分         
				"s+" : this.getSeconds(), //秒         
				"q+" : Math.floor((this.getMonth()+3)/3), //季度         
				"S" : this.getMilliseconds() //毫秒         
				};         
				var week = {         
				"0" : "/u65e5",         
				"1" : "/u4e00",         
				"2" : "/u4e8c",         
				"3" : "/u4e09",         
				"4" : "/u56db",         
				"5" : "/u4e94",         
				"6" : "/u516d"        
				};         
				if(/(y+)/.test(fmt)){         
					fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));         
				}         
				if(/(E+)/.test(fmt)){         
					fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "/u661f/u671f" : "/u5468") : "")+week[this.getDay()+""]);         
				}         
				for(var k in o){         
					if(new RegExp("("+ k +")").test(fmt)){         
						fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));         
					}         
				}         
				return fmt;         
			}   
			/*******日期格式化Function*******/
			var newdate = new Date();
			//var newdates = new Date();
			if($('#main_option').find(":selected").val() == 'PO')
			{
				//alert($('#tbl_po_carton_edit tr').index());
				
				newtr += '<tr id="po_ord'+newindex+'_tr" idx="'+newindex+'">';
				newtr += '<td><input type="text" id="po_ord'+newindex+'_order" name="po_ord'+newindex+'_order" value=""/></td>';
				newtr += '<td><div id="po_ord'+newindex+'_partial"></div></td>';
				newtr += '<td><div id="po_ord'+newindex+'_sizebreakdown"></div></td>';
				//newtr += '<td><input type="text" id="po_ord'+newindex+'_ucc" name="po_ord'+newindex+'_ucc" value=""/></td>';
				newtr += '<td><input type="text" id="po_ord'+newindex+'_prepack" name="po_ord'+newindex+'_prepack" value=""/></td>';
				newtr += '<td><img src="images/Save.png"  id="po_ord'+newindex+'_save" class="single_save" idx="'+newindex+'" border="0"><img src="images/trashcan.png"  id="po_ord'+newindex+'_delete" class="single_delete" idx="'+newindex+'" border="0"></td>';
				newtr += '<input type="hidden" id="PHP_po_index'+newindex+'" name="PHP_po_index[]" value="'+newindex+'">';
				newtr += '</tr>';
				newjquery += '<script language="javascript">';

					newjquery += '$("#po_ord'+newindex+'_save").click(function() {';
					
						newjquery += 'var carton_start = $("#po_carton_begin").val();';
						newjquery += 'var carton_end = $("#po_carton_end").val();';
						newjquery += 'var packstyle = $("#po_packstyle").find(":selected").val();';
						newjquery += 'var hanger = "";';
						
						newjquery += 'if($("#po_hanger").is(":checked")){';
							newjquery += 'hanger = "checked";';
						newjquery += '} else { ';
							newjquery += 'hanger = "unchecked";';
						newjquery += '} ';
						newjquery += 'var shipdate = $("#po_calendar-inputField").val();';
						newjquery += 'var shipto = $("#po_consignee").find(":selected").val();';
						newjquery += 'var shipway = $("#PHP_shipway").find(":selected").val();';
						newjquery += 'var cust = $("#PHP_cust").find(":selected").val();';
						newjquery += 'var option = "PO";';
						newjquery += 'var po_order = $("#main_order").val();';
						newjquery += 'var scm_order = $("#po_ord'+newindex+'_order").val();';
						//newjquery += 'var ucc = $("#po_ord'+newindex+'_ucc").val();';
						newjquery += 'var ucc = "";';
						newjquery += 'var prepack = $("#po_ord'+newindex+'_prepack").val();';
						newjquery += 'var partial = $("#po_ord'+newindex+'_partial_select").find(":selected").val();';
						newjquery += 'var sizecolor = $("#po_ord'+newindex+'_colorsize").val();';
						
						newjquery += 'var sizecolor_array = sizecolor.split("_");';
						newjquery += 'var color_length = sizecolor_array[0];';
						newjquery += 'var size_length = sizecolor_array[1];';
						
						newjquery += 'var colorsize = new Array(color_length);';
					 	newjquery += 'for(colori=0;colori<color_length;colori++){';
						newjquery += 'colorsize[colori]= new Array();';
							newjquery += 'for(sizei=0;sizei<size_length;sizei++){';
								newjquery += 'colorsize[colori][sizei]="";';
							newjquery += '}';
						newjquery += '}';
						newjquery += 'var colorint = 0; ';
						newjquery += 'var sizeint = 0; ';
						
						
						newjquery += 'if($("#PHP_cust").find(":selected").val() == "") {';
						newjquery += 'alert("customer can not empty!!");';
						newjquery += '} else {';
						
							newjquery += 'if($("#main_order").val() == ""){';
							newjquery += 'alert("Order NO. can not empty!!");';
							newjquery += '}	else {';
							
								newjquery += 'if($("#po_carton_begin").val() == "" || $("#po_carton_end").val() == ""){';
								newjquery += 'alert("Carton NO. can not empty!!");';
								newjquery += '} else {';
								
								
								
								
									newjquery += 'var start = $("#po_carton_begin").val();';
									newjquery += 'var end = $("#po_carton_end").val();';
									//newjquery += 'alert("Start:"+start+";end:"+end);';
									newjquery += 'if(Number(start) > Number(end)){';
										newjquery += 'alert("[Carton NO. End] must be greater [Carton NO. Start]");';
									newjquery += '} else {'; 
									
									//shipway
										newjquery += 'if($("#PHP_shipway").find(":selected").val() == ""){';
										newjquery += 'alert("[SHIP Way] can not empty!!");';
										newjquery += '} else {';
									
											newjquery += 'if($("#consignee_select").find(":selected").val() == ""){';
											newjquery += 'alert("[SHIP To] can not empty!!");';
											newjquery += '} else {';
											
												newjquery += 'if($("#po_ord'+newindex+'_partial_select").find(":selected").val() == ""){';
												newjquery += 'alert("[Partial] can not empty!!");';
												newjquery += '}	else {';
													newjquery += 'var totalqty=0;';
													newjquery += '$("input[name^=\'po['+newindex+']\']").each(function(){ ';
														newjquery += 'if( colorint < color_length){';
															newjquery += 'if( sizeint < size_length){';
																newjquery += 'if($(this).val() == ""){';
																	newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";';
																newjquery += '} else {';
																	newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();';
																newjquery += '}';
																newjquery += 'sizeint++;';
															newjquery += '} else {';
																newjquery += 'colorint++;';
																newjquery += 'sizeint=0;';
																newjquery += 'if($(this).val() == ""){';
																	newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";';
																newjquery += '} else {';
																	newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();';
																newjquery += '}';
																newjquery += 'sizeint++;';
															newjquery += '}';
														newjquery += '}';	
														newjquery += 'totalqty = totalqty + Number(($(this).val()==""?0:$(this).val()));';
													newjquery += '});';
													
													newjquery += 'if(totalqty > 0){';
														newjquery += '$.post("./packing_edit.php?PHP_action=packlist_save_one",{PHP_cust:cust,PHP_option:option,PHP_po_order:po_order,PHP_scm_order:scm_order,PHP_ucc:ucc,PHP_prepack:prepack,PHP_partial:partial,PHP_sizebreakdown:colorsize,PHP_carton_start:carton_start,PHP_carton_end:carton_end,PHP_packstyle:packstyle,PHP_hanger:hanger,PHP_shipdate:shipdate,PHP_shipto:shipto,PHP_shipway:shipway},function(data) {';
															//newjquery += 'alert(data);';
															newjquery += 'if(data=="finish"){';
																newjquery += '$("table#tbl_po_carton_edit tr#po_ord'+newindex+'_tr").remove();';
																//newjquery += 'alert(data);';
																newjquery += '$.post("./packing_edit.php?PHP_action=get_packing_po_view",{PHP_po_order:po_order},function(data) {';
																	newjquery += '$("#carton_list").html(data);';
																newjquery += '});';
																
															newjquery += '}	else {';
																newjquery += 'alert(data);';
															newjquery += '}';
														newjquery += '});';
													newjquery += '} else {';
														newjquery += 'alert("No qty in this row,please check again!!");';
													newjquery += '}';
													
												newjquery += '}';
											newjquery += '}';
										
										newjquery += '}';
									newjquery += '}';
								newjquery += '}';
							newjquery += '}';
						newjquery += '}';
					newjquery += '});';
					
					
					newjquery += '$("#po_ord'+newindex+'_delete").click(function() {';
						//newjquery += 'alert($(this).attr("idx"));';
						newjquery += '$("table#tbl_po_carton_edit tr#po_ord'+newindex+'_tr").remove();';
					newjquery += '});';
					
					newjquery += '$("#po_ord'+newindex+'_order").bind("change keypress",function(e){';
						newjquery += 'if(e.type=="keypress"){';
							//newjquery += 'alert(e.keyCode);';
							newjquery += 'if(e.keyCode == 13){';
								newjquery += 'change_val=$(this).val();';
								newjquery += 'cust=$("#PHP_cust").find(":selected").val();';
								newjquery += 'iOrder = $(this).val();';
								newjquery += 'iIndex = $("#po_ord'+newindex+'_tr").attr("idx");';

								newjquery += '$.post("./packing_edit.php?PHP_action=get_partial",{PHP_order:iOrder,PHP_rowindex:iIndex,PHP_order_type:"PO"},function(data) {';
									newjquery += '$("#po_ord'+newindex+'_partial").html(data);';
									newjquery += '$("#po_ord'+newindex+'_sizebreakdown").html("");';
									
									newjquery += 'if(cust != ""){';
										newjquery += '$.post("./packing_edit.php?PHP_action=consignee",{cust_id:cust,PHP_order:iOrder},function(data) {';
											newjquery += '$("#po_consignee").html(data);';
										newjquery += '});';
									newjquery += '}';
								newjquery += '});';
								
							newjquery += '}';
						newjquery += '}';
					newjquery += '});';
				newjquery += '</script>';
				$('#tbl_po_carton_edit').append(newtr);
				$('#jquery_div').append(newjquery);
				//newjquery += '$("table#order_table tr#scm_ord'+newrow_index+'_tr").remove();';//刪除單筆儲存的紀錄
			}
			else
			{
				newtr += '<tr id="scm_ord'+newindex+'_tr" idx="'+newindex+'">';
				newtr += '<td><input type="text" id="scm_ord'+newindex+'_order" name="scm_ord'+newindex+'_order" style="width:100px;" value=""/></td>';
				newtr += '<td><select id="scm_ord'+newindex+'_packstyle" name="scm_ord'+newindex+'_packstyle" class="packstyle" ><option value="pack">By Pack</option><option value="bulk">By Bulk</option></select></td>';
				newtr += '<td><input type="checkbox" id="scm_ord'+newindex+'_hanger" name="scm_ord'+newindex+'_hanger" class="hanger"/></td>';
				newtr += '<td><font size="2" style="cursor:pointer;font-weight:bold;" id="scm_ord'+newindex+'_calendar-trigger" color="blue">'+newdate.pattern('yyyy-MM-dd')+'</font>';
				newtr += '<input type="hidden" id="scm_ord'+newindex+'_calendar-inputField" name="scm_ord'+newindex+'_ship_date" size="8" value="'+newdate.pattern('yyyy-MM-dd')+'"></td>';
				newtr += '<td><div id="scm_ord'+newindex+'_shipway"></div></td>';//html_shipway()
				newtr += '<td><div id="scm_ord'+newindex+'_shipto"></div></td>';
				newtr += '<td><div id="scm_ord'+newindex+'_partial"></div></td>';
				newtr += '<td><div id="scm_ord'+newindex+'_sizebreakdown"></div></td>';
				newtr += '<td><input type="text" id="scm_ord'+newindex+'_ucc" name="scm_ord'+newindex+'_ucc" value=""/></td>';
				newtr += '<td><input type="text" id="scm_ord'+newindex+'_prepack" name="scm_ord'+newindex+'_prepack" value=""/></td>';
				newtr += '<td><img src="images/Save.png"  id="scm_ord'+newindex+'_save" class="single_save" idx="'+newindex+'" border="0"><img src="images/trashcan.png"  id="scm_ord'+newindex+'_delete" class="single_delete" idx="'+newindex+'" border="0"></td>';
				newtr += '<input type="hidden" id="PHP_scm_index'+newindex+'" name="PHP_scm_index[]" value="'+newindex+'">';
				newtr += '</tr>';
				
				
				newjquery += '<script type="text/javascript">';
					newjquery += 'cal'+newindex+' = Calendar.setup({';
						newjquery += 'weekNumbers : false,';
						newjquery += 'trigger     : "scm_ord'+newindex+'_calendar-trigger",';
						newjquery += 'inputField  : "scm_ord'+newindex+'_calendar-inputField",';
						newjquery += 'date        : '+newdate.pattern('yyyyMMdd')+' ,';
						newjquery += 'selection   : '+newdate.pattern('yyyyMMdd')+',';
						newjquery += 'showTime    : false,';
						newjquery += 'onSelect: function() {';
						newjquery += '$("#scm_ord'+newindex+'_calendar-trigger").html($("#scm_ord'+newindex+'_calendar-inputField").val());';
						newjquery += 'cal'+newindex+'.hide();';
						newjquery += '}';
					newjquery += '});';
				newjquery += '</script>';
				
				newjquery += '<script language="javascript">';
				
					newjquery += '$("#scm_ord'+newindex+'_save").click(function() {';
					
					
					
					
					
						newjquery += 'var cust = $("#PHP_cust").find(":selected").val();';
						newjquery += 'var option = "SCM";';
					
					
					
					
						newjquery += 'var scm_order = $("#main_order").val();';
						newjquery += 'var po_order = $("#scm_ord'+newindex+'_order").val();';
						newjquery += 'var ucc = $("#scm_ord'+newindex+'_ucc").val();';
						newjquery += 'var prepack = $("#scm_ord'+newindex+'_prepack").val();';
						newjquery += 'var partial = $("#scm_ord'+newindex+'_partial_select").find(":selected").val();';
						newjquery += 'var shipway = $("#scm_ord'+newindex+'_shipway_select").find(":selected").val();';
						newjquery += 'var sizecolor = $("#scm_ord'+newindex+'_colorsize").val();';
						newjquery += 'var packstyle = $("#scm_ord'+newindex+'_packstyle").find(":selected").val();';
						newjquery += 'var hanger = "";';
						newjquery += 'var shipto = $("#scm_ord'+newindex+'_shipto").find(":selected").val();';
						newjquery += 'var shipdate = $("#scm_ord'+newindex+'_calendar-inputField").val();';
						

						newjquery += 'if($("#scm_ord'+newindex+'_hanger").is(":checked")){';
							newjquery += 'hanger = "checked";';
						newjquery += '} else {';
							newjquery += 'hanger = "unchecked";';
						newjquery += '}';
						
						
						newjquery += 'var sizecolor_array = sizecolor.split("_");';
						newjquery += 'var color_length = sizecolor_array[0];';
						newjquery += 'var size_length = sizecolor_array[1];'; 
						
						
						
						/* newjquery += 'if(typeof(sizecolor)== "undefined"){';
							newjquery += 'alert("please check sizebreakdown!!");';	
						newjquery += '} else {';
							newjquery += 'var sizecolor_array = sizecolor.split("_");';
							newjquery += 'var color_length = sizecolor_array[0];';
							newjquery += 'var size_length = sizecolor_array[1];'; 
						newjquery += '}'; */
						
						newjquery += 'var colorsize = new Array(color_length);';
					 	newjquery += 'for(colori=0;colori<color_length;colori++){';
							newjquery += 'colorsize[colori]= new Array();';
							newjquery += 'for(sizei=0;sizei<size_length;sizei++){';
								newjquery += 'colorsize[colori][sizei]="";';
							newjquery += '}';
						newjquery += '}';
						newjquery += 'var colorint = 0; ';
						newjquery += 'var sizeint = 0; ';
						newjquery += 'var carton_start = $("#scm_carton_begin").val();';
						newjquery += 'var carton_end = $("#scm_carton_end").val();';
						
						
						newjquery += 'if($("#PHP_cust").find(":selected").val() == ""){';
							newjquery += 'alert("customer can not empty!!");';
						newjquery += '} else {';
						
							newjquery += 'if($("#main_order").val() == ""){';
								newjquery += 'alert("[Order NO.] can not empty!!");';
							newjquery += '} else {'; 
						
								newjquery += 'if($("#scm_carton_begin").val() == "" || $("#scm_carton_end").val() == ""){';
									newjquery += 'alert("[Carton NO.] can not empty!!");';
								newjquery += '} else {';
								
									newjquery += 'var start = $("#scm_carton_begin").val();';
									newjquery += 'var end = $("#scm_carton_end").val();';
									
									newjquery += 'if(Number(start) > Number(end)){';
										newjquery += 'alert("[Carton NO. End] must be greater [Carton NO. Start]");';
									newjquery += '} else {';
									
								
										newjquery += 'if($("#scm_ord'+newindex+'_order").val() == ""){';
										newjquery += 'alert("[P.O.] can not empty!!");';
										newjquery += '} else {';
										
										
											newjquery += 'if($("#scm_ord'+newindex+'_shipway_select").find(":selected").val() == ""){';
											newjquery += 'alert("[SHIP Way] can not empty!!");';
											newjquery += '} else {';
										
										
										
										
										
												newjquery += 'if($("#scm_ord'+newindex+'_shipto").find(":selected").val() == ""){';
												newjquery += 'alert("[SHIP To] can not empty!!");';
												newjquery += '} else {';
												
													newjquery += 'if($("#scm_ord'+newindex+'_partial_select").find(":selected").val() == ""){';
													newjquery += 'alert("[Partial] can not empty!!");';
													newjquery += '} else {';
													newjquery += 'var totalqty=0;';
														newjquery += '$("input[name^=\'scm['+newindex+']\']").each(function(){ ';
															newjquery += 'if( colorint < color_length){';
																newjquery += 'if( sizeint < size_length){';
																	newjquery += 'if($(this).val() == ""){';
																		newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";';
																	newjquery += '} else {';
																		newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();';
																	newjquery += '}';
																	newjquery += 'sizeint++;';
																newjquery += '} else {';
																	newjquery += 'colorint++;';
																	newjquery += 'sizeint=0;';
																	newjquery += 'if($(this).val() == ""){';
																		newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+",0";';
																	newjquery += '} else {';
																		newjquery += 'colorsize[colorint][sizeint] = $(this).attr("color")+","+$(this).attr("size")+","+$(this).val();';
																	newjquery += '}';
																	newjquery += 'sizeint++;';
																newjquery += '}';
															newjquery += '}';	
															newjquery += 'totalqty = totalqty + Number(($(this).val()==""?0:$(this).val()));';
														newjquery += '});';
														
														newjquery += 'if(totalqty > 0){';

															newjquery += '$.post("./packing_edit.php?PHP_action=packlist_save_one",{PHP_cust:cust,PHP_option:option,PHP_po_order:po_order,PHP_scm_order:scm_order,PHP_ucc:ucc,PHP_prepack:prepack,PHP_partial:partial,PHP_sizebreakdown:colorsize,PHP_carton_start:carton_start,PHP_carton_end:carton_end,PHP_packstyle:packstyle,PHP_hanger:hanger,PHP_shipdate:shipdate,PHP_shipto:shipto,PHP_shipway:shipway},function(data) {';
																newjquery += 'alert(data);';
																newjquery += 'if(data=="finish"){';
																	newjquery += 'alert(data);';
																	newjquery += '$("table#tbl_scm_carton_edit tr#scm_ord'+newindex+'_tr").remove();';
																	
																newjquery += '} else {';
																	newjquery += 'alert(data);';
																newjquery += '}';
															newjquery += '});';
														newjquery += '} else {';
															newjquery += 'alert("No qty in this row,please check again!!");';
														newjquery += '}';
													newjquery += '}';
												newjquery += '}';
											
											
											newjquery += '}';
											
										newjquery += '}';
									newjquery += '}';
								newjquery += '}';
							newjquery += '}';
						newjquery += '}';
					newjquery += '});';
					
					newjquery += '$("#scm_ord'+newindex+'_delete").click(function() {';
						newjquery += '$("table#tbl_scm_carton_edit tr#scm_ord'+newindex+'_tr").remove();';
					newjquery += '});';
					
				newjquery += '</script>';
				$('#tbl_scm_carton_edit').append(newtr);
				$('#jquery_div').append(newjquery);
				
				cust = $("#PHP_cust").find(":selected").val();
				scm_order = $("#main_order").val();
				if(cust != '' && scm_order !='')
				{
					html_cust_consignee(cust,newindex,scm_order);
					html_shipway(newindex);
				}
				scm_order = $('#main_order').val();
				if($.trim(scm_order) != '')
				{
					html_partial(scm_order,newindex);
				}
				
				
			}
		});
		$('#btn_save').click(function(){
			//明天前端比對箱號大小
			
			var aprv_submit = false;
			var chk_item = true;
			//alert($('#po_ALR14-0468_A_sizebreak_1 tr').index());
			//alert($('#tbl_po_carton_edit tr').index());
			if($('#main_option').val()=='PO')
			{
				var trlength = $('#tbl_po_carton_edit tr').length;//table的tr總數
				if($.trim($("#PHP_cust").find(":selected").val()) == '')
				{
					alert("customer can not empty!!");
				}
				else
				{
					if($.trim($("#main_order").val()) == '')
					{
						alert("[P.O.] can not empty!!");
					}
					else
					{
						if($.trim($('#po_carton_begin').val()) == '' || $.trim($('#po_carton_end').val()) == '')
						{
							alert("[Carton NO.] can not empty!!");
						}
						else
						{
							var start = $("#po_carton_begin").val();
							var end =$("#po_carton_end").val();
							if(Number(start) > Number(end)){
								alert("[Carton NO. End] must be greater [Carton NO. Start]");
							} else {
								
								
							
								if($.trim($("#po_consignee").find(":selected").val()) == '')
								{
									alert("[SHIP To] can not empty!!");
								}
								else
								{
									for(loopi=1;loopi<trlength;loopi++)
									{
										
										nowidx = $('#tbl_po_carton_edit').find('tr:eq('+loopi+')').attr('idx');
										if(nowidx != 'nouse')
										{
											if(typeof($("#po_ord"+nowidx+"_partial_select").find(":selected").val()) == 'undefined')
											{
												alert('Please delete no information rows!!');
												chk_item = false;
											}
											
											if($("#po_ord"+nowidx+"_partial_select").find(":selected").val() == '')
											{
												alert('[Partial] can not empty!!');
												chk_item = false;
											}
										}			
													
										if(!chk_item)
										{
											return false;
										}
										if(loopi == (trlength-1))	
										{	
											aprv_submit = true;
										}									
																		
										
									}
									if(aprv_submit)
									{
										$('#packing_list').submit();//確認表單上資料正確才可送出
									} 
									
									
								}
							}
						}
					}
				}
				
			}
			else
			{
				var trlength = $('#tbl_scm_carton_edit tr').length;//table的tr總數
				//var first_idx = $('#tbl_scm_carton_edit').find('tr:eq(1)').attr('idx');
				//var last_idx = $('#tbl_scm_carton_edit tr').index()+1;//index從0開始，所以要加1
				//alert(last_idx);
				if($.trim($("#PHP_cust").find(":selected").val()) == '')
				{
					alert("customer can not empty!!");
				}
				else
				{
					if($.trim($("#main_order").val()) == '')
					{
						alert("[SCM Order] can not empty!!");
					}
					else
					{
						if($.trim($('#scm_carton_begin').val()) == '' || $.trim($('#scm_carton_end').val()) == '')
						{
							alert("[Carton NO.] can not empty!!");
						}
						else
						{
							
							var start = $("#scm_carton_begin").val();
							var end =$("#scm_carton_end").val();
							if(Number(start) > Number(end)){
								alert("[Carton NO. End] must be greater [Carton NO. Start]");
							} else {
									
								for(loopi=1;loopi<trlength;loopi++)
								{
									nowidx = $('#tbl_scm_carton_edit').find('tr:eq('+loopi+')').attr('idx');
						
									if(nowidx != 'nouse')
									{
										if($.trim($("#scm_ord"+nowidx+"_order").val()) == '')
										{
											alert('[P.O.] can not empty!!');
											chk_item = false;
										}
										else
										{
											if(typeof($("#scm_ord"+nowidx+"_shipto").find(":selected").val()) == 'undefined')
											{
												alert('Please select customer!!');
												chk_item = false;
											}
											else
											{
												if($("#scm_ord"+nowidx+"_shipto").find(":selected").val() == '')
												{
													alert('[SHIP To] can not empty!!');
													chk_item = false;
												}
												else
												{
													if(typeof($("#scm_ord"+nowidx+"_partial_select").find(":selected").val()) == 'undefined')
													{
														alert('Please press enter on [Order No] column!!');
														chk_item = false;
													}
													else
													{
														if($("#scm_ord"+nowidx+"_partial_select").find(":selected").val() == '')
														{
															alert('[Partial] can not empty!!');
															chk_item = false;
														}
														
													}
												}
											} 
										}

										
										if(!chk_item)
										{
											return false;
										}
										if(loopi == (trlength-1))	
										{	
											aprv_submit = true;
										}
									}
							
								}
							}
							if(aprv_submit)
							{
								$('#packing_list').submit();//確認表單上資料正確才可送出
							} 
						}
					}
				} 
			} 
			//$('#packing_list').submit();//確認表單上資料正確才可送出
		});
		
	});
})(jQuery);

