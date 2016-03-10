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
include_once($config['root_dir']."/lib/class.warehousing.php");
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
# Template
$TPL_MATERIALS_RETREAT_ADJUSTMENT = 'returned_material_adjustment.html';
$TPL_MATERIALS_RETREAT_ADJUSTMENT_SEARCH_INCOMING = 'returned_material_adjustment_search_incoming.html';
$TPL_MATERIALS_RETREAT_ADJUSTMENT_SEARCH_RECEIVING = 'returned_material_adjustment_search_receiving.html';
$TPL_RETURN_SEND_MATERIAL_LIST = "return_send_material_list.html";
$TPL_RECEIVING_TO_GET_MATERIAL_SEND_SEARCH = "receiving_to_get_material_send_search.html";
#
#
#
$AUTH = '085';
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
case "returned_material_adjustment":
check_authority($AUTH,"view");

$op['css'] = array( 'css/scm.css' , 'css/jquery-autocomplete.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery-autocomplete.js' , 'js/jquery.blockUI.js' );

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,$TPL_MATERIALS_RETREAT_ADJUSTMENT);
break;
#
#
#
#
#
#
# :returned_material_adjustment_search_incoming
case "returned_material_adjustment_search_incoming":
check_authority($AUTH,"view");

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' );

# 判斷是否有輸入提單號碼
$bl_num = !empty($_GET['bl_num'])? $_GET['bl_num']: '' ;
$carrier_num = !empty($_GET['carrier_num'])? $_GET['carrier_num'] : '';

if( $Po_ship = $WAREHOUSING->get_po_ship( $bl_num , $carrier_num , 'user' ) ){
	$op['po_ship'] = $Po_ship;
	if( $Po_ship_det = $WAREHOUSING->get_stock_i( $bl_num , $carrier_num , $Po_ship['dist'] ) ){
		$op['po_ship_det'] = $Po_ship_det;
	}
    
	# 確認是否在盤點中
	$Dept = $Po_ship['dist'];
	$Item = $Po_ship_det[0]['mat_cat'] == 'l' ? 'lots' : 'acc' ;
	$ver = $WAREHOUSING->get_ver( $Dept , $Item );
    
	if( !empty($ver) && $ver['confirm_user'] == '' ){
		$_SESSION['MSG'][] = 'Inventory !!';
		header ("Location: ".$PHP_SELF."?PHP_action=incoming_material");
		exit;
	}
	$op['ver'] = $ver['ver'];
    
    #radio
	$op['del_key'] = array('y','n');
	$op['del_names'] = array('yes','no');
	
	if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
		$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
	}

	$utf_field_size = sizeof($utf_field['utf_field']);
	for( $i=0; $i<$utf_field_size; $i++){
		$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
	}

	page_display($op,$AUTH,$TPL_MATERIALS_RETREAT_ADJUSTMENT_SEARCH_INCOMING);
} else {
	$_SESSION['MSG'][] = 'No Data!!';
	header ("Location: ".$PHP_SELF."?PHP_action=returned_material_adjustment");
}
break;
#
#
#
#
#
#
# :returned_material_adjustment_search
case "returned_material_adjustment_search":
check_authority($AUTH,"view");

$rn_num = !empty($_GET['rn_num'])? $_GET['rn_num'] :'';
$ord_num = !empty($_GET['ord_num'])? $_GET['ord_num'] :'';

# 確認是否在盤點中
if(empty($ord_num)){
	$Dept = $WAREHOUSING->get_order_fty_by_notice($rn_num);
}else{
	$Dept = $WAREHOUSING->get_order_fty($ord_num);
}

$ver = $WAREHOUSING->get_ver( $Dept , "lots" );
if( !empty($ver) && $ver['confirm_user'] == '' ){
	$_SESSION['MSG'][] = 'Inventory !!';
	header ("Location: ".$PHP_SELF."?PHP_action=receiving_to_get_material");
	exit;
}
$ver = $WAREHOUSING->get_ver( $Dept , "acc" );
if( !empty($ver) && $ver['confirm_user'] == '' ){
	$_SESSION['MSG'][] = 'Inventory !!';
	header ("Location: ".$PHP_SELF."?PHP_action=receiving_to_get_material");
	exit;
}

