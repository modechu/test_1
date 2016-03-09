<?php 

#++++++++++++++++++++++ WIQTY  class #####  �s�y�O�ƶq +++++++++++++++++++++++++++++++++++
#	->init($sql)				�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)				�[�J
#	->search($mode=0)			�j�M   
#	->get($id=0, nbr=0)			��X���w �O�������   
#	->edit($parm)				��s ���
#	->update_field($parm)		��s ��Ƥ� �Y�ӳ�@���
#	->del($id)					�R�� ��ƿ�
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class WIQTY {
		
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
#	->check($parm)		�ˬd �[�J�s �s�y�O�ƶq�O�� ��J�O�_���T
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
		if (!$parm['colorway'] ) {
			$this->msg->add("Error ! �ж�J ��եN���ΦW�� �C");
		}






		if (count($this->msg->get(2))){
			return false;
		}
				
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �s�y�O�ƶq�O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
		$admin =  new AUTH();   // �N�|�I�s�쪺 class    ?????????????????????????????
		$perm_array = array();
		
			
					# �[�J��Ʈw
		$q_str = "INSERT INTO wiqty (	wi_id, 
										p_id,
										colorway, 
										qty) VALUES('".
										$parm['wi_id']."','".
										$parm['p_id']."','".
										$parm['colorway']."','".
										$parm['qty']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W�O��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$team_id = $sql->insert_id();  //���X �s�� id
//		$this->msg->add("���\ �n�J�s�y�O �ƶq�O��: [".$parm['wi_num']."]�C") ;
		return $parm;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	�j�M �s�y�O�ƶq�� ���O��	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if($mode=1){
			$q_header = "SELECT * FROM wiqty ".$where_str;
			if (!$srh->add_q_header($q_header)) {
				$this->msg->merge($srh->msg);
				return false;
			}
		} else {
			$q_header = "SELECT * FROM wiqty";
			if (!$srh->add_q_header($q_header)) {
				$this->msg->merge($srh->msg);
				return false;
			}
		}
		
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id");
		$srh->row_per_page = 500;
/*
		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
		} 
*/
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ���d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['wiqty'] = $result;  // ��ƿ� �ߤJ $op
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
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($field_name,$where_str="")	���X���� ���������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	# 2008-11-13
	function get_fields($field_name='*', $where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$field_name." FROM wiqty ".$where_str;
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;
		}

		while ($row = $sql->fetch($q_result)) {
			$fields[] = $row;
		}

		return $fields;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, team_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/*	function get($id=0, $team_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM team WHERE id='$id' ";
		} elseif ($team_code) {
			$q_str = "SELECT * FROM team WHERE team_code='$team_code' ";
		} else {
			$this->msg->add("Error ! �Ы��� ������Ʀb��Ʈw���� ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! �L�k���o���O��!");
			return false;    
		}
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($field_name,$where_str="")	���X���� ���������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($field_name, $where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$field_name." FROM team ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
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
			if ($match != 100) {   // �O�d �|���@��
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field($field, $where_str="")	��X���w�O������� RETURN $field_body[0]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_field($field, $where_str="") {

		$sql = $this->sql;

			$q_str = "SELECT $field FROM team WHERE $where_str ";
		if (!$field){	
			$this->msg->add("Error ! �Ы��� �n���o����ƪ����� ���W��.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$field_body = $sql->fetch($q_result)) {
			$this->msg->add("Error ! �L�k���o���O��!");
			return false;    
		}
		return $field_body[0];
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �էO���
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		$admin =  new AUTH();   // �N�|�I�s�쪺 class

		############### �ˬd��J����
					# �����令�j�g
		$parm['team_code'] = strtoupper($parm['team_code']);	

					# �ˬd�O�_���o���ɮצs�b
		$q_str = "SELECT id FROM team WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! �L�k����Ʈw�O��!");
			return false;    	    
		}

###########  �߬d �s�[�J�� team_code �O�_���ߤ@ [note:�i��|����Ӫ��ۤv #####  �������ߤ@[�D�����ߤ@]
$q_str2 = "SELECT id FROM team WHERE team_code='".$parm['team_code']."'"." AND id <> '".$parm['id']."'"." AND dept_code = '".$parm['dept_code']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			return false;    	    
		}
		if ($sql->num_rows($q_result2) ) {
			$this->msg->add("SORRY ! �o�ӲէO�N���w�g�إ߹L�F�A�д��O���N��!");
			return false;    	    
		}
		
		// �v���}�C�令�r��
		$perm_array = $parm['perm'];
		$parm['perm_str'] = $admin->encode_perm_str($perm_array);

		#####   ��s��Ʈw���e
		$q_str = "UPDATE team SET team_code='".$parm['team_code']."', team_name='".$parm['team_name']."', dept_code='". $parm['dept_code']."', perm='".$parm['perm_str']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ���~�򥻸�Ƥ� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE team SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
*/

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R��  �s�y�O�ƶq�O�� [mode=0: $id��qty��id ; $mode=1: $id��wi_id]
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� WI QTY ��ƪ��� ID.");		    
			return false;
		}
		if ($mode){
			$q_str = "DELETE FROM wiqty WHERE wi_id='$id' ";
		}else{
			$q_str = "DELETE FROM wiqty WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($field, $val, $id)		��s s_order��ưO���� �Y�ӳ�@���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($field, $val, $id) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE wiqty SET ".$field."='".$val."'  WHERE id=".$id." ";
// echo $q_str.'<br>';

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



} // end class


?>