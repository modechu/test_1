<?php

class PRODUCTION_ANALYSIS {



# :SQL LINK
function init($sql) {

	$this->msg = new MSG_HANDLE();

	if (!$sql) {
		$this->msg->add("Error ! Can't connect to database.");
		return false;
	}
	
	$this->sql = $sql;
	
	return true;
} # FUN END



function get_capacity_mm($fty,$YEAR) {

    $sql = $this->sql;

    $q_str = "SELECT * FROM capaci WHERE factory='".$fty."' AND year='".$YEAR."' AND c_type='capacity' ";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot access database, pls try later !");
        $this->msg->merge($sql->msg);
        return false;
    }
    
    $ym = array();
    while ($row = $sql->fetch($q_result)) {
        $ym['date'][$row['year']]['01'] = $row['m01'];
        $ym['date'][$row['year']]['02'] = $row['m02'];
        $ym['date'][$row['year']]['03'] = $row['m03'];
        $ym['date'][$row['year']]['04'] = $row['m04'];
        $ym['date'][$row['year']]['05'] = $row['m05'];
        $ym['date'][$row['year']]['06'] = $row['m06'];
        $ym['date'][$row['year']]['07'] = $row['m07'];
        $ym['date'][$row['year']]['08'] = $row['m08'];
        $ym['date'][$row['year']]['09'] = $row['m09'];
        $ym['date'][$row['year']]['10'] = $row['m10'];
        $ym['date'][$row['year']]['11'] = $row['m11'];
        $ym['date'][$row['year']]['12'] = $row['m12'];
    }
    
    $ym['year'] = $YEAR;
    
    return $ym;
} // end func



