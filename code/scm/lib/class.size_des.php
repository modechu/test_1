<?php 

#++++++++++++++++++++++ SIZE_DES  class ++++  �ؽX���O +++++++++++++++++++++++++++++++++++
#	->init($sql)				�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)				�[�J
#	->search($mode=0)			�j�M   
#	->get($id=0, size_type=0, $where_str="")	��X���w�O������� RETURN $row[]
#	->edit($parm)				��s ���
#	->update_field($parm)		��s ��Ƥ� �Y�ӳ�@���
#	->del($id)					�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� SIZE TYPE �� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SIZE_DES {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Can't connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s�ؽX ���
#		�[�J�s�ؽX���			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### �ˬd��J����					
		if (!$parm['cust'] = trim($parm['cust'])) {
			$this->msg->add("Error ! Please input customer name !");
		    return false;
		}

		if (!$parm['check']) {
			$this->msg->add("Error ! Please input currect base size !");
		    return false;
		}

					# �[�J��Ʈw
		$q_str = "INSERT INTO size_des (cust,
										dept,
										
										size,
										base_size,
										size_scale) VALUES ('".
										$parm['cust']."','".
										$parm['dept']."','".
									//	$parm['size_type']."','".
										$parm['size']."','".
										$parm['base_size']."','".
										$parm['size_scale']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$size_type_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("Successfully add customer:[".$parm['cust']."] size record�C") ;
		return $size_type_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	�j�M �ؽX���O	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM size_des WHERE (size_scale <>'' && size_scale IS NOT NULL)".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("cust ");
#####2006.11.14 �H�Ʀr������ܭ��X star
		$srh->row_per_page = 12;
		$pagesize=10;

		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	}     	
#####2006.11.14 �H�Ʀr������ܭ��X end
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['size_des'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

#####2006.11.14�s���X�ݭn��oup_put	start
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];	
#####2006.11.14�s���X�ݭn��oup_put	end
		return $op;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function wi_search($mode=0, $where_str='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM size_des WHERE (size_scale <>'' && size_scale IS NOT NULL)".$where_str;

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
	}
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['size_des'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
if(!$limit_entries){ 
##--*****--2006.11.16���X�s�W start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16���X�s�W end
}	
		return $op;
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, size_type=0, $where_str="")	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $size_type=0, $where_str="") {
		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM size_des WHERE id='$id' ";
		} elseif ($size_type) {
			$q_str = "SELECT * FROM size_des WHERE size_type='$size_type' ";
		} elseif ($where_str) {
			$q_str = "SELECT * FROM size_des ".$where_str;
		} else {
			$this->msg->add("Error ! Please point size_des rule");		    
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
#	->edit($parm)		��s �ؽX���O ���
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {
		$sql = $this->sql;
		############### �ˬd��J����

					# �ˬd�O�_���o���ɮצs�b
		$q_str = "SELECT id FROM size_des WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! Can't find this record!");
			return false;    	    
		}


		if (!$parm['check']) {
			$this->msg->add("Error ! Please input currect base size !");
		    return false;
		}
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE size_des SET 
				size='".$parm['size']."', 
				base_size='".$parm['base_size']."',
				size_scale='".$parm['size_scale']
				."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �ؽX���O��Ƥ� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE size_des SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �ؽX���O���[��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point size_des ID.");		    
			return false;
		}
		$q_str = "DELETE FROM size_des WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� SIZE TYPE �� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM size_des ".$where_str;
		
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