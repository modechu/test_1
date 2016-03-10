<?php 

#++++++++++++++++++++++ ACC  class ##### �Ʈ�  +++++++++++++++++++++++++++++++++++
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

class ACC {
		
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> buy_get_no($hend,$n_field,$tables)	���s��ڰ��s��
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_no($hend,$n_field,$tables) {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT ". $n_field." FROM ".$tables." where ".$n_field. " like '%".$hend."%' order by ".$n_field." desc limit 1";
		if (!$q_result = $sql->query($q_str)) {		//�j�M�̫�@��
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//�p�G�S����ƪ���
			$buy_no = '1';
		
		}else{	//�N�̫�@�����Ʀr+1
			$long = strlen($hend);
			$buy_no = substr($row[$n_field],$long);	//�h�����Y
			$tmp=explode('-',$buy_no);
			$buy_no=$tmp[0].$tmp[1];
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//�b�Ʀr�e��0��F�|��Ʀr
		{
			$buy_no=$hend."000-00".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$hend."000-0".$buy_no;
		}else if(strlen($buy_no) == 3){
			$buy_no=$hend."000-".$buy_no;			
		}else if(strlen($buy_no) == 4){
			$buy_no=substr($buy_no,0,1)."-".substr($buy_no,1);
	    $buy_no=$hend."00".$buy_no;
	  }else if(strlen($buy_no) == 5){
	  	$buy_no=substr($buy_no,0,2)."-".substr($buy_no,2);
	  	$buy_no=$hend."0".$buy_no;
	  }else{
	  	$buy_no=substr($buy_no,0,3)."-".substr($buy_no,3);
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func
				
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �ƮưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	
			//  �令�j�g
//		$parm['acc_code'] = strtoupper($parm['acc_code']);	

//		if (!$parm['acc_code'] = trim($parm['acc_code'])) {
//			$this->msg->add("Error ! Please input Accessory code [short name] !");
//		    return false;
//		}
		
		if (!$parm['acc_name'] = trim($parm['acc_name'])) {
			$this->msg->add("Error ! Please input Accessory Name  !");
		    return false;
		}
/*
					# �ˬd�O�_������
		$q_str = "SELECT id FROM acc WHERE acc_code='".$parm['acc_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This accessory code is exist in database, please change another one or check again!");
			return false;    	    
		}
*/


		$parm['des'] 		 = str_replace("'","\'",$parm['des']);
		$parm['specify'] = str_replace("'","\'",$parm['specify']);

				
					# �[�J��Ʈw
		$q_str = "INSERT INTO acc (acc_code,
									acc_name,
									des,
									specify,
									mile_code,
									mile_name,
									arrange,
									vendor1,									
									price1,
									unit1,
									currency1,
									term1,									
									vendor2,									
									price2,
									unit2,
									currency2,
									term2,									
									vendor3,								
									price3,
									unit3,
									term3,
									currency3
									) VALUES('".
									$parm['acc_code']."','".
									$parm['acc_name']."','".
									$parm['des']."','".
									$parm['specify']."','".
									$parm['mile_code']."','".
									$parm['mile_name']."','".
									$parm['size_mk']."','".
									$parm['vendor1']."','".									
									$parm['price1']."','".
									$parm['unit1']."','".
									$parm['currency1']."','".
									$parm['term1']."','".									
									$parm['vendor2']."','".									
									$parm['price2']."','".
									$parm['unit2']."','".
									$parm['currency2']."','".
									$parm['term2']."','".								
									$parm['vendor3']."','".									
									$parm['price3']."','".
									$parm['unit3']."','".
									$parm['term3']."','".									
									$parm['currency3']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

//		$this->msg->add("���\ �s�W�ƮưO��: [".$parm['acc_code']."]�C") ;

		return $new_id;

	} // end func

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �ƮưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_parts($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	
			//  �令�j�g
		$parm['num'] = strtoupper($parm['num']);	

		if (!$parm['num'] = trim($parm['num'])) {
			$this->msg->add("Error ! Please input Accessory code [short name] !");
		    return false;
		}


					# �ˬd�O�_������
		$q_str = "SELECT id FROM acc WHERE acc_code='".$parm['num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This accessory code is exist in database, please change another one or check again!");
			return false;    	    
		}
				
					# �[�J��Ʈw
		$q_str = "INSERT INTO acc (acc_code,
									acc_name,
									
									unit1									
									) VALUES('".
									$parm['num']."','".
									$parm['name']."','".
														
									$parm['unit']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

//		$this->msg->add("���\ �s�W�ƮưO��: [".$parm['acc_code']."]�C") ;

		return $new_id;

	} // end func	
*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	Search  �Ʈ� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0) {

		$sql = $this->sql;
		if($mode == 2)
		{
			$argv = $_SESSION['sch_parm'];
		}else{
			$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		}			
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM acc ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		if(isset($argv['SCH_sort']))$SCH_sort =  ($argv['SCH_ud'] > 0) ? $argv['SCH_sort'] ."" : $argv['SCH_sort'] ." DESC";
		if(isset($argv['SCH_sort']))$srh->add_sort_condition($SCH_sort);
		
//2006.11.14 �H�Ʀr������ܭ��X star		
		$srh->row_per_page = 12;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 �H�Ʀr������ܭ��X end  
   

if ($mode > 0){    //������Search��
	
		$mesg = '';
		if ($str = $argv['PHP_acc_code'] )  { 
			$srh->add_where_condition("acc_code LIKE  '%$str%'", "PHP_acc_code",$str); 
			$mesg .=" Code :[$str].";
		}
		
		if ($str = $argv['PHP_acc_name'] )  { 
			$srh->add_where_condition("acc_name LIKE  '%$str%'", "PHP_acc_name",$str); 
			$mesg .=" Name :[$str].";
		}

		if ($str = $argv['PHP_des'] )  { 
			$srh->add_where_condition("des LIKE '%$str%'", "PHP_des",$str); 
			$mesg .=" Description :[$str].";
		
		}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
}
		#M11021503 �ק� List �Ƨ�
		$srh->add_sort_condition("`id` DESC ");
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
		
#####2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14�s���X�ݭn��oup_put	end
		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_cat($mode=0)	Search  �Ʈ� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cat($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM acc_category ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("acc_key");
		
//2006.11.14 �H�Ʀr������ܭ��X star		
		$srh->row_per_page = 12;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 �H�Ʀr������ܭ��X end  
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['acc_cat'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
//		$op['prev_no'] = $srh->prev_no;
//		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
//		$op['last_no'] = $srh->last_no;
//		$op['start_no'] = $srh->start_no;
//		$op['per_page'] = $srh->row_per_page;
		
#####2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14�s���X�ݭn��oup_put	end
		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $acc_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $acc_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM acc WHERE id='$id' ";
		} elseif ($acc_code) {
			$q_str = "SELECT * FROM acc WHERE acc_code='$acc_code' ";
		} else {
			$this->msg->add("Error ! Please point Accessory ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record!");
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
#	->edit($parm)		��s �ƮưO��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		
		
		#####   ��s��Ʈw���e
		
		$parm['des'] 		 = str_replace("'","\'",$parm['des']);
		$parm['specify'] = str_replace("'","\'",$parm['specify']);

		$q_str = "UPDATE acc SET acc_name='"	.$parm['acc_name'].
							"', des='"				.$parm['des'].
							"', specify='"		.$parm['specify'].
							"', mile_code='"	.$parm['mile_code'].
							"', mile_name='"	.$parm['mile_name'].
							
							"', arrange='"		.$parm['size_mk'].
							
							"', vendor1='"		.$parm['vendor1'].							
							"', price1='"			.$parm['price1'].
							"', unit1='"			.$parm['unit1'].
							"', currency1='"	.$parm['currency1'].
							"', term1='"			.$parm['term1'].							
							"', vendor2='"		.$parm['vendor2'].						
							"', price2='"			.$parm['price2'].
							"', unit2='"			.$parm['unit2'].
							"', currency2='"	.$parm['currency2'].
							"', term2='"			.$parm['term2'].							
							"', vendor3='"		.$parm['vendor3'].						
							"', price3='"			.$parm['price3'].
							"', unit3='"			.$parm['unit3'].
							"', currency3='"	.$parm['currency3'].
							"', term3='"			.$parm['term3'].
							"', last_user='"	.$parm['user'].							
							"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$acc=$this->get($parm['id']);
		$q_str= "Update smpl_acc_use SET acc_name = '".$parm['acc_name']."' WHERE acc_code = '".$acc['acc_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$q_str= "Update acc_use SET acc_name = '".$parm['acc_name']."' WHERE acc_code = '".$acc['acc_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
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
		$q_str = "UPDATE acc SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �Ʈ� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_code($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE acc SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE acc_code='".$parm['code']."'";
		// echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �ƮưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point Accessory ID.");		    
			return false;
		}
		$q_str = "DELETE FROM acc WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record !");
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
		$q_str = "SELECT ".$n_field." FROM acc ".$where_str;
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
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
#	-> get_acc_name() ���o�Ʈ����O(Accessory Name)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_acc_name() {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT name FROM acc_category";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			while ($row = $sql->fetch($q_result)) {
				if ($row[0]<>'other') $fields[] = $row[0];
			}
			$fields[] ='other';
		return $fields;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_key()	���o�Ʈ����O���N��
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_acc_key($name) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT acc_key FROM acc_category WHERE name ='$name'";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);
		return $row['acc_key'];
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_key()	���o�Ʈ����O���N��
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_max_key() {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT max(acc_key)as acc_key FROM acc_category ";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);
		return $row['acc_key'];
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> add_cat($parm)		�[�J�s �Ʈ����O�O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cat($parm) {
					
		$sql = $this->sql;
				
					# �[�J��Ʈw
		$q_str = "INSERT INTO acc_category (acc_key,
									name
									) VALUES('".
									$parm['acc_key']."','".
									$parm['name']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

		return $new_id;

	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cat($id=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cat($id=0) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM acc_category WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record!");
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
#	->update_field($parm)		��s �Ʈ� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_cat($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE acc_category SET name ='".$parm['name']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>