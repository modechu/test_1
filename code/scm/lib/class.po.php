<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->bom_search($supl,$cat)	查詢BOM的主副料


#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class po {
		
	var $sql;
	var $msg ;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
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


    
/*----------
# 功能說明 : 重新開啟已 APV 的 PO
----------*/
function reopen($po_num) {

    $sql = $this->sql;

    $q_str="UPDATE `ap` SET `status` = '8',`po_cfm_date` = '',`po_cfm_user` = '',`po_apv_date` = '',`po_apv_user` = '' WHERE `po_num` = '".($po_num)."' ;";
    
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
    
    return true;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $smpl_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_po($id,$state='') {

	$sql = $this->sql;

	$q_str="SELECT * FROM `ap_det` WHERE 1 AND `used_id` = '".$id."';";
		   

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Can't access DB!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row = $sql->fetch($q_result)) {
		if($state == 'po')
		{
			if($row['ap_mark']) return 1;
		}else{
			return 1;
		}
	}
	
	return 0;
	
} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->check_po_in($id=0, $smpl_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_po_in($id,$mat) {

	$sql = $this->sql;

	$q_str="SELECT * FROM `ap_det` WHERE `mat_cat` = '".$mat."' AND `used_id` = '".$id."' ;";
		   

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Can't access DB!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row = $sql->fetch($q_result)) {
		return 1;
	}
	
	return 0;
	
} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_use($order_num)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_lots_use($order_num) {

	$sql = $this->sql;
	
	$lots = array();
	
	$q_str="
	SELECT lots_use.* , SUM(ap_det.ap_qty) as sum_po
 
	FROM lots_use LEFT JOIN ap_det ON (lots_use.id = ap_det.used_id) LEFT JOIN ap ON ( ap_det.ap_num = ap.ap_num )

	WHERE lots_use.smpl_code = '".$order_num."' 
	
	GROUP BY lots_use.id

	";
    // echo '<p>'.$q_str.'<br>';
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$lots[]=$row1;
	}

	return $lots;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_use($order_num)	抓出指定記錄 bom 副料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_acc_use($order_num) {

	$sql = $this->sql;
	$acc = array();
	
	$q_str="
	SELECT acc_use.* , SUM(ap_det.ap_qty) as sum_po
 
	FROM acc_use LEFT JOIN ap_det ON (acc_use.id = ap_det.used_id) LEFT JOIN ap ON ( ap_det.ap_num = ap.ap_num )

	WHERE acc_use.smpl_code = '".$order_num."' 
	
	GROUP BY acc_use.id

	";
			
	// echo '<p>'.$q_str.'<br>'; #ap_qty
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$acc[]=$row1;
	}

	return $acc;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_lots_det($id=0) {

	$sql = $this->sql;
	
	$lots = array();
	
	$q_str="SELECT DISTINCT 
			bom_lots.*,
			lots_use.lots_code, lots_use.lots_name, lots_use.unit, lots_use.use_for, lots_use.est_1, lots_use.id as use_id, lots_use.support ,
			ap.status, ap.id as ap_id, ap.po_num, ap.currency, ap.ann_num , ap.special ,  ap.sup_code , 
			wi.wi_num , 
			ap_det.ap_num ,
			lots.vendor1 , 
            
			wi.wi_num as order_num ,
			wi.id as wi_id ,
            bom_lots.id as bom_id , 
            lots.id as mat_id ,
            lots_use.id as used_id 
            
			FROM 
			bom_lots, lots_use 
			LEFT JOIN ap_det ON ( bom_lots.id = ap_det.bom_id AND ap_det.mat_cat = 'l' )
			LEFT JOIN ap ON ap.ap_num = ap_det.ap_num 
			LEFT JOIN wi ON bom_lots.wi_id = wi.id 
			LEFT JOIN lots ON lots.lots_code = lots_use.lots_code
			
			WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND bom_lots.wi_id='".$id."' 
			
			GROUP BY bom_lots.id
			
			";

    // echo '<p>'.$q_str.'<br>';
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {

		# BOM Q'TY	
		$qty=explode(',',$row1['qty']);
		$row1['bom_qty'] = array_sum($qty);
		
		# SHIP / PO / TW RCV / RCV / APB Q'TY       $ap_num,$mat_cat,$mat_id,$color,$size
		$row1['po'] = $this->get_po_qty($row1['id'],$row1['wi_num'],$row1['special'],$row1['lots_code'],$row1['ap_num'],'l',$row1['mat_id'],$row1['color'],$row1['size']);	
		// print_r($row1['po']);
		# 正常採購
		if($row1['support'] == '0') $lots['bom'][]=$row1;
		
		# 工廠採購
		if($row1['support'] == '1')	$lots['fty'][]=$row1;
		
		# 客人提供
		if($row1['support'] == '2') $lots['cust'][]=$row1;
	}

	return $lots;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_det($id=0)	抓出指定記錄 bom 副料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_acc_det($id=0) {

	$sql = $this->sql;
	$acc = array();
	
	$q_str="SELECT DISTINCT 
			bom_acc.*,
			acc_use.acc_code, acc_use.acc_name, acc_use.unit, acc_use.use_for, acc_use.acc_cat, acc_use.est_1, acc_use.support ,
			ap.status, ap.id as ap_id, ap.po_num, ap.currency, ap.ann_num , ap.special , ap.sup_code , 
			wi.wi_num , 
			ap_det.ap_num ,
			acc.vendor1 ,
            
			wi.wi_num as order_num ,
			wi.id as wi_id ,
            bom_acc.id as bom_id , 
            acc.id as mat_id ,
            acc_use.id as used_id 
 
			FROM 
			bom_acc, acc_use 
			LEFT JOIN ap_det ON ( bom_acc.id = ap_det.bom_id AND ap_det.mat_cat = 'a' )
			LEFT JOIN ap ON ap.ap_num = ap_det.ap_num 			
			LEFT JOIN wi ON bom_acc.wi_id = wi.id
			LEFT JOIN acc ON acc.acc_code = acc_use.acc_code
			
			WHERE 
			bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 AND bom_acc.wi_id='".$id."' 
			
			GROUP BY bom_acc.id
			";
			
	// echo '<p>'.$q_str.'<br>'; #ap_qty
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {

		# BOM Q'TY	
		$qty=explode(',',$row1['qty']);
		$row1['bom_qty'] = array_sum($qty);
		
		# SHIP / PO / TW RCV / RCV / APB Q'TY 
		$row1['po'] = $this->get_po_qty($row1['bom_id'],$row1['wi_num'],$row1['special'],$row1['acc_code'],$row1['ap_num'],'a',$row1['mat_id'],$row1['color'],$row1['size']);
		
		# 正常採購
		if($row1['support'] == '0') $acc['bom'][]=$row1;
		
		# 工廠採購
		if($row1['support'] == '1') $acc['fty'][]=$row1;
		
		# 客人提供
		if($row1['support'] == '2') $acc['cust'][]=$row1;
	}

	return $acc;
} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_status($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_lots_status($id=0) {

	$sql = $this->sql;
	
	$lots = array();
	
	$q_str="SELECT DISTINCT 
			bom_lots.*,
			lots_use.lots_code, lots_use.lots_name, lots_use.unit, lots_use.use_for, lots_use.est_1, lots_use.id as use_id, lots_use.support ,
			ap.status, ap.id as ap_id, ap.po_num, ap.currency, ap.ann_num , ap.special ,  ap.sup_code , 
			wi.wi_num , 
			ap_det.ap_num ,
			lots.vendor1 , 
            
			wi.wi_num as order_num ,
			wi.id as wi_id ,
            bom_lots.id as bom_id , 
            lots.id as mat_id ,
            lots_use.id as used_id 
            
			FROM 
			bom_lots, lots_use 
			LEFT JOIN ap_det ON ( bom_lots.id = ap_det.bom_id AND ap_det.mat_cat = 'l' )
			LEFT JOIN ap ON ap.ap_num = ap_det.ap_num 
			LEFT JOIN wi ON bom_lots.wi_id = wi.id 
			LEFT JOIN lots ON lots.lots_code = lots_use.lots_code
			
			WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND bom_lots.wi_id='".$id."' 
			
			GROUP BY bom_lots.id
			
			";

    
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {

		# BOM Q'TY	
		$qty=explode(',',$row1['qty']);
		$row1['bom_qty'] = array_sum($qty);
		
		# SHIP / PO / TW RCV / RCV / APB Q'TY 
        // if( $row1['status'] == '12' ) {
		$row1['po'] = $this->get_po_supl_qty($row1['bom_id'], 'l', $row1['mat_id'], $row1['color'], $row1['size'], $row1['wi_num'], $row1['ap_num']);	
		
		# 正常採購
		if($row1['support'] == '0') $lots['bom'][]=$row1;
		
		# 工廠採購
		if($row1['support'] == '1')	$lots['fty'][]=$row1;
		
		# 客人提供
		if($row1['support'] == '2') $lots['cust'][]=$row1;
        // }
	}

	return $lots;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_status($id=0)	抓出指定記錄 bom 副料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_acc_status($id=0) {

	$sql = $this->sql;
	$acc = array();
	
	$q_str="SELECT DISTINCT 
			bom_acc.*,
			acc_use.acc_code, acc_use.acc_name, acc_use.unit, acc_use.use_for, acc_use.acc_cat, acc_use.est_1, acc_use.support ,
			ap.status, ap.id as ap_id, ap.po_num, ap.currency, ap.ann_num , ap.special , ap.sup_code , 
			wi.wi_num , 
			ap_det.ap_num ,
			acc.vendor1 ,
            
			wi.wi_num as order_num ,
			wi.id as wi_id ,
            bom_acc.id as bom_id , 
            acc.id as mat_id ,
            acc_use.id as used_id 
 
			FROM 
			bom_acc, acc_use 
			LEFT JOIN ap_det ON ( bom_acc.id = ap_det.bom_id AND ap_det.mat_cat = 'a' )
			LEFT JOIN ap ON ap.ap_num = ap_det.ap_num 			
			LEFT JOIN wi ON bom_acc.wi_id = wi.id
			LEFT JOIN acc ON acc.acc_code = acc_use.acc_code
			
			WHERE 
			bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 AND bom_acc.wi_id='".$id."' 
			
			GROUP BY bom_acc.id
			";
			
	
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		
		# BOM Q'TY	
		$qty=explode(',',$row1['qty']);
		$row1['bom_qty'] = array_sum($qty);
		
		# SHIP / PO / TW RCV / RCV / APB Q'TY 
		$row1['po'] = $this->get_po_supl_qty($row1['bom_id'], 'a', $row1['mat_id'], $row1['color'], $row1['size'], $row1['wi_num'], $row1['ap_num']);	
		
		# 正常採購
		if($row1['support'] == '0') $acc['bom'][]=$row1;
		
		# 工廠採購
		if($row1['support'] == '1') $acc['fty'][]=$row1;
		
		# 客人提供
		if($row1['support'] == '2') $acc['cust'][]=$row1;
	}

	return $acc;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_po_supl_qty($id=0) 丟出 bom_lots,bom_acc 的 ID 抓出所有關 bom_id 的資料 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_supl_qty($bom_id, $mat, $mat_id, $color, $size, $ord_num, $ap_num){
	# special => ( 3 未沖 ) , ( 4 已沖 )
    
	// $bom = $special == '2' ? " `ap_det`.`ord_num` = '".$wi_num."' AND `ap_det`.`mat_code` = '".$mat_code."' AND `ap_det`.`color` = '".$color."' AND `ap_det`.`size` = '".$size."' " : " `ap_det`.`bom_id` = '".$bom_id."' " ;
    $sql = $this->sql;
	
	$q_str = "SELECT DISTINCT
			`ap`.`id` as `ap_id`, `ap`.`ap_num` , `ap`.`po_num` , `ap`.`status` , `ap`.`special` , `ap_det`.`mat_cat` , `ap_det`.`mat_id` , `ap_det`.`color` , `ap_det`.`size` , 
			`ap_det`.`id` as `po_id` , `ap_det`.`po_qty` , `ap_det`.`po_unit` as `unit` , `ap_det`.`prc_unit` , `ap_det`.`po_eta` , `ap_det`.`rev_eta` , 
			`ap_det`.`amount` as `po_amount` , `ap_det`.`prics` as `price` 
            
			FROM  `ap_det` left join `ap` on `ap_det`.`ap_num` = `ap`.`ap_num` 
			
			WHERE `ap_det`.`bom_id` = '".$bom_id."' and `ap_det`.`mat_cat` = '".$mat."' AND `ap_det`.`mat_id` = '".$mat_id."' AND `ap_det`.`color` = '".$color."' AND `ap_det`.`size` = '".$size."' 
			
			GROUP BY `ap`.`po_num`
			;";
	
	if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;
    }

    $po_ary = $rtn_ary = $ship_ary = array();
	while($row1 = $sql->fetch($q_result))
    {
		
		# PO
		$ary_qty = $this->get_po_qtys($row1['ap_num'],$bom_id);
		$rtn_ary['po_qty'] += $ary_qty['qty'];
		$rtn_ary['po_uqty'] += $ary_qty['uqty'];
		$rtn_ary['po_fqty'] += $ary_qty['fqty'];
		
		# SHIP
		$ship_supl_ary = $this->get_po_supl_ship_qtys($row1['ap_id'],$row1['mat_cat'],$row1['mat_id'],$row1['color'],$row1['size']);
		foreach($ship_supl_ary as $key => $val){
			$rtn_ary['ship_qty'] += $val['qty'];
			$row1['ship_way'] = $val['ship_way'];
			$row1['ship_date'] = $val['ship_date'];
			$row1['ship_eta_date'] = $val['ship_eta_date'];
		}
		
		
		# TW SHIP         
		$rtn_ary['tw_ship_qty'] += $this->get_po_ship_det_qtys($row1['ap_id'],$row1['mat_cat'],$row1['mat_id'],$row1['color'],$row1['size']);		
		
		if (!in_array($row1['po_num'], $po_ary)) {
			$po_ary[] = $row1['po_num'];
			$ship_ary['po_num'] = $row1['po_num'];
			$ship_ary['ap_num'] = str_replace("PO", "PA", $row1['po_num']);
			$ship_ary['ship_way'] = $row1['ship_way'];
			$ship_ary['ship_date'] = $row1['ship_date'];
			$ship_ary['ship_eta_date'] = $row1['ship_eta_date'];
			$ship_ary['special'] = $row1['special'];
			$ship_ary['statuss'] = get_po_status($row1['status']);
			$ship_ary['ap_id'] = $row1['ap_id'];
			$ship_ary['mat_cat'] = $row1['mat_cat'];
			$ship_ary['mat_id'] = $row1['mat_id'];
			$ship_ary['color'] = $row1['color'];
			$ship_ary['size'] = $row1['size'];
			$ship_ary['unit'] = $row1['unit'];
			$rtn_ary['ship'][] = $ship_ary;
		}
		
		$rtn_ary['po_nums'] .= $row1['po_num'].",";
		
		# FTY RCV
		$rtn_ary['rcv_qty'] += $this->get_po_supl_rcv_qtys($row1['po_num'],$row1['mat_cat'],$row1['mat_id'],$row1['color'],$row1['size']);
		
		# TW RCV
		$rtn_ary['tw_rcv_qty'] += $this->get_po_supl_tw_rcv_qtys($row1['po_num'],$row1['mat_cat'],$row1['mat_id'],$row1['color'],$row1['size']);
		
		# PRE APB
		$rtn_ary['pre_apb_qty'] += $this->get_apb_qtys($bom_id, $row1['ap_num'], $ord_num, "2,14");
		
		# APB
		$rtn_ary['apb_qty'] += $this->get_apb_qtys($bom_id, $row1['ap_num'], $ord_num, "4,6,8,10,12,16");
		
		
	}
	
	$rtn_ary['po_nums'] = substr($rtn_ary['po_nums'], 0, -1);
	return $rtn_ary;

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_po_qty($id=0) 丟出 bom_lots,bom_acc 的 ID 抓出所有關 bom_id 的資料 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_qty($bom_id=0,$wi_num='',$special,$mat_code,$ap_num,$mat_cat,$mat_id,$color,$size) {
	# ( mat_code , color ) 在 ap.special = 2 的時候才會用到，等之後全都採用 ap_det , 取消 ap_special 時才可以取消該判斷式
	# special => ( 3 未沖 ) , ( 4 已沖 )
    $ap_num=array();
    $sql = $this->sql;
	
	$tbl = $special == '2' ? 'ap_special' : 'ap_det' ;
	$bom = $special == '2' ? " `".$tbl."`.`ord_num` = '".$wi_num."' AND `".$tbl."`.`mat_code` = '".$mat_code."' AND `".$tbl."`.`color` = '".$color."' AND `".$tbl."`.`size` = '".$size."' " : " `".$tbl."`.`bom_id` = '".$bom_id."' " ;

    $q_str="SELECT DISTINCT
			`ap`.`ap_num` , `ap`.`po_num` , `ap`.`status` , `ap`.`ann_num` , `ap`.`special` , 
			`".$tbl."`.`id` as `po_id` , `".$tbl."`.`po_qty` , `".$tbl."`.`po_unit` as `unit` , `".$tbl."`.`prc_unit` , `".$tbl."`.`po_spare` , `".$tbl."`.`po_eta` , `".$tbl."`.`rev_eta` , 
			`po_ship`.`ship_date` , `po_ship`.`ship_eta` , `po_ship`.`ship_way` , `".$tbl."`.`amount` as `po_amount` , `".$tbl."`.`prics` as `price` , 
             `".$tbl."`.`mat_cat` , `".$tbl."`.`mat_id` , `".$tbl."`.`color` , `".$tbl."`.`size` 
			
			FROM  `".$tbl."` left join `ap` on `".$tbl."`.`ap_num` = `ap`.`ap_num` 
            left join `po_ship_det` on ( `po_ship_det`.`ap_num` = `".$tbl."`.`ap_num` AND `po_ship_det`.`mat_id` = `".$tbl."`.`mat_id` AND `po_ship_det`.`color` = `".$tbl."`.`color` AND `po_ship_det`.`size` = `".$tbl."`.`size` )
            left join `po_ship` on `po_ship`.`id` = `po_ship_det`.`ship_id`
			
			WHERE `".$tbl."`.`mat_cat` = '".$mat_cat."' AND ".$bom." 
			
			GROUP BY `ap`.`po_num`
			;";
    // echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;
    }

    $rtn_ary = $ship_ary = $po_ary = array();
	while($row1 = $sql->fetch($q_result))
    {

		# PO
		$ary_qty = $this->get_po_qtys($row1['ap_num'],$bom_id);
		$rtn_ary['po_qty'] += $ary_qty['qty'];
		$rtn_ary['po_uqty'] += $ary_qty['uqty'];
		$rtn_ary['po_fqty'] += $ary_qty['fqty'];
		
		# SHIP
		$rtn_ary['ship_qty'] += $this->get_ship_qtys($row1['ap_num'],$row1['mat_cat'],$row1['mat_id'],$row1['color'],$row1['size']);		
		
		# TW RCV
		$ary_qty = $this->get_rcv_qtys($row1['ap_num'],$row1['po_id'],$row1['po_spare'],$wi_num,1);
		$rtn_ary['tw_rcv_qty'] += $ary_qty['qty'];
		$rtn_ary['tw_rcv_uqty'] += $ary_qty['uqty'];
		
		# FTY RCV
		$ary_qty = $this->get_rcv_qtys($row1['ap_num'],$row1['po_id'],$row1['po_spare'],$wi_num,0);
		$rtn_ary['rcv_qty'] += $ary_qty['qty'];
		$rtn_ary['rcv_uqty'] += $ary_qty['uqty'];
		
		# PRE APB
		$rtn_ary['pre_apb_qty'] += $this->get_apb_qtys($row1['po_id'],$wi_num,1);
		
		# APB
		$rtn_ary['apb_qty'] += $this->get_apb_qtys($row1['po_id'],$wi_num,0);
		
		$rtn_ary['po_id'] = $row1['po_id'];
		$rtn_ary['po_spare'] = $row1['po_spare'];
		$rtn_ary['unit'] = $row1['unit'];
		if(!$row1['prc_unit'])$rtn_ary['prc_unit'] = $row1['unit'];
		$rtn_ary['po_amount'] = $row1['po_amount'];
		$rtn_ary['price'] = $row1['price'];

		
		# SHIP
		$ship = array();
		if(!in_array($po_ary[$row1['po_num']])){
			$po_ary[] = $row1['po_num'];
			$ship['ap_num'] = $row1['ap_num'];
			$ship['po_num'] = $row1['po_num'];
			$ship['po_qty'] = $row1['po_qty'];
			$ship['po_fqty'] = $row1['po_fqty'];
			$ship['unit'] = $row1['unit'];
			$ship['eta'] = ($row1['rev_eta'] != '0000-00-00')?$row1['rev_eta']:$row1['po_eta'];
			$ship['status'] = $row1['status'];
			$ship['statuss'] = get_po_status($row1['status']);
			$ship['ann_num'] = $row1['ann_num'];
			$ship['ship_way'] = $row1['ship_way'];
			$ship['ship_eta'] = $row1['ship_eta'];
			$ship['ship_date'] = $row1['ship_date'];
			$ship['po_spare'] = $row1['po_spare'];
			$ship['special'] = $row1['special'];
			$ship_ary[] = $ship;
		}
		
	}
	
	# MASK
	if($pp_mark == 0){
		$rtn_ary['det_special'] = 0;
	} else {			
		$rtn_ary['det_special'] = 1;
	}

	$rtn_ary['ship'] = $ship_ary;
    // print_r($rtn_ary['ship']);
	return $rtn_ary;

} // end func



function get_po_supl_ship_qtys($ap_id,$mat_cat,$mat_id,$color,$size) {

    $sql = $this->sql;
	
	$ship_ary = array();
	
    $q_str = "SELECT `po_supl_ship`.`ship_date`, `po_supl_ship`.`ship_eta_date`, `po_supl_ship`.`ship_way` ,sum( `po_supl_ship_link`.`qty` ) AS `qty` 
			  FROM `po_supl_ship` , `po_supl_ship_link` 
			  WHERE `po_supl_ship`.`id` = `po_supl_ship_link`.`po_supl_ship_id` AND 
					`po_supl_ship_link`.`ap_id` = '".($ap_id)."' AND 
					`po_supl_ship_link`.`mat_cat` = '".($mat_cat)."' AND 
					`po_supl_ship_link`.`mat_id` = '".($mat_id)."' AND 
					`po_supl_ship_link`.`color` = '".($color)."' AND 
					`po_supl_ship_link`.`size` = '".($size)."' AND 
					`po_supl_ship`.`status` = '1' and `po_supl_ship`.`del_mk` = 'n'
					  
					GROUP BY `po_supl_ship_link`.`po_supl_ship_id` ; ";
	
	
	
    $q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		$ship_ary[] = $row;
	}

    return $ship_ary;

}



function get_ship_qtys($ap_num,$mat_cat,$mat_id,$color,$size) {

    $sql = $this->sql;
	
	$qty = 0;

    $q_str = "SELECT 
	DISTINCT sum( `po_ship_det`.`qty` ) AS `qty` , `po_ship`.`status`
	
    FROM 
    `po_ship_det` , `po_ship`
    
    WHERE 
	`po_ship`.`id` = `po_ship_det`.`ship_id` AND 
	
	`po_ship_det`.`ap_num` = '".($ap_num)."' AND 
	`po_ship_det`.`mat_cat` = '".($mat_cat)."' AND 
	`po_ship_det`.`mat_id` = '".($mat_id)."' AND 
	`po_ship_det`.`color` = '".($color)."' AND 
	`po_ship_det`.`size` = '".($size)."' AND 
    
	`po_ship`.`status` = '2' 
      
    GROUP BY `po_ship`.`id` ; ";
	
	// echo '<p>'.$q_str.'<br>';
	
    $q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		$qty += $row['qty'];
	}

    return $qty;

}


