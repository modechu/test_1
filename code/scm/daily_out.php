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
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '099';
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

$ipLong = ip2long($_SERVER["REMOTE_ADDR"]);
$time = ( $ipLong >= '1906311168' && $ipLong <= '1908408319' )?
date("H:i l",mktime (date(H)-1, date(i), date(s), date(m), date(d), date(Y))):
'TPE. Time '.date("H:i l").' ) ~ ( Vietnam Time '.date("H:i",mktime (date(H)-1, date(i), date(s), date(m), date(d), date(Y))) ;

$_SESSION['Date'] = $Date = !empty($_SESSION['Date']) ? $_SESSION['Date'] : date('Y-m-d') ;
$_SESSION['Date'] = $Date = !empty($_POST['PHP_Date']) ? $_POST['PHP_Date'] : $_SESSION['Date'] ;
$_SESSION['PHP_detail'] = $detail = !empty($_POST['PHP_detail']) ? $_POST['PHP_detail'] : ( !empty($_SESSION['PHP_detail']) ? $_SESSION['PHP_detail'] : 'true' ) ;
$_SESSION['PHP_list'] = $list = !empty($_POST['PHP_list']) ? $_POST['PHP_list'] : ( !empty($_SESSION['PHP_list']) ? $_SESSION['PHP_list'] : 'true' ) ;
$_SESSION['PHP_bar'] = $bar = !empty($_POST['PHP_bar']) ? $_POST['PHP_bar'] : ( !empty($_SESSION['PHP_bar']) ? $_SESSION['PHP_bar'] : 'true' ) ;
$_SESSION['PHP_auto'] = $auto = !empty($_POST['PHP_auto']) ? $_POST['PHP_auto'] : ( !empty($_SESSION['PHP_auto']) ? $_SESSION['PHP_auto'] : 'true' ) ;

