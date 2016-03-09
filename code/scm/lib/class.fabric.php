<?php 

#++++++++++++++++++++++ FABRIC  class ##### 開發布料  +++++++++++++++++++++++++++++++++++
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

class FABRIC {
		
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
#	->check($parm, $mode=0)		檢查 加入新 布料記錄 是否正確
#								# mode =0:一般add的check,  mode=1: edit時的check
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm, $mode=0) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目
	if($mode==0){
		if (!$parm['kind']) {
			$this->msg->add("Error ! 請選擇 布種 。");
//		    return false;
		}
	}
		if (!$parm['cat']) {
			$this->msg->add("Error ! 請選擇 分類 。");
//		    return false;
		}
		if (!$parm['name'] = trim($parm['name'])) {
			$this->msg->add("Error ! 請輸入布名，如無布名請輸入[N/A] 。");
//		    return false;
		}

		// 相關欄位 必需要輸入時---------
		if (trim($parm['weight'])){
			if (!$parm['unit_wt']) {
				$this->msg->add("Error ! 請選擇 碼重單位 。");
//				return false;
			}
		}
		
		if (trim($parm['width'])){
			if (!$parm['width_unit']) {
				$this->msg->add("Error ! 請選擇 碼寬單位 。");
//				return false;
			}
		}

		if (trim($parm['price'])){
			if (!$parm['currency']) {
				$this->msg->add("Error ! 請選擇 幣值單位 。");
//				return false;
			}
		}
		if (trim($parm['price'])){
			if (!$parm['unit_price']) {
				$this->msg->add("Error ! 請選擇 計價單位 。");
//				return false;
			}
		}

		if (trim($parm['term'])){
			if (!$parm['location']) {
				$this->msg->add("Error ! 請選擇 供應條件的地點 。");
//				return false;
			}
		}

		if (count($this->msg->get(2))){
			return false;
		}
					
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 布料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO fabric (art_code,
									cat,
									supl,
									supl_ref,
									name,
									content,
									width,
									width_unit,
									weight,
									unit_wt,
									construct,
									finish,
									co,
									price,
									unit_price,
									term,
									location,
									leadtime,
									currency,
									remark) VALUES('".
									$parm['art_code']."','".
									$parm['cat']."','".
									$parm['supl']."','".
									$parm['supl_ref']."','".
									$parm['name']."','".
									$parm['content']."','".
									$parm['width']."','".
									$parm['width_unit']."','".
									$parm['weight']."','".
									$parm['unit_wt']."','".
									$parm['construct']."','".
									$parm['finish']."','".
									$parm['co']."','".
									$parm['price']."','".
									$parm['unit_price']."','".
									$parm['term']."','".
									$parm['location']."','".
									$parm['leadtime']."','".
									$parm['currency']."','".
									$parm['remark']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("成功 新增 布料記錄: [".$parm['art_code']."]。") ;

	
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/fabric/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic1'] <> "none") {
						# 上傳圖相處理
			//上傳大圖 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);
			//上傳小圖 150x150
//			$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
//			$up_result = $upFile->upload($parm['pic'], 150, 150);
				if ($up_result){
					$this->msg->add("成功上傳主圖");
				} else {
					$this->msg->add("上傳主圖 失敗");
				}
		}else{
			// 將gray 圖復製 一個
			copy($no_img,$style_dir.$pdt_id.".jpg");
//			copy($no_img,$style_dir."s".$pdt_id.".jpg");
		}

		return $pdt_id;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
//		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$argv = $_SESSION['sch_parm'];
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM fabric ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['action']);
		$srh->add_sort_condition("id DESC ");
		if($limit_entries) 
		{
			$srh->row_per_page = $limit_entries;
		}else{
			$srh->row_per_page = 20;
		}

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
			
	}else{
##--*****--2006.11.16頁碼新增 start		##	
			
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}

