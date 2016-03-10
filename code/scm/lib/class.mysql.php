<?php

####################################### 向下相容用
class SQL extends MySQL {

	function SQL() {
		$this->MySQL();
	}

}       // end class SQL

######################  class Mysql  ########################
#	 class MySQL
#		mysql->connect($server, $user, $passwd, $db)						
#		mysql->set_link_id($link =0)						
#		mysql->disconnect()					
#		mysql->free_result($q_result)				
#		mysql->query($str)					
#		mysql->fetch($q_result = 0) 				
#		mysql->num_rows($q_result =0)				
#		mysql-> insert_id($link = 0)				
#		mysql->error()				
#		mysql->seek($rows=0,$result=0)				
# pages??
# currentPage??
#
##########################################################

class MySQL {
	var $link_id;
	var $po_link_id;
	var $errno;
	var $error;
	var $query_result;
	var $msg;

##########################################################
#		function mySQL()							
#				必需inlclude    msg class
##########################################################
	function mySQL() {

		$this->msg = new MSG_HANDLE();         //  先建 msg class
	}

##########################################################
#		mysql->connect($server, $user, $passwd, $db)						
#				 設定資料庫連結( 連上 mysql +  聯接資料庫 )
##########################################################
	function connect($server, $user, $passwd, $db) {
		
		$this->link_id = mysql_connect($server, $user, $passwd);
		if (!$this->link_id) {
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			echo $this->error;exit;
			$this->msg->add("Error: fail to link specified MySQL."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			return false;
		}
		
		if (!mysql_select_db($db,$this->link_id))
		{
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: fail connect choosed MySQL database."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			return false;
		}   
		
		return $this->link_id;
	}			// end func

##########################################################
#		mysql->set_link_id($link =0)						
##########################################################
	function set_link_id($link =0) {

		$this->link_id = $link;
		return true;
	}
				
##########################################################
#		mysql->disconnect()					
#				 中斷MySQL
##########################################################
	function disconnect() {

		if (!mysql_close($this->link_id)) {
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: fail to interrupt MySQL."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			return false;
		}
		$this->link_id =0;
		return true;
	}  // end func

##########################################################
#		mysql->free_result($q_result)				
#				 釋放MySQL result
##########################################################
	function free_result($q_result) {

		if (!$q_result && !$q_result = $this->query_result) {
			$this->msg->add("Error: please specify MySQL result。"); 
			return false;			
		}
		if (!mysql_free_result($q_result)) {
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: unable to release MySQL result."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			return false;
		}
		$this->query_result = 0;
		return true;
	}  //end func

##########################################################
#		mysql->query($str)					
#				 Query MySQL
##########################################################
	function query($str) {
		$q_result = mysql_query($str,$this->link_id);

		if(!$q_result)
		{
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("   Error: fail to Query MySQL."); 
			$this->msg->add("   message: ".$this->errno.":".$this->error);
			// $this->msg->add("   SQL : ".$str);
			return false;
			
		}
		
		$this->query_result = $q_result;
		return $q_result;
	}  // end func

##########################################################
# mysql->fetch($q_result = 0) 				
##########################################################
	function fetch($q_result = 0) {
	
		if (!$q_result && !$q_result = $this->query_result) {
			$this->msg->add("Error: please specify MySQL result。"); 
			return false;			
		}
		$this->query_result = $q_result;
// echo $q_result."<br>";
		$row = mysql_fetch_array($q_result);
		if(!$row) return false;
		return $row;	
	}		// end func

##########################################################
# mysql->fetch_row($q_result = 0) 				
##########################################################
	function fetch_row($q_result = 0) {
	
		if (!$q_result && !$q_result = $this->query_result) {
			$this->msg->add("Error: please specify MySQL result。"); 
			return false;			
		}
		$this->query_result = $q_result;
		$row = mysql_fetch_row($q_result);
		if(!$row) return false;
		return $row;	
	}		// end func

##########################################################
# mysql->fetch_field($q_result = 0) 				
##########################################################
	function fetch_field($q_result = 0) {
	
		if (!$q_result && !$q_result = $this->query_result) {
			$this->msg->add("Error: please specify MySQL result。"); 
			return false;			
		}
		$this->query_result = $q_result;
		$row = mysql_fetch_field($q_result);
		if(!$row) return false;
		return $row;	
	}		// end func

##########################################################
#		mysql->fetch_object($q_result = 0) 				
##########################################################
	function fetch_object($q_result = 0) {
	
		if (!$q_result && !$q_result = $this->query_result) {
			$this->msg->add("Error: please specify MySQL result。"); 
			return false;			
		}
		$this->query_result = $q_result;
		$row = mysql_fetch_object($q_result);
		if(!$row) return false;
		return $row;	
	}		// end func

##########################################################
#		mysql->num_rows($q_result =0)				
##########################################################
	function num_rows($q_result =0) {

		if (!$q_result && !$q_result = $this->query_result) {
			$this->msg->add("Error: please specify MySQL result。"); 
			return false;			
		}
		$this->query_result = $q_result;
		$num = mysql_num_rows($q_result);

		return $num;     
	}		// end func
	
##########################################################
#		mysql-> insert_id($link = 0)				
##########################################################
	function insert_id($link = 0) {

		if (!$link && !$link = $this->link_id) {
			$this->msg->add("Error: please specify MySQL link。"); 
			return false;					
		}
		$this->link_id = $link;
		$id = mysql_insert_id($link);
		return $id;
	}			// end func.....
		
##########################################################
#		mysql->error()				
##########################################################
	function error() {

		$str = $this->errno." : ".$this->error;
		return $str;
	}				// end func 

##########################################################
#		mysql->seek($rows=0,$result=0)				
##########################################################
function seek($rows=0,$result=0) {
// print_r($result);
//  2004/11/24  disable if 的判斷		因出現 undifined variablew q_result
	if ($result) {
		$q_result = $result;
	} else {
		$q_result = $this->query_result;
	}
	if (!$q_result) {
		$this->msg->add("Error: please specify MySQL result。"); 
		return false;
	}
	$flag = mysql_data_seek($q_result,$rows);
	$this->query_result = $q_result;
	return $flag;
} // end func


##########################################################
#		mysql->connect($server, $user, $passwd, $db)						
#				 設定資料庫連結( 連上 mysql +  聯接資料庫 )
##########################################################
	function db_change($db) {
		
		if (!mysql_select_db($db,$this->link_id))
		{
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: fail connect choosed MySQL database."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			echo $this->errno." : ".$this->error."<br>";
			return false;
		}    
		
		return $this->link_id;
	}			// end func
	
##########################################################
#		mysql->po_connect($server, $user, $passwd, $db)						
#				 設定資料庫連結( 連上 mysql +  聯接資料庫 )
##########################################################
	function po_connect($server, $user, $passwd, $db) {
		
		$this->link_id = mysql_connect($server, $user, $passwd);
		
		if (!$this->link_id) {
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: fail to link specified MySQL."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			return false;
		}
		if (!mysql_select_db($db,$this->link_id))
		{
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: fail connect choosed MySQL database."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			return false;
		}   
		mysql_query("SET NAMES 'UTF8'");
		mysql_query("SET CHARACTER SET UTF8");
		mysql_query("SET CHARACTER_SET_RESULTS=UTF8'");
		return $this->link_id;
	}
	
##########################################################
#		mysql->po_disconnect()					
#				 中斷MySQL
##########################################################
	function po_disconnect() {

		if (!mysql_close($this->link_id)) {
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: fail to interrupt MySQL."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			return false;
		}
		return true;
	}	

##########################################################
#		mysql->po_query($str)
##########################################################
	function po_query($str) {
		$q_result = mysql_query($str,$this->po_link_id);

		if(!$q_result)
		{
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			$this->msg->add("Error: fail to Query MySQL."); 
			$this->msg->add("      message: ".$this->errno.":".$this->error);
			// $this->msg->add("      SQL : ".$str);
			return false;
			
		}
		
		$this->query_result = $q_result;
		return $q_result;
	}
	



	
}   // end class mySQL

?>