function get_po_qtys($ap_num,$bom_id) {

    $sql = $this->sql;
	
	$qty = $uqty = 0;

    $q_str = "SELECT 
	DISTINCT sum( `ap_det`.`po_qty` ) AS `qty` , `ap`.`status`
	
    FROM 
    `ap` , `ap_det` 
    
    WHERE 
	`ap`.`ap_num` = `ap_det`.`ap_num` AND 
	`ap_det`.`ap_num` = '".($ap_num)."' AND 
	`ap_det`.`bom_id` = '".($bom_id)."' 
      
    GROUP BY `ap_det`.`id` ; ";
	
	// echo '<p>'.$q_str.'<br>';
	
    $q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		if ( $row['status'] == '12' )
			$qty += $row['qty'];
		else 
			$uqty += $row['qty'];

		# 不知道有啥用途，忘記了 保留
		$fqty += $row['qty'];
	}

    return array( 'qty' => $qty , 'uqty' => $uqty , 'fqty' => $fqty );

}



function get_po_supl_rcv_qtys($po_num,$mat_cat,$mat_id,$color,$size) {

    $sql = $this->sql;
	
	$qty = 0;

    $q_str = "select sum(qty) as qty
			  from stock_inventory
			  where type = 'i' and po_num = '".$po_num."' and mat_cat = '".$mat_cat."' and mat_id = ".$mat_id."
					and color = '".$color."' and size = '".$size."'";
	
    $q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		$qty += $row['qty'];
	}

    return $qty;

}





function get_rcv_qtys($ap_num,$po_id,$bom_id,$ord_num,$tw_rcv) {

    $sql = $this->sql;
	
	$qty = $uqty = 0;

    $q_str = "SELECT 
	DISTINCT sum( `rcv_po_link`.`qty` ) AS `qty` , `receive`.`status`
	
    FROM 
    `rcv_po_link` , `receive_det` ,`receive`
    
    WHERE 
	`receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
	`receive_det`.`id` = `rcv_po_link`.`rcv_id` AND 
	
	`receive_det`.`ap_num` = '".($ap_num)."' AND 
	`receive_det`.`po_id` = '".($bom_id)."' AND 
	`rcv_po_link`.`po_id` = '".($po_id)."' AND 
	`rcv_po_link`.`ord_num` = '".($ord_num)."' AND 
	`receive`.`tw_rcv` = '".($tw_rcv)."'
      
    GROUP BY `receive`.`po_id` ; ";
	
	// echo '<p>'.$q_str.'<br>';
	
    $q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		if ( $row['status'] == '4' )
			$qty += $row['qty'];
		else 
			$uqty += $row['qty'];
	}

    return array( 'qty' => $qty , 'uqty' => $uqty );

}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_apb_qtys($id=0)	抓出 APB 數量 ** by mode **
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_apb_qtys($bom_id, $ap_num, $ord_num, $status) {
	$sql = $this->sql;

	$q_str = "SELECT sum(`apb_po_link`.`qty` ) as qty
			  FROM `apb_po_link` , `apb_det` , `apb`, `ap_det`
			  WHERE `apb_det`.`ap_num` = '".$ap_num."' and ap_det.ap_num = apb_det.ap_num and 
					 ap_det.bom_id = '".$bom_id."' and apb_po_link.po_id = ap_det.id and `apb_po_link`.`ord_num` = '".$ord_num."' AND
					`apb_det`.`id` = `apb_po_link`.`rcv_id` AND
					`apb`.`rcv_num` = `apb_det`.`rcv_num` AND
					`apb`.`status` in (".$status.") ";

	$q_result = $sql->query($q_str);
	if($row = $sql->fetch($q_result))
		return $row['qty'];
	else
		return false;

} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pp_apb_qtys($id=0)	抓出 APB 數量 ** by mode **
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pp_apb_qtys($po_id,$ord_num,$mode=0) {
	$sql = $this->sql;

	$apb_qty = 0;
	
	$status = $mode == 0 ? 
	" AND ( ( `apb`.`status` = '2' and `apb`.`payment` not like '%before%' ) or ( `apb`.`status` = '4' ) ) "
	: 
	" AND ( `apb`.`status` = '2' and `apb`.`payment` like '%before%' ) ";

	$q_str = "
	SELECT `apb_po_link`.`qty` , apb.rcv_num , apb.status , apb.payment 
	
	FROM `apb_po_link` , `apb_det` , `apb` 
	
	WHERE `apb_po_link`.`po_id` = '".$po_id."' AND
	`apb_det`.`id` = `apb_po_link`.`rcv_id` AND
	`apb`.`rcv_num` = `apb_det`.`rcv_num` AND
	`apb_po_link`.`ord_num` = '".$ord_num."' ".$status." ;";

	$q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		$rcv_num = $row['rcv_num'];
		$status = $row['status'];
		$payment = $row['payment'];
		$apb_qty += $row['qty'];
	}

	return array( 'apb_qty' => $apb_qty , 'rcv_num' => $rcv_num , 'status' => $status , 'payment' => $payment );

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_rcv_qty($det_id,$wi_num,$id,$tw_rcv=0)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_rcv_qty($det_id,$wi_num,$id,$tw_rcv=0) {

    $sql = $this->sql;

    $q_str = "SELECT DISTINCT sum( `rcv_po_link`.`qty` ) AS `qty` 
    FROM 
    `rcv_po_link` , `receive_det` ,`receive`
    
    WHERE 
    `receive_det`.`po_id` = '".($det_id)."' AND 
	`receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
	`receive`.`tw_rcv` = '".$tw_rcv."' AND 
    `receive_det`.`id` = `rcv_po_link`.`rcv_id` AND 
    `rcv_po_link`.`po_id` = '".($id)."' AND 
    `rcv_po_link`.`ord_num` = '".($wi_num)."' AND 
    `receive`.`status` = '4' 
    
    ORDER BY `rcv_po_link`.`po_id` ; ";

    $q_result = $sql->query($q_str);
    $row = $sql->fetch($q_result);
    $qty = $row['qty'];

    return $qty;
    
}


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_price($id=0)	抓出一般請購量等資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_price($id=0,$mat) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT ap_det.po_qty, ap_det.prics, ap.currency, ap.po_apv_date, ap_det.amount
						FROM ap_det, ap 
						WHERE ap.ap_num = ap_det.ap_num AND ap_det.mat_cat ='".$mat."' AND bom_id ='".$id."' 
								AND ap.status >= 0";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$po_price=0;
		$rate_mk = 0;
		while($row1 = $sql->fetch($q_result))
		{
			if ($row1['currency'] <> 'NTD')				
					$row1['prics'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['po_apv_date'],$row1['prics']);
			if($row1['amount'] > 0) 
			{
				$po_price += $row1['amount'];
			}else{
				$po_price += $row1['prics'] * $row1['po_qty'];			
			}
			if($row1['prics'] == 0) $rate_mk = 1;
		}
		if($rate_mk == 1)$po_price=0;
		return $po_price;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pp_qty($id=0)	抓出預先請購量等資料 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pp_qty($id=0,$ap_num,$mat,$code,$color,$wi_num='') {

    $ap_mark = explode(',',$ap_num);
    $ap_n = 0;
    $ap_str = '';
    foreach($ap_mark as $key){
        $ap_or = $ap_n > 0 ? " or " : "" ;
        $ap_str .= " ".$ap_or." ap.ap_num = '".$key."' ";
        $ap_n ++;
    }

    $sql = $this->sql;
    $q_str="SELECT ap_special.rcv_qty, ap_special.po_qty as qty, po_unit as unit, 
                             ap_special.eta, (ap_special.prics*ap_special.po_qty) as po_amount, 
                             ap_special.prics as price, ap.currency, ap.po_apv_date, ap_special.id,
                             ap_special.amount, ap_special.prc_unit, ap.ap_num, 
                             ap_special.ship_way, ap_special.ship_date, ap_special.ship_eta,
                             ap_special.id as det_id, ap_special.ship_rmk
                FROM ap_special, ap
                WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_cat ='".$mat."' 
                            AND ( ".$ap_str." ) AND mat_code ='".$code."' AND ap.status >= 0
                            AND ap_special.color = '".$color."'";
    // echo $q_str."<br>";
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }		
    $row1 = $sql->fetch($q_result);
    if($row1['amount'] > 0)$row1['po_amount'] = $row1['amount'];

/*
    if ($row1['currency'] <> 'NTD')				
            $row1['po_amount'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['po_apv_date'],$row1['po_amount']);
    

//其他一般追加請/採購
    $q_str="SELECT ap_det.rcv_qty, ap_det.po_qty as qty, po_unit as unit, ap_det.eta, 
                   sum(ap_det.prics*ap_det.po_qty) as po_amount, ap_det.prics as price,
                   ap.currency, ap.po_apv_date
                    FROM ap_det, ap
                    WHERE ap.ap_num = ap_det.ap_num AND ap_det.mat_cat ='".$mat."' AND bom_id ='".$id."' GROUP BY bom_id";
//echo $q_str."<br>";
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }		
    //$row2 = $sql->fetch($q_result);
    $po_amount=0;
    $rcv_qty = 0;
    $qty = 0;
    $price = 0;
    $i = 1;
    while($row2 = $sql->fetch($q_result))
    {
        $row1['rcv_qty'] +=$row2['rcv_qty'];
        $row1['qty'] += $row2['qty'];
        $row1['price'] += $row2['price'];
        
        if ($row2['currency'] <> 'NTD')				
                $row2['po_amount'] = $GLOBALS['rate']->change_rate( $row2['currency'], $row2['po_apv_date'],$row2['po_amount']);
        $row1['po_amount'] += $row2['po_amount'];
        $i++;
    }			


    $q_str="SELECT sum(ap_det.rcv_qty)as rcv_qty, sum(ap_det.po_qty) as qty, po_unit as unit, 
                                    ap_det.eta, sum(ap_det.prics*ap_det.po_qty) as po_amount, 
                                    avg(ap_det.prics) as price 
                    FROM ap_det WHERE ap_det.mat_cat ='".$mat."' AND bom_id ='".$id."' GROUP BY bom_id";
//echo $q_str."<br>";
*/
    $q_str="SELECT rcv_qty, po_qty as qty, po_unit as unit, prc_unit, ap_special.eta,
                                 (ap_special.prics*ap_special.po_qty) as po_amount, ap_special.amount, prics
                    FROM ap_special WHERE ap_special.mat_cat ='".$mat."' AND ord_num ='".$wi_num."'";
