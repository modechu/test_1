<?php
#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->bom_search($supl,$cat)	查詢BOM的主副料
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class APB {

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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_rcvd($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_rcvd($mode=0, $dept='',$limit_entries=0) {

	$sql = $this->sql;
	$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	if($argv['PHP_po'])	{
		$po_row = $this->get_link_field("ap_num","ap","po_num like '%".$argv['PHP_po']."%'");
		$ap_str='';
		for( $i=0;$i<sizeof($po_row);$i++)
			$ap_str.="'".$po_row[$i]['ap_num']."',";
		$ap_str = substr($ap_str,0,-1);
		$q_header = "SELECT distinct receive.rcv_user, receive_det.sup_no, supl.supl_s_name as s_name , ap.dm_way
				 From receive, receive_det, supl, ap";
	}else{
		$q_header = "SELECT distinct receive.rcv_user, receive_det.sup_no, supl.supl_s_name as s_name , ap.dm_way
				 From receive left join receive_det on receive.rcv_num=receive_det.rcv_num left join supl on receive_det.sup_no=supl.vndr_no 
				 left join ap on receive_det.ap_num = ap.ap_num";
	}

	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_group_condition("`receive_det`.`sup_no`,`ap`.`dm_way`");
	$srh->add_sort_condition("`supl`.`s_name` ASC");
	$srh->row_per_page = 20;

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
	
	
	if($argv['PHP_po']){
		$srh->add_where_condition("`receive_det`.`ap_num` in(".$ap_str.")");
		$srh->add_where_condition("`ap`.`ap_num` in(".$ap_str.")");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`receive`.`ship_num` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`receive_det`.`sup_no` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}

	if ($str = $argv['PHP_payment'] )  {
		if (strlen($str) > 35)
			$srh->add_where_condition("`ap`.`dm_way` like '%|%'", "PHP_payment",$str,"search payment=[ $str ]. ");
		else
			$srh->add_where_condition("`ap`.`dm_way` like '%$str%'", "PHP_payment",$str,"search payment=[ $str ]. "); 
	}
	/* if ($str = $argv['PHP_ship'] )  { 
		$srh->add_where_condition("(`ap`.`arv_area` = '$str' OR `ap`.`finl_dist` = '$str') ", "PHP_ship",$str,"search ship :[ $str ] "); 
	}
	if ($str = $argv['PHP_ship2'] )  { 
		$srh->add_where_condition("`ap`.`ship_name` like '%$str%'", "PHP_ship2",$str,"search ship :[ $str ] "); 
	} */	
	
	$srh->add_where_condition("receive_det.apb_rmk = 0");
	$srh->add_where_condition("receive.rcv_num = receive_det.rcv_num");
	$srh->add_where_condition("supl.vndr_no = receive_det.sup_no");
	$srh->add_where_condition("`receive`.`status` = '4'");
	$srh->add_where_condition("`receive`.`tw_rcv` = '0'");
	
	// 這邊的時間是區隔開始使用 APB 的時間 ~ 正確是 2011-06-01 停掉王安日期
	//$srh->add_where_condition("`ap`.`po_apv_date` > '2011-01-01'");
	
	/* if($po['special'] == '2')
		$srh->add_where_condition("`ap_special`.`rcv_qty` > '0' ");
	else
		$srh->add_where_condition("`ap_det`.`rcv_qty` > '0' ");
	
	$srh->add_where_condition("`ap`.`rcv_rmk` = '0' "); */

	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
	if (!$result)  $op['record_NONE'] = 1; // 當查尋無資料時
// echo $srh->q_str;
	$op['rcvd'] = $result;  // 資料錄 拋入 $op
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

	return $op;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_tw_rcvd($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_tw_rcvd($mode=0, $dept='',$limit_entries=0) {
	$sql = $this->sql;
	$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	if($argv['PHP_po'])	{
		$po_row = $this->get_link_field("ap_num","ap","po_num like '%".$argv['PHP_po']."%'");
		$ap_str='';
		for( $i=0;$i<sizeof($po_row);$i++)
			$ap_str.="'".$po_row[$i]['ap_num']."',";
		$ap_str = substr($ap_str,0,-1);
		$q_header = "SELECT distinct receive.rcv_user, receive_det.sup_no, supl.supl_s_name as s_name , ap.dm_way
				 From receive, receive_det, supl, ap";
	}else{
		$q_header = "SELECT distinct receive.rcv_user, receive_det.sup_no, supl.supl_s_name as s_name , ap.dm_way
				 From receive left join receive_det on receive.rcv_num=receive_det.rcv_num left join supl on receive_det.sup_no=supl.vndr_no 
				 left join ap on receive_det.ap_num = ap.ap_num";
	}

	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_group_condition("`receive_det`.`sup_no`,`ap`.`dm_way`");
	$srh->add_sort_condition("`receive_det`.`id` DESC");
	$srh->row_per_page = 20;

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
	
	
	if($argv['PHP_po']){
		$srh->add_where_condition("`receive_det`.`ap_num` in(".$ap_str.")");
		$srh->add_where_condition("`ap`.`ap_num` in(".$ap_str.")");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`receive`.`ship_num` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`receive_det`.`sup_no` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}

	if ($str = $argv['PHP_payment'] )  {
		if (strpos($str, "%"))
			$srh->add_where_condition("`ap`.`dm_way` like '%|%'", "PHP_payment",$str,"search payment=[ $str ]. ");
		else
			$srh->add_where_condition("`ap`.`dm_way` like '%$str%'", "PHP_payment",$str,"search payment=[ $str ]. "); 
	}
	
	$srh->add_where_condition("receive_det.apb_rmk = 0");
	$srh->add_where_condition("receive.rcv_num = receive_det.rcv_num");
	$srh->add_where_condition("supl.vndr_no = receive_det.sup_no");
	$srh->add_where_condition("`receive`.`status` = '4'");
	$srh->add_where_condition("`receive`.`rcv_date` > '2011-06-01'");
	
	if($argv['PHP_tw_rcv'])
		$srh->add_where_condition("`receive`.`tw_rcv` = 1");
	else
		$srh->add_where_condition("`receive`.`tw_rcv` = 0");
	// 這邊的時間是區隔開始使用 APB 的時間 ~ 正確是 2011-06-01 停掉王安日期
	//$srh->add_where_condition("`ap`.`po_apv_date` > '2011-01-01'");
	
	/* if($po['special'] == '2')
		$srh->add_where_condition("`ap_special`.`rcv_qty` > '0' ");
	else
		$srh->add_where_condition("`ap_det`.`rcv_qty` > '0' ");
	
	$srh->add_where_condition("`ap`.`rcv_rmk` = '0' "); */

	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
	if (!$result)  $op['record_NONE'] = 1; // 當查尋無資料時
// echo $srh->q_str;
	$op['rcvd'] = $result;  // 資料錄 拋入 $op
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

	return $op;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_ord_po($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_ord_po() {

	$sql = $this->sql;
	$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
	$where_str = '';
	$i = 0;

	if ($str = strtoupper($argv['PHP_po']) )  { 
		$where_str.= " AND ap.po_num like '%".$str."'%";
	}		
	if ($str = $argv['PHP_sup'] )  { 
		$where_str.= " AND ap.sup_code = '".$str."'";
	}
	if ($str = $argv['PHP_ship'] )  { 
		$where_str.= " AND( ap.arv_area = '".$str."' || ap.finl_dist = '".$str."')";
	}
	if ($str = $argv['PHP_ship2'] )  { 
		$where_str.= " AND ap.ship_name like '%".$str."%'";
	}					
 
	$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name
							 FROM ap, supl, ap_det, bom_lots, lots_use 
							 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_det.ap_num 
							 AND ap_det.bom_id = bom_lots.id AND bom_lots.lots_used_id = lots_use.id
							 AND ap_det.mat_cat = 'l' AND ap.status = 12 AND ap.rcv_rmk = 0 ".$where_str;

	if ($str = $argv['PHP_ord'] )  { 
		$q_header.= " AND lots_use.smpl_code  like '%".$str."%'";
	}

	if (!$q_result = $sql->query($q_header)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}		
	// echo $q_header."<br>";
	while ($row1 = $sql->fetch($q_result)) {
		$op['apply'][$i]=$row1;
		$i++;
	}

	$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name
							 FROM ap, supl, ap_det, bom_acc, acc_use 
							 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_det.ap_num 
							 AND ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id
							 AND ap_det.mat_cat = 'a' AND ap.status = 12 AND ap.rcv_rmk = 0 ".$where_str;

	if ($str = $argv['PHP_ord'] )  { 
		$q_header.= " AND acc_use.smpl_code  like '%".$str."%'";
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
							 FROM ap, supl, ap_special 
							 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_special.ap_num 								 	
							 AND ap.status = 12 AND ap.rcv_rmk = 0 ".$where_str;

	if ($str = $argv['PHP_ord'] )  { 
		$q_header.= " AND ap_special.ord_num  like '%".$str."%'";
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

	if(!isset($op['apply'])) 	$op['apply'] = array();	
	return $op;
} // end func


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->creat_po_det()	組合採購明細
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function creat_po_det($ap_det)
{
		$ap_det2[0] = $ap_det[0];
		$ap_det2[0]['orders'][0] = $ap_det[0]['ord_num'];
		$ap_det2[0]['ids'] = $ap_det[0]['id'];
		$ap_det2[0]['un_qty'] = $ap_det[0]['po_qty'] - $ap_det[0]['apb_qty'] ;
		$ap_det2[0]['i'] = 0;

		$k=1;
		for ($i=1; $i<sizeof($ap_det); $i++)
		{
			$mk=0;	$order_add=0;
			$x=1;
			for ($j=0; $j< sizeof($ap_det2); $j++)
			{
				if ($ap_det2[$j]['mat_code'] == $ap_det[$i]['mat_code'] && $ap_det2[$j]['color'] == $ap_det[$i]['color'] && $ap_det2[$j]['unit'] == $ap_det[$i]['unit'] && $ap_det2[$j]['eta'] == $ap_det[$i]['eta'])
				{
					$ap_det2[$j]['ap_qty'] = $ap_det[$i]['ap_qty'] +$ap_det2[$j]['ap_qty'];
					$ap_det2[$j]['po_qty'] = $ap_det[$i]['po_qty'] +$ap_det2[$j]['po_qty'];
					$ap_det2[$j]['rcv_qty'] = $ap_det[$i]['rcv_qty'] +$ap_det2[$j]['rcv_qty'];
					$un_qty = $ap_det[$i]['po_qty'] - $ap_det[$i]['apb_qty'];
					$ap_det2[$j]['un_qty'] += $un_qty;
					for ($z =0; $z < sizeof($ap_det2[$j]['orders']); $z++)
					{
						if ($ap_det2[$j]['orders'][$z] == $ap_det[$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0)	$ap_det2[$j]['orders'][] = $ap_det[$i]['ord_num'];
					$ap_det2[$j]['ids'] = $ap_det2[$j]['ids']."|".$ap_det[$i]['id'];
					$x++;
					$mk = 1;
				}
			}
			// echo $ap_det[$i]['apb_qty'].'<br>';
			if ($mk == 0)
			{
				$ap_det2[$k] = $ap_det[$i];
				$ap_det2[$k]['orders'][0] = $ap_det[$i]['ord_num'];
				$ap_det2[$k]['ids'] = $ap_det[$i]['id'];
				$ap_det2[$k]['un_qty'] = $ap_det[$i]['po_qty'] - $ap_det[$i]['apb_qty'];
				$ap_det2[$k]['i'] = $k;
//				if ($ap_det[$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
//				$op['ap_det2'][$i]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);

				$k++;
			}
		}
		return $ap_det2;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm1) {
					
		$sql = $this->sql;

		$q_str = "INSERT INTO apb (rcv_num,rcv_date,rcv_user ,inv_num ,foreign_inv_num ,inv_date ,inv_price ,sup_no 
																	,dept,discount,off_duty,vat,currency,status,payment) VALUES('".
							$parm1['rcv_num']."','".
							$parm1['rcv_date']."','".
							$parm1['rcv_user']."','".						
							$parm1['inv_num']."','".	
							$parm1['foreign_inv_num']."','".
							$parm1['inv_date']."','".	
							$parm1['inv_price']."','".	
							$parm1['sup_no']."','".																																																
							$parm1['dept']."','".																																																
							$parm1['discount']."','".																																																
							$parm1['off_duty']."','".																																																
							$parm1['vat']."','".
							$parm1['currency']."',".
							$parm1['status'].",'".
							$parm1['payment']."')";
        // echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
		$sql = $this->sql;

			$q_str = "INSERT INTO apb_det (ship_num, rcvd_num, receive_det_id, inv_num, rcv_num, mat_code, qty, uprice, uprices, color, foc, ap_num, mat_id, size) 
				  VALUES('".
							$parm['ship_num']."','".
							$parm['rcvd_num']."','".
							$parm['receive_det_id']."','".
							$parm['inv_num']."','".
							$parm['rcv_num']."','".
							$parm['mat_code']."','".
							$parm['qty']."','".
							$parm['uprice']."','".
							$parm['uprices']."','".
							$parm['color']."','".
							$parm['foc']."','".
							$parm['ap_num']."',".
							$parm['mat_id'].",'".
							$parm['size']."')";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot append order");
				$this->msg->merge($sql->msg);
				return false;    
			}

		$this->msg->add("append apb#: [".$parm['rcv_num']."]。") ;
		$det_id = $sql->insert_id();
		return $det_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_link($parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_link($parm) {
		
		$sql = $this->sql;

		$q_str = "INSERT INTO apb_po_link (po_id, rcv_id, qty, currency, amount, rate, ord_num) 
				  VALUES(".
							$parm['po_id'].",".
							$parm['rcv_id'].",".
							$parm['qty'].",'".
							$parm['currency']."',".
							$parm['amount'].",".
							$parm['rate'].",'".
							$parm['ord_num']."')";
        // echo $q_str.'<br>';
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot append order");
				$this->msg->merge($sql->msg);
				return false;    
			}

		//$ord_id = $sql->insert_id();
		return true;

	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_apb_qty($field1, $value1,  $id, $table='ap')	
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_apb_qty($field1, $value1, $id, $table='ap') {

		$sql = $this->sql;

//加總驗收數量
		$q_str = "SELECT sum(qty) as qty	FROM apb_po_link 	WHERE  po_id='$id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$rcv_row = $sql->fetch($q_result)) {
			$rcv_row['qty'] = 0;
		}


		#####   更新資料庫內容

		$q_str = "UPDATE ".$table." SET apb_qty = '".$rcv_row['qty'].
								"' WHERE id= '".	$id ."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_link_qty($rcv_id, $po_id, $qty)
#		修改apb和po的連結檔
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_link_qty($rcv_id, $po_id, $qty) {

		$sql = $this->sql;

		#####   更新資料庫內容

		$q_str = "UPDATE apb_po_link SET qty = '".$qty.
								"' WHERE rcv_id= '".$rcv_id."' AND po_id ='".$po_id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $rcv_id;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get($num=0,$excel=0) {//第二個參數是為了判斷是不是產出excel) {
	global $receive;
	
	$sql = $this->sql;
	
	//驗收主檔
	$q_str = "
	SELECT 
	apb.*, 
	supl.country, supl.uni_no,supl.supl_s_name as s_name,supl.supl_f_name as f_name, 
	ap.special, ap.po_num, ap.currency as acurrency, ap.ap_num, ap.toler, ap.ann_num
	FROM apb, supl, ap 
	WHERE  ap.id =apb.po_id AND apb.sup_no = supl.vndr_no AND rcv_num='$num' ";
	// WHERE  ap.id =apb.po_id AND apb.sup_no = supl.vndr_no AND rcv_num='$num' and apb.status ='2'"; //add#####################mmmmmmmmmmmmmmmmmmmmm
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!2");
		return false;    
	}
	$op['rcv']=$row;
	$toler = explode('|',$op['rcv']['toler']);
	$op['rcv']['toleri'] = $toler[0];
	$op['rcv']['tolern'] = $toler[1];
	
	//改變Login帳號為名字
	$po_user=$GLOBALS['user']->get(0,$op['rcv']['rcv_user']);
	$op['rcv']['rcv_user_id'] = $op['rcv']['rcv_user'];
	if ($po_user['name'])$op['rcv']['rcv_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$op['rcv']['rcv_sub_user']);
	$op['rcv']['rcv_sub_user_id'] = $op['rcv']['rcv_sub_user'];	
	if ($po_user['name'])$op['rcv']['rcv_sub_user'] = $po_user['name'];
	
	if ($row['special'] == 2){$table = 'ap_special';}else{$table = 'ap_det';}
	$special = $row['special'];
	
//驗收明細 		
	$q_str="SELECT apb_det.*
					FROM `apb_det`
					WHERE `rcv_num` = '$num'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=$o_ii=0;
	$order_num = array();
	
	while ($row1 = $sql->fetch($q_result)) {
		$op['rcv_det'][$i]=$row1;
		$op['rcv_det'][$i]['i']=$i;
//			$op['rcv_det'][$i]['cate'] = substr($op['rcv_det'][$i]['mat_code'],0,1);
		//取得採購資訊
		//$id = explode("|",$row1['po_id']);
		$id = $this->get_po_id($row1['id']);
		$po_qty=$un_qty=$apb_qty=$rcv_qty=0;
		$tmp_ord = '';
		$op['rcv_det'][$i]['ord_num'] = array();
		$op['rcv_det'][$i]['receive_time'] = sizeof($id); //add 收料次數==========================mmmmmmmmmmmmmmmmmmmmmmmmmmmm

		
		// rcv_det
		for ($j=0; $j<sizeof($id); $j++)//取得採購資訊
		{
			# RCV_QTY 
			$rcv_det = $receive->get_revd_det($row1['po_id'],$special);
			if($rcv_det)
				$rcv_qty = $rcv_det[0]['qty'];

			#
			$apb_ar = $this->get_rcv_det($id[$j],$table);
			// print_r($apb_ar);
			// $op['po_spec'][$i]['un_qty'] = $op['po_spec'][$i]['po_qty'] - $apb_ar['qty'];
			$po_det = $this->get_po_det($id[$j],$table);

			$po_qty +=$po_det['po_qty'];
			$un_qty +=$po_det['un_qty'];
			$apb_qty +=$apb_ar['qty'];
			$mk = $od_mk =0;
			if($table == 'ap_det')
			{
				if ($po_det['mat_cat'] == 'l') //取得訂單編號
				{
					$ord_nums = $this->get_ord_num($id[$j],'bom_lots');
			//		$op['rcv_det'][$i]['ord_num'][$j] = $ord_nums['wi_num'];
				}else{
					$ord_nums = $this->get_ord_num($id[$j],'bom_acc');
			//		$op['rcv_det'][$i]['ord_num'][$j] = $ord_nums['wi_num'];
				}	
			}else{
				$ord_nums['wi_num'] = $this->get_spec_ord_num($id[$j]);
			}	
			
			for($on=0; $on<sizeof($op['rcv_det'][$i]['ord_num']); $on++)  //判斷各物料的訂單編號是否有重覆
			{
				if($op['rcv_det'][$i]['ord_num'][$on] == $ord_nums['wi_num']) $mk = 1;

			}
			
			for($od=0; $od<sizeof($order_num); $od++)  //判斷驗收單中的訂單編號是否有重覆
			{
				if($order_num[$od]['ord_num'] == $ord_nums['wi_num']) $od_mk = 1;
			}
			
			
			if($mk == 0){
				if(!$excel){//為了產出excel報表,資料會有些不同
					$op['rcv_det'][$i]['ord_num'][] = $ord_nums['wi_num'];
				}else{
					$op['rcv_det'][$i]['ord_num'][$j]=array();
					$op['rcv_det'][$i]['ord_num'][$j]['ord_num'] = $ord_nums['wi_num'];
					$op['rcv_det'][$i]['ord_num'][$j]['po_link_qty'] = $this->get_po_qty($id[$j]);
				}
			}
			if($od_mk == 0)
			{
				$order_num[$o_ii]['ord_num'] = $ord_nums['wi_num'];
				$order_num[$o_ii]['i'] = $o_ii;
				$o_ii++;
			}
			
		}
		$op['rcv_det'][$i]['mat_name']=$po_det;
		$op['rcv_det'][$i]['po']=$po_det;
		$op['rcv_det'][$i]['unit'] = $po_det['po_unit'];
		$op['rcv_det'][$i]['price'] = $po_det['prics'];
		$op['rcv_det'][$i]['po']['po_qty'] = $po_qty;
		$op['rcv_det'][$i]['po']['un_qty'] = $un_qty;
		$op['rcv_det'][$i]['po']['rcv_qty'] = $rcv_qty;
		$op['rcv_det'][$i]['po']['apb_qty'] = $apb_qty;
		// echo $po_det['prc_unit'],$po_det['po_unit'],$po_det['prics'];
		$op['rcv_det'][$i]['po']['chg_qty'] = change_unit_price($po_det['prc_unit'],$po_det['po_unit'],$po_det['prics']);
		$i++;
	}
	#rcv_det
	
	//add #########################mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm
	$q_str = "SELECT ap.*
						FROM apb, ap 
						WHERE  ap.id =apb.po_id AND apb.rcv_num='$num'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!3");
		return false;    
	}
	$op['apv_date']=$row;
	// print_r($row);
	# 應出貨日期
	
	$tb = $row['special'] == '2' ? 'ap_special' : 'ap_det';
	$q_str = "SELECT *
						FROM `".$tb."`
						WHERE  ap_num='".$row['ap_num']."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!4");
		return false;    
	}
	$op['ap_det']=$row;
	 // print_r($row);
	//=====================================================================
	
	
	$op['ord_num'] = $order_num;
	
	//驗收明細 -- LOG檔		
	$q_str="SELECT apb_log.*	FROM `apb_log`  WHERE `rcv_num` = '".$num."' ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}
	
	
	//驗收明細 -- 附加檔案
	$q_str="SELECT apb_file.*	FROM `apb_file`  WHERE `rcv_id` = '".$op['rcv']['id']."' ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		$op['done'][$i]=$row1;
		$i++;
	}	


	//收料數量add===========================================================##################3
	$q_str="SELECT receive_det.*,receive.rcv_sub_user FROM apb,receive,receive_det  WHERE apb.rcv_num='$num' and apb.po_id=receive.po_id and receive.rcv_num =receive_det.rcv_num ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		$op['receive_det'][$i]=$row1;
		$i++;
	}
	//end收料數量===============================================###########################
	$rcv_num = ($op['receive_det'][0]['rcv_num']);

	
	//收料 LOG add ===============================================###########################
	$q_str="SELECT * FROM receive_log WHERE rcv_num='$rcv_num' ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$op['receive_log']=array();
	while ($row1 = $sql->fetch($q_result)) {
		$op['receive_log'][]=$row1;
	}
	//收料 LOG end ===============================================###########################	
	
	
	//合計用資料============================================Add
		$op['sum'][0]['sum_po_qty']=0;
		$op['sum'][0]['sum_apb_qty']=0;
		$op['sum'][0]['sum_uprice']=0;
		$op['sum'][0]['sum_pay']=0;
		
	for($j=0;$j<(count($op['rcv_det']));$j++) {
		$op['sum'][0]['sum_po_qty']+=$op['rcv_det'][$j]['po']['po_qty'];
		$op['sum'][0]['sum_apb_qty']+=$op['rcv_det'][$j]['qty'];
		$op['sum'][0]['sum_uprice']+=$op['rcv_det'][$j]['uprice'];
		$op['sum'][0]['sum_pay']+=($op['rcv_det'][$j]['uprice']*$op['rcv_det'][$j]['qty']);
	}
	//end==================##################################################			
	
	//收料次數 #################################
	$k=sizeof($op['rcv_det']);
	for ($x=0;$x<$k;$x++){
		$p_id=explode("|",$op['rcv_det'][$x]['po_id']);
		$q_str="SELECT count(*) FROM apb_po_link WHERE po_id='$p_id[0]'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access! table:apb_po_link");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row1 = $sql->fetch($q_result);
		$op['rcv_det'][$x]['receive_time'] = $row1[0];
	}		
			// print_r($op);
	return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->show_apb($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function show_apb($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# 物料檔 apb_det
	$q_str = "SELECT distinct rcvd_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$rcvd_row[] = $row;
	}
	
	for($k=0;$k<sizeof($rcvd_row);$k++){
        $amt = 0;
		# 抓receive相關資料
		$q_str = "SELECT ship_num, rcv_num, rcv_date FROM receive 
			WHERE rcv_num = '".$rcvd_row[$k]['rcvd_num']."'";
		
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$op['apb_det'][$k] =  $row;
		
		# apb明細
		$q_str = "SELECT `apb_det`.* FROM `apb_det` 
			WHERE `apb_det`.`rcvd_num` = '".$rcvd_row[$k]['rcvd_num']."' and `apb_det`.`rcv_num`='".$num."'";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			`id` as `link_id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`currency`,`rate`,`amount`,`ord_num`
			FROM `apb_po_link`  
			WHERE `rcv_id` = '".$row['id']."' ";
			
			$link_res = $sql->query($q_str);
			$order = array();
            
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$row['currency'] = $rows['currency'];
                $row['rate'] = $rows['rate'];
			}
			$order = array_unique($order);
			foreach($order as $key)
			$row['order'][] = $key;
			
			
			# PO 數量 單位
			$po_eta = '';
			$po_qty = 0;
			$po_id = explode('|',$row['po_id']);
			foreach($po_id as $key){
				$special=$this->get_det_field("special","ap","ap_num='".$row['ap_num']."'");
				$special['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det';
				$q_str = "
				SELECT  ap.ap_num, ap.po_num, ap.toler, `po_qty`,`po_eta`,`unit`,`po_unit`,`prc_unit`,`prics`, ap.special
				FROM `".$tbl."` ,ap
				WHERE ".$tbl.".`id` = '".$key."' and ".$tbl.".ap_num = ap.ap_num";
				
				$q_res = $sql->query($q_str);
				while ($rows = $sql->fetch($q_res)) {
					$po_qty += $rows['po_qty'];
					$po_eta = $rows['po_eta'];
					$row['po'] = $rows;
					$toler = explode("|",$row['po']['toler']);
					$row['po']['toleri'] = $toler[0];
					$row['po']['tolern'] = $toler[1];
				}
			}
			$row['po']['po_qty'] = $po_qty;
			$row['po']['po_eta'] = $po_eta;
			
			
			# RCV 數量
			$q_str = "SELECT qty
					FROM receive_det
					WHERE `rcv_num` = '".$row['rcvd_num']."' and `po_id` = '".$row['po_id']."' and inv_num='".$row['inv_num']."'";
			
			$q_res = $sql->query($q_str);
			$rcv_qty = 0;
			while ($rows = $sql->fetch($q_res)) {
				$rcv_qty += $rows[0];
			}
			$row['rcv_qty'] = $rcv_qty;
			$row['i'] = $i;
			
			$op['apb_det'][$k]['det'][$i] = $row;
            
			#貨幣下拉選單 SHOW
			//$op['apb_det'][$k]['det'][$i]['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['apb_det'][$k]['det'][$i]['currency'],'PHP_currency'.$op['apb_det'][$k]['det'][$i]['rcvd_num'].$i,'select',"cal_amt('".$op['apb_det'][$k]['det'][$i]['rcvd_num']."')"); //幣別下拉式
			
			#抓匯率
			$TODAY = date('Y-m-d');
			//$rate=1;
			//if($op['apb_det'][$k]['det'][$i]['currency'] <> 'NTD') $rate = $GLOBALS['rate']->get_rate($op['apb_det'][$k]['det'][$i]['currency'], $TODAY);
			//$op['apb_det'][$k]['det'][$i]['rate'] = $rate;
			
			#單價轉換
			$c_unit = $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == '' ? $op['apb_det'][$k]['det'][$i]['po']['unit'] : $op['apb_det'][$k]['det'][$i]['po']['po_unit'];
			$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$c_unit,$op['apb_det'][$k]['det'][$i]['po']['prics']);
			$op['apb_det'][$k]['det'][$i]['uprices'] = $ORI_price;
			
			#小計金額 inv_price
			if($op['apb']['payment']=='T/T before shipment' or $op['apb']['payment']=='L/C at sight'){
				$amt += $op['apb_det'][$k]['det'][$i]['uprices'] *  $op['apb_det'][$k]['det'][$i]['po']['po_qty'];
				$amt = number_format($amt,2,'','');
				$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['po']['po_qty'],2,'','');
			}elseif($op['apb']['payment']=='T/T after shipment' or $op['apb']['payment']=='月結'){
				#判斷是否超過tolerance
				$toler_qty = (1 + $op['apb_det'][$k]['det'][$i]['po']['toleri'] * 0.01) * $op['apb_det'][$k]['det'][$i]['po']['po_qty'];
				$toler_qty = number_format($toler_qty,2,'','');
				
				if($op['apb_det'][$k]['det'][$i]['po']['po_unit'] == "pc" or $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == "K pcs")
					$toler_qty = ceil($toler_qty);
				
				$apbed_qty = $this->get_det_field("sum(qty) as qty","apb_det","po_id='".$op['apb_det'][$k]['det'][$i]['po_id']."' and id <> ".$op['apb_det'][$k]['det'][$i]['id']);
				$op['apb_det'][$k]['det'][$i]['toler_qty'] = number_format($toler_qty-$apbed_qty['qty'],2,'','');
				
				if($mode == 'view')
				{
					$op['apb_det'][$k]['det'][$i]['toler_qty'] = $op['apb_det'][$k]['det'][$i]['qty'];
					$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprices'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
				}
				else
				{
					if ( $op['apb_det'][$k]['det'][$i]['qty'] + $apbed_qty['qty'] <= $toler_qty ){
						$op['apb_det'][$k]['det'][$i]['toler_qty'] = $op['apb_det'][$k]['det'][$i]['qty'];
						$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprices'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
					}else{
						$op['apb_det'][$k]['det'][$i]['qty'] = $op['apb_det'][$k]['det'][$i]['toler_qty'];
						$op['apb_det'][$k]['det'][$i]['toler_qty'] = $op['apb_det'][$k]['det'][$i]['qty'];
						$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprices'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
					}
				}
				$amt += $op['apb_det'][$k]['det'][$i]['price'];
				$amt = number_format($amt,2,'','');
			}else{ #30% 70%付款方式
				$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprices'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
				$amt += $op['apb_det'][$k]['det'][$i]['price'];
				//$before_row = $this->get_det_field("sum(cost) as cost","apb_oth_cost","apb_num <> '$num' and ap_num = '".."'");
				//print_r($before_row);exit;
				//$amt = $amt - $before_row['cost'];
				$amt = number_format($amt,2,'','');
			}
            
			$i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}

	#ap_oth_cost 額外費用
	$q_str="SELECT * FROM `ap_oth_cost` 
			WHERE apb_num = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while($row1 = $sql->fetch($q_result)) {
		$op['ap_oth_cost'][]=$row1;
	}
	
	#apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_oth_cost($num," and item <> '%|after'");
// print_r($op['apb']['inv_price']);
	return $op;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->apb_after_print($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function apb_after_print($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# 物料檔 apb_det
	$q_str = "SELECT distinct rcvd_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$rcvd_row[] = $row;
	}
	
	for($k=0;$k<sizeof($rcvd_row);$k++){
        $amt = 0;
		# 抓receive相關資料
		$q_str = "SELECT ship_num, rcv_num, rcv_date FROM receive 
			WHERE rcv_num = '".$rcvd_row[$k]['rcvd_num']."'";
		
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$op['apb_det'][$k] =  $row;
		
		# apb明細
		$q_str = "SELECT `apb_det`.* FROM `apb_det` 
			WHERE `apb_det`.`rcvd_num` = '".$rcvd_row[$k]['rcvd_num']."' and `apb_det`.`rcv_num`='".$num."'";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			`id` as `link_id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`currency`,`rate`,`amount`,`ord_num`
			FROM `apb_po_link`  
			WHERE `rcv_id` = '".$row['id']."' ";
			
			$link_res = $sql->query($q_str);
			$order = array();
            
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$row['currency'] = $rows['currency'];
                $row['rate'] = $rows['rate'];
			}
			$order = array_unique($order);
			foreach($order as $key)
			$row['order'][] = $key;
			
			
			# PO 數量 單位
			$po_eta = '';
			$po_qty = 0;
			$po_id = explode('|',$row['po_id']);
			foreach($po_id as $key){
				$special=$this->get_det_field("special","ap","ap_num='".$row['ap_num']."'");
				$special['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det';
				$q_str = "
				SELECT  ap.ap_num, ap.po_num, ap.toler, `po_qty`,`po_eta`,`unit`,`po_unit`,`prc_unit`,`prics`, ap.special, ap.dm_way
				FROM `".$tbl."` ,ap
				WHERE ".$tbl.".`id` = '".$key."' and ".$tbl.".ap_num = ap.ap_num";
				
				$q_res = $sql->query($q_str);
				while ($rows = $sql->fetch($q_res)) {
					$po_qty += $rows['po_qty'];
					$po_eta = $rows['po_eta'];
					$row['po'] = $rows;
					$toler = explode("|",$row['po']['toler']);
					$row['po']['toleri'] = $toler[0];
					$row['po']['tolern'] = $toler[1];
				}
			}
			$row['po']['po_qty'] = $po_qty;
			$row['po']['po_eta'] = $po_eta;
			
			
			# RCV 數量
			$q_str = "SELECT qty
					FROM receive_det
					WHERE `rcv_num` = '".$row['rcvd_num']."' and  `po_id` = '".$row['po_id']."' ";
			
			$q_res = $sql->query($q_str);
			$rcv_qty = 0;
			while ($rows = $sql->fetch($q_res)) {
				$rcv_qty += $rows[0];
			}
			$row['rcv_qty'] = $rcv_qty;
			$row['i'] = $i;
			
			$op['apb_det'][$k]['det'][$i] = $row;
            
			
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprices'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
            
			$i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}

	#ap_oth_cost 額外費用
	$q_str="SELECT * FROM `ap_oth_cost` 
			WHERE apb_num = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while($row1 = $sql->fetch($q_result)) {
		$op['ap_oth_cost'][]=$row1;
	}
	
	#apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_oth_cost($num,"and item <> '%|after'");

	return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->show_apb3($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function show_apb3($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$num."' ";
	
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;
	
	# 取出 ap_num
	$q_str = "SELECT distinct `ap_num`
			  FROM `apb_det` 
			  WHERE rcv_num = '".$num."'";
	
	$ap_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($ap_rsl)) {
		$ap_row[] = $row;
	}
	
	for($k=0;$k<sizeof($ap_row);$k++){
		$op['po'][$k]['ap_num'] = $ap_row[$k]['ap_num'];
		$apb_oth = $this->get_after_oth_cost($num, $ap_row[$k]['ap_num']," and item like '%|after'");
		$op['po'][$k]['after_price'] = $apb_oth['cost'];
		# 取出 apb_det的細項
		$q_str = "SELECT `id`, `ap_num`, `po_id`, `mat_code` , `color` , `uprice` , `uprices` , `foc` , `qty`
				  FROM `apb_det` 
				  WHERE rcv_num = '".$num."' and ap_num = '".$ap_row[$k]['ap_num']."'";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($det_row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT `ord_num`
			FROM `apb_po_link`  
			WHERE `rcv_id` = '".$det_row['id']."'";
			
			$link_res = $sql->query($q_str);
			$order = array();
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
			}
			$order = array_unique($order);
			foreach($order as $key)
				$det_row['order'][] = $key;
			
			$det_row['amount'] = number_format($det_row['qty'] * $det_row['uprice'],2,'','');
			$op['po'][$k]['apb_det'][$i] = $det_row;
			
			# PO 數量 單位
			$po_eta = '';
			$po_qty = 0;
			$po_id = explode('|',$det_row['po_id']);
			foreach($po_id as $key){
				$special=$this->get_det_field("special","ap","ap_num='".$det_row['ap_num']."'");
				$special['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det';
				$q_str = "
				SELECT  ap.ap_num, ap.po_num, ap.dept, ap.toler, `po_qty`,`po_eta`,`unit`,`po_unit`,`prc_unit`,`amount`,`special`,`dm_way`, `po_spare`
				FROM `".$tbl."` ,ap
				WHERE ".$tbl.".`id` = '".$key."' and ".$tbl.".ap_num = ap.ap_num";
				
				$q_res = $sql->query($q_str);
				while ($rows = $sql->fetch($q_res)) {
					$po_qty += $rows['po_qty'];
					$po_eta = $rows['po_eta'];
					$op['po'][$k]['po_num'] = $rows['po_num'];
					$op['po'][$k]['dept'] = $rows['dept'];
					$op['po'][$k]['toler'] = $rows['toler'];
					$op['po'][$k]['unit'] = $rows['unit'];
					$op['po'][$k]['po_unit'] = $rows['po_unit'];
					$op['po'][$k]['prc_unit'] = $rows['prc_unit'];
					$op['po'][$k]['special'] = $rows['special'];
					$op['po'][$k]['dm_way'] = $rows['dm_way'];
					
					$toler = explode("|",$rows['toler']);
					$op['po'][$k]['toleri'] = $toler[0];
					$op['po'][$k]['tolern'] = $toler[1];
					
				}
			}
			$op['po'][$k]['apb_det'][$i]['po_qty'] = $po_qty;
			$op['po'][$k]['apb_det'][$i]['po_eta'] = $po_eta;
			#驗收數量
			$rcv_qty = $this->get_det_field("sum(qty) as qty", "receive_det", "ap_num='".$det_row['ap_num']."' and po_id='".$det_row['po_id']."' group by po_id");
			$op['po'][$k]['apb_det'][$i]['rcv_qty'] = $rcv_qty['qty'];
			
			#已付款數量
			$apbed_qty = $this->get_det_field("sum(qty) as qty", "apb,apb_det", "apb_det.ap_num='".$det_row['ap_num']."' and apb_det.po_id='".$det_row['po_id']."' and apb_det.id <> '".$det_row['id']."' and apb.rcv_num = apb_det.rcv_num and apb.payment like '%|after' group by apb_det.po_id");
			$op['po'][$k]['apb_det'][$i]['apbed_qty'] = $apbed_qty['qty'];
			
			#最大付款數
			$op['po'][$k]['apb_det'][$i]['toler_qty'] = $op['po'][$k]['apb_det'][$i]['rcv_qty'] - $op['po'][$k]['apb_det'][$i]['apbed_qty'];
			
			$op['po'][$k]['apb_det'][$i]['i'] = $i;
			$i++;
			
		}
		//$op['po'][$k]['apb_oth_cost'] = $this->get_after_oth_cost($num, $ap_row[$k]['ap_num'], "and item <> '%|after'");
		
		#取出貨前已付金額
		$apb_num = $this->get_det_field("apb_num","apb_oth_cost","ap_num='".$ap_row[$k]['ap_num']."' and item like 'before|%'");
		$before_price = $this->get_det_field("sum(cost) as cost","apb_oth_cost","apb_num='".$apb_num['apb_num']."' and ap_num='".$ap_row[$k]['ap_num']."'");
		$op['po'][$k]['before_price'] = $before_price['cost'];
		
		#取出貨後已付金額(可能會分批付款)
		$apb_num = $this->get_link_field("apb_num","apb_oth_cost","ap_num='".$ap_row[$k]['ap_num']."' and item like '%|after'");
		$op['po'][$k]['after_price'] = array();
		foreach($apb_num as $key=>$val){
			$after_price = $this->get_det_field("apb_num,sum(cost) as cost","apb_oth_cost","apb_num='".$val['apb_num']."' and ap_num='".$ap_row[$k]['ap_num']."' group by apb_num,ap_num");
			$op['po'][$k]['after_price'][] = $after_price;
		}
		
		#取出本次PO已付金額
		$after_price = $this->get_det_field("cost","apb_oth_cost","apb_num='".$num."' and item like '%|after'");
		$op['po'][$k]['po_price'] = $after_price['cost'];
	}

	//驗收明細 -- LOG檔		
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}

	#ap_oth_cost 額外費用
	$q_str="SELECT * FROM `ap_oth_cost`	WHERE apb_num = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while($row1 = $sql->fetch($q_result)) {
		$op['ap_oth_cost'][]=$row1;
	}

	return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_before($num='')	抓出指定apb記錄資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_before($rcv_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "SELECT `apb`.*,`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
			  FROM `apb`,`supl`
			  WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$rcv_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	$q_str = "SELECT distinct ap_num
			  FROM `apb_det` 
			  WHERE rcv_num = '".$rcv_num."'";
	
	$ap_rsl = $sql->query($q_str);
	$p = 0;
	while ($ap_row = $sql->fetch($ap_rsl)) {
		$po_price = 0;
		#ap
		$q_str = "SELECT ap_num, po_num, toler, dear, po_total, currency, fob, fob_area
				  FROM ap
				  WHERE ap_num = '".$ap_row['ap_num']."'";
		
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$op['ap'][$p] = $row;
		
		$toler = explode('|',$op['ap'][$p]['toler']);
		$op['ap'][$p]['toleri'] = $toler[0];
		$op['ap'][$p]['tolern'] = $toler[1];
		
		#apb_det
		$q_str = "SELECT *
				  FROM apb_det
				  WHERE rcv_num = '".$rcv_num."' and ap_num = '".$ap_row['ap_num']."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$i = 0;
		while($row = $sql->fetch($q_result)){
			$ap_det_row = $this->get_group_det($row['ap_num'],$row['mat_id'],$row['color'],$row['size']);
			$row['po_qty'] = $ap_det_row['po_qty'];
			$q_str = "select rate, ord_num
					  from apb_po_link
					  where rcv_id = ".$row['id'];
			$link_rel = $sql->query($q_str);
			$ord_ary = array();
			while($link_row = $sql->fetch($link_rel)){
				$rate = $link_row['rate'];
				$ord_ary[] = $link_row['ord_num'];
			}
			$row['orders'] = $ord_ary;
			
			$row['rate'] = $rate;
			$row['po_unit'] = $ap_det_row ['po_unit'];
			$row['prc_unit'] = $ap_det_row ['prc_unit'];
			$row['prics'] = $ap_det_row ['prics'];
			
			$c_unit = $row['prc_unit'] == ''? $row['po_unit'] : $row['prc_unit'];
			$toleri_qty = $row['po_qty'] * (1+($op['ap'][$p]['toleri']*0.01));
			if($c_unit == 'pc' or $c_unit == 'K pcs')
				$toleri_qty = ceil($toleri_qty);
			$row['toleri_qty'] = $toleri_qty;
			
			# 已請款數量 不含本次
			$row['apbed_qty'] = $this->get_apbed_qty($row['ap_num'],$row['mat_id'],$row['color'],$row['size'],"1,2"," and apb_det.rcv_num <> '".$rcv_num."'");
			if($row['apbed_qty'] == null) $row['apbed_qty'] = 0;
			# 已請款數量 含本次
			$row['ttl_apbed_qty'] = $this->get_apbed_qty($row['ap_num'],$row['mat_id'],$row['color'],$row['size'],"1,2","");
			if($row['ttl_apbed_qty'] == null) $row['ttl_apbed_qty'] = 0;
			
			$row['amount'] = number_format($row['qty'] * $row['uprice'],2,'.','');
			
			$po_price += $row['amount'];
			
			$op['ap'][$p]['ap_det'][$i] = $row;
			$op['ap'][$p]['ap_det'][$i]['i'] = $i;
			$i++;
		}
		
		# po其他費用
		$q_str="SELECT * FROM `ap_oth_cost` WHERE apb_num = '$rcv_num' and ap_num='".$op['ap'][$p]['ap_num']."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$op['ap'][$p]['ap_oth_cost'] = array();
		while ($row1 = $sql->fetch($q_result)) {			
			$op['ap'][$p]['ap_oth_cost'][]=$row1;
			$po_price += $row1['cost'];
		}
		
		# T/T before金額
		$op['ap'][$p]['pay_price'] = $this->get_po_pay_price($ap_row['ap_num'], $rcv_num);
		
		# T/T before 其他費用
		$op['ap'][$p]['apb_oth_cost'] = $this->get_oth_cost($ap_row['ap_num'], $rcv_num, 0);
		
		$op['ap'][$p]['po_price'] = $po_price;
		
		$p++;
	}

    # po 其他費用 單獨只有其他費用時顯示
    $q_str="SELECT * FROM `ap_oth_cost` WHERE apb_num =  '".$rcv_num."' ;";
    
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;
    }
    
    $ap_oth_cost = array();
    while ($row = $sql->fetch($q_result)) {
        $row['ap_num'] = str_replace("PA","PO",$row['ap_num']);
        $ap_oth_cost[] = $row;
    }
    $op['ap_oth_cost'] = $ap_oth_cost;
    
    # apb 其他費用 單獨只有其他費用時顯示
    $apb_oth_cost = array();
    $q_str="select * from apb_oth_cost where apb_num = '".$rcv_num."' ";
    $q_res = $sql->query($q_str);
    while($row = $sql->fetch($q_res)){
        $row['ap_num'] = str_replace("PA","PO",$row['ap_num']);
        $apb_oth_cost[] = $row;
    }
    $op['apb_oth_cost'] = $apb_oth_cost;

	# apb_log
	$q_str = "SELECT * FROM apb_log	WHERE rcv_num = '".$rcv_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$apb_log = array();
	while ($row = $sql->fetch($q_result)) {
		$apb_log[] = $row;
	}
	$op['rcv_log']=$apb_log;
	
	return $op;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_apb_check_list($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[] rcv_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_apb_check_list() {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "SELECT `apb`.`rcv_num`,`apb`.`rcv_user`, `apb`.`rcv_date`, `apb`.`inv_num`, `apb`.`status`, `apb`.`payment`, 
					 `supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
			  FROM `apb`,`supl`
			  WHERE (MOD(`apb`.`status`, 2)=1) and `apb`.`sup_no` = `supl`.`vndr_no`";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($rows = $sql->fetch($q_result)) {
		$op[]=$rows;
	}

	return $op;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_apb($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_apb($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# 物料檔 apb_det
	$q_str = "SELECT distinct rcvd_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$rcvd_row[] = $row;
	}
	
	for($k=0;$k<sizeof($rcvd_row);$k++){
		$amt = 0;
		# 抓receive相關資料
		$q_str = "SELECT ship_num, rcv_num, rcv_date FROM receive 
			WHERE rcv_num = '".$rcvd_row[$k]['rcvd_num']."'";
            
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$op['apb_det'][$k] =  $row;
		
		# apb明細
		$q_str = "SELECT `apb_det`.*, ap.special 
				FROM `apb_det`, `ap` 
				WHERE `apb_det`.`rcvd_num` = '".$rcvd_row[$k]['rcvd_num']."' and `apb_det`.`rcv_num`='".$num."' and apb_det.ap_num = ap.ap_num";
        $q_str = "SELECT `apb_det`.* FROM `apb_det` 
			WHERE `apb_det`.`rcvd_num` = '".$rcvd_row[$k]['rcvd_num']."' and `apb_det`.`rcv_num`='".$num."'";
            
		$q_result = $sql->query($q_str);
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			`id` as `link_id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`currency`,`rate`,`amount`,`ord_num`
			FROM `apb_po_link`  
			WHERE `rcv_id` = '".$row['id']."' ";
			
			$link_res = $sql->query($q_str);
			$order = array();
			
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$row['currency'] = $rows['currency'];
				$row['rate'] = $rows['rate'];
				//$row['price'] += $rows['amount'];
			}
			$order = array_unique($order);
			foreach($order as $key)
			$row['order'][] = $key;
			
			
			# PO 數量 單位
			$po_eta = '';
			$po_qty = 0;
			$po_id = explode('|',$row['po_id']);
			foreach($po_id as $key){
				$q_str = "
				SELECT  ap.ap_num, ap.po_num, ap.toler, `po_qty`,`po_eta`,`unit`,`po_unit`,`prc_unit`,`prics`, ap.special
				FROM `".( $row['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det' )."` ,ap
				WHERE ".$tbl.".`id` = '".$key."' and ".$tbl.".ap_num = ap.ap_num";
				
				$q_res = $sql->query($q_str);
				while ($rows = $sql->fetch($q_res)) {
					$po_qty += $rows['po_qty'];
					$po_eta = $rows['po_eta'];
					$row['po'] = $rows;
					$toler = explode("|",$row['po']['toler']);
					$row['po']['toleri'] = $toler[0];
					$row['po']['tolern'] = $toler[1];
				}
			}
			$row['po']['po_qty'] = $po_qty;
			$row['po']['po_eta'] = $po_eta;
			
			
			# RCV 數量
			$q_str = "
			SELECT `qty`
			FROM receive_det 
			WHERE `po_id` = '".$row['po_id']."' ";
			
			$q_res = $sql->query($q_str);
			$rcv_qty = '';
			while ($rows = $sql->fetch($q_res)) {
				$rcv_qty += $rows[0];
			}
			$row['rcv_qty'] = $rcv_qty;
			$row['i'] = $i;
			
			$op['apb_det'][$k]['det'][$i] = $row;
			 
			#貨幣下拉選單 GET amount
			//$op['apb_det'][$k]['det'][$i]['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['apb_det'][$k]['det'][$i]['currency'],'PHP_currency'.$op['apb_det'][$k]['det'][$i]['rcvd_num'].$i,'select',"rate('".$op['apb_det'][$k]['det'][$i]['rcvd_num']."',".$i.",".$k.")"); //幣別下拉式
			$op['apb_det'][$k]['det'][$i]['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['apb_det'][$k]['det'][$i]['currency'],'PHP_currency'.$op['apb_det'][$k]['det'][$i]['rcvd_num'].$i,'select',"cal_amt('".$op['apb_det'][$k]['det'][$i]['rcvd_num']."')"); //幣別下拉式
			
			#抓匯率
			$TODAY = date('Y-m-d');
			//$rate=1;
			//if($op['apb_det'][$k]['det'][$i]['currency'] <> 'NTD') $rate = $GLOBALS['rate']->get_rate($op['apb_det'][$k]['det'][$i]['currency'], $TODAY);
			//$op['apb_det'][$k]['det'][$i]['rate'] = $rate;
			
			#小計金額
			if($op['apb']['payment']=='T/T before shipment'){
				$amt += $op['apb_det'][$k]['det'][$i]['uprice'] *  $op['apb_det'][$k]['det'][$i]['po']['po_qty'];
				$amt = number_format($amt,2,'','');
				$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['po']['po_qty'],2,'','');
			}elseif($op['apb']['payment']=='T/T after shipment' or $op['apb']['payment']=='月結' or $op['apb']['payment']=='L/C at sight'){
				#判斷是否超過tolerance
				$toler_qty = (1 + $op['apb_det'][$k]['det'][$i]['po']['toleri'] * 0.01) * $op['apb_det'][$k]['det'][$i]['po']['po_qty'];
				if($op['apb_det'][$k]['det'][$i]['po']['po_unit'] == "pc" or $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == "K pcs")
					$toler_qty = ceil($toler_qty);
				$op['apb_det'][$k]['det'][$i]['toler_qty'] = $toler_qty;
				#單價轉換
				//$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$op['apb_det'][$k]['det'][$i]['po']['po_unit'],$op['apb_det'][$k]['det'][$i]['po']['prics']);
				//$op['apb_det'][$k]['det'][$i]['po']['ORI_price'] = $ORI_price;
				if ( $op['apb_det'][$k]['det'][$i]['qty'] <= $toler_qty ){
					$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
				}else{
					$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $toler_qty,2,'','');
				}
				$amt += $op['apb_det'][$k]['det'][$i]['price'];
				$amt = number_format($amt,2,'','');
			}else{ #30% 70%付款方式
				$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
				$amt += $op['apb_det'][$k]['det'][$i]['price'];
				$amt = number_format($amt,2,'','');
			}
			
			$i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}

	#ap_oth_cost 額外費用
	$q_str="SELECT * FROM `ap_oth_cost` 
			WHERE apb_num = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while($row1 = $sql->fetch($q_result)) {
		$op['ap_oth_cost'][]=$row1;
	}
	
	#apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_oth_cost($num);
	
	return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_month_print($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_month_print($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# 物料檔 apb_det
	$q_str = "SELECT distinct rcvd_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$rcvd_row[] = $row;
	}
	
	for($k=0;$k<sizeof($rcvd_row);$k++){
		$amt = 0;
		# 抓receive相關資料
		$q_str = "SELECT ship_num, rcv_num, rcv_date FROM receive 
			WHERE rcv_num = '".$rcvd_row[$k]['rcvd_num']."'";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$op['apb_det'][$k] =  $row;
		
		# apb明細
		$q_str = "SELECT `apb_det`.*, ap.special 
				FROM `apb_det`, `ap` 
				WHERE `apb_det`.`rcvd_num` = '".$rcvd_row[$k]['rcvd_num']."' and `apb_det`.`rcv_num`='".$num."' and apb_det.ap_num = ap.ap_num";
		$q_result = $sql->query($q_str);
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			`id` as `link_id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`currency`,`rate`,`amount`,`ord_num`
			FROM `apb_po_link`  
			WHERE `rcv_id` = '".$row['id']."' ";
			
			$link_res = $sql->query($q_str);
			$order = array();
			
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$row['currency'] = $rows['currency'];
				$row['rate'] = $rows['rate'];
				$row['price'] += $rows['amount'];
			}
			$order = array_unique($order);
			foreach($order as $key)
			$row['order'][] = $key;
			
			
			# PO 數量 單位
			$po_eta = '';
			$po_qty = 0;
			$po_id = explode('|',$row['po_id']);
			foreach($po_id as $key){
				$q_str = "
				SELECT  ap.ap_num, ap.po_num, ap.toler, `po_qty`,`po_eta`,`unit`,`po_unit`,`prc_unit`,`prics`, ap.special
				FROM `".( $row['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det' )."` ,ap
				WHERE ".$tbl.".`id` = '".$key."' and ".$tbl.".ap_num = ap.ap_num";
				
				$q_res = $sql->query($q_str);
				while ($rows = $sql->fetch($q_res)) {
					$po_qty += $rows['po_qty'];
					$po_eta = $rows['po_eta'];
					$row['po'] = $rows;
					$toler = explode("|",$row['po']['toler']);
					$row['po']['toleri'] = $toler[0];
					$row['po']['tolern'] = $toler[1];
				}
			}
			$row['po']['po_qty'] = $po_qty;
			$row['po']['po_eta'] = $po_eta;
			
			
			# RCV 數量
			$q_str = "
			SELECT `qty`
			FROM receive_det 
			WHERE `po_id` = '".$row['po_id']."' ";
			
			$q_res = $sql->query($q_str);
			$rcv_qty = '';
			while ($rows = $sql->fetch($q_res)) {
				$rcv_qty += $rows[0];
			}
			$row['rcv_qty'] = $rcv_qty;
			$row['i'] = $i;
			
			$op['apb_det'][$k]['det'][$i] = $row;
			
			#貨幣下拉選單
			//$op['apb_det'][$k]['det'][$i]['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['apb_det'][$k]['det'][$i]['currency'],'PHP_currency'.$op['apb_det'][$k]['det'][$i]['rcvd_num'].$i,'select',"rate('".$op['apb_det'][$k]['det'][$i]['rcvd_num']."',".$i.",".$k.")"); //幣別下拉式
			$op['apb_det'][$k]['det'][$i]['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['apb_det'][$k]['det'][$i]['currency'],'PHP_currency'.$op['apb_det'][$k]['det'][$i]['rcvd_num'].$i,'select',"cal_amt('".$op['apb_det'][$k]['det'][$i]['rcvd_num']."')"); //幣別下拉式
			
			#抓匯率
			$TODAY = date('Y-m-d');
			//$rate=1;
			//if($op['apb_det'][$k]['det'][$i]['currency'] <> 'NTD') $rate = $GLOBALS['rate']->get_rate($op['apb_det'][$k]['det'][$i]['currency'], $TODAY);
			//$op['apb_det'][$k]['det'][$i]['rate'] = $rate;
			
			#小計金額
			if($op['apb']['payment']=='T/T before shipment'){
				$amt += $op['apb_det'][$k]['det'][$i]['uprice'] *  $op['apb_det'][$k]['det'][$i]['po']['po_qty'];
				$amt = number_format($amt,2,'','');
				$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['po']['po_qty'],2,'','');
			}elseif($op['apb']['payment']=='T/T after shipment' or $op['apb']['payment']=='月結' or $op['apb']['payment']=='L/C at sight'){
				#判斷是否超過tolerance
				$toler_qty = (1 + $op['apb_det'][$k]['det'][$i]['po']['toleri'] * 0.01) * $op['apb_det'][$k]['det'][$i]['po']['po_qty'];
				if($op['apb_det'][$k]['det'][$i]['po']['po_unit'] == "pc" or $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == "K pcs")
					$toler_qty = ceil($toler_qty);
				$op['apb_det'][$k]['det'][$i]['toler_qty'] = $toler_qty;
				#單價轉換
				$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$op['apb_det'][$k]['det'][$i]['po']['po_unit'],$op['apb_det'][$k]['det'][$i]['po']['prics']);
				$op['apb_det'][$k]['det'][$i]['po']['ORI_price'] = $ORI_price;
				//if ( $op['apb_det'][$k]['det'][$i]['qty'] <= $toler_qty ){
				//	$op['apb_det'][$k]['det'][$i]['price'] = number_format($ORI_price * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
				//}else{
				//	$op['apb_det'][$k]['det'][$i]['price'] = number_format($ORI_price * $toler_qty,2,'','');
				//}
				$amt += $op['apb_det'][$k]['det'][$i]['price'];
				$amt = number_format($amt,2,'','');
			}else{ #30% 70%付款方式
				$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
				$amt += $op['apb_det'][$k]['det'][$i]['price'];
				$amt = number_format($amt,2,'','');
			}
			
			$i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}

	#ap_oth_cost 額外費用
	$q_str="SELECT * FROM `ap_oth_cost` 
			WHERE apb_num = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while($row1 = $sql->fetch($q_result)) {
		$op['ap_oth_cost'][]=$row1;
	}
	
	#apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_oth_cost($num);
	
	return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ap($num='') {
	$sql = $this->sql;
	
	# 採購主檔 apb_qty
	$q_str = "
	SELECT 
	`ap`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name` 
	FROM `ap`,`supl`
	WHERE `ap`.`sup_code` = `supl`.`vndr_no` AND `ap`.`ap_num` = '".$num."' ";
	

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
	$row['toleri'] = $toler[0];
	$row['tolern'] = $toler[1];
	$row['ship'] = !empty($row['finl_dist']) ? $row['finl_dist'] : $row['arv_area'] ;
	$op['ap']=$row;
	// print_r($op['ap']['special']);

	# RCV 已驗收數量 START
	$q_str = "SELECT `rcv_num` FROM `receive` WHERE `status` = '2' AND `po_id` = '".$op['ap']['id']."' ";
	 
	$q_res = $sql->query($q_str);
	$rcv_num = $rcv_qty = array();
	while ($rows = $sql->fetch($q_res)) {
		# 物料次檔 apb_det
		$q_str = "SELECT `po_id`,`qty`,`mat_code` FROM `receive_det` WHERE `receive_det`.`rcv_num` = '".$rows['rcv_num']."'";
		
		$q_result = $sql->query($q_str);
		while ($rowss = $sql->fetch($q_result)) {
			#2011/11/17	apb為國內且[po_ship] org 欄位是 CL,則rcv_qty抓 [po_ship] 的qty
			// $q_str = "select po_ship.org from po_ship,po_ship_det 
					  // where po_ship_det.po_id='".$rowss['po_id']."' and po_ship.id=po_ship_det.ship_id";
			
			// $ship_res = $sql->query($q_str);
			// $ship_rows = $sql->fetch($ship_res);
			// if ($ship_rows['org']=="CL" and is_numeric($op['ap']['uni_no'])){ //國內 且 出貨地為 CL
				// $q_str = "select sum(po_ship_det.qty) as sum_qty from po_ship,po_ship_det 
					  // where po_ship_det.po_id='".$rowss['po_id']."' and po_ship.id=po_ship_det.ship_id";
				// $ship_res = $sql->query($q_str);
				// $ship_rows = $sql->fetch($ship_res);
				// $rcv_qty[$rowss['mat_code'].$rowss['po_id']] = $ship_rows['sum_qty'];
				// $op['ap']['ship_mark'] = 1;
			// }else{
				$rcv_qty[$rowss['mat_code'].$rowss['po_id']] += $rowss['qty'];
			// }
		}
		$rcv_num[] = $rows['rcv_num'];
	}

	$i=0;
	foreach ( $rcv_num as $key ) {
		# 驗收明細 -- LOG檔		
		$q_str="SELECT `receive_log`.* FROM `receive_log` WHERE `rcv_num` = '".$key."' ";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row1 = $sql->fetch($q_result)) {
			#改變Login帳號為名字
			$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];
			$op['rcv_log'][$i]=$row1;
			$i++;
		}
	}
	
	# RCV 已驗收數量 END
	
	# APB 已驗收數量 START
	$q_str = "SELECT `rcv_num` FROM `apb` WHERE `status` = '2' AND `po_id` = '".$op['ap']['id']."' ";
	
	$q_res = $sql->query($q_str);
	$apb_qty = array();
	while ($rows = $sql->fetch($q_res)) {
		# 物料次檔 apb_det
		$q_str = "SELECT `po_id`,`qty`,`mat_code` FROM `apb_det` WHERE `apb_det`.`rcv_num` = '".$rows['rcv_num']."'";
		
		$q_result = $sql->query($q_str);
		while ($rowss = $sql->fetch($q_result)) {
			$apb_qty[$rowss['mat_code'].$rowss['po_id']] += $rowss['qty'];
		}
	}
	# APB 已驗收數量 END	
	
	# 判斷是否為特殊採購
	$table = ( $op['ap']['special'] == 2 ? 'ap_special' : 'ap_det' );
	
	# 判斷是主料還副料
	$q_str = "SELECT `mat_cat` FROM `".$table."` WHERE `ap_num` LIKE '".$num."' LIMIT 1 ";
	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	$mat_cat = ( $row['mat_cat'] == 'l' ? 'lots' : 'acc' );	
	
	# 採購主副料
	$q_str = $op['ap']['special'] == 2 ? 
	"	SELECT 
	`".$table."`.* ,
	`".$mat_cat."`.`price1` 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`comp` as `con1`' : ' , `acc`.`des` as `con1`' )."
	, `".$mat_cat."`.`specify` as `con2` 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`des` as `mile`' : ' , `acc`.`mile_code` as `mile`' )." 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`lots_name` as `mile_name`' : ' , `acc`.`mile_name` ' )." 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`width` , `lots`.`weight` , `lots`.`cons` as `finish` ' : '' )."
	"
	:
	"	SELECT 
	`".$table."`.* ,
	`".$mat_cat."_use`.`smpl_code` as `ord_num` , 
	`".$mat_cat."_use`.`".$mat_cat."_code` as `mat_code` , 
	`".$mat_cat."_use`.`".$mat_cat."_name` as `mat_name` , 
	`bom_".$mat_cat."`.`color` , 
	`".$mat_cat."`.`price1` 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`comp` as `con1`' : ' , `acc`.`des` as `con1`' )." 
	, `".$mat_cat."`.`specify` as `con2` 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`des` as `mile`' : ' , `acc`.`mile_code` as `mile`' )." 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`lots_name` as `mile_name`' : ' , `acc`.`mile_name` ' )." 
	".( $row['mat_cat'] == 'l' ? ' , `lots`.`width` , `lots`.`weight` , `lots`.`cons` as `finish` ' : '' )."
	, `bom_".$mat_cat."`.`wi_id` ";
	
	$q_str .= $op['ap']['special'] == 2 ? 
	"	FROM `".$table."`, `".$mat_cat."` "
	:
	"	FROM 
	`".$table."` , 
	`bom_".$mat_cat."` , 
	`".$mat_cat."_use` , 
	`".$mat_cat."` 
	";

	$q_str .= $op['ap']['special'] == 2 ? 
	"	WHERE `".$mat_cat."`.`".$mat_cat."_code` = `".$table."`.`mat_code` AND `".$table."`.`ap_num` = '".$num."'"
	:
	"	WHERE 
	`".$mat_cat."`.`".$mat_cat."_code` = `".$mat_cat."_use`.`".$mat_cat."_code` AND 
	`".$mat_cat."_use`.`id` = `bom_".$mat_cat."`.`".$mat_cat."_used_id` AND 
	`".$table."`.`bom_id` = `bom_".$mat_cat."`.`id` AND 
	`".$table."`.`ap_num` = '".$num."'
	";
	


	
	$rows = array();
	$q_result = $sql->query($q_str);
	while ( $row = $sql->fetch($q_result) ) {
		if( !in_array( $row['ord_num'] , $rows[$row['mat_code'].$row['color']]['order'] ) ) {
			$rows[$row['mat_code'].$row['po_spare']]['order'][] = $row['ord_num'];
			$rows[$row['mat_code'].$row['po_spare']]['mat_code'] = $row['mat_code'];
			$rows[$row['mat_code'].$row['po_spare']]['color'] = $row['color'];
			$rows[$row['mat_code'].$row['po_spare']]['po_eta'] = $row['po_eta'];
			$rows[$row['mat_code'].$row['po_spare']]['po_unit'] = $row['po_unit'];
			$rows[$row['mat_code'].$row['po_spare']]['prc_unit'] = $row['prc_unit'];
			$rows[$row['mat_code'].$row['po_spare']]['prics'] = $row['prics'];
			$rows[$row['mat_code'].$row['po_spare']]['chg_qty'] = Number_format(change_unit_price($row['prc_unit'],$row['po_unit'],$row['prics']),5);
			// $rows[$row['mat_code'].$row['po_spare']]['ids'] = $rows[$row['mat_code'].$row['color']]['ids']."|".$row['id'];
			$rows[$row['mat_code'].$row['po_spare']]['po_qty'] += $row['po_qty'];
			
			$rows[$row['mat_code'].$row['po_spare']]['mat_name'] = $row['mat_name'];
			$rows[$row['mat_code'].$row['po_spare']]['width'] = $row['width'];
			$rows[$row['mat_code'].$row['po_spare']]['weight'] = $row['weight'];
			$rows[$row['mat_code'].$row['po_spare']]['mile'] = $row['mile'];
			$rows[$row['mat_code'].$row['po_spare']]['mile_name'] = $row['mile_name'];
			$rows[$row['mat_code'].$row['po_spare']]['con1'] = $row['con1'];
			$rows[$row['mat_code'].$row['po_spare']]['con2'] = $row['con2'];
			$rows[$row['mat_code'].$row['po_spare']]['ids'] = $row['po_spare'];
		} else {
			// $rows[$row['mat_code'].$row['po_spare']]['ids'] = $rows[$row['mat_code'].$row['po_spare']]['ids']."|".$row['id'];
			$rows[$row['mat_code'].$row['po_spare']]['po_qty'] += $row['po_qty'];
			$rows[$row['mat_code'].$row['po_spare']]['ids'] = $row['po_spare'];
		}
	}
	
	
	// print_r($rows);
	// print_r($rcv_qty);
	// print_r($apb_qty);
	$mo = 0;
	foreach($rows as $key){
		$order = array_unique($key['order']);
		$ord = array();
		foreach($order as $keyS)
			$ord[] = $keyS;	
		$key['order'] = $ord;
		
		$key['rcv_qty'] = $rcv_qty[$key['mat_code'].$key['ids']];
		$key['apb_qty'] = $apb_qty[$key['mat_code'].$key['ids']];
		
		# Remain 數量
		$key['un_qty'] = $key['rcv_qty'] - $key['apb_qty'];
		
		$key['i'] = $mo++;
		$op['ap_det'][] = $key;
	}
	//print_r($op);exit;
	return $op;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_link($id,$table)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_link($id,$table) {
	$sql = $this->sql;
	
	$q_str="SELECT sum(`qty`) as qty FROM ".$table." WHERE `po_id` = '".$id."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$row1 = $sql->fetch($q_result);
	
	return $row1;
} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_det($id,$table) {

		$sql = $this->sql;

		$q_str = "SELECT  po_qty, (po_qty - apb_qty) as un_qty, apb_qty, mat_cat, 
											$table.eta, prics, po_unit, prc_unit,po_eta
							FROM $table WHERE $table.id='$id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}

		return $row;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rcv_det($id,$table) {

		$sql = $this->sql;

		$q_str = "SELECT sum(qty) as qty FROM $table , apb
							WHERE $table.po_id = '".$id."' and apb.rcv_num = $table.rcv_num and apb.status = 2  GROUP BY $table.po_id";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}

		return $row;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_id($id) {

		$sql = $this->sql;
		$row_id = array();
		$q_str = "SELECT  po_id
							FROM apb_po_link WHERE rcv_id='$id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
  		$row_id[] = $row['po_id'];
		}

		return $row_id;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_spec_ord_num($id) {

		$sql = $this->sql;

		$q_str = "SELECT ord_num
							FROM ap_special WHERE ap_special.id='$id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}

		return $row['ord_num'];
	} // end func
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

		$q_header = "SELECT distinct apb.*, supl.country, supl.supl_s_name as s_name
								 FROM apb LEFT JOIN supl ON supl.vndr_no = apb.sup_no left join apb_det on apb.rcv_num = apb_det.rcv_num";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_group_condition("apb.id DESC");
		$srh->row_per_page = 15;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if (isset($argv['PHP_sr_startno']) && $argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
		}else{
			$pages = $srh->get_page(1,$pagesize);
		} 
		##--*****--2006.11.16頁碼新增 end	   ##
	}


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

//部門 : 業務部門
	if ($user_dept=='LY' || $user_dept =='HJ')	
		$srh->add_where_condition("apb.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");

   if ($mode==1){
		if ($str = strtoupper($argv['SCH_num']) )  { 
			$srh->add_where_condition("apb.rcv_num LIKE '%$str%'", "SCH_num",$str,"search apb #:[ $str ] "); 
			}
		
		if ($str = $argv['SCH_supl'] )  { 
			$srh->add_where_condition("apb.sup_no = '$str'", "SCH_supl",$str,"search supplier=[ $str ]. "); 
			}
		if ($str = strtoupper($argv['SCH_po']) )  { 
			$str = str_replace("O","A",$str);
			$srh->add_where_condition("apb_det.ap_num like '%$str%'", "SCH_po",$str,"search P/O# :[ $str ] "); 
			}
		if ($str = $argv['SCH_ship'] )  { 
			$srh->add_where_condition("apb_det.ship_num like '%$str%'", "SCH_ship",$str,"search Ship num# :[ $str ] "); 
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
		$op['rcvd'] = $result;  // 資料錄 拋入 $op
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
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm1) {
					
		$sql = $this->sql;

					# 修改資料庫
		$q_str = "UPDATE apb SET
								inv_num   = '". $parm1['inv_num']."',
								foreign_inv_num   = '". $parm1['foreign_inv_num']."',
								inv_date   = '". $parm1['inv_date']."',
								inv_price   = '". $parm1['inv_price']."',
								discount   = '". $parm1['discount']."',
								off_duty   = '". $parm1['off_duty']."',
								vat  	 = '". $parm1['vat']."',
								currency   = '". $parm1['currency']."'
							WHERE rcv_num = '".$parm1['rcv_num']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return 1;

	} // end func

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_det_field($field,$table,$where)	抓出指定表格的指定資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_det_field($field,$table,$where) {
		$sql = $this->sql;
		
		$q_str="SELECT ". $field. " FROM ".$table." WHERE ".$where;
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		return $row1;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pdate_field_id($field1, $value1, $id, $table='apb')
#		更新 field的值 (以ID)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_id($field1, $value1, $id, $table='apb') {

		$sql = $this->sql;

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE id= '".	$id ."'";
        
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pdate_field($field, $value, $where_str, $table='apb')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($field, $value, $where_str, $table='apb') {

		$sql = $this->sql;

		$q_str = "UPDATE ".$table." SET ".$field." ='".$value.
								"' WHERE ".$where_str;
        // echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_det($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_det($id) {
	$sql = $this->sql;

	$q_str = "DELETE FROM apb_det WHERE id= '".	$id ."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return $id;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_cost($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_cost($id) {
	$sql = $this->sql;

	$q_str = "DELETE FROM apb_oth_cost WHERE id= '".	$id ."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return $id;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_apb_cost($where_str, $table)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_apb_cost($where_str, $table) {
	$sql = $this->sql;

	$q_str = "DELETE FROM ".$table." WHERE ".$where_str;

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return true;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_ap_cost($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_ap_cost($id) {
	$sql = $this->sql;

	$q_str = "update ap_oth_cost set apb_num = '' WHERE id= '".$id."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return $id;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_link($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_link($id) {

		$sql = $this->sql;

		$q_str = "DELETE FROM apb_po_link WHERE rcv_id= '".	$id ."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return $id;
	} // end func		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_del_full($num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_del_full($num) {
	$sql = $this->sql;

	$q_str = "SELECT id FROM apb_det WHERE rcv_num = '".$num."'";
	$q_result = $sql->query($q_str);
	if(!$row = $sql->fetch($q_result)) 
	{
		$q_str = "DELETE FROM apb WHERE rcv_num = '".$num."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$q_str = "DELETE FROM apb_oth_cost WHERE apb_num = '".$num."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$q_str = "update ap_oth_cost set apb_num = '' where apb_num = '".$num."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$q_str = "DELETE FROM apb_log WHERE rcv_num = '".$num."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}			
		return 1;
	}
	return 0;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_det_rmk($ap_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_det_rmk($ap_num) {
	$sql = $this->sql;
	
	$q_str = "SELECT distinct apb_rmk FROM ap_det WHERE ap_num = '".$ap_num."'";
	$q_result = $sql->query($q_str);
	$rmk = array();
	while($row = $sql->fetch($q_result)){
		$rmk[] = $row;
	}
	
	if(sizeof($rmk)==1 and $rmk[0]['apb_rmk']==1){
		$q_str = "update ap set apb_rmk = 1 where ap_num='".$ap_num."'";
	}else{
		$q_str = "update ap set apb_rmk = 0 where ap_num='".$ap_num."'";
	}
	
	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
	}
				
	return true;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_del_full2($num, $where_str)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_del_full2($field, $value, $table, $where_str) {
	$sql = $this->sql;

	$q_str = "SELECT id FROM ".$table." WHERE ".$field." = '".$value."' ".$where_str;
	
	$q_result = $sql->query($q_str);
	if(!$row = $sql->fetch($q_result)) 
	{
		$q_str = "DELETE FROM apb WHERE rcv_num = '".$num."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$q_str = "DELETE FROM apb_log WHERE rcv_num = '".$num."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}			
		return 1;
	}
	return 0;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function po_close($pa_num,$special) {

		$sql = $this->sql;
		//$ap_det = array();	
//ad_det的採購項目close	==> "rcv_rmk = 1"
		$q_str = "UPDATE ap_det SET rcv_rmk ='1' WHERE ap_num= '".$pa_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
//ad_special的採購項目close		==> "rcv_rmk = 1"	
		$q_str = "UPDATE ap_special SET rcv_rmk ='1' WHERE ap_num= '".$pa_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}			
//ap的採購單Close		==> "rcv_rmk = 1"
		$q_str = "UPDATE ap SET rcv_rmk ='1' WHERE ap_num= '".$pa_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$mk = 0;
		if ($special < 2)
		{
			$q_str = "SELECT mat_cat FROM ap_det WHERE ap_det.ap_num ='".$pa_num."'";
			$q_result = $sql->query($q_str);
			$po_mat = $sql->fetch($q_result);
			if ($po_mat['mat_cat'] == 'a')
			{
				$table = "bom_acc";
				$field = "m_acc_shp";	
				$mat_cat = 'a';
			}else{
				$table = "bom_lots";
				$field = "mat_shp";	
				$mat_cat = 'l';	
			}			
//找採購的訂單
			$q_str = "SELECT wi.wi_num FROM wi, ".$table.", ap_det WHERE ap_det.bom_id=".$table.".id 
								AND wi.id = ".$table.".wi_id AND ap_det.mat_cat = '".$mat_cat."' AND ap_det.ap_num ='".$pa_num."'";

			$q_result = $sql->query($q_str);
			while ($row1 = $sql->fetch($q_result)) $ord[]=$row1['wi_num'];
		}else{
//找採購的訂單 從特殊採購項目找
			$q_str = "SELECT ord_num, mat_cat FROM ap_special WHERE ap_num ='".$pa_num."'";

			$q_result = $sql->query($q_str);
			while ($row1 = $sql->fetch($q_result)) 
			{
				$ord[]=$row1['ord_num'];
				if ($row1['mat_cat'] == 'a')
				{
					$table = "bom_acc";
					$field = "m_acc_shp";	
					$mat_cat = 'a';
				}else{
					$table = "bom_lots";
					$field = "mat_shp";	
					$mat_cat = 'l';	
				}	// end if
			} // end while
		} // end if ($special < 2)
//		$f1 = $this->add_ord_rcvd($table,$field,$mat_cat,$ord);
		return 1;
	} // end func

/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_rcvd($table,$field,$mat_cat,$ord) {

		$sql = $this->sql;
		$mk = 0;

		for ($i=0; $i<sizeof($ord); $i++)
		{
			$q_str = "SELECT rcv_rmk, apb_qty FROM ap_det,". $table.", wi 
								WHERE ap_det.bom_id = ".$table.".id AND ".$table.".wi_id = wi.id 
								AND ap_det.mat_cat ='".$mat_cat."' AND wi.wi_num ='".$ord[$i]."'";

			$q_result = $sql->query($q_str);		
			while ($row = $sql->fetch($q_result)) {	
		//		if ($row['rcv_rmk']==0) $mk = 1;	
				if ($row['apb_qty']== 0) $mk = 1;
			}

			$q_str = "SELECT rcv_rmk, apb_qty FROM ap_special WHERE ord_num ='".$ord[$i]."' AND mat_cat ='".$mat_cat."'";

			$q_result = $sql->query($q_str);		
			while ($row = $sql->fetch($q_result)) {	
		//		if ($row['rcv_rmk']==0) $mk = 1;	
				if ($row['apb_qty']== 0) $mk = 1;
			}
			
			if ($mk == 0)
			{
				$q_str = "UPDATE pdtion SET ".$field." = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";

				$q_result = $sql->query($q_str);		
			}
			$mk = 0;
			
		}

		return 1;
	} // end func
*/	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_rcvd($table,$field,$mat_cat,$ord,$acc_cat=0,$submit=1)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_rcvd($table,$field,$mat_cat,$ord,$acc_cat=0,$submit=1) {


		$sql = $this->sql;
		$mk = 0;

		for ($i=0; $i<sizeof($ord); $i++)
		{
			
			if( $mat_cat == 'l' )
			{
				$ship_det = array();
//判斷是否存在BOM
				$q_str = "SELECT bom_lots.id  FROM bom_lots, lots_use
									WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND lots_use.support = 0
												AND lots_use.smpl_code  ='".$ord[$i]."' ";

				$q_result = $sql->query($q_str);
				if (!$row = $sql->fetch($q_result)) {	
					$mk = 1;					
				}	

//判斷BOM是否都有採購
				$q_str = "SELECT ap_mark  FROM bom_lots, lots_use
									WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND lots_use.support = 0
												AND ap_mark = '' AND lots_use.smpl_code  ='".$ord[$i]."' ";

				$q_result = $sql->query($q_str);
				if ($row = $sql->fetch($q_result)) {	
					$mk = 1;					
				}				

				if($mk == 0)
				{
					//取得WI ID
					$q_str = "SELECT bom_lots.wi_id  FROM bom_lots, lots_use
										WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND lots_use.support = 0
													AND lots_use.smpl_code  ='".$ord[$i]."' ";
					$q_result = $sql->query($q_str);
					$row = $sql->fetch($q_result);
					$wi_id = $row['wi_id'];
							

					$q_str = "SELECT ap_det.apb_qty 
										FROM ap_det, bom_lots, ap
										WHERE ap_det.bom_id = bom_lots.id  AND ap.ap_num = ap_det.ap_num
										AND bom_lots.dis_ver = 0														
										AND ap.status >= 0	AND ap_det.mat_cat ='l'	
										AND ap_det.apb_qty = 0 AND bom_lots.wi_id  ='".$wi_id."'";

					$q_result = $sql->query($q_str);		
				
					if ($row = $sql->fetch($q_result)) {	
						$mk = 1;					
					}	
					
				}
				if($mk == 0)	//當submit時需確認是物皆submit
				{
					$q_str = "SELECT apb.status, apb.rcv_num 
										FROM  apb, apb_det, apb_po_link, ap_det, bom_lots, ap
										WHERE ap_det.bom_id = bom_lots.id 
										AND apb_po_link.po_id = ap_det.id AND apb_po_link.rcv_id = apb_det.id
										AND apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id
										AND ap_det.ap_num = ap.ap_num AND apb.status  = 0
										AND ap_det.mat_cat ='l' AND bom_lots.wi_id ='".$wi_id."'";

					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) {	
						if(isset($row['status']))$mk = 1;
									
					}
					
				}
				

				$q_str = "SELECT ap.id 
									FROM ap_special, ap
									WHERE ap_special.ap_num = ap.ap_num AND ap_special.apb_qty = 0 AND ap.status > 0
												AND ord_num ='".$ord[$i]."' AND mat_cat ='l'";

				$q_result = $sql->query($q_str);		
				if ($row = $sql->fetch($q_result)) {	
					$mk = 1;					
				}	
				
				if($mk == 0)	//當submit時需確認是物皆submit
				{
					$q_str = "SELECT apb.status FROM apb, apb_det, apb_po_link, ap_special, ap
										WHERE apb_po_link.po_id = ap_special.id AND apb_po_link.rcv_id = apb_det.id
										AND apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id
										AND ap_special.ap_num = ap.ap_num AND apb.status = 0
										AND ap_special.mat_cat ='l' AND ap_special.ord_num ='".$ord[$i]."'";

					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) {	
						$mk = 1;					
					}	
				
				}
		
				if ($mk == 0)		//當物料皆驗收完成填入驗收日
				{
					//記錄驗收日期
					// $q_str = "UPDATE pdtion SET mat_shp = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";

					// $q_result = $sql->query($q_str);		
					
					
				
				
				}
				
				
				
				
				
/*
				$ship_date = $GLOBALS['order']->get_field_value('mat_rcv', '', $ord[$i],'pdtion');
				$ship_way = $GLOBALS['order']->get_field_value('mat_ship_way', '', $ord[$i],'pdtion');
				for($s=0; $s<sizeof($ship_det); $s++)//若ship物料同驗收物料時,消除ship資料
				{
					if($ship_date == $ship_det[$s]['ship_date'] && $ship_way == $ship_det[$s]['ship_way'])
					{
						$q_str = "UPDATE pdtion SET mat_rcv = NULL, mat_ship_way = '' WHERE order_num = '".$ord[$i]."'";
						$q_result = $sql->query($q_str);	
						break;						
					}
				}
*/				
				$mk = 0;
			}else{
//主要副料
			if ( $acc_cat == 2 || $acc_cat == 3)
			{							
				$ship_det = array();
				$mk2=1;
//判斷是否有BOM
				$q_str = "SELECT bom_acc.id FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '1' AND acc_use.support = 0	
									AND acc_use.smpl_code ='".$ord[$i]."'";			

				$q_result = $sql->query($q_str);		
				if (!$row = $sql->fetch($q_result)) $mk = 1;	

//判斷BOM是否購買物料
				$q_str = "SELECT ap_mark FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '1' AND acc_use.support = 0	AND ap_mark = ''
									AND acc_use.smpl_code ='".$ord[$i]."'";			

				$q_result = $sql->query($q_str);		
				if ($row = $sql->fetch($q_result)) $mk = 1;	
				if($mk == 0)
				{
					$q_str = "SELECT bom_acc.wi_id as id FROM bom_acc, acc_use
										WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
										AND acc_use.acc_cat = '1' AND acc_use.support = 0	
										AND acc_use.smpl_code ='".$ord[$i]."' LIMIT 1";						
					$q_result = $sql->query($q_str);		
					if (!$wi = $sql->fetch($q_result)) $mk = 1;	

				}
				if($mk == 0)
				{
					$q_str = "SELECT ap_det.apb_qty 
										FROM ap_det, bom_acc, ap, acc_use 
										WHERE ap_det.bom_id = bom_acc.id AND ap.ap_num = ap_det.ap_num 
										AND bom_acc.acc_used_id = acc_use.id  AND acc_use.acc_cat = '1' 
										AND acc_use.support = 0 AND bom_acc.dis_ver = 0 										
										AND ap.status >= 0 AND ap_det.apb_qty = 0
										AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";

					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) $mk = 1;	
				}
					
					//if($mk2 == 1)$mk = $mk2;
					
					if($mk == 0)	//當submit時需確認是物皆submit
					{
						$q_str = "SELECT apb.status 
											FROM   apb, apb_det, apb_po_link, ap_det, bom_acc, ap, acc_use
											WHERE ap_det.bom_id = bom_acc.id 											
											AND apb_po_link.po_id = ap_det.id AND apb_po_link.rcv_id = apb_det.id																						
											AND apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id
											AND ap_det.ap_num = ap.ap_num AND bom_acc.acc_used_id = acc_use.id 
											AND acc_use.acc_cat = '1' AND acc_use.support = 0
											AND apb.status = 0
											AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";

						$q_result = $sql->query($q_str);		
						if ($row = $sql->fetch($q_result)) $mk = 1;
					}
						
					if ($mk == 0)
					{
						// $q_str = "UPDATE pdtion SET m_acc_shp = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";

						// $q_result = $sql->query($q_str);		
					}
/*					
					$ship_date = $GLOBALS['order']->get_field_value('m_acc_rcv', '', $ord[$i],'pdtion');
					$ship_way = $GLOBALS['order']->get_field_value('m_acc_ship_way', '', $ord[$i],'pdtion');
					for($s=0; $s<sizeof($ship_det); $s++)
					{
						if($ship_date == $ship_det[$s]['ship_date'] && $ship_way == $ship_det[$s]['ship_way'])
						{
							$q_str = "UPDATE pdtion SET m_acc_rcv = NULL, m_acc_ship_way = '' WHERE order_num = '".$ord[$i]."'";
							$q_result = $sql->query($q_str);							
							break;
						}
					}					
*/					
					$mk = 0;	
					
				}
//其他副料		
				if ( $acc_cat == 1 || $acc_cat == 3)
				{
					
				$ship_det = array();
				$mk2=1;
//判斷BOM是否購買物料
				$q_str = "SELECT bom_acc.ap_mark FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '0' AND acc_use.support = 0
									AND acc_use.smpl_code ='".$ord[$i]."'";			

				$q_result = $sql->query($q_str);		
				if (!$row = $sql->fetch($q_result)) $mk = 1;	

//判斷BOM是否購買物料
				$q_str = "SELECT ap_mark FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '0' AND acc_use.support = 0	AND ap_mark = ''
									AND acc_use.smpl_code ='".$ord[$i]."'";			

				$q_result = $sql->query($q_str);		
				if ($row = $sql->fetch($q_result)) $mk = 1;	
				if($mk == 0)
				{
					$q_str = "SELECT bom_acc.wi_id as id FROM bom_acc, acc_use
										WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
										AND acc_use.acc_cat = '0' AND acc_use.support = 0	
										AND acc_use.smpl_code ='".$ord[$i]."' LIMIT 1";						

					$q_result = $sql->query($q_str);		
					if (!$wi = $sql->fetch($q_result)) $mk = 1;	

				}
				if($mk == 0)
				{
					$q_str = "SELECT ap_det.apb_qty 
										FROM ap_det, bom_acc, ap, acc_use
										WHERE ap_det.bom_id = bom_acc.id AND ap.ap_num = ap_det.ap_num 
										AND bom_acc.acc_used_id = acc_use.id
										AND acc_use.acc_cat = '0' AND acc_use.support = 0
										AND bom_acc.dis_ver = 0 										
										AND ap.status >= 0 AND ap_det.apb_qty = 0
										AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";

					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) $mk = 1;	
				}
					
					//if($mk2 == 1)$mk = $mk2;
					
					if($mk == 0)	//當submit時需確認是物皆submit
					{
						$q_str = "SELECT apb.status 
											FROM   apb, apb_det, apb_po_link, ap_det, bom_acc, ap, acc_use
											WHERE ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id
											AND apb_po_link.po_id = ap_det.id AND apb_po_link.rcv_id = apb_det.id																						
											AND apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id
											AND ap_det.ap_num = ap.ap_num 
											AND acc_use.acc_cat = '0' AND acc_use.support = 0
											AND apb.status = 0
											AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";

						$q_result = $sql->query($q_str);		
						if ($row = $sql->fetch($q_result)) $mk = 1;
					}

					$q_str = "SELECT  apb_qty
										FROM ap_special, ap 
										WHERE ap.ap_num = ap_special.ap_num AND ord_num ='".$ord[$i]."'AND 
													apb_qty = 0 AND mat_cat ='a' AND ap.status > 0";

					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) $mk = 1;
					
					if($mk == 0)	//當submit時需確認是物皆submit
					{			
						$q_str = "SELECT apb.status 
											FROM  apb, apb_det, apb_po_link, ap_special, ap
											WHERE apb_po_link.po_id = ap_special.id AND apb_po_link.rcv_id = apb_det.id
											AND apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id
											AND ap_special.ap_num = ap.ap_num AND apb.status = 0
											AND ap_special.mat_cat ='a' AND ap_special.ord_num ='".$ord[$i]."'";

						$q_result = $sql->query($q_str);		
						if ($row = $sql->fetch($q_result)) $mk = 1;
					}
					if ($mk == 0)
					{
						// $q_str = "UPDATE pdtion SET acc_shp = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";

						// $q_result = $sql->query($q_str);		
					}
/*
					$ship_date = $GLOBALS['order']->get_field_value('acc_rcv', '', $ord[$i],'pdtion');
					$ship_way = $GLOBALS['order']->get_field_value('acc_ship_way', '', $ord[$i],'pdtion');
					for($s=0; $s<sizeof($ship_det); $s++)
					{
						if($ship_date == $ship_det[$s]['ship_date'] && $ship_way == $ship_det[$s]['ship_way'])
						{
							$q_str = "UPDATE pdtion SET acc_rcv = NULL, acc_ship_way = '' WHERE order_num = '".$ord[$i]."'";
							$q_result = $sql->query($q_str);							
							break;
						}
					}	//end for ($s=0; $s<sizeof($ship_det); $s++)
*/							
				}// end if ( $acc_cat == 1 || $acc_cat == 3)

			}




			$mk = 0;	
		}

		return 1;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_po_rcvd($rcv_num) {

		$sql = $this->sql;
		//$ap_det = array();	
	
		$q_str="SELECT apb_po_link.po_id, ap.special
						FROM apb_det, apb_po_link, apb, ap
						WHERE apb_det.id = apb_po_link.rcv_id AND apb_det.rcv_num = apb.rcv_num AND
						      apb.po_id = ap.id AND apb_det.rcv_num ='$rcv_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {	
			$rcv[]=$row1;
		}
		for ($i =0; $i< sizeof($rcv); $i++)
		{
			if ($rcv[$i]['special'] =='0'){$table = 'ap_det';}else{$table='ap_special';}
			
			$q_str="SELECT ap_num,po_qty, apb_qty	FROM $table WHERE id = ".$rcv[$i]['po_id'];
			$q_result = $sql->query($q_str);		
			$row = $sql->fetch($q_result);			
			if ($row['po_qty'] <= $row['apb_qty'])
			{
				$q_str = "UPDATE $table SET rcv_rmk ='1' WHERE id= '".$rcv[$i]['po_id']."'";

				$q_result = $sql->query($q_str);
			}		
		//判斷整張採購單是否驗收完了	
		$q_str ="SELECT id FROM $table WHERE rcv_rmk ='0' AND ap_num ='".$row['ap_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row2 = $sql->fetch($q_result)) {
			$q_str = "UPDATE ap SET rcv_rmk ='1' WHERE ap_num= '".$row['ap_num']."'";
			$q_result = $sql->query($q_str);
		}
		
	}	
		return 1;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_ord_rcvd($rcv_num,$ord_num,$mat_cat) {

		$sql = $this->sql;
		//$ap_det = array();	
		$acc_cat = 0;
/*
		$q_str="SELECT apb_po_link.po_id, apb_det.mat_code, ap.special  
						FROM apb_det, apb_po_link, apb, ap 	
						WHERE apb_det.id = apb_po_link.rcv_id AND apb_det.rcv_num = apb.rcv_num AND 
						      apb.po_id = ap.id AND apb_det.rcv_num ='$rcv_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {	
			$rcv[]=$row1;
			
		}

		if (substr($rcv[0]['mat_code'],0,1) == 'A')
		{
			$table = "bom_acc";
			$field = "m_acc_shp";	
			$mat_cat = 'a';
			$tmp1=$tmp2=0;
			
			if ($rcv[0]['special'] < '2' )
			{
				$q_str="SELECT acc_use.acc_cat  
								FROM apb_det, apb_po_link, apb, ap_det, ap, bom_acc, acc_use
								WHERE apb_det.id = apb_po_link.rcv_id AND apb_det.rcv_num = apb.rcv_num AND 											
						    		  apb.po_id = ap.id AND apb_po_link.po_id = ap_det.id AND 
						    		  ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id AND
						    		  apb_det.rcv_num ='$rcv_num'";
				if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error ! Database can't access!");
					$this->msg->merge($sql->msg);
					return false;    
				}		
				while ($row1 = $sql->fetch($q_result)) {	
					if($row1['acc_cat'] == '1') $tmp1 = 2;
					if($row1['acc_cat'] == '0') $tmp2 = 1;			
				}				
			}
			$acc_cat = $tmp1+$tmp2;
		}else{
			$table = "bom_lots";
			$field = "mat_shp";			
			$mat_cat = 'l';
			
			
		}

		$ord = array(); $o = 0; $mk =0;
		for ($i =0; $i< sizeof($rcv); $i++)
		{
//			if ($rcv[$i]['po_cat'] =='0'){$table = 'ap_det';}else{$table='ap_special';}

				if ($rcv[$i]['special'] < '2' )
				{
					$q_str = "SELECT wi_num as ord_num FROM ap_det,". $table.", wi 
										WHERE ap_det.bom_id = ".$table.".id AND ".$table.".wi_id = wi.id 
											AND ap_det.mat_cat ='".$mat_cat."' AND ap_det.id ='".$rcv[$i]['po_id']."'";

					$q_result = $sql->query($q_str);		
					$row = $sql->fetch($q_result);
				}else{
					$q_str = "SELECT ord_num FROM ap_special WHERE ap_special.id = '".$rcv[$i]['po_id']."'";
					$q_result = $sql->query($q_str);		
					$row = $sql->fetch($q_result);
				}
				for ($k=0; $k<sizeof($ord); $k++)
				{
					if ($ord[$k] == $row['ord_num']) $mk = 1;
				}
				if ($mk == 0) $ord[] = $row['ord_num'];
				$mk = 0;
					
		}
*/		
		$acc_cat = 3;
		$f1 = $this->add_ord_rcvd('','',$mat_cat,$ord_num,$acc_cat);

		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->change_currency($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_currency($rcv_num) {

		$sql = $this->sql;
	
		$q_str="SELECT po_id, ap_num FROM apb_det WHERE rcv_num ='$rcv_num'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($det_row = $sql->fetch($q_result)) {	
			$special = $this->get_det_field("special","ap","ap_num='".$det_row['ap_num']."'");
			$special['special'] == 2 ? $table='ap_special' : $table='ap_det';
			
			$q_str="SELECT apb_po_link.*,".$table.".prics as price, po_unit, prc_unit
					FROM apb_det, apb_po_link, ".$table."
					WHERE apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ".$table.".id AND apb_det.rcv_num ='$rcv_num'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while ($row1 = $sql->fetch($q_result)) {	
				$rcv[]=$row1;			
			}
		}
		

		$TODAY = date('Y-m-d');
		for ($i =0; $i< sizeof($rcv); $i++)
		{
			$rate = 0;
			if($rcv[$i]['prc_unit'] <> '') $rcv[$i]['price'] = change_unit_price($rcv[$i]['prc_unit'],$rcv[$i]['po_unit'],$rcv[$i]['price']);

			$tmp_price = $GLOBALS['rate']->change_rate($rcv[$i]['currency'],$TODAY,$rcv[$i]['price']);
			
			if($rcv[$i]['currency'] <> 'NTD') $rate = $GLOBALS['rate']->get_rate($rcv[$i]['currency'], $TODAY);
			if($rate > 0) $rcv[$i]['price'] = $rcv[$i]['price'] * $rate;
			
			$amount = $rcv[$i]['price'] * $rcv[$i]['qty'];
			$amount = number_format($amount,2,'.','');
			$this->update_field_id('apb_po_link.amount', $amount, $rcv[$i]['id'], 'apb_po_link');
			$this->update_field_id('apb_po_link.currency',$rcv[$i]['currency'], $rcv[$i]['id'], 'apb_po_link');
			$this->update_field_id('apb_po_link.rate',$rate, $rcv[$i]['id'], 'apb_po_link');
		}	

		return 1;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_log($parm) {
	$sql = $this->sql;
	$parm['des']=str_replace("'", "\'",$parm['des']);
			$q_str = "INSERT INTO apb_log (rcv_num,user,item,des,k_date) 
				  VALUES('".
							$parm['rcv_num']."','".
							$parm['user']."','".
							$parm['item']."','".
							$parm['des']."','".																													
							$parm['k_date']."')";
							
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't insert.");
			$this->msg->merge($sql->msg);
			return false;    
		}   			
		
	return true;
	}// end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_revd_det($id,$table) {

		$sql = $this->sql;
		$where_str='';
		if ($table == 2) $where_str = " AND po.special = 2";
//驗收主檔
		$q_str = "SELECT apb_det.*, apb.inv_num, apb.rcv_date										 
							FROM apb, ap, apb_det
							WHERE  ap.id =apb.po_id AND apb.rcv_num = apb_det.rcv_num AND apb_det.po_id='$id' ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$rcv_det[$i]=$row1;
			$i++;
		}
		return $rcv_det;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_apb_det($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_apb_det($po_id,$ap_num) {

		$sql = $this->sql;
		
		$q_str = "SELECT apb_det.*, apb.rcv_date, apb.rcv_sub_user
							FROM apb, apb_det
							WHERE apb.rcv_num = apb_det.rcv_num and apb_det.po_id='$po_id' and ap_num='$ap_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$rcv_det[$i]=$row1;
			$i++;
		}
		return $rcv_det;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_rpt_used($parm)	搜尋驗收匯總資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_rpt_used($parm) {
		$rcv_det = array();

		$sql = $this->sql;
		$where_str='';
		if($parm['cat'] == 'F'){$table="lots";}else{$table="acc";}
//驗收主檔
		if($parm['ship']) $where_str.=" AND apb.ship ='".$parm['ship']."' ";

		$q_str = "SELECT apb_det.*, ap.currency, ap.special, ".$table.".".$table."_name as mat_name,
										 apb.rcv_sub_date,apb_po_link.qty as apb_qty, apb_po_link.amount as cost, 
										 apb_po_link.rate, apb_po_link.currency,	apb_po_link.id as link_id,
										 apb_po_link.po_id as po_link_id
							FROM apb, ap, apb_det, $table, apb_po_link 
							WHERE  ap.id =apb.po_id AND apb.rcv_num = apb_det.rcv_num 
							 AND apb_det.id = apb_po_link.rcv_id AND ".$table.".".$table."_code = mat_code".
							$where_str." AND apb.rcv_sub_date >= '".$parm['str']."' AND apb.rcv_sub_date <= '".$parm['end'].
							"' AND apb_det.mat_code like '".$parm['cat']."%' AND apb.status = 2 order by mat_code";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			

//當匯率尚無時
			if($row1['rate'] == 0 && $row1['currency'] <> 'NTD')
			{						 
				$row1['rate'] = $GLOBALS['rate']->get_rate($row1['currency'], $row1['rcv_sub_date']);
				if($row1['rate'] > 0)
				{
					$this->update_field_id('rate', $row1['rate'], $row1['link_id'], 'apb_po_link');
					$row1['cost'] = $row1['cost'] * $row1['rate'];				
					$this->update_field_id('amount', $row1['cost'], $row1['link_id'], 'apb_po_link');
				}else{
					$row1['cost'] = 0;
				}

			}

			$rcv_det[$i]=$row1;





			//取得採購資訊 -- 單價
			if($row1['special'] == 2){$table='ap_special';}else{$table='ap_det';}
			$po_det = $this->get_po_det($row1['po_link_id'],$table);
			if($po_det['prc_unit'] == '')$po_det['prc_unit'] = $po_det['po_unit'];
			$rcv_det[$i]['price'] = change_unit_price($po_det['prc_unit'],$po_det['po_unit'],$po_det['prics']);
			if($row1['currency'] <> 'NTD') $rcv_det[$i]['price']   = $po_det['prics'] * $row1['rate'];
			$rcv_det[$i]['unit'] = $po_det['po_unit'];
			$rcv_det[$i]['prc_unit'] = $po_det['prc_unit'];
	//		$rcv_det[$i]['cost'] = $rcv_det[$i]['price'] * $rcv_det[$i]['qty'];
			$i++;
		}	 
		return $rcv_det;

}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_rpt_dtl($parm)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_rpt_dtl($parm) {
		
		$rcv_det = array();
		$sql = $this->sql;
		$where_str='';
		if($parm['cat'] == 'F'){
			$table="lots";
			$cat = 'l';
			$bom_table = 'bom_lots';
		}else{
			$table="acc";
			$cat = 'a';
			$bom_table ='bom_acc';
		}
//驗收主檔
		if($parm['ship']) $where_str.=" AND apb.ship ='".$parm['ship']."' ";
//		if($parm['ord']) $where_str.=" AND wi.wi_num like '%".$parm['ord']."%' ";

/*
		$q_str = "SELECT apb_det.*, apb.rcv_sub_date, apb.ship, apb.dept, ap.ap_num, ap.currency, ap.special, ".$table.".".$table."_name as mat_name, rate.in
							FROM apb, ap, apb_det, $table LEFT JOIN rate ON (rate.date = apb.rcv_sub_date AND ap.currency = rate.currency)
							WHERE  ap.special < 2 AND ap.id =apb.po_id AND apb.rcv_num = apb_det.rcv_num AND ".$table.".".$table."_code = mat_code".
							$where_str." AND apb.rcv_sub_date >= '".$parm['str']."' AND apb.rcv_sub_date <= '".$parm['end'].
							"' AND apb_det.mat_code like '".$parm['cat']."%' AND apb.status = 2 ";
*/
/*
		$q_str = "SELECT apb_det.*, apb_po_link.qty as rcv_po_qty, apb_po_link.amount, apb_po_link.currency 
										 apb.rcv_sub_date, apb.ship, apb.dept, ap.ap_num,
										 ap.currency, ap.special, ".$table.".".$table."_name as mat_name
							FROM apb, ap, apb_det, apb_po_link, $table 
							WHERE  ap.special < 2 AND ap.id =apb.po_id AND apb.rcv_num = apb_det.rcv_num AND ".$table.".".$table."_code = mat_code".
							$where_str." AND apb.rcv_sub_date >= '".$parm['str']."' AND apb.rcv_sub_date <= '".$parm['end'].
							"' AND apb_det.mat_code like '".$parm['cat']."%' AND apb.status = 2 ";
*/

		$q_str = "SELECT apb_det.*, apb_po_link.qty as apb_qty, apb_po_link.amount as cost, apb_po_link.rate,
										 apb_po_link.currency, apb.rcv_sub_date, ap_det.id as po_id, ap_det.prics as price,
										 apb.ship, apb.dept, ap.ap_num, ap.po_num, ap_det.po_unit as unit, ap_det.prc_unit,
										 apb_po_link.id as link_id,
										 ap.currency, ap.special, ".$table.".".$table."_name as mat_name
							FROM apb, ap,ap_det, apb_det, apb_po_link, $table 
							WHERE  ap.ap_num = ap_det.ap_num AND ap_det.id = apb_po_link.po_id 
							 AND apb_po_link.rcv_id = apb_det.id AND ap.id =apb.po_id 
							 AND apb.rcv_num = apb_det.rcv_num AND ap.special < 2
							 AND  ".$table.".".$table."_code = mat_code".$where_str."
							 AND apb.rcv_sub_date >= '".$parm['str']."' AND apb.rcv_sub_date <= '".$parm['end']."'
							 AND apb_det.mat_code like '".$parm['cat']."%' AND apb.status = 2 ";



		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			if($row1['prc_unit'] == '')$row1['prc_unit'] = $row1['unit'];
			$ord_nums = $this->get_ord_num($row1['po_id'],$bom_table); //取得訂單編號
			if($row1['rate'] == 0 && $row1['currency'] <> 'NTD')
			{						 
				$row1['rate'] = $GLOBALS['rate']->get_rate($row1['currency'], $row1['rcv_sub_date']);
				if( $row1['rate'] > 0)
				{
					$this->update_field_id('rate', $row1['rate'], $row1['link_id'], 'apb_po_link');
					$row1['cost'] =$row1['cost'] * $row1['rate'];				
					$this->update_field_id('amount', $row1['cost'], $row1['link_id'], 'apb_po_link');
				}else{
					$row1['cost'] = 0;
				}
			}
			if($row1['currency'] <> 'NTD') $row1['price']   = $row1['price'] * $row1['rate'];
			$rcv_det[$i]=$row1;
			$rcv_det[$i]['sort']    = $ord_nums['wi_num'];
			$rcv_det[$i]['ord_num'] = $ord_nums['wi_num']; //取得訂單編號
	
			$i++;
	/*		
			//取得採購資訊 -- 單價/訂單/數量
			$id = explode("|",$row1['po_id']);
			$tmp_po_qty = 0;
			$po_det = array();
			for ($j=0; $j<sizeof($id); $j++)
			{
				$tmp_po = $this->get_po_det($id[$j],'ap_det');
				$tmp_po_qty += $tmp_po['po_qty'];
				$po_det[$j] = $tmp_po;
				$ord_nums = $this->get_ord_num($id[$j],$bom_table); //取得訂單編號
				$po_det[$j]['ord_num'] = $ord_nums['wi_num']; //取得訂單編號
				$po_det[$j]['bom_id'] = $ord_nums['bom_id']; //取得訂單編號
			}
			$tmp_apb_qty = 0;
			for ($j=0; $j<(sizeof($po_det) -1 ); $j++)  
			{				 
				//計算單一訂單驗收量
				$po_det[$j]['apb_qty'] = $row1['qty'] * ($po_det[$j]['po_qty'] / $tmp_po_qty ); 
				$po_det[$j]['apb_qty'] = number_format($po_det[$j]['apb_qty'], 2, '.', '');
				$tmp_apb_qty += $po_det[$j]['apb_qty'];
				//計算匯率轉換
			//	if ($row1['in']) $po_det[$j]['prics'] = $po_det[$j]['prics'] * $row1['in'];
				
				if ($row1['currency'] <> 'NTD')				
					$po_det[$j]['prics'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['rcv_sub_date'],$po_det[$j]['prics']);
				//將單一訂單驗收資料加入陣列中
				if($parm['ord'] == '' || strstr($po_det[$j]['ord_num'], $parm['ord']))
				{
					$rcv_det[$i]=$row1;
					$rcv_det[$i]['sort']    = $po_det[$j]['ord_num'];
					$rcv_det[$i]['ord_num'] = $po_det[$j]['ord_num'];
					$rcv_det[$i]['bom_id']  = $po_det[$j]['bom_id'];
					$rcv_det[$i]['apb_qty'] = $po_det[$j]['apb_qty'];
					$rcv_det[$i]['unit']    = $po_det[$j]['po_unit'];
					$rcv_det[$i]['price']   = $po_det[$j]['prics'];
					$rcv_det[$i]['cost']    = $rcv_det[$i]['price'] * $rcv_det[$i]['apb_qty'];
					$i++;
				}
			}
		
      //最後一筆訂單驗收資料
				$po_det[$j]['apb_qty'] = $row1['qty'] - $tmp_apb_qty;
				$po_det[$j]['apb_qty'] = number_format($po_det[$j]['apb_qty'], 2, '.', '');
				$tmp_apb_qty += $po_det[$j]['apb_qty'];
				// if ($row1['in']) $po_det[$j]['prics'] = $po_det[$j]['prics'] * $row1['in'];
	//			if ($row1['currency'] <> 'NTD') $po_det[$j]['prics'] = $po_det[$j]['prics'] * $row1['in'];
				
				if ($row1['currency'] <> 'NTD')				
					$po_det[$j]['prics'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['rcv_sub_date'],$po_det[$j]['prics']);

				if($parm['ord'] == '' || strstr($po_det[$j]['ord_num'], $parm['ord']))
				{
					$rcv_det[$i]=$row1;
					$rcv_det[$i]['sort']    = $po_det[$j]['ord_num'];
					$rcv_det[$i]['ord_num'] = $po_det[$j]['ord_num'];
					$rcv_det[$i]['bom_id']  = $po_det[$j]['bom_id'];
					$rcv_det[$i]['apb_qty'] = $po_det[$j]['apb_qty'];
					$rcv_det[$i]['unit']    = $po_det[$j]['po_unit'];
					$rcv_det[$i]['price']   = $po_det[$j]['prics'];
					$rcv_det[$i]['cost']    = $rcv_det[$i]['price'] * $rcv_det[$i]['apb_qty'];
					$i++;
				}
		*/
		}	 



		$where_str='';
//驗收主檔
		if($parm['ship']) $where_str.=" AND apb.ship ='".$parm['ship']."' ";
		if($parm['ord']) $where_str.=" AND ap_special.ord_num like '%".$parm['ord']."%' ";
		if($parm['cat'] == 'F'){
			$table="lots";
			$cat = 'l';
		}else{
			$table="acc";
			$cat = 'a';
		}

/*
		$q_str = "SELECT apb_det.*, apb.rcv_sub_date, apb.ship, apb.dept,ap_special.ord_num, ap.ap_num, ap.currency, ap.special, ".$table.".".$table."_name as mat_name, rate.in
							FROM  apb, ap, ap_special, apb_det, $table LEFT JOIN rate ON (rate.date = apb.rcv_sub_date AND ap.currency = rate.currency)
							WHERE ap.ap_num = ap_special.ap_num AND ap.special = 2 AND  apb_det.po_id = ap_special.id
							AND ap.id =apb.po_id AND apb.rcv_num = apb_det.rcv_num AND ".$table.".".$table."_code = apb_det.mat_code".
							$where_str." AND apb.rcv_sub_date >= '".$parm['str']."' AND apb.rcv_sub_date <= '".$parm['end'].
							"' AND apb_det.mat_code like '".$parm['cat']."%' AND apb.status = 2 order by ap_special.ord_num";


		$q_str = "SELECT apb_det.*, apb.rcv_sub_date, apb.ship, apb.dept,ap_special.ord_num, ap.ap_num, ap.currency, ap.special, ".$table.".".$table."_name as mat_name
							FROM  apb, ap, ap_special, apb_det, $table 
							WHERE ap.ap_num = ap_special.ap_num AND ap.special = 2 AND  apb_det.po_id = ap_special.id
							AND ap.id =apb.po_id AND apb.rcv_num = apb_det.rcv_num AND ".$table.".".$table."_code = apb_det.mat_code".
							$where_str." AND apb.rcv_sub_date >= '".$parm['str']."' AND apb.rcv_sub_date <= '".$parm['end'].
							"' AND apb_det.mat_code like '".$parm['cat']."%' AND apb.status = 2 order by ap_special.ord_num";
*/


		$q_str = "SELECT apb_det.*, apb_po_link.currency, apb_po_link.amount as cost, apb_po_link.rate,
											apb_po_link.qty as apb_qty, apb.rcv_sub_date, apb.ship, 
											apb.dept,ap_special.ord_num, ap.po_num, ap.ap_num, ap_special.po_unit as unit,
											ap_special.prics as price, ap_special.prc_unit,
											 ".$table.".".$table."_name as mat_name
							FROM  apb, ap, ap_special, apb_det, apb_po_link, $table 
							WHERE ap.ap_num = ap_special.ap_num AND ap_special.id = apb_po_link.po_id 
							 AND  apb_det.po_id = ap_special.id AND apb_det.id = apb_po_link.rcv_id
							 AND ap.id =apb.po_id AND apb.rcv_num = apb_det.rcv_num
							 AND ap.special = 2  AND ".$table.".".$table."_code = apb_det.mat_code".
							$where_str." AND apb.rcv_sub_date >= '".$parm['str']."' AND apb.rcv_sub_date <= '".$parm['end'].
							"' AND apb_det.mat_code like '".$parm['cat']."%' AND apb.status = 2 order by ap_special.ord_num";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row1 = $sql->fetch($q_result)) {
			if($row1['prc_unit'] == '')$row1['prc_unit'] = $row1['po_unit'];

			if($row1['rate'] == 0 && $row1['currency'] <> 'NTD')
			{						 
				$row1['rate'] = $GLOBALS['rate']->get_rate($row1['currency'], $row1['rcv_sub_date']);
				if($row1['rate'] > 0)
				{
					$this->update_field_id('rate', $row1['rate'], $row1['link_id'], 'apb_po_link');
					$row1['cost'] = $row1['cost'] * $row1['rate'];				
					$this->update_field_id('amount', $row1['cost'], $row1['link_id'], 'apb_po_link');
				}else{
					$row1['cost'] = 0;
				}

			}
			if($row1['currency'] <> 'NTD') $row1['price'] = $row1['price'] * $row1['rate'];

			$rcv_det[$i]=$row1;
			$rcv_det[$i]['sort'] = $row1['ord_num'];

			$i++;
	/*
			//取得採購資訊 -- 單價
			$id = explode("|",$row1['po_id']);			
			if($row1['special'] == 2){$table='ap_special';}else{$table='ap_det';}
			$po_det = $this->get_po_det($id[0],$table);
			$rcv_det[$i]['price'] = $po_det['prics'];
			//if ($row1['in']) $rcv_det[$i]['price'] = $rcv_det[$i]['price'] * $row1['in'];
			//if ($row1['currency'] <> 'NTD') $rcv_det[$i]['price'] = $rcv_det[$i]['price'] * $row1['in'];
			if ($row1['currency'] <> 'NTD')				
					$rcv_det[$i]['price'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['rcv_sub_date'],$rcv_det[$i]['price']);

			$rcv_det[$i]['unit'] = $po_det['po_unit'];
			$rcv_det[$i]['apb_qty'] = $rcv_det[$i]['qty'];
			$rcv_det[$i]['cost'] = $rcv_det[$i]['price'] * $rcv_det[$i]['apb_qty'];
			
			$i++;
	*/		
		}	 
		return $rcv_det;

}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rcv_po_det($det_id,$special) {

		$sql = $this->sql;
		$rcvd = array();
		$special == 2 ? $tbl="ap_special" : $tbl="ap_det";
		
		$q_str = "SELECT ap.po_num, ".$tbl.".po_qty, ".$tbl.".po_eta, apb.rcv_num, apb.rcv_sub_date, apb.payment, apb.status,apb.rcv_user,
										 apb_po_link.qty, ".$tbl.".po_unit
							FROM  ap, apb, apb_det, apb_po_link,".$tbl."
							WHERE apb.payment not like '%before%' and apb_po_link.po_id = '".$det_id."' and apb_det.id = apb_po_link.rcv_id
								  and apb.rcv_num = apb_det.rcv_num and ".$tbl.".id = apb_po_link.po_id 
								  and  ap.ap_num = ".$tbl.".ap_num ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['payment']<>'T/T before shipment' or $row['status']<>2)
				$rcvd[] = $row;
		}

		return $rcvd;
	}
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_apb_po_det($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_apb_po_det($apb_po_id,$special,$ord_num) {
	$sql = $this->sql;
	$rcvd = array();
	
	$special == 2 ? $tbl="ap_special" : $tbl="ap_det";

	$q_str = "
	SELECT ap.po_num, 
	".$tbl.".po_qty, ".$tbl.".po_eta, 
	apb.rcv_num, apb.rcv_sub_date, apb.rcv_user,apb.payment,
	apb_po_link.qty, ".$tbl.".po_unit
	
	FROM  
	ap ,".$tbl.",
	apb, apb_det, apb_po_link
	
	WHERE 

	apb_po_link.po_id = '".$apb_po_id."' and 
	apb_po_link.ord_num = '".$ord_num."' and 
	apb_det.id = apb_po_link.rcv_id and 
	apb.rcv_num = apb_det.rcv_num AND 
	((`apb`.`status` = '2' and `apb`.`payment` not like '%before%') or `apb`.`status` = '4') and 
	".$tbl.".id = apb_po_link.po_id and  
	ap.ap_num = ".$tbl.".ap_num ";

	// echo $q_str;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row = $sql->fetch($q_result)) {
		$rcvd[] = $row;
	}
	
	return $rcvd;		
} // end func




function get_bom_pre_apb_det($po_nums, $mat_cat, $mat_id, $color, $size, $ord_num) {

	$sql = $this->sql;
	
	$apb_det = array();
	$po_num_ary = array();
	$ap_num_str = '';
	
	$po_num_ary = explode(",", $po_nums);
	foreach($po_num_ary as $key => $val){
		$ap_num_str .= "'".str_replace("PO", "PA", $val)."',";
	}
	$ap_num_str = substr($ap_num_str, 0, -1);
	
	$q_str = "SELECT apb.rcv_num, apb.rcv_date, apb.rcv_user, apb_det.color, apb_det.size, apb_det.ap_num, apb_po_link.qty
			  FROM apb , apb_det , apb_po_link
			  WHERE apb_det.mat_id = '".$mat_id."' and apb_det.color = '".$color."' and apb_det.size = '".$size."' and 
					apb_det.ap_num in(".$ap_num_str.") and apb_po_link.rcv_id = apb_det.id and apb_po_link.ord_num ='".$ord_num."' and 
					apb.rcv_num = apb_det.rcv_num and apb.status in(2,14)";

	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while ($row = $sql->fetch($q_result)) {
		$row['po_num'] = str_replace("PA", "PO", $row['ap_num']);
		$apb_det[] = $row;
	}
	
	return $apb_det;
}



function get_bom_apb_det($po_nums, $mat_cat, $mat_id, $color, $size, $ord_num) {
	
	$sql = $this->sql;
	
	$apb_det = array();
	$po_num_ary = array();
	$ap_num_str = '';
	
	$po_num_ary = explode(",", $po_nums);
	foreach($po_num_ary as $key => $val){
		$ap_num_str .= "'".str_replace("PO", "PA", $val)."',";
	}
	$ap_num_str = substr($ap_num_str, 0, -1);
	
	$q_str = "SELECT apb.rcv_num, apb.rcv_date, apb.rcv_user, apb_det.color, apb_det.size, apb_det.ap_num, apb_po_link.qty
			  FROM apb , apb_det , apb_po_link
			  WHERE apb_det.mat_id = '".$mat_id."' and apb_det.color = '".$color."' and apb_det.size = '".$size."' and 
					apb_det.ap_num in(".$ap_num_str.") and apb_po_link.rcv_id = apb_det.id and apb_po_link.ord_num ='".$ord_num."' and 
					apb.rcv_num = apb_det.rcv_num and apb.status in(4,6,8,10,12,16)";

	
	
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while ($row = $sql->fetch($q_result)) {
		$row['po_num'] = str_replace("PA", "PO", $row['ap_num']);
		$apb_det[] = $row;
	}
	
	return $apb_det;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rcv_pp_lot($id) {

		$sql = $this->sql;
		$rcvd = array();
		$rcvd = $this->get_rcv_po_det($id,'l');
		$q_str = "SELECT ap.po_num, ap_special.po_qty, ap_special.po_eta, apb.rcv_num, apb.rcv_sub_date,
										 apb_det.mat_code, apb_po_link.qty, ap_special.po_unit, ap_special.id as spec_id
							FROM  bom_lots, ap_special, ap, apb_det, apb_po_link, apb
							WHERE bom_lots.ap_mark = ap_special.ap_num AND ap_special.ap_num = ap.ap_num AND 
									  apb.po_id = ap.id AND
							      apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ap_special.id AND
							      apb.rcv_num = apb_det.rcv_num AND ap_special.mat_cat = 'l' AND bom_lots.id = $id";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp[] = $row;
		}
		for($i=0; $i<sizeof($tmp); $i++)		
		{
			$q_str = "SELECT sum(qty) as qty FROM apb_po_link 
							  WHERE po_id = '".$tmp[$i]['spec_id']."' GROUP BY po_id";
			$q_result = $sql->query($q_str);
			$row = $sql->fetch($q_result);
			$q_str = "UPDATE `ap_special` SET `apb_qty` = '".$row['qty']."' WHERE `id` = '".$tmp[$i]['spec_id']."'";
			$q_result = $sql->query($q_str);
			$rcvd[] = $tmp[$i];			
		}
		return $rcvd;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rcv_pp_acc($id) {

		$sql = $this->sql;
		$rcvd = array();
		$rcvd = $this->get_rcv_po_det($id,'a');
		$q_str = "SELECT ap.po_num, ap_special.po_qty, ap_special.po_eta, apb.rcv_num, apb.rcv_sub_date,
										 apb_det.mat_code, apb_po_link.qty, ap_special.po_unit, ap_special.id as spec_id
							FROM  bom_acc, ap_special, ap, apb_det, apb_po_link, apb
							WHERE bom_acc.ap_mark = ap_special.ap_num AND ap_special.ap_num = ap.ap_num AND 
									  apb.po_id = ap.id AND
							      apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ap_special.id AND
							      apb.rcv_num = apb_det.rcv_num AND ap_special.mat_cat = 'a' AND bom_acc.id = $id";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp[] = $row;
		}
		for($i=0; $i<sizeof($tmp); $i++)		
		{
			$q_str = "SELECT sum(qty) as qty FROM apb_po_link 
							  WHERE po_id = '".$tmp[$i]['spec_id']."' GROUP BY po_id";
			$q_result = $sql->query($q_str);
			$row = $sql->fetch($q_result);
			$q_str = "UPDATE `ap_special` SET `apb_qty` = '".$row['qty']."' WHERE `id` = '".$tmp[$i]['spec_id']."'";
			$q_result = $sql->query($q_str);
			$rcvd[] = $tmp[$i];			
		}
		return $rcvd;
	} // end func		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rcv_po_ext($id) {

		$sql = $this->sql;
		$rcvd = array();		
		$q_str = "SELECT ap.po_num, ap_special.po_qty, ap_special.po_eta, apb.rcv_num, apb.rcv_sub_date,
										 apb_det.mat_code, apb_po_link.qty, ap_special.po_unit, ap_special.id as spec_id
							FROM  ap_special, ap, apb_det, apb_po_link, apb
							WHERE ap_special.ap_num = ap.ap_num AND apb.po_id = ap.id AND									  
							      apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ap_special.id AND
							      apb.rcv_num = apb_det.rcv_num AND ap_special.id = $id";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$rcvd[] = $row;
		}
		for($i=0; $i<sizeof($rcvd); $i++)		
		{
			$q_str = "SELECT sum(qty) as qty FROM apb_po_link 
							  WHERE po_id = '".$rcvd[$i]['spec_id']."' GROUP BY po_id";
			$q_result = $sql->query($q_str);
			$row = $sql->fetch($q_result);
			$q_str = "UPDATE `ap_special` SET `apb_qty` = '".$row['qty']."' WHERE `id` = '".$rcvd[$i]['spec_id']."'";
			$q_result = $sql->query($q_str);
			
		}
		return $rcvd;
	} // end func			


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_link_field($field,$table,$where) {
		$row=array();
		$sql = $this->sql;
		$q_str="SELECT ". $field. " FROM ".$table." WHERE ".$where;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
        // echo $q_str.'<br>';
		while($row1 = $sql->fetch($q_result))
		{
			$row[] = $row1;
		}
        // print_r($row);
		return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_table_name($id) {
		$row=array();
		$sql = $this->sql;
		$q_str="SELECT ap.special FROM apb, apb_det, ap
						WHERE apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id AND apb_det.id =".$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		if($row1['special'] == 2)
		{
			return 'ap_special';
		}else{
			return 'ap_det';
		}
		
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_lots_cost($ord_num,$wi_id) {
		$row=array();

		$sql = $this->sql;
		$cury_d = $GLOBALS['cury_d'];
		$q_str="SELECT bom_lots.qty as bom_qty, sum(ap_det.po_qty) as po_qty, ap_det.po_unit, ap_det.prics, ap_det.prc_unit,
									 ap_det.amount as po_amount, ap.special, ap.dm_way, sum(apb_po_link.qty) as apb_qty,
									 ap.currency, apb_det.mat_code, bom_id, ap_det.unit
						FROM  apb, apb_det, apb_po_link, ap_det, ap, bom_lots
						WHERE apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id AND
									apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ap_det.id AND
									ap_det.ap_num = ap.ap_num AND ap_det.bom_id = bom_lots.id AND
									ap_det.mat_cat = 'l' AND ap.status >= 0 AND
									bom_lots.wi_id = '".$wi_id."'
						GROUP BY bom_id
						ORDER BY bom_id";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$cost['bom'] = $cost['po'] = $cost['rcv'] = 0; $bom_id = '';
		while($row = $sql->fetch($q_result))
		{
			if($row['prc_unit'])
			{
				$rtn_price = change_unit_price($row['prc_unit'],$row['po_unit'],$row['prics']);
			}else{
				$rtn_price = $row['prics'];
				$row['po_amount'] = $row['prics'] * $row['po_qty'];
			}
			$rtn_price *= $cury_d[$row['currency']]['NTD'];
			$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);
			if($bom_id <> $row['bom_id'])
			{
				$tmp = explode(',',$row['bom_qty']);
				$bom_qty = 0;
				for($i=0; $i<sizeof($tmp); $i++) $bom_qty += $tmp[$i];
				$rtn_price2 = change_unit_price($row['po_unit'],$row['unit'],$rtn_price);
				$cost['bom'] += ($bom_qty * $rtn_price2);		
				$bom_id = $row['bom_id'];				
			}
			if(strstr($row['dm_way'],'before shipment') )
			{
				$cost['rcv'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);
			}else{
				$cost['rcv'] += ($row['apb_qty'] * $rtn_price);
			}
			
		}
		
	
		$q_str="SELECT ap_special.po_qty, ap_special.po_unit, ap_special.prics, ap_special.prc_unit,
									 ap_special.amount as po_amount, ap.dm_way, sum(apb_po_link.qty) as apb_qty,
									 ap.currency
						FROM  apb, apb_det, apb_po_link, ap_special, ap
						WHERE apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id AND
									apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ap_special.id AND
									ap_special.ap_num = ap.ap_num AND
									ap_special.mat_cat = 'l' AND ap_special.ord_num = '".$ord_num."' GROUP BY ap_special.id";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			if($row['prc_unit'])
			{
				$rtn_price = change_unit_price($row['prc_unit'],$row['po_unit'],$row['prics']);
			}else{
				$rtn_price = $row['prics'];
				$row['po_amount'] = $row['prics'] * $row['po_qty'];
			}
			$rtn_price *= $cury_d[$row['currency']]['NTD'];
			$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);
			if(strstr($row['dm_way'],'before shipment') )
			{
				$cost['rcv'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);
			}else{
				$cost['rcv'] += ($row['apb_qty'] * $rtn_price);
			}
			
		}	
		
		
		$q_str="SELECT DISTINCT ap_special.po_qty, ap_special.po_unit, ap_special.prics, ap_special.prc_unit,
									 ap_special.amount as po_amount, ap.dm_way, ap.currency, bom_lots.qty as bom_qty,
									 lots_use.unit, bom_lots.id as bom_id, ap.ap_num
						FROM   ap_special, ap, bom_lots, lots_use
						WHERE  ap_special.ap_num = ap.ap_num AND ap.ap_num = bom_lots.ap_mark AND 
									 ap_special.mat_code = lots_use.lots_code AND ap_special.color = bom_lots.color AND
									 bom_lots.lots_used_id = lots_use.id AND
									 ap_special.mat_cat = 'l' AND ap_special.ord_num = '".$ord_num."' ORDER BY bom_id";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			if($row['prc_unit'])
			{
				$rtn_price = change_unit_price($row['prc_unit'],$row['po_unit'],$row['prics']);
			}else{
				$rtn_price = $row['prics'];
			}
			$rtn_price *= $cury_d[$row['currency']]['NTD'];
			$tmp = explode(',',$row['bom_qty']);
			$bom_qty = 0;
			for($i=0; $i<sizeof($tmp); $i++) $bom_qty += $tmp[$i];
			$rtn_price2 = change_unit_price($row['po_unit'],$row['unit'],$rtn_price);
			$cost['bom'] += ($bom_qty * $rtn_price2);					
			$bom_id = $row['bom_id'];				
		}	
				
		return $cost;
		
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_acc_cost($ord_num,$wi_id) {
		$row=array();
		$sql = $this->sql;
		$cury_d = $GLOBALS['cury_d'];
		$q_str="SELECT bom_acc.qty as bom_qty, sum(ap_det.po_qty) as po_qty, ap_det.po_unit, ap_det.prics, ap_det.prc_unit,
									 ap_det.amount as po_amount, ap.special, ap.dm_way, sum(apb_po_link.qty) as apb_qty,
									 ap.currency, apb_det.mat_code, ap_det.unit, bom_id, ap_det.id as po_id, 
									 apb_det.id as rcv_id
						FROM  apb, apb_det, apb_po_link, ap_det, ap, bom_acc
						WHERE apb.po_id = ap.id AND apb.rcv_num = apb_det.rcv_num AND 
									apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ap_det.id AND
									ap_det.ap_num = ap.ap_num AND ap_det.bom_id = bom_acc.id 
									AND ap.status >= 0 AND
									ap_det.mat_cat = 'a' AND bom_acc.wi_id = '".$wi_id."'
									GROUP BY bom_id
									ORDER BY bom_id, ap_det.id";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$cost['bom'] = $cost['po'] = $cost['rcv'] = 0; $bom_id = $po_id ='';
		while($row = $sql->fetch($q_result))
		{
			if($row['prc_unit'])
			{
				$rtn_price = change_unit_price($row['prc_unit'],$row['po_unit'],$row['prics']);

			}else{
				$rtn_price = $row['prics'];
				$row['po_amount'] = $row['prics'] * $row['po_qty'];
			}
			$rtn_price *= $cury_d[$row['currency']]['NTD'];
			
			if($po_id <> $row['po_id'])
			{
				$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);				
				$po_id = $row['po_id'];
			}
			if($bom_id <> $row['bom_id'])
			{
				$tmp = explode(',',$row['bom_qty']);
				$bom_qty = 0;
				for($i=0; $i<sizeof($tmp); $i++) $bom_qty += $tmp[$i];
				$rtn_price2 = change_unit_price($row['po_unit'],$row['unit'],$rtn_price);
				$cost['bom'] += ($bom_qty * $rtn_price2);			
				$bom_id = $row['bom_id'];
			}
			if(strstr($row['dm_way'],'before shipment') )
			{
				$cost['rcv'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);
			}else{
				$cost['rcv'] += ($row['apb_qty'] * $rtn_price);
			}
			
		}
		
	
		$q_str="SELECT ap_special.po_qty, ap_special.po_unit, ap_special.prics, ap_special.prc_unit,
									 ap_special.amount as po_amount, ap.dm_way, sum(apb_po_link.qty) as apb_qty,
									 ap.currency
						FROM  apb, apb_det, apb_po_link, ap_special, ap
						WHERE apb_det.rcv_num = apb.rcv_num AND apb.po_id = ap.id AND
									apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id = ap_special.id AND
									ap_special.ap_num = ap.ap_num AND
									ap_special.mat_cat = 'a' AND ap_special.ord_num = '".$ord_num."' GROUP BY ap_special.id";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			if($row['prc_unit'])
			{
				$rtn_price = change_unit_price($row['prc_unit'],$row['po_unit'],$row['prics']);
			}else{
				$rtn_price = $row['prics'];
				$row['po_amount'] = $row['prics'] * $row['po_qty'];
			}
			$rtn_price *= $cury_d[$row['currency']]['NTD'];
			$cost['po'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);
			if(strstr($row['dm_way'],'before shipment') )
			{
				$cost['rcv'] += ($row['po_amount'] * $cury_d[$row['currency']]['NTD']);
			}else{
				$cost['rcv'] += ($row['apb_qty'] * $rtn_price);
			}			
		}	
	
	
		$q_str="SELECT ap_special.po_qty, ap_special.po_unit, ap_special.prics, ap_special.prc_unit,
									 ap_special.amount as po_amount, ap.dm_way, ap.currency, bom_acc.qty as bom_qty,
									 acc_use.unit, bom_acc.id as bom_id
						FROM   ap_special, ap, bom_acc, acc_use
						WHERE  ap_special.ap_num = ap.ap_num AND ap.ap_num = bom_acc.ap_mark AND
									 bom_acc.acc_used_id = acc_use.id AND
									 ap_special.mat_cat = 'a' AND ap_special.ord_num = '".$ord_num."'";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			if($row['prc_unit'])
			{
				$rtn_price = change_unit_price($row['prc_unit'],$row['po_unit'],$row['prics']);
			}else{
				$rtn_price = $row['prics'];
			}
			$rtn_price *= $cury_d[$row['currency']]['NTD'];
			$tmp = explode(',',$row['bom_qty']);
			$bom_qty = 0;
			for($i=0; $i<sizeof($tmp); $i++) $bom_qty += $tmp[$i];
			$rtn_price2 = change_unit_price($row['po_unit'],$row['unit'],$rtn_price);
			$cost['bom'] += ($bom_qty * $rtn_price2);					
			$bom_id = $row['bom_id'];				
		}		
		
		return $cost;
		
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_rcvd($ord_num) {
		$row=array();
		$sql = $this->sql;
		$det_row = array();
		$special_row = array();
		
		$q_str="SELECT ap_det.apb_qty, ap_det.id, ap.id as ap_id
						FROM  ap_det, bom_acc, wi, ap
						WHERE ap.ap_num = ap_det.ap_num AND ap_det.bom_id = bom_acc.id AND ap.status >= 0 AND
									bom_acc.wi_id = wi.id AND ap_det.mat_cat = 'a' AND wi.wi_num = '".$ord_num."'";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			$det_row[] = $row;
			if($row['apb_qty'] == 0)			
			{
				return 0;
			}			
		}

		$q_str="SELECT ap_det.apb_qty, ap_det.id, ap.id as ap_id
						FROM  ap_det, bom_lots, wi, ap
						WHERE ap.ap_num = ap_det.ap_num AND ap_det.bom_id = bom_lots.id AND ap.status >= 0 AND
									bom_lots.wi_id = wi.id AND ap_det.mat_cat = 'l' AND wi.wi_num = '".$ord_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			$det_row[] = $row;
			if($row['apb_qty'] == 0)			
			{
				return 0;
			}			
		}		
	
		$q_str="SELECT ap_special.apb_qty, ap_special.id, ap.id as ap_id
						FROM   ap_special, ap
						WHERE	 ap.ap_num = ap_special.ap_num AND ap.status >= 0 AND ord_num = '".$ord_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			$det_row[] = $row;
			if($row['apb_qty'] == 0)			
			{
				return 0;
			}			
		}	

	for($i=0; $i<sizeof($det_row); $i++)
	{
		$q_str="SELECT apb.status
						FROM  apb, apb_det, apb_po_link
						WHERE apb_det.rcv_num = apb.rcv_num AND apb.po_id =".$det_row[$i]['ap_id']." AND
									apb_det.id = apb_po_link.rcv_id AND apb_po_link.po_id =".$det_row[$i]['id'];			
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			if($row['status'] < 2)			
			{
				return 0;
			}			
		}

	}

		return 1;
		
	} // end func	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_rec($fd,$mat_cat,$ap_table) {

		$sql = $this->sql;
		//$ap_det = array();	
		$ord_fd = array();
		for($i=0; $i<sizeof($fd); $i++)
		{
			if($mat_cat == 'l')
			{
				if($ap_table == 'ap_det')
				{
					$q_str = "SELECT wi_num as ord_num
										FROM ap_det, bom_lots, wi
										WHERE ap_det.bom_id = bom_lots.id AND bom_lots.wi_id = wi.id AND
													ap_det.id =".$fd[$i]['po_id'];

					if (!$q_result = $sql->query($q_str)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;    
					}		
					while ($row1 = $sql->fetch($q_result)) {	
						$mk = 0;
						for($j=0; $j<sizeof($ord_fd); $j++)
						{
							if($ord_fd[$j]['ord_num'] == $row1['ord_num'])
							{
								$mk = 1;
								break;
							}// end if($ord_fd[$j] == $row1['ord_num'])
						}// end for($j=0; $j<sizeof($ord_fd); $j++)
						
						if($mk == 0)$ord_fd[]=$row1;			
					}	// end while ($row1 = $sql->fetch($q_result))		
			
				}else{
					$q_str = "SELECT ord_num FROM ap_special WHERE id =".$fd[$i]['po_id'];

					if (!$q_result = $sql->query($q_str)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;    
					}	
					$row1 = $sql->fetch($q_result);
					$ord_fd[]=$row1;
				}// end if($ap_table == 'ap_det')
				
			
			}else{
				if($ap_table == 'ap_det')
				{
					$q_str = "SELECT smpl_code as ord_num, acc_cat
										FROM ap_det, bom_acc, acc_use
										WHERE ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id AND
													ap_det.id =".$fd[$i]['po_id'];
					if (!$q_result = $sql->query($q_str)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;    
					}	
					while ($row1 = $sql->fetch($q_result)) {	
						$mk = 0;
						for($j=0; $j<sizeof($ord_fd); $j++)
						{
							if($ord_fd[$j]['ord_num'] == $row1['ord_num'] && $ord_fd[$j]['acc_cat'] == $row1['acc_cat'])
							{
								$mk = 1;
								break;
							}// end if($ord_fd[$j] == $row1['ord_num'])
						}// end for($j=0; $j<sizeof($ord_fd); $j++)
						
						if($mk == 0)$ord_fd[]=$row1;			
					}	// end while ($row1 = $sql->fetch($q_result))						
				}else{
					$q_str = "SELECT ord_num FROM ap_special WHERE id =".$fd[$i]['po_id'];
					if (!$q_result = $sql->query($q_str)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;    
					}	
					$row1 = $sql->fetch($q_result);
					$row1['acc_cat'] = 0;
					$ord_fd[]=$row1;
				}
			}
			
			
			
		}// for($i=0; $i<sizeof($fd); $i++)

		return $ord_fd;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->back_ord_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function back_ord_rcvd($mat_cat,$ord_fd) {

	$sql = $this->sql;
	$mk = 0;

	for ($i=0; $i<sizeof($ord_fd); $i++)
	{
		if( $mat_cat == 'l' )
		{
			$ship_det = array();
			$q_str = "SELECT rcv_rmk, apb_qty, ship_way, ship_date 
								FROM ap_det, bom_lots, lots_use, wi 
								WHERE ap_det.bom_id = bom_lots.id AND bom_lots.wi_id = wi.id 
								AND bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0
								AND lots_use.support = 0
								AND ap_det.mat_cat ='l' AND wi.wi_num ='".$ord_fd[$i]['ord_num']."'";

			$q_result = $sql->query($q_str);		
			
			while ($row = $sql->fetch($q_result)) {	
				if ($row['apb_qty']== 0) 
				{
					$mk = 1;
				}else{
					$ship_det[] = $row; 
				}
			}

			$q_str = "SELECT rcv_rmk, apb_qty, ship_way, ship_date FROM ap_special WHERE ord_num ='".$ord_fd[$i]['ord_num']."' AND mat_cat ='l'";
			$q_result = $sql->query($q_str);
			
			while ($row = $sql->fetch($q_result)) {	
				if ($row['apb_qty']== 0) 
				{
					$mk = 1;
				}else{
					$ship_det[] = $row; 
				}
			}				
		
			if ($mk == 1)		//當物料皆驗收完成填入驗收日
			{
				// $q_str = "UPDATE pdtion SET mat_shp = NULL WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
				// $q_result = $sql->query($q_str);		
			}
/*				
			$ship_date = $GLOBALS['order']->get_field_value('mat_rcv', '', $ord_fd[$i]['ord_num'],'pdtion');
			$ship_way = $GLOBALS['order']->get_field_value('mat_ship_way', '', $ord_fd[$i]['ord_num'],'pdtion');
			if( $ship_way == '')
			{
				$ship_date = '';
				for($s=0; $s<sizeof($ship_det); $s++)//若ship物料同驗收物料時,消除ship資料				
				{
					if($ship_date < $ship_det[$s]['ship_date'] )
					{
						$ship_date = $ship_det[$s]['ship_date'];
						$ship_way = $ship_det[$s]['ship_way'];
					}
				}	
				$q_str = "UPDATE pdtion SET mat_rcv ='$ship_date' mat_ship_way = '$ship_way' WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
				$q_result = $sql->query($q_str);	
			}
*/
			} else {
				//主要副料
				if ( $ord_fd[$i]['acc_cat'] == 1)
				{							
					$ship_det = array();
					$q_str = "SELECT rcv_rmk, apb_qty, ship_way, ship_date FROM ap_det, bom_acc, acc_use, wi 
										WHERE ap_det.bom_id = bom_acc.id AND bom_acc.wi_id = wi.id 
										AND bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
										AND acc_use.acc_cat = '1' AND acc_use.support = 0
										AND ap_det.mat_cat ='a' AND wi.wi_num ='".$ord_fd[$i]['ord_num']."'";

					$q_result = $sql->query($q_str);		
					while ($row = $sql->fetch($q_result)) {	
						if ($row['apb_qty']== 0) 
						{
							$mk = 1;
						}else{
							$ship_det[] = $row;
						}
					}
					if(!isset($row) || !$row) $mk = 1;

						
					if ($mk == 1)
					{
						// $q_str = "UPDATE pdtion SET m_acc_shp = NULL WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
						
						// $q_result = $sql->query($q_str);		
					}

					/*$ship_date = $GLOBALS['order']->get_field_value('m_acc_rcv', '', $ord_fd[$i]['ord_num'],'pdtion');
					$ship_way = $GLOBALS['order']->get_field_value('m_acc_ship_way', '', $ord_fd[$i]['ord_num'],'pdtion');
					if( $ship_way == '')
					{
						$ship_date = '';
						for($s=0; $s<sizeof($ship_det); $s++)//若ship物料同驗收物料時,消除ship資料				
						{
							if($ship_date < $ship_det[$s]['ship_date'] )
							{
								$ship_date = $ship_det[$s]['ship_date'];
								$ship_way = $ship_det[$s]['ship_way'];
							}
						}	
						$q_str = "UPDATE pdtion SET m_acc_rcv ='$ship_date' m_acc_ship_way = '$ship_way' WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
						$q_result = $sql->query($q_str);	
					}*/
				}
//其他副料		
			if ( $ord_fd[$i]['acc_cat'] == 0)
			{
				
				$ship_det = array();
				$q_str = "SELECT rcv_rmk, apb_qty, ship_way, ship_date FROM ap_det, bom_acc, acc_use, wi 
									WHERE ap_det.bom_id = bom_acc.id AND bom_acc.wi_id = wi.id 
									AND bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '0' AND acc_use.support = 0
									AND ap_det.mat_cat ='a' AND wi.wi_num ='".$ord_fd[$i]['ord_num']."'";

				$q_result = $sql->query($q_str);		
				while ($row = $sql->fetch($q_result)) {	
					if ($row['apb_qty']== 0) 
					{
						$mk = 1;
					}else{
						$ship_det[] = $row;
					}
				}
				if(!isset($row) || !$row) $mk = 1;

				$q_str = "SELECT rcv_rmk, apb_qty, ship_way, ship_date FROM ap_special WHERE ord_num ='".$ord_fd[$i]['ord_num']."' AND mat_cat ='a'";

				$q_result = $sql->query($q_str);		
				while ($row = $sql->fetch($q_result)) {	
					if ($row['apb_qty']== 0) 
					{
						$mk = 1;
					}else{
						$ship_det[] = $row;
					}
				}

				if ($mk == 1)
				{
					// $q_str = "UPDATE pdtion SET acc_shp = NULL WHERE order_num = '".$ord_fd[$i]['ord_num']."'";

					// $q_result = $sql->query($q_str);		
				}
/*
				$ship_date = $GLOBALS['order']->get_field_value('acc_rcv', '', $ord[$i],'pdtion');
				$ship_way = $GLOBALS['order']->get_field_value('acc_ship_way', '', $ord[$i],'pdtion');
				if( $ship_way == '')
				{
					$ship_date = '';
					for($s=0; $s<sizeof($ship_det); $s++)//若ship物料同驗收物料時,消除ship資料				
					{
						if($ship_date < $ship_det[$s]['ship_date'] )
						{
							$ship_date = $ship_det[$s]['ship_date'];
							$ship_way = $ship_det[$s]['ship_way'];
						}
					}	
						$q_str = "UPDATE pdtion SET acc_rcv ='$ship_date' acc_ship_way = '$ship_way' WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
						$q_result = $sql->query($q_str);	
				}
*/

						
			}// if ( $ord_fd[$i]['acc_cat'] == 0)
		}




		$mk = 0;	
	}

	return 1;
} // end func	




/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_po_cat() {

		$sql = $this->sql;
		$mk = 0;

				$q_str = "SELECT apb_det.id, ap.special
									FROM ap, apb, apb_det 
									WHERE ap.id = apb.po_id AND apb.rcv_num = apb_det.rcv_num";

				$q_result = $sql->query($q_str);		
				$rcv = array();
				while ($row = $sql->fetch($q_result)) {	
					$rcv[] = $row;
				}
				for($i=0; $i<sizeof($rcv); $i++)
				{
					if($rcv[$i]['special'] < 2)
					{
						$q_str = "UPDATE apb_det SET apb_det.po_cat = 0 WHERE id=".$rcv[$i]['id'] ;
					}else{
						$q_str = "UPDATE apb_det SET apb_det.po_cat = 1 WHERE id=".$rcv[$i]['id'] ;
					}
					$q_result = $sql->query($q_str);
				}
				
				
		return 1;
	} // end func	
*/


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_order()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_order() {

		$sql = $this->sql;
		$mk = 0;

		$q_str = "ALTER TABLE `s_order` ADD `quota_unit` VARCHAR( 20 ) NOT NULL ,
ADD `content` VARCHAR( 255 ) NOT NULL ,
ADD `des` VARCHAR( 255 ) NOT NULL ,
ADD `ship_quota` FLOAT( 6, 2 ) NOT NULL ; ";
									
				$q_result = $sql->query($q_str);		
				
				
		return 1;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_cost($ord_num,$mat_cat) {

		$sql = $this->sql;
		$mk = 0;
		$cury_d = $GLOBALS['cury_d'];
		
			if($mat_cat == 'l')
			{
				$q_str = "SELECT sum(apb_po_link.amount) as amount, ap.dm_way, ap.special, 
												 apb_po_link.po_id, ap.currency
									FROM apb_po_link, apb_det, apb, ap
									WHERE apb_det.id = apb_po_link.rcv_id AND apb_det.rcv_num = apb.rcv_num AND
												ap.id = apb.po_id AND
												apb_det.mat_code like 'F%' AND 
												apb_po_link.ord_num ='".$ord_num."'
									GROUP BY po_id";
				$q_result = $sql->query($q_str);	
				$cost_ttl = 0;	
				while ($row = $sql->fetch($q_result)) {	
					if(strstr($row['dm_way'],'before shipment') )
					{
						if($row['special'] == 2) $table = 'ap_special';
						if($row['special'] < 2) $table = 'ap_det';
						$where = ' id ='.$row['po_id'];
						$po_tmp = $GLOBALS['po']->get_det_field('amount,(po_qty*prics) as amt',$table,$where);
						if($po_tmp[0] == 0) $po_tmp[0] = $po_tmp[1];
						$row['amount'] = $po_tmp[0];
					}
					if(!isset($row['amount']))$row['amount'] = 0;

					$row['amount'] = $row['amount'] * $cury_d[$row['currency']]['USD'];
					$cost_ttl += $row['amount'];
				}
				$q_str = "UPDATE s_order SET rel_mat_cost = '".$cost_ttl."' WHERE order_num ='".$ord_num."'";

				$q_result = $sql->query($q_str);				
			}else{
				$q_str = "SELECT sum(apb_po_link.amount) as amount, ap.dm_way, ap.special, 
												 apb_po_link.po_id, ap.currency
									FROM apb_po_link, apb_det, ap, apb
									WHERE apb_det.id = apb_po_link.rcv_id AND apb_det.rcv_num = apb.rcv_num AND
												ap.id = apb.po_id AND
												apb_det.mat_code like 'A%' AND 
												apb_po_link.ord_num ='".$ord_num."'
									GROUP BY po_id";
											
				$q_result = $sql->query($q_str);	
				$cost_ttl = 0;	
				while ($row = $sql->fetch($q_result)) {	
					if(strstr($row['dm_way'],'before shipment') )
					{
						if($row['special'] == 2) $table = 'ap_special';
						if($row['special'] < 2) $table = 'ap_det';
						$where = ' id ='.$row['po_id'];
						$po_tmp = $GLOBALS['po']->get_det_field('amount,(po_qty*prics) as amt',$table,$where);
						if($po_tmp[0] == 0) $po_tmp[0] = $po_tmp[1];
						$row['amount'] = $po_tmp[0];
					}
					
					if(!isset($row['amount']))$row['amount'] = 0;
					$row['amount'] = $row['amount'] * $cury_d[$row['currency']]['USD'];
					
					$cost_ttl += $row['amount'];

				}
				$q_str = "UPDATE s_order SET rel_acc_cost = '".$cost_ttl."' WHERE order_num ='".$ord_num."'";
				$q_result = $sql->query($q_str);				
			}
				
				
		return $cost_ttl;
	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_bom_lots_cost($ord_num,$wi_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_bom_lots_cost($ord_num,$wi_id) {
		$row=array();
		$cost['bom']=0;
		$sql = $this->sql;
		$cury_d = $GLOBALS['cury_d'];
		$q_str="SELECT sum((ap_det.ap_qty * ap_det.prics)) as amount, ap.currency
						FROM ap, ap_det, bom_lots
						WHERE ap.ap_num = ap_det.ap_num AND bom_lots.id = ap_det.bom_id AND 
									ap.ap_num = bom_lots.ap_mark AND ap.special = 0 AND ap_det.mat_cat = 'l' AND
									bom_lots.dis_ver = 0 AND (ap_det.unit = ap_det.prc_unit  OR ap_det.prc_unit = '') AND
									bom_lots.wi_id = '".$wi_id."'
						GROUP BY ap.currency
						HAVING amount > 0";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			$cost['bom'] += ($row['amount'] * $cury_d[$row['currency']]['NTD']);			
		}

		$q_str="SELECT ap_det.ap_qty, ap_det.prics, ap.currency, ap_det.unit, ap_det.prc_unit
						FROM ap, ap_det, bom_lots
						WHERE ap.ap_num = ap_det.ap_num AND bom_lots.id = ap_det.bom_id AND 
									ap.ap_num = bom_lots.ap_mark AND ap.special = 0 AND ap_det.mat_cat = 'l' AND
									bom_lots.dis_ver = 0 AND (ap_det.unit <> ap_det.prc_unit  AND ap_det.prc_unit <> '') AND
									bom_lots.wi_id = '".$wi_id."'";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			$row['prics'] = change_unit_price($row['prc_unit'],$row['unit'],$row['prics']);
			$cost['bom'] += ($row['ap_qty'] * $row['prics'] * $cury_d[$row['currency']]['NTD']);			
		}
		
		$q_str="SELECT ap_special.prics, ap_special.prc_unit,
									 ap.currency, bom_lots.qty as bom_qty, lots_use.unit
						FROM   ap_special, ap, bom_lots, lots_use
						WHERE  ap_special.ap_num = ap.ap_num AND ap.ap_num = bom_lots.ap_mark AND
									 bom_lots.lots_used_id = lots_use.id AND lots_use.lots_code = ap_special.mat_code AND
									 ap_special.color = bom_lots.color AND bom_lots.dis_ver = 0 AND
									 ap_special.mat_cat = 'l' AND ap_special.ord_num = '".$ord_num."'";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			$row['prics'] = change_unit_price($row['prc_unit'],$row['unit'],$row['prics']);
			$bom_qty = explode(',',$row['bom_qty']);
			$row['bom_qty'] = 0;
			for($i=0; $i<sizeof($bom_qty); $i++)$row['bom_qty'] +=$bom_qty[$i];
			$cost['bom'] += ($row['bom_qty'] * $row['prics'] * $cury_d[$row['currency']]['NTD']);			
		}		

		return $cost;
		
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_bom_acc_cost($ord_num,$wi_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_bom_acc_cost($ord_num,$wi_id) {
		$row=array();
		$sql = $this->sql;
		$cost['bom']=0;
		$cury_d = $GLOBALS['cury_d'];
		$q_str="SELECT sum((ap_det.ap_qty * ap_det.prics)) as amount, ap.currency
						FROM ap, ap_det, bom_acc
						WHERE ap.ap_num = ap_det.ap_num AND bom_acc.id = ap_det.bom_id AND 
									ap.ap_num = bom_acc.ap_mark AND ap.special = 0 AND ap_det.mat_cat = 'a' AND
									bom_acc.dis_ver = 0 AND (ap_det.unit = ap_det.prc_unit  OR ap_det.prc_unit = '') AND
									bom_acc.wi_id = '".$wi_id."'
						GROUP BY ap.currency
						HAVING amount > 0";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			$cost['bom'] += ($row['amount'] * $cury_d[$row['currency']]['NTD']);			
		}
		

		$q_str="SELECT ap_det.ap_qty, ap_det.prics, ap.currency, ap_det.unit, ap_det.prc_unit
						FROM ap, ap_det, bom_acc
						WHERE ap.ap_num = ap_det.ap_num AND bom_acc.id = ap_det.bom_id AND 
									ap.ap_num = bom_acc.ap_mark AND ap.special = 0 AND ap_det.mat_cat = 'a' AND
									bom_acc.dis_ver = 0 AND (ap_det.unit <> ap_det.prc_unit  AND ap_det.prc_unit <> '') AND
									bom_acc.wi_id = '".$wi_id."'";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			$row['prics'] = change_unit_price($row['prc_unit'],$row['unit'],$row['prics']);
			$cost['bom'] += ($row['ap_qty'] * $row['prics'] * $cury_d[$row['currency']]['NTD']);			
		}
		
		$q_str="SELECT ap_special.prics, ap_special.prc_unit,
									 ap.currency, bom_acc.qty as bom_qty, acc_use.unit
						FROM   ap_special, ap, bom_acc, acc_use
						WHERE  ap_special.ap_num = ap.ap_num AND ap.ap_num = bom_acc.ap_mark AND
									 bom_acc.acc_used_id = acc_use.id AND acc_use.acc_code = ap_special.mat_code AND
									 bom_acc.dis_ver = 0 AND
									 ap_special.mat_cat = 'a' AND ap_special.ord_num = '".$ord_num."'";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		while($row = $sql->fetch($q_result))
		{
			$row['prics'] = change_unit_price($row['prc_unit'],$row['unit'],$row['prics']);
			$bom_qty = explode(',',$row['bom_qty']);
			$row['bom_qty'] = 0;
			for($i=0; $i<sizeof($bom_qty); $i++)$row['bom_qty'] +=$bom_qty[$i];
			$cost['bom'] += ($row['bom_qty'] * $row['prics'] * $cury_d[$row['currency']]['NTD']);			
		}		
		return $cost;
		
	} // end func	

function search2($q_str) {
		
		$sql = $this->sql;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while($row = $sql->fetch($q_result)){
			$e['supl_s_name'][$i]=$row['supl_s_name'];
			$e['uni_no'][$i]=$row['uni_no'];
			$e['inv_num'][$i]=$row['inv_num'];
			$e['rcv_sub_date'][$i]=$row['rcv_sub_date'];
			$e['inv_price'][$i]=$row['inv_price'];
			$e['tax_rate'][$i]=$row['tax_rate'];
			$e['discount'][$i]=$row['discount'];
			$e['off_duty'][$i]=$row['off_duty'];
			$e['ship'][$i]=$row['ship'];
			$e['dept'][$i]=$row['dept'];
			$e['mat_code'][$i]=$row['mat_code'];//edit mmmmmmmmmmmmmmmmmmmmmmmmmmmmmm
			$e['rcv_sub_date'][$i]=$row['rcv_sub_date'];
			//$e['g'][$i]=$row[6];
			//$e['h'][$i]=$row[7];
			//$e['i'][$i]=$row[8];
			$i++;
		}
		return isset($e) ? $e : 0; //是否無資料

	}


function get_rcv_num($q_str) { //add #################t
    $sql = $this->sql;

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot append order");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $i=0;
    while($row = $sql->fetch($q_result)){
        $e['rcv_num'][$i]=$row['rcv_num'];
        $e['status'][$i]=$row['status'];
        $e['inv_num'][$i]=$row['inv_num'];
        $i++;
    }
    
    return isset($e) ? $e : 0; //是否無資料

}

function get_apb2($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb_det`.*, `apb`.`inv_num` as `apb_inv_num`, `apb`.`rcv_date`, `apb`.`discount` as `apb_discount`, `apb`.`inv_price`, `apb`.`sup_no`, `apb`.`inv_date`,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`, ap.currency as po_currency
	FROM `apb`, `apb_det`,`supl`, `ap`
	WHERE `apb`.`rcv_num` = `apb_det`.`rcv_num` AND `apb`.`sup_no` = `supl`.`vndr_no` AND `apb_det`.`rcv_num` = '".$num."' and ap.ap_num = apb_det.ap_num";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$k=0;
	while ($det_row = $sql->fetch($q_result)) {
		# 合併的採購單 apb_po_link
		$q_str = "SELECT `id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`rate`,`currency`,`amount`,`ord_num`
					from apb_po_link
					WHERE `rcv_id` = '".$det_row['id']."' ";
		
		$q_res = $sql->query($q_str);
		$order = array();
		
		while ($link_row = $sql->fetch($q_res)) {
			$op['apb_det'][$k] = $det_row;
			$op['apb_det'][$k]['link_qty'] = $link_row['link_qty'];
			$op['apb_det'][$k]['rate'] = $link_row['rate'];
			$op['apb_det'][$k]['currency'] = $link_row['currency'];
			//$op['apb_det'][$k]['amount'] = $link_row['amount'];
			$op['apb_det'][$k]['ord_num'] = $link_row['ord_num'];
			$order[$k]['ord_num'] = $link_row['ord_num']; # 組合訂單檔
			$order[$k]['po_link_qty'] = $link_row['link_qty'];
			
			# 取po_unit
			$q_str = "SELECT `po_unit`
					FROM ap_det
					WHERE `id` = '".$link_row['po_id']."' ";
			
			$unit_result = $sql->query($q_str);
			$unit_row = $sql->fetch($unit_result);
			$op['apb_det'][$k]['po_unit'] = $unit_row['po_unit'];
			
			$k++;
		}
	}

	return $op;
} // end func

function get_apb2pdf($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`,`ap`.`ann_num`
	FROM `apb`,`supl`,`ap`
	WHERE `apb`.`po_id`=`ap`.`id` and `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$num."' ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# PO 類型，組合 sql 用
	$q_str = "
	SELECT `special`,`ap_num`,`po_num`,`po_apv_date`,`toler`,`finl_dist`,`dm_way`
	FROM `ap` 
	WHERE `id` = '".$op['apb']['po_id']."'";
	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	$special = $row['special'];
	$op['apb']['po_num'] = $row['po_num'];
	$op['apb']['ap_num'] = $row['ap_num'];
	$op['apb']['po_apv_date'] = $row['po_apv_date'];
	$op['apb']['toler'] = $row['toler'];
	$toler = explode('|',$row['toler']);
	$op['apb']['toleri'] = $toler[0];
	$op['apb']['tolern'] = $toler[1];
	$op['apb']['finl_dist'] = $row['finl_dist'];
	$op['apb']['dm_way'] = $row['dm_way'];
	
	
	# 物料檔 apb_det
	$apb_qty = array();
	
	# 物料次檔 apb_det
	$q_str = "SELECT * FROM `apb_det` WHERE `apb_det`.`rcv_num` = '".$num."'";
	$q_result = $sql->query($q_str);
	$i=0;
	$sum_foc=0;
	$order = array();
	$ord_num = array();
	while ($row = $sql->fetch($q_result)) {
		$apb_qty[$row['po_id']] += $row['qty'];
		$sum_foc += $row['foc'];
	
		# 合併的採購單 apb_po_link
		$q_str = "
		SELECT 
		`id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`currency`,`amount`,`ord_num`
		FROM `apb_po_link`  
		WHERE `rcv_id` = '".$row['id']."' ";
		$q_res = $sql->query($q_str);
		
		$k = 0;
		while ($rows = $sql->fetch($q_res)) {
			$order[$k]['ord_num'] = $rows['ord_num']; # 組合訂單檔
			$order[$k]['po_link_qty'] = $rows['link_qty'];
			$ord_num[]=$rows['ord_num'];
			
			
			//收料次數
			$p_id=explode("|",$rows['po_id']);
			$q_str1="SELECT count(*) FROM apb_po_link WHERE po_id='$p_id[0]'";
			if (!$q_result1 = $sql->query($q_str1)) {
				$this->msg->add("Error ! Database can't access! table:apb_po_link");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$row11 = $sql->fetch($q_result1);
			$order[$k]['receive_time'] = $row11[0];
			$k++;
		}

		$row['ord_num'] = $order;
		
		# PO 數量、單位
		$q_str = "
			SELECT `po_unit`,`po_eta`,`po_qty`
			FROM `".( $special == 2 ? 'ap_special' : 'ap_det' )."` 
			WHERE `po_spare` = '".$row['po_id']."' ";
		$q_res = $sql->query($q_str);
		
		while ($rows = $sql->fetch($q_res)) {
			$row['unit'] = $rows['po_unit'];
			$row['po_eta'] = $rows['po_eta'];
			$row['po_qty'] += $rows['po_qty'];
		}
		
		# APB 數量
		$row['apb_qty'] = $apb_qty[$row['po_id']];
		
		# Remain 數量
		//$row['un_qty'] = $row['rcv_qty'] - $row['apb_qty'];
		
		$row['i'] = $i++;
		$op['apb_det'][]=$row;
		
		#合計用資料
		$op['sum']['sum_po_qty']+=$row['po_qty'];
		$op['sum']['sum_apb_qty']+=$row['apb_qty'];
		$op['sum']['sum_foc']=$sum_foc;
		$op['sum']['amount']+=$row['uprice']*$row['apb_qty'];
		
	}
	
	#apb log記錄
	$q_str="SELECT apb_log.des FROM `apb_log`  WHERE `rcv_num` = '".$num."' ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($apb_log_row = $sql->fetch($q_result)) {
		$op['apb_log'][]=$apb_log_row;
	}
	
	$op['ord_num']=$ord_num;
	return $op;
} // end func



function apb_local($inv_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*, sum(apb.inv_price) as sum_inv_price,sum(apb.vat) as sum_vat,sum(apb.discount) as sum_discount,sum(apb.off_duty) as sum_off_duty,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `supl` , `apb`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`inv_num` = '".$inv_num."' and (`apb`.`payment` not like '%before%' or `apb`.`status` = 4) group by apb.inv_num";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;
	$num = $row['rcv_num'];
	
	#主副料別
	$q_str = "
	SELECT `id`,`mat_code`,`ap_num`
	FROM `apb_det` 
	WHERE `rcv_num` = '".$num."'";
	
	$q_result = $sql->query($q_str);
	$mat_row = $sql->fetch($q_result);
	
	$op['apb']['mat_code'] = $mat_row['mat_code'];

	# PO 類型，組合 sql 用
	$q_str = "
	SELECT `dept`,`special`,`ap_num`,`po_num`,`po_apv_date`,`toler`,`finl_dist`,`dm_way`
	FROM `ap` 
	WHERE `ap_num` = '".$mat_row['ap_num']."'";
	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	$special = $row['special'];
	$op['apb']['po_num'] = $row['po_num'];
	$op['apb']['ap_num'] = $row['ap_num'];
	$op['apb']['po_apv_date'] = $row['po_apv_date'];
	$op['apb']['toler'] = $row['toler'];
	$op['apb']['finl_dist'] = $row['finl_dist'];
	$op['apb']['dm_way'] = $row['dm_way'];
	$op['apb']['po_dept'] = $row['dept'];

	
	/* # 物料檔 apb_det
	$apb_qty = array();
	
	# 物料次檔 apb_det
	$q_str = "SELECT * FROM `apb_det` WHERE `apb_det`.`rcv_num` = '".$num."'";
	$q_result = $sql->query($q_str);
	$i=0;
	while ($row = $sql->fetch($q_result)) {
		$apb_qty[$row['po_id']] += $row['qty'];
	
		# 合併的採購單 apb_po_link
		$q_str = "
		SELECT 
		`id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`currency`,`amount`,`ord_num`
		FROM `apb_po_link`  
		WHERE `rcv_id` = '".$row['id']."' ";
		$q_res = $sql->query($q_str);
		$order = array();
		$po_id = array();
		$k = 0;
		while ($rows = $sql->fetch($q_res)) {
			$order[$k]['ord_num'] = $rows['ord_num']; # 組合訂單檔
			$order[$k]['po_link_qty'] = $rows['link_qty'];
			$po_id[] = $rows['po_id'];
			$k++;
		}

		$row['ord_num'] = $order;
		
		# PO 單位
		$q_str = "
			SELECT `po_unit`
			FROM `".( $special == 2 ? 'ap_special' : 'ap_det' )."` 
			WHERE `id` = '".$po_id[0]."' ";
		
		$q_res = $sql->query($q_str);
		$po_unit = '';
		while ($rowss = $sql->fetch($q_res)) {
			$po_unit = $rowss['po_unit'];
		}
		$row['unit'] = $po_unit;
		
		# APB 數量
		$row['apb_qty'] = $apb_qty[$row['po_id']];
		
		# Remain 數量
		//$row['un_qty'] = $row['rcv_qty'] - $row['apb_qty'];
		
		$row['i'] = $i++;
		$op['apb_det'][]=$row;
	} */

	//驗收人
	$q_str="SELECT receive.rcv_user as receive_user FROM apb,receive,receive_det  WHERE apb.rcv_num='$num' and apb.po_id=receive.po_id and receive.rcv_num =receive_det.rcv_num ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($receive_row = $sql->fetch($q_result)) {
		$op['apb']['receive_user']=$receive_row['receive_user'];
	}
	
	#7/26 財務部要求 "部門別"及"廠別部門"要用order的部門及工廠, 不要用驗收(驗收人,部門)或apb(請款人,部門),因為資料不準
	$q_str="SELECT s_order.dept, s_order.factory
			FROM apb_po_link,s_order  
			WHERE apb_po_link.rcv_id=".$mat_row['id']." and apb_po_link.ord_num = s_order.order_num limit 1";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$ord_red = $sql->fetch($q_result);
	$op['apb']['ord_dept']=$ord_red['dept'];
	$op['apb']['ord_fty']=$ord_red['factory'];
	
	return $op;
} // end func



function apb_tiptop_domestic($rcv,$begin_date,$end_date) {

	$sql = $this->sql;

	foreach($rcv['inv_num'] as $key => $val){
	
		# 主檔 
		$q_str = "
		SELECT DISTINCT 
		`apb_det`.*, `apb`.*, sum(`apb_det`.`qty`) as `sum_qty`,
		`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name` , `apb_det`.`po_id` as `pid`,`apb_det`.`id` as `apb_det_id`
		FROM `apb`,`apb_det`,`supl`
		WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`inv_num` = '".$val."' AND 
        `apb`.`rcv_num` = `apb_det`.`rcv_num` AND (apb.rcv_date between '$begin_date' and '$end_date') GROUP BY `apb`.`inv_num` 
        ;";

        // echo $q_str.'<br>';

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		// if (!$row = $sql->fetch($q_result)) {
			// $this->msg->add("Error ! Can't find this record!");
			// return false;    
		// }
        
		while ( $row = $sql->fetch($q_result)) {
			$q_str = "select distinct  sum(`apb`.`inv_price`) as `sum_uprice`, sum(`apb`.`vat`) as `sum_vat`
					  from apb
					  where apb.inv_num='$val' and (apb.rcv_date between '$begin_date' and '$end_date')
					  group by apb.inv_num ";
			// echo $q_str.'<br>';
			if (!$s_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
            
			$s_row = $sql->fetch($s_result);
			$row['sum_uprice'] = $s_row['sum_uprice'];
			$row['sum_vat'] = $s_row['sum_vat'];
			
			# PO 類型，組合 sql 用
			$q_str = "SELECT `dm_way`,`finl_dist`,`arv_area` FROM `ap` WHERE `ap_num` = '".$row['ap_num']."'";
            // echo $q_str.'<br>';
			$q_results = $sql->query($q_str);
			$rows = $sql->fetch($q_results);
			$row['dm_way'] = $rows['dm_way'];
			$row['ship'] = $rows['finl_dist']!=''?$rows['finl_dist']:$rows['arv_area'];
			
			#主副料別
			// substr($row['mat_code'],0,1)=='A'?
			// $q_str = "SELECT `acc_name`,`vendor1`	FROM `acc` WHERE `acc_code` = '".$row['mat_code']."'"	: 
			// $q_str = "SELECT `lots_name`,`vendor1` FROM `lots` WHERE `lots_code` = '".$row['mat_code']."'"	;
			// $q_results = $sql->query($q_str);
			// $mat_row = $sql->fetch($q_results);
			// $row['mat_name'] = $mat_row[0];
			
			#7/26 財務部要求 "部門別"及"廠別部門"要用order的部門及工廠, 不要用驗收(驗收人,部門)或apb(請款人,部門),因為資料不準
			$q_str="SELECT s_order.dept, s_order.factory
					FROM apb_po_link,s_order  
					WHERE apb_po_link.rcv_id=".$row['apb_det_id']." and apb_po_link.ord_num = s_order.order_num limit 1";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$ord_red = $sql->fetch($q_result);
			$row['ord_dept']=$ord_red['dept'];
			$row['ord_fty']=$ord_red['factory'];
			
			$mo[]=$row;
		}
	}
	// print_r($mo);
	return $mo;
} // end func




function apb_tiptop_foreign($rcv) {
	$sql = $this->sql;
	$o_ary = array();
	$det_id = array();
	
	foreach($rcv['rcv_num'] as $key => $val){
		
		# 主檔
		$q_str = "
		SELECT 
		`apb`.*, `apb_det`.*, sum(`apb`.`inv_price`) as `sum_inv_price`, sum(`apb`.`vat`) as `sum_vat`, sum(`apb_det`.`qty`) as `sum_qty`, sum(`apb_det`.`qty`*`apb_det`.`uprices`) as `sum_uprice`,
		`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name` , `apb`.`po_id` as `pid`,`ap`.`po_num`
		FROM `apb`,`apb_det`,`supl`,`ap`
		WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$val."' AND `apb`.`rcv_num` = `apb_det`.`rcv_num` AND `apb`.`po_id` = `ap`.`id` GROUP BY `apb`.`rcv_num` ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord2dept = ''; //取訂單的部門及工廠用
		while ( $row = $sql->fetch($q_result)) {
			$ord = '';	//組合訂單號碼用
			$o_ary = '';
			# PO 類型，組合 sql 用
			$q_str = "SELECT `dm_way`	FROM `ap` WHERE `id` = '".$row['pid']."'";
			$q_results = $sql->query($q_str);
			$rows = $sql->fetch($q_results);
			$row['dm_way'] = $rows['dm_way'];

			$mo[$key]=$row;
			
			# 訂單號碼 apb_po_link
			$det_str = "SELECT apb_det.id FROM apb_det WHERE rcv_num = '".$val."'";
			$det_res = $sql->query($det_str);
			while ($det_rows = $sql->fetch($det_res)){
				$link_str = "SELECT ord_num FROM apb_po_link WHERE rcv_id = '".$det_rows['id']."'";
				$link_res = $sql->query($link_str);
				while ($link_rows = $sql->fetch($link_res)){
					$o_ary[] = $link_rows['ord_num'];
				}
			}
			
			$o_ary = array_unique($o_ary);
			foreach($o_ary as $k2){
				$ord .= $k2.",";
			}
			$ord2dept = $k2;
			$ord = substr($ord,0,-1);
			$mo[$key]['ord_num'] = $ord;
			
			#tiptop 國外 匯出 暫時不改,willson說沒有用到
			/* #7/26 財務部要求 "部門別"及"廠別部門"要用order的部門及工廠, 不要用驗收(驗收人,部門)或apb(請款人,部門),因為資料不準
			$q_str="SELECT s_order.dept, s_order.factory
					FROM s_order  
					WHERE s_order.order_num = '".$ord2dept."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$ord_red = $sql->fetch($q_result);
			$mo[$key]['ord_dept']=$ord_red['dept'];
			$mo[$key]['ord_fty']=$ord_red['factory']; */
			
			
		}
		
	}
	return $mo;
} // end func




function get_apb_dept($dept="note"){
	switch($dept){
		Default;
			return $dept;
		break;
		
		case "LY";
			return "AEJ0000";
		break;
		
		case "HJ";
			return "AEF0000";
		break;
		
		case "PO";
			return "AEJ0000";
		break;
		
		case "CF";
			return "AEE0000";
		break;
		
		case "KA";
			return "AHF0000";
		break;
		
		case "KB";
			return "AHF0000";
		break;
		
		case "DA";
			return "AHF1000";
		break;
		
		case "PM";
			return "AYB0000";
		break;
		
		case "J1";
			return "AYB0000";
		break;
	}
}

function get_dept_code($dept="note"){
	switch($dept){
		Default;
			return $dept;
		break;
		
		case "B";
			return "KB";
		break;
		
		case "A";
			return "KA";
		break;
		
		case "D";
			return "DA";
		break;
		
		case "J";	#特殊案例，目前有　AP13-0987 / 1010 / 0991 / 0986  / 0985　因為都是J1部門，但要歸帳到 KB
			return "KB";
		break;
	}
}

function get_apb_dm_way($day="",$date=""){

	$py = $pm = $pd = '';

	$s_date = explode('-',$date);

	$sm = round($day/30);
	$sd = $day % 30 ;
	
	if ( $s_date[1] + $sm > 12 ) {
		$py = $s_date[0] + 1 ;
		$pm = ( $s_date[1] + $sm ) - 12 ;
	} else {
		$py = $s_date[0];
		$pm = $s_date[1] + $sm ;
	}
	
	switch($sd){
		case "0";
		$pd = "10";
		break;
		
		case "10";
		$pd = "20";
		break;
		
		case "20";
		$pd = "30";
		break;
	}

	return $py.'/'.$pm.'/'.$pd;

}

function get_apb_pay($pay="note"){
    
    // $mk = re_dm_way( $pay );
    
    // if ( $mk[0] == 2 )
        // $pay = "30% TT before shipment, 70% TT after shipment";

	switch($pay){
		Default;
			return $pay;
		break;
		
		case "月結30天";
			return "CP0001";
		break;
				
		case "月結60天";
			return "CP0002";
		break;
				
		case "mmmmm";
			return "CP0003";
		break;
				
		case "月結80天";
			return "CP0004";
		break;
				
		case "mmmm";
			return "CP0005";
		break;
				
		case "mmmm";
			return "CP0006";
		break;
				
		case "mmmm";
			return "CP0007";
		break;
				
		case "mmmmm";
			return "CP0008";
		break;
		
		case "L/C at sight";
			return "CP0009";
		break;
				
		case "COD";
			return "CP0010";
		break;
				
		case "T/T before shipment";
			return "CP0011";
		break;
				
		case "Check";
			return "CP0012";
		break;
				
		case "Check before shipment";
			return "CP0013";
		break;
				
		case "T/T after shipment";
			return "CP0014";
		break;
								
		case "30% TT before shipment, 70% TT after shipment";
			return "CP0014";
		break;
				
		case "Bank Draft";
			return "CP0015";
		break;
						
		case "D/D";
			return "CP0015";
		break;
								
		case "D/D befort shipment";
			return "CP0015";
		break;
		
		case "mmmm";
			return "CP0016";
		break;
		
		case "月結45天";
			return "CP0017";
		break;
	}
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_rcvd($sup_no)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_rcvd($sup_no='',$payment='') {
	$sql = $this->sql;
	
	$where_str = '';
	if($sup_no) $where_str.=" and receive_det.sup_no = '".$sup_no."' ";
	if($payment) $where_str.=" and ap.dm_way like '%".$payment."%' ";
	# 驗收主檔
	$q_str = "SELECT distinct receive.ship_num, receive.rcv_num, receive.rcv_date
			  From receive, receive_det,ap
			  WHERE receive.rcv_num = receive_det.rcv_num and receive.status = 4 
			  and receive_det.ap_num = ap.ap_num and receive.tw_rcv = 0 and receive_det.apb_rmk = 0".$where_str." order by receive.ship_num desc";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	$count_i=0;
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
		# 驗收明細
		$q_str = "SELECT distinct receive_det.*
				  From receive_det,ap
				  WHERE receive_det.rcv_num = '".$row['rcv_num']."'".$where_str." and receive_det.ap_num = ap.ap_num and receive_det.apb_rmk = 0 
						order by receive_det.id desc";

		if (!$det_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$p=0;
		$amt = 0; //計算金額
		
		while ($det_row = $sql->fetch($det_result)) {
			$order = array();
			$arr_ap_num[] = $det_row['ap_num'];
			$op['rcv'][$r]['rcv_det'][$p] = $det_row;
						
			# start 取得訂單號碼
			$p_id = explode('|',$op['rcv'][$r]['rcv_det'][$p]['po_id']);
			for ($s=0;$s<sizeof($p_id);$s++){
				$q_str = "select rcv_po_link.ord_num 
						from rcv_po_link 
						where po_id='".$p_id[$s]."' and rcv_id=".$det_row['id'];
				
				$ord_result = $sql->query($q_str);
				$ord_row = $sql->fetch($ord_result);
				$order[$s] = $ord_row['ord_num'];
			}
						
			$order = array_unique($order);
			$ord = array();
			foreach($order as $keyS)
				$ord[] = $keyS;
			$op['rcv'][$r]['rcv_det'][$p]['orders'] = $ord;
			# end 取訂單號碼
			
			# start 取 幣別、Tolerance、單價、匯率、採購數量
			
			$op['rcv'][$r]['rcv_det'][$p]['po'] = $this->get_det_field("ap_num,currency,toler,special,po_num","ap","ap_num='".$op['rcv'][$r]['rcv_det'][$p]['ap_num']."'");
			$toler = explode("|",$op['rcv'][$r]['rcv_det'][$p]['po']['toler']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] = $toler[0];
			$op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] = $toler[1];
			$op['rcv'][$r]['rcv_det'][$p]['po']['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det';
			$po_det_row = $this->get_det_field("unit,po_unit,prc_unit,prics,po_spare",$tbl,"po_spare='".$op['rcv'][$r]['rcv_det'][$p]['po_id']."'");
			#單價
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $po_det_row['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['unit'] = $po_det_row['unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = $po_det_row['po_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'] = $po_det_row['prc_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_spare'] = $po_det_row['po_spare'];
			$TODAY = date('Y-m-d');
			//if($op['rcv'][$r]['rcv_det'][$p]['po']['currency'] <> 'NTD'){
			//	$rate = $GLOBALS['rate']->get_rate($op['rcv'][$r]['rcv_det'][$p]['po']['currency'], $TODAY);
			
			$rate = 1;
			
			$op['rcv'][$r]['rcv_det'][$p]['po']['rate'] = $rate;
			#台幣
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $rate * $op['rcv'][$r]['rcv_det'][$p]['po']['prics'];
			//$op['rcv'][$r]['rcv_det'][$p]['po']['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['rcv'][$r]['rcv_det'][$p]['po']['currency'],'PHP_currency'.$op['rcv'][$r]['rcv_det'][$p]['rcv_num'].$p,'select',"rate('".$op['rcv'][$r]['rcv_det'][$p]['rcv_num']."',".$p.",".$r.")"); //幣別下拉式
			$op['rcv'][$r]['rcv_det'][$p]['po']['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['rcv'][$r]['rcv_det'][$p]['po']['currency'],'PHP_currency'.$op['rcv'][$r]['rcv_det'][$p]['rcv_num'].$p,'select',"cal_amt('".$op['rcv'][$r]['rcv_det'][$p]['rcv_num']."')"); //幣別下拉式
			$op['rcv'][$r]['rcv_det'][$p]['po']['i']=$p;
			//$op['rcv'][$r]['rcv_det'][$p]['r']=$count_i;
			
			$po_qty_row = $this->get_det_field("sum(po_qty) as qty",$tbl,"po_spare='".$op['rcv'][$r]['rcv_det'][$p]['po_id']."' group by po_spare");
			$op['rcv'][$r]['rcv_det'][$p]['po']['qty'] = $po_qty_row['qty'];
			# end 取 幣別、Tolerance、單價、匯率、採購數量
			
			//$op['b_a_flag'] = 0;	//判斷是不是 30%貨前,70%貨後 的70%貨後
			if($payment == "T/T before shipment"){
				$amt += $op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
				$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'],2,'','');
			}elseif($payment == "T/T after shipment" or $payment == "L/C at sight"){
				#計算 tolerance
				$chk_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
				$min_qty = (1 - $op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
				if($op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "pc" or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "K pcs")
					$chk_qty = ceil($chk_qty);
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $chk_qty;
				#單價轉換
				$ORI_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'],$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
				$op['rcv'][$r]['rcv_det'][$p]['po']['ORI_price'] = $ORI_price;
				
				//$rmk_qty = $this->check_qty($det_row['po_id']);
				//$po_id_qty[$det_row['po_id']] += $rmk_qty + $det_row['qty'];
				$apb_row = $this->get_det_field("sum(qty) as qty","apb_det","ap_num='".$det_row['ap_num']."' and po_id='".$det_row['po_id']."'");
				
				//$this->get_det_field("sum(qty) as qty","apb_det","a");
				if ( $apb_row['qty'] < $chk_qty ){
					$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = ($apb_row['qty']+$det_row['qty'] > $chk_qty ? $chk_qty-$apb_row['qty'] : $det_row['qty']);
					//$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($NTD_price * $op['rcv'][$r]['rcv_det'][$p]['qty'],2,'','');
					$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($ORI_price * $op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
				}else{
					//$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $chk_qty - $po_id_qty[$det_row['po_id']] + $det_row['qty'];
					$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = 0;
					//if($op['rcv'][$r]['rcv_det'][$p]['apb_qty'] < 0) $op['rcv'][$r]['rcv_det'][$p]['apb_qty']=0;
					//$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($NTD_price * $chk_qty,2,'','');
					$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($ORI_price * $op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
				}
				
				#判斷是否大於最小toler
				/* $chk_rcv = $this->get_po_link($det_row['po_id'],"receive_det");
				if($chk_rcv['qty'] < $min_qty){
					$op['rcv'][$r]['rcv_det'][$p]['chk_pay'] = 0;
					$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = 0;
				}else{
					$op['rcv'][$r]['rcv_det'][$p]['chk_pay'] = 1;
				} */
				
				$amt += $op['rcv'][$r]['rcv_det'][$p]['po']['price'];
			}else{
				$op['b_a_flag'] = "1";
				$where = "po_id = '".$op['rcv'][$r]['rcv_det'][$p]['po']['po_spare']."' and ap_num = '".$op['rcv'][$r]['rcv_det'][$p]['po']['ap_num']."'";
				$qty_row = $this->get_det_field("qty","apb_det",$where);
				$op['rcv'][$r]['rcv_det'][$p]['before_qty'] = $qty_row['qty'];
				#單價轉換
				$NTD_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'],$op['rcv'][$r]['rcv_det'][$p]['po']['uprice']);
				$op['rcv'][$r]['rcv_det'][$p]['po']['NTD_price'] = $NTD_price;
				#判斷是否超過tolerance
				$chk_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
				if ( $op['rcv'][$r]['rcv_det'][$p]['qty'] <= $chk_qty ){
					$op['rcv'][$r]['rcv_det'][$p]['after_qty'] = $op['rcv'][$r]['rcv_det'][$p]['qty'] - $op['rcv'][$r]['rcv_det'][$p]['before_qty'];
				}else{
					$op['rcv'][$r]['rcv_det'][$p]['after_qty'] = $chk_qty - $op['rcv'][$r]['rcv_det'][$p]['before_qty'];
				}
				$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($NTD_price * $op['rcv'][$r]['rcv_det'][$p]['after_qty'],2,'','');
				if($op['rcv'][$r]['rcv_det'][$p]['before_qty'] > 0){
					$amt += $op['rcv'][$r]['rcv_det'][$p]['po']['price'];
				}
			}
			
			$p++;
			$count_i++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}

	#額外費用
	$arr_ap_num = array_unique($arr_ap_num);
	foreach($arr_ap_num as $val){
		$q_str="SELECT * FROM `ap_oth_cost` 
				WHERE ap_num = '$val' and `apb_num` = ''";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while($row1 = $sql->fetch($q_result)) {
			$op['ap_oth_cost'][]=$row1;
		}
	}
	
	return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_tw_rcvd($sup_no)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_tw_rcvd($sup_no='',$payment='',$tw_rcv=0) {
	$sql = $this->sql;
	
	$where_str = '';
	if($sup_no) $where_str.=" and receive_det.sup_no = '".$sup_no."' ";
	if($payment) $where_str.=" and ap.dm_way like '%".$payment."%' ";

	# 驗收主檔
	$q_str = "SELECT distinct receive.ship_num, receive.rcv_num, receive.rcv_date
			  From receive, receive_det,ap
			  WHERE receive.rcv_num = receive_det.rcv_num and receive.status = 4 and receive.tw_rcv = ".$tw_rcv."
			  and receive_det.ap_num = ap.ap_num and receive_det.apb_rmk = 0 and receive.rcv_date > '2012-08-01'".$where_str." order by receive_det.rcv_num desc";
// echo $q_str;exit;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	$count_i=0;
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
		# 驗收明細
		$q_str = "SELECT distinct receive_det.*
				  From receive_det,ap
				  WHERE receive_det.rcv_num = '".$row['rcv_num']."'".$where_str." and receive_det.ap_num = ap.ap_num and receive_det.apb_rmk = 0 order by receive_det.id desc";

		if (!$det_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$p=0;
		$amt = 0; //計算金額
		
		while ($det_row = $sql->fetch($det_result)) {
			$order = array();
			$arr_ap_num[] = $det_row['ap_num'];
			$op['rcv'][$r]['rcv_det'][$p] = $det_row;
						
			# start 取得訂單號碼
			$p_id = explode('|',$op['rcv'][$r]['rcv_det'][$p]['po_id']);
			for ($s=0;$s<sizeof($p_id);$s++){
				$q_str = "select rcv_po_link.ord_num 
						from rcv_po_link 
						where po_id='".$p_id[$s]."' and rcv_id = ".$det_row['id'];
				
				$ord_result = $sql->query($q_str);
				$ord_row = $sql->fetch($ord_result);
				$order[$s] = $ord_row['ord_num'];
			}
						
			$order = array_unique($order);
			$ord = array();
			foreach($order as $keyS)
				$ord[] = $keyS;
			$op['rcv'][$r]['rcv_det'][$p]['orders'] = $ord;
			# end 取訂單號碼
			
			# start 取 幣別、Tolerance、單價、匯率、採購數量
			
			$op['rcv'][$r]['rcv_det'][$p]['po'] = $this->get_det_field("ap_num,currency,toler,special,po_num","ap","ap_num='".$op['rcv'][$r]['rcv_det'][$p]['ap_num']."'");
			$toler = explode("|",$op['rcv'][$r]['rcv_det'][$p]['po']['toler']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] = $toler[0];
			$op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] = $toler[1];
			$op['rcv'][$r]['rcv_det'][$p]['po']['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det';
			$po_det_row = $this->get_det_field("unit,po_unit,prc_unit,prics,po_spare",$tbl,"po_spare='".$op['rcv'][$r]['rcv_det'][$p]['po_id']."'");
			#單價
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $po_det_row['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['unit'] = $po_det_row['unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = $po_det_row['po_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'] = $po_det_row['prc_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_spare'] = $po_det_row['po_spare'];
			$TODAY = date('Y-m-d');
			$rate = 1;
			
			$op['rcv'][$r]['rcv_det'][$p]['po']['rate'] = $rate;
			#台幣
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = number_format($rate * $op['rcv'][$r]['rcv_det'][$p]['po']['prics'],5,'','');
			//$op['rcv'][$r]['rcv_det'][$p]['po']['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['rcv'][$r]['rcv_det'][$p]['po']['currency'],'PHP_currency'.$op['rcv'][$r]['rcv_det'][$p]['rcv_num'].$p,'select',"rate('".$op['rcv'][$r]['rcv_det'][$p]['rcv_num']."',".$p.",".$r.")"); //幣別下拉式
			$op['rcv'][$r]['rcv_det'][$p]['po']['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['rcv'][$r]['rcv_det'][$p]['po']['currency'],'PHP_currency'.$op['rcv'][$r]['rcv_det'][$p]['rcv_num'].$p,'select',""); //幣別下拉式
			$op['rcv'][$r]['rcv_det'][$p]['po']['i']=$p;
			//$op['rcv'][$r]['rcv_det'][$p]['r']=$count_i;
			
			$po_qty_row = $this->get_det_field("sum(po_qty) as qty",$tbl,"po_spare='".$op['rcv'][$r]['rcv_det'][$p]['po_id']."' group by po_spare");
			$op['rcv'][$r]['rcv_det'][$p]['po']['qty'] = $po_qty_row['qty'];
			# end 取 幣別、Tolerance、單價、匯率、採購數量
			
			
				#計算 tolerance
				$chk_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
				$min_qty = (1 - $op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
				if ( $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == 'pc' or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == 'K pcs' )
					$chk_qty = ceil($chk_qty);
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $chk_qty;
				#單價轉換
				$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = '' ? $po_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['unit'] : $po_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'];
				$ORI_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$po_unit,$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
				$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $op['rcv'][$r]['rcv_det'][$p]['po']['ORI_price'] = $ORI_price;
				
				$rmk_qty = $this->check_qty($det_row['po_id']);
				//$po_id_qty[$det_row['po_id']] += $rmk_qty + $det_row['qty'];
				
				$apb_row = $this->get_det_field("sum(qty) as qty","apb_det","ap_num='".$det_row['ap_num']."' and po_id='".$det_row['po_id']."'");
				
				if ( $apb_row['qty'] <= $chk_qty and $rmk_qty <= $chk_qty ){
					$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = ($det_row['qty']+$apb_row['qty']) > $chk_qty ? $chk_qty-$apb_row['qty'] : $det_row['qty'];
					//$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $det_row['qty'];
					$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($ORI_price * $op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
				}else{
					$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = 0;
					$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($ORI_price * $op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
				}
				
				$amt += $op['rcv'][$r]['rcv_det'][$p]['po']['price'];
			
			$p++;
			$count_i++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}

	#額外費用
	$arr_ap_num = array_unique($arr_ap_num);
	foreach($arr_ap_num as $val){
		$q_str="SELECT * FROM `ap_oth_cost` 
				WHERE ap_num = '$val' and `apb_num` = ''";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while($row1 = $sql->fetch($q_result)) {
			$op['ap_oth_cost'][]=$row1;
		}
	}
	
	return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_rcvd_after($sup_no)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_rcvd_after($sup_no='',$payment='') {
		$sql = $this->sql;

		$q_str = "select ap.*, sum(apb_oth_cost.cost) as cost
				  from ap,apb_oth_cost
				  where sup_code= '$sup_no' and dm_way like '$payment' and status=12 and apb_rmk= 1 and po_date >= '2011-06-01' and ap.ap_num=apb_oth_cost.ap_num
						group by apb_oth_cost.ap_num having cost < ap.po_total order by id desc";

		if (!$ap_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$p=0;
	while ($ap_row = $sql->fetch($ap_result)) {
		$num = $ap_row['ap_num'];
		//請購主檔
		$q_str = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, supl.usance, supl.dm_way as supl_dm_way, supl.cntc_phone, supl.cntc_addr, supl.supl_f_name, supl.cntc_person1, supl.id as supl_id , supl.uni_no
				FROM ap, supl 
				WHERE  ap.sup_code = supl.vndr_no AND ap_num='$num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}

		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!1");
			return false;
		}
		
		$toler = explode('|',$row['toler']);
		$row['toleri'] = $toler[0];
		$row['tolern'] = $toler[1];
		
		$op['ap'][$p]=$row;
		//$op['ap'][$p]['select_currency'] = $GLOBALS['arry2']->select($GLOBALS['CURRENCY'],$op['ap'][$p]['currency'],'PHP_currency'.$op['ap'][$p]['po_num'],'select',"rate()"); //幣別下拉式
		
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$op['ap'][$p]['po_user']);
		$op['ap'][$p]['po_user_id'] = $op['ap'][$p]['po_user'];
		if ($po_user['name'])$op['ap'][$p]['po_user'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$op['ap']['po_sub_user']);
		$op['ap'][$p]['po_sub_user_id'] = $op['ap'][$p]['po_sub_user'];
		if ($po_user['name'])$op['ap'][$p]['po_sub_user'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$op['ap'][$p]['po_cfm_user']);
		$op['ap'][$p]['po_cfm_user_id'] = $op['ap'][$p]['po_cfm_user'];
		if ($po_user['name'])$op['ap'][$p]['po_cfm_user'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$op['ap'][$p]['po_apv_user']);
		$op['ap'][$p]['po_apv_user_id'] = $op['ap'][$p]['po_apv_user'];
		if ($po_user['name'])$op['ap'][$p]['po_apv_user'] = $po_user['name'];
		$op['ap'][$p]['base_ck'] = 0;
		
		
		//請購主檔
		$q_str = "SELECT min(exceptional.status) as exc_status
		FROM exceptional 
		WHERE  po_num='".$op['ap'][$p]['po_num']."'
		GROUP BY po_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if ($row = $sql->fetch($q_result)) {
			$op['ap'][$p]['exc_check'] =1;
			$op['ap'][$p]['exc_status'] = $row['exc_status'];
		}else{
			$op['ap'][$p]['exc_check'] =0;
		}

		$amt = 0;
		$ttl_po_qty = $ttl_rcv_qty = 0;
		$op['ap'][$p]['ord_num'] = array();
		
		//請購明細 -- 主料		
		$q_str="SELECT ap_det.*,ap.toler , smpl_code as ord_num, lots_use.lots_code as mat_code, 
		lots_use.lots_name as mat_name, bom_lots.color, lots.price1, lots.comp as con1, 
		lots.specify as con2, lots.des as mile, lots.lots_name as mile_name, lots.width,
		lots.weight, lots.cons as finish, bom_lots.wi_id
		FROM `ap`,`ap_det`,`bom_lots`,`lots_use`,`lots`  
		WHERE `lots`.`lots_code` = `lots_use`.`lots_code` AND `lots_use`.`id` = `bom_lots`.`lots_used_id` AND `ap_det`.`bom_id`= `bom_lots`.`id` 
				AND `ap_det`.`mat_cat` = 'l' AND `ap`.`ap_num` = `ap_det`.`ap_num` AND `ap_det`.`ap_num` = '$num' AND ap_det.apb_rmk=1";

		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$i=0;
		$op['ap'][$p]['rcv_mk']=0;
		while ($row1 = $sql->fetch($q_result)) {
			$ord_mk =0;
			$ttl_po_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_det'][$i]=$row1;
			$op['ap'][$p]['ap_det'][$i]['i']=$i;
			if($op['ap'][$p]['ap_det'][$i]['amount'] == 0)$op['ap'][$p]['ap_det'][$i]['amount']=$op['ap'][$p]['ap_det'][$i]['po_qty'] * $op['ap'][$p]['ap_det'][$i]['prics'];
			if($op['ap'][$p]['ap_det'][$i]['rcv_qty'] > 0)$op['ap'][$p]['rcv_mk'] = 1;
			$amt += $op['ap'][$p]['ap_det'][$i]['amount'];
			if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];
			for($j=0; $j<sizeof($op['ap'][$p]['ord_num']); $j++)
			{
				if($op['ap'][$p]['ord_num'][$j] == $row1['ord_num'])
				{
					$ord_mk = 1;
					break;
				}
			}
			if($ord_mk == 0)$op['ap'][$p]['ord_num'][] = $row1['ord_num'];
			if($row1['base_qty'] > 0)$op['ap'][$p]['base_ck'] = 1;
			
			#APB用
			global $apb;
			// print_r($op['ap_det'][$i]);
			//$rcv_qty = $apb->get_apb_det($op['ap'][$p]['ap_det'][$i]['id']);
			//$op['ap'][$p]['ap_det'][$i]['apb_qty'] = $rcv_qty['qty'];
			$op['ap'][$p]['po_eta'] = $row1['po_eta'];
			
			$i++;
		}

		//請購明細 -- 副料
		$q_str="SELECT ap_det.*, smpl_code as ord_num, acc_use.acc_code as mat_code, 
		acc_use.acc_name as mat_name, bom_acc.color, acc.price1, acc.des as con1, 
		acc.specify as con2, acc.mile_code as mile, acc.mile_name, bom_acc.wi_id
		FROM `ap_det`, bom_acc,acc_use, acc  
		WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id AND bom_id=bom_acc.id AND mat_cat = 'a' AND `ap_num` = '$num' 
				AND ap_det.apb_rmk=1";
		
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row1 = $sql->fetch($q_result)) {
			$ord_mk = 0;
			$ttl_po_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_det'][$i]=$row1;
			$op['ap'][$p]['ap_det'][$i]['i']=$i;
			if($op['ap'][$p]['ap_det'][$i]['amount'] == 0)$op['ap'][$p]['ap_det'][$i]['amount']=$op['ap'][$p]['ap_det'][$i]['po_qty']*$op['ap'][$p]['ap_det'][$i]['prics'];
			if($op['ap'][$p]['ap_det'][$i]['rcv_qty'] > 0)$op['ap'][$p]['rcv_mk'] = 1;
			$amt += $op['ap'][$p]['ap_det'][$i]['amount'];
			if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];

			for($j=0; $j<sizeof($op['ap'][$p]['ord_num']); $j++)
			{
				if($op['ap'][$p]['ord_num'][$j] == $row1['ord_num'])
				{
					$ord_mk = 1;
					break;
				}
			}
			if($ord_mk == 0)$op['ap'][$p]['ord_num'][] = $row1['ord_num'];
			if($row1['base_qty'] > 0)$op['ap'][$p]['base_ck'] = 1;
			$i++;
			#APB用
			$op['ap'][$p]['po_eta'] = $row1['po_eta'];
		} 	
		
			
		//請購明細 -- 特殊請購 -- 主料
		$op['ap'][$p]['pp'] = 0;
		$q_str="SELECT ap_special.*, lots.price1	, lots.comp as con1, lots.specify as con2, 
						lots.lots_name as mat_name, lots.des as mile, lots.lots_name as mile_name
				FROM `ap_special`, `lots` 
				WHERE `lots`.`lots_code` = `mat_code` AND `ap_num` = '$num' AND ap_special.apb_rmk=1";
		
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$ord_mk = 0;
			$ttl_po_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_spec'][$i]=$row1;
			$op['ap'][$p]['pp'] = $row1['pp_mark'];
			$op['ap'][$p]['ap_spec'][$i]['i']=$i;
			if($op['ap'][$p]['ap_spec'][$i]['amount'] == 0)$op['ap'][$p]['ap_spec'][$i]['amount']=$op['ap'][$p]['ap_spec'][$i]['po_qty']*$op['ap'][$p]['ap_spec'][$i]['prics'];
			if($op['ap'][$p]['ap_spec'][$i]['rcv_qty'] > 0)$op['ap'][$p]['rcv_mk'] = 1;
			$amt += $op['ap'][$p]['ap_spec'][$i]['amount'];
			if(!$op['ap'][$p]['ap_spec'][$i]['prc_unit'])$op['ap'][$p]['ap_spec'][$i]['prc_unit'] = $op['ap'][$p]['ap_spec'][$i]['po_unit'];

			for($j=0; $j<sizeof($op['ap'][$p]['ord_num']); $j++)
			{
				if($op['ap'][$p]['ord_num'][$j] == $row1['ord_num'])
				{
					$ord_mk = 1;
					break;
				}
			}
			if($ord_mk == 0)$op['ap'][$p]['ord_num'][] = $row1['ord_num'];
			if($row1['base_qty'] > 0)$op['ap'][$p]['ap']['base_ck'] = 1;
			$i++;
			#APB用
			$op['ap'][$p]['po_eta'] = $row1['po_eta'];
		}

		//請購明細 -- 特殊請購 -- 副料
		$q_str="SELECT ap_special.*, acc.price1	, acc.des as con1, acc.specify as con2, 
		acc.acc_name as mat_name, acc.des as mile, acc.mile_name
		FROM `ap_special`, acc	WHERE acc.acc_code = mat_code AND `ap_num` = '$num' AND ap_special.apb_rmk=1";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		while ($row1 = $sql->fetch($q_result)) {
			$ord_mk = 0;
			$ttl_po_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_spec'][$i]=$row1;
			$op['ap'][$p]['ap_spec'][$i]['i']=$i;
			if($op['ap'][$p]['ap_spec'][$i]['amount'] == 0)$op['ap'][$p]['ap_spec'][$i]['amount']=$op['ap'][$p]['ap_spec'][$i]['po_qty']*$op['ap'][$p]['ap_spec'][$i]['prics'];
			if($op['ap'][$p]['ap_spec'][$i]['rcv_qty'] > 0)$op['ap'][$p]['rcv_mk'] = 1;
			$amt += $op['ap'][$p]['ap_spec'][$i]['amount'];
			if(!$op['ap'][$p]['ap_spec'][$i]['prc_unit'])$op['ap'][$p]['ap_spec'][$i]['prc_unit'] = $op['ap'][$p]['ap_spec'][$i]['po_unit'];

			for($j=0; $j<sizeof($op['ap'][$p]['ord_num']); $j++)
			{
				if($op['ap'][$p]['ord_num'][$j] == $row1['ord_num'])
				{
					$ord_mk = 1;
					break;
				}
			}
			if($ord_mk == 0)$op['ap'][$p]['ord_num'][] = $row1['ord_num'];
			if($row1['base_qty'] > 0)$op['ap'][$p]['base_ck'] = 1;
			$op['ap'][$p]['po_eta'] = $row1['po_eta'];
			$i++;
		}

		//Remark項目
		/* $op['ap'][$p]['apply_log'] = array();
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='remark'";
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
			$op['ap'][$p]['apply_log'][]=$row1;
		}

		//特殊採購原因(Remark)
		$op['ap'][$p]['apply_special'] = array ();		
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='special'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
			$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];			
			$op['ap'][$p]['apply_special'][]=$row1;
		} */			

		if(isset($op['ap'][$p]['ap_det'])) $op['ap'][$p]['ap_det']=$GLOBALS['po']->grout_ap($op['ap'][$p]['ap_det']);
		
		$ttl_rcv_qty=0;
		$ttl_rcv_price=0;
		
		# ap_det 匯率、台幣單價、ttl_rcv_qty
		for($ap_s=0;$ap_s<sizeof($op['ap'][$p]['ap_det']);$ap_s++){
			$TODAY = date('Y-m-d');
			$rate=1;
			//if($op['ap'][$p]['currency'] <> 'NTD') $rate = $GLOBALS['rate']->get_rate($op['ap'][$p]['currency'], $TODAY);
			$op['ap'][$p]['rate'] = $rate;
			$op['ap'][$p]['ap_det'][$ap_s]['uprice'] = $op['ap'][$p]['ap_det'][$ap_s]['prics'] * $rate;
			#toler最大數
			$toleri_qty = $op['ap'][$p]['ap_det'][$ap_s]['po_qty'] * (1+($op['ap'][$p]['toleri']*0.01));
			
			$rcv_qty=0;
			#取出 驗收資料 準備寫入apb_det用的
			$q_str = "select distinct receive.ship_num, receive_det.* 
						from receive, receive_det 
						where receive.rcv_num=receive_det.rcv_num and receive_det.po_id='".$op['ap'][$p]['ap_det'][$ap_s]['po_spare']."' and receive.tw_rcv = 0";
			
			if (!$rcv_det_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;
			}
			
			while ($rcv_det_row = $sql->fetch($rcv_det_result)) {
				$rcv_qty += $rcv_det_row['qty'];
				if($rcv_qty > $toleri_qty or $rcv_det_row['qty'] > $toleri_qty){
					$rcv_det_row['qty'] = $toleri_qty - $rcv_qty + $rcv_det_row['qty'];
					if ( $rcv_det_row['qty'] < 0 ) $rcv_det_row['qty']=0;
				}
				if($rcv_det_row['apb_rmk'] == 0){
					$op['ap'][$p]['ap_det'][$ap_s]['receive_det'][] = $rcv_det_row;
				}
			}
			$ttl_rcv_qty += $rcv_qty;
			$op['ap'][$p]['ap_det'][$ap_s]['rcv_qty'] = $rcv_qty;
			
			#判斷是否超過tolerance
			$rcv_qty = $rcv_qty > $toleri_qty ? $toleri_qty : $rcv_qty;
			#apb_qty
			$apbed_row = $this->get_det_field("sum(qty) as qty","apb,apb_det","apb_det.po_id='".$op['ap'][$p]['ap_det'][$ap_s]['po_spare']."' and apb_det.ap_num='".$op['ap'][$p]['ap_det'][$ap_s]['ap_num']."' and apb.rcv_num=apb_det.rcv_num and apb.payment like '%|after'");
			$apbed_qty = $apbed_row['qty'];
			$op['ap'][$p]['ap_det'][$ap_s]['apbed_qty'] = $apbed_qty;
			$op['ap'][$p]['ap_det'][$ap_s]['apb_qty'] = $rcv_qty - $apbed_qty;
			//$op['ap'][$p]['ap_det'][$ap_s]['apb_qty'] = $rcv_qty;
			$op['ap'][$p]['ap_det'][$ap_s]['toleri_qty'] = $rcv_qty - $op['ap'][$p]['ap_det'][$ap_s]['apbed_qty'];
			
			
			#單價轉換
			$ORI_price = change_unit_price($op['ap'][$p]['ap_det'][$ap_s]['po_unit'],$op['ap'][$p]['ap_det'][$ap_s]['prc_unit'],$op['ap'][$p]['ap_det'][$ap_s]['prics']);
			$op['ap'][$p]['ap_det'][$ap_s]['prics'] = $ORI_price;
			
			#小計
			$op['ap'][$p]['ap_det'][$ap_s]['price'] = number_format($op['ap'][$p]['ap_det'][$ap_s]['apb_qty'] * $op['ap'][$p]['ap_det'][$ap_s]['prics'],2,'','');
			$ttl_rcv_price += $op['ap'][$p]['ap_det'][$ap_s]['price'];
			
		}
		
		# ap_special 匯率、台幣單價、ttl_rcv_qty
		for($ap_s=0;$ap_s<sizeof($op['ap'][$p]['ap_spec']);$ap_s++){
			$TODAY = date('Y-m-d');
			$rate=1;
			//if($op['ap'][$p]['currency'] <> 'NTD') $rate = $GLOBALS['rate']->get_rate($op['ap'][$p]['currency'], $TODAY);
			$op['ap'][$p]['rate'] = $rate;
			$op['ap'][$p]['ap_spec'][$ap_s]['uprice'] = $op['ap'][$p]['ap_spec'][$ap_s]['prics'] * $rate;
			#toler最大數
			$toleri_qty = $op['ap'][$p]['ap_spec'][$ap_s]['po_qty'] * (1+($op['ap'][$p]['toleri']*0.01));
			$rcv_qty=0;
			#取出 驗收資料 準備寫入apb_det用的
			$q_str = "select distinct receive.ship_num, receive_det.* 
						from receive, receive_det 
						where receive.rcv_num=receive_det.rcv_num and receive_det.po_id='".$op['ap'][$p]['ap_spec'][$ap_s]['po_spare']."' and receive.tw_rcv = 0";
			
			if (!$rcv_det_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;
			}
			
			while ($rcv_det_row = $sql->fetch($rcv_det_result)) {
				$rcv_qty += $rcv_det_row['qty'];
				if($rcv_qty > $toleri_qty or $rcv_det_row['qty'] > $toleri_qty){
					$rcv_det_row['qty'] = $toleri_qty - $rcv_qty + $rcv_det_row['qty'];
					if ( $rcv_det_row['qty'] < 0 ) $rcv_det_row['qty']=0;
				}
				$op['ap'][$p]['ap_spec'][$ap_s]['receive_det'][] = $rcv_det_row;
			}
			$ttl_rcv_qty += $rcv_qty;
			$op['ap'][$p]['ap_spec'][$ap_s]['rcv_qty'] = $rcv_qty;
			
			#判斷是否超過tolerance
			$rcv_qty = $rcv_qty > $toleri_qty ? $toleri_qty : $rcv_qty;
			#apb_qty
			$op['ap'][$p]['ap_spec'][$ap_s]['apb_qty'] = $toleri_qty;
			$change_qty = change_unit_qty($op['ap'][$p]['ap_spec'][$ap_s]['po_unit'],$op['ap'][$p]['ap_spec'][$ap_s]['prc_unit'],$op['ap'][$p]['ap_spec'][$ap_s]['apb_qty']);
			
			#小計
			$op['ap'][$p]['ap_spec'][$ap_s]['price'] = number_format($change_qty * $op['ap'][$p]['ap_spec'][$ap_s]['prics'],2,'','');
			$ttl_rcv_price += $op['ap'][$p]['ap_spec'][$ap_s]['price'];
		}
						
		//其他費用
		$op['ap'][$p]['ap_oth_cost'] = array ();		
		$q_str="SELECT * FROM `ap_oth_cost` WHERE  `ap_num` = '$num' and apb_num = ''";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$op['ap'][$p]['ap_oth_cost'] = array();
		$r=0;
		while ($row1 = $sql->fetch($q_result)) {			
			$op['ap'][$p]['ap_oth_cost'][$r]=$row1;
			$amt += $row1['cost'];
			$r++;
		}
		
		$GLOBALS['po']->update_field_num('po_total', $amt , $op['ap']['po_num']);
		$op['ap'][$p]['po_total'] = $amt;
		
		//po 貨前已付金額
		$op['ap'][$p]['apb_oth_cost'] = $this->get_det_field("cost,apb_num","apb_oth_cost","ap_num='".$op['ap'][$p]['ap_num']."' and item='before|%'");
		$price_row = $this->get_det_field("sum(cost) as price", "apb_oth_cost" , "ap_num='".$ap_row['ap_num']."' and item <> '%|after'");
		$op['ap'][$p]['before_price'] = $price_row['price'];
		
		//po 貨後已付金額
		$price_row = $this->get_det_field("cost", "apb_oth_cost" , "ap_num='".$ap_row['ap_num']."' and item = '%|after'");
		$op['ap'][$p]['after_price'] = $price_row['cost'];
		
		//po 額外金額(樣品費...等等)
		/* $op['ap'][$p]['apb_oth_cost2'] = array();
		$op['ap'][$p]['apb_oth_cost2'] = $this->get_link_field("item,cost,apb_num","apb_oth_cost","ap_num='".$op['ap'][$p]['ap_num']."' and item not like 'before|%'");
		$pre = 0;
		for($h=0;$h<sizeof($op['ap'][$p]['apb_oth_cost2']);$h++){
			//$pre += $op['ap'][$p]['apb_oth_cost2'][$h]['cost'];
		} */
		
		$op['ap'][$p]['ttl_po_qty'] = $ttl_po_qty;
		$op['ap'][$p]['ttl_rcv_qty'] = $ttl_rcv_qty;

		#chk_pay 用來判斷驗收數量是否在tolerance範圍內,並計算應付金額
		$chk_pay=0;
		//if( $ttl_rcv_qty >= ($ttl_po_qty * (1-$op['ap'][$p]['tolern'])) and $op['ap'][$p]['apb_oth_cost'] > 0){
			$chk_pay=1;
			$op['ap'][$p]['chk_pay'] = $chk_pay;
			if($op['ap'][$p]['after_price'] > 0){	#是否已經付過尾款(但還沒付清)
				$op['ap'][$p]['after_pay'] = number_format($ttl_rcv_price,2,'','');
			}else{
				$op['ap'][$p]['after_pay'] = number_format($ttl_rcv_price - $op['ap'][$p]['before_price'],2,'','');
			}
		//}
		
		$p++;
	}

	return $op;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_tt_before()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_tt_before($dm_way) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT sup_code, supl.country, supl.supl_s_name as s_name
                             FROM ap, supl ";
    
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("supl.s_name ASC");
    $srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
        ##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
        ##--*****--2006.11.16頁碼新增 end	   ##
	}


	//2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    //部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
		if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
    //部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}
	
	if($str = $argv['PHP_po']){
		$srh->add_where_condition("`ap`.`po_num` like '%$str%'");
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}
	
	$srh->add_where_condition("ap.sup_code = supl.vndr_no");
	$srh->add_where_condition("ap.status = 12");
	$srh->add_where_condition("ap.dm_way like '".$dm_way."'");
	$srh->add_where_condition("ap.po_date >= '2012-06-01'");
	$srh->add_where_condition("ap.apb_rmk = 0");
	
	$srh->add_group_condition("ap.sup_code");	
	
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
    echo $srh->q_str;
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
#	->search_po()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_po($sup_code,$dm_way) {
		$sql = $this->sql;

		$q_str = "select ap.ap_num
				  from ap
				  where sup_code= '$sup_code' and dm_way like '%$dm_way%' and status=12 and apb_rmk= 0 and po_date >= '2012-06-01' order by id desc";

		if (!$ap_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$p=0;	
	while ($ap_row = $sql->fetch($ap_result)) {
		$num = $ap_row['ap_num'];
		//請購主檔
		$q_str = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, supl.usance, supl.dm_way as supl_dm_way, supl.cntc_phone, supl.cntc_addr, supl.supl_f_name, supl.cntc_person1, supl.id as supl_id , supl.uni_no
		FROM ap, supl WHERE  ap.sup_code = supl.vndr_no AND ap_num='$num'";
		
		
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}

		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!1");
			return false;
		}
		
		$toler = explode('|',$row['toler']);
		$row['toleri'] = $toler[0];
		$row['tolern'] = $toler[1];
		
		$op['ap'][$p]=$row;
		
		$amt = 0;
		$ttl_qty = 0;
		$op['ap'][$p]['ord_num'] = array();
		
		# 請購明細 -- 主料		
		$q_str="SELECT ap_det.*,ap.toler , smpl_code as ord_num, lots_use.lots_code as mat_code, 
					   lots_use.lots_name as mat_name, bom_lots.color, lots.price1, lots.comp as con1, 
					   lots.specify as con2, lots.des as mile, lots.lots_name as mile_name, lots.width,
					   lots.weight, lots.cons as finish, bom_lots.wi_id
				FROM `ap`,`ap_det`,`bom_lots`,`lots_use`,`lots`  
				WHERE `lots`.`lots_code` = `lots_use`.`lots_code` AND `lots_use`.`id` = `bom_lots`.`lots_used_id` AND `ap_det`.`bom_id`= `bom_lots`.`id` 
						AND `ap_det`.`mat_cat` = 'l' AND `ap`.`ap_num` = `ap_det`.`ap_num` AND `ap_det`.`ap_num` = '$num' AND ap_det.apb_rmk=0";

		$q_result = $sql->query($q_str);
		
		$i=0;
		$op['ap'][$p]['rcv_mk']=0;
		while ($row1 = $sql->fetch($q_result)) {
			$ord_mk =0;
			$ttl_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_det'][$i]=$row1;
			$op['ap'][$p]['ap_det'][$i]['i']=$i;
			if($op['ap'][$p]['ap_det'][$i]['amount'] == 0)$op['ap'][$p]['ap_det'][$i]['amount']=$op['ap'][$p]['ap_det'][$i]['po_qty'] * $op['ap'][$p]['ap_det'][$i]['prics'];
			
			$amt += $op['ap'][$p]['ap_det'][$i]['amount'];
			if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];
			for($j=0; $j<sizeof($op['ap'][$p]['ord_num']); $j++)
			{
				if($op['ap'][$p]['ord_num'][$j] == $row1['ord_num'])
				{
					$ord_mk = 1;
					break;
				}
			}
			if($ord_mk == 0)$op['ap'][$p]['ord_num'][] = $row1['ord_num'];
			if($row1['base_qty'] > 0)$op['ap'][$p]['base_ck'] = 1;
			
			$op['ap'][$p]['po_eta'] = $row1['po_eta'];
			
			$i++;
		}

		# 請購明細 -- 副料
		$q_str="SELECT ap_det.*, smpl_code as ord_num, acc_use.acc_code as mat_code, 
		acc_use.acc_name as mat_name, bom_acc.color, acc.price1, acc.des as con1, 
		acc.specify as con2, acc.mile_code as mile, acc.mile_name, bom_acc.wi_id
		FROM `ap_det`, bom_acc,acc_use, acc  
		WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id AND bom_id=bom_acc.id AND mat_cat = 'a' AND ap_det.ap_num = '$num' 
				AND ap_det.apb_rmk=0";
		
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row1 = $sql->fetch($q_result)) {
			$ord_mk = 0;
			$ttl_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_det'][$i]=$row1;
			$op['ap'][$p]['ap_det'][$i]['i']=$i;
			if($op['ap'][$p]['ap_det'][$i]['amount'] == 0)$op['ap'][$p]['ap_det'][$i]['amount']=$op['ap'][$p]['ap_det'][$i]['po_qty']*$op['ap'][$p]['ap_det'][$i]['prics'];
			if($op['ap'][$p]['ap_det'][$i]['rcv_qty'] > 0)$op['ap'][$p]['rcv_mk'] = 1;
			$amt += $op['ap'][$p]['ap_det'][$i]['amount'];
			if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];

			for($j=0; $j<sizeof($op['ap'][$p]['ord_num']); $j++)
			{
				if($op['ap'][$p]['ord_num'][$j] == $row1['ord_num'])
				{
					$ord_mk = 1;
					break;
				}
			}
			if($ord_mk == 0)$op['ap'][$p]['ord_num'][] = $row1['ord_num'];
			if($row1['base_qty'] > 0)$op['ap'][$p]['base_ck'] = 1;
			$i++;
			
			$op['ap'][$p]['po_eta'] = $row1['po_eta'];
		} 	
		
		//Remark項目
		/* $op['ap'][$p]['apply_log'] = array();
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='remark'";
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
			$op['ap'][$p]['apply_log'][]=$row1;
		}

		//特殊採購原因(Remark)
		$op['ap'][$p]['apply_special'] = array ();		
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='special'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
			$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];			
			$op['ap'][$p]['apply_special'][]=$row1;
		} */			

		if(isset($op['ap'][$p]['ap_det'])) $op['ap'][$p]['ap_det']=$GLOBALS['po']->group_ap($op['ap'][$p]['ap_det']);
		
		# ap_det 貨前數量 、 金額小計 及 台幣單價
		$m_size = sizeof($op['ap'][$p]['ap_det']);
		for($m=0;$m<$m_size;$m++){
			$rate=1;
			$op['ap'][$p]['ap_det'][$m]['rate'] = $rate;
			$op['ap'][$p]['ap_det'][$m]['uprice'] = $op['ap'][$p]['ap_det'][$m]['prics'] * $rate;
			
			# 原幣單價轉換
			$op['ap'][$p]['ap_det'][$m]['po_unit'] == '' ? $c_unit = $op['ap'][$p]['ap_det'][$m]['unit'] : $c_unit = $op['ap'][$p]['ap_det'][$m]['po_unit'];
			$chg_price = change_unit_price($op['ap'][$p]['ap_det'][$m]['prc_unit'],$c_unit,$op['ap'][$p]['ap_det'][$m]['prics']);
			$op['ap'][$p]['ap_det'][$m]['uprices'] = $op['ap'][$p]['ap_det'][$m]['uprice'] = $chg_price;
			
			$apbed_qty = $this->get_apbed_qty($op['ap'][$p]['ap_det'][$m]['ap_num'],$op['ap'][$p]['ap_det'][$m]['mat_id'],$op['ap'][$p]['ap_det'][$m]['color'],$op['ap'][$p]['ap_det'][$m]['size'],"0,2","");
			if($apbed_qty == null) $apbed_qty = 0;
			$op['ap'][$p]['ap_det'][$m]['apbed_qty'] = $apbed_qty;
			
			$op['ap'][$p]['ap_det'][$m]['toleri_qty'] = $op['ap'][$p]['ap_det'][$m]['po_qty'] * ( 1 + $op['ap'][$p]['toleri'] / 100);
			if($c_unit == 'pc' or $c_unit == 'K pcs')
				$op['ap'][$p]['ap_det'][$m]['toleri_qty'] = ceil($op['ap'][$p]['ap_det'][$m]['toleri_qty']);
			$op['ap'][$p]['ap_det'][$m]['apb_qty'] = $op['ap'][$p]['ap_det'][$m]['po_qty'] - $apbed_qty;
			$op['ap'][$p]['ap_det'][$m]['amount'] = number_format($op['ap'][$p]['ap_det'][$m]['apb_qty'] * $op['ap'][$p]['ap_det'][$m]['uprice'],2,'','');
			$op['ap'][$p]['ap_det'][$m]['i'] = $m;
			
			$op['ap'][$p]['before_price'] += $op['ap'][$p]['ap_det'][$m]['amount'];
		}
		
		# 其他費用
		$op['ap'][$p]['ap_oth_cost'] = array ();		
		$q_str="SELECT * FROM `ap_oth_cost` WHERE  `ap_num` = '$num' and apb_num = ''";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$op['ap'][$p]['ap_oth_cost'] = array();
		$r=0;
		while ($row1 = $sql->fetch($q_result)) {			
			$op['ap'][$p]['ap_oth_cost'][$r]=$row1;
			$amt += $row1['cost'];
			$r++;
		}
		
		$op['ap'][$p]['ttl_qty'] = $ttl_qty;
		
		$p++;
	}
	
	return $op;
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	check_po_apb($po_num) 檢查此張PO是否已經全部付款
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_po_apb($po_num) {
	$sql = $this->sql;
	
	$spec = $this->get_det_field("special,ap_num","ap","po_num = '".$po_num."'");
	$spec['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det';
	
	$q_str="SELECT apb_rmk FROM ".$tbl." WHERE ap_num = '".$spec['ap_num']."' and apb_rmk=0";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}

	if ($row1 = $sql->fetch($q_result)) {
		return false;
	}
	
	return $this->update_field("apb_rmk", "1", "po_num='".$po_num."'", "ap");
	
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	oth_cost_add($ap_num, $item, $cost, $apb_num) 加入apb 其他cost
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function oth_cost_add($parm) {
	$sql = $this->sql;
	
	$q_str="insert into apb_oth_cost(`ap_num`,`item`,`cost`,`apb_num`,`payment_status`)
			values('".$parm['ap_num']."','".
					  $parm['item']."',".
					  $parm['cost'].",'".
					  $parm['apb_num']."',".
					  $parm['payment_status'].
					")";
    // echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;
	
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_after_oth_cost($apb_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_after_oth_cost($apb_num, $ap_num, $where_str='') {
	$sql = $this->sql;
	
	$q_str="SELECT * FROM `apb_oth_cost` 
			WHERE apb_num = '$apb_num' and ap_num = '$ap_num' ".$where_str;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$row1 = $sql->fetch($q_result);
	
	return $row1;
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	change_dept($d)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function change_dept($d) {
	$sql = $this->sql;
	
	switch ($d){
		case "D":
			return "DA";
		case "A":
			return "KA";
		case "B":
			return "KB";
		default:
			return $d;
	}
	
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_link_qty_by_id($po_id)	抓出指定receive記錄資料 RETURN qty	2012/01/12
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_link_qty_by_id($id) {

		$sql = $this->sql;
		
		$q_str = "SELECT sum(qty) as apb_qty
							FROM apb_po_link
							WHERE po_id = '$id'";

//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);

		return $row['apb_qty'];
	}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	num2chinese($num)	將傳入的數字轉成中文字
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function num2chinese($num) {
	$tmp = explode('.',$num);
	$mypoint = array();
	$myint = array();
 
	if(sizeof($tmp) > 1)	{
		//表示有小數點
		for($p=0;$p<strlen($tmp[1]);$p++){
			$mypoint[$p]=substr($tmp[1],$p,1);
			$mypoint[$p]=$this->chineseNum($mypoint[$p]);
		}
		for($i=0;$i<strlen($tmp[0]);$i++){
			$myint[$i]=substr($tmp[0],$i,1);
			$myint[$i]=$this->chineseNum($myint[$i]);
		}
	}
	else{
		//沒有小數點
		for($i=0;$i<strlen($tmp[0]);$i++){
			$myint[$i]=substr($tmp[0],$i,1);
			$myint[$i]=$this->chineseNum($myint[$i]);
		}
	}
	$num=0;
	if(sizeof($myint)>0){
		if(sizeof($mypoint)>0){
			for($b=sizeof($mypoint)-1;$b>=0;$b--){
				$myrtn[$num]=$mypoint[$b];
				$num++;
			}
		}
		for($a=sizeof($myint)-1;$a>=0;$a--){
			$myrtn[$num]=$myint[$a];
			$num++;
		}
		$num=0;
	}
 return $myrtn;
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	num2chinese($num)	將傳入的數字轉成國字
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chineseNum($num)
{
 $myrtn="";
 switch($num)
 {
  case "1":
  $myrtn="壹";
  break;
  case "2":
  $myrtn="貳";
  break;
  case "3":
  $myrtn="參";
  break;
  case "4":
  $myrtn="肆";
  break;
  case "5":
  $myrtn="伍";
  break;
  case "6":
  $myrtn="陸";
  break;
  case "7":
  $myrtn="柒";
  break;
  case "8":
  $myrtn="捌";
  break;
  case "9":
  $myrtn="玖";
  break;
  case "0":
  $myrtn="零";
  break;
  default:
	$myrtn=$num;
 }
 return $myrtn;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->adjust_edit($cost,$apb_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function adjust_edit($cost,$apb_num){
	$sql = $this->sql;

	$q_str = "update apb set adjust_amt='$cost' where rcv_num='".$apb_num."'";
		
	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
	}

	return true;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_qty($ap_num,$mat_id,$color,$size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_qty($ap_num,$mat_id,$color,$size){
	$sql = $this->sql;

	$q_str = "select sum(qty) as qty from receive_det 
				where ap_num='".$ap_num."' and mat_id=".$mat_id." and color='".$color."' and size='".$size."' and apb_rmk=1";
	
	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
	}
	$rcvd_row = $sql->fetch($q_result);
	if($rcvd_row['qty'] == null) $rcvd_row['qty'] = 0;

	return $rcvd_row['qty'];
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_full_det_rmk($po_id,$rcv_qty,$po_qty)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_full_det_rmk($ap_num){
	$sql = $this->sql;
	
	$q_str = "select id from ap_det 
				where ap_num='".$ap_num."' and apb_rmk=0";
		
	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
	}
	
	if(!$row = $sql->fetch($q_result)){
		$q_str = "update ap set apb_rmk = 1
				where ap_num='".$ap_num."'";
		
		$q_result = $sql->query($q_str);
		return true;
	}
	
	return false;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_po_rcv()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_po_rcv($dm_way) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT distinct sup_code, supl.country, supl.supl_s_name as s_name
					 FROM ap left join supl on ap.sup_code = supl.vndr_no left join stock_inventory on stock_inventory.po_num = ap.po_num";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("supl.s_name ASC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 	}


	//2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
		if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}
	
	if( $str = $argv['PHP_po']){
		$srh->add_where_condition("`ap`.`po_num` like '%$str%'");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`stock_inventory`.`ship_inv` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}
	
	$srh->add_where_condition("ap.status = 12");
	$srh->add_where_condition("ap.dm_way like '".$dm_way."'");
	$srh->add_where_condition("ap.po_date >= '2012-08-01'");
	$srh->add_where_condition("ap.apb_rmk = 1");
	$srh->add_where_condition("stock_inventory.type = 'i'");
	$srh->add_where_condition("stock_inventory.apb_num = ''");
	
	$srh->add_group_condition("ap.sup_code");	
	
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
#	->get_po_rcvd($sup_no)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_rcvd($sup_no='',$payment='') {
	$sql = $this->sql;
	
	$where_str = '';
	if($sup_no) $where_str.=" and receive_det.sup_no = '".$sup_no."' ";
	if($payment) $where_str.=" and ap.dm_way like '%".$payment."%' ";
	# 驗收主檔
	$q_str = "SELECT distinct receive.ship_num, receive.rcv_num, receive.rcv_date
			  From receive, receive_det,ap
			  WHERE receive.rcv_num = receive_det.rcv_num and receive.status = 4 and receive.tw_rcv = 0
			  and receive_det.ap_num = ap.ap_num and receive_det.apb_rmk = 0 and receive.rcv_date > '2012-01-01'".$where_str." order by receive.ship_num desc";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	$count_i=0;
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
		# 驗收明細
		$q_str = "SELECT distinct receive_det.*
				  From receive_det,ap
				  WHERE receive_det.rcv_num = '".$row['rcv_num']."'".$where_str." and receive_det.ap_num = ap.ap_num and receive_det.apb_rmk = 0 
						order by receive_det.id desc";

		if (!$det_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$p=0;
		$amt = 0; //計算金額
		
		while ($det_row = $sql->fetch($det_result)) {
			$order = array();
			$arr_ap_num[] = $det_row['ap_num'];
			$op['rcv'][$r]['rcv_det'][$p] = $det_row;
						
			# start 取得訂單號碼
			$p_id = explode('|',$op['rcv'][$r]['rcv_det'][$p]['po_id']);
			for ($s=0;$s<sizeof($p_id);$s++){
				$q_str = "select rcv_po_link.ord_num 
						from rcv_po_link 
						where po_id='".$p_id[$s]."' and rcv_id = ".$det_row['id'];
				
				$ord_result = $sql->query($q_str);
				$ord_row = $sql->fetch($ord_result);
				$order[$s] = $ord_row['ord_num'];
			}
						
			$order = array_unique($order);
			$ord = array();
			foreach($order as $keyS)
				$ord[] = $keyS;
			$op['rcv'][$r]['rcv_det'][$p]['orders'] = $ord;
			# end 取訂單號碼
			
			# start 取 幣別、Tolerance、單價、匯率、採購數量
			$op['rcv'][$r]['rcv_det'][$p]['po'] = $this->get_det_field("ap_num,currency,toler,special,po_num","ap","ap_num='".$op['rcv'][$r]['rcv_det'][$p]['ap_num']."'");
			$toler = explode("|",$op['rcv'][$r]['rcv_det'][$p]['po']['toler']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] = $toler[0];
			$op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] = $toler[1];
			$op['rcv'][$r]['rcv_det'][$p]['po']['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det';
			$po_det_row = $this->get_det_field("unit,po_unit,prc_unit,prics,po_spare",$tbl,"po_spare='".$op['rcv'][$r]['rcv_det'][$p]['po_id']."'");
			#單價
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $po_det_row['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['unit'] = $po_det_row['unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = $po_det_row['po_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'] = $po_det_row['prc_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_spare'] = $po_det_row['po_spare'];
			
			$rate = 1;
			$op['rcv'][$r]['rcv_det'][$p]['po']['rate'] = $rate;
			
			#台幣
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $rate * $op['rcv'][$r]['rcv_det'][$p]['po']['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['i']=$p;
			//$op['rcv'][$r]['rcv_det'][$p]['r']=$count_i;
			
			$po_qty_row = $this->get_det_field("sum(po_qty) as qty",$tbl,"po_spare='".$op['rcv'][$r]['rcv_det'][$p]['po_id']."' group by po_spare");
			$op['rcv'][$r]['rcv_det'][$p]['po']['qty'] = $po_qty_row['qty'];
			# end 取 幣別、Tolerance、單價、匯率、採購數量
			
			#計算 tolerance
			$max_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
			$min_qty = (1 - $op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['qty'];
			if($op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "pc" or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			
			#單價轉換
			$ORI_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'],$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $ORI_price;
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $ORI_price;
				
			//$rmk_qty = $this->check_qty($det_row['po_id']);
			$rcvd_row = $this->get_det_field("sum(qty) as qty","receive_det","po_id='".$det_row['po_id']."' and ap_num='".$det_row['ap_num']."' and apb_rmk=1");
			$apb_row = $this->get_det_field("sum(qty) as qty","apb,apb_det","apb.rcv_num=apb_det.rcv_num and apb.status>2 and rcvd_num='".$row['rcv_num']."' and apb_det.po_id='".$det_row['po_id']."'");;
			
			//$po_id_qty[$det_row['po_id']] += $rmk_qty + $det_row['qty'];
			$po_id_qty[$det_row['po_id']] += $det_row['qty'];
			
			if ( ($rcvd_row['qty'] + $po_id_qty[$det_row['po_id']]) <= $max_qty ){
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $det_row['qty'];// - $apb_row['qty'];
				$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($ORI_price * $op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $op['rcv'][$r]['rcv_det'][$p]['apb_qty'];
			}else{
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $max_qty - $po_id_qty[$det_row['po_id']] - $rcvd_row['qty'] + $det_row['qty']; 
				if($op['rcv'][$r]['rcv_det'][$p]['apb_qty'] < 0) $op['rcv'][$r]['rcv_det'][$p]['apb_qty']=0;
				$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($ORI_price * $op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $op['rcv'][$r]['rcv_det'][$p]['apb_qty'];
			}
				
				#判斷是否大於最小toler
				/* $chk_rcv = $this->get_po_link($det_row['po_id'],"receive_det");
				if($chk_rcv['qty'] < $min_qty){
					$op['rcv'][$r]['rcv_det'][$p]['chk_pay'] = 0;
					$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = 0;
				}else{
					$op['rcv'][$r]['rcv_det'][$p]['chk_pay'] = 1;
				} */
				
				$amt += $op['rcv'][$r]['rcv_det'][$p]['po']['price'];
			
			
			$p++;
			$count_i++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}

	#額外費用
	$arr_ap_num = array_unique($arr_ap_num);
	foreach($arr_ap_num as $val){
		$q_str="SELECT * FROM `ap_oth_cost` 
				WHERE ap_num = '$val' and `apb_num` = ''";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while($row1 = $sql->fetch($q_result)) {
			$op['ap_oth_cost'][]=$row1;
		}
	}
	
	return $op;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_before_apb($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_before_apb($num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# 物料檔 apb_det
	$q_str = "SELECT distinct rcvd_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$rcvd_row[] = $row;
	}
	
	for($k=0;$k<sizeof($rcvd_row);$k++){
		$amt = 0;
		# 抓receive相關資料
		$q_str = "SELECT ship_num, rcv_num, rcv_date FROM receive 
			WHERE rcv_num = '".$rcvd_row[$k]['rcvd_num']."'";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$op['apb_det'][$k] =  $row;
		
		# apb明細
		$q_str = "SELECT `apb_det`.*, ap.special 
				FROM `apb_det`, `ap` 
				WHERE `apb_det`.`rcvd_num` = '".$rcvd_row[$k]['rcvd_num']."' and `apb_det`.`rcv_num`='".$num."' and apb_det.ap_num = ap.ap_num";
		$q_result = $sql->query($q_str);
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			`id` as `link_id`,`po_id`,`rcv_id`,`qty` as `link_qty`,`currency`,`rate`,`amount`,`ord_num`
			FROM `apb_po_link`  
			WHERE `rcv_id` = '".$row['id']."' ";
			
			$link_res = $sql->query($q_str);
			$order = array();
			
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$row['currency'] = $rows['currency'];
				$row['rate'] = $rows['rate'];
				//$row['price'] += $rows['amount'];
			}
			$order = array_unique($order);
			foreach($order as $key)
			$row['order'][] = $key;
			
			
			# PO 數量 單位
			$po_eta = '';
			$po_qty = 0;
			$po_id = explode('|',$row['po_id']);
			foreach($po_id as $key){
				$q_str = "
				SELECT  ap.ap_num, ap.po_num, ap.toler, `po_qty`,`po_eta`,`unit`,`po_unit`,`prc_unit`,`prics`, ap.special
				FROM `".( $row['special'] == 2 ? $tbl='ap_special' : $tbl='ap_det' )."` ,ap
				WHERE ".$tbl.".`id` = '".$key."' and ".$tbl.".ap_num = ap.ap_num";
				
				$q_res = $sql->query($q_str);
				while ($rows = $sql->fetch($q_res)) {
					$po_qty += $rows['po_qty'];
					$po_eta = $rows['po_eta'];
					$row['po'] = $rows;
					$toler = explode("|",$row['po']['toler']);
					$row['po']['toleri'] = $toler[0];
					$row['po']['tolern'] = $toler[1];
				}
			}
			$row['po']['po_qty'] = $po_qty;
			$row['po']['po_eta'] = $po_eta;
			
			
			# RCV 數量
			$q_str = "
			SELECT `qty`
			FROM receive_det 
			WHERE `po_id` = '".$row['po_id']."' and inv_num = '".$row['inv_num']."' and rcv_num = '".$row['rcvd_num']."'";
			
			$q_res = $sql->query($q_str);
			$rows = $sql->fetch($q_res);
			$row['rcv_qty'] = $rows['qty'];
			$row['i'] = $i;
			
			# 計算可付款的最大tolerance
			$apb_row = $this->get_det_field("sum(qty) as qty","apb,apb_det","apb.rcv_num=apb_det.rcv_num and ap_num='".$row['ap_num']."' and apb_det.po_id='".$row['po_id']."' and apb.status in(3,4)");
			$apb_row['qty'] = $apb_row['qty'] - $row['qty'];//扣掉自己
			$po_id_qty[$row['po_id']] += $row['rcv_qty'];
			
			$max_qty = (1 + $row['po']['toleri']/100) * $row['po']['po_qty'];
			
			if ( ($po_id_qty[$row['po_id']]+$apb_row['qty']) <= $max_qty ){
				$row['toler_qty'] = $po_id_qty[$row['po_id']];
			}else{
				$row['toler_qty'] = $max_qty - $apb_row['qty'];// - $po_id_qty[$row['po_id']] + $row['rcv_qty'];
			}
			
			$op['apb_det'][$k]['det'][$i] = $row;
			
			#小計金額
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
			$i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}

	#ap_oth_cost 額外費用
	$q_str="SELECT * FROM `ap_oth_cost` 
			WHERE apb_num = '$num'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while($row1 = $sql->fetch($q_result)) {
		$op['ap_oth_cost'][]=$row1;
	}
	
	#apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_oth_cost($num);
	
	return $op;
} // end func


function get_apb_oth_cost($apb_num) {
    $sql = $this->sql;
	
	$q_str = "select * from apb_oth_cost
			  where apb_num='".$apb_num."' and (item not like '%before%' and item not like '%after%')";
	
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot append order");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    while($row = $sql->fetch($q_result)){
        $apb_cost[]=$row;
    }
    
    return $apb_cost;

}

function get_ap_oth_cost($apb_num,$where_str = '') {
    $sql = $this->sql;
	
	$q_str = "select * from ap_oth_cost
			  where apb_num='".$apb_num."' ".$where_str;
	
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot append order");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    while($row = $sql->fetch($q_result)){
        $ap_cost[]=$row;
    }
    
    return $ap_cost;

}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_same_mat($ap_num,$mat_id,$color,$size,$sort='')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_same_mat($ap_num,$mat_id,$color,$size,$sort=''){
	#$sort是依照po_qty以小排到大，在分攤數量時用到
	$sql = $this->sql;
	
	$q_str = "select id,ap_num, bom_id, mat_cat, mat_id, used_id, color, size, ap_qty, po_qty, prc_unit
			  from ap_det
			  where ap_num = '".$ap_num."' and mat_id = ".$mat_id." and color = '".$color."' and size = '".$size."' ".$sort;

	if (!$q_res = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$rtn = array();
	while($row = $sql->fetch($q_res)){
		$rtn[] = $row;
	}
	
	return $rtn;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_ord_num($id,$po_cate)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_num($id,$po_cate) {
		$sql = $this->sql;
		
		if($po_cate == "l"){
			$q_str = "SELECT wi_num
					  FROM wi, ap_det, bom_lots 
					  WHERE ap_det.id =".$id." and bom_lots.id = ap_det.bom_id AND bom_lots.wi_id = wi.id";
			
			$q_result = $sql->query($q_str);
			$ord_row = $sql->fetch($q_result);
		}else{
			$q_str = "SELECT wi_num
					  FROM wi, ap_det, bom_acc 
					  WHERE ap_det.id =".$id." and bom_acc.id = ap_det.bom_id AND bom_acc.wi_id = wi.id";
			
			$q_result = $sql->query($q_str);
			$ord_row = $sql->fetch($q_result);
		}
		return $ord_row['wi_num'];
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_group_det($ap_num,$mat_id,$color,$size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_group_det($ap_num,$mat_id,$color,$size) {
		$sql = $this->sql;
		
		$q_str = "SELECT sum(po_qty) as po_qty, unit, po_unit, prc_unit, prics
				  FROM ap_det
				  WHERE ap_num = '".$ap_num."' and mat_id = ".$mat_id." and color = '".$color."' and size = '".$size."'
						group by ap_num,mat_id,color,size";
		
		$q_result = $sql->query($q_str);
		$det_row = $sql->fetch($q_result);
		
		return $det_row;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_group_apb_inventory($apb_num,$po_num,$mat_id,$color,$size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_group_apb_inventory($apb_num,$po_num,$mat_id,$color,$size) {
		$sql = $this->sql;
		
		$q_str = "SELECT sum(qty) as qty
				  FROM stock_inventory
				  WHERE type = 'i' and apb_num = '".$apb_num."' and po_num = '".$po_num."' and mat_id = ".$mat_id." and color = '".$color."' and size = '".$size."'
						group by po_num,mat_id,color,size";
		
		$q_result = $sql->query($q_str);
		$det_row = $sql->fetch($q_result);
		
		return $det_row['qty'];
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_group_inventory($ship_num,$po_num,$mat_id,$color,$size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_group_inventory($ship_num,$po_num,$mat_id,$color,$size) {
		$sql = $this->sql;
		
		$q_str = "SELECT sum(qty) as qty
				  FROM stock_inventory
				  WHERE ship_id = '".$ship_num."' and po_num = '".$po_num."' and mat_id = ".$mat_id." and color = '".$color."' and size = '".$size."'
						group by po_num,mat_id,color,size";
		
		$q_result = $sql->query($q_str);
		$det_row = $sql->fetch($q_result);
		
		return $det_row['qty'];
	}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_tw_rcv_qty($receive_det_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_tw_rcv_qty($receive_det_id){
		$sql = $this->sql;
		
		$q_str = "SELECT qty
				  FROM receive_det
				  WHERE id = '".$receive_det_id."'";
		// echo $q_str.'<br>';
		$q_result = $sql->query($q_str);
		$det_row = $sql->fetch($q_result);
		
		return $det_row['qty'];
	}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_apbed_qty($ap_num,$mat_id,$color,$size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_apbed_qty($ap_num,$mat_id,$color,$size,$status,$where_str='') {
		$sql = $this->sql;
		
		$q_str = "SELECT sum(apb_det.qty) as apbed_qty
				  FROM apb, apb_det
				  WHERE apb_det.ap_num = '".$ap_num."' and apb_det.mat_id = ".$mat_id." and apb_det.color = '".$color.
				  "' and apb_det.size = '".$size."'	and apb.rcv_num = apb_det.rcv_num and apb.status in(".$status.") ".$where_str;
		echo $q_str.'<br>';
		$q_result = $sql->query($q_str);
		$det_row = $sql->fetch($q_result);
		if(!$det_row['apbed_qty'])
			$det_row['apbed_qty'] = 0;
		
		return $det_row['apbed_qty'];
	}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_before_apb_price($ap_num, $where_str) {
		$row=array();
		$sql = $this->sql;
		$q_str="select apb_num
				from apb_oth_cost
				where item = 'before|%' and ap_num ='".$ap_num."' ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row1 = $sql->fetch($q_result)){
			$q_str="select sum(cost) as cost
				from apb_oth_cost
				where apb_num = '".$row1['apb_num']."' and ap_num ='".$ap_num."' group by apb_num,ap_num";
			$q_res = $sql->query($q_str);
			$row[] = $sql->fetch($q_res);
		}
		return $row;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_pay_price()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_pay_price($ap_num, $rcv_num) {
		
		$sql = $this->sql;
		$q_str="select sum(cost) as cost
				from apb_oth_cost
				where ap_num ='".$ap_num."' and apb_num = '".$rcv_num."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row = $sql->fetch($q_result);
		
		return $row['cost'];
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_oth_cost()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_oth_cost($ap_num, $rcv_num, $status, $where_str='') {
		
		$sql = $this->sql;
		$rtn = array();
		
		$q_str="select *
				from apb_oth_cost
				where apb_num = '".$rcv_num."' and ap_num ='".$ap_num."' and payment_status = ".$status." ".$where_str;
		$q_res = $sql->query($q_str);
		while($row = $sql->fetch($q_res)){
			$rtn[] = $row;
		}
		
		return $rtn;
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_payment_cost()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_payment_cost($ap_num, $status, $where_str='') {
		
		$sql = $this->sql;
		$rtn = array();
		
		$q_str="select sum(cost) as cost
				from apb_oth_cost
				where ap_num ='".$ap_num."' and payment_status = ".$status." ".$where_str;
		
		$q_res = $sql->query($q_str);
		$row = $sql->fetch($q_res);
		if($row['cost']==null) $row['cost']=0;
		
		return $row['cost'];
	}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_rcv_oth_cost()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rcv_oth_cost($apb_num, $status) {
		
		$sql = $this->sql;
		$rtn = array();
		
		$q_str="select *
				from apb_oth_cost
				where apb_num = '".$apb_num."' and payment_status = ".$status;
		$q_res = $sql->query($q_str);
		while($row = $sql->fetch($q_res)){
			$rtn[] = $row;
		}
		
		return $rtn;
	}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_po_inventory()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_po_inventory($sup_code) {

    $sql = $this->sql;

    $q_str = "select distinct stock_inventory.ship_id as ship_num
              from stock_inventory, ap
              where stock_inventory.type = 'i' and stock_inventory.po_num = ap.po_num and ap.sup_code= '$sup_code' and ap.dm_way = 'T/T before shipment'
                    and ap.status=12 and ap.apb_rmk= 1
                    and ap.po_date >= '2012-06-01' and stock_inventory.apb_num = ''
              order by stock_inventory.ship_id desc";
    
    echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$chk_qty_ary = array();
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	$count_i=0;
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
		$num = $row['ship_num'];
		# stock_inventory
		$q_str = "SELECT stock_inventory.open_date, stock_inventory.invoice_num, sum(stock_inventory.qty) as qty, stock_inventory.color, 
						 stock_inventory.size, stock_inventory.po_num, stock_inventory.mat_cat, stock_inventory.mat_id, ap.ap_num, ap.toler, ap.currency
				  FROM stock_inventory, ap
				  WHERE stock_inventory.type = 'i' and stock_inventory.ship_id='$num' and stock_inventory.apb_num = ''
						and ap.po_num = stock_inventory.po_num and ap.apb_rmk = 1 and ap.dm_way = 'T/T before shipment'
				  GROUP BY stock_inventory.po_num, stock_inventory.mat_id, stock_inventory.color, stock_inventory.size";
		
		$stock_result = $sql->query($q_str);
		$p=0;
		$amt = 0; //計算金額
		
		while ($stock_row = $sql->fetch($stock_result)) {
			$toler = explode("|",$stock_row['toler']);
			$stock_row['toleri'] = $toler[0];
			$stock_row['tolern'] = $toler[1];
			$op['rcv'][$r]['rcv_det'][$p] = $stock_row;
			
			# 沒有po_id，用mark取代，用來判斷是哪一筆
			$op['rcv'][$r]['rcv_det'][$p]['mark'] = $stock_row['po_num'].$stock_row['mat_id'].$stock_row['color'].$stock_row['size'];
			
			$ap_det_row = $this->get_group_det($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'] = $ap_det_row['po_qty'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $ap_det_row['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['unit'] = $ap_det_row['unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = $ap_det_row['po_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'] = $ap_det_row['prc_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['i'] = $p;
			
			if($stock_row['mat_cat'] == 'a'){
				$mat_sname = $GLOBALS['acc']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['acc_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['acc_code'];
			}else{
				$mat_sname = $GLOBALS['lots']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['lots_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['lots_code'];
			}
			
			# 訂單號碼、數量
			$ord_ary = array();
			$ord_ary = $this->re_avg_qty($stock_row['qty'], $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'], $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['orders'] = $ord_ary;
			
			# 原幣單價轉換
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == '' ? $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['unit'] : $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'];
			$chg_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$c_unit,$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprices'] = $op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $chg_price;
			
			$apbed_qty = $this->get_apbed_qty($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size'],"3,4","");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$op['rcv'][$r]['rcv_det'][$p]['apbed_qty'] = $apbed_qty;
			
			# 計算 tolerance
			$max_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'];
			if($op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "pc" or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			
			$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $max_qty;
			$op['rcv'][$r]['rcv_det'][$p]['max_qty'] = $max_qty;
			
			if( $stock_row['qty'] + $apbed_qty <= $max_qty ){
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $stock_row['qty'];
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $stock_row['qty'];
			}else{
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = number_format($max_qty - $apbed_qty,2,'','');
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $op['rcv'][$r]['rcv_det'][$p]['apb_qty'];
				# 數量有變，所以要重新分配
				$op['rcv'][$r]['rcv_det'][$p]['orders'] = $this->re_avg_qty($op['rcv'][$r]['rcv_det'][$p]['apb_qty'], $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'], $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			}
			
			$op['rcv'][$r]['rcv_det'][$p]['price'] = number_format($op['rcv'][$r]['rcv_det'][$p]['po']['uprices']*$op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
			
			$amt += $op['rcv'][$r]['rcv_det'][$p]['price'];
			$p++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}
	//print_r($op);exit;
	# 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['rcv'][0]['rcv_det'][0]['po_num'].$op['rcv'][0]['rcv_det'][0]['mat_id'].$op['rcv'][0]['rcv_det'][0]['color'].$op['rcv'][0]['rcv_det'][0]['size']]= $op['rcv'][0]['rcv_det'][0]['apb_qty'];
	$op['rcv'][0]['rcv_det'][0]['over_qty_chk'] = 0;
	$rcv_size = sizeof($op['rcv']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['rcv'][$i]['rcv_det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['rcv'][$i]['rcv_det'][$j]['po_num'].$op['rcv'][$i]['rcv_det'][$j]['mat_id'].$op['rcv'][$i]['rcv_det'][$j]['color'].$op['rcv'][$i]['rcv_det'][$j]['size'];
					$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 0;
					if($str <> $key){
						$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
					}else{
						if( $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty > $op['rcv'][$i]['rcv_det'][$j]['max_qty'] ){
							$op['rcv'][$i]['rcv_det'][$j]['apb_qty'] = $op['rcv'][$i]['rcv_det'][$j]['max_qty'] - $qty;
							$op['rcv'][$i]['rcv_det'][$j]['toler_qty'] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$tmp_price = number_format($op['rcv'][$i]['rcv_det'][$j]['po']['uprices']*$op['rcv'][$i]['rcv_det'][$j]['apb_qty'],2,'','');
							$div_price = $op['rcv'][$i]['rcv_det'][$j]['price'] - $tmp_price;
							$op['rcv'][$i]['rcv_det'][$j]['price'] = $tmp_price;
							$op['rcv'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 1;
							# apb數量 不等於 驗收數量，所以重新分配
							$op['rcv'][$i]['rcv_det'][$j]['orders'] = $this->re_avg_qty($op['rcv'][$i]['rcv_det'][$j]['apb_qty'],$op['rcv'][$i]['rcv_det'][$j]['po']['po_qty'],$num,$op['rcv'][$i]['rcv_det'][$j]['po_num'],$op['rcv'][$i]['rcv_det'][$j]['mat_id'],$op['rcv'][$i]['rcv_det'][$j]['color'],$op['rcv'][$i]['rcv_det'][$j]['size']);
						}else{
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty;
						}
					}
				}
			}
		}
	}
	
	return $op;
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_inventory_ord()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	# 未使用到的FUNCTION
	function get_ap_det_ord($po_num,$mat_cat,$mat_id,$color='',$size='') {
		$sql = $this->sql;
		$q_str="SELECT ap_det.id, ap_det.order_num
				FROM ap, ap_det
				WHERE ap.po_num = '".$po_num."' and ap_det.ap_num = ap.ap_num and ap_det.mat_cat='".$mat_cat."' 
					  and ap_det.mat_id=".$mat_id." and ap_det.color='".$color."' and ap_det.size='".$size."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row=array();		
		while($row1 = $sql->fetch($q_result))
		{
			$row[] = $row1;
		}
		
		return $row;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->re_avg_qty()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function re_avg_qty($apb_qty,$ttl_po_qty,$po_num,$mat_id,$color='',$size='') {
		$sql = $this->sql;
		$ap_num = str_replace("O", "A", $po_num);
		$q_str="SELECT id, order_num, sum(po_qty) as po_qty
				FROM ap_det
				WHERE ap_num='".$ap_num."' and mat_id=".$mat_id." and color='".$color."' and size='".$size."'
				GROUP BY ap_num, order_num, mat_id, color, size";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$percent = number_format($apb_qty/$ttl_po_qty,5,'','');
		$sum_qty = 0;
		$row=array();		
		while($row1 = $sql->fetch($q_result)){
			$row[] = $row1;
		}
		$s_size = sizeof($row);
		for($i=0;$i<$s_size-1;$i++){
			$row[$i]['qty'] = number_format($row[$i]['po_qty'] * $percent,0,'','');
			$sum_qty += $row[$i]['qty'];
		}
		$row[$i]['qty'] = $apb_qty - $sum_qty;
		
		return $row;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_row_size()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_inventory_row($ship_inv,$po_num,$mat_id,$color='',$size='') {
		$sql = $this->sql;
		$q_str="SELECT po_ship_link.po_id, stock_inventory.qty, stock_inventory.ord_num
				FROM stock_inventory,po_ship_link
				WHERE stock_inventory.type='i' and stock_inventory.ship_inv = '".$ship_inv."' and stock_inventory.po_num='".$po_num."' and stock_inventory.mat_id=".$mat_id." and 
					  stock_inventory.color='".$color."' and stock_inventory.size='".$size."' and stock_inventory.ship_link_id = po_ship_link.id";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = array();
		while($row1 = $sql->fetch($q_result))
		{
			$row[] = $row1;
		}
		
		return $row;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_rcv_apb($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_rcv_apb($apb_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$apb_num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# apb_det
	$q_str = "SELECT distinct ship_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$apb_num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$op['apb_det'][] = $row;
	}
	
	for($k=0;$k<sizeof($op['apb_det']);$k++){
        $amt = 0;
		# apb_det
		$q_str = "SELECT apb_det.*, ap.po_num, ap.toler
				  FROM apb_det, ap
				  WHERE apb_det.rcv_num = '".$apb_num."' and apb_det.ship_num = '".$op['apb_det'][$k]['ship_num']."'
						and apb_det.ap_num = ap.ap_num";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($det_row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			apb_po_link.id, apb_po_link.po_id, apb_po_link.rcv_id, apb_po_link.qty, apb_po_link.rate, apb_po_link.ord_num, ap_det.po_qty
			FROM apb_po_link, ap_det
			WHERE apb_po_link.rcv_id = '".$det_row['id']."' and ap_det.id = apb_po_link.po_id";
			
			$link_res = $sql->query($q_str);
			$order = array();
            $link = array();
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$link[] = $rows;
				$det_row['rate'] = $rows['rate'];
			}
			$det_row['link'] = $link;
			$order = array_unique($order);
			foreach($order as $key)
				$det_row['order'][] = $key;
			
			$toler = explode("|",$det_row['toler']);
			$det_row['toleri'] = $toler[0];
			$det_row['tolern'] = $toler[1];
			
			$det_row['mat_cat'] = substr($det_row['mat_code'],0,1)=="F"?"l":"a";
			
			# po資料
			$det_row['po'] = $this->get_group_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 計算 tolerance
			$max_qty = (1 + $det_row['toleri'] * 0.01) * $det_row['po']['po_qty'];
			if($det_row['po']['po_unit'] == "pc" or $det_row['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			$det_row['max_qty'] = $max_qty;
			# 驗收資料
			$det_row['rcv_qty'] = $this->get_group_inventory($det_row['ship_num'],$det_row['po_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 已付款數量
			$apbed_qty = $this->get_apbed_qty($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size'],"3,4","and apb_det.id <> '".$det_row['id']."'");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$det_row['apb_qty'] = $apbed_qty;
			
			if( $det_row['rcv_qty'] + $det_row['apb_qty'] > $max_qty ){
				$det_row['toler_qty'] = $max_qty - $det_row['apb_qty'];
			}else{
				$det_row['toler_qty'] = $det_row['rcv_qty'];
			}
			
			$det_row['mark'] = $det_row['ap_num'].$det_row['mat_id'].$det_row['color'].$det_row['size'];
			$det_row['i'] = $i;
			$op['apb_det'][$k]['det'][$i] = $det_row;
            
			/* #單價轉換
			$c_unit = $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == '' ? $op['apb_det'][$k]['det'][$i]['po']['unit'] : $op['apb_det'][$k]['det'][$i]['po']['po_unit'];
			$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$c_unit,$op['apb_det'][$k]['det'][$i]['po']['prics']);
			$op['apb_det'][$k]['det'][$i]['uprices'] = $ORI_price; */
			
			#小計金額 inv_price
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
			
            $i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$apb_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}

	#apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_rcv_oth_cost($apb_num,"4");
	
	/* # 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['apb_det'][0]['det'][0]['po_num'].$op['apb_det'][0]['det'][0]['mat_id'].$op['apb_det'][0]['det'][0]['color'].$op['apb_det'][0]['det'][0]['size']]= $op['apb_det'][0]['det'][0]['qty'];
	
	$rcv_size = sizeof($op['apb_det']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['apb_det'][$i]['det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['apb_det'][$i]['det'][$j]['po_num'].$op['apb_det'][$i]['det'][$j]['mat_id'].$op['apb_det'][$i]['det'][$j]['color'].$op['apb_det'][$i]['det'][$j]['size'];
					if($str <> $key){
						$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['qty'];
					}else{
						if( $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty > $op['apb_det'][$i]['det'][$j]['max_qty'] ){
							$op['apb_det'][$i]['det'][$j]['apb_qty'] = $op['apb_det'][$i]['det'][$j]['max_qty'] - $qty;
							$op['apb_det'][$i]['det'][$j]['toler_qty'] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
							$tmp_price = number_format($op['apb_det'][$i]['det'][$j]['uprice']*$op['apb_det'][$i]['det'][$j]['apb_qty'],2,'','');
							$div_price = $op['apb_det'][$i]['det'][$j]['price'] - $tmp_price;
							$op['apb_det'][$i]['det'][$j]['price'] = $tmp_price;
							$op['apb_det'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
						}else{
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty;
						}
						break;
					}
				}
			}
		}
	} */
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_after_rcv()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_after_rcv($dm_way) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT distinct sup_code, supl.country, supl.supl_s_name as s_name
                 FROM ap left join supl on ap.sup_code = supl.vndr_no left join stock_inventory on stock_inventory.po_num = ap.po_num";
    
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("supl.s_name ASC");
    $srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 	}

    //2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    //部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
		if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
    //部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}
	
	if( $str = $argv['PHP_po']){
		$srh->add_where_condition("`ap`.`po_num` like '%$str%'");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`stock_inventory`.`ship_inv` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}
	
	$srh->add_where_condition("ap.status = 12");
	$srh->add_where_condition("ap.dm_way like '".$dm_way."'");
	$srh->add_where_condition("ap.po_date >= '2015-01-01'");
	$srh->add_where_condition("stock_inventory.type = 'i'");
	$srh->add_where_condition("stock_inventory.apb_num = ''");
	$srh->add_where_condition("stock_inventory.qty >= stock_inventory.apb_qty");
	
	$srh->add_group_condition("ap.sup_code");	
	
	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
        echo $srh->q_str.'<br>';
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
		echo $srh->q_str.'<br>';
		if(!$limit_entries){ 
		##--*****--2006.11.16頁碼新增 start			
				$op['maxpage'] =$srh->get_max_page();
				$op['pages'] = $pages;
				$op['now_pp'] = $srh->now_pp;
			$op['lastpage']=$pages[$pagesize-1];		
		##--*****--2006.11.16頁碼新增 end
		}	
		return $op;
}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_after_inventory()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_after_inventory($sup_code) {
	$sql = $this->sql;

	$q_str = "select distinct stock_inventory.ship_id as ship_num
			  from stock_inventory, ap
			  where stock_inventory.type = 'i' and stock_inventory.po_num = ap.po_num and ap.sup_code= '$sup_code' and ap.dm_way = 'T/T after shipment'
					and ap.status=12 and ap.po_date >= '2012-06-01' and stock_inventory.apb_num = ''
			  order by stock_inventory.ship_id desc";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$chk_qty_ary = array();
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	$count_i=0;
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
		$ship_num = $row['ship_num'];
		# stock_inventory
		$q_str = "SELECT stock_inventory.invoice_num, sum(stock_inventory.qty) as qty, stock_inventory.color, stock_inventory.size, stock_inventory.po_num, stock_inventory.mat_cat, 
						 stock_inventory.mat_id, ap.ap_num, ap.toler, ap.currency
				  FROM stock_inventory, ap
				  WHERE stock_inventory.type = 'i' and stock_inventory.ship_id='$ship_num' and stock_inventory.apb_num = ''
						and ap.po_num = stock_inventory.po_num and ap.dm_way = 'T/T after shipment'
				  GROUP BY stock_inventory.po_num, stock_inventory.mat_id, stock_inventory.color, stock_inventory.size";
		
		$stock_result = $sql->query($q_str);
		$p=0;
		$amt = 0; //計算金額
		
		while ($stock_row = $sql->fetch($stock_result)) {
			$toler = explode("|",$stock_row['toler']);
			$stock_row['toleri'] = $toler[0];
			$stock_row['tolern'] = $toler[1];
			$op['rcv'][$r]['rcv_det'][$p] = $stock_row;
			
			# 沒有po_id，用mark取代，用來判斷是哪一筆
			$op['rcv'][$r]['rcv_det'][$p]['mark'] = $stock_row['po_num'].$stock_row['mat_id'].$stock_row['color'].$stock_row['size'];
			
			$ap_det_row = $this->get_group_det($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'] = $ap_det_row['po_qty'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $ap_det_row['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['unit'] = $ap_det_row['unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = $ap_det_row['po_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'] = $ap_det_row['prc_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['i'] = $p;
			
			if($stock_row['mat_cat'] == 'a'){
				$mat_sname = $GLOBALS['acc']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['acc_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['acc_code'];
			}else{
				$mat_sname = $GLOBALS['lots']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['lots_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['lots_code'];
			}
			
			# 原幣單價轉換
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == '' ? $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['unit'] : $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'];
			$chg_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$c_unit,$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprices'] = $op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $chg_price;
			
			$apbed_qty = $this->get_apbed_qty($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size'],"5,6","");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$op['rcv'][$r]['rcv_det'][$p]['apbed_qty'] = $apbed_qty;
			
			# 計算 tolerance
			$max_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'];
			if($op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "pc" or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			
			$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $max_qty;
			$op['rcv'][$r]['rcv_det'][$p]['max_qty'] = $max_qty;
			
			if( $stock_row['qty'] + $apbed_qty <= $max_qty ){
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $stock_row['qty'];
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $stock_row['qty'];
			}else{
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = number_format($max_qty - $apbed_qty,2,'','');
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $op['rcv'][$r]['rcv_det'][$p]['apb_qty'];
				# 數量有變，所以要重新分配
				// $op['rcv'][$r]['rcv_det'][$p]['orders'] = $this->re_avg_qty($op['rcv'][$r]['rcv_det'][$p]['apb_qty'], $ap_det_row['po_qty'], $num, $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			}
			
			$op['rcv'][$r]['rcv_det'][$p]['price'] = number_format($op['rcv'][$r]['rcv_det'][$p]['po']['uprices']*$op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
			$amt += $op['rcv'][$r]['rcv_det'][$p]['price'];
			
			# 訂單號碼、數量 (平分數量)
			$ord_ary = array();
			$ord_ary = $this->re_avg_qty($op['rcv'][$r]['rcv_det'][$p]['apb_qty'], $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'], $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['orders'] = $ord_ary;
			
			$p++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}
	
	# 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['rcv'][0]['rcv_det'][0]['po_num'].$op['rcv'][0]['rcv_det'][0]['mat_id'].$op['rcv'][0]['rcv_det'][0]['color'].$op['rcv'][0]['rcv_det'][0]['size']] = $op['rcv'][0]['rcv_det'][0]['apb_qty'];
	$op['rcv'][0]['rcv_det'][0]['over_qty_chk'] = 0;
	$rcv_size = sizeof($op['rcv']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['rcv'][$i]['rcv_det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['rcv'][$i]['rcv_det'][$j]['po_num'].$op['rcv'][$i]['rcv_det'][$j]['mat_id'].$op['rcv'][$i]['rcv_det'][$j]['color'].$op['rcv'][$i]['rcv_det'][$j]['size'];
					$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 0;
					if($str <> $key){
						$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
					}else{
						if( $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty > $op['rcv'][$i]['rcv_det'][$j]['max_qty'] ){
							$op['rcv'][$i]['rcv_det'][$j]['apb_qty'] = $op['rcv'][$i]['rcv_det'][$j]['max_qty'] - $qty;
							$op['rcv'][$i]['rcv_det'][$j]['toler_qty'] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$tmp_price = number_format($op['rcv'][$i]['rcv_det'][$j]['po']['uprices']*$op['rcv'][$i]['rcv_det'][$j]['apb_qty'],2,'','');
							$div_price = $op['rcv'][$i]['rcv_det'][$j]['price'] - $tmp_price;
							$op['rcv'][$i]['rcv_det'][$j]['price'] = $tmp_price;
							$op['rcv'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 1;
							# apb數量 不等於 驗收數量，所以重新分配
							$op['rcv'][$i]['rcv_det'][$j]['orders'] = $this->re_avg_qty($op['rcv'][$i]['rcv_det'][$j]['apb_qty'], $op['rcv'][$i]['rcv_det'][$j]['po']['po_qty'], $op['rcv'][$i]['rcv_det'][$j]['po_num'], $op['rcv'][$i]['rcv_det'][$j]['mat_id'], $op['rcv'][$i]['rcv_det'][$j]['color'], $op['rcv'][$i]['rcv_det'][$j]['size']);
						}else{
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty;
						}
					}
				}
			}
		}
	}
	
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_after_apb($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_after_apb($apb_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$apb_num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# apb_det
	$q_str = "SELECT distinct ship_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$apb_num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$op['apb_det'][] = $row;
	}
	
	for($k=0;$k<sizeof($op['apb_det']);$k++){
        $amt = 0;
		# apb_det
		$q_str = "SELECT apb_det.*, ap.po_num, ap.toler
				  FROM apb_det, ap
				  WHERE apb_det.rcv_num = '".$apb_num."' and apb_det.ship_num = '".$op['apb_det'][$k]['ship_num']."'
						and apb_det.ap_num = ap.ap_num";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($det_row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			apb_po_link.id, apb_po_link.po_id, apb_po_link.rcv_id, apb_po_link.qty, apb_po_link.rate, apb_po_link.ord_num, ap_det.po_qty
			FROM apb_po_link, ap_det
			WHERE apb_po_link.rcv_id = '".$det_row['id']."' and ap_det.id = apb_po_link.po_id";
			
			$link_res = $sql->query($q_str);
			$order = array();
            $link = array();
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$link[] = $rows;
				$det_row['rate'] = $rows['rate'];
			}
			$det_row['link'] = $link;
			$order = array_unique($order);
			foreach($order as $key)
				$det_row['order'][] = $key;
			
			$toler = explode("|",$det_row['toler']);
			$det_row['toleri'] = $toler[0];
			$det_row['tolern'] = $toler[1];
			
			$det_row['mat_cat'] = substr($det_row['mat_code'],0,1)=="F"?"l":"a";
			
			# po資料
			$det_row['po'] = $this->get_group_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 計算 tolerance
			$max_qty = (1 + $det_row['toleri'] * 0.01) * $det_row['po']['po_qty'];
			if($det_row['po']['po_unit'] == "pc" or $det_row['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			$det_row['max_qty'] = $max_qty;
			# 驗收資料
			$det_row['rcv_qty'] = $this->get_group_inventory($det_row['ship_num'],$det_row['po_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 已付款數量
			$apbed_qty = $this->get_apbed_qty($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size'],"5,6","and apb_det.id <> '".$det_row['id']."'");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$det_row['apb_qty'] = $apbed_qty;
			
			if( $det_row['rcv_qty'] + $det_row['apb_qty'] > $max_qty ){
				$det_row['toler_qty'] = $max_qty - $det_row['apb_qty'];
			}else{
				$det_row['toler_qty'] = $det_row['rcv_qty'];
			}
			
			$det_row['mark'] = $det_row['ap_num'].$det_row['mat_id'].$det_row['color'].$det_row['size'];
			$det_row['i'] = $i;
			$op['apb_det'][$k]['det'][$i] = $det_row;
            
			/* #單價轉換
			$c_unit = $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == '' ? $op['apb_det'][$k]['det'][$i]['po']['unit'] : $op['apb_det'][$k]['det'][$i]['po']['po_unit'];
			$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$c_unit,$op['apb_det'][$k]['det'][$i]['po']['prics']);
			$op['apb_det'][$k]['det'][$i]['uprices'] = $ORI_price; */
			
			#小計金額 inv_price
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
			
            $i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$apb_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}
	
	# ap_oth_cost 額外費用
	$op['ap_oth_cost'] = $this->get_ap_oth_cost($apb_num);
	
	# apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_rcv_oth_cost($apb_num,"6");
	
	/* # 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['apb_det'][0]['det'][0]['po_num'].$op['apb_det'][0]['det'][0]['mat_id'].$op['apb_det'][0]['det'][0]['color'].$op['apb_det'][0]['det'][0]['size']]= $op['apb_det'][0]['det'][0]['qty'];
	
	$rcv_size = sizeof($op['apb_det']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['apb_det'][$i]['det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['apb_det'][$i]['det'][$j]['po_num'].$op['apb_det'][$i]['det'][$j]['mat_id'].$op['apb_det'][$i]['det'][$j]['color'].$op['apb_det'][$i]['det'][$j]['size'];
					if($str <> $key){
						$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['qty'];
					}else{
						if( $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty > $op['apb_det'][$i]['det'][$j]['max_qty'] ){
							$op['apb_det'][$i]['det'][$j]['apb_qty'] = $op['apb_det'][$i]['det'][$j]['max_qty'] - $qty;
							$op['apb_det'][$i]['det'][$j]['toler_qty'] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
							$tmp_price = number_format($op['apb_det'][$i]['det'][$j]['uprice']*$op['apb_det'][$i]['det'][$j]['apb_qty'],2,'','');
							$div_price = $op['apb_det'][$i]['det'][$j]['price'] - $tmp_price;
							$op['apb_det'][$i]['det'][$j]['price'] = $tmp_price;
							$op['apb_det'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
						}else{
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty;
						}
						break;
					}
				}
			}
		}
	} */
	
	return $op;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lc_rcv()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_lc_rcv($dm_way) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT distinct sup_code, supl.country, supl.supl_s_name as s_name
					 FROM ap left join supl on ap.sup_code = supl.vndr_no left join stock_inventory on stock_inventory.po_num = ap.po_num";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("supl.s_name ASC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 	}


	//2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
		if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}
	
	if( $str = $argv['PHP_po']){
		$srh->add_where_condition("`ap`.`po_num` like '%$str%'");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`stock_inventory`.`ship_inv` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}
	
	$srh->add_where_condition("ap.status = 12");
	$srh->add_where_condition("ap.dm_way like '".$dm_way."'");
	$srh->add_where_condition("ap.po_date >= '2012-08-01'");
	$srh->add_where_condition("stock_inventory.type = 'i'");
	$srh->add_where_condition("stock_inventory.apb_num = ''");
	
	$srh->add_group_condition("ap.sup_code");	
	
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
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lc_inventory()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_lc_inventory($sup_code) {
	$sql = $this->sql;

	$q_str = "select distinct stock_inventory.ship_id as ship_num
			  from stock_inventory, ap
			  where stock_inventory.type = 'i' and stock_inventory.po_num = ap.po_num and ap.sup_code= '$sup_code' and ap.dm_way = 'L/C at sight'
					and ap.status=12 and ap.po_date >= '2012-06-01' and stock_inventory.apb_num = ''
			  order by stock_inventory.ship_id desc";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$chk_qty_ary = array();
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	$count_i=0;
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
		$num = $row['ship_num'];
		# stock_inventory
		$q_str = "SELECT stock_inventory.open_date, stock_inventory.invoice_num, sum(stock_inventory.qty) as qty, stock_inventory.color, stock_inventory.size, 
						 stock_inventory.po_num, stock_inventory.mat_cat, stock_inventory.mat_id, ap.ap_num, ap.toler, ap.currency
				  FROM stock_inventory, ap
				  WHERE stock_inventory.type = 'i' and stock_inventory.ship_id='$num' and stock_inventory.apb_num = ''
						and ap.po_num = stock_inventory.po_num and ap.dm_way = 'L/C at sight'
				  GROUP BY stock_inventory.po_num, stock_inventory.mat_id, stock_inventory.color, stock_inventory.size";
		
		$stock_result = $sql->query($q_str);
		$p=0;
		$amt = 0; //計算金額
		
		while ($stock_row = $sql->fetch($stock_result)) {
			$toler = explode("|",$stock_row['toler']);
			$stock_row['toleri'] = $toler[0];
			$stock_row['tolern'] = $toler[1];
			$op['rcv'][$r]['rcv_det'][$p] = $stock_row;
			
			# 沒有po_id，用mark取代，用來判斷是哪一筆
			$op['rcv'][$r]['rcv_det'][$p]['mark'] = $stock_row['po_num'].$stock_row['mat_id'].$stock_row['color'].$stock_row['size'];
			
			$ap_det_row = $this->get_group_det($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'] = $ap_det_row['po_qty'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $ap_det_row['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['unit'] = $ap_det_row['unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = $ap_det_row['po_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'] = $ap_det_row['prc_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['i'] = $p;
			
			if($stock_row['mat_cat'] == 'a'){
				$mat_sname = $GLOBALS['acc']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['acc_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['acc_code'];
			}else{
				$mat_sname = $GLOBALS['lots']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['lots_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['lots_code'];
			}
			
			# 訂單號碼、數量 (平分數量)
			$ord_ary = array();
			$ord_ary = $this->re_avg_qty($stock_row['qty'], $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'], $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['orders'] = $ord_ary;
			
			# 原幣單價轉換
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == '' ? $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['unit'] : $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'];
			$chg_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$c_unit,$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprices'] = $op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $chg_price;
			
			$apbed_qty = $this->get_apbed_qty($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size'],"7,8","");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$op['rcv'][$r]['rcv_det'][$p]['apbed_qty'] = $apbed_qty;
			
			# 計算 tolerance
			$max_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'];
			if($op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "pc" or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			
			$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $max_qty;
			$op['rcv'][$r]['rcv_det'][$p]['max_qty'] = $max_qty;
			
			if( $stock_row['qty'] + $apbed_qty <= $max_qty ){
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $stock_row['qty'];
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $stock_row['qty'];
			}else{
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = number_format($max_qty - $apbed_qty,2,'','');
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $op['rcv'][$r]['rcv_det'][$p]['apb_qty'];
				# 數量有變，所以要重新分配
				$op['rcv'][$r]['rcv_det'][$p]['orders'] = $this->re_avg_qty($op['rcv'][$r]['rcv_det'][$p]['apb_qty'], $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'], $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			}
			
			$op['rcv'][$r]['rcv_det'][$p]['price'] = number_format($op['rcv'][$r]['rcv_det'][$p]['po']['uprices']*$op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
			
			$amt += $op['rcv'][$r]['rcv_det'][$p]['price'];
			$p++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}
	
	# 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['rcv'][0]['rcv_det'][0]['po_num'].$op['rcv'][0]['rcv_det'][0]['mat_id'].$op['rcv'][0]['rcv_det'][0]['color'].$op['rcv'][0]['rcv_det'][0]['size']]= $op['rcv'][0]['rcv_det'][0]['apb_qty'];
	$op['rcv'][0]['rcv_det'][0]['over_qty_chk'] = 0;
	$rcv_size = sizeof($op['rcv']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['rcv'][$i]['rcv_det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['rcv'][$i]['rcv_det'][$j]['po_num'].$op['rcv'][$i]['rcv_det'][$j]['mat_id'].$op['rcv'][$i]['rcv_det'][$j]['color'].$op['rcv'][$i]['rcv_det'][$j]['size'];
					$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 0;
					if($str <> $key){
						$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
					}else{
						if( $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty > $op['rcv'][$i]['rcv_det'][$j]['max_qty'] ){
							$op['rcv'][$i]['rcv_det'][$j]['apb_qty'] = $op['rcv'][$i]['rcv_det'][$j]['max_qty'] - $qty;
							$op['rcv'][$i]['rcv_det'][$j]['toler_qty'] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$tmp_price = number_format($op['rcv'][$i]['rcv_det'][$j]['po']['uprices']*$op['rcv'][$i]['rcv_det'][$j]['apb_qty'],2,'','');
							$div_price = $op['rcv'][$i]['rcv_det'][$j]['price'] - $tmp_price;
							$op['rcv'][$i]['rcv_det'][$j]['price'] = $tmp_price;
							$op['rcv'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 1;
							# apb數量 不等於 驗收數量，所以重新分配
							$op['rcv'][$i]['rcv_det'][$j]['orders'] = $this->re_avg_qty($op['rcv'][$i]['rcv_det'][$j]['apb_qty'],$op['rcv'][$i]['rcv_det'][$j]['po']['po_qty'],$num,$op['rcv'][$i]['rcv_det'][$j]['po_num'],$op['rcv'][$i]['rcv_det'][$j]['mat_id'],$op['rcv'][$i]['rcv_det'][$j]['color'],$op['rcv'][$i]['rcv_det'][$j]['size']);
						}else{
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty;
						}
					}
				}
			}
		}
	}
	
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lc_apb($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_lc_apb($apb_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$apb_num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# apb_det
	$q_str = "SELECT distinct ship_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$apb_num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$op['apb_det'][] = $row;
	}
	
	for($k=0;$k<sizeof($op['apb_det']);$k++){
        $amt = 0;
		# apb_det
		$q_str = "SELECT apb_det.*, ap.po_num, ap.toler
				  FROM apb_det, ap
				  WHERE apb_det.rcv_num = '".$apb_num."' and apb_det.ship_num = '".$op['apb_det'][$k]['ship_num']."'
						and apb_det.ap_num = ap.ap_num";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($det_row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			apb_po_link.id, apb_po_link.po_id, apb_po_link.rcv_id, apb_po_link.qty, apb_po_link.rate, apb_po_link.ord_num, ap_det.po_qty
			FROM apb_po_link, ap_det
			WHERE apb_po_link.rcv_id = '".$det_row['id']."' and ap_det.id = apb_po_link.po_id";
			
			$link_res = $sql->query($q_str);
			$order = array();
            $link = array();
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$link[] = $rows;
				$det_row['rate'] = $rows['rate'];
			}
			$det_row['link'] = $link;
			$order = array_unique($order);
			foreach($order as $key)
				$det_row['order'][] = $key;
			
			$toler = explode("|",$det_row['toler']);
			$det_row['toleri'] = $toler[0];
			$det_row['tolern'] = $toler[1];
			
			$det_row['mat_cat'] = substr($det_row['mat_code'],0,1)=="F"?"l":"a";
			
			# po資料
			$det_row['po'] = $this->get_group_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 計算 tolerance
			$max_qty = (1 + $det_row['toleri'] * 0.01) * $det_row['po']['po_qty'];
			if($det_row['po']['po_unit'] == "pc" or $det_row['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			$det_row['max_qty'] = $max_qty;
			# 驗收資料
			$det_row['rcv_qty'] = $this->get_group_inventory($det_row['ship_num'],$det_row['po_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 已付款數量
			$apbed_qty = $this->get_apbed_qty($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size'],"7,8","and apb_det.id <> '".$det_row['id']."'");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$det_row['apb_qty'] = $apbed_qty;
			
			if( $det_row['rcv_qty'] + $det_row['apb_qty'] > $max_qty ){
				$det_row['toler_qty'] = $max_qty - $det_row['apb_qty'];
			}else{
				$det_row['toler_qty'] = $det_row['rcv_qty'];
			}
			
			$det_row['mark'] = $det_row['ap_num'].$det_row['mat_id'].$det_row['color'].$det_row['size'];
			$det_row['i'] = $i;
			$op['apb_det'][$k]['det'][$i] = $det_row;
            
			/* #單價轉換
			$c_unit = $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == '' ? $op['apb_det'][$k]['det'][$i]['po']['unit'] : $op['apb_det'][$k]['det'][$i]['po']['po_unit'];
			$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$c_unit,$op['apb_det'][$k]['det'][$i]['po']['prics']);
			$op['apb_det'][$k]['det'][$i]['uprices'] = $ORI_price; */
			
			#小計金額 inv_price
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
			
            $i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$apb_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}

	# ap_oth_cost 額外費用
	$op['ap_oth_cost'] = $this->get_ap_oth_cost($apb_num);
	# apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_rcv_oth_cost($apb_num,"8");
	
	/* # 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['apb_det'][0]['det'][0]['po_num'].$op['apb_det'][0]['det'][0]['mat_id'].$op['apb_det'][0]['det'][0]['color'].$op['apb_det'][0]['det'][0]['size']]= $op['apb_det'][0]['det'][0]['qty'];
	
	$rcv_size = sizeof($op['apb_det']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['apb_det'][$i]['det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['apb_det'][$i]['det'][$j]['po_num'].$op['apb_det'][$i]['det'][$j]['mat_id'].$op['apb_det'][$i]['det'][$j]['color'].$op['apb_det'][$i]['det'][$j]['size'];
					if($str <> $key){
						$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['qty'];
					}else{
						if( $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty > $op['apb_det'][$i]['det'][$j]['max_qty'] ){
							$op['apb_det'][$i]['det'][$j]['apb_qty'] = $op['apb_det'][$i]['det'][$j]['max_qty'] - $qty;
							$op['apb_det'][$i]['det'][$j]['toler_qty'] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
							$tmp_price = number_format($op['apb_det'][$i]['det'][$j]['uprice']*$op['apb_det'][$i]['det'][$j]['apb_qty'],2,'','');
							$div_price = $op['apb_det'][$i]['det'][$j]['price'] - $tmp_price;
							$op['apb_det'][$i]['det'][$j]['price'] = $tmp_price;
							$op['apb_det'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
						}else{
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty;
						}
						break;
					}
				}
			}
		}
	} */
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_month_fty_rcv()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_month_fty_rcv($dm_way) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT distinct sup_code, supl.country, supl.supl_s_name as s_name
					 FROM ap left join supl on ap.sup_code = supl.vndr_no left join stock_inventory on stock_inventory.po_num = ap.po_num";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("supl.s_name ASC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 	}


	//2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
		if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}
	
	if( $str = $argv['PHP_po']){
		$srh->add_where_condition("`ap`.`po_num` like '%$str%'");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`stock_inventory`.`ship_inv` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}
	
	$srh->add_where_condition("ap.status = 12");
	$srh->add_where_condition("ap.dm_way like '".$dm_way."%'");
	$srh->add_where_condition("ap.po_date >= '2012-08-01'");
	$srh->add_where_condition("stock_inventory.type = 'i'");
	$srh->add_where_condition("stock_inventory.apb_num = ''");
	
	$srh->add_group_condition("ap.sup_code");	
	
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
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_month_fty_inventory()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_month_fty_inventory($sup_code) {
	$sql = $this->sql;

	$q_str = "select distinct stock_inventory.ship_id as ship_num
			  from stock_inventory, ap
			  where stock_inventory.type = 'i' and stock_inventory.po_num = ap.po_num and ap.sup_code= '$sup_code' and ap.dm_way LIKE '月結%'
					and ap.status=12 and ap.po_date >= '2012-06-01' and stock_inventory.apb_num = ''
			  order by stock_inventory.ship_id desc";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$chk_qty_ary = array();
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	$count_i=0;
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
		$num = $row['ship_num'];
		# stock_inventory
		$q_str = "SELECT stock_inventory.open_date, stock_inventory.invoice_num, sum(stock_inventory.qty) as qty, stock_inventory.color, stock_inventory.size, stock_inventory.po_num, stock_inventory.mat_cat, 
						 stock_inventory.mat_id, ap.ap_num, ap.toler, ap.currency
				  FROM stock_inventory, ap
				  WHERE stock_inventory.type = 'i' and stock_inventory.ship_id='$num' and stock_inventory.apb_num = ''
						and ap.po_num = stock_inventory.po_num and ap.dm_way LIKE '月結%'
				  GROUP BY stock_inventory.po_num, stock_inventory.mat_id, stock_inventory.color, stock_inventory.size";
		
		$stock_result = $sql->query($q_str);
		$p=0;
		$amt = 0; //計算金額
		
		while ($stock_row = $sql->fetch($stock_result)) {
			$toler = explode("|",$stock_row['toler']);
			$stock_row['toleri'] = $toler[0];
			$stock_row['tolern'] = $toler[1];
			$op['rcv'][$r]['rcv_det'][$p] = $stock_row;
			
			# 沒有po_id，用mark取代，用來判斷是哪一筆
			$op['rcv'][$r]['rcv_det'][$p]['mark'] = $stock_row['po_num'].$stock_row['mat_id'].$stock_row['color'].$stock_row['size'];
			
			$ap_det_row = $this->get_group_det($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'] = $ap_det_row['po_qty'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $ap_det_row['prics'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['unit'] = $ap_det_row['unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = $ap_det_row['po_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'] = $ap_det_row['prc_unit'];
			$op['rcv'][$r]['rcv_det'][$p]['po']['i'] = $p;
			
			if($stock_row['mat_cat'] == 'a'){
				$mat_sname = $GLOBALS['acc']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['acc_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['acc_code'];
			}else{
				$mat_sname = $GLOBALS['lots']->get( $stock_row['mat_id'],'' );
				$op['rcv'][$r]['rcv_det'][$p]['mat_name'] = $mat_sname['lots_name'];
				$op['rcv'][$r]['rcv_det'][$p]['mat_code'] = $mat_sname['lots_code'];
			}
			
			# 訂單號碼、數量
			$ord_ary = array();
			$ord_ary = $this->re_avg_qty($stock_row['qty'], $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'], $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			$op['rcv'][$r]['rcv_det'][$p]['orders'] = $ord_ary;
			
			# 原幣單價轉換
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == '' ? $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['unit'] : $c_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'];
			$chg_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$c_unit,$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['uprices'] = $op['rcv'][$r]['rcv_det'][$p]['po']['uprice'] = $chg_price;
			
			$apbed_qty = $this->get_apbed_qty($stock_row['ap_num'],$stock_row['mat_id'],$stock_row['color'],$stock_row['size'],"9,10","");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$op['rcv'][$r]['rcv_det'][$p]['apbed_qty'] = $apbed_qty;
			
			# 計算 tolerance
			$max_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['toleri'] * 0.01) * $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'];
			if($op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "pc" or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			
			$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $max_qty;
			$op['rcv'][$r]['rcv_det'][$p]['max_qty'] = $max_qty;
			
			if( $stock_row['qty'] + $apbed_qty <= $max_qty ){
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = $stock_row['qty'];
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $stock_row['qty'];
			}else{
				
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = number_format($max_qty - $apbed_qty,2,'','');
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $op['rcv'][$r]['rcv_det'][$p]['apb_qty'];
				# 數量有變，所以要重新分配
				$op['rcv'][$r]['rcv_det'][$p]['orders'] = $this->re_avg_qty($op['rcv'][$r]['rcv_det'][$p]['apb_qty'], $op['rcv'][$r]['rcv_det'][$p]['po']['po_qty'], $stock_row['po_num'], $stock_row['mat_id'], $stock_row['color'], $stock_row['size']);
			}
			
			$op['rcv'][$r]['rcv_det'][$p]['price'] = number_format($op['rcv'][$r]['rcv_det'][$p]['po']['uprices']*$op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
			
			$amt += $op['rcv'][$r]['rcv_det'][$p]['price'];
			$p++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}
	//print_r($op);exit;
	# 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['rcv'][0]['rcv_det'][0]['po_num'].$op['rcv'][0]['rcv_det'][0]['mat_id'].$op['rcv'][0]['rcv_det'][0]['color'].$op['rcv'][0]['rcv_det'][0]['size']]= $op['rcv'][0]['rcv_det'][0]['apb_qty'];
	$op['rcv'][0]['rcv_det'][0]['over_qty_chk'] = 0;
	$rcv_size = sizeof($op['rcv']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['rcv'][$i]['rcv_det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['rcv'][$i]['rcv_det'][$j]['po_num'].$op['rcv'][$i]['rcv_det'][$j]['mat_id'].$op['rcv'][$i]['rcv_det'][$j]['color'].$op['rcv'][$i]['rcv_det'][$j]['size'];
					$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 0;
					if($str <> $key){
						$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
					}else{
						if( $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty > $op['rcv'][$i]['rcv_det'][$j]['max_qty'] ){
							$op['rcv'][$i]['rcv_det'][$j]['apb_qty'] = $op['rcv'][$i]['rcv_det'][$j]['max_qty'] - $qty;
							$op['rcv'][$i]['rcv_det'][$j]['toler_qty'] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$tmp_price = number_format($op['rcv'][$i]['rcv_det'][$j]['po']['uprices']*$op['rcv'][$i]['rcv_det'][$j]['apb_qty'],2,'','');
							$div_price = $op['rcv'][$i]['rcv_det'][$j]['price'] - $tmp_price;
							$op['rcv'][$i]['rcv_det'][$j]['price'] = $tmp_price;
							$op['rcv'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'];
							$op['rcv'][$i]['rcv_det'][$j]['over_qty_chk'] = 1;
							# apb數量 不等於 驗收數量，所以重新分配
							$op['rcv'][$i]['rcv_det'][$j]['orders'] = $this->re_avg_qty($op['rcv'][$i]['rcv_det'][$j]['apb_qty'],$op['rcv'][$i]['rcv_det'][$j]['po']['po_qty'],$num,$op['rcv'][$i]['rcv_det'][$j]['po_num'],$op['rcv'][$i]['rcv_det'][$j]['mat_id'],$op['rcv'][$i]['rcv_det'][$j]['color'],$op['rcv'][$i]['rcv_det'][$j]['size']);
						}else{
							$chk_qty_ary[$str] = $op['rcv'][$i]['rcv_det'][$j]['apb_qty'] + $qty;
						}
					}
				}
			}
		}
	}
	
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_month_fty_apb($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_month_fty_apb($apb_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$apb_num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# apb_det
	$q_str = "SELECT distinct ship_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$apb_num."'";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$op['apb_det'][] = $row;
	}
	
	for($k=0;$k<sizeof($op['apb_det']);$k++){
        $amt = 0;
		# apb_det
		$q_str = "SELECT apb_det.*, ap.po_num, ap.toler
				  FROM apb_det, ap
				  WHERE apb_det.rcv_num = '".$apb_num."' and apb_det.ship_num = '".$op['apb_det'][$k]['ship_num']."'
						and apb_det.ap_num = ap.ap_num";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($det_row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			apb_po_link.id, apb_po_link.po_id, apb_po_link.rcv_id, apb_po_link.qty, apb_po_link.rate, apb_po_link.ord_num, ap_det.po_qty
			FROM apb_po_link, ap_det
			WHERE apb_po_link.rcv_id = '".$det_row['id']."' and ap_det.id = apb_po_link.po_id";
			
			$link_res = $sql->query($q_str);
			$order = array();
            $link = array();
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$link[] = $rows;
				$det_row['rate'] = $rows['rate'];
			}
			$det_row['link'] = $link;
			$order = array_unique($order);
			foreach($order as $key)
				$det_row['order'][] = $key;
			
			$toler = explode("|",$det_row['toler']);
			$det_row['toleri'] = $toler[0];
			$det_row['tolern'] = $toler[1];
			
			$det_row['mat_cat'] = substr($det_row['mat_code'],0,1)=="F"?"l":"a";
			
			# po資料
			$det_row['po'] = $this->get_group_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 計算 tolerance
			$max_qty = (1 + $det_row['toleri'] * 0.01) * $det_row['po']['po_qty'];
			if($det_row['po']['po_unit'] == "pc" or $det_row['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			$det_row['max_qty'] = $max_qty;
			# 驗收資料
			$det_row['rcv_qty'] = $this->get_group_inventory($det_row['ship_num'],$det_row['po_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 已付款數量
			$apbed_qty = $this->get_apbed_qty($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size'],"9,10","and apb_det.id <> '".$det_row['id']."'");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$det_row['apb_qty'] = $apbed_qty;
			
			if( $det_row['rcv_qty'] + $det_row['apb_qty'] > $max_qty ){
				$det_row['toler_qty'] = $max_qty - $det_row['apb_qty'];
			}else{
				$det_row['toler_qty'] = $det_row['rcv_qty'];
			}
			
			$det_row['mark'] = $det_row['ap_num'].$det_row['mat_id'].$det_row['color'].$det_row['size'];
			$det_row['i'] = $i;
			$op['apb_det'][$k]['det'][$i] = $det_row;
            
			/* #單價轉換
			$c_unit = $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == '' ? $op['apb_det'][$k]['det'][$i]['po']['unit'] : $op['apb_det'][$k]['det'][$i]['po']['po_unit'];
			$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$c_unit,$op['apb_det'][$k]['det'][$i]['po']['prics']);
			$op['apb_det'][$k]['det'][$i]['uprices'] = $ORI_price; */
			
			#小計金額 inv_price
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
			
            $i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$apb_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}
	
	# ap_oth_cost 額外費用
	$op['ap_oth_cost'] = $this->get_ap_oth_cost($apb_num);
	# apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_rcv_oth_cost($apb_num,"10");
	
	/* # 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['apb_det'][0]['det'][0]['po_num'].$op['apb_det'][0]['det'][0]['mat_id'].$op['apb_det'][0]['det'][0]['color'].$op['apb_det'][0]['det'][0]['size']]= $op['apb_det'][0]['det'][0]['qty'];
	
	$rcv_size = sizeof($op['apb_det']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['apb_det'][$i]['det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['apb_det'][$i]['det'][$j]['po_num'].$op['apb_det'][$i]['det'][$j]['mat_id'].$op['apb_det'][$i]['det'][$j]['color'].$op['apb_det'][$i]['det'][$j]['size'];
					if($str <> $key){
						$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['qty'];
					}else{
						if( $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty > $op['apb_det'][$i]['det'][$j]['max_qty'] ){
							$op['apb_det'][$i]['det'][$j]['apb_qty'] = $op['apb_det'][$i]['det'][$j]['max_qty'] - $qty;
							$op['apb_det'][$i]['det'][$j]['toler_qty'] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
							$tmp_price = number_format($op['apb_det'][$i]['det'][$j]['uprice']*$op['apb_det'][$i]['det'][$j]['apb_qty'],2,'','');
							$div_price = $op['apb_det'][$i]['det'][$j]['price'] - $tmp_price;
							$op['apb_det'][$i]['det'][$j]['price'] = $tmp_price;
							$op['apb_det'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
						}else{
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty;
						}
						break;
					}
				}
			}
		}
	} */
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_month_tw_rcv($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_month_tw_rcv($mode=0, $dept='',$limit_entries=0) {
	$sql = $this->sql;
	$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	if($argv['PHP_po'])	{
		$po_row = $this->get_link_field("ap_num","ap","po_num like '%".$argv['PHP_po']."%'");
		$ap_str='';
		for( $i=0;$i<sizeof($po_row);$i++)
			$ap_str.="'".$po_row[$i]['ap_num']."',";
		$ap_str = substr($ap_str,0,-1);
		$q_header = "SELECT distinct receive.rcv_user, receive_det.sup_no, supl.supl_s_name as s_name , ap.dm_way
				 From receive, receive_det, supl, ap";
		$q_header = "SELECT distinct po_ship.shipper, ap.sup_code, supl.supl_s_name as s_name , ap.dm_way
				 From po_ship, po_ship_det, supl, ap";
	}else{
		$q_header = "SELECT distinct receive.rcv_user, receive_det.sup_no, supl.supl_s_name as s_name , ap.dm_way
				 From receive left join receive_det on receive.rcv_num=receive_det.rcv_num left join supl on receive_det.sup_no=supl.vndr_no 
				 left join ap on receive_det.ap_num = ap.ap_num";

		$q_header = "SELECT distinct po_ship.shipper, ap.sup_code, supl.supl_s_name as s_name , ap.dm_way
				 From po_ship left join po_ship_det on po_ship.id=po_ship_det.ship_id left join ap on po_ship_det.ap_num = ap.ap_num
				 left join supl on ap.sup_code=supl.vndr_no ";
	}
// echo $q_header;
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	// echo $srh->q_str;
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_group_condition("`ap`.`sup_code`,`ap`.`dm_way`");
	$srh->add_sort_condition("`po_ship_det`.`id` DESC");
	$srh->row_per_page = 20;

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
	
	
	if($argv['PHP_po']){
		$srh->add_where_condition("`po_ship_det`.`ap_num` in(".$ap_str.")");
		$srh->add_where_condition("`ap`.`ap_num` in(".$ap_str.")");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`po_ship`.`ship_num` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}

	if ($str = $argv['PHP_payment'] )  {
		if (strpos($str, "%"))
			$srh->add_where_condition("`ap`.`dm_way` like '%|%'", "PHP_payment",$str,"search payment=[ $str ]. ");
		else
			$srh->add_where_condition("`ap`.`dm_way` like '%$str%'", "PHP_payment",$str,"search payment=[ $str ]. "); 
	}
	// echo $srh->q_str;
	$srh->add_where_condition("po_ship_det.apb_rmk = 0");
	$srh->add_where_condition("po_ship.id = po_ship_det.ship_id");
	$srh->add_where_condition("supl.vndr_no = ap.sup_code");
	$srh->add_where_condition("`po_ship`.`status` = '2'");
	$srh->add_where_condition("`po_ship`.`ship_date` > '2015-08-01'");
	
	if($argv['PHP_tw_rcv'])
		$srh->add_where_condition("`po_ship`.`tw_rcv` = 1");
	else
		$srh->add_where_condition("`po_ship`.`tw_rcv` = 0");
	// 這邊的時間是區隔開始使用 APB 的時間 ~ 正確是 2011-06-01 停掉王安日期
	//$srh->add_where_condition("`ap`.`po_apv_date` > '2011-01-01'");
	
	/* if($po['special'] == '2')
		$srh->add_where_condition("`ap_special`.`rcv_qty` > '0' ");
	else
		$srh->add_where_condition("`ap_det`.`rcv_qty` > '0' ");
	
	$srh->add_where_condition("`ap`.`rcv_rmk` = '0' "); */

	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	if (!is_array($result)) {
        // echo $srh->q_str;
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
	if (!$result)  $op['record_NONE'] = 1; // 當查尋無資料時
// echo $srh->q_str;
	$op['rcvd'] = $result;  // 資料錄 拋入 $op
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

	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_month_tw_det($sup_no)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_month_tw_det($sup_no='',$payment='') {
	$sql = $this->sql;
	
	$where_str = '';
	if($sup_no) $where_str.=" and ap.sup_code = '".$sup_no."' ";
	if($payment) $where_str.=" and ap.dm_way like '%".$payment."%' ";

	# 驗收主檔
	$q_str = "SELECT distinct po_ship.ship_inv, po_ship.num as rcv_num, po_ship.ship_date as rcv_date
			  From po_ship, po_ship_det,ap
			  WHERE po_ship.id = po_ship_det.ship_id and po_ship.status = 2 and po_ship.tw_rcv = 1
			  and po_ship_det.ap_num = ap.ap_num and po_ship_det.apb_rmk = 0 and po_ship.ship_date > '2015-08-01'".$where_str." order by po_ship_det.id desc";
    // echo $q_str.'<br>';
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$arr_ap_num = array();	#用來取ap的額外費用
	$po_id_qty = array();	#記錄分批驗收數量,用來判斷付款數量
	$r=0;
	
	while ($row = $sql->fetch($q_result)) {
		$op['rcv'][$r] = $row;
        
		# 驗收明細
		$q_str = "SELECT distinct po_ship_det.*, ap.po_num, ap.toler, ap.currency
				  From po_ship,po_ship_det,ap
				  WHERE po_ship.id = po_ship_det.ship_id and po_ship.ship_inv = '".$row['ship_inv']."'".$where_str." and po_ship_det.ap_num = ap.ap_num and po_ship_det.apb_rmk = 0 order by po_ship_det.id desc";
        // echo $q_str.'<br>';
		if (!$det_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$p=0;
		$amt = 0; //計算金額
		
		while ($det_row = $sql->fetch($det_result)) {
			$order = array();
			$arr_ap_num[] = $det_row['ap_num'];
			# mark是用來判斷html是哪筆資料，取代po_spare的
			$det_row['mark'] = $det_row['rcv_num'].$p;
			if($det_row['mat_cat'] == 'l')
				$mat_code_row = $this->get_det_field("lots_code as mat_code","lots","id=".$det_row['mat_id']);
			else
				$mat_code_row = $this->get_det_field("acc_code as mat_code","acc","id=".$det_row['mat_id']);
			$det_row['mat_code'] = $mat_code_row['mat_code'];
			$op['rcv'][$r]['rcv_det'][$p] = $det_row;
			
			$op['rcv'][$r]['rcv_det'][$p]['po'] = $this->get_group_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			$toler = explode("|",$det_row['toler']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] = $toler[0];
			$op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] = $toler[1];
			$TODAY = date('Y-m-d');
            
            # 該 PO SHIP 已驗收數量
            $apbed_qty = $this->get_apbed_tw_qty($det_row['ap_num'],$det_row['id'],$det_row['mat_id'],$det_row['color'],$det_row['size'],"11,12","");
            
			$q_str = "select ap_det.id as po_id, po_ship_det.qty, ap_det.order_num as ord_num, ap_det.po_qty , po_ship_det.ap_num , po_ship_det.mat_id , po_ship_det.color , po_ship_det.size , po_ship_det.apb_qty
					from po_ship_det, ap_det
					where po_ship_det.id = ".$det_row['id']." and po_ship_det.ap_num = ap_det.ap_num and po_ship_det.mat_id = ap_det.mat_id and po_ship_det.color = ap_det.color and po_ship_det.size = ap_det.size";
			// echo $q_str.'<br>';
            
			$ord_result = $sql->query($q_str);
			while($ord_row = $sql->fetch($ord_result)){
                $ord_row['qty'] = ($ord_row['qty']-$det_row['apb_qty'])/($op['rcv'][$r]['rcv_det'][$p]['po']['po_qty']/$ord_row['po_qty']);
				$op['rcv'][$r]['rcv_det'][$p]['link_qty'][] = $ord_row;
			}
			
			$rate = 1;
			$op['rcv'][$r]['rcv_det'][$p]['po']['rate'] = $rate;
			$op['rcv'][$r]['rcv_det'][$p]['po']['i']=$p;
			
			#計算 tolerance
			$chk_qty = (1 + $op['rcv'][$r]['rcv_det'][$p]['po']['toleri'] * 0.01) * $det_row['qty']-$det_row['apb_qty'];
			// $min_qty = (1 - $op['rcv'][$r]['rcv_det'][$p]['po']['tolern'] * 0.01) * $det_row['qty'];
			if ( $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == 'pc' or $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] == 'K pcs' )
				$chk_qty = ceil($chk_qty);
			$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = $chk_qty;
            
			#單價轉換
			$op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'] = '' ? $po_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['unit'] : $po_unit = $op['rcv'][$r]['rcv_det'][$p]['po']['po_unit'];
			$ORI_price = change_unit_price($op['rcv'][$r]['rcv_det'][$p]['po']['prc_unit'],$po_unit,$op['rcv'][$r]['rcv_det'][$p]['po']['prics']);
			$op['rcv'][$r]['rcv_det'][$p]['po']['prics'] = $ORI_price;
			
			// $rmk_qty = $this->check_qty($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			// if($rmk_qty == null) $rmk_qty = 0;
			
			if($apbed_qty == null) $apbed_qty = 0;
			// echo $det_row['qty'].' + '.$apbed_qty .' > '.$chk_qty.' apb_qty '.$det_row['apb_qty'] . '<br>';
			// echo  $det_row['qty'].' + '.$apbed_qty .' > '.$chk_qty . '<br>';
			// if ( $apbed_qty <= $chk_qty and $rmk_qty <= $chk_qty ){
			if ( $chk_qty > 0 ){
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = ($det_row['qty']+$det_row['apb_qty']) > $chk_qty ? $chk_qty : $det_row['qty'];
				$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = number_format($ORI_price * $op['rcv'][$r]['rcv_det'][$p]['apb_qty'],2,'','');
			}else{
				$op['rcv'][$r]['rcv_det'][$p]['apb_qty'] = 0;
				$op['rcv'][$r]['rcv_det'][$p]['toler_qty'] = 0;
				$op['rcv'][$r]['rcv_det'][$p]['po']['price'] = 0;
			}
			
			$amt += $op['rcv'][$r]['rcv_det'][$p]['po']['price'];
			
			$p++;
		}
		
		$op['rcv'][$r]['amount'] = number_format($amt,2,'','');
		$r++;
	}

	#額外費用
	$arr_ap_num = array_unique($arr_ap_num);
	foreach($arr_ap_num as $val){
		$q_str="SELECT * FROM `ap_oth_cost` 
				WHERE ap_num = '$val' and `apb_num` = ''";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while($row1 = $sql->fetch($q_result)) {
			$op['ap_oth_cost'][]=$row1;
		}
	}
	
	return $op;
}

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> get_apbed_tw_qty($ap_num,$mat_id,$color,$size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_apbed_tw_qty($ap_num,$receive_det_id,$mat_id,$color,$size,$where_str='') {
		$sql = $this->sql;
		
		$q_str = "SELECT sum(apb_det.qty) as apbed_qty
				  FROM apb, apb_det
				  WHERE apb_det.ap_num = '".$ap_num."' 
                  and apb_det.mat_id = ".$mat_id." 
                  and apb_det.color = '".$color."' 
                  and apb_det.size = '".$size."' 
                  and apb_det.receive_det_id = '".$receive_det_id."' 
                  and apb.rcv_num = apb_det.rcv_num 
                  and apb.status in(".$status.") ".$where_str;		
		$q_str = "SELECT sum(apb_det.qty) as apbed_qty
				  FROM apb, apb_det
				  WHERE apb_det.ap_num = '".$ap_num."' 
                  and apb_det.mat_id = ".$mat_id." 
                  and apb_det.color = '".$color."' 
                  and apb_det.size = '".$size."' 
                  and apb_det.receive_det_id = '".$receive_det_id."' 
                  and apb.rcv_num = apb_det.rcv_num 
                   ".$where_str;
		// echo $q_str.'<br>';
		$q_result = $sql->query($q_str);
		$det_row = $sql->fetch($q_result);
		if(!$det_row['apbed_qty'])
			$det_row['apbed_qty'] = 0;
		
		return $det_row['apbed_qty'];
	}
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_month_tw_apb($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_month_tw_apb($apb_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$apb_num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# apb_det
	$q_str = "SELECT distinct ship_num, rcvd_num FROM `apb_det` 
			WHERE `apb_det`.`rcv_num` = '".$apb_num."'";
	// echo $q_str.'<br>';
	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$op['apb_det'][] = $row;
	}
	
	for($k=0;$k<sizeof($op['apb_det']);$k++){
        $amt = 0;
		# apb_det
		$q_str = "SELECT apb_det.*, ap.po_num, ap.toler
				  FROM apb_det, ap
				  WHERE apb_det.rcv_num = '".$apb_num."' and apb_det.ship_num = '".$op['apb_det'][$k]['ship_num']."'
						and apb_det.ap_num = ap.ap_num";
		// echo $q_str.'<br>';
		$q_result = $sql->query($q_str);
		$i=0;
		while ($det_row = $sql->fetch($q_result)) {
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			apb_po_link.id, apb_po_link.po_id, apb_po_link.rcv_id, apb_po_link.qty, apb_po_link.rate, apb_po_link.ord_num, ap_det.po_qty
			FROM apb_po_link, ap_det
			WHERE apb_po_link.rcv_id = '".$det_row['id']."' and ap_det.id = apb_po_link.po_id";
			// echo $q_str.'<br>';
			$link_res = $sql->query($q_str);
			$order = array();
            $link = array();
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$link[] = $rows;
				$det_row['rate'] = $rows['rate'];
			}
			$det_row['link'] = $link;
			$order = array_unique($order);
			foreach($order as $key)
				$det_row['order'][] = $key;
			
			$toler = explode("|",$det_row['toler']);
			$det_row['toleri'] = $toler[0];
			$det_row['tolern'] = $toler[1];
			
			$det_row['mat_cat'] = substr($det_row['mat_code'],0,1)=="F"?"l":"a";
			
			# po資料
			$det_row['po'] = $this->get_group_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 計算 tolerance
			$max_qty = (1 + $det_row['toleri'] * 0.01) * $det_row['po']['po_qty'];
			if($det_row['po']['po_unit'] == "pc" or $det_row['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			$det_row['max_qty'] = $max_qty;
			# 驗收資料
			$det_row['rcv_qty'] = $this->get_tw_rcv_qty($det_row['receive_det_id']);
			# 已付款數量
			$apbed_qty = $this->get_apbed_qty($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size'],"5,6","and apb_det.receive_det_id <> '".$det_row['receive_det_id']."'");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$det_row['apb_qty'] = $apbed_qty;
			
			if( $det_row['rcv_qty'] + $det_row['apb_qty'] > $max_qty ){
				$det_row['toler_qty'] = $max_qty - $det_row['apb_qty'];
			}else{
				$det_row['toler_qty'] = $det_row['rcv_qty'];
			}
			
			$det_row['mark'] = $det_row['ap_num'].$det_row['mat_id'].$det_row['color'].$det_row['size'];
			$det_row['i'] = $i;
			$op['apb_det'][$k]['det'][$i] = $det_row;
            
			/* #單價轉換
			$c_unit = $op['apb_det'][$k]['det'][$i]['po']['po_unit'] == '' ? $op['apb_det'][$k]['det'][$i]['po']['unit'] : $op['apb_det'][$k]['det'][$i]['po']['po_unit'];
			$ORI_price = change_unit_price($op['apb_det'][$k]['det'][$i]['po']['prc_unit'],$c_unit,$op['apb_det'][$k]['det'][$i]['po']['prics']);
			$op['apb_det'][$k]['det'][$i]['uprices'] = $ORI_price; */
			
			#小計金額 inv_price
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
			
            $i++;
		}
		$op['apb_det'][$k]['amount'] = $amt;
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$apb_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}

	#apb_oth_cost 額外費用
	$op['apb_oth_cost'] = $this->get_rcv_oth_cost($apb_num,"12");
	$op['ap_oth_cost'] = $this->get_ap_oth_cost($apb_num);
	
	/* # 重組資料，為了判斷在同一次驗收付款時,分次驗收數量是否有超過tolerance
	$chk_qty_ary[$op['apb_det'][0]['det'][0]['po_num'].$op['apb_det'][0]['det'][0]['mat_id'].$op['apb_det'][0]['det'][0]['color'].$op['apb_det'][0]['det'][0]['size']]= $op['apb_det'][0]['det'][0]['qty'];
	
	$rcv_size = sizeof($op['apb_det']);
	for($i=0;$i<$rcv_size;$i++){
		$rcv_det_size = sizeof($op['apb_det'][$i]['det']);
		for($j=0;$j<$rcv_det_size;$j++){
			if($i<>0 or $j<>0){
				foreach($chk_qty_ary as $key => $qty){
					$str = $op['apb_det'][$i]['det'][$j]['po_num'].$op['apb_det'][$i]['det'][$j]['mat_id'].$op['apb_det'][$i]['det'][$j]['color'].$op['apb_det'][$i]['det'][$j]['size'];
					if($str <> $key){
						$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['qty'];
					}else{
						if( $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty > $op['apb_det'][$i]['det'][$j]['max_qty'] ){
							$op['apb_det'][$i]['det'][$j]['apb_qty'] = $op['apb_det'][$i]['det'][$j]['max_qty'] - $qty;
							$op['apb_det'][$i]['det'][$j]['toler_qty'] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
							$tmp_price = number_format($op['apb_det'][$i]['det'][$j]['uprice']*$op['apb_det'][$i]['det'][$j]['apb_qty'],2,'','');
							$div_price = $op['apb_det'][$i]['det'][$j]['price'] - $tmp_price;
							$op['apb_det'][$i]['det'][$j]['price'] = $tmp_price;
							$op['apb_det'][$i]['amount'] -= $div_price;
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'];
						}else{
							$chk_qty_ary[$str] = $op['apb_det'][$i]['det'][$j]['apb_qty'] + $qty;
						}
						break;
					}
				}
			}
		}
	} */
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_po_before()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_po_before($dm_way) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT sup_code, supl.country, supl.supl_s_name as s_name
								 FROM ap, supl ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("supl.s_name ASC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}


	//2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
		if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}
	
	if($str = $argv['PHP_po']){
		$srh->add_where_condition("`ap`.`po_num` like '%$str%'");
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}
	
	$srh->add_where_condition("ap.sup_code = supl.vndr_no");
	$srh->add_where_condition("ap.status = 12");
	$srh->add_where_condition("ap.dm_way like '".$dm_way."'");
	$srh->add_where_condition("ap.po_date >= '2012-08-01'");
	$srh->add_where_condition("ap.apb_rmk = 0");
	
	$srh->add_group_condition("ap.sup_code");	
	
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
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_before_after_po()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_before_after_po($sup_code,$dm_way) {
		$sql = $this->sql;

		$q_str = "select ap_num, po_num, currency, toler, dm_way, dear
				  from ap
				  where sup_code= '$sup_code' and dm_way like '$dm_way' and status=12 and apb_rmk= 0 and po_date >= '2012-08-01' order by id desc";

		if (!$ap_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$p=0;	
	while ($ap_row = $sql->fetch($ap_result)) {
		$num = $ap_row['ap_num'];
		$toler = explode('|',$ap_row['toler']);
		$ap_row['toleri'] = $toler[0];
		$ap_row['tolern'] = $toler[1];
		
		$op['ap'][$p] = $ap_row;
		
		$amt = 0;
		$ttl_qty = 0;
		$op['ap'][$p]['ord_num'] = array();
		
		# 請購明細 -- 主料		
		$q_str="SELECT ap_det.*, ap_det.order_num as ord_num, lots_use.lots_code as mat_code, 
						lots_use.lots_name as mat_name, bom_lots.color, lots.price1, lots.comp as con1, 
						lots.specify as con2, lots.des as mile, lots.lots_name as mile_name, lots.width,
						lots.weight, lots.cons as finish, bom_lots.wi_id
				FROM `ap`,`ap_det`,`bom_lots`,`lots_use`,`lots`  
				WHERE `lots`.`lots_code` = `lots_use`.`lots_code` AND `lots_use`.`id` = `bom_lots`.`lots_used_id` AND `ap_det`.`bom_id`= `bom_lots`.`id` 
				AND `ap_det`.`mat_cat` = 'l' AND `ap`.`ap_num` = `ap_det`.`ap_num` AND `ap_det`.`ap_num` = '$num' AND ap_det.apb_rmk=0";

		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$ord_mk =0;
			$ttl_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_det'][$i]=$row1;
			$op['ap'][$p]['ap_det'][$i]['i']=$i;
			if($op['ap'][$p]['ap_det'][$i]['amount'] == 0)$op['ap'][$p]['ap_det'][$i]['amount']=$op['ap'][$p]['ap_det'][$i]['po_qty'] * $op['ap'][$p]['ap_det'][$i]['prics'];
			
			$amt += $op['ap'][$p]['ap_det'][$i]['amount'];
			if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];
			
			$i++;
		}

		# 請購明細 -- 副料
		$q_str="SELECT ap_det.*, ap_det.order_num as ord_num, acc_use.acc_code as mat_code, 
						acc_use.acc_name as mat_name, bom_acc.color, acc.price1, acc.des as con1, 
							acc.specify as con2, acc.mile_code as mile, acc.mile_name, bom_acc.wi_id
				FROM `ap_det`, bom_acc,acc_use, acc  
				WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id AND bom_id=bom_acc.id AND mat_cat = 'a' AND `ap_num` = '$num' 
						AND ap_det.apb_rmk=0";
		
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row1 = $sql->fetch($q_result)) {
			$ttl_qty += $row1['po_qty'];
			$op['ap'][$p]['ap_det'][$i]=$row1;
			$op['ap'][$p]['ap_det'][$i]['i']=$i;
			if($op['ap'][$p]['ap_det'][$i]['amount'] == 0)$op['ap'][$p]['ap_det'][$i]['amount']=$op['ap'][$p]['ap_det'][$i]['po_qty']*$op['ap'][$p]['ap_det'][$i]['prics'];
			
			$amt += $op['ap'][$p]['ap_det'][$i]['amount'];
			if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];
			$i++;
		} 	
		
			
		//Remark項目
		$op['ap'][$p]['apply_log'] = array();
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='remark'";
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
			$op['ap'][$p]['apply_log'][]=$row1;
		}

		//特殊採購原因(Remark)
		$op['ap'][$p]['apply_special'] = array ();		
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='special'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
			$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];			
			$op['ap'][$p]['apply_special'][]=$row1;
		}			

		if(isset($op['ap'][$p]['ap_det'])) $op['ap'][$p]['ap_det'] = $GLOBALS['po']->grout_ap($op['ap'][$p]['ap_det']);
		
		# ap_det 貨前數量 、 金額小計 及 台幣單價
		for($ap_s=0;$ap_s<sizeof($op['ap'][$p]['ap_det']);$ap_s++){
			$rate=1;
			$op['ap'][$p]['rate'] = $rate;
			$op['ap'][$p]['ap_det'][$ap_s]['uprice'] = $op['ap'][$p]['ap_det'][$ap_s]['prics'] * $rate;
			
			#原幣單價轉換
			$op['ap'][$p]['ap_det'][$ap_s]['po_unit'] == '' ? $c_unit=$op['ap'][$p]['ap_det'][$ap_s]['unit'] : $c_unit=$op['ap'][$p]['ap_det'][$ap_s]['po_unit'];
			$chg_price = change_unit_price($op['ap'][$p]['ap_det'][$ap_s]['prc_unit'],$c_unit,$op['ap'][$p]['ap_det'][$ap_s]['prics']);
			$op['ap'][$p]['ap_det'][$ap_s]['uprice'] = $chg_price;
			$apbed_qty = $this->get_apbed_qty($op['ap'][$p]['ap_det'][$ap_s]['ap_num'],$op['ap'][$p]['ap_det'][$ap_s]['mat_id'],$op['ap'][$p]['ap_det'][$ap_s]['color'],$op['ap'][$p]['ap_det'][$ap_s]['size'],"13,14","");
			if($apbed_qty == null) $apbed_qty = 0;
			$op['ap'][$p]['ap_det'][$ap_s]['apbed_qty'] = $apbed_qty;
			
			$percent = explode("|",$op['ap'][$p]['dm_way']);
			$toleri_qty = $op['ap'][$p]['ap_det'][$ap_s]['po_qty'] *  (1 +($toler[0] / 100));
			$op['ap'][$p]['ap_det'][$ap_s]['toleri_qty'] = $toleri_qty - $apbed_qty;
			$op['ap'][$p]['ap_det'][$ap_s]['apb_qty'] = number_format($op['ap'][$p]['ap_det'][$ap_s]['po_qty'] * ($percent[0] / 100),2,'','');
			$op['ap'][$p]['ap_det'][$ap_s]['amount'] = number_format($op['ap'][$p]['ap_det'][$ap_s]['apb_qty'] * $chg_price,2,'','');
			$op['ap'][$p]['before_price'] += $op['ap'][$p]['ap_det'][$ap_s]['amount'];
			
		}
		
		//其他費用
		$op['ap'][$p]['ap_oth_cost'] = array ();		
		$q_str="SELECT * FROM `ap_oth_cost` WHERE  `ap_num` = '$num' and apb_num = ''";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$op['ap'][$p]['ap_oth_cost'] = array();
		$r=0;
		while ($row1 = $sql->fetch($q_result)) {			
			$op['ap'][$p]['ap_oth_cost'][$r]=$row1;
			$amt += $row1['cost'];
			$r++;
		}
		
		$op['ap'][$p]['po_total'] = $amt;
		$op['ap'][$p]['ttl_qty'] = $ttl_qty;
		
        # 重組 TT payment
        $re_dm_way = re_dm_way($op['ap'][$p]['dm_way']);
        $op['ap'][$p]['dm_way'] = $re_dm_way[1];
        
		$p++;
	}
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_before_after_apb($num='')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_before_after_apb($rcv_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "SELECT `apb`.*,`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
			  FROM `apb`,`supl`
			  WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$rcv_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	$q_str = "SELECT distinct ap_num
			  FROM `apb_det` 
			  WHERE rcv_num = '".$rcv_num."'";
	
	$ap_rsl = $sql->query($q_str);
	$p = 0;
	while ($ap_row = $sql->fetch($ap_rsl)) {
		$po_price = 0;
		# ap
		$q_str = "SELECT ap_num, po_num, toler, dear, po_total, currency, fob, fob_area, dm_way
				  FROM ap
				  WHERE ap_num = '".$ap_row['ap_num']."'";
		
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$op['ap'][$p] = $row;
		
		$toler = explode('|',$op['ap'][$p]['toler']);
		$op['ap'][$p]['toleri'] = $toler[0];
		$op['ap'][$p]['tolern'] = $toler[1];
		
		#apb_det
		$q_str = "SELECT *
				  FROM apb_det
				  WHERE rcv_num = '".$rcv_num."' and ap_num = '".$ap_row['ap_num']."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$i = 0;
		while($row = $sql->fetch($q_result)){
			$ap_det_row = $this->get_group_det($row['ap_num'],$row['mat_id'],$row['color'],$row['size']);
			$row['po_qty'] = $ap_det_row['po_qty'];
			substr($row['mat_code'],0,1) == "F" ? $row['mat_cat'] = "l" : $row['mat_cat'] = "a";
			$q_str = "select rate, ord_num
					  from apb_po_link
					  where rcv_id = ".$row['id'];
			$link_rel = $sql->query($q_str);
			$ord_ary = array();
			while($link_row = $sql->fetch($link_rel)){
				$rate = $link_row['rate'];
				$ord_ary[] = $link_row['ord_num'];
			}
			$row['orders'] = $ord_ary;
			
			$row['rate'] = $rate;
			$row['po_unit'] = $ap_det_row ['po_unit'];
			$row['prc_unit'] = $ap_det_row ['prc_unit'];
			$row['prics'] = $ap_det_row ['prics'];
			
			$c_unit = $row['prc_unit'] == ''? $row['po_unit'] : $row['prc_unit'];
			$toleri_qty = $row['po_qty'] * (1+($op['ap'][$p]['toleri']*0.01));
			if($c_unit == 'pc' or $c_unit == 'K pcs')
				$toleri_qty = ceil($toleri_qty);
			$row['toleri_qty'] = $toleri_qty;
			
			# 已請款數量 不含本次
			$row['apbed_qty'] = $this->get_apbed_qty($row['ap_num'],$row['mat_id'],$row['color'],$row['size'],"13,14"," and apb_det.rcv_num <> '".$rcv_num."'");
			
			if($row['apbed_qty'] == null) $row['apbed_qty'] = 0;
			# 已請款數量 含本次
			$row['ttl_apbed_qty'] = $this->get_apbed_qty($row['ap_num'],$row['mat_id'],$row['color'],$row['size'],"13,14","");
			if($row['ttl_apbed_qty'] == null) $row['ttl_apbed_qty'] = 0;
			
			$row['amount'] = number_format($row['qty'] * $row['uprice'],2,'.','');
			
			$po_price += $row['amount'];
			
			$op['ap'][$p]['ap_det'][$i] = $row;
			$op['ap'][$p]['ap_det'][$i]['i'] = $i;
			$i++;
		}
		
		# po其他費用
		$q_str="SELECT * FROM `ap_oth_cost` WHERE apb_num = '$rcv_num' and ap_num='".$op['ap'][$p]['ap_num']."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		
		$op['ap'][$p]['ap_oth_cost'] = array();
		while ($row1 = $sql->fetch($q_result)) {			
			$op['ap'][$p]['ap_oth_cost'][]=$row1;
			$po_price += $row1['cost'];
		}
		
		# 本次貨前付款金額
		$op['ap'][$p]['pay_price'] = $this->get_po_pay_price($ap_row['ap_num'], $rcv_num);
		
		# 貨前 新增的其他費用
		$op['ap'][$p]['apb_oth_cost'] = $this->get_oth_cost($ap_row['ap_num'], $rcv_num, 14, "and item<>''");
		
		$op['ap'][$p]['po_price'] = $po_price;
		
		$p++;
	}

	# apb_log
	$q_str = "SELECT * FROM apb_log	WHERE rcv_num = '".$rcv_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$apb_log = array();
	while ($row = $sql->fetch($q_result)) {
		$apb_log[] = $row;
	}
	$op['rcv_log']=$apb_log;
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_before_after_rcv($sup_code,$ship) 找出未驗收的採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_before_after_rcv($dept='',$limit_entries=0) {
	$sql = $this->sql;
	$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$q_header = "SELECT distinct ap.sup_code, supl.supl_s_name as s_name, ap.dm_way
				 From stock_inventory left join ap on stock_inventory.po_num = ap.po_num 
					  left join supl on supl.vndr_no = ap.sup_code left join ap_det on ap_det.ap_num = ap.ap_num";
	

	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_group_condition("`ap`.`sup_code`,`ap`.`dm_way`");
	$srh->add_sort_condition("`supl`.`s_name` ASC");
	$srh->row_per_page = 20;

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
	
	
	if($argv['PHP_po']){
		$srh->add_where_condition("`ap`.`po_num` like '%".$argv['PHP_po']."%'");
	}
	
	if ($str = $argv['PHP_ship_inv'] )  {
		$srh->add_where_condition("`stock_inventory`.`ship_id` like '%$str%'", "PHP_ship_inv",$str,"search Ship num#=[ $str ]. "); 
	}
	
	if ($str = $argv['PHP_sup'] )  {
		$srh->add_where_condition("`ap`.`sup_code` = '$str'", "PHP_sup",$str,"search supplier=[ $str ]. "); 
	}

	$srh->add_where_condition("ap.dm_way like '%|%'");
	$srh->add_where_condition("ap_det.apb_rmk = 1");
	$srh->add_where_condition("stock_inventory.type = 'i'");
	$srh->add_where_condition("stock_inventory.apb_num = ''");
	
	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
	if (!$result)  $op['record_NONE'] = 1; // 當查尋無資料時

	$op['rcvd'] = $result;  // 資料錄 拋入 $op
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

	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_before_after_rcv($sup_no)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_before_after_rcv($sup_no='',$payment='') {
	$sql = $this->sql;

	$q_str = "select distinct ap.ap_num
			  from ap, stock_inventory
			  where stock_inventory.type = 'i' and  ap.po_num = stock_inventory.po_num and ap.status = 12 and ap.apb_rmk = 1 
					and ap.dm_way like '$payment' and ap.sup_code= '$sup_no' and stock_inventory.apb_num = ''
					order by ap_num desc";
	
	
	$ap_row_result = $sql->query($q_str);
	$p=0;
	while ($ap_num_row = $sql->fetch($ap_row_result)) {
		$num = $ap_num_row['ap_num'];
		$ttl_po_qty = $ttl_rcv_qty = 0;
		$q_str = "select ap.ap_num, ap.po_num, ap.currency, ap.toler, ap.po_total, ap.dear, ap.status, ap.after_apb, sum(apb_oth_cost.cost) as cost
			  from ap, apb_oth_cost
			  where ap.ap_num = '".$num."' and apb_oth_cost.ap_num = ap.ap_num and apb_oth_cost.payment_status = 14
					group by apb_oth_cost.ap_num having cost < ap.po_total";
		
		$ap_result = $sql->query($q_str);
		$ap_row = $sql->fetch($ap_result);
		
		if($ap_row <> null){
			$toler = explode("|",$ap_row['toler']);
			$ap_row['toleri'] = $toler[0];
			$ap_row['tolern'] = $toler[1];
			$ap_row['toleri_price'] = number_format($ap_row['po_total'] * (1+$ap_row['toleri']/100),2,'','');
			
			$op['ap'][$p] = $ap_row;
			
			
			# 請購明細 -- 主料
			$q_str="SELECT ap_det.*, lots.lots_name as mat_name, lots.lots_code as mat_code
					FROM ap_det, lots
					WHERE ap_det.mat_cat = 'l' AND ap_det.ap_num = '$num' AND ap_det.apb_rmk = 1 and lots.id = ap_det.mat_id";
			
			$det_result = $sql->query($q_str);
			$i=0;
			while ($det_row = $sql->fetch($det_result)) {
				$ord_mk =0;
				$ttl_po_qty += $det_row['po_qty'];
				$op['ap'][$p]['ap_det'][$i] = $det_row;
				$op['ap'][$p]['ap_det'][$i]['i']=$i;
				
				if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];
				$i++;
			}
			
			# 請購明細 -- 副料
			$q_str="SELECT ap_det.*, acc.acc_name as mat_name, acc.acc_code as mat_code
					FROM ap_det, acc
					WHERE ap_det.a = 'a' AND ap_det.ap_num = '$num' AND ap_det.apb_rmk = 1 and acc.id = ap_det.mat_id";
			
			
			$det_result = $sql->query($q_str);
			while ($det_row = $sql->fetch($det_result)) {
				$ord_mk = 0;
				$ttl_po_qty += $det_row['po_qty'];
				$op['ap'][$p]['ap_det'][$i] = $det_row;
				$op['ap'][$p]['ap_det'][$i]['i'] = $i;
				if(!$op['ap'][$p]['ap_det'][$i]['prc_unit'])$op['ap'][$p]['ap_det'][$i]['prc_unit'] = $op['ap'][$p]['ap_det'][$i]['po_unit'];

				$i++;
			}
			
			if(isset($op['ap'][$p]['ap_det'])) $op['ap'][$p]['ap_det'] = $GLOBALS['po']->grout_ap($op['ap'][$p]['ap_det']);
			
			$amt = 0;
			$d_size = sizeof($op['ap'][$p]['ap_det']);
			for($k=0;$k<$d_size;$k++){
				$op['ap'][$p]['ap_det'][$k]['uprice'] = $op['ap'][$p]['ap_det'][$k]['prics'];
				
				
				
				# 取出 驗收資料
				$q_str = "select ship_id, invoice_num, sum(qty) as qty
						  from stock_inventory
						  where type = 'i' and apb_num = '' and po_num = '".$op['ap'][$p]['po_num']."' 
								and mat_id = '".$op['ap'][$p]['ap_det'][$k]['mat_id']."' 
								and color = '".$op['ap'][$p]['ap_det'][$k]['color']."' 
								and size = '".$op['ap'][$p]['ap_det'][$k]['size']."' 
								group by po_num, mat_id , color, size";
				
				$rcv_det_result = $sql->query($q_str);
				$z = 0;
				if($rcv_det_row = $sql->fetch($rcv_det_result)){
					$ttl_rcv_qty += $rcv_det_row['qty'];
					$op['ap'][$p]['ap_det'][$k]['ship_id'] = $rcv_det_row['ship_id'];
					$op['ap'][$p]['ap_det'][$k]['invoice_num'] = $rcv_det_row['invoice_num'];
					$op['ap'][$p]['ap_det'][$k]['acpt_qty'] = $rcv_det_row['qty'];
					$acpt_qty = $rcv_det_row['qty'];
					# 訂單號碼、數量
					$op['ap'][$p]['ap_det'][$k]['orders'] = $this->re_avg_qty($rcv_det_row['qty'], $op['ap'][$p]['ap_det'][$k]['po_qty'], $op['ap'][$p]['po_num'], $op['ap'][$p]['ap_det'][$k]['mat_id'], $op['ap'][$p]['ap_det'][$k]['color'], $op['ap'][$p]['ap_det'][$k]['size']);
				}
				
				# apbed_qty
				$apbed_qty = $this->get_apbed_qty($op['ap'][$p]['ap_det'][$k]['ap_num'], $op['ap'][$p]['ap_det'][$k]['mat_id'], $op['ap'][$p]['ap_det'][$k]['color'],$op['ap'][$p]['ap_det'][$k]['size'],"15,16","");
				$op['ap'][$p]['ap_det'][$k]['apbed_qty'] = $apbed_qty;
				
				$toleri_qty = number_format($op['ap'][$p]['ap_det'][$k]['po_qty'] * (1 + $op['ap'][$p]['toleri']/100),2,'','');
				$op['ap'][$p]['ap_det'][$k]['acpt_qty'] = $acpt_qty;
				
				# 判斷是否超過tolerance
				if( $acpt_qty + $apbed_qty > $toleri_qty ){
					$op['ap'][$p]['ap_det'][$k]['apb_qty'] = $toleri_qty - $apbed_qty;
				}else{
					$op['ap'][$p]['ap_det'][$k]['apb_qty'] = $acpt_qty;
				}
				
				$op['ap'][$p]['ap_det'][$k]['toleri_qty'] = $op['ap'][$p]['ap_det'][$k]['apb_qty'];
				
				$amt += number_format($op['ap'][$p]['ap_det'][$k]['prics'] * $op['ap'][$p]['ap_det'][$k]['apb_qty'],2,'','');
				
				
				
				
				
				#單價轉換
				$chg_price = change_unit_price($op['ap'][$p]['ap_det'][$k]['po_unit'],$op['ap'][$p]['ap_det'][$k]['prc_unit'],$op['ap'][$p]['ap_det'][$k]['prics']);
				$op['ap'][$p]['ap_det'][$k]['prics'] = $chg_price;
				
				#小計
				$op['ap'][$p]['ap_det'][$k]['price'] = number_format($op['ap'][$p]['ap_det'][$k]['apb_qty'] * $op['ap'][$p]['ap_det'][$k]['prics'],2,'','');
				$ttl_rcv_price += $op['ap'][$p]['ap_det'][$k]['price'];
			}
			
			# ap其他費用
			$op['ap'][$p]['ap_oth_cost'] = array ();		
			$q_str="SELECT * FROM `ap_oth_cost` WHERE  `ap_num` = '$num' and apb_num = ''";
			$ap_oth_result = $sql->query($q_str);
			while ($ap_oth_row = $sql->fetch($ap_oth_result)) {			
				$op['ap'][$p]['ap_oth_cost'][]=$ap_oth_row;
				$amt += $ap_oth_row['cost'];
			}
			
			# po貨前已付金額
			$op['ap'][$p]['before_price'] = $this->get_payment_cost($op['ap'][$p]['ap_num'],14,"");
			# po貨後已付金額
			$op['ap'][$p]['after_price'] = $this->get_payment_cost($op['ap'][$p]['ap_num'],16,"");
			
			$op['ap'][$p]['after_apb_flag'] = 0;
			if($amt > $op['ap'][$p]['toleri_price']){
				$op['ap'][$p]['after_pay'] = $op['ap'][$p]['toleri_price'] - $op['ap'][$p]['before_price'] - $op['ap'][$p]['after_price'];
				$op['ap'][$p]['after_apb_flag'] = 1;
			}else{
				if($op['ap'][$p]['after_apb']==null && $amt > $op['ap'][$p]['before_price']){
					$op['ap'][$p]['after_pay'] = $amt - $op['ap'][$p]['before_price'];
					$op['ap'][$p]['after_apb_flag'] = 1;
				}elseif($op['ap'][$p]['after_apb']==null && $amt < $op['ap'][$p]['before_price']){
					$op['ap'][$p]['after_pay'] = $amt;
				}else{
					if($amt + $op['ap'][$p]['before_price'] + $op['ap'][$p]['after_price'] <= $op['ap'][$p]['toleri_price']){
						$op['ap'][$p]['after_pay'] = $amt;
					}else{
						$op['ap'][$p]['after_pay'] = $op['ap'][$p]['toleri_price'] - $op['ap'][$p]['before_price'] - $op['ap'][$p]['after_price'];
					}
				}
				
			}
			
			$op['ap'][$p]['ttl_po_qty'] = $ttl_po_qty;
			$op['ap'][$p]['ttl_rcv_qty'] = $ttl_rcv_qty;
			$p++;
		}
	}
	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_apb_payment($ap_num,$status)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_apb_payment($ap_num,$status) {
		$sql = $this->sql;
		$ap_num = str_replace("PO", "PA", $ap_num);
		$q_str="SELECT apb_oth_cost.apb_num, apb_oth_cost.ap_num, sum(apb_oth_cost.cost) as cost, apb.rcv_user, apb.rcv_date, apb.currency
				FROM apb_oth_cost, apb
				WHERE apb_oth_cost.ap_num = '".$ap_num."' and apb_oth_cost.payment_status = ".$status."
					  and apb.rcv_num = apb_oth_cost.apb_num group by apb_num, ap_num";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
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
#	->get_rcv_after_apb($num='')	抓出指定apb記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_rcv_after_apb($apb_num='') {
	$sql = $this->sql;
	
	# 驗收主檔
	$q_str = "
	SELECT 
	`apb`.*,
	`supl`.`uni_no`,`supl`.`supl_s_name` as `s_name`,`supl`.`supl_f_name` as `f_name`
	FROM `apb`,`supl`
	WHERE `apb`.`sup_no` = `supl`.`vndr_no` AND `apb`.`rcv_num` = '".$apb_num."' ";
	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	$op['apb']=$row;

	# apb_det
	$q_str = "SELECT distinct ap.ap_num, ap.po_num, ap.toler, ap.dear, ap.after_apb
			  FROM apb_det, ap
			  WHERE apb_det.rcv_num = '".$apb_num."' and ap.ap_num = apb_det.ap_num";

	$rcvd_rsl = $sql->query($q_str);
	while ($row = $sql->fetch($rcvd_rsl)) {
		$toler = explode("|",$row['toler']);
		$row['toleri'] = $toler[0];
		$row['tolern'] = $toler[1];
		$row['toleri_price'] = $row['dear'] * (1+$row['toleri']/100);
		$op['apb_det'][] = $row;
	}
	
	for($k=0;$k<sizeof($op['apb_det']);$k++){
        $amt = 0;
		# apb_det
		$q_str = "SELECT apb_det.*
				  FROM apb_det
				  WHERE apb_det.rcv_num = '".$apb_num."' and apb_det.ap_num = '".$op['apb_det'][$k]['ap_num']."'";
		
		$q_result = $sql->query($q_str);
		$i=0;
		while ($det_row = $sql->fetch($q_result)) {
			$det_row['po_num'] = str_replace("PA","PO",$det_row['ap_num']);
			# 合併的採購單 apb_po_link
			$q_str = "
			SELECT 
			apb_po_link.id, apb_po_link.po_id, apb_po_link.rcv_id, apb_po_link.qty, apb_po_link.rate, apb_po_link.ord_num, ap_det.po_qty
			FROM apb_po_link, ap_det
			WHERE apb_po_link.rcv_id = '".$det_row['id']."' and ap_det.id = apb_po_link.po_id";
			
			$link_res = $sql->query($q_str);
			$order = array();
            $link = array();
			while ($rows = $sql->fetch($link_res)) {
				$order[] = $rows['ord_num']; # 組合訂單檔
				$link[] = $rows;
				$det_row['rate'] = $rows['rate'];
			}
			$det_row['link'] = $link;
			$order = array_unique($order);
			foreach($order as $key)
				$det_row['order'][] = $key;
			
			
			
			$det_row['mat_cat'] = substr($det_row['mat_code'],0,1)=="F"?"l":"a";
			
			# po資料
			$det_row['po'] = $this->get_group_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 計算 tolerance
			$max_qty = (1 + $op['apb_det'][$k]['toleri'] * 0.01) * $det_row['po']['po_qty'];
			if($det_row['po']['po_unit'] == "pc" or $det_row['po']['po_unit'] == "K pcs")
				$max_qty = ceil($max_qty);
			$det_row['max_qty'] = $max_qty;
			# 驗收資料
			$det_row['rcv_qty'] = $this->get_group_apb_inventory($op['apb']['rcv_num'],$det_row['po_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
			# 已付款數量
			$apbed_qty = $this->get_apbed_qty($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size'],"15,16","and apb_det.id <> '".$det_row['id']."'");
			
			if($apbed_qty == null) $apbed_qty = 0;
			$det_row['apb_qty'] = $apbed_qty;
			
			if( $det_row['rcv_qty'] + $det_row['apb_qty'] > $max_qty ){
				$det_row['toler_qty'] = $max_qty - $det_row['apb_qty'];
			}else{
				$det_row['toler_qty'] = $det_row['rcv_qty'];
			}
			
			$det_row['mark'] = $det_row['ap_num'].$det_row['mat_id'].$det_row['color'].$det_row['size'];
			$det_row['i'] = $i;
			$op['apb_det'][$k]['det'][$i] = $det_row;
            
			# 小計金額 inv_price
			$op['apb_det'][$k]['det'][$i]['price'] = number_format($op['apb_det'][$k]['det'][$i]['uprice'] * $op['apb_det'][$k]['det'][$i]['qty'],2,'','');
			$amt += $op['apb_det'][$k]['det'][$i]['price'];
			$amt = number_format($amt,2,'','');
			
			
            $i++;
		}
		# po貨前已付金額
		$op['apb_det'][$k]['before_price'] = $this->get_payment_cost($op['apb_det'][$k]['ap_num'],14,"");
		# po貨後已付金額
		$op['apb_det'][$k]['after_price'] = $this->get_payment_cost($op['apb_det'][$k]['ap_num'],16,"and apb_num <> '".$apb_num."'");
		# after_price edit用，after_price2 show用
		$op['apb_det'][$k]['after_price2'] = $this->get_payment_cost($op['apb_det'][$k]['ap_num'],16,"");
		
		if($amt > $op['apb_det'][$k]['toleri_price']){
			$op['apb_det'][$k]['amount'] = $op['apb_det'][$k]['toleri_price'] - $op['apb_det'][$k]['before_price'] - $op['apb_det'][$k]['after_price'];
		}else{
			if( ($op['apb_det'][$k]['after_apb']==null or $op['apb_det'][$k]['after_apb']==$apb_num) && $amt > $op['apb_det'][$k]['before_price']){
				$op['apb_det'][$k]['amount'] = $amt - $op['apb_det'][$k]['before_price'];
			}elseif( ($op['apb_det'][$k]['after_apb']==null or $op['apb_det'][$k]['after_apb']==$apb_num) && $amt < $op['apb_det'][$k]['before_price']){
				$op['apb_det'][$k]['amount'] = $amt;
			}else{
				if($amt + $op['apb_det'][$k]['before_price'] + $op['apb_det'][$k]['after_price'] <= $op['apb_det'][$k]['toleri_price']){
					$op['apb_det'][$k]['amount'] = $amt;
				}else{
					$op['apb_det'][$k]['amount'] = $op['apb_det'][$k]['toleri_price'] - $op['apb_det'][$k]['before_price'] - $op['apb_det'][$k]['after_price'];
				}
			}
			
		}
		
		if($op['apb_det'][$k]['after_apb'])
			$op['apb_det'][$k]['after_apb_flag'] = 1;
		else
			$op['apb_det'][$k]['after_apb_flag'] = 0;
		
		#ap_oth_cost 額外費用
		$op['apb_det'][$k]['ap_oth_cost'] = $this->get_ap_oth_cost($apb_num,"and ap_num='".$op['apb_det'][$k]['ap_num']."'");
		#apb_oth_cost 額外費用
		$op['apb_det'][$k]['apb_oth_cost'] = $this->get_oth_cost($op['apb_det'][$k]['ap_num'], $apb_num, "16", "and item <>''");
	}
	
	
	//驗收明細 -- LOG檔		 SHOW
	$q_str="SELECT apb_log.* FROM `apb_log` WHERE `rcv_num` = '".$apb_num."' ";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];

		$op['rcv_log'][$i]=$row1;
		$i++;
	}

	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_after_apb($po_num, $apb_num, $table="ap")
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_after_apb($po_num, $apb_num, $table="ap") {
	$sql = $this->sql;

	$q_str = "select after_apb FROM ap WHERE po_num = '".$po_num."'";

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	if($row['after_apb'] == $apb_num){
		$q_str = "update ap set after_apb = '' WHERE po_num = '".$po_num."'";
		$q_result = $sql->query($q_str);
	}

	return true;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_array($field,$table,$where='')
	{
		$row=array();
		$sql = $this->sql;
		$q_str='';
		if($where == '')
		{
			$q_str="select ".$field." from ".$table;
		}
		else
		{
			$q_str="select ".$field." from ".$table." where ".$where;
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row1 = $sql->fetch($q_result)){
			$row[] = $row1;
		}
		return $row;
		
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cost4pdf($ap_num,$apb_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cost4pdf($ap_num, $apb_num){
		$sql = $this->sql;
		
		$q_str="SELECT ap_num, apb_num, sum(cost) as cost
				FROM apb_oth_cost
				WHERE ap_num = '".$ap_num."' and payment_status = 16
				GROUP BY ap_num, apb_num";
		
		$q_result = $sql->query($q_str);
		$rtn = array();
		while($row = $sql->fetch($q_result)){
			if($row['apb_num'] == $apb_num){
				$q_str = "select qty, uprice
						  from apb_det
						  where ap_num = '".$ap_num."' and rcv_num = '".$apb_num."'";
				$q_res = $sql->query($q_str);
				$prc = 0;
				while($det_row = $sql->fetch($q_res)){
					$prc += round( $det_row['uprice'] * $det_row['qty'], 2);
				}
				$row['cost2'] = $prc;
			}else{
				$row['cost2'] = $row['cost']; #應付等於實付
			}
			$rtn[] = $row;
		}
		
		return $rtn;
	}	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_inventory_qty()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_inventory_qty($type='i',$po_num,$mat_id,$color='',$size='') {
		$sql = $this->sql;
		$q_str="SELECT ship_id, bl_num, open_date, color, size, qty, r_no, l_no, apb_num
				FROM stock_inventory
				WHERE type='".$type."' and po_num='".$po_num."' and mat_id=".$mat_id." and 
					  color='".$color."' and size='".$size."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = array();
		while($row1 = $sql->fetch($q_result))
		{
			$row[] = $row1;
		}
		
		return $row;
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_un_rcv_po()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_un_rcv_po($post) {
		$sql = $this->sql;
		
		$rtn = array();
		$where_str = "";
		if(!empty($post['PHP_cust'])) $where_str .= " and ap.cust = '".$post['PHP_cust']."' ";
		if(!empty($post['PHP_begin_date']) and !empty($post['PHP_end_date'])) $where_str .= " and ap.ap_date between '".$post['PHP_begin_date']."' and '".$post['PHP_end_date']."' ";
		if(!empty($post['PHP_po_user'])) $where_str .= " and ap_user = '".$post['PHP_po_user']."' ";
		
		$q_str="SELECT distinct apb_det.ap_num
				FROM apb, apb_det, ap
				WHERE apb.rcv_num = apb_det.rcv_num and apb.payment = 'T/T before shipment' and apb.status = 2
					  and ap.ap_num = apb_det.ap_num ".$where_str." order by apb_det.ap_num asc";
		
		if (!$q_result = $sql->query($q_str)) {
			return false;    
		}
		
		while($row = $sql->fetch($q_result)){
			$q_str="SELECT apb_det.ap_num
				FROM apb, apb_det
				WHERE apb_det.ap_num = '".$row['ap_num']."' and apb.rcv_num = apb_det.rcv_num and apb.payment = 'T/T before shipment' and apb.status = 4";
			
			$rcv_rsl = $sql->query($q_str);
			if(!$ap_num_row = $sql->fetch($rcv_rsl)){
				$q_str = "select ap.ap_num, ap.po_num, ap.sup_code, supl.supl_s_name
						  from ap, supl
						  where ap.ap_num = '".$row['ap_num']."' and supl.vndr_no = ap.sup_code";
				
				$ap_rsl = $sql->query($q_str);
				$ap_row = $sql->fetch($ap_rsl);
				
				# order_num
				$q_str = "select distinct order_num
						  from ap_det
						  where ap_num = '".$row['ap_num']."'";
				
				$ord_rsl = $sql->query($q_str);
				$ord_num = array();
				while($ord_row = $sql->fetch($ord_rsl)){
					$ord_num[] = $ord_row['order_num'];
				}
				$ap_row['orders'] = $ord_num;
				
				# ship num
				$q_str = "select distinct po_ship.id, po_ship.num
						  from po_ship, po_ship_det, ap_det
						  where ap_det.ap_num = '".$row['ap_num']."' and po_ship_det.po_id = ap_det.po_spare and po_ship.id = po_ship_det.ship_id ";
				
				$ship_rsl = $sql->query($q_str);
				$ship = array();
				while($ship_row = $sql->fetch($ship_rsl)){
					$ship[] = $ship_row;
				}
				$ap_row['ship'] = $ship;
				
				# receive num
				$q_str = "select distinct receive.rcv_num
						  from receive_det, receive
						  where receive_det.ap_num = '".$row['ap_num']."' and receive.rcv_num = receive_det.rcv_num and receive.tw_rcv = 0 ";
				
				$receive_rsl = $sql->query($q_str);
				$rcv_num = array();
				while($rcv_row = $sql->fetch($receive_rsl)){
					$rcv_num[] = $rcv_row['rcv_num'];
				}
				$ap_row['rcv'] = $rcv_num;
				
				$rtn[] = $ap_row;
			}
		}
		
		return $rtn;
	}
	
} // end class

?>