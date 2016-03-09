<?php
session_start();

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/order_breakdown.class.php");

$ORDER_COST = new ORDER_COST();
if (!$ORDER_COST->init($mysql,"log")) { print "error!! cannot initialize database for ORDER_COST class"; exit; }

$PHP_SELF = $_SERVER['PHP_SELF'];

$op = array();

$PHP_action = !empty($PHP_action) ? $PHP_action : '';
// echo $PHP_action;

$auth = '092';

switch ($PHP_action) {
//=======================================================



case "main":
check_authority($auth,"view");

// creat cust combo box
$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
$op['year'] = $arry2->select($YEAR_WORK,$GLOBALS['THIS_YEAR'],'PHP_year','select','');  
$op['month'] = $arry2->select($MONTH_WORK,date('m'),'PHP_month','select','');	

$op['msg'] = $order->msg->get(2);

// message 
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op,$auth,'order_breakdown_main.html');    	    
break;



case "order_breakdown_view":
check_authority($auth,"view");

# GET POST
$op['fty'] = $PHP_fty = !empty($PHP_fty) ? $PHP_fty : 'LY' ;
$PHP_year = !empty($PHP_year) ? $PHP_year : $GLOBALS['THIS_YEAR'] ;
$op['month'] = $PHP_month = !empty($PHP_month) ? $PHP_month : date('m') ;

# 設定起始月
$reset_m = -3;

# get capacity
$capaci = $ORDER_COST->get_capacity_mm($PHP_fty,$PHP_year,$PHP_month,$reset_m);
$capaci_array = $capaci['date'];
$op['year'] = $capaci['year'];

