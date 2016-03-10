<?php 

#++++++++++++++++++++++ ACC  class ##### 副料  +++++++++++++++++++++++++++++++++++
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

class ACC {
		
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> buy_get_no($hend,$n_field,$tables)	為新單據做編號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_no($hend,$n_field,$tables) {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT ". $n_field." FROM ".$tables." where ".$n_field. " like '%".$hend."%' order by ".$n_field." desc limit 1";
		if (!$q_result = $sql->query($q_str)) {		//搜尋最後一筆
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//如果沒有資料的話
			$buy_no = '1';
		
		}else{	//將最後一筆的數字+1
			$long = strlen($hend);
			$buy_no = substr($row[$n_field],$long);	//去掉表頭
			$tmp=explode('-',$buy_no);
			$buy_no=$tmp[0].$tmp[1];
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//在數字前補0到達四位數字
		{
			$buy_no=$hend."000-00".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$hend."000-0".$buy_no;
		}else if(strlen($buy_no) == 3){
			$buy_no=$hend."000-".$buy_no;			
		}else if(strlen($buy_no) == 4){
			$buy_no=substr($buy_no,0,1)."-".substr($buy_no,1);
	    $buy_no=$hend."00".$buy_no;
	  }else if(strlen($buy_no) == 5){
	  	$buy_no=substr($buy_no,0,2)."-".substr($buy_no,2);
	  	$buy_no=$hend."0".$buy_no;
	  }else{
	  	$buy_no=substr($buy_no,0,3)."-".substr($buy_no,3);
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func
				
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 副料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	
			//  改成大寫
//		$parm['acc_code'] = strtoupper($parm['acc_code']);	

//		if (!$parm['acc_code'] = trim($parm['acc_code'])) {
//			$this->msg->add("Error ! Please input Accessory code [short name] !");
//		    return false;
//		}
		
		if (!$parm['acc_name'] = trim($parm['acc_name'])) {
			$this->msg->add("Error ! Please input Accessory Name  !");
		    return false;
		}
/*
					# 檢查是否有重覆
		$q_str = "SELECT id FROM acc WHERE acc_code='".$parm['acc_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This accessory code is exist in database, please change another one or check again!");
			return false;    	    
		}
*/


		$parm['des'] 		 = str_replace("'","\'",$parm['des']);
		$parm['specify'] = str_replace("'","\'",$parm['specify']);

				
					# 加入資料庫
		$q_str = "INSERT INTO acc (acc_code,
									acc_name,
									des,
									specify,
									mile_code,
									mile_name,
									arrange,
									vendor1,									
									price1,
									unit1,
									currency1,
									term1,									
									vendor2,									
									price2,
									unit2,
									currency2,
									term2,									
									vendor3,								
									price3,
									unit3,
									term3,
									currency3
									) VALUES('".
									$parm['acc_code']."','".
									$parm['acc_name']."','".
									$parm['des']."','".
									$parm['specify']."','".
									$parm['mile_code']."','".
									$parm['mile_name']."','".
									$parm['size_mk']."','".
									$parm['vendor1']."','".									
									$parm['price1']."','".
									$parm['unit1']."','".
									$parm['currency1']."','".
									$parm['term1']."','".									
									$parm['vendor2']."','".									
									$parm['price2']."','".
									$parm['unit2']."','".
									$parm['currency2']."','".
									$parm['term2']."','".								
									$parm['vendor3']."','".									
									$parm['price3']."','".
									$parm['unit3']."','".
									$parm['term3']."','".									
									$parm['currency3']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增副料記錄: [".$parm['acc_code']."]。") ;

		return $new_id;

	} // end func

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 副料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_parts($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	
			//  改成大寫
		$parm['num'] = strtoupper($parm['num']);	

		if (!$parm['num'] = trim($parm['num'])) {
			$this->msg->add("Error ! Please input Accessory code [short name] !");
		    return false;
		}


					# 檢查是否有重覆
		$q_str = "SELECT id FROM acc WHERE acc_code='".$parm['num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This accessory code is exist in database, please change another one or check again!");
			return false;    	    
		}
				
					# 加入資料庫
		$q_str = "INSERT INTO acc (acc_code,
									acc_name,
									
									unit1									
									) VALUES('".
									$parm['num']."','".
									$parm['name']."','".
														
									$parm['unit']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增副料記錄: [".$parm['acc_code']."]。") ;

		return $new_id;

	} // end func	
*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	Search  副料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0) {

		$sql = $this->sql;
		if($mode == 2)
		{
			$argv = $_SESSION['sch_parm'];
		}else{
			$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		}			
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM acc ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		if(isset($argv['SCH_sort']))$SCH_sort =  ($argv['SCH_ud'] > 0) ? $argv['SCH_sort'] ."" : $argv['SCH_sort'] ." DESC";
		if(isset($argv['SCH_sort']))$srh->add_sort_condition($SCH_sort);
		
//2006.11.14 以數字型式顯示頁碼 star		
		$srh->row_per_page = 12;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 以數字型式顯示頁碼 end  
   

if ($mode > 0){    //有條件的Search時
	
		$mesg = '';
		if ($str = $argv['PHP_acc_code'] )  { 
			$srh->add_where_condition("acc_code LIKE  '%$str%'", "PHP_acc_code",$str); 
			$mesg .=" Code :[$str].";
		}
		
		if ($str = $argv['PHP_acc_name'] )  { 
			$srh->add_where_condition("acc_name LIKE  '%$str%'", "PHP_acc_name",$str); 
			$mesg .=" Name :[$str].";
		}

		if ($str = $argv['PHP_des'] )  { 
			$srh->add_where_condition("des LIKE '%$str%'", "PHP_des",$str); 
			$mesg .=" Description :[$str].";
		
		}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
}
		#M11021503 修改 List 排序
		$srh->add_sort_condition("`id` DESC ");
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
		
#####2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14新頁碼需要的oup_put	end
		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_cat($mode=0)	Search  副料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cat($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM acc_category ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("acc_key");
		
//2006.11.14 以數字型式顯示頁碼 star		
		$srh->row_per_page = 12;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 以數字型式顯示頁碼 end  
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['acc_cat'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
//		$op['prev_no'] = $srh->prev_no;
//		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
//		$op['last_no'] = $srh->last_no;
//		$op['start_no'] = $srh->start_no;
//		$op['per_page'] = $srh->row_per_page;
		
#####2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14新頁碼需要的oup_put	end
		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $acc_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $acc_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM acc WHERE id='$id' ";
		} elseif ($acc_code) {
			$q_str = "SELECT * FROM acc WHERE acc_code='$acc_code' ";
		} else {
			$this->msg->add("Error ! Please point Accessory ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record!");
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
#	->edit($parm)		更新 副料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		
		
		#####   更新資料庫內容
		
		$parm['des'] 		 = str_replace("'","\'",$parm['des']);
		$parm['specify'] = str_replace("'","\'",$parm['specify']);

		$q_str = "UPDATE acc SET acc_name='"	.$parm['acc_name'].
							"', des='"				.$parm['des'].
							"', specify='"		.$parm['specify'].
							"', mile_code='"	.$parm['mile_code'].
							"', mile_name='"	.$parm['mile_name'].
							
							"', arrange='"		.$parm['size_mk'].
							
							"', vendor1='"		.$parm['vendor1'].							
							"', price1='"			.$parm['price1'].
							"', unit1='"			.$parm['unit1'].
							"', currency1='"	.$parm['currency1'].
							"', term1='"			.$parm['term1'].							
							"', vendor2='"		.$parm['vendor2'].						
							"', price2='"			.$parm['price2'].
							"', unit2='"			.$parm['unit2'].
							"', currency2='"	.$parm['currency2'].
							"', term2='"			.$parm['term2'].							
							"', vendor3='"		.$parm['vendor3'].						
							"', price3='"			.$parm['price3'].
							"', unit3='"			.$parm['unit3'].
							"', currency3='"	.$parm['currency3'].
							"', term3='"			.$parm['term3'].
							"', last_user='"	.$parm['user'].							
							"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$acc=$this->get($parm['id']);
		$q_str= "Update smpl_acc_use SET acc_name = '".$parm['acc_name']."' WHERE acc_code = '".$acc['acc_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$q_str= "Update acc_use SET acc_name = '".$parm['acc_name']."' WHERE acc_code = '".$acc['acc_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE acc SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_code($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE acc SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE acc_code='".$parm['code']."'";
		// echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 副料記錄  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point Accessory ID.");		    
			return false;
		}
		$q_str = "DELETE FROM acc WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record !");
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
		$q_str = "SELECT ".$n_field." FROM acc ".$where_str;
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
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
#	-> get_acc_name() 取得副料類別(Accessory Name)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_acc_name() {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT name FROM acc_category";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			while ($row = $sql->fetch($q_result)) {
				if ($row[0]<>'other') $fields[] = $row[0];
			}
			$fields[] ='other';
		return $fields;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_key()	取得副料類別的代號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_acc_key($name) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT acc_key FROM acc_category WHERE name ='$name'";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);
		return $row['acc_key'];
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_key()	取得副料類別的代號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_max_key() {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT max(acc_key)as acc_key FROM acc_category ";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);
		return $row['acc_key'];
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> add_cat($parm)		加入新 副料類別記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cat($parm) {
					
		$sql = $this->sql;
				
					# 加入資料庫
		$q_str = "INSERT INTO acc_category (acc_key,
									name
									) VALUES('".
									$parm['acc_key']."','".
									$parm['name']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

		return $new_id;

	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cat($id=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cat($id=0) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM acc_category WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record!");
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
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_cat($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE acc_category SET name ='".$parm['name']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>