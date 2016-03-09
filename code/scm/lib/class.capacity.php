<?php 

#++++++++++++++++++++++ CAPACITY  class ##### 開發布料  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#   ->divert_month_su($qty, $ratio, $s_date, $f_date)   分配各月份之 su 傳出 陣列[200507=>254 ..]
#	->add_etp_su($parm)		加入 order monthy etp su [etp_su]
#	->add_fty_su($parm)		加入 order fty schedule su [fty_su]

#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class CAPACITY {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Database can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func


/*
################# ????   2005/07/28 轉入  class order   改成送出 csv ####################
#	divert_month_su($qty, $ratio, $s_date, $f_date)
# Detail description	: 將 數量 換成su 再分配到生產月份內 
#						: 傳回 陣列 ( 200505=>su , 200506=>su, ......
####################################################
function divert_month_su($qty, $ratio, $s_date, $f_date) {

 		$div = array();
   
		list($s_year,$s_mon,$s_day) = split("-",$s_date);
		list($f_year,$f_mon,$f_day) = split("-",$f_date);
		$days =	countDays($s_date,$f_date);
		$T_su = intval($ratio * $qty);		// 總 su 數
		$day_su = $T_su/$days;		// 每日產出 --(偏小)

		// 計算總共有幾個月份?
		$y = $f_year - $s_year;
		$m = 12*$y + (12-$s_mon+1) - (12-$f_mon);

echo "<br>total qty = ".$qty." are equal to  ".$T_su." s.u.<br>";
echo "<br>from ".$s_date." to ".$f_date." totally distribute for ".$m." 個月份; 計 ".$days."天 <br>";

			$su_year = $s_year;	// 年份的計數器
			$su_mon = $s_mon;	// 月份的計數器

			$divered_su =0;	// 已經統計的su數 做為最後月之減項

		for ($i=0; $i<$m; $i++){
			if($su_mon >12){
				$su_year = $su_year+1;
				$su_mon = 1;
			}

		$mon[$i] = sprintf("%04d%02d", $su_year, $su_mon);   // 月份的標記


			// 計算每月的天數 ---- 將 su 分配進入
		if($s_mon==$f_mon){   // 如果開始和最後是同月份時-----
				$d = $f_day - $s_day ;
				$su[$i] = intval($day_su * $d);
		}else{
			if ($i==0){  // 第一個月
				$d = getDaysInMonth($su_mon,$su_year)- intval($s_day);
				$su[$i] = intval($day_su * $d);
			} elseif($i==$m-1){  // 最後一個月
				$d = intval($f_day);
				$su[$i] = $T_su - $divered_su;
			} else{
				$d = getDaysInMonth($su_mon,$su_year);
				$su[$i] = intval($day_su * $d);
			}
		}

		$divered_su = $divered_su + $su[$i];

echo "<br>   distribute-month ==> ".$mon[$i]." ==>".$d." days ==> for ".$su[$i]." su" ;
		$su_mon = $su_mon+1;
		$tmp_m = $mon[$i];
		$div[$tmp_m] = $su[$i];   // 置入 array 
		}


return $div;





} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_etp_su($parm)		加入 order monthy su [etp_su]
#								傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_etp_su($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO etp_su (mon,
									factory,
									ord_num,
									etp_su) VALUES('".
									$parm['mon']."','".
									$parm['factory']."','".
									$parm['ord_num']."','".
									$parm['etp_su']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增 ".$parm['ord_num']." etp_su 資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		return $pdt_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_fty_su($parm)		加入 order fty schedule su [fty_su]
#								傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_fty_su($parm) {
					
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO fty_su (mon,
									factory,
									ord_num,
									etp_su) VALUES('".
									$parm['mon']."','".
									$parm['factory']."','".
									$parm['ord_num']."','".
									$parm['fty_su']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增 ".$parm['ord_num']." fty_su 資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		return $pdt_id;

	} // end func
*/

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_capacity_su($field,$fty='',$mon='', $tbl='capacity')	取出 某個  field的值
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_capacity_su($field, $fty='',$mon='',$tbl='capacity') {
		$sql = $this->sql;
		$row = array();

  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE factory='".$fty."' AND mon='".$mon."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		$row = $sql->fetch($q_result);

		$field_val = $row[0];

		return $field_val;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_su_field($fty,$mon,$su_field,$val)		更新 capacity 資料錄 某個單一欄位
#													###### 因為只有 su 欄可用直接加減 *******				
#													###### 因為只有 su 欄可用直接加減 *******				
#													###### 因為只有 su 欄可用直接加減 *******				
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_su_field($fty, $mon, $su_field, $val) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容  // 直接在 sql 內加入
	$q_str = "UPDATE capacity SET ".$su_field."=".$su_field."+".$val."  WHERE factory='".$fty."' AND mon='".$mon."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法更新資料庫內容.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



/*
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
		}
	}
		if (!$parm['cat']) {
			$this->msg->add("Error ! 請選擇 分類 。");
		}
		if (!$parm['name'] = trim($parm['name'])) {
			$this->msg->add("Error ! 請輸入布名，如無布名請輸入[N/A] 。");
		}

		// 相關欄位 必需要輸入時---------
		if (trim($parm['weight'])){
			if (!$parm['unit_wt']) {
				$this->msg->add("Error ! 請選擇 碼重單位 。");
			}
		}
		
		if (trim($parm['width'])){
			if (!$parm['width_unit']) {
				$this->msg->add("Error ! 請選擇 碼寬單位 。");
			}
		}

		if (trim($parm['price'])){
			if (!$parm['currency']) {
				$this->msg->add("Error ! 請選擇 幣值單位 。");
			}
		}
		if (trim($parm['price'])){
			if (!$parm['unit_price']) {
				$this->msg->add("Error ! 請選擇 計價單位 。");
			}
		}

		if (trim($parm['term'])){
			if (!$parm['location']) {
				$this->msg->add("Error ! 請選擇 供應條件的地點 。");
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
#	->search($mode=0,$where_str="")	搜尋 布料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
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
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC ");
		$srh->row_per_page = 48;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
if ($mode==1){
		if ($str = $argv['PHP_cat'] )  { 
			$srh->add_where_condition("cat = '$str'", "PHP_cat",$str,"布料類別 = [ $str ]. "); }
		if ($str = $argv['PHP_art_code'] )  { 
			$srh->add_where_condition("art_code LIKE '%$str%'", "PHP_name",$str,"搜尋 布料編號含有[ $str ]內容. "); }
		if ($str = $argv['PHP_name'] )  { 
			$srh->add_where_condition("name LIKE '%$str%'", "PHP_name",$str,"搜尋 布料名稱含有[ $str ]內容. "); }
		if ($str = $argv['PHP_content'] )  { 
			$srh->add_where_condition("content LIKE '%$str%'", "PHP_content",$str,"搜尋布料成份含有: [ $str ]內容 "); }
		if ($str = $argv['PHP_supl_ref'] )  { 
			$srh->add_where_condition("supl_ref LIKE '%$str%'", "PHP_supl_ref",$str,"搜尋供應商編號含有: [ $str ]內容 "); }
		if ($str = $argv['PHP_finish'] )  { 
			$srh->add_where_condition("finish LIKE '%$str%'", "PHP_finish",$str,"搜尋布料後整含有: [ $str ]內容 "); }
		if ($str = $argv['PHP_supl'] )  { 
			$srh->add_where_condition("supl = '$str'", "PHP_supl",$str,"供應商 = [ $str ]. "); }
}	

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 加入 $limit_entries
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
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['rows_this_page'] = $srh->rows_this_page;

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

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
			$this->msg->add("Error ! Database can't access!");
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

				//2004/05/03 先檢查是否存在 如存在就先砍檔
//			if(file_exists($style_dir."s".$pdt_id.".jpg")){
//				unlink ($style_dir."s".$pdt_id.".jpg") or die("無法刪除舊圖檔:"."s".$pdt_id.".jpg");  // 刪除舊檔
//			}
//				//上傳小圖 150x150
//				$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
//				$up_result = $upFile->upload($parm['pic1'], 150, 150);
		}

		
		return $pdt_id;
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
			$this->msg->add("Error ! Database can't access !");
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

*/





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>