// $op['css'] = array( 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
// $op['js'] = array( 'js/jquery.min.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' , 'js/jquery.blockUI.js' );
// echo $_POST['PHP_detail'];
// echo $_POST['PHP_list'];
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
        // var chkStatus = $("input[id*=\'checklist\'][type=\'checkbox\']").attr(\'checked\');
    
        // $("input[id*=\'chk\'][type=\'checkbox\']").attr(\'checked\',true);
        $("input[id^=\'check\']").live("click", function () {
            if($("#checkdetail").attr(\'checked\')){
                $("#input-detail").val("true");
            }else{
                $("#input-detail").val("false");
            }
            if($("#checklist").attr(\'checked\')){
                $("#input-list").val("true");
            }else{
                $("#input-list").val("false");
            }
            if($("#checkbar").attr(\'checked\')){
                $("#input-bar").val("true");
            }else{
                $("#input-bar").val("false");
            }
            if($("#checkauto").attr(\'checked\')){
                $("#input-auto").val("true");
            }else{
                $("#input-auto").val("false");
            }
            DD.submit();
        });
		
        $("td[id^=\'monitor\']").live("click", function () {
            var line = $(this).attr(\'line\');
			var gofile ="w_daily_out.php?PHP_action=get_monitor&PHP_line="+line;
			var nm = line;
			window.open(gofile,\'_bank\',\'toolbar=no,location=no,directories=no,status=no,bar=no,menu=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=1200,height=800 top=30, left=30\');
        });

    });
})(jQuery);
</script>
';
// 
// 
$ByCHECK = '';
$ByCHECK .= '<div class="subtitle">DAILY OUT PUT <font size="6" style="cursor:pointer;font-weight:bold;" id="calendar-trigger" color="blue">'.(( date('Y-m-d') == $_SESSION['Date'] )? date('Y-m-d').'</font><font size="3" style="font-weight:bold;" id="calendar-trigger" color="blue"> ( '.$time.' )':$_SESSION['Date']).'</font> <input type="hidden" id="calendar-inputField" /></div>';
$ByCHECK .= '&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp; ';
$ByCHECK .= 'Detail : <input type="checkbox" id="checkdetail" '.(($detail=='true')?'checked':'').'/> &nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;';
$ByCHECK .= 'List : <input type="checkbox" id="checklist" '.(($list=='true')?'checked':'').'/> &nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;';
$ByCHECK .= 'BarPlot : <input type="checkbox" id="checkbar" '.(($bar=='true')?'checked':'').'/> &nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;';
if( date('Y-m-d') == $_SESSION['Date'] ) {
    if($auto=='true'){
        echo '<meta http-equiv="refresh" content="59" />';
    }
    $ByCHECK .= 'Auto Reload : <input type="checkbox" id="checkauto" '.(($auto=='true')?'checked':'').'/> &nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;';
}
echo $ByCHECK;
echo '<form id="DD" METHOD="POST">';
echo '<input type="hidden" id="calendar-inputField2" name="PHP_Date" size="8" value="'.$Date.'">';
echo '<input type="hidden" id="input-detail" name="PHP_detail" size="8" value="'.$detail.'">';
echo '<input type="hidden" id="input-list" name="PHP_list" size="8" value="'.$list.'">';
echo '<input type="hidden" id="input-bar" name="PHP_bar" size="8" value="'.$bar.'">';
echo '<input type="hidden" id="input-auto" name="PHP_auto" size="8" value="'.$auto.'">';
echo '</form>';
// echo '<font size="6">'.$_SESSION['Date'].'</font><br>';


$daily = $W_DAILY_OUT->get_daily_out_put($_SESSION['Date']);
$capacity = $W_DAILY_OUT->get_daily_capacity($_SESSION['Date']);
// $scm = $W_DAILY_OUT->get_scm_capacity($_SESSION['Date'],'LY');
$scm = $W_DAILY_OUT->get_scm_full_capacity($_SESSION['Date'],'LY');

// print_r($scm);


$TT_VIEW = array(
'T1' => '07:30<br>08:30' ,
'T2' => '08:30<br>09:30' ,
'T3' => '09:30<br>10:30' ,
'T4' => '10:30<br>11:30' ,
'T5' => '12:30<br>13:30' ,
'T6' => '13:30<br>14:30' ,
'T7' => '14:30<br>15:30' ,
'T8' => '15:30<br>16:30' ,
'T9' => '16:30<br>24:00' 
);

$TT_A1 = array('T1'=>'07:30','T2'=>'08:30','T3'=>'09:30','T4'=>'10:30','T5'=>'12:30','T6'=>'13:30','T7'=>'14:30','T8'=>'15:30','T9'=>'16:30');
$TT_A2 = array('T1'=>'08:30','T2'=>'09:30','T3'=>'10:30','T4'=>'11:30','T5'=>'13:30','T6'=>'14:30','T7'=>'15:30','T8'=>'16:30','T9'=>'24:00');
// echo "\r\n";

$CPCT = $LINE = $CPCT_ORD = $CPCT_LINE = array();

foreach($capacity as $row){

    # 過濾目前重複設定訂單在同條線生產
    if( !in_array( (substr($row['SectionCode'],-2,2)*1) , $CPCT_ORD[$row['LotId']]['Line'] ) ){
        $CPCT_ORD[$row['LotId']]['Target_Qty'] += $row['Qty'];
        $CPCT_ORD[$row['LotId']]['ie'] = ($row['ie2'] > 0)?$row['ie2']:$row['ie1'];
        $CPCT_ORD[$row['LotId']]['ie1'] = $row['ie1'];
        $CPCT_ORD[$row['LotId']]['ie2'] = $row['ie2'];
        $CPCT_ORD[$row['LotId']]['ord_qty'] = $row['qty'];
        $CPCT_ORD[$row['LotId']]['scm_rem_qty'] = $row['qty'] - $W_DAILY_OUT->get_scm_output($row['LotId'],$_SESSION['Date']);
        $CPCT_ORD[$row['LotId']]['rfid_rem_qty'] = $row['qty'] - $W_DAILY_OUT->get_rfid_output($row['LotId'],$_SESSION['Date']);
        $CPCT_ORD[$row['LotId']]['Line'][] = (substr($row['SectionCode'],-2,2)*1);
        $CPCT[] = $row['LotId'].(substr($row['SectionCode'],-2,2)*1);
    }
    
    $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']]['Target_Qty'] = $row['Qty'];
    // $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']][$row['LotId']]['Target_Qty'] = $row['Qty'];
    // $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)]['Order'] = $row['LotId'];
    $LINE[] = (substr($row['SectionCode'],-2,2)*1);
    // echo (substr($row['SectionCode'],-2,2)*1)."\r\n";
    // echo (substr($row['SectionCode'],-2,2)*1).' '.$row['LotId'].'<br>';
    // $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']]['Reader'] = $W_DAILY_OUT->get_set_num((substr($row['SectionCode'],-2,2)*1),$row['LotId']);
}



$WORKDONE_ORD = $WORKDONE_LINE = array();
foreach($daily as $row){
    $SONumber = $row['SONumber'];
    $Line = $row['Line'];
    $LINE[] = $Line;
    foreach($TT_A1 as $Time => $v){
        // echo $TT_A1[$Time].'-'.$Line.'-'.$SONumber.'-'.$CPCT_LINE[$Line][$SONumber]['Target_Qty'].'<br>';
        if($CPCT_LINE[$Line][$SONumber]['Target_Qty'] > 0 ){
            if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time] += $row['PackedQty']; }
            if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time.'_F'] += $row['FailedQty']; }
        }
        if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Line][$SONumber][$Time] += $row['PackedQty']; }
        if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Line][$SONumber][$Time.'_F'] += $row['FailedQty']; }
    }
}
// print_r($WORKDONE_LINE[26]);


