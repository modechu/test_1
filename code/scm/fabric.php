<?php
session_start();
/*
session_register	('SCH_art_code');
session_register	('SCH_name');
session_register	('SCH_cat');
session_register	('SCH_content');
session_register	('SCH_finish');
session_register	('SCH_supl');
session_register	('SCH_ref');
session_register	('PHP_sr_startno');
*/
session_register	('sch_parm');
session_register	('sch_stock_parm');
require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";


$op = array();

$PHP_action = !empty($PHP_action) ? $PHP_action : '';
// echo $PHP_action;
switch ($PHP_action) {
//=======================================================


#++++++++++++++    Fabric +++++++++++++++++++  2009/01/01  +++++++++++++++++
#		 job 91    主料開發 
#++++++++++++++++++++++++++++++++++++++++++++  2009/01/01  +++++++++++++++++
#		case "fabric":		job 91
#		case "fabric_search": job 91
#		case "fabric_add":
#		case "do_fabric_add":
#		case "fabric_view":
#		case "fabric_edit":
#		case "do_fabric_edit":
#		case "fabric_del":
#		case "fabric_excel":
###################################################

//-----------------------------------------------------------------------------------
//		JOB  9-1   研究開發 [ 布料開發]
//		case "fabric":
//-----------------------------------------------------------------------------------
    case "fabric":
		check_authority('051',"view");

			// creat cust combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
			// create combo box for vendor fields.....
			$where_str =" WHERE supl_cat ='fabric' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','');  	

		$op['msg']= $fabric->msg->get(2);

		page_display($op,'051', $TPL_FABRIC_SEARCH);		    	    
	break;

//=======================================================
    case "fabric_search":

		check_authority('051',"view");
		if(isset($PHP_name))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"art_code"		=>  $PHP_art_code,
													"name"				=>  $PHP_name,
													"cat"					=>	$PHP_cat,
													"content"			=>	$PHP_content,
													"finish"			=>	$PHP_finish,
													"supl"				=>	$PHP_supl,
													"supl_ref"		=>	$PHP_supl_ref,
													"sr_startno"	=>	$PHP_sr_startno,
													"action"			=>	$PHP_action
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
			}

		if (!$op = $fabric->search(1)) {
			$op['msg']= $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			$op['msg']= $fabric->msg->get(2);

	//	$op['back_str'] = "&PHP_art_code=".$PHP_art_code."&PHP_name=".$PHP_name."&PHP_cat=".$PHP_cat."&PHP_content=".$PHP_content."&PHP_finish=".$PHP_finish."&PHP_supl=".$PHP_supl."&PHP_supl_ref=".$PHP_supl_ref;
		page_display($op,'051', $TPL_FABRIC);		    	    
		break;


//=======================================================
    case "fabric_add":
		
		check_authority('051',"add");

			// 取出年碼...
			$dt = decode_date(1);
			$year_code = substr($dt['year'],-2);
			
			// 取出選項資料 及傳入之參數
			// pre  編號....
		$op['fabric']['pre_art_code'] = "FK".$year_code."-...";

			// creat cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
		// creat fabric kind combo box
		$op['select_kind'] = $arry2->select($FABRIC_KIND,'','PHP_kind','select','');  	
			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='fabric' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,'','PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,'','PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,'','PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,'','PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,'','PHP_unit_wt','select','');  	
			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,'','PHP_unit_width','select','');  	



		    	    
		$op['msg']= $fabric->msg->get(2);

		page_display($op,'051', $TPL_FABRIC_ADD);		    	    
		break;

//=======================================================
	case "do_fabric_add":

		check_authority('051',"add");


		$parm = array(	"cat"					=>	$PHP_cat,
										"kind"				=>	$PHP_kind,
										"supl"				=>	$PHP_supl,
										"co"					=>	$PHP_co,
										"location"		=>	$PHP_location,
										"currency"		=>	$PHP_currency,
										"unit_price"	=>	$PHP_unit_price,
										"term"				=>	$PHP_term,
										"unit_wt"			=>	$PHP_unit_wt,

										"name"				=>	$PHP_name,
										"content"			=>	$PHP_content,
										"construct"		=>	$PHP_construct,
										"finish"			=>	$PHP_finish,
										"supl_ref"		=>	$PHP_supl_ref,
										"width"				=>	$PHP_width,
										"width_unit"	=>	$PHP_unit_width,
										"weight"			=>	$PHP_weight,
										"price"				=>	$PHP_price,
										"leadtime"		=>	$PHP_leadtime,

										"pic"					=>	$PHP_pic,
										"pic_upload"	=>	$PHP_pic_upload,

										"remark"			=>	$PHP_remark
			);
				$op['fabric'] = $parm;
			$check = $fabric->check($parm);
	// .....................................輸入資料 有錯誤時  再回到樣本輸入表單
	if (!$check) {  


				// 取出年碼...
				$dt = decode_date(1);
				$year_code = substr($dt['year'],-2);
				
				// 取出選項資料 及傳入之參數
				// PRE 編號....
		$op['fabric']['pre_art_code'] = "F".$year_code.".....";

			// creat fabric cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,$op['fabric']['cat'],'PHP_cat','select','');  	
			// creat fabric kind combo box
		$op['select_kind'] = $arry2->select($FABRIC_KIND,$op['fabric']['kind'],'PHP_kind','select','');  	
			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='fabric' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,$op['fabric']['supl'],'PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,$op['fabric']['co'],'PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,$op['fabric']['location'],'PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,$op['fabric']['currency'],'PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,$op['fabric']['unit_price'],'PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,$op['fabric']['term'],'PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,$op['fabric']['unit_wt'],'PHP_unit_wt','select','');  	

			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,$op['fabric']['width_unit'],'PHP_unit_width','select',''); 		

		$op['msg']= $fabric->msg->get(2);
	
		page_display($op,'051', $TPL_FABRIC_ADD);		    	    
		break;
	}

	// 輸入項正確後............after check input.........
	// 設定 ART_CODE ..........
	if ($PHP_kind == "梭織布") { $kind = "W"; };
	if ($PHP_kind == "針織布") { $kind = "N"; };
			
	// 取出年碼...
	$dt = decode_date(1);
	$year_code = substr($dt['year'],-2);

	$dept_code = "K";      //.................................暫時以 K 部門編入 .............
	$art_code = $dept->get_fabric_serious($dept_code, $year_code,$kind);  // 也同時更新dept檔內的num值[csv]

	$parm['art_code'] = $art_code;
	$GLOBALS['PHP_art_code'] = $art_code; //為了下面的search 進入 search時的判斷式

