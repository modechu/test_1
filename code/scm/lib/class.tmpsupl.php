<?php 

#++++++++++++++++++++++ DEPT  class ##### �����O +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->get_field_value($dept_code,$field_name)	��dept_code ��X�Y���$field_name����
#	->update($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#	->get_smpl_serious($dept, $year_code)	�Ѧ~�פγ����O ���X smpl ���Ǹ�
#	->get_smpl_num($year_code, $cust): �Ѧ~�פγ����O ��X �˥��q�� ���Ǹ�  �A�s�J��Ʈw
#	->get_wis_num($dept_code, $year_code): �Ѧ~�פγ����O ��X �˥��s�y�O ���Ǹ�  �A�s�J��Ʈw
#	->get_fabric_serious($dept_code, $year_code,$kind):		�Ѧ~�פγ����O ��X smpl ���Ǹ�  �A�s�J��Ʈw
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class TMPSUPL {
		
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
#	->search($mode=0)	�j�M �����O���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		
//2006.11.15�ק�s�W��e�{�覡	start	
		$q_header = "SELECT * FROM supl3";
//2006.11.15�ק�s�W��e�{�覡    end
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("vndr_no");
		$srh->row_per_page = 12;
//2006.11.14 �H�Ʀr������ܭ��X star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 �H�Ʀr������ܭ��X end  
	 $srh->add_where_condition("supl_cat=''"); 
 if ($mode == 0){		
//		if ($str = $argv['PHP_vndr_no] )  { 
	//		$srh->add_where_condition("vndr_no = '$str'", "PHP_supl_cat",$str,"�j�M �����ӥN�� = [ ".$str." ] "); }
		if ($str = $argv['PHP_vndr_no'] )  { 
			$srh->add_where_condition("vndr_no LIKE '%$str%'", "PHP_vndr_no",$str,"�j�M�����ӥ��W�t��: [ $str ]���e "); }
		if ($str = $argv['PHP_supl_f_name'] )  { 
			$srh->add_where_condition("supl_f_name LIKE '%$str%'", "PHP_supl_f_name",$str,"�j�M�����ӥ��W�t��: [ $str ]���e "); }
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

		$op['sup'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
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
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, dept_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $dept_code=0) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM supl3 WHERE vndr_no='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
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
#	->get($id=0, dept_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function exchange($id="",$supl_cat="") {
		
		$str="''";
		$sql = $this->sql;
		$q_str = "SELECT * FROM supl3 WHERE id='$id' ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		
									# �ˬd�O�_������
		$row['supl_s_name'] = str_replace("'","\'",$row['supl_s_name']);
		$q_str = "SELECT id FROM supl WHERE supl_s_name='".$row['supl_s_name']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This supplier short name is exist, Please change another one or check again!");
			return false;    	    
		}
		
		if ($supl_cat)
		{
		for($i=0; $i < 34; $i++)
		{			
			
			if ($i > 0)
			{
				if ($i == 25) $row[$i]=$supl_cat;
				$row[$i] = str_replace("'","\'",$row[$i]);
				$str=$str.",'".$row[$i]."'";				
			}
		}
		$q_str="INSERT INTO `supl` VALUES (".$str.")";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$q_str = "UPDATE supl3 SET supl_cat='".$supl_cat."' WHERE id='".$id."'";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$this->msg->add("Successfully add supplier ".$row['supl_s_name']."to SCM system");
		return $row;
		}else{
			$this->msg->add("Please choice supplier category ");
			return true;		
		}
	} // end func






#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


} // end class


?>