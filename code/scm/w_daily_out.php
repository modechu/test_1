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
			var ordernum = $(this).attr(\'ordernum\');
			var thedate = $(this).attr(\'thedate\');
			var gofile ="w_daily_out.php?PHP_action=get_monitor&PHP_line="+line+"&PHP_order="+ordernum+"&PHP_date="+thedate;
			var nm = line;
			window.open(gofile,\'_bank\',\'toolbar=no,location=no,directories=no,status=no,bar=no,menu=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=1280,height=720 top=30, left=30\');
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

//$test = $W_DAILY_OUT->get_online_date($daily);
$capacity = $W_DAILY_OUT->get_daily_capacity($_SESSION['Date']);
$capacity_new = $W_DAILY_OUT->get_online_date($capacity);//多一個上線天數
// $scm = $W_DAILY_OUT->get_scm_capacity($_SESSION['Date'],'LY');
$scm = $W_DAILY_OUT->get_scm_full_capacity($_SESSION['Date'],'LY');
//print_r($daily);
//print_r($capacity);
//print_r($capacity_new);


$TT_VIEW = array(
'T1' => '07:30<br>08:30' ,
'T2' => '08:30<br>09:30' ,
'T3' => '09:30<br>10:30' ,
'T4' => '10:30<br>12:30' ,
'T5' => '12:30<br>13:30' ,
'T6' => '13:30<br>14:30' ,
'T7' => '14:30<br>15:30' ,
'T8' => '15:30<br>16:30' ,
'T9' => '16:30<br>24:00' 
);

$TT_A1 = array('T1'=>'07:30','T2'=>'08:30','T3'=>'09:30','T4'=>'10:30','T5'=>'12:30','T6'=>'13:30','T7'=>'14:30','T8'=>'15:30','T9'=>'16:30');
$TT_A2 = array('T1'=>'08:30','T2'=>'09:30','T3'=>'10:30','T4'=>'12:30','T5'=>'13:30','T6'=>'14:30','T7'=>'15:30','T8'=>'16:30','T9'=>'24:00');
// echo "\r\n";

$CPCT = $LINE = $CPCT_ORD = $CPCT_LINE = array();

foreach($capacity_new as $row){//20140801修正

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
	$CPCT_LINE[(substr($row['SectionCode'],-2,2)*1)][$row['LotId']]['Online_Day'] = $row['online_day'];//20140801
    $LINE[] = (substr($row['SectionCode'],-2,2)*1);

}

//print_r($CPCT);//訂單有關
//print_r($CPCT_ORD);//訂單細節
//print_r($CPCT_LINE);//組別訂單目標產量
//print_r($LINE);//不知道





$WORKDONE_ORD = $WORKDONE_LINE = array();
foreach($daily as $row){
    $SONumber = $row['SONumber'];
    $Line = $row['Line'];
    $LINE[] = $Line;//有刷卡紀錄就加入Line的陣列中
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

//print_r($WORKDONE_LINE);//組別每小時件數
//print_r($WORKDONE_ORD);//訂單每小時件數
//print_r($daily);
# By LINE
//$ByLINE_New = '<table border="0" cellspacing="0" cellpadding="0" cols="0">';

$ByLINE_columns = '';
//$ByLINE_New = '<div class="subtitle"> 第27組LIST ( By LINE )</div>';//20140801
$ByLINE_columns .= '<tr class="title_output"><td rowspan="2">Line</td><td rowspan="2">Order</td><td rowspan="2">Full Capacity</td><td rowspan="2">Online Days</td>';
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_columns .= '<td colspan="2">Target</td>';
    $ByLINE_columns .= '<td rowspan="2">Calc.<br>I.E.</td><td rowspan="2">Final<br>I.E.</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE_columns .= '<td rowspan="2">'.$val.'</td>';
    }
}
$ByLINE_columns .= '<td colspan="2">Output</td><td rowspan="2">EFF %</td>';
$ByLINE_columns .= '</tr>';
$ByLINE_columns .= '<tr class="title_output">';
if( $detail == 'true' ){
$ByLINE_columns .= '<td>Qty</td><td>SU.</td>';
}
$ByLINE_columns .= '<td>Qty</td><td>SU.</td></tr>';


$ByLINE_New = '';
$ByLINE_New .= '<tr class="title_output">';
if( $detail == 'true' ){
	$ByLINE_New .= '<td colspan="20">';
	$ByLINE_New .= '<div class="onlineday_title"> 新開款訂單</div>'; //20140801
	$ByLINE_New .= '</td>';
}
else
{
	$ByLINE_New .= '<td colspan="7">';
	$ByLINE_New .= '<div class="onlineday_title"> 新開款訂單</div>'; //20140801
	$ByLINE_New .= '</td>';
}
$ByLINE_New .= '</tr>';



//$ByLINE_27 = '<table border="0" cellspacing="0" cellpadding="0" cols="0">';
/*2014-11-15 27組已經解散*/
$ByLINE_27 = '';
$ByLINE_27 .= '<tr class="title_output">';
if( $detail == 'true' ){
	$ByLINE_27 .= '<td colspan="20">';
	$ByLINE_27 .= '<div class="onlineday_title"> 一條龍實驗組</div>'; //20140801
	$ByLINE_27 .= '</td>';
}
else
{
	$ByLINE_27 .= '<td colspan="7">';
	$ByLINE_27 .= '<div class="onlineday_title"> 一條龍實驗組</div>'; //20140801
	$ByLINE_27 .= '</td>';
}
$ByLINE_27 .= '</tr>';
//$ByLINE_New = '<div class="subtitle"> 第27組LIST ( By LINE )</div>';//20140801




//$ByLINE = '<table border="0" cellspacing="0" cellpadding="0" cols="0">';
$ByLINE = '';
$ByLINE .= '<tr class="title_output">';
if( $detail == 'true' ){
	$ByLINE .= '<td colspan="20">';
	$ByLINE .= '<div class="onlineday_title"> 開款超過三天訂單</div>'; //20140801
	$ByLINE .= '</td>';
}
else
{
	$ByLINE .= '<td colspan="7">';
	$ByLINE .= '<div class="onlineday_title"> 開款超過三天訂單</div>'; //20140801
	$ByLINE .= '</td>';
}
$ByLINE .= '</tr>';
//$ByLINE_New = '<div class="subtitle"> 第27組LIST ( By LINE )</div>';//20140801
/* $ByLINE .= '<tr class="title_output"><td rowspan="2">Line</td><td rowspan="2">Order</td><td rowspan="2">Full Capacity</td><td rowspan="2">Online Days</td>';
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
$ByLINE .= '<td>Qty</td><td>SU.</td></tr>'; */

$LINE= array_unique($LINE);
sort($LINE);
$A_TT_QTY = array();
$A_TT_QTY_27 = array();
$A_TT_QTY_new = array();
$A_TT_QTY_over3 = array();
$Target_Qty_ttl = $Target_SU_ttl = $Capacity_SU_ttl = 0;
$Target_Qty_ttl_new = $Target_SU_ttl_new = $Capacity_SU_ttl_new = 0;
$Target_Qty_ttl_over3 = $Target_SU_ttl_over3 = $Capacity_SU_ttl_over3 = 0;
$Target_Qty_ttl_27 = $Target_SU_ttl_27 = $Capacity_SU_ttl_27 = 0;
$A_TTL_QTY = 0;
$A_TTL_QTY_27 = 0;
$A_TTL_QTY_new = 0;
$A_TTL_QTY_over3 = 0;
$A_TTL_F_QTY = 0;
$A_TTL_F_QTY_27 = 0;
$A_TTL_F_QTY_new = 0;
$A_TTL_F_QTY_over3 = 0;
$A_TTL_SU = 0;
$A_TTL_SU_27 = 0;
$A_TTL_SU_new = 0;
$A_TTL_SU_over3 = 0;


$ByLINE_ORDER = $ByLINE_Traget = $ByLINE_Capacity = $ByLINE_qty = array();
//開始組內容，不含欄位
//print_r($LINE);//組別(如果scm沒有output,wipone也沒有預排，但是有生產拍卡，表示員工有加班，但是不列入系統中)
//print_r($CPCT_LINE);//index為組別，內容有訂單號與目標產量

foreach($LINE as $L_K => $L_V ){
    //print_r($LINE);
    $line = $L_V;
    if ( $CPCT_LINE[$line] ) {
	//正常生產紀錄會在此判斷式
		/* echo "test1";
		echo "<br>"; */
		

        foreach($CPCT_LINE[$line] as $WK_ORDER => $WK_TT){
		
		
			$Capacity_SU = $Target_SU = $su_ttl = $qty_ttl = $f_qty_ttl = 0;
			$Capacity_SU_27 = $Target_SU_27 = $su_ttl_27 = $qty_ttl_27 = $f_qty_ttl_27 = 0;
			$Capacity_SU_new = $Target_SU_new = $su_ttl_new = $qty_ttl_new = $f_qty_ttl_new = 0;
			$Capacity_SU_over3 = $Target_SU_over3 = $su_ttl_over3 = $qty_ttl_over3 = $f_qty_ttl_over3 = 0;
			if($line == 25 or $line == 26 or $line == 1)//20140801
			{//20140801
					/*2014-11-15 27組已經解散*/
					$order_num = trim($WK_ORDER);
					//echo $scm[$line][$order_num]['full_su'];
					//echo '<br>';
					$Capacity_SU = $scm[$line][$order_num]['full_su'];
					// echo $line.'-'.$Capacity_SU.'<br>';
					// echo $order_num.'<br>';
					$Target_Qty = $CPCT_LINE[$line][$order_num]['Target_Qty'];
					$Reader = $CPCT_LINE[$line][$order_num]['Reader'];
					
					if(!empty($Target_Qty)){
						$ie = $CPCT_ORD[$order_num]['ie'];
						
						$Target_Qty_ttl += $Target_Qty;
						$Target_SU = $Target_Qty*$ie;
						$Target_SU_ttl += $Target_SU;
						$Capacity_SU_ttl += $Capacity_SU;
						
						$Target_Qty_ttl_27 += $Target_Qty;
						$Target_SU_27 = $Target_Qty*$ie;
						$Target_SU_ttl_27 += $Target_SU_27;
						$Capacity_SU_ttl_27 += $Capacity_SU;
					}
					
					$ie1 = $CPCT_ORD[$order_num]['ie1'];
					$ie2 = $CPCT_ORD[$order_num]['ie2'];
				//echo $WK_ORDER.'第'.$line.'組 OnlineDay='.$WK_TT['Online_Day'];
				//echo '<br>';
					if( $WORKDONE_LINE[$line] ) {

						$ByLINE_27 .= '<tr class="list_txt">';
						$ByLINE_27 .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
						$ByLINE_27 .= '<td>'.$order_num.'</td>';
						$ByLINE_27 .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td><td>'.$WK_TT['Online_Day'].'</td>';//全產能
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_27 .= '<td>'.$Target_Qty.'</td>';
							$ByLINE_27 .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
						}
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_27 .= '<td>'.$ie1.'</td>';
							$ByLINE_27 .= '<td>'.$ie2.'</td>';
						}
						
						$ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
						
						foreach($TT_VIEW as $key => $val ){
							$qty = $WORKDONE_LINE[$line][$WK_ORDER][$key];
							$qty_f = $WORKDONE_LINE[$line][$WK_ORDER][$key."_F"];
							$qtys = (!empty($qty)?$qty-$qty_f:0);
							# 是否顯示詳細資料
							if( $detail == 'true' ){
								$ByLINE_27 .= '<td>'.$qtys.
									 '<font color="red">'.(!empty($qty_f)?':-'.$qty_f:'').'</font> '.( !empty($qty)? '('.$qty.')':'').'</td>';
							}
							
							# SU
							$su_ttl += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
							$qty_ttl += $qty;
							$f_qty_ttl += $qty_f;
							$A_TT_QTY[$key] += $qty;
							
							$su_ttl_27 += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
							$qty_ttl_27 += $qty;
							$f_qty_ttl_27 += $qty_f;
							$A_TT_QTY_27[$key] += $qty;
							
						}
						
						$ByLINE_Capacity[] = $Capacity_SU;
						$ByLINE_Traget[] = $Target_SU;
						$ByLINE_qty[] = $su_ttl;
						
						$ByLINE_27 .= '<td><div align="right">'.$qty_ttl.' <font color="red">('.( !empty($f_qty_ttl)? $f_qty_ttl:0).')</font></div></td>';
						$ByLINE_27 .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($su_ttl,2).'</div></td>';
						$ByLINE_27 .= '<td><div align="right">'.number_format($su_ttl/$Capacity_SU*100).' %</div></td>';
						$ByLINE_27 .= '</tr>';
						$A_TTL_QTY += $qty_ttl;
						$A_TTL_F_QTY += $f_qty_ttl;
						$A_TTL_SU += $su_ttl;
						
						$A_TTL_QTY_27 += $qty_ttl_27;
						$A_TTL_F_QTY_27 += $f_qty_ttl_27;
						$A_TTL_SU_27 += $su_ttl_27;
						
						//echo $ByLINE;
						//echo "<hr>";
					
					} else {

						$ByLINE_27 .= '<tr class="list_txt">';
						//$ByLINE_27 .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
						$ByLINE_27 .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
						$ByLINE_27 .= '<td>'.$order_num.'</td>';
						$ByLINE_27 .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td><td>'.$WK_TT['Online_Day'].'</td>';
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_27 .= '<td>'.$Target_Qty.'</td>';
							$ByLINE_27 .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
						}
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_27 .= '<td>'.$ie1.'</td>';
							$ByLINE_27 .= '<td>'.$ie2.'</td>';
						}
						
						$ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
						$ByLINE_Capacity[] = $Capacity_SU;
						$ByLINE_Traget[] = $Target_SU;
						$ByLINE_qty[] = 0;
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							foreach($TT_VIEW as $key => $val ){
								$ByLINE_27 .= '<td>0</td>';
							}
						}
						$ByLINE_27 .= '<td><div align="right">0 <font color="red">(0)</font></div></td>';
						$ByLINE_27 .= '<td class="list_txt_bg_daily"><div align="right">'.number_format(0,2).'</div></td>';
						$ByLINE_27 .= '<td><div align="right">0 %</div></td>';
						$ByLINE_27 .= '</tr>';            
					}
					
			}//20140801
			else//20140801
			{//20140801
				if($WK_TT['Online_Day'] < 4)//20140801
				{//20140801
					//上線天數小於4天
					$order_num = trim($WK_ORDER);
					//echo $scm[$line][$order_num]['full_su'];
					//echo '<br>';
					$Capacity_SU = $scm[$line][$order_num]['full_su'];
					// echo $line.'-'.$Capacity_SU.'<br>';
					// echo $order_num.'<br>';
					$Target_Qty = $CPCT_LINE[$line][$order_num]['Target_Qty'];
					$Reader = $CPCT_LINE[$line][$order_num]['Reader'];
					
					if(!empty($Target_Qty)){

						$ie = $CPCT_ORD[$order_num]['ie'];
						
						$Target_Qty_ttl += $Target_Qty;
						$Target_SU = $Target_Qty*$ie;
						$Target_SU_ttl += $Target_SU;
						$Capacity_SU_ttl += $Capacity_SU;
						
						$Target_Qty_ttl_new += $Target_Qty;
						$Target_SU_new = $Target_Qty*$ie;
						$Target_SU_ttl_new += $Target_SU_new;
						$Capacity_SU_ttl_new += $Capacity_SU;
					}
					
					$ie1 = $CPCT_ORD[$order_num]['ie1'];
					$ie2 = $CPCT_ORD[$order_num]['ie2'];
					//echo $WK_ORDER.'小於四天'.$line.'組 OnlineDay='.$WK_TT['Online_Day'];
			        //echo '<br>';
					if( $WORKDONE_LINE[$line] ) {

						$ByLINE_New .= '<tr class="list_txt">';
						//$ByLINE_New .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
						$ByLINE_New .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
						$ByLINE_New .= '<td>'.$order_num.'</td>';
						$ByLINE_New .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td><td>'.$WK_TT['Online_Day'].'</td>';//全產能
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_New .= '<td>'.$Target_Qty.'</td>';
							$ByLINE_New .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
						}
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_New .= '<td>'.$ie1.'</td>';
							$ByLINE_New .= '<td>'.$ie2.'</td>';
						}
						
						$ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
						
						foreach($TT_VIEW as $key => $val ){
							$qty = $WORKDONE_LINE[$line][$WK_ORDER][$key];
							$qty_f = $WORKDONE_LINE[$line][$WK_ORDER][$key."_F"];
							$qtys = (!empty($qty)?$qty-$qty_f:0);
							# 是否顯示詳細資料
							if( $detail == 'true' ){
								$ByLINE_New .= '<td>'.$qtys.
									 '<font color="red">'.(!empty($qty_f)?':-'.$qty_f:'').'</font> '.( !empty($qty)? '('.$qty.')':'').'</td>';
							}
							
							# SU
							$su_ttl += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
							$qty_ttl += $qty;
							$f_qty_ttl += $qty_f;
							$A_TT_QTY[$key] += $qty;
							
							$su_ttl_new += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
							$qty_ttl_new += $qty;
							$f_qty_ttl_new += $qty_f;
							$A_TT_QTY_new[$key] += $qty;
						}
						
						$ByLINE_Capacity[] = $Capacity_SU;
						$ByLINE_Traget[] = $Target_SU;
						$ByLINE_qty[] = $su_ttl;
						
						$ByLINE_New .= '<td><div align="right">'.$qty_ttl.' <font color="red">('.( !empty($f_qty_ttl)? $f_qty_ttl:0).')</font></div></td>';
						$ByLINE_New .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($su_ttl,2).'</div></td>';
						$ByLINE_New .= '<td><div align="right">'.number_format($su_ttl/$Capacity_SU*100).' %</div></td>';
						$ByLINE_New .= '</tr>';
						$A_TTL_QTY += $qty_ttl;
						$A_TTL_F_QTY += $f_qty_ttl;
						$A_TTL_SU += $su_ttl;
						
						$A_TTL_QTY_new += $qty_ttl_new;
						$A_TTL_F_QTY_new += $f_qty_ttl_new;
						$A_TTL_SU_new += $su_ttl_new;
						//echo $ByLINE;
						//echo "<hr>";
					
					} else {

						$ByLINE_New .= '<tr class="list_txt">';
						// $ByLINE_New .= '<td>'.$line.'</td>';
                        //$ByLINE_New .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
						$ByLINE_New .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
						$ByLINE_New .= '<td>'.$order_num.'</td>';
						$ByLINE_New .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td><td>'.$WK_TT['Online_Day'].'</td>';
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_New .= '<td>'.$Target_Qty.'</td>';
							$ByLINE_New .= '<td class="list_txt_bg_daily">'.number_format($Target_SU,2).'</td>';
						}
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							$ByLINE_New .= '<td>'.$ie1.'</td>';
							$ByLINE_New .= '<td>'.$ie2.'</td>';
						}
						
						$ByLINE_ORDER[] = $line."\n|".substr($order_num,-4,4);
						$ByLINE_Capacity[] = $Capacity_SU;
						$ByLINE_Traget[] = $Target_SU;
						$ByLINE_qty[] = 0;
						
						# 是否顯示詳細資料
						if( $detail == 'true' ){
							foreach($TT_VIEW as $key => $val ){
								$ByLINE_New .= '<td>0</td>';
							}
						}
						$ByLINE_New .= '<td><div align="right">0 <font color="red">(0)</font></div></td>';
						$ByLINE_New .= '<td class="list_txt_bg_daily"><div align="right">'.number_format(0,2).'</div></td>';
						$ByLINE_New .= '<td><div align="right">0 %</div></td>';
						$ByLINE_New .= '</tr>';            
					}
				}//20140801
				else//20140801
				{//20140801
					//echo $ByLINE_New;
					$order_num = trim($WK_ORDER);
					//echo $scm[$line][$order_num]['full_su'];
					//echo '<br>';
					$Capacity_SU = $scm[$line][$order_num]['full_su'];
					// echo $line.'-'.$Capacity_SU.'<br>';
					// echo $order_num.'<br>';
					$Target_Qty = $CPCT_LINE[$line][$order_num]['Target_Qty'];
					$Reader = $CPCT_LINE[$line][$order_num]['Reader'];
					
					if(!empty($Target_Qty)){

						$ie = $CPCT_ORD[$order_num]['ie'];
						
						$Target_Qty_ttl += $Target_Qty;
						$Target_SU = $Target_Qty*$ie;
						$Target_SU_ttl += $Target_SU;
						$Capacity_SU_ttl += $Capacity_SU;
						
						$Target_Qty_ttl_over3 += $Target_Qty;
						$Target_SU_over3 = $Target_Qty*$ie;
						$Target_SU_ttl_over3 += $Target_SU_over3;
						$Capacity_SU_ttl_over3 += $Capacity_SU;
					}
					
					$ie1 = $CPCT_ORD[$order_num]['ie1'];
					$ie2 = $CPCT_ORD[$order_num]['ie2'];
				
					//echo $WK_ORDER.'大於四天'.$line.'組 OnlineDay='.$WK_TT['Online_Day'];
					//echo '<br>';
					if( $WORKDONE_LINE[$line] ) {

						$ByLINE .= '<tr class="list_txt">';
						//$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
						$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
						$ByLINE .= '<td>'.$order_num.'</td>';
						$ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td><td>'.$WK_TT['Online_Day'].'</td>';//全產能
						
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
							
							$su_ttl_over3 += $CPCT_ORD[$WK_ORDER]['ie'] * $qty;
							$qty_ttl_over3 += $qty;
							$f_qty_ttl_over3 += $qty_f;
							$A_TT_QTY_over3[$key] += $qty;
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
						
						$A_TTL_QTY_over3 += $qty_ttl_over3;
						$A_TTL_F_QTY_over3 += $f_qty_ttl_over3;
						$A_TTL_SU_over3 += $su_ttl_over3;
						//echo $ByLINE;
						//echo "<hr>";
					
					} else {

						$ByLINE .= '<tr class="list_txt">';
						// $ByLINE .= '<td>'.$line.'</td>';
                        //$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
						$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
						$ByLINE .= '<td>'.$order_num.'</td>';
						$ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU,2).'</td><td>'.$WK_TT['Online_Day'].'</td>';
						
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
				}//20140801
			}//20140801   ByLINE_qty  ByLINE_Target ByLINE_Plus
			
        }
		
    } else {
	//有隱藏式的生產紀錄，會在此判斷式
		/* echo $CPCT_LINE[$line];
		echo "<br>";
		echo $line;
		echo "<br>"; */
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
                // $ByLINE .= '<td>'.$line.'</td>';
                //$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
				$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
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
                // $ByLINE .= '<td>'.$line.'</td>';
                //$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
				$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
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
//exit;
//與總結分隔部分////////////////////////////////////////////////////////////////////////////////////////////////////////
$ByLINE_New .= '<tr class="list_txt" height="2">';
$ByLINE_New .= '<td colspan="2"></td>';
$ByLINE_New .= '<td></td><td></td>';
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_New .= '<td colspan="2"></td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE_New .= '<td></td>';
    }
}
$ByLINE_New .= '<td></td>';
$ByLINE_New .= '<td></td>';
$ByLINE_New .= '<td colspan="1"></td></tr>';

