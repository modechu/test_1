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
include_once($config['root_dir']."/lib/w_daily_out.class.php");
$W_DAILY_OUT = new W_DAILY_OUT();
if (!$W_DAILY_OUT->init($mysql,"log")) { print "error!! cannot initialize database for W_DAILY_OUT class"; exit; }
include_once($config['root_dir']."/lib/class.order.php");
$order = new ORDER();
if (!$order->init($mysql,"log")) { print "error!! cannot initialize database for order class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
#$AUTH = '099';
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
$ipLong = ip2long($_SERVER["REMOTE_ADDR"]);
$time = ( $ipLong >= '1906311168' && $ipLong <= '1908408319' )?
date("H:i l",mktime (date(H)-1, date(i), date(s), date(m), date(d), date(Y))):'' ;
$_SESSION['Date'] = $Date = !empty($_SESSION['Date']) ? $_SESSION['Date'] : date('Y-m-d') ;
$_SESSION['Date'] = $Date = !empty($_POST['PHP_Date']) ? $_POST['PHP_Date'] : $_SESSION['Date'] ;
$_SESSION['Date_end'] = $Date_end = !empty($_SESSION['Date']) ? $_SESSION['Date'] : date('Y-m-d') ;
$_SESSION['Date_end'] = $Date_end = !empty($_POST['PHP_Date']) ? $_POST['PHP_Date'] : $_SESSION['Date'] ;
/*
$_SESSION['PHP_detail'] = $detail = !empty($_POST['PHP_detail']) ? $_POST['PHP_detail'] : ( !empty($_SESSION['PHP_detail']) ? $_SESSION['PHP_detail'] : 'true' ) ;
$_SESSION['PHP_list'] = $list = !empty($_POST['PHP_list']) ? $_POST['PHP_list'] : ( !empty($_SESSION['PHP_list']) ? $_SESSION['PHP_list'] : 'true' ) ;
$_SESSION['PHP_bar'] = $bar = !empty($_POST['PHP_bar']) ? $_POST['PHP_bar'] : ( !empty($_SESSION['PHP_bar']) ? $_SESSION['PHP_bar'] : 'true' ) ;
$_SESSION['PHP_auto'] = $auto = !empty($_POST['PHP_auto']) ? $_POST['PHP_auto'] : ( !empty($_SESSION['PHP_auto']) ? $_SESSION['PHP_auto'] : 'true' ) ;
*/
echo '<!DOCTYPE html PUBLIC
          "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <link rel="stylesheet" type="text/css" href="js/calendar/css/jscal2.css" />
    <link rel="stylesheet" type="text/css" href="js/calendar/css/border-radius.css" />
    <link rel="stylesheet" type="text/css" href="js/calendar/css/gold/gold.css" />
    <link rel="stylesheet" type="text/css" href="css/scm.css?';
echo date('mdhis');
echo '" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/w_daily_out.js"></script>    
    <script type="text/javascript" src="js/calendar/js/jscal2.js"></script>
    <script type="text/javascript" src="js/calendar/js/lang/en.js"></script>    
</head>
<script>
(function($){
    $(document).ready(function(){
        
        $("input[id^=\'check\']").live("click", function () {
            
        });

    });
})(jQuery);
</script>
';

$ByCHECK = '';
$ByCHECK .= '<div class="subtitle">Date Start <font size="6" style="cursor:pointer;font-weight:bold;" id="calendar-trigger" color="blue">'.(( date('Y-m-d') == $_SESSION['Date'] )? date('Y-m-d').'</font><font size="3" style="font-weight:bold;" id="calendar-trigger" color="blue"> ':$_SESSION['Date']).'</font> <input type="hidden" id="calendar-inputField" />';
$ByCHECK .= 'Date End <font size="6" style="cursor:pointer;font-weight:bold;" id="calendar-trigger-end" color="blue">'.(( date('Y-m-d') == $_SESSION['Date'] )? date('Y-m-d').'</font><font size="3" style="font-weight:bold;" id="calendar-trigger-end" color="blue"> ':$_SESSION['Date']).'</font> <input type="hidden" id="calendar-inputField-end" /></div>';


echo $ByCHECK;
echo '<form id="DD" METHOD="POST">';
echo '<input type="hidden" id="calendar-inputField2" name="PHP_Date" size="8" value="'.$Date.'">';
echo '<input type="hidden" id="calendar-inputField2-end" name="PHP_Date-end" size="8" value="'.$Date_end.'">';
echo '<input type="hidden" id="input-detail" name="PHP_detail" size="8" value="'.$detail.'">';
echo '<input type="hidden" id="input-list" name="PHP_list" size="8" value="'.$list.'">';
echo '<input type="hidden" id="input-bar" name="PHP_bar" size="8" value="'.$bar.'">';
echo '<input type="hidden" id="input-auto" name="PHP_auto" size="8" value="'.$auto.'">';
echo '</form>';
//$div = $order->distri_month_su('319','2014-07-07','2014-08-20','LY','pre_schedule');
//echo $div;
//exit;
//$daily = $W_DAILY_OUT->get_daily_out_put($_SESSION['Date']);
//$capacity = $W_DAILY_OUT->get_daily_capacity($_SESSION['Date']);
// $scm = $W_DAILY_OUT->get_scm_capacity($_SESSION['Date'],'LY');
//$scm = $W_DAILY_OUT->get_scm_full_capacity($_SESSION['Date'],'LY');
$daily_orderinfo = $W_DAILY_OUT->get_order('2014-06-30','2014-07-01');
//print_r($orderinfo);

for($i=0;$i<sizeof($daily_orderinfo);$i++)
{
    $orderquery[$i] = $daily_orderinfo[$i]['LotId'];
}
//print_r($orderquery);
$order_procinfo = $W_DAILY_OUT->get_orderProc($orderquery);
//print_r($order_procinfo);  
$procqtyinfo = $W_DAILY_OUT->get_scm_full_capacity($_SESSION['Date'],'LY');
//print_r($order_procinfo);     

echo '

<script type="text/javascript">

    Calendar.setup({
   
        weekNumbers : false,
        trigger     : "calendar-trigger",
        inputField  : "calendar-inputField",
        date        : '.$Dates.' ,
        selection   : '.$Dates.' ,
        showTime    : false,
        
        onSelect: function() {
            document.getElementById("calendar-inputField2").value = document.getElementById("calendar-inputField").value;
            //DD.submit();
        }

    });
	Calendar.setup({
   
        weekNumbers : false,
        trigger     : "calendar-trigger-end",
        inputField  : "calendar-inputField-end",
        date        : '.$Dates_end.' ,
        selection   : '.$Dates_end.' ,
        showTime    : false,
        
        onSelect: function() {
            document.getElementById("calendar-inputField2-end").value = document.getElementById("calendar-inputField-end").value;
            //DD.submit();
        }

    });

</script>
';    
break;
#
#
#
#
#
#
# :get_bl_num
case "get_bl_num":
check_authority($AUTH,"view");
break;
#
#
#
#
#
#
} # CASE END
?>