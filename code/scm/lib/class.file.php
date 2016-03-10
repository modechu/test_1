<?php 

#++++++++++++++++++++++ TI  class ##### 生產說明  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class  FILES {
		
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
#	->upload_check($file_name,$num)	確認檔案是否存在
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function upload_check($file_name)
	{
		$sql = $this->sql;
		$q_str = "SELECT id FROM file_det where file_name= '".$file_name."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);
		if ($row['id'])
		{
			return false;
		}else{
			return true;
		}
	}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}		
		$q_str = "INSERT INTO file_det (file_name,
										num,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['num']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 
		return $ff_id;	
	}
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_smpl_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function update_file($file_name,$id)
	{
			$sql = $this->sql;

		$q_str="UPDATE file_det set file_name = '".$file_name."'where id = ".$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		

		return true;	
	}
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_bom_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}
		$q_str = "INSERT INTO bom_file_det (file_name,
										num,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['num']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 
		return $ff_id;	
	}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_bom_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function update_bom_file($file_name,$id)
	{
			$sql = $this->sql;

		$q_str="UPDATE bom_file_det set file_name = '".$file_name."'where id = ".$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		

		return true;	
	}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_smpl_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_smpl_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}
					
		$q_str = "INSERT INTO smpl_file_det (file_name,
										num,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['num']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 

		return $ff_id;	
	}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_smpl_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function update_smpl_file($file_name,$id)
	{
			$sql = $this->sql;

		$q_str="UPDATE smpl_file_det set file_name = '".$file_name."'where id = ".$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		

		return true;	
	}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_file($num) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT * FROM file_det where num='".$num."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
				$fields[] = $row;				
			}		
		return $fields;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_bom_file($num) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT * FROM bom_file_det where num='".$num."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
				$fields[] = $row;				
			}		
		return $fields;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_smpl_file($num) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT * FROM smpl_file_det where num='".$num."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
				$fields[] = $row;				
			}		
		return $fields;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_file($table,$id) {
		$sql = $this->sql;
		$fields = array();  
		$q_str = "DELETE FROM ".$table." WHERE id='".$id."'";
		//echo $q_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_name_id($tbl) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT id FROM ".$tbl." order by id desc limit 1";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$row = $sql->fetch($q_result);
	if(!isset($row[0]))$row[0] = 0;
	$row[0]	++;
		return $row[0];
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_po_ship_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}
		$q_str = "INSERT INTO po_ship_file (file_name,
										ship_id,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['id']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 
		return $ff_id;	
	}
	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_cm_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_cm_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}
		$q_str = "INSERT INTO remun_file (file_name,
										num,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['id']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 
		return $ff_id;	
	}	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_smpl_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_exc_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}
		$q_str = "INSERT INTO exc_file_det (file_name,
										ex_id,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['ex_id']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 

		return $ff_id;	
	}	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_ord_qc_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_ord_qc_file($parm)
	{
		$sql = $this->sql;

		$q_str = "INSERT INTO ord_qc_file (file_name,
										ord_num,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['num']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 

		return $ff_id;	
	}	
	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_shipdoc_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}		
		$q_str = "INSERT INTO shipdoc_file (file_name,
										ship_id,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['id']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 
		return $ff_id;	
	}
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_receive_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}
		$q_str = "INSERT INTO receive_file (file_name,
										rcv_id,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['id']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 
		return $ff_id;	
	}	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_pi_file($parm)	記錄上傳資料及上傳說明
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function upload_pi_file($parm)
	{
		$sql = $this->sql;
		$parm['file_des'] = str_replace("'","\'",$parm['file_des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['file_des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['file_des']);
		}
		$q_str = "INSERT INTO ap_file (file_name,
										po_id,
										file_des,
										file_user,
										file_date) VALUES('".
										$parm['file_name']."','".
										$parm['po_id']."','".
										$parm['file_des']."','".
										$parm['file_user']."','".
										$parm['file_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		$ff_id = $sql->insert_id(); 
		return $ff_id;	
	}
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>