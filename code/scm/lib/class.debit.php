<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### �q ��  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)				�[�J�s �q��O��  �Ǧ^ $id
#	->search($mode=0, $dept='',$limit_entries=0)			�j�M �q �� ���
#
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class DEBIT {
		
	var $sql;
	var $msg ;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Data base can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order')	
#
#		�P�ɧ�s��� field���� 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_2fields($field1, $field2, $value1, $value2, $id, $table='debit') {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
									"', ".$field2." ='".$value2.
								"' WHERE id=".	$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order')	
#
#		�P�ɧ�s��� field���� 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_fields($field1, $value1, $id, $table='debit') {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.									
								"' WHERE id=".	$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
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
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//�b�Ʀr�e��0��F�|��Ʀr
		{
			$buy_no=$hend."000".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$hend."00".$buy_no;
		}else if(strlen($buy_no) == 3){
			$buy_no=$hend."0".$buy_no;			
		}else{
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw(2007.03.02�[�J�ئT���)
		$q_str = "INSERT INTO debit (
								dept, dn_to, ord_num, dn_num, dn_des, dn_price, currency, chk_to, po_num, cust_po, oth_dept, dn_user, dn_date
		          ) VALUES('".
							$parm['dept']."','".
							$parm['dn_to']."','".
							$parm['ord_num']."','".
							$parm['dn_num']."','".
							$parm['dn_des']."','".
							$parm['price']."','".
							$parm['currency']."','".
							$parm['chk_to']."','".
							$parm['po_num']."','".
							$parm['cust_po']."','".
							
							$parm['oth_dept']."','".
							$parm['dn_user']."','".														
							$parm['dn_date']."')";
 		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;  
			 
		}


		$ord_id = $sql->insert_id();  //���X �s�� id
		

		$this->msg->add("append Debit Note On order#: [".$parm['ord_num']."]�C") ;	
		return $ord_id;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_comm($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw(2007.03.02�[�J�ئT���)
		$q_str = "INSERT INTO debit_comm (
								dn_id, comm_dept, comm
		          ) VALUES('".
							$parm['dn_id']."','".
							$parm['comm_dept']."','".
							$parm['comm']."')";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //���X �s�� id		

		return $ord_id;

	} // end func		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT debit.* 
								 FROM debit ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("debit.id DESC");
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

/*
	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("debit.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
	
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if  ($user_team == 'MD')	$srh->add_where_condition("debit.dept = '$user_dept'", "",$dept,"Dept=[ $user_dept ]. ");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $user_team <> 'MD' && $oth_exc == 0) 	$srh->add_where_condition("debit.dept = '$user_dept'", "",$dept,"Dept=[ $user_dept ]. ");
	}
*/
   if ($mode==1){
   	$mesg = '';		
		if ($str = $argv['SCH_to'] )  { 
			$srh->add_where_condition("debit.dn_to = '$str'", "SCH_to",$str); 
			$mesg.= "  Cust = [ $str ]. ";
		}
		if ($str = $argv['SCH_ord'] )  { 
			$srh->add_where_condition("debit.ord_num LIKE '%$str%'", "SCH_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
		}
			
		if ($mesg)
		{
			$msg = "Search : ".$mesg;
		
			$this->msg->add($msg);
		}		

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
		$op['dn'] = $result;  // ��ƿ� �ߤJ $op
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $dn_num=0) {
        $e_date=increceDaysInDate($GLOBALS['TODAY'],30);     
        
		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT debit.* FROM debit WHERE debit.id='$id'";

		} elseif ($dn_num) {
			$q_str = "SELECT debit.* FROM debit  WHERE debit.dn_num='$dn_num'";
		} else {
			$this->msg->add("Error ! please specify order number.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record for debit note!");
			return false;    
		}		
		$exc_id = $row['id'];
		
		if($row['chk_to'] == 0)
		{
			$tmp = $GLOBALS['supl']->get_fields('supl_s_name'," WHERE vndr_no='".$row['dn_to']."'");			
			$row['dn_to_name'] = $tmp[0];

			$tmp = $GLOBALS['supl']->get_fields('supl_f_name'," WHERE vndr_no='".$row['dn_to']."'");			
			$row['dn_to_f_name'] = $tmp[0];

		}else if($row['chk_to'] == 1){
			$tmp = $GLOBALS['cust']->get_fields('cust_init_name'," WHERE cust_s_name='".$row['dn_to']."'");
			$row['dn_to_name'] = $tmp[0];

			$tmp = $GLOBALS['cust']->get_fields('cust_f_name'," WHERE cust_s_name='".$row['dn_to']."'");
			$row['dn_to_f_name'] = $tmp[0];

		}else{
			$tmp = $GLOBALS['dept']->get_fields('dept_name'," WHERE dept_code='".$row['dn_to']."'");
			$row['dn_to_name'] = $tmp[0];
			$row['dn_to_f_name'] = $tmp[0];
		
		}
		$row['orders'] = explode(',',$row['ord_num']);
		$row['pa_num'] = "PA".substr($row['po_num'],2);
		
		$po_user=$GLOBALS['user']->get(0,$row['dn_user']);
		$row['dn_user_id'] = $row['dn_user'];
		if ($po_user['name'])$row['dn_user'] = $po_user['name'];
		$po_user=$GLOBALS['user']->get(0,$row['apv_user']);
		$row['apv_user_id'] = $row['apv_user'];
		if ($po_user['name'])$row['apv_user'] = $po_user['name'];
		
		$op['dn'] = $row;
		$op['dn']['dn_des'] = str_replace( chr(13).chr(10), "<br>", $op['dn']['dn_des'] );


	
		$comm_dept = array(	'HJ'	=>	'��żt',
												'LY'	=>	'�ߤ��t',
												'RD'	=>	'��o����',
												'PM'	=>	'�Ͳ�������',
												'KA'	=>	'�T���Ʒ~�BA��',
												'KB'	=>	'�T���Ʒ~�BB��'
												);


	

		$q_str = "SELECT debit_comm.* FROM debit_comm WHERE dn_id='$exc_id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		$com_mk = 1;
		while ($row = $sql->fetch($q_result)) {
			$op['comm'][$i] = $row;
			if($op['comm'][$i]['Comm'] == '')$com_mk = 0;
			$op['comm'][$i]['dept_name'] = $comm_dept[$row['comm_dept']];
			$op['comm'][$i]['Comm'] = str_replace( chr(13).chr(10), "<br>", $op['comm'][$i]['Comm'] );
			$po_user=$GLOBALS['user']->get(0,$op['comm'][$i]['comm_user']);
			$op['comm'][$i]['comm_user_id'] = $op['comm'][$i]['comm_user'];
			if ($po_user['name'])$op['comm'][$i]['comm_user'] = $po_user['name'];
			
			$i++;
		}				
		$op['com_mk'] = $com_mk;
	

		$q_str = "SELECT debit_log.* FROM debit_log WHERE dn_id='$exc_id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		
		while ($row = $sql->fetch($q_result)) {
			$op['logs'][$i] = $row;			
			$i++;
		}		
	
		return $op;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		��s �q�� �O�� 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm, $mode=0) {

		$sql = $this->sql;		
		
		if ($mode == 0){			
		#####   ��s��Ʈw���e
			$q_str = "UPDATE debit SET ".
							"	  dn_des='"		  .$parm['dn_des'].
							"',	oth_dept='"		.$parm['oth_dept'].
							"',	currency='"		.$parm['currency'].
							"',	cust_po='"		.$parm['cust_po'].
							"',	dn_price='"		.$parm['dn_price'].
							"'  WHERE id='"		.$parm['id']."'";

		} elseif($mode ==1){      // --- order revise -----

		}


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return true;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		��s �q�� �O�� 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_comm($parm) {

		$sql = $this->sql;		
		
		$q_str = "UPDATE debit_comm SET ".
						 "	  Comm='"	.$parm['Comm'].
						 "',  comm_date='"	.$parm['comm_date'].
						 "',  comm_user='"	.$parm['comm_user'].
						 "'  WHERE id='"	.$parm['id']."'";

//echo $q_str;
			
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_cfm($parm)		 �q��e�X �ݽT�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function send_submit($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE debit SET status =	2".
							",  sub_user='"	.$parm['submit_user'].
							"', sub_date='"	 	.$parm['submit_date'].
							"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_cfm($parm)		 �q��e�X �ݽT�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function send_apv($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE debit SET status =	4".
							",  apv_user='"	.$parm['submit_user'].
							"', apv_date='"	 	.$parm['submit_date'].
							"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($order_num=0)	�T�{�q��O�_�s�b
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($order_num) {
        $e_date=increceDaysInDate($GLOBALS['TODAY'],30);     
        
		$sql = $this->sql;

			$q_str = "SELECT id FROM exceptional 
								WHERE exceptional.ord_num ='".$order_num."'";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}		
		return $row['id'];
	}
	




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_unord($fty, $year,  $cat='capacity')	��X���w�O�������֥i�q��SU��� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_comm_field($field, $where_str) {

		$sql = $this->sql;
		$total = 0;		
		
		
		
			$q_str = "SELECT $field FROM debit_comm WHERE $where_str";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$row = $sql->fetch($q_result);



		
		return $row[0];
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		�R���@����ʩ���
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function comm_del($where_str) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM debit_comm WHERE $where_str";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     
    
	return true;


	}// end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_apv($limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT debit.* FROM debit ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("debit.id DESC");
		$srh->row_per_page = 10;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;

		if (isset($argv['PHP_sr_dn']) && $argv['PHP_sr_dn']) {
			$pages = $srh->get_page($argv['PHP_sr_dn'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
	}


		$srh->add_where_condition("debit.status = 2"); 		

		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['dn'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		
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
#	->add($parm)		�[�J�s sales_log �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_log($parm,$item='') {
					
		$sql = $this->sql;
		$today = date('Y-m-d');
					# �[�J��Ʈw
		$q_str = "INSERT INTO debit_log (dn_id,
									user,
									k_date,
									des) VALUES('".
									$parm['dn_id']."','".
									$parm['user']."',
									'$today','".
									$parm['des']."')";
					
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't upload sales_log record.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id


		return $pdt_id;

	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		�R���@����ʩ���
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM debit WHERE id =".$id;
	 	$q_result = $sql->query($q_str);   


	 	$q_str="DELETE FROM debit_comm WHERE dn_id =".$id;
	 	$q_result = $sql->query($q_str);     

	 	$q_str="DELETE FROM debit_log WHERE dn_id =".$id;
	 	$q_result = $sql->query($q_str);
	 	     
	return true;


	}// end func	




} // end class


?>