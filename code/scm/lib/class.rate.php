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

class RATE {
		
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
/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s�����O
#		�[�J�s�����O			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### �ˬd��J����					
		if (!$parm['date'] = trim($parm['date'])) {
			$this->msg->add("Error ! �п�J��� !");
		    return false;
		}
		if (!$parm['currency'] = trim($parm['currency'])) {
			$this->msg->add("Error ! �п�ܹ��O !");
		    return false;
		}
		if (!$parm['in'] = trim($parm['in'])) {
			$this->msg->add("Error ! �п�J�R�J !");
		    return false;
		}
		if (!$parm['out'] = trim($parm['out'])) {
			$this->msg->add("Error ! �п�J��X !");
		    return false;
		}
					# �����令�j�g
//		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# �ˬd�O�_������
		$q_str = "SELECT id FROM rate WHERE date='".$parm['date']."' and currency='".$parm['currency']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! �o�Ӥ�����O�v�g�s�b�A�д��O��������O!");
			return false;    	    
		}
					# �[�J��Ʈw
		$today=date('y-m-d');
		$q_str = "INSERT INTO `rate` ( `date` , `currency` , `in` , `out` ) VALUES ('".$parm['date']."','".$parm['currency']."','".$parm['in']."','".$parm['out']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("���\ �s�W�ײv : [".$parm['date']."]�C") ;
		return $dept_id;

	} // end func
	*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_in($parm
#		�[�J�ײv			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_in($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);


					# �[�J��Ʈw
		$today=date('y-m-d');
		$q_str = "INSERT INTO `rate_in` ( `rate_date` , `USD` , `HKD` , `GBP`, `JPY` , `EUR` , `RMB` ) 
							VALUES ('".$parm['rate_date']."','".
												 $parm['USD']."','".
												 $parm['HKD']."','".
												 $parm['GBP']."','".
												 $parm['JPY']."','".
												 $parm['EUR']."','".
												 $parm['RMB']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("Successfully Append Rate : [".$parm['rate_date']."]") ;
		return $dept_id;

	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_in($parm
#		�[�J�ײv			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_out($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);


					# �[�J��Ʈw
		$today=date('y-m-d');
		$q_str = "INSERT INTO `rate_out` ( `rate_date` , `USD` , `HKD` , `GBP`, `JPY` , `EUR` , `RMB` ) 
							VALUES ('".$parm['rate_date']."','".
												 $parm['USD']."','".
												 $parm['HKD']."','".
												 $parm['GBP']."','".
												 $parm['JPY']."','".
												 $parm['EUR']."','".
												 $parm['RMB']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("Successfully Append Rate : [".$parm['rate_date']."]") ;
		return $dept_id;

	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		��s �ײv�R�J
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_in($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE `rate_in` SET 
							`USD` = '".$parm['USD']."',
							`HKD` = '".$parm['HKD']."',
							`GBP` = '".$parm['GBP']."',
							`JPY` = '".$parm['JPY']."',
							`EUR` = '".$parm['EUR']."',
							`RMB` = '".$parm['RMB']."' 
							WHERE `rate_date` = '".$parm['rate_date']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_out($parm)		��s �ײv��X
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_out($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE `rate_out` SET 
							`USD` = '".$parm['USD']."',
							`HKD` = '".$parm['HKD']."',
							`GBP` = '".$parm['GBP']."',
							`JPY` = '".$parm['JPY']."',
							`EUR` = '".$parm['EUR']."',
							`RMB` = '".$parm['RMB']."' 
							WHERE `rate_date` = '".$parm['rate_date']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	
	
/*	
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
if ($mode == 0){		
		if ($str = $argv['PHP_year'] )  { 
			$search_date=$str; }
		
		if ($str = $argv['PHP_month'] )  { 
			if(!isset($search_date)){$search_date=date('Y')."-".$str;}else{$search_date=$search_date."-".$str;} }
		if (isset($search_date))
		{
			if ($argv['PHP_month'])
			{
				$s_date=$search_date."-01";
				$e_date=$search_date."-31";
			}else{
				$s_date=$search_date."-01-01";
				$e_date=$search_date."-12-31";
			}			
			$srh->add_where_condition("date >= '$s_date'", "PHP_part_num",$str,"�j�M ��� = [ $search_date ]. ");			
		}
		if ($str = $argv['PHP_currency'] )  { 
			$srh->add_where_condition("currency = '$str'", "PHP_currency",$str,"�j�M ���O = [ $str ]. "); }		
}
//2006.11.15�ק�s�W��e�{�覡	start	
	$q_header = "SELECT * FROM rate";
//2006.11.15�ק�s�W��e�{�覡    end
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("date ");
		$srh->row_per_page = 12;
//2006.11.14 �H�Ʀr������ܭ��X star		
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

		$op['rate'] = $result;  // ��ƿ� �ߤJ $op		
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

		if ($id)	{
			$q_str = "SELECT * FROM rate WHERE id='$id' ";
		} elseif ($dept_code) {
			$q_str = "SELECT * FROM rate ";
		} else {
			$this->msg->add("Error ! �Ы��� ������Ʀb��Ʈw���� ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! �L�k���o���O��!");
			return false;    
		}
		return $row;
	} // end func

*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, dept_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_rate($currency=0, $ratedate, $price) {

		$sql = $this->sql;

//			$q_str = "SELECT rate.in FROM rate WHERE  rate.currency = '$currency' AND rate.date ='$ratedate'  ";
			$q_str = "SELECT * FROM rate_in WHERE  rate_date ='$ratedate'  ";
			

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return 0;    
		}
//		echo $currency;
		if(!isset($row[$currency])) return 0;
		$ntd_price = $row[$currency] * $price;
		//echo $ntd_price;
		return $ntd_price;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_rate($currency=0, $ratedate)	��X���w�O�����ײv
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rate($currency=0, $ratedate) {

		$sql = $this->sql;

//			$q_str = "SELECT rate.in FROM rate WHERE  rate.currency = '$currency' AND rate.date ='$ratedate'  ";
			$q_str = "SELECT * FROM rate_out WHERE  rate_date ='$ratedate'  ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return 0;    
		}
//		echo $currency;
		if(!isset($row[$currency])) return 0;
		return $row[$currency];
	} // end func	
		

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_rate_date($ratedate)	��X���w�O�����ײv
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_rate_date($ratedate) {

$sql = $this->sql;

$q_str = "SELECT * FROM `rate_out` WHERE  `rate_date` = '$ratedate'  ";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k�s����Ʈw!");
    $this->msg->merge($sql->msg);
    return false;    
}

if (!$row = $sql->fetch($q_result)) {

    $q_str = "SELECT * FROM `rate_out` ORDER BY `rate_date` DESC LIMIT 1 ";
    $q_result = $sql->query($q_str);
    if (!$row = $sql->fetch($q_result)) {
        return 0;
    }
    
}

return $row;

} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mm_rate($mm)	��X���w������ײv
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_mm_rate($mm) {

		$sql = $this->sql;

//			$q_str = "SELECT rate.in FROM rate WHERE  rate.currency = '$currency' AND rate.date ='$ratedate'  ";
			$q_str = "SELECT rate_in.*, rate_out.id as out_id, rate_out.USD as out_USD, rate_out.HKD as out_HKD,
											 rate_out.GBP as out_GBP, rate_out.JPY as out_JPY, rate_out.EUR as out_EUR,
											 rate_out.RMB as out_RMB, rate_out.rate_date as date
								FROM rate_in, rate_out
								WHERE  rate_in.rate_date = rate_out.rate_date  AND rate_in.rate_date like '".$mm."%'";
			

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$mm_rate[$i] = $row;    
			$mm_rate[$i]['i'] = $i;
			$i++;
		}

		if(!isset($mm_rate)) return 0;
		return $mm_rate;
	} // end func		

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mm_rate($mm)	��X���w������ײv
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($mm) {

		$sql = $this->sql;
		$q_str = "SELECT rate_in.*, rate_out.id as out_id, rate_out.USD as out_USD, rate_out.HKD as out_HKD,
										 rate_out.GBP as out_GBP, rate_out.JPY as out_JPY, rate_out.EUR as out_EUR,
										 rate_out.RMB as out_RMB, rate_out.rate_date as date
							FROM rate_in, rate_out
							WHERE  rate_in.rate_date = rate_out.rate_date  AND rate_in.rate_date = '".$mm."'";
	//echo $q_str;		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		if (!$row = $sql->fetch($q_result)) {
			return 0;
		}		
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		��s �����O���
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
					# �����令�j�g
//		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# �ˬd�O�_���o���ɮצs�b
		$q_str = "SELECT id FROM rate WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! �L�k����Ʈw�O��!");
			return false;    	    
		}
		if (!$parm['in'] = trim($parm['in'])) {
			$this->msg->add("Error ! �п�J�R�J !");
		    return false;
		}
		if (!$parm['out'] = trim($parm['out'])) {
			$this->msg->add("Error ! �п�J��X !");
		    return false;
		}
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE `rate` SET `in` = '".$parm['in']."',`out` = '".$parm['out']."' WHERE `id` = '".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm,$table)		��s �ײv��Ƥ� �Y�ӳ�@���
#								$parm = [$date, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm,$table) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE $table SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE rate_date='".$parm['date']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �����O���  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� �ϥγ�� ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM section WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
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
		$q_str = "SELECT ".$n_field." FROM section".$where_str;		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
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