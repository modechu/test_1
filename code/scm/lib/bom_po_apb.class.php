<?php

class BOM_PO_APB {



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



function get_bom_apb($fty, $year, $month1, $month2){
	$sql = $this->sql;
	
	if($fty) $where_str = " and s_order.factory = '".$fty."'";
	$ttl_ord_qty = 0;
	$ttl_ship_qty = 0;
	$ttl_bom_USD = 0;
	$ttl_bom_HKD = 0;
	$ttl_bom_NTD = 0;
	$ttl_bom_EUR = 0;
	$ttl_bom_RMB = 0;
	$ttl_apb_USD = 0;
	$ttl_apb_HKD = 0;
	$ttl_apb_NTD = 0;
	$ttl_apb_EUR = 0;
	$ttl_apb_RMB = 0;
	
	# 判斷 BOM 的 bom_id 是否已經計算過了(分多次付款的話，bom_id會重覆計算)
	$fab_bom_id_ary = array();
	$acc_bom_id_ary = array();
	$fab_bom_id_flag = 0;
	$acc_bom_id_flag = 0;
	
	
		$q_str = "select distinct wi.wi_num, wi.id as wi_id, s_order.qty as ord_qty, s_order.id as ord_id
				  from order_partial, wi, s_order
				  where (order_partial.p_etd between '$year-$month1-01' and '$year-$month2-31') and wi.wi_num = order_partial.ord_num
						and s_order.order_num = wi.wi_num and s_order.status between 0 and 12".$where_str;
		
	
	$wi_res = $sql->query($q_str);
	$k = 0;
	while($wi_row = $sql->fetch($wi_res)){
		$rtn[$k]['ord_num'] = $wi_row['wi_num'];
		$rtn[$k]['ord_qty'] = $wi_row['ord_qty'];
		$rtn[$k]['ord_id'] = $wi_row['ord_id'];
		$ttl_ord_qty += $wi_row['ord_qty'];
		#### shipping qty ####
		$q_str = "select sum(ttl_qty) as ship_qty
				  from shipping_doc_qty
				  where ord_num = '".$wi_row['wi_num']."' group by ord_num";
		
		$ship_res = $sql->query($q_str);
		$ship_row = $sql->fetch($ship_res);
		$rtn[$k]['ship_qty'] = $ship_row['ship_qty'];
		$ttl_ship_qty += $ship_row['ship_qty'];
		
		$rtn[$k]['bom_fab']['USD'] = $rtn[$k]['bom_acc']['USD'] = 0;
		$rtn[$k]['bom_fab']['HKD'] = $rtn[$k]['bom_acc']['HKD'] = 0;
		$rtn[$k]['bom_fab']['NTD'] = $rtn[$k]['bom_acc']['NTD'] = 0;
		$rtn[$k]['bom_fab']['EUR'] = $rtn[$k]['bom_acc']['EUR'] = 0;
		$rtn[$k]['bom_fab']['RMB'] = $rtn[$k]['bom_acc']['RMB'] = 0;
		
		$rtn[$k]['po_fab']['USD'] = $rtn[$k]['po_acc']['USD'] = 0;
		$rtn[$k]['po_fab']['HKD'] = $rtn[$k]['po_acc']['HKD'] = 0;
		$rtn[$k]['po_fab']['NTD'] = $rtn[$k]['po_acc']['NTD'] = 0;
		$rtn[$k]['po_fab']['EUR'] = $rtn[$k]['po_acc']['EUR'] = 0;
		$rtn[$k]['po_fab']['RMB'] = $rtn[$k]['po_acc']['RMB'] = 0;
		
		$rtn[$k]['apb_fab']['USD'] = $rtn[$k]['apb_acc']['USD'] = 0;
		$rtn[$k]['apb_fab']['HKD'] = $rtn[$k]['apb_acc']['HKD'] = 0;
		$rtn[$k]['apb_fab']['NTD'] = $rtn[$k]['apb_acc']['NTD'] = 0;
		$rtn[$k]['apb_fab']['EUR'] = $rtn[$k]['apb_acc']['EUR'] = 0;
		$rtn[$k]['apb_fab']['RMB'] = $rtn[$k]['apb_acc']['RMB'] = 0;
		
		#### 主料 ####
		$q_str = "select apb.currency, apb_det.uprice, apb_po_link.po_id, apb_po_link.qty, apb_po_link.id
				  from apb, apb_det, apb_po_link
				  where apb_po_link.ord_num = '".$wi_row['wi_num']."' and apb_det.id = apb_po_link.rcv_id and apb_det.mat_code like 'F%'
						and apb.rcv_num = apb_det.rcv_num and (apb.payment not like '%before%' or apb.status > 2)";
		
		
		$apb_link_res = $sql->query($q_str);
		while($apb_link_row = $sql->fetch($apb_link_res)){
			# apb 主料
			$tmp_cost = 0;
			$tmp_cost = number_format($apb_link_row['qty']*$apb_link_row['uprice'],2,'','');
			$rtn[$k]['apb_fab'][$apb_link_row['currency']] += $tmp_cost;
			${"ttl_apb_".$apb_link_row['currency']} +=  $tmp_cost;
		}
		
		
		# BOM主料成本
		$q_str = "select bom_lots.id, bom_lots.qty
				  from bom_lots
				  where bom_lots.wi_id = ".$wi_row['wi_id']." and bom_lots.ap_mark <> 'stock' and bom_lots.ap_mark <> ''";
		
		$bom_lots_res = $sql->query($q_str);
		while($bom_lots_row = $sql->fetch($bom_lots_res)){
			$tmp_qty = 0;
			$tmp_qty_ary = explode(",",$bom_lots_row['qty']);
			foreach($tmp_qty_ary as $k1 => $v1){
				$tmp_qty += number_format($v1,0,'','');
			}
			# 單價、單位相關資料
			$q_str = "select ap_det.unit, ap_det.po_unit, ap_det.prc_unit,apb_det.uprice, apb.currency
					  from ap_det, apb_po_link, apb_det, apb
					  where ap_det.bom_id = ".$bom_lots_row['id']." and ap_det.mat_cat = 'l' and apb_po_link.po_id = ap_det.id and apb_po_link.ord_num = '".$wi_row['wi_num']."' 
							and apb_det.id = apb_po_link.rcv_id	and apb.rcv_num = apb_det.rcv_num and (apb.payment not like '%before%' or apb.status > 2)";
			$ap_det_res = $sql->query($q_str);
			if(!$ap_det_row = $sql->fetch($ap_det_res)){
				# 沒有 apb 資料時，抓ap_det的
				$q_str = "select ap_det.unit, ap_det.po_unit, ap_det.prc_unit, ap_det.prics as uprice, ap.currency
					  from ap_det, ap
					  where ap_det.bom_id = ".$bom_lots_row['id']." and ap_det.mat_cat = 'l' and ap.ap_num = ap_det.ap_num";
				$ap_det_res = $sql->query($q_str);
				$ap_det_row = $sql->fetch($ap_det_res);
			}
			$tmp_qty = change_unit_qty($ap_det_row['unit'],$ap_det_row['po_unit'],$tmp_qty);
			$tmp_cost = 0;
			$tmp_cost = number_format($tmp_qty*$ap_det_row['uprice'],2,'','');
			
			$rtn[$k]['bom_fab'][$ap_det_row['currency']] += $tmp_cost;
			${"ttl_bom_".$ap_det_row['currency']} += $tmp_cost;
		}
		
		
		
		
		# ap_det
			/* $q_str = "select bom_id, po_qty, unit, po_unit, prc_unit
					  from ap_det
					  where id = ".$apb_link_row['po_id'];
			
			$ap_det_res = $sql->query($q_str);
			if($ap_det_row = $sql->fetch($ap_det_res)){
				foreach($fab_bom_id_ary as $b_k => $b_v){
					if($ap_det_row['bom_id'] == $b_v){
						 $fab_bom_id_flag = 1;
						 break;
					}
				}
				
				if(!$fab_bom_id_flag){
					$fab_bom_id_ary[] = $ap_det_row['bom_id'];
					
					
				}
				$fab_bom_id_flag = 0;
				
				# PO主料成本，物料可能會不只採購一次，所以再跑一次迴圈
				$q_str = "select po_qty, prics, unit, po_unit, prc_unit
						  from ap_det
						  where bom_id = ".$ap_det_row['bom_id']." and mat_cat = 'l'";
				$po_fab_res = $sql->query($q_str);
				if($po_fab_row = $sql->fetch($po_fab_res)){
					$po_fab_row['prics'] = change_unit_price($po_fab_row['po_unit'],$po_fab_row['prc_unit'],$po_fab_row['prics']);
					$tmp_cost = 0;
					$tmp_cost = number_format($po_fab_row['prics']*$po_fab_row['po_qty'],2,'','');
					$rtn[$k]['po_fab'][$apb_link_row['currency']] += $tmp_cost;
					${"ttl_po_".$apb_link_row['currency']} += $tmp_cost;
				}
			} */
			
			
			
			
		
		#### 副料 ####
		$q_str = "select apb.currency, apb_det.uprice, apb_po_link.po_id, apb_po_link.qty, apb_po_link.id
				  from apb, apb_det, apb_po_link
				  where apb_po_link.ord_num = '".$wi_row['wi_num']."' and apb_det.id = apb_po_link.rcv_id and apb_det.mat_code like 'A%'
						and apb.rcv_num = apb_det.rcv_num and (apb.payment not like '%before%' or apb.status > 2)";
		
		
		$apb_link_res = $sql->query($q_str);
		while($apb_link_row = $sql->fetch($apb_link_res)){
			# apb 副料
			$tmp_cost = 0;
			$tmp_cost = number_format($apb_link_row['qty']*$apb_link_row['uprice'],2,'','');
			$rtn[$k]['apb_acc'][$apb_link_row['currency']] += $tmp_cost;
			${"ttl_apb_".$apb_link_row['currency']} += $tmp_cost;
		}
		
		
		# BOM副料成本
		$q_str = "select bom_acc.id, bom_acc.qty
				  from bom_acc
				  where bom_acc.wi_id = ".$wi_row['wi_id']." and bom_acc.ap_mark <> 'stock' and bom_acc.ap_mark <> ''";
		
		$bom_acc_res = $sql->query($q_str);
		while($bom_acc_row = $sql->fetch($bom_acc_res)){
			$tmp_qty = 0;
			$tmp_qty_ary = explode(",",$bom_acc_row['qty']);
			foreach($tmp_qty_ary as $k1 => $v1){
				$tmp_qty += number_format($v1,0,'','');
			}
			
			# 單價、單位相關資料
			$q_str = "select ap_det.unit, ap_det.po_unit, ap_det.prc_unit,apb_det.uprice, apb.currency
					  from ap_det, apb_po_link, apb_det, apb
					  where ap_det.bom_id = ".$bom_acc_row['id']." and ap_det.mat_cat = 'a' and apb_po_link.po_id = ap_det.id and apb_po_link.ord_num = '".$wi_row['wi_num']."' 
							and apb_det.id = apb_po_link.rcv_id	and apb.rcv_num = apb_det.rcv_num and (apb.payment not like '%before%' or apb.status > 2)";
			$ap_det_res = $sql->query($q_str);
			if(!$ap_det_row = $sql->fetch($ap_det_res)){
				# 沒有 apb 資料時，抓ap_det的
				$q_str = "select ap_det.unit, ap_det.po_unit, ap_det.prc_unit, ap_det.prics as uprice, ap.currency
					  from ap_det, ap
					  where ap_det.bom_id = ".$bom_acc_row['id']." and ap_det.mat_cat = 'l' and ap.ap_num = ap_det.ap_num";
				$ap_det_res = $sql->query($q_str);
				$ap_det_row = $sql->fetch($ap_det_res);
			}
			$tmp_qty = change_unit_qty($ap_det_row['unit'],$ap_det_row['po_unit'],$tmp_qty);
			$tmp_cost = 0;
			$tmp_cost = number_format($tmp_qty*$ap_det_row['uprice'],2,'','');
			$rtn[$k]['bom_acc'][$ap_det_row['currency']] += $tmp_cost;
			${"ttl_bom_".$ap_det_row['currency']} += $tmp_cost;
		}
		
		
		
		
		
		
		
		
		
		$k++;
		unset($fab_bom_id_ary);
		unset($fab_acc_id_ary);
	}
	
	$op['order_det'] = $rtn;
	$op["ttl_ord_qty"] = $ttl_ord_qty;
	$op["ttl_ship_qty"] = $ttl_ship_qty;
	$op["ttl_bom_USD"] = $ttl_bom_USD;
	$op["ttl_bom_HKD"] = $ttl_bom_HKD;
	$op["ttl_bom_NTD"] = $ttl_bom_NTD;
	$op["ttl_bom_EUR"] = $ttl_bom_EUR;
	$op["ttl_bom_RMB"] = $ttl_bom_RMB;
	$op["ttl_apb_USD"] = $ttl_apb_USD;
	$op["ttl_apb_HKD"] = $ttl_apb_HKD;
	$op["ttl_apb_NTD"] = $ttl_apb_NTD;
	$op["ttl_apb_EUR"] = $ttl_apb_EUR;
	$op["ttl_apb_RMB"] = $ttl_apb_RMB;
	
	// print_r($rtn);exit;
	
	return $op;
}



function get_bom_apb_qty($fty, $year, $month1, $month2){
	$sql = $this->sql;
	
	if($fty) $where_str = " and s_order.factory = '".$fty."'";

    $q_str = "select distinct wi.wi_num, wi.id as wi_id, s_order.qty as ord_qty, s_order.id as ord_id, s_order.etd, s_order.cust , s_order.style , cust.cust_f_name  ,order_partial.p_qty ,order_partial.p_etd 
              from order_partial, wi, s_order, cust
              where (order_partial.p_etd between '$year-$month1-01' and '$year-$month2-31') and wi.wi_num = order_partial.ord_num
              and s_order.order_num = wi.wi_num and s_order.status between 4 and 12".$where_str." and s_order.cust = cust.cust_s_name  group by s_order.order_num 
              
              ";
		
	// echo $q_str."<BR>";

	$wi_res = $sql->query($q_str);
    
    
	while($wi_row = $sql->fetch($wi_res)){
        // echo $wi_row['wi_num'].' : <BR>';
		# BOM主料成本
		$q_str = "select bom_lots.id,bom_lots.o_qty,bom_lots.qty,bom_lots.color,
                  lots_use.est_1
                  from bom_lots,lots_use 
                  where 
                  bom_lots.lots_used_id = lots_use.id and 
                  bom_lots.wi_id = '".$wi_row['wi_id']."' and 
                  bom_lots.ap_mark <> 'stock' and 
                  bom_lots.ap_mark <> ''
                  group by bom_lots.id
                  ";
		// echo $q_str."<BR>"; 
		$bom_lots_res = $sql->query($q_str);
        
		while($bom_lots_row = $sql->fetch($bom_lots_res)){
        
        
            $ap_q_str = "select 
            ap_det.id as po_id,ap_det.ap_qty,ap_det.color,ap_det.unit,ap_det.po_unit,ap_det.po_qty,ap_det.ap_num,ap_det.prics,ap_det.amount,ap_det.ship_way,ap_det.po_spare,ap_det.ship_rmk,
            ap.currency,ap.po_num,ap.apb_rmk,ap.dm_way
            from ap_det,ap
            where 
            ap.ap_num = ap_det.ap_num and 
            ap_det.bom_id = '".$bom_lots_row['id']."' and 
            ap_det.wi_id = '".$wi_row['wi_id']."'  
            group by ap_det.id
            ";
            // echo $ap_q_str."<BR>"; 
            $ap_lots_res = $sql->query($ap_q_str);
            
            while($ap_lots_row = $sql->fetch($ap_lots_res)){
            
            
                $q_str = "select sum(qty) as qty from po_ship_det where po_id = '".$ap_lots_row['po_spare']."' group by po_id ";
                $ship_res = $sql->query($q_str);
                $ship_row = $sql->fetch($ship_res);
                        
                $rcv_str = "select sum(rcv_po_link.qty) as qty
                from receive,receive_det,rcv_po_link 
                where 
                receive.rcv_num = receive_det.rcv_num and
                receive.tw_rcv = '0' and
                receive.status = '4' and
                receive_det.id = rcv_po_link.rcv_id and
                rcv_po_link.po_id = '".$ap_lots_row['po_id']."' group by rcv_po_link.po_id ";
                
                if( substr($ap_lots_row['dm_way'],0,2) == 'T/T'){
                
                    $apb_str = "select sum(apb_po_link.qty) as qty,apb_po_link.currency,sum(apb_po_link.amount) as amount,sum(apb_po_link.qty*apb_po_link.rate) as amounts,apb_po_link.rate 
                    from apb,apb_det,apb_po_link 
                    where 
                    apb.rcv_num = apb_det.rcv_num and
                    apb.payment != 'before|%' and
                    apb.status = '4' and
                    apb_det.id = apb_po_link.rcv_id and
                    apb_po_link.po_id = '".$ap_lots_row['po_id']."' group by apb_po_link.po_id ";
                    
                }else{
                
                    $apb_str = "select sum(apb_po_link.qty) as qty,apb_po_link.currency,sum(apb_po_link.amount) as amount,sum(apb_po_link.qty*apb_po_link.rate) as amounts,apb_po_link.rate 
                    from apb,apb_det,apb_po_link 
                    where 
                    apb.rcv_num = apb_det.rcv_num and
                    apb.payment != 'before|%' and
                    apb.status = '2' and
                    apb_det.id = apb_po_link.rcv_id and
                    apb_po_link.po_id = '".$ap_lots_row['po_id']."' group by apb_po_link.po_id ";
                    
                }
                $rcv_res = $sql->query($rcv_str);
                $rcv_row = $sql->fetch($rcv_res);
                
                $apb_res = $sql->query($apb_str);
                $apb_row = $sql->fetch($apb_res);
                // echo $rcv_str."<BR>"; 
                // echo $apb_str."<BR>"; 
                // echo $q_str."<BR>"; 
                // echo $wi_row['wi_num'].'~'.$ap_lots_row['po_id'].'~'.$ap_lots_row['color'].'~'.
                // $ap_lots_row['ap_qty'].'~'.$ap_lots_row['po_qty'].'~'.$ap_lots_row['prics'].'~'.$ap_lots_row['amount'].'~'.$ap_lots_row['currency'].'~'.
                // $rcv_row['qty'].'~'.$rcv_row['currency'].'~'.$rcv_row['amount'].'~'.
                // $apb_row['qty'].'~'.$apb_row['currency'].'~'.$apb_row['amount'].'<BR>';
                // '#Order    #PO Color   BOM Q\'ty   PO. Q\'ty   PO. Prics   PO. Amount   SHIP. Q\'ty   RCV. Q\'ty   RCV. Amount   APB Q\'ty   APB. Amount'

                $str .= $wi_row['wi_num']."\t";
                $str .= $wi_row['p_etd']."\t";
                $str .= $wi_row['cust_f_name']."\t";
                $str .= $wi_row['style']."\t";
                $str .= $ap_lots_row['po_num']."\t";
                $str .= $ap_lots_row['color']."\t";
                $str .= $ap_lots_row['ap_qty']."\t";
                $str .= $ap_lots_row['unit']."\t";
                $str .= $ap_lots_row['po_qty']."\t";
                $str .= $ap_lots_row['po_unit']."\t";
                $str .= $ap_lots_row['ship_rmk']."\t";
                $str .= $ap_lots_row['prics']."\t";
                $str .= $ap_lots_row['amount']."\t";
                $str .= $ap_lots_row['currency']."\t";
                
                $str .= $ship_row['qty']."\t";
                
                $str .= $rcv_row['qty']."\t";
                
                $str .= $apb_row['qty']."\t";
                $str .= $apb_row['qty']*$apb_row['rate']."\t";
                $str .= $apb_row['currency']."\n".'';
                
                $mode = '';
                $mode .= $wi_row['wi_num']."\t";
                $mode .= $wi_row['p_etd']."\t";
                $mode .= $wi_row['cust_f_name']."\t";
                $mode .= $wi_row['style']."\t";
                $mode .= $ap_lots_row['po_num']."\t";
                $mode .= $ap_lots_row['color']."\t";
                $mode .= $ap_lots_row['ap_qty']."\t";
                $mode .= $ap_lots_row['unit']."\t";
                $mode .= $ap_lots_row['po_qty']."\t";
                $mode .= $ap_lots_row['po_unit']."\t";
                $mode .= $ap_lots_row['prics']."\t";
                $mode .= $ap_lots_row['amount']."\t";
                $mode .= $ap_lots_row['currency']."\t";
                $mode .= $ship_row['qty']."\t";
                $mode .= $rcv_row['qty']."\t";
                $mode .= $apb_row['qty']."\t";
                // $mode .= $apb_row['qty']*$apb_row['rate']."\t";
                $mode .= $apb_row['amounts']."\t";
                $mode .= $apb_row['currency']."\n".'';
        
                // echo $ap_lots_row['po_id'].'~'.$apb_row['amounts'].'~'.$mode."<br>"; 
            }
            
        }

        // echo "<BR>"; 
    }
    


    
	
	return $str;
}


}

?>