<?php
#
#
#
session_start();
// echo $PHP_action.'<br>';
#
#
#
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
require_once($config['root_dir']."/lib/class.warehousing.php");
$WAREHOUSING = new WAREHOUSING();
if (!$WAREHOUSING->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '083';
#
#
#
switch ($PHP_action) {
#
#
#
#
#
#
# :main
default;
case "incoming_material":
check_authority($AUTH,"view");

$op['css'] = array( 'css/scm.css' , 'css/jquery-autocomplete.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery-autocomplete.js' , 'js/jquery.blockUI.js' );

$User_dept = $_SESSION['USER']['ADMIN']['dept'];

if( is_dept($User_dept) ){
	$op['dept'] = $User_dept;
} else {
	$op['dept'] = $User_dept = $_GET['PHP_dept'];
	$op['dept_select'] = get_dept_code("incoming_chg(this)");
}

$op['dept_html'] = $WAREHOUSING->get_carrier_list($User_dept,'y','n');

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}
$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,'incoming_material.html');
break;
#
#
#
#
#
#
# :get_carrier_num
case "get_carrier_num":
check_authority($AUTH,"view");

header("Content-Type:text/html;charset=BIG5");

if( !empty($_GET['carrier_num']) ) {
	if ( $str = $WAREHOUSING->get_carrier_num( $_GET['carrier_num'],$_GET['arrival_active'],$_GET['incoming_active'] ) ) {
		echo 'ok@'.(implode(',',$str));
	} else {
		echo 'off@nodata'.$str.$_GET['carrier_num'];
	}
}

break;
#
#
#
#
#
#
# :incoming_material_search 
case "incoming_material_search":
check_authority($AUTH,"view");


$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js', 'js/open_detial.js' );

# 判斷是否有輸入提單號碼
$bl_num = !empty($_GET['bl_num'])? $_GET['bl_num']: '' ;
$carrier_num = !empty($_GET['carrier_num'])? $_GET['carrier_num'] : '';

if( $Po_ship = $WAREHOUSING->get_po_ship( $bl_num , $carrier_num , 'user' ) ){
    $Po_ship['arrival_user'] = big52uni(get_user_html($Po_ship['arrival_user']));
	$op['po_ship'] = $Po_ship;
	if( $Po_ship_det = $WAREHOUSING->get_po_ship_det_group_by_mat($Po_ship['id']) ){
		$op['po_ship_det'] = $Po_ship_det;
	}

	# 取訂單號碼 分攤數量
	// $p_size = sizeof($op['po_ship_det']);
	// for($i=0;$i<$p_size;$i++){
		// $op['po_ship_det'][$i]['orders'] = $WAREHOUSING->avg_order_qty4incoming($op['po_ship_det'][$i]['ap_num'], $op['po_ship_det'][$i]['mat_cat'], $op['po_ship_det'][$i]['mat_id'], $op['po_ship_det'][$i]['color'], $op['po_ship_det'][$i]['size'], $op['po_ship_det'][$i]['po_unit'], $op['po_ship_det'][$i]['remain']);
	// }
	
	# 確認是否在盤點中
	$Dept = $Po_ship['dist'];
	$Item = $Po_ship_det[0]['mat_cat'] == 'l' ? 'lots' : 'acc' ;
	$ver = $WAREHOUSING->get_ver( $Dept , $Item );
    // exit;
	if( !empty($ver) && $ver['confirm_user'] == '' ){
		$_SESSION['MSG'][] = 'Inventory !!';
		header ("Location: ".$PHP_SELF."?PHP_action=incoming_material");
		exit;
	}
	$op['ver'] = $ver['ver'];
	
	if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
		$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
	}

	$utf_field_size = sizeof($utf_field['utf_field']);
	for( $i=0; $i<$utf_field_size; $i++){
		$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
	}
	
	page_display($op,$AUTH,'incoming_material_search.html');
} else {
	$_SESSION['MSG'][] = 'No Data!!';
	header ("Location: ".$PHP_SELF."?PHP_action=incoming_material");
}
break;
#
#
#
#
#
#
# :append_incoming_qty 
case "append_incoming_qty":
check_authority($AUTH,"add");

if( $WAREHOUSING->append_incoming_qty('i',$_POST) ) {
	$_SESSION['MSG'][] = $message = "Successfully Append Arrival, carrier number : ". $_POST['bl_num'];
	$log->log_add(0,$AUTH."A",$message);
} else {
	$_SESSION['MSG'][] = "Successfully Append Arrival, carrier number : ". $_POST['bl_num'];
}

header ("Location: ".$PHP_SELF."?PHP_action=incoming_material");
break;
#
#
#
#
#
#
# :rev_po_det 
case "rev_po_det":
	check_authority('043',"view");

	$op['rcvd'] = $WAREHOUSING->get_inventory_det('i', $_GET['PHP_po_nums'], $_GET['PHP_mat_cat'], $_GET['PHP_mat_id'], $_GET['PHP_color'], $_GET['PHP_size']);
	
	page_display($op, '043', $TPL_RCV_PO_SHOW );
break;
} # CASE END
?>