function get_line_output($fty,$year){

$sql = $this->sql;

// SELECT s_order.revise, pdtion.order_num, pdtion.ets, s_order.status, pdtion.etf, s_order.qty, s_order.su, pdtion.fty_su, pdtion.qty_shp, pdtion.qty_done
// FROM s_order, pdtion
// WHERE pdtion.factory = 'LY' AND pdtion.fty_su
// LIKE '%201403%' AND s_order.order_num = pdtion.order_num AND s_order.status > 0
// ORDER BY pdtion.order_num
$query  = " SELECT 
`saw_out_put`.`line_id` , 
`saw_out_put`.`saw_line` , 
`saw_out_put`.`out_date` , 
`saw_out_put`.`qty` , 
`saw_out_put`.`su` , 
`saw_out_put`.`workers` , 
`saw_out_put`.`work_qty` , 
`saw_out_put`.`ot_wk` , 
`saw_out_put`.`over_qty` , 
`saw_out_put`.`ot_hr` , 
`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`dept` 
FROM `saw_out_put` , `pdt_saw_line` , `s_order`
WHERE `saw_out_put`.`holiday` = '0' AND 
`pdt_saw_line`.`id` = `saw_out_put`.`line_id` AND 

`saw_out_put`.`ord_num` = `s_order`.`order_num` AND 

`saw_out_put`.`saw_fty` = '".$fty."' AND 
`saw_out_put`.`out_date` LIKE '".$year."%' AND 
`saw_out_put`.`qty` > 0 ";
// $query .= " ;";
// `pdt_saw_line`.`del_mk` = '0' AND 
// `pdt_saw_line`.`sc` = '0' AND 
// echo $query.'<br>';
$lien_id_to_saw_line = array();
$saw_out_put = array();
$line = array();
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
    
        $line[$row['saw_line']] = $row['saw_line'];
        $lien_id_to_saw_line[$row['line_id']] = $row['saw_line'];
        
        $month = substr($row['out_date'],5,2);
        
        $ie = $row['ie2'] > 0 ? $row['ie2'] : $row['ie1'] ;
        
        // $saw_out_put[$row['saw_line']][$month]['count'] = ( $saw_out_put[$row['saw_line']][$month]['count'] <
        $saw_out_put[$row['saw_line']][$month]['qty'] += $row['qty'];
        $saw_out_put[$row['saw_line']][$month]['su'] += $row['su'];
        $saw_out_put[$row['saw_line']][$month]['su_base'] += number_format($row['work_qty'] * $ie,'','','');
        $saw_out_put[$row['saw_line']][$month]['su_over'] += number_format($row['over_qty'] * $ie,'','','');
        // if($row['saw_line'] ==1){
            // echo $row['saw_line'] . ' ~ [' . $month . '] ~ ' . $row['su'] . ' = ' . (number_format($row['work_qty'] * $ie,'','','')+number_format($row['over_qty'] * $ie,'','','')) . ' = ' . number_format(($row['work_qty']+$row['over_qty']) * $ie,'','','') . ' = ' . number_format($row['work_qty'] * $ie,'','','') . ' + ' . number_format($row['over_qty'] * $ie,'','','') . ' <br> ';
            // echo '['.$month.']'.$saw_out_put[$row['saw_line']][$month]['su'] . ' = ' . $saw_out_put[$row['saw_line']][$month]['su_base'] . ' + '. $saw_out_put[$row['saw_line']][$month]['su_over'] . ' = ' . ($saw_out_put[$row['saw_line']][$month]['su_base']+$saw_out_put[$row['saw_line']][$month]['su_over']) . ' <br> ';
        // }
        if(only_sales_dept8($row['dept'])){
            # 工廠
            $saw_out_put[$row['saw_line']][$month]['su_f'] += $row['su'];
        } else {
            # 台北
            $saw_out_put[$row['saw_line']][$month]['su_t'] += $row['su'];
        }
        
        # 去除同天換單重複的資料
        $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['workers'] = ( $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['workers'] > $row['workers'] ) ? $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['workers'] : $row['workers'];
        $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['ot_wk'] = ( $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['ot_wk'] > $row['ot_wk'] ) ? $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['ot_wk'] : $row['ot_wk'];
        $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['ot_hr'] = ( $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['ot_hr'] > $row['ot_hr'] ) ? $saw_out_put['target'][$row['saw_line']][$month][$row['out_date']]['ot_hr'] : $row['ot_hr'];
         
        // $saw_out_put[$row['saw_line']][$month]['count'] += $row['ot_hr'];
    
    }
}

// print_r($saw_out_put);

$line = array_unique($line);
sort($line);
$saw_out_put['all_line'] = $line;

$query  = " SELECT 
`schedule`.`ord_num` ,
`schedule`.`su` ,
`schedule`.`rel_ets` ,
`schedule`.`rel_etf` ,
`s_order`.`su` ,
`pdtion`.`order_num` ,
`pdtion`.`fty_su`
FROM `s_order` , `schedule` , `pdtion`
WHERE `pdtion`.`factory` = '".$fty."' AND 
( `schedule`.`rel_ets` <= '".$year."-12-31' AND `schedule`.`rel_etf` >= '".$year."-01-01' ) AND 
`s_order`.`order_num` = `pdtion`.`order_num` AND 
`s_order`.`order_num` = `schedule`.`ord_num` AND 
`s_order`.`status` > 0 
GROUP BY `schedule`.`id`
";

$query  = " SELECT 
`schedule`.`id` ,
`schedule`.`line_id` ,
`schedule`.`ord_num` ,
`schedule`.`su` ,
`schedule`.`rel_ets` ,
`schedule`.`rel_etf` 
FROM `schedule` , `pdt_saw_line` 
WHERE `schedule`.`fty` = '".$fty."' AND 
`pdt_saw_line`.`id` = `schedule`.`line_id` AND 

( `schedule`.`rel_ets` <= '".$year."-12-31' AND `schedule`.`rel_etf` >= '".$year."-01-01' ) 
";
// `pdt_saw_line`.`del_mk` = '0' AND 
// `pdt_saw_line`.`sc` = '0' AND 
// echo $query.'<br>';
$line_su_month = array();
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
        $row['saw_line'] = $lien_id_to_saw_line[$row['line_id']];
        $row_array[] = $row;
        // $row['su']
        // $row['rel_ets']
        // $row['rel_etf']
        // $month = $row['line_id']
        // $line_su_month[$row['line_id']][]
    }
}

$saw_out_put['row_array'] = $row_array;

return $saw_out_put;

} # END



function get_line_output_month($fty,$year,$month){

$sql = $this->sql;

// SELECT s_order.revise, pdtion.order_num, pdtion.ets, s_order.status, pdtion.etf, s_order.qty, s_order.su, pdtion.fty_su, pdtion.qty_shp, pdtion.qty_done
// FROM s_order, pdtion
// WHERE pdtion.factory = 'LY' AND pdtion.fty_su
// LIKE '%201403%' AND s_order.order_num = pdtion.order_num AND s_order.status > 0
// ORDER BY pdtion.order_num
$query  = " SELECT 
`saw_out_put`.`line_id` , 
`saw_out_put`.`saw_line` , 
`saw_out_put`.`out_date` , 
`saw_out_put`.`qty` , 
`saw_out_put`.`su` , 
`saw_out_put`.`workers` , 
`saw_out_put`.`work_qty` , 
`saw_out_put`.`ot_wk` , 
`saw_out_put`.`over_qty` , 
`saw_out_put`.`ot_hr` , 
`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`dept` , 
`s_order`.`order_num`
FROM `saw_out_put` , `pdt_saw_line` , `s_order`
WHERE `saw_out_put`.`holiday` = '0' AND 
`pdt_saw_line`.`id` = `saw_out_put`.`line_id` AND 

`saw_out_put`.`ord_num` = `s_order`.`order_num` AND 

`saw_out_put`.`saw_fty` = '".$fty."' AND 
`saw_out_put`.`out_date` LIKE '".$year."-".$month."-%' AND 
`saw_out_put`.`qty` > 0 ";
// $query .= " ;";
// `pdt_saw_line`.`del_mk` = '0' AND 
// `pdt_saw_line`.`sc` = '0' AND 
// echo $query.'<br>';
$lien_id_to_saw_line = array();
$saw_out_put = array();
$line = array();
$order = array();
$ord = array();
$ord_arr = array();
$ord_str = '';
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
    
        $line[$row['saw_line']] = $row['saw_line'];
        $lien_id_to_saw_line[$row['line_id']] = $row['saw_line'];
        
        $month = substr($row['out_date'],5,2);
        
        $ie = $row['ie2'] > 0 ? $row['ie2'] : $row['ie1'] ;
        
        // $saw_out_put[$row['saw_line']]['count'] = ( $saw_out_put[$row['saw_line']]['count'] <
        $saw_out_put[$row['saw_line']]['qty'] += $row['qty'];
        $saw_out_put[$row['saw_line']]['su'] += $row['su'];
        $saw_out_put[$row['saw_line']]['su_base'] += number_format($row['work_qty'] * $ie,'','','');
        $saw_out_put[$row['saw_line']]['su_over'] += number_format($row['over_qty'] * $ie,'','','');
        
        # 組合訂單資訊
        if(!in_array($row['order_num'],$order[$row['saw_line']])){
            $order[$row['saw_line']][] = $row['order_num'];
            $ord_str[$row['saw_line']] .= $ord[$row['saw_line']] > 0 ? ' , ' . $row['order_num'] : $row['order_num'] ;
            $ord[$row['saw_line']]++;
            $ord_arr[$row['saw_line']]['ord_num'] = $order[$row['saw_line']];
            $ord_arr[$row['saw_line']]['ord_str'] = $ord_str[$row['saw_line']];
            $ord_arr[$row['saw_line']]['ord_count'] = $ord[$row['saw_line']];
        }

        if( only_sales_dept8($row['dept']) ){
            # 工廠
            $saw_out_put[$row['saw_line']]['su_f'] += $row['su'];
        } else {
            # 台北
            $saw_out_put[$row['saw_line']]['su_t'] += $row['su'];
        }
        
        # 去除同天換單重複的資料
        $saw_out_put['target'][$row['saw_line']][$row['out_date']]['workers'] = ( $saw_out_put['target'][$row['saw_line']][$row['out_date']]['workers'] > $row['workers'] ) ? $saw_out_put['target'][$row['saw_line']][$row['out_date']]['workers'] : $row['workers'];
        $saw_out_put['target'][$row['saw_line']][$row['out_date']]['ot_wk'] = ( $saw_out_put['target'][$row['saw_line']][$row['out_date']]['ot_wk'] > $row['ot_wk'] ) ? $saw_out_put['target'][$row['saw_line']][$row['out_date']]['ot_wk'] : $row['ot_wk'];
        $saw_out_put['target'][$row['saw_line']][$row['out_date']]['ot_hr'] = ( $saw_out_put['target'][$row['saw_line']][$row['out_date']]['ot_hr'] > $row['ot_hr'] ) ? $saw_out_put['target'][$row['saw_line']][$row['out_date']]['ot_hr'] : $row['ot_hr'];
    }
}
// print_r($ord_arr);
$line = array_unique($line);
sort($line);
$saw_out_put['all_line'] = $line;

