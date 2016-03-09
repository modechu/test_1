<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->bom_search($supl,$cat)	查詢BOM的主副料


#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class APPLY {
		
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
	function get_fab_eta($ord_num) {

		$sql = $this->sql;
		$eta_date='';
		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
/*		
		$q_str = "SELECT ap_mark FROM wi, bom_lots WHERE wi.id=bom_lots.wi_id AND bom_lots.ap_mark ='' AND wi.wi_num = '".$ord_num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
	   return false;
		}
*/
		############ 當 status有未apv時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購完成)
		$q_str = "SELECT min(ap.status) as status, max(ap_det.po_eta) as eta FROM wi, bom_lots, ap, ap_det WHERE
							wi.id=bom_lots.wi_id AND bom_lots.id=ap_det.bom_id AND ap.ap_num=ap_det.ap_num AND ap_det.mat_cat = 'l' AND wi.wi_num ='".$ord_num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result))
		{
			return false;
		}
		$eta_date = $row['eta'];
/*		
		if ($row['status'] < 4 || $row['status'] == 5)
		{
			return false;
		}else{
			$eta_date = $row['eta'];
		}
*/
		$q_str = "SELECT min(ap.status) as status, max(ap_special.po_eta) as eta FROM  ap, ap_special WHERE
							ap.ap_num=ap_special.ap_num AND ap_special.mat_cat = 'l' AND ap_special.ord_num ='".$ord_num."'";
//echo $q_str."<br>";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($row = $sql->fetch($q_result))
		{
/*
			if ($row['status'] &&($row['status'] < 4 || $row['status'] == 5))
			{
//				echo $row['status']."<br>here<br>";
				return false;
			}else{
				if ($row['eta'] > $eta_date) $eta_date = $row['eta'];
			}
*/			
			if ($row['eta'] > $eta_date) $eta_date = $row['eta'];
		}
		if ($eta_date <> '')
		{
			$q_str = "UPDATE pdtion SET mat_etd ='".$eta_date."' WHERE order_num= '".$ord_num."'";
//echo $q_str."<br>";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}
/*			
			$etd_date = increceDaysInDate($eta_date,10);
			$q_str = "UPDATE pdtion SET mat_eta ='".$etd_date."' WHERE order_num= '".$ord_num."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}		
*/				
		}
		return $eta_date;
	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_acc_eta($ord_num,$bom_id,$fd='',$ap_num='') {

		$sql = $this->sql;
		$eta_date='';
		if (!$fd)
		{
			if($ap_num)
			{
				$q_str = "SELECT max(acc_cat) as acc_cat  FROM bom_acc, acc_use, ap WHERE ap.ap_num = bom_acc.ap_mark AND acc_use.id=bom_acc.acc_used_id AND acc_use.smpl_code = '".$ord_num."' AND ap.ap_num ='".$ap_num."'";
			}else{
				$q_str = "SELECT acc_cat FROM bom_acc, acc_use WHERE acc_use.id=bom_acc.acc_used_id AND bom_acc.id = '".$bom_id."'";
			}
//echo $q_str."<br>";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't find.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$row = $sql->fetch($q_result))
			{
				return false;
			}
			$acc_cat=$row['acc_cat'];
		if ($row['acc_cat'] == 1) {$acc_fd = "m_acc_etd";$acc_fd2="m_acc_eta";}else{$acc_fd = "acc_etd";$acc_fd2="acc_eta";}
		}else{
			$acc_cat=1;
			$acc_fd = "m_acc_etd";
			$acc_fd2 = "m_acc_eta";
		}
/*
		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
		$q_str = "SELECT ap_mark FROM wi, bom_acc WHERE wi.id=bom_acc.wi_id AND bom_acc.ap_mark ='' AND wi.wi_num = '".$ord_num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
	   return false;
		}
*/
		############ 當 status有未apv時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購完成)
		$q_str = "SELECT min(ap.status) as status, max(ap_det.po_eta) as eta FROM wi, bom_acc, ap, ap_det, acc_use WHERE
							acc_use.id = bom_acc.acc_used_id AND wi.id=bom_acc.wi_id AND bom_acc.id=ap_det.bom_id AND ap.ap_num=ap_det.ap_num AND ap_det.mat_cat = 'a' AND acc_use.acc_cat = '".$acc_cat."' AND wi.wi_num ='".$ord_num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result))
		{
			return false;
		}
/*		
		if ($row['status'] < 4 || $row['status'] == 5)
		{
			return false;
		}else{
			$eta_date = $row['eta'];
		}
*/		
		$eta_date = $row['eta'];
		if ($acc_cat == 1)
		{
			$q_str = "SELECT min(ap.status) as status, max(ap_special.po_eta) as eta FROM  ap, ap_special WHERE
								ap.ap_num=ap_special.ap_num AND ap_special.mat_cat = 'a' AND ap_special.ord_num ='".$ord_num."'";
//echo $q_str."<br>";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't find.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if ($row = $sql->fetch($q_result))
			{
/*
				if ($row['status']&&($row['status'] < 4 || $row['status'] == 5))
				{
					return false;
				}else{
					if ($row['eta'] > $eta_date) $eta_date = $row['eta'];
				}
*/				
				if ($row['eta'] > $eta_date) $eta_date = $row['eta'];
			}
		}		
		if ($eta_date <> '')
		{
			$etd_date = increceDaysInDate($eta_date,10);
			$q_str = "UPDATE pdtion SET ". $acc_fd ." = '".$eta_date."' WHERE order_num= '".$ord_num."'";
//echo $q_str."<br>";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}
/*			
			$q_str = "UPDATE pdtion SET ". $acc_fd2 ." = '".$etd_date."' WHERE order_num= '".$ord_num."'";
//echo $q_str."<br>";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}	
*/					
		}
		

		return $eta_date;
	} // end func	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order')	
#
#		同時更新兩個 field的值 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_2fields($field1, $field2, $value1, $value2, $id, $table='ap') {

$sql = $this->sql;

#####   更新資料庫內容
############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
                            "', ".$field2." ='".$value2.
                        "' WHERE id=".$id;
//echo $q_str."<br>";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error !  Database can't update.");
    $this->msg->merge($sql->msg);
    return false;    
}

    return $id;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_2fields($field1, $value1,  $id, $table='ap')	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_fields($field1, $value1, $id, $table='ap') {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE ap_num= '".	$id ."'";
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_2fields($field1, $value1,  $id, $table='ap')	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_fields_id($field1, $value1, $id, $table='ap') {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE id= '".	$id ."'";
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
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
			$buy_no=$hend."000".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$hend."00".$buy_no;
		}else if(strlen($buy_no) == 3){
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
	function add($parm1) {
					
		$sql = $this->sql;

//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm1['cust']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	
	
	
					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO ap (ap_num,dept,sup_code,cust,cust_ver,special,open_dept,au_user,au_date,ap_user,ap_date,status) 
				  VALUES('".
							$parm1['ap_num']."','".
							$parm1['dept']."','".
							$parm1['sup_code']."','".						
							$parm1['cust']."','".	
							$cust_row['ver']."','".	
							$parm1['ap_special']."','".	
							$GLOBALS['SCACHE']['ADMIN']['dept']."','".
							$parm1['ap_user']."','".
							$parm1['ap_date']."','".						
							$parm1['ap_user']."','".																									
							$parm1['ap_date']."','6')";

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

$q_str = "INSERT INTO ap_det (ap_num,bom_id,mat_cat,eta,unit,ap_qty,order_num,wi_id,mat_id,used_id,color,size) 
	  VALUES('".
				$parm['ap_num']."','".
				$parm['bom_id']."','".
				$parm['mat_cat']."','".
				$parm['eta']."','".
				$parm['unit']."','".																								
				$parm['qty']."','".	
                
				$parm['order_num']."','".																								
				$parm['wi_id']."','".																								
				$parm['mat_id']."','".																								
				$parm['used_id']."','".																								
				$parm['color']."','".																								
				$parm['size']."')";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->add("Error ! cannot append order");
	$this->msg->merge($sql->msg);
	return false;    
}
$this->msg->add("append apply#: [".$parm['ap_num']."]。") ;
$ord_id = $sql->insert_id();

return $ord_id;

} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_pp($parm) {
					
		$sql = $this->sql;
//		$parm=$tmp;
/*
		if($parm['unit']=='meter')
		{
			$parm['qty']=$parm['qty']*1.09361;	
			$parm['unit']='yd';	

		}
		if($parm['unit']=='lb')
		{
			$parm['qty']=$parm['qty']*2.20462;	
			$parm['unit']='kg';	

		}
*/
			$parm['color'] = trim($parm['color']);
			$q_str = "INSERT INTO ap_special (ap_num,ord_num,mat_code,color,mat_cat,eta,unit,use_for,ap_qty,pp_mark) 
				  VALUES('".
							$parm['ap_num']."','".
							$parm['ord_num']."','".
							$parm['mat_code']."','".
							$parm['color']."','".
							$parm['mat_cat']."','".
							$parm['eta']."','".
							$parm['unit']."','".
							$parm['use_for']."','".																								
							$parm['qty']."','1')";
			//echo $q_str;
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot append order");
				$this->msg->merge($sql->msg);
				return false;    
			}
			

		$this->msg->add("append apply#: [".$parm['ap_num']."] for special detial") ;
		$ord_id = $sql->insert_id();
		return $ord_id;

	} // end func
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_special($parm) {
					
		$sql = $this->sql;
//		$parm=$tmp;
/*
		if($parm['unit']=='meter')
		{
			$parm['qty']=$parm['qty']*1.09361;	
			$parm['unit']='yd';	

		}
		if($parm['unit']=='lb')
		{
			$parm['qty']=$parm['qty']*2.20462;	
			$parm['unit']='kg';	

		}
*/
			$parm['color'] = trim($parm['color']);
			$q_str = "INSERT INTO ap_special (ap_num,ord_num,mat_code,color,mat_cat,eta,unit,use_for,ap_qty) 
				  VALUES('".
							$parm['ap_num']."','".
							$parm['ord_num']."','".
							$parm['mat_code']."','".
							$parm['color']."','".
							$parm['mat_cat']."','".
							$parm['eta']."','".
							$parm['unit']."','".
							$parm['use_for']."','".																								
							$parm['qty']."')";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot append order");
				$this->msg->merge($sql->msg);
				return false;    
			}

		$this->msg->add("append apply#: [".$parm['ap_num']."] for special detial") ;
		$ord_id = $sql->insert_id();
		return $ord_id;

	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name
								 FROM ap, supl left join ap_det on ap.ap_num=ap_det.ap_num ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("ap.id DESC");
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
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");		
	}	

   if ($mode==1){
		$mesg = '';
		if ($str = strtoupper($argv['PHP_SCH_num']) )  { 
			$srh->add_where_condition("ap.ap_num LIKE '%$str%'", "PHP_SCH_num",$str); 
			$mesg.= "  PA# : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_SCH_supl'] )  { 
			$srh->add_where_condition("ap.sup_code = '$str'", "PHP_SCH_supl",$str); 
			$mesg.= "  Supl. = [ $str ]. ";
			}
		if ($str = $argv['PHP_SCH_fty'] )  { 
			$srh->add_where_condition("ap.arv_area = '%$str%'", "PHP_SCH_fty",$str); 
			$mesg.= "  Ship = [ $str ]. ";
			}
		if ($str = $argv['PHP_SCH_cust'] )  { 
			$srh->add_where_condition("ap.cust = '$str'", "PHP_SCH_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}			
   }	
   
		$srh->add_where_condition("ap.sup_code = supl.vndr_no");
//		$srh->add_group_condition("ap_det.ap_num");
   
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['apply'] = $result;  // 資料錄 拋入 $op
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
	function search_cfm($mode=0, $dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name FROM ap, supl ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("ap.id DESC");
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
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");		
	}	

   
		$srh->add_where_condition("ap.sup_code = supl.vndr_no");
		$srh->add_where_condition("ap.status = 2");

   
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['apply'] = $result;  // 資料錄 拋入 $op
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
	function search_apv($mode=0, $dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT distinct ap.*, supl.country, supl.supl_s_name as s_name FROM ap, supl ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("ap.id DESC");
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
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("ap.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
//部門 : 業務部門
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($team == 'MD')	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("ap.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");		
	}	


   
		$srh->add_where_condition("ap.sup_code = supl.vndr_no");
		$srh->add_where_condition("ap.status = 3");

   
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['apply'] = $result;  // 資料錄 拋入 $op
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
	function get($num=0,$log_where='') {

		$sql = $this->sql;
		
//請購主檔
		$q_str = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, supl.dm_way as dm_way2, supl.id as supl_id, supl.usance  FROM ap, supl WHERE  ap.sup_code = supl.vndr_no AND ap_num='$num'";
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
		$op['ap']=$row;

		//改變Login帳號為名字
	$po_user=$GLOBALS['user']->get(0,$op['ap']['apv_user']);
	$op['ap']['apv_user_id'] = $op['ap']['apv_user'];
	if ($po_user['name'])$op['ap']['apv_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['cfm_user']);
	$op['ap']['cfm_user_id'] = $op['ap']['cfm_user'];
	if ($po_user['name'])$op['ap']['cfm_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['submit_user']);
	$op['ap']['submit_user_id'] = $op['ap']['submit_user'];
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['ap_user']);
	$op['ap']['ap_user_id'] = $op['ap']['ap_user'];
	if ($po_user['name'])$op['ap']['ap_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['au_user']);
	$op['ap']['au_user_id'] = $op['ap']['au_user'];
	if ($po_user['name'])$op['ap']['au_user'] = $po_user['name'];
		



//請購明細 -- 主料		
		$q_str="SELECT ap_det.*, smpl_code as ord_num, lots.lots_code as mat_code, lots.lots_name as mat_name, bom_lots.color, bom_lots.qty as bom_qty, bom_lots.id as bom_id, lots.price1, lots.comp as con1, lots.specify as con2
						FROM `ap_det`, bom_lots, lots_use, lots  
						WHERE lots.lots_code = lots_use.lots_code AND lots_use.id = bom_lots.lots_used_id AND
								  bom_id=bom_lots.id AND mat_cat = 'l' AND `ap_num` = '$num' 
					  ORDER BY lots_use.lots_code, bom_lots.color";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_det'][$i]=$row1;
			$op['ap_det'][$i]['i']=$i;
			$op['ap_det'][$i]['amount']=$op['ap_det'][$i]['ap_qty']*$op['ap_det'][$i]['price1'];
			$bom_qty = $qty=explode(',',$row1['bom_qty']);
			$op['ap_det'][$i]['bom_qty'] = array_sum($bom_qty);
			$op['ap_det'][$i]['sum_po'] = $this->sum_po_qty($row1['bom_id'],'l');

			$i++;
		}

//請購明細 -- 副料
		$q_str="SELECT ap_det.*, smpl_code as ord_num, acc.acc_code as mat_code, acc.acc_name as mat_name, bom_acc.color, bom_acc.qty as bom_qty, bom_acc.id as bom_id, acc.price1 , acc.des as con1, acc.specify as con2
						FROM `ap_det`, bom_acc,acc_use, acc  
						WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id 
								   AND bom_id=bom_acc.id AND mat_cat = 'a' AND `ap_num` = '$num' 
					  ORDER BY acc_use.acc_code, bom_acc.color";
//echo $q_str."<br>";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_det'][$i]=$row1;
			$op['ap_det'][$i]['i']=$i;
			$op['ap_det'][$i]['amount']=$op['ap_det'][$i]['ap_qty']*$op['ap_det'][$i]['price1'];
			$bom_qty = $qty=explode(',',$row1['bom_qty']);
			$op['ap_det'][$i]['bom_qty'] = array_sum($bom_qty);
			$op['ap_det'][$i]['sum_po'] = $this->sum_po_qty($row1['bom_id'],'l');

			$i++;
		} 	
	
	$i=0;	
//請購明細 -- 特殊請購 -- 主料
		$q_str="SELECT ap_special.*, lots.price1	, lots.comp as con1, lots.specify as con2, lots.lots_name as mat_nam
						FROM `ap_special`, lots	WHERE lots.lots_code = mat_code AND `ap_num` = '$num' ";
//echo $q_str."<br>";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_spec'][$i]=$row1;
			$op['ap_spec'][$i]['i']=$i;
			$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['ap_qty']*$op['ap_spec'][$i]['price1'];

			$i++;
		} 			

//請購明細 -- 特殊請購 -- 副料
		$q_str="SELECT ap_special.*, acc.price1	, acc.des as con1, acc.specify as con2, acc.acc_name as mat_name
						FROM `ap_special`, acc	WHERE acc.acc_code = mat_code AND `ap_num` = '$num' ";
//echo $q_str."<br>";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_spec'][$i]=$row1;
			$op['ap_spec'][$i]['i']=$i;
			$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['ap_qty']*$op['ap_spec'][$i]['price1'];
			$i++;
		} 			
		
	$op['apply_log'] = array ();		
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' ".$log_where;
	//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];
			$op['apply_log'][]=$row1;
		}

	$op['apply_special'] = array ();		
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='special'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
			$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];
			$op['apply_special'][]=$row1;
		}		
		
		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pp($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pp($num=0,$log_where='') {

		$sql = $this->sql;
		
//請購主檔
		$q_str = "SELECT ap.*, supl.country, supl.supl_s_name as s_name, supl.dm_way as dm_way2, supl.id as supl_id, supl.usance  FROM ap, supl WHERE  ap.sup_code = supl.vndr_no AND ap_num='$num'";
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
		$op['ap']=$row;

		//改變Login帳號為名字
	$po_user=$GLOBALS['user']->get(0,$op['ap']['apv_user']);
	$op['ap']['apv_user_id'] = $op['ap']['apv_user'];
	if ($po_user['name'])$op['ap']['apv_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['cfm_user']);
	$op['ap']['cfm_user_id'] = $op['ap']['cfm_user'];
	if ($po_user['name'])$op['ap']['cfm_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['submit_user']);
	$op['ap']['submit_user_id'] = $op['ap']['submit_user'];
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['ap_user']);
	$op['ap']['ap_user_id'] = $op['ap']['ap_user'];
	if ($po_user['name'])$op['ap']['ap_user'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$op['ap']['au_user']);
	$op['ap']['au_user_id'] = $op['ap']['au_user'];
	if ($po_user['name'])$op['ap']['au_user'] = $po_user['name'];
		



//請購明細 -- 主料		
		$q_str="SELECT ap_det.*, smpl_code as ord_num, lots.lots_code as mat_code, lots.lots_name as mat_name, bom_lots.color, bom_lots.qty as bom_qty, bom_lots.id as bom_id, lots.price1, lots.comp as con1, lots.specify as con2
						FROM `ap_det`, bom_lots, lots_use, lots  
						WHERE lots.lots_code = lots_use.lots_code AND lots_use.id = bom_lots.lots_used_id AND
								  bom_id=bom_lots.id AND mat_cat = 'l' AND `ap_num` = '$num' 
					  ORDER BY lots_use.lots_code, bom_lots.color";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_det'][$i]=$row1;
			$op['ap_det'][$i]['i']=$i;
			$op['ap_det'][$i]['amount']=$op['ap_det'][$i]['ap_qty']*$op['ap_det'][$i]['price1'];
			$bom_qty = $qty=explode(',',$row1['bom_qty']);
			$op['ap_det'][$i]['bom_qty'] = array_sum($bom_qty);
			$op['ap_det'][$i]['sum_po'] = $this->sum_po_qty($row1['bom_id'],'l');

			$i++;
		}

//請購明細 -- 副料
		$q_str="SELECT ap_det.*, smpl_code as ord_num, acc.acc_code as mat_code, acc.acc_name as mat_name, bom_acc.color, bom_acc.qty as bom_qty, bom_acc.id as bom_id, acc.price1 , acc.des as con1, acc.specify as con2
						FROM `ap_det`, bom_acc,acc_use, acc  
						WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id 
								   AND bom_id=bom_acc.id AND mat_cat = 'a' AND `ap_num` = '$num' 
					  ORDER BY acc_use.acc_code, bom_acc.color";
//echo $q_str."<br>";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_det'][$i]=$row1;
			$op['ap_det'][$i]['i']=$i;
			$op['ap_det'][$i]['amount']=$op['ap_det'][$i]['ap_qty']*$op['ap_det'][$i]['price1'];
			$bom_qty = $qty=explode(',',$row1['bom_qty']);
			$op['ap_det'][$i]['bom_qty'] = array_sum($bom_qty);
			$op['ap_det'][$i]['sum_po'] = $this->sum_po_qty($row1['bom_id'],'l');

			$i++;
		} 	
	
	$i=0;	
//請購明細 -- 特殊請購 -- 主料
		$q_str="SELECT ap_special.*, lots.price1	, lots.comp as con1, lots.specify as con2, lots.lots_name as mat_nam
						FROM `ap_special`, lots	WHERE lots.lots_code = mat_code AND `ap_num` = '$num' ";
//echo $q_str."<br>";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_spec'][$i]=$row1;
			$op['ap_spec'][$i]['i']=$i;
			$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['ap_qty']*$op['ap_spec'][$i]['price1'];

			$i++;
		} 			

//請購明細 -- 特殊請購 -- 副料
		$q_str="SELECT ap_special.*, acc.price1	, acc.des as con1, acc.specify as con2, acc.acc_name as mat_name
						FROM `ap_special`, acc	WHERE acc.acc_code = mat_code AND `ap_num` = '$num' ";
//echo $q_str."<br>";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['ap_spec'][$i]=$row1;
			$op['ap_spec'][$i]['i']=$i;
			$op['ap_spec'][$i]['amount']=$op['ap_spec'][$i]['ap_qty']*$op['ap_spec'][$i]['price1'];
			$i++;
		} 			
		
	$op['apply_log'] = array ();		
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' ".$log_where;
	//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			//改變Login帳號為名字
		$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];
			$op['apply_log'][]=$row1;
		}

	$op['apply_special'] = array ();		
		$q_str="SELECT * FROM `ap_log` WHERE  `ap_num` = '$num' and item ='special'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
		//改變Login帳號為名字
			$po_user=$GLOBALS['user']->get(0,$row1['user']);
			if ($po_user['name'])$row1['user'] = $po_user['name'];
			$op['apply_special'][]=$row1;
		}		
		
		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_det($num=0,$cat) {

		$sql = $this->sql;

	if($cat == 'l')
	{
//請購明細 -- 主料		
		$q_str="SELECT ap_det.*, smpl_code as ord_num, lots.lots_code as mat_code, lots.lots_name as mat_name, bom_lots.color, lots.price1
						FROM `ap_det`, bom_lots, lots_use, lots  
						WHERE lots.lots_code = lots_use.lots_code AND lots_use.id = bom_lots.lots_used_id AND bom_id=bom_lots.id AND mat_cat = 'l' AND ap_det.id = '$num' ";
		
	}else{
//請購明細 -- 副料
		$q_str="SELECT ap_det.*, smpl_code as ord_num, acc.acc_code as mat_code, acc.acc_name as mat_name, bom_acc.color , acc.price1
						FROM `ap_det`, bom_acc,acc_use , acc  
						WHERE acc.acc_code = acc_use.acc_code AND acc_use.id = bom_acc.acc_used_id AND bom_id=bom_acc.id AND mat_cat = 'a' AND ap_det.id = '$num' ";
	}
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$row1 = $sql->fetch($q_result);

		return $row1;
	} // end func	
	
	
