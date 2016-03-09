// 取出上傳的檔名
// var  LSA  =  $(file).val().split(".")[0].split("\\");
// var  CFN  =  LSA[LSA.length-1];

(function($){
	$(document).ready(function(){
		$("span[id='close']").click(function(){
			if (confirm('必須按下 UpLoad 才可上傳檔案，放棄上傳請按確定，繼續上傳請按取消。')) {
				$("#note").html("");
				$("div[id='cust_po']").attr({'style':'display:none;'});
				$("div[id='cust_po_view']").attr({'style':'display:block'});
				return true;
			}
			
			return false;
		});
		
		$("#append_file").click(function(){
			var gg =
			'<tr>'+
			'	<td class="snvy" bgcolor="#dddddd"><input type="text" name="PHP_cust_po[]" size="18" value="" class="select" onkeyup="value=value.replace(/[^A-z0-9-]/g,\'\')" style="text-transform:uppercase" ></td>'+
			'	<td class="snvy" bgcolor="#FFFFFF">'+
			'  		<input type="file" onchange="chk_file(this);" name="PHP_cust_file[]" size="66" value="" class="select">'+
			'	</td>'+	
			'	<td class="snvy" bgcolor="#FFFFFF"></td>'+
			'	<td class="snvy" bgcolor="#FFFFFF"><img onclick="delimg(this);" src="../images/del.gif" alt="刪除" style="cursor:pointer"></td>'+
			'</tr>';
			$("#cust_file").append(gg);
		});
		
		chk_file = function (file){
			$("#note").html("");
			/*
			var re = /\.(pdf|PDF|rar|RAR|xls|XLS|xlsx|XLSX)$/i;
			if ( !re.test($(file).val()) ) {
				alert('附檔名錯誤'+$(file).val());
				var row = $(file).parent().parent();
				row.fadeOut('fast', function() {
					row.remove();
				});
			}/*
			var value = $(file).val();
			if( value.match("[\u4E00-\u9FA5]+") ){
				alert('附檔名不可以有中文'+$(file).val());
				var row = $(file).parent().parent();
				row.fadeOut('fast', function() {
					row.remove();
				});
			}
			if( value.match("[^&]-") ){
				alert('附檔名不可以有單引號'+$(file).val());
				var row = $(file).parent().parent();
				row.fadeOut('fast', function() {
					row.remove();
				});
			}*/
			check_cust_po(file);
		}
		
		check_cust_po = function (file){
		
			var  LSA  =  $(file).val().split("\\");
			var  CFN  =  LSA[LSA.length-1]
			
			$.ajax({
				url: 'order.php?PHP_action=check_cust_po',
				type: 'GET',
				data: {
					file: CFN
				},
				error: function(xhr) {
					alert('Ajax request 發生錯誤'+xhr);
				},
				success: function(response) {
					var sts = response.split(",");
					if ( sts[0] == 'in' ) {
						alert('該檔案存在！如果繼續上傳將會覆蓋此檔案 ( '+sts[1]+' )！');
						$("#note").html('該檔案存在！如果繼續上傳將會覆蓋此檔案 ( '+sts[1]+' )！');
					} else {
						// alert('沒事！繼續動作~');
					}
				}
			});
		};
		
		// Delete File
		drop_cust_po = function (elem,id,files,o_id){
		
			if ( confirm('確定刪除 PO ' + id ) ) {
            
                $.blockUI({ message: ' Loding ...'});
		
				$.ajax({
					url: 'order.php?PHP_action=drop_cust_po',
					type: 'GET',
					data: {
						po_num	: id,
						files	: files,
						order_id: o_id
					},
					error: function(xhr) {
						alert('Ajax request 發生錯誤'+xhr);
					},
					success: function(response) {
						// alert(response);
						var arr = response.split("@"); 
						if ( arr[0] == 'ok' ) {
							var row = $(elem).parent().parent();
							row.fadeOut('fast', function() {
								row.remove();
								if ( arr[1] == 'ok' ) { 
									$("#note").html("Customer PO / "+files+" 刪除成功");
								} else {
									$("#note").html("Customer PO 刪除成功");
								}
							});
						} else {
							$("#note").html(response);
							// alert(response);
						}
					}
				});
			
			}
			
		};
        
		$("#combine_add_img").click(function(){
			$("td[id='combine_add_td']").show();
            $("td[id='combine_txt']").hide();
			// $("td[id='combine_add_td']").attr({'style':'display:block'});
		});
		
		$("#combine_del_img").click(function(){
            $("td[id='combine_txt']").show();
			$("td[id='combine_add_td']").hide();
			// $("td[id='combine_add_td']").attr({'style':'display:block'});
		});
		
	});
})(jQuery);

function delimg(elem){
	var row = $(elem).parent().parent();
	row.fadeOut('fast', function() {
		row.remove();
	});
}