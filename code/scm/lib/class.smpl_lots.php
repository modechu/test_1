<?php 

#++++++++++++++++++++++ SMPL_LOTS  class ##### 樣本 主料 +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->check($parm)		檢查 加入新 樣本記錄 是否正確
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL_LOTS {
		
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
#	->check($parm)		檢查 加入新 樣本記錄 是否正確
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目
		$parm['num'] = strtoupper($parm['num']);	
		if (!$parm['cust'] = trim($parm['cust'])) {
			$this->msg->add("Error ! 請輸入客戶別 。");
		    return false;
		}
		if (!$parm['year'] = trim($parm['year'])) {
			$this->msg->add("Error ! 請輸入年份 。");
		    return false;
		}
												
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 樣本 主料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目
			
		$parm['num'] = strtoupper($parm['num']);
		if (!$parm['num'] = trim($parm['num'])) {
			$this->msg->add("Error ! Please input fabric # 。");
		    return false;
		}
		$q_str = "SELECT id FROM lots WHERE lots_code='".$parm['num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This fabric code is not exist in database!");
			return false;    	    
		}
		if ($parm['unit'] == '' ) {
			$this->msg->add("Error ! Please choice unit 。");
		    return false;
		}
/*		
		if (!is_numeric($parm['est'] ) && !$parm['est'] == '') {
			$this->msg->add("Error ! Please input currect ETS unit cost。");
			return false;
		}
*/
		if ($parm['fab_type'] == '' ) {
			$this->msg->add("Error ! Please choice fabric type first 。");
		    return false;
		}	
		if($parm['use_for']) 
		{
			$parm['use_for'] = $parm['fab_type'].":".$parm['use_for'];
		}else{
			$parm['use_for'] = $parm['fab_type'];
		}
		
		$parm['use_for'] = str_replace("'","\'",$parm['use_for']);	
		$parm['use_for']  = str_replace("&#33031;","脅",$parm['use_for'] );
		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
			$parm['use_for'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['use_for']);
					# 加入資料庫
		$q_str = "INSERT INTO lots_use (smpl_code,										
										lots_code,
										unit,
										color,
										lots_name,
										support,
										user,
										add_date,
										use_for) VALUES('".
									$parm['smpl_code']."','".									
									$parm['num']."','".
									$parm['unit']."','".
									$parm['color']."','".
									$parm['name']."','".
									$parm['support']."','".
									$parm['user']."','".
									$parm['add_date']."','".
									$parm['use_for']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$id = $sql->insert_id();  //取出 新的 id
		return $id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	搜尋 樣本 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $where_str="") {
		$sql = $this->sql;
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		if ($where_str)
		{
			$q_header = "SELECT lots_use.*, lots.comp, lots.width, lots.weight, lots.cons, lots.des, lots.specify, lots.vendor1, lots.price1 FROM lots_use, lots ".$where_str." and lots_use.lots_code=lots.lots_code";
		}else{
			$q_header = "SELECT lots_use.*, lots.comp, lots.width, lots.weight, lots.cons, lots.des, lots.specify, lots.vendor1, lots.price1 FROM lots_use, lots Where lots_use.lots_code=lots.lots_code";
		}
        
		// echo $q_header;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("id");
		$srh->row_per_page = 200;
	
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$lots_use = $result;  // 資料錄 拋入 $op
		
		for ($i=0; $i<sizeof($lots_use); $i++) $lots_use[$i]['i'] = $i;
		
		return $lots_use;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_lots()
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_lots($mode=0, $where_str="") {

    $sql = $this->sql;
    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    if ($where_str)
    {
        $q_header = "SELECT lots_use.*,bom_lots.*, lots.comp, lots.width, lots.weight, lots.cons, lots.des, lots.specify, lots.vendor1, lots.price1 FROM bom_lots,lots_use, lots ".$where_str." and bom_lots.lots_used_id=lots_use.id and lots_use.lots_code=lots.lots_code";
    }else{
        $q_header = "SELECT lots_use.*,bom_lots.*, lots.comp, lots.width, lots.weight, lots.cons, lots.des, lots.specify, lots.vendor1, lots.price1 FROM bom_lots,lots_use, lots Where lots_use.lots_code=lots.lots_code and bom_lots.lots_used_id=lots_use.id";
    }
    
    // echo $q_header;
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_sort_condition("bom_lots.id");
    $srh->row_per_page = 200;

    $result= $srh->send_query2();
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);
        if (!$result){   // 當查尋無資料時
            $op['record_NONE'] = 1;
        }

    $lots_use = $result;  // 資料錄 拋入 $op
    
    for ($i=0; $i<sizeof($lots_use); $i++) $lots_use[$i]['i'] = $i;
    
    return $lots_use;
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $smpl_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $smpl_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM lots_use WHERE id='$id' ";
		} elseif ($smpl_code) {
			$q_str = "SELECT * FROM lots_use WHERE smpl_code='$style_code' ";
		} else {
			$this->msg->add("Error ! 請指明 主料資料在資料庫內的 ID.");		    
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
#	->get($id=0, $smpl_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_eta($smpl_code) {

		$sql = $this->sql;

		$q_str=" SELECT lots_use.smpl_code, apply_det. *, lots_use.id as lid FROM apply_det, bom_lots, lots_use".
			   " WHERE apply_det.aply_num = bom_lots.ap_mark AND bom_lots.lots_used_id = lots_use.id 
			   AND lots_code = apply_det.aply_mat AND lots_use.smpl_code = '".$smpl_code."' order by eta";
			   

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access DB!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			   $lots_eta[]=$row;
		}
		if (isset($lots_eta))
		{
			return $lots_eta;
		}else{
			return false;
		}
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 主料使用 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		$parm[2] = str_replace("'","\'",$parm[2]);	
		$parm[2]  = str_replace("&#33031;","脅",$parm[2] );
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
			$parm[2] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm[2]);
		#####   更新資料庫內容
	$q_str = "UPDATE lots_use SET ".$parm[1]."='".$parm[2]."'  WHERE id=".$parm[0]." ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		刪除 樣本主料記錄  [由ID]刪除
#							$mode=0: $id= 記錄之id; $mode<>0: $id=樣本編號smpl_code
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! 請指明 樣本主料 資料表的 ID 或 樣本代號 .");		    
			return false;
		}

		if($mode){
			$q_str = "DELETE FROM lots_use WHERE smpl_code='$id' ";
		}else{
			$q_str = "DELETE FROM lots_use WHERE id='$id' ";
		}

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
		$q_str = "SELECT ".$n_field." FROM lots_use ".$where_str;

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
#	->get($id=0, $smpl_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_po($id,$state='') {

	$sql = $this->sql;

	$q_str=" SELECT id , ap_mark FROM bom_lots WHERE lots_used_id = $id ;";
		   
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Can't access DB!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	while ($row = $sql->fetch($q_result)) {
		if($state == 'po')
		{
			if($row['ap_mark']) return 1;
		}else{
			return 1;
		}
	}
	
	return 0;
	
} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>