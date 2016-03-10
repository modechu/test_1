<?php

	##########################  class search  #####################################
	# Short description: 用來search db 的class，目前使用Smarty
	#
	#  search->init($server, $user, $passwd, $db,$link=0)
	#  search->set_sql($sql)
	#  search->clean_all_condition()
	#  search->add_q_header($q_header_str="")
	#  search->add_where_condition($where_condition,$sr_parm_name=0,$sr_parm_value=0,$msg=0)
	#  search->add_sort_condition($sort_condition,$sr_sorttype=0,$msg=0)
	#  search->add_limit_condition($from_no=0,$row_per_page=0)
	#  search->add_cgi_parm($parm_name,$parm_value)
	#  search->send_query($mode=0)
	#  search->send_query2($limit_entries=0)
	#  search->get_cgi_str($mode=0)
	#  search->get_display_msg($mode=0)
	#  search->get_sql_link_id()
	#  search->get_total_row_num()
	#
	# 20010713: ver 1.3
	#	修改 set_link()的bug，改set_link為set_sql
	#	加入send_query2()，加入start_no,row_per_page供add_limit_condition使用
	# 20010518: 加入 set_link(),改用MSG_HANDLE,send_query()加入mode=2(傳回所有match的資料)，
	# 20010517: 之前不可考....
	#
	##############################################################################

class SEARCH {

//init()
//add_q_header($q_header_str="")
//add_where_condition($where_condition,$sr_parm_name,$sr_parm_value,$msg)
//add_sort_condition($sort_condition,$sr_sorttype,$msg)
//add_limit_condition($from_no,$row_per_page)
//send_query($mode=0)
//add_cgi_parm($parm_name,$parm_value)
//get_cgi_str($mode=0)
//get_display_msg($mode=0)
//get_sql_link_id()
//get_total_row_num()

	var $sql;
	var $q_result;

    var $where_ary;
	var $cgi_parm_ary;
	var $display_msg_ary;
	var $msg;

	var $q_str;
	var $q_header;
	var $q_sorttype;
	var $q_limit;

	// Description: 從第幾筆資料開始顯示
	var $start_no="0";
	
	// Description: 一頁要傳回幾筆
	var $row_per_page = "20";

	//  Description: 總共的筆數
	var $max_no;

	// Description: 給browse用，上一頁開始的列
	var $prev_no ;

	// Description:給browse用，下一頁開始的列
	var $next_no;
	
// Description: 給browse用，最後一頁開始的列
	var $last_no ;
				
// Description: 給browse用，本頁的行數
	var $rows_this_page ;
//	Description:總頁數
	var $full_page;
	var $now_pp;
	

	###################################################################
	#	search->init($server, $user, $passwd, $db,$link=0)
	############## 初始化資料庫連結等    #如果有Link則使用之
	function init($server, $user, $passwd, $db,$link=0) {
					# 清空必要的array
		$this->where_ary=array();
		$this->cgi_parm_ary = array();
					# 向下相容
		$this->display_msg_ary = array();

		$sql = new msSQL();	# 依情況而定

		$this->msg = new MSG_HANDLE();

		if ($link) {
			$sql->set_link_id($link);
		}	else if (!$sql->connect($server, $user, $passwd, $db)) {
			$this->msg->merge($sql->msg);
			return false;
		}

		$this->sql = $sql;
		return true;
	}

	###################################################################
	#	search->set_sql($sql)
   ##################### 傳入弄成的link
	function set_sql($sql) {

		$this->msg = new MSG_HANDLE();
		if (!$sql) {
			$this->msg->add("Error! no databs specified...");
			return false;
		}
		$this->where_ary=array();
		$this->cgi_parm_ary = array();
		$this->sql = $sql;
		return true;
	}

	###################################################################
	#	search->clean_all_condition()
	################## 清理所有buffer #######
	function clean_all_condition() {
		$this->where_ary=array();
		$this->cgi_parm_ary=array();
		$this->msg->clean();
		$this->q_str="";
		$this->q_header="";
		$this->q_sorttype="";
		$this->q_limit="";

		return true;
	}

	###################################################################
	#	search->add_q_header($q_header_str="")
   ################### 寫入q_header_str

