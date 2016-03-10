<?php 

#++++++++++++++++++++++ SHIPPING  class ##### �}�o����  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#	->order_ship_out($order)				�j�M  ���w�q�� �����X���
#	->order_ship_delt($order)	�R���C��o�f�q	 
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SHIPPING {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! cannot connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		�ˬd �[�J�s�O�� �O�_���T
#								# mode =0:�@��add��check,  mode=1: edit�ɪ�check
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����

		$T = $parm['qty_shp'];
		if (!(is_numeric($T)&&(intval($T)==floatval($T)))){  // ���ݬ����

			$this->msg->add("Error ! please input the correct qty [numeric only] �C");

			return false;
		}
		if ($parm['qty_shp'] > $parm['remain']) {  // ���ˬd��J �X�f�Ʀr�O�_�X�z (�P�Ѿl�ƶq�ۤ��) 
			$this->msg->add("sorry! your shipping Q'TY is overdue the Q'ty remained !");
			return false;
		}  // end if  ��J��� �����T 
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO shipping (k_date,
									factory,
									ord_num,
									p_id,
									acm,
									cm,
									qty,
									su) VALUES('".
									$parm['shp_date']."','".
									$parm['factory']."','".
									$parm['ord_num']."','".
									$parm['p_id']."','".
									$parm['acm']."','".
									$parm['cm']."','".
									$parm['qty_shp']."','".
									$parm['su_shp']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("adding shipping record for : [".$parm['ord_num']."]�C") ;

		return $pdt_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	�j�M ��� �X�f�q�� [�I�s pdtion table] ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_mon_shp($fty,$str,$limit_entries,$where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.uprice, shipping.ord_num, shipping.qty AS s_qty,s_order.qty, 
												shipping.su, shipping.k_date, s_order.etd, s_order.ship_fob,
												cust_init_name as cust_iname 
								 FROM shipping,s_order, cust 
								 WHERE s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver AND
								 			 shipping.factory='".$fty."' AND shipping.k_date LIKE '%".$str."%' AND
								 				s_order.order_num = shipping.ord_num ".$where_str.
								 "ORDER by s_order.cust, shipping.k_date,shipping.ord_num ";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$result = "none";
			}

//		$result;  // ��ƿ� �ߤJ $op

		return $result;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	�j�M ��� �X�f�q�� [�I�s pdtion table] ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_mon_cm($fty,$str,$limit_entries=500) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT shipping.*, s_order.cm as ord_cm, s_order.qty as ord_qty, s_order.etd,	s_order.ie1 
								 FROM   shipping,s_order 
								 WHERE  shipping.factory='".$fty."' AND shipping.k_date LIKE '%".$str."%' 
								 AND s_order.order_num = shipping.ord_num ORDER by shipping.k_date,shipping.ord_num ";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$result = "none";
			}

//		$result;  // ��ƿ� �ߤJ $op

		return $result;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	�j�M  ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($parm,$mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT shipping.id, shipping.ord_num, sum(shipping.qty) as qty, shipping.su, shipping.factory, 
        s_order.unit, s_order.dept, shipping.p_id
        FROM shipping,s_order ";
        
		$q_header = "SELECT shipping.id, shipping.ord_num, shipping.qty, shipping.su, shipping.factory, s_order.unit, s_order.dept, shipping.p_id  , order_partial.mks 
        FROM shipping,s_order , order_partial ";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("shipping.id DESC ");
        // $srh->add_group_condition("shipping.p_id");
		$srh->row_per_page = 100;

        if ($mode==1){
            if ($str = $parm['factory'] )  { 
                $srh->add_where_condition("shipping.factory = '$str'", "PHP_factory",$str," factory = [ $str ]. "); }
            if ($str = $parm['ord_num'] )  { 
                $srh->add_where_condition("shipping.ord_num LIKE '%$str%'", "PHP_order_num",$str," order number as [ $str ]. "); }
            if ($str = $parm['k_date'] )  { 
                $srh->add_where_condition("shipping.k_date = '%$str%'", "PHP_k_date",$str," shipping date=[ $str ]. "); }
        }
		$srh->add_where_condition("shipping.ord_num = s_order.order_num", '','','');
		$srh->add_where_condition("s_order.order_num = order_partial.ord_num", '','','');
		$srh->add_where_condition("order_partial.id = shipping.p_id", '','','');

		$result= $srh->send_query2();  
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
        
		$this->msg->merge($srh->msg);
        if (!$result){   // ��d�M�L��Ʈ�
            $op['record_NONE'] = 1;
        }

		$op['shipping'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['rows_this_page'] = $srh->rows_this_page;
        // echo $srh->q_str;
		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id) {

		if (!$id) {
			$this->msg->add("Error ! please idendify record ID for delete.");		    
			return false;
		}

		$sql = $this->sql;

		$q_str = "SELECT * FROM shipping WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! can't find these record!");
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
		$q_str = "UPDATE shipping SET k_date='"		.$parm['k_date'].
								"',	factory='"		.$parm['factory'].
								"', ord_num='"		.$parm['ord_num'].
								"', qty='"			.$parm['qty'].

								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �Ʈ� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE shipping SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� ���ưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! please idendify record ID for delete.");		    
			return false;
		}

		$q_str = "DELETE FROM shipping WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot delete data file !");
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
		$q_str = "SELECT ".$n_field." FROM shipping ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
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
#	->order_ship_delt($order)	�R���C��o�f�q	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_ship_del($order,$price=0) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM shipping WHERE ord_num ='".$order."' ORDER by k_date ";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			$mm = explode('-',$row['k_date']);
			$fob=$price*$row['qty'];
			$F = $GLOBALS['capaci']->delete_su($row['factory'], $mm[0], $mm[1], 'shipping', $row['qty']);
			$F = $GLOBALS['capaci']->delete_su($row['factory'], $mm[0], $mm[1], 'shp_fob', $fob);
		}  

		return true;

	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ship_del($order) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "DELETE FROM shipping WHERE ord_num ='".$order."'";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;

	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_ship_out($order)	�j�M  ���w�q�� �����X���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_ship_out($order) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM shipping WHERE ord_num ='".$order."' ORDER by k_date ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2();  

		return $result;
		// �Ǧ^ $result [ $result[$i][$k_date], $result[$i][$order_num].....]


	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_ship_out($sch_date,$fty,$cust) {
		$sql = $this->sql;

		$q_str = "SELECT shipping.*, s_order.style_num, s_order.ref, style_type.des, s_order.uprice, s_order.cm, cust_f_name as cust_name
							FROM shipping, s_order, style_type, cust 
							WHERE s_order.order_num = shipping.ord_num AND style_type.style_type = s_order.style
										AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver
										AND  shipping.k_date like'$sch_date%' AND  shipping.factory = '$fty' AND s_order.cust = '$cust' ORDER BY inv_num";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i = 0;
		$tmp_status=4;
		$tmp_sub=1;
		while ($row = $sql->fetch($q_result)) {
   		if ($tmp_status > $row['status']) $tmp_status = $row['status'];
   		if($row['inv_num'] == '')$tmp_sub=0;
	    $tmp =  explode(' ',$row['des']);
	    $row['fob_amt'] = $row['uprice'] * $row['qty'];
	    $row['cm_amt'] = $row['cm'] * $row['qty'];
	    $row['style'] = $tmp[0];
   		$op['ship'][$i] = $row;
   		$op['ship'][$i]['i'] =$i;
   		$i++;
		}
		$op['status'] = $tmp_status;
		$op['chk_submit'] = $tmp_sub;
		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �ƮưO��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_inv($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE shipping SET inv_date='"		.$parm['inv_date'].
								"',	inv_num='"		.$parm['inv_num'].
								"', t_qty='"		.$parm['t_qty'].
								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_ship_cfm($fty) {
		$sql = $this->sql;
		$op = array();
		$q_str = "SELECT shipping.*, s_order.style_num, s_order.ref, style_type.des, s_order.uprice, s_order.cm, s_order.ie1
							FROM shipping, s_order, style_type 
							WHERE s_order.order_num = shipping.ord_num AND style_type.style_type = s_order.style
										AND  shipping.status = '2' AND  shipping.factory = '$fty' ";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$i = 0;
		while ($row = $sql->fetch($q_result)) {
	    $tmp =  explode(' ',$row['des']);
	    $row['fob_amt'] = $row['uprice'] * $row['qty'];
	    $row['cm_amt'] = $row['cm'] * $row['qty'];
	    $row['style'] = $tmp[0];
   		$op['ship'][$i] = $row;
   		$op['ship'][$i]['i'] =$i;
   		$i++;
		}
		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� ���ưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_f_shipdoc($ord_num,$k_date) {

		$sql = $this->sql;


		$q_str = "DELETE FROM shipping WHERE ord_num='$ord_num' AND k_date='$k_date' ";
// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot delete data file !");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return true;
	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_ie($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_ie($ord_num,$ie) {

$sql = $this->sql;

$q_str = "SELECT `id`,`qty` FROM `shipping` WHERE `ord_num` = '".$ord_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! �L�k��s��Ʈw���e.");
    $this->msg->merge($sql->msg);
    return false;    
}

$qty = '';
while ($row = $sql->fetch($q_result)) {			
    $q_str = "UPDATE `shipping` SET `su` = '".set_su($ie,$row['qty'])."' WHERE `id` = '".$row['id']."' ;";
    // echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! �L�k��s��Ʈw���e.");
        $this->msg->merge($sql->msg);
        return false;    
    }
}

    return true;
} // end func

} // end class


?>