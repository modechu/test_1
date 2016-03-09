<?php 

#++++++++++++++++++++++ SIZE_DES  class ++++  尺碼類別 +++++++++++++++++++++++++++++++++++
#	->init($sql)				啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)				加入
#	->search($mode=0)			搜尋   
#	->get($id=0, size_type=0, $where_str="")	抓出指定記錄內資料 RETURN $row[]
#	->edit($parm)				更新 資料
#	->update_field($parm)		更新 資料內 某個單一欄位
#	->del($id)					刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 SIZE TYPE 的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SIZE_DES {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Can't connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新尺碼 資料
#		加入新尺碼資料			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### 檢查輸入項目					
		if (!$parm['cust'] = trim($parm['cust'])) {
			$this->msg->add("Error ! Please input customer name !");
		    return false;
		}

		if (!$parm['check']) {
			$this->msg->add("Error ! Please input currect base size !");
		    return false;
		}

					# 加入資料庫
		$q_str = "INSERT INTO size_des (cust,
										dept,
										
										size,
										base_size,
										size_scale) VALUES ('".
										$parm['cust']."','".
										$parm['dept']."','".
									//	$parm['size_type']."','".
										$parm['size']."','".
										$parm['base_size']."','".
										$parm['size_scale']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$size_type_id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("Successfully add customer:[".$parm['cust']."] size record。") ;
		return $size_type_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	搜尋 尺碼類別	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM size_des WHERE (size_scale <>'' && size_scale IS NOT NULL)".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("cust ");
#####2006.11.14 以數字型式顯示頁碼 star
		$srh->row_per_page = 12;
		$pagesize=10;

		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	}     	
#####2006.11.14 以數字型式顯示頁碼 end
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['size_des'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

#####2006.11.14新頁碼需要的oup_put	start
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];	
#####2006.11.14新頁碼需要的oup_put	end
		return $op;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function wi_search($mode=0, $where_str='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM size_des WHERE (size_scale <>'' && size_scale IS NOT NULL)".$where_str;

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['size_des'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}	
		return $op;
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, size_type=0, $where_str="")	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $size_type=0, $where_str="") {
		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM size_des WHERE id='$id' ";
		} elseif ($size_type) {
			$q_str = "SELECT * FROM size_des WHERE size_type='$size_type' ";
		} elseif ($where_str) {
			$q_str = "SELECT * FROM size_des ".$where_str;
		} else {
			$this->msg->add("Error ! Please point size_des rule");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 尺碼類別 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {
		$sql = $this->sql;
		############### 檢查輸入項目

					# 檢查是否有這個檔案存在
		$q_str = "SELECT id FROM size_des WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! Can't find this record!");
			return false;    	    
		}


		if (!$parm['check']) {
			$this->msg->add("Error ! Please input currect base size !");
		    return false;
		}
		
		#####   更新資料庫內容
		$q_str = "UPDATE size_des SET 
				size='".$parm['size']."', 
				base_size='".$parm['base_size']."',
				size_scale='".$parm['size_scale']
				."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 尺碼類別資料內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE size_des SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 尺碼類別資料[由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point size_des ID.");		    
			return false;
		}
		$q_str = "DELETE FROM size_des WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 SIZE TYPE 的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM size_des ".$where_str;
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access database!");
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