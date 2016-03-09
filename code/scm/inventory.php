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
$TPL_INVENTORY = 'inventory.html';
$TPL_INVENTORY_STOCK = 'inventory_stock.html';
$TPL_INVENTORY_SEARCH = 'inventory_search.html';
#
#
#
$AUTH = '087';
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
case "inventory":
check_authority($AUTH,"view");
$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' );

$User_dept = $_SESSION['USER']['ADMIN']['dept'];
$Dept = !empty($_GET['PHP_dept'])? $_GET['PHP_dept'] :'';
$Item = !empty($_GET['item'])? $_GET['item'] :'';
$Order= !empty($_GET['PHP_order'])? $_GET['PHP_order'] :'';

//echo $Dept;

if( is_dept($User_dept) ){
	$op['dept'] = $User_dept;
	$op['item'] = $Item;
	$op['order'] = $Order;
	if ( $Str = $WAREHOUSING->chk_ver($Dept,substr($Item,0,1)) ) {
		$op[$Item] = $Str;
	}
} else {
	
	$op['dept'] = $Dept;
	$op['item'] = $Item;
	$op['order'] = $Order;
	$op['dept_select'] = get_dept_code("chk_inventory(this)");
	
	if ( $Str = $WAREHOUSING->chk_ver($Dept,substr($Item,0,1)) ) {
		$op[$Item] = $Str;
	}
}
//echo $Dept.";".$Item;

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

//echo $op[$Item][sizeof($op[$Item]) - 1 ]['submit_user'] ; 
if ($op[$Item][sizeof($op[$Item]) - 1 ]['submit_user'] != '')
{
	$op['finish_button_show'] = 1;
}
//print_r($GLOBALS);

page_display($op,$AUTH,$TPL_INVENTORY);
break;
#
#
#
#
#
#
# :append_inventory 
case "append_inventory":
check_authority($AUTH,"add");

$Dept = !empty($_GET['PHP_dept'])? $_GET['PHP_dept'] :'';
$Item = !empty($_GET['item'])? $_GET['item'] :'';

//echo $Dept;

if ( $Ver = $WAREHOUSING->get_ver($Dept,$Item,1) ) {
	$Ver = $Ver['ver'];
	if( $mat = $WAREHOUSING->append_inventory( $Dept , $Item , $Ver ) ) {
		$_SESSION['MSG'][] = $message = "Successfully Append Inventory Records! [".$Dept."/".$Item."/".$Ver."]";
		$log->log_add(0,$AUTH."A",$message);
	} else {
		$_SESSION['MSG'][] = "Error ! Can't find Append records in these rule! [".$Dept."/".$Item."/".$Ver."]";
	}
}
// exit;
header ("Location: ".$PHP_SELF."?PHP_action=inventory&PHP_dept=".$Dept."&item=".$Item);
break;
#
#
#
#
#
#
# :inventory_edit 
case "inventory_edit":
check_authority($AUTH,"edit");

$Dept = !empty($_GET['dept'])? $_GET['dept'] :'';
$Item = !empty($_GET['item'])? $_GET['item'] :'';
$Ver = !empty($_GET['ver'])? $_GET['ver'] :'';
$Text = !empty($_GET['text'])? $_GET['text'] :'';

$Text = urldecode(uniDecode($Text,'big-5')); //轉碼

if ( $WAREHOUSING->edit_remark_text($Dept,$Item,$Ver,$Text) ) {
	$_SESSION['MSG'][] = $message = "Successfully Save Remark Records! ";
	
	$log->log_add(0,$AUTH."E",$message);
} else {
	$_SESSION['MSG'][] = "Error ! Can't Save Remark records !";
}

header ("Location: ".$PHP_SELF."?PHP_action=inventory&PHP_dept=".$Dept."&item=".$Item);
break;
#
#
#
#
#
#
# :inventory_stock 
case "inventory_stock":
check_authority($AUTH,"view");

$Dept = !empty($_GET['dept'])? $_GET['dept'] :'';
$Item = !empty($_GET['item'])? $_GET['item'] :'';
$Order = !empty($_GET['order'])? $_GET['order'] :'';
$finish = !empty($_GET['PHP_finish'])? $_GET['PHP_finish'] :0;

# 判斷是否有版本
$ver = $WAREHOUSING->get_ver($Dept,$Item);
$Ver = ( $ver['ver'] == '0001' && $ver['confirm_user'] == '' ) ? '' : str_pad( ( $ver['ver'] - 1 ) , 4, 0, STR_PAD_LEFT) ;

if( $mat = $WAREHOUSING->get_inventory_stock( $Dept , $Item , $Ver , $Order) ) {
	$op = $mat;
}

