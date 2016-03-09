<?php 

#++++++++++++++++++++++ USER  class ##### �ϥΪ�  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class USER {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! �L�k�p�W��Ʈw.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		�[�J�s USER ���߬d
#	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$sql = $this->sql;
			############### �ˬd��J����					
		if (!$parm['dept_code'] = trim($parm['dept_code'])) {
			$this->msg->add("Error ! �п�J�����N�� !");
		    return false;
		}
		if (!$parm['login_id'] = trim($parm['login_id'])) {
			$this->msg->add("Error ! �п�J�ϥΪ̥N��[login ID !");
		    return false;
		}
		if (!$parm['login_pass1'] = trim($parm['login_pass1'])) {
			$this->msg->add("Error ! �п�J�ϥΪ̱K�X[login password !");
		    return false;
		}
		if (!$parm['login_pass2'] = trim($parm['login_pass2'])) {
			$this->msg->add("Error ! �аO�o��J�ϥΪ̱K�X�⦸[login password !");
		    return false;
		}
		if (!$parm['login_pass1'] == $parm['login_pass2']) {
			$this->msg->add("sorry! �⦸�K�X��J���P�A�Ъ`�N�j�p�g�t�O !");
		    return false;
		}
		if ($parm['login_pass1'] != $parm['login_pass2']) {
			$this->msg->add("sorry! �⦸�K�X��J���P�A�Ъ`�N�j�p�g�t�O !");
		    return false;
		}
		if (!$parm['team_code'] = trim($parm['team_code'])) {
			$this->msg->add("Error ! �п�ܲէO�N�� !");
		    return false;
		}

					# �ˬd�O�_������
		$q_str = "SELECT id FROM user WHERE login_id='".$parm['login_id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if ($sql->num_rows($q_result) || is_manager($parm['login_id'])) {
			$this->msg->add("sorry ! �o�ӥN���w�g���O�H�ϥΤF�A�д��O���N��!");
			return false;    	    
		}

		$this->msg->add("�ЦA�T�{�s�Wuser ������v�� ! ") ;

	return true;

	} // end functin		
					

					
					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s USER
#		�[�J�s USER			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
					
					# �[�J��Ʈw
					
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['name'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['name']);
		}
		$q_str = "INSERT INTO user (emp_id, dept, login_id, login_pass, team_id, name, phone, cellphone, email, perm) VALUES ('".$parm['emp_id']."','".$parm['dept_code']."','".$parm['login_id']."','".$parm['login_pass']."','".$parm['team_code']."','".$parm['name']."','".$parm['phone']."','".$parm['cellphone']."','".$parm['email']."','".$parm['perm']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("���\ �s�Wuser : [".$parm['login_id']."]�C") ;
		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	�j�M  USER ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
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
#--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
		$srh->row_per_page = 10;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end    	
 		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
			
			
//2006.11.10�W�[�P�w�O�_�i�H�s�Wteam�����			
		$q_dep_str = "select id from dept";
		$q_dep_result = $sql->query($q_dep_str);
		if ($sql->fetch($q_dep_result)){ $op['dep_check']=1; }


		$op['user'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
#--*****--##2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#--*****--##2006.11.14�s���X�ݭn��oup_put	end   

		return $op;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, login_id=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $login_id=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM user WHERE id='$id' ";
		} elseif ($login_id) {
			$q_str = "SELECT * FROM user WHERE login_id='$login_id' ";
		} else {
			$this->msg->add("Error ! �Ы��� USER��Ʀb��Ʈw���� ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if (!$row = $sql->fetch($q_result)) {

			$this->msg->add("Error ! �L�k���o���O��!");
			if($login_id == 'yankee')
			{
				$row['name'] = '�����';
				$row['dept'] = 'SA';
				$row['email'] = 'jack@tp.carnival.com.tw';
			}else{
				return false; 
			}  
		}		

		
		
		return $row;
	} // end func


/*----------
# �\�໡�� : ��s USER ���
# �ɤJ�ܼ� : 	
# ��J�ܼ� : 
# ��X�ܼ� : true
# ���g��� : 2010/06/01
# �Ƶ����� : 
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
    $_SESSION['msg'][] = ("Error !  �L�k��s��Ʈw.");
    return false;    
  }

  $_SESSION['USER']['ADMIN']['name'] = $parm['name'];

  return true;
} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_team($n_field,$where_str="")	�[�`team�Muser���ȧ@���,�w�����n��user���v����team�Ӫ��j
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_team($perm,$dept_code,$team_code){
    global $MySQL;

		$q_str = "SELECT `perm` FROM `team` WHERE `dept_code` LIKE '".$dept_code ."' AND `team_code` LIKE '".$team_code."' ";

		if (!$q_result = $MySQL->query($q_str)) { 
			$_SESSION['msg'][] = ("Error! �L�k�s����Ʈw!");
			return false;
		}
	
		if (!$row = $MySQL->fetch($q_result)) {
 			$_SESSION['msg'][] = ("Error ! �L�k���o���O��!"); 
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
#	->update_field($parm)		��s USER ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE user SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� USER ���  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� USER ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM user WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM user ".$where_str;
// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
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
			if ($match != 500) {   // �O�d �|���@��
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>