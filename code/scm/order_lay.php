<?php
// error_reporting(E_ALL);
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
session_register	('sch_parm');
session_register  ('sub_ti');

##################  2004/11/10  ########################
#			order_lay.php  
#		for Carnival SCM [Sample]  management
#			mode     2009/10/23
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$PHP_SELF = $_SERVER['PHP_SELF'];

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
include_once($config['root_dir']."/lib/class.order_lay.php");
$Ol = new ORDER_LAY();
if (!$Ol->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$op = array();

$TPL_ORDER_LAY = "order_lay.html";

$PHP_action = !empty($_POST['PHP_action']) ? $_POST['PHP_action'] : 'order_lay' ;
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "main":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "order_lay":
default;
check_authority('072',"view");

$PHP_dept       = !empty($_POST['PHP_dept']) ? $_POST['PHP_dept'] : '' ;
$PHP_cust       = !empty($_POST['PHP_cust']) ? $_POST['PHP_cust'] : '' ;
$PHP_factory    = !empty($_POST['PHP_factory']) ? $_POST['PHP_factory'] : '' ;
$PHP_order_num  = !empty($_POST['PHP_order_num']) ? $_POST['PHP_order_num'] : '' ;
$PHP_status     = !empty($_POST['PHP_status']) ? $_POST['PHP_status'] : '' ;
$PHP_year1      = ( !empty($_POST['PHP_year1']) )? $_POST['PHP_year1'] : null ;
$PHP_month1     = ( !empty($_POST['PHP_month1']) )? $_POST['PHP_month1'] : null ;
  
  if( !empty($PHP_year1) && !empty($PHP_month1) ){
    $date     = $PHP_year1.'-'.$PHP_month1 ;
  }elseif( !empty($PHP_year1) || !empty($PHP_month1) ){
    if( !empty($PHP_year1) )
      $date   = $PHP_year1.'-';
    else
      $date   = '%-'.$PHP_month1.'-';
  }else{
    if( !empty($PHP_dept) || !empty($PHP_cust) || !empty($PHP_factory) || !empty($PHP_order_num) ){
      $date   = $PHP_year1 = $PHP_month1 = null;
    }else{
      $PHP_year1 = date('Y');
      $PHP_month1 = date('m');
      $date   = $PHP_year1.'-'.$PHP_month1 ; 
    }
  }

  $arrs = '';
  $arrs = array(
  'dept'      => $PHP_dept , 
  'cust'      => $PHP_cust , 
  'factory'   => $PHP_factory , 
  'date'      => $date , 
  'order_num' => $PHP_order_num , 
  'status'    => $PHP_status 
  );

  $table      = 's_order JOIN pdtion';
  $m_sql      = 's_order.id,s_order.order_num,s_order.qty,s_order.etd,s_order.opendate,s_order.dept,s_order.cust,s_order.style,s_order.su,s_order.factory,s_order.status,s_order.uprice,s_order.po_mat_cost'
                .',s_order.po_acc_cost,s_order.rel_mat_cost,s_order.rel_acc_cost,s_order.mat_u_cost,s_order.mat_useage'
                .',s_order.fusible,s_order.interline,s_order.acc_u_cost,s_order.wash'
                .',s_order.emb,s_order.oth,s_order.quota_fee,s_order.comm_fee'
                .',s_order.smpl_fee,s_order.fty_cm,s_order.ie1,s_order.cm, s_order.rel_cm_cost'
                .',pdtion.start,pdtion.finish,pdtion.shp_date';
$where_str  = !empty($_SESSION['PAGE']['order_lay']['where_str']) ? $_SESSION['PAGE']['order_lay']['where_str'] : '' ;
// print_r($_POST);
// print_r($_GET);
$op = $Ol->search_mode($arrs,$where_str,$table,$m_sql,$PHP_action);
// print_r($op);
$note = 'Search : ';
$note .= !empty($PHP_dept) ? ' [ Dept:'.$PHP_dept.' ] ' : '' ;
$note .= !empty($PHP_cust) ? ' [ Cust:'.$PHP_cust.' ] ' : '' ;
$note .= !empty($PHP_factory) ? ' [ FTY:'.$PHP_factory.' ] ' : '' ;
$note .= ( !empty($PHP_year1) || !empty($PHP_month1) )? ' [ DATE:'.$PHP_year1.' / '.$PHP_month1.' ] ' : '' ;
$note .= !empty($PHP_order_num) ? ' [ Order#:'.$PHP_order_num.' ] ' : '' ;

$op['date'] = $PHP_year1.' / '.$PHP_month1;
$op['dept'] = $_SESSION['SCACHE']['ADMIN']['dept'];
$op['note'] = $note;
// echo $TPL_ORDER_LAY;
// print_r($op);
page_display($op, '072', $TPL_ORDER_LAY);
break;

}   // end case ---------

?>