/*2014-11-15 27組已經解散*/
$ByLINE_27 .= '<tr class="list_txt" height="2">';
$ByLINE_27 .= '<td colspan="2"></td>';
$ByLINE_27 .= '<td></td><td></td>';
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_27 .= '<td colspan="2"></td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE_27 .= '<td></td>';
    }
}
$ByLINE_27 .= '<td></td>';
$ByLINE_27 .= '<td></td>';
$ByLINE_27 .= '<td colspan="1"></td></tr>';


$ByLINE .= '<tr class="list_txt" height="2">';
$ByLINE .= '<td colspan="2"></td>';
$ByLINE .= '<td></td><td></td>';
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
//與總結分隔部分########################################################################################################################################################################
//總結部分////////////////////////////////////////////////////////////////////////////////////////////////////////
$ByLINE_normal_summary = '<tr class="list_txt">';
$ByLINE_normal_summary .= '<td colspan="2" class="list_txt_bg_daily_total">Total<font color="red">(above the row)</font></td>';//2014-11-15 27組已經解散
$ByLINE_normal_summary .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format(($Capacity_SU_ttl_new + $Capacity_SU_ttl_over3),2).'</td><td class="list_txt_bg_daily_total">&nbsp;</td>';

# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_normal_summary .= '<td class="list_txt_bg_daily_total">'.($Target_Qty_ttl_new+$Target_Qty_ttl_over3).'</td>';
    $ByLINE_normal_summary .= '<td class="list_txt_bg_daily">'.number_format(($Target_SU_ttl_new+$Target_SU_ttl_over3),2).'</td>';
}
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_normal_summary .= '<td colspan="2" class="list_txt_bg_daily_total">&nbsp;</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE_normal_summary .= '<td class="list_txt_bg_daily_total">'.($A_TT_QTY_new[$key]+$A_TT_QTY_over3[$key]).'</td>';
    }
}
$ByLINE_normal_summary .= '<td class="list_txt_bg_daily_total"><div align="right">'.($A_TTL_QTY_over3+$A_TTL_QTY_new).' <font color="red">('.( !empty($A_TTL_F_QTY_over3)? ($A_TTL_F_QTY_over3+$A_TTL_F_QTY_new):0).')</font></div></td>';
$ByLINE_normal_summary .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($A_TTL_SU_over3+$A_TTL_SU_new,2).'</div></td>';
$ByLINE_normal_summary .= '<td class="list_txt_bg_daily_total"><div align="right">'.number_format(($A_TTL_SU_over3+$A_TTL_SU_new)/($Capacity_SU_ttl_over3+$Capacity_SU_ttl_new)*100).' %</div></td></tr>';
/*2014-11-15 27組已經解散*/
$ByLINE_total_summary = '<tr class="list_txt">';
$ByLINE_total_summary .= '<td colspan="2" class="list_txt_bg_daily_total">Total</td>';
$ByLINE_total_summary .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU_ttl,2).'</td><td class="list_txt_bg_daily_total">&nbsp;</td>';

# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_total_summary .= '<td class="list_txt_bg_daily_total">'.$Target_Qty_ttl.'</td>';
    $ByLINE_total_summary .= '<td class="list_txt_bg_daily">'.number_format($Target_SU_ttl,2).'</td>';
}
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_total_summary .= '<td colspan="2" class="list_txt_bg_daily_total">&nbsp;</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE_total_summary .= '<td class="list_txt_bg_daily_total">'.$A_TT_QTY[$key].'</td>';
    }
}
$ByLINE_total_summary .= '<td class="list_txt_bg_daily_total"><div align="right">'.$A_TTL_QTY.' <font color="red">('.( !empty($A_TTL_F_QTY)? $A_TTL_F_QTY:0).')</font></div></td>';
$ByLINE_total_summary .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($A_TTL_SU,2).'</div></td>';
$ByLINE_total_summary .= '<td class="list_txt_bg_daily_total"><div align="right">'.number_format($A_TTL_SU/$Capacity_SU_ttl*100).' %</div></td></tr>';



$ByLINE_New .= '<tr class="list_txt">';
$ByLINE_New .= '<td colspan="2">Sub Total</td>';
$ByLINE_New .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU_ttl_new,2).'</td><td></td>';

# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_New .= '<td>'.$Target_Qty_ttl_new.'</td>';
    $ByLINE_New .= '<td class="list_txt_bg_daily">'.number_format($Target_SU_ttl_new,2).'</td>';
}
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_New .= '<td colspan="2">&nbsp;</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE_New .= '<td>'.$A_TT_QTY_new[$key].'</td>';
    }
}
$ByLINE_New .= '<td><div align="right">'.$A_TTL_QTY_new.' <font color="red">('.( !empty($A_TTL_F_QTY_new)? $A_TTL_F_QTY_new:0).')</font></div></td>';
$ByLINE_New .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($A_TTL_SU_new,2).'</div></td>';
$ByLINE_New .= '<td><div align="right">'.number_format($A_TTL_SU_new/$Capacity_SU_ttl_new*100).' %</div></td></tr>';

