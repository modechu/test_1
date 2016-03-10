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
#
$AUTH = '082';
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
case "arrival_time":
check_authority($AUTH,"view");

$op['css'] = array( 'css/scm.css' , 'css/jquery-autocomplete.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery-autocomplete.js' , 'js/jquery.blockUI.js' );

$User_dept = $_SESSION['USER']['ADMIN']['dept'];

if( is_dept($User_dept) ){
	$op['dept'] = $User_dept;
} else {
	$op['dept'] = $User_dept = $_GET['PHP_dept'];
	$op['dept_select'] = get_dept_code("arrival_chg(this)");
}



$op['dept_html'] = $WAREHOUSING->get_carrier_list($User_dept ,'n','n');

// echo print_r($_SESSION['STOCK_USER']);
if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,'arrival_time.html');
break;
#
#
#
#
#
#
# :get_bl_num
case "get_bl_num":
check_authority($AUTH,"view");

header("Content-Type:text/html;charset=BIG5");

if( !empty($_GET['bl_num']) ) {
	if ( $Str = $WAREHOUSING->get_bl_num( $_GET['bl_num'] ,$_GET['arrival_active'],$_GET['incoming_active'] ) ) {
		echo 'ok@'.(implode(',',$Str));
	} else {
		echo 'off@nodata'; #.urlencode($_GET['bl_num']).iconv("big5","ucs-2",$Str);
	}
}

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
	if ( $Str = $WAREHOUSING->get_carrier_num( $_GET['carrier_num'],$_GET['arrival_active'],$_GET['incoming_active'] ) ) {
		echo 'ok@'.(implode(',',$Str));
	} else {
		echo 'off@nodata'; #.urlencode($_GET['carrier_num']).iconv("big5","ucs-2",$_GET['carrier_num'])
	}
}

break;
#
#
#
#
#
#
# :WAREHOUSING_search 
case "arrival_time_search":
check_authority($AUTH,"view");

$op['css'] = array( 'css/scm.css' , 'css/po_ship.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js'  );

# 判斷是否有輸入提單號碼
$bl_num = !empty($_GET['bl_num'])? $_GET['bl_num']: '' ;
$carrier_num = !empty($_GET['carrier_num'])? $_GET['carrier_num'] : '';

$op['bl_num'] = $bl_num;
$op['carrier_num'] = $carrier_num;
if( $Po_ship = $WAREHOUSING->get_po_ship( $bl_num , $carrier_num ) ){
    $Po_ship['arrival_user'] = big52uni(get_user_html($Po_ship['arrival_user']));
	$op['po_ship'] = $Po_ship;
	// print_r($Po_ship).'<br>';
	if( $po_ship_det =$WAREHOUSING->get_po_ship_det($Po_ship['id']) ){
		$op['po_ship_det'] = $po_ship_det;
	}
    
    #判斷是否已經有進料
    $op['inventory_i'] = $WAREHOUSING->chk_inventory_i( $Po_ship['bl_num'] );
}
// print_r($po_ship_det).'<br>';
if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

page_display($op,$AUTH,'arrival_time_search.html');
break;
#
#
#
#
#
#
# :append_arrival
case "append_arrival":
check_authority($AUTH,"add");

if( !empty($_GET['ship_id']) ) {
	if ( $Str = $WAREHOUSING->append_arrival( $_GET['ship_id'] ) ) {
		$message = "Successfully Append Arrival, carrier number : ". $_GET['carrier_num'];
		$log->log_add(0,$AUTH."A",$message);
		echo 'ok@'.$message;
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
# :reply_arrival
case "reply_arrival":
check_authority($AUTH,"edit");

if( !empty($_GET['ship_id']) ) {
	
	if ( $Str = $WAREHOUSING->reply_arrival( $_GET['ship_id'] ) ) {
		$message = "Successfully Reply Arrival, carrier number : ". $_GET['carrier_num'];
		$log->log_add(0,$AUTH."E",$message);
		echo 'ok@'.$message;
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
} # CASE END
?>