if ($mode==1){
		if ($str =$argv['cat'] )  { 
			$srh->add_where_condition("cat = '$str'", "PHP_cat",$str,"Fabric cate.= [ $str ]. "); }
		if ($str = $argv['art_code'] )  { 
			$srh->add_where_condition("art_code LIKE '%$str%'", "PHP_name",$str,"Fabric # :[ $str ]. "); }
		if ($str = $argv['name'] )  { 
			$srh->add_where_condition("name LIKE '%$str%'", "PHP_name",$str,"Name :[ $str ]. "); }
		if ($str = $argv['content'] )  { 
			$srh->add_where_condition("content LIKE '%$str%'", "PHP_content",$str,"Content: [ $str ] "); }
		if ($str = $argv['supl_ref'] )  { 
			$srh->add_where_condition("supl_ref LIKE '%$str%'", "PHP_supl_ref",$str,"Supplier ref. : [ $str ] "); }
		if ($str = $argv['finish'] )  { 
			$srh->add_where_condition("finish LIKE '%$str%'", "PHP_finish",$str,"Finish : [ $str ] "); }
		if ($str = $argv['supl'] )  { 
			$srh->add_where_condition("supl = '$str'", "PHP_supl",$str,"Supplier = [ $str ]. "); }
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

		$op['fabric'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] =  $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}


		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $art_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $art_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM fabric WHERE id='$id' ";
		} elseif ($fabric_code) {
			$q_str = "SELECT * FROM fabric WHERE art_code='$art_code' ";
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
#	->edit($parm)		更新 副料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE fabric SET cat='"		.$parm['cat'].
							"',	supl='"			.$parm['supl'].
							"', co='"			.$parm['co'].
							"', location='"		.$parm['location'].
							"', currency='"		.$parm['currency'].
							"', unit_price='"	.$parm['unit_price'].
							"', term='"			.$parm['term'].
							"', unit_wt='"		.$parm['unit_wt'].
							"', name='"			.$parm['name'].
							"', content='"		.$parm['content'].
							"', construct='"	.$parm['construct'].
							"', finish='"		.$parm['finish'].

							"', supl_ref='"		.$parm['supl_ref'].
							"', width='"		.$parm['width'].
							"', width_unit='"	.$parm['width_unit'].
							"', weight='"		.$parm['weight'].
							"', price='"		.$parm['price'].
							"', leadtime='"		.$parm['leadtime'].

							"', remark='"		.$parm['remark'].

						"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];


	//  圖的處理 ===============
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/fabric/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";

		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
			# 上傳圖相處理
				//2004/05/03 先檢查是否存在 如存在就先砍檔
			if(file_exists($style_dir.$pdt_id.".jpg")){
				unlink($style_dir.$pdt_id.".jpg") or die("無法刪除舊圖檔:".$pdt_id.".jpg");  // 刪除舊檔
			}
			//上傳大圖 600X600
				$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);
		}

		
		return $pdt_id;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE fabric SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 布料記錄  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 開發布料 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM fabric WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

			// 圖的刪除 -------
			$style_dir	= $GLOBALS['config']['root_dir']."/fabric/";  

			if(file_exists($style_dir.$id.".jpg")){
				unlink($style_dir.$id.".jpg") or die("無法刪除圖檔:".$id.".jpg");  // 刪除舊檔
			}
//			if(file_exists($style_dir."s".$id.".jpg")){
//				unlink ($style_dir."s".$id.".jpg") or die("無法刪除圖檔:"."s".$id.".jpg");  // 刪除舊檔
//			}




		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM fabric ".$where_str;

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
#	->search($mode=0,$where_str='')	搜尋  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_stock($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
//		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$argv = $_SESSION['sch_parm'];
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT fab_stock.*, lots.lots_name, lots.comp, lots.specify 
								 FROM fab_stock, lots ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['action']);
		$srh->add_sort_condition("fab_stock.art_code DESC ");
		if($limit_entries) 
		{
			$srh->row_per_page = $limit_entries;
		}else{
			$srh->row_per_page = 20;
		}

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
			
	}else{
##--*****--2006.11.16頁碼新增 start		##	
			
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}
		$srh->add_where_condition("fab_stock.art_code = lots.lots_code");
