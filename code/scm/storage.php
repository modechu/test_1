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
$STORAGE = array("End Line", "Packing", "Final");
#
#
# Template
$TPL_STORAGE = 'storage.html';
$TPL_STORAGE_ADD = 'storage_add.html';
$TPL_STORAGE_SEARCH = "storage_search.html";
$TPL_STORAGE_TRANSFER_OUT = "storage_transfer_out.html";
$TPL_STORAGE_TRANSFER_IN = "storage_transfer_in.html";
$TPL_STORAGE_TRANSFER = "storage_transfer.html";
$TPL_STORAGE_TRANSFER_SEARCH = "storage_transfer_search.html";
$TPL_STORAGE_TRANSFER_VIEW = "storage_transfer_view.html";
#
#
#
$AUTH = '087';
$TR_AUTH = '110';
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
	
	if(in_array($_SESSION['SCACHE']['ADMIN']['dept'], $FACTORY)){
		$fty = $_SESSION['SCACHE']['ADMIN']['dept'];
		$select_fty = array($_SESSION['SCACHE']['ADMIN']['dept']);
		$op['fty_select'] = $arry2->select($select_fty, $_SESSION['SCACHE']['ADMIN']['dept'], "PHP_factory_to", "styled-select","");
	}else{
		$fty = "LY";
		$op['fty_select_append'] = $arry2->select($FACTORY, $fty, "PHP_factory_to", "styled-select","get_full_line(this.value,'append_storage')");
		$op['fty_select_search'] = $arry2->select($FACTORY, $fty, "PHP_factory_to", "styled-select","get_full_line(this.value,'search_storage')");
	}
    
    $line = $WAREHOUSING->get_fty_line($fty);
    $line_name = $line_id = array();
    foreach($line as $key => $val){
        $line_name[] = $val['line'];
    }
    
	$op['line_select_append'] = $arry2->select($line_name, "", "PHP_line_to", "styled-select","get_full_zone(this.value,'append_storage')");
	$op['line_select_search'] = $arry2->select($line_name, "", "PHP_line_to", "styled-select","get_full_zone(this.value,'search_storage')");
	
	$zone = $WAREHOUSING->get_storage_zone($fty);
	$zone_id = $zone_name = array();
	foreach($zone as $k => $val){
		$zone_id[] = $val['id'];
		$zone_name[] = $val['zone'];
	}
	$op['zone_select'] = $arry2->select($zone_name, "", "PHP_zone_to", "styled-select", "", "");
    $op['today'] = $TODAY;

	$op['css'] = array( 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
	$op['js'] = array( 'js/jquery.min.js' , 'js/storage.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' );
	
	page_display($op,$AUTH,$TPL_STORAGE);
break;
#
#
#
#
#
#
# :append_storage_qty 
case "append_storage_qty":
	check_authority($AUTH,"add");

	$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
	$fty = $_POST['PHP_factory_to'] ? $_POST['PHP_factory_to'] : $_GET['PHP_factory_to'];
	$line = $_POST['PHP_line_to'] ? $_POST['PHP_line_to'] : $_GET['PHP_line_to'];
	$zone = $_POST['PHP_zone_to'] ? $_POST['PHP_zone_to'] : $_GET['PHP_zone_to'];
	
	$op = $WAREHOUSING->get_size_breakdown($ord_num);
	
	# 羆 Grand Total计秖
	foreach($op['partial'] as $p_key => $p_val){
		for($i=0;$i<sizeof($p_val['wiqty']);$i++){
			$ttl_color_qty = 0;
			$op['partial'][$p_key]['wiqty'][$i]['qtys'] = array();
			foreach($op['size_des'] as $s_key => $s_val){
				$qty = $WAREHOUSING->get_size_breakdown_qty("'i'", $fty, $line, $zone, $ord_num, $p_val['mks'], $op['partial'][$p_key]['wiqty'][$i]['colorway'], $s_val);
				$op['partial'][$p_key]['wiqty'][$i]['qtys'][] = $qty;
				$ttl_color_qty += $qty;
			}
			$op['partial'][$p_key]['wiqty'][$i]['ttl_color_qty'] = $ttl_color_qty;
		}
	}

	# 块计秖
	$op['storage_det'] = $WAREHOUSING->get_storage_det("'i'", $fty, $line, $zone, $ord_num ,$TODAY);
	
	$op['ord_num'] = $ord_num;
	$op['fty_to'] = $fty;
	$op['zone_to'] = $zone;
	$op['line_to'] = $line;
	$op['today'] = $TODAY;
	
	$op['css'] = array( 'css/scm.css' );
	$op['js'] = array( 'js/jquery.min.js' , 'js/storage.js' , 'js/jquery.blockUI.js' );

	page_display($op,$AUTH,$TPL_STORAGE_ADD);
break;
#
#
#
#
#
#
# :search_storage 
case "search_storage":
	check_authority($AUTH,"add");
	
	$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
	$fty = $_POST['PHP_factory_to'] ? $_POST['PHP_factory_to'] : $_GET['PHP_factory_to'];
	$zone = $_POST['PHP_zone_to'] ? $_POST['PHP_zone_to'] : $_GET['PHP_zone_to'];
	$line = $_POST['PHP_line_to'] ? $_POST['PHP_line_to'] : $_GET['PHP_line_to'];
	$pt_date = $_POST['PHP_pt_date'] ? $_POST['PHP_pt_date'] : $_GET['PHP_pt_date'];
	// $zone_id = $WAREHOUSING->get_zone_id($fty, $zone);

	$op = $WAREHOUSING->get_size_breakdown($ord_num);
	
	# 羆 Grand Total计秖
	foreach($op['partial'] as $p_key => $p_val){
		for($i=0;$i<sizeof($p_val['wiqty']);$i++){
			$ttl_color_qty = 0;
			$op['partial'][$p_key]['wiqty'][$i]['qtys'] = array();
			foreach($op['size_des'] as $s_key => $s_val){
				$qty = $WAREHOUSING->get_size_breakdown_qty("'i'", $fty, $line, $zone, $ord_num, $p_val['mks'], $op['partial'][$p_key]['wiqty'][$i]['colorway'], $s_val);
				$op['partial'][$p_key]['wiqty'][$i]['qtys'][] = $qty;
				$ttl_color_qty += $qty;
			}
			$op['partial'][$p_key]['wiqty'][$i]['ttl_color_qty'] = $ttl_color_qty;
		}
	}

	# 块计秖
	$op['storage_det'] = $WAREHOUSING->get_storage_det("'i'",$fty, $line, $zone, $ord_num , $pt_date );
	
	$op['ord_num'] = $ord_num;
	$op['fty'] = $fty;
	$op['zone'] = $zone;
	$op['line'] = $line;
	$op['today'] = $pt_date;
	
	$op['css'] = array( 'css/scm.css' );
	$op['js'] = array( 'js/jquery.min.js' , 'js/storage.js' , 'js/jquery.blockUI.js' );

	page_display($op,$AUTH,$TPL_STORAGE_SEARCH);
break;
#
#
#
#
#
#
# :do_append_storage_qty 
case "do_append_storage_qty":
	check_authority($AUTH,"add");

	if ( $WAREHOUSING->append_storage('i', $_POST) ) {
		$_SESSION['MSG'][] = $message = "Successfully Append Records! ";
		
		$log->log_add(0,$AUTH."E",$message);
	} else {
		$_SESSION['MSG'][] = "Error ! Can't Append records!";
	}

	header("Location: ".$PHP_SELF."?PHP_action=append_storage_qty&PHP_ord_num=".$_POST['PHP_ord_num']."&PHP_line_to=".$_POST['PHP_line_to']."&PHP_zone_to=".$_POST['PHP_zone_to']."&PHP_factory_to=".$_POST['PHP_factory_to']);
break;
#
#
#
#
#
#
case "delete_storage_qty":
	check_authority($AUTH,"del");

	if ( $WAREHOUSING->delete_storage_qty('y', $_GET['det_id']) ) {
		$_SESSION['MSG'][] = "Successfully Delete record! ";
		$log->log_add(0,$AUTH."D", "delete storage_det id = ".$_GET['det_id']);
	} else {
		$_SESSION['MSG'][] = "Error ! Can't Delete record!";
	}
    
    if ( $_GET['PHP_status'] == 'append' ) {
        header("Location: ".$PHP_SELF."?PHP_action=append_storage_qty&PHP_ord_num=".$_GET['PHP_ord_num']."&PHP_line=".$_GET['PHP_line']."&PHP_zone=".$_GET['PHP_zone']."&PHP_factory=".$_GET['PHP_facroty']."&PHP_pt_date=".$_GET['PHP_pt_date']);
    } else {
        header("Location: ".$PHP_SELF."?PHP_action=search_storage&PHP_ord_num=".$_GET['PHP_ord_num']."&PHP_line=".$_GET['PHP_line']."&PHP_zone=".$_GET['PHP_zone']."&PHP_factory=".$_GET['PHP_facroty']."&PHP_pt_date=".$_GET['PHP_pt_date']);
    }
break;
#
#
#
#
#
case "delete_transfer_qty":
	check_authority($AUTH,"del");
	
	if ( $WAREHOUSING->delete_storage_qty('y', $_GET['det_id']) ) {
		$_SESSION['MSG'][] = $message = "Successfully Delete record! ";
		$log->log_add(0,$AUTH."D",$message);
	} else {
		$_SESSION['MSG'][] = "Error ! Can't Delete record!";
	}
	
	header("Location: ".$PHP_SELF."?PHP_action=transfer_qty&PHP_factory=".$_GET['PHP_factory']."&PHP_factory_to=".$_GET['PHP_factory_to']."&PHP_ord_num=".$_GET['PHP_ord_num']."&PHP_line=".$_GET['PHP_line']."&PHP_line_to=".$_GET['PHP_line_to']."&PHP_zone=".$_GET['PHP_zone']."&PHP_zone_to=".$_GET['PHP_zone_to']."&PHP_in_out=".$_GET['PHP_in_out']);
break;
#
#
#
#
#
#
case "do_append_transfer_qty":
	check_authority($AUTH,"add");
	
	if($_POST['storage_id'] = get_insert_id('storage')){
		if ( $WAREHOUSING->append_storage('t', $_POST, true) ) {
			$_SESSION['MSG'][] = $message = "Successfully Append Records! ";
			
			$log->log_add(0,$AUTH."E",$message);
		} else {
			$_SESSION['MSG'][] = "Error ! Can't Append records!";
		}
	}
	
	
	header("Location: ".$PHP_SELF."?PHP_action=transfer_qty&PHP_in_out=".$_POST['PHP_in_out']."&PHP_factory=".$_POST['PHP_factory']."&PHP_factory_to=".$_POST['PHP_factory_to']."&PHP_ord_num=".$_POST['PHP_ord_num']."&PHP_line=".$_POST['PHP_line']."&PHP_line_to=".$_POST['PHP_line_to']."&PHP_zone=".$_POST['PHP_zone']."&PHP_zone_to=".$_POST['PHP_zone_to']);
break;
#
#
#
# ############################################################################################################################
#
#
# transfer Θ珇-锣
case "transfer":
	check_authority($TR_AUTH,"view");
	
	if(in_array($_SESSION['SCACHE']['ADMIN']['dept'], $FACTORY)){
		$fty = $_SESSION['SCACHE']['ADMIN']['dept'];
		$select_fty = array($_SESSION['SCACHE']['ADMIN']['dept']);
		$op['fty_select'] = $arry2->select($select_fty, $_SESSION['SCACHE']['ADMIN']['dept'], "PHP_factory", "styled-select","get_fty_line(this.value,'line','from')");
		$op['fty_to_select'] = $arry2->select($select_fty, $_SESSION['SCACHE']['ADMIN']['dept'], "PHP_factory_to", "styled-select","get_fty_line(this.value,'line_to','to')");
		$line = $WAREHOUSING->get_fty_line($_SESSION['SCACHE']['ADMIN']['dept']);
	}else{
		$fty = "LY";
		$op['fty_select'] = $arry2->select($FACTORY, $fty, "PHP_factory", "styled-select", "get_fty_line(this.value,'line','from')");
		$op['fty_to_select'] = $arry2->select($FACTORY, $fty, "PHP_factory_to", "styled-select", "get_fty_line(this.value,'line_to','to')");
		$line = $WAREHOUSING->get_fty_line($fty);
		$line_name = $line_id = array();
		foreach($line as $key => $val){
			$line_name[] = $val['line'];
			//$line_id[] = $val['id'];
		}
		$op['line_select'] = $arry2->select($line_name, "", "PHP_line", "styled-select","get_line_zone(this.value,'zone','from')");
		$op['line_to_select'] = $arry2->select($line_name, "", "PHP_line_to", "styled-select","get_line_zone(this.value,'zone_to','to')");
		
	}
    
	$zone = $WAREHOUSING->get_storage_zone($fty);
	$zone_id = $zone_name = array();
	foreach($zone as $k => $val){
		$zone_id[] = $val['id'];
		$zone_name[] = $val['zone'];
	}
	$op['zone_select'] = $arry2->select($zone_name, "", "PHP_zone", "styled-select", "", "");
	$op['zone_to_select'] = $arry2->select($zone_name, "", "PHP_zone_to", "styled-select", "", "");
	
	$op['css'] = array( 'css/scm.css' );
	$op['js'] = array( 'js/jquery.min.js' , 'js/storage.js' , 'js/jquery.blockUI.js' );
	
	page_display($op,$TR_AUTH,$TPL_STORAGE_TRANSFER);
break;
#
#
#
#
#
case "get_fty_line":
	check_authority($TR_AUTH,"view");

	$line = $WAREHOUSING->get_fty_line($_GET['PHP_factory']);
    $str_line = '';
    foreach($line as $key => $val){
        $pp = $key > 0 ? ',':'';
        $str_line .= $pp.$val['line'];
    }
	echo !empty($str_line)?$str_line:'nodata';
	
break;	
#
#
#
#
#
#
case "get_line_zone":
	check_authority($AUTH,"view");

	$PHP_zoen = $_GET['PHP_zoen'];
    
    if($PHP_zoen == 'full'){
        $zone = $WAREHOUSING->get_storage_zone($_GET['PHP_factory']);
    } else {
        $zone = $WAREHOUSING->get_line_zone($_GET['PHP_factory'] , $_GET['PHP_line']);
    }
    $str_zone = '';
    foreach($zone as $key => $val){
        $pp = $key > 0 ? ',':'';
        $str_zone .= $pp.$val['zone'];
    }
    echo !empty($str_zone)?$str_zone:'nodata';
	
break;
#
#
#
#
#
#
case "transfer_qty":
	check_authority($TR_AUTH,"add");
	
	$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
	$fty = $_POST['PHP_factory'] ? $_POST['PHP_factory'] : $_GET['PHP_factory'];
	$fty_to = $_POST['PHP_factory_to'] ? $_POST['PHP_factory_to'] : $_GET['PHP_factory_to'];
	$line = $_POST['PHP_line'] ? $_POST['PHP_line'] : $_GET['PHP_line'];
	$line_to = $_POST['PHP_line_to'] ? $_POST['PHP_line_to'] : $_GET['PHP_line_to'];
	$zone = $_POST['PHP_zone'] ? $_POST['PHP_zone'] : $_GET['PHP_zone'];
	$zone_to = $_POST['PHP_zone_to'] ? $_POST['PHP_zone_to'] : $_GET['PHP_zone_to'];
	$in_out = $_POST['PHP_in_out'] ? $_POST['PHP_in_out'] : $_GET['PHP_in_out'];
	
	if(strtoupper($in_out) == 'OUT'){
		$op = $WAREHOUSING->get_size_breakdown($ord_num);
		# 羆 Grand Total计秖
		foreach($op['partial'] as $p_key => $p_val){
			for($i=0;$i<sizeof($p_val['wiqty']);$i++){
				$ttl_color_qty = 0;
				$op['partial'][$p_key]['wiqty'][$i]['qtys'] = array();
				foreach($op['size_des'] as $s_key => $s_val){
					$qty = $WAREHOUSING->get_size_breakdown_qty("'i'", $fty, $line, $zone, $ord_num, $p_val['mks'], $op['partial'][$p_key]['wiqty'][$i]['colorway'], $s_val);
					$op['partial'][$p_key]['wiqty'][$i]['qtys'][] = $qty;
					$ttl_color_qty += $qty;
				}
				$op['partial'][$p_key]['wiqty'][$i]['ttl_color_qty'] = $ttl_color_qty;
			}
		}
		
		# 块计秖
		$op['storage_det'] = $WAREHOUSING->get_storage_det4transfer("'t'", $fty, $line, $zone, $ord_num);
	}else{
		$op['storage_det'] = $WAREHOUSING->get_transfer("'t'", $fty_to, $line_to, $zone_to, $ord_num);
	}

    $op['today'] = $TODAY;
	$op['sid'] = $op['storage_det']['storage_det_out'][0]['sid'];
	$op['ord_num'] = $ord_num;
	$op['fty'] = $fty;
	$op['fty_to'] = $fty_to;
	$op['line'] = $line;
	$op['line_to'] = $line_to;
	$op['zone'] = $zone;
	$op['zone_to'] = $zone_to;
	$op['in_out'] = $in_out;
	
	$op['css'] = array( 'css/scm.css' );
	$op['js'] = array( 'js/jquery.min.js' , 'js/storage.js' , 'js/jquery.blockUI.js' );

	if(strtoupper($in_out) == 'OUT'){
		page_display($op,$TR_AUTH,$TPL_STORAGE_TRANSFER_OUT);
	}else{
		page_display($op,$TR_AUTH,$TPL_STORAGE_TRANSFER_IN);
	}
break;
#
#
#
#
#
#
case "transfer_search":
	check_authority($TR_AUTH,"view");
    	
	$storage_num = $_GET['PHP_storage_num'] ? $_GET['PHP_storage_num'] : $_POST['PHP_storage_num'];
	$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

    $table = "storage";
    $m_sql= "id,ord_num,create_date,create_user,cfm_date,cfm_user,status";
    $where_str = "WHERE del_mk = 'n' ";
    $where_str .= !empty($storage_num) ? " AND `id` LIKE '%".$storage_num."%' " : "";
    $where_str .= !empty($ord_num) ? " AND `ord_num` LIKE '%".$ord_num."%' " : "";
    
    $op = $Search->page_sorting($table,$m_sql,$PHP_action,$where_str,10);


	$op['css'] = array( 'css/scm.css' );
	$op['js'] = array( 'js/jquery.min.js' , 'js/storage.js' , 'js/jquery.blockUI.js' );
    
	page_display($op,$TR_AUTH,$TPL_STORAGE_TRANSFER_SEARCH);
break;
#
#
#
#
#
#
case "transfer_view":
	check_authority($TR_AUTH,"view");

	$sid = $_GET['PHP_sid'] ? $_GET['PHP_sid'] : $_POST['PHP_sid'];
    
    $op['storage_det'] = $WAREHOUSING->get_transfer_id($sid);

    $ord_num = $op['storage_det']['storage_det_in'][0]['ord_num'];
    $fty_from = $op['storage_det']['storage_det_in'][0]['fty_from'];
    $fty_to = $op['storage_det']['storage_det_in'][0]['fty_to'];
    $line_from = $op['storage_det']['storage_det_in'][0]['line_from'];
    $line_to = $op['storage_det']['storage_det_in'][0]['line_to'];
    $zone_from = $op['storage_det']['storage_det_in'][0]['zone_from'];
    $zone_to = $op['storage_det']['storage_det_in'][0]['zone_to'];
	$op = $WAREHOUSING->get_size_breakdown($ord_num);
    

    $op['fty_select'] = $arry2->select($FACTORY, $fty_from, "PHP_factory", "styled-select", "get_fty_line(this.value,'line','from')" ,"","disabled");
    $op['fty_to_select'] = $arry2->select($FACTORY, $fty_to, "PHP_factory_to", "styled-select", "get_fty_line(this.value,'line_to','to')");
    $line = $WAREHOUSING->get_fty_line($fty_from);
    $line_name = $line_id = array();
    foreach($line as $key => $val){
        $line_name[$val['id']] = $val['line'];
    }
    $op['line_select'] = $arry2->select_id($line_name,$line_from, "PHP_line", "PHP_line", "styled-select","get_line_zone(this.value,'zone','from')","","","","disabled");
    $op['line_to_select'] = $arry2->select_id($line_name,$line_to, "PHP_line_to", "PHP_line_to", "styled-select","get_line_zone(this.value,'zone_to','to')");

	$zone = $WAREHOUSING->get_storage_zone($fty_from);
	$zone_name = array();
	foreach($zone as $k => $val){
		$zone_name[$val['id']] = $val['zone'];
	}
	$op['zone_select'] = $arry2->select_id($zone_name,$zone_from, "PHP_zone", "PHP_zone", "styled-select", "","","","","disabled");
	$op['zone_to_select'] = $arry2->select_id($zone_name,$zone_to, "PHP_zone_to", "PHP_zone_to", "styled-select", "", "");

	# 羆 Grand Total计秖
	foreach($op['partial'] as $p_key => $p_val){
		for($i=0;$i<sizeof($p_val['wiqty']);$i++){
			$ttl_color_qty = 0;
			$ttl_color_qtye = 0;
			$op['partial'][$p_key]['wiqty'][$i]['qtys'] = array();
			$op['partial'][$p_key]['wiqty'][$i]['qtye'] = array();
			foreach($op['size_des'] as $s_key => $s_val){
				$qty = $WAREHOUSING->get_size_breakdown_qty_id("'i'",$sid, $fty_from, $line_from, $zone_from, $ord_num, $p_val['mks'], $op['partial'][$p_key]['wiqty'][$i]['colorway'], $s_val);
				$qtye_arr = $WAREHOUSING->get_transfer_breakdown_qty_id("'t'",$sid, $fty_to, $line_to, $zone_to, $ord_num, $p_val['mks'], $op['partial'][$p_key]['wiqty'][$i]['colorway'], $s_val);
				$op['partial'][$p_key]['wiqty'][$i]['qtys'][] = $qty;
                $ide = $qtye_arr['id'];
                $qtye = $qtye_arr['qty'];
				$op['partial'][$p_key]['wiqty'][$i]['ide'][] = $ide;
				$op['partial'][$p_key]['wiqty'][$i]['qtye'][] = $qtye;
				$ttl_color_qty += $qty;
				$ttl_color_qtye += $qtye;
			}
			$op['partial'][$p_key]['wiqty'][$i]['ttl_color_qty'] = $ttl_color_qty;
			$op['partial'][$p_key]['wiqty'][$i]['ttl_color_qtye'] = $ttl_color_qtye;
		}
	}
    // print_r($op);
    $op['reload'] = "onLoad=re_cal_storage('1')";
    $op['sid'] = $sid;
    $op['ord_num'] = $ord_num;
    $op['fty_from'] = $fty_from;
    $op['fty_to'] = $fty_to;
    $op['line_from'] = $line_from;
    $op['line_to'] = $line_to;
    $op['zone_from'] = $zone_from;
    $op['zone_to'] = $zone_to;
	$op['css'] = array( 'css/scm.css' );
	$op['js'] = array( 'js/jquery.min.js' , 'js/storage.js' , 'js/jquery.blockUI.js' );
    
	page_display($op,$TR_AUTH,$TPL_STORAGE_TRANSFER_VIEW);
break;
#
#
#
#
#
#
case "do_update_transfer_qty":
	check_authority($AUTH,"edit");
// print_r($_POST);
    if ( $WAREHOUSING->update_storage_qty('t', $_POST, true) ) {
        $_SESSION['MSG'][] = $message = "Successfully Update Records! " . $_POST['PHP_sid'];

        $log->log_add(0,$AUTH."E",$message);
    } else {
        $_SESSION['MSG'][] = "Error ! Can't Update records!";
    }
    
    header("Location: ".$PHP_SELF."?PHP_action=transfer_view&PHP_sid=".$_POST['PHP_sid']);
	exit;
	header("Location: ".$PHP_SELF."?PHP_action=transfer_qty&PHP_in_out=".$_POST['PHP_in_out']."&PHP_factory=".$_POST['PHP_factory']."&PHP_factory_to=".$_POST['PHP_factory_to']."&PHP_ord_num=".$_POST['PHP_ord_num']."&PHP_line=".$_POST['PHP_line']."&PHP_line_to=".$_POST['PHP_line_to']."&PHP_zone=".$_POST['PHP_zone']."&PHP_zone_to=".$_POST['PHP_zone_to']);
break;
#
#
#
#
#
#
case "transfer_delete":
	check_authority($TR_AUTH,"del");

	$sid = $_GET['PHP_sid'] ? $_GET['PHP_sid'] : $_POST['PHP_sid'];
    
    if( $WAREHOUSING->delete_transfer_id('y',$sid) ) {
        $_SESSION['MSG'][] = "Successfully Delete record! ID:".$sid;
        $log->log_add(0,$AUTH."D", "delete storag id = ".$sid);
        header("Location: ".$PHP_SELF."?PHP_action=transfer");
    } else {
        $_SESSION['MSG'][] = "Error ! Can't Update records!";
    }
break;
#
#
#
#
#
case "check_transfer":
	check_authority($TR_AUTH,"add");
	
	page_display($op,$TR_AUTH,$TPL_STORAGE_TRANSFER_IN);
break;
} # CASE END
?>