# By LINE
$ByLINE = '';
$ByLINE .= '<div class="subtitle"> LIST ( By LINE )</div>';
$ByLINE .= '<table border="0" cellspacing="0" cellpadding="0" cols="0"><tr class="title_output"><td rowspan="2">Line</td><td rowspan="2">Order</td><td rowspan="2">Full Capacity</td>';
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE .= '<td colspan="2">Target</td>';
    $ByLINE .= '<td rowspan="2">Calc.<br>I.E.</td><td rowspan="2">Final<br>I.E.</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE .= '<td rowspan="2">'.$val.'</td>';
    }
}

$ByLINE .= '<td colspan="2">Output</td><td rowspan="2">EFF %</td>';
$ByLINE .= '</tr>';
$ByLINE .= '<tr class="title_output">';
# 是否顯示詳細資料
if( $detail == 'true' ){
$ByLINE .= '<td>Qty</td><td>SU.</td>';
}
$ByLINE .= '<td>Qty</td><td>SU.</td></tr>';

$LINE= array_unique($LINE);
sort($LINE);
$A_TT_QTY = array();
$Target_Qty_ttl = $Target_SU_ttl = $Capacity_SU_ttl = 0;
$A_TTL_QTY = 0;
$A_TTL_F_QTY = 0;
$A_TTL_SU = 0;

$ByLINE_ORDER = $ByLINE_Traget = $ByLINE_Capacity = $ByLINE_qty = array();