	function add_q_header($q_header_str="") {

		if ($q_header_str) {
			$this->q_header = $q_header_str;
			return true;
		}	else  {
		    $this->msg->add("Error!! empty SQL Header");
			return false;
		}
	}

	###################################################################
    #	search->add_where_condition($where_condition, $sr_parm_name=0, $sr_parm_value=0, $msg=0)
	######################## 寫入where_ary
	function add_where_condition($where_condition,$sr_parm_name=0,$sr_parm_value=0,$msg=0) {
		if (!$where_condition) {
		    $this->msg->add("Error!! empty Where_Str. in query");
			return false;
		}
		$this->where_ary[] = $where_condition;

		if ($sr_parm_name && $sr_parm_value) {
//			$this->cgi_parm_ary[$sr_parm_name] = $sr_parm_value;       //2005/01/03去掉 主要是重覆
		}
		if ($msg) {
			$this->msg->add($msg);
		}
		return true;
	}  // end of func

	###################################################################
	#	search->add_sort_condition($sort_condition,$sr_sorttype=0,$msg=0)
	####################### 寫入q_sort_str
	function add_sort_condition($sort_condition,$sr_sorttype=0,$msg=0) {

		if (!$sort_condition) {
		    $this->msg->add("Error!! empty sort condition in query");
			return false;
		}
		$this->q_sorttype = "ORDER BY ".$sort_condition;
		if ($sr_sorttype) {
			$this->cgi_parm_ary[PHP_sr_sorttype] = $sr_sorttype;
		}
		if ($msg) {
			$this->display_msg_ary[] = $msg;   
		}
		return true;
	}  // end of func

	###################################################################
	#	search->add_limit_condition($from_no=0,$row_per_page=0)
	################### 寫入q_limit_str
	function add_limit_condition($from_no=0,$row_per_page=0) {

		if ($from_no) {
			$this->start_no = $from_no;		    
		}	else {
		    $this->start_no = $from_no = 0;
		}
		if (!$row_per_page) {
			$row_per_page = $this->row_per_page;
		}	else {
       		$this->row_per_page = $row_per_page;
		}
//		if (!$from_no || !$row_per_page) {
//			return false;
//		}
		$this->q_limit = "LIMIT ".$from_no.",".$row_per_page;
		return true;
	}   // end of func

	###################################################################
	#	search->add_cgi_parm($parm_name,$parm_value)
	################## 加入CGI用參數
	function add_cgi_parm($parm_name,$parm_value) {

		if (!$parm_name) {
			$this->msg->add("Error!! unreasonable CGI parm. contact programer.");
			return false;
		}
		$this->cgi_parm_ary[$parm_name]=$parm_value;
		return true;
	}	// end of func

