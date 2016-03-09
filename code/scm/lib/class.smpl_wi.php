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

class SMPL_WI {
		
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
#	->check($parm)		�ˬd �[�J�s �s�y�O�O�� �O�_���T
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {
		$today=date('Y-m-d');
		$this->msg = new MSG_HANDLE();
		if(!$smpl = $GLOBALS['smpl_ord']->get($parm['smpl_id'])){
			$this->msg->add("Error ! Can't find this order record!");
			return false;
		}
			############### �ˬd��J����	
		if (!$parm['style_code'] ) {
			$this->msg->add("Error ! Please choice order on order list �C");
		}
		if (!$parm['size_scale'] ) {
			$this->msg->add("Error ! Make <b>SIZE RANGE</b> first, before father step.");
		}

		
		if (!$parm['unit'] ) {
			$this->msg->add("Error ! Please select Unit�C");
		}
		if ($parm['etd']<>$smpl['etd'])
		{
			if (!$parm['etd']){
				$this->msg->add("Error ! Please input currect ETD date �C");
			}
			if ($parm['etd'] < $today){
				$this->msg->add("Error ! Please input ETD > Today �C");
			}
		}
		if (count($this->msg->get(2))){
			return false;
		}
				
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check2($parm)		�ˬd �[�J�O�� �O�_���T [ edit �ɪ� check ]
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check2($parm) {
	
		$this->msg = new MSG_HANDLE();
		$today=date('Y-m-d');	
		if(!$smpl = $GLOBALS['smpl_ord']->get($parm['smpl_id'])){
			$this->msg->add("Error ! Can't find this order record!");
			return false;
		}
			############### �ˬd��J����	
		if ($parm['etd']<>$smpl['etd'])
		{
			if (!$parm['etd']){
				$this->msg->add("Error ! Please input currect date �C");
				return false;
			}
			if ($parm['etd'] < $today){
				$this->msg->add("Error ! Please input ETD > Today �C");
				return false;
			}
		}
		return true;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check3($parm)		�ˬd �[�J�O�� �O�_���T [ step2 �ɪ� check ]
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check3($parm) {

		$this->msg = new MSG_HANDLE();
			
			############### �ˬd��J����	

		//  ���ˬd���O �D�Ʈƥήƹw������J���� 



		return true;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �˥� �s�y�O�O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

//�d�̫߳᪩�� 
		$q_str = "SELECT cust_ver as ver FROM smpl_ord WHERE num='".$parm['wi_num']."' LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	


					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl_wi (style_code,
									dept,
									cust,
									cust_ver,
									wi_num,
									smpl_id,
									etd,
									unit,									
									creator,
									open_date,
									version
									) VALUES('".
									$parm['style_code']."','".
									$parm['dept']."','".
									$parm['cust']."','".
									$cust_row['ver']."','".
									$parm['wi_num']."','".
									$parm['smpl_id']."','".
									$parm['etd']."','".
									$parm['unit']."','".									
									$parm['creator']."',
									NOW(),".
									$parm['version'].")";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't add any data.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("Successfully add wi record: [".$parm['wi_num']."]�C") ;

		return $pdt_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	�j�M �s�y�O ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$rule=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT smpl_wi.*, cust.cust_init_name as cust_iname FROM smpl_wi, cust, smpl_ord ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("smpl_wi.id DESC");
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
	
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("smpl_wi.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}

	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("smpl_wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("smpl_wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}



	
	if ($team <> 'MD' && ($user_dept=='HJ' || $user_dept == 'LY' || $user_dept == 'WX'))
	{
		$srh->add_where_condition("smpl_ord.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
		$srh->add_where_condition("smpl_wi.status > 1");
	}else{
		if ($rule == 1)	$srh->add_where_condition("smpl_wi.status > 0");
	}
	
if ($mode==1){
		if ($str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("smpl_wi.dept = '$str'", "PHP_dept_code",$str,"Dept. = [ $str ]. "); }
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("smpl_wi.cust = '$str'", "PHP_cust",$str,"Search customer = [ $str ]. "); }
		if ($str = $argv['PHP_wi_num'] )  { 
			$srh->add_where_condition("smpl_wi.wi_num LIKE '%$str%'", "PHP_wi_num",$str,"Search wi # above: [ $str ] "); }
		if ($str = $argv['PHP_etdstr'] )  { 
			$srh->add_where_condition("smpl_wi.etd >= '$str'", "PHP_etdstr",$str,"search etd>=[ $str ]. "); 
			}
		if ($str = $argv['PHP_etdfsh'] )  { 
			$srh->add_where_condition("smpl_wi.etd <= '$str'", "PHP_etdfsh",$str,"search etd<=[ $str ]. "); 
			}	

}	
		$srh->add_where_condition("smpl_wi.cust = cust.cust_s_name AND smpl_wi.cust_ver = cust.ver");
		$srh->add_where_condition("smpl_wi.wi_num = smpl_ord.num");
		$srh->add_where_condition("smpl_ord.cust = cust.cust_s_name AND smpl_ord.cust_ver = cust.ver");
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
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

##--*****--2006.11.16���X�s�W start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16���X�s�W end

		return $op;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	�j�M �s�y�O ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_uncfm($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if ($mode=="wi") $q_header = "SELECT distinct smpl_wi.*, cust.cust_init_name as cust_iname, smpl_ord.size FROM smpl_wi, smpl_wiqty, cust, smpl_ord ";
		if ($mode=="bom") $q_header="SELECT distinct smpl_wi.*, cust.cust_init_name as cust_iname FROM `smpl_wi`,smpl_bom_acc,smpl_bom_lots, cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("smpl_wi.id DESC");
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

	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	// $sale_mang = $GLOBALS['SALES_MANG'];
	// for ($i=0; $i< sizeof($sale_f_mang); $i++)
	// {			
			// if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("smpl_wi.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	// }

	// $sales_dept = $GLOBALS['SALES_DEPT'];
	// if ($team == 'MD')	$srh->add_where_condition("smpl_wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	// for ($i=0; $i< sizeof($sales_dept); $i++)
	// {			
			// if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("smpl_wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	// }



		if ($mode=="wi")
		{
			$srh->add_where_condition("smpl_wi.status=0");	
			$srh->add_where_condition("smpl_wi.id=smpl_wiqty.wi_id");
			$srh->add_where_condition("smpl_wi.wi_num=smpl_ord.num");
		}
		if ($mode=="bom")
		{
			$srh->add_where_condition("smpl_wi.status=1");
			$srh->add_where_condition("smpl_wi.id=smpl_bom_acc.wi_id AND smpl_wi.id=smpl_bom_lots.wi_id");			
		}
		$srh->add_where_condition("smpl_wi.cust = cust.cust_s_name AND smpl_wi.cust_ver = cust.ver");   // ���p����M ���M�n�[
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
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

##--*****--2006.11.16���X�s�W start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16���X�s�W end

		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	�j�M �s�y�O ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_search($cust, $dept) {
		
		$search_day = increceDaysInDate(date('Y-m-d'),-270);
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		// $q_str = "SELECT id, wi_num FROM smpl_wi WHERE dept = '".$dept."' AND cust = '".$cust."' AND cfm_date > '".$search_day."'";
		// $q_str = "SELECT id, wi_num FROM smpl_wi WHERE dept = '".$dept."' AND cust = '".$cust."' ;";
		$q_str = "SELECT id, wi_num FROM smpl_wi WHERE cust = '".$cust."' AND cfm_date > '".$search_day."';";
		$q_str = "SELECT id, wi_num FROM smpl_wi WHERE cfm_date > '".$search_day."';";
//		echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=$j=0;
		while ($row = $sql->fetch($q_result)) {
			if ($j > 5)
			{
				$j=0; 
				$i++;
			}
			 $return[$i]['id'.$j]=$row['id'];
			 $return[$i]['wi_num'.$j]=$row['wi_num'];
			 $j++;
		}


		return $return;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	�j�M �s�y�O ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_ti_search($cust, $dept) {
		
		$search_day = increceDaysInDate(date('Y-m-d'),-270);
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		// $q_str = "SELECT id, wi_num FROM smpl_wi WHERE dept = '".$dept."' AND cust = '".$cust."' AND ti_cfm > '".$search_day."'";
		// $q_str = "SELECT id, wi_num FROM smpl_wi WHERE dept = '".$dept."' AND cust = '".$cust."' ;";
		// $q_str = "SELECT id, wi_num FROM smpl_wi WHERE cust = '".$cust."' ;";
		$q_str = "SELECT id, wi_num FROM smpl_wi WHERE cust = '".$cust."' AND ti_cfm > '".$search_day."' ;";
		$q_str = "SELECT id, wi_num FROM smpl_wi WHERE ti_cfm > '".$search_day."' ;";
		
		// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=$j=0;
		while ($row = $sql->fetch($q_result)) {
			if ($j > 5)
			{
				$j=0; 
				$i++;
			}
			 $return[$i]['id'.$j]=$row['id'];
			 $return[$i]['wi_num'.$j]=$row['wi_num'];
			 $j++;
		}


		return $return;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_num=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $wi_num=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM smpl_wi WHERE id='$id' ";
		} elseif ($wi_num) {
			$q_str = "SELECT * FROM smpl_wi WHERE wi_num='$wi_num' ";
		} else {
			$this->msg->add("Error ! Please point wi ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record for smpl_wi!");
			return false;    
		}
		$tmp_user=$GLOBALS['user']->get(0,$row['creator']);
		$row['creator_id'] = $row['creator'];
		if ($tmp_user['name'])$row['creator'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$row['updator']);
		$row['updator_id'] = $row['updator'];
		if ($tmp_user['name'])$row['updator'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$row['confirmer']);
		$row['confirmer_id'] = $row['confirmer'];
		if ($tmp_user['name'])$row['confirmer'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$row['bcfm_user']);
		$row['bcfm_user_id'] = $row['bcfm_user'];
		if ($tmp_user['name'])$row['bcfm_user'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$row['ti_cfm_user']);
		$row['ti_cfm_user_id'] = $row['ti_cfm_user'];
		if ($tmp_user['name'])$row['ti_cfm_user'] = $tmp_user['name'];			

		return $row;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_num=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_all($id=0, $wi_num=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM smpl_wi WHERE id='$id' ";
		} elseif ($wi_num) {
			$q_str = "SELECT * FROM smpl_wi WHERE wi_num='$wi_num' ";
		} else {
			$this->msg->add("Error ! Please point wi ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record for smpl_wi!");
			return false;    
		}
		$op['wi'] =$row;
		$op['wi']['open_date']=substr($op['wi']['open_date'],0,10);
		$op['wi']['cfm_date']=substr($op['wi']['cfm_date'],0,10);
		$op['wi']['last_update']=substr($op['wi']['last_update'],0,10);
		
		
		//����Login�b�����W�r
		$GLOBALS['user'];
		
		
		
		$tmp_user=$GLOBALS['user']->get(0,$op['wi']['creator']);
		$op['wi']['creator_id'] = $op['wi']['creator'];
		if ($tmp_user['name'])$op['wi']['creator'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$op['wi']['updator']);
		$op['wi']['updator_id'] = $op['wi']['updator'];
		if ($tmp_user['name'])$op['wi']['updator'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$op['wi']['confirmer']);
		$op['wi']['confirmer_id'] = $op['wi']['confirmer'];
		if ($tmp_user['name'])$op['wi']['confirmer'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$op['wi']['bcfm_user']);
		$op['wi']['bcfm_user_id'] = $op['wi']['bcfm_user'];
		if ($tmp_user['name'])$op['wi']['bcfm_user'] = $tmp_user['name'];			
		$tmp_user=$GLOBALS['user']->get(0,$op['wi']['ti_cfm_user']);
		$op['wi']['ti_cfm_user_id'] = $op['wi']['ti_cfm_user'];
		if ($tmp_user['name'])$op['wi']['ti_cfm_user'] = $tmp_user['name'];			
		
		
		
		
		
		if(!$op['smpl'] = $GLOBALS['smpl_ord']->get($op['wi']['smpl_id'])){ //sample order
			$this->msg->add("Error ! Can't access sample database!");
			$this->msg->merge($sql->msg);
			return false; 
		}
		
		$style_dir	= "./smpl_pic/";  				//�ۤ�
		$no_img		= "./images/graydot.gif";
		if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
			$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
		} else {
			$op['wi']['pic_url'] = $no_img;
		}
		
		//���X�D��
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$op['lots_use'] =  $GLOBALS['smplord_lots']->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		} 
		
				//  ���X�ƮƮưO��  -----------------------------------------------------
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$op['acc_use'] = $GLOBALS['smplord_acc']->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}

		//  ���X �Ͳ����� �O�� -------------------------------------------------
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' AND ti_new = 0";
		$T = $GLOBALS['smpl_ti']->search(0,$where_str,"id");  //���X�ӵ� �˥��D�ưO��
		$op['ti'] = $T['ti'];
		if (!count($op['ti'])){	$op['ti_NONE'] = "1";}


		$where_str = " WHERE wi_id = '".$op['wi']['id']."' AND item = 'Comment' AND ti_new = 1";
		$T = $GLOBALS['smpl_ti']->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��		

		if (count($T['ti']))
		{
			$op['comment']['detail'] = $T['ti'][0]['detail'];
			$op['comment']['id'] = $T['ti'][0]['id'];
			$op['comm_flag'] = 1;
		}else{
			$op['comm_flag'] = 0;
		}

		$sub_item = array();
		$q_str = "SELECT sub_item FROM smpl_ti WHERE wi_id='".$op['wi']['id']."' AND ti_new = 1  GROUP BY sub_item ORDER BY id ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$sub_item[] = $row['sub_item'];
		}
	
		if(sizeof($sub_item) > 0) $op['sub_ti'] = array();
		for($i=0; $i<sizeof($sub_item); $i++)
		{
			
			$where_str = " WHERE wi_id = '".$op['wi']['id']."' AND ti_new = 1 AND item <> 'Comment' AND sub_item = '".$sub_item[$i]."' ";
			$T = $GLOBALS['smpl_ti']->search(0,$where_str,'id');  //���X�ӵ� �˥��D�ưO��					
			if (count($T['ti']))
			{
				 if($sub_item[$i] == '')$sub_item[$i] ='main';
				 $op['sub_ti'][$i]['name'] = $sub_item[$i];
				 
				 
				 $op['sub_ti'][$i]['i'] = $i;
				 $op['sub_ti'][$i]['new_ti'] = 0;
				 $op['sub_ti'][$i]['txt'] = $T['ti'];
				 //echo $op['sub_ti'][$i]['txt'][0]['item']."<BR>";
			}
			

		}


		return $op;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_edit($parm)		��s �s�y�O �O�� [ �[�J�s �s�O��step2]
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_edit($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE wi SET etd='"			.$parm['etd'].
							"',	smpl_type='"	.$parm['smpl_type'].
							"', unit='"			.$parm['unit'].
							"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		
		
		return $pdt_id;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �s�y�O �O��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_wi SET version='"	.$parm['version'].							
							"', etd='"			.$parm['etd'].
							"', unit='"			.$parm['unit'].
							"', updator='"		.$parm['updator'].
							"', last_update=	NOW()" .
						"  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_wi SET ".$parm[1]."='".$parm[2]."'  WHERE id=".$parm[0]." ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R�� �s�@�O�O��  [��ID]�R��
#							$mode=0: $id= �O����id; $mode<>0: $id=�s�O�s�� wi_num
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! Please point wi ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM smpl_wi WHERE wi_num='$id' ";
		}else{
			$q_str = "DELETE FROM smpl_wi WHERE id='$id' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}



		return true;
	} // end func

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM wi ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access database!");
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
*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function submits_wi($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_wi SET status= '1' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE smpl_ord SET m_status = '2' where order_num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cfm($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_wi SET status= '1', cfm_date='".$parm['date']."', confirmer='".$parm['user']."' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE smpl_ord SET m_status = '2' where num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_wi SET status= '0', cfm_date='".$parm['date']."', confirmer='".$parm['user']."', revise='".$parm['rev']."' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE smpl_ord SET m_status = '1' where num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field2($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE `smpl_wi` SET `".$parm[1]."`='0', `".$parm[2]."`='', `".$parm[3]."`='' WHERE `id` = '".$parm[0]."' ;";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function submits_bom($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_wi SET status= '2' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE smpl_ord SET m_status = '3' where order_num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_cfm($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_wi SET status= '2', bcfm_date='".$parm['date']."', bcfm_user='".$parm['user']."' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE smpl_ord SET m_status = '3' where num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise_bom($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_wi SET status= '1', bcfm_date='".$parm['date']."', bcfm_user='".$parm['user']."', bom_rev='".$parm['bom_rev']."' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE smpl_ord SET m_status = '2' where num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_size($num) {

		$sql = $this->sql;
		############### �ˬd��J����
		$flag=false;
		#####   ��s��Ʈw���e
		$q_str = "SELECT id FROM smpl_wi WHERE style_code='".$num."'";
				
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return true;    
		}		
		$id=$row['id'];
		$q_str = "SELECT id FROM smpl_wiqty WHERE wi_id='".$id."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$flag=true;    	    
		}
		return $flag;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_wi($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function copy_wi($wi_num,$smpl_id,$new_num,$etd,$date,$user,$dept,$cust)
{
		$sql = $this->sql;
		$q_str = "SELECT * FROM smpl_ord WHERE num='".$wi_num."'";		
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$smpl = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		$q_str="UPDATE smpl_ord SET	size ='".$smpl['size']."' where num='".$new_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		
		$q_str = "SELECT * FROM smpl_acc_use WHERE smpl_code='".$wi_num."' ";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		while ($row = $sql->fetch($q_result)) {
			$str_acc[$j]= "insert into `smpl_acc_use` set ";
			foreach ($row as $key => $value)
			{
				if ($key=='use_for')
				{
					for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
					{
						$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
					}
					$value = str_replace("'","\'",$value);		
				}
				if (!is_int($key) && $key<>'id' && $key<>'smpl_code') 
					$str_acc[$j]=$str_acc[$j].$key."='".$value."',";					
			}
			$str_acc[$j]=$str_acc[$j]."smpl_code='".$new_num."'";
			$j++;
			
		}
		
		$q_str = "SELECT * FROM smpl_lots_use WHERE smpl_code='".$wi_num."' ";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		while ($row = $sql->fetch($q_result)) {
			$str_lots[$j]= "insert into `smpl_lots_use` set ";
			foreach ($row as $key => $value)
			{
				if ($key=='use_for')
				{
					for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
					{
						$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
					}
					$value = str_replace("'","\'",$value);		
				}
				if (!is_int($key) && $key<>'id' && $key<>'smpl_code') 
					$str_lots[$j]=$str_lots[$j].$key."='".$value."',";					
			}
			$str_lots[$j]=$str_lots[$j]."smpl_code='".$new_num."'";
			$j++;
			
		}
		if (isset($str_lots))
		{
			for ($i=0; $i<sizeof($str_lots); $i++)
			{
				if (!$q_result = $sql->query($str_lots[$i])) {
					$this->msg->add("Error ! Can't access sample_lots_use table for add!".$i);
					$this->msg->merge($sql->msg);
					return false;    
				}
			}
		}
		if (isset($str_acc))
		{
			for ($i=0; $i<sizeof($str_acc); $i++)
			{
				if (!$q_result = $sql->query($str_acc[$i])) {
					$this->msg->add("Error ! Can't access sample_acc_use table for add!".$i);
					$this->msg->merge($sql->msg);
					return false;    
				}
			}
		}
		
		
				
		$q_str = "SELECT * FROM smpl_wi WHERE style_code='".$wi_num."'";				
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		if (!$row = $sql->fetch($q_result)) {
			$str_wi= "insert into `smpl_wi` set ";
			$str_wi=$str_wi."wi_num='".$new_num."', style_code='".$new_num."', smpl_id='".$smpl_id."', etd='".$etd."', open_date='".$date."', creator='".$user."', version='001', cust='".$cust."', dept='".$dept."'";    
		}else{
			$old_id=$row['id'];
			$str_wi= "insert into `smpl_wi` set ";
			foreach ($row as $key => $value)
			{
				if (!is_int($key) && ($key=='dept' || $key=='cust' || $key=='smpl_type' || $key=='unit' || $key=='cust_ver')) 
					$str_wi=$str_wi.$key."='".$value."',";					
			}
			$str_wi=$str_wi."wi_num='".$new_num."', style_code='".$new_num."', smpl_id='".$smpl_id."', etd='".$etd."', open_date='".$date."', creator='".$user."', version='001'";
		}	
		if (!$q_result = $sql->query($str_wi)) {
			$this->msg->add("Error ! Can't access sample_wi table for add!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$pdt_id = $sql->insert_id();  //���X �s�� id
		
		
		
		if (isset($old_id))
		{
			$q_str = "SELECT * FROM smpl_wiqty WHERE wi_id='".$old_id."' ";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_wiqty[$j]= "insert into `smpl_wiqty` set ";
				foreach ($row_add as $key => $value)
				{
					if (!is_int($key) && $key<>'id' && $key<>'wi_id') 
						$str_wiqty[$j]=$str_wiqty[$j].$key."='".$value."', ";					
				}
				$str_wiqty[$j]=$str_wiqty[$j]." wi_id='".$pdt_id."'";
				$j++;
			}
			
/*			
			$q_str = "SELECT * FROM smpl_ti WHERE wi_id='".$old_id."' ";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_ti[$j]= "insert into `smpl_ti` set ";
				foreach ($row_add as $key => $value)
				{
					if ($key=='detail')
					{
						for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
						{
							$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
						}
					}
					if ($key=='detail') $value=str_replace("'", "\'",$value);
					if (!is_int($key) && $key<>'id' && $key<>'wi_id') 
						$str_ti[$j]=$str_ti[$j].$key."='".$value."', ";					
				}
				$str_ti[$j]=$str_ti[$j]." wi_id='".$pdt_id."'";
				$j++;	
			}
			
			$q_str = "SELECT * FROM smpl_file_det WHERE num ='".$wi_num."' ";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_file[$j]= "insert into `smpl_file_det` set ";
				foreach ($row_add as $key => $value)
				{
					if (!is_int($key) && $key<>'id' && $key<>'num') 
						$str_file[$j]=$str_file[$j].$key."='".$value."', ";					
				}
				$str_file[$j]=$str_file[$j]." num='".$new_num."'";
				$j++;			
			}
*/			
			
			if (isset($str_wiqty))
			{
				for ($i=0; $i<sizeof($str_wiqty); $i++)
				{
					if (!$q_result = $sql->query($str_wiqty[$i])) {
						$this->msg->add("Error ! Can't access sample_wiqty table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
/*			
			if (isset($str_ti))
			{
				for ($i=0; $i<sizeof($str_ti); $i++)
				{
					if (!$q_result = $sql->query($str_ti[$i])) {
						$this->msg->add("Error ! Can't access sample_ti table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
			
			if (isset($str_file))
			{
				for ($i=0; $i<sizeof($str_file); $i++)
				{
					if (!$q_result = $sql->query($str_file[$i])) {
						$this->msg->add("Error ! Can't access sample_file_det table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}			
	*/		
		}
		

		return $pdt_id;
		
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_wi($parm)		��s ��ưO���� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function copy_ti($old_id,$pdt_id)
{
		$sql = $this->sql;

			
			$q_str = "SELECT * FROM smpl_ti WHERE wi_id='".$old_id."' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_ti[$j]= "insert into `smpl_ti` set ";
				foreach ($row_add as $key => $value)
				{
					if ($key=='detail' || $key=='item')
					{
						for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
						{
							$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
						}
					}
					if ($key=='detail') $value=str_replace("'", "\'",$value);
					if ($key=='item') $value=str_replace("'", "\'",$value);
					if (!is_int($key) && $key<>'id' && $key<>'wi_id' && $key<>'date') 
						$str_ti[$j]=$str_ti[$j].$key."='".$value."', ";					
				}
				$str_ti[$j].="date='".date("Y-m-d")."',";
				$str_ti[$j]=$str_ti[$j]." wi_id='".$pdt_id."'";
				$j++;	
			}
			

		
			if (isset($str_ti))
			{
				for ($i=0; $i<sizeof($str_ti); $i++)
				{
					
					if (!$q_result = $sql->query($str_ti[$i])) {
						$this->msg->add("Error ! Can't access sample_ti table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
				

		return true;
		
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_ti2	2011/11/14
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function copy_ti2($old_id,$pdt_id){
		$sql = $this->sql;

			
			$q_str = "SELECT * FROM smpl_ti WHERE wi_id='".$old_id."' and item <> 'Comment' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k�s����Ʈw!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_ti[$j]= "insert into `ti` set ";
				foreach ($row_add as $key => $value)
				{
					if ($key=='detail' || $key=='item')
					{
						for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
						{
							$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
						}
					}
					if ($key=='detail') $value=str_replace("'", "\'",$value);
					if ($key=='item') $value=str_replace("'", "\'",$value);
					if (!is_int($key) && $key<>'id' && $key<>'wi_id' && $key<>'date') 
						$str_ti[$j]=$str_ti[$j].$key."='".$value."', ";					
				}
				$str_ti[$j].="date='".date("Y-m-d")."',";
				$str_ti[$j]=$str_ti[$j]." wi_id='".$pdt_id."'";
				$j++;	
			}
			

		
			if (isset($str_ti))
			{
				for ($i=0; $i<sizeof($str_ti); $i++)
				{
					
					if (!$q_result = $sql->query($str_ti[$i])) {
						$this->msg->add("Error ! Can't access sample_ti table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
		return true;
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pdf_etd($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_pdf_etd($ord_num){
		$sql = $this->sql;
			
		$q_str = "SELECT p_etd
				  FROM order_partial
				  WHERE ord_num = '".$ord_num."' 
				  ORDER BY p_etd asc limit 0,1";
		
		if (!$q_result = $sql->query($q_str)) {
			return true;    
		}
		
		$row = $sql->fetch($q_result);
		

		return $row['p_etd'];
		
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>