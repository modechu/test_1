<?php

class PACKING_LIST {



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
	function select_carton_inv($t_sql)
	{
		$sql = $this->sql;
		$inv = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {
			
			$inv = $row;
		}
		return $inv;
		
	}# FUN END
	function delete_carton($t_sql)
	{
		$sql = $this->sql;
		//$inv = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		return true;
	}# FUN END
	function select_carton($t_sql)
	{
		$sql = $this->sql;
		$carton = array();
		if( !$result = $sql->query($t_sql) ){

			return false;
		}
		while( $row = $sql->fetch($result) ) {
			
			$carton[] = $row;
		}
		return $carton;
		
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