<?php 

#++++++++++++++++++++++ CUST  class ##### 客戶  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class REMNANT {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
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
#	->search($mode=0, $where_str="")	搜尋  USER 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_shipped($mode=0, $per_page=0) {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT s_order.*, cust_init_name as cust_iname FROM s_order, cust ";
		if ($mode==2) $q_header = "SELECT * FROM cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id");
		if ($per_page == 0)
		{
			$srh->row_per_page = 20;
		}else{
			$srh->row_per_page = $per_page;
		}

//2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
    	
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	}    	
    	
if ($mode==1){    //有條件的Search時
		if ($str = $argv['SCH_order'] )  { 
			$srh->add_where_condition("s_order.order_num like  '%$str%'", "SCH_dept",$str,"DEPT = [ $str ]. "); }
		if ($str = $argv['SCH_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "SCH_line",$str,"Cust.: [ $str ]. "); }
		if ($str = $argv['SCH_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "SCH_ord",$str,"Short Name : [ $str ]. "); }
		if ($str = $argv['SCH_style'] )  { 
			$srh->add_where_condition("s_order.style_num like '%$str%'", "SCH_ord",$str,"Short Name : [ $str ]. "); }
}   			
	$srh->add_where_condition("s_order.status > 8");
	$srh->add_where_condition("s_order.close_status = 0");
	$srh->add_where_condition("s_order.m_status = 3");
	$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
 //2006.11.14 以數字型式顯示頁碼 end 
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			} else {
				$op['record_NONE'] = "";
			}

		$op['sorder'] = $result;  // 資料錄 拋入 $op
		
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;

#####2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14新頁碼需要的oup_put	end

		return $op;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_lots($id=0,$status='') {

		$sql = $this->sql;
		$lots = array();
		$q_str="SELECT bom_lots.*, lots_use.lots_code, lots_use.lots_name, lots_use.unit, lots_use.use_for, 
									 lots_use.est_1, lots_use.smpl_code, lots_use.support
						FROM 	 bom_lots, lots_use
						WHERE  bom_lots.lots_used_id = lots_use.id AND bom_lots.dis_ver = 0 AND
									 bom_lots.wi_id='".$id."'";
		if($status == 'v') $q_str .= " AND rem_qty > 0";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$qty=explode(',',$row1['qty']);
			$tmp_qty=0;
			for ($i=0; $i<sizeof($qty); $i++) $tmp_qty=$tmp_qty+$qty[$i];
			$row1['total'] = $tmp_qty;
			$row1['rcv_qty'] = 0;
			$lots[]=$row1;
		}
		
		for($i=0; $i<sizeof($lots); $i++)
		{
			if($lots[$i]['ap_mark'])
			{
					$q_str="SELECT rcv_po_link.qty, rcv_po_link.id, receive.rcv_num, ap_det.po_unit								
									FROM  receive, receive_det, rcv_po_link, ap_det, ap
									WHERE ap_det.ap_num = ap.ap_num AND receive.po_id = ap.id AND
							     			receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id = ap_det.id AND
							      		receive.rcv_num = receive_det.rcv_num AND ap_det.mat_cat = 'l' AND
												ap_det.bom_id='".$lots[$i]['id']."'";
								
					$q_result = $sql->query($q_str);
					while ($row1 = $sql->fetch($q_result)) 
					{
						$lots[$i]['rcv_qty'] += $row1['qty'];
						$lots[$i]['po_unit'] =  $row1['po_unit'];
					}

					$q_str="SELECT rcv_po_link.qty, rcv_po_link.id, receive.rcv_num, ap_special.po_unit									
									FROM  receive, receive_det, rcv_po_link, ap_special, ap
									WHERE ap_special.ap_num = ap.ap_num AND receive.po_id = ap.id AND
							     			receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id = ap_special.id AND
							      		receive.rcv_num = receive_det.rcv_num AND ap_special.mat_cat = 'l' AND
												ap_special.ord_num='".$lots[$i]['smpl_code']."' AND 
												ap_special.mat_code = '".$lots[$i]['lots_name']."' AND
												ap_special.color ='".$lots[$i]['color']."'";
					$q_result = $sql->query($q_str);
					while ($row1 = $sql->fetch($q_result)) 
					{
						$lots[$i]['rcv_qty'] += $row1['qty'];
						$lots[$i]['po_unit'] =  $row1['po_unit'];
					}
			}
		}
		
		return $lots;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_acc($id=0,$status='') {

		$sql = $this->sql;
		$lots = array();
		$q_str="SELECT bom_acc.*, acc_use.acc_code, acc_use.acc_name, acc_use.unit, acc_use.use_for, 
									 acc_use.est_1, acc_use.smpl_code, acc_use.support
						FROM 	 bom_acc, acc_use
						WHERE  bom_acc.acc_used_id = acc_use.id AND bom_acc.dis_ver = 0 AND
									 bom_acc.wi_id='".$id."'";
		if($status == 'v') $q_str .= " AND rem_qty > 0";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$qty=explode(',',$row1['qty']);
			$tmp_qty=0;
			for ($i=0; $i<sizeof($qty); $i++) $tmp_qty=$tmp_qty+$qty[$i];
			$row1['total'] = $tmp_qty;
			$row1['rcv_qty'] = 0;
			$lots[]=$row1;
		}
		
		for($i=0; $i<sizeof($lots); $i++)
		{
			if($lots[$i]['ap_mark'])
			{
					$q_str="SELECT rcv_po_link.qty, rcv_po_link.id, receive.rcv_num, ap_det.po_unit									
									FROM  receive, receive_det, rcv_po_link, ap_det, ap
									WHERE ap_det.ap_num = ap.ap_num AND receive.po_id = ap.id AND
							     			receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id = ap_det.id AND
							      		receive.rcv_num = receive_det.rcv_num AND ap_det.mat_cat = 'a' AND
												ap_det.bom_id='".$lots[$i]['id']."'";
									
					$q_result = $sql->query($q_str);
					while ($row1 = $sql->fetch($q_result)) 
					{
						$lots[$i]['rcv_qty'] += $row1['qty'];
						$lots[$i]['po_unit'] =  $row1['po_unit'];
					}

					$q_str="SELECT rcv_po_link.qty, rcv_po_link.id, receive.rcv_num, ap_special.po_unit						
									FROM  receive, receive_det, rcv_po_link, ap_special, ap
									WHERE ap_special.ap_num = ap.ap_num AND receive.po_id = ap.id AND
							     			receive_det.id = rcv_po_link.rcv_id AND rcv_po_link.po_id = ap_special.id AND
							      		receive.rcv_num = receive_det.rcv_num AND ap_special.mat_cat = 'a' AND
												ap_special.ord_num='".$lots[$i]['smpl_code']."' AND 
												ap_special.mat_code = '".$lots[$i]['acc_name']."' AND
												ap_special.color ='".$lots[$i]['color']."'";
					$q_result = $sql->query($q_str);
					while ($row1 = $sql->fetch($q_result)) 
					{
						$lots[$i]['rcv_qty'] += $row1['qty'];
						$lots[$i]['po_unit'] =  $row1['po_unit'];
					}
			}
		}
		
		return $lots;
	} // end func





		
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>