$saw_out_put['ord_arr'] = $ord_arr;


// $query  = " SELECT 
// `schedule`.`ord_num` ,
// `schedule`.`su` ,
// `schedule`.`rel_ets` ,
// `schedule`.`rel_etf` ,
// `s_order`.`su` ,
// `pdtion`.`order_num` ,
// `pdtion`.`fty_su`
// FROM `s_order` , `schedule` , `pdtion`
// WHERE `pdtion`.`factory` = '".$fty."' AND 
// ( `schedule`.`rel_ets` <= '".$year."-".$month."-31' AND `schedule`.`rel_etf` >= '".$year."-".$month."-01' ) AND 
// `s_order`.`order_num` = `pdtion`.`order_num` AND 
// `s_order`.`order_num` = `schedule`.`ord_num` AND 
// `s_order`.`status` > 0 
// GROUP BY `schedule`.`id`
// ";

// $query  = " SELECT 
// `schedule`.`id` ,
// `schedule`.`line_id` ,
// `schedule`.`ord_num` ,
// `schedule`.`su` ,
// `schedule`.`rel_ets` ,
// `schedule`.`rel_etf` 
// FROM `schedule` , `pdt_saw_line` 
// WHERE `schedule`.`fty` = '".$fty."' AND 
// `pdt_saw_line`.`id` = `schedule`.`line_id` AND 

// ( `schedule`.`rel_ets` <= '".$year."-".$month."-31' AND `schedule`.`rel_etf` >= '".$year."-".$month."-01' ) 
// ";
// `pdt_saw_line`.`del_mk` = '0' AND 
// `pdt_saw_line`.`sc` = '0' AND 
// echo $query.'<br>';
// $line_su_month = array();
// if( $result = $sql->query($query) ){
    // while($row = $sql->fetch($result)){
        // $row['saw_line'] = $lien_id_to_saw_line[$row['line_id']];
        // $row_array[] = $row;
        // $row['su']
        // $row['rel_ets']
        // $row['rel_etf']
        // $month = $row['line_id']
        // $line_su_month[$row['line_id']][]
    // }
// }

// $saw_out_put['row_array'] = $row_array;

return $saw_out_put;

} # END




function get_line_output_week($fty,$date1,$date2,$week){

$sql = $this->sql;

$query  = " SELECT 
`saw_out_put`.`line_id` , 
`saw_out_put`.`saw_line` , 
`saw_out_put`.`out_date` , 
`saw_out_put`.`ord_num` , 
`saw_out_put`.`qty` , 
`saw_out_put`.`su` , 
`saw_out_put`.`workers` , 
`saw_out_put`.`ot_wk` , 
`saw_out_put`.`ot_hr`  , 
`s_order`.`factory` , 
`s_order`.`ie1` , 
`s_order`.`ie2` 
FROM `saw_out_put` , `pdt_saw_line` , `s_order` 
WHERE `saw_out_put`.`holiday` = '0' AND 
`pdt_saw_line`.`id` = `saw_out_put`.`line_id` AND 
`saw_out_put`.`ord_num` = `s_order`.`order_num` AND 
`saw_out_put`.`saw_fty` = '".$fty."' AND 
( `saw_out_put`.`out_date` BETWEEN '".$date1."' AND '".$date2."' ) AND 
`saw_out_put`.`qty` > 0 ";

// $query  = " SELECT 
// `saw_out_put`.`saw_line` , 
// `saw_out_put`.`out_date` , 
// `saw_out_put`.`workers` , 
// `saw_out_put`.`ot_wk` , 
// `saw_out_put`.`ot_hr` 
// FROM `saw_out_put` , `pdt_saw_line` 
// WHERE `saw_out_put`.`holiday` = '0' AND 
// `pdt_saw_line`.`id` = `saw_out_put`.`line_id` AND 

// `saw_out_put`.`saw_fty` = '".$fty."' AND 
// ( `saw_out_put`.`out_date` BETWEEN '".$date1."' AND '".$date2."' ) AND 
// `saw_out_put`.`qty` > 0 ";

// echo $query.'<br>';
$lien_id_to_saw_line = array();
$saw_out_put = array();
$out_put = array();



$line = array();
$full = array();
$out = array();
$out_put_arr = array();
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){

        $LINE = $row['saw_line'];
        $out_date = $row['out_date'];
        $ord_num = $row['ord_num'];
        $qty = $row['qty'];
        
        $line[$LINE] = $LINE;
        $su[$ord_num] = $row['ie2'] > 0 ? $row['ie2'] : $row['ie1'];
        
        // $out[$row['saw_line']][$row['out_date']] += $row['su'];
        $full[$row['saw_line']][$row['out_date']] = (($row['workers']*8)+($row['ot_wk']*$row['ot_hr']))*1.2;
        // $out_put_arr[$row['saw_line']][$row['ord_num']][$row['out_date']] = $row['su'];
    
        $out[$LINE][$out_date] += $qty*$su[$ord_num];
        $out_put_arr[$LINE][$ord_num][$out_date] += $qty*$su[$ord_num];


        
    }
}