//$ByLINE_New .= '</table>';

//$ByLINE_New .= '<p>';

/*2014-11-15 27組已經解散*/
$ByLINE_27 .= '<tr class="list_txt">';
$ByLINE_27 .= '<td colspan="2">Sub Total</td>';
$ByLINE_27 .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU_ttl_27,2).'</td><td></td>';

# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_27 .= '<td>'.$Target_Qty_ttl_27.'</td>';
    $ByLINE_27 .= '<td class="list_txt_bg_daily">'.number_format($Target_SU_ttl_27,2).'</td>';
}
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE_27 .= '<td colspan="2">&nbsp;</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE_27 .= '<td>'.$A_TT_QTY_27[$key].'</td>';
    }
}
$ByLINE_27 .= '<td><div align="right">'.$A_TTL_QTY_27.' <font color="red">('.( !empty($A_TTL_F_QTY_27)? $A_TTL_F_QTY_27:0).')</font></div></td>';
$ByLINE_27 .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($A_TTL_SU_27,2).'</div></td>';
$ByLINE_27 .= '<td><div align="right">'.number_format($A_TTL_SU_27/$Capacity_SU_ttl_27*100).' %</div></td></tr>';
//$ByLINE_27 .= $ByLINE_normal_summary;
//$ByLINE_27 .= $ByLINE_total_summary;
//$ByLINE_27 .= '</table>';