$M_ord_num = !empty($_GET['ord_num'])? $_GET['ord_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['ord_num'])? $_SESSION[$AUTH][$PHP_action]['ord_num'] : '' );
$M_rn_num = !empty($_GET['rn_num'])? $_GET['rn_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['rn_num'])? $_SESSION[$AUTH][$PHP_action]['rn_num'] : '' );
$M_now_num = isset($_GET['M_now_num'])? $_GET['M_now_num'] : (isset($_SESSION[$AUTH][$PHP_action]['now_num'])? $_SESSION[$AUTH][$PHP_action]['now_num'] : '' );

$parm = array(
    'ord_num'       => $M_ord_num ,
    'rn_num'   => $M_rn_num , 
    'action'        => $PHP_action , 
    'now_num'       => $M_now_num , 
    'page_num'      => 20 , 
);

$op = $WAREHOUSING->notice_search4adjust( $parm );

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' );

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,$TPL_RETURN_SEND_MATERIAL_LIST);


break;
#
#
#
#
#
#
# :returned_material_adjustment_send_search
case "returned_material_adjustment_send_search":
check_authority($AUTH,"view");

# 確認是否在盤點中
$ver = $WAREHOUSING->get_ver( $_SESSION['SCACHE']['ADMIN']['dept'] == "SA" ? "LY" : $_SESSION['SCACHE']['ADMIN']['dept'] , "lots" );
if( !empty($ver) && $ver['confirm_user'] == '' ){
	$_SESSION['MSG'][] = 'Fabric Inventory !!';
	header ("Location: ".$PHP_SELF."?PHP_action=collar_to_get_material");
	exit;
}
$lots_ver = $ver['ver'];;

$ver = $WAREHOUSING->get_ver( $_SESSION['SCACHE']['ADMIN']['dept'] == "SA" ? "LY" : $_SESSION['SCACHE']['ADMIN']['dept'] , "acc" );
if( !empty($ver) && $ver['confirm_user'] == '' ){
	$_SESSION['MSG'][] = 'Acc Inventory !!';
	header ("Location: ".$PHP_SELF."?PHP_action=collar_to_get_material");
	exit;
}
$acc_ver = $ver['ver'];

$M_num = !empty($_GET['M_num'])? $_GET['M_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['M_num'])? $_SESSION[$AUTH][$PHP_action]['M_num'] : '' );
$M_now_num = isset($_GET['M_now_num'])? $_GET['M_now_num'] : (isset($_SESSION[$AUTH][$PHP_action]['now_num'])? $_SESSION[$AUTH][$PHP_action]['now_num'] : '' );

$parm = array(
    'rn_num'   		=> $M_num , 
    'action'        => $PHP_action , 
    'now_num'       => $M_now_num , 
    'page_num'      => 20 , 
);

if( $op = $WAREHOUSING->get_order_bom4adjust( $M_num , $ver['ver'] ) ){
	$op['color_html'] = get_bom_colorway_qty_html($op['s_order']['size_des']['size'],$op['wiqty']);
}

$op['ver']['lots'] = $lots_ver;
$op['ver']['acc'] = $acc_ver;
$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' );

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,$TPL_RECEIVING_TO_GET_MATERIAL_SEND_SEARCH);


break;
#
#
#
#
#
#
# :update_incoming_qty 
case "update_incoming_qty":
check_authority($AUTH,"edit");

if( $WAREHOUSING->update_incoming_qty($_POST) ) {
	$_SESSION['MSG'][] = $message = "Successfully Update incoming Qty!";
	$log->log_add(0,$AUTH."E",$message);
} else {
	$_SESSION['MSG'][] = "Error!! Update incoming Qty!";
}

header ("Location: ".$PHP_SELF."?PHP_action=returned_material_adjustment");
break;
#
#
#
#
#
#
# :append_return_send_qty 
case "append_return_send_qty":
check_authority($AUTH,"add");

if( $WAREHOUSING->append_retreat_qty('b',$_POST) ) {
	$_SESSION['MSG'][] = $message = "Successfully Append Retreat Qty, Order number : ". $_POST['order_num'];
	$log->log_add(0,$AUTH."B",$message);
} else {
	$_SESSION['MSG'][] = "Successfully Append Retreat Qty, Order number : ". $_POST['order_num'];
}
header ("Location: ".$PHP_SELF."?PHP_action=returned_material_adjustment");
break;
#
#
#
#
#
#
# :send_detail 
case "send_detail":
check_authority($AUTH,"view");

if( $op = $WAREHOUSING->get_send_detail($_GET['bl_num'], $_GET['l_no'], $_GET['r_no'], $_GET['mat_id'], $_GET['color'], $_GET['size']) ) {
	$_SESSION['MSG'][] = $message = "Successfully Append Retreat Qty, Order number : ". $_POST['order_num'];
	$log->log_add(0,$AUTH."B",$message);
}

page_display($op,$AUTH,"send_detail_show.html");
break;
#
#
#
#
#
#
} # CASE END
?>
