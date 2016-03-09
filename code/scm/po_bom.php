<?php
##################  2004/11/10  ########################
#			index.php  主程式
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [生產製造][生產產能]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();

include_once($config['root_dir']."/lib/class.transport.php");
$transport = new TRANSPORT();
if (!$transport->init($mysql,"log")) { print "error!! cannot initialize database for ORDER SHIFT class"; exit; }
# 匯出檔案用########################################################################################
include_once($config['root_dir']."/lib/class.export.php");
$export = new export();
if (!$export->init($mysql)) { print "error!! cannot initialize database for ORDER class"; exit; }
####################################################################################################


session_register('sch_parm');

// echo $PHP_action;
switch ($PHP_action) {
//= trim_add ====================================================

	case "upload_trim_file":

		check_authority('022',"edit");

				$op['back_str'] = $PHP_back_str;
		$filename = $_FILES['PHP_pttn']['name'];
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

		if ($ext == "pdf" || $ext == "PDF"){   // 上傳檔的副檔名為 mdl 時 -----

			// upload pattern file to server
			$pttn_name = $PHP_num.'.pdf';  // 指定為 mdl 副檔名
			$upload = new Upload;
			$upload->uploadFile(dirname($PHP_SELF).'/order_trim/', 'other', 16, $pttn_name );
			if (!$upload){
				$op['msg'][] = $upload;				
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			$wi_parm = array($PHP_num,'trim_date',$TODAY);
			$A=$wi->update_field_num($wi_parm); //記錄trim carad上傳日期		
			$wi_parm = array($PHP_num,'trim_user',$GLOBALS['SCACHE']['ADMIN']['login_id']);
			$A=$wi->update_field_num($wi_parm); //記錄trim carad上傳日期			
			$message = "UPLOAD TRIMCARD of ".$PHP_num;
			$log->log_add(0,"35E",$message);
		} else {  // 上傳檔的副檔名  不是  pdf 時 -----

			$message = "upload file is incorrect format ! Please re-send.";
		}

		$redir_str="bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);




//= trim_del ====================================================
	case "do_trim_del":
		check_authority('022',"edit");
		$wi_parm = array($PHP_num,'trim_date','0000-00-00');
		$A=$wi->update_field_num($wi_parm); //取消上傳			
		$message = "DELETE TRIMCARD of ".$PHP_num;

// 	  $back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$redir_str="bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);

//= fty pattern file del ====================================================
	case "do_fty_pattern_del":
		check_authority('022',"edit");
		//$wi_parm = array($PHP_num,'trim_date','0000-00-00');
		$A=$wi->fty_pattern_del($PHP_id); //刪除fty pattern資料			
		$message = "DELETE TRIMCARD of ".$wi_id;

// 	  $back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$redir_str="bom.php?PHP_action=bom_view&PHP_id=".$wi_id."&PHP_msg=".$message;
		redirect_page($redir_str);
	

//= reject_trim ====================================================
	case "reject_trim":
		check_authority('022',"del");
			
		$wi_parm = array(
											'order_num'	=>	$PHP_num,
											'item'			=>	'trim-rjt',
											'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
											'des'				=> 	$PHP_detail
										);
		$A=$order_log->add_multi_ord($wi_parm); 			

		$wi_parm = array($PHP_num,'trim_date','0000-00-00');
		$A=$wi->update_field_num($wi_parm); //取消上傳			
	
	
		$message = "REJECT TRIMCARD of ".$PHP_num;

		$redir_str="bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);



//= trim_cfm ====================================================
	case "trim_cfm":
		check_authority('022',"del");

		$wi_parm = array($PHP_num,'trim_cfm_date',$TODAY);
		$A=$wi->update_field_num($wi_parm); //取消上傳
		$wi_parm = array($PHP_num,'trim_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id']);
		$A=$wi->update_field_num($wi_parm); //取消上傳			
	
	
		$message = "REJECT TRIMCARD of ".$PHP_num;

		$redir_str="bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);
//= END TRIMCARD===================================================


//=BOM=============================================================
    case "bom":

		check_authority('022',"view");

		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // 判定進入身份的指標(team)
		$sales_dept_ary = get_sales_dept();// 取出 業務的部門 [不含K0] ------
		// for ($i=0; $i<count($sales_dept_ary);$i++){
			// if($user_dept == $sales_dept_ary[$i]){

				// 如果是業務部進入 則dept_code 指定該業務部---
				// $dept_id = $sales_dept_ary[$i];  
			// }
		// }
		$op['dept_id'] = $dept_id;
		// if (!$dept_id || $team<>'MD') {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$op['manager_flag'] = 1;
			$manager_v = 1;
			//業務部門 select選單
			$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 
 
		// }

								
		// creat cust combo box	 取出 客戶代號
		$where_str=$where_str."order by cust_s_name";
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if(!$cust_def_vue = $cust->get_fields('cust_s_name',$where_str)){;  
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		$op['fty_select'] =  $arry2->select($FACTORY,'','PHP_fty_sch','select',''); 
		for ($i=0; $i< sizeof($FACTORY); $i++)
		{
			if ($user_dept == $FACTORY[$i]) $op['fty_select'] = $user_dept."<input type='hidden' name='PHP_fty_sch' value='$user_dept'>";
		}

		$op['msg']= $wi->msg->get(2);

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

		page_display($op, '022', $TPL_BOM_SEARCH);		    	    
		break;		


//=bom_search_wi======================================================
case "bom_search_wi":   //  先找出製造令.......
check_authority('022',"view");

if(isset($PHP_etdfsh))
{
	$sch_parm = array();
	$sch_parm = array(
		"dept_code"		=>  $PHP_dept_code,
		"etdfsh"		=>  $PHP_etdfsh,
		"cust"			=>	$PHP_cust,
		"wi_num"		=>	$PHP_wi_num,
		"etdstr"		=>	$PHP_etdstr,
		"fty_sch"		=>	$PHP_fty_sch,
		"sr_startno"	=>	$PHP_sr_startno
	);
	}else{
		if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
		$PHP_dept_code = $sch_parm['dept_code'];
		$PHP_etdfsh = $sch_parm['etdfsh'];
		$PHP_cust = $sch_parm['cust'];
		$PHP_wi_num = $sch_parm['wi_num'];
		$PHP_etdstr = $sch_parm['etdstr'];
		$PHP_fty_sch = $sch_parm['fty_sch'];
		$PHP_sr_startno = $sch_parm['sr_startno'];
	}



	if (!$op = $wi->search(1,1)) {
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	// op 被清除了 需再判斷一次 

//2006/05/02 update
$op['dept_id'] = get_dept_id();

// 如果是 manager 進入時...
if (substr($op['dept_id'],0,7) == "<select"){
	$op['manager_flag'] = 1;
}

# 查看是否 BOM 已完成
for ($i=0; $i< sizeof($op['wi']); $i++)
{
	// $op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
	// $op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
	$op['wi'][$i]['lots_ap'] = $op['wi'][$i]['lots_mark'] === NULL ? 1 : 0;
	$op['wi'][$i]['acc_ap'] = $op['wi'][$i]['acc_mark'] === NULL ? 1 : 0;
}

$op['msg']= $wi->msg->get(2);				
// $back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
// $op['back_str']="&back_str"; acc_ap

page_display($op, '022', $TPL_BOM);
break;		
		
//=bom_add======================================================
case "bom_add":
check_authority('022',"add");

//-------------------- 將 BOM 製造令 show out ------------------
//  wi 主檔
if(!$op['wi'] = $wi->get($PHP_id)){    //取出該筆 製造令記錄
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
}
//  smpl 樣本檔
if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
}
// 取出 size_scale ----------------------------------------------
$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];
$sizing = explode(",", $size_A['size']);


//  wi_qty 數量檔				
	$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
	$p_id = $order->get_fields('id', $where_str2,'order_partial');
	$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
	$op['num_colors'] = $op['total'] =0; $op['g_ttl'] = 0;
	for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
	$k=0;

	for($i=0; $i<sizeof($p_id); $i++)
	{
		$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
		$T_wiqty = $wiqty->search(1,$where_str3);
		$p_remark = $order->get_fields('remark', $where_str2,'order_partial');
		//morial新增
		/* $p_mks = $order->get_fields('mks', " WHERE id='".$p_id[$i]."' " ,'order_partial'); */
		$num_colors = count($T_wiqty);
		$op['num_colors'] += $num_colors;
		$op['partial'][$i]['size'] = $num_colors;
		$op['partial'][$i]['etd'] = $p_etd[$i];
		//-------------------- 整理 供 title 部份的資料 ----------------------------------
		if ($num_colors){
			for ($j=0;$j<$num_colors;$j++){					
				$op['color'][$k]['name'] = $T_wiqty[$j]['colorway'];
				$T_qty = explode(",", $T_wiqty[$j]['qty']);
				$op['color'][$k]['qty'] = array_sum($T_qty);
				$color_qty[$k] = array_sum($T_qty);
				for ($l=0; $l<sizeof($T_qty); $l++) $size_qty[$sizing[$l]][$k] = $T_qty[$l];	
				$k++;			
			}
		}else{
			$color_qty[] = 0;   // 避免 error							
		}
		
		// 做出 size table-------------------------------------------
																									
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i],$i,$p_remark[$i]);
		$op['show_breakdown'][$i] = $reply['html'];				
		$op['total'] += $reply['total'];
		for($j=0; $j<sizeof($sizing); $j++)
		{
			$op['g_ttl'] += $reply['size_ttl'][$j];				
			$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
		}		
	}
