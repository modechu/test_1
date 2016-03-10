<?php 

	#####################  sql_log  class  ##################################
	# Short description: 用來記錄log   的class
	#
	#	sql_log->init($sql,$table)		啟始 [使用msg_handle();  先聯sql ]
	#	sql_log->set_user($user=0)
	#	sql_log->log_add($user=0,$class="",$description="")			加入log
	#	sql_log->function log_del($mode=0, $id_arry=0, $str=0 )		刪除 log 
	#		mode=0, 1, 2 [ 0: 勾選id刪除  1: 刪除全部   :2:條件刪除 (需加入$str) ]
	#	sql_log->log_nums($where_str )	由  $where_str 找出 log  記錄的數目
	####################################################################
class SQL_LOG {
	var $sql;
	var $msg ;
	var $user ;
	var $table;
	
	var $id_arry;

	###################################################################
	#	sql_log->init($sql,$table)		啟始 [使用msg_handle();  先聯sql ]
	#				
	#	必需聯上 sql 才可  啟始
	###################################################################
	function init($sql,$table) {

		$this->msg = new MSG_HANDLE();
		if (!$sql) {
			$this->msg->add("Error! enable to contact database !");
		    return false;
		}
		if (!$table) {
			$this->msg->add("Error! enable to get database Table !");
		    return false;
		}
		$this->sql = $sql;
		$this->table = $table;
		return true;
	} // end func

	###################################################################
	#	sql_log->set_user($user=0)
	#				
	#					
	###################################################################
	function set_user($user=0) {

		if (!$user) {
			return false;
		}
		$this->user = $user;
		return true;
	} // end func

	###################################################################
	#	sql_log->log_add($user=0,$class="",$description="")		加入log
	#				
	#					加入 log
	###################################################################
//	function log_add($user=0,$class="",$description="") {
	function log_add($user=0,$class="",$description="") {   // 2005/09/02 修改

		$sql = $this->sql;
		$table = $this->table;
		if (!$user) {
			$user = $GLOBALS['SCACHE']['ADMIN']['name'];
		}
		if (!$user) {
			$user = $this->user;
		}
		if (!$user) {
			$user = "UNKNOWN";
		}
		if (!$class) {
			return false;
		}
//		$description = str_replace("\\", "", str_replace("'", "''", $description)); 	    
		$description = addslashes($description); 	    //  字串加上 引號
					# 抓出IP
		$ip = $GLOBALS['REMOTE_ADDR'];
		$q_str = "INSERT IGNORE INTO $table (login_id, date, job, description, ip) VALUES ('$user', NOW(), '$class', '$description', '$ip')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! enable to add record to log file!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func


	###################################################################
	#	sql_log->log_del($mode=0, $id_arry=0, $str )	刪除 log 
	#				
	#	 mode=0, 1, 2 [ 0: 勾選id刪除  1: 刪除全部   :2:條件刪除 (需加入$str) ]
	###################################################################
	function log_del($mode=0, $arry=0, $str="" ) {
	 
		$sql = $this->sql;
		$table = $this->table;
		$id_arry = $this->id_arry;
		$id_arry = $arry;

//		$id_arry = $this->id_arry;
		if ($id_arry ) {

		foreach ($id_arry AS  $id=>$val  ) {
			$q_str = "DELETE FROM $table WHERE id = $id_arry[$id] ";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error! enable to delete LOG records !");
				$this->msg->merge($sql->msg);
				return false;    
				}   // end if not $q_result
			}  // end forech
				return true;

		} else {	// $id_arry=0

			if ($mode == 1 ) {
				$q_str = "DELETE FROM $table ";					// 全部刪除
				if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error ! enable to clear entire LOG files !");
					$this->msg->merge($sql->msg);
					return false;    
				}   // end if mode=1
				return true;		
			} elseif ($mode == 2 ) {
					if (!trim($str)) {
						$this->msg->add("Error! please specify delete condition !");
						$this->msg->merge($sql->msg);
							return false;    
					}
					$str = ereg_replace("[\x5c\]","",$str);     // 刪除反斜線~~~~~nice..........
					
					$q_str = "DELETE FROM $table $str ";  //  條件刪除

				if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error! enable to delete records in LOG file!");
					$this->msg->merge($sql->msg);
					return false;   
				}
			}	else {		// end of mode=2
				$this->msg->add("Error! please specify LOG information !");
				$this->msg->merge($sql->msg);
				return false;  
			}			// end mode

		}

	}  // end func



	###################################################################
	#	sql_log->log_nums($where_str )
	#				
	#		由  $where_str 找出 log  記錄的數目
	###################################################################
	function log_nums($where_str ) {

		$sql = $this->sql;
		$table = $this->table;

		$q_str = "SELECT * FROM $table $where_str ";
		$q_result = $sql->query($q_str);
		
		$num = $sql->num_rows($q_result);
	return $num;     
	}		// end func


	###################################################################
	#	sql_log-> dump_log($date)
	#				
	#					
	###################################################################
	function search($where_str) {
		$sql = $this->sql;
		$table = $this->table;
		$q_str="Select * from log ".$where_str;
//		echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! enable to add record to log file!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$log[]=$row;					   
		}
		if (isset($log))
		{
			return $log;
		}else{
			return false;
		}
	}
	###################################################################
	#	sql_log-> dump_log($date)
	#				
	#					
	###################################################################
	function dump_log($date) {
		$sql = $this->sql;
		$table = $this->table;
		$org_db = "scm_smpl";	//SCM的資料庫
		$dmp_db = "scm_dump"; //DUMP的資料庫
		
		$q_str="Select * from log where date <= '$date'";
//		echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! enable to add record to log file!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$log[]=$row;					   
		}

$j=0;
    if (isset($log))
    {
		$sql->free_result($q_result);
		$sql->db_change($dmp_db);
 		
			
		for ($i=0; $i<sizeof($log); $i++)
		{
			$log[$i]['description'] = str_replace("'","\'",$log[$i]['description']);
			for ($j=0; $j< sizeof($GLOBALS['chin_error_word']); $j++)
			{
				$log[$i]['description'] = str_replace($GLOBALS['chin_error_word'][$j],$GLOBALS['chin_error_word'][$j]."\\",$log[$i]['description']);
			}
			$q_str= "INSERT INTO `log` ( `login_id` , `date` , `job` , `description` , `ip`) VALUES ('"
				  .$log[$i]['login_id']."','".$log[$i]['date']."','".$log[$i]['job']."','"
				  .$log[$i]['description']."','".$log[$i]['ip']."')";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error! enable to add record to log file!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$j++;
		}
		
		$sql->db_change($org_db);

		$q_str = "DELETE FROM log WHERE date <= '$date'";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! enable to add record to log file!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	 }else{
	 	$this->msg->add("Worring! Without any data in tables!");
		$this->msg->merge($sql->msg);
		return false;
	 }
		return $j;
	} // end func


} // end class

?>