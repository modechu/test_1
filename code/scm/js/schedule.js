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

pageWidth=function(){return window.innerWidth != null ? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body != null ? document.body.clientWidth : null;}
pageHeight=function(){return window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;}
topPosition=function(){return typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ? document.body.scrollTop : 0;}
bottomPosition=function(){var height = $(document.documentElement).innerHeight() + topPosition(); return height;}
leftPosition=function(){return typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement && document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft ? document.body.scrollLeft : 0;}
get_line=function(y){valus='';valus_id='';$('table[id^='+event_table+'] tr').each(function(i,elm){if(y>$('table[id^='+event_table+']').offset().top &&(y>=$(elm).offset().top)&&(y<($(elm).offset().top+$(elm).height()))){if($(elm).attr("id"))valus=$(elm).attr("line");valus_id=$(elm).attr("line_id");}});dragline_id=valus_id?valus_id:'?';return dragline=valus?valus:'?';}
function divCenter(div){
    var MyDiv_w = $("#"+div).width();
    var MyDiv_h = $("#"+div).height();
        
    MyDiv_w = parseInt(MyDiv_w);
    MyDiv_h = parseInt(MyDiv_h);

    var width = pageWidth(); 
    var height = pageHeight();
    var left = leftPosition();
    var top = topPosition();

    var Div_topposition = top + ( height / 2 ) - ( MyDiv_h / 2 );
    var Div_leftposition = left + ( width / 2 ) - ( MyDiv_w / 2 );

    $("#"+div).offset({"top":( Div_topposition ) ,"left": ( Div_leftposition ) });
}

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
//數字處理為有千分位
function AppendComma(n)
{
    if (!/^((-*\d+)|(0))$/.test(n))
    {
        var newValue = /^((-*\d+)|(0))$/.exec(n);
        if (newValue != null)
        {
            if (parseInt(newValue, 10))
            {
                n = newValue;
            }
            else
            {
                n = '0';
            }
        }
        else
        {
            n = '0';
        }
    }
    if (parseInt(n, 10) == 0)
    {
        n = '0';
    }
    else
    {
        n = parseInt(n, 10).toString();
    }
    
    n += '';
    var arr = n.split('.');
    var re = /(\d{1,3})(?=(\d{3})+$)/g;
    return arr[0].replace(re, '$1,') + (arr.length == 2 ? '.' + arr[1] : '');
}
//將有千分位的數值轉為一般數字
function RemoveComma(n)
{
    return n.replace(/[,]+/g, '');
}
//調整千分位
function AdjustComma(item, length)
{
    var originalValue = $.trim($(item).val()).length > length 
        ? $.trim($(item).val()).substr(0, length) 
        : $.trim($(item).val());
    
    $(item).val(AppendComma(originalValue));
}
//動態調整輸入欄位的長度
function TextAreaLength(item, length) 
{
    if (item.value.length > length) 
    {
        item.value = item.value.substring(0, length);
    }
}