//-----------------------------------------------------------------

// 相片的URL決定 ------------------------------------------------------
		$style_dir	= "./picture/";  
		$no_img		= "./images/graydot.gif";
	if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
		$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
	} else {
		$op['wi']['pic_url'] = $no_img;
	}

//  取出主料用料記錄 --------------------------------------------------------------
 $op['lots_NONE']= '';
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$lots_used['lots_use'] = $smpl_lots->search(0,$where_str);  //取出該筆 樣本主料記錄

if (!is_array($lots_used['lots_use'])) {
	$op['msg'] = $smpl_lots->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$num_lots_used = count($lots_used['lots_use']);
if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

//  取出副料用料記錄 --------------------------------------------------------------
 $op['acc_NONE']= '';
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //取出該筆 樣本主料記錄

if (!is_array($acc_used['acc_use'])) {     
	$op['msg'] = $smpl_acc->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$num_acc_used = count($acc_used['acc_use']);
if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

//  取出  BOM  主料記錄 --------------------------------------------------------------
 $op['bom_lots_NONE']= '';
$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
$bom_lots = $bom->search_lots(0,$where_str);  //取出該筆 bom 內ALL主料記錄
$op['bom_lots'] = $bom_lots['lots'];

if (!is_array($bom_lots['lots'])) {
	$op['msg'] = $bom->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$num_bom_lots = count($bom_lots['lots']);
if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

//  取出  BOM  副料記錄 --------------------------------------------------------------
 $op['bom_acc_NONE']= '';
$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
$bom_acc = $bom->search_acc(0,$where_str);  //取出該筆 bom 內ALL副料記錄
$op['bom_acc'] = $bom_acc['acc'];

if (!is_array($bom_acc['acc'])) {
	$op['msg'] = $bom->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$num_bom_acc = count($bom_acc['acc']);
if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

//-------------------- 整理部份資料 ---- for bom table TITLE------------------------------
//		if ($num_colors){
//			for ($i=0;$i<$num_colors;$i++){
//				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
//				$T_qty = explode(",", $T_wiqty[$i]['qty']);
//				$op['color'][$i]['qty'] = array_sum($T_qty);
//				$color_qty[$i] = array_sum($T_qty);
//				for ($j=0; $j<sizeof($T_qty); $j++)
//				{
//					$size_qty[$sizing[$j]][$i] = $T_qty[$j];
//				}
//			}
//		} else {
//				$color_qty[] = 0;   // 避免 error
//				$op['colors_NONE'] = "1";
//		}

// --------------- 主料 ------------ 做出 $bom_lots_list[]  array....
$op['bom_lots_list'] = array();
if (!$op['lots_NONE']){   // 樣本有主料用料記錄---
  $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$op['num_colors'], $PHP_id, $color_qty);
} 
// ---------------------- end BOM data layout [主料] ----------------------------

// --------------- 副料 ------------ 做出 $bom_acc_list[]  array....
$op['bom_acc_list'] = array();
if (!$op['acc_NONE']){   // 樣本有副料用料記錄---
	$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$op['num_colors'], $PHP_id, $color_qty,$sizing,$size_qty);	
} 
// ---------------------- end BOM data layout [副料] ----------------------------

	// 表格 改進用 -----
	if (!$num_colors){	
		$op['total_fields'] = 9;   // BOM table的總欄位數
	}else{
		$op['total_fields'] = 8 + $op['num_colors'];   // BOM table的總欄位數
	}


$op['done']=$fils->get_bom_file($op['wi']['wi_num']);
// ---------------------- end BOM data layout table ----------------------------

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if (isset($PHP_rev))
{
	page_display($op, '022', $TPL_BOM_CFM_EDIT);
}else{
	page_display($op, '022', $TPL_BOM_ADD);	
}  	    
break;
				
//=======================================================
case "do_bom_add":
check_authority('022',"add");

// 加入 bom 檔
$PHP_color = urldecode(uniDecode($PHP_color,'big-5'));//轉碼

$dt = decode_date(1);

if ($PHP_mat=="lots") {
	$parm = array(
		"wi_id"			=>	$PHP_wi_id,
		"color"			=>	$PHP_color,
		"lots_used_id"	=>	$PHP_mat_use_id,
		"qty"			=>	$PHP_qty,		
		"o_qty"			=>	$PHP_o_qty,
		"this_day"		=>	$dt['date']	
	);
	$f1 = $bom->add_lots($parm);   // 加入
	$K = "Fabric";
} elseif ($PHP_mat=="acc") {
	$mat_use_id = explode('x',$PHP_mat_use_id);
	$parm = array(
		"wi_id"			=>	$PHP_wi_id,
		"color"			=>	$PHP_color,
		"acc_used_id"	=>	$mat_use_id[0],
		"qty"			=>	$PHP_qty,	
		"o_qty"			=>	$PHP_o_qty,									
		"this_day"		=>	$dt['date'],
		"size"			=>	$PHP_size,
	);

	$f1 = $bom->add_acc($parm);   // 加入
	if ($PHP_size)
	{
		$acc_use = $smpl_acc->get($PHP_mat_use_id);
		if(!strstr($acc_use['use_for'],$PHP_size_des))
		{
			$placement = $acc_use['use_for']." (".$PHP_size_des.")";
			$acc_parm = array ( $mat_use_id[0],'use_for',$placement);
			$smpl_acc->update_field($acc_parm);
		}
	}
	$K = "Accessory";
} else {
	$op['msg'][] = "Error ! Not point materiel catagory of BOM !";
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

if (!$f1) {  // 沒有成功輸入資料時
	$op['msg'] = $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

# 記錄使用者動態
$parm = array($PHP_wi_id,'bupdator',$GLOBALS['SCACHE']['ADMIN']['name']);
$wi->update_field($parm);
$op['wi'] = $wi->get($PHP_wi_id);			
$message = "Successfully add ".$K." record on BOM [ ".$op['wi']['wi_num']." ] ";			
$log->log_add(0,"35A",$message);
$op['msg'][] = $message;

//if (isset($PHP_rev))
//{
//	$redir_str="bom.php?&PHP_action=bom_add&PHP_id=".$PHP_wi_id."&PHP_rev=".$PHP_rev."&PHP_msg=".$message;
//}else{
//	$redir_str="bom.php?&PHP_action=bom_add&PHP_id=".$PHP_wi_id."&PHP_msg=".$message;	
//}
//redirect_page($redir_str);  	    
echo $f1;
break;

//=do_bom_del_ajx======================================================
    case "do_bom_del_ajx":

		check_authority('022',"add");
		// 刪除 bom 檔
		
		if ($PHP_mat=="lots"){
			$T = "Fabric";
			$f1 = $bom->del_lots($PHP_bom_id);   
		}elseif($PHP_mat=="acc"){
			$T = "Accessory";
			$f1 = $bom->del_acc($PHP_bom_id);   // 加入
		}else{
			$message = "Error ! Don't point delete information of BOM !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$f1) {  // 沒有成功刪除資料時
			$message = $wi->msg->get(2);
		}
			
		//-------------------- 將 BOM 製造令 show out ------------------
		//  wi 主檔
		if(!$op['wi'] = $wi->get($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}


		$message = "Delete ".$T." Record on BOM : [ ".$op['wi']['wi_num' ]."]";
				# 記錄使用者動態
		$log->log_add(0,"35D",$message);
		echo $message;
		exit;
		break;
		
//=upload_bom_file======================================================		
	case "upload_bom_file":

		check_authority('022',"add");

		
		$filename = $_FILES['PHP_pttn']['name'];		
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));
		
		$f_check = 0;
		for ($i=0; $i<sizeof( $GLOBALS['VALID_TYPES']); $i++)
		{
			if ($GLOBALS['VALID_TYPES'][$i] == $ext)
			{
				$f_check = 1;
				break;
			}
		}		
		
		if ($filename && $PHP_desp)
		{
																		
		if ($_FILES['PHP_pttn']['size'] < 2072864 && $_FILES['PHP_pttn']['size'] > 0)
		{
		if ($f_check == 1){   // 上傳檔的副檔名為 mdl 時 -----

			// upload pattern file to server
			$today = $GLOBALS['TODAY'];
			$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
			$parm = array(	"file_name"		=>  $PHP_num,				
											"num"					=>  $PHP_num,
											"file_des"		=>	$PHP_desp,
											"file_user"		=>	$user_name,
											"file_date"		=>	$today
			);


			
			$A = $fils->get_name_id('bom_file_det');
			$pttn_name = $PHP_num."_".$A.".".$ext;  // 組合檔名
			$parm['file_name'] = $pttn_name;
			
			$str_long=strlen($pttn_name);
			$upload = new Upload;
			
			$upload->setMaxSize(2072864);
			
			$upload->uploadFile(dirname($PHP_SELF).'/bom_file/', 'other', 16, $pttn_name );
			$upload->setMaxSize(2072864);
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$A = $fils->upload_bom_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}			
			$message = "UPLOAD file of : ".$PHP_num;
			$log->log_add(0,"31E",$message);
		} else {  // 上傳檔的副檔名  是  exe 時 -----

			$message = "upload file is incorrect format ! Please re-send.";
		}
		}else{  //上傳檔名重覆時
			$message = "Upload file is too big";
		}
	
		}else{
			$message="You don't pick any file or add file descript.";
		}	
		if($PHP_back)
		{	
			$redir_str = "bom.php?PHP_action=".$PHP_back."&PHP_id=".$PHP_id."&PHP_msg=".$message;
		}else{
			$redir_str = "bom.php?PHP_action=bom_add&PHP_id=".$PHP_id."&PHP_msg=".$message;
		}
		redirect_page($redir_str);
		break;
		
//=do_bom_file_del======================================================
    case "do_bom_file_del":
		
	//	check_authority(3,1,"edit");	

		$f1 = $fils->del_file($PHP_talbe,$PHP_id);
		if (!$f1) {
			$op['msg'] = $fils->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		if(file_exists($GLOBALS['config']['root_dir']."/bom_file/".$PHP_file_name)){
			unlink("./bom_file/".$PHP_file_name);
		}
		
		$message = "Successfully delete file";
		if($PHP_back)
		{
			$redir_str = "bom.php?PHP_action=".$PHP_back."&PHP_id=".$PHP_wi_id."&PHP_msg=".$message;
		}else{		
			$redir_str = "bom.php?PHP_action=bom_add&PHP_id=".$PHP_wi_id."&PHP_msg=".$message;
		}
		
		redirect_page($redir_str);		
		
		break;


//=bom_view======================================================
case "bom_view":
check_authority('022',"view");
//-------------------- 將 製造令 show out ------------------
//  wi 主檔

if (isset($PHP_id))
{
if(!$op['wi'] = $wi->get($PHP_id)){    //取出該筆 製造令記錄
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
}
}else{	
if(!$op['wi']  = $wi->get(0,$PHP_bom_num)){    //取出該筆 製造令記錄
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
}
}

//  smpl 樣本檔
if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
}
$op['trim_des'] = $order_log->get_multi($op['wi']['wi_num'],'trim-rjt');



// 取出 size_scale ----------------------------------------------
$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];
	//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
$sizing = explode(",", $size_A['size']);

//  wi_qty 數量檔			
$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
$p_id = $order->get_fields('id', $where_str2,'order_partial');
$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
$p_remark = $order->get_fields('remark', $where_str2,'order_partial');
$op['num_colors'] = $op['total'] =0; $op['g_ttl'] = 0;
for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
$op['title_colspan'] = sizeof($sizing) + 3;
$k=0;
for($i=0; $i<sizeof($p_id); $i++)
{
	$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
	$T_wiqty = $wiqty->search(1,$where_str3);
	$num_colors = count($T_wiqty);
	$op['num_colors'] += $num_colors;
	$op['partial'][$i]['size'] = $num_colors;
	$op['partial'][$i]['etd'] = $p_etd[$i];
	//-------------------- 整理 供 title 部份的資料 ----------------------------------
	for ($j=0;$j<$num_colors;$j++){					
		$op['color'][$k]['name'] = $T_wiqty[$j]['colorway'];
		$T_qty = explode(",", $T_wiqty[$j]['qty']);
		$op['color'][$k]['qty'] = array_sum($T_qty);
		$k++;
	}
	// 做出 size table-------------------------------------------
	$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i],$i,$p_remark[$i]);
	$op['show_breakdown'][$i] = $reply['html'];				
	$op['total'] += $reply['total'];
	for($j=0; $j<sizeof($sizing); $j++)
	{
		$op['g_ttl'] += $reply['size_ttl'][$j];				
		$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
	}				
}

