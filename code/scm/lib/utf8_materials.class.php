<?php 

class UTF8_MATERIALS {

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
        $this->msg->add("Error ! 無法聯上資料庫.");
        return false;
    }
    $this->sql = $sql;
    
    return true;
} // end func
#
#
#
#
#
#
# :ADD_REQUI_NOTIFY
function add_requi_notify($parm) {

	global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
    
    $id = get_insert_id("requi_notify","1");
	$q_str = "INSERT INTO requi_notify ( id , ord_num , fty , line , create_user , create_date ) VALUES (
    '".$id."',
    '".$parm['ord_num']."',
    '".$parm['fty']."',
    '".$parm['line']."',
    '".$_SESSION['USER']['ADMIN']['id']."',
    NOW())";
    
	if(!$q_result = $MySQL->query($q_str)){
		return false;
	}

	$MySQL->po_disconnect();

	return $id;
}
#
#
#
#
#
#
# :EDIT_REQUI_NOTIFY_LINE
function edit_requi_notify_line($parm) {

	global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
    
	$q_str = "UPDATE `requi_notify` SET 
    `line` = '".$parm['line']."' WHERE 
    `id` = '".$parm['rn_num']."';";
    echo $q_str.'<br>';
	if(!$q_result = $MySQL->query($q_str)){
		return false;
	}

	$MySQL->po_disconnect();

	return $parm['rn_num'];
}
#
#
#
#
#
#
# :ADD_REQUI_NOTIFY_BOM
function add_requi_notify_bom($rn_num,$wi_id,$ARR_qty) {

    global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
    
    foreach($ARR_qty as $wiqty_id => $a_qty){
        $m = 0 ;
        $qty = '';
        foreach($a_qty as $this_qty){
            $qty .= ($m>0?',':'').($this_qty>0?$this_qty:0);
            $m++;
        }
        $q_str = "INSERT INTO requi_notify_bom ( id , rn_num , wiqty_id , wi_id , qty ) VALUES (
        '".get_insert_id("requi_notify_bom","1",1)."',
        '".$rn_num."',
        '".$wiqty_id."',
        '".$wi_id."',
        '".$qty."') ;";
        
        if(!$q_result = $MySQL->query($q_str)){
            return false;
        }
    }

    $MySQL->po_disconnect();
    
	return true;
}
#
#
#
#
#
#
# :EDIT_REQUI_NOTIFY_BOM
function edit_requi_notify_bom($rn_num,$wi_id,$ARR_qty) {

    global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
    
    foreach($ARR_qty as $wiqty_id => $a_qty){
        $m = 0 ;
        $qty = '';
        foreach($a_qty as $this_qty){
            $qty .= ($m>0?',':'').($this_qty>0?$this_qty:0);
            $m++;
        }
        
        $q_str = "SELECT `id` FROM `requi_notify_bom` WHERE `rn_num` = '".$rn_num."' AND `wiqty_id` = '".$wiqty_id."' AND `wi_id` = '".$wi_id."' ;";
        $q_result = $MySQL->query($q_str);
        
        if(!$row = $MySQL->fetch($q_result)){
        
            $q_str = "INSERT INTO `requi_notify_bom` ( id , rn_num , wiqty_id , wi_id , qty ) VALUES (
            '".get_insert_id("requi_notify_bom","1",1)."',
            '".$rn_num."',
            '".$wiqty_id."',
            '".$wi_id."',
            '".$qty."') ;";
        } else {
            $q_str = "UPDATE `requi_notify_bom` SET 
            `qty` = '".$qty."' WHERE 
            `rn_num` = '".$rn_num."' AND 
            `wiqty_id` = '".$wiqty_id."' AND 
            `wi_id` = '".$wi_id."' ;";
        }
        
        echo $q_str.'<br>';
        if(!$q_result = $MySQL->query($q_str)){
            return false;
        }
    }

    $MySQL->po_disconnect();
    
	return true;
}
#
#
#
#
#
#
# :GET_BOM_MATERIALS_QTY
function get_bom_materials_qty($rn_num) {

    global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select wiqty_id , qty from `requi_notify_bom` where `rn_num` = '".$rn_num."'";
	$q_result = $MySQL->query($q_str);
	$rtn = array();
	while($row = $MySQL->fetch($q_result)){
		// $rtn[] = $row;
        $rtn[$row['wiqty_id']] = $row['qty'];
	}
	
	$MySQL->po_disconnect();
    
	return $rtn;
}
#
#
#
#
#
#
# :GET_SEND_QTY
function get_send_qty($ord_num) {

    global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

    $q_str = "SELECT `id` FROM `requi_notify` WHERE `ord_num` = '".$ord_num."' AND `status` = 4 ORDER BY `id` ASC";
	$q_result = $MySQL->query($q_str);
	$rtn = array();
	while($row = $MySQL->fetch($q_result)){
        $q_strs = "SELECT * FROM `requi_notify_bom` WHERE `rn_num` = '".$row['id']."' ORDER BY `id` ASC";
        $q_results = $MySQL->query($q_strs);
        while($rows = $MySQL->fetch($q_results)){
            if( $rtn[$rows['wiqty_id']] ) {
                $qty_arr_o = explode(",", $rtn[$rows['wiqty_id']] );
                $qty_arr = explode(",", $rows['qty'] );
                $qty_new = array();
                foreach($qty_arr_o as $k => $v){
                    $qty_new[] = $v + $qty_arr[$k];
                }
                $qty = implode(",", $qty_new);
            } else {
                $qty = $rows['qty'];
            }
            $rtn[$rows['wiqty_id']] = $qty;
        }
	}
	
	$MySQL->po_disconnect();
    
	return $rtn;
}
#
#
#
#
#
#
# :ADDED_SEND_MATERIAL
function added_send_material($rn_num,$ARR_bom_id,$ARR_qty) {

    global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
    
    foreach($ARR_qty as $key => $qty){
        if( $qty > 0 ){
            $q_str = "SELECT `id` FROM `requi_notify_det` WHERE `rn_num` = '".$rn_num."' AND `po_bom_id` = '".$ARR_bom_id[$key]."' ;";
            $q_result = $MySQL->query($q_str);
            
            if(!$row = $MySQL->fetch($q_result)){
                $q_str = "INSERT INTO requi_notify_det ( id , rn_num , po_bom_id , qty ) VALUES (
                '".get_insert_id("requi_notify_det","1",1)."',
                '".$rn_num."',
                '".$ARR_bom_id[$key]."',
                '".$qty."') ;";
                // echo $q_str.'<br>';
                if(!$q_result = $MySQL->query($q_str)){
                    return false;
                }
            } else {
                $q_str = "UPDATE `requi_notify_det` SET 
                `qty` = '".$qty."' WHERE 
                `rn_num` = '".$rn_num."' AND
                `po_bom_id` = '".$ARR_bom_id[$key]."'
                ;";
                // echo $q_str.'<br>';
                if(!$q_result = $MySQL->query($q_str)){
                    return false;
                }
            }
        }
    }

    $MySQL->po_disconnect();
    
	return true;
}
#
#
#
#
#
#
# :GET_SEND_QTY
function get_rn_num($rn_num) {

    global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

    $q_str = "SELECT * FROM `requi_notify` WHERE `id` = '".$rn_num."' ;";
	$q_result = $MySQL->query($q_str);
	$rtn = array();
	$rtn = $MySQL->fetch($q_result);

	$MySQL->po_disconnect();
    
	return $rtn;
}
#
#
#
#
#
#
# :GET_SEND_QTY
function get_bom_qty($rn_num) {

    global $MySQL;

    $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

    $q_str = "SELECT * FROM `requi_notify_det` WHERE `rn_num` = '".$rn_num."' ;";
	$q_result = $MySQL->query($q_str);
	$rtn = array();
    while($row = $MySQL->fetch($q_result)){
        $rtn[$row['po_bom_id']] = $row['qty'];
        // $rtn[] = $row;
    }

	$MySQL->po_disconnect();
    
	return $rtn;
}
#
#
#
#
#
#
# :GET_BOM_MATERIALS
function get_bom_materials($wi_num) {

    global $MySQL;

	$MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select * from `po_bom` where `ord_num` = '".$wi_num."'";
	$q_result = $MySQL->query($q_str);
	$rtn = array();
	while($row = $MySQL->fetch($q_result)){
		$rtn[] = $row;
		$rtn['utf8'][$row['mat_cat']][$row['bom_id']] = $row;
	}
	
	$MySQL->po_disconnect();
    
	return $rtn;
}
#
#
#
#
#
#
# :DO_SEND_MATERIAL_SUBMIT
function do_send_material_submit($rn_num) {

	global $MySQL;
    
	$MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "update `requi_notify` set `sub_date` = NOW() , `sub_user` = '".$_SESSION['USER']['ADMIN']['id']."' , `status` = 2
			  where `id` = '".$rn_num."'";
	
	if (!$q_result = $MySQL->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($MySQL->msg);
		return false;    
	}   		
	
	$MySQL->po_disconnect();
	
	return true;
}
#
#
#
#
#
#
# :DO_SEND_MATERIAL_CONFIRM
function do_send_material_confirm($rn_num) {

	global $MySQL;
    
	$MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "update `requi_notify` set `cfm_date` = NOW() , `cfm_user` = '".$_SESSION['USER']['ADMIN']['id']."' , `status` = 4
			  where `id` = '".$rn_num."'";
	
	if (!$q_result = $MySQL->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($MySQL->msg);
		return false;    
	}   		
	
	$MySQL->po_disconnect();
	
	return true;
}
#
#
#
#
#
#
# :DO_SEND_MATERIAL_REVISE
function do_send_material_revise($rn_num) {

	global $MySQL;
    
	$MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "update `requi_notify` set 
    `sub_date` = '' , `sub_user` = '' , 
    `cfm_date` = '' , `cfm_user` = '' , 
    `status` = 0 , `version` = `version`+1
    where `id` = '".$rn_num."'";
	
	if (!$q_result = $MySQL->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($MySQL->msg);
		return false;    
	}   		
	
	$MySQL->po_disconnect();
	
	return true;
}
#
#
#
#
#
#
# :DO_SEND_MATERIAL_REVISE
function do_send_material_delete($rn_num) {

	global $MySQL;
    
	$MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "DELETE FROM `requi_notify` WHERE `id` = '".$rn_num."'; ";

	if (!$q_result = $MySQL->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($MySQL->msg);
		return false;    
	}   		
	
	$MySQL->po_disconnect();
	
	return true;
}




















