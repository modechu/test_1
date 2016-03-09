<?php 

#++++++++++++++++++++++ ORDER_LOG  class ##### 開發布料  +++++++++++++++++++++++++++++++++++
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

class ORDER_LOG {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Database can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 sales_log 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm,$item='') {
					
		$sql = $this->sql;

		$parm['status'] = 0;
		if($item == 'user_log')$parm['status'] = 1;
	
		if ($item!='revise')	$parm['des'] = str_replace("'","\'",$parm['des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['des']);
		}
//		$parm['des'] = str_replace("許\","許\\\",$parm['des']);
//		$parm['des'] = str_replace("功\","功\\\",$parm['des']);
//		$parm['des'] = str_replace("蓋\","蓋\\\",$parm['des']);		
//		$parm['des'] = str_replace("擺\","擺\\\",$parm['des']);
					# 加入資料庫
		$q_str = "INSERT INTO order_log (order_num,
									user,
									status,
									k_time,
									des) VALUES('".
									$parm['order_num']."','".
									$parm['user']."','".
									$parm['status']."',									
									NOW(),'".
									$parm['des']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't upload sales_log record.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id


		return $pdt_id;

	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 sales_log 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_multi_ord($parm) {
					
		$sql = $this->sql;



		$parm['des'] = str_replace("'","\'",$parm['des']);
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['des']);
		}
					# 加入資料庫
		$q_str = "INSERT INTO ord_multi_log (ord_num,
									item,	
									user,
									k_date,
									des) VALUES('".
									$parm['order_num']."','".
									$parm['item']."','".
									$parm['user']."','".
									date('Y-m-d')."','".
									$parm['des']."')";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't upload sales_log record.");
			$this->msg->merge($sql->msg);
			return false;    
		}




		$pdt_id = $sql->insert_id();  //取出 新的 id


		return $pdt_id;

	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search50($mode=0,$where_str="")	搜尋 布料 資料	
#		僅抓取最近的五十筆資料  排序依時間
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search50($order_num) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM order_log WHERE order_num='".$order_num."' ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("status DESC, id DESC ");
		$srh->row_per_page = 50;


		$result= $srh->send_query2(50);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['records'] = '';
			} else {
				$op['records'] = 1;
			}

		for ($i=0; $i<sizeof($result); $i++)
		{
			$tmp_user=$GLOBALS['user']->get(0,$result[$i]['user']);
			if ($tmp_user['name'])$result[$i]['user'] = $tmp_user['name'];			
		}

		$op['order_log'] = $result;  // 資料錄 拋入 $op


		
		return $op;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search50($mode=0,$where_str="")	搜尋 布料 資料	
#		僅抓取最近的五十筆資料  排序依時間
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_multi($order_num,$item) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM ord_multi_log WHERE ord_num='".$order_num."' AND item='".$item."'";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("id DESC ");
		$srh->row_per_page = 50;


		$result= $srh->send_query2(50);   // 2005/05/16 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['records'] = '';
			} else {
				$op['records'] = 1;
			}

		for ($i=0; $i<sizeof($result); $i++)
		{
			$tmp_user=$GLOBALS['user']->get(0,$result[$i]['user']);
			if ($tmp_user['name'])$result[$i]['user'] = $tmp_user['name'];			
		}

	


		
		return $result;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		刪除   [由ID]刪除
#							$mode=0: $id= 記錄之id; $mode<>0: $id=ORDER_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! please specify order number.");		    
			return false;
		}
		$q_str = "DELETE FROM order_log WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE order_log SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func




} // end class


?>