<?php 

#++++++++++++++++++++++ CUST  class ##### 客戶  +++++++++++++++++++++++++++++++++++
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

class CUST {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function init($sql) {
	$this->msg = new MSG_HANDLE();

	if (!$sql) {
		$this->msg->add("Error ! Can't connect database.");
		return false;
	}
	$this->sql = $sql;
	return true;
} // end func


function get_cust_name( $cust = '' , $ver = '' ) {
    $sql = $this->sql;
    
    $q_str = "SELECT `cust_f_name` FROM `cust` WHERE `cust`.`cust_s_name` ='".$cust."' AND `cust`.`ver` ='".$ver."' ";
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    if (!$rows = $sql->fetch($q_result)) {
        $this->msg->add("Error ! this record can't be find!");
        return false;    
    }
    
	return $rows[0];
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $cust_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_consignee( $id = '' , $cust = '' ) {

	$sql = $this->sql;

	if ($id) {
		$q_str = "SELECT consignee.id , consignee.f_name FROM consignee WHERE consignee.id='$id' ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$rows = $sql->fetch($q_result)) {
			$this->msg->add("Error ! this record can't be find!");
			return false;    
		}
	} elseif ($cust) {
		$q_str = "SELECT consignee.id , consignee.f_name FROM consignee WHERE consignee.cust = '$cust' ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$rows = array();
		while ($row = $sql->fetch($q_result)) {
			$rows[$row['id']] = $row['f_name'];
		}

	} else {
		$this->msg->add("Error ! please specify customer number.");		    
		return false;
	}
	
	return $rows;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 USER
#		加入新 USER			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	
			//  改成大寫
		$parm['country'] = strtoupper($parm['country']);	
		$parm['cust_s_name'] = strtoupper($parm['cust_s_name']);	

		if (!$parm['dept_code'] = trim($parm['dept_code'])) {
			$this->msg->add("Error ! Please input dept. # !");
		    return false;
		}
		if (strlen($parm['cust_s_name']) > 2) {
			$this->msg->add("Error ! Customer for sont limit 2 letters");
		    return false;
		}
		if (!$parm['cust_s_name'] = trim($parm['cust_s_name'])) {
			$this->msg->add("Error ! Please input customer code !");
		    return false;
		}
		if (!$parm['cust_init_name'] = trim($parm['cust_init_name'])) {
			$this->msg->add("Error ! Please input customer short name!");
		    return false;
		}


					# 檢查是否有重覆
		$q_str = "SELECT id FROM cust WHERE cust_s_name='".$parm['cust_s_name']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! this customer code is exist in database，please change short name or check data again!");
			return false;    	    
		}
	

/*		
		$q_str = "SELECT id FROM cust WHERE cust_init_name='".$parm['cust_init_name']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! this customer short namde is exist in database，please change short name or check data again!");
			return false;    	    
		}
*/		

//查詢最後版本 並增一版
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust_s_name']."' ORDER BY ver DESC LIMIT 1";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($row = $sql->fetch($q_result)) {

