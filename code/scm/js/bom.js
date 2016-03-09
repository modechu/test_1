(function($){

	$(document).ready(function(){

		$('td[id^=HT-]').hover(function(){
			$(this).addClass("m_p_line");
		},function(){
            $(this).removeClass("m_p_line");
		});
        
		$('td[id^=HT-]').live('click', function(){
            $id = $(this).attr('id').split("-");
            $id = $id[1];
            if( $('td[id^=H-'+$id+']').is(':visible') ){
                $('td[id^=H-'+$id+']').hide();
                $('img[id^=HT-'+$id+']').attr({src:'./js/src/css/img/nav-right.gif'});
                $('td[id^=HT-'+$id+']').removeClass("f_62B50F");
                $('td[id^=HT-'+$id+']').addClass("BomName");
            } else {
                $('td[id^=H-'+$id+']').show();
                $('img[id^=HT-'+$id+']').attr({src:'./js/src/css/img/nav-left.gif'});
                $('td[id^=HT-'+$id+']').addClass("f_62B50F");
                $('td[id^=HT-'+$id+']').removeClass("BomName");
            }
		});
        
		// <TR> MOUSEOVER
		$('tr[id^=Bom_TR]').hover(function(){
			$(this).addClass("Bom_TR");
		},function(){
			$(this).removeClass("Bom_TR");
		});
		
		// <TR> MOUSEOVER
		$('tr[id^=Bom_TR_link]').hover(function(){
			$(this).addClass("Bom_TR_link");
		},function(){
			$(this).removeClass("Bom_TR_link");  
		});
        
		// <TR> MOUSEOVER
		$('tr[id^=Bom_TR_Index],tr[id=Bom_show_all]').hover(function(){
			$(this).addClass("Bom_TR_link");
		},function(){
			$(this).removeClass("Bom_TR_link");  
		});
        
		$('tr[id=Bom_TR_hover]').hover(function(){
			$(this).addClass("Bom_TR_hover");
		},function(){
			$(this).removeClass("Bom_TR_hover");  
		});
        
		$('tr[id^=Bom_TR_Index]').live('click', function(){
            $p_id = $(this).attr('p_id');
            $('tr[class^=Bom_dis_').hide();
            $('tr[id=Bom_show_all]').show();
            $('tr[class^=Bom_dis_'+$p_id+']').show();
            $('td[id^=bom_color_]').hide();
            $('td[id^=bom_color_'+$p_id+']').show();
            // alert($('#breakdown tr').size());
            status = -2;
            $('#breakdown tr').each(function(i, elm) {
                if( $(elm).is(":visible") == true && $(elm).attr("class").substr(0,8) == 'Bom_dis_' ){
                    status++;
                }
            });
            // alert(status);
            $("td[id^='colspan']").attr({'colspan':status});
            
            // alert(status);
            
		});

		$('tr[id=Bom_show_all]').live('click', function(){
            $('tr[class^=Bom_dis]').show();
            $('tr[id=Bom_show_all]').hide();
            $('td[id^=bom_color_]').show();
            status = 0;
            $('#breakdown tr').each(function(i, elm) {
                if( $(elm).attr("mark") == '1' ){
                    status++;
                }
            });
            $("td[id^='colspan']").attr({'colspan':status});
		});

		// $('tr #notice_adjust_link').hover(function(){
			// $(this).addClass("b_add");
		// },function(){
			// $(this).removeClass("submit_over"); 
		// }).bind('selectstart', function(){
			// return false; 
		// }).css('MozUserSelect','none');

	});
})(jQuery);


function cfm_revise(ord_num,id) {
	if ( confirm("are you sure to REVISE order :#[ "+ord_num+" ] ?\r\rEvery Revise event will cancel entire process had done !\rAnd restart the Order procedure from beginning Again !\r\rBe sure this is OK for you!")) {
		location.href="bom.php?PHP_action=revise_bom&PHP_id="+id+"{/literal}{$back_str2}{literal}";
	} 
}

