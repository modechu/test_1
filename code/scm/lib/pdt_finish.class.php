<?php 

class PDT_FINISH {

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
# ->mdf_ie($ord_num) qty  su  cutting  cut_su  sawing  saw_su  packing  pack_su
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_ie($order_num,$ie) {

$sql = $this->sql;

$q_str = "SELECT `qty` FROM `s_order` WHERE `order_num` = '".$order_num."' ;";
$q_results = $sql->query($q_str);
$row = $sql->fetch($q_results);
$order_qty = ( $row['qty'] > 0 ) ? $row['qty'] : 0 ;
// echo $q_str.'<br>';
$q_str = "SELECT sum(`qty`) FROM `cutting_out` WHERE `ord_num` = '".$order_num."' ;";
$q_results = $sql->query($q_str);
$row = $sql->fetch($q_results);
$cutting_qty = ( $row['qty'] > 0 ) ? $row['qty'] : 0 ;
// echo $q_str.'<br>';
$q_str = "SELECT sum(`qty`) FROM `saw_out_put` WHERE `ord_num` = '".$order_num."' ;";
$q_results = $sql->query($q_str);
$row = $sql->fetch($q_results);
$out_put_qty = ( $row['qty'] > 0 ) ? $row['qty'] : 0 ;
// echo $q_str.'<br>';
$q_str = "SELECT sum(`qty`) FROM `shipping` WHERE `ord_num` = '".$order_num."' ;";
$q_results = $sql->query($q_str);
$row = $sql->fetch($q_results);
$shipping_qty = ( $row['qty'] > 0 ) ? $row['qty'] : 0 ;
// echo $q_str.'<br>';
$q_str = "UPDATE `pdt_finish` SET 
`qty` = '".$order_qty."' , 
`su` = '".set_su($ie,$order_qty)."' , 
`cutting` = '".$cutting_qty."' , 
`cut_su` = '".set_su($ie,$cutting_qty)."' , 
`sawing` = '".$out_put_qty."' , 
`saw_su` = '".set_su($ie,$out_put_qty)."' , 
`packing` = '".$shipping_qty."' , 
`pack_su` = '".set_su($ie,$shipping_qty)."' 
WHERE `order_num` = '".$order_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}


    return true;
} # end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_cut($parm,$ie,$qty)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_cut($parm,$ie,$qty) {

$sql = $this->sql;

$q_str = "UPDATE `pdt_finish` SET `cutting` = '".$qty."' , `cut_su` = '".set_su($ie,$qty)."' WHERE `order_num` = '".$parm['ord_num']."' ;";
$q_results = $sql->query($q_str);
// echo $q_str.'<br>';

return $row['qty'];
} # end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} # end class
?>