//-------------------- 整理 供 title 部份的資料 ----------------------------------
//		if ($num_colors){
//			for ($i=0;$i<$num_colors;$i++){
//				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
//				$T_qty = explode(",", $T_wiqty[$i]['qty']);
//				$op['color'][$i]['qty'] = array_sum($T_qty);
//			}
//		}else{
//			$op['colors_NONE'] = "1";
//		}			
//-----------------------------------------------------------------

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
	$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
	$op['wi']['pic_url'] = $no_img;
}
			
//  取出主料用料記錄 --------------------------------------------------------------
$op['lots_NONE']= '';
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$lots_used['lots_use'] = $smpl_lots->search(0,$where_str);  //取出該筆 樣本主料記錄

if (!is_array($lots_used['lots_use'])) {
	$op['msg'] = $smpl_lots->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['mat_support'] = 0;
for($i=0; $i<sizeof($lots_used['lots_use']); $i++)
{
	if(	$lots_used['lots_use'][$i]['support'] > 0)$op['mat_support'] = 1;
}

$num_lots_used = count($lots_used['lots_use']);
if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

//  取出副料用料記錄 --------------------------------------------------------------
$op['acc_NONE']= '';
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //取出該筆 樣本主料記錄
for($i=0; $i<sizeof($acc_used['acc_use']); $i++)
{
	if($acc_used['acc_use'][$i]['support'] > 0)$op['mat_support'] = 1;
}

if (!is_array($acc_used['acc_use'])) {     
	$op['msg'] = $smpl_acc->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$num_acc_used = count($acc_used['acc_use']);
if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

//  取出  BOM  主料記錄 --------------------------------------------------------------

$op['pa'] = $op['cust_rcv'] = '';
$op['bom_lots_NONE']= '';
$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
$bom_lots = $bom->search_lots(0,$where_str);  //取出該筆 bom 內ALL主料記錄
for ($pi=0; $pi<sizeof($bom_lots['lots']); $pi++)
{
	if ($bom_lots['lots'][$pi]['ap_mark'] <> '')
	{
		$op['pa'] = 1;
		break;
	}			
}

if (!is_array($bom_lots['lots'])) {
	$op['msg'] = $bom->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$num_bom_lots = count($bom_lots['lots']);
if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

//  取出  BOM  副料記錄 --------------------------------------------------------------
$op['bom_acc_NONE']= '';
$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
$bom_acc = $bom->search_acc(0,$where_str);  //取出該筆 bom 內ALL副料記錄

for ($pi=0; $pi<sizeof($bom_acc['acc']); $pi++)
{
	if ($bom_acc['acc'][$pi]['ap_mark'] <> '' || $op['pa'] == 1)
	{
		$op['pa'] = 1;
		break;
	}
}

if (!is_array($bom_acc['acc'])) {
	$op['msg'] = $bom->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$num_bom_acc = count($bom_acc['acc']);
if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

// --------------- 主料 ------------ 做出 $bom_lots_list[]  array....
$op['bom_lots_list'] = array();
if (!$op['lots_NONE']){   // 樣本有主料用料記錄---
	$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$op['num_colors']);
}

// ---------------------- end BOM data layout [主料] ----------------------------

// --------------- 副料 ------------ 做出 $bom_acc_list[]  array....
$op['bom_acc_list'] = array();
if (!$op['acc_NONE']){   // 樣本有副料用料記錄---
	$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$op['num_colors']);
}
// ---------------------- end BOM data layout [副料] ----------------------------


// 表格 改進用 -----
if (!$num_colors){	
	$op['total_fields'] = 10;   // BOM table的總欄位數
}else{
	$op['total_fields'] = 9 + $op['num_colors'];   // BOM table的總欄位數
}


//  取出  BOM  加購記錄 --------------------------------------------------------------
$op['ext_mat_NONE']= '';
$op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
$num_ext_mat = count($op['ext_mat']);
if (!$num_ext_mat){	$op['ext_mat_NONE'] = "1";}

//  取出  BOM  預先採購記錄 --------------------------------------------------------------
$op['pp_mat_NONE']= '';
$op['pp_mat'] = $po->get_pp_ap($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
$num_ext_mat = count($op['pp_mat']);
if (!$num_ext_mat){	$op['pp_mat_NONE'] = "1";}

//  取出  BOM  Disable計錄中己採購主料者 --------------------------------------------------------------
$op['dis_lots_NONE']= '';
$op['dis_lots'] = $bom->get_dis_lots($op['wi']['id']);  //取出該筆 bom 內記錄
$num_lots = count($op['dis_lots']);
if (!$num_lots){	$op['dis_lots_NONE'] = "1";}

//  取出  BOM  Disable計錄中己採購副料者 --------------------------------------------------------------
$op['dis_acc_NONE']= '';
$op['dis_acc'] = $bom->get_dis_acc($op['wi']['id']);  //取出該筆 bom 內記錄
$num_acc = count($op['dis_acc']);
if (!$num_acc){	$op['dis_acc_NONE'] = "1";}

$op['done']=$fils->get_bom_file($op['wi']['wi_num']);

$op['bom_log'] = $bom->get_log($op['wi']['id']);

if (isset($PHP_ex_order))
{
	$op['edit_non']=1;
}

$op['fty_pattern'] = $bom->get_fty_pattern($op['wi']['id']);

if($op['wi']['bom_status'] == 2 && $op['wi']['bsub_user'] == $GLOBALS['SCACHE']['ADMIN']['login_id']) $op['revise_flag'] = 1;
if($op['wi']['bcfm_user_id'] == $GLOBALS['SCACHE']['ADMIN']['login_id']) $op['revise_flag'] = 1;
if($GLOBALS['SCACHE']['ADMIN']['dept'] == 'SA')$op['revise_flag'] = 1;
if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
if(isset($PHP_cfm_view))
{
	$op['pp']=$PHP_sr;
	page_display($op, '022', $TPL_BOM_CFM_VIEW);
}else{
	page_display($op, '022', $TPL_BOM_VIEW);		    	    
}
break;

		

//==bom_consum_add=====================================================
	case "bom_consum_add":

		check_authority('022',"view");

		// 將 製造令 完整show out ------
		//  wi 主檔

			if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}

//--------------------------------------------------------------------------
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);
		
		//  wi_qty 數量檔			
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$p_remark = $order->get_fields('remark', $where_str2,'order_partial');
			$op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// 做出 size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i],$i,$p_remark[$i]);
				$op['show_breakdown'][$i] = $reply['html'];
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}
				
			}
		//-----------------------------------------------------------------
		
		$op['msg'] = $wi->msg->get(2);
			page_display($op, '022', $TPL_BOM_CON_ADD);			
		break;
		