//echo $q_str;
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }		
    if(!isset( $row1['rcv_qty'] )) $row1['rcv_qty'] =0;
    if(!isset( $row1['qty'] )) $row1['qty'] =0;

    while($row2 = $sql->fetch($q_result))
    {
		$row1['rcv_qty'] = $this->get_rcv_qty($row1['det_id'],$wi_num,$row1['det_id']);
		//$row1['rcv_qty'] += $row2['rcv_qty'];
        $row1['qty'] += $row2['qty'];
        if($row2['amount'] > 0)$row2['po_amount'];
        $row1['po_amount'] += $row2['po_amount'];
    }
    
    if($row2['price'] > 0)$row1['price'] = Number_format(($row1['po_amount'] / $row1['qty'] ),2,'.','');

    return $row1;
} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_special_ap($id=0)	抓出訂單特殊請購資訊(BOM內額外請購)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_special_ap($id=0,$mat,$ap_mark) {
	$ap_num=array();
	$sql = $this->sql;
	
	$ap_mark = explode(',',$ap_mark);
	$ap_n = 0;
	$ap_str = '';
	foreach($ap_mark as $key){
		$ap_or = $ap_n > 0 ? " or " : "" ;
		$ap_str .= " ".$ap_or." ap.ap_num <> '".$key."' ";
		$ap_n ++;
	}

	$q_str="SELECT DISTINCT 
	ap.ap_num, ap.status,ap.po_num, ap.ann_num,
	ap_det.eta, ap_det.po_qty, ap_det.ship_way, ap_det.ship_date, ap_det.ship_eta, ap_det.po_spare as det_id, ap_det.ship_rmk
	
	FROM ap, ap_det 
	
	WHERE ap_det.ap_num = ap.ap_num AND ap.special = 1 
	AND ap_det.mat_cat ='".$mat."' AND bom_id ='".$id."' AND ( ".$ap_str." )
	AND ap.status >= 0";
	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}		
	while ($row1 = $sql->fetch($q_result)) {
		$row1['status'] = get_po_status($row1['status']);
		$ap_num[]=$row1;
	}
	return $ap_num;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_special_pp($id=0)	抓出訂單特殊請購資訊(BOM內額外請購)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_special_pp($id=0,$mat,$ap_mark,$mat_code='') {
	$ap_num=array();
	$sql = $this->sql;
		
	$ap_mark = explode(',',$ap_mark);
	$ap_n = 0;
	$ap_str = '';
	foreach($ap_mark as $key){
		$ap_or = $ap_n > 0 ? " or " : "" ;
		$ap_str .= " ".$ap_or." ap.ap_num = '".$key."' ";
		$ap_n ++;
	}
	$tb = ($mat == 'l') ? 'bom_lots' : 'bom_acc';
	$q_str="SELECT DISTINCT ap.ap_num, ap.status,ap.po_num, ap_special.eta, ap_special.po_qty, ap.ann_num, 
	ap_special.ship_way, ap_special.ship_date, ap_special.ship_eta, 
	ap_special.po_spare as det_id, ap_special.ship_rmk
	FROM ap, ap_special , $tb WHERE ap_special.ap_num = ap.ap_num AND ap.special = 2 
	AND ap_special.mat_cat ='".$mat."' AND $tb.id ='".$id."' AND ap_special.color = $tb.color AND ( ".$ap_str." )
	AND ap.status >= 0 GROUP BY ap_num";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}		
	while ($row1 = $sql->fetch($q_result)) {
		$row1['status'] = get_po_status($row1['status']);
		$ap_num[]=$row1;
	}
	return $ap_num;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ext_ap($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[] ap_qty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ext_ap($num) {
		
		$sql = $this->sql;
		
		$q_str="SELECT 
		`ap`.`ap_num` , `ap`.`po_num` , `ap`.`status` , `ap`.`ann_num` , `ap`.`currency` , `ap`.`special` , `ap`.`po_apv_date` as `apv_date` , 
		`ap_special`.`id` as `po_id` , `ap_special`.`po_qty` , `ap_special`.`po_unit` as `unit` , `ap_special`.`prc_unit` , `ap_special`.`po_spare` , `ap_special`.`mat_code` , `ap_special`.`mat_cat` , 
		`ap_special`.`ship_date` , `ap_special`.`ship_eta` , `ap_special`.`ship_way` , `ap_special`.`use_for` , `ap_special`.`color` , `ap_special`.`ap_qty` , 
		`lots`.`lots_name` as `mat_name`
		
		FROM  
		`ap_special`, `ap` , `lots` 
		
		WHERE 
		`ap_special`.`ap_num` = `ap`.`ap_num` AND `ap_special`.`mat_code` = `lots`.`lots_code` AND `ap_special`.`mat_cat` = 'l' AND 
		`ap`.`status` >= '0' AND `ap_special`.`pp_mark` = '0' AND `ord_num` = '".$num."'";

		// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$ap = $ship_ary = $po_ary = array();
		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			#重新計算只挑海外工廠驗收的數量
			# PO
			if ( $row1['status'] == '12' )
			$row1['po_qty'] += $row1['po_qty'];
			else 
			$row1['po_uqty'] += $row1['po_qty'];
			
			# SHIP
			$row1['ship_qty'] += $this->get_ship_qtys($row1['po_spare']);				
			
			# TW RCV
			$ary_qty = $this->get_pp_rcv_qtys($row1['ap_num'],$row1['po_id'],$row1['po_spare'],$num,1);
			$row1['tw_rcv_qty'] += $ary_qty['qty'];
			$row1['tw_rcv_uqty'] += $ary_qty['uqty'];
			$row1['tw_rcv'] = $ary_qty['rcv_num'];
			
			# FTY RCV
			$ary_qty = $this->get_pp_rcv_qtys($row1['ap_num'],$row1['po_id'],$row1['po_spare'],$num,0);
			$row1['rcv_qty'] += $ary_qty['qty'];
			$row1['rcv_uqty'] += $ary_qty['uqty'];
			$row1['rcv'] = $ary_qty['rcv_num'];

			# APB
			$pp_apb_qty = $this->get_pp_apb_qtys($row1['po_id'],$num,1);
			$row1['pre_apb_qty'] = $pp_apb_qty['apb_qty'];
			$row1['pre_apb_rcv_num'] = $pp_apb_qty['rcv_num'];
			$row1['pre_apb_status'] = $pp_apb_qty['status'];
			$row1['pre_apb_payment'] = $pp_apb_qty['payment'];

			$pp_apb_qty = $this->get_pp_apb_qtys($row1['po_id'],$num,0);
			$row1['apb_qty'] = $pp_apb_qty['apb_qty'];
			$row1['apb_rcv_num'] = $pp_apb_qty['rcv_num'];
			$row1['apb_status'] = $pp_apb_qty['status'];
			$row1['apb_payment'] = $pp_apb_qty['payment'];
			
			$row1['status'] = get_po_status($row1['status']);
			$ap[$i]=$row1;
			$i++;			
		}

		$q_str="SELECT 
		`ap`.`ap_num` , `ap`.`po_num` , `ap`.`status` , `ap`.`ann_num` , `ap`.`currency` , `ap`.`special` , `ap`.`po_apv_date` as `apv_date` , 
		`ap_special`.`id` as `po_id` , `ap_special`.`po_qty` , `ap_special`.`po_unit` as `unit` , `ap_special`.`prc_unit` , `ap_special`.`po_spare` , `ap_special`.`mat_code` , `ap_special`.`mat_cat` , 
		`ap_special`.`ship_date` , `ap_special`.`ship_eta` , `ap_special`.`ship_way` , `ap_special`.`use_for` , `ap_special`.`color` ,`ap_special`.`mat_id` , `ap_special`.`ap_qty` , `ap_special`.`mat_cat` , `ap_special`.`mat_id` , 
		`acc`.`acc_name` as `mat_name`
		
		FROM  
		`ap_special`, `ap` , `acc` 
		
		WHERE 
		`ap_special`.`ap_num` = `ap`.`ap_num` AND `ap_special`.`mat_code` = `acc`.`acc_code` AND `ap_special`.`mat_cat` = 'a' AND 
		`ap`.`status` >= '0' AND `ap_special`.`pp_mark` = '0' AND `ord_num` = '".$num."'";
		
		// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ship_ary = $po_ary = array();
		while ($row1 = $sql->fetch($q_result)) {
			#重新計算只挑海外工廠驗收的數量
			# PO
			if ( $row1['status'] == '12' )
			$row1['po_qty'] = $row1['po_qty'];
			else 
			$row1['po_uqty'] = $row1['po_qty'];
			
			# SHIP
			$row1['ship_qty'] = $this->get_ship_qtys($row1['ap_num'],$row1['mat_cat'],$row1['mat_id'],$row1['color'],$row1['size']);	
			
			# TW RCV
			$ary_qty = $this->get_pp_rcv_qtys($row1['ap_num'],$row1['po_id'],$row1['po_spare'],$num,1);
			$row1['tw_rcv_qty'] += $ary_qty['qty'];
			$row1['tw_rcv_uqty'] += $ary_qty['uqty'];
			$row1['tw_rcv'] = $ary_qty['rcv_num'];
			
			# FTY RCV
			$ary_qty = $this->get_pp_rcv_qtys($row1['ap_num'],$row1['po_id'],$row1['po_spare'],$num,0);
			$row1['rcv_qty'] += $ary_qty['qty'];
			$row1['rcv_uqty'] += $ary_qty['uqty'];
			$row1['rcv'] = $ary_qty['rcv_num'];

			# APB
			$pp_apb_qty = $this->get_pp_apb_qtys($row1['po_id'],$num,1);
			$row1['pre_apb_qty'] = $pp_apb_qty['apb_qty'];
			$row1['pre_apb_rcv_num'] = $pp_apb_qty['rcv_num'];
			$row1['pre_apb_status'] = $pp_apb_qty['status'];
			$row1['pre_apb_payment'] = $pp_apb_qty['payment'];

			$pp_apb_qty = $this->get_pp_apb_qtys($row1['po_id'],$num,0);
			$row1['apb_qty'] = $pp_apb_qty['apb_qty'];
			$row1['apb_rcv_num'] = $pp_apb_qty['rcv_num'];
			$row1['apb_status'] = $pp_apb_qty['status'];
			$row1['apb_payment'] = $pp_apb_qty['payment'];
			
			$row1['statuss'] = get_po_status($row1['status']);
			$ap[$i]=$row1;
			$i++;
		}

		return $ap;
	} // end func

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_pp_rcv_qtys($po_spare,$wi_num,$tw_rcv=0)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pp_rcv_qtys($ap_num,$det_id,$po_spare,$wi_num,$tw_rcv=0) {

    $sql = $this->sql;

    $q_str = "SELECT DISTINCT sum( `rcv_po_link`.`qty` ) AS `qty` , `receive`.`status` , `receive`.`rcv_num`
    FROM 
    `rcv_po_link` , `receive_det` ,`receive`
    
    WHERE 
    `receive_det`.`ap_num` = '".($ap_num)."' AND 
    `receive_det`.`po_id` = '".($po_spare)."' AND 
	`receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
	`receive`.`tw_rcv` = '".$tw_rcv."' AND 
    `receive_det`.`id` = `rcv_po_link`.`rcv_id` AND 
    `rcv_po_link`.`po_id` = '".($det_id)."' AND 
    `rcv_po_link`.`ord_num` = '".($wi_num)."' 
    
    GROUP BY `rcv_po_link`.`po_id` ; ";
// echo $q_str."<br>";
    $q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)){
		$rcv_num = $row['rcv_num'];
		if ( $row['status'] == '4' )
			$qty += $row['qty'];
		else 
			$uqty += $row['qty'];
	}

    return array( 'qty' => $qty , 'uqty' => $uqty , 'rcv_num' => $rcv_num );
    
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pp_ap($id=0)	取出預先採購資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pp_ap($num) {
		$ap = array();
		$sql = $this->sql;
		$q_str="SELECT ap_special.*, ap.status, ap.po_num, ap.currency, lots.lots_name as mat_name  
						FROM ap_special,ap,lots 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = lots.lots_code 
							AND ap.status >= 0
							AND ap_special.mat_cat ='l' AND ap_special.pp_mark > 0 AND ord_num = '".$num."'";
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			#重新計算只挑海外工廠驗收的數量
			$row1['rcv_qty'] = $this->get_rcv_qty($row1['id'],$row1['ord_num'],$row1['id']);
			$ap[]=$row1;
		}

		$q_str="SELECT ap_special.*, ap.status, ap.po_num, acc.acc_name as mat_name 
						FROM ap_special,ap,acc 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = acc.acc_code 
									AND ap.status >= 0
									AND ap_special.mat_cat ='a' AND ap_special.pp_mark > 0 AND ord_num = '".$num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			#重新計算只挑海外工廠驗收的數量
			$row1['rcv_qty'] = $this->get_rcv_qty($row1['id'],$row1['ord_num'],$row1['id']);
			$ap[]=$row1;
		}

		return $ap;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_unadd_pp($id=0)	取出預先採購資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_unadd_pp($num) {
		$ap = array();
		$sql = $this->sql;
		$q_str="SELECT ap_special.*, ap.status, ap.po_num, ap.currency, lots.lots_name as mat_name  
						FROM ap_special,ap,lots 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = lots.lots_code 
						  AND ap.status >= 0
							AND ap_special.mat_cat ='l' AND ap_special.pp_mark = 1 AND ord_num = '".$num."'";
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			#重新計算只挑海外工廠驗收的數量
			$row1['rcv_qty'] = $this->get_rcv_qty($row1['id'],$row1['ord_num'],$row1['id']);
			$ap[]=$row1;
		}

		$q_str="SELECT ap_special.*, ap.status, ap.po_num, acc.acc_name as mat_name 
						FROM ap_special,ap,acc 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = acc.acc_code
									AND ap.status >= 0 
									AND ap_special.mat_cat ='a' AND ap_special.pp_mark = 1 AND ord_num = '".$num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			#重新計算只挑海外工廠驗收的數量
			$row1['rcv_qty'] = $this->get_rcv_qty($row1['id'],$row1['ord_num'],$row1['id']);
			$ap[]=$row1;
		}

		return $ap;
	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_match_pp($id=0)	取出預先採購資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_match_pp($num,$mat_name,$mat_cat,$color) {
    $ap = array();
    $sql = $this->sql;
	if($mat_cat == 'lots')
	{
		$q_str="SELECT ap_special.*, ap.status, ap.po_num, ap.currency, lots.lots_name as mat_name  
						FROM ap_special,ap,lots 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = lots.lots_code 
						  AND ap.status >= 0
							AND ap_special.mat_cat ='l' AND ap_special.pp_mark = 1 AND ord_num = '".$num."' 
						  AND ap_special.color = '".$color."' AND lots.lots_code = '$mat_name'";
	}else{
		$q_str="SELECT ap_special.*, ap.status, ap.po_num, acc.acc_name as mat_name 
						FROM ap_special,ap,acc 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = acc.acc_code
						      AND ap.status >= 0 
									AND ap_special.mat_cat ='a' AND ap_special.pp_mark = 1 AND ord_num = '".$num."'
									AND ap_special.color = '".$color."' AND acc.acc_code = '$mat_name'";
	}
    //echo $q_str."<br>";
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }		
    while ($row1 = $sql->fetch($q_result)) {
		#重新計算只挑海外工廠驗收的數量
		$row1['rcv_qty'] = $this->get_rcv_qty($row1['id'],$row1['ord_num'],$row1['id']);
        $ap[]=$row1;
    }

    return $ap;
} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[] rcv_mk ap_det rcv_qty po_det rcv_mk
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get($num=0,$log_where='') {
	$sql = $this->sql;

	//請購主檔
	$q_str = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, supl.usance, supl.dm_way as supl_dm_way, supl.cntc_phone, supl.cntc_addr, supl.supl_f_name, supl.cntc_person1, supl.id as supl_id , supl.uni_no
	FROM ap, supl WHERE  ap.sup_code = supl.vndr_no AND ap_num='$num'";
	
	// echo $q_str."<br>"; 
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}

	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;
	}
	
	$toler = explode('|',$row['toler']);
	$row['toleri'] = !empty($toler[0])?$toler[0]:'';
	$row['tolern'] = !empty($toler[1])?$toler[1]:'';
	$row['statuss'] = get_po_status($row['status']);
	
	$op['ap']=$row;
	
	if($row['toleri']=='')
	{
		$op['ap']['toleri'] =0;
	}
	else
	{
		$op['ap']['toleri'] =$row['toleri'];
	}
	if($row['tolern']=='')
	{
		$op['ap']['tolern'] =0;
	}
	else
	{
		$op['ap']['tolern'] =$row['tolern']*(-1);
	}
	//改變Login帳號為名字
	$po_user=$GLOBALS['user']->get(0,$op['ap']['ap_user']);
	$op['ap']['ap_user_id'] = $op['ap']['ap_user'];
	if ($po_user['name'])$op['ap']['ap_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_user']);
	$op['ap']['po_user_id'] = $op['ap']['po_user'];
	if ($po_user['name'])$op['ap']['po_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_sub_user']);
	$op['ap']['po_sub_user_id'] = $op['ap']['po_sub_user'];
	if ($po_user['name'])$op['ap']['po_sub_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_cfm_user']);
	$op['ap']['po_cfm_user_id'] = $op['ap']['po_cfm_user'];
	if ($po_user['name'])$op['ap']['po_cfm_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_apv_user']);
	$op['ap']['po_apv_user_id'] = $op['ap']['po_apv_user'];
	if ($po_user['name'])$op['ap']['po_apv_user'] = $po_user['name'];
	$op['ap']['base_ck'] = 0;
	
	if($op['ap']['arv_area'])	{
		$SHIP_TO = $GLOBALS['SHIP_TO'];
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
	
	if($op['ap']['finl_dist']) {
		$DIST_TO = $GLOBALS['DIST_TO'];
		$op['ap']['finl_name'] = $DIST_TO[$op['ap']['finl_dist']]['Name'];
		$op['ap']['finl_addr'] = $DIST_TO[$op['ap']['finl_dist']]['Addr'];
		$op['ap']['finl_tel']  = $DIST_TO[$op['ap']['finl_dist']]['TEL'];
		$op['ap']['finl_fax']  = $DIST_TO[$op['ap']['finl_dist']]['FAX'];
		$op['ap']['finl_attn'] = $DIST_TO[$op['ap']['finl_dist']]['Attn'];
	}

	//請購主檔
	$q_str = "SELECT min(exceptional.status) as exc_status
	FROM exceptional 
	WHERE  po_num='".$op['ap']['po_num']."'
	GROUP BY po_num";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	if ($row = $sql->fetch($q_result)) {
		$op['ap']['exc_check'] =1;
		$op['ap']['exc_status'] = $row['exc_status'];
	}else{
		$op['ap']['exc_check'] =0;
	}

	$amt = 0;
	$ttl_qty = 0;
	$ap_ttl_qty = 0;
	$op['ord_num'] = array();
	
	//請購明細 -- 主料		 weight
	$q_str="SELECT ap_det.*,ap.toler , smpl_code as ord_num, lots_use.lots_code as mat_code, 
	lots_use.lots_name as mat_name, bom_lots.color, lots.price1, lots.comp as con1, 
	lots.specify as con2, lots.des as mile, lots.lots_name as mile_name, lots.width,
	lots.weight, lots.cons as finish, bom_lots.wi_id , bom_lots.ap_mark as b_ap_mark, s_order.etp
	FROM `ap`,`ap_det`,`bom_lots`,`lots_use`,`lots`, s_order
	WHERE `lots`.`lots_code` = `lots_use`.`lots_code` AND `lots_use`.`id` = `bom_lots`.`lots_used_id` AND `ap_det`.`bom_id`= `bom_lots`.`id` 
			AND `ap_det`.`mat_cat` = 'l' AND `ap`.`ap_num` = `ap_det`.`ap_num` AND `ap_det`.`ap_num` = '$num' and s_order.order_num = smpl_code
	ORDER BY `ap_det`.`id` ASC 
	";

	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$i=0;
	$op['rcv_mk']=0;
	while ($row1 = $sql->fetch($q_result)) {
		$ord_mk =0;
		$ap_ttl_qty += $row1['ap_qty'];
		$ttl_qty += $row1['po_qty'];
		$op['ap_det'][$i]=$row1;
		$op['ap_det'][$i]['i']=$i;
		if($op['ap_det'][$i]['amount'] == 0)$op['ap_det'][$i]['amount']=$op['ap_det'][$i]['po_qty'] * $op['ap_det'][$i]['prics'];
		if($op['ap_det'][$i]['rcv_qty'] > 0)$op['rcv_mk'] = 1;
		$amt += $op['ap_det'][$i]['amount'];
		if(!$op['ap_det'][$i]['prc_unit'])$op['ap_det'][$i]['prc_unit'] = $op['ap_det'][$i]['po_unit'];
		for($j=0; $j<sizeof($op['ord_num']); $j++)
		{
			if($op['ord_num'][$j] == $row1['ord_num'])
			{
				$ord_mk = 1;
				break;
			}
		}
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		
		#APB用
		global $apb;
		// print_r($op['ap_det'][$i]);
		$rcv_qty = $apb->get_apb_det($op['ap_det'][$i]['id']);
		$op['ap_det'][$i]['apb_qty'] = $rcv_qty['qty'];
		$op['ap']['po_eta'] = $row1['po_eta'];
		$i++;
        #SHIP判斷+
        $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '0' ;";
        $q_results = $sql->query($q_str);
        $row = $sql->fetch($q_results);
        if($row)$op['rcv_mk'] = 1;        
	}

	//請購明細 -- 副料
	$q_str="SELECT ap_det.*, smpl_code as ord_num, acc_use.acc_code as mat_code, 
	acc_use.acc_name as mat_name, bom_acc.color, acc.price1, acc.des as con1, 
	acc.specify as con2, acc.mile_code as mile, acc.mile_name, bom_acc.wi_id , bom_acc.ap_mark as b_ap_mark, s_order.etp
	FROM `ap_det`, bom_acc,acc_use, acc, s_order
	WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id AND bom_id=bom_acc.id AND mat_cat = 'a' AND `ap_num` = '$num' and s_order.order_num = smpl_code
	ORDER BY `ap_det`.`id` ASC 
	";
	// echo $q_str."<br>";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$ord_mk = 0;
		$ap_ttl_qty += $row1['ap_qty'];
		$ttl_qty += $row1['po_qty'];
		$op['ap_det'][$i]=$row1;
		$op['ap_det'][$i]['i']=$i;
		if($op['ap_det'][$i]['amount'] == 0)$op['ap_det'][$i]['amount']=$op['ap_det'][$i]['po_qty']*$op['ap_det'][$i]['prics'];
		if($op['ap_det'][$i]['rcv_qty'] > 0)$op['rcv_mk'] = 1;
		$amt += $op['ap_det'][$i]['amount'];
		if(!$op['ap_det'][$i]['prc_unit'])$op['ap_det'][$i]['prc_unit'] = $op['ap_det'][$i]['po_unit'];

		for($j=0; $j<sizeof($op['ord_num']); $j++)
		{
			if($op['ord_num'][$j] == $row1['ord_num'])
			{
				$ord_mk = 1;
				break;
			}
		}
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		$i++;
		#APB用
		$op['ap']['po_eta'] = $row1['po_eta'];
        #SHIP判斷+
        $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '0' ;";
        $q_results = $sql->query($q_str);
        $row = $sql->fetch($q_results);
        if($row)$op['rcv_mk'] = 1;
	} 	
	
	$i=0;	
	//請購明細 -- 特殊請購 -- 主料
	$op['pp'] = 0;
	$q_str="SELECT ap_special.* , lots.price1 , lots.comp as con1 , lots.specify as con2 , 
	lots.lots_name as mat_name , lots.des as mile , lots.lots_name as mile_name, s_order.etp
	FROM `ap_special`, `lots`, s_order	WHERE `lots`.`lots_code` = `mat_code` AND `ap_num` = '$num' and s_order.order_num = ap_special.ord_num";
	// echo $q_str."<br>";		
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	// apb_qty
	while ($row1 = $sql->fetch($q_result)) {
		$ord_mk = 0;
		$ap_ttl_qty += $row1['ap_qty'];
		$ttl_qty += $row1['po_qty'];
		$op['ap_spec'][$i]=$row1;
		$op['pp'] = $row1['pp_mark'];
		$op['ap_spec'][$i]['i']=$i;
		if($op['ap_spec'][$i]['amount'] == 0)$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['po_qty']*$op['ap_spec'][$i]['prics'];
		if($op['ap_spec'][$i]['rcv_qty'] > 0)$op['rcv_mk'] = 1;
		$amt += $op['ap_spec'][$i]['amount'];
		if(!$op['ap_spec'][$i]['prc_unit'])$op['ap_spec'][$i]['prc_unit'] = $op['ap_spec'][$i]['po_unit'];

		for($j=0; $j<sizeof($op['ord_num']); $j++)
		{
			if($op['ord_num'][$j] == $row1['ord_num'])
			{
				$ord_mk = 1;
				break;
			}
		}
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		$i++;
		#APB用
		$op['ap']['po_eta'] = $row1['po_eta'];
        #SHIP判斷+
        $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '1' ;";
        $q_results = $sql->query($q_str);
        $row = $sql->fetch($q_results);
        if($row)$op['rcv_mk'] = 1;
	} 			

	//請購明細 -- 特殊請購 -- 副料
	$q_str="SELECT ap_special.*, acc.price1	, acc.des as con1, acc.specify as con2, 
	acc.acc_name as mat_name, acc.des as mile, acc.mile_name, s_order.etp
	FROM `ap_special`, acc, s_order	WHERE acc.acc_code = mat_code AND `ap_num` = '$num' and s_order.order_num = ap_special.ord_num";
	// echo $q_str."<br>";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$ord_mk = 0;
		$ap_ttl_qty += $row1['ap_qty'];
		$ttl_qty += $row1['po_qty'];
		$op['ap_spec'][$i]=$row1;
		$op['ap_spec'][$i]['i']=$i;
		if($op['ap_spec'][$i]['amount'] == 0)$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['po_qty']*$op['ap_spec'][$i]['prics'];
		if($op['ap_spec'][$i]['rcv_qty'] > 0)$op['rcv_mk'] = 1;
		$amt += $op['ap_spec'][$i]['amount'];
		if(!$op['ap_spec'][$i]['prc_unit'])$op['ap_spec'][$i]['prc_unit'] = $op['ap_spec'][$i]['po_unit'];

		for($j=0; $j<sizeof($op['ord_num']); $j++)
		{
			if($op['ord_num'][$j] == $row1['ord_num'])
			{
				$ord_mk = 1;
				break;
			}
		}
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		$i++;
		#APB用
		$op['ap']['po_eta'] = $row1['po_eta'];
        #SHIP判斷+
        $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '1' ;";
        $q_results = $sql->query($q_str);
        $row = $sql->fetch($q_results);
        if($row)$op['rcv_mk'] = 1;
	} 	
    
	// print_r($op['ap_spec']);
	//Remark項目
	$op['apply_log'] = array();
	$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' ".$log_where." ORDER BY `id` ASC ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
	//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];
		$row1['des'] = str_replace( chr(13).chr(10), "<br>", $row1['des'] );
		$op['apply_log'][]=$row1;
	}

	//特殊採購原因(Remark)
	$op['apply_special'] = array ();		
	$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='special' ORDER BY `id` ASC ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
	//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];			
		$op['apply_special'][]=$row1;
	}			

	//特殊採購原因(Remark)
	$op['ap_oth_cost'] = array ();		
	$q_str="SELECT * FROM `ap_oth_cost` WHERE  `ap_num` = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}

	while ($row1 = $sql->fetch($q_result)) {			
		$op['ap_oth_cost'][]=$row1;
		$amt += $row1['cost'];
	}
	
	$this->update_field_num('po_total', $amt , $op['ap']['po_num']);
	$op['ap']['po_total'] = $amt;
	$op['ap']['ap_ttl_qty'] = $ap_ttl_qty;
	$op['ap']['ttl_qty'] = $ttl_qty;
	
	//PI上傳檔案
	$q_str = "SELECT * FROM ap_file WHERE po_id='".$op['ap']['id']."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while($row = $sql->fetch($q_result)) {
		$op['po_file'][] = $row;
	}
	
	return $op;
} // end func





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mat($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_mat($num) {

    $sql = $this->sql;

	//請購明細 -- 主料		 weight
	$q_str="SELECT ap_det.wi_id , ap_det.mat_cat 
	FROM `ap`,`ap_det`
	WHERE `ap`.`ap_num` = `ap_det`.`ap_num` AND `ap_det`.`ap_num` = '".$num."' 
	ORDER BY `ap_det`.`id` ASC 
	";

	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
    
    $op = array();
	while ($row = $sql->fetch($q_result)) {
        $op[$row['wi_id']] = $row;
	}

	return $op;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[] rcv_mk ap_det rcv_qty po_det rcv_mk ap_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pp($num) {
	$sql = $this->sql;

	//請購主檔
	$q_str = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, supl.usance, supl.dm_way as supl_dm_way, supl.cntc_phone, supl.cntc_addr, supl.supl_f_name, supl.cntc_person1, supl.id as supl_id , supl.uni_no
	FROM ap, supl WHERE  ap.sup_code = supl.vndr_no AND ap_num='$num'";
	
	// echo $q_str."<br>"; 
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}

	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;
	}
	
	$row['statuss'] = get_po_status($row['status']);
	// echo $row['toler'].'<br>';
	$toler = explode('|',$row['toler']);
	$row['toleri'] = !empty($toler[0])?$toler[0]:'';
	$row['tolern'] = !empty($toler[1])?$toler[1]:'';
	// echo $row['toleri'].'<br>';
	// echo $row['tolern'].'<br>';
	$op['ap']=$row;
	
	if($row['toleri']=='') {
		$op['ap']['toleri'] = 0;
	} else {
		$op['ap']['toleri'] = $row['toleri'];
	}
	
	if($row['tolern']=='') {
		$op['ap']['tolern'] = 0;
	} else {
		$op['ap']['tolern'] = $row['tolern']*(-1);
	}
	// echo $row['toleri'].'<br>';
	// echo $row['tolern'].'<br>';
	//改變Login帳號為名字
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_user']);
	$op['ap']['po_user_id'] = $op['ap']['po_user'];
	if ($po_user['name'])$op['ap']['po_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_sub_user']);
	$op['ap']['po_sub_user_id'] = $op['ap']['po_sub_user'];
	if ($po_user['name'])$op['ap']['po_sub_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_cfm_user']);
	$op['ap']['po_cfm_user_id'] = $op['ap']['po_cfm_user'];
	if ($po_user['name'])$op['ap']['po_cfm_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['po_apv_user']);
	$op['ap']['po_apv_user_id'] = $op['ap']['po_apv_user'];
	if ($po_user['name'])$op['ap']['po_apv_user'] = $po_user['name'];
	
	$op['ap']['base_ck'] = 0;
	
	if($op['ap']['arv_area']) {
		$SHIP_TO = $GLOBALS['SHIP_TO'];
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
	
	if($op['ap']['finl_dist']) {
		$DIST_TO = $GLOBALS['DIST_TO'];
		$op['ap']['finl_name'] = $DIST_TO[$op['ap']['finl_dist']]['Name'];
		$op['ap']['finl_addr'] = $DIST_TO[$op['ap']['finl_dist']]['Addr'];
		$op['ap']['finl_tel']  = $DIST_TO[$op['ap']['finl_dist']]['TEL'];
		$op['ap']['finl_fax']  = $DIST_TO[$op['ap']['finl_dist']]['FAX'];
		$op['ap']['finl_attn'] = $DIST_TO[$op['ap']['finl_dist']]['Attn'];
	}

	//請購主檔
	$q_str = "SELECT min(exceptional.status) as exc_status
	FROM exceptional 
	WHERE  po_num='".$op['ap']['po_num']."'
	GROUP BY po_num";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	if ($row = $sql->fetch($q_result)) {
		$op['ap']['exc_check'] =1;
		$op['ap']['exc_status'] = $row['exc_status'];
	}else{
		$op['ap']['exc_check'] =0;
	}

	$amt = 0;
	$ttl_qty = 0;
	$op['ord_num'] = array();
	
	//請購明細 -- 主料		
	$q_str="SELECT ap_det.*,ap.toler , smpl_code as ord_num, lots_use.lots_code as mat_code, 
	lots_use.lots_name as mat_name, bom_lots.color, lots.price1, lots.comp as con1, 
	lots.specify as con2, lots.des as mile, lots.lots_name as mile_name, lots.width,
	lots.weight, lots.cons as finish, bom_lots.wi_id , bom_lots.ap_mark as b_ap_mark
	FROM `ap`,`ap_det`,`bom_lots`,`lots_use`,`lots`  
	WHERE `lots`.`lots_code` = `lots_use`.`lots_code` AND `lots_use`.`id` = `bom_lots`.`lots_used_id` AND `ap_det`.`bom_id`= `bom_lots`.`id` AND `ap_det`.`mat_cat` = 'l' AND `ap`.`ap_num` = `ap_det`.`ap_num` AND `ap_det`.`ap_num` = '$num' ";

	$q_str="SELECT 
	ap_det.*,
	ap.toler , 
	lots_use.smpl_code as ord_num, lots_use.lots_code as mat_code, lots_use.lots_name as mat_name, 
	lots.price1, lots.comp as con1, lots.specify as con2, lots.des as mile, lots.lots_name as mile_name, lots.width, lots.weight, lots.cons as finish 
	
	FROM 
	`ap` 
	LEFT JOIN `ap_det` ON (`ap`.`ap_num` = `ap_det`.`ap_num`) 
	LEFT JOIN `lots_use` ON ( `ap_det`.`used_id` = `lots_use`.`id` ) 
	LEFT JOIN `lots` ON ( `lots`.`lots_code` = `lots_use`.`lots_code` )  
	
	WHERE 
	`ap_det`.`mat_cat` = 'l' AND `ap_det`.`ap_num` = '$num' ";

	// echo $q_str."<br>";
	// exit;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$i=0;
	$op['rcv_mk']=0;
	while ($row1 = $sql->fetch($q_result)) {
	
		$ord_mk =0;
		$ttl_qty += $row1['po_qty'];
		$op['ap_det'][$i] = $row1;
		$op['ap_det'][$i]['i'] = $i;

		# 抓取所有採購的PO數量包含未核可的採購單
		$op['ap_det'][$i]['sum_po'] = $this->get_ap_det_used_qty($row1['used_id']);
		
		if( $op['ap_det'][$i]['amount'] == 0 ) $op['ap_det'][$i]['amount'] = $op['ap_det'][$i]['po_qty'] * $op['ap_det'][$i]['prics'];
		if( $op['ap_det'][$i]['rcv_qty'] > 0 ) $op['rcv_mk'] = 1;
		
		$amt += $op['ap_det'][$i]['amount'];
		
		if(!$op['ap_det'][$i]['prc_unit'])$op['ap_det'][$i]['prc_unit'] = $op['ap_det'][$i]['po_unit'];
		
		for($j=0; $j<sizeof($op['ord_num']); $j++) {
			if($op['ord_num'][$j] == $row1['ord_num']) {
				$ord_mk = 1;
				break;
			}
		}
		
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		
		#APB用
		// global $apb;
		// print_r($op['ap_det'][$i]);
		// $rcv_qty = $apb->get_apb_det($op['ap_det'][$i]['id']);
		// $op['ap_det'][$i]['apb_qty'] = $rcv_qty['qty'];
		// $op['ap']['po_eta'] = $row1['po_eta'];
		$i++;
        #SHIP判斷+
        // $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '0' ;";
        // $q_results = $sql->query($q_str);
        // $row = $sql->fetch($q_results);
        // if($row)$op['rcv_mk'] = 1;        
	}

	//請購明細 -- 副料
	$q_str="SELECT ap_det.*, smpl_code as ord_num, acc_use.acc_code as mat_code, 
	acc_use.acc_name as mat_name, bom_acc.color, 
	acc.price1, acc.des as con1, acc.specify as con2, acc.mile_code as mile, acc.mile_name, bom_acc.wi_id , bom_acc.ap_mark as b_ap_mark
	FROM `ap_det`, bom_acc,acc_use, acc  
	WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id AND bom_id=bom_acc.id AND mat_cat = 'a' AND `ap_num` = '$num' ";
	
	
	$q_str="SELECT 
	ap_det.*,
	ap.toler , 
	acc_use.smpl_code as ord_num, acc_use.acc_code as mat_code, acc_use.acc_name as mat_name, 
	acc.price1, acc.des as con1 ,acc.specify as con2, acc.mile_code as mile, acc.mile_name 
	
	FROM 
	`ap` 
	LEFT JOIN `ap_det` ON (`ap`.`ap_num` = `ap_det`.`ap_num`) 
	LEFT JOIN `acc_use` ON ( `ap_det`.`used_id` = `acc_use`.`id` ) 
	LEFT JOIN `acc` ON ( `acc`.`acc_code` = `acc_use`.`acc_code` )  
	
	WHERE 
	`ap_det`.`mat_cat` = 'a' AND `ap_det`.`ap_num` = '$num' ";	
	
	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$ord_mk = 0;
		$ttl_qty += $row1['po_qty'];
		$op['ap_det'][$i]=$row1;

		$op['ap_det'][$i]['i']=$i;
		
		# 抓取所有採購的PO數量包含未核可的採購單
		$op['ap_det'][$i]['sum_po']=$this->get_ap_det_used_qty($row1['used_id']);
		
		if($op['ap_det'][$i]['amount'] == 0)$op['ap_det'][$i]['amount']=$op['ap_det'][$i]['po_qty']*$op['ap_det'][$i]['prics'];
		if($op['ap_det'][$i]['rcv_qty'] > 0)$op['rcv_mk'] = 1;
		$amt += $op['ap_det'][$i]['amount'];
		if(!$op['ap_det'][$i]['prc_unit'])$op['ap_det'][$i]['prc_unit'] = $op['ap_det'][$i]['po_unit'];

		for($j=0; $j<sizeof($op['ord_num']); $j++)
		{
			if($op['ord_num'][$j] == $row1['ord_num'])
			{
				$ord_mk = 1;
				break;
			}
		}
		
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		$i++;
		#APB用
		$op['ap']['po_eta'] = $row1['po_eta'];
        #SHIP判斷+
        $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '0' ;";
        $q_results = $sql->query($q_str);
        $row = $sql->fetch($q_results);
        if($row)$op['rcv_mk'] = 1;
	} 	

	$i=0;
	
	//請購明細 -- 特殊請購 -- 主料
	$op['pp'] = 0;
	$q_str="SELECT ap_special.* , lots.price1 , lots.comp as con1 , lots.specify as con2 , 
	lots.lots_name as mat_name , lots.des as mile , lots.lots_name as mile_name
	FROM `ap_special`, `lots`	WHERE `lots`.`lots_code` = `mat_code` AND `ap_num` = '$num' ";
	// echo $q_str."<br>";		
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	// apb_qty
	while ($row1 = $sql->fetch($q_result)) {
		$ord_mk = 0;
		$ttl_qty += $row1['po_qty'];
		$op['ap_spec'][$i]=$row1;
		$op['pp'] = $row1['pp_mark'];
		$op['ap_spec'][$i]['i']=$i;
		if($op['ap_spec'][$i]['amount'] == 0)$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['po_qty']*$op['ap_spec'][$i]['prics'];
		if($op['ap_spec'][$i]['rcv_qty'] > 0)$op['rcv_mk'] = 1;
		$amt += $op['ap_spec'][$i]['amount'];
		if(!$op['ap_spec'][$i]['prc_unit'])$op['ap_spec'][$i]['prc_unit'] = $op['ap_spec'][$i]['po_unit'];

		for($j=0; $j<sizeof($op['ord_num']); $j++)
		{
			if($op['ord_num'][$j] == $row1['ord_num'])
			{
				$ord_mk = 1;
				break;
			}
		}
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		$i++;
		#APB用
		$op['ap']['po_eta'] = $row1['po_eta'];
        #SHIP判斷+
        $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '1' ;";
        $q_results = $sql->query($q_str);
        $row = $sql->fetch($q_results);
        if($row)$op['rcv_mk'] = 1;
	} 			

	//請購明細 -- 特殊請購 -- 副料
	$q_str="SELECT ap_special.*, acc.price1	, acc.des as con1, acc.specify as con2, 
	acc.acc_name as mat_name, acc.des as mile, acc.mile_name
	FROM `ap_special`, acc	WHERE acc.acc_code = mat_code AND `ap_num` = '$num' ";
	// echo $q_str."<br>";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$ord_mk = 0;
		$ttl_qty += $row1['po_qty'];
		$op['ap_spec'][$i]=$row1;
		$op['ap_spec'][$i]['i']=$i;
		if($op['ap_spec'][$i]['amount'] == 0)$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['po_qty']*$op['ap_spec'][$i]['prics'];
		if($op['ap_spec'][$i]['rcv_qty'] > 0)$op['rcv_mk'] = 1;
		$amt += $op['ap_spec'][$i]['amount'];
		if(!$op['ap_spec'][$i]['prc_unit'])$op['ap_spec'][$i]['prc_unit'] = $op['ap_spec'][$i]['po_unit'];

		for($j=0; $j<sizeof($op['ord_num']); $j++)
		{
			if($op['ord_num'][$j] == $row1['ord_num'])
			{
				$ord_mk = 1;
				break;
			}
		}
		if($ord_mk == 0)$op['ord_num'][] = $row1['ord_num'];
		if($row1['base_qty'] > 0)$op['ap']['base_ck'] = 1;
		$i++;
		#APB用
		$op['ap']['po_eta'] = $row1['po_eta'];
        #SHIP判斷+
        $q_str="SELECT * FROM `po_ship_det` WHERE `po_id` = '".$row1['po_spare']."' AND `special` = '1' ;";
        $q_results = $sql->query($q_str);
        $row = $sql->fetch($q_results);
        if($row)$op['rcv_mk'] = 1;
	} 	
    
	// print_r($op['ap_spec']);
	//Remark項目
	$op['apply_log'] = array();
	$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' ".$log_where." ORDER BY `id` ASC ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
	//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];
		$row1['des'] = str_replace( chr(13).chr(10), "<br>", $row1['des'] );
		$op['apply_log'][]=$row1;
	}

	//特殊採購原因(Remark)
	$op['apply_special'] = array ();		
	$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='special' ORDER BY `id` ASC ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
	//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];			
		$op['apply_special'][]=$row1;
	}			

	//特殊採購原因(Remark)
	$op['ap_oth_cost'] = array ();		
	$q_str="SELECT * FROM `ap_oth_cost` WHERE  `ap_num` = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}

	while ($row1 = $sql->fetch($q_result)) {			
		$op['ap_oth_cost'][]=$row1;
		$amt += $row1['cost'];
	}
	
	$this->update_field_num('po_total', $amt , $op['ap']['po_num']);
	$op['ap']['po_total'] = $amt;
	$op['ap']['ttl_qty'] = $ttl_qty;
	
	return $op;
} // end func



# get_ap_det_used_qty
function get_ap_det_used_qty($used_id){

$sql = $this->sql;

$q_str="SELECT SUM(ap_qty) as sum_po FROM ap_det WHERE used_id = '".$used_id."' GROUP BY ap_det.used_id	";
// echo $q_str."<br>";

$q_results = $sql->query($q_str);
if( $row = $sql->fetch($q_results) ){
	return $row['sum_po'];
}else {
	return 0;
}

}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name
                             FROM ap, supl ";
    
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("ap.id DESC");
    
    # 分頁
    $srh->row_per_page = 25;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("ap.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("ap.arv_area = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {
                $srh->or_where_condition("ap.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }

    if ($mode > 0){
        $mesg = '';
        if ($str = strtoupper($argv['PHP_SCH_num']) )  { 
            $srh->add_where_condition("ap.po_num LIKE '%$str%'", "PHP_SCH_num",$str); 
            $mesg.= "  PO# : [ $str ]. ";
        }
        
        if ($str = strtoupper($argv['PHP_pi_num']) )  { 
            $srh->add_where_condition("ap.pi_num LIKE '%$str%'", "PHP_pi_num",$str); 
            $mesg.= "  PI# : [ $str ]. ";
        }
        
        if ($str = $argv['PHP_SCH_supl'] )  { 
            $srh->add_where_condition("ap.sup_code = '$str'", "PHP_SCH_supl",$str); 
            $mesg.= "  Supl. = [ $str ]. ";
            }
        if ($str = $argv['PHP_SCH_fty'] ) { 
            $srh->add_where_condition("ap.arv_area = '$str'", "PHP_SCH_fty",$str); 
            $mesg.= "  Ship = [ $str ]. ";
            }
        if ($str = $argv['PHP_SCH_cust'] ) {
            $srh->add_where_condition("ap.cust = '$str'", "PHP_SCH_cust",$str); 
            $mesg.= "  Cust. = [ $str ]. ";
            }
        if ($mesg)
        {
            $msg = "Search ".$mesg;
            $this->msg->add($msg);
        }
    }	

    $srh->add_where_condition("ap.sup_code = supl.vndr_no");
    if( $mode == 2)
    {
        $srh->add_where_condition("ap.status >= 6");
        if (isset($argv['SCH_ann']) && $str = $argv['SCH_ann'] )  { 
            $srh->add_where_condition("ap.ann_num like '%$str%'", "SCH_ann",$str); 
            $mesg.= "  NOTE : [ $str ]. ";
        }		
            
    }else if($mode == 3){
        $srh->add_where_condition("ap.status = 12");		
    
    }else{
        if(!isset($argv['SCH_del']))$argv['SCH_del'] = 0;
        if($argv['SCH_del'] == 1)
        {
            $srh->add_where_condition("ap.status >= 6 || ap.status = -1");
        }else if($argv['SCH_del'] == 2){
            $srh->add_where_condition("ap.status = -1");
        }else{
            $srh->add_where_condition("ap.status >= 6 ");
        }
    }

    $result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }

    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    $op['apply'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    //	$op['prev_no'] = $srh->prev_no;
    //	$op['next_no'] = $srh->next_no;
    $op['max_no'] = $srh->max_no;
    //	$op['last_no'] = $srh->last_no;
    $op['start_no'] = $srh->start_no;
    //	$op['per_page'] = $srh->row_per_page;
    // echo $srh->q_str;
    if(!$limit_entries){ 
        ##--*****--2006.11.16頁碼新增 start			
        $op['maxpage'] =$srh->get_max_page();
        $op['pages'] = $pages;
        $op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
        ##--*****--2006.11.16頁碼新增 end
    }	
    return $op;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_cfm($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name FROM ap, supl ";
    
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("ap.id DESC");
    
    # 分頁
    $srh->row_per_page = 25;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("ap.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("ap.arv_area = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {
                $srh->or_where_condition("ap.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    $srh->add_where_condition("ap.sup_code = supl.vndr_no");
    $srh->add_where_condition("ap.status = 8");

    $result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }

    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    $op['apply'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['max_no'] = $srh->max_no;
    $op['start_no'] = $srh->start_no;
    
    if(!$limit_entries){ 
        ##--*****--2006.11.16頁碼新增 start			
        $op['maxpage'] =$srh->get_max_page();
        $op['pages'] = $pages;
        $op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
        ##--*****--2006.11.16頁碼新增 end
    }	

    return $op;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_apv($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name FROM ap, supl ";
    
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("ap.id DESC");
    
    # 分頁
    $srh->row_per_page = 25;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("ap.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("ap.arv_area = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {
                $srh->or_where_condition("ap.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }

    $srh->add_where_condition("ap.sup_code = supl.vndr_no");
    $srh->add_where_condition("ap.status = 10");

    $result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }

    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    $op['apply'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['max_no'] = $srh->max_no;
    $op['start_no'] = $srh->start_no;
    
    if(!$limit_entries){ 
        ##--*****--2006.11.16頁碼新增 start			
        $op['maxpage'] =$srh->get_max_page();
        $op['pages'] = $pages;
        $op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
        ##--*****--2006.11.16頁碼新增 end
    }	

    return $op;
} // end func		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_det_field
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_det_field($field,$table,$where) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT ". $field. " FROM ".$table." WHERE ".$where;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		return $row1;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->count_totoal_price($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_totoal_price($table,$where) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT po_qty, prics, amount FROM ".$table.", ap WHERE ap.ap_num = ".$table.".ap_num ".$where;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$total=0;
		while ($row1 = $sql->fetch($q_result)) {
//			$total = $total + ($row1['po_qty']*$row1['prics']);
				$total = $total + $row1['amount'];
//			echo $total."<br>";
		}			
		
		
	return $total;
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->grout_ap($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[] ttl_qty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function grout_ap($ap_det) {
		
		$ap_det2[0] = $ap_det[0];
		$ap_det2[0]['orders'][0] = $ap_det[0]['ord_num'];
		$ap_det2[0]['orders_etp'][0] = $ap_det[0]['etp'];
		$k=1;
		//print_r($ap_det);
		//exit;
		for ($i=1; $i<sizeof($ap_det); $i++)
		{
			$mk=0;	$order_add=0;
			for ($j=0; $j< sizeof($ap_det2); $j++)
			{
				if ($ap_det2[$j]['mat_code'] == $ap_det[$i]['mat_code'] && $ap_det2[$j]['color'] === $ap_det[$i]['color'] && $ap_det2[$j]['unit'] == $ap_det[$i]['unit'] && $ap_det2[$j]['size'] == $ap_det[$i]['size'])
				{
					$ap_det2[$j]['ap_qty'] = $ap_det[$i]['ap_qty'] +$ap_det2[$j]['ap_qty'];
					$ap_det2[$j]['po_qty'] = $ap_det[$i]['po_qty'] +$ap_det2[$j]['po_qty'];
					$ap_det2[$j]['rcv_qty'] = $ap_det[$i]['rcv_qty'] +$ap_det2[$j]['rcv_qty'];
					$ap_det2[$j]['amount'] = $ap_det[$i]['amount'] +$ap_det2[$j]['amount'];
					for ($z =0; $z < sizeof($ap_det2[$j]['orders']); $z++)
					{
						if ($ap_det2[$j]['orders'][$z] == $ap_det[$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0){
						$ap_det2[$j]['orders'][] = $ap_det[$i]['ord_num'];
						$ap_det2[$j]['orders_etp'][] = $ap_det[$i]['etp'];
					}
					$mk = 1;
				}
				/*
				if ($ap_det2[$j]['mat_code'] == $ap_det[$i]['mat_code'] && $ap_det2[$j]['color'] === $ap_det[$i]['color'] && $ap_det2[$j]['unit'] == $ap_det[$i]['unit'] && $ap_det2[$j]['eta'] == $ap_det[$i]['eta'])
				{
					$ap_det2[$j]['ap_qty'] = $ap_det[$i]['ap_qty'] +$ap_det2[$j]['ap_qty'];
					$ap_det2[$j]['po_qty'] = $ap_det[$i]['po_qty'] +$ap_det2[$j]['po_qty'];
					$ap_det2[$j]['rcv_qty'] = $ap_det[$i]['rcv_qty'] +$ap_det2[$j]['rcv_qty'];
					$ap_det2[$j]['amount'] = $ap_det[$i]['amount'] +$ap_det2[$j]['amount'];
					for ($z =0; $z < sizeof($ap_det2[$j]['orders']); $z++)
					{
						if ($ap_det2[$j]['orders'][$z] == $ap_det[$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0){
						$ap_det2[$j]['orders'][] = $ap_det[$i]['ord_num'];
						$ap_det2[$j]['orders_etp'][] = $ap_det[$i]['etp'];
					}
					$mk = 1;
				}
				*/
			}
			
			if ($mk == 0)
			{
				$ap_det2[$k] = $ap_det[$i];
				$ap_det2[$k]['orders'][0] = $ap_det[$i]['ord_num'];
				$ap_det2[$k]['orders_etp'][] = $ap_det[$i]['etp'];
				$k++;
			}
		}
		$sql = $this->sql;
		$ship_total=0;
		foreach($ap_det2 as $key => $value)
		{
			$q_str = "SELECT  * FROM po_ship_det where po_id='".$value['po_spare']."'";
			
			if (!$q_result = $sql->query($q_str)) 
			{
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}

			$ship_total=0;
			$my_row=0;
			while ($row = $sql->fetch($q_result))
			{
				$ap_det2[$key]['ship_id'][]=$row['ship_id'];
				$ap_det2[$key]['ship_qty'][]=$row['qty'];
				$ship_total+=$row['qty'];
				$my_row++;
				$q_str = "select * from po_ship where id='".$row['ship_id']."'";
				if (!$q_result1 = $sql->query($q_str)) 
				{
					$this->msg->add("Error ! Database can't access!");
					$this->msg->merge($sql->msg);
					return false;    
				}
				$row1 = $sql->fetch($q_result1);
				$ap_det2[$key]['ship_num'][]=$row1['num'];
				
				
			}
			$ap_det2[$key]['my_row']=$my_row -1 ;
			$ap_det2[$key]['ship_qty_total']=$ship_total;
			$diff=$ship_total - $ap_det2[$key]['po_qty'];
			$ap_det2[$key]['qty_different'] = number_format(($diff/$ap_det2[$key]['po_qty'])*100,2,'.',0);
			
		}
		
	return $ap_det2;
	} // end func	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_po_ok($id,$bom_table,$mat_cat)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function chk_po_ok($id,$bom_table,$mat_cat) {

		$sql = $this->sql;
		$mk = 0;
//查詢訂單的所有請購單是否採購(分主副料)
		$q_str = "SELECT ap.po_num	FROM ap, ap_det, $bom_table, wi
							WHERE ap.ap_num = ap_det.ap_num AND ap_det.bom_id = $bom_table.id AND 
										$bom_table.wi_id = wi.id AND ap_det.mat_cat =  '$mat_cat' and wi_id = '$id'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			if ($row['po_num'] == '') return "0";
			$mk = 1;
		}
	//echo $mk;
	
//查詢訂單的所有特殊請購單是否採購(分主副料)

		$q_str = "SELECT ap.po_num   FROM ap_special, ap, wi
							WHERE ap_special.ap_num = ap.ap_num AND ap_special.ord_num = wi.wi_num AND 
										ap_special.mat_cat = '$mat_cat' AND wi.id = '$id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}


		while ($row = $sql->fetch($q_result)) {
			if ($row['po_num'] == '') return "0";
			$mk = 1;
		}
		
		return $mk;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_unit_group($unit) : 取得訂單的狀態
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function get_unit_group($unit,$id,$unit_name='PHP_unit'){ 
 	$unit_group = $GLOBALS['UNIT_GROUP'];
 	$ug = '';
 	foreach ($unit_group as $key => $value)
 	{
 		for ($i=0; $i<sizeof($unit_group[$key]); $i++)
 		{
 			if($unit_group[$key][$i] == $unit)
 			{
 				$ug = $key;
 				break;
 			} 			
 		}  			
		if ($ug <> '') break;
 	}
 	if($unit_name <> 'PHP_unit')
 	{
 		if($ug == 'AP') $ug = 'AP2';
 		$unit_select = $GLOBALS['arry2']->select($unit_group[$ug],$unit,$unit_name.$id,'select');

 	}else{
 		if($ug == 'AP2') $ug = 'AP';
 		$unit_select = $GLOBALS['arry2']->select($unit_group[$ug],$unit,$unit_name.$id,"select","chg_unit('".$id."',this.selectedIndex)");
	}
 	return $unit_select;

}	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_num($field1, $value1, $id, $table='ap')
#
#		更新 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_num($field1, $value1, $po_num, $table='ap') {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE po_num = '".	$po_num ."'" ;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_po($id)
#
#		刪除PO
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_po($id) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ap SET po_num ='' , po_user = '', po_date='0000-00-00'  WHERE ap_num= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$q_str = "UPDATE ap_det SET po_spare ='' , po_qty = '0', po_eta='0000-00-00', po_unit=''  WHERE ap_num= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$q_str = "UPDATE ap_special SET po_spare ='' , po_qty = '0', po_eta='0000-00-00', po_unit=''  WHERE ap_num= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return $id;
	} // end func	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_edit_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[] 似乎無用
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_edit_det($id=0,$table) {
		$po_id=array();
		$sql = $this->sql;
		$q_str="SELECT mat_cat  FROM $table WHERE id = $id"; 

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		$mat_cat = $row1[0]; // 取得為主料或副料
		if($mat_cat == 'l') 
		{
			$bom_table = 'bom_lots';
			$mat_table = 'lots_use';
			$mat_string = 'lots_use.id = bom_lots.lots_used_id';
			$mat_field = "lots_code";
			$bom_field = 'bom_lots.color';
		}else{
		 $bom_table = 'bom_acc';
		 $mat_table = 'acc_use';
		 $mat_string = 'acc_use.id = bom_acc.acc_used_id';		 
		 $mat_field = "acc_code";
		 $bom_field = 'bom_acc.color';
		}

		$q_str="SELECT ap_num, $mat_field, $bom_field as bom_color, eta   
						FROM $table, $bom_table, $mat_table
						WHERE ".$bom_table.".id = ".$table.".bom_id AND ".$mat_string."  AND ".$table.".id = $id"; 

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row = $sql->fetch($q_result);
		
		$q_str="SELECT ".$table.".id  FROM $table, $bom_table, $mat_table 
						WHERE ".$bom_table.".id = ".$table.".bom_id AND ".$mat_string.
									" AND ap_num= '".$row['ap_num']."' AND ".$mat_field ." = '".$row[$mat_field]."' AND ".$bom_field." = '".$row['bom_color']."' AND eta = '".$row['eta']."'"; 

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row = $sql->fetch($q_result)) {
			$po_id[] = $row;
		}		
		
		return $po_id;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_other_cost($parm1) {
					
		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO ap_oth_cost (ap_num,item,cost) 
				 		  VALUES('".
							$parm1['ap_num']."','".						
							$parm1['item']."','".																									
							$parm1['cost']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_cost($id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM ap_oth_cost WHERE id='".$id."'";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     
    
	return true;


	}// end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_log($id)		刪除log檔
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_log($id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM ap_log WHERE id='".$id."'";
 	
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     
    
	return true;


	}// end func		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> po_search_by_ord
#搜尋訂單採購項目
# rcv_mk 為 1 時 搜尋未驗收採購單
# rcv_mk 為 2 時 搜尋己驗收採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function po_search_by_ord($ord_num,$po_status='',$rcv_mk='',$group_mk='') {
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$where_str = '';
		$i = 0;
   
		$q_header = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, sum(ap_det.po_qty) as qty, 
												ap_det.po_unit, lots_use.lots_name as mat_name, lots_use.lots_code as mat_code
								 FROM ap, supl, ap_det, bom_lots, lots_use 
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_det.ap_num 
								 	 AND ap_det.bom_id = bom_lots.id AND bom_lots.lots_used_id = lots_use.id
								 	 AND ap_det.mat_cat = 'l'  
								 	 AND lots_use.smpl_code  like '%".$ord_num."%'";

		if ($po_status )  { 
			$q_header.= " AND ap.status  = '".$po_status."'";
			}			
		
		if($group_mk == 'order_del')
		{
			$q_header.= " GROUP BY ap.ap_num,  lots_use.smpl_code, lots_use.lots_code";
		}else{
			$q_header.= " GROUP BY ap.ap_num ";
		}
		
		if ($rcv_mk == 1 )  { 
			$q_header.= " HAVING sum(ap_det.rcv_qty)  = '0'";
			}  
		if ($rcv_mk == 2 )  { 
			$q_header.= "HAVING sum(ap_det.rcv_qty)  > '0'";
			} 	
//echo $q_header."<br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][]=$row1;
			$i++;
		}

		$q_header = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, sum(ap_det.po_qty) as qty, 
												ap_det.po_unit, acc_use.acc_code as mat_code, acc_use.acc_name as mat_name
								 FROM ap, supl, ap_det, bom_acc, acc_use 
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_det.ap_num 
								 	 AND ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id
								 	 AND ap_det.mat_cat = 'a' 
								 	 AND acc_use.smpl_code  like '%".$ord_num."%'";
		if ($po_status )  { 
			$q_header.= " AND ap.status  = '".$po_status."'";
			} 
			
		if($group_mk == 'order_del')
		{
			$q_header.= " GROUP BY ap.ap_num,  acc_use.smpl_code, acc_use.acc_code";
		}else{
			$q_header.= " GROUP BY ap.ap_num ";
		}
		
		if ($rcv_mk == 1 )  { 
			$q_header.= " HAVING sum(ap_det.rcv_qty)  = '0'";
			}  
		if ($rcv_mk == 2 )  { 
			$q_header.= " HAVING sum(ap_det.rcv_qty)  > '0'";
			}  			
	
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][]=$row1;

			$i++;
		}


		$q_header = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, sum(po_qty) as qty, po_unit,
												mat_code
								 FROM ap, supl, ap_special 
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_special.ap_num 								 	
								 	     AND ap_special.ord_num  like '%".$ord_num."%'";
		
		if ($po_status )  { 
			$q_header.= " AND ap.status  = '".$po_status."'";
			} 
			
		if($group_mk == 'order_del')
		{
			$q_header.= " GROUP BY ap.ap_num,  ap_special.ord_num, ap_special.mat_code";
		}else{
			$q_header.= " GROUP BY ap.ap_num ";
		}
		
		if ($rcv_mk == 1 )  { 
			$q_header.= " HAVING sum(ap_special.rcv_qty)  = '0'";
			}  
		if ($rcv_mk == 2 )  { 
			$q_header.= " HAVING sum(ap_special.rcv_qty)  > '0'";
			} 

		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][]=$row1;
			$i++;
		}

		if(!isset($op['apply'])) 	$op['apply'] = array();	
		return $op;
	} // end func			
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> po_search_by_ord
#搜尋訂單採購項目
# rcv_mk 為 1 時 搜尋未驗收採購單
# rcv_mk 為 2 時 搜尋己驗收採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function po_rcvd_fab($ord_num) {
		$sql = $this->sql;
		$apply = array();
		$i = 0;
   
		$q_header = "SELECT ap.*, ap_det.id as det_id, rcv_po_link.id as rcv_id, rcv_po_link.qty as rcv_qty,
												bom_lots.qty, receive_det.rcv_num,
												lots_use.lots_code as mat_code, bom_lots.color, ap_det.unit											
								 FROM ap, ap_det, bom_lots, lots_use, rcv_po_link, receive_det
								 WHERE ap.ap_num = ap_det.ap_num AND ap_det.id = rcv_po_link.po_id
								 	 AND receive_det.id = rcv_po_link.rcv_id
								 	 AND ap_det.bom_id = bom_lots.id AND bom_lots.lots_used_id = lots_use.id
								 	 AND ap_det.mat_cat = 'l'  
								 	 AND lots_use.smpl_code  like '%".$ord_num."%'";
//echo $q_header."<br><br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		while ($row1 = $sql->fetch($q_result)) {
			$tmp_qty = explode(',',$row1['qty']);
			$row1['qty'] = 0;
			for($j=0; $j<sizeof($tmp_qty); $j++) $row1['qty'] += $tmp_qty[$j];
			$apply[$i]=$row1;
			$apply[$i]['table'] = 'det';
			$i++;
		}
		$q_header = "SELECT ap.*,	ap_special.mat_code, ap_special.color, ap_special.id as det_id, 
												rcv_po_link.id as rcv_id, rcv_po_link.qty, ap_special.po_unit as unit,
												receive_det.rcv_num
								 FROM ap, ap_special, rcv_po_link , receive_det
								 WHERE ap.ap_num = ap_special.ap_num AND ap_special.id = rcv_po_link.po_id
								 			 AND receive_det.id = rcv_po_link.rcv_id
								 	     AND ap_special.mat_cat = 'l' AND ap_special.ord_num  like '%".$ord_num."%'";
//echo $q_header."<br><br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$apply[$i]=$row1;
			$apply[$i]['table'] = 'special';
			$i++;
		}

		return $apply;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> po_search_by_ord
#搜尋訂單採購項目
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function po_rcvd_acc($ord_num) {	
		$sql = $this->sql;
		$apply = array();
		$i = 0;
		
		$q_header = "SELECT ap.*, ap_det.id as det_id, rcv_po_link.id as rcv_id, rcv_po_link.qty as rcv_qty,												
												acc_use.acc_code as mat_code, bom_acc.color, ap_det.unit as unit,
												receive_det.rcv_num, bom_acc.qty
								 FROM ap, supl, ap_det, bom_acc, acc_use, rcv_po_link  , receive_det								 
								 WHERE ap.ap_num = ap_det.ap_num AND ap_det.id = rcv_po_link.po_id
								   AND receive_det.id = rcv_po_link.rcv_id
								 	 AND ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id
								 	 AND ap_det.mat_cat = 'a' 
								 	 AND acc_use.smpl_code  like '%".$ord_num."%'";
//echo $q_header."<br><br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$tmp_qty = explode(',',$row1['qty']);
			$row1['qty'] = 0;
			for($j=0; $j<sizeof($tmp_qty); $j++) $row1['qty'] += $tmp_qty[$j];

			$apply[$i]=$row1;
			$apply[$i]['table'] = 'det';
			$i++;
		}


		$q_header = "SELECT ap.*,	ap_special.mat_code, ap_special.color, ap_special.id as det_id, 
												rcv_po_link.id as rcv_id, rcv_po_link.qty, ap_special.po_unit as unit,
												receive_det.rcv_num
								 FROM ap, ap_special, rcv_po_link  , receive_det
								 WHERE ap.ap_num = ap_special.ap_num AND ap_special.id = rcv_po_link.po_id
								 	     AND receive_det.id = rcv_po_link.rcv_id
								 	     AND ap_special.mat_cat = 'a' AND ap_special.ord_num  like '%".$ord_num."%'";
//echo $q_header."<br><br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$apply[$i]=$row1;
			$apply[$i]['table'] = 'special';
			$i++;
		}

		return $apply;
	} // end func			

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->change_pp($ord_num)
# 刪除PO
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function change_pp($ord_num) {

	$sql = $this->sql;

	#####   更新資料庫內容
	############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

	$q_str = "UPDATE ap_special SET pp_mark = '1' WHERE pp_mark = '2' AND ord_num= '".$ord_num."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return $ord_num;
} // end func	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->change_pp($ord_num)
#
#		刪除PO
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_fab_ship($wi_id,$ord_num) {

		$sql = $this->sql;
		
		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "SELECT ap.ship_way FROM ap, ap_det, bom_lots
							WHERE ap.ap_num = ap_det.ap_num AND ap_det.bom_id = bom_lots.id  AND 
										ap_det.mat_cat = 'l' AND bom_lots.wi_id='".$wi_id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result))
		{		
			if ($row['ship_way'] == '')
			{
				return 0;
			}
		}

		$q_str = "SELECT ap.ship_way FROM ap, ap_special
							WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_cat= 'l' AND
										ap_special.ord_num='".$ord_num."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result))
		{		
			if ($row['ship_way'] == '')
			{
				return 0;
			}
		}	

		return 1;
	} // end func		
	


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->change_pp($ord_num)
#
#		刪除PO
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_acc_ship($wi_id,$ord_num,$cat) {

		$sql = $this->sql;
		$mk = 0;
		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "SELECT ap.ship_way FROM ap, ap_det, bom_acc, acc_use
							WHERE ap.ap_num = ap_det.ap_num AND ap_det.bom_id = bom_acc.id  AND 
										acc_use.id = bom_acc.acc_used_id AND acc_use.acc_cat = '".$cat."' AND
										ap_det.mat_cat='a' AND bom_acc.wi_id='".$wi_id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result))
		{		
			$mk =1;
			if ($row['ship_way'] == '')
			{
				return 0;
			}
		}
	
	if($cat == 0)
	{
		$q_str = "SELECT ap.ship_way FROM ap, ap_special
							WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_cat='a' AND 
										ap_special.ord_num='".$ord_num."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result))
		{		
			$mk =1;
			if ($row['ship_way'] == '')
			{
				return 0;
			}
		}	
	}
		return $mk;
	} // end func			


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_po($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_ord_po() {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$where_str = '';
		$i = 0;					

	//2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$where_str = " AND ap.dept LIKE '".$sale_mang[$i]."%'";		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$where_str = "AND ap.dept = '$user_dept'";
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$where_str = "AND ap.dept = '$user_dept'";
	}




	$i = 0;
   
		$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name
								 FROM ap, supl, ap_det, bom_lots, lots_use 
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_det.ap_num 
								 	 AND ap_det.bom_id = bom_lots.id AND bom_lots.lots_used_id = lots_use.id
								 	 AND ap_det.mat_cat = 'l' AND ap.status = 12 AND ap.rcv_rmk = 0 ".$where_str;

		if ($str = $argv['SCH_ord'] )  { 
			$q_header.= " AND lots_use.smpl_code  like '%".$str."%'";
			}
// echo $q_header."<br>"; 
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][$i]=$row1;
			$i++;
		}

		$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name
								 FROM ap, supl, ap_det, bom_acc, acc_use 
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_det.ap_num 
								 	 AND ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id
								 	 AND ap_det.mat_cat = 'a' AND ap.status = 12 AND ap.rcv_rmk = 0 ".$where_str;

		if ($str = $argv['SCH_ord'] )  { 
			$q_header.= " AND acc_use.smpl_code  like '%".$str."%'";
			}
//echo $q_header."<br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][$i]=$row1;
			$i++;
		}


		$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name
								 FROM ap, supl, ap_special 
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_special.ap_num 								 	
								 	 AND ap.status = 12 AND ap.rcv_rmk = 0 ".$where_str;

		if ($str = $argv['SCH_ord'] )  { 
			$q_header.= " AND ap_special.ord_num  like '%".$str."%'";
			}
//echo $q_header."<br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][$i]=$row1;
			$i++;
		}

		if(!isset($op['apply'])) 	$op['apply'] = array();	
		return $op;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_ord_num($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_num($po_id,$po_cate,$mat_code) {
		$sql = $this->sql;
		if($po_cate == 0)
		{
			if(substr($mat_code,0,1) == 'F')
			{
				$q_str = "SELECT wi_num FROM wi, ap_det, bom_lots 
									WHERE ap_det.bom_id = bom_lots.id AND bom_lots.wi_id = wi.id AND ap_det.id =".$po_id;
				$q_result = $sql->query($q_str);	
				$row1 = $sql->fetch($q_result);						
			}else{
				$q_str = "SELECT wi_num FROM wi, ap_det, bom_acc 
									WHERE ap_det.bom_id = bom_acc.id AND bom_acc.wi_id = wi.id AND ap_det.id =".$po_id;
				$q_result = $sql->query($q_str);	
				$row1 = $sql->fetch($q_result);					
			
			}
		}else{
				$q_str = "SELECT ord_num FROM ap_special
									WHERE ap_special.id =".$po_id;
				$q_result = $sql->query($q_str);	
				$row1 = $sql->fetch($q_result);					
		
		}
		return $row1[0];
	}// enf function


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_acc_cost($ord_num,$wi_id) 加入副料成本(PO,BOM)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_acc_cost($ord_num,$wi_id) {
		$row=array();
		$sql = $this->sql;
		$cury_d = $GLOBALS['cury_d'];

		$q_str="SELECT sum(ap_det.amount) as po_amount, ap.currency
						FROM  ap_det, ap, bom_acc
						WHERE ap_det.ap_num = ap.ap_num AND ap_det.bom_id = bom_acc.id 
									AND ap.status = 12 AND
									ap_det.mat_cat = 'a' AND bom_acc.wi_id = '".$wi_id."'									
					  GROUP BY currency";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		 $cost['po'] = 0;
		$bom_ary = array();
		while($row = $sql->fetch($q_result))
		{
			
			$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['USD']); // 累加PO成本			
		}

		$q_str="SELECT sum(ap_special.amount) as po_amount, ap.currency
						FROM   ap_special, ap
						WHERE  ap_special.ap_num = ap.ap_num AND									 
									 ap_special.mat_cat = 'a' AND ap_special.ord_num = '".$ord_num."'
						GROUP BY currency";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			
			$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['USD']); // 累加PO成本			
		}

	
		$q_str = "UPDATE s_order SET po_acc_cost='".$cost['po']."'  WHERE `order_num` = '".$ord_num."' ;";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		
		return $cost['po'];
		
	} // end func	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_lots_cost($ord_num,$wi_id) 加入主料成本(PO,BOM)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_lots_cost($ord_num,$wi_id) {
		$row=array();
		$sql = $this->sql;
		$cury_d = $GLOBALS['cury_d'];

		$q_str="SELECT sum(ap_det.amount) as po_amount, ap.currency
						FROM  ap_det, ap, bom_lots
						WHERE ap_det.ap_num = ap.ap_num AND ap_det.bom_id = bom_lots.id 
									AND ap.status = 12 AND
									ap_det.mat_cat = 'l' AND bom_lots.wi_id = '".$wi_id."'									
					  GROUP BY currency";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		 $cost['po'] = 0;
		$bom_ary = array();
		while($row = $sql->fetch($q_result))
		{
			
			$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['USD']); // 累加PO成本			
		}

		$q_str="SELECT sum(ap_special.amount) as po_amount, ap.currency
						FROM   ap_special, ap
						WHERE  ap_special.ap_num = ap.ap_num AND ap.status = 12 AND									 
									 ap_special.mat_cat = 'l' AND ap_special.ord_num = '".$ord_num."'
						GROUP BY currency";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			
			$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['USD']); // 累加PO成本			
		}

	
		$q_str = "UPDATE s_order SET po_mat_cost='".$cost['po']."'  WHERE `order_num` = '".$ord_num."' ;";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		
		return $cost['po'];
		
	} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_det_group($num) 依po_spare匯總ap_det資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_det_group($num) {
		$ap_num=array();
		$sql = $this->sql;

		$q_str="SELECT  wi_id, mat_cat, po_spare, sum(ap_qty)as qty, unit, sum(po_qty) as po_qty, po_unit,
										range, bom_id, sum(amount) as amount, prics, prc_unit, base_qty
						FROM  ap_det 
						WHERE ap_det.ap_num =	'".$num."'
						GROUP BY po_spare";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$rtn_ary = array();

		while($row = $sql->fetch($q_result))
		{			 
			$tmp = $this->get_material_field($row['mat_cat'], $row['bom_id']);
			$row['mat_code'] = $tmp['mat_code'];
			$row['mat_color'] = $tmp['color'];
			$row['ord_num'] = $tmp['ord_num'];
			$row['mat_name'] = $tmp['mat_name'];
			$row['use_for'] = $tmp['use_for'];
			$rtn_ary[] = $row;
		}
		return $rtn_ary;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_material_field(mat_cat, bom_id) 取得物料明細
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_material_field($mat_cat, $bom_id) {
		$ap_num=array();
		$sql = $this->sql;
		if($mat_cat == 'l')
		{
			$q_str="SELECT  lots_code as mat_code, bom_lots.color, lots_use.smpl_code as ord_num,
			                lots_name as mat_name, use_for
							FROM  	bom_lots, lots_use
							WHERE 	bom_lots.lots_used_id = lots_use.id AND bom_lots.id =	'".$bom_id."'";
		}else{
			$q_str="SELECT  acc_code as mat_code, bom_acc.color,  acc_use.smpl_code as ord_num,
			                acc_name as mat_name, use_for
							FROM  	bom_acc, acc_use 
							WHERE 	bom_acc.acc_used_id = acc_use.id AND bom_acc.id =	'".$bom_id."'";	
		}

		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		
		return $row;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_special($num) 依po_spare匯總ap_det資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_special($id) {
		$ap_num=array();
		$sql = $this->sql;

		$q_str="SELECT  mat_cat, po_spare, ap_qty, unit, amount, prics, prc_unit, ord_num,
										mat_code, color, ap_num, po_qty, range, use_for
						FROM  ap_special 
						WHERE ap_special.id =	'".$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$rtn_ary = array();

		if($row = $sql->fetch($q_result))
		{			 
			return $row;
		}
		
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rnew_rcv_qty($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rnew_rcv_qty($id) {

		$sql = $this->sql;
		$rcvd = array();

			$q_str = "SELECT sum(rcv_po_link.qty) as qty
							FROM   ap_special, ap, receive_det, rcv_po_link, receive
							WHERE  ap_special.ap_num = ap.ap_num AND receive.po_id = ap.id AND
							       receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id = ap_special.id AND
							       receive.rcv_num = receive_det.rcv_num AND rcv_po_link.po_id = '".$id."'
							       GROUP BY rcv_po_link.po_id";

			$q_result = $sql->query($q_str);
			if($row = $sql->fetch($q_result))
			{
				$q_str = "UPDATE ap_det SET rcv_qty ='".$row['qty']."' WHERE id= '".$id."'";
				$q_result = $sql->query($q_str);
			}

			$q_str = "SELECT sum(rcv_po_link.qty) as qty
							FROM   ap_special, ap, receive_det, rcv_po_link, receive
							WHERE  ap_special.ap_num = ap.ap_num AND receive.po_id = ap.id AND
							       receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id = ap_special.id AND
							       receive.rcv_num = receive_det.rcv_num AND rcv_po_link.po_id = '".$id."'
							       GROUP BY rcv_po_link.po_id";
			$q_result = $sql->query($q_str);
			if($row = $sql->fetch($q_result))
			{
				$q_str = "UPDATE `ap_special` SET `rcv_qty` = '".$row['qty']."' WHERE `id` = '".$id."'";
				$q_result = $sql->query($q_str);
			}

		return $row['qty'];
	} // end func	
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ship_rmk($parm)
#
#		加入出貨備註
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ship_rmk($parm) {

		$sql = $this->sql;
		if($parm['special'] == 2){$table = 'ap_special';}else{$table='ap_det';}

		$q_str = "UPDATE ".$table." SET ship_rmk ='".$parm['ship_rmk'].
								"' WHERE po_spare= '".	$parm['po_spare'] ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func		
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ship_rmk($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ship_rmk($ord_num,$cat) {

		$sql = $this->sql;
		$rmk_str = '';
		if($cat == 'l')
		{
			$q_str = "SELECT DISTINCT lots_use.lots_code as mat_code, bom_lots.color as color, 
											 ap_det.ship_rmk as ship_rmk
								FROM   ap_det, bom_lots, lots_use
								WHERE  ap_det.mat_cat = 'l' AND ap_det.bom_id = bom_lots.id AND 
											 bom_lots.lots_used_id = lots_use.id AND lots_use.smpl_code = '".$ord_num."' AND
											 ship_rmk <> ''";
		}else{
			$q_str = "SELECT DISTINCT acc_use.acc_code as mat_code, bom_acc.color as color, 
											 ap_det.ship_rmk as ship_rmk
								FROM   ap_det, bom_acc, acc_use
								WHERE  ap_det.mat_cat = 'a' AND ap_det.bom_id = bom_acc.id AND 
											 bom_acc.acc_used_id = acc_use.id AND acc_use.smpl_code = '".$ord_num."' AND
											 ship_rmk <> ''";
		}		

			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$rmk_str .= $row['mat_code'].'('.$row['color'].') : '.$row['ship_rmk'].'|';
			}

			$q_str = "SELECT mat_code, color, ship_rmk
								FROM   ap_special
								WHERE  ord_num = '".$ord_num."' AND mat_cat = '".$cat."'";

			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$rmk_str .= $row['mat_code'].'('.$row['color'].') : '.$row['ship_rmk'].'|';
			}
			$rmk_str = substr($rmk_str,0,-1);

		return $rmk_str;
	} // end func		
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_remain_fabric($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_remain_fabric($parm) {
//AND (ap_qty*1.07) < po_qty
			$sql = $this->sql;
			$rtn = $exc_rec = array();
//			if($sch_date == '' && $ord_num == '') 	$sch_date = increceDaysInDate(date('Y-m-d'),-30);

			$where_str = '';
			if($parm['ord_num']) $where_str .= " AND lots_use.smpl_code like '%".$parm['ord_num']."%'";
			if($parm['sch_str']) $where_str .= " AND po_eta >= '".$parm['sch_str']."'";
			if($parm['sch_end']) $where_str .= " AND po_eta <= '".$parm['sch_end']."'";
//額外採購
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id,
													ap_det.po_unit
								FROM  		ap_det , bom_lots, lots_use,  ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.special > 0 AND ap.status = 12 AND 
													ap_det.mat_cat = 'l'".$where_str."
								GROUP BY  bom_lots.id";				
//echo $q_str."<BR><BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$exc_rec[] = $row; 
			}

			$reg_where = $spc_where ='';
			$exc_rtn = array();
		  for($i=0; $i<sizeof($exc_rec); $i++)
		  {
		  	$reg_where .=" AND bom_id <> '".$exc_rec[$i]['bom_id']."'";
		  	
		  	$q_str = "SELECT 	ap_det.ap_qty,  ap_det.po_qty, ap_det.unit,	ap_det.po_unit, po_spare, 
		  										bom_lots.qty as bom_qty
		  						FROM		ap_det, ap, bom_lots
		  						WHERE   ap_det.ap_num = ap.ap_num AND	 ap_det.bom_id = bom_lots.id AND
		  										ap_det.mat_cat = 'l' AND ap.special = 0 AND
		  										bom_id = '".$exc_rec[$i]['bom_id']."'";
//echo $q_str."<BR><BR>";
		  	$q_result = $sql->query($q_str);
				if($row = $sql->fetch($q_result))
				{
					$tmp_qty = explode(',',$row['bom_qty']);
					$row['ap_qty'] = array_sum($tmp_qty);
					$row['po_qty'] = change_unit_qty($row['po_unit'],$exc_rec[$i]['po_unit'],$row['po_qty']);	
					$exc_rec[$i]['po_qty'] += $row['po_qty'];
					$row['ap_qty'] = change_unit_qty($row['unit'],$exc_rec[$i]['po_unit'],$row['ap_qty']);	
					if(($row['ap_qty']*1.07) < $exc_rec[$i]['po_qty'])
					{
						$exc_rec[$i]['ap_qty'] = $row['ap_qty'];						
					
		  			$q_str = "SELECT 	ap_det.ap_qty,  ap_det.po_qty, ap_det.unit,	ap_det.po_unit,
		  												po_spare, ap_det.bom_id, bom_lots.qty as bom_qty
		  								FROM		ap_det, ap
		  								WHERE   ap_det.ap_num = ap.ap_num AND	ap_det.bom_id = bom_lots.id AND
		  												ap.special = 0 AND ap_det.mat_cat = 'l'
		  												po_spare = '".$row['po_spare']."' AND bom_id <>'".$exc_rec[$i]['bom_id']."'";					
//echo $q_str."<BR><BR>";					
						$q_result = $sql->query($q_str);
						while($row = $sql->fetch($q_result))
						{
							$tmp_qty = explode(',',$row['bom_qty']);
							$row['ap_qty'] = array_sum($tmp_qty);
							$row['ap_qty'] = change_unit_qty($row['unit'],$exc_rec[$i]['po_unit'],$row['ap_qty']);
							$exc_rec[$i]['ap_qty'] += $row['ap_qty'];
							$exc_rec[$i]['po_qty'] += $row['po_qty'];
							$reg_where .=" AND bom_id <> '".$row['bom_id']."'";
						}
						$exc_rtn[] = $exc_rec[$i];
					}					
					continue;					
				}					
				$q_str = "SELECT  ap_special.po_qty, bom_lots.qty as bom_qty, po_unit, lots_use.unit,
													ap_special.id
									FROM  	bom_lots , ap_special, lots_use
									WHERE 	bom_lots.ap_mark = ap_special.ap_num AND bom_lots.lots_used_id = lots_use.id AND 
													bom_lots.color = ap_special.color AND lots_use.lots_code = ap_special.mat_code AND
													bom_lots.id ='".$exc_rec[$i]['bom_id']."'"; 
//echo $q_str."<BR><BR>";			
				$q_result = $sql->query($q_str);	
				if($row = $sql->fetch($q_result))
				{
					$tmp_qty = explode(',',$row['bom_qty']);
					$row['ap_qty'] = array_sum($tmp_qty);
					$row['ap_qty'] = change_unit_qty($row['unit'],$exc_rec[$i]['po_unit'],$row['ap_qty']);	
					$row['po_qty'] = change_unit_qty($row['po_unit'],$exc_rec[$i]['po_unit'],$row['po_qty']);	
					$exc_rec[$i]['po_qty'] += $row['po_qty'];					
					if(($row['ap_qty']*1.07) < $exc_rec[$i]['po_qty'])
					{
						$exc_rec[$i]['ap_qty'] = $row['ap_qty'];
						$exc_rtn[] = $exc_rec[$i];
					}
					
					$spc_where .=" AND ap_special.id <> '".$row['id']."'";
				
				}	  					
		  }
			for($i=0; $i<sizeof($exc_rtn); $i++)
			{
				$str_name = $exc_rtn[$i]['mat_code']."_".$exc_rtn[$i]['color'];
				if(!isset($rtn[$str_name]))
				{
					$rtn[$str_name] =  $exc_rtn[$i];
				}else{
					$po_qty = change_unit_qty($exc_rtn[$i]['po_unit'],$rtn[$str_name]['po_unit'],$exc_rtn[$i]['po_qty']);	
					$ap_qty = change_unit_qty($exc_rtn[$i]['unit'],$rtn[$str_name]['po_unit'],$exc_rtn[$i]['ap_qty']);	
					$rtn[$str_name]['po_qty'] +=  $po_qty;
					$rtn[$str_name]['ap_qty'] +=  $ap_qty;
				}
			}
		
		
		
//一般採購		同單位
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id,	ap_det.po_unit
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.special = 0 AND ap.status = 12 AND 
													ap_det.mat_cat = 'l' AND ap_det.po_unit = ap_det.unit".$where_str.$reg_where.
							" GROUP BY  lots_name, color, ap_det.unit, ap_det.po_unit, ap_det.ap_num
							  HAVING    (sum(ap_det.ap_qty)*1.07) < sum(ap_det.po_qty)";				
//echo $q_str."<BR><BR>";	
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$str_name = $row['mat_code']."_".$row['color'];
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);
				if(!isset($rtn[$str_name]))
				{
					$rtn[$str_name] =  $row;
				}else{
					$po_qty = change_unit_qty($row['po_unit'],$rtn[$str_name]['po_unit'],$row['po_qty']);	
					$ap_qty = change_unit_qty($row['unit'],$rtn[$str_name]['po_unit'],$row['ap_qty']);	
					$rtn[$str_name]['po_qty'] +=  $po_qty;
					$rtn[$str_name]['ap_qty'] +=  $ap_qty;
				}
			}

//一般採購		不同單位
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id,	ap_det.po_unit
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.special = 0 AND ap.status = 12 AND 
													ap_det.mat_cat = 'l' AND ap_det.po_unit <> ap_det.unit".$where_str.$reg_where.
							" GROUP BY  lots_name, color, ap_det.unit, ap_det.po_unit , ap_det.ap_num";		
//echo $q_str."<BR><BR>";	
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				if(!$row['po_unit'])$row['po_unit'] = $row['unit'];
				
				if(($row['ap_qty'] * 1.07) < $row['po_qty'])
				{
					$str_name = $row['mat_code']."_".$row['color'];
					if(!isset($rtn[$str_name]))
					{
						$rtn[$str_name] =  $row;
					}else{
						$po_qty = change_unit_qty($row['po_unit'],$rtn[$str_name]['po_unit'],$row['po_qty']);	
						$ap_qty = change_unit_qty($row['unit'],$rtn[$str_name]['po_unit'],$row['ap_qty']);	
						$rtn[$str_name]['po_qty'] +=  $po_qty;
						$rtn[$str_name]['ap_qty'] +=  $ap_qty;
					}
				}
			}



			$q_str = "SELECT  ap_special.mat_code, ap_special.color, bom_lots.qty as bom_qty, lots_name,
												po_unit, lots_use.unit, ap_special.po_qty
								FROM  	bom_lots , ap_special, lots_use
								WHERE 	bom_lots.ap_mark = ap_special.ap_num AND bom_lots.lots_used_id = lots_use.id AND 
												bom_lots.color = ap_special.color AND lots_use.lots_code = ap_special.mat_code"
												.$where_str.$spc_where;	
//echo $q_str."<BR><BR>";	
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$tmp_qty = explode(',',$row['bom_qty']);
				$row['ap_qty'] = array_sum($tmp_qty);
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				if(($row['ap_qty'] * 1.07) < $row['po_qty'])
				{
					$str_name = $row['mat_code']."_".$row['color'];
					if(!isset($rtn[$str_name]))
					{
						$rtn[$str_name] =  $row;
					}else{
						$po_qty = change_unit_qty($row['po_unit'],$rtn[$str_name]['po_unit'],$row['po_qty']);	
						$ap_qty = change_unit_qty($row['unit'],$rtn[$str_name]['po_unit'],$row['ap_qty']);	
						$rtn[$str_name]['po_qty'] +=  $po_qty;
						$rtn[$str_name]['ap_qty'] +=  $ap_qty;
					}
				}
			}
			


		return $rtn;
	} // end func		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_remain_fabric_det($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_remain_fabric_det($code,$color) {
//AND (ap_qty*1.07) < po_qty
			$sql = $this->sql;
			$rtn = $exc_rec = array();

//一般採購
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.status = 12 AND ap.special = 0 AND
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													bom_lots.color = '".$color."'
								GROUP BY  smpl_code";				
//echo $q_str."<BR><BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				$rtn[$row['smpl_code']] = $row; 
			}

//額外採購
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.status = 12 AND ap.special > 0 AND
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													bom_lots.color = '".$color."'
								GROUP BY  smpl_code";				
//echo $q_str."<BR><BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$row['ap_qty'] = 0;	
				if(!isset($rtn[$row['smpl_code']]))
				{
					$rtn[$row['smpl_code']] =  $row;
				}else{
					$po_qty = change_unit_qty($row['po_unit'],$rtn[$row['smpl_code']]['po_unit'],$row['po_qty']);	
					$ap_qty = change_unit_qty($row['po_unit'],$rtn[$row['smpl_code']]['po_unit'],$row['ap_qty']);	
					$rtn[$row['smpl_code']]['po_qty'] +=  $po_qty;
					$rtn[$row['smpl_code']]['ap_qty'] +=  $ap_qty;
				}				
			}



//預先採購
			$q_str = "SELECT  ap_special.mat_code, ap_special.color, bom_lots.qty as bom_qty, lots_name,
												po_unit, lots_use.unit, ap_special.po_qty, lots_use.smpl_code
								FROM  	bom_lots , ap_special, lots_use
								WHERE 	bom_lots.ap_mark = ap_special.ap_num AND bom_lots.lots_used_id = lots_use.id AND 
												bom_lots.color = ap_special.color AND lots_use.lots_code = ap_special.mat_code AND
												lots_use.lots_code = '".$code."' AND bom_lots.color = '".$color."'"
												;	
//echo $q_str."<BR><BR>";	
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$tmp_qty = explode(',',$row['bom_qty']);
				$row['ap_qty'] = array_sum($tmp_qty);
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				
				$str_name = $row['mat_code']."_".$row['color'];
				if(!isset($rtn[$str_name]))
				{
					$rtn[$row['smpl_code']] =  $row;
				}else{
					$po_qty = change_unit_qty($row['po_unit'],$rtn[$str_name]['po_unit'],$row['po_qty']);	
					$ap_qty = change_unit_qty($row['po_unit'],$rtn[$str_name]['po_unit'],$row['ap_qty']);	
					$rtn[$row['smpl_code']]['po_qty'] +=  $po_qty;
					$rtn[$row['smpl_code']]['ap_qty'] +=  $ap_qty;
				}
			}
			


		return $rtn;
	} // end func		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cut_report_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cut_report_lots_det($id=0,$where_str='',$ord_num='') {

		$sql = $this->sql;
		$lots = array();
		$q_str="SELECT bom_lots.*, lots_use.lots_code, lots_use.lots_name, lots_use.unit, lots_use.use_for, 
						lots_use.est_1, lots_use.id as use_id, ap.status, ap.po_num, ap.currency, ap.ann_num
						FROM bom_lots, lots_use LEFT JOIN ap ON ap.ap_num = bom_lots.ap_mark
						WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 
							AND bom_lots.wi_id='".$id."' ".$where_str;
							
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$qty=explode(',',$row1['qty']);
			$tmp_qty=0;
			for ($i=0; $i<sizeof($qty); $i++)
			{
				$tmp_qty=$tmp_qty+$qty[$i];				
			}
			$row1['total'] = $tmp_qty;
			$row1['spec_ap'] = $this->get_special_ap($row1['id'],'l',$row1['ap_mark']);	
			if($row1['pp_mark'] == 0)
			{
				$row1['po'] = $this->get_po_supl_qty($row1['id'],'l',$ord_num);						
				$row1['po']['det_special'] = 0;
			}else{
				$row1['po'] = $this->get_pp_qty($row1['id'],$row1['ap_mark'],'l',$row1['lots_code'],$row1['color']);	
				$row1['po']['det_special'] = 1;
			}
			$lots[]=$row1;
		}
		return $lots;
	} // end func


  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_po_status($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_status($wi_id,$mat_code) {
  $sql = $this->sql;
  
  if($mat_code == 'l')
  {
    $q_str = "SELECT * FROM `bom_lots` WHERE `wi_id` = '".$wi_id."'";
    $q_result = $sql->query($q_str);
    while ($row1 = $sql->fetch($q_result)) {
			$ap[]=$row1;
		}
  } else {
    $q_str = "SELECT * FROM `bom_acc` WHERE `wi_id` = '".$wi_id."'";
    
    $q_result = $sql->query($q_str);	
    while ($row1 = $sql->fetch($q_result)) {
			$ap[]=$row1;
		}
  }
	// echo $q_str . '<br>';
  if(!empty($ap))return $ap;
}// enf function



	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> track_po_status(查詢語法) 採購單追蹤總表(採購總表新增20120531)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function track_po_status($sql_header,$sql_where,$sql_orderby,$title=''){

	$sql = $this->sql;
	$srh = new SEARCH();
    
	$q_str = $sql_header.$sql_where.$sql_orderby;
	/* echo $q_str; */
	/* echo $q_str;
	exit; */
	if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	while($row1 = $sql->fetch($q_result)){
		$op['tmp'][]=$row1;
	}
    
	// echo $q_str;
	// echo "<br>";
	
	$po_id='';
	
	$ydqty = $pcqty = $meterqty = $dzqty = $SFqty = $grossqty = $kgqty = $setqty = $unitqty = $lbqty = $coneqty = $inchqty =0;
	$newindex=0;
	$bomNewIndex=0;
	$Po_etaNewIndex=0;
	$pa_num='';
	$dept='';
	$supplier='';
	$po_num='';
	$po_note='';
	$bom_id='';
	$category='';
	$po_etd='';
	$po_unit='';
	$leadtime='';
	$special='';
	$order_id='';
	$EtaExist=false;
	foreach ($op['tmp'] as $key => $value)
 	{
		//leadtime
		//unit
		/* echo "<br>".$key."<br>"; */
		if($po_id == '')
		{
			//第一筆資料

			$op['po'][$newindex]['id'] = $po_id = $op['tmp'][$key]['po_id'];
			$op['po'][$newindex]['ap_num'] = $pa_num = $op['tmp'][$key]['ap_num'];
			$op['po'][$newindex]['dept'] = $dept = $op['tmp'][$key]['dept'];
			$op['po'][$newindex]['supl_s_name'] = $supplier = $op['tmp'][$key]['supl_s_name'];
			$op['po'][$newindex]['po_num'] = $po_num = $op['tmp'][$key]['po_num'];
			$op['po'][$newindex]['po_note'] = $po_note = $op['tmp'][$key]['po_note'];
			$op['po'][$newindex]['special'] = $special = $op['tmp'][$key]['special'];
			if($special == 2)
			{
				$op['po'][$newindex]['bom_id'] = $bom_id = '';
				$op['po'][$newindex]['bom_unit'] = $po_unit = $op['tmp'][$key]['apspecialunit'];
				
				$op['po'][$newindex]['ord_num'][0] = $order_id = $op['tmp'][$key]['ord_num'];
				$op['po'][$newindex]['mat_cat'] = $category = $op['tmp'][$key]['apspecialcat'];
				//PO的ETD有可能會有多筆資料
				$op['po'][$newindex]['po_eta'][$Po_etaNewIndex] = $po_etd = $op['tmp'][$key]['apspecialeta'];
				
				if($po_unit=='pc') $pcqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='yd') $ydqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='meter') $meterqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='dz') $dzqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='SF') $SFqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='gross') $grossqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='kg') $kgqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='set') $setqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='unit') $unitqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='lb') $lbqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='cone') $coneqty+=$op['tmp'][$key]['apspecialqty'];
				if($po_unit=='inch') $inchqty+=$op['tmp'][$key]['apspecialqty'];
				$Po_etaNewIndex++;
			}
			else
			{
				
				$op['po'][$newindex]['bom_id'][$bomNewIndex] = $bom_id = $op['tmp'][$key]['bom_id'];
				$op['po'][$newindex]['bom_unit'][$bomNewIndex]= $po_unit = $op['tmp'][$key]['apdetunit'];
				
				/* $op['po'][$newindex]['ord_num'] = $order_id = ''; */
				$op['po'][$newindex]['mat_cat'] = $category = $op['tmp'][$key]['apdetcat'];
				//PO的ETD有可能會有多筆資料
				$op['po'][$newindex]['po_eta'][$Po_etaNewIndex] = $po_etd = $op['tmp'][$key]['apdeteta'];
				if($po_unit=='pc') $pcqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='yd') $ydqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='meter') $meterqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='dz') $dzqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='SF') $SFqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='gross') $grossqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='kg') $kgqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='set') $setqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='unit') $unitqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='lb') $lbqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='cone') $coneqty+=$op['tmp'][$key]['apdetqty'];
				if($po_unit=='inch') $inchqty+=$op['tmp'][$key]['apdetqty'];
				$bomNewIndex++;
				$Po_etaNewIndex++;
			}

		}
		else
		{
			//非第一筆資料
			
			if($po_id == $op['tmp'][$key]['po_id'])
			{
				//表示相同PO的資料,繼續累加
				//除了數量，還有bom id也要繼續記錄
				//額外採購部分，ETD日期可能會有兩組以上，以最近日算leadtime，但是日期要全部呈現在網頁上
				
				/* echo $po_id."<br>"; */
				if($op['tmp'][$key]['special'] == 2)
				{
					$EtaExist = false;
					foreach($op['po'][$newindex]['po_eta'] as $po_eta_key => $po_eta_value)
					{
						if($op['po'][$newindex]['po_eta'][$po_eta_key]==$op['tmp'][$key]['apspecialeta'])
						{
							$EtaExist = true;
						}
					}
					if(!$EtaExist)
					{
						if($po_etd = $op['tmp'][$key]['apspecialeta']!='')
						{
							$op['po'][$newindex]['po_eta'][$Po_etaNewIndex] = $po_etd = $op['tmp'][$key]['apspecialeta'];
							$Po_etaNewIndex++;
							$EtaExist=false;
						}
					}
				
						
					//這邊不處理Bom id,因為額外採購可以直接取得訂單號
					$op['po'][$newindex]['bom_id'] = $bom_id = '';
					$op['po'][$newindex]['bom_unit'] = $po_unit = $op['tmp'][$key]['apspecialunit'];
					
					if($po_unit=='pc') $pcqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='yd') $ydqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='meter') $meterqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='dz') $dzqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='SF') $SFqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='gross') $grossqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='kg') $kgqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='set') $setqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='unit') $unitqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='lb') $lbqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='cone') $coneqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='inch') $inchqty+=$op['tmp'][$key]['apspecialqty'];
				}
				else
				{
					
					$EtaExist = false;
					foreach($op['po'][$newindex]['po_eta'] as $po_eta_key => $po_eta_value)
					{
						if($op['po'][$newindex]['po_eta'][$po_eta_key]==$op['tmp'][$key]['apspecialeta'])
						{
							$EtaExist = true;
						}
					}
					if(!$EtaExist)
					{
						if($po_etd = $op['tmp'][$key]['apspecialeta']!='')
						{
							$op['po'][$newindex]['po_eta'][$Po_etaNewIndex] = $po_etd = $op['tmp'][$key]['apspecialeta'];
							$Po_etaNewIndex++;
							$EtaExist=false;
						}
					}
					//這邊有問題，序號會跳($bomNewIndex移上來後，已解決)
					if($bom_id != $op['tmp'][$key]['bom_id'])
					{
						$op['po'][$newindex]['bom_id'][$bomNewIndex] = $bom_id = $op['tmp'][$key]['bom_id'];
						$op['po'][$newindex]['bom_unit'][$bomNewIndex]= $po_unit = $op['tmp'][$key]['apdetunit'];
						$bomNewIndex++;
					}
					
					if($po_unit=='pc') $pcqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='yd') $ydqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='meter') $meterqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='dz') $dzqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='SF') $SFqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='gross') $grossqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='kg') $kgqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='set') $setqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='unit') $unitqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='lb') $lbqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='cone') $coneqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='inch') $inchqty+=$op['tmp'][$key]['apdetqty'];
					
				}
				/* $myqty+=$op['tmp'][$key]['po_qty']; */
			}
			else
			{
				//同一張PO已經完成，開始計算leadtime
				if(sizeof($op['po'][$newindex]['po_eta']) > 1)
				{
					$earlydate='';
					//多筆ETD，須取出最靠近目前日期的日期來運算leadtime
					foreach($op['po'][$newindex]['po_eta'] as $get_eta_key => $get_eta_value)
					{
						if($earlydate=='')
						{
							$earlydate = strtotime($op['po'][$newindex]['po_eta'][$get_eta_key]);
						}
						else
						{
							if(strtotime($op['po'][$newindex]['po_eta'][$get_eta_key]) < $earlydate)
							{
								$earlydate = strtotime($op['po'][$newindex]['po_eta'][$get_eta_key]);
							}
						}
					}
					$op['po'][$newindex]['sort'] = floor(($earlydate - strtotime(date('Y-m-d')))/86400);
				}
				else
				{
						//只有一筆ETD，直接取出日期來運算leadtime
						$op['po'][$newindex]['sort'] = floor((strtotime($op['po'][$newindex]['po_eta'][0]) - strtotime(date('Y-m-d')))/86400);
				}
				
				/* $op['po'][$newindex]['qty'] = $myqty; */
				$op['po'][$newindex]['ydqty'] = $ydqty;
				$op['po'][$newindex]['pcqty'] = $pcqty;
				$op['po'][$newindex]['meterqty'] = $meterqty;
				$op['po'][$newindex]['dzqty'] = $dzqty;
				$op['po'][$newindex]['SFqty'] = $SFqty;
				$op['po'][$newindex]['grossqty'] = $grossqty;
				$op['po'][$newindex]['kgqty'] = $kgqty;
				$op['po'][$newindex]['setqty'] = $setqty;
				$op['po'][$newindex]['unitqty'] = $unitqty;
				$op['po'][$newindex]['lbqty'] = $lbqty;
				$op['po'][$newindex]['coneqty'] = $coneqty;
				$op['po'][$newindex]['inchqty'] = $inchqty;

				//非相同資料，重新產生一個新的陣列索引

				$newindex++;
				$bomNewIndex=0;
				$Po_etaNewIndex=0;
				$ydqty = $pcqty = $meterqty = $dzqty = $SFqty = $grossqty = $kgqty = $setqty = $unitqty = $lbqty = $coneqty = $inchqty = 0;
				$pa_num = $dept = $supplier = $po_num = $po_note = $bom_id = $category = $po_etd = $po_unit = $leadtime = $special = $order_id='';
				
				//請抄襲第一筆資料的程式內容
				$op['po'][$newindex]['id'] = $po_id = $op['tmp'][$key]['po_id'];
				$op['po'][$newindex]['ap_num'] = $pa_num = $op['tmp'][$key]['ap_num'];
				$op['po'][$newindex]['dept'] = $dept = $op['tmp'][$key]['dept'];
				$op['po'][$newindex]['supl_s_name'] = $supplier = $op['tmp'][$key]['supl_s_name'];
				$op['po'][$newindex]['po_num'] = $po_num = $op['tmp'][$key]['po_num'];
				$op['po'][$newindex]['po_note'] = $po_note = $op['tmp'][$key]['po_note'];
				$op['po'][$newindex]['special'] = $special = $op['tmp'][$key]['special'];
				
				if($special == 2)
				{
					$op['po'][$newindex]['bom_id'] = $bom_id = '';
					$op['po'][$newindex]['bom_unit'] = $po_unit = $op['tmp'][$key]['apspecialunit'];
					
					$op['po'][$newindex]['ord_num'][0] = $order_id = $op['tmp'][$key]['ord_num'];
					$op['po'][$newindex]['mat_cat'] = $category = $op['tmp'][$key]['apspecialcat'];
					//PO的ETD有可能會有多筆資料
					$op['po'][$newindex]['po_eta'][$Po_etaNewIndex] = $po_etd = $op['tmp'][$key]['apspecialeta'];
					
					if($po_unit=='pc') $pcqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='yd') $ydqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='meter') $meterqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='dz') $dzqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='SF') $SFqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='gross') $grossqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='kg') $kgqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='set') $setqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='unit') $unitqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='lb') $lbqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='cone') $coneqty+=$op['tmp'][$key]['apspecialqty'];
					if($po_unit=='inch') $inchqty+=$op['tmp'][$key]['apspecialqty'];
					$Po_etaNewIndex++;
				}
				else
				{
					
					$op['po'][$newindex]['bom_id'][$bomNewIndex] = $bom_id = $op['tmp'][$key]['bom_id'];
					$op['po'][$newindex]['bom_unit'][$bomNewIndex]= $po_unit = $op['tmp'][$key]['apdetunit'];
					
					/* $op['po'][$newindex]['ord_num'] = $order_id = ''; */
					$op['po'][$newindex]['mat_cat'] = $category = $op['tmp'][$key]['apdetcat'];
					//PO的ETD有可能會有多筆資料
					$op['po'][$newindex]['po_eta'][$Po_etaNewIndex] = $po_etd = $op['tmp'][$key]['apdeteta'];
					if($po_unit=='pc') $pcqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='yd') $ydqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='meter') $meterqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='dz') $dzqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='SF') $SFqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='gross') $grossqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='kg') $kgqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='set') $setqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='unit') $unitqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='lb') $lbqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='cone') $coneqty+=$op['tmp'][$key]['apdetqty'];
					if($po_unit=='inch') $inchqty+=$op['tmp'][$key]['apdetqty'];
					$bomNewIndex++;
					$Po_etaNewIndex++;
				}
				
			}
			
		}
 	}
	//最後一筆資料
	if(sizeof($op['po'][$newindex]['po_eta']) > 1)
	{
		$earlydate='';
		//多筆ETD，須取出最靠近目前日期的日期來運算leadtime
		foreach($op['po'][$newindex]['po_eta'] as $get_eta_key => $get_eta_value)
		{
			if($earlydate=='')
			{
				$earlydate = strtotime($op['po'][$newindex]['po_eta'][$get_eta_key]);
			}
			else
			{
				if(strtotime($op['po'][$newindex]['po_eta'][$get_eta_key]) < $earlydate)
				{
					$earlydate = strtotime($op['po'][$newindex]['po_eta'][$get_eta_key]);
				}
			}
		}
		$op['po'][$newindex]['sort'] = floor(($earlydate - strtotime(date('Y-m-d')))/86400);
	}
	else
	{
		//只有一筆ETD，直接取出日期來運算leadtime
		$op['po'][$newindex]['sort'] = floor((strtotime($op['po'][$newindex]['po_eta'][0]) - strtotime(date('Y-m-d')))/86400);
	}
				
	/* $op['po'][$newindex]['qty'] = $myqty; */
	$op['po'][$newindex]['ydqty'] = $ydqty;
	$op['po'][$newindex]['pcqty'] = $pcqty;
	$op['po'][$newindex]['meterqty'] = $meterqty;
	$op['po'][$newindex]['dzqty'] = $dzqty;
	$op['po'][$newindex]['SFqty'] = $SFqty;
	$op['po'][$newindex]['grossqty'] = $grossqty;
	$op['po'][$newindex]['kgqty'] = $kgqty;
	$op['po'][$newindex]['setqty'] = $setqty;
	$op['po'][$newindex]['unitqty'] = $unitqty;
	$op['po'][$newindex]['lbqty'] = $lbqty;
	$op['po'][$newindex]['coneqty'] = $coneqty;
	$op['po'][$newindex]['inchqty'] = $inchqty;
		
	
	//開始取出每張PO的接收記錄
	//(Begin)#################統計每張PO的已接收數量(以單位區分)#################################(Begin)//
	foreach($op['po'] as $po_key => $po_value)
	{
		/* $aaa++; */
		$ydqty_rcv = $pcqty_rcv = $meterqty_rcv = $dzqty_rcv = $SFqty_rcv = $grossqty_rcv = $kgqty_rcv = $setqty_rcv = $unitqty_rcv = $lbqty_rcv = $coneqty_rcv = $inchqty_rcv = 0;
		unset($op['tmp']);
		$sql_str="select receive_det.po_id,receive_det.qty 
					from receive 
					left join receive_det
					on receive.rcv_num = receive_det.rcv_num
					where receive.po_id=".$op['po'][$po_key]['id'];
		//sql執行傳回陣列(需要判斷是否有資料，沒驗收就不會有資料)

		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
	
	
		if (!$q_result = $sql->query($sql_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
	
		while($row1 = $sql->fetch($q_result)){
			$op['tmp'][]=$row1;
		}
		
        if(!empty($op['tmp']))
		if(sizeof($op['tmp']) > 0)
		{
			//表示有撈到資料
			/* explode("|",$str) */
			/* print_r($op['tmp']); */
			
			//判斷此PO每筆接收資料的單位
			foreach($op['tmp'] as $tmp_key => $tmp_value)
			{
				unset($op['unit']);
				$temp = explode("|",$op['tmp'][$tmp_key]['po_id']);
				/* print_r($temp); */
				
				
				for($temp1=0;$temp1<sizeof($temp);$temp1++)
				{
					$sql_str1="select po_unit from ap_det where id=".$temp[$temp1];

					if (!$srh->set_sql($sql)) {
						$this->msg->merge($srh->msg);
						return false;
					}
		
					if (!$q_result1 = $sql->query($sql_str1)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;
					}
		
					while($row11 = $sql->fetch($q_result1)){
						$op['unit'][$temp1]=$row11;
					}
					
				}
				$sameUnit=true;
				$myUnit='';
				
				foreach($op['unit'] as $unit_key => $unit_value)
				{
					if($unit_key > 0)
					{
						if($myUnit != $op['unit'][$unit_key]['po_unit'])
						{
							$sameUnit=false;
							break;
						}
					}
					else
					{
						$myUnit=$op['unit'][$unit_key]['po_unit'];
					}
					/* $op['unit'][$unit_key]['unit'] */
				}
				
				if($sameUnit)
				{
					/* echo "true<br>"; */
					
					if($myUnit=='yd') $ydqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='pc') $pcqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='meter') $meterqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='dz') $dzqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='SF') $SFqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='gross') $grossqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='kg') $kgqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='set') $setqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='unit') $unitqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='lb') $lbqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='cone') $coneqty_rcv+=$op['tmp'][$tmp_key]['qty'];
					if($myUnit=='inch') $inchqty_rcv+=$op['tmp'][$tmp_key]['qty'];
				}
				else
				{
					/* echo "false<br>"; */
				}
				
				
				
				
				/* print_r($op['unit']); */
			}
			$op['po'][$po_key]['ydqty_rcv']=$ydqty_rcv;
			$op['po'][$po_key]['pcqty_rcv']=$pcqty_rcv;
            $op['po'][$po_key]['meterqty_rcv']=$meterqty_rcv;
            $op['po'][$po_key]['dzqty_rcv']=$dzqty_rcv;
            $op['po'][$po_key]['SFqty_rcv']=$SFqty_rcv;
            $op['po'][$po_key]['grossqty_rcv']=$grossqty_rcv;
            $op['po'][$po_key]['kgqty_rcv']=$kgqty_rcv;
            $op['po'][$po_key]['setqty_rcv']=$setqty_rcv;
            $op['po'][$po_key]['unitqty_rcv']=$unitqty_rcv;
            $op['po'][$po_key]['lbqty_rcv']=$lbqty_rcv;
            $op['po'][$po_key]['coneqty_rcv']=$coneqty_rcv;
            $op['po'][$po_key]['inchqty_rcv']=$inchqty_rcv;
			
			
		}
		else
		{
			//表示沒撈到資料，直接塞0
			$op['po'][$po_key]['ydqty_rcv']=0;
			$op['po'][$po_key]['pcqty_rcv']=0;
            $op['po'][$po_key]['meterqty_rcv']=0;
            $op['po'][$po_key]['dzqty_rcv']=0;
            $op['po'][$po_key]['SFqty_rcv']=0;
            $op['po'][$po_key]['grossqty_rcv']=0;
            $op['po'][$po_key]['kgqty_rcv']=0;
            $op['po'][$po_key]['setqty_rcv']=0;
            $op['po'][$po_key]['unitqty_rcv']=0;
            $op['po'][$po_key]['lbqty_rcv']=0;
            $op['po'][$po_key]['coneqty_rcv']=0;
            $op['po'][$po_key]['inchqty_rcv']=0;

		}
		
		//(Begin)#################透過Bom id或ord_num 取得訂單號碼最早的交期(並且存入陣列中)#################################(Begin)//

		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if($op['po'][$po_key]['special'] == 2)
		{
			/* $sql_wi_num="select etd  from wi where wi_num='".."'"; */
			unset($op['tmp_wi']);
			$myetd='';
			foreach($op['po'][$po_key]['ord_num'] as $wi_num_key => $wi_num_value)
			{
				$sql_wi_num="select etd  from wi where wi_num='".$op['po'][$po_key]['ord_num'][$wi_num_key]."'";
				
				if (!$q_result = $sql->query($sql_wi_num)) {
					$this->msg->add("Error ! Database can't access!");
					$this->msg->merge($sql->msg);
					return false;
				}
				
				while($row_bom = $sql->fetch($q_result)){
					
					$op['tmp_wi'][]=$row_bom;
				}
				
				if($myetd=='')
				{
					$myetd = $op['tmp_wi'][$wi_num_key]['etd'];
				}
				else
				{
					if(strtotime($op['tmp_wi'][$wi_num_key]['etd']) < strtotime($myetd) )
					{
						$myetd = $op['tmp_wi'][$wi_num_key]['etd'];
					}
				}	
				
				
			}
			$op['po'][$po_key]['ord_etd'] = $myetd;
		}
		else
		{

			if($op['po'][$po_key]['mat_cat'] == 'l')
			{
				//主料
				unset($op['tmp_bom']);
				$myetd='';
				foreach($op['po'][$po_key]['bom_id'] as $bom_key => $bom_value)
				{
					
					$sql_bom_id="
					select s_order.etd 
					from bom_lots
					left join wi
					on bom_lots.wi_id = wi.id
					left join s_order
					on wi.wi_num = s_order.order_num
					where bom_lots.id=".$op['po'][$po_key]['bom_id'][$bom_key];
					
			
					
					if (!$q_result = $sql->query($sql_bom_id)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;
					}
	
					while($row_bom = $sql->fetch($q_result)){
						$op['tmp_bom'][]=$row_bom;
					}
					if($myetd=='')
					{
						$myetd = $op['tmp_bom'][$bom_key]['etd'];
					}
					else
					{
						if(strtotime($op['tmp_bom'][$bom_key]['etd']) < strtotime($myetd) )
						{
							$myetd = $op['tmp_bom'][$bom_key]['etd'];
						}
					}
				}
				$op['po'][$po_key]['ord_etd'] = $myetd;
				/* print_r($op['tmp_bom']); */
			}
			if($op['po'][$po_key]['mat_cat'] == 'a')
			{
				//副料
				unset($op['tmp_bom']);
				$myetd='';
				foreach($op['po'][$po_key]['bom_id'] as $bom_key => $bom_value)
				{
					$sql_bom_id="
					select s_order.etd 
					from bom_acc
					left join wi
					on bom_acc.wi_id = wi.id
					left join s_order
					on wi.wi_num = s_order.order_num
					where bom_acc.id=".$op['po'][$po_key]['bom_id'][$bom_key];

					if (!$q_result = $sql->query($sql_bom_id)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;
					}
	
					while($row_bom = $sql->fetch($q_result)){
						$op['tmp_bom'][]=$row_bom;
					}
					if($myetd=='')
					{
						$myetd = $op['tmp_bom'][$bom_key]['etd'];
					}
					else
					{
						if(strtotime($op['tmp_bom'][$bom_key]['etd']) < strtotime($myetd) )
						{
							$myetd = $op['tmp_bom'][$bom_key]['etd'];
						}
					}
				}
				$op['po'][$po_key]['ord_etd'] = $myetd;
			}
			
		}
	
		//(End)#################透過Bom id或ord_num 取得訂單號碼最早的交期#################################(End)//
		
	}
	//(End)#################統計每張PO的以接收數量(以單位區分)#################################(End)//
	
	//(Begin)#################以leadtime的大小來排序(leadtime的索引名稱為sort)#################################(Begin)//
	$op['po'] = bubble_sort_desc($op['po']);//由小至大的排序
		
	//(End)#################以leadtime的大小來排序(leadtime的索引名稱為sort)#################################(End)//
	/* print_r($op['po']); */
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_etd_edit($po_num,$po_spare,$po_eta,$special)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function po_etd_edit($ap_num,$po_spare,$po_rev_eta,$special,$update_all){
	$sql = $this->sql;
	
	$special == 1 ? $tbl="ap_special" : $tbl="ap_det";
	if($update_all){
		$q_str = "update $tbl set rev_eta='$po_rev_eta' where ap_num='".$ap_num."'";
	}else{
		$q_str = "update $tbl set rev_eta='$po_rev_eta' where ap_num='".$ap_num."' and po_spare='".$po_spare."'";
	}
	
	
	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
	}

	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_pp($parm) {

$sql = $this->sql;

$q_str = "INSERT INTO ap_det (ap_num,bom_id,mat_cat,unit,ap_qty,order_num,wi_id,mat_id,used_id,color,size) 
	  VALUES('".
				$parm['ap_num']."','".
				$parm['bom_id']."','".
				$parm['mat_cat']."','".
				$parm['unit']."','".																								
				$parm['qty']."','".	
                
				$parm['order_num']."','".																								
				$parm['wi_id']."','".																								
				$parm['mat_id']."','".																								
				$parm['used_id']."','".																								
				$parm['color']."','".																								
				$parm['size']."')";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
	$this->msg->add("Error ! cannot append order");
	$this->msg->merge($sql->msg);
	return false;    
}
$this->msg->add("append apply#: [".$parm['ap_num']."]。") ;
$ord_id = $sql->insert_id();

return $ord_id;

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pp_mat($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pp_mat($num=0,$log_where='') {

	$sql = $this->sql;
		
	$q_str = "
	SELECT 
	ap.*, 
	supl.country, supl.supl_s_name as s_name, supl.dm_way as dm_way2, supl.id as supl_id, supl.usance 
	
	FROM 
	ap, supl 
	
	WHERE 
	ap.sup_code = supl.vndr_no AND 
	ap_num = '$num'
	";
	
	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['ap']=$row;

	//改變Login帳號為名字
	$po_user=$GLOBALS['user']->get(0,$op['ap']['apv_user']);
	$op['ap']['apv_user_id'] = $op['ap']['apv_user'];
	if ($po_user['name'])$op['ap']['apv_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['cfm_user']);
	$op['ap']['cfm_user_id'] = $op['ap']['cfm_user'];
	if ($po_user['name'])$op['ap']['cfm_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['submit_user']);
	$op['ap']['submit_user_id'] = $op['ap']['submit_user'];
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['ap_user']);
	$op['ap']['ap_user_id'] = $op['ap']['ap_user'];
	if ($po_user['name'])$op['ap']['ap_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['au_user']);
	$op['ap']['au_user_id'] = $op['ap']['au_user'];
	if ($po_user['name'])$op['ap']['au_user'] = $po_user['name'];
		



	//請購明細 -- 主料		
	$q_str="
	SELECT 
	ap_det.*, 
	smpl_code as ord_num, 
	lots.lots_code as mat_code, lots.lots_name as mat_name, lots.price1, lots.comp as con1, lots.specify as con2
	
	FROM 
	`ap_det` , `lots_use` , `lots`  

	WHERE 
	`ap_det`.`ap_num` = '".$num."' AND 
	`ap_det`.`mat_cat` = 'l' AND 
	`ap_det`.`used_id` = `lots_use`.`id` AND 
	`lots`.`lots_code` = `lots_use`.`lots_code` 

	ORDER BY lots_use.lots_code
	";
	
	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	// exit;
	while ($row1 = $sql->fetch($q_result)) {
		$op['ap_det'][]=$row1;
	}

	//請購明細 -- 副料
	$q_str="
	SELECT 
	ap_det.*, 
	smpl_code as ord_num, 
	acc.acc_code as mat_code, acc.acc_name as mat_name, acc.price1, acc.des as con1, acc.specify as con2
	
	FROM 
	`ap_det` , `acc_use` , `acc`  

	WHERE 
	`ap_det`.`ap_num` = '".$num."' AND 
	`ap_det`.`mat_cat` = 'a' AND 
	`ap_det`.`used_id` = `acc_use`.`id` AND 
	`acc`.`acc_code` = `acc_use`.`acc_code` 

	ORDER BY acc_use.acc_code
	";
	// echo $q_str."<br>";		
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row1 = $sql->fetch($q_result)) {
		$op['ap_det'][]=$row1;
	} 	
	
		
	return $op;
} // end func
	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pp_order($num=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pp_order($num=0,$log_where='') {

	$sql = $this->sql;
	
	$lots = $this->get_lots_use($num);
	$acc = $this->get_acc_use($num);
	
	$mat_arr = array();
	
	foreach($lots as $k => $v){
		$q_str = "
		SELECT 
		`ap`.`po_num` , `wi`.`id` as `wi_id` , 
		`ap_det`. * , `ap_det`.`id` as `ap_id` 

		FROM `ap_det` 
		LEFT JOIN `ap` ON ( `ap`.`ap_num` = `ap_det`.`ap_num` ) 
		LEFT JOIN `lots_use` ON ( `ap_det`.`used_id` = `lots_use`.`id` ) 
		LEFT JOIN `wi` ON ( `lots_use`.`smpl_code` = `wi`.`wi_num` ) 

		WHERE 
		`ap_det`.`used_id` = '".$v['id']."' AND ( `ap_det`.`bom_id` = '0' )

		GROUP BY `ap_det`.`id`
		;";
		// echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['mat_name'] = $v['lots_name'];
			$row['mat_code'] = $v['lots_code'];
			$row['use_for'] = $v['use_for'];
			$mat_arr[] = $row;
		}
	}
	// echo '<br>';
	foreach($acc as $k => $v){
		$q_str = "
		SELECT 
		`ap`.`po_num`, `wi`.`id` as `wi_id` , 
		`ap_det`. * , `ap_det`.`id` as `ap_id` 

		FROM `ap_det` 
		LEFT JOIN `ap` ON ( `ap`.`ap_num` = `ap_det`.`ap_num` ) 
		LEFT JOIN `acc_use` ON ( `ap_det`.`used_id` = `acc_use`.`id` ) 
		LEFT JOIN `wi` ON ( `acc_use`.`smpl_code` = `wi`.`wi_num` ) 
		
		WHERE 
		`ap_det`.`used_id` = '".$v['id']."' AND ( `ap_det`.`bom_id` = '0' || `ap_det`.`bom_id` = 'NULL' )

		GROUP BY `ap_det`.`id`
		;";
		// echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['mat_name'] = $v['acc_name'];
			$row['mat_code'] = $v['acc_code'];
			$row['use_for'] = $v['use_for'];
			$mat_arr[] = $row;
		}
	}
// print_r($mat_arr);		
	return $mat_arr;
} // end func
	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_pp($mode=0, $where_str='')	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_pp($mode=0) {

$sql = $this->sql;
if($mode == 1)
{
	$argv = $_SESSION['sch_parm'];
}else{
	$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
}

$srh = new SEARCH();
$cgi = array();
if (!$srh->set_sql($sql)) {
	$this->msg->merge($srh->msg);
	return false;
}

$q_header = "SELECT DISTINCT order.*, cust.cust_init_name as cust_iname, s_order.factory, s_order.style											  
						 FROM wi, cust, s_order 
						 LEFT JOIN bom_acc ON wi.id	= bom_acc.wi_id 
		 LEFT JOIN bom_lots ON wi.id = bom_lots.wi_id";
$q_header = "SELECT s_order.*, cust.cust_init_name as cust_iname FROM cust, s_order left join wi on wi.wi_num=s_order.order_num";
if (!$srh->add_q_header($q_header)) {
	$this->msg->merge($srh->msg);
	return false;
}
$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
$srh->add_sort_condition("id DESC");
$srh->row_per_page = 12;

##--*****--2006.11.16頁碼新增 start		##	
$pagesize=10;
if ($argv['PHP_sr_startno']) {
	$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
}else{
	$pages = $srh->get_page(1,$pagesize);
} 

if ($str = $argv['PHP_dept_code'] )  { 
	// $srh->add_where_condition("wi.dept = '$str'", "PHP_dept_code",$str,"Dept. = [ $str ]. "); 
}else{

	//2007/11/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	
	//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
		if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("wi.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
	
	//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}
	
	//部門 : 工廠
	if ($user_dept == 'HJ' || $user_dept == 'LY' || $user_dept == 'CF')	$srh->add_where_condition("s_order.factory = '$user_dept'");
}

if ($str = $argv['PHP_cust'] )  { 
	$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"Search customer = [ $str ]. "); }
if ($str = $argv['PHP_num'] )  { 
	$srh->add_where_condition("wi.wi_num LIKE '%$str%'", "PHP_wi_num",$str,"Search wi # above: [ $str ] "); }

if (!isset($argv['PHP_full']))$argv['PHP_full']=0;
$srh->add_where_condition("s_order.status >= '4'"); 
$srh->add_where_condition("s_order.status <> '5'");
$srh->add_where_condition("s_order.status < '8'");
$srh->add_where_condition("s_order.cust = cust.cust_s_name  AND s_order.cust_ver = cust.ver");
$srh->add_where_condition("wi.status < 1 || wi.status IS NULL");
$srh->add_where_condition("wi.revise < 1 || wi.revise IS NULL");

$result= $srh->send_query2();

if (!is_array($result)) {
	$this->msg->merge($srh->msg);
	return false;		    
}

$this->msg->merge($srh->msg);
if (!$result){   // 當查尋無資料時
	$op['record_NONE'] = 1;
}
$op['wi'] = $result;  // 資料錄 拋入 $op
$op['cgistr_get'] = $srh->get_cgi_str(0);
$op['cgistr_post'] = $srh->get_cgi_str(1);
$op['prev_no'] = $srh->prev_no;
$op['next_no'] = $srh->next_no;
$op['max_no'] = $srh->max_no;
$op['last_no'] = $srh->last_no;
$op['start_no'] = $srh->start_no;
$op['per_page'] = $srh->row_per_page;
// echo $srh->q_str;
##--*****--2006.11.16頁碼新增 start			
$op['maxpage'] =$srh->get_max_page();
$op['pages'] = $pages;
$op['now_pp'] = $srh->now_pp;
$op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end

	return $op;
} // end func






#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->add_bom_by_po
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_bom_by_po($parm) {

	$sql = $this->sql;
	
	$table = ( $parm['mat_cat'] == 'l' )? 'lots' : 'acc' ;
	
	$q_str = "SELECT `bom_id` FROM `ap_det` WHERE `id` = '".$parm['ap_id']."'	;";
	// echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$row = $sql->fetch($q_result);
	if( $row['bom_id'] != '0' ) {
		return false;
	}
	
	$q_str = "SELECT `id` FROM `bom_".$table."` WHERE  `wi_id` = '".$parm['wi_id']."' AND `lots_used_id` = '".$parm['used_id']."' AND `color` = '".$parm['color']."'  ;";
	// echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	if ( $row = $sql->fetch($q_result) ) {
		$bom_id = $row['id'];
	} else {
		$q_str = "INSERT INTO `bom_".$table."`(
		`wi_id`,`".$table."_used_id`,`color`,`qty`,`k_date`,`ap_mark`,`pp_mark`".
		(( $parm['mat_cat'] == 'a' ) ? ",`size`" : "" )."
		) 
		VALUES(
		'".$parm['wi_id']."',
		'".$parm['used_id']."',
		'".$parm['color']."',
		'".$parm['qty']."',
		'".$parm['k_date']."',
		'".$parm['ap_num']."',
		'1'".
		(( $parm['mat_cat'] == 'a' ) ? ",'".$parm['size']."'" : "" ).
		")";
		
		// echo $q_str."<br>";
		// exit;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;
		}

		$bom_id = $sql->insert_id();
	}

	$q_str = "UPDATE `bom_".$table."` SET `ap_mark` ='".$parm['ap_num']."' , `pp_mark` = '1' WHERE `id` = '".$bom_id."' ;";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	
	$q_str = "UPDATE `ap_det` SET `bom_id` ='".$bom_id."' WHERE `id` = '".$parm['ap_id']."' ;";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	
	return true;	
	
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->file_del($id)	刪除ship檔案
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function file_del($id) {
	$sql = $this->sql;
 	$q_str="DELETE FROM ap_file WHERE id='".$id."'";
 	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}     
	return true;
}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->group_ap($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function group_ap($ap_det) {
		
		$ap_det2[0] = $ap_det[0];
		$ap_det2[0]['orders'][0] = $ap_det[0]['ord_num'];
		$k=1;
		for ($i=1; $i<sizeof($ap_det); $i++)
		{
			$mk=0;	$order_add=0;
			for ($j=0; $j< sizeof($ap_det2); $j++)
			{
				if ($ap_det2[$j]['mat_id'] == $ap_det[$i]['mat_id'] && $ap_det2[$j]['color'] == $ap_det[$i]['color'] && $ap_det2[$j]['size'] == $ap_det[$i]['size'] && $ap_det2[$j]['eta'] == $ap_det[$i]['eta'])
				{
					$ap_det2[$j]['ap_qty'] = $ap_det[$i]['ap_qty'] +$ap_det2[$j]['ap_qty'];
					$ap_det2[$j]['po_qty'] = $ap_det[$i]['po_qty'] +$ap_det2[$j]['po_qty'];
					$ap_det2[$j]['rcv_qty'] = $ap_det[$i]['rcv_qty'] +$ap_det2[$j]['rcv_qty'];
					$ap_det2[$j]['amount'] = $ap_det[$i]['amount'] +$ap_det2[$j]['amount'];
					for ($z =0; $z < sizeof($ap_det2[$j]['orders']); $z++)
					{
						if ($ap_det2[$j]['orders'][$z] == $ap_det[$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0)	$ap_det2[$j]['orders'][] = $ap_det[$i]['ord_num'];
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$ap_det2[$k] = $ap_det[$i];
				$ap_det2[$k]['orders'][0] = $ap_det[$i]['ord_num'];
				$k++;
			}
		}
		$sql = $this->sql;
		$ship_total=0;
		foreach($ap_det2 as $key => $value)
		{
			$q_str = "SELECT  * 
					  FROM po_ship_det 
					  where ap_num='".$value['ap_num']."' and mat_id=".$value['mat_id']." and color='".$value['color']."' and size='".$value['size']."'";
			
			if (!$q_result = $sql->query($q_str)) 
			{
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}

			$ship_total=0;
			$my_row=0;
			while ($row = $sql->fetch($q_result))
			{
				$ap_det2[$key]['ship_id'][]=$row['ship_id'];
				$ap_det2[$key]['ship_qty'][]=$row['qty'];
				$ship_total+=$row['qty'];
				$my_row++;
				$q_str = "select * from po_ship where id='".$row['ship_id']."'";
				if (!$q_result1 = $sql->query($q_str)) 
				{
					$this->msg->add("Error ! Database can't access!");
					$this->msg->merge($sql->msg);
					return false;    
				}
				$row1 = $sql->fetch($q_result1);
				$ap_det2[$key]['ship_num'][]=$row1['num'];
				
				
			}
			$ap_det2[$key]['my_row']=$my_row -1 ;
			$ap_det2[$key]['ship_qty_total']=$ship_total;
			$diff=$ship_total - $ap_det2[$key]['po_qty'];
			$ap_det2[$key]['qty_different'] = number_format(($diff/$ap_det2[$key]['po_qty'])*100,2,'.',0);
			
		}
		
		return $ap_det2;
	}

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po_reopen($ap_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_po_reopen($ap_num) {
	$sql = $this->sql;
 	
	$q_str = "select id
			  from apb_det
			  where ap_num = '".$ap_num."'";
 	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	if($row = $sql->fetch($q_result)){
		return 1;
	}else{
		return 0;
	}

}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_supl_tw_rcv_qtys($ap_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_supl_tw_rcv_qtys($po_num,$mat_cat,$mat_id,$color,$size) {

    $sql = $this->sql;
	
	$ap_num = str_replace("PO", "PA", $po_num);
	
    $q_str = "select sum(receive_det.qty) as qty
			  from receive, receive_det
			  where receive_det.ap_num = '".$ap_num."' and receive_det.mat_id = ".$mat_id."
					and receive_det.color = '".$color."' and receive_det.size = '".$size."'
					and receive.rcv_num = receive_det.rcv_num and receive.tw_rcv = 1
			  group by receive_det.ap_num, receive_det.mat_id, receive_det.color, receive_det.size";
	
    $q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);

    return $row['qty'];

}


function get_po_ship_det_qtys($ap_id,$mat_cat,$mat_id,$color,$size) {

    $sql = $this->sql;
	
	$qty = 0;

    $q_str = "select distinct ap_det.po_spare
			  from ap, ap_det
			  where ap.id = ".$ap_id." and ap_det.ap_num = ap.ap_num and ap_det.mat_cat = '".$mat_cat."' and ap_det.mat_id = '".$mat_id."' 
					and ap_det.color = '".$color."' and ap_det.size = '".$size."'; ";
	
    $q_result = $sql->query($q_str);
	if($row = $sql->fetch($q_result)){
		$q_str = "select sum(po_ship_det.qty) as qty
				  from po_ship_det, po_ship
				  where po_ship_det.po_id = '".$row['po_spare']."' and po_ship.id = po_ship_det.ship_id and po_ship.status = 2";
	
		$q_result = $sql->query($q_str);
		if($row = $sql->fetch($q_result)){
			return $row['qty'];
		}else{
			return 0;
		}
	}

    

}



function unlock_apb($ap_id,$ap_num) {

    $sql = $this->sql;
    
    // $q_str = "UPDATE `ap_det` SET `apb_rmk` ='0' WHERE `id` = '".$ap_id."' ;";
    // if(!$q_result = $sql->query($q_str)){
        // return 'error';
    // }
    
    // $q_str = "UPDATE `ap` SET `apb_rmk` ='0' WHERE `ap_num` = '".$ap_num."' ;";
    // if(!$q_result = $sql->query($q_str)){
        // return 'error';
    // }

    return 'ok';    

}


} // end class
?>