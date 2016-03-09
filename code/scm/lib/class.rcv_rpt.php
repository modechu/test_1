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

class RCV_RPT {
		
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
#	->search($mode=0)	Search  �Ʈ� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_search($mode=0) {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT DISTINCT ap.id, ap.ap_num, ap.po_num, ap.dept, supl.country, ap.cust,
												supl.supl_s_name as s_name, ap.finl_dist, ap.ship_name, ap.arv_area,
												sum(ap_det.po_qty) as det_po, sum(ap_det.rcv_qty) as det_rcv, ap_det.po_unit as det_unit,
												sum(ap_special.po_qty) as spc_po, sum(ap_special.rcv_qty) as spc_rcv, ap_special.po_unit as spc_unit
							   FROM ap , supl
								 LEFT JOIN ap_det ON ap.ap_num = ap_det.ap_num
								 LEFT JOIN ap_special ON ap.ap_num = ap_special.ap_num";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("ap.id desc");
		
//2006.11.14 �H�Ʀr������ܭ��X star		
		$srh->row_per_page = 20;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 �H�Ʀr������ܭ��X end  
   
//		$srh->row_per_page = 12;

		$srh->add_where_condition("(ap_det.mat_cat = 'l' AND ap_special.mat_cat IS NULL AND ap_det.rcv_qty > 0) OR (ap_det.mat_cat IS NULL AND ap_special.mat_cat = 'l' AND ap_special.rcv_qty > 0)"); 
		$srh->add_where_condition("ap.sup_code = supl.vndr_no");
		$srh->add_where_condition("ap.status = 12"); 

if ($mode==1){    //������Search��
		$mesg = '';
		if ($str = $argv['PHP_sup'] )  { 
			$srh->add_where_condition("ap.sup_code =  '$str'", "PHP_sup",$str); 
			$msg = $mesg." Supplier  :[$str].";
		}
		
		if ($str = $argv['PHP_ship'] )  { 
			$srh->add_where_condition("(finl_dist =  '$str') OR (arv_area = '$str' )", "PHP_ship",$str); 
			$msg = $mesg." receiver :[$str].";
		}

		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("(ap.cust like  '%$str%')", "PHP_cust",$str); 
			$msg = $mesg." receiver :[$str].";
		}
		
		if ($str = $argv['PHP_po'] )  { 
			$srh->add_where_condition("ap.po_num LIKE '%$str%'", "PHP_po",$str); 
			$msg = $mesg." PO# :[$str].";
		}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
}
		


		$srh->add_group_condition('ap.po_num');
		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['apply'] = $result;  // ��ƿ� �ߤJ $op
		$op['max_no'] = $srh->max_no;
		
#####2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14�s���X�ݭn��oup_put	end
		return $op;
	} // end func

					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �ƮưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
