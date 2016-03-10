<?php 

#++++++++++++++++++++++ SMPL  class ##### 樣本  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->copy_smpl($parm)						複製新 樣本記錄
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL {
		
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
		if (!$parm['cust'] = trim($parm['cust'])) {
			$this->msg->add("Error ! 請輸入客戶別 。");
		    return false;
		}
		if (!$parm['year'] = trim($parm['year'])) {
			$this->msg->add("Error ! 請輸入年份 。");
		    return false;
		}
							
//		if ($this->msg){
//			return false;
//		}
					
//echo "<br>[debug in  check procedure.............]";
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 樣本記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO smpl (style_code,
									dept,
									cust,
									cust_ref,
									year,
									season,
									creator,
									size_type,
									size_scale,
									style_type,
									style_relate,
									quota,
									chief_val,
									ling_val,
									opendate,
									memo) VALUES('".
									$parm['style_code']."','".
									$parm['dept_code']."','".
									$parm['cust']."','".
									$parm['cust_ref']."','".
									$parm['year']."','".
									$parm['season']."','".
									$parm['creator']."','".
									$parm['size_type']."','".
									$parm['size_scale']."','".
									$parm['style_type']."','".
									$parm['style_relate']."','".
									$parm['quota']."','".
									$parm['chief_val']."','".
									$parm['ling_val']."',
									NOW(),'".
									$parm['comments']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("成功 新增樣本記錄: [".$parm['style_code']."]。") ;

	
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic1_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic1'] <> "none") {
						# 上傳圖相處理
			//上傳大圖 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 600, 600);
			//上傳小圖 150x150
			$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 150, 150);
				if ($up_result){
					$this->msg->add("成功上傳主圖");
				} else {
					$this->msg->add("上傳主圖 失敗");
				}
		}else{
			// 將gray 圖復製 一個
			copy($no_img,$style_dir.$pdt_id.".jpg");
			copy($no_img,$style_dir."s".$pdt_id.".jpg");
		}


	// 次圖 A上傳
		if($parm['pic2_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic2'] <> "none"){
						# 上傳圖相處理
			//圖檔目錄(以 pdt_id 來設圖檔檔名 前面加上 "A" //上傳圖 320X320
			$upFile->setSaveTo($style_dir,"A".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic2'], 320, 320);
				if ($up_result){
					$this->msg->add("成功上傳 次圖一");
				} else {
					$this->msg->add("上傳次圖一 失敗");
				}
		}else{
			// 將gray 圖復製 一個
			copy($no_img,$style_dir."A".$pdt_id.".jpg");
		}

	// 次圖 B上傳
		if($parm['pic3_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic3'] <> "none"){
						# 上傳圖相處理
			//圖檔目錄(以 pdt_id 來設圖檔檔名 前面加上 "B" //上傳圖 320X320
			$upFile->setSaveTo($style_dir,"B".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic3'], 320, 320);
				if ($up_result){
					$this->msg->add("成功上傳 次圖二");
				} else {
					$this->msg->add("上傳次圖二 失敗");
				}
		}else{
			// 將gray 圖復製 一個
			copy($no_img,$style_dir."B".$pdt_id.".jpg");
		}

		return $pdt_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_smpl($parm)		複製新 樣本記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_smpl($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO smpl (style_code,
									dept,
									cust,
									cust_ref,
									year,
									season,
									creator,
									size_type,
									size_scale,
									style_type,
									style_relate,
									quota,
									chief_val,
									ling_val,
									opendate,
									copykey,
									memo) VALUES('".
									$parm['style_code']."','".
									$parm['dept_code']."','".
									$parm['cust']."','".
									$parm['cust_ref']."','".
									$parm['year']."','".
									$parm['season']."','".
									$parm['creator']."','".
									$parm['size_type']."','".
									$parm['size_scale']."','".
									$parm['style_type']."','".
									$parm['style_relate']."','".
									$parm['quota']."','".
									$parm['chief_val']."','".
									$parm['ling_val']."',
									NOW(),'".
									"*','".
									$parm['comments']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		// 原記錄 寫入 key 欄位資料 ------------




		$updt = array ( 'id'			=> $parm['ori_id'],
						'field_name'	=> 'copykey',
						'field_value'	=> $parm['copykey']	);

		if (!$this->update_field($updt)) {
			$this->msg->add("Error ! 無法 更新原資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}







//		$this->msg->add("成功 複製樣本記錄: [".$parm['style_code']."]。") ;

	
	// 主圖 先做兩個圖上傳[大圖]及[小圖]
			//圖檔目錄(以 pdt_id 來設圖檔檔名
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic1_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic1'] <> "none") {
						# 上傳圖相處理
			//上傳大圖 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 600, 600);
			//上傳小圖 150x150
			$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 150, 150);
				if ($up_result){
					$this->msg->add("成功上傳主圖");
				} else {
					$this->msg->add("上傳主圖 失敗");
				}
		}else{
			if(file_exists($style_dir.$parm['ori_id'].".jpg")){  // 複製之原檔有圖時
				copy($style_dir.$parm['ori_id'].".jpg",$style_dir.$pdt_id.".jpg");
				copy($style_dir."s".$parm['ori_id'].".jpg",$style_dir."s".$pdt_id.".jpg");
			} else {
				// 將gray 圖復製 一個
				copy($no_img,$style_dir.$pdt_id.".jpg");
				copy($no_img,$style_dir."s".$pdt_id.".jpg");
			}
		}


	// 次圖 A上傳
		if($parm['pic2_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic2'] <> "none"){
						# 上傳圖相處理
			//圖檔目錄(以 pdt_id 來設圖檔檔名 前面加上 "A" //上傳圖 320X320
			$upFile->setSaveTo($style_dir,"A".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic2'], 320, 320);
				if ($up_result){
					$this->msg->add("成功上傳 次圖一");
				} else {
					$this->msg->add("上傳次圖一 失敗");
				}
		}else{
			if(file_exists($style_dir."A".$parm['ori_id'].".jpg")){  // 複製之原檔有圖時
				copy($style_dir."A".$parm['ori_id'].".jpg",$style_dir."A".$pdt_id.".jpg");
			} else {
				// 將gray 圖復製 一個
				copy($no_img,$style_dir."A".$pdt_id.".jpg");
			}
		}

	// 次圖 B上傳
		if($parm['pic3_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if($parm['pic3'] <> "none"){
						# 上傳圖相處理
			//圖檔目錄(以 pdt_id 來設圖檔檔名 前面加上 "B" //上傳圖 320X320
			$upFile->setSaveTo($style_dir,"B".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic3'], 320, 320);
				if ($up_result){
					$this->msg->add("成功上傳 次圖二");
				} else {
					$this->msg->add("上傳次圖二 失敗");
				}
		}else{
			if(file_exists($style_dir."B".$parm['ori_id'].".jpg")){  // 複製之原檔有圖時
				copy($style_dir."B".$parm['ori_id'].".jpg",$style_dir."B".$pdt_id.".jpg");
			} else {
				// 將gray 圖復製 一個
				copy($no_img,$style_dir."B".$pdt_id.".jpg");
			}
		}


		return $pdt_id;

	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	搜尋 樣本 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
	
		$q_header = "SELECT * FROM smpl ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("style_code DESC , id DESC ");
		$srh->row_per_page = 12;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
if ($mode==1){
		if ($str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("dept = '$str'", "PHP_dept_code",$str,"部門別 = [ $str ]. "); }

		if ($str = strtoupper($argv['PHP_style_code']) )  { 
			$srh->add_where_condition("style_code LIKE '%$str%'", "PHP_style_code",$str,"搜尋 款式編號含有[ $str ]內容. "); }
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("cust = '$str'", "PHP_cust",$str,"搜尋 客戶 = [ $str ]. "); }
		if ($str = $argv['PHP_cust_ref'] )  { 
			$srh->add_where_condition("cust_ref LIKE '%$str%'", "PHP_cust_ref",$str,"搜尋客戶編號含有: [ $str ]內容 "); }

		if ($str = $argv['PHP_year'] )  { 
			$srh->add_where_condition("year = '$str'", "PHP_year",$str,"搜尋年份 = [ $str ]. "); }
		if ($str = $argv['PHP_season'] )  { 
			$srh->add_where_condition("season = '$str'", "PHP_season",$str,"搜尋季節 = [ $str ]. "); }
		if ($str = $argv['PHP_style_type'] )  { 
			$srh->add_where_condition("style_type = '$str'", "PHP_style_type",$str,"搜尋款式類別 = [ $str ]. "); }

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

		$op['smpl'] = $result;  // 資料錄 拋入 $op
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

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $smpl_code=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $smpl_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM smpl WHERE id='$id' ";
		} elseif ($smpl_code) {
			$q_str = "SELECT * FROM smpl WHERE style_code='$smpl_code' ";
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
		$q_str = "UPDATE smpl SET cust='"		.$parm['cust'].
							"',	cust_ref='"		.$parm['cust_ref'].
							"', season='"		.$parm['season'].
							"', updator='"		.$parm['updator'].
							"', size_type='"	.$parm['size_type'].
							"', size_scale='"	.$parm['size_scale'].
							"', style_type='"	.$parm['style_type'].
							"', style_relate='"	.$parm['style_relate'].
							"', quota='"		.$parm['quota'].
							"', chief_val='"	.$parm['chief_val'].
							"', ling_val='"		.$parm['ling_val'].
							"', lastupdate=		NOW()" .
							", memo='"			.$parm['comments'].
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
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";

		if($parm['pic1_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if(($parm['pic1'] != "") && ($parm['pic1'] != "none"))  {   // *** 注意 : 在unix下可能會是 "none" 不是 "".........
//echo "<br>[debug] parm['pic1']=====> ".$parm['pic1'];
//echo "<br>[debug] pic1 有更新檔案 ";
//exit;

						# 上傳圖相處理
				//2004/05/03 先檢查是否存在 如存在就先砍檔
//				$file1 = $style_dir.$pdt_id.".jpg";
			if(file_exists($style_dir.$pdt_id.".jpg")){
				unlink($style_dir.$pdt_id.".jpg") or die("無法刪除舊圖檔:".$pdt_id.".jpg");  // 刪除舊檔
			}
				//上傳大圖 600X600
				$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
				$up_result = $upFile->upload($parm['pic1'], 600, 600);

				//2004/05/03 先檢查是否存在 如存在就先砍檔
			if(file_exists($style_dir."s".$pdt_id.".jpg")){
				unlink ($style_dir."s".$pdt_id.".jpg") or die("無法刪除舊圖檔:"."s".$pdt_id.".jpg");  // 刪除舊檔
			}
				//上傳小圖 150x150
				$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
				$up_result = $upFile->upload($parm['pic1'], 150, 150);
		}

//echo "<br>[debug] pic1 沒有 更新檔案 ";
//exit;


	// 次圖 A上傳
		if($parm['pic2_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if(($parm['pic2'] != "") && ($parm['pic2'] != "none")){
			//2004/05/03 先檢查是否存在 如存在就先砍檔
			if(file_exists($style_dir."A".$pdt_id.".jpg")){
				unlink($style_dir."A".$pdt_id.".jpg") or die("無法刪除舊圖檔:"."A".$pdt_id.".jpg");  // 刪除舊檔
			}
						# 上傳圖相處理
			//圖檔目錄(以 pdt_id 來設圖檔檔名 前面加上 "A" //上傳圖 320x320
			$upFile->setSaveTo($style_dir,"A".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic2'], 320, 320);
		}
	// 次圖 B上傳
		if($parm['pic3_upload'])  {   // 2005/01/04 改成由javascript來驅動一個 hidden值 送入後台
//		if(($parm['pic3'] != "") && ($parm['pic3'] != "none")){
			//2004/05/03 先檢查是否存在 如存在就先砍檔
			if(file_exists($style_dir."B".$pdt_id.".jpg")){
				unlink($style_dir."B".$pdt_id.".jpg") or die("無法刪除舊圖檔:"."B".$pdt_id.".jpg");  // 刪除舊檔
			}
						# 上傳圖相處理
			//圖檔目錄(以 pdt_id 來設圖檔檔名 前面加上 "B" //上傳圖 320x320
			$upFile->setSaveTo($style_dir,"B".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic3'], 320, 320);
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
	$q_str = "UPDATE smpl SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 樣本記錄  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 供應商 資料表的 ID.");		    
			return false;
		}
		$q_str = "DELETE FROM smpl WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

			// 圖的刪除 -------
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  

			if(file_exists($style_dir.$id.".jpg")){
				unlink($style_dir.$id.".jpg") or die("無法刪除圖檔:".$id.".jpg");  // 刪除舊檔
			}
			if(file_exists($style_dir."s".$id.".jpg")){
				unlink ($style_dir."s".$id.".jpg") or die("無法刪除圖檔:"."s".$id.".jpg");  // 刪除舊檔
			}
			if(file_exists($style_dir."A".$id.".jpg")){
				unlink($style_dir."A".$id.".jpg") or die("無法刪除圖檔:"."A".$id.".jpg");  // 刪除舊檔
			}
			if(file_exists($style_dir."B".$id.".jpg")){
				unlink($style_dir."B".$id.".jpg") or die("無法刪除圖檔:"."B".$id.".jpg");  // 刪除舊檔
			}






		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl ".$where_str;

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

} // end class


?>