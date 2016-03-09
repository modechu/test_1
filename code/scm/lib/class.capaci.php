<?php 

#++++++++++++++++++++++ CAPACI  class   +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->update_capacity($parm)		更新 capaci->capacity 資料錄 
#	->check($parm)		檢查 加入新記錄 是否正確

#	->add($parm)		加入新 capacity 記錄
#	->append_new($parm)		新開 capacity 的 shipping記錄
#	->search_etp($fty,$year_mon,$limit_entries)	搜尋 order ETP 的月份訂單 [呼叫 pdtion table] 資料	 
#	->search_schdl($fty,$year_mon,$limit_entries)	搜尋 FTY 排產 的月份訂單 [呼叫 pdtion table] 資料	 
#	->get($fty, $year,  $cat='capacity')	抓出指定記錄內資料 RETURN $row[]
#	->update_su($fty, $year, $mon, $cat, $add_num)		更新 SU 記錄
#	->update_field($fty, $year, $type, $field_name, $field_value)	直接更新 capacity 之某個field內記錄

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class CAPACI {
		
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_capacity($parm)		更新 capaci->capacity 資料錄 
#				
#										###### 因為只有 su 欄可用直接加減 *******				
#										###### 因為只有 su 欄可用直接加減 *******				
#										###### 因為只有 su 欄可用直接加減 *******				
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_capacity($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		$this->msg = new MSG_HANDLE();
		
		#####   更新資料庫內容  // 直接在 sql 內加入
			$q_str = "UPDATE capaci SET m01="	.$parm['A1'][1].
										",m02="	.$parm['A1'][2].
										",m03="	.$parm['A1'][3].
										",m04="	.$parm['A1'][4].
										",m05="	.$parm['A1'][5].
										",m06="	.$parm['A1'][6].
										",m07="	.$parm['A1'][7].
										",m08="	.$parm['A1'][8].
										",m09="	.$parm['A1'][9].
										",m10="	.$parm['A1'][10].
										",m11="	.$parm['A1'][11].
										",m12="	.$parm['A1'][12].
										"  WHERE id='".$parm['A1']['id']."' ";
		if($parm['year2']){
			$q_str2 = "UPDATE capaci SET m01="	.$parm['A2'][1].
										",m02="	.$parm['A2'][2].
										",m03="	.$parm['A2'][3].
										",m04="	.$parm['A2'][4].
										",m05="	.$parm['A2'][5].
										",m06="	.$parm['A2'][6].
										",m07="	.$parm['A2'][7].
										",m08="	.$parm['A2'][8].
										",m09="	.$parm['A2'][9].
										",m10="	.$parm['A2'][10].
										",m11="	.$parm['A2'][11].
										",m12="	.$parm['A2'][12].
										"  WHERE id='".$parm['A2']['id']."' ";
		}
		if($parm['year3']){
			$q_str3 = "UPDATE capaci SET m01="	.$parm['A3'][1].
										",m02="	.$parm['A3'][2].
										",m03="	.$parm['A3'][3].
										",m04="	.$parm['A3'][4].
										",m05="	.$parm['A3'][5].
										",m06="	.$parm['A3'][6].
										",m07="	.$parm['A3'][7].
										",m08="	.$parm['A3'][8].
										",m09="	.$parm['A3'][9].
										",m10="	.$parm['A3'][10].
										",m11="	.$parm['A3'][11].
										",m12="	.$parm['A3'][12].
										"  WHERE id='".$parm['A3']['id']."' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update [".$parm['fty']."]'s capacity of year:[".$parm['year1']."]. ");
			$this->msg->merge($sql->msg);
//			return false;    
		}
		if($parm['year2']){
			if (!$q_result2 = $sql->query($q_str2)) {
				$this->msg->add("Error ! cannot update [".$parm['fty']."]'s capacity of year:[".$parm['year2']."]. ");
				$this->msg->merge($sql->msg);
//				return false;    
			}
		}
		if($parm['year3']){
			if (!$q_result3 = $sql->query($q_str3)) {
				$this->msg->add("Error ! cannot update [".$parm['fty']."]'s capacity of year:[".$parm['year3']."]. ");
				$this->msg->merge($sql->msg);
//				return false;    
			}
		}

		if (count($this->msg->get(2))){
			return false;
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		檢查 加入新記錄 是否正確
#								# 只檢查輸入項是否為 "數字"
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目

			for ($i=1;$i<13;$i++){
				$T = trim($parm['A1'][$i]);
				if (!(($T==''))){ // 只要有一個錯 就直接跳出
					if (!(is_numeric($T)&&(intval($T)==floatval($T)))){  // 必需為整數
						$this->msg->add("Error ! please input the correct volume [numeric only] for year[".$parm['year1']."]。");
					}
				}
			}  // end for

		if ($parm['A2']){  // 當有輸入第二個年度時
			for ($i=1;$i<13;$i++){
				$T = trim($parm['A2'][$i]);
				if (!($T=='')){ // 只要有一個錯 就直接跳出
					if (!(is_numeric($T)&&(intval($T)==floatval($T)))){  // 必需為整數
						$this->msg->add("Error ! please input the correct volume [numeric only] for year[".$parm['year2']."]。");
					}
				}
			}  // end for
		}

		if ($parm['A3']){  // 當有輸入第三個年度時
			for ($i=1;$i<13;$i++){
				$T = trim($parm['A3'][$i]);
				if (!($T=='')){ // 只要有一個錯 就直接跳出
					if (!(is_numeric($T)&&(intval($T)==floatval($T)))){  // 必需為整數
						$this->msg->add("Error ! please input the correct volume [numeric only] for year[".$parm['year3']."]。");
					}
				}
			}  // end for
		}

		if (count($this->msg->get(2))){
			return false;
		}
					
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 capacity 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$this->msg = new MSG_HANDLE();
		$sql = $this->sql;

		$q_str =''; $q_str2=''; $q_str3='';
		$pre_q_str =''; $pre_q_str2 =''; $pre_q_str3 ='';
		$sch_q_str =''; $sch_q_str2 =''; $sch_q_str3 ='';
		$act_q_str =''; $act_q_str2 =''; $act_q_str3 ='';

					# 加入資料庫
		$q_str = "INSERT INTO capaci (factory,
									year,
									c_type,
									m01,
									m02,
									m03,
									m04,
									m05,
									m06,
									m07,
									m08,
									m09,
									m10,
									m11,
									m12) VALUES('".
									$parm['fty']."','".
									$parm['year1']."','".
									"capacity"."','".
									$parm['A1'][1]."','".
									$parm['A1'][2]."','".
									$parm['A1'][3]."','".
									$parm['A1'][4]."','".
									$parm['A1'][5]."','".
									$parm['A1'][6]."','".
									$parm['A1'][7]."','".
									$parm['A1'][8]."','".
									$parm['A1'][9]."','".
									$parm['A1'][10]."','".
									$parm['A1'][11]."','".
									$parm['A1'][12]."')";
			// 其它的檔 [PRE_SCHEDULE],[SHCEDULE],[ACTUAL]
			$pre_q_str = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year1']."','"."pre_schedule"."')";

			$sch_q_str = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year1']."','"."schedule"."')";

			$act_q_str = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year1']."','"."actual"."')";

			$shp_q_str = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year1']."','"."shipping"."')";

			$fob_q_str = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year1']."','"."shp_fob"."')";
