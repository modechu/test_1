<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->check($parm)		檢查 加入新 訂單記錄 是否正確
#	->mat_schedule_check($parm)		檢查 加入mat_schedule 記錄 是否正確
#	->pd_schedule_check($parm)		檢查 加入生產排程 schedule 記錄 是否正確
#	->check_add_ie($ie_time)		檢查 加入新IE工時 是否正確  需全為 正整數
#	->update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order')	同時更新兩個 field的值
#	->update_ie($parm)	
#	->add_material_etd($parm)		[寫入主副料之 ETD ]
#	->add_pd_schedule($parm)		[寫入 producting ETS 及 ETF ]
#	->add_cfm_pd_schedule($parm)	[寫入 producting ETS 及 ETF 及 fty_su ] 確認 工廠排程
#	->update_sorder_4_cfm_pd_schedule($parm)	[寫入 producting status 及 記錄排程日及人]
#	->mat_ship($pd_id, $shp_date)		更新 訂單 記錄 [寫入主料 出貨日期 ]
#	->macc_ship($pd_id, $shp_date)		更新 訂單 記錄 [寫入主要副料料 出貨日期 ]
#	->acc_ship($pd_id, $shp_date)		更新 訂單 記錄 [寫入其它副料 出貨日期 ]
#	->add_shipping($parm)		更新 訂單 出貨 記錄 [寫入 pdtion -> qty_shp 及 shp_date ]

#	->add($parm)				加入新 訂單記錄  傳回 $id
#	->search($mode=0, $dept='',$limit_entries=0)			搜尋 訂 單 資料
#	->ord_search($mode=0, $where_str='', $limit_entries=0)	搜尋 訂 單 資料	 
#			mode = 2 :		排產確認後的訂單搜尋 ( status >= 7 )
#	->pdt_search($parm, $limit_entries=500)	搜尋 訂單 生產資料	 
#	->apved_search($mode=0, $where_str='', $limit_entries=0)	搜尋 訂 單 資料	 
#			mode = 2 :		確認後的訂單搜尋 ( status >= 4 )
#	->uncfm_search($mode=0, $limit_entries=0)	搜尋 待確認之訂單 資料	 
#	->unapv_search($mode=0,$limit_entries=0)	搜尋 待 核可 之訂單 資料	 
#	->schedule_search($mode=0)	搜尋 訂 單 資料	 [ 核可後訂單 ]
#	->schedule_uncfm_search($fty_id=0)	搜尋 尚未確認 生產排程的訂單 資料	 
#	->mat_schedule_search($mode=0)	搜尋 訂單資料 [ 主料到料行程 ]	 


#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#	->mat_schedule_get($id=0, $wi_num=0)	抓出指定記錄內資料 RETURN $row[]
#	->get_ord_output($ord_num)	定指訂訂單號碼 取出垓單之記錄 RETURN $row[]  ??????????
#	->get_fields_4_del_pdt($ord_num)	指訂訂單號碼 取出要刪除某個生產記錄的訂單內容 RETURN $row[]
#	->schedule_get($id=0, $order_num=0)	抓出指定記錄內資料 RETURN $row[]
#	->get_pdtion($order_num, $factory)	抓出指定的 pdtion 記錄 RETURN $row[]
#	->revise_pdtion($parm, $id='', $ord_num='')	更新 pdtion的內容 [ order revise 時的更新 ]
#	->revise_apv_ord($parm, $ord_num='')	更新 已核可後的訂單 改 status=1 , etp_su, ftp_su, ets, etf 歸零

#	->add_edit($parm)			更新 製造令 記錄 [ 加入新 製令的step2]
#	->edit($parm,$mode=0)		更新 訂單 記錄   mode=0 : EDIT    mode=1 : REVISE
#	->send_cfm($parm)		 訂單送出 待確認  
#	->do_cfm($parm)		訂單 確認ok  送出待 核可  
#	->reject_cfm($parm)		訂單 REJECT 確認  
#	->do_apv($parm)		訂單 核可ok  ############>>>>>> 預定排產  
#	->reject_apv($parm)		訂單 REJECT 核可  
#	->pd_out_update($parm)		工廠完成數量之更新 pdtion 
#	->update_smpl_apv($parm)		 更新 樣本確認資料 --- by order_num  

#	->distri_month_su($T_su, $s_date, $f_date, $fty, $cat, $mode=0)  會更新 capacity 內的欄位
#		Detail description	: 將 數量 換成su 再分配到生產月份內 
#							: 寫入 capacity table 的 $field 內 [如沒找到 error ]
#							: 傳回 陣列 ( 200505=>su , 200506=>su, ......
#					$mode === 0 時 為正常的加入 <>   $mode = 1 時  為加入一個負質 即減去 

