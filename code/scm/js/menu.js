(function($){

    $(document).ready(function(){

        $("#m_active").live("click", function () {

            var $m = $("#menu"), $c = $("#m_active");

            if ($m.position().left > 0) {
                $m.animate({ "left": -$m.width(), opacity: 0 }, 666);
                $("#m_active").html("MENU OPEN");
            } else {
                $m.show().animate({ left: $c.position().left , opacity: 1 }, 666);
                $("#m_active").html("MENU CLOSE");
            }            
        });
    
    
    
		edit = function(id){
			$("#para_d_"+id).hide();
			$("#para_v_"+id).show();
		};

		update = function(id){

			$.blockUI({ message: ' Loding ...'});

			var para_name = $("#name_v_"+id).val();
			var para_key = $("#key_v_"+id).val();
			var para_val = $("#val_v_"+id).val();

			$.ajax({
				url: 'mode.php?mode=up_alert_set',
				type: 'GET',
				data: {
					para_id: 	id,
					para_name: 	para_name,
					para_key: 	para_key,
					para_val: 	para_val
				},
				error: function(xhr) {
					alert('Ajax request 發生錯誤'+xhr);
				},
				success: function(response) {
					var sts = response.split(",");
					if ( sts[0] == 'true' ) {
						alert(sts[1]);
						location.reload();
						$("#name_d_"+id).html(para_name);
						$("#key_d_"+id).html(para_key);
						$("#val_d_"+id).html(para_val);
						$("#para_d_"+id).show();
						$("#para_v_"+id).hide();						
					} else {
						alert(sts[1]);
					}
					$.unblockUI();
				}
			});
		};

		$('.showpic').hover(function(){
			// $('#img_b_mk').mask('Waiting...');
			$("#img_b").attr({'src':$(this).attr('medium')}); 

			// $('#img_b_mk').unmask();    
			// $(this).addClass("list_txt_bg_d");
		},function(){
			// $("#img_b").attr({'src':$("#showpic").val()}); 
			// $('#img_b_mk').unmask();
			// $(this).removeClass("list_txt_bg_d");  
		});

		$("img[id^='simg']").hover(function(){
			$(this).animate({ height: '160px' , width: '160px' }, 200);
		},function(){
			$(this).animate({ height: '120px' , width: '120px' }, 200);
		});

		$("img[id^='sn']").hover(function(){
			$(this).css({'cursor':'pointer'});
		});

		$("img[id^='sn']").click(function(){
			$("#product_sn").html($(this).attr("alt"));
		});


	});

})(jQuery);