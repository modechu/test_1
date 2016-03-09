<?php 

#++++++++++++++++++++++ ORDER_LOG  class ##### �}�o����  +++++++++++++++++++++++++++++++++++
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

class ORDER_LOG {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Database can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s sales_log �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm,$item='') {
					
		$sql = $this->sql;

		$parm['status'] = 0;
		if($item == 'user_log')$parm['status'] = 1;
	
		if ($item!='revise')	$parm['des'] = str_replace("'","\'",$parm['des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['des']);
		}
//		$parm['des'] = str_replace("�\\","�\\\\",$parm['des']);
//		$parm['des'] = str_replace("�\\","�\\\\",$parm['des']);
//		$parm['des'] = str_replace("�\\","�\\\\",$parm['des']);		
//		$parm['des'] = str_replace("�\\","�\\\\",$parm['des']);
					# �[�J��Ʈw
		$q_str = "INSERT INTO order_log (order_num,
									user,
									status,
									k_time,
									des) VALUES('".
									$parm['order_num']."','".
									$parm['user']."','".
									$parm['status']."',									
									NOW(),'".
									$parm['des']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't upload sales_log record.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id


		return $pdt_id;

	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s sales_log �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_multi_ord($parm) {
					
		$sql = $this->sql;



		$parm['des'] = str_replace("'","\'",$parm['des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['des']);
		}
					# �[�J��Ʈw
		$q_str = "INSERT INTO ord_multi_log (ord_num,
									item,	
									user,
									k_date,
									des) VALUES('".
									$parm['order_num']."','".
									$parm['item']."','".
									$parm['user']."','".
									date('Y-m-d')."','".
									$parm['des']."')";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't upload sales_log record.");
			$this->msg->merge($sql->msg);
			return false;    
		}




		$pdt_id = $sql->insert_id();  //���X �s�� id


		return $pdt_id;

	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search50($mode=0,$where_str="")	�j�M ���� ���	
#		�ȧ���̪񪺤��Q�����  �ƧǨ̮ɶ�
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search50($order_num) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM order_log WHERE order_num='".$order_num."' ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("status DESC, id DESC ");
		$srh->row_per_page = 50;


		$result= $srh->send_query2(50);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['records'] = '';
			} else {
				$op['records'] = 1;
			}

		for ($i=0; $i<sizeof($result); $i++)
		{
			$tmp_user=$GLOBALS['user']->get(0,$result[$i]['user']);
			if ($tmp_user['name'])$result[$i]['user'] = $tmp_user['name'];			
		}

		$op['order_log'] = $result;  // ��ƿ� �ߤJ $op


		
		return $op;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search50($mode=0,$where_str="")	�j�M ���� ���	
#		�ȧ���̪񪺤��Q�����  �ƧǨ̮ɶ�
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_multi($order_num,$item) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM ord_multi_log WHERE ord_num='".$order_num."' AND item='".$item."'";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("id DESC ");
		$srh->row_per_page = 50;


		$result= $srh->send_query2(50);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['records'] = '';
			} else {
				$op['records'] = 1;
			}

		for ($i=0; $i<sizeof($result); $i++)
		{
			$tmp_user=$GLOBALS['user']->get(0,$result[$i]['user']);
			if ($tmp_user['name'])$result[$i]['user'] = $tmp_user['name'];			
		}

	


		
		return $result;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R��   [��ID]�R��
#							$mode=0: $id= �O����id; $mode<>0: $id=ORDER_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! please specify order number.");		    
			return false;
		}
		$q_str = "DELETE FROM order_log WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �Ʈ� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE order_log SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func




} // end class


?>