<?php
session_start();

session_register	('sch_parm');
session_register	('sch_fty');
session_register	('sch_year');
session_register	('sch_month');

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

$P_LINE = array (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

session_register	('sch_period');
$para_cm = $para->get(0,'sch_period');
$sch_period = $para_cm['set_value'];
$sch_period++;


session_register	('finish_rate');
$para_cm = $para->get(0,'sch_finish_rate');
$finish_rate = $para_cm['set_value'];
$AUTH = "017";
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pd_schedule":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pd_schedule":
 	check_authority($AUTH,"view");

// 		$op['fty_select'] =  $arry2->select($FACTORY,'','PHP_fty','select',''); 
// 		$op['fty_sch_select'] =  $arry2->select($FACTORY,'','SCH_fty','select','');
 		$op['year_select'] =  $arry2->select($YEAR_WORK,date('Y'),'SCH_yy','select','');
		$op['month_select'] =  $arry2->select($MONTH_WORK,'','SCH_mm','select','');

	
	
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		$op['year'] = date('Y');
		$op['month'] = date('m');
		$op['today'] = $TODAY;
	page_display($op, $AUTH, $TPL_SMPL_SCH);
	
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "schedule_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "schedule_add":
 	check_authority($AUTH,"add");

 		if(!isset($PHP_fty)) $PHP_fty = '';
 		if(isset($PHP_fty) && $PHP_fty)$sch_fty = $PHP_fty;

 		$str_date = date('Y-m').'-01 00:00:00';
		$late_date = $smpl_sch->get_last_date($sch_fty);
		if(!$late_date)$late_date = date('Y-m');
		$date_ary = explode('-',$late_date);
		$days = getDaysInMonth($date_ary[1],$date_ary[0]);
		$late_date = $date_ary[0].'-'.$date_ary[1].$days;
		
 		$op['ln_rec'] = $smpl_sch->get_by_mm($sch_fty, $str_date,$late_date);
// 		$op['con_rec'] = $smpl_sch->get_contract($sch_fty, $str_date,$late_date);
//計算日期
		$lst_mm = ($date_ary[0] - date('Y')) * 12 + $date_ary[1];
		$yy = date('Y');
		$mm = date('m');
		for($j=date('m'); $j<=$lst_mm; $j++)
		{
			$end_day = getDaysInMonth($mm,$yy);
			for($i=1; $i <= $end_day; $i++)
			{
				$op['days'][] = $i;				
				$op['days_init'][] = '';
				$op['days_init'][] = '';
				$op['week_day'][] = date('w',strtotime(($yy.'-'.$mm.'-'.$i))); 
			}			
			$op['yy_mm'][] = $yy.'-'.$mm;
			$op['span_mm'][] = (getDaysInMonth($mm,$yy)*2);
			$mm++;
			if($mm < 10) $mm = '0'.$mm;
			if($mm == 13)
			{
				$mm = '01';
				$yy++;
			}
		}
 		
 		$ord_rec = $smpl_sch->get_order($sch_fty);
		$ord_value = $ord_key = array();
		for($i=0; $i<sizeof($ord_rec); $i++)
		{
			$qty = $ord_rec[$i]['qty'] - $ord_rec[$i]['ext_qty'];
			$ord_value[] =$ord_rec[$i]['num']." (".$qty." pcs ;  Styel : ".$ord_rec[$i]['style'].";  ETD : ".$ord_rec[$i]['etd']." )";
			$ord_key[] =$ord_rec[$i]['num'];
		}
		$op['ord_select'] =  $arry2->select($ord_value,'','PHP_ord','select','',$ord_key);
		
		$line = $smpl_sch->get_line($sch_fty);
		for($i=0; $i < sizeof($line); $i++)
		{
			$line_value[] = $line[$i]['line'];
			$line_key[] = $line[$i]['id'];
		}
		$op['line_select'] = $arry2->select($line_value,'','PHP_line','select',"get_ets('".$sch_fty."',this)",$line_key);
		$op['line_sp'] = $arry2->select($line_value,'','PHP_line_sp','select',"get_edit_ets('".$sch_fty."',this)",$line_key);
		$op['line_change'] = $arry2->select($line_value,'','PHP_line','select',"get_edit_ets('".$sch_fty."',this)",$line_key);
		

		$op['yy'] = $sch_year;
		$op['mm'] = $sch_month;
		$op['fty'] = $sch_fty;
		$op['span'] = $days - 10;
		
		$op['msg'] = $schedule->msg->get(2);
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 
		$op['today'] = $TODAY;
	page_display($op, $AUTH, $TPL_SMPL_SCH_ADD);
	
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_sch_point":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_sch_point":
 	check_authority($AUTH,"add");
 		
 		if(!$PHP_line)
 		{
 			$msg = "please select production line first";
			$redir_str ="smpl_sch.php?PHP_action=schedule_add&PHP_msg=".$msg;
			redirect_page($redir_str);
 		}
 	
 		if(!$smpl_sch->check_qty($PHP_ord,$PHP_sp_qty))
 		{

 			$msg = "ORDER QTY must bigger than SCHEDULE  QTY";
			$redir_str ="smpl_sch.php?PHP_action=schedule_add&PHP_msg=".$msg;
			redirect_page($redir_str); 			
 		}
 			
		$parm = array(
									'ord_num'	=>	$PHP_ord,
									'period'	=>	$sch_period,
									'fty'			=> 	$sch_fty,
									'line_sp'	=>	$PHP_line,
									'sp_qty'	=>	$PHP_sp_qty,
									'des'			=>	$PHP_des,
									'pt_ets'	=>	$PHP_pt_ets
									);


 		$new_id = $smpl_sch->point_schedule($parm);
	 		$smpl_sch->add_ord_schedule($PHP_ord);

		$s_ln = $smpl_sch->get_ln_det($new_id);

 		 
 		if($new_id)
 		{
			$message = "ADD order [ ".$PHP_ord." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
			$log->log_add(0,"42A",$message);
		}else{		
			$msg = $smpl_sch->msg->get(2);
			$message = $msg[0];
		}

		$redir_str ="smpl_sch.php?PHP_action=schedule_add&PHP_msg=".$message;
		redirect_page($redir_str);
	
	break;	

	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sch_line_spare":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "sch_line_spare":
 	check_authority($AUTH,"add");
 		
 		if(!$PHP_line_sp)
 		{
 			$msg = "please select production line first";
			$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
			redirect_page($redir_str);
 		}
 		if($ord_qty < $PHP_sp_qty)
 		{
 			$msg = "ORDER QTY must bigger than SCHEDULE QTY";
			$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
			redirect_page($redir_str);
 		}
 
 		
		$parm = array('id'			=>	$PHP_id,
									'ord_num'	=>	$PHP_ord_num,
									'line_id'	=>	$PHP_line_id,
									'period'	=>	$sch_period,
									'fty'			=> 	$sch_fty,
									'line_sp'	=>	$PHP_line_sp,
									'sp_qty'	=>	$PHP_sp_qty,
									'des'			=>	$PHP_des,
									'pt_ets'	=>	$PHP_pt_ets,
									);


 		$new_id = $smpl_sch->spare_schedule($parm);
 		$smpl_sch->add_ord_schedule($PHP_ord_num);


 		 
 		if($new_id)
 		{
			$message = "SPARE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
			$log->log_add(0,"42A",$message);
		}else{		
			$msg = $schedule->msg->get(2);
			$message = $msg[0];			
		} 

		$redir_str ="smpl_sch.php?PHP_action=schedule_add&PHP_msg=".$message;
		redirect_page($redir_str);
	
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sch_line_change":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "sch_line_change":
 	check_authority($AUTH,"add");
 		
 		if(!$PHP_line)
 		{
 			$msg = "please select production line first";
			$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
			redirect_page($redir_str);
 		}

 		
		$parm = array('org_id'		=>	$PHP_id,
									'ord_num'		=>	$PHP_ord_num,
									'period'		=>	$sch_period,
									'fty'				=> 	$sch_fty,
									'line_sp'		=>	$PHP_line,
									'des'				=>	$PHP_des,
									'pt_ets'		=>	$PHP_pt_ets,
									'sp_qty'		=>	$ord_qty,
									);


 		$new_id = $smpl_sch->change_line($parm);
 		$smpl_sch->add_ord_schedule($PHP_ord_num);

		$s_ln = $smpl_sch->get_ln_det($new_id);
 		 
 		if($new_id)
 		{
			$message = "CHANGE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
			$log->log_add(0,"42A",$message);
		}else{		
			$msg = $schedule->msg->get(2);
			$message = $msg[0];
		}

		$redir_str ="smpl_sch.php?PHP_action=schedule_add&PHP_msg=".$message;
		redirect_page($redir_str);
	
	break;		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sch_del":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "sch_del":
 	check_authority($AUTH,"add");
 		

		$s_ln = $smpl_sch->get_ln_det($PHP_id);
 		 
		$message = "DELETE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$PHP_del_qty."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
		$log->log_add(0,"42A",$message);


 		$smpl_sch->del_schedule($PHP_id,$PHP_del_qty,$PHP_ord_num);
 		$smpl_sch->add_ord_schedule($PHP_ord_num);



		$redir_str ="smpl_sch.php?PHP_action=schedule_add&PHP_msg=".$message;
		redirect_page($redir_str);
	
	break;			
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "schedule_view_small":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "schedule_view_small":
 	check_authority($AUTH,"view");
 		if(!isset($SCH_fty)) $SCH_fty = '';
 		if(!$SCH_fty) $SCH_fty = 'RD';
		$sch_fty = $SCH_fty;
		if(!isset($PHP_pt_date) || !$PHP_pt_date)$PHP_pt_date = $TODAY;
		$PHP_pt_date = $PHP_pt_date." 00:00:00";
		$lst_date = $smpl_sch->get_last_date($SCH_fty);
		if(!$lst_date)$lst_date = date('Y-m-').getDaysInMonth(date('m'),date('Y'));
//		if($tmp_date < $lst_date)	$lst_date = $tmp_date;		 		
		$ll = explode(' ',$lst_date);
		$pp = explode(' ',$PHP_pt_date);
		$tmp_lst = explode('-',$ll[0]);
		$tmp_now = explode('-',$pp[0]);
		


		
		$lst_mm = ($tmp_lst[0] - $tmp_now[0]) * 12 + $tmp_lst[1];
		$yy = $tmp_now[0];
		$mm = $tmp_now[1];
		for($j=$tmp_now[1]; $j<=$lst_mm; $j++)
		{
			$end_day = getDaysInMonth($mm,$yy);
			$str_day = 1;
			if($mm == $tmp_now[1] && $yy == $tmp_now[0])$str_day = $tmp_now[2];
			if($mm == $tmp_lst[1] && $yy == $tmp_lst[0])$end_day = $tmp_lst[2];
			$k = ($str_day+1-1);
			$x = ($str_day+1-1);
			for($i=$str_day; $i <= $end_day; $i++)
			{
			  $op['week_day'][] = date('w',strtotime(($yy.'-'.$mm.'-'.$i)));
			 
			  if($i <= 10)
			  {
			  	$op['days_color'][] = '#797979';	
			  }else if($i <= 20){
					$op['days_color'][] = '#555555';	
				}else{
					$op['days_color'][] = '#003333';	
				}
				if($k >= 10) $k = $k % 10;
				$op['days'][] = $k;	
				$op['days_hr'][] = $k;		
				$op['days_hr'][] = $k;
				
				$k++;			
			}
			
			$op['yy_mm'][] = $yy.'-'.$mm;
			$op['span_mm'][] = ($end_day - $str_day + 1) * 2;
			$mm++;
			if($mm < 10) $mm = '0'.$mm;
			if($mm == 13)
			{
				$mm = '01';
				$yy++;
			}
		}

 				
 		$op['ln_rec'] = $smpl_sch->get_by_mm( $SCH_fty, $PHP_pt_date,$lst_date);
 		for($i=0; $i<sizeof($op['ln_rec']); $i++)
 		{
 			if(isset($op['ln_rec'][$i]['sch_rec']))
 			{
 				for($j=0; $j<sizeof($op['ln_rec'][$i]['sch_rec']); $j++)
 				{
 					if(isset($op['ln_rec'][$i]['sch_rec'][$j]['ord_num']))$op['ln_rec'][$i]['sch_rec'][$j]['ord_link'] = $smpl_sch->get_ord_link($op['ln_rec'][$i]['sch_rec'][$j]['ord_num']);
 				}
 			}
 		}
 		
// 		$op['con_rec'] = $schedule->get_contract( $SCH_fty, $PHP_pt_date,$lst_date);
/*
		for($i=0; $i<sizeof($op['ln_rec']); $i++)
		{
			for($j=0; $j<sizeof($op['ln_rec'][$i]['sch_rec']); $j++)
			{
				 if($op['ln_rec'][$i]['sch_rec'][$j]['rel_etf'] > '2010-04-15')
				 {		
				 		$schedule->add_ord_schedule($op['ln_rec'][$i]['sch_rec'][$j]['ord_num']);						
 						if($schedule->check_sch_finish($op['ln_rec'][$i]['sch_rec'][$j]['ord_num']))
 						{
 							$schedule->add_capacity($op['ln_rec'][$i]['sch_rec'][$j]['ord_num']);
							$message = "UPDATE order [ ".$op['ln_rec'][$i]['sch_rec'][$j]['ord_num']." ] production schedule [ETS, ETF] ";
							$log->log_add(0,"42A",$message);
 						}
 					}
			}
		}
*/



		$op['ord_rec'] = $smpl_sch->get_order($SCH_fty);

		$op['year_select'] =  $arry2->select($YEAR_WORK,date('Y'),'SCH_year','select'); 
		$op['month_select'] =  $arry2->select($MONTH_WORK,date('m'),'SCH_month','select'); 

					
		$op['fty'] = $SCH_fty;

		$sch_year = date('Y');
		$sch_month = date('m');

		
		$op['msg'] = $smpl_sch->msg->get(2);
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 
		$op['today'] = $TODAY;
	page_display($op, $AUTH, $TPL_SMPL_SCH_VIEW_SMALL);
	
	break;
	
	
	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-1-4     訂單 產出量記錄 報表
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "sch_ord_view":

		check_authority($AUTH,"view");
		$op['ord_num'] = $PHP_ord_num;
		$daily_out = $result = array();
    $smpl_sch->count_pdt_qty($PHP_ord_num);
		$ord = $smpl_ord->get('','',$PHP_ord_num);
		$op['ord_out']	 = $smpl_output->get($PHP_ord_num);
		
		$smpl_qty = 0;
		for($i=0; $i<sizeof($op['ord_out']); $i++)
		{
			$smpl_qty += $op['ord_out'][$i]['qty'];
			$op['ord_out'][$i]['sum_qty'] = $smpl_qty;
		}
		$op['order'] = $ord;
		$op['order']['order_num'] = $PHP_ord_num;			

		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['schedule'] = $smpl_sch->order_get($op['order']['num']);  //取出該筆記錄
		$ets = '9999-99-99';  $etf = '0000-00-00';
		for($i=0; $i<sizeof($op['schedule']); $i++)
		{
			if($op['schedule'][$i]['rel_ets'] < $ets )$ets = $op['schedule'][$i]['rel_ets'];
			if($op['schedule'][$i]['rel_etf'] > $etf )$etf = $op['schedule'][$i]['rel_etf'];
		}
	
//========================================================================================
include_once($config['root_dir']."/lib/src/jpgraph.php");
include_once($config['root_dir']."/lib/src/jpgraph_gantt.php");
	
		// A new graph with automatic size 
		$sch_graph  = new GanttGraph (750); 
		$sch_graph->title->Set("ORDER SCHEDULE Chart");
		$sch_graph->ShowHeaders(GANTT_HWEEK | GANTT_HDAY | GANTT_HMONTH | GANTT_HYEAR);
		$sch_graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY2);
