<?php 

#++++++++++++++++++++++ USER  class ##### 使用者  +++++++++++++++++++++++++++++++++++
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

class USER {
		
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
#	->check($parm)		加入新 USER 之撿查
#	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$sql = $this->sql;
			############### 檢查輸入項目					
		if (!$parm['dept_code'] = trim($parm['dept_code'])) {
			$this->msg->add("Error ! 請輸入部門代號 !");
		    return false;
		}
		if (!$parm['login_id'] = trim($parm['login_id'])) {
			$this->msg->add("Error ! 請輸入使用者代號[login ID !");
		    return false;
		}
		if (!$parm['login_pass1'] = trim($parm['login_pass1'])) {
			$this->msg->add("Error ! 請輸入使用者密碼[login password !");
		    return false;
		}
		if (!$parm['login_pass2'] = trim($parm['login_pass2'])) {
			$this->msg->add("Error ! 請記得輸入使用者密碼兩次[login password !");
		    return false;
		}
		if (!$parm['login_pass1'] == $parm['login_pass2']) {
			$this->msg->add("sorry! 兩次密碼輸入不同，請注意大小寫差別 !");
		    return false;
		}
		if ($parm['login_pass1'] != $parm['login_pass2']) {
			$this->msg->add("sorry! 兩次密碼輸入不同，請注意大小寫差別 !");
		    return false;
		}
		if (!$parm['team_code'] = trim($parm['team_code'])) {
			$this->msg->add("Error ! 請選擇組別代號 !");
		    return false;
		}

					# 檢查是否有重覆
		$q_str = "SELECT id FROM user WHERE login_id='".$parm['login_id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if ($sql->num_rows($q_result) || is_manager($parm['login_id'])) {
			$this->msg->add("sorry ! 這個代號已經有別人使用了，請換別的代號!");
			return false;    	    
		}

		$this->msg->add("請再確認新增user 的行使權限 ! ") ;

	return true;

	} // end functin		
					

					
					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 USER
#		加入新 USER			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
					
					# 加入資料庫
					
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['name'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['name']);
		}
		$q_str = "INSERT INTO user (emp_id, dept, login_id, login_pass, team_id, name, phone, cellphone, email, perm) VALUES ('".$parm['emp_id']."','".$parm['dept_code']."','".$parm['login_id']."','".$parm['login_pass']."','".$parm['team_code']."','".$parm['name']."','".$parm['phone']."','".$parm['cellphone']."','".$parm['email']."','".$parm['perm']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("成功 新增user : [".$parm['login_id']."]。") ;
		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  USER 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM user ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		if(isset($argv['PHP_sort']))
		{
			if ($argv['PHP_sort'])
			{
		
				$srh->add_sort_condition($argv['PHP_sort']);
			}else{
				$srh->add_sort_condition("dept");
			}
		}else{
			$srh->add_sort_condition("dept");
		}
	if($where_str) $srh->add_where_condition($where_str); 	

    if($str = @$argv['SCH_del']){
        if($str == '2')
            $srh->add_where_condition("active = 'N'", "SCH_del",$str,""); 
    } else {
        $srh->add_where_condition("active = 'Y'", "SCH_del",$str,"");
    }
    
	if ($mode > 1)
	{
		if($str = $argv['PHP_dept']){
			$srh->add_where_condition("dept = '$str'", "PHP_dept",$str,"Dept = [ $str ] "); 
		}
		if($str = $argv['PHP_logid']){
			$srh->add_where_condition("login_id  like '%$str%'", "PHP_logid",$str,"Login-id like [ $str ] "); 
		}
		if($str = $argv['PHP_name']){
			$srh->add_where_condition("name like '%$str%'", "PHP_dept",$str,"Name like [ $str ] "); 
		}
		if ($mode == 1) $srh->add_where_condition("active  = 'Y'"); 
		
	}
#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$srh->row_per_page = 10;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 以數字型式顯示頁碼 end    	
 		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
			
			
//2006.11.10增加判定是否可以新增team的資料			
		$q_dep_str = "select id from dept";
		$q_dep_result = $sql->query($q_dep_str);
		if ($sql->fetch($q_dep_result)){ $op['dep_check']=1; }


		$op['user'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
#--*****--##2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#--*****--##2006.11.14新頁碼需要的oup_put	end   

		return $op;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, login_id=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $login_id=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM user WHERE id='$id' ";
		} elseif ($login_id) {
			$q_str = "SELECT * FROM user WHERE login_id='$login_id' ";
		} else {
			$this->msg->add("Error ! 請指明 USER資料在資料庫內的 ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if (!$row = $sql->fetch($q_result)) {

			$this->msg->add("Error ! 無法找到這筆記錄!");
			if($login_id == 'yankee')
			{
				$row['name'] = '楊基國';
				$row['dept'] = 'SA';
				$row['email'] = 'jack@tp.carnival.com.tw';
			}else{
				return false; 
			}  
		}		

		
		
		return $row;
	} // end func


/*----------
# 功能說明 : 更新 USER 資料
# 導入變數 : 	
# 輸入變數 : 
# 輸出變數 : true
# 撰寫日期 : 2010/06/01
# 備註說明 : 
----------*/
function edit($parm) {
  global $MySQL;

  $q_str = "UPDATE user SET 
			emp_id='"		.$parm['emp_id'].
          "', dept='"		.$parm['dept'].
          "', team_id='"	.$parm['team_id'].
          "', name='"		.$parm['name'].
          "', phone='"		.$parm['phone'].
          "', cellphone='"	.$parm['cellphone'].
          "', email='"		.$parm['email'].
          "', perm='"		.$parm['perm'].
          "'  WHERE id='"	.$parm['id']."'";

  if (!$q_result = $MySQL->query($q_str)) {
    $_SESSION['msg'][] = ("Error !  無法更新資料庫.");
    return false;    
  }

  $_SESSION['USER']['ADMIN']['name'] = $parm['name'];

  return true;
} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_team($n_field,$where_str="")	加總team和user的值作比較,預防必要給user的權限比team來的大
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_team($perm,$dept_code,$team_code){
    global $MySQL;

		$q_str = "SELECT `perm` FROM `team` WHERE `dept_code` LIKE '".$dept_code ."' AND `team_code` LIKE '".$team_code."' ";

		if (!$q_result = $MySQL->query($q_str)) { 
			$_SESSION['msg'][] = ("Error! 無法存取資料庫!");
			return false;
		}
	
		if (!$row = $MySQL->fetch($q_result)) {
 			$_SESSION['msg'][] = ("Error ! 無法找到這筆記錄!"); 
 			return false;    
		}

		$perm_m = "";
		$perm_team = split(",",$row['perm']);
		for ($m=0;$m < count($perm_team);$m++){
			$perm_team_code = split("-",$perm_team[$m]);
			@$perm_m = $perm_m + $perm_team_code[1];
		}
	
		$perm_o = "";
		$perm_me = split(",",$perm);
		for ($o=0;$o < count($perm_me);$o++){
			$perm_me_code = split("-",$perm_me[$o]);	
			@$perm_o = $perm_o + $perm_me_code[1];
		} 

		($perm_m > $perm_o)? $perm_count = $row['perm'] : $perm_count = $perm;

		return $perm_count;
	} // end func
	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 USER 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE user SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 USER 資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 USER 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM user WHERE id='$id' ";

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
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM user ".$where_str;
// echo $q_str;
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