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
		$this->msg->add("Error ! Data base can't connect.");
		return false;
	}
	$this->sql = $sql;
	return true;
} // end func



function get_combine($combine){

	$sql = $this->sql;

	$q_str = "SELECT `order_num`,`qty` FROM `s_order` WHERE `combine` = '".$combine."' AND `combine` != '' ;";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot access database, pls try later !");
		$this->msg->merge($sql->msg);
		return false;    
	}

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot access database, pls try later !");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    $qty = 0;
    $i = 0;
    $combine_str = '';
    while ($row = $sql->fetch($q_result)) {
        $combine_str .= $row['order_num'].' ( '.number_format($row['qty'],0,'.',',').'pc ) <br>';
        $qty += $row['qty'];
        $i++;
    }
    if( $i > 0 )$combine_str .= '<br>Total : '.number_format($qty,0,'.',',').'pc';
    return $combine_str;
}


function get_order_number($yaer){

	$sql = $this->sql;

	$q_str = "SELECT * FROM `s_order` WHERE `opendate` >= '".$yaer."-01-01' ORDER BY `id` DESC;";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot access database, pls try later !");
		$this->msg->merge($sql->msg);
		return false;    
	}

	if (!$row = $sql->fetch_row($q_result)) {
		return '0001';
	} else {
		return str_pad(substr($row[1],-4)+1,4,'0',STR_PAD_LEFT);
	}
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		檢查 加入新 訂單記錄 是否正確
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {
// print_r($parm);
		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目	
		$TODAY=date('Y-m-d');
		$check_date=increceDaysInDate($TODAY,10);
		if (!$parm['smpl_ord'] ) {
			$this->msg->add("Error ! Please choice sample order。");
		}
		if (!$parm['unit'] ) {
			$this->msg->add("Error ! Please choice unit 。");
		}
		if (!is_numeric($parm['uprice'] )  || $parm['uprice']==0) {
			$this->msg->add("Error ! Please input currect FOB。");
		}
		if (isset($parm['qty']) && (!is_numeric($parm['qty'] ) || $parm['qty']==0)) {
			$this->msg->add("Error ! Please input currect qty");
		}
		if (!is_numeric($parm['mat_u_cost'] ) && !$parm['mat_u_cost']=='') {
			$this->msg->add("Error ! Please input currect mat unit cost。");
		}
		if (!is_numeric($parm['mat_useage'] ) && !$parm['mat_useage']=='') {
			$this->msg->add("Error ! Please input currect mat useage。");
		}
		if (!is_numeric($parm['acc_u_cost'] ) && !$parm['acc_u_cost']=='') {
			$this->msg->add("Error ! Please input acc unit cost。");
		}
		if (!is_numeric($parm['quota_fee'] ) && !$parm['quota_fee']=='') {
			$this->msg->add("Error ! Please input currect qouta fee。");
		}
		if (!is_numeric($parm['comm_fee'] ) && !$parm['comm_fee']=='') {
			$this->msg->add("Error ! Please input currect commission fee。");
		}
		if (!is_numeric($parm['cm'] ) && !$parm['cm']=='') {
			$this->msg->add("Error ! Please input currect cm fee。");
		}
		if (!is_numeric($parm['smpl_fee'] ) && !$parm['smpl_fee']=='') {
			$this->msg->add("Error ! Please input currect sample fee。");
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
		if (!$parm['etp']) {
			$this->msg->add("Error ! please choose the date  of ETP。");
		}
		if (!$parm['etd']) {
			$this->msg->add("Error ! please choose the date of  ETD。");
		}		
		if ($parm['etp'] < $GLOBALS['ord_append_ETP_limit']) {
			$this->msg->add("Sorry ! please update your unreasonable ETP ( before today at least)。");
		}
		if ($parm['etd'] < $GLOBALS['ord_append_ETD_limit']) {
			$this->msg->add("Sorry ! please update your unreasonable ETD ( 30 days before today at least)。");
		}
		if ($parm['etp'] && $parm['etd'])
		{
			$tmp_etp=explode('-',$parm['etp']);
			$tmp_etd=explode('-',$parm['etd']);
			$etp=$tmp_etp[0].$tmp_etp[1].$tmp_etp[2];
			$etd=$tmp_etd[0].$tmp_etd[1].$tmp_etd[2];
			if ($etp > $etd) {
				$this->msg->add("Error ! you have the wrong date of ETP or ETD。");
			}
		}
		if (!$parm['line_sex'] ) {
			$this->msg->add("Sorry ! 請選擇男女裝線");
		}
		if (!$parm['lots_unit'] ) {
			$this->msg->add("Sorry ! Please select lots unit");
		}		
		
		if ($parm['etd'] < $_SESSION['FTY_ETD_LIMIT'] ) {
			$this->msg->add("Sorry ! ETD must > ".$_SESSION['FTY_ETD_LIMIT']);
		}	
		
		foreach($parm['ps_etd'] as $key => $value)
		{
			$tmp_etp=explode('-',$parm['ps_etp'][$key]);
			$tmp_etd=explode('-',$parm['ps_etd'][$key]);
			$etp=$tmp_etp[0].$tmp_etp[1].$tmp_etp[2];
			$etd=$tmp_etd[0].$tmp_etd[1].$tmp_etd[2];
			if ($etp > $etd) 
				$this->msg->add("Error ! you have the wrong date of ETP or ETD。");						
			if ($parm['ps_etp'][$key] < $GLOBALS['ord_append_ETP_limit']) 
				$this->msg->add("Sorry ! please update your unreasonable ETP ( before today at least)。");			
			if ($parm['ps_etd'][$key] < $GLOBALS['ord_append_ETD_limit']) 
				$this->msg->add("Sorry ! please update your unreasonable ETD ( 30 days before today at least)。");			
			if ($parm['ps_etd'][$key] < $_SESSION['FTY_ETD_LIMIT'] ) 
				$this->msg->add("Sorry ! ETD must > ".$_SESSION['FTY_ETD_LIMIT']);			
		}
		
		if(isset($parm['syear']))$parm['s_year'] = $parm['syear'];
		if (!$parm['season'] || !$parm['s_year'] ) {
			$this->msg->add("Error ! please select season first");
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
				$this->msg->add("Error ! Please input mat's ETD 。");
			}
		}

		if (($parm['macc_etdmon'] =='') && ($parm['macc_etdday'] =='') && ($parm['macc_etdyear'] =='')){
			$parm['macc_etdmon'] ='00';
			$parm['macc_etdday'] ='00';
			$parm['macc_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['macc_etdmon'],$parm['macc_etdday'],$parm['macc_etdyear'])){
				$this->msg->add("Error ! Please input macc's ETD 。");
			}
		}

		if (($parm['acc_etdmon'] =='') && ($parm['acc_etdday'] =='') && ($parm['acc_etdyear'] =='')){
			$parm['acc_etdmon'] ='00';
			$parm['acc_etdday'] ='00';
			$parm['acc_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['acc_etdmon'],$parm['acc_etdday'],$parm['acc_etdyear'])){
				$this->msg->add("Error ! Please input acc's ETD 。");
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

		$q_str = "UPDATE `".$table."` SET `".$field1."` = '".$value1."' , `".$field2."` = '".$value2."' WHERE `id` = '".$id."';";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
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
                        "', status ='"			.'1'.
                        "' WHERE id="	.$parm['id'];
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error !  Database can't update.");
        $this->msg->merge($sql->msg);
        return false;
    }
    return $parm['id'];
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mdf_ie($parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_ie($order_num,$ie,$su,$ie_time) {

$sql = $this->sql;

$q_str = "UPDATE `s_order` SET `ie2` = '".$ie."' , `ie_time2` = '".$ie_time."' , `su` = '".$su."' WHERE `order_num` = '".$order_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_partial_ie($partial_arr,$ie)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_partial_ie($partial_arr,$ie) {

$sql = $this->sql;
$pdtion_arr = array();
foreach($partial_arr as $s_id => $row){

    $q_str = "SELECT max(`rel_etf`) as `etf` , min(`rel_ets`) as `ets` , sum(`su`) as `su` , sum(`qty`) as `qty` , sum(`pdt_qty`) as `pdt_qty` , `ord_num`	
    FROM `schedule`
    WHERE `p_id` = '".$row['p_id']."'
    GROUP BY `ord_num` ;
    ";
    
    $q_results = $sql->query($q_str);
    $rows = $sql->fetch($q_results);

    // $p_su = $rows['su']);
    // $ext_su = $rows['ext_qty']);
    
    // echo $q_str."<br>";
    // echo $ie."<br>";
    // echo $rows['ord_num']."<br>";
    // echo $p_su."<br>";
    // echo $row['p_qty']*$ie."<br>";
    // echo $row['p_etp']."<br>";
    // echo $row['p_etd']."<br>";
    // echo $row['p_ets']."<br>";
    // echo $row['p_etf']."<br>";
    // echo $p_su."<br>";
    $etp_su = $this->distri_month_su($row['p_su'],$row['p_etp'],$row['p_etd']);
    // print_r($etp_su);
    // echo "<p>";
    // echo $ext_su."<br>";
    // echo $rows['su']."<br>";
    // echo $rows['ets']."<br>";
    // echo $rows['etf']."<br>";
    $fty_su = $this->distri_month_su($rows['su'],$rows['ets'],$rows['etf']);
    // print_r($fty_su);
    // echo "<p>";
    
    $ext_period = countDays($rows['ets'],$rows['etf']);

    // p_su
    $q_str = "UPDATE `order_partial` SET 
    `p_su` = '".$row['p_su']."' , 
    `p_etp_su` = '".$etp_su."' , 
    `p_fty_su` = '".$fty_su."' , 
    `p_ets` = '".$rows['ets']."' , 
    `p_etf` = '".$rows['etf']."' , 
    `ext_qty` = '".$rows['qty']."' , 
    `ext_su` = '".$rows['su']."' , 
    `p_qty_done` = '".$rows['pdt_qty']."' , 
    `ext_period` = '".$ext_period."' 
    WHERE `id` = '".$row['p_id']."' 
    ;";
    
    // echo $q_str.'<br>';
    if (!$q_results = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法更新資料庫內容.");
        $this->msg->merge($sql->msg);
        return false;    
    }
    // echo "<p>";
    $pdtion_arr[$row['order_num']] = $row['order_num'];
}

    return $pdtion_arr;
} # end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_partial_id_ie($p_id,$ie) ####
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_partial_id_ie($p_id,$ie) {

$sql = $this->sql;

$q_str = "SELECT max(`rel_etf`) as `etf` , min(`rel_ets`) as `ets` , sum(`su`) as `su` , sum(`qty`) as `qty` , sum(`pdt_qty`) as `pdt_qty` , `ord_num`	
FROM `schedule`
WHERE `p_id` = '".$p_id."'
GROUP BY `ord_num` ;
";

$q_results = $sql->query($q_str);
if( $rows = $sql->fetch($q_results) ){
    $fty_su = $this->distri_month_su($rows['su'],$rows['ets'],$rows['etf']);
    $ext_period = countDays($rows['ets'],$rows['etf']);
} else {
    $fty_su = $rows['ets'] = $rows['etf'] = $rows['qty'] = $rows['su'] = $rows['pdt_qty'] = $ext_period = '';
}

// p_su
$q_str = "UPDATE `order_partial` SET 
`p_fty_su` = '".$fty_su."' , 
`p_ets` = '".$rows['ets']."' , 
`p_etf` = '".$rows['etf']."' , 
`ext_qty` = '".$rows['qty']."' , 
`ext_su` = '".$rows['su']."' , 
`p_qty_done` = '".$rows['pdt_qty']."' , 
`ext_period` = '".$ext_period."' 
WHERE `id` = '".$p_id."' 
;";

// echo $q_str.'<br>';
if (!$q_results = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

    return true;
} # end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_cut($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_cut($parm,$ie) {

$sql = $this->sql;

$q_str = "SELECT sum(`qty`) as `qty` FROM `cutting_out` WHERE `p_id` = '".$parm['p_id']."' ;";
$q_results = $sql->query($q_str);
$row = $sql->fetch($q_results);
// echo $q_str.'<br>';
$q_str = "UPDATE `order_partial` SET `cut_qty` = '".$row['qty']."' , `cut_su` = '".set_su($ie,$row['qty'])."' WHERE `id` = '".$parm['p_id']."' ;";
$q_results = $sql->query($q_str);
// echo $q_str.'<br>';

} # end func

    
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reset_ie($parm)	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reset_ie($parm) {

    $sql = $this->sql;

    #####   更新資料庫內容
    ############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

    $q_str = "UPDATE s_order SET 
    ie1 = '0' , 
    ie_time1 = '0' , 
    su = '0' , 
    status = '0' , 
    cfmer = '' , 
    cfm_date = '' , 
    apver = '' , 
    apv_date = '' , 
    `revise` = `revise`+1 
    WHERE id = '".$parm."';";

    // echo $q_str;              
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error !  Database can't update.");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    return $parm;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reset_ie_final($parm)	#MODE MDF
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reset_ie_final($parm) {

$sql = $this->sql;

$q_str = "UPDATE s_order SET `ie2` = '0' , `ie_time2` = '0' , `revise` = `revise`+1 WHERE `id` = '".$parm."';";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error !  Database can't update.");
    $this->msg->merge($sql->msg);
    return false;    
}

return $parm;

} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_ie_file($parm)	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_ie_file($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE s_order SET `ie_file` = '".$parm['ie_file']."' WHERE `order_num` = '".$parm['order_num']."';";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
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
		if ($parm['mat_etd']) { $q_str = $q_str."mat_etd='".$parm['mat_etd']."',"; }
		if ($parm['macc_etd']) { $q_str = $q_str." m_acc_etd='".$parm['macc_etd']."',"; }
		if ($parm['acc_etd']) { $q_str = $q_str." acc_etd='".$parm['acc_etd']."',"; }
		if ($parm['macc_eta']) { $q_str = $q_str." m_acc_eta='".$parm['macc_eta']."',"; }
		if ($parm['mat_eta']) { $q_str = $q_str."mat_eta='".$parm['mat_eta']."',"; }
		if ($parm['acc_eta']) { $q_str = $q_str." acc_eta='".$parm['acc_eta']."',"; }
		$q_str = substr($q_str,0,-1);
		$q_str = $q_str." WHERE id=".$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_pd_schedule($parm)		[寫入 producting ETS 及 ETF ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_pd_schedule_chk($parm) {
//2007-04-19 日期呈現修改
		
		if (!($parm['ets'] > $GLOBALS['TODAY']))
		{
			$this->msg->add("Error !  ETF must > TODAY.");
			return false;
		}
		if ($parm['ets'] > $parm['etf'] )
		{
			$this->msg->add("Error !  ETS must < ETF.");
			return false;
		}
		if (!$parm['ets'])
		{
			$this->msg->add("Error !  Please choice ETS");
			return false;
		}
		
		if (!$parm['etf'])
		{
			$this->msg->add("Error !  Please choice ETF");
			return false;
		}
		return true;
	}
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_pd_schedule($parm)		[寫入 producting ETS 及 ETF ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_pd_schedule($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~
		$q_str = "UPDATE pdtion SET ";
		$q_str = $q_str."  sub_con='".$parm['sub_con']."'";
		if ($parm['ets']) { $q_str = $q_str.", ets='".$parm['ets']."'"; }
		if ($parm['etf']) { $q_str = $q_str.", etf='".$parm['etf']."'"; }
		
		$q_str = $q_str." WHERE id=".$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
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
//2007-04-19 日期呈現修改		
		if ($parm['ets']>$parm['etf'] )
		{
			$this->msg->add("Error !  ETS must < ETF.");
			return false;
		}
		if (!$parm['ets'])
		{
			$this->msg->add("Error !  Please choice ETS");
			return false;
		}
		
		if (!$parm['etf'])
		{
			$this->msg->add("Error !  Please choice ETF");
			return false;
		}

		$q_str = "UPDATE pdtion SET ets='".		$parm['ets'].
								"', etf='".		$parm['etf'].
								"', fty_su='".	$parm['fty_su'].
								"' WHERE id=".	$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_cfm_pd_schedule($parm)	[刪除 producting ETS 及 ETF 及 fty_su ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_cfm_pd_schedule($id) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE pdtion SET ets=''".
								", etf=''".
								", fty_su=''".
								" WHERE id=".	$id ;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $id;

		return $pdt_id;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_partial($ord_num)	[刪除 Partial ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#M11022502 修改 REVISE (Partial)
	function del_partial($ord_num) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "DELETE FROM `order_partial` WHERE `ord_num` = '".	$ord_num ."'" ;
        echo "<BR>".$q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $ord_num;
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
			$this->msg->add("Error !  Database can't update.");
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
			$this->msg->add("Error !  Database can't update.");
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
			$this->msg->add("Error !  Database can't update.");
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
			$this->msg->add("Error !  Database can't update.");
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
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$q_str = "UPDATE order_partial SET p_qty_shp=p_qty_shp + ".$parm['qty_shp'].								
								", p_shp_date='".		$parm['shp_date'].
								"' WHERE id=".			$parm['p_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$pdt_id = $parm['pd_id'];
		$this->finish_order($parm['p_id'],'',2);
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add($parm) {
                
$sql = $this->sql;
$english = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

//查詢最後版本 
$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
$q_result = $sql->query($q_str);
$cust_row = $sql->fetch($q_result);	


# 加入資料庫(2007.03.02加入尺吋資料)
$q_str = "INSERT INTO s_order (dept,cust,cust_ver,order_num,ref,factory,style,qty,unit,style_num,patt_num,
smpl_ord,uprice,quota,mat_u_cost,mat_useage, acc_u_cost,quota_fee,comm_fee,cm,smpl_fee,etd,
etp,gmr,creator,emb,wash,oth,oth_treat,handling_fee,smpl_apv,ie_time1,agent,fusible,interline,org_cm,line_sex,
lots_unit,cust_po,season,syear,partial_num,opendate) VALUES('".
$parm['dept']."','".
$parm['cust']."','".
$cust_row['ver']."','".
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
$parm['etd']."','".
$parm['etp']."','".
$parm['gmr']."','".
$parm['creator']."','".
$parm['emb']."','".
$parm['wash']."','".
$parm['oth']."','".
$parm['oth_treat']."','".
$parm['handling_fee']."','".
$parm['smpl_apv']."','".							
$parm['ie_time1']."','".							
$parm['agent']."','".
$parm['fusible']."','".
$parm['interline']."','".		

$parm['org_cm']."','".
$parm['line_sex']."','".
$parm['lots_unit']."','".
$parm['cust_po']."','".
$parm['season']."','".
$parm['s_year']."','".
$parm['ps_num']."','".
            
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
        $this->msg->add("successful upload main picture");
    } else {
        $this->msg->add("failure upload main picutre");
    }
}

$j=0;
for($i=0; $i<sizeof($parm['oth_pic']); $i++)
{
    if($parm['oth_pic_upload'][$i])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
        $style_dir	= $GLOBALS['config']['root_dir']."/picture/";  
        $upFile->setSaveTo($style_dir,$parm['order_num']."_".$i.".jpg"); //更換圖檔名稱==>"訂單編號_流水號"
        $up_result = $upFile->upload($parm['oth_pic'][$i], 600, 600); //上傳圖 600X600
        if ($up_result){
            $this->msg->add("successful upload main picture");
        } else {
            $this->msg->add("failure upload main picutre");
        }
        $j++;
    }			
}

$this->update_field_num('pic_num', $j, $parm['order_num']);
$GLOBALS['smpl_ord']->add_order_link($parm['smpl_ord']);
$i=0;

foreach($parm['ps_qty'] as $key => $value)
{
    $q_str = "INSERT INTO order_partial (ord_num,p_etd,p_etp,p_qty,remark,p_su,mks) VALUES('".
                        $parm['order_num']."','".
                        $parm['ps_etd'][$key]."','".
                        $parm['ps_etp'][$key]."','".
                        $parm['ps_qty'][$key]."','".
                        $parm['ps_remark'][$key]."','".
                        $parm['ps_qty'][$key]*$parm['ie1']."','".
                        $english[$i]."')";
    $i++;
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot append order");
        $this->msg->merge($sql->msg);
        return false;    
    }
}

return $ord_id;

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料 -- 無BOM但要做請採購
#					// 2007/09/28 加入 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_unbom($limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust";
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


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("s_order.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	}	
		$mesg = '';
		if ($str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str); 
			$mesg.= "  Dept = [ $str ]. ";
		}		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str);
			$mesg.= "  Cust. = [ $str ]. ";
		}
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_wi_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
		}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
		
		$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.opendate < '2007-09-05'");
   	$srh->add_where_condition("s_order.status >= 0");	
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
function search($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    
    if($mode == 1 || $mode == 2 )
    {
        $argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
    }else{
        $argv = $GLOBALS;
    }

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT s_order.* , cust_init_name as cust_iname, wi.id as wi_id, 
    wi.cfm_date as wi_date, wi.bcfm_date as bom_date, wi.ti_cfm as ti_date 
    FROM s_order, cust LEFT JOIN wi ON wi.wi_num=s_order.order_num ";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    
    if ($mode == 2) {
        $srh->add_sort_condition("status , s_order.id DESC");
    }else{
        $srh->add_sort_condition("s_order.id DESC");
    }
    
    # 分頁
    $srh->row_per_page = 20;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=20;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];

	// $sale_f_mang = $GLOBALS['SALES_F_MANG'];
	// $sale_mang = $GLOBALS['SALES_MANG'];

	// for ($i=0; $i< sizeof($sale_f_mang); $i++)
	// {			
			// if( $user_dept == $sale_f_mang[$i] ) 	$srh->add_where_condition("s_order.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	// }
    
    // $sale_mang = get_sales_dept();
	// foreach ($sale_mang as $key => $val)
	// {			
		// $srh->or_where_condition("s_order.dept LIKE '".$val."%'", "PHP_dept",$val,"");		
	// }
	// print_r($sale_mang);
	// print_r(get_dept_group());
    
	# 分部門顯示
	if ( $dept ) {
		$srh->add_where_condition("s_order.dept = '$dept'", "",$dept,"Department=[ $dept ]. ");
	} else {
		if ($user_team == 'MD') {
            $srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
        } else {
            if ( if_factory() && !$argv['PHP_factory'] ) {
                $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
            } else {
                $dept_group = get_dept_group();
                for ($i=0; $i< sizeof($dept_group); $i++) {			
                    $srh->or_where_condition("s_order.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
                }
            }
        }
	}

    $c_mode = 0 ; #MODE 增加沒有搜尋條件時，只顯示200天內申請的訂單
    if ($mode > 0){
        $mesg = '';
		if (!isset($argv['PHP_etdstr']))$argv['PHP_etdstr']='';
		if (!isset($argv['PHP_etdfsh']))$argv['PHP_etdfsh']='';
		if (!isset($argv['SCH_style']))$argv['SCH_style']='';
		if (!isset($argv['SCH_sample']))$argv['SCH_sample']='';
		if (!isset($argv['SCH_del']))$argv['SCH_del']=0;
		if (!isset($argv['SCH_month']))$argv['SCH_month']='';
		if (!isset($argv['SCH_year']))$argv['SCH_year']='';

		if ($str = strtoupper($argv['PHP_dept']) )  { 
			$srh->add_where_condition("s_order.dept LIKE '%$str%'", "PHP_dept",$str); 
			$mesg.= "  department : [ $str ]. ";
            $c_mode++;
		}
		
		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  customer ref. : [ $str ]. ";
            $c_mode++;
		}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust = [ $str ]. ";
            $c_mode++;
		}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
            $c_mode++;
		}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
            $c_mode++;
		}
		if ($str = $argv['PHP_etdstr'] )  { 
			$srh->add_where_condition("s_order.etd >= '$str'", "PHP_etdstr",$str); 
			$mesg.= "  ETD : $str ~ ";
            $c_mode++;
		}
		if ($str = $argv['PHP_etdfsh'] )  { 
			$srh->add_where_condition("s_order.etd <= '$str'", "PHP_etdfsh",$str); 
			if (!$argv['PHP_etdstr']){	$mesg.= "ETD:  ~  $str ";	}else{ $mesg.= "  ~  $str ";	}
            $c_mode++;
		}	
		if ($str = $argv['SCH_style'] )  { 
			$srh->add_where_condition("s_order.style_num like '%$str%'", "SCH_sample",$str); 
			$mesg.= " Style : [ $str ]. ";
            $c_mode++;
		}	
		if ($str = $argv['SCH_sample'] )  { 
			$srh->add_where_condition("s_order.smpl_ord like '%$str%'", "SCH_sample",$str); 
			$mesg.= " Sample : [ $str ]. ";
            $c_mode++;
		}			
		if (isset($argv['SCH_po']) && $str = $argv['SCH_po'] )  { 
			$srh->add_where_condition("s_order.cust_po like '%$str%'", "SCH_po",$str); 
			$mesg.= " Customer PO# : [ $str ]. ";
            $c_mode++;
		}		
		if ($argv['SCH_year'] && $argv['SCH_month'] )  { 
			$str = $argv['SCH_year'].'-'.$argv['SCH_month'];
			$srh->add_where_condition("s_order.etd like '$str%'", "SCH_year",$str); 
			$mesg.= " ETD : [ $str ]. ";
            $c_mode++;
		}			
		if (!$argv['SCH_year'] && $argv['SCH_month'] )  { 
			$str = date('Y').'-'.$argv['SCH_month'];
			$srh->add_where_condition("s_order.etd like '$str%'", "SCH_year",$str); 
			$mesg.= " ETD : [ $str ]. ";
            $c_mode++;
		}					
		if ($argv['SCH_year'] && !$argv['SCH_month'] )  { 
			$str = $argv['SCH_year'];
			$srh->add_where_condition("s_order.etd like '$str%'", "SCH_year",$str); 
			$mesg.= " ETD : [ $str ]. ";
            $c_mode++;
		}	
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}		
    }
    
    $srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
        
    if (empty($c_mode)){
		$srh->add_where_condition("s_order.opendate >= '".increceDaysInDate($GLOBALS['TODAY'],-200)."'");   // 關聯式察尋 必然要加
    }
    if($argv['SCH_del'] == 1){
        $srh->add_where_condition("s_order.status >= -2");
    }elseif($argv['SCH_del'] == 2){
        $srh->add_where_condition("s_order.status = -2");
   	}else{
   		$srh->add_where_condition("s_order.status >= 0");	
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
    // echo $srh->q_str;
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
#	->mater_search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mater_search($mode=0, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT s_order.* , wi.id as wi_id, cust_init_name as cust_iname, 
                                            wi.ti_cfm, order_partial.p_etd as etd, order_partial.p_etp as etp,
                                            order_partial.p_qty as qty, order_partial.wi_status, order_partial.mks,
                                            order_partial.id as p_id, order_partial.wi_status
                            FROM s_order, cust, order_partial Left Join wi On order_partial.ord_num = wi.wi_num";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("s_order.id DESC");

    # 分頁
    $srh->row_per_page = 18;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {
                $srh->or_where_condition("s_order.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    # 工廠身分進入只看到已完成的 WI
	if ( $user_team <> 'MD' && ( $user_dept=='HJ' || $user_dept == 'LY' || $user_dept == 'CF' ) ) {
		$srh->add_where_condition("wi.status > 0");
	}

    if ( $mode == 1 ) {
		$mesg = '';
		if ( !isset($argv['PHP_etdstr']) )$argv['PHP_etdstr']='';
		if ( !isset($argv['PHP_etdfsh']) )$argv['PHP_etdfsh']='';			
		
		if ( $str = $argv['PHP_dept_code'] ) { 
			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str); 
			$mesg.= "  department = [ $str ]. ";
        }		
		if ( $str = $argv['PHP_cust'] ) { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
        }
		if ( $str = $argv['PHP_num'] ) { 
            $srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
        }
		if ( $str = $argv['PHP_etdstr'] ) { 
            $srh->add_where_condition("s_order.etd >= '$str'", "PHP_etdstr",$str); 
			$mesg.= "  ETD :  $str ~ ";
        }
		if ( $str = $argv['PHP_etdfsh'] ) { 
			$srh->add_where_condition("s_order.etd <= '$str'", "PHP_etdfsh",$str); 
			if($argv['PHP_etdstr']){$etd_msg='';}else{$etd_msg=' ETD : ';}
			$mesg.= $etd_msg." $str ";
        }	
		if ( $str = $argv['PHP_sch_fty'] ) { 
			$srh->add_where_condition("s_order.factory = '$str'", "",$user_dept);
			$mesg.= " FTY = [ $str ]";
        }
		if ( $mesg ) {
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
        }
    }
    
    $srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
    $srh->add_where_condition("s_order.order_num = order_partial.ord_num");   // 關聯式察尋 必然要加
   	$srh->add_where_condition("s_order.status >= 0");	
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
    // echo $srh->q_str;
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
	function smpl_search($smpl) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT order_num, id, etd, etp, qty, su, ie1, opendate FROM s_order ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("s_order.id DESC");
		$srh->row_per_page = 20;
		$srh->add_where_condition("s_order.smpl_ord = '$smpl'");

   		
		$result= $srh->send_query2();   // 2005/11/24 加入 $limit_entries
		if (!is_array($result) || !$result) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		return $result;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function wi_search($mode=0, $where_str='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.*, size_des.size_scale FROM s_order left join size_des on size_des.id=s_order.size".$where_str;

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
##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
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
#	->ord_search($mode=0, $where_str='', $limit_entries=0)	搜尋 訂 單 資料	 
#			mode = 2 :  排產確認後的訂單搜尋 ( status >= 7 )
#			mode = 3 :  已有產出後的訂單搜尋 ( status > 7 )
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ord_search($mode=0, $where_str='', $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$limit_day = increceDaysInDate($GLOBALS['TODAY'],-480);
	
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if($mode == 3)
		{
			$q_header = "SELECT s_order.* , pdtion.*, cust_init_name as cust_iname, s_order.id as s_id,
													order_partial.p_etd as etd, order_partial.p_qty as qty, 
													order_partial.p_qty_done as qty_done, order_partial.p_qty_shp as qty_shp,
													order_partial.id as p_id, order_partial.mks
									 FROM s_order, pdtion, cust, order_partial";
		}else{
			$q_header = "SELECT s_order.* , pdtion.*, cust_init_name as cust_iname, s_order.id as s_id
									 FROM s_order, pdtion, cust";
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
#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 以數字型式顯示頁碼 end
	}
	
		$srh->add_where_condition("s_order.order_num = pdtion.order_num");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加


		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		$sales_dept = $GLOBALS['SALES_DEPT'];

		// if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		// for ($i=0; $i< sizeof($sales_dept); $i++)
		// {			
			// if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		// } 	
	
if ($mode==1 || $mode==2 || $mode==3 || $mode==4 || $mode==5){
		$mesg = '';
		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref# : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
}		


if ($mode==1){   // 當要搜尋的 訂單是 訂單確認後的時
		$srh->add_where_condition("s_order.status >= 4", "","","");   
}		
if ($mode==2){   // 當要搜尋的 訂單是 排產確認後的時
		$srh->add_where_condition("s_order.status >= 7", "","",""); 
		$srh->add_where_condition("finish >= '".$limit_day."' or finish IS NULL or finish ='0000-00-00'", "","","");  		 
}		
if ($mode==3){   // 當要搜尋的 訂單是 排產確認後的時
		$srh->add_where_condition("s_order.status > 7", "","","");   
		$srh->add_where_condition("shp_date >= '".$limit_day."' or shp_date IS NULL", "","",""); 
		$srh->add_where_condition("s_order.order_num = order_partial.ord_num");   // 關聯式察尋 必然要加
		$srh->add_where_condition("pdtion.order_num = order_partial.ord_num");   // 關聯式察尋 必然要加
}		

if ($mode==4){   // 當要搜尋的 訂單是 排產確認後的時
	$where_strs ='';
		if ($str = $argv['PHP_ship'] ) $where_strs = $where_strs." || s_order.status = 12";
		if ($str = $argv['PHP_finish'] ) $where_strs = $where_strs." || s_order.status = 10";

		$srh->add_where_condition("s_order.status = 4 || s_order.status = 6 || s_order.status = 7 || s_order.status = 8 ".$where_strs, "","","");   
		$srh->add_where_condition("finish >= '".$limit_day."' or finish IS NULL or finish ='0000-00-00'", "","","");  		 
		if ($str = $argv['PHP_dept_code'] )  { 
//			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str); 
//			$mesg.= "  Dept = [ $str ]. ";
			}
		

}	

if ($mode==5){   // 當要搜尋的 訂單是 排產確認後的時
	$where_strs ='';
		if ($str = $argv['PHP_ship'] ) $where_strs = $where_strs."|| s_order.status = 12";
		if ($str = $argv['PHP_finish'] ) $where_strs = $where_strs."|| s_order.status = 10";

		$srh->add_where_condition("s_order.status = 7 || s_order.status = 8".$where_strs, "","","");   
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
		// echo $srh->q_str;
##--*****--## 2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
##--*****--## 2006.11.14新頁碼需要的oup_put	end		
		return $op;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_search($mode=0, $where_str='', $limit_entries=0)	搜尋 訂 單 資料	 
#			mode = 2 :  排產確認後的訂單搜尋 ( status >= 7 )
#			mode = 3 :  已有產出後的訂單搜尋 ( status > 7 )
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function shipping_search($mode=0, $where_str='', $limit_entries=0) {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
		$limit_day = increceDaysInDate($GLOBALS['TODAY'],-360);
	
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT s_order.* , pdtion.id, cust_init_name as cust_iname, s_order.id as s_id,
												order_partial.p_etd as etd, sum(order_partial.p_qty) as qty, 
												sum(order_partial.p_qty_done) as qty_done, order_partial.p_qty_shp as qty_shp,
												order_partial.id as p_id, order_partial.mks
								 FROM s_order, pdtion, cust, order_partial";
$q_header = "SELECT s_order.* , pdtion.id, cust_init_name as cust_iname, s_order.id as s_id, order_partial.p_etd as etd, order_partial.p_qty as qty, order_partial.p_qty_done as qty_done, order_partial.p_qty_shp as qty_shp, order_partial.id as p_id, order_partial.mks 
FROM s_order, pdtion, cust, order_partial";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("order_partial.id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 以數字型式顯示頁碼 end
	}
	
		$srh->add_where_condition("s_order.order_num = pdtion.order_num");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.status >= 6", "","","");   
		$srh->add_where_condition("shp_date >= '".$limit_day."' or shp_date IS NULL", "","",""); 
		$srh->add_where_condition("s_order.order_num = order_partial.ord_num");   // 關聯式察尋 必然要加
		$srh->add_where_condition("pdtion.order_num = order_partial.ord_num");   // 關聯式察尋 必然要加
		// $srh->add_group_condition("s_order.order_num");


		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		$sales_dept = $GLOBALS['SALES_DEPT'];

		if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		for ($i=0; $i< sizeof($sales_dept); $i++)
		{			
			if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		} 	

		$mesg = '';
		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref# : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
			}
		
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
	


		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);

		$op['sorder'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;

// echo $srh->q_str;
##--*****--## 2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
##--*****--## 2006.11.14新頁碼需要的oup_put	end		
		return $op;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_mode
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_mode($mode='',$where_str='',$table='',$m_sql='',$page_view='',$show_num=10) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!empty($mode['status'])){
			unset($_SESSION['PAGE']);
			$where_str = "where ";
			$status=0;
			foreach($mode as $key => $val){
				#echo "[".$key."] => [".$val."]<br>";
				if(!empty($val)){
					($status==0)? $and = '' : $and = ' and ';
					if($key == 'cust' || $key == 'factory' ){
						$where_str .= $and.$key." = '".$val."' ";
						$status=1;
					}elseif($key == 'etd_start'){
						$where_str .= $and."etd >= '".$val."' ";
						$status=1;
					}elseif($key == 'etd_end'){
						$where_str .= $and."etd =< '".$val."' ";
						$status=1;
					}elseif($key == 'etd'){
						$where_str .= $and." ".$val." ";
						$status=1;						
					}elseif($key == 'unstatus'){
						$where_str .= $and."status ".$val." ";
						$status=1;
					}elseif($key == 'status'){
						if ( $val == 'clear' ){
							$where_str = "";
						}
					}else{
						$where_str .= $and.$key." LIKE '%".$val."%' ";
						$status=1;
					}
				}
			}
			if ( $where_str == 'where ')$where_str=''; 
		}
		#echo '<br>['.$where_str.']<br>';
		$op = $srh->page_sorting($table,$m_sql,$page_view,$where_str,$show_num);
		return $op;
	} // end funcc


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
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
		}
		if ($str = $parm['cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
		}
		if(!$parm['ref']){
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
		}
		if ($str = $parm['fty'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
		}

	  $fields = "s_order.order_num,s_order.cust,s_order.style,s_order.qty,s_order.ref,s_order.unit,pdtion.qty_done,pdtion.qty_shp,s_order.su,s_order.ie_time1,s_order.ie1,s_order.etd,pdtion.etf,s_order.etp,pdtion.ets,pdtion.start,pdtion.finish,pdtion.factory,s_order.opendate,s_order.apv_date,s_order.smpl_apv,s_order.ptn_upload,pdtion.mat_shp,pdtion.m_acc_shp,s_order.dept,s_order.creator,s_order.style_num,s_order.smpl_ord,s_order.patt_num,s_order.quota,s_order.revise,s_order.status";

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


		$srh->add_where_condition("s_order.order_num = pdtion.order_num");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.status >= 0");	
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
		$mesg = '';
		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref# : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
}		
if ($mode==2){   // 當要搜尋的 訂單是 排產確認後的時
		$srh->add_where_condition("s_order.status >= 4", "","","");   
}else{
		$srh->add_where_condition("s_order.status >= 0");	
}
		$srh->add_where_condition("s_order.order_num = pdtion.order_num");   // 關聯式察尋 必然要加

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
	// $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	// if($user_dept == "J0"){
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0) ) AND s_order.dept LIKE 'J%'  AND s_order.cust = cust.cust_s_name  AND s_order.cust_ver = cust.ver";
	// }elseif($user_dept == "K0"){
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0)) AND s_order.dept LIKE 'K%' AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver";
	// }elseif($user_dept == "T0"){
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0) ) AND s_order.dept LIKE 'T%' AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver";	
	// }elseif($user_dept == "HJ"){
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0) ) AND s_order.dept = 'HJ' AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver";
	// }elseif($user_dept == "LY"){
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0)) AND s_order.dept = 'LY' AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver";
	// }elseif(substr($user_dept,0,1) == "J"){
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0) ) AND s_order.dept = '$user_dept' AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver";			
	// }elseif($user_dept == "KA" || $user_dept == "KB"){
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0) ) AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver AND s_order.dept='$user_dept' ";
	// }else{
		// $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust WHERE ((status=2 AND del_mk=0) ) AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver";
	// }
    
    // SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust 
    // WHERE ((status=2 AND del_mk=0) ) AND s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver ORDER BY s_order.id DESC
    
    $q_header = "SELECT s_order.* , cust_init_name as cust_iname FROM s_order, cust ";
 
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("s_order.id DESC");
    $srh->row_per_page = 10;

    $srh->add_where_condition(" `s_order`.`cust` = `cust`.`cust_s_name` ", "" , "" ,"");
    $srh->add_where_condition(" `s_order`.`cust_ver` = `cust`.`ver` ", "" , "" ,"");
    $srh->add_where_condition(" `s_order`.`status`= '2' AND `s_order`.`del_mk` = '0' ", "" , "" ,"");
    
	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {			
                $srh->or_where_condition("s_order.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
        $srh->q_limit = "LIMIT ".$limit_entries." ";
    }else{
        #--*****--##  2006.11.14 以數字型式顯示頁碼 star				
        $pagesize=10;
        if ($argv['PHP_sr_startno']) {
            $pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
            $pages = $srh->get_page(1,$pagesize);
        } 
        ##--*****--##  2006.11.14 以數字型式顯示頁碼 end 
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
    
    // echo $srh->q_str;
    
    $op['sorder'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['prev_no'] = $srh->prev_no;
    $op['next_no'] = $srh->next_no;
    $op['max_no'] = $srh->max_no;
    $op['last_no'] = $srh->last_no;
    $op['start_no'] = $srh->start_no;
    $op['per_page'] = $srh->row_per_page;
    #--*****--##2006.11.14新頁碼需要的oup_put	start		
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
    #--*****--##2006.11.14新頁碼需要的oup_put	end

    return $op;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->unrev_search($mode=0,$limit_entries=0)	搜尋 待 核可 之訂單 資料	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function unrev_search($mode=0,$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.* , cust_init_name as cust_iname 
								 FROM s_order, cust 
								 WHERE (status=13) AND s_order.cust = cust.cust_s_name  AND s_order.cust_ver = cust.ver";
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
#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 以數字型式顯示頁碼 end   
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
		
#--*****--##2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#--*****--##2006.11.14新頁碼需要的oup_put	end

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

    $q_header = "SELECT s_order.* , cust_init_name as cust_iname 
                             FROM s_order, cust ";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    
    $srh->add_where_condition(" s_order.status = 3"); 	
    $srh->add_where_condition(" s_order.cust = cust.cust_s_name "); 	
    $srh->add_where_condition(" s_order.cust_ver = cust.ver "); 	
    
	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {			
                $srh->or_where_condition("s_order.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("s_order.id DESC");
    $srh->row_per_page = 20;

    if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
        $srh->q_limit = "LIMIT ".$limit_entries." ";
    }else{
        #--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
        ##--*****--##  2006.11.14 以數字型式顯示頁碼 end   
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
		
    #--*****--##2006.11.14新頁碼需要的oup_put	start		
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
    #--*****--##2006.11.14新頁碼需要的oup_put	end

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
		$q_header = "SELECT s_order.id, s_order.order_num, s_order.cust, s_order.style_num, s_order.ptn_upload,
		             s_order.etd, s_order.etp, s_order.qty, s_order.unit, s_order.status, pdtion.sub_con,
		             s_order.smpl_apv, pdtion.ets, s_order.factory, s_order.ref, pdtion.mat_etd,
		             pdtion.m_acc_etd, cust_init_name as cust_iname FROM s_order, pdtion, cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 以數字型式顯示頁碼 end    	
if ($mode==1){
		$mesg = '';
		if (isset($argv['PHP_dept_code']) && $str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str);
			$mesg.= "  Dept = [ $str ]. ";
		}

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref# : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
			}
		$srh->add_where_condition("status >= 4", "PHP_status",$str,"");   // ******* 必然要加 列表皆為 核可後
		$srh->add_where_condition("status < 10", "PHP_status",$str,"");   // ******* 必然要加 不需排產部分移除(己finish訂單)

		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.cust = cust.cust_s_name  AND s_order.cust_ver = cust.ver", "",$str,"");   // 關聯式察尋 必然要加
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
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
		
##--*****--## 2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
##--*****--## 2006.11.14新頁碼需要的oup_put	end		

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
		$q_header = "SELECT s_order.id, s_order.ref, s_order.order_num, s_order.cust,
		             s_order.style_num, s_order.etd, s_order.etp, s_order.qty, s_order.unit, 
		             pdtion.ets, s_order.factory, pdtion.mat_etd, pdtion.m_acc_etd, 
		             s_order.status , cust_init_name as cust_iname FROM s_order, pdtion, cust";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--##  2006.11.14 以數字型式顯示頁碼 end 

	if ($fty_id){
			$srh->add_where_condition("s_order.factory = '$fty_id'"); 
	}

		$srh->add_where_condition("status = 6 ");   // ******* 必然要加 列表皆為 待核可shcedule
		$srh->add_where_condition("s_order.order_num = pdtion.order_num ");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.cust = cust.cust_s_name  AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加

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
		
#--*****--##2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#--*****--##2006.11.14新頁碼需要的oup_put	end		
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
		$q_header = "SELECT s_order.id,s_order.order_num,s_order.cust,s_order.etd,s_order.etp,s_order.qty,s_order.unit,s_order.status, s_order.style,s_order.factory,pdtion.mat_etd,pdtion.m_acc_etd,pdtion.mat_shp, s_order.su,pdtion.m_acc_shp FROM s_order, pdtion ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

##--*****--## 2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--## 2006.11.14 以數字型式顯示頁碼 end    	 
 
//業務部門		
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		$sales_dept = $GLOBALS['SALES_DEPT'];
		if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		for ($i=0; $i< sizeof($sales_dept); $i++)
		{			
			if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
		} 
 
 
if ($mode==1){
		$mesg = '';

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref.# : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
			}
		$srh->add_where_condition("status >= 4", "PHP_status",$str,"");   // ******* 必然要加 可排產的部份
		$srh->add_where_condition("status < 10", "PHP_status",$str,"");   // ******* 必然要加 不需排產部分移除(己finish訂單)
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
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
		
#--*****-- 2006.11.14新頁碼需要的oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];
#--*****-- 2006.11.14新頁碼需要的oup_put	end

		return $op;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $order_num=0) {
        $e_date=increceDaysInDate($GLOBALS['TODAY'],30);
        
		$sql = $this->sql;

		if ($id) {
			$q_str = "SELECT s_order.*, cust_init_name as cust_iname FROM s_order ,cust WHERE s_order.id='$id' AND s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver";
		} elseif ($order_num) {
			$q_str = "SELECT s_order.*, cust_init_name as cust_iname FROM s_order ,cust WHERE s_order.order_num='$order_num' AND s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver";
		} else {
			$this->msg->add("Error ! please specify order number.");
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;
		}

		$q_str="SELECT wi.id, wi.cfm_date, wi.bcfm_date, wi.ti_cfm, wi.fty_chk FROM wi WHERE wi_num = '".$row['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;
		}

		if ($row_wi = $sql->fetch($q_result)) {
			$row['wi_date']=substr($row_wi['cfm_date'],0,10);
			$row['bom_date']=substr($row_wi['bcfm_date'],0,10);
			$row['ws_date']=$row_wi['ti_cfm'];
			$row['wi_id']=$row_wi['id'];
			$row['fty_chk']=$row_wi['fty_chk'];	
		}	
		
		$po_user=$GLOBALS['user']->get(0,$row['creator']);
		$row['creator_id'] = $row['creator'];
		if ($po_user['name'])$row['creator'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$row['cfmer']);
		$row['cfmer_id'] = $row['cfmer'];
		if ($po_user['name'])$row['cfmer'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$row['rev_user']);
		$row['rev_id'] = $row['apver'];
		if ($po_user['name'])$row['rev_user'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$row['apver']);
		$row['apver_id'] = $row['apver'];
		if ($po_user['name'])$row['apver'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$row['last_updator']);
		$row['last_updator_id'] = $row['last_updator'];
		if ($po_user['name'])$row['last_updator'] = $po_user['name'];
		
		$po_user=$GLOBALS['user']->get(0,$row['last_updator']);
		$row['last_updator_id'] = $row['last_updator'];
		if ($po_user['name'])$row['last_updator'] = $po_user['name'];
		
		
		$po_user=$GLOBALS['user']->get(0,$row['del_user']);
		$row['del_user_id'] = $row['del_user'];
		if ($po_user['name'])$row['del_user'] = $po_user['name']; 

		$po_user=$GLOBALS['user']->get(0,$row['del_cfm_user']);
		$row['del_cfm_user_id'] = $row['del_cfm_user'];
		if ($po_user['name'])$row['del_cfm_user'] = $po_user['name'];

		$po_user=$GLOBALS['user']->get(0,$row['del_rev_user']);
		$row['del_rev_user_id'] = $row['del_rev_user'];
		if ($po_user['name'])$row['del_rev_user'] = $po_user['name']; 

		$po_user=$GLOBALS['user']->get(0,$row['del_apv_user']);
		$row['del_apv_user_id'] = $row['del_apv_user'];
		if ($po_user['name'])$row['del_apv_user'] = $po_user['name']; 

		if($row['cust_po']){
			$cust_po = $row['cust_po'];
			$cust_po = explode('|',$cust_po);
			$row['cust_po'] = array();
			foreach($cust_po as $k => $v) {
				$cp = explode('/',$v);
				@$row['cust_po'][$k]['id'] = $k;
				@$row['cust_po'][$k]['po'] = $cp[0];
				@$row['cust_po'][$k]['file'] = $cp[1];
			}
		}

        if(isset($row['close_des'] ))$row['close_des'] = str_replace( chr(13).chr(10), "<br>", $row['close_des'] );
		$q_str="SELECT num as rem_num, rem_id,rem_qty FROM remun_det, remun WHERE ord_num = '".$row['order_num']."' AND remun.id = remun_det.rem_id GROUP BY rem_id";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row['rem_det'] = array();
		while ($row_rem = $sql->fetch($q_result)) {
			$row['rem_det'][]=$row_rem;	
		}

		$q_str="SELECT `inv_num` , `shipping_doc`.`id` FROM `shipping_doc` , `shipping_doc_qty` WHERE `ord_num` = '".$row['order_num']."' AND shipping_doc.id = shipping_doc_qty.s_id GROUP BY s_id";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;
		}

		$row['ship_det'] = array();
		while ($row_ship = $sql->fetch($q_result)) {
			$row['ship_det'][]=$row_ship;
		}

		$q_str="SELECT * FROM stk_ord_link WHERE ord_org = '".$row['order_num']."'";
		$q_result = $sql->query($q_str);
	//	$row['comb_to'] = array();
		while ($row_ship = $sql->fetch($q_result)) {
			$row['fellow_ord'][]=$row_ship;
		}

		$q_str="SELECT * FROM stk_ord_link WHERE ord_new = '".$row['order_num']."'";
		$q_result = $sql->query($q_str);
	//	$row['fellow_up'] = array();
		while ($row_ship = $sql->fetch($q_result)) {
			$row['carry_ord'][]=$row_ship;
		}

		$q_str="SELECT * FROM order_partial WHERE ord_num = '".$row['order_num']."'" ;
		$q_result = $sql->query($q_str);
		$row['partial_ship'] = array();
		$row['ct'] = 0;
		while ($row_ship = $sql->fetch($q_result)) {
			$row['ps_qty'][]=$row_ship['p_qty'];
			$row['ps_ext_qty'][]=$row_ship['ext_qty'];
			$row['ps_qty_done'][]=$row_ship['p_qty_done'];
			$row['ps_wi_status'][]=$row_ship['wi_status'];
			$row['ps_su'][]=$row_ship['p_su'];
			$row['ps_etd'][]=$row_ship['p_etd'];
			$row['ps_etp'][]=$row_ship['p_etp'];
			$row['ps_ets'][]=$row_ship['p_ets'];
			$row['ps_etf'][]=$row_ship['p_etf'];
			$row['ps_id'][]=$row_ship['id'];
			$row['ps_remark'][]=$row_ship['remark'];
			$row['mks'][]=$row_ship['mks'];
			$row['ct'] ++ ;
		}

		#抓pattern
		$q_str="SELECT * FROM ord_ptn_file WHERE ord_id = ".$id." order by file_date asc" ;
		$q_result = $sql->query($q_str);
		while ($row_file = $sql->fetch($q_result)) {
            $file_name = explode(".",$row_file['file_name']);
            if(!$file_name[1]){
                $row_file['file_name'] = $file_name[0].'.mdl';
            }
			$row['pttn'][] = $row_file;
		}

		$ptn_num = sizeof($row['pttn']);
		for($i=0;$i<$ptn_num;$i++){
			if($i==$ptn_num-1)
				$row['pttn'][$i]['download_flag'] = 1;
			else
				$row['pttn'][$i]['download_flag'] = 0;
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
		$q_str = "SELECT s_order.*, pdtion.*, cust_init_name as cust_iname FROM s_order,pdtion, cust WHERE s_order.id='$id' AND s_order.order_num=pdtion.order_num AND s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver";
	} elseif ($order_num) {
		$q_str = "SELECT s_order.*, pdtion.*, cust_init_name as cust_iname FROM s_order,pdtion, cust WHERE order_num='$order_num' AND s_order.order_num=pdtion.order_num AND s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver";
	} else {
		$this->msg->add("Error ! please specify order number.");		    
		return false;
	}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		
		$q_str="SELECT wi.id, wi.cfm_date, wi.bcfm_date, wi.ti_cfm FROM wi WHERE wi_num = '".$row['order_num']."'";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		if ($row_wi = $sql->fetch($q_result)) {
			$row['wi_date']=substr($row_wi['cfm_date'],0,10);	
			$row['bom_date']=substr($row_wi['bcfm_date'],0,10);
			$row['ws_date']=$row_wi['ti_cfm'];	
			$row['wiwi_id']=$row_wi['id'];	
		}			
		

	$po_user=$GLOBALS['user']->get(0,$row['creator']);
	$row['creator_id'] = $row['creator'];
	if ($po_user['name'])$row['creator'] = $po_user['name'];
	$po_user=$GLOBALS['user']->get(0,$row['cfmer']);
	$row['cfmer_id'] = $row['cfmer'];
	if ($po_user['name'])$row['cfmer'] = $po_user['name'];
	$po_user=$GLOBALS['user']->get(0,$row['apver']);
	$row['apver_id'] = $row['apver'];
	if ($po_user['name'])$row['apver'] = $po_user['name'];
	$po_user=$GLOBALS['user']->get(0,$row['last_updator']);
	$row['last_updator_id'] = $row['last_updator'];
	if ($po_user['name'])$row['last_updator'] = $po_user['name'];		




		return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_output($ord_num)	定指訂訂單號碼 取出垓單之記錄 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ord_output($ord_num) {

    $sql = $this->sql;
	
    $q_str = "SELECT s_order.opendate, s_order.cfm_date,s_order.creator,s_order.apv_date, s_order.id,
    s_order.schd_date,s_order.cust, s_order.qty,s_order.smpl_apv,s_order.factory, s_order.style,
    s_order.su,s_order.etd,s_order.etp, s_order.status,pdtion.ets,pdtion.etf,pdtion.pre_ets,pdtion.pre_etf,pdtion.ext_ets,pdtion.ext_etf,pdtion.ext_period,pdtion.mat_shp,
    pdtion.check_lots_po_etd, pdtion.check_lots_shp_eta, pdtion.check_lots_rcv_date,
    pdtion.check_acc_po_etd, pdtion.check_acc_shp_eta, pdtion.check_acc_rcv_date,
    pdtion.lots_po_etd, pdtion.lots_shp_eta, pdtion.lots_rcv_date,
    pdtion.acc_po_etd, pdtion.acc_shp_eta, pdtion.acc_rcv_date,
    pdtion.acc_shp, pdtion.m_acc_shp,pdtion.qty_done,pdtion.start, pdtion.sub_con,
    pdtion.finish, pdtion.shp_date ,cust_init_name as cust_iname, pdtion.mat_etd, pdtion.m_acc_etd,
    pdtion.mat_eta, pdtion.m_acc_eta,  pdtion.mat_ship_way , 
    pdtion.rel_ets , pdtion.rel_etf
  
    FROM s_order, cust LEFT JOIN pdtion on s_order.order_num = pdtion.order_num 
    WHERE s_order.order_num='$ord_num' AND s_order.cust=cust.cust_s_name AND s_order.cust_ver = cust.ver 
    GROUP BY s_order.order_num
    ";
		       		
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    if (!$row = $sql->fetch($q_result)) {
        $this->msg->add("Error ! Can't find this record!");
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
			$this->msg->add("Error ! Can't find this record!");
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
		$q_str = "SELECT s_order.*,pdtion.*, s_order.id as sid, cust_init_name as cust_iname FROM s_order,pdtion, cust WHERE s_order.id='$id' AND s_order.order_num=pdtion.order_num AND s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver";
	} elseif ($order_num) {
		$q_str = "SELECT s_order.*,pdtion.*, s_order.id as sid, cust_init_name as cust_iname FROM s_order,pdtion, cust WHERE s_order.order_num='$order_num' AND s_order.order_num=pdtion.order_num AND s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver";
	} else {
		$this->msg->add("Error ! please specify order number.");		    
		return false;
	}
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}


		$q_str="SELECT wi.id, wi.cfm_date, wi.bcfm_date, wi.ti_cfm FROM wi WHERE wi_num = '".$row['order_num']."'";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		if ($row_wi = $sql->fetch($q_result)) {
			$row['wi_date']=substr($row_wi['cfm_date'],0,10);	
			$row['bom_date']=substr($row_wi['bcfm_date'],0,10);
			$row['ws_date']=$row_wi['ti_cfm'];	
			$row['wi_id']=$row_wi['id'];	
		}	
	
	
	$po_user=$GLOBALS['user']->get(0,$row['creator']);
	$row['creator_id'] = $row['creator'];
	if ($po_user['name'])$row['creator'] = $po_user['name'];
	$po_user=$GLOBALS['user']->get(0,$row['cfmer']);
	$row['cfmer_id'] = $row['cfmer'];
	if ($po_user['name'])$row['cfmer'] = $po_user['name'];
	$po_user=$GLOBALS['user']->get(0,$row['apver']);
	$row['apver_id'] = $row['apver'];
	if ($po_user['name'])$row['apver'] = $po_user['name'];
	$po_user=$GLOBALS['user']->get(0,$row['last_updator']);
	$row['last_updator_id'] = $row['last_updator'];
	if ($po_user['name'])$row['last_updator'] = $po_user['name'];
	
		return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pdtion($order_num, $factory)	抓出指定的 pdtion 記錄 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pdtion($order_num, $factory) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM pdtion WHERE order_num='$order_num' AND factory='$factory' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		return $row;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pdtion($order_num, $factory)	抓出指定的 pdtion 記錄 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pdtion_new($order_num, $factory) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM pdtion WHERE order_num='$order_num' AND factory='$factory' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
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
		if ($id){
			$q_str = "UPDATE pdtion SET etp_su='"	.$parm['etp_su'].
			 						"',	factory='"	.$parm['factory'].
								"'  WHERE id='"		.$id."'";
		}elseif($ord_num){
			$q_str = "UPDATE pdtion SET etp_su='"	.$parm['etp_su'].
								"',	factory='"	.$parm['factory'].
								"'  WHERE order_num='"		.$ord_num."'";
		}


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
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

		$q_str = "UPDATE s_order,pdtion SET pdtion.etp_su='".$parm['etp_su']."',pdtion.fty_su='".$parm['fty_su']."', pdtion.ets='NULL', pdtion.etf ='NULL'  WHERE s_order.order_num='".$ord_num."' AND pdtion.order_num ='".$ord_num."'";



		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return $ord_num;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm, $mode=0,$status) {
// print_r($parm);
		$sql = $this->sql;
		$english = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

/*
		if($parm['old_orders']<>"no_order")
		{
		$q_str1= "UPDATE smpl_ord SET	orders='".$parm['old_orders']."' where num='".$parm['old_num']."'";		
		if (!$q_result1 = $sql->query($q_str1)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}				
		if ($parm['orders'])
		{
			$parm['orders']=$parm['orders']."|".$parm['order_num'];			
		}else{
			$parm['orders']=$parm['order_num'];
		}
		$q_str1= "UPDATE smpl_ord SET	orders='".$parm['orders']."' where num='".$parm['num']."'";
		if (!$q_result1 = $sql->query($q_str1)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}	
		}
*/		
		
		if ($mode == 0){			
		#####   更新資料庫內容
			$q_str = "UPDATE s_order SET ".
            " ref='"				.$parm['ref'].
            "',	factory='"			.$parm['factory'].
            "',	style='"			.$parm['style'].
            "',	qty='"				.$parm['qty'].
            "',	su='"				.$parm['su'].
            "', unit='"				.$parm['unit'].
            "',	style_num='"		.$parm['style_num'].
            "',	patt_num='"			.$parm['patt_num'].
            "', uprice='"			.$parm['uprice'].
            "',	quota='"			.$parm['quota'].													

            "', mat_u_cost='"		.$parm['mat_u_cost'].
            "',	mat_useage='"		.$parm['mat_useage'].
            "',	acc_u_cost='"		.$parm['acc_u_cost'].
            "',	quota_fee='"		.$parm['quota_fee'].
            "',	comm_fee='"			.$parm['comm_fee'].
            "', cm='"				.$parm['cm'].
            "',	smpl_fee='"			.$parm['smpl_fee'].
            "',	emb='"				.$parm['emb'].
            "',	wash='"				.$parm['wash'].
            "',	oth='"				.$parm['oth'].
            "',	oth_treat='"		.$parm['oth_treat'].
            "',	handling_fee='"		.$parm['handling_fee'].

            "', smpl_ord='"			.$parm['smpl_ord'].
            "', smpl_apv='"			.$parm['smpl_apv'].

            "', ie_time1='"			.$parm['ie_time1'].							

            "',	etd='"				.$parm['etd'].
            "', etp='"				.$parm['etp'].
            "', gmr='"				.$parm['gmr'].

            "', agent='"			.$parm['agent'].
            "', fusible='"			.$parm['fusible'].
            "', interline='"		.$parm['interline'].

            "', line_sex='"			.$parm['line_sex'].
            "', lots_unit='"		.$parm['lots_unit'].

            "', season='"			.$parm['season'].
            "', syear='"			.$parm['syear'].


            "', last_updator='"		.$parm['last_updator'].

            "', partial_num='"		.$parm['ps_num'].

            "', last_update=		NOW()".
            "  WHERE id='"			.$parm['id']."'";

		} elseif($mode ==1){      // --- order revise -----

			$q_str = "
			UPDATE s_order SET 
			ref 		= '".$parm['ref']."' , 
			style 		= '".$parm['style']."' , 
			unit 		= '".$parm['unit']."' ,	
			style_num 	= '".$parm['style_num']."', 
			patt_num	= '".$parm['patt_num']."', 
			uprice 		= '".$parm['uprice']."' ,
			quota		= '".$parm['quota']."',	
			qty			= '".$parm['qty']."',
			su			= '".$parm['su']."',
			mat_u_cost 	= '".$parm['mat_u_cost']."',
			mat_useage	= '".$parm['mat_useage']."',
			acc_u_cost	= '".$parm['acc_u_cost']."',
			quota_fee	= '".$parm['quota_fee']."',
			comm_fee	= '".$parm['comm_fee']."',
			cm			= '".$parm['cm']."',
			smpl_fee	= '".$parm['smpl_fee']."',
			emb			= '".$parm['emb']."',
			wash		= '".$parm['wash']."',
			oth			= '".$parm['oth']."',
			oth_treat	= '".$parm['oth_treat']."',
			smpl_ord	= '".$parm['smpl_ord']."',
			smpl_apv	= '".$parm['smpl_apv']."',
			ie_time1	= '".$parm['ie_time1']."',
			ie1			= '".$parm['ie1']."',
			ie2			= '".$parm['ie2']."',
			etd			= '".$parm['etd']."',
			etp			= '".$parm['etp']."',
			gmr			= '".$parm['gmr']."',
			revise		= ".$parm['revise'].",
			status		= ".$parm['status'].",
			cfmer		= '', 
			cfm_date 	= '".'0000-00-00'."',
			rev_user 	= '',
			rev_date 	= '".'0000-00-00'."',
			apver		= '',
			apv_date 	= '".'0000-00-00'."',
			agent		= '".$parm['agent']."',
			fusible		= '".$parm['fusible']."',
			interline	= '".$parm['interline']."',
			line_sex	= '".$parm['line_sex']."',
			lots_unit	= '".$parm['lots_unit']."',
			season		= '".$parm['season']."',
			syear		= '".$parm['syear']."',
			last_updator= '".$parm['last_updator']."',
			partial_num = '".$parm['ps_num']."',
			last_update	= NOW()"."  WHERE id='".$parm['id']."'";
		}
// echo $q_str;
			
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
//		$this->update_field('status', $parm['status'], $parm['id']);

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
				unlink($style_dir.$parm['order_num'].".jpg") or die("can't delete old picture:".$pic_id.".jpg");  // 刪除舊檔
			}
				//上傳大圖 600X600
				$upFile->setSaveTo($style_dir,$parm['order_num'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);
		}



		$j=0;
		for($i=0; $i<$parm['pic_num']; $i++)
		{
			if($parm['oth_pic_upload'][$i])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
				if(file_exists($style_dir.$parm['order_num'].$i.".jpg")){
					unlink($style_dir.$parm['order_num']."_".$i.".jpg");  // 刪除舊檔
				}
				$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  
				$upFile->setSaveTo($style_dir,$parm['order_num']."_".$i.".jpg"); //更換圖檔名稱==>"訂單編號_流水號"
				$up_result = $upFile->upload($parm['oth_pic'][$i], 600, 600); //上傳圖 600X600				
			}			
			$j++;
		}
		for($i=($parm['pic_num']-1); $i<sizeof($parm['oth_pic']); $i++)
		{
			if($parm['oth_pic_upload'][$i])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
				$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  
				$upFile->setSaveTo($style_dir,$parm['order_num']."_".$i.".jpg"); //更換圖檔名稱==>"訂單編號_流水號"
				$up_result = $upFile->upload($parm['oth_pic'][$i], 600, 600); //上傳圖 600X600
					if ($up_result){
						$this->msg->add("successful upload main picture");
					} else {
						$this->msg->add("failure upload main picutre");
					}	
					$j++;				
			}			
			
		}
        
		$this->update_field_num('pic_num', $j, $parm['order_num']);
		$GLOBALS['smpl_ord']->add_order_link($parm['smpl_ord']);
	 
		$i=0;
		foreach( $parm['ps_qty'] as $key => $value ) {
			if( !$parm['ps_id'][$key] ) {
				$q_str = "INSERT INTO order_partial (ord_num,p_etd,p_etp,p_qty,p_su,remark,mks) VALUES('".
				$parm['order_num']."','".
				$parm['ps_etd'][$key]."','".
				$parm['ps_etp'][$key]."','".
				$parm['ps_qty'][$key]."','".
				$parm['ps_su'][$key]."','".
				$parm['ps_remark'][$key]."','".
				$english[$i]."')";
			} else {
				#M11022502 修改 REVISE (Partial)
				$q_str = "UPDATE order_partial SET 
				p_etd       ='"	.$parm['ps_etd'][$key]."', 
				p_etp       ='".$parm['ps_etp'][$key]."', 
				mks         ='".$english[$i]."', 
				p_qty       ='".$parm['ps_qty'][$key]."', 
				p_su        ='".$parm['ps_su'][$key]."', 
				remark      ='".$parm['ps_remark'][$key]."'
				WHERE id	='".$parm['ps_id'][$key]."'";
                
				// p_etp_su    ='', 
				// p_fty_su    ='', 
				// ext_qty     ='0', 
				// ext_su      ='0', 
				// p_ets       ='0000-00-00', 
				// p_etf       ='0000-00-00', 
				// ext_period	='0', 
				// p_qty_done	='0', 
				// p_qty_shp   ='0', 
				// pdt_status	='0', 
				// p_shp_date	=''                 
			}
            
            // echo $q_str.'<br>';
            
			$i++;
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot append order");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}
		
		#M11010501 # 台文增加 2011/1/4 
		// if( $parm['dept'] == 'DA' ){
			// $this->update_field_num( 'ie1' , $parm['ie1'] , $parm['order_num'] );
			// $this->update_field_num( 'su' , $parm['su'] , $parm['su'] );
		// }
	 
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
			$this->msg->add("Error !  Database can't update.");
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
		$q_str = "UPDATE s_order SET status = ".$parm['status'].
							", cfmer='".$parm['cfmer'].
							"', cfm_date = NOW()".
							"  WHERE id= '".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
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
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_rev($parm)		 訂單送出 待確認  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function send_rev($parm) {

	$sql = $this->sql;

	#####   更新資料庫內容
	$q_str = "UPDATE s_order SET status=		13".
						", last_updator='"	.$parm['last_updator'].
						"', last_update=		NOW()".
						"  WHERE id='"		.$parm['id']."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$pdt_id = $parm['id'];
	
	return $pdt_id;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->do_rev($parm)		訂單 確認ok  送出待 核可  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function do_rev($parm) {

	$sql = $this->sql;

	#####   更新資料庫內容
	$q_str = "UPDATE s_order SET status = 3 ".
						",  rev_user 	= '".$parm['rev_user'].
						"', rev_date 	= NOW()".
						"  WHERE id 	= '".$parm['id']."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$pdt_id = $parm['id'];

	return $pdt_id;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reject_rev($parm)		訂單 REJECT 確認  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reject_rev($parm) {

	$sql = $this->sql;

	#####   更新資料庫內容
	$q_str = "UPDATE s_order SET status =	5".
						", rev_user = '".$parm['rev_user'].
						"', rev_date =	NOW()".
						"  WHERE id = '".$parm['id']."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
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
		$q_str = "UPDATE s_order SET status = ".$parm['status'].
							", apver = '".$parm['apver'].
							"', apv_date = NOW()".
							"  WHERE id = '".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
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
			$this->msg->add("Error !  Database can't update.");
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
# ************************ 會更新 capacity 內的欄位 ******************
#	->delete_month_su($ord_ets,$ord_etf,$T_su, $fty, $cat)
#		將本張訂單己存在於capacity中的su清除
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function delete_month_su($ord_ets,$ord_etf,$T_su, $fty, $cat) {		
		$ets=explode('-',$ord_ets);
		$etf=explode('-',$ord_etf);
		$date_time=countDays($ord_ets,$ord_etf);
		$day_su=$T_su/$date_time;
	if ($ets[1]<>$etf[1] || ($ets[1] == $etf[1] && $ets[0]<>$etf[0]))//當開始結束不在同月時
	{
		//第一個月
		$tmp_date=getDaysInMonth($ets[1],$ets[0]); //取得該月的最後一日為幾號
		$mon_time[0]=countDays($ord_ets,$ets[0]."-".$ets[1]."-".$tmp_date); //計算期間有幾日
		$mon_su[0]=(int)($day_su*$mon_time[0]); //計算該月SU
		$mon_mm[0]=$ets[1];
		$mon_year[0]=$ets[0];
		
		//最後一個月
		$mon_time[1]=countDays($etf[0]."-".$etf[1]."-0",$ord_etf);	//計算期間有幾日
		$mon_su[1]=(int)($day_su*$mon_time[1]); //計算該月SU
		$mon_mm[1]=$etf[1];
		$mon_year[1]=$etf[0];
		
		$j=2;
		//中間月份
		if ($ets[1]==12){$ets_tmp=0;}else{$ets_tmp=$ets[1];}
		if ($etf[0] > $ets[0]) {$etf_tmp=$etf[1]+12;}else{$etf_tmp=$etf[1];}
		for ($i=($ets[1]+1); $i< $etf_tmp; $i++)
		{
			if ($i>12)
			{
				$xm=$i-12;
				$mon_year[$j]=$etf[0];
			} else {
				$xm=$i;
				$mon_year[$j]=$ets[0];
			}
			$mon_time[$j]=getDaysInMonth($xm,$mon_year[$j]); //取得該月共幾日
			$mon_su[$j]=(int)($day_su*$mon_time[$j]);	//計算該月SU
			if ($xm < 10){ $mon_mm[$j]="0".$xm;}else{$mon_mm[$j]=$xm;}					
			$j++;
		}
		$tmp=$T_su;
		for ($i=0; $i<sizeof($mon_time); $i++) //補足su的差額於最後一個月
		{
			$tmp=$tmp-$mon_su[$i];
		}
		if ($tmp > 0)$mon_su[1]=$mon_su[1]+$tmp;
	}else{ //當開始結束在同月時
		$mon_time[0]=$date_time;
		$mon_su[0]=(int)$date_time*$day_su;
		$mon_mm[0]=$ets[1];
		$mon_year[0]=$ets[0];
	}
		for ($i=0; $i<sizeof($mon_time); $i++)
		{
			$F = $GLOBALS['capaci']->delete_su($fty, $mon_year[$i], $mon_mm[$i], $cat, $mon_su[$i]);
		}
			
		return true;
	
}
# ************************ 會更新 capacity 內的欄位 ******************
#	->distri_month_su($T_su, $s_date, $f_date, $fty, $cat, $mode=0)
#		Detail description	: 將 數量 換成su 再分配到生產月份內 
#							: 寫入 capacity table 的 $field 內 [如沒找到 error ]
#							: 傳回 陣列 ( 200505=>su , 200506=>su, ......
#		$fty, $cat 為了要在 capacity table更新 [ $cat 指的是 capacity內的 c_type 欄位名 ]
#
#			$mode === 0 時 為正常的加入 <>   $mode = 1 時  為加入一個負質 即減去 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function distri_month_su($T_su, $s_date, $f_date, $fty='', $cat='', $mode=0,$add_check=1) {

    if($mode==1) { $factor = -1; } else { $factor = 1; }  // 加入 正負值 視需要-- 減或加 capaci
    $div = array();
    $distribute ='';   // 做為csv 變數

    list($s_year,$s_mon,$s_day) = split("-",$s_date);  // 開始日
    list($f_year,$f_mon,$f_day) = split("-",$f_date);  // 結束日
    
    $days =	countDays($s_date,$f_date);
    // $T_su = $su;		// 總 su 數
    if ($days==0)$days=1;
    $day_su = $T_su/$days;		// 每日產出 --(偏小)

    // 計算總共有幾個月份?
    $y = $f_year - $s_year;
    $m = 12*$y + (12-$s_mon+1) - (12-$f_mon);

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
            $su = $T_su;
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
        $su_mon = $su_mon+1;
        $tmp_m = $mon;
        $div[$tmp_m] = $su;   // 置入 array 

        # #####============ 加入 capacity ->    #########################

        $su_m = substr($mon,4);
        $su = $su * $factor; 	// 加入正負值 2005/11/21
        if($add_check) {
            if (!$F = $GLOBALS['capaci']->update_su($fty, $su_year, $su_m, $cat, $su)) {
                $this->msg->add("Error ! cannot update [".$cat."] field of capacity table, pls try later !");
                $this->msg->merge($sql->msg);
                return false;    
            }
        }
        $distribute = $distribute.','.$mon.$su;
    }

    $distribute = substr($distribute,1);  // 去除開頭的',' 符號

    // 傳回的參數為一個 csv 如: 2005071200,200508850,
	return $distribute;

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->creat_ord_pdtion($parm)		加入 新的 pdtion 訂單記錄 [ 寫入工廠 及 etp_su 排產數]
#								傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function creat_ord_pdtion($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO pdtion (order_num,factory) VALUES('".
							$parm['order_num']."','".							
							$parm['factory']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update new record.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

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
			$this->msg->add("Error ! Database can't update new record.");
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
	
	##### 更新資料庫內容
	$q_str = "UPDATE `s_order` SET `".$field."` = '".addslashes($val)."'  WHERE `id` = '".$id."';";
	// echo "<BR>".$q_str."<BR>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	return true;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_num($field, $val, $id) 2006-12-19更新 s_order資料記錄內 某個單一欄位 part2
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_num($field, $val, $id,$table='s_order') {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE ".$table." SET ".$field."='".$val."'  WHERE order_num='".$id."'";
// echo $q_str."<BR>";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_pdtfld_num($field, $val, $id) 2006-12-19更新 s_order資料記錄內 某個單一欄位 part2
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_pdtfld_num($field, $val, $id) {

    $sql = $this->sql;
    ############### 檢查輸入項目
    
    #####   更新資料庫內容
    $q_str = "UPDATE pdtion SET ".$field."='".$val."'  WHERE order_num='".$id."'";
    // echo $q_str.'<br>';

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't update.");
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
		if($val == 'NULL')
		{
			$q_str = "UPDATE pdtion SET ".$field."=".$val."  WHERE id=".$id." ";
		}else{
			$q_str = "UPDATE pdtion SET ".$field."='".$val."'  WHERE id=".$id." ";
		}
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update內容.");
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
			$this->msg->add("Error ! please specify order number.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM s_order WHERE order_num='$id' ";
		}else{
			$q_str = "DELETE FROM s_order WHERE id='$id' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_pdtion($id)		刪除   [由ID]刪除
#							資料表 : pdtion
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_pdtion($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! please specify order number for pdtion.");		    
			return false;
		}
		$q_str = "DELETE FROM pdtion WHERE order_num='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access !");
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
		
		if ( $tbl == 'order_partial' || $tbl == 'schedule' || $tbl == 'saw_out_put' ) 
			$order_num = 'ord_num';
		else
			$order_num = 'order_num';

		if ($id) {
			$q_str = "SELECT ".$field." FROM ".$tbl." WHERE id='".$id."' ";
		} elseif($ord_num) {
			$q_str = "SELECT ".$field." FROM ".$tbl." WHERE $order_num='".$ord_num."' ";
		} else {
			$this->msg->add("Error! not enough info to get data record !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		// echo $q_str."<br>";
		mysql_query("SET NAMES 'big5'");
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
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
			$this->msg->add("Error !  Database can't update.");
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
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}



		return $argv['id'];
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_like($field, $val, $id)		更新 s_order資料記錄內 某個單一欄位
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_like($field, $val, $key, $key_v) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE s_order SET ".$field."='".$val."'  WHERE ".$key." like '".$key_v."%' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_apvd_ord($mode=0, $where_str='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.*, cust.cust_init_name as cust_iname FROM cust, s_order left join wi on wi.wi_num=s_order.order_num";

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
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];

	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	}
	
	
//搜尋條件 start
		$mesg = '';
		if ($str = strtoupper($argv['PHP_dept_code']) )  { 
			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str); 
			$mesg.= "  Dept = [ $str ]. ";
			}		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
			$srh->add_where_condition("s_order.status >= '4'"); 
			$srh->add_where_condition("s_order.status <> '5'");
			$srh->add_where_condition("s_order.status < '8'");
			$srh->add_where_condition("s_order.cust = cust.cust_s_name  AND s_order.cust_ver = cust.ver");
			
		if($mode == 2)
		{
			$srh->add_where_condition("wi.status < 1 || wi.status IS NULL");
			$srh->add_where_condition("wi.revise < 1 || wi.revise IS NULL");
		}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
//搜尋條件 end


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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_ord($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT sum(order_partial.p_su) as su
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND p_etd like '".$yymm."%' AND 
										s_order.status >= 4 AND s_order.status <> 5 AND s_order.status <> 13 ".$where_str;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return false;
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $row['su'];
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_ord_style($fty, $yymm, $type, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT sum(order_partial.p_su) as su
						  FROM s_order, style_type, order_partial
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.style = style_type.style_type AND s_order.factory='".$fty."' AND 
										p_etd like '".$yymm."%' AND style_type.memo = '".$type."' AND
										s_order.status >= 4 AND  s_order.status <> 5 AND s_order.status <> 13 ".$where_str;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return false;
			
		}
		return $row['su'];
	} // end func	
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_ord_prc($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT sum(order_partial.p_qty * uprice) as ord_amt
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND p_etd like '".$yymm."%' AND 
										s_order.status >= 4 AND  s_order.status <> 5 AND s_order.status <> 13 ".$where_str;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return false;
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $row['ord_amt'];
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_ord_full($fty, $yystr,$yyend, $where_str='',$mode=0) {

		$sql = $this->sql;		
		$rtn = array();
		$q_str = "SELECT sum(order_partial.p_su) as su, YEAR(p_etd)as etd_year, MONTH(p_etd) as etd_month
						  FROM s_order, order_partial
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND p_etd >= '".$yystr."' AND p_etd <='".$yyend."' AND
										s_order.status >= 4 AND  s_order.status <> 5 AND  s_order.status <> 13 ".$where_str." 
							GROUP BY etd_year, etd_month ";
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
//		echo $yymm."====>".$row['su']."<br>";
		return $rtn;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_ord_prc_full($fty, $yystr,$yyend, $where_str='') {

		$sql = $this->sql;		
		$rtn = array();
		$q_str = "SELECT sum(order_partial.p_qty * s_order.uprice) as ord_amt, YEAR(p_etd)as etd_year, 
										 MONTH(p_etd) as etd_month
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND p_etd >= '".$yystr."' AND p_etd <='".$yyend."' AND
										s_order.status >= 4 AND  s_order.status <> 5 AND s_order.status <> 13 ".$where_str." 
							GROUP BY etd_year, etd_month";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			$rtn[$tmp] = $row['ord_amt'];
			
		}
		return $rtn;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_schd($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT sum(order_partial.p_su) as su
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND p_etd like '".$yymm."%' AND 
										s_order.status >= 7 ".$where_str;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return false;
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $row['su'];
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_schd_full($fty, $yystr,$yyend, $where_str='') {

		$sql = $this->sql;		
		$rtn = array();
		$q_str = "SELECT sum(order_partial.p_su) as su, YEAR(p_etd)as etd_year, MONTH(p_etd) as etd_month
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND p_etd >= '".$yystr."' AND p_etd <='".$yyend."' AND 
										s_order.status >= 7 ".$where_str."
							GROUP BY etd_year, etd_month";

//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			$rtn[$tmp] = $row['su'];
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $rtn;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_output($fty, $yymm, $where_str='') {
		$rec = array();
		$sql = $this->sql;	

		$q_str = "SELECT sum(saw_out_put.su) as su
						  FROM s_order,  saw_out_put , pdt_saw_line, order_partial
							WHERE s_order.order_num = order_partial.ord_num AND order_partial.id = saw_out_put.p_id AND
										s_order.order_num = saw_out_put.ord_num AND s_order.factory='".$fty."' AND 
										saw_out_put.line_id = pdt_saw_line.id AND
										order_partial.p_etd like '".$yymm."%' AND 
										s_order.status >= 8 ".$where_str;
// echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return false;
			
		}

//	if($where_str)	echo $yymm."====>".$row['su']."<br>";
		return $row['su'];
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
# mode = 0 key值 :  年-月
# mode = 2 key值 :  月
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_output_full($fty, $yystr,$yyend, $where_str='',$mode=0) {

		$sql = $this->sql;		
		$rtn = array();
		
		$q_str = "SELECT sum( `saw_out_put`.`su` ) AS su, YEAR( `order_partial`.`p_etd` ) AS `etd_year` , MONTH( `order_partial`.`p_etd` ) AS `etd_month` 
							FROM `s_order` , `order_partial` , `saw_out_put` , `pdt_saw_line`
							WHERE 
							`s_order`.`order_num` = `saw_out_put`.`ord_num` AND 
							`saw_out_put`.`ord_num` = `order_partial`.`ord_num` AND 
							`saw_out_put`.`line_id` = `pdt_saw_line`.`id` AND 
							`s_order`.`factory` = '".$fty."' AND 
							`order_partial`.`id` = `saw_out_put`.`p_id` AND 
							`order_partial`.`p_etd` BETWEEN '".$yystr."' AND '".$yyend."' AND 
							`s_order`.`status` >= 7 ".$where_str."
							GROUP BY `etd_year` , `etd_month` ";

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
// foreach($rtn as $key => $value) echo $key."====>".$value."<BR>";
		return $rtn;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_shp($fty, $yymm, $where_str='') {

		$sql = $this->sql;	
		$su = 0;	
		$q_str = "SELECT sum(shipping.su) as su
						  FROM s_order,  shipping, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND shipping.p_id = order_partial.id AND
										s_order.order_num = shipping.ord_num AND s_order.factory='".$fty."' AND 
										order_partial.p_etd like '".$yymm."%' AND 
										s_order.status >= 8 ".$where_str."
							GROUP BY order_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$su += (int)$row['su'];
			
		}
//		echo $yymm."====>".$su."<br>";
		return $su;
	} // end func	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
# mode = 0 key值 :  年-月
# mode = 2 key值 :  月
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_shp_full($fty, $yystr,$yyend, $where_str='',$mode=0) {

		$sql = $this->sql;		
		$rtn = array();

		$q_str = "SELECT sum(shipping.su) as su, YEAR(p_etd)as etd_year, MONTH(p_etd) as etd_month
						  FROM s_order,  pdtion, shipping, order_partial
							WHERE order_partial.ord_num = s_order.order_num AND order_partial.id = shipping.p_id AND
										s_order.order_num = pdtion.order_num AND s_order.order_num = shipping.ord_num AND
										s_order.factory='".$fty."' AND 
										order_partial.p_etd >= '".$yystr."' AND order_partial.p_etd <='".$yyend."' AND 
										s_order.status >= 8 ".$where_str." 
							GROUP BY etd_year, etd_month";

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

	
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_shp_prc($fty, $yymm, $where_str='') {

		$sql = $this->sql;	
		$shp_price = 0;	
		$q_str = "SELECT sum(shipping.qty * uprice) as shp_price
						  FROM s_order,  shipping, order_partial  
							WHERE s_order.order_num = order_partial.ord_num AND order_partial.id = shipping.p_id AND
										s_order.order_num = shipping.ord_num AND s_order.factory='".$fty."' AND 
										order_partial.p_etd like '".$yymm."%' AND 
										s_order.status >= 8 ".$where_str." 
							GROUP BY order_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$shp_price += $row['shp_price'];
			
		}
//		echo $yymm."====>".$su."<br>";
		return $shp_price;
	} // end func		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
# mode = 0 key值 :  年-月
# mode = 2 key值 :  月
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_shp_prc_full($fty, $yystr,$yyend, $where_str='',$mode=0) {

		$sql = $this->sql;		
		$rtn = array();


		$q_str = "SELECT sum(shipping.qty * s_order.uprice) as shp_price, YEAR(p_etd)as etd_year, 
										 MONTH(p_etd) as etd_month
						  FROM s_order,  pdtion, shipping, order_partial
							WHERE s_order.order_num = order_partial.ord_num AND pdtion.order_num = order_partial.ord_num AND
										order_partial.id = shipping.p_id AND
										s_order.order_num = pdtion.order_num AND s_order.order_num = shipping.ord_num AND
										s_order.factory='".$fty."' AND 
										order_partial.p_etd >= '".$yystr."' AND order_partial.p_etd <='".$yyend."' AND 
										s_order.status >= 8 ".$where_str." 
							GROUP BY etd_year, etd_month";

// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			if($mode == 0)$rtn[$tmp] = $row['shp_price'];
			if($mode == 2)$rtn[$row['etd_month']] = $row['shp_price'];
			
		}
		return $rtn;
	} // end func			
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_unsch($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$q_str = "SELECT sum(order_partial.p_su) as su
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND p_etd like '".$yymm."%' AND 
										s_order.status < 7 AND s_order.status >=4 AND s_order.status <> 5 ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			return false;
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $row['su'];
	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_unsch_full($fty, $yystr,$yyend, $where_str='') {

		$sql = $this->sql;		
		$rtn = array();

		$q_str = "SELECT sum(order_partial.p_su) as su, YEAR(p_etd)as etd_year, MONTH(p_etd) as etd_month
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND 
										p_etd >= '".$yystr."' AND p_etd <='".$yyend."' AND  
										s_order.status < 7 AND s_order.status >3 AND s_order.status <> 5 ".$where_str."
							GROUP BY etd_year, etd_month";

//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			$rtn[$tmp] = $row['su'];
			
		}
		return $rtn;
	} // end func			


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_unout($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$su = 0;
		$q_str = "SELECT (order_partial.p_su - sum(saw_out_put.su) ) as out_su
						  FROM s_order,  saw_out_put, order_partial
							WHERE s_order.order_num = saw_out_put.ord_num AND order_partial.id = saw_out_put.p_id AND
										s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND 
										s_order.etd like '".$yymm."%' AND 
										s_order.status < 10 AND s_order.status >=4 AND s_order.status <> 5										
										".$where_str." GROUP BY s_order.order_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['out_su'] > 0) $su += $row['out_su'];
			
		}
		
		$q_str = "SELECT s_order.su
						  FROM s_order,  pdtion 
							WHERE s_order.order_num =pdtion.order_num AND 
										s_order.factory='".$fty."' AND 
										s_order.etd like '".$yymm."%' AND 
										s_order.status >=4 AND s_order.status <> 5 AND s_order.status < 10 AND										
										pdtion.qty_done = 0										
										".$where_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$su += $row['su'];
			
		}		
		return $su;
	} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
# mode = 0 key值 :  年-月
# mode = 2 key值 :  月
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_etd_unout_full($fty, $yystr,$yyend, $where_str='',$mode=0) {

		$sql = $this->sql;		
		$su = 0;
		$rtn = array();

		$q_str = "SELECT (order_partial.p_su - sum(saw_out_put.su) ) as out_su, YEAR(p_etd)as etd_year, 
										MONTH(p_etd) as etd_month, sum(saw_out_put.su) as d_su, order_partial.p_su as ord_su
						  FROM s_order,  saw_out_put , pdtion, order_partial
							WHERE s_order.order_num = order_partial.ord_num AND pdtion.order_num = order_partial.ord_num AND
										saw_out_put.p_id = order_partial.id AND
										s_order.order_num = saw_out_put.ord_num AND s_order.order_num = pdtion.order_num AND
										pdtion.order_num = saw_out_put.ord_num AND
										s_order.factory='".$fty."' AND
										order_partial.p_etd >= '".$yystr."' AND order_partial.p_etd <='".$yyend."' AND  
										s_order.status < 10 AND s_order.status >=4 AND s_order.status <> 5										
										".$where_str." 							
							GROUP BY s_order.order_num
							HAVING d_su < ord_su";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			if(!isset($rtn[$tmp]) && $mode == 0)$rtn[$tmp] = 0;
			if(!isset($rtn[$row['etd_month']]) && $mode == 2)$rtn[$row['etd_month']] = 0;
			if($mode == 0)$rtn[$tmp] += $row['out_su'];
			if($mode == 2)$rtn[$row['etd_month']] += $row['out_su'];
			
		}

		$q_str = "SELECT sum(order_partial.p_su) as su,  YEAR(p_etd)as etd_year, MONTH(p_etd) as etd_month
						  FROM s_order,  pdtion, order_partial
							WHERE s_order.order_num = order_partial.ord_num AND pdtion.order_num = order_partial.ord_num AND
										s_order.order_num =pdtion.order_num AND s_order.factory='".$fty."' AND 
										order_partial.p_etd >= '".$yystr."' AND order_partial.p_etd <='".$yyend."' AND
										s_order.status >=4 AND s_order.status <> 5 AND s_order.status < 10 AND										
										order_partial.p_qty_done = 0										
										".$where_str."GROUP BY etd_year, etd_month";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			if(!isset($rtn[$tmp]) && $mode == 0)$rtn[$tmp] = 0;
			if(!isset($rtn[$row['etd_month']]) && $mode == 2)$rtn[$row['etd_month']] = 0;
			if($mode == 0)$rtn[$tmp] += $row['su'];
			if($mode == 2)$rtn[$row['etd_month']] += $row['su'];
			
		}		
		return $rtn;
	} // end func		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_un_ord($fty, $yymm, $where_str='') {

		$sql = $this->sql;		
		$su = 0;
		$q_str = "SELECT p_su as su, p_qty as qty, style, order_num, status 
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND s_order.factory='".$fty."' AND p_etd like '".$yymm."%' AND 
										(s_order.status < 4 OR s_order.status = 5 OR s_order.status = 13) AND s_order.status >= 0 ".$where_str;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if ($row['su'] == 0)
			{
				if ($row['style']=='PS' || $row['style']=='BS' || $row['style']=='BZ' || $row['style']=='DR' || $row['style']=='JK' || $row['style']=='PS-J' || $row['style']=='PS-P' || $row['style']=='PS-S' || $row['style']=='VS' || $row['style']=='SS' || $row['style']=='TP'){
					$row['su'] = 2*$row['qty'];
				}else{
					$row['su'] = 1*$row['qty'];
				}				
			}				
			$su += $row['su'];
		}
		return $su;
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_one_un_ord_full($fty, $yystr,$yyend, $where_str='') {

		$sql = $this->sql;		
		$su = 0;
		$rtn = array();

		$q_str = "SELECT sum(order_partial.p_su) as su, YEAR(p_etd)as etd_year, MONTH(p_etd) as etd_month
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND
										s_order.factory='".$fty."' AND order_partial.p_su > 0 AND 
										order_partial.p_etd >= '".$yystr."' AND order_partial.p_etd <='".$yyend."' AND  
										(s_order.status < 4 OR  s_order.status = 5 OR s_order.status = 13) AND s_order.status >= 0 ".$where_str."
							GROUP BY etd_year,  etd_month ";
		// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			$rtn[$tmp] = $row['su'];
		}
		
		$q_str = "SELECT sum(p_qty) as qty, style, YEAR(p_etd)as etd_year, MONTH(p_etd) as etd_month
						  FROM s_order, order_partial 
							WHERE s_order.order_num = order_partial.ord_num AND s_order.factory='".$fty."' AND 
										p_su = 0 AND p_etd >= '".$yystr."' AND p_etd <='".$yyend."' AND  										
										(s_order.status < 4 OR  s_order.status = 5 OR s_order.status = 13) AND s_order.status >= 0 ".$where_str."
							GROUP BY etd_year,  etd_month, style";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
				if ($row['style']=='PS' || $row['style']=='BS' || $row['style']=='BZ' || $row['style']=='DR' || $row['style']=='JK' || $row['style']=='PS-J' || $row['style']=='PS-P' || $row['style']=='PS-S' || $row['style']=='VS' || $row['style']=='SS'){
					$row['su'] = 2*$row['qty'];
				}else{
					$row['su'] = 1*$row['qty'];
				}	
							
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			if(!isset($rtn[$tmp]))$rtn[$tmp] = 0;
			$rtn[$tmp] += $row['su'];
            // echo $rtn[$tmp].'<br>';
		}
		
	

		return $rtn;
	} // end func	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->orgainzation_ord($op) 組織訂單需要內容
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function orgainzation_ord($op)
{
	$parm = $op['order'];
  
	// 計算 lead time ( Partial ) 
  #M11042201 修改 lead time (Partial)
  $etp = $parm['ps_etp'][0];
  foreach($parm['ps_etp'] as $key) $etp = $etp < $key ? $etp : $key;

  $etd = $parm['ps_etd'][0];
  foreach($parm['ps_etd'] as $key) $etd = $etd > $key ? $etd : $key;

  $op['lead_time'] = countDays ($etp,$etd);



	// 檢查 相片是否存在
	if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
		$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
	} else {
		$op['main_pic'] = "./images/graydot.gif";
	}

	//08.09.23 其他相片加入
	for($i=0; $i<$op['order']['pic_num']; $i++)
	{
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num']."_".$i.".jpg")){
			$op['oth_pic'][$i] = "./picture/".$op['order']['order_num']."_".$i.".jpg";
			$op['oth_num'][$i] = $i;
			$img_size = GetImageSize($op['oth_pic'][$i]);
			if ($img_size[0] < $img_size[1]) $op['oth_height'][$i] = 1;
			}else{
				$op['oth_pic'][$i] = '';
			}
	}
	
	
	// 檢查 預估IE上傳檔
	if(file_exists($GLOBALS['config']['root_dir']."/ord_ie/".$op['order']['order_num']."_ie1.xls")){		
		$op['order']['ie1_file'] = $op['order']['order_num']."_ie1.xls";
		$op['order']['ie1_check'] = 1;
	} else {
		$op['order']['ie1_check'] = 0;
	}	
	
	// 檢查 實際IE上傳檔
	if(file_exists($GLOBALS['config']['root_dir']."/ord_ie/".$op['order']['order_num']."_ie2.xls")){
		$op['order']['ie2_file'] = $op['order']['order_num']."_ie2.xls";
		$op['order']['ie2_check'] = 1;
	} else {
		$op['order']['ie2_check'] = 0;
	}		
	
	// 檢查 customer po上傳檔 cust_po_check = 確認是否有檔案
	if(file_exists($GLOBALS['config']['root_dir']."/ord_cust_po/".$op['order']['order_num'].".pdf")){
		$op['order']['cust_po_file'] = $op['order']['order_num'].".pdf";
		$op['order']['cust_po_check'] = 1;
	} else if(file_exists($GLOBALS['config']['root_dir']."/ord_cust_po/".$op['order']['order_num'].".rar")) {
		$op['order']['cust_po_file'] = $op['order']['order_num'].".rar";
		$op['order']['cust_po_check'] = 1;	
	} else if(file_exists($GLOBALS['config']['root_dir']."/ord_cust_po/".$op['order']['order_num'].".zip")) {
		$op['order']['cust_po_file'] = $op['order']['order_num'].".zip";
		$op['order']['cust_po_check'] = 1;	
	} else {
		$op['order']['cust_po_check'] = 0;
	}			
	
	// 取出 handling fee 的值 (LY 廠才需要)
	# 2013-07-11 將今天以後的訂單的 handling_fee 寫回 s_order 內(factory = LY 且 排除 L 開頭的訂單)
	# 2013-07-11 由於 工繳 目前是手動挑選單訂，自行加入 handling_fee。改成 工繳 多一欄 將 handling_fee 納入計算(2013-07-11程式更新後所新建立的訂單才納入計算)
	# 2013-07-11 今天以前的 handling_fee 欄位不填值，改用程式判斷2012/08/15之後將handling_fee設為0.25，目的是讓前台顯示(order list、order show、order cfm...等)正常
	$handling_row = $this->get_fields("set_value", " where set_name='handling_fee'","para_set");
	if($parm['factory'] == 'LY' and $parm['opendate'] >= '2013-07-11' and substr($parm['order_num'],0,1) <> 'L'){
		$parm['handling_fee'] = $parm['handling_fee'];
		$op['order']['handling_fee'] = $parm['handling_fee'];
	}elseif($parm['factory'] == 'LY' and $parm['opendate'] >= '2012-08-15' and substr($parm['order_num'],0,1) <> 'L'){
		$parm['handling_fee'] = $handling_row[0];
		$op['order']['handling_fee'] = $parm['handling_fee'];
	}else{
		$parm['handling_fee'] = 0.00;
		$op['order']['handling_fee'] = 0.00;
	}
    
	//計算 C.M. estimate
	if($op['order']['ie2'] > 0){
		// $fty_cm = $this->get_fields("set_value", " where set_name='".strtolower($op['order']['factory'])."-cm'","para_set");
		// $op['order']['cm2'] = $op['order']['ie2'] * $fty_cm[0];
		$op['order']['cm2'] = $op['order']['ie2'] * $op['order']['org_cm'];
	}elseif($op['order']['ie1'] > 0){
		// $fty_cm = $this->get_fields("set_value", " where set_name='".strtolower($op['order']['factory'])."-cm'","para_set");
		// $op['order']['cm2'] = $op['order']['ie1'] * $fty_cm[0];
		$op['order']['cm2'] = $op['order']['ie1'] * $op['order']['org_cm'];
	}else{
		$op['order']['cm2'] = $op['order']['cm'];
	}
    
