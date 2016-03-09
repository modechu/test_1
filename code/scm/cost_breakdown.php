<?php
session_start();

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/cost_breakdown.class.php");

$ORDER_COST = new ORDER_COST();
if (!$ORDER_COST->init($mysql,"log")) { print "error!! cannot initialize database for ORDER_COST class"; exit; }

$PHP_SELF = $_SERVER['PHP_SELF'];

$op = array();

$PHP_action = !empty($PHP_action) ? $PHP_action : '';
// echo $PHP_action;

$budget[2015]['LY'] = array(495807,550948,236600,370000,949600,1658220,1698520,1406400,1268860,764335,618335,1074500);
$budget[2015]['CF'] = array(1948947,1763333,2305897,2056032,2830614,2498650,3855060,2798488,2370148,2305897,1563441,2305897);
$budget[2015]['HJ'] = array(300000,300000,300000,300000,300000,300000,300000,300000,300000,300000,300000,300000);

$auth = '096';

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

page_display($op,$auth,'cost_breakdown_main.html');    	    
break;



case "order_breakdown_view":
check_authority($auth,"view");

# GET POST
$op['fty'] = $PHP_fty = !empty($PHP_fty) ? $PHP_fty : 'LY' ;
$PHP_year = !empty($PHP_year) ? $PHP_year : $GLOBALS['THIS_YEAR'] ;
// $op['month'] = $PHP_month = !empty($PHP_month) ? $PHP_month : date('m') ;
// $op['month'] = $PHP_month = '04';

# 設定起始月
$reset_m = -3;

# get capacity
$capaci = $ORDER_COST->get_capacity_mm($PHP_fty,$PHP_year);
$capaci_array = $capaci['date'];
// $op['year'] = $capaci['year'];
$op['year'] = $PHP_year;