	###################################################################
	#	search->send_query($mode=0)
	#				
	################ 執行檢索
			# mode=0 : Normal
			# mode=1 : only return row_nums
			# mode=2 : return all match rows
	function send_query($mode=0) {

			# 做出where_str
		$where_ary = $this->where_ary;
		if ($where_ary) {
			# 替每個where加上括號，避免誤判
			foreach ($where_ary AS $key=>$val)
			{
				$where_ary[$key]='('.$val.')';
			}
			# 用 AND　把 where連起來
			$where_str= "WHERE ".join(" AND ", $where_ary);
		}
			# 作出q_str
		$q_str = $this->q_header . " $where_str ".$this->q_sorttype;
		if ($mode ==0) {
			$q_str .= " " .$this->q_limit;
		}
		$this->q_str = $q_str;
//print $q_str."<BR>";
		$sql= $this->sql;
		if (!$sql) {
			$this->msg->add("Error! disconnect databse.");
			return false;
		}
		if (!($q_result= $sql->query($q_str))) {
			$this->msg->merge($sql->msg);
			return false;
		}
		$this->q_result = $q_result;
		if ($mode==1) {		//**************mode=1
			$result_num = $sql->num_rows();
			return $result_num;
		}	else if($mode==2) {	//**************mode=2
								# 傳回所有match的資料(可能很大!!!!)
								# 先用match上限 限制一下
								# 找時間再寫程式
			$match_limit = 500;
			$match = 0;
			$return_ary = array();
			while ($row = $sql->fetch($q_result)) {
				$return_ary[] = $row;
				$match++;
				if ($match==500) {
					break;
				}
			}
			if ($match != 500) {
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
			return $return_ary;
		}	else  {				//*************mode <>1 and mode<>2
			return $sql;
		}   // end  if  mode...
	
	}   //end of func
	###################################################################
	#			search->send_query2($limit_entries=0)
   ########################################## 執行檢索 (第二代!!)
	function send_query2($now_Page=1,$limit_entries=0) {

		# 做出where_str
# 2004/11/19 define $where_str.............for windows server...
		$where_str = "" ;

		$where_ary = $this->where_ary;
		$start_no = $this->start_no;
		$row_per_page = $this->row_per_page;
		if ($where_ary) {
		# 替每個where加上括號，避免誤判
			foreach ($where_ary AS $key=>$val)
			{
				$where_ary[$key]='('.$val.')';
			}
		# 用 AND　把 where連起來
			$where_str= "WHERE ".join(" AND ", $where_ary);
		}     // end if $where_ary

		# 作出q_str
		$q_str = $this->q_header . " $where_str ".$this->q_sorttype;
		$this->q_str = $q_str;

		$sql= $this->sql;
		if (!$sql) {
			$this->msg->add("Error! disconnect database.");
			return false;
		}
		if (!($q_result= $sql->query($q_str))) {
			$this->msg->merge($sql->msg);
			return false;
		}

		$this->q_result = $q_result;
					# 計算並填入有關工具列要用的值
		$this->max_no = $sql->num_rows($q_result);
		$max_no = $this->max_no;						
		$this->prev_no = (($start_no - $row_per_page) >0)?($start_no - $row_per_page) : 0 ;
		$this->last_no = (($max_no-$row_per_page)>0) ? ($max_no - $max_no%$row_per_page +1) : 0;
		$this->next_no =(($start_no + $row_per_page)<$max_no)?($start_no + $row_per_page):0 ;
					# 移至開始的那一筆資料
		if ($start_no) {
			$maxnum = $this->max_no;
			if ($start_no > $maxnum) {
				$start_no = $maxnum;
			}

			if (!$sql->seek($start_no,$q_result)) {
				$this->msg->add("Error!! unable to record:'$start_no' !");
				return false;
			}
		}   // end if $start_no
	# 2005/05/14 加入 ------
		if($this->max_no > $row_per_page){
			$this->rows_this_page = $row_per_page;
		}else{
			$this->rows_this_page = $this->max_no;
		}

			# 傳回所有match的資料(可能很大!!!!)
			# 如果沒有設定row_per_page的話，上限40
			# 先用match_limit 限制一下
			# 找時間再寫程式
			#
			# 2005/05/16 加入 可擴大搜尋量 至 輸入值 $limit_entries
		if ($limit_entries){
			$match_limit = $limit_entries;
		}else{
			$match_limit = $this->row_per_page;
		}

//		$match_limit = $this->row_per_page;    // 2005/05/16 改成上列判斷式



		$match = 0;
		$return_ary = array();

		while ($row = $sql->fetch($q_result)) {
			$return_ary[] = $row;
			$match++;

			if ($match >= $match_limit) {
				break;
			}
		}  // end while...




		$sql->free_result($q_result);
		$result =0;
		$this->q_result = $q_result;
		return $return_ary;
	}   // end func

	###################################################################
	#			search->get_cgi_str($mode=0)
	######### 取得CGI用參數字串
				# mode=0 : get用
				# mode=1 : post用
	function get_cgi_str($mode=0) {

		$cgi_parm_ary = $this->cgi_parm_ary;
		if ($mode==0) {			# mode=0 : get用
			foreach ($cgi_parm_ary AS $key=>$val){
				$cgi_parm_ary[$key] = $key."=".$val;
			}
			$url_str=join("&",$cgi_parm_ary);

//			$url_str = "?".$url_str;
			// 20020912  改掉!!! 因不知PHP_SELF 會如何?......2005/0103 又打開 試看看
			$url_str = $GLOBALS['PHP_SELF']."?".$url_str;	
			return $url_str;
		}	else  {
			foreach ($cgi_parm_ary AS $key=>$val){
				$cgi_parm_ary[$key] = '<INPUT TYPE="hidden" NAME="'.$key.'" VALUE="'.$val.'">'."\n";
			}
			$url_str=join("",$cgi_parm_ary);
			return $url_str;
		}  // end if
	}	// end func

	###################################################################
	#			search->get_display_msg($mode=0)
	################ 取得display_msg內的資料
			 # *** 已廢棄!!! 純用來向下相容用!!
			 # *** 請改用$this->msg
			 # mode=0 : HTML格式(預設)
			 # mode=1 : Plain TEXT
			 # mode=2 : HTML格式(RP)
	function get_display_msg($mode=0) {

		foreach ($this->display_msg_ary AS $key=>$val) {
			$this->msg->add($val);
		}
		$msg_html= $this->msg->get(0);
		$msg_text= $this->msg->get(1);
		$return_str="";
		if ($mode == 0 || $mode == 2) {
			$return_str .= '<TABLE border=0 cellspacing=5 align="center">'."<TR><TD>\n";
			$return_str .= "$msg_html</TD>\n";
			$return_str .= '<TD>';
							# RF用
			if ($mode==0) {
				$return_str .= '<TABLE border=0 >';
				for ($i=0;$i<5 ;$i++) {
					$return_str .= '<TR ><TD width="80" BGCOLOR="'.  $GLOBALS["COLOR_level_".$i].'"><font size=2>&nbsp;</font></TD><TD><font size=2>'.$GLOBALS["STR_level_".$i]."</font></TD></TR>\n";
				}
				$return_str .= "</TABLE>";
			}	else  {						# RP用
				$return_str .= '<TABLE border=0 >';
				
				for ($i=0;$i<4 ;$i++) {
					$return_str .= '<TR ><TD width="80" BGCOLOR="'.  $GLOBALS["COLOR_RP_level_".$i].'"><font size=2>&nbsp;</font></TD><TD><font size=2>'.$GLOBALS["STR_RP_level_".$i]."</font></TD></TR>\n";
				}
				$return_str .= "</TABLE>";
			}
			$return_str .= "</TD></TR>";
			$return_str .= "</TABLE>\n";
		}	else  {
				$return_str = $msg_text;
		}
		return $return_str;
	}	// end func
	###################################################################
	#			search->get_sql_link_id()
	####### 取得sql link id
	function get_sql_link_id() {

		return $this->sql;
	
	}

	###################################################################
	#			search->get_total_row_num()
	####### 取得檢索結果總筆數
	function get_total_row_num() {

		$num = $this->send_query(1);
		if (!$num) {
			return false;
		}
		return $num;
	}
	
	###################################################################
	#			search->get_full_page()
	####### 取得檢索結果總頁數	2006.11.14
	
	function get_max_page()
	{
		$total_count =$this->max_no;
		$row_per_page = $this->row_per_page;
		$full_page = $total_count / $row_per_page;
		settype($full_page, 'integer');
		if (($total_count % $row_per_page) != 0) {$full_page=$full_page+1;}
		return $full_page;		
	}
	
	###################################################################
	#			search->get_full_page()
	####### 分頁頁碼顯示	2006.11.14
	function get_page($now_page,$pagesize=10)
	{
		$row_per_page = $this->row_per_page;
//		$pagesize=10;
		if(substr($now_page,0,1)=="p")
		{		
			$now_page=substr($now_page,1);
			$now_page=$now_page-$pagesize;
		}elseif(substr($now_page,0,1)=="n"){			
			    $now_page=substr($now_page,1);
			    $now_page=$now_page+1;				    		    			
		}
		$this->now_pp = $now_page;
		$g_page = ($now_page / $pagesize );		
		settype($g_page, 'integer');
		if (($now_page%$pagesize)==0) $g_page=$g_page-1;
        $g_page=$g_page*$pagesize+1;
        $j=0;
		for($i=$g_page; $i< $g_page +$pagesize; $i++)
			{
				$pages[$j]=$i;
				$j++;				
			}	
			$limit_page= ($now_page-1)*$row_per_page;
			$this->add_limit_condition($limit_page);
			return $pages;
		}
		
		
		
		
		
}    // end class

?>