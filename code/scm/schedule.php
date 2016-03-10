<?php
session_start();
// error_reporting(E_ALL);
##################  2014/11/28  ########################
# schedule.php
# for Carnival SCM [Schedule]  management
# Mode Chu 2014/11/28
########################################################
require_once "config.php";
require_once "config.admin.php";
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once($config['root_dir']."/lib/class.monitor.php");
require_once "init.object.php";
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
include_once($config['root_dir']."/lib/pdt_finish.class.php");
$pdt_finish = new PDT_FINISH();
if (!$pdt_finish->init($mysql,"log")) { print "error!! cannot initialize database for pdt_finish class"; exit; }
include_once($config['root_dir']."/lib/pdtion.class.php");
$pdtion = new PDTION();
if (!$pdtion->init($mysql,"log")) { print "error!! cannot initialize database for pdtion class"; exit; }
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++
session_register	('edit_mode');
session_register	('pdt_range');
$para_cm = $para->get(0,'pdt_range');
$pdt_range = $para_cm['set_value'];
session_register	('schedule_var');
$para_cm = $para->get(0,'schedule_var');
$schedule_var = $para_cm['set_value'];
session_register	('sch_finish_rate');
$para_cm = $para->get(0,'sch_finish_rate');
$sch_finish_rate = $para_cm['set_value'];
# 每小時的產能
$DF_RATE = 1.5;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++

// $PHP_SELF = $_SERVER['PHP_SELF'];
// $perm = $GLOBALS['power'];
// $P_LINE = array (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

$op = array();
$AUTH = '026';
$Title = 'Schedule';

# setting show time
$min_date = 30; # min day
$max_date = 90; # max day

