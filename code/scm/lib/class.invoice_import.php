<?php

class INVOICE_IMPORT {



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
function checkinv($inv_id)
{
	$sql = $this->sql;
	//$t_sql = 'select inv_no from invoice_import where inv_no="'.$inv_id.'"';
	$t_sql = 'select inv_num from shipping_doc where inv_num="'.$inv_id.'"';
	//echo $t_sql;
	$inv = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $inv[] = $row;
    }
	
	/* print_r($inv);
	exit; */
	if(sizeof($inv) > 0)
	{
		$return_val = true;
	}
	else
	{
		$return_val = false;
	}
	if($GLOBALS['SCACHE']['ADMIN']['login_id']=='morial')
	{
		echo "22".$return_val."1<br>";
		
	}
	return $return_val;
	
}
function sql_i_invoice($parm)
{
	//mysql_affected_rows()
	/* print_r();
	exit; */
	$sql = $this->sql;
	foreach( $parm as $key => $val) {
		//echo $val."<br>";
		
		/* if( $result = $sql->query($val) ){
			
		} */
		if( !$result = $sql->query($val) ){
        
            return false;

        } 
	}
}
function sql_del_invoice($t_sql)
{
	$sql = $this->sql;
	if( !$result = $sql->query($t_sql) ){
        
        return false;

    } 
	return true;
}
function sql_i_shipping_doc($t_sql)
{
	//mysql_affected_rows()
	/* print_r();
	exit; */
	$sql = $this->sql;
	if( !$result = $sql->query($t_sql) ){  
        return false;
    } 
	$myid = mysql_insert_id();
	return $myid;
}
function sql_u_invoice($t_sql)
{
	//mysql_affected_rows()
	//echo $t_sql;
	$sql = $this->sql;
	if( !$result = $sql->query($t_sql) )
	{
            return false;
    }
	return true;
}
function select_inv($inv_id)
{
	$sql = $this->sql;
	$t_sql = "select * from invoice_import where inv_no='".$inv_id."'";
	$inv = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $inv[] = $row;
    }
	
	return $inv;
}
function select_status($inv_id)
{
	$sql = $this->sql;
	$t_sql = "select status from shipping_doc where inv_num='".$inv_id."'";
	//$inv = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $inv = $row;
    } 
	//$row = $sql->fetch($result);
	return $inv;
}
function select_shipdate($inv_id)
{
	$sql = $this->sql;
	$t_sql = "select ship_date from shipping_doc where inv_num='".$inv_id."'";
	//$inv = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $inv = $row;
    } 
	//$row = $sql->fetch($result);
	return $inv;
}
function get_consignee($cust_code)
{
	$sql = $this->sql;
	$t_sql = "select id,f_name from consignee where cust='".$cust_code."'";
	//echo $t_sql."<br>";
	$consignee = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $consignee[] = $row;
    }
	
	return $consignee;
	
}
function get_sorder($ord_num)
{
	$sql = $this->sql;
	$t_sql = "select * from s_order where order_num='".$ord_num."'";
	$sorder = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $sorder = $row;
    }
	return $sorder; 

} # END CLASS
}
?>