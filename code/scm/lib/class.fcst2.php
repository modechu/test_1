<?php 

#++++++++++++++++++++++ FORECAST  class ##### �w��  +++++++++++++++++++++++++++++++++++
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

class FCST2 {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Cannot connect database, please contact the Administrator.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s forecast
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;


//�d�̫߳᪩�� 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	

			############### �ˬd��J����	
			//  ��J�O�_���Ʀr��
					
					# �[�J��Ʈw
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];			
		$q_str = "INSERT INTO forecast2 (year,method,fty,cust,cust_ver,uprc,qty,cm,dept,fcst) 
								VALUES ('".$parm['year']."','".
													 $parm['method']."','".
													 $parm['fty']."','".
													 $parm['cust']."','".
													 $cust_row['ver']."','".
													 $parm['uprc']."','".
													 $parm['qty']."','".
													 $parm['cm']."','".
													 $user_dept."','".
													 $parm['fcst']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	�j�M  FORECAST ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$where_str=$where_str." and cust.cust_s_name=forecast2.cust AND cust.ver = forecast2.cust_ver";
		$q_header = "SELECT forecast2.*, cust_init_name as cust_iname FROM forecast2 ,cust ".$where_str;
//echo $q_header."<br>";	
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			} else {
				$op['record_NONE'] = "";
			}

		$op['fcst'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['max_no'] = $srh->max_no;

		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0,$parm='',$method='')	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0,$parm='',$method='') {

		$sql = $this->sql;
		$where_str = '';

		if ($id)	{
			$q_str = "SELECT * FROM forecast2 WHERE id='$id' ";
		} elseif($parm) {

			if(isset($parm['fty'])){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."fty ='".$parm['fty']."' ";
			}
			if(isset($parm['year'])){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."year ='".$parm['year']."' ";
			}
			if(isset($parm['cust'])){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."cust ='".$parm['cust']."' ";
			}
			if($method){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."method ='".$method."' ";
			}

			if($where_str) { $where_str = " WHERE ".$where_str; }

			$q_str = "SELECT * FROM forecast2 ".$where_str;
		} else {
			$this->msg->add("Error ! please specify searching data for forecast2 table.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! cannot find this record!");
			return false;    
		}
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s forecast ���
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		#####   ��s��Ʈw���e
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$q_str = "UPDATE forecast2 SET uprc='"	.$parm['uprc'].
								"', qty='"		.$parm['qty'].
								"', cm='"		  .$parm['cm'].
								"', dept='"		  .$user_dept.
								"', fcst='"		.$parm['fcst'].
							"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $parm['id'];
	} // end func







#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	��X���w�O�������֥i�q��SU��� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_fob_mm($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT sum(s_order.qty * s_order.uprice) as fob
						  FROM s_order 
							WHERE s_order.factory='".$fty."' AND etd like '".$yymm."%' AND 
										s_order.status >= 4 AND  s_order.status <> 5 ".$where_str;
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return false;
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $row['fob'];
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	��X���w�O�������֥i�q��SU��� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_shp_fob_mm($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$fob = 0;
		$q_str = "SELECT sum(shipping.qty) as qty, s_order.uprice, s_order.ship_fob
						  FROM s_order, shipping 
							WHERE s_order.order_num = shipping.ord_num AND s_order.factory='".$fty."' AND 
										k_date like '".$yymm."%' AND 
										s_order.status >= 4 AND  s_order.status <> 5 ".$where_str."
							GROUP BY s_order.order_num";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['ship_fob'] == 0)
			{
				$fob += $row['qty'] * $row['uprice'];
			}else{
				$fob += $row['qty'] * $row['ship_fob'];
			}
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $fob;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_etp($fty,$year_mon,$limit_entries)	
#			�j�M order ETP ������q�� [�I�s pdtion table] ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp_ord($fty,$year_mon,$limit_entries, $where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_str = "SELECT s_order.*
		          FROM s_order
		          WHERE s_order.factory='".$fty."' AND s_order.etd LIKE '".$year_mon."%' 		             			 
		            AND s_order.status >= 4 AND s_order.status <> 5 ".$where_str.
		        " ORDER BY etd";
		             
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
		$row['unit_cost'] = ($row['mat_u_cost']* $row['mat_useage'])+ $row['interline']+ $row['fusible']+ $row['acc_u_cost'] + $row['quota_fee'] + $row['comm_fee'] + $row['cm'] + $row['emb'] + $row['wash'] + $row['oth'];
		$row['grand_cost'] = $row['unit_cost']*$row['qty'] + $row['smpl_fee'];
		$row['sales'] = $row['uprice']*$row['qty'];
		$row['gm'] = $row['sales'] - $row['grand_cost'];
		$result[] = $row;
			
		}

		return $result;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	��X���w�O�������֥i�q��SU��� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_ship_mm($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT sum(shipping.qty) as ship_qty, s_order.*, pdtion.qty_shp as done_ship
						  FROM s_order, shipping, pdtion
							WHERE s_order.order_num = pdtion.order_num AND pdtion.order_num = shipping.ord_num AND
										s_order.order_num = shipping.ord_num AND s_order.factory='".$fty."' AND 
										k_date like '".$yymm."%' AND 
										s_order.status >= 4 AND  s_order.status <> 5 ".$where_str."
							GROUP BY s_order.order_num";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {

			if($row['ship_fob'] == 0)$row['ship_fob'] = $row['uprice'];
			
			$row['sales'] = $row['qty'] * $row['uprice'];
			$row['ship_sales'] = $row['ship_qty'] * $row['ship_fob'];
			$row['unit_cost'] = ($row['mat_u_cost']* $row['mat_useage'])+ $row['interline']+ $row['fusible']+ $row['acc_u_cost'] + $row['quota_fee'] + $row['comm_fee'] + $row['cm'] + $row['emb'] + $row['wash'] + $row['oth'];
			$row['grand_cost'] = $row['unit_cost']*$row['qty'] + $row['smpl_fee'];
			$row['gm'] = $row['sales'] - $row['grand_cost'];	
			$result[] = $row;	
			
		}
		return $result;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>