// echo $PHP_action.'<br>';
switch ($PHP_action) {
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "main":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "main":
check_authority($AUTH,"view");
//print_r($GLOBALS);

$SubTitle = 'Main';
$op['css'] = array( 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/schedule.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' );

if ( if_factory() ) {
    $op['fty_select'] = $arry2->select(array($GLOBALS['SCACHE']['ADMIN']['dept']),$GLOBALS['SCACHE']['ADMIN']['dept'],'PHP_fty','select','');
} else {
    $op['fty_select'] = $arry2->select($FACTORY,'','PHP_fty','select','');
}

if(isset($PHP_msg))$op['msg'][] = $PHP_msg;

$op['pt_date'] = $TODAY;
$op['pt_dates'] = date('Ymd');
//print_r($op);
page_display($op,$AUTH,'schedule.html',$Title,$SubTitle);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "schedule_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_view":
check_authority($AUTH,"view");

if( empty($PHP_pt_date) || empty($PHP_fty) ){
    $_SESSION['MSG'][] = '請由正常頁面登入!';
    redirect_page("schedule.php?PHP_action=main");
}

// echo $PHP_pt_date.$PHP_fty;

$SubTitle = 'View';
$op['css'] = array( 'css/bom.css' , 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/schedule.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' );
$op['pt_date'] = $PHP_pt_date;
$op['pt_dates'] = date('Ymd', strtotime($PHP_pt_date));

$hd_ary = $_SESSION['hd_ary'][$PHP_fty];
// print_r($hd_ary);
$last_date = $schedule->get_last_date($PHP_fty);

$first_date = $PHP_pt_date;
$countDays = countDays($first_date,$last_date);
if( $countDays < $min_date ){
    $last_date = increceDaysInDate($first_date,$min_date);
    $countDays = $min_date;
}
if( $countDays > $max_date ){
    $last_date = increceDaysInDate($first_date,$max_date);
    $countDays = $max_date;
}

# Combine calendar string
$day_array = $Ym_array = array();
for($i=0;$i<$countDays+1;$i++){
    $date = increceDaysInDate($first_date,$i);
    $tmp_date = explode('-',$date);
    $weekday = date('w', strtotime($date));
    $Ym = $tmp_date[0].'-'.$tmp_date[1];
    $Y = $tmp_date[0];
    $m = $tmp_date[1];
    $d = $tmp_date[2];
    $Ym_array[$Ym]++;
    # week
    $week = ceil(($d-$weekday)/7)+1;    
    $day_array['d'][] = $d;
    $day_array['wd'][] = $weekday;
    $day_array['bg'][] = ( $hd_ary[$Y][$m][$d] ) ? 'line_bg_holiday' : ( ( $weekday === '0' ) ? 'line_bg_sunday' : 'line_bg_w'.$week );
}
foreach($Ym_array as $key => $val){
    $day_array['Ym'][] = array( 'Ym' => $key , 'count' => $val ) ;
}
$op['calendar'] = $day_array;

# get schedule 
$op['schedule'] = $schedule->get_schedule($PHP_fty,$first_date,$last_date);
$op['ord_rec'] = $schedule->get_order($PHP_fty);
// $op['ln_rec'] = $schedule->get_by_mm( $PHP_fty, $PHP_pt_date,$lst_date);
$op['fty'] = $PHP_fty;
$op['msg'] = $schedule->msg->get(2);

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 

$op['limit_date'] = $max_date;
$op['pt_date'] = $PHP_pt_date;
$op['schedule_var'] = $schedule_var;

page_display($op,$AUTH,'schedule_view.html',$Title,$SubTitle);
 // $TPL_SCH_VIEW_SMALL
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "schedule_reload":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_reload":
check_authority('000',"view");

$schedule->schedule_reload($PHP_fty);
// echo '123';
// $redir_str = 'schedule.php?PHP_action=schedule_view_small&SCH_fty='.$SCH_fty.'&PHP_pt_date='.$PHP_pt_date;
// redirect_page($redir_str);
exit; 	




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "edit_mode":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "edit_mode":
check_authority($AUTH,"view");

$mode = $_POST['mode'] ? $_POST['mode'] : false ;
echo $_SESSION['edit_mode'] = $mode;

exit; 



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "get_order_info":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_order_info":
check_authority($AUTH,"view");

header("Content-Type:text/html;charset=BIG5");

if( !empty($_POST['fty']) && !empty($_POST['add_order']) ) {
    $order = explode('_',$_POST['add_order']);
    $order[1] = !empty($order[1])?$order[1]:'A';
	if ( $Str = $schedule->get_order_info( $_POST['fty'] , $order[0] , $order[1] ) ) {
		echo 'ok@'.(implode(',',$Str));
	} else {
		echo 'off@nodata'.$_POST['fty'].' , '.$_POST['add_order']; #.urlencode($_GET['add_order']).iconv("big5","ucs-2",$_GET['add_order'])
	}
} else {
    echo 'Error!';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "get_line_info":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_line_info":
check_authority($AUTH,"view");

header("Content-Type:text/html;charset=BIG5");

if( !empty($_POST['fty']) ) {
    // $sc = $_POST['radio'] == 'in' ? 0 : 1 ;
	if ( $line_value = $schedule->get_line_info( $_POST['fty'] ) ) {
		echo 'ok@'.$arry2->select_id($line_value,'','add_line','select');
	} else {
		echo 'off@nodata'.$_POST['fty'].' , '.$sc; #.urlencode($_GET['add_order']).iconv("big5","ucs-2",$_GET['add_order'])
	}
} else {
    echo 'Error!';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "get_line_date":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_line_date":
check_authority($AUTH,"view");

header("Content-Type:text/html;charset=BIG5");

if( !empty($_POST['line_id']) ) {
	if ( $ets = $schedule->get_line_date( $_POST['line_id'] ) ) {
		echo 'ok@'.$arry2->select($ets,'','add_ets','select');
	} else {
		echo 'off@nodata'.$_POST['fty'].' , '.$sc; #.urlencode($_GET['add_order']).iconv("big5","ucs-2",$_GET['add_order'])
	}
} else {
    echo 'Error!';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "add_order_Submit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "add_order_Submit":
check_authority($AUTH,"add");

$line = !empty($_POST['line'])? $_POST['line'] : '';
$line_id = !empty($_POST['line_id'])? $_POST['line_id'] : '';
$ord_num = !empty($_POST['ord_num'])? $_POST['ord_num'] : '';
$mks = !empty($_POST['mks'])? $_POST['mks'] : '';
$qty = !empty($_POST['qty'])? $_POST['qty'] : '';
$rel_ets = !empty($_POST['rel_ets'])? $_POST['rel_ets'] : '';
$fty = !empty($_POST['fty'])? $_POST['fty'] : '';
$des = !empty($_POST['des'])? uniDecode($_POST['des'],'big5') : '';
$p_id = !empty($_POST['p_id'])? $_POST['p_id'] : '';
$add_radio = !empty($_POST['add_radio'])? $_POST['add_radio'] : '';

$parm = array(
    'order_num'	=>	$ord_num,
    'factory'   => 	$fty
);

if (!$pdt = $order->get_pdtion($ord_num, $fty)){
	if (!$order->creat_ord_pdtion($parm)) {
		echo 'error@pdtion error!';
		break;	    
	}
}

$rel_ets = increceDaysInDate($rel_ets,-1);
$ie = $order->get_final_ie('',$ord_num);
$parm = array(
    'ord_num'	=>	$ord_num,
    'p_id'		=>	$p_id,
    'fty'       => 	$fty,
    'line_id'	=>	$line_id,
    'qty'	    =>	$qty,
    'su'	    =>	$qty*$ie,
    'des'       =>	$des,
    'ets'	    =>	$rel_ets
);
$schedule->add_schedule($parm);

# `STATUS`
$schedule->chk_pdc_status($PHP_ord_num);

$message = "ADD order [ ".$ord_num.$mks." ] schedule for  line [".$line."] , QTY [".$qty."] , ETS [".$rel_ets."] ";
$log->log_add(0,$AUTH."A",$message);

$str = '';
$waitting_array = $schedule->get_waitting_line_id_val($line_id);
$new_sch_array = array();
foreach( $waitting_array as $key => $val ) {
    $new_sch_array[] = $val;
}

ksort($new_sch_array);
$ets = $schedule->get_pdtion_rel_etf_last_date($line_id);
$line_avg = $schedule->reset_line_avg($line_id,0);

$i = 0 ;
foreach( $new_sch_array as $key => $val ) {
    $worker = $val['worker'];
    $worker = empty($worker)?30:$worker;
    # 抓出每組的平均產能
    $avg = $line_avg * $worker;
    # 計算剩餘 SU / 計算生產時間 / 無條件進位
    $w_day = $schedule->get_work_day($val['su'],$avg);
    // echo '<br>'.$key.'~';
    if( $i == 0 ) {
        $Ets = $ets;
        $TMP_ets = $Etf = $schedule->increceDay($Ets,$w_day,$val['fty']);
    } else {
        $Ets = $TMP_ets;
        $TMP_ets = $Etf = $schedule->increceDay($TMP_ets,$w_day,$val['fty']);
    }
    # `schedule`        `rel_ets`,`rel_etf`
    $schedule->mdf_schedule_move_date($val['id'],$Ets,$Etf);
    # `order_partial`   `p_fty_su`,`p_ets`,`p_etf`,`ext_period`
    $schedule->mdf_partial($val['p_id'],0);
    # `pdtion`          `fty_su`,`ext_period`
    $schedule->mdf_pdtion($val['ord_num']);
    $i++;
}
echo 'ok@';
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "schedule_append_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_append_edit":
check_authority($AUTH,"view");


if( empty($PHP_pt_date) ){
    $PHP_pt_date = date('Y-m-d');
}

if( empty($PHP_pt_date) || empty($PHP_fty) ){
    $_SESSION['MSG'][] = '請由正常頁面登入!';
    redirect_page("schedule.php?PHP_action=main");
}

// echo $PHP_pt_date.$PHP_fty;

$SubTitle = 'append/edit';
$op['css'] = array( 'css/bom.css' , 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' , 'css/jquery-autocomplete.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/schedule.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' , 'js/jquery-autocomplete.js' );
$op['today'] = date('Ymd');
$op['pt_date'] = $PHP_pt_date;
$op['pt_dates'] = date('Ymd', strtotime($PHP_pt_date));

$hd_ary = $_SESSION['hd_ary'][$PHP_fty];
// print_r($hd_ary[2015]);
$last_date = $schedule->get_last_date($PHP_fty);

$first_date = $PHP_pt_date;
$countDays = countDays($first_date,$last_date);
if( $countDays < $min_date ){
    $last_date = increceDaysInDate($first_date,$min_date);
    $countDays = $min_date;
}
if( $countDays > $max_date ){
    $last_date = increceDaysInDate($first_date,$max_date);
    $countDays = $max_date;
}

# Combine calendar string
$day_array = $Ym_array = array();
for($i=0;$i<$countDays+1;$i++){
    $date = increceDaysInDate($first_date,$i);
    $tmp_date = explode('-',$date);
    $weekday = date('w', strtotime($date));
    $Ym = $tmp_date[0].'-'.$tmp_date[1];
    $Y = $tmp_date[0];
    $m = $tmp_date[1];
    $d = $tmp_date[2];
    $Ym_array[$Ym]++;
    # week
    $week = ceil(($d-$weekday)/7)+1;    
    $day_array['d'][] = $d;
    $day_array['wd'][] = $weekday;
    $day_array['bg'][] = ( $hd_ary[$Y][$m][$d] ) ? 'line_bg_holiday' : ( ( $weekday === '0' ) ? 'line_bg_sunday' : 'line_bg_w'.$week );
    
}

foreach($Ym_array as $key => $val){
    $day_array['Ym'][] = array( 'Ym' => $key , 'count' => $val ) ;
}
$op['calendar'] = $day_array;
// print_r($day_array);
# get schedule 
$op['schedule'] = $schedule->get_schedule($PHP_fty,$first_date,$last_date);
$op['ln_rec'] = $schedule->get_by_mm( $PHP_fty, $PHP_pt_date,$lst_date);
// $op['con_rec'] = $schedule->get_contract( $PHP_fty, $PHP_pt_date,$lst_date);
// $op['ord_rec'] = $schedule->get_order($PHP_fty);
// $op['year_select'] =  $arry2->select($YEAR_WORK,date('Y'),'SCH_year','select');
// $op['month_select'] =  $arry2->select($MONTH_WORK,date('m'),'SCH_month','select');
$op['fty'] = $PHP_fty;
$op['msg'] = $schedule->msg->get(2);

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 

$op['pt_date'] = $PHP_pt_date;
$op['schedule_var'] = $schedule_var;
$op['edit_mode'] = empty($_SESSION['edit_mode'])?'false':$_SESSION['edit_mode'];

$Str = $schedule->get_schedule_num( $PHP_fty );
$op['available'] = implode(',',$Str);
// print_r($op);
$op['limit_date'] = $max_date;

page_display($op,$AUTH,'schedule_append_edit.html',$Title,$SubTitle);
 // $TPL_SCH_VIEW_SMALL
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "schedule_finish":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_finish":
check_authority($AUTH,"edit");

$ord_num = !empty($_POST['ord_num'])? $_POST['ord_num'] : '';
$mks = !empty($_POST['mks'])? $_POST['mks'] : '';
$s_id = !empty($_POST['s_id'])? $_POST['s_id'] : '';
$p_id = !empty($_POST['p_id'])? $_POST['p_id'] : '';
$line_id = !empty($_POST['line_id'])? $_POST['line_id'] : '';
$line = !empty($_POST['line'])? $_POST['line'] : '';

if( $s_id && $p_id && $ord_num && $line_id ){

    if( $schedule->chk_saw_out_put($s_id) ){
        $schedule->set_schedule_status($s_id);
        $schedule->set_schedule_ets_etf($s_id);
        $schedule->mdf_partial($p_id);
        $schedule->mdf_pdtion($ord_num);
        $schedule->schedule_reset_pdtion_waitting($line_id);
        echo 'ok !';
        
        $message = "Finish order [ ".$ord_num.$mks." ] schedule for  line [".$line."] ";
        $log->log_add(0,$AUTH."F",$message);
    } else {
        echo 'No output record !';
    }

} else {
    echo 'Data error !';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "schedule_reopen":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_reopen":
check_authority($AUTH,"edit");

$ord_num = !empty($_POST['ord_num'])? $_POST['ord_num'] : '';
$mks = !empty($_POST['mks'])? $_POST['mks'] : '';
$s_id = !empty($_POST['s_id'])? $_POST['s_id'] : '';
$p_id = !empty($_POST['p_id'])? $_POST['p_id'] : '';
$line_id = !empty($_POST['line_id'])? $_POST['line_id'] : '';

if( $s_id && $p_id && $ord_num && $line_id ){
    $schedule->set_schedule_status($s_id,0);
    $schedule->set_schedule_ets_etf($s_id);
    $schedule->mdf_partial($p_id,0);
    $schedule->mdf_pdtion($ord_num);
    $schedule->schedule_reset_pdtion_waitting($line_id);
    echo 'ok !';
    
    $message = "Reopen order [ ".$ord_num.$mks." ] schedule for  line [".$line."] ";
    $log->log_add(0,$AUTH."R",$message);
} else {
    echo 'Data error !';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "schedule_delete":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_delete":
check_authority($AUTH,"view");

$ord_num = !empty($_POST['ord_num'])? $_POST['ord_num'] : '';
$mks = !empty($_POST['mks'])? $_POST['mks'] : '';
$s_id = !empty($_POST['s_id'])? $_POST['s_id'] : '';
$p_id = !empty($_POST['p_id'])? $_POST['p_id'] : '';
$line_id = !empty($_POST['line_id'])? $_POST['line_id'] : '';

if( $s_id && $p_id && $ord_num && $line_id ){

    if( $schedule->chk_saw_out_put($s_id) ){
        echo 'No output record !';
    } else {
        $schedule->delete_schedule($s_id);
        $schedule->mdf_partial($p_id,0);
        $schedule->mdf_pdtion($ord_num);
        $schedule->schedule_reset_pdtion_waitting($line_id);
        echo 'ok !['.$p_id.']';
    
        $message = "Delete order [ ".$ord_num.$mks." ] schedule for  line [".$line."] ";
        $log->log_add(0,$AUTH."R",$message);
    }

} else {
    echo 'Data error !';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "schedule_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_update":
check_authority($AUTH,"view");

$ord_num = !empty($_POST['ord_num'])? $_POST['ord_num'] : '';
$mks = !empty($_POST['mks'])? $_POST['mks'] : '';
$s_id = !empty($_POST['s_id'])? $_POST['s_id'] : '';
$p_id = !empty($_POST['p_id'])? $_POST['p_id'] : '';
$line_id = !empty($_POST['line_id'])? $_POST['line_id'] : '';

$ie = !empty($_POST['ie'])? $_POST['ie'] : '';
$s_out_qty = !empty($_POST['s_out_qty'])? $_POST['s_out_qty'] : '';
$s_sch_qty = !empty($_POST['s_sch_qty'])? $_POST['s_sch_qty'] : '';
$pre_ets = !empty($_POST['pre_ets'])? $_POST['pre_ets'] : '';
$pre_etf = !empty($_POST['pre_etf'])? $_POST['pre_etf'] : '';
$des = !empty($_POST['des'])? $_POST['des'] : '';
// echo $ie.','.$s_sch_qty.','.$pre_ets.','.$pre_etf.','.$des;
if( $s_id && $p_id && $ord_num && $line_id ){

    if( $s_out_qty > $s_sch_qty ){
        echo 'output over schedule qty !';
    } else {
        $schedule->update_schedule($s_id,$s_sch_qty,($s_sch_qty*$ie),$pre_ets,$pre_etf,uniDecode($des,'big5'));
        // $schedule->mdf_partial($p_id,0);
        // $schedule->mdf_pdtion($ord_num);
        $schedule->schedule_reset_pdtion_waitting($line_id);
        echo 'ok !['.$p_id.']';
    
        $message = "Update order [ ".$ord_num.$mks." ] schedule for  line [".$line."] ";
        $log->log_add(0,$AUTH."R",$message);
    }

} else {
    echo 'Data error !';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "schedule_move":	 no pdtion
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_move":
check_authority($AUTH,"add");

// echo 'Line:'.$_POST['line_down'].' s_id:['.$_POST['s_id_down'].']p_id:['.$_POST['p_id_down'].'] ~ Line:'.$_POST['line_up'].' s_id:['.$_POST['s_id_up'].']p_id:['.$_POST['p_id_up'].']'.$_POST['ms_status'];

$line_id_down = $_POST['line_id_down'];
$line_id_up = $_POST['line_id_up'];
$line_down = $_POST['line_down'];
$line_up = $_POST['line_up'];
$s_id_down = $_POST['s_id_down'];
$s_id_up = $_POST['s_id_up'];
$msg = $_POST['msg'];
$ms_status = $_POST['ms_status'];

$p_id_down = $_POST['p_id_down'];
$p_id_up = $_POST['p_id_up'];

if ( $line_id_down == $line_id_up ) {
    # 相同
    $waitting_array = $schedule->get_waitting_line_id_val($line_id_down);
    if ( $ms_status == 1 ){
        $i = 1;
        $new_sch_array = array();
        foreach( $waitting_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
                $new_sch_array[0] = $val;
            } else {
                $new_sch_array[$i] = $val;
                $i++;
            }
        }
    } else if ( $ms_status == 2 ) {
        $new_sch_array = array();
        $after_array = array();
        foreach( $waitting_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
                $after_array = $val;
            }
        }
        foreach( $waitting_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
            } else if ( $s_id_up == $val['id'] ) {
                $new_sch_array[] = $val;
                $new_sch_array[] = $after_array;
            } else {
                $new_sch_array[] = $val;
            }
        }
    } else if ( $ms_status == 3 ) {
        $new_sch_array = array();
        $last_array = array();
        foreach( $waitting_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
                $last_array = $val;
            } else {
                $new_sch_array[] = $val;
            }
        }
        $new_sch_array[] = $last_array;
    } else {
    }
    ksort($new_sch_array);
    $ets = $schedule->get_pdtion_rel_etf_last_date($line_id_down);
    $line_avg = $schedule->reset_line_avg($line_id_down,0);
    // echo '<br>'.$ets.'~';
    // print_r($waitting_array);
    $i = 0 ;
    foreach( $new_sch_array as $key => $val ) {
        // echo $val['ord_num'].'+11'.$val['id'].'/';
        
        $worker = $val['worker'];
        $worker = empty($worker)?30:$worker;
        # 抓出每組的平均產能
        $avg = $line_avg * $worker;
        # 計算剩餘 SU / 計算生產時間 / 無條件進位
        $w_day = $schedule->get_work_day($val['su'],$avg);
        // echo '<br>'.$key.'~';
        if( $i == 0 ) {
            $Ets = $ets;
            $TMP_ets = $Etf = $schedule->increceDay($Ets,$w_day,$val['fty']);
        } else {
            $Ets = $TMP_ets;
            $TMP_ets = $Etf = $schedule->increceDay($TMP_ets,$w_day,$val['fty']);
        }
        # `schedule`        `rel_ets`,`rel_etf`
        $schedule->mdf_schedule_move_date($val['id'],$Ets,$Etf);
        # `order_partial`   `p_fty_su`,`p_ets`,`p_etf`,`ext_period`
        $schedule->mdf_partial($val['p_id'],0);
        # `pdtion`          `fty_su`,`ext_period`
        $schedule->mdf_pdtion($val['ord_num']);
        $i++;
    }
    
} else {
    # 不同
    # DOWN
    $waitting_down_array = $schedule->get_waitting_line_id_val($line_id_down);
    $new_sch_array = array();
    foreach( $waitting_down_array as $key => $val ) {
        if( $s_id_down == $val['id'] ) {
            $q_str = "UPDATE `schedule` SET `line_id` = '".$line_id_up."' WHERE `id` = '".$val['id']."' ;";
            mysql_query($q_str);
        } else {
            $new_sch_array[] = $val;
        }
    }
    $ets_down = $schedule->get_pdtion_rel_etf_last_date($line_id_down);
    $line_avg_down = $schedule->reset_line_avg($line_id_down,0);

    $i = 0 ;
    foreach( $new_sch_array as $key => $val ) {

        $worker = $val['worker'];
        $worker = empty($worker)?30:$worker;
        $avg = $line_avg_down * $worker;
        $w_day = $schedule->get_work_day($val['su'],$avg);
        if( $i == 0 ) {
            $Ets = $ets_down;
            $TMP_ets = $Etf = $schedule->increceDay($Ets,$w_day,$val['fty']);
        } else {
            $Ets = $TMP_ets;
            $TMP_ets = $Etf = $schedule->increceDay($TMP_ets,$w_day,$val['fty']);
        }
        # `schedule`        `rel_ets`,`rel_etf`
        $schedule->mdf_schedule_move_date($val['id'],$Ets,$Etf);
        # `order_partial`   `p_fty_su`,`p_ets`,`p_etf`,`ext_period`
        $schedule->mdf_partial($val['p_id'],0);
        # `pdtion`          `fty_su`,`ext_period`
        $schedule->mdf_pdtion($val['ord_num']);
        $i++;
    }
    
    # UP
    $waitting_up_array = $schedule->get_waitting_line_id_val($line_id_up);
    if ( $ms_status == 1 ){
        $i = 1;
        $new_sch_array = array();
        foreach( $waitting_up_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
                $new_sch_array[0] = $val;
            } else {
                $new_sch_array[$i] = $val;
                $i++;
            }
        }
    } else if ( $ms_status == 2 ) {
        $new_sch_array = array();
        $after_array = array();
        foreach( $waitting_up_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
                $after_array = $val;
            }
        }
        foreach( $waitting_up_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
            } else if ( $s_id_up == $val['id'] ) {
                $new_sch_array[] = $val;
                $new_sch_array[] = $after_array;
            } else {
                $new_sch_array[] = $val;
            }
        }
    } else if ( $ms_status == 3 ) {
        $new_sch_array = array();
        $last_array = array();
        foreach( $waitting_up_array as $key => $val ) {
            if( $s_id_down == $val['id'] ) {
                $last_array = $val;
            } else {
                $new_sch_array[] = $val;
            }
        }
        $new_sch_array[] = $last_array;
    } else {
    }
    ksort($new_sch_array);
    $ets_up = $schedule->get_pdtion_rel_etf_last_date($line_id_up);
    $line_avg_up = $schedule->reset_line_avg($line_id_up,0);
    $i = 0 ;
    foreach( $new_sch_array as $key => $val ) {
        $worker = $val['worker'];
        $worker = empty($worker)?30:$worker;
        $avg = $line_avg_up * $worker;
        $w_day = $schedule->get_work_day($val['su'],$avg);
        if( $i == 0 ) {
            $Ets = $ets_up;
            $TMP_ets = $Etf = $schedule->increceDay($Ets,$w_day,$val['fty']);
        } else {
            $Ets = $TMP_ets;
            $TMP_ets = $Etf = $schedule->increceDay($TMP_ets,$w_day,$val['fty']);
        }
        # `schedule`        `rel_ets`,`rel_etf`
        $schedule->mdf_schedule_move_date($val['id'],$Ets,$Etf);
        # `order_partial`   `p_fty_su`,`p_ets`,`p_etf`,`ext_period`
        $schedule->mdf_partial($val['p_id'],0);
        # `pdtion`          `fty_su`,`ext_period`
        $schedule->mdf_pdtion($val['ord_num']);
        $i++;
    }
}
$log->log_add(0,$AUTH."E",$msg);
break;
























#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "schedule_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "schedule_add":
 	check_authority($AUTH,"add");
		
 		if(!isset($PHP_fty)) $PHP_fty = '';
 		if(isset($PHP_fty) && $PHP_fty)$sch_fty = $PHP_fty;
	
 		if(!$PHP_fty && !$sch_fty)
 		{
 			$msg = "please select factory first!";
 			$redir_str = 'schedule.php?PHP_action=pd_schedule&PHP_msg='.$msg;
			redirect_page($redir_str);
 		}
 		$str_date = date('Y-m').'-01';
 		// $str_date = date('Y-m-d');
		$late_date = $schedule->get_last_date($sch_fty);
		$date_ary = explode('-',$late_date);
		$days = getDaysInMonth($date_ary[1],$date_ary[0]);
		$late_date = $date_ary[0].'-'.$date_ary[1].$days;
		
 		$op['ln_rec'] = $schedule->get_by_mm($sch_fty, $str_date,$late_date);
 		$op['con_rec'] = $schedule->get_contract($sch_fty, $str_date,$late_date);
		
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
				$op['week_day'][] = date('w',strtotime(($yy.'-'.$mm.'-'.$i))); 
			}			
			$op['yy_mm'][] = $yy.'-'.$mm;
			$op['span_mm'][] = getDaysInMonth($mm,$yy);
			$mm++;
			if($mm < 10) $mm = '0'.$mm;
			if($mm == 13)
			{
				$mm = '01';
				$yy++;
			}
		}

/*
 		if(isset($PHP_fty) && $PHP_fty)$sch_fty = $PHP_fty;
 		if(isset($PHP_yy) && $PHP_yy)$sch_year = $PHP_yy;
 		if(isset($PHP_mm) && $PHP_mm)$sch_month = $PHP_mm;
 		$days = getDaysInMonth($sch_month,$sch_year); 		
 		$op['ln_rec'] = $schedule->get_by_mm($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
 		$op['con_rec'] = $schedule->get_contract($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
 */		

 		$ord_rec = $schedule->get_order($sch_fty);
		$ord_value = $ord_key = array();
		for($i=0; $i<sizeof($ord_rec); $i++)
		{
			$qty = $ord_rec[$i]['qty'] - $ord_rec[$i]['ext_qty'];
			$su = $ord_rec[$i]['su'] - $ord_rec[$i]['ext_su'];
			$ord_name = $ord_rec[$i]['order_num'];
			if($ord_rec[$i]['partial_num'] > 1)$ord_name.=$ord_rec[$i]['mks'];
			
			$ord_value[] =$ord_name." (".$qty." pcs ;  ".$su." su ; Styel : ".$ord_rec[$i]['style'].";  ETD : ".$ord_rec[$i]['etd']." )";
			$ord_key[] =$ord_rec[$i]['p_id'];
		}
		$op['ord_select'] =  $arry2->select($ord_value,'','PHP_p_id','select','',$ord_key);
		
		$line = $monitor->get_line($sch_fty);

		for($i=0; $i < sizeof($line); $i++)
		{
			if( $line[$i]['line'] <> 'contractor' ) {
				if($line[$i]['sc'] == 0 && $line[$i]['worker'] > 0 )$line_value[] = $line[$i]['line']."  (".$line[$i]['line_style'].")";
				if($line[$i]['sc'] == 0 && $line[$i]['worker'] > 0 )$line_key[] = $line[$i]['id'];
				
	//			if($line[$i]['sc'] == 1 )$ct_value[] = $line[$i]['line'];
	//			if($line[$i]['sc'] == 1 )$ct_key[] = $line[$i]['id'];
				// if($line[$i]['sc'] == 1 && $line[$i]['line'] == 'contractor')$op['ct_select'] = "<input type=hidden name='PHP_ct' value=".$line[$i]['id'].">";
			}
		}
		$op['line_select'] = $arry2->select($line_value,'','PHP_line','select',"get_ets('".$sch_fty."',this)",$line_key);
		$op['line_sp'] = $arry2->select($line_value,'','PHP_line_sp','select',"get_spl_ets('".$sch_fty."',this)",$line_key);
//		$op['ct_select'] = $arry2->select($ct_value,'','PHP_ct','select','',$ct_key);
		$op['line_change'] = $arry2->select($line_value,'','PHP_line','select',"get_chg_ets('".$sch_fty."',this)",$line_key);
		

		
//		$days = getDaysInMonth(date('m'),date('Y'));
/*
		for($i=1; $i <= $days; $i++)
		{
			$op['days'][] = $i;		
		}
*/
	

		$op['yy'] = $sch_year;
		$op['mm'] = $sch_month;
		$op['fty'] = $sch_fty;
		$op['span'] = $days - 10;
		
		$op['msg'] = $schedule->msg->get(2);
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 
		$op['today'] = $TODAY;
	page_display($op, $AUTH, $TPL_SCH_ADD,$Title,$SubTitle);
	
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "schedule_view_small":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_view_small":
check_authority($AUTH,"view");

if(!isset($PHP_fty)) $PHP_fty = '';
if(!$PHP_fty) $PHP_fty = 'LY';
$hd_ary = $_SESSION['hd_ary'][$PHP_fty];
if(!isset($PHP_pt_date) || !$PHP_pt_date)$PHP_pt_date = $TODAY;
$lst_date = $schedule->get_last_date($PHP_fty);
$tmp_lst = explode('-',$lst_date);
$tmp_now = explode('-',$PHP_pt_date);

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
    for($i=$str_day; $i <= $end_day; $i++) {
      $hd_mk=0;
      if(isset($hd_ary[$yy][$mm]))  //特殊假日
      {
        for($hi=0; $hi<sizeof($hd_ary[$yy][$mm]); $hi++)
        {
            if($hd_ary[$yy][$mm][$hi] == $i)
            {
                $op['week_day'][] = 0;
                $hd_mk =1;
            }
        }
      }
      if($hd_mk == 0)$op['week_day'][] = date('w',strtotime(($yy.'-'.$mm.'-'.$i)));
      
      if($i <= 10)
      {
        $op['days_color'][] = '#797979';	
      }else if($i <= 20){
            $op['days_color'][] = '#555555';	
        }else{
            $op['days_color'][] = '#003333';	
        }
        $op['days'][] = $k;			
        $k++;		
        
    }
    
    $op['yy_mm'][] = $yy.'-'.$mm;
    $op['span_mm'][] = ($end_day - $str_day + 1);
    $mm++;
    if($mm < 10) $mm = '0'.$mm;
    if($mm == 13)
    {
        $mm = '01';
        $yy++;
    }
}

$op['ln_rec'] = $schedule->get_by_mm( $PHP_fty, $PHP_pt_date,$lst_date);
$op['con_rec'] = $schedule->get_contract( $PHP_fty, $PHP_pt_date,$lst_date);
$op['ord_rec'] = $schedule->get_order($PHP_fty);
$op['year_select'] =  $arry2->select($YEAR_WORK,date('Y'),'SCH_year','select'); 
$op['month_select'] =  $arry2->select($MONTH_WORK,date('m'),'SCH_month','select'); 
$op['fty'] = $PHP_fty;
$sch_year = date('Y');
$sch_month = date('m');
$op['msg'] = $schedule->msg->get(2);
if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 
$op['today'] = $TODAY;
$op['pt_date'] = $PHP_pt_date;
$op['schedule_var'] = $schedule_var;

page_display($op, $AUTH, $TPL_SCH_VIEW_SMALL,$Title,$SubTitle);

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "schedule_un_finish":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_un_finish":
check_authority($AUTH,"view");
if(!$PHP_fty)
{
    $msg = "please select factory first!";
    $redir_str = 'schedule.php?PHP_action=main&PHP_msg='.$msg;
    redirect_page($redir_str);
}

$op['un_sch'] = $schedule->un_finish($PHP_fty);
$op['fty'] = $SCH_fty;
page_display($op, $AUTH, $TPL_SCH_UN_FINISH,$Title,$SubTitle);

break;
    
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_sch_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_sch_add":
check_authority($AUTH,"add");

$ptl_rec = $order->get_partial($PHP_p_id);
$new_id = $schedule->auto_schedule($PHP_p_id,$ptl_rec['ord_num'],$sch_fty);
$schedule->add_ord_schedule($PHP_p_id);

$s_ln = $schedule->get_ln_det($new_id);
if($schedule->check_sch_finish($PHP_p_id))
{
    $f1 = $schedule->add_capacity($PHP_p_id); 			
    if($f1)
    {
        $message = "UPDATE order [ ".$ptl_rec['ord_num']." ] production schedule [ETS, ETF] ";
        $log->log_add(0,"026A",$message);
    }
}
 
if($new_id)
{
    $message = "ADD order [ ".$ptl_rec['ord_num']." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
    $log->log_add(0,"026A",$message);
}else{
    $msg = $schedule->msg->get(2);
    $message = $msg[0];
}

$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$message;
redirect_page($redir_str);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "do_sch_point":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_sch_point":
check_authority($AUTH,"add");

if(!$PHP_line) {
    $msg = "please select production line first";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str);
}

