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
include_once($config['root_dir']."/lib/w_line_qc.class.php");
$W_LINE_QC = new W_LINE_QC();
if (!$W_LINE_QC->init($mysql,"log")) { print "error!! cannot initialize database for W_LINE_QC class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '100';
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
$_SESSION['PHP_list'] = $list = !empty($_POST['PHP_list']) ? $_POST['PHP_list'] : ( !empty($_SESSION['PHP_list']) ? $_SESSION['PHP_list'] : 'true' ) ;
$_SESSION['PHP_bar'] = $bar = !empty($_POST['PHP_bar']) ? $_POST['PHP_bar'] : ( !empty($_SESSION['PHP_bar']) ? $_SESSION['PHP_bar'] : 'false' ) ;
$_SESSION['PHP_auto'] = $auto = !empty($_POST['PHP_auto']) ? $_POST['PHP_auto'] : ( !empty($_SESSION['PHP_auto']) ? $_SESSION['PHP_auto'] : 'true' ) ;

// $op['css'] = array( 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
// $op['js'] = array( 'js/jquery.min.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' , 'js/jquery.blockUI.js' );
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
// echo date('mdhis');
echo '" />
    <script type="text/javascript" src="js/calendar/js/jscal2.js"></script>
    <script type="text/javascript" src="js/calendar/js/lang/en.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/supl_ship.js"></script>
</head>
<script>
(function($){
    $(document).ready(function(){
        // var chkStatus = $("input[id*=\'checklist\'][type=\'checkbox\']").attr(\'checked\');
    
        // $("input[id*=\'chk\'][type=\'checkbox\']").attr(\'checked\',true);
        $("input[id^=\'check\']").live("click", function () {
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

    });
})(jQuery);
</script>
';
// 
// 
$ByCHECK = '';
$ByCHECK .= '<div class="subtitle">LINE QC <font size="6" style="cursor:pointer;font-weight:bold;" id="calendar-trigger" color="blue">'.(( date('Y-m-d') == $_SESSION['Date'] )? date('Y-m-d').'</font><font size="3" style="font-weight:bold;" id="calendar-trigger" color="blue"> ( '.$time.' )':$_SESSION['Date']).'</font> <input type="hidden" id="calendar-inputField" /></div>';
$ByCHECK .= '&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp; ';
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
echo '<input type="hidden" id="input-list" name="PHP_list" size="8" value="'.$list.'">';
echo '<input type="hidden" id="input-bar" name="PHP_bar" size="8" value="'.$bar.'">';
echo '<input type="hidden" id="input-auto" name="PHP_auto" size="8" value="'.$auto.'">';
echo '</form>';
// echo '<font size="6">'.$_SESSION['Date'].'</font><br>';


$daily = $W_LINE_QC->get_daily_out_put($_SESSION['Date']);
$capacity = $W_LINE_QC->get_daily_capacity($_SESSION['Date']);

// print_r($daily);


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
        $CPCT_ORD[$row['LotId']]['Line'][] = (substr($row['SectionCode'],-2,2)*1);
        $CPCT[] = $row['LotId'].(substr($row['SectionCode'],-2,2)*1);
    }
    
    $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']]['Target_Qty'] = $row['Qty'];
    // $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)]['Order'] = $row['LotId'];
    $LINE[] = (substr($row['SectionCode'],-2,2)*1);
    // echo (substr($row['SectionCode'],-2,2)*1)."\r\n";
    // echo (substr($row['SectionCode'],-2,2)*1).' '.$row['LotId'].'<br>';
    $CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']]['Reader'] = $W_LINE_QC->get_set_num((substr($row['SectionCode'],-2,2)*1),$row['LotId'],$Date);
}



$WORKDONE_ORD = $WORKDONE_LINE = array();
foreach($daily as $row){
    $SONumber = $row['SONumber'];
    $Line = $row['Line'];
    $LINE[] = $Line;
    foreach($TT_A1 as $Time => $v){
        // echo $TT_A1[$Time];
        if($CPCT_LINE[$Line][$SONumber]['Target_Qty'] > 0 ){
            if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time] += $row['PackedQty']; }
            if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time.'_F'] += $row['FailedQty']; }
        }
        if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Line][$SONumber][$Time] += $row['PackedQty']; }
        if( substr($row['ScanTime'],11,8) >= $TT_A1[$Time] && substr($row['ScanTime'],11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Line][$SONumber][$Time.'_F'] += $row['FailedQty']; }
    }
}



