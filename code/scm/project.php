<?php
session_start();

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];

$TPL_PROJECT_MAIN = "project_main.html";

$op = array();
switch ($PHP_action) {
//=======================================================
case "main":
check_authority('060',"view");
page_display($op,'060', $TPL_PROJECT_MAIN);		    	    
break;

  
  
//=======================================================
case "B":
check_authority('060',"view");
// BOM 
$BOM = 1.03;

function num_fmt($num){
  $num = explode('.',$num);
  return $num[0];
}

function is_apv($ord_num){
  global $mysql;
  $sql = "
  SELECT `status`,`qty` 
  FROM `s_order` 
  WHERE `order_num` LIKE '".$ord_num."'
  ";

  $sql_query = $mysql->query($sql);
  $row = $mysql->fetch($sql_query);
  
  return $row;
}

echo
'
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>PROJECT</title>

<script language="javascript" src="./js/jquery.min.js"></script>
</head>
<body>
';



//setting
$FTY = 'LY';
$N_DAY = date("Y-m-d");
$D_DAY = increceDaysInDate(date("Y-m-d"),-30);



// 狀態顯示
$status = isset($_POST['status']) ? $_POST['status'] : (isset($_GET['status'])?$_GET['status']:'1');
$dfrt = isset($_POST['dfrt']) ? $_POST['dfrt'] : (isset($_GET['dfrt'])?$_GET['dfrt']:'0');
$MQTY = isset($_POST['MQTY']) ? $_POST['MQTY'] : (isset($_GET['MQTY'])?$_GET['MQTY']:'10');
$N_DAY = isset($_POST['N_DAY']) ? $_POST['N_DAY'] : (isset($_GET['N_DAY'])?$_GET['N_DAY']:date("Y-m-d"));



// line ( id => line )
$line = array();
$sql = "
SELECT * 
FROM `pdt_saw_line` 
WHERE `fty` LIKE '".$FTY."'
AND `del_mk` = '0'
ORDER BY `pdt_saw_line`.`line` ASC 
";
$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  $line[$row['id']] = $row['line'];
}



// order
$order = array();
$order_line = array();
$scd = array();
$sql = "
SELECT `qty`,`line_id`,`ord_num`,`status`,`rel_ets`,`rel_etf`,`pdt_qty` 
FROM `schedule` 
WHERE `ets` > '".$N_DAY."'
AND `fty` LIKE '".$FTY."'
AND `status` = '0'
ORDER BY `schedule`.`rel_ets` ASC 
";

$sql_query = $mysql->query($sql);
$t_ord = array(); // 暫存訂單號碼，判斷是否有重複訂單，合併顯示生產時間，同訂單生產最早時間 ets 最晚時間 etf
while ( $row = $mysql->fetch($sql_query) ) {
  if ( !empty($t_ord[$row['ord_num']]) ) {
    foreach ( $order as $key => $val ) {
      if ( $val['ord_num'] == $row['ord_num'] ) {
        $order[$key]['rel_etf'] = $row['rel_etf'];
        $order[$key]['qty'] = $order[$key]['qty'] + $row['qty'];
      }
    }
  } else {
    $t_ord[$row['ord_num']] = $row['ord_num'];
    $order[] = $row;
  }
}



// 依生產線組合訂單
foreach($line as $keys => $vals){
  foreach($order as $key => $val){
    if( $keys == $val['line_id'] ){
      $order_line[$keys][] = array(
      'qty'     => $val['qty'] ,
      'line_id' => $val['line_id'] ,
      'ord_num' => $val['ord_num'] ,
      'status'  => $val['status'] ,
      'rel_ets' => $val['rel_ets'] ,
      'rel_etf' => $val['rel_etf'] ,
      'pdt_qty' => $val['pdt_qty']);
    }
  }
}
// print_r($order_line);
// exit;

// 排列生產線順序，列出訂單
foreach($line as $keys => $vals){
  if(!empty($order_line[$keys])){
    foreach($order_line[$keys] as $key => $val){
      if( $val['status'] == '2' ){
        $scd[$keys] = array();
      }
      $scd[$keys][] = array(
      'qty'     => $val['qty'] ,
      'ord_num' => $val['ord_num'] ,
      'rel_ets' => $val['rel_ets'] ,
      'rel_etf' => $val['rel_etf'] ,
      'pdt_qty' => $val['pdt_qty']);
    }
  }
}