//=======================================================
	case "do_bom_con_edit": //數字有錯的話,有可能是WEB顯示順序跟查詢結果順序不同

		check_authority('022',"view");
		$tmp='';
		$x=0;$k=0;

		#取出size
		$ord_size = $bom->get_ord_size($PHP_wi_id);
		$ord_size = explode(',',$ord_size['size']);
		
		$bom_size = '';
		
		#取得partial id
		$p_id = $bom->get_p_id($PHP_wi_id);
		$r=0;//筆數用
		$sum_ttl = 0;
		$s = array();
		$colors = 0; //用來判斷一筆qty應有幾筆記錄
		for($i=0;$i<sizeof($p_id);$i++){
			$ord_qty = $bom->get_ord_qty($p_id[$i]['id']);
			$colors += sizeof($ord_qty);
			for($j=0;$j<sizeof($ord_qty);$j++){
				$ttl = 0;
				$tmp_qty = explode(',',$ord_qty[$j]['qty']);
				foreach($ord_size as $key=>$value){
					$bom_size[$r][$value] = $tmp_qty[$key];
					$s[$value]+=$bom_size[$r][$value];//顏色總合計
					$ttl+=$tmp_qty[$key];
				}
				$bom_size[$r]['cut_ttl'] = $ttl;
				$sum_ttl += $ttl;
				$r++;
			}
			
			#加一行小計
			/* $ttl = 0;
			for($j=0;$j<sizeof($ord_qty);$j++){
				$tmp_qty = explode(',',$ord_qty[$j]['qty']);
				foreach($ord_size as $key=>$value){
					$bom_size[$r][$value] += $tmp_qty[$key];
					$ttl+=$tmp_qty[$key];
				}
				$bom_size[$r]['cut_ttl'] = $ttl;
			}
			$r++; */
		}
		//print_r($s);exit;
		#加一行總計
		foreach($ord_size as $kk=>$vv){
			$bom_size[$r][$vv] = $s[$vv];
		}
		$bom_size[$r]['cut_ttl'] = $sum_ttl;
					
		//print_r($bom_size);exit;

		foreach($PHP_acc_est as $key=>$value)
		{
			
				$where_str="WHERE wi_id = '$PHP_wi_id' and acc_used_id = '".$key."'  and dis_ver = 0 order by id" ;			
				$bom_acc = $bom->get_fields_acc('qty',$where_str);
				$acc_size  = $bom->get_fields_acc('size',$where_str);
				$acc_id = $bom->get_fields_acc('id',$where_str);
				if (count($bom_acc) && $value > 0)
				{
					for ($i=0; $i<sizeof($bom_acc); $i++)
					{
					
						if( sizeof(explode(',',$bom_acc[$i]))>$colors and substr($bom_acc[$i],-1,1)==","){
							$bom_acc[$i] = substr($bom_acc[$i],0,-1);// 拿掉最後一個逗號
						}
						$tmp_bom=explode(',',$bom_acc[$i]);
						//echo "<script>alert('".$colors." ".sizeof($tmp_bom)."');</script>";exit;
						
						for ($j=0; $j < sizeof($tmp_bom); $j++)
						{
							if ($tmp_bom[$j]=='' ||$tmp_bom[$j]==0)
							{
								$qty=0;
							}else{
						
								if($acc_size[$i] != '')
								{
									if(sizeof($tmp_bom) == 1)
									{
										$tmp_size_cout = sizeof($bom_size) - 1;
										$qty=$bom_size[$tmp_size_cout][$acc_size[$i]]*$value;
										//echo "<script>alert('".$bom_size[$tmp_size_cout][$acc_size[$i]]." * ".$value."');</script>";
										//exit;
									
									}else{
										$qty=$bom_size[$j][$acc_size[$i]]*$value;									
									}
								}else{
									if(sizeof($tmp_bom) == 1)
									{
										$tmp_size_cout = sizeof($bom_size) - 1;
										$qty=$bom_size[$tmp_size_cout]['cut_ttl']*$value;
									}else{
										$qty=$bom_size[$j]['cut_ttl']*$value;
									}								
								}
							}
							$tmp=$tmp.$qty.",";
						}
						//echo $tmp;
						//exit;
						$tmp=substr($tmp,0,-1);	
						$parm_bom_acc[$k]=array($acc_id[$i],'qty',$tmp,'bom_acc');
					
						$tmp='';
						$k++;
					}
				}
				$parm_acc[$x]=array($key,'est_1',$value);			
				$x++;	
		
		}
		
		for ($i=0; $i<sizeof($parm_bom_acc); $i++)	$bom->update_field($parm_bom_acc[$i]);
		for ($i=0; $i<sizeof($parm_acc); $i++)		$smpl_acc->update_field($parm_acc[$i]);
				
		$parm=array($PHP_wi_id,'revise',($PHP_revise+1))	;
		$wi->update_field($parm);

		$redt_p="bom.php?&PHP_action=bom_view".$PHP_back_str2."&PHP_id=".$PHP_wi_id;
		redirect_page($redt_p);
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   bom_cfm      //  WI CFM...........
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "bom_uncfm":
		check_authority('023',"view");
		
		if (!$op = $wi->search_uncfm('bom')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
//		$back_str="&PHP_dept_code=&PHP_etdfsh=&PHP_cust=&PHP_wi_num=&PHP_etdstr=&PHP_fty_sch=";

		//2006/05/02 update
		// $op['dept_id'] = get_dept_id();
		
		// 如果是 manager 進入時...
		// if (substr($op['dept_id'],0,7) == "<select"){
			// $op['manager_flag'] = 1;
		// }	
				// $op['msg']= $wi->msg->get(2);
//				$op['back_str']= $back_str;

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

			page_display($op, '023', $TPL_BOM_UNCFM_LIST);
		break;
		
		
//=======================================================
	case "do_bom_cfm":
		check_authority('023',"edit");
		
		$parm = array(	"order_num" =>	$PHP_order_num,
										"id"				=>	$PHP_id,
										"date"			=>	$dt['date_str'],
										"user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
					  );
		$f1 = $wi->bom_cfm($parm);
		if (!$f1) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array($PHP_id, 'bom_status', '4');
		$wi->update_field($parm);	
		
		$message="success CFM BOM:[".$PHP_wi."]";
		$log->log_add(0,"36A",$message);
		if (!$op = $wi->search_uncfm('bom')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'bom_rev','1');
				$wi->update_field($parm);
		}

		$ord_ary[] =$PHP_order_num;
		$ord_rec = $order->get('',$PHP_order_num);
		$pdt_rec = $order->get_pdtion($PHP_order_num, $ord_rec['factory']);
		
		if(!$pdt_rec['mat_shp'])$f1 = $receive->add_ord_rcvd('','','l',$ord_ary);
		if(!$pdt_rec['m_acc_shp'])$f1 = $receive->add_ord_rcvd('','','a',$ord_ary,2);
		if(!$pdt_rec['acc_shp'])$f1 = $receive->add_ord_rcvd('','','a',$ord_ary,1);

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// 如果是 manager 進入時...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}
		//新增區塊，當confirm後須將該訂單相關資料產出成Excel，傳送到指定位置(for wipone用)
		$export_status = $export -> export4wipone($PHP_order_num);
		//exit;
		
		$notify->system_msg_send('3-6-C','PM',$PHP_wi,$PHP_wi);
	
		//echo "2<br>";
		$op['msg'][]= $message;
		//echo "3<br>";
		//print_r($op);
		page_display($op, '023', $TPL_BOM_UNCFM_LIST);
		break;