function bom_file_del(file_name,table,id,wi_id,file_name) {
    if ( confirm("Are you shure to delete this file  ??")) {
        location.href="bom.php?PHP_action=do_bom_file_del&PHP_id="+id+"&PHP_talbe="+table+"&PHP_wi_id="+wi_id+"&PHP_file_name="+file_name+"&PHP_back=bom_view";
    } 
}

function file_del(id,wi_num,back_str) {
	if ( confirm("Are you shure to delete [ "+wi_num+" ] TRIM CARD file  ??")) {
		location.href="bom.php?PHP_action=do_trim_del&PHP_id="+id+"&PHP_num="+wi_num+back_str;
	} 
}
 
function check(obj){
    var obj;
    var max=126;
    obj.value=obj.value.substring(0,max);
}

function fty_pattern_file_del(id,wi_id){
	if ( confirm("Are you shure to delete Fty pattern file  ??")) {
		location.href="bom.php?PHP_action=do_fty_pattern_del&PHP_id="+id+"&wi_id="+wi_id;
	} 
}

$(document).ready(function(){

    $('tr[id=color_name] td[id^=MK_pid]').hover(function(){
        $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:;'});
        $("div[id=preview1]").css({"left" : event.clientX + document.body.scrollLeft + 26 + 'px'});
        $("div[id=preview1]").css({"top" : event.clientY + document.body.scrollTop + 20 + 'px'});
        $("span[id=fab_placement]").html($(this).attr("value"));
    },function(){
        $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:hidden;'});
    });
    
    $('td[id^=detail]').hover(function(){
        $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:;'});
        $("div[id=preview1]").css({"left" : event.clientX + document.body.scrollLeft + 26 + 'px'});
        $("div[id=preview1]").css({"top" : event.clientY + document.body.scrollTop + 20 + 'px'});
        $("span[id=fab_placement]").html($(this).attr("value"));
    },function(){
        $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:hidden;'});
    });

    $("td[Mid^=pid]").live('mousedown', function(e){
        $("td[id^=MK_pid]").hide();
        $("table[id=color_table] td[id^=breakdown]").hide();
        id = $(this).attr("pid");
        $("td[pid^="+id+"]").show();
    });

    $("td[id=table_display]").live('mousedown', function(e){
        if ( $("table[id=color_table]").is(':visible') ) {
            $("table[id=color_table]").hide();
            $("td[id=table_display]").html('顯示(Show)');
        } else {
            $("table[id=color_table]").show();
            $("td[id=table_display]").html('隱藏(Hide)');
        }
    });

    reload = function (id){
        $("td[id^=MK_pid]").hide();
        $("table[id=color_table] td[id^=breakdown]").hide();
        $("td[pid^="+id+"]").show();
        
        $("img[id^=pic]").each(function(i, elm) {
            //alert(i);
            var status = $(elm).attr('status');
            var bomid = $(elm).attr('bomid');
            
            if ( status == 2 ) {
                pp = '<div bom_id="'+bomid+'" style="top:'+$(elm).offset().top+'px;left:'+$(elm).offset().left+'px;height:'+$(elm).height()+'px;width:6px;white-space:nowrap;position:absolute;color:#FFFFFF;z-index:1300;background-color:#00A002;opacity:0.5;"></div>';
            } else if ( status == 1 ) {
                pp = '<div bom_id="'+bomid+'" style="top:'+$(elm).offset().top+'px;left:'+$(elm).offset().left+'px;height:'+$(elm).height()+'px;width:6px;white-space:nowrap;position:absolute;color:#FFFFFF;z-index:1300;background-color:#A00200;opacity:0.5;"></div>';
            } else {
                pp = '';
            }
            //pp += '<img bt="1" bom_id="'+bomid+'" src="images/upload.gif" style="top:'+($(elm).offset().top+30)+'px;left:'+$(elm).offset().left+'px;white-space:nowrap;position:absolute;z-index:1300;">';
            $("body").append(pp);
        });
    }

    // $("td[Mid^=pid]").live('mousedown', function(e){
        // $("td[id^=MK_pid]").hide();
        // $("table[id=color_table] td[id^=breakdown]").hide();
        // id = $(this).attr("pid");
        // $("td[pid^="+id+"]").show();
    // });
    
    
    
    // 以下正在進行修改 不關正常顯示問題
    $("td[id^=OPF]").live('mousedown', function(e){
        if( $("td[UpLoad^=1]").is(':visible') ){
            $("td[UpLoad^=1]").hide();
        } else {
            $("td[UpLoad^=1]").show();
        }

        $("img[id^=pic]").each(function(i, elm) {
            var bomid = $(elm).attr('bomid');
            $("div[bom_id="+bomid+"]").css({"top" : $(elm).offset().top + 'px'});
            $("div[bom_id="+bomid+"]").css({"left" : $(elm).offset().left + 'px'});
        }); 
    });
    
    $("img[bt^=1]").live('mousedown', function(e){
        var bomid = $(this).attr('bom_id');
        alert(bomid);
    });
    
    $('img[id^=pic]').hover(function(){
        num = Math.floor(Math.random() * (100-1+1)+1);
    //$("img[id^=pic]").live('MouseOver', function(e){
        var bomid = $(this).attr('bomid');
        var bom_code = $(this).attr('bom_code');
        var status = $(this).attr('status');
        if( status > 0 ) {
            $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:;'});
            $("div[id=preview1]").css({"left" : event.clientX + document.body.scrollLeft + 26 + 'px'});
            $("div[id=preview1]").css({"top" : event.clientY + document.body.scrollTop - 90 + 'px'});
            $("span[id=fab_placement]").html('<img src="images/bom_pic/'+bom_code+'_middle.jpg?'+num+'">');
            $("img[bom_id^="+bomid+"]").show();
        }
    },function(){
        var bomid = $(this).attr('bomid');
        var status = $(this).attr('status');
        if( status > 0 ) {
            $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:hidden;'});
            $("img[bom_id^="+bomid+"]").hide();
        }
    });

    //$('img[id^=pic],img[bt^=1]').live('MouseOver', function(e){
    //    var bomid = $(this).attr('bomid');
    //    $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:hidden;'});
    //    $("img[bom_id^="+bomid+"]").hide();
    //});      
    
    //$('img[id^=pic],img[bt^=1]').live('MouseOut', function(e){
    //    var bomid = $(this).attr('bomid');
    //    $("div[id=preview1]").attr({'style':'position:absolute; z-index:1;width:10; filter:revealTrans(duration=0.3); visibility:hidden;'});
    //    $("img[bom_id^="+bomid+"]").hide();
    //});
        
    ajaxFileUpload = function ( wi_id , mat , id ) {
    
        num = Math.floor(Math.random() * (100-1+1)+1);
        
        mat_code = mat+'_'+id;
        //alert(mat_code);
		$("#loading")
		.ajaxStart(function(){
			//$(this).show();
            $.blockUI();
		})
		.ajaxComplete(function(){
			//$(this).hide();
            $.unblockUI();
		});

		$.ajaxFileUpload
		(
			{
				url:'bom.php?PHP_action=upload_bom_pic&mat_code='+mat_code+'&Mat='+mat+'&Bom_id='+id+'&Wi_id='+wi_id,
				secureuri:false,
				fileElementId: mat_code,
				dataType: 'script',
				success: function (data, status)
				{
                    var sts = data.split(",");
                    
                    var str = '';
                    for( var d=1; d < sts.length; d++){
                        if(typeof(sts[d]) != 'undefined'){
                            str += sts[d]+'\n';
                        }
                    }

					if(sts[0] == 'error')
					{
						alert(str);
					}else{

                        $('img[bom_code='+mat_code+']').attr({'src':'images/bom_pic/'+mat_code+'_small.jpg?'+num,'status':'1'});
                        alert(str);
                    }
				},
				error: function (data, status, e)
				{
                    alert(data+'_'+status+'_'+e);
					alert(e);
				}
			}
		)
        
       // $("img[id^=loading]").hide();
        return false;
    }

});