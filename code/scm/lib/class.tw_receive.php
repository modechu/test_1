<?php
#++++++++++++++++++++++++++++++++++ ORDER  class ##### �q ��  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->bom_search($supl,$cat)	�d��BOM���D�Ʈ�
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class TW_RECEIVE {

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
#	-> search_inv($mode=0, $dept='',$limit_entries=0) ��X���禬���o��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_inv($argv,$limit_entries=0) {
	$sql = $this->sql;
	
	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "SELECT id, bl_num, carrier_num, org, dist, ship_date, ship_eta_date FROM po_supl_ship ";
	
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
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
		}else{
			$pages = $srh->get_page(1,$pagesize);
		}
	}

	if ($str = $argv['PHP_ship'] )  { 
		$srh->add_where_condition("id like '%$str%'", "PHP_ship",$str,"search ship #:[ $str ] "); 
	}

	if ($str = $argv['PHP_carrier'] )  { 
		$srh->add_where_condition("carrier_num like '%".$str."%'", "PHP_carrier",$str,"search carrier #:[ $str ] "); 
	}
	
	if ($str = $argv['PHP_bl'] )  { 
		$srh->add_where_condition("bl_num like '%".$str."%'", "PHP_bl",$str,"search B.L #:[ $str ] "); 
	}
	
	$srh->add_where_condition("status = 1");

	$result = $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries

	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
	if (!$result)  $op['record_NONE'] = 1; // ��d�M�L��Ʈ�

	$op['ship'] = $result;  // ��ƿ� �ߤJ $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;

	if(!$limit_entries){ 
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];		
	}	

	return $op;
}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_po()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_po() {
	
	$sql = $this->sql;
	$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$spec = $this->get_det_field("special","ap","po_num = '".$argv['PHP_po']."'");
	$table='ap_det';
	
	$q_header = "SELECT DISTINCT po_ship. *
						FROM ap, ".$table.", po_ship, po_ship_det ";
	
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_sort_condition("po_ship.id DESC");
	$srh->row_per_page = 20;
	
	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
		$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
		}else{
			$pages = $srh->get_page(1,$pagesize);
		}
	}
	
	if ($str = strtoupper($argv['PHP_po']) )  { 
		$srh->add_where_condition("ap.po_num like '%".$str."%'", "PHP_po",$str,"search P/O #:[ $str ] "); 
	}
	if ($str = $argv['PHP_ship'] )  { 
		$srh->add_where_condition("po_ship.dist = '".$str."'", "PHP_shpi",$str,"search ship :[ $str ] "); 
	}
	
	$srh->add_where_condition($table.".ap_num = ap.ap_num");
	$srh->add_where_condition("po_ship_det.po_id = ".$table.".po_spare");
	$srh->add_where_condition("po_ship_det.ship_id = po_ship.id");
	$srh->add_where_condition("po_ship.status = 2");

	$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
	if (!$result)  $op['record_NONE'] = 1; // ��d�M�L��Ʈ�

	$op['apply'] = $result;  // ��ƿ� �ߤJ $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;

	if(!$limit_entries){ 
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];		
	}	
	
	
		return $op;
	} // end func	

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->creat_po_det()	�զX���ʩ���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function creat_po_det($ap_det)
{
		$ap_det2[0] = $ap_det[0];
		$ap_det2[0]['orders'][0] = $ap_det[0]['ord_num'];
		$ap_det2[0]['ids'] = $ap_det[0]['id'];

		$toler = explode('|',$ap_det[0]['toler']);
		if( strlen($toler[0]) == 1 ) {
			$toler = '1.0'.$toler[0];
		} else {
			$toler = '1.'.$toler[0];
		}
		
		$ap_det2[0]['un_qty'] = ( $ap_det[0]['po_qty'] * $toler  ) - $ap_det[0]['rcv_qty'] ;
		$ap_det2[0]['i'] = 0;

		$k=1;
		for ($i=1; $i<sizeof($ap_det); $i++)
		{
			$mk=0;	$order_add=0;
			$x=1;
			for ($j=0; $j< sizeof($ap_det2); $j++)
			{
				if ($ap_det2[$j]['ap_num'] == $ap_det[$i]['ap_num'] && $ap_det2[$j]['mat_code'] == $ap_det[$i]['mat_code'] && $ap_det2[$j]['color'] == $ap_det[$i]['color'] && $ap_det2[$j]['unit'] == $ap_det[$i]['unit'] && $ap_det2[$j]['eta'] == $ap_det[$i]['eta'])
				{
					$ap_det2[$j]['ap_qty'] = $ap_det[$i]['ap_qty'] + $ap_det2[$j]['ap_qty'];
					$ap_det2[$j]['po_qty'] = $ap_det[$i]['po_qty'] + $ap_det2[$j]['po_qty'];
					$ap_det2[$j]['rcv_qty'] = $ap_det[$i]['rcv_qty'] +$ap_det2[$j]['rcv_qty'];
					$un_qty = ( $ap_det[$i]['po_qty'] * $toler ) - $ap_det[$i]['rcv_qty'];
					
					$ap_det2[$j]['un_qty'] += $un_qty;
					for ($z =0; $z < sizeof($ap_det2[$j]['orders']); $z++)
					{
						if ($ap_det2[$j]['orders'][$z] == $ap_det[$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0)	$ap_det2[$j]['orders'][] = $ap_det[$i]['ord_num'];
					$ap_det2[$j]['ids'] = $ap_det2[$j]['ids']."|".$ap_det[$i]['id'];
					$x++;
					$mk = 1;
				}
			}

			if ($mk == 0)
			{
				$ap_det2[$k] = $ap_det[$i];
				$ap_det2[$k]['orders'][0] = $ap_det[$i]['ord_num'];
				$ap_det2[$k]['ids'] = $ap_det[$i]['id'];
				$ap_det2[$k]['un_qty'] = ( $ap_det[$i]['po_qty'] * $toler ) - $ap_det[$i]['rcv_qty'];
				$ap_det2[$k]['i'] = $k;
//				if ($ap_det[$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
//				$op['ap_det2'][$i]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);

				$k++;
			}
		}
		return $ap_det2;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm1) {
					
		$sql = $this->sql;

		$q_str = "INSERT INTO receive (ship_num,rcv_num,rcv_date,rcv_user,ship,dept,tw_rcv) VALUES('".
							$parm1['ship_num']."','".
							$parm1['rcv_num']."','".
							$parm1['rcv_date']."','".
							$parm1['rcv_user']."','".					
							$parm1['ship']."','".																																																
							$parm1['dept']."',1)";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //���X �s�� id

		return $ord_id;

	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_det($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
					
		$sql = $this->sql;

			$q_str = "INSERT INTO receive_det (rcv_num,ship_link_id,inv_num,sup_no,color,qty,ap_num,mat_cat,mat_id,size) 
				  VALUES('".
							$parm['rcv_num']."','".
							$parm['ship_link_id']."','".
							$parm['inv_num']."','".
							$parm['sup_no']."','".
							$parm['color']."','".
							$parm['qty']."','".
							$parm['ap_num']."','".
							$parm['mat_cat']."',".
							$parm['mat_id'].",'".
							$parm['size']."'".
						")";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot append order");
				$this->msg->merge($sql->msg);
				return false;    
			}

		$this->msg->add("append receive#: [".$parm['rcv_num']."]�C") ;
		$ord_id = $sql->insert_id();
		return $ord_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_link
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_link($rcv_id,$po_id,$qty,$ord_num) {
					
		$sql = $this->sql;

			$q_str = "INSERT INTO rcv_po_link (rcv_id,po_id,ord_num,qty) 
				  VALUES('".
							$rcv_id."','".
							$po_id."','".
							$ord_num."','".
							$qty."')";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot append order");
				$this->msg->merge($sql->msg);
				return false;    
			}

//		$this->msg->add("append receive#: [".$parm['rcv_num']."]�C") ;
		$ord_id = $sql->insert_id();
		return $ord_id;

	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_rcv_qty($field1, $value1,  $id, $table='ap_det')	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_rcv_qty($field1, $value1, $id, $table='ap') {

		$sql = $this->sql;

//�[�`�禬�ƶq
		$q_str = "SELECT sum(qty) as qty	FROM rcv_po_link 	WHERE  po_id='$id'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$rcv_row = $sql->fetch($q_result)) {
			$rcv_row['qty'] = 0;
		}


		#####   ��s��Ʈw���e

		$q_str = "UPDATE ".$table." SET rcv_qty = '".$rcv_row['qty'].
								"' WHERE id= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_link_qty($id, $qty)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_link_qty($id, $qty) {

		$sql = $this->sql;

		$q_str = "UPDATE rcv_po_link SET qty = '".$qty."' 
				  WHERE id = ".$id;
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get($num=0) {

	$sql = $this->sql;

	// �禬�D��
	$q_str = "SELECT receive.* FROM receive	WHERE rcv_num='$num' and tw_rcv = 1";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		$op['rcv']=$row;

	// ����Login�b�����W�r
	$po_user=$GLOBALS['user']->get(0,$op['rcv']['rcv_user']);
	$op['rcv']['rcv_user_id'] = $op['rcv']['rcv_user'];
	if ($po_user['name'])$op['rcv']['rcv_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['rcv']['rcv_sub_user']);
	$op['rcv']['rcv_sub_user_id'] = $op['rcv']['rcv_sub_user'];	
	if ($po_user['name'])$op['rcv']['rcv_sub_user'] = $po_user['name'];
		
		
	// �禬���� 		
	$q_str="SELECT DISTINCT `receive_det`.*, ap.special
						FROM `receive_det`, `ap`
						WHERE `receive_det`.`rcv_num` = '$num' and receive_det.ap_num = ap.ap_num order by receive_det.id asc";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$i=$o_ii=0;
	while ($det_row = $sql->fetch($q_result)) {
		$det_row['po_num'] = str_replace("PA","PO",$det_row['ap_num']);
		if($det_row['mat_cat'] =='a')
			$det_row['sname'] = $GLOBALS['acc']->get_fields("acc_name","where id = ".$det_row['mat_id']);
		else
			$det_row['sname'] = $GLOBALS['lots']->get_fields("lots_name","where id= ".$det_row['mat_id']);
			
		$po_qty=$un_qty=$rcv_qty=$ship_qty=0;
		
		$ship_qty = $this->get_ship_qty($det_row['ship_link_id']);
		
		$po_det = $this->get_po_det($det_row['ap_num'],$det_row['mat_id'],$det_row['color'],$det_row['size']);
		
		#���q�渹�X�B�q��ƶq�Bpo_qty
		$q_str = "select rcv_po_link.id, rcv_po_link.qty, rcv_po_link.ord_num, ap_det.po_qty
				  from rcv_po_link,ap_det
				  where rcv_id = ".$det_row['id']." and ap_det.id = rcv_po_link.po_id";
		$link_res = $sql->query($q_str);
		$det_row['link_qty'] = array();
		while($link_row = $sql->fetch($link_res)){
			$det_row['link_qty'][] = $link_row;
		}
		
		#�P�@�i SHIP ���i������禬,�ҥH�n�X�p�P�@�iSHIP���P�@��item
		$where_str = "receive.rcv_num=receive_det.rcv_num and receive_det.ship_link_id='".$det_row['ship_link_id']."' and receive.tw_rcv=1 group by receive_det.ship_link_id";
		$ship_rcvd_qty = $this->get_det_field("sum(qty) as qty","receive,receive_det",$where_str);
		
		$det_row['ship_qty'] = $ship_qty;
		$det_row['un_qty'] = $ship_qty - $ship_rcvd_qty['qty'] + $det_row['qty'];
		$det_row['po'] = $po_det;
		
		$op['rcv_det'][$i] = $det_row;
		$op['rcv_det'][$i]['i'] = $i;
		
		$i++;
	}
		
		
	
	#�禬���� -- LOG��		
	$q_str="SELECT receive_log.*	FROM `receive_log`  WHERE `rcv_num` = '".$num."' ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		//����Login�b�����W�r
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
		if ($po_user['name'])$row1['user'] = $po_user['name'];
			$op['rcv_log'][$i]=$row1;
		$i++;
	}
		
	#�禬���� -- ���[�ɮ�
	$q_str="SELECT receive_file.*	FROM `receive_file`  WHERE `rcv_id` = '".$op['rcv']['id']."' ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$op['done'][]=$row1;
	}		

	return $op;
} // end func

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_det($ap_num,$mat_id,$color,$size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_det($ap_num,$mat_id,$color,$size) {

		$sql = $this->sql;

		$q_str = "SELECT distinct sum(po_qty) as po_qty, mat_cat, eta, prics, po_unit, prc_unit
				  FROM ap_det
				  WHERE ap_num = '".$ap_num."' and mat_id = ".$mat_id." and color = '".$color."' and size = '".$size."'
				  group by ap_num,mat_id,color,size";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}

		return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_num($id,$table) {

		$sql = $this->sql;

		$q_str = "SELECT wi.wi_num, ap_det.bom_id
							FROM ap_det, wi, $table WHERE  ap_det.bom_id = $table.id AND $table.wi_id = wi.id AND ap_det.id='$id'";
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

		return $row;
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
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if($mode==0){
			$q_header = "SELECT receive.* FROM receive ";
		}
		if($mode==1){
			$q_header = "SELECT distinct receive.* FROM receive, receive_det ";
		}
		if($mode==2){
			$q_header = "SELECT distinct receive.* FROM receive, receive_det, rcv_po_link ";
		}
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("receive.id DESC");
		$srh->row_per_page = 15;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}
	else{
		$pagesize=10;
		if (isset($argv['PHP_sr_startno']) && $argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

	if($mode==1){
		$srh->add_where_condition("receive_det.rcv_num = receive.rcv_num ");
		$argv['SCH_po'] = str_replace("PO","PA",$argv['SCH_po']);
		$srh->add_where_condition("receive_det.ap_num like '%".$argv['SCH_po']."%'");
	}
	if($mode==2){
		$srh->add_where_condition("receive_det.rcv_num = receive.rcv_num ");
		$srh->add_where_condition("rcv_po_link.rcv_id = receive_det.id");
		$srh->add_where_condition("rcv_po_link.ord_num like '%".$GLOBALS['SCH_order']."%'");
	}
	
	//���� : �~�ȳ���
		if ($user_dept=='LY' || $user_dept =='HJ' || $user_dept =='CF')	
			$srh->add_where_condition("receive.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");

   
		if ($str = strtoupper($argv['SCH_num']) )  { 
			$srh->add_where_condition("receive.rcv_num LIKE '%$str%'", "SCH_num",$str,"search receive #:[ $str ] "); 
		}
		
		if ($str = $argv['SCH_supl'] )  { 
			$srh->add_where_condition("receive.sup_no = '$str'", "SCH_supl",$str,"search supplier=[ $str ]. "); 
		} 
		/* if ($str = $argv['SCH_fab'] )  { 
			$srh->add_where_condition("receive_det.mat_code like '%$str%'", "SCH_fab",$str,"search Fabric# :[ $str ] "); 
			}
		if ($str = $argv['SCH_acc'] )  { 
			$srh->add_where_condition("receive_det.mat_code like '%$str%'", "SCH_acc",$str,"search Accessory# :[ $str ] "); 
			}*/
		
	$srh->add_where_condition("receive.tw_rcv = '1'",$str,"search tw rcv=[ $str ]. ");
	
	$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}
	$this->msg->merge($srh->msg);
	if (!$result){   // ��d�M�L��Ʈ�
		$op['record_NONE'] = 1;
	}
	$op['rcvd'] = $result;  // ��ƿ� �ߤJ $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;
		
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
#	->search2($mode=0, $dept='',$limit_entries=0) �j�M �禬 ���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search2($mode=0, $dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT receive.* FROM receive ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("receive.id DESC");
		$srh->row_per_page = 15;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}
	else{
##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;
		if (isset($argv['PHP_sr_startno']) && $argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
	}


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

//���� : �~�ȳ���
	if ($user_dept=='LY' || $user_dept =='HJ' || $user_dept =='CF')	
		$srh->add_where_condition("receive.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");

   if ($mode==1)
   {
		if ($str = strtoupper($argv['SCH_num']) )  { 
			$srh->add_where_condition("receive.rcv_num LIKE '%$str%'", "SCH_num",$str,"search receive #:[ $str ] "); 
		}
		
		/* if ($str = $argv['SCH_supl'] )  { 
			$srh->add_where_condition("receive.sup_no = '$str'", "SCH_supl",$str,"search supplier=[ $str ]. "); 
			} 
		if ($str = $argv['SCH_fab'] )  { 
			$srh->add_where_condition("receive_det.mat_code like '%$str%'", "SCH_fab",$str,"search Fabric# :[ $str ] "); 
			}
		if ($str = $argv['SCH_acc'] )  { 
			$srh->add_where_condition("receive_det.mat_code like '%$str%'", "SCH_acc",$str,"search Accessory# :[ $str ] "); 
			}*/
	}	
   
		//$srh->add_where_condition("receive.sup_no = supl.vndr_no");
		//$srh->add_where_condition("receive.po_id = ap.id");
  
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['rcvd'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
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
	function edit($parm1) {
					
		$sql = $this->sql;

					# �ק��Ʈw
		$q_str = "UPDATE receive SET
								inv_num   = '". $parm1['inv_num']."',
								inv_date  = '". $parm1['inv_date']."'
							WHERE rcv_num = '".$parm1['rcv_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return 1;

	} // end func

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_det_field$field,$table,$where)	��X���w���O�� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_det_field($field,$table,$where) {
		$sql = $this->sql;
		$q_str="SELECT ". $field. " FROM ".$table." WHERE ".$where;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		return $row1;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_id($field1, $value1, $id, $table='receive') ��s field���� (�HID)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_id($field1, $value1, $id, $table='receive') {

		$sql = $this->sql;

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE id= '".$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func	
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_det($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_det($id) {

		$sql = $this->sql;

		$q_str = "DELETE FROM receive_det WHERE id= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return $id;
	} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_link($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_link($id) {

		$sql = $this->sql;

		$q_str = "DELETE FROM rcv_po_link WHERE rcv_id= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return $id;
	} // end func		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_del_full($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function chk_del_full($num) {

		$sql = $this->sql;

		$q_str = "SELECT id FROM receive_det WHERE rcv_num = '".$num."'";
		$q_result = $sql->query($q_str);
		if(!$row = $sql->fetch($q_result)) 
		{
			$q_str = "DELETE FROM receive WHERE rcv_num = '".$num."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}

			$q_str = "DELETE FROM receive_log WHERE rcv_num = '".$num."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}				
			return 1;
		}
		return 0;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function po_close($pa_num,$special) {

		$sql = $this->sql;
		//$ap_det = array();	
//ad_det�����ʶ���close	==> "rcv_rmk = 1"
		$q_str = "UPDATE ap_det SET rcv_rmk ='1' WHERE ap_num= '".$pa_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
//ad_special�����ʶ���close		==> "rcv_rmk = 1"	
		$q_str = "UPDATE ap_special SET rcv_rmk ='1' WHERE ap_num= '".$pa_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}			
//ap�����ʳ�Close		==> "rcv_rmk = 1"
		$q_str = "UPDATE ap SET rcv_rmk ='1' WHERE ap_num= '".$pa_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$mk = 0;
		if ($special < 2)
		{
			$q_str = "SELECT mat_cat FROM ap_det WHERE ap_det.ap_num ='".$pa_num."'";
			$q_result = $sql->query($q_str);
			$po_mat = $sql->fetch($q_result);
			if ($po_mat['mat_cat'] == 'a')
			{
				$table = "bom_acc";
				$field = "m_acc_shp";	
				$mat_cat = 'a';
			}else{
				$table = "bom_lots";
				$field = "mat_shp";	
				$mat_cat = 'l';	
			}			
//����ʪ��q��
			$q_str = "SELECT wi.wi_num FROM wi, ".$table.", ap_det WHERE ap_det.bom_id=".$table.".id 
								AND wi.id = ".$table.".wi_id AND ap_det.mat_cat = '".$mat_cat."' AND ap_det.ap_num ='".$pa_num."'";
//echo $q_str."<br>";
			$q_result = $sql->query($q_str);
			while ($row1 = $sql->fetch($q_result)) $ord[]=$row1['wi_num'];
		}else{
//����ʪ��q�� �q�S����ʶ��ا�
			$q_str = "SELECT ord_num, mat_cat FROM ap_special WHERE ap_num ='".$pa_num."'";
//echo $q_str."<br>";
			$q_result = $sql->query($q_str);
			while ($row1 = $sql->fetch($q_result)) 
			{
				$ord[]=$row1['ord_num'];
				if ($row1['mat_cat'] == 'a')
				{
					$table = "bom_acc";
					$field = "m_acc_shp";	
					$mat_cat = 'a';
				}else{
					$table = "bom_lots";
					$field = "mat_shp";	
					$mat_cat = 'l';	
				}	// end if
			} // end while
		} // end if ($special < 2)
//		$f1 = $this->add_ord_rcvd($table,$field,$mat_cat,$ord);
		return 1;
	} // end func

/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_rcvd($table,$field,$mat_cat,$ord) {

		$sql = $this->sql;
		$mk = 0;

		for ($i=0; $i<sizeof($ord); $i++)
		{
			$q_str = "SELECT rcv_rmk, rcv_qty FROM ap_det,". $table.", wi 
								WHERE ap_det.bom_id = ".$table.".id AND ".$table.".wi_id = wi.id 
								AND ap_det.mat_cat ='".$mat_cat."' AND wi.wi_num ='".$ord[$i]."'";

			$q_result = $sql->query($q_str);		
			while ($row = $sql->fetch($q_result)) {	
		//		if ($row['rcv_rmk']==0) $mk = 1;	
				if ($row['rcv_qty']== 0) $mk = 1;
			}

			$q_str = "SELECT rcv_rmk, rcv_qty FROM ap_special WHERE ord_num ='".$ord[$i]."' AND mat_cat ='".$mat_cat."'";

			$q_result = $sql->query($q_str);		
			while ($row = $sql->fetch($q_result)) {	
		//		if ($row['rcv_rmk']==0) $mk = 1;	
				if ($row['rcv_qty']== 0) $mk = 1;
			}
			
			if ($mk == 0)
			{
				$q_str = "UPDATE pdtion SET ".$field." = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";
//echo $q_str;
				$q_result = $sql->query($q_str);		
			}
			$mk = 0;
			
		}

		return 1;
	} // end func
*/	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_rcvd($table,$field,$mat_cat,$ord,$acc_cat=0,$submit=1)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_rcvd($table,$field,$mat_cat,$ord,$acc_cat=0,$submit=1) {


		$sql = $this->sql;
		$mk = 0;

		for ($i=0; $i<sizeof($ord); $i++)
		{
			
			if( $mat_cat == 'l' )
			{
				$ship_det = array();
//�P�_�O�_�s�bBOM
				$q_str = "SELECT bom_lots.id  FROM bom_lots, lots_use
									WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND lots_use.support = 0
												AND lots_use.smpl_code  ='".$ord[$i]."' ";

				$q_result = $sql->query($q_str);
				if (!$row = $sql->fetch($q_result)) {	
					$mk = 1;					
				}	

//�P�_BOM�O�_��������
				$q_str = "SELECT ap_mark  FROM bom_lots, lots_use
									WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND lots_use.support = 0
												AND ap_mark = '' AND lots_use.smpl_code  ='".$ord[$i]."' ";

				$q_result = $sql->query($q_str);
				if ($row = $sql->fetch($q_result)) {	
					$mk = 1;					
				}				

				if($mk == 0)
				{
					//���oWI ID
					$q_str = "SELECT bom_lots.wi_id  FROM bom_lots, lots_use
										WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND lots_use.support = 0
													AND lots_use.smpl_code  ='".$ord[$i]."' ";
					$q_result = $sql->query($q_str);
					$row = $sql->fetch($q_result);
					$wi_id = $row['wi_id'];
							

					$q_str = "SELECT ap_det.rcv_qty 
										FROM ap_det, bom_lots, ap
										WHERE ap_det.bom_id = bom_lots.id  AND ap.ap_num = ap_det.ap_num
										AND bom_lots.dis_ver = 0														
										AND ap.status >= 0	AND ap_det.mat_cat ='l'	
										AND ap_det.rcv_qty = 0 AND bom_lots.wi_id  ='".$wi_id."'";
//echo $q_str."<br>";
					$q_result = $sql->query($q_str);		
				
					if ($row = $sql->fetch($q_result)) {	
						$mk = 1;					
					}	
					
				}
				if($mk == 0)	//��submit�ɻݽT�{�O����submit
				{
					$q_str = "SELECT receive.status, receive.rcv_num 
										FROM  receive, receive_det, rcv_po_link, ap_det, bom_lots, ap
										WHERE ap_det.bom_id = bom_lots.id 
										AND rcv_po_link.po_id = ap_det.id AND rcv_po_link.rcv_id = receive_det.id
										AND receive_det.rcv_num = receive.rcv_num AND receive.po_id = ap.id
										AND ap_det.ap_num = ap.ap_num AND receive.status  = 0
										AND ap_det.mat_cat ='l' AND bom_lots.wi_id ='".$wi_id."'";
//echo $q_str."<br>";
					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) {	
						if(isset($row['status']))$mk = 1;
									
					}
					
				}
				

				$q_str = "SELECT ap.id 
									FROM ap_special, ap
									WHERE ap_special.ap_num = ap.ap_num AND ap_special.rcv_qty = 0 AND ap.status > 0
												AND ord_num ='".$ord[$i]."' AND mat_cat ='l'";

				$q_result = $sql->query($q_str);		
				if ($row = $sql->fetch($q_result)) {	
					$mk = 1;					
				}	
				
				if($mk == 0)	//��submit�ɻݽT�{�O����submit
				{
					$q_str = "SELECT receive.status FROM receive, receive_det, rcv_po_link, ap_special, ap
										WHERE rcv_po_link.po_id = ap_special.id AND rcv_po_link.rcv_id = receive_det.id
										AND receive_det.rcv_num = receive.rcv_num AND receive.po_id = ap.id
										AND ap_special.ap_num = ap.ap_num AND receive.status = 0
										AND ap_special.mat_cat ='l' AND ap_special.ord_num ='".$ord[$i]."'";
//echo $q_str."<br>";
					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) {	
						$mk = 1;					
					}	
				
				}
		
				if ($mk == 0)		//���Ƭ��禬������J�禬��
				{
					//�O���禬���
					$q_str = "UPDATE pdtion SET mat_shp = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";
//echo $q_str."<br>";
					$q_result = $sql->query($q_str);		
					
					
				
				
				}
				
				
				
				
				
/*
				$ship_date = $GLOBALS['order']->get_field_value('mat_rcv', '', $ord[$i],'pdtion');
				$ship_way = $GLOBALS['order']->get_field_value('mat_ship_way', '', $ord[$i],'pdtion');
				for($s=0; $s<sizeof($ship_det); $s++)//�Yship���ƦP�禬���Ʈ�,����ship���
				{
					if($ship_date == $ship_det[$s]['ship_date'] && $ship_way == $ship_det[$s]['ship_way'])
					{
						$q_str = "UPDATE pdtion SET mat_rcv = NULL, mat_ship_way = '' WHERE order_num = '".$ord[$i]."'";
						$q_result = $sql->query($q_str);	
						break;						
					}
				}
*/				
				$mk = 0;
			}else{
//�D�n�Ʈ�
			if ( $acc_cat == 2 || $acc_cat == 3)
			{							
				$ship_det = array();
				$mk2=1;
//�P�_�O�_��BOM
				$q_str = "SELECT bom_acc.id FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '1' AND acc_use.support = 0	
									AND acc_use.smpl_code ='".$ord[$i]."'";			
//echo $q_str."<br>";	
				$q_result = $sql->query($q_str);		
				if (!$row = $sql->fetch($q_result)) $mk = 1;	

//�P�_BOM�O�_�ʶR����
				$q_str = "SELECT ap_mark FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '1' AND acc_use.support = 0	AND ap_mark = ''
									AND acc_use.smpl_code ='".$ord[$i]."'";			
//echo $q_str."<br>";	
				$q_result = $sql->query($q_str);		
				if ($row = $sql->fetch($q_result)) $mk = 1;	
				if($mk == 0)
				{
					$q_str = "SELECT bom_acc.wi_id as id FROM bom_acc, acc_use
										WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
										AND acc_use.acc_cat = '1' AND acc_use.support = 0	
										AND acc_use.smpl_code ='".$ord[$i]."' LIMIT 1";						
					$q_result = $sql->query($q_str);		
					if (!$wi = $sql->fetch($q_result)) $mk = 1;	

				}
				if($mk == 0)
				{
					$q_str = "SELECT ap_det.rcv_qty 
										FROM ap_det, bom_acc, ap, acc_use 
										WHERE ap_det.bom_id = bom_acc.id AND ap.ap_num = ap_det.ap_num 
										AND bom_acc.acc_used_id = acc_use.id  AND acc_use.acc_cat = '1' 
										AND acc_use.support = 0 AND bom_acc.dis_ver = 0 										
										AND ap.status >= 0 AND ap_det.rcv_qty = 0
										AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";
//echo $q_str."<br>";	
					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) $mk = 1;	
				}
					
					//if($mk2 == 1)$mk = $mk2;
					
					if($mk == 0)	//��submit�ɻݽT�{�O����submit
					{
						$q_str = "SELECT receive.status 
											FROM   receive, receive_det, rcv_po_link, ap_det, bom_acc, ap, acc_use
											WHERE ap_det.bom_id = bom_acc.id 											
											AND rcv_po_link.po_id = ap_det.id AND rcv_po_link.rcv_id = receive_det.id																						
											AND receive_det.rcv_num = receive.rcv_num AND receive.po_id = ap.id
											AND ap_det.ap_num = ap.ap_num AND bom_acc.acc_used_id = acc_use.id 
											AND acc_use.acc_cat = '1' AND acc_use.support = 0
											AND receive.status = 0
											AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";
//echo $q_str."<br>";	
						$q_result = $sql->query($q_str);		
						if ($row = $sql->fetch($q_result)) $mk = 1;
					}
						
					if ($mk == 0)
					{
						$q_str = "UPDATE pdtion SET m_acc_shp = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";

						$q_result = $sql->query($q_str);		
					}
/*					
					$ship_date = $GLOBALS['order']->get_field_value('m_acc_rcv', '', $ord[$i],'pdtion');
					$ship_way = $GLOBALS['order']->get_field_value('m_acc_ship_way', '', $ord[$i],'pdtion');
					for($s=0; $s<sizeof($ship_det); $s++)
					{
						if($ship_date == $ship_det[$s]['ship_date'] && $ship_way == $ship_det[$s]['ship_way'])
						{
							$q_str = "UPDATE pdtion SET m_acc_rcv = NULL, m_acc_ship_way = '' WHERE order_num = '".$ord[$i]."'";
							$q_result = $sql->query($q_str);							
							break;
						}
					}					
*/					
					$mk = 0;	
					
				}
//��L�Ʈ�		
				if ( $acc_cat == 1 || $acc_cat == 3)
				{
					
				$ship_det = array();
				$mk2=1;
//�P�_BOM�O�_�ʶR����
				$q_str = "SELECT bom_acc.ap_mark FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '0' AND acc_use.support = 0
									AND acc_use.smpl_code ='".$ord[$i]."'";			
//echo $q_str."<br>";	
				$q_result = $sql->query($q_str);		
				if (!$row = $sql->fetch($q_result)) $mk = 1;	

//�P�_BOM�O�_�ʶR����
				$q_str = "SELECT ap_mark FROM bom_acc, acc_use
									WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
									AND acc_use.acc_cat = '0' AND acc_use.support = 0	AND ap_mark = ''
									AND acc_use.smpl_code ='".$ord[$i]."'";			
//echo $q_str."<br>";	
				$q_result = $sql->query($q_str);		
				if ($row = $sql->fetch($q_result)) $mk = 1;	
				if($mk == 0)
				{
					$q_str = "SELECT bom_acc.wi_id as id FROM bom_acc, acc_use
										WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 
										AND acc_use.acc_cat = '0' AND acc_use.support = 0	
										AND acc_use.smpl_code ='".$ord[$i]."' LIMIT 1";						
//echo $q_str."<br>";	
					$q_result = $sql->query($q_str);		
					if (!$wi = $sql->fetch($q_result)) $mk = 1;	

				}
				if($mk == 0)
				{
					$q_str = "SELECT ap_det.rcv_qty 
										FROM ap_det, bom_acc, ap, acc_use
										WHERE ap_det.bom_id = bom_acc.id AND ap.ap_num = ap_det.ap_num 
										AND bom_acc.acc_used_id = acc_use.id
										AND acc_use.acc_cat = '0' AND acc_use.support = 0
										AND bom_acc.dis_ver = 0 										
										AND ap.status >= 0 AND ap_det.rcv_qty = 0
										AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";
//echo $q_str."<br>";	
					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) $mk = 1;	
				}
					
					//if($mk2 == 1)$mk = $mk2;
					
					if($mk == 0)	//��submit�ɻݽT�{�O����submit
					{
						$q_str = "SELECT receive.status 
											FROM   receive, receive_det, rcv_po_link, ap_det, bom_acc, ap, acc_use
											WHERE ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id
											AND rcv_po_link.po_id = ap_det.id AND rcv_po_link.rcv_id = receive_det.id																						
											AND receive_det.rcv_num = receive.rcv_num AND receive.po_id = ap.id
											AND ap_det.ap_num = ap.ap_num 
											AND acc_use.acc_cat = '0' AND acc_use.support = 0
											AND receive.status = 0
											AND ap_det.mat_cat ='a' AND bom_acc.wi_id ='".$wi['id']."'";
//echo $q_str."<br>";	
						$q_result = $sql->query($q_str);		
						if ($row = $sql->fetch($q_result)) $mk = 1;
					}

					$q_str = "SELECT  rcv_qty
										FROM ap_special, ap 
										WHERE ap.ap_num = ap_special.ap_num AND ord_num ='".$ord[$i]."'AND 
													rcv_qty = 0 AND mat_cat ='a' AND ap.status > 0";
//echo $q_str."<br>";	
					$q_result = $sql->query($q_str);		
					if ($row = $sql->fetch($q_result)) $mk = 1;
					
					if($mk == 0)	//��submit�ɻݽT�{�O����submit
					{			
						$q_str = "SELECT receive.status 
											FROM  receive, receive_det, rcv_po_link, ap_special, ap
											WHERE rcv_po_link.po_id = ap_special.id AND rcv_po_link.rcv_id = receive_det.id
											AND receive_det.rcv_num = receive.rcv_num AND receive.po_id = ap.id
											AND ap_special.ap_num = ap.ap_num AND receive.status = 0
											AND ap_special.mat_cat ='a' AND ap_special.ord_num ='".$ord[$i]."'";
//echo $q_str."<br>";	
						$q_result = $sql->query($q_str);		
						if ($row = $sql->fetch($q_result)) $mk = 1;
					}
					if ($mk == 0)
					{
						$q_str = "UPDATE pdtion SET acc_shp = '".date('Y-m-d')."' WHERE order_num = '".$ord[$i]."'";
//echo $q_str;
						$q_result = $sql->query($q_str);		
					}
/*
					$ship_date = $GLOBALS['order']->get_field_value('acc_rcv', '', $ord[$i],'pdtion');
					$ship_way = $GLOBALS['order']->get_field_value('acc_ship_way', '', $ord[$i],'pdtion');
					for($s=0; $s<sizeof($ship_det); $s++)
					{
						if($ship_date == $ship_det[$s]['ship_date'] && $ship_way == $ship_det[$s]['ship_way'])
						{
							$q_str = "UPDATE pdtion SET acc_rcv = NULL, acc_ship_way = '' WHERE order_num = '".$ord[$i]."'";
							$q_result = $sql->query($q_str);							
							break;
						}
					}	//end for ($s=0; $s<sizeof($ship_det); $s++)
*/							
				}// end if ( $acc_cat == 1 || $acc_cat == 3)

			}




			$mk = 0;	
		}

		return 1;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_po_rcvd($rcv_num) {

		$sql = $this->sql;
		//$ap_det = array();	
	
		$q_str="SELECT rcv_po_link.po_id, ap.special
						FROM receive_det, rcv_po_link, receive, ap
						WHERE receive_det.id = rcv_po_link.rcv_id AND receive_det.rcv_num = receive.rcv_num AND
						      receive.po_id = ap.id AND receive_det.rcv_num ='$rcv_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {	
			$rcv[]=$row1;
		}
		for ($i =0; $i< sizeof($rcv); $i++)
		{
			if ($rcv[$i]['special'] =='0'){$table = 'ap_det';}else{$table='ap_special';}
			
			$q_str="SELECT ap_num,po_qty, rcv_qty	FROM $table WHERE id = ".$rcv[$i]['po_id'];
			$q_result = $sql->query($q_str);		
			$row = $sql->fetch($q_result);			
			if ($row['po_qty'] <= $row['rcv_qty'])
			{
				$q_str = "UPDATE $table SET rcv_rmk ='1' WHERE id= '".$rcv[$i]['po_id']."'";

				$q_result = $sql->query($q_str);
			}		
		//�P�_��i���ʳ�O�_�禬���F	
		$q_str ="SELECT id FROM $table WHERE rcv_rmk ='0' AND ap_num ='".$row['ap_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row2 = $sql->fetch($q_result)) {
			$q_str = "UPDATE ap SET rcv_rmk ='1' WHERE ap_num= '".$row['ap_num']."'";
			$q_result = $sql->query($q_str);
		}
		
	}	
		return 1;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_ord_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_ord_rcvd($rcv_num,$ord_num,$mat_cat) {

		$sql = $this->sql;
		//$ap_det = array();	
		$acc_cat = 0;
/*
		$q_str="SELECT rcv_po_link.po_id, receive_det.mat_code, ap.special  
						FROM receive_det, rcv_po_link, receive, ap 	
						WHERE receive_det.id = rcv_po_link.rcv_id AND receive_det.rcv_num = receive.rcv_num AND 
						      receive.po_id = ap.id AND receive_det.rcv_num ='$rcv_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {	
			$rcv[]=$row1;
			
		}

		if (substr($rcv[0]['mat_code'],0,1) == 'A')
		{
			$table = "bom_acc";
			$field = "m_acc_shp";	
			$mat_cat = 'a';
			$tmp1=$tmp2=0;
			
			if ($rcv[0]['special'] < '2' )
			{
				$q_str="SELECT acc_use.acc_cat  
								FROM receive_det, rcv_po_link, receive, ap_det, ap, bom_acc, acc_use
								WHERE receive_det.id = rcv_po_link.rcv_id AND receive_det.rcv_num = receive.rcv_num AND 											
						    		  receive.po_id = ap.id AND rcv_po_link.po_id = ap_det.id AND 
						    		  ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id AND
						    		  receive_det.rcv_num ='$rcv_num'";
				if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error ! Database can't access!");
					$this->msg->merge($sql->msg);
					return false;    
				}		
				while ($row1 = $sql->fetch($q_result)) {	
					if($row1['acc_cat'] == '1') $tmp1 = 2;
					if($row1['acc_cat'] == '0') $tmp2 = 1;			
				}				
			}
			$acc_cat = $tmp1+$tmp2;
		}else{
			$table = "bom_lots";
			$field = "mat_shp";			
			$mat_cat = 'l';
			
			
		}

		$ord = array(); $o = 0; $mk =0;
		for ($i =0; $i< sizeof($rcv); $i++)
		{
//			if ($rcv[$i]['po_cat'] =='0'){$table = 'ap_det';}else{$table='ap_special';}

				if ($rcv[$i]['special'] < '2' )
				{
					$q_str = "SELECT wi_num as ord_num FROM ap_det,". $table.", wi 
										WHERE ap_det.bom_id = ".$table.".id AND ".$table.".wi_id = wi.id 
											AND ap_det.mat_cat ='".$mat_cat."' AND ap_det.id ='".$rcv[$i]['po_id']."'";

					$q_result = $sql->query($q_str);		
					$row = $sql->fetch($q_result);
				}else{
					$q_str = "SELECT ord_num FROM ap_special WHERE ap_special.id = '".$rcv[$i]['po_id']."'";
					$q_result = $sql->query($q_str);		
					$row = $sql->fetch($q_result);
				}
				for ($k=0; $k<sizeof($ord); $k++)
				{
					if ($ord[$k] == $row['ord_num']) $mk = 1;
				}
				if ($mk == 0) $ord[] = $row['ord_num'];
				$mk = 0;
					
		}
*/		
		$acc_cat = 3;
		$f1 = $this->add_ord_rcvd('','',$mat_cat,$ord_num,$acc_cat);

		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->change_currency($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_currency($rcv_num) {

		$sql = $this->sql;	
	
		$q_str="SELECT receive_det.po_id, receive_det.ap_num, ap.special 
				FROM receive_det, ap 
				WHERE rcv_num ='$rcv_num' and ap.ap_num=receive_det.ap_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}

		while($row = $sql->fetch($q_result)){
			$row['special'] == 2 ? $table = 'ap_special' : $table = 'ap_det';
			$q_str="SELECT rcv_po_link.id,rcv_po_link.qty,rcv_po_link.po_id,".$table.".prics as price,".$table.".po_unit,".$table.".prc_unit, ap.currency
						 FROM receive_det, rcv_po_link, ap, ".$table."
						 WHERE receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id = ".$table.".id 
								AND ".$table.".ap_num=receive_det.ap_num AND ap.ap_num=receive_det.ap_num AND receive_det.rcv_num ='$rcv_num'";


			if (!$q_result2 = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;
			}				
			while ($row1 = $sql->fetch($q_result2)) {	
				$rcv[]=$row1;
			}
			$TODAY = date('Y-m-d');
			for ($i =0; $i< sizeof($rcv); $i++)
			{
				$rate = 0;
				if($rcv[$i]['prc_unit'] <> '') $rcv[$i]['price'] = change_unit_price($rcv[$i]['prc_unit'],$rcv[$i]['po_unit'],$rcv[$i]['price']);

				# �p����B���⦨�x��
				$tmp_price = $GLOBALS['rate']->change_rate( $rcv[$i]['currency'],$TODAY,$rcv[$i]['price']);
				if($rcv[$i]['currency'] <> 'NTD') $rate = $GLOBALS['rate']->get_rate($rcv[$i]['currency'], $TODAY);
				if($rate > 0) $rcv[$i]['price'] = $rcv[$i]['price'] * $rate;
				
				$amount = $rcv[$i]['price'] * $rcv[$i]['qty'];
				$amount = number_format($amount,2,'.','');
				$this->update_field_id('rcv_po_link.amount', $amount, $rcv[$i]['id'], 'rcv_po_link');
				$this->update_field_id('rcv_po_link.currency',$rcv[$i]['currency'], $rcv[$i]['id'], 'rcv_po_link');
				$this->update_field_id('rcv_po_link.rate',$rate, $rcv[$i]['id'], 'rcv_po_link');
			}
		}
		return 1;
	} // end func
	
/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function back_po_rcvd($rcv_num) {

		$sql = $this->sql;
		//$ap_det = array();	
	
		$q_str="SELECT po_cat, po_id
						FROM receive_det
						WHERE receive_det.rcv_num ='$rcv_num'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {	
			$rcv[]=$row1;
		}
		for ($i =0; $i< sizeof($rcv); $i++)
		{
			if ($rcv[$i]['po_cat'] =='0'){$table = 'ap_det';}else{$table='ap_special';}
			$id = explode ('|',$rcv[$i]['po_id']);
			$q_str="SELECT ap_num	FROM $table WHERE id = ".$id[0];					
			$q_result = $sql->query($q_str);		
			$row = $sql->fetch($q_result);
			$ap_num = $row['ap_num'];
			for ($j=0; $j<sizeof($id); $j++)
			{
					$q_str = "UPDATE $table SET rcv_rmk ='0' WHERE id= '".$id[$j]."'";
					$q_result = $sql->query($q_str);
			}
			
		}
		$q_str = "UPDATE ap SET rcv_rmk ='0' WHERE ap_num= '".$ap_num."'";
		$q_result = $sql->query($q_str);
		return 1;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po_rcvd($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function back_ord_rcvd($rcv_num) {

		$sql = $this->sql;
		//$ap_det = array();	
	
		$q_str="SELECT po_cat, rcv_po_link.po_id as po_det_id, mat_code, receive.po_id 
						FROM receive_det, rcv_po_link,receive 	
						WHERE receive_det.rcv_num = receive.rcv_num AND receive_det.id = rcv_po_link.rcv_id AND 
									receive_det.rcv_num ='$rcv_num'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {	
			$rcv[]=$row1;
			
		}

		if (substr($rcv[0]['mat_code'],0,1) == 'A')
		{
			$table = "bom_acc";
			$field = "m_acc_shp";	
			$mat_cat = 'a';
		}else{
			$table = "bom_lots";
			$field = "mat_shp";			
			$mat_cat = 'l';
		}

		$ord = array(); $o = 0; $mk =0;
		for ($i =0; $i< sizeof($rcv); $i++)
		{
//			if ($rcv[$i]['po_cat'] =='0'){$table = 'ap_det';}else{$table='ap_special';}
			$id = explode ('|',$rcv[$i]['po_id']);
			for ($j=0; $j<sizeof($id); $j++)
			{
				if ($rcv[$i]['po_cat'] =='0' )
				{
					$q_str = "SELECT wi_num as ord_num FROM ap_det,". $table.", wi 
										WHERE ap_det.bom_id = ".$table.".id AND ".$table.".wi_id = wi.id 
											AND ap_det.mat_cat ='".$mat_cat."' AND ap_det.id ='".$id[$j]."'";
					$q_result = $sql->query($q_str);		
					$row = $sql->fetch($q_result);
				}else{
					$q_str = "SELECT ord_num FROM ap_special WHERE ap_special.id = '".$id[$j]."'";
					$q_result = $sql->query($q_str);		
					$row = $sql->fetch($q_result);
				}
				for ($k=0; $k<sizeof($ord); $k++)
				{
					if ($ord[$k] == $row['ord_num']) $mk = 1;
				}
				if ($mk == 0) $ord[] = $row['ord_num'];
				$mk = 0;
			}		
		}
		$mk = 0;
		for ($i=0; $i<sizeof($ord); $i++)
		{	
			if ($mk == 0)
			{
				$q_str = "UPDATE pdtion SET ".$field." = NULL WHERE order_num = '".$ord[$i]."'";
				$q_result = $sql->query($q_str);		
			}
			$mk = 0;
			
		}
		return 1;
	} // end func
*/	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_log($parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_log($parm) {
	
		$sql = $this->sql;
		$parm['des']=str_replace("'", "\'",$parm['des']);
		$q_str = "INSERT INTO receive_log (rcv_num,user,item,des,k_date) 
				  VALUES('".
							$parm['rcv_num']."','".
							$parm['user']."','".
							$parm['item']."','".
							$parm['des']."','".																													
							$parm['k_date']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't insert.");
			$this->msg->merge($sql->msg);
			return false;    
		}   			
		
		return true;
	}// end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_revd_det($ship_link_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_revd_det($ship_link_id) {

		$sql = $this->sql;
		
		$q_str = "SELECT po_supl_ship_link.po_supl_ship_id, po_supl_ship.bl_num, receive_det.qty, po_supl_ship_link.po_unit, po_supl_ship_link.l_no, 
						 po_supl_ship_link.r_no, receive.rcv_num, receive.rcv_date
				  FROM receive, receive_det, po_supl_ship_link, po_supl_ship
				  WHERE receive_det.ship_link_id = ".$ship_link_id." and po_supl_ship_link.id = receive_det.ship_link_id and 
						receive.rcv_num = receive_det.rcv_num and po_supl_ship.id = po_supl_ship_link.po_supl_ship_id";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$rcv_det[$i]=$row1;
			$i++;
		}
		return @$rcv_det;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_revd_det2($po_id, $apdet_id, $rcv_date='')	��X���wreceive�O����� RETURN qty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_revd_det2($po_id,$apdet_id,$rcv_date='') {

		$sql = $this->sql;
		
		$q_str = "SELECT sum(rcv_po_link.qty) as qty
							FROM receive, receive_det, rcv_po_link
							WHERE receive.po_id = '$po_id'
								  and receive.rcv_date like '$rcv_date%'
								  and receive_det.rcv_num=receive.rcv_num
								  and rcv_po_link.po_id='$apdet_id'
								  and rcv_po_link.rcv_id=receive_det.id 
								  group by receive_det.mat_code";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);

		return $row['qty'];
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_rpt_used($parm)	�j�M�禬���`���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_rpt_used($parm) {
		$rcv_det = array();

		$sql = $this->sql;
		$where_str='';
		if($parm['cat'] == 'F'){$table="lots";}else{$table="acc";}
//�禬�D��
		if($parm['ship']) $where_str.=" AND receive.ship ='".$parm['ship']."' ";

		$q_str = "SELECT receive_det.*, ap.currency, ap.special, ".$table.".".$table."_name as mat_name,
										 receive.rcv_sub_date,rcv_po_link.qty as rcv_qty, rcv_po_link.amount as cost, 
										 rcv_po_link.rate, rcv_po_link.currency,	rcv_po_link.id as link_id,
										 rcv_po_link.po_id as po_link_id
							FROM receive, ap, receive_det, $table, rcv_po_link 
							WHERE  ap.id =receive.po_id AND receive.rcv_num = receive_det.rcv_num 
							 AND receive_det.id = rcv_po_link.rcv_id AND ".$table.".".$table."_code = mat_code".
							$where_str." AND receive.rcv_sub_date >= '".$parm['str']."' AND receive.rcv_sub_date <= '".$parm['end'].
							"' AND receive_det.mat_code like '".$parm['cat']."%' AND receive.status = 2 order by mat_code";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			

//��ײv�|�L��
			if($row1['rate'] == 0 && $row1['currency'] <> 'NTD')
			{						 
				$row1['rate'] = $GLOBALS['rate']->get_rate($row1['currency'], $row1['rcv_sub_date']);
				if($row1['rate'] > 0)
				{
					$this->update_field_id('rate', $row1['rate'], $row1['link_id'], 'rcv_po_link');
					$row1['cost'] = $row1['cost'] * $row1['rate'];				
					$this->update_field_id('amount', $row1['cost'], $row1['link_id'], 'rcv_po_link');
				}else{
					$row1['cost'] = 0;
				}

			}

			$rcv_det[$i]=$row1;





			//���o���ʸ�T -- ���
			if($row1['special'] == 2){$table='ap_special';}else{$table='ap_det';}
			$po_det = $this->get_po_det($row1['po_link_id'],$table);
			if($po_det['prc_unit'] == '')$po_det['prc_unit'] = $po_det['po_unit'];
			$rcv_det[$i]['price'] = change_unit_price($po_det['prc_unit'],$po_det['po_unit'],$po_det['prics']);
			if($row1['currency'] <> 'NTD') $rcv_det[$i]['price']   = $po_det['prics'] * $row1['rate'];
			$rcv_det[$i]['unit'] = $po_det['po_unit'];
			$rcv_det[$i]['prc_unit'] = $po_det['prc_unit'];
	//		$rcv_det[$i]['cost'] = $rcv_det[$i]['price'] * $rcv_det[$i]['qty'];
			$i++;
		}	 
		return $rcv_det;

}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_rpt_dtl($parm)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_rpt_dtl($parm) {
		
		$rcv_det = array();
		$sql = $this->sql;
		$where_str='';
		if($parm['cat'] == 'F'){
			$table="lots";
			$cat = 'l';
			$bom_table = 'bom_lots';
		}else{
			$table="acc";
			$cat = 'a';
			$bom_table ='bom_acc';
		}
//�禬�D��
		if($parm['ship']) $where_str.=" AND receive.ship ='".$parm['ship']."' ";
//		if($parm['ord']) $where_str.=" AND wi.wi_num like '%".$parm['ord']."%' ";

/*
		$q_str = "SELECT receive_det.*, receive.rcv_sub_date, receive.ship, receive.dept, ap.ap_num, ap.currency, ap.special, ".$table.".".$table."_name as mat_name, rate.in
							FROM receive, ap, receive_det, $table LEFT JOIN rate ON (rate.date = receive.rcv_sub_date AND ap.currency = rate.currency)
							WHERE  ap.special < 2 AND ap.id =receive.po_id AND receive.rcv_num = receive_det.rcv_num AND ".$table.".".$table."_code = mat_code".
							$where_str." AND receive.rcv_sub_date >= '".$parm['str']."' AND receive.rcv_sub_date <= '".$parm['end'].
							"' AND receive_det.mat_code like '".$parm['cat']."%' AND receive.status = 2 ";
*/
/*
		$q_str = "SELECT receive_det.*, rcv_po_link.qty as rcv_po_qty, rcv_po_link.amount, rcv_po_link.currency 
										 receive.rcv_sub_date, receive.ship, receive.dept, ap.ap_num,
										 ap.currency, ap.special, ".$table.".".$table."_name as mat_name
							FROM receive, ap, receive_det, rcv_po_link, $table 
							WHERE  ap.special < 2 AND ap.id =receive.po_id AND receive.rcv_num = receive_det.rcv_num AND ".$table.".".$table."_code = mat_code".
							$where_str." AND receive.rcv_sub_date >= '".$parm['str']."' AND receive.rcv_sub_date <= '".$parm['end'].
							"' AND receive_det.mat_code like '".$parm['cat']."%' AND receive.status = 2 ";
*/

		$q_str = "SELECT receive_det.*, rcv_po_link.qty as rcv_qty, rcv_po_link.amount as cost, rcv_po_link.rate,
										 rcv_po_link.currency, receive.rcv_sub_date, ap_det.id as po_id, ap_det.prics as price,
										 receive.ship, receive.dept, ap.ap_num, ap.po_num, ap_det.po_unit as unit, ap_det.prc_unit,
										 rcv_po_link.id as link_id,
										 ap.currency, ap.special, ".$table.".".$table."_name as mat_name
							FROM receive, ap,ap_det, receive_det, rcv_po_link, $table 
							WHERE  ap.ap_num = ap_det.ap_num AND ap_det.id = rcv_po_link.po_id 
							 AND rcv_po_link.rcv_id = receive_det.id AND ap.id =receive.po_id 
							 AND receive.rcv_num = receive_det.rcv_num AND ap.special < 2
							 AND  ".$table.".".$table."_code = mat_code".$where_str."
							 AND receive.rcv_sub_date >= '".$parm['str']."' AND receive.rcv_sub_date <= '".$parm['end']."'
							 AND receive_det.mat_code like '".$parm['cat']."%' AND receive.status = 2 ";

//echo $q_str."<br>";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			if($row1['prc_unit'] == '')$row1['prc_unit'] = $row1['unit'];
			$ord_nums = $this->get_ord_num($row1['po_id'],$bom_table); //���o�q��s��
			if($row1['rate'] == 0 && $row1['currency'] <> 'NTD')
			{						 
				$row1['rate'] = $GLOBALS['rate']->get_rate($row1['currency'], $row1['rcv_sub_date']);
				if( $row1['rate'] > 0)
				{
					$this->update_field_id('rate', $row1['rate'], $row1['link_id'], 'rcv_po_link');
					$row1['cost'] =$row1['cost'] * $row1['rate'];				
					$this->update_field_id('amount', $row1['cost'], $row1['link_id'], 'rcv_po_link');
				}else{
					$row1['cost'] = 0;
				}
			}
			if($row1['currency'] <> 'NTD') $row1['price']   = $row1['price'] * $row1['rate'];
			$rcv_det[$i]=$row1;
			$rcv_det[$i]['sort']    = $ord_nums['wi_num'];
			$rcv_det[$i]['ord_num'] = $ord_nums['wi_num']; //���o�q��s��
	
			$i++;
	/*		
			//���o���ʸ�T -- ���/�q��/�ƶq
			$id = explode("|",$row1['po_id']);
			$tmp_po_qty = 0;
			$po_det = array();
			for ($j=0; $j<sizeof($id); $j++)
			{
				$tmp_po = $this->get_po_det($id[$j],'ap_det');
				$tmp_po_qty += $tmp_po['po_qty'];
				$po_det[$j] = $tmp_po;
				$ord_nums = $this->get_ord_num($id[$j],$bom_table); //���o�q��s��
				$po_det[$j]['ord_num'] = $ord_nums['wi_num']; //���o�q��s��
				$po_det[$j]['bom_id'] = $ord_nums['bom_id']; //���o�q��s��
			}
			$tmp_rcv_qty = 0;
			for ($j=0; $j<(sizeof($po_det) -1 ); $j++)  
			{				 
				//�p���@�q���禬�q
				$po_det[$j]['rcv_qty'] = $row1['qty'] * ($po_det[$j]['po_qty'] / $tmp_po_qty ); 
				$po_det[$j]['rcv_qty'] = number_format($po_det[$j]['rcv_qty'], 2, '.', '');
				$tmp_rcv_qty += $po_det[$j]['rcv_qty'];
				//�p��ײv�ഫ
			//	if ($row1['in']) $po_det[$j]['prics'] = $po_det[$j]['prics'] * $row1['in'];
				
				if ($row1['currency'] <> 'NTD')				
					$po_det[$j]['prics'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['rcv_sub_date'],$po_det[$j]['prics']);
				//�N��@�q���禬��ƥ[�J�}�C��
				if($parm['ord'] == '' || strstr($po_det[$j]['ord_num'], $parm['ord']))
				{
					$rcv_det[$i]=$row1;
					$rcv_det[$i]['sort']    = $po_det[$j]['ord_num'];
					$rcv_det[$i]['ord_num'] = $po_det[$j]['ord_num'];
					$rcv_det[$i]['bom_id']  = $po_det[$j]['bom_id'];
					$rcv_det[$i]['rcv_qty'] = $po_det[$j]['rcv_qty'];
					$rcv_det[$i]['unit']    = $po_det[$j]['po_unit'];
					$rcv_det[$i]['price']   = $po_det[$j]['prics'];
					$rcv_det[$i]['cost']    = $rcv_det[$i]['price'] * $rcv_det[$i]['rcv_qty'];
					$i++;
				}
			}
		
      //�̫�@���q���禬���
				$po_det[$j]['rcv_qty'] = $row1['qty'] - $tmp_rcv_qty;
				$po_det[$j]['rcv_qty'] = number_format($po_det[$j]['rcv_qty'], 2, '.', '');
				$tmp_rcv_qty += $po_det[$j]['rcv_qty'];
				// if ($row1['in']) $po_det[$j]['prics'] = $po_det[$j]['prics'] * $row1['in'];
	//			if ($row1['currency'] <> 'NTD') $po_det[$j]['prics'] = $po_det[$j]['prics'] * $row1['in'];
				
				if ($row1['currency'] <> 'NTD')				
					$po_det[$j]['prics'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['rcv_sub_date'],$po_det[$j]['prics']);

				if($parm['ord'] == '' || strstr($po_det[$j]['ord_num'], $parm['ord']))
				{
					$rcv_det[$i]=$row1;
					$rcv_det[$i]['sort']    = $po_det[$j]['ord_num'];
					$rcv_det[$i]['ord_num'] = $po_det[$j]['ord_num'];
					$rcv_det[$i]['bom_id']  = $po_det[$j]['bom_id'];
					$rcv_det[$i]['rcv_qty'] = $po_det[$j]['rcv_qty'];
					$rcv_det[$i]['unit']    = $po_det[$j]['po_unit'];
					$rcv_det[$i]['price']   = $po_det[$j]['prics'];
					$rcv_det[$i]['cost']    = $rcv_det[$i]['price'] * $rcv_det[$i]['rcv_qty'];
					$i++;
				}
		*/
		}	 



		$where_str='';
//�禬�D��
		if($parm['ship']) $where_str.=" AND receive.ship ='".$parm['ship']."' ";
		if($parm['ord']) $where_str.=" AND ap_special.ord_num like '%".$parm['ord']."%' ";
		if($parm['cat'] == 'F'){
			$table="lots";
			$cat = 'l';
		}else{
			$table="acc";
			$cat = 'a';
		}

/*
		$q_str = "SELECT receive_det.*, receive.rcv_sub_date, receive.ship, receive.dept,ap_special.ord_num, ap.ap_num, ap.currency, ap.special, ".$table.".".$table."_name as mat_name, rate.in
							FROM  receive, ap, ap_special, receive_det, $table LEFT JOIN rate ON (rate.date = receive.rcv_sub_date AND ap.currency = rate.currency)
							WHERE ap.ap_num = ap_special.ap_num AND ap.special = 2 AND  receive_det.po_id = ap_special.id
							AND ap.id =receive.po_id AND receive.rcv_num = receive_det.rcv_num AND ".$table.".".$table."_code = receive_det.mat_code".
							$where_str." AND receive.rcv_sub_date >= '".$parm['str']."' AND receive.rcv_sub_date <= '".$parm['end'].
							"' AND receive_det.mat_code like '".$parm['cat']."%' AND receive.status = 2 order by ap_special.ord_num";


		$q_str = "SELECT receive_det.*, receive.rcv_sub_date, receive.ship, receive.dept,ap_special.ord_num, ap.ap_num, ap.currency, ap.special, ".$table.".".$table."_name as mat_name
							FROM  receive, ap, ap_special, receive_det, $table 
							WHERE ap.ap_num = ap_special.ap_num AND ap.special = 2 AND  receive_det.po_id = ap_special.id
							AND ap.id =receive.po_id AND receive.rcv_num = receive_det.rcv_num AND ".$table.".".$table."_code = receive_det.mat_code".
							$where_str." AND receive.rcv_sub_date >= '".$parm['str']."' AND receive.rcv_sub_date <= '".$parm['end'].
							"' AND receive_det.mat_code like '".$parm['cat']."%' AND receive.status = 2 order by ap_special.ord_num";
*/


		$q_str = "SELECT receive_det.*, rcv_po_link.currency, rcv_po_link.amount as cost, rcv_po_link.rate,
											rcv_po_link.qty as rcv_qty, receive.rcv_sub_date, receive.ship, 
											receive.dept,ap_special.ord_num, ap.po_num, ap.ap_num, ap_special.po_unit as unit,
											ap_special.prics as price, ap_special.prc_unit,
											 ".$table.".".$table."_name as mat_name
							FROM  receive, ap, ap_special, receive_det, rcv_po_link, $table 
							WHERE ap.ap_num = ap_special.ap_num AND ap_special.id = rcv_po_link.po_id 
							 AND  receive_det.po_id = ap_special.id AND receive_det.id = rcv_po_link.rcv_id
							 AND ap.id =receive.po_id AND receive.rcv_num = receive_det.rcv_num
							 AND ap.special = 2  AND ".$table.".".$table."_code = receive_det.mat_code".
							$where_str." AND receive.rcv_sub_date >= '".$parm['str']."' AND receive.rcv_sub_date <= '".$parm['end'].
							"' AND receive_det.mat_code like '".$parm['cat']."%' AND receive.status = 2 order by ap_special.ord_num";
//echo $q_str."<br>";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row1 = $sql->fetch($q_result)) {
			if($row1['prc_unit'] == '')$row1['prc_unit'] = $row1['po_unit'];

			if($row1['rate'] == 0 && $row1['currency'] <> 'NTD')
			{						 
				$row1['rate'] = $GLOBALS['rate']->get_rate($row1['currency'], $row1['rcv_sub_date']);
				if($row1['rate'] > 0)
				{
					$this->update_field_id('rate', $row1['rate'], $row1['link_id'], 'rcv_po_link');
					$row1['cost'] = $row1['cost'] * $row1['rate'];				
					$this->update_field_id('amount', $row1['cost'], $row1['link_id'], 'rcv_po_link');
				}else{
					$row1['cost'] = 0;
				}

			}
			if($row1['currency'] <> 'NTD') $row1['price'] = $row1['price'] * $row1['rate'];

			$rcv_det[$i]=$row1;
			$rcv_det[$i]['sort'] = $row1['ord_num'];

			$i++;
	/*
			//���o���ʸ�T -- ���
			$id = explode("|",$row1['po_id']);			
			if($row1['special'] == 2){$table='ap_special';}else{$table='ap_det';}
			$po_det = $this->get_po_det($id[0],$table);
			$rcv_det[$i]['price'] = $po_det['prics'];
			//if ($row1['in']) $rcv_det[$i]['price'] = $rcv_det[$i]['price'] * $row1['in'];
			//if ($row1['currency'] <> 'NTD') $rcv_det[$i]['price'] = $rcv_det[$i]['price'] * $row1['in'];
			if ($row1['currency'] <> 'NTD')				
					$rcv_det[$i]['price'] = $GLOBALS['rate']->change_rate( $row1['currency'], $row1['rcv_sub_date'],$rcv_det[$i]['price']);

			$rcv_det[$i]['unit'] = $po_det['po_unit'];
			$rcv_det[$i]['rcv_qty'] = $rcv_det[$i]['qty'];
			$rcv_det[$i]['cost'] = $rcv_det[$i]['price'] * $rcv_det[$i]['rcv_qty'];
			
			$i++;
	*/		
		}	 
		return $rcv_det;

}// end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_link_field($id=0)	��X���w�O�� bom �D�Ƭ������ RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_link_field($field,$table,$where) {
		$row=array();
		$sql = $this->sql;
		$q_str="SELECT ". $field. " FROM ".$table." WHERE ".$where;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row1 = $sql->fetch($q_result))
		{
			$row[] = $row1;
		}
		return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	��X���w�O�� bom �D�Ƭ������ RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_table_name($id) {
		$row=array();
		$sql = $this->sql;
		$q_str="SELECT ap.special FROM receive, receive_det, ap
						WHERE receive_det.rcv_num = receive.rcv_num AND receive.po_id = ap.id AND receive_det.id =".$id;
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		if($row1['special'] == 2)
		{
			return 'ap_special';
		}else{
			return 'ap_det';
		}
		
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	��X���w�O�� bom �D�Ƭ������ RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_rcvd($ord_num) {
		$row=array();
		$sql = $this->sql;
		$det_row = array();
		$special_row = array();
		
		$q_str="SELECT ap_det.rcv_qty, ap_det.id, ap.id as ap_id
						FROM  ap_det, bom_acc, wi, ap
						WHERE ap.ap_num = ap_det.ap_num AND ap_det.bom_id = bom_acc.id AND ap.status >= 0 AND
									bom_acc.wi_id = wi.id AND ap_det.mat_cat = 'a' AND wi.wi_num = '".$ord_num."'";
									

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			$det_row[] = $row;
			if($row['rcv_qty'] == 0)			
			{
				return 0;
			}			
		}

		$q_str="SELECT ap_det.rcv_qty, ap_det.id, ap.id as ap_id
						FROM  ap_det, bom_lots, wi, ap
						WHERE ap.ap_num = ap_det.ap_num AND ap_det.bom_id = bom_lots.id AND ap.status >= 0 AND
									bom_lots.wi_id = wi.id AND ap_det.mat_cat = 'l' AND wi.wi_num = '".$ord_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			$det_row[] = $row;
			if($row['rcv_qty'] == 0)			
			{
				return 0;
			}			
		}		
	
		$q_str="SELECT ap_special.rcv_qty, ap_special.id, ap.id as ap_id
						FROM   ap_special, ap
						WHERE	 ap.ap_num = ap_special.ap_num AND ap.status >= 0 AND ord_num = '".$ord_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			$det_row[] = $row;
			if($row['rcv_qty'] == 0)			
			{
				return 0;
			}			
		}	

	for($i=0; $i<sizeof($det_row); $i++)
	{
		$q_str="SELECT receive.status
						FROM  receive, receive_det, rcv_po_link
						WHERE receive_det.rcv_num = receive.rcv_num AND receive.po_id =".$det_row[$i]['ap_id']." AND
									receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id =".$det_row[$i]['id'];			
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while($row = $sql->fetch($q_result))
		{
			if($row['status'] < 2)			
			{
				return 0;
			}			
		}

	}

		return 1;
		
	} // end func	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_rec($fd,$mat_cat,$ap_table)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_rec($link_row,$mat_cat,$ap_table) {

		$sql = $this->sql;
		$ord_fd = array();
		for($i=0; $i<sizeof($link_row); $i++)
		{
			if($mat_cat == 'l')
			{
				$q_str = "SELECT wi_num as ord_num
						  FROM ap_det, bom_lots, wi
						  WHERE ap_det.bom_id = bom_lots.id AND bom_lots.wi_id = wi.id AND
								ap_det.id =".$link_row[$i]['po_id'];

					if (!$q_result = $sql->query($q_str)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;    
					}		
					while ($row1 = $sql->fetch($q_result)) {	
						$mk = 0;
						for($j=0; $j<sizeof($ord_fd); $j++)
						{
							if($ord_fd[$j]['ord_num'] == $row1['ord_num'])
							{
								$mk = 1;
								break;
							}
						}
						
						if($mk == 0)$ord_fd[]=$row1;			
					}
			}else{
				$q_str = "SELECT smpl_code as ord_num, acc_cat
						  FROM ap_det, bom_acc, acc_use
						  WHERE ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id AND
								ap_det.id =".$link_row[$i]['po_id'];
					
					if (!$q_result = $sql->query($q_str)) {
						$this->msg->add("Error ! Database can't access!");
						$this->msg->merge($sql->msg);
						return false;    
					}	
					while ($row1 = $sql->fetch($q_result)) {	
						$mk = 0;
						for($j=0; $j<sizeof($ord_fd); $j++)
						{
							if($ord_fd[$j]['ord_num'] == $row1['ord_num'] && $ord_fd[$j]['acc_cat'] == $row1['acc_cat'])
							{
								$mk = 1;
								break;
							}
						}
						
						if($mk == 0)$ord_fd[]=$row1;			
					}
			}
		}

		return $ord_fd;
	} // end func
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->back_ord_rcvd($mat_cat,$ord_fd)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function back_ord_rcvd($mat_cat,$ord_fd) {

		$sql = $this->sql;
		$mk = 0;

		for ($i=0; $i<sizeof($ord_fd); $i++)
		{
			if( $mat_cat == 'l' )
			{
				$q_str = "UPDATE pdtion SET mat_shp = NULL WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
				$q_result = $sql->query($q_str);		
			}else{
				//�D�n�Ʈ�
				if ( $ord_fd[$i]['acc_cat'] == 1)
				{							
					$q_str = "UPDATE pdtion SET m_acc_shp = NULL WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
					$q_result = $sql->query($q_str);		
				}else{
				//��L�Ʈ�
					$q_str = "UPDATE pdtion SET acc_shp = NULL WHERE order_num = '".$ord_fd[$i]['ord_num']."'";
					$q_result = $sql->query($q_str);
				}
			}
		}

		return 1;
	} // end func	




/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_po_cat() {

		$sql = $this->sql;
		$mk = 0;

				$q_str = "SELECT receive_det.id, ap.special
									FROM ap, receive, receive_det 
									WHERE ap.id = receive.po_id AND receive.rcv_num = receive_det.rcv_num";

				$q_result = $sql->query($q_str);		
				$rcv = array();
				while ($row = $sql->fetch($q_result)) {	
					$rcv[] = $row;
				}
				for($i=0; $i<sizeof($rcv); $i++)
				{
					if($rcv[$i]['special'] < 2)
					{
						$q_str = "UPDATE receive_det SET receive_det.po_cat = 0 WHERE id=".$rcv[$i]['id'] ;
					}else{
						$q_str = "UPDATE receive_det SET receive_det.po_cat = 1 WHERE id=".$rcv[$i]['id'] ;
					}
					$q_result = $sql->query($q_str);
				}
				
				
		return 1;
	} // end func	
*/


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_order()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_order() {

		$sql = $this->sql;
		$mk = 0;

		$q_str = "ALTER TABLE `s_order` ADD `quota_unit` VARCHAR( 20 ) NOT NULL ,
ADD `content` VARCHAR( 255 ) NOT NULL ,
ADD `des` VARCHAR( 255 ) NOT NULL ,
ADD `ship_quota` FLOAT( 6, 2 ) NOT NULL ; ";
									
				$q_result = $sql->query($q_str);		
				
				
		return 1;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_cost($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_cost($ord_num,$mat_cat) {

		$sql = $this->sql;
		$mk = 0;
		
			if($mat_cat == 'l')
			{
				$q_str = "SELECT sum(rcv_po_link.amount) as amount, receive_det.special, 
												 rcv_po_link.po_id
									FROM rcv_po_link, receive_det, receive
									WHERE receive_det.id = rcv_po_link.rcv_id AND receive_det.rcv_num = receive.rcv_num AND
												receive_det.mat_code like 'F%' AND 
												rcv_po_link.ord_num ='".$ord_num."'
									GROUP BY po_id,special";
			
				$q_result = $sql->query($q_str);	
				$cost_ttl = 0;	
				while ($row = $sql->fetch($q_result)) {	
					$row['special'] == 1 ? $table = 'ap_special' : $table = 'ap_det';
					$ap_str = "select dm_way from ap,".$table." 
								where ".$table.".id=".$row['po_id']." and ap.ap_num=".$table.".ap_num";
					$ap_result = $sql->query($ap_str);
					$ap_row = $sql->fetch($ap_result);
					if(strstr($ap_row['dm_way'],'before shipment') )
					{
						$where = ' id ='.$row['po_id'];
						$po_tmp = $GLOBALS['po']->get_det_field('amount,(po_qty*prics) as amt',$table,$where);
						if($po_tmp[0] == 0) $po_tmp[0] = $po_tmp[1];
						$row['amount'] = $po_tmp[0];
					}
					if(!isset($row['amount']))$row['amount'] = 0;

					$cost_ttl += $row['amount'];
				}
				$q_str = "UPDATE s_order SET rel_mat_cost = '".$cost_ttl."' WHERE order_num ='".$ord_num."'";
				$q_result = $sql->query($q_str);	
			}
			else{
				$q_str = "SELECT sum(rcv_po_link.amount) as amount, receive_det.special, 
												 rcv_po_link.po_id
									FROM rcv_po_link, receive_det, receive
									WHERE receive_det.id = rcv_po_link.rcv_id AND receive_det.rcv_num = receive.rcv_num AND
												receive_det.mat_code like 'A%' AND 
												rcv_po_link.ord_num ='".$ord_num."'
									GROUP BY po_id,special";
			
				$q_result = $sql->query($q_str);	
				$cost_ttl = 0;	
				while ($row = $sql->fetch($q_result)) {	
					$row['special'] == 1 ? $table = 'ap_special' : $table = 'ap_det';
					$ap_str = "select dm_way from ap,".$table." 
								where ".$table.".id=".$row['po_id']." and ap.ap_num=".$table.".ap_num";
					$ap_result = $sql->query($ap_str);
					$ap_row = $sql->fetch($ap_result);
					if(strstr($ap_row['dm_way'],'before shipment') )
					{
						$where = ' id ='.$row['po_id'];
						$po_tmp = $GLOBALS['po']->get_det_field('amount,(po_qty*prics) as amt',$table,$where);
						if($po_tmp[0] == 0) $po_tmp[0] = $po_tmp[1];
						$row['amount'] = $po_tmp[0];
					}
					
					if(!isset($row['amount']))$row['amount'] = 0;
					// $row['amount'] = $row['amount'] * $cury_d[$row['currency']]['USD'];
					$cost_ttl += $row['amount'];

				}
				$q_str = "UPDATE s_order SET rel_acc_cost = '".$cost_ttl."' WHERE order_num ='".$ord_num."'";
				$q_result = $sql->query($q_str);			
			}
				
				
		return $cost_ttl;
	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_link_qty_by_id($po_id)	��X���wreceive�O����� RETURN qty	2012/01/12
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_link_qty_by_id($id) {

		$sql = $this->sql;
		
		$q_str = "SELECT sum(qty) as rcv_qty
							FROM rcv_po_link
							WHERE po_id = '$id'";

//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);

		return $row['rcv_qty'];
	}

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ap_field($field,$po_id,$special)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ap_field($field,$ap_num) {

		$sql = $this->sql;
		
		$q_str = "SELECT ap.$field
							FROM ap
							WHERE ap.ap_num = '$ap_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);

		return $row[$field];
	}

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cfm_search($mode=0, $dept='',$limit_entries=0)	2012/2/4
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cfm_search($mode=0, $dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT distinct receive.*
								 FROM receive ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("receive.id DESC");
		$srh->row_per_page = 15;

		if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			$pagesize=10;
			if (isset($argv['PHP_sr_startno']) && $argv['PHP_sr_startno']) {
				$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
			}else{
				$pages = $srh->get_page(1,$pagesize);
			} 
		}

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

		//���� : �~�ȳ���
		if ($user_dept=='LY' || $user_dept =='HJ'|| $user_dept =='CF')	
			$srh->add_where_condition("receive.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");

		$srh->add_where_condition("receive.status = 2");
		$srh->add_where_condition("receive.tw_rcv = 1");
  
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['rcvd'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
		if(!$limit_entries){ 
			$op['maxpage'] =$srh->get_max_page();
			$op['pages'] = $pages;
			$op['now_pp'] = $srh->now_pp;
			$op['lastpage']=$pages[$pagesize-1];		
		}
		
		return $op;
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pdate_field($field1, $value1, $where_str, $table='receive') ��s field����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($field1, $value1, $where_str, $table='receive') {

		$sql = $this->sql;

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_receive_check_list($id=0, $order_num=0)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_receive_check_list() {
	$sql = $this->sql;

	# �u�t
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$rcvd = ($user_dept=='LY' || $user_dept =='HJ' || $user_dept =='CF')? " and `dept` = '".$user_dept."' " : '' ;
	
	$q_str = "
	SELECT *
	FROM `receive`
	WHERE `status` = '0' and `tw_rcv` = '1' $rcvd ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($rows = $sql->fetch($q_result)) {
		$op[]=$rows;
	}

	return $op;

} // end func




function get_rcv_det($bom_id,$ord_num) {

	$sql = $this->sql;
	
	$rcvd = array();
	
	$q_str = "
	SELECT 
	ap_det.po_spare , ap_det.po_qty , ap_det.po_eta , 
	ap.special , 
	receive.rcv_num , receive.rcv_sub_date , receive.rcv_user ,
	receive_det.ap_num , REPLACE(receive_det.ap_num,'A','O') as po_num , receive_det.mat_code , 
	
	rcv_po_link.qty
	
	FROM receive , receive_det , rcv_po_link , ap , ap_det
	WHERE 

	ap_det.bom_id = '".$bom_id."' and 
	ap_det.id = rcv_po_link.po_id and 
	rcv_po_link.ord_num = '".$ord_num."' and 
	ap_det.ap_num = ap.ap_num and 
	receive_det.id = rcv_po_link.rcv_id and 
	receive.rcv_num = receive_det.rcv_num and 
	receive.tw_rcv = '0' ";

	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while ($row = $sql->fetch($q_result)) {
		$rcvd[] = $row;
	}
	
	return $rcvd;
}


function get_shipping($num=''){

$sql = $this->sql;

$query  = " SELECT * 
            FROM `po_supl_ship` 
            WHERE `po_supl_ship`.`id` = '".$num."' ";

if( $result = $sql->query($query) ){
    $po_supl_ship_rows = array();
    $po_supl_ship_rows = $sql->fetch($result);
}

$query  = " SELECT * , REPLACE(`po_num`,'O','A') as `ap_num`
            FROM `po_supl_ship_det` 
            WHERE `po_supl_ship_id` = '".$num."' AND 
            `del_mk` = 'n' ";

if( $result = $sql->query($query) ){
    $po_supl_ship_det_rows = array();
    while( $row = $sql->fetch($result) ) {
        
		$query_link  = "select *
					   from po_supl_ship_link
					   where po_supl_ship_det_id = ".$row['id']." and status = 1 and del_mk = 'n'";
		$result_link = $sql->query($query_link);
        $po_supl_ship_det_link_rows = array();
		while( $row_det = $sql->fetch($result_link) ) {
			# po_qty �����ƶq��
			$q_str = "select sum(po_qty) as ttl_po_qty
					  from ap_det
					  where ap_num = '".$row['ap_num']."' and color = '".$row_det['color']."' and mat_id = '".$row_det['mat_id']."' and size = '".$row_det['size']."'
					  group by ap_num, color, mat_id, size";
			
			$po_qty_rsl = $sql->query($q_str);
			$po_qty_row = $sql->fetch($po_qty_rsl);
			$row_det['ttl_po_qty'] = $po_qty_row['ttl_po_qty'];
			
			# �q�渹�X
			$row_det['orders'] = $this->get_ap_det_ord($row_det['ttl_po_qty'], $row_det['qty'], $row['ap_num'], $row_det['color'], $row_det['mat_id'], $row_det['size']);
			
			
			
			# �w�禬�ƶq
			$row_det['rcvd_qty'] = $this->get_ship_rcvd_qty($row_det['id'], $row['ap_num'], $row_det['mat_id'], $row_det['color'], $row_det['size']);
			
			$row_det['un_qty'] = $row_det['qty'] - $row_det['rcvd_qty'];
			$po_supl_ship_det_link_rows[] = $row_det;
		}
		$row['po_det_link'] = $po_supl_ship_det_link_rows;
		$po_supl_ship_det_rows[] = $row;
	}
}

$rtn = array();
$rtn['po_supl_ship'] = $po_supl_ship_rows;
$rtn['po_supl_ship_det'] = $po_supl_ship_det_rows;

return $rtn;

} # END


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ship_rcvd_qty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ship_rcvd_qty($id,$ap_num,$mat_id,$color,$size) {
	$sql = $this->sql;
	
	$q_str = "select sum(qty) as qty
			  from receive_det
			  where receive_det.ship_link_id = '".$id."'
							and receive_det.ap_num = '".$ap_num."' and receive_det.mat_id = '".$mat_id."' 
							and receive_det.color = '".$color."' and receive_det.size = '".$size."'";
	
	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	if(!$row['qty'])
		$row['qty'] = 0;
	
	return $row['qty'];
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ap_det_ord($ap_num, $color, $mat_id, $size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ap_det_ord($ttl_po_qty, $rcv_qty, $ap_num, $color, $mat_id, $size) {
		$sql = $this->sql;
		$rtn = array();
		
		
		
		$q_str="select id, order_num, po_qty
				from ap_det
				where ap_num = '".$ap_num."' and color = '".$color."' and mat_id = '".$mat_id."' and size = '".$size."'
				order by po_qty asc";
		
		
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)){
			$rtn[] = $row;
		}
		
		$rtn_size = sizeof($rtn);
		$tmp = 0;
		
		for($i=0; $i<$rtn_size-1; $i++){
			$rtn[$i]['qty'] = number_format($rcv_qty * ($rtn[$i]['po_qty'] / $ttl_po_qty),2,'','');
			$tmp += $rtn[$i]['qty'];
		}
		$rtn[$i]['qty'] = $rcv_qty - $tmp;
		
		return $rtn;
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ap_det_ord($ap_num, $color, $mat_id, $size)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ship_qty($ship_link_id) {
		$sql = $this->sql;
		$row = array();
		
		$q_str="select qty
				from po_supl_ship_link
				where id = ".$ship_link_id;
		
		
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		
		return $row['qty'];
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_tw_rcvd_qty($ap_nums,$mat_id,$color,$size) {
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_tw_rcvd_qty($po_nums,$mat_id,$color,$size) {

		$sql = $this->sql;
		
		$rtn = array();
		$po_num_ary = array();
		$ap_num_str = '';
		
		$po_num_ary = explode(",", $po_nums);
		
		foreach($po_num_ary as $key => $val){
			$ap_num_str .= "'".str_replace("PO", "PA", $val)."',";
		}
		$ap_num_str = substr($ap_num_str, 0, -1);
		
		$q_str = "SELECT receive_det.rcv_num, receive.rcv_user, receive.rcv_date, receive_det.ap_num, receive_det.color, receive_det.size, receive_det.qty,
						 po_supl_ship_link.po_supl_ship_id, po_supl_ship_link.r_no, po_supl_ship_link.l_no
				  FROM receive, receive_det, po_supl_ship_link
				  WHERE receive_det.ap_num in (".$ap_num_str.") and receive_det.mat_id = ".$mat_id." and receive_det.color = '".$color."' and 
						receive_det.size = '".$size."' and receive.rcv_num = receive_det.rcv_num and receive.status = 4 and 
						po_supl_ship_link.id = receive_det.ship_link_id";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result)){
			$row['po_num'] = str_replace("PA", "PO", $row['ap_num']);
			$rtn[] = $row;
		}

		return $rtn;
	} // end func
	

} // end class
?>