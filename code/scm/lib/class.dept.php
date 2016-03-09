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

class DEPT {
		
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


/*----------
# 功能說明 : 
# 導入變數 : 
# 輸入變數 : 
# 輸出變數 : 
# 撰寫日期 : 2010/06/02
# 備註說明 : 
----------*/

function search($mode=0,$where_str='',$table='',$m_sql='',$page_view='',$show_num = 10) {
	global $Search;

	if(!empty($set_dept))$run2=1;
	if(!empty($run2)){
		unset($_SESSION['PAGE'][$page_view]);
		$status=0;
		$where_str = "where ";
		if (!empty($run2)){
			$where_str .= $set_dept;
			$status=1;
		}
	}

	$op = $Search->page_sorting($table,$m_sql,$page_view,$where_str,$show_num);

	return $op;
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_field_value($n_field,$where_str="") {
  $sql = $this->sql;
  
  $fields = array();
  $q_str = "SELECT ".$n_field." FROM dept ".$where_str;
  // echo $q_str,'<br>';
  if (!$q_result = $sql->query($q_str)) {
    $_SESSION['msg'][] = ("Error! 無法存取資料庫!");
    return false;
  }

  while ($row = $sql->fetch($q_result)) {
    $fields[] = $row;
  }

  return $fields;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新部門別
#		加入新部門別			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### 檢查輸入項目					
		if (!$parm['dept_code'] = trim($parm['dept_code'])) {
			$this->msg->add("Error ! 請輸入部門代號 !");
		    return false;
		}
					# 全部改成大寫
		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# 檢查是否有重覆
		$q_str = "SELECT id FROM dept WHERE dept_code='".$parm['dept_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! 這個代號已經有別的部門使用了，請換別的代號!");
			return false;    	    
		}
					# 加入資料庫
		$q_str = "INSERT INTO dept (dept_name, dept_code, chief) VALUES ('".$parm['dept_name']."','".$parm['dept_code']."','".$parm['chief']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //取出 新的 id
		$this->msg->add("成功 新增部門別 : [".$parm['dept_code']."]。") ;
		return $dept_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	搜尋 部門別資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search2($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM dept";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("dept_code ");
		$srh->row_per_page = 12;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
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

		$op['dept'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, dept_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $dept_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM dept WHERE id='$id' ";
		} elseif ($dept_code) {
			$q_str = "SELECT * FROM dept WHERE dept_code='$dept_code' ";
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update($parm)		更新 部門別資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
					# 全部改成大寫
		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# 檢查是否有這個檔案存在
		$q_str = "SELECT id FROM dept WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! 無法找到資料庫記錄!");
			return false;    	    
		}

###########  撿查 新加入的 dept_code 是否為唯一 [note:可能會找到原來的自己 #####
$q_str2 = "SELECT id FROM dept WHERE dept_code='".$parm['dept_code']."'"." AND id <> '".$parm['id']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			return false;    	    
		}
		if ($sql->num_rows($q_result2) ) {
			$this->msg->add("SORRY ! 這個代號已經有別的部門使用了，請換別的代號!");
			return false;    	    
		}
		
		#####   更新資料庫內容
		$q_str = "UPDATE dept SET dept_code='".$parm['dept_code']."', dept_name='".$parm['dept_name']."', chief='". $parm['chief']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 部門別資料內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE dept SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

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
			$this->msg->add("Error !請指明 dept 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM dept WHERE id='$id' ";

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
		$q_str = "SELECT ".$n_field." FROM dept".$where_str;
// echo $q_str;
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


# ????????????????????????????????????????????????????????????
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_smpl_serious($dept_code, $year_code): 由年度及部門別 算出 smpl 的序號  再存入資料庫
#      運算由 dept_code 取出 smpl_num再加上 部門別及年碼(兩位)；如該年份時自動加上 故smpl_num為cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_smpl_serious($dept_code, $year_code) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);

		if (!$dept) {
			$this->msg->add("Error ! 無法存取部門別資料庫.");		    
			return false;
		}

		$year_serious = decode_number($dept['style_num']);    //年度序號之陣列....以兩碼為主

			if (isset($year_serious[$year_code])) {   // 序號 存在時

				$serious = $year_serious[$year_code] + 1+1000000;
				$style_code = "S".$dept_code.$year_code."-".substr($serious,3);
				$new_num = substr($serious,3);
				$year_serious[$year_code] = $new_num;
				$style_num = encode_number($year_serious);
					
			} else {

				$style_code = "S".$dept_code.$year_code."-"."0001";
				$year_serious[$year_code] = "0001";
				$style_num = encode_number($year_serious);  // 改成cvs字串[function.php]
			}

			//  寫入新的 num 入資料庫
			$q_str = "UPDATE dept SET style_num ='".$style_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法更新資料庫內容.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $style_code;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_wis_num($dept_code, $year_code): 由年度及部門別 算出 樣本製造令 的序號  再存入資料庫
#      運算由 dept_code 取出 wsi_num再加上 部門別及年碼(兩位)；如該年份時自動加上 故wsi_num為cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_wis_num($dept_code, $year_code) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);

		if (!$dept) {
			$this->msg->add("Error ! 無法存取部門別資料庫.");		    
			return false;
		}

		$year_serious = decode_number($dept['wis_num']);    //年度序號之陣列....以兩碼為主

					if (isset($year_serious[$year_code])) {   // 序號 存在時

						$serious = $year_serious[$year_code] + 1+1000000;
						$style_code = "WI-".$dept_code.$year_code."-".substr($serious,3);
						$new_num = substr($serious,3);
						
						$year_serious[$year_code] = $new_num;
						$style_num = encode_number($year_serious);
					
					} else {

						$style_code = "WI-".$dept_code.$year_code."-"."0001";
						$year_serious[$year_code] = "0001";
						$style_num = encode_number($year_serious);  // 改成cvs字串[function.php]
					}

			//  寫入新的 num 入資料庫
			$q_str = "UPDATE dept SET wis_num ='".$style_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法更新資料庫內容.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $style_code;

	} // end func



#############   2005/05/17 更新 製作令 編號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_wi_num($dept_code, $year_code): 由年度及部門別 算出 樣本製造令 的序號  再存入資料庫
#      運算由 dept_code 取出 wsi_num再加上 部門別及年碼(兩位)；如該年份時自動加上 故wsi_num為cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_wi_num($dept_code, $year_code) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);

		if (!$dept) {
			$this->msg->add("Error ! 無法存取部門別資料庫.");		    
			return false;
		}

		$year_serious = decode_number($dept['wis_num']);    //年度序號之陣列....以兩碼為主

					if (isset($year_serious[$year_code])) {   // 序號 存在時

						$serious = $year_serious[$year_code] + 1+1000000;
						$style_code = "WI-".$dept_code.$year_code."-".substr($serious,3);
						$new_num = substr($serious,3);
						
						$year_serious[$year_code] = $new_num;
						$style_num = encode_number($year_serious);
					
					} else {

						$style_code = "WIS-".$dept_code.$year_code."-"."0001";
						$year_serious[$year_code] = "0001";
						$style_num = encode_number($year_serious);  // 改成cvs字串[function.php]
					}

			//  寫入新的 num 入資料庫
			$q_str = "UPDATE dept SET wis_num ='".$style_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法更新資料庫內容.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $style_code;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_order_num($dept_code, $year_code, $cust): 由年度及部門別 算出 訂單 的序號  再存入資料庫
#      運算由 dept_code 取出 order_num再加上 部門別及年碼(兩位)；如該年份時自動加上 故order_num為cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	# 不另外記錄新增訂單紛部門的記錄
	function get_order_num_close($dept_code, $year_code, $cust) {
		$sql = $this->sql;
		$dept_name = $dept_code;

		if($dept_code == 'KB') $dept_code = 'KA';
		if($dept_code == 'J1') $dept_code = 'KA';
		if($dept_code == 'LY') $dept_code = 'KA';
		
		$dept = $this->get(0,$dept_code);  // 先在資料庫找看有沒有該筆資料 

		if (!$dept) {
			$this->msg->add("Error ! 無法存取部門別資料庫.");		    
			return false;
		}

		$year_serious = decode_ord_number($dept['order_num']);    //年度序號之陣列....以兩碼為主

					if (isset($year_serious[$year_code])) {   // 序號 存在時
						$serious = $year_serious[$year_code] + 1+1000;
						#M11010501
						// echo $dept_code;
						if ( $dept_name == "HJ" || $dept_name == 'LY' || $dept_name == "DA" || $dept_name == "CF" )//判斷部門是否代表工廠,代表工廠時==>取部門的第一個字
						{															  //不代表工廠時==>取部門的第二個字	
							$order_code = substr($dept_name,0,1).$cust.$year_code."-".substr($serious,1);							
						}else{
							$order_code = substr($dept_name,1,1).$cust.$year_code."-".substr($serious,1);
						}
						$new_num = substr($serious,1);
						
						$year_serious[$year_code] = $new_num;
						$order_num = encode_number($year_serious);
					
					} else {
						$order_code = substr($dept_name,1,1).$cust.$year_code."-"."0001";
						$year_serious[$year_code] = "0001";
						$order_num = encode_number($year_serious);  // 改成cvs字串[function.php]
					}
					

			//  寫入新的 num 入資料庫
			$q_str = "UPDATE dept SET order_num ='".$order_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法更新資料庫內容.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $order_code;

	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_order_num($dept_code, $year_code, $cust): 由年度及部門別 算出 訂單 的序號  再存入資料庫
#      運算由 dept_code 取出 order_num再加上 部門別及年碼(兩位)；如該年份時自動加上 故order_num為cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_pre_order_num($dept_code, $year_code, $cust) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);  // 先在資料庫找看有沒有該筆資料 

		if (!$dept) {
			$this->msg->add("Error ! 無法存取部門別資料庫.");		    
			return false;
		}

		$year_serious = decode_ord_number($dept['pre_ord_num']);    //年度序號之陣列....以兩碼為主

					if (isset($year_serious[$year_code])) {   // 序號 存在時
						$serious = $year_serious[$year_code] + 1+1000;
						$order_code = "P".$year_code.$cust."-".substr($serious,1);
						$new_num = substr($serious,1);
						
						$year_serious[$year_code] = $new_num;
						$order_num = encode_number($year_serious);
					
					} else {
						$order_code = "P".$year_code.$cust."-"."001";
						$year_serious[$year_code] = "001";
						$order_num = encode_number($year_serious);  // 改成cvs字串[function.php]
					}
					

			//  寫入新的 num 入資料庫
			$q_str = "UPDATE dept SET pre_ord_num ='".$order_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法更新資料庫內容.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $order_code;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_smpl_num($year_code, $cust): 由年度及部門別 算出 樣本訂單 的序號  再存入資料庫
#      運算寫入 dept_code="PM"(生企) "S"+年碼(壹位)+客戶(兩位)+4位流水
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_smpl_num($year_code, $cust) {
		$sql = $this->sql;

		$dept = $this->get(0,'PM');  // 先資料庫找看有沒有生企這個單位 [代號為 PM] 

		if (!$dept) {
			$this->msg->add("Error ! cannot get dept PM from database.");		    
			return false;
		}

		$year_serious = decode_smpl_number($dept['order_num']);    //年度序號之陣列....以兩碼為主

			if (isset($year_serious[$year_code])) {   // 序號 存在時
				$serious = $year_serious[$year_code] + 1+10000;
				$order_code = "S".$year_code.$cust."-".substr($serious,1);
				$new_num = substr($serious,1);
						
				$year_serious[$year_code] = $new_num;
				$order_num = encode_number($year_serious);
					
			} else {
				$order_code = "S".$year_code.$cust."-"."0001";
				$year_serious[$year_code] = "0001";
				$order_num = encode_number($year_serious);  // 改成cvs字串[function.php]

			}

			//  寫入新的 num 入資料庫 [寫入生企的 order_num 欄內 ----
			$q_str = "UPDATE dept SET order_num ='".$order_num."'  WHERE dept_code ='PM'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! unable to update DEPT database.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $order_code;

	} // end func





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fabric_serious($dept_code, $year_code,$kind): 由年度及部門別 算出 smpl 的序號  再存入資料庫
#      運算由 dept_code 取出 smpl_num再加上 部門別及年碼(兩位)；如該年份時自動加上 故smpl_num為cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_fabric_serious($dept_code, $year_code, $kind) {
		$sql = $this->sql;

		$dept = $this->get(0,'PM');

		if (!$dept) {
			$this->msg->add("Error ! 無法存取部門別資料庫.");		    
			return false;
		}

		$year_serious = decode_number($dept['fabric_num']);    //年度序號之陣列....以兩碼為主

					if (isset($year_serious[$year_code])) {   // 序號 存在時

						$serious = $year_serious[$year_code] + 1+1000000;
						$art_code = "F".$dept_code.$year_code.$kind."-".substr($serious,3);
						$new_num = substr($serious,3);
						$year_serious[$year_code] = $new_num;
						$fabric_num = encode_number($year_serious);
					
					} else {

						$art_code = "F".$dept_code.$year_code.$kind."-"."0001";
						$year_serious[$year_code] = "0001";
						$fabric_num = encode_number($year_serious);  // 改成cvs字串[function.php]
					}

			//  寫入新的 num 入資料庫
			$q_str = "UPDATE dept SET fabric_num ='".$fabric_num."'  WHERE dept_code ='PM'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法更新資料庫內容.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $art_code;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fabric_serious($dept_code, $year_code,$kind): 由年度及部門別 算出 smpl 的序號  再存入資料庫
#      運算由 dept_code 取出 smpl_num再加上 部門別及年碼(兩位)；如該年份時自動加上 故smpl_num為cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_fab_stock_serious($dept_code, $year_code, $kind) {
		$sql = $this->sql;

		$dept = $this->get(0,'PM');

		if (!$dept) {
			$this->msg->add("Error ! 無法存取部門別資料庫.");		    
			return false;
		}

		$year_serious = decode_number($dept['fabstock_num']);    //年度序號之陣列....以兩碼為主

					if (isset($year_serious[$year_code])) {   // 序號 存在時

						$serious = $year_serious[$year_code] + 1+1000000;
						$art_code = "F".$dept_code.$year_code.$kind."-".substr($serious,3);
						$new_num = substr($serious,3);
						$year_serious[$year_code] = $new_num;
						$fabric_num = encode_number($year_serious);
					
					} else {

						$art_code = "F".$dept_code.$year_code.$kind."-"."0001";
						$year_serious[$year_code] = "0001";
						$fabric_num = encode_number($year_serious);  // 改成cvs字串[function.php]
					}

			//  寫入新的 num 入資料庫
			$q_str = "UPDATE dept SET fabstock_num ='".$fabric_num."'  WHERE dept_code ='PM'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法更新資料庫內容.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $art_code;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


} // end class


?>