for ($i=1; $i<13; $i++) {

    $value = $i-1;
    # 月份數字
    $op['mm_name'][$value]['key'] = ($value);
    # 月份英文
    $op['mm_name'][$value]['val'] = $MM2[date('m',mktime(0,0,0,($i),1,$PHP_year))];
    # 年月
    $op['mm_yaer'][$value] = $yaer = date('Y',mktime(0,0,0,$i,1,$PHP_year));
    $op['mm_month'][$value] = $month = date('m',mktime(0,0,0,$i,1,$PHP_year));
    
    
    
    ## EST. EXPENSE / Budget Amt.($)
    // $op['expense'][$value] = $ORDER_COST->get_expense_etd($PHP_fty,$yaer,$month );
    // $op['expense_ttl'] += $op['expense'][$value]['sum'];
    
    ## Order Amt.($)
    $op['order_amt'][$value] = $ORDER_COST->get_order_amt_etd($PHP_fty,$yaer,$month);
    $op['order_amt_ttl'] += $op['order_amt'][$value]['sum'];

    ## Est. Cost($)
    $op['est_cost_sum'][$value] = $ORDER_COST->get_est_cost_etd($PHP_fty,$yaer,$month);
    $op['est_cost_ttl'] += $op['est_cost_sum'][$value]['sum'];
    
    ## EST. G.M.
    $op['est_gm_sum'][$value]['sum'] = $op['order_amt'][$value]['sum'] - $op['est_cost_sum'][$value]['sum'];
    $op['est_gm_ttl'] += $op['est_gm_sum'][$value]['sum'];  

    ## EST. $G.M. rate (%)
    $op['est_gm_rate_sum'][$value]['sum'] = ( $op['est_gm_sum'][$value]['sum'] / $op['order_amt'][$value]['sum'] ) * 100;
    $op['est_gm_rate_ttl'] = ( $op['est_gm_ttl'] / $op['order_amt_ttl'] ) * 100;
    
    
    
    ## PO. Cost($)
    $order_arr = $op['est_cost_sum'][$value]['order_arr'];
    $op['po_cost_sum'][$value] = $ORDER_COST->get_po_cost_etd($yaer,$month,$order_arr);
    $op['po_cost_sum'][$value]['sum'] = $op['po_cost_sum'][$value]['sum'] + $op['est_cost_sum'][$value]['other_sum'];
    $op['po_cost_ttl'] += $op['po_cost_sum'][$value]['sum'];
        
    ## PO. G.M.
    $op['po_gm_sum'][$value]['sum'] = $op['order_amt'][$value]['sum'] - $op['po_cost_sum'][$value]['sum'];
    $op['po_gm_ttl'] += $op['po_gm_sum'][$value]['sum'];  

    ## PO. $G.M. rate (%)
    $op['po_gm_rate_sum'][$value]['sum'] = ( $op['po_gm_sum'][$value]['sum'] / $op['order_amt'][$value]['sum'] ) * 100;
    $op['po_gm_rate_ttl'] = ( $op['po_gm_ttl'] / $op['order_amt_ttl'] ) * 100;    
    

    
    ## Exp. Ship Amt.($)
    // if( $yaer.'-'.$month <= date("Y-m", strtotime('-1 month')) ){
    if( $yaer.'-'.$month <= date("Y-m", mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))) ){
    // $op['exp_ship_amt'][$value] = $ORDER_COST->get_exp_ship_amt_etd($PHP_fty,$yaer,$month);
    $op['exp_ship_amt'][$value] = $ORDER_COST->get_exp_ship_amt_etd($PHP_fty,$yaer,$month);
    } else {
    $op['exp_ship_amt'][$value]['sum'] = 0;
    }
    $op['exp_ship_amt_ttl'] += $op['exp_ship_amt'][$value]['sum'];
        
    # Exp. SU.
    $op['exp_su'][$value]['sum'] = $op['exp_ship_amt'][$value]['su'];
    $op['exp_su_ttl'] += $op['exp_su'][$value]['sum'];        
    # Exp. PC.
    $op['exp_pc'][$value]['sum'] = $op['exp_ship_amt'][$value]['pc'];
    $op['exp_pc_ttl'] += $op['exp_pc'][$value]['sum'];      
    
    if( $op['exp_ship_amt'][$value]['sum'] > 0 ){
    # Exp. Cost($)
    $order_arr = $op['exp_ship_amt'][$value]['order_arr'];
    $op['exp_cost_sum'][$value] = $ORDER_COST->get_exp_cost_etd($yaer,$month,$order_arr);
    // echo $op['exp_cost_sum'][$value]['ttl_amount'].'<br>';
    // $op['exp_cost_sum'][$value]['sum'] = $op['exp_cost_sum'][$value]['sum'] + $op['est_cost_sum'][$value]['other_sum'];
    $op['exp_cost_ttl'] += $op['exp_cost_sum'][$value]['sum'];
        
    # Exp. G.M.
    $op['exp_gm_sum'][$value]['sum'] = $op['exp_ship_amt'][$value]['sum'] - $op['exp_cost_sum'][$value]['sum'];
    $op['exp_gm_ttl'] += $op['exp_gm_sum'][$value]['sum'];

    # Exp. $G.M. rate (%)
    $op['exp_gm_rate_sum'][$value]['sum'] = ( $op['exp_gm_sum'][$value]['sum'] / $op['exp_ship_amt'][$value]['sum'] ) * 100;
    $op['exp_gm_rate_ttl'] = ( $op['exp_gm_ttl'] / $op['exp_ship_amt_ttl'] ) * 100;
    } else {
        $op['exp_cost_sum'][$value]['sum'] = 0;
        $op['exp_cost_ttl'] += 0;
        $op['exp_gm_sum'][$value]['sum'] = 0;
        $op['exp_gm_ttl'] += 0;
        $op['exp_gm_rate_sum'][$value]['sum'] = 0;
        $op['exp_gm_rate_ttl'] += 0;
    }
    
    
    # capacity
    $op['capacity'][$value] = $capaci_array[$yaer][$month];
    $op['capacity_sum'] += $op['capacity'][$value];
    
    # EST. TPE ORDER SU
    $op['order_su'][$value] = $ORDER_COST->get_order_su_etd($PHP_fty,$yaer,$month);
    $op['order_su_ttl'] += $op['order_su'][$value]['sum'];
    
    ## EST. TPE OUTPUT SU
    $op['output_su'][$value] = $ORDER_COST->get_output_su_etd($PHP_fty,$yaer,$month);
    $op['output_su_ttl'] += $op['output_su'][$value]['sum'];
    
    # EST. Un APV SU
    $op['est_un_apv_su'][$value] = $ORDER_COST->get_est_un_apv_etd($PHP_fty,$yaer,$month);
    $op['est_un_apv_su_ttl'] += $op['est_un_apv_su'][$value]['sum'];

    # EST. FORECAST SU
    $op['forecast_su'][$value] = $ORDER_COST->get_forecast_su_etd($PHP_fty,$yaer,$month);
    $op['forecast_su_ttl'] += $op['forecast_su'][$value]['sum'];

    ## Exp. SU.
    // $op['order_amt'][$value] = $ORDER_COST->get_ext_order_su_etd($PHP_fty,$yaer,$month);
    // $op['order_amt_ttl'] += $op['order_amt'][$value]['sum'];
    
    ## Exp. PC.
    // $op['order_amt'][$value] = $ORDER_COST->get_ext_order_qty_etd($PHP_fty,$yaer,$month);
    // $op['order_amt_ttl'] += $op['order_amt'][$value]['sum'];
    
    # EST. CM
    // $op['est_cm_sum'][$value] = $ORDER_COST->get_est_cm_etd($PHP_fty,$yaer,$month);
    // $op['est_cm_ttl'] += $op['est_cm_sum'][$value]['sum'];
    
    # Summary 
    // $op['summary'][$value] = $op['order_su'][$value]['sum'] + $op['est_un_apv_su'][$value]['sum'];
    // $op['summary_ttl'] += $op['summary'][$value]['sum'];    
    
    // # EST. ORDER 
    // $op['est_sales_sum'][$value] = $ORDER_COST->get_est_sales_etd($PHP_fty,$yaer,$month);
    // $op['est_sales_ttl'] += $op['est_sales_sum'][$value]['sum'];


    

    
    # EST. OTHER
    // $op['est_other_sum'][$value] = $ORDER_COST->get_est_other_etd($PHP_fty,$yaer,$month);
    // $op['est_other_ttl'] += $op['est_other_sum'][$value]['sum'];

  
    // # EST. FTY ORDER SU
    // $op['fty_order_su'][$value] = $ORDER_COST->get_fty_order_su_etd($PHP_fty,$yaer,$month);
    // $op['fty_order_su_ttl'] += $op['fty_order_su'][$value]['sum'];
    
    // # EST. TPE + FTY ORDER SU
    // $op['order_su_sum'][$value]['sum'] = $op['order_su'][$value]['sum'] + $op['fty_order_su'][$value]['sum'];
    // $op['order_su_sum_ttl'] += $op['order_su_sum'][$value]['sum'];
    
    // # EST. FTY Un APV SU
    // $op['fty_un_apv_su'][$value] = $ORDER_COST->get_fty_un_apv_su_etd($PHP_fty,$yaer,$month);
    // $op['fty_un_apv_su'][$value]['sum'] = $op['fty_un_apv_su'][$value]['sum'] + $op['est_un_apv_su'][$value]['sum'];
    // $op['fty_un_apv_su_ttl'] += $op['fty_un_apv_su'][$value]['sum'];
    
    // # EST. FTY LOCAL
    // $op['est_local_sum'][$value] = $ORDER_COST->get_fty_local_etd($PHP_fty,$yaer,$month);
    // $op['est_local_ttl'] += $op['est_local_sum'][$value]['sum'];
    
    // # EST. FTY IN COME
    // $op['fty_in_come'][$value]['sum'] = $op['est_local_sum'][$value]['sum'] + $op['est_cm_sum'][$value]['sum'];
    // $op['fty_in_come_ttl'] += $op['fty_in_come'][$value]['sum'];
    

  
    // # EST. FTY EXPENSE
    // $op['fty_expense'][$value] = $ORDER_COST->get_fty_expense_etd($PHP_fty,$yaer,$month);
    // $op['fty_expense_ttl'] += $op['fty_expense'][$value]['sum'];
    
    # EST. FTY P/L
    // $op['fty_pl_sum'][$value]['sum'] = $op['fty_in_come'][$value]['sum'] - $op['fty_expense'][$value]['sum']; ;
    // $op['fty_pl_sum_ttl'] += $op['fty_pl_sum'][$value]['sum'];
    
    
    
    
    
    
    
    
    
    
    
    
    // # Shipping
    // $op['shipping_sum'][$value] = $ORDER_COST->get_shipping($PHP_fty,$yaer,$month);
    // $op['shipping_ttl'] += $op['shipping_sum'][$value]['sum'];
    
    // # Order
    // $op['order_ship_sum'][$value] = $ORDER_COST->get_order_for_ship($op['shipping_sum'][$value]);
    // $op['order_ship_ttl'] += $op['order_ship_sum'][$value]['sum'];
    
    // # Purchase
    // $op['purchase_sum'][$value] = $ORDER_COST->get_purchase($op['shipping_sum'][$value]);
    // $op['purchase_ttl'] += $op['purchase_sum'][$value]['sum'];  

    // # C.M.
    // $op['cm_sum'][$value] = $ORDER_COST->get_cm($op['shipping_sum'][$value]);
    // $op['cm_ttl'] += $op['cm_sum'][$value]['sum'];  

    // # G.M.
    // $op['gm_sum'][$value]['sum'] = $op['shipping_sum'][$value]['sum'] - $op['purchase_sum'][$value]['sum'] - $op['cm_sum'][$value]['sum'];
    // $op['gm_ttl'] += $op['gm_sum'][$value]['sum'];  
    
    // # $G.M. rate (%)
    // $op['gm_rate_sum'][$value]['sum'] = ( $op['gm_sum'][$value]['sum'] / $op['shipping_sum'][$value]['sum'] ) * 100;
    // $op['gm_rate_ttl'] = ( $op['gm_ttl'] / $op['shipping_ttl'] ) * 100;
    // print_r($op['shipping_sum'][$value]['order']);
}
// print_r($op);
$op['budget']=$budget[$PHP_year][$PHP_fty];
$op['budget_ttl']=array_sum($budget[$PHP_year][$PHP_fty]);
page_display($op,$auth,'cost_breakdown_view.html');
break;



case "exp_ship":
check_authority($auth,"view");

$op = $ORDER_COST->get_exp_ship($PHP_fty, $PHP_year, $PHP_month);

$op['est_order'] = $op['arr'];
$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'exp_ship.html');
break;



case "exp_cost":
check_authority($auth,"view");

$op = $ORDER_COST->get_exp_cost($PHP_fty, $PHP_year, $PHP_month);

$op['est_order'] = $op['arr'];
$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'exp_cost.html');
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
