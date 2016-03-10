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

$TPL_TRANSPORT_SEARCH = "transport_search.html";
$TPL_TRANSPORT_LIST = "transport_list.html";
$TPL_TRANSPORT_VIEW = "transport_view.html";
$TPL_TRANSPORT_EDIT = "transport_edit.html";
$TPL_TRANSPORT_REVISE_VIEW = "transport_revise_view.html";
$TPL_TRANSPORT_RECEIVE_VIEW = "transport_receive_view.html";

switch ($PHP_action) {
//= start case ====================================================

	case "transport":
	check_authority(10,1,"view");
	
	page_display($op, 10, 1, $TPL_TRANSPORT_SEARCH);
	break;



	case "transport_list":
	check_authority(10,1,"view");
	
	$op = $transport->search(0);
	if (!$op) {
		$op['msg'] = $order->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	if(!empty($PHP_ord_num) || !empty($PHP_trn_num))
	$op['back_str'] = "&PHP_ord_num=".$PHP_ord_num."&PHP_trn_num=".$PHP_trn_num;
	
	page_display($op,10,1, $TPL_TRANSPORT_LIST);
	break;



	case "transport_view":
	check_authority(10,1,"view");

	$op = $transport->get($trn_num);
	$op['ord_num'] = $op['ord_num'];
	if(!empty($PHP_ord_num) || !empty($PHP_trn_num))
	$op['back_str'] = '&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num;

/*
	if(!$op['wi']  = $wi->get(0,$op['order_num'])){    //取出該筆 製造令記錄
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

	if(!$op['smpl'] = $order->get($op['wi']['smpl_id']) ){
		$op['msg']= $order->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

	//-------------------- wi_qty 數量檔 -------------------------------
	$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
	$T_wiqty = $wiqty->search(1,$where_str);

	$num_colors = count($T_wiqty);
	$op['num_colors'] = $num_colors;

	// 取出 size_scale ----------------------------------------------
	$size_A = $size_des->get($op['smpl']['size']);
	$op['smpl']['size_scale']=$size_A['size_scale'];
	// 注意 有時沒有 size type 資料時
	$sizing = explode(",", $size_A['size']);

	// 做出 size table-------------------------------------------
	$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
	$op['show_breakdown'] = $reply['html'];
	$op['total'] = $reply['total'];   // 總件數
	//-----------------------------------------------------------------
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
	$op['pa'] = '';
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
	#$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
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

	//-------------------- 整理 供 title 部份的資料 ----------------------------------
	if ($num_colors){
		for ($i=0;$i<$num_colors;$i++){
			$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
			$T_qty = explode(",", $T_wiqty[$i]['qty']);
			$op['color'][$i]['qty'] = array_sum($T_qty);
		}
	}else{
		$op['colors_NONE'] = "1";
	}

	// --------------- 主料 ------------ 做出 $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	if (!$op['lots_NONE']){   // 樣本有主料用料記錄---
		$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors,1);
	}   
	// ---------------------- end BOM data layout [主料] ----------------------------

	// --------------- 副料 ------------ 做出 $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
	if (!$op['acc_NONE']){   // 樣本有副料用料記錄---
		$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors,1);
	}  
	// ---------------------- end BOM data layout [副料] ----------------------------


*/
	if( !empty($PHP_receive) == 1 ){
		#填寫驗收數量
		page_display($op,10,1, $TPL_ORDER_RECEIVE_VIEW);
	}else{	
		page_display($op,10,1, $TPL_TRANSPORT_VIEW);
	}
	break;



	case "transport_edit":
	check_authority(10,1,"edit");
	
	$op = $transport->get($trn_num);
	$op['ord_num'] = $transport_ord_num;
	if(!empty($PHP_ord_num) || !empty($PHP_trn_num))
	$op['back_str'] = '&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num;
	
	page_display($op,10,1, $TPL_TRANSPORT_EDIT);
	break;



	case "do_transport_edit":
	check_authority(10,1,"edit");

	if( $transport->edit($qty) ){
		$transport->update_field_id('remark',$remark,$trn_num,'transport');
	}
	redirect_page($PHP_SELF.'?PHP_action=transport_view&trn_num='.$trn_num.'&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num);
	break;



	case "transport_submit":
	check_authority(10,1,"edit");

	$transport->update_field_id('sub_date',date('Y-m-d'),$trn_num,'transport');
	$transport->update_field_id('sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$trn_num,'transport');
	
	redirect_page($PHP_SELF.'?PHP_action=transport_view&trn_num='.$trn_num.'&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num);
	break;



	case "transport_revise":
	check_authority(10,1,"edit");

	$op = $transport->get($trn_num);
	$op['ord_num'] = $op['ord_num'];
	if(!empty($PHP_ord_num) || !empty($PHP_trn_num))
	$op['back_str'] = '&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num;
	
	page_display($op,10,1, $TPL_TRANSPORT_REVISE_VIEW);
	break;



	case "do_transport_revise":
	check_authority(10,1,"edit");

	$transport->update_field_id('revise',$revise+1,$trn_num,'transport');
	$transport->update_field_id('sub_date','0000-00-00',$trn_num,'transport');
	$transport->update_field_id('sub_user','',$trn_num,'transport');
	if( $transport->edit($qty) ){
		$transport->update_field_id('remark',$remark,$trn_num,'transport');
	}
	
	redirect_page($PHP_SELF.'?PHP_action=transport_view&trn_num='.$trn_num.'&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num);
	break;



	case "transport_receive":
	check_authority(10,1,"view");

	$op = $transport->get($transport_id);
	$op['ord_num'] = $op['ord_num'];
	if(!empty($PHP_ord_num) || !empty($PHP_trn_num))
	$op['back_str'] = '&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num;
	
	page_display($op,10,1, $TPL_TRANSPORT_RECEIVE_VIEW);
	break;



	case "do_transport_receive":
	check_authority(10,1,"edit");

	$transport->update_field_id('rcv_rmk',$rcv_rmk,$transport_id,'tran_det');
	#$transport->update_field_id('sub_date','0000-00-00',$transport_id,'transport');
	#$transport->update_field_id('sub_user','',$transport_id,'transport');
	#$transport->edit($transport_id,$qty,$remark);
	
	redirect_page($PHP_SELF.'?PHP_action=transport_view&transport_id='.$transport_id.'&PHP_ord_num='.$PHP_ord_num.'&PHP_trn_num='.$PHP_trn_num);
	break;
//= end case ======================================================
}

?>