//�d�̫߳᪩�� 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	
			
					# �[�J��Ʈw
		$q_str = "INSERT INTO fab_rpt (fr_num,
									po_num,
									cust,
									cust_ver,
									lots_code,
									color,
									po_cat,
									po_spare,
									op_date,		
									dept,							
									qty,
									unit,
									supl_no
									) VALUES('".
									$parm['fr_num']."','".
									$parm['po_num']."','".
									$parm['cust']."','".
									$cust_row['ver']."','".
									$parm['lots_code']."','".
									$parm['color']."','".
									$parm['po_cat']."','".
									$parm['po_spare']."','".
									$parm['op_date']."','".	
									$parm['dept']."','".								
									$parm['qty']."','".		
									$parm['unit']."','".								
									$parm['supl_no']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id


		return $new_id;

	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �ƮưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
					
		$sql = $this->sql;
		
					# �[�J��Ʈw
		$q_str = "INSERT INTO fab_rpt_det (fr_id,
									roll_no,
									dyed_lot,
									fab_width,
									org_yd,
									fab_yd,
									rmk,
									defect_1,
									defect_2,									
									defect_3,
									defect_4,
									ttl_point_1,
									ttl_point_2,
									ttl_point_3,
									ttl_point_4,
									point_h_yd
									) VALUES('".
									$parm['fr_id']."','".
									$parm['roll_no']."','".
									$parm['dyed_lot']."','".
									$parm['fab_width']."','".
									$parm['org_yd']."','".
									$parm['fab_yd']."','".
									$parm['rmk']."','".
									$parm['defect_1']."','".
									$parm['defect_2']."','".									
									$parm['defect_3']."','".
									$parm['defect_4']."','".	
									$parm['ttl_point_1']."','".		
									$parm['ttl_point_2']."','".								
									$parm['ttl_point_3']."','".
									$parm['ttl_point_4']."','".									
									$parm['point_h_yd']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data on database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id


		return $new_id;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �Ʈ� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE fab_rpt SET ".$parm['field']." ='".$parm['value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $acc_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0) {

		$sql = $this->sql;

		$q_str = "SELECT fab_rpt.*, lots.lots_name as name, lots.width,  cust.cust_init_name as cust_name,
										 supl.supl_s_name as supl_name
						  FROM fab_rpt, lots, cust, supl
							WHERE lots.lots_code = fab_rpt.lots_code AND fab_rpt.cust = cust.cust_s_name AND 
										fab_rpt.cust_ver = cust.ver AND supl.vndr_no = fab_rpt.supl_no AND
										fab_rpt.id='$id' ";
//echo $q_str."<BR>";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		$op['lr'] = $row;
		

		$submit_user=$GLOBALS['user']->get(0,$op['lr']['sub_user']);
		$op['lr']['sub_user_name'] = $submit_user['name'];
		if (!$submit_user['name'])$op['lr']['sub_user_name'] = $op['lr']['sub_user'];
		
		if($row['po_cat'] == 1)
		{
			$q_str = "SELECT ord_num FROM ap_special  
								WHERE po_spare = '".$row['po_spare']."'";
//echo $q_str."<BR>";			
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access can't find this record!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$po_rec = $sql->fetch($q_result);		
			$op['lr']['ord_num'][] = $po_rec['ord_num'];
		}else{
			$q_str = "SELECT wi_num FROM ap_det, bom_lots, wi
								WHERE ap_det.bom_id = bom_lots.id AND wi.id = bom_lots.wi_id AND 
											ap_det.mat_cat = 'l' AND po_spare = '".$row['po_spare']."'";
//echo $q_str."<BR>";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access can't find this record!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while ($po_rec = $sql->fetch($q_result)) {
				$op['lr']['ord_num'][] = $po_rec['wi_num'];    
			}		
		
		}

		$q_str = "SELECT fab_rpt_det.* FROM fab_rpt_det  
							WHERE fr_id='$id' ";