if(!$schedule->check_qty($PHP_p_id,$PHP_sp_qty)) {
    $msg = "ORDER QTY must bigger than SCHEDULE  QTY";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str); 			
}

if( $PHP_sp_qty < 1 ) {
    $msg = "Please Check SCHEDULE QTY";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str); 			
}

$ptl_rec = $order->get_partial($PHP_p_id);

$PHP_ord_num = $ptl_rec['ord_num'];
// print_r($ptl_rec);
// exit;
$ie = $order->get_final_ie('',$PHP_ord_num);

if( empty($PHP_pt_ets) ) $PHP_pt_ets = date('Y-m-d');
$PHP_pt_ets = increceDaysInDate($PHP_pt_ets,-1);
$parm = array(
    'ord_num'	=>	$PHP_ord_num,
    'p_id'		=>	$PHP_p_id,
    'fty'       => 	$sch_fty,
    'line_id'	=>	$PHP_line,
    'qty'	    =>	$PHP_sp_qty,
    'des'       =>	$PHP_des,
    'ets'	    =>	$PHP_pt_ets
);
// print_r($parm);
// exit;


$s_id = $schedule->add_schedule($parm);

# `schedule`              `su`
$partial_arr = $schedule->mdf_line_ie($s_id);
// print_r($partial_arr);
// exit;
# `pdt_finish`            `su`,`cut_su`,`saw_su`,`pack_su` 此TABLE看起來是要整合訂單所有生產階段的完成狀態，目前看起來是沒有再用：直接乘數量帶SU
$pdt_finish->mdf_ie($PHP_ord_num,$ie);

# `order_partial`         `p_su`,`p_etp_su`,`p_fty_su`,`ext_su`  缺少重新計算 ext_period 生產天數：分年月FTY的SU，相同需要重排時間再計算SU
# 重制 partial 抓取 `schedule` 分佈的 `partial` 日期區間
$pdtion_arr = $order->mdf_partial_ie($partial_arr,$ie);

# `pdtion`                `etp_su 訂單`,`fty_su 排產`,`out_su 產出`,`ext_su`  `ext_su` 已移到 Partial 計算  此計算個階段總 su：分年月SU需要先重排時間再計算SU
# 重制 pdtion 
$pdtion->mdf_ie($pdtion_arr,$ie);

# `STATUS`
$schedule->chk_pdc_status($PHP_ord_num);
// exit;

// $message = "UPDATE order [ ".$ptl_rec['ord_num']." ] production schedule [ETS, ETF] ";
// $log->log_add(0,"026A",$message);

$message = "ADD order [ ".$parm['ord_num']." ]  schedule for QTY [".$parm['qty']."], ETS [".$parm['ets']."]";
$log->log_add(0,"026A",$message);

// $new_id = $schedule->point_schedule($parm);
// $order->update_field_num("status",6,$ptl_rec['ord_num']);
// $schedule->add_ord_schedule($PHP_p_id);