for ($i=0; $i<12; $i++) {
    # 月份數字
    $op['mm_name'][$i]['key'] = $PHP_month+$reset_m+$i;

    # 月份英文
    $op['mm_name'][$i]['val'] = $MM2[date('m',mktime(0,0,0,($PHP_month+$reset_m+$i),1,$PHP_year))];
    # 年月
    $op['mm_yaer'][$i] =date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year));
    $op['mm_month'][$i] = date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year));
    
    # capacity
    $op['capacity'][$i] = $capaci_array[date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year))][date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year))];
    $op['capacity_sum'] += $op['capacity'][$i];
    
    # EST. ORDER SU
    $op['order_su'][$i] = $ORDER_COST->get_order_su_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['order_su_ttl'] += $op['order_su'][$i]['sum'];
    
    # EST. Un APV SU
    $op['est_un_apv_su'][$i] = $ORDER_COST->get_est_un_apv_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['est_un_apv_su_ttl'] += $op['est_un_apv_su'][$i]['sum'];

    # EST. FORECAST SU
    $op['forecast_su'][$i] = $ORDER_COST->get_forecast_su_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['forecast_su_ttl'] += $op['forecast_su'][$i]['sum'];
    
    # EST. ORDER 
    $op['est_sales_sum'][$i] = $ORDER_COST->get_est_sales_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['est_sales_ttl'] += $op['est_sales_sum'][$i]['sum'];

    # EST. MATERIAL
    $op['est_cost_sum'][$i] = $ORDER_COST->get_est_cost_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['est_cost_ttl'] += $op['est_cost_sum'][$i]['sum'];
    
    # EST. CM
    $op['est_cm_sum'][$i] = $ORDER_COST->get_est_cm_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['est_cm_ttl'] += $op['est_cm_sum'][$i]['sum'];
    
    # EST. OTHER
    // $op['est_other_sum'][$i] = $ORDER_COST->get_est_other_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    // $op['est_other_ttl'] += $op['est_other_sum'][$i]['sum'];
    
    # EST. G.M.
    $op['est_gm_sum'][$i]['sum'] = $op['est_sales_sum'][$i]['sum'] - $op['est_cost_sum'][$i]['sum'];
    $op['est_gm_ttl'] += $op['est_gm_sum'][$i]['sum'];  

    # EST. $G.M. rate (%)
    $op['est_gm_rate_sum'][$i]['sum'] = ( $op['est_gm_sum'][$i]['sum'] / $op['est_sales_sum'][$i]['sum'] ) * 100;
    $op['est_gm_rate_ttl'] = ( $op['est_gm_ttl'] / $op['est_sales_ttl'] ) * 100;
  
    # EST. FTY ORDER SU
    $op['fty_order_su'][$i] = $ORDER_COST->get_fty_order_su_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['fty_order_su_ttl'] += $op['fty_order_su'][$i]['sum'];
    
    # EST. TPE + FTY ORDER SU
    $op['order_su_sum'][$i]['sum'] = $op['order_su'][$i]['sum'] + $op['fty_order_su'][$i]['sum'];
    $op['order_su_sum_ttl'] += $op['order_su_sum'][$i]['sum'];
    
    # EST. FTY Un APV SU
    $op['fty_un_apv_su'][$i] = $ORDER_COST->get_fty_un_apv_su_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['fty_un_apv_su'][$i]['sum'] = $op['fty_un_apv_su'][$i]['sum'] + $op['est_un_apv_su'][$i]['sum'];
    $op['fty_un_apv_su_ttl'] += $op['fty_un_apv_su'][$i]['sum'];
    
    # EST. FTY LOCAL
    $op['est_local_sum'][$i] = $ORDER_COST->get_fty_local_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['est_local_ttl'] += $op['est_local_sum'][$i]['sum'];
    
    # EST. FTY IN COME
    $op['fty_in_come'][$i]['sum'] = $op['est_local_sum'][$i]['sum'] + $op['est_cm_sum'][$i]['sum'];
    $op['fty_in_come_ttl'] += $op['fty_in_come'][$i]['sum'];
    
    
    # EST. EXPENSE
    // $op['expense'][$i] = $ORDER_COST->get_expense_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    // $op['expense_ttl'] += $op['expense'][$i]['sum'];
  
    # EST. FTY EXPENSE
    $op['fty_expense'][$i] = $ORDER_COST->get_fty_expense_etd($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    $op['fty_expense_ttl'] += $op['fty_expense'][$i]['sum'];
    
    # EST. FTY P/L
    $op['fty_pl_sum'][$i]['sum'] = $op['fty_in_come'][$i]['sum'] - $op['fty_expense'][$i]['sum']; ;
    $op['fty_pl_sum_ttl'] += $op['fty_pl_sum'][$i]['sum'];
    
    
    
    
    
    
    
    
    
    
    
    
    // # Shipping
    // $op['shipping_sum'][$i] = $ORDER_COST->get_shipping($PHP_fty,date('Y',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)),date('m',mktime(0,0,0,$PHP_month+$reset_m+$i,1,$PHP_year)));
    // $op['shipping_ttl'] += $op['shipping_sum'][$i]['sum'];
    
    // # Order
    // $op['order_ship_sum'][$i] = $ORDER_COST->get_order_for_ship($op['shipping_sum'][$i]);
    // $op['order_ship_ttl'] += $op['order_ship_sum'][$i]['sum'];
    
    // # Purchase
    // $op['purchase_sum'][$i] = $ORDER_COST->get_purchase($op['shipping_sum'][$i]);
    // $op['purchase_ttl'] += $op['purchase_sum'][$i]['sum'];  

    // # C.M.
    // $op['cm_sum'][$i] = $ORDER_COST->get_cm($op['shipping_sum'][$i]);
    // $op['cm_ttl'] += $op['cm_sum'][$i]['sum'];  

    // # G.M.
    // $op['gm_sum'][$i]['sum'] = $op['shipping_sum'][$i]['sum'] - $op['purchase_sum'][$i]['sum'] - $op['cm_sum'][$i]['sum'];
    // $op['gm_ttl'] += $op['gm_sum'][$i]['sum'];  
    
    // # $G.M. rate (%)
    // $op['gm_rate_sum'][$i]['sum'] = ( $op['gm_sum'][$i]['sum'] / $op['shipping_sum'][$i]['sum'] ) * 100;
    // $op['gm_rate_ttl'] = ( $op['gm_ttl'] / $op['shipping_ttl'] ) * 100;
    // print_r($op['shipping_sum'][$i]['order']);
}

page_display($op,$auth,'order_breakdown_view.html');
break;


case "est_order_su":
check_authority($auth,"view");

$op = $ORDER_COST->get_order_su_det($PHP_fty, $PHP_year, $PHP_month);

$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'est_order_su_det.html');
break;

case "est_un_apv_su":
check_authority($auth,"view");

$op = $ORDER_COST->get_un_apv_su_det($PHP_fty, $PHP_year, $PHP_month);

$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'est_order_su_det.html');
break;

case "est_order_su_sum":
check_authority($auth,"view");

$op = $ORDER_COST->get_order_su_sum_det($PHP_fty, $PHP_year, $PHP_month);

$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'est_order_su_det.html');
break;

case "est_fty_un_apv_su":
check_authority($auth,"view");

$op = $ORDER_COST->get_fty_un_apv_su_det($PHP_fty, $PHP_year, $PHP_month);

$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'est_order_su_det.html');
break;



case "est_ord_det":
check_authority($auth,"view");

$op = $ORDER_COST->get_est_ord_det($PHP_fty, $PHP_year, $PHP_month);

$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'est_ord_det.html');
break;


case "shipping_ord_det":
check_authority($auth,"view");

$mm = array("1"=>"01","2"=>"02","3"=>"03","4"=>"04","5"=>"05","6"=>"06","7"=>"07","8"=>"08","9"=>"09");
$op = $ORDER_COST->get_shipping_det($PHP_fty, $PHP_year, $mm[$PHP_month]);

$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'shipping_ord.html');
break;




}   // end case ---------
?>