#	->creat_pdtion($parm)		加入 新的 pdtion 訂單記錄 [ 寫入工廠 及 etp_su 排產數]
#	->update_field($field, $val, $id)			更新 s_order資料記錄內 某個單一欄位
#	->update_pdtion_field($field, $val, $id)	更新 pdtion資料記錄內 某個單一欄位
#	->del($id,$mode=0)		刪除 [由ID]刪除  $mode=0: $id= 記錄之id; $mode<>0: $id=ORDER_num

#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#	->shift($argv, $parm)	 訂單移轉 更新 s_order , pdtion 內容

#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class ORDER {
		
	var $sql;
	var $msg ;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! 無法聯上資料庫.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		檢查 加入新 訂單記錄 是否正確
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目	
	
		if (!$parm['style'] ) {
			$this->msg->add("Error ! 請選擇 款式類別(style)。");
		}
		if (!$parm['unit'] ) {
			$this->msg->add("Error ! 請選擇 計量單位 。");
		}
		if (!is_numeric($parm['uprice'] )) {
			$this->msg->add("Error ! 請輸入正確 FOB 單價。");
		}
		if (!is_numeric($parm['qty'] )) {
			$this->msg->add("Error ! 請輸入正確數量。");
		}
		if (!is_numeric($parm['mat_u_cost'] ) && !$parm['mat_u_cost']=='') {
			$this->msg->add("Error ! 請輸入正確 主料單位成本。");
		}
		if (!is_numeric($parm['mat_useage'] ) && !$parm['mat_useage']=='') {
			$this->msg->add("Error ! 請輸入正確 主料單位用量。");
		}
		if (!is_numeric($parm['acc_u_cost'] ) && !$parm['acc_u_cost']=='') {
			$this->msg->add("Error ! 請輸入正確 副料單位成本。");
		}
		if (!is_numeric($parm['quota_fee'] ) && !$parm['quota_fee']=='') {
			$this->msg->add("Error ! 請輸入正確 配額單位成本。");
		}
		if (!is_numeric($parm['comm_fee'] ) && !$parm['comm_fee']=='') {
			$this->msg->add("Error ! 請輸入正確 佣金單位成本。");
		}
		if (!is_numeric($parm['cm'] ) && !$parm['cm']=='') {
			$this->msg->add("Error ! 請輸入正確 工繳單位成本。");
		}
		if (!is_numeric($parm['smpl_fee'] ) && !$parm['smpl_fee']=='') {
			$this->msg->add("Error ! 請輸入正確 樣品成本。");
		}

		if (!is_numeric($parm['emb'] ) && !$parm['emb']=='') {
			$this->msg->add("Error ! please check the embroidery cost !");
		}
		if (!is_numeric($parm['wash'] ) && !$parm['wash']=='') {
			$this->msg->add("Error ! please check the garment-wash cost !");
		}
		if (!is_numeric($parm['oth'] ) && !$parm['oth']=='') {
			$this->msg->add("Error ! please check the other-treatment cost !");
		}

		if (!$parm['factory'] ) {
			$this->msg->add("Error ! please choose the manufacture FACTORY。");
		}

		if (!checkdate($parm['etdmonth'],$parm['etdday'],$parm['etdyear'])){
			$this->msg->add("Error ! 請輸入正確 ETD日期。");
		}

		if (!checkdate($parm['etpmonth'],$parm['etpday'],$parm['etpyear'])){
			$this->msg->add("Error ! 請輸入正確 ETP日期。");
		}

		$etd = $parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday'];
		$etp = $parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday'];
		
		if ($etp > $etd) {
			$this->msg->add("Error ! you have the wrong date of ETP or ETD。");
		}

		if (count($this->msg->get(2))){
			return false;
		}
				
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_schedule_check($parm)		檢查 加入mat_schedule 記錄 是否正確
#						
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_schedule_check($parm) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目	
			// 可以不輸入日期 -----

		if (($parm['mat_etdmon'] =='') && ($parm['mat_etdday'] =='') && ($parm['mat_etdyear'] =='')){
			$parm['mat_etdmon'] ='00';
			$parm['mat_etdday'] ='00';
			$parm['mat_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['mat_etdmon'],$parm['mat_etdday'],$parm['mat_etdyear'])){
				$this->msg->add("Error ! 請輸入正確 主料 之 ETD 。");
			}
		}

		if (($parm['macc_etdmon'] =='') && ($parm['macc_etdday'] =='') && ($parm['macc_etdyear'] =='')){
			$parm['macc_etdmon'] ='00';
			$parm['macc_etdday'] ='00';
			$parm['macc_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['macc_etdmon'],$parm['macc_etdday'],$parm['macc_etdyear'])){
				$this->msg->add("Error ! 請輸入正確 主要副料之 ETD 。");
			}
		}

		if (($parm['acc_etdmon'] =='') && ($parm['acc_etdday'] =='') && ($parm['acc_etdyear'] =='')){
			$parm['acc_etdmon'] ='00';
			$parm['acc_etdday'] ='00';
			$parm['acc_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['acc_etdmon'],$parm['acc_etdday'],$parm['acc_etdyear'])){
				$this->msg->add("Error ! 請輸入正確 其它副料之 ETD 。");
			}
		}
		if (count($this->msg->get(2))){
			return false;
		}
		
		return true;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pd_schedule_check($parm)		檢查 加入生產排程 schedule 記錄 是否正確
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pd_schedule_check($parm) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目	
			// 可以不輸入日期 -----

		if (($parm['ets_mon'] =='') && ($parm['ets_day'] =='') && ($parm['ets_year'] =='') || (!checkdate($parm['ets_mon'],$parm['ets_day'],$parm['ets_year']))){
				$this->msg->add("Error ! illegal date of ETS 。");
		}

		if (($parm['etf_mon'] =='') && ($parm['etf_day'] =='') && ($parm['etf_year'] =='') || (!checkdate($parm['etf_mon'],$parm['etf_day'],$parm['etf_year']))){
				$this->msg->add("Error ! illegal date of ETF 。");
		}

		$ets = $parm['ets_year']."-".$parm['ets_mon']."-".$parm['ets_day'];
		$etf = $parm['etf_year']."-".$parm['etf_mon']."-".$parm['etf_day'];
		
		if ($ets > $etf) {
			$this->msg->add("Error ! you have the wrong date between ETS or ETF。");
		}

		if (count($this->msg->get(2))){
			return false;
		}
		
		return true;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_add_ie($ie_time)	檢查 加入新IE工時 是否正確  需全為 正整數
