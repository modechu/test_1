<?php 

#+++++++++++++++ SMPL_ORDER  class ##### �˥����O +++++++++++++++++++++++++++++++++
#	->init($sql)		�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->check($parm)		�ˬd �[�J�s �q��O�� �O�_���T
#	->add($parm)		�[�J�s�O��
#	->search($mode=0, $dept='',$limit_entries=0)	�j�M �q �� ���
#	->get_order_like($str)	�j�M �q�渹�X �ۦ� $str �������q�渹�X(�̰���100��)
#	->supplier_search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#	->schedule_search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#	->check_pttn_schedule($parm)	 �[�J �q��PATTERN  �Ƶ{ CHECK��J
#	->add_pttn_schedule($parm)		 �[�J �q��PATTERN  �Ƶ{
#	->check_smpl_schedule($parm)	 �[�J �q�� SAMPLE  �Ƶ{ CHECK��J
#	->add_smpl_schedule($parm)		 �[�J �q�� SAMPLE  �Ƶ{
#	->output_search($mode=0, $dept='',$limit_entries=0) �j�M �q����(���X�C��)
#	->get_fieldvalue($id, $field)	��X���w�O�����Y���� RETURN field.value
#	->get($id=0, smpl_type=0)	��X���w�O������� RETURN $row[]
#	->edit($parm)				��s ���
#	->update_field($parm)		��s ��Ƥ� �Y�ӳ�@���
#	->update_status($id, $newstatus)	��s  ���p�����(status)
#	->regist_update($id)				��s  �̫��s�H�ήɶ����
#	->upload_pttn($parm)		�W�� �O�l
#	->upload_marker($parm)		�W�� marker
#	->pttn_resend($parm)	��s �O�l���p �ϥi�H�A���e�X pttn
#	->marker_resend($parm)	��s marker ���p �ϥi�H�A���e�X marker
#	->add_complete($parm)		��s  �w�����ƶq(�֥[)

#	->del($id)					�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	
#							���X���� SMPL TYPE �� $n_field �m�Jarry return