foreach($LINE as $L_K => $L_V ){
    
    $line = $L_V;
    
    if ( $CPCT_LINE[$line] ) {

        foreach($CPCT_LINE[$line] as $WK_ORDER => $WK_TT){
            $Capacity_SU = $Target_SU = $su_ttl = $qty_ttl = $f_qty_ttl = 0;
            $order_num = trim($WK_ORDER);
            $Capacity_SU = $scm[$line][$order_num]['full_su'];
            // echo $line.'-'.$Capacity_SU.'<br>';
            // echo $order_num.'<br>';
            $Target_Qty = $CPCT_LINE[$line][$order_num]['Target_Qty'];
            $Reader = $CPCT_LINE[$line][$order_num]['Reader'];
            
            if(!empty($Target_Qty)){
                $Target_Qty_ttl += $Target_Qty;
                
                $ie = $CPCT_ORD[$order_num]['ie'];
                
                $Target_SU = $Target_Qty*$ie;
                $Target_SU_ttl += $Target_SU;
                $Capacity_SU_ttl += $Capacity_SU;
            }
            
            $ie1 = $CPCT_ORD[$order_num]['ie1'];
            $ie2 = $CPCT_ORD[$order_num]['ie2'];
            
            if( $WORKDONE_LINE[$line] ) {

                $ByLINE .= '<tr class="list_txt">';
                $ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td>';
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$Target_Qty.'</td>';
                    $ByLINE .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
                }
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$ie1.'</td>';
                    $ByLINE .= '<td>'.$ie2.'</td>';
                }
                
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                
                foreach($TT_VIEW as $key => $val ){
                    $qty = $WORKDONE_LINE[$line][$WK_ORDER][$key];
                    $qty_f = $WORKDONE_LINE[$line][$WK_ORDER][$key."_F"];
                    $qtys = (!empty($qty)?$qty-$qty_f:0);
                    # 是否顯示詳細資料
                    if( $detail == 'true' ){
                        $ByLINE .= '<td>'.$qtys.
                             '<font color="red">'.(!empty($qty_f)?':-'.$qty_f:'').'</font> '.( !empty($qty)? '('.$qty.')':'').'</td>';
                    }
                    
                    # SU
                    $su_ttl += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
                    $qty_ttl += $qty;
                    $f_qty_ttl += $qty_f;
                    $A_TT_QTY[$key] += $qty;
                }
                
                $ByLINE_Capacity[] = $Capacity_SU;
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = $su_ttl;
                
                $ByLINE .= '<td><div align="right">'.$qty_ttl.' <font color="red">('.( !empty($f_qty_ttl)? $f_qty_ttl:0).')</font></div></td>';
                $ByLINE .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($su_ttl,2).'</div></td>';
                $ByLINE .= '<td><div align="right">'.number_format($su_ttl/$Capacity_SU*100).' %</div></td>';
                $ByLINE .= '</tr>';
                $A_TTL_QTY += $qty_ttl;
                $A_TTL_F_QTY += $f_qty_ttl;
                $A_TTL_SU += $su_ttl;
                
            } else {

                $ByLINE .= '<tr class="list_txt">';
                $ByLINE .= '<td class="m_p" id="monitor" line="'.$line.'">'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td>';
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$Target_Qty.'</td>';
                    $ByLINE .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
                }
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$ie1.'</td>';
                    $ByLINE .= '<td>'.$ie2.'</td>';
                }
                
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                $ByLINE_Capacity[] = $Capacity_SU;
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = 0;
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    foreach($TT_VIEW as $key => $val ){
                        $ByLINE .= '<td>0</td>';
                    }
                }
                $ByLINE .= '<td><div align="right">0 <font color="red">(0)</font></div></td>';
                $ByLINE .= '<td class="list_txt_bg_daily"><div align="right">'.number_format(0,2).'</div></td>';
                $ByLINE .= '<td><div align="right">0 %</div></td>';
                $ByLINE .= '</tr>';            
            }
        }
    } else {
        foreach($WORKDONE_LINE[$line] as $WK_ORDER => $WK_TT){
            $ie = $ie1 = $ie2 = $Capacity_SU = $Target_SU = $su_ttl = $qty_ttl = $f_qty_ttl = 0;
            $Capacity_SU = $scm[$line][$order_num]['full_su'];
            $order_num = trim($WK_ORDER);
            
            $Target_Qty = $CPCT_LINE[$line][$order_num]['Target_Qty'];
            if(!empty($Target_Qty)){
                $Target_Qty_ttl += $Target_Qty;
                
                $ie = $CPCT_ORD[$order_num]['ie'];
                
                $Target_SU = $Target_Qty*$ie;
                $Target_SU_ttl += $Target_SU;
                $Capacity_SU_ttl += $Capacity_SU;
                $ie1 = $CPCT_ORD[$order_num]['ie1'];
                $ie2 = $CPCT_ORD[$order_num]['ie2'];
            }
            if( $WORKDONE_LINE[$line] ) {

                $ByLINE .= '<tr class="list_txt">';
                $ByLINE .= '<td class="m_p" id="monitor" line="'.$line.'">'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td>';
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$Target_Qty.'</td>';
                    $ByLINE .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
                }
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$ie1.'</td>';
                    $ByLINE .= '<td>'.$ie2.'</td>';
                }
            
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                foreach($TT_VIEW as $key => $val ){
                    $qtys = $WORKDONE_LINE[$line][$WK_ORDER][$key];
                    $qty_f = $WORKDONE_LINE[$line][$WK_ORDER][$key."_F"];
                    $qtys = (!empty($qtys)?$qtys-$qty_f:0);
                    $qty = 0;
                    # 是否顯示詳細資料
                    if( $detail == 'true' ){
                        $ByLINE .= '<td><font color="blue">'.$qtys.
                             '</font><font color="red">'.(!empty($qty_f)?':-'.$qty_f:'').'</font></td>';
                    }
                    # SU
                    $su_ttl += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
                    $qty_ttl += $qty;
                    $f_qty_ttl += $qty_f;
                    $A_TT_QTY[$key] += $qty;
                }
                
                $ByLINE_Capacity[] = $Capacity_SU;
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = $su_ttl;
                
                $ByLINE .= '<td><div align="right">'.(!empty($Target_Qty)?$qty_ttl:0).' <font color="red">('.( !empty($f_qty_ttl)? $f_qty_ttl:0).')</font></div></td>';
                $ByLINE .= '<td class="list_txt_bg_daily"><div align="right">'.number_format(!empty($Target_Qty)?$su_ttl:0,2).'</div></td>';
                $ByLINE .= '<td><div align="right">'.number_format(!empty($Capacity_SU)?$su_ttl/$Capacity_SU*100:0).' %</div></td>';
                $ByLINE .= '</tr>';
                $A_TTL_QTY += (!empty($Target_Qty)?$qty_ttl:0);
                $A_TTL_F_QTY += (!empty($Target_Qty)?$f_qty_ttl:0);
                $A_TTL_SU += (!empty($Target_Qty)?$su_ttl:0);
                
            } else {

                $ByLINE .= '<tr class="list_txt">';
                $ByLINE .= '<td class="m_p" id="monitor" line="'.$line.'">'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td>';
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$Target_Qty.'</td>';
                    $ByLINE .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
                }
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    $ByLINE .= '<td>'.$ie1.'</td>';
                    $ByLINE .= '<td>'.$ie2.'</td>';
                }
                
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                $ByLINE_Capacity[] = $Capacity_SU;
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = 0;
                
                # 是否顯示詳細資料
                if( $detail == 'true' ){
                    foreach($TT_VIEW as $key => $val ){
                        $ByLINE .= '<td>0</td>';
                    }
                }
                $ByLINE .= '<td><div align="right">0 <font color="red">(0)</font></div></td>';
                $ByLINE .= '<td class="list_txt_bg_daily"><div align="right">'.number_format(0,2).'</div></td>';
                $ByLINE .= '<td><div align="right">0 %</div></td>';
                $ByLINE .= '</tr>';            
            }
        }
    }
}

