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
#			monitor.php  ￥Dμ{|!
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['REQUEST_URI'];


$perm = $GLOBALS['power'];

require_once "init.object.php";
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

include_once($config['root_dir']."/lib/pdt_finish.class.php");
$pdt_finish = new PDT_FINISH();
if (!$pdt_finish->init($mysql,"log")) { print "error!! cannot initialize database for pdt_finish class"; exit; }

include_once($config['root_dir']."/lib/pdtion.class.php");
$pdtion = new PDTION();
if (!$pdtion->init($mysql,"log")) { print "error!! cannot initialize database for pdtion class"; exit; }

$op = array();

$P_LINE = array (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

/*
//工繳取得

*/
//工繳取得

$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$para_cm = $para->get(0,'cf-cm');
$FTY_CM['CF'] = $para_cm['set_value'];

$FTY_CM['SC'] = 0;

$para_cm = $para->get(0,'hj_labor');
$FTY_LABOR['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly_labor');
$FTY_LABOR['LY'] = $para_cm['set_value'];
$para_cm = $para->get(0,'cf_labor');
$FTY_LABOR['CF'] = $para_cm['set_value'];

$LINE_STYLE = array("top","btm.","all");


session_register	('RATE');
$para_cm = $para->get(0,'RATE1');
$RATE['1'] = $para_cm['set_value'];
$para_cm = $para->get(0,'RATE2');
$RATE['2'] = $para_cm['set_value'];
$para_cm = $para->get(0,'RATE3');
$RATE['3'] = $para_cm['set_value'];
$para_cm = $para->get(0,'RATE4');
$RATE['4'] = $para_cm['set_value'];
$para_cm = $para->get(0,'RATE5');
$RATE['5'] = $para_cm['set_value'];
$para_cm = $para->get(0,'RATE6');
$RATE['6'] = $para_cm['set_value'];

session_register	('LY_SALARY');
$para_cm = $para->get(0,'fty_salary');
$LY_SALARY = $para_cm['set_value'];

session_register	('VND');
$para_cm = $para->get(0,'VND');
$VND = $para_cm['set_value'];

session_register	('pdt_range');
$para_cm = $para->get(0,'pdt_range');
$pdt_range = $para_cm['set_value'];

session_register	('sch_finish_rate');
$para_cm = $para->get(0,'sch_finish_rate');
$sch_finish_rate = $para_cm['set_value'];

session_register	('schedule_var');
$para_cm = $para->get(0,'schedule_var');
$schedule_var = $para_cm['set_value'];

session_register('sch_ln_id');

$AUTH = '028';

// echo $PHP_action;
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "monitor":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "monitor":
check_authority('028',"view");

$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');
$op['LINE_select'] = $arry2->select($P_LINE,'','PHP_LINE','select','');

page_display($op, '028', $TPL_MONITOR_MAIN);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "monitor_ord":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "monitor_daily":
check_authority('028',"view");

$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
if ($user_dept =='HJ' || $user_dept=='LY' || $user_dept=='CF')
{
	$op['FACTORY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
	$where_str = "AND ord_num <>'' AND fty = '$user_dept'";
	$op['FTY_search'] = "<b>".$user_dept."</b><input type=hidden name='SCH_fty' value='$user_dept'>";

}else{
	$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');

	$op['FTY_search'] = $arry2->select($FACTORY,'','SCH_fty','select','get_fty(this)');
	$where_str = "AND ord_num <>''";
}

$line = $monitor->get_fields_saw('DISTINCT pdt_saw_line.line');
$op['LINE_search'] = $arry2->select($line,'','SCH_line','select');
$ord_num = $monitor->get_fields_saw('DISTINCT ord_num',$where_str);
$op['ORDER_search'] = $arry2->select($ord_num,'','SCH_ord','select');

$where_str="order by cust_s_name";
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);
for ($i=0; $i< sizeof($cust_def); $i++) {
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
}
$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 


//		$monitor->count_mon_peo();


if (!isset($ord_num[0])) $op['ORDER_search'] ="<input type=hidden name='SCH_ord' value=''>";
if (!isset($line[0])) $op['LINE_search'] ="<input type=hidden name='SCH_line' value=''>";
$op['date'] = $TODAY;
$op['day'] = date('Y-m-d');

//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];		
if(isset($PHP_msg))	$op['msg'][] = $PHP_msg;

page_display($op, '028', $TPL_SAW);
break;

	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "monitor_ord":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "saw_finish_create":
 	check_authority('028',"view");
 	
 		$f1 = $monitor->saw_finish_creat();
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
 		if ($user_dept =='HJ' || $user_dept=='LY' || $user_dept=='CF')
 		{
			$op['FACTORY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
 			$where_str = "AND ord_num <>'' AND fty = $user_dept";
			$op['FTY_search'] = "<b>".$user_dept."</b><input type=hidden name='SCH_fty' value='$user_dept'>";

 		}else{
			$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');
			$where_str = "AND ord_num <>''";
			$op['FTY_search'] = $arry2->select($FACTORY,'','SCH_fty','select','get_fty(this)');
 		}
		
		$line = $monitor->get_fields_saw('DISTINCT pdt_saw_line.line');
		$op['LINE_search'] = $arry2->select($line,'','SCH_line','select');
		$ord_num = $monitor->get_fields_saw('DISTINCT ord_num',$where_str);
		$op['ORDER_search'] = $arry2->select($ord_num,'','SCH_ord','select');

		$where_str="order by cust_s_name";
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 


		if (!isset($ord_num[0])) $op['ORDER_search'] ="<input type=hidden name='SCH_ord' value=''>";
		if (!isset($line[0])) $op['LINE_search'] ="<input type=hidden name='SCH_line' value=''>";
		$op['date'] = $TODAY;

	page_display($op, '028', $TPL_SAW);
	break;	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "monitor_ord":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_fty":
check_authority('028',"view");

$where_str = " AND fty = '$SCH_fty' AND ord_num <>''";
$line = $monitor->get_fields_saw('DISTINCT line',$where_str);
$op['LINE_search'] = $arry2->select($line,'','SCH_line','select');
$ord_num = $monitor->get_fields_saw('DISTINCT ord_num',$where_str);
$op['ORDER_search'] = $arry2->select($ord_num,'','SCH_ord','select');
if (!isset($ord_num[0])) $op['ORDER_search'] ="<input type=hidden name='SCH_ord' value=''>";
if (!isset($line[0])) $op['LINE_search'] ="<input type=hidden name='SCH_line' value=''>";

echo $op['LINE_search']."|".$op['ORDER_search'];
break;	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "monitor":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "dm_add": 
check_authority('028',"add");

// if ( !$PHP_date ) $PHP_date = $TODAY;
if ( !$PHP_FTY || !$PHP_date )
{
	$PHP_msg= "Please select Factory, and Out-put Date.";
	$redirect_str = "monitor.php?PHP_action=monitor_daily&PHP_msg=".$PHP_msg;
	redirect_page($redirect_str);
}
if ( isset($PHP_chg) ) $PHP_date = 	$org_date = increceDaysInDate($PHP_date,$PHP_chg);

$op['mon_det'] = $monitor->get_daily_out($PHP_FTY,$PHP_date);

$total = 0;
for ($i=0; $i<sizeof($op['mon_det']); $i++)      
{
	$total += $op['mon_det'][$i]['qty'];
}

$op['mon']['total'] = $total;
$op['mon']['fty'] = $PHP_FTY;
$op['mon']['date'] = $PHP_date;

if(isset($PHP_edit_mk))$op['back_none']=1;
page_display($op, '028', $TPL_SAW_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "monitor":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "saw_other_style":
check_authority('028',"add");

$op['mon']['fty'] = $PHP_FTY;
$op['mon']['date'] = $PHP_date;

$op['mon_det'] = $mon_det = $monitor->get_daily_out($PHP_FTY,$PHP_date);
$new_i = sizeof($op['mon_det']);
$total = $mk = 0;

for ($j=0; $j<sizeof($op['mon_det']); $j++)
{
	$line3[] = $op['mon_det'][$j];
	if( $op['mon_det'][$j]['id'] == $PHP_id && $mk === 0 ) 
	{
		$tmp = $op['mon_det'][$j];

		$tmp['ord_num'] = '';
		$tmp['mks'] = '';
		$tmp['s_qty'] = '';
		$tmp['sch_out_qty'] = '';
		$tmp['ord_qty'] = '';
		$tmp['cut_qty'] = '';
		$tmp['saw_qty'] = '';
		
		$tmp['sid'] = '';
		
		$tmp['ot_wk'] = '';
		$tmp['ot_hr'] = '';
		
		$tmp['work_qty'] = '';
		$tmp['over_qty'] = '';
		
		$tmp['qty'] = '';
		$tmp['ord_qty'] = '';
		$tmp['saw_qty'] = '';

		$tmp['holiday'] = '';
		$tmp['style'] = '';
		$tmp['ie'] = '';
		
		$tmp['i'] = $new_i;
		$tmp['new_mk'] = 1;
		$line3[] = $tmp;
		$mk = 1;
	}
	$total += $op['mon_det'][$j]['qty'];
}

$op['mon_det'] = $line3;
$op['mon']['total'] = $total;

page_display($op, '028', $TPL_SAW_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "dm_add_line":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "saw_hdy_add":
check_authority('028',"add");

$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
if ($user_dept =='HJ' || $user_dept=='LY' || $user_dept=='CF')
{
	$op['FACTORY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
	$where_str = "AND ord_num <>'' AND fty = '$user_dept'";
	$op['FTY_search'] = "<b>".$user_dept."</b><input type=hidden name='SCH_fty' value='$user_dept'>";

}else{
	$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');

	$op['FTY_search'] = $arry2->select($FACTORY,'','SCH_fty','select','get_fty(this)');
	$where_str = "AND ord_num <>''";
}

$line = $monitor->get_fields_saw('DISTINCT pdt_saw_line.line');
$op['LINE_search'] = $arry2->select($line,'','SCH_line','select');
$ord_num = $monitor->get_fields_saw('DISTINCT ord_num',$where_str);
$op['ORDER_search'] = $arry2->select($ord_num,'','SCH_ord','select');

$where_str="order by cust_s_name";
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);
for ($i=0; $i< sizeof($cust_def); $i++)
{
	$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
}

$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 

if (!isset($ord_num[0])) $op['ORDER_search'] ="<input type=hidden name='SCH_ord' value=''>";
if (!isset($line[0])) $op['LINE_search'] ="<input type=hidden name='SCH_line' value=''>";

if (!$PHP_FTY || !$PHP_date)
{
	$op['msg'][]= "Please select Factory, and Date.";
	$op['date'] = $TODAY;
	page_display($op, '069', $TPL_SAW);
	break;
}

$f1 = $monitor->check_date($PHP_FTY,$PHP_date);
if(!$f1)
{
	$op['msg'][]= "<b>".$PHP_date." had out-put records alerady.</b>  Please check carefully, if it's Holiday";
	$op['date'] = $TODAY;

	page_display($op, '028', $TPL_SAW);
	break;
}

$org_date = increceDaysInDate($PHP_date,-1);
$f1 = $monitor->check_date($PHP_FTY,$org_date);
if($f1)
{
	$op['msg'][]= "<b>".$org_date." need out-put records.</b>  Please check carefully, if it's not Holiday";
	$op['date'] = $TODAY;

	page_display($op, '028', $TPL_SAW);
	break;
} 		

$line = $monitor->get_daily_out($PHP_FTY,$org_date); //取得前一天的資料

for ($i=0; $i<sizeof($line); $i++)
{
	$parm = array (	
		"line_id"		=>	$line[$i]['id'],
		"ord_num"		=>	$line[$i]['ord_num'],
		"workers"		=>	$line[$i]['worker'],
		"saw_line"	=>	$line[$i]['line'],
		"saw_fty"		=>	$line[$i]['fty'],
		"p_id"			=>	$line[$i]['p_id'],
		"s_id"			=>	$line[$i]['s_id'],
		"out_date"	=>	$PHP_date,
		"qty"				=>	'0',
		"holiday"		=>	'1',
		"su"				=>	'0',
		"worktime"	=> 	'',
		"overtime"	=>	'',
		"ot_wk"			=>	'',
		"ot_wk"			=>	'',
		"ot_hr"			=> 	'' 
	);
	$f1 =	$monitor->add_saw($parm);
}

if($f1) $msg = "Successfully ADD [fty :".$PHP_FTY."] HOLIDAY In : ".$PHP_date;
if(!$f1)$msg = $monitor->msg->get(2);
$op['msg'][] = $msg;
$op['date'] = $TODAY;

page_display($op, '028', $TPL_SAW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "saw_line_add":
	check_authority('028',"add");
 		$line =	$monitor->get_line($PHP_FTY);
		$op['fty'] = $PHP_FTY;
		$op['line'] = $line;
		$value = array(-1,0,1);
		$key  = array('select',$PHP_FTY,'SC.');
		if($PHP_FTY == 'LY')
		{
			$value[] = 2;
			$key[] = 'LA';
		}
		
		
		$op['sc_select'] =  $arry2->select_by_pline($key,'-1','PHP_sc','select','',$value); 
		$op['style'] = $arry2->select($LINE_STYLE,'','PHP_style','select'); 
	page_display($op, '028', $TPL_SAW_LINE_ADD);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_saw_line_add":
 	check_authority('028',"add");
		$sub_fty = array('2'	=>	'LA');
		$PHP_sub = '';
		if($PHP_sc < 0)
		{
			$op['msg'][] = 'please select fty first';
		}else{
			if($PHP_sc > 1)
			{
				$PHP_sub = $sub_fty[$PHP_sc];
				$PHP_sc = 0;
			}

 			$parm = array (	"fty"			=>	$PHP_FTY,
 											"line"		=>	$PHP_line,
 											"worker"	=>	$PHP_worker,
 											'style'		=>	$PHP_style,
 											'sc'			=>	$PHP_sc,
 											'sub_fty'	=>	$PHP_sub,		
 											);

											$f1 =	$monitor->add_line($parm);
 			if($f1) $op['msg'][] = "Successfully Append Line : ".$PHP_line." ON FTY : ".$PHP_FTY;
 			if(!$f1) $op['msg'][] = "Production Line : ".$PHP_line." ON FTY : ".$PHP_FTY." has already exist, please change another once.";
 		}
 		$line =	$monitor->get_line($PHP_FTY);
		$value = array(-1,0,1);
		$key  = array('select',$PHP_FTY,'SC.');
		$op['sc_select'] =  $arry2->select_by_pline($key,'-1','PHP_sc','select','',$value); 
		$op['style'] = $arry2->select($LINE_STYLE,'','PHP_style','select'); 

		$op['fty'] = $PHP_FTY;
		$op['line'] = $line;
	page_display($op, '028', $TPL_SAW_LINE_ADD);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "saw_line_edit":
check_authority('028',"edit");

$line =	$monitor->get_line($PHP_FTY);
$op['fty'] = $PHP_FTY;
$op['line'] = $line;

$value = array(-1,0,1);
$key  = array('select',$PHP_FTY,'SC.');

for ($i=0; $i<sizeof($line); $i++)
{
	$op['line'][$i]['style_select'] = $arry2->select($LINE_STYLE,$op['line'][$i]['line_style'],'PHP_style','select'); 
	$op['line'][$i]['sc_select'] =  $arry2->select_by_pline($key,$op['line'][$i]['sc'],'PHP_sc','select','',$value); 
}

page_display($op, '028', $TPL_SAW_LINE_EDIT);
break;

	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_saw_line_edit":
check_authority('028',"edit");

$parm = array (	
"id"			=>	$PHP_id,
"line"		=>	$PHP_line,
"worker"	=>	$PHP_worker,
"fty"			=>	$PHP_FTY,
"style"		=>	$PHP_style,
"sc"			=>	$PHP_sc,
);

$f1 =	$monitor->edit_line($parm);
if($f1) $msg = "Successfully Edit Line : ".$PHP_line." & Worker :".$PHP_worker." ON Factory : ".$PHP_FTY;
if(!$f1) $msg = "Production Line : ".$PHP_line." ON FTY : ".$PHP_FTY." has already exist, please change another once.";
echo $msg;

break;


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_saw_line_del":
check_authority('028',"edit");

if ( $schedule->chk_saw_out_put_line($PHP_id) ){
    $f1 =	$monitor->del_line($PHP_id);
    $msg = "Successfully Delete Line : ".$PHP_line." ON Factory : ".$PHP_FTY;
} else {
    $msg = "Error Delete Line : ".$PHP_line." ON Factory : ".$PHP_FTY;
}
echo $msg;
break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_dm_add":
check_authority('028',"add");

if($PHP_holiday == 3) $PHP_qty = 0;

$PHP_worktime = $PHP_qty;
$PHP_qty += $PHP_overtime;
$su = number_format(($PHP_qty * $PHP_ie),0,'','');


# 判斷產出是否超過裁減量
# 該訂單已生產總件數
$fQty = $GLOBALS['order']->get_field_value('pdt_qty',$PHP_s_id);

# 該條生產線生產小記
// $sQty = $monitor->get_schedule_finish_value('sum(qty)','',$PHP_s_id);

$mQty = $fQty + $PHP_qty;
if( $PHP_p_cut_qty < $mQty ) {
	echo 'error|'.'|'.$mQty.'|'.$sQty.'|'.$PHP_p_cut_qty;
	// echo 'error|'.($mQty - $PHP_p_cut_qty).'|'.$sQty.'|'.$fQty.'|'.$PHP_p_id;
	exit;
}

$parm = array (
	"line_id"		=>	$PHP_line_id,
	"saw_line" 		=>	$PHP_line,
	"saw_fty"		=>	$PHP_fty,
	"ord_num"		=>	$PHP_ord_num,
	"out_date"		=>	$PHP_out_date,
	"qty"			=>	$PHP_qty,
	"su"			=>	$su,
	"holiday"		=>	$PHP_holiday,
	"attendence"    =>	$PHP_attendence,
	"workers"		=>	$PHP_t_worker,
	"worktime"		=>	$PHP_worktime,
	"overtime"		=>	$PHP_overtime,
	'ot_wk'			=> 	$PHP_ot_wk,
	'ot_hr'			=>	$PHP_ot_hr,
	'p_id'			=>	$PHP_p_id,
	's_id'			=>	$PHP_s_id
);

$f1 = $monitor->add_saw($parm); # 回傳陣列 : id 和 finish saw qty

# 修改status的狀態
# s_order
$schedule->chk_pdc_status($PHP_ord_num);

if($f1) $msg = "Successfully ADD Line : ".$PHP_line." Daily Out-put ON ORDER : ".$PHP_ord_num;
$log->log_add(0,"028A",$msg);
if(!$f1)$msg = $monitor->msg->get(2);

$ord_rec = '<a href="javascript://" onMouseOver="overTip(preview1,'.$PHP_ord_num.'); OrdBox(\'mon_add_'.$PHP_i.'\')" onMouseOut="outTip (preview1)"><b>'.$PHP_ord_num.'</b></a>';

# 抓取訂單已完成數量
$nowQty = $monitor->get_schedule_finish_value('sum(qty)','',$PHP_s_id);
# `schedule`              `pdt_qty`
$schedule->set_pdt_qty($PHP_s_id,$nowQty);

# 該訂單已生產總件數
$P_Qty = $GLOBALS['order']->get_saw_partial_qty('sum(qty)',$PHP_p_id);

$GLOBALS['schedule']->schedule_chk_finish($PHP_line_id);
$GLOBALS['schedule']->schedule_reset_pdtion_waitting($PHP_line_id);

if($PHP_holiday == 3) {$PHP_holiday ="day-off";}else{$PHP_holiday='&nbsp;';}
#0~6
echo $msg."|".$ord_rec."|".$PHP_style."|".number_format($PHP_worktime,0,'',',')."|".$PHP_holiday."|".$f1['id']."|".number_format($PHP_p_ord_qty,0,'',',')."|";
#7~11
echo number_format($f1['f_qty'],0,'',',')."|".$f1['f_qty']."|".number_format($PHP_total_qty,0,'',',')."|".$PHP_total_qty."|".$PHP_worker."|";
#12~16
echo $PHP_t_worker."|".number_format($PHP_overtime,0,'',',')."|".number_format($PHP_qty,0,'',',')."|".$PHP_ot_wk."|".$PHP_ot_hr."|";
#17~20
echo $P_Qty."|".$nowQty."|".$PHP_attendence."|".$PHP_s_id."|".$PHP_ord_num;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_saw_finish":
check_authority('028',"add");

if($PHP_holiday == 3) $PHP_qty =0;

$PHP_worktime = $PHP_qty;
$PHP_qty += $PHP_overtime;

# 判斷產出是否超過裁減量

# 該訂單已生產總件數
$fQty = $GLOBALS['order']->get_field_value('pdt_qty',$PHP_s_id);
$mQty = $fQty + $PHP_qty;

if( $PHP_p_cut_qty < $mQty ) {
	echo 'error|'.($mQty - $PHP_p_cut_qty).'|'.$mQty.'|'.$PHP_p_cut_qty."<".$mQty;
	exit;
}

$su = (int)($PHP_qty * $PHP_ie);
$parm = array (
	"line_id"       =>	$PHP_line_id,
	"ord_num"       =>	$PHP_ord_num,
	"out_date"	    =>	$PHP_out_date,
	"qty"           =>	$PHP_qty,
	"overtime"	    =>	$PHP_overtime,
	"attendence"    =>	$PHP_attendence,
	"worktime"	    =>	$PHP_worktime,

	"holiday"       =>	$PHP_holiday,
	"su"            =>	$su,
	"workers"       =>	$PHP_t_worker,
	"saw_line" 	    =>	$PHP_line,
	"saw_fty"       =>	$PHP_fty,
	'ord_qty'       =>	$PHP_p_ord_qty,

	'ot_wk'         => 	$PHP_ot_wk,
	'ot_hr'         =>	$PHP_ot_hr,
	'p_id'			=>	$PHP_p_id,
	's_id'			=>	$PHP_s_id
);

if($PHP_qty > 0)
{
	$f1 =	$monitor->add_saw($parm); //回傳陣列 : id和finish saw qty 			
}else{
	$f1['f_qty'] = '0';
	$f1['id'] = '0'; 			
}
$order->finish_order($PHP_p_id,$PHP_out_date);

if($f1) $msg = "Success Finish Order :$PHP_ord_num";
$log->log_add(0,"028A",$msg); 		

if(!$f1)$msg = $monitor->msg->get(2);

# schedule status
$sparm = array( 'field_name' => 'status' , 'field_value' => '2' , 'id' => $PHP_s_id );
$schedule->update_field($sparm);

$PHP_total_qty+=$PHP_qty;

$ord_rec = '<a href="javascript://" onMouseOver="overTip(preview1); OrdBox(\'mon_add_'.$PHP_i.'\')" onMouseOut="outTip (preview1)"><b>'.$PHP_ord_num.'</b></a>';

# 抓取訂單已完成數量
$nowQty = $monitor->get_schedule_finish_value('sum(qty)','',$PHP_s_id);

# `schedule`              `pdt_qty`
$schedule->set_pdt_qty($PHP_s_id,$nowQty);

# 該訂單已生產總件數
$P_Qty = $GLOBALS['order']->get_saw_partial_qty('sum(qty)',$PHP_p_id);

$GLOBALS['schedule']->schedule_chk_finish($PHP_line_id);
$GLOBALS['schedule']->schedule_reset_pdtion_waitting($PHP_line_id);

if($PHP_holiday == 3) {$PHP_holiday ="day-off";}else{$PHP_holiday='&nbsp;';}
#0~6
echo $msg."|".$ord_rec."|".$PHP_style."|".number_format($PHP_worktime,0,'',',')."|".$PHP_holiday."|".$f1['id']."|".number_format($PHP_p_ord_qty,0,'',',')."|";
#7~11
echo number_format($f1['f_qty'],0,'',',')."|".$f1['f_qty']."|".number_format($PHP_total_qty,0,'',',')."|".$PHP_total_qty."|".$PHP_worker."|";
#12~16
echo $PHP_t_worker."|".number_format($PHP_overtime,0,'',',')."|".number_format($PHP_qty,0,'',',')."|".$PHP_ot_wk."|".$PHP_ot_hr."|";
#17~20
echo $P_Qty."|".$nowQty."|".$PHP_attendence."|".$PHP_s_id."|".$PHP_ord_num;
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "do_saw_del": 刪除產出時 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_saw_del":
check_authority('028',"add");

if(!$PHP_qty) $PHP_qty =0;
$su = (int)($PHP_qty * $PHP_ie);
$parm = array (	"sid"   =>  $PHP_o_id );
$f1 = $monitor->del_saw($parm); //回傳陣列 : id和finish saw qty

# 修改status的狀態
# s_order
$schedule->chk_pdc_status($PHP_ord_num);
# order partial status
$order->update_partial('pdt_status','0', $PHP_p_id);
# pdtion finish status
$order->update_pdtfld_num('finish','NULL',$PHP_ord_num);
# schedule status
$sparm = array( 'field_name' => 'status' , 'field_value' => '0' , 'id' => $PHP_s_id );
$schedule->update_field($sparm);

if($f1) $msg = "Success Delete Order :$PHP_ord_num ON LINE : $PHP_line";
$log->log_add(0,"028A",$msg);
if(!$f1){
	$msg = $monitor->msg->get(2);		
	$msg = $msg[0];
}

if($PHP_holiday == 3) {$PHP_holiday ="day-off";}else{$PHP_holiday='&nbsp;';}
$PHP_total_qty = $PHP_total_qty - $PHP_qty;

# 抓取訂單已完成數量
$nowQty = $monitor->get_schedule_finish_value('sum(qty)','',$PHP_s_id);

# `schedule`              `pdt_qty`
$schedule->set_pdt_qty($PHP_s_id,$nowQty);

# 該訂單已生產總件數
$P_Qty = $GLOBALS['order']->get_saw_partial_qty('sum(qty)',$PHP_p_id);
# `order_partial`              `pdt_qty`
// $GLOBALS['order']->set_p_qty_done($PHP_p_id,$P_Qty);

# `schedule`              `su`
// $partial_arr = $schedule->mdf_line_ie($PHP_s_id);
    
# `pdt_finish`            `su`,`cut_su`,`saw_su`,`pack_su` 此TABLE看起來是要整合訂單所有生產階段的完成狀態，目前看起來是沒有再用：直接乘數量帶SU
// $pdt_finish->mdf_ie($PHP_ord_num,$PHP_ie);

# `order_partial`         `p_su`,`p_etp_su`,`p_fty_su`,`ext_su`  缺少重新計算 ext_period 生產天數：分年月FTY的SU，相同需要重排時間再計算SU
# 重制 partial 抓取 `schedule` 分佈的 `partial` 日期區間
// $pdtion_arr = $order->mdf_partial_ie($partial_arr,$PHP_ie);

# `pdtion`                `etp_su 訂單`,`fty_su 排產`,`out_su 產出`,`ext_su`  `ext_su` 已移到 Partial 計算  此計算個階段總 su：分年月SU需要先重排時間再計算SU
# 重制 pdtion 
// $pdtion->mdf_ie($pdtion_arr,$PHP_ie);

$GLOBALS['schedule']->schedule_chk_finish($PHP_line_id);
$GLOBALS['schedule']->schedule_reset_pdtion_waitting($PHP_line_id);

echo $msg."|".$PHP_ord_num."|".$PHP_style."|0|".$PHP_holiday."| | |".number_format($nowQty,0,'',',')."|".$nowQty."|".
number_format($PHP_total_qty,0,'',',')."|".$PHP_total_qty."|".
$P_Qty."|".$nowQty."|".$PHP_s_id;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_add_line":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_dm_edit":
check_authority('028',"view");
if($PHP_holiday == 3) $PHP_qty =0;
$su = (int)($PHP_qty * $PHP_ie);

$parm = array (
    "ord_num"   =>	$PHP_ord_num, 										
    "qty"       =>	$PHP_qty,
    "holiday"   =>	$PHP_holiday,
    "su"        =>	$su,
    "id"        =>	$PHP_o_id
);

$f1 =	$monitor->edit_saw($parm); //回傳finish saw qty
if($f1) $msg = "Successfully UPDATE Line : ".$PHP_line." Daily Out-put";
if(!$f1)$msg = $monitor->msg->get(2);
if($PHP_holiday == 3) {$PHP_holiday ="day-off";}else{$PHP_holiday='&nbsp;';}
echo $msg."|".$PHP_ord_num."|".$PHP_style."|".$PHP_qty."|".$PHP_holiday."|".$PHP_o_id."|".$PHP_p_ord_qty."|".$f1;
break;
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_daily_viewe":	  mon_det d_out ie su
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "dm_daily_viewe":
 	check_authority('028',"view");
 	
	$unix_time = strtotime($PHP_date); 
	$week_day = date('w', $unix_time);  	

    $mon_det = $monitor->daily_saw_view($PHP_fty,$PHP_date);
    
    // print_r($mon_det);
    $mon_people = $monitor->get_month_people((substr($PHP_date,0,7)),$PHP_fty);
    $mon_line = $monitor->get_month_line((substr($PHP_date,0,7)),$PHP_fty);
    $op['mon']['fty'] = $PHP_fty;
    $op['mon']['date'] = $PHP_date;
    $tmp=$mon_su=$tol_su=$tol_peo=$tol_line=$su=$qty=$line_people=$ttl_su=$wk_qty=$ot_qty=$ot_wk=$ot_hr=$spt=$d_out=$ot_cost=$reg_wk=0;
    $hdy=1;
    $line='';
    
    $k=0;
    for ($i=0; $i<sizeof($mon_det); $i++) {
        if ($mon_det[$i]['holiday'] <> 1  && $mon_det[$i]['holiday'] <> '') $hdy=$mon_det[$i]['holiday'];
        if($PHP_date < '2010-01-01' && $mon_det[$i]['work_qty'] == 0)$mon_det[$i]['work_qty'] = $mon_det[$i]['qty'];
        $mon_det[$i]['ot_rate'] = 0;
        if($mon_det[$i]['workers'] > 0)$mon_det[$i]['ot_rate'] = $mon_det[$i]['ot_day'] / $mon_det[$i]['workers'];
    }
    
    $line_arr=array();
    for ($i=0; $i<sizeof($mon_det); $i++) {
    
        if ($mon_det[$i]['sc'] == 0 && $mon_det[$i]['holiday'] == 0 && $mon_det[$i]['sid']<>'') {
            if(!in_array($mon_det[$i]['line'],$line_arr)){
            $tol_peo += $mon_det[$i]['attendence'];
            }
            $reg_wk += $mon_det[$i]['workers'];
            $ot_wk += $mon_det[$i]['ot_wk'];
            $ot_hr += $mon_det[$i]['ot_hr'];
        }
        
        array_push($line_arr,$mon_det[$i]['line']);
        // print_r($line_arr);
        $where_str = "WHERE order_num ='".$mon_det[$i]['ord_num']."'";
        if($mon_det[$i]['ord_num']) $mon_det[$i]['ord_fsh_qty'] = $monitor->get_field_finish('sawing',$where_str);
        // echo $mon_det[$i]['ord_fsh_qty']."<br>";
        
        $s_date = substr($PHP_date,0,7)."-01";
        $e_date = substr($PHP_date,0,7)."-31";
        $where_str = " AND fty ='$PHP_fty' AND line='".$mon_det[$i]['line']."' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 GROUP BY out_date";
        $lnsu= $monitor->get_fields_saw('sum(su) as su',$where_str);
        $lnpp= $monitor->get_fields_saw('workers',$where_str);
        $avg_su=0;
        $avg_pp=0;
        $ck = 0;
        for ($j=0; $j < sizeof($lnsu); $j++) {
            $avg_su+=$lnsu[$j];
            if($lnpp[$j] > 0)$avg_pp+=($lnsu[$j]/$lnpp[$j]);
            $ck = 1;
        }
        
        $where_str = " AND fty ='$PHP_fty' AND line='".$mon_det[$i]['line']."' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday > 0 GROUP BY out_date";
        $out_hdy = $monitor->get_fields_saw('count(out_date) as ot_day',$where_str);
        if(!isset($out_hdy[0]))$out_hdy[0] = 0;
        if(substr($PHP_date,0,7) == date('Y-m')) {
            $dd = date('d');
            if($ck > 0)$j = $dd - $out_hdy[0];
        } else {
            $mm_tmp = explode('-',$PHP_date);
            $dd = getDaysInMonth($mm_tmp[1],$mm_tmp[0]);
            if($ck > 0)$j = $dd - $out_hdy[0];
        }

        $mon_det[$i]['avg_su'] = $avg_su;
        $mon_det[$i]['avg_pp_su'] = $avg_pp;

        if($ck > 0 ) {
        
            $mon_det[$i]['avg_su'] = $avg_su / $j;
            $mon_det[$i]['avg_pp_su'] = $avg_pp / $j;
            $su += $mon_det[$i]['su'];
            $qty += $mon_det[$i]['qty'];
            $wk_qty += $mon_det[$i]['work_qty'];
            $ot_qty += $mon_det[$i]['over_qty'];
            if ($mon_det[$i]['holiday'] == '' && $hdy == 1) $mon_det[$i]['holiday'] = $hdy;
            if ($hdy > $mon_det[$i]['holiday'] && $mon_det[$i]['holiday'] <> '') $hdy =$mon_det[$i]['holiday'];
            $mon_det[$i]['ord_qty'] = $order->get_field_value('qty','',$mon_det[$i]['ord_num']);	

            if ($line <> $mon_det[$i]['line']) {
            
                $line = $mon_det[$i]['line'];
                if ($mon_det[$i]['sc'] == 0 && $mon_det[$i]['holiday'] == 0 && $mon_det[$i]['sid']<>'') {
                    $tol_line++;
                    
                    $spt += $mon_det[$i]['spt'];
                    $d_out += $mon_det[$i]['ot_day'];
                    if($PHP_fty == 'LY') {
                        if( $week_day > 0) {															
                            $tmp_h1 = ($mon_det[$i]['ot_hr'] > 4) ? '4' : $mon_det[$i]['ot_hr'];
                            $tmp_h2 = ($mon_det[$i]['ot_hr'] > 4) ? ($mon_det[$i]['ot_hr']-4) : '0';
                            $ot_cost += ($mon_det[$i]['ot_wk']*$tmp_h1* ($LY_SALARY /8 )* $RATE[1] / $VND);
                            $ot_cost += ($mon_det[$i]['ot_wk']*$tmp_h2* ($LY_SALARY /8 )* $RATE[2] / $VND);
                        } else {
                            $tmp_h1 = ($mon_det[$i]['ot_hr'] > 8) ? '8' : $mon_det[$i]['ot_hr'];
                            $tmp_h2 = ($mon_det[$i]['ot_hr'] > 8) ? ($mon_det[$i]['ot_hr']-8) : '0';
                            $ot_cost += ($mon_det[$i]['ot_wk']*$tmp_h1* ($LY_SALARY /8 )* $RATE[3] / $VND);
                            $ot_cost += ($mon_det[$i]['ot_wk']*$tmp_h2* ($LY_SALARY /8 )* $RATE[4] / $VND);								
                        }
                    }
                }

                if ($mon_det[$i]['sc'] == 0 && $avg_su > 0) $tmp++;
                if ($mon_det[$i]['sc'] == 0 && $avg_su > 0) $mon_su+=$mon_det[$i]['avg_su'];
                if ($mon_det[$i]['sc'] == 0 && $avg_su > 0) $ttl_su+=$avg_su;
                
                if ($mon_det[$i]['sc'] == 0 && $avg_su > 0) $line_people+=$mon_det[$i]['workers'];
            }		
            if ($mon_det[$i]['line'] != 'contractor' && $mon_det[$i]['holiday'] == 0 && $mon_det[$i]['sid']<>'')$tol_su += $mon_det[$i]['su'];				
            
            $op['mon_det'][$k] = $mon_det[$i];
            $op['mon_det'][$k]['i'] = $k;
            $k++;
        }			
        if($j == 0 && $mon_det[$i]['holiday']) {
            $tmp_mon_det[] = $mon_det[$i];
        }
        // echo $line.'	'.$mon_det[$i]['line'].'	'.$mon_det[$i]['attendence'].'	'.$tol_peo.'~'.$tmp.'<br>';
    }			
    // echo $tol_peo;
    // print_r($line_arr);
    $pdt_mk = array();
    $op['s_ttl']['r_qty'] = $op['s_ttl']['o_qty'] = $op['s_ttl']['qty'] = $op['s_ttl']['su'] = 0;
    $op['sc_ttl']['r_qty'] = $op['sc_ttl']['o_qty'] = $op['sc_ttl']['qty'] = $op['sc_ttl']['su'] = 0;
    
    for ($i=0; $i<sizeof($op['mon_det']); $i++) {
        if(!isset($pdt_mk[$op['mon_det'][$i]['line']]['d_out']))		$pdt_mk[$op['mon_det'][$i]['line']]['d_out'] =0;
        if(!isset($pdt_mk[$op['mon_det'][$i]['line']]['out_rate'])) $pdt_mk[$op['mon_det'][$i]['line']]['out_rate'] =0;
        if(!isset($pdt_mk[$op['mon_det'][$i]['line']]['cc']))        $pdt_mk[$op['mon_det'][$i]['line']]['cc'] =0;
        $pdt_mk[$op['mon_det'][$i]['line']]['d_out']    +=$op['mon_det'][$i]['d_out'];
        $pdt_mk[$op['mon_det'][$i]['line']]['out_rate'] +=$op['mon_det'][$i]['d_out'];
        $pdt_mk[$op['mon_det'][$i]['line']]['cc'] ++;
        
        if($op['mon_det'][$i]['sc'] == 1) {
            $op['sc_ttl']['r_qty'] +=   $op['mon_det'][$i]['work_qty'];
            $op['sc_ttl']['o_qty'] += 	$op['mon_det'][$i]['over_qty'];
            $op['sc_ttl']['qty']   += 	$op['mon_det'][$i]['qty'];
            $op['sc_ttl']['su']    += 	$op['mon_det'][$i]['su'];					
        } else {
            $op['s_ttl']['r_qty'] +=  $op['mon_det'][$i]['work_qty'];
            $op['s_ttl']['o_qty'] += 	$op['mon_det'][$i]['over_qty'];
            $op['s_ttl']['qty']   += 	$op['mon_det'][$i]['qty'];
            $op['s_ttl']['su']    += 	$op['mon_det'][$i]['su'];			
        }

        //判斷是否需要小計(分廠內與外發)
        $op['mon_det'][$i]['s_mk'] = 0;
        if(isset($op['mon_det'][($i+1)]['sc']) && $op['mon_det'][($i+1)]['sc'] != $op['mon_det'][$i]['sc']) $op['mon_det'][$i]['s_mk'] = 1;			
        if(!isset($op['mon_det'][($i+1)]['sc']) && $op['sc_ttl']['qty'] > 0) $op['mon_det'][$i]['s_mk'] = 2;

    }
    
    foreach($pdt_mk as $key => $value) {
        if($pdt_mk[$key]['cc'] > 1) {
            for ($i=0; $i<sizeof($op['mon_det']); $i++) {
                if($op['mon_det'][$i]['line'] == $key) {
                    $op['mon_det'][$i]['d_out']	= $pdt_mk[$key]['d_out'];
                    $op['mon_det'][$i]['out_rate']	= $pdt_mk[$key]['out_rate'];
                }
            }
        }
    } 

    $op['su'] = $su;
    $op['qty'] =$qty;
    $op['wk_qty'] =$wk_qty;
    $op['ot_qty'] =$ot_qty;
    
    $op['hdy'] =$hdy;
    
    $op['t_line'] = $tol_line;
    $op['t_worker'] = $tol_peo;
    
    $op['t_ot_wk'] = $ot_wk;		//加班人數總計
    $op['t_ot_hr'] = $ot_hr;		//加班小時數總計
    $op['t_ot_dy'] = $d_out ;		//加班工作日(人數*小時數)總計
    $op['t_ot_rt'] = $op['t_ot_dy'] / $op['t_worker'];		//加班率
    $op['t_spt'] = $spt;		//標準產出總計
    $op['ot_cost'] = $ot_cost;
    $op['t_d_out'] = $op['qty'] / ($op['t_ot_dy'] + $op['t_worker']);		//每日工作產出總計
    $op['t_rate'] = $op['qty'] / $op['t_spt'] * 100;				//效率總計
		
    $ttl_rec = array(
        't_wk' 	    =>	$reg_wk,
        'ot_wk'	    =>	$ot_wk,
        'ot_hr'	    =>	$ot_hr,
        'ot_day'	=>	$d_out,
        'ot_rt'	    =>	$op['t_ot_rt'],
        'spt'		=>	$spt,
        'wk_qty'	=>	$wk_qty,
        'ot_qty'	=>	$ot_qty,
        'qty'		=>	$qty,
        'su'		=>	$su,
        'd_out'	    =>	$op['t_d_out'],
        'rate'		=>	$op['t_rate'],
        'reg_wk'	=>	$tol_peo,
    );
		
    if($tmp == 0) $tmp=1;
    $month_line = $tmp;
    $mon_su  = number_format($mon_su,'',0,'');
	
    //計算生產天數  -- start	
    $where_str = " AND fty ='$PHP_fty'  AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 GROUP BY out_date";
    $out_dy = $monitor->get_fields_saw('out_date',$where_str);
    $mon_days = sizeof($out_dy);
    /*			
            if(substr($PHP_date,0,7) == date('Y-m'))
            {
                $dd = date('d');
                $mon_days = $dd - sizeof($out_hdy);
                //echo $where_str."<BR>".$dd;
            }else{
                $mm_tmp = explode('-',$PHP_date);
                $dd = getDaysInMonth($mm_tmp[1],$mm_tmp[0]);
                $mon_days = $dd - sizeof($out_hdy);
            }
    */		
    //計算生產天數  -- end
    //echo '<font color=#ffffff>'.$ttl_su.'/'.$mon_days.'/'.$mon_line.'/</font>';
    $op['mon_avg_su'] = $ttl_su / $mon_days / $mon_line;  //計算每月生產線平均
    $op['mon_avg_peo_su'] = $ttl_su / $mon_days / $mon_people; //計算每月每人平均
    
    for($i=0; $i<sizeof($op['mon_det']); $i++) {
        $op['mon_det'][$i]['SD'] = $op['mon_avg_peo_su'] * $op['mon_det'][$i]['workers'];
    }
    
    if ($tol_line == 0)	{
        $op['a_line_su'] = 0;
        $op['a_people_su'] = 0;
    } else {
        $op['a_line_su'] = $tol_su / $tol_line;
        $op['a_people_su'] =$tol_su / $tol_peo;
    }
    
    if(!isset($op['mon_det']))$op['mon_det']=$tmp_mon_det;
    $tmp_size = sizeof($op['mon_det']);

    if ($op['hdy'] == '' &&!$tol_line) {
        $op['msg'][]="No any Record.";
        $op['mon_none'] = 1;
    }

    //計算工廠收入支出
    $where_str = " AND fty ='$PHP_fty' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 order by out_date DESC";		
    $lst_date= $monitor->get_fields_saw('out_date',$where_str);
    
    // $mon_now_day = countDays ($s_date,$lst_date[0])+1;  //計算該月目前日數
    # 當月目前產出天數
    $mon_now_day = $monitor->get_month_day(substr($PHP_date,0,7),$PHP_fty);
    // if($PHP_fty == 'HJ')$day_cm = 2.33;
    // if($PHP_fty == 'LY')$day_cm = 1.5;

    // if($PHP_fty == 'HJ')$labor = 150;
    // if($PHP_fty == 'LY')$labor = 100;
    
    $labor = $FTY_LABOR[$PHP_fty];
    
    // echo $ttl_su."*".$FTY_CM[$PHP_fty];
    // $line_people = $monitor->get_month_people(substr($PHP_date,0,7),$PHP_fty);
    $op['in_cost'] = $ttl_su*$FTY_CM[$PHP_fty];
    
    $tmp_date = explode('-',$PHP_date);
    $mm_day = getDaysInMonth($tmp_date[1],$tmp_date[0]);
    
    $op['out_cost'] = $mon_people*($labor / $mm_day *$mon_now_day) ;
    // echo $mon_people."*(".$labor." / ".$mm_day." * ".$mon_now_day.")<br>";
    // echo '('.$op['in_cost'].' - '.$op['out_cost'].') / '.$mon_now_day.' / '.$tmp;
    #  mon_now_day = 當月目前產出天數 / tmp = 幾組
    $op['markup'] = ($op['in_cost'] - $op['out_cost'])/$mon_now_day/$tmp;
    $op['cm_cost'] = $FTY_CM[$PHP_fty];
		$op['per_cost'] = $labor;
		
		
		
	if(isset($PHP_pdf))	{
    
		include_once($config['root_dir']."/lib/class.pdf_production.php");		
		$print_title=$PHP_fty." Production Daildy Report";
		$print_title2 = "DATE : ".$PHP_date;
		$mark = $PHP_date;

		$pdf=new PDF_prduction('L','mm','A4');
		$pdf->AddBig5Font();
		$pdf->Open();
		$pdf->AddPage();
		$pdf->SetAutoPageBreak(1);
        // $pdf->SetFont('Arial','B',14);	
		$j=0;
		for($i=0; $i<sizeof($op['mon_det']); $i++) {
			if($j>25) {
				$pdf->AddPage();		
				$j=0;
			}
			if(isset($op['mon_det'][$i]['ord_num']) && $op['mon_det'][$i]['ord_num']) {
				$pdf->pdt_value($op['mon_det'][$i]);
				$j++;
			}
			if($op['mon_det'][$i]['s_mk'] == 1) {
				$pdf->pdt_sub_ttl($op['s_ttl']);
				$j++;
			}
			if($op['mon_det'][$i]['s_mk'] == 2) {
				$pdf->pdt_sub_ttl($op['sc_ttl']);
				$j++;
			}			
		}
		if($j>25)	$pdf->AddPage();
		$pdf->pdt_ttl($ttl_rec);
		
		$name=$PHP_date."_".$PHP_fty.'_pdt.pdf';
		$pdf->Output($name,'D');	
	} else if (isset($PHP_excel) ) {
		require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
		require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}

        // HTTP headers
        HeaderingExcel('prdouction.xls');
	 
        // Creating a workbook
        $workbook = new Workbook("-");

        // Creating the first worksheet
        $worksheet1 =& $workbook->add_worksheet('Production daily list');

        $formatot =& $workbook->add_format();
        $formatot->set_size(10);
        $formatot->set_align('center');
        $formatot->set_color('white');
        $formatot->set_pattern();
        $formatot->set_fg_color('navy');

        $f3 =& $workbook->add_format(); //置右
        $f3->set_size(10);
        $f3->set_align('right');
        $f3->set_num_format(3);	  

        $f4 =& $workbook->add_format();  //灰底白字置右數字有小數點二位
        $f4->set_size(10);
        // $f4->set_pattern(1);
        $f4->set_align('right');
        $f4->set_num_format(4);

        $f6 =& $workbook->add_format();  //灰底白字置右
        $f6->set_size(10);
        $f6->set_color('white');
        $f6->set_pattern(1);
        $f6->set_align('right');
        $f6->set_num_format(3);
        $f6->set_fg_color('grey');

        $f5 =& $workbook->add_format();  //灰底白字置中
        $f5->set_size(10);
        $f5->set_align('center');
        $f5->set_color('white');
        $f5->set_pattern(1);
        $f5->set_fg_color('grey');
	  
        $now = $GLOBALS['THIS_TIME'];
        $clumn = array(    10,   15,     8,        20,     8,          8,         8,          8,        12,          8,       8,       8,       10,            10,            10,       10,     10,    12,           10);
        $title = array('Line','order#','style','style#','應有人數','出勤人數','加班人數','加班小時','加班工作日','加班率%', 'Calc. IE', 'Final IE','應產出(PC)', 'reg. out-put','ot. out-put','total', 'su','每日工作產出','效率%');

        for ($i=0; $i< sizeof($clumn); $i++) {
            $worksheet1->set_column(0,$i,$clumn[$i]);
        }
        $worksheet1->write_string(0,1,$PHP_fty.' Production daildy report');
        $worksheet1->write_string(0,10,$THIS_TIME);
        for ($i=0; $i< sizeof($title); $i++) {
            $worksheet1->write_string(1,$i,$title[$i],$formatot);
        }	  
        $j=2;
        for ($i=0; $i< sizeof($op['mon_det']); $i++) {	  	
            if(isset($op['mon_det'][$i]['ord_num']) && $op['mon_det'][$i]['ord_num']) {
                $worksheet1->write_string($j,0,$op['mon_det'][$i]['line']);
                $worksheet1->write_string($j,1,$op['mon_det'][$i]['ord_num']);
                $worksheet1->write_string($j,2,$op['mon_det'][$i]['ord_style']);
                $worksheet1->write_string($j,3,$op['mon_det'][$i]['style']);
                $worksheet1->write_number($j,4,$op['mon_det'][$i]['attendence'],$f3);
                $worksheet1->write_number($j,5,$op['mon_det'][$i]['workers'],$f3);
                $worksheet1->write_number($j,6,$op['mon_det'][$i]['ot_wk'],$f3);
                $worksheet1->write_number($j,7,$op['mon_det'][$i]['ot_hr'],$f3);
                $worksheet1->write_number($j,8,$op['mon_det'][$i]['ot_day'],$f3);
                $worksheet1->write_number($j,9,$op['mon_det'][$i]['ot_rate'],$f3);
                $worksheet1->write_number($j,10,$op['mon_det'][$i]['ie'],$f4);
                $worksheet1->write_number($j,11,$op['mon_det'][$i]['ie2'],$f4);
                $worksheet1->write_number($j,12,$op['mon_det'][$i]['spt'],$f3);
                $worksheet1->write_number($j,13,$op['mon_det'][$i]['work_qty'],$f3);
                $worksheet1->write_number($j,14,$op['mon_det'][$i]['over_qty'],$f3);
                $worksheet1->write_number($j,15,$op['mon_det'][$i]['qty'],$f3);
                $worksheet1->write_number($j,16,$op['mon_det'][$i]['su'],$f3);
                $worksheet1->write_number($j,17,$op['mon_det'][$i]['d_out'],$f3);
                $worksheet1->write_number($j,18,$op['mon_det'][$i]['out_rate'],$f3);	
                $j++;
            }
            if($op['mon_det'][$i]['s_mk'] == 2){
                $worksheet1->write_string($j,3,'SUB Total');
                // for($k=0; $k<7; $k++) $worksheet1->write_string($j,$k,'',$f5);
                $worksheet1->write_number($j,13,$op['sc_ttl']['r_qty'],$f3);
                $worksheet1->write_number($j,14,$op['sc_ttl']['o_qty'],$f3);
                $worksheet1->write_number($j,15,$op['sc_ttl']['qty'],$f3);
                $worksheet1->write_number($j,16,$op['sc_ttl']['su'],$f3);
                $j++;
			}	
            if($op['mon_det'][$i]['s_mk'] == 1) {
                $worksheet1->write_string($j,3,'SUB Total');
                // for($k=0; $k<7; $k++) $worksheet1->write_string($j,$k,'',$f5);
                $worksheet1->write_number($j,13,$op['s_ttl']['r_qty'],$f3);
                $worksheet1->write_number($j,14,$op['s_ttl']['o_qty'],$f3);
                $worksheet1->write_number($j,15,$op['s_ttl']['qty'],$f3);
                $worksheet1->write_number($j,16,$op['s_ttl']['su'],$f3);
                $j++;
			}				
        }	  
        for($i=0; $i<3; $i++) $worksheet1->write_string($j,$i,'',$f5);
        $worksheet1->write_string($j,3,'Total',$f5);
        $worksheet1->write_number($j,4,$ttl_rec['reg_wk'],$f6);
        $worksheet1->write_number($j,5,$ttl_rec['t_wk'],$f6);
        $worksheet1->write_number($j,6,$ttl_rec['ot_wk'],$f6);
        $worksheet1->write_number($j,7,$ttl_rec['ot_hr'],$f6);
        $worksheet1->write_number($j,8,$ttl_rec['ot_day'],$f6);
        $worksheet1->write_number($j,9,$ttl_rec['ot_rt'],$f6);
        $worksheet1->write_number($j,10,'',$f5);
        $worksheet1->write_number($j,11,'',$f5);
        $worksheet1->write_number($j,12,$ttl_rec['spt'],$f6);
        $worksheet1->write_number($j,13,$ttl_rec['wk_qty'],$f6);
        $worksheet1->write_number($j,14,$ttl_rec['ot_qty'],$f6);
        $worksheet1->write_number($j,15,$ttl_rec['qty'],$f6);
        $worksheet1->write_number($j,16,$ttl_rec['su'],$f6);
        $worksheet1->write_number($j,17,$ttl_rec['d_out'],$f6);
        $worksheet1->write_number($j,18,$ttl_rec['rate'],$f6);	
        $workbook->close();
	} else {
		if (isset($PHP_sr_startno)) {
			$op['back_str'] = "&PHP_sr_startno=".$PHP_sr_startno."&SCH_fty=".$SCH_fty."&SCH_line=".$SCH_line."&SCH_ord=".$SCH_ord."&SCH_str=".$SCH_str."&SCH_end=".$SCH_end."&SCH_cust=".$SCH_cust."&SCH_style=".$SCH_style;
		} else {
			if($PHP_back_str) $op['back_str'] = $PHP_back_str;
		}
        
		if($GLOBALS['SCACHE']['ADMIN']['team_id'] == 'SU' || $GLOBALS['SCACHE']['ADMIN']['team_id'] == 'SA' ) $op['mang_flag'] = 1;
        $op['pdt_range'] = $pdt_range;
        
		page_display($op, '028', $TPL_SAW_DAILY_VIEW);
		break;	
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "dm_search":
 	check_authority('028',"view");
		$SCH_line = str_replace("+","|",$SCH_line);
 		$op =	$monitor->saw_search();
 		$op['msg']= $monitor->msg->get(2);
 		if (!$SCH_str || !$SCH_end) $op['disable'] =1;
		$SCH_line = str_replace("+","|",$SCH_line);

		$op['back_str'] = "&SCH_fty=".$SCH_fty."&SCH_line=".$SCH_line."&SCH_ord=".$SCH_ord."&SCH_str=".$SCH_str."&SCH_end=".$SCH_end."&SCH_cust=".$SCH_cust."&SCH_style=".$SCH_style;
	page_display($op, '028', $TPL_SAW_LIST);
	break;
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_message_send":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "saw_ord_pic":
    check_authority('028',"view");
    
 		$line = $monitor->get_ord($PHP_ord_num);
		$PL   = $monitor->get_ord_line($PHP_ord_num);
		$op['order_num'] = $PHP_ord_num;
		$op['ord_rec'] = $order->get('',$PHP_ord_num);
		
//畫圖
		$years='';	$j=0;
		$first_date = increceDaysInDate($line[0]['out_date'],-1);
		// $x_ary[0]=substr($first_date,5);
		// $data[0]=0;
		// $x_ary[0]=substr($line[0]['out_date'],5);
		// $data[0]=$line[0]['su'];	
		$hy=0; //holiday的計數
		$tmp_hdy_date = '';		//holiday的前一日
		$tmp_hy = array();  //holiday的陣列內容
		$total_su=0;
		
//統計資料
		$op['ln_rec'][0]['str_date'] = $line[0]['out_date'];
		$op['ln_rec'][0]['end_date'] = $line[(sizeof($line) -1)]['out_date'];
		$op['ln_rec'][0]['qty'] = $op['ln_rec'][0]['su'] = $op['ln_rec'][0]['day'] = 0;
		for($i=0; $i<sizeof($line); $i++)
		{
			$op['ln_rec'][0]['qty'] += $line[$i]['out_qty'];
			$op['ln_rec'][0]['su'] += $line[$i]['su'];
			if($line[$i]['holiday'] == 0) $op['ln_rec'][0]['day']++;
		}
		$op['ln_rec'][0]['avg_qty'] = $op['ln_rec'][0]['qty'] / $op['ln_rec'][0]['day'];
		$op['ln_rec'][0]['avg_su'] = $op['ln_rec'][0]['su'] / $op['ln_rec'][0]['day'];		
		
		for ($i=0; $i < sizeof($line); $i++)
		{
			if ($line[$i]['holiday'] == 0) //如果不是假日時才記錄數量日期
			{
				$total_su += $line[$i]['su'];
				
				if ($tmp_hdy_date == '') //如果前一天是假日的話必需由假日向前扣一天
				{
					if($i > 0)$c_day=countDays ($line[($i-1)]['out_date'],$line[$i]['out_date']);
					if($i<= 0)$c_day= 1; 
	//				$c_day=countDays ($line[($i-1)]['out_date'],$line[$i]['out_date']);		
							
						
				}else{
					$c_day=countDays ($tmp_hdy_date,$line[$i]['out_date']);
					
				}
				
					$cut_day = $c_day - $hy; 
				if ($cut_day > 1)
				{
					if ($tmp_hdy_date == '') $tmp_hdy_date = $line[($i-1)]['out_date'];
					$su=$line[$i]['su'] / $cut_day;
					$tmp_su=0;
					for ($x=1; $x < $c_day; $x++)
					{
						$tmp_hy_mk = 0;
						$tmp_day=increceDaysInDate($tmp_hdy_date,$x);
						for ($hh=0; $hh <sizeof($tmp_hy); $hh++)
						{
							if ($tmp_day == $tmp_hy[$hh]) $tmp_hy_mk = 1;
						}
						if ($tmp_hy_mk == 0)
						{
							$x_ary[$j] = substr($tmp_day,5);
							$data[$j]=$su;
							$tmp_su=$tmp_su+$su;
							$j++;							
						}						
					}
					$tmp_day=increceDaysInDate($tmp_hdy_date,$c_day);
					$x_ary[$j] = substr($tmp_day,5);
					$su=$line[$i]['su']-$tmp_su;
					$data[$j]=$su;
					$j++;
				}else{				
					$x_ary[$j] = substr($line[$i]['out_date'],5);
					$data[$j]=$line[$i]['su'];					
					$j++;
					$su=$line[$i]['su'];
				}
				$tmp_hdy_date = '';
				$tmp_hy = array();
				$hy=0;

			}else{
				
				$tmp_hy[$hy] = $line[$i]['out_date'];
				$hy++;
				if ($tmp_hdy_date == '')  //如果之前不是假日的話
				{					
					$tmp_hdy_date = $line[($i-1)]['out_date'];			
				}
			}
        }

		if ($j == 1)
		{
			$data[$j] = $data[0];
			$x_ary[$j] =$x_ary[0];

		}
        
		$main_date = $x_ary;
		if (substr($line[0]['out_date'],0,4) <> substr($line[($i-1)]['out_date'],0,4))
		{
			$years=substr($line[0]['out_date'],0,4)."~".substr($line[($i-1)]['out_date'],0,4);
		}else{
			$years=substr($line[0]['out_date'],0,4);
		}

		//&#164;&#1956;J graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		include ($config['root_dir']."/lib/src/jpgraph_bar.php");
		$line_title ='';
		if (sizeof($PL) == 1) $line_title = "   Line : ".$PL[0]['line'];
		$graphic_title = "Order# : ".$PHP_ord_num." (".$line[0]['style_num'].")  YEAR : ".$years." TOTAL : ".$total_su."(su)".$line_title ;
		
		// Create the graph. These two calls are always required
		$graph = new Graph(600,210,"auto");    
		$graph->SetScale("textlin");
		
		// Adjust the margin
		$graph->img->SetMargin(60,20,30,40);    
		$graph->SetShadow();
		
		// Create the linear plot
		$lineplot=new LinePlot($data);
		$lineplot->mark->SetType(MARK_UTRIANGLE);
		$lineplot->value->SetFont( FF_FONT1, FS_BOLD); 
		$lineplot->value->SetFormat( " %d");
		$lineplot->value->show();

		// Add the plot to the graph
		$graph->Add($lineplot);

		$graph->title->Set($graphic_title);
		$graph->yaxis->title->Set("SU");

		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

		// Setup X-scale
		$graph->xaxis->SetTickLabels($x_ary);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
		$graph->xaxis->SetLabelAngle(45);

		$lineplot->SetColor("blue");
		$lineplot->SetWeight(2);

		// Display the graph
		$op['echart'][0]['pic'] = $graph->Stroke('picture/saw0.png');
		$op['echart'][0]['pic'] = 'picture/saw0.png';
		$op['echart'][0]['id'] = 0;
		
	if (sizeof($PL) > 1)
	{
		
		for ($a=0; $a<sizeof($PL); $a++)
		{			
		
			$line = $monitor->get_ord($PHP_ord_num,$PL[$a]['line']);

//統計資料
		$op['ln_rec'][($a+1)]['str_date'] = $line[0]['out_date'];
		$op['ln_rec'][($a+1)]['end_date'] = $line[(sizeof($line) -1)]['out_date'];
		$op['ln_rec'][($a+1)]['qty'] = $op['ln_rec'][($a+1)]['su'] = $op['ln_rec'][($a+1)]['day'] = 0;
		for($i=0; $i<sizeof($line); $i++)
		{
			$op['ln_rec'][($a+1)]['qty'] += $line[$i]['out_qty'];
			$op['ln_rec'][($a+1)]['su'] += $line[$i]['su'];
			if($line[$i]['holiday'] == 0) $op['ln_rec'][($a+1)]['day']++;
		}
		$op['ln_rec'][($a+1)]['avg_qty'] = $op['ln_rec'][($a+1)]['qty'] / $op['ln_rec'][($a+1)]['day'];
		$op['ln_rec'][($a+1)]['avg_su'] = $op['ln_rec'][($a+1)]['su'] / $op['ln_rec'][($a+1)]['day'];

//畫圖
		$years='';	$j=1;
		$x_ary = array();
		$data = array();
		$first_date = increceDaysInDate($line[0]['out_date'],-1);
		$x_ary[0]=substr($first_date,5);
		$data[0]=0;

//		$x_ary[0]=substr($line[0]['out_date'],5);
//		$data[0]=$line[0]['su'];	

		$hy=0; //holiday的計數
		$tmp_hdy_date = '';		//holiday的前一日
		$tmp_hy = array();  //holiday的陣列內容

	 
		
		for ($i=0; $i < sizeof($line); $i++)
		{
			if ($line[$i]['holiday'] == 0)
			{

				if ($tmp_hdy_date == '')
				{
					if($i > 0)$c_day=countDays ($line[($i-1)]['out_date'],$line[$i]['out_date']);					
					if($i <= 0)$c_day= 1;					
				}else{
					$c_day=countDays ($tmp_hdy_date,$line[$i]['out_date']);
				
				}
					$cut_day = $c_day - $hy;
				if ($cut_day > 1)
				{					
					if ($tmp_hdy_date == '') $tmp_hdy_date = $line[($i-1)]['out_date'];
					$su=$line[$i]['su'] / $cut_day;
					$tmp_su=0;
					for ($x=1; $x < $c_day; $x++)
					{
						$tmp_hy_mk = 0;
						$tmp_day=increceDaysInDate($tmp_hdy_date,$x);
						for ($hh=0; $hh <sizeof($tmp_hy); $hh++)
						{
							if ($tmp_day == $tmp_hy[$hh]) $tmp_hy_mk = 1;
						}
						if ($tmp_hy_mk == 0)
						{
							$x_ary[$j] = substr($tmp_day,5);
							$data[$j]=$su;
							$tmp_su=$tmp_su+$su;
							$j++;							
						}						
					}
					$tmp_day=increceDaysInDate($tmp_hdy_date,$c_day);
					$x_ary[$j] = substr($tmp_day,5);
					$su=$line[$i]['su']-$tmp_su;
					$data[$j]=$su;
					$j++;
				}else{				
					$x_ary[$j] = substr($line[$i]['out_date'],5);
					$data[$j]=$line[$i]['su'];	
					$j++;
					$su=$line[$i]['su'];
				}
				$tmp_hdy_date = '';
				$tmp_hy = array();
				$hy=0;

			}else{
				$tmp_hy[$hy] = $line[$i]['out_date'];
				$hy++;
				if ($tmp_hdy_date == '')  //如果之前不是假日的話
				{					
					$tmp_hdy_date = $line[($i-1)]['out_date'];			
				}
				
			}					
		}
	
		if ($j == 1)
		{
			$data[$j] = $data[0];
			$x_ary[$j] =$x_ary[0];
		}	
		
		for($m=0; $m<sizeof($main_date); $m++)
		{
			$dd_mk = 0;
			for($n=0; $n<sizeof($x_ary); $n++)
			{
				if($main_date[$m] == $x_ary[$n])
				{
					$ord_data[$m] = $data[$n];
					$dd_mk = 1;
					break;
				}				
			}
			if($dd_mk == 0)$ord_data[$m] = '';
		}
		
		
		
		$graphic_title = "   Line : ".$PL[$a]['line'];
		
		// Create the graph. These two calls are always required
		$graph = new Graph(600,160,"auto");    
		$graph->SetScale("textlin");
		
		// Adjust the margin
		$graph->img->SetMargin(60,20,30,40);    
		$graph->SetShadow();
		
		// Create the linear plot
		$lineplot=new LinePlot($ord_data);
		$lineplot->mark->SetType(MARK_UTRIANGLE);
		$lineplot ->value->SetFont( FF_FONT1, FS_BOLD); 
		$lineplot ->value->SetFormat( " %d");
		$lineplot->value->show();

		// Add the plot to the graph
		$graph->Add($lineplot);

		$graph->title->Set($graphic_title);
		$graph->yaxis->title->Set("SU");

		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

		// Setup X-scale
		$graph->xaxis->SetTickLabels($main_date);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
		$graph->xaxis->SetLabelAngle(45);

		$lineplot->SetColor("blue");
		$lineplot->SetWeight(2);

		// Display the graph
		$op['echart'][($a+1)]['pic'] = $graph->Stroke('picture/saw'.($a+1).'.png');
		$op['echart'][($a+1)]['pic'] = 'picture/saw'.($a+1).'.png';
		$op['echart'][($a+1)]['id'] = ($a+1);
	}
	}
	

	page_display($op, '028', $TPL_SAW_ORD_PIC);

				
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_message_send":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "saw_line_pic":
    check_authority('028',"view");
   
    if(substr($PHP_date,8,2) == '01') $PHP_date = increceDaysInDate($PHP_date,-1);
    $s_date = increceDaysInDate($PHP_date,-30);
		$s_date = substr($s_date,0,7)."-01";
		$e_date = substr($PHP_date,0,7)."-31";
 		$line = $monitor->get_line_det($PHP_line,$PHP_fty,$s_date,$e_date);
 	
//計算訂單平均生產日 		
 		$n_field = "count(saw_out_put.out_date) as out_date";
 		$where_str = " AND saw_out_put.saw_line = '".$PHP_line."' AND saw_out_put.saw_fty= '".$PHP_fty."'";
 		$where_str .=" AND saw_out_put.out_date >= '".$s_date."' AND saw_out_put.out_date <='".$e_date."'";
 		$where_str .=" AND s_order.status > 8 GROUP BY s_order.order_num";
 		$ord_recs = $monitor-> get_with_ord($n_field,$where_str);
 		$pdt_out_days = 0;
 		for($i=0; $i<sizeof($ord_recs); $i++)
 		{
 			$pdt_out_days +=  $ord_recs[$i]['out_date'];
 		}
 		$op['avg_ord'] = $pdt_out_days / sizeof($ord_recs);
 		
		$op['line'] = $PHP_line;
//畫圖


$years='';
$max_su=$line[0]['su'];
	for ($i=1; $i < sizeof($line); $i++)
	{
		if ($line[$i]['su'] > $max_su) $max_su = $line[$i]['su'];			
	}
	
  if ($line[0]['holiday'] <> 0)
  {
		$j=1;
		$x_ary[0]=substr($line[0]['out_date'],5);
		$data[0]='';	
		$total_day=0;  	
		$style_num = '';
		$k=-1;
		
  }else{
		$j=2;
		$tmp_day=increceDaysInDate($line[0]['out_date'],-1); //預留空間
//		$x_ary[0]=substr($tmp_day,5);
		$x_ary[0]= '';
		$data[0]='';			
		
		$x_ary[1]=substr($line[0]['out_date'],5);
		$data[1]=$line[0]['su'];	
		$total_day=1;
		
		$ord_num=$tmp_ie=$style_num='';
		$where_str = " AND saw_fty = '".$PHP_fty."' AND saw_line='".$PHP_line."' AND out_date='".$line[0]['out_date']."' ORDER BY ord_num";
		$ords = $monitor->get_fields_saw('ord_num',$where_str);
		$ord_qty = $monitor->get_fields_saw('qty',$where_str);
		$tmp_big_qty = 0;
		$tmp_ord_num='';
		for ($od=0; $od<sizeof($ords); $od++) 
		{
			if($tmp_big_qty < $ord_qty[$od])
			{
				$tmp_big_qty = $ord_qty[$od];
				$tmp_ord_num = $ords[$od];
			}
		}
			
		$tmp_ie = $order->get_field_value('ie1','',$tmp_ord_num);	
		$style_num= $order->get_field_value('style_num','',$tmp_ord_num); //取得訂單style_num
		for ($od=0; $od<sizeof($ords); $od++) 
		{
			$ord_style = $order->get_field_value('style_num','',$ords[$od]); //取得訂單style_num
			if($style_num == $ord_style)	$ord_num=$ord_num.$ords[$od]."(".$ord_style.")<br>";
		}


		$ord_num=substr($ord_num,0,-4);		
		$area_ary[$style_num][] = $max_su;
		$style_ary[0]=$style_num;
		$ord_ary[0]=$ord_num;
		$ie_ary[0]=$tmp_ie;
		$last_date = $line[0]['out_date'];
		$k=0;
	}

		$hy=0; //holiday的計數
		$tmp_hdy_date = '';		//holiday的前一日
		$tmp_hy = array();  //holiday的陣列內容
		$total_su=$line[0]['su'];
		


		$color_ary = array('#F0FFDD','#DEFEB4','#C8FE80','#ACFF3D','#CBDEB1','#B4D983','#9CD54F','#83D11C','#96A87E','#73A431','#6B755E','#5C7042','#446517','#3D4730'
											,'#F5E9FF','#DBB0FF','#B969FE','#CAB2DE','#AC76DB','#8E35DA','#AB9AB9','#9368B7','#7A33B7','#8A7A98','#7A5995','#5C238C','#5E5467','#462761'
											,'#FCD2D6','#D7D7D7','#D7B8CB','#D1ECEB','#C9B8F0','#B8CCF0','#FFBAB8','#B8F0BA','#FDF998','#B4FD98','#FDBE98');
		
		
		


		for ($i=1; $i < sizeof($line); $i++)
		{

			if ($line[$i]['holiday'] == 0) //如果不是假日時才記錄數量日期
			{
				$tmp_num=$tmp_ie=$tmp_style='';
				$last_date = $line[$i]['out_date'];
				$where_str =  " AND saw_fty = '".$PHP_fty."' AND saw_line='".$PHP_line."' AND out_date='".$line[$i]['out_date']."' ORDER BY ord_num";
				$ords = $monitor->get_fields_saw('ord_num',$where_str);
				$ord_qty = $monitor->get_fields_saw('qty',$where_str); //取得order的當日產量
				$tmp_big_qty = 0;
				$tmp_ord_num='';
				for ($od=0; $od<sizeof($ords); $od++)   //找出產量最大值的訂單
				{
					if($tmp_big_qty < $ord_qty[$od])
					{
						$tmp_big_qty = $ord_qty[$od];
						$tmp_ord_num = $ords[$od];
					}
				}
			
				$tmp_ie = $order->get_field_value('ie1','',$tmp_ord_num);	
				$tmp_style= $order->get_field_value('style_num','',$tmp_ord_num); //取得訂單style_num

				for ($od=0; $od<sizeof($ords); $od++) //比較當日訂單的style_num是否與最大值訂單的相同
				{
					$ord_style = $order->get_field_value('style_num','',$ords[$od]); //取得訂單style_num
					if($tmp_style == $ord_style)	$tmp_num=$tmp_num.$ords[$od]."(".$ord_style.")<br>";
				}
				$tmp_num=substr($tmp_num,0,-4);	
					
				if ($style_num <> $tmp_style) //style區塊記錄 ,不同style時就新增一筆
				{
					
					
					if($style_num) $area_ary[$style_num][] = $max_su;
					$style_num = $tmp_style;
					
					if (!isset($area_ary[$style_num][0]))
					{
						$k++;					
						$ord_ary[$k]=$tmp_num;
						$ie_ary[$k]=$tmp_ie;
						$style_ary[$k]=$style_num;
					}
					for ($z=0; $z<($j-1);$z++)
					{
						if (!isset($area_ary[$style_num][$z])){$area_ary[$style_num][$z] = '';}
					}
				}else{  //相同style時,將不同的訂單資訊加入
					$tmp_ary_ord =explode("<br>",$tmp_num);
					$ta2=$k;
					for ($ta1=0; $ta1<=$k; $ta1++)	 //取得style所在的陣列位置,存在$ta2
					{
						if($style_ary[$ta1] == $style_num )$ta2=$ta1;
					}
					for ($ta=0; $ta<sizeof($tmp_ary_ord); $ta++)  //將目前的ord_num加入ord_ary[$ta2]中
					{
						if( $ord_ary[$ta2] && (!strstr($ord_ary[$ta2],$tmp_ary_ord[$ta])))$ord_ary[$ta2]=$ord_ary[$ta2]."<br>".$tmp_ary_ord[$ta];
					}
					
				}


				$total_su += $line[$i]['su'];
				$total_day++;
				if ($tmp_hdy_date == '') //如果前一天是假日的話必需由假日向前扣一天
				{
					$c_day=countDays ($line[($i-1)]['out_date'],$line[$i]['out_date']);					
				}else{
					$c_day=countDays ($tmp_hdy_date,$line[$i]['out_date']);
				}
					$cut_day = $c_day - $hy; 
				if ($cut_day > 1)
				{
					if ($tmp_hdy_date == '') $tmp_hdy_date = $line[($i-1)]['out_date'];
					$su=$line[$i]['su'] / $cut_day;
					$tmp_su=0;
					for ($x=1; $x < $c_day; $x++)
					{
						$tmp_hy_mk = 0;
						$tmp_day=increceDaysInDate($tmp_hdy_date,$x);
						for ($hh=0; $hh <sizeof($tmp_hy); $hh++)
						{
							if ($tmp_day == $tmp_hy[$hh]) $tmp_hy_mk = 1;
						}
						if ($tmp_hy_mk == 0)
						{
							$x_ary[$j] = substr($tmp_day,5);
							$data[$j]=$su;
							$tmp_su=$tmp_su+$su;
							$area_ary[$style_num][] = $max_su;
							$j++;							
						}						
					}
					$tmp_day=increceDaysInDate($tmp_hdy_date,$c_day);
					$x_ary[$j] = substr($tmp_day,5);
					$su=$line[$i]['su']-$tmp_su;
					$data[$j]=$su;
					$area_ary[$style_num][] = $max_su;
					$j++;
				}else{				
					$x_ary[$j] = substr($line[$i]['out_date'],5);
					$data[$j]=$line[$i]['su'];					
					$j++;
					$area_ary[$style_num][] = $max_su;
					$su=$line[$i]['su'];
				}
				$tmp_hdy_date = '';
				$tmp_hy = array();
				$hy=0;

			}else{
				
				$tmp_hy[$hy] = $line[$i]['out_date'];
				$hy++;
				if ($tmp_hdy_date == '')  //如果之前不是假日的話
				{					
					$tmp_hdy_date = $line[($i-1)]['out_date'];			
				}
			}					
		}
		$area_ary[$style_num][] = $max_su;
		if ($j == 1)
		{
			$data[$j] = $data[0];
			$x_ary[$j] =$x_ary[0];
		}
		

		
	
		//&#164;&#1956;J graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		include ($config['root_dir']."/lib/src/jpgraph_bar.php");
		$graphic_title = "Production-Line : ".$PHP_line." FACTORY : ".$PHP_fty;
		
		// Create the graph. These two calls are always required
		$graph = new Graph(600,300,"auto");    
		$graph->SetScale("textlin");
		
		// Adjust the margin
		$graph->img->SetMargin(60,20,30,40);    
		$graph->SetShadow();
		
	if(!strstr($PHP_line,'contract'))
	{
		for ($i=0; $i<=$k; $i++)
		{
			$plot_ord = $i."polt";
			
			$$plot_ord = new LinePlot($area_ary[$style_ary[$i]]);
			$$plot_ord->SetFillColor($color_ary[$i]);
			$graph->Add($$plot_ord);
			$op['polt_det'][$i]['order'] = $ord_ary[$i];
			$op['polt_det'][$i]['ord_num'] = substr($ord_ary[$i],0,10);

			$op['polt_det'][$i]['color'] = $color_ary[$i];	
			$op['polt_det'][$i]['ie'] = $ie_ary[$i];		
		}
	}


		// Create the linear plot
		$lineplot=new LinePlot($data);
		$lineplot->mark->SetType(MARK_UTRIANGLE);
		$lineplot ->value->SetFont( FF_FONT1, FS_BOLD); 
		$lineplot ->value->SetFormat( " %d");
		$lineplot->value->show();

		// Add the plot to the graph
		$graph->Add($lineplot);
		
				




		$graph->title->Set($graphic_title);
//		$graph->xaxis->title->Set("Out-Put Date");
		$graph->yaxis->title->Set("SU");

		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
//		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

		// Setup X-scale
		$graph->xaxis->SetTickLabels($x_ary);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
		$graph->xaxis->SetLabelAngle(45);

		$lineplot->SetColor("blue");
		$lineplot->SetWeight(2);

		// Display the graph
		$op['echart'][0]['pic'] = $graph->Stroke('picture/saw0.png');
		$op['echart'][0]['pic'] = 'picture/saw0.png';
		$op['echart'][0]['id'] = 0;
		$op['total_su'] = $total_su;
		$op['avg_su'] = $total_su / $total_day;
		
		$tmp = $ord_day = 0;
		foreach ($area_ary as $key => $value) 
		{
			
			$ord_day += (sizeof($area_ary[$key]) - $tmp);
			$tmp = sizeof($area_ary[$key]);
		}
//		$op['avg_ord'] = $ord_day / sizeof($area_ary );
		
		$op['year_title'] ="($s_date  ~  ".$last_date.")";
		

	page_display($op, '028', $TPL_SAW_LINE_PIC);

	break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dm_daily_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "dm_view":
 	check_authority('028',"view");
		$op['out_date'] = $monitor->saw_rpt_date(1);
// CUTTING資料		
		$argv = array(	"ord_num"	=>  '',	"factory"	=>	$SCH_fty);
		$op['cut_su_sum'] = $op['cut_sum'] = 0;
		for ($j=0; $j<sizeof($op['out_date']); $j++)
		{			
			$argv['k_date'] = $op['out_date'][$j]['out_date'];
			$op['cut'][$j]['su'] = $op['cut'][$j]['qty'] = 0;
			
			$cut_rec = $cutting->search($argv,1);
			for($x=0;$x<sizeof($cut_rec);$x++)  $op['cut'][$j]['qty'] += $cut_rec[$x]['qty']; // 計算 今日生產總數量			
			$op['cut_sum'] += $op['cut'][$j]['qty'];

			for($x=0;$x<sizeof($cut_rec);$x++)  $op['cut'][$j]['su'] += $cut_rec[$x]['su']; // 計算 今日生產總數量			
			$op['cut_su_sum'] += $op['cut'][$j]['su'];

		}	
	
		
		$line = $monitor->get_rpt_line($SCH_fty,'i');
		$dm = $monitor->saw_rpt_search(1,'i');
		
		$op['saw'] =array();
		$out_date = '';				//建立顯示的陣列
	
		$date_ttl['line'] = "TOTAL";
		$date_ttl['sum_qty'] = 0;
		$date_ttl['sum_su'] = 0;	
		
		$gen_ttl['line'] = "G-TTL";
		$gen_ttl['sum_qty'] = 0;
		$gen_ttl['sum_su'] = 0;	
			
		if ($SCH_line) 
		{
			
			for ($i=0; $i<sizeof($line); $i++)
			{
				if ($line[$i]['line'] == $SCH_line)$line_tmp[] =$line[$i];
			}
			$line = $line_tmp;
		}
		$l=0;
		$hdy=0;
		for ($j=0; $j<sizeof($op['out_date']); $j++)
		{			
				$date_ary[$op['out_date'][$j]['out_date']] = $j;
				$date_ttl['qty'][$j] =  0; //累計該線的數量總數
				$date_ttl['su'][$j] =  0;
				$date_ttl['hdy'][$j] =  $op['out_date'][$j]['holiday'];				

				$gen_ary[$op['out_date'][$j]['out_date']] = $j;
				$gen_ttl['qty'][$j] =  0; //累計該線的數量總數
				$gen_ttl['su'][$j] =  0;
				$gen_ttl['hdy'][$j] =  $op['out_date'][$j]['holiday'];		
		}
		
		$k=1;
			for ($i=0; $i<sizeof($line); $i++)
			{
				//echo $line[$i]['line']."<BR>";
				$op['saw'][$k]['line'] = $line[$i]['line'];		
				$op['saw'][$k]['day_avg'] = $line[$i]['day_avg'];	
				$op['saw'][$k]['sum_qty'] = 0;
				$op['saw'][$k]['sum_su'] = 0;
				for ($l=0; $l<sizeof($op['out_date']); $l++) 
				{
					$op['saw'][$k]['qty'][$l] = 0;
					$op['saw'][$k]['su'][$l] = 0;
					$op['saw'][$k]['hdy'][$l] = $op['out_date'][$l]['holiday'];
				}
				for ($j=0; $j<sizeof($dm); $j++)
				{
					if ($line[$i]['line'] == $dm[$j]['line'] &&  $line[$i]['fty'] = $dm[$j]['fty']) 
					{
						$op['saw'][$k]['qty'][$date_ary[$dm[$j]['out_date']]] += $dm[$j]['qty'];
						$op['saw'][$k]['su'][$date_ary[$dm[$j]['out_date']]]  += $dm[$j]['su'];
						//$op['saw'][$k]['hdy'][$date_ary[$dm[$j]['out_date']]]  = $dm[$j]['holiday'];
						$op['saw'][$k]['sum_qty'] += $dm[$j]['qty'];	//累計該日總數
						$op['saw'][$k]['sum_su'] += $dm[$j]['su'];
					
						$date_ttl['qty'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['qty']; //累計該線的數量總數
						$date_ttl['su'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['su'];

						$gen_ttl['qty'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['qty']; //累計該線的數量總數
						$gen_ttl['su'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['su'];
						
						if ($date_ttl['qty'][$date_ary[$dm[$j]['out_date']]] > 0) 
						{
						//	$date_ttl['hdy'][$date_ary[$dm[$j]['out_date']]] = 0;
						}
						
						$date_ttl['sum_qty'] += $dm[$j]['qty'];	//累計全部的總數
						$date_ttl['sum_su'] += $dm[$j]['su'];
						$gen_ttl['sum_qty'] += $dm[$j]['qty'];	//累計全部的總數
						$gen_ttl['sum_su'] += $dm[$j]['su'];

					}			
				}
				$op['saw'][$k]['sort'] = $op['saw'][$k]['sum_su'];
				$k++;
			}			
			
			$op['saw'][0] = $date_ttl;
		  $op['saw'][0]['sort'] = $op['saw'][0]['sum_su'];
		  $op['saw'] = bubble_sort($op['saw']);
	

//子工廠 -- LA
	if($SCH_fty == 'LY')
	{
		$line = $monitor->get_rpt_line($SCH_fty,'i','LA');
		$dm = $monitor->saw_rpt_search(1,'i','LA');
		
		$op['s_la'] =array();
		$out_date = '';				//建立顯示的陣列

		$date_ttl['line'] = "TOTAL";
		$date_ttl['sum_qty'] = 0;
		$date_ttl['sum_su'] = 0;		
		if ($SCH_line) 
		{
			
			for ($i=0; $i<sizeof($line); $i++)
			{
				if ($line[$i]['line'] == $SCH_line)$line_tmp[] =$line[$i];
			}
			$line = $line_tmp;
		}
		$l=0;
		$hdy=0;
		for ($j=0; $j<sizeof($op['out_date']); $j++)
		{			
			$date_ary[$op['out_date'][$j]['out_date']] = $j;
			$date_ttl['qty'][$j] =  0; //累計該線的數量總數
			$date_ttl['su'][$j] =  0;
			$date_ttl['hdy'][$j] =  $op['out_date'][$j]['holiday'];				
		}
		
		$k=1;
			for ($i=0; $i<sizeof($line); $i++)
			{
				//echo $line[$i]['line']."<BR>";
				$op['s_la'][$k]['line'] = $line[$i]['line'];	
				$op['s_la'][$k]['day_avg'] = $line[$i]['day_avg'];		
				$op['s_la'][$k]['sum_qty'] = 0;
				$op['s_la'][$k]['sum_su'] = 0;
				for ($l=0; $l<sizeof($op['out_date']); $l++) 
				{
					$op['s_la'][$k]['qty'][$l] = 0;
					$op['s_la'][$k]['su'][$l] = 0;
					$op['s_la'][$k]['hdy'][$l] = $op['out_date'][$l]['holiday'];
				}
				for ($j=0; $j<sizeof($dm); $j++)
				{
					if ($line[$i]['line'] == $dm[$j]['line'] &&  $line[$i]['fty'] = $dm[$j]['fty']) 
					{
						$op['s_la'][$k]['qty'][$date_ary[$dm[$j]['out_date']]] += $dm[$j]['qty'];
						$op['s_la'][$k]['su'][$date_ary[$dm[$j]['out_date']]]  += $dm[$j]['su'];
						//$op['s_la'][$k]['hdy'][$date_ary[$dm[$j]['out_date']]]  = $dm[$j]['holiday'];
						$op['s_la'][$k]['sum_qty'] += $dm[$j]['qty'];	//累計該日總數
						$op['s_la'][$k]['sum_su'] += $dm[$j]['su'];
					
						$date_ttl['qty'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['qty']; //累計該線的數量總數
						$date_ttl['su'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['su'];

						$gen_ttl['qty'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['qty']; //累計該線的數量總數
						$gen_ttl['su'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['su'];
						
						if ($date_ttl['qty'][$date_ary[$dm[$j]['out_date']]] > 0) 
						{
						//	$date_ttl['hdy'][$date_ary[$dm[$j]['out_date']]] = 0;
						}
						
						$date_ttl['sum_qty'] += $dm[$j]['qty'];	//累計全部的總數
						$date_ttl['sum_su'] += $dm[$j]['su'];
						
						$gen_ttl['sum_qty'] += $dm[$j]['qty'];	//累計全部的總數
						$gen_ttl['sum_su'] += $dm[$j]['su'];

					}			
				}
				$op['s_la'][$k]['sort'] = $op['s_la'][$k]['sum_su'];
				$k++;
			}			
			
			$op['s_la'][0] = $date_ttl;
		  $op['s_la'][0]['sort'] = $op['s_la'][0]['sum_su'];
		  $op['s_la'] = bubble_sort($op['s_la']);
		}
		
	
	// print_r($op['g_ttl']);
	
//外發	
		$line = $monitor->get_rpt_line($SCH_fty,'s');
		$dm = $monitor->saw_rpt_search(1,'s');
		$op['sc_saw'] =array();
		$out_date = '';				//建立顯示的陣列

		$date_ttl['line'] = "TOTAL";
		$date_ttl['sum_qty'] = 0;
		$date_ttl['sum_su'] = 0;
		
		if ($SCH_line) 
		{
			
			for ($i=0; $i<sizeof($line); $i++)
			{
				if ($line[$i]['line'] == $SCH_line)$line_tmp[] =$line[$i];
			}
			$line = $line_tmp;
		}
		$l=0;
		$hdy=0;
		for ($j=0; $j<sizeof($op['out_date']); $j++)
		{			
				$date_ttl['qty'][$j] =  0; //累計該線的數量總數
				$date_ttl['su'][$j] =  0;
		}		

		$k=1;
			for ($i=0; $i<sizeof($line); $i++)
			{
				$op['sc_saw'][$k]['line'] = $line[$i]['line'];
				$op['sc_saw'][$k]['sum_qty'] = 0;
				$op['sc_saw'][$k]['sum_su'] = 0;
				for ($l=0; $l<sizeof($op['out_date']); $l++) 
				{
					$op['sc_saw'][$k]['qty'][$l] = 0;
					$op['sc_saw'][$k]['su'][$l] = 0;

					$op['sc_saw'][$k]['hdy'][$l] = $op['out_date'][$l]['holiday'];
					//echo $op['out_date'][$l]['out_date']."===>".$op['out_date'][$l]['holiday']."<BR>";
				}
				for ($j=0; $j<sizeof($dm); $j++)
				{
					if ($line[$i]['line'] == $dm[$j]['line'] &&  $line[$i]['fty'] = $dm[$j]['fty']) 
					{
						$op['sc_saw'][$k]['qty'][$date_ary[$dm[$j]['out_date']]] += $dm[$j]['qty'];
						$op['sc_saw'][$k]['su'][$date_ary[$dm[$j]['out_date']]]  += $dm[$j]['su'];
//						$op['sc_saw'][$k]['hdy'][$date_ary[$dm[$j]['out_date']]]  = $dm[$j]['holiday'];
						$op['sc_saw'][$k]['sum_qty'] += $dm[$j]['qty'];	//累計該日總數
						$op['sc_saw'][$k]['sum_su'] += $dm[$j]['su'];
					
						$date_ttl['qty'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['qty']; //累計該線的數量總數
						$date_ttl['su'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['su'];
						
						$gen_ttl['qty'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['qty']; //累計該線的數量總數
						$gen_ttl['su'][$date_ary[$dm[$j]['out_date']]] +=  $dm[$j]['su'];
						
						if ($date_ttl['qty'][$date_ary[$dm[$j]['out_date']]] > 0) 
						{
	//						$date_ttl['hdy'][$date_ary[$dm[$j]['out_date']]] = 0;
						}
						
						$date_ttl['sum_qty'] += $dm[$j]['qty'];	//累計全部的總數
						$date_ttl['sum_su'] += $dm[$j]['su'];
						
						$gen_ttl['sum_qty'] += $dm[$j]['qty'];	//累計全部的總數
						$gen_ttl['sum_su'] += $dm[$j]['su'];						

					}			
				}
				$k++;
			}			
			
			$op['sc_saw'][0] = $date_ttl;
	
			$op['g_ttl'] = $gen_ttl;
		
		
		
		
		
		$op['line'] = $line;
		$op['title'] ='';
		if($SCH_fty) $op['title'] = 'Factory : '.$SCH_fty;
		if($SCH_ord) $op['title'] = $op['title'].' ORDER : '.$SCH_ord;
		if($SCH_cust) $op['title'] = $op['title'].' Cust. : '.$SCH_cust;
		if($SCH_style) $op['title'] = $op['title'].' Style : '.$SCH_style;
		if($SCH_line) $op['title'] = $op['title'].' Production Line : '.$SCH_line;
		$op['title_d'] = "Date : From ".$SCH_str." To ".$SCH_end;

		$op['back_str'] = "&SCH_fty=".$SCH_fty."&SCH_line=".$SCH_line."&SCH_ord=".$SCH_ord."&SCH_str=".$SCH_str."&SCH_end=".$SCH_end."&SCH_style=".$SCH_style."&SCH_cust=".$SCH_cust;

		
$op['fty'] = $SCH_fty;
  if ($PHP_excel == 0)
  {
		if($SCH_su&&$SCH_pc)		
		{
			page_display($op, '028', $TPL_SAW_REPORT);
		}else if($SCH_pc){
			page_display($op, '028', $TPL_SAW_REPORT_PC);
		}else{
			page_display($op, '028', $TPL_SAW_REPORT_SU);
		}		
		break;
	}else{
	 require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
	 require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");
	  function HeaderingExcel($filename) {
		  header("Content-type: application/vnd.ms-excel");
		  header("Content-Disposition: attachment; filename=$filename" );
		  header("Expires: 0");
		  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		  header("Pragma: public");
		  }

	  // HTTP headers
	  HeaderingExcel('saw_daily_rpt.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('MONITOR DAILY OUT-PUT REPORT');

		$now = $GLOBALS['THIS_TIME'];

	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern(1);
	  $formatot->set_fg_color('navy');
	  $formatot->set_num_format(4);
//	   $formatot->setMerge(2);
	   
	  $f3 =& $workbook->add_format(); //置右
	  $f3->set_size(10);
	  $f3->set_align('right');
	  $f3->set_num_format(3);

	  $f4 =& $workbook->add_format(); //置右
	  $f4->set_size(10);
	  $f4->set_align('right');
	  $f4->set_color('navy');
	  $f4->set_num_format(3);


	  $f1 =& $workbook->add_format(); //置右
	  $f1->set_size(10);
	  $f1->set_align('right');

	  $f2 =& $workbook->add_format(); //置右
	  $f2->set_size(10);
	  $f2->set_align('right');
	  $f2->set_color('navy');
	  
	  $f5 =& $workbook->add_format(); //置右
	  $f5->set_size(10);
	  $f5->set_align('right');
		$f5->set_fg_color('grey');
		$f5->set_bold();
//		$f5->set_pattern(1);
	  $f5->set_num_format(3);
	  $f5->set_right(-10);
		
	  $f6 =& $workbook->add_format(); //置右
	  $f6->set_size(10);
	  $f6->set_align('right');
	  $f6->set_color('navy');
	  $f6->set_bold();
//	  $f6->set_pattern(1);
	  $f6->set_num_format(3);
	  $f6->set_right(-10);
	  
	  $f7 =& $workbook->add_format(); //置右
	  $f7->set_size(10);
	  $f7->set_align('right');
	  $f7->set_bold();

	  $f8 =& $workbook->add_format(); //置右
	  $f8->set_size(10);
	  $f8->set_align('right');
	  $f8->set_color('navy');
	  $f8->set_bold();
	  	  
	  $ftitle =& $workbook->add_format(); //置右
	  $ftitle->set_size(13);
	  $ftitle->set_bold();

	  $fbk =& $workbook->add_format(); //置右
	  $fbk->set_size(1);
	  $fbk->set_bold();

	  $f9 =& $workbook->add_format(); //置右
	  $f9->set_size(10);
	  $f9->set_align('right');
		$f9->set_fg_color('grey');
		$f9->set_bold();
	  $f9->set_num_format(3);
	  $f9->set_bottom(-10);
		
	  $f10 =& $workbook->add_format(); //置右
	  $f10->set_size(10);
	  $f10->set_align('right');
	  $f10->set_color('navy');
	  $f10->set_bold();
	  $f10->set_num_format(3);
	  $f10->set_bottom(-10);

	  $f11 =& $workbook->add_format(); //置右
	  $f11->set_size(10);
	  $f11->set_align('right');
	  $f11->set_bold();
	  $f11->set_num_format(3);
	  $f11->set_bottom(-10);
	  $f11->set_right(-10);

	  $f12 =& $workbook->add_format(); //置右
	  $f12->set_size(10);
	  $f12->set_align('right');
	  $f12->set_color('navy');
	  $f12->set_bold();
	  $f12->set_num_format(3);
	  $f12->set_bottom(-10);
	  $f12->set_right(-10);

	  
		$k=2;
		$worksheet1->set_column(0,0,13);
		$worksheet1->set_column(0,1,10);
	  for ($i=0; $i< sizeof($op['out_date']); $i++)  //設格子
	  {
	  	$worksheet1->set_column(0,$k,7);
	  	$k++;
	  }

	  
	  $worksheet1->write_string(0,0,' DAILY OUT-PUT REPORT [ '.$op['title']."  /  ".$op['title_d']."]    print date :".$TODAY,$ftitle);
	  
	  
	  
	  $worksheet1->write_string(1,0,'LINE',$formatot);
	  $worksheet1->write_string(1,1,'Total',$formatot);
  	$k=2;
	  for ($i=0; $i< sizeof($op['out_date']); $i++)  //標題
	  {
	  	$worksheet1->write_string(1,$k,substr($op['out_date'][$i]['out_date'],5),$formatot);
	  	$k++;
	  }


	 //內容
	 	$k=2;
	  for ($i=0; $i< sizeof($op['saw']); $i++)
	  {
	  	
		 if ($SCH_pc == 1)
		 {
	  	if ($i == 0)
	  	{
	  		$worksheet1->write_string($k,0,$op['saw'][$i]['line']."[pc.]",$f9);
  			$worksheet1->write_number($k,1,$op['saw'][$i]['sum_qty'],$f11);
				$l=2;
			}else{
	  		$worksheet1->write_string($k,0,$op['saw'][$i]['line']."[pc.]");
  			$worksheet1->write_number($k,1,$op['saw'][$i]['sum_qty'],$f5);
				$l=2;
			}

	  	for($j=0; $j<sizeof($op['saw'][$i]['qty']); $j++)
	  	{
	  		$worksheet1->write_number($k,$l,$op['saw'][$i]['hdy'][$j],$f5);

	  		if ($op['saw'][$i]['hdy'][$j] == 0)		
	  		{
					if ($op['saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_number($k,$l,$op['saw'][$i]['qty'][$j],$f9);
					}else{
	 			 		$worksheet1->write_number($k,$l,$op['saw'][$i]['qty'][$j],$f3);	  				
	  			}
	  			$l++;
	  		}elseif ($op['saw'][$i]['hdy'][$j] == 1){
					if ($op['saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_string($k,$l,"H.Day",$f9);
					}else{
	 			 		$worksheet1->write_string($k,$l,"H.Day",$f1);
	  			}
	  			$l++;	
	  		}elseif($op['saw'][$i]['hdy'][$j] == 3){
	  			$worksheet1->write_string($k,$l,"day-off",$f2);  		
	  		}else{
	  			$worksheet1->write_string($k,$l," ");
	  			$l++;
	  		}
	  	}
	  	$k++;
	   }
		 if ($SCH_su == 1)
		 {	  	
	  	if ($i == 0)
	  	{
	  	$worksheet1->write_string($k,0,$op['saw'][$i]['line']."[su.]",$f10);
  		$worksheet1->write_number($k,1,$op['saw'][$i]['sum_su'],$f12);
			$l=2;
			}else{
	  	$worksheet1->write_string($k,0,$op['saw'][$i]['line']."[su.]");
  		$worksheet1->write_number($k,1,$op['saw'][$i]['sum_su'],$f6);
			$l=2;
		}




	  	for($j=0; $j<sizeof($op['saw'][$i]['su']); $j++)
	  	{
	  		$worksheet1->write_number($k,$l,$op['saw'][$i]['hdy'][$j],$f6);

	  		if ($op['saw'][$i]['hdy'][$j] == 0)
	  		{
					if ($op['saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_number($k,$l,$op['saw'][$i]['su'][$j],$f10);
					}else{
	 			 		$worksheet1->write_number($k,$l,$op['saw'][$i]['su'][$j],$f4);	  				
	  			}
	  			$l++;
	  		}elseif ($op['saw'][$i]['hdy'][$j] == 1){
	  			
	  			
					if ($op['saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_string($k,$l,"H.Day",$f10);
					}else{
	 			 		$worksheet1->write_string($k,$l,"H.Day",$f2);
	  			}
	  			$l++;
	  		}elseif($op['saw'][$i]['hdy'][$j] == 3){
	  			$worksheet1->write_string($k,$l,"day-off",$f2);
	  		}else{
	  			$worksheet1->write_string($k,$l," ");
	  			$l++;
	  		}
	  	}
	  	$k++;
	   }
	  }
	  
	  
//外發	  
	  for ($i=0; $i< sizeof($op['sc_saw']); $i++)
	  {
	  	
		 if ($SCH_pc == 1)
		 {
	  	if ($i == 0)
	  	{
	  		$worksheet1->write_string($k,0,$op['sc_saw'][$i]['line']."[pc.]",$f9);
  			$worksheet1->write_number($k,1,$op['sc_saw'][$i]['sum_qty'],$f11);
				$l=2;
			}else{
	  		$worksheet1->write_string($k,0,$op['sc_saw'][$i]['line']."[pc.]");
  			$worksheet1->write_number($k,1,$op['sc_saw'][$i]['sum_qty'],$f5);
				$l=2;
			}

	  	for($j=0; $j<sizeof($op['sc_saw'][$i]['qty']); $j++)
	  	{
	  		$worksheet1->write_number($k,$l,$op['sc_saw'][$i]['hdy'][$j],$f5);

	  		if ($op['sc_saw'][$i]['hdy'][$j] == 0)		
	  		{
					if ($op['sc_saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_number($k,$l,$op['sc_saw'][$i]['qty'][$j],$f9);
					}else{
	 			 		$worksheet1->write_number($k,$l,$op['sc_saw'][$i]['qty'][$j],$f3);	  				
	  			}
	  			$l++;
	  		}elseif ($op['sc_saw'][$i]['hdy'][$j] == 1){
					if ($op['sc_saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_string($k,$l,"H.Day",$f9);
					}else{
	 			 		$worksheet1->write_string($k,$l,"H.Day",$f1);
	  			}
	  			$l++;	
	  		}elseif($op['sc_saw'][$i]['hdy'][$j] == 3){
	  			$worksheet1->write_string($k,$l,"day-off",$f2);  		
	  		}else{
	  			$worksheet1->write_string($k,$l," ");
	  			$l++;
	  		}
	  	}
	  	$k++;
	   }
		 if ($SCH_su == 1)
		 {	  	
	  	if ($i == 0)
	  	{
	  	$worksheet1->write_string($k,0,$op['sc_saw'][$i]['line']."[su.]",$f10);
  		$worksheet1->write_number($k,1,$op['sc_saw'][$i]['sum_su'],$f12);
			$l=2;
			}else{
	  	$worksheet1->write_string($k,0,$op['sc_saw'][$i]['line']."[su.]");
  		$worksheet1->write_number($k,1,$op['sc_saw'][$i]['sum_su'],$f6);
			$l=2;
		}




	  	for($j=0; $j<sizeof($op['sc_saw'][$i]['su']); $j++)
	  	{
	  		$worksheet1->write_number($k,$l,$op['sc_saw'][$i]['hdy'][$j],$f6);

	  		if ($op['sc_saw'][$i]['hdy'][$j] == 0)
	  		{
					if ($op['sc_saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_number($k,$l,$op['sc_saw'][$i]['su'][$j],$f10);
					}else{
	 			 		$worksheet1->write_number($k,$l,$op['sc_saw'][$i]['su'][$j],$f4);	  				
	  			}
	  			$l++;
	  		}elseif ($op['sc_saw'][$i]['hdy'][$j] == 1){
	  			
	  			
					if ($op['sc_saw'][$i]['line'] == 'TOTAL')
					{
	  				$worksheet1->write_string($k,$l,"H.Day",$f10);
					}else{
	 			 		$worksheet1->write_string($k,$l,"H.Day",$f2);
	  			}
	  			$l++;
	  		}elseif($op['sc_saw'][$i]['hdy'][$j] == 3){
	  			$worksheet1->write_string($k,$l,"day-off",$f2);
	  		}else{
	  			$worksheet1->write_string($k,$l," ");
	  			$l++;
	  		}
	  	}
	  	$k++;
	   }
	  }	  
	  
	  
	  
	  
	  
	  
	  
	  
  	  $workbook->close();
	break;



	}
	
	
	
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "monitor_ord":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "monitor_ord":
 	check_authority('028',"view");
		$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');
		$op['LINE_select'] = $arry2->select($P_LINE,'','PHP_LINE','select','');

	page_display($op, '028', $TPL_MONITOR);
	break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "monitor":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "monitor_add":
 	check_authority('028',"view");
 		if (!$PHP_FTY || !$PHP_LINE || !$PHP_worker)
 		{
 			$op['FACTORY_select'] = $arry2->select($FACTORY,$PHP_FTY,'PHP_FTY','select','');
			$op['LINE_select'] = $arry2->select($P_LINE,$PHP_LINE,'PHP_LINE','select','');
			$op['worker'] = $PHP_worker;
			$op['msg'][]= "Please select Factory, Production Line and Worker.";
			page_display($op, '028', $TPL_MONITOR);
			break;
 		}
		$op['mon']['fty'] = $PHP_FTY;
		$op['mon']['line'] = $PHP_LINE;
		$op['mon']['worker'] = $PHP_worker;

	page_display($op, '028', $TPL_MONITOR_ADD);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_notify_mark":	 	JOB 53A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_monitor_add":
	
		check_authority('028',"view");	
		
		$parm = array (	'fty' 			=>	$PHP_fty,
										'line'			=>	$PHP_line,
										'worker'		=>	$PHP_worker,
										'ord_num'		=>	$PHP_order,
										'style'			=>	$PHP_style,
										'ie'				=>	$PHP_ie,
										'open_date'	=>	$PHP_open_date,
						);		
		$f1=$monitor->add($parm);	
		if ($f1)
		{	
			$op['mon'] = $parm;
			$op['mon']['id'] = $f1;
			$parm = array (	'mon_id' 			=>	$f1,
											'out_date'		=>	$PHP_out_date,
											'qty'					=>	$PHP_out_qty,
											'open_date'		=>	$PHP_open_date,
											'hdy'					=>  0,
							);
			if ($PHP_out_qty > 0) 
			{
				$f1=$monitor->add_det($parm);			
				$op['mon_det'][0] = $parm;
				$op['mon_det'][0]['su'] = $PHP_out_qty * $PHP_ie;
				$op['mon_det'][0]['sum_su'] = $op['mon_det'][0]['su'];
				$op['mon_det'][0]['sum_qty'] = $op['mon_det'][0]['qty'];
			}
		page_display($op, '028', $TPL_MONITOR_EDIT);
	}else{
			$op['mon'] = $parm;
			$op['mon']['id'] = $f1;
			$op['msg']= $monitor->msg->get(2);

			page_display($op, '028', $TPL_MONITOR_ADD);

	}
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_notify_mark":	 	JOB 53A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_mon_add_det":
	
		check_authority('028',"view");	
		$parm = array (	'mon_id' 			=>	$PHP_mon_id,
										'out_date'		=>	$PHP_out_date,
										'qty'					=>	$PHP_out_qty,
										'open_date'		=>	$PHP_open_date,
										'hdy'					=>	$PHP_holday,
						);
		$f1=$monitor->add_det($parm);
		$op = $monitor->get($PHP_mon_id);
		$op['back_str'] = $PHP_back_str;
		if ($f1)
		{
			$op['msg'][]	= "SUCCESS ADD Monitor Item"; 
		}else{
			$op['msg']= $monitor->msg->get(2);
		}
	page_display($op, '028', $TPL_MONITOR_EDIT);
	break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_notify_mark":	 	JOB 53A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_date_edit_ajx":
	
		check_authority('028',"view");	
		$parm = array (	'field_name' 	=>	'open_date',
										'field_value'	=>	$PHP_open_date,
										'id'					=>	$PHP_id,										
						);
		$f1=$monitor->update_field($parm);
		if ($f1)
		{			
			echo "SUCCESS EDIT OPEN DATE"; 
		}else{
			$op['msg']= $monitor->msg->get(2);
			echo $op['msg'][0];
		}
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_notify_mark":	 	JOB 53A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "labor_ajax":
	
		check_authority('028',"view");	
		$parm = array (	'field_name' 	=>	'labor',
										'field_value'	=>	$PHP_labor,
										'id'					=>	$PHP_id,										
						);
		$f1=$monitor->update_field($parm);
		$parm = array (	'field_name' 	=>	'labor_date',
										'field_value'	=>	$PHP_date,
										'id'					=>	$PHP_id,										
						);
		$f1=$monitor->update_field($parm);

		if ($f1)
		{
			$per_labor = $PHP_labor / $PHP_qty;
			$per_labor	= $monitor->count_labor($PHP_id,$PHP_date,$PHP_labor);
			
			echo "SUCCESS EDIT Labor for order # : ".$PHP_ord_num."|".$PHP_id."|"; 
			printf('%.2f',$per_labor);
			echo " ";
		}else{
			$op['msg']= $monitor->msg->get(2);
			echo $op['msg'][0];
		}
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "message_send":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "monitor_search":
 		check_authority('028',"view");
 		$PHP_LINE = str_replace('+','|',$PHP_LINE);

 		$op = $monitor->search();

 	 	for ($k =0; $k< sizeof($op['monitor']); $k++)
 		{
			if ($op['monitor'][$k]['labor'] > 0) $op['monitor'][$k]['per_lb']	= $monitor->count_labor($op['monitor'][$k]['id'],$op['monitor'][$k]['labor_date'],$op['monitor'][$k]['labor']);
			$op['monitor'][$k]['su'] =(int)($op['monitor'][$k]['sum_qty'] * $op['monitor'][$k]['ie']);
			$op['monitor'][$k]['fty_su'] =(int)($op['monitor'][$k]['sum_qty'] * 0.76);
			
 		}
 		$PHP_LINE = str_replace('+','|',$PHP_LINE);

 		$op['back_str'] = "&PHP_FTY=".$PHP_FTY."&PHP_LINE=".$PHP_LINE."&PHP_style=".$PHP_style."&PHP_ord=".$PHP_ord;
 		$op['msg']= $monitor->msg->get(2);
		page_display($op, '028', $TPL_MONITOR_LIST);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_message_send":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "monitor_view":
    check_authority('028',"view");
 		$op = $monitor->get($PHP_id);


//畫圖
		$years='';	$j=1;
		$x_ary[0]=substr($op['mon']['open_date'],5);
		$data[0]=0;	
		$yy=substr($op['mon']['open_date'],0,4);
	if (isset($op['mon_det'][0]['out_date']))
	{
		$cut_day=countDays ($op['mon']['open_date'],$op['mon_det'][0]['out_date']);
		for ($x=1; $x < $cut_day; $x++)
		{
			$tmp_day=increceDaysInDate($op['mon']['open_date'],$x);
			$x_ary[$j] = substr($tmp_day,5);
			$data[$j]=0;
			$j++;
		}
		
		$x_ary[$j]=substr($op['mon_det'][0]['out_date'],5);
		$data[$j]=$op['mon_det'][0]['qty'];		
		$j++;
		$hy=0; //holiday的計數
		$tmp_hdy_date = '';		//holiday的前一日
		$tmp_hy = array();  //holiday的陣列內容
		for ($i=1; $i < sizeof($op['mon_det']); $i++)
		{
			if ($op['mon_det'][$i]['hdy'] == 0)
			{
				if ($tmp_hdy_date == '')
				{
					$c_day=countDays ($op['mon_det'][($i-1)]['out_date'],$op['mon_det'][$i]['out_date']);					
				}else{
					$c_day=countDays ($tmp_hdy_date,$op['mon_det'][$i]['out_date']);
				}
					$cut_day = $c_day - sizeof($tmp_hy);
				if ($c_day > 1)
				{
					if ($tmp_hdy_date == '') $tmp_hdy_date = $op['mon_det'][($i-1)]['out_date'];
					$su=$op['mon_det'][$i]['qty'] / $cut_day;
					$tmp_su=0;
					for ($x=1; $x < $c_day; $x++)
					{
						$tmp_hy_mk = 0;
						$tmp_day=increceDaysInDate($tmp_hdy_date,$x);
						for ($hh=0; $hh <sizeof($tmp_hy); $hh++)
						{
							if ($tmp_day == $tmp_hy[$hh]) $tmp_hy_mk = 1;
						}
						if ($tmp_hy_mk == 0)
						{
							$x_ary[$j] = substr($tmp_day,5);
							$data[$j]=$su;
							$tmp_su=$tmp_su+$su;
							$j++;							
						}						
					}
					$tmp_day=increceDaysInDate($tmp_hdy_date,$c_day);
					$x_ary[$j] = substr($tmp_day,5);
					$su=$op['mon_det'][$i]['qty']-$tmp_su;
					$data[$j]=$su;
					$tmp_hdy_date = '';
					$tmp_hy = array();
					$hy=0;
					$j++;
				}else{				
					$x_ary[$j] = substr($op['mon_det'][$i]['out_date'],5);
					$data[$j]=$op['mon_det'][$i]['qty'];
					$j++;
					$su=$op['mon_det'][$i]['qty'];
				}
			}else{
				$tmp_hy[$hy] = $op['mon_det'][$i]['out_date'];
				$hy++;
				if ($tmp_hdy_date == '') 
				{					
					$tmp_hdy_date = $op['mon_det'][($i-1)]['out_date'];					
				}
			

			}
							
			if ($yy <> substr($op['mon_det'][$i]['out_date'],0,4) )	$years=$yy."~".substr($op['mon_det'][($i+1)]['out_date'],0,4);
					
		}
	}else{
		$x_ary[1]='';
		$data[1]=0;	

	}
		if ($years=='') $years=$yy;
		//&#164;&#1956;J graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		include ($config['root_dir']."/lib/src/jpgraph_bar.php");

		$graphic_title = "Order # : ".$op['mon']['ord_num']."  Style : ".$op['mon']['style']."  FTY: ".$op['mon']['fty']."  LINE: ".$op['mon']['line']." (".$op['mon']['worker']."p)    YEAR: ".$years;
		
		// Create the graph. These two calls are always required
		$graph = new Graph(600,270,"auto");    
		$graph->SetScale("textlin");
		
		// Adjust the margin
		$graph->img->SetMargin(60,30,40,40);    
		$graph->SetShadow();
		
		// Create the linear plot
		$lineplot=new LinePlot($data);
		$lineplot->mark->SetType(MARK_UTRIANGLE);
		$lineplot ->value->SetFont( FF_FONT1, FS_BOLD); 
		$lineplot ->value->SetFormat( " %d");
		$lineplot->value->show();

		// Add the plot to the graph
		$graph->Add($lineplot);

		$graph->title->Set($graphic_title);
//		$graph->xaxis->title->Set("Out-Put Date");
		$graph->yaxis->title->Set("QTY");

		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
//		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

		// Setup X-scale
		$graph->xaxis->SetTickLabels($x_ary);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
		$graph->xaxis->SetLabelAngle(45);

		$lineplot->SetColor("blue");
		$lineplot->SetWeight(2);

		// Display the graph
		$op['echart_1'] = $graph->Stroke('picture/monitor_lc.png');
		$op['echart_1'] = 'picture/monitor_lc.png';




 		if (isset($PHP_sr_startno))
 		{
 			$op['back_str'] = "&PHP_FTY=".$PHP_FTY."&PHP_LINE=".$PHP_LINE."&PHP_sr_startno=".$PHP_sr_startno."&PHP_style=".$PHP_style."&PHP_ord=".$PHP_ord;
 		}else{
 			$op['back_str'] =$PHP_back_str;
 		}
	page_display($op, '028', $TPL_MONITOR_SHOW);

				
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_message_send":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "monitor_edit":
		check_authority('028',"view");
 		$op = $monitor->get($PHP_id);
 		$op['back_str'] = $PHP_back_str;
		  	
		page_display($op, '028', $TPL_MONITOR_EDIT);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_message_send":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "monitor_del_ajx":
		check_authority('028',"edit");
 		$f1 = $monitor->del($PHP_id);
 		echo "SUCCESS DELETE Monitor Item";  	
		break;
//====================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 105  訂 單 SEARCH --開新窗 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "order_search":
check_authority('028',"view");

$op['PHP_fty'] = $_GET['PHP_fty'];

// 取出 客戶代號
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

$op['PHP_ln_id'] = $PHP_ln_id;
$op['sch_ln_id'] = $sch_ln_id;
$op['cust_select'] = $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
    
page_display($op, '028', $TPL_ORDER_S_LIST);			    	    
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 105  訂 單 SEARCH --開新窗SEARCH PHP_fty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_order_search":
check_authority('065',"view");

$PHP_factory = $_POST['PHP_factory'];

if(!isset($PHP_finish))$PHP_finish=0;
if(!isset($PHP_ship))$PHP_ship=0;
$parm = array(
	"dept"			=>  $PHP_dept_code,	
	"PHP_order_num" =>  $PHP_order_num,
	"cust"			=>	$PHP_cust,		
	"ref"			=>	$PHP_ref,
	"factory"		=>	$PHP_fty,
	"PHP_finish"    => 	$PHP_finish,
	"PHP_ship"  => 	$PHP_ship,
	"ln_id"			=>	$_GET['sch_ln_id'],
);

//可利用 PHP_dept_code 判定是否 業務部門進入
if (!$op = $schedule->pdt_ord_search(4,$parm)) {  
	$op['msg']= $schedule->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['PHP_fty'] = $PHP_fty;

// print_r($op);
// for($i=0; $i<sizeof($op['sorder']); $i++) {
	// $op['sorder'][$i]['saw_qty'] = $order->get_field_value('sum(qty)','',$op['sorder'][$i]['order_num'],'saw_out_put');
	// $op['sorder'][$i]['finish_qty'] = $monitor->get_schedule_finish_value('sum(qty) as finish_qty',$op['sorder'][$i]['sd_id'],'','saw_out_put');
// }

// creat cust combo box
// 取出 客戶代號
$where_str="order by cust_s_name"; // 依cust_s_name排序
if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  // 取出客戶簡稱
	$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	// 取出客戶代號
for ($i=0; $i< sizeof($cust_def); $i++)
{
	$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	// 以 [客戶代號-客戶簡稱] 呈現
}
$op['PHP_ln_id'] = $PHP_ln_id;
$op['sch_ln_id'] = $_GET['sch_ln_id'];
$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
$op['msg']= $order->msg->get(2);
$op['cgi']= $parm;

page_display($op, '065', $TPL_ORDER_S_LIST);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 105  訂 單 SEARCH --開新窗SEARCH
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "saw_adm":		
$op['FTY_search'] = $arry2->select($FACTORY,'','PHP_fty','select');

page_display($op, '028', $TPL_SAW_ADM);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 105  訂 單 SEARCH --開新窗SEARCH
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "saw_adm_add":
	
if($PHP_date > '2008-05-09')
{
	$redir_str = "monitor.php?PHP_action=dm_add&PHP_FTY=".$PHP_fty."&PHP_date=".$PHP_date."&PHP_edit_mk=1";
	redirect_page($redir_str);   	
}

//  先 取出 產出記錄列表-------------+++++++++++++
$argv = array(
"ord_num"	=>  '',
"k_date"	=>	$PHP_date,
"factory"	=>	$PHP_fty,
);

//  先 取出今日 產出記錄列表-------------+++++++++++++
if (!$pd = $daily->search($argv,1)) {   
	$op['msg']= $daily->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['pdt'] = $pd['daily'];
$op['date'] = $PHP_date;
$op['fty'] = $PHP_fty;

page_display($op, '028', $TPL_SAW_ADM_ADD);
break;		




//-------------------------------------------------------------------------------------
 
	case "saw_adm_del":

//    同 add_production 只是將 qty 改成負值 
//              但要注意原來是否已是finish的訂單......  及 del後是否還沒開始生產
//-------------------------------------------------------------------------------------
		check_authority('028',"edit");
		$today = $PHP_date;
		$tmp_date = explode('-',$PHP_date);
		$this_mon = $tmp_date[0].$tmp_date[1];
		$this_year = $tmp_date[0];
		$parm = array(	
						"ord_num"	=>  $PHP_ord_num,
						"factory"	=>  $PHP_fty,
						"qty"		=>  $PHP_qty * (-1),	// 直接 將qty改成負值
						"su"		=>  $PHP_su * (-1),	// 直接 將qty改成負值
						"k_date"	=>  $today,

				);

	//2006/04/10 改成一 個命令完成原資出料取出動作------原則上是生產的東西 一定有pdtion檔
	if(!$tmp = $order->get_fields_4_del_pdt($PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 

		$parm['id'] = $tmp['id'];
		$parm['pd_id'] = $tmp['p_id'];
		$ord_status = $tmp['status'];
		$out_su = $tmp['out_su'];

	
	// 改成陣列 月=>su	
		$A_out_su = decode_mon_su($out_su);

	// 本月  減少   新的產出 su
		if(array_key_exists ($this_mon, $A_out_su )){ //先查該月份存不存在
			$A_out_su[$this_mon] = $A_out_su[$this_mon] + $parm['su'];
		} else {
			$A_out_su[$this_mon] = $parm['su'];
		}

	// 將 陣列 A_out_su 內的值為零的去掉 **************
		$B_out_su = array_filter($A_out_su, 'filter_0_value');
	
	// 將 B_out_su 改成 csv 
		$parm['out_su'] = encode_number($B_out_su);  // 轉成 csv 
		
	// *************************************************
	// *********  刪除 daily table *********************
		if(!$F = $daily->del($PHP_daily_id)){   // 刪除 daily 資料庫
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *************************************************
	// *********  改寫入 pdtion table *********************

		if(!$F1 = $order->pd_out_update($parm)){   // 更新 pdtion 資料庫
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *************************************************
	// *********  寫入 capacity table *********************
		$m_su = substr($this_mon,4);

		if(!$F1 = $capaci->update_su($parm['factory'],$this_year,$m_su,'actual',$parm['su'])){  		$op['msg'][] = $capaci->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

		//20060410加入 判斷是否原來的 status為10[結束產出了] 則改成 status 為8 [生產中]
		if ($ord_status == '10'){
			//-----------  寫入 s_order ....... status ->8
			if(!$A1 = $order->update_field('status','8',$parm['id'])){;  // 更新 訂單狀況記錄  status =>8
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}


//---------------最後 取出 pdtion->qty_done 看看是否為 0 ; 如果為0 則尚未有產出 ----
//--- 要在 pdtion 將 start date 去掉 及 改變 s_order的status- >7
	// 先取出 原來 pdtion table內的資料來加入[qty_done],[out_su]

		if (!$qty_done = $order->get_field_value('qty_done', '', $PHP_ord_num,'pdtion')) {  

			//-------------------------------------------------
			//-----------  寫入 s_order ....... status ->7
			if(!$A1 = $order->update_field('status','7',$parm['id'])){;   // 更新 訂單狀況記錄  status =>8
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			//-------------------------------------------------
			//------------  寫入 pdtion ......... start 今日
			if(!$A1 = $order->update_pdtion_field('start',NULL ,$parm['pd_id'])){ 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}
			
				# 記錄使用者動態
		$message = "delete production [ ".$PHP_ord_num." ] in Q'ty:[".$PHP_qty."] from FTY:[".$parm['factory']."]";

			$log->log_add(0,"028E",$message);

##--****新增頁碼 start
		$redir_str = "monitor.php?PHP_action=saw_adm_add&&PHP_fty=".$parm['factory']."&PHP_date=".$PHP_date;
##--****新增頁碼 end
			redirect_page($redir_str);


	break;
	
	
	
	
//-------------------------------------------------------------------------------------
    case "do_saw_adm_add":

		check_authority('028',"add");
		$today = $PHP_date;
		$tmp_date = explode('-',$PHP_date);
		$this_mon = $tmp_date[0].$tmp_date[1];
		$this_year = $tmp_date[0];

	//2006/04/10 改成一 個命令完成原資出料取出動作------原則上是生產的東西 一定有pdtion檔
	if(!$tmp = $order->get_fields_4_del_pdt($PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

			break;
	} 

		$PHP_id = $tmp['id'];
		$PHP_pd_id = $tmp['p_id'];
		
		$parm = array(	"pd_id"		=>  $PHP_pd_id,
										"ord_num"	=>  $PHP_ord_num,
										"qty"		=>  $PHP_qty,
										"k_date"	=>  $today,

				);


//-----||  改寫 pdtion->qty_don,qty_update ||     daily     ||  改寫 capacity->actural(SU)  ||---------

	
	// 設定一些其它的 參數及 必要的 值 ++++++++++++++++++++++++++++++++++++++++++++++++
	// 由 s_order 取出 su_ratio 算出 su
		//   2005/08/31 改由資料庫抓 su [以 ie 計算填入 ]
		$su_ratio = $order->get_field_value('ie1', '', $PHP_ord_num);
		if ($su_ratio == 0)
		{
			$PHP_style = $order->get_field_value('style', '', $PHP_ord_num);
			if ($PHP_style=='PS' || $PHP_style=='BS' || $PHP_style=='BZ' || $PHP_style=='DR' || $PHP_style=='JK' || $PHP_style=='PS-J' || $PHP_style=='PS-P' || $PHP_style=='PS-S' || $PHP_style=='VS' || $PHP_style=='SS'){
				$cac_su=2*$PHP_qty;			
			}else{
				$cac_su=1*$PHP_qty;			
			}
		}else{
			$cac_su = $su_ratio * $PHP_qty;
		}
		$PHP_id = $order->get_field_value('id', '', $PHP_ord_num);
		$parm['factory'] = $order->get_field_value('factory', '', $PHP_ord_num);
		
		$parm['su'] = number_format($cac_su, 0, '', '');  //四捨五入 ----- 單筆

	// 先取出 原來 pdtion table內的資料來加入[qty_done],[out_su]
		$qty_done = $order->get_field_value('qty_done', '', $PHP_ord_num,'pdtion');

	// 當第一次產出時 [找不到資料]--- 要在 pdtion 寫入 start date 及 改變 s_order的status >8
	// 以後有產出時就不必再寫入.....................
		if (!$qty_done) {  

			//-----------  寫入 s_order ....... status ->8
			if(!$A1 = $order->update_field('status','8',$PHP_id)){;   // 更新 訂單狀況記錄  status =>8
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			//-------------------------------------------------
			//------------  寫入 pdtion ......... start 今日
			if(!$A1 = $order->update_pdtion_field('start',$PHP_date,$PHP_pd_id)){ 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}

	// 取出原來 pdtion 內的 out_su 字串
		$out_su = $order->get_field_value('out_su', '', $PHP_ord_num,'pdtion');

	// 改成陣列 月=>su	
		$A_out_su = decode_mon_su($out_su);

	// 本月加入 新的產出 su
		if(array_key_exists ($this_mon, $A_out_su )){ //先查該月份存不存在
			$A_out_su[$this_mon] = $A_out_su[$this_mon] + $parm['su'];
		} else {
			$A_out_su[$this_mon] = $parm['su'];
		}
			
	// 將 A_out_su 改成 csv 
		$parm['out_su'] = encode_number($A_out_su);  // 轉成 csv 
		
		
		// 設定日期
	// *************************************************
	// *********  寫入 daily table *********************
			$f1 = $daily->check($parm);
		if (!$f1) {  // 輸入資料 不正確時 
			$op['msg'] = $daily->msg->get(2);
			redirect_page($redir_str);
			break;
		}  // end if  輸入資料 不正確 

		if(!$F = $daily->add($parm)){   // 新增 daily 資料庫
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 


	// *************************************************
	// *********  改寫入 pdtion table *********************

		if(!$F1 = $order->pd_out_update($parm)){   // 更新 pdtion 資料庫
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 


	// *************************************************
	// *************************************************
	// *********  寫入 capacity table *********************
		$m_su = substr($this_mon,4);

		if(!$F1 = $capaci->update_su($parm['factory'],$this_year,$m_su,'actual',$parm['su'])){  		$op['msg'][] = $capaci->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
			
				# 記錄使用者動態
		$message = "Add [ ".$PHP_ord_num." ] in Q'ty:[".$PHP_qty."] to FTY:[".$parm['factory']."]";

			$log->log_add(0,"028A",$message);
##--****新增頁碼 start
		$redir_str = "monitor.php?PHP_action=saw_adm_add&&PHP_fty=".$parm['factory']."&PHP_date=".$PHP_date;
##--****新增頁碼 end
			redirect_page($redir_str);

		break;
	
	
	
	
	
//-------------------------------------------------------------------------------------
  case "production_year_report":

	check_authority('028',"view");
	$yy = $PHP_year1;	
	if($PHP_year1 == '')$yy = date('Y');
	$op['fty'] = $PHP_fty;
	$op['year'] = $yy;
	$op['nb_mon'] = $MONTH_WORK;
	if($PHP_fty == 'HJ')
	{
		$op['line_ttl'][sizeof($MONTH_WORK)] = $op['pdt_line'][sizeof($MONTH_WORK)] = $op['wk_qty'][sizeof($MONTH_WORK)] = $op['ot_qty'][sizeof($MONTH_WORK)] = 0;
		$op['line_count'][sizeof($MONTH_WORK)] = $op['pdt_pp'][sizeof($MONTH_WORK)] = $op['pp_rate'][sizeof($MONTH_WORK)] = $op['overtime'][sizeof($MONTH_WORK)] = 0;
		$op['overcost'][sizeof($MONTH_WORK)] = $op['cost_rate'][sizeof($MONTH_WORK)] = 0;
	
		for($a = 0; $a<sizeof($MONTH_WORK); $a++)
		{
			
			$s_date = $yy."-".$MONTH_WORK[$a]."-01";
			$e_date = $yy."-".$MONTH_WORK[$a]."-31";
			$op['mon'][$a] = $MM2[$MONTH_WORK[$a]];
//生產線全		
			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 GROUP BY saw_line";
			$ln= $monitor->get_fields_saw('saw_line',$where_str);			
			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 GROUP BY line_id";
			$peos= $monitor->get_fields_saw('max(saw_out_put.workers)',$where_str);			
			$pps = 0;
			for($i=0; $i<sizeof($peos); $i++)  $pps+=$peos[$i];
			
			$line_su = $op['pdt_pp'][$a] = $op['pdt_line'][$a] = $line_ttl = 0;
		
			for($i=0; $i<sizeof($ln); $i++)
			{
				
				if($MONTH_WORK[$a] == '11') 
				$x=0;
				$out_su = 0;
				$pp_su = 0;
				$day_det = array();

				$where_str = " AND fty ='HJ' AND line='".$ln[$i]."' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 AND sc = 0 GROUP BY out_date";
				$lnsu= $monitor->get_fields_saw('sum(su) as su',$where_str);				
				$tmp_su = $tmp_pp_su = 0;
				for($j=0; $j<sizeof($lnsu); $j++)
				{
					$tmp_su += $lnsu[$j];
					$line_ttl += $lnsu[$j];
					
				}							
				
				if($j > 0)$line_su += ($tmp_su / $j );
				
			}
//			$mon_people = $monitor->get_month_people($yy."-".$MONTH_WORK[$a],'HJ');
//	 		$mon_line   = $monitor->get_month_line($yy."-".$MONTH_WORK[$a],'HJ');


//計算生產天數  -- start	
			$where_str = " AND fty ='HJ' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 GROUP BY out_date";
			$out_hdy = $monitor->get_fields_saw('out_date',$where_str);
			$mon_days = sizeof($out_hdy);
/*			
			if(substr($s_date,0,7) == date('Y-m'))
			{
				$dd = date('d');
				$mon_days = $dd - sizeof($out_hdy);
			}else{
				$mm_tmp = explode('-',$s_date);
				$dd = getDaysInMonth($mm_tmp[1],$mm_tmp[0]);
				$mon_days = $dd - sizeof($out_hdy);
			}
*/			
//計算生產天數  -- end

			$line_su = NUMBER_FORMAT($line_su,0,'.','');
			if($i > 0) $op['pdt_pp'][$a] = $line_ttl/ $mon_days / $pps ;
			$op['pp_rate'][$a] = ($op['pdt_pp'][$a] / 9.6) * 100;
			if($i > 0) $op['pdt_line'][$a] = $line_ttl / $mon_days /sizeof($ln);
			$op['line_count'][$a] = $i;
			$op['line_ttl'][$a] = $line_ttl;

	
		}
	}
	if($PHP_fty == 'LY')
	{
		$op['line_ttl'][sizeof($MONTH_WORK)] = $op['pdt_line'][sizeof($MONTH_WORK)] = $op['wk_qty'][sizeof($MONTH_WORK)] = $op['ot_qty'][sizeof($MONTH_WORK)] = 0;
		$op['line_count'][sizeof($MONTH_WORK)] = $op['pdt_pp'][sizeof($MONTH_WORK)] = $op['pp_rate'][sizeof($MONTH_WORK)] = $op['overtime'][sizeof($MONTH_WORK)] = 0;
		$op['overcost'][sizeof($MONTH_WORK)] = $op['cost_rate'][sizeof($MONTH_WORK)] = 0;
		$sm_mk = 0;
		for($a = 0; $a<sizeof($MONTH_WORK); $a++)
		{
			$s_date = $yy."-".$MONTH_WORK[$a]."-01";
			$e_date = $yy."-".$MONTH_WORK[$a]."-31";
			$op['mon'][$a] = $MM2[$MONTH_WORK[$a]];
			
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 GROUP BY saw_line";
			$ln= $monitor->get_fields_saw('saw_line',$where_str);			

			$line_su = $op['pdt_pp'][$a] = $op['pdt_line'][$a] = $line_ttl = 0;
			for($i=0; $i<sizeof($ln); $i++)
			{
				$where_str = " AND fty ='LY' AND line='".$ln[$i]."' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 AND sc = 0 GROUP BY out_date";
				$lnsu= $monitor->get_fields_saw('sum(su) as su',$where_str);
				$tmp_su = $tmp_pp_su = 0;
				for($j=0; $j<sizeof($lnsu); $j++)
				{
					$tmp_su += $lnsu[$j];
					$line_ttl += $lnsu[$j];
				}
				
				if($j > 0)$line_su += ($tmp_su / $j );
			}
			
			$mon_people = $monitor->get_month_people($yy."-".$MONTH_WORK[$a],'LY');
	 		$mon_line   = $monitor->get_month_line($yy."-".$MONTH_WORK[$a],'LY');	 		
			$line_su = NUMBER_FORMAT($line_su,0,'.','');
			
			
//計算生產天數  -- start	
			$where_str = " AND fty ='LY' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 GROUP BY out_date";
			$out_dy = $monitor->get_fields_saw('out_date',$where_str);
			$mon_days = sizeof($out_dy);			
/*						
			if(substr($s_date,0,7) == date('Y-m'))
			{
				$dd = date('d');
				$mon_days = $dd - sizeof($out_hdy);
			}else{
				$mm_tmp = explode('-',$s_date);
				$dd = getDaysInMonth($mm_tmp[1],$mm_tmp[0]);
				$mon_days = $dd - sizeof($out_hdy);
			}
*/			
//計算生產天數  -- end			
	//		if($a == 8)echo "<FONT color='#FFFFFF'>".$line_ttl."==>".$mon_people."<BR>";
	//		if($a == 8)echo $line_ttl."==>".$mon_line."<BR></FONT>";
			if($i > 0)
			{
			 	$op['pdt_pp'][$a] = $line_ttl / $mon_days / $mon_people ;
				$op['pdt_line'][$a] = $line_ttl / $mon_days / $mon_line;
				$op['pdt_line'][sizeof($MONTH_WORK)] += $op['pdt_line'][$a];
				$op['pdt_pp'][sizeof($MONTH_WORK)] += $op['pdt_pp'][$a];
				$sm_mk++;
			}
			$op['pp_rate'][$a] = ($op['pdt_pp'][$a] / 9.6) * 100;

			$op['line_ttl'][$a] = $line_ttl;
			$op['line_ttl'][sizeof($MONTH_WORK)] += $line_ttl;
			$op['line_count'][$a] = $i;
			
			$o_date = $yy."-".$MONTH_WORK[$a];
			$op['overtime'][$a] = $overtime->count_work_time($o_date,$PHP_fty);
			$op['overcost'][$a] = $overtime->count_work_cost($o_date,$PHP_fty);
			$op['cost_rate'][$a] = $op['pp_rate'][$a]/(1+($op['overtime'][$a]/100));
/*	
//計算上班時間產量
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0";
			$tmp= $monitor->get_with_ord('sum(ROUND(work_qty * ie1)) as su',$where_str);
			//$tmp= $monitor->get_fields_saw('sum(work_qty)',$where_str);			
			if(!isset($tmp[0]))$tmp[0]['su'] = 0;
			$op['wk_qty'][$a] = $tmp[0]['su'];
*/
//計算加班時間產量
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0";
			$tmp= $monitor->get_with_ord('sum(ROUND(over_qty * ie1)) as su',$where_str);
			//$tmp= $monitor->get_fields_saw('sum(over_qty)',$where_str);			
			if(!isset($tmp[0]))$tmp[0]['su'] = 0;
			$op['ot_qty'][$a] = $tmp[0]['su'];	
			$op['ot_qty'][sizeof($MONTH_WORK)] +=$op['ot_qty'][$a];		
			
//計算上班時間產量			
			$op['wk_qty'][$a] = $op['line_ttl'][$a] - $op['ot_qty'][$a];
			$op['wk_qty'][sizeof($MONTH_WORK)] +=$op['wk_qty'][$a];
		}
		$op['pdt_line'][sizeof($MONTH_WORK)] /= $sm_mk;
		$op['pdt_pp'][sizeof($MONTH_WORK)] /= $sm_mk;



//立元內廠
		$op['LY'][sizeof($MONTH_WORK)]['line_ttl'] = $op['LY'][sizeof($MONTH_WORK)]['pdt_line'] = $op['LY'][sizeof($MONTH_WORK)]['wk_qty'] = $op['LY'][sizeof($MONTH_WORK)]['ot_qty'] = 0;
		$op['LY'][sizeof($MONTH_WORK)]['line_count'] = $op['LY'][sizeof($MONTH_WORK)]['pdt_pp'] = $op['LY'][sizeof($MONTH_WORK)]['pp_rate'] = $op['LY'][sizeof($MONTH_WORK)]['overtime'] = 0;
		$op['LY'][sizeof($MONTH_WORK)]['overcost'] = $op['LY'][sizeof($MONTH_WORK)]['cost_rate'] = 0;
		$sm_mk = 0;
		for($a = 0; $a<sizeof($MONTH_WORK); $a++)
		{
			$s_date = $yy."-".$MONTH_WORK[$a]."-01";
			$e_date = $yy."-".$MONTH_WORK[$a]."-31";
			
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND sub_fty = '' GROUP BY saw_line";
			$ln= $monitor->get_fields_saw('saw_line',$where_str);			

			$line_su = $op['LY'][$a]['pdt_pp'] = $op['LY'][$a]['pdt_line'] = $line_ttl = 0;
			for($i=0; $i<sizeof($ln); $i++)
			{
				$where_str = " AND fty ='LY' AND line='".$ln[$i]."' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 AND sc = 0 AND sub_fty = '' GROUP BY out_date";
				$lnsu= $monitor->get_fields_saw('sum(su) as su',$where_str);
				$tmp_su = $tmp_pp_su = 0;
				for($j=0; $j<sizeof($lnsu); $j++)
				{
					$tmp_su += $lnsu[$j];
					$line_ttl += $lnsu[$j];
				}
				
				if($j > 0)$line_su += ($tmp_su / $j );
			}
			
			$mon_people = $monitor->get_month_people($yy."-".$MONTH_WORK[$a],'LY','LY');
	 		$mon_line   = $monitor->get_month_line($yy."-".$MONTH_WORK[$a],'LY','LY');	 		
			$line_su = NUMBER_FORMAT($line_su,0,'.','');
			
			
//計算生產天數  -- start	
			$where_str = " AND fty ='LY' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 AND sub_fty = '' GROUP BY out_date";
			$out_dy = $monitor->get_fields_saw('out_date',$where_str);
			$mon_days = sizeof($out_dy);			
			
//計算生產天數  -- end			
			if($i > 0)
			{ 
				$op['LY'][$a]['pdt_pp'] = $line_ttl / $mon_days / $mon_people ;
				$op['LY'][$a]['pdt_line'] = $line_ttl / $mon_days / $mon_line;
				$op['LY'][sizeof($MONTH_WORK)]['pdt_line'] += $op['LY'][$a]['pdt_line'];
				$op['LY'][sizeof($MONTH_WORK)]['pdt_pp'] += $op['LY'][$a]['pdt_pp'];
				$sm_mk++;
			}
			$op['LY'][$a]['pp_rate'] = ($op['LY'][$a]['pdt_pp'] / 9.6) * 100;
			$op['LY'][$a]['line_ttl'] = $line_ttl;
			$op['LY'][sizeof($MONTH_WORK)]['line_ttl'] += $op['LY'][$a]['line_ttl'];
			$op['LY'][$a]['line_count'] = $i;
			
			$o_date = $yy."-".$MONTH_WORK[$a];
			$op['LY'][$a]['overtime'] = $overtime->count_work_time($o_date,$PHP_fty,'LY');
//			$op['LY'][$a]['overcost'] = $overtime->count_work_cost($o_date,$PHP_fty);
			$op['LY'][$a]['cost_rate'] = $op['LY'][$a]['pp_rate']/(1+($op['LY'][$a]['overtime']/100));
/*
//計算上班時間產量
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sub_fty = '' AND sc = 0";
			$tmp= $monitor->get_fields_saw('sum(work_qty)',$where_str);			
			if(!isset($tmp[0]))$tmp[0] = 0;
			$op['LY'][$a]['wk_qty'] = $tmp[0];
*/
//計算加班時間產量
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sub_fty = '' AND sc = 0";
			$tmp= $monitor->get_with_ord('sum(ROUND(over_qty * ie1)) as su',$where_str);
//			$tmp= $monitor->get_fields_saw('sum(over_qty)',$where_str);			
			if(!isset($tmp[0]))$tmp[0]['su'] = 0;
			$op['LY'][$a]['ot_qty'] = $tmp[0]['su'];	
			$op['LY'][sizeof($MONTH_WORK)]['ot_qty'] +=	$op['LY'][$a]['ot_qty'];
//計算上班時間產量			
			$op['LY'][$a]['wk_qty'] = $op['LY'][$a]['line_ttl'] - $op['LY'][$a]['ot_qty'];
			$op['LY'][sizeof($MONTH_WORK)]['wk_qty'] +=	$op['LY'][$a]['wk_qty'];
		}
		$op['LY'][sizeof($MONTH_WORK)]['pdt_line'] /= $sm_mk;
		$op['LY'][sizeof($MONTH_WORK)]['pdt_pp'] /= $sm_mk;


//隆安廠
		$op['LA'][sizeof($MONTH_WORK)]['line_ttl'] = $op['LA'][sizeof($MONTH_WORK)]['pdt_line'] = $op['LA'][sizeof($MONTH_WORK)]['wk_qty'] = $op['LA'][sizeof($MONTH_WORK)]['ot_qty'] = 0;
		$op['LA'][sizeof($MONTH_WORK)]['line_count'] = $op['LA'][sizeof($MONTH_WORK)]['pdt_pp'] = $op['LA'][sizeof($MONTH_WORK)]['pp_rate'] = $op['LA'][sizeof($MONTH_WORK)]['overtime'] = 0;
		$op['LA'][sizeof($MONTH_WORK)]['overcost'] = $op['LA'][sizeof($MONTH_WORK)]['cost_rate'] = 0;
		$sm_mk = 0;
		for($a = 0; $a<sizeof($MONTH_WORK); $a++)
		{
			$s_date = $yy."-".$MONTH_WORK[$a]."-01";
			$e_date = $yy."-".$MONTH_WORK[$a]."-31";
			
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND sub_fty = 'LA' GROUP BY saw_line";
			$ln= $monitor->get_fields_saw('saw_line',$where_str);			

			$line_su = $op['LA'][$a]['pdt_pp'] = $op['LA'][$a]['pdt_line'] = $line_ttl = 0;
			for($i=0; $i<sizeof($ln); $i++)
			{
				$where_str = " AND fty ='LY' AND line='".$ln[$i]."' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 AND sc = 0 AND sub_fty = 'LA' GROUP BY out_date";
				$lnsu= $monitor->get_fields_saw('sum(su) as su',$where_str);
				$tmp_su = $tmp_pp_su = 0;
				for($j=0; $j<sizeof($lnsu); $j++)
				{
					$tmp_su += $lnsu[$j];
					$line_ttl += $lnsu[$j];
				}
				
				if($j > 0)$line_su += ($tmp_su / $j );
			}
			
			$mon_people = $monitor->get_month_people($yy."-".$MONTH_WORK[$a],'LY','LA');
	 		$mon_line   = $monitor->get_month_line($yy."-".$MONTH_WORK[$a],'LY','LA');	 		
			$line_su = NUMBER_FORMAT($line_su,0,'.','');
			
			
//計算生產天數  -- start	
			$where_str = " AND fty ='LY' AND out_date >= '$s_date' AND out_date <= '$e_date' AND holiday = 0 AND sub_fty = 'LA' GROUP BY out_date";
			$out_dy = $monitor->get_fields_saw('out_date',$where_str);
			$mon_days = sizeof($out_dy);			
			
//計算生產天數  -- end			
			if($i > 0) 
			{
				$op['LA'][$a]['pdt_pp'] = $line_ttl / $mon_days / $mon_people ;
				$op['LA'][$a]['pdt_line'] = $line_ttl / $mon_days / $mon_line;
				$op['LA'][sizeof($MONTH_WORK)]['pdt_line'] += $op['LA'][$a]['pdt_line'];
				$op['LA'][sizeof($MONTH_WORK)]['pdt_pp'] += $op['LA'][$a]['pdt_pp'];
				$sm_mk++;		
			}
			$op['LA'][$a]['pp_rate'] = ($op['LA'][$a]['pdt_pp'] / 9.6) * 100;

			$op['LA'][$a]['line_ttl'] = $line_ttl;
			$op['LA'][sizeof($MONTH_WORK)]['line_ttl'] += $op['LA'][$a]['line_ttl'];
			$op['LA'][$a]['line_count'] = $i;
			
			$o_date = $yy."-".$MONTH_WORK[$a];
			$op['LA'][$a]['overtime'] = $overtime->count_work_time($o_date,$PHP_fty,'LA');
//			$op['LA'][$a]['overcost'] = $overtime->count_work_cost($o_date,$PHP_fty,'LA');
			$op['LA'][$a]['cost_rate'] = $op['LA'][$a]['pp_rate']/(1+($op['LA'][$a]['overtime']/100));
	
//計算上班時間產量
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sub_fty = 'LA' AND sc = 0";
			$tmp= $monitor->get_fields_saw('sum(work_qty)',$where_str);			
			if(!isset($tmp[0]))$tmp[0] = 0;
			$op['LA'][$a]['wk_qty'] = $tmp[0];

//計算加班時間產量
			$where_str = " AND fty ='LY' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sub_fty = 'LA' AND sc = 0";
			$tmp= $monitor->get_with_ord('sum(ROUND(over_qty * ie1)) as su',$where_str);
//			$tmp= $monitor->get_fields_saw('sum(over_qty)',$where_str);			
			if(!isset($tmp[0]))$tmp[0]['su'] = 0;
			$op['LA'][$a]['ot_qty'] = $tmp[0]['su'];			
			$op['LA'][sizeof($MONTH_WORK)]['ot_qty'] +=	$op['LA'][$a]['ot_qty'];

//計算上班時間產量			
			$op['LA'][$a]['wk_qty'] = $op['LA'][$a]['line_ttl'] - $op['LA'][$a]['ot_qty'];
			$op['LA'][sizeof($MONTH_WORK)]['wk_qty'] +=	$op['LA'][$a]['wk_qty'];
		}
		$op['LA'][sizeof($MONTH_WORK)]['pdt_line'] /= $sm_mk;
		$op['LA'][sizeof($MONTH_WORK)]['pdt_pp'] /= $sm_mk;











	}
		page_display($op, '028', $TPL_RPT_YEAR_PDT_LINE);
		break;	
	
	
	
//-------------------------------------------------------------------------------------
  case "production_year_report_det":

	check_authority('028',"view");
	$yy = $PHP_year1;	
	$op['fty'] = $PHP_fty;
	$op['year'] = $yy;
	$op['nb_mon'] = $MONTH_WORK;
	
	for($a = 0; $a<sizeof($MONTH_WORK); $a++)
	{
			
			$s_date = $yy."-".$MONTH_WORK[$a]."-01";
			$e_date = $yy."-".$MONTH_WORK[$a]."-31";
			$op['mon'][$a] = $MM2[$MONTH_WORK[$a]];


//生產線男裝			
			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND s_order.line_sex = 'M' GROUP BY saw_line";
			$ln= $monitor->get_fields_with_ord('saw_line',$where_str);			
			
			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND s_order.line_sex = 'M' GROUP BY line_id";
			$peos= $monitor->get_fields_with_ord('max(saw_out_put.workers)',$where_str);			
			$pps = 0;
			for($i=0; $i<sizeof($peos); $i++)  $pps+=$peos[$i];

	
			$line_su = $op['man'][$a]['pdt_pp'] = $op['man'][$a]['pdt_line'] = $line_ttl = 0;

			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND s_order.line_sex = 'M' GROUP BY saw_line, out_date ORDER BY saw_line";
			$sus= $monitor->get_with_ord('sum( saw_out_put.su ) AS su, out_date, saw_line',$where_str);			
			$ln_su = $j = 0;
			$tmp_ln='';

			for($i=0; $i<sizeof($sus); $i++)  
			{
				if($tmp_ln <> $sus[$i]['saw_line'])
				{
					if($tmp_ln <> '')
					{
						if($j > 0)$line_su += $ln_su / $j;
						$ln_su = $j = 0;
					}
					
					$tmp_ln = $sus[$i]['saw_line'];
				}
				$ln_su += $sus[$i]['su'];
				$line_ttl += $sus[$i]['su'];
				$j++;
				
			}
			if($j > 0)$line_su += $ln_su / $j;	
	
			$line_su = NUMBER_FORMAT($line_su,0,'.','');

//計算生產天數  -- start	
			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday > 0 AND s_order.line_sex = 'M' GROUP BY line_id";
			$out_hdy = $monitor->get_fields_saw('out_date',$where_str);
						
			if(substr($s_date,0,7) == date('Y-m'))
			{
				$dd = date('d');
				$mon_days = $dd - sizeof($out_hdy);
			}else{
				$mm_tmp = explode('-',$s_date);
				$dd = getDaysInMonth($mm_tmp[1],$mm_tmp[0]);
				$mon_days = $dd - sizeof($out_hdy);
			}
//計算生產天數  -- end	
			
			if($i > 0) $op['man'][$a]['pdt_pp'] = $line_ttl/ $mon_days/ $pps ;
			$op['man'][$a]['pp_rate'] = ($op['man'][$a]['pdt_pp'] / 9.6) * 100;
			if($i > 0) $op['man'][$a]['pdt_line'] = $line_ttl / $mon_days / sizeof($ln);
			$op['man'][$a]['line_count'] = sizeof($ln);
			$op['man'][$a]['line_ttl'] = $line_ttl;


		
		
		
//生產線女裝	
			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND s_order.line_sex = 'F' GROUP BY saw_line";
			$ln= $monitor->get_fields_with_ord('saw_line',$where_str);			
			$line_su = $op['girl'][$a]['pdt_pp'] = $op['girl'][$a]['pdt_line'] = 0;

			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND s_order.line_sex = 'F' GROUP BY line_id";
			$peos= $monitor->get_fields_with_ord('max(saw_out_put.workers)',$where_str);			
			$pps = 0;
			for($i=0; $i<sizeof($peos); $i++)  $pps+=$peos[$i];
	

			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND sc = 0 AND s_order.line_sex = 'F' GROUP BY saw_line, out_date ORDER BY saw_line";
			$sus= $monitor->get_with_ord('sum( saw_out_put.su ) AS su, out_date, saw_line',$where_str);			
			$ln_su = $j = $line_ttl = 0;
			$tmp_ln='';

			for($i=0; $i<sizeof($sus); $i++)  
			{
				if($tmp_ln <> $sus[$i]['saw_line'])
				{
					if($tmp_ln <> '')
					{
						if($j > 0)$line_su += $ln_su / $j;
						$ln_su = $j = 0;
					}
					
					$tmp_ln = $sus[$i]['saw_line'];
				}
				$ln_su += $sus[$i]['su'];
				$line_ttl += $sus[$i]['su'];
				$j++;
				
			}
			if($j > 0)$line_su += $ln_su / $j;


			$line_su = NUMBER_FORMAT($line_su,0,'.','');

//計算生產天數  -- start	
			$where_str = " AND fty ='HJ' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday > 0 AND s_order.line_sex = 'F' GROUP BY saw_line, out_date ORDER BY saw_line";
			$out_hdy = $monitor->get_fields_saw('out_date',$where_str);
						
			if(substr($s_date,0,7) == date('Y-m'))
			{
				$dd = date('d');
				$mon_days = $dd - sizeof($out_hdy);
			}else{
				$mm_tmp = explode('-',$s_date);
				$dd = getDaysInMonth($mm_tmp[1],$mm_tmp[0]);
				$mon_days = $dd - sizeof($out_hdy);
			}
//計算生產天數  -- end	
			
			if($i > 0) $op['girl'][$a]['pdt_pp'] = $line_ttl/ $mon_days/ $pps ;
			$op['girl'][$a]['pp_rate'] = ($op['girl'][$a]['pdt_pp'] / 9.6) * 100;
			if($i > 0) $op['girl'][$a]['pdt_line'] = $line_ttl / $mon_days / sizeof($ln);
			$op['girl'][$a]['line_count'] = sizeof($ln);
			$op['girl'][$a]['line_ttl'] = $line_ttl;


		}	
		page_display($op, '028', $TPL_RPT_YEAR_PDT_LINE_DET);
		break;	
	
	
	
//-------------------------------------------------------------------------------------
  case "line_mm_report":

	check_authority('028',"view");
	$yy = $PHP_year1;	
	$op['fty'] = $PHP_fty;
	$op['year'] = $yy;
	$op['month'] = $PHP_mm;
	if($PHP_sex == 'F')$op['sex'] = "Women";
	if($PHP_sex == 'M')$op['sex'] = "Men";
	
			
			$s_date = $yy."-".$PHP_mm."-01";
			$e_date = $yy."-".$PHP_mm."-31";

	$mon_now_day = getDaysInMonth($PHP_mm,$yy);
	$op['s_date'] = $PHP_mm."/01";
	$op['e_date'] = $PHP_mm."/31";
	if($PHP_mm == date('m')) 
	{
		$mon_now_day = countDays ($s_date,$yy."-".$PHP_mm."-".date('d'))+1; 
		$op['e_date'] = date('m/d');
	}
	



	if($PHP_sex)
	{
			$where_str = " AND fty ='".$PHP_fty."' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND workers > 0 AND s_order.line_sex = '".$PHP_sex."' GROUP BY saw_line";
			$ln= $monitor->get_fields_with_ord('saw_line',$where_str);			
			
	}else{
			$where_str = " AND fty ='".$PHP_fty."' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND workers > 0 GROUP BY saw_line";
			$ln= $monitor->get_fields_with_ord('saw_line',$where_str);			

	}

	
	
	$in=$out=$ttl_su=0;
	for($i=0; $i<sizeof($ln); $i++)
	{
		$op['pdt'][$i]['line'] = $ln[$i];
		if($PHP_sex)
		{	
			$where_str = " AND fty ='".$PHP_fty."' AND line='".$ln[$i]."'  AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND workers > 0 AND s_order.line_sex = '".$PHP_sex."' GROUP BY line_id";
			$peos= $monitor->get_fields_with_ord(' max(saw_out_put.workers)',$where_str);			

			$where_str = " AND fty ='".$PHP_fty."' AND line='".$ln[$i]."' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND workers > 0 AND s_order.line_sex = '".$PHP_sex."' GROUP BY saw_line, out_date ORDER BY saw_line";
			$sus= $monitor->get_fields_with_ord(' sum( saw_out_put.su ) AS su',$where_str);			
		}else{
			$where_str = " AND fty ='".$PHP_fty."' AND line='".$ln[$i]."'  AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 AND workers > 0 GROUP BY line_id";
			$peos= $monitor->get_fields_with_ord(' max(saw_out_put.workers)',$where_str);			



			$where_str = " AND fty ='".$PHP_fty."' AND line='".$ln[$i]."' AND out_date >= '".$s_date."' AND out_date <= '".$e_date."' AND holiday = 0 GROUP BY out_date";
			$sus= $monitor->get_fields_saw('sum(su) as su',$where_str);

		}

		$op['pdt'][$i]['peos'] = $peos[0];
		$tmp_su = 0;

		for($j=0; $j<sizeof($sus); $j++)
		{
			$tmp_su += $sus[$j];
			
		}
		if($j == 0) $j = 1;
		$ttl_su += ($tmp_su / $j);
		$op['pdt'][$i]['l_su'] = NUMBER_FORMAT( ($tmp_su / $j),2,'.','');	
		$op['pdt'][$i]['p_su'] = NUMBER_FORMAT($op['pdt'][$i]['l_su']/$peos[0],2,'.','');	
		$op['pdt'][$i]['pp_rate'] = ($op['pdt'][$i]['p_su'] / 9.6) * 100;


		$labor = $FTY_LABOR[$PHP_fty];
		
//		echo $op['pdt'][$i]['l_su']." X ".$mon_now_day." X ".$FTY_CM[$PHP_fty]."<br>";
		$op['pdt'][$i]['in_cost'] = $op['pdt'][$i]['l_su']*$mon_now_day*$FTY_CM[$PHP_fty];
		$op['pdt'][$i]['out_cost'] = $op['pdt'][$i]['peos']*$labor;
		$op['pdt'][$i]['markup'] = ($op['pdt'][$i]['in_cost'] - $op['pdt'][$i]['out_cost'])/$mon_now_day;

		$in = $ttl_su*$mon_now_day*$FTY_CM[$PHP_fty];
		$out += $op['pdt'][$i]['out_cost'];
//		$mark += $op['pdt'][$i]['markup'];
	}
		$op['cm_cost'] = $FTY_CM[$PHP_fty];
		$op['per_cost'] = $labor;
	
		$op['in_cost'] = NUMBER_FORMAT($ttl_su,0,'','')*$mon_now_day*$FTY_CM[$PHP_fty];
		$op['out_cost'] = $out;
		$op['markup'] = ($in - $out)/$mon_now_day/$i;
		page_display($op, '028', $TPL_RPT_PDT_LINE);
		break;	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_message_send":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "saw_line_pic_by_date":
    check_authority('028',"view");
    if ($PHP_line == "TOTAL")
    {
    	$line = $monitor->get_line_det('',$SCH_fty,$SCH_str,$SCH_end);    	
		}else{
			$line = $monitor->get_line_det($PHP_line,$SCH_fty,$SCH_str,$SCH_end);
			$where_str =  " AND saw_fty = '".$SCH_fty."' AND saw_line='".$PHP_line;
		}
		$op['line'] = $PHP_line;

//計算訂單平均生產日 		
 		$n_field = "order_num"; 		
 		$where_str = " AND saw_out_put.saw_fty= '".$SCH_fty."'";
 		if($PHP_line != "TOTAL")$where_str .=" AND saw_out_put.saw_line = '".$PHP_line."'";
 		$where_str .=" AND saw_out_put.out_date >= '".$SCH_str."' AND saw_out_put.out_date <='".$SCH_end."'";
 		$where_str .=" AND holiday = 0";
 		$where_str .=" AND s_order.status > 8 GROUP BY s_order.order_num";
 		$ord_recs = $monitor-> get_with_ord($n_field,$where_str);
 		$pdt_out_days = 0;
 		for($i=0; $i<sizeof($ord_recs); $i++)
 		{
 			$n_field = "count(saw_out_put.out_date) as out_date"; 		
 			$where_str = " AND saw_out_put.saw_fty= '".$SCH_fty."'";
 			if($PHP_line != "TOTAL")$where_str .=" AND saw_out_put.saw_line = '".$PHP_line."'";
 			$where_str .=" AND s_order.order_num = '".$ord_recs[$i]['order_num']."'";
 			$where_str .=" AND holiday = 0";
 			$where_str .=" AND s_order.status > 8 GROUP BY s_order.order_num";
 			$tmp_recs = $monitor-> get_with_ord($n_field,$where_str);    			
			
 		 	$pdt_out_days +=  $tmp_recs[0]['out_date'];
    } 
 		if(sizeof($ord_recs)> 0)$op['avg_ord'] = $pdt_out_days / sizeof($ord_recs);


//畫圖

		$color_ary = array('#F0FFDD','#DEFEB4','#C8FE80','#ACFF3D','#CBDEB1','#B4D983','#9CD54F','#83D11C','#96A87E','#73A431','#6B755E','#5C7042','#446517','#3D4730'
											,'#F5E9FF','#DBB0FF','#B969FE','#CAB2DE','#AC76DB','#8E35DA','#AB9AB9','#9368B7','#7A33B7','#8A7A98','#7A5995','#5C238C','#5E5467','#462761'
											,'#FCD2D6','#D7D7D7','#D7B8CB','#D1ECEB','#C9B8F0','#B8CCF0','#FFBAB8','#B8F0BA','#FDF998','#B4FD98','#FDBE98');

$years='';
$tmp_num = $style_num = '';
$max_su=$line[0]['su'];
$op['total_su'] = 0;
$j = $k =1;
$k = -1;

$x_ary[0] = '';
$data[0]=0;
$hd_x = 0;
		for ($i=0; $i < sizeof($line); $i++)
		{
			$sch_date = ($i > 0) ? $line[($i-1)]['out_date'] : increceDaysInDate($SCH_str,-1);
			$tmp_days = countDays ($sch_date,$line[$i]['out_date']);
			for($ds=1; $ds < $tmp_days; $ds++)
			{
					$x_ary[$j] = substr(increceDaysInDate($sch_date,$ds),5);
					$data[$j]=0;
					$j++;					
			}
			
			if ($line[$i]['holiday'] == 0) //如果不是假日時才記錄數量日期
			{
				$x_ary[$j] = substr($line[$i]['out_date'],5);
				$data[$j]=$line[$i]['su'];
				if ($line[$i]['su'] > $max_su) $max_su = $line[$i]['su'];		
				$op['total_su'] += $line[$i]['su'];
				$j++;					

			}else{
		  	$hd_x ++;
		  }			
		}
			
		$op['avg_su'] = $op['total_su'] / (countDays ($SCH_str,$SCH_end) - $hd_x);
		
$tmp_style = '';
$x = -1;

		
		
		for ($i=0; $i < sizeof($line); $i++)
		{
			if($i > 0)
			{
				$tmp_days = countDays ($line[($i-1)]['out_date'],$line[$i]['out_date']);
				for($ds=1; $ds < $tmp_days; $ds++) $area_ary[$x][] = $max_su;							
			}

			if ($line[$i]['holiday'] == 0 && $PHP_line != "TOTAL") //如果不是假日時才記錄數量日期
			{
				$where_str = " AND saw_fty = '".$SCH_fty."' AND saw_line='".$PHP_line."' AND out_date='".$line[$i]['out_date']."' ORDER BY qty DESC";
				$ords = $monitor->get_fields_saw('ord_num',$where_str);
				
				$tmp_ie = $order->get_field_value('ie1','',$ords[0]);	
				$style_num= $order->get_field_value('style_num','',$ords[0]); //取得訂單style_num		
				
				if (!isset($ie_ary[$style_num]))
				{				
						$ord_ary[$style_num] = '';
						$ie_ary[$style_num]=$tmp_ie;	
				}
				if(!strstr($ord_ary[$style_num],$ords[0]))$ord_ary[$style_num] .= $ords[0].'('.$style_num.')<BR>';			
				if($tmp_style != $style_num)
				{
					
					$x ++;
					if($x > 0)
					{
						$y = $x -1;
						for($j=0; $j<(sizeof($area_ary[$y])-1); $j++) $area_ary[$x][] = $max_su;
						$area_ary[$x][] = $max_su;
					}
					$area_ary[$x][] = $max_su;
					$style_ary[$x] =  $style_num;
					$tmp_style = $style_num;		
					if($i == 0)
					{
						$sch_date = increceDaysInDate($SCH_str,-1);
						$tmp_days = countDays ($sch_date,$line[$i]['out_date']);
						for($ds=1; $ds < $tmp_days; $ds++) $area_ary[$x][] = $max_su;					
					}
				}else{
					$area_ary[$x][] = $max_su;					
				}
			
			}
		}
		$area_ary[$x][] = $max_su;

	
		//&#164;&#1956;J graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		include ($config['root_dir']."/lib/src/jpgraph_bar.php");
		$graphic_title = "Production-Line : ".$PHP_line." FACTORY : ".$SCH_fty;
		
		// Create the graph. These two calls are always required
		$graph = new Graph(600,300,"auto");    
		$graph->SetScale("textlin");
		
		// Adjust the margin
		$graph->img->SetMargin(60,20,30,40);    
		$graph->SetShadow();

//訂單著色
	if(!strstr($PHP_line,'contract') && $PHP_line != 'TOTAL')
	{
		$ord_days = 0;
		$tmp = 0;
		for($i = (sizeof($area_ary)-1); $i >= 0; $i--)
		{
			if($style_ary[$i] != 'NONE')
			{
				$op['polt_det'][$i]['order'] = $ord_ary[$style_ary[$i]];
				$op['polt_det'][$i]['ord_num'] = substr($ord_ary[$style_ary[$i]],0,10);
			}

			$plot_ord = $i."polt";
			$$plot_ord = new LinePlot($area_ary[$i]);
			$$plot_ord->SetFillColor($color_ary[$i]);
			$graph->Add($$plot_ord);


			$op['polt_det'][$i]['color'] = $color_ary[$i];	
			if($style_ary[$i] != 'NONE')$op['polt_det'][$i]['ie'] = $ie_ary[$style_ary[$i]];		
			$ord_days += (sizeof($area_ary[$i]) - $tmp);
			$tmp = sizeof($area_ary[$i]);
			//echo sizeof($area_ary[$i])."<BR>";
		}
//		$op['avg_ord'] = $ord_days/sizeof($area_ary);
	}







		// Create the linear plot
		$lineplot=new LinePlot($data);
		$lineplot->mark->SetType(MARK_UTRIANGLE);
		$lineplot ->value->SetFont( FF_FONT1, FS_BOLD); 
		$lineplot ->value->SetFormat( " %d");
		$lineplot->value->show();
		// Add the plot to the graph
		$graph->Add($lineplot);


		$graph->title->Set($graphic_title);
		$graph->yaxis->title->Set("SU");

		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

		// Setup X-scale
		$graph->xaxis->SetTickLabels($x_ary);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
		$graph->xaxis->SetLabelAngle(45);

		$lineplot->SetColor("blue");
		$lineplot->SetWeight(2);

		// Display the graph
		$op['echart'][0]['pic'] = $graph->Stroke('picture/saw0.png');
		$op['echart'][0]['pic'] = 'picture/saw0.png';
		$op['echart'][0]['id'] = 0;
		
		$op['year_title'] ="(".$SCH_str."  ~  ".$SCH_end.")";
		

	page_display($op, '028', $TPL_SAW_LINE_PIC);

	break;	
	
	
//-------------------------------------------------------------------------------------
    case "month_overtime_report":
	$TPL_RPT_OVERTIME_DET = "rpt_overtime_det.html";
	check_authority('028',"view");
	$op['fty'] = $PHP_fty;
	$op['yy'] = $PHP_year;
	$op['mm'] = $PHP_mm;
	$ary_mm = array('JAN'	=>	'01',  'FEB'	=>	'02', 'MAR'	=>	'03',  'APR'	=>	'04', 
						      'MAY'	=>	'05',  'JUN'	=>	'06', 'JUL'	=>	'07',  'AUG'	=>  '08',
						      'SEP' =>	'09',	 'OCT'	=>	'10', 'NOV'	=>	'11',  'DEC'	=>	'12' );
	$sch_date = $PHP_year."-".$ary_mm[$PHP_mm];
	$op['ot_det'] = $report->get_month_overtime($sch_date, $PHP_fty);
	$op['amt']['ot_wk'] = $op['amt']['ot_time'] = $op['amt']['ot_qty'] = 0;
	$op['amt']['workers']  = $op['amt']['wk_time'] = $op['amt']['wk_qty']  = 0;
	for($i=0; $i<sizeof($op['ot_det']); $i++)
	{
		$op['amt']['ot_wk'] += $op['ot_det'][$i]['ot_wk'];
		$op['amt']['ot_time'] += $op['ot_det'][$i]['ot_time'];
		$op['amt']['ot_qty'] += $op['ot_det'][$i]['ot_qty'];
		$op['amt']['workers'] += $op['ot_det'][$i]['workers'];
		$op['amt']['wk_time'] += $op['ot_det'][$i]['wk_time'];
		$op['amt']['wk_qty'] += $op['ot_det'][$i]['wk_qty'];
	}
	
	
	page_display($op, '028', $TPL_RPT_OVERTIME_DET);
	break;		


}   // end case ---------

?>