(function($){

	$(document).ready(function(){
    
		$('#add_box_display,#in,#div_calendar,#div_line,#div_line_detail,#div_order_detail,#div_order_edit').hover(function(){
		}).bind('selectstart', function(){
			return false; 
		}).css('MozUserSelect','none');
        
        if( keyctrl == true ){
            $('#mode').html('<img src="./images/xin.gif"> EDIT MODE');
            $('#mode').addClass("edit_mode");
            $('#add_box_display tr').show();
        } else {
            $('#mode').removeClass("edit_mode"); 
            $('#mode').html('<img src="./images/xin.gif"> VIEW MODE');
            // $('tr[id^=add_box_display').hide();
            $('#add_box_display tr').hide();
        }
        
        // Update
        $('#mode').bind('click', function(){
            $.blockUI();
            if ( keyctrl == true ){
                $('#mode').removeClass("edit_mode"); 
                $('#mode').html('<img src="./images/xin.gif"> VIEW MODE');
                keyctrl = false;
                $('#add_box_display tr').hide();
                resize();
            } else {
                $('#mode').addClass("edit_mode");
                $('#mode').html('<img src="./images/xin.gif"> EDIT MODE');
                keyctrl = true;
                $('#add_box_display tr').show();
                resize();
            }
            $.ajax({
                url: '?PHP_action=edit_mode',
                type: 'POST',
                data: { mode : keyctrl },
                dataType:'text',
                error: function(xhr) { alert('Ajax request 發生錯誤'+xhr); },
                success: function(response) { 
                    // var sts = response.split(","); $('#m_mode').html(sts); 
                }
            });
            $.unblockUI();
        });
        
        // $(document)
        // .bind('keydown',"shift",function(e){if ( e.keyCode == '16' )keyshift = true;})
        // .bind("keyup","shift",function(e){if ( e.which == '16' )keyshift = false;})
        // .bind("keydown","ctrl",function(e){if ( e.which == '17' )m_alert();})
        // .bind("keyup","ctrl",function(e){if ( e.which == '17' )m_alert(keyctrl);})
        // .bind("keydown","alt",function(e){if ( e.which == '18' )keyalt = true;})
        // .bind("keyup","alt",function(e){if ( e.which == '18' )keyalt = false;});
        
        // m_alert = function(){
            // $.blockUI();
            // if ( keyctrl == true ){
                // $('#mode').removeClass("edit_mode"); 
                // $('#mode').html('VIEW MODE');
                // keyctrl = false;
            // } else {
                // $('#mode').addClass("edit_mode");
                // $('#mode').html('EDIT MODE');
                // keyctrl = true;
            // }
            // $.ajax({
                // url: '?PHP_action=edit_mode',
                // type: 'POST',
                // data: { mode : keyctrl },
                // dataType:'text',
                // error: function(xhr) { alert('Ajax request 發生錯誤'+xhr); },
                // success: function(response) { var sts = response.split(","); $('#m_mode').html(sts); }
            // });
            // $.unblockUI();
        // }

		$('input[class=submit_ajax],input[id=add_order_Submit]').hover(function(){
			$(this).animate({ fontSize: '+=3' }, 200);
			$(this).addClass("submit_over");
		},function(){
			$(this).removeClass("submit_over"); 
			$(this).animate({ fontSize: '-=3' }, 200);
		});

		// Submit setting
		$('input[class=submit_ajax]').click(function(){
			$.blockUI();
			var SubName = $(this).attr('id');
			switch( SubName ){

				case 'schedule_append_edit':
				var Factory = $('form[name='+SubName+'] #PHP_fty :selected').val();
				if( !Factory ){
					alert('Please Choise Factory!');
					$.unblockUI();
					return false; 
				}
				$('#schedule_append_edit').submit();
				break;

				case 'schedule_view':
				var Factory = $('form[name='+SubName+'] #PHP_fty :selected').val();
				if( !Factory ){
					alert('Please Choise Factory!');
					$.unblockUI();
					return false; 
				}
                $('#schedule_view').submit();
				break;

				case 'schedule_un_finish':
				var Factory = $('form[name='+SubName+'] #PHP_fty :selected').val();
				if( !Factory ){
					alert('Please Choise Factory!');
					$.unblockUI();
					return false; 
				}
                $('#schedule_un_finish').submit();
				break;

			}
		});

        // resize
        $(window).resize(function(){
            $("#div_line_bar").css( 'width', $(window).width() - 2 );
            $("#div_calendar").css( 'width', $("#div_line_bar").width() );
            $("#div_line_bar").css( 'height', $(window).height() - 80 - $('#add_box').height() );
            $("#div_line").css( 'height', $("#div_line_bar").height() );
            // $('#notes').html($(this).scrollTop()+'~'+$("div[id*=div_line_bar]").scrollTop()+'~'+$(this).offset().top);
            // $('#notes').html($(window).height()+'~'+$("body").height()+'~'+$("#div_line_bar").height());
            
            if ( $('#div_order_edit').css('display') != 'none' ){
                divCenter('div_order_edit');
                $("#mask").css({ 'width':$(window).width() , 'height':$(window).height()});
            }
            
        });
        // resize
        resize = function(){
            $("#div_line_bar").css( 'width', $(window).width() - 2 );
            $("#div_calendar").css( 'width', $("#div_line_bar").width() );
            $("#div_line_bar").css( 'height', $(window).height() - 80 - $('#add_box').height() );
            $("#div_line").css( 'height', $("#div_line_bar").height() );
        }

        // mouse cursor
		$('td[id^=ms_] , span[id=in] , span[id=un] , span[id=Desc] ').hover(function(){
            // if( $(this).attr("id") == 'un' )
			$(this).addClass("m_p");
		},function(){
			$(this).removeClass("m_p");
		});
        
        // mouse cursor
		$('td[id^=line_link] , td[id^=div_order_detail]').hover(function(){
			$(this).addClass("m_help");
		},function(){
			$(this).removeClass("m_help");
		});

        $('span[id=in]').bind('click', function(){
            $('span[id=in]').removeClass("menu_off");
            $('tr[id=un]').hide();
            $('tr[id=Desc]').hide();
            $('tr[id=in]').show();
            $('span[id=in]').addClass("menu_on");
            $('span[id=un]').addClass("menu_off");
            $('span[id=Desc]').addClass("menu_off");
        });
        $('span[id=un]').bind('click', function(){
            $('span[id=un]').removeClass("menu_off");
            $('tr[id=in]').hide();
            $('tr[id=Desc]').hide();
            $('tr[id=un]').show();
            $('span[id=in]').addClass("menu_off");
            $('span[id=Desc]').addClass("menu_off");
            $('span[id=un]').addClass("menu_on");
            $('div[id=div_calendar]').hide();
            $('div[id=div_line]').hide();
        });
        $('span[id=Desc]').bind('click', function(){
            $('span[id=Desc]').removeClass("menu_off");
            $('tr[id=in]').hide();
            $('tr[id=un]').hide();
            $('tr[id=Desc]').show();
            $('span[id=in]').addClass("menu_off");
            $('span[id=un]').addClass("menu_off");
            $('span[id=Desc]').addClass("menu_on");
        });
        
        $('td[id^=ms_]').bind('click', function(){
            var ms = "tr[name^="+$(this).attr('id')+"]";
            var line = "td[id^=line_link"+$(this).attr('line')+"]";

            // $('#notes').html(line+' ~ '+$(this).attr('id'));
            if($(ms).is(":visible")){
                $(ms).hide();
                $(this).removeClass("line_hover_bg");
            }else{
                $(ms).show();
                $(this).addClass("line_hover_bg");
            }
            
            // $(line).addClass("line_hover_bg");
            $(line).removeClass("line_bg_df");
            $(line).addClass("line_hover_bg_title");

            var status = 0
            var ddd =  '';
            $("div[id=div_line_bar] tr[id^=display_"+$(this).attr('line')+"]").each(function(i, elm) {
                if( $(elm).is(":visible") == true ){
                    status++;
                }
            });
            if( status == 0 ){
                // $(line).removeClass("line_hover_bg");
                $(line).removeClass("line_hover_bg_title");
                $(line).addClass("line_bg_df");
            }
        });

        // line_link
        $('td[id^=line_link]').live('dblclick', function(){
            $line = $(this).attr('line');
            $PHP_fty = $('input[name=PHP_fty]').val();
            $PHP_pt_date = $('input[name=PHP_pt_date]').val();
            var url ="monitor.php?PHP_action=saw_line_pic&PHP_line="+$line+"&PHP_date="+$PHP_pt_date+"&PHP_fty="+$PHP_fty;
            var nm = 'line_link';
            window.open(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
        });
        
        // ReLoad
        $('#ReLoad').bind('click', function(){
        
            $.blockUI();
            $PHP_fty = $('input[name=PHP_fty]').val();
            $PHP_pt_date = $('input[name=PHP_pt_date]').val();
            // alert($PHP_fty+$PHP_pt_date);
			$.ajax({
				url: 'schedule.php?PHP_action=schedule_reload',
				type: 'GET',
				data: {
					PHP_fty : $PHP_fty,
					PHP_pt_date : $PHP_pt_date,
					contentType: "application/x-www-form-urlencoded; charset=Big5"
				},
				error: function(xhr) {
					alert('Ajax request 發生錯誤 ['+xhr+']');
				},
				success: function(response) {
                    $('#schedule_view').submit();
                    $('#schedule_append_edit').submit();
				}
			});
            
        });
        
        // scrollbar
        $("div[id*=div_line_bar]").bind('scroll mousewheel DOMMouseScroll', function(e) {

            var $height = $("#bar_main tr[class=line_bg_Ym_df]").height();
            var $width = $("#bar_main td[class=line_bg_Ym]").width();
            
            var $div_top = $("#div_line_bar").offset().top;
            var $div_left = $("#div_line_bar").offset().left;

            $("#div_calendar tr[class=line_bg_Ym_df]").css( 'height', $height );
            $("#div_calendar td[class=line_bg_Ym]").css( 'width', $width );
            $("#div_calendar").css( 'height', $height + 22 );
            
            $("#div_line tr[class=line_bg_Ym_df]").css( 'height', $height );
            $("#div_line td[class=line_bg_Ym]").css( 'width', $width );
            $("#div_line").css( 'width', $width + 5 );
            
            $("#div_calendar").scrollTop(0);
            $("#div_calendar").scrollLeft($("#div_line_bar").scrollLeft());
            $("#div_calendar").offset({ top : $div_top });
            
            $("#div_line").scrollLeft(0);
            $("#div_line").scrollTop($(this).scrollTop());
            $("#div_line").offset({ top : $div_top });
            
            if( $(this).scrollTop() > 0 ){ $("#div_calendar").show(); } else { $("#div_calendar").hide(); }
            if( $(this).scrollLeft() > 0 ){ $("#div_line").show(); } else { $("#div_line").hide(); }
            
        });

        // DIV Line Detail
        $('td[id^=line_link]').bind('mouseout', function(e) {
            $("#div_line_detail").hide();
        }).bind('mousemove', function(e){
            if( drag_bar_status == false ){
                $("#div_line_detail td[id=line]").html($(this).attr('line'));
                $("#div_line_detail td[id=people]").html($(this).attr('people'));
                $("#div_line_detail td[id=avg]").html($(this).attr('avg'));
                $("#div_line_detail").show();
                
                $("#div_line_detail").offset({
                    left : e.pageX + document.documentElement.scrollTop + 20 ,
                    top : e.pageY + document.documentElement.scrollLeft - ( $("#div_line_detail").height() / 2  )
                });
            }
        });

        
        // DIV Order Detail
        $('td[id^=div_order_detail]').bind('mouseout', function(e) {
            $('div[id^=div_order_detail]').hide();
        }).bind('mousemove', function(e){
            if( drag_bar_status == false ){
                $("div[id^=div_order_detail] span[id=order]").html($(this).attr('ord_num')+$(this).attr('mks'));
                $("div[id^=div_order_detail] span[id=pattern]").html($(this).attr('pattern'));
                $("div[id^=div_order_detail] span[id=Style]").html($(this).attr('styles'));
                $("div[id^=div_order_detail] span[id=ie1]").html($(this).attr('ie1'));
                $("div[id^=div_order_detail] span[id=ie2]").html($(this).attr('ie2'));
                $("div[id^=div_order_detail] span[id=ord_qty]").html($(this).attr('ord_qty'));
                $("div[id^=div_order_detail] span[id=sch_qty]").html($(this).attr('sch_qty'));
                $("div[id^=div_order_detail] span[id=out_qty]").html($(this).attr('out_qty'));
                if ( $(this).attr('status') == '2' ) {
                    $("div[id^=div_order_detail] span[id=ETS]").html($(this).attr('ets'));
                    $("div[id^=div_order_detail] span[id=ETF]").html($(this).attr('etf'));
                } else {
                    $("div[id^=div_order_detail] span[id=ETS]").html($(this).attr('rel_ets'));
                    $("div[id^=div_order_detail] span[id=ETF]").html($(this).attr('rel_etf'));
                }
                $("div[id^=div_order_detail] span[id=ETD]").html($(this).attr('etd'));
                
                if(isNaN($(this).attr('combine'))){ 
                    $("div[id^=div_order_detail] span[id=Combine_Order]").html('Combine Order：<br>'+$(this).attr('combine'));
                    $("tr[id^=combine").show();
                } else {
                    $("tr[id^=combine").hide();
                }
                
                
                $('div[id^=div_order_detail]').show();
                
                w_height = $(window).height()
                w_width = $(window).width()
                div_height = $('div[id^=div_order_detail]').height();
                div_width = $('div[id^=div_order_detail]').width();

                var re_top = ( ( e.pageY + ( div_height / 2 ) ) > w_height )?
                ( w_height - div_height - 10 ) : 
                ( ( topPosition() > ( e.pageY - ( div_height / 2 )) ) ? topPosition() + 10 : ( e.pageY - ( div_height / 2 )) - 10 );

                var re_left = ( ( e.pageX + ( div_width + 60 ) ) > w_width )?
                ( e.pageX - div_width - 30 ) : e.pageX + 30 ;
                
                $('div[id^=div_order_detail]').offset({ left : re_left , top : re_top });
            }
        });

		// autocomplete share
        if( auto_order ) {
            
            $('#add_order').bind('click', function(){
                // alert($('#add_order').val());
                if( $('#add_order').val() == 'Input Order #' ) $('#add_order').val('');
            });
        
            $( "#add_order" ).autocomplete({
                source: availableTags,
                select: function(event, ui) {
                    $('#add_order').val(ui.item.value);
                    $( "#add_line" ).html('');
                    $( "#add_ets" ).html('');
                    $('input[id^=radio]').prop('checked', false);
                    // $( "#radio2" ).prop('checked', false);
                    $.blockUI();
                    $.ajax({
                        url: '?PHP_action=get_order_info',
                        type: 'POST',
                        data: {
                            fty         : $('#PHP_fty').val(),
                            add_order   : $('#add_order').val(),
                            contentType : "application/x-www-form-urlencoded; charset=Big5"
                        },
                        error: function(xhr) {
                            alert('Ajax request 發生錯誤'+xhr);
                        },
                        success: function(response) {

                            
                            // var arr = decodeURI(response);
                            arr = response.split("@"); 
                            if ( arr[0] == 'ok' ) {
                                str = arr[1].split("_");

                                $( "#mks" ).val(str[1]);
                                $( "#add_p_id" ).val(str[2]);
                                $( "#add_style" ).html(str[3]);
                                $( "#add_p_etd" ).html(str[4]);
                                $( "#add_p_qty" ).html(str[5]);
                                $( "#add_s_qty" ).html(str[6]);
                                $( "#add_ord_num" ).val(str[7]);

                                add_s_qty = str[6];
                                qty = (str[5]-str[6])>0?(str[5]-str[6]):0;
                                
                                html = str[6]+'+='+'<span style="color:0000FF;cursor:default;">'+qty+'</span>';
                                $( "#add_s_qty" ).html(html);
                                $( "#add_qty" ).val(qty);
                                $( "#add_qty" ).show();
                                $.unblockUI();
                            } else {
                                // availableTags = arr[1];
                                
                            }
                            
                        }
                    });
                }
                
            });
        }
        
        // add_qty
        $('#add_qty').bind('keyup click', function(){
            $.blockUI();
            html = add_s_qty+'+='+'<span style="color:0000FF;cursor:default;">'+( Number(add_s_qty) + Number($( "#add_qty" ).val()) )+'</span>';
            $( "#add_s_qty" ).html(html);
            if( $( "#add_qty" ).val() > 0 ){
                $.ajax({
                    url: '?PHP_action=get_line_info',
                    type: 'POST',
                    data: {
                        fty     : $('#PHP_fty').val(),
                        contentType: "application/x-www-form-urlencoded; charset=Big5"
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        arr = response.split("@"); 
                        if ( arr[0] == 'ok' ) {
                            $( "#add_line" ).html(arr[1]);
                            $( "#add_ets" ).html('');
                        } 
                    }
                });
            }
            
            $.unblockUI();
        });

        // add_line select
        $('#add_line').bind('change', function(){
            $.blockUI();
            add_line_id = $('#add_line :selected').val();
            $.ajax({
                url: '?PHP_action=get_line_date',
                type: 'POST',
                data: {
                    line_id : add_line_id,
                    contentType: "application/x-www-form-urlencoded; charset=Big5"
                },
                error: function(xhr) {
                    alert('Ajax request 發生錯誤'+xhr);
                },
                success: function(response) {
                    var arr = decodeURI(response);
                    arr = response.split("@"); 
                    if ( arr[0] == 'ok' ) {
                        // alert(arr[1]);
                        $( "#add_ets" ).html(arr[1]);
                    }
                    $.unblockUI();
                }
            });
            $.unblockUI();
        });
        
		// add_order_Submit
		$('#add_order_Submit').bind('click', function(){
            
            var line_id = $('#add_line :selected').val();
            var lines = $('#add_line :selected').text();
            var ord_num = $('#add_ord_num').val();
            var qty = $('#add_qty').val();
            var rel_ets = $('#add_ets :input').val() ;
            var fty = $('#PHP_fty').val();
            var dess = $('#add_remark').val();
            var p_id = $('#add_p_id').val();
            var add_order = $('#add_order').val();
            add_order = add_order.split("_");
            add_order = ( add_order[1] == '' ) ? '' : '('+add_order[1]+')' ;
            if ( !ord_num ) { alert('please input order number!'); $('#add_ord_num').focus(); return false; }
            if ( !qty || qty == 0 ) { alert('please input schedule qty!'); $('#add_qty').focus(); return false; }
            if ( !line_id ) { alert('please choice Line!'); $('#add_line').focus(); return false; }
            if ( !rel_ets ) { alert('please choice ETS date!'); $('#add_ets').focus(); return false; }
            if (!p_id || !fty) { alert('system error !'); return false; }
            
            // $('#m_mode').html(line_id+','+ord_num+','+qty+','+rel_ets+','+fty+','+des+','+p_id);
            // alert(lines);
            // break;
            $.blockUI();
			$.ajax({
                url: '?PHP_action=add_order_Submit',
				type: 'POST',
				data: {
					line        : lines,
					line_id     : line_id,
					ord_num     : ord_num,
					mks         : add_order,
					qty         : qty,
					rel_ets     : rel_ets,
					fty         : fty,
					des         : escape(dess),
					p_id        : p_id,
					contentType : "application/x-www-form-urlencoded; charset=Big5"
				},
				error: function(xhr) {
					alert('Ajax request 發生錯誤'+xhr);
				},
				success: function(response) {
					arr = response.split("@"); 
                    // alert(response);
					if ( arr[0] == 'ok' ) {
                        $('#schedule_append_edit').submit();
					} else {
                        $('#m_alert').html(arr[1]);
					}
				}
			});
		});
        

        
        // close
        $('#close').bind('click', function(){
            $('div[id^=div_order_edit]').hide();
            $("#mask").remove();
        });

        // Finish
        $('#Finish').bind('click', function(){
            var ord_num = $("div[id^=div_order_edit] input[id=PHP_ord_num]").val();
            var s_id = $("div[id^=div_order_edit] input[id=PHP_s_id]").val();
            var p_id = $("div[id^=div_order_edit] input[id=PHP_p_id]").val();
            var line_id = $("div[id^=div_order_edit] input[id=PHP_line_id]").val();
            var line = $("div[id^=div_order_edit] input[id=PHP_line]").val();
            var mks = $("div[id^=div_order_edit] input[id=PHP_mks]").val();
            // alert($("div[id^=div_order_edit] input[id=PHP_line_id]").val());
            // alert( ord_num+' , '+s_id+' , '+p_id+' , '+line_id);
            $.blockUI();
            $.ajax({
                url: '?PHP_action=schedule_finish',
                type: 'POST',
                data: {
                    ord_num :   ord_num ,
                    mks     :   mks ,
                    line_id :   line_id ,
                    line    :   line ,
                    s_id    :   s_id ,
                    p_id    :   p_id
                },
                error: function(xhr) {
                    alert('Ajax request 發生錯誤'+xhr);
                },
                success: function(response) {
                    var sts = response.split(",");
                    // $('#m_mode').html(sts+'~'+s_id);
                    $('#schedule_append_edit').submit();
                }
            });
            
            // $('div[id^=div_order_edit]').hide();
            // $("#mask").remove();
        });
        
        // ReOpen
        $('#ReOpen').bind('click', function(){
            var ord_num = $("div[id^=div_order_edit] input[id=PHP_ord_num]").val();
            var s_id = $("div[id^=div_order_edit] input[id=PHP_s_id]").val();
            var p_id = $("div[id^=div_order_edit] input[id=PHP_p_id]").val();
            var line_id = $("div[id^=div_order_edit] input[id=PHP_line_id]").val();
            var line = $("div[id^=div_order_edit] input[id=PHP_line]").val();
            var mks = $("div[id^=div_order_edit] input[id=PHP_mks]").val();
            $.blockUI();
            $.ajax({
                url: '?PHP_action=schedule_reopen',
                type: 'POST',
                data: {
                    ord_num :   ord_num ,
                    mks     :   mks ,
                    line_id :   line_id ,
                    line    :   line ,
                    s_id    :   s_id ,
                    p_id    :   p_id
                },
                error: function(xhr) {
                    alert('Ajax request 發生錯誤'+xhr);
                },
                success: function(response) {
                    var sts = response.split(",");
                    // $('#m_mode').html(sts+'~'+s_id);
                    $('#schedule_append_edit').submit();
                }
            });
        });
        
        // Delete
        $('#Delete').bind('click', function(){
            var ord_num = $("div[id^=div_order_edit] input[id=PHP_ord_num]").val();
            var s_id = $("div[id^=div_order_edit] input[id=PHP_s_id]").val();
            var p_id = $("div[id^=div_order_edit] input[id=PHP_p_id]").val();
            var line_id = $("div[id^=div_order_edit] input[id=PHP_line_id]").val();
            var line = $("div[id^=div_order_edit] input[id=PHP_line]").val();
            var mks = $("div[id^=div_order_edit] input[id=PHP_mks]").val();
            if( confirm('Are you sure to Delete ' + ord_num ) ){
                $.blockUI();
                $.ajax({
                    url: '?PHP_action=schedule_delete',
                    type: 'POST',
                    data: {
                        ord_num :   ord_num ,
                        mks     :   mks ,
                        line_id :   line_id ,
                        line    :   line ,
                        s_id    :   s_id ,
                        p_id    :   p_id
                    },
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        // $('#m_mode').html(sts+'~'+s_id);
                        $('#schedule_append_edit').submit();
                    }
                });
            } else {
                $('div[id^=div_order_edit]').hide();
                $("#mask").remove();
            }
        });
        
        // Update
        $('#Update').bind('click', function(){
            var ord_num = $("div[id^=div_order_edit] input[id=PHP_ord_num]").val();
            var s_id = $("div[id^=div_order_edit] input[id=PHP_s_id]").val();
            var p_id = $("div[id^=div_order_edit] input[id=PHP_p_id]").val();
            var line_id = $("div[id^=div_order_edit] input[id=PHP_line_id]").val();
            var line = $("div[id^=div_order_edit] input[id=PHP_line]").val();
            var mks = $("div[id^=div_order_edit] input[id=PHP_mks]").val();
            
            var s_out_qty = Number(RemoveComma($("div[id^=div_order_edit] td[id=s_out_qty]").html()));
            
            var ie = $("div[id^=div_order_edit] input[id=ie]").val();
            var s_sch_qty = Number(RemoveComma($("div[id^=div_order_edit] input[id=s_sch_qty]").val()));
            var pre_ets = $("div[id^=div_order_edit] input[id=pre_ets]").val();
            var pre_etf = $("div[id^=div_order_edit] input[id=pre_etf]").val();
            var des = $("div[id^=div_order_edit] input[id=des]").val();
            
            // alert( ord_num+' , '+s_id+' , '+p_id+' , '+line_id);
            // alert( s_out_qty+' , '+s_sch_qty+' , '+ie+' , '+pre_ets+' , '+pre_etf+' , '+des);
            
            if ( pre_ets > pre_etf ){
                alert( pre_ets+' > '+pre_etf );
                alert('Date error !');
                return false;
            }
            if ( s_out_qty > s_sch_qty ){
                alert( s_out_qty+' > '+s_sch_qty );
                alert('Output over schedule qty !');
                return false;
            }
            
            if( confirm('Are you sure to Update ' + ord_num ) ){
                $.blockUI();
                $.ajax({
                    url: '?PHP_action=schedule_update',
                    type: 'POST',
                    data: {
                        ord_num     :   ord_num ,
                        mks         :   mks ,
                        line_id     :   line_id ,
                        line        :   line ,
                        s_id        :   s_id ,
                        p_id        :   p_id , 
                        ie          :   ie , 
                        s_out_qty   :   s_out_qty , 
                        s_sch_qty   :   s_sch_qty , 
                        pre_ets     :   pre_ets , 
                        pre_etf     :   pre_etf , 
                        des         :   escape(des) 
                        
                    },
                    dataType:'text',
                    error: function(xhr) {
                        alert('Ajax request 發生錯誤'+xhr);
                    },
                    success: function(response) {
                        var sts = response.split(",");
                        // $('#m_mode').html(sts+'~'+s_id);
                        $('#schedule_append_edit').submit();
                    }
                });
            } else {
                $('div[id^=div_order_edit]').hide();
                $("#mask").remove();
            }
        });
        
        // order_link
        $('td[id^=div_order_detail]').bind('dblclick', function(){
            $PHP_ord = $(this).attr('ord_num');
            if( !keyctrl ) {
                var url ="schedule.php?PHP_action=sch_ord_view&PHP_ord_num="+$PHP_ord;
                var nm = 'order_link';
                window.open(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
            } else {
                // $.blockUI({ message: ' Loding ... '});
                mask();
                $('div[id^=div_order_edit]').show();
                divCenter('div_order_edit');
                var s_sch_qty = $(this).attr('s_sch_qty');
                var s_out_qty = $(this).attr('s_out_qty');
                var status = $(this).attr('status');
                var pre_ets = $(this).attr('pre_ets');
                var pre_etf = $(this).attr('pre_etf');

                // $('#m_mode').html('FFF'+s_sch_qty+s_out_qty+status+pre_ets+pre_etf);
                var classs = $(this).attr('class').split(" ");

                $("div[id^=div_order_edit] td[id=line]").html('Line：'+$(this).attr('line'));
                $("div[id^=div_order_edit] td[id=order]").html($(this).attr('order'));

                $("div[id^=div_order_edit] td[id=p_ord_qty]").html( AppendComma($(this).attr('p_ord_qty')) );
                $("div[id^=div_order_edit] td[id=p_sch_qty]").html( AppendComma($(this).attr('p_sch_qty')) );
                $("div[id^=div_order_edit] td[id=p_cut_qty]").html( AppendComma($(this).attr('p_cut_qty')) );
                $("div[id^=div_order_edit] td[id=p_out_qty]").html( AppendComma($(this).attr('p_out_qty')) );
                $("div[id^=div_order_edit] td[id=s_out_qty]").html( AppendComma(s_out_qty) );
                
                $("div[id^=div_order_edit] input[id=s_sch_qty]").val(s_sch_qty);
                $("div[id^=div_order_edit] input[id=pre_ets]").val(pre_ets);
                $("div[id^=div_order_edit] input[id=pre_etf]").val(pre_etf);
                $("div[id^=div_order_edit] input[id=des]").val($(this).attr('des'));
                $("div[id^=div_order_edit] input[id=PHP_s_id]").val($(this).attr('s_id'));
                $("div[id^=div_order_edit] input[id=PHP_p_id]").val($(this).attr('p_id'));
                $("div[id^=div_order_edit] input[id=PHP_line_id]").val($(this).attr('line_id'));
                $("div[id^=div_order_edit] input[id=PHP_line]").val($(this).attr('line'));
                $("div[id^=div_order_edit] input[id=PHP_ord_num]").val($(this).attr('ord_num'));
                $("div[id^=div_order_edit] input[id=PHP_mks]").val($(this).attr('mks'));
                
                var ie = ( $(this).attr('ie2') > 0 )?$(this).attr('ie2'):$(this).attr('ie1');
                $("div[id^=div_order_edit] input[id=ie]").val(ie);
                
                $("div[id^=div_order_edit] span[id=s_sch_qty]").html(s_sch_qty);
                $("div[id^=div_order_edit] span[id=spre_ets]").html(pre_ets);
                $("div[id^=div_order_edit] span[id=spre_etf]").html(pre_etf);
                $("div[id^=div_order_edit] span[id=des]").html($(this).attr('des'));

                if( status == 2 ) {
                    // $("div[id^=div_order_edit] input[id=s_sch_qty]").prop('readonly', true) ;
                    // $("div[id^=div_order_edit] input[id=pre_ets]").prop('readonly', true) ;
                    // $("div[id^=div_order_edit] input[id=pre_etf]").prop('readonly', true) ;
                    // $("div[id^=div_order_edit] input[id=des]").prop('readonly', true) ;
                    $("div[id^=div_order_edit] input[id=ReOpen]").show();
                    $("div[id^=div_order_edit] input[id=Finish]").hide();
                    $("div[id^=div_order_edit] input[id=Delete]").hide();
                    $("div[id^=div_order_edit] input[id=Submit]").hide();
                    $("div[id^=div_order_edit] input[id=Update]").hide();
                    
                    $("div[id^=div_order_edit] input[id=s_sch_qty]").hide();
                    $("div[id^=div_order_edit] input[id=pre_ets]").hide();
                    $("div[id^=div_order_edit] input[id=pre_etf]").hide();
                    $("div[id^=div_order_edit] input[id=des]").hide();
                    
                    $("div[id^=div_order_edit] span[id=s_sch_qty],span[id=spre_ets],span[id=spre_etf],span[id=des]").show();
                } else {
                    $("div[id^=div_order_edit] input[id=s_sch_qty]").show();
                    $("div[id^=div_order_edit] input[id=pre_ets]").show();
                    $("div[id^=div_order_edit] input[id=pre_etf]").show();
                    $("div[id^=div_order_edit] input[id=des]").show();
                    
                    $("div[id^=div_order_edit] span[id=s_sch_qty]").hide();
                    $("div[id^=div_order_edit] span[id=spre_ets]").hide();
                    $("div[id^=div_order_edit] span[id=spre_etf]").hide();
                    $("div[id^=div_order_edit] span[id=des]").hide();
                    
                    $("div[id^=div_order_edit] input[id=ReOpen]").hide();
                    $("div[id^=div_order_edit] input[id=Submit]").show();
                    $("div[id^=div_order_edit] input[id=Update]").show();
                    if( s_out_qty > 0 ){
                        $("div[id^=div_order_edit] input[id=Finish]").show();
                        $("div[id^=div_order_edit] input[id=Delete]").hide();
                    } else {
                        $("div[id^=div_order_edit] input[id=Finish]").hide();
                        $("div[id^=div_order_edit] input[id=Delete]").show();
                    }
                }
                
                divCenter('div_order_edit');
                // $('div[id^=div_order_edit] #order').
                // html($(this).attr(''));
            }
        });
   
        // bgcolor line
        $('.list_over').hover(function(){
            $(this).addClass("list_over_bg");
        },function(){
            $(this).removeClass("list_over_bg");  
        });
        
        
        $('td[id^=ms_]').bind('mousemove', function(e) {
            ms = true;
        }).bind('mouseover', function(e){
            ms = false;
        }).bind('mouseleave', function(e){
            ms = false;
        });

        // Line Bar Detail
        $('tr[id^=in]').bind('mouseleave', function(e){
            $('#notes').html('');
        }).bind('mousemove', function(e){
            ms_status = ms ? 'the beginning of the' : ( order_now ? 'behind of' : 'the end of' );
            ms_statuss = ms_status == 'the beginning of the' ? 1 : ( ms_status == 'behind of' ? 2 : ( ms_status == 'the end of' ? 3 : 0 ) ); 
            get_line(e.pageY);
                
            $('#drag_bar').html('Order : '+jQuery.trim(order_down)+' From Line : '+line_down+', \nMove To ' + ms_status + ' Line : ' + dragline + ( ms_statuss == 2 ? ( order_now ?' Order : '+ order_now :' ' ) : '' ) );
            $('div[id^=drag_bar]').offset({ left : e.pageX+26 , top : e.pageY+3 });
        }).bind('mousedown', function(e){
            line_down = line_up = line_id_down = line_id_up = '';
            // $('#m_mode').html('');
            line_down = get_line(e.pageY);
            line_id_down = dragline_id;
            if(ms)order_down = '?';
            // $('#m_mode').html(line_down+':'+order_down+':'+dragline_id);
        }).bind('mouseup', function(e){
        
            if( keyctrl == true ) {
                line_up = get_line(e.pageY);
                line_id_up = dragline_id;
                // $('#m_mode').html(line_down+':'+order_down+' ~ '+line_up+':'+order_up);
                $('div[id^=drag_bar]').remove();
                
                if( line_down=='?' || order_down=='?' || line_up=='?' || line_down=='' || order_down=='' || line_up=='' || s_id_down=='' ){
                    // $('#m_mode').html($('#m_mode').html()+' ~ 請重新選取');
                } else if ( line_down == line_up && s_id_down == s_id_up && p_id_down == p_id_up ) {
                    // $('#m_mode').html('沒有變動');
                } else {
                    // $('#m_mode').html();
                    $.blockUI();
                    ms_statuss = ms_status == 'the beginning of the' ? 1 : ( ms_status == 'behind of' ? 2 : ( ms_status == 'the end of' ? 3 : 0 ) ); 
                    
                    if( confirm('Are you sure !! \nOrder : '+jQuery.trim(order_down)+' From Line : '+line_down+', \nMove To ' + ms_status + ' Line : ' + line_up + ( ms_statuss == 2 ? ( order_now ?' Order : '+ order_now :' ' ) : '' )    ) ){
                        var msg = 'Order : '+jQuery.trim(order_down)+' From Line : '+line_down+', Move To ' + ms_status + ' Line : ' + line_up + ( ms_statuss == 2 ? ( order_now ?' Order : '+ order_now :' ' ) : '' );
                        $.ajax({
                            url: '?PHP_action=schedule_move',
                            type: 'POST',
                            data: {
                                line_id_down: line_id_down,
                                line_down   : line_down,
                                s_id_down   : s_id_down,
                                p_id_down   : p_id_down,
                                line_id_up  : line_id_up,
                                line_up     : line_up,
                                s_id_up     : s_id_up,
                                p_id_up     : p_id_up,
                                msg         : msg,
                                ms_status   : ms_statuss,
								contentType : "application/x-www-form-urlencoded; charset=Big5"
                            },
                            error: function(xhr, textStatus, jqXHR){
                                // alert("error");
                                // alert(JSON.stringify(xhr));
                                alert('Ajax request 發生錯誤'+xhr);
                            },
                            success: function(response) {
								// alert(response);
                                var sts = response.split(",");
                                // $('#m_mode').html(sts);
                                $('#schedule_append_edit').submit();
                            }
                        });
                    } else {
                        $.unblockUI();
                    }
                    
                }
                order_now = order_down = order_up = s_id_down = s_id_up = p_id_down = p_id_up = '';
                line_down = line_up = line_id_down = line_id_up = order_down = '';
            }
        });

        // Drag Bar Detail
        $('td[name^=drag_bar]').bind('mousedown', function(e) {
            if( keyctrl == true ) {
                order_now = order_down = order_up = s_id_down = s_id_up = p_id_down = p_id_up = '';
                $('div[id^=drag_bar]').remove();
                if( $(this).attr('ord_num') ){
                    
                    drag_html = '<div id="drag_bar" class="'+$(this).attr('class')+'" style="border:solid 1px black;height:'+$(this).height()+'px;width:'+$(this).width()+'px; position:absolute; z-index:3;cursor: default;font-size:'+$(this).css('font-size')+';color:'+$(this).css('color')+';'+$(this).attr('style')+';">Line:'+dragline+' /'+$(this).html()+'</div>';
                    $("body").append(drag_html);
                    $('div[id^=drag_bar]').offset({ left : e.pageX+26 , top : e.pageY+3 });
                    
                    order_now = jQuery.trim($(this).html());
                    order_down = $(this).html();
                    s_id_down = $(this).attr('s_id');
                    p_id_down = $(this).attr('p_id');
                    
                    drag_bar_status = true;
                    $('div[id^=div_order_detail]').hide();
                    
                    // $('#notes').html('['+order_down+']');
                }
            }
        }).bind('mouseup', function(e){
            if( keyctrl == true ) {
                if( $(this).attr('ord_num') ){
                    // $('#notes').html('['+$(this).attr('ord_num')+']');
                    s_id_up = $(this).attr('s_id');
                    p_id_up = $(this).attr('p_id');
                    $('div[id^=drag_bar]').remove();
                    order_up = $(this).html()?$(this).html():'';
                    drag_bar_status = false;
                }
            }
        }).bind('mouseover', function(e){
            order_now = '';
        }).bind('mouseleave', function(e){
            order_now = '';
        }).bind('mousemove', function(e){
            order_now = jQuery.trim($(this).html());
        });


	});
})(jQuery);

