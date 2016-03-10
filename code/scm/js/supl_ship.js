(function($){

    $(document).ready(function(){

        // Go Back
        $("button[id^='goback']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
            
            var $go = $(this).attr('go');

            location.href = '?PHP_action='+$go;

        });
        
        // button
		$('button[class=submit_ajax]').hover(function(){
			$(this).animate({ fontSize: '+=3' }, 200);
			$(this).addClass("submit_over");
		},function(){
			$(this).removeClass("submit_over"); 
			$(this).animate({ fontSize: '-=3' }, 200);
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');
        
        // bgcolor
		$('tr[id^=box_txt]').hover(function(){
			$(this).addClass("List_TR");
		},function(){
			$(this).removeClass("List_TR");  
		});
    
        // link bgcolor
		$('tr[id^=link_]').hover(function(){
			$(this).addClass("List_TR");
		},function(){
			$(this).removeClass("List_TR");  
		});
    
        // bgcolor line
        $('.list_txt').hover(function(){
            $(this).addClass("list_txt_bg");
        },function(){
            $(this).removeClass("list_txt_bg");  
        });
        
        // bgcolor line
        $('.list_txt_right').hover(function(){
            $(this).addClass("list_txt_bg");
        },function(){
            $(this).removeClass("list_txt_bg");  
        });
                
        // bgcolor line
        $('.list_over').hover(function(){
            $(this).addClass("list_over_bg");
        },function(){
            $(this).removeClass("list_over_bg");  
        });
        
        // Form Submit
        $("[id^='box_form']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
            
            var $form_id = $(this).attr('form_id');
            
            if( $form_id == 'form_submit')
            if( !confirm( 'Are you sure to Submit!') ){
                $.unblockUI();
                return false;
            }
            
            if( $form_id == 'form_revise')
            if( !confirm( 'Are you sure to Revise!') ){
                $.unblockUI();
                return false;
            }
            
            $("#"+$form_id).submit();
            
        });

        // Update
        $("button[id^='box_update']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});

            var $goback = $("#goback").val();
            
            var $M_ship_id = $("#M_ship_id").val();
            var $M_bl_num = $("#M_bl_num").val();
            var $M_carrier_num = $("#M_carrier_num").val();

            var $M_org = $("#M_org2").val() ? $("#M_org2").val() : $("#M_org").val() ;
            var $org = $("#M_org2").val() ? $("#M_org2").val() : $("#M_org :selected").text() ;
            
            var $M_dist = $("#M_dist").val();
            var $dist = $("#M_dist :selected").text();

            var $M_ship_date_yy = $("#M_ship_date_yy").val();
            var $M_ship_date_mm = $("#M_ship_date_mm").val();
            var $M_ship_date_dd = $("#M_ship_date_dd").val();

            var $M_ship_eta_date_yy = $("#M_ship_eta_date_yy").val();
            var $M_ship_eta_date_mm = $("#M_ship_eta_date_mm").val();
            var $M_ship_eta_date_dd = $("#M_ship_eta_date_dd").val();
            
            var $M_ship_way = $("#M_ship_way").val();
            var $M_express = !$("#M_express").val() ? '' : $("#M_express").val() ;

            

                $.ajax({
                    url: '?PHP_action=supl_ship_up',
                    type: 'POST',
                    data: {
                        M_ship_id: 	        $M_ship_id,
                        M_carrier_num:      $M_carrier_num,
                        M_bl_num:           $M_bl_num,
                        M_org: 	            $M_org,
                        M_dist:             $M_dist,
                        M_ship_way:         $M_ship_way,
                        M_express:          $M_express,
                        M_ship_date: 	    $M_ship_date_yy + '-' + $M_ship_date_mm + '-' + $M_ship_date_dd,
                        M_ship_eta_date:    $M_ship_eta_date_yy + '-' + $M_ship_eta_date_mm + '-' + $M_ship_eta_date_dd
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] == 'true' ) {
                            location.reload();
                            // location.href = '?PHP_action='+$goback;
                        } else {
                            alert(sts[1]);
                            $.unblockUI();
                        }
                    }
                });
                return true;
            

        });
                
        // Det Edit
        $("button[id^='box_det_edit']").live("click", function () {
            $(this).attr({'style':'display:none;'});
            $("#box_det_update").attr({'style':'height:70px;width:90px;display:;'});
            $("#S_invoice_num").attr({'style':'display:none;'});
            $("#S_pi_num").attr({'style':'display:none;'});
            $("#M_invoice_num").attr({'style':'width:160px;display:;'});
            $("#M_pi_num").attr({'style':'width:160px;display:;'});
        });
                
        // Det Update
        $("button[id^='box_det_update']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});

            var $goback = $("#goback").val();
            
            var $M_po_ship_id = $("#M_po_ship_id").val();
            var $M_po_ship_det_id = $("#M_po_ship_det_id").val();
            var $M_po_num = $("#M_po_num").val();
            var $M_invoice_num = $("#M_invoice_num").val();
            var $M_pi_num = $("#M_pi_num").val();

            

                $.ajax({
                    url: '?PHP_action=supl_ship_det_up',
                    type: 'POST',
                    data: {
                        M_po_ship_id:   $M_po_ship_id,
                        M_ship_det_id:  $M_po_ship_det_id,
                        M_invoice_num:  $M_invoice_num,
                        M_pi_num:       $M_pi_num
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        
                        $("#S_invoice_num").html($M_invoice_num);
                        $("#S_pi_num").html($M_pi_num);
                        
                        $(this).attr({'style':'display:;'});
                        $("#box_det_edit").attr({'style':'height:70px;width:90px;display:;'});
                        $("#box_det_update").attr({'style':'height:70px;width:90px;display:none;'});
                        $("#S_invoice_num").attr({'style':'display:;'});
                        $("#S_pi_num").attr({'style':'display:;'});
                        $("#M_invoice_num").attr({'style':'width:160px;display:none;'});
                        $("#M_pi_num").attr({'style':'width:160px;display:none;'});                        
                        
                        $.unblockUI();
                    }
                });
                return true;
            

        });
        
        // Det Del
        $("button[id^='box_det_del']").live("click", function () {
            $.blockUI({ message: ' Loding ...'});

            var $M_po_ship_det_id = $("#M_po_ship_det_id").val();
            var $M_po_num = $("#M_po_num").val();

            if( confirm(  'Are you sure to DELETE! \r\nP.O. #：'+ $M_po_num ) ){

                $.ajax({
                    url: '?PHP_action=supl_ship_det_del',
                    type: 'POST',
                    data: {
                        M_ship_det_id:  $M_po_ship_det_id,
                        M_po_num:       $M_po_num
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                        $.unblockUI();
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        alert(sts[1]);
                        
                        location.href="supplier_ship.php?PHP_action=supl_ship_edit";
                    }
                });
                return true;
            } else {
                $.unblockUI();
                return false;
            }
            
            
        });
        
        // 新增開起畫面
        $("img[id^='append']").live("click", function () {
            
            if( $("tr #"+$(this).attr('po_id')).is(':visible') ) {
                $("tr[id^="+$(this).attr('po_id')+"]").attr({'style':'display:none;'});
                $(this).attr({'src':'images/bullet_toggle_plus.png'});
                $(this).attr({'title':'OPEN'});
            } else {
                $("tr[id^="+$(this).attr('po_id')+"]").attr({'style':'display:;'});
                $(this).attr({'src':'images/bullet_toggle_minus.png'});
                $(this).attr({'title':'CLOSE'});
            }
        
            // $("tr[id^='append_']").attr({'style':'display:none;'}); 
            // $("#"+$(this).attr('po_id')).attr({'style':'display:;'});
            // bullet_toggle_plus.png

        });
        
        // 新增
        $("button[id^='add']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
        
            var id = $(this).attr('po_id');
            var ship_id = $('#M_po_ship_id').val();
            var ship_det_id = $('#M_po_ship_det_id').val();
            
            var ap_id = $('#M_ap_id_'+id).val();
            var mat_cat = $('#M_mat_cat_'+id).val();
            var mat_id = $('#M_mat_id_'+id).val();
            var color = $('#M_color_'+id).val();
            var size = $('#M_size_'+id).val();
            var po_unit = $('#M_po_unit_'+id).val();
            
            var c_no = $('#M_c_no_'+id).val();
            var l_no = $('#M_l_no_'+id).val();
            var r_no = $('#M_r_no_'+id).val();
            var qty = $('#M_qty_'+id).val();
            var nw = $('#M_nw_'+id).val();
            var gw = $('#M_gw_'+id).val();
            var c_o = $('#M_c_o_'+id).val();
            
            if( qty == '' || qty < 0 ) {
                alert('Please input Qty!');
                $.unblockUI();
                return false;
            }
            
            if ( !ap_id ){
                $.unblockUI();
                location.reload();
                return false;
            }

            
            
                $.ajax({
                    url: '?PHP_action=supl_ship_link_add',
                    type: 'POST',
                    data: {
                        M_ship_id:      ship_id,
                        M_ship_det_id:  ship_det_id,
                        M_ap_id:        ap_id,
                        M_mat_cat:      mat_cat,
                        M_mat_id:       mat_id,
                        M_color:        escape(color),
                        M_size:         escape(size),
                        M_po_unit:      escape(po_unit),
                        
                        M_c_no:         escape(c_no),
                        M_l_no:         escape(l_no),
                        M_r_no:         escape(r_no),
                        M_qty:          qty,
                        M_nw:           nw,
                        M_gw:           gw,
                        M_c_o:          escape(c_o)

                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] != 'false' ) {
                            htmls = 
'                                        <tr id="link_'+sts[2]+'">'+
'                                            <td id="list_title3"><span id="S_c_no_'+sts[2]+'">'+c_no+'</span><input class="input" type="text" id="E_c_no_'+sts[2]+'" name="E_c_no_'+id+'" value="'+c_no+'" style="width:60px;display:none;" autocomplete="off"/></td>'+
'                                            <td id="list_title3"><span id="S_l_no_'+sts[2]+'">'+l_no+'</span><input class="input" type="text" id="E_l_no_'+sts[2]+'" name="E_l_no_'+id+'" value="'+l_no+'" style="width:60px;display:none;" autocomplete="off"/></td>'+
'                                            <td id="list_title3"><span id="S_r_no_'+sts[2]+'">'+r_no+'</span><input class="input" type="text" id="E_r_no_'+sts[2]+'" name="E_r_no_'+id+'" value="'+r_no+'" style="width:60px;display:none;" autocomplete="off"/></td>'+
'                                            <td id="list_title3_number" class="bsqty_right">'+
'                                                <span id="S_qty_'+sts[2]+'" name="ship_det_qty_'+id+'" >'+FormatNumber(qty)+'</span>'+
'                                                <input class="input" type="text" id="E_qty_'+sts[2]+'" name="E_qty_'+sts[2]+'" value="'+FormatNumber(qty)+'" style="width:60px;display:none;" autocomplete="off"/>'+
'                                            </td>'+
'                                            <td id="list_title3_number"><span id="S_nw_'+sts[2]+'">'+FormatNumber(nw)+'</span><input class="input" type="text" id="E_nw_'+sts[2]+'" name="E_nw_'+id+'" value="'+FormatNumber(nw)+'" style="width:60px;display:none;" autocomplete="off"/></td>'+
'                                            <td id="list_title3_number"><span id="S_gw_'+sts[2]+'">'+FormatNumber(gw)+'</span><input class="input" type="text" id="E_gw_'+sts[2]+'" name="E_gw_'+id+'" value="'+FormatNumber(gw)+'" style="width:60px;display:none;" autocomplete="off"/></td>'+
'                                            <td id="list_title3_txt"><span id="S_c_o_'+sts[2]+'">'+c_o+'</span><input class="input" type="text" id="E_c_o_'+sts[2]+'" name="E_c_o_'+id+'" value="'+c_o+'" style="width:120px;display:none;" autocomplete="off"/></td>'+
'                                            <td id="box_left">'+
'                                                <button class="submit_ajax" type="button" id="edit_'+sts[2]+'" ship_id="'+id+'" link_id="'+sts[2]+'" style="height:30px;width:80px;" title="Edit">Edit</button>'+
'                                                <button class="submit_ajax" type="button" id="update_'+sts[2]+'" ship_id="'+id+'" link_id="'+sts[2]+'" style="height:30px;width:80px;display:none;" title="Delete">Update</button>'+                                            
'                                                <button class="submit_ajax" type="button" id="delete" ship_id="'+id+'" link_id="'+sts[2]+'" style="height:30px;width:80px;" title="Delete">Delete</button>'+
'                                            </td>'+
'                                        </tr>';

                            $('table[id=mat_'+id+']').append(htmls);
                            
                            rechkqty(id);
                            
                        } else {
                           // alert(sts[1]);
                        }
                        $.unblockUI();
                    }
                });            
            
                $.unblockUI();
                return true;
            
            
        });
        
        // 重新計算數量
        rechkqty = function(ship_id){

            var Sqty = new Number(0);
            var qtty = new Number(0);

            $("td span[name^=ship_det_qty_"+ship_id+"]").each(function(i, elm) {
                var a = $(elm).html();
                var b = a.replace(/[,]+/g,"");
                Sqty += Number(b);
            });
            
            qtty = $("td[name=qtty_shipped_qty_"+ship_id+"]").html().replace(/[,]+/g,"");

            $("td[name=ship_total_qty_"+ship_id+"]").html( FormatNumber( Number(qtty) + Number(Sqty) ) );
            $("td[name=ship_qty_"+ship_id+"]").html(FormatNumber(Sqty));

        }
        
        // 千分位+小數第二位顯示
        FormatNumber = function(n) {
            n += "";
            var arr = n.split(".");
            var re = /(\d{1,3})(?=(\d{3})+$)/g;
            return arr[0].replace(re,"$1,") + "." + ( arr.length == 2 ? arr[1].substr(0,2) : '00' );
        }
        
        // 刪除細項
        $("button[id^='delete']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
        
            var link_id = $(this).attr('link_id');
            var ship_id = $(this).attr('ship_id');

            if( confirm( 'Are you sure to DELETE!') ){
            
                $.ajax({
                    url: '?PHP_action=supl_ship_link_del',
                    type: 'POST',
                    data: {
                        M_link_id:      link_id
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] != 'false' ) {
                            //alert(sts[1]);
                            
                            $("#link_"+link_id).remove();
                            
                            rechkqty(ship_id);
                        } else {
                            alert(sts[1]);
                        }
                        $.unblockUI();
                    }
                });            
            
                $.unblockUI();
                return true;
            } else {
                $.unblockUI();
                return false;
            }
            
        });

        // 修改細項
        $("button[id^='edit']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
        
            var link_id = $(this).attr('link_id');
            var ship_id = $(this).attr('ship_id');
            
            $("#S_c_no_"+link_id).attr({'style':'width:60px;display:none;'});
            $("#S_l_no_"+link_id).attr({'style':'width:60px;display:none;'});
            $("#S_r_no_"+link_id).attr({'style':'width:60px;display:none;'});
            $("#S_qty_"+link_id).attr({'style':'width:60px;display:none;'});
            $("#S_nw_"+link_id).attr({'style':'width:60px;display:none;'});
            $("#S_gw_"+link_id).attr({'style':'width:60px;display:none;'});
            $("#S_c_o_"+link_id).attr({'style':'width:120px;display:none;'});
            $("#edit_"+link_id).attr({'style':'width:80px;display:none;'});

            $("#E_c_no_"+link_id).attr({'style':'width:60px;display:;'});
            $("#E_l_no_"+link_id).attr({'style':'width:60px;display:;'});
            $("#E_r_no_"+link_id).attr({'style':'width:60px;display:;'});
            $("#E_qty_"+link_id).attr({'style':'width:60px;display:;'});
            $("#E_qty_"+link_id).val($("#E_qty_"+link_id).val().replace(/,|\s/g,'')); 
            $("#E_nw_"+link_id).attr({'style':'width:60px;display:;'});
            $("#E_gw_"+link_id).attr({'style':'width:60px;display:;'});
            $("#E_c_o_"+link_id).attr({'style':'width:120px;display:;'});
            $("#update_"+link_id).attr({'style':'width:80px;display:;'});
            
            $.unblockUI();
            
        });

        // 修改細項
        $("button[id^='update']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
        
            var link_id = $(this).attr('link_id');
            var ship_id = $(this).attr('ship_id');
            
            var c_no = $("#E_c_no_"+link_id).val();
            var l_no = $("#E_l_no_"+link_id).val();
            var r_no = $("#E_r_no_"+link_id).val();
            var qty = $("#E_qty_"+link_id).val();
            var nw = $("#E_nw_"+link_id).val();
            var gw = $("#E_gw_"+link_id).val();
            var c_o = $("#E_c_o_"+link_id).val();

            $.ajax({
                url: '?PHP_action=supl_ship_link_up',
                type: 'POST',
                data: {
                    M_link_id:      link_id , 
                    M_c_no:         escape(c_no) , 
                    M_l_no:         escape(l_no) , 
                    M_r_no:         escape(r_no) , 
                    M_qty:          qty , 
                    M_nw:           nw , 
                    M_gw:           gw , 
                    M_c_o:          escape(c_o) 
                },
                error: function(xhr) {
                    alert('Ajax request 發生錯誤'+xhr);
                },
                success: function(response) {
                    var sts = response.split(",");
                    if ( sts[0] != 'false' ) {
                        alert(sts[1]);
                        
                        $("#S_c_no_"+link_id).html(c_no);
                        $("#S_l_no_"+link_id).html(l_no);
                        $("#S_r_no_"+link_id).html(r_no);
                        $("#S_qty_"+link_id).html(qty);
                        $("#S_nw_"+link_id).html(nw);
                        $("#S_gw_"+link_id).html(gw);
                        $("#S_c_o_"+link_id).html(c_o);                        
                        
                        $("#S_c_no_"+link_id).attr({'style':'width:60px;display:;'});
                        $("#S_l_no_"+link_id).attr({'style':'width:60px;display:;'});
                        $("#S_r_no_"+link_id).attr({'style':'width:60px;display:;'});
                        $("#S_qty_"+link_id).attr({'style':'width:60px;display:;'});
                        $("#S_nw_"+link_id).attr({'style':'width:60px;display:;'});
                        $("#S_gw_"+link_id).attr({'style':'width:60px;display:;'});
                        $("#S_c_o_"+link_id).attr({'style':'width:120px;display:;'});
                        $("#edit_"+link_id).attr({'style':'width:80px;display:;'});

                        $("#E_c_no_"+link_id).attr({'style':'width:60px;display:none;'});
                        $("#E_l_no_"+link_id).attr({'style':'width:60px;display:none;'});
                        $("#E_r_no_"+link_id).attr({'style':'width:60px;display:none;'});
                        $("#E_qty_"+link_id).attr({'style':'width:60px;display:none;'});
                        $("#E_nw_"+link_id).attr({'style':'width:60px;display:none;'});
                        $("#E_gw_"+link_id).attr({'style':'width:60px;display:none;'});
                        $("#E_c_o_"+link_id).attr({'style':'width:120px;display:none;'});
                        $("#update_"+link_id).attr({'style':'width:80px;display:none;'});
                        rechkqty(ship_id);
                    } else {
                        alert(sts[1]);
                    }
                    $.unblockUI();
                }
            });            

        });
        
        // 修改 SHIP STATUS
        $("#ship_status").live("dblclick", function () {
        
            $.blockUI({ message: ' Loding ...'});
            
            $this = $(this);
        
            var ap_id = $($this).attr('ap_id');
            var ship_status = $($this).attr('ship_status');
            var po_num = $($this).attr('po_num');
            var supl_f_name = $($this).attr('supl_f_name');
            
            var s_status = 0;
            var ac = true;
            
            if ( ship_status == '0' ) {
                if ( confirm('是否將 #'+po_num+' 標示未出貨狀態!') ) {
                    s_status = 1;
                } else {
                    s_status = 0;
                    ac = false;
                }
            } else {
                if ( confirm('是否將 #'+po_num+' 標示已出貨狀態!') ) {
                    s_status = 0;
                } else {
                    s_status = 1;
                    ac = false;
                }
            }

            if ( ac ) {
            
                $.ajax({
                    url: '?PHP_action=ship_chg_status',
                    type: 'POST',
                    data: {
                        M_ap_id:        ap_id ,
                        M_po_num:       po_num ,
                        M_s_status:     s_status
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");

                        $($this).css({ "background-color" : ( s_status == "1" ? "#FFFFFF" : "#EEEEEE" ) });
                        $($this).attr({ "ship_status" : ( ship_status == "1" ? "0" : "1" ) });
                    }
                });
            }
            
            $.unblockUI();
            return true;

        });
        
        // $("*").live('mousemove', function(e) {
        // }).bind('selectstart', function(){
			// return false;
		// }).css('MozUserSelect','none');
    
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
        
        // search ship ship_link
        $("tr #ship_link").live("click", function () {
            window.location = "?PHP_action=supl_ship_view&M_id="+$(this).attr('ship_id');
		});
        
		$('tr #ship_link').hover(function(){
			$(this).addClass("submit_over");
		},function(){
			$(this).removeClass("submit_over"); 
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');
        

        // chk_way
        chk_way = function() {

            if( $('#ship select[name=M_ship_way]').val() == 'Express' ){

                $('#way').append($ppd);

            } else {
            
                $('#way').html('<span id="way"></span>');
                
            }
        }
        
        // chk_way
        blockUI = function(action,now_po) {
            $.blockUI({ message: ' Loding ...'});
            location.href = '?PHP_action='+action+'&S_po_num='+now_po;
        }
        
        $("img[class^='b_']").live("click", function () {
            $.blockUI({ message: ' Loding ...'});
        });

        $("#Popup").live("click", function () {
            var NWin = window.open($(this).attr('www'), '', 'scrollbars=yes,height=800,width=800');
            if (window.focus) 
            {
                NWin.focus();
            }
            return false;
        });
        
		
		// 新增開起畫面
        $("img[id^='det_view']").live("click", function () {
            if( $("tr #"+$(this).attr('po_id')).is(':visible') ) {
                $("tr[id^="+$(this).attr('po_id')+"]").attr({'style':'display:none;'});
                $(this).attr({'src':'images/bullet_toggle_plus.png'});
                $(this).attr({'title':'OPEN'});
            } else {
                $("tr[id^="+$(this).attr('po_id')+"]").attr({'style':'display:;'});
                $(this).attr({'src':'images/bullet_toggle_minus.png'});
                $(this).attr({'title':'CLOSE'});
            }
        });
		
		// del ship
		del_ship = function(id) {
			$.blockUI({ message: ' Loding ...'});
			if( confirm(  'Are you sure to DELETE! \r\nSHIP. #：S'+ id ) ){

                $.ajax({
                    url: '?PHP_action=del_ship',
                    type: 'GET',
                    data: {
                        id:  id,
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                        $.unblockUI();
                    },
                    success: function(response) {
                        if(response == 1){
							alert("S" + id + " 已做驗收，無法刪除！");
						}else{
							$("#aaaaa").text(response);
						}
                        $.unblockUI();
						location.href="supplier_ship.php?PHP_action=search_ship";
                    }
                });
            } else {
                $.unblockUI();
                return false;
            }
        }
		
		del_po = function(ship_id, ship_det_id, po_num) {
			if(confirm("確定要刪除 " + po_num + "?")){
				$.blockUI({ message: ' Loding ...'});
				location.href = 'supplier_ship.php?PHP_action=ship_del_po&PHP_ship_id='+ship_id+'&PHP_ship_det_id='+ship_det_id;
			}
		}
		
	});
    


})(jQuery);