/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm, $mode=0) {

		$sql = $this->sql;		
		$q_str = "UPDATE apply SET ".
							"   sup_area='"				.$parm['sup_area'].
							"',	currency='"				.$parm['currency'].	
							"',	fty='"					.$parm['fty'].							
							"'  WHERE aply_num='"		.$parm['aply_num']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	   if (isset($parm['mat_id']))
	   {
		for ($i=0; $i<sizeof($parm['mat_id']); $i++)
		{
			$q_str = "UPDATE apply_det SET ".
							"   eta='"				.$parm['eta'][$i].
							"'  WHERE id='"	.$parm['mat_id'][$i]."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error !  Database can't update.");
				$this->msg->merge($sql->msg);
				return false;    
			}			
		}
       }
		return true;
	} // end func
*/

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_del($id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM ap_det WHERE id='".$id."'";

	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     
    
	return true;


	}// end func	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->bom_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_del($bom_tb,$ap_num, $id) {
	$sql = $this->sql;

	 	$q_str="UPDATE $bom_tb SET ap_mark='' WHERE ap_mark='".$ap_num."' AND id='".$id."'";

	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     
    
	return true;


	}// end func		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_pa($id) {
	$sql = $this->sql;
//echo "here";
	 	$q_str="DELETE FROM ap_det WHERE ap_num='".$id."'";
