<?php 

#++++++++++++++++++++++ STYLE_TYPE  class ++++  �ڦ����O +++++++++++++++++++++++++++++++++++
#	->init($sql)				�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)				�[�J
#	->search($mode=0)			�j�M   
#	->get($id=0, nbr=0)			��X���w �O�������   
#	->update($parm)				��s ���
#	->update_field($parm)		��s ��Ƥ� �Y�ӳ�@���
#	->del($id)					�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� STYLE TYPE �� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class STYLE_TYPE {
		
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
#	->add($parm)		�[�J�s�ڦ����O
#		�[�J�s�ڦ����O			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### �ˬd��J����					
		if (!$parm['style_type'] = trim($parm['style_type'])) {
			$this->msg->add("Error ! �п�J�ڦ����O !");
		    return false;
		}
					# �����令�j�g
		$parm['style_type'] = strtoupper($parm['style_type']);	

					# �ˬd�O�_������
		$q_str = "SELECT id FROM style_type WHERE style_type='".$parm['style_type']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! �o�Ӵڦ����O�w�g�n���F�A�д��O�����O�Χ��줺�e!");
			return false;    	    
		}
					# �[�J��Ʈw
		$q_str = "INSERT INTO style_type (style_type, des) VALUES ('".$parm['style_type']."','".$parm['des']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$style_type_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("���\ �s�W�ڦ����O : [".$parm['style_type']."]�C") ;
		return $style_type_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	�j�M �ڦ����O	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM style_type";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("style_type ");
		$srh->row_per_page = 12;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
		} 
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['style_type'] = $result;  // ��ƿ� �ߤJ $op
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

		return $op;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, style_type=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $style_type=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM style_type WHERE id='$id' ";
		} elseif ($style_type) {
			$q_str = "SELECT * FROM style_type WHERE style_type='$style_type' ";
		} else {
			$this->msg->add("Error ! �Ы��� �ڦ����O �b��Ʈw���� ID.");		    
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		��s �ڦ����O ���
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
					# �����令�j�g
		$parm['style_type'] = strtoupper($parm['style_type']);	

					# �ˬd�O�_���o���ɮצs�b
		$q_str = "SELECT id FROM style_type WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! �L�k����Ʈw�O��!");
			return false;    	    
		}

###########  �߬d �s�[�J�� style_type �O�_���ߤ@ [note:�i��|����Ӫ��ۤv #####
$q_str2 = "SELECT id FROM style_type WHERE style_type='".$parm['style_type']."'"." AND id <> '".$parm['id']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			return false;    	    
		}
		if ($sql->num_rows($q_result2) ) {
			$this->msg->add("SORRY ! �o�Ӵڦ����O�w�g�n����Ʈw���A�д��O���W�٩άO��s��O��!");
			return false;    	    
		}
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE style_type SET style_type='".$parm['style_type']."', des='".$parm['des']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �ڦ����O��Ƥ� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE style_type SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �ڦ����O���[��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� style_type ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM style_type WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� STYLE TYPE �� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM style_type ".$where_str;

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