// echo $op['order']['ie1'].','.$op['order']['ie2'].','.$fty_cm[0].','.strtolower($op['order']['factory']) ;
	// 計算  SU 供 html -----------------------------
	//   2005/08/31 改由資料庫抓 su [以 ie 計算填入 ]
	$op['order']['f_su'] = $op['order']['su'];

	// 計算  gm rate -----------------------------
	$op['order']['unit_cost'] = ($parm['mat_u_cost']* $parm['mat_useage'])+ $parm['interline']+ $parm['fusible']+ $parm['acc_u_cost'] + $parm['quota_fee'] + $parm['comm_fee'] + $op['order']['cm2'] + $parm['emb'] + $parm['wash'] + $parm['oth'] + $parm['handling_fee'];
	$op['order']['grand_cost'] = $op['order']['unit_cost']*$parm['qty'] + $parm['smpl_fee'];
	$op['order']['sales'] = $parm['uprice']*$parm['qty'];
	$op['order']['gm'] = $op['order']['sales'] - $op['order']['grand_cost'];
	if ($op['order']['sales']){
		$op['order']['gm_rate'] = ($op['order']['gm']/ $op['order']['sales'])*100;
	}else{
		$op['order']['gm_rate'] = 0;
	}
	return $op;		
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_shiped() {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		
		$q_header = "
		SELECT 
		`s_order`.`id` , `s_order`.`order_num` , `s_order`.`apv_date` , 
		`s_order`.`style_num` , `s_order`.`cust` , `s_order`.`ref` , `s_order`.`dept` , 
		`s_order`.`factory` , `s_order`.`style` , `s_order`.`qty` , `s_order`.`su` , 
		`s_order`.`uprice` , `s_order`.`quota` , `s_order`.`smpl_ord` , `s_order`.`size` , 
		`s_order`.`etd` , `s_order`.`etp` , `s_order`.`ie1` , `s_order`.`ie2` , `s_order`.`status` , 


		`s_order`.`mat_useage` , `s_order`.`mat_u_cost` , `s_order`.`fusible` , `s_order`.`interline` , 
		`s_order`.`acc_u_cost` , 
		`s_order`.`emb` , `s_order`.`wash` , `s_order`.`oth` , 
		`s_order`.`rel_cm_cost` , 
		`s_order`.`cm` , `s_order`.`fty_cm` , 
		`s_order`.`quota_fee` , `s_order`.`comm_fee` , `s_order`.`ship_fob` , `s_order`.`comm_fee` , `s_order`.`smpl_fee` , 		
		
		`cust`.`cust_init_name` as `cust_name` , 
		`wi`.`id` as `wi_id` , `wi`.`bcfm_date`
		
		FROM 
		s_order, pdtion,cust,wi ";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
		$srh->row_per_page = 5;

		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
    }else{
     	$pages = $srh->get_page(1,$pagesize);
   	} 


	//2006/05/12 adding 
	$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