//	 	echo $q_str."<br>";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     

	 	$q_str="DELETE FROM ap_special WHERE ap_num='".$id."'";
//	 	echo $q_str."<br>";

	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     

	 	$q_str="DELETE FROM ap_log WHERE ap_num='".$id."'";
//	 	echo $q_str."<br>";

	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     

	 	$q_str="DELETE FROM ap WHERE ap_num='".$id."'";
//	 	echo $q_str."<br>";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     


	 	$q_str="UPDATE bom_lots SET ap_mark = '', pp_mark = 0 WHERE ap_mark='".$id."'";
//	 	echo $q_str."<br>";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}      


	 	$q_str="UPDATE bom_acc SET ap_mark = '', pp_mark = 0  WHERE ap_mark='".$id."'";
//	 	echo $q_str."<br>";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}      
	return true;

	}// end func		
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_pa_apvd($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_pa_apvd($id) {
	$sql = $this->sql;

	 	$q_str="UPDATE ap SET status = '-1' WHERE ap_num='".$id."'";
	 	
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     


	 	$q_str="UPDATE bom_lots SET ap_mark = '', pp_mark = 0 WHERE ap_mark='".$id."'";
//	 	echo $q_str."<br>";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}      


	 	$q_str="UPDATE bom_acc SET ap_mark = '', pp_mark = 0  WHERE ap_mark='".$id."'";
//	 	echo $q_str."<br>";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}      
	return true;

	}// end func	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->special_del($id)		刪除特殊請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function special_del($id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM ap_special WHERE id='".$id."'";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}     
    
	return true;


	}// end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_log($parm) {
	$sql = $this->sql;
	$parm['des']=str_replace("'", "\'",$parm['des']);
			$q_str = "INSERT INTO ap_log (ap_num,user,item,des,k_date) 
				  VALUES('".
							$parm['ap_num']."','".
							$parm['user']."','".
							$parm['item']."','".
							$parm['des']."','".																													
							$parm['k_date']."')";
							//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't insert.");
			$this->msg->merge($sql->msg);
			return false;    
		}   			
		
	return true;
	}// end func	

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
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ap_det_table($ap_det,$ap_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function ap_det_table($ap_det,$ap_num)
{
		$html='';
					$html=$html."<input type='image' src='images/del.png'  onClick=\"del_mat('".$ap_num."','".$ap_det['id']."','".$ap_det['bom_id']."','".$ap_det['mat_cat']."','".$ap_det['mat_code']."',this);\">";

		
		return $html;
}	
/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_fab_bom($id=0, $order_num=0)	抓出指定請購單記錄資料 
#															-- 某一張BOM相關的主料請購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_fab_bom($id,$num) {

		$sql = $this->sql;
		
		$i=0;
		$q_str = "SELECT DISTINCT ap.*, supl.country, supl.supl_s_name as s_name
				  FROM ap, supl, bom_lots, ap_det 
				  WHERE  bom_lots.id = ap_det.bom_id AND ap.ap_num = ap_det.ap_num AND ap.sup_code = supl.vndr_no AND ap_det.mat_cat = 'l' AND bom_lots.wi_id='$id'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][$i]=$row1;
			$op['apply'][$i]['i']=$i;
			$i++;
		}
		$q_str = "SELECT DISTINCT ap.*, supl.country, supl.supl_s_name as s_name
				  FROM ap, supl, ap_special 
				  WHERE ap.ap_num = ap_special.ap_num AND ap.sup_code = supl.vndr_no AND ap_special.mat_cat = 'l' AND ap_special.ord_num='$num'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][$i]=$row1;
			$op['apply'][$i]['i']=$i;
			$i++;
		}
		
		
		return $op;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_acc_bom($id=0, $order_num=0)	抓出指定請購單記錄資料 
