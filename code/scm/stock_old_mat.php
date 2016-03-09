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
$TPL_STOCK_OLD_LOTS_SEARCH = 'stock_old_lots_search.html';
$TPL_STOCK_OLD_LOTS_ADD = "stock_old_lots_add.html";
$TPL_STOCK_OLD_LOTS_LIST = "stock_old_lots_list.html";
$TPL_STOCK_OLD_LOTS_EDIT = "stock_old_lots_edit.html";
$TPL_STOCK_OLD_ACC_SEARCH = 'stock_old_acc_search.html';
$TPL_STOCK_OLD_ACC_ADD = "stock_old_acc_add.html";
$TPL_STOCK_OLD_ACC_LIST = "stock_old_acc_list.html";
$TPL_STOCK_OLD_ACC_EDIT = "stock_old_acc_edit.html";
#

#
#
$AUTH = '103';
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
	
	if($PHP_msg) $op['msg'][] = $PHP_msg;
	page_display($op,$AUTH,$TPL_STOCK_OLD_LOTS_SEARCH);
break;
#
#
#
#
#
#
case "lots_add":
	check_authority($AUTH,"add");
	
	page_display($op,$AUTH,$TPL_STOCK_OLD_LOTS_ADD);
break;
#
#
#
#
#
#
case "do_lots_add":
	check_authority($AUTH,"add");
	
	$parm = array(	
				"mat_name"		=>	$PHP_lots_name,
				"supl_code"		=>	$PHP_supl_code,
				"qty"			=>	$PHP_qty,
				"color"			=>	$PHP_color,
				"price"			=>	$PHP_price,
				"unit"			=>	$PHP_unit,
				"width"			=>	$PHP_width
			);


	$f1 = $WAREHOUSING->append_old_lots($parm);
	if ($f1) {
		$message= "Append fabric record:".$parm['mat_name'];
		$log->log_add(0,$AUTH."A",$message);    
		$redir_str = 'stock_old_mat.php?PHP_action=do_lots_search&PHP_msg='.$message;
		redirect_page($redir_str);
		break;
	}else{
		
	}
	
break;
#
#
#
#
#
case "do_lots_search":		
	check_authority($AUTH,"view");

	$sch_parm = array(	"SCH_lots_code"		=>	$SCH_lots_code,
						"SCH_lots_name"		=>	$SCH_lots_name,
						"PHP_action"		=>	"do_lots_search",
						"PHP_sr_startno"	=>	$PHP_sr_startno
				);
				
			
	$op = $WAREHOUSING->search_old_lots($sch_parm);
	$op['msg']= $lots->msg->get(2);
	if (isset($PHP_msg)) $op['msg'][]=$PHP_msg;
	page_display($op,"003", $TPL_STOCK_OLD_LOTS_LIST);		    	    
	
break;
#
#
#
#
#
case "lots_edit":		
	check_authority($AUTH,"edit");

	$op['lots'] = $WAREHOUSING->get_old_lots($_GET['PHP_id']);
	
	page_display($op,"003", $TPL_STOCK_OLD_LOTS_EDIT);		    	    
	
break;
#
#
#
#
#
case "do_lots_edit":		
	check_authority($AUTH,"edit");

	$parm = array("id"	=>	$_POST['PHP_id'],
				   "mat_name"	=> $_POST['PHP_lots_name'],
				  "supl_code"	=> $_POST['PHP_supl_code'],
				  "qty"			=> $_POST['PHP_qty'],
				  "color"		=> $_POST['PHP_color'],
				  "price"	=> $_POST['PHP_price'],
				  "unit"	=> $_POST['PHP_unit'],
				  "width"	=> $_POST['PHP_width']
				);
	if($t_f = $WAREHOUSING->update_old_lots($parm)){
		$msg = "Successfully update Fabric : ". $parm['mat_name'];
	}else{
		$msg = "Can't update Fabric : ". $parm['mat_name'];
	}
	
	
	redirect_page($PHP_SELF."?PHP_action=main&PHP_msg=".$msg);
	