// $s_ln = $schedule->get_ln_det($new_id);
// if($schedule->check_sch_finish($PHP_p_id)) {
    // $f1 = $schedule->add_capacity($PHP_p_id); 			
    // if($f1) {
        // $message = "UPDATE order [ ".$ptl_rec['ord_num']." ] production schedule [ETS, ETF] ";
        // $log->log_add(0,"026A",$message);
    // }
// }

// if($new_id) {
    // $message = "ADD order [ ".$ptl_rec['ord_num']." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
    // $log->log_add(0,"026A",$message);
// } else {		
    // $msg = $schedule->msg->get(2);
    // $message = $msg[0];
// }

$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$message;
redirect_page($redir_str);

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "chg_month":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "chg_month":
 	check_authority($AUTH,"view");
		$sch_month += $PHP_chg;
		if($sch_month == 13) 
		{
			$sch_year ++;
			$sch_month = 1; 
		}
		
		if($sch_month == 0) 
		{
			$sch_year --;
			$sch_month = 12; 
		}			

		if($sch_month < 10) $sch_month ='0'.$sch_month;

		

		$redir_str ="schedule.php?PHP_action=".$PHP_action_to;
		redirect_page($redir_str);

	break;
	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "sch_line_spare":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "sch_line_spare":
check_authority($AUTH,"add");

$ord_status = $order->get_field_value('status','',$PHP_ord_num);
if($ord_status < 4) {
    $msg = "Order isn't approval yet. Can't move!";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str); 			
}

if(!$PHP_line_sp) {
    $msg = "please select production line first";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str);
}

if($ord_qty < $PHP_sp_qty) {
    $msg = "ORDER QTY must bigger than SCHEDULE QTY";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str);
}

$parm = array(
    'id'		=>	$PHP_id,
    'ord_num'	=>	$PHP_ord_num,
    'p_id'		=>	$PHP_p_id,
    'line_id'	=>	$PHP_line_id,
    'fty'		=> 	$sch_fty,
    'line_sp'	=>	$PHP_line_sp,
    'sp_qty'	=>	$PHP_sp_qty,
    'des'		=>	$PHP_des,
    'pt_ets'	=>	$PHP_pt_sp_ets,
);

$new_id = $schedule->spare_schedule($parm);
$schedule->add_ord_schedule($PHP_ord_num);

$s_ln = $schedule->get_ln_det($new_id);
if($schedule->check_sch_finish($PHP_p_id)) {
    $f1 = $schedule->add_capacity($PHP_p_id); 			
    if($f1) {
        $message = "UPDATE order [ ".$PHP_ord_num." ] production schedule [ETS, ETF] ";
        $log->log_add(0,"026A",$message);
    }
}
 
if($new_id) {
    $message = "SPARE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
    $log->log_add(0,"026A",$message);
} else {
    $msg = $schedule->msg->get(2);
    $message = $msg[0];			
}

$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$message;
redirect_page($redir_str);

break;	

	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "sch_line_change":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "sch_line_change":
check_authority($AUTH,"add");

$ord_status = $order->get_field_value('status','',$PHP_ord_num);

if($ord_status < 4) {
    $msg = "Order isn't approval yet. Can't move!";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str); 			
}

if(!$PHP_line) {
    $msg = "please select production line first";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str);
}

$parm = array(
    'ord_num'	=>	$PHP_ord_num,
    'p_id'		=>	$PHP_p_id,
    'fty'       => 	$sch_fty,
    'line_id'	=>	$PHP_line,
    'qty'	    =>	$PHP_sp_qty,
    'des'       =>	$PHP_des,
    'ets'	    =>	$PHP_pt_ets
);
// print_r($parm);

$s_id = $schedule->add_schedule($parm);

$new_id = $schedule->change_line($parm);
$schedule->add_ord_schedule($PHP_ord_num);

$s_ln = $schedule->get_ln_det($new_id);
if($schedule->check_sch_finish($PHP_p_id)) {
    $f1 = $schedule->add_capacity($PHP_p_id); 			
    if($f1) {
        $message = "UPDATE order [ ".$PHP_ord_num." ] production schedule [ETS, ETF] ";
        $log->log_add(0,"026A",$message);
    }
}
 
if($new_id) {
    $message = "CHANGE order [ ".$PHP_ord_num." ]  schedule for line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
    $log->log_add(0,"026A",$message);
} else {
    $msg = $schedule->msg->get(2);
    $message = $msg[0];
}

$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$message;
redirect_page($redir_str);

break;

	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sch_del":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "sch_del":
check_authority($AUTH,"add");

if( $_POST['rem_qty'] < $_POST['PHP_del_qty'] ) {
    $msg = "ORDER QTY must bigger than SCHEDULE QTY";
    $redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$msg;
    redirect_page($redir_str);
}
  
$ie = $order->get_final_ie('',$_POST['PHP_ord_num']);
$status = $schedule->del_schedule_qty($_POST['PHP_id'],$ie,$_POST['sch_qty'],$_POST['PHP_del_qty'],$_POST['PHP_p_id']);

# `schedule`              `su`
$partial_arr = $schedule->mdf_schedule_line($_POST['PHP_line_id']);

# `pdt_finish`            `su`,`cut_su`,`saw_su`,`pack_su` 此TABLE看起來是要整合訂單所有生產階段的完成狀態，目前看起來是沒有再用：直接乘數量帶SU
$pdt_finish->mdf_ie($_POST['PHP_ord_num'],$ie);

# `order_partial`         `p_su`,`p_etp_su`,`p_fty_su`,`ext_su`  缺少重新計算 ext_period 生產天數：分年月FTY的SU，相同需要重排時間再計算SU
# 重制 partial 抓取 `schedule` 分佈的 `partial` 日期區間
$pdtion_arr = $order->mdf_partial_ie($partial_arr,$ie);

# `pdtion`                `etp_su 訂單`,`fty_su 排產`,`out_su 產出`,`ext_su`  `ext_su` 已移到 Partial 計算  此計算個階段總 su：分年月SU需要先重排時間再計算SU
# 重制 pdtion 
if( $status == 'DELETE' ) $pdtion_arr[$_POST['PHP_ord_num']] = $_POST['PHP_ord_num'];
$pdtion->mdf_ie($pdtion_arr,$ie);

# `STATUS`
$schedule->chk_pdc_status($_POST['PHP_ord_num']);

if( $_POST['ord_qty'] == $_POST['PHP_del_qty'] ) {
    $message = "DELETE order [ ".$_POST['PHP_ord_num']." ]  schedule for  line [".$_POST['PHP_lines']."] , QTY [".$_POST['PHP_del_qty']."] ";
} else {
    $s_ln = $schedule->get_ln_det($_POST['PHP_id']);
    $message = "DELETE order [ ".$_POST['PHP_ord_num']." ]  schedule for  line [".$_POST['PHP_lines']."] , QTY [".$_POST['PHP_del_qty']."], ETS [".$s_ln['rel_ets']."],  ETF[".$s_ln['rel_etf']."]";
}

$log->log_add(0,"026A",$message);

$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$message;
redirect_page($redir_str);

break;
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "schedule_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_view":
check_authority($AUTH,"view");

if(!isset($SCH_fty)) $SCH_fty = '';
if(isset($PHP_first) &&  !$SCH_yy) $SCH_yy = date('Y');
if(isset($PHP_first) &&  !$SCH_mm) $SCH_mm = date('m');

if(!$SCH_fty && !$sch_fty){
    $msg = "please select factory first!";
    $redir_str = 'schedule.php?PHP_action=pd_schedule&PHP_msg='.$msg;
    redirect_page($redir_str);
}

if(isset($SCH_fty) && $SCH_fty)$sch_fty = $SCH_fty;
if(isset($SCH_yy) && $SCH_yy)$sch_year = $SCH_yy;
if(isset($SCH_mm) && $SCH_mm)$sch_month = $SCH_mm;
$days = getDaysInMonth($sch_month,$sch_year);

$op['ln_rec'] = $schedule->get_by_mm($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
$op['con_rec'] = $schedule->get_contract($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
$limit_date=$sch_year.'-'.$sch_month.'-31';
$op['ord_rec'] = $schedule->get_order($sch_fty,$limit_date);
for($i=1; $i <= $days; $i++) {
    $op['days'][] = $i;
}

$op['yy'] = $sch_year;
$op['mm'] = $sch_month;
$op['fty'] = $sch_fty;
$op['span'] = $days - 10;
$op['today'] = $TODAY;
$op['msg'] = $schedule->msg->get(2);
if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op, $AUTH, $TPL_SCH_VIEW,$Title,$SubTitle);
break;



	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_contractor_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_contractor_add":
check_authority($AUTH,"add");

# 外發 contractor
$line_id = $schedule->get_contractor($sch_fty);

$ptl_rec = $order->get_partial($PHP_p_id); 	
$parm = array(
	'p_id'			=>	$PHP_p_id,
	'ord'			=>	$ptl_rec['ord_num'],
	'ets'			=>	$PHP_ets,
	'etf'			=>	$PHP_etf,
	'qty'			=>	$PHP_qty,
	'line_id'		=>	$line_id,
	'fty'			=>	$sch_fty,
	'des'			=>	$PHP_con_des
);

$new_id = $schedule->contractor_schedule($parm);

$schedule->add_ord_schedule($PHP_p_id);

$s_ln = $schedule->get_ln_det($new_id);
if($schedule->check_sch_finish($PHP_p_id))
{
	$f1 = $schedule->add_capacity($PHP_p_id);
	if($f1)
	{
		$message = "UPDATE order [ ".$ptl_rec['ord_num']." ] production schedule [ETS, ETF] ";
		$log->log_add(0,"026A",$message);
	}
}

if($new_id)
{
	$message = "UPDATE order [ ".$ptl_rec['ord_num']." ]  contractor schedule for line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$PHP_ets."],  ETF[".$PHP_etf."]";
	$log->log_add(0,"026A",$message);
}else{		
	$msg = $schedule->msg->get(2);
	$message = $msg[0];	
} 		 

$redir_str ="schedule.php?PHP_action=schedule_add&PHP_msg=".$message;
redirect_page($redir_str);