//echo $q_str."<BR>";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['defect_1'] = explode(',',$row['defect_1']);
			$row['defect_2'] = explode(',',$row['defect_2']);
			$row['defect_3'] = explode(',',$row['defect_3']);
			$row['defect_4'] = explode(',',$row['defect_4']);
			$row['yd_diff'] = $row['fab_yd'] - $row['org_yd'];
			$row['rmk_view'] = str_replace( chr(13).chr(10), "<br>",$row['rmk'] );			
			$op['lr_det'][] = $row;			
		}		
		
				
		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �ƮưO��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_det($parm) {

		$sql = $this->sql;
				
		#####   ��s��Ʈw���e
		
		$q_str = "UPDATE fab_rpt_det SET roll_no='"	.$parm['roll_no'].
							"', dyed_lot='"			.$parm['dyed_lot'].
							"', fab_width='"		.$parm['fab_width'].
							"', org_yd='"				.$parm['org_yd'].
							"', fab_yd='"				.$parm['fab_yd'].		
							"', rmk='"					.$parm['rmk'].						
							"', defect_1='"			.$parm['defect_1'].							
							"', defect_2='"			.$parm['defect_2'].														
							"', defect_3='"			.$parm['defect_3'].
							"', defect_4='"			.$parm['defect_4'].
							"', ttl_point_1='"	.$parm['ttl_point_1'].
							"', ttl_point_2='"	.$parm['ttl_point_2'].							
							"', ttl_point_3='"	.$parm['ttl_point_3'].						
							"', ttl_point_4='"	.$parm['ttl_point_4'].
							"', point_h_yd='"		.$parm['point_h_yd'].							
							"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �ƮưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_det($det_id,$id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point Accessory ID.");		    
			return false;
		}
		$q_str = "DELETE FROM fab_rpt_det WHERE id='$det_id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$q_str = "SELECT id FROM fab_rpt_det  
							WHERE fr_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$q_str = "DELETE FROM fab_rpt WHERE id='$id' ";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access can't find this record !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			return 'FULL_DEL';
	
		}				
		return 'SUB_DEL';
	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	Search  �Ʈ� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0) {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT fab_rpt.*, supl.supl_s_name as supl_name, cust.cust_init_name as cust_name
								 FROM fab_rpt, supl, cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("fab_rpt.id desc");
		
//2006.11.14 �H�Ʀr������ܭ��X star		
		$srh->row_per_page = 12;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 �H�Ʀr������ܭ��X end  
   
//		$srh->row_per_page = 12;
	$srh->add_where_condition("cust.cust_s_name = fab_rpt.cust AND fab_rpt.cust_ver = cust.ver"); 
	$srh->add_where_condition("supl.vndr_no = fab_rpt.supl_no");
	
if ($mode==1){    //������Search��
		$mesg = '';
		if ($str = $argv['SCH_fab'] )  { 
			$srh->add_where_condition("fab_rpt.lots_code LIKE  '%$str%'", "SCH_fab",$str); 
			$mesg = $mesg." fab. # :[$str].";
		}
		
		if ($str = $argv['PHP_sup'] )  { 
			$srh->add_where_condition("fab_rpt.supl_no =  '$str'", "PHP_sup",$str); 
			$mesg = $mesg." supl # :[$str].";
		}

		if ($str = $argv['PHP_ship'] )  { 
			$srh->add_where_condition("fab_rpt.dept = '$str'", "PHP_des",$str); 
			$mesg = $mesg." FTY :[$str].";
		}
/*		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("fab_rpt.cust LIKE  '%$str%'", "SCH_fab",$str); 
			$msg = $mesg." cust. # :[$str].";
		}		
*/	
		if ($str = $argv['SCH_num'] )  { 
			$srh->add_where_condition("fab_rpt.fr_num LIKE  '%$str%'", "SCH_num",$str); 
			$mesg = $mesg." report. # :[$str].";
		}				
		if ($str = $argv['SCH_po'] )  { 
			$srh->add_where_condition("fab_rpt.po_num LIKE  '%$str%'", "SCH_po",$str); 
			$mesg = $mesg." P.O. # :[$str].";
		}			
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
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

		$op['lr'] = $result;  // ��ƿ� �ߤJ $op
		$op['max_no'] = $srh->max_no;
		
#####2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14�s���X�ݭn��oup_put	end
		return $op;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM fab_rpt ".$where_str;
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

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �ƮưO��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function avg_point($id,$det) {

		$point_h_yd = 0;
				
		for($i=0; $i<sizeof($det); $i++)
		{
			$point_h_yd += $det[$i]['point_h_yd'];
		}
		$point_h_yd = $point_h_yd/sizeof($det);

 		$parm = array('value'	=> $point_h_yd, 'field'	=>'avg_point'	, 'id'	=>	$id);
 		$f1 = $this->update_field($parm);	
	
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> search_po($sup_code,$ship) ��X���禬�����ʳ�
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_by_ord() {
		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //�N�Ҧ��� globals ����J$argv
		$where_str = '';
		$i = 0;
		$lr = array();
			
   
		$q_header = "SELECT ap.id, ap.ap_num, ap.po_num, supl.supl_s_name as supl_name, ap.arv_area, 
												ap.finl_dist, sum(ap_det.po_qty) as po_qty, ap_det.po_unit, bom_lots.color,
												lots_use.lots_code, lots_use.lots_name, ap_det.po_spare
								 FROM ap, supl, ap_det, bom_lots, lots_use 								 
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_det.ap_num 
								 	 AND ap_det.bom_id = bom_lots.id AND bom_lots.lots_used_id = lots_use.id
								 	 AND ap_det.mat_cat = 'l' AND ap.status = 12 AND lots_use.smpl_code = '".$argv['SCH_ord']."'
								 GROUP BY lots_use.smpl_code, lots_use.lots_code, bom_lots.color";
  
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
//echo $q_header."<br>";
		while ($row1 = $sql->fetch($q_result)) {
			$lr[]=$row1;
			
		}

		$q_header = "SELECT ap.id, ap.ap_num, ap.po_num, supl.supl_s_name as supl_name, 
												ap.arv_area, ap.finl_dist, ap_special.po_qty, ap_special.po_unit, 
												ap_special.color, ap_special.mat_code as lots_code, lots.lots_name,
												ap_special.po_spare
								 FROM ap, supl, ap_special , lots
								 WHERE ap.sup_code = supl.vndr_no AND ap.ap_num = ap_special.ap_num AND
								 			 ap_special.mat_code = lots.lots_code AND	ap.status = 12  AND 
								 			 ap_special.ord_num = '".$argv['SCH_ord']."'";


//echo $q_header."<br>";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$lr[]=$row1;
		}

		for($i=0; $i<sizeof($lr); $i++)
		{
			$q_header = "SELECT fab_rpt.fr_num, fab_rpt.id
									 FROM fab_rpt							 
								 	 WHERE fab_rpt.po_spare = '".$lr[$i]['po_spare']."'";
  
			$q_result = $sql->query($q_header);		
			$mk = 0;
			while($row1 = $sql->fetch($q_result))
			{
				$lr[$i]['fr_num'][] = $row1['fr_num'];
				$lr[$i]['fr_id'][] = $row1['id'];
				$mk = 1;
			}
					
			if($mk == 0)
			{
				$lr[$i]['fr_num'][0] = '';	
				$lr[$i]['fr_id'][0] = '';
			}
				
		}		
		return $lr;
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>