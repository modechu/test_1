<?php 

	######################### Auth  class ##### 管理員 ###########################
	#	auth->init($sql)				啟始 (使用 Msg_handle(); 先聯上 sql)
	#	auth->extra_perm_str($perm_def)		權限字串 解譯   $perm_def 為 字串 [,]
	#	auth->decode_perm_str($admin_data)	授權陣列碼 解譯	傳回陣列 :$decode
	#	auth->decode_perm($i,$j)			授權碼解譯		傳回 :$decode
	#	auth->full_perm							最高授權碼解譯	傳回 :$decode
	#	auth->encode_perm($perm_ary)			授權碼 加碼
	#	auth->encode_perm_str($perm_array)		授權碼 加碼後將陣列組成字串
	#	auth->login($login_id,$login_pass)		登入
	#	auth->logout()							登出
	#	auth->is_power($i, $j, $job="view")		由傳入的 值 判定是否有這個權限 
	#	auth->is_perm($i,$j)	 檢查授權狀況 檢查可否有進入的權     ??????
	#	auth->admin_add($parm)					加入user 記錄 傳回 $admin_id
	#	auth->change_pass($parm)		更新 密碼
	#	auth->admin_update($mode,$parm)		更新 管理員資料(mode=0,1,2)
	#	auth->admin_get($id=0,$login_id=0)	取出user記錄
	#	auth->admin_browse($mode=0)			瀏覽管理員列表
	#
	#	auth->admin_log_search($mode=0)		搜尋 管理員日誌 [引入 search class]
	#
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class AUTH {
		
	var $sql;
	var $msg ;
	
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->init($sql)	啟始(使用 Msg_handle()  ; 先聯上 sql)
	#					必需聯上 sql 才可  啟始
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();
		$this->arry2 = new array2html();

		if (!$sql) {
			$this->msg->add("Error ! cannot connect to database 。");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func



	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->extra_perm_str($perm_def)	權限字串 解譯   $perm_def 為 cvs 字串 [,]
	#					將PERM權限字串改成 ARRAY
	#					解譯  權限   傳回 :$ext_arry   [**** 處理權限字串非真的權限設定 ]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function extra_perm_str($perm_def) {

			$ext_arry =array();

			$perm_arry = $this->arry2->decode_array($perm_def);
			$A_Item = $GLOBALS['A_Item'];   // 取自config.admin內之menu項
					 
					 $k = 0;
			  for ($i=1;$i<=count($A_Item);$i++)	{
				  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {
						$ext_arry[$i][$j] = $perm_arry[$k];
//	echo "<br>ext_arry[$i][$j] ===>".$perm_arry[$k];
					$k = $k +1 ;
				  }
			  }

			return $ext_arry;
	} // end func
	
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->decode_perm_str($admin_data)	授權陣列碼 解譯
	#					陣列碼解議     傳回陣列 :$decode
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_perm_str($admin_data) {

//		$admin_data = $_SESSION['SCACHE']['ADMIN']['perm_dec'];

		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
		$A_Item = $GLOBALS['A_Item'];   // 取自config.admin內之menu項

		$decoded = array();

			  for ($i=1;$i<=count($A_Item);$i++)	{
				  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {

					foreach ($perm_def AS  $key=>$val  ) {
						if ($admin_data[$i][$j] & $val) {
								$decoded[$key] = 1;
						} 	else {
								$decoded[$key] = 0;
						}
						$decoded[$i][$j][$key] = $decoded[$key];
//echo "<br>decoded[$i][$j][$key] ===>".$decoded[$i][$j][$key];
					}

				  }
			  }
//exit;		
		
		return $decoded;
	} // end func
	
	
	
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->decode_perm($i,$j)	授權碼解譯
	#					解譯  權限   傳回 :$decode
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_perm($i,$j) {

		$admin_data = $_SESSION['SCACHE']['ADMIN']['perm_dec'];

		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
		$decoded = array();

		foreach ($perm_def AS  $key=>$val  ) {
		    if ($admin_data[$i][$j] & $val) {
					$decoded[$key] = 1;
		    } 	else {
					$decoded[$key] = 0;
			}
		}
		return $decoded;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->full_perm	最高授權碼解譯
	#					解譯  所有的權限   傳回 :$decode
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function full_perm() {

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++原程式碼
//		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
//		$decoded = array();
//		foreach ($perm_def AS  $key=>$val  ) {
//					$decoded[$key] = 1;
//		}
//		return $decoded;

	$A_Item = $GLOBALS['A_Item'];   // 取自config.admin內之menu項
		 
		 // 將POWER皆設為15  
	  for ($i=1;$i<=count($A_Item);$i++)	{
		  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {
			$decoded[$i][$j]=15;
		  }
	  }

//  暫時將 log 管理 / 尺吋規格 disable.............................................

	    $decoded[3][3]=0;
	    $decoded[3][4]=0;
//	    $decoded[3][5]=0;


//		$decoded[4][1]=0;
	    $decoded[4][2]=0;
	    $decoded[5][1]=0;
	    $decoded[5][2]=0;

//	    $decoded[8][1]=0;
	    $decoded[8][2]=0;

	    $decoded[10][5]=0;
//	    $decoded[10][7]=0;

		return $decoded;
	} // end func

	
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->encode_perm($perm_ary)		 授權碼 加碼
	#					傳回 $encoded
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function encode_perm($perm_ary) {
			$count = count($perm_ary);

		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];

		$encoded = 0;

	  for ($i=0;$i=$count-1;$i++){
//echo "<br>perm_ary[$i] ===>".$perm_ary['$i'];
		foreach ($perm_def AS  $key=>$val  ) {

			if ($perm_ary[$i] = $key) {		$encoded = $encoded | $val;     }
		}
	  }
		return $encoded;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->encode_perm_str($perm_array)		 授權碼 加碼後將陣列組成字串
	#					傳回 $encoded_str [cvs string]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function encode_perm_str($perm_array) {

		$encoded_str = "";
		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
		$encoded = 0;

		$encode = array();
		$A_Item = $GLOBALS['A_Item'];   // 取自config.admin內之menu項
		  for ($i=1;$i<=count($A_Item);$i++)	{
			if(!empty($perm_array[$i])){
			  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {
					$encoded = 0;

				if(!empty($perm_array[$i][$j])){

					while(list($key,$value) = each($perm_array[$i][$j])	){
						if($key=="'view'"){ 
							$encoded = $encoded | $GLOBALS['CFG']['ADMIN_PERM_DEF']['view']; }  
						if($key=="'add'"){ 
							$encoded = $encoded | $GLOBALS['CFG']['ADMIN_PERM_DEF']['add']; }  
						if($key=="'edit'"){ 
							$encoded = $encoded | $GLOBALS['CFG']['ADMIN_PERM_DEF']['edit']; }  
						if($key=="'del'"){ 
							$encoded = $encoded | $GLOBALS['CFG']['ADMIN_PERM_DEF']['del']; }  
					
					}//while

				} // if
					$encoded_str = $encoded_str.$encoded.",";

			  }  //for
			} else {
				  //  當沒有任何權限的 job時 也該補入零值
				  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {
						$encoded = 0;
						$encoded_str = $encoded_str.$encoded.",";

				  }  //for
				  
			}//if
		  } // for
				//	echo "<br>encoded_str ===>".$encoded_str;
			// 去除最後的一 個痘號
			$encoded_str = substr($encoded_str,0,-1);
			//	echo "<br>encoded_str ===>".$encoded_str;
					
		return $encoded_str;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->login($login_id,$login_pass)		 登入
	#					必需聯上 sql 才可  啟始
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function login($login_id,$login_pass) {

					$sql = $this->sql;
					$login_id = trim($login_id);
		if (!$login_id) {
			$this->msg->add("Error !! 請輸入 ID !");
			return false;
		}
			### 2003/01/26 加入判定是否為 超級管理員 ###
			if (is_manager_login($login_id,$login_pass)) {   // from function.php
				#### 2003/01/26  建構超級管理員身份 ####
				
				$admin_data['perm_dec'] = $this->full_perm();
				$admin_data['login_id'] = $login_id;
				$admin_data['name']		= $login_id;
				$admin_data['team_id']	= "SA";
				$admin_data['dept']		= "SA";
				$admin_data['id']		= "SA";

							# 寫進SESSION
				$_SESSION['SCACHE']['ADMIN'] = $admin_data;
					return true;
			} 

		$admin_data = $this->admin_get(0,$login_id);
		if (!$admin_data) {
			$this->msg->add("Error !! USER is not exist !");
			return false;
		}
		if ($admin_data['login_pass'] != $login_pass) {
			$this->msg->add("Error !! wrong PASSWORD !");
			return false;		    
		}

				# 寫進SESSION
		$_SESSION['SCACHE']['ADMIN'] = $admin_data;
				# 如果被停權........
		if ($admin_data['active'] == "N") {
			$this->msg->add("SORRY ! this account had been grounded, please contact your supervisor or system administrator! ");
			unset($_SESSION['SCACHE']['ADMIN']);
			return false;		    
		}
		
		return true;

	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->logout()			登出
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function logout() {

//			unset($GLOBALS['SCACHE']['ADMIN']);
			unset($_SESSION['SCACHE']['ADMIN']);
		return true;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->is_power($i, $j, $job="view") 由傳入的 值 判定是否有這個權限 
	#										傳回 true, false.........
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function is_power($i, $j, $job="view") {

		
		$allow = $this->decode_perm($i,$j); 

		if ($this->is_perm($i,$j) && $allow[$job]){
			return true;
		}

		return false;
	} // end func


	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->is_perm($i,$j)	 檢查授權狀況 檢查可否有進入的權
	#								增刪改的權 在程式內控制
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function is_perm($i,$j) {
		
		$admin_data = $_SESSION['SCACHE']['ADMIN']['perm_dec'];
		if (!is_array($admin_data)) {
			return false;
		}
		if (!$admin_data[$i][$j]) {
			return false;
		}

		return true;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->admin_add($parm)		加入user 記錄
	#					加入user 記錄			傳回 $admin_id
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_add($parm) {

		$sql = $this->sql;
			############### 檢查輸入項目
					# login_id   代號
		if (!$parm['login_id'] = trim($parm['login_id'])) {
			$this->msg->add("Error ! please key-in User [ID]。");
		    return false;
		}
		$parm['login_id'] = strtolower($parm['login_id']);	// 全部改成小寫
		if (!preg_match('/^[0-9a-z]+$/',$parm['login_id'])) {
			   $this->msg->add("Error ! only figure [0-9] or english character [A-Z] are acceptable for log-in ID");
		    return false;		    
		}
							# 檢查是否有重覆
		$q_str = "SELECT id FROM user WHERE login_id='".$parm['login_id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! unable to contact database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) || is_manager($parm['login_id'])) {
			$this->msg->add("sorry ! this ID had been used, please change to another!");
			return false;    	    
		}
					# login_pass	密碼
		$parm['login_pass'] = trim($parm['login_pass']);
		if (!$parm['login_pass']) {
   			$this->msg->add("Error ! press key-in your password。");
		    return false;
		}
					# name			名字
		$parm['name'] = trim($parm['name']);
		if (!$parm['name'] || strlen($parm['name'])<3) {
			$this->msg->add("Error ! please keyin your name as regular length。");
		    return false;	    
		}

					# 權限
		if (is_array($parm['perm_dec'])) {
			$perm = $this->encode_perm($parm['perm_dec']);
		}	else {
		    $perm =0;
		}
	//	$perm = $this->encode_perm($parm['perm_dec']);
					# 加入資料庫
		$q_str = "INSERT INTO user (login_id,login_pass,name,phone,perm,cellphone,email,team_id) VALUES ('".$parm['login_id']."','".$parm['login_pass'] ."','". $parm['name'] ."','". $parm['phone']."', '$perm','".$parm['cellphone'].$parm['email']."','".$parm['team_id']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("database save Error! cannot append new supervisor !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$admin_id = $sql->insert_id();
		return $admin_id;
	} // end func
	
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->change_pass($parm)		更新 密碼
	#					必需聯上 sql 才可  啟始   //  
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_pass($parm) {

		$sql = $this->sql;
		################## 檢查輸入
						# id
		if (!$parm['id'] = trim($parm['id'])) {
			$this->msg->add("Error ! please enter your ID。");
//		    return false;
		}
		if (!$parm['pass'] = trim($parm['pass'])) {
			$this->msg->add("Error ! please enter your old password。");
//		    return false;
		}
		if (!$parm['new1'] || !$parm['new2'] || $parm['new1'] != $parm['new2']) {
			$this->msg->add("Error ! mistype your new password。");
//			return false;
		}

		if (count($this->msg->get(2))){
			return false;
		}
			$parm['new'] = $parm['new1'];

		##### 檢查是否 有在資料庫內
		$q_str = "SELECT login_pass FROM user WHERE login_id='".$parm['id']."'";
		if (!$result = $sql->query($q_str)) {
			$this->msg->add("Error ! unable to contact database! try it later !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if (!$row = $sql->fetch($result)) {
			$this->msg->add("sorry ! this ID cannot be found from database, please correct it!");
			return false;    
		}

		if ($row[0] != $parm['pass']) {
			$this->msg->add("sorry ! the old pass word is not correct !, try again !");
			return false;    
		}
				
		#####   更新資料庫內容
		$q_str = "UPDATE user SET login_pass='".$parm['new']."' WHERE login_id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update User data from database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->admin_update($mode,$parm)		更新 管理員資料(mode=0,1,2)
	#					必需聯上 sql 才可  啟始   //  
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_update($mode,$parm) {

		$sql = $this->sql;
		################## 檢查輸入
			##### id
		if (!$parm['id'] = trim($parm['id'])) {
			$this->msg->add("Error ! please enter your ID。");
		    return false;
		}
		if ($mode==0) {
			##### login_pass
			$parm['login_pass'] = trim($parm['login_pass']);
			if (!$parm['login_pass']) {
				$this->msg->add("Error ! please key-in your password。");
				return false;
			}
		}
		if ($mode==2) {
			$parm['login_pass1'] = trim($parm['login_pass1']);
			$parm['login_pass2'] = trim($parm['login_pass2']);
			if (!$parm['login_pass1'] || !$parm['login_pass2'] || $parm['login_pass1'] != $parm['login_pass2']) {
				$this->msg->add("Error ! mistype your password。");
				return false;
			}
			$parm['login_pass'] = $parm['login_pass1'];
		}
		if ($mode==0) {
						# 連絡電話
//			if (!$parm['tel'] = trim($parm['tel'])) {
//				$this->msg->add("Error ! mistype contact phone。");
//				return false;	    
//			}
				##### 權限
			if (is_array($parm['perm_dec'])) {
				$perm = $this->encode_perm($parm['perm_dec']);
			}	else {
				$perm =0;
			}
		}
				#####   更新資料庫內容
		if ($mode==0) {
			$q_str = "UPDATE user SET login_pass='".$parm['login_pass']."' , cellphone='". $parm['cellphone']."' , phone='". $parm['phone']."' , team_id='". $parm['team_id'] ."' , email='". $parm['email']. "', perm='$perm' WHERE id='".$parm['id']."'";
		}	elseif ($mode==1) {
			$enable_val = $GLOBALS['CFG']['ADMIN_PERM_DEF']['enable'];
			$q_str = "UPDATE user SET perm=perm & ~$enable_val WHERE id='".$parm['id']."'";   
		}	elseif ($mode==2) {
			$q_str = "UPDATE user SET login_pass='".$parm['login_pass']."' WHERE id='".$parm['id']."'";
		}	else {
			$this->msg->add("Error ! User data update mode error!!");
		    return false;
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update User data from database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func


	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->admin_get($id=0,$login_id=0)	取出user記錄
	#									//   傳回 $row[perm_dec]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_get($id=0,$login_id=0) {

		$sql = $this->sql;
//		$login_id = trim(strtolower($login_id));        //   改成小寫
		$login_id = trim($login_id);       
		if (!$id && !$login_id) {
			$this->msg->add("Error ! 請輸入 ID。");		    
			return false;
		}
		if ($id) {
		    $q_str = "SELECT * FROM user WHERE id='$id' ";
		}	elseif ($login_id) {
		    $q_str = "SELECT * FROM user WHERE login_id='$login_id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法聯結資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到該筆資料記錄 !");
			return false;    
		}

		$row['perm_dec'] = $this->extra_perm_str($row['perm']);
		return $row;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->admin_browse($mode=0)		瀏覽管理員列表
	#					return $admin_datas
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_browse($mode=0) {
		$sql= $this->sql;
		$q_str = "SELECT * FROM user";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! unable to contact database !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$admin_datas = array();
		while ($row = $sql->fetch($q_result)) {
			$row['perm_dec'] = $this->decode_perm($row['perm']);
			$admin_datas[] = $row;
		}
		return $admin_datas;
	} // end func


	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->admin_log_search($mode=0)	搜尋 管理員日誌 [引入 search class]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_log_search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;		// 取回所有變數 包括post 或 get
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM log";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("date DESC");
		$srh->row_per_page = 30;
	
		if ($argv['PHP_edit_flag']) {
			$srh->add_cgi_parm("PHP_edit_flag",1);
		}
//2006.11.14 以數字型式顯示頁碼 star		
		$srh->row_per_page = 20;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 以數字型式顯示頁碼 end    	
		
		if ($mode =="id" )  { 	
			$str = $argv['PHP_sr_parm_user'];
			$srh->add_cgi_parm("PHP_sr_parm_user",$str);
			$srh->add_cgi_parm("PHP_srch_by",'id');
			$srh->add_where_condition("login_id LIKE '%$str%'", "PHP_sr_parm_user",$str," 帳號ID含有:[ $str ] 的");
		}
		if ($mode =="date" )  { 	
			$str = $argv['PHP_sr_parm_date'];
			$srh->add_cgi_parm("PHP_sr_parm_date",$str);
			$srh->add_cgi_parm("PHP_srch_by",'date');
			$sr_date = substr($str,0,4)."/".substr($str,4,2)."/".substr($str,6,2);
			$srh->add_where_condition("date <= '$str'", "PHP_sr_parm_date",$str," 日期為[  $sr_date ] 之前的");
		}
		if ($mode =="code" )  { 	
			$str = $argv['PHP_sr_parm_code'];
			$srh->add_cgi_parm("PHP_sr_parm_code",$str);
			$srh->add_cgi_parm("PHP_srch_by",'code');
			$srh->add_where_condition("job  LIKE '%$str%'", "PHP_sr_parm_code",$str," 工作代號含有:[ $str ] 的");
		}
		if ($mode =="all" )  { 	
			$str = $argv['PHP_sr_parm_all'];
			$srh->add_cgi_parm("PHP_sr_parm_all",$str);
			$srh->add_cgi_parm("PHP_srch_by",'all');
			$srh->add_where_condition("1>0", "PHP_sr_parm_all",$str," 全部的");
		}

		$result= $srh->send_query2();

//DEBUG:
//print "<P>[DEBUG] q_str of srh: ".$srh->q_str;

		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
			##### 把 log 解碼      // 在此不需解開
//		foreach ($result AS  $key=>$val  ) {
//			$result[$key] = $this->decode_customer_data($val);
//		}
		$this->msg->merge($srh->msg);
		$op['logs'] = $result;
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;

#####2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14新頁碼需要的oup_put	end

		return $op;
	} // end func






} // end class


?>