#															-- 某一張BOM相關的主料請購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_acc_bom($id,$num) {

		$sql = $this->sql;
		$op=array();
		$i=0;
		$q_str = "SELECT DISTINCT ap.*, supl.country, supl.supl_s_name as s_name
				  FROM ap, supl, bom_acc, ap_det 
				  WHERE  bom_acc.id = ap_det.bom_id AND ap.ap_num = ap_det.ap_num AND ap.sup_code = supl.vndr_no AND ap_det.mat_cat = 'a' AND bom_acc.wi_id='$id'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][$i]=$row1;
			$op['apply'][$i]['i']=$i;
			$i++;
		}
		$q_str = "SELECT DISTINCT ap.*, supl.country, supl.supl_s_name as s_name
				  FROM ap, supl, ap_special 
				  WHERE ap.ap_num = ap_special.ap_num AND ap.sup_code = supl.vndr_no AND ap_special.mat_cat = 'a' AND ap_special.ord_num='$num'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$op['apply'][$i]=$row1;
			$op['apply'][$i]['i']=$i;
			$i++;
		}
		
		
		return $op;
	} // end func	

*/
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_det($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_lots_det($id=0,$support=0) {

		$sql = $this->sql;
		$lots = array();
		$q_str="SELECT bom_lots.*, lots.vendor1, lots.price1, ap.status, ap.po_num,
									 lots_use.lots_code, lots_use.lots_name, lots_use.unit, lots_use.use_for, lots_use.est_1
						FROM bom_lots, lots_use, lots LEFT JOIN ap ON ap.ap_num = bom_lots.ap_mark 
						WHERE  lots.lots_code = lots_use.lots_code AND bom_lots.lots_used_id = lots_use.id
						   AND bom_lots.dis_ver = 0 AND bom_lots.wi_id='".$id."' AND lots_use.support = ".$support;
// echo $q_str."<br>"; 
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$qty=explode(',',$row1['qty']);
			$tmp_qty=0;
			for ($i=0; $i<sizeof($qty); $i++)
			{
				$tmp_qty=$tmp_qty+$qty[$i];				
			}
			$row1['total'] = $tmp_qty;
			$spec = $this->get_special_ap($row1['id'],'l');
			// echo '['.$row1['id'].']';
			$row1['spec_ap'] = $this->get_special_ap($row1['id'],'l');	
			if($row1['pp_mark'] == 0)
			{
				$str_arr = explode(',',$row1['ap_mark']);
				foreach($str_arr as $key){
					$row1['pp'][] = $this->get_po_qty($row1['id'],'l',$key);
				}
			} else {
				$str_arr = explode(',',$row1['ap_mark']);
				foreach($str_arr as $key){
					$row1['pp'][] = $this->get_pp_qty($key,'l',$row1['lots_code'],$row1['color']);	
				}
			}
			$row1['statuss'] = get_po_status($row1['status']);
      // echo $row1['statuss']."here";
			$lots[]=$row1;
		}
		return $lots;
	} // end func spec_ap

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_acc_det($id=0)	抓出指定記錄 bom 副料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_acc_det($id=0,$support=0) {

		$sql = $this->sql;
		$acc = array();
		$q_str="SELECT bom_acc.*, acc.vendor1, acc.price1, ap.status, ap.po_num,
									 acc_use.acc_code, acc_use.acc_name, acc_use.unit, acc_use.use_for, acc_use.est_1
						FROM bom_acc, acc_use, acc LEFT JOIN ap ON ap.ap_num = bom_acc.ap_mark 
						WHERE acc.acc_code = acc_use.acc_code AND bom_acc.acc_used_id = acc_use.id 
						  AND bom_acc.dis_ver = 0 AND bom_acc.wi_id='".$id."' AND acc_use.support = ".$support;
echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row1 = $sql->fetch($q_result)) {
			$qty=explode(',',$row1['qty']);
			$tmp_qty=0;
			for ($i=0; $i<sizeof($qty); $i++)
			{
				$tmp_qty=$tmp_qty+$qty[$i];				
			}
			$row1['total'] = $tmp_qty;
			// $spec = $this->get_special_ap($row1['id'],'a');
			$row1['spec_ap'] = $this->get_special_ap($row1['id'],'a');			
			if($row1['pp_mark'] == 0)
			{
				$row1['pp'][] = $this->get_po_qty($row1['id'],'a',$row1['ap_mark']);			
			}else{
				// $row1['po'] = $this->get_pp_qty($row1['ap_mark'],'a',$row1['acc_code'],$row1['color']);
				$str_arr = explode(',',$row1['ap_mark']);
				foreach($str_arr as $key){
				  $row1['pp'][] = $this->get_pp_qty($key,'l',$row1['lots_code'],$row1['color']);	
				}				
			}
			$row1['statuss'] = get_po_status($row1['status']);
			$acc[]=$row1;
		}
		return $acc;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_special_ap($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[] eta
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_special_ap($id=0,$mat) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT DISTINCT ap.ap_num, ap.status, ap_det.po_eta as eta, ap_det.ap_qty, ap_det.unit, ap_det.po_qty, ap_det.po_unit, ap.po_num 
						FROM ap, ap_det 
						WHERE ap_det.ap_num = ap.ap_num AND ap.special = 1 AND ap.status >= 0
						AND ap_det.mat_cat ='".$mat."' AND bom_id ='".$id."'";
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$row1['statuss'] = get_po_status($row1['status']);
			$ap_num[]=$row1;
		}
		return $ap_num;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_po_qty($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_qty($id=0,$mat, $num) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT ap_det.po_qty as qty, po_unit as unit, unit as pa_unit, po_eta as eta, ap_det.ap_qty, ap_det.po_qty , ap.status, ap.po_num ,ap.ap_num
						FROM ap_det, ap
						WHERE ap_det.ap_num = ap.ap_num AND ap.status >= 0
							AND ap_det.mat_cat ='".$mat."' AND bom_id ='".$id."' AND ap_det.ap_num = '".$num."'";
// if($mat == 'l' && $num == 'PA11-0363,PA11-0362')
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		$row1['statuss'] = get_po_status($row1['status']);
		return $row1;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pp_qty($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pp_qty($id=0,$mat, $code,$color) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT ap_special.po_qty as qty, po_unit as unit, unit as pa_unit, po_eta as eta, ap_special.ap_qty , ap.status, ap.po_num ,ap.ap_num
						FROM ap_special , ap
						WHERE ap.ap_num = ap_special.ap_num AND ap.status >= 0
							AND ap_special.mat_cat ='".$mat."' AND ap_special.ap_num ='".$id."' AND mat_code ='".$code."' 
							AND ap_special.color = '".$color."'  
						GROUP BY ap_special.ap_num";
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$row1 = $sql->fetch($q_result);
		$row1['statuss'] = get_po_status($row1['status']);
		return $row1;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ext_ap($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ext_ap($num) {
		$ap = array();
		$sql = $this->sql;
		$q_str="SELECT ap_special.*, ap.status, ap.po_num, lots.lots_name as mat_name 
						FROM ap_special,ap,lots 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = lots.lots_code 
							AND ap.status >= 0
							AND ap_special.mat_cat ='l' AND ord_num = '".$num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$ap[]=$row1;
		}

		$q_str="SELECT ap_special.*, ap.status, ap.po_num, acc.acc_name as mat_name 
						FROM ap_special,ap,acc 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = acc.acc_code 
							AND ap.status >= 0
							AND ap_special.mat_cat ='a' AND ord_num = '".$num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row1 = $sql->fetch($q_result)) {
			$ap[]=$row1;
		}
		return $ap;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# -> group_ap($ap_det)	
# 將同[訂單]同[色/類]同[物料編號]同[ETA]的物料數量加總合一
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function group_ap($ap_det) {

		$ap_det2[0] = $ap_det[0];
		$ap_det2[0]['id_comp'] = $ap_det2[0]['id'];
		$ap_det2[0]['bom_id_comp'] = $ap_det2[0]['bom_id'];

		$k=1;
		for ($i=1; $i<sizeof($ap_det); $i++)
		{
			$mk=0;	
			for ($j=0; $j< sizeof($ap_det2); $j++)
			{
				if ($ap_det2[$j]['mat_code'] == $ap_det[$i]['mat_code'] && $ap_det2[$j]['color'] === $ap_det[$i]['color'] && $ap_det2[$j]['unit'] == $ap_det[$i]['unit'] && $ap_det2[$j]['size'] == $ap_det[$i]['size'] &&  $ap_det2[$j]['ord_num'] == $ap_det[$i]['ord_num'])
				{
					$ap_det2[$j]['ap_qty']      = $ap_det[$i]['ap_qty'] +$ap_det2[$j]['ap_qty'];
					$ap_det2[$j]['bom_qty']     = $ap_det[$i]['bom_qty'] +$ap_det2[$j]['bom_qty'];
					$ap_det2[$j]['sum_po']     = $ap_det[$i]['bom_qty'] +$ap_det2[$j]['sum_po'];
					$ap_det2[$j]['id_comp']     = $ap_det2[$j]['id_comp'] . "|". $ap_det[$i]['id'];
					$ap_det2[$j]['bom_id_comp'] = $ap_det2[$j]['bom_id_comp'] . "|". $ap_det[$i]['bom_id'];

					$mk = 1;
				}
			}
			/*
			for ($j=0; $j< sizeof($ap_det2); $j++)
			{
				if ($ap_det2[$j]['mat_code'] == $ap_det[$i]['mat_code'] && $ap_det2[$j]['color'] === $ap_det[$i]['color'] && $ap_det2[$j]['unit'] == $ap_det[$i]['unit'] && $ap_det2[$j]['eta'] == $ap_det[$i]['eta'] &&  $ap_det2[$j]['ord_num'] == $ap_det[$i]['ord_num'])
				{
					$ap_det2[$j]['ap_qty']      = $ap_det[$i]['ap_qty'] +$ap_det2[$j]['ap_qty'];
					$ap_det2[$j]['bom_qty']     = $ap_det[$i]['bom_qty'] +$ap_det2[$j]['bom_qty'];
					$ap_det2[$j]['sum_po']     = $ap_det[$i]['bom_qty'] +$ap_det2[$j]['sum_po'];
					$ap_det2[$j]['id_comp']     = $ap_det2[$j]['id_comp'] . "|". $ap_det[$i]['id'];
					$ap_det2[$j]['bom_id_comp'] = $ap_det2[$j]['bom_id_comp'] . "|". $ap_det[$i]['bom_id'];

					$mk = 1;
				}
			}
			*/
			if ($mk == 0)
			{
				$ap_det2[$k] = $ap_det[$i];
				$ap_det2[$k]['id_comp']     = $ap_det[$i]['id'];
				$ap_det2[$k]['bom_id_comp'] = $ap_det[$i]['bom_id'];

				$k++;
			}
		}

		return $ap_det2;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_po_qty($id=0)	抓出指定記錄 bom 主料相關資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sum_po_qty($id=0,$mat) {
		$ap_num=array();
		$sql = $this->sql;
		$q_str="SELECT sum(ap_det.po_qty) as qty FROM ap_det WHERE ap_det.mat_cat ='".$mat."' AND bom_id ='".$id."' GROUP BY bom_id";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		if ($row1 = $sql->fetch($q_result))
		{
			return $row1['qty'];
		}else{
			return 0;
		}
		
		
	} // end func

} // end class


?>