<?php 

#++++++++++++++++++++++ BOM  class ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add_lots($parm)		加入新 BOM 主料

#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class  SMPL_BOM {
		
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
#	->add_lots($parm)		加入新 BOM 主料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_lots($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	

					
					# 加入資料庫
		$q_str = "INSERT INTO smpl_bom_lots (wi_id,
									lots_used_id,
									color,
									qty,
									k_date) VALUES('".
									$parm['wi_id']."','".
									$parm['lots_used_id']."','".
									$parm['color']."','".
									$parm['qty']."','".
									$parm['this_day']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("成功 新增 BOM 主料記錄。") ;

		return $new_id;

	} // end func
	

					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_acc($parm)		加入新 BOM 副料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_acc($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	

					
					# 加入資料庫
		$q_str = "INSERT INTO smpl_bom_acc (wi_id,
									acc_used_id,
									color,
									qty,
									size,
									k_date) VALUES('".
									$parm['wi_id']."','".
									$parm['acc_used_id']."','".
									$parm['color']."','".
									$parm['qty']."','".
									$parm['size']."','".
									$parm['this_day']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("成功 新增 BOM 副料記錄。") ;

		return $new_id;

	} // end func
	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lots ($mode=0, $where_str="")	搜尋  BOM 之主料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_lots($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM smpl_bom_lots ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 200;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
		} 
if ($mode==1){    //有條件的搜尋時
/*
		if ($str = $argv['PHP_acc_code'] )  { 
			$srh->add_where_condition("acc_code LIKE  '%$str%'", "PHP_acc_code",$str,"搜尋 生產說明代號含有: [ $str ]內容. "); }
		
		if ($str = $argv['PHP_acc_name'] )  { 
			$srh->add_where_condition("acc_name LIKE  '%$str%'", "PHP_acc_name",$str,"搜尋 生產說明名稱含有: [ $str ]內容. "); }

		if ($str = $argv['PHP_des'] )  { 
			$srh->add_where_condition("des LIKE '%$str%'", "PHP_des",$str,"搜尋 生產說明說明含有: [ $str ]內容 "); }

		if ($str = $argv['PHP_specify'] )  { 
			$srh->add_where_condition("specify LIKE '%$str%'", "PHP_specify",$str,"搜尋 生產說明規格含有: [ $str ]內容 "); }
*/
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

		$op['lots'] = $result;  // 資料錄 拋入 $op
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
#	->search_acc ($mode=0, $where_str="")	搜尋  BOM 之副料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_acc($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM smpl_bom_acc ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 200;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
		} 
if ($mode==1){    //有條件的搜尋時
/*
		if ($str = $argv['PHP_acc_code'] )  { 
			$srh->add_where_condition("acc_code LIKE  '%$str%'", "PHP_acc_code",$str,"搜尋 生產說明代號含有: [ $str ]內容. "); }
		
		if ($str = $argv['PHP_acc_name'] )  { 
			$srh->add_where_condition("acc_name LIKE  '%$str%'", "PHP_acc_name",$str,"搜尋 生產說明名稱含有: [ $str ]內容. "); }

		if ($str = $argv['PHP_des'] )  { 
			$srh->add_where_condition("des LIKE '%$str%'", "PHP_des",$str,"搜尋 生產說明說明含有: [ $str ]內容 "); }

		if ($str = $argv['PHP_specify'] )  { 
			$srh->add_where_condition("specify LIKE '%$str%'", "PHP_specify",$str,"搜尋 生產說明規格含有: [ $str ]內容 "); }
*/
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

		$op['acc'] = $result;  // 資料錄 拋入 $op
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

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_id=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $acc_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM ti WHERE id='$id' ";
		} elseif ($acc_code) {
			$q_str = "SELECT * FROM ti WHERE wi_id='$wi_id' ";
		} else {
			$this->msg->add("Error ! 請指明 生產說明資料在資料庫內的 ID.");		    
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
*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_lots($id,$mode=0)		刪除 BOM 主料記錄 
#					[mode=0: $id為smpl_bom_lots的id [單筆刪除] ; 
#					[mode=1: $id為wi_id 為整個 將 指定之製令的全部BOM主料記錄都刪除 ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_lots($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 BOM的主料資料的 ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM smpl_bom_lots WHERE wi_id='$id' ";
		}else{
			$q_str = "DELETE FROM smpl_bom_lots WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_acc($id,$mode=0)		刪除 BOM 副料記錄 
#					[mode=0: $id為smpl_bom_lots的id [單筆刪除] ; 
#					[mode=1: $id為wi_id 為整個 將 指定之製令的全部BOM副料記錄都刪除 ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_acc($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 BOM的副料資料的 ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM smpl_bom_acc WHERE wi_id='$id' ";
		}else{
			$q_str = "DELETE FROM smpl_bom_acc WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_lots($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl_bom_lots ".$where_str;
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_acc($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl_bom_acc ".$where_str;

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
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE ".$parm[3]." SET ".$parm[1]."='".$parm[2]."'  WHERE id='".$parm[0]."'";
echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>