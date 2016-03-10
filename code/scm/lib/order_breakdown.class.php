<?php

class ORDER_COST {



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



function get_capacity_mm($fty,$year,$this_mm,$reset_m) {

    $sql = $this->sql;

    $year2 = ( $this_mm + $reset_m <= 0 ) ? $year - 1 : $year + 1;
    
    $q_str = "SELECT * FROM capaci WHERE factory='".$fty."' AND ( year='".$year."' OR year='".$year2."' ) AND c_type='capacity' ";

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
    
    $yaer_arr = array();
    $yaer_arr[] = $year;
    $yaer_arr[] = $year2;
    sort($yaer_arr);
    $ym['year'] = $yaer_arr[0] . ' ~ ' . $yaer_arr[1];
    
    return $ym;
} // end func



function get_order_su_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$dept = is_factory($fty) ? " `s_order`.`dept` <> '".$fty."' AND " : '';

$query  = " SELECT 
`s_order`.`ie1` , 
`s_order`.`ie2` , 
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13  AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
    
        $qty = $row['qty'];
        
        $su = ( $row['ie2'] > 0 ) ? $row['ie2'] * $qty : $row['ie1'] * $qty ;
        
        $sum += $su;
    }
}


$rows['sum'] = $sum;

return $rows;

} # END



function get_fty_order_su_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$dept = is_factory($fty) ? " `s_order`.`dept` = '".$fty."' AND " : '';

$query  = " SELECT 
`s_order`.`ie1` , 
`s_order`.`ie2` , 
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
    
        $qty = $row['qty'];
        
        $su = ( $row['ie2'] > 0 ) ? $row['ie2'] * $qty : $row['ie1'] * $qty ;
        
        $sum += $su;
    }
}


$rows['sum'] = $sum;

return $rows;

} # END



function get_fty_un_apv_su_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$dept = is_factory($fty) ? " `s_order`.`dept` = '".$fty."' AND " : '';

$query  = " SELECT 
`s_order`.`style` , 
`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`qty` as 
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` < '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
    
        $qty = $row['qty'];

        $ie = group_order_style($row['style']) == 'top' ? 2 : 1;
        $su = ( $row['ie2'] > 0 ) ? $row['ie2'] * $qty : ( ( $row['ie1'] > 0 ) ? $row['ie1'] * $qty : 1 * $qty );
        
        $sum += $su;
    }
}

$rows['sum'] = $sum;

return $rows;

} # END




function get_forecast_su_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$query  = " SELECT 
`forecast`.*
FROM `forecast`
WHERE 
`forecast`.`cust` != '' AND 
`forecast`.`fty` = '".$fty."' AND 
`forecast`.`year` = '".$year."' AND 
`forecast`.`method` LIKE 'forecast' ";
$query .= " ;";
// echo $query.'<br>';
$qtys = array();
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
    
        $qty = explode(',',$row['qty']);

        for($i=0;$i<12;$i++){
            if( $month*1 == $i+1 )
                $sum += $qty[$i];
        }
    }
}

$rows['sum'] = $sum;

return $rows;

} # END



function get_fty_local_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$dept = is_factory($fty) ? " `s_order`.`dept` = '".$fty."' AND " : '';

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
`s_order`.`fty_cm` , 
`s_order`.`qty` as `ord_qty` , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm` , 
`s_order`.`partial_num` , 

`order_partial`.`p_qty` as `qty` , 
`order_partial`.`mks`  
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13  AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";
// echo $query.'<br>';
$qtys = array();
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
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));

    while($row = $sql->fetch($result)){
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        $ord_qty = $row['ord_qty'];
        
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        
        $cost = $unit_cost * $qty + $handling_fee * $qty;
    }
    $sum += $cost;
}


$rows['sum'] = $sum;

return $rows;

} # END



function get_est_sales_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();


$query  = " SELECT 
`s_order`.`uprice` , 
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
`s_order`.`dept` <> '".$fty."' AND 
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
        $sum += $row['qty'] * $row['uprice'];
    }
}


$rows['sum'] = $sum;

return $rows;

} # END


function get_est_un_apv_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$dept = is_factory($fty) ? " `s_order`.`dept` <> '".$fty."' AND " : '';

$query  = " SELECT 
`s_order`.`style` , 
`s_order`.`ie1` , 
`s_order`.`ie2` , 
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` < '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13  AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
    
        $qty = $row['qty'];
        $ie = group_order_style($row['style']) == 'top' ? 2 : 1;
        $su = ( $row['ie2'] > 0 ) ? $row['ie2'] * $qty : ( ( $row['ie1'] > 0 ) ? $row['ie1'] * $qty : $ie * $qty );
        
        $sum += $su;
    }
}


