<?php 


class SHIPPING_DOC {

var $sql;
var $msg ;


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
# 必需聯上 sql 才可  啟始
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



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_status($status)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_status($status){
	switch($status){
		# wait for submit
		case 0:
		return '<font color="blue">WAIT FOR SUBMIT</font>';
		break;
		
		# wait for CFM.
		case 1:
		return '<font color="#f5d226">WAIT FOR CFM.</font>';
		break;
		
		# CONFORMED
		case 2:
		return '<font color="#666666">CONFORMED</font>';
		break;
		
		# APPROVED
		case 3:
		return 'APPROVED';
		break;

	}
}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_search($mode=0,$ord_parm="",$limit_entries=0) {

	$sql = $this->sql;
	$argv = $_SESSION['add_sch'];   //將所有的 globals 都抓入$argv
    // print_r($_SESSION['add_sch']);
	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "SELECT s_order.*, size_des.size_scale, cust.cust_init_name as cust_name,
										  order_partial.mks, order_partial.id as p_id, order_partial.p_etd as etd
							 FROM cust, order_partial, s_order LEFT JOIN size_des ON size_des.id = s_order.size ";

	$q_header = "SELECT s_order.*, size_des.size_scale, cust.cust_init_name as cust_name 
							 FROM cust, s_order LEFT JOIN size_des ON size_des.id = s_order.size ";
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
	$srh->row_per_page = 10;

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
	// $srh->add_where_condition("s_order.order_num = order_partial.ord_num");
	$srh->add_where_condition("s_order.cust_po <> ''");
	for($i=0; $i<sizeof($ord_parm); $i++)
	{
		$srh->add_where_condition("s_order.order_num <> '".$ord_parm[$i]['num']."'");
	}

	if ($mode==1){
		if ($str = $argv['PHP_fty'] ) { $srh->add_where_condition("s_order.factory = '$str'", "PHP_fty",$str," factory = [ $str ]. "); }
		if ($str = $argv['PHP_num'] ) { $srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_num",$str," order# as [ $str ]. "); }
		if ($str = $argv['PHP_cust'] ){ $srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str," Customer =[ $str ]. "); }
	}
	
	$srh->add_where_condition("s_order.status >= 7", '','','');
	// $srh->add_where_condition("order_partial.pdt_status > 0", '','','');
	$srh->add_where_condition("s_order.m_status >= 2", '','','');
    
    
	$srh->add_where_condition("s_order.etd > '".increceDaysInDate(date('Y-m-d'),-360)."'");

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



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->change_color_name($old_color,$new_color,$ord_num) 更改訂單與shipdoc的color 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function change_color_name($id,$new_color) {

	$sql = $this->sql;

	# 更新訂單colorway
	$q_str = "UPDATE wiqty SET ship_color ='".$new_color."' WHERE id='".$id."'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	} 
	//echo $q_str."<br>";exit;
	return true;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->chk_shipping() 刪除沒有數量的資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_shipping() {

	$sql = $this->sql;

	//$q_str = "SELECT `id` FROM `shipping_doc` WHERE `status` = '0' OR `ttl_amt` = '0.00';";
	$q_str = "SELECT `id` FROM `shipping_doc` WHERE `ttl_amt` = '0.00';";
	//echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while ($row = $sql->fetch($q_result)) {
		
		//$q_str = "SELECT `id` FROM `shipping_doc_qty` WHERE `s_id` = '".$row['id']."';";
		//echo $q_str."<br>";
		$q_header = "DELETE FROM `shipping_doc_qty` WHERE `s_id` = '".$row['id']."';";//新的_morial
		$sql->query($q_header);//新的_morial
		$q_header = "DELETE FROM `shipping_doc` WHERE `id` = '".$row['id']."';";//新的_morial
		$sql->query($q_header);//新的_morial
		/* $q_rst = $sql->query($q_str);
		if(!$row2 = $sql->fetch($q_rst)) {
			$q_header = "DELETE FROM `shipping_doc` WHERE `id` = '".$row['id']."';";
			$sql->query($q_header);
			//echo $q_header."<br>";
		} */
	}

	return true;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_data($($fty,$str,$limit_entries)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_data($field,$id) {

	$sql = $this->sql;

	// 取得訂單基本資料
	$q_str = "SELECT $field FROM `shipping_doc_qty` WHERE `s_id` ='".$id."';";
	// echo $q_str;
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$ord_num = array();
	$cust_po = array();
	
	while ($row = $sql->fetch($q_result)) {
		$ord_num[] = $row['ord_num'];
		$cust_po[] = $row['cust_po'];
	}
	
	$ord_num = array_unique($ord_num);
	$cust_po = array_unique($cust_po);
	
	$str['ord_num'] = $str['cust_po'] = '';
	
	foreach($ord_num as $k => $v){
		$str['ord_num'] .= (( $k > 0 ) ? '<br>'.$v : $v );
	}
	
	foreach($cust_po as $k => $v){
		$str['cust_po'] .= (( $k > 0 ) ? '<br>'.$v : $v );
	}
	
	return  $str;
} // end func	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_data($($fty,$str,$limit_entries)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_data_new($field,$id) {

	$sql = $this->sql;

	// 取得訂單基本資料
	$q_str = "SELECT $field FROM `invoice_import` WHERE `shipping_doc_id` ='".$id."';";
	//echo $q_str."<br>";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$ord_num = array();
	$cust_po = array();
	
	while ($row = $sql->fetch($q_result)) {
		$ord_num[] = $row['ord_num'];
		$cust_po[] = $row['po'];
	}
	
	$ord_num = array_unique($ord_num);
	$cust_po = array_unique($cust_po);
	
	$str['ord_num'] = $str['cust_po'] = '';
	
	foreach($ord_num as $k => $v){
		$str['ord_num'] .= (( $k > 0 ) ? '<br>'.$v : $v );
	}
	
	foreach($cust_po as $k => $v){
		$str['cust_po'] .= (( $k > 0 ) ? '<br>'.$v : $v );
	}
	
	return  $str;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_colorway($($fty,$str,$limit_entries)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_colorway($parm) {

global $arry2;

$sql = $this->sql;
$op = array();

foreach($parm as $key) {
	
	# 取得訂單基本資料
	$q_str	=
	"SELECT 
	s_order.cust , s_order.cust_po , s_order.size , s_order.qty as ord_qty , s_order.uprice as ord_fob , s_order.factory , 
	wiqty.id as w_id , wiqty.colorway , wiqty.ship_color , wiqty.qty as qty , 
	order_partial.mks 

	FROM 
	s_order , wi , wiqty , order_partial

	WHERE 
	s_order.order_num = wi.wi_num AND 
	wi.id = wiqty.wi_id AND 
	order_partial.id = wiqty.p_id AND 
	s_order.id =".$key['id']." 
	ORDER BY order_partial.mks";
	//echo $q_str."<br>";
	$q_result = $sql->query($q_str);
	
	$colorway = array();
	$qty_plus = array();

	while ($row = $sql->fetch($q_result)) {
	
		$colorway[] = array(
			'w_id' 			=> $row['w_id'] , 
			'ship_color'	=> $row['ship_color'], 
			'colorway' 		=> $row['colorway'] , 
			'mks' 			=> $row['mks'] , 
			'qty' 			=> explode( ',' , $row['qty']) 
		);
		
		$qty_plus[$row['ship_color']] 	= @qty_arr($row['qty'],$qty_plus[$row['ship_color']]);
		$cust		= $row['cust'];
		$cust_po	= $row['cust_po'];
		$size 		= $row['size'];
		$ord_qty 	= $row['ord_qty'];
		$ord_fob 	= $row['ord_fob'];
		$factory 	= $row['factory'];
		
	}

	$mo = 0;
	$recolor = array();

	foreach($qty_plus as $ky => $vl){
		
		// if($ky){

			# 扣除已核可確認的數量
			$q_str = "
			SELECT `shipping_doc_qty`.`size_qty` , `shipping_doc_qty`.`ship_fob` 

			FROM `shipping_doc` , `shipping_doc_qty` 
			WHERE 
			`shipping_doc_qty`.`ord_num` = '".$key['num']."' AND 
			`shipping_doc_qty`.`colorway` = '".$ky."' AND 
			`shipping_doc`.`id` = `shipping_doc_qty`.`s_id` AND 
			`shipping_doc`.`status` = '3'";
			if($q_result = $sql->query($q_str)) {
				$sqty = array();
				while ($row = $sql->fetch($q_result)) {
					$size_qty = explode( ',' , $row['size_qty']);
					foreach($size_qty as $keys => $vals) {
						$sqty[$keys] = $vl[$keys] - $vals;
					}
				}
			}
			
			// echo '<br>'.$vl.'<br>';
			$recolor[$mo] = array(
				'ship_color'	=> $ky ,
				'qty' 			=> $vl ,
				'sqty'			=> !$sqty?$vl:$sqty
			);

			$mo++;

		// }
	}

	$qty_plus = $recolor;

	$val = explode( '|' , $cust_po);
	$str = '';
	$arr = array();
	foreach($val as $keys => $vals){
		$vals = explode( '/' , $vals);
		$str .= $vals[0].',';
		$arr[] = $vals[0];
	}
	$cust_po = substr($str,0,-1);
	$cust_po_select = $arry2->select_id( $arr,'','','PHP_cust_po_'.$key['num'],'select','' );
	$q_str = "SELECT `size` FROM `size_des` WHERE `id` = '".$size."' ;";

	$q_result = $sql->query($q_str);
	$size = $sql->fetch($q_result);

	$op[] = array( 
		'id' 				=> $key['id'] , 
		'num' 				=> $key['num'] , 
		'ord_qty' 			=> $ord_qty , 
		'qty_plus' 			=> $qty_plus , 
		'ord_fob' 			=> $ord_fob , 
		'factory' 			=> $factory , 
		'sort' 				=> $key['num'] , 
		'colorway'			=> $colorway , 
		'cust' 				=> $cust , 
		'cust_po' 			=> $cust_po , 
		'cust_po_select' 	=> $cust_po_select , 
		'size' 				=> explode( ',' , $size[0]) ,
		'p_id' 				=> @$key['p_id'] ,
		'mks' 				=> @$key['mks'] 
	);

}
	//print_r($op);
	return $op;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_invoice($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_invoice($id) {

	$sql = $this->sql;

	if (!$id) {
		$this->msg->add("Error ! please check record ID.");		    
		return false;
	}

	$q_str = "
	SELECT `shipping_doc`.* , `consignee`.`f_name` 
	FROM `shipping_doc` LEFT JOIN `consignee` on ( `shipping_doc`.`consignee_id` = `consignee`.`id` ) 
	WHERE `shipping_doc`.`id` = '".$id."' ; ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! can't find these record!");
		return false;    
	}

	$po_user = $GLOBALS['user']->get(0,$row['submit_user']);
	$row['submit_id'] = $row['submit_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];

	$po_user = $GLOBALS['user']->get(0,$row['cfm_user']);
	$row['cfm_id'] = $row['cfm_user'];
	if ($po_user['name'])$row['cfm_user'] = $po_user['name'];

	$po_user = $GLOBALS['user']->get(0,$row['apv_user']);
	$row['apv_id'] = $row['apv_user'];
	if ($po_user['name'])$row['apv_user'] = $po_user['name'];

	$tmp = @explode('|',$row['vendor']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['vendor_name'] = $tmp[0];
	$row['vendor_addr'] = $tmp[1];

	$csn = @$GLOBALS['cust']->get_csge('', $row['consignee']);
	$row['consignee_name'] = $csn['f_name'];
	$row['consignee_addr'] = $csn['addr'];

	$invoice = $row;
	
	$q_str = "
	SELECT `shipping_doc_qty`.* , `size_des`.`size` , `s_order`.`id` as `o_id` 

	FROM `shipping_doc_qty` , `s_order` , `size_des` 
	WHERE 
	`shipping_doc_qty`.`ord_num` = `s_order`.`order_num` AND 
	`s_order`.`size` = `size_des`.`id` AND 
	`shipping_doc_qty`.`s_id` ='".$id."' 

	ORDER BY `shipping_doc_qty`.`ord_num`, `shipping_doc_qty`.`id` ";
	
	// echo $q_str;							
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$t_qty = 0 ;
	$t_amt = 0 ;
	while ($row = $sql->fetch($q_result)) {
		$arow['cust_po'][] = $row['cust_po'];
		$arow[$row['cust_po']][] = $row;
		$t_qty += $row['ttl_qty'] ;
		$t_amt += $row['amount'] ;
	}
	
	$mo = array();
	
	$arow['cust_po'] = array_unique($arow['cust_po']);
	
	# grand total
	$gt=array();
	
	foreach($arow['cust_po'] as $k => $v) {
	
		# sub total
		$tt=array();
		
		foreach($arow[$v] as $ke => $va) {
		
			$a_size = explode( ',' , $va['size'] );
			
			if( $ke == 0 ) {
			
				# 宣告設定尺寸的初始值
				foreach($a_size as $sk => $sv){
					if(!empty($gt[$sk])) $gt[$sk] = 0;
					if(!empty($tt[$sk])) $tt[$sk] = 0;
				}
				
			}
			
			$a_qty = explode( ',' , $va['size_qty'] );
			foreach($a_size as $sk => $sv){
				@$gt[$sk] += $a_qty[$sk];
				@$tt[$sk] += $a_qty[$sk];
			}
			
		
			# 塞 mo 做索引的標示
			$mo[] =  array( 
				'mk' 		=> 'mk'.$k , 
				'count' 	=> ( count($arow[$v]) - 1 ) , 
				'mo' 		=> $ke , 
				'cust_po' 	=> $va , 
				'size' 		=> $a_size , 
				'qty' 		=> $a_qty  , 
				'sub_ttl' 	=> $tt  , 
				'gd_ttl' 	=> $gt , 
				't_ttl' 	=> array_sum($a_qty) , 
				's_ttl' 	=> array_sum($tt) 
			);
		}
	}

	$row['invoice'] = $invoice;
	$row['charge'] = $this->get_charge($id);
	$row['file'] = $this->get_file($id);
	$row['status'] = $this->get_status($invoice['status']);
	$row['PHP_id'] = $id;
	$row['row'] = $mo;
	$row['t_qty'] = $t_qty;
	$row['t_amt'] = $t_amt;
	//print_r($row);
	//exit;
	
	return $row;
} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_invoice_new($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_invoice_new($id) {

	$sql = $this->sql;

	if (!$id) {
		$this->msg->add("Error ! please check record ID.");		    
		return false;
	}

	$q_str = "
	SELECT `shipping_doc`.* , `consignee`.`f_name` 
	FROM `shipping_doc` LEFT JOIN `consignee` on ( `shipping_doc`.`consignee_id` = `consignee`.`id` ) 
	WHERE `shipping_doc`.`id` = '".$id."' ; ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	if (!$row = $sql->fetch($q_result)) {
		$this->msg->add("Error ! can't find these record!");
		return false;    
	}

	$po_user = $GLOBALS['user']->get(0,$row['submit_user']);
	$row['submit_id'] = $row['submit_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];

	$po_user = $GLOBALS['user']->get(0,$row['cfm_user']);
	$row['cfm_id'] = $row['cfm_user'];
	if ($po_user['name'])$row['cfm_user'] = $po_user['name'];

	$po_user = $GLOBALS['user']->get(0,$row['apv_user']);
	$row['apv_id'] = $row['apv_user'];
	if ($po_user['name'])$row['apv_user'] = $po_user['name'];

	$tmp = @explode('|',$row['vendor']);
	if (!isset($tmp[1]))$tmp[1]='';
	$row['vendor_name'] = $tmp[0];
	$row['vendor_addr'] = $tmp[1];

	$csn = @$GLOBALS['cust']->get_csge('', $row['consignee']);
	$row['consignee_name'] = $csn['f_name'];
	$row['consignee_addr'] = $csn['addr'];

	$invoice = $row;
	
	$q_str = "
	SELECT `invoice_import`.* , `size_des`.`size` as `ord_size`, `s_order`.`id` as `o_id` 

	FROM `invoice_import` , `s_order` , `size_des` 
	WHERE 
	`invoice_import`.`ord_num` = `s_order`.`order_num` AND 
	`s_order`.`size` = `size_des`.`id` AND 
	`invoice_import`.`shipping_doc_id` ='".$id."' 

	ORDER BY `invoice_import`.`ord_num`, `invoice_import`.`id` ";
	
	//echo $q_str;
	//exit;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$t_qty = 0 ;
	$t_amt = 0 ;
	$i=0;
	$tmp_color='';
	//$tmp_color_index=0;
	while ($row = $sql->fetch($q_result)) {
		$i++;
		//明天把相同PO的顏色尺碼區分
		//print_r($row);
		//echo "1";
		//echo "<br>".$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i.$i; 
		$arow['cust_po'][] = $row['po'];
		//一張PO可能多顏色多尺碼(也可能是單顏色多尺碼或單尺碼，甚至沒顏色沒尺碼)
		$arow['po_det'][$row['po']]['color'][$row['color']][$row['size']]['qty'] = $row['qty'];
		$arow['po_det'][$row['po']]['color'][$row['color']][$row['size']]['fob'] = $row['fob'];
		$arow['po_det'][$row['po']]['order'][$row['ord_num']][$row['color']][$row['size']]['qty'] = $row['qty'];
		$arow['po_det'][$row['po']]['order'][$row['ord_num']][$row['color']][$row['size']]['fob'] = $row['fob'];
		if(strtoupper($row['color']) != 'HANGER')
		{
			$t_qty += $row['qty'] ;//各PO的件數加總
		}
		$t_amt += $row['amount'] ;//各PO的總價加總
	}
	//print_r($arow);
	$mo = array();
	
	$arow['cust_po'] = array_unique($arow['cust_po']);
	
	$color_span = 0;
	$order_span =0;
	$po_span = 0;
	//$color_amount=0;
	$po_amount = 0;
	$order_amount=0;
	$tmp_po='';
	$tmp_order='';
	foreach($arow['cust_po'] as $k_po => $v_po) {
		$tmp_po=$v_po;
		foreach($arow['po_det'][$v_po]['color'] as $k_color => $v_color) {
			
			foreach($v_color as $k_size => $v_size) {
				//echo $k_size."<br>";
				$arow['po_det'][$v_po]['color'][$k_color][$k_size]['amount'] = $v_size['qty']*$v_size['fob'];
				$po_amount = $po_amount + ($v_size['qty']*$v_size['fob']);
				$color_amount=$color_amount + ($v_size['qty']*$v_size['fob']);
				$color_span++;
				$po_span++;
			}
			$arow['po_det'][$v_po]['color'][$k_color]['color_amount'] = $color_amount;
			$arow['po_det'][$v_po]['color'][$k_color]['color_span'] = $color_span;
			$color_amount=0;
			$color_span =0;
			
		}
		foreach($arow['po_det'][$v_po]['order'] as $key_order => $val_order) {
			$tmp_order = $key_order;
			$order_amount = 0;
			foreach($val_order as $key_color => $val_color)
			{
				foreach($val_color as $key_size => $val_size)
				{
					$order_amount = $order_amount + ($val_size['qty']*$val_size['fob']);
					$color_amount=$color_amount + ($val_size['qty']*$val_size['fob']);
					$arow['po_det'][$v_po]['order'][$key_order][$key_color][$key_size]['amount'] = $val_size['qty']*$val_size['fob'];
					$color_span++;
					$order_span++;
				}
				$arow['po_det'][$v_po]['order'][$key_order][$key_color]['color_amount']=$color_amount;
				$arow['po_det'][$v_po]['order'][$key_order][$key_color]['color_span']=$color_span;
				$color_amount=0;
				$color_span=0;
			}
			$arow['po_det'][$v_po]['order'][$key_order]['order_amount'] = $order_amount;
			$arow['po_det'][$v_po]['order'][$key_order]['order_span'] = $order_span;
			$order_span=0;
		}
		$arow['po_det'][$v_po]['po_amount'] = $po_amount;
		$arow['po_det'][$v_po]['po_span'] = $po_span;
		$po_amount =0;
		$po_span=0;
	}
	//$arow['amount'] =$po_amount;
	/* print_r($arow);
	exit; */
	/*
	# grand total
	$gt=array();
	
	foreach($arow['cust_po'] as $k => $v) {
	
		# sub total
		$tt=array();
		
		foreach($arow[$v] as $ke => $va) {
		
			$a_size = explode( ',' , $va['ord_size'] );
			
			if( $ke == 0 ) {
			
				# 宣告設定尺寸的初始值
				foreach($a_size as $sk => $sv){
					if(!empty($gt[$sk])) $gt[$sk] = 0;
					if(!empty($tt[$sk])) $tt[$sk] = 0;
				}
				
			}
			
			$a_qty = explode( ',' , $va['size_qty'] );//輸入INVOICE各尺碼數量//未來用不到
			foreach($a_size as $sk => $sv){
				@$gt[$sk] += $a_qty[$sk];
				@$tt[$sk] += $a_qty[$sk];
			}
			
		
			# 塞 mo 做索引的標示
			$mo[] =  array( 
				'mk' 		=> 'mk'.$k , 
				'count' 	=> ( count($arow[$v]) - 1 ) , 
				'mo' 		=> $ke , 
				'cust_po' 	=> $va , 
				'size' 		=> $a_size , 
				'qty' 		=> $a_qty  , 
				'sub_ttl' 	=> $tt  , 
				'gd_ttl' 	=> $gt , 
				't_ttl' 	=> array_sum($a_qty) , 
				's_ttl' 	=> array_sum($tt) 
			);
		}
	}
	*/
	//print_r($arow);
	$row['invoice'] = $invoice;
	$row['charge'] = $this->get_charge($id);
	$row['file'] = $this->get_file($id);
	$row['status'] = $this->get_status($invoice['status']);
	$row['PHP_id'] = $id;
	$row['row'] = $arow;
	$row['t_qty'] = $t_qty;
	$row['t_amt'] = $t_amt;
	
	return $row;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_charge($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_charge($id) {

	$sql = $this->sql;

	$q_str = "
	SELECT `id` , `des` , `charge`

	FROM `shipping_doc_charge` 
	
	WHERE `s_id` = '".$id."';";
	
	// echo $q_str.'<br>';							
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$rows = array();
	while ($row = $sql->fetch($q_result)) {
	
		$rows[] = $row;

	}
	
	return $rows;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_file($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_file($id) {
	
	$sql = $this->sql;

	$q_str = "
	SELECT `id` , `file_des` , `file_name` , `file_user` , `file_date`

	FROM `shipping_doc_file` 
	
	WHERE `s_id` = '".$id."';";

	// echo $q_str;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$rows = array();
	while ($row = $sql->fetch($q_result)) {
	
		$row['file_user'] = $GLOBALS['user']->get(0,$row['file_user']);
		
		$rows[] = $row;

	}
	
	return $rows;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_invoice_by_edit($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_invoice_by_edit($id) {

	$sql = $this->sql;

	if (!$id) {
		$this->msg->add("Error ! please check record ID.");		    
		return false;
	}

	$q_str = "
	SELECT DISTINCT 
	`shipping_doc`.`inv_num` , `shipping_doc`.`cust` , `shipping_doc`.`consignee_id` , `shipping_doc`.`ship_date` , 
	`shipping_doc_qty`.`id` as `q_id` , `shipping_doc_qty`.`cust_po` , `shipping_doc_qty`.`colorway` , `shipping_doc_qty`.`w_id` , `shipping_doc_qty`.`size_qty` , `shipping_doc_qty`.`ttl_qty` , `shipping_doc_qty`.`ship_fob` , `shipping_doc_qty`.`amount` , 
	`s_order`.`order_num` as `num` , `s_order`.`id` as `o_id` , `s_order`.`factory` as `fty` 

	FROM `shipping_doc` , `shipping_doc_qty` , `s_order` , `size_des` 
	
	WHERE 
	`shipping_doc_qty`.`ord_num` = `s_order`.`order_num` AND 
	`shipping_doc`.`id` = `shipping_doc_qty`.`s_id` AND 
	`shipping_doc`.`id` = '".$id."' 

	ORDER BY `shipping_doc_qty`.`ord_num` ";
	
	// echo $q_str;							
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row = $sql->fetch($q_result)) {
		# 搜尋原始數量檔案
		$rows[$row['num']] = array( 'id' => $row['o_id'] , 'num' => $row['num'] , 'sort' => $row['num'] , 'fty' => $row['fty'] );
		
		# 基本資料
		$rowr['cust'] = $row['cust'];
		$rowr['consignee_id'] = $row['consignee_id'];
		$rowr['inv_num'] = $row['inv_num'];
		$rowr['ship_date'] = $row['ship_date'];
		
		# 該訂單數量檔案
		$rowr['td'][] = array( 'q_id' => $row['q_id'] , 'num' => $row['num'] , 'cust_po' => $row['cust_po'] , 'w_id' => $row['w_id'] , 'colorway' => $row['colorway'] , 'sqty' => $row['size_qty'] , 'qty' => explode( ',' , $row['size_qty']) , 'ttl' => $row['ttl_qty'] , 'fob' => $row['ship_fob'] , 'amount' => $row['amount'] );
		
	}
	
	$rowr['invoice'] = $this->get_invoice($id);
	
	$rowr['ord'] = $this->get_colorway($rows);
	
	foreach($rowr['ord'] as $k => $v){
		foreach($v['qty_plus'] as $ke => $va){
			# 扣除已核可確認的數量
			$q_str = "
			SELECT `shipping_doc_qty`.`size_qty` , `shipping_doc_qty`.`ship_fob` 

			FROM `shipping_doc` , `shipping_doc_qty` 
			WHERE 
			`shipping_doc_qty`.`ord_num` = '".$v['num']."' AND 
			`shipping_doc_qty`.`colorway` = '".$va['ship_color']."' AND 
			`shipping_doc`.`id` = `shipping_doc_qty`.`s_id` AND 
			`shipping_doc`.`id` != '".$id."' AND 
			`shipping_doc`.`status` = '3' ";

			if($q_result = $sql->query($q_str)){
				while ($row = $sql->fetch($q_result)){
					$size_qty = explode( ',' , $row['size_qty']);
					foreach($size_qty as $key => $val){
						$rowr['ord'][$k]['qty_plus'][$ke]['qty'][$key] -= $val;
					}
				}
			}
		}
	}
	
	$rowr['charge'] = $this->get_charge($id);
	
	$rowr['file'] = $this->get_file($id);
	
	return $rowr;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->del_color_qty($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_color_qty($id) {

	$sql = $this->sql;

	$q_header = "DELETE FROM `shipping_doc_qty` WHERE `id` = '".$id."' ;";
	if (!$q_result = $sql->query($q_header)) {
		return false;
	} else {
		return true;
	}
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mon_shp($fty,$str,$limit_entries)	搜尋 月份 出口訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_invoice_no($invoice) {

	$sql = $this->sql;
	
	$op = array();

	$q_str = "SELECT * FROM `shipping_doc` WHERE `inv_num` ='".$invoice."';";
	
	$q_result = $sql->query($q_str);		
	$row = $sql->fetch($q_result);

	$row = ( !empty($row) ) ? 'in' : 'null';
	
	return $row;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->add_shipping($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_shipping($inv_num,$consignee_id,$cust,$factory,$ttl_qty,$ttl_amt,$ship_date) {

	$sql = $this->sql;

	echo $q_str = "INSERT INTO `shipping_doc` ( inv_num,consignee_id,cust,factory,ttl_qty,ttl_amt,ship_date,open_user,open_date )
	VALUES ('".$inv_num."','".$consignee_id."','".$cust."','".$factory."','".$ttl_qty."','".$ttl_amt."','".$ship_date."','".$GLOBALS['SCACHE']['ADMIN']['login_id']."','".date('Y-m-d')."')";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	$ship_id = $sql->insert_id();  //取出 新的 id

	return $ship_id;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->add_shipping_doc_qty($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_shipping_doc_qty($s_id,$ord_num,$cust_po,$w_id,$colorway,$size_qty,$ttl_qty,$ship_fob,$amount) {

	$sql = $this->sql;

	$q_str = "INSERT INTO `shipping_doc_qty` ( s_id,ord_num,cust_po,w_id,colorway,size_qty,ttl_qty,ship_fob,amount )
	VALUES ('".$s_id."','".$ord_num."','".$cust_po."','".$w_id."','".$colorway."','".$size_qty."','".$ttl_qty."','".$ship_fob."','".$amount."')";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->add_shipping_doc_charge($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_shipping_doc_charge($s_id,$charge,$des) {

	$sql = $this->sql;

	$q_str = "INSERT INTO `shipping_doc_charge` ( s_id,des,charge )
	VALUES ('".$s_id."','".$des."','".$charge."' )";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;

} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->add_shipping_doc_file($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_shipping_doc_file($s_id,$file_name,$file_des,$file_user,$file_date) {

	$sql = $this->sql;

	$q_str = "INSERT INTO `shipping_doc_file` ( s_id,file_des,file_name,file_user,file_date )
	VALUES ('".$s_id."','".$file_des."','".$file_name."','".$GLOBALS['SCACHE']['ADMIN']['login_id']."','".date('Y-m-d')."' )";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->update_shipping($parm)		更新
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_shipping( $id , $invoice , $ship_date , $consignee , $ttl_qty , $ttl_amt ) {

	$sql = $this->sql;
    
	$q_str = "UPDATE `shipping_doc` SET 
	`inv_num` 		= '".$invoice."' , 
	`ship_date` 	= '".$ship_date."' , 
	`consignee_id` 	= '".$consignee."' , 
	`ttl_qty` 		= '".$ttl_qty."' , 
	`ttl_amt` 		= '".$ttl_amt."' 
	WHERE `id` 		= '".$id."';";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  can't update database.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return true;

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->update_shipping_doc_qty($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_shipping_doc_qty($id,$size_qty,$ttl_qty,$ship_fob,$amount) {

	$sql = $this->sql;

	$q_str = "UPDATE `shipping_doc_qty` SET	
	`size_qty`	= '".$size_qty."' , 
	`ttl_qty`	= '".$ttl_qty."' , 
	`ship_fob`	= '".$ship_fob."' , 
	`amount` 	= '".$amount."' 
	WHERE `id` 	= '".$id."';";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->update_shipping_doc_charge($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_shipping_doc_charge($id,$charge,$des) {

	$sql = $this->sql;

	$q_str = "UPDATE `shipping_doc_charge` SET	
	`charge`	= '".$charge."' , 
	`des`	= '".$des."' 
	WHERE `id` 	= '".$id."';";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->update_shipping_doc_file($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_shipping_doc_file($id,$des) {

	$sql = $this->sql;

	$q_str = "UPDATE `shipping_doc_file` SET	
	`file_des`		= '".$des."' ,
	`file_user`		= '".$GLOBALS['SCACHE']['ADMIN']['login_id']."' ,
	`file_date`		= '".date('Y-m-d')."' 
	WHERE `id` 		= '".$id."';";
	// echo $q_str;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->ship_invoice_revise($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ship_invoice_revise($id) {

	$sql = $this->sql;

	$q_str = "UPDATE `shipping_doc` SET	
	`submit_user`	= '' ,
	`submit_date`	= '' ,
	`cfm_user`		= '' ,
	`cfm_date`		= '' ,
	`rev_user`		= '".$GLOBALS['SCACHE']['ADMIN']['login_id']."' ,
	`rev_date`		= '".date('Y-m-d')."' , 
	`status`		= '0' 
	WHERE `id` 		= '".$id."';";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->drop_charge($id) 刪除
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function drop_charge($id) {

	$sql = $this->sql;

	$q_header = "DELETE FROM `shipping_doc_charge` WHERE `id` = '".$id."';";
	if (!$q_result = $sql->query($q_header)) {
		$this->msg->add("Error! 無法存取資料庫!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->drop_file($id) 刪除
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function drop_file($id) {

	$sql = $this->sql;

	$q_header = "DELETE FROM `shipping_doc_file` WHERE `id` = '".$id."';";
	if (!$q_result = $sql->query($q_header)) {
		$this->msg->add("Error! 無法存取資料庫!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->ship_invoice_submit()
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ship_invoice_submit( $id ) {

	$sql = $this->sql;
    
	$q_str = "UPDATE `shipping_doc` SET 
	`submit_user` 	= '".$GLOBALS['SCACHE']['ADMIN']['login_id']."' , 
	`submit_date` 	= '".date('Y-m-d')."' , 
	`status` 		= '1' 
	WHERE `id` 		= '".$id."';";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  can't update database.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return true;

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->ship_invoice_confirm()
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ship_invoice_confirm( $id ) {

	$sql = $this->sql;
    
	$q_str = "UPDATE `shipping_doc` SET 
	`cfm_user` 	= '".$GLOBALS['SCACHE']['ADMIN']['login_id']."' , 
	`cfm_date` 	= '".date('Y-m-d')."' , 
	`status` 		= '2' 
	WHERE `id` 		= '".$id."';";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  can't update database.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return true;

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->search($mode=0,$where_str='') 搜尋 資料
# mode = 2 wait for packing confirm
# mode = 4 wait for invoice submit/reject
# mode = 6 wait for invoice confirm
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search( $mode=0,$where_str="" ) {

	$sql = $this->sql;
	
	$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "SELECT DISTINCT `shipping_doc`.*, `cust`.`cust_init_name` as `cust_init`  
							 FROM `shipping_doc` , `shipping_doc_qty` , `cust` ";
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_sort_condition("`shipping_doc`.`inv_num` DESC");
	$srh->row_per_page = 10;
	$pagesize = 10;

	if ($argv['PHP_sr_startno']) {
		$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
	}else{
		$pages = $srh->get_page(1,$pagesize);
	} 

	$srh->add_where_condition("`shipping_doc`.`cust` = `cust`.`cust_s_name`");
	$srh->add_where_condition("`shipping_doc`.`id` = `shipping_doc_qty`.`s_id`");

	if ($mode){
		if ($str = $argv['SCH_inv'] )  {
			$srh->add_where_condition("`shipping_doc`.`inv_num` like '%$str%'", "SCH_inv",$str," Invoice : [ $str ]. "); }
		if ($str = $argv['SCH_date_str'] )  {
			$srh->add_where_condition("`shipping_doc`.ship_date >= '$str'", "SCH_date",$str," SHIP DATE > [ $str ]. "); }
		if ($str = $argv['SCH_date_end'] )  {
			$srh->add_where_condition("`shipping_doc`.ship_date <= '$str'", "SCH_date",$str," SHIP DATE < [ $str ]. "); }
		if ($str = $argv['SCH_des'] )  {
			$srh->add_where_condition("`shipping_doc`.des like '%$str%'", "SCH_des",$str," Description =[ $str ]. "); }
		if ($str = $argv['PHP_cust'] )  {
			$srh->add_where_condition("`shipping_doc`.cust = '$str'", "SCH_cust",$str," Customer =[ $str ]. "); }
		if ($str = $argv['PHP_fty'] )  {
			$srh->add_where_condition("`shipping_doc`.factory = '$str'", "SCH_fty",$str," Factory =[ $str ]. "); }
		if ($str = $argv['SCH_ord'] )  {
			$srh->add_where_condition("`shipping_doc_qty`.`ord_num` = '$str'", "SCH_ord",$str," ORDER# =[ $str ]. "); }
	}

	if ($mode==2){
		$srh->add_where_condition("`shipping_doc`.`status` = 1"); 
	}

	if ($mode==4){
		$srh->add_where_condition("`shipping_doc`.`status` >= 4"); 
	}

	if ($mode==6){
		$srh->add_where_condition("`shipping_doc`.`status` = 6"); 
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
    // echo $srh->q_str;
	$op['ship_doc'] = $result;  // 資料錄 拋入 $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;
	$op['maxpage'] =$srh->get_max_page();
	$op['pages'] =  $pages;
	$op['now_pp'] = $srh->now_pp;
	$op['lastpage']=$pages[$pagesize-1];		

	return $op;
} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->search_new($mode=0,$where_str='') 搜尋 資料，將shipping_doc_qty改為invoice_import
# mode = 2 wait for packing confirm
# mode = 4 wait for invoice submit/reject
# mode = 6 wait for invoice confirm
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_new( $mode=0,$where_str="" ) {

	$sql = $this->sql;
	
	$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "SELECT DISTINCT `shipping_doc`.*, `cust`.`cust_init_name` as `cust_init`  
							 FROM `shipping_doc` , `invoice_import` , `cust` ";
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_sort_condition("`shipping_doc`.`inv_num` DESC");
	$srh->row_per_page = 10;
	$pagesize = 10;

	if ($argv['PHP_sr_startno']) {
		$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
	}else{
		$pages = $srh->get_page(1,$pagesize);
	} 

	$srh->add_where_condition("`shipping_doc`.`cust` = `cust`.`cust_s_name`");
	$srh->add_where_condition("`shipping_doc`.`id` = `invoice_import`.`shipping_doc_id`");

	if ($mode){
		if ($str = $argv['SCH_inv'] )  {
			$srh->add_where_condition("`shipping_doc`.`inv_num` like '%$str%'", "SCH_inv",$str," Invoice : [ $str ]. "); }
		if ($str = $argv['SCH_date_str'] )  {
			$srh->add_where_condition("`shipping_doc`.ship_date >= '$str'", "SCH_date",$str," SHIP DATE > [ $str ]. "); }
		if ($str = $argv['SCH_date_end'] )  {
			$srh->add_where_condition("`shipping_doc`.ship_date <= '$str'", "SCH_date",$str," SHIP DATE < [ $str ]. "); }
		if ($str = $argv['SCH_des'] )  {
			$srh->add_where_condition("`shipping_doc`.des like '%$str%'", "SCH_des",$str," Description =[ $str ]. "); }
		if ($str = $argv['PHP_cust'] )  {
			$srh->add_where_condition("`shipping_doc`.cust = '$str'", "SCH_cust",$str," Customer =[ $str ]. "); }
		if ($str = $argv['PHP_fty'] )  {
			$srh->add_where_condition("`shipping_doc`.factory = '$str'", "SCH_fty",$str," Factory =[ $str ]. "); }
		if ($str = $argv['SCH_ord'] )  {
			$srh->add_where_condition("`invoice_import`.`ord_num` = '$str'", "SCH_ord",$str," ORDER# =[ $str ]. "); }
	}

	if ($mode==2){
		$srh->add_where_condition("`shipping_doc`.`status` = 1"); 
	}

	if ($mode==4){
		$srh->add_where_condition("`shipping_doc`.`status` >= 4"); 
	}

	if ($mode==6){
		$srh->add_where_condition("`shipping_doc`.`status` = 6"); 
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
    // echo $srh->q_str;
	$op['ship_doc'] = $result;  // 資料錄 拋入 $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['max_no'] = $srh->max_no;
	$op['start_no'] = $srh->start_no;
	$op['maxpage'] =$srh->get_max_page();
	$op['pages'] =  $pages;
	$op['now_pp'] = $srh->now_pp;
	$op['lastpage']=$pages[$pagesize-1];		

	return $op;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_mm_rpt_tiptop($etd)  出貨月報表 for tiptop
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_mm_rpt_tiptop($PHP_year,$PHP_month,$fty,$check='',$newold='') {
// function get_mm_rpt_tiptop($etd,$fty,$check='') {

	$sql = $this->sql;

	$ord_rec = array();
	
		if( $PHP_year == '2015' && $PHP_month == '04' ) {
			$where = " `shipping_doc`.`ship_date` between '".$PHP_year.'-'.$PHP_month."-01' AND '".$PHP_year.'-'.$PHP_month."-25' AND ";
		} else if($PHP_year=='2015'&&$PHP_month>'04'){
			$before = date("Y-m", mktime(0, 0, 0, $PHP_month-1, 01, $PHP_year));
			$where = " `shipping_doc`.`ship_date` between '".$before."-26' AND '".$PHP_year.'-'.$PHP_month."-25' AND ";
		} else {
			$where = " `shipping_doc`.`ship_date` like '".($PHP_year.'-'.$PHP_month)."%' AND ";
		}
	
	if($newold == 'new')
	{
		// 
		# 調整結帳計算日期 
		//
		
		if( $check ){
			//invoice_import
			$q_str = "
			SELECT 
			`shipping_doc`.`id` , 
			`shipping_doc`.`inv_num` , 
			`shipping_doc`.`ship_date` , 
			`consignee`.`f_name` as `buyer` , 
			`consignee`.`code` , 
			`invoice_import`.`ord_num` , 
			`invoice_import`.`po` as cust_po , 
			`invoice_import`.`color` as color ,
			`invoice_import`.`qty` as ttl_qty , 
			`invoice_import`.`fob` as ship_fob , 
			`invoice_import`.`amount` 

			FROM 
			`invoice_import` , `consignee` , `shipping_doc` 

			WHERE 
			".$where." 
			`shipping_doc`.`factory` = '".$fty."' AND 
			`shipping_doc`.`status` = '2' AND 
			`shipping_doc`.`consignee_id` = `consignee`.`id` AND 
			`shipping_doc`.`id` = `invoice_import`.`shipping_doc_id` 
			
			GROUP BY 
			`invoice_import`.`id`
			
			ORDER BY 
			`shipping_doc`.`inv_num` ASC , `invoice_import`.`ord_num` ASC
			
			";
			/* echo $q_str;
			exit;  */
		
		} else {
		
			$q_str = "
			SELECT 
			`shipping_doc`.`id` , 
			`shipping_doc`.`ship_date` , 
			`shipping_doc`.`ttl_qty` , 
			`shipping_doc`.`ttl_amt` , 
			`shipping_doc`.`inv_num` , 
			`invoice_import`.`ord_num` , 
			`consignee`.`f_name` , 
			`consignee`.`code` , 
			`s_order`.`dept` , 
			`dept`.`chief` , 
			`user`.`emp_id` , 
			`cust`.`uni_no`  

			FROM 
			`invoice_import` , `s_order` , `dept` , `user` , `cust` , `consignee` , `shipping_doc`

			WHERE 
			".$where." 
			`shipping_doc`.`status` = '2' AND 
			`shipping_doc`.`cust` = `cust`.`cust_s_name` AND 
			`shipping_doc`.`consignee_id` = `consignee`.`id` AND 
			`shipping_doc`.`factory` = '".$fty."' AND 
			`shipping_doc`.`id` = `invoice_import`.`shipping_doc_id` AND 
			
			`invoice_import`.`ord_num` = `s_order`.`order_num` AND  
			`s_order`.`dept` = `dept`.`dept_code` AND  
			`dept`.`chief` = `user`.`login_id` 

			GROUP BY 
			`shipping_doc`.`inv_num` ";
			/* echo "<br>";
			echo $q_str;
			exit;  */
		
		}
		/* echo $q_str;
		exit; */
		//echo $q_str.'<br>';
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$mo = array();
		while ($row = $sql->fetch($q_result)) {
		
			$sd = $this->get_charge($row['id']);
			$s_c = 0 ;
			foreach($sd as $a => $k) $s_c += $k['charge'];
			$row['charge'] = $s_c;	

			if( $check == 'on' ) $mo[$row['inv_num']][] = $row['charge'];
			if( count($mo[$row['inv_num']]) > 1 ) $row['charge'] = '';
			
			$ord_rec[] = $row;
		}
	}
	else
    {
		// 
		# 調整結帳計算日期 
		//
		
		if( $check ){

			$q_str = "
			SELECT 
			`shipping_doc`.`id` , 
			`shipping_doc`.`inv_num` , 
			`shipping_doc`.`ship_date` , 
			`consignee`.`f_name` as `buyer` , 
			`consignee`.`code` , 
			`shipping_doc_qty`.`ord_num` , 
			`shipping_doc_qty`.`cust_po` , 
			`shipping_doc_qty`.`ttl_qty` , 
			`shipping_doc_qty`.`ship_fob` , 
			`shipping_doc_qty`.`amount` 

			FROM 
			`shipping_doc_qty` , `consignee` , `shipping_doc` 

			WHERE 
			".$where." 
			`shipping_doc`.`factory` = '".$fty."' AND 
			`shipping_doc`.`status` = '2' AND 
			`shipping_doc`.`consignee_id` = `consignee`.`id` AND 
			`shipping_doc`.`id` = `shipping_doc_qty`.`s_id` 
			
			GROUP BY 
			`shipping_doc_qty`.`id`
			
			ORDER BY 
			`shipping_doc`.`inv_num` ASC , `shipping_doc_qty`.`ord_num` ASC
			
			";
		
		} else {
		
			$q_str = "
			SELECT 
			`shipping_doc`.`id` , 
			`shipping_doc`.`ship_date` , 
			`shipping_doc`.`ttl_qty` , 
			`shipping_doc`.`ttl_amt` , 
			`shipping_doc`.`inv_num` , 
			`shipping_doc_qty`.`ord_num` , 
			`consignee`.`f_name` , 
			`consignee`.`code` , 
			`s_order`.`dept` , 
			`dept`.`chief` , 
			`user`.`emp_id` , 
			`cust`.`uni_no`  

			FROM 
			`shipping_doc_qty` , `s_order` , `dept` , `user` , `cust` , `consignee` , `shipping_doc`

			WHERE 
			".$where." 
			`shipping_doc`.`status` = '2' AND 
			`shipping_doc`.`cust` = `cust`.`cust_s_name` AND 
			`shipping_doc`.`consignee_id` = `consignee`.`id` AND 
			`shipping_doc`.`factory` = '".$fty."' AND 
			`shipping_doc`.`id` = `shipping_doc_qty`.`s_id` AND 
			
			`shipping_doc_qty`.`ord_num` = `s_order`.`order_num` AND  
			`s_order`.`dept` = `dept`.`dept_code` AND  
			`dept`.`chief` = `user`.`login_id` 

			GROUP BY 
			`shipping_doc`.`inv_num` ";
		
		}
		
		//echo $q_str.'<br>';
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$mo = array();
		while ($row = $sql->fetch($q_result)) {
		
			$sd = $this->get_charge($row['id']);
			$s_c = 0 ;
			foreach($sd as $a => $k) $s_c += $k['charge'];
			$row['charge'] = $s_c;	

			if( $check == 'on' ) $mo[$row['inv_num']][] = $row['charge'];
			if( count($mo[$row['inv_num']]) > 1 ) $row['charge'] = '';
			
			$ord_rec[] = $row;
		}
	}
	
	/* print_r($ord_rec);
	exit; */
	/*
	$rtn = array();
	for($i=0; $i<sizeof($rec); $i++)
	{
		//取得訂單相關資料			
		$q_str = "
		SELECT 
		s_order.etd, s_order.fty_cm, s_order.cust, cust.cust_f_name as cust_name, style_type.des as style
		
		FROM 
		s_order, style_type, cust
		
		WHERE 
		s_order.cust = cust.cust_s_name AND s_order.style = style_type.style_type AND order_num = '".$rec[$i]['ord_num']."'";
		
		//echo $q_str."<BR>";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$rec[$i]['etd'] = $row['etd'];
		$rec[$i]['fty_cm'] = $row['fty_cm'];
		$rec[$i]['cust'] = $row['cust'];
		$rec[$i]['cust_name'] = $rec[$i]['consignee'];
		$style = explode(' ',$row['style']);
		$rec[$i]['style'] = $style[0];
		
		$rec[$i]['amount'] = $rec[$i]['qty'] * $rec[$i]['ship_fob'];
		$rec[$i]['cm_amt'] = $rec[$i]['qty'] * $rec[$i]['fty_cm'];
		
		//依客戶和目的地分層			
		// $fst_key = $rec[$i]['consignee'].'_'.$rec[$i]['ship_to'];
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
		// echo $key."<BR>";
		foreach($rtn[$key]['inv'] as $s_key => $s_value)
		{
			// echo '  '.$s_key.'====>'.sizeof($rtn[$key]['inv'][$s_key]['det'])."<BR>";				
			$rtn[$key]['inv'][$s_key]['ticket_fee'] = $rtn[$key]['inv'][$s_key]['damage_fee'] = $rtn[$key]['inv'][$s_key]['other_fee'] = 0;
			$q_str = "SELECT `charge` FROM `shipping_doc_charge` WHERE  `s_id` = '".$s_key."' AND `des` like '%PRICE TICKET%'";
			// echo $q_str."<BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['ticket_fee'] += $row['charge'];

			$q_str = "SELECT `charge` FROM `shipping_doc_charge` WHERE  `s_id` = '".$s_key."' AND `des` like '%DAMAGE%'";
			// echo $q_str."<BR>";				
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['damage_fee'] += $row['charge'];

			$q_str = "SELECT `charge` FROM `shipping_doc_charge` WHERE  `s_id` = '".$s_key."' AND !(`des` like '%DAMAGE%') AND !(`des` like '%PRICE TICKET%')";
			// echo $q_str."<BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result)) $rtn[$key]['inv'][$s_key]['other_fee'] += $row['charge'];
			$rtn[$key]['inv'][$s_key]['amount'] = $rtn[$key]['inv'][$s_key]['amount']- $rtn[$key]['inv'][$s_key]['ticket_fee'] - $rtn[$key]['inv'][$s_key]['damage_fee'] - $rtn[$key]['inv'][$s_key]['other_fee'];
			$rtn[$key]['amount'] = $rtn[$key]['amount'] - $rtn[$key]['inv'][$s_key]['ticket_fee'] - $rtn[$key]['inv'][$s_key]['damage_fee'] - $rtn[$key]['inv'][$s_key]['other_fee'];
		}
	}	
*/
	return $ord_rec;
} // end func






function get_cost($order_num){

	$sql = $this->sql;
	
	$q_str = "
	SELECT 
	sum(`ttl_qty`) as `ttl_qty` , sum(`amount`) as `amount`  , `ship_fob`
	
	FROM 
	`shipping_doc_qty`
	
	WHERE 
	`ord_num` = '".$order_num."' 
	
	GROUP BY `ord_num` 
	;";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	// echo '<br>'.$q_str.'<br>'; #  rate_date
	$row = array();
	$row = $sql->fetch($q_result);

	return $row;
} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->ship_rmk($id,$remark)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ship_rmk($id,$remark) {

	$sql = $this->sql;

	# 更新訂單colorway
	$q_str = "UPDATE `shipping_doc` SET `remark` = '".$remark."' WHERE `id` = '".$id."' ;";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	return true;
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_inv_info($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_inv_info($t_sql) {

	$sql = $this->sql;

	/* $q_str = "
	SELECT `id` , `des` , `charge`

	FROM `shipping_doc_charge` 
	
	WHERE `s_id` = '".$id."';"; */
	
	// echo $q_str.'<br>';							
	if (!$q_result = $sql->query($t_sql)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$rows = array();
	while ($row = $sql->fetch($q_result)) {
	
		$rows[] = $row;

	}
	
	return $rows;
} // end func

} // end class
?>