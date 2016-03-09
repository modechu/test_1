<?php
#
#
#
// session_start();
// echo $PHP_action.'<br>';
#
#
#
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/class.ie_data.php");
// phpinfo();
// $server = '192.168.2.3';
// $database = 'NewWIPOne';
// $user = 'mode';
// $password = '6699';
$connection_string = 'DRIVER={SQL Server};SERVER=192.168.2.3;DATABASE=NewWIPOne'; 
$conn = odbc_connect($connection_string, 'mode', '6699');
if($conn)
{
	$sql = "SELECT * FROM dbo.Users ;";
	$result = odbc_exec($conn, $sql);
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
          print_r($row);
        }
	}
	else
	{
		echo iconv("big5","utf-8",'執行SQL失敗');
	}	
}
else
{
	echo iconv("big5","utf-8",'連線失敗');
}

// exit;
$IE_DATA = new IE_DATA();
if (!$IE_DATA->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '097';
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
case "main":
check_authority($AUTH,"view");

$op['css'] = array( 'css/scm.css' , 'css/jquery-autocomplete.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery-autocomplete.js' , 'js/jquery.blockUI.js' );

page_display($op,$AUTH,'ie_class_main.html');
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
	if ( $Str = $WAREHOUSING->get_bl_num( $_GET['bl_num'] ) ) {
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
	if ( $Str = $WAREHOUSING->get_carrier_num( $_GET['carrier_num'] ) ) {
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

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' );

# 判斷是否有輸入提單號碼
$bl_num = !empty($_GET['bl_num'])? $_GET['bl_num']: '' ;
$carrier_num = !empty($_GET['carrier_num'])? $_GET['carrier_num'] : '';

$op['bl_num'] = $bl_num;
$op['carrier_num'] = $carrier_num;
if( $Po_ship = $WAREHOUSING->get_po_ship( $bl_num , $carrier_num ) ){
	$op['po_ship'] = $Po_ship;
	// print_r($Po_ship).'<br>';
	if( $po_ship_det =$WAREHOUSING->get_po_ship_det($Po_ship['id']) ){
		$op['po_ship_det'] = $po_ship_det;
	}
    
    #判斷是否已經有進料
    $op['inventory_i'] = ( $chk_inventory_i = $WAREHOUSING->chk_inventory_i( $Po_ship['id'] ) ) ?  'y' : 'n' ;
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