# By LINE
$ByLINE = '';
$ByLINE .= '<div class="subtitle"> LIST ( By LINE )</div>';
$ByLINE .= '<table border="0" cellspacing="0" cellpadding="0" cols="0"><tr class="title_output"><td rowspan="2">Line</td><td rowspan="2">Order</td><td colspan="2">Target</td><td rowspan="2">Calc.<br>I.E.</td><td rowspan="2">Final<br>I.E.</td>';
foreach($TT_VIEW as $key => $val ){
$ByLINE .= '<td rowspan="2">'.$val.'</td>';
}
$ByLINE .= '<td colspan="2">Output</td><td rowspan="2">EFF %</td>';
$ByLINE .= '</tr>';
$ByLINE .= '<tr class="title_output"><td>Qty</td><td>SU.</td><td>Qty</td><td>SU.</td></tr>';

$LINE= array_unique($LINE);
sort($LINE);
$A_TT_QTY = array();
$Target_Qty_ttl = $Target_SU_ttl = 0;
$A_TTL_QTY = 0;
$A_TTL_SU = 0;

$ByLINE_ORDER = $ByLINE_Traget = $ByLINE_qty = array();
// print_r($LINE);
foreach($LINE as $L_K => $L_V ){
    
    $line = $L_V;
    
    if ( $CPCT_LINE[$line] ) {

        foreach($CPCT_LINE[$line] as $WK_ORDER => $WK_TT){
            $Target_SU = $su_ttl = $qty_ttl = 0;
            $order_num = trim($WK_ORDER);
            
            $Target_Qty = $CPCT_LINE[$line][$order_num]['Target_Qty'];
            $Reader = $CPCT_LINE[$line][$order_num]['Reader'];
            if(!empty($Target_Qty)){
            $Target_Qty_ttl += $Target_Qty;
            
            $ie = $CPCT_ORD[$order_num]['ie'];
            
            $Target_SU = $Target_Qty*$ie;
            $Target_SU_ttl += $Target_SU;
            }
            $ie1 = $CPCT_ORD[$order_num]['ie1'];
            $ie2 = $CPCT_ORD[$order_num]['ie2'];
            
            if( $WORKDONE_LINE[$line] ) {

                $ByLINE .= '<tr class="list_txt" id="ship_link">';
                $ByLINE .= '<td title="'.$Reader.'">'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td>'.$Target_Qty.'</td>';
                $ByLINE .= '<td>'.number_format($Target_SU,2).'</td>';
                $ByLINE .= '<td>'.$ie1.'</td>';
                $ByLINE .= '<td>'.$ie2.'</td>';
            
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                
                foreach($TT_VIEW as $key => $val ){
                    $qty = $WORKDONE_LINE[$line][$WK_ORDER][$key];
                    $qty_f = $WORKDONE_LINE[$line][$WK_ORDER][$key."_F"];
                    $qty = (!empty($qty)?$qty-$qty_f:0);
                    $ByLINE .= '<td>'.$qty.
                         '<font color="red">'.(!empty($qty_f)?':-'.$qty_f:'&nbsp;').'</font></td>';
                    # SU
                    $su_ttl += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
                    $qty_ttl += $qty;
                    $A_TT_QTY[$key] += $qty;
                }
                
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = $su_ttl;
                
                $ByLINE .= '<td>'.$qty_ttl.'</td>';
                $ByLINE .= '<td>'.number_format($su_ttl,2).'</td>';
                $ByLINE .= '<td>'.number_format($su_ttl/$Target_SU*100).'</td>';
                $ByLINE .= '</tr>';
                $A_TTL_QTY += $qty_ttl;
                $A_TTL_SU += $su_ttl;
                
            } else {

                $ByLINE .= '<tr class="list_txt" id="ship_link">';
                $ByLINE .= '<td>'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td>'.$Target_Qty.'</td>';
                $ByLINE .= '<td>'.number_format($Target_SU,2).'</td>';
                $ByLINE .= '<td>'.$ie1.'</td>';
                $ByLINE .= '<td>'.$ie2.'</td>';
                
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = 0;
                foreach($TT_VIEW as $key => $val ){
                    $ByLINE .= '<td>0</td>';
                }
                $ByLINE .= '<td>0</td>';
                $ByLINE .= '<td>'.number_format(0,2).'</td>';
                $ByLINE .= '<td>0</td>';
                $ByLINE .= '</tr>';            
            }
        }
    } else {
        foreach($WORKDONE_LINE[$line] as $WK_ORDER => $WK_TT){
            $ie = $ie1 = $ie2 = $Target_SU = $su_ttl = $qty_ttl = 0;
            $order_num = trim($WK_ORDER);
            
            $Target_Qty = $CPCT_LINE[$line][$order_num]['Target_Qty'];
            if(!empty($Target_Qty)){
                $Target_Qty_ttl += $Target_Qty;
                
                $ie = $CPCT_ORD[$order_num]['ie'];
                
                $Target_SU = $Target_Qty*$ie;
                $Target_SU_ttl += $Target_SU;
                $ie1 = $CPCT_ORD[$order_num]['ie1'];
                $ie2 = $CPCT_ORD[$order_num]['ie2'];
            }
            if( $WORKDONE_LINE[$line] ) {

                $ByLINE .= '<tr class="list_txt" id="ship_link">';
                $ByLINE .= '<td>'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td>'.$Target_Qty.'</td>';
                $ByLINE .= '<td>'.number_format($Target_SU,2).'</td>';
                $ByLINE .= '<td>'.$ie1.'</td>';
                $ByLINE .= '<td>'.$ie2.'</td>';
            
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                foreach($TT_VIEW as $key => $val ){
                    $qtys = $WORKDONE_LINE[$line][$WK_ORDER][$key];
                    $qty_f = $WORKDONE_LINE[$line][$WK_ORDER][$key."_F"];
                    $qtys = (!empty($qtys)?$qtys-$qty_f:0);
                    $qty = 0;
                    $ByLINE .= '<td><font color="blue">'.$qtys.
                         '</font><font color="red">'.(!empty($qty_f)?':-'.$qty_f:'&nbsp;').'</font></td>';
                    # SU
                    $su_ttl += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
                    $qty_ttl += $qty;
                    $A_TT_QTY[$key] += $qty;
                }
                
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = $su_ttl;
                
                $ByLINE .= '<td>'.(!empty($Target_Qty)?$qty_ttl:0).'</td>';
                $ByLINE .= '<td>'.number_format(!empty($Target_Qty)?$su_ttl:0,2).'</td>';
                $ByLINE .= '<td>'.number_format(!empty($Target_Qty)?$su_ttl/$Target_SU*100:0).'</td>';
                $ByLINE .= '</tr>';
                $A_TTL_QTY += (!empty($Target_Qty)?$qty_ttl:0);
                $A_TTL_SU += (!empty($Target_Qty)?$su_ttl:0);
                
            } else {

                $ByLINE .= '<tr class="list_txt" id="ship_link">';
                $ByLINE .= '<td>'.$line.'</td>';
                $ByLINE .= '<td>'.$order_num.'</td>';
                $ByLINE .= '<td>'.$Target_Qty.'</td>';
                $ByLINE .= '<td>'.number_format($Target_SU,2).'</td>';
                $ByLINE .= '<td>'.$ie1.'</td>';
                $ByLINE .= '<td>'.$ie2.'</td>';
                
                $ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
                $ByLINE_Traget[] = $Target_SU;
                $ByLINE_qty[] = 0;
                foreach($TT_VIEW as $key => $val ){
                    $ByLINE .= '<td>0</td>';
                }
                $ByLINE .= '<td>0</td>';
                $ByLINE .= '<td>'.number_format(0,2).'</td>';
                $ByLINE .= '<td>0</td>';
                $ByLINE .= '</tr>';            
            }
        }
    }
}

