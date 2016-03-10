<?php 
// i = 進料，r = 領料，g = 領用，b = 退料，e = 調整，c = 刪除
class  WAREHOUSING {
	
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
# :GET_BL_NUM
function get_bl_num($bl_num,$arrival_active='n',$incoming_active='n') {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT `bl_num` FROM `po_supl_ship` WHERE `bl_num` LIKE '%".$bl_num."%' AND `status` = '1' AND `arrival_active` = '".$arrival_active."' AND `incoming_active` = '".$incoming_active."';";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = $sql->fetch($q_result)) {
	$fields[] = $row['bl_num'];
}
if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_CARRIER_NUM
function get_carrier_num($carrier,$arrival_active='n',$incoming_active='n') {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT `carrier_num` FROM `po_supl_ship` WHERE `carrier_num` LIKE '%".$carrier."%' AND `status` = '1' AND `arrival_active` = '".$arrival_active."' AND `incoming_active` = '".$incoming_active."';";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = $sql->fetch($q_result)) {
	$fields[] = $row['carrier_num'];
}
if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_CARRIER_LIST
function get_carrier_list($dept,$arrival_active='n',$incoming_active='n') {

$sql = $this->sql;

$fields = false;

$q_str = "
SELECT 
*

FROM 
`po_supl_ship` 

WHERE 
`dist` = '".$dept."' AND `status` = '1' AND `arrival_active` = '".$arrival_active."' AND `incoming_active` = '".$incoming_active."'
;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = $sql->fetch($q_result)) {
	$fields[] = $row;
}

if(!empty($fields))
return $fields;
else
return $q_str;

} # FUN END
#
#
#
#
#
#
# :GET_PO_SHIP
function get_po_ship($bl_num='',$carrier='',$is_user='',$active='') {

$sql = $this->sql;

$fields = false;

$arrival_user = ( !empty($is_user) )? 'AND `arrival_user` IS NOT NULL ' : '' ;
$active = ( !empty($active) )? 'AND `arrival_active` = "y" ' : '' ;
$bl_num = ( !empty($bl_num) )? " `bl_num` = '".$bl_num."' " : "" ;
$AND = ( !empty($bl_num) )? " AND " : "" ;
// $carrier = ( !empty($carrier) )? $AND." `carrier_num` = '".$carrier."' " : "" ;

$q_str = "SELECT * FROM `po_supl_ship` WHERE ".$bl_num." AND `status` = '1' ".$arrival_user." ".$active." ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

if( $row = $sql->fetch($q_result)){
	$fields = $row;
}

return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_STOCK_I
function get_stock_i($bl_num='',$carrier='',$fty='') {

$sql = $this->sql;

$bl_num = ( !empty($bl_num) )? " `bl_num` = '".$bl_num."' " : "" ;
$AND = ( !empty($bl_num) )? " AND " : "" ;
$carrier = ( !empty($carrier) )? $AND." `carrier_num` = '".$carrier."' " : "" ;
$AND = ( !empty($fty) && ( !empty($bl_num) || !empty($carrier) ) )? " AND " : "" ;
$fty = ( !empty($fty) )? $AND." `fty` = '".$fty."' " : "" ;
$rtn = array();

$q_str = "SELECT bl_num, po_num, mat_cat, mat_id, color, size, unit , sum(qty) as qty
          FROM stock_inventory_link
          WHERE ".$bl_num."
          GROUP BY bl_num, po_num, mat_cat, mat_id, color , size, unit";

if (!$link_res = $sql->query($q_str)) {
    $_SESSION['MSG'][] = "Error! Can't access database!";
    return false;    
}

while ($link_row = $sql->fetch($link_res)) {
	$mat = $link_row['mat_cat'] == "l" ? "lots" : "acc";
	$q_str = "select ".$mat."_name as mat_name from ".$mat." where id = ".$link_row['mat_id'];
	$mat_res = $sql->query($q_str);
	$mat_row = $sql->fetch($mat_res);
	$link_row['mat_name'] = $mat_row['mat_name'];
	
	# 取訂單號碼
	$q_str = "select id, bl_num, bom_id, mat_cat, ord_num, qty
			  from stock_inventory_link
			  where bl_num = '".$link_row['bl_num']."' and po_num = '".$link_row['po_num']."' and mat_cat = '".$link_row['mat_cat']."' and 
					mat_id = '".$link_row['mat_id']."' and color = '".$link_row['color']."' and size = '".$link_row['size']."' and 
					unit = '".$link_row['unit']."'";
	
	if(!$link_ord_res = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}
	
	while($link_ord_row = $sql->fetch($link_ord_res)){
		$link_ord_row['send_qty'] = $this->get_send_qty4link($link_ord_row['bl_num'], $link_ord_row['mat_cat'], $link_ord_row['bom_id']);
		$link_row['orders'][] = $link_ord_row;
	}
	
	$q_str = "select id, bl_num, po_num, l_no, r_no, qty
			  from stock_inventory
			  where `type` = 'i' and bl_num = '".$link_row['bl_num']."' and po_num = '".$link_row['po_num']."' and 
					mat_cat = '".$link_row['mat_cat']."' and mat_id = '".$link_row['mat_id']."' and color = '".$link_row['color']."' and 
					size = '".$link_row['size']."' and unit = '".$link_row['unit']."'";
	
	if(!$stock_inventory_res = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}
	$i=0;
	while($stock_inventory_row = $sql->fetch($stock_inventory_res)){
		$link_row['link_det'][$i] = $stock_inventory_row;
		$i++;
	}
	
	$rtn[] = $link_row;
	//$row['mk'] = 'del_mk['.$row['id'].']';
    //$row['mk_id'] = 'del_mk_'.$row['id'];
	
}


return $rtn;

} # FUN END
#
#
#
#
#
#
# :GET_PO_SHIP_DET
function get_po_ship_det($id) {

$sql = $this->sql;

$fields = false;

$q_str = "
SELECT DISTINCT 
`po_supl_ship_det`.`po_num` , `po_supl_ship_det`.`invoice_num` , `po_supl_ship_det`.`pi_num` , 
`po_supl_ship_link`.`mat_cat` , `po_supl_ship_link`.`mat_id` , `po_supl_ship_link`.`id` , `po_supl_ship_link`.`color` , `po_supl_ship_link`.`size` , 
`po_supl_ship_link`.`qty` , `po_supl_ship_link`.`po_unit` , `po_supl_ship_link`.`po_supl_ship_id` as `ship_id` , `po_supl_ship_link`.`po_supl_ship_det_id` as `ship_det_id` ,
`po_supl_ship_link`.`c_no` , `po_supl_ship_link`.`r_no` , `po_supl_ship_link`.`l_no` , `po_supl_ship_link`.`gw` , `po_supl_ship_link`.`nw` , `po_supl_ship_link`.`c_o`,`po_supl_ship_link`.`ap_id`
 
FROM 
`po_supl_ship_det` LEFT JOIN `po_supl_ship_link` ON ( `po_supl_ship_det`.`id` = `po_supl_ship_link`.`po_supl_ship_det_id` ) 

WHERE `po_supl_ship_link`.`po_supl_ship_id` = '".$id."' AND `po_supl_ship_det`.`status` = '1' AND `po_supl_ship_link`.`status` = '1'

ORDER BY `po_supl_ship_link`.`ap_id`,`po_supl_ship_link`.`po_supl_ship_det_id`,`po_supl_ship_link`.`color`,`po_supl_ship_link`.`size`,`po_supl_ship_link`.`c_no`,`po_supl_ship_link`.`l_no`,`po_supl_ship_link`.`r_no` ASC
;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}
$po_val = $tmp_row = array();
while ($row = $sql->fetch($q_result)) {
	$row['ap_num'] = str_replace("PO", "PA", $row['po_num']);
	($cat = $row['mat_cat']=='l'?'lots':'acc');
	if ($row['mat_cat']=='l' ){
        $cat = 'lots';
        $str = ' comp as des , specify , width ';
    }else{
        $cat = 'acc';
        $str = ' des ,  specify ';
    }
    
    if( $tmp_row[$row['mat_cat'].$row['mat_id']] ) {
        $mat_name_row = $tmp_row[$row['mat_cat'].$row['mat_id']];
    } else {
        $q_str = "select ".$cat."_name as mat_name , ".$str."
                  from ".$cat."
                  where id =".$row['mat_id'];
        // echo $q_str.'<br>';
        $mat_name_rel = $sql->query($q_str);
        $mat_name_row = $sql->fetch($mat_name_rel);
        $tmp_row[$row['mat_cat'].$row['mat_id']] = $mat_name_row;
    }
    
    $row['mat_name'] = $mat_name_row['mat_name'];
    $row['des'] = $mat_name_row['des'];
    $row['specify'] = $mat_name_row['specify'];
    $row['width'] = $row['mat_cat']=='l'?$mat_name_row['width']:'';
    
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['invoice_num'] = $row['invoice_num'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['pi_num'] = $row['pi_num'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['mat_name'] = $row['mat_name'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['des'] = $row['des'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['specify'] = $row['specify'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['width'] = $row['width'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['color'] = $row['color'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['size'] = $row['size'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['unit'] = $row['po_unit'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['mat_id'] = $row['mat_id'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['po_num'] = $row['po_num'];
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['mat_cat'] = $row['mat_cat'];
    
    
    $po_det = str_replace(" ","",$row['ap_id'].$row['color'].$row['size']);
    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['po_det'] = $po_det;

    $po_val[$row['po_num']][$row['mat_id'].$row['color'].$row['size']]['qty'] += $row['qty'];
    
	// $q_strs = "
	// SELECT 
	// `bom_".$cat."`.`color` , `bom_".$cat."`.`size` , `bom_".$cat."`.`wi_id` , `bom_".$cat."`.`".$cat."_used_id` , `bom_".$cat."`.`wi_id` ,
	// `wi`.`wi_num` , 
	// `".$cat."_use`.`".$cat."_name` , `".$cat."_use`.`use_for` 

	// FROM 
	// `bom_".$cat."` , `wi` , `".$cat."_use`

	// WHERE 
	// `bom_".$cat."`.`id` = '".$row['bom_id']."' AND
	// `bom_".$cat."`.`wi_id` = `wi`.`id` AND
	// `".$cat."_use`.`id` = `bom_".$cat."`.`".$cat."_used_id`
	
	// ;";
	
	// $q_results = $sql->query($q_strs);
	// $rows = $sql->fetch($q_results);
	// $row['detail'] = $rows[$cat.'_name'].':'.$rows['use_for'];

	// $row['qty'] = round($row['qty']);


	// $q_strss = "SELECT sum(`qty`) as `qty` FROM `stock_inventory` WHERE `ship_link_id` = '".$row['id']."' ;";
	
	// $q_resultss = $sql->query($q_strss);
	// $rowss = $sql->fetch($q_resultss);
	
	// $row['remain'] = round($row['qty']) - $rowss['qty'];
	// $row['received'] = $rowss['qty'];
	$fields[$po_det][] = $row;
}
// print_r($po_val);
$fields['po_val'] = $po_val;
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_PO_SHIP_DET
function get_po_ship_det222222($id) {

$sql = $this->sql;

$fields = false;

$q_str = "
SELECT DISTINCT 
`po_supl_ship_det`.`po_num` , `po_supl_ship_det`.`invoice_num` , `po_supl_ship_det`.`pi_num` , 
`po_supl_ship_link`.`mat_cat` , `po_supl_ship_link`.`mat_id` , `po_supl_ship_link`.`id` , `po_supl_ship_link`.`color` , `po_supl_ship_link`.`size` , 
`po_supl_ship_link`.`qty` , `po_supl_ship_link`.`po_unit` , `po_supl_ship_link`.`po_supl_ship_id` as `ship_id` , `po_supl_ship_link`.`po_supl_ship_det_id` as `ship_det_id` ,
`po_supl_ship_link`.`c_no` , `po_supl_ship_link`.`r_no` , `po_supl_ship_link`.`l_no` , `po_supl_ship_link`.`gw` , `po_supl_ship_link`.`nw` , `po_supl_ship_link`.`c_o`
 
FROM 
`po_supl_ship_det` LEFT JOIN `po_supl_ship_link` ON ( `po_supl_ship_det`.`id` = `po_supl_ship_link`.`po_supl_ship_det_id` ) 

WHERE `po_supl_ship_link`.`po_supl_ship_id` = '".$id."' AND `po_supl_ship_det`.`status` = '1' AND `po_supl_ship_link`.`status` = '1'

ORDER BY `po_supl_ship_link`.`c_no`,`po_supl_ship_link`.`l_no`,`po_supl_ship_link`.`r_no` ASC
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

while ($row = $sql->fetch($q_result)) {
	$row['ap_num'] = str_replace("PO", "PA", $row['po_num']);
	($cat = $row['mat_cat']=='l'?'lots':'acc');
    
	$q_str = "select ".$cat."_name as mat_name
			  from ".$cat."
			  where id =".$row['mat_id'];
	$mat_name_rel = $sql->query($q_str);
	$mat_name_row = $sql->fetch($mat_name_rel);
	$row['mat_name'] = $mat_name_row['mat_name'];
	
	$q_strs = "
	SELECT 
	`bom_".$cat."`.`color` , `bom_".$cat."`.`size` , `bom_".$cat."`.`wi_id` , `bom_".$cat."`.`".$cat."_used_id` , `bom_".$cat."`.`wi_id` ,
	`wi`.`wi_num` , 
	`".$cat."_use`.`".$cat."_name` , `".$cat."_use`.`use_for` 

	FROM 
	`bom_".$cat."` , `wi` , `".$cat."_use`

	WHERE 
	`bom_".$cat."`.`id` = '".$row['bom_id']."' AND
	`bom_".$cat."`.`wi_id` = `wi`.`id` AND
	`".$cat."_use`.`id` = `bom_".$cat."`.`".$cat."_used_id`
	
	;";
	
	$q_results = $sql->query($q_strs);
	$rows = $sql->fetch($q_results);
	$row['detail'] = $rows[$cat.'_name'].':'.$rows['use_for'];

	$row['qty'] = round($row['qty']);


	$q_strss = "SELECT sum(`qty`) as `qty` FROM `stock_inventory` WHERE `ship_link_id` = '".$row['id']."' ;";
	
	$q_resultss = $sql->query($q_strss);
	$rowss = $sql->fetch($q_resultss);
	
	$row['remain'] = round($row['qty']) - $rowss['qty'];
	$row['received'] = $rowss['qty'];
	$fields[] = $row;
}

return $fields;

} # FUN END
#
#
#
#
#
#
# :get_ver
function get_ver($fty,$mat_cat,$ver='') {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT * FROM `stock` WHERE `fty` = '".$fty."' AND `mat_cat` = '".substr($mat_cat,0,1)."' ORDER BY `ver` DESC LIMIT 0 , 1 ;";
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

} # FUN END
#
#
#
#
#
#
# :chk_ver
function chk_ver($fty,$mat_cat) {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT * FROM `stock` WHERE `fty` = '".$fty."' AND `mat_cat` = '".$mat_cat."' AND ( `confirm_user` = '' || `confirm_user` IS NULL ) ORDER BY `ver` ;";


if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = $sql->fetch($q_result)) {
	$fields[] = $row;
}

if ( empty($fields) ) {
	return false;
} else {
	return $fields;
}

# true = safe

} # FUN END
#
#
#
#
#
#
# :append_arrival
function append_arrival($ship_id) {

$sql = $this->sql;

$q_str = "UPDATE `po_supl_ship` SET `arrival_date` = NOW() , `arrival_user` = '".$_SESSION['USER']['ADMIN']['id']."' , `arrival_active` = 'y' WHERE `id` = '".$ship_id."' ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :reply_arrival
function reply_arrival($ship_id) {

$sql = $this->sql;

$fields = false;

$q_str = "UPDATE `po_supl_ship` SET `arrival_date` = NULL , `arrival_user` = NULL , `arrival_active` = 'n' WHERE `id` = '".$ship_id."' ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :append_incoming_qty
function append_incoming_qty($type,$get) {

$sql = $this->sql;

$active = 0;

$stock_id = $this->get_stock_id();

foreach($get['order_qty'] as $det_id => $ord_link_val){
    
    $ver = $this->get_ver( $get['fty'] , $get['mat_cat'][$det_id] );
    
	if( $get['po_ship_link_qty'][$det_id] > 0 ){
        
		# stock_inventory_link
		// foreach($ord_link_val as $ord_num => $ord_link_qty){
			// $this_ord_ary = explode("_", $ord_num);
			// $link_parm = array(
                // "stock_id"		=> $stock_id,
                // "ver"		    => $ver['ver'],
                // "bl_num"		=> $get['bl_num'],
                // "po_num"		=> $get['po_num'][$det_id],
                // "ap_det_id"		=> $get['ap_det_id'][$det_id][$ord_num],
                // "bom_id"		=> $get['bom_id'][$det_id][$ord_num],
                // "mat_cat"		=> $get['mat_cat'][$det_id],
                // "mat_id"		=> $get['mat_id'][$det_id],
                // "color"			=> $get['color'][$det_id],
                // "size"			=> $get['size'][$det_id],
                // "unit"			=> $get['unit'][$det_id],
                // "ord_num"		=> $this_ord_ary[0],
                // "qty"			=> $ord_link_qty
            // );
			// $t = $this->append_stock_inventory_link($link_parm);
		// }
		
		# stock_inventory
		foreach($get['link_order_qty'][$det_id] as $key => $ord_link_det_qty){
			$stock_inventory_id = get_insert_id('stock_inventory');
			if($stock_inventory_id){

				$q_str = "
				INSERT INTO `stock_inventory` SET 
				`id` = '".$stock_inventory_id."' , 
				`stock_id` = '".$stock_id."' , 
				`ver` = '".$ver['ver']."' , 
				`type` = '".$type."' , 
				`fty` = '".$get['fty']."' , 
				`dept` = '".$_SESSION['USER']['ADMIN']['dept']."' , 
				`open_user` = '".$_SESSION['USER']['ADMIN']['id']."' , 
				`open_date` = NOW() , 
				`color` = '".$get['color'][$det_id]."' , 
				`size` = '".$get['size'][$det_id]."' , 
				`qty` = '".$ord_link_det_qty."' , 
				`r_no` = '".$get['r_no'][$det_id][$key]."' , 
				`l_no` = '".$get['l_no'][$det_id][$key]."' ,
				`unit` = '".$get['unit'][$det_id]."' , 
				`invoice_num` = '".$get['invoice_num'][$det_id]."' , 
				`pi_num` = '".$get['pi_num'][$det_id]."' , 
				`bl_num` = '".$get['bl_num']."' , 
				`carrier_num` = '".$get['carrier_num']."' , 
				`ord_num` = '' , 
				`ship_id` = '".$get['ship_id']."' , 
				`ship_det_id` = '".$get['ship_det_id'][$det_id]."' , 
				`ship_link_id` = '".$get['ship_link_id'][$det_id][$key]."' , 
				`po_num` = '".$get['po_num'][$det_id]."' , 
				`mat_cat` = '".$get['mat_cat'][$det_id]."' , 
				`mat_id` = '".$get['mat_id'][$det_id]."' , 
				`bom_id` = '' , 
				`remark` = '' 
				;";
                // echo $q_str.'<br>';
                
				if (!$q_result = $sql->query($q_str)) {
					$_SESSION['MSG'][] = "Error! Can't access database!";
					return false;
				}
				
				

				# stock_mat_acc & lots
				$stock_parm = array(
                    "mat_cat"	            => $get['mat_cat'][$det_id],
                    "ver"		            => $ver['ver'],
                    "fty"		            => $get['fty'],
                    "stock_id"              => $stock_id,
                    "stock_inventory_id"    => $stock_inventory_id,
                    "mat_id"	            => $get['mat_id'][$det_id],
                    "color"		            => $get['color'][$det_id],
                    "size"		            => $get['size'][$det_id],
                    "r_no"		            => $get['r_no'][$det_id][$key],
                    "l_no"		            => $get['l_no'][$det_id][$key],
                    "qty"		            => $ord_link_det_qty,
                    "unit"		            => $get['unit'][$det_id],
                    "bl_num"	            => $get['bl_num'],
                    "po_num"	            => $get['po_num'][$det_id],
                    "storage"	            => $get['storage'][$det_id][$key]
                );
				$stock_mat_id = $this->append_cat_det($stock_parm);

				# stock_inventory_det
				foreach($get['link_det_order_qty'][$det_id][$key] as $link_det_ord_num => $link_det_qty){
					$this_ord_ary = explode("_", $link_det_ord_num);
					$link_det_parm = array(
                        "stock_id"					=> $stock_id,
                        "ver"					    => $ver['ver'],
                        "bl_num"					=> $get['bl_num'],
                        "po_num"	                => $get['po_num'][$det_id],
                        "stock_inventory_id"        => $stock_inventory_id,
                        "ap_det_id"					=> $get['ap_det_id'][$det_id][$link_det_ord_num],
                        "bom_id"					=> $get['bom_id'][$det_id][$link_det_ord_num],
                        "mat_cat"					=> $get['mat_cat'][$det_id],
                        "mat_id"					=> $get['mat_id'][$det_id],
                        "color"						=> $get['color'][$det_id],
                        "size"						=> $get['size'][$det_id],
                        "unit"						=> $get['unit'][$det_id],
                        "ord_num"					=> $this_ord_ary[0],
                        "qty"						=> $link_det_qty,
                        "stock_mat_id"				=> $stock_mat_id
                    );
					$this->append_stock_inventory_det($link_det_parm);
				}
				$active++;
			}
		}
	}
}

if( $active > 0 ){
	$this->up_po_ship_active($get['bl_num'],$get['carrier_num']);
	return true;
} else {
	return false;
}



} # FUN END
#
#
#
#
#
#
# :get_insert_id
function get_insert_id() {

$sql = $this->sql;

$q_str = "SELECT `id` FROM `stock_inventory` ORDER BY `id` DESC LIMIT 0 , 1 ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

$row = $sql->fetch($q_result);

if ( empty($row) ) {
	$id = 0;
} else {
	$id = substr($row['id'],-6,6);
}

return date('ym').str_pad( ($id+1) , 6, 0, STR_PAD_LEFT);

} # FUN END
#
#
#
#
#
#
# :get_stock_id
function get_stock_id() {

$sql = $this->sql;

$q_str = "SELECT `stock_id` FROM `stock_inventory` ORDER BY `stock_id` DESC LIMIT 0 , 1 ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

$row = $sql->fetch($q_result);

if ( empty($row) ) {
	$id = 0;
} else {
	$id = substr($row['stock_id'],-6,6);
}

return date('ym').str_pad( ($id+1) , 6, 0, STR_PAD_LEFT);

} # FUN END
#
#
#
#
#
#
# :append_cat_det
function append_cat_det($parm) {

$sql = $this->sql;

$cat = substr($parm['mat_cat'],0,1) == 'l' ? 'lots' : 'acc' ;

if( $cat =="acc" ){
	
    $q_str = "
    INSERT INTO `stock_mat_".$cat."` SET 
    `ver` = '".$parm['ver']."' , 
    `fty` = '".$parm['fty']."' , 
    `stock_id` = '".$parm['stock_id']."' , 
    `stock_inventory_id` = '".$parm['stock_inventory_id']."' , 
    `".$cat."_id` = '".$parm['mat_id']."' , 
    `color` = '".$parm['color']."' , 
    `size` = '".$parm['size']."' , 
    `qty` = '".$parm['qty']."' , 
    `unit` = '".$parm['unit']."' , 
    `bl_num` = '".$parm['bl_num']."' ,
    `po_num` = '".$parm['po_num']."' ,
    `storage` = '".$parm['storage']."' 
    ;";
    
    if (!$q_result = $sql->query($q_str)) {
        $_SESSION['MSG'][] = "Error! Can't access database!";
        return false;
    }
}else{

    $q_str = "
    INSERT INTO `stock_mat_".$cat."` SET 
    `ver` = '".$parm['ver']."' , 
    `fty` = '".$parm['fty']."' , 
    `stock_id` = '".$parm['stock_id']."' , 
    `stock_inventory_id` = '".$parm['stock_inventory_id']."' , 
    `".$cat."_id` = '".$parm['mat_id']."' , 
    `color` = '".$parm['color']."' , 
    `size` = '".$parm['size']."' , 
    `r_no` = '".$parm['r_no']."' , 
    `l_no` = '".$parm['l_no']."' , 
    `qty` = '".$parm['qty']."' , 
    `unit` = '".$parm['unit']."' , 
    `bl_num` = '".$parm['bl_num']."' ,
    `po_num` = '".$parm['po_num']."' ,
    `storage` = '".$parm['storage']."' 
    ;";
    
    if (!$q_result = $sql->query($q_str)) {
        $_SESSION['MSG'][] = "Error! Can't access database!";
        return false;
    }
}
// echo $q_str.'<br>';
$id = $sql->insert_id();
return $id;

} # FUN END
#
#
#
#
#
#
# :chk_po_ship_active
function chk_po_ship_active($ship_id) {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT `id`,`qty` FROM `po_supl_ship_link` WHERE `po_supl_ship_id` = '".$ship_id."' ;";



if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

$chk_qty = 0;
while ($row = $sql->fetch($q_result)) {

    $q_strs = "SELECT `id`,`qty` FROM `stock_inventory` WHERE `ship_link_id` = '".$row['id']."' ;";
    
    if (!$q_results = $sql->query($q_strs)) {
        $_SESSION['MSG'][] = "Error! Can't access database!";
        return false;    
    }
    $fields = $sql->fetch($q_results);
    $rg = $row['qty'] / $fields['qty'].'<br>';
    
    
    if( $rg < 0.98 ){
        $chk_qty++;
    }
    
}

$active = ( $chk_qty == 0 ) ? 'y' : 'n' ;

$q_str = "UPDATE `po_supl_ship` SET `incoming_active` = '".$active."' WHERE `id` = '".$ship_id."' ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :up_po_ship_active
function up_po_ship_active($bl_num,$carrier_num,$active='y') {

$sql = $this->sql;

$fields = false;

$q_str = "UPDATE `po_supl_ship` SET `incoming_active` = '".$active."' WHERE `bl_num` = '".$bl_num."' AND `carrier_num` = '".$carrier_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :GET_ORDER_NUM
function get_order_num($order_num) {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT `order_num` FROM `s_order` WHERE `order_num` LIKE '%".$order_num."%' ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = $sql->fetch($q_result)) {
	$fields[] = $row['order_num'];
}

if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_ORDER_FTY
function get_order_fty($order_num) {

$sql = $this->sql;

$fields = false;

$q_str = "SELECT `fty` FROM `requi_notify` WHERE `ord_num` LIKE '%".$order_num."%' AND `status` = '4' ORDER BY `requi_notify`.`id` DESC limit 0,1;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = $sql->fetch($q_result)) {
	$fields = $row['fty'];
}

if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_ORDER_BOM
function get_order_bom($rn_num, $mat_cat='', $ver='') {

	global $MySQL;

	$rtn = false;

	$Utf8 = $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

	# 發料明細表 主檔
	$q_str = "select *
			  from `requi_notify`
			  where `id` = '".$rn_num."'";

	if (!$q_result = $MySQL->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}

	$row = $MySQL->fetch($q_result);
	$rtn['notice'] = $row;

	# 發料明細表 明細
	$q_str = "select `requi_notify_det`.*,`po_bom`.* , `requi_notify_det`.`qty` as `det_qty`
			  from `requi_notify_det` , `po_bom`
			  where `requi_notify_det`.`po_bom_id` = `po_bom`.`id` AND `requi_notify_det`.`rn_num` = '".$rn_num."' AND `po_bom`.`mat_cat` = '". substr($mat_cat,0,1)."'";
    // echo $q_str.'<br>';
	if (!$q_result = $MySQL->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}

	while($row = $MySQL->fetch($q_result)){
		$rtn['notice_det'][] = $row;
	}

	$MySQL->po_disconnect();

	$sql = $this->sql;
// print_r($rtn);
	# order
	$q_str = "
	SELECT `order_num` , `dept` , `cust` , `style` , `qty` , `unit` , `etd` , `smpl_ord` , `ref` , `factory` , `status` , `size` , `line_sex` , `cust_po` 
	FROM `s_order` 
	WHERE `order_num` = '".$rtn['notice']['ord_num']."' ;";

	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;
	}
	$fields = $sql->fetch($q_result);

	# size
	// $q_str = "SELECT `size`,`size_scale`,`base_size` FROM `size_des` WHERE `id` = '".$fields['size']."' ;";
	// if (!$q_result = $sql->query($q_str)) {
		// $_SESSION['MSG'][] = "Error! Can't access database!";
		// return false;    
	// }
	// $size_des = $sql->fetch($q_result);
	// $size['size_des'] = $size_des;
	// $fields = array_merge($fields, $size);
    $rtn['s_order'] = $fields;

	// if(!empty($rtn))
	// return $rtn;

	# wi
	// $q_str = "SELECT `id` FROM `wi` WHERE `wi_num` = '".$rtn['notice']['ord_num']."' ;";
	// if (!$q_result = $sql->query($q_str)) {
		// $_SESSION['MSG'][] = "Error! Can't access database!";
		// return false;    
	// }
	// $wi = $sql->fetch($q_result);
	// $rtn['wi_id'] = $wi['id'];
	 
	# wiqty
	// $q_str = "SELECT `id` as `wiqty_id` , `colorway` , `qty` , `p_id` FROM `wiqty` WHERE `wi_id` = '".$wi['id']."' ;";
	// if (!$q_result = $sql->query($q_str)) {
		// $_SESSION['MSG'][] = "Error! Can't access database!";
		// return false;    
	// }
	// while ($wiqty = $sql->fetch($q_result)) {
		// $rtn['wiqty'][] = $wiqty;
	// }

	$lots_ver = $this->get_ver( $rtn['notice']['fty'] , "lots" );
	$acc_ver = $this->get_ver( $rtn['notice']['fty'] , "acc" );
	$det_size = sizeof($rtn['notice_det']);
	for($i=0;$i<$det_size;$i++){
		if($rtn['notice_det'][$i]['mat_cat'] == "l"){
			// $ver = ( $lots_ver )? " stock_mat_lots.`ver` = '".$lots_ver['ver']."' and " : "";
			// $q_str = "SELECT 
					// `lots`.`lots_code` as `mat_code` , `lots`.`lots_name` , `lots`.`comp` , `lots`.`specify` , `lots`.`width`, 
					// `lots_use`.`use_for` , `lots_use`.`est_1` , `lots_use`.`unit` , 
					// `bom_lots`.`o_qty` as `bom_qty` 
					
					// FROM 
					// `lots`, `lots_use`, `bom_lots`
					
					// WHERE 

					 // `lots_use`.`lots_code` = `lots`.`lots_code` and 
					 // `bom_lots`.`lots_used_id` = `lots_use`.`id` and 
					// `bom_lots`.`wi_id` = '".$wi['id']."' AND 
					// `bom_lots`.`dis_ver` = '0' AND 
					// `bom_lots`.`id` = '".$rtn['notice_det'][$i]['bom_id']."' 

					// ;";
			
			// if (!$q_result = $sql->query($q_str)) {
				// $_SESSION['MSG'][] = "Error! Can't access database!";
				// return false;    
			// }

			// $row = $sql->fetch($q_result);
			
			// $row['use_for'] = iconv("big5","utf-8",$row['use_for']);
			// $row['receive_qty'] = $this->get_inventory_qty_by_type($rtn['notice']['fty'], "i", $rtn['notice_det'][$i]['stock_inventory_id']);
			$row['send_qty'] = $this->get_inventory_qty_by_i_id($rtn['notice']['fty'], "r", $rtn['notice_det'][$i]['po_bom_id']);
			$row['stock_qty'] = $this->get_stock_qty($lots_ver['ver'], $rtn['notice_det'][$i]['mat_cat'],$rtn['notice_det'][$i]['mat_id'],$rtn['notice_det'][$i]['o_color'],$rtn['notice_det'][$i]['size']);
			$row['unclaimed_qty'] = $rtn['notice_det'][$i]['det_qty'] - $row['send_qty'] <= $row['stock_qty'] ? $rtn['notice_det'][$i]['det_qty'] - $row['send_qty'] : $row['stock_qty'];
			// if($row['unclaimed_qty']<0) $row['unclaimed_qty']=0;
			
			$rtn['notice_det'][$i] = array_merge($rtn['notice_det'][$i], $row);
		}else{
			// $ver = ( $acc_ver )? " stock_mat_acc.`ver` = '".$acc_ver['ver']."' and " : "";
			$q_str = "SELECT 
					`acc`.`acc_code` as `mat_code`, `acc`.`acc_name` , `acc`.`des` , `acc`.`specify` , 
					`acc_use`.`use_for` , `acc_use`.`est_1` , `acc_use`.`unit` , 
					`bom_acc`.`o_qty` as `bom_qty`
					
					FROM 
					`acc`, `acc_use`,`bom_acc`
					
					WHERE 
					
					`acc_use`.`acc_code` = `acc`.`acc_code` and 
					`bom_acc`.`acc_used_id` = `acc_use`.`id` and 
					`bom_acc`.`wi_id` = '".$wi['id']."' AND 
					`bom_acc`.`dis_ver` = '0' AND 
					`bom_acc`.`id` = '".$rtn['notice_det'][$i]['bom_id']."' 
					
					;";
			
			if (!$q_result = $sql->query($q_str)) {
				$_SESSION['MSG'][] = "Error! Can't access database!";
				return false;    
			}

			$row = $sql->fetch($q_result);
			$row['use_for'] = iconv("big5","utf-8",$row['use_for']);
			
			$row['receive_qty'] = $this->get_inventory_qty_by_type($rtn['notice']['fty'], "i", $rtn['notice_det'][$i]['stock_inventory_id']);
			$row['send_qty'] = $this->get_inventory_qty_by_i_id($rtn['notice']['fty'], "r", $rtn['notice_det'][$i]['id']);
			$row['stock_qty'] = $this->get_stock_qty_by_inventory_id($acc_ver['ver'], $rtn['notice_det'][$i]['mat_cat'], $rtn['notice_det'][$i]['stock_inventory_id']);
			$row['unclaimed_qty'] = $rtn['notice_det'][$i]['det_qty'] - $row['send_qty'] <= $row['stock_qty'] ? $rtn['notice_det'][$i]['det_qty'] - $row['send_qty'] : $row['stock_qty'];
			if($row['unclaimed_qty']<0) $row['unclaimed_qty']=0;
			
			$rtn['notice_det'][$i] = array_merge($rtn['notice_det'][$i], $row);
		}
	}

	if(!empty($rtn))
	return $rtn;

} # FUN END
#
#
#
#
#
# :GET_ORDER_BOM
function get_order_bom4adjust($rn_num, $ver='') {

global $MySQL;



$rtn = false;

$Utf8 = $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

# 發料明細表 主檔
$q_str = "select *
		  from requi_notify
		  where id = '".$rn_num."'";

if (!$q_result = $MySQL->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

$row = $MySQL->fetch($q_result);
$rtn['notice'] = $row;

# 發料明細表 明細
$q_str = "select *
		  from requi_notify_det
		  where rn_num = '".$rn_num."'";

if (!$q_result = $MySQL->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while($row = $MySQL->fetch($q_result)){
	$rtn['notice_det'][] = $row;
}

$MySQL->po_disconnect();

$sql = $this->sql;

# order
$q_str = "
SELECT `order_num` , `dept` , `cust` , `style` , `qty` , `unit` , `etd` , `smpl_ord` , `ref` , `factory` , `status` , `size` , `line_sex` , `cust_po` 
FROM `s_order` 
WHERE `order_num` = '".$rtn['notice']['ord_num']."' ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}
$fields = $sql->fetch($q_result);

# size
$q_str = "SELECT `size`,`size_scale`,`base_size` FROM `size_des` WHERE `id` = '".$fields['size']."' ;";
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}
$size_des = $sql->fetch($q_result);
$size['size_des'] = $size_des;
$fields = array_merge($fields, $size);

$rtn['s_order'] = $fields;

# wi
$q_str = "SELECT `id` FROM `wi` WHERE `wi_num` = '".$rtn['notice']['ord_num']."' ;";
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}
$wi = $sql->fetch($q_result);
$rtn['wi_id'] = $wi['id'];
 
# wiqty
$q_str = "SELECT `id` as `wiqty_id` , `colorway` , `qty` , `p_id` FROM `wiqty` WHERE `wi_id` = '".$wi['id']."' ;";
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}
while ($wiqty = $sql->fetch($q_result)) {
	$rtn['wiqty'][] = $wiqty;
}

$lots_ver = $this->get_ver( $rtn['notice']['fty'] , "lots" );
$acc_ver = $this->get_ver( $rtn['notice']['fty'] , "acc" );
$det_size = sizeof($rtn['notice_det']);
for($i=0;$i<$det_size;$i++){
	if($rtn['notice_det'][$i]['mat_cat'] == "l"){
		$ver = ( $lots_ver )? " stock_mat_lots.`ver` = '".$lots_ver['ver']."' and " : "";
		$q_str = "SELECT 
				`lots`.`lots_name` , `lots`.`comp` , `lots`.`specify` , `lots`.`width`, 
				`lots_use`.`use_for` , `lots_use`.`est_1` , `lots_use`.`unit` , 
				`stock_mat_lots`.`qty` as `stock_qty` , `bom_lots`.`o_qty` as `bom_qty` , 
				sum(`stock_inventory`.`qty`) as `receive_qty`

				FROM 
				`lots` 
				LEFT JOIN `lots_use` ON ( `lots_use`.`lots_code` = `lots`.`lots_code` ) 
				LEFT JOIN `bom_lots` ON ( `bom_lots`.`lots_used_id` = `lots_use`.`id` ) 
				LEFT JOIN `stock_mat_lots` ON ( ".$ver." `stock_mat_lots`.`color` = `bom_lots`.`color` AND `stock_mat_lots`.`size` = `bom_lots`.`size` AND `stock_mat_lots`.`lots_id` = `lots`.`id` AND `stock_mat_lots`.`fty` = '".$rtn['notice']['fty']."')
				LEFT JOIN `stock_inventory` ON ( `bom_lots`.`id` = `stock_inventory`.`bom_id` AND `stock_inventory`.`mat_cat` = 'l' AND `stock_inventory`.`type` = 'r' AND `stock_inventory`.`notify_det_id` = ".$rtn['notice_det'][$i]['id'].") 
				
				WHERE 

				`bom_lots`.`wi_id` = '".$wi['id']."' AND 
				`bom_lots`.`dis_ver` = '0' AND 
				`bom_lots`.`id` = '".$rtn['notice_det'][$i]['bom_id']."' and 
				stock_mat_lots.l_no = stock_inventory.l_no and 
				stock_mat_lots.r_no = stock_inventory.r_no 
				
				
				GROUP BY `bom_lots`.`id` ASC
				;";
		
		if (!$q_result = $sql->query($q_str)) {
			$_SESSION['MSG'][] = "Error! Can't access database!";
			return false;    
		}

		$row = $sql->fetch($q_result);
		$row['use_for'] = iconv("big5","utf-8",$row['use_for']);
		$row['receive_qty'] == '' ? 0 : $row['receive_qty'];
		$row['unclaimed_qty'] = $rtn['notice_det'][$i]['qty'] - $row['receive_qty'];
		$stock_row = $this->get_stock_qty($rtn['notice']['fty'],$rtn['notice_det'][$i]['bl_num'],$rtn['notice_det'][$i]['r_no'],$rtn['notice_det'][$i]['l_no'],$rtn['notice_det'][$i]['mat_cat'], $rtn['notice_det'][$i]['mat_id'], $rtn['notice_det'][$i]['o_color'], $rtn['notice_det'][$i]['size']);
		$row['stock_qty'] = $stock_row['qty'];
		$row['unit'] = $stock_row['unit'];
		$rtn['notice_det'][$i] = array_merge($rtn['notice_det'][$i], $row);
	}else{
		$ver = ( $acc_ver )? " stock_mat_acc.`ver` = '".$acc_ver['ver']."' and " : "";
		$q_str = "SELECT 
				`acc`.`acc_name` , `acc`.`des` , `acc`.`specify` , 
				`acc_use`.`use_for` , `acc_use`.`est_1` , `acc_use`.`unit` , 
				`stock_mat_acc`.`qty` as `stock_qty` , `bom_acc`.`o_qty` as `bom_qty` , 
				sum(`stock_inventory`.`qty`) as `receive_qty`

				FROM 
				`acc` 
				LEFT JOIN `acc_use` ON ( `acc_use`.`acc_code` = `acc`.`acc_code` ) 
				LEFT JOIN `bom_acc` ON ( `bom_acc`.`acc_used_id` = `acc_use`.`id` ) 
				LEFT JOIN `stock_mat_acc` ON ( ".$ver." `stock_mat_acc`.`color` = `bom_acc`.`color` AND `stock_mat_acc`.`size` = `bom_acc`.`size` AND `stock_mat_acc`.`acc_id` = `acc`.`id` AND `stock_mat_acc`.`fty` = '".$rtn['notice']['fty']."')
				LEFT JOIN `stock_inventory` ON ( `bom_acc`.`id` = `stock_inventory`.`bom_id` AND `stock_inventory`.`mat_cat` = 'a' AND `stock_inventory`.`type` = 'r'  AND `stock_inventory`.`notify_det_id` = ".$rtn['notice_det'][$i]['id'].") 
				
				WHERE 

				`bom_acc`.`wi_id` = '".$wi['id']."' AND 
				`bom_acc`.`dis_ver` = '0' AND 
				`bom_acc`.`id` = '".$rtn['notice_det'][$i]['bom_id']."' AND 
				`stock_mat_acc`.`fty` = '".$rtn['notice']['fty']."'
				
				GROUP BY `bom_acc`.`id` ASC
				;";
		
		if (!$q_result = $sql->query($q_str)) {
			$_SESSION['MSG'][] = "Error! Can't access database!";
			return false;    
		}

		$row = $sql->fetch($q_result);
		$row['use_for'] = iconv("big5","utf-8",$row['use_for']);
		$row['receive_qty'] == '' ? 0 : $row['receive_qty'];
		$row['unclaimed_qty'] = $rtn['notice_det'][$i]['qty'] - $row['receive_qty'];
		$stock_row = $this->get_stock_qty($rtn['notice']['fty'],$rtn['notice_det'][$i]['bl_num'],$rtn['notice_det'][$i]['r_no'],$rtn['notice_det'][$i]['l_no'],$rtn['notice_det'][$i]['mat_cat'], $rtn['notice_det'][$i]['mat_id'], $rtn['notice_det'][$i]['o_color'], $rtn['notice_det'][$i]['size']);
		$row['stock_qty'] = $stock_row['qty'];
		$rtn['notice_det'][$i] = array_merge($rtn['notice_det'][$i], $row);
	}
}

if(!empty($rtn))
return $rtn;

} # FUN END
#
#
#
#
#
#
# :append_collar_qty
function append_collar_qty($type,$post) {

$sql = $this->sql;

$active = 0;

foreach($post['bom_id'] as $key => $val){
	
	if( $post['collar_qty'][$key] > 0 ){
	
		$id = get_insert_id('stock_inventory');
		
		if($id){
			$q_str = "
			INSERT INTO `stock_inventory` SET 
			`id` = '".$id."' , 
			`type` = '".$type."' , 
			`fty` = '".$post['fty']."' , 
			`dept` = '".$_SESSION['USER']['ADMIN']['dept']."' , 
			`open_user` = '".$_SESSION['USER']['ADMIN']['id']."' , 
			`open_date` = NOW() , 
			`color` = '".$post['color'][$key]."' , 
			`size` = '".$post['size'][$key]."' , 
			`qty` = '".$post['collar_qty'][$key]."' , 
			`unit` = '".$post['unit'][$key]."' , 
			`invoice_num` = '".$post['invoice_num'][$key]."' , 
			`carrier_num` = '".$post['carrier_num']."' , 
			`ord_num` = '".$post['ord_num'][$key]."' , 
			`ship_inv` = '".$post['ship_num']."' , 
			`ship_link_id` = '".$post['link_id']."' , 
			`po_num` = '".$post['po_num'][$key]."' , 
			`mat_cat` = '".$post['mat_cat'][$key]."' , 
			`mat_id` = '".$post['mat_id'][$key]."' , 
			`bom_id` = '".$post['bom_id'][$key]."' , 
			`remark` = '".$post['remark'][$key]."' 
			;";

			
			if (!$q_result = $sql->query($q_str)) {
				$_SESSION['MSG'][] = "Error! Can't access database!";
				return false;
			}
			
			$this->receive_cat_det($post['mat_cat'][$key],$post['ver'],$post['fty'],$post['mat_id'][$key],$post['color'][$key],$post['size'][$key],$post['collar_qty'][$key],$post['unit'][$key]);
			
			$active++;
		}
	}
}

if( $active > 0 ){
	$this->up_po_ship_active($post['ship_num']);
	return true;
} else {
	return false;
}



} # FUN END
#
#
#
#
#
#
# :receive_cat_det
function receive_cat_det($fty, $ver, $cat, $stock_link_id, $qty) {

$sql = $this->sql;

$cat = substr($cat,0,1) == 'l' ? 'lots' : 'acc' ;
if($cat == "lots"){
	$q_str = "
	UPDATE `stock_mat_".$cat."` SET 
	`qty` = `qty` - '".$qty."' 
	WHERE 
	`ver` = '".$ver."' AND 
	`fty` = '".$fty."' AND 
	`stock_link_id` = '".$stock_link_id."'
	;";

	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;
	}
}else{
	$q_str = "
	UPDATE `stock_mat_".$cat."` SET 
	`qty` = `qty` - '".$qty."' 
	WHERE 
	`ver` = '".$ver."' AND 
	`fty` = '".$fty."' AND 
	`stock_link_id` = '".$stock_link_id."'
	;";

	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :append_retreat_qty
function append_retreat_qty($type,$post) {

$sql = $this->sql;

$active = 0;

foreach($post['return_send_qty'] as $key => $qty){
	
	if( $qty > 0 ){
	
		$id = get_insert_id('stock_inventory');
		
		if($id){
			$q_str = "
			INSERT INTO `stock_inventory` SET 
			`id` = '".$id."' , 
			`type` = '".$type."' , 
			`fty` = '".$post['fty']."' , 
			`dept` = '".$_SESSION['USER']['ADMIN']['dept']."' , 
			`open_user` = '".$_SESSION['USER']['ADMIN']['id']."' , 
			`open_date` = NOW() , 
			`color` = '".$post['color'][$key]."' , 
			`size` = '".$post['size'][$key]."' , 
			`qty` = '".$qty."' , 
			`unit` = '".$post['unit'][$key]."' , 
			`r_no` = '".$post['r_no'][$key]."' , 
			`l_no` = '".$post['l_no'][$key]."' , 
			`invoice_num` = '".$post['invoice_num'][$key]."' , 
			`bl_num` = '".$post['bl_num'][$key]."' , 
			`carrier_num` = '' , 
			`ord_num` = '".$post['order_num']."' , 
			`ship_link_id` = '' , 
			`po_num` = '' , 
			`mat_cat` = '".$post['mat_cat'][$key]."' , 
			`mat_id` = '".$post['mat_id'][$key]."' , 
			`bom_id` = '".$post['bom_id'][$key]."' , 
			`rn_num` = '".$post['rn_num']."' , 
			`notify_det_id` = '".$key."' , 
			`remark` = '' 
			;";

			if (!$q_result = $sql->query($q_str)) {
				$_SESSION['MSG'][] = "Error! Can't access database!";
				return false;
			}
			
			$ver = $post['mat_cat'][$key] == "l" ? $post['ver']['lots'] : $post['ver']['acc'];
			$this->append_cat_det($post['mat_cat'][$key],$ver,$post['fty'],$id,$post['mat_id'][$key],$post['color'][$key],$post['size'][$key],$post['r_no'][$key],$post['l_no'][$key],$qty,$post['unit'][$key],$post['bl_num'][$key],$post['storage'][$key],'');

			$active++;
		}
	}
}

if( $active > 0 ){
	$this->up_po_ship_active($post['ship_num']);
	return true;
} else {
	return false;
}



} # FUN END
#
#
#
#
#
#
# :update_incoming_qty
function update_incoming_qty($post) {

$sql = $this->sql;

$active = 0;

foreach($post['order_qty'] as $key => $val){
	foreach($val as $link_id => $qty){
		$q_str = "update stock_inventory_link
				  set qty = ".$qty."
				  where id = ".$link_id;
		
		if (!$q_result = $sql->query($q_str)) {
			$_SESSION['MSG'][] = "Error! Can't update data!";
			return false;
		}
	}
}

#判斷是否98%驗收 
// if (!$this->chk_po_ship_active($post['ship_id'])) {
    // return false;
// }
return true;
} # FUN END
#
#
#
#
#
#
# :GET_INVENTORY
function get_inventory( $fty , $item , $ver='') {

$sql = $this->sql;

$fields = false;

if( $item == 'lots' ) {

	# bom lots / Name Material Detail Color Size Content Cuttable Width Consumption Unit Stock Qty Collar Qty 
	$ver = ( $ver )? " `stock_mat_lots`.`ver` = '".$ver."' " : " `stock_mat_lots`.`ver` IS NULL ";
	
	$q_str = "
	SELECT 
	`lots`.`id` as `mat_id` , `lots`.`lots_name` , `lots`.`comp` , `lots`.`width` , 
	`stock_mat_lots`.*

	FROM 
	`lots` , `stock_mat_lots` 

	WHERE 
	".$ver." AND `stock_mat_lots`.`fty` = '".$fty."' AND `stock_mat_lots`.`lots_id` = `lots`.`id`

	;";
	
	
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}

	while ($row = $sql->fetch($q_result)) {
		$fields['lots'][] = $row;
	}
	

} else {

	# bom acc / Name Material Detail Color Size BOM Qty Consumption Stock Qty Collar Qty 
	$ver = ( $ver )? " `stock_mat_acc`.`ver` = '".$ver."' " : " `stock_mat_acc`.`ver` IS NULL ";
	
	$q_str = "
	SELECT 
	`acc`.`id` as `mat_id` , `acc`.`acc_name` , `acc`.`des` , `acc`.`specify` , `acc`.`arrange` , 
	`stock_mat_acc`.*

	FROM 
	`acc` , `stock_mat_acc`
	
	WHERE 
	".$ver." AND `stock_mat_acc`.`fty` = '".$fty."' AND `stock_mat_acc`.`acc_id` = `acc`.`id`
	
	;";
	
	
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}
	
	while ($row = $sql->fetch($q_result)) {
		
		$fields['acc'][] = $row;
	}

}

if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :append_inventory
function append_inventory($fty,$mat_cat,$ver) {

$sql = $this->sql;

$q_str = "
INSERT INTO `stock` SET 
`ver` = '".$ver."' , 
`fty` = '".$fty."' , 
`mat_cat` = '".$mat_cat."' , 
`open_user` = '".$_SESSION['USER']['ADMIN']['id']."' , 
`open_date` = NOW() , 
`submit_user` = '' , 
`submit_date` = '' , 
`confirm_user` = '' , 
`confirm_date` = '' , 
`remark` = '' 
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :edit_remark_text
function edit_remark_text($fty,$mat_cat,$ver,$text) {

$sql = $this->sql;

$q_str = "
UPDATE `stock` SET 
`remark` = '".$text."' 
WHERE 
`ver` = '".$ver."' AND 
`fty` = '".$fty."' AND 
`mat_cat` = '".substr($mat_cat,0,1)."'
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :get_inventory_stock
function get_inventory_stock( $fty , $item , $ver='' , $Order) {

$sql = $this->sql;

$fields = false;

if( $item == 'lots' ) {

	# bom lots / Name Material Detail Color Size Content Cuttable Width Consumption Unit Stock Qty Collar Qty 
	$ver = ( $ver )? " `stock_mat_lots`.`ver` = '".$ver."' " : " `stock_mat_lots`.`ver` = '' ";
	if($Order == '')
	{
		$q_str = "
		SELECT 
		`lots`.`id` as `mat_id` , `lots`.`lots_name` , `lots`.`comp` , `lots`.`width` , 
		`stock_mat_lots`.*

		FROM 
		`lots` , `stock_mat_lots` 

		WHERE 
		".$ver." AND `stock_mat_lots`.`fty` = '".$fty."' AND `stock_mat_lots`.`lots_id` = `lots`.`id` AND `stock_mat_lots`.`qty` > 0

		;";
	}
	else
	{
		$q_str = "
		SELECT 
		`lots`.`id` as `mat_id` , `lots`.`lots_name` , `lots`.`comp` , `lots`.`width` , 
		`stock_mat_lots`.*

		FROM 
		`lots` , `stock_mat_lots`  

		WHERE 
		".$ver." AND `stock_mat_lots`.`fty` = '".$fty."' AND `stock_mat_lots`.`lots_id` = `lots`.`id` AND `stock_mat_lots`.`qty` > 0 AND `stock_mat_lots`.`ord_num` = '".$Order."'

		";
	}
	
	//echo $q_str;
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}

	while ($row = $sql->fetch($q_result)) {
		
		$fields['lots'][] = $row;
	}
	

} else {

	# bom acc / Name Material Detail Color Size BOM Qty Consumption Stock Qty Collar Qty 
	$ver = ( $ver )? " `stock_mat_acc`.`ver` = '".$ver."' " : " `stock_mat_acc`.`ver` = '' ";
	if($Order == '')
	{
		$q_str = "
		SELECT 
		`acc`.`id` as `mat_id` , `acc`.`acc_name` , `acc`.`des` , `acc`.`specify` , `acc`.`arrange` , 
		`stock_mat_acc`.*

		FROM 
		`acc` , `stock_mat_acc`
		
		WHERE 
		".$ver." AND `stock_mat_acc`.`fty` = '".$fty."' AND `stock_mat_acc`.`acc_id` = `acc`.`id` AND `stock_mat_acc`.`qty` > 0 
		
		;";
	}
	else
	{
		$q_str = "
		SELECT 
		`acc`.`id` as `mat_id` , `acc`.`acc_name` , `acc`.`des` , `acc`.`specify` , `acc`.`arrange` , 
		`stock_mat_acc`.*

		FROM 
		`acc` , `stock_mat_acc`
		
		WHERE 
		".$ver." AND `stock_mat_acc`.`fty` = '".$fty."' AND `stock_mat_acc`.`acc_id` = `acc`.`id` AND `stock_mat_acc`.`qty` > 0 
		AND `stock_mat_acc`.`ord_num` = '".$Order."'
		
		;";
	}
	
	
	
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}
	
	while ($row = $sql->fetch($q_result)) {
		
		$fields['acc'][] = $row;
	}

}

if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :update_stock_mat_sub_qty
function update_stock_mat_sub_qty($cat,$id,$qty) {

$sql = $this->sql;

$q_str = "
UPDATE `stock_mat_".$cat."` SET 
`sub_qty` = '".$qty."' ,
`checked`  = 1
WHERE 
`id` = '".$id."'
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :update_stock_submit
function update_stock_submit($ver,$fty,$mat_cat) {

$sql = $this->sql;
   
$q_str = "
UPDATE `stock` SET 
`submit_user` = '".$_SESSION['USER']['ADMIN']['id']."' ,
`submit_date` = NOW()  
WHERE 
`ver` = '".$ver."' AND
`fty` = '".$fty."' AND
`mat_cat` = '".substr($mat_cat,0,1)."'
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :update_stock_mat_cfm_qty
function update_stock_mat_cfm_qty($cat,$id,$qty) {

$sql = $this->sql;

$q_str = "
UPDATE `stock_mat_".$cat."` SET 
`cfm_qty` = '".$qty."' 
WHERE 
`id` = '".$id."'
;";


if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :update_stock_confirm
function update_stock_confirm($ver,$fty,$mat_cat) {

$sql = $this->sql;
   
$q_str = "
UPDATE `stock` SET 
`confirm_user` = '".$_SESSION['USER']['ADMIN']['id']."' ,
`confirm_date` = NOW() ,
`finish` = 1 
WHERE 
`ver` = '".$ver."' AND
`fty` = '".$fty."' AND
`mat_cat` = '".substr($mat_cat,0,1)."' 
;";


if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :append_stock_confirm
function append_stock_confirm($cat,$ver,$fty,$cat_id,$color,$size,$qty,$sub_qty,$cfm_qty,$unit) {

$sql = $this->sql;

$cat = $cat == 'l' ? 'lots' : 'acc' ;

$q_str = "
INSERT INTO `stock_mat_".$cat."` SET 
`ver` = '".$ver."' , 
`fty` = '".$fty."' , 
`".$cat."_id` = '".$id."' , 
`color` = '".$color."' , 
`size` = '".$size."' , 
`qty` = '".$qty."' , 
`unit` = '".$unit."' 
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :append_inventory_mat
function append_inventory_mat($old_ver,$new_ver,$fty,$cat) {

$sql = $this->sql;

$q_str = "
SELECT * 
FROM `stock_mat_".$cat."` 
WHERE 
`ver` = '".$old_ver."' AND 
`fty` = '".$fty."' AND `cfm_qty` > 0
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

while ($row = $sql->fetch($q_result)) {

	if($cat=="lots"){
		
			$q_str = "
			INSERT INTO `stock_mat_".$cat."` SET 
			`ver` = '".$new_ver."' , 
			`fty` = '".$fty."' , 
			`stock_inventory_id` = '".$row['stock_inventory_id']."' ,
			`link_det_id` = '".$row['link_det_id']."' ,
			`ap_det_id` = '".$row['ap_det_id']."' ,
			`ord_num` = '".$row['ord_num']."' ,
			`bom_id` = '".$row['bom_id']."' ,
			`".$cat."_id` = '".$row[$cat.'_id']."' , 
			`color` = '".$row['color']."' , 
			`size` = '".$row['size']."' , 
			`l_no` = '".$row['l_no']."' , 
			`r_no` = '".$row['r_no']."' , 
			`qty` = '".$row['cfm_qty']."' , 
			`unit` = '".$row['unit']."' ,
			`bl_num` = '".$row['bl_num']."' ,
			`po_num` = '".$row['po_num']."' ,
			`storage` = '".$row['storage']."' , 
			`this_id` = '".$row['id']."' , 
			`checked` = 0 
			;";
			
	}else{
		
			$q_str = "
			INSERT INTO `stock_mat_".$cat."` SET 
			`ver` = '".$new_ver."' , 
			`fty` = '".$fty."' , 
			`stock_inventory_id` = '".$row['stock_inventory_id']."' ,
			`link_det_id` = '".$row['link_det_id']."' ,
			`ap_det_id` = '".$row['ap_det_id']."' ,
			`ord_num` = '".$row['ord_num']."' ,
			`bom_id` = '".$row['bom_id']."' ,
			`".$cat."_id` = '".$row[$cat.'_id']."' , 
			`color` = '".$row['color']."' , 
			`size` = '".$row['size']."' , 
			`qty` = '".$row['cfm_qty']."' , 
			`unit` = '".$row['unit']."' ,
			`bl_num` = '".$row['bl_num']."' ,
			`po_num` = '".$row['po_num']."' ,
			`storage` = '".$row['storage']."' , 
			`this_id` = '".$row['id']."' , 
			`checked` = 1 
			;";
	}
	
	if (!$result = $sql->query($q_str)) {
		$this->msg->add("Error! Can't access database!");
		$this->msg->merge($sql->msg);
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
# :chk_inventory_i
function chk_inventory_i( $bl_num ) {

$sql = $this->sql;

$q_str = "SELECT `stock_inventory`.* FROM `stock_inventory` WHERE `bl_num` = '".$bl_num."' AND `del_mk` = 'n' ;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

$rows = '';
while ($row = $sql->fetch($q_result)) {
    $rows[] = $row;
}

if(!empty($rows)){
    return true;
} else {
    return false;
}

} # FUN END
#
#
#
#
#
#
# :GET_ORDER_FTY_BY_NOTICE
function get_order_fty_by_notice($notice_num) {

// $sql = $this->sql;
global $MySQL;
$Utf8 = $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

$fields = false;

$q_str = "SELECT `fty` FROM `requi_notify` WHERE `id` LIKE '%".$notice_num."%' AND `status` = '4' ORDER BY `requi_notify`.`id` DESC limit 0,1;";

if (!$q_result = $MySQL->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

if($row = $MySQL->fetch($q_result)) {
	$fields = $row['fty'];
}

$MySQL->po_disconnect();

if(!empty($fields))
	return $fields;
else
	return false;

} # FUN END
#
#
#
#
#
#
function notice_search($parm){

global $PHP_SELF;
global $MySQL;

// $sql = $this->sql;
$Utf8 = $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

$query  = "SELECT `requi_notify`.* FROM `requi_notify` , `requi_notify_det` , `po_bom` WHERE `requi_notify`.`status` = '4' AND `requi_notify`.`id` = `requi_notify_det`.`rn_num`  AND `requi_notify_det`.`po_bom_id` = `po_bom`.`id` ";
$query .= !empty($parm['ord_num']) ? " AND `requi_notify`.`ord_num` LIKE '%".$parm['ord_num']."%' " : "" ;
$query .= !empty($parm['notice_num']) ? " AND `requi_notify`.`id` LIKE '%".$parm['notice_num']."%' " : "" ;
$query .= ( $parm['mat'] == 'lots' ) ? " AND `po_bom`.`mat_cat` = 'l' " : " AND `po_bom`.`mat_cat` = 'a' " ;
$query .= " GROUP BY `requi_notify`.`id` order by `requi_notify`.`id` desc ";

if( $result = $MySQL->query($query) ){
   $num_rows = mysql_num_rows($result);
    
    $record_begin = $parm['now_num'] * $parm['page_num'];
    $record_begin = $record_begin > $num_rows ? $num_rows : $record_begin;
	
	$query .= " limit ".$record_begin." , ".$parm['page_num'];
	// echo $query.'<br>';
    $result = $MySQL->query($query);
    
    $rows['notice'] = array();
    
    while( $row = $MySQL->fetch($result) ) {
		$rows['notice'][] = $row;
        
    }

    $action = $parm['action'];
    $now_num = $parm['now_num'];
    $page_num = $parm['page_num'];
    
    @$PageTotal = ceil($num_rows / $page_num);
    
    $rows['page'] = '
                              <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                <tr>
                                  <td align="left" nowrap width="30%">';
    if ( $now_num > 0 ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num=0" class="pnum" ><img src="images/icon_flag_left2.gif" border="0" title="最前頁"></a>';
    }
    
    if (($now_num-10)>= 0 ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-10).'" class="pnum" >上10頁</a>';
    }
    
    if ( $now_num > 0 ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-1).'" class="pnum" ><img src="images/icon_flag_left.gif" border="0" title="上一頁"></a>';
    }
    
    $rows['page'] .= '</td>
                                  <td align="center" nowrap width="40%">';
    #現在的頁數
    $o=0;
    for ($m=$now_num-2;$m < $PageTotal+1;$m++){
        if ( $m > 0 and $o < 7){
          $CSS = ( $m == $now_num+1 ) ? 'page_num_on' : 'page_num_off' ;
          $dd = ( $m <> 1 ) ? ' . ' : '' ;
          $rows['page'] .= ''.$dd.'<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($m-1).'" class="'.$CSS.'" title="'.$m.'">'.$m.'</a> ';
                                    $o++;
        }
    }
    
    $rows['page'] .= '</td>
                                  <td align="right" nowrap width="30%">';
                                  
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+1).'" class="pnum" ><img src="images/icon_flag_right.gif" border="0" title="下一頁"></a>';
    }
    
    if ( ($now_num+11) <= $PageTotal ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+10).'" class="pnum" >下10頁</a>';
    }
    
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($PageTotal-1).'" class="pnum" ><img src="images/icon_flag_right2.gif" border="0" title="最後頁"></a>';
    }
    
    $rows['page'] .= '</td>
                                  <td colspan="3" align="center">';

    $rows['page'] .= '</td>
                                </tr>
                              </table>
                            ';
    if( !empty($num_rows) ){
        $rows['page_text'] = '
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="num_info">
                          <tr>
                            <td> 第 '. ($now_num+1) .' 頁，總計 '. $PageTotal .' 頁 ( 共 ' . $num_rows . ' 筆資料 ) </td>
                          </tr>
                        </table>
                      ';
    } else {
        $rows['page_text'] = '';
    }
    
    return $rows;

}

} # END
#
#
#
#
#
#
function notice_search4adjust($parm){

global $PHP_SELF;
global $MySQL;

// $sql = $this->sql;
$Utf8 = $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

$query  = "SELECT `requi_notify`.* FROM `requi_notify` WHERE `status` = '4' ";
$query .= !empty($parm['ord_num']) ? " AND `ord_num` LIKE '%".$parm['ord_num']."%' " : "" ;
$query .= !empty($parm['rn_num']) ? " AND `rn_num` LIKE '%".$parm['rn_num']."%' " : "" ;
$query .= " ;";

if( $result = $MySQL->query($query) ){
   $num_rows = mysql_num_rows($result);
    
    $record_begin = $parm['now_num'] * $parm['page_num'];
    $record_begin = $record_begin > $num_rows ? $num_rows : $record_begin;
	
    $result = $MySQL->query($query);
    
    $rows['notice'] = array();
    
    while( $row = $MySQL->fetch($result) ) {
		$rows['notice'][] = $row;
        
    }

    $action = $parm['action'];
    $now_num = $parm['now_num'];
    $page_num = $parm['page_num'];
    
    @$PageTotal = ceil($num_rows / $page_num);
    
    $rows['page'] = '
                              <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                <tr>
                                  <td align="left" nowrap width="30%">';
    if ( $now_num > 0 ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num=0" class="pnum" ><img src="images/icon_flag_left2.gif" border="0" title="最前頁"></a>';
    }
    
    if (($now_num-10)>= 0 ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-10).'" class="pnum" >上10頁</a>';
    }
    
    if ( $now_num > 0 ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-1).'" class="pnum" ><img src="images/icon_flag_left.gif" border="0" title="上一頁"></a>';
    }
    
    $rows['page'] .= '</td>
                                  <td align="center" nowrap width="40%">';
    #現在的頁數
    $o=0;
    for ($m=$now_num-2;$m < $PageTotal+1;$m++){
        if ( $m > 0 and $o < 7){
          $CSS = ( $m == $now_num+1 ) ? 'page_num_on' : 'page_num_off' ;
          $dd = ( $m <> 1 ) ? ' . ' : '' ;
          $rows['page'] .= ''.$dd.'<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($m-1).'" class="'.$CSS.'" title="'.$m.'">'.$m.'</a> ';
                                    $o++;
        }
    }
    
    $rows['page'] .= '</td>
                                  <td align="right" nowrap width="30%">';
                                  
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+1).'" class="pnum" ><img src="images/icon_flag_right.gif" border="0" title="下一頁"></a>';
    }
    
    if ( ($now_num+11) <= $PageTotal ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+10).'" class="pnum" >下10頁</a>';
    }
    
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= '<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($PageTotal-1).'" class="pnum" ><img src="images/icon_flag_right2.gif" border="0" title="最後頁"></a>';
    }
    
    $rows['page'] .= '</td>
                                  <td colspan="3" align="center">';

    $rows['page'] .= '</td>
                                </tr>
                              </table>
                            ';
    if( !empty($num_rows) ){
        $rows['page_text'] = '
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="num_info">
                          <tr>
                            <td> 第 '. ($now_num+1) .' 頁，總計 '. $PageTotal .' 頁 ( 共 ' . $num_rows . ' 筆資料 ) </td>
                          </tr>
                        </table>
                      ';
    } else {
        $rows['page_text'] = '';
    }
    
    return $rows;

}

} # END
#
#
#
#
#
#
function get_stock_qty($ver, $mat_cat, $lots_id, $color, $size){
	$sql = $this->sql;
	
	$tbl = $mat_cat == "l" ? "`stock_mat_lots`" : "`stock_mat_acc`";
	$q_str = "SELECT sum(qty) as `qty` 
			  FROM ".$tbl."
			  WHERE `ver` = '".$ver."' 
              AND `lots_id` = '".$lots_id."' 
              AND `color` = '".$color."' 
              AND `size` = '".$size."' 
              AND `qty` > '0' 
			  GROUP BY `bom_id` ;";
	// echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
	
	return $row['qty'];
} # END
#
#
#
#
#
#
function get_stock_qty_by_inventory_id($ver, $mat_cat, $stock_inventory_id){
	$sql = $this->sql;
	
	$tbl = $mat_cat == "l" ? "stock_mat_lots" : "stock_mat_acc";
	$q_str = "SELECT sum(qty) as qty
			  from ".$tbl."
			  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id."
			  group by stock_inventory_id";
	// echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
	
	return $row['qty'];
} # END
#
#
#
#
#
#
function get_inventory_qty($fty, $bl_num,$r_no,$l_no,$mat_cat, $mat_id, $color, $size) {
	$sql = $this->sql;
	
	$q_str = "SELECT sum(qty) as qty
			  from stock_inventory
			  where type = 'r' and fty = '".$fty."' and bl_num = '".$bl_num."' and r_no = '".$r_no."' and l_no = '".$l_no."' 
					and mat_id = '".$mat_id."' and color = '".$color."' and size = '".$size."' 
			  group by fty, bl_num, r_no, l_no, mat_id, color, size";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
		
	return $row['qty'];
} # END
#
#
#
#
#
#
# :append_send_qty
function append_send_qty($type,$get) {

$sql = $this->sql;

$active = 0;

foreach($get['send_qty'] as $key => $qty){
	if( $qty > 0 ){
	
		$id = get_insert_id('stock_inventory');
		
		if($id){
			$q_str = "
			INSERT INTO `stock_inventory` SET 
			`id` = '".$id."' , 
			`type` = '".$type."' , 
			`fty` = '".$get['fty']."' , 
			`dept` = '".$_SESSION['USER']['ADMIN']['dept']."' , 
			`open_user` = '".$_SESSION['USER']['ADMIN']['id']."' , 
			`open_date` = NOW() , 
			`acpt_user` = '".$get['PHP_acpt_user']."' , 
			`color` = '".$get['color'][$key]."' , 
			`size` = '".$get['size'][$key]."' , 
			`qty` = '".$qty."' , 
			`r_no` = '".$get['r_no'][$key]."' , 
			`l_no` = '".$get['l_no'][$key]."' ,
			`unit` = '".$get['unit'][$key]."' , 
			`invoice_num` = '' , 
			`pi_num` = '' , 
			`bl_num` = '".$get['bl_num'][$key]."' , 
			`carrier_num` = '' , 
			`ord_num` = '".$get['order_num']."' , 
			`ship_id` = '' , 
			`ship_det_id` = '' , 
			`ship_link_id` = '' , 
			`po_num` = '".$get['po_num'][$key]."' , 
			`mat_cat` = '".$get['mat_cat'][$key]."' , 
			`mat_id` = '".$get['mat_id'][$key]."' , 
			`bom_id` = '".$get['bom_id'][$key]."' , 
			`rn_num` = '".$get['rn_num']."' , 
			`notify_det_id` = '".$key."' , 
			`stock_inventory_id` = '".$get['stock_inventory_id'][$key]."' , 
			`remark` = '' 
			;";

			
			if (!$q_result = $sql->query($q_str)) {
				$_SESSION['MSG'][] = "Error! Can't access database!";
				return false;
			}
			$ver = $this->get_ver( $get['fty'] , $get['mat_cat'][$key] );
			# 扣庫存數量功能還沒做
			$this->update_cat_qty_after_send($get['fty'],$ver['ver'],$get['bom_id'][$key],$get['mat_cat'][$key],$get['stock_inventory_id'][$key],$qty);
			
			$active++;
		}
	}
}

} # FUN END
#
#
#
#
#
#
# :GET_PO_SHIP_DET4ADJUST
function get_po_ship_det4adjust($id) {

$sql = $this->sql;

$fields = false;

$q_str = "
SELECT DISTINCT 
`po_supl_ship`.`bl_num`, `po_supl_ship_det`.`po_num` , `po_supl_ship_det`.`invoice_num` , `po_supl_ship_det`.`pi_num` , 
`po_supl_ship_link`.`mat_cat` , `po_supl_ship_link`.`mat_id` , `po_supl_ship_link`.`id` , `po_supl_ship_link`.`color` , `po_supl_ship_link`.`size` , 
`po_supl_ship_link`.`qty` , `po_supl_ship_link`.`po_unit` , `po_supl_ship_link`.`po_supl_ship_id` as `ship_id` , `po_supl_ship_link`.`po_supl_ship_det_id` as `ship_det_id` ,
`po_supl_ship_link`.`c_no` , `po_supl_ship_link`.`r_no` , `po_supl_ship_link`.`l_no` , `po_supl_ship_link`.`gw` , `po_supl_ship_link`.`nw` , `po_supl_ship_link`.`c_o`
 
FROM 
`po_supl_ship` LEFT JOIN`po_supl_ship_det` ON `po_supl_ship_det`.`po_supl_ship_id`=`po_supl_ship`.`id` LEFT JOIN `po_supl_ship_link` ON ( `po_supl_ship_det`.`id` = `po_supl_ship_link`.`po_supl_ship_det_id` ) 

WHERE `po_supl_ship_link`.`po_supl_ship_id` = '".$id."' AND `po_supl_ship_det`.`status` = '1' AND `po_supl_ship_link`.`status` = '1'

ORDER BY `po_supl_ship_link`.`id` ASC
;";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

while ($row = $sql->fetch($q_result)) {
	($cat = $row['mat_cat']=='l'?'lots':'acc');
    
	$q_strs = "
	SELECT 
	`bom_".$cat."`.`color` , `bom_".$cat."`.`size` , `bom_".$cat."`.`wi_id` , `bom_".$cat."`.`".$cat."_used_id` , `bom_".$cat."`.`wi_id` ,
	`wi`.`wi_num` , 
	`".$cat."_use`.`".$cat."_name` , `".$cat."_use`.`use_for` 

	FROM 
	`bom_".$cat."` , `wi` , `".$cat."_use`

	WHERE 
	`bom_".$cat."`.`id` = '".$row['bom_id']."' AND
	`bom_".$cat."`.`wi_id` = `wi`.`id` AND
	`".$cat."_use`.`id` = `bom_".$cat."`.`".$cat."_used_id`
	
	;";
	
	$q_results = $sql->query($q_strs);
	$rows = $sql->fetch($q_results);
	$row['detail'] = $rows[$cat.'_name'].':'.$rows['use_for'];

	$row['qty'] = round($row['qty']);


	$q_strss = "SELECT notify_det_id 
				FROM `stock_inventory` 
				WHERE type = 'r' and bl_num = '".$row['bl_num']." ' and l_no = '".$row['l_no']."' and r_no = '".$row['r_no']."' and mat_cat = '".$row['mat_cat']."' and 
					  mat_id = '".$row['mat_id']."' and color = '".$row['color']."' and size = '".$row['size']."'";
	
	$q_resultss = $sql->query($q_strss);
	$rowss = $sql->fetch($q_resultss);
	
	$row['send_flag'] = $rowss['notify_det_id'];
	$fields[] = $row;
}

return $fields;

} # FUN END
#
#
#
#
#
#
function stock_search($argv){

	global $PHP_SELF;
	
	$sql = $this->sql;

	$ver = $this->get_ver($argv['fty'], $argv['mat']);

	$srh = new SEARCH();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "select stock_mat_".$argv['mat'].".*, ".$argv['mat'].".".$argv['mat']."_name
			   from stock_mat_".$argv['mat']." left join ".$argv['mat']." on stock_mat_".$argv['mat'].".".$argv['mat']."_id = ".$argv['mat'].".id";
	
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$srh->add_cgi_parm("PHP_action",$argv['action']);
	$srh->add_sort_condition("stock_mat_".$argv['mat'].".id ");
	$srh->row_per_page = 20;
	
	if($limit_entries){
		$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}
	
	if(isset($argv['mat_name'])){
		$srh->add_where_condition($argv['mat'].".".$argv['mat']."_name like '%".$argv['mat_name']."%'");
	}
	$srh->add_where_condition("stock_mat_".$argv['mat'].".ver = '".$ver['ver']."'");
	$srh->add_where_condition("stock_mat_".$argv['mat'].".fty = '".$argv['fty']."'");			

	$result= $srh->send_query2();  
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}
	$this->msg->merge($srh->msg);
	if (!$result){   // 當查尋無資料時
		$op['record_NONE'] = 1;
	}

	$op[$argv['mat']] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
	if(!$limit_entries){ 
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] =  $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];		
	}
	
	return $op;
} # END
#
#
#
#
#
#
function get_send_detail($bl_num, $l_no, $r_no, $mat_id, $color, $size){
	global $MySQL;
	$rtn = false;
	$Utf8 = $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

	$q_str = "select requi_notify.rn_num, requi_notify.ord_num, requi_notify.create_user, requi_notify_det.bl_num,
					 requi_notify_det.l_no, requi_notify_det.r_no, requi_notify_det.color, requi_notify_det.qty
			  from requi_notify, requi_notify_det
			  where requi_notify_det.bl_num = '".$bl_num."' and requi_notify_det.l_no = '".$l_no."' and requi_notify_det.r_no = '".$r_no."' and 
					requi_notify_det.mat_id = '".$mat_id."' and requi_notify_det.o_color = '".$color."' and size = '".$size."' and 
					requi_notify.rn_num = requi_notify_det.rn_num";
	
	if (!$q_result = $MySQL->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}

	while($row = $MySQL->fetch($q_result)){
		$rtn['notice_det'][] = $row;
	}

	$MySQL->po_disconnect();

	return $rtn;

} # FUN END
#
#
#
#
#
#
function update_storage( $id, $mat, $storage ){
	$sql = $this->sql;
	
	$q_str = "update stock_mat_".$mat."
			  set storage = '".$storage."'
			  where id = ".$id;
	
	if (!$q_result = $sql->query($q_str)) {
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
function get_stock2pdf($fty, $mat) {
	$sql = $this->sql;
	
	$ver = $this->get_ver( $fty, $mat);
	
	$q_str = "SELECT stock_mat_".$mat.".*, ".$mat.".".$mat."_name as mat_name
			  from stock_mat_".$mat.", ".$mat."
			  where fty = '".$fty."' and ver = '".$ver['ver']."' and stock_mat_".$mat.".".$mat."_id = ".$mat.".id";
	
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
} # END
#
#
#
#
#
# :get_utf_field_name
function get_utf_field_name($fty, $auth) {

global $MySQL;
$Utf8 = $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

if($fty == "LY")
	$country = "VN";
if($fty == "CF")
	$country = "CF";

$fields = array();

$q_str = "SELECT  field_name, field_value
		  FROM utf_field_name 
		  WHERE country = '".$country."' and auth = '".$auth."';";

if (!$q_result = $MySQL->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while($row = $MySQL->fetch($q_result)) {
	$fields[] = $row;
}

$MySQL->po_disconnect();

return $fields;

} # FUN END
#
#
#
#
#
function get_inventory_det($type, $po_nums, $mat_cat, $mat_id, $color, $size) {
	$sql = $this->sql;
	
	$rtn = array();
	if(substr($po_nums,-1) == ","){
		$po_nums = substr($po_nums, 0, -1);
	}
	$po_num_ary = explode(",", $po_nums);
	$po_num_size = sizeof($po_num_ary);
	$po_where_str = "";
	for($i=0; $i<$po_num_size; $i++){
		$po_where_str .= "'".$po_num_ary[$i] .= "',";
	}
	$po_where_str = substr($po_where_str, 0, -1);
	
	$q_str = "SELECT ship_id, bl_num, po_num, open_date, mat_cat, mat_id, color, size, qty
			  from stock_inventory
			  where type = '".$type."' and po_num in(".$po_where_str.") and mat_cat = '".$mat_cat."' and mat_id = '".$mat_id."' 
					and color = '".$color."' and size = '".$size."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}
	
		
	return $rtn;
} # END
#
#
#
#
#
function get_print_inventory($fty, $mat, $order_num) {
	
	$sql = $this->sql;
	
	$rtn = array();
	$ver = $this->get_ver( $fty, $mat);
	$int_ver = intval($ver['ver']);
	if($int_ver >= 1){
		$ver['ver'] = '';
	}else{
		$int_ver = intval($ver['ver']) - 1;
		$int_ver = str_pad($int_ver, 4, "0", STR_PAD_LEFT);
		$ver['ver'] = $int_ver;
	}
	
	$q_str = "SELECT stock_mat_".$mat.".*, ".$mat.".".$mat."_name as mat_name
			  from stock_mat_".$mat.", ".$mat."
			  where stock_mat_".$mat.".ver = '".$ver['ver']."' and stock_mat_".$mat.".".$mat."_id = ".$mat.".id and stock_mat_".$mat.".ord_num='".$order_num."' 
			  order by ".$mat.".".$mat."_name";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}

		
	return $rtn;
} # END
#
#
#
#
#
#
function get_print_stock($fty, $mat, $begin_date, $end_date) {
	$sql = $this->sql;
	
	$ver = $this->get_ver( $fty, $mat);
	if($begin_date && $end_date){
		$end_date .= " 23:59:59";
		$q_str = "SELECT stock_mat_".$mat.".*, sum(stock_mat_".$mat.".qty) as stock_qty, ".$mat.".".$mat."_name as mat_name
			  from stock_mat_".$mat.", ".$mat.", stock_inventory
			  where stock_mat_".$mat.".stock_inventory_id = stock_inventory.id and stock_inventory.open_date between '".$begin_date."' and '".$end_date."' and stock_mat_".$mat.".fty = '".$fty."' and stock_mat_".$mat.".ver = '".$ver['ver']."' and stock_mat_".$mat.".".$mat."_id = ".$mat.".id 
			  group by stock_mat_".$mat.".po_num, stock_mat_".$mat.".".$mat."_id, stock_mat_".$mat.".color, stock_mat_".$mat.".size, stock_mat_".$mat.".unit and stock_mat_".$mat.".storage";
	}else{
		$q_str = "SELECT stock_mat_".$mat.".*, sum(stock_mat_".$mat.".qty) as stock_qty, ".$mat.".".$mat."_name as mat_name
				  from stock_mat_".$mat.", ".$mat."
				  where stock_mat_".$mat.".fty = '".$fty."' and stock_mat_".$mat.".ver = '".$ver['ver']."' and stock_mat_".$mat.".".$mat."_id = ".$mat.".id 
				  group by stock_mat_".$mat.".po_num, stock_mat_".$mat.".".$mat."_id, stock_mat_".$mat.".color, stock_mat_".$mat.".size, stock_mat_".$mat.".unit and stock_mat_".$mat.".storage";
	}
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$rtn = array();
	while($stock_row = $sql->fetch($q_result)){
		$q_str = "select distinct ap.currency, ap_det.po_unit, ap_det.prc_unit, ap_det.prics
				  from ap, ap_det
				  where ap.po_num = '".$stock_row['po_num']."' and ap_det.ap_num = ap.ap_num and ap_det.mat_id = '".$stock_row[$mat.'_id']."' and 
						ap_det.color = '".$stock_row['color']."' and ap_det.size = '".$stock_row['size']."'";
		
		$ap_det_rsl = $sql->query($q_str);
		$ap_row = $sql->fetch($ap_det_rsl);
		
		$change_prics = number_format(change_unit_price($ap_row['prc_unit'],$stock_row['unit'],$ap_row['prics']),5,'','');
		$stock_row['prics'] = $change_prics;
		$stock_row['currency'] = $ap_row['currency'];
		
		$rtn[] = $stock_row;
	}
	
	return $rtn;
} # END
#
#
#
#
#
#
function get_print_stock_detail($fty, $mat, $begin_date, $end_date) {
	$sql = $this->sql;
	if(empty($fty)) $fty = "LY";
	$ver = $this->get_ver( $fty, $mat);
	
	$inventory_type = array('i','r','b');
	$inventory_row = array();
	if(empty($begin_date)) $begin_date = date("Y-m-d");
	if(empty($end_date)) $end_date = date("Y-m-d");
	$end_date .= " 23:59:59";
	
	for($x=0;$x<sizeof($inventory_type);$x++){
		$q_str = "SELECT stock_inventory.open_date, stock_inventory.mat_cat, stock_inventory.mat_id, stock_inventory.color,
						 stock_inventory.size, stock_inventory.unit, ".$mat.".".$mat."_name as mat_name, stock_inventory.qty
				  from stock_inventory, ".$mat." 
				  where stock_inventory.fty = '".$fty."' and stock_inventory.type = '".$inventory_type[$x]."' and stock_inventory.open_date between '".$begin_date."' and '".$end_date."' 
						and stock_inventory.mat_cat = '".strtolower(substr($mat,0,1))."' and stock_inventory.mat_id = ".$mat.".id 
				  order by stock_inventory.mat_id";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while($row = $sql->fetch($q_result)){
			$inventory_row['inventory_'.$inventory_type[$x]][] = $row;
		}
	}
	
	$rtn = array();
	foreach($inventory_row['inventory_i'] as $i_key => $i_val){
		$rtn[$i_val['mat_id']."_".$i_val['color']."_".$i_val['size']."_".$i_val['unit']]['inventory_i'][] = $i_val;
	}
	
	foreach($inventory_row['inventory_r'] as $r_key => $r_val){
		$rtn[$r_val['mat_id']."_".$r_val['color']."_".$r_val['size']."_".$r_val['unit']]['inventory_r'][] = $r_val;
	}
	
	foreach($inventory_row['inventory_b'] as $b_key => $b_val){
		$rtn[$b_val['mat_id']."_".$b_val['color']."_".$b_val['size']."_".$b_val['unit']]['inventory_b'][] = $b_val;
	}
	
	if(isset($ver['ver'])){
		foreach($rtn as $key => $val){
			foreach($val as $k2 => $v2){
				# 庫存unit 跟 驗收 unit 不符 所以還沒做
				$rtn[$key]['begin_qty'] = $this->get_stock_begin_qty($fty, $ver['ver'], $v2[0]['mat_cat'], $v2[0]['mat_id'], $v2[0]['color'], $v2[0]['size'], $v2[0]['unit'], $begin_date, $end_date);
				break;
			}
		}
	}else{
		foreach($rtn as $key => $val){
			$rtn[$key]['begin_qty'] = 0;
		}
	}
	
	return $rtn;
} # END
#
#
#
#
#
#
function get_print_stock_diff_detail($fty, $mat, $begin_date, $end_date) {
	$sql = $this->sql;
	if(empty($fty)) $fty = "LY";
	$ver = $this->get_ver( $fty, $mat);
	
	$inventory_type = array('i','r','b');
	$inventory_row = array();
	if(empty($begin_date)) $begin_date = date("Y-m-d");
	if(empty($end_date)) $end_date = date("Y-m-d");
	$end_date .= " 23:59:59";
	
	for($x=0;$x<sizeof($inventory_type);$x++){
		$q_str = "SELECT stock_inventory.open_date, stock_inventory.mat_cat, stock_inventory.mat_id, stock_inventory.color,
						 stock_inventory.size, stock_inventory.unit, ".$mat.".".$mat."_name as mat_name, stock_inventory.qty
				  from stock_inventory, ".$mat." 
				  where stock_inventory.fty = '".$fty."' and stock_inventory.type = '".$inventory_type[$x]."' and stock_inventory.open_date between '".$begin_date."' and '".$end_date."' 
						and stock_inventory.mat_cat = '".strtolower(substr($mat,0,1))."' and stock_inventory.mat_id = ".$mat.".id 
				  order by stock_inventory.mat_id";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while($row = $sql->fetch($q_result)){
			$inventory_row['inventory_'.$inventory_type[$x]][] = $row;
		}
	}
	
	$rtn = array();
	foreach($inventory_row['inventory_i'] as $i_key => $i_val){
		$rtn[$i_val['mat_id']."_".$i_val['color']."_".$i_val['size']."_".$i_val['unit']]['inventory_i'][] = $i_val;
	}
	
	foreach($inventory_row['inventory_r'] as $r_key => $r_val){
		$rtn[$r_val['mat_id']."_".$r_val['color']."_".$r_val['size']."_".$r_val['unit']]['inventory_r'][] = $r_val;
	}
	
	foreach($inventory_row['inventory_b'] as $b_key => $b_val){
		$rtn[$b_val['mat_id']."_".$b_val['color']."_".$b_val['size']."_".$b_val['unit']]['inventory_b'][] = $b_val;
	}
	
	return $rtn;
} # END
#
#
#
#
#
#
function get_print_stock_type_detail($fty, $type, $mat, $begin_date, $end_date, $order) {
	$sql = $this->sql;
	if(empty($fty)) $fty = "LY";
	$ver = $this->get_ver( $fty, $mat);
	
	if($order !='')
	{
		if($begin_date && $end_date){
			$end_date .= " 23:59:59";
			$q_str = "SELECT stock_inventory_link.ord_num, stock_inventory.type, stock_inventory.open_date, stock_inventory.mat_cat, stock_inventory.mat_id, stock_inventory.color,
							 stock_inventory.size, stock_inventory.unit, stock_inventory.r_no, stock_inventory.l_no, 
							 ".$mat.".".$mat."_name as mat_name, stock_inventory.qty
					  from stock_inventory, ".$mat." , stock_inventory_link
					  where stock_inventory_link.ord_num='".$order."' 
							and stock_inventory.fty = '".$fty."' 
							and stock_inventory.type = '".$type."' 
							and stock_inventory.open_date between '".$begin_date."' and '".$end_date."' 
							and stock_inventory.mat_cat = '".strtolower(substr($mat,0,1))."' 
							and stock_inventory.mat_id = ".$mat.".id 
							and stock_inventory_link.bl_num = stock_inventory.bl_num 
							and stock_inventory_link.po_num = stock_inventory.po_num 
							and stock_inventory_link.mat_id = stock_inventory.mat_id 
							and stock_inventory_link.color = stock_inventory.color 
							and stock_inventory_link.bom_id = stock_inventory.bom_id 
					  order by stock_inventory_link.ord_num,stock_inventory.mat_id";
		}else{
			$q_str = "SELECT stock_inventory_link.ord_num, stock_inventory.type, stock_inventory.open_date, stock_inventory.mat_cat, stock_inventory.mat_id, stock_inventory.color,
							 stock_inventory.size, stock_inventory.unit, stock_inventory.r_no, stock_inventory.l_no, 
							 ".$mat.".".$mat."_name as mat_name, stock_inventory.qty
					  from stock_inventory, ".$mat." , stock_inventory_link
					  where stock_inventory_link.ord_num='".$order."' 
							and stock_inventory.fty = '".$fty."' 
							and stock_inventory.type = '".$type."' 
							and stock_inventory.mat_cat = '".strtolower(substr($mat,0,1))."' 
							and stock_inventory.mat_id = ".$mat.".id 
							and stock_inventory_link.bl_num = stock_inventory.bl_num 
							and stock_inventory_link.po_num = stock_inventory.po_num 
							and stock_inventory_link.mat_id = stock_inventory.mat_id 
							and stock_inventory_link.color = stock_inventory.color 
							and stock_inventory_link.bom_id = stock_inventory.bom_id 
					  order by stock_inventory_link.ord_num,stock_inventory.mat_id";
		}
	}
	else
	{
	if($begin_date && $end_date){
		$end_date .= " 23:59:59";
		$q_str = "SELECT stock_inventory_link.ord_num, stock_inventory.type, stock_inventory.open_date, stock_inventory.mat_cat, stock_inventory.mat_id, stock_inventory.color,
						 stock_inventory.size, stock_inventory.unit, stock_inventory.r_no, stock_inventory.l_no, 
						 ".$mat.".".$mat."_name as mat_name, stock_inventory.qty
				  from stock_inventory, ".$mat." , stock_inventory_link 
				  where stock_inventory.fty = '".$fty."' 
						and stock_inventory.type = '".$type."' 
						and stock_inventory.open_date between '".$begin_date."' and '".$end_date."' 
						and stock_inventory.mat_cat = '".strtolower(substr($mat,0,1))."' 
						and stock_inventory.mat_id = ".$mat.".id 
						and stock_inventory_link.bl_num = stock_inventory.bl_num 
						and stock_inventory_link.po_num = stock_inventory.po_num 
						and stock_inventory_link.mat_id = stock_inventory.mat_id 
						and stock_inventory_link.color = stock_inventory.color 
						and stock_inventory_link.bom_id = stock_inventory.bom_id 
				  order by stock_inventory_link.ord_num,stock_inventory.mat_id";
	}else{
		$q_str = "SELECT stock_inventory_link.ord_num, stock_inventory.type, stock_inventory.open_date, stock_inventory.mat_cat, stock_inventory.mat_id, stock_inventory.color,
						 stock_inventory.size, stock_inventory.unit, stock_inventory.r_no, stock_inventory.l_no, 
						 ".$mat.".".$mat."_name as mat_name, stock_inventory.qty
				  from stock_inventory, ".$mat." , stock_inventory_link 
				  where stock_inventory.fty = '".$fty."' 
						and stock_inventory.type = '".$type."' 
						and stock_inventory.mat_cat = '".strtolower(substr($mat,0,1))."' 
						and stock_inventory.mat_id = ".$mat.".id 
						and stock_inventory_link.bl_num = stock_inventory.bl_num 
						and stock_inventory_link.po_num = stock_inventory.po_num 
						and stock_inventory_link.mat_id = stock_inventory.mat_id 
						and stock_inventory_link.color = stock_inventory.color 
						and stock_inventory_link.bom_id = stock_inventory.bom_id 
				  order by stock_inventory_link.ord_num,stock_inventory.mat_id";
	}
	}
	/* echo $q_str;
	exit;  */
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$inventory_row = array();
	while($row = $sql->fetch($q_result)){
		$inventory_row[] = $row;
	}
	
	return $inventory_row;
} # END
#
#
#
#
#
#
function get_print_stock2pdf($fty, $mat) {
	$sql = $this->sql;
	
	$ver = $this->get_ver( $fty, $mat);
	
	$q_str = "SELECT stock_mat_".$mat.".*, ".$mat.".".$mat."_name as mat_name
			  from stock_mat_".$mat.", ".$mat."
			  where fty = '".$fty."' and ver = '".$ver['ver']."' and stock_mat_".$mat.".".$mat."_id = ".$mat.".id";
	
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
} # END
#
#
#
#
#
function get_stock_begin_qty($fty, $ver, $mat_cat, $mat_id, $color, $size, $unit, $begin_date, $end_date){
	$sql = $this->sql;
	
	# 庫存數量
	$mat = ($mat_cat=='l'? "lots" : "acc");
	$q_str = "SELECT sum(qty) as qty
			  from stock_mat_".$mat."
			  where fty = '".$fty."' and ver = '".$ver."' and ".$mat."_id = ".$mat_id." and color = '".$color."' and size = '".$size."' and unit = '".$unit."'";
	
	if (!$q_result = $sql->query($q_str)) {
		return false;    
	}
	$stock_row = $sql->fetch($q_result);
	if(empty($stock_row['qty'])) $stock_row['qty'] = 0;
	
	# 驗收數量
	$q_str = "select sum(qty) as qty
			  from stock_inventory
			  where fty = '".$fty."' and type = 'i' and open_date between '".$begin_date."' and '".$end_date."' and mat_cat = '".$mat_cat."' and 
					mat_id = '".$mat_id."' and color = '".$color."' and size = '".$size."' and unit = '".$unit."'";
	
	if (!$q_result = $sql->query($q_str)) {
		return false;    
	}
	$i_row = $sql->fetch($q_result);
	if(empty($i_row['qty'])) $i_row['qty'] = 0;
	
	# 領料數量
	$q_str = "select sum(qty) as qty
			  from stock_inventory
			  where fty = '".$fty."' and type = 'r' and open_date between '".$begin_date."' and '".$end_date."' and mat_cat = '".$mat_cat."' and 
					mat_id = '".$mat_id."' and color = '".$color."' and size = '".$size."' and unit = '".$unit."'";
	
	if (!$q_result = $sql->query($q_str)) {
		return false;    
	}
	$r_row = $sql->fetch($q_result);
	if(empty($r_row['qty'])) $r_row['qty'] = 0;
	
	# 退料數量
	$q_str = "select sum(qty) as qty
			  from stock_inventory
			  where fty = '".$fty."' and type = 'b' and open_date between '".$begin_date."' and '".$end_date."' and mat_cat = '".$mat_cat."' and 
					mat_id = '".$mat_id."' and color = '".$color."' and size = '".$size."' and unit = '".$unit."'";
	
	if (!$q_result = $sql->query($q_str)) {
		return false;    
	}
	$b_row = $sql->fetch($q_result);
	if(empty($b_row['qty'])) $b_row['qty'] = 0;
	
	return $stock_row['qty'] - $i_row['qty'] + $r_row['qty'] - $b_row['qty'];
	
}
#Function End
function get_print_lots_rcv_detail(){

	//print_r($_POST);
	$detail=$_POST['PHP_detail'];
	if($detail)
	{
		
		//明細(取消細節勾選，暫不用)
		$q_str = "select fty,color,size,sum(qty),unit,r_no,l_no,po_num,mat_id ";
		$q_str .= "from stock_inventory ";
		$q_str .= "where open_date between '";
		$q_str .= $_POST['PHP_lots_detail_begin_date']." 00:00:00' ";
		$q_str .= "and '";
		$q_str .= $_POST['PHP_lots_detail_end_date']." 23:59:59' ";
		$q_str .= "and mat_cat='".($_POST['PHP_mat']=='lots'? 'l' : 'a')."' ";
		$q_str .= "and type='i' ";
		$q_str .= "and fty like '%".$_POST['PHP_factory']."%' ";
		$q_str .= "group by fty,color,size,unit,r_no,l_no,po_num,mat_id ";
		$q_str .= "order by fty,po_num,color,size,unit,l_no,r_no,mat_id";
	}
	else
	{
		
		
		/*
		$q_str = "select fty,color,size,sum(qty),unit,po_num,mat_id ";
		$q_str .= "from stock_inventory ";
		$q_str .= "where open_date between '";
		$q_str .= $_POST['PHP_lots_detail_begin_date']." 00:00:00' ";
		$q_str .= "and '";
		$q_str .= $_POST['PHP_lots_detail_end_date']." 23:59:59' ";
		$q_str .= "and mat_cat='".($_POST['PHP_mat']=='lots'? 'l' : 'a')."' ";
		$q_str .= "and type='i' ";
		$q_str .= "and fty like '%".$_POST['PHP_factory']."%' ";
		$q_str .= "group by fty,color,size,unit,po_num,mat_id ";
		$q_str .= "order by fty,po_num,color,size,unit,mat_id";
		*/
		
		//日期區間內驗收總量
		if($_POST['PHP_mat']=='lots')
		{
			$q_str = "select stock_inventory.fty,stock_inventory.color,stock_inventory.size,sum(stock_inventory.qty) as qty,stock_inventory.unit,stock_inventory.po_num,stock_inventory.mat_id,lots.lots_code,lots.lots_name ";
			$q_str .= "from stock_inventory ";
			$q_str .= "left join lots on stock_inventory.mat_id=lots.id ";
			$q_str .= "where stock_inventory.open_date between '";
			$q_str .= $_POST['PHP_lots_detail_begin_date']." 00:00:00' ";
			$q_str .= "and '";
			$q_str .= $_POST['PHP_lots_detail_end_date']." 23:59:59' ";
			$q_str .= "and stock_inventory.mat_cat='l' ";
			$q_str .= "and stock_inventory.type='i' ";
			$q_str .= "and stock_inventory.fty like '%".$_POST['PHP_factory']."%' ";
			$q_str .= "group by stock_inventory.fty,stock_inventory.color,stock_inventory.size,stock_inventory.unit,stock_inventory.po_num,stock_inventory.mat_id,lots.lots_code,lots.lots_name ";
			$q_str .= "order by stock_inventory.fty,stock_inventory.po_num,stock_inventory.color,stock_inventory.size,stock_inventory.unit,stock_inventory.mat_id,lots.lots_code,lots.lots_name";
		}
		else
		{
			$q_str = "select stock_inventory.fty,stock_inventory.color,stock_inventory.size,sum(stock_inventory.qty) as qty,stock_inventory.unit,stock_inventory.po_num,stock_inventory.mat_id,acc.acc_code,acc.acc_name ";
			$q_str .= "from stock_inventory ";
			$q_str .= "left join acc on stock_inventory.mat_id=acc.id ";
			$q_str .= "where stock_inventory.open_date between '";
			$q_str .= $_POST['PHP_lots_detail_begin_date']." 00:00:00' ";
			$q_str .= "and '";
			$q_str .= $_POST['PHP_lots_detail_end_date']." 23:59:59' ";
			$q_str .= "and stock_inventory.mat_cat='a' ";
			$q_str .= "and stock_inventory.type='i' ";
			$q_str .= "and stock_inventory.fty like '%".$_POST['PHP_factory']."%' ";
			$q_str .= "group by stock_inventory.fty,stock_inventory.color,stock_inventory.size,stock_inventory.unit,stock_inventory.po_num,stock_inventory.mat_id,acc.acc_code,acc.acc_name ";
			$q_str .= "order by stock_inventory.fty,stock_inventory.po_num,stock_inventory.color,stock_inventory.size,stock_inventory.unit,stock_inventory.mat_id,acc.acc_code,acc.acc_name";
		}
		
	}
	
	
	
	//exit;
	$sql = $this->sql;
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$stock_inventory = array();
	while($row = $sql->fetch($q_result)){
		$stock_inventory[] = $row;
	}
	//print_r($stock_inventory);
	//exit;
	//利用日期區間內驗收總量來查詢同一張po中同顏色同尺碼同料號一起採購的訂單號有幾筆
	foreach($stock_inventory as $key => $val)
	{
		
		//po請購總數量
		$q_str = "select sum(po_qty) as qty  ";
		$q_str .= "from ap_det ";
		$q_str .= "where ap_num in ('";
		$q_str .= str_replace("PO","PA",$val['po_num']);
		$q_str .= "') ";
		$q_str .= "and mat_id=";
		$q_str .= $val['mat_id']." ";
		$q_str .= "and color='";
		$q_str .= $val['color']."' ";
		$q_str .= "and size='";
		$q_str .= $val['size']."' ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result)){
			$stock_inventory[$key]['po_qty'] = $row['qty'];
			$po_qty=$row['qty'];
		}
		
		//驗收總數量
		$q_str = "SELECT sum(qty) as qty  ";
		$q_str .= "FROM stock_inventory ";
		$q_str .= "WHERE po_num = '";
		$q_str .= $val['po_num'];
		$q_str .= "' ";
		$q_str .= "AND type='i'";
		$q_str .= "AND color = '";
		$q_str .= $val['color']."' ";
		$q_str .= "AND size = '";
		$q_str .= $val['size']."' ";
		$q_str .= "AND mat_id =";
		$q_str .= $val['mat_id']." ";
		
		//exit;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result)){
			$stock_inventory[$key]['rcv_qty'] = $row['qty'];
			//$po_qty=$row['qty'];
		}
		
		//PO下的各訂單請購總數量
		$q_str = "select order_num,sum(po_qty) as qty,sum(ap_qty) as ap_qty  ";
		//$q_str = "select order_num,sum(po_qty) as qty  ";
		$q_str .= "from ap_det ";
		$q_str .= "where ap_num in ('";
		$q_str .= str_replace("PO","PA",$val['po_num']);
		$q_str .= "') ";
		$q_str .= "and mat_id=";
		$q_str .= $val['mat_id']." ";
		$q_str .= "and color='";
		$q_str .= $val['color']."' ";
		$q_str .= "and size='";
		$q_str .= $val['size']."' ";
		$q_str .= "group by order_num ";
		$q_str .= "order by order_num";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		//$stock_inventory = array();
		while($row = $sql->fetch($q_result)){
			$stock_inventory[$key]['order'][] = $row;
			//$stock_inventory[$key]['order'][]['percentage'] = $row['qty']/$po_qty;
		}
		
		//print_r();
		foreach($stock_inventory[$key]['order'] as $subkey => $subval)//修改成val
		{
			//$stock_inventory[$key]['order'][$subkey]['percentage']=number_format($subval['qty']/$po_qty,5);
			//$stock_inventory[$key]['order'][$subkey]['percentage_qty'] = number_format($val['qty']*number_format($subval['qty']/$po_qty,5),2);//number_format($rcv_qty*$subval['percentage'],2);
			//$stock_inventory[$key]['order'][$subkey]['percentage_rcvqty_total'] = number_format($stock_inventory[$key]['rcv_qty']*number_format($subval['qty']/$po_qty,5),2);
			$stock_inventory[$key]['order'][$subkey]['percentage']=number_format($subval['qty']/$po_qty,5);
			$qty=number_format($subval['qty']/$po_qty,5,'','');
			$stock_inventory[$key]['order'][$subkey]['percentage_qty'] = number_format($val['qty']*$qty,2,'','');
			$qty=number_format($subval['qty']/$po_qty,5,'','');
			$stock_inventory[$key]['order'][$subkey]['percentage_rcvqty_total'] = number_format($stock_inventory[$key]['rcv_qty']*$qty,2,'','');
		}
		
		//$stock_inventory[$key]['order']['percentage ']
		//print_r( $po_qty)."<br>";
	}
	
	//print_r($stock_inventory);
	//exit;
	return $stock_inventory;
	
}
#
#
#
#
#
#
function get_print_order_mat_cost($fty, $mat, $begin_date, $end_date) {
	$sql = $this->sql;
	
	// $end_date .= " 23:59:59";
	if(empty($fty)) $fty = "LY";
	$ver = $this->get_ver( $fty, $mat);
	
	
	$q_str = "select distinct ord_num
			  from schedule
			  where fty = '".$fty."' and rel_ets between '".$begin_date."' and '".$end_date."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$rtn = array();
	$mat_cat = strtolower(substr($mat,0,1));
	while($ord_row = $sql->fetch($q_result)){
		$rtn[$ord_row['ord_num']] = array();
		if($mat_cat == 'l'){
			$q_str = "select lots.lots_name as mat_name, lots.lots_code, ap_det.ap_num, ap.ap_date, ap_det.order_num, ap_det.bom_id, 
							 ap_det.ap_qty, ap_det.po_qty, ap_det.mat_cat, ap_det.mat_id, ap_det.color, ap_det.size, 
							 ap_det.unit, ap_det.po_unit, ap_det.prc_unit, ap_det.prics, ap_det.amount as po_amount, ap.currency
					  from wi, bom_lots, ap_det, ap, lots
					  where wi.wi_num = '".$ord_row['ord_num']."' and bom_lots.wi_id = wi.id and ap_det.mat_cat = 'l' and 
							ap_det.bom_id = bom_lots.id and ap_det.mat_id = lots.id and ap.ap_num = ap_det.ap_num and ap.status = 12";
			
			  
			$bom_result = $sql->query($q_str);
			while($bom_row = $sql->fetch($bom_result)){
				if($bom_row['currenty'] == 'NTD'){
					$bom_row['rate'] = 1;
				}else{
					$bom_row['rate'] = $GLOBALS['rate']->get_rate($bom_row['currency'], $bom_row['ap_date']);
				}
				
				# 額外採購的話 會有多筆相同bom_id的資料，且額外採購的 prics unit currency 不一定會一樣，所以要轉換
				if(isset($rtn[$ord_row['ord_num']][$bom_row['bom_id']])){
					# 採購
					$change_qty = change_unit_qty($bom_row['po_unit'], $rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_unit'], $bom_row['po_qty']);
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_qty'] += $change_qty;
					$change_price = change_unit_qty($bom_row['po_unit'], $rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_unit'], $bom_row['prics']);
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']]['prics'] = ($rtn[$ord_row['ord_num']][$bom_row['bom_id']]['prics']+$change_price) /2 ;
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_amount'] += $bom_row['po_amount'];
					# 驗收數量
					$bom_row['i_qty'] = $this->get_i_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
					
					# 領用數量
					$bom_row['r_qty'] += $this->get_r_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
				}else{
					# 驗收數量
					$bom_row['i_qty'] = $this->get_i_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
					
					# 領用數量
					$bom_row['r_qty'] = $this->get_r_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']] = $bom_row;
				}
			}
		}else{
			$q_str = "select acc.acc_name as mat_name, acc.acc_code, ap_det.ap_num, ap.ap_date, ap_det.order_num, ap_det.bom_id, 
							 ap_det.ap_qty, ap_det.po_qty, ap_det.mat_cat, ap_det.mat_id, ap_det.color, ap_det.size, 
							 ap_det.unit, ap_det.po_unit, ap_det.prc_unit, ap_det.prics, ap_det.amount as po_amount, ap.currency
					  from wi, bom_acc, ap_det, ap, acc
					  where wi.wi_num = '".$ord_row['ord_num']."' and bom_acc.wi_id = wi.id and ap_det.mat_cat = 'a' and 
							ap_det.bom_id = bom_acc.id and ap_det.mat_id = acc.id and ap.ap_num = ap_det.ap_num and ap.status = 12";
			
			
			$bom_result = $sql->query($q_str);
			while($bom_row = $sql->fetch($bom_result)){
				if($bom_row['currenty'] == 'NTD'){
					$bom_row['rate'] = 1;
				}else{
					$bom_row['rate'] = $GLOBALS['rate']->get_rate($bom_row['currency'], $bom_row['ap_date']);
				}
				
				# 額外採購的話 會有多筆相同bom_id的資料，且額外採購的 prics unit currency 不一定會一樣，所以要轉換
				if(isset($rtn[$ord_row['ord_num']][$bom_row['bom_id']])){
					# 採購
					$change_qty = change_unit_qty($bom_row['po_unit'], $rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_unit'], $bom_row['po_qty']);
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_qty'] += $change_qty;
					$change_price = change_unit_qty($bom_row['po_unit'], $rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_unit'], $bom_row['prics']);
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']]['prics'] = ($rtn[$ord_row['ord_num']][$bom_row['bom_id']]['prics']+$change_price) /2 ;
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']]['po_amount'] += $bom_row['po_amount'];
					# 驗收數量
					$bom_row['i_qty'] += $this->get_i_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
					# 領用數量
					$bom_row['r_qty'] += $this->get_r_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
				}else{
					# 驗收數量
					$bom_row['i_qty'] += $this->get_i_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
					# 領用數量
					$bom_row['r_qty'] = $this->get_r_qty4order_cost($bom_row['mat_cat'], $bom_row['bom_id']);
					$rtn[$ord_row['ord_num']][$bom_row['bom_id']] = $bom_row;
				}
			}
		}
	}
	/* print_r($rtn);
	exit; */
	
	return $rtn;
} # END
#
#
#
#
#
#
function get_r_qty4order_cost($mat_cat, $bom_id) {
	$sql = $this->sql;
	
	$q_str = "select sum(qty) as qty
			  from stock_inventory
			  where `type` = 'r' and mat_cat = '".$mat_cat."' and bom_id = '".$bom_id."'";

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
	
	return $row['qty'];
} # END
#
#
#
#
#
function get_i_qty4order_cost($mat_cat, $bom_id) {
	$sql = $this->sql;
	
	$q_str = "SELECT SUM(`stock_inventory_link`.`qty`) as qty ";
	$q_str .= "FROM `stock_inventory_link`";
	$q_str .= "WHERE `stock_inventory_link`.`bom_id` = ";
	$q_str .= $bom_id." ";
	$q_str .= "AND `stock_inventory_link`.`mat_cat` = '";
	$q_str .= $mat_cat;
	$q_str .= "';";
	
	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	
	if(!$row['qty']) $row['qty'] = 0;
	
	return number_format($row['qty'], 2,'','');
} # END
#Function End
#
#
#
#
#
function avg_order_qty4incoming($ap_num, $mat_cat, $mat_id, $color, $size, $po_unit, $i_qty) {
	$sql = $this->sql;
	
	# 訂單的採購量
	$q_str = "select id, order_num, bom_id, po_qty
			  from ap_det
			  where ap_num = '".$ap_num."' and mat_cat = '".$mat_cat."' and mat_id = '".$mat_id."'
					and color = '".$color."' and size = '".$size."'
			  order by po_qty asc";
	
	$q_result = $sql->query($q_str);
	
	$rtn = array();
	$ttl_po_qty = 0;
	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
		$ttl_po_qty += $row['po_qty'];
	}
	
	$tmp_qty = 0;
	for($i=0;$i<sizeof($rtn)-1;$i++){
		$percent = number_format($rtn[$i]['po_qty']/$ttl_po_qty, 5, '', '');
		$rtn[$i]['percent'] = $percent;
		$avg_qty = number_format($i_qty * $percent, 2, '', '');
		$rtn[$i]['i_qty'] = $po_unit == 'pc' ? ceil($avg_qty) : $avg_qty;
		$tmp_qty += $rtn[$i]['i_qty'];
	}
	$rtn[$i]['i_qty'] = $i_qty - $tmp_qty;
	
	return $rtn;
} # END
#
#
#
#
#
#
# :append_stock_inventory_link
function append_stock_inventory_link($parm) {
	$sql = $this->sql;

	$q_str = "INSERT INTO `stock_inventory_link` SET 
				`stock_id` = '".$parm['stock_id']."' , 
				`ver` = '".$parm['ver']."' , 
				`bl_num` = '".$parm['bl_num']."' , 
				`po_num` = '".$parm['po_num']."' , 
				`ap_det_id` = '".$parm['ap_det_id']."' , 
				`bom_id` = '".$parm['bom_id']."' , 
				`mat_cat` = '".$parm['mat_cat']."' , 
				`mat_id` = '".$parm['mat_id']."' , 
				`color` = '".$parm['color']."' , 
				`size` = '".$parm['size']."' , 
				`unit` = '".$parm['unit']."' , 
				`ord_num` = '".$parm['ord_num']."' , 
				`qty` = '".$parm['qty']."'
				;";
    // echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
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
# :append_stock_inventory_link_det
function append_stock_inventory_link_det($parm) {
	$sql = $this->sql;

	$q_str = "INSERT INTO `stock_inventory_link_det` SET 
    `stock_id` = '".$parm['stock_id']."' , 
    `ver` = '".$parm['ver']."' , 
    `bl_num` = '".$parm['bl_num']."' , 
    `po_num` = '".$parm['po_num']."' , 
    `stock_inventory_id` = '".$parm['stock_inventory_id']."' , 
    `ap_det_id` = '".$parm['ap_det_id']."' , 
    `bom_id` = '".$parm['bom_id']."' , 
    `mat_cat` = '".$parm['mat_cat']."' , 
    `mat_id` = '".$parm['mat_id']."' , 
    `color` = '".$parm['color']."' , 
    `size` = '".$parm['size']."' , 
    `unit` = '".$parm['unit']."' , 
    `ord_num` = '".$parm['ord_num']."' , 
    `qty` = '".$parm['qty']."' , 
    `stock_mat_id` = '".$parm['stock_mat_id']."' 
    ;";

    // echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}
	
	return true;

} # FUN END
#Function End
#
#
#
#
#
#
# :append_stock_inventory_det
function append_stock_inventory_det($parm) {
	$sql = $this->sql;

	$q_str = "INSERT INTO `stock_inventory_det` SET 
    `stock_id` = '".$parm['stock_id']."' , 
    `ver` = '".$parm['ver']."' , 
    `bl_num` = '".$parm['bl_num']."' , 
    `po_num` = '".$parm['po_num']."' , 
    `stock_inventory_id` = '".$parm['stock_inventory_id']."' , 
    `ap_det_id` = '".$parm['ap_det_id']."' , 
    `bom_id` = '".$parm['bom_id']."' , 
    `mat_cat` = '".$parm['mat_cat']."' , 
    `mat_id` = '".$parm['mat_id']."' , 
    `color` = '".$parm['color']."' , 
    `size` = '".$parm['size']."' , 
    `unit` = '".$parm['unit']."' , 
    `ord_num` = '".$parm['ord_num']."' , 
    `qty` = '".$parm['qty']."' , 
    `stock_mat_id` = '".$parm['stock_mat_id']."' 
    ;";

    // echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}
	
	return true;

} # FUN END
#Function End
#
#
#
#
#
# :append_lots
function append_old_lots($parm) {
	$sql = $this->sql;

	$q_str = "INSERT INTO `stock_old_lots` SET 
				`mat_name` = '".$parm['mat_name']."' , 
				`supl_code` = '".$parm['supl_code']."' , 
				`qty` = '".$parm['qty']."' ,
				`color` = '".$parm['color']."' ,
				`price` = '".$parm['price']."' ,
				`unit` = '".$parm['unit']."' ,
				`width` = '".$parm['width']."'
				;";

	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}

	return true;

} # FUN END
#
#
#
#
#
function search_old_lots($argv) {
	$sql = $this->sql;
	
	$srh = new SEARCH();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$q_header = "SELECT * FROM stock_old_lots";
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->row_per_page = 20;
	$pagesize=10;
	if ($argv['PHP_sr_startno']) {
		$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
    }else{
       	$pages = $srh->get_page(1,$pagesize);
    } 
 
	
		$mesg = '';
		if ($str = $argv['SCH_lots_code'] )  { 
			$srh->add_where_condition("lots_code LIKE  '%$str%'", "SCH_lots_code",$str); 
			$mesg.=" Fabric#  : [ $str ].";
		}
		if ($str = $argv['SCH_lots_name'] )  { 
			$srh->add_where_condition("mat_name LIKE  '%$str%'", "SCH_lots_name",$str); 
			$mesg.= " Name : [ $str ]. ";
		}

		if ($mesg){
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}		
		
		$srh->add_sort_condition("`id` DESC ");
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
		if (!$result){
			$op['record_NONE'] = 1;
		}

		$op['lots'] = $result;
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
		
		return $op;
} // end func
#
#
#
#
#
#
# :get_old_lots
function get_old_lots($id) {
	$sql = $this->sql;

	$q_str = "select *
			  from stock_old_lots
			  where id = ".$id;

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);

	return $row;

} # FUN END
#
#
#
#
#
# :update_old_lots
function update_old_lots($parm) {
	$sql = $this->sql;

	$q_str = "UPDATE `stock_old_lots` 
			  SET `mat_name` = '".$parm['mat_name']."', 
				  `supl_code` = '".$parm['supl_code']."', 
				  `qty` = '".$parm['qty']."', 
				  `color` = '".$parm['color']."', 
				  `price` = '".$parm['price']."', 
				  `unit` = '".$parm['unit']."', 
				  `width` = '".$parm['width']."'
			  WHERE `id` = '".$parm['id']."' ;";

	if($q_result = $sql->query($q_str)){
		return true;
	}else{
		return false;
	}
	
} # FUN END
# :del_old_lots
function del_old_lots($id) {
	$sql = $this->sql;

	$q_str = "DELETE FROM `stock_old_lots` 
			  WHERE `id` = '".$id."' ;";

	if($q_result = $sql->query($q_str)){
		return true;
	}else{
		return false;
	}
	
} # FUN END
#
#
#
#
#
# :append_lots
function append_old_acc($parm) {
	$sql = $this->sql;

	$q_str = "INSERT INTO `stock_old_acc` SET 
				`mat_name` = '".$parm['mat_name']."' , 
				`supl_code` = '".$parm['supl_code']."' , 
				`qty` = '".$parm['qty']."' ,
				`color` = '".$parm['color']."' ,
				`price` = '".$parm['price']."' ,
				`unit` = '".$parm['unit']."' 
				;";

	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;    
	}

	return true;

} # FUN END
#
#
#
#
#
function search_old_acc($argv) {
	$sql = $this->sql;
	
	$srh = new SEARCH();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$q_header = "SELECT * FROM stock_old_acc";
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->row_per_page = 20;
	$pagesize=10;
	if ($argv['PHP_sr_startno']) {
		$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
    }else{
       	$pages = $srh->get_page(1,$pagesize);
    } 
 
	
		$mesg = '';
		if ($str = $argv['SCH_lots_code'] )  { 
			$srh->add_where_condition("lots_code LIKE  '%$str%'", "SCH_lots_code",$str); 
			$mesg.=" Fabric#  : [ $str ].";
		}
		if ($str = $argv['SCH_lots_name'] )  { 
			$srh->add_where_condition("mat_name LIKE  '%$str%'", "SCH_lots_name",$str); 
			$mesg.= " Name : [ $str ]. ";
		}

		if ($mesg){
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}		
		
		$srh->add_sort_condition("`id` DESC ");
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
		if (!$result){
			$op['record_NONE'] = 1;
		}

		$op['acc'] = $result;
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
		
		return $op;
} // end func
#
#
#
#
#
# :get_old_lots
function get_old_acc($id) {
	$sql = $this->sql;

	$q_str = "select *
			  from stock_old_acc
			  where id = ".$id;

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);

	return $row;

} # FUN END
#
#
#
#
#
# :update_old_lots
function update_old_acc($parm) {
	$sql = $this->sql;

	$q_str = "UPDATE `stock_old_acc` 
			  SET `mat_name` = '".$parm['mat_name']."', 
				  `supl_code` = '".$parm['supl_code']."', 
				  `qty` = '".$parm['qty']."', 
				  `color` = '".$parm['color']."', 
				  `price` = '".$parm['price']."', 
				  `unit` = '".$parm['unit']."' 
			  WHERE `id` = '".$parm['id']."' ;";

	if($q_result = $sql->query($q_str)){
		return true;
	}else{
		return false;
	}
	
} # FUN END
#
#
#
#
#
# :del_old_lots
function del_old_acc($id) {
	$sql = $this->sql;

	$q_str = "DELETE FROM `stock_old_acc` 
			  WHERE `id` = '".$id."' ;";

	if($q_result = $sql->query($q_str)){
		return true;
	}else{
		return false;
	}
	
} # FUN END
#
#
#
#
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_inventory_qty($wi_id, $wi_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_inventory_qty_by_type($fty, $type, $stock_inventory_id) {
	$sql = $this->sql;
	
	$q_str = "SELECT sum(qty) as `qty`
			  FROM `stock_inventory`
			  WHERE `type` = '".$type."' AND `fty` = '".$fty."' AND `id` = '".$stock_inventory_id."'";
	echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
		
	return $row['qty'];
}
#
#
#
#
#
#
# :get_inventory_qty_by_i_id
function get_inventory_qty_by_i_id($fty, $type, $notify_det_id ) {
	$sql = $this->sql;
	
	$q_str = "SELECT sum(qty) as `qty`
			  FROM `stock_inventory`
			  WHERE `type` = '".$type."' AND `fty` = '".$fty."' AND `notify_det_id`  = '".$notify_det_id ."'
			  GROUP BY `fty`, `type`, `notify_det_id` ";
	// echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
		
	return $row['qty'];
}
#
#
#
#
#
#
# :get_po_ship_det_group_by_mat
function get_po_ship_det_group_by_mat($id) {

	$sql = $this->sql;

	$fields = false;

	$q_str = "
	SELECT  
	`po_supl_ship_det`.`id`, `po_supl_ship_det`.`id` as det_id ,`po_supl_ship`.`bl_num`, `po_supl_ship_det`.`po_num` , `po_supl_ship_det`.`invoice_num` , 
	`po_supl_ship_det`.`pi_num` , 
	`po_supl_ship_link`.`mat_cat`, `po_supl_ship_link`.`mat_id`, `po_supl_ship_link`.`color`, `po_supl_ship_link`.`size`,  
	sum(`po_supl_ship_link`.`qty`) as `qty` , `po_supl_ship_link`.`po_unit`
	 
	FROM 
	`po_supl_ship_det` LEFT JOIN `po_supl_ship_link` ON ( `po_supl_ship_det`.`id` = `po_supl_ship_link`.`po_supl_ship_det_id` ) 
					   LEFT JOIN `po_supl_ship` on `po_supl_ship`.`id` = `po_supl_ship_link`.`po_supl_ship_id`

	WHERE `po_supl_ship_link`.`po_supl_ship_id` = '".$id."' AND `po_supl_ship_det`.`status` = '1' AND `po_supl_ship_link`.`status` = '1'

	GROUP BY `po_supl_ship_det`.`po_num`,`po_supl_ship_link`.`mat_id`,`po_supl_ship_link`.`color`,`po_supl_ship_link`.`size`;";
	
	if (!$q_result = $sql->query($q_str)) {
		$_SESSION['MSG'][] = "Error! Can't access database!";
		return false;
	}

	$m=0;
	while ($row = $sql->fetch($q_result)) {
		$row['id'] = $row['id']."_".$m;
		$row['ap_num'] = str_replace("PO", "PA", $row['po_num']);
		($cat = $row['mat_cat']=='l'?'lots':'acc');
		
		$q_str = "select ".$cat."_name as mat_name
				  from ".$cat."
				  where id =".$row['mat_id'];
		$mat_name_rel = $sql->query($q_str);
		$mat_name_row = $sql->fetch($mat_name_rel);
		$row['mat_name'] = $mat_name_row['mat_name'];
		
		# 取訂單號碼 及 數量
		$q_str = "SELECT ap_det.id, ap_det.order_num, ap_det.bom_id, ap_det.po_qty, ap_det.po_unit,
						 sum(stock_inventory_det.qty) as i_qtyed
					FROM ap_det left join stock_inventory_det on stock_inventory_det.bom_id = ap_det.bom_id and stock_inventory_det.bl_num = '".$row['bl_num']."'
					WHERE ap_det.ap_num = '".$row['ap_num']."' and ap_det.mat_id = '".$row['mat_id']."' and 
						  ap_det.color = '".$row['color']."' and ap_det.size = '".$row['size']."' 
					GROUP BY bom_id
					ORDER BY ap_det.po_qty asc";
		
		
		$ap_det_rsl = $sql->query($q_str);
		$ttl_i_qtyed = $ttl_po_qty = 0;
		while($ap_det_row = $sql->fetch($ap_det_rsl)){
			$ap_det_row['order_num'] = $ap_det_row['order_num']."_".$ap_det_row['id'];
			$ttl_i_qtyed += $ap_det_row['i_qtyed'];
			$ttl_po_qty += $ap_det_row['po_qty'];
			$row['orders'][] = $ap_det_row;
		}
		
		$row['received'] = $ttl_i_qtyed;
		$row['remain'] = $row['qty'] - $ttl_i_qtyed;
		if($row['remain']<0) $row['remain'] = 0;
		
		# 平分數量
		$order_size = sizeof($row['orders']);
		
		$tmp_qty = 0;
		for($i=0;$i<$order_size-1;$i++){
			$percent = number_format($row['orders'][$i]['po_qty']/$ttl_po_qty, 5, '', '');
			$row['orders'][$i]['percent'] = $percent;
			$avg_qty = number_format($row['remain'] * $percent, 2, '', '');
			$row['orders'][$i]['i_qty'] = $row['orders'][$i]['po_unit'] == 'pc' ? ceil($avg_qty) : round($avg_qty);
			$tmp_qty += $row['orders'][$i]['i_qty'];
		}
		$row['orders'][$i]['i_qty'] = $row['remain'] - $tmp_qty;
		# END 取訂單號碼 及 數量
		
		# supl_ship_link 明細
		$q_str = "select *
				  from po_supl_ship_link
				  where po_supl_ship_det_id = '".$row['det_id']."' and mat_id = '".$row['mat_id']."' and 
						color = '".$row['color']."' and size = '".$row['size']."'
				  order by l_no, r_no, c_no";
		
		$link_rsl = $sql->query($q_str);
		while($link_row = $sql->fetch($link_rsl)){
			$link_tmp_qty = 0;
			for($i=0;$i<$order_size-1;$i++){
				$link_row['orders'][$i]['order_num'] = $row['orders'][$i]['order_num'];
				$link_row['orders'][$i]['percent'] = $row['orders'][$i]['percent'];
				$link_row['orders'][$i]['i_qty'] = number_format($row['orders'][$i]['percent'] * $link_row['qty'],0,'','');
				$link_tmp_qty += $link_row['orders'][$i]['i_qty'];
			}
			$link_row['orders'][$i]['order_num'] = $row['orders'][$i]['order_num'];
			$link_row['orders'][$i]['percent'] = $row['orders'][$i]['percent'];
			$link_row['orders'][$i]['i_qty'] = $row['orders'][$i]['percent'] * $link_row['qty'];
			$link_row['orders'][$i]['i_qty'] = $link_row['qty'] - $link_tmp_qty;
			
			$row['ship_link'][] = $link_row;
		}
		
		$fields[] = $row;
		$m++;
	}

	return $fields;

} # FUN END
#
#
#
#
#
# :get_send_qty4link
function get_send_qty4link($bl_num, $mat_cat, $bom_id) {
	$sql = $this->sql;
	
	$q_str = "SELECT sum(qty) as qty
			  from stock_inventory
			  where `type` = 'r' and bl_num = '".$bl_num."' and mat_cat = '".$mat_cat."' and bom_id = '".$bom_id."'
			  group by bl_num, mat_cat, bom_id";
	
	
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row['qty']) $row['qty'] = 0;
		
	return $row['qty'];
}
#
#
#
#
#
#
# :update_cat_qty_after_send
function update_cat_qty_after_send($fty,$ver, $bom_id, $cat, $stock_inventory_id, $qty) {

	$sql = $this->sql;

	$q_str = "select sum(qty) as qty
			  from stock_inventory_det
			  where stock_inventory_id = ".$stock_inventory_id;

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	if($row['qty'] <= $qty){
		$q_str = "update stock_inventory_det
				  set send_qty = qty
				  where stock_inventory_id = ".$stock_inventory_id;
		
		
		$q_result = $sql->query($q_str);
	}else{
		$q_str = "select id, qty
				  from stock_inventory_det
				  where bom_id = ".$bom_id." and stock_inventory_id = ".$stock_inventory_id;
		
		
		$q_result = $sql->query($q_str);
		$un_equal_row = $sql->fetch($q_result);
		if($un_equal_row['qty'] >= $qty){
			$q_str = "update stock_inventory_det
					  set send_qty = ".$qty."
					  where id = ".$un_equal_row['id'];
			
			
			$q_result = $sql->query($q_str);
		}else{
			$tmp_qty = 0;
			$q_str = "update stock_inventory_det
					  set send_qty = qty
					  where id = ".$un_equal_row['id'];
			
			
			$q_result = $sql->query($q_str);
			$tmp_qty += $un_equal_row['qty'];
			
			$q_str = "select id, qty
					  from stock_inventory_det
					  where stock_inventory_id = ".$stock_inventory_id." 
							and id <> ".$un_equal_row['id']." and qty > 0";
			
			
			$m_result = $sql->query($q_str);
			while($m_row = $sql->fetch($m_result)){
				if( $m_row['qty'] + $tmp_qty <= $qty ){
					$q_str = "update stock_inventory_det
							  set send_qty = qty
							  where id = ".$m_row['id'];
					
					$q_result = $sql->query($q_str);
					$tmp_qty += $m_row['qty'];
				}else{
					$q_str = "update stock_inventory_det
							  set send_qty = ".($qty - $tmp_qty)."
							  where id = ".$m_row['id'];
					
					$q_result = $sql->query($q_str);
					break;
				}
			}
		}
	}

	# update stock_mat_cat
	$cat = substr($cat,0,1) == 'l' ? 'lots' : 'acc' ;
	$q_str = "update stock_mat_".$cat."
			  set qty = qty - ".$qty."
			  where fty = '".$fty."' and ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;
	
	$q_result = $sql->query($q_str);
	// echo $q_str;
    exit;

return true;

} # FUN END
#
#
#
#
#
#
# :純備份用 可刪除此function
function update_cat_qty_after_send_zzzzzzzzzz($ver, $bom_id, $cat, $stock_inventory_id, $qty) {

$sql = $this->sql;

$cat = substr($cat,0,1) == 'l' ? 'lots' : 'acc' ;
if($cat == "lots"){
	$q_str = "select sum(qty) as qty
			  from stock_mat_lots
			  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	if($row['qty'] == $qty){
		$q_str = "update stock_mat_lots
				  set qty = 0
				  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;
		
		
		$q_result = $sql->query($q_str);
	}else{
		$q_str = "select id, qty
				  from stock_mat_lots
				  where ver = '".$ver."' and bom_id = ".$bom_id." and stock_inventory_id = ".$stock_inventory_id;
		
		
		$q_result = $sql->query($q_str);
		$un_equal_row = $sql->fetch($q_result);
		if($un_equal_row['qty'] >= $qty){
			$q_str = "update stock_mat_lots
					  set qty = ".($un_equal_row['qty']-$qty)."
					  where id = ".$un_equal_row['id'];
			
			
			$q_result = $sql->query($q_str);
		}else{
			$tmp_qty = 0;
			$q_str = "update stock_mat_lots
					  set qty = 0
					  where id = ".$un_equal_row['id'];
			
			
			$q_result = $sql->query($q_str);
			$tmp_qty += $un_equal_row['qty'];
			
			$q_str = "select id, qty
					  from stock_mat_lots
					  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id." 
							and id <> ".$un_equal_row['id']." and qty > 0";
			
			
			$m_result = $sql->query($q_str);
			while($m_row = $sql->fetch($m_result)){
				if( $m_row['qty'] + $tmp_qty <= $qty ){
					$q_str = "update stock_mat_lots
						  set qty = 0
						  where id = ".$m_row['id'];
					
					$q_result = $sql->query($q_str);
					$tmp_qty += $m_row['qty'];
				}else{
					$q_str = "update stock_mat_lots
						  set qty = qty - ".($qty - $tmp_qty)."
						  where id = ".$m_row['id'];
					
					$q_result = $sql->query($q_str);
					break;
				}
			}
		}
	}
}else{
	$q_str = "select sum(qty) as qty
			  from stock_mat_acc
			  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	if($row['qty'] == $qty){
		$q_str = "update stock_mat_acc
				  set qty = 0
				  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id;
		
		
		$q_result = $sql->query($q_str);
	}else{
		$q_str = "select id, qty
				  from stock_mat_acc
				  where ver = '".$ver."' and bom_id = ".$bom_id." and stock_inventory_id = ".$stock_inventory_id;
		
		
		$q_result = $sql->query($q_str);
		$un_equal_row = $sql->fetch($q_result);
		if($un_equal_row['qty'] >= $qty){
			$q_str = "update stock_mat_acc
					  set qty = ".($un_equal_row['qty']-$qty)."
					  where id = ".$un_equal_row['id'];
			
			
			$q_result = $sql->query($q_str);
		}else{
			$tmp_qty = 0;
			$q_str = "update stock_mat_acc
					  set qty = 0
					  where id = ".$un_equal_row['id'];
			
			
			$q_result = $sql->query($q_str);
			$tmp_qty += $un_equal_row['qty'];
			
			$q_str = "select id, qty
					  from stock_mat_acc
					  where ver = '".$ver."' and stock_inventory_id = ".$stock_inventory_id." 
							and id <> ".$un_equal_row['id']." and qty > 0";
			
			
			$m_result = $sql->query($q_str);
			while($m_row = $sql->fetch($m_result)){
				if( $m_row['qty'] + $tmp_qty <= $qty ){
					$q_str = "update stock_mat_acc
						  set qty = 0
						  where id = ".$m_row['id'];
					
					$q_result = $sql->query($q_str);
					$tmp_qty += $m_row['qty'];
				}else{
					$q_str = "update stock_mat_acc
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

} # FUN END
#
#
#
#
#
#get_size_breakdown
function get_size_breakdown($ord_num){
	$sql = $this->sql;
	$rtn = array();
	
	$q_str = "select size_des.`size`
			  from size_des, s_order
			  where size_des.id = s_order.`size` and s_order.order_num = '".$ord_num."'";
	
	$size_result = $sql->query($q_str);
	$size_row = $sql->fetch($size_result);
	$rtn['size_des'] = explode(',', $size_row['size']);
    $rtn['qty_count'] = count($rtn['size_des']);
	
	$q_str = "select id, mks, p_etd, remark
			  from order_partial
			  where ord_num = '".$ord_num."'";
	
	$partial_rsl = $sql->query($q_str);
	
	while($partial_row = $sql->fetch($partial_rsl)){
		$q_str = "select id, colorway
			  from wiqty
			  where p_id = ".$partial_row['id'];
		
		$wiqty_result = $sql->query($q_str);
		while($wiqty_row = $sql->fetch($wiqty_result)){
			$partial_row['wiqty'][] = $wiqty_row;
		}
		$rtn['partial'][] = $partial_row;
	}
	
	return $rtn;
}
#
#
#
#
#
function get_send_num($fty) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "SELECT id, rn_num, ord_num, create_date, status
			  from requi_notify
			  where fty = '".$fty."' and `status` = '4'  
			  order by id desc";
	// echo $q_str.'<br>';
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
#
#
#
#
#
function check_send_num_is_sended($send_num) {
	$sql = $this->sql;
	
	$q_str = "SELECT id
			  from stock_inventory
			  where rn_num = '".$send_num."' and `type` = 'r'
			  limit 0, 1";
	// echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	if($row = $sql->fetch($q_result)){
		return true;
	}else{
		return false;
	}
}
#
#
#
#
#
function get_print_stock_inventory_diff($fty, $mat, $order_num) {
	$sql = $this->sql;
	
	$ver = $this->get_ver( $fty, $mat);
	
	if($ver['confirm_user']){
		$this_ver = $ver['ver'];
		$pre_ver = intval($ver['ver']) - 1;
		if($pre_ver){
			$pre_ver = str_pad($pre_ver, 4, "0",STR_PAD_LEFT);
		}else{
			$pre_ver = '';
		}
	}else{
		return false;
	}
	
	$q_str = "SELECT stock_mat_".$mat.".*, ".$mat.".".$mat."_name as mat_name
			  from stock_mat_".$mat.", ".$mat."
			  where stock_mat_".$mat.".ver = '".$pre_ver."' and stock_mat_".$mat.".".$mat."_id = ".$mat.".id and stock_mat_".$mat.".ord_num='".$order_num."' 
			  order by ".$mat.".".$mat."_name";
	
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
} # END
#
#
#
#
#
#
### STORAGE ###
#
#
#
#
#
function get_storage_zone($fty=''){
	$sql = $this->sql;
	
	$q_str = "SELECT id, zone
			  from storage_zone
			  where fty = '".$fty."'";
	
	if (!$q_result = $sql->query($q_str)) {
		return false;    
	}

	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}
	
	return $rtn;
}
#
#
#
#
#
function append_storage($type, $post, $add_record_flag = false){
	$sql = $this->sql;
	
	foreach($post['PHP_qty'] as $mks => $mks_val){
		foreach($mks_val as $color_key => $val){
			foreach($val as $size_key => $qty){
				if($qty > 0){
					$q_str = '';
						# storage_det
						$q_str = '';
						$parm = array(
									'type'			=>	$type, 
									'storage_id'	=>	$post['storage_id'], 
									'input_user'	=>	$_SESSION['SCACHE']['ADMIN']['login_id'], 
									'input_date'	=>	date("Y-m-d H:i:s"), 
									'ord_num'		=>	$post['PHP_ord_num'], 
									'partial'		=>	$mks, 
									'quality'		=>	'A', 
									'fty_from'		=>	$post['PHP_factory'], 
									'fty_to'		=>	$post['PHP_factory_to'], 
									'zone_from'		=>	$this->get_zone_id($post['PHP_factory'], $post['PHP_zone']), 
									'zone_to'		=>	$this->get_zone_id($post['PHP_factory_to'], $post['PHP_zone_to']), 
									'line_from'		=>	$this->get_line_id($post['PHP_factory'], $post['PHP_line']), 
									'line_to'		=>	$this->get_line_id($post['PHP_factory_to'], $post['PHP_line_to']), 
									'color'			=>	$post['PHP_color'][$mks][$color_key][$size_key], 
									'size'			=>	$post['PHP_size'][$mks][$color_key][$size_key], 
									'qty'			=>	$qty, 
									'p_id'			=>	$post['PHP_p_id'][$mks]
						);
						$q_str = "insert into storage_det
								  set ";
						foreach($parm as $parm_key => $parm_val){
							$q_str .= "`".$parm_key."` = '".$parm_val."',";
						}
						$q_str = substr($q_str, 0, -1);
						
						if($q_result = $sql->query($q_str) and $add_record_flag){
							$parm = array(
										'id'	=>	$post['storage_id'], 
										'ord_num'		=>	$post['PHP_ord_num'], 
										'create_date'	=>	date("Y-m-d h:i:s"), 
										'create_user'	=>	$_SESSION['SCACHE']['ADMIN']['login_id'], 
										'cfm_date'		=>	'', 
										'cfm_user'		=>	'', 
										'status'		=>	0
									);
							$q_str = "insert into storage
									  set ";
							foreach($parm as $parm_key => $parm_val){
								$q_str .= "`".$parm_key."` = '".$parm_val."',";
							}
							$q_str = substr($q_str, 0, -1);
							$q_result = $sql->query($q_str);
						}
					
				}
			}
		}
	}
	
	return true;	
}
#
#
#
#
#
function update_storage_qty($type, $post, $add_record_flag = false){
	$sql = $this->sql;
	
	foreach($post['PHP_qty'] as $mks => $mks_val){
		foreach($mks_val as $color_key => $val){
			foreach($val as $size_key => $qty){
				// if($qty > 0){
                
                    // echo $post['PHP_id'][$mks][$color_key][$size_key].'<br>';
					$q_str = '';
                    
                    # storage_det
                    $q_str = '';
                    $parm = array(
                        'type'			=>	$type,
                        'storage_id'	=>	$post['PHP_sid'],
                        ( $post['PHP_id'][$mks][$color_key][$size_key] > 0 )?'update_user':'input_user'	=>	$_SESSION['SCACHE']['ADMIN']['login_id'],
                        ( $post['PHP_id'][$mks][$color_key][$size_key] > 0 )?'update_date':'input_date'	=>	date("Y-m-d H:i:s"),
                        'ord_num'		=>	$post['PHP_ord_num'],
                        'partial'		=>	$mks,
                        'quality'		=>	'A',
                        'fty_from'		=>	$post['PHP_factory_from'],
                        'fty_to'		=>	$post['PHP_factory_to'],
                        'zone_from'		=>	$post['PHP_zone_from'],
                        'zone_to'		=>	$post['PHP_zone_to'],
                        'line_from'		=>	$post['PHP_line_from'],
                        'line_to'		=>	$post['PHP_line_to'],
                        'color'			=>	$post['PHP_color'][$mks][$color_key][$size_key],
                        'size'			=>	$post['PHP_size'][$mks][$color_key][$size_key],
                        'qty'			=>	$qty,
                        'p_id'			=>	$post['PHP_p_id'][$mks]
                    );
                    
                    if( $post['PHP_id'][$mks][$color_key][$size_key] > 0 )
                        $q_str = "update ";
                    else 
                        $q_str = "insert into ";
                        
                    $q_str .= " storage_det set ";
                    foreach($parm as $parm_key => $parm_val){
                        $q_str .= "`".$parm_key."` = '".$parm_val."',";
                    }
                    $q_str = substr($q_str, 0, -1);
                    
                    if( $post['PHP_id'][$mks][$color_key][$size_key] > 0 )
                        $q_str .= " where id = '".$post['PHP_id'][$mks][$color_key][$size_key]."' ";

                    $q_result = $sql->query($q_str);
                    // echo $q_str.'<br>'.$post['PHP_id'][$mks][$color_key][$size_key];
					
				// }
			}
		}
	}
	
	return true;	
}
#
#
#
#
#
function get_zone_id($fty, $zone_name){
	$sql = $this->sql;
	
	$q_str = "select zone_id
			  from storage_zone
			  where fty = '".$fty."' and zone = '".$zone_name."'";
	
	if (!$q_result = $sql->query($q_str)) {
		return false;    
	}

	$row = $sql->fetch($q_result);
	
	return $row['zone_id'];
}
#
#
#
#
#                           ("'i'", $zone_id, $ord_num, $p_val['mks'], $op['partial'][$p_key]['wiqty'][$i]['colorway'], $s_val);
function get_size_breakdown_qty($type, $fty, $line, $zone, $ord_num, $partial, $color, $size){
	$sql = $this->sql;
	
	$line_id = $this->get_line_id($fty, $line);
	$zone_id = $this->get_zone_id($fty, $zone);
	
	$q_str = "select sum(qty) as qty
			  from storage_det
			  where `type` in(".$type.") and fty_to = '".$fty."' and line_to = ".$line_id." and zone_to = ".$zone_id." and ord_num = '".$ord_num."' and `partial` = '".$partial."' and 
					color = '".$color."' and `size` = '".$size."' and del_mk = 'n'";
	// echo $q_str.'<br>';
	$q_result = $sql->query($q_str);
	if(!$row = $sql->fetch($q_result)){
		$row['qty'] = 0;
	}
	
	# 轉出數量
	$q_str = "select sum(qty) as qty
			  from storage_det
			  where `type` in('t') and line_from = ".$line_id." and zone_from = ".$zone_id." and ord_num = '".$ord_num."' and `partial` = '".$partial."' and 
					color = '".$color."' and `size` = '".$size."' and del_mk = 'n'";
	// echo $q_str.'<br>';
	$q_result = $sql->query($q_str);
	$out_row = $sql->fetch($q_result);
	if(!$out_row['qty']){
		$out_row['qty'] = 0;
	}
	
	# 轉入數量
	
	return $row['qty'] - $out_row['qty'];
}
#
#
#
#
#
function get_size_breakdown_qty_id($type, $sid, $fty, $line_id, $zone_id, $ord_num, $partial, $color, $size){
	$sql = $this->sql;

	$q_str = "select sum(qty) as qty
			  from storage_det
			  where `type` in(".$type.") and fty_to = '".$fty."' and line_to = '".$line_id."' and zone_to = '".$zone_id."' and ord_num = '".$ord_num."' and `partial` = '".$partial."' and 
					color = '".$color."' and `size` = '".$size."' and del_mk = 'n'
    ;";
	
	$q_result = $sql->query($q_str);
	if(!$row = $sql->fetch($q_result)){
		$row['qty'] = 0;
	}
	
	# 轉出所有數量
	$q_str = "select sum(qty) as qty
			  from storage_det
			  where `type` in('t') and line_from = '".$line_id."' and zone_from = '".$zone_id."' and ord_num = '".$ord_num."' and `partial` = '".$partial."' and 
					color = '".$color."' and `size` = '".$size."' and del_mk = 'n'
    ;";
	
	$q_result = $sql->query($q_str);
	$out_row = $sql->fetch($q_result);
    if(!isset($out_row['qty'])){
		$out_row['qty'] = 0;
	}
	
	# 轉出自身數量
	$q_str = "select sum(qty) as qty
			  from storage_det
			  where `type` in('t') and storage_id = '".$sid."' and line_from = '".$line_id."' and zone_from = '".$zone_id."' and ord_num = '".$ord_num."' and `partial` = '".$partial."' and 
					color = '".$color."' and `size` = '".$size."' and del_mk = 'n'
    ;";
	
	$q_result = $sql->query($q_str);
	$out_rows = $sql->fetch($q_result);
	if(!isset($out_rows['qty'])){
		$out_rows['qty'] = 0;
	}
	
	# 轉入數量
	
	return $row['qty'] - $out_row['qty'] + $out_rows['qty'];
}
#
#
#
#
#
function get_transfer_breakdown_qty_id($type, $sid, $fty, $line, $zone, $ord_num, $partial, $color, $size){
	$sql = $this->sql;

	$q_str = "select id,qty
			  from storage_det
			  where `type` in('t') and storage_id = '".$sid."' and fty_to = '".$fty."' and line_to = '".$line."' and zone_to = '".$zone."' and ord_num = '".$ord_num."' and `partial` = '".$partial."' and 
					color = '".$color."' and `size` = '".$size."' and del_mk = 'n'
    ;";
	// echo $q_str.'<br>';
	$q_result = $sql->query($q_str);
	$out_rows = $sql->fetch($q_result);
	if(!isset($out_rows['qty'])){
		$out_rows['qty'] = 0;
		$out_rows['id'] = 0;
	}
    
	return $out_rows;
}
#
#
#
#
#
function get_storage_det($type, $fty, $line, $zone, $ord_num,$TODAY=''){
	$sql = $this->sql;
    
	$line_id = $this->get_line_id($fty, $line);
	$zone_id = $this->get_zone_id($fty, $zone);
	
    $today = $TODAY ? ' AND storage_det.input_date LIKE "'.$TODAY.'%" ': '' ;
	
	$q_str = "select storage_det.zone_to, storage_zone.zone, 
					storage_det.id, storage_det.input_date, storage_det.input_date, storage_det.`partial`, storage_det.color, storage_det.size, storage_det.qty
			  from storage_det, storage_zone
			  where storage_det.`type` in(".$type.") and storage_det.fty_to = '".$fty."' and storage_det.line_to = ".$line_id." 
					and storage_det.zone_to = ".$zone_id." and storage_det.`ord_num` = '".$ord_num."' 
					and storage_zone.zone_id = '".$zone_id."' and storage_zone.fty = '".$fty."' $today and storage_det.del_mk = 'n'
			  order by storage_det.id";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}
	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}
	
	return $rtn;
}
#
#
#
#
#
function get_storage_det4transfer($type, $fty, $line, $zone, $ord_num){
	$sql = $this->sql;
	
	$line_id = $this->get_line_id($fty, $line);
	$zone_id = $this->get_zone_id($fty, $zone);
	# 轉出
	$q_str = "select storage_det.id, storage_det.input_date, storage_det.input_user, storage_det.ord_num,
					 storage_det.fty_from, storage_det.fty_to, storage_det.line_from, storage_det.line_to, storage_det.zone_from, storage_det.zone_to, 
					 `partial`, color, size, qty, storage.status, storage.id as sid
			  from storage_det, storage
			  where storage_det.`type` = 't' and storage_det.fty_from = '".$fty."' and storage_det.line_from = ".$line_id." and storage_det.zone_from = '".$zone_id."' and 
					storage_det.ord_num = '".$ord_num."' and 
					storage.id = storage_det.storage_id and storage.del_mk = 'n'
			  order by storage_det.id";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}
	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn['storage_det_out'][] = $row;
	}

	# 轉入
	$q_str = "select id, input_date, input_user, 
					 fty_from, fty_to, line_from, line_to, zone_from, zone_to, 
					 `partial`, color, size, qty
			  from storage_det
			  where `type` = 't' and fty_to = '".$fty."' and line_to = ".$line_id." and zone_to = '".$zone_id."' and 
					ord_num = '".$ord_num."' and del_mk = 'n'
			  order by id";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}

	while($row = $sql->fetch($q_result)){
		$rtn['storage_det_in'][] = $row;
	}
	
	return $rtn;
}
#
#
#
#
#
function get_transfer($type, $fty, $line, $zone, $ord_num){
	$sql = $this->sql;
	
	$line_id = $this->get_line_id($fty, $line);
	$zone_id = $this->get_zone_id($fty, $zone);
	
	$q_str = "select distinct storage.id, storage.ord_num, storage.create_date, storage.create_user, storage.status, 
					 storage_det.fty_from, storage_det.fty_to, storage_det.line_from, storage_det.line_to, storage_det.zone_from, storage_det.zone_to 
			  from storage_det, storage
			  where storage.id = storage_det.storage_id and storage_det.`type` = 't' and 
					storage_det.fty_to = '".$fty."' and storage_det.line_to = ".$line_id." and storage_det.zone_to = '".$zone_id."' and 
					storage_det.ord_num = '".$ord_num."' and storage.del_mk = 'n'
			  order by storage.id";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}
	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn['storage_det_in'][] = $row;
	}
	
	return $rtn;
}
#
#
#
#
#
function get_transfer_id($sid){
	$sql = $this->sql;
	
	$q_str = "select distinct storage.id, storage.ord_num, storage.create_date, storage.create_user, storage.status, 
					 storage_det.fty_from, storage_det.fty_to, storage_det.line_from, storage_det.line_to, storage_det.zone_from, storage_det.zone_to 
			  from storage_det, storage
			  where storage.id = storage_det.storage_id and storage_det.`type` = 't' and 
					storage.id = '".$sid."' and storage.del_mk = 'n'
			  order by storage.id";
	
	if(!$q_result = $sql->query($q_str)){
		return false;
	}
	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn['storage_det_in'][] = $row;
	}
	
	return $rtn;
}
#
#
#
#
#
function delete_transfer_id($type,$sid){
	$sql = $this->sql;
	
    $q_str = "update storage set `del_mk` = '".$type."' where id = ".$sid;
	if(!$det_rsl = $sql->query($q_str)){
        $_SESSION['MSG'][] = "Error! Can't access database! storage ";
		return false;
	}
	$q_str = "update storage_det set `del_mk` = '".$type."' where storage_id = ".$sid;
	if(!$det_rsl = $sql->query($q_str)){
        $_SESSION['MSG'][] = "Error! Can't access database! storage_det ";
		return false;
	}
	
	return true;
}
#
#
#
#
#
function delete_storage_qty($type, $det_id){
	$sql = $this->sql;
	
	# 先取資料 要寫入storage log檔
	$q_str = "update storage_det
			  set `type` = '".$type."'
			  where id = ".$det_id;
	
	if($det_rsl = $sql->query($q_str)){
		return true;
	}else{
		return false; 
	}
	
}
#
#
#
#
#
function get_fty_line($fty){
	$sql = $this->sql;
	
	$q_str = "select id, line
			  from pdt_saw_line
			  where fty = '".$fty."' and del_mk = 0
			  order by line asc";
	
	$line_rsl = $sql->query($q_str);
	$rtn = array();
	while($line_row = $sql->fetch($line_rsl)){
		$rtn[] = $line_row;
	}
	
	return $rtn;
}
#
#
#
#
#
function get_line_zone($fty,$line){
	$sql = $this->sql;
    
    $line_id = $this->get_line_id($fty, $line);
	
	$q_str = "select storage_zone.zone
			  from storage_det , storage_zone
			  where storage_det.fty = '".$fty."' and storage_det.line_id = '".$line_id."' and  storage_det.zone_id = storage_zone.id
			  GROUP BY storage_zone.zone";
	
	$zone_rsl = $sql->query($q_str);
	$zone = array();
	while($zone_row = $sql->fetch($zone_rsl)){
		$zone[] = $zone_row;
	}
	return $zone;
}
#
#
#
#
#
function get_line_id($fty, $line_name){
	$sql = $this->sql;
	
	$q_str = "select id
			  from pdt_saw_line
			  where fty = '".$fty."' and line = '".$line_name."'";
	
	$line_rsl = $sql->query($q_str);
	if($line_row = $sql->fetch($line_rsl)){
		$rtn[] = $line_row;
	}
	return $line_row['id'];
}#
#
#
#
#
function get_storage_order_num($storage='', $ord_num=''){

	$sql = $this->sql;
    
    $str_storage = !empty($storage) ? " AND storage.ord_num LIKE '%".$storage."%' " : "";
    $str_ord_num = !empty($ord_num) ? " AND storage_det.ord_num LIKE '%".$ord_num."%' " : "";
	
	$q_str = "
    select storage.* , storage_det.*
    from storage , storage_det
    where storage_det.storage_id = storage.id ".$str_storage.$str_ord_num." 
    ";
	
	$str = $sql->query($q_str);
	if($row = $sql->fetch($str)){
		$rtn[] = $row;
	}
	return $rtn;
}
#
#
#
#
#
} # CLASS END
?>