break;
exit;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pd_schedule":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_pd_schedule":
 	check_authority(4,3,"view");

 		$op['fty_sch_select'] =  $arry2->select($FACTORY,'','PHP_fty','select','');
 		$op['year_select'] =  $arry2->select($YEAR_WORK,date('Y'),'PHP_yy','select','');
		$op['month_select'] =  $arry2->select($MONTH_WORK,'','PHP_mm','select','');


 		if($GLOBALS['SCACHE']['ADMIN']['dept'] == 'HJ' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'LY' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'CF')
 		{
 			$op['fty_sch_select'] = $GLOBALS['SCACHE']['ADMIN']['dept']."<input type=hidden name=PHP_fty value=".$GLOBALS['SCACHE']['ADMIN']['dept'].">";
 		}
	
	
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		$op['year'] = date('Y');
		$op['month'] = date('m');
		$op['today'] = $TODAY;
	page_display($op, 4,3, $TPL_SCH_CFM,$Title,$SubTitle);
	
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_sch_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_sch_edit":
 	check_authority($AUTH,"add");
 		if(!isset($PHP_fty)) $PHP_fty = '';

 		if(!$PHP_fty && !$sch_fty)
 		{
 			$msg = "please select factory first!";
 			$redir_str = 'schedule.php?PHP_action=cfm_pd_schedule&PHP_msg='.$msg;
			redirect_page($redir_str);
 		}
 		if(isset($PHP_first) && $PHP_first == 1)
 		{
 			if(!$PHP_yy)$PHP_yy = date('Y');
 			if(!$PHP_mm)$PHP_mm = date('m');
 			$sch_fty = $PHP_fty;
 			$sch_year = $PHP_yy;
 			$sch_month = $PHP_mm;
 		}

 		$days = getDaysInMonth($sch_month,$sch_year);
 		
 		$op['ln_rec'] = $schedule->get_by_mm($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
 		$op['con_rec'] = $schedule->get_contract($sch_fty, ($sch_year.'-'.$sch_month),($sch_year.'-'.$sch_month.'-'.$days));
 		
 		
 		$ord_rec = $schedule->get_order($sch_fty);
		$ord_value = $ord_key = array();
		for($i=0; $i<sizeof($ord_rec); $i++)
		{
			$su = $ord_rec[$i]['su'] - $ord_rec[$i]['ext_su'];
			if($ord_rec[$i]['mat_eta'] > $ord_rec[$i]['m_acc_eta'])
			{
				$eta = $ord_rec[$i]['mat_eta'];
			}else{
				$eta = $ord_rec[$i]['m_acc_eta'];
			}
			
			$ord_value[] =$ord_rec[$i]['order_num']." (".$su." su ; ETA : ".$eta." )";
			$ord_key[] =$ord_rec[$i]['order_num'];
		}
		$op['ord_select'] =  $arry2->select($ord_value,'','PHP_p_id','select','',$ord_key);
		
		$line = $monitor->get_line($sch_fty);
		for($i=0; $i < sizeof($line); $i++)
		{
			if($line[$i]['worker'] > 0 )$line_value[] = $line[$i]['line']."  (".$line[$i]['line_style'].")";
			if($line[$i]['worker'] > 0 )$line_key[] = $line[$i]['id'];
			
			// if($line[$i]['worker'] == 0 )$ct_value[] = $line[$i]['line'];
			// if($line[$i]['worker'] == 0 )$ct_key[] = $line[$i]['id'];
		}
		$op['line_select'] = $arry2->select($line_value,'','PHP_line','select',"get_edit_ets('".$sch_fty."',this)",$line_key);
		$op['line_sp'] = $arry2->select($line_value,'','PHP_line_sp','select',"get_edit_ets('".$sch_fty."',this)",$line_key);
//		$op['ct_select'] = $arry2->select($ct_value,'','PHP_ct','select','',$ct_key);


		for($i=1; $i <= $days; $i++) $op['days'][] = $i;
	

		$op['yy'] = $sch_year;
		$op['mm'] = $sch_month;
		$op['fty'] = $sch_fty;
		$op['span'] = $days - 10;
		$op['msg'] = array();
		$op['msg'] = $schedule->msg->get(2);
		$op['today'] = $TODAY;
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 
		$op['today'] = $TODAY;
	page_display($op, $AUTH, $TPL_SCH_CFM_EDIT,$Title,$SubTitle);
	
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_sch_line_change":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "cfm_sch_line_change":
check_authority($AUTH,"add");

if(!$PHP_line)
{
    $msg = "please select production line first";
    $redir_str ="schedule.php?PHP_action=cfm_sch_edit&PHP_msg=".$msg;
    redirect_page($redir_str);
}

$parm = array(
    'org_id'		=>	$PHP_id,
    'ord_num'		=>	$PHP_ord_num,
    'fty'			=> 	$sch_fty,
    'new_line'	    =>	$PHP_line,
    'des'			=> 	$PHP_des,
    'pt_ets'		=>	$PHP_pt_ets,
);


$new_id = $schedule->change_line($parm);
$schedule->add_ord_schedule($PHP_ord_num);

$s_ln = $schedule->get_ln_det($new_id);
if($schedule->check_sch_finish($PHP_p_id))
{
    $schedule->add_capacity($PHP_ord_num);
    $message = "UPDATE order [ ".$PHP_ord_num." ] production schedule [ETS, ETF] ";
    $log->log_add(0,"026A",$message);
}
 
if($new_id)
{
    $message = "CHANGE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
    $log->log_add(0,"026A",$message);
}else{		
    $msg = $schedule->msg->get(2);
    $message = $msg[0];
}		
$redir_str ="schedule.php?PHP_action=cfm_sch_edit&PHP_msg=".$message;
redirect_page($redir_str);

break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_sch_line_spare":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_sch_line_spare":
 	check_authority($AUTH,"add");
 		
 		if(!$PHP_line_sp)
 		{
 			$msg = "please select production line first";
			$redir_str ="schedule.php?PHP_action=cfm_sch_edit&PHP_msg=".$msg;
			redirect_page($redir_str);
 		}
 		
		$parm = array('id'			=>	$PHP_id,
									'ord_num'	=>	$PHP_ord_num,
									'line_id'	=>	$PHP_line_id,
									'fty'			=> 	$sch_fty,
									'line_sp'	=>	$PHP_line_sp,
									'sp_qty'	=>	$PHP_sp_qty,
									'des'			=>	$PHP_des,
									'pt_ets'	=>	$PHP_pt_ets,
									);


 		$new_id = $schedule->spare_schedule($parm);
 		$schedule->add_ord_schedule($PHP_ord_num);

		$s_ln = $schedule->get_ln_det($new_id);
 		if($schedule->check_sch_finish($PHP_p_id))
 		{
 			$schedule->add_capacity($PHP_ord_num);
			$message = "UPDATE order [ ".$PHP_ord_num." ] production schedule [ETS, ETF] ";
			$log->log_add(0,"026A",$message);
 		}
 		 

 		if($new_id)
 		{
			$message = "SPARE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
			$log->log_add(0,"026A",$message);
		}else{		
			$msg = $schedule->msg->get(2);
			$message = $msg[0];
		}
		$redir_str ="schedule.php?PHP_action=cfm_sch_edit&PHP_msg=".$message;
		redirect_page($redir_str);
	
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_sch_del":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_sch_del":
 	check_authority($AUTH,"add");
 		
		$message = "DELETE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$PHP_del_qty."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
		$log->log_add(0,"026A",$message);

 		$schedule->del_schedule($PHP_id,$PHP_del_qty,$PHP_ord_num);
 		$schedule->add_ord_schedule($PHP_ord_num);

		$redir_str ="schedule.php?PHP_action=cfm_sch_edit&PHP_msg=".$message;
		redirect_page($redir_str);
	
	break;			
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_cfm_sch":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_cfm_sch":
 	check_authority($AUTH,"add");


		$parm = array('field_name'	=>	'status',
									'field_value'	=>	2,
									'id'					=>	$PHP_id
									);
		$s_ln = $schedule->get_ln_det($PHP_id);

 		$schedule->update_field($parm);
 		if($schedule->check_sch_finish($PHP_ord_num))
 		{
 			$schedule->add_capacity($PHP_ord_num);
			$message = "Confirm order [ ".$PHP_ord_num." ] production schedule [ETS, ETF] ";
			$log->log_add(0,"37A",$message);
 		}
 		 
			$message = "Confirm order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$s_ln['qty']."]";
			$log->log_add(0,"37A",$message);

		$redir_str ="schedule.php?PHP_action=cfm_sch_edit&PHP_msg=".$message;
		redirect_page($redir_str);
	
	break;		
	
	
	/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sch_ord_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "sch_ord_view":

		check_authority($AUTH,"view");
		
		$op['order'] = $order->schedule_get($PHP_id);  //取出該筆記錄
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = $op['order'];

		// 計算 lead time
		$op['lead_time'] = countDays ($parm['etp'],$parm['etd']);
		$op['id'] = $PHP_id;


			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// 計算  SU 供 html -----------------------------
		//   2005/08/31 改由資料庫抓 su [以 ie 計算填入 ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');
		$op['schedule'] = $schedule->order_get($op['order']['order_num']);  //取出該筆記錄
		$ets = '9999-99-99';  $etf = '0000-00-00';
		for($i=0; $i<sizeof($op['schedule']); $i++)
		{
			if($op['schedule'][$i]['rel_ets'] < $ets )$ets = $op['schedule'][$i]['rel_ets'];
			if($op['schedule'][$i]['rel_etf'] > $etf )$etf = $op['schedule'][$i]['rel_etf'];
		}
	
	include_once($config['root_dir']."/lib/src/jpgraph.php");
	include_once($config['root_dir']."/lib/src/jpgraph_gantt.php");
	
		
	// A new graph with automatic size 
	$graph  = new GanttGraph (0,0, "auto"); 
	$graph->title->Set("ORDER PROGRESS Chart");
$graph->ShowHeaders(GANTT_HWEEK | GANTT_HDAY | GANTT_HMONTH | GANTT_HYEAR);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY2);

	//  A new activity on row '0' 
	for($i=0; $i<sizeof($op['schedule']); $i++)
	{
		$activity[$i]  = new GanttBar ($i,$op['schedule'][$i]['line_name'], $op['schedule'][$i]['rel_ets'], $op['schedule'][$i]['rel_etf']); 
		if($op['order']['status'] > 8){
			$activity[$i] ->SetPattern(BAND_RDIAG, "#D7D7FF"); 
			$activity[$i] ->SetFillColor ("#D7D7FF"); 			
		}else if($op['schedule'][$i]['rel_etf'] > $op['order']['etd']){
			$activity[$i] ->SetPattern(BAND_RDIAG, "#FD1838"); 
			$activity[$i] ->SetFillColor ("#FD1838"); 
		}else if($op['order']['smpl_apv'] == '0000-00-00' && !$op['order']['mat_shp'] && !$op['order']['m_acc_shp']){
			$activity[$i] ->SetPattern(BAND_RDIAG, "#FE00B9"); 
			$activity[$i] ->SetFillColor ("#FE00B9"); 
		}else if($op['order']['smpl_apv'] == '0000-00-00' && (!$op['order']['mat_shp'] || !$op['order']['m_acc_shp'])){					
			$activity[$i] ->SetPattern(BAND_RDIAG, "#FF6F43"); 
			$activity[$i] ->SetFillColor ("#FF6F43"); 
		}else if( !$op['order']['mat_shp'] && !$op['order']['m_acc_shp']){					
			$activity[$i] ->SetPattern(BAND_RDIAG, "#FF63E0"); 
			$activity[$i] ->SetFillColor ("#FF63E0"); 
		}else if( !$op['order']['mat_shp'] && !$op['order']['m_acc_shp']){					
			$activity[$i] ->SetPattern(BAND_RDIAG, "#FFC5F3"); 
			$activity[$i] ->SetFillColor ("#FFC5F3"); 
		}else if($op['order']['status'] >= '8'){					
			$activity[$i] ->SetPattern(BAND_RDIAG, "#D0FDCD"); 
			$activity[$i] ->SetFillColor ("#D0FDCD"); 
		}else if(group_order_style($op['order']['style']) != $op['schedule'][$i]['style']){
			$activity[$i] ->SetPattern(BAND_RDIAG, "#F8FF84"); 
			$activity[$i] ->SetFillColor ("#F8FF84"); 
		}else{
			$activity[$i] ->SetPattern(BAND_RDIAG, "#FFFFFF"); 
			$activity[$i] ->SetFillColor ("#DDDDDD"); 
		}	
		$graph->Add($activity[$i]); 

	}
	$op['order']['chart'] = $graph->Stroke('picture/schedule_out.png');		
		
		

		
		$op['today'] = $TODAY;
	page_display($op, $AUTH, $TPL_SCH_ORD_SHOW,$Title,$SubTitle);    
	break;
	*/
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-1-4     訂單 產出量記錄 報表
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "sch_ord_view":
check_authority($AUTH,"view");

$op['ord_num'] = $PHP_ord_num;
$daily_out = $result = array();
// $schedule->count_pdt_qty($PHP_ord_num);

