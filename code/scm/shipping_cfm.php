<?php
session_start();
session_register	('ord_parm');
session_register	('add_sch');
session_register	('sch_parm');

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//$AUTH = '074';
$AUTH = '109';
$PGM = $_SESSION['ITEM']['ADMIN_PERM'];
$op = array();

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================

case "main":
check_authority($AUTH,$PGM[1]);

$op = $shipping_doc->search(2);

for ( $i=0; $i<sizeof($op['ship_doc']); $i++ ) {
	$op['ship_doc'][$i]['status'] = $shipping_doc->get_status($op['ship_doc'][$i]['status']);
	$str = $shipping_doc->get_data('ord_num,cust_po', $op['ship_doc'][$i]['id']);
	$op['ship_doc'][$i]['ord_num'] = $str['ord_num'];
	$op['ship_doc'][$i]['cust_po'] = $str['cust_po'];
}

if(isset($_GET['PHP_msg'])) $op['msg'][] = $_GET['PHP_msg'];

page_display($op,$AUTH,'ship_un_sch_list.html');
break;

case "main_new":
check_authority('109',$PGM[1]);

$op = $shipping_doc->search_new(2);

for ( $i=0; $i<sizeof($op['ship_doc']); $i++ ) {
	$op['ship_doc'][$i]['status'] = $shipping_doc->get_status($op['ship_doc'][$i]['status']);
	$str = $shipping_doc->get_data_new('ord_num,po', $op['ship_doc'][$i]['id']);
	$op['ship_doc'][$i]['ord_num'] = $str['ord_num'];
	$op['ship_doc'][$i]['cust_po'] = $str['cust_po'];
}
$op['new_page'] = 1;
if(isset($_GET['PHP_msg'])) $op['msg'][] = $_GET['PHP_msg'];

page_display($op,'109','ship_un_sch_list.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_un_invoice_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_un_invoice_view":
check_authority($AUTH,$PGM[1]);
$op = $shipping_doc->get_invoice($PHP_id);//ÂÂªº


page_display($op,$AUTH,'ship_un_invoice_view.html');
break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_un_invoice_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_un_invoice_view_new":
check_authority('109',$PGM[1]);

$op = $shipping_doc->get_invoice_new($PHP_id);//·sªº


page_display($op,'109','ship_un_invoice_view_new.html');
break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_confirm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_confirm":
check_authority($AUTH,$PGM[2]);
if(!$_POST['PHP_new'])
{
	if ( $shipping_doc->ship_invoice_confirm($_POST['PHP_id']) ) {

		$_SESSION['MSG'][] = $mesg = "SUCCESS CONFIRM SHIPPING DOCUMENT ON INVOICE : [ ".$_POST['PHP_invoice']." ]";
		$log->log_add(0,$AUTH."C",$mesg);
		
		$back_str ="shipping_cfm.php?PHP_action=main";
		redirect_page($back_str);

	}
}
else
{
	if ( $shipping_doc->ship_invoice_confirm($_POST['PHP_id']) ) {

		$_SESSION['MSG'][] = $mesg = "SUCCESS CONFIRM SHIPPING DOCUMENT ON INVOICE : [ ".$_POST['PHP_invoice']." ]";
		$log->log_add(0,$AUTH."C",$mesg);
		
		$back_str ="shipping_cfm.php?PHP_action=main_new";
		//$back_str ="shipping.php?PHP_action=ship_sch_new";
		redirect_page($back_str);

	}
}
break;




}   // end case ---------
?>