$op['submit_user'] = $ver['submit_user'];
$op['confirm_user'] = $ver['confirm_user'];
$op['finish'] = $finish;
$op['fty'] = $Dept;
$op['ver'] = $Ver;
$op['item'] = $Item;
$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/inventory.js' , 'js/jquery.blockUI.js' );

if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
	$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
}

$utf_field_size = sizeof($utf_field['utf_field']);
for( $i=0; $i<$utf_field_size; $i++){
	$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
}

if($op['finish'] == 1 )
{
	check_authority($AUTH,"edit");

	foreach($op[$Item] as $index => $val){
		$id=$val['id'];
		if($val['checked']){
			$WAREHOUSING->update_stock_mat_cfm_qty($Item,$id,$val['sub_qty']);
		}else{
			$WAREHOUSING->update_stock_mat_cfm_qty($Item,$id,$val['qty']);
		}
	} 
	
	$ver = $WAREHOUSING->get_ver($Dept,$Item);
	
	$WAREHOUSING->append_inventory_mat($Ver,$ver['ver'],$Dept,$Item);
	
	if( $WAREHOUSING->update_stock_confirm($ver['ver'],$Dept,$Item) ) {
		$_SESSION['MSG'][] = $message = "Successfully confirm count qty ";
	}


	header ("Location: ".$PHP_SELF."?PHP_action=inventory&PHP_dept=".$Dept."&item=".$Item);
}
else
{
	page_display($op,$AUTH,$TPL_INVENTORY_STOCK);
}
break;
#
#
#
#
#
#
# :update_stock_mat_sub_qty 
case "update_stock_mat_sub_qty":
check_authority($AUTH,"edit");

$Dept = !empty($_POST['PHP_dept'])? $_POST['PHP_dept'] :'';
$Item = !empty($_POST['item'])? $_POST['item'] :'';
$Ver = !empty($_POST['ver'])? $_POST['ver'] :'';

foreach($_POST['count_qty'] as $id => $val){
	if($val != ''){
		if ( $WAREHOUSING->update_stock_mat_sub_qty($Item,$id,$val) ) {
			$message = "Successfully input count qty ( Ver = ".$Ver." , id = ".$id." , qty = ".$val." )";
			$log->log_add(0,$AUTH."C_SUB".$Ver,$message);
		}
	}

	/* if( $val > 0 ){
		if ( $WAREHOUSING->update_stock_mat_sub_qty($Item,$id,$val) ) {
			$message = "Successfully input count qty ( Ver = ".$Ver." , id = ".$id." , qty = ".$val." )";
			$log->log_add(0,$AUTH."C_SUB".$Ver,$message);
		}
	} */
} 
//exit;

$ver = $WAREHOUSING->get_ver($Dept,$Item);
$Ver = $ver['ver'];

/* echo $Ver;
exit;   */
if( $WAREHOUSING->update_stock_submit($Ver,$Dept,$Item) ) {
	$_SESSION['MSG'][] = $message = "Successfully input count qty ";
}



header ("Location: ".$PHP_SELF."?PHP_action=inventory&PHP_dept=".$Dept."&item=".$Item);
break;
#
#
#
#
#
#
# :update_stock_mat_cfm_qty 
case "update_stock_mat_cfm_qty":
check_authority($AUTH,"edit");

$Dept = !empty($_POST['PHP_dept'])? $_POST['PHP_dept'] :'';
$Item = !empty($_POST['item'])? $_POST['item'] :'';
$Ver = !empty($_POST['ver'])? $_POST['ver'] :'';

foreach($_POST['count_qty'] as $id => $val){
	if( $val > 0 ){
		if ( $WAREHOUSING->update_stock_mat_cfm_qty($Item,$id,$val) ) {
			$message = "Successfully confirm count qty ( Ver = ".$Ver." , id = ".$id." , qty = ".$val." )";
			$log->log_add(0,$AUTH."C_CFM".$Ver,$message);
		}
	}
}  

$ver = $WAREHOUSING->get_ver($Dept,$Item);

if( $WAREHOUSING->append_inventory_mat($Ver,$ver['ver'],$Dept,$Item) ) {
	// $_SESSION['MSG'][] = $message = "Successfully confirm count qty ";
} 

if( $WAREHOUSING->update_stock_confirm($ver['ver'],$Dept,$Item) ) {
	$_SESSION['MSG'][] = $message = "Successfully confirm count qty ";
}


header ("Location: ".$PHP_SELF."?PHP_action=inventory&PHP_dept=".$Dept."&item=".$Item);
break;
#
#
#
#
#
#
} # CASE END
?>