// wi
$wi = array();

$sql = "
SELECT `id`,`wi_num`,`etd`,`bcfm_date`,`smpl_id` 
FROM `wi` 
WHERE `etd`  > '".$D_DAY."'
";

$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  $wi[$row['wi_num']] = array( 'id' => $row['id'] , 'wi_num' => $row['wi_num'] , 'etd' => $row['etd'] , 'bcfm_date' => $row['bcfm_date'] , 'smpl_id' => $row['smpl_id'] );
}



$D_DAY = increceDaysInDate(date("Y-m-d"),-210);

//bom_lots 
$bom_lots = array();
$sql = "
SELECT `id`,`wi_id`,`lots_used_id`,`qty`,`color` 
FROM `bom_lots` 
WHERE `k_date` > '".$D_DAY."'
";
$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  $bom_lots[] = array( 'id' => $row['id'] , 'wi_id' => $row['wi_id'] , 'lots_used_id' => $row['lots_used_id'] , 'qty' => $row['qty'] , 'color' => $row['color'] );
}



//lots_use 把主料全抓出來
$lots_use = array();
$sql = "
SELECT * 
FROM `lots_use` 
";

// WHERE `add_date` > '".$D_DAY."'
$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  $lots_use[$row['id']] = $row;
}




// stk_ord_link 把主料全抓出來
$stk_ord_link = array();
$sql = "
SELECT * 
FROM `stk_ord_link` 
";
// WHERE `add_date` > '".$D_DAY."'
$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  if($row['ord_org'])
  $stk_ord_link[][$row['ord_org']] = $row['ord_new'];
}



$html_str =
'
<style type="text/css">@import url(./js/calendar/skins/aqua/theme.css);</style>
<script type="text/javascript" src="./js/calendar/calendar.js"></script>
<script type="text/javascript" src="./js/calendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="./js/calendar/calendar-setup.js"></script>
<script language="javascript" src="./js/open_detial.js"></script>
<script language="javascript">

function Switch(obj){
  if ( $("tr[id^="+obj+"]").css("display") == "block" ){
    $("span[id^="+obj+"]").html("Fabric Detail OPEN");
    $("tr[id^="+obj+"]").css("display","none");
  } else {
    $("span[id^="+obj+"]").html("Fabric Detail CLOSE");
    $("tr[id^=A]").css("display","none");
    $("tr[id^=B]").css("display","none");
    $("tr[id^="+obj+"]").css("display","block");
  }
}

