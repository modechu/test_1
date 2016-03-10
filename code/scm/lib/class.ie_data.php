<?php 

class IE_DATA {
	
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! 無法聯上資料庫.");
		    return false;
		}
		$this->sql = $sql;
		
		return true;
	} // end func

					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_bom_materials($wi_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_bom_materials($wi_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select *
			  from po_bom
			  where ord_num = '".$wi_num."'";
	$q_result = $sql->query($q_str);
	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}
	
	$sql->po_disconnect();
	
	return $rtn;
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_bom_status($wi_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_bom_status($wi_num) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select *
			  from po_bom_status
			  where ord_num = '".$wi_num."'";
	$q_result = $sql->query($q_str);
	$rtn = array();
	$rtn = $sql->fetch($q_result);
	
	$sql->po_disconnect();
	
	return $rtn;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bom_add($parm)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function bom_add($parm) {
	$sql = $this->sql;
	$id = get_insert_id("po_bom","1");
	
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "INSERT INTO po_bom (id, ord_num, fty, bom_id, mat_cat, mat_id, mat_name, color, size, loss, qty, o_qty) 
				  VALUES(".$id.",'".
							$parm['ord_num']."','".
							$parm['fty']."',".
							$parm['bom_id'].",'".
							$parm['mat_cat']."',".
							$parm['mat_id'].",'".
							$parm['mat_name']."','".
							$parm['color']."','".
							$parm['size']."',".
							$parm['loss'].",'".
							$parm['qty']."','".
							$parm['o_qty']."')";
							
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
	$q_str = "update po_bom
			  set mat_name = '".$parm['mat_name']."', color = '".$parm['color']."', loss = ".$parm['loss'].", qty = ".$parm['qty']."
			  where mat_cat = '".$parm['mat_cat']."' and bom_id = ".$parm['bom_id'];
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
		
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
#	->bom_cfm_mat($bom_id, $mat_cat)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function bom_cfm_mat($bom_id, $mat_cat) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "update po_bom
			  set cfm_date = '".date('Y-m-d H:i:s')."', cfm_user = '".$GLOBALS['SCACHE']['ADMIN']['login_id']."'
			  where bom_id = ".$bom_id." and mat_cat = '".$mat_cat."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't insert.");
		$this->msg->merge($sql->msg);
		return false;    
	}   			
	
	$sql->po_disconnect();
	
	return true;
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search()
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search() {
	$sql = $this->sql;
	$fty = $GLOBALS['PHP_fty_sch'];
	if($fty)
		$where_str = " where fty = '".$fty."' ";
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select distinct po_bom.ord_num
			  from po_bom".$where_str."
			  order by ord_num";
	$q_result = $sql->query($q_str);
	
	$bom_row = array();
	while($row = $sql->fetch($q_result)){
		$bom_row[] = $row;
	}
	
	$sql->po_disconnect();
	
	return $bom_row;
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_requi_det($mat_cat,$bom_id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_requi_det($mat_cat,$bom_id) {
	$sql = $this->sql;
	
	if($mat_cat == "l"){
		$q_str = "select stock_inventory.qty, stock_inventory.`date` as eta, po_ship.ship_date as etd, stock_inventory.carrier_num
				  from stock_inventory, po_ship
				  where stock_inventory.`type` = 'i' and stock_inventory.mat_cat = 'l' and stock_inventory.bom_id = ".$bom_id." and stock_inventory.ship_inv = po_ship.ship_inv";
	}else{
		$q_str = "select stock_inventory.qty, stock_inventory.`date` as eta, po_ship.ship_date as etd, stock_inventory.carrier_num
				  from stock_inventory, po_ship
				  where stock_inventory.`type` = 'i' and stock_inventory.mat_cat = 'a' and stock_inventory.bom_id = ".$bom_id." and stock_inventory.ship_inv = po_ship.ship_inv";
	}
	$q_result = $sql->query($q_str);
	$rtn = array();
	while($row = $sql->fetch($q_result)){
		$rtn[] = $row;
	}
	if(!$rtn[0]['qty'])
		$rtn[0]['qty'] = 0;
	
	return $rtn;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bom_status_add($parm)		更新 訂單 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function bom_status_add($parm) {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "INSERT INTO po_bom_status (ord_num, fty, create_user, create_date, status, version) 
				  VALUES('".
							$parm['ord_num']."','".
							$parm['fty']."','".
							$parm['create_user']."','".
							$parm['create_date']."','".
							$parm['status']."','".
							$parm['version']."')";
							
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't insert.");
			$this->msg->merge($sql->msg);
			return false;    
		}   			
		
	return true;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_bom_submit($parm,$where_str, $table='po_bom_status')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_bom_submit($parm,$where_str, $table='po_bom_status') {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "UPDATE ".$table." SET status = '".$parm['status']."', sub_date = '".$parm['sub_date']."',
									sub_user = '.".$parm['sub_user']."' WHERE ".$where_str;
	
	$q_result = $sql->query($q_str);
	$sql->po_disconnect();
	
	return true;
}


function search_uncfm() {
	$sql = $this->sql;
	// $fty = $GLOBALS['PHP_fty_sch'];
	// if($fty)
		// $where_str = " and fty = '".$fty."' ";
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	
	$q_str = "select distinct ord_num, fty, status
			  from po_bom_status
			  where status = 2
			  order by ord_num";
	$q_result = $sql->query($q_str);
	
	$bom_row = array();
	while($row = $sql->fetch($q_result)){
		$bom_row[] = $row;
	}
	
	$sql->po_disconnect();
	
	return $bom_row;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_bom_cfm($parm,$where_str, $table='po_bom_status')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_bom_cfm($parm,$where_str, $table='po_bom_status') {
	$sql = $this->sql;
	$po_link_id = $sql->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);
	$q_str = "UPDATE ".$table." SET status = '".$parm['status']."', cfm_date = '".$parm['cfm_date']."',
									cfm_user = '.".$parm['cfm_user']."' WHERE ".$where_str;
	
	$q_result = $sql->query($q_str);
		
	$sql->po_disconnect();
	
	return true;
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
function get_stock_qty($mat_cat, $fty, $mat_id, $color, $size) {
	$sql = $this->sql;
	
	if($mat_cat == "l"){
		$tbl = "stock_mat_lots";
		$mat_field = "lots_id";
	}else{
		$tbl = "stock_mat_acc";
		$mat_field = "acc_id";
	}
	
	$q_str = "SELECT qty
			  from ".$tbl."
			  where fty = '".$fty."' and ".$mat_field." = ".$mat_id." and color = '".$color."' and size = '".$size."' order by ver desc limit 0,1";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$row = $sql->fetch($q_result);
	if(!$row) $row['qty'] = 0;
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
	
	$q_str = "INSERT INTO requi_notify (id, rn_num, ord_num, fty, create_user, create_date, status) VALUES('".
							$id."','".
							$parm['rn_num']."','".
							$parm['ord_num']."','".
							$parm['fty']."','".
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
	
	$q_str = "INSERT INTO requi_notify_det (id, rn_num, po_bom_id, bom_id, mat_cat, mat_id, mat_name, color, size, qty) VALUES('".
							$id."','".
							$parm['rn_num']."','".
							$parm['po_bom_id']."','".
							$parm['bom_id']."','".
							$parm['mat_cat']."','".
							$parm['mat_id']."','".
							$parm['mat_name']."','".
							$parm['color']."','".	
							$parm['size']."','".	
							$parm['status']."')";
	
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
	$srh->row_per_page = 3;

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
		
	$sql->po_disconnect();
	
	return $op;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
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




} // end class

?>