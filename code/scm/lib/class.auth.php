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




/*----------
# �\�໡�� : �T�{�ϥΪ�id,pw�T�{�ᵹ���v��
# �ɤJ�ܼ� : 
# ��J�ܼ� : $login_id,$login_pass
# ��X�ܼ� : true �g�i SESSION or false unset
# ���g��� : 2007/09/13
# �Ƶ����� : 
----------*/
function login($login_id,$login_pass) {

	global $MySQL,$Log,$MYSQL_DB;

	$login_id = trim($login_id);
  
	if (empty($login_id)) {
  
		$_SESSION['msg'][] = ("Error !! please input your ID ! ~ ~ ~ ".$login_id.','.$login_pass);
	
		return false;
	
	}

	# �P�w�O�_�� �W�ź޲z��
	if (is_manager_login($login_id,$login_pass)  ) {
	
		$admin_data['perm_dec'] 	= $this->full_perm();
		
		$admin_data['login_id'] 	= $login_id;
		
		$admin_data['name'] 		= $_SESSION['ADMIN'][$login_id]['name'];
		
		$admin_data['login_pass']	= $login_pass;
		
		$admin_data['team_id']		= $admin_data['dept']	= $admin_data['id']	= "SA";
		
		$admin_data['email']		= $_SESSION['ADMIN'][$login_id]['email'];
		$admin_data['id']		    = '00';
		
		# �g�iSESSION
		$_SESSION['USER']['ADMIN'] = $admin_data;
		
		$GLOBALS['SCACHE']['ADMIN'] = $admin_data;
		
		# �����v������~ �θ�Ʈw�W�٧@������
		$_SESSION[$MYSQL_DB] = $admin_data;
		
		return true;
		
	} else {
  
		# �@��ϥΪ�
	
		$admin_data = $this->admin_get(0,$login_id);
	
		if (!$admin_data) {
	
			$_SESSION['msg'][] = ("Error !! USER is not exist !");
	  
			return false;
	  
		}
	
		if ($admin_data['login_pass'] != $login_pass) {
	
			$_SESSION['msg'][] = ("Error !! wrong PASSWORD !");
	  
			return false;
	  
		}
        // print_r($admin_data);
        if($admin_data['dept'] == 'SA')
        $admin_data['perm_dec'] = $this->full_perm();

		# �g�iSESSION
		$_SESSION['USER']['ADMIN'] = $admin_data;
	
		$GLOBALS['SCACHE']['ADMIN'] = $admin_data;
		
		# �����v������~ �θ�Ʈw�W�٧@������
		$_SESSION[$MYSQL_DB] = $admin_data;
	
		# �p�G�Q���v........
	
		if ($admin_data['active'] == "N") {
	
			$_SESSION['msg'][] = ("SORRY ! this account had been grounded, please contact your supervisor or system administrator! ");
	  
			session_unset();
	  
			return false;
	  
		}
	
		return true;

	}
  
} // end func



/*----------
# �\�໡�� : ���ͺ޲z�̪��v��
# �ɤJ�ܼ� : 	
# ��J�ܼ� : 
# ��X�ܼ� : 
# ���g��� : 2007/09/13
# �Ƶ����� : 
----------*/

function full_perm() {

	foreach($_SESSION['ITEM']['A_Item'] as $keys => $vales){
	
		foreach($vales as $key => $val){
		
			if($key!=0){
			
				$decoded[$val[0][5]] = $_SESSION['full_key'];
				
			}
			
		}
		
	}
	
	return $decoded;
  
} // end func



/*----------
# �\�໡�� : ���menu���ةM�ϥΪ̪��ϥ��v��
# �ɤJ�ܼ� : check_authority,
# ��J�ܼ� : $auth,$job
# ��X�ܼ� : 
# ���g��� : 2007/09/13
# �Ƶ����� : 
----------*/

function is_power($auth,$job) {

	$allow = $this->decode_perm($auth);

	if ($allow[$job] && true){

		return true;

	}

	return false;
} // end func



/*----------
# �\�໡�� : �P�_�����Ǧ^���wmenu�N�X���������s���v��
# �ɤJ�ܼ� : is_power
# ��J�ܼ� : $auth '032'
# ��X�ܼ� : [view] => 1 [add] => 1 [edit] => 1 [del] => 1 [confirm] => 1 [approval] => 1
# ���g��� : 2007/09/13
# �Ƶ����� : �O��1�_��0
----------*/

function decode_perm($auth) {

	if ( !empty($_SESSION['SCACHE']['ADMIN']['perm_dec']) )
		$admin_data = $_SESSION['SCACHE']['ADMIN']['perm_dec'];

	// print_r($admin_data);
	foreach ($_SESSION['ITEM']['ADMIN_PERM_DEF'] AS  $key => $val  ) {

		if (@$admin_data[$auth] & $val) {

			$decoded[$key] = 1;

		} else {

			$decoded[$key] = 0;

		}

	}

  return $decoded;
  
} // end func



