<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
##################  2004/11/10  ########################
#			monitor.php  ¥Dµ{¦¡
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$op = array();

/*
//工繳取得

*/
//工繳取得

$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

$para_cm = $para->get(0,'hj_labor');
$FTY_LABOR['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly_labor');
$FTY_LABOR['LY'] = $para_cm['set_value'];

$RATE['0'] = 0;
$RATE_NAME['0'] = '=select=';
$para_cm = $para->get(0,'RATE1');
$RATE['1'] = $para_cm['set_value'];
$RATE_NAME['1'] = 'RATE1';
$RATE_DES = 'RATE1='.$para_cm['memo'].",&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$para_cm = $para->get(0,'RATE2');
$RATE['2'] = $para_cm['set_value'];
$RATE_NAME['2'] = 'RATE2';
$RATE_DES .= 'RATE2='.$para_cm['memo'].",&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$para_cm = $para->get(0,'RATE3');
$RATE['3'] = $para_cm['set_value'];
$RATE_NAME['3'] = 'RATE3';
$RATE_DES .= 'RATE3='.$para_cm['memo'].", <BR>";
$para_cm = $para->get(0,'RATE4');
$RATE['4'] = $para_cm['set_value'];
$RATE_NAME['4'] = 'RATE4';
$RATE_DES .= 'RATE4='.$para_cm['memo'].",&nbsp;&nbsp;&nbsp;&nbsp;";
$para_cm = $para->get(0,'RATE5');
$RATE['5'] = $para_cm['set_value'];
$RATE_NAME['5'] = 'RATE5';
$RATE_DES .= 'RATE5='.$para_cm['memo'].",&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$para_cm = $para->get(0,'RATE6');
$RATE['6'] = $para_cm['set_value'];
$RATE_NAME['6'] = 'RATE5';
$RATE_DES .= 'RATE6='.$para_cm['memo'];
$RATE_KEY = array(0,1,2,3,4,5,6);

$para_cm = $para->get(0,'fty_salary');
$LY_SALARY = $para_cm['set_value'];

$para_cm = $para->get(0,'VND');
$VND = $para_cm['set_value'];

//$ly_cost = 1352800;


switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "overtime":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "overtime":
 	check_authority(4,4,"view");
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
 		if ($user_dept =='HJ' || $user_dept=='LY')
 		{
			$op['FACTORY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
 			$where_str = "AND ord_num <>'' AND fty = '$user_dept'";
			$op['FTY_search'] = "<b>".$user_dept."</b><input type=hidden name='SCH_fty' value='$user_dept'>";
 		
 		}else{
			$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');
 		
			$op['FTY_search'] = $arry2->select($FACTORY,'','SCH_fty','select');
			$where_str = "AND ord_num <>''";
		}

/*	
			$where_str="order by cust_s_name";
			$cust_def = $cust->get_fields('cust_init_name',$where_str);
			$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);
			for ($i=0; $i< sizeof($cust_def); $i++)
			{
				$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
			}
			$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 
*/		


		$op['MM_select'] =$arry2->select($MONTH_WORK,'','SCH_MM','select','');
		$op['YY_select'] =$arry2->select($YEAR_WORK,date('Y'),'SCH_YY','select','');

		$op['date'] = $TODAY;
		
//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];		
	$op['msg'] = $overtime->msg->get(2);
	page_display($op, 4,4, $TPL_OVERTIME);
	break;
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "overtime_add":
 	check_authority(4,4,"add");
 		//$tmp_date = explode('-',$PHP_date);
		$f1 = $overtime->add($PHP_date,$PHP_FTY,$VND);
		if(!$f1)
		{
//			$redir_str = "overtime.php?PHP_action=overtime";
//			redirect_page($redir_str);		
 			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
 			if ($user_dept =='HJ' || $user_dept=='LY')
 			{
				$op['FACTORY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
				$op['FTY_search'] = "<b>".$user_dept."</b><input type=hidden name='SCH_fty' value='$user_dept'>";
 		
 			}else{
				$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');	
				$op['FTY_search'] = $arry2->select($FACTORY,'','SCH_fty','select');
			}

			$op['MM_select'] =$arry2->select($MONTH_WORK,'','SCH_MM','select','');
			$op['YY_select'] =$arry2->select($YEAR_WORK,date('Y'),'SCH_YY','select','');

			$op['date'] = $TODAY;
		
//080725message增加		
			$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
			$op['max_notify'] = $note['max_no'];		
			$op['msg'] = $overtime->msg->get(2);
			page_display($op, 4,4, $TPL_OVERTIME);
			break;
		}
		
 		$op = $overtime->add_get($PHP_date,$PHP_FTY,$RATE_NAME);
 		$op['rate'] =  $arry2->select($RATE_NAME,'','PHP_rate','select',"onChange=count_cost('ot_add')",$RATE_KEY);
 		for($i=0 ; $i<sizeof($op['ot_det']); $i++)
 		{
 			$op['ot_det'][$i]['rate_select'] =$arry2->select($RATE_NAME,$op['ot_det'][$i]['rate'],'PHP_rate','select',"onChange=count_cost('ot_edit".$op['ot_det'][$i]['id']."')",$RATE_KEY);
 		}
 		
 		
 		
		$op['fty'] = $PHP_FTY;
		$op['date'] = $PHP_date;
		$op['cost'] = $LY_SALARY;
		$op['rate_key'] = $RATE_KEY;
		$op['rate_value'] = $RATE;
		$op['rate_des'] = $RATE_DES;
		$op['currency'] = $VND;
	page_display($op, 4,4, $TPL_OVERTIME_ADD);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_ot_add_det":
 	check_authority(4,4,"add");
 		//$tmp_date = explode('-',$PHP_date);

 		$ot_cost = $PHP_ot_worker * $PHP_ot_time * $RATE[$PHP_rate] * ($LY_SALARY /8 )/ $VND;
 		$parm = array(
 									'ot_id'			=> 	$PHP_id,
 									'ot_worker'	=>	$PHP_ot_worker,
 									'ot_time'		=>	$PHP_ot_time,
 									'rate'			=>	$PHP_rate,
 									'ot_cost'		=>	$ot_cost
 								 );
		$f1 = $overtime->add_det($parm);
		
		$parm = array(
 									'field_name'	=> 	'worker',
 									'field_value'	=>	$PHP_worker,
 									'id'					=>	$PHP_id, 			
 								 );
		$f1 = $overtime->update_field($parm);
		
 		$op = $overtime->add_get($PHP_date,$PHP_FTY,$RATE_NAME);
 		$op['rate'] =  $arry2->select($RATE_NAME,'','PHP_rate','select',"onChange=count_cost('ot_add')",$RATE_KEY);
 		for($i=0 ; $i<sizeof($op['ot_det']); $i++)
 		{
 			$op['ot_det'][$i]['rate_select'] =$arry2->select($RATE_NAME,$op['ot_det'][$i]['rate'],'PHP_rate','select',"onChange=count_cost('ot_edit".$op['ot_det'][$i]['id']."')",$RATE_KEY);
 		}
 		
		$op['fty'] = $PHP_FTY;
		$op['date'] = $PHP_date;
		$op['cost'] = $LY_SALARY;
		$op['rate_key'] = $RATE_KEY;
		$op['rate_value'] = $RATE;
		$op['rate_des'] = $RATE_DES;
		$op['currency'] = $VND;
		$op['msg'][] = "successfully append overtime on [".$PHP_date."]";
	page_display($op, 4,4, $TPL_OVERTIME_ADD);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_ot_edit_det":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_ot_edit_det":
 	check_authority(4,4,"add");
 		//$tmp_date = explode('-',$PHP_date);

 		$ot_cost = $PHP_ot_worker * $PHP_ot_time * $RATE[$PHP_rate] * ($LY_SALARY/8) / $VND;
 		$parm = array(
 									'id'				=> 	$PHP_id,
 									'ot_worker'	=>	$PHP_ot_worker,
 									'ot_time'		=>	$PHP_ot_time,
 									'rate'			=>	$PHP_rate,
 									'ot_cost'		=>	$ot_cost
 								 );
		$f1 = $overtime->edit_det($parm);
		
 		$op = $overtime->add_get($PHP_date,$PHP_FTY,$RATE_NAME);
 		$op['rate'] =  $arry2->select($RATE_NAME,'','PHP_rate','select',"onChange=count_cost('ot_add')",$RATE_KEY);
 		for($i=0 ; $i<sizeof($op['ot_det']); $i++)
 		{
 			$op['ot_det'][$i]['rate_select'] =$arry2->select($RATE_NAME,$op['ot_det'][$i]['rate'],'PHP_rate','select',"onChange=count_cost('ot_edit".$op['ot_det'][$i]['id']."')",$RATE_KEY);
 		}
 		
		$op['fty'] = $PHP_FTY;
		$op['date'] = $PHP_date;
		$op['cost'] = $LY_SALARY;
		$op['rate_key'] = $RATE_KEY;
		$op['rate_value'] = $RATE;
		$op['rate_des'] = $RATE_DES;
		$op['currency'] = $VND;
		$op['msg'][] = "successfully update overtime on [".$PHP_date."]";
	page_display($op, 4,4, $TPL_OVERTIME_ADD);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_ot_edit_det":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "overtime_del":
 	check_authority(4,4,"add");
 		//$tmp_date = explode('-',$PHP_date);
 
		$f1 = $overtime->del_det($PHP_id);
		
 		$op = $overtime->add_get($PHP_date,$PHP_FTY,$RATE_NAME);
 		$op['rate'] =  $arry2->select($RATE_NAME,'','PHP_rate','select',"onChange=count_cost('ot_add')",$RATE_KEY);
 		for($i=0 ; $i<sizeof($op['ot_det']); $i++)
 		{
 			$op['ot_det'][$i]['rate_select'] =$arry2->select($RATE_NAME,$op['ot_det'][$i]['rate'],'PHP_rate','select',"onChange=count_cost('ot_edit".$op['ot_det'][$i]['id']."')",$RATE_KEY);
 		}
 		
		$op['fty'] = $PHP_FTY;
		$op['date'] = $PHP_date;
		$op['cost'] = $LY_SALARY;
		$op['rate_key'] = $RATE_KEY;
		$op['rate_value'] = $RATE;
		$op['rate_des'] = $RATE_DES;
		$op['currency'] = $VND;
		$op['msg'][] = "successfully delete overtime on [".$PHP_date."]";
	page_display($op, 4,4, $TPL_OVERTIME_ADD);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_overtime_add":
 	check_authority(4,4,"add");
 		//$tmp_date = explode('-',$PHP_date);
		
		$parm = array(
 									'field_name'	=> 	'worker',
 									'field_value'	=>	$PHP_worker,
 									'id'					=>	$PHP_id, 			
 								 );
		$f1 = $overtime->update_field($parm);
		
 		$op = $overtime->get_all($PHP_date,$PHP_FTY,$RATE_NAME);
		$op['rate_des'] = $RATE_DES;
		$op['currency'] = $VND;
		$op['cost'] = $LY_SALARY;
	page_display($op, 4,4, $TPL_OVERTIME_VIEW);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "overtime_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "overtime_view":
 	check_authority(4,4,"add");
 		//$tmp_date = explode('-',$PHP_date);
		if(!$SCH_YY)$SCH_YY = date('Y');
		if(!$SCH_MM)$SCH_MM = date('m');
		$PHP_date = $SCH_YY."-".$SCH_MM;
		
 		$op = $overtime->get_all($PHP_date,$SCH_fty,$RATE_NAME);
		$op['rate_des'] = $RATE_DES;
		$op['currency'] = $VND;
		$op['cost'] = $LY_SALARY;
	page_display($op, 4,4, $TPL_OVERTIME_VIEW);
	break;	

	
//-------------------------------------------------------------------------

}   // end case ---------

?>