$ByLINE .= '<tr class="list_txt" height="2">';
$ByLINE .= '<td colspan="2"></td>';
$ByLINE .= '<td></td>';
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE .= '<td colspan="2"></td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE .= '<td></td>';
    }
}
$ByLINE .= '<td></td>';
$ByLINE .= '<td></td>';
$ByLINE .= '<td colspan="1"></td></tr>';


$ByLINE .= '<tr class="list_txt">';
$ByLINE .= '<td colspan="2">Total</td>';
$ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU_ttl,2).'</td>';

# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE .= '<td>'.$Target_Qty_ttl.'</td>';
    $ByLINE .= '<td class="list_txt_bg_daily">'.number_format($Target_SU_ttl,2).'</td>';
}
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE .= '<td colspan="2">&nbsp;</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE .= '<td>'.$A_TT_QTY[$key].'</td>';
    }
}
$ByLINE .= '<td><div align="right">'.$A_TTL_QTY.' <font color="red">('.( !empty($A_TTL_F_QTY)? $A_TTL_F_QTY:0).')</font></div></td>';
$ByLINE .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($A_TTL_SU,2).'</div></td>';
$ByLINE .= '<td><div align="right">'.number_format($A_TTL_SU/$Capacity_SU_ttl*100).' %</div></td></tr>';

$ByLINE .= '</table>';

$ByLINE .= '<p>';


# By ORDER
$ByORDER = '';
$M=0;
$ByORDER .= '<br><div class="subtitle"> LIST ( By ORDER )</div>';
$ByORDER .= '<table border="0" cellspacing="0" cellpadding="0" cols="0"><tr class="title_output"><td rowspan="2">Order</td><td rowspan="2">Qty</td><td rowspan="2">REM. QTY<br>(RFID)</td><td rowspan="2">REM. QTY<br>(SCM)</td><td rowspan="2">Line</td>';
// $ByORDER .= '<td colspan="2">Target</td>';
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByORDER .= '<td rowspan="2">Calc.<br>I.E.</td><td rowspan="2">Final<br>I.E.</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByORDER .= '<td rowspan="2">'.$val.'</td>';
    }
}

$ByORDER .= '<td colspan="2">Output</td>';
$ByORDER .= '</tr>';
$ByORDER .= '<tr class="title_output"><td>Qty</td><td>SU.</td></tr>';

