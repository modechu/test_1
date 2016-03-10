<?php 

#++++++++++++++++++++++ revise ORDER  class ##### 更新 訂 單  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->add_edit($parm)						更新 revise order 記錄 [   ]
#	->edit($parm)							更新 整筆資料



#	divert_month_su($qty, $ratio, $s_date, $f_date)
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class R_ORDER {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! 無法聯上資料庫.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 revise 訂單記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

//查詢最後版本 
		$q_str = "SELECT cust_ver as ver FROM s_order WHERE order_num='".$parm['order_num']."' LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	

					# 加入資料庫
		$q_str = "INSERT INTO r_order (order_num,combine,creator,opendate,dept,cust,cust_ver,ref,factory,style,qty,unit,
							style_num,patt_num,ptn_upload,smpl_ord,uprice,quota,mat_u_cost,mat_useage,acc_u_cost,
							quota_fee,comm_fee,cm,smpl_fee,etd,etp,gmr,ie_time1,ie_time2,ie1,ie2,su,cfmer,cfm_date,
							smpl_apv,smpl_apv_type,smpl_apv2,smpl_apv_type2,smpl_apv3,smpl_apv_type3,apver,apv_date,
							schd_date,schd_er,last_updator,last_update,status,revise,emb,wash,oth,oth_treat,
							agent,fusible,interline,line_sex,cust_po,remark) VALUES('".
							$parm['order_num']."','".
							$parm['combine']."','".
							$parm['creator']."','".
							$parm['opendate']."','".
							$parm['dept']."','".
							$parm['cust']."','".
							$parm['cust_ver']."','".
							$parm['ref']."','".
							$parm['factory']."','".
							$parm['style']."','".
							$parm['qty']."','".
							$parm['unit']."','".
							$parm['style_num']."','".
							$parm['patt_num']."','".
							$parm['ptn_upload']."','".
							$parm['smpl_ord']."','".
							$parm['uprice']."','".
							$parm['quota']."','".
							$parm['mat_u_cost']."','".
							$parm['mat_useage']."','".
							$parm['acc_u_cost']."','".
							$parm['quota_fee']."','".
							$parm['comm_fee']."','".
							$parm['cm']."','".
							$parm['smpl_fee']."','".
							$parm['etd']."','".
							$parm['etp']."','".
							$parm['gmr']."','".
							$parm['ie_time1']."','".
							$parm['ie_time2']."','".
							$parm['ie1']."','".
							$parm['ie2']."','".
							$parm['su']."','".
							$parm['cfmer']."','".
							$parm['cfm_date']."','".
							$parm['smpl_apv']."','".
							$parm['smpl_apv_type']."','".
							$parm['smpl_apv2']."','".
							$parm['smpl_apv_type2']."','".
							$parm['smpl_apv3']."','".
							$parm['smpl_apv_type3']."','".
							$parm['apver']."','".
							$parm['apv_date']."','".
							$parm['schd_date']."','".
							$parm['schd_er']."','".
							$parm['last_updator']."','".
							$parm['last_update']."','".
							$parm['status']."','".
							$parm['revise']."','".
							$parm['emb']."','".
							$parm['wash']."','".
							$parm['oth']."','".
							$parm['oth_treat']."','".
							$parm['agent']."','".
							$parm['fusible']."','".
							$parm['interline']."','".
							$parm['line_sex']."','".
							$parm['cust_po']."','".
							$parm['remark']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("append order#: [".$parm['order_num']."]。") ;

		return $ord_id;

	} // end func
	
