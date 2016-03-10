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

class TMPSUPL {
		
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
		
//2006.11.15修改新增後呈現方式	start	
		$q_header = "SELECT * FROM supl3";
//2006.11.15修改新增後呈現方式    end
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("vndr_no");
		$srh->row_per_page = 12;
//2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 //2006.11.14 以數字型式顯示頁碼 end  
	 $srh->add_where_condition("supl_cat=''"); 
 if ($mode == 0){		
//		if ($str = $argv['PHP_vndr_no] )  { 
	//		$srh->add_where_condition("vndr_no = '$str'", "PHP_supl_cat",$str,"搜尋 供應商代號 = [ ".$str." ] "); }
		if ($str = $argv['PHP_vndr_no'] )  { 
			$srh->add_where_condition("vndr_no LIKE '%$str%'", "PHP_vndr_no",$str,"搜尋供應商全名含有: [ $str ]內容 "); }
		if ($str = $argv['PHP_supl_f_name'] )  { 
			$srh->add_where_condition("supl_f_name LIKE '%$str%'", "PHP_supl_f_name",$str,"搜尋供應商全名含有: [ $str ]內容 "); }
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

		$op['sup'] = $result;  // 資料錄 拋入 $op
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

		$q_str = "SELECT * FROM supl3 WHERE vndr_no='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
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
#	->get($id=0, dept_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function exchange($id="",$supl_cat="") {
		
		$str="''";
		$sql = $this->sql;
		$q_str = "SELECT * FROM supl3 WHERE id='$id' ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}
		
									# 檢查是否有重覆
		$row['supl_s_name'] = str_replace("'","\'",$row['supl_s_name']);
		$q_str = "SELECT id FROM supl WHERE supl_s_name='".$row['supl_s_name']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! This supplier short name is exist, Please change another one or check again!");
			return false;    	    
		}
		
		if ($supl_cat)
		{
		for($i=0; $i < 34; $i++)
		{			
			
			if ($i > 0)
			{
				if ($i == 25) $row[$i]=$supl_cat;
				$row[$i] = str_replace("'","\'",$row[$i]);
				$str=$str.",'".$row[$i]."'";				
			}
		}
		$q_str="INSERT INTO `supl` VALUES (".$str.")";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		$q_str = "UPDATE supl3 SET supl_cat='".$supl_cat."' WHERE id='".$id."'";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$this->msg->add("Successfully add supplier ".$row['supl_s_name']."to SCM system");
		return $row;
		}else{
			$this->msg->add("Please choice supplier category ");
			return true;		
		}
	} // end func






#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


} // end class


?>