$Target_Qty_ttl = $Target_SU_ttl = 0;
$A_TTL_QTY = 0;
$A_TTL_F_QTY = 0;
$A_TTL_SU = 0;
$A_TT_QTY = array();
foreach($CPCT_ORD as $row => $qtys){

    $Target_Qty = $qtys['Target_Qty'];
    $Target_Qty_ttl += $Target_Qty;
    
    $ie = $qtys['ie'];
    
    $Target_SU = $Target_Qty*$ie;
    $Target_SU_ttl += $Target_SU;
        
    $ByORDER .= '<tr class="list_txt">';
    $ByORDER .= '<td>'.$row.'</td>';
    $ByORDER .= '<td>'.number_format($qtys['ord_qty'],0).'</td>';
    $ByORDER .= '<td><font color="'.( $qtys['rfid_rem_qty'] > 0 ? "" : "red" ).'">'.number_format($qtys['rfid_rem_qty'],0).'</font></td>';
    $ByORDER .= '<td><font color="'.( $qtys['scm_rem_qty'] > 0 ? "" : "red" ).'">'.number_format($qtys['scm_rem_qty'],0).'</font></td>';
    $ByORDER .= '<td>'.implode (",", $qtys['Line']).'</td>';
    // $ByORDER .= '<td>'.$Target_Qty.'</td>';
    // $ByORDER .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
    
    # 是否顯示詳細資料
    if( $detail == 'true' ){
        $ByORDER .= '<td>'.$qtys['ie1'].'</td>';
        $ByORDER .= '<td>'.$qtys['ie2'].'</td>';
    }
    
    $su_ttl = 0;
    $qty_ttl = 0;
    $f_qty_ttl = 0;
    foreach($TT_VIEW as $key => $val ){
        $qty = $WORKDONE_ORD[$row][$key];
        $qty_f = $WORKDONE_ORD[$row][$key."_F"];
        $qtys = (!empty($qty)?$qty-$qty_f:0);
    
        // $ByORDER .= '<td title="'.$qtys.(!empty($qty_f)?':-'.$qty_f:'').'">'.( !empty($qty)?$qty:0).'</td>';
        # 是否顯示詳細資料
        if( $detail == 'true' ){
            $ByORDER .= '<td>'.$qtys.
                 '<font color="red">'.(!empty($qty_f)?':-'.$qty_f:'').'</font> '.( !empty($qty)? '('.$qty.')':'').'</td>';
        }
        # SU
        $su_ttl += $ie * $qty;
        $qty_ttl += $qty;
        $f_qty_ttl += $qty_f;
        // $A_TTL_QTY += (!empty($Target_Qty)?$qty_ttl:0);
        // $A_TTL_SU += (!empty($Target_Qty)?$su_ttl:0);
        $A_TT_QTY[$key] += $qty;
        // $ie += $qtys['ie'] * $qty;
        // $qty += $qty;
    }
    $ByORDER .= '<td><div align="right">'.(!empty($Target_Qty)?$qty_ttl:0).' <font color="red">('.( !empty($f_qty_ttl)? $f_qty_ttl:0).')</font></div></td>';
    $ByORDER .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($su_ttl,2).'</div></td>';
    $ByORDER .= '</tr>';
    $A_TTL_QTY += $qty_ttl;
    $A_TTL_F_QTY += $f_qty_ttl;
    $A_TTL_SU += $su_ttl;
}
$ByORDER .= '<tr class="list_txt">';
$ByORDER .= '<td colspan="5">Total</td>';
// $ByORDER .= '<td>'.$Target_Qty_ttl.'</td>';
// $ByORDER .= '<td class="list_txt_bg_daily">'.number_format($Target_SU_ttl,2).'</td>';
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByORDER .= '<td colspan="2">&nbsp;</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByORDER .= '<td>'.$A_TT_QTY[$key].'</td>';
    }
}
$ByORDER .= '<td><div align="right">'.$A_TTL_QTY.' <font color="red">('.( !empty($A_TTL_F_QTY)? $A_TTL_F_QTY:0).')</font></div></td>';
$ByORDER .= '<td class="list_txt_bg_daily">'.number_format($A_TTL_SU,2).'</td>';
$ByORDER .= '</tr>';
$ByORDER .= '</table>';



if($list=='true'){
echo $ByLINE;
echo '<span class="note_bg">Full Capacity：[ ( 到班人數 * 8 ) + ( 加班人數 * ( 加班時數 - 0.5 ) ) ] * 1.2</span><br>';
}