function nwin_ord(ord){
  var url ="./schedule.php?PHP_action=sch_ord_view&PHP_ord_num="+ord;
  var nm = "nwin_ord";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

function monitor(line,date){
  var url ="./monitor.php?PHP_action=saw_line_pic&PHP_line="+line+"&PHP_date="+date+"&PHP_fty=LY";
  var nm = "monitor";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

function bom(bom){
  var url ="./bom.php?PHP_action=bom_view&PHP_ex_order=1&PHP_id="+bom;
  var nm = "bom";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

function lots(lots){
  var url ="./index2.php?PHP_action=lots_view&PHP_code="+lots+"&SCH_lots_code=&SCH_lots_name=&SCH_comp=&SCH_cat1=&SCH_cat2=&SCH_mile=&SCH_cons=";
  var nm = "lots";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

function marker(ord){
  var url ="./marker.php?PHP_action=marker_ord_add&PHP_order_num="+ord;
  var nm = "marker";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

function order(ord_num){
  var url ="./index2.php?PHP_action=order_view&PHP_ord_num="+ord_num;
  var nm = "order";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

Calendar.setup(
  {
    inputField  : "N_DAY",         // ID of the input field
    button			:	"f_trigger_c",
    ifFormat    : "%Y-%m-%d"    // the date format
  }
);

function total_qty(ord){
	var sum = 0;
  var obm = $("input[id^=o"+ord+"]");
	for (var i=0;i<obm.length;i++)
		if (obm[i].type == "text")
      if ( parseInt(obm[i].value) > 0 )
        sum += parseInt(obm[i].value);
	$("input[id^="+ord+"]").val(sum);
}
</script>
<br><form></form>
';


// scd.ord_num => bom_lots.wi_id
$mod = array();


if( $status == '1' ){
  unset($scd);
  $scd[0] = $order;
}


foreach($scd as $keys => $vals){
  foreach($vals as $key => $val){
    // 狀態顯示
    if( $status == '1' ){
      $keys = $val['line_id'];
    }  
    $html_strs = '';
    $html_strm = $html_strm1 = $html_strm2 = $html_strm3 = '';
    $ord_qty = $pcs_str = '';
    $nbom = 0;
    $differents = 0;
    $runone = 0;
    // 有 bom 顯示
    if( !empty($wi[$val['ord_num']]) ){
$html_strm1 .=
'
  <tr>
    <td colspan="6" bgcolor="#000000">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="font-weight: bold; font-style:oblique; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
          <td align="left"><span style="font-size:16px;cursor:pointer;color:#FFFF00;" onclick="nwin_ord(\''.$val['ord_num'].'\')">'.$val['ord_num'].'</span> &nbsp; (LINE:<span style="cursor:pointer;color:#ff0000;" onclick="monitor(\''.$line[$keys].'\',\''.$N_DAY.'\')">'.$line[$keys].'</span>) &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  <span id="'.$val['ord_num'].'" onclick="Switch(\''.$val['ord_num'].'\')" style="cursor:pointer;color:#33FF00;">Fabric Detail OPEN</span></td>
          <td align="right">'.$val['rel_ets'].' ~ '.$val['rel_etf'].'</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr style="font-weight: bold; font-style:oblique; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000;">
    <td colspan="6" bgcolor="#FFFFFF">
      <table border="0" cellpadding="3" cellspacing="1">
        <tr>
          <td>'._pic_url('big','100',3,'picture',$val['ord_num'],'jpg','','').'</td>
          <td>
            <table border="0" cellpadding="0" cellspacing="0">
';
      
      foreach( $bom_lots as $ke =>  $va )
      {
        if( $va['wi_id'] == $wi[$val['ord_num']]['id'] )
        {
          if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) )
            $color = "#FCE8FF";
          else
            $color = "#FFFFFF";

            if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) && $runone == 0){
            // 將主料編號放這，因為進入回圈狀態所以增加 $runone
            $runone ++;
$html_strm1 .=
'
              <tr>
                <td>
                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td align="left">
                        <table border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
                          <tr style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
                            <td bgcolor="#003399">BOM</td>
                            <td bgcolor="#003399">ETD</td>
                            <td bgcolor="#003399">Q\'TY</td>
                            <td bgcolor="#003399">Fabric #</td>
                          </tr>
                          <tr style="font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                            <td bgcolor="#FFFFFF"><span style="cursor:pointer;" onclick="bom(\''.$wi[$val['ord_num']]['id'].'\')">'.substr($wi[$val['ord_num']]['bcfm_date'],0,10).'</span></td>
                            <td bgcolor="#FFFFFF">'.$wi[$val['ord_num']]['etd'].'</td>
                            <td bgcolor="#FFFFFF">'.number_format(num_fmt($val['qty'])).'</td>
                            <td bgcolor="#FFFFFF"><span style="cursor:pointer;" onclick="lots(\''.$lots_use[$va['lots_used_id']]['lots_code'].'\')">'.$lots_use[$va['lots_used_id']]['lots_code'].'</span></td>
                          </tr>
                        </table>
                      </td>
                      <td>&nbsp;</td>
                      <td align="right" >
                      </td>
                    </tr>                    
                  </table>
                </td>
              </tr>
              <tr>
                <td height="6"></td>
              </tr>
';
}
            
$sql = "
SELECT rcv_qty, po_qty AS qty, po_unit AS unit, ap_det.eta, prc_unit, (
ap_det.prics * ap_det.po_qty
) AS po_amount, ap.ap_num, prics, amount, ap_det.ship_way, ap_det.ship_date, ap_det.ship_eta, ap_det.po_spare AS det_id, ap_det.ship_rmk
FROM ap_det, ap
WHERE ap_det.ap_num = ap.ap_num
AND ap_det.mat_cat = 'l'
AND bom_id = '".$va['id']."'
";

$sql_query = $mysql->query($sql);
$row = $mysql->fetch($sql_query);

if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) ){
  $mks = $averages = $clothes = $estimate = $different = $pc = $different2 = $pc2 = '';
  $sql2 = "SELECT * FROM `marker_ord` WHERE ord_id='".$wi[$val['ord_num']]['smpl_id']."' and `fab_type` = '1' and `combo` = '1'";
  $sql_query2 = $mysql->query($sql2);
  while ($rows = $mysql->fetch($sql_query2)) {
    $mks[] = $rows;
  }
  
  // print_r($mks);
  if(!empty($mks)){
    foreach($mks as $mkeys => $mvals){
      if(is_array($mvals)){
        foreach($mvals as $mkey => $mval){
          if($mkey === 'assortment'){
            if($mks[$mkeys]['length'] and !empty($mval) ){
              $asmts = $Marker->average($mval,$mks[$mkeys]['length']);
              $mks[$mkeys]['averages'] = $asmts['averages'];
              $clothes += $mks[$mkeys]['clothes'] = $asmts['clothes'];
              $estimate += $mks[$mkeys]['estimate'] = $asmts['estimate'];
            }
          }
        }
      }
    }
  }
  

  if($clothes and $estimate){
    $averages = $clothes / $estimate;
  }
  
  // Marker 計算
  $different = ( $row['rcv_qty'] != 0 )? $row['rcv_qty'] - array_sum(explode(',',$va['qty'])) : $row['qty'] - array_sum(explode(',',$va['qty'])) ;
  $pc = ( $averages != 0 )? $different / $averages : $different / $lots_use[$va['lots_used_id']]['est_1'] ;
  $pcs = ( $averages != 0 )? ( $different - $MQTY ) / $averages : ( $different - $MQTY ) / $lots_use[$va['lots_used_id']]['est_1'] ;
  
  $different2 = ( $row['rcv_qty'] != 0 )? $row['rcv_qty'] - ( array_sum(explode(',',$va['qty']))*$BOM ) : $row['qty'] - ( array_sum(explode(',',$va['qty']))*$BOM )  ;
  $pc2 = ( $averages != 0 )? $different2 / $averages : $different2 / $lots_use[$va['lots_used_id']]['est_1'] ;
  $pcs2 = ( $averages != 0 )? ( $different2 - $MQTY ) / $averages : ( $different2 - $MQTY ) / $lots_use[$va['lots_used_id']]['est_1'] ;

  $t_marker = ( $averages != 0 )? $averages : $lots_use[$va['lots_used_id']]['est_1'];
}


      if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) )
      $html_strm1 .= '
              <tr>
                <td>
                  <table border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
                    <tr style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
                      <td bgcolor="#003399" width="130">Color</td>
                      <td bgcolor="#CC3300" width="40" title="BOM 預估 yy">YY</td>
                      <td bgcolor="#003399" width="60">BOM Q\'TY</td>
                      <td bgcolor="#003399" width="60">PO. Q\'TY</td>
                      <td bgcolor="#003399" width="60">Received</td>
                      <td bgcolor="#9900FF" width="40" title="江大哥排的碼克">Marker</td>
                      <td bgcolor="#003399" width="60">different</td>
                      <td bgcolor="#003399" width="40">pc</td>
                      <td bgcolor="#003399" width="40">安全量</td>
                    </tr>
                    <tr style="font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                      <td align="center" bgcolor="#FFFFFF">'.$va['color'].'</td>
                      <td align="center" bgcolor="#FFFFFF">'.$lots_use[$va['lots_used_id']]['est_1'].'</td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format(num_fmt(array_sum(explode(',',$va['qty'])))).'</td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format(num_fmt($row['qty'])).'</td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format(num_fmt($row['rcv_qty'])).'</td>
                      <td align="center" bgcolor="#FFFFFF"><span style="cursor:pointer;" onclick="marker(\''.$val['ord_num'].'\')">'.number_format($averages,2,'.',',').'</span></td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format(num_fmt($different)).'</td>
                      <td align="center" bgcolor="#FFFFFF" style="font-size:11px;font-weight: bold;color:#FF0000;">'.number_format(num_fmt($pc)).'</td>
                      <td align="center" bgcolor="#FFFFFF" style="font-size:11px;font-weight: bold;color:#006600;">'.number_format(num_fmt($pcs)).'</td>
                    </tr>
                    <tr style="font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                      <td align="center" bgcolor="#FFFFFF"></td>
                      <td align="center" bgcolor="#FFFFFF"></td>
                      <td align="center" bgcolor="#FFFFCC">'.number_format(num_fmt(array_sum(explode(',',$va['qty']))*$BOM)).'</td>
                      <td colspan="3" align="center" bgcolor="#FFFFCC">BOM+3%</td>
                      <td align="center" bgcolor="#FFFFCC">'.number_format(num_fmt($different2)).'</td>
                      <td align="center" bgcolor="#FFFFCC" style="font-size:11px;font-weight: bold;color:#FF0000;">'.number_format(num_fmt($pc2)).'</td>
                      <td align="center" bgcolor="#FFFFCC" style="font-size:11px;font-weight: bold;color:#006600;">'.number_format(num_fmt($pcs2)).'</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td height="6"></td>
              </tr>
      ';

if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) ){
if( $different2 - $MQTY > 0 )
  $differents += ( $different2 - $MQTY );

//組合主料顏色位置
$ord_qty .=
'
                      <tr style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#333333;">
                        <td style="text-align :right;">'.$va['color'].'</td>
                        <td><input id="o'.$wi[$val['ord_num']]['smpl_id'].'" type="text" onchange="total_qty(\''.$wi[$val['ord_num']]['smpl_id'].'\')" style="text-align :right;border-bottom-color:#FFFFFF;" value="'.number_format(num_fmt($pcs2)).'" size="3" /></td>
                      </tr>
';
}
      $html_strs .= 
      '
  <tr id="'.$val['ord_num'].'" style=" font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;display:none;">
    <td nowrap="nowrap" bgcolor="'.$color.'"><span style="cursor:pointer;" onclick="lots(\''.$lots_use[$va['lots_used_id']]['lots_code'].'\')">'.$lots_use[$va['lots_used_id']]['lots_code'].'</span></td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['lots_name'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$va['color'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['use_for'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['est_1'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['unit'].'</td>
  </tr>
      ';

      $html_strm2 .= '
      ';
      
      
        }
      }
      
$html_strm3 = '
                  <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr id="'.$val['ord_num'].'" style="font-weight: bold; font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;display:none;">
    <td align="center" bgcolor="#999999">Fabirc #</td>
    <td align="center" bgcolor="#999999">Fabric Name</td>
    <td align="center" bgcolor="#999999">Color</td>
    <td align="center" bgcolor="#999999">Detail</td>
    <td align="center" bgcolor="#999999" colspan="2">consump.</td>
  </tr>
';




$pcs_str = '
    <tr>
      <td colspan="6" bgcolor="#FFFFFF" align="right"  >
        <table border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
          <tr>';
          
// 判斷跟單訂單 - 及是否 apv
foreach($stk_ord_link as $ks => $vs){
  $stk_ord = !empty($vs[$val['ord_num']]) ? $vs[$val['ord_num']] : '';
  if ( !empty($vs[$val['ord_num']]) ){
    $apv = is_apv($vs[$val['ord_num']]);
    if( $apv['status'] == '4' ){
      $differents =  $differents - (($t_marker * $apv['qty']) * $BOM ) - $MQTY ;
$pcs_str .= '
            <td valign="bottom">
              <table border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
                <tr bgcolor="#336699" style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
                  <td bgcolor="#336699">生產線跟單核可</td>
                </tr>
                <tr bgcolor="#666666" style="text-align: center; height:40px; font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                  <td bgcolor="#FFFFFF"><span style="text-align: center;cursor:pointer;color:#ff0000;" onclick="order(\''.$stk_ord.'\')">'.$stk_ord.'</span><br>'.$apv['qty'].'pc</td>
                </tr>
              </table>
            </td>';
    } else {
$pcs_str .= '
            <td valign="bottom">
              <table border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
                <tr bgcolor="#666666" style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
                  <td bgcolor="#996600">生產線跟單</td>
                </tr>
                <tr bgcolor="#666666" style="text-align: center;height:40px; font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                  <td bgcolor="#FFFFFF"><span style="text-align: center;cursor:pointer;color:#333333;" onclick="order(\''.$stk_ord.'\')">'.$stk_ord.'</span><br>'.$apv['qty'].'pc</td>
                </tr>
              </table>
            </td>';
    }
  }
}

$html_str2 = '
  <tr style="font-weight: bold; font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;display:none;">
    <td align="center" bgcolor="#999999">Fabirc #</td>
    <td align="center" bgcolor="#999999">Fabric Name</td>
    <td align="center" bgcolor="#999999">Color</td>
    <td align="center" bgcolor="#999999">Detail</td>
    <td align="center" bgcolor="#999999" colspan="2">consump.</td>
  </tr>
';

$pcs_str .= '
            <td>
              <table border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
                <form method="post" action="order.php">
                <tr bgcolor="#666666" style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
                  <td bgcolor="#996600">新增訂單</td>
                </tr>
                <tr bgcolor="#666666" style="height:40px;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                  <td bgcolor="#FFFFFF">
                    <table width="100%" border="0" cellspacing="3" cellpadding="1">
                      <div id="'.$wi[$val['ord_num']]['smpl_id'].'">'.$ord_qty.'</div>
                      <tr style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#333333;">
                        <td style="text-align :right;" title="'.number_format(num_fmt( ( ($differents) / $t_marker ) )).'">淨裁數量：</td>
                        <td><input type="hidden" name="PHP_action" value="stock_ord_add" />
                        <input type="hidden" name="PHP_id" value="'.$wi[$val['ord_num']]['smpl_id'].'" />
                        <input readonly id="'.$wi[$val['ord_num']]['smpl_id'].'" style="text-align :center;border-top-width: 0px;border-left-width: 0px;border-right-width: 0px;border-bottom-color:#FFFFFF;" name="PHP_qty" value="'.number_format(num_fmt( ( ($differents) / $t_marker ) )).'" size="3" /></td>
                        <td><input type="submit" value="submit" /></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                </form>
              </table>
            <td>
          </tr>
        </table>
      </td>
    </tr>
';





    } else {
      $nbom = 1;
      $html_strm1 .= '
  <tr style="font-weight: bold; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#996600;" bgcolor="#363636">
    <td colspan="6" >'.$val['ord_num'].' &nbsp; (LINE:'.$line[$keys].') 無 BOM </td>
  </tr>
           ';
    }


$html_strm = $html_strm1.$html_strm2.$html_strm3;

// 剩布大於 100 碼
if( ( $different2 < $MQTY && $MQTY != '0' ) ||  ( $averages <= 0 && $dfrt == '1' ) || ($nbom ==1) ) 
  $html_strm = $html_strs = '';
else
  $html_str .= $html_strm.$html_strs.$html_str2.$pcs_str.'
    <tr>
      <td colspan="6" bgcolor="#FFFFFF" height="10"></td>
    </tr>
  ';

  }
}