//=======================================================
	case "do_bom_reject":
		check_authority('023',"edit");

		$parm = array($PHP_id, 'bom_status', '0');
		$wi->update_field($parm);	
		
		$message="success REJECT BOM:[".$PHP_wi."]";
		$log->log_add(0,"36A",$message);
		if (!$op = $wi->search_uncfm('bom')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'bom_rev','1');
				$wi->update_field($parm);
		}

		

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// 如果是 manager 進入時...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}
		
				$op['msg'][]= $message;
			page_display($op, '023', $TPL_BOM_UNCFM_LIST);
		break;		
		
//=revise_bom======================================================
    case "revise_bom":

		check_authority('022',"add");
		//-------------------- 將 BOM 製造令 show out ------------------
		//  wi 主檔	
		if(!$op['wi'] = $wi->get($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		$op['wi']['bom_rev']=$op['wi']['bom_rev']+1;
		
		$parm = array(	"order_num" =>	$op['wi']['style_code'],
						"id"		=>	$PHP_id,
						"date"		=>	'0000-00-00 00:00:00',
						"user"		=>	'',
						"bom_rev"	=>	$op['wi']['bom_rev']
					  );

		
		$f1=$wi->revise_bom($parm);
		$parm = array($PHP_id, 'bom_status', '0');
		$wi->update_field($parm);	
				
		if (!$f1)
		{
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		
		
		
		//  smpl 樣本檔
		if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);
		//------------- wi_qty 數量檔 ----------------------------------
		//  wi_qty 數量檔				
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$p_remark = $order->get_fields('remark', $where_str2,'order_partial');
			$op['num_colors'] = $op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			$k=0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				$op['num_colors'] += $num_colors;
				$op['partial'][$i]['size'] = $num_colors;
				$op['partial'][$i]['etd'] = $p_etd[$i];
				//-------------------- 整理 供 title 部份的資料 ----------------------------------
				for ($j=0;$j<$num_colors;$j++){					
					$op['color'][$k]['name'] = $T_wiqty[$j]['colorway'];
					$T_qty = explode(",", $T_wiqty[$j]['qty']);
					$op['color'][$k]['qty'] = array_sum($T_qty);
					$color_qty[$k] = array_sum($T_qty);
					for ($l=0; $l<sizeof($T_qty); $l++) $size_qty[$sizing[$l]][$k] = $T_qty[$l];	
					$k++;
				
				}
				// 做出 size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i],$i,$p_remark[$i]);
				$op['show_breakdown'][$i] = $reply['html'];				
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}				
			}
		//-----------------------------------------------------------------

		// 相片的URL決定 ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

		//  取出主料用料記錄 --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smpl_lots->search(0,$where_str);  //取出該筆 樣本主料記錄