$db_utf = getDBconnectUTF();

$q_str = "
SELECT 
`workdone`.`ScanTime`,`workdone`.`PackedQty`,`workdone`.`FailedQty`,`workdone`.`SONumber`,
`ticketreaders`.`Line` , `s_order`.`ie1`,`s_order`.`ie2` 
FROM `workdone` , `ticketreaders` , `s_order` 
WHERE `workdone`.`StepId` = 900 AND 
`workdone`.`ReaderId` = `ticketreaders`.`ReaderId` AND 
`workdone`.`SONumber` = `s_order`.`order_num` AND 
( `workdone`.`ScanTime` BETWEEN '".$date1."' AND '".$date2."' )
GROUP BY `workdone`.`WorkdoneId`
;";
// echo $q_str.'<br>';

// $line = array();
// $out = array();
// $out_put_arr = array();
// if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	// $_SESSION['MSG'][] = "Error! Can't access database!";
	// return false;    
// }
// .str_pad( ($id+1) , 6, 0, STR_PAD_LEFT);
// while ($row = mysql_fetch_array($q_result)) {
    // $LINE = str_pad( $row['Line'] , 2, 0, STR_PAD_LEFT);
    // $ScanTime = substr($row['ScanTime'],0,10);
    // $SONumber = $row['SONumber'];
    
    // $su[$SONumber] = $row['ie2'] > 0 ? $row['ie2'] : $row['ie1'] ;
    
    // $out[$LINE][$ScanTime] += $row['PackedQty']*$su[$SONumber];
    
    
    // if($LINE == '01')
    // echo $LINE.' ~ '.$ScanTime.' ~ '.$row['PackedQty'].' ~ '.$out[$LINE][$ScanTime].'<br>';
    // $out_put_arr[$LINE][$row['SONumber']][$ScanTime] += $row['PackedQty']*$su[$SONumber];
    // $line[$LINE] = $LINE;
// }


# 目標產量 `sectioncapacitysetting`.`Qty`
// $q_str = "SELECT 
// `sectioncapacitysetting`.`SectionCode`,`sectioncapacitysetting`.`LotId`,
// `sectioncapacitysetting`.`SettingDate`,`sectioncapacitysetting`.`Qty`,
// `s_order`.`ie1`,`s_order`.`ie2` 
// FROM `sectioncapacitysetting` , `s_order` 
// WHERE 
// `sectioncapacitysetting`.`LotId` = `s_order`.`order_num` AND 
// ( `sectioncapacitysetting`.`SettingDate` BETWEEN '".$date1."' AND '".$date2."' ) 
// ;";

// echo $q_str.'<br>';
// $su = array();
// if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	// $_SESSION['MSG'][] = "Error! Can't access database!";
	// return false;    
// }

#訂單IE
// while ($row = mysql_fetch_array($q_result)) {
    // $su[$row['LotId']] = $row['ie2'] > 0 ? $row['ie2'] : $row['ie1'] ;
// }




// print_r($week);
$line = array_unique($line);
sort($line);

$out_put_week = array();
foreach($line as $LINE){
    $num = 1;
    foreach($out_put_arr[$LINE] as $ord_num => $v ){
        // echo $LINE.' - '.$ord_num.' - ';
        
        $row = array();
        $row['line'] = $LINE;
        $row['ord_num'] = $ord_num;
        $row['count'] = count($out_put_arr[$LINE]);
        $row['num'] = $num;

        $pp = '';
        foreach($week as $WEEK ){
            $row['out'] += $out[$LINE][$WEEK];
            $row['full'] += $full[$LINE][$WEEK];
            // echo ' - '.$WEEK.' - '.$full[$LINE][$WEEK];
            // echo $LINE.' - '.$ord_num.' - '.$WEEK.' - '.$out_put_arr[$LINE][$ord_num][$WEEK];
            // echo ' - '.$WEEK.' - '.$out_put_arr[$LINE][$ord_num][$WEEK];
            
            $row['su'][] = $out_put_arr[$LINE][$ord_num][$WEEK] > 0 ? $out_put_arr[$LINE][$ord_num][$WEEK] : 0;
            // $pp .= ','.$out_put_arr[$LINE][$ord_num][$WEEK];
            // echo '<br>';
        }
        $out_put_week[] = $row;
        // echo $LINE.' - '.$row['out'].' - '.$row['full'].' - '.$pp.' - '.$su[$ord_num];
        // echo '<br>';
        $num++;
    }
}

// print_r($week);
// print_r($lines);