//$ByLINE_27 .= '<p>';




$ByLINE .= '<tr class="list_txt">';
$ByLINE .= '<td colspan="2">Sub Total</td>';
$ByLINE .= '<td class="list_txt_bg_daily" title="'.$scm[$line][$order_num]['qty_str'].'">'.number_format($Capacity_SU_ttl_over3,2).'</td><td></td>';

# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE .= '<td>'.$Target_Qty_ttl_over3.'</td>';
    $ByLINE .= '<td class="list_txt_bg_daily">'.number_format($Target_SU_ttl_over3,2).'</td>';
}
# 是否顯示詳細資料
if( $detail == 'true' ){
    $ByLINE .= '<td colspan="2">&nbsp;</td>';
    foreach($TT_VIEW as $key => $val ){
        $ByLINE .= '<td>'.$A_TT_QTY_over3[$key].'</td>';
    }
}
$ByLINE .= '<td><div align="right">'.$A_TTL_QTY_over3.' <font color="red">('.( !empty($A_TTL_F_QTY_over3)? $A_TTL_F_QTY_over3:0).')</font></div></td>';
$ByLINE .= '<td class="list_txt_bg_daily"><div align="right">'.number_format($A_TTL_SU_over3,2).'</div></td>';
$ByLINE .= '<td><div align="right">'.number_format($A_TTL_SU_over3/$Capacity_SU_ttl_over3*100).' %</div></td></tr>';

//$ByLINE .= '</table>';

//$ByLINE .= '<p>';

//總結部分####################################################ByLINE_qty  ByLINE_Target ByLINE_Plus





####################################################################################################################################################



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

echo '<table border="0" cellspacing="0" cellpadding="0" cols="0">'.$ByLINE_columns.$ByLINE.$ByLINE_New.$ByLINE_normal_summary.$ByLINE_27.$ByLINE_total_summary.'</table><p>';
//echo '<table border="0" cellspacing="0" cellpadding="0" cols="0">'.$ByLINE_27.$ByLINE_total_summary.'</table><p>';

echo '<span class="note_bg">Full Capacity：[ ( Worker * 8 ) + ( OT Worker * ( OT Hour - 0.5 ) ) ] * 1.2</span><br>';
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




################################################################################################################
# 原始顯示 開始 MODE

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
                //$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'">'.$line.'</td>';
				$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
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
                //$ByLINE .= '<td class="m_p" id="monitor" line="'.$line.'">'.$line.'</td>';
				$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
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
                //$ByLINE .= '<td class="m_p" id="monitor" line="'.$line.'">'.$line.'</td>';
				$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
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
                //$ByLINE .= '<td class="m_p" id="monitor" line="'.$line.'">'.$line.'</td>';
				$ByLINE .= '<td class="m_p" title="'.$Reader.'" id="monitor" line="'.$line.'" ordernum="'.$order_num.'" thedate="'.$Date.'">'.$line.'</td>';
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



################################################################################################################
# 原始顯示 結束 MODE

