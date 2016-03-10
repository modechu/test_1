<?php

class PACKING_EDIT {



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
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->get_consignee($cust_code)	
	#	$cust_code是客戶簡稱
	#	取得該客戶都所有出貨地地址
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_consignee($cust_code,$dept)
	{
		$sql = $this->sql;
		$t_sql = "select id,f_name from consignee where cust='".$cust_code."' and dept='".$dept."'";
		//echo $t_sql."<br>";
		$consignee = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {

			$consignee[] = $row;
		}
		//print_r($consignee);
		return $consignee;
		
	}# FUN END
	function get_consignee_addr($consignee_code)
	{
		$sql = $this->sql;
		$t_sql = "select f_name from consignee where id=".$consignee_code;
		$consignee_addr = "";
		if( !$result = $sql->query($t_sql) ){
			return false;
		}
		while( $row = $sql->fetch($result) ) {

			$consignee_addr = $row['f_name'];
		}
		return $consignee_addr;
	}
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cust($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();

		//去除舊版本客戶
		//echo $where_str;
		$order_by = substr($where_str,' order by ');
		$where_str = str_replace($order_by,'',$where_str);
		if($where_str == ''){$where_str = " WHERE active = 'y' ";}else{$where_str .= " AND active = 'y' ";}
		$where_str .= $order_by;
		
		$q_str = "SELECT ".$n_field." FROM cust ".$where_str;
		//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			$match_limit = 500;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				$match++;
				if ($match==500) {
					break;
				}
			}
			if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			} 
		
		return $fields;
	} # FUN END
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->get_order_partial($order)	
	#	$order是SCM訂單號碼
	#   取得該訂單的所有交貨日的MKS資料
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_partial($order)
	{
		$sql = $this->sql;
		$t_sql = "select mks from order_partial where ord_num='".$order."'";
		$partial_mks = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {

			$partial_mks[] = $row;
		}
		//return($partial_mks);
		if(sizeof($partial_mks) > 0)
		{
			return $partial_mks;
		}
		else
		{
			return 0;
		}  
		//return $partial_mks;
		//return $t_sql
	}# FUN END
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->get_sizebreakdown($order,$partial)	
	#	$order是SCM訂單號碼,$partial是交期簡碼
	#   取得該訂單交期的顏色尺碼表
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_sizebreakdown($order,$partial)
	{
	
		$sql = $this->sql;
		$t_sql = "select wiqty.id,wiqty.p_id,wiqty.colorway,size_des.size ";
		$t_sql .= "from s_order,order_partial,size_des,wiqty ";
		$t_sql .= "where s_order.size = size_des.id ";
		$t_sql .= "and s_order.order_num=order_partial.ord_num ";
		$t_sql .= "and order_partial.id = wiqty.p_id ";
		$t_sql .= "and order_partial.ord_num='";
		$t_sql .= $order;
		$t_sql .= "' ";
		$t_sql .= "and order_partial.mks='";
		$t_sql .= $partial;
		$t_sql .= "' ";
		
		$size_breakdown = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {
			
			$size_breakdown[] = $row;
		}
		if(sizeof($size_breakdown) > 0)
		{
			return $size_breakdown;
		}
		else
		{
			return 0;
		}
		//print_r($consignee);
		//return $size_breakdown;
		//return $t_sql;
		
	}# FUN END
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->chk_carton_number($t_sql)	
	#   取的某PO出貨日當天的箱號資料
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function chk_carton_number($t_sql)
	{
	
		$sql = $this->sql;

		
		$carton = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {
			
			$carton = $row;
		}
		/* if(sizeof($carton) > 0)
		{ */
			return $carton;
		/* }
		else
		{
			return 0;
		} */
		//print_r($consignee);
		//return $size_breakdown;
		//return $t_sql;
		
	}# FUN END
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->insert_main($t_sql)	
	#   新增包裝主表資料
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function insert_main($t_sql)
	{
		$sql = $this->sql;
		if( !$result = $sql->query($t_sql) ){  
			return false;
		} 
		$myid = mysql_insert_id();
		return $myid;
		
	}# FUN END
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->insert_sub($t_sql)	
	#   新增包裝子表資料
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function insert_sub($t_sql)
	{
		$sql = $this->sql;
		if( !$result = $sql->query($t_sql) ){  
			return false;
		} 
		$result = 'finish';
		return $result;
		
	}# FUN END
	function get_packing_detail4po($t_sql)
	{
	
		$sql = $this->sql;

		
		$packing = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {
			
			$packing[] = $row;
		}
		return $packing;
	}# FUN END
	function get_order_size($order)
	{
		$t_sql = "select size_des.size from s_order,size_des where s_order.size = size_des.id and s_order.order_num='".$order."'";
		$sql = $this->sql;
		$size = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {
			
			$size = $row;
		}
		return $size;
	}# FUN END
	function get_order_info($t_sql)
	{
		$sql = $this->sql;
		$order = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {
			
			$order = $row;
		}
		return $order;
		
	}# FUN END
} # END CLASS

?>