/*----------
# �\�໡�� : ��Ķ�ϥΪ��v��[perm_dec]
# �ɤJ�ܼ� : extra_perm_str
# ��J�ܼ� : [001]=> 7
# ��X�ܼ� : [001] => Array ( [view] => 1 [add] => 1 [edit] => 1 [del] => 0 [confirm] => 0 [approval] => 0 )
# ���g��� : 2007/09/13
# �Ƶ����� : 
----------*/

function decode_perm_str($admin_data) {

	$decoded = array();

	$perm_def = $_SESSION['ITEM']['ADMIN_PERM_DEF'];
	
	$A_Item = $_SESSION['ITEM']['A_Item'];

	foreach ($A_Item AS $keys => $vales ){
	
		foreach ($vales AS $key => $maker ){
		
			if($key!=0){
			
				foreach ($perm_def AS  $key => $val  ) {
				
					( !empty($admin_data[$maker[0][5]]) && $admin_data[$maker[0][5]] & $val )? $dekey = 1 : $dekey = 0;
					// echo $maker[0][1] . ' ~ ' . ($admin_data[$maker[0][5]] & $val) . ' = ' . $admin_data[$maker[0][5]].' & '.$val . '<br>';
					$decoded[$maker[0][5]][$key] = $dekey;
					
				}
				
			}
			
		}
		
	}
	
	return $decoded;
	
} // end func


/*----------
# �\�໡�� : ��Ķ team �v��[perm_def]
# �ɤJ�ܼ� : 
# ��J�ܼ� : 001-7,002-7
# ��X�ܼ� : [001] => 7 [002] => 7
# ���g��� : 2007/09/13
# �Ƶ����� : 
----------*/
function extra_perm_str($perm_def) {

	$this->arry2 = new array2html();

	if($perm_def){
	
		$A_Item = $_SESSION['ITEM']['A_Item'];
		
		$perm_arry = $this->arry2->decode_array($perm_def);
		
		foreach ($perm_arry AS $key => $val ){
		
			$team[$key] = explode("-",$val);
		  
		}
		
		foreach ($A_Item AS $keys => $vales ){
		
			foreach ($vales AS $key => $val ){
		  
				for($d=0;$d < count($team);$d++){
			
					if ( !empty($A_Item[$keys][$key][0][5]) && !empty($team[$d][0]) && $A_Item[$keys][$key][0][5] === $team[$d][0] ){

						$ext_arry[$val[0][5]] = $team[$d][1];

					}

				}

			}

		}

		return $ext_arry;

	}

} // end func



/*----------
# �\�໡�� : �sĶ��N�}�C�զ��r��
# �ɤJ�ܼ� : 
# ��J�ܼ� : [001] => Array ( [view] => 1 [add] => 1 [edit] => 1
# ��X�ܼ� : 001-7,002-7
# ���g��� : 2007/09/14
# �Ƶ����� : 
----------*/