if (!$ord = $order->get_ord_output($PHP_ord_num)) {   
    $op['msg']= $daily->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

$op['order'] = $ord;
$op['order']['order_num'] = $PHP_ord_num;			
    
$ttl_qty = $ttl_su = 0;
$plan_days = $schedule_days = $progress_days = 0;
$est_fdate = ''; // 估算完成日期
$est_period = 0; // 估算總生產時程(日)

// GANTT CHART 參數設定
$bar2_s = $bar2_f = $bar3_s = $bar3_f = '';   // bar2及bar3的起迄日
$bar2_des = $bar3_des = ''; // bar2, bar3的說明文字
$duration1 = $duration2 = $duration3 = $duration4 = ''; 
$out = $schedule->set_saw_out_put_ets_etf($PHP_ord_num);
$op['order']['out_ets'] = $out['out_ets'];
$op['order']['out_etf'] = $out['out_etf'];


if (!$out = $daily->order_daily_out($PHP_ord_num)) {
    $op['record_NONE'] = 1;
} else {
    // smarty 計數由零啟始 thus轉換 同時計算必要之管理數據
    for ($i = 0; $i < sizeof($out); $i++) {
        $op['order_out'][$i] = $out[$i];
        $ttl_qty = $ttl_qty + $out[$i]['qty'];
        $ttl_su = $ttl_su + $out[$i]['su'];

        $op['order_out'][$i]['ttl_qty'] = $ttl_qty;
        $op['order_out'][$i]['ttl_su'] = $ttl_su;

        $op['order_out'][$i]['k_date'] = $out[$i]['k_date'];
        $op['order_out'][$i]['qty'] = $out[$i]['qty'];
        $op['order_out'][$i]['su'] = $out[$i]['su'];
        $op['order_out'][$i]['f_rate'] = $ttl_qty/$op['order']['qty']*100;
    }
    
    $op['order']['finish_qty'] = $ttl_qty;
    $op['order']['finish_su'] = $ttl_su;
    $op['order']['finish_rate'] = $ttl_qty/$op['order']['qty']*100;
    
    // 計算 in-house days [如果沒有 finish date就顯示 "not finish" ]
    $op['order']['inhouse'] = countDays($op['order_out'][0]['k_date'],$op['order_out'][count($out)-1]['k_date'])+(1);

    if (!$op['order']['finish']){
        $op['order']['finish'] ="not finish yet!";
    }
    
    if (!$op['order']['shp_date']){
        $op['order']['shp_date'] ="not ship yet!";
    }

    //計算 估計完成日(平均算法) ------------
    //最後一天 有產出的日期
    $last_date = $op['order_out'][count($out)-1]['k_date'];
    if ( $ord['qty'] > $ttl_qty ) {
        // 平均日產
        $day_avg = $ttl_qty/$op['order']['inhouse'];
        // 預計還要幾天
        $more_days = number_format(($ord['qty'] - $ttl_qty)/$day_avg);
        // 預計完成日
        $est_fdate = increceDaysInDate($last_date,$more_days);
        $est_period = $op['order']['inhouse'] + $more_days +1;
    } else {
        $est_fdate = increceDaysInDate($last_date,1);
        $est_period = $op['order']['inhouse'] + 1;
    }
}  //if (!$out

// -------- factors for Gantt chart & anysys -----------------
$plan_days = countDays($ord['etp'],$ord['etd']) +1;
$schedule_days = (($ord['ext_ets'] =='0000-00-00') || (!$ord['ext_ets'])) ? 0 : countDays($ord['ext_ets'],$ord['ext_etf']) ;
$progress_days = (($ord['finish'] =='0000-00-00') || (!$ord['finish'])) ? 0 : countDays($ord['start'],$ord['finish']) ;

// $duration1 = $plan_days ." days";
// $duration2 = $schedule_days ." days";
// $duration3 = $progress_days ." days";

// 預設 bar2, bar3 的值
//比較哪個日期最小 ;最前面
// $top_date = ($ord['apv_date'] < $ord['etp']) ? $top_date = $ord['apv_date'] : $top_date = $ord['etp'];
// $btm_date='0000-00-00';
// if (($ord['status'] == 12)){
	// $btm_date = $ord['shp_date'];
	// $btm_date = increceDaysInDate($btm_date,2);

	// $bar2_s = $ord['ext_ets'];
	// $bar2_f = $ord['ext_etf'];
	// $bar3_s = $ord['start'];
	// $bar3_f = $ord['finish'];

	// 如果已出貨 但卻沒有finish時 --- 要估算出可能的 完成日
	// if (!$ord['finish'] ||($ord['finish'] =='0000-00-00')){
		// $bar3_f = $est_fdate;
		// $duration3 = "est.:".$est_period." days";
	// }



// }elseif(($ord['status'] == 10)){  // finish production
	//比較哪個日期最後
	// $btm_date = ($ord['etd'] > $ord['ext_etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['ext_etf'];
	// if($ord['finish'] && ($ord['finish'] !='0000-00-00')){
		// $btm_date = ($btm_date > $ord['finish']) ? $btm_date = $btm_date : $btm_date = $ord['finish'];
	// }
	// $btm_date = increceDaysInDate($btm_date,2);
	// $bar2_s = $ord['ext_ets'];
	// $bar2_f = $ord['ext_etf'];
	// $bar3_s = $ord['start'];
	// $bar3_f = $ord['finish'];

// }elseif(($ord['status'] == 8)){  // producing
	//比較哪個日期最後
	// $btm_date = ($ord['etd'] > $ord['ext_etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['ext_etf'];
	// $btm_date = ($btm_date > $est_fdate) ? $btm_date = $btm_date : $btm_date = $est_fdate;
	// $btm_date = increceDaysInDate($btm_date,2);

	// $bar2_s = $ord['ext_ets'];
	// $bar2_f = $ord['ext_etf'];

	// $bar3_s = $ord['start'];
	// $bar3_f = $est_fdate;  //以預估日代入
	// $duration3 = "est.:".$est_period." days";
    //******** bar3 色

// }elseif(($ord['status'] == 7)){  //cfm schedule no output
	//比較哪個日期最後
	// $btm_date = ($ord['etd'] > $ord['ext_etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['ext_etf'];
	// $btm_date = increceDaysInDate($btm_date,2);

	// $bar2_s = $ord['pre_ets'];
	// $bar2_f = $ord['pre_etf'];

	// $bar3_s = $ord['ext_ets'];
	// $bar3_f = $ord['ext_etf'];
	// $duration3 = "[unset]";

// }elseif(($ord['status'] == 6)){  // schedule but not cfm schedule
	//比較哪個日期最後
	// $btm_date = ($ord['etd'] > $ord['ext_etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['ext_etf'];
	// $btm_date = increceDaysInDate($btm_date,2);

	// $bar2_s = $ord['ext_ets'];
	// $bar2_f = $ord['ext_etf'];
	// $duration2 = "[un-CFM]";
    //******** bar2 色

	// $bar3_s = increceDaysInDate($top_date,5);
	// $bar3_f = increceDaysInDate($top_date,5);
	// $duration3 = "[unset]";

// }elseif(($ord['status'] == 4)){  // order APV
	// $btm_date = $ord['etd'];
	// $btm_date = increceDaysInDate($btm_date,2);

	// $bar2_s = increceDaysInDate($top_date,5);
	// $bar2_f = increceDaysInDate($top_date,5);
	// $duration2 = "[unset]";

	// $bar3_s = increceDaysInDate($top_date,5);
	// $bar3_f = increceDaysInDate($top_date,5);
	// $duration3 = "[unset]";
// }


//比較哪個日期最後
// $btm_date = ($ord['etd'] > $ord['ext_etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['ext_etf'];
// $btm_date = increceDaysInDate($btm_date,2);

// $plan_days = countDays($ord['etp'],$ord['etd']) +1;
// $schedule_days = (($ord['ext_ets'] =='0000-00-00') || (!$ord['ext_ets'])) ? 0 : countDays($ord['ext_ets'],$ord['ext_etf']) +1;
// $progress_days = (($ord['finish'] =='0000-00-00') || (!$ord['finish'])) ? 0 : countDays($ord['start'],$ord['finish']) +1;

// $duration1 = $plan_days ." days";

// $duration3 = $progress_days ." days";

$top_date = increceDaysInDate($ord['apv_date'],-2);
$btm_date = increceDaysInDate($ord['etf'],6);

$bar2_s = $ord['pre_ets'];
$bar2_f = $ord['pre_etf'];

$bar3_s = $ord['ext_ets'];
$bar3_f = $ord['ext_etf'];


// Sales
$duration1 = countDays($ord['ets'],$ord['etf'])+($ord['ets']!='0000-00-00'?1:0)." Days";
// Factory
$duration2 = countDays($ord['pre_ets'],$ord['pre_etf'])+( ($ord['pre_etf']=='0000-00-00' || $ord['pre_etf']=='')?0:1)." Days";
// Computer
$duration3 = countDays($ord['rel_ets'],$ord['rel_etf'])+( ($ord['rel_ets']=='0000-00-00' || $ord['rel_ets']=='')?0:1)." Days";;
// Actual
$duration4 = countDays($op['order']['out_ets'],$op['order']['out_etf'])+( ($op['order']['out_ets']=='0000-00-00' || $op['order']['out_ets']=='')?0:1)." Days";;


if($ord['qty'] != 0){
	$finish_rate = number_format(($ttl_qty/$ord['qty']),4, '.', ',');
}else{
	$finish_rate = 0;
}
$T = $finish_rate *100;;
$finish = $T."%";

// 因為 jpgraph 沒法接受 progress 大於 100%
if ($finish_rate > 1){ $progress = 1; }else{ $progress = $finish_rate; }

// Gantt chart-----------------------------------------------------------------
include_once($config['root_dir']."/lib/src/jpgraph.php");
include_once($config['root_dir']."/lib/src/jpgraph_gantt.php");

$graph = new GanttGraph(780);

$graph->title->Set("ORDER PROGRESS Chart");
$graph->subtitle->Set($PHP_ord_num." - [".$ord['qty']." unit] FTY: ".$ord['factory']." by:[ ".$ord['creator']." ]");

// Setup some "very" nonstandard colors
$graph->SetMargin(15,50,20,40);
$graph->title->SetFont(FF_ARIAL,FS_BOLD,10);
$graph->SetMarginColor('lightgreen@0.8');
$graph->SetBox(true,'yellow:0.6',2);
$graph->SetFrame(true,'darkgreen',4);
$graph->scale->divider->SetColor('yellow:0.6');
$graph->scale->dividerh->SetColor('yellow:0.6');
$graph->SetShadow();

$graph->SetDateRange($top_date,$btm_date);

// Display month and year scale with the gridlines
$graph->ShowHeaders(GANTT_HWEEK | GANTT_HDAY | GANTT_HMONTH | GANTT_HYEAR);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY2);

$graph->scale->month->SetFontColor("white");
$graph->scale->month->SetBackgroundColor("teal");

$graph->scale->month->grid->SetColor('gray');
$graph->scale->month->grid->Show(true);
$graph->scale->year->grid->SetColor('gray');
$graph->scale->year->grid->Show(true);

// set TODAY Vline
$vline = new GanttVline($TODAY,'Today','red',2,'solid');
$vline->SetDayOffset(0.5);
$vline->title->SetColor('red');
$graph->Add($vline);


// set APV Vline
$apv = new GanttVline($ord['apv_date'],'APV','blue',2,'solid');
$apv->SetDayOffset(0.5);
$apv->title->SetColor('blue');
$graph->Add($apv);

// Setup activity info

// For the titles we also add a minimum width of 100 pixels for the Task name column
$graph->scale->actinfo->SetColTitles(
array('Project','Duration'),array(70,20));
$graph->scale->actinfo->SetBackgroundColor('teal');
$graph->scale->actinfo->SetFontColor("white");
$graph->scale->actinfo->SetFont(FF_ARIAL,FS_BOLD,10);
$graph->scale->actinfo->vgrid->SetStyle('solid');
$graph->scale->actinfo->vgrid->SetColor('gray');

// Data for our example activities
$data = array(
	array(0,array("Sales",$duration1),$ord['ets'],increceDaysInDate($ord['etf'],-1),FF_ARIAL,FS_BOLD,9),
	array(5,array("Factory",$duration2),$ord['pre_ets'],increceDaysInDate($ord['pre_etf'],-1),FF_ARIAL,FS_BOLD,9),
	array(6,array("Computer",$duration3),$ord['ext_ets'],increceDaysInDate($ord['ext_etf'],-1),FF_ARIAL,FS_BOLD,9)
);
	
// Create the bars and add them to the gantt chart

for($i=0; $i<count($data); ++$i) {
    if ($data[$i][3] > 0 && $data[$i][2])
    {
        $bar = new GanttBar($data[$i][0],$data[$i][1],$data[$i][2],$data[$i][3],$finish,10);
        if( count($data[$i])>4 )
            $bar->title->SetFont($data[$i][4],$data[$i][5],$data[$i][6]);
        $bar->SetPattern(BAND_RDIAG,"yellow");
        $bar->SetFillColor("gray");
        $bar->progress->Set($progress);
        $bar->progress->SetPattern(GANTT_SOLID,"teal");
        $graph->Add($bar);
    }
}
//必要時改變 BAR2 及 BAR3的顏色 =============

// milestone for fabric ship, main acc ship....
// fabric ship
if($ord['mat_shp'] && ($ord['mat_shp'] != '0000-00-00' && $ord['mat_shp'] != '1111-11-11')){
	$mat_date = $ord['mat_shp'];
	$mat_des = $ord['mat_shp'];
}else{
	$mat_date = $top_date;
	$mat_des = 'no info !';
}
$fabric_ship = new MileStone(1,array("- fabric receive",$mat_des),$mat_date,"($mat_des)");
$fabric_ship->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$fabric_ship->title->SetColor('firebrick1');
$fabric_ship->caption->SetColor('black');
$graph->Add($fabric_ship);

// key accessories ship
if($ord['m_acc_shp'] && ($ord['m_acc_shp'] != '0000-00-00' && $ord['m_acc_shp'] != '1111-11-11' )){
	$macc_date = $ord['m_acc_shp'];
	$macc_des = $ord['m_acc_shp'];
}else{
	$macc_date =$top_date;
	$macc_des = 'no info !';
}
$macc_ship = new MileStone(2,array("- key acc receive",$macc_des),$macc_date,"($macc_des)");
$macc_ship->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$macc_ship->title->SetColor('firebrick1');
$macc_ship->caption->SetColor('black');
$graph->Add($macc_ship);

// sample approved
if($ord['smpl_apv'] && ($ord['smpl_apv'] != '0000-00-00')){
	$smpl_date = $ord['smpl_apv'];
	$smpl_des = $ord['smpl_apv'];
}else{
	$smpl_date = $top_date;
	$smpl_des = 'no info !';
}
$smpl_apv = new MileStone(3,array("- sample APV",$smpl_des),$smpl_date,"(".$smpl_des.")");
$smpl_apv->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$smpl_apv->title->SetColor('tomato3');
$smpl_apv->caption->SetColor('black');
$graph->Add($smpl_apv);

// ETD
$ETD = new MileStone(4,array("- ETD",$ord['etd']),increceDaysInDate($ord['etd'],-1),"(".$ord['etd'].")");
$ETD->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$ETD->title->SetColor('tomato3');
$ETD->caption->SetColor('black');
$graph->Add($ETD);

// shipping
if($ord['shp_date'] && ($ord['shp_date'] != '0000-00-00')){
	$shp_date = $ord['shp_date'];
	$shp_des = $ord['shp_date'];
}else{
	$shp_date = $top_date;
	$shp_des = 'no info !';
}
$shipping = new MileStone(7,array("- shipping",$shp_des),$shp_date,"($shp_des)");
// $shipping->caption->SetFont(FF_ARIAL,FS_BOLD,8);
$shipping->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$shipping->title->SetColor('blue');
$shipping->caption->SetColor('black');
$graph->Add($shipping);

