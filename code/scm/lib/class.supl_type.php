<?php 

#++++++++++++++++++++++ SUPL_TYPE  class ++++  供應商類別 +++++++++++++++++++++++++++++++++++
#	->init($sql)				啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)				加入
#	->search($mode=0)			搜尋   
#	->get($id=0, nbr=0)			抓出指定 記錄內資料   
#	->update($parm)				更新 資料
#	->update_field($parm)		更新 資料內 某個單一欄位
#	->del($id)					刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 供應商類別表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SUPL_TYPE {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! 無法聯結資料庫.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新供應商類別
#		加入新供應商類別			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### 檢查輸入項目					
		if (!$parm['supl_type'] = trim($parm['supl_type'])) {
			$this->msg->add("Error ! 請輸入供應商類別 !");
		    return false;
		}
					# 全部改成大寫
//		$parm['supl_type'] = strtoupper($parm['supl_type']);	

					# 檢查是否有重覆
		$q_str = "SELECT id FROM supl_type WHERE supl_type='".$parm['supl_type']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! 這個供應商類別已經登錄了，請換別的類別或更改原內容!");
			return false;    	    
		}
					# 加入資料庫
		$q_str = "INSERT INTO supl_type (supl_type, des) VALUES ('".$parm['supl_type']."','".$parm['des']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$supl_type_id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("成功 新增供應商類別 : [".$parm['supl_type']."]。") ;
		return $supl_type_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	搜尋 供應商類別 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM supl_type";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("supl_type ");
		$srh->row_per_page = 12;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
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

		$op['supl_type'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, supl_type=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $supl_type=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM supl_type WHERE id='$id' ";
		} elseif ($supl_type) {
			$q_str = "SELECT * FROM supl_type WHERE supl_type='$supl_type' ";
		} else {
			$this->msg->add("Error ! 請指明 供應商類別 在資料庫內的 ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return false;    
		}
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		更新 供應商類別 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
					# 全部改成大寫
//		$parm['supl_type'] = strtoupper($parm['supl_type']);	

					# 檢查是否有這個檔案存在
		$q_str = "SELECT id FROM supl_type WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! 無法找到資料庫記錄!");
			return false;    	    
		}

###########  撿查 新加入的 supl_type 是否為唯一 [note:可能會找到原來的自己 #####
$q_str2 = "SELECT id FROM supl_type WHERE supl_type='".$parm['supl_type']."'"." AND id <> '".$parm['id']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			return false;    	    
		}
		if ($sql->num_rows($q_result2) ) {
			$this->msg->add("SORRY ! 這個 供應商類別已經登錄資料庫內，請換別的名稱或是更新原記錄!");
			return false;    	    
		}
		
		#####   更新資料庫內容
		$q_str = "UPDATE supl_type SET supl_type='".$parm['supl_type']."', des='".$parm['des']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 供應商類別 資料內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE supl_type SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 供應商類別 資料[由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 supl_type 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM supl_type WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 供應商類別表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM supl_type".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
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
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>