/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='')	搜尋 訂 單 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $dept='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM s_order ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
	if ($dept){  //  當要分部門時
		$srh->add_where_condition("dept = '$dept'", "",$dept,"部門別=[ $dept ]. ");
	}

   if ($mode==1){
//		if ($str = $argv['PHP_dept_code'] )  { 
//			$srh->add_where_condition("dept = '$str'", "PHP_dept_code",$str,"部門別 = [ $str ]. ");
//		}

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("ref LIKE '%$str%'", "PHP_ref",$str,"搜尋 客戶參考編號含有:[ $str ]內容 "); 
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("cust = '$str'", "PHP_cust",$str,"搜尋 客戶=[ $str ]. "); 
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("order_num LIKE '%$str%'", "PHP_order_num",$str,"搜尋 訂單編號含有:[ $str ]內容 "); 
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("factory = '$str'", "PHP_factory",$str,"搜尋 承製工廠=[ $str ]. "); 
			}
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
		$op['sorder'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
*/
/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_search($mode=0, $where_str='')	搜尋 訂 單 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ord_search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM s_order, pdtion ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
		$srh->row_per_page = 20;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
if ($mode==1 || $mode==2){
//		$srh->row_per_page = 10;

//		if ($str = $argv['PHP_dept_code'] )  { 
//			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str,"部門別 = [ $str ]. ");
//		}

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str,"搜尋 客戶參考編號含有: [ $str ]內容 "); 
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"搜尋 客戶 = [ $str ]. "); 
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str,"搜尋 訂單編號含有: [ $str ]內容 "); 
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str,"搜尋 承製工廠 = [ $str ]. "); 
			}
}		
if ($mode==2){   // 當要搜尋的 訂單是 排產確認後的時
		$srh->add_where_condition("s_order.status >= 7", "","","");   
}		
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加

		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
*/

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_num=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $order_num=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM r_order WHERE id='$id' ";
		} elseif ($order_num) {
			$q_str = "SELECT * FROM r_order WHERE order_num='$order_num' ";
		} else {
			$this->msg->add("Error ! 請指明 訂單 在資料庫內的 ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return false;    
		}
		return $row;
	} // end func

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 訂單 記錄 
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE s_order SET ref='"		.$parm['ref'].
							"',	factory='"		.$parm['factory'].
							"',	style='"		.$parm['style'].
							"',	qty='"			.$parm['qty'].
							"',	su='"			.$parm['su'].
//							"', su_ratio='"		.$parm['su_ratio'].
							"', unit='"			.$parm['unit'].