if($ByLINE_ORDER && $bar=='true'){

require_once ('lib/src/jpgraph.php');
require_once ('lib/src/jpgraph_line.php');
require_once ('lib/src/jpgraph_bar.php');
 
// Create the graph. 產生圖檔的大小
$graph = new Graph(880,260);
$graph->SetScale('textlin');
$graph->SetMarginColor('white');
 
// Setup title 
$graph->title->Set("DAILY OUT PUT (SU.) ".$Date.(( $Date === date('Y-m-d')) ? " \n ( ".$time." )" : "") );
// $graph->title->SetLayout(1);
// 長條圖在圖檔中間的邊距位置
$graph->img->SetMargin(40,20,60,40);
$graph->legend->Pos(0.5,0.13,"center","top");//位置
$graph->legend->SetLayout(1);

# 橫向說明位置
$graph->xaxis->title->Set('Line|Order');
$graph->xaxis->SetTickLabels($ByLINE_ORDER); # 橫向說明陣列 array(第一組,第二組,...);
$graph->xaxis->title->SetMargin(-20); 

# 直向說明位置
$graph->yaxis->title->Set('SU.');
$graph->yaxis->SetLabelAngle(0);
$graph->yaxis->title->SetMargin(1); 



$ByLINE_Traget = $ByLINE_Capacity;
# 計算超出目標產量
$ByLINE_Plus = array();
foreach($ByLINE_qty as $k => $v){
    if( $ByLINE_Traget[$k] > 0 ) {

        if( $ByLINE_qty[$k] > $ByLINE_Traget[$k] ) {
            $ByLINE_Plus[$k] = $ByLINE_qty[$k] - $ByLINE_Traget[$k];
            $ByLINE_Traget[$k] = 0;
        } else {
            $ByLINE_Traget[$k] = $ByLINE_Traget[$k] - $ByLINE_qty[$k];
        }
    
    } else {
        $ByLINE_Traget[$k] = 0;
        $ByLINE_Plus[$k] = 0;
    }
    
    if( empty($ByLINE_Plus[$k]) ){
        $ByLINE_Plus[$k] = 0;
    }
}

// Create the first bar
# 長條圖 顏色區塊設定 有幾個顏色就打幾個
$bplot = new BarPlot($ByLINE_qty); # 產量陣列 array(10,12,35,15);
$bplot->SetFillGradient('olivedrab1','olivedrab4:0.8',GRAD_VERT); # 柱狀顏色
$bplot->SetColor('darkred');
$bplot->SetWeight(0);
$bplot->SetLegend('Output'); # 說明
 
// Create the second bar
$bplot2 = new BarPlot($ByLINE_Traget); # 目標陣列 array(10,12,35,15);
$bplot2->SetFillGradient('AntiqueWhite2','red:0.8',GRAD_VERT); # 柱狀顏色
$bplot2->SetColor('darkgreen');
$bplot2->SetWeight(0);
$bplot2->SetLegend('Full Capacity'); # 說明

// Create the second bar
$bplot3 = new BarPlot($ByLINE_Plus); # 超過產量陣列 array(10,12,35,15);
$bplot3->SetFillgradient('AntiqueWhite1','orange1:0.8',GRAD_VERT);  # 柱狀顏色
$bplot3->SetColor('darkgreen');
$bplot3->SetWeight(0);
$bplot3->SetLegend('Output Over'); # 說明

// Create the second bar
// $bplot4 = new BarPlot($ByLINE_Capacity); # 超過產量陣列 array(10,12,35,15);

// $bplot4->SetColor('darkgreen');
// $bplot4->SetWeight(0);

// $bplot4=new LinePlot($ByLINE_Capacity);
// $bplot4->SetFillgradient('AntiqueWhite1','orange1:0.8',GRAD_VERT);  # 柱狀顏色
// $bplot4->SetColor('#8f09f7');
// $bplot4->SetWeight(2);
// $bplot4->SetLegend('Full Capacity'); # 說明


// And join them in an accumulated bar
// $accbplot = new AccBarPlot(array($bplot,$bplot2)); 
$accbplot = new AccBarPlot(array($bplot,$bplot2,$bplot3)); #合併柱狀
$accbplot->SetColor('darkgray');
$accbplot->SetWeight(1);
$graph->Add($accbplot);

// $graph->Add($bplot4);

$gdImgHandler = $graph->Stroke(_IMG_HANDLER);
$fileName = "images/w_daily_out.png"; #產生圖檔位置
$graph->img->Stream($fileName);
// $graph->Stroke();



echo '<img src="images/w_daily_out.png?'.date('mdhis').'"><br>';
}

if($list=='true'){
echo $ByORDER;
}

$Dates = str_replace('-','',$Date);
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
            DD.submit();
        }

    });

</script>
';



page_display($op,$AUTH,'');
break;
#
#
#
#
#
#
# :get_bl_num
case "get_monitor":
check_authority($AUTH,"view");

$ipLong = ip2long($_SERVER["REMOTE_ADDR"]);
$time = ( $ipLong >= '1906311168' && $ipLong <= '1908408319' )?
date("H:i l",mktime (date(H)-1, date(i), date(s), date(m), date(d), date(Y))):
'TPE. Time '.date("H:i l").' ) ~ ( Vietnam Time '.date("H:i",mktime (date(H)-1, date(i), date(s), date(m), date(d), date(Y))) ;

$_SESSION['Date'] = $Date = !empty($_SESSION['Date']) ? $_SESSION['Date'] : date('Y-m-d') ;
$_SESSION['Date'] = $Date = !empty($_POST['PHP_Date']) ? $_POST['PHP_Date'] : $_SESSION['Date'] ;

$daily = $W_DAILY_OUT->get_daily_out_put_for_line($_SESSION['Date'],$_GET['PHP_line']);
$capacity = $W_DAILY_OUT->get_daily_capacity_for_line($_SESSION['Date'],$_GET['PHP_line']);
// $scm = $W_DAILY_OUT->get_scm_capacity($_SESSION['Date'],'LY');
$scm = $W_DAILY_OUT->get_scm_full_capacity_for_line($_SESSION['Date'],'LY',$_GET['PHP_line']);

