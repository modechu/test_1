<?php 

#++++++++++++++++++++++ DAILY  class ##### 開發布料  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#	->sum_daily_out($year,$mon)	  加總 月份 之日產出數	[以array傳回 每日之 su ,qty] 
#	->sum_daily_subout($year,$mon)	  加總 月份 之日產出數(代工)	[以array傳回 每日之 su ,qty] 
#	->daily_subout($fty, $date)	搜尋  指定日期 指定工廠外發代工 之產出資料	 
#	->daily_out($fty, $date)	搜尋  指定日期 指定工廠 之產出資料	 
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class DAILY {
		
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
#	->check($parm)		檢查 加入新記錄 是否正確
#								# mode =0:一般add的check,  mode=1: edit時的check
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目

		$T = $parm['qty'];
		if (!(is_numeric($T)&&(intval($T)==floatval($T)))){  // 必需為整數

			$this->msg->add("Error ! please input the correct qty [numeric only] 。");

			return false;
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 布料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					echo "HERE";
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO daily_out (k_date,
									factory,
									ord_num,
									qty,
									su) VALUES('".
									$parm['k_date']."','".
									$parm['factory']."','".
									$parm['ord_num']."','".
									$parm['qty']."','".
									$parm['su']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("adding daily out record for : [".$parm['ord_num']."]。") ;

		return $pdt_id;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_daily_out($year,$mon)	  加總 月份 之日產出數	[以array傳回 每日之 su ,qty] 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sum_daily_out($fty,$year,$mon,$where_str='') {

		$sql = $this->sql;

		$srh = new SEARCH();

		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		// 先計算 總共本月有幾日
		$mon_days = getDaysInMonth($mon,$year);
		// 設定 array
		$out_mon = array();
    for ($i = 1; $i <= $mon_days; $i++) {
			// 先將每日初值設定為 0
			$out_mon[$i]['qty'] = 0;
			$out_mon[$i]['su'] = 0;

		$j = 100+ $i;  // 加入前置零值
		$out_mon[$i]['date'] = substr($j,1,2);
		$out_date = $year.'-'.$mon.'-'.substr($j,1,2);
 		$q_header = "SELECT saw_out_put.qty,saw_out_put.su 
								 FROM saw_out_put , s_order
								 WHERE saw_out_put.ord_num = s_order.order_num AND 
								       saw_out_put.saw_fty ='".$fty."' AND saw_out_put.out_date='".$out_date."' ".$where_str."
								 GROUP BY saw_out_put.ord_num, saw_out_put.out_date";
/*
		$q_header = "SELECT daily_out.qty,daily_out.su 
								 FROM daily_out , s_order
								 WHERE daily_out.ord_num = s_order.order_num AND 
								       daily_out.factory ='".$fty."' AND daily_out.k_date='".$out_date."' ".$where_str;
*/		
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
				$result[] = $row;				
		}
		
		if (isset($result) && $result)
		{	
			for ($k = 0; $k < count($result); $k++) {
				$out_mon[$i]['qty'] = $out_mon[$i]['qty'] + $result[$k]['qty'];
				$out_mon[$i]['su'] = $out_mon[$i]['su'] + $result[$k]['su'];
			}
			
		}
		$result = false;
	  

	}

		return $out_mon;

		// 傳回$out_mon[$i]['date']=日期,  $out_mon[$i]['qty']=qty,  $out_mon[$i]['su']=su
	
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_daily_subout($year,$mon)	  加總 月份 之日產出數(代工)	[以array傳回 每日之 su ,qty] 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sum_daily_subout($fty,$year,$mon,$where_str='') {

		$sql = $this->sql;
		$ym=$year."-".$mon;
		
		$mon_days = getDaysInMonth($mon,$year);
		
		 for ($i = 1; $i <= $mon_days; $i++) 
		 {
		 	$j = 100+ $i;  // 加入前置零值
			$out_mon[$i]['date'] = substr($j,1,2);
		 	$out_mon[$i]['qty'] = 0;
			$out_mon[$i]['su'] = 0;
		 }


	$q_str= "	 SELECT	saw_out_put.out_date, sum(saw_out_put.qty) as qty, sum(saw_out_put.su) as su 
						 FROM 	saw_out_put, s_order, pdt_saw_line
						 WHERE  saw_out_put.line_id = pdt_saw_line.id AND saw_out_put.ord_num = s_order.order_num AND 						 				
		    						pdt_saw_line.sc = 1 and s_order.factory = '".$fty."' AND out_date like '".$ym."%'  ".
		    						$where_str.
		    		"GROUP BY out_date";
		    		
/*		
		$q_str= "SELECT daily_out.k_date, sum(daily_out.qty) as qty, sum(daily_out.su) as su 
						 FROM daily_out, pdtion , s_order
						 WHERE daily_out.ord_num = s_order.order_num AND pdtion.order_num = s_order.order_num AND
		    						pdtion.order_num = daily_out.ord_num and sub_con = 1 and pdtion.factory = '".$fty."' 
		    						and k_date like '".$ym."%'  ".$where_str.
		    		"GROUP BY k_date";	
*/		    	
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp_date=explode('-',$row['out_date']);
			$i = $tmp_date[2]-1+1;
			$out_mon[$i]['su']=$row['su'];
			$out_mon[$i]['qty']=$row['qty'];			
		}
		
		return $out_mon;

		// 傳回$out_mon[$i]['date']=日期,  $out_mon[$i]['qty']=qty,  $out_mon[$i]['su']=su
	
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->daily_subout($fty, $date)	搜尋  指定日期 指定工廠外發代工 之產出資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_subout($fty,$date,$where_str='') {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
/*
		$q_header = "SELECT daily_out.* 
								 FROM daily_out, pdtion ,s_order
								 WHERE s_order.order_num = pdtion.order_num AND s_order.order_num = daily_out.ord_num AND 
								       pdtion.order_num = daily_out.ord_num and sub_con = 1 
								       and k_date ='".$date."' AND daily_out.factory='".$fty."' ".$where_str;
*/		


		$q_header = "SELECT saw_out_put.* 
								 FROM 	saw_out_put, s_order, pdt_saw_line
								 WHERE  saw_out_put.line_id = pdt_saw_line.id AND s_order.order_num = saw_out_put.ord_num AND 								         
								       	pdt_saw_line.sc = 1 AND saw_out_put.out_date ='".$date."' AND 
								       	s_order.factory='".$fty."' ".$where_str;
 
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
				$result[] = $row;				
		}
		
		if (!isset($result))
		{
			false;
		}else{		
			return $result;
		}
		// 傳回 $result [ $result[$i][$k_date], $result[$i][$order_num].....]


	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->daily_out($fty, $date)	搜尋  指定日期 指定工廠 之產出資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_out($fty,$date,$where_str='') {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT saw_out_put.out_date as k_date, saw_out_put.qty, saw_out_put.su, 
		                 		saw_out_put.saw_fty as factory, saw_out_put.ord_num
								 FROM saw_out_put, s_order 
								 WHERE s_order.order_num = saw_out_put.ord_num AND saw_out_put.out_date ='".$date."' 
								 AND saw_out_put.saw_fty='".$fty."' ".$where_str."
								 GROUP BY saw_out_put.ord_num, saw_out_put.out_date";

/*
		$q_header = "SELECT daily_out.* 
								 FROM daily_out, s_order 
								 WHERE s_order.order_num = daily_out.ord_num AND k_date ='".$date."' 
								 AND daily_out.factory='".$fty."' ".$where_str;
*/		
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
				$result[] = $row;				
		}
		if (!isset($result))
		{
			false;
		}else{		
			return $result;
		}
		// 傳回 $result [ $result[$i][$k_date], $result[$i][$order_num].....]


	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_daily_out($order)	搜尋  指定訂單 之產出資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_daily_out($order) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT saw_out_put.out_date as k_date, sum(saw_out_put.qty) as qty, sum(saw_out_put.su) as su,
		                    saw_out_put.saw_fty as factory, saw_out_put.ord_num 
		             FROM saw_out_put
		             WHERE ord_num ='".$order."' AND holiday=0
		             GROUP BY saw_out_put.ord_num, saw_out_put.out_date	
		             ORDER by saw_out_put.out_date ";

		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
				$result[] = $row;				
		}
		
		if (!isset($result))
		{
			false;
		}else{		
			return $result;
		}
		// 傳回 $result [ $result[$i][$k_date], $result[$i][$order_num].....]


	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_daily_out($order)	搜尋  指定訂單 之產出資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_out_per_day($order) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT saw_out_put.out_date as k_date, sum(qty)as qty, sum(su) as su 
								 FROM saw_out_put 
								 WHERE ord_num ='".$order."' AND holiday = 0
								 GROUP BY saw_out_put.out_date ORDER by saw_out_put.out_date ";
//		$q_header = "SELECT k_date, sum(qty)as qty, sum(su) as su FROM daily_out WHERE ord_num ='".$order."' GROUP BY k_date ORDER by k_date ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2();  

		return $result;
		// 傳回 $result [ $result[$i][$k_date], $result[$i][$order_num].....]


	} // end func

/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($parm,$mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT daily_out.id, daily_out.ord_num, daily_out.qty, daily_out.su, daily_out.factory, s_order.unit FROM daily_out,s_order ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("daily_out.id DESC ");
		$srh->row_per_page = 100;

if ($mode==1){
		if ($str = $parm['factory'] )  { 
			$srh->add_where_condition("daily_out.factory = '$str'", "PHP_factory",$str," factory = [ $str ]. "); }
		if ($str = $parm['ord_num'] )  { 
			$srh->add_where_condition("daily_out.ord_num LIKE '%$str%'", "PHP_order_num",$str," order number as [ $str ]. "); }
		if ($str = $parm['k_date'] )  { 
			$srh->add_where_condition("daily_out.k_date = '%$str%'", "PHP_k_date",$str," production date=[ $str ]. "); }
}
		$srh->add_where_condition("daily_out.ord_num = s_order.order_num", '','','');

		$result= $srh->send_query2();  
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['daily'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['rows_this_page'] = $srh->rows_this_page;

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
*/
/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id) {

		if (!$id) {
			$this->msg->add("Error ! please idendify record ID for delete.");		    
			return false;
		}

		$sql = $this->sql;

		$q_str = "SELECT * FROM daily_out WHERE id='$id' ";

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
*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 副料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE daily_out SET k_date='"	.$parm['k_date'].
								"',	factory='"		.$parm['factory'].
								"', ord_num='"		.$parm['ord_num'].
								"', qty='"			.$parm['qty'].

								"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE daily_out SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 布料記錄  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! please idendify record ID for delete.");		    
			return false;
		}

		$q_str = "DELETE FROM daily_out WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot delete data file !");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM daily_out ".$where_str;

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
#	->order_daily_out($order)	刪除訂單時將capacity中每月產出資料刪除
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_daily_del($order) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM daily_out WHERE ord_num ='".$order."' ORDER by k_date ";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			$mm = explode('-',$row['k_date']);
			$F = $GLOBALS['capaci']->delete_su($row['factory'], $mm[0], $mm[1], 'actual', $row['su']);
		}  

		return true;

	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_daily_out($order)	刪除訂單時將產出資料刪除	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_del($order) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "DELETE FROM daily_out WHERE ord_num ='".$order."'";
		if (!$q_result = $sql->query($q_header)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;

	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_month_overtime($sch_date,$fty)  取出每月每日加班資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_month_output($fty,$yy_mm) {		
		$sql = $this->sql;
		$rtn = array();

		// 關聯式資料庫查尋 ~~~~~~
		
			$q_str = "SELECT s_order.*, sum(saw_out_put.qty)as out_qty, sum(saw_out_put.su)as out_su
								FROM `saw_out_put`, s_order 
								WHERE saw_out_put.ord_num = s_order.order_num AND saw_out_put.out_date like '".$yy_mm."%' AND 
											s_order.factory = '".$fty."' AND s_order.status > 4
								GROUP BY order_num
								ORDER BY out_su DESC
								";			
		echo $q_str.'<br>';
/*		
			$q_str = "SELECT s_order.*, sum(daily_out.qty)as out_qty, sum(daily_out.su)as out_su
								FROM `daily_out`, s_order 
								WHERE daily_out.ord_num = s_order.order_num AND k_date like '".$yy_mm."%' AND 
											s_order.factory = '".$fty."' AND s_order.status > 4
								GROUP BY order_num
								ORDER BY out_su DESC
								";	
*/								
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$rtn[] = $row;

		}
		

		return $rtn;
		
} // end func


	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>