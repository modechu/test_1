<?php 

class PDTION {

var $sql;
var $msg;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->init($sql)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function init($sql) {
	$this->msg = new MSG_HANDLE();

	if (!$sql) {
		$this->msg->add("Error ! Data base can't connect.");
		return false;
	}
	$this->sql = $sql;
	return true;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_ie($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_ie($order_num) {

$sql = $this->sql;

foreach($order_num as $num){
    // 
    // echo $num."<br>";
    // order_partial
    // 訂單  p_qty	p_su
    // 排產  ext_qty	ext_su
    // 裁減  cut_qty	cut_su
    // 產出  p_qty_done
    // 出貨  p_qty_shp

    $q_str = "SELECT 
    max(`p_etp`) as `etp` , min(`p_etd`) as `etd` , 
    max(`p_ets`) as `ets` , min(`p_etf`) as `etf`, 
    sum(`p_su`) as `etp_su` , 
    sum(`ext_qty`) as `ext_qty` , 
    sum(`ext_su`) as `ext_su` , 
    sum(`p_qty_done`) as `qty_done` 
    
    FROM `order_partial`
    
    WHERE `ord_num` = '".$num."' 
    GROUP BY `ord_num` 
    ;";
    $q_results = $sql->query($q_str);
    $row = $sql->fetch($q_results);
    
    $ets = $row['ets'];
    $etf = $row['etf'];
    $ext_qty = $row['ext_qty'];
    $ext_su = $row['ext_su'];
    $qty_done = $row['qty_done'];
    $etp_su = $GLOBALS['order']->distri_month_su($row['etp_su'],$row['etp'],$row['etd']);
    $fty_su = $GLOBALS['order']->distri_month_su($row['ext_su'],$row['ets'],$row['etf']);
    
    $q_str = "SELECT 
    sum(`qty`) as `qty` , sum(`su`) as `su` , 
    max(`out_date`) as `etf`, min(`out_date`) as `ets` 
    FROM `saw_out_put` 						  
    WHERE `ord_num` = '".$num."' 
    GROUP BY `ord_num` 
    ;";
    // echo $q_str.'<br>';
    $q_results = $sql->query($q_str);
    $row = $sql->fetch($q_results);
    
    $out_su = $GLOBALS['order']->distri_month_su($row['su'],$row['ets'],$row['etf']);
    $ext_period = countDays($row['ets'],$row['etf']);
    
    // print_r($etp_su);
    // echo "<br>";
    // print_r($fty_su);
    // echo "<br>";
    // print_r($out_su);
    // echo "<p>";
    
    // pdtion
    // 訂單  無
    // 排產  ext_qty	ext_su
    // 裁減  qty_cut
    // 產出  qty_done
    // 出貨  shp_qty
    
    $q_str = "UPDATE 
    `pdtion` SET 
    `ets` = '".$ets."' , 
    `etf` = '".$etf."' , 
    `etp_su` = '".$etp_su."' , 
    `fty_su` = '".$fty_su."' , 
    `out_su` = '".$out_su."' , 
    `qty_done` = '".$qty_done."' , 
    `ext_period` = '".$ext_period."' , 
    `ext_qty` = '".$ext_qty."' , 
    `ext_su` = '".$ext_su."' 
    WHERE `order_num` = '".$num."' 
    ;";
    $sql->query($q_str);
    // echo $q_str.'<br>';
}
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_cut($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_cut($parm) {

$sql = $this->sql;

$q_str = "SELECT sum(`qty`) as `qty` FROM `cutting_out` WHERE `ord_num` = '".$parm['ord_num']."' ;";
$q_results = $sql->query($q_str);
$row = $sql->fetch($q_results);
// echo $q_str.'<br>';
$q_str = "UPDATE `pdtion` SET `qty_cut` = '".$row['qty']."' WHERE `order_num` = '".$parm['ord_num']."' ;";
$q_results = $sql->query($q_str);
// echo $q_str.'<br>';
return $row['qty'];
} # end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} # end class
?>