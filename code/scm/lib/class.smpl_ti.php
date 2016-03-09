<?php 

#++++++++++++++++++++++ TI  class ##### �Ͳ�����  +++++++++++++++++++++++++++++++++++
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

class  SMPL_TI {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Can't connect to database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �Ͳ������O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	
			//  �令�j�g

		if (!$parm['item']) {
			$this->msg->add("Error ! Please input production Description item !");
		    return false;
		}

		$parm['detail'] = str_replace("'","\'",$parm['detail']);		
		$parm['detail']  = str_replace("&#33031;","��",$parm['detail'] );
		$parm['detail']  = str_replace("&#37390;","�_",$parm['detail'] );
		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
			$parm['detail'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['detail']);

		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
			$parm['item'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['item']);
	
					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl_ti (wi_id,
									item,
									sub_item,
									detail,
									ti_time,
									ti_new,
									date) VALUES('".
									$parm['wi_id']."','".
									$parm['item']."','".
									$parm['sub_item']."','".
									$parm['detail']."','".
									$parm['times']."','".
									$parm['ti_new']."','".
									$parm['today']."')";

//echo $q_str."<BR>";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("Successfully add Production Description�C") ;

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	Search  �Ͳ����� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $where_str="", $sort_str='item , id') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM smpl_ti ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition($sort_str);
		$srh->row_per_page = 50;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
		} 
if ($mode==1){    //������Search��
		if ($str = $argv['PHP_acc_code'] )  { 
			$srh->add_where_condition("acc_code LIKE  '%$str%'", "PHP_acc_code",$str,"Search Description code above: [ $str ]. "); }
		
		if ($str = $argv['PHP_acc_name'] )  { 
			$srh->add_where_condition("acc_name LIKE  '%$str%'", "PHP_acc_name",$str,"Search Description name above: [ $str ]. "); }

		if ($str = $argv['PHP_des'] )  { 
			$srh->add_where_condition("des LIKE '%$str%'", "PHP_des",$str,"Search Description context above: [ $str ] "); }

		if ($str = $argv['PHP_specify'] )  { 
			$srh->add_where_condition("specify LIKE '%$str%'", "PHP_specify",$str,"Search Description specifity above: [ $str ] "); }
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

		$op['ti'] = $result;  // ��ƿ� �ߤJ $op
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
#	->get($id=0, $wi_id=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $wi_id=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM smpl_ti WHERE id='$id' ";
		} elseif ($wi_id) {
			$q_str = "SELECT * FROM smpl_ti WHERE wi_id='$wi_id' ";
		} else {
			$this->msg->add("Error ! Please point description ID.");		    
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

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �Ͳ������O��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		#####   ��s��Ʈw���e
		$q_str = "UPDATE ti SET acc_name='"	.$parm['acc_name'].
							"', des='"			.$parm['des'].
							"', specify='"		.$parm['specify'].
							"', vendor1='"		.$parm['vendor1'].
							"', price1='"		.$parm['price1'].
							"', unit1='"		.$parm['unit1'].
							"', currency1='"	.$parm['currency1'].
							"', term1='"		.$parm['term1'].
							"', location1='"	.$parm['location1'].
							"', vendor2='"		.$parm['vendor2'].
							"', price2='"		.$parm['price2'].
							"', unit2='"		.$parm['unit2'].
							"', currency2='"	.$parm['currency2'].
							"', term2='"		.$parm['term2'].
							"', location2='"	.$parm['location2'].
							"', vendor3='"		.$parm['vendor3'].
							"', price3='"		.$parm['price3'].
							"', unit3='"		.$parm['unit3'].
							"', currency3='"	.$parm['currency3'].
							"', term3='"		.$parm['term3'].
							"', location2='"	.$parm['location2'].
						"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func

*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �Ͳ����� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$parm['field_value'] = str_replace("'","\'",$parm['field_value']);
		$parm['field_value']  = str_replace("&#33031;","��",$parm['field_value'] );
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
			$parm['field_value'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['field_value']);

		$q_str = "UPDATE smpl_ti SET ".$parm['field_name']."='".$parm['field_value']."',date='".date('Y-m-d')."',ti_time='".date('H:i:s')."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R�� �Ͳ������O�� 
#					[mode=0: $id��qty��id ; $mode=1: $id��wi_id]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point description ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM smpl_ti WHERE wi_id='$id' ";
		}else{
			$q_str = "DELETE FROM smpl_ti WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R�� �Ͳ������O�� 
#					[mode=0: $id��qty��id ; $mode=1: $id��wi_id]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_group($id,$sub_item) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point description ID.");		    
			return false;
		}
		$q_str = "DELETE FROM smpl_ti WHERE wi_id='$id' AND sub_item='$sub_item' ";
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
		$q_str = "SELECT ".$n_field." FROM smpl_ti ".$where_str;

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
#	->del_wi_id($id) �R�����w��ti->wi_id�O��
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_wi_id($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point description ID.");		    
			return false;
		}
		$q_str = "DELETE FROM smpl_ti WHERE wi_id='$id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>