			$ver = $row['ver'] + 1;
			if($ver < 10){
				$ver = '00'.$ver;
			}else if($ver < 100){
				$ver = '0'.$ver;
			}
		}else{
			$ver = '001';
		}

			
					# 加入資料庫
		$q_str = "INSERT INTO cust (dept, cust_s_name, cust_f_name, ver, cntc_addr, cntc_phone, cntc_fax, cntc_person1, cntc_cell1, email1, cntc_person2, cntc_cell2, email2, country, agent, cust_init_name) 
							VALUES ('".strtoupper($parm['dept_code']).
							"','".strtoupper($parm['cust_s_name']).
							"','".strtoupper($parm['cust_f_name']).
							"','".strtoupper($ver).
							"','".strtoupper($parm['cntc_addr']).
							"','".$parm['cntc_phone'].
							"','".$parm['cntc_fax'].
							"','".$parm['cntc_person1'].
							"','".$parm['cntc_cell1'].
							"','".$parm['email1'].
							"','".$parm['cntc_person2'].
							"','".$parm['cntc_cell2'].
							"','".$parm['email2'].
							"','".strtoupper($parm['country']).
							"','".strtoupper($parm['agent']).
							"','".strtoupper($parm['cust_init_name'])."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't add new record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增客戶 : [".$parm['login_id']."]。") ;

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	搜尋  USER 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $where_str="", $per_page=0) {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM cust ".$where_str;
		if ($mode==2) $q_header = "SELECT * FROM cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$SCH_sort =  ($argv['SCH_ud'] > 0) ? $argv['SCH_sort'] ."" : $argv['SCH_sort'] ." DESC";
		$srh->add_sort_condition($SCH_sort);
		if ($per_page == 0)
		{
			$srh->row_per_page = 12;
		}else{
			$srh->row_per_page = $per_page;
		}

//2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 

//print_r($argv);
if ($mode==2){    //有條件的Search時
		if ($str = $argv['SCH_dept'] )  { 
			$srh->add_where_condition("cust.dept =  '$str'", "SCH_dept",$str,"DEPT = [ $str ]. "); }
		
		if ($str = $argv['SCH_cust'] )  { 
			$srh->add_where_condition("cust.cust_s_name like '%$str%'", "SCH_line",$str,"Cust.: [ $str ]. "); }
		if ($str = $argv['SCH_name'] )  { 
			$srh->add_where_condition("cust.cust_init_name like '%$str%'", "SCH_ord",$str,"Short Name : [ $str ]. "); }
		if ($str = $argv['PHP_letter'] )   //add
			$srh->add_where_condition("cust.cust_s_name like '$str%'", "",$str,"CUSTOMER : [ $str* ]. ");
		elseif($argv['PHP_letter']=='0') //數字0會被判斷成false
			$srh->add_where_condition("cust.cust_s_name like '0%'", "",$str,"CUSTOMER : [ $str* ]. ");
		
}    	

  	//$srh->add_where_condition("cust.active= y");  	
 //2006.11.14 以數字型式顯示頁碼 end 
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			} else {
				$op['record_NONE'] = "";
			}

		$op['cust'] = $result;  // 資料錄 拋入 $op
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

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $cust_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $cust_s_name=0,$ver='001') {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM cust WHERE id='$id' ";
		} elseif ($cust_s_name) {
			$q_str = "SELECT * FROM cust WHERE cust_s_name='$cust_s_name' AND ver ='".$ver."'";
		} else {
			$this->msg->add("Error ! please specify customer number.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! this record can't be find!");
			return false;    
		}else{
			$CUST_DEL =  $_SESSION['CUST_DEL'] * -1;
			$q_str = "SELECT max(opendate) as op_date FROM s_order WHERE cust ='".$row['cust_s_name']."' GROUP BY cust";
		
			$q_result = $sql->query($q_str);
			if ($ord_row = $sql->fetch($q_result)) {
				
				$del_date = increceDaysInDate(date('Y-m-d'),$CUST_DEL);
				$row['ord_date'] = $ord_row['op_date'];
				if($del_date < $ord_row['op_date']){$row['del_mk'] = 0;}else{$row['del_mk'] = 1;}
			
			}else{
				$row['del_mk'] = 2;
				$row['ord_date'] = '9999-99-99';
			}
		}
		
		
		
		
		return $row;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 USER 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		
		if (!$parm['cust_init_name'] = trim($parm['cust_init_name'])) { //檢查客戶簡稱是否存在
			$this->msg->add("Error ! Please input customer short name!");
		    return false;
		}

		#####   更新資料庫內容
		$q_str = "UPDATE cust SET dept='"			.strtoupper($parm['dept_code']).
						"', country='"				.strtoupper($parm['country']).
						"', cust_f_name='"			.strtoupper($parm['cust_f_name']).
						"', cust_init_name='"		.strtoupper($parm['cust_init_name']).
						"', agent='"				.strtoupper($parm['agent']).
						"', cntc_phone='"			.$parm['cntc_phone'].
						"', cntc_addr='"			.strtoupper($parm['cntc_addr']).
						"', cntc_person1='"			.$parm['cntc_person1'].
						"', cntc_cell1='"			.$parm['cntc_cell1'].
						"', email1='"				.$parm['email1'].
						"', cntc_person2='"			.$parm['cntc_person2'].
						"', cntc_cell2='"			.$parm['cntc_cell2'].
						"', email2='"				.$parm['email2'].
						"', uni_no='"				.$parm['uni_no'].
						"'  WHERE id='"				.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 CUST 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE cust SET ".$parm['field_name']."='".strtoupper($parm['field_value'])."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 CUST 資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! please specify customer number");		    
			return false;
		}
