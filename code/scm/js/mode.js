// ���X�W�Ǫ��ɦW
// var  LSA  =  $(file).val().split(".")[0].split("\\");
// var  CFN  =  LSA[LSA.length-1];

(function($){
	$(document).ready(function(){
		$("span[id='close']").click(function(){
			if (confirm('�������U UpLoad �~�i�W���ɮסA���W�ǽЫ��T�w�A�~��W�ǽЫ������C')) {
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
			'	<td class="snvy" bgcolor="#FFFFFF"><img onclick="delimg(this);" src="../images/del.gif" alt="�R��" style="cursor:pointer"></td>'+
			'</tr>';
			$("#cust_file").append(gg);
		});
		
		chk_file = function (file){
			$("#note").html("");
			/*
			var re = /\.(pdf|PDF|rar|RAR|xls|XLS|xlsx|XLSX)$/i;
			if ( !re.test($(file).val()) ) {
				alert('���ɦW���~'+$(file).val());
				var row = $(file).parent().parent();
				row.fadeOut('fast', function() {
					row.remove();
				});
			}/*
			var value = $(file).val();
			if( value.match("[\u4E00-\u9FA5]+") ){
				alert('���ɦW���i�H������'+$(file).val());
				var row = $(file).parent().parent();
				row.fadeOut('fast', function() {
					row.remove();
				});
			}
			if( value.match("[^&]-") ){
				alert('���ɦW���i�H����޸�'+$(file).val());
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
					alert('Ajax request �o�Ϳ��~'+xhr);
				},
				success: function(response) {
					var sts = response.split(",");
					if ( sts[0] == 'in' ) {
						alert('���ɮצs�b�I�p�G�~��W�ǱN�|�л\���ɮ� ( '+sts[1]+' )�I');
						$("#note").html('���ɮצs�b�I�p�G�~��W�ǱN�|�л\���ɮ� ( '+sts[1]+' )�I');
					} else {
						// alert('�S�ơI�~��ʧ@~');
					}
				}
			});
		};
		
		// Delete File
		drop_cust_po = function (elem,id,files,o_id){
		
			if ( confirm('�T�w�R�� PO ' + id ) ) {
            
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
						alert('Ajax request �o�Ϳ��~'+xhr);
					},
					success: function(response) {
						// alert(response);
						var arr = response.split("@"); 
						if ( arr[0] == 'ok' ) {
							var row = $(elem).parent().parent();
							row.fadeOut('fast', function() {
								row.remove();
								if ( arr[1] == 'ok' ) { 
									$("#note").html("Customer PO / "+files+" �R�����\");
								} else {
									$("#note").html("Customer PO �R�����\");
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