//		$op['lots_used'] = $lots_used['lots_use'];

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  取出副料用料記錄 --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //取出該筆 樣本主料記錄
//		$op['acc_used'] = $acc_used['acc_use'];

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $bom->search_lots(0,$where_str);  //取出該筆 bom 內ALL主料記錄
		$op['bom_lots'] = $bom_lots['lots'];

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}
		
		//  取出  BOM  副料記錄 --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $bom->search_acc(0,$where_str);  //取出該筆 bom 內ALL副料記錄
		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- 整理部份資料 ---- for bom table TITLE------------------------------
//		if ($num_colors){
//			for ($i=0;$i<$num_colors;$i++){
//				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
//				$T_qty = explode(",", $T_wiqty[$i]['qty']);
//				$op['color'][$i]['qty'] = array_sum($T_qty);
//				$color_qty[$i] = array_sum($T_qty);
//				for ($j=0; $j<sizeof($T_qty); $j++)
//				{
//					$size_qty[$sizing[$j]][$i] = $T_qty[$j];
//				}
//
//			}
//		} else {
//				$color_qty[] = 0;   // 避免 error
//				$op['colors_NONE'] = "1";
//		}

	// --------------- 主料 ------------ 做出 $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // 樣本有主料用料記錄---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$op['num_colors'], $PHP_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [主料] ----------------------------

	// --------------- 副料 ------------ 做出 $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // 樣本有副料用料記錄---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$op['num_colors'], $PHP_id, $color_qty,$sizing,$size_qty);	
		} 
	// ---------------------- end BOM data layout [副料] ----------------------------

	// 表格 改進用 -----
			if (!$num_colors){	
				$op['total_fields'] = 9;   // BOM table的總欄位數
			}else{
				$op['total_fields'] = 8 + $op['num_colors'];   // BOM table的總欄位數
			}
		$op['done']=$fils->get_bom_file($op['wi']['wi_num']);

		// ---------------------- end BOM data layout table ----------------------------


		page_display($op, '022', $TPL_BOM_CFM_EDIT);  	    
		break;
		
//=bom_revise_view======================================================
    case "bom_revise_view":

		check_authority('022',"view");
		//-------------------- 將 製造令 show out ------------------
		//  wi 主檔
		if(!$op['wi'] = $wi->get($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl 樣本檔
		if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);
		//  wi_qty 數量檔			
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$p_remark = $order->get_fields('remark', $where_str2,'order_partial');
			$op['num_colors'] = $op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			$k=0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				$op['num_colors'] += $num_colors;
				$op['partial'][$i]['size'] = $num_colors;
				$op['partial'][$i]['etd'] = $p_etd[$i];
				//-------------------- 整理 供 title 部份的資料 ----------------------------------
				for ($j=0;$j<$num_colors;$j++){					
					$op['color'][$k]['name'] = $T_wiqty[$j]['colorway'];
					$T_qty = explode(",", $T_wiqty[$j]['qty']);
					$op['color'][$k]['qty'] = array_sum($T_qty);
					$k++;
				}
				// 做出 size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i],$p_remark[$i]);
				$op['show_breakdown'][$i] = $reply['html'];				
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}				
			}
		//-----------------------------------------------------------------

		// 相片的URL決定 ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}
					
		//  取出主料用料記錄 --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smpl_lots->search(0,$where_str);  //取出該筆 樣本主料記錄

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  取出副料用料記錄 --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //取出該筆 樣本主料記錄

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $bom->search_lots(0,$where_str);  //取出該筆 bom 內ALL主料記錄

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  取出  BOM  副料記錄 --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $bom->search_acc(0,$where_str);  //取出該筆 bom 內ALL副料記錄

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- 整理 供 title 部份的資料 ----------------------------------
//		if ($num_colors){
//			for ($i=0;$i<$num_colors;$i++){
//				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
//				$T_qty = explode(",", $T_wiqty[$i]['qty']);
//				$op['color'][$i]['qty'] = array_sum($T_qty);
//			}
//		}else{
//			$op['colors_NONE'] = "1";
//		}

		// --------------- 主料 ------------ 做出 $bom_lots_list[]  array....
			$op['bom_lots_list'] = array();

		if (!$op['lots_NONE']){   // 樣本有主料用料記錄---
			$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$op['num_colors']);

		}   
		// ---------------------- end BOM data layout [主料] ----------------------------

		// --------------- 副料 ------------ 做出 $bom_acc_list[]  array....
			$op['bom_acc_list'] = array();

		if (!$op['acc_NONE']){   // 樣本有副料用料記錄---
			$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$op['num_colors']);

		}  
		// ---------------------- end BOM data layout [副料] ----------------------------

			// 表格 改進用 -----
			if (!$num_colors){	
				$op['total_fields'] = 10;   // BOM table的總欄位數
			}else{
				$op['total_fields'] = 9 + $op['num_colors'];   // BOM table的總欄位數
			}
		// ---------------------- end BOM data layout table ----------------------------
			$message = "success review bom : ".$op['wi']['wi_num'];
			$log->log_add(0,"35E",$message);
			$op['msg'][] = $message;
			page_display($op, '022', $TPL_BOM_REVISE_SHOW);		    	    
		break;

		
		
		
		
		