//							"',	style_num='"	.$parm['style_num'].
							"',	patt_num='"		.$parm['patt_num'].
							"', uprice='"		.$parm['uprice'].
							"',	quota='"		.$parm['quota'].
							"',	qty='"			.$parm['qty'].
							"', mat_u_cost='"	.$parm['mat_u_cost'].
							"',	mat_useage='"	.$parm['mat_useage'].
							"',	acc_u_cost='"	.$parm['acc_u_cost'].
							"',	quota_fee='"	.$parm['quota_fee'].
							"',	comm_fee='"		.$parm['comm_fee'].
							"', cm='"			.$parm['cm'].
							"',	smpl_fee='"		.$parm['smpl_fee'].
							"',	etd='"			.$parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday'].
							"', etp='"			.$parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday'].
							"', gmr='"			.$parm['gmr'].
							"', last_updator='"	.$parm['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func
*/


/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ************************ 會更新 capacity 內的欄位 ******************
#	distri_month_su($su, $s_date, $f_date, $fty, $cat)
# Detail description	: 將 數量 換成su 再分配到生產月份內 
#						: 寫入 capacity table 的 $field 內 [如沒找到 error ]
#						: 傳回 陣列 ( 200505=>su , 200506=>su, ......
#		$fty, $cat 為了要在 capacity table更新 [ $cat 指的是 capacity內的 c_tyep 欄位名 ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function distri_month_su($T_su, $s_date, $f_date, $fty, $cat) {

 		$div = array();

		$distribute ='';   // 做為csv 變數
   
		list($s_year,$s_mon,$s_day) = split("-",$s_date);
		list($f_year,$f_mon,$f_day) = split("-",$f_date);
		$days =	countDays($s_date,$f_date);
//		$T_su = $su;		// 總 su 數
		$day_su = $T_su/$days;		// 每日產出 --(偏小)

		// 計算總共有幾個月份?
		$y = $f_year - $s_year;
		$m = 12*$y + (12-$s_mon+1) - (12-$f_mon);

//echo "<br>total qty = ".$qty." are equal to  ".$T_su." s.u.<br>";
//echo "<br>from ".$s_date." to ".$f_date." totally distribute for ".$m." 個月份; 計 ".$days."天 <br>";

			$su_year = $s_year;	// 年份的計數器
			$su_mon = $s_mon;	// 月份的計數器

			$divered_su =0;	// 已經統計的su數 做為最後月之減項

		for ($i=0; $i<$m; $i++){
			if($su_mon >12){
				$su_year = $su_year+1;
				$su_mon = 1;
			}

			$mon = sprintf("%04d%02d", $su_year, $su_mon);   // 月份的標記

				// 計算每月的天數 ---- 將 su 分配進入
			if($s_mon==$f_mon){   // 如果開始和最後是同月份時-----
					$d = $f_day - $s_day ;
					$su = intval($day_su * $d);
			}else{
				if ($i==0){  // 第一個月
					$d = getDaysInMonth($su_mon,$su_year)- intval($s_day);
					$su = intval($day_su * $d);
				} elseif($i==$m-1){  // 最後一個月
					$d = intval($f_day);
					$su = $T_su - $divered_su;
				} else{
					$d = getDaysInMonth($su_mon,$su_year);
					$su = intval($day_su * $d);
				}
			}

			$divered_su = $divered_su + $su;

//echo "<br>   distribute-month ==> ".$mon." ==>".$d." days ==> for ".$su." su" ;
			$su_mon = $su_mon+1;
			$tmp_m = $mon;
			$div[$tmp_m] = $su;   // 置入 array 

		# #####============ 加入 capacity ->  pre_schedule  #########################
	
			$su_m = substr($mon,4);
						
		if (!$F = $GLOBALS['capaci']->update_su($fty, $su_year, $su_m, $cat, $su)) {
			$this->msg->add("Error ! cannot update [".$cat."] field of capacity table, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
			
			
			
			$distribute = $distribute.','.$mon.$su;
		}

		$distribute = substr($distribute,1);  // 去除開頭的',' 符號

//echo "<br> distribute monthy su are ===>".$distribute;
//exit;
	
	// 傳回的參數為一個 csv 如: 2005071200,200508850,

	return $distribute;


} // end func


*/
/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->creat_pdtion($parm)		加入 新的 pdtion 訂單記錄 [ 寫入工廠 及 etp_su 排產數]
#								傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function creat_pdtion($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO pdtion (order_num,factory,etp_su) VALUES('".
							$parm['order_num']."','".
							$parm['factory']."','".
							$parm['etp_su']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增 PDTION 記錄: [".$parm['order_num']."]。") ;

		return $ord_id;

	} // end func

*/

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($field, $val, $id)		更新 資料記錄內 某個單一欄位
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($field, $val, $id) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE s_order SET ".$field."='".$val."'  WHERE id=".$id." ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

*/

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_pdtion_field($field, $val, $id)		更新 資料記錄內 某個單一欄位
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_pdtion_field($field, $val, $id) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE pdtion SET ".$field."='".$val."'  WHERE id=".$id." ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
*/


/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		刪除 製作令記錄  [由ID]刪除
#							$mode=0: $id= 記錄之id; $mode<>0: $id=製令編號 wi_num
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! 請指明 訂單 資料檔的 ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM s_order WHERE order_num='$id' ";
		}else{
			$q_str = "DELETE FROM s_order WHERE id='$id' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func
*/


/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_field_value($field, $id='',$ord_num='',$tbl='s_order') {
		$sql = $this->sql;
		$row = array();

		if ($id){
	  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE id='".$id."' ";
		} elseif($ord_num){
	  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE order_num='".$ord_num."' ";
		} else {
			$this->msg->add("Error! not enough info to get data record !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		$row = $sql->fetch($q_result);

		$field_val = $row[0];

		return $field_val;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

*/


} // end class


?>