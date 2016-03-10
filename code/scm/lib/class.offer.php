<?php 

#++++++++++++++++++++++ OFFER  class ##### �˥������u�@��++++++++++++++++++++++
#	->init($sql)					�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->check($parm, $mode=0)			�ˬd �[ �O�� �O�_���T
#	->add($parm)					�[�J
#	->search($mode=0,$where_str="",$limit_entries=0)	�j�M  ���	 
#	->get_new_number($sr_key)		��X�s���X
#	->get($id=0, $num=0)			��X���w�O������� RETURN $row[]
#	->edit($parm)					��s ���
#	->update_field($id, $field_name, $value)��s ��� �Y�ӳ�@�����
#	->del($id)						�R�� ��ƿ�
#	->get_fields($n_field,$where_str="") ���X���� $n_field �m�Jarry return
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class OFFER {
		
	var $sql;
	var $msg ;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! cannot contact to database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm, $mode=0)		�ˬd �[�J�O�� �O�_���T
#						ode =0:�@��add��check,  mode=1: edit�ɪ�check
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm, $mode=0) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

		$parm['des'] = str_replace("'","\'",$parm['des']);
		$parm['fab'] = str_replace("'","\'",$parm['fab']);
		$parm['fab_item'] = str_replace("'","\'",$parm['fab_item']);
		$parm['acc_item'] = str_replace("'","\'",$parm['acc_item']);
		$parm['other_item'] = str_replace("'","\'",$parm['other_item']);
		$ie = $parm['spt']/$GLOBALS['IE_TIME'];

