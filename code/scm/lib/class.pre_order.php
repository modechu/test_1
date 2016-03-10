<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)				加入新 訂單記錄  傳回 $id
#	->search($mode=0, $dept='',$limit_entries=0)			搜尋 訂 單 資料
#
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class PRE_ORDER {
		
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
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO pre_order (
								dept, cust, pre_ord, style, qty, unit, agent, etd, spt, ie, su, fty, creator, open_date
		          ) VALUES('".
							$parm['dept']."','".
							$parm['cust']."','".
							$parm['pre_ord']."','".
							$parm['style']."','".
							$parm['qty']."','".
							$parm['unit']."','".		
							$parm['agent']."','".	
							$parm['etd']."','".
							$parm['spt']."','".
							$parm['ie']."','".
							$parm['su']."','".
							$parm['fty']."','".
							$parm['creator']."','".														
							$parm['open_date']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id
		

		$this->msg->add("append order#: [".$parm['pre_ord']."]。") ;
	
		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic1'] <> "none") {
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/preord_pic/";  

						# 上傳圖相處理
			//上傳圖 600X600
			$upFile->setSaveTo($style_dir,$parm['pre_ord'].".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);

				if ($up_result){
					$this->msg->add("successful upload main picture");
				} else {
					$this->msg->add("failure upload main picutre");
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

		$q_header = "SELECT pre_order.* , cust_init_name as cust_iname FROM pre_order, cust ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("pre_order.id DESC");
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
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("pre_order.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
	
	if ($user_team == 'MD'){  //  當要分部門時
		$srh->add_where_condition("pre_order.dept = '$dept'", "",$dept,"department=[ $dept ]. ");
	}

   if ($mode==1){
   	$mesg = '';		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("pre_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust = [ $str ]. ";
		}
		if ($str = $argv['SCH_per_order'] )  { 
			$srh->add_where_condition("pre_order.pre_ord LIKE '%$str%'", "SCH_order_num",$str); 
			$mesg.= "  Pre-Order# : [ $str ]. ";
		}


		if ($str = $argv['SCH_style'] )  { 
			$srh->add_where_condition("pre_order.style = '$str'", "SCH_sample",$str); 
			$mesg.= " Style : [ $str ]. ";	
		}		

		if ($str = $argv['SCH_str'] )  { 
			$srh->add_where_condition("pre_order.etd >= '$str'", "SCH_str",$str); 
			$mesg.= " ETD > [ $str ]. ";	
		}	

		if ($str = $argv['SCH_end'] )  { 
			$srh->add_where_condition("pre_order.etd <= '$str'", "SCH_str",$str); 
			$mesg.= " ETD < [ $str ]. ";	
		}	

			
		if ($mesg)
		{
			$msg = "Search : ".$mesg;
		
			$this->msg->add($msg);
		}		

   }	
		$srh->add_where_condition("pre_order.cust = cust.cust_s_name");   // 關聯式察尋 必然要加

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
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $order_num=0) {
        $e_date=increceDaysInDate($GLOBALS['TODAY'],30);     
        
		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT pre_order.*, cust_init_name as cust_iname FROM pre_order ,cust WHERE pre_order.id='$id' AND pre_order.cust=cust.cust_s_name";
		} elseif ($order_num) {
			$q_str = "SELECT pre_order.*, cust_init_name as cust_iname FROM pre_order ,cust WHERE pre_order.order_num='$order_num' AND pre_order.cust=cust.cust_s_name";
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


		
		
	$po_user=$GLOBALS['user']->get(0,$row['creator']);
	$row['creator_id'] = $row['creator'];
	if ($po_user['name'])$row['creator'] = $po_user['name'];
	
	$po_user=$GLOBALS['user']->get(0,$row['submit_user']);
	$row['cfmer_id'] = $row['submit_user'];
	if ($po_user['name'])$row['submit_user'] = $po_user['name'];	

	$po_user=$GLOBALS['user']->get(0,$row['last_updater']);
	$row['last_updater_id'] = $row['last_updater'];
	if ($po_user['name'])$row['last_updater'] = $po_user['name'];
		
		return $row;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm, $mode=0) {

		$sql = $this->sql;		
		
		if ($mode == 0){			
		#####   更新資料庫內容
			$q_str = "UPDATE pre_order SET ".
							"	  style='"					.$parm['style'].
							"',	qty='"						.$parm['qty'].
							"',	su='"							.$parm['su'].
							"', unit='"						.$parm['unit'].							
							"', spt='"						.$parm['spt'].
							"', ie='"							.$parm['ie'].														
							"',	etd='"						.$parm['etd'].							
							"', agent='"					.$parm['agent'].
							"', fty='"						.$parm['fty'].							
							"', last_updater='"		.$parm['last_updater'].
							"', last_update='"		.$parm['last_update'].
							"'  WHERE id='"				.$parm['id']."'";

		} elseif($mode ==1){      // --- order revise -----

			$q_str = "UPDATE pre_order SET ".
							"	  style='"					.$parm['style'].
							"',	qty='"						.$parm['qty'].
							"',	su='"							.$parm['su'].
							"', unit='"						.$parm['unit'].							
							"', spt='"						.$parm['spt'].
							"', ie='"							.$parm['ie'].														
							"',	etd='"						.$parm['etd'].							
							"', agent='"					.$parm['agent'].
							"', fty='"						.$parm['fty'].							
							"', last_updater='"		.$parm['last_updater'].
							"', last_update='"		.$parm['last_update'].
							"', revise='"					.$parm['revise'].
							"', status= 0 ".
							", submit_user='".
							"', submit_date='0000-00-00".
							"'  WHERE id='"				.$parm['id']."'";
		}
//echo $q_str;
			
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
//		$this->update_field('status', $parm['status'], $parm['id']);

		$pdt_id = $parm['id'];
		$pic_id = $parm['pre_ord'];

	//  圖的處理 ===============
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/preord_pic/";  

		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台

						# 上傳圖相處理
				//2004/05/03 先檢查是否存在 如存在就先砍檔
			if(file_exists($style_dir.$parm['pre_ord'].".jpg")){
				unlink($style_dir.$parm['pre_ord'].".jpg") or die("can't delete old picture:".$pic_id.".jpg");  // 刪除舊檔
			}
				//上傳大圖 600X600
				$upFile->setSaveTo($style_dir,$parm['pre_ord'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);
		}

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_cfm($parm)		 訂單送出 待確認  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function send_submit($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE pre_order SET status=		2".
							",  submit_user='"	.$parm['submit_user'].
							"', submit_date="	 	.$parm['submit_date'].
							"  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_cfm($parm)		 訂單送出 待確認  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_qty($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE pre_order SET ".
						  "   ord_qty = ord_qty +"	.$parm['ord_qty'].
							",  last_updater='"	.$parm['last_updater'].
							"', last_update='"	 	.$parm['last_update'].
							"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_unord($fty, $year,  $cat='capacity')	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_by_year($fty, $year) {

		$sql = $this->sql;
		$total = 0;		
		$mm=array('01','02','03','04','05','06','07','08','09','10','11','12');
		
		for($i=0; $i<sizeof($mm); $i++)
		{
			$yy = $year."-".$mm[$i];
			$q_str = "SELECT sum(su) as su FROM pre_order WHERE fty = '".$fty."' AND etd like '".$yy."%'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$row = $sql->fetch($q_result);
			if (isset($row[0]))
			{
			 	$fld[$mm[$i]] = $row[0];
			 	$total+=$row[0];
			}else{
				$fld[$mm[$i]] = 0;
		  }
		}
		$fld['13'] = $total;
		return $fld;
	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_unord($fty, $year,  $cat='capacity')	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_by_mm($fty, $year,$mm) {

		$sql = $this->sql;
		$total = 0;		
		
		
			$yy = $year."-".$mm;
			$q_str = "SELECT * FROM pre_order WHERE fty = '".$fty."' AND etd like '".$yy."%'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! cannot access database, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while($row = $sql->fetch($q_result))
			{
				$fld[] = $row;
			}


		
		return $fld;
	} // end func


} // end class


?>