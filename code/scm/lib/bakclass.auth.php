<?php 

	######################### Auth  class ##### �޲z�� ###########################
	#	auth->init($sql)				�ҩl (�ϥ� Msg_handle(); ���p�W sql)
	#	auth->extra_perm_str($perm_def)		�v���r�� ��Ķ   $perm_def �� �r�� [,]
	#	auth->decode_perm_str($admin_data)	���v�}�C�X ��Ķ	�Ǧ^�}�C :$decode
	#	auth->decode_perm($i,$j)			���v�X��Ķ		�Ǧ^ :$decode
	#	auth->full_perm							�̰����v�X��Ķ	�Ǧ^ :$decode
	#	auth->encode_perm($perm_ary)			���v�X �[�X
	#	auth->encode_perm_str($perm_array)		���v�X �[�X��N�}�C�զ��r��
	#	auth->login($login_id,$login_pass)		�n�J
	#	auth->logout()							�n�X
	#	auth->is_power($i, $j, $job="view")		�ѶǤJ�� �� �P�w�O�_���o���v�� 
	#	auth->is_perm($i,$j)	 �ˬd���v���p �ˬd�i�_���i�J���v     ??????
	#	auth->admin_add($parm)					�[�Juser �O�� �Ǧ^ $admin_id
	#	auth->change_pass($parm)		��s �K�X
	#	auth->admin_update($mode,$parm)		��s �޲z�����(mode=0,1,2)
	#	auth->admin_get($id=0,$login_id=0)	���Xuser�O��
	#	auth->admin_browse($mode=0)			�s���޲z���C��
	#
	#	auth->admin_log_search($mode=0)		�j�M �޲z����x [�ޤJ search class]
	#
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class AUTH {
		
	var $sql;
	var $msg ;
	
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->init($sql)	�ҩl(�ϥ� Msg_handle()  ; ���p�W sql)
	#					�����p�W sql �~�i  �ҩl
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();
		$this->arry2 = new array2html();

		if (!$sql) {
			$this->msg->add("Error ! cannot connect to database �C");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func



	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->extra_perm_str($perm_def)	�v���r�� ��Ķ   $perm_def �� cvs �r�� [,]
	#					�NPERM�v���r��令 ARRAY
	#					��Ķ  �v��   �Ǧ^ :$ext_arry   [**** �B�z�v���r��D�u���v���]�w ]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function extra_perm_str($perm_def) {

			$ext_arry =array();

			$perm_arry = $this->arry2->decode_array($perm_def);
			$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��
					 
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
	#	auth->decode_perm_str($admin_data)	���v�}�C�X ��Ķ
	#					�}�C�X��ĳ     �Ǧ^�}�C :$decode
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_perm_str($admin_data) {

//		$admin_data = $_SESSION['SCACHE']['ADMIN']['perm_dec'];

		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
		$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��

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
	#	auth->decode_perm($i,$j)	���v�X��Ķ
	#					��Ķ  �v��   �Ǧ^ :$decode
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
	#	auth->full_perm	�̰����v�X��Ķ
	#					��Ķ  �Ҧ����v��   �Ǧ^ :$decode
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function full_perm() {

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++��{���X
//		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
//		$decoded = array();
//		foreach ($perm_def AS  $key=>$val  ) {
//					$decoded[$key] = 1;
//		}
//		return $decoded;

	$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��
		 
		 // �NPOWER�ҳ]��15  
	  for ($i=1;$i<=count($A_Item);$i++)	{
		  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {
			$decoded[$i][$j]=15;
		  }
	  }

//  �ȮɱN log �޲z / �ئT�W�� disable.............................................

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
	#	auth->encode_perm($perm_ary)		 ���v�X �[�X
	#					�Ǧ^ $encoded
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
	#	auth->encode_perm_str($perm_array)		 ���v�X �[�X��N�}�C�զ��r��
	#					�Ǧ^ $encoded_str [cvs string]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function encode_perm_str($perm_array) {

		$encoded_str = "";
		$perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
		$encoded = 0;

		$encode = array();
		$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��
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
				  //  ��S�������v���� job�� �]�ӸɤJ�s��
				  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {
						$encoded = 0;
						$encoded_str = $encoded_str.$encoded.",";

				  }  //for
				  
			}//if
		  } // for
				//	echo "<br>encoded_str ===>".$encoded_str;
			// �h���̫᪺�@ �ӵk��
			$encoded_str = substr($encoded_str,0,-1);
			//	echo "<br>encoded_str ===>".$encoded_str;
					
		return $encoded_str;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->login($login_id,$login_pass)		 �n�J
	#					�����p�W sql �~�i  �ҩl
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function login($login_id,$login_pass) {

					$sql = $this->sql;
					$login_id = trim($login_id);
		if (!$login_id) {
			$this->msg->add("Error !! �п�J ID !");
			return false;
		}
			### 2003/01/26 �[�J�P�w�O�_�� �W�ź޲z�� ###
			if (is_manager_login($login_id,$login_pass)) {   // from function.php
				#### 2003/01/26  �غc�W�ź޲z������ ####
				
				$admin_data['perm_dec'] = $this->full_perm();
				$admin_data['login_id'] = $login_id;
				$admin_data['name']		= $login_id;
				$admin_data['team_id']	= "SA";
				$admin_data['dept']		= "SA";
				$admin_data['id']		= "SA";

							# �g�iSESSION
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

				# �g�iSESSION
		$_SESSION['SCACHE']['ADMIN'] = $admin_data;
				# �p�G�Q���v........
		if ($admin_data['active'] == "N") {
			$this->msg->add("SORRY ! this account had been grounded, please contact your supervisor or system administrator! ");
			unset($_SESSION['SCACHE']['ADMIN']);
			return false;		    
		}
		
		return true;

	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->logout()			�n�X
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function logout() {

//			unset($GLOBALS['SCACHE']['ADMIN']);
			unset($_SESSION['SCACHE']['ADMIN']);
		return true;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->is_power($i, $j, $job="view") �ѶǤJ�� �� �P�w�O�_���o���v�� 
	#										�Ǧ^ true, false.........
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function is_power($i, $j, $job="view") {

		
		$allow = $this->decode_perm($i,$j); 

		if ($this->is_perm($i,$j) && $allow[$job]){
			return true;
		}

		return false;
	} // end func


	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->is_perm($i,$j)	 �ˬd���v���p �ˬd�i�_���i�J���v
	#								�W�R�諸�v �b�{��������
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
	#	auth->admin_add($parm)		�[�Juser �O��
	#					�[�Juser �O��			�Ǧ^ $admin_id
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_add($parm) {

		$sql = $this->sql;
			############### �ˬd��J����
					# login_id   �N��
		if (!$parm['login_id'] = trim($parm['login_id'])) {
			$this->msg->add("Error ! please key-in User [ID]�C");
		    return false;
		}
		$parm['login_id'] = strtolower($parm['login_id']);	// �����令�p�g
		if (!preg_match('/^[0-9a-z]+$/',$parm['login_id'])) {
			   $this->msg->add("Error ! only figure [0-9] or english character [A-Z] are acceptable for log-in ID");
		    return false;		    
		}
							# �ˬd�O�_������
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
					# login_pass	�K�X
		$parm['login_pass'] = trim($parm['login_pass']);
		if (!$parm['login_pass']) {
   			$this->msg->add("Error ! press key-in your password�C");
		    return false;
		}
					# name			�W�r
		$parm['name'] = trim($parm['name']);
		if (!$parm['name'] || strlen($parm['name'])<3) {
			$this->msg->add("Error ! please keyin your name as regular length�C");
		    return false;	    
		}

					# �v��
		if (is_array($parm['perm_dec'])) {
			$perm = $this->encode_perm($parm['perm_dec']);
		}	else {
		    $perm =0;
		}
	//	$perm = $this->encode_perm($parm['perm_dec']);
					# �[�J��Ʈw
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
	#	auth->change_pass($parm)		��s �K�X
	#					�����p�W sql �~�i  �ҩl   //  
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_pass($parm) {

		$sql = $this->sql;
		################## �ˬd��J
						# id
		if (!$parm['id'] = trim($parm['id'])) {
			$this->msg->add("Error ! please enter your ID�C");
//		    return false;
		}
		if (!$parm['pass'] = trim($parm['pass'])) {
			$this->msg->add("Error ! please enter your old password�C");
//		    return false;
		}
		if (!$parm['new1'] || !$parm['new2'] || $parm['new1'] != $parm['new2']) {
			$this->msg->add("Error ! mistype your new password�C");
//			return false;
		}

		if (count($this->msg->get(2))){
			return false;
		}
			$parm['new'] = $parm['new1'];

		##### �ˬd�O�_ ���b��Ʈw��
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
				
		#####   ��s��Ʈw���e
		$q_str = "UPDATE user SET login_pass='".$parm['new']."' WHERE login_id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update User data from database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->admin_update($mode,$parm)		��s �޲z�����(mode=0,1,2)
	#					�����p�W sql �~�i  �ҩl   //  
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_update($mode,$parm) {

		$sql = $this->sql;
		################## �ˬd��J
			##### id
		if (!$parm['id'] = trim($parm['id'])) {
			$this->msg->add("Error ! please enter your ID�C");
		    return false;
		}
		if ($mode==0) {
			##### login_pass
			$parm['login_pass'] = trim($parm['login_pass']);
			if (!$parm['login_pass']) {
				$this->msg->add("Error ! please key-in your password�C");
				return false;
			}
		}
		if ($mode==2) {
			$parm['login_pass1'] = trim($parm['login_pass1']);
			$parm['login_pass2'] = trim($parm['login_pass2']);
			if (!$parm['login_pass1'] || !$parm['login_pass2'] || $parm['login_pass1'] != $parm['login_pass2']) {
				$this->msg->add("Error ! mistype your password�C");
				return false;
			}
			$parm['login_pass'] = $parm['login_pass1'];
		}
		if ($mode==0) {
						# �s���q��
//			if (!$parm['tel'] = trim($parm['tel'])) {
//				$this->msg->add("Error ! mistype contact phone�C");
//				return false;	    
//			}
				##### �v��
			if (is_array($parm['perm_dec'])) {
				$perm = $this->encode_perm($parm['perm_dec']);
			}	else {
				$perm =0;
			}
		}
				#####   ��s��Ʈw���e
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
	#	auth->admin_get($id=0,$login_id=0)	���Xuser�O��
	#									//   �Ǧ^ $row[perm_dec]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_get($id=0,$login_id=0) {

		$sql = $this->sql;
//		$login_id = trim(strtolower($login_id));        //   �令�p�g
		$login_id = trim($login_id);       
		if (!$id && !$login_id) {
			$this->msg->add("Error ! �п�J ID�C");		    
			return false;
		}
		if ($id) {
		    $q_str = "SELECT * FROM user WHERE id='$id' ";
		}	elseif ($login_id) {
		    $q_str = "SELECT * FROM user WHERE login_id='$login_id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�p����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! �L�k���ӵ���ưO�� !");
			return false;    
		}

		$row['perm_dec'] = $this->extra_perm_str($row['perm']);
		return $row;
	} // end func

	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	auth->admin_browse($mode=0)		�s���޲z���C��
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
	#	auth->admin_log_search($mode=0)	�j�M �޲z����x [�ޤJ search class]
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function admin_log_search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;		// ���^�Ҧ��ܼ� �]�Apost �� get
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
//2006.11.14 �H�Ʀr������ܭ��X star		
		$srh->row_per_page = 20;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 �H�Ʀr������ܭ��X end    	
		
		if ($mode =="id" )  { 	
			$str = $argv['PHP_sr_parm_user'];
			$srh->add_cgi_parm("PHP_sr_parm_user",$str);
			$srh->add_cgi_parm("PHP_srch_by",'id');
			$srh->add_where_condition("login_id LIKE '%$str%'", "PHP_sr_parm_user",$str," �b��ID�t��:[ $str ] ��");
		}
		if ($mode =="date" )  { 	
			$str = $argv['PHP_sr_parm_date'];
			$srh->add_cgi_parm("PHP_sr_parm_date",$str);
			$srh->add_cgi_parm("PHP_srch_by",'date');
			$sr_date = substr($str,0,4)."/".substr($str,4,2)."/".substr($str,6,2);
			$srh->add_where_condition("date <= '$str'", "PHP_sr_parm_date",$str," �����[  $sr_date ] ���e��");
		}
		if ($mode =="code" )  { 	
			$str = $argv['PHP_sr_parm_code'];
			$srh->add_cgi_parm("PHP_sr_parm_code",$str);
			$srh->add_cgi_parm("PHP_srch_by",'code');
			$srh->add_where_condition("job  LIKE '%$str%'", "PHP_sr_parm_code",$str," �u�@�N���t��:[ $str ] ��");
		}
		if ($mode =="all" )  { 	
			$str = $argv['PHP_sr_parm_all'];
			$srh->add_cgi_parm("PHP_sr_parm_all",$str);
			$srh->add_cgi_parm("PHP_srch_by",'all');
			$srh->add_where_condition("1>0", "PHP_sr_parm_all",$str," ������");
		}

		$result= $srh->send_query2();

//DEBUG:
//print "<P>[DEBUG] q_str of srh: ".$srh->q_str;

		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
			##### �� log �ѽX      // �b�����ݸѶ}
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

#####2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14�s���X�ݭn��oup_put	end

		return $op;
	} // end func






} // end class


?>