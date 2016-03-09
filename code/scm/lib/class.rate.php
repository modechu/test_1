<?php 

#++++++++++++++++++++++ DEPT  class ##### 部門別 +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->get_field_value($dept_code,$field_name)	用dept_code 抓出某欄位$field_name的值
#	->update($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#	->get_smpl_serious($dept, $year_code)	由年度及部門別 取出 smpl 的序號
#	->get_smpl_num($year_code, $cust): 由年度及部門別 算出 樣本訂單 的序號  再存入資料庫
#	->get_wis_num($dept_code, $year_code): 由年度及部門別 算出 樣本製造令 的序號  再存入資料庫
#	->get_fabric_serious($dept_code, $year_code,$kind):		由年度及部門別 算出 smpl 的序號  再存入資料庫
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class RATE {
		
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
/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新部門別
#		加入新部門別			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### 檢查輸入項目					
		if (!$parm['date'] = trim($parm['date'])) {
			$this->msg->add("Error ! 請輸入日期 !");
		    return false;
		}
		if (!$parm['currency'] = trim($parm['currency'])) {
			$this->msg->add("Error ! 請選擇幣別 !");
		    return false;
		}
		if (!$parm['in'] = trim($parm['in'])) {
			$this->msg->add("Error ! 請輸入買入 !");
		    return false;
		}
		if (!$parm['out'] = trim($parm['out'])) {
			$this->msg->add("Error ! 請輸入賣出 !");
		    return false;
		}
					# 全部改成大寫
//		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# 檢查是否有重覆
		$q_str = "SELECT id FROM rate WHERE date='".$parm['date']."' and currency='".$parm['currency']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! 這個日期幣別己經存在，請換別的日期幣別!");
			return false;    	    
		}
					# 加入資料庫
		$today=date('y-m-d');
		$q_str = "INSERT INTO `rate` ( `date` , `currency` , `in` , `out` ) VALUES ('".$parm['date']."','".$parm['currency']."','".$parm['in']."','".$parm['out']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("成功 新增匯率 : [".$parm['date']."]。") ;
		return $dept_id;

	} // end func
	*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_in($parm
#		加入匯率			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_in($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);


					# 加入資料庫
		$today=date('y-m-d');
		$q_str = "INSERT INTO `rate_in` ( `rate_date` , `USD` , `HKD` , `GBP`, `JPY` , `EUR` , `RMB` ) 
							VALUES ('".$parm['rate_date']."','".
												 $parm['USD']."','".
												 $parm['HKD']."','".
												 $parm['GBP']."','".
												 $parm['JPY']."','".
												 $parm['EUR']."','".
												 $parm['RMB']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("Successfully Append Rate : [".$parm['rate_date']."]") ;
		return $dept_id;

	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_in($parm
#		加入匯率			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_out($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);


					# 加入資料庫
		$today=date('y-m-d');
		$q_str = "INSERT INTO `rate_out` ( `rate_date` , `USD` , `HKD` , `GBP`, `JPY` , `EUR` , `RMB` ) 
							VALUES ('".$parm['rate_date']."','".
												 $parm['USD']."','".
												 $parm['HKD']."','".
												 $parm['GBP']."','".
												 $parm['JPY']."','".
												 $parm['EUR']."','".
												 $parm['RMB']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("Successfully Append Rate : [".$parm['rate_date']."]") ;
		return $dept_id;

	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		更新 匯率買入
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_in($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);
		
		#####   更新資料庫內容
		$q_str = "UPDATE `rate_in` SET 
							`USD` = '".$parm['USD']."',
							`HKD` = '".$parm['HKD']."',
							`GBP` = '".$parm['GBP']."',
							`JPY` = '".$parm['JPY']."',
							`EUR` = '".$parm['EUR']."',
							`RMB` = '".$parm['RMB']."' 
							WHERE `rate_date` = '".$parm['rate_date']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_out($parm)		更新 匯率賣出
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_out($parm) {

		$sql = $this->sql;
		foreach($parm as $key => $value) $pamr[$key] = trim($value);
		
		#####   更新資料庫內容
		$q_str = "UPDATE `rate_out` SET 
							`USD` = '".$parm['USD']."',
							`HKD` = '".$parm['HKD']."',
							`GBP` = '".$parm['GBP']."',
							`JPY` = '".$parm['JPY']."',
							`EUR` = '".$parm['EUR']."',
							`RMB` = '".$parm['RMB']."' 
							WHERE `rate_date` = '".$parm['rate_date']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	
	
/*	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	搜尋 部門別資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
if ($mode == 0){		
		if ($str = $argv['PHP_year'] )  { 
			$search_date=$str; }
		
		if ($str = $argv['PHP_month'] )  { 
			if(!isset($search_date)){$search_date=date('Y')."-".$str;}else{$search_date=$search_date."-".$str;} }
		if (isset($search_date))
		{
			if ($argv['PHP_month'])
			{
				$s_date=$search_date."-01";
				$e_date=$search_date."-31";
			}else{
				$s_date=$search_date."-01-01";
				$e_date=$search_date."-12-31";
			}			
			$srh->add_where_condition("date >= '$s_date'", "PHP_part_num",$str,"搜尋 日期 = [ $search_date ]. ");			
		}
		if ($str = $argv['PHP_currency'] )  { 
			$srh->add_where_condition("currency = '$str'", "PHP_currency",$str,"搜尋 幣別 = [ $str ]. "); }		
}
//2006.11.15修改新增後呈現方式	start	
	$q_header = "SELECT * FROM rate";
//2006.11.15修改新增後呈現方式    end
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("date ");
		$srh->row_per_page = 12;
//2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 以數字型式顯示頁碼 end    	
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['rate'] = $result;  // 資料錄 拋入 $op		
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
#####2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14新頁碼需要的oup_put	end

		return $op;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, dept_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $dept_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM rate WHERE id='$id' ";
		} elseif ($dept_code) {
			$q_str = "SELECT * FROM rate ";
		} else {
			$this->msg->add("Error ! 請指明 部門資料在資料庫內的 ID.");		    
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

*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, dept_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_rate($currency=0, $ratedate, $price) {

		$sql = $this->sql;

//			$q_str = "SELECT rate.in FROM rate WHERE  rate.currency = '$currency' AND rate.date ='$ratedate'  ";
			$q_str = "SELECT * FROM rate_in WHERE  rate_date ='$ratedate'  ";
			

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return 0;    
		}
//		echo $currency;
		if(!isset($row[$currency])) return 0;
		$ntd_price = $row[$currency] * $price;
		//echo $ntd_price;
		return $ntd_price;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_rate($currency=0, $ratedate)	抓出指定記錄內匯率
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rate($currency=0, $ratedate) {

		$sql = $this->sql;

//			$q_str = "SELECT rate.in FROM rate WHERE  rate.currency = '$currency' AND rate.date ='$ratedate'  ";
			$q_str = "SELECT * FROM rate_out WHERE  rate_date ='$ratedate'  ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return 0;    
		}
//		echo $currency;
		if(!isset($row[$currency])) return 0;
		return $row[$currency];
	} // end func	
		

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_rate_date($ratedate)	抓出指定記錄內匯率
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_rate_date($ratedate) {

$sql = $this->sql;

$q_str = "SELECT * FROM `rate_out` WHERE  `rate_date` = '$ratedate'  ";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法存取資料庫!");
    $this->msg->merge($sql->msg);
    return false;    
}

if (!$row = $sql->fetch($q_result)) {

    $q_str = "SELECT * FROM `rate_out` ORDER BY `rate_date` DESC LIMIT 1 ";
    $q_result = $sql->query($q_str);
    if (!$row = $sql->fetch($q_result)) {
        return 0;
    }
    
}

return $row;

} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mm_rate($mm)	抓出指定月份的匯率
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_mm_rate($mm) {

		$sql = $this->sql;

//			$q_str = "SELECT rate.in FROM rate WHERE  rate.currency = '$currency' AND rate.date ='$ratedate'  ";
			$q_str = "SELECT rate_in.*, rate_out.id as out_id, rate_out.USD as out_USD, rate_out.HKD as out_HKD,
											 rate_out.GBP as out_GBP, rate_out.JPY as out_JPY, rate_out.EUR as out_EUR,
											 rate_out.RMB as out_RMB, rate_out.rate_date as date
								FROM rate_in, rate_out
								WHERE  rate_in.rate_date = rate_out.rate_date  AND rate_in.rate_date like '".$mm."%'";
			

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$mm_rate[$i] = $row;    
			$mm_rate[$i]['i'] = $i;
			$i++;
		}

		if(!isset($mm_rate)) return 0;
		return $mm_rate;
	} // end func		

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mm_rate($mm)	抓出指定月份的匯率
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($mm) {

		$sql = $this->sql;
		$q_str = "SELECT rate_in.*, rate_out.id as out_id, rate_out.USD as out_USD, rate_out.HKD as out_HKD,
										 rate_out.GBP as out_GBP, rate_out.JPY as out_JPY, rate_out.EUR as out_EUR,
										 rate_out.RMB as out_RMB, rate_out.rate_date as date
							FROM rate_in, rate_out
							WHERE  rate_in.rate_date = rate_out.rate_date  AND rate_in.rate_date = '".$mm."'";
	//echo $q_str;		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		if (!$row = $sql->fetch($q_result)) {
			return 0;
		}		
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		更新 部門別資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
					# 全部改成大寫
//		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# 檢查是否有這個檔案存在
		$q_str = "SELECT id FROM rate WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! 無法找到資料庫記錄!");
			return false;    	    
		}
		if (!$parm['in'] = trim($parm['in'])) {
			$this->msg->add("Error ! 請輸入買入 !");
		    return false;
		}
		if (!$parm['out'] = trim($parm['out'])) {
			$this->msg->add("Error ! 請輸入賣出 !");
		    return false;
		}
		
		#####   更新資料庫內容
		$q_str = "UPDATE `rate` SET `in` = '".$parm['in']."',`out` = '".$parm['out']."' WHERE `id` = '".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm,$table)		更新 匯率資料內 某個單一欄位
#								$parm = [$date, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm,$table) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE $table SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE rate_date='".$parm['date']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 部門別資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 使用單位 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM section WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
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
		$q_str = "SELECT ".$n_field." FROM section".$where_str;		
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