#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bom_add($parm)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function bom_add($parm) {
	$sql = $this->sql;
	$id = get_insert_id("po_bom","1");
	
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "INSERT INTO po_bom (id, ord_num, fty, bom_id, mat_code, mat_cat, mat_id, mat_name, color, o_color, size, consump, loss, qty, o_qty, unit , up_user , up_date) 
    VALUES('".$id."','".
    $parm['ord_num']."','".
    $parm['fty']."','".
    $parm['bom_id']."','".
    $parm['mat_code']."','".
    $parm['mat_cat']."','".
    $parm['mat_id']."','".
    $parm['mat_name']."','".
    $parm['color']."','".
    $parm['o_color']."','".
    $parm['size']."','".
    $parm['consump']."','".
    $parm['loss']."','".
    $parm['qty']."','".
    $parm['o_qty']."','".
    $parm['unit']."','".
    $_SESSION['SCACHE']['ADMIN']['id']."', NOW() )";
    // echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error !  Database can't insert.");
        $this->msg->merge($sql->msg);
        return false;    
    }   			
		
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bom_edit($parm)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function bom_edit($parm) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "update po_bom set 
    mat_code    = '".$parm['mat_code']."', 
    mat_name    = '".$parm['mat_name']."', 
    color       = '".$parm['color']."', 
    o_color     = '".$parm['o_color']."', 
    size        = '".$parm['size']."', 
    consump     = '".$parm['consump']."', 
    loss        = '".$parm['loss']."', 
    o_qty       = '".$parm['o_qty']."', 
    qty         = '".$parm['qty']."', 
    unit        = '".$parm['unit']."',
    up_user     = '".$_SESSION['SCACHE']['ADMIN']['id']."',
    up_date     = NOW() 
    where id = '".$parm['po_bom_id']."' ;";
	// echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$sql->po_disconnect();
	
	return true;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->submit_mat($bom_id, $mat_cat)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function submit_mat($bom_id, $mat_cat) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "update po_bom
			  set trans_sub_date = '".date('Y-m-d H:i:s')."'
			  where bom_id = ".$bom_id." and mat_cat = '".$mat_cat."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
	
	$sql->po_disconnect();
	
	return true;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bom_reopen($ord_num)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function bom_reopen($ord_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "update po_bom
			  set cfm_date = '', cfm_user = ''
			  where ord_num = '".$ord_num."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
	
	$sql->po_disconnect();
	
	return true;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->confirm_all($ord_numt)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function confirm_all($ord_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "update po_bom
			  set cfm_date = NOW() , cfm_user = '".$_SESSION['SCACHE']['ADMIN']['id']."'
			  where ord_num = '".$ord_num."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}else{
		$sql->po_disconnect();
		return true;
	}
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bom_cfm_mat($bom_id, $mat_cat)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_confirm_status($ord_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

	$q_str = "select cfm_date, cfm_user from po_bom where ord_num = '".$ord_num."' AND cfm_user != '' ORDER BY cfm_date DESC Limit 0,1";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$sql->po_disconnect();
    
    if ($row = $sql->fetch($q_result)) {
        return true;
    } else {
        return false;
    }
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search()
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search($parm) {

	$sql = $this->sql;
	
	if($parm['ord_num']){
		$where_str = " and ord_num like '%".$parm['ord_num']."%'";
	}
    
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select distinct po_bom.ord_num, fty
			  from po_bom
			  where fty = '".$parm['fty']."'".$where_str."
			  order by ord_num";

	$q_result = $sql->query($q_str);
	$num_rows = mysql_num_rows($q_result);
	
	$record_begin = $parm['now_num'] * $parm['page_num'];
    $record_begin = $record_begin > $num_rows ? $num_rows : $record_begin;
	
	$bom_row['wi'] = array();
	$q_str = "select distinct po_bom.ord_num, fty
			  from po_bom
			  where fty = '".$parm['fty']."'".$where_str."
			  order by ord_num
			  limit ".($record_begin)." , ".$parm['page_num'];
	
	$q_result = $sql->query($q_str);
	
	


	if( $q_result = $sql->query($q_str) ){

    while($row = $sql->fetch($q_result)){
		$bom_row['wi'][] = $row;
    }

    $action = $parm['action'];
    $now_num = $parm['now_num'];
    $page_num = $parm['page_num'];
    $ord_num = $parm['ord_num'];
    @$PageTotal = ceil($num_rows / $page_num);
    
    $bom_row['page'] = '
                              <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                <tr>
                                  <td align="left" nowrap width="30%">';
    if ( $now_num > 0 ) {
        $bom_row['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&PHP_fty_sch='.$parm['fty'].'&PHP_mat='.$parm['PHP_mat'].'&PHP_wi_num='.$ord_num.'&M_now_num=0" class="pnum" >最前頁</a> ';
    }
    
    if (($now_num-10)>= 0 ) {
        $bom_row['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&PHP_fty_sch='.$parm['fty'].'&PHP_mat='.$parm['PHP_mat'].'&PHP_wi_num='.$ord_num.'&M_now_num='.($now_num-10).'" class="pnum" >上10頁</a> ';
    }
    
    if ( $now_num > 0 ) {
        $bom_row['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&PHP_fty_sch='.$parm['fty'].'&PHP_mat='.$parm['PHP_mat'].'&PHP_wi_num='.$ord_num.'&M_now_num='.($now_num-1).'" class="pnum" >上一頁</a> ';
    }
    
    $bom_row['page'] .= '</td>
                                  <td align="center" nowrap width="40%">';
    #現在的頁數
    $o=0;
    for ($m=$now_num-2;$m < $PageTotal+1;$m++) {
        if ( $m > 0 and $o < 7){
          $CSS = ( $m == $now_num+1 ) ? 'page_num_on' : 'page_num_off' ;
          $dd = ( $m <> 1 ) ? ' . ' : '' ;
          $bom_row['page'] .= ''.$dd.'<a href="'.$PHP_SELF.'?PHP_action='.$action.'&PHP_fty_sch='.$parm['fty'].'&PHP_mat='.$parm['PHP_mat'].'&PHP_wi_num='.$ord_num.'&M_now_num='.($m-1).'" class="'.$CSS.'" title="'.$m.'">'.$m.'</a> ';
                                    $o++;
        }
    }
    
    $bom_row['page'] .= '</td>
                                  <td align="right" nowrap width="30%">';
                                  
    if ( $PageTotal > ($now_num+1) ) {
        $bom_row['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&PHP_fty_sch='.$parm['fty'].'&PHP_mat='.$parm['PHP_mat'].'&PHP_wi_num='.$ord_num.'&M_now_num='.($now_num+1).'" class="pnum" >下一頁</a> ';
    }
    
    if ( ($now_num+11) <= $PageTotal ) {
        $bom_row['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&PHP_fty_sch='.$parm['fty'].'&PHP_mat='.$parm['PHP_mat'].'&PHP_wi_num='.$ord_num.'&M_now_num='.($now_num+10).'" class="pnum" >下10頁</a> ';
    }
    
    if ( $PageTotal > ($now_num+1) ) {
        $bom_row['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&PHP_fty_sch='.$parm['fty'].'&PHP_mat='.$parm['PHP_mat'].'&PHP_wi_num='.$ord_num.'&M_now_num='.($PageTotal-1).'" class="pnum" >最後頁</a> ';
    }
    
    $bom_row['page'] .= '</td>
                                  <td colspan="3" align="center">';

    $bom_row['page'] .= '</td>
                                </tr>
                              </table>
                            ';
    if( !empty($num_rows) ){
        $bom_row['page_text'] = '
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="num_info">
                          <tr>
                            <td> 第 '. ($now_num+1) .' 頁，總計 '. $PageTotal .' 頁 ( 共 ' . $num_rows . ' 筆資料 ) </td>
                          </tr>
                        </table>
                      ';
    } else {
        $bom_row['page_text'] = '';
    }
    
    $sql->po_disconnect();

    return $bom_row;
    
}
	
	
	########################################################################
	
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_requi_det($po_num,$mat_cat,$color,$size)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_requi_det($fty, $mat_cat, $bom_id) {
	$sql = $this->sql;
	
	$ver = $this->get_ver($fty,$mat_cat);
	
	if($mat_cat == "l"){
		$q_str = "select distinct stock_inventory_link_det.stock_inventory_id, stock_inventory_link_det.bom_id, stock_inventory_link_det.mat_cat, 
						 stock_inventory.bl_num, stock_inventory.po_num, stock_mat_lots.l_no
				 from stock_inventory_link_det join stock_inventory on stock_inventory.id = stock_inventory_link_det.stock_inventory_id
					  left join stock_mat_lots on stock_mat_lots.id = stock_inventory_link_det.stock_mat_id
				 where stock_inventory.`type` = 'i' and stock_inventory_link_det.bom_id = '".$bom_id."' 
						and stock_inventory_link_det.mat_cat = '".$mat_cat."' and stock_mat_lots.ver = '".$ver['ver']."'
					   and stock_inventory.del_mk = 'n'
				 group by stock_inventory.bl_num, stock_inventory.po_num, stock_inventory_link_det.bom_id, stock_mat_lots.l_no
				 order by stock_inventory.bl_num, stock_mat_lots.l_no, stock_mat_lots.r_no";
		
		$q_result = $sql->query($q_str);
		$rtn = array();
		# 為了小計數量 以缸號作區隔
		while($tmp_row = $sql->fetch($q_result)){
			$q_str = "select stock_inventory_link_det.id, stock_inventory_link_det.stock_inventory_id, stock_inventory.qty as rcv_qty, 
							 stock_inventory_link_det.bom_id, stock_inventory_link_det.mat_cat, 
							 stock_inventory.`open_date` as eta, po_supl_ship.ship_date as etd, stock_inventory.bl_num, 
							 stock_inventory.po_num, 
							 stock_mat_lots.qty as remain_qty, stock_mat_lots.l_no, stock_mat_lots.r_no, stock_mat_lots.storage
					 from stock_inventory_link_det join stock_inventory on stock_inventory.id = stock_inventory_link_det.stock_inventory_id
						  left join po_supl_ship on po_supl_ship.id = stock_inventory.ship_id
						  left join stock_mat_lots on stock_mat_lots.id = stock_inventory_link_det.stock_mat_id
					 where stock_inventory.`type` = 'i' and stock_inventory.bl_num = '".$tmp_row['bl_num']."' and stock_inventory.po_num = '".$tmp_row['po_num']."' 
							and stock_inventory_link_det.bom_id = '".$tmp_row['bom_id']."' and stock_inventory.l_no = '".$tmp_row['l_no']."'
							and stock_inventory_link_det.mat_cat = '".$mat_cat."' and stock_mat_lots.ver = '".$ver['ver']."'
						   and stock_inventory.del_mk = 'n'
					 order by stock_mat_lots.l_no, stock_mat_lots.r_no";
			
			$det_rsl = $sql->query($q_str);
			$tmp_row['det'] = array();
			$sum_qty = 0;
			while($row = $sql->fetch($det_rsl)){
				$sum_qty += $row['rcv_qty'];
				$row['require_qty'] = $this->get_pre_set_qty($row['stock_inventory_id']);
				$row['send_qty'] = $this->get_inventory_qty_by_type($fty, "r", $row['stock_inventory_id']);
				$row['remain_qty'] = $row['rcv_qty'] - $row['send_qty'];
				$tmp_row['det'][] = $row;
			}
			$tmp_row['lot_sum_qty'] = $sum_qty;
			$rtn[] = $tmp_row;
			// $row['remain_qty'] = $this->get_stock_qty_by_inventory_id($ver['ver'], $mat_cat, $row['stock_inventory_id']);
			// if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'ming'){
				// $this->re_cal_stock_qty($ver['ver'], $row['bom_id'], $mat_cat, $row['stock_inventory_id'], $row['send_qty']);
			// }
			
		}
	}else{
		$q_str = "select stock_inventory_link_det.id, stock_inventory_link_det.stock_inventory_id, stock_inventory.qty as rcv_qty, 
						 stock_inventory_link_det.bom_id, stock_inventory_link_det.mat_cat, 
						 stock_inventory.`open_date` as eta, po_supl_ship.ship_date as etd, stock_inventory.bl_num, 
						 stock_inventory.po_num, 
						 stock_mat_acc.qty as remain_qty, stock_mat_acc.storage
				 from stock_inventory_link_det join stock_inventory on stock_inventory.id = stock_inventory_link_det.stock_inventory_id
					  left join po_supl_ship on po_supl_ship.id = stock_inventory.ship_id
					  left join stock_mat_acc on stock_mat_acc.id = stock_inventory_link_det.stock_mat_id
				 where stock_inventory.`type` = 'i' and stock_inventory_link_det.bom_id = '".$bom_id."' 
						and stock_inventory_link_det.mat_cat = '".$mat_cat."' and stock_mat_acc.ver = '".$ver['ver']."'
					   and stock_inventory.del_mk = 'n'";
		$q_result = $sql->query($q_str);
		$rtn = array();
		
		while($row = $sql->fetch($q_result)){
			$row['require_qty'] = $this->get_pre_set_qty($row['stock_inventory_id']);
			$row['send_qty'] = $this->get_inventory_qty_by_type($fty, "r", $row['stock_inventory_id']);
			$row['remain_qty'] = $row['rcv_qty'] - $row['send_qty'];
			// $row['remain_qty'] = $row['rcv_qty'] - $row['send_qty'];
			// if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'ming'){
				// $this->re_cal_stock_qty($ver['ver'], $row['bom_id'], $mat_cat, $row['stock_inventory_id'], $row['send_qty']);
			// }
			// $row['remain_qty'] = $this->get_stock_qty_by_inventory_id($ver['ver'], $mat_cat, $row['stock_inventory_id']);
			
			$rtn[] = $row;
		}
	}
	
	
	
	return $rtn;
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_ver($fty,$mat_cat,$ver='')
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ver($fty,$mat_cat,$ver='') {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT * FROM `stock` WHERE `fty` = '".$fty."' AND `mat_cat` = '".$mat_cat."' and `confirm_user` <> '' ORDER BY `ver` DESC LIMIT 0 , 1 ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}
$row = $sql->fetch($q_result);

if ( empty($row) ) {
	$fields = null;
} else {
	$fields = $row;
}

if( $ver ) {
	$fields['ver'] = str_pad( ($fields['ver']+1) , 4, 0, STR_PAD_LEFT);
}

return $fields;

}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_pre_set_qty($mat_cat,$mat_id,$stock_link_id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pre_set_qty($stock_inventory_id, $det_id = '') {
	$sql = $this->sql;
	
	if($det_id) $where_str = ' and id <> '.$det_id." ";
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select sum(qty) as qty
			  from requi_notify_det
			  where stock_inventory_id = '".$stock_inventory_id."'".$where_str." 
			  group by stock_inventory_id";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}
	
	$row = $sql->fetch($q_result);
	if(!$row) $row['qty'] = 0;
	
	$sql->po_disconnect();

	return $row['qty'];
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field($field, $where_str='',$tbl='po_bom')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_field($field, $where_str='',$tbl='po_bom') {
	$sql = $this->sql;
	$row = array();

	$q_str = "SELECT ".$field." FROM ".$tbl." ".$where_str;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	return $row[0];
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_stock_qty($mat_cat, $fty, $mat_id, $color, $size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_stock_qty($stock_inventory_id) {
	$sql = $this->sql;
	
	$q_str = "select qty
			  from stock_inventory
			  where `type` = 'i' and id = ".$stock_inventory_id;
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i_row = $sql->fetch($q_result);
	
	$q_str = "select sum(qty) as qty
			  from stock_inventory
			  where `type` = 'r' and stock_inventory_id = ".$stock_inventory_id."
			  group by stock_inventory_id";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$r_row = $sql->fetch($q_result);
	
	return $i_row['qty'] - $r_row['qty'];
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acceptance_qty($fty, $bl_num, $mat_cat, $mat_id, $color, $size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_acceptance_qty($fty, $bl_num, $r_no, $l_no, $mat_cat, $mat_id, $color, $size, $flag, $det_id) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	if($flag)
		$where_str = " and requi_notify_det.id <> $det_id ";
	
	$q_str = "SELECT sum(requi_notify_det.qty) as qty
			  from requi_notify,requi_notify_det
			  where requi_notify.fty = '".$fty."' and requi_notify_det.bl_num = '".$bl_num."' and requi_notify_det.r_no = '".$r_no."' and
					requi_notify_det.l_no = '".$l_no."' and requi_notify_det.mat_cat = '".$mat_cat."' and requi_notify_det.mat_id = '".$mat_id."' and 
					requi_notify_det.o_color = '".$color."' and requi_notify_det.size = '".$size."' and requi_notify_det.stock_id <> 0 ".$where_str." 
			  group by requi_notify.fty, requi_notify_det.bl_num, requi_notify_det.r_no, requi_notify_det.l_no, requi_notify_det.mat_cat, 
					   requi_notify_det.mat_id, requi_notify_det.color, requi_notify_det.size";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row) $row['qty'] = 0;
	
	$sql->po_disconnect();
	
	return $row['qty'];
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> buy_get_no($hend,$n_field,$tables)	為新單據做編號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_no($hend,$n_field,$tables) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$fields = array();		
	$q_str = "SELECT ". $n_field." FROM ".$tables." where ".$n_field. " like '%".$hend."%' order by ".$n_field." desc limit 1";
	if (!$q_result = $sql->query($q_str)) {		//搜尋最後一筆
		$this->msg->add("Error! 無法存取資料庫!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {	//如果沒有資料的話
		$buy_no = '1';
	
	}else{	//將最後一筆的數字+1
		$long = strlen($hend);
		$buy_no = substr($row[$n_field],$long);	//去掉表頭
		
		settype($buy_no, 'integer');
		$buy_no=$buy_no+1;
		settype($buy_no, 'string');			
	}
	
	if (strlen($buy_no) == 1)	//在數字前補0到達四位數字
	{
		$buy_no=$hend."000".$buy_no;
	}else if(strlen($buy_no) == 2){
		$buy_no=$hend."00".$buy_no;
	}else if(strlen($buy_no) == 3){
		$buy_no=$hend."0".$buy_no;			
	}else{
		$buy_no=$hend.$buy_no;
	}

	$sql->po_disconnect();

	return $buy_no;
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> add_main($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_main($parm) {
	$sql = $this->sql;
	$id = get_insert_id("requi_notify","1");
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "INSERT INTO requi_notify (id, rn_num, ord_num, fty, line, create_user, create_date, status) VALUES('".
							$id."','".
							$parm['rn_num']."','".
							$parm['ord_num']."','".
							$parm['fty']."','".
							$parm['line']."','".
							$parm['create_user']."','".						
							$parm['create_date']."','".	
							$parm['status']."')";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}
	
	$sql->po_disconnect();

	return true;
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> add_det($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_det($parm) {
	$sql = $this->sql;
	$id = get_insert_id("requi_notify_det","1");
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "INSERT INTO requi_notify_det (id, rn_num, po_bom_id, bl_num, po_num, bom_id, mat_cat, mat_id, mat_name, color, o_color, size, r_no, l_no, qty, stock_inventory_id, storage) VALUES('".
							$id."','".
							$parm['rn_num']."','".
							$parm['po_bom_id']."','".
							$parm['bl_num']."','".
							$parm['po_num']."','".
							$parm['bom_id']."','".
							$parm['mat_cat']."','".
							$parm['mat_id']."','".
							$parm['mat_name']."','".
							$parm['color']."','".
							$parm['o_color']."','".
							$parm['size']."','".
							$parm['r_no']."','".
							$parm['l_no']."','".
							$parm['qty']."','".
							$parm['stock_inventory_id']."','".
							$parm['storage']."')";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}
	
	$sql->po_disconnect();

	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_notice_search($sch_parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mat_notice_search($sch_parm) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "SELECT * FROM requi_notify ";
	
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$sch_parm['PHP_action']);
	$srh->add_sort_condition("id DESC");
	$srh->row_per_page = 15;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
		$srh->q_limit = "LIMIT ".$limit_entries." ";
	}
	else{
		$pagesize=10;
		if (isset($sch_parm['PHP_sr_startno']) && $sch_parm['PHP_sr_startno']) {
			$pages = $srh->get_page($sch_parm['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	
	if (strtoupper($sch_parm['rn_num'])){
		$srh->add_where_condition("rn_num like '%".$sch_parm['rn_num']."%'");
	}
	if (strtoupper($sch_parm['fty'])){
		$srh->add_where_condition("fty = '".$sch_parm['fty']."'");
	}
	if (strtoupper($sch_parm['ord_num'])){
		$srh->add_where_condition("ord_num LIKE '%".$sch_parm['ord_num']."%'");
	}
	
	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	$this->msg->merge($srh->msg);
	if (!$result){   // 當查尋無資料時
		$op['record_NONE'] = 1;
	}
	
	$op['mat_notice'] = $result;
	
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;
		
	if(!$limit_entries){ 
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];		
	}	
		
	$sql->po_disconnect();
	
	return $op;
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mat_notice($rn_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_mat_notice($rn_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	# 主檔
	$q_str = "select *
			  from requi_notify
			  where rn_num = '".$rn_num."'";
	$q_result = $sql->query($q_str);
	
	if(!$notice = $sql->fetch($q_result)){
		return false;
	}
	$op['notice'] = $notice;
	
	# 明細
	$op['notice_det'] = array();
	$q_str = "select *
			  from requi_notify_det
			  where rn_num = '".$rn_num."'";
	
	$q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		$op['notice_det'][] = $row;
	}
	
	# log
	$op['notice_log'] = array();
	$q_str = "select *
			  from requi_log
			  where rn_num = '".$rn_num."'";
	
	$q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		$op['notice_log'][] = $row;
	}
	
	$sql->po_disconnect();
	
	return $op;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_log($parm)	更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_log($parm) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$parm['des']=str_replace("'", "\'",$parm['des']);
	$q_str = "INSERT INTO requi_log (rn_num, des, user, k_date) 
			  VALUES('".
						$parm['rn_num']."','".
						$parm['des']."','".
						$parm['user']."','".																													
						$parm['k_date']."')";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
	$sql->po_disconnect();
	
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->submit_notice($parm)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function submit_notice($parm) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "update requi_notify
			  set sub_date = '".$parm['sub_date']."', sub_user = '".$parm['sub_user']."', status = 2
			  where rn_num = '".$parm['rn_num']."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
	$sql->po_disconnect();
	
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_notice($rn_num)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function revise_notice($rn_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "update requi_notify
			  set sub_user = '', sub_date = '0000-00-00 00:00:00', version=version+1, status = 0
			  where rn_num = '".$rn_num."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
	$sql->po_disconnect();
	
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->uncfm_search($sch_parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function uncfm_search($sch_parm) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "SELECT * FROM requi_notify ";
	
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$sch_parm['PHP_action']);
	$srh->add_sort_condition("id DESC");
	$srh->row_per_page = 15;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
		$srh->q_limit = "LIMIT ".$limit_entries." ";
	}
	else{
		$pagesize=10;
		if (isset($sch_parm['PHP_sr_startno']) && $sch_parm['PHP_sr_startno']) {
			$pages = $srh->get_page($sch_parm['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	if ($sch_parm['fty'] == "LY" or $sch_parm['fty'] == "CF"){
		$srh->add_where_condition("fty = '".$sch_parm['fty']."'");
	}
	$srh->add_where_condition("status = 2");
	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	$this->msg->merge($srh->msg);
	if (!$result){   // 當查尋無資料時
		$op['record_NONE'] = 1;
	}
	
	$op['mat_notice'] = $result;
	
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;
		
	if(!$limit_entries){ 
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];		
	}	
		
	$sql->po_disconnect();
	
	return $op;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cfm_notice($parm)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function cfm_notice($parm) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "update requi_notify
			  set cfm_date = '".$parm['cfm_date']."', cfm_user = '".$parm['cfm_user']."', status = 4
			  where rn_num = '".$parm['rn_num']."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
	$sql->po_disconnect();
	
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_num($mat_cat, $bom_id, $color, $size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_num($bom_id, $mat_cat, $mat_id, $color, $size) {
	$sql = $this->sql;
	$row = array();

	$q_str = "SELECT ap.po_num
			  FROM ap, ap_det
			  WHERE ap_det.bom_id = '".$bom_id."' and ap_det.mat_cat = '".$mat_cat."' and ap_det.mat_id = '".$mat_id."'
					and ap_det.color = '".iconv("UTF-8","big5",$color)."' and ap_det.size = '".$size."' and ap.ap_num = ap_det.ap_num";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	
	return $row['po_num'];
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->notice_edit($parm, $line, $rn_num)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function notice_edit($parm, $line, $rn_num) {
	$sql = $this->sql;
	
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	foreach($parm as $id => $qty){
		$q_str = "update requi_notify_det
				  set qty = '".$qty."'
				  where id = '".$id."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't insert.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	}
	
	$q_str = "update requi_notify
				  set line = '".$line."'
				  where rn_num = '".$rn_num."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't insert.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	
	
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->notice_mat_del($parm, $line, $rn_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function notice_mat_del($id, $rn_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "delete from requi_notify_det
			  where id = '".$id."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	if($this->check_notice_mat($rn_num)){
		return true;
	}else{
		return false;
	}
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_notice_mat($rn_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_notice_mat($rn_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select count(id)
			  from requi_notify_det
			  where rn_num = '".$rn_num."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	if($rtn_row = $sql->fetch($q_result)){
		if($rtn_row[0] == 0){
			$q_str = "delete from requi_notify
					  where rn_num = '".$rn_num."' ;
					  delete from requi_log
					  where rn_num = '".$rn_num."' ;";
			
			$q_result = $sql->query($q_str);
		}
	}
	
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_det_lots_view($wi_id, $wi_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function send_det_lots_view($wi_id, $wi_num){
	$sql = $this->sql;
	
	# 原BOM表資料 
	$rtn = array();
	$q_str = "select bom_lots.id as bom_id, bom_lots.qty, bom_lots.o_qty, bom_lots.color as o_color, bom_lots.size, lots.id as lots_id, lots.lots_name as o_lots_name
			  from bom_lots, lots_use, lots
			  where bom_lots.wi_id = ".$wi_id." and bom_lots.lots_used_id = lots_use.id
					and lots.lots_code = lots_use.lots_code
			  order by bom_lots.id asc";
	
	$q_result = $sql->query($q_str);
	while($lots_row = $sql->fetch($q_result)){
		$rtn[] = $lots_row;
	}
	
	# 越南翻譯的資料
	$utf_sql = $this->sql;
	$po_link_id = $utf_sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select fty, bom_id, mat_cat, mat_name, color, qty
			  from po_bom
			  where ord_num = '".$wi_num."' and mat_cat = 'l'
			  order by bom_id asc";
	$q_result = $utf_sql->query($q_str);
	
	while($row = $utf_sql->fetch($q_result)){
		$utf_row[] = $row;
	}
	
	$utf_sql->po_disconnect($po_link_id);
	
	# 合併資料
	$rtn_row_size = sizeof($rtn);
	$utf_row_size = sizeof($utf_row);
	for($i=0;$i<$rtn_row_size;$i++){
		for($j=0;$j<$utf_row_size;$j++){
			if($utf_row[$j]['bom_id'] == $rtn[$i]['bom_id']){
				$rtn[$i]['lots_name'] = $utf_row[$j]['mat_name'];
				$rtn[$i]['color'] = $utf_row[$j]['color'];
				$rtn[$i]['demand_qty'] = $utf_row[$j]['qty'];
				$rtn[$i]['send_qty'] = $this->get_inventory_qty($utf_row[$j]['fty'], "r", "l", $rtn[$i]['bom_id']);
				$rtn[$i]['stock_qty'] = $this->get_stock_total_qty($utf_row[$j]['fty'], "l", $rtn[$i]['lots_id'], $rtn[$i]['o_color'], $rtn[$i]['size']);
				break;
			}
		}
	}
	
	
	return $rtn;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_det_acc_view($wi_id, $wi_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function send_det_acc_view($wi_id, $wi_num){
	$sql = $this->sql;
	
	# 原BOM表資料 
	$rtn = array();
	$q_str = "select bom_acc.id as bom_id, bom_acc.qty, bom_acc.o_qty, bom_acc.color as o_color, bom_acc.size, acc.id as acc_id, acc.acc_name as o_acc_name
			  from bom_acc, acc_use, acc
			  where bom_acc.wi_id = ".$wi_id." and bom_acc.acc_used_id = acc_use.id
					and acc.acc_code = acc_use.acc_code
			  order by bom_acc.id asc";
	
	$q_result = $sql->query($q_str);
	while($acc_row = $sql->fetch($q_result)){
		$rtn[] = $acc_row;
	}
	
	# 越南翻譯的資料
	$utf_sql = $this->sql;
	$po_link_id = $utf_sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select fty, bom_id, mat_cat, mat_name, color, qty
			  from po_bom
			  where ord_num = '".$wi_num."' and mat_cat = 'a'
			  order by bom_id asc";
	$q_result = $utf_sql->query($q_str);
	
	while($row = $utf_sql->fetch($q_result)){
		$utf_row[] = $row;
	}
	
	$utf_sql->po_disconnect($po_link_id);
	
	# 合併資料
	$rtn_row_size = sizeof($rtn);
	$utf_row_size = sizeof($utf_row);
	for($i=0;$i<$rtn_row_size;$i++){
		for($j=0;$j<$utf_row_size;$j++){
			if($utf_row[$j]['bom_id'] == $rtn[$i]['bom_id']){
				$rtn[$i]['acc_name'] = $utf_row[$j]['mat_name'];
				$rtn[$i]['color'] = $utf_row[$j]['color'];
				$rtn[$i]['demand_qty'] = $utf_row[$j]['qty'];
				$rtn[$i]['send_qty'] = $this->get_inventory_qty($utf_row[$j]['fty'], "r", "a", $rtn[$i]['bom_id']);
				$rtn[$i]['stock_qty'] = $this->get_stock_total_qty($utf_row[$j]['fty'], "a", $rtn[$i]['acc_id'], $rtn[$i]['o_color'], $rtn[$i]['size']);
				break;
			}
		}
	}
	
	
	return $rtn;
}


function get_inventory_qty($fty, $type, $mat_cat, $bom_id) {
	$sql = $this->sql;
	
	$q_str = "SELECT sum(qty) as qty
			  from stock_inventory
			  where type = '".$type."' and fty = '".$fty."' and mat_cat = '".$mat_cat."' and bom_id = '".$bom_id."' 
			  group by bom_id";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
		
	return $row['qty'];
}


function get_stock_total_qty($fty, $mat_cat, $mat_id, $color, $size) {
	$sql = $this->sql;
	
	if($mat_cat == "l"){
		$tbl = "stock_mat_lots";
		$mat_field = "lots_id";
		$q_str = "SELECT sum(qty) as qty
			  from ".$tbl."
			  where fty = '".$fty."' and ".$mat_field." = ".$mat_id." 
					and color = '".$color."' and size = '".$size."' 
			  group by ver, fty, ".$mat_field.", color, size
			  order by ver desc limit 0,1";
	}else{
		$tbl = "stock_mat_acc";
		$mat_field = "acc_id";
		$q_str = "SELECT sum(qty) as qty
			  from ".$tbl."
			  where fty = '".$fty."' and ".$mat_field." = ".$mat_id." 
					and color = '".$color."' and size = '".$size."' 
			  group by ver, fty, bl_num, ".$mat_field.", color, size
			  order by ver desc limit 0,1";
	}
	
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row) $row['qty'] = 0;
		
	return $row['qty'];
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_inventory_qty($wi_id, $wi_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_inventory_qty_by_type($fty, $type, $stock_inventory_id) {
	$sql = $this->sql;
	
	$q_str = "SELECT sum(qty) as qty
			  from stock_inventory
			  where `type` = '".$type."' and fty = '".$fty."' and stock_inventory_id = '".$stock_inventory_id."'
			  group by fty, type, stock_inventory_id";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
		
	return $row['qty'];
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_stock_mat($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_stock_mat($stock_inventory_id, $qty){
	$sql = $this->sql;
	
	$q_str = "update stock_mat_acc
			  set qty = ".$qty."
			  where stock_inventory_id = ".$stock_inventory_id;
	
	$q_result = $sql->query($q_str);
		
	return;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_num4set_mat_list($fty, $bl_num, $mat_cat, $mat_id, $color, $size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ord_num4set_mat_list($fty, $begin_date, $end_date) {
	$sql = $this->sql;
	
	$q_str = "SELECT distinct ord_num
			  from schedule
			  where fty = '".$fty."' and ord_num not like 'L%' and rel_ets between '".$begin_date."' and '".$end_date."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}
	
	return $rtn;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_send_num($fty, $bl_num, $mat_cat, $mat_id, $color, $size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_send_num($fty) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "SELECT id, rn_num, ord_num, create_date, status
			  from requi_notify
			  where fty = '".$fty."' 
			  order by id desc";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}
	
	$sql->po_disconnect($po_link_id);
	
	return $rtn;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_stock_qty_by_inventory_id($ver, $mat_cat, $stock_inventory_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_stock_qty_by_inventory_id($ver, $mat_cat, $stock_inventory_id) {
	$sql = $this->sql;
	
	$tbl = $mat_cat == "l" ? "stock_mat_lots" : "stock_mat_acc";
	$q_str = "SELECT sum(qty) as qty
			  from ".$tbl."
			  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id."
			  order by stock_inventory_id";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
	
	return $row['qty'];
}

# 有些舊資料庫存數量有誤，所以重新計算庫存量
function re_cal_stock_qty($ver, $bom_id, $cat, $stock_inventory_id, $qty) {

$sql = $this->sql;

$tbl = substr($cat,0,1) == 'l' ? 'stock_mat_lots' : 'stock_mat_acc' ;

	$q_str = "select this_id
			  from ".$tbl."
			  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;
	
	$this_id_rsl = $sql->query($q_str);
	$this_id_row = $sql->fetch($this_id_rsl);
	
	if(!$this_id_row[0]['this_id']){
		# 重新更新驗收數量
		$q_str = "select ".$tbl.".id, ".$tbl.".link_det_id, stock_inventory_link_det.qty
				  from ".$tbl.", stock_inventory_link_det
				  where ".$tbl.".ver = '".$ver."' and ".$tbl.".stock_inventory_id = ".$stock_inventory_id." and 
						stock_inventory_link_det.id = ".$tbl.".link_det_id";
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)){
			$q_str = "update ".$tbl."
					  set qty = ".$row['qty']."
					  where id = ".$row['id'];
			$jj_rsl = $sql->query($q_str);
		}
		
		# 扣除庫存數量
		$q_str = "select sum(qty) as qty
				  from ".$tbl."
				  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;

		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		if($row['qty'] == $qty){
			$q_str = "update ".$tbl."
					  set qty = 0
					  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;
			
			
			$q_result = $sql->query($q_str);
		}else{
			$q_str = "select id, qty
					  from ".$tbl."
					  where ver = '".$ver."' and bom_id = ".$bom_id." and stock_inventory_id = ".$stock_inventory_id;
			
			
			$q_result = $sql->query($q_str);
			$un_equal_row = $sql->fetch($q_result);
			if($un_equal_row['qty'] >= $qty){
				$q_str = "update ".$tbl."
						  set qty = ".($un_equal_row['qty']-$qty)."
						  where id = ".$un_equal_row['id'];
				
				
				$q_result = $sql->query($q_str);
			}else{
				$tmp_qty = 0;
				$q_str = "update ".$tbl."
						  set qty = 0
						  where id = ".$un_equal_row['id'];
				
				
				$q_result = $sql->query($q_str);
				$tmp_qty += $un_equal_row['qty'];
				
				$q_str = "select id, qty
						  from ".$tbl."
						  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id." 
								and id <> ".$un_equal_row['id']." and qty > 0";
				
				
				$m_result = $sql->query($q_str);
				while($m_row = $sql->fetch($m_result)){
					if( $m_row['qty'] + $tmp_qty <= $qty ){
						$q_str = "update ".$tbl."
							  set qty = 0
							  where id = ".$m_row['id'];
						
						$q_result = $sql->query($q_str);
						$tmp_qty += $m_row['qty'];
					}else{
						$q_str = "update ".$tbl."
							  set qty = qty - ".($qty - $tmp_qty)."
							  where id = ".$m_row['id'];
						
						$q_result = $sql->query($q_str);
						break;
					}
				}
			}
		}
	}


return true;

}

} // end class

?>