$saw_out_put['all_line'] = $line;
$saw_out_put['out_put'] = $out_put_week;

return $saw_out_put;

} # END


























function get_est_cost_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$query  = " SELECT 
            `s_order`.`order_num` , 
            `s_order`.`mat_useage` , 
            `s_order`.`mat_u_cost` , 
            `s_order`.`fusible` , 
            `s_order`.`interline` , 
            
            `s_order`.`acc_u_cost` , 
            
            `s_order`.`emb` , 
            `s_order`.`wash` , 
            `s_order`.`oth` , 
            
            `s_order`.`quota_fee` , 
            `s_order`.`comm_fee` , 
            
            `s_order`.`handling_fee` , 
            
            `s_order`.`qty` , 
            
            `s_order`.`ie1` , 
            `s_order`.`ie2` , 
            `s_order`.`cm` 
            FROM `s_order` 
            WHERE `s_order`.`status` >= '4' AND `s_order`.`factory` = '".$fty."' AND `s_order`.`etd` LIKE '".$year.'-'.$month."%'  ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $fabric = 0;
    $accessory = 0;
    $special = 0;
    $others = 0;
    $handling_fee = 0;
    $cm = 0;
    $unit_cost = 0;
    $sum = 0;
    
    $fty_cm = $this->get_cm(strtolower($fty.'-cm'));

    while($row = $sql->fetch($result)){
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        $others = $row['quota_fee'] + $row['comm_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( !empty($row['ie2']) ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];

        $qty = $row['qty'];
        
        $unit_cost = $fabric + $accessory + $special + $others + $handling_fee + $cm;
        
        $sum += $unit_cost * $qty;

    }
    
}

$rows['sum'] = $sum;

return $rows;

} # END



function get_est_cm_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$query  = " SELECT 
            `s_order`.`handling_fee` , 
            
            `s_order`.`qty` , 
            
            `s_order`.`ie1` , 
            `s_order`.`ie2` , 
            `s_order`.`cm` 
            FROM `s_order` 
            WHERE `s_order`.`status` >= '4' AND `s_order`.`factory` = '".$fty."' AND `s_order`.`etd` LIKE '".$year.'-'.$month."%'  ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $handling_fee = 0;
    $cm = 0;
    $unit_cost = 0;
    $sum = 0;
    
    $fty_cm = $this->get_cm(strtolower($fty.'-cm'));

    while($row = $sql->fetch($result)){

        $handling_fee = $row['handling_fee'];
        $cm = ( !empty($row['ie2']) ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];

        $qty = $row['qty'];
        
        $unit_cost = $handling_fee + $cm;
        
        $sum += $unit_cost * $qty;

    }
    
}

$rows['sum'] = $sum;

return $rows;

} # END



function get_est_other_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$query  = " SELECT 
            
            `s_order`.`emb` , 
            `s_order`.`wash` , 
            `s_order`.`oth` , 

            `s_order`.`qty` 
            
            FROM `s_order` 
            WHERE `s_order`.`status` >= '4' AND `s_order`.`factory` = '".$fty."' AND `s_order`.`etd` LIKE '".$year.'-'.$month."%'  ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $special = 0;

    $unit_cost = 0;
    $sum = 0;
    

    while($row = $sql->fetch($result)){

        $special = $row['emb'] + $row['wash'] + $row['oth'];
        
        $qty = $row['qty'];
        
        $unit_cost = $special;
        
        $sum += $unit_cost * $qty;

    }
    
}

$rows['sum'] = $sum;

return $rows;

} # END



function get_est_cost_etdss($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();


$query  = " SELECT 
            `s_order`.`order_num` , 
            `s_order`.`mat_useage` , 
            `s_order`.`mat_u_cost` , 
            `s_order`.`fusible` , 
            `s_order`.`interline` , 
            
            `s_order`.`acc_u_cost` , 
            
            `s_order`.`emb` , 
            `s_order`.`wash` , 
            `s_order`.`oth` , 
            
            `s_order`.`quota_fee` , 
            `s_order`.`comm_fee` , 
            
            `s_order`.`handling_fee` , 
            
            `s_order`.`uprice` , 
            `s_order`.`qty` , 
            
            `s_order`.`ie1` , 
            `s_order`.`ie2` , 
            `s_order`.`cm` 
            FROM `s_order` 
            WHERE `s_order`.`status` >= '4' AND `s_order`.`factory` = '".$fty."' AND `s_order`.`etd` LIKE '".$year.'-'.$month."%'  ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $fabric = 0;
    $accessory = 0;
    $special = 0;
    $others = 0;
    $handling_fee = 0;
    $cm = 0;
    $uprice = 0;
    $unit_cost = 0;
    $cost = 0;
    
    $fty_cm = $this->get_cm(strtolower($fty.'-cm'));

    while($row = $sql->fetch($result)){
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        $others = $row['quota_fee'] + $row['comm_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( !empty($row['ie2']) ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        
        $unit_cost = $fabric + $accessory + $special + $others + $handling_fee + $cm;
        
        $cost = $unit_cost * $qty;
    }
    $sum += $cost;
}


$rows['sum'] = $sum;

return $rows;

} # END



