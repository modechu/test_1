<?php 

#++++++++++++++++++++++ SUPL  class ##### 供應商  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						Search   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SUPL {
		
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
#	->add($parm)		加入新 供應商
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	
//增加單引號輸入
		$parm['supl_s_name'] = str_replace("'","\'",$parm['supl_s_name']);
		$parm['supl_f_name'] = str_replace("'","\'",$parm['supl_f_name']);
		$parm['cntc_addr'] = str_replace("'","\'",$parm['cntc_addr']);
		$parm['cntc_person1'] = str_replace("'","\'",$parm['cntc_person1']);
		$parm['cntc_person2'] = str_replace("'","\'",$parm['cntc_person2']);



			//  改成大寫 strtoupper
		$parm['country'] = $parm['country'];	
//		$parm['supl_s_name'] = strtoupper($parm['supl_s_name']);	// 中文沒法 uppercase

		if (!$parm['supl_cat'] = trim($parm['supl_cat'])) {
			$this->msg->add("Error ! Please input supllier catagory !");
		    return false;
		}
		if (!$parm['supl_s_name'] = trim($parm['supl_s_name'])) {
			$this->msg->add("Error ! Please input supplier short name !");
		    return false;
		}
		if (!$parm['vndr_no'] = trim($parm['vndr_no'])) {
			$this->msg->add("Error ! Please input supplier number !");
		    return false;
		}
		if ($parm['usance'] == '') {
			$this->msg->add("Error ! Please choice Delivery !");
		    return false;
		}
		if (!$parm['dm_way'] = trim($parm['dm_way'])) {
			$this->msg->add("Error ! Please choice Payment !");
		    return false;
		}

					# 檢查是否有重覆
		$q_str = "SELECT id FROM supl WHERE vndr_no='".$parm['vndr_no']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This supplier is exist, Please change another one or check again!");
			return false;    	    
		}
		
							# 檢查是否有重覆
		$q_str = "SELECT id FROM supl WHERE supl_s_name='".$parm['supl_s_name']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This supplier short name is exist, Please change another one or check again!");
			return false;    	    
		}
		
		if ($parm['usance'] == "Cash") $parm['usance']="000"; //將現金票期改成->000
		$dm_way=$GLOBALS['dm_way'];
		for ($i=0; $i< 4; $i++)
		{
			if ($parm['dm_way'] == $dm_way[0][$i]) $parm['dm_way']=$dm_way[1][$i];
		}

		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['supl_s_name'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['supl_s_name']);
			$parm['supl_f_name'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['supl_f_name']);
			$parm['cntc_addr'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['cntc_addr']);
			$parm['cntc_person1'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['cntc_person1']);
			$parm['cntc_person2'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['cntc_person2']);
		}
					
					# 加入資料庫
		$q_str = "INSERT INTO supl (supl_cat,
									vndr_no,
									supl_s_name,
									supl_f_name,
									uni_no,
									cntc_addr,
									cntc_phone,
									cntc_fax,
									
									`usance` ,									
									`dm_way` ,
									
									cntc_person1,
									cntc_cell1,
									email1,
									cntc_person2,
									cntc_cell2,
									email2,
									
									country) VALUES('".
									$parm['supl_cat']."','".
									$parm['vndr_no']."','".
									$parm['supl_s_name']."','".
									$parm['supl_f_name']."','".
									$parm['uni_no']."','".
									$parm['cntc_addr']."','".
									$parm['cntc_phone']."','".
									$parm['cntc_fax']."','".
									
									$parm['usance']."','".									
									$parm['dm_way']."','".
									
									$parm['cntc_person1']."','".
									$parm['cntc_cell1']."','".
									$parm['email1']."','".
									$parm['cntc_person2']."','".
									$parm['cntc_cell2']."','".
									$parm['email2']."','".
									
									$parm['country']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id


		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	Search  供應商 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM supl ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if(isset($argv['PHP_action']))$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		if(isset($argv['SCH_sort']))$SCH_sort =  ($argv['SCH_ud'] > 0) ? $argv['SCH_sort'] ."" : $argv['SCH_sort'] ." DESC";
		if(isset($argv['SCH_sort']))$srh->add_sort_condition($SCH_sort);
		
#####2006.11.14 以數字型式顯示頁碼 star
		$srh->row_per_page = 12;
		$pagesize=10;

		if (isset($argv['PHP_sr_startno']) && $argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	}     	
#####2006.11.14 以數字型式顯示頁碼 end

		if(isset($argv['PHP_supl_s_name']))$argv['PHP_supl_s_name'] = str_replace("'","\'",$argv['PHP_supl_s_name']);
		if(isset($argv['PHP_supl_f_name']))$argv['PHP_supl_f_name'] = str_replace("'","\'",$argv['PHP_supl_f_name']);

 
if ($mode == 0){
		if (isset($argv['PHP_country']) && $str = $argv['PHP_country'] )  { 
			$srh->add_where_condition("country = '$str'", "PHP_country",$str,"Search Country = [ $str ]. "); }
		
		if (isset($argv['PHP_supl_cat']) && $str = $argv['PHP_supl_cat'] )  { 
			$srh->add_where_condition("supl_cat = '$str'", "PHP_supl_cat",$str,"Search catagory = [ $str ]. "); }
		
		if (isset($argv['PHP_supl_s_name']) && $str = $argv['PHP_supl_s_name'] )  { 
			$srh->add_where_condition("vndr_no LIKE '%$str%'", "PHP_supl_s_name",$str,"Search supplier # above: [ $str ] "); }

		if (isset($argv['PHP_supl_f_name']) && $str = $argv['PHP_supl_f_name'] )  { 
			$srh->add_where_condition("supl_f_name LIKE '%$str%'", "PHP_supl_f_name",$str,"Search supplier full name above: [ $str ]"); }
}	
		$srh->add_where_condition("vndr_no <> ''");
		$result= $srh->send_query2();
		
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);

		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}

		$op['supl'] = $result;  // 資料錄 拋入 $op		
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


		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $vndr_no=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM supl WHERE id='$id' ";
		} elseif ($vndr_no) {
			$q_str = "SELECT * FROM supl WHERE vndr_no='$vndr_no' ";
		} else {
			$this->msg->add("Error ! Please point supplier ID.");		    
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
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_sname($sname) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM supl WHERE supl_s_name='$sname' ";

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
#	->edit($parm)		更新 供應商 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		
		if ($parm['usance'] == "Cash") $parm['usance']="000"; //將現金票期改成->000
		$dm_way=$GLOBALS['dm_way'];
		for ($i=0; $i< 4; $i++)
		{
			if ($parm['dm_way'] == $dm_way[0][$i]) $parm['dm_way']=$dm_way[1][$i];
		}

		$parm['supl_s_name'] = str_replace("'","\'",$parm['supl_s_name']);
		$parm['supl_f_name'] = str_replace("'","\'",$parm['supl_f_name']);
		$parm['cntc_addr'] = str_replace("'","\'",$parm['cntc_addr']);
		$parm['cntc_person1'] = str_replace("'","\'",$parm['cntc_person1']);
		$parm['cntc_person2'] = str_replace("'","\'",$parm['cntc_person2']);

		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['supl_s_name'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['supl_s_name']);
			$parm['supl_f_name'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['supl_f_name']);
			$parm['cntc_addr'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['cntc_addr']);
			$parm['cntc_person1'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['cntc_person1']);
			$parm['cntc_person2'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['cntc_person2']);
		}
		
		#####   更新資料庫內容
		$q_str = "UPDATE supl SET supl_cat='"		.$parm['supl_cat'].
						"', country='"				.$parm['country'].
						"', supl_f_name='"			.$parm['supl_f_name'].
						"', uni_no='"				.$parm['uni_no'].
						"', cntc_phone='"			.$parm['cntc_phone'].
						"', cntc_addr='"			.$parm['cntc_addr'].
						"', cntc_person1='"			.$parm['cntc_person1'].
						"', cntc_cell1='"			.$parm['cntc_cell1'].
						"', email1='"				.$parm['email1'].
						"', cntc_person2='"			.$parm['cntc_person2'].
						"', cntc_cell2='"			.$parm['cntc_cell2'].
						"', email2='"				.$parm['email2'].
						"', cntc_fax='"				.$parm['cntc_fax'].
						"', `usance`='"				.$parm['usance'].						
						"', `dm_way`='"				.$parm['dm_way'].						
						"'  WHERE id='"				.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 供應商資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE supl SET ".$parm['field_name']."='".strtoupper($parm['field_value'])."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 供應商 資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$vndr_no) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point supplier ID.");		    
			return false;
		}
		$q_str = "DELETE FROM supl WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$q_str = "UPDATE supl3 SET supl_cat=''  WHERE vndr_no='".$vndr_no."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM supl ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			$match_limit = 500;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				/* $match++;
				if ($match==500) {
					break;
				} */
			}
			/* if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			} */

		
		return $fields;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_bank($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_bank($id=0, $vndr_no=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM supl_bank WHERE id='$id' ";
		} elseif ($vndr_no) {
			$q_str = "SELECT * FROM supl_bank WHERE vndr_no='$vndr_no' ";
		} else {
			$this->msg->add("Error ! Please point supplier ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result)) {
			$rows[] = $row;
		}
		return $rows;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bank_edit($parm)		更新 供應商_銀行 資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bank_edit($parm) {

		$sql = $this->sql;
		
		#####   更新資料庫內容
		$q_str = "UPDATE supl_bank SET bank_code='".strtoupper($parm['bank_code']).
						"', bank_name='".strtoupper($parm['bank_name']).
						"', account='".strtoupper($parm['account'])."' where id='".$parm['id']."'";
						
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bank_add($parm)		更新 供應商_銀行 資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bank_add($parm) {

		$sql = $this->sql;
		
		#####   新增資料庫內容
		$q_str = "INSERT INTO supl_bank (vndr_no,bank_code,bank_name,account) VALUES('".
				strtoupper($parm['vndr_no'])."','".
				strtoupper($parm['bank_code'])."','".
				strtoupper($parm['bank_name'])."','".
				strtoupper($parm['account'])."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bank_del($parm)		更新 供應商_銀行 資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bank_del($id) {

		$sql = $this->sql;
		
		$q_str = "delete from supl_bank where id='$id'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	}
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>