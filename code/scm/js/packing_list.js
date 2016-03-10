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

		$(".cls_static_IntValidate").keyup(function(){    
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		}).bind("paste",function(){  //CTR+V事件處理    
			$(this).val($(this).val().replace(/\D|^0/g,''));     
		}).css("ime-mode", "disabled"); //CSS設置輸入法不可用    
			
		function get_packing_list(po_no,ship_date_start,ship_date_end,carton_start,carton_end,shipway)
		{
			if(ship_date_start > ship_date_end)
			{
				alert("[First Date] can\'t greater than [Last Date]!!");
				return false;
			}
			else
			{
				if(carton_start > carton_end)
				{
					alert("[First Carton NO] can\'t greater than [Last Carton NO]!!");
					return false;
				}
				else
				{
					$.post("./packing_list.php?PHP_action=get_packing_list",{PHP_po_order:po_no,PHP_date_start:$("#start_calendar-inputField").val(),PHP_date_end:$("#end_calendar-inputField").val(),PHP_carton_start:carton_start,PHP_carton_end:carton_end,PHP_shipway:shipway},function(data) {
						//alert(data);
						$("#carton_list").html(data);
					});
				}
			}
		}		
		$("#search").click(function(){
			var po_no = ($.trim($("#txt_po_no").val())) == ""?"": $.trim($("#txt_po_no").val().replace(" ","_"));
			var ship_date_start = new Date($("#start_calendar-inputField").val().replace("-","/"));
			var ship_date_end = new Date($("#end_calendar-inputField").val().replace("-","/"));
			var carton_start = ($("#txt_carton_start").val()=="")?Number("0"):Number($("#txt_carton_start").val());
			var carton_end = ($("#txt_carton_end").val()=="")?Number("0"):Number($("#txt_carton_end").val());
			var shipway = $("#PHP_shipway").find(":selected").val();
			get_packing_list(po_no,ship_date_start,ship_date_end,carton_start,carton_end,shipway);
		});
		
		
		
	});
})(jQuery);