if($list=='true'){
echo $ByLINE;
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
# :get_monitor
case "get_monitor":
check_authority($AUTH,"view");
//print_r($_GET);
/*
$connection_string = 'DRIVER={SQL Server};SERVER='.$GLOBALS['RFID_SERVER'].';DATABASE='.$GLOBALS['$WIPONE_DB']; 
$conn = odbc_connect($connection_string, $GLOBALS['RFID_USER'], $GLOBALS['RFID_PASSWD']);
if (!$conn)
{
	printf("<Br> %s disconnect<br>", $GLOBALS['RFID_SERVER']);
}
else
{
	$sql = "select emp.c_em_code,timezone.timename ";
	$sql .= "from NewWIPOne.dbo.lotsworkallocation assign ";
	$sql .= "left join NewWIPOne.dbo.lotssteps step on assign.lotsstepid = step.lotsstepid ";
	$sql .= "left join HR.dbo.t_employee emp on emp.c_em_id = assign.workerid ";
	$sql .= "left join NewWIPOne.dbo.distributiontimesheet timezone on timezone.id = assign.distributiontimesheetid ";
	$sql .= "left join HR.dbo.t_code code on code.c_co_code = assign.sectioncode ";
	$sql .= "where step.lotid='";
	$sql .= $_GET['PHP_order'];
	$sql .= "' and code.c_co_name='";
	$sql .= $_GET['PHP_line'];
	$sql .= "' and code.c_co_in='033' ";
	$sql .= "group by emp.c_em_code,timezone.timename ";
	$sql .= "order by emp.c_em_code,timezone.timename ";
	//echo $sql;
	$result = odbc_exec($conn, $sql);
	$onduty_cal = array();
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
          //print_r($row);
		  $onduty_cal[] = $row;
		  //print_r($_SESSION);
        }
	}
	else
	{
		echo '執行SQL失敗';
	}
	
	$sql = "select code.c_co_name,emp.c_em_code,timezone.timename,step.stepid,step.sam,step.sas ";
	$sql .= "from NewWIPOne.dbo.lotsworkallocation assign ";
	$sql .= "left join NewWIPOne.dbo.lotssteps step on assign.lotsstepid = step.lotsstepid ";
	$sql .= "left join HR.dbo.t_employee emp on emp.c_em_id = assign.workerid ";
	$sql .= "left join NewWIPOne.dbo.distributiontimesheet timezone on timezone.id = assign.distributiontimesheetid ";
	$sql .= "left join HR.dbo.t_code code on code.c_co_code = assign.sectioncode ";
	$sql .= "where step.lotid='";
	$sql .= $_GET['PHP_order'];
	$sql .= "' order by code.c_co_name,emp.c_em_code,timezone.timename,step.stepid";
	//echo $sql;
	$result = odbc_exec($conn, $sql);
	$assign = array();
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
          //print_r($row);
		  $assign[] = $row;
		  //print_r($_SESSION);
        }
	}
	else
	{
		echo '執行SQL失敗';
	}
	//print_r($assign);	
	$sql = "select wk.lotid,emp.c_em_code,wk.ReaderName,wk.stepid,sum(wk.packedqty) as qty,step.sam,step.sas ";
	$sql .= "from NewWIPOne.dbo.Workdone wk  ";
	$sql .= "left join HR.dbo.t_employee emp on emp.c_em_id = wk.workerid ";
	$sql .= "left join NewWIPOne.dbo.LotsSteps step on wk.lotid=step.lotid and wk.stepid=step.stepid ";
	$sql .= "where wk.lotid='";
	$sql .= $_GET['PHP_order'];
	$sql .= "' and Convert(varchar(100),wk.scantime,111)='";
	//$sql .= $_GET['PHP_date'];
	$sql .= str_replace("-","/",$_GET['PHP_date']);

	$sql .= "' and wk.ReaderName like '";
	$theline = $_GET['PHP_line'];
	if( $theline < 10) 
	{
		$theline = "0".$theline;
	}
	$sql .= $theline;
	$sql .= "_%' group by wk.lotid,emp.c_em_code,wk.ReaderName,wk.stepid,step.sam,step.sas ";
	$sql .= "order by wk.lotid,emp.c_em_code,wk.ReaderName,wk.stepid";
	$result = odbc_exec($conn, $sql);
	$scancard = array();
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
		  $scancard[] = $row;
        }
	}
	else
	{
		echo '執行SQL失敗';
	}
	//echo $sql."<br>";
	//print_r($scancard);	
}
//$scancard = array();
$emp_code='';
$sum_su=0;
$sum_sas=0;
$emp_sumsu = array();//工人su加總用
$i=0;
$cal_step=0;
foreach($scancard as $key => $val ){

	if($emp_code == $val['c_em_code'])
	{
		//同一工人
		$sum_su += $scancard[$key]['su']=number_format(($val['sas'] * $val['qty'])/3000,2);
		$sum_sas += $scancard[$key]['sas'];
		$cal_step++;
	}
	else
	{
		//不同工人
		if($key == 0)
		{
			$emp_code = $emp_sumsu[$i]['emp_id'] = $val['c_em_code'];
			$sum_su = $scancard[$key]['su']=number_format(($val['sas'] * $val['qty'])/3000,2);
			$sum_sas = $scancard[$key]['sas'];
			$cal_step++;
		}
		else
		{
			$emp_sumsu[$i]['su'] = $sum_su;
			$emp_sumsu[$i]['sas'] = $sum_sas;
			$emp_sumsu[$i]['step_num'] = $cal_step;
			$cal_step =0;
			$i++;
			$sum_su = $scancard[$key]['su']=number_format(($val['sas'] * $val['qty'])/3000,2);
			$sum_sas = $scancard[$key]['sas'];
			$emp_code = $emp_sumsu[$i]['emp_id'] = $val['c_em_code'];
			$cal_step++;
			
		}
	}
}
//print_r($emp_sumsu);
foreach($emp_sumsu as $key_empsu => $val_empsu ){
	$AM = 0;
	$PM = 0;
	$OT = 0;
	foreach($onduty_cal as $key_onduty => $val_onduty ){
		//echo $val_empsu['c_em_code']."<br>";
		//echo $val_onduty['c_em_code']."<br>";
		if($val_empsu['emp_id'] == $val_onduty['c_em_code'])
		{
		    //echo $val_onduty['timename'];
			//echo "<br>";
			switch($val_onduty['timename'])
			{
				case 'AM':
					$AM = 4;
				break;
				case 'PM':
					$PM = 4;
				break;
				case 'OT':
					$OT = 2;
				break;
			} 
		}
	}
	$emp_sumsu[$key_empsu]['onduty'] = $AM+$PM+$OT;
	$emp_sumsu[$key_empsu]['performance'] = number_format(($val_empsu['su']/(($AM+$PM+$OT)*1.2))*100,0);
}

$op['emp_summary'] = $emp_sumsu;
$op['scancard'] = $scancard;
$op['assign_step'] = $assign;
*/
//print_r($scancard);

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

$connection_string = 'DRIVER={SQL Server};SERVER='.$GLOBALS['RFID_SERVER'].';DATABASE='.$GLOBALS['$WIPONE_DB']; 
$conn = odbc_connect($connection_string, $GLOBALS['RFID_USER'], $GLOBALS['RFID_PASSWD']);
if (!$conn)
{
	printf("<Br> %s disconnect<br>", $GLOBALS['RFID_SERVER']);
}
else
{
	$sql = "select distinct c_em_id,c_em_code ";
	$sql .= "from HR.dbo.t_employee emp ";
	$sql .= "left join HR.dbo.t_code code on code.c_co_code = emp.c_em_append3 ";
	$sql .= "where emp.c_em_status = 0 and c_co_in='033' and c_co_name='";
	$sql .= $_GET['PHP_line'];
	$sql .= "'";
	//echo $sql;
	$result = odbc_exec($conn, $sql);
	$emp_table = array();
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
          //print_r($row);
		  $emp_table[] = $row;
		  //print_r($_SESSION);
        }
	}
	else
	{
		echo '執行SQL失敗';
	}	
	//ㄧ組可能會有多筆訂單處理
	/* select distinct lotid from workdone
	where readername like '04_%' and convert(varchar(100),scantime,111)='str_replace("-","/",$_GET['PHP_date'])' */
	//從workdone的刷卡紀錄來判斷該組當天實際生產的訂單號
	$sql = "select distinct lotid ";
	$sql .= "from NewWIPOne.dbo.workdone wk ";
	$sql .= "where wk.readername like '";
	$theline = $_GET['PHP_line'];
	if( $theline < 10) 
	{
		$theline = "0".$theline;
	}
	$sql .= $theline;
	$sql .= "_%' and convert(varchar(100),wk.scantime,111)='";
	$sql .= str_replace("-","/",$_GET['PHP_date']);
	$sql .= "'";
	$result = odbc_exec($conn, $sql);
	$order_table = array();
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
          //print_r($row);
		  $order_table[] = $row;
		  //print_r($_SESSION);
        }
	}
	else
	{
		echo '執行SQL失敗';
	}
	//print_r($order_table);
	//exit;
	//echo count($order_table);
	//派工資料
	$sql = "select step.lotid,code.c_co_name,emp.c_em_code,timezone.timename,step.stepid,step.sam,step.sas ";
	$sql .= "from NewWIPOne.dbo.lotsworkallocation assign ";
	$sql .= "left join NewWIPOne.dbo.lotssteps step on assign.lotsstepid = step.lotsstepid ";
	$sql .= "left join HR.dbo.t_employee emp on emp.c_em_id = assign.workerid ";
	$sql .= "left join NewWIPOne.dbo.distributiontimesheet timezone on timezone.id = assign.distributiontimesheetid ";
	$sql .= "left join HR.dbo.t_code code on code.c_co_code = assign.sectioncode ";
	$sql .= "where step.lotid in ('";
	//要判斷最後一筆
	for($a=0;$a<count($order_table);$a++)
	{
		//$sql .= $_GET['PHP_order'];
		if($a == (count($order_table)-1))
		{
			$sql .= $order_table[$a]['lotid'];
		}
		else
		{
			$sql .= $order_table[$a]['lotid'];
			$sql .= "','"; 
		}
	}
	$sql .= "') ";
	//未來派工表有日期，就加上日期
	$sql .= "order by step.lotid,code.c_co_name,emp.c_em_code,timezone.timename,step.stepid";
	//echo $sql;
	$result = odbc_exec($conn, $sql);
	$assign_table = array();
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
          //print_r($row);
		  $assign_table[] = $row;
		  //print_r($_SESSION);
        }
	}
	else
	{
		echo '執行SQL失敗';
	}
}
//print_r($assign_table);
$AM=0;
$PM=0;
$OT=0;
foreach($emp_table as $key_emp => $val_emp )
{
	foreach($assign_table as $key_assign => $val_assign)
	{
		if($val_emp['c_em_code'] == $val_assign['c_em_code'])
		{
			switch($val_assign['timename'])
			{
				case "AM":
					$AM=4;
				break;
				case "PM":
					$PM=4;
				break;
				case "OT":
					$OT=2;
				break;
			}
		}
	}
	$emp_table[$key_emp]['work_hour'] = $AM+$PM+$OT;
	$emp_table[$key_emp]['today_su'] = ($AM+$PM+$OT)*60*60/3000;
	$AM=0;
	$PM=0;
	$OT=0;
}
//print_r($emp_table);