if ($mode==1){
		if ($str =$argv['cat'] )  { 
			$srh->add_where_condition("fab_stock.cat = '$str'", "PHP_cat",$str,"Fabric cate.= [ $str ]. "); }
		if ($str = $argv['art_code'] )  { 
			$srh->add_where_condition("fab_stock.art_code LIKE '%$str%'", "PHP_name",$str,"Fabric # :[ $str ]. "); }
		if ($str = $argv['name'] )  { 
			$srh->add_where_condition("fab_stock.name LIKE '%$str%'", "PHP_name",$str,"Name :[ $str ]. "); }
		if ($str = $argv['content'] )  { 
			$srh->add_where_condition("lots.comp LIKE '%$str%'", "PHP_content",$str,"Content: [ $str ] "); }
		if ($str = $argv['supl_ref'] )  { 
			$srh->add_where_condition("fab_stock.supl_ref LIKE '%$str%'", "PHP_supl_ref",$str,"Supplier ref. : [ $str ] "); }
		if ($str = $argv['finish'] )  { 
			$srh->add_where_condition("lots.cons LIKE '%$str%'", "PHP_finish",$str,"Finish : [ $str ] "); }
		if ($str = $argv['supl'] )  { 
			$srh->add_where_condition("fab_stock.supl = '$str'", "PHP_supl",$str,"Supplier = [ $str ]. "); }
		if ($str = $argv['fty'] )  { 
			$srh->add_where_condition("fab_stock.fty = '$str'", "PHP_fty",$str,"stock area = [ $str ]. "); }

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

		$op['fabric'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] =  $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}


		return $op;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 布料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_stock($parm) {
					
		$sql = $this->sql;


		foreach($parm as $key => $value)$parm[$key] = str_replace("'","\'",$parm[$key]);	
					# 加入資料庫
		$q_str = "INSERT INTO fab_stock (art_code,
									cat,
									supl,
									supl_ref,
									name,
									co,
									qty,
									unit,
									fty,
									ord_num,
									est_qty,
									po_unit,
									bom_color,
									remark) VALUES('".
									$parm['mat_code']."','".
									$parm['cat']."','".
									$parm['supl']."','".
									$parm['supl_ref']."','".
									$parm['name']."','".
									$parm['co']."','".
									$parm['qty']."','".
									$parm['unit']."','".
									$parm['fty']."','".
									$parm['ord_num']."','".
									$parm['est_qty']."','".
									$parm['po_unit']."','".
									$parm['bom_color']."','".
									$parm['remark']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("成功 新增 布料記錄: [".$parm['art_code']."]。") ;

	
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/fab_stock/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
						# 上傳圖相處理
			//上傳大圖 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);

       //上傳小圖 50x50
       $upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
       $up_result = $upFile->upload($parm['pic'], 50, 50);
			
				if ($up_result){
					$this->msg->add("成功上傳主圖");
				} else {
					$this->msg->add("上傳主圖 失敗");
				}
		}else{
			// 將gray 圖復製 一個
			copy($no_img,$style_dir.$pdt_id.".jpg");
		}

		return $pdt_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $art_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_stock($id=0, $art_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT fab_stock.*, lots.comp, lots.specify, lots.width, lots.weight, lots.cons,
											 lots.lots_name 
								FROM fab_stock, lots WHERE art_code = lots.lots_code AND fab_stock.id='$id' ";
		} elseif ($fabric_code) {
			$q_str = "SELECT fab_stock.*, lots.comp, lots.specify, lots.width, lots.weight, lots.cons,
										   lots.lots_name
								FROM fab_stock, lots WHERE art_code = lots.lots_code AND art_code='$art_code' ";
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
#	->edit($parm)		更新 副料記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_stock($parm) {

		$sql = $this->sql;

		foreach($parm as $key => $value)$parm[$key] = str_replace("'","\'",$parm[$key]);	


		#####   更新資料庫內容
		$q_str = "UPDATE fab_stock SET cat='"		.$parm['cat'].
							"',	supl='"				.$parm['supl'].
							"', co='"					.$parm['co'].
							"', name='"				.$parm['name'].

							"', supl_ref='"		.$parm['supl_ref'].
							"', qty='"				.$parm['qty'].
							"', unit='"				.$parm['unit'].
							"', fty='"				.$parm['fty'].

							"', remark='"			.$parm['remark'].

						"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  無法更新資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];


	//  圖的處理 ===============
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/fab_stock/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";

		if($parm['pic_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台

						# 上傳圖相處理
				//2004/05/03 先檢查是否存在 如存在就先砍檔
			if(file_exists($style_dir.$pdt_id.".jpg")){
				unlink($style_dir.$pdt_id.".jpg") or die("無法刪除舊圖檔:".$pdt_id.".jpg");  // 刪除舊檔
			}
				//上傳大圖 600X600
				$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);

        //上傳小圖 150x150
        $upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
        $up_result = $upFile->upload($parm['pic'], 50, 50);


		}

		
		return $pdt_id;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 布料記錄  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_stock($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 布料 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM fab_stock WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

			// 圖的刪除 -------
			$style_dir	= $GLOBALS['config']['root_dir']."/fab_stock/";  

			if(file_exists($style_dir.$id.".jpg")){
				unlink($style_dir.$id.".jpg") or die("無法刪除圖檔:".$id.".jpg");  // 刪除舊檔
			}

			if(file_exists($style_dir."s".$id.".jpg")){
				unlink($style_dir."s".$id.".jpg") or die("無法刪除圖檔:".$id.".jpg");  // 刪除舊檔
			}

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_stock($code,$color,$ord_num) {
			$sql = $this->sql;
			$rtn = $exc_rec = array();
			$mk = 0;
			// sup_code
//一般採購
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit,
													ap.sup_code
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.status = 12 AND ap.special = 0 AND
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													bom_lots.color = '".$color."' AND smpl_code = '".$ord_num."'
								GROUP BY  smpl_code";				
//echo $q_str."<BR><BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				$rtn = $row; 
				$mk = 1;
			}

//額外採購
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit,
													ap.sup_code
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.status = 12 AND ap.special > 0 AND
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													bom_lots.color = '".$color."' AND smpl_code = '".$ord_num."'
								GROUP BY  smpl_code";		
			#M11020802 修改找不到 wi 資料 移除 status 和 color 的判斷
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit,
													ap.sup_code
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND 
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													smpl_code = '".$ord_num."'
								GROUP BY  smpl_code";				
// echo $q_str."<BR><BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$row['ap_qty'] = 0;	
				if($mk == 0)
				{
					$rtn =  $row;
					$mk = 1;
				}else{
					$po_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['po_qty']);	
					$ap_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['ap_qty']);	
					$rtn['po_qty'] +=  $po_qty;
					$rtn['ap_qty'] +=  $ap_qty;
				}				
			}