$ByLINE .= '<tr class="list_txt" id="ship_link" height="2">';
$ByLINE .= '<td colspan="2"></td>';
$ByLINE .= '<td></td>';
$ByLINE .= '<td colspan="2"></td>';
foreach($TT_VIEW as $key => $val ){
    $ByLINE .= '<td></td>';
}
$ByLINE .= '<td></td>';
$ByLINE .= '<td></td>';
$ByLINE .= '<td colspan="1"></td></tr>';


$ByLINE .= '<tr class="list_txt" id="ship_link">';
$ByLINE .= '<td colspan="2">Total</td>';
$ByLINE .= '<td>'.$Target_Qty_ttl.'</td>';
$ByLINE .= '<td>'.number_format($Target_SU_ttl,2).'</td>';
$ByLINE .= '<td colspan="2">&nbsp;</td>';
foreach($TT_VIEW as $key => $val ){
    $ByLINE .= '<td>'.$A_TT_QTY[$key].'</td>';
}
$ByLINE .= '<td>'.$A_TTL_QTY.'</td>';
$ByLINE .= '<td>'.number_format($A_TTL_SU,2).'</td>';
$ByLINE .= '<td>'.number_format($A_TTL_SU/$Target_SU_ttl*100).'</td></tr>';

$ByLINE .= '</table>';

