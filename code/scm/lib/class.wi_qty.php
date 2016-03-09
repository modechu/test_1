<?php 

#++++++++++++++++++++++ WI_QTY  class #####  製造令數量 +++++++++++++++++++++++++++++++++++
#	->init($sql)				啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)				加入
#	->search($mode=0)			搜尋   
#	->get($id=0, nbr=0)			抓出指定 記錄內資料   
#	->edit($parm)				更新 資料
#	->update_field($parm)		更新 資料內 某個單一欄位
#	->del($id)					刪除 資料錄
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class WI_QTY {
		
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
#	->add($parm)		加入新 製造令數量記錄
#		加入新組別			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
		$admin =  new AUTH();   // 將會呼叫到的 class    ?????????????????????????????
		$perm_array = array();
		
			
					# 加入資料庫
		$q_str = "INSERT INTO wi_qty (	wi_id, 
										colorway, 
										size, 
										qty) VALUES('".
										$parm['wi_id']."','".
										$parm['colorway']."','".
										$parm['size']."','".
										$parm['qty']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$team_id = $sql->insert_id();  //取出 新的 id
//		$this->msg->add("成功 登入製造令 數量記錄: [".$parm['wi_num']."]。") ;
		return $parm;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	搜尋 製造令數量檔 的記錄	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if($mode=1){
			$q_header = "SELECT * FROM wi_qty ".$where_str;
			if (!$srh->add_q_header($q_header)) {
				$this->msg->merge($srh->msg);
				return false;
			}
		} else {
			$q_header = "SELECT * FROM wi_qty";
			if (!$srh->add_q_header($q_header)) {
				$this->msg->merge($srh->msg);
				return false;
			}
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id");
		$srh->row_per_page = 500;

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

		$op['wi_qty'] = $result;  // 資料錄 拋入 $op
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

		return $result;
//		return $op['wi_qty'];
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, team_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/*	function get($id=0, $team_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM team WHERE id='$id' ";
		} elseif ($team_code) {
			$q_str = "SELECT * FROM team WHERE team_code='$team_code' ";
		} else {
			$this->msg->add("Error ! 請指明 部門資料在資料庫內的 ID.");		    
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($field_name,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($field_name, $where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$field_name." FROM team ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			$match_limit = 100;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				$match++;
				if ($match==100) {
					break;
				}
			}
			if ($match != 100) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field($field, $where_str="")	抓出指定記錄內資料 RETURN $field_body[0]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_field($field, $where_str="") {

		$sql = $this->sql;

			$q_str = "SELECT $field FROM team WHERE $where_str ";
		if (!$field){	
			$this->msg->add("Error ! 請指明 要取得的資料表內的 欄位名稱.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$field_body = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return false;    
		}
		return $field_body[0];
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 組別資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		$admin =  new AUTH();   // 將會呼叫到的 class

		############### 檢查輸入項目
					# 全部改成大寫
		$parm['team_code'] = strtoupper($parm['team_code']);	

					# 檢查是否有這個檔案存在
		$q_str = "SELECT id FROM team WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! 無法找到資料庫記錄!");
			return false;    	    
		}

###########  撿查 新加入的 team_code 是否為唯一 [note:可能會找到原來的自己 #####  部門的唯一[非全部唯一]
$q_str2 = "SELECT id FROM team WHERE team_code='".$parm['team_code']."'"." AND id <> '".$parm['id']."'"." AND dept_code = '".$parm['dept_code']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			return false;    	    
		}
		if ($sql->num_rows($q_result2) ) {
			$this->msg->add("SORRY ! 這個組別代號已經建立過了，請換別的代號!");
			return false;    	    
		}
		
		// 權限陣列改成字串
		$perm_array = $parm['perm'];
		$parm['perm_str'] = $admin->encode_perm_str($perm_array);

		#####   更新資料庫內容
		$q_str = "UPDATE team SET team_code='".$parm['team_code']."', team_name='".$parm['team_name']."', dept_code='". $parm['dept_code']."', perm='".$parm['perm_str']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 產品基本資料內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE team SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
*/

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id, $wi='')		刪除  製造令數量記錄 [由ID, 製令號碼]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id, $wi='') {

		$sql = $this->sql;

		if ($wi){
			$q_str = "DELETE FROM wi_qty WHERE wi_id='$wi' ";
		}else{
			if (!$id) {
				$this->msg->add("Error !請指明 製作令數量 資料表的 ID.");		    
				return false;
			}
			$q_str = "DELETE FROM wi_qty WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



} // end class


?>