$op['order']['chart'] = $graph->Stroke('picture/order_out.png');
$op['dur1'] = $duration1;
$op['dur2'] = $duration2;
$op['dur3'] = $duration3;
$op['dur4'] = $duration4;

if(file_exists($GLOBALS['config']['root_dir']."/picture/".$PHP_ord_num.".jpg")){
    $op['main_pic'] = "./picture/".$PHP_ord_num.".jpg";
} else {
    $op['main_pic'] = "./images/graydot.gif";
}

$op['schedule'] = $schedule->order_get($op['order']['order_num']);  //取出該筆記錄
// $ets = '9999-99-99';  $etf = '0000-00-00';
// for($i=0; $i<sizeof($op['schedule']); $i++)
// {
    // if($op['schedule'][$i]['rel_ets'] < $ets )$ets = $op['schedule'][$i]['rel_ets'];
    // if($op['schedule'][$i]['rel_etf'] > $etf )$etf = $op['schedule'][$i]['rel_etf'];
// }

// A new graph with automatic size 
$sch_graph  = new GanttGraph(780); 
$sch_graph->SetMargin(15,40,20,10);
$sch_graph->title->Set("ORDER SCHEDULE Chart - for Computer Plan");
$sch_graph->ShowHeaders(GANTT_HWEEK | GANTT_HDAY | GANTT_HMONTH | GANTT_HYEAR);
$sch_graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY2);
$sch_graph->title->SetFont(FF_ARIAL,FS_BOLD,10);
$sch_graph->SetShadow();