function encode_perm_str($perm_array) {

	$encoded_str = '';

	$A_Item = $_SESSION['ITEM']['A_Item'];

	$PERM_DEF = $_SESSION['ITEM']['ADMIN_PERM_DEF'];

	foreach ($perm_array AS $keys => $vales ){

		$encoded = 0;

		foreach ($vales AS $key => $val){

			$m_code = $keys.'-';

			$encoded = $encoded | $PERM_DEF[$key];
			// echo $encoded.' = '.$encoded.' | '.$PERM_DEF[$key].'<br>';

		}
		// echo $encoded.'<br>';
		$encoded_str = $encoded_str.$m_code.$encoded.',';

	}

	$encoded_str = substr($encoded_str,0,-1);

	return $encoded_str;

} // end func




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
	#	auth->decode_perm_str($admin_data)	���v�}�C�X ��Ķ
	#					�}�C�X��ĳ     �Ǧ^�}�C :$decode
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// function decode_perm_str($admin_data) {



		// $perm_def = $GLOBALS['CFG']['ADMIN_PERM_DEF'];
		// $A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��

		// $decoded = array();

			  // for ($i=1;$i<=count($A_Item);$i++)	{
				  // for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {

					// foreach ($perm_def AS  $key=>$val  ) {
						// if ($admin_data[$i][$j] & $val) {
								// $decoded[$key] = 1;
						// } 	else {
								// $decoded[$key] = 0;
						// }
						// $decoded[$i][$j][$key] = $decoded[$key];

					// }

				  // }
			  // }

		
		// return $decoded;
	// } // end func
	
	


	
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
	#	->login($login_id,$login_pass)		 �n�J
	#					�����p�W sql �~�i  �ҩl
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function dlogin($login_id,$login_pass) {

					$sql = $this->sql;
					$login_id = trim($login_id);
		if (!$login_id) {
			$this->msg->add("Error !! please input your ID !");
			return false;
		}
			### 2003/01/26 �[�J�P�w�O�_�� �W�ź޲z�� ###
			if (is_manager_login($login_id,$login_pass)) {   // from function.php
				#### 2003/01/26  �غc�W�ź޲z������ ####
				
				$admin_data['perm_dec'] = $this->full_perm();
				$admin_data['login_id'] = $login_id;
				$admin_data['name']		= $login_id;
				$admin_data['login_pass']	= $login_pass;
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

			unset($_SESSION['SCACHE']['ADMIN']);
		return true;
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
		$srh->row_per_page = 12;
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
			$srh->add_where_condition("login_id LIKE '%$str%'", "PHP_sr_parm_user",$str," Account ID above:[ $str ]");
		}
		if ($mode =="date" )  { 	
			$str = $argv['PHP_sr_parm_date'];
			$srh->add_cgi_parm("PHP_sr_parm_date",$str);
			$srh->add_cgi_parm("PHP_srch_by",'date');
			$sr_date = substr($str,0,4)."/".substr($str,4,2)."/".substr($str,6,2);
			$srh->add_where_condition("date <= '$str'", "PHP_sr_parm_date",$str," Before Date : [  $sr_date ] ");
		}
		if ($mode =="code" )  { 	
			$str = $argv['PHP_sr_parm_code'];
			$srh->add_cgi_parm("PHP_sr_parm_code",$str);
			$srh->add_cgi_parm("PHP_srch_by",'code');
			$srh->add_where_condition("job  LIKE '%$str%'", "PHP_sr_parm_code",$str," Work ID above:[ $str ] ");
		}
		if ($mode =="all" )  { 	
			$str = $argv['PHP_sr_parm_all'];
			$srh->add_cgi_parm("PHP_sr_parm_all",$str);
			$srh->add_cgi_parm("PHP_srch_by",'all');
			$srh->add_where_condition("1>0", "PHP_sr_parm_all",$str," ALL");
		}

		$result= $srh->send_query2();

//DEBUG:
//print "<P>[DEBUG] q_str of srh: ".$srh->q_str;

		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
			##### �� log �ѽX      // �b�����ݸѶ}
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	auth->workmsg($mode=0)		
#					return $workmsg
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function workmsg($mode) {
$sql= $this->sql;
/*
//�t�δ��ܰѼƨ��o
$para_cm = $GLOBALS['para']->get(0,'un-submit_etd');
$alt_value['un_sub'] = $para_cm['set_value'];

$para_cm = $GLOBALS['para']->get(0,'un-schedule_etd');
$alt_value['un_schld'] = $para_cm['set_value'];


$para_cm = $GLOBALS['para']->get(0,'una-production_etd');
$alt_value['una_prd'] = $para_cm['set_value'];
$para_cm = $GLOBALS['para']->get(0,'unfinish_etd');
$alt_value['un_fish'] = $para_cm['set_value'];
$para_cm = $GLOBALS['para']->get(0,'un-eta_etd');
$alt_value['un_eta'] = $para_cm['set_value'];
$para_cm = $GLOBALS['para']->get(0,'un-smplapv_etd');
$alt_value['un_smpl'] = $para_cm['set_value'];
$para_cm = $GLOBALS['para']->get(0,'no_patten');
$alt_value['no_pttn'] = $para_cm['set_value'];


$alt_value['un_sub'] = 75;
$alt_value['un_schld'] = 60;
$alt_value['una_prd'] = 30;
$alt_value['un_fish'] = 10;
$alt_value['un_eta'] = 65;
$alt_value['un_smpl'] = 60;
$alt_value['no_pttn'] = 2;
*/
//session_start();  
$alt_value = $_SESSION['alt_value'];

switch ($mode) {
		
//-------------------------------------------------------
case "order_rec":
$TODAY=date('Y-m-d');
$search_date=increceDaysInDate($TODAY,$alt_value['un_sub']);
$search_date1=increceDaysInDate($TODAY,-90);

if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA" || $GLOBALS['SCACHE']['ADMIN']['dept'] == "PM" || $GLOBALS['SCACHE']['ADMIN']['dept'] == "GM"){
    $creator = "AND `status` < '2' AND `status` >= '0' AND DEPT <> 'HJ'";
}else if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "RD"){
    $creator = "AND `status` < '1' AND `status` >= '0'";
}else if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "K0"){
    $creator = "AND `status` < '2' AND `status` >= '0' and `dept` like 'k%' ";
}else if($GLOBALS['SCACHE']['ADMIN']['dept'] == "J0"){
    $creator = "AND `status` < '2' AND `status` >= '0' and `dept` like 'J%' ";
//			$creator = "AND `status` < '2' AND `status` >= '0' and `dept` like 'k%' ";
}else if($GLOBALS['SCACHE']['ADMIN']['dept'] == "T0"){
    $creator = "AND `status` < '2' AND `status` >= '0' and `dept` like 'T%' ";
}else{
    $creator = "AND `status` < '2' AND `status` >= '0' and `dept` = '".$GLOBALS['SCACHE']['ADMIN']['dept']."' ";
}

