<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');

session_register	('sch_parm');
##################  2004/11/10  ########################
#			monitor.php  ¥Dµ{¦¡
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.remnant.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";
$remnant = new REMNANT();
if (!$remnant->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$op = array();



switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "remnant":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "remnant":
 	check_authority(5,8,"view");
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		
		$where_str="order by cust_s_name"; //依cust_s_name排序
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //取出客戶簡稱
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 
		$op['factory'] = $arry2->select($FACTORY,'','SCH_factory','select','');
		
		for($i=0; $i<sizeof($FACTORY); $i++)
		{
			if($user_dept == $FACTORY[$i]) $op['factory'] = $user_dept."<input type=hidden name=SCH_factory value='".$user_dept."'>";
		}
		

	page_display($op, 5,8, $TPL_REMNANT);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "remnant_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "remnant_search":
 	check_authority(5,8,"add");

		if(isset($SCH_order))
		{		
			$sch_parm = array();
			$sch_parm = array(	
										"SCH_order"				=>  $SCH_order,				"SCH_factory"		=>	$SCH_factory,
										"SCH_cust"				=>	$SCH_cust,				"SCH_style"			=>	$SCH_style,
										"PHP_sr_startno"	=>	$PHP_sr_startno,	"PHP_action"		=>	$PHP_action
				);			
			}else{
				if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
			}
		$op = $remnant->search_shipped(1);


	page_display($op, 5,8, $TPL_REMNANT_LIST);
	break;


//=======================================================
    case "remnant_view":

		check_authority(5,8,"view");
		//-------------------- 將 製造令 show out ------------------
		//  wi 主檔
		if(!$op['wi']  = $wi->get(0,$PHP_ord_num)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		
		//  smpl 樣本檔
		$op['order'] = $order->get($op['wi']['smpl_id']);
		$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);
		//-----------------------------------------------------------------
			
			
		//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['bom_lots'] = $remnant->get_lots($op['wi']['id'],'v');  //取出該筆 bom 內ALL主料記錄


		//  取出  BOM  副料記錄 --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc'] = $remnant->get_acc($op['wi']['id'],'v');  //取出該筆 bom 內ALL副料記錄
	
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		page_display($op, 5,8, $TPL_REMNANT_VIEW);
		break;



//=======================================================
    case "remnant_edit":

		check_authority(5,8,"view");
		//-------------------- 將 製造令 show out ------------------
		//  wi 主檔
		if(!$op['wi']  = $wi->get(0,$PHP_ord_num)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		
		//  smpl 樣本檔
		$op['order'] = $order->get($op['wi']['smpl_id']);
		$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);
		//-----------------------------------------------------------------
			
			
		//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['bom_lots'] = $remnant->get_lots($op['wi']['id']);  //取出該筆 bom 內ALL主料記錄


		//  取出  BOM  副料記錄 --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc'] = $remnant->get_acc($op['wi']['id']);  //取出該筆 bom 內ALL副料記錄
	
		page_display($op, 5,8, $TPL_REMNANT_EDIT);
		break;



//=======================================================
    case "do_remnant_edit":

		check_authority(5,8,"view");
		//-------------------- 將 製造令 show out ------------------
		foreach($PHP_lots_rem as $key => $value)
		{
			$parm = array($key, 'rem_qty', $value, 'bom_lots');
			$bom->update_field($parm);
		}

		foreach($PHP_acc_rem as $key => $value)
		{
			$parm = array($key, 'rem_qty', $value, 'bom_acc');
			$bom->update_field($parm);
		}		

		$PHP_msg = "Suceess Update Remnant Material by Order: ".$PHP_ord_num;
		$log->log_add(0,"58E",$PHP_msg);

		$redir_str = 'ord_close.php?PHP_action=remnant_view&PHP_ord_num='.$PHP_ord_num."&PHP_msg=".$PHP_msg;
		redirect_page($redir_str);		
		


//=======================================================
    case "remnant_submit":

		check_authority(5,8,"view");
		
		$order->update_field_num('close_status', 2, $PHP_ord_num);
		
		//-------------------- 將 製造令 show out ------------------
				

		$PHP_msg = "Suceess Submit Remnant Material by Order: ".$PHP_ord_num;
		$log->log_add(0,"58E",$PHP_msg);

		$redir_str = 'ord_close.php?PHP_action=remnant_view&PHP_ord_num='.$PHP_ord_num."&PHP_msg=".$PHP_msg;
		redirect_page($redir_str);		
		
//=======================================================
    case "ord_close":

		check_authority(5,8,"view");
		
		$order->update_field_num('close_status', 4, $PHP_ord_num);
		$order->update_field_num('close_des', $PHP_close_value, $PHP_ord_num);
		//-------------------- 將 製造令 show out ------------------
				

		$PHP_msg = "Suceess Close Order: ".$PHP_ord_num;
		$log->log_add(0,"58E",$PHP_msg);

		$redir_str = 'index2.php?PHP_action=order_view&PHP_id='.$PHP_id."&PHP_msg=".$PHP_msg;
		redirect_page($redir_str);			
//-------------------------------------------------------------------------

}   // end case ---------

?>