$i_mk = 0;
//  A new activity on row '0' 
for($i=0; $i<sizeof($op['schedule']); $i++)
{
    $mks = $op['schedule'][$i]['mks'];
    $remark = $op['schedule'][$i]['remark'];
    $ets = $op['schedule'][$i]['rel_ets'] = $op['schedule'][$i]['pdt_qty'] > 0 ? $op['schedule'][$i]['ets'] : $op['schedule'][$i]['rel_ets'];
    $etf = $op['schedule'][$i]['rel_etf'] = $op['schedule'][$i]['status'] == 2 ? $op['schedule'][$i]['etf'] : $op['schedule'][$i]['rel_etf'];
    $op['schedule'][$i]['day'] = countDays($ets,$etf)+1;
    $op['schedule'][$i]['pre_day'] = ( $op['schedule'][$i]['pre_ets'] > 0 && $op['schedule'][$i]['pre_etf'] > 0 )?countDays($op['schedule'][$i]['pre_ets'],$op['schedule'][$i]['pre_etf'])+1:0;
    
    $op['ttl_qty'] += $op['schedule'][$i]['qty'];
    $op['ttl_pdt_qty'] += $op['schedule'][$i]['pdt_qty'];

    $fsh_rate = NUMBER_FORMAT(($op['schedule'][$i]['pdt_qty'] / $op['schedule'][$i]['qty']),4);
    $sch_activity[$i]  = new GanttBar ($i_mk,'LINE:'.$op['schedule'][$i]['line_name'], $ets, increceDaysInDate($etf,-1) ,'['.($fsh_rate * 100).'%]',10);
    if($op['order']['status'] > 8){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#D7D7FF");
        $sch_activity[$i] ->SetFillColor ("#D7D7FF");
    }else if($op['schedule'][$i]['status'] == '2' ){#Finish
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#EEEEEE");
        $sch_activity[$i] ->SetFillColor ("#999999");
    }else if($etf > $op['order']['etd']){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FD1838");
        $sch_activity[$i] ->SetFillColor ("#FD1838");
    }else if($op['order']['smpl_apv'] == '0000-00-00' && !$op['order']['mat_shp'] && !$op['order']['m_acc_shp']){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FE00B9");
        $sch_activity[$i] ->SetFillColor ("#FE00B9");
    }else if($op['order']['smpl_apv'] == '0000-00-00' && (!$op['order']['mat_shp'] || !$op['order']['m_acc_shp'])){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FF6F43");
        $sch_activity[$i] ->SetFillColor ("#FF6F43");
    }else if( !$op['order']['mat_shp'] && !$op['order']['m_acc_shp']){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FF63E0");
        $sch_activity[$i] ->SetFillColor ("#FF63E0");
    }else if( !$op['order']['mat_shp'] && !$op['order']['m_acc_shp']){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#FFC5F3");
        $sch_activity[$i] ->SetFillColor ("#FFC5F3");
    }else if($op['order']['status'] >= '8'){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#D0FDCD");
        $sch_activity[$i] ->SetFillColor ("#D0FDCD");
    }else if(group_order_style($op['order']['style']) != $op['schedule'][$i]['style']){
        $sch_activity[$i] ->SetPattern(BAND_RDIAG, "#F8FF84");
        $sch_activity[$i] ->SetFillColor ("#F8FF84");
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
    $milestone = new MileStone($i_mk,$mks.'):'.$remark,increceDaysInDate($etf,-1),increceDaysInDate($etf,0));
    // $milestone->title->SetColor("black");
    $milestone->title->SetColor ("#DC7100"); 
    // $milestone->title->SetFont(FF_FONT1,FS_BOLD);
    $sch_graph->Add($milestone);
    $i_mk ++;
}

$op['order']['chart'] = $sch_graph->Stroke('picture/schedule_out.png');		
$op['today'] = $TODAY;
// print_r($op['order']);
page_display($op, $AUTH, $TPL_SCH_ORD_SHOW,$Title,$SubTitle);
break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_ets_ajx":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_ets_ajx":

$onChange = ( $PHP_onChange ) ? $PHP_onChange : '' ;
$line_ets = $schedule->get_etf($PHP_fty,$PHP_line);  //取出該筆記錄
$line_select = $arry2->select($line_ets,'',$_GET['PHP_select_name'],'select',$onChange);

echo $line_select;
break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "schedule_print":	 
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
// 		if(!isset($SCH_fty)) $sch_fty = '';

		$tmp_date = increceDaysInDate($PHP_pt_date,68);
		$lst_date = $schedule->get_last_date($sch_fty); 
//		echo $lst_date;
		if($tmp_date < $lst_date)	$lst_date = $tmp_date;
		$tmp_lst = explode('-',$lst_date);
		$tmp_now = explode('-',$PHP_pt_date);

		//foreach($tmp_now as $key -> $value) echo $key."==>".$vaule."<BR>";
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

 				
 		$ln_rec 	= $schedule->get_by_mm( $sch_fty, $PHP_pt_date,$lst_date);
 		$con_rec 	= $schedule->get_contract( $sch_fty, $PHP_pt_date,$lst_date);
		$ord_rec 	= $schedule->get_order($sch_fty);

include_once($config['root_dir']."/lib/class.pdf_schedule_small.php");

$mark = $print_title=$PHP_pt_date.' ~ '.$lst_date.' SCHEDULE REPORT';
$print_title2 = "FTY :".$sch_fty;
$mark =' ARIAL VIEW SCHEDULE REPORT';
$pdf=new PDF_schedule_small('L','mm','A4');
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

for($i = 0; $i<sizeof($con_rec); $i++)
{
		if ($x >= 35)
		{
			$pdf->AddPage();
			$x = 0;
		}	
		$pdf->sc_line_sch($con_rec[$i],$days);
		$x ++;
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
		$line = $monitor->get_line($sch_fty);
		for($i=0; $i < sizeof($line); $i++)
		{
			if($line[$i]['sc'] == 0 && $line[$i]['worker'] > 0 )$line_value[] = $line[$i]['line']."  (".$line[$i]['line_style'].")";
			if($line[$i]['sc'] == 0 && $line[$i]['worker'] > 0 )$line_key[] = $line[$i]['id'];
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
			$line_change = $arry2->select_sch_line($tmp_line_value,'','PHP_line','select',"get_chg_ets('".$sch_fty."',this)",$tmp_line_key);
			$line_sp = $arry2->select_sch_line($tmp_line_value,'','PHP_line_sp','select',"get_spl_ets('".$sch_fty."',this)",$tmp_line_key);
	 	echo $line_change."|".$line_sp;
	 	
exit;	 	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "get_ord_schedule":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_ord_schedule":
$ord_sch = $schedule->order_get($PHP_ord_num);
$sch_str = '';
for($i=0; $i<sizeof($ord_sch); $i++) {
    $sch_str .= 'LN:'.$ord_sch[$i]['line_name'].' ; QTY:'.$ord_sch[$i]['qty'].' ; '.substr($ord_sch[$i]['rel_ets'],5).'~'.substr($ord_sch[$i]['rel_etf'],5).'|';
}
$sch_str = substr($sch_str,0,-1);
echo $sch_str;
exit;	 


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "schedule_auto_reload":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "schedule_auto_reload":
check_authority($AUTH,"view");
if(!$SCH_fty)$SCH_fty = $sch_fty;
$schedule->auto_reload($sch_fty);
// echo '123';
$redir_str = 'schedule.php?PHP_action=schedule_view_small&SCH_fty='.$SCH_fty.'&PHP_pt_date='.$PHP_pt_date;
redirect_page($redir_str);
exit; 	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sch_dynamic_edit":	 // mode 新增
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "sch_dynamic_edit":
	check_authority($AUTH,"add");
	
	if(!isset($PHP_fty)) $PHP_fty = '';
	if(isset($PHP_fty) && $PHP_fty)$sch_fty = $PHP_fty;

	if(!$PHP_fty && !$sch_fty) {
		$msg = "please select factory first!";
		$redir_str = 'schedule.php?PHP_action=pd_schedule&PHP_msg='.$msg;
		redirect_page($redir_str);
	}
	
	$str_date = ($pt_date);
	// $str_date = '2011-04-01'; #pt_date
	$late_date = $schedule->get_last_date($sch_fty);
	$date_ary = explode('-',$late_date);
	$days = getDaysInMonth($date_ary[1],$date_ary[0]);
	$late_date = $date_ary[0].'-'.$date_ary[1].'-'.$days;
	
	$op['ln_rec'] = $schedule->get_by_mm_dynamic($sch_fty, $str_date,$late_date);

	$mod = '';
	$mods = '';
	foreach ( $op['ln_rec'] as $k1 => $v1 ) {
		if( !empty($v1['sch_rec']) ) {
			foreach( $v1['sch_rec'] as $k2 => $v2 ) {
				$pp = ( $k1 == 0 && $k2 == 0 )? '' : '|' ;
				// if ( $v2['rel_ets'] < $str_date ) $v2['rel_ets'] = $str_date;
				if( !empty( $v2['id'] ) ) {
          $v2['flag']=isset($v2['flag'])?$v2['flag']:'';
          $v2['flag_color']=isset($v2['flag_color'])?$v2['flag_color']:'';
          $pattern = ($v2['ptn_upload'] == '0000-00-00')?'=NONE=':$v2['ptn_upload'];
					$mod .= $pp.$v2['id'].','.$v2['line_id'].','.$v2['ord_num'].','.$v2['su'].','.$v2['ord_qty'].','.$v2['qty'].','.
					$v2['ets'].','.$v2['etf'].','.$v2['rel_ets'].','.$v2['rel_etf'].','.$v2['open_date'].','.
					$v2['open_user'].','.$v2['fty'].','.$v2['status'].','.$v2['pdt_qty'].','.$v2['des'].','.
					$v2['style'].','.$v2['smpl_apv'].','.$v2['ord_status'].','.$v2['etd'].','.$v2['ord_id'].','.
					$v2['mat_shp'].','.$v2['m_acc_shp'].','.$v2['style_num'].','.$v2['ord_init'].','.$v2['day_range'].','.
					$v2['flag'].','.$v2['flag_color'].','.$sch_finish_rate.','.$v2['m_status'].','.$v2['ie1'].','.
					$v2['dept'].','.$v2['mks'].','.$pattern.','.$v2['partial_num'];
				}
			}
		}
		$pp = $k1 == 0 ? '' : '|' ;
		# 新線沒有平均產量時 預設 5.00
		$v1['day_avg'] = ( $v1['day_avg'] != 0.00 ) ? $v1['day_avg'] : '24.00' ;
		$mods .= $pp.$v1['line'].','.$v1['id'].','.$v1['worker'].','.$v1['day_avg'].','.$v1['style'];
	}
		
	// 外代工
	$op['con_rec'] = $schedule->get_contract($sch_fty, $str_date,$late_date);
	$con_rec = '';
	foreach ( $op['con_rec'] as $k1 => $v1 ) {
		if( !empty( $v1['sch_rec']) ) {
			foreach( $v1['sch_rec'] as $k2 => $v2) {
				if ( !empty( $v2['id'] ) ) {
					$pp = $k1 == 0 ? '' : '|' ;
          $pattern = ( $v2['ptn_upload'] == '0000-00-00' ) ? '=NONE=' : $v2['ptn_upload'] ;
					$con_rec .= $pp.$v2['id'].','.$v2['line_id'].','.$v2['ord_num'].','.$v2['su'].','.$v2['qty'].','.$v2['qty'].','.
					$v2['ets'].','.$v2['etf'].','.$v2['rel_ets'].','.$v2['rel_etf'].','.$v2['open_date'].','.
					$v2['open_user'].','.$v2['fty'].','.$v2['status'].','.$v2['pdt_qty'].','.$v2['des'].','.
					$v2['style'].','.$v2['smpl_apv'].','.$v2['ord_status'].','.$v2['etd'].','.$v2['ord_id'].','.
					$v2['mat_shp'].','.$v2['m_acc_shp'].','.$v2['style_num'].','.$v2['ord_init'].','.$v2['day_range'].','.
					$v2['flag'].','.$v2['flag_color'].','.$v2['ie1'].','.$v2['dept'].','.$v2['mks'].','.
					$pattern.','.$v2['partial_num'];
				}
			}
		}
	}
		
	// #計算日期
	// $lst_mm = ($date_ary[0] - date('Y')) * 12 + $date_ary[1];
	// $yy = date('Y');
	// $mm = date('m');
	// for($j=date('m'); $j<=$lst_mm; $j++)
	// {
		// $end_day = getDaysInMonth($mm,$yy);
		// for($i=1; $i <= $end_day; $i++)
		// {
			// $op['days'][] = $i;
			// $op['week_day'][] = date('w',strtotime(($yy.'-'.$mm.'-'.$i))); 
		// }			
		// $op['yy_mm'][] = $yy.'-'.$mm;
		// $op['span_mm'][] = getDaysInMonth($mm,$yy);
		// $mm++;
		// if($mm < 10) $mm = '0'.$mm;
		// if($mm == 13)
		// {
			// $mm = '01';
			// $yy++;
		// }
	// }

	// 未排 line_id, ord_num,des,su, qty, ets,etf,rel_ets,rel_etf,fty 沒有 SCHEDULE ID
	$ord_rec = $schedule->get_order($sch_fty);
	$un_rec = '';
	foreach($ord_rec as $k1 => $v1){
		$pp = $k1 == 0 ? '' : '|' ;
		$pattern = ($v1['ptn_upload'] == '0000-00-00')?'=NONE=':$v1['ptn_upload'];
		@$un_rec .= $pp.$v1['order_num'].',,'.$v1['etd'].','.$v1['rem_qty'].','.$v1['ie1'].','.$sch_fty.','.
		$v1['style'].','.$v1['mks'].','.$pattern.','.$v1['partial_num'];
	}
	$op['mode'] = $mods.'@'.$mod.'@'.$un_rec.'@'.$con_rec;
	
	// $ord_value = $ord_key = array();
	// for($i=0; $i<sizeof($ord_rec); $i++)
	// {
		// $qty = $ord_rec[$i]['qty'] - $ord_rec[$i]['ext_qty'];
		// $su = $ord_rec[$i]['su'] - $ord_rec[$i]['ext_su'];			
		// $ord_value[] =$ord_rec[$i]['order_num']." (".$qty." pcs ;  ".$su." su ; Styel : ".$ord_rec[$i]['style'].";  ETD : ".$ord_rec[$i]['etd']." )";
		// $ord_key[] =$ord_rec[$i]['order_num'];
  // }

  // $op['ord_select'] =  $arry2->select($ord_value,'','PHP_ord','select','',$ord_key);

	// $line = $monitor->get_line($sch_fty);
	// for($i=0; $i < sizeof($line); $i++)
	// {
		// if($line[$i]['sc'] == 0 )$line_value[] = $line[$i]['line']."  (".$line[$i]['line_style'].")";
		// if($line[$i]['sc'] == 0 )$line_key[] = $line[$i]['id'];
		// if($line[$i]['sc'] == 1 && $line[$i]['line'] == 'contractor')$op['ct_select'] = "<input type=hidden name='PHP_ct' value=".$line[$i]['id'].">";
	// }
  
	// $op['line_select'] = $arry2->select($line_value,'','PHP_line','select',"get_ets('".$sch_fty."',this)",$line_key);
	// $op['line_sp'] = $arry2->select($line_value,'','PHP_line_sp','select',"get_edit_ets('".$sch_fty."',this)",$line_key);
	// $op['line_change'] = $arry2->select($line_value,'','PHP_line','select',"get_edit_ets('".$sch_fty."',this)",$line_key);

	// $op['yy'] = $sch_year;
	// $op['mm'] = $sch_month;
	// $op['fty'] = $sch_fty;
	// $op['span'] = $days - 10;
	
	$op['msg'] = $schedule->msg->get(2);
	if(isset($PHP_msg)) $op['msg'][] = $PHP_msg; 
	$op['today'] = $TODAY;
	$op['pt_date'] = $pt_date;
	$op['str_date'] = $str_date;
	$TPL_SCH_DYNAMIC_EDIT = 'sch_dynamic_edit.html';
	page_display($op, $AUTH, $TPL_SCH_DYNAMIC_EDIT,$Title,$SubTitle);
	exit; 	


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "upline": // mode 新增
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "upline":
check_authority($AUTH,"edit");

$message = '';

//刪除
if ( !empty($_POST['mod_tmp']) ) {
	// echo 'mod_tmp = '.$_POST['mod_tmp'].'<br>';
	$tmpS1 = explode('|',$_POST['mod_tmp']);
	foreach( $tmpS1 as $k1 => $v1 ){
		$tmpS2 = explode(',',$v1);
		if ( $tmpS2[0] == 4 ) {
			$message .= 'DEL:'.$tmpS2[1].','.$tmpS2[2].','.$tmpS2[3].','.$tmpS2[4].'<br>';
			$schedule->del_dynamic_schedule($tmpS2[1]);
			$schedule->add_dynamic_schedule($tmpS2[2]);
			// $message = "DELETE order [ ".$PHP_ord_num." ]  schedule for  line [".$s_ln['line']."] , QTY [".$PHP_del_qty."], ETS [".$s_ln['ets']."],  ETF[".$s_ln['etf']."]";
			// $log->log_add(0,"026A",$message);
		}
	}
}
	 

// 外代工
if ( !empty($_POST['con_tmp']) ) {
  $tmpC1 = explode('|',$_POST['con_tmp']);
	foreach( $tmpC1 as $k1 ){
		$tmpC2 = explode(',',$k1);
		// 新增
		if ( $tmpC2[0] == 2 ) {
		
			$line_id = ( $tmpC2[7] == 'LY' ) ? '38' : '12';
			$parm = array(
										'line_id'	=>	$line_id,
										'ord'			=>	$tmpC2[2],
										'des'			=>	$tmpC2[3],
										'su'			=>	$tmpC2[4],
										'qty'			=>	$tmpC2[5],
										'ets'			=>	$tmpC2[6],
										'etf'			=>	$tmpC2[7],
										'fty'			=> 	$tmpC2[8],
										);

			$new_id = $schedule->contractor_schedule($parm);
			$schedule->add_dynamic_schedule($tmpC2[2]);

			$s_ln = $schedule->get_ln_det($new_id);
			if($schedule->check_sch_finish($tmpC2[2]))
			{
				$schedule->add_capacity($tmpC2[2]);
				$message = "UPDATE order [ ".$tmpC2[2]." ] production schedule [ETS, ETF] ";
				$log->log_add(0,"026A",$message);
			}
			 
			if($new_id)
			{
				$message = "ADD order [ ".$tmpC2[2]." ]".$tmpC2[2]."  schedule for  line [".$s_ln['line']."]".$tmpC2[1]." , QTY [".$s_ln['qty']."]".$tmpC2[5].", ETS [".$s_ln['ets']."]".$tmpC2[6].",  ETF[".$s_ln['etf']."]".$tmpC2[7]."";
				$log->log_add(0,"026A",$message);
			}else{		
				$msg = $schedule->msg->get(2);
				$message = $msg[0];
			}
		}
	}
}

// 新增修改
foreach( $_SESSION['line'] as $key => $val ){
	if( !empty($val['id']) && $val['id'] != 38 && $val['id'] != 12 ){
		if( !empty($_POST[$val['id']]) ){
			$tmpS1 = explode('|',$_POST[$val['id']]);
			foreach( $tmpS1 as $k1 => $v1 ){
				$tmpS2 = explode(',',$v1);
				// 新增
				if ( $tmpS2[0] == 2 ) {
				
					$parm = array(
												'line_id'	=>	$val['id'],
												'ord_num'	=>	$tmpS2[2],
												'des'			=>	$tmpS2[3],
												'su'			=>	$tmpS2[4],
												'qty'			=>	$tmpS2[5],
												'ets'			=>	$tmpS2[6],
												'etf'			=>	$tmpS2[7],
												'fty'			=> 	$tmpS2[8],
												);

					$new_id = $schedule->point_dynamic_schedule($parm);
					$schedule->add_dynamic_schedule($tmpS2[2]);

					$s_ln = $schedule->get_ln_det($new_id);
					if($schedule->check_sch_finish($tmpS2[2]))
					{
						$schedule->add_capacity($tmpS2[2]);
						$message = "UPDATE order [ ".$tmpS2[2]." ] production schedule [ETS, ETF] ";
						$log->log_add(0,"026A",$message);
					}
					 
					if($new_id)
					{
						$message = "ADD order [ ".$tmpS2[2]." ]".$tmpS2[2]."  schedule for  line [".$s_ln['line']."]".$tmpS2[1]." , QTY [".$s_ln['qty']."]".$tmpS2[5].", ETS [".$s_ln['ets']."]".$tmpS2[6].",  ETF[".$s_ln['etf']."]".$tmpS2[7]."";
						$log->log_add(0,"026A",$message);
					}else{		
						$msg = $schedule->msg->get(2);
						$message = $msg[0];
					}
				}
				
				// 修改
				if ( $tmpS2[0] == 3 ) {
					
					$parm = array(
						'line_id'	=> $val['id'] ,
						'ets'			=> $tmpS2[6] ,  
						'etf'			=> $tmpS2[7] , 
						'qty'			=> $tmpS2[5],
						'id'			=> $tmpS2[1],
						'des'			=> $tmpS2[3],
					);

					if( $schedule->up_scd($parm) ){

					}
				}
			}
		}
	}
}

//刪除
if ( !empty($_POST['mod_del']) ) {
	$tmpS1 = explode('|',$_POST['mod_del']);
	foreach( $tmpS1 as $k1 => $v1 ){
		$tmpS2 = explode(',',$v1);
		$message .= 'DEL:'.$tmpS2[0].','.$tmpS2[1].'<br>';
		$schedule->del_dynamic_schedule($tmpS2[0]);
		$schedule->add_dynamic_schedule($tmpS2[1]);
	}
}

	
$redir_str ="schedule.php?PHP_action=sch_dynamic_edit&PHP_msg=".$message;
redirect_page($redir_str);

break;
//-------------------------------------------------------------------------

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "up_all_line": // mode 新增
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "up_all_line":
$message = '';

// 新增修改
foreach( $_SESSION['line'] as $key => $val ){
	if( !empty($val['id']) && $val['id'] != 38 && $val['id'] != 12 ){
		if( !empty($_POST[$val['id']]) ){
			$tmpS1 = explode('|',$_POST[$val['id']]);
			foreach( $tmpS1 as $k1 => $v1 ){
				$tmpS2 = explode(',',$v1);
				// 新增
				if ( $tmpS2[0] == 2 ) {
				
					$parm = array(
												'line_id'	=>	$val['id'],
												'ord_num'	=>	$tmpS2[2],
												'des'			=>	$tmpS2[3],
												'su'			=>	$tmpS2[4],
												'qty'			=>	$tmpS2[5],
												'ets'			=>	$tmpS2[6],
												'etf'			=>	$tmpS2[7],
												'fty'			=> 	$tmpS2[8],
												);

					$new_id = $schedule->point_dynamic_schedule($parm);
					$schedule->add_dynamic_schedule($tmpS2[2]);

					$s_ln = $schedule->get_ln_det($new_id);
					if($schedule->check_sch_finish($tmpS2[2]))
					{
						$schedule->add_capacity($tmpS2[2]);
						$message = "UPDATE order [ ".$tmpS2[2]." ] production schedule [ETS, ETF] ";
						$log->log_add(0,"026A",$message);
					}
					 
					if($new_id)
					{
						$message = "ADD order [ ".$tmpS2[2]." ]".$tmpS2[2]."  schedule for  line [".$s_ln['line']."]".$tmpS2[1]." , QTY [".$s_ln['qty']."]".$tmpS2[5].", ETS [".$s_ln['ets']."]".$tmpS2[6].",  ETF[".$s_ln['etf']."]".$tmpS2[7]."";
						$log->log_add(0,"026A",$message);
					}else{		
						$msg = $schedule->msg->get(2);
						$message = $msg[0];
					}
				}
				
				// 修改
				if ( $tmpS2[0] == 3 ) {
					
					$parm = array(
						'line_id'	=> $val['id'] ,
						'ets'			=> $tmpS2[6] ,  
						'etf'			=> $tmpS2[7] , 
						'qty'			=> $tmpS2[5],
						'id'			=> $tmpS2[1],
						'des'			=> $tmpS2[3],
					);

					if( $schedule->up_scd($parm) ){

					}
				}
			}
		}
	}
}

echo 'END';	
// $redir_str ="schedule.php?PHP_action=sch_dynamic_edit&PHP_msg=".$message;
// redirect_page($redir_str);

break;

//-------------------------------------------------------------------------

}   // end case ---------

?>