//		$q_str = "DELETE FROM cust WHERE id='$id' ";
			$q_str = "UPDATE cust SET active='n',
																cust_del_user = '".$GLOBALS['SCACHE']['ADMIN']['login_id']."',
																cust_del_date = '".date('Y-m-d')."'
								WHERE id='".$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access !");
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

    //去除舊版本客戶
    $order_by = substr($where_str,' order by ');
    $where_str = str_replace($order_by,'',$where_str);
    if($where_str == ''){$where_str = " WHERE active = 'y' ";}else{$where_str .= " AND active = 'y' ";}
    $where_str .= $order_by;
    
    $q_str = "SELECT ".$n_field." FROM cust ".$where_str;
    // echo $q_str;
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! Database can't access!");
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
#	->add($parm)		加入新 USER
#		加入新 USER			傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_csge($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	
			//  改成大寫

		if (!$parm['s_name'] = trim($parm['s_name'])) {
			$this->msg->add("Error ! Please input short name!");
		    return false;
		}
		if (!$parm['cust'] = trim($parm['cust'])) {
			$this->msg->add("Error ! Please select customer !");
		    return false;
		}

					# 檢查是否有重覆
		$q_str = "SELECT id FROM consignee WHERE s_name='".$parm['s_name']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! this short name is exist in database，please change short name or check data again!");
			return false;    	    
		}
	
				
					# 加入資料庫
		$q_str = "INSERT INTO consignee (dept, s_name, f_name, addr, tel, cont, cust) 
		VALUES (
		'".$parm['dept']."',
		'".$parm['s_name']."',
		'".$parm['f_name']."',
		'".$parm['addr']."',
		'".$parm['tel']."',
		'".$parm['cont']."',
		'".$parm['cust']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't add new record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增客戶 : [".$parm['login_id']."]。") ;

		return $new_id;

	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	搜尋  USER 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_csge($mode=0, $where_str="", $per_page=0) {

	$sql = $this->sql;
	$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv
	$srh = new SEARCH();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$q_header = "SELECT * FROM consignee ";
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$argv['action']);
	$srh->add_sort_condition("consignee.s_name");
	if ($per_page == 0)
	{
		$srh->row_per_page = 12;
	}else{
		$srh->row_per_page = $per_page;
	}

//2006.11.14 以數字型式顯示頁碼 star		
	$pagesize=10;
	if ($argv['sr_startno']) {
		$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
	}else{
		$pages = $srh->get_page(1,$pagesize);
	} 
	
if ($mode==2){    //有條件的Search時
	if ($str = $argv['dept'] )  { 
		$srh->add_where_condition("consignee.dept =  '$str'", "SCH_dept",$str,"DEPT = [ $str ]. "); }
	if ($str = $argv['cust'] )  { 
		$srh->add_where_condition("consignee.cust = '$str'", "SCH_line",$str,"Cust.: [ $str ]. "); }
	if ($str = $argv['name'] )  { 
		$srh->add_where_condition("consignee.s_name like '%$str%'", "SCH_ord",$str,"Short Name : [ $str ]. "); }
	if ($str = $argv['f_name'] )  { 
		$srh->add_where_condition("consignee.f_name like '%$str%'", "SCH_ord",$str,"Full Name : [ $str ]. "); }
	if ($str = $argv['PHP_letter'] )   //add
		$srh->add_where_condition("cust.cust_s_name like '$str%'", "",$str,"CUSTOMER = [ $str* ]. ");
	elseif($argv['PHP_letter']=='0') //數字0會被判斷成false
		$srh->add_where_condition("cust.cust_s_name like '0%'", "",$str,"CUSTOMER = [ $str* ]. ");

}    	
	
//2006.11.14 以數字型式顯示頁碼 end 
	$result= $srh->send_query2();
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}
	$this->msg->merge($srh->msg);

	$op['csge'] = $result;  // 資料錄 拋入 $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
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
#	->get($id=0, $cust_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_csge($id=0, $s_name=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT consignee.*, cust.cust_init_name as cust_name FROM consignee, cust WHERE cust.cust_s_name = consignee.cust AND consignee.id='$id' ";
		} elseif ($s_name) {
			$q_str = "SELECT consignee.*, cust.cust_init_name as cust_name FROM consignee, cust WHERE cust.cust_s_name = consignee.cust AND consignee.s_name='$s_name' ";
		} else {
			$this->msg->add("Error ! please specify customer number.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! this record can't be find!");
			return false;    
		}
		return $row;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 USER 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function edit_csge($parm) {

	$sql = $this->sql;
	
	if (!$parm['f_name'] = trim($parm['f_name'])) { //檢查客戶簡稱是否存在
		$this->msg->add("Error ! Please input customer full name!");
		return false;
	}

	
	#####   更新資料庫內容
	$q_str = "UPDATE consignee SET 
					f_name='"				.strtoupper($parm['f_name']).
					"', tel='"				.$parm['tel'].
					"', addr='"				.strtoupper($parm['addr']).
					"', cont='"				.strtoupper($parm['cont']).
					"', code='"				.strtoupper($parm['code']).
					"'  WHERE id='"			.$parm['id']."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	return true;
} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_csge_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM consignee ".$where_str;
	//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
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
#	->edit($parm)		更新 USER 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_ver() {

		$sql = $this->sql;
		

		
		#####   更新資料庫內容
		$q_str = "UPDATE `s_order` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `salescost` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `shipdoc` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `smpl_ord` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `smpl_wi` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `wi` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `ap` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `consignee` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `exceptional` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `lots` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `offer` SET `cust_ver` = '001'";
		$q_result = $sql->query($q_str);

		$q_str = "UPDATE `cust` SET `ver` = '001'";
		$q_result = $sql->query($q_str);

		return true;
	} // end func	
		
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>