//部門 : K0,J0,T0
	// $sale_f_mang = $GLOBALS['SALES_F_MANG'];
	// $sale_mang = $GLOBALS['SALES_MANG'];	
	// for ($i=0; $i< sizeof($sale_f_mang); $i++)
	// {			
			// if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("s_order.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	// }
//部門 : 業務部門
	// $sales_dept = $GLOBALS['SALES_DEPT'];
	// if ($team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	// for ($i=0; $i< sizeof($sales_dept); $i++)
	// {			
			// if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");		
	// }	

	$mesg = '';
	if ($str = strtoupper($argv['SCH_ord']) )  { 
		$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "SCH_ord",$str); 
		$mesg.= "  Order : [ $str ]. ";
	}
		
	if ($str = $argv['SCH_cust'] )  { 
		$srh->add_where_condition("s_order.cust = '$str'", "SCH_cust",$str); 
		$mesg.= "  Cust = [ $str ]. ";
		}
	if ($str = $argv['SCH_ship'] )  { 
		$srh->add_where_condition("pdtion.shp_date like '$str%'", "SCH_ship",$str); 
		$mesg.= "  Ship date : [ $str ]. ";
		}
	if ($str = $argv['SCH_fty'] )  { 
		$srh->add_where_condition("s_order.factory = '$str'", "SCH_fty",$str); 
		$mesg.= "  factory = [ $str ]. ";
		}		
	if ($str = $argv['SCH_status'] )  { 
		$srh->add_where_condition("s_order.status >= 4", "SCH_status",$str); 
		$mesg.= "  Finished only ";
	}else{
		$srh->add_where_condition("s_order.status >= 4");
	}	
	if ($mesg)
	{
		$msg = "Search ".$mesg;
		$this->msg->add($msg);
	}
   
	$srh->add_where_condition("pdtion.order_num = s_order.order_num");
	$srh->add_where_condition("cust.cust_s_name = s_order.cust  AND s_order.cust_ver = cust.ver");
	$srh->add_where_condition("s_order.apv_date > '2010-01-01'");
	$srh->add_where_condition("s_order.factory <> s_order.dept");
	$srh->add_where_condition("s_order.order_num = wi.wi_num");
	$srh->add_where_condition("wi.status = '2'");


  
	$result= $srh->send_query2();   // 2005/11/24 加入 $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}

	if (!$result){   // 當查尋無資料時
		$op['record_NONE'] = 1;
	}
		$op['ord'] = $result;  // 資料錄 拋入 $op
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function check_sourceing($ord_num,$source)
{
	$sql = $this->sql;		
	$q_str = "SELECT id
					  FROM lots_use 
						WHERE lots_use.support = $source AND lots_use.smpl_code = '$ord_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			return 1;			
		}


	$q_str = "SELECT id
					  FROM acc_use 
						WHERE acc_use.support = $source AND acc_use.smpl_code = '$ord_num'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			return 1;			
		}		
		return 0;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_close($rcv_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_non_rcvd($mat_cat,$ord,$acc_cat=0) {

$sql = $this->sql;
$mk = 0;
$back = '';
        $mat_rcvd_date = $this->get_field_value('mat_shp','',$ord,'pdtion');
        $macc_rcvd_date = $this->get_field_value('m_acc_shp','',$ord,'pdtion');
        $acc_rcvd_date = $this->get_field_value('acc_shp','',$ord,'pdtion');

    if( $mat_cat == 'l' && ($mat_rcvd_date == '1111-11-11' ||!$mat_rcvd_date ))
    {
        //判斷是否不用買料
        $q_str = "SELECT id FROM lots_use WHERE lots_use.support = 0 AND smpl_code ='".$ord."'";
        $q_result = $sql->query($q_str);
        if (!$row = $sql->fetch($q_result)) {
            $q_str = "UPDATE pdtion SET mat_shp = '1111-11-11' WHERE order_num = '".$ord."'";
            $q_result = $sql->query($q_str);
        //	$back = '1111-11-11';
            return '1111-11-11';
        }			
        //判斷是否都用庫存
        $q_str = "SELECT lots_use.id FROM lots_use, bom_lots 
                            WHERE  bom_lots.lots_used_id = lots_use.id AND lots_use.support = 0 AND 
                                   bom_lots.ap_mark <> 'stock' AND  smpl_code ='".$ord."'";
        $q_result = $sql->query($q_str);
        // echo $q_str."<BR>";
        if (!$row = $sql->fetch($q_result)) {
            $q_str = "UPDATE pdtion SET mat_shp = '1111-11-11' WHERE order_num = '".$ord."'";
            // echo $q_str." ---<BR>";
            $q_result = $sql->query($q_str);
            return '1111-11-11';
        }
        if($mat_rcvd_date == '1111-11-11'){
            $q_str = "UPDATE pdtion SET mat_shp = NULL WHERE order_num = '".$ord."'";
            $q_result = $sql->query($q_str);
            return '';
        }


    }else{
//主要副料
    if ( $acc_cat == 2 || $acc_cat == 3 && ($macc_rcvd_date == '1111-11-11' ||!$macc_rcvd_date ))
    {		
            $q_str = "SELECT id FROM acc_use 
                                WHERE  acc_use.acc_cat = '1' AND acc_use.support = 0 AND
                                             acc_use.smpl_code ='".$ord."'";

            $q_result = $sql->query($q_str);
            if (!$row = $sql->fetch($q_result)) {
            
                $q_str = "UPDATE pdtion SET m_acc_shp = '1111-11-11' WHERE order_num = '".$ord."'";
                $q_result = $sql->query($q_str);		
                return '1111-11-11';
            }
             
            //判斷是否都用庫存
        $q_str = "SELECT acc_use.id FROM acc_use, bom_acc 
                                WHERE  bom_acc.acc_used_id = acc_use.id AND acc_use.acc_cat = '1' AND
                                             acc_use.support = 0 AND bom_acc.ap_mark <> 'stock' AND
                                       smpl_code ='".$ord."'";
            $q_result = $sql->query($q_str);
            if (!$row = $sql->fetch($q_result)) {
            
                $q_str = "UPDATE pdtion SET m_acc_shp = '1111-11-11' WHERE order_num = '".$ord."'";
                $q_result = $sql->query($q_str);
                return '1111-11-11';
            }
            
            if($macc_rcvd_date == '1111-11-11'){
                $q_str = "UPDATE pdtion SET m_acc_shp = NULL WHERE order_num = '".$ord."'";
                $q_result = $sql->query($q_str);
                return '';
            }

        }
//其他副料		
        if ( $acc_cat == 1 || $acc_cat == 3 && ($acc_rcvd_date == '1111-11-11' ||!$acc_rcvd_date ))
        {
            $q_str = "SELECT id FROM acc_use 
                                WHERE  acc_use.acc_cat = '0' AND acc_use.support = 0 AND
                                             acc_use.smpl_code ='".$ord."'";

            $q_result = $sql->query($q_str);		
            if (!$row = $sql->fetch($q_result)) {	
                $q_str = "UPDATE pdtion SET acc_shp = '1111-11-11' WHERE order_num = '".$ord."'";
                $q_result = $sql->query($q_str);		
                return '1111-11-11';
            }
            //判斷是否都用庫存		
        $q_str = "SELECT acc_use.id FROM acc_use, bom_acc 
                                WHERE  bom_acc.acc_used_id = acc_use.id AND acc_use.acc_cat = '0' AND
                                             acc_use.support = 0 AND bom_acc.ap_mark <> 'stock' AND
                                       smpl_code ='".$ord."'";
            $q_result = $sql->query($q_str);
            if (!$row = $sql->fetch($q_result)) {	
            
                $q_str = "UPDATE pdtion SET m_acc_shp = '1111-11-11' WHERE order_num = '".$ord."'";
                $q_result = $sql->query($q_str);		
                return '1111-11-11';
            }					
            if($acc_rcvd_date == '1111-11-11'){
                $q_str = "UPDATE pdtion SET acc_shp = NULL WHERE order_num = '".$ord."'";
                $q_result = $sql->query($q_str);	
                return '';						
            }

                    
        }
        
    }


//		return $back;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reset_pdtion($mat_cat,$wi_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reset_pdtion($mat_cat,$wi_id) {

    $sql = $this->sql;
    
    // if( ( empty($mat_eta) || $mat_eta == '0000-00-00' || empty($mat_shp) || $mat_shp == '0000-00-00' ) )
    // if( $mat_cat == 'l' ) {

    # Lots
    
    $q_str = "SELECT `bom_lots`.`id`,`bom_lots`.`ap_mark` , 
    `lots_use`.`smpl_code` , `lots_use`.`support`
    FROM `bom_lots` 
    LEFT JOIN `lots_use` ON ( `bom_lots`.`lots_used_id` = `lots_use`.`id` )
    WHERE `bom_lots`.`wi_id` = '".$wi_id."' AND `bom_lots`.`ap_mark` != 'stock' AND `bom_lots`.`dis_ver` = '0'
    GROUP BY `bom_lots`.`id`
    ;";
    
    // echo $q_str.'<br>';
    
    $check_lots_bom = $check_lots_po_etd = $check_lots_shp_eta = $check_lots_rcv_date = 0;
    $check_bom = $check_po = $check_shp = $check_rcv = $check_acc_bom = $check_acc_po_etd = $check_acc_shp_eta = $check_acc_rcv_date = 0;
    $lots_po_etd = $lots_ship_eta = $lots_rcv_date = $check = array();
    $order_num = '';
    
    $q_result = $sql->query($q_str);
    while ($row = $sql->fetch($q_result)) {	
        $order_num = $row['smpl_code'];
        
        if( $row['support'] == '0' ) {

            # PO_ETD
            $q_str = "SELECT `ap_det`.`po_spare` , `ap_det`.`po_eta` , 
            `ap_det`.`ap_num` , `ap_det`.`mat_cat` , `ap_det`.`mat_id` , `ap_det`.`color` , `ap_det`.`size` 
            FROM `ap`,`ap_det` 
            WHERE 
            `ap_det`.`wi_id` = '".$wi_id."' AND 
            `ap_det`.`bom_id` = '".$row['id']."' AND 
            `ap_det`.`ap_num` = `ap`.`ap_num` AND 
            `ap_det`.`mat_cat` = 'l' AND 
            `ap`.`status` = '12'
            ;";
            // echo $q_str .'<br>';
            $q_po_etd = $sql->query($q_str);
            while ($row_po_etd = $sql->fetch($q_po_etd)) {
                $po_id = $row_po_etd['po_spare'];
                $ap_num = $row_po_etd['ap_num'];
                $mat_cat = $row_po_etd['mat_cat'];
                $mat_id = $row_po_etd['mat_id'];
                $color = $row_po_etd['color'];
                $size = $row_po_etd['size'];
                
                # DATE
                if( $row_po_etd['po_eta'] && $row_po_etd['po_eta'] != '0000-00-00' ) $lots_po_etd[] = ( $row_po_etd['rev_eta'] > $row_po_etd['po_eta'] ) ? $row_po_etd['rev_eta'] : $row_po_etd['po_eta'];

                # PO QTY
                $check[$po_id]['PO']=1;
            }

            # SHP_ETA
            $q_str = "SELECT `po_ship`.`ship_eta`,`po_ship`.`ship_way`,`po_ship`.`status`
            FROM `po_ship` , `po_ship_det` 
            WHERE 
            `po_ship_det`.`ap_num` = '".$ap_num."' AND `po_ship_det`.`mat_cat` = '".$mat_cat."' AND `po_ship_det`.`mat_id` = '".$mat_id."' AND `po_ship_det`.`color` = '".$color."' AND `po_ship_det`.`size` = '".$size."' AND 
            `po_ship`.`id` = `po_ship_det`.`ship_id`  AND 
            `po_ship`.`status` = '2'
            
            ;";
            // echo $q_str .'<br>';
            $q_shp_eta = $sql->query($q_str);
            while ($row_shp_eta = $sql->fetch($q_shp_eta)) {
                $lots_ship_way = $row_shp_eta['ship_way'];
            
                # DATE
                if( $row_shp_eta['ship_eta'] ) $lots_ship_eta[] = $row_shp_eta['ship_eta'];

                # SHP QTY
                $check[$po_id]['SHP']=1;
            }

            # RCV_DATE
            $q_str = "SELECT `receive`.`rcv_cfm_date`
            FROM `receive` , `receive_det` 
            WHERE
            `receive_det`.`po_id` = '".$po_id."' AND
            `receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
            `receive`.`status` = '4'
            ;";
            // echo $q_str .'<br>';
            $q_rcv_date = $sql->query($q_str);
            while ($row_rcv_date = $sql->fetch($q_rcv_date)) {
            
                # DATE
                if( $row_rcv_date['rcv_cfm_date'] ) $lots_rcv_date[] = $row_rcv_date['rcv_cfm_date'];
                
                # RCV QTY
                $check[$po_id]['RCV']=1;
            }
            
            # 確認 BOM 採購狀態
            $check_bom++;
            $check_po += $check[$po_id]['PO'];
            $check_shp += $check[$po_id]['SHP'];
            $check_rcv += $check[$po_id]['RCV'];
        }
    }
    // echo $order_num.' : '.$check_bom.'|'.$check_po.'|'.$check_shp.'|'.$check_rcv.'<br>';
    
    # 確認 BOM 採購狀態
    $check_lots_bom = ( $check_bom > 0 ) ? 1 : 0 ;
    $check_lots_po_etd = ( $check_po >= $check_bom ) ? 1 : 0 ;
    $check_lots_shp_eta = ( $check_shp >= $check_po ) ? 1 : 0 ;
    $check_lots_rcv_date = ( $check_rcv >= $check_shp ) ? 1 : 0 ;
    // print_r($lots_po_etd);
        
    if( $check_bom > 0 ){
    
        sort($lots_po_etd);
        sort($lots_ship_eta);
        sort($lots_rcv_date);
        $lots_po_etd = $lots_po_etd[0];
        $lots_ship_eta = $lots_ship_eta[0];
        $lots_rcv_date = $lots_rcv_date[0];

    }
    
    $lots_po_etd = ( $lots_po_etd && $lots_po_etd != '0000-00-00' ) ? "'".$lots_po_etd."'" : 'NULL';
    $lots_ship_eta = ( $lots_ship_eta && $lots_ship_eta != '0000-00-00' ) ? "'".$lots_ship_eta."'" : 'NULL';
    $lots_rcv_date = ( $lots_rcv_date && $lots_rcv_date != '0000-00-00' ) ? "'".$lots_rcv_date."'" : 'NULL';
    
    
    
    # Acc
    
    $q_str = "SELECT `bom_acc`.`id`,`bom_acc`.`ap_mark` , 
    `acc_use`.`smpl_code` , `acc_use`.`acc_cat` , `acc_use`.`support`
    FROM `bom_acc` 
    LEFT JOIN `acc_use` ON ( `bom_acc`.`acc_used_id` = `acc_use`.`id` )
    WHERE `bom_acc`.`wi_id` = '".$wi_id."' AND `bom_acc`.`ap_mark` != 'stock' AND `bom_acc`.`dis_ver` = '0'
    GROUP BY `bom_acc`.`id`
    ;";
    
    // echo $q_str.'<br>';
    
    $check_acc_bom = $check_acc_po_etd = $check_acc_shp_eta = $check_acc_rcv_date = 0;
    $check_bom = $check_po = $check_shp = $check_rcv = $check_acc_bom = $check_acc_po_etd = $check_acc_shp_eta = $check_acc_rcv_date = 0;
    $acc_po_etd = $acc_ship_eta = $acc_rcv_date = $check = array();
    
    $q_result = $sql->query($q_str);
    while ($row = $sql->fetch($q_result)) {	
        $order_num = $row['smpl_code'];
        
        if( $row['acc_cat'] == '1' && $row['support'] == '0' ) {
        
            # PO_ETD
            $q_str = "SELECT `ap_det`.`po_spare` , `ap_det`.`po_eta`  , `ap_det`.`rev_eta` , 
            `ap_det`.`ap_num` , `ap_det`.`mat_cat` , `ap_det`.`mat_id` , `ap_det`.`color` , `ap_det`.`size` 
            FROM `ap`,`ap_det` 
            WHERE 
            `ap_det`.`wi_id` = '".$wi_id."' AND 
            `ap_det`.`bom_id` = '".$row['id']."' AND 
            `ap_det`.`ap_num` = `ap`.`ap_num` AND 
            `ap_det`.`mat_cat` = 'a' AND 
            `ap`.`status` = '12'
            ;";
            // echo $q_str .'<br>';
            $q_po_etd = $sql->query($q_str);
            while ($row_po_etd = $sql->fetch($q_po_etd)) {
                $po_id = $row_po_etd['po_spare'];
                $ap_num = $row_po_etd['ap_num'];
                $mat_cat = $row_po_etd['mat_cat'];
                $mat_id = $row_po_etd['mat_id'];
                $color = $row_po_etd['color'];
                $size = $row_po_etd['size'];
                
                
                # DATE
                if( $row_po_etd['po_eta'] && $row_po_etd['po_eta'] != '0000-00-00' ) $acc_po_etd[] = ( $row_po_etd['rev_eta'] > $row_po_etd['po_eta'] ) ? $row_po_etd['rev_eta'] : $row_po_etd['po_eta'];

                # PO QTY
                $check[$po_id]['PO']=1;
            }

            # SHP_ETA
            $q_str = "SELECT `po_ship`.`ship_eta`,`po_ship`.`ship_way`
            FROM `po_ship` , `po_ship_det` 
            WHERE 
            `po_ship_det`.`ap_num` = '".$ap_num."' AND `po_ship_det`.`mat_cat` = '".$mat_cat."' AND `po_ship_det`.`mat_id` = '".$mat_id."' AND `po_ship_det`.`color` = '".$color."' AND `po_ship_det`.`size` = '".$size."' AND 
            `po_ship`.`id` = `po_ship_det`.`ship_id` AND 
            `po_ship`.`status` = '2'
            ;";
            // echo $q_str .'<br>';
            $q_shp_eta = $sql->query($q_str);
            while ($row_shp_eta = $sql->fetch($q_shp_eta)) {
                $acc_ship_way = $row_shp_eta['ship_way'];
            
                # DATE
                if( $row_shp_eta['ship_eta'] ) $acc_ship_eta[] = $row_shp_eta['ship_eta'];

                # SHP QTY
                $check[$po_id]['SHP']=1;
            }

            # RCV_DATE
            $q_str = "SELECT `receive`.`rcv_cfm_date`
            FROM `receive` , `receive_det` 
            WHERE
            `receive_det`.`po_id` = '".$po_id."' AND
            `receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
            `receive`.`status` = '4'
            ;";
            // echo $q_str .'<br>';
            $q_rcv_date = $sql->query($q_str);
            while ($row_rcv_date = $sql->fetch($q_rcv_date)) {
            
                # DATE
                if( $row_rcv_date['rcv_cfm_date'] ) $acc_rcv_date[] = $row_rcv_date['rcv_cfm_date'];
                
                # RCV QTY
                $check[$po_id]['RCV']=1;
            }
            
            # 確認 BOM 採購狀態
            $check_bom++;
            $check_po += $check[$po_id]['PO'];
            $check_shp += $check[$po_id]['SHP'];
            $check_rcv += $check[$po_id]['RCV'];
        }
    }

    # 確認 BOM 採購狀態
    $check_acc_bom = ( $check_bom > 0 ) ? 1 : 0 ;
    $check_acc_po_etd = ( $check_po === $check_bom ) ? 1 : 0 ;
    $check_acc_shp_eta = ( $check_shp === $check_po ) ? 1 : 0 ;
    $check_acc_rcv_date = ( $check_rcv === $check_shp ) ? 1 : 0 ;
        
    if( $check_bom > 0 ){    
        // print_r($acc_po_etd);
        sort($acc_po_etd);
        sort($acc_ship_eta);
        sort($acc_rcv_date);
        $acc_po_etd = $acc_po_etd[0];
        $acc_ship_eta = $acc_ship_eta[0];
        $acc_rcv_date = $acc_rcv_date[0];
        
    }
    
    $acc_po_etd = ( $acc_po_etd && $acc_po_etd != '0000-00-00' ) ? "'".$acc_po_etd."'" : 'NULL';
    $acc_ship_eta = ( $acc_ship_eta && $acc_ship_eta != '0000-00-00' ) ? "'".$acc_ship_eta."'" : 'NULL';
    $acc_rcv_date = ( $acc_rcv_date && $acc_rcv_date != '0000-00-00' ) ? "'".$acc_rcv_date."'" : 'NULL';
        
    // print_r($acc_po_etd);
    // print_r($acc_ship_eta);
    // print_r($acc_rcv_date);
    // 3576
    // JAE14-0893
    $q_str = "UPDATE `pdtion` SET 
    `check_lots_po_etd` = '".($check_lots_po_etd)."' , `check_lots_shp_eta` = '".($check_lots_shp_eta)."' , `check_lots_rcv_date` = '".($check_lots_rcv_date)."' , 
    `check_acc_po_etd` = '".($check_acc_po_etd)."' , `check_acc_shp_eta` = '".($check_acc_shp_eta)."' , `check_acc_rcv_date` = '".($check_acc_rcv_date)."' , 
    `lots_po_etd` = ".($lots_po_etd)." , `lots_shp_eta` = ".($lots_ship_eta)." , `lots_rcv_date` = ".($lots_rcv_date)." , 
    `acc_po_etd` = ".($acc_po_etd)." , `acc_shp_eta` = ".($acc_ship_eta)." , `acc_rcv_date` = ".($acc_rcv_date)." , 
    `mat_ship_way` = '".($lots_ship_way)."' , `m_acc_ship_way` = '".($acc_ship_way)."'
    WHERE 
    `order_num` = '".$order_num."'
    ";
    $sql->query($q_str);
    // echo $q_str.'<br>';
   

    

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_picture($parm) {

		$sql = $this->sql;

		$j=0;
		$upFile = new uploadIMG();
		$style_dir	= $GLOBALS['config']['root_dir']."/picture/"; 
		for($i=0; $i<$parm['pic_num']; $i++)
		{
			if($parm['oth_pic_upload'][$i])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
				if(file_exists($style_dir.$parm['order_num'].$i.".jpg")){
					unlink($style_dir.$parm['order_num']."_".$i.".jpg");  // 刪除舊檔
				}
				$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  
				$upFile->setSaveTo($style_dir,$parm['order_num']."_".$i.".jpg"); //更換圖檔名稱==>"訂單編號_流水號"
				$up_result = $upFile->upload($parm['oth_pic'][$i], 600, 600); //上傳圖 600X600				
			}			
			$j++;
		}
		for($i=($parm['pic_num']-1); $i<sizeof($parm['oth_pic']); $i++)
		{
			if($parm['oth_pic_upload'][$i])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
				$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  
				$upFile->setSaveTo($style_dir,$parm['order_num']."_".$i.".jpg"); //更換圖檔名稱==>"訂單編號_流水號"
				$up_result = $upFile->upload($parm['oth_pic'][$i], 600, 600); //上傳圖 600X600
					if ($up_result){
						$this->msg->add("successful upload main picture");
					} else {
						$this->msg->add("failure upload main picutre");
					}	
					$j++;				
			}			
			
		}		
		$this->update_field_num('pic_num', $j, $parm['order_num']);

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pdtion($order_num, $factory)	抓出指定的 pdtion 記錄 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_ftycm($order_num) {

		$sql = $this->sql;

		$q_str = "SELECT id FROM exceptional  WHERE ord_num = '$order_num' AND oth_exc = 1 AND status = 4";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}
		// echo $q_str.'<br>';
		if ($row = $sql->fetch($q_result)) {
			return 1;    
		}

		$q_str = "SELECT remun.id FROM remun_det, remun  
		          WHERE remun.id = remun_det.rem_id AND ord_num = '$order_num' AND remun.status > 0";
                  // echo $q_str.'<p>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($row = $sql->fetch($q_result)) {
			return 1;    
		}		
		return 0;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_apv_ord($parm, $ord_num='')	更新 已核可後的訂單 
