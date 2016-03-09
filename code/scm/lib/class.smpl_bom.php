<?php 

#++++++++++++++++++++++ BOM  class ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add_lots($parm)		�[�J�s BOM �D��

#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class  SMPL_BOM {
		
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
#	->add_lots($parm)		�[�J�s BOM �D��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_lots($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	

					
					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl_bom_lots (wi_id,
									lots_used_id,
									color,
									qty,
									k_date) VALUES('".
									$parm['wi_id']."','".
									$parm['lots_used_id']."','".
									$parm['color']."','".
									$parm['qty']."','".
									$parm['this_day']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("���\ �s�W BOM �D�ưO���C") ;

		return $new_id;

	} // end func
	

					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_acc($parm)		�[�J�s BOM �Ʈ�
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_acc($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	

					
					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl_bom_acc (wi_id,
									acc_used_id,
									color,
									qty,
									size,
									k_date) VALUES('".
									$parm['wi_id']."','".
									$parm['acc_used_id']."','".
									$parm['color']."','".
									$parm['qty']."','".
									$parm['size']."','".
									$parm['this_day']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("���\ �s�W BOM �ƮưO���C") ;

		return $new_id;

	} // end func
	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lots ($mode=0, $where_str="")	�j�M  BOM ���D�� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_lots($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM smpl_bom_lots ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 200;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
		} 
if ($mode==1){    //�����󪺷j�M��
/*
		if ($str = $argv['PHP_acc_code'] )  { 
			$srh->add_where_condition("acc_code LIKE  '%$str%'", "PHP_acc_code",$str,"�j�M �Ͳ������N���t��: [ $str ]���e. "); }
		
		if ($str = $argv['PHP_acc_name'] )  { 
			$srh->add_where_condition("acc_name LIKE  '%$str%'", "PHP_acc_name",$str,"�j�M �Ͳ������W�٧t��: [ $str ]���e. "); }

		if ($str = $argv['PHP_des'] )  { 
			$srh->add_where_condition("des LIKE '%$str%'", "PHP_des",$str,"�j�M �Ͳ����������t��: [ $str ]���e "); }

		if ($str = $argv['PHP_specify'] )  { 
			$srh->add_where_condition("specify LIKE '%$str%'", "PHP_specify",$str,"�j�M �Ͳ������W��t��: [ $str ]���e "); }
*/
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

		$op['lots'] = $result;  // ��ƿ� �ߤJ $op
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
#	->search_acc ($mode=0, $where_str="")	�j�M  BOM ���Ʈ� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_acc($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM smpl_bom_acc ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 200;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
		} 
if ($mode==1){    //�����󪺷j�M��
/*
		if ($str = $argv['PHP_acc_code'] )  { 
			$srh->add_where_condition("acc_code LIKE  '%$str%'", "PHP_acc_code",$str,"�j�M �Ͳ������N���t��: [ $str ]���e. "); }
		
		if ($str = $argv['PHP_acc_name'] )  { 
			$srh->add_where_condition("acc_name LIKE  '%$str%'", "PHP_acc_name",$str,"�j�M �Ͳ������W�٧t��: [ $str ]���e. "); }

		if ($str = $argv['PHP_des'] )  { 
			$srh->add_where_condition("des LIKE '%$str%'", "PHP_des",$str,"�j�M �Ͳ����������t��: [ $str ]���e "); }

		if ($str = $argv['PHP_specify'] )  { 
			$srh->add_where_condition("specify LIKE '%$str%'", "PHP_specify",$str,"�j�M �Ͳ������W��t��: [ $str ]���e "); }
*/
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

		$op['acc'] = $result;  // ��ƿ� �ߤJ $op
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

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_id=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $acc_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM ti WHERE id='$id' ";
		} elseif ($acc_code) {
			$q_str = "SELECT * FROM ti WHERE wi_id='$wi_id' ";
		} else {
			$this->msg->add("Error ! �Ы��� �Ͳ�������Ʀb��Ʈw���� ID.");		    
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
*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_lots($id,$mode=0)		�R�� BOM �D�ưO�� 
#					[mode=0: $id��smpl_bom_lots��id [�浧�R��] ; 
#					[mode=1: $id��wi_id ����� �N ���w���s�O������BOM�D�ưO�����R�� ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_lots($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� BOM���D�Ƹ�ƪ� ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM smpl_bom_lots WHERE wi_id='$id' ";
		}else{
			$q_str = "DELETE FROM smpl_bom_lots WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_acc($id,$mode=0)		�R�� BOM �ƮưO�� 
#					[mode=0: $id��smpl_bom_lots��id [�浧�R��] ; 
#					[mode=1: $id��wi_id ����� �N ���w���s�O������BOM�ƮưO�����R�� ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_acc($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� BOM���ƮƸ�ƪ� ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM smpl_bom_acc WHERE wi_id='$id' ";
		}else{
			$q_str = "DELETE FROM smpl_bom_acc WHERE id='$id' ";
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
	function get_fields_lots($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl_bom_lots ".$where_str;
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_acc($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl_bom_acc ".$where_str;

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
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE ".$parm[3]." SET ".$parm[1]."='".$parm[2]."'  WHERE id='".$parm[0]."'";
echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>