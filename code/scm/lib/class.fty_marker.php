<?php

class FTY_MARKER {

	var $sql;
	var $msg ;

	#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
	#		必需聯上 sql 才可  啟始
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


	function fab_item($fab_type) {
		#$combo = (!empty($combo))? $combo : '';
		switch ($fab_type) {

		case 1:
		$str = 'S';
		return $str;
		break;

		case 2:
		$str = 'L';
		return $str;
		break;

		case 3:
		$str = 'F';
		return $str;
		break;

		case 4:
		$str = 'N';
		return $str;
		break;

		case 5:
		$str = 'P';
		return $str;
		break;

	} // end func
}








#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_combo($smpl_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_combo($tb=1,$id,$fab_type) {
		$sql = $this->sql;
		$q_str = "SELECT `combo` FROM `marker_fty` WHERE ord_id = '".$id."' AND fab_type = '".$fab_type."' order by combo DESC limit 1";

		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		if (!$row = $sql->fetch($q_result)){return 0;}
		return $row['combo'];
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_num($tb='marker_fty',$field, $val, $id,$order_num='') {

		$sql = $this->sql;
		$q_str = "UPDATE ".$tb." SET ".$field."='".$val."'  WHERE id ='".$id."'";
		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return 0;
		}
		# 記錄使用者動態
		$message = "EDIT order marker: [".$order_num."] (".$field.")";
		$GLOBALS['log']->log_add(0,"063E",$message);
		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_ord($field, $val, $ord_id ,$fab_type=0 ,$combo=0,$order_num='') {

		$sql = $this->sql;

		$q_str = "UPDATE `marker_fty` SET ".$field."='".$val."' WHERE ord_id ='".$ord_id."' and fab_type ='".$fab_type."' and combo ='".$combo."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return 0;
		}else{
			# 記錄使用者動態
			$message = "EDIT order marker: [".$order_num."] (".$field.")";
			$GLOBALS['log']->log_add(0,"063E",$message);
		}

		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $dept='',$limit_entries=0) {

		$argv = $_SESSION['sch_parm'];
		$sql = $this->sql;

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

			$q_header = "SELECT s_order.* , wi.id as wi_id, wi.status as wi_status, 
											    cust_init_name as cust_iname, wi.ti_cfm
									 FROM s_order, cust Left Join wi On s_order.order_num = wi.wi_num";


		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("smpl_id DESC");
		$srh->row_per_page = 7;

		if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
				$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			##--*****--2006.11.16頁碼新增 start		##
			$pagesize=10;
			if ($argv['PHP_sr_startno']) {
				$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);
			}else{
				$pages = $srh->get_page(1,$pagesize);
			}
	 		##--*****--2006.11.16頁碼新增 end	   ##
		}

		$mesg = '';
		
		 if ($mode == 2){
			if (!isset($argv['PHP_order_num']))$argv['PHP_order_num']='';
			if ($str = strtoupper($argv['PHP_order_num']) ) {
				$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str);
				$mesg = "  Sample number : [ $str ]. ";
			}
			if ($str = $argv['PHP_style'] ) {
				$srh->add_where_condition("s_order.style = '$str'", "PHP_style",$str);
				$mesg .= "  Style = [ $str ]. ";
			}
			$srh->add_where_condition("s_order.cust = cust.cust_s_name AND cust.ver = s_order.cust_ver");   // 關聯式察尋 必然要加
	   	if($argv['SCH_finish'] == 'false')$srh->add_where_condition("s_order.status >= 0 && s_order.status < 10");
	   	if($argv['SCH_finish'] == 'true')$srh->add_where_condition("s_order.status >= 0");
	   	$srh->add_where_condition("wi.status > 1");

		}

		$result = $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;
		}


		$op['marker']	 			= $result;  // 資料錄 拋入 $op

		$this->msg->merge($srh->msg);
		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}
		$op['cgistr_get'] 	= $srh->get_cgi_str(0);
		$op['cgistr_post']	= $srh->get_cgi_str(1);
		$op['prev_no']			= $srh->prev_no;
		$op['next_no'] 			= $srh->next_no;
		$op['max_no'] 			= $srh->max_no;
		$op['last_no'] 			= $srh->last_no;
		$op['start_no'] 		= $srh->start_no;
		$op['per_page'] 		= $srh->row_per_page;