foreach($daily as $key_daily => $val_daily ){
	foreach($emp_table as $key_emp => $val_emp ){
		if($val_daily['WorkerId'] == $val_emp['c_em_id'])
		{
			$daily[$key_daily]['WorkerId'] = $val_emp['c_em_code'];
		}
	}
}

 
//print_r($daily);

/* $connection_string = 'DRIVER={SQL Server};SERVER=192.168.1.162;DATABASE=NewWIPOne'; 
$ms_connect = mssql_connect($connection_string , "morial", "morial");
if(!$ms_connect)
{
	die('Something wrong while connecting to MSSQL');
	//echo $ms_connect;
	//echo "true";
}
else
{
	echo "true";
} */


$IE = $Target = array();
foreach($capacity as $IE_K => $IE_V ){
    $IE[$IE_V['LotId']] = ( $IE_V['ie2'] > 0 ) ? $IE_V['ie2'] : $IE_V['ie1'];
    $Target[$IE_V['LotId']] = $IE_V['Qty'];
}

// print_r($daily);


$TT_VIEW = array(
'T1' => '07:30<br>08:30' ,
'T2' => '08:30<br>09:30' ,
'T3' => '09:30<br>10:30' ,
'T4' => '10:30<br>12:30' ,
'T5' => '12:30<br>13:30' ,
'T6' => '13:30<br>14:30' ,
'T7' => '14:30<br>15:30' ,
'T8' => '15:30<br>16:30' ,
'T9' => '16:30<br>24:00' 
);

$TT_A1 = array('T1'=>'00:00','T2'=>'08:30','T3'=>'09:30','T4'=>'10:30','T5'=>'12:30','T6'=>'13:30','T7'=>'14:30','T8'=>'15:30','T9'=>'16:30');
$TT_A2 = array('T1'=>'08:30','T2'=>'09:30','T3'=>'10:30','T4'=>'12:30','T5'=>'13:30','T6'=>'14:30','T7'=>'15:30','T8'=>'16:30','T9'=>'24:59');
// echo "\r\n";

$tt_Sas = 0 ;
$READER_ID = array();
$READER = $WORKDONE_ORD = $WORKDONE_LINE = $READERDDDD = array();
$IE_QTY = array();
$StepId_arr = array();
$StepId_str = '';
//print_r($daily);
foreach($daily as $row){
	
    $SONumber = $row['SONumber'];
    $ScanTime = $row['ScanTime'];
    $PackedQty = $row['PackedQty'];
    $FailedQty = $row['FailedQty'];
    $ReaderName = $row['ReaderName'];
    $WorkerId = $row['WorkerId'];
    $StepId = $row['StepId'];
    $StepPrice = $row['StepPrice'];
    $StepSam = $row['StepSam'];
    $Sas = $row['Sas'];
    
    $W_StepPrice = $PackedQty * $StepPrice;
    // $W_StepSam = $PackedQty * $StepSam;
    $W_Sas = $PackedQty * $Sas;

    $Time_arr = array();
    foreach($TT_A1 as $Time => $v){

        if( $StepId == 900){
            $WORKDONE_ORD[$SONumber][$Time] += 0;
            $WORKDONE_ORD[$SONumber][$Time.'_F'] += 0;
            if( substr($ScanTime,11,8) >= $TT_A1[$Time] && substr($ScanTime,11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time] += $PackedQty; }
            if( substr($ScanTime,11,8) >= $TT_A1[$Time] && substr($ScanTime,11,8) < $TT_A2[$Time] ) {  $WORKDONE_ORD[$SONumber][$Time.'_F'] += $FailedQty; }

            $WORKDONE_LINE[$Time] += 0;
            // $WORKDONE_LINE[$Time.'_F'] += 0;
            if( substr($ScanTime,11,8) >= $TT_A1[$Time] && substr($ScanTime,11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Time] += $PackedQty; }
            if( substr($ScanTime,11,8) >= $TT_A1[$Time] && substr($ScanTime,11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$Time.'_F'] += $FailedQty; }
            if( substr($ScanTime,11,8) >= $TT_A1[$Time] && substr($ScanTime,11,8) < $TT_A2[$Time] ) {  $WORKDONE_LINE[$SONumber] += $PackedQty; }
        } else {
            if( substr($ScanTime,11,8) >= $TT_A1[$Time] && substr($ScanTime,11,8) < $TT_A2[$Time] ) {  
                // $READER[$SONumber][$ReaderName][$WorkerId]['ie'] = [$StepId][$Time] += $PackedQty; 
                // echo $Time.'<br>';
                $Time_arr[$Time] = $PackedQty;
                // $READER[$SONumber][$ReaderName][$WorkerId][$StepId][$Time] += $PackedQty;
                
                # Array WorkerId
                if(!in_array($WorkerId,$READER[$SONumber][$ReaderName]['WorkerId']))
                $READER[$ReaderName][$SONumber]['WorkerId'][$WorkerId] = $WorkerId;
                # ReaderName
                $READER[$ReaderName][$SONumber]['ReaderName'] = $ReaderName;
                # SONumber
                $READER[$ReaderName]['SONumber'][$SONumber] = $SONumber;
                # Array StepPrice
                $READER[$ReaderName][$SONumber]['StepPrice'][$StepId] = $StepPrice;
                # Array Sas
                $READER[$ReaderName][$SONumber]['Sas'][$StepId] = $Sas;
                # Array WK_PackedQty
                $READER[$ReaderName][$SONumber][$WorkerId]['WK_PackedQty'][$StepId] += $PackedQty;
                # Array WK_StepPrice
                $READER[$ReaderName][$SONumber][$WorkerId]['WK_StepPrice'][$StepId] += $PackedQty * $StepPrice;
                # Array WK_Sas
                $READER[$ReaderName][$SONumber][$WorkerId]['WK_Sas'][$StepId] += $PackedQty * $Sas;
                # Array StepId
                $READER[$ReaderName][$SONumber][$WorkerId]['StepId'][$StepId] = $StepId;
                # Sum_StepPrice
                $READER[$ReaderName]['Sum_StepPrice'] += $PackedQty * $StepPrice;
                # Sum_Sas
                $READER[$ReaderName]['Sum_Sas'] += $PackedQty * $Sas;
                
                $IE_QTY[$WorkerId][$ReaderName][$StepId]['PackedQty'] += $PackedQty;
                $IE_QTY[$WorkerId][$ReaderName][$StepId]['FailedQty'] += $FailedQty;
                $IE_QTY[$WorkerId][$ReaderName][$StepId]['Sas'] = $Sas;
                $IE_QTY[$WorkerId][$ReaderName]['Sas'] = $Sas;
                
            } else {
                // echo $Time.'<br>';
                $Time_arr[$Time] = 0;
            }
        }
    }
	
}


// print_r($READER_ID);
// print_r($IE_QTY);
//print_r($READER);
// $READER[$SONumber][$ReaderName][$WorkerId]['StepId']