//---------------------------------------------- 寫入第一個年的6個錄
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append year [".$parm['year1']."] capacity record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$q_result = $sql->query($pre_q_str)) {
			$this->msg->add("Error ! cannot append year [".$parm['year1']."] pre_schedule record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$q_result = $sql->query($sch_q_str)) {
			$this->msg->add("Error ! cannot append year [".$parm['year1']."] schedule record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$q_result = $sql->query($act_q_str)) {
			$this->msg->add("Error ! cannot append year [".$parm['year1']."] actual record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$q_result = $sql->query($shp_q_str)) {
			$this->msg->add("Error ! cannot append year [".$parm['year1']."] actual record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$q_result = $sql->query($fob_q_str)) {
			$this->msg->add("Error ! cannot append year [".$parm['year1']."] actual record.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		//-================================2 nd year
		if($parm['year2']){

			$q_str2 = "INSERT INTO capaci (factory,
										year,
										c_type,
										m01,
										m02,
										m03,
										m04,
										m05,
										m06,
										m07,
										m08,
										m09,
										m10,
										m11,
										m12) VALUES('".
										$parm['fty']."','".
										$parm['year2']."','".
										"capacity"."','".
										$parm['A2'][1]."','".
										$parm['A2'][2]."','".
										$parm['A2'][3]."','".
										$parm['A2'][4]."','".
										$parm['A2'][5]."','".
										$parm['A2'][6]."','".
										$parm['A2'][7]."','".
										$parm['A2'][8]."','".
										$parm['A2'][9]."','".
										$parm['A2'][10]."','".
										$parm['A2'][11]."','".
										$parm['A2'][12]."')";
			// 其它的檔 [PRE_SCHEDULE],[SHCEDULE],[ACTUAL]
			$pre_q_str2 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year2']."','"."pre_schedule"."')";

			$sch_q_str2 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year2']."','"."schedule"."')";

			$act_q_str2 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year2']."','"."actual"."')";

			$shp_q_str2 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year2']."','"."shipping"."')";

			$fob_q_str2 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year2']."','"."shp_fob"."')";
//----------------------------------------------寫入第2個年的6個錄-----------------------
			if (!$q_result = $sql->query($q_str2)) {
				$this->msg->add("Error ! cannot append year [".$parm['year2']."] capacity record.");
				$this->msg->merge($sql->msg);
				return false;    
			}

			if (!$q_result = $sql->query($pre_q_str2)) {
				$this->msg->add("Error ! cannot append year [".$parm['year2']."] pre_schedule record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($sch_q_str2)) {
				$this->msg->add("Error ! cannot append year [".$parm['year2']."] schedule record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($act_q_str2)) {
				$this->msg->add("Error ! cannot append year [".$parm['year2']."] actual record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($shp_q_str2)) {
				$this->msg->add("Error ! cannot append year [".$parm['year2']."] actual record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($fob_q_str2)) {
				$this->msg->add("Error ! cannot append year [".$parm['year2']."] actual record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}

		//======================== 3rd year
		if($parm['year3']){
			$q_str3 = "INSERT INTO capaci (factory,
										year,
										c_type,
										m01,
										m02,
										m03,
										m04,
										m05,
										m06,
										m07,
										m08,
										m09,
										m10,
										m11,
										m12) VALUES('".
										$parm['fty']."','".
										$parm['year3']."','".
										"capacity"."','".
										$parm['A3'][1]."','".
										$parm['A3'][2]."','".
										$parm['A3'][3]."','".
										$parm['A3'][4]."','".
										$parm['A3'][5]."','".
										$parm['A3'][6]."','".
										$parm['A3'][7]."','".
										$parm['A3'][8]."','".
										$parm['A3'][9]."','".
										$parm['A3'][10]."','".
										$parm['A3'][11]."','".
										$parm['A3'][12]."')";
			// 其它的檔 [PRE_SCHEDULE],[SHCEDULE],[ACTUAL]
			$pre_q_str3 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year3']."','"."pre_schedule"."')";

			$sch_q_str3 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year3']."','"."schedule"."')";

			$act_q_str3 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year3']."','"."actual"."')";

			$shp_q_str3 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year3']."','"."shipping"."')";

			$fob_q_str3 = "INSERT INTO capaci (factory, year, c_type) VALUES('".$parm['fty']."','".$parm['year3']."','"."shp_fob"."')";
//---------------------------------------------寫入第3個年的6個錄------------------------
			if (!$q_result = $sql->query($q_str3)) {
				$this->msg->add("Error ! cannot append year [".$parm['year3']."] capacity record.");
				$this->msg->merge($sql->msg);
				return false;    
			}

			if (!$q_result = $sql->query($pre_q_str3)) {
				$this->msg->add("Error ! cannot append year [".$parm['year3']."] pre_schedule record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($sch_q_str3)) {
				$this->msg->add("Error ! cannot append year [".$parm['year3']."] schedule record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($act_q_str3)) {
				$this->msg->add("Error ! cannot append year [".$parm['year3']."] actual record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($shp_q_str3)) {
				$this->msg->add("Error ! cannot append year [".$parm['year3']."] actual record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			if (!$q_result = $sql->query($fob_q_str3)) {
				$this->msg->add("Error ! cannot append year [".$parm['year3']."] actual record.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}
		
		if (count($this->msg->get(2))){
			return false;
		}
					
		return true;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->append_new($parm)		新開 capacity 的 shipping記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function append_new($year, $fty, $c_type) {
					
		$this->msg = new MSG_HANDLE();
		$sql = $this->sql;

		$q_str =''; 

					# 加入資料庫
		$q_str =  "INSERT INTO capaci (factory, year, c_type) VALUES('".$fty."','".$year."','".$c_type."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append year [".$year."] [".$c_type."] record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_etp($fty,$year_mon,$limit_entries)	
#			搜尋 order ETP 的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp($fty,$year_mon,$limit_entries, $where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.revise, s_order.uprice, s_order.status, s_order.ie1, pdtion.order_num, 
												s_order.etp, s_order.etd, s_order.qty, s_order.su, pdtion.etp_su, pdtion.qty_shp,
												pdtion.qty_done, s_order.dept
								 FROM s_order,pdtion 
								 WHERE pdtion.factory='".$fty."' AND pdtion.etp_su LIKE '%".$year_mon."%'  
								 			AND s_order.order_num = pdtion.order_num AND s_order.status > 0 ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op

		return $result;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_schdl($fty,$year_mon,$limit_entries)	
#				搜尋 FTY 排產 的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_schdl($fty,$year_mon,$limit_entries,$where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.revise, pdtion.order_num, pdtion.ets, s_order.status, pdtion.etf, 
		                     s_order.qty, s_order.su, pdtion.fty_su, pdtion.qty_shp, pdtion.qty_done  
		             FROM s_order,pdtion 
		             WHERE pdtion.factory='".$fty."' AND pdtion.fty_su LIKE '%".$year_mon."%'  
		                  AND s_order.order_num = pdtion.order_num AND s_order.status > 0 ".$where_str.
		             "ORDER BY pdtion.order_num";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op

		return $result;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($fty, $year,  $cat='capacity')	抓出指定記錄內資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get($fty, $year, $cat='capacity') {

    $sql = $this->sql;

    $q_str = "SELECT * FROM capaci WHERE factory='".$fty."' AND year='".$year."' AND c_type='".$cat."' ";
// echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot access database, pls try later !");
        $this->msg->merge($sql->msg);
        return false;
    }
    if (!$row = $sql->fetch($q_result)) {
        $this->msg->add("Sorry ! cannot find factory[".$fty."] of year:[".$year."] for [".$cat."] record!");
        return false;
    }
    return $row;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_su($fty, $year, $mon, $cat, $add_num)		更新 SU 記錄
#
# $fty,$year,$cat[=c_type] ==>找出記錄 ; 再用 $mon找出 field, 加入更新
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_su($fty, $year, $mon, $cat, $add_num) {

	$sql = $this->sql;

	$field = "m".$mon;

	#####   更新資料庫內容
	$q_str = "UPDATE capaci SET m".$mon." = m".$mon." + ".$add_num." WHERE factory='".$fty."' AND year='".$year."' AND c_type='".$cat."' ";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  cannot update [".$fty."][".$year."][".$cat."]on month[".$mon."][".$add_num."] valume to database, try it later.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	return true;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->delete_su($fty, $year, $mon, $cat, $add_num)		刪除 SU 記錄
#			
#	 $fty,$year,$cat[=c_type] ==>找出記錄 ; 再用 $mon找出 field, 刪除		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function delete_su($fty, $year, $mon, $cat, $add_num) {

		$sql = $this->sql;

		$field = "m".$mon;

		#####   更新資料庫內容
		$q_str = "UPDATE capaci SET m".$mon." = m".$mon." - ".$add_num." WHERE factory='".$fty."' AND year='".$year."' AND c_type='".$cat."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  cannot update [".$fty."][".$year."][".$cat."]on month[".$mon."][".$add_num."] valume to database, try it later.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($fty, $year, $type, $field_name, $field_value)	直接更新 capacity 之某個field內記錄
#		
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($fty, $year, $type, $field_name, $field_value) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE capaci SET ".$field_name." = ".$field_value." WHERE factory='".$fty."' AND year='".$year."' AND c_type='".$type."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update [".$fty."][".$year."][".$type."] on [".$field_name."] value: [".$field_value."] , try it later.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_unord($fty, $year,  $cat='capacity')	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_unord($fty, $year, $where_str='') {

		$sql = $this->sql;
		$s_date=$year."-01-01";
		$e_date=$year."-12-31";
		$m['01']=$m['02']=$m['03']=$m['04']=$m['05']=$m['06']=$m['07']=$m['08']=$m['09']=$m['10']=$m['11']=$m['12']=0;
		$q_str = "SELECT * FROM s_order 
							WHERE factory='".$fty."' 
							AND ((etp <= '".$s_date."' and etd >= '".$e_date."') 
							OR (etp >= '".$s_date."' and etp <='".$e_date."') 
							OR (etd >= '".$s_date."' and etd<='".$e_date."')) 
							AND s_order.status >= 0 and (status < 4 or status = 5) ".$where_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if ($row['ie1'] > 0)
			{
				$T_su=$row['su'];
			}else if ($row['style']=='PS' || $row['style']=='BS' || $row['style']=='BZ' || $row['style']=='DR' || $row['style']=='JK' || $row['style']=='PS-J' || $row['style']=='PS-P' || $row['style']=='PS-S' || $row['style']=='VS' || $row['style']=='SS'){
				$T_su=2*$row['qty'];
			}else{
				$T_su=1*$row['qty'];
			}
			$msu=get_su($row['etp'],$row['etd'],$T_su,$year);
			$sum=0;
			$tmp=0;
			$ets=explode('-',$row['etp']);
			$etf=explode('-',$row['etd']);
			if($ets[0]==$etf[0])
			{
				for ($i=0; $i<sizeof($msu); $i++)
				{
					$suc=explode('-',$msu[$i]);
					$sum=$sum+$suc[1];
				}
				$suc=explode('-',$msu[($i-1)]);
				$suc[1]=$suc[1]+($T_su-$sum);
				$msu[($i-1)]=$suc[0]."-".$suc[1];
			}
			for ($i=0; $i<sizeof($msu); $i++)
			{				
				$suc=explode('-',$msu[$i]);				
				$suc[1]=(int)($suc[1]);
				$m[$suc[0]]=$m[$suc[0]]+$suc[1];
			}
		}
		$m['13']=$m['01']+$m['02']+$m['03']+$m['04']+$m['05']+$m['06']+$m['07']+$m['08']+$m['09']+$m['10']+$m['11']+$m['12'];
		return $m;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_unord($fty, $year,  $cat='capacity')	抓出指定記錄內資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_unord_itme($fty, $year, $month, $where_str='') {

		$sql = $this->sql;
		$s_date=$year."-".$month."-01";
		$e_date=$year."-".$month."-31";
		$q_str = "SELECT * FROM s_order 
							WHERE factory='".$fty."' AND ((etp <= '".$s_date."' and etd >= '".$e_date."') 
							OR (etp >= '".$s_date."' and etp <='".$e_date."') 
							OR (etd >= '".$s_date."' and etd<='".$e_date."')) AND s_order.status >= 0 
							AND (status < 4 or status = 5) ".$where_str.
							"order by etd";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$ord_row[]=$row;
		}
		if (!isset($ord_row)){
			return "none";
		}else{
			return $ord_row;
		}
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_subcon($fty, $year, $where_str='')	抓出指定記錄內外發單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_subcon($fty, $year, $where_str='') {

		$sql = $this->sql;
		$m['01']=$m['02']=$m['03']=$m['04']=$m['05']=$m['06']=$m['07']=$m['08']=$m['09']=$m['10']=$m['11']=$m['12']=0;

	$q_str = "SELECT YEAR(out_date) as yy, MONTH(out_date) as mm, SUM(saw_out_put.su) as su 
						FROM saw_out_put, pdt_saw_line, s_order
						WHERE  s_order.order_num = saw_out_put.ord_num AND saw_out_put.line_id = pdt_saw_line.id AND 
									out_date like '$year%' AND sc =1 AND pdt_saw_line.fty='$fty' ".$where_str."
						GROUP BY YEAR(out_date), MONTH(out_date)
						";
		
	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$mm = $row['mm'];
			if ($row['mm'] < 10) $mm = '0'.$row['mm'];
			$m[$mm] = $row['su'];			
		}			
/*
		
		$s_date=$year."-01-01";
		$e_date=$year."-12-31";
		$q_str = "SELECT * FROM s_order,pdtion 
							WHERE s_order.order_num=pdtion.order_num AND s_order.factory='".$fty."' 
							AND ((start <= '".$e_date."' AND finish >= '".$s_date."') 
							OR (start <= '".$e_date."' and finish IS NULL) 
							OR (start <= '".$e_date."' and finish = '0000-00-00')) 
							AND start IS NOT NULL AND s_order.status > 0 ".$where_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if ($row['sub_con'] == 1)
			{
				$out_ary = extract_su($row['out_su']);
				for ($i=0; $i< sizeof($out_ary); $i++)
				{
					$yy = substr($out_ary[$i]['mon'],0,4);
					$mm = substr($out_ary[$i]['mon'],-2);
					if ($yy == $year)
					{
						$m[$mm]=$m[$mm]+$out_ary[$i]['su'];
					}
				}
			}
			
		}
*/
		$m['13']=$m['01']+$m['02']+$m['03']+$m['04']+$m['05']+$m['06']+$m['07']+$m['08']+$m['09']+$m['10']+$m['11']+$m['12'];
		return $m;
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_etd_ord($fty, $year, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT * FROM s_order,pdtion 
							WHERE s_order.order_num=pdtion.order_num and s_order.factory='".$fty."'
									  AND etd like '".$year."%' AND s_order.status > 0 ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$result[]=$row;
			
		}
		if (isset($result))
		{
			return $result;
		}else{
			return false;
		}
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_etd_unapv_ord($fty, $year, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT s_order.qty, s_order.id, s_order.su, s_order.order_num, s_order.etp, s_order.etd, s_order.status FROM s_order 
							WHERE s_order.factory='".$fty."'
								AND etd like '".$year."%' AND s_order.status > 0 AND (s_order.status < 4 || s_order.status = 5) ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$result[]=$row;
			
		}
		if (isset($result))
		{
			return $result;
		}else{
			return false;
		}
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

		$q_header = "SELECT s_order.order_num, s_order.revise, s_order.uprice, s_order.ie1, smpl_apv, 
												s_order.partial_num, s_order.status, s_order.smpl_apv, s_order.cm, s_order.style, 												
		             	      pdtion.mat_shp , pdtion.out_su, pdtion.sub_con, pdtion.m_acc_shp, mat_etd, m_acc_etd,		             	      
		             	      order_partial.p_qty_done as qty_done, 
		             	      order_partial.p_qty as qty, order_partial.p_su as su, order_partial.mks,
		             	      order_partial.p_qty_shp as qty_shp, order_partial.p_shp_date as shp_date,
		             	      order_partial.p_etp as etp, order_partial.p_etd as etd
		             FROM s_order, pdtion, order_partial
		             WHERE s_order.order_num = order_partial.ord_num AND pdtion.order_num = order_partial.ord_num AND
		             			 s_order.factory='".$fty."' AND 
		             			 order_partial.p_etd LIKE '".$year_mon."%' 
		                   AND s_order.order_num = pdtion.order_num AND s_order.status >= 4 AND s_order.status <> 5 ".$where_str.
		             " ORDER BY etd";
		             
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op

		return $result;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_etp_unout($fty,$year_mon,$limit_entries, $where_str='')
#			搜尋 order ETP 未完成的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp_unout($fty,$year_mon,$limit_entries, $where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		$tmp = 0;
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, s_order.revise, s_order.uprice, s_order.ie1, order_partial.p_etp as etp, 
											  order_partial.p_etd as etd, order_partial.p_qty as qty, order_partial.p_su as su,
											  s_order.status, order_partial.p_qty_shp qty_shp, order_partial.p_shp_date as shp_date, 
											  pdtion.mat_shp , pdtion.sub_con, pdtion.m_acc_shp, s_order.smpl_apv,
		             	      mat_etd, m_acc_etd ,smpl_apv, sum(saw_out_put.su) as out_su, sum(saw_out_put.qty) as qty_done,
		             	      s_order.style, pdtion.mat_eta, pdtion.m_acc_eta, s_order.partial_num, order_partial.mks
		             FROM s_order, pdtion, saw_out_put, order_partial
		             WHERE s_order.order_num = order_partial.ord_num AND saw_out_put.p_id = order_partial.id AND
		             			 order_partial.ord_num = pdtion.order_num AND order_partial.ord_num = saw_out_put.ord_num AND
		             			 pdtion.order_num = order_partial.ord_num AND s_order.order_num = saw_out_put.ord_num AND
		             			 s_order.factory='".$fty."' AND order_partial.p_etd LIKE '".$year_mon."%' AND		             			  
		                   s_order.order_num = pdtion.order_num AND s_order.status > 0 ".$where_str.
		             " GROUP BY s_order.order_num ORDER BY etd";

$q_header = "
SELECT 
order_partial.id,
s_order.order_num, s_order.revise, s_order.uprice, s_order.ie1, s_order.status, s_order.smpl_apv, s_order.style, s_order.partial_num, 
order_partial.p_etp as etp, order_partial.p_etd as etd, order_partial.p_qty as qty, order_partial.p_su as su, order_partial.p_qty_shp qty_shp, order_partial.p_shp_date as shp_date, order_partial.mks , 
pdtion.mat_shp , pdtion.sub_con, pdtion.m_acc_shp, pdtion.mat_eta, pdtion.m_acc_eta , 
mat_etd, m_acc_etd ,smpl_apv, sum(saw_out_put.su) as out_su, sum(saw_out_put.qty) as qty_done

FROM order_partial 
left join s_order on ( s_order.order_num = order_partial.ord_num ) 
left join pdtion on ( order_partial.ord_num = pdtion.order_num ) 
left join saw_out_put on ( saw_out_put.p_id = order_partial.id AND order_partial.ord_num = saw_out_put.ord_num ) 

WHERE 
s_order.status > 0  AND s_order.factory='".$fty."' AND 
order_partial.p_etd LIKE '".$year_mon."%' ".$where_str."

GROUP BY order_partial.id  

ORDER BY etd
";
                     
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if ($result){   // 當查尋無資料時
				$tmp = 1;
			}


		// $q_header = "SELECT s_order.order_num, s_order.revise, s_order.uprice, s_order.ie1, order_partial.p_etp as etp, 
												// s_order.partial_num,
												// order_partial.p_etd as etd, order_partial.p_qty as qty, order_partial.p_su as su, s_order.status,
		             	   		// order_partial.p_qty_shp as qty_shp, order_partial.p_shp_date as shp_date, order_partial.mks,
		             	   		// pdtion.mat_shp , pdtion.sub_con, pdtion.m_acc_shp, s_order.smpl_apv,
		             	    	// mat_etd, m_acc_etd ,smpl_apv, order_partial.p_qty_done as qty_done, s_order.style, 
		             	    	// pdtion.mat_eta, pdtion.m_acc_eta
		             // FROM s_order, pdtion, order_partial
		             // WHERE s_order.order_num = order_partial.ord_num AND pdtion.order_num = order_partial.ord_num AND
		             			 // s_order.factory='".$fty."' AND order_partial.p_etd LIKE '".$year_mon."%' AND
											 // s_order.status >=4 AND s_order.status <> 5 AND s_order.status < 10 AND										
										   // pdtion.qty_done = 0 AND
		                   // s_order.order_num = pdtion.order_num ".$where_str.
		             // " ORDER BY etd";       
		// if (!$srh->add_q_header($q_header)) {
			// $this->msg->merge($srh->msg);
			// return false;
		// }

		// $result2 = $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		// if (!is_array($result)) {
			// $this->msg->merge($srh->msg);
			// return false;		    
		// }
		// $this->msg->merge($srh->msg);
			// if ($result2){   // 當查尋無資料時
//				#$op['record_NONE'] = 1;

				// $tmp = 1;

				// for($i=0; $i<sizeof($result2); $i++)
				// {
					// $result2[$i]['out_su'] = 0;
					// $result[] = $result2[$i]; 
				// }
			// }
			// if($tmp == 0) $result = 'none';

//		$result;  // 資料錄 拋入 $op

		return $result;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_unapv_etp_ord($fty,$year_mon,$limit_entries)	
#			搜尋 order ETP 的月份未核可訂單 [呼叫 s_order table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_unapv_etp_ord($fty,$year_mon,$limit_entries, $where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, s_order.id, s_order.status, s_order.style, s_order.uprice as fob,
												order_partial.p_etp as etp, order_partial.p_etd as etd,
											  order_partial.p_qty as qty, order_partial.p_su as su,
											  s_order.partial_num, order_partial.mks											  		             	  
		             FROM s_order, order_partial
		             WHERE s_order.order_num = order_partial.ord_num AND
		             			 s_order.factory='".$fty."' AND order_partial.p_etd LIKE '".$year_mon."%' AND		             			 
		                   s_order.status >= 0  AND ( s_order.status < 4 || s_order.status = 5 || s_order.status = 13)".$where_str.
		             "ORDER BY p_etd";
		             
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op

			for($i=0; $i<sizeof($result); $i++)
			{
				if ($result[$i]['su'] == 0)
				{
					if ($result[$i]['style']=='PS' || $result[$i]['style']=='BS' || $result[$i]['style']=='BZ' || $result[$i]['style']=='DR' || $result[$i]['style']=='JK' || $result[$i]['style']=='PS-J' || $result[$i]['style']=='PS-P' || $result[$i]['style']=='PS-S' || $result[$i]['style']=='VS' || $result[$i]['style']=='SS'){
						$result[$i]['su']=2*$result[$i]['qty'];
					}else{
						$result[$i]['su']=1*$result[$i]['qty'];
					}
				} 
			}

		return $result;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_etp($fty,$year_mon,$limit_entries)	
#			搜尋 order ETP 的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp_sch($fty,$year_mon,$limit_entries) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT order_partial.p_etp as etp, order_partial.p_etd as etd, order_partial.mks,
											  order_partial.p_etf as etf, order_partial.p_ets as ets ,
		                    order_partial.p_su as su, order_partial.p_qty as qty,
		                    s_order.order_num, s_order.uprice, s_order.status, s_order.style, s_order.partial_num
		             FROM s_order, pdtion, order_partial 
		             WHERE order_partial.ord_num = s_order.order_num AND  s_order.order_num=pdtion.order_num AND
		             			 s_order.factory='".$fty."' AND order_partial.p_etd LIKE '".$year_mon."%' AND  
		             			 s_order.status > 6 order by etd";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op

		return $result;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

#	->search_etp($fty,$year_mon,$limit_entries)	
#			搜尋 order ETP 的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp_unsch($fty,$year_mon,$limit_entries) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, s_order.uprice, s_order.style, s_order.status,
												order_partial.p_etp as etp, 
												order_partial.p_etd as etd, order_partial.p_qty as qty, 
		                    order_partial.p_su as su,  order_partial.p_etf as etf, 
		                    order_partial.p_ets as ets
		             FROM s_order, order_partial
		             WHERE order_partial.ord_num = s_order.order_num AND s_order.factory='".$fty."' AND 
		             			 order_partial.p_etd LIKE '".$year_mon."%' AND s_order.status <= 6 AND 
		             			 s_order.status > 3 AND s_order.status <> 5 order by p_etd";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op

		return $result;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_daily_out($year,$mon)	  加總 月份 之日產出數	[以array傳回 每日之 su ,qty] 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sum_etp_out($fty,$year,$mon,$where_str) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		$year_mon = $year."-".$mon;	
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, order_partial.p_qty_done as qty_done, order_partial.p_etd as etd, 
												order_partial.p_qty as qty,s_order.status, sum(shipping.qty) as qty_shp, s_order.uprice, shp_date,
		              			order_partial.p_su as su , pdtion.out_su , s_order.style, sum(shipping.su) as shp_su
		             FROM s_order, pdtion, shipping, order_partial
		             WHERE order_partial.ord_num = s_order.order_num AND order_partial.id = shipping.p_id AND
		             			 s_order.order_num = shipping.ord_num AND s_order.factory='".$fty."' AND 
		             		   order_partial.p_etd LIKE '".$year_mon."%' AND s_order.order_num=pdtion.order_num AND 
		             			 s_order.status > 7 ".$where_str.
		             " GROUP BY s_order.order_num ORDER BY etd";
	             
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2(500);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op
		return $result;	
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_daily_out($year,$mon)	  加總 月份 之日產出數	[以array傳回 每日之 su ,qty] 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_etp_out($fty,$year,$mon,$where_str) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		$year_mon = $year."-".$mon;	
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, s_order.ie1 , s_order.style, s_order.partial_num,
												s_order.status,s_order.uprice,
												order_partial.p_etd as etd, order_partial.p_shp_date as shp_date,
												order_partial.p_qty as qty, order_partial.p_qty_shp as qty_shp, order_partial.mks,
												sum(saw_out_put.su) as su, sum(saw_out_put.qty) as qty_done		                     
		             FROM s_order, saw_out_put, pdt_saw_line, order_partial
		             WHERE s_order.order_num = order_partial.ord_num AND 
											 order_partial.id = saw_out_put.p_id AND
		             			 s_order.order_num = saw_out_put.ord_num AND 
											 saw_out_put.line_id = pdt_saw_line.id AND		             			 
		             			 s_order.factory='".$fty."' AND 
											 order_partial.p_etd LIKE '".$year_mon."%' AND
		             			 s_order.status >= 7 ".$where_str.
		             " GROUP BY order_partial.ord_num ORDER BY etd";
		// echo $q_header."<br>";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2(500);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op
		return $result;	
	} // end func	

	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_shp_out($year,$mon)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sum_shp_out($fty,$year,$mon,$where_str) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		$year_mon = $year."-".$mon;	
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, s_order.ie1 , s_order.style, s_order.partial_num,
												s_order.status,s_order.uprice,
												order_partial.p_etd as etd, order_partial.p_shp_date as shp_date,
												order_partial.p_qty as qty, order_partial.p_qty_shp as qty_shp, order_partial.mks,
												sum(saw_out_put.su) as su, sum(saw_out_put.qty) as qty_done		                     
		             FROM s_order, saw_out_put, pdt_saw_line, order_partial
		             WHERE s_order.order_num = order_partial.ord_num AND 
											 order_partial.id = saw_out_put.p_id AND
		             			 s_order.order_num = saw_out_put.ord_num AND 
											 saw_out_put.line_id = pdt_saw_line.id AND		             			 
		             			 s_order.factory='".$fty."' AND 
											 order_partial.p_etd LIKE '".$year_mon."%' AND
		             			 s_order.status >= 7 ".$where_str.
		             " GROUP BY order_partial.ord_num ORDER BY etd";
		// echo $q_header."<br>";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2(500);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op
		return $result;	
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_daily_out($year,$mon)	  加總 月份 之日產出數	[以array傳回 每日之 su ,qty] 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_out($fty,$year_mon) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();		
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, pdtion.qty_done, s_order.etd, s_order.qty,s_order.status, 
		             s_order.ie1, s_order.su FROM s_order, pdtion WHERE s_order.factory='".$fty."' AND s_order.etd LIKE '".$year_mon."' AND s_order.order_num=pdtion.order_num AND s_order.status > 7";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2(500);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op
		return $result;	
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->sum_daily_out($year,$mon)	  加總 月份 之日產出數	[以array傳回 每日之 su ,qty] 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_un_out($fty,$year_mon) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();		
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.order_num, pdtion.qty_done, s_order.etd, s_order.qty,s_order.status, 
		             s_order.ie1, s_order.su FROM s_order, pdtion WHERE s_order.factory='".$fty."' AND s_order.etd LIKE '".$year_mon."' AND s_order.order_num=pdtion.order_num AND s_order.status > 0 AND s_order.status < 10 ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$result= $srh->send_query2(500);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
//				$op['record_NONE'] = 1;
				$result = "none";
			}

//		$result;  // 資料錄 拋入 $op
		return $result;	
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_etp($fty,$year_mon,$limit_entries)	
#			搜尋 order ETP 的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp_full($fty,$yy,$where_str='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_str = "SELECT etp_su
								 FROM s_order,pdtion 
								 WHERE s_order.order_num=pdtion.order_num AND pdtion.factory='".$fty."' 
								 			 AND s_order.status > 0  
								 			 AND pdtion.etp_su like '%$yy%' ".$where_str."order by etp_su";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$tal_su = 0;
		while ($row = $sql->fetch($q_result)) {
			$etp_su = explode(',',$row['etp_su']);
			for($i=0; $i<sizeof($etp_su); $i++)
			{
				$tmp_yy = substr($etp_su[$i],0,4);
				$tmp_su = substr($etp_su[$i],6);
				
				if($tmp_yy == $yy) 
				{
					$tal_su+=$tmp_su;
				}
			}
		}
		return $tal_su;
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cm($fty, $year)	抓出指定記錄內工繳總額 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cm($fty, $year, $fty_cm, $where_str='') {

		$sql = $this->sql;
		$sch_mm = array("01","02","03","04","05","06","07","08","09","10","11","12");
		$m['01']=$m['02']=$m['03']=$m['04']=$m['05']=$m['06']=$m['07']=$m['08']=$m['09']=$m['10']=$m['11']=$m['12']=0;
		for($i =0; $i<sizeof($sch_mm); $i++)
		{
			$q_str = "SELECT shipping.qty, s_order.cm, s_order.ie1, shipping.cm as s_cm 		
								FROM s_order,shipping 
								WHERE s_order.order_num=shipping.ord_num AND s_order.factory='".$fty."' 
								AND k_date like '".$year."-".$sch_mm[$i]."%' AND s_order.status > 0 ".$where_str;
							
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$tmp_cm = 0;
			while ($row = $sql->fetch($q_result)) {
				if($row['s_cm'] == 0 && $row['cm'] <> 0)
				{
					$tmp_cm += $row['qty'] * $row['ie1'] * $fty_cm;
				}else{
					$tmp_cm +=$row['s_cm'];					
				}
			}
			$m[$sch_mm[$i]] = $tmp_cm;
		}
		$m['13']=$m['01']+$m['02']+$m['03']+$m['04']+$m['05']+$m['06']+$m['07']+$m['08']+$m['09']+$m['10']+$m['11']+$m['12'];
		return $m;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cm($fty, $year)	抓出指定記錄內工繳總額 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function create_mm($rec,$year) {

		$sql = $this->sql;
		$m['01']=$m['02']=$m['03']=$m['04']=$m['05']=$m['06']=$m['07']=$m['08']=$m['09']=$m['10']=$m['11']=$m['12']=0;
		for($i=0; $i<sizeof($rec); $i++)
		{
			$etp_su = explode(',',$rec[$i]['mm_su']);
			for($j=0; $j<sizeof($etp_su); $j++)
			{
				if(substr($etp_su[$j],0,4) == $year)
				{
					$mm = substr($etp_su[$j],4,2);
			
					$m[$mm] += substr($etp_su[$j],6);

				}
			}
		}


		$m['13']=$m['01']+$m['02']+$m['03']+$m['04']+$m['05']+$m['06']+$m['07']+$m['08']+$m['09']+$m['10']+$m['11']+$m['12'];
		return $m;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cm($fty, $year)	抓出指定記錄內工繳總額 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_qty($fty, $year, $where_str='') {
		$tmp_rec = array();
		$sql = $this->sql;			
			$q_str = "SELECT pdtion.etp_su as mm_su		
								FROM s_order,pdtion 
								WHERE s_order.order_num=pdtion.order_num AND s_order.factory='".$fty."' 
								AND pdtion.etp_su like '%".$year."%' ".$where_str;
					
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$tmp_cm = 0;
			while ($row = $sql->fetch($q_result)) {
				$tmp_rec[] = $row ;
			}
			$ord_rec = $this->create_mm($tmp_rec,$year);

		return $ord_rec;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cm($fty, $year)	抓出指定記錄內工繳總額 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_sch_qty($fty, $year, $where_str='') {
		$tmp_rec = array();
		$sql = $this->sql;			
			$q_str = "SELECT pdtion.fty_su as mm_su		
								FROM s_order,pdtion 
								WHERE s_order.order_num=pdtion.order_num AND s_order.factory='".$fty."' 
								AND pdtion.fty_su like '%".$year."%' ".$where_str;
							
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$tmp_cm = 0;
			while ($row = $sql->fetch($q_result)) {
				$tmp_rec[] = $row ;
			}
			$ord_rec = $this->create_mm($tmp_rec,$year);

		return $ord_rec;
	} // end func		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_unord($fty, $year,  $cat='capacity')	抓出指定記錄內外發單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_out_qty($fty, $year, $where_str='') {

		$sql = $this->sql;
		$sch_mm = array("01","02","03","04","05","06","07","08","09","10","11","12");
		$m['01']=$m['02']=$m['03']=$m['04']=$m['05']=$m['06']=$m['07']=$m['08']=$m['09']=$m['10']=$m['11']=$m['12']=0;
		for($i =0; $i<sizeof($sch_mm); $i++)
		{
			$q_str = "SELECT saw_out_put.su		
								FROM s_order,saw_out_put 
								WHERE s_order.order_num=saw_out_put.ord_num AND s_order.factory='".$fty."' 
								AND saw_out_put.out_date like '".$year."-".$sch_mm[$i]."%' AND s_order.status > 0 ".$where_str;
							
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while ($row = $sql->fetch($q_result)) {
				$m[$sch_mm[$i]] += $row['su'];
			}
			
		}
		$m['13']=$m['01']+$m['02']+$m['03']+$m['04']+$m['05']+$m['06']+$m['07']+$m['08']+$m['09']+$m['10']+$m['11']+$m['12'];
		return $m;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cm($fty, $year)	抓出指定記錄內工繳總額 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_shp_qty($fty, $year, $where_str='') {

		$sql = $this->sql;
		$sch_mm = array("01","02","03","04","05","06","07","08","09","10","11","12");
		$m['01']=$m['02']=$m['03']=$m['04']=$m['05']=$m['06']=$m['07']=$m['08']=$m['09']=$m['10']=$m['11']=$m['12']=0;
		for($i =0; $i<sizeof($sch_mm); $i++)
		{
			$q_str = "SELECT shipping.qty		
								FROM s_order,shipping 
								WHERE s_order.order_num=shipping.ord_num AND s_order.factory='".$fty."' 
								AND k_date like '".$year."-".$sch_mm[$i]."%' AND s_order.status > 0 ".$where_str;
							
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$tmp_cm = 0;
			while ($row = $sql->fetch($q_result)) {
				$m[$sch_mm[$i]] += $row['qty'];
			}
			
		}
		$m['13']=$m['01']+$m['02']+$m['03']+$m['04']+$m['05']+$m['06']+$m['07']+$m['08']+$m['09']+$m['10']+$m['11']+$m['12'];
		return $m;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_etp($fty,$year_mon,$limit_entries)	
#			搜尋 order ETP 的月份訂單 [呼叫 pdtion table] 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_etp_ord_style($fty,$year_mon,$type, $where_str='') {

		$sql = $this->sql;

		$q_str = "SELECT order_partial.p_etp as etp, order_partial.p_qty_shp as qty_shp, order_partial.mks,
										 order_partial.p_etd as etd, order_partial.p_qty as qty, order_partial.p_su as su, 
										 order_partial.p_qty_done as qty_done, order_partial.p_shp_date as shp_date,
										 s_order.status, s_order.smpl_apv, s_order.order_num, s_order.revise, s_order.uprice, 
		             	   s_order.style, s_order.cm, s_order.partial_num, s_order.ie1, smpl_apv,
		             	   pdtion.mat_shp , pdtion.sub_con, pdtion.m_acc_shp, mat_etd, m_acc_etd , pdtion.out_su		             	    
		             FROM s_order, pdtion, style_type, order_partial
		             WHERE s_order.order_num = order_partial.ord_num AND pdtion.order_num = order_partial.ord_num AND
		             			 s_order.order_num = pdtion.order_num AND s_order.style = style_type.style_type AND 
		             			 s_order.factory='".$fty."' AND p_etd LIKE '".$year_mon."%' AND
		                   style_type.memo = '".$type."' AND s_order.status > 0 ".$where_str.
		             " ORDER BY etd";
		             
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$tmp_cm = 0;
			while ($row = $sql->fetch($q_result)) {
				$result[] = $row;
			}


		return $result;
	} // end func	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>