#			改 status=1 , etp_su, ftp_su, ets, etf 歸零
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_smpl_apv_det($smpl, $spt) {

		$sql = $this->sql;

		#####   更新資料庫內容

		$q_str = "UPDATE s_order SET smpl_apv ='".date('Y-m-d')."'  WHERE smpl_ord like '".$smpl."%' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

/*
		$q_str = "UPDATE s_order SET ie_time2 ='".$spt."'  WHERE smpl_ord like '".$smpl."%' AND ie_time2 < 1 ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}		
*/		
		return true;
	} // end func





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_apv_ord($parm, $ord_num='')	更新 已核可後的訂單 
#			改 status=1 , etp_su, ftp_su, ets, etf 歸零
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_ord_by_exc($PHP_id, $PHP_order_num,$PHP_status) {

	$sql = $this->sql;
	$po = $GLOBALS['po'];
	$apply = $GLOBALS['apply'];
	$daily = $GLOBALS['daily'];
	$shipping = $GLOBALS['shipping'];
	#####   更新資料庫內容

	$order_rec = $this->get($PHP_id);  //取出該筆記錄
	$sch_rec = $this->schedule_get($PHP_id);  //取出該筆生產相關記錄

	$del_mk = $PHP_status+1;

	$f1 = $this->update_field('status', -2, $PHP_id);
	$f1 = $this->update_field('del_mk', $del_mk, $PHP_id);
	$f1 = $this->update_field('del_apv_date', date('Y-m-d'), $PHP_id);
	$f1 = $this->update_field('del_apv_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_id);
	
	if($PHP_status == 4 || $PHP_status == 6) {		//己核可且排產未確認
		$f1 = $this->delete_month_su( $order_rec['etp'], $order_rec['etd'],$order_rec['su'],$order_rec['factory'],'pre_schedule');  //刪除ETD~ETP年度月份分配
	}elseif($PHP_status == 7){  //己排產
		$f1 = $this->delete_month_su( $order_rec['etp'], $order_rec['etd'],$order_rec['su'],$order_rec['factory'],'pre_schedule'); //刪除ETD~ETP年度月份分配

		$this->delete_month_su( $sch_rec['ets'], $sch_rec['etf'],$sch_rec['su'],$sch_rec['factory'],'schedule');	//刪除ETF~ETS年度月份分配
	}elseif($PHP_status == 8 || $PHP_status == 10 || $PHP_status == 12){	//訂單開始生產後
		$f1 = $shipping->order_ship_del($order_rec['order_num'],$order_rec['uprice']);		//刪除ship年度月份分配
		$f1 = $shipping->ship_del($order_rec['order_num']);			//刪除每日ship記錄
		$f1 = $daily->order_daily_del($order_rec['order_num']);		//刪除out-pu年度月份分配	
		$f1 = $daily->daily_del($order_rec['order_num']);			//刪除每日out-put記錄
		$f1 = $this->delete_month_su( $order_rec['etp'], $order_rec['etd'],$order_rec['su'],$order_rec['factory'],'pre_schedule');	//刪除ETD~ETP年度月份分配

		$this->delete_month_su( $sch_rec['ets'], $sch_rec['etf'],$sch_rec['su'],$sch_rec['factory'],'schedule'); //刪除ETF~ETS年度月份分配			
	}
	
	$message="APVD Delete order : ".$PHP_order_num;

	return $message;
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_schedule_get($id=0, $wi_num=0)	抓出指定記錄內資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_qc_file($order_num='') {

		$sql = $this->sql;
		$rtn_ary = array();

		// 關聯式資料庫查尋 ~~~~~~
		$q_str = "SELECT * FROM ord_qc_file WHERE ord_qc_file.ord_num='".$order_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp = explode('.',$row['file_name']);
			$s_name = explode('-',$row['file_date']);
			$row['s_name'] = $s_name[0].$s_name[1].$s_name[2].".".$tmp[1];
			$rtn_ary[] = $row;
		}
				
		return $rtn_ary;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ie_uncfm_search($mode=0, $dept='') 
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ie_uncfm_search($mode=0, $dept='') {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];
		$sch_date = increceDaysInDate(date('Y-m-d'),365);
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT DISTINCT s_order.*	FROM s_order LEFT JOIN remun_det ON remun_det.ord_num = s_order.order_num";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("status , s_order.id DESC");
		$srh->row_per_page = 20;

		$pagesize=10;
		if (!$argv['IE1_sr_startno']) $argv['IE1_sr_startno'] = 1;
		if (!$argv['IE2_sr_startno']) $argv['IE2_sr_startno'] = 1;

	
 		if($mode == 1) 	
	  {
 	 		$srh->add_where_condition("ie_time1 > 0 AND ie1 = 0");	
 	 		$pages = $srh->get_page($argv['IE1_sr_startno'],$pagesize);
 	 		$srh->add_where_condition("opendate > '2010-01-01'");	
 	 	}
 	 	if($mode == 2) 	
 	 	{
 		 	$srh->add_where_condition("ie_time2 > 0 AND ie2 = 0");
  		$pages = $srh->get_page($argv['IE2_sr_startno'],$pagesize);	
  		$srh->add_where_condition("remun_det.id IS NULL");
  		$srh->add_where_condition("opendate > '2009-10-01'");
  	}
  	
		$result= $srh->send_query2();   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$op['ord_ie'] = $result;  // 資料錄 拋入 $op
		$op['max_no'] = $srh->max_no;
		
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end

		return $op;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($field, $where_str='',$tbl='s_order') {
		$sql = $this->sql;
		$row = array();

		$q_str = "SELECT ".$field." FROM ".$tbl." ".$where_str;
        // echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		while($row = $sql->fetch($q_result))
		{
			$field_val[] = $row[0];
		}

		return $field_val;
	} // end func
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_values($field,$id='',$ord_num='', $tbl='s_order')	取出 多個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_array($field, $where_str='',$tbl='s_order') {
		$sql = $this->sql;
		$row = array();
		$q_str = "SELECT ".$field." FROM ".$tbl." ".$where_str;
		// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		while($row = $sql->fetch($q_result))
		{
			$field_val[] = $row;
		}

		return $field_val;
	} // end func
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#   ->get_partial_etd(#id)	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_partial_etd($ord_num) {

    $sql = $this->sql;
    $row = array();

    $q_str = "SELECT `p_etd` FROM `order_partial` WHERE `ord_num` = '".$ord_num."' ORDER BY `p_etd` ASC  LIMIT 0 , 1 ;";
    // echo $q_str."<BR>";
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
        
    if($row = $sql->fetch($q_result))
    {
        return $row['p_etd'];
    }

    return false;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_partial($id) {
		$sql = $this->sql;
		$row = array();

		$q_str = "SELECT * FROM order_partial WHERE id=".$id;
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		if($row = $sql->fetch($q_result))
		{
			return $row;
		}

		return false;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_pdtion($id)		刪除   [由ID]刪除
#							資料表 : pdtion
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function dle_partial($id,$ord_num) {

    $sql = $this->sql;
    
    if (!$id) {
        $this->msg->add("Error ! please specify order number for pdtion.");		    
        return false;
    }
    $q_str = "DELETE FROM order_partial WHERE id='$id' ";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access !");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    $ord_id = $this->get_field_value('id','',$ord_num,'s_order');
    $p_num = $this->get_field_value('partial_num','',$ord_num,'s_order');
    
    if($p_num > 1){
        $this->update_field("partial_num", $p_num-1, $ord_id);
    }
    
    return true;
    
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_partial($field, $val, $id) 2006-12-19更新 s_order資料記錄內 某個單一欄位 part2
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_partial($field, $val, $id) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE order_partial SET ".$field."='".$val."'  WHERE id='".$id."'";
    // echo $q_str."<BR>";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

    
        
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_saw_partial_qty($field,$id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_saw_partial_qty($field, $id='') {

    $sql = $this->sql;
    $tbl = 'saw_out_put';
    
    $q_str = "SELECT ".$field." FROM ".$tbl." WHERE `p_id` = '".$id."' ";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }

    $row = $sql->fetch($q_result);

    $field_val = $row[0];

    return $field_val;
} // end func


    
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->set_p_qty_done($p_id,$nowQty)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function set_p_qty_done($p_id,$nowQty){

    $sql = $this->sql;

    $q_str =  "UPDATE `order_partial` SET `p_qty_done` = '".$nowQty."' WHERE id = '".$p_id."'";
    $sql->query($q_str);

}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_distri_su($ord_num,$from_fd,$to_fd) {
		$sql = $this->sql;
		$row = array();

		$q_str = "SELECT ".$from_fd." FROM order_partial WHERE ord_num='".$ord_num."'"; //搜

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		while($row = $sql->fetch($q_result))  //將年月和SU分開並將年月做為array的key
		{
			$su_dis = explode(',',$row[0]);
			for($i=0; $i<sizeof($su_dis); $i++)
			{
				if(!isset($field_val[substr($su_dis[$i],0,6)]))$field_val[substr($su_dis[$i],0,6)] = 0;
				$field_val[substr($su_dis[$i],0,6)] += substr($su_dis[$i],6);
			}
		}
		$su_str = '';
		foreach($field_val as $key	=> $value)//組合訂單su分配
		{
			$su_str .= $key.$value.',';
		}
		$su_str = substr($su_str,0,-1);

		$q_str = "UPDATE pdtion SET ".$to_fd."='".$su_str."'  WHERE order_num='".$ord_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $su_str;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->finish_order($id,$out_date,$mode=0)
#		order_partial ID , 日期, 處理模式(0 : finish; 2 : shipping)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function finish_order($id,$out_date,$mode=0) {

		$sql = $this->sql;
		$ptl_rec = $this->get_partial($id);
		// if($ptl_rec['p_qty_shp'] > 0 && $ptl_rec['pdt_status'] == 2)
		if($ptl_rec['p_qty_shp'] > 0 )
			$this->update_partial('pdt_status', 4, $id);
		else
			$this->update_partial('pdt_status', 2, $id);
		
		$q_str = "SELECT min(pdt_status) as status FROM order_partial WHERE ord_num = '".$ptl_rec['ord_num']."'";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		
		$pdt_id = $this->get_field_value('id', '', $ptl_rec['ord_num'],'pdtion'); //取得ptiono的ID

		if($row['status'] == 2)
		{		
			$this->update_field_num('status',10,$ptl_rec['ord_num']);  //修改status的狀態						
		}else if($row['status'] == 4){
			$this->update_field_num('status',12,$ptl_rec['ord_num']);  //修改status的狀態							
		}
		if($mode == 0) $this->update_pdtion_field('finish',$out_date,$pdt_id); //填入finish的日期
		
		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_cost($order) status
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_cost($id) {

	$sql = $this->sql;
// ref  patt_num  ptn_upload  paper_ptn  factory  mat_u_cost  mat_useage  acc_u_cost  quota  quota_fee  comm_fee  smpl_fee  emb  wash  oth  oth_treat  cm  cfmer  cfm_date  smpl_apv  smpl_apv2  smpl_apv3  smpl_apv_type  smpl_apv_type2  smpl_apv_type3  apver  apv_date  schd_date  schd_er  etp  gmr  last_updator  last_update  status  revise  remark  size  marked  m_status  agent  fusible  interline  del_mk  del_date  del_user  del_cfm_date  del_apv_date  fty_cm  line_sex  lots_unit  pic_num  cust_po  marker_date  rel_mat_cost  rel_acc_cost  rel_cm_cost  po_mat_cost  po_acc_cost  quota_unit  content  des  ship_quota  ship_fob  fty_marker_date  close_status  close_des  lots_chk  org_etd  ship_way  ship_dist  fab_type  ship_day  season  syear  partial_num 
	$q_str = "
	SELECT 
	`s_order`.`id` , `s_order`.`order_num` , `s_order`.`apv_date` , 
	`s_order`.`style_num` , `s_order`.`cust` , `s_order`.`ref` , `s_order`.`dept` , 
	`s_order`.`factory` , `s_order`.`style` , `s_order`.`qty` , `s_order`.`su` , 
	`s_order`.`uprice` , `s_order`.`quota` , `s_order`.`smpl_ord` , `s_order`.`size` , 
	`s_order`.`etd` , `s_order`.`etp` , `s_order`.`ie1` , `s_order`.`ie2` , `s_order`.`status` , 
	
	
	`s_order`.`mat_useage` , `s_order`.`mat_u_cost` , `s_order`.`fusible` , `s_order`.`interline` , 
	`s_order`.`acc_u_cost` , 
	`s_order`.`emb` , `s_order`.`wash` , `s_order`.`oth` , 
	`s_order`.`rel_cm_cost` , 
	`s_order`.`cm` , `s_order`.`fty_cm` , 
	`s_order`.`quota_fee` , `s_order`.`comm_fee` , `s_order`.`ship_fob` , `s_order`.`comm_fee` , `s_order`.`smpl_fee` , `s_order`.`handling_fee` ,  `s_order`.`opendate` , 
	
	`cust`.`cust_init_name` as `cust_iname` , 
	`wi`.`id` as `wi_id` , `wi`.`bcfm_date`
	
	FROM 
	`s_order` , `cust` , `wi`
	
	WHERE 
	`s_order`.`id` = '".$id."' AND 
	`s_order`.`order_num` = `wi`.`wi_num` AND 
	`s_order`.`cust` = `cust`.`cust_s_name` AND 
	`s_order`.`cust_ver` = `cust`.`ver`
	";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	if (!$row = $sql->fetch($q_result)) {
		// echo $q_str;
		// $this->msg->add("Error ! Can't find this record!");
		return false;    
	}

	return $row;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ac_cost($order) status
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ac_cost($order_num) {

	$sql = $this->sql;
	$row=array();
	$q_str = "select * from ac_cost where order_num='".$order_num."'";
	$no_data=false;
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	if (!$row = $sql->fetch($q_result)) {
		// echo $q_str;
		// $this->msg->add("Error ! Can't find this record!");
		$no_data=true;
		/* echo "no data1";	
		return false;     */
	}
	if($no_data)
	{
		$row['rate']="";
		$row['order_num']="";
		$row['qty']="";
		$row['fob']="";
		$row['fab']="";
		$row['acc']="";
		$row['special']="";
		$row['cm']="";
		$row['other']="";
		$row['desc']="";
	}


	//print_r($row);
	return $row;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_output($order_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_output($order_num) {

	$sql = $this->sql;		
	$q_str = "SELECT sum(saw_out_put.qty) as qty FROM saw_out_put WHERE `ord_num` = '".$order_num."';";
// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot access database, pls try later !");
		$this->msg->merge($sql->msg);
		return false;    
	}
    
	if (!$row = $sql->fetch($q_result)) {
		return false;
	}

	return $row['qty'];
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_shipping($order_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_shipping($order_num) {

	$sql = $this->sql;		
	$q_str = "SELECT sum(ttl_qty) as qty FROM `shipping_doc_qty` WHERE `ord_num` = '".$order_num."';";
// echo $q_str."<br>";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! cannot access database, pls try later !");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$row = $sql->fetch($q_result)) {
		return false;
		
	}

	return $row['qty'];
} 


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->Insert ac_cost($parm[])		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function creat_ac_cost($q_str) {
	
		$sql = $this->sql;
		

					# 加入資料庫
		
		
		
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update new record.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		//$ord_id = $sql->insert_id();  //取出 新的 id

		return true;

	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->count_order_ptn_num($ord_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_ptn_num($ord_id) {
		$sql = $this->sql;
		
		$q_str = "select file_name 
				  from ord_ptn_file 
				  where ord_id=".$ord_id." order by file_name desc limit 1";

		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$num = 1;
		if(!$row){
			$num = 1;
		}else{
			$file_ary = explode("-",$row['file_name']);
			if($file_ary[2]){
				$tmp = $file_ary[2];
				settype($tmp, 'integer');
				$num = $tmp+1;
			}else{
				$num = 1;
			}
		}
		return $num;

	} // end func
    
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_order_ptn_num_ext($ord_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_ptn_num_ext($ord_id) {
		$sql = $this->sql;
		
		$q_str = "select file_name 
				  from ord_ptn_file 
				  where ord_id=".$ord_id." order by file_name desc limit 1";

		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$num = 1;
		if(!$row){
			$num = 1;
		}else{
            $file_ary = explode(".",$row['file_name']);
			$file_ary = explode("-",$file_ary[0]);
			if($file_ary[2]){
				$tmp = $file_ary[2];
				settype($tmp, 'integer');
				$num = $tmp+1;
			}else{
				$num = 1;
			}
		}
		return $num;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_pttn($parm)		加入 新的 pdtion 訂單記錄 [ 寫入工廠 及 etp_su 排產數]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_pttn($parm) {
		$sql = $this->sql;

		$q_str = "INSERT INTO ord_ptn_file (ord_id,file_name,file_des,file_user,file_date) VALUES('".
					$parm['ord_id']."','".
					$parm['file_name']."','".
					$parm['file_des']."','".
					$parm['file_user']."','".
					$parm['file_date']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update new record.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->file_del($id)	刪除ord_ptn_file檔案
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function file_del($id) {
	$sql = $this->sql;
 	$q_str="DELETE FROM ord_ptn_file WHERE id='".$id."'";
 	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}     
	return true;
}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_ord_ptn($ord_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_ord_ptn($ord_id){
	$sql = $this->sql;		
	$q_str = "SELECT file_date
			  FROM ord_ptn_file
			  WHERE ord_id = ".$ord_id."
			  order by file_date desc limit 0,1";

	if (!$q_result = $sql->query($q_str)) {
		return false;    
	}
	$row = $sql->fetch($q_result);
	
	$q_str = "update s_order
			  set ptn_upload = '".$row['file_date']."'
			  where id = ".$ord_id;
	$q_result = $sql->query($q_str);
	
	return true;
}




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_final_ie($id='',$order_num='')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_final_ie($id='',$order_num=''){

$sql = $this->sql;

if($id)
    $q_str = "SELECT `ie1`,`ie2` FROM `s_order` WHERE `id` = ".$id.";";
else 
    $q_str = "SELECT `ie1`,`ie2` FROM `s_order` WHERE `order_num` = '".$order_num."';";

// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    return false;    
}

$row = $sql->fetch($q_result);

return $row['ie2'] > 0 ? $row['ie2'] : $row['ie1'] ;

}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->remove_order($ord_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function remove_order($ord,$mks){
	$sql = $this->sql;		

	$q_str = "update `s_order` set `status` = '12' where `order_num` = '".$ord."' ;";
	if (!$q_result = $sql->query($q_str)) {
		return 'no';    
	}
    
	$q_str = "update `order_partial` set `pdt_status` = '4' where `ord_num` = '".$ord."' AND `mks` = '".$mks."' ;";
	if (!$q_result = $sql->query($q_str)) {
		return 'no';    
	}
	
	return 'yes';
}



} // end class


?>