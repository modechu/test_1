<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### �q ��  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)				�[�J�s �q��O��  �Ǧ^ $id
#	->search($mode=0, $dept='',$limit_entries=0)			�j�M �q �� ���
#
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class EXCEPTION {
		
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
	function update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order') {

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
	function update_fields($field1, $value1, $id, $table='exceptional') {

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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

//�d�̫߳᪩�� 
	
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	

					# �[�J��Ʈw(2007.03.02�[�J�ئT���)
		$q_str = "INSERT INTO exceptional (
								dept, cust, cust_ver, ord_num, exc_des, sys_des, oth_dept, ex_num, oth_static, exc_from, exc_user, exc_date
		          ) VALUES('".
							$parm['dept']."','".
							$parm['cust']."','".
							$cust_row['ver']."','".
							$parm['ord_num']."','".
							$parm['exc_des']."','".
							$parm['sys_des']."','".
							$parm['oth_dept']."','".
							$parm['ex_num']."','".
							$parm['oth_static']."','".
							$parm['exc_from']."','".
							$parm['exc_user']."','".														
							$parm['exc_date']."')";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;  
			 
		}
//echo $q_str."<br>";	

		$ord_id = $sql->insert_id();  //���X �s�� id
		

		$this->msg->add("append Exceptional On order#: [".$parm['ord_num']."]�C") ;	
		return $ord_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_static($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw(2007.03.02�[�J�ئT���)
		$q_str = "INSERT INTO exc_static (
								exc_id, state, org_rec, new_rec
		          ) VALUES('".
							$parm['exc_id']."','".
							$parm['state']."','".
							$parm['org_rec']."','".													
							$parm['new_rec']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //���X �s�� id		

		return $ord_id;

	} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_comm($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw(2007.03.02�[�J�ئT���)
		$q_str = "INSERT INTO exc_comment (
								exc_id, comm_dept, comm
		          ) VALUES('".
							$parm['exc_id']."','".
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
	function search($mode=0,$oth_exc=0,$dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT DISTINCT exceptional.* , cust_init_name as cust_iname, exceptional.dept as ord_dept
								 FROM exceptional, cust,  exc_comment ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("exceptional.id DESC");
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

	# ���������
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ( $argv['PHP_dept'] ) {
		$srh->add_where_condition("exceptional.dept = '".$argv['PHP_dept']."'", "",$argv['PHP_dept'],"Department=[ ".$argv['PHP_dept']." ] ");
	} else {
        if ($user_team == 'MD') {
            $srh->add_where_condition("exceptional.dept = '".$user_dept."' || exc_comment.comm_dept = '".$user_dept."'", "",$user_dept,"Department=[ $user_dept ]. ");
        } else {
            if ( if_factory() && !$argv['PHP_dept'] ) {
                $srh->add_where_condition("exceptional.dept = '".$user_dept."' || exc_comment.comm_dept = '".$user_dept."'", "",$user_dept,"Department=[ $user_dept ]. ");
            } else {
                $dept_group = get_dept_group();
                for ($i=0; $i< sizeof($dept_group); $i++) {
                    $srh->or_where_condition("exceptional.dept = '".$dept_group[$i]."' || exc_comment.comm_dept = '".$dept_group[$i]."'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
                }
            }
        }
    }
    
	// if ($argv['PHP_dept']){  //  ��n��������
        // $DEPT = $argv['PHP_dept'];
        // if( substr($DEPT,0,1) == 'K' ) $DEPT = 'K%';
        
		// $srh->add_where_condition("exceptional.dept = '".$argv['PHP_dept']."' || exc_comment.comm_dept LIKE '".$DEPT."'", "",$argv['PHP_dept'],"Dept=[ ".$argv['PHP_dept']." ]. ");
	// } else {
        // �D�޲z��
        // if ($oth_exc == 1 && ($user_dept == 'HJ' || $user_dept == 'LY' || $user_dept == 'CF')){  //  ��n��������
            // $srh->add_where_condition("exceptional.dept = '$user_dept' || exc_comment.comm_dept = '$user_dept'", "",$dept,"Dept=[ $user_dept ]. ");
        // }
    // }


   if ($mode==1){
   	$mesg = '';		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("exceptional.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust = [ $str ]. ";
		}
		if ($str = $argv['SCH_ord'] )  { 
			$srh->add_where_condition("exceptional.ord_num LIKE '%$str%'", "SCH_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
		}
			
		if ($mesg)
		{
			$msg = "Search : ".$mesg;
		
			$this->msg->add($msg);
		}		

   }	
		$srh->add_where_condition("exceptional.cust = cust.cust_s_name AND exceptional.cust_ver = cust.ver");   // ���p����M ���M�n�[
//		$srh->add_where_condition("exceptional.ord_num = s_order.order_num");   // ���p����M ���M�n�[
		$srh->add_where_condition("exceptional.oth_exc = $oth_exc");   
		$srh->add_where_condition("exceptional.id = exc_comment.exc_id");
		$srh->add_where_condition("exceptional.status < 14");
		
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['exc'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		// echo $srh->q_str;
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
	function get($id=0, $order_num=0) {
        $e_date=increceDaysInDate($GLOBALS['TODAY'],30);     
        
		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT exceptional.*, cust_init_name as cust_iname FROM exceptional ,cust WHERE exceptional.id='$id' AND exceptional.cust=cust.cust_s_name AND exceptional.cust_ver = cust.ver";

		} elseif ($order_num) {
			$q_str = "SELECT exceptional.*, cust_init_name as cust_iname FROM exceptional ,cust WHERE exceptional.order_num='$order_num' AND exceptional.cust=cust.cust_s_name AND exceptional.cust_ver = cust.ver";
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
			$this->msg->add("Error ! Can't find this record for exceptional!");
			return false;    
		}		
		$exc_id = $row['id'];
		
		
		$po_user=$GLOBALS['user']->get(0,$row['exc_user']);
		$row['exc_user_id'] = $row['exc_user'];
		if ($po_user['name'])$row['exc_user'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$row['rev_user']);
		$row['rev_user_id'] = $row['rev_user'];
		if ($po_user['name'])$row['rev_user'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$row['apv_user']);
		$row['apv_user_id'] = $row['apv_user'];
		if ($po_user['name'])$row['apv_user'] = $po_user['name'];
		
		$op['exc'] = $row;
		$op['exc']['exc_des'] = str_replace( chr(13).chr(10), "<br>", $op['exc']['exc_des'] );
		$op['exc']['sys_des'] = str_replace( chr(13).chr(10), "<br>", $op['exc']['sys_des'] );
		$op['exc']['pa_num'] = "PA".substr($op['exc']['po_num'],2);

// print_r($op['exc']);
		$state_name = array(
			'1'		=>	'�v�WSCM�t�Ψ���/�R��q��',
			'2'		=>	'�q����O�ܧ�',
			'3'		=>	'�q�����ܧ�',
			'4'		=>	'���B��ŹB',
			'5'		=>	'�O���f�کεu�I�f��',
			'6'		=>	'���ƶW�q����',
			'7'		=>	'���ʳ�R��',
			'8'		=>	'���uú���',
			'9'		=>	'�q�沾��',
			'10'	=>	'�q��R��',
			'99'	=>	'��L',
		);	

		$org_name = array( 	
			'1'		=>	'��q��ƶq',
			'2'		=>	'����O',
			'3'		=>	'����',
			'4'		=>	'���B�O',
			'5'		=>	'��q��f��',
			'6'		=>	'�쪫�ƻݨD�q',
			'7'		=>	'���ʪ��B',
			'8'		=>	'�s�uú���',
			'9'		=>	'���Ʋ��ন��',
			'10'	=>	'�q��ƶq',
			'99'	=>	'',
		);	

		$new_name = array(
			'1'		=>	'����/�R���q��ƶq',
			'2'		=>	'�ק����O',
			'3'		=>	'�ק����',
			'4'		=>	'�ŹB�O',
			'5'		=>	'�O��/�u�I�f��',
			'6'		=>	'���ʼƶq',
			'7'		=>	'',
			'8'		=>	'',
			'9'		=>	'',
			'10'	=>	'',
			'99'	=>	'',
		);
		
		$comm_dept = array(
			'HJ'	=>	'��żt',
			'LY'	=>	'�ߤ��t',
			'CF'	=>	'�ŵ�t',
			'RD'	=>	'��o����',
			'PM'	=>	'�Ͳ�������',
			'MD'	=>	'�T���Ʒ~�B',
			'KA'	=>	'�T���Ʒ~�B KA ��',
			'KB'	=>	'�T���Ʒ~�B KB ��',
			'DA'	=>	'�T���Ʒ~�B DA ��',
			'PO'	=>	'���ʳ��'
		);

		$q_str = "SELECT exc_static.* FROM exc_static WHERE exc_id='$exc_id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$op['state'][$i] = $row;
			if($row['state'] == 99)
			{
				$op['state'][$i]['sta_name'] = $op['exc']['oth_static'];
			}else{				
				$op['state'][$i]['sta_name'] = $state_name[$row['state']];
			}
			$op['state'][$i]['org_name'] = $org_name[$row['state']];
			$op['state'][$i]['new_name'] = $new_name[$row['state']];

			$i++;
		}			
		$i=0;
	
		foreach($state_name as $key => $value)
		{
			$tmp_mk = 0;
			if(isset($op['state']))
			{
				for($k=0; $k<sizeof($op['state']); $k++)
				{
					if($op['state'][$k]['state'] == $key)
					{
						$tmp_mk = 1;
						break;
				}
				}
			}
			if($tmp_mk == 0)
			{
				$op['un_sta'][$i]['state'] = $key;
				$op['un_sta'][$i]['sta_name'] = $value;
				$op['un_sta'][$i]['org_name'] = $org_name[$key];
				$op['un_sta'][$i]['new_name'] = $new_name[$key];
				$i++;

			}
		}	
		
	
	

		$q_str = "SELECT exc_comment.* FROM exc_comment WHERE exc_id='$exc_id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		$com_mk = 1;
		$com_e_mk = 0;
		while ($row = $sql->fetch($q_result)) {
			$op['comm'][$i] = $row;
			if($op['comm'][$i]['Comm'] == '')
			{
				$com_mk = 0;
			}else{
				$com_e_mk = 1;
                // print_r($row);
			}
			$op['comm'][$i]['dept_name'] = $comm_dept[$row['comm_dept']];
			$op['comm'][$i]['Comm'] = str_replace( chr(13).chr(10), "<br>", $op['comm'][$i]['Comm'] );
			$po_user=$GLOBALS['user']->get(0,$op['comm'][$i]['comm_user']);
			$op['comm'][$i]['comm_user_id'] = $op['comm'][$i]['comm_user'];
			if ($po_user['name'])$op['comm'][$i]['comm_user'] = $po_user['name'];
			$i++;
		}				
		$op['com_mk'] = $com_mk;
		$op['com_e_mk'] = $com_e_mk;
// echo $op['com_mk'];
		$ord_nums = explode(',',$op['exc']['ord_num']);
		$op['exc']['ord_nums'] = $ord_nums;
		
		if(sizeof($ord_nums ) == 1 && $op['exc']['ord_num'])
		{
			$q_str = "SELECT s_order.etd, s_order.qty, s_order.smpl_apv, pdtion.mat_etd, pdtion.mat_shp,
											 pdtion.m_acc_etd, pdtion.m_acc_shp, pdtion.shp_date, pdtion.qty_shp, s_order.dept,
											 s_order.ie1
							  FROM s_order, pdtion 
							  WHERE s_order.order_num = pdtion.order_num AND s_order.order_num = '".$op['exc']['ord_num']."'";
			
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}

			if (!$row = $sql->fetch($q_result)) {
				$this->msg->add("Error ! Can't find this record for s_order!");
				return false;    
			}				
			$op['ord_rec'] = $row;
		}
		
		$q_str = "SELECT exc_log.* FROM exc_log WHERE exc_id='$exc_id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		
		while ($row = $sql->fetch($q_result)) {
			$op['logs'][$i] = $row;	
			$op['logs'][$i]['des'] = str_replace( chr(13).chr(10), "<br>", $op['logs'][$i]['des'] );	
//			$dept_name = $GLOBALS['dept']->get(0,$op['logs'][$i]['item']);
//			if($dept_name['dept_name'])$op['logs'][$i]['item'] = $dept_name['dept_name'];
				// $op['logs'][$i]['item'] = '�~�P�Ʒ~�B';
                $op['logs'][$i]['item'] = $SYS_DEPT;
			$po_user=$GLOBALS['user']->get(0,$op['logs'][$i]['user']);
			$op['logs'][$i]['user_id'] = $op['logs'][$i]['user'];
			if ($po_user['name'])$op['logs'][$i]['user'] = $po_user['name'];


			$i++;
		}			
		
		$q_str = "SELECT exc_file_det.* FROM exc_file_det WHERE ex_id='$exc_id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		
		while ($row = $sql->fetch($q_result)) {
			$op['done'][$i] = $row;	
			$i++;
		}			
		
		$op['user_dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
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
			$q_str = "UPDATE exceptional SET ".
							"	  exc_des='"		.$parm['exc_des'].
							"',	oth_dept='"		.$parm['oth_dept'].
							"',	oth_static='"		.$parm['oth_static'].
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
	function edit_state($parm) {

		$sql = $this->sql;		
		
		$q_str = "UPDATE exc_static SET ".
						 "	  org_rec='"	.$parm['org_rec'].
						 "',	new_rec='"	.$parm['new_rec'].
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
#	->edit($parm,$mode=0)		��s �q�� �O�� 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function edit_comm($parm) {

	$sql = $this->sql;		
	
	$q_str = "UPDATE exc_comment SET ".
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
#	->send_submit($parm)		 �q��e�X �ݽT�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function send_submit($parm) {

$sql = $this->sql;

#####   ��s��Ʈw���e
$q_str = "UPDATE exceptional SET status = 2 ".
					",  submit_user='"	.$parm['submit_user'].
					"', submit_date='"	.$parm['submit_date'].
					"'  WHERE id='"		.$parm['id']."'";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->add("Error !  Database can't update.");
	$this->msg->merge($sql->msg);
	return false;    
}
$pdt_id = $parm['id'];

return $pdt_id;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_apv($parm)		 �q��e�X �ݽT�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function send_apv($parm) {

$sql = $this->sql;

#####   ��s��Ʈw���e
$q_str = "UPDATE exceptional SET status = 4 ".
					",  apv_user='"	.$parm['apv_user'].
					"', apv_date='"	.$parm['apv_date'].
					"'  WHERE id='"	.$parm['id']."'";

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
		
		
		
			$q_str = "SELECT $field FROM exc_comment WHERE $where_str";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if(!$row = $sql->fetch($q_result))
			{
				return "UN-COMM";
			}



		
		return $row[0];
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		�R���@����ʩ���
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function comm_del($where_str) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM exc_comment WHERE $where_str";
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

		$q_header = "SELECT exceptional.* , cust_init_name as cust_iname, exceptional.dept as ord_dept FROM exceptional, cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("exceptional.id DESC");
		$srh->row_per_page = 10;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;

		if (isset($argv['PHP_sr_exc']) && $argv['PHP_sr_exc']) {
			$pages = $srh->get_page($argv['PHP_sr_exc'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
	}


		$srh->add_where_condition("exceptional.status = 3"); 		
		$srh->add_where_condition("exceptional.cust = cust.cust_s_name AND exceptional.cust_ver = cust.ver");   // ���p����M ���M�n�[
//		$srh->add_where_condition("exceptional.ord_num = s_order.order_num");   // ���p����M ���M�n�[

		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['exc'] = $result;  // ��ƿ� �ߤJ $op
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
		$q_str = "INSERT INTO exc_log (exc_id,
									user,
									item,
									k_date,
									des) VALUES('".
									$parm['exc_id']."','".
									$parm['user']."','".
									$parm['item']."',
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
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cm_exc($mode=0,$oth_exc=0,$dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT DISTINCT exceptional.* , cust_init_name as cust_iname, s_order.dept as ord_dept, 
												s_order.factory as ord_fty
								 FROM exceptional, cust, s_order, exc_comment ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("exceptional.id DESC");
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


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	// $sale_mang = $GLOBALS['SALES_MANG'];
	// for ($i=0; $i< sizeof($sale_f_mang); $i++)
	// {			
			// if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("exceptional.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	// }
	
	// $sales_dept = $GLOBALS['SALES_DEPT'];
	// if  ($oth_exc == 0 && $user_team == 'MD')	$srh->add_where_condition("exceptional.dept = '$user_dept'", "",$dept,"Dept=[ $user_dept ]. ");
	// for ($i=0; $i< sizeof($sales_dept); $i++)
	// {			
			// if($user_dept == $sales_dept[$i] && $user_team <> 'MD' && $oth_exc == 0) 	$srh->add_where_condition("exceptional.dept = '$user_dept'", "",$dept,"Dept=[ $user_dept ]. ");
	// }

	// if ($oth_exc == 1 && ($user_dept <> 'SA' && $user_dept <> 'GM')){  //  ��n��������
		// $srh->add_where_condition("exceptional.dept = '$user_dept' || exc_comment.comm_dept = '$user_dept'", "",$dept,"Dept=[ $user_dept ]. ");
	// }


   if ($mode==1){
   	$mesg = '';		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("exceptional.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust = [ $str ]. ";
		}
		if ($str = $argv['SCH_ord'] )  { 
			$srh->add_where_condition("exceptional.ord_num LIKE '%$str%'", "SCH_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
		}
			
		if ($mesg)
		{
			$msg = "Search : ".$mesg;
		
			$this->msg->add($msg);
		}		

   }	
		$srh->add_where_condition("exceptional.cust = cust.cust_s_name AND exceptional.cust_ver = cust.ver");   // ���p����M ���M�n�[
		$srh->add_where_condition("exceptional.ord_num = s_order.order_num");   // ���p����M ���M�n�[
		$srh->add_where_condition("exceptional.oth_exc = $oth_exc");   
		$srh->add_where_condition("exceptional.id = exc_comment.exc_id");   

		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['exc'] = $result;  // ��ƿ� �ߤJ $op
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
	function get_status_name($exc_id=0,$static='') {
        $e_date=increceDaysInDate($GLOBALS['TODAY'],30);     
        
		$sql = $this->sql;



		$state_name = array( '1'	=>	'�v�WSCM�t�Ψ���/�R��q��',
												 '2'	=>	'�q����O�ܧ�',
												 '3'	=>	'�q�����ܧ�',
												 '4'	=>	'���B��ŹB',
												 '5'	=>	'�O���f�کεu�I�f��',
												 '6'	=>	'���ƶW�q����',
												 '7'	=>	'���ʳ�R��',
												 '8'	=>	'���uú���',
												 '9'	=>	'�q�沾��',
												 '10'	=>	'�q��R��',
												 '99'	=>	$static
												 );	


		$q_str = "SELECT exc_static.* FROM exc_static WHERE exc_id='$exc_id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		$exc_status = '';
		while ($row = $sql->fetch($q_result)) {
			
			$exc_status .= $state_name[$row['state']].",";
		}			
		$exc_status = substr($exc_status,0,-1);
		return $exc_status;
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_un_num()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_un_num() {
       
		$sql = $this->sql;

		$q_str = "SELECT exceptional.* FROM exceptional WHERE ex_num = ''";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$exc[] = $row;
 
		}		



		return $exc;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_cfm($parm)		 �q��e�X �ݽT�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function send_cfm($parm) {

$sql = $this->sql;

#####   ��s��Ʈw���e
$q_str = "UPDATE exceptional SET status = 3 ".
					",  cfm_user='"		.$parm['cfm_user'].
					"', cfm_date='"	 	.$parm['cfm_date'].
					"'  WHERE id='"		.$parm['id']."'";
		// echo $q_str.'<br>';
		// exit;
if (!$q_result = $sql->query($q_str)) {
	$this->msg->add("Error !  Database can't update.");
	$this->msg->merge($sql->msg);
	return false;    
}
$pdt_id = $parm['id'];

return $pdt_id;
} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_cfm($limit_entries=0) �j�M ��CONFIRM���`���i ���
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_cfm($limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT exceptional.* , cust_init_name as cust_iname, exceptional.dept as ord_dept FROM exceptional, cust ";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }

	# ���������
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("exceptional.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("exceptional.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {			
                $srh->or_where_condition("exceptional.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("exceptional.id DESC");
    $srh->row_per_page = 10;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
        ##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;

		if (isset($argv['PHP_sr_exc']) && $argv['PHP_sr_exc']) {
			$pages = $srh->get_page($argv['PHP_sr_exc'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
        ##--*****--2006.11.16���X�s�W end	   ##
	}


    $srh->add_where_condition("exceptional.status = 2"); 		
    $srh->add_where_condition("exceptional.cust = cust.cust_s_name AND exceptional.cust_ver = cust.ver");   // ���p����M ���M�n�[
    //  $srh->add_where_condition("exceptional.ord_num = s_order.order_num");   // ���p����M ���M�n�[

    $result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    
    echo $srh->q_str;

    $this->msg->merge($srh->msg);
    if (!$result){   // ��d�M�L��Ʈ�
        $op['record_NONE'] = 1;
    }
    $op['exc'] = $result;  // ��ƿ� �ߤJ $op
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



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->send_rev($parm)		 �q��e�X �ݽT�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function send_rev($parm) {

$sql = $this->sql;

#####   ��s��Ʈw���e
$q_str = "UPDATE `exceptional` SET `status` = 3 , rev_user = '".$parm['rev_user']."' , rev_date = '".$parm['rev_date']."' WHERE id = '".$parm['id']."';";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->add("Error !  Database can't update.");
	$this->msg->merge($sql->msg);
	return false;    
}
$pdt_id = $parm['id'];

return $pdt_id;
} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->search_rev($limit_entries=0) �j�M ��CONFIRM���`���i ���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_rev($limit_entries=0) {

	$sql = $this->sql;
	$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "SELECT exceptional.* , cust_init_name as cust_iname, exceptional.dept as ord_dept FROM exceptional, cust ";
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_sort_condition("exceptional.id DESC");
	$srh->row_per_page = 10;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;

		if (isset($argv['PHP_sr_exc']) && $argv['PHP_sr_exc']) {
			$pages = $srh->get_page($argv['PHP_sr_exc'],$pagesize);	
		}else{
			$pages = $srh->get_page(1,$pagesize);
		} 
		##--*****--2006.11.16���X�s�W end	   ##
	}

	$srh->add_where_condition("exceptional.status = 6"); 		
	$srh->add_where_condition("exceptional.cust = cust.cust_s_name AND exceptional.cust_ver = cust.ver");   // ���p����M ���M�n�[
	// $srh->add_where_condition("exceptional.ord_num = s_order.order_num");   // ���p����M ���M�n�[

	$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
		if (!$result){   // ��d�M�L��Ʈ�
			$op['record_NONE'] = 1;
		}
	$op['exc'] = $result;  // ��ƿ� �ߤJ $op
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



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_un_num()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_exc($ord_num) {
   
	$sql = $this->sql;
	
	$state_name = array(
		'1'		=>	'�v�WSCM�t�Ψ���/�R��q��',
		'2'		=>	'�q����O�ܧ�',
		'3'		=>	'�q�����ܧ�',
		'4'		=>	'���B��ŹB',
		'5'		=>	'�O���f�کεu�I�f��',
		'6'		=>	'���ƶW�q����',
		'7'		=>	'���ʳ�R��',
		'8'		=>	'���uú���',
		'9'		=>	'�q�沾��',
		'10'	=>	'�q��R��',
		'99'	=>	'��L',
	);

	$q_str = "SELECT ex_num, ap.po_num, lots_code, exc_id, state, oth_static, ap.ap_num
						FROM 	 exceptional, ap, ap_det, bom_lots, lots_use, exc_static
						WHERE  lots_use.id = bom_lots.lots_used_id AND bom_lots.id = ap_det.bom_id AND 
									 ap_det.ap_num = ap.ap_num AND ap.po_num = exceptional.po_num AND 
									 exc_static.exc_id = exceptional.id AND ap_det.mat_cat =  'l' AND 
									 lots_use.smpl_code =  '".$ord_num."'
						";
						
	//echo $q_str."<BR>";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row = $sql->fetch($q_result)) {
		$row['exc_name'] = $row['oth_static'];
		if(!$row['oth_static'])$row['exc_name'] =$state_name[$row['state']];
		$exc[$row['exc_id']] = $row;
		if(!isset($exc[$row['exc_id']]['mat_code']))$exc[$row['exc_id']]['mat_code'] = '';
		$exc[$row['exc_id']]['mat_code'] .= $row['lots_code'].',';
	}		

	$q_str = "SELECT ex_num, ap.po_num, acc_code, exc_id, state, oth_static, ap.ap_num
						FROM 	exceptional, ap, ap_det, bom_acc, acc_use, exc_static
						WHERE acc_use.id = bom_acc.acc_used_id AND bom_acc.id = ap_det.bom_id AND 
									ap_det.ap_num = ap.ap_num AND ap.po_num = exceptional.po_num AND 
									exc_static.exc_id = exceptional.id AND ap_det.mat_cat =  'a' AND 
									acc_use.smpl_code =  '".$ord_num."'
						";
	//echo $q_str."<BR>";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row = $sql->fetch($q_result)) {
		$row['exc_name'] = $row['oth_static'];
		if(!$row['oth_static'])$row['exc_name'] =$state_name[$row['state']];
		$exc[$row['exc_id']] = $row;
		if(!isset($exc[$row['exc_id']]['mat_code']))$exc[$row['exc_id']]['mat_code'] = '';
		$exc[$row['exc_id']]['mat_code'] .= $row['acc_code'].','; 
	}		

	$q_str = "SELECT 	ex_num, ap.po_num, exc_id, state, oth_static, mat_code, ap.ap_num
						FROM 		exceptional, ap, ap_special, exc_static
						WHERE 	ap_special.ap_num = ap.ap_num AND ap.po_num = exceptional.po_num AND 
										exc_static.exc_id = exceptional.id AND ap_special.ord_num =  '".$ord_num."'			
						";
						
	//echo $q_str."<BR>";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row = $sql->fetch($q_result)) {
		$row['exc_name'] = $row['oth_static'];
		if(!$row['oth_static'])$row['exc_name'] =$state_name[$row['state']];
		$exc[] = $row;
// 			if(!isset($mat_code[$row['exc_id']]))$mat_code[$row['exc_id'] = '';
// 			$mat_code[$row['exc_id']] .= $row['mat_code'].','; 
	}		
	
	$rtn = array();
	$i=0;
	if(isset($exc))
	{
		foreach($exc as $key => $value)
		{			
			$rtn[$i] = $exc[$key];			
			$i++;
		}
	}

	return $rtn;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_del_apvd()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_del_apvd($where_value) {
       
		$sql = $this->sql;

		$q_str = "SELECT id FROM exceptional WHERE status <> 4 AND act_des like '%".$where_value."%'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($row = $sql->fetch($q_result)) {
			return false; 
		}		

		return true;
	} // end func






} // end class


?>