$rows['sum'] = $sum;

return $rows;

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
`s_order`.`fty_cm` , 

`s_order`.`qty` as `ord_qty`  , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm` ,
`s_order`.`org_cm` ,
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
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
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));

    while($row = $sql->fetch($result)){
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2'] * $fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];

        $qty = $row['qty'];
        $ord_qty = $row['ord_qty'];
        
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        
        $sum += $unit_cost * $qty + $handling_fee * $qty;
       
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

`s_order`.`qty` as `ord_qty` , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm`  ,
`s_order`.`fty_cm`  ,
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
// `s_order`.`status` >= '4' AND 
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $handling_fee = 0;
    $cm = 0;
    $unit_cost = 0;
    $sum = 0;
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));

    while($row = $sql->fetch($result)){

        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];

        $qty = $row['qty'];
        
        $unit_cost = $cm;
        
        $sum += $unit_cost * $qty + $handling_fee * $qty ;

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
 ,
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
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



function get_expense_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

if( $fty == 'LY'){
    $fty = 'KA';
} else if ( $fty == 'CF') {
    $fty = 'DA';
} else {
    $fty = $fty;
}
$query  = " SELECT 

`expense`.`cost`

FROM `expense` 
WHERE 
`expense`.`type` = 'F' AND 
`expense`.`dept` = '".$fty."' AND   
`expense`.`yy` = '".$year."' AND 
`expense`.`mm` = '".$month."'  ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $special = 0;

    $unit_cost = 0;
    $sum = 0;
    

    while($row = $sql->fetch($result)){

        $cost = $row['cost'];
        
        $sum += $cost;

    }
    
}

$rows['sum'] = $sum;

return $rows;

} # END




function get_fty_expense_etd($fty,$year,$month){

$sql = $this->sql;

$sum = 0;
$rows = array();

$query  = " SELECT 

`expense`.`cost`

FROM `expense` 
WHERE 
`expense`.`type` = 'F' AND 
`expense`.`dept` = '".$fty."' AND   
`expense`.`yy` = '".$year."' AND 
`expense`.`mm` = '".$month."'  ";
$query .= " ;";
// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $special = 0;

    $unit_cost = 0;
    $sum = 0;
    

    while($row = $sql->fetch($result)){

        $cost = $row['cost'];
        
        $sum += $cost;

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
`s_order`.`cm` ,
`s_order`.`fty_cm` 
 ,
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
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
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));

    while($row = $sql->fetch($result)){
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        
        $cost = $unit_cost * $qty + $handling_fee * $qty;
    }
    $sum += $cost;
}


$rows['sum'] = $sum;

return $rows;

} # END



