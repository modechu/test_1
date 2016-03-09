<?php 

#++++++++++++++++++++++ SMPL_LOTS  class ##### �˥� �D�� +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->check($parm)		�ˬd �[�J�s �˥��O�� �O�_���T
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL_ORD_LOTS {
		
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
#	->check($parm)		�ˬd �[�J�s �˥��O�� �O�_���T
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
		if (!$parm['cust'] = trim($parm['cust'])) {
			$this->msg->add("Error ! �п�J�Ȥ�O �C");
		    return false;
		}
		if (!$parm['year'] = trim($parm['year'])) {
			$this->msg->add("Error ! �п�J�~�� �C");
		    return false;
		}
							
//		if ($this->msg){
//			return false;
//		}
					
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �˥� �D�ưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
		$parm['num'] = strtoupper($parm['num']);
		if (!$parm['num'] = trim($parm['num'])) {
			$this->msg->add("Error ! Please input fabric # �C");
		    return false;
		}
		$q_str = "SELECT id FROM lots WHERE lots_code='".$parm['num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This fabric code is not exist in database!");
			return false;    	    
		}
		if ($parm['unit'] == '' ) {
			$this->msg->add("Error ! Please choice unit �C");
		    return false;
		}
		if (!is_numeric($parm['est'] ) && !$parm['est'] == '') {
			$this->msg->add("Error ! Please input currect ETS unit cost�C");
			return false;
		}
		$parm['use_for'] = str_replace("'","\'",$parm['use_for']);	
		$parm['use_for']  = str_replace("&#33031;","��",$parm['use_for'] );
		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
			$parm['use_for'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['use_for']);
					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl_lots_use (smpl_code,
										use_method,
										lots_code,
										unit,
										lots_name,
										est_1,
										user,
										add_date,
										use_for) VALUES('".
									$parm['smpl_code']."','".
									$parm['use_method']."','".
									$parm['num']."','".
									$parm['unit']."','".
									$parm['name']."','".
									$parm['est']."','".
									$parm['user']."','".
									$parm['add_date']."','".
									$parm['use_for']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$id = $sql->insert_id();  //���X �s�� id
		return $id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	�j�M �˥� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $where_str="") {
		$sql = $this->sql;
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if ($where_str)
		{
			$q_header = "SELECT smpl_lots_use.*, lots.comp, lots.width, lots.weight, lots.cons, lots.des, lots.specify, lots.vendor1, lots.price1 FROM smpl_lots_use, lots ".$where_str." and smpl_lots_use.lots_code=lots.lots_code";
		}else{
			$q_header = "SELECT smpl_lots_use.*, lots.comp, lots.width, lots.weight, lots.cons, lots.des, lots.specify, lots.vendor1, lots.price1 FROM smpl_lots_use, lots Where smpl_lots_use.lots_code=lots.lots_code";
		}
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("id");
		$srh->row_per_page = 50;
		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$lots_use = $result;  // ��ƿ� �ߤJ $op
		return $lots_use;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $smpl_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $smpl_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM smpl_lots_use WHERE id='$id' ";
		} elseif ($smpl_code) {
			$q_str = "SELECT * FROM smpl_lots_use WHERE smpl_code='$style_code' ";
		} else {
			$this->msg->add("Error ! �Ы��� �D�Ƹ�Ʀb��Ʈw���� ID.");		    
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
#	->update_field($parm)		��s �D�ƨϥ� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		$parm[2] = str_replace("'","\'",$parm[2]);	
		$parm[2]  = str_replace("&#33031;","��",$parm[2] );
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
			$parm[2] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm[2]);
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_lots_use SET ".$parm[1]."='".$parm[2]."'  WHERE id=".$parm[0]." ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R�� �˥��D�ưO��  [��ID]�R��
#							$mode=0: $id= �O����id; $mode<>0: $id=�˥��s��smpl_code
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! �Ы��� �˥��D�� ��ƪ� ID �� �˥��N�� .");		    
			return false;
		}

		if($mode){
			$q_str = "DELETE FROM smpl_lots_use WHERE smpl_code='$id' ";
		}else{
			$q_str = "DELETE FROM smpl_lots_use WHERE id='$id' ";
		}

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
		$q_str = "SELECT ".$n_field." FROM smpl_lots_use ".$where_str;

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