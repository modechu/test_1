<?php
session_start();

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/bom_po_apb.class.php");

$BOM_PO_APB = new BOM_PO_APB();
if (!$BOM_PO_APB->init($mysql,"log")) { print "error!! cannot initialize database for BOM_PO_APB class"; exit; }

$PHP_SELF = $_SERVER['PHP_SELF'];

$op = array();

$PHP_action = !empty($PHP_action) ? $PHP_action : '';

$auth = '093';

switch ($PHP_action) {
//=======================================================


case "bom_apb_search":
check_authority($auth,"view");

	$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
	$op['year_1'] = $arry2->select($YEAR_WORK,'','PHP_year1','select','');
	$op['year_2'] = $arry2->select($YEAR_WORK,'','PHP_year2','select','');
	$op['year_3'] = $arry2->select($YEAR_WORK,'','PHP_year3','select','');
	$op['month_1'] = $arry2->select($MONTH_WORK,'01','PHP_month1','select','');
	$op['month_2'] = $arry2->select($MONTH_WORK,'','PHP_month2','select','');
	$op['msg'] = $order->msg->get(2);
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
	
page_display($op, $auth, "bom_apb_search.html");    	    
break;



case "do_bom_apb_search":
check_authority($auth,"view");

	if($PHP_year1 == $PHP_year2){ $PHP_year2 =''; }
	if($PHP_year1 == $PHP_year3){ $PHP_year3 =''; }
	if($PHP_year2 == $PHP_year3){ $PHP_year3 =''; }
	
	if (!$PHP_fty){
		$PHP_fty = "LY";
	}
	if (!$PHP_year1){
		$PHP_year1 = "2013";
	}
	if (!$PHP_month1){
		$PHP_month1 = "01";
	}
	if (!$PHP_month2){
		$PHP_month2 = "12";
	}
	if ($PHP_year1){
		$op = $BOM_PO_APB->get_bom_apb($PHP_fty, $PHP_year1, $PHP_month1, $PHP_month2);
	}
	
	$op['fty'] = $PHP_fty;
	$op['year1'] = $PHP_year1;
	$op['year2'] = $PHP_year2;
	$op['year3'] = $PHP_year3;
	$op['month1'] = $PHP_month1;
	$op['month2'] = $PHP_month2;
page_display($op, $auth, "bom_po_apb_view.html");    	    
break;



case "do_bom_apb_qty_search":
check_authority($auth,"view");

	if($PHP_year1 == $PHP_year2){ $PHP_year2 =''; }
	if($PHP_year1 == $PHP_year3){ $PHP_year3 =''; }
	if($PHP_year2 == $PHP_year3){ $PHP_year3 =''; }
	
	if (!$PHP_fty){
		$PHP_fty = "LY";
	}
	if (!$PHP_year1){
		$PHP_year1 = "2014";
	}
	if (!$PHP_month1){
		$PHP_month1 = "01";
	}
	if (!$PHP_month2){
		$PHP_month2 = "12";
	}
	if ($PHP_year1){
        $str =  
        '#Order'."\t".
        'ETD'."\t".
        'Cust.'."\t".
        'Style'."\t".
        '#PO'."\t".
        'Color'."\t".
        'BOM Q\'ty'."\t".
        'BOM Unit'."\t".
        'PO. Q\'ty'."\t".
        'PO. Unit'."\t".
        'Mark'."\t".
        'PO. Prics'."\t".
        'PO. Amount'."\t".
        'PO. Currency'."\t".
        'SHIP. Q\'ty'."\t".
        'RCV. Q\'ty'."\t".
        'APB Q\'ty'."\t".
        'APB. Amount'."\t".
        'APB. Currency'."\n";
		$str .= $BOM_PO_APB->get_bom_apb_qty($PHP_fty, $PHP_year1, $PHP_month1, $PHP_month2);
	}
    // echo 
	$file = fopen("apb.txt","w"); 
	fputs($file,$str);
	fclose($file);
	$op['fty'] = $PHP_fty;
	$op['year1'] = $PHP_year1;
	$op['year2'] = $PHP_year2;
	$op['year3'] = $PHP_year3;
	$op['month1'] = $PHP_month1;
	$op['month2'] = $PHP_month2;
page_display($op, $auth, "bom_po_apb_view.html"); 	    
break;


}   // end case ---------
?>