//預先採購
			$q_str = "SELECT  ap_special.mat_code, ap_special.color, bom_lots.qty as bom_qty, lots_name,
												po_unit, lots_use.unit, ap_special.po_qty, lots_use.smpl_code, ap.sup_code
								FROM  	bom_lots , ap_special, lots_use, ap
								WHERE 	bom_lots.ap_mark = ap_special.ap_num AND bom_lots.lots_used_id = lots_use.id AND 
												ap.ap_num = ap_special.ap_num AND
												bom_lots.color = ap_special.color AND lots_use.lots_code = ap_special.mat_code AND
												lots_use.lots_code = '".$code."' AND bom_lots.color = '".$color."' AND
												lots_use.smpl_code = '".$ord_num."'"
												;	
// echo $q_str."<BR><BR>";	
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$tmp_qty = explode(',',$row['bom_qty']);
				$row['ap_qty'] = array_sum($tmp_qty);
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				if($mk == 0)
				{
						$rtn =  $row;
				}else{
						$po_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['po_qty']);	
						$ap_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['ap_qty']);	
						$rtn['po_qty'] +=  $po_qty;
						$rtn['ap_qty'] +=  $ap_qty;
				}
			}
			$rtn['est_qty'] = $rtn['po_qty'] - $rtn['ap_qty'];


		return $rtn;
	} // end func		






#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>