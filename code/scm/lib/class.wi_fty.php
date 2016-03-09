<?php 

#++++++++++++++++++++++ WI  class ##### �s�y�O  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->add_edit($parm)						��s �s�y�O �O�� [ �[�J�s �s�O��step2]
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class WI_FTY {
		
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

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	�j�M �s�y�O ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search() {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT wi.*, cust.cust_init_name as cust_iname, s_order.factory , s_order.etd FROM wi, cust, s_order ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 12;

##--*****--2006.11.16���X�s�W start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
 	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	
	// $sale_f_mang = $GLOBALS['SALES_F_MANG'];
	// $sale_mang = $GLOBALS['SALES_MANG'];
	// for ($i=0; $i< sizeof($sale_f_mang); $i++)
	// {			
			// if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("wi.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	// }

	
	// $sales_dept = $GLOBALS['SALES_DEPT'];
	// if ($team == 'MD')	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	// for ($i=0; $i< sizeof($sales_dept); $i++)
	// {			
			// if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	// }			


	$srh->add_where_condition("wi.status > 1");
	
		if ($str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("wi.dept = '$str'", "PHP_dept_code",$str,"Dept. = [ $str ]. "); }
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("wi.cust = '$str'", "PHP_cust",$str,"Search customer = [ $str ]. "); }
		if ($str = $argv['PHP_wi_num'] )  { 
			$srh->add_where_condition("wi.wi_num LIKE '%$str%'", "PHP_wi_num",$str,"Search wi # above: [ $str ] "); }
		if ($str = $argv['PHP_etdstr'] )  { 
			$srh->add_where_condition("wi.etd >= '$str'", "PHP_etdstr",$str,"search etd>=[ $str ]. "); 
			}
		if ($str = $argv['PHP_etdfsh'] )  { 
			$srh->add_where_condition("wi.etd <= '$str'", "PHP_etdfsh",$str,"search etd<=[ $str ]. "); 
			}	
		if ($str = $argv['PHP_fty_sch'] )  { 
			$srh->add_where_condition(" s_order.factory = '$str'", "PHP_fty_sch",$str,"search Factory=[ $str ]. "); 
			}	
				
$srh->add_where_condition("wi.cust = cust.cust_s_name AND wi.cust_ver = cust.ver");
$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");
//$srh->add_where_condition("wi.dept = cust.dept");   // ���p����M ���M�n�[
$srh->add_where_condition("wi.wi_num = s_order.order_num");
$srh->add_where_condition("s_order.status >=0");
		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['wi'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;


##--*****--2006.11.16���X�s�W start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16���X�s�W end

		return $op;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_wi($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function copy_wi($id)
{
	
		$sql = $this->sql;
		$j=0;
		$q_str = "SELECT * FROM wiqty WHERE wi_id='".$id."' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			
			while ($row_add = $sql->fetch($q_result)) {
				$sql_ary[$j]= "insert into `fty_wiqty` set ";
				foreach ($row_add as $key => $value)
				{					
					if (!is_int($key) && $key<>'id') 
						$sql_ary[$j].=$key."='".$value."', ";					
				}		
				$sql_ary[$j] = substr($sql_ary[$j],0,-2);
				$j++;	
			}

		$q_str = "SELECT * FROM bom_lots WHERE wi_id='".$id."' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			while ($row_add = $sql->fetch($q_result)) {
				$sql_ary[$j]= "insert into `fty_bom_lots` set ";
				foreach ($row_add as $key => $value)
				{					
					if (!is_int($key) && $key<>'id') 
						$sql_ary[$j].=$key."='".$value."', ";					
				}			
				$sql_ary[$j] = substr($sql_ary[$j],0,-2);	
				$j++;	
			}		
			
		$q_str = "SELECT * FROM bom_acc WHERE wi_id='".$id."' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}

			while ($row_add = $sql->fetch($q_result)) {
				$sql_ary[$j]= "insert into `fty_bom_acc` set ";
				foreach ($row_add as $key => $value)
				{					
					if (!is_int($key) && $key<>'id') 
						$sql_ary[$j].=$key."='".$value."', ";					
				}	
				$sql_ary[$j] = substr($sql_ary[$j],0,-2);			
				$j++;	
			}				
			$sql_ary[] = "UPDATE WI SET fty_chk = 1 WHERE id = '".$id."'";
			for($i=0; $i<sizeof($sql_ary); $i++) 
			{
				$q_result = $sql->query($sql_ary[$i]);
				//echo $sql_ary[$i]."<BR>";
			}
		
}	
	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_wi($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_lots($ord_num)
{
		$sql = $this->sql;
		$lots_ary=array();
		$q_str = "SELECT lots_use.id, lots.lots_code, lots.lots_name, lots_use.unit, est_1, use_for,lots.comp, 
										 lots.width, lots.weight, lots.cons, 
									 	 lots.des, lots.specify, lots.vendor1, lots.price1
							FROM lots_use, bom_lots, lots 
							WHERE lots_use.id = bom_lots.lots_used_id AND lots_use.lots_code = lots.lots_code AND
									 lots_use.smpl_code='".$ord_num."' 
							GROUP BY lots_use.id
							ORDER BY lots_use.id";	

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return true;    
		}
			
		while ($row = $sql->fetch($q_result)) $lots_ary[] = $row;

		for($i=0; $i<sizeof($lots_ary); $i++)
		{
			$q_str = "SELECT color, qty 
							FROM bom_lots WHERE lots_used_id='".$lots_ary[$i]['id']."' ORDER BY id";	

			$q_result = $sql->query($q_str);	
			while ($row = $sql->fetch($q_result))
			{
				$tmp_qty = explode(',',$row['qty']);				
				for($j=0; $j<sizeof($tmp_qty); $j++)
				{
					if(!isset($lots_ary[$i]['color_use'][$j]))$lots_ary[$i]['color_use'][$j]='';
					if($tmp_qty[$j] > 0)$lots_ary[$i]['color_use'][$j].=$row['color'];
				}
			}
		}

		return $lots_ary;

		
}		
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_wi($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_acc($ord_num)
{
	$sql = $this->sql;
	$acc_ary=array();
	$q_str = "
	SELECT acc_use.id, acc.acc_code, acc.acc_name, acc_use.unit, est_1, specify,
	use_for, size, acc.arrange
	FROM acc_use, bom_acc, acc 
	WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id AND 
				acc_use.smpl_code='".$ord_num."' 
	GROUP BY acc_use.id, size
	ORDER BY acc_name DESC";	

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! �L�k�s����Ʈw!");
		$this->msg->merge($sql->msg);
		return true;    
	}
		
	while ($row = $sql->fetch($q_result)) $acc_ary[] = $row;

	for($i=0; $i<sizeof($acc_ary); $i++)
	{
		$q_str = "
		SELECT color, qty 
		FROM bom_acc 
		WHERE acc_used_id='".$acc_ary[$i]['id']."' AND size = '".$acc_ary[$i]['size']."'
		ORDER BY id";	

		$q_result = $sql->query($q_str);	
		while ($row = $sql->fetch($q_result))
		{
			$tmp_qty = explode(',',$row['qty']);				
			for($j=0; $j<sizeof($tmp_qty); $j++)
			{
				if(!isset($lots_ary[$i]['color_use'][$j]))$acc_ary[$i]['color_use'][$j]='';
				if($tmp_qty[$j] > 0)$acc_ary[$i]['color_use'][$j].=$row['color'];
			}
		}
	}
	return $acc_ary;
}		



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	�j�M �s�y�O�ƶq�� ���O��	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function wiqty_search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if($mode=1){
			$q_header = "SELECT * FROM fty_wiqty ".$where_str;
			if (!$srh->add_q_header($q_header)) {
				$this->msg->merge($srh->msg);
				return false;
			}
		} else {
			$q_header = "SELECT * FROM fty_wiqty";
			if (!$srh->add_q_header($q_header)) {
				$this->msg->merge($srh->msg);
				return false;
			}
		}
		
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id");
		$srh->row_per_page = 500;

		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['wiqty'] = $result;  // ��ƿ� �ߤJ $op

		return $result;
//		return $op['wi_qty'];
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �s�y�O�ƶq�O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_wiqty($parm) {

		$sql = $this->sql;
		$admin =  new AUTH();   // �N�|�I�s�쪺 class    ?????????????????????????????
		$perm_array = array();
		
			
					# �[�J��Ʈw
		$q_str = "INSERT INTO fty_wiqty (	wi_id, 
										p_id,
										colorway, 
										qty) VALUES('".
										$parm['wi_id']."','".
										$parm['p_id']."','".
										$parm['colorway']."','".
										$parm['qty']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W�O��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$team_id = $sql->insert_id();  //���X �s�� id
		return $parm;

	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R��  �s�y�O�ƶq�O�� [mode=0: $id��qty��id ; $mode=1: $id��wi_id]
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_wiqty($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� WI QTY ��ƪ� ID.");		    
			return false;
		}
		if ($mode){
			$q_str = "DELETE FROM fty_wiqty WHERE wi_id='$id' ";
		}else{
			$q_str = "DELETE FROM fty_wiqty WHERE id='$id' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_lots($id,$mode=0)		�R�� BOM �D�ưO�� 
#					[mode=0: $id��bom_lots��id [�浧�R��] ; 
#					[mode=1: $id��wi_id ����� �N ���w���s�O������BOM�D�ưO�����R�� ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_lots($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� BOM���D�Ƹ�ƪ� ID.");		    
			return false;
		}
		if($mode == 1){
			$q_str = "DELETE FROM fty_bom_lots WHERE wi_id='$id' ";
		}else if($mode == 2){
			$q_str = "DELETE FROM fty_bom_lots WHERE wi_id='$id' AND ap_mark=''";
		}else{
			$q_str = "DELETE FROM fty_bom_lots WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_acc($id,$mode=0)		�R�� BOM �ƮưO�� 
#					[mode=0: $id��bom_lots��id [�浧�R��] ; 
#					[mode=1: $id��wi_id ����� �N ���w���s�O������BOM�ƮưO�����R�� ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_acc($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� BOM���ƮƸ�ƪ� ID.");		    
			return false;
		}
		if($mode == 1){
			$q_str = "DELETE FROM fty_bom_acc WHERE wi_id='$id' ";
		}else if($mode == 2){
			$q_str = "DELETE FROM fty_bom_acc WHERE wi_id='$id' AND ap_mark='' ";
		}else{
			$q_str = "DELETE FROM fty_bom_acc WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lots ($mode=0, $where_str="")	�j�M  BOM ���D�� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_lots($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		
		if($where_str) $where_str = $where_str. "AND dis_ver = 0";
		if(!$where_str) $where_str = "WHERE dis_ver = 0";
		$q_header = "SELECT * FROM fty_bom_lots ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 400;


		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['lots'] = $result;  // ��ƿ� �ߤJ $op

		return $op;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_acc ($mode=0, $where_str="")	�j�M  BOM ���Ʈ� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_acc($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if($where_str) $where_str = $where_str. "AND dis_ver = 0";
		if(!$where_str) $where_str = "WHERE dis_ver = 0";


		$q_header = "SELECT * FROM fty_bom_acc ".$where_str;
//echo $q_header;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 400;



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

		return $op;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_lots($parm)		�[�J�s BOM �D��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_lots($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	

		$parm['color'] = trim($parm['color']);
					# �[�J��Ʈw
		$q_str = "INSERT INTO fty_bom_lots (wi_id,
									lots_used_id,
									color,
									qty,
									k_date) VALUES('".
									$parm['wi_id']."','".
									$parm['lots_used_id']."','".
									$parm['color']."','".
									$parm['qty']."','".
									$parm['this_day']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("���\ �s�W BOM �D�ưO���C") ;

		return $new_id;

	} // end func
	

					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_acc($parm)		�[�J�s BOM �Ʈ�
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_acc($parm) {
					
		$sql = $this->sql;

			############### �ˬd��J����	

		$parm['color'] = trim($parm['color']);
					# �[�J��Ʈw
		$q_str = "INSERT INTO fty_bom_acc (wi_id,
									acc_used_id,
									color,
									qty,
									size,
									k_date) VALUES('".
									$parm['wi_id']."','".
									$parm['acc_used_id']."','".
									$parm['color']."','".
									$parm['qty']."','".
									$parm['size']."','".
									$parm['this_day']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("���\ �s�W BOM �ƮưO���C") ;

		return $new_id;

	} // end func	
	
	
	
	
	
	
	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++




} // end class


?>