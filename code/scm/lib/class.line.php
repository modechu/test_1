<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->bom_search($supl,$cat)	查詢BOM的主副料
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class LINE {
		
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



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->search($mode=0,$where_str='') 搜尋 資料
# mode = 1 dailys
# mode = 2 wait for invoice submit/reject
# mode = 6 wait for invoice confirm
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search( $mode=0,$where_str="" ) {

	$sql = $this->sql;
	
	$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	// $q_header = "SELECT DISTINCT `shipping_doc`.*, `cust`.`cust_init_name` as `cust_init`  
							 // FROM `line_balance` , `shipping_doc_qty` , `cust` ";
	$q_header = "SELECT DISTINCT `line_balance`.* 
							 FROM `line_balance` ";
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	// $srh->add_group_condition("`line_balance`.`workmanship_id`");
	// $srh->add_group_condition("`line_balance`.`line`");
	// $srh->add_group_condition("`line_balance`.`w_date`");
	// $srh->add_sort_condition("`line_balance`.`id` DESC");
	$srh->row_per_page = 10;
	$pagesize = 10;

	if ($argv['PHP_sr_startno']) {
		$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
	}else{
		$pages = $srh->get_page(1,$pagesize);
	} 

	// $srh->add_where_condition("`line_balance`.`cust` = `cust`.`cust_s_name`");
	// $srh->add_where_condition("`shipping_doc`.`id` = `shipping_doc_qty`.`s_id`");

	if ($mode){
		// if ($str = $argv['SCH_inv'] )  {
			// $srh->add_where_condition("`shipping_doc`.`inv_num` like '%$str%'", "SCH_inv",$str," Invoice : [ $str ]. "); }
		// if ($str = $argv['SCH_date_str'] )  {
			// $srh->add_where_condition("`shipping_doc`.ship_date >= '$str'", "SCH_date",$str," SHIP DATE > [ $str ]. "); }
		// if ($str = $argv['SCH_date_end'] )  {
			// $srh->add_where_condition("`shipping_doc`.ship_date <= '$str'", "SCH_date",$str," SHIP DATE < [ $str ]. "); }
		// if ($str = $argv['SCH_des'] )  {
			// $srh->add_where_condition("`shipping_doc`.des like '%$str%'", "SCH_des",$str," Description =[ $str ]. "); }
		// if ($str = $argv['PHP_cust'] )  {
			// $srh->add_where_condition("`shipping_doc`.cust = '$str'", "SCH_cust",$str," Customer =[ $str ]. "); }
		if ($str = $argv['PHP_fty'] )  {
			$srh->add_where_condition("`line_balance`.fty = '$str'", "SCH_fty",$str," Factory =[ $str ]. "); }
		if ($str = $argv['SCH_ord'] )  {
			$srh->add_where_condition("`line_balance`.`order_num` = '$str'", "SCH_ord",$str," ORDER# =[ $str ]. "); }
	}

	if ($mode==1){
		$srh->add_group_condition("`line_balance`.`line`");
		$srh->add_group_condition("`line_balance`.`order_num`");
	}
	if ($mode==2){
		$srh->add_where_condition("`shipping_doc`.`status` = 1"); 
	}

	if ($mode==4){
		$srh->add_where_condition("`shipping_doc`.`status` >= 4"); 
	}

	if ($mode==6){
		$srh->add_where_condition("`shipping_doc`.`status` = 6"); 
	}
	
	

	$result= $srh->send_query2();

	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}
	
	$this->msg->merge($srh->msg);
	if (!$result){   // 當查尋無資料時
		$op['record_NONE'] = 1;
	}
// echo $srh->q_str.'<br>';
	$op['line'] = $result;  // 資料錄 拋入 $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;
	$op['maxpage'] =$srh->get_max_page();
	$op['pages'] =  $pages;
	$op['now_pp'] = $srh->now_pp;
	$op['lastpage']=$pages[$pagesize-1];		

	return $op;
} // end func


function get_daily($ord,$line,$w_date='',$emp_id=''){

	$sql = $this->sql;
	$srh = new SEARCH();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$w_date = !empty($w_date) ? '`w_date` = "'.$w_date.'" AND ' : '';
	$emp_id = !empty($emp_id) ? '`emp_id` = "'.$emp_id.'" AND ' : '';
	
	$q_str = "
	SELECT DISTINCT `line_balance`.* 
	FROM `line_balance` 
	WHERE 
	`order_num` = '".$ord."' AND
	$w_date 
	$emp_id 
	`line` = '".$line."'
	";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! 無法存取資料庫!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$row['line'] = array();
	while ($row_line = $sql->fetch($q_result)) {
		$row['line'][]=$row_line;	
	}	

	return $row;
}


function get_order($fty){

	$sql = $this->sql;
	$srh = new SEARCH();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	// $w_date = !empty($w_date) ? '`w_date` = "'.$w_date.'" AND ' : '';
	
	$q_str = "
	SELECT DISTINCT `line_balance`.`order_num` 
	FROM `line_balance` 
	WHERE 
	`fty` = '".$fty."' 
	ORDER BY `order_num` ASC  
	";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! 無法存取資料庫!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$row = array();
	while ($row_fty = $sql->fetch($q_result)) {
		$row[] = $row_fty['order_num'];	
	}	

	return $row;
}

} // end class
?>