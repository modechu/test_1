<?php 

#++++++++++++++++++++++ SHIPPING  class ##### 開發布料  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入

#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#	->order_ship_out($order)				搜尋  指定訂單 之產出資料
#	->order_ship_delt($order)	刪除每月發貨量	 
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SHIPDOC {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
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
#	->add($parm)		加入新 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_main($cust,$fty) {
					
		$sql = $this->sql;

					# 加入資料庫
		$hend = date('ym');
		$num = '';
		$q_str = "SELECT inv_num FROM shipdoc WHERE fty ='".$fty."' AND inv_num like '".$hend."%' ORDER BY inv_num DESC";
	
		$q_result = $sql->query($q_str);		
		if (!$row = $sql->fetch($q_result)) {
			$num =  $hend."001-".$fty;
			 
		}					
		if($num == '')
		{
			$long = strlen($hend);
			$num = substr($row['inv_num'],$long,3);	//去掉表頭			
			
			settype($num, 'integer');
			$num=$num+1;
			settype($num, 'string');	
				
				
			if (strlen($num) == 1)	//在數字前補0到達四位數字
			{
				$num=$hend."00".$num."-".$fty;
			}elseif(strlen($num) == 2){
				$num=$hend."0".$num."-".$fty;			
			}else{
				$num=$hend.$num."-".$fty;
			}				
			
		}			
					
//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$cust."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	
					
					
		$q_str = "INSERT INTO shipdoc (ord_cust,cust_ver,fty,inv_num,open_user,	open_date) VALUES('".
									$cust."','".
									$cust_row['ver']."','".
									$fty."','".
									$num."','".
									$GLOBALS['SCACHE']['ADMIN']['login_id']."','".
									date('Y-m-d')."')";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id
		
		
		
		return $pdt_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord($id,$cust_po,$ord_num) {
					
		$sql = $this->sql;


		$q_str = "SELECT id
							FROM shipdoc_ord
							WHERE ship_id = '".$id."' AND ord_num = '".$ord_num."' AND cust_po = '".$cust_po."'"; 							
		$q_result = $sql->query($q_str);
		if ($row = $sql->fetch($q_result)) {			
			return $row['id'];    
		}


					# 加入資料庫
		$q_str = "INSERT INTO shipdoc_ord (ship_id,ord_num,cust_po) VALUES('".
									$id."','".
									$ord_num."','".									
									$cust_po."')";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id
		
		return $pdt_id;

	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_charge($id,$charge,$des) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO shipdoc_cost (ship_id,charge,chg_des) VALUES('".
									$id."','".
									$charge."','".									
									$des."')";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id
		
		return $pdt_id;

	} // end func		
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO shipdoc_qty (ship_id,	
																	 ord_num,
																	 ship_ord_id,
																	 cnt,
																	 ctn_mk,
																	 cnum,
																	 color,
																	 size_qty,
																	 size_box,
																	 nnw,
																	 nw,
																	 gw,
																	 qty,
																	 s_qty,
																	 s_cnum,
																	 ucc,
																	 ppk,
																	 fit,
																	 ctn_id,
																	 f_mk,
																	 ctn_l,
																	 ctn_w,																	 
																	 ctn_h) VALUES('".
									$parm['ship_id']."','".
									$parm['ord_num']."','".
									$parm['ship_ord_id']."','".
									$parm['ctn']."','".
									$parm['ctn_mk']."','".
									$parm['cnum']."','".
									$parm['color']."','".
									$parm['size_qty']."','".
									$parm['size_box']."','".
									$parm['nnw']."','".
									$parm['nw']."','".
									$parm['gw']."','".
									$parm['qty']."','".
									$parm['s_qty']."','".
									$parm['s_cnum']."','".
									$parm['ucc']."','".
									$parm['ppk']."','".
									$parm['fit']."','".
									$parm['ctn_id']."','".
									$parm['f_mk']."','".
									$parm['ctn_l']."','".
									$parm['ctn_w']."','".									
									$parm['ctn_h']."')";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id
		
		return $pdt_id;

	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	搜尋 月份 出口訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order($id='',$ord_num='') {

		$sql = $this->sql;

//取得訂單基本資料
	if($id)
	{
		$q_str = "SELECT s_order. *, size_des.size, cust.cust_init_name as cust_name 
							FROM s_order, size_des, cust
							WHERE s_order.size = size_des.id AND  
										s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver AND 
										s_order.id =".$id;
	}else{
		$q_str = "SELECT s_order. *, size_des.size, cust.cust_init_name as cust_name 
							FROM s_order, size_des, cust
							WHERE s_order.size = size_des.id AND  
										s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver AND 
										s_order.order_num = '".$ord_num."'";	
	}
	//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! can't find these record!");
			return false;    
		}
		$op['ord'] = $row;
		$op['size'] = explode(',',$row['size']);
		$cust_po = explode('|',$row['cust_po']);
		$op['cust_po'] = $GLOBALS['arry2']->select($cust_po,'','PHP_cust_po','select','');
		

		

//取得colorway/ size資料
		$op['assort'] = array();
		$color = array();
	if($id)
	{
		$q_str = "SELECT wiqty.* FROM s_order, wi, wiqty 
						  WHERE s_order.order_num = wi.wi_num AND wi.id = wiqty.wi_id AND s_order.id= '".$id."'";
	}else{
		$q_str = "SELECT wiqty.* FROM wi, wiqty 
						  WHERE  wi.id = wiqty.wi_id AND wi.wi_num= '".$ord_num."'";	
	}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			 
			$tmp[] = $row;
			 
		}
		
		for($i=0; $i<sizeof($tmp); $i++)
		{
			 if($tmp[$i]['ship_color'] == '')
			 {
					$q_str = "UPDATE wiqty SET ship_color='".$tmp[$i]['colorway']."' WHERE id='".$tmp[$i]['id']."'";
					$q_result = $sql->query($q_str);		
					$tmp[$i]['ship_color'] = 	 $tmp[$i]['colorway'];	
			 }
			 
			$tmp[$i]['colorway'] = $tmp[$i]['ship_color'];
			 
			 $tmp_qty = explode(',',$tmp[$i]['qty']);
			 $tmp[$i]['qty'] = $tmp_qty;

			 $ttl = 0;
			 for($j=0; $j<sizeof($tmp_qty); $j++) $ttl += $tmp_qty[$j];
			 $tmp[$i]['sum'] = $ttl;
			 $tmp_nnw = explode(',',$row['nnw']);
			 $tmp[$i]['nnw'] = $tmp_nnw;


			 $op['assort'][] = $tmp[$i];
			 $color[] = $tmp[$i]['colorway'];			
		}
		$op['color'] = $color;

//		$result;  // 資料錄 拋入 $op

		return $op;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	搜尋 月份 出口訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_by_add($parm,$inbox=0) {

		$sql = $this->sql;
	$op['row_span'] = 0;
	
	for($i=0; $i<sizeof($parm); $i++)
	{
//取得訂單基本資料

		$q_str = "SELECT s_order. *, size_des.size, cust.cust_init_name as cust_name, order_partial.id as p_id
							FROM s_order, size_des, cust, order_partial
							WHERE s_order.size = size_des.id AND  s_order.order_num = order_partial.ord_num AND
										s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver AND
										s_order.id =".$parm[$i]['id']." AND order_partial.id =".$parm[$i]['p_id'];
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);

		$op['ord'][$i] = $row;
		$op['size'][$i] = explode(',',$row['size']);
		$cust_po = explode('|',$row['cust_po']);
		$op['cust_po'][$i] = $cust_po;

		$op['assort'][$i] = array();
		$color = array();
//取得colorway/ size資料
		$q_str = "SELECT wiqty.* FROM s_order, wi, wiqty 
						  WHERE s_order.order_num = wi.wi_num AND wi.id = wiqty.wi_id AND 
						  			s_order.id= '".$parm[$i]['id']."' AND wiqty.p_id='".$parm[$i]['p_id']."'";

		$q_result = $sql->query($q_str);
		$tmp = array();
		while ($row = $sql->fetch($q_result)) {			 
			$tmp[] = $row;			 
		}
		$color = $color_f = $color_id =array();
		for($j=0; $j<sizeof($tmp); $j++)
		{
			 if($tmp[$j]['ship_color'] == '')
			 {
					$q_str = "UPDATE wiqty SET ship_color='".$tmp[$j]['colorway']."' WHERE id='".$tmp[$j]['id']."'";
					$q_result = $sql->query($q_str);		
					$tmp[$j]['ship_color'] = 	 $tmp[$j]['colorway'];	
			 }
			 
			$tmp[$j]['colorway'] = $tmp[$j]['ship_color'];

			 $tmp_qty = explode(',',$tmp[$j]['qty']);
			 $tmp[$j]['qty'] = $tmp_qty;

			 $ttl = 0;
			 for($k=0; $k<sizeof($tmp_qty); $k++) $ttl += $tmp_qty[$k];
			 $tmp[$j]['sum'] = $ttl;
			 $tmp_nnw = explode(',',$tmp[$j]['nnw']);
			 $tmp[$j]['nnw'] = $tmp_nnw;

			 if($tmp[$j]['item'])
			 {
			 	$tmp[$j]['colorway_f'] = $tmp[$j]['colorway']."-".$tmp[$j]['item'];
			 }else{
			 	$tmp[$j]['colorway_f'] = $tmp[$j]['colorway'];
			 }
			 $op['assort'][$i][] = $tmp[$j];
			 
			 $color[] = $tmp[$j]['colorway'];		
			 $color_f[] = $tmp[$j]['colorway_f'];		
			 $color_id[] = $tmp[$j]['id'];
		}
		if(!isset($color_id))$color_id[0]=0;
		if(!isset($color_f))$color_f[0]='';
		$op['color_f'][$i] = $color_id;
		$op['color'][$i] = $color_f;
		$op['color_size'][$i] = sizeof($color);
		
		
		$size_color = sizeof($color);
		$size_po = sizeof($cust_po);
		if($size_color == 0)$size_color = 1;
		if($size_po == 0)$size_po = 1;
		if($inbox == 1)
		{
			$op['row_span'] += ($size_color * $size_po)*2 +3;		
		}else{
			$op['row_span'] += ($size_color * $size_po) + 3;

		}
	}
	$op['row_span']++;
	//echo $q_str;

		
		

		

//		$result;  // 資料錄 拋入 $op

		return $op;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_search($mode=0,$ord_parm="",$limit_entries=0) {

		

		$sql = $this->sql;
		$argv = $_SESSION['add_sch'];   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.*, size_des.size_scale, cust.cust_init_name as cust_name,
											  order_partial.mks, order_partial.id as p_id, order_partial.p_etd as etd
								 FROM cust, order_partial, s_order LEFT JOIN size_des ON size_des.id = s_order.size ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
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



		$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");
		$srh->add_where_condition("s_order.order_num = order_partial.ord_num");
		$srh->add_where_condition("s_order.cust_po <> ''");
		for($i=0; $i<sizeof($ord_parm); $i++)
		{
			$srh->add_where_condition("s_order.order_num <> '".$ord_parm[$i]['num']."'");
		}

if ($mode==1){
		if ($str = $argv['PHP_fty'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_fty",$str," factory = [ $str ]. "); }
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_num",$str," order# as [ $str ]. "); }
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str," Customer =[ $str ]. "); }
}
		$srh->add_where_condition("s_order.status >= 8", '','','');
		$srh->add_where_condition("order_partial.pdt_status > 0", '','','');
		$srh->add_where_condition("s_order.m_status >= 2", '','','');
		$srh->add_where_condition("s_order.etd > '2007-01-01'");

		$result= $srh->send_query2();  
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['ord'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
// echo $srh->q_str;
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
#	->get($id)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id) {

		if (!$id) {
			$this->msg->add("Error ! please check record ID.");		    
			return false;
		}

		$sql = $this->sql;

		$q_str = "SELECT * FROM shipdoc WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! can't find these record!");
			return false;    
		}

	$po_user=$GLOBALS['user']->get(0,$row['submit_user']);
	$row['submit_id'] = $row['submit_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$row['cfm_user']);
	$row['cfm_id'] = $row['cfm_user'];
	if ($po_user['name'])$row['cfm_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$row['apv_user']);
	$row['apv_id'] = $row['apv_user'];
	if ($po_user['name'])$row['apv_user'] = $po_user['name'];

	$tmp = explode('|',$row['vendor']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['vendor_name'] = $tmp[0];
	$row['vendor_addr'] = $tmp[1];
	
	$csn = $GLOBALS['cust']->get_csge('', $row['consignee']);
	$row['consignee_name'] = $csn['f_name'];
	$row['consignee_addr'] = $csn['addr'];


		$fld['shp'] = $row;

		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 s_order.dept, shipdoc_ord.cust_po, wiqty.ship_color as color
							FROM shipdoc_qty, s_order, size_des, shipdoc_ord, wiqty
							WHERE shipdoc_ord.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										shipdoc_ord.id = shipdoc_qty.ship_ord_id AND wiqty.id = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
							ORDER BY shipdoc_qty.ctn_mk, shipdoc_qty.cnt, shipdoc_qty.f_mk, shipdoc_ord.ord_num, shipdoc_qty.id";
//echo $q_str;							
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = '';
		$tb_mk = 1;
		
		while ($row = $sql->fetch($q_result)) {
			
			$row['ord_mk'] = 0;
			if($row['f_mk'] == 1)$ord_num = '';
			$row['tb_mk'] = $tb_mk;
			$tb_mk = 0;
			$tmp = explode(',',$row['size_qty']);			
			$row['size_qty'] = $tmp;			
			$row['y_ln'] = sizeof($row['size_qty']);
			for($i=0; $i<sizeof($row['size_qty']); $i++) $row['yy'][$i] = $i;
			if($row['size_box'] <> '')
			{
				$tmp = explode(',',$row['size_box']);
				$row['size_box'] = $tmp;
				
				$row['ln_qty'] = 0;
				for($i=0; $i<sizeof($row['size_qty']); $i++)$row['ln_qty'] += ($row['size_qty'][$i]*$row['size_box'][$i]);
			}else{
				$row['ln_qty'] = 0;
				for($i=0; $i<sizeof($row['size_qty']); $i++)
				{
					$row['ln_qty'] += $row['size_qty'][$i];

			  }
			}

			$tmp = explode(',',$row['size']);
			$row['size'] = $tmp;
			$tmp = explode('-',$row['cnt']);
			$row['s_cnt'] = $tmp[0];
			$row['e_cnt'] = $tmp[1];	
			$row['sort'] = $row['s_cnt'];

//			if($ord_num == '')$ord_num = $row['ord_num'];
			if($ord_num != $row['ord_num'])
			{
				$ord_num = $row['ord_num'];
				$row['ord_mk'] = 1;
			}

			$fld['shp_det'][] = $row;

		}
		
		
		$row_span=$i_mk = 0;
		$o_mk = 0;
		for ($i=0; $i<sizeof($fld['shp_det']); $i++)
		{
			if($fld['shp_det'][$i]['ord_mk'] == 1 )
			{
				for($j=0; $j<sizeof($fld['shp_det'][$i]['size_qty']); $j++)
				{
					$fld['shp_det'][$i]['sub_qty'][$j] = 0;
				}
				$fld['shp_det'][$i]['ln_sub_qty'] = 0;
				$o_mk = $i;
			}
			for($j=0; $j<sizeof($fld['shp_det'][$i]['size_qty']); $j++)
			{
				$fld['shp_det'][$o_mk]['sub_qty'][$j] += $fld['shp_det'][$i]['size_qty'][$j];				
			}
			$fld['shp_det'][$o_mk]['ln_sub_qty'] +=$fld['shp_det'][$i]['ln_qty'];
			
			$fld['shp_det'][$i_mk]['row_span'] = 0;
			if($fld['shp_det'][$i]['f_mk'] == 1)
			{
				if($i > 0) 
				{
					$fld['shp_det'][$i_mk]['row_span'] = $row_span;
				}
				$row_span=0;
				$i_mk = $i;
			}
			$row_span++;
			if($fld['shp']['inner_box'] == 1)$row_span++;
			if($fld['shp_det'][$i]['ord_mk'] == 1  )$row_span +=2;
//			if($fld['shp_det'][$i]['tb_mk'] == 1 && $fld['shp_det'][$i]['ord_mk'] == 1 )$row_span ++; //第一行
		}
		$fld['shp_det'][$i_mk]['row_span'] = $row_span;
		
//計算每箱幾張訂單,每張訂單幾個項目	
		$o_mk = $x_ln = 0;
		$tmp_f_mk = $tmp_ord_mk = 0;
		for ($i=0; $i<sizeof($fld['shp_det']); $i++)
		{
			if ($fld['shp_det'][$i]['f_mk'] == 1)
			{
				if($i > 0) $fld['shp_det'][$tmp_f_mk]['ord_cnt'] = $o_mk;
				
				$tmp_f_mk = $i;
				$o_mk = 0;
			}
			
			if($fld['shp_det'][$i]['ord_mk'] == 1 )
			{
				 $fld['shp_det'][$i]['oo'] = $o_mk;
				 $o_mk++;
				 if($i > 0) $fld['shp_det'][$tmp_ord_mk]['x_ln'] = $x_ln;	
				 	
				 $x_ln =0;
				 $tmp_ord_mk = $i;
			}
			
			$fld['shp_det'][$i]['xx'] = $x_ln;
			$x_ln++;
		}
		$fld['shp_det'][$tmp_f_mk]['ord_cnt'] = $o_mk;
		$fld['shp_det'][$tmp_ord_mk]['x_ln'] = $x_ln;	
		
		$fld['shp_det'] = bubble_sort_s($fld['shp_det']);
		
		return $fld;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pack($id)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pack($id,$order_by='shipdoc_qty.ctn_mk, shipdoc_qty.cnt, shipdoc_qty.f_mk, shipdoc_ord.cust_po, shipdoc_qty.id',$st_mk=0) {

		if (!$id) {
			$this->msg->add("Error ! please check record ID.");		    
			return false;
		}

		$sql = $this->sql;
//shipping document基本資料 
		$q_str = "SELECT * FROM shipdoc WHERE id='$id' ";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! can't find these record!");
			return false;    
		}

	$po_user=$GLOBALS['user']->get(0,$row['submit_user']);
	$row['submit_id'] = $row['submit_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];
	$row['submit_mail'] = $po_user['email'];

	$po_user=$GLOBALS['user']->get(0,$row['cfm_user']);
	$row['cfm_id'] = $row['cfm_user'];
	if ($po_user['name'])$row['cfm_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$row['apv_user']);
	$row['apv_id'] = $row['apv_user'];
	if ($po_user['name'])$row['apv_user'] = $po_user['name'];
	
	$tmp = explode('|',$row['vendor']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['vendor_name'] = $tmp[0];
	$row['vendor_addr'] = $tmp[1];

	$csn = $GLOBALS['cust']->get_csge('', $row['consignee']);
	$row['consignee_name'] = $csn['f_name'];
	$row['consignee_addr'] = $csn['addr'];

	$csn = $GLOBALS['cust']->get('', $row['ord_cust']);
	$row['cust_name'] = $csn['cust_init_name'];
	$row['cust_addr'] = $csn['cntc_addr'];

	$tmp = explode('|',$row['ship_addr']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['ship_addr1'] = $tmp[0];
	$row['ship_addr2'] = $tmp[1];

	$tmp = explode('|',$row['bill_addr']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['bill_addr1'] = $tmp[0];
	$row['bill_addr2'] = $tmp[1];


		$fld['shp'] = $row;
		$fld['shp']['mark_no'] = $this->get_other_value($fld['shp']['id'],'Marks NO.');
		$fld['shp']['facility_value'] = $this->get_other_value($fld['shp']['id'],'Facility Type');
		$fld['shp']['mode'] = $this->get_other_value($fld['shp']['id'],'Mode');
		$fld['shp']['po_type'] = $this->get_other_value($fld['shp']['id'],'PO Type');
		$fld['shp']['method'] = $this->get_other_value($fld['shp']['id'],'Shipping method');
		$fld['shp']['conter_num'] = $this->get_other_value($fld['shp']['id'],'CNTR');		
		
//取得訂單style		
		$q_str = "SELECT s_order.style_num										 
							FROM shipdoc_qty, s_order
							WHERE shipdoc_qty.ord_num = s_order.order_num AND 
										ship_id ='$id' 
							GROUP BY s_order.style_num";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp']['ord_style'] = '';
		while ($row = $sql->fetch($q_result)) {	
			$fld['shp']['ord_style'] .= $row['style_num']."/";
		}
			$fld['shp']['ord_style'] = substr($fld['shp']['ord_style'],0,-1);


//取得訂單編號		
		$q_str = "SELECT s_order.order_num, shipdoc_qty.des, shipdoc_qty.content										 
							FROM shipdoc_qty, s_order
							WHERE shipdoc_qty.ord_num = s_order.order_num AND 
										ship_id ='$id' 
							GROUP BY order_num";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp']['ord_num'] = '';
		while ($row = $sql->fetch($q_result)) {	
			$fld['shp']['ord_num'] .= $row['order_num']."/";
			$fld['shp']['det_des'][] = $row['content'];
			$fld['shp']['det_des'][] = $row['des'];
		}
			$fld['shp']['ord_num'] = substr($fld['shp']['ord_num'],0,-1);


			
//packling list明細
/*
		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 s_order.uprice, s_order.dept, s_order.qty as ord_qty, 
										 s_order.content, s_order.ship_fob, wiqty.item, shipdoc_ord.case_num,
										 shipdoc_ord.mat_ft, shipdoc_ord.ship_ft, shipdoc_ord.belt_co,
										 shipdoc_ord.cust_po
							FROM shipdoc_qty, s_order, size_des, wi, wiqty, shipdoc_ord 
							WHERE shipdoc_ord.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										shipdoc_ord.id = shipdoc_qty.ship_ord_id AND
										wi.wi_num = s_order.order_num AND wi.id = wiqty.wi_id AND wiqty.colorway = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
							ORDER BY ".$order_by;
*/
		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 s_order.uprice, s_order.dept, s_order.qty as ord_qty, 
										 shipdoc_ord.cust_po, wiqty.ship_color as color, wiqty.item
							FROM shipdoc_qty, s_order, size_des, shipdoc_ord, wiqty
							WHERE shipdoc_ord.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										shipdoc_ord.id = shipdoc_qty.ship_ord_id AND wiqty.id = shipdoc_qty.color	AND									
										shipdoc_qty.ship_id ='$id' 
							ORDER BY ".$order_by;							
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = '';
		$size = '';
		$cnum = $qty = $nw =$nnw = $gw = $size_ct = $amt = $ctn = 0;
		$f_color = array();
		while ($row = $sql->fetch($q_result)) {
/*
			$where_str = ", wi WHERE wi.id = wiqty.wi_id AND wi.wi_num ='".$row['ord_num']."' AND wiqty.colorway = '".$row['color']."'";
			$wi_item = $GLOBALS['wiqty']->get_fields('item', $where_str);
			$row['item'] = (!isset($wi_item[0])) ? '' : $wi_item[0]['item'];
*/						
//			$tmp = explode('-',$row['color']); 
			$cc_mk = 0;
			for($cc=0; $cc<sizeof($f_color); $cc++)
			{
				if($f_color[$cc] == $row['color']) $cc_mk = 1;
			}
			if($cc_mk == 0) $f_color[] = $row['color'];
			
			$box_mk = 0;
			// packing list			
			$tmp = explode(',',$row['size_qty']);  // 數量
			$row['size_qty'] = $tmp;
			
			if($row['size_box'] <> '')
			{
				$tmp = explode(',',$row['size_box']); //inner box
				$row['size_box'] = $tmp;
				$box_mk = 1;
			
			}

			$row['size_csv'] = $row['size'];
			$tmp = explode(',',$row['size']);
			$row['size'] = $tmp;
			
			$tmp = explode('-',$row['cnt']);
			$row['s_cnt'] = $tmp[0];
			$row['e_cnt'] = $tmp[1];			
			$row['sort'] = $row['s_cnt'];

			if($size != $row['size_csv'])
			{
				$size = $row['size_csv'];
				$row['tb_mk'] = 1;
			}

			$fld['shp_det'][] = $row;

			$qty +=($row['qty'] * $row['cnum']);
			
/*
			$nw +=($row['nw'] * $row['cnum']);
			$nnw +=($row['nnw'] * $row['cnum']);
			$gw +=($row['gw'] * $row['cnum']);
*/
			$nw +=($row['nw'] * $row['cnum']);
			$nnw +=($row['nnw'] * $row['cnum']);
			$gw +=($row['gw'] * $row['cnum']);
			
			
			
			$cnum += $row['cnum'];

			$amt += ($row['qty'] * $row['cnum']) * $row['uprice'];
			
			$size_ct = sizeof($row['size']);
			
		}

		$fld['amt']['qty'] = $qty;
		$fld['amt']['nw'] = $nw;
		$fld['amt']['nnw'] = $nnw;
		$fld['amt']['gw'] = $gw;
		$fld['amt']['cnum'] = $cnum;
		$fld['amt']['eng_cnum'] = engilsh_math($fld['amt']['cnum'])." (".$fld['amt']['cnum'].") CARTONS ONLY.";

		$fld['amt']['span']=$size_ct+5+$box_mk;
		if($fld['shp']['bar_mk'] == 1)$fld['amt']['span']++;

		$this->update_field_main('ttl_qty',$fld['amt']['qty'],$id);
		$this->update_field_main('ttl_amt',$amt,$id);


//計算每箱項目數
		$ct_mk = 0; $i_mk = 0;
		for($i=0; $i<sizeof($fld['shp_det']); $i++)
		{
			if($fld['shp_det'][$i]['f_mk'] == 1)
			{
				$fld['shp_det'][$i]['row_span'] = 0;
				if($i > 0)$fld['shp_det'][$i_mk]['row_span'] = $ct_mk;
				$fld['shp_det'][$i]['ctn_nnw'] = 0;
				$fld['shp_det'][$i]['ctn_nw'] = 0;
				$fld['shp_det'][$i]['ctn_gw'] = 0;
				$ct_mk = 0;
				$i_mk = $i;
			}
			$ct_mk++;
			if($fld['shp']['inner_box'] == 1)$ct_mk++;
			if(!isset($fld['shp_det'][$i_mk]['ctn_nnw']))$fld['shp_det'][$i_mk]['ctn_nnw'] = 0;
			if(!isset($fld['shp_det'][$i_mk]['ctn_nw']))$fld['shp_det'][$i_mk]['ctn_nw'] = 0;
			if(!isset($fld['shp_det'][$i_mk]['ctn_gw']))$fld['shp_det'][$i_mk]['ctn_gw'] = 0;
			$fld['shp_det'][$i_mk]['ctn_nnw'] += $fld['shp_det'][$i]['nnw'];
			$fld['shp_det'][$i_mk]['ctn_nw'] += $fld['shp_det'][$i]['nw'];
			$fld['shp_det'][$i_mk]['ctn_gw'] += $fld['shp_det'][$i]['gw'];
		}
		$fld['shp_det'][$i_mk]['row_span'] = $ct_mk;




//packing list訂單數量匯總
		$q_str = "SELECT shipdoc_qty.*, shipdoc_qty.color as color_id, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 shipdoc_ord.cust_po, s_order.uprice, s_order.dept, s_order.qty as ord_qty,
										 wiqty.ship_color as color, wiqty.item, wiqty.colorway
							FROM shipdoc_qty, s_order, size_des, shipdoc_ord, wiqty
							WHERE shipdoc_ord.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										shipdoc_ord.id = shipdoc_qty.ship_ord_id AND wiqty.id = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
							ORDER BY size_des.size, shipdoc_ord.cust_po, shipdoc_qty.color, shipdoc_qty.ctn_id";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = '';
		$color = $size = $ord_id = '';
		$rowspan =  $tb_mk = $rowspan = 0;
		$x = $ord_mk = -1;
		while ($row = $sql->fetch($q_result)) {			
/*
			$where_str = ", wi WHERE wi.id = wiqty.wi_id AND wi.wi_num ='".$row['ord_num']."' AND wiqty.colorway = '".$row['color']."'";
			$wi_item = $GLOBALS['wiqty']->get_fields('item', $where_str);
			$row['item'] = (!isset($wi_item[0])) ? '' : $wi_item[0]['item'];
*/
			$box_mk = 0;
			if($row['ship_ord_id'] != $ord_id)
			{
				
				if($ord_id != '')
				{					
					$ord_mk++;
					$fld['break'][$ord_mk]['ord_mk'] = 1;
					$fld['break'][$ord_mk]['rowspan'] = $rowspan;
					$ord_mk = $x;
				} 
				$color = '';
				$rowspan=0;
				$ord_id = $row['ship_ord_id'];
				
			}
			$tmp = explode(',',$row['size_qty']);
			$row['size_qty'] = $tmp;

			if($row['size_box'] <> '')
			{
				$tmp = explode(',',$row['size_box']); //inner box
				$row['size_box'] = $tmp;
				$box_mk = 1;
			}

			$row['size_csv'] = $row['size'];
			$tmp = explode(',',$row['size']);
			$row['size'] = $tmp;

			if($size != $row['size_csv'])
			{

				if($size!= '')
				{
					$fld['break'][$x+1]['g_size_qty'] = $size_ary;
					$fld['break'][$x+1]['g_qty'] = $ttl_qty;
				}
				$size = $row['size_csv'];
				$tb_mk = 1;

				$size_ary = array();
				for($i=0; $i<sizeof($row['size_qty']); $i++)	$size_ary[]=0;
				$ttl_qty=0;
			}
			$color_ctn = $row['colorway'].$row['ctn_id'];
		
			// colour size breakdown
			if($color != $color_ctn)
			{
				
				$x++;
				for($i=0; $i<sizeof($row['size_qty']); $i++)
				{
					$fld['break'][$x]['size'][$i] = $row['size'][$i];
					if($box_mk == 0)$row['size_box'][$i] = 1;
					$fld['break'][$x]['qty'][$i] = $row['size_box'][$i] * $row['size_qty'][$i] * $row['s_cnum'];
					
					$size_ary[$i] +=$row['size_box'][$i] * $row['size_qty'][$i] * $row['s_cnum'];
					
				}
				$fld['break'][$x]['style_num'] =$row['style_num'];
				$fld['break'][$x]['item'] =$row['item'];
				$fld['break'][$x]['po_num'] =$row['cust_po'];
				$fld['break'][$x]['ctn_id'] =$row['ctn_id'];
				$fld['break'][$x]['ord_qty'] =$row['ord_qty'];
				$fld['break'][$x]['color'] =$row['color'];	
				$fld['break'][$x]['ttl_qty'] = $row['s_qty'] * $row['s_cnum'];
				$fld['break'][$x]['size_csv']=$row['size_csv'];
				$fld['break'][$x]['rowspan'] = 0;
				$fld['break'][$x]['ord_mk']=0;
				$fld['break'][$x]['tb_mk']=$tb_mk;	
				$fld['break'][$x]['size_ct']= sizeof($row['size']);
				$ttl_qty +=$row['s_qty'] * $row['s_cnum'];
				$color = $color_ctn;
				$rowspan++;									
			}else{
				for($i=0; $i<sizeof($row['size_qty']); $i++)
				{
					if($box_mk == 0)$row['size_box'][$i] = 1;
					$fld['break'][$x]['qty'][$i] += $row['size_box'][$i] *$row['size_qty'][$i] * $row['s_cnum'];
					$size_ary[$i] += $row['size_box'][$i] * $row['size_qty'][$i] * $row['s_cnum'];
				}				
				$fld['break'][$x]['ttl_qty'] += $row['s_qty'] * $row['s_cnum'];
				$ttl_qty +=$row['s_qty'] * $row['s_cnum'];
			}
			
			$tb_mk = 0;			
		}
		$fld['break'][0]['tb_mk'] = 2;
		$ord_mk++;
		$fld['break'][$ord_mk]['ord_mk'] = 1;
		$fld['break'][$ord_mk]['rowspan'] = $rowspan;
		$x = sizeof($fld['break']) - 1;
		$fld['break'][$x]['tb_mk'] += 3;
		$fld['break'][$x]['g_size_qty'] = $size_ary;
		$fld['break'][$x]['g_qty'] = $ttl_qty;

//		if($fld['break'][1]['tb_mk'] == 4)$fld['break'][0]['tb_mk'] = 4;
		
		$q_str = "SELECT * FROM shipdoc_log WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp_log'] = array();
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_log'][] = $row;
		}	
		
		$q_str = "SELECT * FROM shipdoc_file WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['done'] = array();
		while ($row = $sql->fetch($q_result)) {
			$fld['done'][] = $row;
		}			
		
		
		
		
		$fld['shp']['color'] = '';
		for($i=0; $i<sizeof($f_color); $i++)
		{
			$fld['shp']['color'] .= $f_color[$i]."/";
		}	
		$fld['shp']['color'] = substr($fld['shp']['color'],0,-1);
		
		
		$fld['shp_charge'] = array();
		$q_str = "SELECT * FROM shipdoc_cost WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_charge'][] = $row;
		}			
		
		if($st_mk == 0)$fld['shp_det'] = bubble_sort_s($fld['shp_det']);
		return $fld;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pack($id)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pl_ord($id) {
		$sql = $this->sql;

//取得訂單style		
		$q_str = "SELECT s_order.order_num as num, s_order.id, s_order.order_num as sort, wiqty.p_id
							FROM shipdoc_qty, s_order, wiqty
							WHERE shipdoc_qty.ord_num = s_order.order_num AND wiqty.id = shipdoc_qty.color AND
										ship_id ='$id' 
							GROUP BY s_order.order_num, wiqty.p_id
							ORDER BY s_order.order_num";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld = array();
		while ($row = $sql->fetch($q_result)) {	
			$fld[]= $row;
		}		
		return $fld;
	} // end func	
	
	
/*	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pack_byord($id)	抓出指定記錄內資料 RETURN $row[]
#  ORDER BY s_order.cust_po, s_order.style_num, shipdoc_qty.color
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pack_byord($id) {

		if (!$id) {
			$this->msg->add("Error ! please check record ID.");		    
			return false;
		}

		$sql = $this->sql;

		$q_str = "SELECT * FROM shipdoc WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! can't find these record!");
			return false;    
		}

	$po_user=$GLOBALS['user']->get(0,$row['submit_user']);
	$row['submit_id'] = $row['submit_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$row['cfm_user']);
	$row['cfm_id'] = $row['cfm_user'];
	if ($po_user['name'])$row['cfm_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$row['apv_user']);
	$row['apv_id'] = $row['apv_user'];
	if ($po_user['name'])$row['apv_user'] = $po_user['name'];
	
	$tmp = explode('|',$row['vendor']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['vendor_name'] = $tmp[0];
	$row['vendor_addr'] = $tmp[1];

	$csn = $GLOBALS['cust']->get_csge('', $row['consignee']);
	$row['consignee_name'] = $csn['f_name'];
	$row['consignee_addr'] = $csn['addr'];

	$csn = $GLOBALS['cust']->get('', $row['ord_cust']);
	$row['cust_name'] = $csn['cust_init_name'];
	$row['cust_addr'] = $csn['cntc_addr'];

		$fld['shp'] = $row;
		
		$q_str = "SELECT s_order.style_num										 
							FROM shipdoc_qty, s_order
							WHERE shipdoc_qty.ord_num = s_order.order_num AND 
										ship_id ='$id' 
							GROUP BY s_order.style_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp']['ord_style'] = '';
		while ($row = $sql->fetch($q_result)) {	
			$fld['shp']['ord_style'] .= $row['style_num']."/";
		}
			$fld['shp']['ord_style'] = substr($fld['shp']['ord_style'],0,-1);


			

		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 s_order.cust_po, s_order.uprice, s_order.dept, s_order.qty as ord_qty,
										 s_order.belt_co, s_order.mat_ft, s_order.ship_ft
							FROM shipdoc_qty, s_order, size_des 
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										ship_id ='$id' 
							ORDER BY s_order.cust_po, s_order.style_num, shipdoc_qty.color";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = '';
		$size = '';
		$qty = $nw =$nnw = $gw = $size_ct = $amt = $ctn = $cnum = 0;
		$f_color = array();
		while ($row = $sql->fetch($q_result)) {			
			$tmp = explode('-',$row['color']); 
			$cc_mk = 0;
			for($cc=0; $cc<sizeof($f_color); $cc++)
			{
				if($f_color[$cc] == $tmp[0]) $cc_mk = 1;
			}
			if($cc_mk == 0) $f_color[] = $tmp[0];
			
			$box_mk = 0;
			// packing list			
			$tmp = explode(',',$row['size_qty']);  // 數量
			$row['size_qty'] = $tmp;
			
			if($row['size_box'] <> '')
			{
				$tmp = explode(',',$row['size_box']); //inner box
				$row['size_box'] = $tmp;
				$box_mk = 1;
			
			}

			$row['size_csv'] = $row['size'];
			$tmp = explode(',',$row['size']);
			$row['size'] = $tmp;
			
			$tmp = explode('-',$row['cnt']);
			$row['s_cnt'] = $tmp[0];
			$row['e_cnt'] = $tmp[1];			
//			$row['cnum'] = $tmp[1] - $tmp[0] + 1;

			if($size != $row['size_csv'])
			{
				$size = $row['size_csv'];
				$row['tb_mk'] = 1;
			}

			$fld['shp_det'][] = $row;
			$qty +=($row['qty'] * $row['cnum']);

			$nw +=($row['nw'] * $row['cnum']);
			$nnw +=($row['nnw'] * $row['cnum']);
			$gw +=($row['gw'] * $row['cnum']);

			$nw +=($row['nw'] );
			$nnw +=($row['nnw'] );
			$gw +=($row['gw'] );
			$cnum += $row['cnum'];

			$amt += ($row['qty'] * $row['cnum']) * $row['uprice'];
			
			$size_ct = sizeof($row['size']);
			
		}

		$fld['amt']['qty'] = $qty;
		$fld['amt']['nw'] = $nw;
		$fld['amt']['nnw'] = $nnw;
		$fld['amt']['gw'] = $gw;
		$fld['amt']['cnum'] = $cnum;
		$fld['amt']['span']=$size_ct+4+$box_mk;

		$this->update_field_main('ttl_qty',$fld['amt']['qty'],$id);
		$this->update_field_main('ttl_amt',$amt,$id);


		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 s_order.cust_po, s_order.uprice, s_order.dept, s_order.qty as ord_qty
							FROM shipdoc_qty, s_order, size_des 
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										ship_id ='$id' 
							ORDER BY size_des.size, s_order.style_num, shipdoc_qty.color";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = '';
		$color = $size = $ord_id = '';
		$rowspan =  $tb_mk = $rowspan = 0;
		$x = $ord_mk = -1;
		while ($row = $sql->fetch($q_result)) {			
			$box_mk = 0;
			if($row['ord_id'] != $ord_id)
			{
				
				if($ord_id != '')
				{					
					$ord_mk++;
					$fld['break'][$ord_mk]['ord_mk'] = 1;
					$fld['break'][$ord_mk]['rowspan'] = $rowspan;
					$ord_mk = $x;
				} 
				$color = '';
				$rowspan=0;
				$ord_id = $row['ord_id'];
				
			}
			$tmp = explode(',',$row['size_qty']);
			$row['size_qty'] = $tmp;

			if($row['size_box'] <> '')
			{
				$tmp = explode(',',$row['size_box']); //inner box
				$row['size_box'] = $tmp;
				$box_mk = 1;
			}

			$row['size_csv'] = $row['size'];
			$tmp = explode(',',$row['size']);
			$row['size'] = $tmp;

			if($size != $row['size_csv'])
			{

				if($size!= '')
				{
					$fld['break'][$x+1]['g_size_qty'] = $size_ary;
					$fld['break'][$x+1]['g_qty'] = $ttl_qty;
				}
				$size = $row['size_csv'];
				$tb_mk = 1;
				$size_ary = array();
				for($i=0; $i<sizeof($row['size_qty']); $i++)	$size_ary[]=0;
				$ttl_qty=0;
			}
		
			// colour size breakdown
			if($color != $row['color'])
			{
				
				$x++;
				for($i=0; $i<sizeof($row['size_qty']); $i++)
				{
					$fld['break'][$x]['size'][$i] = $row['size'][$i];
					if($box_mk == 0)$row['size_box'][$i] = 1;
					$fld['break'][$x]['qty'][$i] = $row['size_box'][$i] * $row['size_qty'][$i] * $row['cnum'];
					$size_ary[$i] +=$row['size_box'][$i] * $row['size_qty'][$i] * $row['cnum'];
					
				}
				$fld['break'][$x]['style_num'] =$row['style_num'];
				$fld['break'][$x]['po_num'] =$row['cust_po'];
				$fld['break'][$x]['ord_qty'] =$row['ord_qty'];
				$fld['break'][$x]['color'] =$row['color'];	
				$fld['break'][$x]['ttl_qty'] = $row['qty'] * $row['cnum'];
				$fld['break'][$x]['size_csv']=$row['size_csv'];
				$fld['break'][$x]['rowspan'] = 0;
				$fld['break'][$x]['ord_mk']=0;
				$fld['break'][$x]['tb_mk']=$tb_mk;	
				$fld['break'][$x]['size_ct']= sizeof($row['size']);
				$ttl_qty +=$row['qty'] * $row['cnum'];
				$color = $row['color'];
				$rowspan++;									
			}else{
				for($i=0; $i<sizeof($row['size_qty']); $i++)
				{
					if($box_mk == 0)$row['size_box'][$i] = 1;
					$fld['break'][$x]['qty'][$i] += $row['size_box'][$i] *$row['size_qty'][$i] * $row['cnum'];
					$size_ary[$i] += $row['size_box'][$i] * $row['size_qty'][$i] * $row['cnum'];
				}				
				$fld['break'][$x]['ttl_qty'] += $row['qty'] * $row['cnum'];
				$ttl_qty +=$row['qty'] * $row['cnum'];
			}
			
			$tb_mk = 0;			
		}
		$fld['break'][0]['tb_mk'] = 2;
		$ord_mk++;
		$fld['break'][$ord_mk]['ord_mk'] = 1;
		$fld['break'][$ord_mk]['rowspan'] = $rowspan;
		$x = sizeof($fld['break']) - 1;
		$fld['break'][$x]['tb_mk'] += 3;
		$fld['break'][$x]['g_size_qty'] = $size_ary;
		$fld['break'][$x]['g_qty'] = $ttl_qty;


		$q_str = "SELECT * FROM shipdoc_log WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp_log'] = array();
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_log'][] = $row;
		}	
		$fld['shp']['color'] = '';
		for($i=0; $i<sizeof($f_color); $i++)
		{
			$fld['shp']['color'] .= $f_color[$i]."/";
		}	
		$fld['shp']['color'] = substr($fld['shp']['color'],0,-1);
		return $fld;
	} // end func		
*/	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_inv($id,$sort='shipdoc_ord.cust_po, shipdoc_qty.color, id') {

		if (!$id) {
			$this->msg->add("Error ! please check record ID.");		    
			return false;
		}

		$sql = $this->sql;

		$q_str = "SELECT * FROM shipdoc WHERE id='$id' ";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! can't find these record!");
			return false;    
		}
		$tmp = explode('|',$row['ship_addr']);
		if (!isset($tmp[1]))$tmp[1]='';
		$row['ship_addr1'] = $tmp[0];
		$row['ship_addr2'] = $tmp[1];

		$tmp = explode('|',$row['bill_addr']);
		if (!isset($tmp[1]))$tmp[1]='';
		$row['bill_addr1'] = $tmp[0];
		$row['bill_addr2'] = $tmp[1];

	$tmp = explode('|',$row['vendor']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['vendor_name'] = $tmp[0];
	$row['vendor_addr'] = $tmp[1];


	$csn = $GLOBALS['cust']->get_csge('', $row['consignee']);
	$row['consignee_name'] = $csn['f_name'];
	$row['consignee_addr'] = $csn['addr'];

	$csn = $GLOBALS['cust']->get('', $row['ord_cust']);
	$row['cust_name'] = $csn['cust_f_name'];
	$row['cust_addr'] = $csn['cntc_addr'];


	$po_user=$GLOBALS['user']->get(0,$row['inv_sub_user']);
	$row['submit_id'] = $row['inv_sub_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];
	$row['submit_mail'] = $po_user['email'];
	
	$po_user=$GLOBALS['user']->get(0,$row['inv_cfm_user']);
	$row['cfm_id'] = $row['inv_cfm_user'];
	if ($po_user['name'])$row['cfm_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$row['apv_user']);
	$row['apv_id'] = $row['apv_user'];
	if ($po_user['name'])$row['apv_user'] = $po_user['name'];
	
	if($row['conter']) $row['conter_det'] = $GLOBALS['CONTAINER'][$row['conter']];
	if($row['constor']) $row['constor_det'] = $GLOBALS['CONTAINER'][$row['constor']];

	$row['method_value'] = $row['mode_value'] = $row['facility_value'] = $row['po_type_value'] = '';
//	if($row['po_type']) $row['po_type_value'] = $GLOBALS['SHIP_PO_TYPE'][($row['po_type']-1)];
//	if($row['facility']) $row['facility_value'] = $GLOBALS['SHIP_FACILLITY'][($row['facility']-1)];
//	if($row['mode']) $row['mode_value'] = $GLOBALS['SHIP_MODE'][($row['mode']-1)];
//	if($row['method']) $row['method_value'] = $GLOBALS['SHIP_METHOD'][($row['method']-1)];
	
		
		$fld['shp'] = $row;
		$fld['shp']['mark_no'] = $this->get_other_value($fld['shp']['id'],'Marks NO.');
		$fld['shp']['facility_value'] = $this->get_other_value($fld['shp']['id'],'Facility Type');
		$fld['shp']['mode'] = $this->get_other_value($fld['shp']['id'],'Mode');
		$fld['shp']['po_type'] = $this->get_other_value($fld['shp']['id'],'PO Type');
		$fld['shp']['freight_date'] = $this->get_other_value($fld['shp']['id'],'Freight Receipt Date');
		$fld['shp']['dischg_date'] = $this->get_other_value($fld['shp']['id'],'Est Discharge Date');
		$fld['shp']['fty_ship_date'] = $this->get_other_value($fld['shp']['id'],'FTY Ship Date');
		$fld['shp']['lod_date'] = $this->get_other_value($fld['shp']['id'],'Est Load Date');
		$fld['shp']['conter_num'] = $this->get_other_value($fld['shp']['id'],'Container Number');
		$fld['shp']['voyage'] = $this->get_other_value($fld['shp']['id'],'Voyage/Trip');
		$fld['shp']['freight_rcp'] = $this->get_other_value($fld['shp']['id'],'Freight Receipt');		
		$fld['shp']['facility_code'] = $this->get_other_value($fld['shp']['id'],'Facility ship to(4-Digit Code)');
		$fld['shp']['hbl'] = $this->get_other_value($fld['shp']['id'],'HBL# / HAWB#');
		$fld['shp']['facility_name'] = $this->get_other_value($fld['shp']['id'],'Facility ship to(Name)');
		$fld['shp']['method'] = $this->get_other_value($fld['shp']['id'],'Shipping method');


		
//取得訂單編號		
		$q_str = "SELECT s_order.order_num										 
							FROM shipdoc_qty, s_order
							WHERE shipdoc_qty.ord_num = s_order.order_num AND 
										ship_id ='$id' 
							GROUP BY order_num";
// echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp']['ord_num'] = '';
		while ($row = $sql->fetch($q_result)) {	
			$fld['shp']['ord_num'] .= $row['order_num']."/";
		}
			$fld['shp']['ord_num'] = substr($fld['shp']['ord_num'],0,-1);
		
	$fld['shp']['oth_field'] = array();
//SHIPPING DOCUMENT的其他欄位		
		$q_str = "SELECT * FROM shipdoc_other_field  WHERE ship_id='$id' ";
		$q_result = $sql->query($q_str);
		while ($row = $sql->fetch($q_result)) {
			$fld['shp']['oth_field'][] = $row;
		}			

		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num as ord_style_num, 
										 s_order.ship_quota, s_order.quota_unit, 
										 s_order.uprice, style_type.des as style_des, s_order.dept,
										 shipdoc_ord.cust_po,	wiqty.ship_color as color, wiqty.item
							FROM shipdoc_qty, s_order, size_des, style_type, shipdoc_ord, wiqty
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										s_order.style = style_type.style_type AND	shipdoc_ord.id = shipdoc_qty.ship_ord_id AND
										 wiqty.id = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
							ORDER BY ".$sort;		
// echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp = explode(' ',$row['style_des']);
			$row['style_des'] = $tmp[0];

			$fld['shp_multi'][] = $row;
		}

/*
		$q_str = "SELECT shipdoc_qty.*, shipdoc_qty.s_qty as qty, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 s_order.cust_po, s_order.ship_quota, s_order.content, s_order.quota_unit, 
										 s_order.des, s_order.uprice, style_type.des as style_des, s_order.dept,
										 s_order.ship_fob, s_order.hts_cat, s_order.hanger, s_order.belt, s_order.belt_co,
										 s_order.mat_ft, s_order.ship_ft, wiqty.item, s_order.compt, s_order.case_num
							FROM shipdoc_qty, s_order, size_des, style_type, wiqty, wi
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										s_order.style = style_type.style_type AND wiqty.colorway = shipdoc_qty.color AND
										wiqty.wi_id = wi.id AND wi.wi_num = s_order.order_num AND
										ship_id ='$id' 
							ORDER BY ".$sort;
			
		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num, 
										 s_order.ship_quota, s_order.content, s_order.quota_unit, 
										 s_order.des, s_order.uprice, style_type.des as style_des, s_order.dept,
										 s_order.ship_fob, shipdoc_ord.compt,	shipdoc_ord.case_num,	shipdoc_ord.cust_po,								   
										 shipdoc_ord.hts_cat, shipdoc_ord.hanger, shipdoc_ord.belt, shipdoc_ord.belt_co,
										 shipdoc_ord.mat_ft, shipdoc_ord.ship_ft, wiqty.ship_color as color, wiqty.item
							FROM shipdoc_qty, s_order, size_des, style_type, shipdoc_ord, wiqty
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										s_order.style = style_type.style_type AND	shipdoc_ord.id = shipdoc_qty.ship_ord_id AND
										 wiqty.id = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
							ORDER BY ".$sort;				
*/							
		$q_str = "SELECT shipdoc_qty.id, shipdoc_qty.ship_id, shipdoc_qty.ship_ord_id, 
										 shipdoc_qty.ord_num, shipdoc_qty.style,
										 shipdoc_qty.color, shipdoc_qty.ship_fob, shipdoc_qty.hts_cat, shipdoc_qty.des,
										 shipdoc_qty.content, shipdoc_qty.style_num, sum(shipdoc_qty.nnw * shipdoc_qty.cnum) as nnw,
										 sum(shipdoc_qty.nw * shipdoc_qty.cnum) as nw, sum(shipdoc_qty.gw * shipdoc_qty.cnum) as gw,
										 sum(shipdoc_qty.cnum) as cnums, sum(shipdoc_qty.s_cnum * shipdoc_qty.s_qty) as bk_qty,
										 sum(shipdoc_qty.s_cnum * shipdoc_qty.s_qty * shipdoc_qty.ship_fob) as amount,
										 size_des.size, s_order.id as ord_id, s_order.style_num as ord_style_num, 
										 s_order.ship_quota, s_order.quota_unit, 
										 s_order.uprice, style_type.des as style_des, s_order.dept,
										 shipdoc_ord.cust_po,	wiqty.ship_color as color, wiqty.item
							FROM shipdoc_qty, s_order, size_des, style_type, shipdoc_ord, wiqty
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										s_order.style = style_type.style_type AND	shipdoc_ord.id = shipdoc_qty.ship_ord_id AND
										 wiqty.id = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
							GROUP BY shipdoc_qty.ship_ord_id, shipdoc_qty.color, shipdoc_qty.ship_fob,
											 shipdoc_qty.style_num,	shipdoc_qty.content, shipdoc_qty.des, shipdoc_qty.hts_cat
							ORDER BY ".$sort;		
// echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = '';
		$color='||';
		$bk_qty=0;
		$i= 0;
		$i_mk=0;
		$ord_qty = 0;
		$fld['amt']['qty'] = $fld['amt']['amount'] =  $fld['amt']['ctn'] = 0;
		$fld['pack']['ctn'] = $fld['pack']['nnw'] = $fld['pack']['nw'] = $fld['pack']['gw'] =0;
		while ($row = $sql->fetch($q_result)) {
						
			$box_mk = 0;			
			$fld['pack']['ctn'] += $row['cnums'];
			$fld['pack']['nnw'] += $row['nnw'];
			$fld['pack']['nw']  += $row['nw'];
			$fld['pack']['gw']  += $row['gw'];

			
//把color後面的單位取消			
		  if($fld['shp']['ord_cust'] =='PH' || $fld['shp']['ord_cust'] =='MN')
		  {
				$tmp = explode('-',$row['color']);
				if(is_numeric($tmp[0]))
				{
					$row['color'] = $tmp[1];
				}else{
					$row['color'] = $tmp[0];
				}
			}

			
			$tmp = explode(',',$row['size']);
			$row['size'] = $tmp;
			$ord_qty += $row['bk_qty'];
			$tmp = explode(' ',$row['style_des']);
			$row['style_des'] = $tmp[0];
			
			if($ord_num != $row['ship_ord_id'])
			{
				$ord_num = $row['ship_ord_id'];
				$row['tb_mk'] = 1;
				$color='||';		
				$row['span']=1;
				$i_mk = $i;				
			}else{
				$fld['shp_det'][$i_mk]['span']++;
			}
			$fld['shp_det'][$i] = $row;
			
			$fld['amt']['qty'] += $row['bk_qty'];
			$fld['amt']['amount'] += $row['amount'];	
			$i++;		
		}		
		
		$fld['amt']['eng_amount'] = engilsh_math($fld['amt']['amount'])." ONLY";		
		$this->update_field_main('ttl_qty',$fld['amt']['qty'],$id);
		$this->update_field_main('ttl_amt',$fld['amt']['amount'],$id);
		
		$i_mk = 0;
		$quota = 0;
		$qty = 0;
		$j = 1;
		$ord_num = '';
		$unit_group = $GLOBALS['UNIT_GROUP']['AP2'];
		for($i=0; $i<sizeof($fld['shp_det']); $i++)
		{
// 原shipdoc_ord欄位			
			$fld['shp_det'][$i]['compt'] = $this->get_other_ord_value($fld['shp_det'][$i]['ship_ord_id'],'Component');
			$fld['shp_det'][$i]['mat_ft'] = $this->get_other_ord_value($fld['shp_det'][$i]['ship_ord_id'],'Material');
			$fld['shp_det'][$i]['ship_ft'] = $this->get_other_ord_value($fld['shp_det'][$i]['ship_ord_id'],'Ship');
			$fld['shp_det'][$i]['belt_co'] = $this->get_other_ord_value($fld['shp_det'][$i]['ship_ord_id'],'Belt CO');
			$fld['shp_det'][$i]['hanger'] = $this->get_other_ord_value($fld['shp_det'][$i]['ship_ord_id'],'Hanger Unit Price');
			$fld['shp_det'][$i]['belt'] = $this->get_other_ord_value($fld['shp_det'][$i]['ship_ord_id'],'Belt Unit Price');
			$fld['shp_det'][$i]['case_num'] = $this->get_other_ord_value($fld['shp_det'][$i]['ship_ord_id'],'CASE');
			if($ord_num != $fld['shp_det'][$i]['ship_ord_id'])
			{
				if($i > 0)			
				{
				$fld['shp_det'][$i_mk]['f_qty'] = $qty;
				$qty = change_unit_qty('pc',$fld['shp_det'][$i_mk]['quota_unit'],$qty);
				$fld['shp_det'][$i_mk]['quota_amt'] = $qty * $fld['shp_det'][$i_mk]['ship_quota'];
				$fld['shp_det'][$i_mk]['unit_select'] = $GLOBALS['arry2']->select($unit_group,$fld['shp_det'][$i_mk]['quota_unit'],'PHP_quota_unit['.$fld['shp_det'][$i_mk]['ord_num'].']','select'); 
				 
			//	$fld['shp_det'][$i_mk]['span'] = $j;		
				}
				$qty = 0;
				$i_mk = $i;
				$j=1;

				$ord_num = $fld['shp_det'][$i]['cust_po'];
			}else{
				if($i > 0)$j++;
			}
				$qty += $fld['shp_det'][$i]['bk_qty'];
				
			
		}
		$fld['shp_det'][$i_mk]['f_qty'] = $qty;
		$qty = change_unit_qty('pc',$fld['shp_det'][$i_mk]['quota_unit'],$qty);
		$fld['shp_det'][$i_mk]['quota_amt'] = $qty * $fld['shp_det'][$i_mk]['ship_quota'];
		$fld['shp_det'][$i_mk]['unit_select'] = $GLOBALS['arry2']->select($unit_group,$fld['shp_det'][$i_mk]['quota_unit'],'PHP_quota_unit['.$fld['shp_det'][$i_mk]['ord_num'].']','select'); 

//		$fld['shp_det'][$i_mk]['span'] = $j;
		
		for($i=0; $i<sizeof($fld['shp_det']); $i++)
		{
			$q_str = "SELECT * FROM shipdoc_other_ord_field WHERE ship_ord_id='".$fld['shp_det'][$i]['ship_ord_id']."'";
			$q_result = $sql->query($q_str);
			while ($row = $sql->fetch($q_result)) {
				$fld['shp_det'][$i]['oth_field'][]=$row;
			}
		}
		
		
		
		$q_str = "SELECT * FROM shipdoc_log WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp_log'] = array();
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_log'][] = $row;
		}		
		

		$q_str = "SELECT * FROM shipdoc_file WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['done'] = array();
		while ($row = $sql->fetch($q_result)) {
			$fld['done'][] = $row;
		}			

		$fld['shp_charge'] = array();
		$q_str = "SELECT * FROM shipdoc_cost WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_charge'][] = $row;
		}				
		
		return $fld;
	} // end func	
	
/*	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_inv($id,$sort='shipdoc_ord.cust_po, shipdoc_qty.color, id') {

		if (!$id) {
			$this->msg->add("Error ! please check record ID.");		    
			return false;
		}

		$sql = $this->sql;

		$q_str = "SELECT * FROM shipdoc WHERE id='$id' ";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! can't find these record!");
			return false;    
		}
		$tmp = explode('|',$row['ship_addr']);
		if (!isset($tmp[1]))$tmp[1]='';
		$row['ship_addr1'] = $tmp[0];
		$row['ship_addr2'] = $tmp[1];

		$tmp = explode('|',$row['bill_addr']);
		if (!isset($tmp[1]))$tmp[1]='';
		$row['bill_addr1'] = $tmp[0];
		$row['bill_addr2'] = $tmp[1];

	$tmp = explode('|',$row['vendor']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['vendor_name'] = $tmp[0];
	$row['vendor_addr'] = $tmp[1];


	$csn = $GLOBALS['cust']->get_csge('', $row['consignee']);
	$row['consignee_name'] = $csn['f_name'];
	$row['consignee_addr'] = $csn['addr'];

	$csn = $GLOBALS['cust']->get('', $row['ord_cust']);
	$row['cust_name'] = $csn['cust_f_name'];
	$row['cust_addr'] = $csn['cntc_addr'];


	$po_user=$GLOBALS['user']->get(0,$row['inv_sub_user']);
	$row['submit_id'] = $row['inv_sub_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];
	$row['submit_mail'] = $po_user['email'];
	
	$po_user=$GLOBALS['user']->get(0,$row['inv_cfm_user']);
	$row['cfm_id'] = $row['inv_cfm_user'];
	if ($po_user['name'])$row['cfm_user'] = $po_user['name'];

	$po_user=$GLOBALS['user']->get(0,$row['apv_user']);
	$row['apv_id'] = $row['apv_user'];
	if ($po_user['name'])$row['apv_user'] = $po_user['name'];
	
	if($row['conter']) $row['conter_det'] = $GLOBALS['CONTAINER'][$row['conter']];
	if($row['constor']) $row['constor_det'] = $GLOBALS['CONTAINER'][$row['constor']];

	$row['method_value'] = $row['mode_value'] = $row['facility_value'] = $row['po_type_value'] = '';
	if($row['po_type']) $row['po_type_value'] = $GLOBALS['SHIP_PO_TYPE'][($row['po_type']-1)];
	if($row['facility']) $row['facility_value'] = $GLOBALS['SHIP_FACILLITY'][($row['facility']-1)];
	if($row['mode']) $row['mode_value'] = $GLOBALS['SHIP_MODE'][($row['mode']-1)];
	if($row['method']) $row['method_value'] = $GLOBALS['SHIP_METHOD'][($row['method']-1)];
	
		
		$fld['shp'] = $row;



		
//取得訂單編號		
		$q_str = "SELECT s_order.order_num										 
							FROM shipdoc_qty, s_order
							WHERE shipdoc_qty.ord_num = s_order.order_num AND 
										ship_id ='$id' 
							GROUP BY order_num";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp']['ord_num'] = '';
		while ($row = $sql->fetch($q_result)) {	
			$fld['shp']['ord_num'] .= $row['order_num']."/";
		}
			$fld['shp']['ord_num'] = substr($fld['shp']['ord_num'],0,-1);
		
	$fld['shp']['oth_field'] = array();
//SHIPPING DOCUMENT的其他欄位		
		$q_str = "SELECT * FROM shipdoc_other_field  WHERE ship_id='$id' ";
		$q_result = $sql->query($q_str);
		while ($row = $sql->fetch($q_result)) {
			$fld['shp']['oth_field'][] = $row;
		}			
							
		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num as ord_style_num, 
										 s_order.ship_quota, s_order.quota_unit, 
										 s_order.uprice, style_type.des as style_des, s_order.dept,
										 shipdoc_ord.cust_po,	wiqty.ship_color as color, wiqty.item
							FROM shipdoc_qty, s_order, size_des, style_type, shipdoc_ord, wiqty
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										s_order.style = style_type.style_type AND	shipdoc_ord.id = shipdoc_qty.ship_ord_id AND
										 wiqty.id = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
							ORDER BY ".$sort;		
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_multi'][] = $row;
		}
		
		
		
		$q_str = "SELECT shipdoc_qty.*, size_des.size, s_order.id as ord_id, s_order.style_num as ord_style_num, 
										 s_order.ship_quota, s_order.quota_unit, 
										 s_order.uprice, style_type.des as style_des, s_order.dept,
										 shipdoc_ord.cust_po,	wiqty.ship_color as color, wiqty.item
							FROM shipdoc_qty, s_order, size_des, style_type, shipdoc_ord, wiqty
							WHERE shipdoc_qty.ord_num = s_order.order_num AND s_order.size = size_des.id AND 
										s_order.style = style_type.style_type AND	shipdoc_ord.id = shipdoc_qty.ship_ord_id AND
										 wiqty.id = shipdoc_qty.color AND
										shipdoc_qty.ship_id ='$id' 
																	
							ORDER BY ".$sort;		
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$ord_num = '';
		$color='||';
		$bk_qty=0;
		$i= -1;
		$i_mk=0;
		$ord_qty = 0;
		$fld['amt']['qty'] = $fld['amt']['amount'] =  $fld['amt']['ctn'] = 0;
		$fld['pack']['ctn'] = $fld['pack']['nnw'] = $fld['pack']['nw'] = $fld['pack']['gw'] =0;
		while ($row = $sql->fetch($q_result)) {				
			$box_mk = 0;
			$ctn_tmp = explode('-',$row['cnt']);
			$fld['pack']['ctn'] += $row['cnum'];
			$fld['pack']['nnw'] += ($row['nnw'] * $row['cnum']);
			$fld['pack']['nw']  += ($row['nw'] * $row['cnum']);
			$fld['pack']['gw']  += ($row['gw'] * $row['cnum']);

			
//把color後面的單位取消			
		  if($fld['shp']['ord_cust'] =='PH' || $fld['shp']['ord_cust'] =='MN')
		  {
				$tmp = explode('-',$row['color']);
				if(is_numeric($tmp[0]))
				{
					$row['color'] = $tmp[1];
				}else{
					$row['color'] = $tmp[0];
				}
			}
	
	
	
			
			$tmp = explode(',',$row['size_qty']);
			$row['size_qty'] = $tmp;
			
			if($row['size_box'] <> '')
			{
				$tmp = explode(',',$row['size_box']);
				$row['size_box'] = $tmp;
				$box_mk = 1;
			}
			
			$tmp = explode(',',$row['size']);
			$row['size'] = $tmp;
			$ctn_tmp = explode('-',$row['cnt']);
			$row['bk_qty'] = $row['s_qty'] * ($ctn_tmp[1] - $ctn_tmp[0] + 1);
			$row['amount'] = $row['bk_qty'] * $row['uprice'];
			$ord_qty += $row['bk_qty'];
			$tmp = explode(' ',$row['style_des']);
			$row['style_des'] = $tmp[0];
			
			if($ord_num != $row['cust_po'])
			{

				$ord_num = $row['cust_po'];
				$row['tb_mk'] = 1;
		//		$row['ord_qty'] = $ord_qty;
				$color='||';		
	//			$ord_qty = 0;		

			}
			if($color<>$row['color'])
			{
				$i++;
				$fld['shp_det'][$i] = $row;
				$color = $row['color'];

			}else{
				$fld['shp_det'][$i]['bk_qty'] += $row['bk_qty'];
				$fld['shp_det'][$i]['amount'] += $row['amount'];
				$fld['shp_det'][$i]['nw'] += $row['nw'];
				$fld['shp_det'][$i]['gw'] += $row['gw'];
			}
			
			$fld['amt']['qty'] += $row['bk_qty'];
			$fld['amt']['amount'] += $row['amount'];
			
		}
		
		$fld['amt']['eng_amount'] = engilsh_math($fld['amt']['amount'])." ONLY";
		
		$this->update_field_main('ttl_qty',$fld['amt']['qty'],$id);
		$this->update_field_main('ttl_amt',$fld['amt']['amount'],$id);
		
		$i_mk = 0;
		$quota = 0;
		$qty = 0;
		$j = 1;
		$ord_num = '';
		$unit_group = $GLOBALS['UNIT_GROUP']['AP2'];
		for($i=0; $i<sizeof($fld['shp_det']); $i++)
		{
			if($ord_num != $fld['shp_det'][$i]['cust_po'])
			{
				if($i > 0)			
				{
				$fld['shp_det'][$i_mk]['f_qty'] = $qty;
				$qty = change_unit_qty('pc',$fld['shp_det'][$i_mk]['quota_unit'],$qty);
				$fld['shp_det'][$i_mk]['quota_amt'] = $qty * $fld['shp_det'][$i_mk]['ship_quota'];
				$fld['shp_det'][$i_mk]['unit_select'] = $GLOBALS['arry2']->select($unit_group,$fld['shp_det'][$i_mk]['quota_unit'],'PHP_quota_unit['.$fld['shp_det'][$i_mk]['ord_num'].']','select'); 
				 
				$fld['shp_det'][$i_mk]['span'] = $j;		
				}
				$qty = 0;
				$i_mk = $i;
				$j=1;

				$ord_num = $fld['shp_det'][$i]['cust_po'];
			}else{
				if($i > 0)$j++;
			}
				$qty += $fld['shp_det'][$i]['bk_qty'];
				
			
		}
		$fld['shp_det'][$i_mk]['f_qty'] = $qty;
		$qty = change_unit_qty('pc',$fld['shp_det'][$i_mk]['quota_unit'],$qty);
		$fld['shp_det'][$i_mk]['quota_amt'] = $qty * $fld['shp_det'][$i_mk]['ship_quota'];
		$fld['shp_det'][$i_mk]['unit_select'] = $GLOBALS['arry2']->select($unit_group,$fld['shp_det'][$i_mk]['quota_unit'],'PHP_quota_unit['.$fld['shp_det'][$i_mk]['ord_num'].']','select'); 

		$fld['shp_det'][$i_mk]['span'] = $j;
		
		for($i=0; $i<sizeof($fld['shp_det']); $i++)
		{
			$q_str = "SELECT * FROM shipdoc_other_ord_field WHERE ship_ord_id='".$fld['shp_det'][$i]['ship_ord_id']."'";
			$q_result = $sql->query($q_str);
			while ($row = $sql->fetch($q_result)) {
				$fld['shp_det'][$i]['oth_field'][]=$row;
			}
		}
		
		
		
		$q_str = "SELECT * FROM shipdoc_log WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['shp_log'] = array();
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_log'][] = $row;
		}		
		

		$q_str = "SELECT * FROM shipdoc_file WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$fld['done'] = array();
		while ($row = $sql->fetch($q_result)) {
			$fld['done'][] = $row;
		}			

		$fld['shp_charge'] = array();
		$q_str = "SELECT * FROM shipdoc_cost WHERE ship_id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	
		while ($row = $sql->fetch($q_result)) {
			$fld['shp_charge'][] = $row;
		}				
		
		return $fld;
	} // end func		
*/	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id)	抓出指定記錄內資料  approval時判斷數量用
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_apvd($id) {

		if (!$id) {
			$this->msg->add("Error ! please check record ID.");		    
			return false;
		}

		$sql = $this->sql;
		$FTY_CM = $_SESSION['FTY_CM'];
		$fld = array();

		$q_str = "SELECT sum(shipdoc_qty.s_qty * shipdoc_qty.s_cnum) as qty_shp, shipdoc_qty.ord_num,										  
										 (order_partial.p_qty_done - order_partial.p_qty_shp) as rem_qty,  
										 shipdoc.fv_etd as ship_date, order_partial.id as p_id
							FROM shipdoc_qty, shipdoc, order_partial, wiqty
							WHERE wiqty.id = shipdoc_qty.color AND wiqty.p_id = order_partial.id AND
										shipdoc_qty.ship_id = shipdoc.id AND
										ship_id ='$id'
							GROUP BY ord_num, wiqty.p_id";
					
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
//			if($row['ship_fob'] == 0 )$row['ship_fob'] = $row['uprice'];
//			$row['su_shp']  = NUMBER_FORMAT(($row['ie1'] * $row['qty_shp']),0,'','');
//			$row['acm'] = $row['cm'] = 0;
//			$row['acm'] = $FTY_CM[$row['factory']];
//			$row['cm'] = $row['acm'] * $row['su_shp'];	
//			$row['fob']=$row['qty_shp'] * $row['ship_fob'];

			$fld[] = $row;
			
		}
//		echo $FTY_CM['HJ'];

		return $fld;
	} // end func		
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->count_size_qty($ord_num, $assort) 計算assortment中各尺碼未出口量
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_size_qty($ord_num, $assort) {
		
		$sql = $this->sql;
		$shp_det = array();
		$q_str = "SELECT * FROM shipdoc_qty WHERE ord_num ='$ord_num' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp = explode(',',$row['size_qty']);
			$row['size_qty'] = $tmp;
			$shp_det[] = $row;
		}
		for($i=0; $i<sizeof($assort); $i++)
		{
			for($j=0; $j<sizeof($shp_det); $j++)
			{
				
				if($assort[$i]['id'] == $shp_det[$j]['color'])
				{
					
					for($k=0; $k<sizeof($assort[$i]['qty']); $k++)
					{
						
						if($shp_det[$j]['size_box'] <> "")
						{
						
							$inner_box = explode(',',$shp_det[$j]['size_box']);
							$assort[$i]['qty'][$k] -= ($shp_det[$j]['size_qty'][$k] * $inner_box[$k] * $shp_det[$j]['cnum']);
						}else{							 
							$assort[$i]['qty'][$k] -= ($shp_det[$j]['size_qty'][$k] * $shp_det[$j]['s_cnum']);
						}
					}					
				}
			}
		
		}

		for($i=0; $i<sizeof($assort); $i++)
		{
			$sum = 0;
			for($k=0; $k<sizeof($assort[$i]['qty']); $k++)
			{
				$sum +=$assort[$i]['qty'][$k];				
			}		
			$assort[$i]['sum'] = $sum;
		}		
		
		return $assort;
	} // end func
	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 副料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_pack_det($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE shipdoc_qty SET size_qty='"		.$parm['size_qty'].
								"',	size_box='"		.$parm['size_box'].
								"',	nnw='"		.$parm['nnw'].
								"',	nw='"			.$parm['nw'].
								"',	gw='"			.$parm['gw'].
								"', qty='"		.$parm['qty'].
								"', s_qty='"	.$parm['s_qty'].
								"', s_cnum='"	.$parm['s_cnum'].
								"', ppk='"		.$parm['ppk'].
								"', fit='"		.$parm['fit'].
								"', ucc='"		.$parm['ucc'].
								"', ctn_id='"	.$parm['ctn_id'].
								"',	cnum='"		.$parm['cnum'].
								"',	cnt='"		.$parm['ctn'].
								"',	ctn_mk='"	.$parm['ctn_mk'].
								"', ctn_l='"	.$parm['ctn_l'].
								"', ctn_w='"	.$parm['ctn_w'].
								"', ctn_h='"	.$parm['ctn_h'].								
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
#	->edit($parm)		更新 副料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_charge($id,$charge,$des) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE shipdoc_cost SET charge='"		.$charge.
								"', chg_des='"	.$des.								
								"'  WHERE id='"		.$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_pack_det($id) 刪除裝箱項目 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_pack_det($id) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}



	 $q_str = "SELECT ship_id, ship_ord_id FROM shipdoc_qty WHERE id ='".$id."'";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ship_det = $sql->fetch($q_result);


		$q_header = "DELETE FROM shipdoc_qty WHERE id ='".$id."'";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}


	 $q_str = "SELECT id FROM shipdoc_qty WHERE ship_ord_id ='".$ship_det['ship_ord_id']."'";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result))
		{
			$q_header = "DELETE FROM shipdoc_ord WHERE id ='".$ship_det['ship_ord_id']."'";
//echo $q_str."<BR>";
			$q_result = $sql->query($q_header);
		}

	 $q_str = "SELECT id FROM shipdoc_qty WHERE ship_id ='".$ship_det['ship_id']."'";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result))
		{
			$q_header = "DELETE FROM shipdoc WHERE id ='".$ship_det['ship_id']."'";
//echo $q_str."<BR>";
			$q_result = $sql->query($q_header);
			return 2;
		}

		
		
	
		return 1;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_pack_det($id) 刪除裝箱項目 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_charge($id) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "DELETE FROM shipdoc_cost WHERE id ='".$id."'";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_main($name,$value,$id) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$value = str_replace("'","\'",$value);		
	$q_str = "UPDATE shipdoc SET ".$name."='".$value."'  WHERE id='".$id."' ";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_ord($name,$value,$id) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$value = str_replace("'","\'",$value);		
	$q_str = "UPDATE shipdoc_ord SET ".$name."='".$value."'  WHERE id='".$id."' ";
	

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_ship_out($order)	搜尋  指定訂單 之產出資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pack_det($id) {

		$sql = $this->sql;
		$srh = new SEARCH();
		$result = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_str = "SELECT * FROM shipdoc_qty WHERE id ='".$id."'";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		
		$q_str = "SELECT * FROM shipdoc_qty WHERE cnt = '".$row['cnt']."' AND ship_id = '".$row['ship_id']."'";
		$q_result = $sql->query($q_str);
		while($rows = $sql->fetch($q_result))
		{
			$result[] = $rows;
		}
		
		
		return $result;
		// 傳回 $result [ $result[$i][$k_date], $result[$i][$order_num].....]


	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
# mode = 2 wait for packing confirm
# mode = 4 wait for invoice submit/reject
# mode = 6 wait for invoice confirm
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT DISTINCT shipdoc.*, cust.cust_init_name as cust_init 
								 FROM cust, shipdoc , shipdoc_ord";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("shipdoc.id DESC");
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



		$srh->add_where_condition("shipdoc.ord_cust = cust.cust_s_name AND shipdoc.cust_ver = cust.ver");
		$srh->add_where_condition("shipdoc.id = shipdoc_ord.ship_id");


if ($mode){
		if ($str = $argv['SCH_inv'] )  { 
			$srh->add_where_condition("shipdoc.inv_num like '%$str%'", "SCH_inv",$str," Invoice : [ $str ]. "); }
		if ($str = $argv['SCH_date_str'] )  { 
			$srh->add_where_condition("shipdoc.ship_date >= '$str'", "SCH_date",$str," SHIP DATE > [ $str ]. "); }
		if ($str = $argv['SCH_date_end'] )  { 
			$srh->add_where_condition("shipdoc.ship_date <= '$str'", "SCH_date",$str," SHIP DATE < [ $str ]. "); }
		if ($str = $argv['SCH_des'] )  { 
			$srh->add_where_condition("shipdoc.des like '%$str%'", "SCH_des",$str," Description =[ $str ]. "); }
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("shipdoc.ord_cust = '$str'", "SCH_des",$str," Customer =[ $str ]. "); }
		if ($str = $argv['PHP_fty'] )  { 
			$srh->add_where_condition("shipdoc.fty = '$str'", "SCH_des",$str," Factory =[ $str ]. "); }
		if ($str = $argv['SCH_ord'] )  { 
			$srh->add_where_condition("shipdoc_ord.ord_num = '$str'", "SCH_ord",$str," ORDER# =[ $str ]. "); }


}

