<?php 

class  FTY_COLORWAY {
	
var $sql;
var $msg ;

#
#
#
#
#
#
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
#
#
#
#
#
#
# :UPDATE_COLORWAY
function update_colorway($color,$qty) {

$sql = $this->sql;

foreach( $qty as $w_id => $ex_qty ){

    $pdc_color = $color[$w_id];
    $pdc_qty = implode(",", $ex_qty);
    
    $q_str = "update `wiqty` set `pdc_color` = '".trim($pdc_color)."' , `pdc_qty` = '".trim($pdc_qty)."' where `id` = '".$w_id."' ;";
    // echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $_SESSION['MSG'][] = "Error! Can't update data!";
        return false;
    }

}

return true;

} # FUN END
#
#
#
#
#
#
# :SUBMIT_COLORWAY
function submit_colorway($ord_num) {

$sql = $this->sql;

$q_str = "update `s_order` set 
`pdc_cut_sub_user` = '".$_SESSION['USER']['ADMIN']['id']."' , 
`pdc_cut_sub_date` = NOW() , 
`pdc_status` = '2' 
where `order_num` = '".$ord_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $_SESSION['MSG'][] = "Error! Can't update data!";
    return false;
}

return true;

} # FUN END
#
#
#
#
#
#
# :REVISE_COLORWAY
function revise_colorway($ord_num) {

$sql = $this->sql;

$q_str = "update `s_order` set 
`pdc_cut_sub_user` = '' , 
`pdc_cut_sub_date` = '' , 
`pdc_status` = '0' ,
`pdc_version` = `pdc_version`+1
where `order_num` = '".$ord_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $_SESSION['MSG'][] = "Error! Can't update data!";
    return false;
}

return true;

} # FUN END
#
#
#
#
#
} # CLASS END
?>