$q_str = "SELECT `id` , `order_num` , `creator`, `qty` , `etd` , `etp` , `style_num` , `uprice`, status, factory, ref  FROM `s_order` WHERE  etd <= '$search_date' and etd >= '$search_date1'".$creator." order by ETD ";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;
}

$tmp[1]='';
$j=0;
$tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
$OrderRec = $p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $OrderRec[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $OrderRec[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $OrderRec);

for($j=0;$j<sizeof($OrderRec);$j++){
    if ($OrderRec[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$OrderRec[$j]['qty'];
    if ($OrderRec[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$OrderRec[$j]['qty'];
    if ($OrderRec[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$OrderRec[$j]['qty'];
    $OrderRec[$j]['rmk']=get_ord_status($OrderRec[$j]['status']);
    $tmp1=explode('-',$OrderRec[$j]['etd']);
    if ($tmp1[1] <> $tmp[1])
    {
        $OrderRec[$j]['bk']=1;
        $tmp=explode('-',$OrderRec[$j]['etd']);	
    }
}

if (!empty($OrderRec)){
    $OrderRec[0]['bk']=0;
    $OrderRec[0]['qty_hj']=$tmp_hj;
    $OrderRec[0]['qty_ly']=$tmp_ly;
    $OrderRec[0]['qty_cf']=$tmp_cf;
    
    $OrderRec['num'] = count($OrderRec);
    
    return $OrderRec;
}else{
    return false;
}

break;



//-------------------------------------------------------
case "ie_rec":
$TODAY=date('Y-m-d');
$search_date=increceDaysInDate($TODAY,$alt_value['un_sub']);
$search_date1=increceDaysInDate($TODAY,-90);

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $creator = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $creator = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $creator = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $creator .= ( $i > 0 ) ? ' OR ' : '' ;
            $creator .= " s_order.dept = '$dept_group[$i]' ";
        }
        $creator .= " ) ";
    }
}

$q_str = "SELECT `id` , `order_num` , `creator`, `qty` , `etd` , `etp` , `style_num` , `uprice`, status, factory, ref  FROM `s_order` WHERE  etd <= '$search_date' and etd >= '$search_date1' ".$creator."  AND `status` >= '0' and ie1 = 0 order by ETD ";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}
$tmp[1]='';
$j=0;
$tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
while ($row = $sql->fetch($q_result)) {
    $OrderRec[$j] = $row;
    if ($row['factory'] == 'HJ')$tmp_hj=$tmp_hj+$row['qty'];
    if ($row['factory'] == 'LY')$tmp_ly=$tmp_ly+$row['qty'];
    if ($row['factory'] == 'CF')$tmp_cf=$tmp_cf+$row['qty'];
    $OrderRec[$j]['rmk']=get_ord_status($row['status']);			
    $tmp1=explode('-',$row['etd']);
    if ($tmp1[1] <> $tmp[1])
    {
        $OrderRec[$j]['bk']=1;
        $tmp=explode('-',$row['etd']);	
    }
    $j++;

}

if (!empty($OrderRec))
{
    $OrderRec[0]['bk']=0;
    $OrderRec[0]['qty_hj']=$tmp_hj;
    $OrderRec[0]['qty_ly']=$tmp_ly;
    $OrderRec[0]['qty_cf']=$tmp_cf;
    
    $OrderRec['num'] = count($OrderRec);
    
    return $OrderRec;
}else{
    return false;
}

break;



//-------------------------------------------------------
case "CFM_ord":

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_dept = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_dept = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_dept = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_dept .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_dept .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_dept .= " ) ";
    }
}

$q_str = "SELECT `id` , `order_num` , `style_num` , `etd` , `etp` , `qty`, `uprice`, `revise`, `last_update`, factory, del_mk, ref  FROM `s_order` WHERE (`status` = '2' OR `del_mk` = '1') ".$where_dept."order by etd";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$z=0;
$CFMorder = $p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $CFMorder[$z] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$z] = $sql->fetch($q_res);
    $CFMorder[$z]['etd'] = $p_etd[$z]['p_etd'];
    $z++;
}

array_multisort($p_etd, SORT_ASC, $CFMorder);

if (!empty($CFMorder)) {
    $CFMorder['num'] = count($CFMorder);
    return $CFMorder;
}else{
    return false;
}

break;



//-------------------------------------------------------
case "REV_ord":