if ($mode==2){
			$srh->add_where_condition("shipdoc.status = 2"); 
}

if ($mode==4){
			$srh->add_where_condition("shipdoc.status >= 4"); 
}		

if ($mode==6){
			$srh->add_where_condition("shipdoc.status = 6"); 
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

		$op['ship_doc'] = $result;  // 資料錄 拋入 $op
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
#	->edit($parm)		更新 副料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_inv($parm) {

		$sql = $this->sql;
//		$parm['others'] = str_replace("'","\'",$parm['others']);
		foreach($parm as $key => $value)
		{
			$parm[$key] = str_replace("'","\'",$value);
		}
				
		#####   更新資料庫內容
		$q_str = "UPDATE shipdoc SET ship_term='"		.$parm['ship_term'].
								"',	ship_via='"			.$parm['ship_via'].
								"',	ship_from='"		.$parm['ship_from'].
								"',	ship_to='"			.$parm['ship_to'].
								"', payment='"			.$parm['payment'].
//								"', mark_no='"			.$parm['mark_no'].
								"', des='"					.$parm['des'].								
								"', fv='"						.$parm['fv'].
								"', fv_etd='"				.$parm['fv_etd'].
								"', fv_eta='"				.$parm['fv_eta'].
								"', mv='"						.$parm['mv'].
								"', mv_etd='"				.$parm['mv_etd'].
								"', mv_eta='"				.$parm['mv_eta'].								
								"', lc_no='"				.$parm['lc_no'].
								"', lc_bank='"			.$parm['lc_bank'].
								"', consignee='"		.$parm['consignee'].
								"', ship_addr='"		.$parm['ship_addr'].
								
								"', bill_addr='"		.$parm['bill_addr'].
								"', fty_det='"			.$parm['fty_det'].
								"', ship_det='"			.$parm['ship_det'].
								
								"', ship_date='"		.$parm['ship_date'].
								"', inv_num='"			.$parm['inv_num'].
								
								"', mid_no='"				.$parm['mid_no'].
								"', country_org='"	.$parm['country_org'].
								"', ship_mark='"		.$parm['ship_mark'].
								"', others='"				.$parm['others'].
								
								"', ship_by='"			.$parm['ship_by'].
								
								"', carrier='"			.$parm['carrier'].
								"', fab_des='"			.$parm['fab_des'].
								"', dis_port='"			.$parm['dis_port'].
								"', lc_date='"			.$parm['lc_date'].
								"', cbm='"					.$parm['cbm'].

								"', nty_part='"				.$parm['nty_part'].
								"', stmt='"						.$parm['stmt'].
								"', comm='"						.$parm['comm'].
								"', conter='"					.$parm['conter'].
								"', constor='"				.$parm['constor'].
							
								"', benf_det='"			.$parm['benf_det'].								
								"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_log($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO shipdoc_log (ship_id, des, log_user, k_time) VALUES('".
									$parm['ship_id']."','".
									$parm['des']."','".
									$GLOBALS['SCACHE']['ADMIN']['login_id']."','".
									date('Y-m-d')."')";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id
		
		return $pdt_id;

	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->change_color_name($old_color,$new_color,$ord_num) 更改訂單與shipdoc的color 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_color_name($id,$new_color) {

		$sql = $this->sql;

//取得訂單WI ID
//	$q_str = "SELECT wi.id FROM wi WHERE wi.wi_num = '".$ord_num."'";
//	//echo $q_str;
//		if (!$q_result = $sql->query($q_str)) {
//			$this->msg->add("Error! Database can't access!");
//			$this->msg->merge($sql->msg);
//			return false;    
//		}
//
//		if (!$row = $sql->fetch($q_result)) {
//			$this->msg->add("Error ! can't find these record!");
//			return false;    
//		}
//		$wi_id = $row[0];
		

		
//更新訂單colorway
	$q_str = "UPDATE wiqty SET ship_color ='".$new_color."' WHERE id='".$id."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
/*		
//更新訂單colorway
	$q_str = "UPDATE shipdoc_qty SET color  ='".$new_color."' WHERE ord_num='".$ord_num."' AND color ='".$old_color."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
*/		
//		$result;  // 資料錄 拋入 $op

		return true;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	搜尋 月份 出口訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ship_ord($field,$id) {

		$sql = $this->sql;

//取得訂單基本資料
	$q_str = "SELECT $field
							FROM shipdoc_ord
							WHERE ship_id =".$id;
	//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$str = array();
		while ($row = $sql->fetch($q_result)) {
			$mk = 0;
			for($i=0; $i<sizeof($str); $i++)
			{
				if($str[$i] == $row[0]) $mk = 1;
			}
			if($mk == 0) $str[] = $row[0];
		}
		return  $str;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	搜尋 月份 出口訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_inv($inv_num,$id) {

		$sql = $this->sql;

//取得訂單基本資料
	$q_str = "SELECT id	FROM shipdoc	WHERE id <>".$id." AND inv_num='".$inv_num."'";
	//echo $q_str;
	$q_result = $sql->query($q_str);
	
	if ($row = $sql->fetch($q_result)) {
		return 0;
	}
	return  1;
	} // end func		
	
	
		
		
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_field($parm)		加入新 訂單其貨欄位記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_field($parm) {
					
		$sql = $this->sql;
		$parm['values'] = str_replace("'","\'",$parm['values']);
		$parm['field'] = str_replace("'","\'",$parm['field']);

					# 加入資料庫
		$q_str = "INSERT INTO shipdoc_other_ord_field (ship_id, ship_ord_id, f_name, f_values) VALUES('".
									$parm['shp_id']."','".
									$parm['id']."','".
									$parm['field']."','".
									$parm['values']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id
		
		return $pdt_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_pack_det($id) 刪除裝箱項目 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_ord_field($id) {

		$sql = $this->sql;

		$q_header = "DELETE FROM shipdoc_other_ord_field WHERE id ='".$id."'";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_other_field($parm)		加入新 訂單其貨欄位記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_other_field($parm) {
					
		$sql = $this->sql;
		$parm['values'] = str_replace("'","\'",$parm['values']);
		$parm['field'] = str_replace("'","\'",$parm['field']);
					# 加入資料庫
		$q_str = "INSERT INTO shipdoc_other_field (ship_id, f_name, f_values) VALUES('".
									$parm['id']."','".
									$parm['field']."','".
									$parm['values']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id
		
		return $pdt_id;

	} // end func		


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_pack_det($id) 刪除裝箱項目 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_other_field($id) {

		$sql = $this->sql;

		$q_header = "DELETE FROM shipdoc_other_field WHERE id ='".$id."'";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	搜尋 月份 出口訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_other_value($id,$f_names) {

		$sql = $this->sql;
	
//取得訂單基本資料
	$q_str = "SELECT f_values	FROM shipdoc_other_field	WHERE ship_id =".$id." AND f_name='".$f_names."'";

//	echo $q_str."<BR>";
	$q_result = $sql->query($q_str);
	
	if ($row = $sql->fetch($q_result)) {
		return $row[0];
	}
	return  false;
	} // end func		


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	搜尋 月份 出口訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_other_ord_value($id,$f_names) {

		$sql = $this->sql;
	
//取得訂單基本資料
	$q_str = "SELECT f_values	FROM shipdoc_other_ord_field	WHERE ship_ord_id =".$id." AND f_name='".$f_names."'";

//	echo $q_str."<BR>";
	$q_result = $sql->query($q_str);
	
	if ($row = $sql->fetch($q_result)) {
		return $row[0];
	}
	return  false;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_det($name,$value,$id=0,$ship_ord_id=0) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$value = str_replace("'","\'",$value);		
		if($id)
		{
			$q_str = "UPDATE shipdoc_qty SET ".$name."='".$value."'  WHERE id='".$id."' ";
		}else if($ship_ord_id){
			$q_str = "UPDATE shipdoc_qty SET ".$name."='".$value."'  WHERE ship_ord_id='".$ship_ord_id."' ";
		}
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_order_fob($PHP_id) {

		$sql = $this->sql;
		############### 檢查輸入項目
		$ord_rec = array();
		#####   更新資料庫內容
		$q_str = "SELECT ord_num FROM shipdoc_qty WHERE ship_id = ".$PHP_id." GROUP BY ord_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$ord_rec[] = $row['ord_num'];
		}
		for($i=0; $i<sizeof($ord_rec); $i++)
		{
			$q_str = "SELECT sum(s_cnum * s_qty * ship_fob) as fob, sum(s_cnum * s_qty ) as qty   FROM shipdoc_qty 
								WHERE ord_num = '".$ord_rec[$i]."' GROUP BY ord_num";
	
			$q_result = $sql->query($q_str);
			if ($row = $sql->fetch($q_result)) {
				$row['ship_fob'] = $row['fob'] / $row['qty'];
				$q_str = "UPDATE s_order SET ship_fob ='".$row['ship_fob']."'  WHERE order_num='".$ord_rec[$i]."' ";
		
				$q_result = $sql->query($q_str);		
			}

		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mm_rpt($etd)  出貨月報表
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_mm_rpt($etd,$fty) {

		$sql = $this->sql;
		$ord_rec = array();
		#####   取得shipdoc相關欄位
		// $q_str = "SELECT shipdoc_qty.ord_num, shipdoc_qty.style_num, sum((shipdoc_qty.s_qty * shipdoc_qty.s_cnum)) as qty, 
										 // shipdoc_qty.ship_fob,	shipdoc.inv_num, shipdoc.ship_to, shipdoc_ord.cust_po,
										 // shipdoc.id, consignee.f_name as consignee, shipdoc.fv_etd				 										 
							// FROM shipdoc_qty, shipdoc, shipdoc_ord, consignee 
							// WHERE 
							// shipdoc_qty.ship_ord_id = shipdoc_ord.id AND 
							// shipdoc_qty.ship_id = shipdoc.id AND							      
							      // shipdoc_ord.ship_id = shipdoc.id AND shipdoc.consignee = consignee.s_name AND
							      // shipdoc.fv_etd like '".$etd."%' AND shipdoc.status = 8	AND fty = '".$fty."'				      
							// GROUP BY shipdoc_qty.ord_num, shipdoc_ord.ord_num, shipdoc_qty.ship_fob, shipdoc.inv_num, shipdoc_qty.style_num";

		#M11022501 修改 Excel 欄位數量的顯示
		$q_str = "SELECT shipdoc_qty.ord_num, shipdoc_qty.style_num, sum((shipdoc_qty.s_qty * shipdoc_qty.s_cnum)) as qty, 
										 shipdoc_qty.ship_fob,	shipdoc.inv_num, shipdoc.ship_to, shipdoc_ord.cust_po,
										 shipdoc.id, shipdoc.fv_etd	,	shipdoc.consignee		 										 
							FROM shipdoc_qty, shipdoc, shipdoc_ord
							WHERE 
							shipdoc_qty.ship_ord_id = shipdoc_ord.id AND 
							shipdoc_qty.ship_id = shipdoc.id AND							      
							      shipdoc_ord.ship_id = shipdoc.id AND 
							      shipdoc.fv_etd like '".$etd."%' AND shipdoc.status = 8	AND fty = '".$fty."'				      
							GROUP BY shipdoc_qty.ord_num, shipdoc_ord.ord_num, shipdoc_qty.ship_fob, shipdoc.inv_num, shipdoc_qty.style_num";
					
		// echo $q_str."<BR>";
		// exit;

		#M11022501 修改 Excel 欄位數量的顯示
		# 抓入 consignee 
		$con = $rowc = array();
		$q_con = "SELECT `s_name`,`f_name` FROM `consignee`";
		$q_rcon = $sql->query($q_con);
		while ($rowc = $sql->fetch($q_rcon)) {
			$con[$rowc['s_name']] = $rowc['f_name'];
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$mo =array();
		while ($row = $sql->fetch($q_result)) {
			#M11022501 修改 Excel 欄位數量的顯示
			$row['consignee'] = $con[$row['consignee']];
			$rec[] = $row;
		}

		
		$rtn = array();
		for($i=0; $i<sizeof($rec); $i++)
		{
//取得訂單相關資料			
			$q_str = "SELECT s_order.etd, s_order.fty_cm, s_order.cust,
										   cust.cust_f_name as cust_name, style_type.des as style
								FROM   s_order, style_type, cust
								WHERE  s_order.cust = cust.cust_s_name AND 
							         s_order.style = style_type.style_type AND order_num = '".$rec[$i]['ord_num']."'";
//echo $q_str."<BR>";
			$q_result = $sql->query($q_str);
		  $row = $sql->fetch($q_result);
			$rec[$i]['etd'] = $row['etd'];
			$rec[$i]['fty_cm'] = $row['fty_cm'];
			$rec[$i]['cust'] = $row['cust'];
			$rec[$i]['cust_name'] = $rec[$i]['consignee'];
			$style = explode(' ',$row['style']);
			$rec[$i]['style'] = $style[0];
			
			// $rec[$i]['qty'] = $rec[$i]['s_qty']  * $rec[$i]['s_cnum'] ;

			$rec[$i]['amount'] = $rec[$i]['qty'] * $rec[$i]['ship_fob'];
			$rec[$i]['cm_amt'] = $rec[$i]['qty'] * $rec[$i]['fty_cm'];
			
			//依客戶和目的地分層			
			$fst_key = $rec[$i]['consignee'].'_'.$rec[$i]['ship_to'];
			// echo $fst_key."<BR>";
			if(!isset($rtn[$fst_key]['qty']))$rtn[$fst_key]['qty'] = 0;
			if(!isset($rtn[$fst_key]['amount']))$rtn[$fst_key]['amount'] = 0;
			if(!isset($rtn[$fst_key]['cm_amt']))$rtn[$fst_key]['cm_amt'] = 0;
			//依invoice分層			
			$sec_key = $rec[$i]['id'];
			if(!isset($rtn[$fst_key]['inv'][$sec_key]['qty']))$rtn[$fst_key]['inv'][$sec_key]['qty'] = 0;
			if(!isset($rtn[$fst_key]['inv'][$sec_key]['amount']))$rtn[$fst_key]['inv'][$sec_key]['amount'] = 0;
			
			$rtn[$fst_key]['inv'][$sec_key]['det'][] = $rec[$i];
			//加總	
			$rtn[$fst_key]['qty'] += $rec[$i]['qty'];
			$rtn[$fst_key]['amount'] += $rec[$i]['amount'];
			$rtn[$fst_key]['cm_amt'] += $rec[$i]['cm_amt'];
			$rtn[$fst_key]['inv'][$sec_key]['qty'] += $rec[$i]['qty'];
			$rtn[$fst_key]['inv'][$sec_key]['amount'] += $rec[$i]['amount'];
		}	
		
		// print_r($rtn);
//取得其他費用
		foreach($rtn as $key => $value)
		{
	//		echo $key."<BR>";
			foreach($rtn[$key]['inv'] as $s_key => $s_value)
			{
//				echo '  '.$s_key.'====>'.sizeof($rtn[$key]['inv'][$s_key]['det'])."<BR>";				
				$rtn[$key]['inv'][$s_key]['ticket_fee'] = $rtn[$key]['inv'][$s_key]['damage_fee'] = $rtn[$key]['inv'][$s_key]['other_fee'] = 0;
				$q_str = "SELECT charge	 FROM	shipdoc_cost WHERE  ship_id = '".$s_key."' AND chg_des like '%PRICE TICKET%'";
//echo $q_str."<BR>";
				$q_result = $sql->query($q_str);
		  	while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['ticket_fee'] += $row['charge'];

				$q_str = "SELECT charge	 FROM	shipdoc_cost WHERE  ship_id = '".$s_key."' AND chg_des like '%DAMAGE%'";
//echo $q_str."<BR>";				
				$q_result = $sql->query($q_str);
		  	while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['damage_fee'] += $row['charge'];

				$q_str = "SELECT charge	 FROM	shipdoc_cost WHERE  ship_id = '".$s_key."' AND !(chg_des like '%DAMAGE%') AND !(chg_des like '%PRICE TICKET%')";
//echo $q_str."<BR>";
				$q_result = $sql->query($q_str);
		  	while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['other_fee'] += $row['charge'];
		  	$rtn[$key]['inv'][$s_key]['amount'] = $rtn[$key]['inv'][$s_key]['amount']- $rtn[$key]['inv'][$s_key]['ticket_fee'] - $rtn[$key]['inv'][$s_key]['damage_fee'] - $rtn[$key]['inv'][$s_key]['other_fee'];
				$rtn[$key]['amount'] = $rtn[$key]['amount']- $rtn[$key]['inv'][$s_key]['ticket_fee'] - $rtn[$key]['inv'][$s_key]['damage_fee'] - $rtn[$key]['inv'][$s_key]['other_fee'];
			}
		}	
		
		return $rtn;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mm_rpt_tiptop($etd)  出貨月報表 for tiptop
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_mm_rpt_tiptop($etd,$fty) {

		$sql = $this->sql;
		$ord_rec = array();
		
		$q_str = "SELECT shipdoc_qty.ord_num, shipdoc_qty.style_num, sum((shipdoc_qty.s_qty * shipdoc_qty.s_cnum)) as qty, 
										 shipdoc_qty.ship_fob,	shipdoc.inv_num, shipdoc.ship_to, shipdoc_ord.cust_po,
										 shipdoc.id, shipdoc.fv_etd	,	shipdoc.consignee, shipdoc.payment, shipdoc.des,
										 shipdoc.ship_date, shipdoc.ttl_amt, cust.cust_s_name, cust.cust_f_name, cust.cust_init_name, cust.uni_no
							FROM shipdoc_qty, shipdoc, shipdoc_ord, cust
							WHERE 
							shipdoc_qty.ship_ord_id = shipdoc_ord.id AND 
							shipdoc_qty.ship_id = shipdoc.id AND							      
							      shipdoc_ord.ship_id = shipdoc.id AND 
							      shipdoc.ship_date like '".$etd."%' AND shipdoc.status = 8	AND fty = '".$fty."' and
								  shipdoc.ord_cust = cust.cust_s_name AND shipdoc.cust_ver = cust.ver
							GROUP BY shipdoc_qty.ord_num, shipdoc_ord.ord_num, shipdoc_qty.ship_fob, shipdoc.inv_num, shipdoc_qty.style_num";
					

		# 抓入 consignee 
		$con = $rowc = array();
		$q_con = "SELECT `s_name`,`f_name` FROM `consignee`";
		$q_rcon = $sql->query($q_con);
		while ($rowc = $sql->fetch($q_rcon)) {
			$con[$rowc['s_name']] = $rowc['f_name'];
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$mo =array();
		while ($row = $sql->fetch($q_result)) {
			#M11022501 修改 Excel 欄位數量的顯示
			$row['consignee'] = $con[$row['consignee']];
			$rec[] = $row;
		}

		
		$rtn = array();
		for($i=0; $i<sizeof($rec); $i++)
		{
//取得訂單相關資料			
			$q_str = "SELECT s_order.etd, s_order.fty_cm, s_order.cust,
										   cust.cust_f_name as cust_name, style_type.des as style
								FROM   s_order, style_type, cust
								WHERE  s_order.cust = cust.cust_s_name AND 
							         s_order.style = style_type.style_type AND order_num = '".$rec[$i]['ord_num']."'";
//echo $q_str."<BR>";
			$q_result = $sql->query($q_str);
		  $row = $sql->fetch($q_result);
			$rec[$i]['etd'] = $row['etd'];
			$rec[$i]['fty_cm'] = $row['fty_cm'];
			$rec[$i]['cust'] = $row['cust'];
			$rec[$i]['cust_name'] = $rec[$i]['consignee'];
			$style = explode(' ',$row['style']);
			$rec[$i]['style'] = $style[0];
			
			// $rec[$i]['qty'] = $rec[$i]['s_qty']  * $rec[$i]['s_cnum'] ;

			$rec[$i]['amount'] = $rec[$i]['qty'] * $rec[$i]['ship_fob'];
			$rec[$i]['cm_amt'] = $rec[$i]['qty'] * $rec[$i]['fty_cm'];
			
			//依客戶和目的地分層			
			$fst_key = $rec[$i]['consignee'].'_'.$rec[$i]['ship_to'];
			// echo $fst_key."<BR>";
			if(!isset($rtn[$fst_key]['qty']))$rtn[$fst_key]['qty'] = 0;
			if(!isset($rtn[$fst_key]['amount']))$rtn[$fst_key]['amount'] = 0;
			if(!isset($rtn[$fst_key]['cm_amt']))$rtn[$fst_key]['cm_amt'] = 0;
			//依invoice分層			
			$sec_key = $rec[$i]['id'];
			if(!isset($rtn[$fst_key]['inv'][$sec_key]['qty']))$rtn[$fst_key]['inv'][$sec_key]['qty'] = 0;
			if(!isset($rtn[$fst_key]['inv'][$sec_key]['amount']))$rtn[$fst_key]['inv'][$sec_key]['amount'] = 0;
			
			$rtn[$fst_key]['inv'][$sec_key]['det'][] = $rec[$i];
			//加總	
			$rtn[$fst_key]['qty'] += $rec[$i]['qty'];
			$rtn[$fst_key]['amount'] += $rec[$i]['amount'];
			$rtn[$fst_key]['cm_amt'] += $rec[$i]['cm_amt'];
			$rtn[$fst_key]['inv'][$sec_key]['qty'] += $rec[$i]['qty'];
			$rtn[$fst_key]['inv'][$sec_key]['amount'] += $rec[$i]['amount'];
		}	
		
		// print_r($rtn);
//取得其他費用
		foreach($rtn as $key => $value)
		{
	//		echo $key."<BR>";
			foreach($rtn[$key]['inv'] as $s_key => $s_value)
			{
//				echo '  '.$s_key.'====>'.sizeof($rtn[$key]['inv'][$s_key]['det'])."<BR>";				
				$rtn[$key]['inv'][$s_key]['ticket_fee'] = $rtn[$key]['inv'][$s_key]['damage_fee'] = $rtn[$key]['inv'][$s_key]['other_fee'] = 0;
				$q_str = "SELECT charge	 FROM	shipdoc_cost WHERE  ship_id = '".$s_key."' AND chg_des like '%PRICE TICKET%'";
//echo $q_str."<BR>";
				$q_result = $sql->query($q_str);
		  	while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['ticket_fee'] += $row['charge'];

				$q_str = "SELECT charge	 FROM	shipdoc_cost WHERE  ship_id = '".$s_key."' AND chg_des like '%DAMAGE%'";
//echo $q_str."<BR>";				
				$q_result = $sql->query($q_str);
		  	while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['damage_fee'] += $row['charge'];

				$q_str = "SELECT charge	 FROM	shipdoc_cost WHERE  ship_id = '".$s_key."' AND !(chg_des like '%DAMAGE%') AND !(chg_des like '%PRICE TICKET%')";
//echo $q_str."<BR>";
				$q_result = $sql->query($q_str);
		  	while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['other_fee'] += $row['charge'];
		  	$rtn[$key]['inv'][$s_key]['amount'] = $rtn[$key]['inv'][$s_key]['amount']- $rtn[$key]['inv'][$s_key]['ticket_fee'] - $rtn[$key]['inv'][$s_key]['damage_fee'] - $rtn[$key]['inv'][$s_key]['other_fee'];
				$rtn[$key]['amount'] = $rtn[$key]['amount']- $rtn[$key]['inv'][$s_key]['ticket_fee'] - $rtn[$key]['inv'][$s_key]['damage_fee'] - $rtn[$key]['inv'][$s_key]['other_fee'];
			}
		}	
		
		return $rtn;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->daily_ship_capacity($etd)  離峰自動寫入capacity
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_ship_capacity($etd) {		
		$mm = date('M');
		$yy = date('Y');
		if($mm == '01')
		{
			$yymm = array(($yy-1).'-12',$yy.'-'.$mm);
		}else{
			$yymm = array($yy.'-'.($mm-1),$yy.'-'.$mm);
		}
		$sql = $this->sql;
		$ord_rec = array();
		#####   取得shipdoc相關欄位
		for($i=0; $i<sizeof($yymm); $i++)
		{
			$q_str = "SELECT sum((s_qty * s_cnum)) as qty, shipdoc.fty,								 										 
												sum((s_qty * s_cnum * shipdoc_qty.ship_fob)) as ship_fob
								FROM shipdoc_qty, shipdoc
								WHERE shipdoc_qty.ship_id = shipdoc.id AND							      
								      shipdoc.fv_etd like '".$yymm[$i]."%' AND shipdoc.status > 1							      
								GROUP BY shipdoc.fty";
								
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! can't update database.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while ($row = $sql->fetch($q_result)) {
				$tmp = explode('-',$yymm[$i]);
				$capaci->update_field($row['fty'],$tmp[0],"shipping","m".$tmp[1],$row['qty']);
				$capaci->update_field($row['fty'],$tmp[0],"shp_fob","m".$tmp[1],$row['ship_fob']);
			}
		}
		
		return true;
	} // end func


	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_static_month_su 'shipdoc_qty' 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_static_month_su($fty, $yystr,$yyend, $where_str='',$mode=0) {

	$sql = $this->sql;		
	$rtn = array();

	$q_str = "

	SELECT 
	sum(shipping_doc_qty.ttl_qty * s_order.ie2) as su , 
	YEAR(shipping_doc.ship_date)as etd_year, MONTH(shipping_doc.ship_date) as etd_month 

	FROM 
	s_order , pdtion ,  shipping_doc , shipping_doc_qty


	WHERE 
	s_order.order_num = pdtion.order_num AND 
	shipping_doc.id = shipping_doc_qty.s_id AND 
	s_order.order_num = shipping_doc_qty.ord_num AND 
	shipping_doc.factory = '".$fty."' AND 
	shipping_doc.ship_date >= '".$yystr."' AND 
	shipping_doc.ship_date <='".$yyend."' AND 
	s_order.status >= 7 ".$where_str." 
	GROUP BY 
	etd_year, etd_month 
	
	";

	// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot access database, pls try later !");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row = $sql->fetch($q_result)) {
		if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
		$tmp = $row['etd_year']."-".$row['etd_month'];
		if($mode == 0)$rtn[$tmp] = $row['su'];
		if($mode == 2)$rtn[$row['etd_month']] = $row['su'];
		
	}
	return $rtn;
} // end func		
	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# -> get_static_month_prc
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_static_month_prc($fty, $yystr,$yyend, $where_str='',$mode=0) {

	$sql = $this->sql;		
	$rtn = array();

	$q_str = "

	SELECT 
	sum(shipping_doc_qty.ttl_qty * shipping_doc_qty.ship_fob) as su , 
	YEAR(shipping_doc.ship_date)as etd_year, MONTH(shipping_doc.ship_date) as etd_month 

	FROM 
	s_order , pdtion ,  shipping_doc , shipping_doc_qty


	WHERE 
	s_order.order_num = pdtion.order_num AND 
	shipping_doc.id = shipping_doc_qty.s_id AND 
	s_order.order_num = shipping_doc_qty.ord_num AND 
	shipping_doc.factory = '".$fty."' AND 
	shipping_doc.ship_date >= '".$yystr."' AND 
	shipping_doc.ship_date <='".$yyend."' AND 
	s_order.status >= 7 ".$where_str." 
	GROUP BY 
	etd_year, etd_month 
	
	";
	
	

	// echo $q_str."<p><br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot access database, pls try later !");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while ($row = $sql->fetch($q_result)) {
		if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
		$tmp = $row['etd_year']."-".$row['etd_month'];
		if($mode == 0)$rtn[$tmp] = $row['su'];
		if($mode == 2)$rtn[$row['etd_month']] = $row['su'];
		
	}
	return $rtn;
} // end func		
	




} // end class


?>