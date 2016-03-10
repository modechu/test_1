<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->bom_search($supl,$cat)	查詢BOM的主副料
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class COST {
		
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_output($s_date,$e_date,$fty,$rem_date) {

		$sql = $this->sql;
		$output = array();
		$eta_date='';
		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
		$q_str = "SELECT s_order.*, sum(daily_out.qty)as out_qty, sum(daily_out.su)as out_su
							FROM s_order, daily_out  
							WHERE s_order.order_num =daily_out.ord_num AND  s_order.factory = '$fty'
										AND daily_out.k_date >= '$s_date' AND daily_out.k_date <= '$e_date'
							GROUP BY daily_out.ord_num ";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
		 $where_str = " AND out_month = '$rem_date' AND ord_num = '".$row['order_num']."' GROUP BY ord_num";
	   $row['rem'] = $this->get_rem_fld('sum(rem_qty) as remun_qty',$where_str);
	   $row['rmn_qty'] = $row['out_qty'] - $row['rem'];
	   if ($row['rmn_qty'] > 0)   $output[]=$row;
	   
		}


		return $output;
	} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rem_fld($fld,$where) {

		$sql = $this->sql;
		$eta_date='';
		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
		$q_str = "SELECT ".$fld." FROM remun, remun_det WHERE remun.id = remun_det.rem_id ".$where;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);

		if (isset($row[0]))
		{
			return $row[0];
		}else{
			return false;
		}
	} // end func	
	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> buy_get_no($hend,$n_field,$tables)	為新單據做編號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_no($hend,$n_field,$tables) {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT ". $n_field." FROM ".$tables." where ".$n_field. " like '%".$hend."%' order by ".$n_field." desc limit 1";
		if (!$q_result = $sql->query($q_str)) {		//搜尋最後一筆
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//如果沒有資料的話
			$buy_no = '1';
		
		}else{	//將最後一筆的數字+1
			$long = strlen($hend);
			$buy_no = substr($row[$n_field],$long);	//去掉表頭
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//在數字前補0到達四位數字
		{
			$buy_no=$hend."00".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$hend."0".$buy_no;
		}else{
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->  get_cost_no($hend,$n_field,$tables)	為新單據做編號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cost_no($hend,$n_field,$tables) {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT ". $n_field." FROM ".$tables." where ".$n_field. " like '%".$hend."%' order by ".$n_field." desc limit 1";
		if (!$q_result = $sql->query($q_str)) {		//搜尋最後一筆
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//如果沒有資料的話
			$buy_no = '1';
		
		}else{	//將最後一筆的數字+1
			$long = strlen($hend);
			$buy_no = substr($row[$n_field],$long);	//去掉表頭
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//在數字前補0到達四位數字
		{
			$buy_no=$hend."0".$buy_no;
		}else{
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_main($parm) {
					
		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO remun (num,fty,rem_date,out_month,open_user) 
				  VALUES('".
							$parm['num']."','".
							$parm['fty']."','".
							date('Y-md')."','".						
							$parm['out_month']."','".	
							$parm['submit_user']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
					
		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO remun_det (rem_id,ord_num,acc_cost,smpl,rem_qty) 
				  VALUES('".
							$parm['rem_id']."','".
							$parm['ord_num']."','".
							$parm['acc_cost']."','".						
							$parm['smpl']."','".	
							$parm['rem_qty']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_remun($id, $rem_id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM remun_det WHERE id='".$id."'";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}         
		 $where_str = " AND rem_id = '$rem_id' ";
	   $rem = $this->get_rem_fld('remun_det.id',$where_str);
		 if(!$rem)
		 {
	 			$q_str="DELETE FROM remun WHERE id='".$rem_id."'";
	 			if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error !  Database can't update.");
					$this->msg->merge($sql->msg);
					return false;    
				}
				return '';
			}
	
			return $rem_id;

	}// end func	
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->function update_fields($field1, $value1, $id, $table='remun')
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_fields($field1, $value1, $id, $table='remun') {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
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
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_remun($mode=0, $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT remun.*, sum(rem_qty)as qty, sum(smpl) as smpl, sum((rem_qty+smpl) * (acc_cost+fty_cm)) as cost FROM remun, remun_det, s_order ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("remun.id DESC");
		$srh->row_per_page = 20;

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


	//2006/05/12 adding 
//	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

//部門 : 工廠
//	if ($user_dept == 'HJ' || $user_dept == 'LY')	$srh->add_where_condition("remun.fty = '$user_dept'");

   if ($mode==1){
		$mesg = '';
		if ($str = $argv['PHP_FTY'] )  { 
			$srh->add_where_condition("remun.fty = '$str'", "PHP_FTY",$str); 
			$mesg.= "  FTY : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_year'] )  { 
			$srh->add_where_condition("remun.out_month like '$str%'", "PHP_year",$str); 
			$mesg.= "  Year = [ $str ]. ";
			}
		if ($str = $argv['PHP_month'] )  { 
			$srh->add_where_condition("remun.out_month like '%-$str'", "PHP_month",$str); 
			$mesg.= "  Month = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}			
   }	
   
   $srh->add_where_condition("remun.id = remun_det.rem_id");
   $srh->add_where_condition("s_order.order_num = remun_det.ord_num");
   $srh->add_group_condition("remun_det.rem_id");
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['rem'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}	

		return $op;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id) {

		$sql = $this->sql;
		
//主檔
		$q_str = "SELECT remun.* FROM remun WHERE  remun.id = $id";
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
		$op['remun']=$row;

		//改變Login帳號為名字
	$po_user=$GLOBALS['user']->get(0,$op['remun']['submit_user']);
	if ($po_user['name'])$op['remun']['submit_user'] = $po_user['name'];	

	$po_user=$GLOBALS['user']->get(0,$op['remun']['open_user']);
	if ($po_user['name'])$op['remun']['open_user'] = $po_user['name'];	



//明細
		$q_str="SELECT remun_det.*, s_order.fty_cm, s_order.cm, s_order.style_num, sum(daily_out.qty) as qty, s_order.smpl_ord, s_order.id as ord_id
						FROM remun, remun_det, s_order, daily_out
						WHERE remun_det.rem_id = remun.id AND s_order.order_num = remun_det.ord_num AND s_order.order_num = daily_out.ord_num
						AND daily_out.ord_num = remun_det.ord_num AND remun.id = $id GROUP BY s_order.order_num"; 
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		$tmp_cost =0;
		while ($row1 = $sql->fetch($q_result)) {
//			if($row1['fty_cm'] <= 0) $row1['fty_cm'] =$row1['cm'];

		 $where_str = " AND out_month = '".$op['remun']['out_month']."' AND ord_num = '".$row1['ord_num']."' GROUP BY ord_num";
	   $rem_tal = $this->get_rem_fld('sum(rem_qty) as remun_qty',$where_str);
	   $row1['rmn_qty'] = $row1['qty'] - $rem_tal;


			$row1['a_cost'] = $row1['fty_cm'] + $row1['acc_cost'];
			$row1['cost'] = $row1['a_cost'] * ($row1['rem_qty']+$row1['smpl']);
			$tmp_cost  = $tmp_cost +$row1['cost'];
			$op['rem_det'][]=$row1;
			
		}
		$op['total_cost'] = $tmp_cost;


		
		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_det($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE remun_det SET acc_cost ='".$parm['acc_cost'].
																		"', smpl='".$parm['smpl'].
																		"',rem_qty='".$parm['rem_qty'].
								"' WHERE id= '".	$parm['id'] ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function submit_remun($id,$sub_user) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE remun SET status ='2', submit_user='".$sub_user."', submit_date='".date('Y-m-d').
								"' WHERE id= '".	$id ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cm($parm) {

		$sql = $this->sql;
		$eta_date='';
		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
		$q_str = "SELECT s_order.*, remun_det.acc_cost as fty_acc_cost, remun_det.rem_qty, style_type.des as style_des, remun_det.id as rem_id
							FROM s_order, remun_det, remun, style_type
							WHERE s_order.order_num =remun_det.ord_num AND remun_det.rem_id = remun.id AND s_order.style = style_type.style_type
							
										AND  s_order.factory = '".$parm['fty']."'
										AND s_order.dept = '".$parm['dept']."' AND s_order.cust = '".$parm['cust']."'  
										AND remun.out_month = '".$parm['out_date']."' AND remun_det.sc_mk = 0
							GROUP BY remun_det.ord_num ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['mat_cost'] = $row['mat_useage'] * $row['mat_u_cost'];
			$row['in_cost'] = $row['rem_qty'] * $row['uprice'];
			$row['acc_u_cost'] = $row['interline'] + $row['acc_u_cost'] + $row['fusible'];
			$row['a_cost'] = $row['mat_u_cost'] + $row['acc_u_cost']+$row['fty_acc_cost']+$row['comm_fee']+$row['fty_cm'];
	   	$row['cost'] = $row['a_cost'] * $row['rem_qty'];
	   	$row['gross'] = $row['in_cost'] - $row['cost'];
	   	$row['gross_rate'] = $row['gross'] / $row['in_cost'] * 100;
	   $output[]=$row;
	   
		}


		return $output;
	} // end func		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cost_main($parm) {
					
		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO salescost (sc_num,cust,fty,dept,out_month,open_user,open_date) 
				  VALUES('".
				  		$parm['num']."','".
							$parm['cust']."','".
							$parm['fty']."','".
							$parm['dept']."','".						
							$parm['out_month']."','".	
							$parm['open_user']."','".
							$parm['open_date']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cost_det($parm) {

		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO salescost_det (cost_id,remdt_id,etd,qty,quota,fob,fab_cost,yy,acc_cost,acc_f_cost,smpl_cost,comm,cm) 
				  VALUES('".
							$parm['cost_id']."','".
							$parm['rem_id']."','".
							$parm['etd']."','".						
							$parm['qty']."','".	
							$parm['quota']."','".							
							$parm['fob']."','".
							$parm['fab_cost']."','".
							$parm['yy']."','".
							$parm['acc_cost']."','".
							$parm['acc_f_cost']."','".							
							$parm['smpl_cost']."','".
							$parm['comm']."','".							
							$parm['cm']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func	 
	
	
 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_cost($id, $cost_id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM salescost_det WHERE id='".$id."'";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}         
		 $where_str = " AND cost_id = '$cost_id' ";
	   $rem = $this->get_cost_fld('salescost_det.id',$where_str);
		 if(!$rem)
		 {
	 			$q_str="DELETE FROM salescost WHERE id='".$cost_id."'";
	 			if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error !  Database can't update.");
					$this->msg->merge($sql->msg);
					return false;    
				}
				return '';
			}
	
			return $cost_id;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cost_fld($fld,$where) {

		$sql = $this->sql;
		$eta_date='';
		$q_str = "SELECT ".$fld." FROM salescost, salescost_det WHERE salescost.id = salescost_det.cost_id ".$where;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);

		if (isset($row[0]))
		{
			return $row[0];
		}else{
			return false;
		}
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cost($id,$ck=0) {

		$sql = $this->sql;
		$eta_date='';

		$q_str = "SELECT salescost.*, cust.cust_init_name as cust_init, dept.dept_name	
							FROM salescost, cust, dept
						  WHERE  dept.dept_code = salescost.dept AND cust.cust_s_name = salescost.cust
							       AND salescost.id ='$id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$op['cost'] = $sql->fetch($q_result);
if($ck == 0)echo "<font color=#FFFFFF>xxx</font>";
	$po_user=$GLOBALS['user']->get(0,$op['cost']['open_user']);
	if ($po_user['name'])$op['cost']['open_user'] = $po_user['name'];	
	$po_user=$GLOBALS['user']->get(0,$op['cost']['submit_user']);
	if ($po_user['name'])$op['cost']['submit_user'] = $po_user['name'];	
	$po_user=$GLOBALS['user']->get(0,$op['cost']['cfm_user']);
	if ($po_user['name'])$op['cost']['cfm_user'] = $po_user['name'];	
	$po_user=$GLOBALS['user']->get(0,$op['cost']['apv_user']);
	if ($po_user['name'])$op['cost']['apv_user'] = $po_user['name'];	
	if ($op['cost']['status'] == 4 || $op['cost']['version'] > 0 ) $op['cost']['version']++;


		$q_str = "SELECT s_order.order_num, (s_order.mat_useage*s_order.mat_u_cost) as o_mat,
										 s_order.etd as o_etd, s_order.quota as o_quota, s_order.uprice,
										 s_order.mat_useage, s_order.mat_u_cost,
										 (s_order.interline+s_order.acc_u_cost+s_order.fusible) as o_acc,  
										 s_order.comm_fee, s_order.fty_cm, s_order.uprice, salescost_det.*,
										 remun_det.acc_cost as fty_acc_cost, remun_det.rem_qty, style_type.des as style_des
							FROM s_order, remun_det, style_type, salescost, salescost_det
							WHERE s_order.order_num =remun_det.ord_num AND s_order.style = style_type.style_type
										AND salescost.id = salescost_det.id AND salescost_det.remdt_id = remun_det.id
										AND salescost.id ='$id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			//訂單的成本(預估)
			$row['o_in_cost'] = $row['rem_qty'] * $row['uprice'];
			$row['o_a_cost'] = $row['o_mat'] + $row['o_acc']+$row['fty_acc_cost']+$row['comm_fee']+$row['fty_cm'];
	   	$row['o_cost'] = $row['o_a_cost'] * $row['rem_qty'];
	   	$row['o_gross'] = $row['o_in_cost'] - $row['o_cost'];
	   	$row['o_gross_rate'] = $row['o_gross'] / $row['o_in_cost'] * 100;
	    
	    //訂單的成本(實際)
			$row['c_fab'] = $row['fab_cost'] * $row['yy'];
			$row['c_in_cost'] = $row['qty'] * $row['fob'];
			$row['c_a_cost'] = $row['c_fab'] + $row['acc_cost']+$row['acc_f_cost']+$row['comm']+$row['cm'];
	   	$row['c_cost'] = $row['c_a_cost'] * $row['qty'];
	   	$row['c_gross'] = $row['c_in_cost'] - $row['c_cost'];
	   	$row['c_gross_rate'] = $row['c_gross'] / $row['c_in_cost'] * 100;
	    $tmp =  explode(' ',$row['style_des']);
	    $row['style_des'] = $tmp[0];
	    
	    
	    $op['scost'][]=$row;
	   
		}

		$q_str = "SELECT salescost_log.*	FROM salescost_log WHERE  salescost_log.cost_id ='$id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {	    
	    $op['cost_log'][]=$row;	   
		}

		return $op;
	} // end func		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cost($mode=0, $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT salescost.*, sum(qty * fob) as in_cost,
												sum(((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty) as cost,
												sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty)) as gross, 
												(sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty))/sum(qty * fob))*100 as gross_rate
								 FROM salescost, salescost_det ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("salescost.id DESC");
		$srh->row_per_page = 20;

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


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("salescost.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}


   if ($mode==1){
		$mesg = '';
		if ($str = $argv['PHP_FTY'] )  { 
			$srh->add_where_condition("salescost.fty = '$str'", "PHP_FTY",$str); 
			$mesg.= "  FTY : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_year'] )  { 
			$srh->add_where_condition("salescost.out_month like '$str%'", "PHP_year",$str); 
			$mesg.= "  Year = [ $str ]. ";
			}
		if ($str = $argv['PHP_month'] )  { 
			$srh->add_where_condition("salescost.out_month like '%-$str'", "PHP_month",$str); 
			$mesg.= "  Month = [ $str ]. ";
			}
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("salescost.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_dept'] )  { 
			$srh->add_where_condition("salescost.dept = '$str'", "PHP_dept",$str); 
			$mesg.= "  Dept. = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}			
   }	
   if ($mode==2){
   		if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
			$srh->add_where_condition("salescost.status = 2");
   }
   if ($mode==3){
   		if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
			$srh->add_where_condition("salescost.status = 4");
   }   
   
   $srh->add_where_condition("salescost.id = salescost_det.cost_id");
   $srh->add_group_condition("salescost_det.cost_id");
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['cost'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}	

		return $op;
	} // end func
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_uncfm_cost($mode=0, $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT salescost.*, sum(qty * fob) as in_cost,
												sum(((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty) as cost,
												sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty)) as gross, 
												(sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty))/sum(qty * fob))*100 as gross_rate
								 FROM salescost, salescost_det ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("salescost.id DESC");
		$srh->row_per_page = 10;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if (isset($argv['PHP_sr_cost']) && $argv['PHP_sr_cost']) {
			$pages = $srh->get_page($argv['PHP_sr_cost'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("salescost.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}



   if ($mode==2){
   		if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
			$srh->add_where_condition("salescost.status = 2");
   }
   if ($mode==3){
   		if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
			$srh->add_where_condition("salescost.status = 4");
   }   
   
   $srh->add_where_condition("salescost.id = salescost_det.cost_id");
   $srh->add_group_condition("salescost_det.cost_id");
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['cost'] = $result;  // 資料錄 拋入 $op
		$op['max_no'] = $srh->max_no;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}	
		return $op;
	} // end func		
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_cost_det($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容


		$q_str = "UPDATE salescost_det SET      etd 				='".$parm['etd'].
																		"', qty					='".$parm['qty'].																		
																		"', quota				='".$parm['quota'].
																		"', fob					='".$parm['fob'].
																		"', fab_cost		='".$parm['fab_cost'].
																		"', yy					='".$parm['yy'].
																		"', acc_cost		='".$parm['acc_cost'].
																		"', acc_f_cost	='".$parm['acc_f_cost'].
																		"', smpl_cost		='".$parm['smpl_cost'].
																		"', comm				='".$parm['comm'].																		
																		"', cm					='".$parm['cm'].
								"' WHERE id= '".	$parm['id'] ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cost_update_fld($field,$value,$id) {

		$sql = $this->sql;

		#####   更新資料庫內容


		$q_str = "UPDATE salescost SET ".$field." ='".$value.
								"' WHERE id= '".	$id ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func		
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cost_log($parm) {

		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO salescost_log (cost_id,log_user,k_time,subj,des) 
				  VALUES('".
							$parm['cost_id']."','".
							$parm['log_user']."','".
							$parm['k_time']."','".						
							$parm['subj']."','".									
							$parm['des']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func	 	
	
	
	
	
	
	
	


	
	
	
	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM ap ".$where_str;
		//echo $q_str;
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
			if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_det_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM ap".$where_str;

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
			if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func	
	


} // end class


?>