//		$op['fabric'] = $parm;

		$f1 = $fabric->add($parm);

		if (!$f1) {  // 未成功輸入資料時
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			
			// 列出該筆記錄
			$op['fabric'] = $fabric->get($f1);  //取出該筆記錄


			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			
				# 記錄使用者動態
		$log->log_add(0,"71A","成功新增 開發布料記錄 :".$op['fabric']['art_code']);

//		$op['back_str'] = "&PHP_art_code=&PHP_name=&PHP_cat=&PHP_content=&PHP_finish=&PHP_supl=&PHP_supl_ref=";

		$op['search'] = '';
		$op['msg'] = $fabric->msg->get(2);

		page_display($op,'051', $TPL_FABRIC_VIEW);	    	    
	break;

//========================================================================================	
	case "fabric_view":

		check_authority('051',"view");

		$op['fabric'] = $fabric->get($PHP_id);  //取出該筆記錄

		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// 加入返回前頁 2005/05/05
//		$op['back_str'] = "&PHP_art_code=".$PHP_art_code."&PHP_name=".$PHP_name."&PHP_cat=".$PHP_cat."&PHP_content=".$PHP_content."&PHP_finish=".$PHP_finish."&PHP_supl=".$PHP_supl."&PHP_supl_ref=".$PHP_supl_ref."&PHP_sr_startno=".$PHP_sr_startno;

//------------------------------

			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'] = $fabric->msg->get(2);

		page_display($op,'051', $TPL_FABRIC_VIEW);   	    
	break;
//=======================================================
	case "fabric_edit":

		check_authority('051',"edit");
		
		$op['fabric'] = $fabric->get($PHP_id);
		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

// 加入返回前頁 2005/05/05
//		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_art_code=".$cgi_1."&PHP_cat=".$cgi_2."&PHP_name=".$cgi_3."&PHP_content=".$cgi_4."&PHP_supl_ref=".$cgi_5."&PHP_supl=".$cgi_6."&PHP_finish=".$cgi_7;

//		$op['back_str'] = $back_str;
//------------------------------
			// creat fabric cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,$op['fabric']['cat'],'PHP_cat','select','');  	
//			// creat fabric kind combo box
//		$op['select_kind'] = $arry2->select($FABRIC_KIND,$op['fabric']['kind'],'PHP_kind','select','');  	
			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='fabric' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,$op['fabric']['supl'],'PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,$op['fabric']['co'],'PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,$op['fabric']['location'],'PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,$op['fabric']['currency'],'PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,$op['fabric']['unit_price'],'PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,$op['fabric']['term'],'PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,$op['fabric']['unit_wt'],'PHP_unit_wt','select','');  	
		
			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,$op['fabric']['width_unit'],'PHP_unit_width','select',''); 

			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'] = $fabric->msg->get(2);	
		$op['search'] = '1';
 
		page_display($op,'051', $TPL_FABRIC_EDIT); 
		break;

//=======================================================
	case "do_fabric_edit":

		check_authority('051',"edit");

		$argv = array(	"id"					=>	$PHP_id,
										"art_code"		=>	$PHP_art_code,
										"cat"					=>	$PHP_cat,
										"supl"				=>	$PHP_supl,
										"co"					=>	$PHP_co,
										"location"		=>	$PHP_location,
										"currency"		=>	$PHP_currency,
										"unit_price"	=>	$PHP_unit_price,
										"term"				=>	$PHP_term,
										"unit_wt"			=>	$PHP_unit_wt,

										"name"				=>	$PHP_name,
										"content"			=>	$PHP_content,
										"construct"		=>	$PHP_construct,
										"finish"			=>	$PHP_finish,
										"supl_ref"		=>	$PHP_supl_ref,
										"width"				=>	$PHP_width,
										"width_unit"	=>	$PHP_unit_width,
										"weight"			=>	$PHP_weight,
										"price"				=>	$PHP_price,
										"leadtime"		=>	$PHP_leadtime,

										"pic"					=>	$PHP_pic,
										"pic_upload"	=>	$PHP_pic_upload,

										"remark"			=>	$PHP_remark
			);


				$op['fabric'] = $argv;
			$check = $fabric->check($argv,1);
	// .....................................輸入資料 有錯誤時  再回到樣本輸入表單
	if (!$check) {  
			// creat fabric cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,$op['fabric']['cat'],'PHP_cat','select','');  	

			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='fabric' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,$op['fabric']['supl'],'PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,$op['fabric']['co'],'PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,$op['fabric']['location'],'PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,$op['fabric']['currency'],'PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,$op['fabric']['unit_price'],'PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,$op['fabric']['term'],'PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,$op['fabric']['unit_wt'],'PHP_unit_wt','select','');  	

			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,$op['fabric']['width_unit'],'PHP_unit_width','select',''); 

			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

// 加入返回前頁 2005/05/05   ????????????????????? 還不確定對不對
//		$op['back_str'] = $PHP_back_str;
//------------------------------

		$op['msg']= $fabric->msg->get(2);
		page_display($op,'051', $TPL_FABRIC_EDIT); 

	break;
	}

	// 輸入項正確後............after check input.........

		if (!$fabric->edit($argv)) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# 記錄使用者動態
		$message = "成功更新開發布料資料:[$PHP_art_code]的內容‧";
		$log->log_add(0,"71E",$message);

		$op['fabric'] = $fabric->get($PHP_id);  //取出該筆記錄

		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'] = $fabric->msg->get(2);
		$op['msg'][] = $message;

// 加入返回前頁 2005/05/05

