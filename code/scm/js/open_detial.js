
<!--
/*開新視窗檢視詳細內容
		訂單fab和acc內容		==>		Show_ord(id,ord_num)
		主料								==>		Show_fab(code)
		副料								==>		Show_acc(code)
*/


//訂單fab和acc內容
window.open2=window.open; //在此把window.open()函數複製一份給window.open2，未來若要開新視窗則執行window.open2()即可
window.open=function(){return (new Object())}; //把window.open()函數變成空白函數，令提供者的跳窗無法顯示

function Show_ord(id,ord_num) {
	var url ='index2.php?PHP_action=order_fa_view&PHP_id='+id+'&cgiget=&cgino=&cgi_1=&cgi_2=&cgi_3=&cgi_4=&cgi_5=&cgi_6=&cgi_7=&PHP_ord_num='+ord_num+"&PHP_resize=1"; 	
	var nm = 'Order';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
}

function Show_smpl_ord(id) {
	 var url ='index2.php?PHP_action=smpl_fa_view&PHP_id='+id+'&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_ref=&PHP_factory=&cgino=&cgi_5=&PHP_etdstr=&PHP_etdfsh=&PHP_resize=1';
	 var nm = 'Order';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=700 top=50, left=100');
}

//主料
function Show_fab(code) {
	var url ="index2.php?PHP_action=lots_view&PHP_code="+code+"&SCH_lots_code=&SCH_lots_name=&SCH_comp=&SCH_cat1=&SCH_cat2=&SCH_mile=&SCH_cons=&PHP_resize=1"; 
	var nm = 'Fabric';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=700,height=600 top=50, left=100');
}

//副料
function Show_acc(code) {
	var url ="index2.php?PHP_action=acc_view&PHP_code="+code+"&PHP_acc_code=&PHP_acc_name=&PHP_des=&PHP_resize=1"; 
	var nm = 'Accessory';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=700,height=600 top=50, left=100');
}

function bom_view(num,ap_num) {
	 var url ="index2.php?PHP_action=pa_bom_det&PHP_bom_num="+num+"&PHP_pa_num="+ap_num+"&PHP_sr=&PHP_dept_code=&PHP_etdfsh=&PHP_cust=&PHP_wi_num=&PHP_etdstr=&PHP_fty_sch=&PHP_resize=1"; 
	 var nm = 'BOM';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=900,height=600 top=50, left=100');
}

function po_bom_view(num,po_num) {
    var url ="po.php?PHP_action=po_bom_det&PHP_bom_num="+num+"&PHP_po_num="+po_num+"&PHP_resize=1"; 
    var nm = 'BOM';
    window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=850,height=600 top=50, left=100');
}

function po_bom_views(num,po_num) {
	 var url ="po.php?PHP_action=po_bom_status&PHP_bom_num="+num+"&PHP_po_num="+po_num+"&PHP_resize=1"; 
	 var nm = 'BOM_STATUS';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=850,height=600 top=50, left=100');
}

function smpl_out(smpl_num) {
	 var url ="index2.php?PHP_action=show_sample_fa_det&PHP_smpl_num="+smpl_num+"&PHP_resize=1"; 
	 var nm = 'sample';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
}

function pa_show(pa_num){
	var gofile ='index2.php?PHP_action=apply_view&PHP_aply_num='+pa_num+"&PHP_resize=1"; 	
	style=window.open2(gofile,'pa','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=600, top=100, left=150');
}

function po_show(pa_num,mat_code){
	var gofile ='po.php?PHP_action=po_rcv_view&PHP_aply_num='+pa_num+'&PHP_mat_code='+mat_code+"&PHP_resize=1"; 
	style=window.open2(gofile,'po','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600, top=50, left=150');
}

