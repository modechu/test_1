<?php 

#+++++++++++++++ SMPL_ORDER  class ##### 樣本類別 +++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL_SHIP {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
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

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, smpl_type=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_smpl($dept, $fty) {

		$sql = $this->sql;

		$q_str="SELECT smpl_ord.num, smpl_ord.qty_done FROM smpl_ord 
					  WHERE  (smpl_ord.case_close = '0000-00-00' OR smpl_ord.case_close >= '2009-01-01') AND smpl_ord.status >= 6 AND
					  			 dept = '$dept' AND factory = '$fty' AND qty_done > fty_qty_shp
					  GROUP BY smpl_ord.num";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
			$rtn[] = $row;
		}

		
		
		return $rtn;
	} // end func








#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新記錄
#		加入新 記錄			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;

			# 加入資料庫
		$q_str = "INSERT INTO fty_smpl_ship (ship_num,ship_to,open_date,open_user,fty,dept,status) VALUES 			
						('".$parm['ship_num']."','".
							$parm['ship_to']."','".
							$parm['open_date']."','".
							$parm['open_user']."','".
							$parm['fty']."','".
							$parm['dept']."','							
							0')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("sucessful adding sample order : [".$parm['ship_num']."]。") ;
		
		return $id;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新記錄
#		加入新 記錄			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ship($parm) {

		$sql = $this->sql;

//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	


			# 加入資料庫
		$q_str = "INSERT INTO cust_smpl_ship (ship_num,ship_to,open_date,open_user,cust,cust_ver,dept,status) VALUES 			
						('".$parm['ship_num']."','".
							$parm['ship_to']."','".
							$parm['open_date']."','".
							$parm['open_user']."','".
							$parm['cust']."','".
							$cust_row['ver']."','".
							$parm['dept']."','							
							0')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("sucessful adding sample order : [".$parm['ship_num']."]。") ;
		
		return $id;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新記錄
#		加入新 記錄			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {

		$sql = $this->sql;

			# 加入資料庫
		$q_str = "INSERT INTO fty_smpl_ship_det (size,ship_qty,smpl_num,f_ship_id) VALUES 			
						('".$parm['size']."','".
							$parm['qty']."','".
							$parm['smpl_num']."','".
							$parm['f_ship_id']."'
							)";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$id = $sql->insert_id();  //取出 新的 id
		
		return $id;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新記錄
#		加入新 記錄			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ship_det($parm) {

		$sql = $this->sql;

			# 加入資料庫
		$q_str = "INSERT INTO cust_smpl_ship_det (size,ship_qty,smpl_num,c_ship_id) VALUES 			
						('".$parm['size']."','".
							$parm['qty']."','".
							$parm['smpl_num']."','".
							$parm['c_ship_id']."'
							)";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot adding record to database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$id = $sql->insert_id();  //取出 新的 id
		
		return $id;
	} // end func		
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_id($parm,$table = 'fty_smpl_ship') 
#		更新  某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_id($parm,$table = 'fty_smpl_ship') {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE ".$table." SET ".$parm['field_name']." ='".$parm['field_value']."'
						  WHERE id='".$parm['id']."'";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD data table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, smpl_type=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0) {

		$sql = $this->sql;

		$q_str = "SELECT fty_smpl_ship.* FROM fty_smpl_ship  WHERE fty_smpl_ship.id='".$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return false;    
		}
	$tmp_user=$GLOBALS['user']->get(0,$row['open_user']);
	$row['open_user_id'] = $row['open_user'];
	if ($tmp_user['name'])$row['open_user'] = $tmp_user['name'];
	$tmp_user=$GLOBALS['user']->get(0,$row['sub_user']);
	$row['sub_user_id'] = $row['sub_user'];
	if ($tmp_user['name'])$row['sub_user'] = $tmp_user['name'];
	$tmp_user=$GLOBALS['user']->get(0,$row['rcv_o_user']);
	$row['rcv_o_user_id'] = $row['rcv_o_user'];
	if ($tmp_user['name'])$row['rcv_o_user'] = $tmp_user['name'];
	$tmp_user=$GLOBALS['user']->get(0,$row['rcv_s_user']);
	$row['rcv_s_user_id'] = $row['rcv_s_user'];
	if ($tmp_user['name'])$row['rcv_s_user'] = $tmp_user['name'];


		$op['shp'] = $row;


		$q_str="SELECT fty_smpl_ship_det.*, smpl_ord.style, smpl_ord.ref  FROM fty_smpl_ship_det, smpl_ord
					  WHERE smpl_ord.num = fty_smpl_ship_det.smpl_num AND f_ship_id = '".$id."' 
						ORDER BY smpl_num";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
			$op['shp_det'][] = $row;
		}

		
		
		return $op;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, smpl_type=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ship($id=0) {

		$sql = $this->sql;

		$q_str = "SELECT cust_smpl_ship.*, cust.cust_init_name as cust_name 
						  FROM cust_smpl_ship, cust  
						  WHERE cust.cust_s_name = cust_smpl_ship.cust AND cust.ver = cust_smpl_ship.cust_ver AND cust_smpl_ship.id='".$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return false;    
		}
	$tmp_user=$GLOBALS['user']->get(0,$row['open_user']);
	$row['open_user_id'] = $row['open_user'];
	if ($tmp_user['name'])$row['open_user'] = $tmp_user['name'];
	$tmp_user=$GLOBALS['user']->get(0,$row['sub_user']);
	$row['sub_user_id'] = $row['sub_user'];
	if ($tmp_user['name'])$row['sub_user'] = $tmp_user['name'];


		$op['shp'] = $row;


		$q_str="SELECT cust_smpl_ship_det.*, smpl_ord.style, smpl_ord.ref  FROM cust_smpl_ship_det, smpl_ord
					  WHERE smpl_ord.num = cust_smpl_ship_det.smpl_num AND c_ship_id = '".$id."' 
						ORDER BY smpl_num";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
			$op['shp_det'][] = $row;
		}

		
		
		return $op;
	} // end func			


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 產品基本資料[由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_det($id) {

		$sql = $this->sql;
		$q_str = "DELETE FROM fty_smpl_ship_det WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 產品基本資料[由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_ship_det($id) {

		$sql = $this->sql;
		$q_str = "DELETE FROM cust_smpl_ship_det WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func		
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 產品基本資料[由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		$q_str = "DELETE FROM fty_smpl_ship WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func			
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 產品基本資料[由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_ship($id) {

		$sql = $this->sql;
		$q_str = "DELETE FROM cust_smpl_ship WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#  mode = 1 : shipping search
#  mode = 2 : receive add search
#	 mode = 3 :	receive search
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
//		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$argv = $_SESSION['sch_parm'];
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM fty_smpl_ship ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['action']);
		$srh->add_sort_condition("id DESC ");
		if($limit_entries) 
		{
			$srh->row_per_page = $limit_entries;
		}else{
			$srh->row_per_page = 20;
		}

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
			
	}else{
##--*****--2006.11.16頁碼新增 start		##	
			
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}

if ($mode==1){
		if ($str =$argv['fty'] )  { 
			$srh->add_where_condition("fty = '$str'", "PHP_cat",$str,"Ship From = [ $str ]. "); }
		if ($str = $argv['ship_num'] )  { 
			$srh->add_where_condition("ship_num LIKE '%$str%'", "PHP_ship_num",$str,"Shipping # :[ $str ]. "); }
		if ($str = $argv['ship_to'] )  { 
			$srh->add_where_condition("ship_to LIKE '%$str%'", "PHP_ship_to",$str,"Ship TO :[ $str ]. "); }
		if ($str = $argv['ship_str'] )  { 
			$srh->add_where_condition("sub_date <= '$str'", "PHP_content",$str,"Ship date : [ $str ] ~"); }
		if ($str = $argv['ship_fsh'] )  { 
			$srh->add_where_condition("sub_date >= '$str'", "PHP_supl_ref",$str,"Ship date : ~[ $str ] "); }
}			

if ($mode==2){
		if ($str =$argv['fty'] )  { 
			$srh->add_where_condition("fty = '$str'", "PHP_cat",$str,"Ship From = [ $str ]. "); }
		if ($str = $argv['ship_num'] )  { 
			$srh->add_where_condition("ship_num LIKE '%$str%'", "PHP_ship_num",$str,"Shipping # :[ $str ]. "); }
		if ($str = $argv['ship_to'] )  { 
			$srh->add_where_condition("ship_to LIKE '%$str%'", "PHP_ship_to",$str,"Ship TO :[ $str ]. "); }

		$srh->add_where_condition("status = '2'");

}			

if ($mode==3){
		if ($str =$argv['fty'] )  { 
			$srh->add_where_condition("fty = '$str'", "PHP_cat",$str,"Shipper = [ $str ]. "); }
		if ($str = $argv['rcv_num'] )  { 
			$srh->add_where_condition("rcv_num LIKE '%$str%'", "PHP_rcv_num",$str,"receive # :[ $str ]. "); }
		if ($str = $argv['dept'] )  { 
			$srh->add_where_condition("dept = '$str'", "PHP_ship_to",$str,"receiver :[ $str ]. "); }
		if ($str = $argv['rcv_str'] )  { 
			$srh->add_where_condition("rcv_s_date <= '$str'", "PHP_content",$str,"rcv. date : [ $str ] ~"); }
		if ($str = $argv['rcv_fsh'] )  { 
			$srh->add_where_condition("rcv_s_date >= '$str'", "PHP_supl_ref",$str,"rcv. date : ~[ $str ] "); }
		$srh->add_where_condition("status > '2'");

}	


		$result= $srh->send_query2();  
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['ship'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] =  $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}


		return $op;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#  mode = 1 : shipping search
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_ship($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
//		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$argv = $_SESSION['sch_parm'];
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT cust_smpl_ship.*, cust.cust_init_name as cust_name FROM cust_smpl_ship, cust ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['action']);
		$srh->add_sort_condition("id DESC ");
		if($limit_entries) 
		{
			$srh->row_per_page = $limit_entries;
		}else{
			$srh->row_per_page = 20;
		}

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
			
	}else{
##--*****--2006.11.16頁碼新增 start		##	
			
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}

if ($mode==1){
		if ($str =$argv['dept'] )  { 
			$srh->add_where_condition("dept = '$str'", "PHP_cat",$str,"Ship From = [ $str ]. "); }
		if ($str = $argv['ship_num'] )  { 
			$srh->add_where_condition("ship_num LIKE '%$str%'", "PHP_ship_num",$str,"Shipping # :[ $str ]. "); }
		if ($str = $argv['ship_to'] )  { 
			$srh->add_where_condition("ship_to LIKE '%$str%'", "PHP_ship_to",$str,"Ship TO :[ $str ]. "); }
		if ($str = $argv['ship_str'] )  { 
			$srh->add_where_condition("sub_date <= '$str'", "PHP_content",$str,"Ship date : [ $str ] ~"); }
		if ($str = $argv['ship_fsh'] )  { 
			$srh->add_where_condition("sub_date >= '$str'", "PHP_supl_ref",$str,"Ship date : ~[ $str ] "); }
}			

		$srh->add_where_condition("cust.cust_s_name = cust_smpl_ship.cust AND cust.ver = cust_smpl_ship.cust_ver");



		$result= $srh->send_query2();  
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['ship'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] =  $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}


		return $op;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_fty_ship($id) {

		$sql = $this->sql;
		$fld= array();
					# 檢查是否有這個檔案存在
		$q_str = "SELECT sum(ship_qty) as ship_qty, smpl_num, qty_done FROM fty_smpl_ship_det, smpl_ord
							WHERE smpl_ord.num = fty_smpl_ship_det.smpl_num AND fty_smpl_ship_det.f_ship_id='".$id."'
							GROUP BY smpl_num";
							
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access to database, please contact system Administrator.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result) ) {
			if($row['qty_done'] < $row['ship_qty'])
			{
				return false;
			}
			$fld[] = $row;   	    
		}

		#####   更新資料庫內容
		for($i=0; $i<sizeof($fld); $i++)
		{
			$q_str = "UPDATE smpl_ord SET	fty_qty_shp='".$fld[$i]['ship_qty'].
									"'	WHERE num='".$fld[$i]['smpl_num']."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  cannot update databse.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}		
		return true;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_sales_ship($id) {

		$sql = $this->sql;
		$fld= array();
					# 檢查是否有這個檔案存在
		$q_str = "SELECT sum(fty_smpl_ship_det.rcv_qty) as rcv_qty, cust_smpl_ship_det.smpl_num, cust_smpl_ship_det.size,
										 sum(cust_smpl_ship_det.ship_qty) as ship_qty
							FROM fty_smpl_ship_det, cust_smpl_ship_det
							WHERE cust_smpl_ship_det.smpl_num = fty_smpl_ship_det.smpl_num AND 
										cust_smpl_ship_det.size = fty_smpl_ship_det.size AND
										cust_smpl_ship_det.c_ship_id='".$id."'
							GROUP BY cust_smpl_ship_det.smpl_num, cust_smpl_ship_det.size";
						
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access to database, please contact system Administrator.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result) ) {
			if($row['rcv_qty'] < $row['ship_qty'])
			{
				return false;
			}
			if(!isset($fld[$row['smpl_num']]))$fld[$row['smpl_num']] = 0;
			$fld[$row['smpl_num']] += $row['ship_qty'];   	    
		}

		#####   更新資料庫內容
		foreach($fld as $key => $value)
		{
			$q_str = "UPDATE smpl_ord SET	sal_qty_shp='".$value.
									"'	WHERE num='".$key."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  cannot update databse.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}		
		return true;
	} // end func	
	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_id($parm,$table = 'fty_smpl_ship') 
