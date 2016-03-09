<?php 

#++++++++++++++++++++++ FORECAST  class ##### 預算  +++++++++++++++++++++++++++++++++++
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

class FCST2 {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
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
#	->add($parm)		加入新 forecast
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;


//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	

			############### 檢查輸入項目	
			//  輸入是否為數字項
					
					# 加入資料庫
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
		$new_id = $sql->insert_id();  //取出 新的 id

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	搜尋  FORECAST 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
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
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			} else {
				$op['record_NONE'] = "";
			}

		$op['fcst'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['max_no'] = $srh->max_no;

		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0,$parm='',$method='')	抓出指定記錄內資料 RETURN $row[]
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
#	->edit($parm)		更新 forecast 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		#####   更新資料庫內容
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$q_str = "UPDATE forecast2 SET uprc='"	.$parm['uprc'].
								"', qty='"		.$parm['qty'].
								"', cm='"		  .$parm['cm'].
								"', dept='"		  .$user_dept.
								"', fcst='"		.$parm['fcst'].
							"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $parm['id'];
	} // end func







#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
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
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
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
#			搜尋 order ETP 的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp_ord($fty,$year_mon,$limit_entries, $where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
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
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
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