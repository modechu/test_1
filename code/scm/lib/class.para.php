<?php 

#++++++++++++++++++++++ LOTS  class ##### �D��  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						Search   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class PARA {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Can't access database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search()	�j�M�Ҧ�Para Set��Ƥ��e
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search() {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT * FROM para_set ";
		if (!$q_result = $sql->query($q_str)) {		//�j�M�̫�@��
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {	//�p�G�S����ƪ���
			$fields[] = $row;		
		}
		return $fields;
	} // end func					
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search()	�j�M�Ҧ�Para Set��Ƥ��e
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_mesg() {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT * FROM mesg_set ORDER BY id";
		if (!$q_result = $sql->query($q_str)) {		//�j�M�̫�@��
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {	//�p�G�S����ƪ���
			$fields[] = $row;		
		}
		return $fields;
	} // end func				
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �D�ưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
		$back=0;

			############### �ˬd��J����	
			//  �令�j�g
		
		if (!$parm['set_name'] = trim($parm['set_name'])) {
			$this->msg->add("Error ! Please input Para. Set Name !");
			return false;
		}
	
					# �ˬd�O�_������

		$q_str = "SELECT id FROM para_set WHERE set_name='".$parm['set_name']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This Para. Set Name is exist in database, please change another ones!");
			return false;    	    
		}
					
					# �[�J��Ʈw
		$q_str = "INSERT INTO para_set (set_name,
									set_value,
									memo
									) VALUES('".
									$parm['set_name']."','".
									$parm['set_value']."','".								
									$parm['memo']."')";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("Success Append Para. Set on ".$parm['memo']);

		return $new_id;

	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $lots_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $set_name=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM para_set WHERE id='$id' ";
		} elseif ($set_name) {
			$q_str = "SELECT * FROM para_set WHERE set_name='$set_name' ";
		} else {
			$this->msg->add("Error ! Please point fabric ID.");		    
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
#	->get($id=0, $lots_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_mesg($id=0, $set_job=0,$set_group=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM mesg_set WHERE id='$id' ";
		} elseif ($set_job) {
			$q_str = "SELECT * FROM mesg_set WHERE set_job='$set_job' AND set_group='$set_group'";
		} else {
			$this->msg->add("Error ! Please point fabric ID.");		    
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
#	->update($parm)		��s �D�ưO��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update($parm) {

		$sql = $this->sql;
			
		
		#####   ��s��Ʈw���e

		$q_str = "UPDATE para_set SET set_value='"	.$parm['set_value'].
						"', memo='"		.$parm['memo'].		
						"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		��s �D�ưO��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_mesg($parm) {

		$sql = $this->sql;
			
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE mesg_set SET 
							set_user='"		.$parm['set_user']."',
						  set_title='"	.$parm['set_title']."',		
						  set_text='"		.$parm['set_text'].
						"' WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �D�� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE para_set SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �D�ưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point fabric ID.");		    
			return false;
		}
		$q_str = "DELETE FROM para_set WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �D�ưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_mesg($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point fabric ID.");		    
			return false;
		}
		$q_str = "DELETE FROM mesg_set WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
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
		$q_str = "SELECT ".$n_field." FROM para_set ".$where_str;

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
			if ($match != 500) {   // �O�d �|���@��
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_mesg_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM mesg_set ".$where_str;

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