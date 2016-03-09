<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
##################  2004/11/10  ########################
#			monitor.php  ¥Dµ{¦¡
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";

$op = array();

$P_LINE = array (1,2,3,'029',6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

// echo $PHP_action;
switch ($PHP_action) {
//=======================================================
//-------------------------------------------------------------------------------------
//		case "shipping":	 job 39  出口記錄
//-------------------------------------------------------------------------------------
 case "shipping":
 check_authority('029',"view");
	//---------------------------------- 判斷是否工廠人員進入 ---------------

		$where_str = $manager = $dept_id = $fty_select ='';
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 叛定進入身份的指標

		for ($i=0; $i<count($GLOBALS['FACTORY']);$i++){
			if($user_dept == $GLOBALS['FACTORY'][$i]){
				$dept_id = $GLOBALS['FACTORY'][$i];   // 如果是工廠部門人員進入 dept_id 指定該工廠---
			}
		}

		if (!$dept_id) {    // 當不是工廠人員進入時
			$manager = 1;
			$fty_select = $arry2->select($FACTORY,"","PHP_factory","select","");  //工廠 select選單
		} else {
			$where_str = " WHERE factory = '".$dept_id."' ";
		}

		$op['manager_flag'] = $manager;
		$op['fty_id'] = $dept_id;
		$op['fty_select'] = $fty_select;

//---------------------------------- 判斷是否工廠人員進入 ---------------

		$op['msg'] = $order->msg->get(2);
		// 取出 全部客戶代號
		$where_str="order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		$op['year_select'] =  $arry2->select($YEAR_WORK,date('Y'),'PHP_year','select',''); 
		$op['month_select'] =  $arry2->select($MONTH_WORK,'','PHP_month','select',''); 

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];


		page_display($op, '029', $TPL_SHIPPING);			    	    
		break;

//-------------------------------------------------------------------------------------
//		case "shipping_search":
//-------------------------------------------------------------------------------------

	case "shipping_search":

		check_authority('029',"view");
			// 訂單資料列表
		if(isset($PHP_order_num))
		{			
			$_SESSION['sch_parm'] = array();
			$_SESSION['sch_parm'] = array(	
						"PHP_order_num"		=>  $PHP_order_num,
						"PHP_cust"				=>	$PHP_cust,
						"PHP_ref"					=>	$PHP_ref,
						"PHP_factory"			=>	$PHP_factory,
						"PHP_sr_startno"	=>	$PHP_sr_startno,
						"PHP_action"			=>	$PHP_action						
				);
			}else{
				if(isset($PHP_sr_startno))$_SESSION['sch_parm']['PHP_sr_startno'] = $PHP_sr_startno;
			}				

			//  先 取出今日 產出記錄列表-------------+++++++++++++
			$argv = array(	"ord_num"	=> $_SESSION['sch_parm']['PHP_order_num'],
							"k_date"	=>	$GLOBALS['TODAY'],
							"factory"	=>	$_SESSION['sch_parm']['PHP_factory'],
			);

			//  先 取出今日 出口記錄列表-------------+++++++++++++
			if (!$shp = $shipping->search($argv,1)) {   
				$op['msg']= $shipping->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$op = $order->shipping_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			// 轉換給 html用 之參數
			$op['shp'] = $shp['shipping'];
			$op['shipping_records'] = $shp['max_no'];
			$op['shpping_qty'] = 0;  $op['shpping_su']= 0;


			$entry = count($op['shp']);
			for($i=0;$i<$entry;$i++){  // 計算 今日出口總數量
				$op['shpping_qty'] =	$op['shpping_qty'] + $op['shp'][$i]['qty'];
				$op['shpping_su'] =		$op['shpping_su'] + $op['shp'][$i]['su'];
			}

		page_display($op, '029', $TPL_SHIPPING_LIST);	  
		break;


//-------------------------------------------------------------------------------------
//								記錄 s_order->status, pdtion->qty_shp, shp_date
//								 加入 shipping 檔
//-------------------------------------------------------------------------------------
	case "add_shipping":
		check_authority('029',"add");
		$today = $GLOBALS['TODAY'];

		$parm = array(	"pd_id"			=>  $PHP_pd_id,
										"p_id"			=>	$PHP_p_id,
										"ord_num"		=>  $PHP_ord_num,
										"qty_shp"		=>  $PHP_qty,
										"shp_date"	=>  $today,
										"remain"		=>	$PHP_remain,
										"acm"				=>	0,
										"cm"				=>	0
				);

	// 先檢查輸入項之正確 ----------------------
		if (!$f1 = $shipping->check($parm)) {  // 輸入資料 不正確時 
			$op['msg'] = $shipping->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}  // end if  輸入資料 不正確 


	// 先取出 s_order 的 id , factory, $su_ratio ------------------------------------------
	if(!$ord = $order->get(0, $PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 

	$parm['id'] = $ord['id'];
	$parm['factory'] = $ord['factory'];
	$parm['su_ratio'] = $ord['ie1'];
	
	$cac_su = $parm['su_ratio'] * $parm['qty_shp'];
	$parm['su_shp'] = number_format($cac_su, 0, '', '');  //四捨五入 ----- 單筆
	
	//2008-05-06 增加fty-cm的輸入
	if($ord['cm'] > 0) //代表不是代工
	{
		$parm['acm'] = $FTY_CM[$ord['factory']];
		$parm['cm'] = $parm['acm'] * $parm['su_shp'];	
	};
	
	// *********  寫入 table shipping  *****************
		if(!$F = $shipping->add($parm)){   // 新增 shipping 資料庫
				$op['msg']= $shipping->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *********  寫入 pdtion->qty_shp, shp_date    *********************
		if(!$F1 = $order->add_shipping($parm)){
			$op['msg'][] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 	

	// *************************************************




//2006/03/30 新增------------------------------
// *********  寫入 capacity 資料表內 2006/03/30 新增的 shippiing    *********************
//先判斷是否有該筆 shipping 如果沒有 則新增  如果有 就將原資料更新 ~~~~
// 寫入 capaci. shipping ++++++++++++++++++++++++++++++++
// 先檢查 capacity 檔內是否已經有記錄 ???  有可能都是多餘的判斷[ 可以加在 order_apv ]
		
		if (!$T=$capaci->get($parm['factory'], $THIS_YEAR,'shipping')) {   //$cgi_5為 工廠
				// ========= create capaci->shipping 該筆 記錄 =======
			if(!$F1 = $capaci->append_new($THIS_YEAR,$parm['factory'],'shipping')){
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			} 
		}

			$ky_mon = substr($THIS_MON,4,2);

				// ========= 寫入 capaci. shipping ===   [加入更新]  :::  $cgi_5為 工廠 =====

			if(!$F = $capaci->update_su($parm['factory'], $THIS_YEAR, substr($THIS_MON,4,2), 'shipping', $PHP_qty)){  
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			}

			$fob=$PHP_qty*$ord['uprice'];  //加入SHIP FOB記錄
			if(!$F = $capaci->update_su($parm['factory'], $THIS_YEAR, substr($THIS_MON,4,2), "shp_fob", $fob)){  
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			}


				# 記錄使用者動態
		$message = "Add [ ".$PHP_ord_num." ] shipping record in Q'ty:[".$PHP_qty."] ";
		$log->log_add(0,"45A",$message);
		
##--*****新增頁碼 start		
		$redir_str = "ship.php?PHP_action=shipping_search";
##--*****新增頁碼 end

			redirect_page($redir_str);


	break;		
	
	
//-------------------------------------------------------------------------------------
 
	case "del_shipping":

//    同 add_production 只是將 qty 改成負值 但要注意加入 是否finish......
//-------------------------------------------------------------------------------------
		check_authority('029',"edit");

		$today = $GLOBALS['TODAY'];

		$parm = array(	
						"ord_num"		=>  $PHP_ord_num,
						"factory"		=>  $PHP_fty,
						"qty_shp"		=>  $PHP_qty * (-1),    // 直接將 qty 改成 負值
						"su_shp"		=>  $PHP_su * (-1),    // 直接將 qty 改成 負值
						"shp_date"	=>  $today,
						"p_id"			=>	$PHP_p_id
				);



	// 先取出 s_order 的 id , 及 pdtion 的 id ------------------------------------------
	if(!$parm['id'] = $order->get_field_value('id','',$PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 
	if(!$parm['pd_id'] = $order->get_field_value('id','',$PHP_ord_num,'pdtion')){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 
//取出order的price	供SHIP FOB使用
	if(!$parm['uprice'] = $order->get_field_value('uprice','',$PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 


	// *************************************************
	// *********  delete table shipping  *****************
		if(!$F = $shipping->del($PHP_shp_id)){   // delete shipping 資料庫
				$op['msg']= $shipping->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 


	// *********  寫入 pdtion->qty_shp, shp_date    *********************
		if(!$F1 = $order->add_shipping($parm)){
			$op['msg'][] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *********  寫入 capaci-> shipping    ********************* 2006/03/30 adding
		if(!$F = $capaci->update_su($PHP_fty, $THIS_YEAR, substr($THIS_MON,4,2), 'shipping', $parm['qty_shp'])){  
			$op['msg'][] = $capaci->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		}

		$fob=$parm['qty_shp']*$parm['uprice'];  //加入SHIP FOB記錄
		if(!$F = $capaci->update_su($PHP_fty, $THIS_YEAR, substr($THIS_MON,4,2), "shp_fob", $fob)){  
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

				# 記錄使用者動態
		$message = "Delete [".$PHP_ord_num."] shipping Q'ty:[".$PHP_qty."] from FTY:[".$parm['factory']."] ";
		$log->log_add(0,"44E",$message);
		
		$redir_str = "ship.php?PHP_action=shipping_search";
		redirect_page($redir_str);


	break;	
		
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "Remun":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "ship_inv_add":
// 	check_authority('029',"add");
// 		if (!$PHP_yy) $PHP_yy = date('Y');
//		if (!$PHP_mm) $PHP_mm = date('m');
//		$sch_date = $PHP_yy.'-'.$PHP_mm;
//		$op = $shipping->search_ship_out($sch_date,$PHP_fty,$PHP_cust);
//		$op['sch_date'] = $sch_date;
//		$op['fty'] = $PHP_fty;
//		$op['cust'] = $PHP_cust;
//	page_display($op, 4,6, $TPL_SHIP_OUT_ADD);
//	break;
//
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "Remun":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "do_ship_inv_add":
// 	check_authority('029',"add");
//
//		for($i=0; $i<sizeof($PHP_id); $i++)
//		{
//			$parm = array('inv_date'	=>	$PHP_inv_date[$i],
//										'inv_num'		=>	$PHP_inv_num[$i],
//										't_qty'			=>	$PHP_t_qty[$i],
//										'id'				=>	$PHP_id[$i]
//										);
//			if($PHP_inv_num[$i] && $PHP_inv_date[$i])$shipping->add_inv($parm);
//		}
//		$op = $shipping->search_ship_out($PHP_sch_date,$PHP_fty,$PHP_cust);
//		if(isset($PHP_sch_date))$op['sch_date'] = $PHP_sch_date;
//		if(isset($sch_date))$op['sch_date'] = $sch_date;
//		$op['fty'] = $PHP_fty;
//		$op['cust'] = $PHP_cust;
//
//	page_display($op, 4,6, $TPL_SHIP_OUT_SHOW);
//	break;
//
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "Remun":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "ship_inv_view":
// 	check_authority('029',"add");
// 		if (!$PHP_yy) $PHP_yy = date('Y');
//		if (!$PHP_mm) $PHP_mm = date('m');
//		$sch_date = $PHP_yy.'-'.$PHP_mm;
//		$op = $shipping->search_ship_out($sch_date,$PHP_fty,$PHP_cust);
//		$op['sch_date'] = $sch_date;
//		$op['fty'] = $PHP_fty;
//		$op['cust'] = $PHP_cust;
//	page_display($op, 4,6, $TPL_SHIP_OUT_SHOW);
//	break;
//	
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "Remun":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "do_inv_submit":
// 	check_authority('029',"add");
//
//		for($i=0; $i<sizeof($PHP_id); $i++)
//		{
//			if($PHP_o_qty[$i] <> $PHP_t_qty[$i]){$inv_status = 2;}else{$inv_status = 4;}
//			$parm = array('field_name'	=> 'status',
//										'field_value'	=>	$inv_status,
//										'id'					=>	$PHP_id[$i]
//										);
//			$shipping->update_field($parm);
//		}
//		$op = $shipping->search_ship_out($PHP_sch_date,$PHP_fty,$PHP_cust);
//		$op['sch_date'] = $PHP_sch_date;
//		$op['fty'] = $PHP_fty;
//		$op['cust'] = $PHP_cust;
//
//	page_display($op, 4,6, $TPL_SHIP_OUT_SHOW);
//	break;
//	
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "Remun":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "ship_inv_cfm":
// 	check_authority('029',"add");
//		$op = $shipping->search_ship_cfm($PHP_fty);
//		$op['fty'] = $PHP_fty;
//		if (!isset($op['ship'])) $op['msg'][]='No Records';
//	page_display($op, 4,6, $TPL_SHIP_OUT_CFM);
//	break;
//	
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "Remun":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "do_cfm_change":
// 	check_authority('029',"add");
// 		$tmp = explode('-',$PHP_k_date);
// 		$yy = $tmp[0];
// 		$mm = $tmp[1];
// 	// *********  寫入 capaci-> shipping  ==> 將原本的capacity中ship相關刪除  ********************* 
// 	$PHP_qty = $PHP_qty * -1;
//		if(!$F = $capaci->update_su($PHP_fty, $yy, $mm, 'shipping', $PHP_qty)){  
//			$op['msg'][] = $capaci->msg->get(2);
//				$layout->assign($op);
//				$layout->display($TPL_ERROR);  		    
//				break;
//		}
//		$ship_fob = $PHP_fob * $PHP_qty; 
//		if(!$F = $capaci->update_su($PHP_fty, $yy, $mm, "shp_fob", $ship_fob)){  
//				$op['msg'][] = $capaci->msg->get(2);
//					$layout->assign($op);
//					$layout->display($TPL_ERROR);  		    
//					break;
//		}
// 	
// 	// *********  寫入 shipping  ==> 新的Q'TY  ********************* 
//			$parm = array('field_name'	=> 'qty',
//										'field_value'	=>	$PHP_t_qty,
//										'id'					=>	$PHP_id
//										);
//			$shipping->update_field($parm);
//
// 	// *********  寫入 shipping  ==> 新的SU  ********************* 
// 			$su = $PHP_t_qty * $PHP_ie;
//			$parm = array('field_name'	=> 'su',
//										'field_value'	=>	$su,
//										'id'					=>	$PHP_id
//										);
//			$shipping->update_field($parm); 	
//
// 	// *********  寫入 shipping  ==> status = 4 ********************* 
// 			$su = $PHP_t_qty * $PHP_ie;
//			$parm = array('field_name'	=> 'status',
//										'field_value'	=>	4,
//										'id'					=>	$PHP_id
//										);
//			$shipping->update_field($parm);  	
// 	
//
// 	// *********  寫入 capaci-> shipping  ==> 加入新的SHIP資料  *********************  	
//		if(!$F = $capaci->update_su($PHP_fty, $yy, $mm, 'shipping', $PHP_t_qty)){  
//			$op['msg'][] = $capaci->msg->get(2);
//				$layout->assign($op);
//				$layout->display($TPL_ERROR);  		    
//				break;
//		}
//		$new_ship_fob = $PHP_fob * $PHP_t_qty; 
//		if(!$F = $capaci->update_su($PHP_fty, $yy, $mm, "shp_fob", $new_ship_fob)){  
//				$op['msg'][] = $capaci->msg->get(2);
//					$layout->assign($op);
//					$layout->display($TPL_ERROR);  		    
//					break;
//		} 	 	
// 	
//		$op = $shipping->search_ship_cfm($PHP_fty);
//		$op['fty'] = $PHP_fty;
//	page_display($op, 4,6, $TPL_SHIP_OUT_CFM);
//	break;	
//	
//	
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "Remun":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "do_uncfm_change":
// 	check_authority('029',"add");
// 		$tmp = explode('-',$PHP_k_date);
// 		$yy = $tmp[0];
// 		$mm = $tmp[1];
// 	
// 	// *********  寫入 shipping  ==> 新的Q'TY  ********************* 
//			$parm = array('field_name'	=> 't_qty',
//										'field_value'	=>	$PHP_qty,
//										'id'					=>	$PHP_id
//										);
//			$shipping->update_field($parm);
//
//
// 	// *********  寫入 shipping  ==> status = 4 ********************* 
//			$parm = array('field_name'	=> 'status',
//										'field_value'	=>	4,
//										'id'					=>	$PHP_id
//										);
//			$shipping->update_field($parm);  	
// 		
//		$op = $shipping->search_ship_cfm($PHP_fty);
//		$op['fty'] = $PHP_fty;
//	page_display($op, 4,6, $TPL_SHIP_OUT_CFM);
//	break;	
//	
//
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//#		case  "cost_cost":	 
//#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//    case "ship_print":
// 	check_authority(4,7,"view");
//  $op = $shipping->search_ship_out($PHP_sch_date,$PHP_fty,$PHP_cust);
//	$tmp = explode('-',$PHP_sch_date);
//	
//		
//include_once($config['root_dir']."/lib/class.pdf_ship.php");
//
//$print_title1 = $op['ship'][0]['cust_name'];
//$print_title="Summary Monthly Report For the Month Of ".$MM[$tmp[1]]." ".$tmp[0];
//$creator = $GLOBALS['SCACHE']['ADMIN']['login_id']." [ ".date('Y-m-d')." ] ";
//$mark = $PHP_fty;
//$pdf=new PDF_ship('L','mm','A4');
//$pdf->AddBig5Font();
//$pdf->Open();
//$pdf->AddPage();
//$pdf->SetAutoPageBreak(1,20);
//
//$inv_num = '';
//$ship_qty = $fob_amt = $cm_amt = 0;
//$tal_qty = $tal_fob = $tal_cm = 0;
//for ($i=0; $i<sizeof($op['ship']); $i++)
//{
//		$tal_qty += $op['ship'][$i]['qty'];
//		$tal_fob +=	$op['ship'][$i]['fob_amt'];
//		$tal_cm	 += $op['ship'][$i]['cm_amt'];
//		if($inv_num <> $op['ship'][$i]['inv_num'])
//		{
//			if($inv_num <> '')
//			{
//				$ship_qty = number_format($ship_qty,0,'',',');
//				$fob_amt	= number_format($fob_amt,2,'.',',');	
//				$cm_amt   = number_format($cm_amt,2,'.',',');				
//				$pdf->ship_ttl($ship_qty,$fob_amt,$cm_amt);
//			}			
//			$inv_num	= $op['ship'][$i]['inv_num'];
//			$ship_qty	= $op['ship'][$i]['qty'];
//			$fob_amt	= $op['ship'][$i]['fob_amt'];
//			$cm_amt		= $op['ship'][$i]['cm_amt'];
//		}else{
//			$ship_qty	+= $op['ship'][$i]['qty'];
//			$fob_amt	+= $op['ship'][$i]['fob_amt'];
//			$cm_amt		+= $op['ship'][$i]['cm_amt'];		
//		}
//		
//		
//		
//		$op['ship'][$i]['qty']      = number_format($op['ship'][$i]['qty'],0,'',',');
//		$op['ship'][$i]['uprice']   = number_format($op['ship'][$i]['uprice'],2,'.',',');
//		$op['ship'][$i]['fob_amt']	= number_format($op['ship'][$i]['fob_amt'],2,'.',',');
//		$op['ship'][$i]['cm']  			= number_format($op['ship'][$i]['cm'],2,'.',',');
//		$op['ship'][$i]['cm_amt']  	= number_format($op['ship'][$i]['cm_amt'],2,'.',',');
//		
//
//		$pdf->ship_table($op['ship'][$i]);
//}
//
//	$pdf->ship_ttl($tal_qty,$tal_fob,$tal_cm,'TOTAL >>>');				
//
////  $pdf->mang_chk($op['cost']);
//
////$pdf->hend_title($ary_title);		
//
//$name=$PHP_fty.$PHP_sch_date.'_ship.pdf';
//$pdf->Output($name,'D');			
	
	
//-------------------------------------------------------------------------

}   // end case ---------

?>