#	->case_close($parm)		�˥� case close
#	->is_sample_team($team)	�O���O �˥���team[user���էO]
#	->add_spt($parm)		 �[�J �u�� (��)
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL_ORD {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! cannnot connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		�ˬd �[�J�s �q��O�� �O�_���T
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();

			############### �ˬd��J����	
		if (!$parm['smpl_type'] ) {
			$this->msg->add("Error ! please select the '<b>SAMPLE TYPE</b>' �C");	}
		if (!$parm['ref'] ) {
			$this->msg->add("Error ! please support the customer's '<B>STYLE NUMBER or CODE</B>' �C");	}
		if (!$parm['style_cat'] ) {
			$this->msg->add("Error ! please select the '<b>STYLE CATEGORY</b>' �C"); }

		if ($parm['etd'] == "") { //��S��J�����---
			$this->msg->add("Error ! please submit sample <b>ETD date</b>�C");
		} elseif($parm['etd'] < $GLOBALS['TODAY']){
			$this->msg->add("<b>warning !</b> your submit ETD date is due !!");
		}
		if (($parm['etd'] !='--') && (!checkdate(substr($parm['etd'],5,2), substr($parm['etd'],8,2), substr($parm['etd'],0,4)))){
			$this->msg->add("Error ! incorrect <b>ETD date</b>�C");
		}
		if (!$parm['style_type'] ) {
			$this->msg->add("Error ! please select '<b>STYLE TYPE</b>'�C");
		}
		if (!$parm['fab_substu'] && ($parm['feta'] == "--") ) {
			$this->msg->add("Error ! since no substitude fabric, you have to set the <b>febric ETA date</b>�C");
		}
		if (($parm['feta'] !='') && ($parm['etd'] < $parm['feta'])) {
			$this->msg->add("<b>warning !</b> the fabric ETA date is later then sample ETD date�C");
		}
		if (($parm['feta'] !='') && (!checkdate(substr($parm['feta'],5,2), substr($parm['feta'],8,2), substr($parm['feta'],0,4)))){
			$this->msg->add("Error ! incorrect <b>fabric ETA date</b>�C");
		}
		if (!$parm['acc_substu'] && ($parm['aeta'] == "") ) {
			$this->msg->add("Error ! since no substitude accessory, you have to set the accessory <b>ETA date</b> �C");
		}
		if (($parm['aeta'] !='') && ($parm['etd'] < $parm['aeta'])) {
			$this->msg->add("<b>warning !</b> the accessory ETA date is later then sample ETD date�C");
		}
		if (($parm['aeta'] !='') && (!checkdate(substr($parm['aeta'],5,2), substr($parm['aeta'],8,2), substr($parm['aeta'],0,4)))){
			$this->msg->add("Error ! <b>incorrect accessory ETA date</b>�C");
		}
		if ($parm['pttn_suppt']  && ($parm['peta'] == "") ) {
			$this->msg->add("Error ! since customer support pattern, you have to set the <b>pattern ETA date</b> �C");
		}
		if (($parm['peta'] !='') && ($parm['etd'] < $parm['peta'])) {
			$this->msg->add("<b>warning !</b> customer pattern support date is later then sample ETD date�C");
		}
		if (($parm['peta'] !='') && (!checkdate(substr($parm['peta'],5,2), substr($parm['peta'],8,2), substr($parm['peta'],0,4)))){
			$this->msg->add("Error ! incorrect <b>customer pattern ETA date</b>�C");
		}
		if (!is_numeric($parm['rq'])) {
			$this->msg->add("Error ! please type <b>only numberic</b> for quantity required�C");
		}
		if (!is_numeric($parm['backup'])) {
			$this->msg->add("Error ! please type <b>only numberic</b> for quantity backup�C");
		}
		if (!$parm['unit'] ) {
			$this->msg->add("Error ! please select the '<b>UNIT</b>' �C");
		}
		if (!$parm['factory'] ) {
			$this->msg->add("Error ! please select the sampling '<b>MAKER</b>' �C");
		}

		if (count($this->msg->get(2))){
			return false;
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s�O��
#		�[�J�s �O��			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add($parm) {

    $sql = $this->sql;

    # �ˬd�O�_������
    $q_str = "SELECT id FROM smpl_ord WHERE num='".$parm['num']."'";
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot access database.");
        $this->msg->merge($sql->msg);
        return false;    
    }
    if ($sql->num_rows($q_result) ) {
        $this->msg->add("SORRY ! sample order number duplicate, please re-Append again!");
        return false;    	    
    }
    
    # �[�J��Ʈw

    //�d�̫߳᪩�� 
    $q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
    $q_result = $sql->query($q_str);
    $cust_row = $sql->fetch($q_result);	
    # spt not null. ie
    if(!isset($parm['spt']))$parm['spt'] = '0';
    $q_str = "INSERT INTO smpl_ord (num,dept,cust,cust_ver,ref,factory,style,spt,qty,rq,backup,unit,smpl_type,style_cat,fab_substu,acc_substu,pttn_suppt,
                                    etd,feta,aeta,peta,creator,open_date,last_update,orders,updator,smpl_code,ie,des,status) VALUES 			
                    ('".$parm['num']."','".
                        $parm['dept']."','".
                        $parm['cust']."','".
                        $cust_row['ver']."','".
                        $parm['ref']."','".
                        $parm['factory']."','".
                        $parm['style_type']."',".
                        $parm['spt'].",".
                        $parm['qty'].",".
                        $parm['rq'].",".
                        $parm['backup'].",'".
                        $parm['unit']."','".
                        $parm['smpl_type']."','".
                        $parm['style_cat']."','".
                        $parm['fab_substu']."','".
                        $parm['acc_substu']."','".
                        $parm['pttn_suppt']."','".
                        $parm['etd']."','".
                        $parm['feta']."','".
                        $parm['aeta']."','".
                        $parm['peta']."','".
                        $parm['creator']."','".
                        $parm['open_date']."','".
                        $parm['last_update']."','".
                        $parm['orders']."','".
                        $parm['updator']."','".
                        $parm['smpl_code']."','".
                        $parm['ie']."','".
                        $parm['des']."','
                        -1')";
                        
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot adding record to database.");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    $smpl_ord_id = $sql->insert_id();  //���X �s�� id
    $this->msg->add("sucessful adding sample order : [".$parm['num']."]�C") ;
    
    //�s�W���ɤW��07.03.23		
    if($parm['pic_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
        // �D�� ������ӹϤW��[�j��]��[�p��]
        //���ɥؿ�(�H pdt_id �ӳ]�����ɦW
        $upFile = new uploadIMG();
        $style_dir	= $GLOBALS['config']['root_dir']."/smpl_pic/";  

                    # �W�ǹϬ۳B�z
        //�W�ǹ� 600X600
        $upFile->setSaveTo($style_dir,$parm['num'].".jpg");
        $up_result = $upFile->upload($parm['pic'], 600, 600);
        
        //�W�ǹ� 200X200
        $upFile->setSaveTo($style_dir,"s_".$parm['num'].".jpg");
        $up_result = $upFile->upload($parm['pic'], 100, 100);

        if ($up_result){
            $this->msg->add("successful upload main picture");
        } else {
            $this->msg->add("failure upload main picutre");
        }
    }
    
    $this->add_order_link(substr($parm['num'],0,-2));
    return $smpl_ord_id;
    
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $dept='',$limit_entries=0,$where_str='') {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT smpl_ord.*, smpl_wi.cfm_date FROM smpl_ord LEFT JOIN smpl_wi ON smpl_ord.num=smpl_wi.wi_num ";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}		
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("smpl_ord.id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
#--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end    
	}

	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	# ���������
	if ( $dept ) {
		$srh->add_where_condition("smpl_ord.dept = '$dept'", "",$dept,"Department=[ $dept ]. ");
	} else {
		if ($user_team == 'MD') {
            $srh->add_where_condition("smpl_ord.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
        } else {
            if ( if_factory() && !$argv['PHP_factory'] ) {
                $factory_group = get_factory_group();
                for ($i=0; $i< sizeof($factory_group); $i++) {			
                    $srh->or_where_condition("smpl_ord.factory = '$factory_group[$i]'", "",$factory_group[$i],"Factory=[ $factory_group[$i] ]. ");
                }
            } else {
                $dept_group = get_dept_group();
                for ($i=0; $i< sizeof($dept_group); $i++) {			
                    $srh->or_where_condition("smpl_ord.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
                }
            }
        }
	}
    
   if ($mode==1){
		$mesg = '';
		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("smpl_ord.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref.# : [ $str ]. ";
		}
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("smpl_ord.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
		}
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("smpl_ord.num LIKE '%$str%'", "PHP_num",$str); 
			$mesg.= "  Sample# : [ $str ]. ";
		}
		if ($str = $argv['PHP_factory'])  { 			
			$srh->add_where_condition("smpl_ord.factory = '$str'", "PHP_factory",$str," factory=[ $str ]. "); 
			$mesg.= "  FTY = [ $str ]. ";
		}
		if ($str = $argv['PHP_op_str'])  { 		
			$srh->add_where_condition("smpl_ord.open_date >= '$str'", "PHP_factory",$str," submit_date >=[ $str ]. "); 
			$mesg.= "  Open Date :  $str ~ ";
		}
		if ($str = $argv['PHP_op_fsh'])  { 		
			$srh->add_where_condition("smpl_ord.open_date <= '$str'", "PHP_factory",$str," submit_date <=[ $str ]. "); 
			if($argv['PHP_op_str']){$op_msg='';}else{$op_msg="Open Date : ~ ";}
			$mesg.= $op_msg." $str ";		
		}

		if ($str = $argv['SCH_wi'] )  { 
			$srh->add_where_condition("(smpl_wi.cfm_date != '0000-00-00 00:00:00' AND smpl_wi.cfm_date IS NOT NULL ) AND (smpl_wi.ti_cfm != '0000-00-00' AND smpl_wi.ti_cfm IS  NOT NULL)", "SCH_wi",$str); 
			$mesg.= " WI/WS Submitted ";		
		}  
		if ($str = $argv['SCH_close'] )  { 
			$srh->add_where_condition("smpl_ord.status <> 10", "SCH_close",$str); 
			$mesg.= " Exclude 'Case Closed' ";		
		}   
		if ($str = $argv['SCH_pend'] )  { 
			$srh->add_where_condition("smpl_ord.status <> 7", "SCH_pend",$str); 
			$mesg.= " Exclude 'Pending' ";		
		}

		if ($str = $argv['SCH_smpl_type'])  { 			
			$srh->add_where_condition("smpl_ord.smpl_type = '$str'", "SCH_smpl_type",$str," smpl type =[ $str ]. "); 
		}
        
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
	}
	
	if ($mode==2){
		$srh->add_where_condition("smpl_ord.status = -2 ", "status",$str); 
	}
   
	$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
		if (!$result){   // ��d�M�L��Ʈ�
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
    // echo $srh->q_str;
	#--*****--##2006.11.14�s���X�ݭn��oup_put	start		
	if(!$limit_entries){
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];
	}
	#--*****--##2006.11.14�s���X�ݭn��oup_put	end

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_order_like($str) �j�M �q�渹�X �ۦ� $str �������q�渹�X(�̰���100��)
#					
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_like($str) {

		$sql = $this->sql;

		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT num FROM smpl_ord WHERE num LIKE '%$str%'";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2(100);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		return $result;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ��� (�S���Ψ�)
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function wi_search($mode=0, $where_str='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT smpl_ord.*, size_des.size_scale FROM smpl_ord left join size_des on size_des.id=smpl_ord.size  ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("smpl_ord.id DESC");
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
			if (!$result){   // ��d�M�L��Ʈ�
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
#	->fa_search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function fa_search($mode=0, $dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT smpl_ord.*, smpl_wi.id as wi_id, smpl_wi.status as wi_status, smpl_wi.ti_cfm, smpl_wi.ti_cfm FROM smpl_ord Left Join smpl_wi ON wi_num = num ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("smpl_ord.id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
#--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end  
	}

	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("smpl_ord.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
	
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($user_team == 'MD')	$srh->add_where_condition("smpl_ord.dept = '$user_dept'", "",$dept,"department=[ $user_dept ]. ");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("smpl_ord.dept = '$user_dept'", "",$dept,"department=[ $user_dept ]. ");
	}
	
	
	if ($mode==2){
		if ($user_team <> 'MD' && ($user_dept=='HJ' || $user_dept == 'LY' || $user_dept == 'WX'))
		{
			$srh->add_where_condition("smpl_ord.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
			$srh->add_where_condition("smpl_wi.status > 0");
		}
	//	$srh->add_where_condition("smpl_ord.status < 6 && smpl_ord.qty_done = 0");
		$srh->add_where_condition("smpl_ord.qty_done = 0");
	}
   if ($mode>=1){
		$mesg = '';
		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("smpl_ord.ref LIKE '%$str%'", "PHP_ref",$str," customer reference include:[ $str ] "); 
			$mesg.= "  ref.# : [ $str ]. ";
		}
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("smpl_ord.cust = '$str'", "PHP_cust",$str," customer=[ $str ]. "); 
			$mesg.= "  Cust. = [ $str ]. ";
		}
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("smpl_ord.num LIKE '%$str%'", "PHP_num",$str," order number include:[ $str ] "); 
			$mesg.= "  Sample# : [ $str ]. ";
		}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("smpl_ord.factory = '$str'", "PHP_factory",$str," factory=[ $str ]. "); 
			$mesg.= "  FTY : [ $str ]. ";
		}
		if ($str = $argv['PHP_etdstr'] )  { 
			$srh->add_where_condition("smpl_ord.etd >= '$str'", "PHP_factory",$str," ETD >=[ $str ]. "); 
			$mesg.= "  ETD :  $str ~ ";
		}
		if ($str = $argv['PHP_etdfsh'] )  { 
			$srh->add_where_condition("smpl_ord.etd <= '$str'", "PHP_factory",$str," ETD <=[ $str ]. "); 
			if($argv['PHP_etdstr']){$etd_msg='';}else{$etd_msg=" ETD : ~ ";}
			$mesg.= $etd_msg." $str ";
		}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
   }	
   		$srh->add_where_condition("smpl_ord.status >= 0");
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
            // echo $srh->q_str;
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		// echo $srh->q_str;
#--*****--##2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#--*****--##2006.11.14�s���X�ݭn��oup_put	end


		return $op;
	} // end func



	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->supplier_search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function supplier_search($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
    $Today = date('Y-m-d');
    
    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT smpl_ord.*, smpl_wi.cfm_date as wi_cfm , smpl_wi.id as wi_id, smpl_wi.ti_cfm FROM smpl_ord LEFT JOIN smpl_wi  ON smpl_ord.num=smpl_wi.wi_num";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("smpl_ord.etd DESC");
    $srh->row_per_page = 20;

    if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
            $srh->q_limit = "LIMIT ".$limit_entries." ";
    }else{
        #--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
        $pagesize=10;
        if ($argv['PHP_sr_startno']) {
            $pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
            $pages = $srh->get_page(1,$pagesize);
        } 
        ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end  
    }
    
	# ���������
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("smpl_ord.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("smpl_ord.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {			
                $srh->or_where_condition("smpl_ord.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    if ($mode==1){

        if ( $str = $argv['PHP_dept'] ) { 
			$srh->add_where_condition("smpl_ord.dept = '$str'", "PHP_dept",$str,"  department = [ $str ]. "); 
        }
        // ���M�[�J�� supplier search ...... status < 6
        //���M�[�J�� supplier search .....	status>=0 on 20070319
        if ($str = 6 )  { 
            $srh->add_where_condition("(smpl_ord.status < $str && smpl_ord.status >= 0)", "",$str,"search for supplier feature ");			
        }
        if ($str = strtoupper($argv['PHP_ref']) )  { 
            $srh->add_where_condition("smpl_ord.ref LIKE '%$str%'", "PHP_ref",$str," customer reference include:[ $str ] "); 
        }
        if ($str = $argv['PHP_cust'] )  { 
            $srh->add_where_condition("smpl_ord.cust = '$str'", "PHP_cust",$str," customer=[ $str ]. "); 
        }
        if ($str = $argv['PHP_num'] )  { 
            $srh->add_where_condition("smpl_ord.num LIKE '%$str%'", "PHP_num",$str," order number include:[ $str ] "); 
        }
        if ($str = $argv['PHP_factory'] )  { 
            $srh->add_where_condition("smpl_ord.factory = '$str'", "PHP_factory",$str," factory=[ $str ]. "); 
        }
    //�s�Wsearch����
        if ($str = $argv['SCH_wi'] )  { 
            $srh->add_where_condition("(smpl_wi.cfm_date != '0000-00-00 00:00:00' AND smpl_wi.cfm_date IS NOT NULL ) AND (smpl_wi.ti_cfm != '0000-00-00' AND smpl_wi.ti_cfm IS  NOT NULL)", "SCH_wi",$str," WI/WS Submitted "); 
        }  
        if ($str = $argv['SCH_close'] )  { 
            $srh->add_where_condition("smpl_ord.status <> 10", "SCH_close",$str," Exclude 'Case Closed' "); 
        }   
        if ($str = $argv['SCH_pend'] )  { 
            $srh->add_where_condition("smpl_ord.status <> 7", "SCH_pend",$str," Exclude 'Pending' "); 
        }			
        if ($str = $argv['SCH_pttnd'] )  { 
            $srh->add_where_condition("(smpl_ord.rcvd_pttn <>'0000-00-00' OR smpl_ord.pttn_suppt = '')  AND smpl_ord.done_pttn = '0000-00-00' AND smpl_ord.schd_pttn ='0000-00-00'", "SCH_pttnd",$str," Customer Pattern delivered or No Customer Pattern. "); 
        }			
        if ($str = $argv['SCH_fab'] )  { 
            $srh->add_where_condition("smpl_ord.rcvd_fab = '0000-00-00' AND smpl_ord.feta < '$Today' AND smpl_ord.fab_substu = ''", "SCH_fab",$str," Fabric Not Delivered, over ETA. "); 
        }	  
        if ($str = $argv['SCH_acc'] )  { 
            $srh->add_where_condition("smpl_ord.rcvd_acc = '0000-00-00' AND smpl_ord.aeta < '$Today' AND smpl_ord.acc_substu = ''", "SCH_acc",$str," Accessories Not Delivered, over ETA. "); 
        }	
        if ($str = $argv['SCH_ptno'] )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn = '0000-00-00' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptno",$str," Pattern Not Scheduled. "); 
        }	
        if ($str = $argv['SCH_ptdu'] == 1 )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn <> '0000-00-00' AND smpl_ord.schd_pttn < '$Today' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptdu",$str," Pattern Scheduled, but over due "); 
        }	
        
        if ($str = $argv['SCH_ptdu'] == 2 )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn <> '0000-00-00' AND smpl_ord.schd_pttn >= '$Today' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptdu",$str," Pattern Scheduled, not over due "); 
        }
        if ($str = $argv['SCH_usmp'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.schd_smpl = '0000-00-00' AND smpl_ord.rcvd_fab <> '0000-00-00' AND smpl_ord.rcvd_acc <> '0000-00-00' AND smpl_ord.status <> 10", "SCH_usmp",$str," Pattern Uploaded, Sample Not Scheduled. "); 
        }	
        if ($str = $argv['SCH_smpdu'] == 1 )  { 
            $srh->add_where_condition("smpl_ord.schd_smpl <> '0000-00-00' AND smpl_ord.schd_smpl < '$Today' AND smpl_ord.qty_done < smpl_ord.qty AND smpl_ord.done_smpl  = '0000-00-00'", "SCH_smpdu",$str," Sample Scheduled, but over due. "); 
        }	
        if ($str = $argv['SCH_smpdu'] == 2 )  { 
            $srh->add_where_condition("smpl_ord.schd_smpl <> '0000-00-00' AND smpl_ord.schd_smpl >= '$Today' AND smpl_ord.qty_done < smpl_ord.qty AND smpl_ord.done_smpl  = '0000-00-00'", "SCH_smpdu",$str," Sample Scheduled, not over due. "); 
        }	
        if ($str = $argv['SCH_qty'] )  { 
            $srh->add_where_condition("smpl_ord.qty_done <> 0 AND smpl_ord.qty_done < smpl_ord.qty", "SCH_qty",$str," Sample Not Completed. "); 
        }	
        if ($str = $argv['SCH_ie'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.ie = 0", "SCH_ie",$str," SPT Not submitted. "); 
        }	
        if ($str = $argv['SCH_mk'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.marker_date = '0000-00-00'", "SCH_mk",$str," Mini Marker Not Uploaded. "); 
        }	

        if ($str = $argv['PHP_op_str'])  { 
            
            $srh->add_where_condition("smpl_ord.open_date >= '$str'", "PHP_factory",$str," open date >=[ $str ]. "); 
        }
        if ($str = $argv['PHP_op_fsh'])  { 
            
            $srh->add_where_condition("smpl_ord.open_date <= '$str'", "PHP_factory",$str," open date <=[ $str ]. "); 
        }


        if ($str = $argv['SCH_smpl_type'])  { 			
            $srh->add_where_condition("smpl_ord.smpl_type = '$str'", "SCH_smpl_type",$str," smpl type =[ $str ]. "); 
        }
    }	
    $result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }

    $this->msg->merge($srh->msg);
    if (!$result){   // ��d�M�L��Ʈ�
        $op['record_NONE'] = 1;
    }
    $op['sorder'] = $result;  // ��ƿ� �ߤJ $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['max_no'] = $srh->max_no;
    $op['start_no'] = $srh->start_no;
    
    #--*****--##2006.11.14�s���X�ݭn��oup_put	start		
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
    #--*****--##2006.11.14�s���X�ݭn��oup_put	end
    return $op;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					status > 0   and  < 6
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function schedule_search($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
    $Today = date('Y-m-d');
    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT smpl_ord.* FROM smpl_ord LEFT JOIN smpl_wi  ON smpl_ord.num=smpl_wi.wi_num ";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("smpl_ord.etd DESC");
    $srh->row_per_page = 20;

    if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
        $srh->q_limit = "LIMIT ".$limit_entries." ";
    }else{
        #--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
        $pagesize=10;
        if ($argv['PHP_sr_startno']) {
            $pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
            $pages = $srh->get_page(1,$pagesize);
        } 
        ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end  
    }

	# ���������
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("smpl_ord.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("smpl_ord.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {			
                $srh->or_where_condition("smpl_ord.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    if ($mode==1){

        if ( $str = $argv['PHP_dept'] ) { 
			$srh->add_where_condition("smpl_ord.dept = '$str'", "PHP_dept",$str,"  department = [ $str ]. "); 
        }
    
        // ���M�[�J�� supplier search ...... status < 6  and  status > 0
        if ($str = 1 )  { 
            $str = 0;
            $srh->add_where_condition("smpl_ord.status > $str ", "",$str,"search for schedule feature ");
        }
        if ($str = 6 )  { 
            $srh->add_where_condition("smpl_ord.status < $str ", "",$str,"");
        }
        if ($str = strtoupper($argv['PHP_ref']) )  { 
            $srh->add_where_condition("smpl_ord.ref LIKE '%$str%'", "PHP_ref",$str," customer reference include:[ $str ] "); 
        }
        if ($str = $argv['PHP_cust'] )  { 
            $srh->add_where_condition("smpl_ord.cust = '$str'", "PHP_cust",$str," customer=[ $str ]. "); 
        }
        if ($str = $argv['PHP_num'] )  { 
            $srh->add_where_condition("smpl_ord.num LIKE '%$str%'", "PHP_num",$str," order number include:[ $str ] "); 
        }
        if ($str = $argv['PHP_factory'] )  { 
            $srh->add_where_condition("smpl_ord.factory = '$str'", "PHP_factory",$str," factory=[ $str ]. "); 
        }

        //�s�Wsearch����
        if ($str = $argv['SCH_wi'] )  { 
            $srh->add_where_condition("(smpl_wi.cfm_date <> '0000-00-00 00:00:00' AND smpl_wi.cfm_date IS NOT NULL ) AND (smpl_wi.ti_cfm <> '0000-00-00' AND smpl_wi.ti_cfm IS  NOT NULL)", "SCH_wi",$str," WI/WS Submitted "); 
        }  
        if ($str = $argv['SCH_close'] )  { 
            $srh->add_where_condition("smpl_ord.status <> 10", "SCH_close",$str," Exclude 'Case Closed' "); 
        }   
        if ($str = $argv['SCH_pend'] )  { 
            $srh->add_where_condition("smpl_ord.status <> 7", "SCH_pend",$str," Exclude 'Pending' "); 
        }			
        if ($str = $argv['SCH_pttnd'] )  { 
            $srh->add_where_condition("(smpl_ord.rcvd_pttn <>'0000-00-00' OR smpl_ord.pttn_suppt = '')  AND smpl_ord.done_pttn = '0000-00-00' AND smpl_ord.schd_pttn ='0000-00-00'", "SCH_pttnd",$str," Customer Pattern delivered or No Customer Pattern. "); 
        }			
        if ($str = $argv['SCH_fab'] )  { 
            $srh->add_where_condition("smpl_ord.rcvd_fab = '0000-00-00' AND smpl_ord.feta < '$Today' AND smpl_ord.fab_substu = ''", "SCH_fab",$str," Fabric Not Delivered, over ETA. "); 
        }	  
        if ($str = $argv['SCH_acc'] )  { 
            $srh->add_where_condition("smpl_ord.rcvd_acc = '0000-00-00' AND smpl_ord.aeta < '$Today' AND smpl_ord.acc_substu = ''", "SCH_acc",$str," Accessories Not Delivered, over ETA. "); 
        }	
        if ($str = $argv['SCH_ptno'] )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn = '0000-00-00' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptno",$str," Pattern Not Scheduled. "); 
        }	
        if ($str = $argv['SCH_ptdu'] == 1 )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn <> '0000-00-00' AND smpl_ord.schd_pttn < '$Today' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptdu",$str," Pattern Scheduled, but over due "); 
        }	
        
        if ($str = $argv['SCH_ptdu'] == 2 )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn <> '0000-00-00' AND smpl_ord.schd_pttn >= '$Today' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptdu",$str," Pattern Scheduled, not over due "); 
        }
        if ($str = $argv['SCH_usmp'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.schd_smpl = '0000-00-00' AND smpl_ord.rcvd_fab <> '0000-00-00' AND smpl_ord.rcvd_acc <> '0000-00-00' AND smpl_ord.status <> 10", "SCH_usmp",$str," Pattern Uploaded, Sample Not Scheduled. "); 
        }	
        if ($str = $argv['SCH_smpdu'] == 1 )  { 
            $srh->add_where_condition("smpl_ord.schd_smpl <> '0000-00-00' AND smpl_ord.schd_smpl < '$Today' AND smpl_ord.qty_done < smpl_ord.qty AND smpl_ord.done_smpl  = '0000-00-00'", "SCH_smpdu",$str," Sample Scheduled, but over due. "); 
        }	
        if ($str = $argv['SCH_smpdu'] == 2 )  { 
            $srh->add_where_condition("smpl_ord.schd_smpl <> '0000-00-00' AND smpl_ord.schd_smpl >= '$Today' AND smpl_ord.qty_done < smpl_ord.qty AND smpl_ord.done_smpl  = '0000-00-00'", "SCH_smpdu",$str," Sample Scheduled, not over due. "); 
        }	
        if ($str = $argv['SCH_qty'] )  { 
            $srh->add_where_condition("smpl_ord.qty_done <> 0 AND smpl_ord.qty_done < smpl_ord.qty", "SCH_qty",$str," Sample Not Completed. "); 
        }	
        if ($str = $argv['SCH_ie'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.ie = 0", "SCH_ie",$str," SPT Not submitted. "); 
        }	
        if ($str = $argv['SCH_mk'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.marker_date = '0000-00-00'", "SCH_mk",$str," Mini Marker Not Uploaded. "); 
        }	
        if ($str = $argv['PHP_op_str'])  { 
            
            $srh->add_where_condition("smpl_ord.open_date >= '$str'", "PHP_factory",$str," open date >=[ $str ]. "); 
        }
        if ($str = $argv['PHP_op_fsh'])  { 
            
            $srh->add_where_condition("smpl_ord.open_date <= '$str'", "PHP_factory",$str," open date <=[ $str ]. "); 
        }
        
        if ($str = $argv['SCH_smpl_type'])  { 			
            $srh->add_where_condition("smpl_ord.smpl_type = '$str'", "SCH_smpl_type",$str," smpl type =[ $str ]. "); 
        }

    }	
    $result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);
    if (!$result){   // ��d�M�L��Ʈ�
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

    #--*****--##2006.11.14�s���X�ݭn��oup_put	start		
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
    #--*****--##2006.11.14�s���X�ݭn��oup_put	end
    return $op;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_pttn_schedule($parm)		 �[�J �q��PATTERN  �Ƶ{  CHECK��J
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_pttn_schedule($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
	
		if ($parm['pttn_date'] == "--") { //��S��J�����---
			$this->msg->add("Error ! please submit pattern Estimate Finished date�C");
		}elseif($parm['pttn_date'] < $GLOBALS['TODAY']){
			$this->msg->add("<b>warning !</b> your submit ETF date is due !!");
		}
		if (($parm['pttn_date'] !='--') && (!checkdate(substr($parm['pttn_date'],5,2), substr($parm['pttn_date'],8,2), substr($parm['pttn_date'],0,4)))){
			$this->msg->add("Error ! <b>incorrect pattern ETF date</b>�C");
		}
		if (count($this->msg->get(2))){
			return false;
		}
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_pttn_schedule($parm)		 �[�J �q��PATTERN  �Ƶ{
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_pttn_schedule($parm) {

		$sql = $this->sql;

		$today = $GLOBALS['TODAY'];
		$updator =  $GLOBALS['SCACHE']['ADMIN']['login_id'];

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET schd_pttn='"	.$parm['pttn_date'].
								"', updator='"		.$updator.
								"', last_update='"	.$today.
								"', status='"		.$parm['status'].
								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_smpl_schedule($parm)		 �[�J �q�� SAMPLE  �Ƶ{ CHECK��J
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_smpl_schedule($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
	
		if ($parm['smpl_date'] == "--") { //��S��J�����---
			$this->msg->add("Error ! please submit Sample Estimate Finished date�C");
		} elseif($parm['smpl_date'] < $GLOBALS['TODAY']){
			$this->msg->add("<b>warning !</b> your submit ETF date is due !!");
		}
		if (($parm['smpl_date'] !='--') && (!checkdate(substr($parm['smpl_date'],5,2), substr($parm['smpl_date'],8,2), substr($parm['smpl_date'],0,4)))){
			$this->msg->add("Error ! <b>incorrect Sample ETF date</b>�C");
		}

		if (count($this->msg->get(2))){
			return false;
		}
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_smpl_schedule($parm)		 �[�J �q�� SAMPLE  �Ƶ{
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_smpl_schedule($parm) {

		$sql = $this->sql;

		$today = $GLOBALS['TODAY'];
		$updator =  $GLOBALS['SCACHE']['ADMIN']['login_id'];

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET schd_smpl='"	.$parm['smpl_date'].
								"', updator='"		.$updator.
								"', last_update='"	.$today.
								"', status='"		.$parm['status'].
								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->output_search($mode=0, $dept='',$limit_entries=0) �j�M �q����(���X�C��)
#					
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function output_search($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $_SESSION['sch_parm'];   //�N�Ҧ��� globals ����J$argv
    $Today = date('Y-m-d');
    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT max(smpl_ord.id) as id
                             FROM smpl_ord LEFT JOIN smpl_wi  ON smpl_ord.num=smpl_wi.wi_num ";

    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("smpl_ord.etd DESC, id DESC");
    $srh->row_per_page = 20;

    if($limit_entries) {    // �����w�̤j�q��~~~ 2005/11/28 �[�J
        $srh->q_limit = "LIMIT ".$limit_entries." ";
    } else {
        #--*****--##  2006.11.14 �H�Ʀr������ܭ��X star		
        $pagesize=10;
        if ($argv['PHP_sr_startno']) {
            $pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
           $pages = $srh->get_page(1,$pagesize);
        } 
        ##--*****--##  2006.11.14 �H�Ʀr������ܭ��X end    
    }
    
	# ���������
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("smpl_ord.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("smpl_ord.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {			
                $srh->or_where_condition("smpl_ord.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }

    if ($mode==1){

        if ( $str = $argv['PHP_dept'] ) { 
			$srh->add_where_condition("smpl_ord.dept = '$str'", "PHP_dept",$str,"  department = [ $str ]. "); 
        }
        
        // ���M�[�J�� supplier search ...... status > 3
        if ($str = 1 )  { 
            $str = 0;
            $srh->add_where_condition("smpl_ord.status > $str ", "",$str,"search for output feature ");
        }
        if ($str = strtoupper($argv['PHP_ref']) )  { 
            $srh->add_where_condition("smpl_ord.ref LIKE '%$str%'", "PHP_ref",$str," customer reference include:[ $str ] "); 
        }
        if ($str = $argv['PHP_cust'] )  { 
            $srh->add_where_condition("smpl_ord.cust = '$str'", "PHP_cust",$str,"   customer=[ $str ]. "); 
        }
        if ($str = $argv['PHP_num'] )  { 
            $srh->add_where_condition("smpl_ord.num LIKE '%$str%'", "PHP_num",$str," order number include:[ $str ] "); 
        }
        if ($str = $argv['PHP_factory'] )  { 
            $srh->add_where_condition("smpl_ord.factory = '$str'", "PHP_factory",$str," factory=[ $str ]. "); 
        }

        //�s�Wsearch����
        if ($str = $argv['SCH_wi'] )  { 
            $srh->add_where_condition("(smpl_wi.cfm_date != '0000-00-00 00:00:00' AND smpl_wi.cfm_date IS NOT NULL ) AND (smpl_wi.ti_cfm != '0000-00-00' AND smpl_wi.ti_cfm IS  NOT NULL)", "SCH_wi",$str," WI/WS Submitted "); 
        }  
        if ($str = $argv['SCH_close'] )  { 
            $srh->add_where_condition("smpl_ord.status <> 10", "SCH_close",$str," Exclude 'Case Closed' "); 
        }   
        if ($str = $argv['SCH_pend'] )  { 
            $srh->add_where_condition("smpl_ord.status <> 7", "SCH_pend",$str," Exclude 'Pending' "); 
        }			
        if ($str = $argv['SCH_pttnd'] )  { 
            $srh->add_where_condition("(smpl_ord.rcvd_pttn <>'0000-00-00' OR smpl_ord.pttn_suppt = '')  AND smpl_ord.done_pttn = '0000-00-00' AND smpl_ord.schd_pttn ='0000-00-00'", "SCH_pttnd",$str," Customer Pattern delivered or No Customer Pattern. "); 
        }			
        if ($str = $argv['SCH_fab'] )  { 
            $srh->add_where_condition("smpl_ord.rcvd_fab = '0000-00-00' AND smpl_ord.feta < '$Today' AND smpl_ord.fab_substu = ''", "SCH_fab",$str," Fabric Not Delivered, over ETA. "); 
        }	  
        if ($str = $argv['SCH_acc'] )  { 
            $srh->add_where_condition("smpl_ord.rcvd_acc = '0000-00-00' AND smpl_ord.aeta < '$Today' AND smpl_ord.acc_substu = ''", "SCH_acc",$str," Accessories Not Delivered, over ETA. "); 
        }	
        if ($str = $argv['SCH_ptno'] )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn = '0000-00-00' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptno",$str," Pattern Not Scheduled. "); 
        }	
        if ($str = $argv['SCH_ptdu'] == 1 )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn <> '0000-00-00' AND smpl_ord.schd_pttn < '$Today' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptdu",$str," Pattern Scheduled, but over due "); 
        }	
        
        if ($str = $argv['SCH_ptdu'] == 2 )  { 
            $srh->add_where_condition("smpl_ord.schd_pttn <> '0000-00-00' AND smpl_ord.schd_pttn >= '$Today' AND smpl_ord.done_pttn = '0000-00-00'", "SCH_ptdu",$str," Pattern Scheduled, not over due "); 
        }
        
        if ($str = $argv['SCH_usmp'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.schd_smpl = '0000-00-00' AND smpl_ord.rcvd_fab <> '0000-00-00' AND smpl_ord.rcvd_acc <> '0000-00-00' AND smpl_ord.status <> 10", "SCH_usmp",$str," Pattern Uploaded, Sample Not Scheduled. "); 
        }	
        if ($str = $argv['SCH_smpdu'] == 1 )  { 
            $srh->add_where_condition("smpl_ord.schd_smpl <> '0000-00-00' AND smpl_ord.schd_smpl < '$Today' AND smpl_ord.qty_done < smpl_ord.qty AND smpl_ord.done_smpl  = '0000-00-00'", "SCH_smpdu",$str," Sample Scheduled, but over due. "); 
        }	
        if ($str = $argv['SCH_smpdu'] == 2 )  { 
            $srh->add_where_condition("smpl_ord.schd_smpl <> '0000-00-00' AND smpl_ord.schd_smpl >= '$Today' AND smpl_ord.qty_done < smpl_ord.qty AND smpl_ord.done_smpl  = '0000-00-00'", "SCH_smpdu",$str," Sample Scheduled, not over due. "); 
        }	

        if ($str = $argv['SCH_qty'] )  { 
            $srh->add_where_condition("smpl_ord.qty_done <> 0 AND smpl_ord.qty_done < smpl_ord.qty", "SCH_qty",$str," Sample Not Completed. "); 
        }	
        if ($str = $argv['SCH_ie'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.ie = 0", "SCH_ie",$str," SPT Not submitted. "); 
        }	
        if ($str = $argv['SCH_mk'] )  { 
            $srh->add_where_condition("smpl_ord.done_pttn <> '0000-00-00' AND smpl_ord.marker_date = '0000-00-00'", "SCH_mk",$str," Mini Marker Not Uploaded. "); 
        }	

        if ($str = $argv['PHP_op_str'])  { 
            
            $srh->add_where_condition("smpl_ord.open_date >= '$str'", "PHP_factory",$str," open date >=[ $str ]. "); 
        }
        if ($str = $argv['PHP_op_fsh'])  { 
            
            $srh->add_where_condition("smpl_ord.open_date <= '$str'", "PHP_factory",$str," open date <=[ $str ]. "); 
        }

        if ($str = $argv['SCH_smpl_type'])  { 			
            $srh->add_where_condition("smpl_ord.smpl_type = '$str'", "SCH_smpl_type",$str," smpl type =[ $str ]. "); 
        }
       
    }	

    $srh->add_group_condition("substring(smpl_ord.num,1,9)");

    $result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);
    if (!$result){   // ��d�M�L��Ʈ�
        $op['record_NONE'] = 1;
    }
    
    // echo $srh->q_str;
    $op['sorder'] = $result;  // ��ƿ� �ߤJ $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['prev_no'] = $srh->prev_no;
    $op['next_no'] = $srh->next_no;
    $op['max_no'] = $srh->max_no;
    $op['last_no'] = $srh->last_no;
    $op['start_no'] = $srh->start_no;
    $op['per_page'] = $srh->row_per_page;
    
    #--*****--##2006.11.14�s���X�ݭn��oup_put	start		
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
    #--*****--##2006.11.14�s���X�ݭn��oup_put	end		

    return $op;
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fieldvalue($id, $field, $num='')	��X���w�O�����Y���� RETURN field.value
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fieldvalue($id, $field, $num='') {

		$sql = $this->sql;
	  
	  if($id){
		$q_str = "SELECT ".$field." FROM smpl_ord WHERE id='$id' ";
	  }elseif($num){
		$q_str = "SELECT ".$field." FROM smpl_ord WHERE num='$num' ";
	  }else{
			$this->msg->add("Error ! please initialized sample order ID or number !");
			$this->msg->merge($sql->msg);
			return false;    
	  }
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! cannot find the record!");
			return false;    
		}
		return $row;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, smpl_type=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $smpl_type=0, $smpl_num=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT smpl_ord.*, cust_init_name as cust_iname FROM smpl_ord, cust WHERE smpl_ord.id='$id' AND smpl_ord.cust=cust.cust_s_name AND smpl_ord.cust_ver=cust.ver";
		} elseif ($smpl_type) {
			$q_str = "SELECT smpl_ord.*, cust_init_name as cust_iname FROM smpl_ord, cust WHERE smpl_ord.smpl_type='$smpl_type' AND smpl_ord.cust=cust.cust_s_name AND smpl_ord.cust_ver=cust.ver";
		}elseif ($smpl_num){
			$q_str = "SELECT smpl_ord.*, cust_init_name as cust_iname FROM smpl_ord, cust WHERE smpl_ord.num like '%$smpl_num%' AND smpl_ord.cust=cust.cust_s_name AND smpl_ord.cust_ver=cust.ver order by smpl_ord.num DESC limit 1";
		} else {
			$this->msg->add("Error ! please specify sample order number ");		    
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

		$q_str="SELECT smpl_wi.id, smpl_wi.cfm_date, smpl_wi.bcfm_date, smpl_wi.ti_cfm FROM smpl_wi WHERE wi_num = '".$row['num']."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		if ($row_wi = $sql->fetch($q_result)) {
			$row['wi_date']=substr($row_wi['cfm_date'],0,10);	
			$row['bom_date']=substr($row_wi['bcfm_date'],0,10);
			$row['ws_date']=$row_wi['ti_cfm'];	
			$row['wi_id']=$row_wi['id'];	
		}

	$tmp_user=$GLOBALS['user']->get(0,$row['creator']);
	$row['creator_id'] = $row['creator'];
	if ($tmp_user['name'])$row['creator'] = $tmp_user['name'];
	
	$tmp_user=$GLOBALS['user']->get(0,$row['updator']);
	$row['updator_id'] = $row['updator'];
	if ($tmp_user['name'])$row['updator'] = $tmp_user['name'];
	
	$tmp_user=$GLOBALS['user']->get(0,$row['cfm_user']);
	$row['cfm_id'] = $row['cfm_user'];
	if ($tmp_user['name'])$row['cfm_user'] = $tmp_user['name'];
		
		
		return $row;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s ���
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;

					# �ˬd�O�_���o���ɮצs�b
		$q_str = "SELECT id FROM smpl_ord WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access to database, please contact system Administrator.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! cannot get this sample order record!");
			return false;    	    
		}
		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET	ref			='".$parm['ref'].
									"', factory		='".$parm['factory'].
									"', style		='".$parm['style_type'].
									"', qty			='".$parm['qty'].
									"', rq			='".$parm['rq'].
									"', backup		='".$parm['backup'].
									"', unit		='".$parm['unit'].
									"', smpl_type	='".$parm['smpl_type'].
									"', style_cat	='".$parm['style_cat'].
									"', fab_substu	='".$parm['fab_substu'].
									"', acc_substu	='".$parm['acc_substu'].
									"', pttn_suppt	='".$parm['pttn_suppt'].
									"', etd			='".$parm['etd'].
									"', feta		='".$parm['feta'].
									"', aeta		='".$parm['aeta'].
									"', peta		='".$parm['peta'].
									"', last_update	='".$parm['last_update'].
									"', updator		='".$parm['updator'].
									"', smpl_code	='".$parm['smpl_code'].
									"', des			='".$parm['des'].
									"'	WHERE id	=".$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  cannot update databse.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
//  �Ϫ��B�z ===============
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/smpl_pic/";  

		if($parm['pic_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x

						# �W�ǹϬ۳B�z
				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
			if(file_exists($style_dir.$parm['num'].".jpg")){
				unlink($style_dir.$parm['num'].".jpg") or die("can't delete old picture:".$pic_id.".jpg");  // �R������
			}
			if(file_exists($style_dir."s_".$parm['num'].".jpg")){
				unlink($style_dir."s_".$parm['num'].".jpg") or die("can't delete old picture:".$pic_id.".jpg");  // �R������
			}
				//�W�Ǥj�� 600X600
				$upFile->setSaveTo($style_dir,$parm['num'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);
				//�W�ǹ� 100X100
				$upFile->setSaveTo($style_dir,"s_".$parm['num'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 100, 100);

		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s  �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function cfm($id) {

	$sql = $this->sql;
	############### �ˬd��J����
	
	$today = $GLOBALS['TODAY'];
	$user =  $GLOBALS['SCACHE']['ADMIN']['login_id'];

	#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_ord SET `cfm_user` = '".$user."' , `cfm_date` = '".$today."' , `status` = '0' WHERE id = '".$id."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot update SMPL_ORD data table.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	return true;
} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s  �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_field($parm) {

	$sql = $this->sql;
	############### �ˬd��J����
	
	$today = $GLOBALS['TODAY'];
	$updator =  $GLOBALS['SCACHE']['ADMIN']['login_id'];

	#####   ��s��Ʈw���e
	$q_str = "UPDATE smpl_ord SET updator ='".$updator.
							  "', last_update='".$today.
							  "',".$parm['field_name']."='".($parm['field_value']).
							  "' WHERE id = '".$parm['id']."'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot update SMPL_ORD data table.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	return true;
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_status($id, $newstatus)	��s  ���p�����(status)
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_status($id, $newstatus) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET status ='".$newstatus."'  WHERE id='".$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update status of SMPL_ORD.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->regist_update($id)		��s  �̫��s�H�ήɶ����
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function regist_update($id) {

		$sql = $this->sql;
		############### �ˬd��J����

		$today = $GLOBALS['TODAY'];
		$updator =  $GLOBALS['SCACHE']['ADMIN']['login_id'];

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET updator ='"	.$updator.
								"', last_update='"	.$today.
								"'  WHERE id='"		.$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update updating data of SMPL_ORD.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_pttn($parm)		�W�� �O�l
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function upload_pttn($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET done_pttn ='"	.$parm['done_pttn'].
								"', updator='"		.$parm['updator'].
								"', last_update='"	.$parm['last_update'].
								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update upload pattern data of SMPL_ORD.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->upload_marker($parm)		�W�� marker
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function upload_marker($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET marker_date ='"	.$parm['marker_date'].
								"', updator='"		.$parm['updator'].
								"', last_update='"	.$parm['last_update'].
								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update upload Marker file of SMPL_ORD.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pttn_resend($parm)	��s �O�l���p �ϥi�H�A���e�X pttn
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pttn_resend($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET	done_pttn='"	.$parm['done_pttn'].
									"', last_update='"	.$parm['last_update'].
									"', updator='"		.$parm['updator'].
									"'	WHERE id="		.$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_resend($parm)	��s marker ���p �ϥi�H�A���e�X marker
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function marker_resend($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET	marker_date='"	.$parm['marker_date'].
									"', last_update='"	.$parm['last_update'].
									"', updator='"		.$parm['updator'].
									"'	WHERE id="		.$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_complete($parm)		��s  �w�����ƶq(�֥[)
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_complete($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
	
		if ($parm['qty'] <= 0 ) {
			$this->msg->add("Error ! incorrect '<b>Quantity</b>' �C");
			return false;
		}
		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET qty_done= qty_done + ".$parm['qty'].
									", last_update='"		.$parm['last_update'].
									"', done_smpl='"		.$parm['last_update'].
									"', updator='"			.$parm['updator'].
									"'  WHERE id='"			.$parm['id']." '";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD data table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� ���~�򥻸��[��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� smpl_type ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM smpl_ord WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� SMPL TYPE �� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl_ord ".$where_str;
// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			$match_limit = 500;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				// $match++;
				// if ($match==500) {
					// break;
				// }
			}
			if ($match != 500) {   // �O�d �|���@��
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		return $fields;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case_close($parm)		�˥� case close
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function case_close($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$rec = $this->get($parm['id']);
		$id = substr($rec['num'],-1,1);

		for($i=1; $i<=$id; $i++)
		{
		
			$num = substr($rec['num'],0,-1);
			$num = $num.$i;
			$q_str = "UPDATE smpl_ord SET	case_close='".$parm['case_close'].
									"', status='".$parm['status'].
									"', last_update='".$parm['last_update'].
									"', updator='".$parm['updator'].
									"'	WHERE num='".$num."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  cannot update databse.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		}

		
		$q_str = "UPDATE smpl_ord SET	case_close='".$parm['case_close'].
									"', status='".$parm['status'].
									"', last_update='".$parm['last_update'].
									"', updator='".$parm['updator'].
									"'	WHERE id=".$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  cannot update databse.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->is_sample_team($team)		�O���O �˥���team[user���էO]
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function is_sample_team($team) {

		$maker = $GLOBALS['SAMPLER'];		
		foreach ($maker as $value) {
			if($team == $value){
				return $team;
			}
		}
		return false;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_spt($parm)		 �[�J �u�� (��)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_spt($parm) {

			# �ˬd��J���O���O���~

		if (!is_numeric($parm['spt'] )) {
			$this->msg->add("Error ! only numberic for SPT aviable�C");
			return false;    
		}


		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET spt='"		.$parm['spt'].
								"', ie='"			.$parm['ie'].
								"', updator='"		.$parm['updator'].
								"', last_update='"	.$parm['last_update'].
								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_spt($parm)		 �[�J �u�� (��)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_apv($parm) {

			# �ˬd��J���O���O���~
		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE smpl_ord SET apv_date='"		.$parm['field_value'].								
								"'  WHERE num like '"		.$parm['num']."%'";	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case_close($parm)		�˥� case close
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_order_link($smpl_ord) {

		$sql = $this->sql;
		$ord_str = '';
		$q_str = "SELECT order_num FROM s_order WHERE smpl_ord ='".$smpl_ord."'";

		$q_result = $sql->query($q_str);
		while ($row = $sql->fetch($q_result)) {
				$ord_str.=$row['order_num'].'|';
		}
		if($ord_str)$ord_str = substr($ord_str, 0,-1);
		$q_str = "UPDATE smpl_ord SET	orders='".$ord_str.
									"'	WHERE num like '".$smpl_ord."%'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  cannot update databse.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	
		return true;
	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> tmp_use()
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function tmp_smpl_use() {

		$sql = $this->sql;
		$ord_str = '';
		$q_str = "SELECT smpl_ord FROM s_order WHERE smpl_ord LIKE 'S%' AND `opendate` >= '2007-01-01' GROUP BY smpl_ord";
//		echo $q_str;
		$q_result = $sql->query($q_str);
		while ($row = $sql->fetch($q_result)) {

				$this->add_order_link($row['smpl_ord']);
		}

	
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> copy_smpl_ord()	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_smpl_ord($id,$new_num) {
	
		$sql = $this->sql;
		$ord_str = '';
		$q_str = "SELECT * FROM smpl_ord WHERE id = $id";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		
		$q_str = "insert into smpl_ord(`num`,`dept`,`cust`,`cust_ver`,`ref`,`factory`,`style`,`smpl_type`,`style_cat`,`creator`,`open_date`,`smpl_ref`,`size`,`status`)
					values('".$new_num."','".
					$GLOBALS['SCACHE']['ADMIN']['dept']."','".
					$row['cust']."','".
					$row['cust_ver']."','".
					$row['ref']."','".
					$row['factory']."','".
					$row['style']."','".
					$row['smpl_type']."','".
					$row['style_cat']."','".
					$GLOBALS['SCACHE']['ADMIN']['login_id']."','".
					$GLOBALS['TODAY']."','".
					$row['num']."','".
					$row['size']."',-1".
					")";
		$q_result = $sql->query($q_str);
		$smpl_id = $sql->insert_id();
		
		return $smpl_id;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> copy_smpl_wi()	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_smpl_wi($id,$smpl_id,$new_num,$dt) {
		$sql = $this->sql;
		$ord_str = '';
		$q_str = "SELECT * FROM smpl_wi WHERE id = $id";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		
		$q_str = "insert into smpl_wi(`wi_num`,`dept`,`cust`,`cust_ver`,`smpl_id`,`style_code`,`creator`,`open_date`)
					values('".$new_num."','".
					$GLOBALS['SCACHE']['ADMIN']['dept']."','".
					$row['cust']."','".
					$row['cust_ver']."','".
					$smpl_id."','".
					$new_num."','".
					$GLOBALS['SCACHE']['ADMIN']['login_id']."','".
					$dt['date_str']."'".
					")";
		
		$q_result = $sql->query($q_str);
		$wi_id = $sql->insert_id();
		
		return $wi_id;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> copy_smpl_ti()	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_smpl_ti($old_wi_id,$new_wi_id) {
	
		$sql = $this->sql;
		$ord_str = '';
		$q_str = "SELECT * FROM smpl_ti WHERE wi_id = $old_wi_id";
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)){
			$q_str = "insert into smpl_ti(`wi_id`,`item`,`sub_item`,`detail`,`date`,`ti_new`,`ti_time`)
					values('".$new_wi_id."','".
					$row['item']."','".
					$row['sub_item']."','".
					$row['detail']."','".
					$GLOBALS['TODAY']."','0','00:00:00'".
					")";
			
			$q_res = $sql->query($q_str);
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> copy_smpl_lots_use()	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_smpl_lots_use($old_num,$new_num) {
	
		$sql = $this->sql;
		$ord_str = '';
		$q_str = "SELECT * FROM smpl_lots_use WHERE smpl_code = '$old_num'";
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)){
			$q_str = "insert into smpl_lots_use(`smpl_code`,`use_method`,`lots_code`,`lots_name`,`unit`,`color`,`est_1`,`ester_1`,`est_2`,`ester_2`,`act_usage`,`use_for`,`memo`,`user`,`add_date`)
					values('".$new_num."','".
					$row['use_method']."','".
					$row['lots_code']."','".
					$row['lots_name']."','".
					$row['unit']."','".
					$row['color']."','".
					$row['est_1']."','".
					($row['ester_1']==''?'':$GLOBALS['SCACHE']['ADMIN']['login_id'])."','".
					$row['est_2']."','".
					($row['ester_2']==''?'':$GLOBALS['SCACHE']['ADMIN']['login_id'])."','".
					$row['act_usage']."','".
					$row['use_for']."','".
					$row['memo']."','".
					$GLOBALS['SCACHE']['ADMIN']['name']."','".
					$GLOBALS['TODAY']."'".
					")";
			
			$q_res = $sql->query($q_str);
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> copy_smpl_acc_use()	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_smpl_acc_use($old_num,$new_num) {
	
		$sql = $this->sql;
		$ord_str = '';
		$q_str = "SELECT * FROM smpl_acc_use WHERE smpl_code = '$old_num'";
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)){
			$q_str = "insert into smpl_acc_use(`smpl_code`,`use_method`,`acc_code`,`acc_name`,`unit`,`color`,`est_1`,`ester_1`,`est_2`,`ester_2`,`act_usage`,`use_for`,`memo`,`user`,`add_date`,`acc_cat`)
					values('".$new_num."','".
					$row['use_method']."','".
					$row['acc_code']."','".
					$row['acc_name']."','".
					$row['unit']."','".
					$row['color']."','".
					$row['est_1']."','".
					($row['ester_1']==''?'':$GLOBALS['SCACHE']['ADMIN']['login_id'])."','".
					$row['est_2']."','".
					($row['ester_2']==''?'':$GLOBALS['SCACHE']['ADMIN']['login_id'])."','".
					$row['act_usage']."','".
					$row['use_for']."','".
					$row['memo']."','".
					$GLOBALS['SCACHE']['ADMIN']['name']."','".
					$GLOBALS['TODAY']."','".
					$row['acc_cat']."'".
					")";
			
			$q_res = $sql->query($q_str);
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->uupdate_field2($field,$value,$where_str='',$table)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_field2($field,$value,$where_str,$table) {

	$sql = $this->sql;
	
	$q_str = "UPDATE $table SET $field ='".$value.
			  "' WHERE ".$where_str;
	echo $q_str."<BR>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot update SMPL_ORD data table.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	return true;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields_value($n_field,$where_str="")	���X���� SMPL TYPE �� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_value($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl_ord where ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			$fields[] = $row;
		}
		
		return $fields;
	} // end func
	
	
} // end class


?>