function get_order_su_sum_det($fty, $year, $month){

$sql = $this->sql;

// $dept = is_factory($fty) ? " `s_order`.`dept` = '".$fty."' AND " : '';

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
`s_order`.`fty_cm` , 
`s_order`.`qty` , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm`  ,
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";

// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $ttl_qty = 0;
    $ttl_su = 0;
    $ttl_est_sales = 0;
    $ttl_est_cost = 0;
    $ttl_est_cm = 0;
    $ttl_est_other = 0;
    $ttl_est_gm = 0;
    $ttl_est_gm_rate = 0;
    
    $est_order_arr = array();
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));
    
    while($row = $sql->fetch($result)){
    
        $est_order['order_num'] = $row['order_num'];
        $est_order['uprice'] = $row['uprice'];
        $est_order['qty'] = $row['qty'];
        
        $ttl_qty += $row['qty'];
        
        $ie = ( $row['ie2'] > 0 ) ? $row['ie2'] : $row['ie1'] ;
        $ttl_su += $ie * $row['qty'];
        $est_order['ttl_su'] = $ie * $row['qty'];
        
        $est_order['est_sales'] = $row['qty'] * $row['uprice'];
        $ttl_est_sales += $est_order['est_sales'];
        
        /*****/
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        /*****/
        
        $cost = $unit_cost * $qty + $handling_fee * $qty;
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
$rows['ttl_su'] = $ttl_su;
$rows['ttl_est_sales'] = $ttl_est_sales;
$rows['ttl_est_cost'] = $ttl_est_cost;

$rows['ttl_est_cm'] = $ttl_est_cm;
$rows['ttl_est_other'] = $ttl_est_other;

$rows['ttl_est_gm'] = $ttl_est_gm;
$rows['ttl_est_gm_rate'] = ( $ttl_est_gm / $ttl_est_sales ) * 100;

return $rows;
}






function get_fty_un_apv_su_det($fty, $year, $month){

$sql = $this->sql;

// $dept = is_factory($fty) ? " `s_order`.`dept` = '".$fty."' AND " : '';

$query  = " SELECT 
`s_order`.`order_num` , 
`s_order`.`style` , 
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
`s_order`.`fty_cm` , 
`s_order`.`qty` , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm` ,
`order_partial`.`p_qty` as `qty` 
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` < '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 

`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";

// echo $query.'<br>';
if( $result = $sql->query($query) ){

    $ttl_qty = 0;
    $ttl_su = 0;
    $ttl_est_sales = 0;
    $ttl_est_cost = 0;
    $ttl_est_cm = 0;
    $ttl_est_other = 0;
    $ttl_est_gm = 0;
    $ttl_est_gm_rate = 0;
    
    $est_order_arr = array();
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));
    
    while($row = $sql->fetch($result)){
    
        $est_order['order_num'] = $row['order_num'];
        $est_order['uprice'] = $row['uprice'];
        $est_order['qty'] = $row['qty'];
        
        $ttl_qty += $row['qty'];
        
        $ie = group_order_style($row['style']) == 'top' ? 2 : 1;
        $ie = ( $row['ie2'] > 0 ) ? $row['ie2'] : ( ( $row['ie1'] > 0 ) ? $row['ie1'] : $ie ) ;
        $ttl_su += $ie * $row['qty'];
        $est_order['ttl_su'] = $ie * $row['qty'];
        
        $est_order['est_sales'] = $row['qty'] * $row['uprice'];
        $ttl_est_sales += $est_order['est_sales'];
        
        /*****/
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        /*****/
        
        $cost = $unit_cost * $qty + $handling_fee * $qty;
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
$rows['ttl_su'] = $ttl_su;
$rows['ttl_est_sales'] = $ttl_est_sales;
$rows['ttl_est_cost'] = $ttl_est_cost;

$rows['ttl_est_cm'] = $ttl_est_cm;
$rows['ttl_est_other'] = $ttl_est_other;

$rows['ttl_est_gm'] = $ttl_est_gm;
$rows['ttl_est_gm_rate'] = ( $ttl_est_gm / $ttl_est_sales ) * 100;

return $rows;
}






function get_order_su_det($fty, $year, $month){

$sql = $this->sql;

$dept = is_factory($fty) ? " `s_order`.`dept` <> '".$fty."' AND " : '';

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
`s_order`.`fty_cm` , 
`s_order`.`qty` as `ord_qty` , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm` , 
`s_order`.`partial_num` , 

`order_partial`.`p_qty` as `qty` , 
`order_partial`.`mks`  
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` >= '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id`
ORDER BY `order_partial`.`p_etd` , `order_partial`.`id` ";
$query .= " ;";


if( $result = $sql->query($query) ){

    $ttl_qty = 0;
    $ttl_su = 0;
    $ttl_est_sales = 0;
    $ttl_est_cost = 0;
    $ttl_est_cm = 0;
    $ttl_est_other = 0;
    $ttl_est_gm = 0;
    $ttl_est_gm_rate = 0;
    
    $est_order_arr = array();
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));
    
    while($row = $sql->fetch($result)){
    
        $est_order['order_num'] = $row['order_num'];
        $est_order['uprice'] = $row['uprice'];
        $est_order['qty'] = $row['qty'];
        $est_order['mks'] = $row['partial_num'] > 1 ? ' ('.$row['mks'].')':'';
        
        $ttl_qty += $row['qty'];
        
        $ie = ( $row['ie2'] > 0 ) ? $row['ie2'] : $row['ie1'] ;
        $ttl_su += $ie * $row['qty'];
        $est_order['ttl_su'] = $ie * $row['qty'];
        
        $est_order['est_sales'] = $row['qty'] * $row['uprice'];
        $ttl_est_sales += $est_order['est_sales'];
        
        /*****/
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        /*****/
        
        $cost = $unit_cost * $qty + $handling_fee * $qty;
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
$rows['ttl_su'] = $ttl_su;
$rows['ttl_est_sales'] = $ttl_est_sales;
$rows['ttl_est_cost'] = $ttl_est_cost;

$rows['ttl_est_cm'] = $ttl_est_cm;
$rows['ttl_est_other'] = $ttl_est_other;

$rows['ttl_est_gm'] = $ttl_est_gm;
$rows['ttl_est_gm_rate'] = ( $ttl_est_gm / $ttl_est_sales ) * 100;

return $rows;
}




