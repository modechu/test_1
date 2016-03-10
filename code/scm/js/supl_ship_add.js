(function($){

    $(document).ready(function(){
    
        // 新增開起畫面
        $("img[id^='append']").live("click", function () {
        
            $("tr[id^='append_']").attr({'style':'display:none;'}); 
            $("#"+$(this).attr('po_id')).attr({'style':'display:;'});

        });
        
        // 確認送出
		$('button[class=submit_ajax]').hover(function(){
			$(this).animate({ fontSize: '+=3' }, 200);
            $(this).addClass("button_hover");
		},function(){
			$(this).animate({ fontSize: '-=3' }, 200);
            $(this).removeClass("button_hover"); 
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');          
        
        // 更新
        $("button[id^='box_bt']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});

            var $goback = $("#goback").val();
            
            var $M_po_ship_id = $("#M_po_ship_id").val();
            var $M_carrier = $("#M_carrier").val();
            var $M_pi = $("#M_pi").val();
            var $M_invoice = $("#M_invoice").val();
            var $M_org = $("#M_org2").val() ? $("#M_org2").val() : $("#M_org").val() ;
            var $org = $("#M_org2").val() ? $("#M_org2").val() : $("#M_org :selected").text() ;
            
            var $M_dist = $("#M_dist").val();
            var $dist = $("#M_dist :selected").text();
            
            var $M_ship_way = $("#M_ship_way").val();
            var $M_express = !$("#M_express").val() ? '' : $("#M_express").val() ;

            var $M_ship_date_yy = $("#M_ship_date_yy").val();
            var $M_ship_date_mm = $("#M_ship_date_mm").val();
            var $M_ship_date_dd = $("#M_ship_date_dd").val();

            var $M_ship_eta_date_yy = $("#M_ship_eta_date_yy").val();
            var $M_ship_eta_date_mm = $("#M_ship_eta_date_mm").val();
            var $M_ship_eta_date_dd = $("#M_ship_eta_date_dd").val();
            
            if( confirm('Carrier #：'+ $M_carrier + '\r\nP.I. #：'+ $M_pi + '\r\nInvoice #：' + $M_invoice + '\r\nFrom：' + $org + ' ~ To：'+$dist+'\r\nShip By：' + $M_ship_way + ' ' + $M_express +'\r\nShipping date：' + $M_ship_date_yy + '-' + $M_ship_date_mm + '-' + $M_ship_date_dd+'\r\nETA date：' + $M_ship_eta_date_yy + '-' + $M_ship_eta_date_mm + '-' + $M_ship_eta_date_dd) ){

                $.ajax({
                    url: '?PHP_action=supl_ship_up',
                    type: 'POST',
                    data: {
                        M_po_ship_id: 	    $M_po_ship_id,
                        M_carrier: 	        $M_carrier,
                        M_pi: 	            $M_pi,
                        M_invoice:          $M_invoice,
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
                            alert(sts[1]);
                            location.reload();
                            location.href = '?PHP_action='+$goback;
                        } else {
                            alert(sts[1]);
                            $.unblockUI();
                        }
                    }
                });
                return true;
            } else {
                $.unblockUI();
                return false;
            }

        });
        
        // 新增
        $("button[id^='add']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
        
            var id = $(this).attr('po_id');
            var ship_id = $('#M_po_ship_id').val();
            
            var mat_cat = $('#M_mat_cat_'+id).val();
            var mat_id = $('#M_mat_id_'+id).val();
            var used_id = $('#M_used_id_'+id).val();
            var bom_id = $('#M_bom_id_'+id).val();
            var ap_det_id = $('#M_ap_det_id_'+id).val();
            var color = $('#M_color_'+id).val();
            var size = $('#M_size_'+id).val();
            
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

            if( confirm( 'Are you sure to APPEND!\r\nC/NO：'+c_no+'\r\nL/NO：'+l_no+'\r\nR/NO：'+r_no+'\r\nQTY：'+qty+'\r\nN.W.：'+nw+'\r\nG.W.：'+gw+'\r\nC/O：'+c_o+'\r\n') ){
            
                $.ajax({
                    url: '?PHP_action=append_supl_ship_det',
                    type: 'POST',
                    data: {
                        M_ship_id:      ship_id,
                        M_mat_cat:      mat_cat,
                        M_mat_id:       mat_id,
                        M_used_id:      used_id,
                        M_bom_id:       bom_id,
                        M_ap_det_id:    ap_det_id,
                        M_color:        color,
                        M_size:         size,
                        
                        M_c_no:         c_no,
                        M_l_no:         l_no,
                        M_r_no:         r_no,
                        M_qty:          qty,
                        M_nw:           nw,
                        M_gw:           gw,
                        M_c_o:          c_o

                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] != 'false' ) {
                            alert(sts[1]);
                            htmls = 
'                                        <tr id="delete_'+sts[2]+'">'+
'                                            <td id="list_title3">'+c_no+'</td>'+
'                                            <td id="list_title3">'+l_no+'</td>'+
'                                            <td id="list_title3">'+r_no+'</td>'+
'                                            <td id="list_title3" class="bsqty_right" name="ship_det_qty_'+ap_det_id+'">'+FormatNumber(qty)+'</td>'+
'                                            <td id="list_title3">'+FormatNumber(nw)+'</td>'+
'                                            <td id="list_title3">'+FormatNumber(gw)+'</td>'+
'                                            <td id="list_title3">'+c_o+'</td>'+
'                                            <td id=""><button class="submit_ajax" id="delete_det" ship_id="'+ap_det_id+'" det_id="'+sts[2]+'" title="DELETE">DELETE</button></td>'+
'                                        </tr>';

                            $('table[id=mat_'+ap_det_id+']').append(htmls);
                            
                            rechkqty(ap_det_id);
                            
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
        
        // 刪除細項
        $("button[id^='delete']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
        
            var det_id = $(this).attr('det_id');
            var ship_id = $(this).attr('ship_id');

            if( confirm( 'Are you sure to DELETE!') ){
            
                $.ajax({
                    url: '?PHP_action=delete_supl_ship_det',
                    type: 'POST',
                    data: {
                        M_det_id:      det_id
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] != 'false' ) {
                            alert(sts[1]);
                            
                            $("#delete_"+det_id).remove();
                            
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
        
        // 確認
        $("button[id='box_cfm']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
            
            var $goback = $("#goback").val();

            var $M_po_ship_id = $("#M_po_ship_id").val();

            if( confirm( 'Are you sure to Confirm!') ){

                $.ajax({
                    url: '?PHP_action=supl_ship_cfm',
                    type: 'POST',
                    data: {
                        M_id: 	$M_po_ship_id
                    },
                    error: function(xhr, ajaxOptions, thrownError){

                        alert(xhr.status);
                        alert(ajaxOptions);
                        alert(thrownError);
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] == 'true' ) {
                            alert(sts[1]);
                            location.href = '?PHP_action='+$goback;
                        } else {
                            alert(sts[1]);
                        }
                    }
                });
                $.unblockUI();
                return true;

            } else {
                $.unblockUI();
                return false;
            }

        });
        
        // REVISE
        $("img[id^='box_revise']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});

            var $ship_id = $(this).attr('ship_id');

            if( confirm( 'Are you sure to Revise!') ){

                $.ajax({
                    url: '?PHP_action=supl_ship_revise',
                    type: 'POST',
                    data: {
                        M_id: 	    $ship_id 
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] == 'true' ) {
                            alert(sts[1]);
                            $("#"+$ship_id).submit();
                        } else {
                            alert(sts[1]);
                        }
                    }
                });
                $.unblockUI();
                return true;

            } else {
                $.unblockUI();
                return false;
            }

        });
        
        // 重新計算數量
        rechkqty = function(ship_id){

            var Sqty = new Number(0);
            
            $("td[name^=ship_det_qty_"+ship_id+"]").each(function(i, elm) {
                var a = $(elm).html();
                var b = a.replace(/[,]+/g,"");
                Sqty += Number(b);
            });

            $("td[name=ship_total_qty_"+ship_id+"]").html( FormatNumber( Number($("td[name=qtty_shipped_qty_"+ship_id+"]").html()) + Sqty ));
            $("td[name=ship_qty_"+ship_id+"]").html(FormatNumber(Sqty));

        }
        
        // 千分位+小數第二位顯示
        FormatNumber = function(n) {
            n += "";
            var arr = n.split(".");
            var re = /(\d{1,3})(?=(\d{3})+$)/g;
            return arr[0].replace(re,"$1,") + "." + ( arr.length == 2 ? arr[1].substr(0,2) : '00' );
        }
        
        // 檢視跳頁
        $("button[id^='box_view']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});

            var $ship_id = $(this).attr('ship_id');
            
            var $goback = $("#goback").val();
            
            location.href = '?PHP_action=supl_ship_det_view&id='+$ship_id+'&goback='+$goback;

        });
        
        // 修改跳頁
        $("button[id^='box_edit']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});

            var $ship_id = $(this).attr('ship_id');
            
            var $goback = $("#goback").val();
            
            location.href = '?PHP_action=supl_ship_det_add&id='+$ship_id+'&goback='+$goback;

        });
        
        // 回SHIP清單
        $("button[id^='go_po']").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});
            
            var $goback = $("#goback").val();

            location.href = '?PHP_action='+$goback;

        });

        $("#box_po").live("click", function () {
            $m_id = $(this).attr('p_id');
            $("tr[id^='box_txt']").attr({'style':'display:none;'}); 
            $("tr[id='box_txt_"+$m_id+"']").attr({'style':'display:;'});
		});
        
        // 刪除整張出貨單
        $("#box_del").live("click", function () {
        
            $.blockUI({ message: ' Loding ...'});

            $m_id = $(this).attr('p_id');
            
            if( confirm( 'Are you sure to DELETE!') ){
            
                $.ajax({
                    url: '?PHP_action=delete_supl_ship',
                    type: 'POST',
                    data: {
                        M_id:   $m_id
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        if ( sts[0] != 'false' ) {
                            alert(sts[1]);
                            
                            $("#"+$m_id).remove();
                            
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
            // po_supl_ship_id
		});
        
        // chk_way
        chk_way = function() {

            if( $('#ship_'+$m_id+' select[name=M_ship_way]').val() == 'Express' ){

                $('#way_'+$m_id).append($ppd);

            } else {

                $('#way_'+$m_id).html('<span id="way_'+$m_id+'"></span>');

            }
        }
        
		// <TR> MOUSEOVER
		$('tr[id^=box_txt]').hover(function(){
            $('tr[id^='+$(this).attr('id')+']').addClass("List_TR");
		},function(){
            $('tr[id^='+$(this).attr('id')+']').removeClass("List_TR");
		});

	});
    
})(jQuery);