$ByLINE .= '<p>';


# By ORDER
$ByORDER = '';
$M=0;
$ByORDER .= '<br><div class="subtitle"> LIST ( By ORDER )</div>';
$ByORDER .= '<table border="0" cellspacing="0" cellpadding="0" cols="0"><tr class="title_output"><td rowspan="2">Order</td><td rowspan="2">Line</td><td colspan="2">Target</td><td rowspan="2">Calc.<br>I.E.</td><td rowspan="2">Final<br>I.E.</td>';
foreach($TT_VIEW as $key => $val ){
    $ByORDER .= '<td rowspan="2">'.$val.'</td>';
}

$ByORDER .= '<td colspan="2">Output</td>';
$ByORDER .= '</tr>';
$ByORDER .= '<tr class="title_output"><td>Qty</td><td>SU.</td><td>Qty</td><td>SU.</td></tr>';

$Target_Qty_ttl = $Target_SU_ttl = 0;
$A_TTL_QTY = 0;
$A_TTL_SU = 0;
$A_TT_QTY = array();
foreach($CPCT_ORD as $row => $qtys){

    $Target_Qty = $qtys['Target_Qty'];
    $Target_Qty_ttl += $Target_Qty;
    
    $ie = $qtys['ie'];
    
    $Target_SU = $Target_Qty*$ie;
    $Target_SU_ttl += $Target_SU;
        
    $ByORDER .= '<tr class="list_txt" id="ship_link">';
    $ByORDER .= '<td>'.$row.'</td>';
    $ByORDER .= '<td>'.implode (",", $qtys['Line']).'</td>';
    $ByORDER .= '<td>'.$Target_Qty.'</td>';
    $ByORDER .= '<td>'.number_format($Target_SU,2).'</td>';
    $ByORDER .= '<td>'.$qtys['ie1'].'</td>';
    $ByORDER .= '<td>'.$qtys['ie2'].'</td>';
    $su_ttl = 0;
    $qty_ttl = 0;
    foreach($TT_VIEW as $key => $val ){
        $qty = $WORKDONE_ORD[$row][$key];
        $qty_f = $WORKDONE_ORD[$row][$key."_F"];
        $qty = (!empty($qty)?$qty-$qty_f:0);
    
        $ByORDER .= '<td>'.$qty.
             '<font color="red">'.(!empty($qty_f)?':-'.$qty_f:'&nbsp;').'</font></td>';
        # SU
        $su_ttl += $ie * $qty;
        $qty_ttl += $qty;
        // $A_TTL_QTY += (!empty($Target_Qty)?$qty_ttl:0);
        // $A_TTL_SU += (!empty($Target_Qty)?$su_ttl:0);
        $A_TT_QTY[$key] += $qty;
        // $ie += $qtys['ie'] * $qty;
        // $qty += $qty;
    }
    $ByORDER .= '<td>'.$qty_ttl.'</td>';
    $ByORDER .= '<td>'.number_format($su_ttl,2).'</td>';
    $ByORDER .= '</tr>';
    $A_TTL_QTY += $qty_ttl;
    $A_TTL_SU += $su_ttl;
}
$ByORDER .= '<tr class="list_txt" id="ship_link">';
$ByORDER .= '<td colspan="2">Total</td>';
$ByORDER .= '<td>'.$Target_Qty_ttl.'</td>';
$ByORDER .= '<td>'.number_format($Target_SU_ttl,2).'</td>';
$ByORDER .= '<td colspan="2">&nbsp;</td>';
foreach($TT_VIEW as $key => $val ){
    $ByORDER .= '<td>'.$A_TT_QTY[$key].'</td>';
}
$ByORDER .= '<td>'.$A_TTL_QTY.'</td>';
$ByORDER .= '<td>'.number_format($A_TTL_SU,2).'</td>';
$ByORDER .= '</tr>';
$ByORDER .= '</table>';