//�d�̫߳᪩�� 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['buyer']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	


		$q_str = "INSERT INTO offer (num,
									dept,
									k_date,
									buyer,
									cust_ver,
									agent,
									merchan,
									style,
									des,
									fab,
									cm,
									
									comm,
									fty,
									smpl_ord,
									
									tech,
									remark,
									cons,
									spt,
									ie,
									
									tt_cost,
									gm_rate,
									fab_item,
									fab_usage,
									fab_price,
									fab_yy,
									
									inter_item,
									inter_usage,
									inter_price,
									inter_yy,

									acc_item,
									acc_usage,
									acc_price,
									acc_yy,
									other_item,
									other_usage,
									other_price,									
									other_yy) VALUES('".
									$parm['num']."','".
									$parm['dept']."','".
									$parm['k_date']."','".
									$parm['buyer']."','".
									$cust_row['ver']."','".									
									$parm['agent']."','".
									$parm['merchan']."','".
									$parm['style']."','".
									$parm['des']."','".
									$parm['fab']."','".
									$parm['cm']."','".

									$parm['comm']."','".
									$parm['fty']."','".
									$parm['smpl_ord']."','".


									$parm['tech']."','".
									$parm['remark']."','".
									$parm['cons']."','".
									$parm['spt']."','".
									$ie."','".
									
									$parm['tt_cost']."','".
									$parm['gm_rate']."','".
									$parm['fab_item']."','".
									$parm['fab_usage']."','".
									$parm['fab_price']."','".
									$parm['fab_yy']."','".
									$parm['inter_item']."','".
									$parm['inter_usage']."','".
									$parm['inter_price']."','".
									$parm['inter_yy']."','".
									$parm['acc_item']."','".
									$parm['acc_usage']."','".
									$parm['acc_price']."','".
									$parm['acc_yy']."','".
									$parm['other_item']."','".
									$parm['other_usage']."','".
									$parm['other_price']."','".
									$parm['other_yy']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$new_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("Add OFFER record: [".$parm['num']."]�C") ;

		
		// ���� �B�z ---------------(�H num �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/offer/";
		// 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
		if($parm['pic_upload'])  {   
			# �W�ǹϬ۳B�z 600X600
			$upFile->setSaveTo($style_dir,$parm['num'].".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);

				if ($up_result){
					$this->msg->add("done upload picture");
				} else {
					$this->msg->add("fail to upload picture");
				}
		}

		return $new_id;

	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_offer($num) {
					
		$sql = $this->sql;

		$q_str = "INSERT INTO offer (num,k_date) VALUES('".$num."','".date('Y-m-d')."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$new_id = $sql->insert_id();  //���X �s�� id
		return $new_id;

	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_log($parm) {
					
		$sql = $this->sql;

		$parm['comment'] = str_replace("'","\'",$parm['comment']);
		$parm['status'] = str_replace("'","\'",$parm['status']);

		$q_str = "INSERT INTO offer_log (of_num,
									subject,
									comment,
									status,
									c_date,
									c_user,
									s_date,							
									s_user) VALUES('".
									$parm['of_num']."','".
									$parm['subject']."','".
									$parm['comment']."','".
									$parm['status']."','".
									$parm['c_date']."','".
									$parm['c_user']."','".
									$parm['s_date']."','".
									$parm['s_user']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$new_id = $sql->insert_id();  //���X �s�� id
		return $new_id;

	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="",$limit_entries=0)	�j�M  ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="",$limit_entries=0) {
		
		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT offer.*, cust.cust_init_name as cust_ini FROM offer, cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("offer.".$argv['PHP_sourcing']);
//		$srh->add_sort_condition("offer.id");
		$srh->row_per_page = 20;

#--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end  
if ($mode==1){
		if ($str = $argv['PHP_style'] )  { 
			$srh->add_where_condition("offer.style LIKE '%$str%'", "PHP_style",$str,"style like:[ $str ] "); }
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("offer.num LIKE '%$str%'", "PHP_num",$str,"number content:[ $str ] "); }
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("offer.buyer = '$str'", "PHP_cust",$str,"buyer = [ $str ]. "); }
		if ($str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("offer.dept = '$str'", "PHP_dept_code",$str,"dept = [ $str ]. "); }
		if ($str = $argv['SCH_agt'] )  { 
			$srh->add_where_condition("offer.agent like '%$str%'", "PHP_dept_code",$str,"agent = [ $str ]. "); }
		if ($str = $argv['SCH_str'] )  { 
			$srh->add_where_condition("offer.k_date >= '$str'", "PHP_dept_code",$str,"Date >= [ $str ]. "); }
		if ($str = $argv['SCH_end'] )  { 
			$srh->add_where_condition("offer.k_date <= '$str'", "SCH_end",$str,"Date <= [ $str ]. "); }
		if ($str = $argv['SCH_style_type'] )  { 
			$srh->add_where_condition("offer.style_type = '$str'", "SCH_style_type",$str,"Date <= [ $str ]. "); }

}	
		$srh->add_where_condition("offer.new_mk = '0'");
		$srh->add_where_condition("offer.buyer = cust.cust_s_name AND cust.ver = offer.cust_ver");
		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'CU') 
		{
			$srh->add_where_condition("offer.status = '3'");
			$srh->add_where_condition("offer.buyer = '".$GLOBALS['SCACHE']['ADMIN']['team_id']."' OR offer.agent='".$GLOBALS['SCACHE']['ADMIN']['team_id']."'");
			
		}
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		if(!($GLOBALS['SCACHE']['ADMIN']['dept'] == 'SA'))
		{
			if(!(($user_dept == 'KA' || $user_dept == 'KB' || $user_dept == 'GM') && $user_team == 'SU'))
			{
				$srh->add_where_condition("offer.status = '0' OR offer.status = '5'");
			}
		}
		
		$result= $srh->send_query2($limit_entries);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['of'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['rows_this_page'] = $srh->rows_this_page;
		
#--*****--##2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
#--*****--##2006.11.14�s���X�ݭn��oup_put	end		

		return $op;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="",$limit_entries=0)	�j�M  ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_un_apv($mode=0,$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT offer.*, cust.cust_init_name as cust_ini FROM offer, cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("offer.id DESC ");
		$srh->row_per_page = 20;

#--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
		$pagesize=10;
		if (isset($argv['PHP_sr_of']) && $argv['PHP_sr_of']) {
			$pages = $srh->get_page($argv['PHP_sr_of'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end  

		$srh->add_where_condition("offer.new_mk = '0'");
		$srh->add_where_condition("offer.status = '1'");
		$srh->add_where_condition("offer.buyer = cust.cust_s_name AND cust.ver = offer.cust_ver");

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['of'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['rows_this_page'] = $srh->rows_this_page;
		
#--*****--##2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#--*****--##2006.11.14�s���X�ݭn��oup_put	end		
		return $op;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_new_number($sr_key)	��X�s���X
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_new_number($sr_key) {

		$sql = $this->sql;

		$q_str = "SELECT num FROM offer where num like '%".$sr_key."%' order by num desc limit 1";
		if (!$q_result = $sql->query($q_str)) {		//�j�M�̫�@��
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//�p�G�S����ƪ���
			$buy_no = '1';
		
		}else{	//�N�̫�@�����Ʀr+1
			$long = strlen($sr_key);
			$buy_no = substr($row['num'],$long);	//�h�����Y
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//�b�Ʀr�e��0��F�|��Ʀr
		{
			$buy_no=$sr_key."00".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$sr_key."0".$buy_no;
		}else{
			$buy_no=$sr_key.$buy_no;
		}		
		return $buy_no;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $num=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $num=0, $ver=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM offer WHERE id='$id' ";
		} elseif ($num) {
			$num=trim($num);
			$q_str = "SELECT * FROM offer WHERE `num` LIKE '%$num%' AND `ver` = $ver ";			
		} else {
			$this->msg->add("Error ! please specified record ID.");		    
			return false;
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! cannot find record!");
			return false;    
		}

	$po_user=$GLOBALS['user']->get(0,$row['merchan']);
	$row['merchan_id'] = $row['merchan'];
	if ($po_user['name'])$row['merchan'] = $po_user['name'];

		
		return $row;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $num=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_log($id=0) {

		$sql = $this->sql;
		$log = array();
		$q_str = "SELECT * FROM offer_log WHERE of_num='$id' ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		While ($row = $sql->fetch($q_result)) {
			
			$po_user=$GLOBALS['user']->get(0,$row['c_user']);
			if ($po_user['name'])$row['c_user'] = $po_user['name'];
			$po_user=$GLOBALS['user']->get(0,$row['s_user']);
			if ($po_user['name'])$row['s_user'] = $po_user['name'];

			$log[] = $row;
		}
		return $log;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �O��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise($parm) {

		$sql = $this->sql;
		
		$q_str = "SELECT * FROM offer WHERE id=' ".$parm['id']."' ";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! cannot find record!");
			return false;    
		}
		$str="INSERT INTO offer SET ";
		foreach ($row as $key => $value)
		{						
			if (!is_int($key) && $key != 'id' )
			{
				$value = str_replace("'","\'",$value);

				$str=$str.$key." = '".$value."' , ";
			}
		}
		$str=substr($str,0,-2);

		if (!$q_result = $sql->query($str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$new_id = $sql->insert_id();  //���X �s�� id



	$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];


		if($my_dept == "RD"){
			$ie = $parm['spt']/$GLOBALS['IE_TIME'];
			$parm['tech'] = str_replace("'","\'",$parm['tech']);

			$q_str = "UPDATE offer SET cons='"		.$parm['cons'].
						"', spt="		.$parm['spt'].
						", ie="			.$ie.
						", tech='"		.$parm['tech'].
						"', remark='"	.$parm['remark'].
						"', ver='"		.$parm['ver'].
						"', k_date='"	.$parm['k_date'].
						"', merchan='"		.$parm['merchan'].
						"', status='0"		.
						
						"'  WHERE id='"	.$new_id."'";

		}else{
			$ie = $parm['spt']/$GLOBALS['IE_TIME'];
			$parm['tech'] = str_replace("'","\'",$parm['tech']);
			$parm['des'] = str_replace("'","\'",$parm['des']);
			$parm['fab'] = str_replace("'","\'",$parm['fab']);
			$parm['cons'] = str_replace("'","\'",$parm['cons']);

			$q_str = "UPDATE offer SET style='"		.$parm['style'].
						"',	des='"				.$parm['des'].
						"', fab='"				.$parm['fab'].
						"', cons='"				.$parm['cons'].
						"', spt='"				.$parm['spt'].
						"', ie='"					.$ie.
						"', tech='"				.$parm['tech'].
						"', smpl_ord='"		.$parm['smpl_ord'].


						"', fty='"				.$parm['fty'].
						"', agent='"			.$parm['agent'].
						"', comm='"				.$parm['comm'].
						"', quota='"			.$parm['quota'].

						"', tech='"				.$parm['tech'].
						"', ver='"				.$parm['ver'].
						"', k_date='"			.$parm['k_date'].
 						"', merchan='"		.$parm['merchan'].
 
 						"', cm='"			.$parm['cm'].
						"', tt_cost='"		.$parm['tt_cost'].
						"', deal='"				.$parm['deal'].
						"', style_type='"	.$parm['style_type'].
						"', gm_rate='"		.$parm['gm_rate'].
						"', status='0"		.

 						
						"'  WHERE id='"	.$new_id."'";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$q_str = "UPDATE offer SET new_mk = 1 WHERE id=' ".$parm['id']."' ";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $new_id;

		// ���� �B�z ---------------(�H num �ӳ]�����ɦW
		if($my_dept != "RD"){  //���O RD�����H����.....
				$upFile = new uploadIMG();
				$style_dir	= $GLOBALS['config']['root_dir']."/offer/";  
			if($parm['pic_upload'])  {  
				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
				if(file_exists($style_dir.$parm['num'].".jpg")){
					unlink($style_dir.$parm['num'].".jpg") or die("cannot delete old pic file:".$parm['num'].".jpg"); 
				}
				# �W�ǹϬ۳B�z 600X600
				$upFile->setSaveTo($style_dir,$parm['num'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);

				if ($up_result){
					$this->msg->add("done upload picture");
				} else {
					$this->msg->add("fail to upload picture");
				}
			}
		

		}

		
		return $pdt_id;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �O��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		if($my_dept == "RD"){
			$ie = $parm['spt']/$GLOBALS['IE_TIME'];
			$parm['tech'] = str_replace("'","\'",$parm['tech']);
			$parm['remark'] = str_replace("'","\'",$parm['remark']);

			$q_str = "UPDATE offer SET cons='"		.$parm['cons'].
						"', spt="				.$parm['spt'].
						", ie="					.$ie.
						", tech='"			.$parm['tech'].
						"', remark='"		.$parm['remark'].

						"'  WHERE id='"	.$parm['id']."'";

		}else{
			$ie = $parm['spt']/$GLOBALS['IE_TIME'];
			$parm['tech'] = str_replace("'","\'",$parm['tech']);
			$parm['des'] = str_replace("'","\'",$parm['des']);
			$parm['fab'] = str_replace("'","\'",$parm['fab']);
			$parm['cons'] = str_replace("'","\'",$parm['cons']);

//�d�̫߳᪩�� 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['buyer']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	


			$q_str = "UPDATE offer SET style='"		.$parm['style'].
						"',	dept='"					.$parm['dept'].
						"', buyer='"				.$parm['buyer'].
						"', cust_ver='"			.$cust_row['ver'].
						"', merchan='"			.$parm['merchan'].
						"', smpl_ord='"			.$parm['smpl_ord'].

						"',	des='"					.$parm['des'].
						"', fab='"					.$parm['fab'].
						"', cons='"					.$parm['cons'].
						"', spt='"					.$parm['spt'].
						"', ie='"						.$ie.
						"', fty='"					.$parm['fty'].
						"', agent='"				.$parm['agent'].
						"', comm='"					.$parm['comm'].
						"', quota='"				.$parm['quota'].
						"', cm='"						.$parm['cm'].
						"', tt_cost='"			.$parm['tt_cost'].
						"', gm_rate='"			.$parm['gm_rate'].
						"', deal='"					.$parm['deal'].
						"', style_type='"		.$parm['style_type'].
						"', tech='"					.$parm['tech'].
						"'  WHERE id='"			.$parm['id']."'";
		}
/*		
		else{
			$parm['des'] = str_replace("'","\'",$parm['des']);
			$parm['fab'] = str_replace("'","\'",$parm['fab']);
			$parm['fab_item'] = str_replace("'","\'",$parm['fab_item']);
			$parm['acc_item'] = str_replace("'","\'",$parm['acc_item']);
			$parm['other_item'] = str_replace("'","\'",$parm['other_item']);

			$q_str = "UPDATE offer SET style='"		.$parm['style'].
						"',	des='"			.$parm['des'].
						"', fab='"			.$parm['fab'].
						
						"', fab_item='"		.$parm['fab_item'].
						"', fab_price='"	.$parm['fab_price'].
						"', fab_yy='"		.$parm['fab_yy'].

						"', acc_item='"		.$parm['acc_item'].
						"', acc_price='"	.$parm['acc_price'].
						"', acc_yy='"		.$parm['acc_yy'].

						"', other_item='"	.$parm['other_item'].
						"', other_price='"	.$parm['other_price'].
						"', other_yy='"		.$parm['other_yy'].

						"', cm='"			.$parm['cm'].
						"', tt_cost='"		.$parm['tt_cost'].
						"', gm_rate='"		.$parm['gm_rate'].


						"'  WHERE id='"	.$parm['id']."'";
		}
*/
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		// ���� �B�z ---------------(�H num �ӳ]�����ɦW
		if($my_dept != "RD"){  //���O RD�����H����.....
				$upFile = new uploadIMG();
				$style_dir	= $GLOBALS['config']['root_dir']."/offer/";  
			if($parm['pic_upload'])  {  
				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
				if(file_exists($style_dir.$parm['num'].".jpg")){
					unlink($style_dir.$parm['num'].".jpg") or die("cannot delete old pic file:".$parm['num'].".jpg"); 
				}
				# �W�ǹϬ۳B�z 600X600
				$upFile->setSaveTo($style_dir,$parm['num'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);

				if ($up_result){
					$this->msg->add("done upload picture");
				} else {
					$this->msg->add("fail to upload picture");
				}
			}
		

		}

		
		return $pdt_id;
	} // end func





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �O��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_det($parm,$group) {

		$sql = $this->sql;
		$parm['item'] = str_replace("'","\'",$parm['item']);
		$parm['usage'] = str_replace("'","\'",$parm['usage']);

			$q_str = "UPDATE offer SET ".$group."_item='"		.$parm['item'].
						"', ".$group."_usage='"	.$parm['usage'].
						"', ".$group."_price='"	.$parm['price'].
						"', ".$group."_yy='"		.$parm['yy'].
						"'  WHERE id='"	.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];		
		return $pdt_id;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �O��
#			mode :1-->comment    mode : 2 --->sataus
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_log($parm,$mode) {

		$sql = $this->sql;
		if($mode == 1){
			$parm['comment'] = str_replace("'","\'",$parm['comment']);

			$q_str = "UPDATE offer_log SET comment='"		.$parm['comment'].
						"', c_date='"		.$parm['c_date'].
						"', c_user='"		.$parm['c_user'].
						"'  WHERE id='"	.$parm['id']."'";

		}
		if($mode == 2){
			$parm['status'] = str_replace("'","\'",$parm['status']);
			$q_str = "UPDATE offer_log SET status='"		.$parm['status'].
						"', s_date='"		.$parm['s_date'].
						"', s_user='"		.$parm['s_user'].
						"'  WHERE id='"	.$parm['id']."'";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		return $pdt_id;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($id, $field_name, $value)��s ��� �Y�ӳ�@�����
#							$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($id, $field_name, $value) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE offer SET ".$field_name."='".$value."'  WHERE id='".$id."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update OFFER data table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
	

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� ���ưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� �}�o���� ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM fabric WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

			// �Ϫ��R�� -------
			$style_dir	= $GLOBALS['config']['root_dir']."/fabric/";  

			if(file_exists($style_dir.$id.".jpg")){
				unlink($style_dir.$id.".jpg") or die("�L�k�R������:".$id.".jpg");  // �R������
			}
//			if(file_exists($style_dir."s".$id.".jpg")){
//				unlink ($style_dir."s".$id.".jpg") or die("�L�k�R������:"."s".$id.".jpg");  // �R������
//			}




		return true;
	} // end func
*/

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="") ���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM offer ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! cannot access database!");
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
#	->get_fields($n_field,$where_str="") ���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_field($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM offer ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);
		
		return $row[0];
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="") ���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function create_items($offer) {
		$offer['cos']= explode('|',$offer['cons']);
		$cos = '';
		if (isset($offer['cos'][0]) && $offer['cos'][0]) $cos .= "yy : ".$offer['cos'][0]." yd , ";
		if (isset($offer['cos'][1]) && $offer['cos'][1]) $cos .= "base on offer size : ".$offer['cos'][1].";  ";
		if (isset($offer['cos'][2]) && $offer['cos'][2]) $cos .= "offer size range : ".$offer['cos'][2].";  ";
		$offer['cons'] = $cos;		
		$fab_item = explode('|',$offer['fab_item']);
		$fab_usage = explode('|',$offer['fab_usage']);
		$fab_price = explode('|',$offer['fab_price']);
		$fab_yy = explode('|',$offer['fab_yy']);

		$inter_item = explode('|',$offer['inter_item']);
		$inter_usage = explode('|',$offer['inter_usage']);
		$inter_price = explode('|',$offer['inter_price']);
		$inter_yy = explode('|',$offer['inter_yy']);

		$fus_item = explode('|',$offer['fus_item']);
		$fus_usage = explode('|',$offer['fus_usage']);
		$fus_price = explode('|',$offer['fus_price']);
		$fus_yy = explode('|',$offer['fus_yy']);


		$acc_item = explode('|',$offer['acc_item']);
		$acc_usage = explode('|',$offer['acc_usage']);
		$acc_price = explode('|',$offer['acc_price']);
		$acc_yy = explode('|',$offer['acc_yy']);
		$other_item = explode('|',$offer['other_item']);
		$other_usage = explode('|',$offer['other_usage']);
		$other_price = explode('|',$offer['other_price']);
		$other_yy = explode('|',$offer['other_yy']);
		$total_cost=0;
		$j = 0;
		$z=0;
		$offer['fab_cat']= array();
	for ($i =0; $i< sizeof($fab_item); $i++)
		{
			if(!isset($fab_usage[$i]))$fab_usage[$i] = '';
			
			if($fab_usage[$i] || $fab_item[$i])
			{			
				$offer['fab_cat'][$i]['i']=$z;
				$offer['fab_cat'][$i]['j']=$i+1;
				$offer['fab_cat'][$i]['fi']=$fab_item[$i];
				$offer['fab_cat'][$i]['fu']=$fab_usage[$i];
				$offer['fab_cat'][$i]['fp']=$fab_price[$i];
				$offer['fab_cat'][$i]['fq']=$fab_yy[$i];
				$offer['fab_cat'][$i]['fc']=$fab_price[$i]*$fab_yy[$i];
				$total_cost += $offer['fab_cat'][$i]['fc'];
				$z++;
			}
		}
		$z=0;
		$offer['inter_cat'] = array();
		for ($i =0; $i< sizeof($inter_item); $i++)
		{
			if($inter_usage[$i] || $inter_item[$i])
			{
				$offer['inter_cat'][$i]['i']=$z;
				$offer['inter_cat'][$i]['j']=$i+1;
				$offer['inter_cat'][$i]['ii']=$inter_item[$i];
				$offer['inter_cat'][$i]['iu']=$inter_usage[$i];
				$offer['inter_cat'][$i]['ip']=$inter_price[$i];
				$offer['inter_cat'][$i]['iq']=$inter_yy[$i];
				$offer['inter_cat'][$i]['ic']=$inter_price[$i]*$inter_yy[$i];
				$total_cost += $offer['inter_cat'][$i]['ic'];
				$z++;
			}
		}


		$z=0;
		$offer['fus_cat'] = array();
		for ($i =0; $i< sizeof($fus_item); $i++)
		{
			if($fus_usage[$i] || $fus_item[$i])
			{
				$offer['fus_cat'][$i]['i']=$z;
				$offer['fus_cat'][$i]['j']=$i+1;
				$offer['fus_cat'][$i]['fsi']=$fus_item[$i];
				$offer['fus_cat'][$i]['fsu']=$fus_usage[$i];
				$offer['fus_cat'][$i]['fsp']=$fus_price[$i];
				$offer['fus_cat'][$i]['fsq']=$fus_yy[$i];
				$offer['fus_cat'][$i]['fsc']=$fus_price[$i]*$fus_yy[$i];
				$total_cost += $offer['fus_cat'][$i]['fsc'];
				$z++;
			}
		}


		$z = 0;
		$offer['acc_cat'] = array();
		for ($i =0; $i< sizeof($acc_item); $i++)
		{
			if(!isset($acc_usage[$i]))$acc_usage[$i] = '';
			if($acc_usage[$i] || $acc_item[$i])
			{
				$offer['acc_cat'][$i]['i']=$z;
				$offer['acc_cat'][$i]['j']=$i+1;
				$offer['acc_cat'][$i]['ai']=$acc_item[$i];
				$offer['acc_cat'][$i]['au']=$acc_usage[$i];
				$offer['acc_cat'][$i]['ap']=$acc_price[$i];
				$offer['acc_cat'][$i]['aq']=$acc_yy[$i];
				$offer['acc_cat'][$i]['ac']=$acc_price[$i]*$acc_yy[$i];
				$total_cost += $offer['acc_cat'][$i]['ac'];
				$z++;
			}
		}
		$z=0;
		$offer['oth_cat'] = array();
		for ($i =0; $i< sizeof($other_item); $i++)
		{
			if(!isset($other_usage[$i]))$other_usage[$i] = '';
			if($other_usage[$i] || $other_item[$i])
			{
				$offer['oth_cat'][$i]['i']=$z;
				$offer['oth_cat'][$i]['j']=$i+1;
				$offer['oth_cat'][$i]['oi']=$other_item[$i];
				$offer['oth_cat'][$i]['ou']=$other_usage[$i];
				$offer['oth_cat'][$i]['op']=$other_price[$i];
				$offer['oth_cat'][$i]['oq']=$other_yy[$i];
				$offer['oth_cat'][$i]['oc']=$other_price[$i]*$other_yy[$i];
				$total_cost += $offer['oth_cat'][$i]['oc'];
				$z++;
			}
		}	
		$total_cost +=$offer['cm'];
		$total_cost +=$offer['quota'];
		$offer['un_gm_cost'] = $total_cost;
		return $offer;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="") ���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function create_item_pdf($offer) {
		$offer['cos']= explode('|',$offer['cons']);
		$cos = '';
		if (isset($offer['cos'][0]) && $offer['cos'][0]) $cos .= "yy : ".$offer['cos'][0]." yd , ";
		if (isset($offer['cos'][1]) && $offer['cos'][1]) $cos .= "base on mini.-mark size : ".$offer['cos'][1].";  ";
		if (isset($offer['cos'][2]) && $offer['cos'][2]) $cos .= "order size range : ".$offer['cos'][2].";  ";
		$offer['cons'] = $cos;
		$fab_item = explode('|',$offer['fab_item']);
		$fab_usage = explode('|',$offer['fab_usage']);
		$fab_price = explode('|',$offer['fab_price']);
		$fab_yy = explode('|',$offer['fab_yy']);

		$inter_item = explode('|',$offer['inter_item']);
		$inter_usage = explode('|',$offer['inter_usage']);
		$inter_price = explode('|',$offer['inter_price']);
		$inter_yy = explode('|',$offer['inter_yy']);

		$fus_item = explode('|',$offer['fus_item']);
		$fus_usage = explode('|',$offer['fus_usage']);
		$fus_price = explode('|',$offer['fus_price']);
		$fus_yy = explode('|',$offer['fus_yy']);


		$acc_item = explode('|',$offer['acc_item']);
		$acc_usage = explode('|',$offer['acc_usage']);
		$acc_price = explode('|',$offer['acc_price']);
		$acc_yy = explode('|',$offer['acc_yy']);
		$other_item = explode('|',$offer['other_item']);
		$other_usage = explode('|',$offer['other_usage']);
		$other_price = explode('|',$offer['other_price']);
		$other_yy = explode('|',$offer['other_yy']);
		$tmp_totoal =0;
	for ($i =0; $i< sizeof($fab_item); $i++)
		{
			if($i == 0) 
			{
				$offer['fab_cat'][$i]['cate']="Fabric";
			}else{
				$offer['fab_cat'][$i]['cate']='';
			}			
			$offer['fab_cat'][$i]['i']=$fab_item[$i];
			if(isset($fab_usage[$i])) 
			{
				$offer['fab_cat'][$i]['u']=$fab_usage[$i];
			}else{
				$offer['fab_cat'][$i]['u']='';
			}				
			$offer['fab_cat'][$i]['p']= number_format($fab_price[$i],2,'.','');
			$offer['fab_cat'][$i]['q']= number_format($fab_yy[$i],2,'.','');
			$offer['fab_cat'][$i]['c']= number_format(($fab_price[$i]*$fab_yy[$i]),2,'.','');
			$tmp_totoal += ($fab_price[$i]*$fab_yy[$i]);
		}

		for ($i =0; $i< sizeof($inter_item); $i++)
		{
			if($i == 0) 
			{
				$offer['inter_cat'][$i]['cate']="InterLining";
			}else{
				$offer['inter_cat'][$i]['cate']='';
			}	
			$offer['inter_cat'][$i]['i']=$inter_item[$i];
			$offer['inter_cat'][$i]['u']=$inter_usage[$i];
			$offer['inter_cat'][$i]['p']=number_format($inter_price[$i],2,'.','');
			$offer['inter_cat'][$i]['q']=number_format($inter_yy[$i],2,'.','');
			$offer['inter_cat'][$i]['c']=number_format(($inter_price[$i]*$inter_yy[$i]),2,'.','');
			$tmp_totoal += ($inter_price[$i]*$inter_yy[$i]);
		}



		for ($i =0; $i< sizeof($fus_item); $i++)
		{
			if($i == 0) 
			{
				$offer['fus_cat'][$i]['cate']="Fusible";
			}else{
				$offer['fus_cat'][$i]['cate']='';
			}
			$offer['fus_cat'][$i]['i']=$fus_item[$i];
			$offer['fus_cat'][$i]['u']=$fus_usage[$i];
			$offer['fus_cat'][$i]['p']=number_format($fus_price[$i],2,'.','');
			$offer['fus_cat'][$i]['q']=number_format($fus_yy[$i],2,'.','');
			$offer['fus_cat'][$i]['c']=number_format(($fus_price[$i]*$fus_yy[$i]),2,'.','');
			$tmp_totoal += ($fus_price[$i]*$fus_yy[$i]);
		}



		for ($i =0; $i< sizeof($acc_item); $i++)
		{
			if($i == 0) 
			{
				$offer['acc_cat'][$i]['cate']="Trimming";
			}else{
				$offer['acc_cat'][$i]['cate']='';
			}
			$offer['acc_cat'][$i]['i']=$acc_item[$i];
			if(isset($acc_usage[$i]))
			{
				$offer['acc_cat'][$i]['u']=$acc_usage[$i];
			}else{
				$offer['acc_cat'][$i]['u']='';
			}
			$offer['acc_cat'][$i]['p']=number_format($acc_price[$i],2,'.','');
			$offer['acc_cat'][$i]['q']=number_format($acc_yy[$i],2,'.','');
			$offer['acc_cat'][$i]['c']=number_format(($acc_price[$i]*$acc_yy[$i]),2,'.','');
			$tmp_totoal += ($acc_price[$i]*$acc_yy[$i]);
		}
		for ($i =0; $i< sizeof($other_item); $i++)
		{
			if($i == 0) 
			{
				$offer['oth_cat'][$i]['cate']="Other/Treatment";
			}else{
				$offer['oth_cat'][$i]['cate']='';
			}
			$offer['oth_cat'][$i]['i']=$other_item[$i];
			if(isset($other_usage[$i])) 
			{
				$offer['oth_cat'][$i]['u']=$other_usage[$i];
			}else{
				$offer['oth_cat'][$i]['u']='';
			}
			$offer['oth_cat'][$i]['p']=number_format($other_price[$i],2,'.','');
			$offer['oth_cat'][$i]['q']=number_format($other_yy[$i],2,'.','');
			$offer['oth_cat'][$i]['c']=number_format(($other_price[$i]*$other_yy[$i]),2,'.','');
			$tmp_totoal += ($other_price[$i]*$other_yy[$i]);
		}	
		$offer['cmtp'] = $offer['tt_cost'] - $tmp_totoal;
		return $offer;
	} // end func





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �O��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy($old_id,$new_id) {

		$sql = $this->sql;
		
		//���o�Qcopy���
		$q_str = "SELECT * FROM offer WHERE id=' ".$old_id."' ";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! cannot find record!");
			return false;    
		}
		
		//copy�ܷs������
		$str="UPDATE offer SET ";
		foreach ($row as $key => $value)
		{						
			if (!is_int($key) && $key != 'id' && $key !='num' )
			{
				$value = str_replace("'","\'",$value);

				$str=$str.$key." = '".$value."' , ";
			}
		}
		$str=substr($str,0,-2);
		$str.=" WHERE id ='".$new_id."'";
		
		if (!$q_result = $sql->query($str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}


 	 $q_str = "UPDATE offer SET k_date ='"		.date('Y-m-d').
						"', merchan='"		.$GLOBALS['SCACHE']['ADMIN']['login_id'].
						"', status='0"		. 						
						"'  WHERE id='"	.$new_id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>