<?php 

#++++++++++++++++++++++ LOTS  class ##### 主料  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						Search   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class LOTS {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Can't access database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
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
			$tmp=explode('-',$buy_no);
			$buy_no=$tmp[0].$tmp[1];
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//在數字前補0到達四位數字
		{
			$buy_no=$hend."000-00".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$hend."000-0".$buy_no;
		}else if(strlen($buy_no) == 3){
			$buy_no=$hend."000-".$buy_no;			
		}else if(strlen($buy_no) == 4){
			$buy_no=substr($buy_no,0,1)."-".substr($buy_no,1);
	    $buy_no=$hend."00".$buy_no;
	  }else if(strlen($buy_no) == 5){
	  	$buy_no=substr($buy_no,0,2)."-".substr($buy_no,2);
	  	$buy_no=$hend."0".$buy_no;
	  }else{
			$buy_no=substr($buy_no,0,3)."-".substr($buy_no,3);
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 主料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
		$back=0;

			############### 檢查輸入項目	
			//  改成大寫
//		$parm['lots_code'] = strtoupper($parm['lots_code']);	

//		if (!$parm['lots_code'] = trim($parm['lots_code'])) {
//			$this->msg->add("Error ! Please input Fabric [code] !");
//		    return false;
//		}
		
		if (!$parm['lots_name'] = trim($parm['lots_name'])) {
			$this->msg->add("Error ! Please input Fabric Name !");
			$back=1;
		}
		
		if (!$parm['cat1']) {
			$this->msg->add("Error ! Please choose Fabric kind !");
			$back=1;
		}
		
		if (!$parm['cat2']) {
			$this->msg->add("Error ! Please choose Fabric kind !");		   
			$back=1; 
		}
		if ($back==1)
		{
			return false;
		}
		$parm['width'] 	 = str_replace("'","\'",$parm['width']);
		$parm['weight']  = str_replace("'","\'",$parm['weight']);
		$parm['des'] 		 = str_replace("'","\'",$parm['des']);
		$parm['comp'] 	 = str_replace("'","\'",$parm['comp']);
		$parm['specify'] = str_replace("'","\'",$parm['specify']);
		$parm['cons'] 	 = str_replace("'","\'",$parm['cons']);

	
					# 檢查是否有重覆
/*
		$q_str = "SELECT id FROM lots WHERE lots_code='".$parm['lots_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This fabric code is exist in database, please change another ones!");
			return false;    	    
		}
*/					


//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['custer']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	
#M11021501 增加 kind 的單位
					# 加入資料庫
		$q_str = "INSERT INTO lots (lots_code,
									lots_name,
									des,
									comp,
									unit,
									specify,
									custer,
									cust_ver,
									
									width,
									weight,
									cons,				
									memo,														
									
									vendor1,									
									price1,
									unit1,
									currency1,
									term1,
									
									vendor2,									
									price2,
									unit2,
									currency2,
									term2,
									
									vendor3,									
									price3,
									unit3,
									currency3,
									term3
									) VALUES('".
									$parm['lots_code']."','".
									$parm['lots_name']."','".
									$parm['des']."','".
									$parm['comp']."','".
									$parm['unit']."','".
									$parm['specify']."','".
									$parm['custer']."','".
									$cust_row['ver']."','".
															
									$parm['width']."','".
									$parm['weight']."','".
									$parm['cons']."','".
									$parm['memo']."','".
																											
									$parm['vendor1']."','".									
									$parm['price1']."','".
									$parm['unit1']."','".
									$parm['currency1']."','".
									$parm['term1']."','".
									
																		
									$parm['vendor2']."','".									
									$parm['price2']."','".
									$parm['unit2']."','".
									$parm['currency2']."','".
									$parm['term2']."','".									
									$parm['vendor3']."','".									
									$parm['price3']."','".
									$parm['unit3']."','".	
									$parm['currency3']."','".									
									$parm['term3']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增主料記錄: [".$parm['lots_code']."]。") ;

		return $new_id;

	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 主料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_parts($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	
			//  改成大寫
		$parm['num'] = strtoupper($parm['num']);	

		if (!$parm['num'] = trim($parm['num'])) {
			$this->msg->add("Error ! Please input Fabric [code] !");
		    return false;
		}

					# 檢查是否有重覆
		$q_str = "SELECT id FROM lots WHERE lots_code='".$parm['num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This fabric code is exist in database, please change another ones!");
			return false;    	    
		}
					
					# 加入資料庫
		$q_str = "INSERT INTO lots (lots_code,
									lots_name,																		
																		
									unit1
									) VALUES('".
									$parm['num']."','".
									$parm['name']."','".															
									$parm['unit']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增主料記錄: [".$parm['lots_code']."]。") ;

		return $new_id;

	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	Search  主料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0) {

		$sql = $this->sql;
		if($mode == 2)
		{
			$argv = $_SESSION['sch_parm'];
		}else{
			$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		}
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM lots";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		if(isset($argv['SCH_sort']))$SCH_sort =  ($argv['SCH_ud'] > 0) ? $argv['SCH_sort'] ."" : $argv['SCH_sort'] ." DESC";
		if(isset($argv['SCH_sort']))$srh->add_sort_condition($SCH_sort);
		
//2006.11.14 以數字型式顯示頁碼 star		
		$srh->row_per_page = 12;
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 以數字型式顯示頁碼 end  

if ($mode > 0){    //有條件的Search時
		$mesg = '';
		if ($str = $argv['SCH_lots_code'] )  { 
			$srh->add_where_condition("lots_code LIKE  '%$str%'", "SCH_lots_code",$str); 
			$mesg.=" Fabric#  : [ $str ].";
		}
		if (isset($argv['SCH_code']) && $str = $argv['SCH_code'] )  { 			
			$srh->add_where_condition("lots_code LIKE  '%$str%'", "SCH_lots_code",$str); 
			$mesg.= " Category : [ $str ]. ";
		}
		if (isset($argv['SCH_mile']) && $str = $argv['SCH_mile'] )  { 			
			$srh->add_where_condition("des LIKE  '%$str%'", "SCH_lots_code",$str); 
			$mesg.= "  Supplier's code : [ $str ]. ";
		}
		if (isset($argv['SCH_cons']) && $str = $argv['SCH_cons'] )  { 			
			$srh->add_where_condition("specify LIKE  '%$str%'", "SCH_lots_code",$str); 
			$mesg.= "  Content: [ $str ]. ";
		}
		
		if ($str = $argv['SCH_lots_name'] )  { 
			$srh->add_where_condition("lots_name LIKE  '%$str%'", "SCH_lots_name",$str); 
			$mesg.= " Name : [ $str ]. ";
		}

		if ($str = $argv['SCH_comp'] )  { 
			$srh->add_where_condition("comp LIKE '%$str%'", "SCH_comp",$str); 
			$mesg.= " Construction : [ $str ]. ";
		}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}		
}

		#M11021502 修改 List 排序
		$srh->add_sort_condition("`id` DESC ");
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['lots'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
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
#	->get($id=0, $lots_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $lots_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM lots WHERE id='$id' ";
		} elseif ($lots_code) {
			$q_str = "SELECT * FROM lots WHERE lots_code='$lots_code' ";
		} else {
			$this->msg->add("Error ! Please point fabric ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 主料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {
	
		$sql = $this->sql;
			
		
		#####   更新資料庫內容
		
		$parm['width'] 	 = str_replace("'","\'",$parm['width']);
		$parm['weight']  = str_replace("'","\'",$parm['weight']);
		$parm['des'] 		 = str_replace("'","\'",$parm['des']);
		$parm['comp'] 	 = str_replace("'","\'",$parm['comp']);
		$parm['specify'] = str_replace("'","\'",$parm['specify']);
		$parm['cons'] 	 = str_replace("'","\'",$parm['cons']);
		
//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['custer']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);			
		
		#M11021501 增加 kind 的單位
		$q_str = "UPDATE lots SET lots_name='"	.$parm['lots_name'].
							"', des='"				.$parm['des'].
							"', comp='"				.$parm['comp'].
							"', unit='"				.$parm['unit'].
							"', specify='"		.$parm['specify'].	
							"', custer='"			.$parm['custer'].
							"', cust_ver='"		.$cust_row['ver'].
							
							"', width='"			.$parm['width'].
							"', weight='"			.$parm['weight'].
							"', cons='"				.$parm['cons'].	
							"', memo='"				.$parm['memo'].						
							
							"', vendor1='"		.$parm['vendor1'].							
							"', price1='"			.$parm['price1'].
							"', unit1='"			.$parm['unit1'].
							"', currency1='"	.$parm['currency1'].
							"', term1='"			.$parm['term1'].							
							"', vendor2='"		.$parm['vendor2'].							
							"', price2='"			.$parm['price2'].
							"', unit2='"			.$parm['unit2'].
							"', currency2='"	.$parm['currency2'].
							"', term2='"			.$parm['term2'].							
							"', vendor3='"		.$parm['vendor3'].							
							"', price3='"			.$parm['price3'].
							"', unit3='"			.$parm['unit3'].
							"', currency3='"	.$parm['currency3'].	 
							"', term3='"			.$parm['term3'].		
						"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$lots=$this->get($parm['id']);
		$q_str= "Update smpl_lots_use SET lots_name = '".$parm['lots_name']."' WHERE lots_code = '".$lots['lots_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$q_str= "Update lots_use SET lots_name = '".$parm['lots_name']."' WHERE lots_code = '".$lots['lots_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}


		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 主料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE lots SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 主料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_code($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE lots SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE lots_code='".$parm['code']."'";
		// echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 主料記錄  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point fabric ID.");		    
			return false;
		}
		$q_str = "DELETE FROM lots WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
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
		$q_str = "SELECT ".$n_field." FROM lots ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access database!");
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