//		$sch_graph->scale->actinfo->SetFont(FF_BIG5,FS_NORMAL,10); 
		$i_mk = 0;
		//  A new activity on row '0' 
		for($i=0; $i<sizeof($op['schedule']); $i++)
		{
			$fsh_rate = NUMBER_FORMAT(($op['schedule'][$i]['pdt_qty'] / $op['schedule'][$i]['qty']),4);
			$sch_activity[$i]  = new GanttBar ($i_mk,'  ', $op['schedule'][$i]['rel_ets'], $op['schedule'][$i]['rel_etf'],'['.($fsh_rate * 100).'%]'); 
			if($op['order']['status'] == 10){
				$sch_activity[$i] ->SetPattern(BAND_RDIAG, "#D7D7FF"); 
				$sch_activity[$i] ->SetFillColor ("#D7D7FF"); 			
			}else if($op['order']['status'] == 6 && $op['schedule'][$i]['rel_etf'] > $op['order']['etd']){
				$sch_activity[$i] ->SetPattern(BAND_RDIAG, "#10AD26"); 
				$sch_activity[$i] ->SetFillColor ("#10AD26"); 
			}else if($op['order']['status'] == 6 ){
				$sch_activity[$i] ->SetPattern(BAND_RDIAG, "#D0FDCD"); 
				$sch_activity[$i] ->SetFillColor ("#D0FDCD"); 
			}else if($op['schedule'][$i]['rel_etf'] > $op['order']['etd']){
				$sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FD1838"); 
				$sch_activity[$i] ->SetFillColor ("#FD1838"); 
			}else if($op['order']['status'] == 7 ){
				$sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FD66DF"); 
				$sch_activity[$i] ->SetFillColor ("#FD66DF"); 
			}else{
				$sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FFFFFF"); 
				$sch_activity[$i] ->SetFillColor ("#DDDDDD"); 
			}	
			if ($fsh_rate > 1)$fsh_rate = 1;
			$sch_activity[$i]->progress->Set($fsh_rate);
			$sch_activity[$i]->progress->SetPattern(GANTT_SOLID,"teal");
	
			$sch_graph->Add($sch_activity[$i]); 
			$i_mk ++;
			// Create a miletone
			$milestone = new MileStone($i_mk,'',$op['schedule'][$i]['etf'],substr($op['schedule'][$i]['etf'],0,10));
			$milestone->title->SetColor("black");
			//$milestone->title->SetFont(FF_FONT1,FS_BOLD);
			$sch_graph->Add($milestone);
			$i_mk ++;


		}
		$op['order']['chart'] = $sch_graph->Stroke('picture/schedule_out.png');		

		$op['today'] = $TODAY;
	page_display($op,$AUTH, $TPL_SMPL_SCH_ORD_SHOW);	    	    
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_ets_ajx":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "get_ets_ajx":
		
		$line_ets = $smpl_sch->get_etf($PHP_fty,$PHP_line);  //取出該筆記錄

		$line_select =  $arry2->select($line_ets,'','PHP_pt_ets','select');

		echo "<B>ETS : </B>".$line_select;
		
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "schedule_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "schedule_print":
	 	check_authority($AUTH,"view");
	 	$sch_month =$SCH_month;
	 	$sch_year = $SCH_year;
 		$days = getDaysInMonth($sch_month,$sch_year);
 		
 		$ln_rec = $schedule->get_by_mm($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
 		$con_rec = $schedule->get_contract($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
		$limit_date=$sch_year.'-'.$sch_month.'-31';
		$ord_rec = $schedule->get_order($sch_fty,$limit_date);
		for($i=1; $i <= $days; $i++)
		{
			$op['days'][] = $i;		
		}

	

include_once($config['root_dir']."/lib/class.pdf_schedule.php");

$mark = $print_title=$sch_year.'-'.$sch_month.'-01 ~ '.$sch_year.'-'.$sch_month.'-31 SCHEDULE REPORT';
$print_title2 = "FTY :".$sch_fty;
$mark =' ['.$sch_year.'-'.$sch_month.'] SCHEDULE REPORT';
$pdf=new PDF_schedule('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',10);
// 共42行
$x = 0;
for($i = 0; $i<sizeof($ln_rec); $i++)
{
	$thd_mk = 0;
	if(isset($ln_rec[$i]['sch_rec']))
	{
		if ($x >= 39)
		{
			$pdf->AddPage();
			$x = 0;
		}
		for($j=0; $j<sizeof($ln_rec[$i]['sch_rec']); $j++)
		{
			if($ln_rec[$i]['sch_rec'][$j]['day_range'] == 1)$thd_mk = 1;
		}
		if($thd_mk == 1)
		{
		 	$pdf->thd_line_sch($ln_rec[$i],$days);
		 	$x += 3;
		}else{
			$pdf->two_line_sch($ln_rec[$i],$days);
			$x += 2;
		}
	}
}

for($i = 0; $i<sizeof($con_rec); $i++)
{
		if ($x >= 40)
		{
			$pdf->AddPage();
			$x = 0;
		}	
		$pdf->sc_line_sch($con_rec[$i],$days);
		$x +=2;
}
$name=$sch_year.'-'.$sch_month.'_schd.pdf';
$pdf->Output($name,'D');



	
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "schedule_small_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "schedule_small_print":
 		check_authority($AUTH,"view");

		$tmp_date = increceDaysInDate($PHP_pt_date,15).' 00:00:00';
		$lst_date = $smpl_sch->get_last_date($sch_fty); 

		if($tmp_date < $lst_date)	$lst_date = $tmp_date;
		$tmp_ll = explode(' ',$lst_date);
		$tmp_lst = explode('-',$tmp_ll[0]);
		$tmp_nn = explode(' ',$PHP_pt_date);
		$tmp_now = explode('-',$tmp_nn[0]);

		$lst_mm = ($tmp_lst[0] - $tmp_now[0]) * 12 + $tmp_lst[1];
		$yy = $tmp_now[0];
		$mm = $tmp_now[1];
	
		for($j=$tmp_now[1]; $j<=$lst_mm; $j++)
		{
			$end_day = getDaysInMonth($mm,$yy);
			$str_day = 1;
			if($mm == $tmp_now[1] && $yy == $tmp_now[0])$str_day = $tmp_now[2];
			if($mm == $tmp_lst[1] && $yy == $tmp_lst[0])$end_day = $tmp_lst[2];
			$k = ($str_day+1-1);
			for($i=$str_day; $i <= $end_day; $i++)
			{
			
				if($k >= 10) $k = $k % 10;
				$days[] = $k;			
				$k++;
			}
			$yy_mm[] = $yy.'-'.$mm;
			$span_mm[] = ($end_day - $str_day + 1);
			$mm++;
			if($mm < 10) $mm = '0'.$mm;
			if($mm == 13)
			{
				$mm = '01';
				$yy++;
			}
		}

 			
 		$ln_rec 	= $smpl_sch->get_by_mm($sch_fty, $PHP_pt_date.' 00:00:00',$lst_date);
// 		$con_rec 	= $schedule->get_contract( $sch_fty, $PHP_pt_date,$lst_date);
//		$ord_rec 	= $schedule->get_order($sch_fty);

include_once($config['root_dir']."/lib/class.pdf_smpl_schedule_small.php");

$mark = $print_title=$PHP_pt_date.' ~ '.substr($lst_date,0,10).' SCHEDULE REPORT';
$print_title2 = "FTY :".$sch_fty;
$mark =' ARIAL VIEW SCHEDULE REPORT';
$pdf=new PDF_smpl_schedule_small('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',10);
$x = 0;
for($i = 0; $i<sizeof($ln_rec); $i++)
{
	$thd_mk = 0;
	if(isset($ln_rec[$i]['sch_rec']))
	{
		if ($x >= 35)
		{
			$pdf->AddPage();
			$x = 0;
		}
		
		$pdf->two_line_sch($ln_rec[$i],sizeof($days));
		$x ++;

	}
}


$name=$PHP_pt_date.'_schd.pdf';
$pdf->Output($name,'D');
					
	
	break;	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "schedule_excel":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "schedule_excel":
	 	check_authority($AUTH,"view");
	 	$sch_month =$SCH_month;
	 	$sch_year = $SCH_year;	 	
 		$days = getDaysInMonth($sch_month,$sch_year);
 		
 		$ln_rec = $schedule->get_by_mm($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
 		$con_rec = $schedule->get_contract($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
/*
		$limit_date=$sch_year.'-'.$sch_month.'-31';
		$ord_rec = $schedule->get_order($sch_fty,$limit_date);

		for($i=1; $i <= $days; $i++)
		{
			$op['days'][] = $i;		
		}
*/	

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
	  HeaderingExcel('schedule.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('Schedule');

	  $f0 =& $workbook->add_format();  //灰底白字置中
	  $f0->set_top(1);
	  $f0->set_left(1);
	  $f0->set_right(1);
	  $f0->set_bottom(1);

	  $f2 =& $workbook->add_format();  //灰底白字置中
	  $f2->set_top(1);
	  $f2->set_left(1);
	  $f2->set_right(1);

	  $f3 =& $workbook->add_format();  //灰底白字置中
	  $f3->set_left(1);
	  $f3->set_right(1);

	  $f4 =& $workbook->add_format();  //灰底白字置中
	  $f4->set_left(1);
	  $f4->set_right(1);
	  $f4->set_bottom(1);



	  $max_cell = 0;
	  for($i=0;  $i<sizeof($ln_rec); $i++)
	  {
	  	if(isset($ln_rec[$i]['sch_rec']))
	  	{
	  		if(sizeof($ln_rec[$i]['sch_rec']) > $max_cell)$max_cell = sizeof($ln_rec[$i]['sch_rec']);
	  	}
	  }
	  for ($i=0; $i<= $max_cell; $i++) $worksheet1->set_column(1,$i,40);
	  
  
	  
	  $worksheet1->write_string(0,0,$sch_fty.'  SCHEDULE FOR '.$sch_year.'-'.$sch_month);
		$row = 3;
		$str = 1;
		for($i=0;  $i<sizeof($ln_rec); $i++)
		{					
			$set_row = ($row * ($i + 1));
			$worksheet1->write_string($str,0,$ln_rec[$i]['line'],$f0);
			$worksheet1->write_string(($str+1),0,'',$f0);
			$worksheet1->write_string(($str+2),0,'',$f0);
			$worksheet1->merge_cells($str,0,$set_row,0);			
			if(isset($ln_rec[$i]['sch_rec']))
			{
				$k = 1;
				for($j = 0; $j<sizeof($ln_rec[$i]['sch_rec']); $j++)
				{
					if(isset($ln_rec[$i]['sch_rec'][$j]['ord_num']))
					{
						$date_range = countDays($ln_rec[$i]['sch_rec'][$j]['rel_ets'],$ln_rec[$i]['sch_rec'][$j]['rel_etf']);
						$qty_day =  NUMBER_FORMAT(($ln_rec[$i]['sch_rec'][$j]['qty'] / $date_range),0);
						$string = $ln_rec[$i]['sch_rec'][$j]['style_num']."   <".$qty_day."/day>   ".$ln_rec[$i]['sch_rec'][$j]['etd'];
					  $worksheet1->write_string(($str),$k,$string,$f2);
					
						$ets = increceDaysInDate($ln_rec[$i]['sch_rec'][$j]['rel_ets'],-2);
						$string = '<'.$ln_rec[$i]['sch_rec'][$j]['ord_num'].'>   '.$ets.' ~ '.$ln_rec[$i]['sch_rec'][$j]['rel_etf'];
						$worksheet1->write_string(($str+1),$k,$string,$f3);
						$worksheet1->write_string(($str+2),$k,$ln_rec[$i]['sch_rec'][$j]['des'],$f4);
						$k++;
					}
				}
				
			}
			$str = $set_row+1;
		}
		
		for($i=0; $i<sizeof($con_rec); $i++)
		{

			for($j=0; $j<sizeof($con_rec[$i]['sch_rec']); $j++)
			{
				if(isset($con_rec[$i]['sch_rec'][$j]['ord_num']))
				{	
					$date_range = countDays($con_rec[$i]['sch_rec'][$j]['rel_ets'],$con_rec[$i]['sch_rec'][$j]['rel_etf']);
					$qty_day =  NUMBER_FORMAT(($con_rec[$i]['sch_rec'][$j]['qty'] / $date_range),0);
					$string = $con_rec[$i]['sch_rec'][$j]['style_num']."   <".$qty_day."/day>   ".$con_rec[$i]['sch_rec'][$j]['etd'];
					$ets = increceDaysInDate($con_rec[$i]['sch_rec'][$j]['rel_ets'],-2);
					$string .= '   <'.$con_rec[$i]['sch_rec'][$j]['ord_num'].'>   '.$ets.' ~ '.$con_rec[$i]['sch_rec'][$j]['rel_etf'];	
					$string .= '   '.$con_rec[$i]['sch_rec'][$j]['des'];
					$worksheet1->write_string(($str),0,'SC',$f0);
					$worksheet1->write_string(($str),1,$string,$f0);
					$worksheet1->write_string(($str),2,'',$f0);
					$worksheet1->write_string(($str),3,'',$f0);
					$worksheet1->merge_cells($str,1,$str,3);		
					$str++;
				}

			}
		
		}

		
		$workbook->close();
	break;	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_line_change":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "get_line_change":
		$line = $smpl_sch->get_line($sch_fty);
		for($i=0; $i < sizeof($line); $i++)
		{
			$line_value[] = $line[$i]['line'];
			$line_key[] = $line[$i]['id'];
		}

			$tmp_line_value = $tmp_line_key = array();
			for($j = 0; $j<sizeof($line_key); $j++)
			{
				if($PHP_id != $line_key[$j])
				{
					$tmp_line_value[] = $line_value[$j];
					$tmp_line_key[] = $line_key[$j];
				}
			}
			$line_change = $arry2->select_sch_line($tmp_line_value,'','PHP_line','select',"get_edit_ets('".$sch_fty."',this)",$tmp_line_key);
			$line_sp = $arry2->select_sch_line($tmp_line_value,'','PHP_line_sp','select',"get_edit_ets('".$sch_fty."',this)",$tmp_line_key);
	 	header('Content-type: text/html;charset=BIG5'); 
	 	echo $line_change."|".$line_sp;
	 	
exit;	 	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_ord_schedule":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "get_ord_schedule":
		$ord_sch = $schedule->order_get($PHP_ord_num);
		$sch_str = '';
		for($i=0; $i<sizeof($ord_sch); $i++)
		{
			$sch_str .= 'LN:'.$ord_sch[$i]['line_name'].' ; QTY:'.$ord_sch[$i]['qty'].' ; '.substr($ord_sch[$i]['rel_ets'],5).'~'.substr($ord_sch[$i]['rel_etf'],5).'|';
		}
	 	$sch_str = substr($sch_str,0,-1);
	 	echo $sch_str;
exit;	 		
//-------------------------------------------------------------------------

}   // end case ---------

?>