#		更新  某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rcv_add($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE fty_smpl_ship SET 
							rcv_num    ='".$parm['rcv_num']."',
							rcv_o_date ='".$parm['rcv_o_date']."',
							rcv_o_user ='".$parm['rcv_o_user']."',
							status ='".$parm['status']."'
						  WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update SMPL_ORD data table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_rcv($id) {

		$sql = $this->sql;
		$fld= array();
					# 檢查是否有這個檔案存在
		$q_str = "SELECT sum(rcv_qty) as rcv_qty, smpl_num FROM fty_smpl_ship_det
							WHERE fty_smpl_ship_det.f_ship_id='".$id."'
							GROUP BY smpl_num";
							
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access to database, please contact system Administrator.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result) ) {
			$fld[] = $row;   	    
		}

		#####   更新資料庫內容
		for($i=0; $i<sizeof($fld); $i++)
		{
			$q_str = "UPDATE smpl_ord SET	rcv_qty='".$fld[$i]['rcv_qty'].
									"'	WHERE num='".$fld[$i]['smpl_num']."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  cannot update databse.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}		
		return true;
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, smpl_type=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_smpl_ship($dept, $cust) {

		$sql = $this->sql;

		$q_str="SELECT smpl_ord.num, (smpl_ord.rcv_qty - sal_qty_shp ) as qty FROM smpl_ord 
					  WHERE  (smpl_ord.case_close = '0000-00-00' OR smpl_ord.case_close >= '2009-01-01') AND smpl_ord.status >= 6 AND
					  			 dept = '$dept' AND cust = '$cust' AND rcv_qty > sal_qty_shp
					  GROUP BY smpl_ord.num";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
			$rtn[] = $row;
		}

		
		
		return $rtn;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, smpl_type=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_smpl_size($smpl) {

		$sql = $this->sql;

		$q_str="SELECT size_des.size FROM smpl_ord, size_des
					  WHERE  size_des.id = smpl_ord.size AND num = '$smpl'";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$row = $sql->fetch($q_result);
		$rtn = explode(',',$row['size']);

		
		
		return $rtn;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, smpl_type=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ship_size($smpl) {

		$sql = $this->sql;

		$q_str="SELECT fty_smpl_ship_det.size FROM fty_smpl_ship_det
					  WHERE  smpl_num = '$smpl'";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while($row = $sql->fetch($q_result))
		{
			$rtn[] = $row[0];
		}

		
		
		return $rtn;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 SMPL TYPE 的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl_ord ".$where_str;
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
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

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++







} // end class


?>