//=bom_print======================================================
    case "bom_print":   //  .......

		if(!$admin->is_power('022',"view")){ 
			$op['msg'][] = "sorry! 您沒有這項權限!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//-------------------- 將 製造令 資料 由資料庫取出 ------------------
		//  wi 主檔
		if(!$wi = $wi->get(0,$PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl 樣本檔
		if(!$smpl = $order->get($wi['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);     
					break;
		}
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);
		//-------------------- wi_qty 數量檔 -------------------------------
			$where_str2 = " WHERE ord_num = '".$wi['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$op['total'] = $color_cut =0;			
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$wi['id']."' AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// 做出 size table-------------------------------------------
				$reply = get_colorway_qty($T_wiqty,$sizing);
				$data[$i] = $reply['data'];
				$op['total'] += $reply['total'];
				$colorway_name[$i] = $reply['colorway'];
				$colorway_qty[$i] = $reply['colorway_qty'];		
				$color_cut +=sizeof($data[$i]);
				$use_title[$p_etd[$i]]['name'] = $reply['colorway'];	
				$use_title[$p_etd[$i]]['qty'] = $reply['colorway_qty'];
			}

		//-----------------------------------------------------------------

		// 相片的URL決定 -----------------------------------------------------------------
			$style_dir	= "./picture/";  
			$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
				$wi['pic_url'] = $style_dir.$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}
		

		$bom_lots = $bom->search_lots_det($wi['id']);  //取出該筆 bom 內ALL主料記錄
		$bom_acc = $bom->search_acc_det($wi['id']);  //取出該筆 bom 內ALL副料記錄

		$bom_log = $bom->get_log($wi['id']);


//---------------------------------- 資料庫作業結束 ------------------------------

	$print_title = "Bill Of Material";
	$wi['bcfm_date']=substr($wi['bcfm_date'],0,10);
	$print_title2 = "VER.".$wi['bom_rev']."  on  ".$wi['bcfm_date'];
	$creator = $wi['bcfm_user'];
	$ll = 0;
	$parm=array( 'PHP_id'		=>	$PHP_id,
				 'prt_ord'				=>	'Order #',
				 'cust'						=>	$smpl['cust_iname'],
				 'ref'						=>	$smpl['ref'],
				 'dept'						=>	$smpl['dept'],
				 'size_scale'			=>	$smpl['size_scale'],
				 'style'					=>	$smpl['style'],
				 'qty'						=>	$op['total'],
				 'unit'						=>	$wi['unit'],
				 'etd'						=>	$smpl['etd'],
				 'pic_url'				=>	$wi['pic_url'],
				 );
				 
if($color_cut < 5)
{
	include_once($config['root_dir']."/lib/class.pdf_bom.php");



	$pdf=new PDF_bom();
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',14);



	$pdf->hend_title($parm);			 
		$Y = $pdf->getY();
//		$pdf->setx(20);
		$img_size = GetImageSize($wi['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,40,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,40);
		}
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
// 設定 colorway的數量表 - 欄位的寬  [size breakdown]
// 由 80~200 保留 30為 colorway內容 其它 90分配給數量欄
	$num_siz = count($sizing);
	$w_qty = intval(95/ ($num_siz+1));
	$w = array('40');  // 第一格為 30寬
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}

	for($i=0; $i<sizeof($p_etd); $i++)
	{
		$header = array_merge(array('Colorway') , $sizing);	
		$table_title = "Size Breakdown";
		if(sizeof($p_etd) > 1)$table_title .= '[ ETD : '.$p_etd[$i].' ]';
		$R = 100 ;
		$G = 85 ;
		$B = 85 ;
		$Y = 70+($i*(sizeof($data[$i])+2)*8);
		if (count($sizing)){
			$pdf->Table_1(70,$Y,$header,$data[$i],$w,$table_title,$R,$G,$B,$size_A['base_size']);
		}else{
			$pdf->Cell(10,70,'there is no sizing data exist !',1);
		}
		//$pdf->ln();
		$ll+=sizeof($data)+2;	
	}
		$Y =$pdf->getY();
		// 定訂 圖以下的座標 
	//	$pdf->SetXY($X,$Y);
	
		$pdf->ln();
		$header1 = array('Fabric #','Placement','Consump.','color/cat.','size','');
		$title = 'Fabric usage';
		$w1 =  array(18,55,18,24,5,0.5);
		$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
		$ll+=4;
		for($i=0; $i<sizeof($bom_lots); $i++)
		{
			$ll+=2;
			if ($ll >= 36)
			{	    
				$ll=-10;
				$pdf->AddPage();
				$pdf->SetXY(10,30);
				$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
			}
			$bom_lots[$i]['des_m']='';
			if($bom_lots[$i]['weight'])$bom_lots[$i]['des_m'] .="   Weight : ".$bom_lots[$i]['weight'].";";
			if($bom_lots[$i]['width'])$bom_lots[$i]['des_m'] .="  c.Width : ".$bom_lots[$i]['width'].";";
			if($bom_lots[$i]['des'])$bom_lots[$i]['des_m'] .="   Supplier's # : ".$bom_lots[$i]['des'].";";
			if($bom_lots[$i]['comp'])$bom_lots[$i]['des_m'] .="  Construction : ".$bom_lots[$i]['comp'].";";
			if($bom_lots[$i]['des_m'])$bom_lots[$i]['des_m'] = substr($bom_lots[$i]['des_m'],0,-1);		
			$pdf->Table_2($bom_lots[$i],$w1);
		}
		$ll+=4;
		if ($ll >= 36)
		{	    
			$ll=-10;
			$pdf->AddPage();
			$pdf->SetXY(10,30);		
		}						
//副料
		$header1 = array('Acc. #','Placement','Consump.','color/cat.','size','');
		$title = 'Accessory usage';
		$w1 =  array(18,55,18,24,5,0.5);
		$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
		for($i=0; $i<sizeof($bom_acc); $i++)
		{
			$ll+=2;
			if ($ll >= 36)
			{	    
				$ll=-10;
				$pdf->AddPage();
				$pdf->SetXY(10,30);
				$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
			}
			$bom_acc[$i]['des_m']='';
			if($bom_acc[$i]['specify']) $bom_acc[$i]['des_m'] = "Construction : ".$bom_acc[$i]['specify'];
			$pdf->Table_2($bom_acc[$i],$w1);
			
		}	



	if(sizeof($bom_log) > 0)
	{
		$pdf->ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(197,6,'REMARK RECORDS',1,1,'C');
		$pdf->Cell(25,6,'Date',1,0,'C');
		$pdf->Cell(147,6,'Date',1,0,'C');
		$pdf->Cell(25,6,'user',1,1,'C');
	}
	for($i=0; $i<sizeof($bom_log); $i++)
	{
		$pdf->SetFont('Big5','',9);
		$pdf->Cell(25,6,$bom_log[$i]['k_date'],1,0,'C');
		$pdf->Cell(147,6,$bom_log[$i]['des'],1,0,'L');
		$pdf->Cell(25,6,$bom_log[$i]['user'],1,1,'C');
	}

	$name=$wi['wi_num'].'_bom.pdf';
	$pdf->Output($name,'D');
}else{
	$ary_title = $parm;
	include_once($config['root_dir']."/lib/class.pdf_bom_L.php");

	$pdf=new PDF_bom_L('L','mm','A4');
	$pdf->AddBig5Font();	
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',14);
	$ll = 0;


//	$pdf->hend_title($parm);			 
		$Y = $pdf->getY();

		$img_size = GetImageSize($wi['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,50,40,0);
		}else{
			$pdf->Image($wi['pic_url'],10,50,0,40);
		}
		$pdf->ln();
		
		$Y = $pdf->getY();
		$X = $pdf->getX();
// 設定 colorway的數量表 - 欄位的寬  [size breakdown]
// 由 80~200 保留 30為 colorway內容 其它 90分配給數量欄
	$num_siz = count($sizing);
	$w_qty = intval(175/ ($num_siz+1));
	$w = array('50');  // 第一格為 50寬
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}

	for($i=0; $i<sizeof($p_etd); $i++)
	{
		$header = array_merge(array('Colorway') , $sizing);	
		$table_title = "Size Breakdown";
		if(sizeof($p_etd) > 1)$table_title .= '[ ETD : '.$p_etd[$i].' ]';
		$R = 100 ;
		$G = 85 ;
		$B = 85 ;
		$Y = 50+($i*(sizeof($data[$i])+1)*8);
		if (count($sizing)){
			$pdf->Table_1(50,$Y,$header,$data[$i],$w,$table_title,$R,$G,$B,$size_A['base_size']);
		}else{
			$pdf->Cell(10,70,'there is no sizing data exist !',1);
		}
		//$pdf->ln();
		$ll+=sizeof($data)+2;	
	}
//主料	
		$pdf->ln();
		$header1 = array('Fabric #','Placement','Consump.','color/cat.','size','');
		$title = 'Fabric usage';
		$w1 =  array(18,55,18,26,5,0.5);
		$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
		$ll+=4;
		for($i=0; $i<sizeof($bom_lots); $i++)
		{
			$ll+=2;
			if ($ll >= 20)
			{	    
				$ll=-4;
				$pdf->AddPage();
				$pdf->SetXY(10,50);
				$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
			}
			$bom_lots[$i]['des_m']='';
			if($bom_lots[$i]['weight'])$bom_lots[$i]['des_m'] .="   Weight : ".$bom_lots[$i]['weight'].";";
			if($bom_lots[$i]['width'])$bom_lots[$i]['des_m'] .="  c.Width : ".$bom_lots[$i]['width'].";";
			if($bom_lots[$i]['des'])$bom_lots[$i]['des_m'] .="   Supplier's # : ".$bom_lots[$i]['des'].";";
			if($bom_lots[$i]['comp'])$bom_lots[$i]['des_m'] .="  Construction : ".$bom_lots[$i]['comp'].";";
			if($bom_lots[$i]['des_m'])$bom_lots[$i]['des_m'] = substr($bom_lots[$i]['des_m'],0,-1);		
			$pdf->Table_2($bom_lots[$i],$w1);
		}
		$ll+=4;
		if ($ll >= 20)
		{	    
			$ll=-4;
			$pdf->AddPage();
			$pdf->SetXY(10,50);		
		}		
//副料	
		$header1 = array('Acc. #','Placement','Consump.','color/cat.','size','');
		$title = 'Accessory usage';
		$w1 =  array(18,55,18,26,5,0.5);
		$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
		for($i=0; $i<sizeof($bom_acc); $i++)
		{
			$ll+=2;
			if ($ll >= 20)
			{	    
				$ll=-4;
				$pdf->AddPage();
				$pdf->SetXY(10,50);
				$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
			}
			$bom_acc[$i]['des_m']='';
			if($bom_acc[$i]['specify']) $bom_acc[$i]['des_m'] = "Construction : ".$bom_acc[$i]['specify'];
			$pdf->Table_2($bom_acc[$i],$w1);
			
		}				


	$name=$wi['wi_num'].'_bom.pdf';
	$pdf->Output($name,'D');


}
break;

//= trim_add ====================================================

	case "do_bom_log_add":

		check_authority('022',"edit");

	$parm= array(	'wi_id'			=>	$PHP_id,
								'des'				=>	$PHP_des,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
		$f1=$bom->add_log($parm);
		$message = "Suucess add log on : ".$PHP_num;
		$redir_str="bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);



//= trim_add ====================================================

case "bom_submit":
check_authority('022',"edit");

$parm = array($PHP_id, 'bom_status', '2');
$wi->update_field($parm);	

$parm = array($PHP_id, 'bsub_user', $GLOBALS['SCACHE']['ADMIN']['login_id']);
$wi->update_field($parm);	

$message = "Successfully SUBMIT BOM : [".$PHP_wi_num."]";
$log->log_add(0,"35S",$message);

$redir_str="bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
redirect_page($redir_str);
break;



#fty assort -> upload_fty_pattern
case "upload_fty_pattern":
	check_authority('022',"edit");
	$op['back_str'] = $PHP_back_str;
	$filename = $_FILES['PHP_pttn']['name'];
	$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));
	
	$parm = array(	"wi_id"			=>	$PHP_id,
					"file_ext"		=>	$ext,
					"file_name"		=>	$PHP_fty_des,
					"file_date"		=>	$TODAY,
					"user"			=>	$GLOBALS['SCACHE']['ADMIN']['name']
			);
		//$wi_parm = array($PHP_num,'trim_date',$TODAY);
	$A=$bom->add_fty_pattern($parm);
	$pttn_name = $A.".".$ext;  // 指定為檔名
	$upload = new Upload;
	$upload->uploadFile(dirname($PHP_SELF).'/fty_pattern/', 'other', 16, $pttn_name );
		
	if (!$upload){
		$op['msg'][] = $upload;				
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	$message = "UPLOAD fty pattern of ".$PHP_id;
	$log->log_add(0,"35E",$message);
	
	$redir_str="bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
	redirect_page($redir_str);

break;



//= end case ======================================================
}

?>