function get_est_ord_det($fty, $year, $month){

$sql = $this->sql;

$query  = " SELECT 
            `s_order`.`order_num` , 
            `s_order`.`mat_useage` , 
            `s_order`.`mat_u_cost` , 
            `s_order`.`fusible` , 
            `s_order`.`interline` , 
            
            `s_order`.`acc_u_cost` , 
            
            `s_order`.`emb` , 
            `s_order`.`wash` , 
            `s_order`.`oth` , 
            
            `s_order`.`quota_fee` , 
            `s_order`.`comm_fee` , 
            
            `s_order`.`handling_fee` , 
            
            `s_order`.`uprice` , 
            `s_order`.`qty` , 
            
            `s_order`.`ie1` , 
            `s_order`.`ie2` , 
            `s_order`.`cm` 
            FROM `s_order` 
            WHERE `s_order`.`status` >= '4' AND `s_order`.`factory` = '".$fty."' AND `s_order`.`etd` LIKE '".$year.'-'.$month."%'  ";
$query .= " ;";

if( $result = $sql->query($query) ){

    $ttl_qty = 0;
    $ttl_est_sales = 0;
    $ttl_est_cost = 0;
    $ttl_est_cm = 0;
    $ttl_est_other = 0;
    $ttl_est_gm = 0;
    $ttl_est_gm_rate = 0;
    
    $est_order_arr = array();
    
    $fty_cm = $this->get_cm(strtolower($fty.'-cm'));
    
    while($row = $sql->fetch($result)){
    
        $est_order['order_num'] = $row['order_num'];
        $est_order['uprice'] = $row['uprice'];
        $est_order['qty'] = $row['qty'];
        $ttl_qty += $row['qty'];
    
        $est_order['est_sales'] = $row['qty'] * $row['uprice'];
        $ttl_est_sales += $est_order['est_sales'];
        
        /*****/
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        $others = $row['quota_fee'] + $row['comm_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( !empty($row['ie2']) ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        $unit_cost = $fabric + $accessory + $special + $others + $handling_fee + $cm;
        /*****/
        
        $cost = $unit_cost * $qty;
        $est_order['est_cost'] = $cost;
        $ttl_est_cost += $cost;
        
        $cm = ( $handling_fee + $cm ) * $qty;
        $est_order['est_cm'] = $cm;
        $ttl_est_cm += $cm;
        
        $other = $special * $qty;
        $est_order['est_other'] = $other;
        $ttl_est_other += $other;
        
        $est_order['est_gm'] = $est_order['est_sales'] - $est_order['est_cost'];
        $ttl_est_gm += $est_order['est_gm'];
        
        $est_order['est_gm_rate'] = ( $est_order['est_gm'] / $est_order['est_sales'] ) * 100 ;
        $ttl_est_gm_rate += $est_order['est_gm_rate'];
        
        $est_order_arr[] = $est_order;
    }
    
}

$rows['est_order'] = $est_order_arr;

$rows['ttl_qty'] = $ttl_qty;
$rows['ttl_est_sales'] = $ttl_est_sales;
$rows['ttl_est_cost'] = $ttl_est_cost;

$rows['ttl_est_cm'] = $ttl_est_cm;
$rows['ttl_est_other'] = $ttl_est_other;

$rows['ttl_est_gm'] = $ttl_est_gm;
$rows['ttl_est_gm_rate'] = ( $ttl_est_gm / $ttl_est_sales ) * 100;

return $rows;
}




    
function get_cm($factory) {
    $sql = $this->sql;
    $row = array();

    $q_str = "SELECT `set_value` FROM `para_set` WHERE `set_name` = '".$factory."';";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
        
    while($row = $sql->fetch($q_result))
    {
        $field_val = $row[0];
    }

    return $field_val;
} // end func

















function get_shipping($fty,$year,$month){

$sql = $this->sql;

// $query  = " SELECT `shipping_doc_qty`.`ord_num` , `shipping_doc_qty`.`ship_fob` , 
            // sum(`shipping_doc_qty`.`ttl_qty`) as `qty` , 
            // sum(`shipping_doc_qty`.`amount`) as `amount` , 
            // sum(`shipping_doc_charge`.`charge`) as `charge` 
            // FROM `shipping_doc` , `shipping_doc_qty` LEFT JOIN `shipping_doc_charge` ON (`shipping_doc`.`id` = `shipping_doc_charge`.`s_id`)
            // WHERE `shipping_doc`.`id` = `shipping_doc_qty`.`s_id` AND `shipping_doc`.`factory` = '".$fty."' AND `shipping_doc`.`ship_date` LIKE '".$year.'-'.$month."%' 
            // GROUP BY `shipping_doc_qty`.`ord_num` ";
            
$query  = " SELECT `shipping_doc_qty`.`s_id` , `shipping_doc_qty`.`ord_num` , `shipping_doc_qty`.`ship_fob` , `shipping_doc_qty`.`ttl_qty` , `shipping_doc_qty`.`amount` , 
            sum(`shipping_doc_charge`.`charge`) as `charge` 
            FROM `shipping_doc` , `shipping_doc_qty` LEFT JOIN `shipping_doc_charge` ON (`shipping_doc`.`id` = `shipping_doc_charge`.`s_id`)
            WHERE `shipping_doc`.`id` = `shipping_doc_qty`.`s_id` AND `shipping_doc`.`factory` = '".$fty."' AND `shipping_doc`.`ship_date` LIKE '".$year.'-'.$month."%' 
            GROUP BY `shipping_doc_qty`.`id` ";
$query .= " ;";

// echo $query.'<br>';

if( $result = $sql->query($query) ){
    $sum = 0;
    
    $rows = array();
    $s_id = array();
    $s_id_amt = array();
    $order = array();
    $order_qty = array();
    $charge = array();
    while($row = $sql->fetch($result)){
        $s_id[$row['ord_num']] = $row['s_id'];
        $s_id_amt[$row['s_id']] += $row['ttl_qty'];
        $order[$row['ord_num']] += $row['amount'];
        $order_qty[$row['ord_num']] += $row['ttl_qty'];
        $charge[$row['ord_num']] = $row['charge'];
    }
    
    # % charge 

    foreach( $charge as $key => $val ){
        if(!empty($val)) {
            // echo $key.' => '.$val.'<br>';
            $order[$key] = $order[$key] + ( $val / $s_id_amt[$s_id[$key]] ) * $order_qty[$key].'<br>';
        }
        $sum += $order[$key]; 
    }
    
    $rows['sum'] = $sum;
    $rows['order'] = $order;
    $rows['order_qty'] = $order_qty;
    $rows['charge'] = $charge;
    $rows['s_id'] = $s_id;
    $rows['s_id_amt'] = $s_id_amt;
    
    return $rows;
}

} # END



function get_order_for_ship($order){

$sql = $this->sql;

$sum = 0;
$rows = array();

foreach( $order['order_qty'] as $key => $val ){

    $query  = " SELECT 
                `s_order`.`mat_useage` , 
                `s_order`.`mat_u_cost` , 
                `s_order`.`fusible` , 
                `s_order`.`interline` , 
                
                `s_order`.`acc_u_cost` , 
                
                `s_order`.`emb` , 
                `s_order`.`wash` , 
                `s_order`.`oth` , 
                
                `s_order`.`quota_fee` , 
                `s_order`.`comm_fee` , 
                
                `s_order`.`handling_fee` , 
                
                `s_order`.`cm` 
                FROM `s_order` 
                WHERE `s_order`.`order_num` = '".$key."'  ";
    $query .= " ;";

    if( $result = $sql->query($query) ){
    
        $fabric = 0;
        $accessory = 0;
        $special = 0;
        $others = 0;
        $handling_fee = 0;
        $cm = 0;
        $unit_cost = 0;
        $cost = 0;

        while($row = $sql->fetch($result)){
            $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
            $accessory = $row['acc_u_cost'];
            $special = $row['emb'] + $row['wash'] + $row['oth'];
            $others = $row['quota_fee'] + $row['comm_fee'];
            $handling_fee = $row['handling_fee'];
            $cm = $row['cm'];
            $unit_cost = $fabric + $accessory + $special + $others + $handling_fee + $cm;
            $cost = $unit_cost * $val;
        }
        $sum += $cost;
    }
}

$rows['sum'] = $sum;

return $rows;

} # END



function get_purchase($order){

$sql = $this->sql;

$sum = 0;
$rows = array();
$rate_date = array();

foreach( $order['order_qty'] as $key => $val ){

    $query  = " SELECT 
                `apb_po_link`.`qty` , 
                `apb_po_link`.`currency` , 
                `apb_po_link`.`ord_num` , 
                
                `apb_det`.`uprice` , 
                
                `apb`.`rcv_date` 
                FROM `apb` , `apb_det` , `apb_po_link` 
                WHERE `apb`.`rcv_num` = `apb_det`.`rcv_num` AND `apb_det`.`id` = `apb_po_link`.`rcv_id` AND `apb_po_link`.`ord_num` = '".$key."'  ";
    $query .= " ;";
    // echo $query.'<br>';

    if( $result = $sql->query($query) ){

        $cost = 0;

        while($row = $sql->fetch($result)){
            if( $row['currency']  <> 'USD' ) {
                if( empty($rate_date[$row['rcv_date']] ) ) {
                    $rate_date[$row['rcv_date']] = $GLOBALS['rate']->get_rate_date($row['rcv_date']);
                }
                    
                $row['uprice'] = ( $rate_date[$row['rcv_date']][$row['currency']] * $row['uprice'] ) / $rate_date[$row['rcv_date']]['USD'] ;
                // $row['currency'] * $price ;
                // echo $rate_date[$row['rcv_date']][$row['currency']].',';

            }
            # 訂單全部費用
            $cost += $row['qty'] * $row['uprice'];
        }
        
        $ship_ttl = $this->get_ship_ttl($key);
        $sum += ( $cost / $ship_ttl ) * $val ;
        
        // $sum += $cost;
    }
}
// echo $sum;
$rows['sum'] = $sum;

return $rows;

} # END



function get_ship_ttl($order){

    $sql = $this->sql;
    
    $query  = " SELECT 
                sum(`ttl_qty`) as `ttl_qty` FROM `shipping_doc_qty` WHERE `ord_num` = '".$order."' GROUP BY `ord_num` ";
    $query .= " ;";
    
    if( $result = $sql->query($query) ){
    
        $row = $sql->fetch($result);
    
        $rows = $row['ttl_qty'];
        
    }
    
    return $rows;

}



function get_remun($order){

$sql = $this->sql;

$sum = 0;
$rows = array();
$rate_date = array();

foreach( $order['order_qty'] as $key => $val ){

    $query  = " SELECT 
                `cost` , `acc_cost` , `oth_cost` , `exc_cost` , `smpl` , `rem_qty`  
                FROM `remun_det` 
                WHERE `remun_det`.`ord_num` = '".$key."' ";
    $query .= " ;";
    // echo $query.'<br>';

    if( $result = $sql->query($query) ){

        $cost = 0;
        $cm = 0;
        $qty = 0;
        $exc_cost = 0;

        while($row = $sql->fetch($result)){
        
            # C.M. 的
            $cm = $row['cost'] + $row['acc_cost'] + $row['oth_cost'];
            
            $qty = $row['smpl'] + $row['rem_qty'];
         
            $exc_cost = $row['exc_cost'];
            
            # 全部費用
            $cost += ( $cm * $qty ) - $exc_cost;    

        }
            
    
        
        // echo $key . ' ~ ' . $cost.'<br>';
        $ship_ttl = $this->get_ship_ttl($key);
        // echo $key . ' ~ ' . $cost.'<br>';
        $sum += ( $cost / $ship_ttl ) * $val ;
        
        // $sum += $cost;
    }
}
// echo $sum;
$rows['sum'] = $sum;

return $rows;

} # END





function get_shipping_det($fty, $year, $month){

$sql = $this->sql;

$q_str  = "select shipping_doc_qty.ord_num, sum(shipping_doc_qty.ttl_qty) as qty, sum(shipping_doc_qty.amount) as amount,
				  s_order.qty as ord_qty, s_order.mat_useage, s_order.mat_u_cost, s_order.fusible, s_order.interline,
				  s_order.acc_u_cost, s_order.quota_fee, s_order.comm_fee, s_order.cm, s_order.fty_cm, s_order.handling_fee
		   from shipping_doc, shipping_doc_qty, s_order
		   where shipping_doc.factory = '".$fty."' and shipping_doc.ship_date like '".$year."-".$month."-%' and shipping_doc_qty.s_id = shipping_doc.id
				 and s_order.order_num = shipping_doc_qty.ord_num
		   group by shipping_doc_qty.ord_num";

$rtn['shipping'] = array();
$ttl_ord_qty = $ttl_cm_cost = $ttl_shipping_cost = $ttl_fab_ord_cost = $ttl_acc_ord_cost = $ttl_fab_po_cost = $ttl_acc_po_cost = 0;
if( $result = $sql->query($q_str) ){
    while($row = $sql->fetch($result)){
		$ttl_ord_qty += $row['qty'];
		$ttl_shipping_cost += $row['amount'];
		$row['cm_cost'] = $row['fty_cm'] * $row['qty'];
		$ttl_cm_cost += $row['cm_cost'];
		$row['fab_ord_cost'] = $row['mat_useage'] * $row['mat_u_cost'] * $row['qty'];
		$row['acc_ord_cost'] = ($row['acc_u_cost'] + $row['fusible'] + $row['interline'] + $row['comm_fee'] + $row['cm'] + $row['handling_fee']) * $row['qty'];
		$ttl_fab_ord_cost += $row['fab_ord_cost'];
		$ttl_acc_ord_cost += $row['acc_ord_cost'];
		# apb 的主料費用
		$q_str = "select apb_po_link.ord_num, apb_po_link.qty, apb_po_link.currency, apb_det.uprice, apb_det.uprices,
						 apb_det.mat_code, apb.rcv_date, apb.payment
				  from apb_po_link, apb_det, apb
				  where apb_po_link.ord_num = '".$row['ord_num']."'	and apb_det.id = apb_po_link.rcv_id
						and apb.rcv_num = apb_det.rcv_num and (apb.status <> 4)";
		
		$apb_res = $sql->query($q_str);
		while($apb_row = $sql->fetch($apb_res)){
			if($apb_row['payment'] == '%|after') $apb_row['qty'] = $apb_row['qty'] * 0.6;
			if(substr($apb_row['mat_code'],0,1)=="A"){
				if($apb_row['currency'] == "USD"){
					$row['acc_po_cost'] += $apb_row['qty'] * $apb_row['uprice'];
				}elseif($apb_row['currency'] == "NTD"){
					$rate_row = $GLOBALS['rate']->get_rate("USD", $apb_row['rcv_date']);
					$row['acc_po_cost'] += number_format($apb_row['qty'] * $apb_row['uprice'] / $rate_row,5,'','');
				}else{
					$rate_row = $GLOBALS['rate']->get_rate("USD", $apb_row['rcv_date']);
					$rate_row2 = $GLOBALS['rate']->get_rate($apb_row['currency'], $apb_row['rcv_date']);
					$row['acc_po_cost'] += number_format($apb_row['qty'] * $apb_row['uprice'] * $rate_row2 / $rate_row,5,'','');
				}
			}else{
				if($apb_row['currency'] == "USD"){
					$row['fab_po_cost'] += $apb_row['qty'] * $apb_row['uprice'];
				}elseif($apb_row['currency'] == "NTD"){
					$rate_row = $GLOBALS['rate']->get_rate("USD", $apb_row['rcv_date']);
					$row['fab_po_cost'] += number_format($apb_row['qty'] * $apb_row['uprice'] / $rate_row,5,'','');
				}else{
					$rate_row = $GLOBALS['rate']->get_rate("USD", $apb_row['rcv_date']);
					$rate_row2 = $GLOBALS['rate']->get_rate($apb_row['currency'], $apb_row['rcv_date']);
					$row['fab_po_cost'] += number_format($apb_row['qty'] * $apb_row['uprice'] * $rate_row2 / $rate_row,5,'','');
				}
			}
		}
		$row['fab_po_cost'] = $row['fab_po_cost'] * number_format($row['qty'] / $row['ord_qty'],5,'','');
		$row['acc_po_cost'] = $row['acc_po_cost'] * number_format($row['qty'] / $row['ord_qty'],5,'','');
		$ttl_fab_po_cost += $row['fab_po_cost'];
		$ttl_acc_po_cost += $row['acc_po_cost'];
		
		$rtn['shipping'][] = $row;
	}
}

$rtn['ttl_ord_qty'] = $ttl_ord_qty;
$rtn['ttl_shipping_cost'] = $ttl_shipping_cost;
$rtn['ttl_cm_cost'] = $ttl_cm_cost;
$rtn['ttl_fab_ord_cost'] = $ttl_fab_ord_cost;
$rtn['ttl_acc_ord_cost'] = $ttl_acc_ord_cost;
$rtn['ttl_fab_po_cost'] = $ttl_fab_po_cost;
$rtn['ttl_acc_po_cost'] = $ttl_acc_po_cost;

return $rtn;
}



}

?>