$q_str = "SELECT `id` , `order_num` , `style_num` , `etd` , `etp` , `qty`, `uprice`, `revise`, 
                                 `cfm_date`, factory, del_mk, ref  
                    FROM `s_order` 
                    WHERE (`status` = '13' OR `del_mk` = '2') order by etd";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$z=0;
$APVorder = $p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $APVorder[$z] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$z] = $sql->fetch($q_res);
    $APVorder[$z]['etd'] = $p_etd[$z]['p_etd'];
    $z++;
}

array_multisort($p_etd, SORT_ASC, $APVorder);

if (!empty($APVorder)) {
    $APVorder['num'] = count($APVorder);
    return $APVorder;
}else{
    return false;
}

break;



//-------------------------------------------------------
case "APV_ord":		
$q_str = "SELECT `id` , `order_num` , `style_num` , `etd` , `etp` , `qty`, `uprice`, `revise`, 
                                 `cfm_date`, factory, del_mk, ref  
                    FROM `s_order` 
                    WHERE (`status` = '3' OR `del_mk` = '2') order by etd";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$z=0;

$APVorder = $p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $APVorder[$z] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$z] = $sql->fetch($q_res);
    $APVorder[$z]['etd'] = $p_etd[$z]['p_etd'];
    $z++;
}

array_multisort($p_etd, SORT_ASC, $APVorder);

if (!empty($APVorder)){
    $APVorder['num'] = count($APVorder);
    return $APVorder;
}else{
    return false;
}

break;



//-------------------------------------------------------	
case "schdl_out":
$TODAY=date('Y-m-d');
$search_date1=increceDaysInDate($TODAY,-90);
$search_date2=increceDaysInDate($TODAY,-10);

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , 
         s_order.qty, s_order.status, pdtion.ets, pdtion.etf, s_order.factory  , pdtion.qty_done ,
         s_order.ref
         FROM `s_order`, pdtion 
         WHERE `status` < '10' and `status` > '5' and pdtion.order_num = s_order.order_num and etf < '$search_date2' and etd >='$search_date1'".$where_str."order by etf";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$j=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    
    $schorder[$j]['ex_date']=countDays($row['etf'],$TODAY);
    $schorder[$j]['rmk']=get_ord_status($row['status']);
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

if (!empty($schorder)){
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}		
break;



//-------------------------------------------------------	
case "schedule":
$TODAY=date('Y-m-d');
$search_date=increceDaysInDate($TODAY,$alt_value['un_schld']);
$search_date1=increceDaysInDate($TODAY,-90);

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , 
         s_order.qty, s_order.ref, mat_etd, m_acc_etd, s_order.factory  FROM `s_order`, pdtion 
         WHERE `status` = '4' and pdtion.order_num = s_order.order_num and etd <='$search_date' and etd >='$search_date1'".$where_str."order by etd";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$tmp[1]='';
$j=0; $tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

