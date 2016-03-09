<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### �q ��  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->bom_search($supl,$cat)	�d��BOM���D�Ʈ�


#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class TRANSPORT {
		
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
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function shift_add($parm1) {
					
		$sql = $this->sql;

					# �[�J��Ʈw(2007.03.02�[�J�ئT���)
		$q_str = "INSERT INTO transport (trn_num,org_fty,new_fty ,ord_num ,ap_date, mat_cat) VALUES('".
							$parm1['trn_num']."','".
							$parm1['org_fty']."','".
							$parm1['new_fty']."','".						
							$parm1['ord_num']."','".	
							date('Y-m-d')."','".																																															
							$parm1['mat_cat']."')";
		
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
	function shift_add_det($parm1) {
					
		$sql = $this->sql;

					# �[�J��Ʈw(2007.03.02�[�J�ئT���)
		$q_str = "INSERT INTO tran_det (trn_num,mat_cat,color ,mat_code ,qty, unit, link_id, po_id, ap_table) VALUES('".
							$parm1['trn_num']."','".
							$parm1['mat_cat']."','".
							$parm1['color']."','".	
							$parm1['mat_code']."','".					
							$parm1['qty']."','".								
							$parm1['unit']."','".
							$parm1['rcv_id']."','".
							$parm1['det_id']."','".																																																					
							$parm1['table']."')";
//		echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //���X �s�� id

		return $ord_id;

	} // end func






#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> rcvd_add_search($parm) ��X���禬����B��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rcvd_add_search() {
		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM transport";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##

		if ($str = strtoupper($argv['PHP_trans_num']) )  { 
			$srh->add_where_condition("atrn_num like '%".$str."%'", "PHP_trans_num",$str,"search Transport #:[ $str ] "); 
			}		
		if ($str = $argv['PHP_ship'] )  { 
			$srh->add_where_condition("new_fty  = '".$str."'", "PHP_ship",$str,"search ship FTY =[ $str ]. "); 
			}
		if ($str = $argv['PHP_ord_num'] )  { 
			$srh->add_where_condition("ord_num like '%".$str."%'", "PHP_ord_num",$str,"search Order # :[ $str ] "); 
			}
   
		$srh->add_where_condition("rcv_num = ''");
//		$srh->add_where_condition("status > 0");

  
		$result= $srh->send_query2();   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
		if (!$result)  $op['record_NONE'] = 1; // ��d�M�L��Ʈ�
		
		$op['trans'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
##--*****--2006.11.16���X�s�W start				
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
##--*****--2006.11.16���X�s�W end

		return $op;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rev($num=0) {

		$sql = $this->sql;

//�禬�D��
		$q_str = "SELECT *
							FROM transport  
							WHERE  trn_num = '$num'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		$op['trans']=$row;

		//����Login�b�����W�r
	$op['trans']['sub_user_id'] = $op['trans']['sub_user'];
	$po_user=$GLOBALS['user']->get(0,$op['trans']['sub_user']);
	if ($po_user['name'])$op['trans']['sub_user'] = $po_user['name'];

	$op['trans']['rcv_user_id'] = $op['trans']['rcv_user'];
	$po_user=$GLOBALS['user']->get(0,$op['trans']['rcv_user']);
	if ($po_user['name'])$op['trans']['rcv_user'] = $po_user['name'];
	
	$op['trans']['rcv_sub_user_id'] = $op['trans']['rcv_sub_user'];
	$po_user=$GLOBALS['user']->get(0,$op['trans']['rcv_sub_user']);
	if ($po_user['name'])$op['trans']['rcv_sub_user'] = $po_user['name'];


//�ƮƩ��� 		
		$q_str="SELECT tran_det.*, acc.acc_name as mat_name FROM tran_det, acc	
						WHERE acc.acc_code= tran_det.mat_code AND trn_num = '$num'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$op['trans_det'][$i]=$row1;
			$op['trans_det'][$i]['i']=$i;
			$i++;
		}

//�D�Ʃ��� 		
		$q_str="SELECT tran_det.*, lots.lots_name as mat_name FROM tran_det, lots	
						WHERE lots.lots_code= tran_det.mat_code AND trn_num = '$num'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['trans_det'][$i]=$row1;
			$op['trans_det'][$i]['i']=$i;
			$i++;
		}		

		return $op;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rcv_add($parm)	== �禬 == �D�ɷs�W���ؤ��e
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rcv_add($parm) {

		$sql = $this->sql;
			$q_str = "UPDATE transport SET 
								rcv_num = '".$parm['rcv_num']."', 
								rcv_date = '".$parm['rcv_date']."', 
								status = '4',
								rcv_user = '".$parm['rcv_user']. 
								"' WHERE `id` = '".$parm['id']."'";
			$q_result = $sql->query($q_str);
		return true;
	} // end func			

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rcv_add($parm)	== �禬 == �D�ɷs�W���ؤ��e
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rcv_det_add($qty,$id) {

		$sql = $this->sql;
			$q_str = "UPDATE tran_det SET rcv_qty = '".$qty."' WHERE `id` = '".$id."'";
//echo $q_str."<br>";
			$q_result = $sql->query($q_str);
		return true;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> rcvd_add_search($parm) ��X���禬����B��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rcvd_search($mod) {
		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT DISTINCT transport.* FROM transport, tran_det";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;
		if (isset($argv['PHP_sr_no']) && $argv['PHP_sr_no']) {
			$pages = $srh->get_page($argv['PHP_sr_no'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
	if($mod == 1)
	{
		if ($str = strtoupper($argv['SCH_num']) )  { 
			$srh->add_where_condition("transport.rcv_num LIKE '%$str%'", "PHP_SCH_num",$str,"search receive #:[ $str ] "); 
			}
		if ($str = $argv['SCH_fab'] )  { 
			$srh->add_where_condition("tran_det.mat_code like '%$str%'", "PHP_SCH_fty",$str,"search Fabric# :[ $str ] "); 
			}
		if ($str = $argv['SCH_acc'] )  { 
			$srh->add_where_condition("tran_det.mat_code like '%$str%'", "PHP_SCH_cust",$str,"search Accessory# :[ $str ] "); 
			} 
		}	  
		$srh->add_where_condition("transport.rcv_num <> ''");
		$srh->add_where_condition("transport.trn_num = tran_det.trn_num");

  
		$result= $srh->send_query2();   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
		if (!$result)  $op['record_NONE'] = 1; // ��d�M�L��Ʈ�
		
		$op['trans'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
##--*****--2006.11.16���X�s�W start				
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
##--*****--2006.11.16���X�s�W end

		return $op;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rcv_add($parm)	== �禬 == �D�ɷs�W���ؤ��e
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rcv_submit($parm) {

		$sql = $this->sql;
			$q_str = "UPDATE transport SET 
								rcv_sub_date = '".$parm['rcv_date']."', 
								rcv_rev = '".$parm['rcv_rev']."', 
								status = '".$parm['status']."', 
								rcv_sub_user = '".$parm['rcv_user']. 
								"' WHERE `id` = '".$parm['id']."'";
			$q_result = $sql->query($q_str);
		return true;
	} // end func			


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($trn_num=0,$num=0) {

		$sql = $this->sql;

		if ($num) {
			$q_str = "SELECT 
			`tran_det`.*,
			`transport`.*
			FROM `tran_det` LEFT JOIN `transport` ON ( `tran_det`.`id` = `transport`.`id` ) 
			WHERE `tran_det`.`id` = '".$id."' ";
			
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! Database can't access!");$this->msg->merge($sql->msg);return false;};
			while ($row1 = $sql->fetch($q_result))$op = $row1;
			$op['org_fty'] = $GLOBALS['SHIP_TO'][$op['org_fty']];
			$op['new_fty'] = $GLOBALS['SHIP_TO'][$op['new_fty']];
			$note = '';
			$at = '��';
			$sps =  ' ';
			if($op['mat_cat']=='l'){
				if($op['mat_code']){
					$lotss = $GLOBALS['lots']->get('',$op['mat_code']);
					if(!empty($lotss['lots_name']))	$op['mat_name'] = $lotss['lots_name'];
					if(!empty($lotss['custer']))	$note = $at.'Customer#'.$lotss['custer'].$sps;
					if(!empty($lotss['des']))			$note .= $at."Supplier's code�G".$lotss['des'].$sps;
					if(!empty($lotss['comp']))		$note .= $at.'Content�G'.$lotss['comp'].$sps;
					if(!empty($lotss['specify']))	$note .= $at.'Construction�G'.$lotss['specify'].$sps;
					if(!empty($lotss['width']))		$note .= $at.'CuttablWidth�G'.$lotss['width'].$sps;
					if(!empty($lotss['weight']))	$note .= $at.'Weight�G'.$lotss['weight'].$sps;
					if(!empty($lotss['cons']))		$note .= $at.'Finish�G'.$lotss['cons'].$sps;
					if(!empty($lotss['memo']))		$note .= $at.'Remark�G'.$lotss['memo'].$sps;
					
					$op['note'] = $note;
				}
			}else{
				if($op['mat_code']){
					$acc = $GLOBALS['acc']->get('',$op['mat_code']);
					if(!empty($acc['acc_name']))	$op['mat_name'] = $acc['acc_name'];
					if(!empty($acc['mile_code']))	$note = $at."Supplier's code�G".$acc['mile_code'].$sps;
					if(!empty($acc['mile_name']))	$note .= $at."Supplier's name�G".$acc['mile_name'].$sps;
					if(!empty($acc['specify']))		$note .= $at.'Construction�G'.$acc['specify'].$sps;
					if(!empty($acc['des']))				$note .= $at.'Description�G'.$acc['des'].$sps;
					$op['note'] = $note;
				}
			}
		}else{
			$note = '';
			$at = '��';
			$sps =  ' ';
			$op['trn_num'] =	'';
			$q_str = "SELECT `org_fty`,`new_fty`,`trn_num`,`ord_num`,`sub_user`,sub_date,revise,remark FROM `transport` WHERE `trn_num` = '".$trn_num."' ";
//echo $q_str;
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! Database can't access!");$this->msg->merge($sql->msg);return false;};
			while ($row1 = $sql->fetch($q_result))$op = $row1;

			$q_str = "SELECT * FROM `tran_det` WHERE `trn_num`='".$trn_num."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! Database can't access!");$this->msg->merge($sql->msg);return false;};
			while ($row2 = $sql->fetch($q_result))$trn2[] = $row2;

			foreach($trn2 as $key => $val){
				$note='';
				$op['mat_list'][$key]['mat_code'] = $val['mat_code'];
				$op['mat_list'][$key]['id'] = $val['id'];
				$op['mat_list'][$key]['color'] = $val['color'];
				$op['mat_list'][$key]['qty'] = $val['qty'];
				$op['mat_list'][$key]['unit'] = $val['unit'];
				if($val['mat_cat']=='l'){
					if($val['mat_code']){
						$lotss = $GLOBALS['lots']->get('',$val['mat_code']);
						if(!empty($lotss['lots_name']))	$op['mat_list'][$key]['mat_name'] = $lotss['lots_name'];
						if(!empty($lotss['custer']))	$note = $at.'Customer#'.$lotss['custer'].$sps;
						if(!empty($lotss['des']))			$note .= $at."Supplier's code�G".$lotss['des'].$sps;
						if(!empty($lotss['comp']))		$note .= $at.'Content�G'.$lotss['comp'].$sps;
						if(!empty($lotss['specify']))	$note .= $at.'Construction�G'.$lotss['specify'].$sps;
						if(!empty($lotss['width']))		$note .= $at.'CuttablWidth�G'.$lotss['width'].$sps;
						if(!empty($lotss['weight']))	$note .= $at.'Weight�G'.$lotss['weight'].$sps;
						if(!empty($lotss['cons']))		$note .= $at.'Finish�G'.$lotss['cons'].$sps;
						if(!empty($lotss['memo']))		$note .= $at.'Remark�G'.$lotss['memo'].$sps;
						$op['mat_list'][$key]['note'] = $note;
					}
				}else{
					if($row2['mat_code']){
						$acc = $GLOBALS['acc']->get('',$row2['mat_code']);
						if(!empty($acc['acc_name']))	$op['mat_list'][$key]['mat_name'] = $acc['acc_name'];
						if(!empty($acc['mile_code']))	$note = $at."Supplier's code�G".$acc['mile_code'].$sps;
						if(!empty($acc['mile_name']))	$note .= $at."Supplier's name�G".$acc['mile_name'].$sps;
						if(!empty($acc['specify']))		$note .= $at.'Construction�G'.$acc['specify'].$sps;
						if(!empty($acc['des']))				$note .= $at.'Description�G'.$acc['des'].$sps;
						$op['mat_list'][$key]['note'] = $note;
					}
				}
			}
			$op['org_fty'] = $GLOBALS['SHIP_TO'][$op['org_fty']];
			$op['new_fty'] = $GLOBALS['SHIP_TO'][$op['new_fty']];
		}
		return $op;

	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search($mode=0, $dept='',$limit_entries=0) {

	$sql = $this->sql;
	$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

	$srh = new SEARCH();
	$cgi = array();
	if(!$srh->set_sql($sql)){$this->msg->merge($srh->msg);return false;};

	$q_sql = "SELECT * FROM transport ";
	if(!$srh->add_q_header($q_sql)){$this->msg->merge($srh->msg);return false;};

	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$mesg = '';

	if (!empty($argv['PHP_ord_num'])){
		if ($str = $argv['PHP_ord_num']){ 
			$srh->add_where_condition("ord_num like '%$str%'", "ord_num",$str);
			$mesg.= " ord_num : [ $str ]. ";	
		}
	}

	if (!empty($argv['PHP_trn_num'])){
		if ($str = $argv['PHP_trn_num']){ 
			$srh->add_where_condition("trn_num like '%$str%'", "trn_num",$str); 
			$mesg.= " trn_num : [ $str ]. ";	
		}
	}

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

	if (!$result){
		// ��d�M�L��Ʈ�
		$op['record_NONE'] = 1;
	}
	$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
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
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($qty) {

		$sql = $this->sql;

		# �ק��Ʈw
		if( !empty($qty) ){
			foreach($qty as $key => $val){
				$q_str = "UPDATE tran_det SET
									qty				= '". $val."'
									WHERE id	= '".$key."'";

				if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error ! cannot append order");
					$this->msg->merge($sql->msg);
					return false;    
				}
			}
			return 1;
		}else{
			return 0;
		}
	} // end func

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	��X���w�O�� bom �D�Ƭ������ RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_det_field($field,$table,$where) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT ". $field. " FROM ".$table." WHERE ".$where;
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		return $row1;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pdate_field_id($field1, $value1, $id, $table='receive')	
#
#		��s field���� (�HID)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_id($field1, $value1, $id, $table='receive') {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE trn_num = '".	$id ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func	
	



} // end class


?>