#				# mode =0:一般add的check,  mode=1: edit時的check
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_add_ie($ie_time) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目

//		$T = $ie_time;
		if (!(is_numeric($ie_time)&&(intval($ie_time)==floatval($ie_time)))){  // 必需為整數

			$this->msg->add("Error ! please input the correct figure [numeric only] 。");

			return false;
		}

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order')	
#
#		同時更新兩個 field的值 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order') {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
									"', ".$field2." ='".$value2.
								"' WHERE id=".	$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_ie($parm)	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_ie($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE s_order SET ie1 ='"	.$parm['ie1'].
							"', ie_time1 ='"	.$parm['ie_time1'].
							"', su ='"			.$parm['su'].
							"', status ='"		.$parm['status'].
								"' WHERE id="	.$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return $parm['id'];
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_material_etd($parm)		 [寫入主副料之 ETD ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_material_etd($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET ";
		if ($parm['mat_etd']) { $q_str = $q_str."mat_etd='".$parm['mat_etd']."'"; }
		if ($parm['macc_etd']) { $q_str = $q_str.", m_acc_etd='".$parm['macc_etd']."'"; }
		if ($parm['acc_etd']) { $q_str = $q_str.", acc_etd='".$parm['acc_etd']."'"; }

		$q_str = $q_str." WHERE id=".$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_pd_schedule($parm)		[寫入 producting ETS 及 ETF ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_pd_schedule($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET ";

		if ($parm['ets']) { $q_str = $q_str."ets='".$parm['ets']."'"; }
		if ($parm['etf']) { $q_str = $q_str.", etf='".$parm['etf']."'"; }

		$q_str = $q_str." WHERE id=".$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_cfm_pd_schedule($parm)	[寫入 producting ETS 及 ETF 及 fty_su ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cfm_pd_schedule($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET ets='".		$parm['ets'].
								"', etf='".		$parm['etf'].
								"', fty_su='".	$parm['fty_su'].
								"' WHERE id=".	$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_sorder_4_cfm_pd_schedule($parm)	[寫入 producting status 及 記錄排程日及人]
#									確認 工廠排程 後之主訂單更新
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_sorder_4_cfm_pd_schedule($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE s_order SET status='".		$parm['status'].
								"', schd_er='".		$parm['schd_er'].
								"', schd_date='".	$parm['schd_date'].
								"' WHERE id=".		$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_ship($pd_id, $shp_date)		更新 訂單 記錄 [寫入主料 出貨日期 ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_ship($pd_id, $shp_date) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET mat_shp='".$shp_date."' WHERE id=".$pd_id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $pd_id;

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->macc_ship($pd_id, $shp_date)		更新 訂單 記錄 [寫入主要副料料 出貨日期 ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function macc_ship($pd_id, $shp_date) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET m_acc_shp='".$shp_date."' WHERE id=".$pd_id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $pd_id;

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->acc_ship($pd_id, $shp_date)		更新 訂單 記錄 [寫入其它副料 出貨日期 ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function acc_ship($pd_id, $shp_date) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET acc_shp='".$shp_date."' WHERE id=".$pd_id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $pd_id;

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_shipping($parm)	更新 訂單 出貨 記錄 [寫入 pdtion -> qty_shp 及 shp_date ]
#									確認 工廠排程
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_shipping($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET qty_shp=qty_shp + ".$parm['qty_shp'].
								", shp_date='".		$parm['shp_date'].
								"' WHERE id=".		$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO s_order (dept,cust,order_num,ref,factory,style,qty,unit,style_num,patt_num,smpl_ord,uprice,quota,mat_u_cost,mat_useage, acc_u_cost,quota_fee,comm_fee,cm,smpl_fee,etd,etp,gmr,creator,emb,wash,oth,oth_treat,opendate) VALUES('".
							$parm['dept']."','".
							$parm['cust']."','".
							$parm['order_num']."','".
							$parm['ref']."','".
							$parm['factory']."','".
							$parm['style']."','".
							$parm['qty']."','".
							$parm['unit']."','".
							$parm['style_num']."','".
							$parm['patt_num']."','".
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
							$parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday']."','".
							$parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday']."','".
							$parm['gmr']."','".
							$parm['creator']."','".
							$parm['emb']."','".
							$parm['wash']."','".
							$parm['oth']."','".
							$parm['oth_treat']."','".
							$parm['open_date']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("append order#: [".$parm['order_num']."]。") ;
	
		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic1'] <> "none") {
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  

						# 上傳圖相處理
			//上傳圖 600X600
			$upFile->setSaveTo($style_dir,$parm['order_num'].".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);

				if ($up_result){
					$this->msg->add("成功上傳主圖");
				} else {
					$this->msg->add("上傳主圖 失敗");
				}
		}
	
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

		$q_header = "SELECT * FROM s_order ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
//---2006/1115
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
//---20061115--------
	}


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	if($user_dept == "J0"){
		$srh->add_where_condition("dept LIKE '%J%'", "PHP_dept","J","");
	}elseif($user_dept == "K0"){
		$srh->add_where_condition("dept LIKE '%K%'", "PHP_dept","K","");
	}

	
	if ($dept){  //  當要分部門時
		$srh->add_where_condition("dept = '$dept'", "",$dept,"部門別=[ $dept ]. ");
	}

   if ($mode==1){

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
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
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
if($limit_entries == 0){
#####2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14新頁碼需要的oup_put	end
}

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_search($mode=0, $where_str='', $limit_entries=0)	搜尋 訂 單 資料	 
#			mode = 2 :  排產確認後的訂單搜尋 ( status >= 7 )
#			mode = 3 :  已有產出後的訂單搜尋 ( status > 7 )
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ord_search($mode=0, $where_str='', $limit_entries=0) {

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


	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
	}
if ($mode==1 || $mode==2 || $mode==3){
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
if ($mode==1){   // 當要搜尋的 訂單是 訂單確認後的時
		$srh->add_where_condition("s_order.status >= 4", "","","");   
}		
if ($mode==2){   // 當要搜尋的 訂單是 排產確認後的時
		$srh->add_where_condition("s_order.status >= 7", "","","");   
}		
if ($mode==3){   // 當要搜尋的 訂單是 排產確認後的時
		$srh->add_where_condition("s_order.status > 7", "","","");   
}		
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加

		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
//		$result= $srh->send_query2();
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
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pdt_search($parm, $limit_entries=500)	搜尋 訂單 生產資料	 
#			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pdt_search($parm, $limit_entries=500) {
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

	 $where_str = "WHERE ";
		if($str = $parm['ord_num']){
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str,"order number contain: [ $str ] "); 
		}
		if ($str = $parm['cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"customer = [ $str ]. "); 
		}
		if(!$parm['ref']){
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str,"ref contain: [ $str ] "); 
		}
		if ($str = $parm['fty'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str,"factory = [ $str ]. "); 
		}

	  $fields = "s_order.order_num,s_order.cust,s_order.style,s_order.qty,s_order.unit,pdtion.qty_done,pdtion.qty_shp,s_order.su,s_order.ie_time1,s_order.ie1,s_order.etd,pdtion.etf,s_order.etp,pdtion.ets,pdtion.start,pdtion.finish,pdtion.factory,s_order.opendate,s_order.apv_date,s_order.smpl_apv,s_order.ptn_upload,pdtion.mat_shp,pdtion.m_acc_shp,s_order.dept,s_order.creator,s_order.style_num,s_order.smpl_ord,s_order.patt_num,s_order.quota,s_order.revise,s_order.status";

		$q_header = "SELECT ".$fields." FROM s_order, pdtion ";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
//		$srh->row_per_page = 20;


	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
	}


		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加

		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
//		$result= $srh->send_query2();
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
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->apved_search($mode=0, $where_str='', $limit_entries=0)	搜尋 訂 單 資料	 
#			mode = 2 :  確認後的訂單搜尋 ( status >= 4 )
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function apved_search($mode=0, $where_str='', $limit_entries=0) {

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

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
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
		$srh->add_where_condition("s_order.status >= 4", "","","");   
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->uncfm_search($mode=0,$limit_entries=0)	搜尋 待確認之訂單 資料	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function uncfm_search($mode=0,$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

	//2006/05/12 adding 判斷是否為 J0 或 K0
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	if($user_dept == "J0"){
//	echo "<br>user dept....===>".$user_dept;
		$q_header = "SELECT * FROM s_order WHERE status=2 AND dept LIKE '%J%'  ";
	}elseif($user_dept == "K0"){
//	echo "<br>user dept....===>".$user_dept;
		$q_header = "SELECT * FROM s_order WHERE status=2 AND dept LIKE '%K%' ";
	}else{
		$q_header = "SELECT * FROM s_order WHERE status=2 ";
	}
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

		if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
				$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			if ($argv['PHP_sr_startno']) {
				$srh->add_limit_condition($argv['PHP_sr_startno']);
			} 
		}
		
		$result= $srh->send_query2($limit_entries);
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

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->unapv_search($mode=0,$limit_entries=0)	搜尋 待 核可 之訂單 資料	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function unapv_search($mode=0,$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM s_order WHERE status=3 ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;


		if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
				$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			if ($argv['PHP_sr_startno']) {
				$srh->add_limit_condition($argv['PHP_sr_startno']);
			} 
		}

		$result= $srh->send_query2($limit_entries);
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

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_search($mode=0)	搜尋 訂 單 資料	 [ 核可後訂單 ]
#	NOTE: 必需設定進入的 user 身份 [ 不同工廠 ===== ==============
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function schedule_search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
			 // 關聯式察尋 ==tabel s_order, pdtion
		$q_header = "SELECT s_order.id, s_order.order_num, s_order.cust, s_order.style_num, s_order.etd, s_order.etp, s_order.qty, s_order.unit, s_order.status, s_order.smpl_apv, pdtion.ets, s_order.factory, s_order.ref, pdtion.mat_etd, pdtion.m_acc_etd FROM s_order, pdtion ";
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
if ($mode==1){
		if (isset($argv['PHP_dept_code']) && $str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str,"部門別 = [ $str ]. ");
		}

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
		$srh->add_where_condition("status >= 4", "PHP_status",$str,"");   // ******* 必然要加 列表皆為 核可後
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加
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
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_uncfm_search($fty_id=0)	搜尋 尚未確認 生產排程的訂單 資料	 
#	NOTE: 必需設定進入的 user 身份 [ 不同工廠 ===== ==============
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function schedule_uncfm_search($fty_id=0) {   // 找  status = 6 [schedule 待確認]

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
			 // 關聯式察尋 ==tabel s_order, pdtion
		$q_header = "SELECT s_order.id, s_order.order_num, s_order.cust,s_order.style_num, s_order.etd, s_order.etp, s_order.qty, s_order.unit, pdtion.ets, s_order.factory, pdtion.mat_etd, pdtion.m_acc_etd FROM s_order, pdtion ";
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

	if ($fty_id){
			$srh->add_where_condition("s_order.factory = '$fty_id'"); 
	}

		$srh->add_where_condition("status = 6 ");   // ******* 必然要加 列表皆為 待核可shcedule
		$srh->add_where_condition("s_order.order_num = pdtion.order_num ");   // 關聯式察尋 必然要加
		
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
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_schedule_search($mode=0)	搜尋 訂單資料 [ 主料到料行程 ]	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_schedule_search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
//		$q_header = "SELECT * FROM s_order ";
			 // 關聯式察尋 ==tabel s_order, pdtion
		$q_header = "SELECT s_order.id,s_order.order_num,s_order.cust,s_order.etd,s_order.etp,s_order.qty,s_order.unit,s_order.status, s_order.style,s_order.factory,pdtion.mat_etd,pdtion.m_acc_etd,pdtion.mat_shp FROM s_order, pdtion ";
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
if ($mode==1){
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
		$srh->add_where_condition("status >= 4", "PHP_status",$str,"");   // ******* 必然要加 可排產的部份
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $order_num=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM s_order WHERE id='$id' ";
		} elseif ($order_num) {
			$q_str = "SELECT * FROM s_order WHERE order_num='$order_num' ";
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_schedule_get($id=0, $wi_num=0)	抓出指定記錄內資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_schedule_get($id=0, $order_num=0) {

		$sql = $this->sql;


		// 關聯式資料庫查尋 ~~~~~~
	if ($id)	{
		$q_str = "SELECT * FROM s_order,pdtion WHERE s_order.id='$id' AND s_order.order_num=pdtion.order_num ";
	} elseif ($order_num) {
		$q_str = "SELECT * FROM s_order,pdtion WHERE order_num='$order_num' AND s_order.order_num=pdtion.order_num ";
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_output($ord_num)	定指訂訂單號碼 取出垓單之記錄 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_output($ord_num) {

		$sql = $this->sql;
	
		$q_str = "SELECT s_order.opendate,s_order.cfm_date,s_order.creator,s_order.apv_date,s_order.schd_date,s_order.cust, s_order.qty,s_order.smpl_apv,s_order.factory,s_order.su,s_order.etd,s_order.etp,s_order.status,pdtion.ets,pdtion.etf,pdtion.mat_shp,pdtion.acc_shp,pdtion.m_acc_shp,pdtion.qty_done,pdtion.start,pdtion.finish,pdtion.shp_date FROM s_order,pdtion WHERE s_order.order_num='$ord_num' AND s_order.order_num=pdtion.order_num ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到記錄!");
			return false;    
		}
		return $row;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields_4_del_pdt($ord_num)	指訂訂單號碼 取出要刪除某個生產記錄的訂單內容 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_4_del_pdt($ord_num) {

		$sql = $this->sql;
	
		$q_str = "SELECT s_order.id, s_order.status, pdtion.id AS p_id, pdtion.out_su FROM s_order,pdtion WHERE s_order.order_num='$ord_num' AND s_order.order_num=pdtion.order_num ";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access databse, please contact the system Administrator !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到記錄!");
			return false;    
		}
		return $row;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_get($id=0, $order_num=0)	抓出指定記錄內資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function schedule_get($id=0, $order_num=0) {

		$sql = $this->sql;

		// 關聯式資料庫查尋 ~~~~~~
	if ($id)	{
		$q_str = "SELECT * FROM s_order,pdtion WHERE s_order.id='$id' AND s_order.order_num=pdtion.order_num ";
	} elseif ($order_num) {
		$q_str = "SELECT * FROM s_order,pdtion WHERE s_order.order_num='$order_num' AND s_order.order_num=pdtion.order_num ";
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pdtion($order_num, $factory)	抓出指定的 pdtion 記錄 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pdtion($order_num, $factory) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM pdtion WHERE order_num='$order_num' AND factory='$factory' ";

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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_pdtion($parm, $id='', $ord_num='')	更新 pdtion的內容 [ order revise 時的更新 ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise_pdtion($parm, $id='', $ord_num='') {

		$sql = $this->sql;

		#####   更新資料庫內容
/*		//20060411更改為 工廠不能 revise  且修正 忘了將 ftp_su清除...工廠已排產後的revise
		if ($id){
			$q_str = "UPDATE pdtion SET factory='"	.$parm['factory'].
								"', etp_su='"	.$parm['etp_su'].
								"', ets='NULL', etf ='NULL'".
								"  WHERE id='"		.$id."'";
		}elseif($ord_num){
			$q_str = "UPDATE pdtion SET factory='"	.$parm['factory'].
								"', etp_su='"	.$parm['etp_su'].
								"', ets='NULL', etf ='NULL'".
								"  WHERE order_num='"		.$ord_num."'";
		}
*/		//20060411 再更改---- 不再改 ETS,ETF 因為revise時已清過了－－－－

		if ($id){
			$q_str = "UPDATE pdtion SET etp_su='"	.$parm['etp_su'].
								"'  WHERE id='"		.$id."'";
		}elseif($ord_num){
			$q_str = "UPDATE pdtion SET etp_su='"	.$parm['etp_su'].
								"'  WHERE order_num='"		.$ord_num."'";
		}

//echo "q_str====>".$q_str;
//exit;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($id) {	$pdt = $id; } elseif($ord_num) { $pdt = $ord_num;}
		
		return $pdt;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_apv_ord($parm, $ord_num='')	更新 已核可後的訂單 
#			改 status=1 , etp_su, ftp_su, ets, etf 歸零
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise_apv_ord($parm, $ord_num='') {

		$sql = $this->sql;

		#####   更新資料庫內容

		$q_str = "UPDATE s_order,pdtion SET pdtion.etp_su='".$parm['etp_su']."',pdtion.fty_su='".$parm['fty_su']."',s_order.opendate='".$parm['today']."',s_order.creator='".$parm['reviser']."', pdtion.ets='NULL', pdtion.etf ='NULL', s_order.status='1'   WHERE s_order.order_num='".$ord_num."' AND pdtion.order_num ='".$ord_num."'";

//echo "q_str====>".$q_str;
//exit;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return $ord_num;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm, $mode=0) {

		$sql = $this->sql;
		
		if ($mode == 0){
		#####   更新資料庫內容
			$q_str = "UPDATE s_order SET ".
							" ref='"		.$parm['ref'].
							"',	factory='"		.$parm['factory'].
							"',	style='"		.$parm['style'].
							"',	qty='"			.$parm['qty'].
							"',	su='"			.$parm['su'].
//							"', su_ratio='"		.$parm['su_ratio'].
							"', unit='"			.$parm['unit'].
//							"',	style_num='"	.$parm['style_num'].
//							"',	patt_num='"		.$parm['patt_num'].
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
							"',	emb='"			.$parm['emb'].
							"',	wash='"			.$parm['wash'].
							"',	oth='"			.$parm['oth'].
							"',	oth_treat='"	.$parm['oth_treat'].

							"',	etd='"			.$parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday'].
							"', etp='"			.$parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday'].
							"', gmr='"			.$parm['gmr'].
							"', last_updator='"	.$parm['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		} elseif($mode ==1){      // --- order revise -----
			$q_str = "UPDATE s_order SET ".
							" ref='"		.$parm['ref'].
//							"',	factory='"		.$parm['factory'].
							"',	style='"		.$parm['style'].
							"',	qty='"			.$parm['qty'].
							"',	su='"			.$parm['su'].
//							"', su_ratio='"		.$parm['su_ratio'].
							"', unit='"			.$parm['unit'].
//							"',	style_num='"	.$parm['style_num'].
//							"',	patt_num='"		.$parm['patt_num'].
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
							"',	emb='"			.$parm['emb'].
							"',	wash='"			.$parm['wash'].
							"',	oth='"			.$parm['oth'].
							"',	oth_treat='"	.$parm['oth_treat'].

							"',	etd='"			.$parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday'].
							"', etp='"			.$parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday'].
							"', gmr='"			.$parm['gmr'].
							"', revise="		.$parm['revise'].
							", status="			.$parm['status'].
							", cfmer='"			.''.
							"', cfm_date='"		.'0000-00-00'.
							"', apver='"		.''.
							"', apv_date='"		.'0000-00-00'.
							"', last_updator='"	.$parm['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$parm['id']."'";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		$pic_id = $parm['order_num'];

	//  圖的處理 ===============
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  
//			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";

		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台

						# 上傳圖相處理
				//2004/05/03 先檢查是否存在 如存在就先砍檔
			if(file_exists($style_dir.$parm['order_num'].".jpg")){
				unlink($style_dir.$parm['order_num'].".jpg") or die("無法刪除舊圖檔:".$pic_id.".jpg");  // 刪除舊檔
			}
				//上傳大圖 600X600
				$upFile->setSaveTo($style_dir,$parm['order_num'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);
		}

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_cfm($parm)		 訂單送出 待確認  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function send_cfm($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE s_order SET status=		2".
							", last_updator='"	.$parm['last_updator'].
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->do_cfm($parm)		訂單 確認ok  送出待 核可  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function do_cfm($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE s_order SET status=		3".
							", cfmer='"			.$parm['cfmer'].
							"', cfm_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reject_cfm($parm)		訂單 REJECT 確認  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function reject_cfm($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE s_order SET status=		5".
							", cfmer='"			.$parm['cfmer'].
							"', cfm_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->do_apv($parm)		訂單 核可ok  ############>>>>>> 預定排產  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function do_apv($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE s_order SET status=		4".
							", apver='"			.$parm['apver'].
							"', apv_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reject_apv($parm)		訂單 REJECT 核可  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function reject_apv($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE s_order SET status=		5".
							", apver='"			.$parm['apver'].
							"', apv_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pd_out_update($parm)		工廠完成數量之更新 pdtion 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pd_out_update($parm) {

		$sql = $this->sql;
		
		#####   更新資料庫內容
//		$q_str = "UPDATE pdtion SET qty_done="		.$parm['qty_done'].
		$q_str = "UPDATE pdtion SET qty_done= qty_done+".$parm['qty'].
								", qty_update='"	.$parm['k_date'].
								"', out_su='"		.$parm['out_su'].
								"'  WHERE id='"		.$parm['pd_id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update pdtion table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_smpl_apv($parm)		 更新 樣本確認資料 --- by order_num  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_smpl_apv($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		if($parm['mode'] ==1){			//加入mode 來分別兩次的smpl apv ----
			$q_str = "UPDATE s_order SET smpl_apv='"	.$parm['smpl_apv'].
								"', smpl_apv_type='"	.$parm['smpl_apv_type'].
								"'  WHERE order_num ='"	.$parm['order_num']."'";
		}elseif($parm['mode'] ==2){
			$q_str = "UPDATE s_order SET smpl_apv2='"	.$parm['smpl_apv'].
								"', smpl_apv_type2='"	.$parm['smpl_apv_type'].
								"'  WHERE order_num ='"	.$parm['order_num']."'";
		}elseif($parm['mode'] ==3){
			$q_str = "UPDATE s_order SET smpl_apv3='"	.$parm['smpl_apv'].
								"', smpl_apv_type3='"	.$parm['smpl_apv_type'].
								"'  WHERE order_num ='"	.$parm['order_num']."'";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = $parm['order_num'];
		
		return $ord_num;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ************************ 會更新 capacity 內的欄位 ******************
#	->distri_month_su($T_su, $s_date, $f_date, $fty, $cat, $mode=0)
#		Detail description	: 將 數量 換成su 再分配到生產月份內 
#							: 寫入 capacity table 的 $field 內 [如沒找到 error ]
#							: 傳回 陣列 ( 200505=>su , 200506=>su, ......
#		$fty, $cat 為了要在 capacity table更新 [ $cat 指的是 capacity內的 c_type 欄位名 ]
#
#			$mode === 0 時 為正常的加入 <>   $mode = 1 時  為加入一個負質 即減去 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function distri_month_su($T_su, $s_date, $f_date, $fty, $cat, $mode=0) {


		if($mode==1) { $factor = -1; } else { $factor = 1; }  // 加入 正負值 視需要-- 減或加 capaci
 		$div = array();

		$distribute ='';   // 做為csv 變數
   
		list($s_year,$s_mon,$s_day) = split("-",$s_date);  // 開始日
		list($f_year,$f_mon,$f_day) = split("-",$f_date);  // 結束日
		$days =	countDays($s_date,$f_date);
//		$T_su = $su;		// 總 su 數
		$day_su = $T_su/$days;		// 每日產出 --(偏小)

		// 計算總共有幾個月份?
		$y = $f_year - $s_year;
		$m = 12*$y + (12-$s_mon+1) - (12-$f_mon);

//echo "<br>total qty = ".$qty." are equal to  ".$T_su." s.u.<br>";
//echo "<br>from ".$s_date." to ".$f_date." totally distribute for ".$m." 個月份; 計 ".$days."天 <br>";

			$su_year = $s_year;	// 年份的計數器:: 開始設訂為起頭年
			$su_mon = $s_mon;	// 月份的計數器:: 開始設定為起頭月

			$divered_su =0;	// 已經統計的su數 做為最後月之減項

		for ($i=0; $i<$m; $i++){
			if($su_mon >12){     // 計數器預到年底時
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

		# #####============ 加入 capacity ->    #########################
	
			$su_m = substr($mon,4);
				
				$su = $su * $factor; 	// 加入正負值 2005/11/21
				
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->creat_pdtion($parm)		加入 新的 pdtion 訂單記錄 [ 寫入工廠 及 etp_su 排產數]
#								傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($field, $val, $id)		更新 s_order資料記錄內 某個單一欄位
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_pdtion_field($field, $val, $id)		更新 pdtion資料記錄內 某個單一欄位
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		刪除   [由ID]刪除
#							$mode=0: $id= 記錄之id; $mode<>0: $id=ORDER_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->shift($argv, $parm)	 訂單移轉 更新 s_order , pdtion 內容
#			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function shift($argv, $parm) {

		$sql = $this->sql;
		
		#####   更新 s_order 資料庫內容

			$q_str = "UPDATE s_order SET ".
							" schd_er ='"		.$argv['schd_er'].
							"',	schd_date='"	.$argv['schd_date'].
							"',	factory='"		.$argv['factory'].
							"',	status=	"		.$argv['status'].
							", last_updator='"	.$argv['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$argv['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		#####   更新 pdtion 資料庫內容

			$q_str2 = "UPDATE pdtion SET ".
							" ets ='"		.$parm['ets'].
							"',	etf='"		.$parm['etf'].
							"',	factory='"	.$parm['factory'].
							"',	fty_su=	'"	.$parm['fty_su'].
							"'  WHERE id='"	.$parm['id']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}



		return $argv['id'];
	} // end func




} // end class


?>