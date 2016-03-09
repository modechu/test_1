<?php 

#++++++++++++++++++++++ SAMPLE OUT PUT  class ##### 樣本完成 CLASS  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入

#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL_OUTPUT {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! cannot connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 out put 記錄
#		加入新 USER			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	

		if (!is_numeric($parm['qty'])) {
			$this->msg->add("Error ! please type <b>only numberic</b> for Quantity。");
			return false;    
		}
					# 加入資料庫
		$q_str = "INSERT INTO smpl_output (num, line_id, factory, fdate, qty) VALUES ('".$parm['num']."','".$parm['line_id']."','".$parm['factory']."','".$parm['fdate']."', qty + ".$parm['qty'].")";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot add new record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增客戶 : [".$parm['login_id']."]。") ;

		return $new_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 產品基本資料[由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($num) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 smpl_type 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM smpl_output WHERE num ='$num' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_link($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($ord_num) {
		$ord_rec = array();
		$sql = $this->sql;
		$ord_num = substr($ord_num,0,-2);
		$q_str = "SELECT * FROM   smpl_output
							WHERE  num = '".$ord_num."'";

		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result))
		{
			$ord_rec[] = $row;
		}

		return $ord_rec;
	} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_link($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_output($ord_num) {
		$ord_rec = array();
		$sql = $this->sql;
//		$ord_num = substr($ord_num,0,-2);
		$q_str = "SELECT smpl_output.*, line as line_name FROM   smpl_output, smpl_saw_line
							WHERE  smpl_output.line_id = smpl_saw_line.id AND num = '".$ord_num."'";

		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result))
		{
			$ord_rec[] = $row;
		}

		return $ord_rec;
	} // end func			

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>