for($j=0;$j<sizeof($schorder);$j++){
    if ($schorder[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$schorder[$j]['qty'];
    $tmp1=explode('-',$schorder[$j]['etd']);
    if ($tmp1[1] <> $tmp[1])
    {
        $schorder[$j]['bk']=1;
        $tmp=explode('-',$schorder[$j]['etd']);	
    }
}		

if (!empty($schorder)){
    $schorder[0]['bk']=0;
    $schorder[0]['qty_hj']=$tmp_hj;
    $schorder[0]['qty_ly']=$tmp_ly;
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}
break;



//-------------------------------------------------------	
case "CFM_SCHDL":
$TODAY= date('Y-m-d');
$search_date1=increceDaysInDate($TODAY,-90);	

if($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA" || $GLOBALS['SCACHE']['ADMIN']['dept'] == "PM" || $GLOBALS['SCACHE']['ADMIN']['dept'] == "GM"){
    $where_str=" AND DEPT <> 'HJ'";
}else{
    $where_str=" AND s_order.factory = '".$GLOBALS['SCACHE']['ADMIN']['dept']."'";
}		
$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , 
         s_order.qty, s_order.ref, mat_etd, m_acc_etd, ets, etf, s_order.factory FROM `s_order`, pdtion 
         WHERE `status` = '6' and pdtion.order_num = s_order.order_num  and etd >='$search_date1'".$where_str."order by ets";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$tmp[1]='';
$j=0;
$tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

for($j=0;$j<sizeof($schorder);$j++){
    if ($schorder[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$schorder[$j]['qty'];
    $tmp1=explode('-',$schorder[$j]['ets']);

    if( empty($tmp1[1]) ) $tmp1[1] = 0 ;
    if ( $tmp1[1] <> $tmp[1] )
    {
        $schorder[$j]['bk']=1;
        $tmp=explode('-',$schorder[$j]['ets']);	
    }
}

if (!empty($schorder)){
    $schorder[0]['bk']=0;
    $schorder[0]['qty_hj']=$tmp_hj;
    $schorder[0]['qty_ly']=$tmp_ly;
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}
break;



//-------------------------------------------------------		
case "mater":
$TODAY= date('Y-m-d');
$search_date1=increceDaysInDate($TODAY,-90);
$search_date=increceDaysInDate($TODAY,$alt_value['un_eta']);	

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , 
         s_order.qty, s_order.ref, mat_etd, m_acc_etd, s_order.factory FROM `s_order`, pdtion 
         WHERE  status < '8' AND `status` >= '0' and pdtion.order_num = s_order.order_num  and (mat_etd IS NULL || m_acc_etd IS NULL)
         and etd >='$search_date1' and etd <='$search_date'".$where_str."order by etd";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$tmp[1]='';
$j=0;
$tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

for($j=0;$j<sizeof($schorder);$j++){
    if ($schorder[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$schorder[$j]['qty'];
    $tmp1=explode('-',$schorder[$j]['etd']);
    if ($tmp1[1] <> $tmp[1])
    {
        $schorder[$j]['bk']=1;
        $tmp=explode('-',$schorder[$j]['etd']);	
    }
}

if (!empty($schorder)){
    $schorder[0]['bk']=0;
    $schorder[0]['qty_hj']=$tmp_hj;
    $schorder[0]['qty_ly']=$tmp_ly;
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}
break;



//-------------------------------------------------------		
case "smpl_apv":
$TODAY= date('Y-m-d');
$search_date1=increceDaysInDate($TODAY,-90);
$search_date=increceDaysInDate($TODAY,$alt_value['un_smpl']);	

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , 
         s_order.qty, s_order.ref, smpl_apv, s_order.smpl_ord, s_order.factory FROM `s_order`, pdtion 
         WHERE  status < '8' AND `status` >= '0' and pdtion.order_num = s_order.order_num  and (smpl_apv IS NULL || smpl_apv = '0000-00-00')
         and etd >='$search_date1' and etd <='$search_date'".$where_str."order by etd";
//echo $q_str."<br>";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$tmp[1]='';
$j=0;
$tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

for($j=0;$j<sizeof($schorder);$j++){
    if ($schorder[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$schorder[$j]['qty'];
    $tmp1=explode('-',$schorder[$j]['etd']);
    if ($tmp1[1] <> $tmp[1])
    {
        $schorder[$j]['bk']=1;
        $tmp=explode('-',$schorder[$j]['etd']);	
    }
}

if (!empty($schorder)){
    $schorder[0]['bk']=0;
    $schorder[0]['qty_hj']=$tmp_hj;
    $schorder[0]['qty_ly']=$tmp_ly;
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}
break;



//-------------------------------------------------------		
case "ord_pttn":
$TODAY= date('Y-m-d');
$search_date1=increceDaysInDate($TODAY,-90);
$search_date=increceDaysInDate($TODAY,45);	
$ord_pttn = $alt_value['no_pttn'];

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$where_str .= " AND s_order.factory <> 'HJ' ";

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , (TO_DAYS(CURRENT_DATE)-TO_DAYS(smpl_apv)) as delay,
         s_order.qty, s_order.ref, s_order.smpl_apv,s_order.smpl_ord, s_order.factory, s_order.ptn_upload FROM `s_order`
         WHERE  status < '8' AND `status` >= '0' and (smpl_apv IS NOT NULL || smpl_apv <> '0000-00-00') and ptn_upload = '0000-00-00'
         and (TO_DAYS(CURRENT_DATE)-TO_DAYS(smpl_apv)) > ".$ord_pttn." and etd >='$search_date1'".$where_str."order by etd";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$tmp[1]='';
$j=0;
$tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

for($j=0;$j<sizeof($schorder);$j++){
    if ($schorder[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$schorder[$j]['qty'];
    $tmp1=explode('-',$schorder[$j]['etd']);
    if ($tmp1[1] <> $tmp[1])
    {
        $schorder[$j]['bk']=1;
        $tmp=explode('-',$schorder[$j]['etd']);	
    }
}

if (!empty($schorder))
{
    $schorder[0]['bk']=0;
    $schorder[0]['qty_hj']=$tmp_hj;
    $schorder[0]['qty_ly']=$tmp_ly;
    
    $schorder['num'] = count($schorder);
    
    return $schorder;
}else{
    return false;
}
break;



//-------------------------------------------------------		
case "mat_rcvd":
$TODAY= date('Y-m-d');
$search_date1=increceDaysInDate($TODAY,-90);	
$search_date=increceDaysInDate($TODAY,45);

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , 
         s_order.qty, mat_etd, m_acc_etd, s_order.factory, mat_shp, m_acc_shp, s_order.status ,
         s_order.ref, mat_eta, m_acc_eta
         FROM `s_order`, pdtion 
         WHERE   ((mat_eta IS NOT NULL and mat_shp IS NULL and mat_eta <= '$TODAY' and mat_eta > '0000-00-00')OR (m_acc_eta IS NOT NULL and m_acc_shp IS NULL and m_acc_eta <= '$TODAY' and m_acc_eta > '0000-00-00')) 
         and status < '8' AND `status` >= '0' and pdtion.order_num = s_order.order_num  
         and etd >='$search_date1'".$where_str."order by etd";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$tmp[1]='';
$j=0; $tmp_hj=0; $tmp_ly=0; $tmp_cf=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

for($j=0;$j<sizeof($schorder);$j++){
    if ($schorder[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$schorder[$j]['qty'];
    $tmp1=explode('-',$schorder[$j]['etd']);
    if ($tmp1[1] <> $tmp[1])
    {
        $schorder[$j]['bk']=1;
        $tmp=explode('-',$schorder[$j]['etd']);	
    }
    $schorder[$j]['rmk']=get_ord_status($schorder[$j]['status']);
}

if (!empty($schorder)){
    $schorder[0]['bk']=0;
    $schorder[0]['qty_hj']=$tmp_hj;
    $schorder[0]['qty_ly']=$tmp_ly;
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}
break;



//-------------------------------------------------------		
case "ex_etd":
$TODAY= date('Y-m-d');
$s_date=increceDaysInDate($TODAY,-90);	
$e_date=increceDaysInDate($TODAY,$alt_value['una_prd']);

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd , s_order.etp , s_order.smpl_apv,
         s_order.qty, mat_etd, m_acc_etd, s_order.factory, mat_shp, m_acc_shp, s_order.status , s_order.ref
         FROM `s_order` left join pdtion on
         s_order.order_num=pdtion.order_num where s_order.status < 8 AND s_order.status >= '0'
          and (mat_shp IS NULL or m_acc_shp IS NULL or smpl_apv = '0000-00-00' or s_order.status=6)
          and s_order.etd > '$s_date' and s_order.etd < '$e_date'".$where_str." order by s_order.etd";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$tmp[1]='';
$j=0; $tmp_hj=0; $tmp_ly=0; $tmp_cf=0; $tal_qty=0;
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

for($j=0;$j<sizeof($schorder);$j++){
    if ($schorder[$j]['factory'] == 'HJ')$tmp_hj=$tmp_hj+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'LY')$tmp_ly=$tmp_ly+$schorder[$j]['qty'];
    if ($schorder[$j]['factory'] == 'CF')$tmp_cf=$tmp_cf+$schorder[$j]['qty'];
    $tal_qty = $tal_qty + $schorder[$j]['qty'];
    $tmp1=explode('-',$schorder[$j]['etd']);
    if ($tmp1[1] <> $tmp[1]){
        $schorder[$j]['bk']=1;
        $tmp=explode('-',$schorder[$j]['etd']);	
    }
    $tmp_date=countDays($TODAY,$schorder[$j]['etd']);
    $schorder[$j]['etn1']=$tmp_date;		
    $schorder[$j]['rmk']=get_ord_status($schorder[$j]['status']);
}

$tmp_qty=0;

if (!empty($schorder)){
    $schorder[0]['tal_qty']=$tal_qty;
    $schorder[0]['bk']=0;
    $schorder[0]['qty_hj']=$tmp_hj;
    $schorder[0]['qty_ly']=$tmp_ly;
    for ($i=0; $i<sizeof($schorder); $i++)    {
        if (!isset($schorder[$i]['bk']) || $schorder[$i]['bk'] == 0){
            $tmp_qty=$tmp_qty+$schorder[$i]['qty'];			
            $tmp_mm= substr($schorder[$i]['etd'],0,7);	
                    
        }else{
            $schorder[$i]['mm_qty']=$tmp_qty;			
            $schorder[$i]['sub_mm']=$tmp_mm;			
            $tmp_qty=$schorder[$i]['qty'];	
        }
    }	
    $schorder[$i]['bk']=1;
    $schorder[$i]['mm_qty']=$tmp_qty;	
    $schorder[$i]['sub_mm']=$tmp_mm;
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}
break;	



//-------------------------------------------------------		
case "smpl_apv_undo":
$TODAY= date('Y-m-d');
$alt_value['smpl_apv_undo'] = $alt_value['smpl_apv_undo'] * -1;
$search_date1=increceDaysInDate($TODAY,-90);
$search_date=increceDaysInDate($TODAY,$alt_value['smpl_apv_undo']);	

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$q_str = "SELECT smpl_ord.id , smpl_ord.num , smpl_ord.ref , smpl_ord.etd , smpl_ord.style , 
                         smpl_ord.qty, smpl_ord.factory, smpl_ord.last_update, smpl_ord.status, 
                         smpl_ord.last_update
         FROM    `smpl_ord`, s_order 
         WHERE   smpl_ord.apv_date = '0000-00-00' AND smpl_ord.last_update <'".$search_date."' AND
                         s_order.smpl_ord = SUBSTRING( smpl_ord.num, 1, 9 )  AND s_order.status > 0 AND        				 
                         smpl_ord.open_date >= '$search_date1' ".$where_str." order by num DESC";
                         
//(smpl_ord.status = 10 || smpl_ord.status =7) AND
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$j=0;
$tmp_num = '';
while ($row = $sql->fetch($q_result)) {			
    if($tmp_num <> substr($row['num'],0,-2)){
        if($row['status'] == 10  || $row['status'] ==7){
            $schorder[$j] = $row;
            $schorder[$j]['lead'] = countDays($row['last_update'],$TODAY);
            $schorder[$j]['sort'] = countDays($row['last_update'],$TODAY);
            $j++;
        }
        $tmp_num = substr($row['num'],0,-2);
    }
}

//exit;
if (!empty($schorder)){
    $schorder = bubble_sort($schorder);
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}	
break;



//-------------------------------------------------------		
case "none_wiws":
$TODAY= date('Y-m-d');
$alt_value['none_wiws'] = $alt_value['none_wiws'] *-1;
$search_date1=increceDaysInDate($TODAY,-90);
$search_date=increceDaysInDate($TODAY,$alt_value['none_wiws']);	

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}

$fty_str = " AND s_order.dept <> s_order.factory ";

$q_str = "SELECT s_order.id , s_order.order_num , s_order.style_num , s_order.etd ,s_order.smpl_apv,
                         s_order.qty, s_order.factory, s_order.status , s_order.ref, wi.wi_num, wi.cfm_date,
                         wi.ti_cfm 
         FROM  `s_order` LEFT JOIN wi ON wi.wi_num = s_order.order_num
         WHERE  (wi.id IS NULL OR wi.ti_cfm = '0000-00-00' OR wi.cfm_date like '0000-00-00%') AND
                        s_order.status < '8' AND s_order.status >= '0' AND
                        s_order.smpl_apv < '$search_date' AND s_order.smpl_apv <> '0000-00-00' AND
                        s_order.etd >= '$search_date1' ".$where_str.$fty_str." order by s_order.etd";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

$j=0;
$tmp_num = '';
$p_etd = array();
while ($row = $sql->fetch($q_result)) {
    $schorder[$j] = $row;
    
    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num`= '".$row['order_num']."' order by p_etd limit 0,1";
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k�s����Ʈw!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $p_etd[$j] = $sql->fetch($q_res);
    $schorder[$j]['etd'] = $p_etd[$j]['p_etd'];
    $schorder[$j]['lead'] = countDays($row['smpl_apv'],$TODAY);
    $schorder[$j]['cfm_date'] = substr($schorder[$j]['cfm_date'],0,10);
    $j++;
}

array_multisort($p_etd, SORT_ASC, $schorder);

if (!empty($schorder)){
    $schorder['num'] = count($schorder);
    return $schorder;
}else{
    return false;
}	
break;



//-------------------------------------------------------			
case "fab_chk":

# ���������
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
if ($user_team == 'MD') {
    $where_str = " AND s_order.dept = '$user_dept'";
} else {
    if ( if_factory() && !$argv['PHP_factory'] ) {
        $where_str = " AND s_order.factory = '$user_dept'";
    } else {
        $dept_group = get_dept_group();
        $where_str = " AND ( ";
        for ($i=0; $i< sizeof($dept_group); $i++) {
            $where_str .= ( $i > 0 ) ? ' OR ' : '' ;
            $where_str .= " s_order.dept = '$dept_group[$i]' ";
        }
        $where_str .= " ) ";
    }
}
		
$q_str = "SELECT s_order.`id` , `order_num` , `creator`, `qty` , `etd` , `etp` , `style_num` , `uprice`, 
                                 factory, ref, avg( est_1 ) AS avg_est, `mat_useage`   
                    FROM  `s_order`, lots_use 
                    WHERE  s_order.order_num = lots_use.smpl_code AND use_for LIKE 'shell%' AND 
                                 s_order.status < 8 AND s_order.status > 0 ".$where_str." AND lots_chk = 0
                    GROUP BY s_order.order_num
                    HAVING avg_est > 0 AND avg_est != `mat_useage` 
                    ORDER BY etd";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}
//		
$tmp[1]='';
while ($row = $sql->fetch($q_result)) {
    $OrderRec[] = $row;
}

if (!empty($OrderRec)){
    $OrderRec['num'] = count($OrderRec);
    return $OrderRec;
}else{
    return false;
}

break;	
		
} 
}// end func





} // end class
?>