// echo $srh->q_str;
		if(!$limit_entries){
			##--*****--2006.11.16頁碼新增 start
			$op['maxpage']	=	$srh->get_max_page();
			$op['pages']		= $pages;
			$op['now_pp']		= $srh->now_pp;
			$op['lastpage']	=	$pages[$pagesize-1];
			##--*****--2006.11.16頁碼新增 end
		}

		return $op;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cut_report($mode=0,$limit_entries=0) {

		$argv = $_SESSION['sch_parm'];
		$sql = $this->sql;

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

			$q_header = "SELECT s_order.* , wi.id as wi_id, wi.status as wi_status, 
											    cust_init_name as cust_iname, wi.ti_cfm
									 FROM s_order, cust, wi";


		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.etd DESC");
		$srh->row_per_page = 18;

		if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
				$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			##--*****--2006.11.16頁碼新增 start		##
			$pagesize=10;
			if ($argv['PHP_sr_startno']) {
				$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);
			}else{
				$pages = $srh->get_page(1,$pagesize);
			}
	 		##--*****--2006.11.16頁碼新增 end	   ##
		}

		$mesg = '';
		
		//2006/05/12 adding 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		$sales_dept = $GLOBALS['SALES_DEPT'];
		if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		// for ($i=0; $i< sizeof($sales_dept); $i++)
		// {			
				// if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		// }				
	
	
	 $srh->add_where_condition("s_order.order_num = wi.wi_num");
	 $srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");	
	 if ($mode){
		$srh->add_where_condition("s_order.status > 4");
//		$srh->add_where_condition("s_order.status < 10");
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
		if ($str = $argv['PHP_etdstr'] )  { 
			$srh->add_where_condition("s_order.etd >= '$str'", "PHP_etdstr",$str); 
			$mesg.= "  ETD :  $str ~ ";
			}
		if ($str = $argv['PHP_etdfsh'] )  { 
			$srh->add_where_condition("s_order.etd <= '$str'", "PHP_etdfsh",$str); 
			if($argv['PHP_etdstr']){$etd_msg='';}else{$etd_msg=' ETD : ';}
			$mesg.= $etd_msg." $str ";
			}	
		if ($str = $argv['PHP_sch_fty'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "",$user_dept);
			$mesg.= " FTY = [ $str ]";
			}
		if ($mesg){
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
	}

		$result = $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;
		}


		$op['marker']	 			= $result;  // 資料錄 拋入 $op

		$this->msg->merge($srh->msg);
		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}
		$op['cgistr_get'] 	= $srh->get_cgi_str(0);
		$op['cgistr_post']	= $srh->get_cgi_str(1);
		$op['prev_no']			= $srh->prev_no;
		$op['next_no'] 			= $srh->next_no;
		$op['max_no'] 			= $srh->max_no;
		$op['last_no'] 			= $srh->last_no;
		$op['start_no'] 		= $srh->start_no;
		$op['per_page'] 		= $srh->row_per_page;

		if(!$limit_entries){
			##--*****--2006.11.16頁碼新增 start
			$op['maxpage']	=	$srh->get_max_page();
			$op['pages']		= $pages;
			$op['now_pp']		= $srh->now_pp;
			$op['lastpage']	=	$pages[$pagesize-1];
			##--*****--2006.11.16頁碼新增 end
		}

		return $op;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($tb=1,$id=0,$tb_id=0,$all=null,$fab_type=null,$combo=null,$group=null) {

		$sql = $this->sql;

			$add = (isset($fab_type))? " and `fab_type` = '".$fab_type."' and `combo` = '".$combo."' " : "";
			$group = (isset($group))? " GROUP BY fab_type,combo " : "";
			if ($id)	{
				$q_str = "SELECT * FROM `marker_fty` WHERE id='$id' $add";
			} elseif ($tb_id) {
				$q_str = "SELECT * FROM `marker_fty` WHERE ord_id='$tb_id' $add $group ";
			} else {
				$this->msg->add("Error ! please specify order number.");
				return false;
			}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}

		if(!empty($all)){
			$row =array();
			while ($rows = $sql->fetch($q_result)) {
				$row[] = $rows;
			}
			for($i=0; $i<sizeof($row); $i++)
			{
				$q_str = "SELECT lots_use.lots_code as mat_code FROM `lots_use` WHERE  id = '".$row[$i]['lots_code']."'";	
				$q_result = $sql->query($q_str);
				$tmp_lots = $sql->fetch($q_result);
				$row[$i]['mat_code'] = $tmp_lots['mat_code'];			
			}
			
		}else{
			if (!$row = $sql->fetch($q_result)) {
				$this->msg->add("Error ! Can't find this record!");
				return false;
			}

			$q_str = "SELECT lots_use.lots_code as mat_code FROM `lots_use` WHERE  id = '".$row['lots_code']."'";
			$q_result = $sql->query($q_str);
			$tmp_lots = $sql->fetch($q_result);
			$row['mat_code'] = $tmp_lots['mat_code'];	
					
			$po_user=$GLOBALS['user']->get(0,$row['updator']);
			if ($po_user['name'])$row['updator'] = $po_user['name'];
			$row['last_update'] = substr($row['last_update'],0,10);
		}
		if(!empty($row))return $row;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->select_fab
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function select_fab($tb=1,$id,$fab='') {
		$sql = $this->sql;
			$q_str = "SELECT DISTINCT `fab_type` FROM `marker_fty` WHERE ord_id = '".$id."' and fab_type = '".$fab."' ";

		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		while ($row = $sql->fetch($q_result)) {
			$fab_type[] = $row['fab_type'];
		}

		$now_arr = array_keys($GLOBALS['fab_type']);
		if(!empty($fab_type))$count1 = count($fab_type);
		$count2 = count($now_arr)+1;
		for($i=1;$i < $count2;$i++){
			$yes = 0;
			if(!empty($count1)){
				for($y=0;$y < $count1;$y++){
					if( $fab_type[$y] == $i ){
						$yes = 1;
					}
				}
			}
			if(!empty($i)){
				if($yes <> 1 ){
					$fab_t[$i] = $GLOBALS['fab_type'][$i];
				}
			}
		}
		$fab_t[2] = $GLOBALS['fab_type'][2] = 'combo';
		if(!empty($fab)) $fab_t[$fab] = $GLOBALS['fab_type'][$fab];

		ksort($fab_t);

		$str = $GLOBALS['arry2']->select_id($fab_t,$fab,'PHP_fab','select_fab','','');

		return $str;
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->select_mk
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function select_mk($ord_id,$fab_type,$combo,$mk='') {
		$sql = $this->sql;
		$q_str = "SELECT `mk_num` FROM `marker_fty` WHERE `ord_id` = '".$ord_id."' and fab_type = '".$fab_type."'  and combo = '".$combo."' ";

		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		while ($row = $sql->fetch($q_result)) {
			$mk_arr[] = $row['mk_num'];
		}

		$now_arr = array_keys($GLOBALS['ALPHA2']);
		if(!empty($mk_arr))$count1 = count($mk_arr);
		$count2 = count($now_arr);
		for($i=0;$i < $count2;$i++){
			$yes = 0;
			for($y=0;$y < $count1;$y++){
				if( isset($mk_arr[$y]) and $mk_arr[$y] == "$i" ){
					$yes = 1;
				}
			}

			if(isset($i)){
				if($yes <> 1 ){
					$fab_t[$i] = $GLOBALS['ALPHA2'][$i];
				}
			}
		}

		if( !empty($mk) || $mk === '0') $fab_t[$mk] = $GLOBALS['ALPHA2'][$mk];

		ksort($fab_t);
		$str = $GLOBALS['arry2']->select_id($fab_t,$mk,'PHP_mk','select_mk','','','','',1);

		return $str;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->select_unit
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function select_unit($tb=1,$val='') {
		if($tb==1)
		$str = $GLOBALS['arry2']->select_id($GLOBALS['unit_type'],$val,'PHP_unit','select_unit','','chg_unit();');
		else
		$str = $GLOBALS['arry2']->select_id($GLOBALS['unit_type2'],$val,'PHP_unit','select_unit','','chg_unit();');

		return $str;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($tb=1,$id,$ord_id,$fab_type='',$combo='',$status='') {

		$sql = $this->sql;
		$nc='';
			if($status)
				$q_str = "DELETE FROM marker_fty WHERE id = '".$id."'";
			else
				$q_str = "DELETE FROM marker_fty WHERE ord_id = '".$id."' and fab_type = '".$fab_type."' and combo = '".$combo."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! 無法存取資料庫 !");$this->msg->merge($sql->msg);return 0;};

			$q_str = "select `id`,`combo` from `marker_fty` WHERE ord_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo = '".$combo."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}

			if (!$row = $sql->fetch($q_result)){
				# 如果之後還有 combo 自動減 1
				$q_str = "select `id`,`combo` from `marker_fty` where ord_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo > '".$combo."' ";
				if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				while ($row = $sql->fetch($q_result)) {
					$q_str2 = "UPDATE `marker_fty` SET `combo` = '".( $row['combo'] - 1 )."' where `id` = '".$row['id']."' ;";
					if (!$q_result2 = $sql->query($q_str2)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				}
				return 2;
			}else{
				return 1;
			}
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_marker(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_marker($tb=1,$id,$marker) {

		$folder = ($tb==1)? '/smpl_marker/' : '/fty_marker/';

		if(file_exists($GLOBALS['config']['root_dir'].$folder.$marker)){
			unlink($GLOBALS['config']['root_dir'].$folder.$marker) or die("can't delete old marker:".$marker);  // 刪除舊檔
		}
			
			# 更改 order 的上傳日期 ------------
			if (!$A = $GLOBALS['order']->update_field('fty_marker_date','0000-00-00',$id)){
				$op['msg'] = $GLOBALS['order']->msg->get(2);
				$GLOBALS['layout']->assign($op);
				$GLOBALS['layout']->display($TPL_ERROR);
				break;
			}
			return 1;
			
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_main(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_main($order_num,$T_wiqty,$size_A,$mk='',$status='') {
		$this->T_wiqty = $T_wiqty;

		# 不同的刪減 *************
		#if($mk['fab_type'] <> 3){
		if($mk['fab_type']){

			# get fab. type asmt.
			$T_qty = $this->re_color($T_wiqty);

			if($T_qty){
				# is true chge.
				foreach($T_qty as $key => $val){
					if($val['assortment'])
						$this->T_wiqty = $this->re_color($this->T_wiqty,$val['assortment'],1,1);
				}
				$T_wiqty = (empty($status))? $this->T_wiqty : $this->re_color($this->T_wiqty,$mk['assortment'],1,2);
			}
			return $this->report_color($order_num,$T_wiqty,$size_A,$this->T_wiqty,$mk,$status);
		}

	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->re_color(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function re_color($T_wiqty,$assortment='',$status='',$p=1) {
		global $op;

		if($assortment){

			$asmt = explode('|',$assortment);
			$a_cr = explode(',',$asmt[0]);
			$a_lv = explode(',',$asmt[1]);
			$a_cw = explode(',',$asmt[2]);

			foreach($T_wiqty as $key => $val){
				$qty = $val['qty'];

				foreach($a_cr as $ckey => $cval){

					if($key == ($cval - 1) ){ # data in one
						$qty = explode(',',$val['qty']);
						$nw = $nw_qty = '';

						# Colorway
						foreach($a_cw as $wkey => $wval){
							if($wval){
								if($p==1){
								 $nw_qty[$wkey] = $qty[$wkey] - ( $wval * $a_lv[$ckey] );
								}else{
									$nw_qty[$wkey] = $qty[$wkey] + ( $wval * $a_lv[$ckey] );
								}
							}else{
								 $nw_qty[$wkey] = $qty[$wkey];
							}
						}

						$qty = '';
						foreach($nw_qty as $nkey => $nval)
							$qty .= $nval.',';
						$qty = substr($qty,0,-1);
					}
				}
				$T_wiqty2[$key]['qty'] = $qty;
				$T_wiqty2[$key]['colorway'] = $val['colorway'];
			}
			return $T_wiqty2;
		}else{
			# 判斷是否有 asmt
			$T_wiqty = '';
			foreach($op['mks'] as $key => $val)
			if($val['assortment'])$T_wiqty[] = array( 'id' => $val['id'] , 'assortment' => $val['assortment']);
			return $T_wiqty;
		}
	}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->re_no_color(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function re_no_color($T_wiqty,$assortment='',$status='',$p=1) {
		global $op;

		if($assortment){

			$asmt = explode('|',$assortment);
			$a_cr = explode(',',$asmt[0]);
			$a_lv = explode(',',$asmt[1]);
			$a_cw = explode(',',$asmt[2]);

			foreach($T_wiqty as $key => $val){
				$qty = $val['qty'];

				foreach($a_cr as $ckey => $cval){

					if($key == ($cval - 1) ){ # data in one
						$qty = explode(',',$val['qty']);
						$nw = $nw_qty = '';

						# Colorway
						foreach($a_cw as $wkey => $wval){
							if($wval){
								if($p==1){
								 $nw_qty[$wkey] = $qty[$wkey] - ( $wval * $a_lv[$ckey] );
								}else{
									$nw_qty[$wkey] = $qty[$wkey] + ( $wval * $a_lv[$ckey] );
								}
							}else{
								 $nw_qty[$wkey] = $qty[$wkey];
							}
						}

						$qty = '';
						foreach($nw_qty as $nkey => $nval)
							$qty .= $nval.',';
						$qty = substr($qty,0,-1);
					}
				}
				$T_wiqty2[$key]['qty'] = $qty;
				$T_wiqty2[$key]['colorway'] = $val['colorway'];
			}
			return $T_wiqty2;
		}else{
			# 判斷是否有 asmt
			$T_wiqty = '';
			foreach($op['mks'] as $key => $val)
			if($val['assortment'])$T_wiqty[] = array( 'id' => $val['id'] , 'assortment' => $val['assortment']);
			return $T_wiqty;
		}
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_color(     layout
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_title($order_num,$T_wiqty,$size_A,$T_wiqty2='',$mk='',$status='') {

		$col = count(explode(',',$size_A['size']));
		$row = count($T_wiqty);

		if( !empty($status) ){
			if( !empty($mk['assortment']) ){
				$asmt = explode('|',$mk['assortment']);
				$a_cr = explode(',',$asmt[0]);
				$a_lv = explode(',',$asmt[1]);
				$a_cw = explode(',',$asmt[2]);
			}
		}else{
			$mk['length'] = '' ;
		}

		$html = '
			<tr>
        <td colspan="'.($col+1).'" align="center" bgcolor="#FFFFFF">配碼 ( Assortment ) </td>

      </tr>';

		$html .= '
      <tr>
        <td width="160" align="center" bgcolor="#FFFFFF">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
					  <tr>
					    <td width="30%" align="left">配色 Color</td>
					    <td width="10%" align="center">\</td>
					    <td width="30%" align="right">Size</td>
					  </tr>
					</table>
        </td>';
    $size_A = explode(',',$size_A['size']);
		foreach($size_A as $key){
		$html .= '
        <td align="center" bgcolor="#FFFFFF">'.$key.'</td>';
		}
		$html .= '
      </tr>';

		$m1=1;

    foreach($T_wiqty as $key => $val){
    $html .= '
      <tr>
        <td align="left" bgcolor="#FFFFFF">&nbsp;'.$val['colorway'].'</td>';

	      $qty = explode(',',$val['qty']);
        $m2=1;
        foreach($qty as $qkey => $qval){
        	if(empty($qval))$qval=0;
        	$t_qty = $qval;

        	# + asmt
        	if( !empty($T_wiqty2[$key]) ){
            $qty2 = explode(',',$T_wiqty2[$key]['qty']);
            $t_qty = $qty2[$qkey];
          }
		$html .= '
        <td align="center" bgcolor="#FFFFCC">
        '.$t_qty.'</td>';
        $m2++;
      	}
    $html .= '
      </tr>';
    $m1++;
    }
    $str['title'] = $html;
    return $str;
  }



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_color(     layout
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_color($order_num,$T_wiqty,$size_A,$T_wiqty2='',$mk='',$status='') {

		$col = count(explode(',',$size_A['size']));
		$row = count($T_wiqty);

		if( !empty($status) ){
			if( !empty($mk['assortment']) ){
				$asmt = explode('|',$mk['assortment']);
				$a_cr = explode(',',$asmt[0]);
				$a_lv = explode(',',$asmt[1]);
				$a_cw = explode(',',$asmt[2]);
			}
		}else{
			$mk['length'] = '' ;
		}

		$html = '
			<tr>
				<td rowspan="'.($row+2).'" align="center" bgcolor="#FFFFFF">馬克<br />MK #</td>
        <td colspan="'.($col+1).'" align="center" bgcolor="#FFFFFF">配碼 ( Assortment ) </td>
        <td rowspan="'.($row+2).'" align="center" bgcolor="#FFFFFF">層數<br />Level</td>
        <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">碼長 ('.$GLOBALS['unit_type2'][$mk['unit_type']].')<br />Length</td>
        <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">餘碼 ('.$GLOBALS['unit_type2'][$mk['unit_type']].')<br />Length</td>
       <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">布號</td>
        <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">&nbsp;</td>
      </tr>';

		$html .= '
      <tr>
        <td align="center" bgcolor="#FFFFFF">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
					  <tr>
					    <td width="40%" align="left" nowrap>配色 Color</td>
					    <td width="20%" align="center">\</td>
					    <td width="40%" align="right">Size</td>
					  </tr>
					</table>
        </td>';
    $size_A = explode(',',$size_A['size']);
		foreach($size_A as $key){
		$html .= '
        <td width="30" align="center" bgcolor="#FFFFFF">'.$key.'</td>';
		}
		$html .= '
      </tr>';

		$m1=1;

    foreach($T_wiqty as $key => $val){
    $html .= '
      <tr>
        <td align="left" bgcolor="#FFFFFF">&nbsp;'.$val['colorway'].'</td>';

	      $qty = explode(',',$val['qty']);
        $m2=1;
        foreach($qty as $qkey => $qval){
        	if(empty($qval))$qval=0;
        	$t_qty = $qval;

        	# + asmt
        	if( !empty($T_wiqty2[$key]) ){
            $qty2 = explode(',',$T_wiqty2[$key]['qty']);
            $t_qty = $qty2[$qkey];
          }
		$html .= '
        <td align="center" bgcolor="#FFFFCC"><input type="text" name="m'.$m1.'_'.$m2.'" id="m'.$m1.'_'.$m2.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" value="'.$t_qty.'" readonly />
        <input type="hidden" name="mh'.$m1.'_'.$m2.'" id="m'.$m1.'_'.$m2.'" value="'.$qval.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" /></td>';
        $m2++;
      	}
    $html .= '
      </tr>';
    $m1++;
    }
    $str['title'] = $html;

    if( !empty($status) )
			$m_num = $this->select_mk($mk['ord_id'],$mk['fab_type'],$mk['combo'],$mk['mk_num']);
		else
			$m_num = $this->select_mk($GLOBALS['op']['mk']['ord_id'],$GLOBALS['op']['mk']['fab_type'],$GLOBALS['op']['mk']['combo']);



		$m1 = 1;
		foreach($T_wiqty as $key => $val){
		if($m1 == 1){
    $st = '
			<tr bgcolor="#CCCCCC">
				<td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">'.$m_num.'</td>
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$val['colorway'].'</td>';
			# + asmt
			$mc = 0;
			$lv = '';
			if( !empty($a_cr) ){
				foreach($a_cr as $ckey => $cval){
					if($key == ($cval - 1) ){
						$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" checked="checked" /></td>';
						$mc = 1;
						$lv = $a_lv[$ckey];
					}
				}
			}
			if($mc <> 1)
			$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" /></td>';

			$st .= '
			      </tr>
			    </table>
			  </td>';
		$m2 = 1;

		# + asmt
		foreach($size_A as $keys => $vals){
		$vals = (!empty($a_cw))? $a_cw[$keys]:'';
		$st .= '
				<td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input type="text" name="cw['.$m2.']" id="cw['.$m2.']" style="width:20px;text-align:right;" value="'.$vals.'" onKeyUp="chk(\''.$row.'\',\''.$col.'\')" /></td>';
		$m2++;
		}

		$st .= '
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="'.$lv.'" onKeyUp="chk(\''.$row.'\',\''.$col.'\')"/></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input name="PHP_length" id="length" type="text" style="width:36px;text-align:right;" value="'.$mk['length'].'" onClick="chk(\''.$row.'\',\''.$col.'\')"/></td>
			   <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input name="PHP_remain" id="remain" type="text" style="width:36px;text-align:right;" value="'.$mk['remain'].'" /></td>
			   <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input name="PHP_fab_code" id="remain" type="text" style="width:36px;text-align:right;" value="'.$mk['fab_code'].'" /></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">';
		if(!empty($status) && $status == 'edit'){
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Update" onclick="atc(\'edit\')" style="width:52px;cursor:pointer;" /><br>';
		}else{
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Append" onclick="atc(\'append\')" style="width:52px;cursor:pointer;" />';
		}
			$st .= '
			  </td>
			</tr>';
		}
		if($m1 <> 1){
		$st .= '
			<tr bgcolor="#CCCCCC">
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$val['colorway'].'</td>';
			# + asmt
			if( !empty($a_cr) ){
				$mc = 0;
				$lv = '';
				foreach($a_cr as $ckey => $cval){
					if($key == ($cval - 1) ){
						$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" checked="checked" /></td>';
						$mc = 1;
						$lv = $a_lv[$ckey];
					}
				}
			}
			if($mc <> 1)
			$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" /></td>';

			$st .= '
			      </tr>
			    </table>
				</td>
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="'.$lv.'" onKeyUp="chk(\''.$row.'\',\''.$col.'\')"/></td>
			</tr>
		';
		}
		$m1++;
		}

    $str['main'] = $st;

		return $str;
	}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_no_color(     layout
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_no_color($order_num,$T_wiqty,$size_A,$T_wiqty2='',$mk='',$status='') {

		$col = count(explode(',',$size_A['size']));
		$row = count($T_wiqty);
		$html = '
			<tr>
        <td width="60" rowspan="3" align="center" bgcolor="#FFFFFF">馬克<br />MK #</td>
        <td colspan="'.($col+1).'" align="center" bgcolor="#FFFFFF">配馬 ( Assortment ) </td>
        <td rowspan="3" align="center" bgcolor="#FFFFFF">層數<br />Level</td>
        <td rowspan="3" align="center" bgcolor="#FFFFFF" nowrap>馬克長度<br />Length</td>
        <td rowspan="3" bgcolor="#FFFFFF">&nbsp;</td>
      </tr>';


		$html .= '
      <tr>
        <td align="center" bgcolor="#FFFFFF">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
					  <tr>
					    <td width="40%" align="left">配色 Color</td>
					    <td width="20%" align="center">\</td>
					    <td width="40%" align="right">Size</td>
					  </tr>
					</table>
        </td>';
    $size_A = explode(',',$size_A['size']);
		foreach($size_A as $key){
		$html .= '
        <td width="30" align="center" bgcolor="#FFFFFF">'.$key.'</td>';
		}
		$html .= '
      </tr>';


    foreach($T_wiqty as $key){
      $qty = explode(',',$key['qty']);
      foreach($qty as $qkey => $qval){
      	if(empty($qq[$qkey]))$qq[$qkey]=0;
      	$qq[$qkey] = $qq[$qkey] + $qval;
    	}
    }

    $html .= '
      <tr>
        <td align="center" bgcolor="#FFFFFF">Total</td>';
		$m1=1;
		foreach($qq as $key => $val){
		$html .= '
        <td align="center" bgcolor="#FFFFCC">
        <input type="text" name="m'.$m1.'" id="m'.$m1.'" value="'.$val.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" readonly />
        <input type="hidden" name="mh'.$m1.'" id="m'.$m1.'" value="'.$qq[$key].'" />
        </td>';
    $m1++;
    }

    $html .= '
      </tr>';

    $str['title'] = ($html);
    #htmlentities

    if( !empty($status) )
			$m_num = $this->select_mk($mk['ord_id'],$mk['mk_num']);
		else
			$m_num = $this->select_mk($GLOBALS['op']['mk']['ord_id']);

		$m1 = 1;
		foreach($T_wiqty as $key){
		if($m1 == 1){
    $st = '
			<tr bgcolor="#CCCCCC">
        <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">'.$m_num.'</td>
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$key['colorway'].'</td>
			        <td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			      </tr>
			    </table>
			  </td>';
		$m2 = 1;
		foreach($size_A as $keys){
		$st .= '
				<td valign="top" bgcolor="#CCCCCC">
					<input type="text" name="cw'.$m1.'['.$m2.']" id="cw'.$m1.'['.$m2.']" style="width:20px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')" />
				</td>';
		$m2++;
		}
		$st .= '
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input name="length" type="text" style="width:36px;text-align:right;" value="" /></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">';
		if(!empty($status) && $status == 'edit'){
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Update" onclick="atc(\'edit\')" style="width:52px;cursor:pointer;" /><br>';
		}else{
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Append" onclick="atc(\'append\')" style="width:52px;cursor:pointer;" />';
		}
			$st .= '
				</td>
			</tr>';
		}


		if($m1 <> 1){
		$st .= '
			<tr bgcolor="#CCCCCC">
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$key['colorway'].'</td>
			        <td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			      </tr>
			    </table>
				</td>';
		$m2 = 1;
		foreach($size_A as $keys){
		$st .= '
				<td valign="top" bgcolor="#CCCCCC">
					<input type="text" name="cw'.$m1.'['.$m2.']" id="cw'.$m1.'['.$m2.']" style="width:20px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')" />
				</td>';
		$m2++;
		}
		$st .= '
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			</tr>
		';
		}


		$m1++;
		}

    $str['main'] = $st;

		return $str;
	}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_ord_upload($parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function marker_ord_upload($parm){

		if($parm['marker']) {

			$filename = $_FILES['PHP_marker']['name'];
			$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

			if ($ext == "zip"){
				// 上傳檔的副檔名為 mdl 時 -----
				// upload marker file to server  // 指定為 pdf 副檔名
				# $fab_item = $this->fab_item($parm['fab_type'],$combo);
				$marker_name = $parm['order_num'].'.mk.zip';

				$upload = new Upload;
				$upload->uploadFile(dirname($_SERVER['PHP_SELF']).'/fty_marker/', 'marker', 16, $marker_name );
				//$upload->uploadFile(dirname($PHP_SELF).'/fty_marker/', 'marker', 16, $marker_name );
				echo dirname($_SERVER['PHP_SELF']).'/fty_marker/';
				//exit;
				if (!$upload){
					$op['msg'][] = $upload;
					$layout->assign($op);
					$layout->display($TPL_ERROR);
					break;
				}

				# 更改 order 的上傳日期 ------------
				if (!$A = $GLOBALS['order']->update_field('fty_marker_date',$GLOBALS['TODAY'],$parm['ord_id'])){
					$op['msg'] = $GLOBALS['order']->msg->get(2);
					$GLOBALS['layout']->assign($op);
					$GLOBALS['layout']->display($TPL_ERROR);
					break;
				}

			} else {  // 上傳檔的副檔名  不是  mdl 時 -----
				$message = "upload MARKER file is incorrect format, Please re-send. [*.zip]";
				$_SESSION['MSG'][] = $message;
			}
		}
		return 1;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_add($parm)		加入新 訂單記錄
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function ord_add($parm,$status='') {
		$sql = $this->sql;

		if(empty($status))$status='';
		if(!empty($parm['fab_type'])){
			$combo_num = $this->get_combo(2,$parm['ord_id'],$parm['fab_type']);
			#$combo = ( $parm['fab_type'] == 2 )? ($combo_num+1) : 0 ;
			$combo = $combo_num+1;
		}



		$rmk='';
		if( $parm['rmk'] ){
			foreach( $parm['rmk'] as $key => $val){
				$rmk .= $key.'|';
			}
		}
		$parm['remark'] = substr($rmk,0,-1);

		if($status == 'append'){
			$combo = $parm['combo'];
		}else{
			$parm['mk_num'] = $parm['assortment'] = '';
		}


		if(!isset($parm['remain']))$parm['remain'] = 0;
		if(!isset($parm['fab_code']))$parm['fab_code'] = '';
		if(!isset($parm['lots_code']))$parm['lots_code'] = 0;
		if(!isset($parm['length']))$parm['length'] = '';
		$q_str = "
		INSERT INTO `marker_fty` (
		`id` , `ord_id` , lots_code, `mk_num` , `fab_type` , `unit_type` , `combo` , `width` , `length` ,
		`remain` ,`fab_code` ,
		`last_update` , `updator` , `remark` , `assortment` )
		VALUES (
		'',
		'".$parm['ord_id']."',	
		'".$parm['lots_code']."',	
		'".$parm['mk_num']."',		
		'".$parm['fab_type']."',
		'".$parm['unit_type']."',
		'".$combo."',
		'".$parm['width']."',
		'".$parm['length']."',
		'".$parm['remain']."',
		'".$parm['fab_code']."',
		'".date("Y-m-d H:i:s")."',
		'".$parm['updator']."' ,
		'".$parm['remark']."' ,
		'".$parm['assortment']."'
		);";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return 0;
		}

		$marker_id = $sql->insert_id();  //取出 新的 id

		return $marker_id;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_list(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function marker_list($id=null,$fab_type=null,$combo=null){
		$str = $this->get(2,null,$id,1,$fab_type,$combo);
		$html = '';
		foreach($str as $key)
			if( !empty($key['mk_num']) or $key['mk_num'] === '0' )
				$html .= $this->fab_item($key['fab_type']).$key['combo'].$GLOBALS['ALPHA2'][$key['mk_num']].' , ';
		if($html) return $html = substr($html,0,-2);
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_list2(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function marker_list2($tb,$id,$tb_id=null,$all=null,$fab_type=null,$combo=null,$size){

		$str = $this->get($tb,null,$tb_id,$all,$fab_type,$combo);
		$size = $GLOBALS['size_des']->get($size);

		$asmts = $averages = $clothes = $estimate = '';
		if($str){
			foreach($str as $keys => $vals){
				if(is_array($vals)){
					foreach($vals as $key => $val){
						if( $key === 'assortment' ){
							$asmts = $this->average($val,$str[$keys]['length']);
							if(!empty($asmts)){
								$clothes += $asmts['clothes'];
								$estimate += $asmts['estimate'];
								$averages = $clothes / $estimate;
							}
						}
					}
				}
			}
		}

		if(!empty($averages)) return $averages;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->choic_rmk(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function rmk_list($str){
		$html = '';
		$str = explode('|',$str);
		foreach($str as $key){
			if($key)$html .= $GLOBALS['m_rmk'][$key].'，';
		}
		if($html) $html = substr($html,0,-2).'。';
		return $html;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->choic_rmk(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function choic_rmk(){
		$html = '';
		foreach($GLOBALS['m_rmk'] as $key => $val)
			$html .= '<input name="rmk['.$key.']" id="rmk['.$key.']" type="checkbox" value="radiobutton" /> '.$val.'&nbsp;&nbsp;&nbsp;';
		return $html;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_rmk(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function edit_rmk($str){

		$rmk = explode('|',$str);

		$html = '';
		foreach($GLOBALS['m_rmk'] as $key => $val){
			$checked = '';
			foreach($rmk as $mkey){
				if( $mkey == $key)$checked = 'checked="checked"';
			}
			$html .= '<input name="rmk['.$key.']" id="rmk['.$key.']" type="checkbox" value="radiobutton" '.$checked.' /> '.$val.'&nbsp;&nbsp;&nbsp;';
		}
		return $html;
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->average(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function average($asmt=0,$length=0){
		$asmts = $clothes = $estimate = '';
	
		if( !empty($asmt) && !empty($length) ){
			$asmt = explode('|',$asmt);
			$a_lv = explode(',',$asmt[1]);
			$a_cw = explode(',',$asmt[2]);

			foreach($a_lv as $key){
				$clothes += $key * $length;
				foreach($a_cw as $keys){
					if( !empty($keys) )
					$estimate += $key * $keys;
				}
			}
			if( !empty($clothes) and !empty($estimate) )
			{
				$averages = $clothes / $estimate;

			}
			
		}


			if(!empty($averages))$asmts['averages'] = $averages;
			if(!empty($clothes))$asmts['clothes'] = $clothes;
			if(!empty($estimate))$asmts['estimate'] = $estimate;


		#echo $averages.' = '.$clothes.' / '.$estimate.'<br>';
		if(empty($averages))$averages='';
		if(empty($asmts))$asmts='';
		return $asmts;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_log($ord_num,$item) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM `marker_log` WHERE ord_num='$ord_num' AND item='$item'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}

		if (!$row = $sql->fetch($q_result)) {
				$this->msg->add("Error ! Can't find this record!");
				return false;
		}
		
			
		if(!empty($row))return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_add($parm)		加入新 訂單記錄
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function marker_log_upload($parm) {
		$sql = $this->sql;

		if(!$parm['id'])
		{
			$q_str = "
			INSERT INTO `marker_log` (`ord_num` , `item` , `des` , `user` ,  `k_date` )
			VALUES (
			'".$parm['ord_num']."',
			'".$parm['item']."',
			'".$parm['des']."',
			'".$parm['user']."',
			'".$parm['k_date']."'
			);";
		}else{
			$q_str = "UPDATE `marker_log` SET `des` ='".$parm['des']."'  WHERE id ='".$parm['id']."'";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return 0;
		}

		$marker_id = $sql->insert_id();  //取出 新的 id

		return $marker_id;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_add($parm)		加入新 訂單記錄
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function rpt_add($parm,$status='') {
		$sql = $this->sql;

		if(empty($status))$status='';
		if(!empty($parm['fab_type'])){
			$combo_num = $this->get_rpt_combo(2,$parm['ord_id'],$parm['fab_type']);
			#$combo = ( $parm['fab_type'] == 2 )? ($combo_num+1) : 0 ;
			$combo = $combo_num+1;
		}



		$rmk='';
		if( isset($parm['rmk'] )){
			foreach( $parm['rmk'] as $key => $val){
				$rmk .= $key.'|';
			}
		}

		$parm['remark'] = substr($rmk,0,-1);

		if($status == 'append'){
			$combo = $parm['combo'];
		}else{
			$parm['mk_num'] = $parm['assortment'] = '';
		}

		$q_str = "
		INSERT INTO `marker_rpt` (
		`id` , `ord_id` , `mk_num` , `fab_type` , `unit_type` , `combo` , `width` , `length` ,
		`last_update` , `updator` , `remark` , `assortment` )
		VALUES (
		'',
		'".$parm['ord_id']."',
		'".$parm['mk_num']."',
		'".$parm['fab_type']."',
		'".$parm['unit_type']."',
		'".$combo."',
		'".$parm['width']."',
		'".$parm['length']."',
		'".date("Y-m-d H:i:s")."',
		'".$parm['updator']."' ,
		'".$parm['remark']."' ,
		'".$parm['assortment']."'
		);";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return 0;
		}

		$marker_id = $sql->insert_id();  //取出 新的 id

		return $marker_id;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rpt($tb=1,$id=0,$tb_id=0,$all=null,$fab_type=null,$combo=null,$group=null) {

		$sql = $this->sql;

			$add = (isset($fab_type))? " and `fab_type` = '".$fab_type."' and `combo` = '".$combo."' " : "";
			$group = (isset($group))? " GROUP BY fab_type,combo " : "";
			if ($id)	{
				$q_str = "SELECT * FROM `marker_rpt` WHERE id='$id' $add";
			} elseif ($tb_id) {
				$q_str = "SELECT * FROM `marker_rpt` WHERE ord_id='$tb_id' $add $group ";
			} else {
				$this->msg->add("Error ! please specify order number.");
				return false;
			}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}

		if(!empty($all)){
			while ($rows = $sql->fetch($q_result)) {
				$row[] = $rows;
			}
		}else{
			if (!$row = $sql->fetch($q_result)) {
				$this->msg->add("Error ! Can't find this record!");
				return false;
			}

			$po_user=$GLOBALS['user']->get(0,$row['updator']);
			if ($po_user['name'])$row['updator'] = $po_user['name'];
			$row['last_update'] = substr($row['last_update'],0,10);
		}
		if(!empty($row))return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_list(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function marker_rpt_list($id=null,$fab_type=null,$combo=null){

		$str = $this->get_rpt(2,null,$id,1,$fab_type,$combo);
		$html = '';
			foreach($str as $key)
				if( !empty($key['mk_num']) or $key['mk_num'] === '0' )
					$html .= $this->fab_item($key['fab_type']).$key['combo'].$GLOBALS['ALPHA2'][$key['mk_num']].' , ';
			if($html) return $html = substr($html,0,-2);

	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_list2(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function marker_rpt_list2($tb,$id,$tb_id=null,$all=null,$fab_type=null,$combo=null,$size){

		$str = $this->get_rpt($tb,null,$tb_id,$all,$fab_type,$combo);
		$size = $GLOBALS['size_des']->get($size);

		$asmts = $averages = $clothes = $estimate = '';
		if($str){
			foreach($str as $keys => $vals){
				if(is_array($vals)){
					foreach($vals as $key => $val){
						if( $key === 'assortment' ){
							$asmts = $this->average($val,$str[$keys]['length']);
							if(!empty($asmts)){
								$clothes += $asmts['clothes'];
								$estimate += $asmts['estimate'];
								$averages = $clothes / $estimate;
							}
						}
					}
				}
			}
		}

		if(!empty($averages)) return $averages;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_rpt($tb=1,$id,$ord_id,$fab_type='',$combo='',$status='') {

		$sql = $this->sql;
		$nc='';
			if($status)
				$q_str = "DELETE FROM marker_rpt WHERE id = '".$id."'";
			else
				$q_str = "DELETE FROM marker_rpt WHERE ord_id = '".$id."' and fab_type = '".$fab_type."' and combo = '".$combo."' ";

			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! 無法存取資料庫 !");$this->msg->merge($sql->msg);return 0;};

			$q_str = "select `id`,`combo` from `marker_rpt` WHERE ord_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo = '".$combo."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}


			if (!$row = $sql->fetch($q_result)){
				# 如果之後還有 combo 自動減 1
				$q_str = "select `id`,`combo` from `marker_rpt` where ord_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo > '".$combo."' ";

				if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				while ($row = $sql->fetch($q_result)) {
					$q_str2 = "UPDATE `marker_rpt` SET `combo` = '".( $row['combo'] - 1 )."' where `id` = '".$row['id']."' ;";
				
					if (!$q_result2 = $sql->query($q_str2)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				}
				return 2;
			}else{
				return 1;
			}
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rpt_report_color(     layout
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rpt_report_color($order_num,$T_wiqty,$size_A,$T_wiqty2='',$mk='',$status='') {

		$col = count(explode(',',$size_A['size']));
		$row = count($T_wiqty);

		if( !empty($status) ){
			if( !empty($mk['assortment']) ){
				$asmt = explode('|',$mk['assortment']);
				$a_cr = explode(',',$asmt[0]);
				$a_lv = explode(',',$asmt[1]);
				$a_cw = explode(',',$asmt[2]);
			}
		}else{
			$mk['length'] = '' ;
		}

		$html = '
			<tr>
				<td rowspan="'.($row+2).'" align="center" bgcolor="#FFFFFF">馬克<br />MK #</td>
        <td colspan="'.($col+1).'" align="center" bgcolor="#FFFFFF">配碼 ( Assortment ) </td>
        <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">碼長 ('.$GLOBALS['unit_type2'][$mk['unit_type']].')<br />Length</td>
        <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">&nbsp;</td>
      </tr>';

		$html .= '
      <tr>
        <td align="center" bgcolor="#FFFFFF">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
					  <tr>
					    <td width="40%" align="left" nowrap>配色 Color</td>
					    <td width="20%" align="center">\</td>
					    <td width="40%" align="right">Size</td>
					  </tr>
					</table>
        </td>';
    $size_A = explode(',',$size_A['size']);
		foreach($size_A as $key){
		$html .= '
        <td width="30" align="center" bgcolor="#FFFFFF">'.$key.'</td>';
		}
		$html .= '
      </tr>';

		$m1=1;

    foreach($T_wiqty as $key => $val){
    $html .= '
      <tr>
        <td align="left" bgcolor="#FFFFFF">&nbsp;'.$val['colorway'].'</td>';

	      $qty = explode(',',$val['qty']);
        $m2=1;
        foreach($qty as $qkey => $qval){
        	if(empty($qval))$qval=0;
        	$t_qty = $qval;

        	# + asmt
        	if( !empty($T_wiqty2[$key]) ){
            $qty2 = explode(',',$T_wiqty2[$key]['qty']);
            $t_qty = $qty2[$qkey];
          }
		$html .= '
        <td align="center" bgcolor="#FFFFCC"><input type="text" name="m'.$m1.'_'.$m2.'" id="m'.$m1.'_'.$m2.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" value="'.$t_qty.'" readonly />
        <input type="hidden" name="mh'.$m1.'_'.$m2.'" id="m'.$m1.'_'.$m2.'" value="'.$qval.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" /></td>';
        $m2++;
      	}
    $html .= '
      </tr>';
    $m1++;
    }
    $str['title'] = $html;

    if( !empty($status) )
			$m_num = $this->rpt_select_mk($mk['ord_id'],$mk['fab_type'],$mk['combo'],$mk['mk_num']);
		else
			$m_num = $this->rpt_select_mk($GLOBALS['op']['mk']['ord_id'],$GLOBALS['op']['mk']['fab_type'],$GLOBALS['op']['mk']['combo']);



		$m1 = 1;
		foreach($T_wiqty as $key => $val){
		if($m1 == 1){
    $st = '
			<tr bgcolor="#CCCCCC">
				<td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">'.$m_num.'</td>
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$val['colorway'].'</td>';
			# + asmt
			$mc = 0;
			$lv = '';
			if( !empty($a_cr) ){
				foreach($a_cr as $ckey => $cval){
					if($key == ($cval - 1) ){
						$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" checked="checked" /></td>';
						$mc = 1;
						$lv = $a_lv[$ckey];
					}
				}
			}
			if($mc <> 1)
			$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" /></td>';

			$st .= '
			      </tr>
			    </table>
			  </td>';
		$m2 = 1;

		# + asmt
		foreach($size_A as $keys => $vals){
		$vals = (!empty($a_cw))? $a_cw[$keys]:'';
		$st .= '
				<td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input type="text" name="cw['.$m2.']" id="cw['.$m2.']" style="width:20px;text-align:right;" value="'.$vals.'" onKeyUp="chk(\''.$row.'\',\''.$col.'\')" /></td>';
		$m2++;
		}

		$st .= '
			  <input name="lv['.$m1.']" id="lv['.$m1.']" type="hidden" value=1 />
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input name="PHP_length" id="length" type="text" style="width:36px;text-align:right;" value="'.$mk['length'].'" onClick="chk(\''.$row.'\',\''.$col.'\')"/></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">';
		if(!empty($status) && $status == 'edit'){
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Update" onclick="atc(\'edit\')" style="width:52px;cursor:pointer;" /><br>';
		}else{
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Append" onclick="atc(\'append\')" style="width:52px;cursor:pointer;" />';
		}
			$st .= '
			  </td>
			</tr>';
		}
		if($m1 <> 1){
		$st .= '
			<tr bgcolor="#CCCCCC">
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$val['colorway'].'</td>';
			# + asmt
			if( !empty($a_cr) ){
				$mc = 0;
				$lv = '';
				foreach($a_cr as $ckey => $cval){
					if($key == ($cval - 1) ){
						$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" checked="checked" /></td>';
						$mc = 1;
						$lv = $a_lv[$ckey];
					}
				}
			}
			if($mc <> 1)
			$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" /></td>';

			$st .= '
			      </tr>
			    </table>
				</td>
			  <input name="lv['.$m1.']" id="lv['.$m1.']" type="hidden" value=1 />
			</tr>
		';
		}
		$m1++;
		}

    $str['main'] = $st;

		return $str;
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_main(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rpt_report_main($order_num,$T_wiqty,$size_A,$mk='',$status='') {
		$this->T_wiqty = $T_wiqty;

		# 不同的刪減 *************
		#if($mk['fab_type'] <> 3){
		if($mk['fab_type']){

			# get fab. type asmt.
			$T_qty = $this->re_color($T_wiqty);

			if($T_qty){
				# is true chge.
				foreach($T_qty as $key => $val){
					if($val['assortment'])
						$this->T_wiqty = $this->re_color($this->T_wiqty,$val['assortment'],1,1);
				}
				$T_wiqty = (empty($status))? $this->T_wiqty : $this->re_color($this->T_wiqty,$mk['assortment'],1,2);
			}
			return $this->rpt_report_color($order_num,$T_wiqty,$size_A,$this->T_wiqty,$mk,$status);
		}

	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rpt_select_mk
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rpt_select_mk($ord_id,$fab_type,$combo,$mk='') {
		$sql = $this->sql;
		$q_str = "SELECT `mk_num` FROM `marker_rpt` WHERE `ord_id` = '".$ord_id."' and fab_type = '".$fab_type."'  and combo = '".$combo."' ";

		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		while ($row = $sql->fetch($q_result)) {
			$mk_arr[] = $row['mk_num'];
		}

		$now_arr = array_keys($GLOBALS['ALPHA2']);
		if(!empty($mk_arr))$count1 = count($mk_arr);
		$count2 = count($now_arr);
		for($i=0;$i < $count2;$i++){
			$yes = 0;
			for($y=0;$y < $count1;$y++){
				if( isset($mk_arr[$y]) and $mk_arr[$y] == "$i" ){
					$yes = 1;
				}
			}

			if(isset($i)){
				if($yes <> 1 ){
					$fab_t[$i] = $GLOBALS['ALPHA2'][$i];
				}
			}
		}

		if( !empty($mk) || $mk === '0') $fab_t[$mk] = $GLOBALS['ALPHA2'][$mk];

		ksort($fab_t);
		$str = $GLOBALS['arry2']->select_id($fab_t,$mk,'PHP_mk','select_mk','','','','',1);

		return $str;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_combo($smpl_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rpt_combo($tb=1,$id,$fab_type) {
		$sql = $this->sql;
		$q_str = "SELECT `combo` FROM `marker_rpt` WHERE ord_id = '".$id."' AND fab_type = '".$fab_type."' order by combo DESC limit 1";

		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		if (!$row = $sql->fetch($q_result)){return 0;}
		return $row['combo'];
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_use_mk($lots_id,$ord_id) cutting report用
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_lots_use_mk($lots_id,$ord_id) {
		$sql = $this->sql;
		$q_str = "SELECT assortment, length FROM `marker_fty` 
							WHERE ord_id = '".$ord_id."' AND lots_code = '".$lots_id."'";

		if (!$q_result = $sql->query($q_str))return 0;
		
		$averages = $clothes = $estimate = 0;
		while($row = $sql->fetch($q_result))
		{
			$asmts = $this->average($row['assortment'],$row['length']);
			if(!empty($asmts)){
					$clothes += $asmts['clothes'];
					$estimate += $asmts['estimate'];					
			}
						 
		}
		if($estimate > 0)$averages = $clothes / $estimate;
		return $averages;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_use_mk($lots_id,$ord_id) cutting report用
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_main_shell($ord_id) {
		$sql = $this->sql;
		$assort = array();
		$q_str = "SELECT assortment FROM `marker_fty` 
							WHERE ord_id = '".$ord_id."' AND fab_type = 1 AND combo = 1";
//echo $q_str;
		if (!$q_result = $sql->query($q_str))return 0;
		
		while($row = $sql->fetch($q_result))
		{
			$assort[] = $row['assortment'];
						 
		}
		
		return $assort;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_use_mk($lots_id,$ord_id) cutting report用
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function creat_cut_assort($size,$T_wiqty,$assort) {
		$cut_assort = array();
		$size = explode(',',$size);
	//	echo $size;
		for($i=0; $i<sizeof($T_wiqty); $i++)
		{
			for($j=0; $j<sizeof($size); $j++)
			{
				$cut_assort[$i][$j] = 0;
				if($i == 0)$ttl[$j]=0;
			}
		}
		for($i=0; $i<sizeof($assort); $i++)
		{
			if($assort[$i])
			{
				$tmp = explode('|',$assort[$i]);
				$tmp_color = explode(',',$tmp[0]);
				$tmp_layer = explode(',',$tmp[1]);
				$tmp_qty = explode(',',$tmp[2]);
				for($j=0; $j<sizeof($tmp_color); $j++)
				{
					for($k=0; $k<sizeof($tmp_qty); $k++) $cut_assort[($tmp_color[$j]-1)][$k] += $tmp_qty[$k] * $tmp_layer[$j];
				}
				
			}
		}
		
		for($i=0; $i<sizeof($T_wiqty); $i++)
		{
			$rtn[$i]['colorway'] = $T_wiqty[$i]['colorway'];
			$rtn[$i]['qty'] = $cut_assort[$i];
			$rtn[$i]['sum'] = 0;
			for($j=0; $j<sizeof($size); $j++)
			{
				$rtn[$i]['sum'] += $cut_assort[$i][$j];
				$ttl[$j] += $cut_assort[$i][$j];
			}
		}
		$rtn[$i]['colorway'] = 'TOTAL';
		$rtn[$i]['qty'] = $ttl;
		$rtn[$i]['sum'] = 0;
		for($j=0; $j<sizeof($size); $j++) $rtn[$i]['sum'] += $ttl[$j];
		
		
		return $rtn;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_qty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_po_qty($order_num, $mat_cat) {
	$sql = $this->sql;
	
	$q_str = "select sum(ap_det.po_qty) as po_qty
			  from ap_det, lots_use
			  where ap_det.order_num = '".$order_num."' and ap_det.mat_cat = '".$mat_cat."'
					and lots_use.id = ap_det.used_id and lots_use.use_for like '%shell%'";
	
	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	if(!$row['po_qty']) $row['po_qty'] = 0;
	
	return $row['po_qty'];
} // end func
	
	

}
?>