function show_order_rec(ord_id,ord_num){
	 var url ='index2.php?PHP_action=order_view&PHP_id='+ord_id+'&cgiget=&cgino=&cgi_1=&cgi_2=&cgi_3=&cgi_4=&cgi_5=&cgi_6=&cgi_7&PHP_ord_num='+ord_num+"&PHP_resize=1"; 	
	window.open2(url,'Order','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
}

function message_out(user_id) {
	 var url ='notify.php?PHP_action=message_send&PHP_user_id='+user_id+"&PHP_resize=1"; 
	 var nm = 'message';
	 window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=700,height=500 top=50, left=100');
}

function open_exc(id)
{
	 var url ='exception.php?PHP_action=exc_show&PHP_id='+id+'&PHP_sr_startno=0&SCH_ord=&PHP_cust=&PHP_resize=1';
	 var nm = 'Exceptional';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
}

function open_dn(id)
{
	 var url ='dabit.php?PHP_action=dn_show&PHP_id='+id+'&PHP_sr_startno=0&SCH_ord=&SCH_to=&PHP_resize=1';
	 var nm = 'DebitNote';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
}

function open_rem_exc(id)
{
  var url ='exception.php?PHP_action=rem_exc_show&PHP_id='+id+'&PHP_sr_startno=0&SCH_ord=&PHP_cust=&PHP_resize=1';
  var nm = 'LREPORT';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
}

function open_lr(id)
{
  var url ='rcv_rpt.php?PHP_action=lots_rpt_view&PHP_id='+id+"&PHP_resize=1"; 
  var nm = 'RemunExceptional';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=870,height=560 top=50, left=100');
}

function show_output(order_num)
{
	 var url ='index2.php?PHP_action=order_output&PHP_ord_num='+order_num+"&PHP_resize=1"; 
	 var nm = 'production';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100');
}

function open_remun(id)
{
  var url ='cost.php?PHP_action=remun_view&PHP_renum='+id+"&PHP_resize=1"; 
  var nm = 'Remundet';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=900,height=560 top=50, left=100');
}

function open_packing(id)
{
  var url ='shipdoc.php?PHP_action=shipdoc_p_view&PHP_id='+id+"&PHP_resize=1"; 
  var nm = 'shipdoc';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=950,height=560 top=50, left=100');
}

function open_inv(id)
{
  var url ='order.php?PHP_action=ship_invoice_view&PHP_id='+id+"&PHP_resize=1"; 
  var nm = 'shipdoc';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=820,height=560 top=50, left=100');
}

function open_remnant(num)
{
  var url ='ord_close.php?PHP_action=remnant_view&PHP_ord_num='+num+"&PHP_resize=1"; 
  var nm = 'shipdoc';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=900,height=560 top=50, left=100');
}

function tblresize() {
	var dh = 150;
	var dw = 60;
	var hh = tblShow.offsetHeight + dh;
	if ( hh > screen.height - dh )
	hh = screen.height - dh ;
	if (tblShow.offsetWidth) window.resizeTo( tblShow.offsetWidth + dw , hh );
	self.focus();
}

function show_rcv(rcv_num) {
	var url ="receive.php?PHP_action=rcvd_view&PHP_rcv_num="+rcv_num+"&PHP_resize=1";
	var nm = 'order';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=850,height=600 top=30, left=100');
}

function show_apb(rcv_num,payment,status) {

	if ( payment == 'before|%' || ( payment == 'T/T before shipment' && status <= 2) ) { 
		//<!-- 請款單 40|60 40 -->
		var url ="apb.php?PHP_action=po_before_view&PHP_rcv_num="+rcv_num+"&PHP_resize=1";

	} else if ( payment == '%|after' ) { 
		//<!-- 驗收付款單 40|60 60 -->
		var url ="apb.php?PHP_action=rcvd_after_view&PHP_rcv_num="+rcv_num+"&PHP_resize=1";
	} else if ( status >= 3 ) { 
		//<!-- 請款單 -->
		var url ="apb.php?PHP_action=po_before_apb_view&PHP_rcv_num="+rcv_num+"&PHP_resize=1";
	} else {
		//<!-- 驗收付款單 -->
		var url ="apb.php?PHP_action=rcvd_view&PHP_rcv_num="+rcv_num+"&PHP_resize=1";
	}
	//var url ="apb.php?PHP_action=rcvd_view&PHP_rcv_num="+rcv_num+"&PHP_resize=1";
//alert(payment+' - '+status+' - '+url);
	var nm = 'order';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=850,height=600 top=30, left=100');
}
--> 
