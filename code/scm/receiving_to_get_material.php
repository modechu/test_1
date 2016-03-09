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
require_once($config['root_dir']."/lib/class.monitor.php");
$monitor = new MONITOR();
$monitor->init($mysql);
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
# Template
$TPL_RECEIVING_TO_GET_MATERIAL = 'receiving_to_get_material.html';
$TPL_RECEIVING_TO_GET_MATERIAL_LIST = 'receiving_to_get_material_list.html';
$TPL_RECEIVING_TO_GET_MATERIAL_SEARCH = 'receiving_to_get_material_search.html';
#
#
#
$AUTH = '084';
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
case "receiving_to_get_material":
check_authority($AUTH,"view");

$op['css'] = array( 'css/scm.css' , 'css/jquery-autocomplete.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery-autocomplete.js' , 'js/jquery.blockUI.js' );

if($_GET['show_un_send_list']){
	$op['show_un_send_list'] = $_GET['show_un_send_list'];
    # 抓出已設定領、發料數量，並判斷是否已領、發料。
	// $op['send_num'] = $WAREHOUSING->get_send_num("LY");
    // print_r($op['send_num']);
	// $send_size = sizeof($op['send_num']);
	// for($i=0;$i<$send_size;$i++){
		// if(!$WAREHOUSING->check_send_num_is_sended($op['send_num'][$i]['id'])){
			// $op['un_send'][] = $op['send_num'][$i];
		// }
	// }
    
}

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,$TPL_RECEIVING_TO_GET_MATERIAL);
#
#
#
#
#
#
# :get_order_num
case "get_order_num":
check_authority($AUTH,"view");

header("Content-Type:text/html;charset=BIG5");

if( !empty($_GET['order_num']) ) {
	if ( $Str = $WAREHOUSING->get_order_num( $_GET['order_num'] ) ) {
		echo 'ok@'.(implode(',',$Str));
	} else {
		echo 'off@nodata';
	}
}

break;
#
#
#
#
#
#
# :receiving_to_get_material_search 
case "receiving_to_get_material_search":
check_authority($AUTH,"view");

# 判斷是否有輸入提單號碼
$Order_num = !empty($_GET['order_num'])? $_GET['order_num'] :'';
$Notice_num = !empty($_GET['notice_num'])? $_GET['notice_num'] :'';

# 確認是否在盤點中
if(empty($Order_num)){
	$Dept = $WAREHOUSING->get_order_fty_by_notice($Notice_num);
}else{
	$Dept = $WAREHOUSING->get_order_fty($Order_num);
}

$ver = $WAREHOUSING->get_ver( $Dept , "l" );
if(empty($ver)){
	$ver = $WAREHOUSING->get_ver( $Dept , "a" );
}

if( !empty($ver) && $ver['confirm_user'] == '' ){
	$_SESSION['MSG'][] = 'Inventory !';
	header ("Location: ".$PHP_SELF."?PHP_action=receiving_to_get_material");
	exit;
}

$M_ord_num = !empty($_GET['order_num'])? $_GET['order_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['order_num'])? $_SESSION[$AUTH][$PHP_action]['order_num'] : '' );
$M_notice_num = !empty($_GET['notice_num'])? $_GET['notice_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['notice_num'])? $_SESSION[$AUTH][$PHP_action]['notice_num'] : '' );
$M_now_num = isset($_GET['M_now_num'])? $_GET['M_now_num'] : (isset($_SESSION[$AUTH][$PHP_action]['now_num'])? $_SESSION[$AUTH][$PHP_action]['now_num'] : '' );
$M_mat = isset($_GET['mat'])? $_GET['mat'] : (isset($_SESSION[$AUTH][$PHP_action]['mat'])? $_SESSION[$AUTH][$PHP_action]['mat'] : '' );

$parm = array(
    'ord_num'       => $M_ord_num ,
    'notice_num'    => str_replace('RN','',$M_notice_num) , 
    'action'        => $PHP_action , 
    'now_num'       => $M_now_num , 
    'mat'           => $M_mat , 
    'page_num'      => 20 , 
);

$op = $WAREHOUSING->notice_search( $parm );
foreach($op['notice'] as $key => $val){
    $op['notice'][$key]['create_user'] = big52uni(get_user_html($val['create_user']));
    $op['notice'][$key]['line'] = $monitor->get_line_name($val['line']);
    $op['notice'][$key]['status_txt'] = get_material_status($val['status']);
}

$op['mat_cat'] = $M_mat;
$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' );

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,$TPL_RECEIVING_TO_GET_MATERIAL_LIST);
break;
#
#
#
#
#
#
# :receiving_to_get_material_view 
case "receiving_to_get_material_view":
check_authority($AUTH,"view");

$M_num = !empty($_GET['M_num'])? $_GET['M_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['M_num'])? $_SESSION[$AUTH][$PHP_action]['M_num'] : '' );
// $M_notice_num = !empty($_GET['M_notice_num'])? $_GET['M_notice_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['notice_num'])? $_SESSION[$AUTH][$PHP_action]['notice_num'] : '' );
// $M_now_num = isset($_GET['M_now_num'])? $_GET['M_now_num'] : (isset($_SESSION[$AUTH][$PHP_action]['now_num'])? $_SESSION[$AUTH][$PHP_action]['now_num'] : '' );
$M_mat = isset($_GET['M_mat'])? $_GET['M_mat'] : (isset($_SESSION[$AUTH][$PHP_action]['mat'])? $_SESSION[$AUTH][$PHP_action]['mat'] : '' );

// $parm = array(
    // 'ord_num'       => $M_ord_num ,
    // 'notice_num'    => $M_notice_num , 
    // 'action'        => $PHP_action , 
    // 'now_num'       => $M_now_num , 
    // 'mat'           => $M_mat , 
    // 'page_num'      => 20 , 
// );

if( $op = $WAREHOUSING->get_order_bom( $M_num , $M_mat ) ){
	// $op['color_html'] = get_bom_colorway_qty_html($op['s_order']['size_des']['size'],$op['wiqty']);
    // print_r($op['color_html']);
}

$op['order_num'] = $op['s_order']['order_num'];
$op['mat_cat'] = $M_mat;
$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' , 'js/open_detial.js');

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,$TPL_RECEIVING_TO_GET_MATERIAL_SEARCH);
break;
#
#
#
#
#
#
# :append_receiving_qty 
case "append_receiving_qty":
check_authority($AUTH,"add");

if( $WAREHOUSING->append_receiving_qty('r',$_POST) ) {
	$_SESSION['MSG'][] = $message = "Successfully Append Collar Qty, Order number : ". $_POST['order_num'];
	$log->log_add(0,$AUTH."R",$message);
} else {
	$_SESSION['MSG'][] = "Successfully Append Collar Qty, Order number : ". $_POST['order_num'];
}

header ("Location: ".$PHP_SELF."?PHP_action=receiving_to_get_material");
break;
#
#
#
#
#
#
# :append_send_qty
case "append_send_qty":
check_authority($AUTH,"add");

if( $WAREHOUSING->append_send_qty('r',$_POST) ) {
    $_SESSION['MSG'][] = $message = "Successfully Append Collar Qty, Order number : ". $_POST['order_num'];
    $log->log_add(0,$AUTH."R",$message);
} else {
    $_SESSION['MSG'][] = "Successfully Append Collar Qty, Order number : ". $_POST['order_num'];
}

header ("Location: ".$PHP_SELF."?PHP_action=receiving_to_get_material");
break;
#
#
#
#
#
} # CASE END
?>