// 狀態顯示
if( $status == '1' ) {
  $note = 'ETS 排序';
  $link = '<a href="?PHP_action=B&status=0&dfrt='.$dfrt.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">生產線排序</a>';
} else {
  $note = '生產線排序';
  $link = '<a href="?PHP_action=B&status=1&dfrt='.$dfrt.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">ETS 排序</a>';
}

if( $dfrt == '1' ) {
  $note .= ' / 大貨餘布 '.$MQTY.' 有含 Marker 用量';
  $link .= ' , <a href="?PHP_action=B&dfrt=0&status='.$status.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">大貨餘布 '.$MQTY.' 無含 Marker 用量</a>';
} else {
  $note .= ' / 大貨餘布 '.$MQTY.' 無含 Marker 用量';
  $link .= ' , <a href="?PHP_action=B&dfrt=1&status='.$status.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">大貨餘布 '.$MQTY.' 有含 Marker 用量</a>';
}

$link .= ' , 查詢時間：<input style="height:20" id="N_DAY" name="N_DAY" value="'.$N_DAY.'" size="7"> , 餘布：<input style="height:20" name="MQTY" value="'.$MQTY.'" size="1"> <input type="submit">';
$link .= '

';

echo
'<table border="0" align="center" cellpadding="3" cellspacing="0" bordercolor="#FFFFFF" bgcolor="#666666">
<form method="post" action="?PHP_action=B&dfrt='.$dfrt.'&status='.$status.'">';
echo '
  <tr style="height:36px; font-weight: bold; font-size:12px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000;" bgcolor="#FFFFFF">
    <td colspan="6" align="left">查詢時間：'.$N_DAY.'<br>狀態：'.$note.'<p>'.$link.'</td>
  </tr>
';
echo $html_str;
echo
'</form>
</table>';


// schedule

break;
	
//-------------------------------------------------------------------------

}   // end case ---------

?>