//$READER_ID_arr = $READER_ID;
$READER_ID_arr = $READER;
$READER_ID = array();
#$READER
#$READER_ID[$ReaderName][$WorkerId][$SONumber]['StepId'][$StepId] = $StepId;
//print_r($READER_ID_arr);
foreach($READER_ID_arr as $ReaderName => $R_V){
    $READER_ID_m = array();
    $READER_ID_m['ReaderName'] = $ReaderName;
    $READER_ID_m['Sum_su'] = number_format(($R_V['Sum_Sas']/3000),2);
	//$READER_ID_m['efficiency'] = number_format(($R_V['Sum_Sas']/3000),2)
    foreach($R_V['SONumber'] as $SONumber){
        $READER_ID_s = array();
        $READER_ID_s['SONumber'] = $SONumber;
		$tmp_emp_ttlsu = array();
		foreach($emp_table as $emp => $emp_value)
		{
			if($WorkerId = $emp_value['c_em_code'])
			{
				$READER_ID_s['today_ttlsu'] = $tmp_emp_ttlsu[$WorkerId]['today_ttlsu'] = $emp_value['today_su']; 
				//$tmp_emp_ttlsu
				
			}
		} 
        
        foreach($R_V[$SONumber]['WorkerId'] as $WorkerId){
		
			//print_r($R_V[$SONumber]);
			$worker_sumsu = 0;
			$worker_performance = 0;
			foreach($R_V[$SONumber][$WorkerId]['WK_PackedQty'] as $key_wk_qty => $val_wk_qty)
			{
				//echo $key_wk_qty."<br>";
				foreach($R_V[$SONumber][$WorkerId]['WK_Sas'] as $key_wk_sas => $val_wk_sas)
				{
					if($key_wk_qty = $key_wk_sas)
					{
						/* echo $val_wk_qty."x".$val_wk_sas."=".$val_wk_qty*$val_wk_sas;
						echo "<br>"; */
						$worker_sumsu += $val_wk_sas;
						
					}
				} 
				$worker_sumsu = number_format($worker_sumsu/3000,2,'','');
				//$worker_performance = $worker_sumsu
				//print_r($tmp_emp_ttlsu);
				foreach($tmp_emp_ttlsu as $key_tmp => $val_tmp)
				{
					if($key_tmp = $WorkerId)
					{
						$worker_performance = number_format($worker_sumsu/$val_tmp['today_ttlsu']*100,2,'',''); 
						/* $worker_performance = $worker_performance * 100; */
						
					}
				} 
				
			}
			$READER_ID_s['Worker_ttlsu'] = $worker_sumsu;
			$READER_ID_s['performance'] = $worker_performance; 
            $READER_ID_s['WorkerId'] = $WorkerId;
            $READER_ID_s['StepId'] = implode(',',$R_V[$SONumber][$WorkerId]['StepId']);
            $READER_ID_s['WK_PackedQty'] = implode(',',$R_V[$SONumber][$WorkerId]['WK_PackedQty']);
            $READER_ID_s['WK_Sas'] = implode(',',$R_V[$SONumber][$WorkerId]['WK_Sas']);
			
            // foreach($R_V[$SONumber][$WorkerId]['StepId'] as $StepId){
                // $READER_ID_s['WorkerId'] = $WorkerId;
            // }
			
		}
		//echo "<br>";
        
        $READER_ID_m['val'][] = $READER_ID_s;
    }
    
    
    $READER_IDS[] = $READER_ID_m;
}
//print_r($READER_IDS);

$READER_arr = $READER;
//print_r($READER);
$READER = array();
#$READER
foreach($READER_arr as $R_K => $R_V ){
	$READER_m = array();
	$val = array();
	foreach($R_V as $R_Ke => $R_Va ){
		//print_r($R_Va);
		foreach($R_Va as $R_Key => $R_Val ){
		
			$READER_m['SONumber'] = $R_Va['SONumber'];
			$READER_m['ReaderName'] = $R_Va['ReaderName'];
			$READER_m['WorkerId'] = $R_Va['WorkerId'];

			$WorkerId = $R_Va['WorkerId'];
			//print_r($R_Val);	
			if($WorkerId == $R_Key){
			$READER_m['qty'] = $R_Val['qty'];
			$READER_m['Sas'] = number_format($R_Va['Sas']);
			$READER_m['ttl'] = $R_Val['sum_qty']/count($R_Val['StepId']);
			$READER_m['su'] = $R_Val['su'];
			$READER_m['StepId'] = $R_Val['StepId'];
			}
		}
		//echo "<br>";
		if(!empty($R_Va['SONumber'])){
			// $SONumber = $R_Va['SONumber'];
			$val[] = $READER_m;
		}
	}
// echo $R_K.'<br>';
$READER[] = array('SONumber'=>$R_K,'val'=>$val);
}


//print_r($READER);
$W_DAILY_SUM = $W_DAILY = $w_daily = array();
$target_sum = 0;
$ttl = $ttl_su = 0;
foreach($capacity as $C_K => $C_V ){
    
    $ORD = $C_V['LotId'];
    $LINE = $C_V['SectionCode'];

    $w_daily['order'] = $ORD;

    foreach($TT_A1 as $Time => $T_V){
        // echo $ORD.'<br>';
        $w_daily[$Time] = $WORKDONE_ORD[$ORD][$Time];
    }
    $w_daily['ttl'] = $WORKDONE_LINE[$ORD];
    $w_daily['su'] = number_format($WORKDONE_LINE[$ORD]*$IE[$ORD],2);
    
    
    $ttl += $WORKDONE_LINE[$ORD];
    $ttl_su += $w_daily['su'];
    
    
    $w_daily['target'] = !empty($scm[$LINE][$ORD]['full_su'])?$scm[$LINE][$ORD]['full_su']:number_format($Target[$ORD]*$IE[$ORD],2);
    $w_daily['target_str'] = $scm[$LINE][$ORD]['qty_str'];
    $w_daily['eff'] = number_format(($w_daily['su']/$w_daily['target']*100),0);
    $target_sum += $w_daily['target'];
    $W_DAILY[] = $w_daily;
}

foreach($TT_A1 as $Time => $T_V){
    $W_DAILY_SUM[] = $WORKDONE_LINE[$Time];
}

$eff = number_format(($ttl_su/$target_sum)*100,0);
// print_r($daily);
// print_r($capacity);
// print_r($scm);
// print_r($READER);

$TT_A1 = array('07:30','08:30','09:30','10:30','12:30','13:30','14:30','15:30','16:30');
$TT_A2 = array('08:30','09:30','10:30','12:30','13:30','14:30','15:30','16:30','24:00');

$op['line'] = $_GET['PHP_line'];
$op['dates'] = $_SESSION['Date'];
$op['TT_A1'] = $TT_A1;
$op['TT_A2'] = $TT_A2;
// $op['CPCT_ORD'] = $CPCT_ORD;
$op['W_DAILY'] = $W_DAILY;
$op['W_DAILY_SUM'] = $W_DAILY_SUM;

$op['READER'] = $READER;
$op['READER_ID'] = $READER_IDS;

$op['target_sum'] = $target_sum;
$op['target_su_sum'] = number_format($target_su_sum,2);
$op['ttl'] = $ttl;
$op['ttl_su'] = number_format($ttl_su,2);
$op['eff'] = $eff;
//print_r($op['READER_ID']);
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