function get_un_apv_su_det($fty, $year, $month){

$sql = $this->sql;

$dept = is_factory($fty) ? " `s_order`.`dept` <> '".$fty."' AND " : '';

$query  = " SELECT 
`s_order`.`order_num` , 
`s_order`.`style` , 
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
`s_order`.`fty_cm` , 
`s_order`.`qty` as `ord_qty` , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm` , 
`s_order`.`partial_num` , 

`order_partial`.`p_qty` as `qty` , 
`order_partial`.`mks`  
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` < '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
$query .= " ;";


if( $result = $sql->query($query) ){

    $ttl_qty = 0;
    $ttl_su = 0;
    $ttl_est_sales = 0;
    $ttl_est_cost = 0;
    $ttl_est_cm = 0;
    $ttl_est_other = 0;
    $ttl_est_gm = 0;
    $ttl_est_gm_rate = 0;
    
    $est_order_arr = array();
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));
    
    while($row = $sql->fetch($result)){
    
        $est_order['order_num'] = $row['order_num'];
        $est_order['uprice'] = $row['uprice'];
        $est_order['qty'] = $row['qty'];
        
        $ttl_qty += $row['qty'];
        
        $ie = group_order_style($row['style']) == 'top' ? 2 : 1;
        $ie = ( $row['ie2'] > 0 ) ? $row['ie2'] : ( ( $row['ie1'] > 0 ) ? $row['ie1'] :$ie ) ;
        $ttl_su += $ie * $row['qty'];
        $est_order['ttl_su'] = $ie * $row['qty'];
        
        $est_order['est_sales'] = $row['qty'] * $row['uprice'];
        $ttl_est_sales += $est_order['est_sales'];
        
        /*****/
        $fabric = ( $row['mat_useage'] * $row['mat_u_cost'] ) + $row['fusible'] + $row['interline'];
        $accessory = $row['acc_u_cost'];
        $special = $row['emb'] + $row['wash'] + $row['oth'];
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        /*****/
        
        $cost = $unit_cost * $qty + $handling_fee * $qty;
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
$rows['ttl_su'] = $ttl_su;
$rows['ttl_est_sales'] = $ttl_est_sales;
$rows['ttl_est_cost'] = $ttl_est_cost;

$rows['ttl_est_cm'] = $ttl_est_cm;
$rows['ttl_est_other'] = $ttl_est_other;

$rows['ttl_est_gm'] = $ttl_est_gm;
$rows['ttl_est_gm_rate'] = ( $ttl_est_gm / $ttl_est_sales ) * 100;

return $rows;
}





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
`s_order`.`fty_cm` , 
`s_order`.`qty` as `ord_qty` , 

`s_order`.`ie1` , 
`s_order`.`ie2` , 
`s_order`.`cm` , 
`s_order`.`partial_num` , 

`order_partial`.`p_qty` as `qty` , 
`order_partial`.`mks`  
FROM `s_order` , `order_partial`
WHERE 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`s_order`.`status` >= '0' AND 
`s_order`.`status` < '4' AND 
`s_order`.`status` <> 5 AND 
`s_order`.`status` <> 13 AND 
`s_order`.`factory` = '".$fty."' AND 
".$dept."
`order_partial`.`p_etd` LIKE '".$year.'-'.$month."%' 
GROUP BY `order_partial`.`id` ";
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
    
    $fty_cm = ( !empty($row['fty_cm']) ) ? $row['fty_cm'] : $this->get_cm(strtolower($fty.'-cm'));
    
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
        # 移除傭金
        // $others = $row['quota_fee'] + $row['comm_fee'];
        $others = $row['quota_fee'];
        $handling_fee = $row['handling_fee'];
        $cm = ( $row['ie2'] > 0 ) ? $row['ie2']*$fty_cm : ( !empty($row['ie1']) ) ? $row['ie1']*$fty_cm : $row['cm'];
        // $cm = $row['cm'];
        $uprice = $row['uprice'];
        $qty = $row['qty'];
        $ord_qty = $row['ord_qty'];
        $unit_cost = $fabric + $accessory + $special + $others + $cm;
        /*****/
        
        $cost = $unit_cost * $qty + $handling_fee * $qty;
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
            # 移除傭金
            // $others = $row['quota_fee'] + $row['comm_fee'];
            $others = $row['quota_fee'];
            $handling_fee = $row['handling_fee'];
            $cm = $row['cm'];
            $unit_cost = $fabric + $accessory + $special + $others + $cm;
            $cost = $unit_cost * $val + $handling_fee * $val;
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
        # 移除傭金
        // $row['acc_ord_cost'] = ($row['acc_u_cost'] + $row['fusible'] + $row['interline'] + $row['comm_fee'] + $row['cm'] + $row['handling_fee']) * $row['qty'];
		$row['acc_ord_cost'] = ($row['acc_u_cost'] + $row['fusible'] + $row['interline'] + $row['cm'] + $row['handling_fee']) * $row['qty'];
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