break;
#
#
#
#
#
case "del_old_lots":
	check_authority($AUTH,"del");
	
	if($t_f = $WAREHOUSING->del_old_lots($_GET['PHP_id'])){
		$msg = "Successfully delete Fabric : ". $_GET['PHP_mat_name'];
	}else{
		$msg = "Can't delete Fabric : ". $_GET['PHP_mat_name'];
	}
	
	
	redirect_page($PHP_SELF."?PHP_action=main&PHP_msg=".$msg);
	
break;
#
#
#
#
#
case "acc":
	check_authority($AUTH,"view");
	
	if($PHP_msg) $op['msg'][] = $PHP_msg;
	page_display($op,$AUTH,$TPL_STOCK_OLD_ACC_SEARCH);
break;
#
#
#
#
#
#
case "acc_add":
	check_authority($AUTH,"add");
	
	page_display($op,$AUTH,$TPL_STOCK_OLD_ACC_ADD);
break;
#
#
#
#
#
#
#
#
#
#
#
case "do_acc_add":
	check_authority($AUTH,"add");
	
	$parm = array(	
				"mat_name"		=>	$PHP_acc_name,
				"supl_code"		=>	$PHP_supl_code,
				"qty"			=>	$PHP_qty,
				"color"			=>	$PHP_color,
				"price"			=>	$PHP_price,
				"unit"			=>	$PHP_unit
			);


	$f1 = $WAREHOUSING->append_old_acc($parm);
	if ($f1) {
		$message= "Append acc record:".$parm['mat_name'];
		$log->log_add(0,$AUTH."A",$message);    
		$redir_str = 'stock_old_mat.php?PHP_action=do_acc_search&PHP_msg='.$message;
		redirect_page($redir_str);
		break;
	}else{
		
	}
	
break;
#
#
#
#
#
case "do_acc_search":		
	check_authority($AUTH,"view");

	$sch_parm = array(	"SCH_lots_code"		=>	$SCH_acc_code,
						"SCH_lots_name"		=>	$SCH_acc_name,
						"PHP_action"		=>	"do_acc_search",
						"PHP_sr_startno"	=>	$PHP_sr_startno
				);
				
			
	$op = $WAREHOUSING->search_old_acc($sch_parm);
	$op['msg']= $lots->msg->get(2);
	if (isset($PHP_msg)) $op['msg'][]=$PHP_msg;
	page_display($op,"003", $TPL_STOCK_OLD_ACC_LIST);		    	    
	
break;
#
#
#
#
#
case "acc_edit":		
	check_authority($AUTH,"edit");

	$op['acc'] = $WAREHOUSING->get_old_acc($_GET['PHP_id']);
	
	page_display($op,"003", $TPL_STOCK_OLD_ACC_EDIT);
	
break;
#
#
#
#
#
case "do_acc_edit":		
	check_authority($AUTH,"edit");

	$parm = array("id"	=>	$_POST['PHP_id'],
				   "mat_name"	=> $_POST['PHP_acc_name'],
				  "supl_code"	=> $_POST['PHP_supl_code'],
				  "qty"			=> $_POST['PHP_qty'],
				  "color"		=> $_POST['PHP_color'],
				  "price"	=> $_POST['PHP_price'],
				  "unit"	=> $_POST['PHP_unit']
				);
	if($t_f = $WAREHOUSING->update_old_acc($parm)){
		$msg = "Successfully update Acc : ". $parm['mat_name'];
	}else{
		$msg = "Can't update Acc : ". $parm['mat_name'];
	}
	
	
	redirect_page($PHP_SELF."?PHP_action=acc&PHP_msg=".$msg);
	
break;
#
#
#
#
#
case "del_old_acc":
	check_authority($AUTH,"del");
	
	if($t_f = $WAREHOUSING->del_old_acc($_GET['PHP_id'])){
		$msg = "Successfully delete Acc : ". $_GET['PHP_mat_name'];
	}else{
		$msg = "Can't delete Fabric : ". $_GET['PHP_mat_name'];
	}
	
	redirect_page($PHP_SELF."?PHP_action=acc&PHP_msg=".$msg);
	
break;
#
#
#
#
#
} # CASE END
?>