//		$op['back_str'] = $PHP_back_str;
//------------------------------


		$op['search'] = '1';
		page_display($op,'051', $TPL_FABRIC_VIEW); 
    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fabric_del":

			// 必需要 manager login 才能真正的刪除 SUPLIER
		check_authority('051',"edit");

		$op['fabric'] = $fabric->get($PHP_id);
		
		if (!$fabric->del($PHP_id)) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# 記錄使用者動態

		$message = "刪除 開發布類記錄:[".$op['fabric']['art_code']."] 。";
		$log->log_add(0,"71D",$message);

		if (!$op = $fabric->search(1)) {
			$op['msg']= $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['msg']= $fabric->msg->get(2);
		$op['msg'][] = $message;

		unset($PHP_id);
	 
		page_display($op,'051', $TPL_FABRIC); 
   	    
	break;

//-------------------------------------------------------------------------

	case "fabric_excel":

		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! 您沒有這項權限!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$fb = $fabric->search(1,'',3000)) {  // 2005/05/16 加入第三個參數 改變搜尋大小
			$op['msg']= $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = sizeof($fb['fabric']);
		//echo "<br> count number.............".$a;

	
	
	require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
	require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  function HeaderingExcel($filename) {
		  header("Content-type: application/vnd.ms-excel");
		  header("Content-Disposition: attachment; filename=$filename" );
		  header("Expires: 0");
		  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		  header("Pragma: public");
		  }

	  // HTTP headers
	  HeaderingExcel('list.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('fabric');

	// 寫入 title

	  // Format for the headings
	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern();
	  $formatot->set_fg_color('navy');

	  $worksheet1->set_column(0,0,15);
	  $worksheet1->set_column(0,1,10);
	  $worksheet1->set_column(0,2,20);
	  $worksheet1->set_column(0,3,10);
	  $worksheet1->set_column(0,4,10);

	  $worksheet1->set_column(0,5,30);
	  $worksheet1->set_column(0,6,10);
	  $worksheet1->set_column(0,7,20);
	  $worksheet1->set_column(0,8,15);
	  $worksheet1->set_column(0,9,15);
	  $worksheet1->set_column(0,10,10);
	  $worksheet1->set_column(0,11,15);
	  $worksheet1->set_column(0,12,15);
	  $worksheet1->set_column(0,13,10);
	  $worksheet1->set_column(0,14,30);

	  $worksheet1->write_string(1,0,"Artical",$formatot);
	  $worksheet1->write_string(1,1,"category",$formatot);
	  $worksheet1->write_string(1,2,"Art. name",$formatot);
	  $worksheet1->write_string(1,3,"suplier.",$formatot);
	  $worksheet1->write_string(1,4,"suplier ref.",$formatot);

	  $worksheet1->write_string(1,5,"content",$formatot);
	  $worksheet1->write_string(1,6,"construct",$formatot);
	  $worksheet1->write_string(1,7,"finish",$formatot);
	  $worksheet1->write_string(1,8,"width",$formatot);
	  $worksheet1->write_string(1,9,"weight",$formatot);
	  $worksheet1->write_string(1,10,"source",$formatot);
	  $worksheet1->write_string(1,11,"price",$formatot);
	  $worksheet1->write_string(1,12,"term",$formatot);
	  $worksheet1->write_string(1,13,"leadtime",$formatot);
	  $worksheet1->write_string(1,14,"remark",$formatot);




	for ($i=0;$i < sizeof($fb['fabric']);$i++){
		$art_code = $fb['fabric'][$i]['art_code'];
		$cat = $fb['fabric'][$i]['cat'];
		$supl = $fb['fabric'][$i]['supl'];
		$supl_ref = $fb['fabric'][$i]['supl_ref'];
		$name = $fb['fabric'][$i]['name'];
		$content = $fb['fabric'][$i]['content'];
		$construct = $fb['fabric'][$i]['construct'];
		$finish = $fb['fabric'][$i]['finish'];
		$width = $fb['fabric'][$i]['width'].' '.$fb['fabric'][$i]['width_unit'];
		$weight = $fb['fabric'][$i]['weight'].' '.$fb['fabric'][$i]['unit_wt'];
		$construct = $fb['fabric'][$i]['construct'];
		$co = $fb['fabric'][$i]['co'];
		if ($fb['fabric'][$i]['price']){
			$price = $fb['fabric'][$i]['currency'].' '.$fb['fabric'][$i]['price'].'/'.$fb['fabric'][$i]['unit_price'];
		}else{
			$price = '';
		}
		$term = $fb['fabric'][$i]['term'].' '.$fb['fabric'][$i]['location'];
		if ($fb['fabric'][$i]['leadtime']){
			$leadtime = $fb['fabric'][$i]['leadtime'].' days';
		}else{
			$leadtime = '';
		}
		$remark = $fb['fabric'][$i]['remark'];


	  $worksheet1->write_string($i+2,0,$art_code);
	  $worksheet1->write_string($i+2,1,$cat);
	  $worksheet1->write_string($i+2,2,$name);
	  $worksheet1->write_string($i+2,3,$supl);
	  $worksheet1->write_string($i+2,4,$supl_ref);
	  $worksheet1->write_string($i+2,5,$content);
	  $worksheet1->write_string($i+2,6,$construct);
	  $worksheet1->write_string($i+2,7,$finish);
	  $worksheet1->write_string($i+2,8,$width);
	  $worksheet1->write_string($i+2,9,$weight);
	  $worksheet1->write_string($i+2,10,$co);
	  $worksheet1->write_string($i+2,11,$price);
	  $worksheet1->write_string($i+2,12,$term);
	  $worksheet1->write_string($i+2,13,$leadtime);
	  $worksheet1->write_string($i+2,14,$remark);

	
	
	}


  $workbook->close();


	break;

//end of fabric

//-----------------------------------------------------------------------------------
//		JOB  9-1   研究開發 [ 布料開發]
//		case "fabric":
//-----------------------------------------------------------------------------------
    case "stock":
		check_authority('052',"view");

		$sales_dept_ary = get_full_sales_dept(); // 取出 業務的部門 [不含K0] ------
		$op['dept_ary'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  

//取出客戶代號
		$where_str=" order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	
		for ($i=0; $i< sizeof($cust_def); $i++)
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現

		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 


			// creat cust combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
			// create combo box for vendor fields.....
			$where_str =" WHERE supl_cat ='fabric' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','');  	

		$op['msg']= $fabric->msg->get(2);

		page_display($op,'052', $TPL_STOCK_SEARCH);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "stock_search_bom":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "stock_search_bom":   //  先找出製造令.......

		check_authority('052',"add");
		$PHP_full = 1;
		if(isset($PHP_num))
		{

			$sch_parm = array();
			$sch_parm = array(	"PHP_num"						=>  $PHP_num,
													"PHP_cust"					=>  $PHP_cust,
													"PHP_dept_code"			=>	$PHP_dept_code,
													"PHP_sr_startno"		=>	$PHP_sr_startno,
													"PHP_action"				=>	$PHP_action
				);
			 $_SESSION['sch_parm']	= $sch_parm;
			}else{
				if(isset($PHP_sr_startno)) $_SESSION['sch_parm']['PHP_sr_startno'] = $PHP_sr_startno;
			}

		
			
			if (!$op = $bom->search_cfm(1)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['msg']= $bom->msg->get(2);				
		
		page_display($op,'052', $TPL_STOCK_BOM_LIST);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "stock_wi_view":			job 74
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "stock_wi_view":

		check_authority('052',"add");

		// 將 製造令 完整show out ------
		//  wi 主檔

			if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}

//--------------------------------------------------------------------------
		//  wi_qty 數量檔
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		$op['other_lots'] = $op['lots_use'];
		
		
		//-----------------------------------------------------------------

		//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['lots_use'] = $apply->get_lots_det($op['wi']['id']);  //取出該筆 bom 內ALL主料記錄
		 $num_bom_lots = count($op['lots_use']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}



		$op['msg'] = $wi->msg->get(2);
		if (isset($PHP_msg)) $op['msg'][] = $PHP_msg;

		page_display($op,'052', $TPL_STOCK_VIEW_WI);	
		break;
	
//=======================================================
    case "stock_search":

		check_authority('052',"view");
		if(isset($PHP_name))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"art_code"		=>  $PHP_art_code,
													"name"				=>  $PHP_name,
													"cat"					=>	$PHP_cat,
													"content"			=>	$PHP_content,
													"finish"			=>	$PHP_finish,
													"supl"				=>	$PHP_supl,
													"supl_ref"		=>	$PHP_supl_ref,
													"fty"					=>	$PHP_fty,
													"sr_startno"	=>	$PHP_sr_startno,
													"action"			=>	$PHP_action
				);
			}else if(!isset($sch_parm['art_code'])){		
			$sch_parm = array();
			$sch_parm = array(	"art_code"		=>  '',
													"name"				=>  '',
													"cat"					=>	'',
													"content"			=>	'',
													"finish"			=>	'',
													"supl"				=>	'',
													"supl_ref"		=>	'',
													"fty"					=>	'',
													"sr_startno"	=>	'',
													"action"			=>	'stock_search'
				);				
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
			}

		if (!$op = $fabric->search_stock(1)) {
			$op['msg']= $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		for($i=0; $i<sizeof($op['fabric']); $i++)
		{
			if(file_exists("./fab_stock/s".$op['fabric'][$i]['id'].".jpg")){
				$op['fabric'][$i]['main_pic'] = "./fab_stock/s".$op['fabric'][$i]['id'].".jpg";
			} else {
				$op['fabric'][$i]['main_pic'] = "./images/graydot.gif";
			}
		}

		$op['msg']= $fabric->msg->get(2);
		if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		page_display($op,'052', $TPL_STOCK);		    	    
		break;	
		
//=======================================================
    case "stock_add":
		
		check_authority('052',"add");

		// 取出年碼...
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-2);
		
		$op['po_sotck'] = $fabric->get_po_stock($PHP_mat_code,$PHP_color,$PHP_ord_num);
		
		$fab_rec = $lots->get(0, $PHP_mat_code);
		$op['po_sotck']['comp'] = $fab_rec['comp'];
		$op['po_sotck']['specify'] = $fab_rec['specify'];
		$op['po_sotck']['width'] = $fab_rec['width'];
		$op['po_sotck']['weight'] = $fab_rec['weight'];
		$op['po_sotck']['cons'] = $fab_rec['cons'];
		
		$op['ord_num'] = $PHP_ord_num;
		$op['mat_code'] = $PHP_mat_code;
		$op['color'] = $PHP_color;
		
		
		
		
		// 取出選項資料 及傳入之參數
		// pre  編號....
		$op['fabric']['pre_art_code'] = "FS".$year_code."-...";

			// creat cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
		// creat fabric kind combo box
		//$op['select_kind'] = $arry2->select($FABRIC_KIND,'','PHP_kind','select','');  	
		// create combo box for suplier .....
		$where_str =" WHERE supl_cat ='fabric' ORDER BY id";
		$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$vndr_no = $supl->get_fields('vndr_no',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,$op['po_sotck']['sup_code'],'PHP_supl','select','',$vndr_no);  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,'','PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,'','PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,'','PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,'','PHP_term','select','');  	
/*			
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,'','PHP_unit_wt','select','');  	
			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,'','PHP_unit_width','select','');  	
*/
		    	    
			// creat width unit combo box
		$op['select_unit'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');  	

			// creat width unit combo box
		$op['select_fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
		    	    



		    	    
		$op['msg']= $fabric->msg->get(2);

		page_display($op,'052', $TPL_STOCK_ADD);		    	    
		break;		
		
		
//=======================================================
    case "other_stock_add":
		
		check_authority('052',"add");

		// 取出年碼...
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-2);
		
		$op['po_sotck']['smpl_code'] = $PHP_ord_num;
		$op['po_sotck']['mat_code'] = $PHP_mat_code;
		
		$fab_rec = $lots->get(0, $PHP_mat_code);
		$op['po_sotck']['comp'] = $fab_rec['comp'];
		$op['po_sotck']['specify'] = $fab_rec['specify'];
		$op['po_sotck']['width'] = $fab_rec['width'];
		$op['po_sotck']['weight'] = $fab_rec['weight'];
		$op['po_sotck']['cons'] = $fab_rec['cons'];
		
		$op['ord_num'] = $PHP_ord_num;
		$op['mat_code'] = $PHP_mat_code;
		$op['color'] = $PHP_color;
		
		
		
		
		// 取出選項資料 及傳入之參數
		// pre  編號....
		$op['fabric']['pre_art_code'] = "FS".$year_code."-...";

			// creat cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
		// creat fabric kind combo box
		//$op['select_kind'] = $arry2->select($FABRIC_KIND,'','PHP_kind','select','');  	
		// create combo box for suplier .....
		$where_str =" WHERE supl_cat ='fabric' ORDER BY id";
		$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$vndr_no = $supl->get_fields('vndr_no',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','',$vndr_no);  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,'','PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,'','PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,'','PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,'','PHP_term','select','');  	
/*			
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,'','PHP_unit_wt','select','');  	
			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,'','PHP_unit_width','select','');  	
*/
		    	    
			// creat width unit combo box
		$op['select_unit'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');  	

			// creat width unit combo box
		$op['select_fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
		    	    



		    	    
		$op['msg']= $fabric->msg->get(2);

		page_display($op,'052', $TPL_STOCK_ADD);		    	    
		break;				
		
//=======================================================
	case "do_stock_add":

		check_authority('052',"add");

		if($PHP_fty2)$PHP_fty = $PHP_fty2;
		$parm = array(	"cat"					=>	$PHP_cat,
										"supl"				=>	$PHP_supl,
										"co"					=>	$PHP_co,
										"name"				=>	$PHP_name,
										"supl_ref"		=>	$PHP_supl_ref,
										"pic"					=>	$PHP_pic,
										"pic_upload"	=>	$PHP_pic_upload,
										"remark"			=>	$PHP_remark,										
										'qty'					=>	$PHP_qty,
										'unit'				=>	$PHP_unit,
										'fty'					=>	$PHP_fty,
										
										'mat_code'		=>	$PHP_mat_code,
										'bom_color'		=>	$PHP_color,
										'ord_num'			=>	$PHP_ord_num,
										'est_qty'			=>	$PHP_est_qty,
										'po_unit'			=>	$PHP_po_unit
			);
			$op['fabric'] = $parm;

		$f1 = $fabric->add_stock($parm);

		if (!$f1) {  // 未成功輸入資料時
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			
# 記錄使用者動態
		$message = "成功新增 庫存布料記錄 :".$op['fabric']['mat_code'];
		$log->log_add(0,"74A",$message);

		$redir_str = 'fabric.php?PHP_action=stock_view&PHP_id='.$f1."&PHP_msg=".$message;
		redirect_page($redir_str);
		break;	

	
//========================================================================================	
	case "stock_view":

		check_authority('052',"view");
		$op['fabric'] = $fabric->get_stock($PHP_id);  //取出該筆記錄
		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['order'] = $order->get('',$op['fabric']['ord_num']);
// 檢查 相片是否存在
		if(file_exists("./picture/".$op['order']['order_num'].".jpg")){
			$op['order']['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['order']['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['order']['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['order']['height'] = 1;

//------------------------------

			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/fab_stock/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = _pic_url('big','200',3,'fab_stock',$op['fabric']['id'],'jpg','','');
		} else {    
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'] = $fabric->msg->get(2);
		if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		
		page_display($op,'052', $TPL_STOCK_VIEW);   	    
	break;	
	
	

//=======================================================
	case "stock_edit":

		check_authority('052',"edit");
		
		$op['fabric'] = $fabric->get_stock($PHP_id);
		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//------------------------------
			// creat fabric cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,$op['fabric']['cat'],'PHP_cat','select','');  	
//			// creat fabric kind combo box
//		$op['select_kind'] = $arry2->select($FABRIC_KIND,$op['fabric']['kind'],'PHP_kind','select','');  	
			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='fabric' ";
		$where_str =" WHERE supl_cat ='fabric' ORDER BY id";
		$supl_def = $supl->get_fields('supl_s_name',$where_str);   // 取出 供應商類別代號
		$vndr_no = $supl->get_fields('vndr_no',$where_str);   // 取出 供應商類別代號
		$op['select_supl'] = $arry2->select($supl_def,$op['fabric']['supl'],'PHP_supl','select','',$vndr_no);  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,$op['fabric']['co'],'PHP_co','select','');  	
/*
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,$op['fabric']['unit_wt'],'PHP_unit_wt','select','');  	
		
			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,$op['fabric']['width_unit'],'PHP_unit_width','select',''); 
*/
			// creat width unit combo box
		$op['select_unit'] = $arry2->select($LOTS_PRICE_UNIT,$op['fabric']['unit'],'PHP_unit','select','');  	

			// creat width factory combo box
		$op['select_fty'] = $arry2->select($FACTORY,$op['fabric']['fty'],'PHP_fty','select','');  
		for($i=0; $i<sizeof($FACTORY); $i++) if($FACTORY[$i] == $op['fabric']['fty'])$op['fabric']['fty'] = '';

			// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/fab_stock/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fab_stock/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'] = $fabric->msg->get(2);	
		$op['search'] = '1';
 
		page_display($op,'052', $TPL_STOCK_EDIT); 
		break;

//=======================================================
	case "do_stock_edit":

		check_authority('052',"edit");
		if($PHP_fty2)$PHP_fty = $PHP_fty2;
		$argv = array(	"id"					=>	$PHP_id,
										"art_code"		=>	$PHP_art_code,
										"cat"					=>	$PHP_cat,
										"supl"				=>	$PHP_supl,
										"co"					=>	$PHP_co,
										
										"name"				=>	$PHP_name,
										"supl_ref"		=>	$PHP_supl_ref,

										"pic"					=>	$PHP_pic,
										"pic_upload"	=>	$PHP_pic_upload,

										"remark"			=>	$PHP_remark,
										
										"qty"					=>	$PHP_qty,
										"unit"				=>	$PHP_unit,
										"fty"					=> 	$PHP_fty
			);


				$op['fabric'] = $argv;

	// 輸入項正確後............after check input.........

		if (!$fabric->edit_stock($argv)) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# 記錄使用者動態
		$message = "成功更新庫存布料資料:[$PHP_art_code]的內容‧";
		$log->log_add(0,"74E",$message);

		$redir_str = 'fabric.php?PHP_action=stock_view&PHP_id='.$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);
		break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "stock_del":

			// 必需要 manager login 才能真正的刪除 SUPLIER
		check_authority('052',"edit");

		$op['fabric'] = $fabric->get_stock($PHP_id);
		
		if (!$fabric->del_stock($PHP_id)) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# 記錄使用者動態

		$message = "刪除 庫存布類記錄:[".$op['fabric']['art_code']."] 。";
		$log->log_add(0,"71D",$message);

		$redir_str = 'fabric.php?PHP_action=stock_search&PHP_msg='.$message;
		redirect_page($redir_str);
		break;

	
	

//-------------------------------------------------------------------------
//default;
case "fab_stock":

echo
'
<script language="javascript" src="./js/jquery.min.js"></script>
';

//setting
$FTY = 'LY';
$N_DAY = date("Y-m-d");
$D_DAY = increceDaysInDate(date("Y-m-d"),-30);

// 狀態顯示
$status = !empty($_POST['status']) ? $_POST['status'] : (!empty($_GET['status'])?$_GET['status']:'1');
$dfrt = !empty($_POST['dfrt']) ? $_POST['dfrt'] : (!empty($_GET['dfrt'])?$_GET['dfrt']:'0');
$MQTY = !empty($_POST['MQTY']) ? $_POST['MQTY'] : (!empty($_GET['MQTY'])?$_GET['MQTY']:'0');
$N_DAY = !empty($_POST['N_DAY']) ? $_POST['N_DAY'] : (!empty($_GET['N_DAY'])?$_GET['N_DAY']:date("Y-m-d"));

// line ( id => line )
$line = array();
$sql = "
SELECT * 
FROM `pdt_saw_line` 
WHERE `fty` LIKE '".$FTY."'
AND `del_mk` = '0'
ORDER BY `pdt_saw_line`.`line` ASC 
";
$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  // echo $row['id'].' - '.$row['line'].'<br>';
  $line[$row['id']] = $row['line'];
}
// print_r($line);
// echo '<p>';



// order
$order = array();
$order_line = array();
$scd = array();
$sql = "
SELECT `qty`,`line_id`,`ord_num`,`status`,`rel_ets`,`rel_etf`,`pdt_qty` 
FROM `schedule` 
WHERE `ets` > '".$N_DAY."'
AND `fty` LIKE '".$FTY."'
AND `status` = '0'
ORDER BY `schedule`.`rel_ets` ASC 
";

$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  // echo $line[$row['line_id']].' - '.$row['ord_num'].'<br>';
  $order[] = $row;
}
// print_r($order);
// exit;

// 依生產線組合訂單
foreach($line as $keys => $vals){
  foreach($order as $key => $val){
    if( $keys == $val['line_id'] ){
      $order_line[$keys][] = array( 'qty'     => $val['qty'] ,
                                    'line_id' => $val['line_id'] ,
                                    'ord_num' => $val['ord_num'] ,
                                    'status'  => $val['status'] ,
                                    'rel_ets' => $val['rel_ets'] ,
                                    'rel_etf' => $val['rel_etf'] ,
                                    'pdt_qty' => $val['pdt_qty']);
    }
  }
}
// print_r($order_line);
// exit;

// 排列生產線順序，列出訂單
foreach($line as $keys => $vals){
  if(!empty($order_line[$keys])){
    foreach($order_line[$keys] as $key => $val){
      if( $val['status'] == '2' ){
        $scd[$keys] = array();
      }
      $scd[$keys][] = array(  'qty'     => $val['qty'] ,
                              'ord_num' => $val['ord_num'] ,
                              'rel_ets' => $val['rel_ets'] ,
                              'rel_etf' => $val['rel_etf'] ,
                              'pdt_qty' => $val['pdt_qty']);
    }
  }
}
// print_r($scd);
// exit;


// wi
$wi = array();

$sql = "
SELECT `id`,`wi_num`,`etd`,`bcfm_date`,`smpl_id` 
FROM `wi` 
WHERE `etd`  > '".$D_DAY."'
";

$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  $wi[$row['wi_num']] = array( 'id' => $row['id'] , 'wi_num' => $row['wi_num'] , 'etd' => $row['etd'] , 'bcfm_date' => $row['bcfm_date'] , 'smpl_id' => $row['smpl_id'] );
}
// print_r($wi);


$D_DAY = increceDaysInDate(date("Y-m-d"),-210);

//bom_lots 
$bom_lots = array();
$sql = "
SELECT `id`,`wi_id`,`lots_used_id`,`qty`,`color` 
FROM `bom_lots` 
WHERE `k_date` > '".$D_DAY."'
";
$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  $bom_lots[] = array( 'id' => $row['id'] , 'wi_id' => $row['wi_id'] , 'lots_used_id' => $row['lots_used_id'] , 'qty' => $row['qty'] , 'color' => $row['color'] );
}



//lots_use 把主料全抓出來
$lots_use = array();
$sql = "
SELECT * 
FROM `lots_use` 
";
// WHERE `add_date` > '".$D_DAY."'
$sql_query = $mysql->query($sql);
while( $row = $mysql->fetch($sql_query) ){
  $lots_use[$row['id']] = $row;
}


$html_str =
'
<style type="text/css">@import url(./js/calendar/skins/aqua/theme.css);</style>
<script type="text/javascript" src="./js/calendar/calendar.js"></script>
<script type="text/javascript" src="./js/calendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="./js/calendar/calendar-setup.js"></script>
<script language="javascript" src="./js/open_detial.js"></script>
<script language="javascript">

function Switch(obj){
  if ( $("tr[id^="+obj+"]").css("display") == "block" ){
    $("span[id^="+obj+"]").html("Fabric Detail OPEN");
    $("tr[id^="+obj+"]").css("display","none");
  } else {
    $("span[id^="+obj+"]").html("Fabric Detail CLOSE");
    $("tr[id^=A]").css("display","none");
    $("tr[id^=B]").css("display","none");
    $("tr[id^="+obj+"]").css("display","block");
  }
}

function nwin_ord(ord){
  var url ="./schedule.php?PHP_action=sch_ord_view&PHP_ord_num="+ord;
  var nm = "order";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

function monitor(line,date){
  var url ="./monitor.php?PHP_action=saw_line_pic&PHP_line="+line+"&PHP_date="+date+"&PHP_fty=LY";
  var nm = "monitor";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}

function bom(bom){
  var url ="./bom.php?PHP_action=bom_view&PHP_ex_order=1&PHP_id="+bom;
  var nm = "bom";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}


function lots(lots){
  var url ="./index2.php?PHP_action=lots_view&PHP_code="+lots+"&SCH_lots_code=&SCH_lots_name=&SCH_comp=&SCH_cat1=&SCH_cat2=&SCH_mile=&SCH_cons=";
  var nm = "lots";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}



function marker(ord){
  var url ="./marker.php?PHP_action=marker_ord_add&PHP_order_num="+ord;
  var nm = "marker";
  window.open2(url,nm,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600 top=50, left=100");
}


Calendar.setup(
  {
    inputField  : "N_DAY",         // ID of the input field
    button			:	"f_trigger_c",
    ifFormat    : "%Y-%m-%d"    // the date format
  }
);
</script>
<br>
';


// scd.ord_num => bom_lots.wi_id
$mod = array();


if( $status == '1' ){
  unset($scd);
  $scd[0] = $order;
}

foreach($scd as $keys => $vals){
  foreach($vals as $key => $val){
    // 狀態顯示
    if( $status == '1' ){
      $keys = $val['line_id'];
    }  
    $html_strs = '';
    $html_strm = '';
    // 有 bom 顯示
    if( !empty($wi[$val['ord_num']]) ){
$html_strm .=
'
  <tr>
    <td colspan="6" bgcolor="#000000">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="font-weight: bold; font-style:oblique; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
          <td align="left"><span style="cursor:pointer;color:#ff0000;" onclick="nwin_ord(\''.$val['ord_num'].'\')">'.$val['ord_num'].'</span> &nbsp; (LINE:<span style="cursor:pointer;color:#FFFF00;" onclick="monitor(\''.$line[$keys].'\',\''.$N_DAY.'\')">'.$line[$keys].'</span>) &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  <span id="'.$val['ord_num'].'" onclick="Switch(\''.$val['ord_num'].'\')" style="cursor:pointer;color:#33FF00;">Fabric Detail OPEN</span></td>
          <td align="right">'.$val['rel_ets'].' ~ '.$val['rel_etf'].'</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr style="font-weight: bold; font-style:oblique; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000;">
    <td colspan="6" bgcolor="#FFFFFF">
      <table border="0" cellpadding="3" cellspacing="1">
        <tr>
          <td>'._pic_url('big','100',3,'picture',$val['ord_num'],'jpg','','').'</td>
          <td>
            <table border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td>
                  <table border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
                    <tr style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
                      <td bgcolor="#003399">BOM</td>
                      <td bgcolor="#003399">ETD</td>
                      <td bgcolor="#003399">Q\'TY</td>
                    </tr>
                    <tr style="font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                      <td bgcolor="#FFFFFF"><span style="cursor:pointer;" onclick="bom(\''.$wi[$val['ord_num']]['id'].'\')">'.substr($wi[$val['ord_num']]['bcfm_date'],0,10).'</span></td>
                      <td bgcolor="#FFFFFF">'.$wi[$val['ord_num']]['etd'].'</td>
                      <td bgcolor="#FFFFFF">'.$val['qty'].'</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
';
      
      foreach( $bom_lots as $ke =>  $va )
      {
        if( $va['wi_id'] == $wi[$val['ord_num']]['id'] )
        {
          if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) )
            $color = "#FCE8FF";
          else
            $color = "#FFFFFF";

$sql = "
SELECT rcv_qty, po_qty AS qty, po_unit AS unit, ap_det.eta, prc_unit, (
ap_det.prics * ap_det.po_qty
) AS po_amount, ap.ap_num, prics, amount, ap_det.ship_way, ap_det.ship_date, ap_det.ship_eta, ap_det.po_spare AS det_id, ap_det.ship_rmk
FROM ap_det, ap
WHERE ap_det.ap_num = ap.ap_num
AND ap_det.mat_cat = 'l'
AND bom_id = '".$va['id']."'
";
$sql_query = $mysql->query($sql);
$row = $mysql->fetch($sql_query);


if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) ){
  $mks = $averages = $clothes = $estimate = $different = $pc = $different2 = $pc2 = '';
  $sql2 = "SELECT * FROM `marker_ord` WHERE ord_id='".$wi[$val['ord_num']]['smpl_id']."' and `fab_type` = '1' and `combo` = '1'";
  $sql_query2 = $mysql->query($sql2);
  while ($rows = $mysql->fetch($sql_query2)) {
    $mks[] = $rows;
  }
  // print_r($mks);
  if(!empty($mks)){
    foreach($mks as $mkeys => $mvals){
      if(is_array($mvals)){
        foreach($mvals as $mkey => $mval){
          if($mkey === 'assortment'){
            if($mks[$mkeys]['length'] and !empty($mval) ){
              $asmts = $Marker->average($mval,$mks[$mkeys]['length']);
              $mks[$mkeys]['averages'] = $asmts['averages'];
              $clothes += $mks[$mkeys]['clothes'] = $asmts['clothes'];
              $estimate += $mks[$mkeys]['estimate'] = $asmts['estimate'];
            }
          }
        }
      }
    }
  }
  

  if($clothes and $estimate){
    $averages = $clothes / $estimate;
  }
  
  // Marker 計算
  $different = ( $row['rcv_qty'] != 0 )? $row['rcv_qty'] - array_sum(explode(',',$va['qty'])) : $row['qty'] - array_sum(explode(',',$va['qty'])) ;
  $pc = ( $averages != 0 )? $different / $averages : $different / $lots_use[$va['lots_used_id']]['est_1'] ;
  $pcs = ( $averages != 0 )? ( $different - $MQTY ) / $averages : ( $different - $MQTY ) / $lots_use[$va['lots_used_id']]['est_1'] ;
  
  $different2 = ( $row['rcv_qty'] != 0 )? $row['rcv_qty'] - ( array_sum(explode(',',$va['qty'])) + array_sum(explode(',',$va['qty']))*0.03 ) : $row['qty'] - ( array_sum(explode(',',$va['qty'])) + array_sum(explode(',',$va['qty']))*0.03 )  ;
  $pc2 = ( $averages != 0 )? $different2 / $averages : $different2 / $lots_use[$va['lots_used_id']]['est_1'] ;
  $pcs2 = ( $averages != 0 )? ( $different2 - $MQTY ) / $averages : ( $different2 - $MQTY ) / $lots_use[$va['lots_used_id']]['est_1'] ;
}


      if( 'shell' == substr($lots_use[$va['lots_used_id']]['use_for'],0,5) )
      $html_strm .= '
              <tr>
                <td>
                  <table border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
                    <tr style="text-align: center;font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;">
                      <td bgcolor="#003399">Fabirc #</td>
                      <td bgcolor="#CC3300" title="BOM 預估 yy">YY</td>
                      <td bgcolor="#003399">BOM Q\'TY</td>
                      <td bgcolor="#003399">PO. Q\'TY</td>
                      <td bgcolor="#003399">Received</td>
                      <td bgcolor="#9900FF" title="江大哥排的碼克">Marker</td>
                      <td bgcolor="#003399">different</td>
                      <td bgcolor="#003399">pc</td>
                      <td bgcolor="#003399">安全量</td>
                    </tr>
                    <tr style="font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                      <td align="center" bgcolor="#FFFFFF"><span style="cursor:pointer;" onclick="lots(\''.$lots_use[$va['lots_used_id']]['lots_code'].'\')">'.$lots_use[$va['lots_used_id']]['lots_code'].'</span></td>
                      <td align="center" bgcolor="#FFFFFF">'.$lots_use[$va['lots_used_id']]['est_1'].'</td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format(array_sum(explode(',',$va['qty'])),2,'.',',').'</td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format($row['qty'],2,'.',',').'</td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format($row['rcv_qty'],2,'.',',').'</td>
                      <td align="center" bgcolor="#FFFFFF"><span style="cursor:pointer;" onclick="marker(\''.$val['ord_num'].'\')">'.number_format($averages,2,'.',',').'</span></td>
                      <td align="center" bgcolor="#FFFFFF">'.number_format($different,0,'.',',').'</td>
                      <td align="center" bgcolor="#FFFFFF" style="font-size:11px;font-weight: bold;color:#FF0000;">'.number_format($pc,0,'.',',').'</td>
                      <td align="center" bgcolor="#FFFFFF" style="font-size:11px;font-weight: bold;color:#006600;">'.number_format($pcs,0,'.',',').'</td>
                    </tr>
                    <tr style="font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
                      <td align="center" bgcolor="#FFFFFF"></td>
                      <td align="center" bgcolor="#FFFFFF"></td>
                      <td align="center" bgcolor="#FFFFCC">'.number_format(array_sum(explode(',',$va['qty']))+array_sum(explode(',',$va['qty']))*0.03,2,'.',',').'</td>
                      <td colspan="3" align="center" bgcolor="#FFFFCC">BOM+3%</td>
                      <td align="center" bgcolor="#FFFFCC">'.number_format($different2,0,'.',',').'</td>
                      <td align="center" bgcolor="#FFFFCC" style="font-size:11px;font-weight: bold;color:#FF0000;">'.number_format($pc2,0,'.',',').'</td>
                      <td align="center" bgcolor="#FFFFCC" style="font-size:11px;font-weight: bold;color:#006600;">'.number_format($pcs2,0,'.',',').'</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
      ';


      $html_strs .= 
      '
  <tr id="'.$val['ord_num'].'" style=" font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;display:none;">
    <td nowrap="nowrap" bgcolor="'.$color.'"><span style="cursor:pointer;" onclick="lots(\''.$lots_use[$va['lots_used_id']]['lots_code'].'\')">'.$lots_use[$va['lots_used_id']]['lots_code'].'</span></td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['lots_name'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$va['color'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['use_for'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['est_1'].'</td>
    <td nowrap="nowrap" bgcolor="'.$color.'">'.$lots_use[$va['lots_used_id']]['unit'].'</td>
  </tr>
      ';
      
        }
      }
      // echo 
      // '
  // <tr>
    // <td colspan="5" bgcolor="#666666" height="20"></td>
  // </tr>
      // ';
         
      // $sql = "SELECT `lots_used_id` FROM `bom_lots` WHERE `wi_id`  = '".$wi[$val['ord_num']]."' ";
      // $sql_query = $mysql->query($sql);
      // while( $row = $mysql->fetch($sql_query) ){
        // $sql2 = "SELECT * FROM `lots_use` WHERE `id`  = '".$row['lots_used_id']."' ";
        // $sql_query2 = $mysql->query($sql2);
        // while( $row2 = $mysql->fetch($sql_query2) ){
          // echo
          // '
  // <tr style=" font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#666666;">
    // <td bgcolor="#FFFFFF">'.$row2['lots_code'].'</td>
    // <td bgcolor="#FFFFFF">'.$row2['lots_name'].'</td>
    // <td bgcolor="#FFFFFF">'.$row2['use_for'].'</td>
    // <td bgcolor="#FFFFFF">'.$row2['est_1'].'</td>
    // <td bgcolor="#FFFFFF">'.$row2['unit'].'</td>
  // </tr>
          // ';
        // }
      // }
$html_strm .= '
                  <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr id="'.$val['ord_num'].'" style="font-weight: bold; font-size:9px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF;display:none;">
    <td align="center" bgcolor="#999999">Fabirc #</td>
    <td align="center" bgcolor="#999999">Fabric Name</td>
    <td align="center" bgcolor="#999999">Color</td>
    <td align="center" bgcolor="#999999">Detail</td>
    <td align="center" bgcolor="#999999" colspan="2">consump.</td>
  </tr>
';
    } else {
      $html_strm .= '
  <tr style="font-weight: bold; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#996600;">
    <td colspan="6" bgcolor="#FFFFFF">'.$val['ord_num'].' &nbsp; (LINE:'.$line[$keys].') 無 BOM </td>
  </tr>
           ';
    }

// 剩布大於 100 碼
if( ( $different2 < $MQTY && $MQTY != '0' ) || $averages <= 0 && $dfrt == '1' ) 
  $html_strm = $html_strs = '';
else
  $html_str .= $html_strm.$html_strs.'
    <tr>
      <td colspan="6" bgcolor="#FFFFFF" height="20"></td>
    </tr>
  ';

  }
}


// 狀態顯示
if( $status == '1' ) {
  $note = 'ETS 排序';
  $link = '<a href="?PHP_action=fab_stock&status=0&dfrt='.$dfrt.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">生產線排序</a>';
} else {
  $note = '生產線排序';
  $link = '<a href="?PHP_action=fab_stock&status=1&dfrt='.$dfrt.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">ETS 排序</a>';
}

if( $dfrt == '1' ) {
  $note .= ' / 大貨餘布 '.$MQTY.' 有含 Marker 用量';
  $link .= ' , <a href="?PHP_action=fab_stock&dfrt=0&status='.$status.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">大貨餘布 '.$MQTY.' 無含 Marker 用量</a>';
} else {
  $note .= ' / 大貨餘布 '.$MQTY.' 無含 Marker 用量';
  $link .= ' , <a href="?PHP_action=fab_stock&dfrt=1&status='.$status.'&N_DAY='.$N_DAY.'&MQTY='.$MQTY.'">大貨餘布 '.$MQTY.' 有含 Marker 用量</a>';
}

$link .= ' , 查詢時間：<input style="height:20" id="N_DAY" name="N_DAY" value="'.$N_DAY.'" size="8"> , 餘布：<input style="height:20" name="MQTY" value="'.$MQTY.'" size="4"> <input type="submit">';
$link .= '

';

echo
'<table border="0" align="center" cellpadding="3" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#666666">
<form method="post" action="?PHP_action=fab_stock&dfrt='.$dfrt.'&status='.$status.'">';
echo '
  <tr style="height:36px; font-weight: bold; font-size:12px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000;" bgcolor="#FFFFFF">
    <td colspan="6" align="left">查詢時間：'.$N_DAY.'<br>狀態：'.$note.'<p>'.$link.'</td>
  </tr>
';
echo $html_str;
echo
'</form>
</table>';

// schedule

break;
	
//-------------------------------------------------------------------------

}   // end case ---------

?>
