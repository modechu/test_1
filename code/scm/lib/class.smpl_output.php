<?php 

#++++++++++++++++++++++ SAMPLE OUT PUT  class ##### �˥����� CLASS  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J

#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL_OUTPUT {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! cannot connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s out put �O��
#		�[�J�s USER			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	

		if (!is_numeric($parm['qty'])) {
			$this->msg->add("Error ! please type <b>only numberic</b> for Quantity�C");
			return false;    
		}
					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl_output (num, line_id, factory, fdate, qty) VALUES ('".$parm['num']."','".$parm['line_id']."','".$parm['factory']."','".$parm['fdate']."', qty + ".$parm['qty'].")";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot add new record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

//		$this->msg->add("���\ �s�W�Ȥ� : [".$parm['login_id']."]�C") ;

		return $new_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� ���~�򥻸��[��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($num) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� smpl_type ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM smpl_output WHERE num ='$num' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_link($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($ord_num) {
		$ord_rec = array();
		$sql = $this->sql;
		$ord_num = substr($ord_num,0,-2);
		$q_str = "SELECT * FROM   smpl_output
							WHERE  num = '".$ord_num."'";

		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result))
		{
			$ord_rec[] = $row;
		}

		return $ord_rec;
	} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_link($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_output($ord_num) {
		$ord_rec = array();
		$sql = $this->sql;
//		$ord_num = substr($ord_num,0,-2);
		$q_str = "SELECT smpl_output.*, line as line_name FROM   smpl_output, smpl_saw_line
							WHERE  smpl_output.line_id = smpl_saw_line.id AND num = '".$ord_num."'";

		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result))
		{
			$ord_rec[] = $row;
		}

		return $ord_rec;
	} // end func			

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>