// print_r($scm);


$TT_VIEW = array(
'T1' => '07:30<br>08:30' ,
'T2' => '08:30<br>09:30' ,
'T3' => '09:30<br>10:30' ,
'T4' => '10:30<br>11:30' ,
'T5' => '12:30<br>13:30' ,
'T6' => '13:30<br>14:30' ,
'T7' => '14:30<br>15:30' ,
'T8' => '15:30<br>16:30' ,
'T9' => '16:30<br>24:00' 
);

$TT_A1 = array('T1'=>'07:30','T2'=>'08:30','T3'=>'09:30','T4'=>'10:30','T5'=>'12:30','T6'=>'13:30','T7'=>'14:30','T8'=>'15:30','T9'=>'16:30');
$TT_A2 = array('T1'=>'08:30','T2'=>'09:30','T3'=>'10:30','T4'=>'11:30','T5'=>'13:30','T6'=>'14:30','T7'=>'15:30','T8'=>'16:30','T9'=>'24:00');
// echo "\r\n";

$CPCT = $LINE = $CPCT_ORD = $CPCT_LINE = array();

foreach($capacity as $row){

    # 過濾目前重複設定訂單在同條線生產
    if( !in_array( (substr($row['SectionCode'],-2,2)*1) , $CPCT_ORD[$row['LotId']]['Line'] ) ){
        $CPCT_ORD[$row['LotId']]['Target_Qty'] += $row['Qty'];
        $CPCT_ORD[$row['LotId']]['ie'] = ($row['ie2'] > 0)?$row['ie2']:$row['ie1'];
        $CPCT_ORD[$row['LotId']]['ie1'] = $row['ie1'];
        $CPCT_ORD[$row['LotId']]['ie2'] = $row['ie2'];
        $CPCT_ORD[$row['LotId']]['ord_qty'] = $row['qty'];
        $CPCT_ORD[$row['LotId']]['scm_rem_qty'] = $row['qty'] - $W_DAILY_OUT->get_scm_output($row['LotId'],$_SESSION['Date']);
        $CPCT_ORD[$row['LotId']]['rfid_rem_qty'] = $row['qty'] - $W_DAILY_OUT->get_rfid_output($row['LotId'],$_SESSION['Date']);
        $CPCT_ORD[$row['LotId']]['Line'][] = (substr($row['SectionCode'],-2,2)*1);
        $CPCT[] = $row['LotId'].(substr($row['SectionCode'],-2,2)*1);
    }
    
    $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']]['Target_Qty'] = $row['Qty'];
    // $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']][$row['LotId']]['Target_Qty'] = $row['Qty'];
    // $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)]['Order'] = $row['LotId'];
    $LINE[] = (substr($row['SectionCode'],-2,2)*1);
    // echo (substr($row['SectionCode'],-2,2)*1)."\r\n";
    // echo (substr($row['SectionCode'],-2,2)*1).' '.$row['LotId'].'<br>';
    // $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']]['Reader'] = $W_DAILY_OUT->get_set_num((substr($row['SectionCode'],-2,2)*1),$row['LotId']);
}



$WORKDONE_ORD = $WORKDONE_LINE = array();
foreach($daily as $row){
    $SONumber = $row['SONumber'];
    $Line = $row['Line'];
    $LINE[] = $Line;
    foreach($TT_A1 as $Time => $v){
        // echo $TT_A1[$Time].'-'.$Line.'-'.$SONumber.'-'.$CPCT_LINE[$Line][$SONumber]['Target_Qty'].'<br>';
        if($CPCT_LINE[$Line][$SONumber]['Target_Qty'] > 0 ){
            if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time] += $row['PackedQty']; }
            if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time.'_F'] += $row['FailedQty']; }
        }
        if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Line][$SONumber][$Time] += $row['PackedQty']; }
        if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Line][$SONumber][$Time.'_F'] += $row['FailedQty']; }
    }
}

foreach($LINE as $L_K => $L_V ){
    
    $line = $L_V;
    
    if ( $CPCT_LINE[$line] ) {

        foreach($CPCT_LINE[$line] as $WK_ORDER => $WK_TT){
		}
	}
}

// print_r($CPCT_ORD);

$op['line'] = $_GET['PHP_line'];
$op['dates'] = $_SESSION['Date'];
$op['TT_A1'] = $TT_A1;
$op['TT_A2'] = $TT_A2;
$op['WORKDONE_ORD'] = $WORKDONE_ORD;
page_display($op,$AUTH,'w_note.html');
break;
#
#
#
#
#
#
} # CASE END
?>