if($list=='true'){
echo $ByLINE;
}
if($ByLINE_ORDER && $bar=='true'){

require_once ('lib/src/jpgraph.php');
require_once ('lib/src/jpgraph_bar.php');
 
$datay1=array(13,8,19,7,17,6);
$datay2=array(4,5,2,7,5,25);
 
// Create the graph.
$graph = new Graph(880,260);
$graph->SetScale('textlin');
$graph->SetMarginColor('white');
 
// Setup title
$graph->title->Set('DAILY OUT PUT (SU.) \n '.$time);
// $graph->title->Set('DAILY OUT PUT (SU.) <br> '.$time);
 
$order = array("Andrew\nTait","Thomas\nAnderssen","Kevin\nSpacey","Nick\nDavidsson",
"David\nLindquist","Jason\nTait","Lorin\nPersson");
$graph->xaxis->SetTickLabels($ByLINE_ORDER);

$ByLINE_Plus = array();
foreach($ByLINE_qty as $k => $v){
    // echo $ByLINE_qty[$k].':'.$ByLINE_Traget[$k].':'.$ByLINE_Plus[$k].'<br>';

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
    
    
    // $ByLINE_qty[$k]
    // $ByLINE_Traget[$k]
    // $ByLINE_Plus[$k]
    // echo $ByLINE_qty[$k].':'.$ByLINE_Traget[$k].':'.$ByLINE_Plus[$k].'<br><br><p>';
}

// Create the first bar
$bplot = new BarPlot($ByLINE_qty);
$bplot->SetFillGradient('olivedrab1','olivedrab4:0.8',GRAD_VERT);
$bplot->SetColor('darkred');
$bplot->SetWeight(0);
 
// Create the second bar
$bplot2 = new BarPlot($ByLINE_Traget);
$bplot2->SetFillGradient('AntiqueWhite2','red:0.8',GRAD_VERT);
// $bplot2->SetFillGradient('olivedrab1','olivedrab4',GRAD_VERT);
$bplot2->SetColor('darkgreen');
$bplot2->SetWeight(0);

// Create the second bar
$bplot3 = new BarPlot($ByLINE_Plus);
// $bplot3->SetFillGradient('AntiqueWhite1','ghostwhite:0.6',GRAD_VERT);
$bplot3->SetFillgradient('AntiqueWhite1','orange1:0.8',GRAD_VERT); 
// $bplot3->SetFillGradient('olivedrab1','olivedrab4',GRAD_VERT);
$bplot3->SetColor('darkgreen');
$bplot3->SetWeight(0);


 
// And join them in an accumulated bar
// $accbplot = new AccBarPlot(array($bplot,$bplot2));
$accbplot = new AccBarPlot(array($bplot,$bplot2,$bplot3));
$accbplot->SetColor('darkgray');
$accbplot->SetWeight(1);
$graph->Add($accbplot);

$gdImgHandler = $graph->Stroke(_IMG_HANDLER);
$fileName = "images/w_line_qc.png";
$graph->img->Stream($fileName);
// $graph->Stroke();



echo '<img src="images/w_line_qc.png?'.date('mdhis').'">';
}

if($list=='true'){
echo $ByORDER;
}

$Dates = str_replace('-','',$Date);
echo '
<script>

    cal= Calendar.setup({
    
        weekNumbers : false,
        trigger     : "calendar-trigger" , 
        inputField  : "calendar-inputField" , 
        date        : '.$Dates.' ,
        selection   : '.$Dates.' ,
        showTime    : false , 
        
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