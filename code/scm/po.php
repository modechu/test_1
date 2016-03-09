<?php
session_start();
session_register('SCACHE');
session_register('PAGE');
session_register('authority');
session_register('where_str');
session_register('parm');
session_register('PHP_ses_etd');
session_register('PHP_unstatus');

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

# 採購明細新增 20120519
$FAB_ACC = array('FAB','ACC');
$Po_Group = array('業務一','業務二');
$Po_Group_value = array('D','K');

$TPL_PO_STATUS_TRACK = "po_track.html";
$TPL_PO_EX_ADD_SCH = "po_ex_add_sch.html";
$TPL_PO_VIEW_WI_SUB = "po_view_wi_sub.html";
$TPL_APLY_EX_VIEW_BOM = "aply_ex_view_bom.html";
$TPL_PO_EX_SHOW = "po_ex_show.html";
$TPL_APLY_EX_EDIT="aply_ex_edit.html";
$TPL_PO_EX_ADD = "po_ex_add.html";

$TPL_PO_PP_ADD_SCH = "po_pp_add_sch.html";
$TPL_PO_VIEW_PP_SUB = "po_view_pp_sub.html";
$TPL_PO_PP_SHOW = "po_pp_show.html";
$TPL_APLY_PP_EDIT="aply_pp_edit.html";
$TPL_PO_PP_ADD = "po_pp_add.html";

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();

/*
include_once($config['root_dir']."/lib/class.notify.php");
$notify = new NOTIFY();
if (!$notify->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
*/

// if($GLOBALS['SCACHE']['ADMIN']['login_id'] != 'mode'){
	// $layout->display('sorry.html'); 
	// break;
// } 

//訂單ETD,ETP限制
$tmp = $para->get(0,'po_accept');
$po_accept_qty = increceDaysInDate($TODAY,$tmp['set_value']);
// echo $PHP_action.'<br>';

switch ($PHP_action) {
#++++++++++++++    SAMPLE ORDER +++++++++++++  2007/09/11  +++++++++++++++++
#		 job 54    請購記錄 
#++++++++++++++++++++++++++++++++++++++++++++  2007/09/11  +++++++++++++++++
#		case "po":								job 54		採購新增/search畫面
#		case "po_search_bom":					job 54		新增採購--搜尋要採購的訂單
#		case "po_bom_view":						job 54		新增採購--檢視要採購的訂單(BOM表)
#		case "po_add":							job 54		新增採購內容
#		case "chg_unit_ajax":					job 54		新增採購--改變採購單位
#		case "count_range_ajax":			    job 54		新增採購--計算採購增加量(範圍)
#		case "add_po_qty":						job 54		新增採購--儲存採購量
#		case "po_view":							job 54		採購內容檢視
#		case "do_apply_logs_add":			    job 54		Log檔記錄
#		case "po_edit":							job 54		採購修改
#		case "po_revise":						job 54		採購Revise
#		case "po_search":						job 54		採購列表搜尋
#		case "po_submit":						job 54		採購submit
#		case "po_cfm_search":					job 54		採購列表搜尋--需CFM項目
#		case "do_po_cfm":						job 54		採購Confrim
#		case "reject_po_cfm":					job 54		採購reject (CFM時)
#		case "po_apv_search":					job 54		採購列表搜尋--需APV項目
#		case "po_apv_view":						job 54		採購apv畫面檢視
#		case "do_po_apv":						job 54		採購approval
#		case "reject_po_apv":					job 54		採購reject



/*----------
# 功能說明 : 重新開啟已 APV 的 PO
----------*/
case "reopen":
check_authority('037',"admin");

$po_num=str_replace("A", "O",$PHP_aply_num);

if( $po->reopen($po_num) ) {
    $message="Successfully reopen PO : ".$po_num;
}

$po = $po->get_mat($PHP_aply_num);
foreach($po as $key => $val) {	
    # RESET PO STATUS FOR PDTION
    $GLOBALS['order']->reset_pdtion($val['mat_cat'],$val['wi_id']);
}

$log->log_add(0,"037R",$message);
$messg  = '<a href="po.php?PHP_action=po_view&PHP_aply_num='.$PHP_aply_num.'">'.$po_num.'</a>'.' by '.$GLOBALS['SCACHE']['ADMIN']['name'];
$notify->system_msg_send('037-R','PM',$po_num,$messg);

$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message;
redirect_page($redir_str);
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_ex_bom_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_ex_bom_view":
check_authority('037',"view");
// print_r($_GET);
// print_r($_POST);
$op['wi'] = $wi->get($PHP_id);
$op['smpl'] = $order->get($op['wi']['smpl_id']);

$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
$T_wiqty = $wiqty->search(1,$where_str);

$num_colors = count($T_wiqty);
$op['num_colors'] = $num_colors;

$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];

$sizing = explode(",", $size_A['size']);

// 做出 size table-------------------------------------------
$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
$op['show_breakdown'] = $reply['html'];
$op['total'] = $reply['total'];   // 總件數
//-----------------------------------------------------------------

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
	$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
	$op['wi']['pic_url'] = $no_img;
}
// echo $PHP_bom_code;
if( $PHP_mat_cat == 'l' ) {
	// 取出  BOM  主料記錄 --------------------------------------------------------------
	$op['bom_lots_list'] = $po->get_lots_det($op['wi']['id']);
} else {
	// 取出  BOM  副料記錄 --------------------------------------------------------------
	$op['bom_acc_list'] = $po->get_acc_det($op['wi']['id']);
}
// print_r($op['bom_acc_list']);
if(isset($PHP_sr_startno))
{
	$back_str="?PHP_action=apply_search_bom&PHP_sr=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
	$op['back_str']=$back_str;
}

$op['mat_id'] = $PHP_bom_id;
$op['mat_cate'] = $PHP_mat_cat;
$op['mat_code'] = $PHP_bom_code;

if(isset($PHP_pa_num))
{
	$op['pa_num'] = $PHP_pa_num;
	$op['po_num'] = 'PO'.substr($PHP_pa_num,2);
	$ap_rec = $apply->get($PHP_pa_num);
	$op['ap_det'] = $ap_rec['ap_det'];
	$op['ap'] = $ap_rec['ap'];
}
// print_r($op);
page_display($op, '037', $TPL_APLY_EX_VIEW_BOM);
break;		




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	 case "apply_bom_view":			job 51 PHP_mat_cat
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_bom_view":
check_authority('037',"view");
//-------------------- 將 製造令 show out ------------------

//  wi 主檔 + smpl 樣本檔
$op['wi'] = $wi->get($PHP_id);
$op['smpl'] = $order->get($op['wi']['smpl_id']);
//-------------------- wi_qty 數量檔 -------------------------------
$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
$T_wiqty = $wiqty->search(1,$where_str);

$num_colors = count($T_wiqty);
$op['num_colors'] = $num_colors;

// 取出 size_scale ----------------------------------------------
$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];

//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
$sizing = explode(",", $size_A['size']);

// 做出 size table-------------------------------------------
$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
$op['show_breakdown'] = $reply['html'];
$op['total'] = $reply['total'];   // 總件數
//-----------------------------------------------------------------

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
	$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
	$op['wi']['pic_url'] = $no_img;
}

//  取出  BOM  主料記錄 --------------------------------------------------------------
$op['bom_lots_NONE']= '';
$op['bom_lots_list'] = $apply->get_lots_det($op['wi']['id']);  //取出該筆 bom 內ALL主料記錄
$num_bom_lots = count($op['bom_lots_list']);
if (!$num_bom_lots){ $op['bom_lots_NONE'] = "1"; }

//  取出  BOM  副料記錄 --------------------------------------------------------------
$op['bom_acc_NONE']= '';
$op['bom_acc_list'] = $apply->get_acc_det($op['wi']['id']);  //取出該筆 bom 內ALL副料記錄
$num_bom_acc = count($op['bom_acc_list']);
if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1"; }

if(isset($PHP_sr_startno))
{
	$back_str="?PHP_action=apply_search_bom&PHP_sr=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
	$op['back_str']=$back_str;
}

$op['mat_id'] = $PHP_bom_id;
$op['mat_cate'] = $PHP_mat_cat;
$op['mat_code'] = $PHP_bom_code;


if(isset($PHP_pa_num))
{
	$op['pa_num'] = $PHP_pa_num;
	$ap_rec = $apply->get($PHP_pa_num);
	$op['ap_det'] = $ap_rec['ap_det'];
	$op['ap'] = $ap_rec['ap'];
}
// print_r($op);
page_display($op, '037', $TPL_APLY_SPC_VIEW_BOM);
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_ex_add":			job 51 PHP_vendor
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_ex_add":
check_authority('037',"add");
$wi_id = $PHP_wi_id;
if ($PHP_ap_num == '')
{

	if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

	$parm_madd= array(
		'cust'			=>	$ord['cust'],
		'dept'			=>	$ord['dept'],
		'sup_code'		=>	$PHP_vendor,
		'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		'ap_date'		=>	$TODAY,
		'ap_special'	=>	1,
	);
	
	$PHP_sup=$PHP_vendor;

	$head="PA".date('y')."-";	//A+日期+部門=請購單開頭
	$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//取得請購的最後編號
	$new_id = $apply->add($parm_madd);	//新增AP資料庫
	$PHP_ap_num=$parm_madd['ap_num'];
	//建po編號
	$tmp_num = substr($parm_madd['ap_num'],2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_ap_num);
} else {
	$po_num = "PO".substr($PHP_ap_num,2);
}


if( !isset($mat_cat) ){
	if ($PHP_item == 'lots') {
		$mat_cat='l';	
	} else {			
		$mat_cat='a';		
	}
}

$parm_dadd = array( 
	'ap_num'	=>	$PHP_ap_num,
	'bom_id'	=>	$PHP_bom_id,
	'eta'		=>	$PHP_eta,
	'qty'		=>	$PHP_qty,
	'unit'		=>	$PHP_unit,
	'mat_cat'	=>	$mat_cat,

	'order_num'	=>	$PHP_order_num,
	'wi_id'	    =>	$wi_id,
	'mat_id'	=>	$PHP_mat_id,
	'used_id'	=>	$PHP_used_id,
	'color'	    =>	$PHP_color,
	'size'	    =>	$PHP_size,
);
// print_r($parm_dadd);
// exit;
$f1 = $apply->add_det($parm_dadd);	
$log->log_add(0,"MODE",$GLOBALS['SCACHE']['ADMIN']['login_id'].'po.php?'.$PHP_action.',ap_num='.$PHP_ap_num.',bom_id='.$PHP_bom_id.',mat_id='.$PHP_mat_id.',mat_cat='.$mat_cat.',color='.$PHP_color);

$message = "Successfully appended PO : ".$po_num;
$op['msg'][] = $message;
$log->log_add(0,"037A",$message);

if( isset($wi_id) )
	$wi_rec['id'] = $wi_id;
else
	$wi_rec= $wi->get($PHP_wi_id);

$redir_str='po.php?PHP_action=apply_ex_bom_view&PHP_id='.$wi_rec['id'].'&PHP_bom_id='.$PHP_mat_id."&PHP_bom_code=".$PHP_mat_code."&PHP_mat_cat=".$mat_cat."&PHP_pa_num=".$PHP_ap_num; 	

redirect_page($redir_str);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_special_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_special_add":
		check_authority('037',"add");		
		if ($PHP_ap_num == '')
		{	
				if(!$ord = $wi->get(0,$PHP_wi_num)){    //取出該筆 製造令記錄 ID
							$op['msg']= $wi->msg->get(2);
							$layout->assign($op);
							$layout->display($TPL_ERROR);  		    
							break;
				}
			
				$parm_madd= array(  'cust'  			=>	$ord['cust'],
														'dept'				=>	$ord['dept'],
														'sup_code'		=>	$PHP_vendor,
														'ap_user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
														'ap_date'			=>	$TODAY,
														'ap_special'	=>	1,
														);
				$PHP_sup=$PHP_vendor;

			$head="PA".date('y')."-";	//A+日期+部門=請購單開頭
			$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//取得請購的最後編號
			$new_id = $apply->add($parm_madd);	//新增AP資料庫
			$PHP_ap_num=$parm_madd['ap_num'];
//建po編號
			$tmp_num = substr($parm_madd['ap_num'],2);
			$po_num = "PO".$tmp_num;
			$f1=$apply->update_fields('po_num',$po_num, $PHP_ap_num);
		}else{
			$po_num = "PO".substr($PHP_ap_num,2);;
		}
		
		if ($PHP_item == 'lots')
		{
			$mat_cat='l';	
//			$parm_mat = array (	'field_name'	=>	'vendor1',
//													'field_value'	=>	$PHP_sup,
//													'code'				=>	$PHP_mat_code
//												);
//			$lots->update_field_code($parm_mat);
		}else{			
			$mat_cat='a';		
//			$parm_mat = array (	'field_name'	=>	'vendor1',
//													'field_value'	=>	$PHP_sup,
//													'code'				=>	$PHP_mat_code
//												);
//			$acc->update_field_code($parm_mat);		
		}



		$parm_dadd = array( 'ap_num'		=>	$PHP_ap_num,
													'mat_id'	=>	$PHP_mat_id,
													'eta'			=>	$PHP_eta,
													'qty'			=>	$PHP_qty,
													'unit'		=>	$PHP_unit,
													'mat_cat'	=>	$mat_cat,
													);
		$f1 = $apply->add_det($parm_dadd);	
	
//		$op = $apply->get($PHP_ap_num);
		
		$message = "Successfully appended PO : ".$po_num;
		$op['msg'][] = $message;
		$log->log_add(0,"54A",$message);
		
		$wi_rec= $wi->get(0,$PHP_wi_num);
		$redir_str='po.php?PHP_action=apply_bom_view&PHP_id='.$wi_rec['id'].'&PHP_bom_id='.$PHP_mat_id."&PHP_bom_code=".$PHP_mat_code."&PHP_mat_cat=".$mat_cat."&PHP_pa_num=".$PHP_ap_num; 	

		redirect_page($redir_str);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_edit":
check_authority('037',"edit");

$where_str="AND( item like '%special%')";		
$op = $apply->get($PHP_aply_num,$where_str);
// print_r($_POST);
//組合畫面List
if (isset($op['ap_det']))
{
	$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
}
// print_r($op['ap_det2']);
$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");

for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
$op['back_str']=$PHP_back_str;
// print_r($_POST);
// print_r($op);
if($PHP_msg)
$op['msg'][] = $PHP_msg;
page_display($op, '037', $TPL_APLY_EDIT);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_add_det":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_add_det":
check_authority('037',"edit");

if( !$PHP_order_num || !$PHP_wi_id || !$PHP_mat_id || !$PHP_used_id ) {
    $redir_str='po.php?PHP_action=apply_edit&PHP_aply_num='.$PHP_ap_num.'&PHP_msg=Error ! Database can\'t access!';
    redirect_page($redir_str);
}

$wi_id = $PHP_wi_id;
if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

if ($PHP_item == 'lots')
{
	$parm_mat = array (	'field_name' => 'vendor1','field_value' => $PHP_vendor,'code' => $PHP_mat_code);
	$lots->update_field_code($parm_mat);	//加入主料供應商
	
	$where_str = "WHERE id = ". $PHP_bom_id;
	$mat=$bom->search('bom_lots', $where_str);
	$fd='bom_lots';	
	$mat_cat='l';
	
}else{

	$parm_mat = array (	
		'field_name'	=>	'vendor1',			//加入副料供應商
		'field_value'	=>	$PHP_vendor,
		'code'			=>	$PHP_mat_code
	);

	$acc->update_field_code($parm_mat);

	$where_str = "WHERE id = ".$PHP_bom_id;
	$mat=$bom->search('bom_acc', $where_str);		
	$fd='bom_acc';
	$mat_cat='a';				
}

for ($i=0; $i<sizeof($mat); $i++)
{
	$tmp_qty=explode(',',$mat[$i]['qty']);
	$qty=0;
	for ($j=0; $j<sizeof($tmp_qty); $j++)
	{
		$qty=$qty+$tmp_qty[$j];
	}
	$parm_dadd = array(
		'ap_num'	=>	$PHP_ap_num,
		'bom_id'	=>	$mat[$i]['id'],
		'eta'		=>	$PHP_eta,
		'qty'		=>	$qty,
		'unit'		=>	$PHP_unit,
		'mat_cat'	=>	$mat_cat,

		'order_num'	=>	$PHP_order_num,
		'wi_id'	    =>	$wi_id,
		'mat_id'	=>	$PHP_mat_id,
		'used_id'	=>	$PHP_used_id,
		'color'	    =>	$PHP_color,
		'size'	    =>	$PHP_size,
	);
	$f1 = $apply->add_det($parm_dadd);	//新增AP資料庫
    $log->log_add(0,"MODE",$GLOBALS['SCACHE']['ADMIN']['login_id'].'po.php?'.$PHP_action.',ap_num='.$PHP_ap_num.',bom_id='.$mat[$i]['id'].',mat_id='.$PHP_mat_id.',mat_cat='.$mat_cat.',color='.$PHP_color);
	
	$parm_mat = array($mat[$i]['id'],'ap_mark',$PHP_ap_num,$fd);
	$f2 = $bom->update_field($parm_mat);			
}
// print_r($parm_dadd);
$where_str="AND( item like '%special%')";		
$op = $apply->get($PHP_ap_num,$where_str);

//組合畫面List
if (isset($op['ap_det']))
{
	$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
}
	
/*	
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/
$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

$op['back_str']=$PHP_back_str;

page_display($op, '037', $TPL_APLY_EDIT);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "mat_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "mat_edit":
// check_authority('037',"edit");

$id = explode('|',$PHP_id_comp);
$s_total=0;
$tmp=0;

if ($PHP_special == 2){$PHP_table = 'ap_special';}else{$PHP_table = 'ap_det';}
for ($i=0; $i< sizeof($id); $i++)		//取得每項的請購量,並加總
{
	$where_str = "id = ".$id[$i];
	$row[$i]=$po->get_det_field('ap_qty',$PHP_table,$where_str);
	$s_total = $s_total + $row[$i]['ap_qty'];
}

for ($i=0; $i< (sizeof($id)-1); $i++)		//計算每項採購量(平分)
{
	$avg_qty[$i] = $PHP_qty * ($row[$i]['ap_qty'] / $s_total);
	$avg_qty[$i] = number_format($avg_qty[$i],0,'.','');
	$tmp = $tmp + $avg_qty[$i];
}

$tmp_qty = $PHP_qty - $tmp;
$avg_qty[$i] = $tmp_qty;
$avg_qty[$i] = number_format($avg_qty[$i],2,'.','');

for ($i=0; $i< sizeof($id); $i++)		//加入DB
{
	$f1=$apply->update_fields_id('ap_qty',$avg_qty[$i], $id[$i], $PHP_table);
	$f1=$apply->update_fields_id('eta',$PHP_eta, $id[$i], $PHP_table);
}

$i--;
$po_spare =$po->get_det_field('po_spare',$PHP_table,$where_str);
$spares = explode('|',$po_spare['po_spare']);

for($i=0; $i< sizeof($spares); $i++)	
{
	$f1=$apply->update_fields_id('po_qty',0, $spares[$i], $PHP_table);
	$f1=$apply->update_fields_id('po_unit','', $spares[$i], $PHP_table);
}

$tmp_num = substr($PHP_ap_num,2);
$po_num = "PO".$tmp_num;

$message = "Successfully add P/O detial : ".$po_num."record。";
$log->log_add(0,"51E",$message);		

$message = "Successfully Edit P/O : ".$po_num." record";
echo $message;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apply_edit":
check_authority('037',"edit");	
/*
$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
$apply->update_fields_id('dm_way',$PHP_dmway,$PHP_id);	//Update 幣別

$parm = array( 'field_name'		=>	'dm_way',
							 'field_value'	=>	$PHP_dmway,
							 'id'						=>	$PHP_sup_id
							 );
$supl->update_field($parm);

$parm_log = array(	'ap_num'	=>	$PHP_ap_num,
										'item'		=>	'special',
										'des'			=>	$PHP_des,
										'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'k_date'	=>	$TODAY,
						);
if ($PHP_des <> $PHP_old_des)
{
	$f1=$apply->add_log($parm_log);		
}

if (!$PHP_FACTORY)	//若無運送地(ID)時,記錄運送地詳細資料
{
$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//運送地址
	$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//運送地公司名
	$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//運送地傳真
	$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//運送地電話
	$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //運送地聯絡人
}  


$log_where = "AND item <> 'special'";	
$op = $apply->get($PHP_ap_num,$log_where);


if (isset($op['ap_det']))
{
	$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
}

$msg = "Successfully update P/A : ".$PHP_ap_num." main record";
$op['msg'][]=$msg;
//Ship TO
if ($op['ap']['arv_area'])
{
$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
}

//Payment
for ($i=0; $i< 4; $i++)
{
if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

//		$op['back_str']=$PHP_back_str;
*/
$f1=$apply->update_fields_id('special',$PHP_new_special,$PHP_id);	 //記錄申請日

	if (isset($PHP_SCH_num))
	{
		$back_str="?PHP_action=apply_search&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno."&SCH_del=".$SCH_del;				
	}else{
		$back_str="?PHP_action=apply_search&PHP_dept_code=&PHP_SCH_num&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=&SCH_del=";
	}
	$op['back_str']=$back_str;
if ($op['ap']['revise'] == 0 && $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

$redir_str = "po.php?PHP_action=po_revise&PHP_aply_num=".$PHP_ap_num."&PHP_revise=1&PHP_id=".$op['ap']['id']."&PHP_new_special=".$PHP_new_special;
redirect_page($redir_str);

if ($PHP_revise == 1)
{
	page_display($op, '037', $TPL_APLY_SHOW_REV);
}else{
	page_display($op, '037', $TPL_APLY_SHOW);			    	    
}		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "po":
		check_authority('037',"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		
		$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// 如果是業務部進入 則dept_code 指定該業務部---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			$manager_v = 1;
			//業務部門 select選單
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
//			$where_str = " WHERE dept = '".$dept_id."' ";			
		}

		$where_str=$where_str."order by cust_s_name"; //依cust_s_name排序
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //取出客戶簡稱
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}

		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
		$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 


		$op['manager_flag'] = $manager;
		$op['manager_v_flag'] = $manager_v;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);
		  	
		page_display($op, '037', $TPL_PO);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search_bom":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "po_search_bom":   //  先找出製造令.......

		check_authority('037',"view");
		
			if (!$op = $bom->search_pa()) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op 被清除了 需再判斷一次 
			for ($i=0; $i< sizeof($op['wi']); $i++)
			{
				$op['wi'][$i]['acc_ap'] = $po->chk_po_ok($op['wi'][$i]['id'], 'bom_acc','a');
				$op['wi'][$i]['lots_ap'] = $po->chk_po_ok($op['wi'][$i]['id'], 'bom_lots','l');
			}
			
			

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		$op['back_str']=$back_str;
		
		page_display($op, '037', $TPL_PO_BOM_LIST);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_bom_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_bom_view":

		check_authority('037',"view");
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
		//-------------------- wi_qty 數量檔 -------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // 總件數
		//-----------------------------------------------------------------

		// 相片的URL決定 ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}
					
		//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['bom_lots'] = $po->get_lots_det($op['wi']['id']);  //取出該筆 bom 內ALL主料記錄
		 $num_bom_lots = count($op['bom_lots']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  取出  BOM  副料記錄 --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc'] = $po->get_acc_det($op['wi']['id']);  //取出該筆 bom 內ALL副料記錄
		$num_bom_acc = count($op['bom_acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//  取出  BOM  加購記錄 --------------------------------------------------------------
		$op['ext_mat_NONE']= '';
		$op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
		$num_ext_mat = count($op['ext_mat']);
		if (!$num_ext_mat){	$op['ext_mat_NONE'] = "1";		}

		$back_str="?PHP_action=po_search_bom&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_sr_startno=".$PHP_sr_startno;
		$op['back_str']=$back_str;

			page_display($op, '037', $TPL_PO_BOM_VIEW);

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_add":
check_authority('037',"add");	

//echo $PHP_aply_num;
$op = $po->get($PHP_aply_num);

if (isset($op['ap_det']))
{
	$op['ap_det2'][0] = $op['ap_det'][0];
	$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
	$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
//		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
	$units = $op['ap_det'][0]['unit'];
	if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit']; }else{$prc_units = $op['ap_det'][0]['unit'];}

	$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
	$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');


	$k=1;
	for ($i=1; $i<sizeof($op['ap_det']); $i++)
	{
		$mk=0;	$order_add=0;
		$x=1;
		for ($j=0; $j< sizeof($op['ap_det2']); $j++)
		{
			if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
			{
				$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
				$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
				for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++)
				{
					if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num'])
					{
						$order_add =1;
						break;
					}
				}
				if ($order_add == 0)	$op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
				$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
				$x++;
				$mk = 1;
			}
		}
		
		if ($mk == 0)
		{
			$op['ap_det2'][$k] = $op['ap_det'][$i];
			$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
			$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
//				if ($op['ap_det'][$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
			$units = $op['ap_det'][$i]['unit'];
			if ($op['ap_det'][$i]['prc_unit']){$prc_units = $op['ap_det'][$i]['prc_unit']; }else{$prc_units = $op['ap_det'][$i]['unit'];}

			$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
			$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');

			$k++;
		}
	}
}
if (isset($op['ap_spec']))
{
	for ($i=0; $i<sizeof($op['ap_spec']); $i++)
	{
//				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
			$units = $op['ap_spec'][$i]['unit'];
			if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}

			$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
			$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');

	}
}
/*
if ($op['ap']['arv_area'])
{
	$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
	$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
	$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
	$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
	$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
}	
*/

$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
$op['FINL_select'] = $arry2->select($DIST,$op['ap']['finl_dist'],'PHP_finl_dist','select',"finl_show(this)");

for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}


$op['ap']['before'] = null;
$op['ap']['after'] = null;
$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","chk_dm_way()");
$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');


if (isset($PHP_wi_id))
{
	$back_str="?PHP_action=po_bom_view&PHP_id=".$PHP_wi_id."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;
	$op['add_back'] ="po_bom_view";
}
//	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

page_display($op, '037', $TPL_PO_ADD);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "chg_unit_ajax":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "chg_unit_ajax":   

		$qty=$PHP_qty;
		// $rtn_qty = change_unit_qty($PHP_ounit,$PHP_nunit,$qty);		
		$rtn_qty = change_unit_qty($PHP_ounit,$PHP_nunit,$qty,$PHP_weight);		
		if ($PHP_ounit == $PHP_nunit) $rtn_qty = $qty;
			if ($rtn_qty)
			{
				printf('%.2f',$rtn_qty);	
			}else{
				echo "no|".$PHP_ounit."|".$PHP_nunit."|".$qty."|".$rtn_qty;
			}
			
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "count_range_ajax":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "count_range_ajax":
$qty=$PHP_qty;
$id = explode('|',$PHP_det_id);
for ($i=0; $i< sizeof($id); $i++)
{
    $f1=$apply->update_fields_id('range',$PHP_range, $id[$i], $PHP_table);
    $f1=$apply->update_fields_id('po_unit',$PHP_unit, $id[$i], $PHP_table);
}	
$tmp_rag = $qty * ($PHP_range / 100);
$qty = $qty + $tmp_rag;

printf('%.2f',$qty);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pi_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "pi_add":

if ( $f1 = $po->update_field_num('pi_num',$_POST['pi'], $_POST['po_num']) ) {
    echo 'ok';
} else {
    echo 'error';
}

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_qty":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_po_qty":
	$id = explode('|',$PHP_det_id);
	$s_total=0;
	$tmp=0;
	$tmp_amt = 0;
	$rtn_price = change_unit_price($PHP_prc_unit,$PHP_unit,$PHP_price);

	$M_sizeof = sizeof($id);
	$rowM=$rows=array();
	for ($i=0; $i< $M_sizeof; $i++)		//取得每項的請購量,並加總
	{
		$where_str = "id = ".$id[$i];
		$row[$i]=$po->get_det_field('ap_qty',$PHP_table,$where_str);
		$s_total = $s_total + $row[$i]['ap_qty'];
		// 0=副料總採購量,1=個別購量,2=ID;
		$rowM[$i]=array($row[$i][0],$id[$i]);
		$rows[$i]=$row[$i][0];			
	}

	if($PHP_unit == 'K pcs')
	{
		$chg_qty = $PHP_qty * 1000;
		$div = 1000;
	}elseif($PHP_unit == '100pcs'){
		$chg_qty = $PHP_qty * 100;
		$div = 100;		
	}elseif($PHP_unit == 'dz'){
		$chg_qty = $PHP_qty * 12;
		$div = 12;		
	}elseif($PHP_unit == 'gross'){
		$chg_qty = $PHP_qty * 144;
		$div = 144;
	}else{
		$chg_qty = $PHP_qty;
		$div = 1;		
	}
	
	# 因為數量太小在最後計算會造成負數的產生，所以將數量排序由最小的數量開始計算
	asort($rows);

	$M=0;
	$MCt = $M_sizeof-1;
	foreach($rows as $kay => $val) { //計算每項採購量(平分)
		if( $M <> $MCt ) {
			$po_qty = $chg_qty * ( $rowM[$kay][0] / $s_total );
			$po_qty = number_format( $po_qty,0,'.','');
			$po_qty = number_format(( $po_qty / $div ),2,'.','');

			$po_amt = $po_qty * $rtn_price;
			$po_amt = number_format( $po_amt,2,'.','');
			
			$tmp += $po_qty;
			$tmp_amt += $po_amt;
		} else {
			$tmp_qty = $PHP_qty - $tmp;
			$po_qty = $tmp_qty;
			// PC 的不要有小數 Mode 暫時拿掉 2013/12/6 轉換PC遇到2筆訂單合併採購會把尾數去掉金額錯誤的問題
			// if ( $PHP_prc_unit == 'pc' )
				// $po_qty = number_format( $po_qty ,0,'.','');
			$po_qty = number_format( $po_qty ,2,'.','');
			$po_amt = $po_qty * $rtn_price ;
			$po_amt = number_format( $po_amt ,2,'.','');
			$tmp_amt += $po_amt;
		}

		$f1=$apply->update_fields_id('po_qty',$po_qty, $rowM[$kay][1], $PHP_table);
		$f1=$apply->update_fields_id('po_spare',$PHP_det_id, $rowM[$kay][1], $PHP_table);	
		$f1=$apply->update_fields_id('prics',$PHP_price, $rowM[$kay][1], $PHP_table);	
		$f1=$apply->update_fields_id('po_eta',$PHP_eta, $rowM[$kay][1], $PHP_table);	
		
		$f1=$apply->update_fields_id('prc_unit',$PHP_prc_unit, $rowM[$kay][1], $PHP_table);
		$f1=$apply->update_fields_id('amount',$po_amt, $rowM[$kay][1], $PHP_table);
		$f1=$apply->update_fields_id('base_qty',$PHP_base_qty, $rowM[$kay][1], $PHP_table);
		if( $PHP_base_qty > 0 )$f1=$apply->update_fields_id('ship_rmk','min purchase :'.$PHP_base_qty, $rowM[$kay][1], $PHP_table);
			else $f1=$apply->update_fields_id('ship_rmk','', $rowM[$kay][1], $PHP_table);

		$M++;
	}

	$parm = array (
	'field_name'	=>	'price1',
	'field_value'	=>	$PHP_price,
	'code'				=>	$PHP_mat_code
	);
	
	//計算總金額 rate 
	$pa_num = "PA".substr($PHP_po_num,2);						
	$where = "AND ap.ap_num = '".$PHP_ap_num."'";
	$total=$po->count_totoal_price($PHP_table,$where);		
	$f1=$apply->update_fields('po_total', $total, $PHP_ap_num);	//儲存總金額
	$f1=$apply->update_fields('po_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_ap_num); //記入申請人
	$f1=$apply->update_fields('po_date',$TODAY, $PHP_ap_num); //記錄申請日

	$po_num=str_replace("A", "O",$PHP_ap_num);
	$message = "Successfully add P/O Detial : ".$po_num."record。";
	$log->log_add(0,"037A",$message);
		
	if ($PHP_mat_cat =='l')$f1 = $lots->update_field_code($parm);	//上傳主料單價
	if ($PHP_mat_cat =='a')$f1 = $acc->update_field_code($parm);	//上傳副料單價
	$message = "Successfully add qty for P/O # :".$po_num."| PO Q'ty : ".$PHP_qty."&nbsp;".$PHP_unit."&nbsp;&nbsp;&nbsp; Range  : ".$PHP_range."% &nbsp;&nbsp;&nbsp; Price : @ $".$PHP_price."/".$PHP_prc_unit;
	$message = $message."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button size=30 class=select_e value='EDIT' onclick=\"po_edit_p2('$PHP_id')\";>|$PHP_eta|$tmp_amt|";
	if($PHP_base_qty > 0)$message.="Min. purchase :".$PHP_base_qty;
	echo $message;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_toler":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "add_po_toler":

$PHP_toler = $PHP_integers.'|'.$PHP_Negative;
$f1 = $apply->update_fields('toler', $PHP_toler, $PHP_po_num);	//儲存總金額		
$PHP_po_num = str_replace("A", "O",$PHP_po_num);

$message = "Successful modification  Tolerance for P/O # :".$PHP_po_num;
echo $message;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_toler":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_po_dear":

		$f1=$apply->update_fields('dear', $PHP_dear, $PHP_po_num);	//儲存總金額		

		$PHP_po_num=str_replace("A", "O",$PHP_po_num);
		
		$message = "Successful modification  Deal Amount for P/O # :".$PHP_po_num;
		echo $message;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_toler":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_oth_cost":

		$parm = array( "ap_num"	=>	$PHP_po_num,
									 "item"		=>	$PHP_item,
									 "cost"		=>	$PHP_cost
									 );
		$f1=$po->add_other_cost($parm);	//儲存總金額		

		$PHP_po_num=str_replace("A", "O",$PHP_po_num);
		
		$message = "Successful ADD others item for P/O # :".$PHP_po_num;
		$oth_show = "<b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Others&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Describe : ".$PHP_item." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Price : ".$PHP_cost."</b>";
		$oth_botton = "<input type='image' src='images/del.gif' height=20 onclick=\"del_cost('".$f1."','$PHP_cost','$PHP_po_num',this)\">";	

		header("Content-Type:text/html;charset=BIG5");
		echo $message."|".$oth_show."|".$oth_botton."|".$PHP_cost;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_toler":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "oth_cost_del":

		$f1=$po->del_cost($PHP_cost_id);	//儲存總金額		

//		$PHP_po_num=str_replace("A", "O",$PHP_po_num);
		
		$message = "Successful Delete others item for P/O # :".$PHP_po_num;
		echo $message;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_view":			job 037 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_view":
check_authority('037',"view");

// echo $PHP_aply_num;
$log_where = "AND item <> 'special'";	
$op = $po->get($PHP_aply_num,$log_where);
// print_r($op);
if (isset($op['ap_det']))
{
	$op['ap_det2']=$po->grout_ap($op['ap_det']);
}
	
/*
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/

if (isset($PHP_SCH_num))
{
	$back_str="?PHP_action=po_search&PHP_dept_code=".$PHP_dept_code."&SCH_del=".$SCH_del."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
} else if ( isset ($PHP_add_back) && $PHP_add_back ) {
	if (isset($op['ap_det2'][0]['ord_num']))
	{
		$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
	} else {
		$wi_id = $wi->get_field($op['ap_spec'][0]['ord_num'],'id');
	}
	$back_str="?PHP_action=po_bom_view&PHP_id=".$wi_id['id']."&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_sr_startno=";
} else {
	$back_str="?PHP_action=po_search&PHP_dept_code=&PHP_SCH_num=&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=&SCH_del=";
}


$op['apb_flag'] = $po->check_po_reopen($op['ap']['ap_num']);
$op['back_str']=$back_str;
$op['rmk_item'] = $arry2->select($PO_ITEM,'','PHP_item','select','');

$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++) {
	if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
}

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];


if (isset($PHP_message)) $op['msg'][]=$PHP_message;
if (!isset($PHP_rev)) $PHP_rev='';
if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

// print_r($op);


if ($PHP_rev == 1)
{
	page_display($op, '037', $TPL_PO_SHOW_REV);
}else{
	page_display($op, '037', $TPL_PO_SHOW);
}
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_logs_add":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apply_logs_add":
// check_authority('037',"view");

$po_num=str_replace("A", "O",$PHP_aply_num);
if(!$PHP_des)
{
	$message="Please Input Log Description On :".$po_num;	
	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
	redirect_page($redir_str);
}

if(!$PHP_item)
{
	$message="Please Input Log Item On :".$po_num;	
	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
	redirect_page($redir_str);		
}

if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

$parm= array(
'ap_num'		=>	$PHP_aply_num,
'item'			=>	$PHP_item,
'des'			=>	$PHP_des,
'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
'k_date'		=>	date('Y-m-d')
);

$f1=$apply->add_log($parm);

if ($f1)
{
	$po_num=str_replace("A", "O",$PHP_aply_num);
	$message="Successfully add log :".$po_num;
	$op['msg'][]=$message;
}else{
	$op['msg']= $apply->msg->get(2);
	$message = $op['msg'][0];
}
if( $PHP_po_type == 'extra' ) {
	$redir_str = "po.php?PHP_action=po_ex_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
} elseif ( $PHP_po_type == 'pp' ) {
	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
} else {
	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
}
redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_edit":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_edit":
		check_authority('037',"edit");	

		//echo $PHP_aply_num;
	$op = $po->get($PHP_aply_num);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
		$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
//		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
		$units = $op['ap_det'][0]['unit'];
		if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

		$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
		$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');

		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;	$order_add=0;
			$x=1;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++)
					{
						if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0)	$op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
					$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
					$x++;
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
				$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
//				if ($op['ap_det'][$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
				$units = $op['ap_det'][$i]['unit'];
				if ($op['ap_det'][$i]['prc_unit']){$prc_units = $op['ap_det'][$i]['prc_unit']; }else{$prc_units = $op['ap_det'][$i]['unit'];}
		
				$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
				$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');

				$k++;
			}
		}
	}
	if (isset($op['ap_spec']))
	{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
//				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
				$units = $op['ap_spec'][$i]['unit'];
				if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}

				$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
				$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');

		}
	}
/*
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/

		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
			$op['FINL_select'] = $arry2->select($DIST,$op['ap']['finl_dist'],'PHP_finl_dist','select',"finl_show(this)");

		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
		$op['ap']['special'] = $PHP_new_special;
		$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');

		$back_str="?PHP_action=po_search".$PHP_back_str;
		$op['back_str']=$back_str;
		$op['edit'] = 1;
//	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

		page_display($op, '037', $TPL_PO_ADD);			    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_revise":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_revise":
check_authority('037',"edit");

// $f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $PHP_id);
// $f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $PHP_id);
// $f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $PHP_id);
// $f1=$apply->update_fields('status',6, $PHP_aply_num);
// $f1=$apply->update_fields('po_rev',($PHP_rev_time+1), $PHP_aply_num);

$op = $po->get($PHP_aply_num);

if (isset($op['ap_det'])) {

	$op['ap_det2'][0] = $op['ap_det'][0];
	$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
    $op['ap_det2'][0]['s_etd'] = $op['ap_det2'][0]['o_etd'][0] = $order->get_partial_etd($op['ap_det'][0]['ord_num']);

	$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
    
	# if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
	$units = $op['ap_det'][0]['unit'];
	if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

	$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
	$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');
    
    # print_r($op['ap_det']);
    
	$k=1;
	for ($i=1; $i<sizeof($op['ap_det']); $i++) {
		$mk=0;
		$x=1;	$order_add=0;
		for ($j=0; $j< sizeof($op['ap_det2']); $j++) {
			if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] === $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta']) {
                
				$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
				$op['ap_det2'][$j]['amount'] = $op['ap_det'][$i]['amount'] +$op['ap_det2'][$j]['amount'];
				$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
				for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++) {
					if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num']) {
						$order_add =1;
						break;
					}
				}
				if ($order_add == 0){
                   
                    $op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
                    $op['ap_det2'][$j]['s_etd'] = $op['ap_det2'][$j]['o_etd'][] = $order->get_partial_etd($op['ap_det'][$i]['ord_num']);
                    
                }
				$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
				$x++;
				$mk = 1;
			}
		}
		
		if ($mk == 0) {

			$op['ap_det2'][$k] = $op['ap_det'][$i];
            
			$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
            $op['ap_det2'][$k]['s_etd'] = $op['ap_det2'][$k]['o_etd'][0] =  $order->get_partial_etd($op['ap_det'][$i]['ord_num']);
            
			$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
			// if ($op['ap_det'][$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
			$units = $op['ap_det'][$i]['unit'];
			if ($op['ap_det'][$i]['prc_unit']){$prc_units = $op['ap_det'][$i]['prc_unit']; }else{$prc_units = $op['ap_det'][$i]['unit'];}
			$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
			$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');
			$k++;

		}
	}
}

if (isset($op['ap_spec'])) {
	for ($i=0; $i<sizeof($op['ap_spec']); $i++) {
		// if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
		$units = $op['ap_spec'][$i]['unit'];
		if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}
		$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
		$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');
        $op['ap_spec'][$i]['s_etd'] = $op['ap_spec'][$i]['o_etd'] =  $order->get_partial_etd($op['ap_spec'][$i]['ord_num']);
	}
}

$tmp_num = substr($PHP_aply_num,2);
$po_num = "PO".$tmp_num;

// $f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);
// $f1=$apply->update_fields('status','6', $PHP_aply_num);

/*
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/

$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
$op['FINL_select'] = $arry2->select($DIST,$op['ap']['finl_dist'],'PHP_finl_dist','select',"finl_show(this)");

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$re_dm_way = re_dm_way($op['ap']['dm_way']);
if ( $re_dm_way[0] == 2 ){
    $op['ap']['dm_way'] = 'Input T/T of shipment separately the BEFORE & AFTER';
    $op['ap']['before'] = $re_dm_way['before'];
    $op['ap']['after'] = $re_dm_way['after'];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","chk_dm_way()");
$op['ap']['special'] = $PHP_new_special;
$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');

// $back_str="?PHP_action=po_search".$PHP_back_str;
// $op['back_str']=$back_str;
$op['revise'] = 1;
// if (isset($PHP_message)) $op['msg'][]=$PHP_message;

page_display($op, '037', $TPL_PO_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_search":
check_authority('037',"view");

if (!isset($SCH_del)) $SCH_del = 0;

if (!$op = $po->search(1, $PHP_dept_code)) {
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

for ($i=0; $i<sizeof($op['apply']); $i++){
    if ($op['apply'][$i]['special'] == 2){$table = 'ap_special';}else{$table = 'ap_det';}
    $where_str = "ap_num ='".$op['apply'][$i]['ap_num']."' GROUP BY ap_num";
    
    $row = $po->get_det_field('min(po_eta) as eta',$table,$where_str);
    $op['apply'][$i]['eta'] = $row['eta'];
    $op['apply'][$i]['statuss'] = get_po_status($op['apply'][$i]['status']);
}

$back_str="&SCH_del=".$SCH_del."&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
$op['back_str']=$back_str;

if (!$PHP_dept_code) {   // 如果 不是 呈辦業務部門進入時...
    $op['manager_flag'] = 1;
}

$op['dept_id'] = $PHP_dept_code;

$op['msg']= $po->msg->get(2);

page_display($op, '037', $TPL_PO_LIST);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_submit":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_submit":
check_authority('037',"edit");	

if(isset($PHP_CURRENCY))
{
    if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

    $apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
    
    # 修改 TT 付款
    if ( !empty($PHP_before) && !empty($PHP_after) ) {
        $PHP_dmway = $PHP_before .'|'. $PHP_after;
    }
    
    $apply->update_2fields('dm_way','finl_dist',$PHP_dmway,$PHP_finl_dist,$PHP_id);	//Update 幣別+final distinction

    $parm = array(
        'field_name'    =>	'dm_way',
        'field_value'	=>	$PHP_dmway,
        'id'            =>	$PHP_sup_id
    );
    
    $supl->update_field($parm);

    $parm_log = array(
        'ap_num'	=>	$PHP_aply_num,
        'item'		=>	'special',
        'des'       =>	$PHP_des,
        'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
        'k_date'	=>	$TODAY,
    );
    
    if ($PHP_des <> $PHP_old_des && $PHP_des <> 'no_des')
    {
        $f1=$apply->add_log($parm_log);		
    }

    /*
    if (!$PHP_FACTORY)	//若無運送地(ID)時,記錄運送地詳細資料 
    {
        $apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//運送地址
        $apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//運送地公司名
        $apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//運送地傳真
        $apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//運送地電話
        $apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //運送地聯絡人
    } 
    */

}

$f1=$apply->update_2fields('fob', 'fob_area', $PHP_fob, $PHP_fob_area, $PHP_id); // Price term

$tmp_num = substr($PHP_aply_num,2);
$po_num = "PO".$tmp_num;
$f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);
$f1=$apply->update_fields('status','6', $PHP_aply_num);
$PHP_po_num = $po_num;

if($PHP_exc_mk == 1)
{
    $redirect_str = "po.php?PHP_action=exc_overload&PHP_num=".$PHP_aply_num."&PHP_po_num=".$po_num;
    redirect_page($redirect_str);
}

$f1=$apply->update_2fields('status', 'po_sub_date', '8', $TODAY, $PHP_id);
$f1=$apply->update_fields('po_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);

if ($f1) {
    $message="Successfully submit PO :".$PHP_po_num;
    $op['msg'][]=$message;
    $log->log_add(0,"037E",$message);
}else{
    $op['msg']= $apply->msg->get(2);
    $message=$op['msg'][0];
}

$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_add_back=".$PHP_add_back;
redirect_page($redir_str);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_cfm_search":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_cfm_search":
check_authority('041',"view");	

if (!$op = $po->search_cfm()) {  
    $op['msg']= $po->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

$op['msg']= $po->msg->get(2);
for ($i=0; $i<sizeof($op['apply']); $i++) {
    if ($op['apply'][$i]['special'] == 2){$table = 'ap_special';}else{$table = 'ap_det';}
    $where_str = "ap_num ='".$op['apply'][$i]['ap_num']."' GROUP BY ap_num";
    
    $row = $po->get_det_field('min(po_eta) as eta',$table,$where_str);
    $op['apply'][$i]['eta'] = $row['eta'];
    $op['apply'][$i]['statuss'] = get_po_status($op['apply'][$i]['status']);
}

$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op, '041', $TPL_PO_CFM_LIST);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_cfm_view":			job 037 dm_way
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_cfm_view":
check_authority('041',"view");	

$log_where = "AND item <> 'special'";	
$op = $po->get($PHP_aply_num, $log_where);
if($op['ap']['special'] == '3')
	$op = $po->get_pp($PHP_aply_num, $log_where);
if (isset($op['ap_det']))
{
	$op['ap_det2']=$po->grout_ap($op['ap_det']);
}

$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++)
{
	if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
}
			
for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

/*
if ($op['ap']['arv_area'])
{
	$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
	$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
	$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
	$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
	$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
}
*/	
$back_str="&PHP_sr_startno=".$PHP_sr_startno;
$op['back_str']=$back_str;
if (isset($PHP_message)) $op['msg'][]=$PHP_message;
if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

page_display($op, '041', $TPL_PO_SHOW_CFM);			    	    
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_cfm":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_po_cfm":
	check_authority('041',"add");	
	$f1=$apply->update_2fields('status', 'po_cfm_date', '10', $TODAY, $PHP_id);
	$f1=$apply->update_fields('po_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
		if ($f1)
	{
		$message="Successfully CFM PO :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"037E",$message);
		$message="Successfully CFM PO ";
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	
	$print_str = $message."|<small>".$GLOBALS['SCACHE']['ADMIN']['login_id']." / ".$TODAY."</small>";
	echo $print_str;
//	$redir_str = "po.php?PHP_action=po_cfm_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
//	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_po_cfm":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_po_cfm":
	check_authority('041',"add");	
	
	if(strstr($PHP_detail,'&#'))	$PHP_detail = $ch_cov->check_cov($PHP_detail);
	
	$f1=$apply->update_2fields('status', 'au_date', '13', $TODAY, $PHP_id);
	$f1=$apply->update_fields('au_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'PO REJ-CFM',
								'des'				=>	$PHP_detail,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully reject PO :".$op['ap']['po_num'];
		$op['msg'][]=$message;
		$log->log_add(0,"037E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "po.php?PHP_action=po_cfm_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_apv_search":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_apv_search":
check_authority('042',"view");	

if (!$op = $po->search_apv()) {  
	$op['msg']= $po->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['msg']= $po->msg->get(2);
for ($i=0; $i<sizeof($op['apply']); $i++)
{
	if ($op['apply'][$i]['special'] == 2){$table = 'ap_special';}else{$table = 'ap_det';}
	$where_str = "ap_num ='".$op['apply'][$i]['ap_num']."' GROUP BY ap_num";
	
	$row = $po->get_det_field('min(po_eta) as eta',$table,$where_str);
	$op['apply'][$i]['eta'] = $row['eta'];
	$op['apply'][$i]['statuss'] = get_po_status($op['apply'][$i]['status']);
}

//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op, '042', $TPL_PO_APV_LIST);
break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_apv_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_apv_view":
check_authority('042',"edit");	

$log_where = "AND item <> 'special'";	
$op = $po->get($PHP_aply_num, $log_where);
if($op['ap']['special'] == '3')
	$op = $po->get_pp($PHP_aply_num, $log_where);
if (isset($op['ap_det']))
{
	$op['ap_det2']=$po->grout_ap($op['ap_det']);
}

$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++)
{
	if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
}

for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];
/*
if ($op['ap']['arv_area'])
{
	$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
	$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
	$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
	$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
	$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
}
*/	

$back_str="&PHP_sr_startno=".$PHP_sr_startno;
$op['back_str']=$back_str;
if (isset($PHP_message)) $op['msg'][]=$PHP_message;
if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

page_display($op, '042', $TPL_PO_SHOW_APV);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_apv":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_apv":
check_authority('042',"add");

$f1=$apply->update_2fields('status', 'po_apv_date', '12', $TODAY, $PHP_id);
$f1=$apply->update_fields('po_apv_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);

$mat_cat = '';
$op = $apply->get($PHP_aply_num);
if ($PHP_ap_special < 2){
    foreach($PHP_ord_num as $key => $value) {
        if ($op['ap_det'][0]['mat_cat'] == 'l') {
            $eta_date = $apply->get_fab_eta($value);
            $mat_cat = 'l';
        } else {
            $eta_date = $apply->get_acc_eta($value,'','',$PHP_aply_num);
            $mat_cat = 'a';
        }
    }
} else {
    foreach($PHP_ord_num as $key => $value) {
        if ($op['ap_spec'][0]['mat_cat'] == 'l'){
            $eta_date = $apply->get_fab_eta($value);
            $mat_cat = 'l';
        } else {
            $eta_date = $apply->get_acc_eta($value,'','acc_etd');
            $mat_cat = 'a';
        }
    }
}	

foreach($PHP_ord_num as $key => $value) {	
    if($wi_rec = $wi->get_field($value,'id')) {
        if($mat_cat == 'a') {
            $po->add_acc_cost($value,$wi_rec['id']);
        } else {
            $po->add_lots_cost($value,$wi_rec['id']);		
        }
        # RESET PO STATUS FOR PDTION
        $GLOBALS['order']->reset_pdtion($mat_cat,$wi_rec['id']);
    }
}

$det = $po->get_det_group($PHP_aply_num);
$rtn_str = '';
for($i=0; $i<sizeof($det); $i++) {
    if($det[$i]['base_qty'] > 0) {
        $des_rec .= "Material [".$det[$i]['mat_code']."], Color [".$det[$i]['mat_color']."], has min. purchase : [".$det[$i]['base_qty']." ".$det[$i]['po_unit']."]";
        $parm= array(	
            'ap_num'		=>	$PHP_aply_num,
            'item'			=>	'Document',
            'des'				=>	$des_rec,
            'user'			=>	'System msg.',
            'k_date'		=>	date('Y-m-d')
        );
        $apply->add_log($parm);
    }
}

if ($f1) {
    $message="Successfully APV PO :".$op['ap']['po_num'];
    $op['msg'][]=$message;
    $log->log_add(0,"042E",$message);
} else {
    $op['msg']= $apply->msg->get(2);
    $message=$op['msg'][0];
}

$redir_str = "po.php?PHP_action=po_apv_search&PHP_message=".$message;
redirect_page($redir_str);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_po_apv":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_po_apv":
	check_authority('042',"add");	
	
	if(strstr($PHP_detail,'&#'))	$PHP_detail = $ch_cov->check_cov($PHP_detail);
	
	$f1=$apply->update_2fields('status', 'au_date', '13', $TODAY, $PHP_id);
	$f1=$apply->update_fields('au_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'PO REJ-APV',
								'des'				=>	$PHP_detail,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully reject PO :".$op['ap']['po_num'];
		$op['msg'][]=$message;
//		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "po.php?PHP_action=po_apv_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;



/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_print":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_print":   //  .......
	check_authority('037',"add");	
	$where_str="AND (item like '%other%' OR item like '%special%')";		
	$op = $po->get($PHP_aply_num,$where_str);
	//取得user name
	$po_user=$user->get(0,$op['ap']['po_apv_user']);
	if ($po_user['name'])$op['ap']['po_apv_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['po_cfm_user']);
	if ($po_user['name'])$op['ap']['po_cfm_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['po_sub_user']);
	if ($po_user['name'])$op['ap']['po_sub_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['submit_user']);
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
	
	$where_str = " WHERE dept_code = '".$op['ap']['dept']."'";
	$dept_name=$dept->get_fields('dept_name',$where_str);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'] = $op['ap_det'][0]['ord_num'];
		if ($op['ap_det2'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_det2'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		
		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					if (!strstr($op['ap_det2'][$j]['orders'],$op['ap_det'][$i]['ord_num']))
							$op['ap_det2'][$j]['orders'] = $op['ap_det2'][$j]['orders']."|".$op['ap_det'][$i]['ord_num'];
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'] = $op['ap_det'][$i]['ord_num'];
				$k++;
			}
		}
	}else{
		if ($op['ap_spec'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_spec'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		

	}
	$ary=array('A','B','C','D','E','F','G','H','I');
	for ($i=0; $i<sizeof($ary); $i++)
	{
		if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
	}
				
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_po.php");

if ($op['ap']['special'] == 0) 
{
	$print_title="Regular Purchase Order";
}else{
	$print_title="Special Purchase Order";
}

$print_title2 = "VER.".($op['ap']['po_rev']+1)."   for   ".$mat_cat;
$creator = $op['ap']['ap_user'];
$mark = $op['ap']['po_num'];
$pdf=new PDF_po('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$parm = array (	'po_num'		=>	$op['ap']['po_num'],
								'supplier'	=>	$op['ap']['s_name'],
								'dept'			=>	$dept_name[0],
								'ap_date'		=>	$op['ap']['po_apv_date'],
								'ap_user'		=>	$op['ap']['ap_user'],
								'currency'	=>	$op['ap']['currency'],
								);
$pdf->hend_title($parm);
$pdf->ln();$pdf->ln();


	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,5,'SHIP TO : ',0,0,'L');
$pdf->cell(100,5,$op['ap']['ship_name'],0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(25,5,'RCVER ATTN : ',0,0,'L');
$pdf->cell(20,5,$op['ap']['ship_attn'],0,0,'L');
$pdf->Cell(23,5,'RCVER TEL : ',0,0,'L');
$pdf->cell(35,5,$op['ap']['ship_tel'],0,0,'L');
$pdf->Cell(23,5,'RCVER FAX : ',0,0,'L');
$pdf->cell(25,5,$op['ap']['ship_fax'],0,0,'L');
$pdf->ln();
$pdf->Cell(28,5,'RCVER ADDR : ',0,0,'L');
$pdf->cell(250,5,$op['ap']['ship_addr'],0,0,'L');
$pdf->ln();
$pdf->ln();
$pdf->po_title();
$pdf->ln();



$x=0;$total=0;
if (isset($op['ap_det2']))
{
	for ($i=0; $i< sizeof($op['ap_det2']); $i++)
	{		
		$op['ap_det2'][$i]['prics'] = NUMBER_FORMAT($op['ap_det2'][$i]['prics'],2);
		$op['ap_det2'][$i]['po_qty'] = NUMBER_FORMAT($op['ap_det2'][$i]['po_qty'],2);
		$op['ap_det2'][$i]['amount'] = NUMBER_FORMAT($op['ap_det2'][$i]['amount'],2);
		$ords= explode('|',$op['ap_det2'][$i]['orders']);		
		$total = $total + ($op['ap_det2'][$i]['prics']*$op['ap_det2'][$i]['po_qty']);
		$ord_cut = sizeof($ords);
		$x = $x+$ord_cut;
		if ($x > 17)
		{
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		
		if ($ord_cut > 1)
		{
			$pdf->po_det_mix($op['ap_det2'][$i],$ords);
		}else{
			$pdf->po_det($op['ap_det2'][$i]);
		}
		$pdf->ln();
		
	}
}

if (isset($op['ap_spec']))
{
	for ($i=0; $i< sizeof($op['ap_spec']); $i++)
	{
		$x++;
		$total = $total + ($op['ap_spec'][$i]['prics']*$op['ap_spec'][$i]['po_qty']);
		$op['ap_spec'][$i]['prics'] = NUMBER_FORMAT($op['ap_spec'][$i]['prics'],2);
		$op['ap_spec'][$i]['po_qty'] = NUMBER_FORMAT($op['ap_spec'][$i]['po_qty'],2);
		$op['ap_spec'][$i]['amount'] = NUMBER_FORMAT($op['ap_spec'][$i]['amount'],2);

		if ($x > 17)
		{
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		
		$pdf->po_det($op['ap_spec'][$i]);
		$pdf->ln();		
	}
}
$x=$x+2;
		if ($x > 17)
		{
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();				
			$x=0;
		}
$total = NUMBER_FORMAT($total,2);
$pdf->Cell(18,5,'Re-Mark','TRL',0,'C');
$pdf->Cell(242,5,'Total Cost : '.$op['ap']['currency'].' $'.$total,'T',0,'R');
$pdf->Cell(20,5,'','TR',0,'C');
$pdf->ln();
$pdf->Cell(18,5,'','RL',0,'C');
$pdf->Cell(242,5,'Delivery : '.$op['ap']['usance'].' days   Payment : '.$op['ap']['dm_way'],0,0,'L');
$pdf->Cell(20,5,'','R',0,'C');
$pdf->ln();
for ($i=0; $i< sizeof($op['apply_log']); $i++)
{
	$x++;
		if ($x > 16)
		{
			$pdf->Cell(18,5,'','RLB',0,'C');
			$pdf->Cell(242,5,'','B',0,'L');
			$pdf->Cell(20,5,'','BR',0,'C');
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();	
			$pdf->Cell(18,5,'Logs','TRL',0,'C');
			$pdf->Cell(242,5,'','T',0,'C');
			$pdf->Cell(20,5,'','TR',0,'C');
			$pdf->ln();	
			$x=0;
		}
	$pdf->Cell(18,5,'','RL',0,'C');
	$pdf->Cell(242,5,$op['apply_log'][$i]['des'],0,0,'L');
	$pdf->Cell(20,5,'','R',0,'C');
	$pdf->ln();
}
	$pdf->Cell(18,5,'','RLB',0,'C');
	$pdf->Cell(242,5,'','B',0,'L');
	$pdf->Cell(20,5,'','BR',0,'C');

	$pdf->ln();
	$pdf->SetFont('Big5','',10);
	$pdf->Cell(40,5,' ','0',0,'C');
	$pdf->Cell(40,5,'',0,0,'L');
	
//	$pdf->SetFont('Big5','B',10);
	$pdf->Cell(50,5,'APPROVAL : '.$op['ap']['po_apv_user'],'0',0,'C');	//PO Approval
	$pdf->Cell(50,5,'CONFIRM :'.$op['ap']['po_cfm_user'],0,0,'L');//PO Confirm
	$pdf->Cell(50,5,'PO :'.$op['ap']['po_sub_user'],0,0,'L');//PO Submit
	
	$pdf->Cell(50,5,'PA :'.$op['ap']['submit_user'].' ('.$op['ap']['submit_date'].')',0,0,'L');//PA submit
	

$name=$op['ap']['po_num'].'_po.pdf';
$pdf->Output($name,'D');
break;	
*/

//=======================================================
case "po_bom_status":
check_authority('022',"view");

//-------------------- 將 製造令 show out ------------------
//  wi 主檔
if (isset($PHP_id)) {
	if ( !$op['wi'] = $wi->get($PHP_id) ) { //取出該筆 製造令記錄
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
} else {
	if (!$op['wi']  = $wi->get(0,$PHP_bom_num)) {//取出該筆 製造令記錄
		$op['wi']['wi_num'] = $PHP_bom_num;
		if( $ap = $po->get_unadd_pp($PHP_bom_num) ) {
			$op['wi']['l_ap_num'] = $op['wi']['a_ap_num'] = '';
			foreach($ap as $key => $val) {
				if( $val['mat_cat'] == 'l' ){
					$op['wi']['l_ap_num'][] = $val['po_num'];
				} else {
					$op['wi']['a_ap_num'][] = $val['po_num'];
				}
			}
		}
		if ( !empty($op['wi']['l_ap_num']) ) $op['wi']['l_ap_num'] = array_unique($op['wi']['l_ap_num']);
		if ( !empty($op['wi']['a_ap_num']) ) $op['wi']['a_ap_num'] = array_unique($op['wi']['a_ap_num']);
		
		$layout->assign($op);
		$layout->display($TPL_BOM_PO_STATUS_VIEW);
		break;
	}
}

if ($op['wi']['bcfm_date'] == '0000-00-00 00:00:00') {    //取出該筆 製造令記錄
	if( $ap = $po->get_unadd_pp($PHP_bom_num) ) {
		$op['wi']['l_ap_num'] = $op['wi']['a_ap_num'] = '';
		foreach($ap as $key => $val) {
			if( $val['mat_cat'] == 'l' ) {
				$op['wi']['l_ap_num'][] = $val['po_num'];
			} else {
				$op['wi']['a_ap_num'][] = $val['po_num'];
			}
		}
	}
	if ( !empty($op['wi']['l_ap_num']) ) $op['wi']['l_ap_num'] = array_unique($op['wi']['l_ap_num']);
	if ( !empty($op['wi']['a_ap_num']) ) $op['wi']['a_ap_num'] = array_unique($op['wi']['a_ap_num']);
	
	$l_ap_num = array();
	$s_date = '0000-00-00';
	if ( $l = $po->get_po_status($op['wi']['id'],'l') ) {
		foreach($l as $key => $val){
			if ( !empty($val['ap_mark']) ) {
				$l_ap_num[] = $l[$key]['ap_mark'];
			}
			$s_date = $s_date > $val['k_date'] ? $s_date : $val['k_date'];
		}
		$l_ap_num = array_unique($l_ap_num);
		foreach($l_ap_num as $key) $op['po']['l_ap_num'][] = $key;
	} else {
		$op['po']['l_ap_num'] = '';
	}
	
	$a_ap_num = array();
	if ( $a = $po->get_po_status($op['wi']['id'],'a') ) {
		foreach($a as $key => $val){
			if ( !empty($val['ap_mark']) ) {
				$a_ap_num[] = $a[$key]['ap_mark'];
			}
			$s_date = $s_date > $val['k_date'] ? $s_date : $val['k_date'];
		}
		$a_ap_num = array_unique($a_ap_num);
		foreach($a_ap_num as $key) $op['po']['a_ap_num'][] = $key;
	} else {
		$op['po']['a_ap_num'] = '';
	}
	
	$op['w_date'] = countDays($op['wi']['last_update'],date('y-m-d'));
	$op['b_date'] = countDays($s_date,date('y-m-d'));
	$op['wi']['last_update'] = substr($op['wi']['last_update'],0,10);
	$op['wi']['cfm_date'] = substr($op['wi']['cfm_date'],0,10);
	$op['wi']['bcfm_date'] = substr($op['wi']['bcfm_date'],0,10);
	
	$op['s_date'] = $s_date;
	$layout->assign($op);
	$layout->display($TPL_BOM_PO_STATUS_VIEW);
	break;  
}

//  smpl 樣本檔
if(!$op['order'] = $order->get($op['wi']['smpl_id'])){
	$op['msg']= $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$size_data=$size_des->get($op['order']['size']);		
$op['order']['size']=$size_data['size_scale'];
//-----------------------------------------------------------------

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
	$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
	$op['wi']['pic_url'] = $no_img;
}
// echo '<img src="'.$op['wi']['pic_url'].'">';
$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);

//  取出  BOM  主料記錄 取出該筆 bom 內ALL主料記錄 --------------------------------------------------------------
$op['bom_lots'] = $po->get_lots_status($op['wi']['id']);

//  取出  BOM  副料記錄 取出該筆 bom 內ALL副料記錄 --------------------------------------------------------------
$op['bom_acc'] = $po->get_acc_status($op['wi']['id'],0,$op['wi']['wi_num'],$PHP_po_num);

//  取出  BOM  加購記錄 取出該筆 bom 內ALL加購記錄 --------------------------------------------------------------
// $op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);

//  取出  BOM  預先採購記錄 --------------------------------------------------------------
// $op['pp_mat_NONE']= '';
// $op['pp_mat'] = $po->get_unadd_pp($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
// $num_ext_mat = count($op['pp_mat']);
// if (!$num_ext_mat){	$op['pp_mat_NONE'] = "1";}

// if (isset($PHP_po_num)) {
	// for ($i =0; $i< sizeof($op['ext_mat']); $i++) {
		// if ($op['ext_mat'][$i]['po_num'] == $PHP_po_num) $op['ext_mat'][$i]['ln_mk'] = 1;
	// }
// }

//  取出  BOM  Disable計錄中己採購主料者 --------------------------------------------------------------
// $op['dis_lots_NONE']= '';
// $op['dis_lots'] = $bom->get_dis_lots($op['wi']['id']);  //取出該筆 bom 內記錄
// $num_lots = count($op['dis_lots']);
// if (!$num_lots){	$op['dis_lots_NONE'] = "1";}
// if (isset($PHP_po_num)) {
	// for ($i =0; $i< sizeof($op['dis_lots']); $i++) {
		// if ($op['dis_lots'][$i]['po_num'] == $PHP_po_num) $op['dis_lots'][$i]['ln_mk'] = 1;
	// }
// }

//  取出  BOM  Disable計錄中己採購副料者 --------------------------------------------------------------
// $op['dis_acc_NONE']= '';
// $op['dis_acc'] = $bom->get_dis_acc($op['wi']['id']);  //取出該筆 bom 內記錄
// $num_acc = count($op['dis_acc']);
// if (!$num_acc){	$op['dis_acc_NONE'] = "1";}
// if (isset($PHP_po_num)) {
	// for ($i =0; $i< sizeof($op['dis_acc']); $i++) {
		// if ($op['dis_acc'][$i]['po_num'] == $PHP_po_num) $op['dis_acc'][$i]['ln_mk'] = 1;
	// }
// }

$op['exc_rec'] = $except->get_po_exc($op['wi']['wi_num']);

page_display($op, '065', "bom_po_status.html");
break;



//=======================================================
case "po_bom_det":
check_authority('022',"view");

//-------------------- 將 製造令 show out ------------------
//  wi 主檔
if (isset($PHP_id)) {
	if ( !$op['wi'] = $wi->get($PHP_id) ) { //取出該筆 製造令記錄
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
} else {
	if (!$op['wi']  = $wi->get(0,$PHP_bom_num)) {//取出該筆 製造令記錄
		$op['wi']['wi_num'] = $PHP_bom_num;
		if( $ap = $po->get_unadd_pp($PHP_bom_num) ) {
			$op['wi']['l_ap_num'] = $op['wi']['a_ap_num'] = '';
			foreach($ap as $key => $val) {
				if( $val['mat_cat'] == 'l' ){
					$op['wi']['l_ap_num'][] = $val['po_num'];
				} else {
					$op['wi']['a_ap_num'][] = $val['po_num'];
				}
			}
		}
		if ( !empty($op['wi']['l_ap_num']) ) $op['wi']['l_ap_num'] = array_unique($op['wi']['l_ap_num']);
		if ( !empty($op['wi']['a_ap_num']) ) $op['wi']['a_ap_num'] = array_unique($op['wi']['a_ap_num']);
		
		$layout->assign($op);
		$layout->display($TPL_BOM_PO_STATUS_VIEW);
		break;
	}
}

if ($op['wi']['bcfm_date'] == '0000-00-00 00:00:00') {    //取出該筆 製造令記錄
	if( $ap = $po->get_unadd_pp($PHP_bom_num) ) {
		$op['wi']['l_ap_num'] = $op['wi']['a_ap_num'] = '';
		foreach($ap as $key => $val) {
			if( $val['mat_cat'] == 'l' ) {
				$op['wi']['l_ap_num'][] = $val['po_num'];
			} else {
				$op['wi']['a_ap_num'][] = $val['po_num'];
			}
		}
	}
	if ( !empty($op['wi']['l_ap_num']) ) $op['wi']['l_ap_num'] = array_unique($op['wi']['l_ap_num']);
	if ( !empty($op['wi']['a_ap_num']) ) $op['wi']['a_ap_num'] = array_unique($op['wi']['a_ap_num']);
	
	$l_ap_num = array();
	$s_date = '0000-00-00';
	if ( $l = $po->get_po_status($op['wi']['id'],'l') ) {
		foreach($l as $key => $val){
			if ( !empty($val['ap_mark']) ) {
				$l_ap_num[] = $l[$key]['ap_mark'];
			}
			$s_date = $s_date > $val['k_date'] ? $s_date : $val['k_date'];
		}
		$l_ap_num = array_unique($l_ap_num);
		foreach($l_ap_num as $key) $op['po']['l_ap_num'][] = $key;
	} else {
		$op['po']['l_ap_num'] = '';
	}
	
	$a_ap_num = array();
	if ( $a = $po->get_po_status($op['wi']['id'],'a') ) {
		foreach($a as $key => $val){
			if ( !empty($val['ap_mark']) ) {
				$a_ap_num[] = $a[$key]['ap_mark'];
			}
			$s_date = $s_date > $val['k_date'] ? $s_date : $val['k_date'];
		}
		$a_ap_num = array_unique($a_ap_num);
		foreach($a_ap_num as $key) $op['po']['a_ap_num'][] = $key;
	} else {
		$op['po']['a_ap_num'] = '';
	}
	
	$op['w_date'] = countDays($op['wi']['last_update'],date('y-m-d'));
	$op['b_date'] = countDays($s_date,date('y-m-d'));
	$op['wi']['last_update'] = substr($op['wi']['last_update'],0,10);
	$op['wi']['cfm_date'] = substr($op['wi']['cfm_date'],0,10);
	$op['wi']['bcfm_date'] = substr($op['wi']['bcfm_date'],0,10);
	
	$op['s_date'] = $s_date;
	$layout->assign($op);
	$layout->display($TPL_BOM_PO_STATUS_VIEW);
	break;  
}

//  smpl 樣本檔
if(!$op['order'] = $order->get($op['wi']['smpl_id'])){
	$op['msg']= $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$size_data=$size_des->get($op['order']['size']);		
$op['order']['size']=$size_data['size_scale'];
//-----------------------------------------------------------------

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
	$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
	$op['wi']['pic_url'] = $no_img;
}
// echo '<img src="'.$op['wi']['pic_url'].'">';
$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);

//  取出  BOM  主料記錄 取出該筆 bom 內ALL主料記錄 --------------------------------------------------------------
$op['bom_lots'] = $po->get_lots_det($op['wi']['id']);
// print_r($op['bom_lots']['bom']);
//  取出  BOM  副料記錄 取出該筆 bom 內ALL副料記錄 --------------------------------------------------------------
$op['bom_acc'] = $po->get_acc_det($op['wi']['id']);

//  取出  BOM  加購記錄 取出該筆 bom 內ALL加購記錄 --------------------------------------------------------------
$op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);

//  取出  BOM  預先採購記錄 --------------------------------------------------------------
$op['pp_mat_NONE']= '';
$op['pp_mat'] = $po->get_unadd_pp($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
$num_ext_mat = count($op['pp_mat']);
if (!$num_ext_mat){	$op['pp_mat_NONE'] = "1";}

if (isset($PHP_po_num)) {
	for ($i =0; $i< sizeof($op['bom_lots']['bom']); $i++) {
		if ($op['bom_lots']['bom'][$i]['po_num'] == $PHP_po_num) $op['bom_lots']['bom'][$i]['ln_mk'] = 1;
	}
	for ($i =0; $i< sizeof($op['bom_acc']['bom']); $i++) {
		if ($op['bom_acc']['bom'][$i]['po_num'] == $PHP_po_num) $op['bom_acc']['bom'][$i]['ln_mk'] = 1;
	}
	for ($i =0; $i< sizeof($op['ext_mat']); $i++) {
		if ($op['ext_mat'][$i]['po_num'] == $PHP_po_num) $op['ext_mat'][$i]['ln_mk'] = 1;
	}
}

//  取出  BOM  Disable計錄中己採購主料者 --------------------------------------------------------------
$op['dis_lots_NONE']= '';
$op['dis_lots'] = $bom->get_dis_lots($op['wi']['id']);  //取出該筆 bom 內記錄
$num_lots = count($op['dis_lots']);
if (!$num_lots){	$op['dis_lots_NONE'] = "1";}
if (isset($PHP_po_num)) {
	for ($i =0; $i< sizeof($op['dis_lots']); $i++) {
		if ($op['dis_lots'][$i]['po_num'] == $PHP_po_num) $op['dis_lots'][$i]['ln_mk'] = 1;
	}
}

//  取出  BOM  Disable計錄中己採購副料者 --------------------------------------------------------------
$op['dis_acc_NONE']= '';
$op['dis_acc'] = $bom->get_dis_acc($op['wi']['id']);  //取出該筆 bom 內記錄
$num_acc = count($op['dis_acc']);
if (!$num_acc){	$op['dis_acc_NONE'] = "1";}
if (isset($PHP_po_num)) {
	for ($i =0; $i< sizeof($op['dis_acc']); $i++) {
		if ($op['dis_acc'][$i]['po_num'] == $PHP_po_num) $op['dis_acc'][$i]['ln_mk'] = 1;
	}
}

$op['exc_rec'] = $except->get_po_exc($op['wi']['wi_num']);

page_display($op, '065', $TPL_BOM_PO_VIEW);
break;



//-------------------------------------------------------------------------


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_print_doc":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_print_doc":   //  .......
	check_authority('037',"view");	
	$where_str="AND (item like '%Packing%' OR item like '%Remark%')";		
	$op = $po->get($PHP_aply_num,$where_str);
	//取得user name
	
	if (isset($op['ap_det']))
	{
		
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'] = $op['ap_det'][0]['ord_num'];
		if ($op['ap_det2'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_det2'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		
		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] === $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					$op['ap_det2'][$j]['amount'] = $op['ap_det'][$i]['amount'] +$op['ap_det2'][$j]['amount'];

					if (!strstr($op['ap_det2'][$j]['orders'],$op['ap_det'][$i]['ord_num']))
							$op['ap_det2'][$j]['orders'] = $op['ap_det2'][$j]['orders']."|".$op['ap_det'][$i]['ord_num'];
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'] = $op['ap_det'][$i]['ord_num'];
				$k++;
			}
		}
	}else{
		if ($op['ap_spec'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_spec'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		

	}
	$ary=array('A','B','C','D','E','F','G','H','I');
	for ($i=0; $i<sizeof($ary); $i++)
	{
		if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
	}
				
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}

	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

	if( $op['ap']['revise'] < 10)	 $op['ap']['revise'] = "00".$op['ap']['revise'];
	if( $op['ap']['revise'] > 10 && $op['ap']['revise'] < 100)	 $op['ap']['revise'] = "0".$op['ap']['revise'];
	
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_po_doc.php");

$print_title2="PURCHASE ORDER";

$creator = $op['ap']['ap_user'];
$mark = $op['ap']['po_num'];

$open_dept = $op['ap']['open_dept'];

$pdf=new PDF_po_doc('P','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
//$Y = $pdf->GetY();
//$pdf->SetXY(10,$Y+10);
$pdf->cell(20, 5,"P.O. NO.",0,0,'L');
$pdf->cell(145, 5,$op['ap']['po_num'],0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->cell(40, 5,"Ver      ".$op['ap']['revise'],0,0,'L');
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->cell(20, 5,"P.I. NO.",0,0,'L');
$pdf->cell(145, 5,$op['ap']['pi_num'],0,0,'L');
$pdf->ln();

$pdf->SetFont('Arial','',10);
$pdf->cell(165, 5,'',0,0,'L');
$pdf->cell(40, 5,"Date    ".$op['ap']['po_apv_date'],0,0,'L');
$pdf->ln();
$pdf->supl_show($op['ap']);
$pdf->ln();
$pdf->SetFont('Arial','',10);
$pdf->cell(205, 5,'We Would like to reconfirm with you the following qty for bulk use..',0,0,'L');
$pdf->ln();
/*
$pdf->SetFont('Arial','B',10);
$pdf->cell(20, 5,'SHELL',0,0,'L');
$pdf->cell(100, 5,'',0,0,'L');
$pdf->cell(80, 5,'FOB Shan Tao by BOA',0,0,'L');
$pdf->ln();
*/
$x=1;
$pdf->SetFont('Arial','',9);
$pdf->mater_title();
$pdf->ln();

$pri_tal = 0;
$qty_tal = 0;
$eta = '0000-00-00';
//$toler = 0;
/*
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/	
$mk_order = $op['ap']['po_num'].";";
if (isset($op['ap_det2']))
{
	for ($i=0; $i< sizeof($op['ap_det2']); $i++)
	{
		$pri_tal = $pri_tal + ($op['ap_det2'][$i]['prics']*$op['ap_det2'][$i]['po_qty']);
		$qty_tal = $qty_tal + $op['ap_det2'][$i]['po_qty'];
		if ($eta < $op['ap_det2'][$i]['po_eta']) $eta = $op['ap_det2'][$i]['po_eta'];
	//	if ($toler < $op['ap_det2'][$i]['range']) $toler = $op['ap_det2'][$i]['range'];
		if ($x > 30)
		{
			$pdf->AddPage();
			$pdf->mater_title();
			$pdf->ln();
			$x=0;
		}	
		$bk_ll = $pdf->mater($op['ap_det2'][$i],$op['ap']['currency'],$op['ap']['fob'],$op['ap']['fob_area']);
		$x=$x+$bk_ll;
	}
}
if (isset($op['ap_det']))
{
	for ($i=0; $i< sizeof($op['ap_det']); $i++)
	{
		if (!strstr($mk_order,$op['ap_det'][$i]['ord_num']))
		{
			$mk_order = $mk_order.$op['ap_det'][$i]['ord_num'].",";		
		}
	}
}
if (isset($op['ap_spec']))
{
	for ($i=0; $i< sizeof($op['ap_spec']); $i++)
	{		
		$mk_order = $op['ap_spec'][0]['ord_num'].",";			
		$pri_tal = $pri_tal + ($op['ap_spec'][$i]['prics']*$op['ap_spec'][$i]['po_qty']);
		$qty_tal = $qty_tal + $op['ap_spec'][$i]['po_qty'];
		if ($eta < $op['ap_spec'][$i]['po_eta']) $eta = $op['ap_spec'][$i]['po_eta'];
	//	if ($toler < $op['ap_spec'][$i]['range']) $toler = $op['ap_spec'][$i]['range'];
		if ($x > 30)
		{
			$pdf->AddPage();
			$pdf->mater_title();
			$pdf->ln();
			$x=0;		
		}			
		$bk_ll = $pdf->mater($op['ap_spec'][$i],$op['ap']['currency'],$op['ap']['fob'],$op['ap']['fob_area']);	
		$x=$x+$bk_ll;
	}
}
$mk_order = substr($mk_order,0,-1);
//echo $mk_order;
//Total
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}

	for ($i=0; $i< sizeof($op['ap_oth_cost']); $i++)
	{		
		if ($x > 30)
		{
			$pdf->AddPage();
			$pdf->mater_title();
			$pdf->ln();
			$x=0;		
		}			
		$bk_ll = $pdf->oth_cost($op['ap_oth_cost'][$i],$op['ap']['currency']);	
		$x++;
	}


$pri_tal = NUMBER_FORMAT($pri_tal,2);
$qty_tal = NUMBER_FORMAT($qty_tal,2);
$op['ap']['dear'] = NUMBER_FORMAT($op['ap']['dear'],2);
$op['ap']['po_total'] = NUMBER_FORMAT($op['ap']['po_total'],2);
$pdf->cell(130,5,'TOTAL',1,0,'R');
$pdf->cell(19,5,$qty_tal,1,0,'R');
$pdf->cell(46,5,$op['ap']['currency']."$ ".$op['ap']['po_total'],1,0,'R');
$pdf->ln();
$x++;
//支付總金額
$pdf->cell(130,5,'Deal Amount',1,0,'R');
$pdf->cell(19,5,'',1,0,'R');
$pdf->cell(46,5,$op['ap']['currency']."$ ".$op['ap']['dear'],1,0,'R');
$pdf->ln();

//Delivery
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"Delivery    : ",0,0,'L');
$pdf->cell(145, 5,$eta,0,0,'L');
$pdf->ln();

//Less(量的增減範圍)
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"Tolerance : ",0,0,'L');

$toler = "0 %";
if ($op['ap']['toler'] == '0|0') {
	$toler = "0 %";
} else {
	$toler = " + ".$op['ap']['toleri']." % ";
	if ( $op['ap']['tolern'] != '0' ){ 
		$toler .= "~ - ".$op['ap']['tolern']." % ";
	}
}

$pdf->cell(145, 5,$toler." in quantity allowable",0,0,'L');
$pdf->ln();

//Payment(付款方式)
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"Payment   : ",0,0,'L');
$pdf->SetFont('BIG5','',10);
$pdf->cell(145, 5,$op['ap']['dm_way'],0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->ln();

//Bill To(帳單送達地)
$x=$x+3;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"BILL TO    : ",0,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->cell(145, 5,'CARNIVAL INDUSTRIAL CORPORATION',0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,'7TH FL., NO.25, JEN-AI RD., SEC.4, TAIPEI, TAIWAN.',0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,'TEL:8862-27113171',0,0,'L');
$pdf->ln();

//SHIP To(貨物送達地)
$x=$x+3;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"SHIP TO   : ",0,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->cell(145, 5,$op['ap']['ship_name'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(165, 5,$op['ap']['ship_addr'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,"TEL:".$op['ap']['ship_tel'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,"FAX:".$op['ap']['ship_fax'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,"Attn:".$op['ap']['ship_attn'],0,0,'L');
$pdf->ln();

//Final Distinctint(貨物送達地)
if($op['ap']['finl_dist'])
{
	$x=$x+3;
	if ($x > 30)
	{
		$pdf->AddPage();
		$x=0;		
	}
	$pdf->SetFont('Arial','',10);
	$pdf->cell(20, 5,"Final Dist. : ",0,0,'L');
	$pdf->SetFont('Arial','B',10);
	$pdf->cell(145, 5,$op['ap']['finl_name'],0,0,'L');
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->cell(20, 5," ",0,0,'L');
	$pdf->cell(165, 5,$op['ap']['finl_addr'],0,0,'L');
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->cell(20, 5," ",0,0,'L');
	$pdf->cell(145, 5,"TEL:".$op['ap']['finl_tel'],0,0,'L');
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->cell(20, 5," ",0,0,'L');
	$pdf->cell(145, 5,"FAX:".$op['ap']['finl_fax'],0,0,'L');
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->cell(20, 5," ",0,0,'L');
	$pdf->cell(145, 5,"Attn:".$op['ap']['finl_attn'],0,0,'L');
	$pdf->ln();
}
//Packing
$ln=$pdf->mk_show($op['apply_log'],'Packing','',$x);

$x=$ln;//原本算筆數用
$x=$pdf->GetY();
if ($x > 240) //原本是$x>25
{
	$pdf->AddPage();
	$x=0;		
}

//Remark
$ln=$pdf->mk_show($op['apply_log'],'Remark',$mk_order,$x);
$x=$ln;//原本算筆數用
$x=$pdf->GetY();
if ($x > 240)//原本是$x>25
{
	$pdf->AddPage();
	$x=0;		
}

/*
//附加條款 2009-08-15
$yy = $pdf->GetY();
$yy+=3;
$pdf->GetY($yy);


$pdf->SetFont('Arial','',7);
$pdf->cell(190, 3,'For terms and conditions please refer to http://scm-b.carnival.com.tw/po/tc0001clhk.pdf',0,1,'L');
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->cell(190, 3,'*Vendor has the full & sole responsibility to check and provide correct category for Customs Clearance.',0,1,'L');
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->cell(190, 3,'**Time is of the essence of this Order. The Vendor shall be liable for all loss or damage suffered by the Purchaser due to the delay in delivery Without prejudice to the rights and',0,1,'L');
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->cell(190, 3,'  remedies of the Purchaser, in the event of delay, either the Vendor shall pay for all airfreight and other necessary charges for delivering the Merchandise by Air to the Purchaser',0,1,'L');
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}

$pdf->cell(190, 3,'  (or its nominated consignees) to expedite the shipment or the Purchaser shall be entitled to cancel the whole or any part of the order.',0,1,'L');
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
*/


$pdf->SetY(240);
$pdf->SetFont('Arial','',7);
$pdf->SetTextColor(95,95,95);
$pdf->cell(190, 3,'For terms and conditions please refer to http://scm-b.carnival.com.tw/vendor/poterm001.pdf',0,1,'L');
$pdf->cell(190, 3,'*Vendor has the full & sole responsibility to check and provide correct category for Customs Clearance.',0,1,'L');
$pdf->cell(190, 3,'**Time is of the essence of this Order. The Vendor shall be liable for all loss or damage suffered by the Purchaser due to the delay in delivery Without prejudice to the rights and',0,1,'L');
$pdf->cell(190, 3,'  remedies of the Purchaser, in the event of delay, either the Vendor shall pay for all airfreight and other necessary charges for delivering the Merchandise by Air to the Purchaser',0,1,'L');
$pdf->cell(190, 3,'  (or its nominated consignees) to expedite the shipment or the Purchaser shall be entitled to cancel the whole or any part of the order.',0,1,'L');


//主管簽名
$pdf->SetY(255);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',10);
$pdf->cell(70, 5,'Confirmed by',0,0,'L');
$pdf->cell(120, 5,'',0,0,'L');
$pdf->ln();
$pdf->SetFont('BIG5','B',10);
$pdf->cell(70, 5,'Carnival Industrial Corporation',0,0,'L');
$pdf->cell(30, 5,'',0,0,'L');
$pdf->cell(90, 5,$op['ap']['supl_f_name'],0,0,'L');
$pdf->ln();
$pdf->ln();
$pdf->cell(70, 5,'','B',0,'L');
$pdf->cell(30, 5,'',0,0,'L');
$pdf->cell(90, 5,'','B',0,'L');
$name=$op['ap']['po_num'].'_po_doc.pdf';
$pdf->Output($name,'D');
break;	





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_rcv_view":
check_authority('037',"view");

$log_where = "AND item <> 'special'";	
$op = $po->get($PHP_aply_num,$log_where);
if (!$op['ap']) {
		$op['msg'][] = "無法查詢該PO或PO己被刪除";
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
}	
	
if (isset($op['ap_det'])) {

	$op['ap_det2']=$po->grout_ap($op['ap_det']);
	
	for ($i=0; $i<sizeof($op['ap_det2']); $i++)
	{
		if($op['ap_det2'][$i]['mat_code'] == $PHP_mat_code) $op['ap_det2'][$i]['ln_mk'] ='1';
	}

} else {

	for ($i=0; $i<sizeof($op['ap_spec']); $i++)
	{
		if($op['ap_spec'][$i]['mat_code'] == $PHP_mat_code) $op['ap_spec'][$i]['ln_mk'] ='1';
	}

}

$op['rmk_item'] = $arry2->select($PO_ITEM,'','PHP_item','select','');

$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++) {
	if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
}

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

	
if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
if ( !empty($goback) ) $op['goback'] = $goback;
$op['resize'] = 1;

// print_r($op);

$layout->assign($op);
$layout->display($TPL_PO_SHOW);		    	    

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_search_pp":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_search_pp":   //  先找出製造令.......

		check_authority('037',"view");

			if (!$op = $order->search_apvd_ord(2)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'SA') 
		{
			$op['close_flag'] = 1;
		}
		
		$op['back_str']=$back_str;
		page_display($op, '037', $TPL_APLY_PP_LIST);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_pp_add":			job 51 ap_det
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_pp_add":
check_authority('037',"add");

# 取出該筆 製造令記錄 ID
if(!$op = $order->get(0,$PHP_ord_num)){
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

if(isset($PHP_sr_startno))
{
	$back_str="?PHP_action=apply_search_bom&PHP_sr=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
	$op['back_str']=$back_str;
}

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['order_num'].".jpg")){
	$op['pic_url'] = $style_dir.$op['order_num'].".jpg";
} else {
	$op['pic_url'] = $no_img;
}

if( $PHP_item == 'lots' || $PHP_item == 'l' ) {
	// 取出  BOM  主料記錄 --------------------------------------------------------------
	$op['bom_lots_list'] = $po->get_lots_use($op['order_num']);
} else {
	// 取出  BOM  副料記錄 --------------------------------------------------------------
	$op['bom_acc_list'] = $po->get_acc_use($op['order_num']);
}
// print_r($op['bom_lots_list']);
$op['mat_cate'] = $PHP_item;
$op['item'] = $PHP_item;

# 原始供應商
if(empty($ap_supl))$ap_supl = $PHP_vendor;
$op['ap_supl'] = $ap_supl;

$op['ap_num'] = $PHP_pa_num;
if(isset($PHP_pa_num))
{
	$op['pa_num'] = $PHP_pa_num;
	$op['po_num'] = 'PO'.substr($PHP_pa_num,2);
	$ap_rec = $po->get_pp_mat($PHP_pa_num);
	$op['ap_det'] = $ap_rec['ap_det'];
	// $op['ap'] = $ap_rec['ap'];
	// print_r($op['ap_det']);
}


$parm_madd= array(
	'cust'  		=>	$ord['cust'],
	'dept'			=>	$ord['dept'],
	'sup_code'		=>	'',
	'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'ap_date'		=>	$TODAY,
	'ap_special'	=>	3,
);

$op['ap'] = $parm_madd;
$op['wi_num'] = $PHP_ord_num;
// print_r($op);
page_display($op, '037', $TPL_APLY_PP_ADD);			    	    
break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_apply_pp_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apply_pp_add":
check_authority('037',"add");	


if ($PHP_ap_num == '')
{
// print_r($_GET);
	// if(!$ord = $wi->get(0,$PHP_wi_num)){    //取出該筆 製造令記錄 ID
		// $op['msg']= $wi->msg->get(2);
		// $layout->assign($op);
		// $layout->display($TPL_ERROR);  		    
		// break;
	// }

	$parm_madd= array(
		'cust'			=>	$ap_cust,
		'dept'			=>	$ap_dept,
		'sup_code'		=>	$PHP_vendor,
		'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		'ap_date'		=>	$TODAY,
		'ap_special'	=>	3,
	);
	
	$ap_supl = $PHP_vendor;

	$head="PA".date('y')."-";	//A+日期+部門=請購單開頭
	$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//取得請購的最後編號
	$new_id = $apply->add($parm_madd);	//新增AP資料庫
	$PHP_ap_num=$parm_madd['ap_num'];
	//建po編號
	$tmp_num = substr($parm_madd['ap_num'],2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_ap_num);
} else {
	$po_num = "PO".substr($PHP_ap_num,2);
}

$parm_dadd = array( 
	'ap_num'	=>	$PHP_ap_num,
	'used_id'	=>	$PHP_used_id,
	'qty'		=>	$PHP_qty,
	'color'		=>	$PHP_color,
	'unit'		=>	$PHP_unit,
	'mat_cat'	=>	$PHP_mat_cat
);

$f1 = $po->add_pp($parm_dadd);	

$message = "Successfully appended PO : ".$po_num;
$op['msg'][] = $message;
$log->log_add(0,"037A",$message);

if( isset($PHP_id) )
	$wi_rec['id'] = $PHP_id;
else
	$wi_rec= $wi->get(0,$PHP_wi_num);

$redir_str='po.php?PHP_action=apply_pp_add&PHP_id='.$wi_rec['id'].'&PHP_used_id='.$PHP_used_id."&PHP_bom_code=".$PHP_mat_code."&PHP_item=".$PHP_mat_cat."&PHP_pa_num=".$PHP_ap_num."&PHP_vendor=".$PHP_vendor."&PHP_ord_num=".$PHP_wi_num."&ap_supl=".$ap_supl;
redirect_page($redir_str);

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_pp_det_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_pp_det_add":
check_authority('037',"add");

if(strstr($PHP_use_for,'&#'))	$PHP_use_for = $ch_cov->check_cov($PHP_use_for);

# 若沒有pa編號時,先建立請購單(母)
if ($PHP_ap_num =='')
{
	# 取出該筆 製造令記錄 ID
	if(!$ord = $order->get(0,$PHP_wi_num)){
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);
		break;
	}
	
	$parm_madd= array(
		'cust'  		=>	$ord['cust'],
		'dept'			=>	$ord['dept'],
		'sup_code'		=>	$PHP_vendor,
		'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		'ap_date'		=>	$TODAY,
		'ap_special'	=>	2,
	);
	
	# A+日期+部門=請購單開頭
	$head="PA".date('y')."-";
	# 取得請購的最後編號
	$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');
	# 新增AP資料庫
	$new_id = $apply->add($parm_madd);
	$message = "Successfully appended Purchase : ".$parm_madd['ap_num'];
	$log->log_add(0,"51A",$message);
	$PHP_ap_num = $parm_madd['ap_num'];

	$tmp_num = substr($parm_madd['ap_num'],2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_ap_num);
}

if ($PHP_item == 'lots')
{
	$parm_mat = array (	
	'field_name'	=>	'vendor1',
	'field_value'	=>	$PHP_vendor,
	'code'			=>	$PHP_mat_code
	);
	$lots->update_field_code($parm_mat);
	$mat_cat='l';
}else{
	$mat_cat='a';		
	$parm_mat = array (
	'field_name'	=>	'vendor1',
	'field_value'	=>	$PHP_vendor,
	'code'			=>	$PHP_mat_code
	);
	$acc->update_field_code($parm_mat);
}

$parm_dadd = array(
	'ap_num'		=>	$PHP_ap_num,
	'mat_code'		=>	$PHP_mat_code,
	'ord_num'		=>	$PHP_wi_num,
	'color'			=>	$PHP_color,
	'eta'			=>	$PHP_eta,
	'qty'			=>	$PHP_qty,
	'unit'			=>	$PHP_unit,
	'mat_cat'		=>	$mat_cat,
	'use_for'		=>	$PHP_use_for,
);

# 新增AP資料庫
$f1 = $apply->add_pp($parm_dadd);
$op = $apply->get($PHP_ap_num);
$message = "Successfully appended Purchase detial for Order : ".$PHP_wi_num;
$op['msg'][] = $message;
$log->log_add(0,"54A",$message);
$op['wi_num'] = $PHP_wi_num;
$op['item'] = $PHP_item;
$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
$op['ACC_select'] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit','select','');
$op['FAB_select'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');
for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
$op['revise'] = $PHP_revise;

if (isset($PHP_SCH_num))
{
	$back_str="&SCH_del=".$SCH_del."&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;
}

page_display($op, '037', $TPL_APLY_PP_ADD);			    	    
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_del":		job 51
# 刪除未送出採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_del":
		check_authority('037',"add");	
		$f1 = $apply->del_pa($PHP_ap_num);
		if($f1){
			$log->log_add(0,"037D","success deletet po ".str_replace("A","O",$PHP_ap_num));
		}
		$redirect_str = 'index2.php?PHP_action=apply';
		if(isset($PHP_back))$redirect_str='po.php?PHP_action=po_search&PHP_dept_code='.$PHP_dept_code."&SCH_del=".$SCH_del."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fyt."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_starton;
		redirect_page($redirect_str);
		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_apv_del":			job 51
# 刪除己核可採購單
# 需填寫異常報告
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_po_apv_del":
		check_authority('037',"add");	
		$rec = $po->get($PHP_ap_num);
		$ord_rec = array();
		if(isset($rec['ap_det']))
		{
			for($i=0; $i<sizeof($rec['ap_det']); $i++)
			{
				$mk = 0;
				for($j=0; $j<sizeof($ord_rec); $j++)
				{
					if($ord_rec[$j] == $rec['ap_det'][$i]['ord_num'])
					{
						$mk = 1;
						break;
					}
				}
				if($mk == 0) $ord_rec[] = $rec['ap_det'][$i]['ord_num'];

			}
		}

		if(isset($rec['ap_spec']))
		{
			for($i=0; $i<sizeof($rec['ap_spec']); $i++)
			{
				$mk = 0;
				for($j=0; $j<sizeof($ord_rec); $j++)
				{
					if($ord_rec[$j] == $rec['ap_spec'][$i]['ord_num']) $mk = 1;
				}
				if($mk == 0) $ord_rec[] = $rec['ap_spec'][$i]['ord_num'];
			}
		}
		$ord_num = '';
		for($j=0; $j<sizeof($ord_rec); $j++) $ord_num .= $ord_rec[$j].",";
//		$ord_num = substr($ord_num,0,-1);
		if($rec['ap']['dear'] == 0) $rec['ap']['dear'] = $rec['ap']['po_total'];
		$rec['ap']['dear'] = number_format($rec['ap']['dear'],2,'.',',');
//		$f1 = $apply->del_pa($PHP_ap_num);

			$op['ap_num'] = $PHP_ap_num;
			
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
			// 取出選項資料 及傳入之參數
			$op['msg'][]= "Please write Exceptional for PO : [".$rec['ap']['po_num']."]";
			$op['cust_id'] = $rec['ap']['cust'];
			$op['dept_id']	 = $user_dept;
			$op['date'] = $TODAY;
			$op['exc']['ord_num'] = $ord_num;
			$op['exc']['po_num'] = $rec['ap']['po_num'];
			$op['rec']['dear'] = $rec['ap']['currency']."$".$rec['ap']['dear'];
			
			$op['rec']['sys_reason'] = "採購單 [ ".$rec['ap']['po_num']." ] 己經核可後再刪除 :[ ".$rec['ap']['currency']."$".$rec['ap']['dear']."]"
														 .chr(13).chr(10)."相關成衣訂單 : ".$ord_num
														 .chr(13).chr(10)."承辦業務如果己經對供應商發出採購合約，而未確實追返"
														 .chr(13).chr(10)."將造成該採購合約己刪除，而卻仍然合約採購中"
														 .chr(13).chr(10)."刪除己核可之採購單務必追回己發出之採購合約";
			// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
			page_display($op,'069',$TPL_EXC_ADD);		
			break;		
	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_ann":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_ann":
		check_authority('038',"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		
		$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// 如果是業務部進入 則dept_code 指定該業務部---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			$manager_v = 1;
			//業務部門 select選單
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
//			$where_str = " WHERE dept = '".$dept_id."' ";			
		}

		$where_str=$where_str."order by cust_s_name"; //依cust_s_name排序
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //取出客戶簡稱
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}

		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
		$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 


		$op['manager_flag'] = $manager;
		$op['manager_v_flag'] = $manager_v;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);

	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

		  	
		page_display($op,'038', $TPL_PO_ANN);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_ann_search":
	check_authority('038',"view");	

	if (!$op = $po->search(2, $PHP_dept_code)) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		for ($i=0; $i<sizeof($op['apply']); $i++)
		{
			if ($op['apply'][$i]['special'] == 2){$table = 'ap_special';}else{$table = 'ap_det';}
			$where_str = "ap_num ='".$op['apply'][$i]['ap_num']."' GROUP BY ap_num";
			
			$row = $po->get_det_field('min(po_eta) as eta',$table,$where_str);
			$op['apply'][$i]['eta'] = $row['eta'];
		}
			


	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ann=".$SCH_ann;
	$op['back_str']=$back_str;

	if (!$PHP_dept_code) {   // 如果 不是 呈辦業務部門進入時...
		$op['manager_flag'] = 1;
	}
	$op['dept_id'] = $PHP_dept_code;
	$op['msg']= $po->msg->get(2);
	if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
	page_display($op,'038', $TPL_PO_ANN_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_ann_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_po_ann_add":
	check_authority('038',"view");	

	if($PHP_org_ann)
	{
		$ann_num = $PHP_org_ann.",".$PHP_ann_no;
	}else{
		$ann_num = $PHP_ann_no;
	}

	$f1=$apply->update_fields_id('ann_num',$ann_num, $PHP_id, 'ap');
	$msg = "Successfully add WA-ANN Number on PO".$PHP_po_num;
	$redirect_str = 'po.php?PHP_action=po_ann_search'.$PHP_back_str."&PHP_msg=".$msg;
	redirect_page($redirect_str);
	
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_ann_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_ann_del":
	check_authority('038',"view");	

	$f1=$apply->update_fields_id('ann_num','', $PHP_id, 'ap');
	$msg = "Successfully Delete WA-ANN Number on PO".$PHP_po_num;

	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ann=".$SCH_ann;

	$redirect_str = 'po.php?PHP_action=po_ann_search'.$back_str."&PHP_msg=".$msg;
	redirect_page($redirect_str);
	
	break;	




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pa_print":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pa_print":   //  .......
	check_authority('037',"view");	
	$where_str="AND( item like '%Document%' OR item like '%special%') AND !(des like '%<a href%')";		
	$op = $po->get($PHP_aply_num,$where_str);

	//取得user name
/*	
	$po_user=$user->get(0,$op['ap']['apv_user']);
	if ($po_user['name'])$op['ap']['apv_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['cfm_user']);
	if ($po_user['name'])$op['ap']['cfm_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['submit_user']);
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
*/
	$mat_cat='';
	$where_str = " WHERE dept_code = '".$op['ap']['dept']."'";
//	echo $where_str;
	$dept_name=$dept->get_fields('dept_name',$where_str);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'] = $op['ap_det'][0]['ord_num'];
		if ($op['ap_det2'][0]['mat_cat'] == 'l')	$mat_cat="Fabric";
		if ($op['ap_det2'][0]['mat_cat'] == 'a')	$mat_cat="Accessory";		
		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] === $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					$op['ap_det2'][$j]['amount'] = $op['ap_det'][$i]['amount'] +$op['ap_det2'][$j]['amount'];
					if (!strstr($op['ap_det2'][$j]['orders'],$op['ap_det'][$i]['ord_num']))
							$op['ap_det2'][$j]['orders'] = $op['ap_det2'][$j]['orders']."|".$op['ap_det'][$i]['ord_num'];
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'] = $op['ap_det'][$i]['ord_num'];
				$k++;
			}
		}
	}else{
		if ($op['ap_spec'][0]['mat_cat'] == 'l')	$mat_cat="Fabric";
		if ($op['ap_spec'][0]['mat_cat'] == 'a')	$mat_cat="Accessory";		
	}
	$ary=array('A','B','C','D','E','F','G','H','I');
	for ($i=0; $i<sizeof($ary); $i++)
	{
		if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
	}
				
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_po.php");

if($op['ap']['base_ck'] == 1){
	$print_title="Min. Purchase Application";
	$mark = $op['ap']['ap_num'];
}else if ($op['ap']['special'] == 0){
	$print_title="Regular Purchase Application";
	$mark = $op['ap']['ap_num'];
}else if ($op['ap']['special'] == 3){
	$print_title="Pre-Purchase Application";
	$mark = "Pre-Order ".$op['ap']['ap_num'];
}else{
	$print_title="SPECIAL Purchase Application";
	$mark = "SPECIAL ".$op['ap']['ap_num'];
}
$open_dept = $op['ap']['open_dept'];

# 輸出頁面設定
$_SESSION['PDF']['w'] = $tmp_full_w = 275;	# 預設可用寬度
$_SESSION['PDF']['h'] = 200;								# 預設可用高度
$_SESSION['PDF']['lh'] = 5;

$print_title2 = "VER.".($op['ap']['revise']+1)."   for   ".$mat_cat;
$creator = $op['ap']['ap_user'];

$pdf=new PDF_po('L','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Big5','B',14);

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

$parm = array (	'po_num'		=>	$op['ap']['po_num'],
							'supplier'		=>	$op['ap']['s_name'],
							'dept'				=>	$dept_name[0],
							'ap_date'			=>	$op['ap']['po_apv_date'],
							'ap_user'			=>	$op['ap']['ap_user'],
							'currency'		=>	$op['ap']['currency'],
							'dm_way'			=>	$op['ap']['dm_way'],
				);
$pdf->hend_title($parm);
$pdf->ln();
$pdf->SetFont('Arial','B',10);
$pdf->cell(20, 5,"P.I. NO.",0,0,'L');
$pdf->cell(145, 5,$op['ap']['pi_num'],0,0,'L');
$pdf->ln();
$pdf->ln();
/*
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,5,'Send TO : ',0,0,'L');
$pdf->cell(100,5,$op['ap']['ship_name'],0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(25,5,'RCVER ATTN : ',0,0,'L');
$pdf->cell(20,5,$op['ap']['ship_attn'],0,0,'L');
$pdf->Cell(23,5,'RCVER TEL : ',0,0,'L');
$pdf->cell(35,5,$op['ap']['ship_tel'],0,0,'L');
$pdf->Cell(23,5,'RCVER FAX : ',0,0,'L');
$pdf->cell(25,5,$op['ap']['ship_fax'],0,0,'L');
$pdf->ln();
$pdf->Cell(28,5,'RCVER ADDR : ',0,0,'L');
$pdf->cell(250,5,$op['ap']['ship_addr'],0,0,'L');
$pdf->ln();
$pdf->ln();
if($op['ap']['finl_dist'])
{
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(20,5,'Final Dist. : ',0,0,'L');
	$pdf->cell(100,5,$op['ap']['finl_name'],0,0,'L');
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(25,5,'RCVER ATTN : ',0,0,'L');
	$pdf->cell(20,5,$op['ap']['finl_attn'],0,0,'L');
	$pdf->Cell(23,5,'RCVER TEL : ',0,0,'L');
	$pdf->cell(35,5,$op['ap']['finl_tel'],0,0,'L');
	$pdf->Cell(23,5,'RCVER FAX : ',0,0,'L');
	$pdf->cell(25,5,$op['ap']['finl_fax'],0,0,'L');	
	$pdf->ln();
	$pdf->Cell(28,5,'RCVER ADDR : ',0,0,'L');
	$pdf->cell(250,5,$op['ap']['finl_addr'],0,0,'L');
	$pdf->ln();
	$pdf->ln();
}
$pdf->po_title();
$pdf->ln();
$x=2;$total=0;
if (isset($op['ap_det2']))
{
	for ($i=0; $i< sizeof($op['ap_det2']); $i++)
	{		
//		$total = $total + ($op['ap_det2'][$i]['amount']);
		$op['ap_det2'][$i]['price1'] = NUMBER_FORMAT($op['ap_det2'][$i]['price1'],2);
		$op['ap_det2'][$i]['prics'] = NUMBER_FORMAT($op['ap_det2'][$i]['prics'],2);
		$op['ap_det2'][$i]['ap_qty'] = NUMBER_FORMAT($op['ap_det2'][$i]['po_qty'],2);
		$op['ap_det2'][$i]['amount'] = NUMBER_FORMAT($op['ap_det2'][$i]['amount'],2);
		
		$ords= explode('|',$op['ap_det2'][$i]['orders']);		
		
		$ord_cut = sizeof($ords);
		$x = $x+$ord_cut;
		//if ($x > 20)
		if ( $pdf->GetY() >= 140 )
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		$f1 = 0;
		if ($ord_cut > 1)
		{
			$f1 = $pdf->pa_det_mix($op['ap_det2'][$i],$ords);
		}else{
			$f1 = $pdf->pa_det_mix($op['ap_det2'][$i],$ords);
			//$pdf->pa_det($op['ap_det2'][$i]);
		}
		$pdf->ln();
		$f1 = $f1 - $ord_cut;
		if($f1 > 0)$x = $x+$f1;
		
		
		
	}
}

if (isset($op['ap_spec']))
{
	for ($i=0; $i< sizeof($op['ap_spec']); $i++)
	{
		$x++;
//		$total = $total + ($op['ap_spec'][$i]['price1']*$op['ap_spec'][$i]['ap_qty']);
		$op['ap_spec'][$i]['price1'] = NUMBER_FORMAT($op['ap_spec'][$i]['price1'],2);
		$op['ap_spec'][$i]['prics'] = NUMBER_FORMAT($op['ap_spec'][$i]['prics'],2);
		$op['ap_spec'][$i]['ap_qty'] = NUMBER_FORMAT($op['ap_spec'][$i]['po_qty'],2);
		$op['ap_spec'][$i]['amount'] = NUMBER_FORMAT($op['ap_spec'][$i]['amount'],2);
		if ($x > 20)
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		$ords[0] = $op['ap_spec'][$i]['ord_num'];
		$f1 = $pdf->pa_det_mix($op['ap_spec'][$i],$ords);
//		$pdf->pa_det($op['ap_spec'][$i]);
		$pdf->ln();		
	}
}
$x=$x+2;
		if ($x > 20)
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();				
			$x=0;
		}

	for ($i=0; $i< sizeof($op['ap_oth_cost']); $i++)
	{
		$x++;
		$op['ap_oth_cost'][$i]['cost'] = NUMBER_FORMAT($op['ap_oth_cost'][$i]['cost'],2);
		if ($x > 20)
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}		
		$pdf->oth_cost($op['ap_oth_cost'][$i]);
		$pdf->ln();		
	}
		
		
//$total = NUMBER_FORMAT($total,2);
$op['ap']['dear'] = NUMBER_FORMAT($op['ap']['dear'],2);
$op['ap']['po_total'] = NUMBER_FORMAT($op['ap']['po_total'],2);
$pdf->Cell(18,5,'Re-Mark','TRL',0,'C');
$pdf->Cell(242,5,'Total : '.$op['ap']['currency'].' $'.$op['ap']['po_total'],'T',0,'R');
$pdf->Cell(20,5,'','TR',0,'C');
$pdf->ln();
$pdf->Cell(18,5,'','RL',0,'C');
$pdf->Cell(242,5,'Deal Amount : '.$op['ap']['currency'].' $'.$op['ap']['dear'],0,0,'R');
$pdf->Cell(20,5,'','R',0,'C');
$pdf->ln();
$pdf->Cell(18,5,'','RL',0,'C');
$pdf->SetFont('BIG5','',8);
$pdf->Cell(242,5,'Delivery : '.$op['ap']['usance'].' days',0,0,'L');
$pdf->Cell(20,5,'','R',0,'C');
$pdf->ln();

for ($i=0; $i< sizeof($op['apply_log']); $i++)
{
	$x+=mb_strlen($op['apply_log'][$i]['des'])/160;
		if ($x > 18)
		{
			$pdf->Cell(18,5,'','RLB',0,'C');
			$pdf->Cell(242,5,'','B',0,'L');
			$pdf->Cell(20,5,'','BR',0,'C');
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();	
			$pdf->Cell(18,5,'Logs','TRL',0,'C');
			$pdf->Cell(242,5,'','T',0,'C');
			$pdf->Cell(20,5,'','TR',0,'C');
			$pdf->ln();	
			$x=0;
		}
	for($j=0; $j<mb_strlen($op['apply_log'][$i]['des']); $j+=160)
	{	
		$pdf->Cell(18,5,'','RL',0,'C');
		$pdf->SetFont('Big5','',9);	
		$pdf->Cell(242,5,mb_substr($op['apply_log'][$i]['des'],$j,($j+160)),0,0,'L');	
		$pdf->Cell(20,5,'','R',0,'C');
		$pdf->ln();		
	}
}
	$pdf->Cell(18,5,'','RLB',0,'C');
	$pdf->Cell(242,5,'','B',0,'L');
	$pdf->Cell(20,5,'','BR',0,'C');

	$pdf->ln();
	$pdf->SetFont('Big5','',10);
//	$pdf->Cell(50,5,' ','0',0,'C');
//	$pdf->Cell(50,5,'',0,0,'L');
//	$pdf->Cell(50,5,'',0,0,'L');//PO Submit


	if($op['ap']['special'] > 0)
	{
		// $pdf->cell(150,5,'總經理 : ','0',0,'L');
		$pdf->cell(150,5,'','0',0,'L');
	}else{
	$pdf->Cell(150,5,'',0,0,'L');
	}
	$pdf->Cell(50,5,'APPROVAL : 黃穎琨','0',0,'C');	//PO Approval .$op['ap']['po_apv_user']
	$pdf->Cell(50,5,'CONFIRM :'.$op['ap']['po_cfm_user'],0,0,'L');//PO Confirm
	
	$pdf->Cell(50,5,'PO :'.$op['ap']['po_user'],0,0,'L');//PA submit
	
	
	
//異常報告 取消
if($op['ap']['special'] == 'mode')
{	
	$print_title = "特殊採購　異常報告";

	$print_title2 = "列印日期:".date('Y-m-d');
	$creator = $op['ap']['po_sub_user'];

	
	$ord_num = $op['ap_det2'][0]['ord_num'];
	
	$title_ary['ord_num'] = $op['ap_det2'][0]['ord_num'];
	$title_ary['cust'] = $op['ap']['cust'];

	$title_ary['ord_nums'] = '';
	$title_ary['po_num'] = $op['ap']['po_num'];
	$title_ary['ex_num'] = $op['ap']['po_num'];

	$x = 0;

	$pdf->Open();
	$pdf->AddPage('P');
	$pdf->SetFont('Arial','B',14);
	$pdf->SetCreator('angel');
	$pdf->title_simpl($title_ary);
	$pdf->SetFont('Big5','',12);
	$pdf->cell(190,7,'異常狀態',1,0,'C');
	$pdf->ln();	
	$pdf->cell(190,7,'採購單額外採購',1,0,'L');
	$pdf->ln();
	$pdf->fld_title('發生原因及預估損失');
	
	$exc_des = '本　特殊採購單編號 '.$op['ap']['po_num'].'所述採購理由如下　：<br>';
	for ($i=0; $i< sizeof($op['apply_log']); $i++)
	{
		$exc_des .=$op['apply_log'][$i]['des'].'<br>';		
	}
	$r = $pdf->exc_value($exc_des);
	$x+=$r;
	
//簽名
	$pdf->SetY(248);
	$pdf->cell(50,4,'總經理 : ',0,0,'L');
	$pdf->cell(50,4,'副總經理 : '.$op['ap']['po_apv_user'],0,0,'L');
	$pdf->cell(50,4,'主管 : ',0,0,'L');
	$pdf->cell(40,4,'承辦單位 : '.$SYS_DEPT,0,0,'L');
	$pdf->ln();
	$pdf->ln();
	$pdf->cell(50,4,'',0,0,'L');
	$pdf->cell(50,4,'(責任歸屬裁定)',0,0,'L');
	$pdf->cell(50,4,'',0,0,'L');
	$pdf->cell(40,4,'承辦人 : '.$op['ap']['po_sub_user'],0,0,'L');

//備註
	$pdf->SetFont('Big5','',7);
	$pdf->ln();
	$pdf->ln();
	$pdf->cell(190,5,'* 本表應於事發2日內填寫，經相關單位會辦見後送交總經理室，呈總經理核簽，以為日後請款附件',0,0,'L');
	$pdf->ln();
	$pdf->SetY(267);

	$pdf->cell(190,5,'* 生產企劃室應整合異常報告資料後於每月經營會議中匯告',0,0,'L');
	$pdf->SetY(270);
	$pdf->cell(190,5,'* 本表欄位不敷使用時，請自行黏貼附件填寫',0,0,'L');	
	
		
}
	
	
	
	
	

$name=$op['ap']['ap_num'].'_pa.pdf';
//echo "<meta http-equiv='refresh' content='50000; url=close2.html'>";
$pdf->Output($name,'D');


break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ord_bom_match":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_bom_match":
check_authority('037',"add");		

if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$parm = array(
	'ap_num'		=>	$PHP_ap_num,
	'ap_det_id'	=>	$PHP_ap_det_id,
	'bom_id'		=>	$PHP_bom_id
);
// print_r($parm);

if ($PHP_item == 'lots')
{
	$chg_rec=$bom->get_old_lots($parm);	
	$cgh_rec['mat_des'] = $chg_rec['mat_code']."(".$chg_rec['lots_name']." 使用於 ".$chg_rec['use_for'].")";
} else {
	$chg_rec=$bom->get_old_acc($parm);
	$cgh_rec['mat_des'] = $chg_rec['mat_code']."(".$chg_rec['acc_name']." 使用於 ".$chg_rec['use_for'].")";
}

if($chg_rec['dif_qty'] < 0)
{


	$chg_rec['po_qty'] = change_unit_qty($chg_rec['po_unit'],$chg_rec['unit'],$chg_rec['po_qty']);
	$chg_rec['po_qty'] = number_format($chg_rec['po_qty'],2,'.','');
	if($chg_rec['prc_unit']){$org_unit = $chg_rec['prc_unit'];}else{$org_unit = $chg_rec['po_unit'];}

	$chg_rec['prics'] = change_unit_price($org_unit,$chg_rec['unit'],$chg_rec['prics']);
	$chg_rec['prics'] = number_format($chg_rec['prics'],2,'.','');
	$chg_rec['po_amount'] = $chg_rec['amount'] ;
	$chg_rec['bom_amount'] = $chg_rec['new_qty'] * $chg_rec['prics'];
	$more_qty = $chg_rec['po_qty'] - $chg_rec['new_qty'];  //計算差異數量

	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
	// 取出選項資料 及傳入之參數
	$op['msg'][]= "Please write Exceptional for Order : [".$ord['wi_num']."]";
	$op['cust_id'] = $ord['cust'];
	$op['dept_id']	 = $user_dept;
	$op['date'] = $TODAY;
	$op['exc']['ord_num'] = $ord['wi_num'].',';
	$op['rec'] = $chg_rec;
	$op['rec']['sys_reason'] = $chg_rec['mat_des']."因BOM修改後造成採購單 [".$chg_rec['po_num']."] 過量採購".chr(13).chr(10)."超額量為 : ".$more_qty.
												 " 單價 : ".$chg_rec['prics']." 差異總金額 : ".number_format(($chg_rec['prics'] * $more_qty),2,'.',',');
	// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
	$op['no_pm'] = 1;
	// print_r($chg_rec);
	page_display($op,'069',$TPL_EXC_ADD);		
	break;		
}

$message = "Success chage PO  : [".$chg_rec['po_num']."] to Material : [".$chg_rec['mat_code']."]";
$redirect_str ="index2.php?PHP_action=apply_wi_view&other=other&PHP_sr".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_id=".$PHP_wi_id."&PHP_full=".$PHP_full."&PHP_msg=".$message;	
redirect_page($redirect_str);	

exit;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "log_del_ajx":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "log_del_ajx":

$f1=$po->del_log($PHP_id);	//儲存總金額		
//$PHP_po_num=str_replace("A", "O",$PHP_po_num);
$message = "Successful Delete PO Log ";

echo $message;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_shift":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_po_shift":

		$op=$po->po_search_by_ord($PHP_ord_num,'',1);	//搜尋採購單		

		for($i=0; $i<sizeof($op['apply']); $i++)
		{
			$po->update_field_num('status', 6 , $op['apply'][$i]['po_num']); //採購狀態回復wait for submit
			$f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $op['apply'][$i]['id']);
			$f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $op['apply'][$i]['id']);
			$f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $op['apply'][$i]['id']);

		}
		$op['shift']['ord_num'] = $PHP_ord_num;
		$op['shift']['org_fty'] = $PHP_fty_org;
		$op['shift']['org_fty'] = $PHP_id;
		$op['shift']['new_fty'] = $PHP_fty_new;
		$op['back_str'] = "&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_id."&PHP_fty_new=".$PHP_fty_new;
		page_display($op,'069',$TPL_PO_SHIFT_LIST);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "check_po_shift":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "check_po_shift":

		$op=$po->po_search_by_ord($PHP_ord_num);	//搜尋採購單	
		$po_count =(sizeof($op['apply']));		
		$op=$po->po_search_by_ord($PHP_ord_num,'',2);	//搜尋採購單	 -- 己驗收
	  $rcv_count =(sizeof($op['apply']));		
		
		echo $po_count.'|'.$rcv_count;
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "aply_shift_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "aply_shift_edit":
		check_authority('037',"edit");		
	$where_str="AND( item like '%special%')";		
	$op = $apply->get($PHP_aply_num,$where_str);

				
//組合畫面List
	if (isset($op['ap_det']))
	{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
	}
	
/*	
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");


		$op['back_str'] = "&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_ord_id."&PHP_fty_new=".$PHP_fty_new;
		
		page_display($op, '037', $TPL_APLY_SHIFT_EDIT);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_sfhift_list":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "po_sfhift_list":

		$op=$po->po_search_by_ord($PHP_ord_num,6,1);	//搜尋採購單		

		$op['back_str'] = "&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_ord_id."&PHP_fty_new=".$PHP_fty_new;
		page_display($op,'069',$TPL_PO_SHIFT_LIST);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_shift_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_shift_edit":
		check_authority('037',"edit");	
		$f1=$apply->update_fields_id('special',$PHP_new_special,$PHP_id);	 //記錄申請日

		$back_str = "&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_ord_id."&PHP_fty_new=".$PHP_fty_new;
		
		if ($op['ap']['revise'] == 0 && $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
		$redir_str = "po.php?PHP_action=po_shift_revise&PHP_aply_num=".$PHP_ap_num."&PHP_revise=1&PHP_id=".$op['ap']['id']."&PHP_new_special=".$PHP_new_special.$back_str;
		redirect_page($redir_str);

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_revise":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_shift_revise":
		check_authority('037',"edit");	

	$op = $po->get($PHP_aply_num);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
		$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
		$units = $op['ap_det'][0]['unit'];
		if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

		$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
		$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');


		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			$x=1;	$order_add=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++)
					{
						if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0)	$op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
					$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
					$x++;
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
				$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
//				if ($op['ap_det'][$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
				if ($op['ap_det'][$i]['prc_unit']){$prc_units = $op['ap_det'][$i]['prc_unit'];}else{$prc_units = $op['ap_det'][$i]['unit'];}
				$units = $op['ap_det'][$i]['unit'];
				$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
				$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');

				$k++;
			}
		}
	}
	if (isset($op['ap_spec']))
	{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
//				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
				$units = $op['ap_spec'][$i]['unit'];
				if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}

				$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
				$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');

		}
	}

/*
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
		$op['ap']['special'] = $PHP_new_special;
		$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');
		$op['back_str'] = "&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_ord_id."&PHP_fty_new=".$PHP_fty_new;

		$op['revise'] = 1;

		page_display($op, '037', $TPL_PO_SHIFT_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_shift_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_shift_submit":
	check_authority('037',"edit");	


	if(isset($PHP_CURRENCY))
	{
		if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

		$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
		
		
		$apply->update_fields_id('dm_way',$PHP_dmway,$PHP_id);	//Update 幣別

		$parm = array( 'field_name'		=>	'dm_way',
									 'field_value'	=>	$PHP_dmway,
									 'id'						=>	$PHP_sup_id
									 );
		$supl->update_field($parm);
	
		$parm_log = array(	'ap_num'	=>	$PHP_aply_num,
												'item'		=>	'special',
												'des'			=>	$PHP_des,
												'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
												'k_date'	=>	$TODAY,
								);
		if ($PHP_des <> $PHP_old_des && $PHP_des <> 'no_des')
		{
			$f1=$apply->add_log($parm_log);		
		}
		
		$parm_log = array(	'ap_num'	=>	$PHP_aply_num,
												'item'		=>	'Document',
												'des'			=>	'訂單'.$PHP_ord_num.'由工廠 ['.$PHP_fty_org.']移轉至 ['.$PHP_fty_new.'],而需更新並重置採購單',
												'user'		=>	'SYSTEM',
												'k_date'	=>	$TODAY,
								);
		$f1=$apply->add_log($parm_log);		

	  if (!$PHP_FACTORY)	//若無運送地(ID)時,記錄運送地詳細資料
	  {
	  	$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//運送地址
			$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//運送地公司名
			$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//運送地傳真
			$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//運送地電話
			$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //運送地聯絡人
	  }  
	}



	$tmp_num = substr($PHP_aply_num,2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);
//	$f1=$apply->update_fields('status','6', $PHP_aply_num);
	$PHP_po_num = $po_num;

	$f1=$apply->update_2fields('status', 'po_sub_date', '8', $TODAY, $PHP_id);
	$f1=$apply->update_2fields('fob', 'fob_area', $PHP_fob, $PHP_fob_area, $PHP_id); // Price term
	$f1=$apply->update_fields('po_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
		if ($f1)
	{
		$message="ORDER $PHP_ord_num SHIFT TO $PHP_fty_new Change  PO :".$PHP_po_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	
	$back_str = "&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_ord_id."&PHP_fty_new=".$PHP_fty_new;

	$redir_str = "po.php?PHP_action=po_sfhift_list".$back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "check_po_rcvd":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "check_po_rcvd":

		$ap_str = $rcv_str= $tr_str='';

		$ap_num=$po->po_search_by_ord($PHP_ord_num,'',2);	//搜尋採購單	
		for ($i=0; $i < sizeof($ap_num['apply'])	; $i++) $ap_str .= $ap_num['apply'][$i]['po_num'];
		
		$rcv_num = array();
		$ap_num = array();
		$rcv_mk = $ap_mk = 0;
		$fab = $po->po_rcvd_fab($PHP_ord_num);
		$acc = $po->po_rcvd_acc($PHP_ord_num);

		$head="TP".date('y')."-";	//TP+日期=轉運單開頭
		$parm['org_fty'] = $PHP_fty_org;
		$parm['new_fty'] = $PHP_fty_new;
		$parm['ord_num'] = $PHP_ord_num;

		$parm_msg['user'] = 'SYSTEM';
		$parm_msg['item'] = 'Document';
		$parm_msg['k_date'] = date('Y-m-d');
		
		
		if($fab && sizeof($fab) > 0)
		{		
			$rcv_str= '主料驗收單 : ';	
			$parm['trn_num'] = $apply->get_no($head,'trn_num','transport');	//取得轉運的最後編號
			$tr_str =$parm['trn_num'];
			$parm['mat_cat'] = 'l';
			$f1 = $trans->shift_add($parm);
			for($i=0; $i<sizeof($fab); $i++)
			{
				$parm_det = $fab[$i];
				$parm_det['trn_num'] = $parm['trn_num'];
				$parm_det['mat_cat'] = 'l';
				$f1 = $trans->shift_add_det($parm_det);
				$parm_msg['des'] = "Order [ ".$PHP_ord_num." ] Shift. Material [ ".$parm_det['mat_code']." ] was receive and need to transport to FTY :[ ".$PHP_fty_new." ]. Transport# : [ ".$parm['trn_num']." ] ";
				$parm_msg['ap_num'] = $fab[$i]['ap_num'];
				$parm_msg['rcv_num'] = $fab[$i]['rcv_num'];
				
				$f1 = $apply->add_log($parm_msg);			
				$f1 = $receive->add_log($parm_msg);
				
				for($j=0; $j<sizeof($rcv_num); $j++)
				{
					if($rcv_num[$j] == $fab[$i]['rcv_num'])
					{
						$rcv_mk = 1;
						break;
					}
				}
				if($rcv_mk == 0) $rcv_num[] = $fab[$i]['rcv_num'];
				$rcv_mk = 0;
				
				for($j=0; $j<sizeof($ap_num); $j++)
				{
					if($ap_num[$j] == $fab[$i]['ap_num'])
					{
						$ap_mk = 1;
						break;
					}
				}
				if($ap_mk == 0) $ap_num[] = $fab[$i]['ap_num'];				
				$ap_mk = 0;
			}			
			$msg = "Order shift : [ ".$PHP_ord_num." ] TRANSPORT# : [ ".$parm['trn_num']." ] ,infuence of received on fab. PO# : [ ";
			
			for($k=0; $k < sizeof($ap_num); $k++)
			{
				$msg = $msg.$ap_num[$k].",";
			}
			$msg = substr($msg,0,-1)." ] , rcv# : [ ";
			for($k=0; $k<sizeof($rcv_num); $k++)
			{
				$msg = $msg.$rcv_num[$k].",";
				if (!strstr($rcv_str, $rcv_num[$k]))$rcv_str .= $rcv_num[$k].",";
			}
			$msg = substr($msg,0,-1)." ] ";
			$log->log_add(0,"101S",$msg);
		}
		
		$rcv_num = array();
		$ap_num = array();
		if($acc && sizeof($acc) > 0)
		{			
			$rcv_str .= '  副料驗收單 : ';	

			$parm['trn_num'] = $apply->get_no($head,'trn_num','transport');	//取得轉運的最後編號
			$tr_str .= ','.$parm['trn_num'];
			$parm['mat_cat'] = 'a';
			$f1 = $trans->shift_add($parm);
			for($i=0; $i<sizeof($acc); $i++)
			{
				$parm_det = $acc[$i];
				$parm_det['trn_num'] = $parm['trn_num'];
				$parm_det['mat_cat'] = 'a';
				$f1 = $trans->shift_add_det($parm_det);
				$parm_msg['des'] = "Order [ ".$PHP_ord_num." ] shift. Material [ ".$parm_det['mat_code']." ] was received and need to transport to  FTY :[ ".$PHP_fty_new." ]. Transport# : [ ".$parm['trn_num']." ] ";
				$parm_msg['ap_num'] = $acc[$i]['ap_num'];
				$parm_msg['rcv_num'] = $acc[$i]['rcv_num'];
				$f1 = $apply->add_log($parm_msg);			
				$f1 = $receive->add_log($parm_msg);				
			
				for($j=0; $j<sizeof($rcv_num); $j++)
				{
					if($rcv_num[$j] == $acc[$i]['rcv_num'])
					{
						$rcv_mk = 1;
						break;
					}
				}
				if($rcv_mk == 0) $rcv_num[] = $acc[$i]['rcv_num'];
				
				for($j=0; $j<sizeof($ap_num); $j++)
				{
					if($ap_num[$j] == $acc[$i]['ap_num'])
					{
						$ap_mk = 1;
						break;
					}
				}
				if($ap_mk == 0) $rcv_num[] = $acc[$i]['ap_num'];					
			}		

			$msg = "Order Shift [ ".$PHP_ord_num." ] Trasport# : [ ".$parm['trn_num']." ] influence of received on acc. po# [ ";
			for($k=0; $k<sizeof($ap_num); $k++)
			{
				$msg = $msg.$ap_num[$k].",";
			}
			$msg = substr($msg,0,-1)." ] rcv# [ ";
			for($k=0; $k<sizeof($rcv_num); $k++)
			{
				$msg = $msg.$rcv_num[$k].",";
				if (!strstr($rcv_str, $rcv_num[$k]))$rcv_str .= $rcv_num[$k].",";				
			}
			$msg = substr($msg,0,-1)." ] ";
			$log->log_add(0,"101S",$msg);			
				
		}

			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
			// 取出選項資料 及傳入之參數
			$op['msg'][]= "Please write Exceptional for ORDER SHIFT : [".$PHP_ord_num."]";
			$op['cust_id'] = substr($PHP_ord_num,1,2);
			$op['dept_id']	 = $user_dept;
			$op['date'] = $TODAY;
			$op['exc']['ord_num'] = $PHP_ord_num.',';
//			$op['rec']['shift'] = $rec['ap']['currency']."$".$rec['ap']['dear'];
			
			$op['rec']['sys_reason'] = "訂單 [ ".$PHP_ord_num." ] 移轉,由工廠 :[ ".$PHP_fty_org."]轉至工廠 : [".$PHP_fty_new."]"
														 .chr(13).chr(10)."其中部分物料己驗收 : [".substr($rcv_str,0,-1)."]"														 
														 .chr(13).chr(10)."相關採購單 : [".substr($ap_str,0,-1)."]"
														 .chr(13).chr(10)."相關轉運單 : [".$tr_str."]"
														 .chr(13).chr(10)."己驗收物料將造成轉運成本,訂單移轉後請提醒工廠[".$PHP_fty_org."]轉運相關物料";
														 










			// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表

			$op['ord_shift'] = "index2.php?PHP_action=do_order_shift&PHP_order_num=".$PHP_ord_num."&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_ord_id."&PHP_fty_new=".$PHP_fty_new;
			page_display($op,'069', $TPL_EXC_ADD);

//		$redir_str = "index2.php?PHP_action=do_order_shift&PHP_order_num=".$PHP_ord_num."&PHP_ord_num=".$PHP_ord_num."&PHP_fty_org=".$PHP_fty_org."&PHP_ord_id=".$PHP_ord_id."&PHP_fty_new=".$PHP_fty_new;
//		redirect_page($redir_str);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	 case "get_ship_ajax":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "get_ship_ajax":
	if ($PHP_ship)
	{
		echo $SHIP_TO[$PHP_ship]['Name'].'|'.$SHIP_TO[$PHP_ship]['Addr'].'|'.$SHIP_TO[$PHP_ship]['TEL'].'|'.$SHIP_TO[$PHP_ship]['FAX'].'|'.$SHIP_TO[$PHP_ship]['Attn'];
	}else{
		echo '||||';
	}

	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	 case "get_ship_ajax":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "get_finl_ajax":
	if ($PHP_finl)
	{
		echo $DIST_TO[$PHP_finl]['Name'].'|'.$SHIP_TO[$PHP_finl]['Addr'].'|'.$SHIP_TO[$PHP_finl]['TEL'].'|'.$SHIP_TO[$PHP_finl]['FAX'].'|'.$SHIP_TO[$PHP_finl]['Attn'];
	}else{
		echo '||||';
	}

	break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	 case "chk_det_ajax":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "chk_det_ajax":
	$det = $po->get_det_group($PHP_num);
	$rtn_str = '';
	for($i=0; $i<sizeof($det); $i++)
	{
		$po_qty = $det[$i]['po_qty'];
		$ap_qty = $det[$i]['qty'] + ($det[$i]['qty'] * ($det[$i]['range'] / 100) );
		$ap_qty = change_unit_qty($det[$i]['unit'], $det[$i]['po_unit'],$ap_qty);
		$tmp_qty = explode('.',$ap_qty);
		if(isset($tmp_qty[1]) && $tmp_qty[1] > 0)$tmp_qty[0]++;
		$ap_qty = $tmp_qty[0];
		if($det[$i]['base_qty'])$ap_qty = $det[$i]['base_qty'];
		if($po_qty > $ap_qty)
		{
			$rtn_str .= "Material [".$det[$i]['mat_code']."], Color [".$det[$i]['mat_color']."], PO QTY : [".$po_qty." ".$det[$i]['po_unit']."] BOM QTY [".$ap_qty." ".$det[$i]['po_unit']."] \r\r";
		}
	}
	echo $rtn_str;
	break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_apv_del":			job 51
# 刪除己核可採購單
# 需填寫異常報告
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "exc_overload":
		check_authority('037',"add");	

		$rec = $po->get($PHP_num);
		$ord_rec = array();
		if(isset($rec['ap_det']))
		{
			for($i=0; $i<sizeof($rec['ap_det']); $i++)
			{
				$mk = 0;
				for($j=0; $j<sizeof($ord_rec); $j++)
				{
					if($ord_rec[$j] == $rec['ap_det'][$i]['ord_num'])
					{
						$mk = 1;
						break;
					}
				}
				if($mk == 0) $ord_rec[] = $rec['ap_det'][$i]['ord_num'];

			}
		}
		$ord_num = '';
		for($j=0; $j<sizeof($ord_rec); $j++) $ord_num .= $ord_rec[$j].",";
		$det = $po->get_det_group($PHP_num);
		$rtn_str = '本P/O溢出BOM計算量'.chr(13).chr(10);
		$amount = $org_amt = 0;
		for($i=0; $i<sizeof($det); $i++)
		{
			$det[$i]['mat_des'] = $det[$i]['mat_name']."(".$det[$i]['mat_code'].") 使用於 ".$det[$i]['use_for'];
			$po_qty = $det[$i]['po_qty'];
			$ap_qty = $det[$i]['qty'] + ($det[$i]['qty'] * ($det[$i]['range'] / 100) );
			$ap_qty = change_unit_qty($det[$i]['unit'], $det[$i]['po_unit'],$ap_qty);
			$tmp_qty = explode('.',$ap_qty);
			if(isset($tmp_qty[1]) && $tmp_qty[1] > 0)$tmp_qty[0]++;
			$ap_qty = $tmp_qty[0];
			if($po_qty > $ap_qty)
			{
				$rtn_str .= '物料 : ['.$det[$i]['mat_des']."] : Color [".$det[$i]['mat_color']."]".chr(13).chr(10);
				$rtn_str .=" BOM 計算量 [".$ap_qty." ".$det[$i]['po_unit']."] 含[".$det[$i]['range']."%] loss ; PO 量 [".$po_qty." ".$det[$i]['po_unit']."],".chr(13).chr(10);
				$rtn_str .="多出 ".($po_qty - $ap_qty).$det[$i]['po_unit'].chr(13).chr(10);
				$amount += $det[$i]['amount'];				
				$prc = change_unit_price($det[$i]['prc_unit'],$det[$i]['po_unit'],$det[$i]['prics']);				
				$org_amt +=($ap_qty * $prc);
			}
		}
			
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
			// 取出選項資料 及傳入之參數
			$op['msg'][]= "Please write Exceptional for PO : [".$rec['ap']['po_num']."]";
			$op['cust_id'] = $rec['ap']['cust'];
			$op['dept_id']	 = $user_dept;
			$op['date'] = $TODAY;
			$op['exc']['ord_num'] = $ord_num;
			$op['exc']['po_num'] = $rec['ap']['po_num'];
			$op['comm']['dept'] = $rec['ap']['dept'];
			$op['rec']['bom_amount'] = $rec['ap']['currency']."$".NUMBER_FORMAT($org_amt,2);
			$op['rec']['po_amount'] = $rec['ap']['currency']."$".NUMBER_FORMAT($amount,2);
			$op['rec']['bom_qty'] = 1;
			$op['rec']['sys_reason'] = $rtn_str;
			
			
			// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
			$op['no_pm'] = 1;
			page_display($op,'069',$TPL_EXC_ADD);		
			break;		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_po_submit":
	check_authority('037',"edit");	


	$f1=$apply->update_2fields('status', 'po_sub_date', '8', $TODAY, $PHP_id);
	$f1=$apply->update_fields('po_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
		if ($f1)
	{
		$message="Successfully submit PO :".$PHP_po_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	

	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back_str=".$PHP_back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_match_pp":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_match_pp":

		check_authority('037',"view");

		// 將 製造令 完整show out ------
		//  wi 主檔
			if(!$op = $wi->get_all($PHP_wi_id)){    //取出該筆 製造令記錄 ID
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

		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------



		//  取出  BOM  預先採購記錄 --------------------------------------------------------------
		$op['pp_mat_NONE']= '';
		$op['pp_mat'] = $po->get_match_pp($op['wi']['wi_num'],$PHP_name,$PHP_mat_cat,$PHP_color);  //取出該筆 bom 內ALL加購記錄
		$num_ext_mat = count($op['pp_mat']);
		if (!$num_ext_mat){	$op['pp_mat_NONE'] = "1";}
		
		$op['org_bom_id'] = $PHP_bom_id;
		$op['org_mat_name'] = $PHP_name;
		$op['org_mat_cat'] = $PHP_mat_cat;
		$op['org_mat_unit'] = $PHP_mat_unit;
		$op['org_mat_qty'] = $PHP_mat_qty;		
		//$op['org_wi_id'] = $PHP_wi_id;
		$op['msg'] = $wi->msg->get(2);

		if (!isset($PHP_sr)) $PHP_sr =1;
		if (isset($PHP_msg)) $op['msg'][] = $PHP_msg;
		$back_str="?PHP_action=apply_wi_view&PHP_id=".$PHP_wi_id."&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
		$op['back_str']=$back_str;

		$back_str2="PHP_id=".$PHP_wi_id."&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
		$op['back_str2']=$back_str2;

		page_display($op, '037', $TPL_APLY_MATCH_PP);		

		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_aply_pp_match":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_aply_pp_match":
	check_authority('037',"view");

	$po_det = $po->get_special($PHP_po_id);
	$po_qty = change_unit_qty($po_det['unit'],$PHP_mat_unit,$po_det['ap_qty']);

	if(($po_qty-$po_accept_qty) > $PHP_mat_qty)
	{
		$redir_str = 'po.php?PHP_action=exc_prepo&PHP_num='.$po_det['ap_num']."&PHP_po_id=".$PHP_po_id."&PHP_bom_id=".$PHP_bom_id."&PHP_bom_qty=".$PHP_mat_qty."&PHP_bom_unit=".$PHP_mat_unit;
		redirect_page($redir_str);
	}

	if(isset($PHP_po_id))
	{
		$f1=$apply->update_fields_id('pp_mark','2', $PHP_po_id, 'ap_special');
		$where_str = "ap_special.id = '".$PHP_po_id."' ";
		$po_num = $po->get_det_field('ap_num','ap_special',$where_str);
		if($PHP_mat_cat == 'lots'){$bom_table = 'bom_lots';}else{$bom_table = 'bom_acc';}
		
		$bom_str = $bom->search($bom_table," where id = '".$PHP_bom_id."' ");
		$mark = !empty($bom_str[0]['ap_mark']) ? $bom_str[0]['ap_mark'].',' : '';
		
		$parm = array($PHP_bom_id, 'ap_mark', $mark.$po_num[0], $bom_table);
		$f1=$bom->update_field($parm);

		$parm = array($PHP_bom_id, 'pp_mark', '1', $bom_table);
		$f1=$bom->update_field($parm);	
		$PHP_msg = "Success Match Pre-Purchase PO#: ".$po_num[0];
		$log->log_add(0,"51M",$PHP_msg);
		$redir_str = 'index2.php?PHP_action=apply_wi_view&'.$PHP_back_str."&PHP_msg=".$PHP_msg;
		redirect_page($redir_str);
	}else{
		$PHP_msg = "Please choice one of pre-pruchase item ";
		$redir_str = 'index2.php?PHP_action=apply_match_pp&PHP_wi_id='.$PHP_id."&".$PHP_back_str."&PHP_msg=".$PHP_msg."&PHP_name=".$PHP_mat_name."&PHP_mat_cat=".$PHP_mat_cat."&PHP_bom_id=".$PHP_bom_id;
		redirect_page($redir_str);		
	}
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "exc_prepo":			job 51
# 預先採購量大於BOM數量
# 需填寫異常報告
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "exc_prepo":
		check_authority('037',"add");	

		$rec = $po->get($PHP_num);
		$ord_rec = array();
		if(isset($rec['ap_det']))
		{
			for($i=0; $i<sizeof($rec['ap_det']); $i++)
			{
				$mk = 0;
				for($j=0; $j<sizeof($ord_rec); $j++)
				{
					if($ord_rec[$j] == $rec['ap_det'][$i]['ord_num'])
					{
						$mk = 1;
						break;
					}
				}
				if($mk == 0) $ord_rec[] = $rec['ap_det'][$i]['ord_num'];

			}
		}
		$ord_num = '';
		for($j=0; $j<sizeof($ord_rec); $j++) $ord_num .= $ord_rec[$j].",";
		$det = $po->get_det_group($PHP_num);

		$rtn_str = '本P/O溢出BOM計算量'.chr(13).chr(10);
		$amount = $org_amt = 0;

		$po_det = $po->get_special($PHP_po_id);
		if($po_det['mat_cat'] == 'l'){
			$bom_table = 'bom_lots';
			$mat_det = $lots->get(0, $po_det['mat_code']);
			$po_det['mat_des'] = $mat_det['lots_name'].' ('.$po_det['mat_code'].') 使用於 '.$po_det['use_for'];
		}else{
			$bom_table = 'bom_acc';
			$mat_det = $acc->get(0, $po_det['mat_code']);
			$po_det['mat_des'] = $mat_det['acc_name'].' ('.$po_det['mat_code'].') 使用於 '.$po_det['use_for'];
		}
		
		
		$po_qty = change_unit_qty($po_det['unit'],$PHP_bom_unit,$po_det['po_qty']);
		$bom_qty = $PHP_bom_qty + ($PHP_bom_qty *( $po_det['range'] / 100));
		$prc = change_unit_price($po_det['prc_unit'],$PHP_bom_unit,$po_det['prics']);	
		$org_amt = ($PHP_bom_qty * $prc);
		//$rtn_str .= $po_det['mat_code'].": Color [".$po_det['color']."], PO QTY [".$po_det['ap_qty']." ".$po_det['unit']."] BOM QTY [".$PHP_bom_qty." ".$PHP_bom_unit."]".chr(13).chr(10);
		$rtn_str .= '物料 : '.$po_det['mat_des'].": Color [".$po_det['color']."]".chr(13).chr(10);
		$rtn_str .=" BOM 計算量 [".$bom_qty." ".$PHP_bom_unit."] 含[".$po_det['range']."%] loss ; PO 量 [".$po_det['po_qty']." ".$po_det['unit']."],".chr(13).chr(10);
		$rtn_str .="多出 ".($po_qty - $bom_qty).$PHP_bom_unit.chr(13).chr(10);




			
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
			// 取出選項資料 及傳入之參數
			$op['msg'][]= "Please write Exceptional for PO : [".$rec['ap']['po_num']."]";
			$op['cust_id'] = $rec['ap']['cust'];
			$op['dept_id']	 = $user_dept;
			$op['date'] = $TODAY;
			$op['exc']['ord_num'] = $po_det['ord_num'].',';
			$op['exc']['po_num'] = $rec['ap']['po_num'];
			$op['exc']['action'] = "PREPO|".$bom_table."|".$PHP_bom_id."|".$PHP_po_id."|".$rec['ap']['ap_num'];
			$op['comm']['dept'] = $rec['ap']['dept'];
			$op['rec']['bom_amount'] = $rec['ap']['currency']."$".NUMBER_FORMAT($org_amt,2);
			$op['rec']['po_amount'] = $rec['ap']['currency']."$".NUMBER_FORMAT($po_det['amount'],2);
			$op['rec']['bom_qty'] = 1;
			$op['rec']['sys_reason'] = $rtn_str;
			
			
			// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
			$op['no_pm'] = 1;
			page_display($op,'069',$TPL_EXC_ADD);		
			break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_ship_rmk":			job 54  採購出貨備註
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_ship_rmk":
		check_authority('037',"view");	
	$log_where = "AND item <> 'special'";	
	$op = $po->get($PHP_aply_num,$log_where);
	if (isset($op['ap_det']))
	{
				$op['ap_det2']=$po->grout_ap($op['ap_det']);
	}
	

	$op['back_str']=$PHP_back_str;
	
	$op['rmk_item'] = $arry2->select($PO_ITEM,'','PHP_item','select','');

	

	
	if (isset($PHP_message)) $op['msg'][]=$PHP_message;
	if (!isset($PHP_rev)) $PHP_rev='';
	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
	page_display($op, '037', $TPL_PO_SHIP_RMK);
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_ship_rmk":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_ship_rmk":
	check_authority('037',"edit");	


	foreach($PHP_des as $key => $value)
	{
		$parm = array('po_spare'		=>	$key,
								 	'ship_rmk'	=>	$value,
								 	'special'		=>	$PHP_special
								 );
		$po->ship_rmk($parm);
	}


	
	$message="Successfully update ship remark on PO :".$PHP_po_num;
	$op['msg'][]=$message;
	$log->log_add(0,"54E",$message);
	if($my_action=="po_ship_det")
	{
		//?PHP_action=po_ship_det&PHP_po=PA12-1837&mat_cat=a&special=2
		$redir_str = "po.php?PHP_action=po_ship_det&PHP_po=".$PHP_aply_num."&PHP_message=".$message;
	}
	else
	{
		$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_add_back=".$PHP_back_str;
	}

	//$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_add_back=".$PHP_back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_ship_rmk":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "get_ship_rmk":


	$rmk_str = $po->get_ship_rmk($PHP_ord_num,$PHP_cat);
	header('Content-Type:text/html;charset=big5');
	echo $rmk_str;
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_remain_report":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_remain_report":
		check_authority('037',"view");	
		if($PHP_from == 'search')
		{
			if($SCH_end == '' && $SCH_str == '' && $SCH_ord_num == '') 	$SCH_str =date('Y').'-01-01';

			$_SESSION['sch_parm'] = array();
			$_SESSION['sch_parm'] = array('ord_num'	=>	$SCH_ord_num, 'sch_str'	=>	$SCH_str,	'sch_end'	=>	$SCH_end);			
		}
		
		$rpt = $po->get_remain_fabric($_SESSION['sch_parm']);
		foreach($rpt as $key => $value)
		{
			$rpt[$key]['diff_qty'] = $rpt[$key]['po_qty'] - $rpt[$key]['ap_qty'];
			$fab_rec = $lots->get('',$rpt[$key]['mat_code']);
			$rpt[$key]['mat_des'] = $fab_rec['comp']." ".$fab_rec['specify'];
			$op['rpt'][] = $rpt[$key];
		}
//		$op['ord_num'] = $_SESSION['sch_parm']['ord_num'];
//		$op['sch_date'] = $_SESSION['sch_parm']['sch_date'];
		if($_SESSION['sch_parm']['ord_num'])$op['msg'][] = 'ORDER :['.$_SESSION['sch_parm']['ord_num'].']';
		if($_SESSION['sch_parm']['sch_str'])$op['msg'][] = 'DATE >['.$_SESSION['sch_parm']['sch_str'].']';
		if($_SESSION['sch_parm']['sch_end'])$op['msg'][] = 'DATE <['.$_SESSION['sch_parm']['sch_end'].']';
	page_display($op, '037', $TPL_PO_FAB_RPT);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_remain_report":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_remain_report_det":
		check_authority('037',"view");	
		
		$rpt = $po->get_remain_fabric_det($PHP_code,$PHP_color);
		foreach($rpt as $key => $value)
		{
			$rpt[$key]['diff_qty'] = $rpt[$key]['po_qty'] - $rpt[$key]['ap_qty'];
			if(file_exists("./picture/".$rpt[$key]['smpl_code'].".jpg")){
				$rpt[$key]['main_pic'] = "./picture/".$rpt[$key]['smpl_code'].".jpg";
			} else {
				$rpt[$key]['main_pic'] = "./images/graydot.gif";
			}
			$ord_rec = $order->get('',$rpt[$key]['smpl_code']);
			$rpt[$key]['etd'] = $ord_rec['etd'];
			$rpt[$key]['style'] = $ord_rec['style'];
			$rpt[$key]['qty'] = $ord_rec['qty'];
			$rpt[$key]['ord_id'] = $ord_rec['id'];
			
			$op['rpt'][] = $rpt[$key];
		}
		$op['fab_code'] = $PHP_code;
		$op['fab_name'] = $PHP_name;
		$op['fab_color'] = $PHP_color;
		
	
	page_display($op, '037', $TPL_PO_FAB_RPT_DET);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_stock":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_stock":
		check_authority('037',"add");		


			if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		
		if ($PHP_item == 'lots')
		{
			$where_str = "WHERE id = ". $PHP_mat_id;
			$mat=$bom->search('bom_lots', $where_str);

			$fd='bom_lots';	
			$mat_cat='l';		
		}else{			
			$where_str = "WHERE id = ". $PHP_mat_id;
			$mat=$bom->search('bom_acc', $where_str);		

			$fd='bom_acc';
			$mat_cat='a';				
		}
		for ($i=0; $i<sizeof($mat); $i++)
		{

			$parm_mat = array($mat[$i]['id'],'ap_mark','stock',$fd);
			$f2 = $bom->update_field($parm_mat);			
			// 沒這各欄位 mode 2011/03/18
			// $parm_mat = array($mat[$i]['id'],'st_qty',$PHP_st_qty,$fd);
			// $f2 = $bom->update_field($parm_mat);			
		}

	//記錄新增message
		$message = "Successfully ADD stock ON Order : ".$ord['wi_num'];
		//$op['msg'][] = $message;
		$log->log_add(0,"51A",$message);
				 
	$redirect_str ="index2.php?PHP_action=apply_wi_view&other=other&PHP_sr".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_msg=".$message."&PHP_id=".$PHP_wi_id."&PHP_full=".$PHP_full;

	
	redirect_page($redirect_str);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_track":採購總表 (採購總表新增 20120530)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_track":
check_authority('037',"view");
/* print_r($_GET); */
$DefaultDate=date('Y-m-d',mktime(0,0,0,date('m')-1,date('d'),date('Y')));
$myAction=$_GET['PHP_action'];
$MyOption=$_GET['PHP_FAB_ACC'];
$myGroup=$_GET['PHP_Po_Group'];
$mySupplier=$_GET['SCH_supl'];
$DateStart=$_GET['date_start'];
$DateEnd=$_GET['date_end'];
$PoNum=$_GET['SCH_Po_num'];

$q_str_header = "
select 
ap.id as po_id,
ap.ap_num,
ap.dept,
supl.supl_s_name,
ap.po_num,
ap.po_note,
ap.special,
ap_det.id as apdet_id,
ap_det.bom_id,
ap_det.mat_cat as apdetcat,
ap_det.po_eta as apdeteta,
ap_det.po_qty as apdetqty,
ap_det.po_unit as apdetunit,
ap_special.id as apspecial_id,
ap_special.ord_num,
ap_special.mat_cat as apspecialcat,
ap_special.po_eta as apspecialeta,
ap_special.po_qty as apspecialqty,
ap_special.po_unit as apspecialunit
from ap
left JOIN ap_det
on ap.ap_num = ap_det.ap_num 
left JOIN ap_special 
on ap.ap_num = ap_special.ap_num 
left JOIN supl 
on ap.sup_code = supl.vndr_no ";

$q_str_orderby = "order by ap.dept";
$gettoday=getdate();

if($MyOption == "init")
{
	$q_str_header="";
	$q_str_where="";
	$q_str_orderby="";
}
else
{
	if($MyOption == "")
	{
		if($myGroup == "")
		{
			if($mySupplier == "")
			{
				//全部的資料
				$q_str_where = "where (ap.rcv_rmk=0 and ap.status=12 ) ";
			}
			else
			{
				//只有供應商
				$q_str_where = "where (ap.rcv_rmk=0 and ap.status=12 )  and (ap.sup_code='".$mySupplier."') ";					
			}
		}
		else
		{
			if($mySupplier == "")
			{
	        	//只有業務別
				if($myGroup == "D")
				{
					$q_str_where = "where (ap.rcv_rmk=0 and ap.status=12 )  and (ap.dept like 'D%') ";
				}
				if($myGroup == "K")
				{
					$q_str_where = "where (ap.rcv_rmk=0 and ap.status=12 )  and (ap.dept like 'K%') ";
				}
			}
			else
			{
				//有供應商與業務別
				if($myGroup == "D")
				{
					$q_str_where = "where (ap.rcv_rmk=0 and ap.status=12 )  and (ap.sup_code='".$mySupplier."') and (ap.dept like 'D%') ";
				}
				if($myGroup == "K")
				{
					$q_str_where = "where (ap.rcv_rmk=0 and ap.status=12 )  and (ap.sup_code='".$mySupplier."') and (ap.dept like 'K%') ";
				}					
			}
		}

	}
	else
	{
		if($myGroup == "")
		{
			if($mySupplier == "")
			{
				//只有物料別
				if($MyOption == "FAB")
				{
					$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='l') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='l'))  ";
				}
				if($MyOption == "ACC")
				{
					$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='a') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='a'))  ";
				}
			}
			else
			{
				//有供應商與物料別
				if($MyOption == "FAB")
				{
					$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='l') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='l'))  and (ap.sup_code='".$mySupplier."') ";
				}
				if($MyOption == "ACC")
				{
					$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='a') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='a'))  and (ap.sup_code='".$mySupplier."') ";
				}
			}
		}
		else
		{
			if($mySupplier == "")
			{
				//有物料別與業務別
				if($MyOption == "FAB")
				{
					if($myGroup == "D")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='l') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='l'))  and (ap.dept like 'D%')  ";
					}
					if($myGroup == "K")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='l') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='l'))  and (ap.dept like 'K%')  ";
					}
					
				}
				if($MyOption == "ACC")
				{
					if($myGroup == "D")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='a') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='a'))  and (ap.dept like 'D%')  ";
					}
					if($myGroup == "K")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='a') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='a'))  and (ap.dept like 'K%')  ";
					}
					
				}
			}
			else
			{
				//三者都有(物料別、業務別與供應商)
				if($MyOption == "FAB")
				{
					if($myGroup == "D")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='l') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='l'))  and (ap.dept like 'D%')  and (ap.sup_code='".$mySupplier."') ";
					}
					if($myGroup == "K")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='l') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='l'))  and (ap.dept like 'K%')  and (ap.sup_code='".$mySupplier."') ";
					}
					
				}
				if($MyOption == "ACC")
				{
					if($myGroup == "D")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='a') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='a'))  and (ap.dept like 'D%')  and (ap.sup_code='".$mySupplier."') and (ap.dept like 'D%') ";
					}
					if($myGroup == "K")
					{
						$q_str_where = "where ((ap.rcv_rmk=0 and ap.status=12 and ap_det.mat_cat='a') or (ap.rcv_rmk=0 and ap.status=12 and ap_special.mat_cat='a'))  and (ap.dept like 'K%')  and (ap.sup_code='".$mySupplier."') and (ap.dept like 'K%') ";
					}
					
				}
			}
			
		}
	}
		
	if($DateStart=="")
	{
		if($DateEnd=="")
		{
			//沒有起始日與結束日
			if($PoNum=="")
			{
				if($PoNum=="")
				{
					$date=" and ((ap_det.po_eta >= '".$DefaultDate."') or (ap_special.po_eta >= '".$DefaultDate."')) ";
				}
			}
			else
			{
				$date="";
			}
			
		}
		else
		{
			//沒有起始日
			$date="and ((ap_det.po_eta <= '".$DateEnd."') or (ap_special.po_eta <= '".$DateEnd."')) ";
		}
		$q_str_where.=$date;
	}
	else
	{
		if($DateEnd=="")
		{
			//沒有結束日
			$date="and ((ap_det.po_eta >= '".$DateStart."') or (ap_special.po_eta >= '".$DateStart."')) ";
		}
		else
		{
			//有起始日與結束日
			$date="and ((ap_det.po_eta between '".$DateStart."' AND '".$DateEnd."') or (ap_special.po_eta between '".$DateStart."' AND '".$DateEnd."')) ";
		}
		$q_str_where.=$date;
	}
						
	if($PoNum!="")
	{
		$q_str_where .= "AND ap.po_num like '%".$PoNum."%'";
	}
	// echo $q_str_where;
	$op = $po->track_po_status($q_str_header,$q_str_where,$q_str_orderby,'po');
}
if($MyOption == "init")
{
    $op['FAB_ACC_select'] = $arry2->select($FAB_ACC,'','PHP_FAB_ACC','select','');/*第五個條件是onchange屬性*/
	$op['Group_select'] = $arry2->select($Po_Group,'','PHP_Po_Group','select','',$Po_Group_value);/*第五個條件是onchange屬性*/
	
}
else
{
	if($MyOption == "")
	{
	
		if($myGroup == "")
		{
			$op['FAB_ACC_select'] = $arry2->select($FAB_ACC,'','PHP_FAB_ACC','select','');/*第五個條件是onchange屬性*/
			$op['Group_select'] = $arry2->select($Po_Group,'','PHP_Po_Group','select','',$Po_Group_value);
		}
		else
		{
			$op['FAB_ACC_select'] = $arry2->select($FAB_ACC,'','PHP_FAB_ACC','select','');
			if($myGroup == "D")
			{
				$op['Group_select'] = $arry2->select($Po_Group,'D','PHP_Po_Group','select','',$Po_Group_value);
			}
			if($myGroup == "K")
			{
				$op['Group_select'] = $arry2->select($Po_Group,'K','PHP_Po_Group','select','',$Po_Group_value);
			}
		}

	}
	else
	{
		if($myGroup == "")
		{
			if($MyOption == "FAB")
			{
				$op['FAB_ACC_select'] = $arry2->select($FAB_ACC,'FAB','PHP_FAB_ACC','select','');
			}
			if($MyOption == "ACC")
			{
				$op['FAB_ACC_select'] = $arry2->select($FAB_ACC,'ACC','PHP_FAB_ACC','select','');
			}
			$op['Group_select'] = $arry2->select($Po_Group,'','PHP_Po_Group','select','',$Po_Group_value);/*第五個條件是onchange屬性*/
		}
		else
		{
			if($MyOption == "FAB")
			{
				$op['FAB_ACC_select'] = $arry2->select($FAB_ACC,'FAB','PHP_FAB_ACC','select','');
			}
			if($MyOption == "ACC")
			{
				$op['FAB_ACC_select'] = $arry2->select($FAB_ACC,'ACC','PHP_FAB_ACC','select','');
			}
			if($myGroup == "D")
			{
				$op['Group_select'] = $arry2->select($Po_Group,'D','PHP_Po_Group','select','',$Po_Group_value);
			}
			if($myGroup == "K")
			{
				$op['Group_select'] = $arry2->select($Po_Group,'K','PHP_Po_Group','select','',$Po_Group_value);
			}
			
		}

	}
}
foreach($op['po'] as $key=>$value)
{
	//echo "[".$key."]".$value['ap_num'];
	//echo "<br>";
	$op_tmp = $po->get($value['ap_num'],$log_where);
	//print_r($op_tmp);
	//一般採購
	$ship_ydqty=$ship_pcqty=$ship_meterqty=$ship_dzqty=$ship_SFqty=$ship_grossqty=$ship_kgqty=$ship_setqty=$ship_unitqty=$ship_lbqty=$ship_coneqty=$ship_inchqty=0;
	if (isset($op_tmp['ap_det']))
	{
		$op_tmp2['ap_det2']=$po->grout_ap($op_tmp['ap_det']);
		//$myunit="";
		foreach($op_tmp2['ap_det2'] as $key1=>$value1)
		{
			//$myunit==$value1['unit'];
			//echo $value1['unit'];
			//echo $value1['ship_qty_total'];
			if($value1['po_unit']=="yd")
			{
				$ship_ydqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="pc")
			{
				$ship_pcqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="meter")
			{
				$ship_meterqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="dz")
			{
				$ship_dzqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="SF")
			{
				$ship_SFqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="gross")
			{
				$ship_grossqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="kg")
			{
				$ship_kgqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="set")
			{
				$ship_setqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="unit")
			{
				$ship_unitqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="lb")
			{
				$ship_lbqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="cone")
			{
				$ship_coneqty+=$value1['ship_qty_total'];
			}
			if($value1['po_unit']=="inch")
			{
				$ship_inchqty+=$value1['ship_qty_total'];
			}
			
		}
		$op['po'][$key]['ship_ydqty']=$ship_ydqty;
		$op['po'][$key]['ship_pcqty']=$ship_pcqty;
		$op['po'][$key]['ship_meterqty']=$ship_meterqty;
		$op['po'][$key]['ship_dzqty']=$ship_dzqty;
		$op['po'][$key]['ship_SFqty']=$ship_SFqty;
		$op['po'][$key]['ship_grossqty']=$ship_grossqty;
		$op['po'][$key]['ship_kgqty']=$ship_kgqty;
		$op['po'][$key]['ship_setqty']=$ship_setqty;
		$op['po'][$key]['ship_unitqty']=$ship_unitqty;
		$op['po'][$key]['ship_lbqty']=$ship_lbqty;
		$op['po'][$key]['ship_coneqty']=$ship_coneqty;
		$op['po'][$key]['ship_inchqty']=$ship_inchqty;
	}
	//特殊採購
	
	if (isset($op_tmp['ap_spec']))
	{
		$op_tmp2['ap_spec2']=$po->grout_ap($op_tmp['ap_spec']);
		$myunit="";
		foreach($op_tmp2['ap_spec2'] as $key2=>$value2)
		{
			$myunit==$value2['unit'];
			if($value2['po_unit']=="yd")
			{
				$ship_ydqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="pc")
			{
				$ship_pcqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="meter")
			{
				$ship_meterqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="dz")
			{
				$ship_dzqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="SF")
			{
				$ship_SFqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="gross")
			{
				$ship_grossqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="kg")
			{
				$ship_kgqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="set")
			{
				$ship_setqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="unit")
			{
				$ship_unitqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="lb")
			{
				$ship_lbqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="cone")
			{
				$ship_coneqty+=$value2['ship_qty_total'];
			}
			if($value2['po_unit']=="inch")
			{
				$ship_inchqty+=$value2['ship_qty_total'];
			}
			
		}
		$op['po'][$key]['ship_ydqty']=$ship_ydqty;
		$op['po'][$key]['ship_pcqty']=$ship_pcqty;
		$op['po'][$key]['ship_meterqty']=$ship_meterqty;
		$op['po'][$key]['ship_dzqty']=$ship_dzqty;
		$op['po'][$key]['ship_SFqty']=$ship_SFqty;
		$op['po'][$key]['ship_grossqty']=$ship_grossqty;
		$op['po'][$key]['ship_kgqty']=$ship_kgqty;
		$op['po'][$key]['ship_setqty']=$ship_setqty;
		$op['po'][$key]['ship_unitqty']=$ship_unitqty;
		$op['po'][$key]['ship_lbqty']=$ship_lbqty;
		$op['po'][$key]['ship_coneqty']=$ship_coneqty;
		$op['po'][$key]['ship_inchqty']=$ship_inchqty;
	}
}
//print_r($op);

page_display($op,'037', $TPL_PO_STATUS_TRACK);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "po_close_pa":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_close_pa":
check_authority('037',"add");

$po->update_field_num('rcv_rmk','1',$PHP_po_num,'ap');

$message= "PO Number:[".$PHP_po_num."] was been closed";
# 記錄使用者動態
$log->log_add(0,"037C",$message);
echo $message;

break;
    
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_etd_edit:
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_etd_edit":

$rtn = $po->po_etd_edit($PHP_ap_num, $PHP_po_spare, $PHP_rev_etd, $PHP_special, $PHP_update_all);
$message = "success edit po_eta for ".str_replace("PA","PO",$PHP_ap_num);
$log->log_add(0,"037E",$message);
echo $rtn;
break;




# PP 合併特殊採購
case "po_pp_add_search":
check_authority('037',"view");

$op['dept_id'] = $PHP_dept;
$op['cust'] = $PHP_cust;
$op['item'] = $PHP_item;
$op['pa_num'] = $PHP_pa_num;
$op['ap_supl'] = $ap_supl;

page_display($op, '037', $TPL_PO_PP_ADD_SCH);
break;


# PP 合併特殊採購 Search
case "do_po_pp_add_search": //  先找出製造令.......
check_authority('037',"view");

if (!$op = $po->search_pp()) {
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

for ($i=0; $i< sizeof($op['wi']); $i++)
{
	$op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
	$op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
}

$op['msg']= $bom->msg->get(2);				
$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_pa_num=".$PHP_pa_num."&ap_supl=".$ap_supl;
$op['back_str']=$back_str;

$op['dept_id'] = $PHP_dept_code;
$op['cust'] = $PHP_cust;
$op['pa_num'] = $PHP_pa_num;
$op['item'] = $PHP_item;
$op['ap_supl'] = $ap_supl;

page_display($op, '037', $TPL_PO_PP_ADD_SCH);
break;



# PO VIEW
case "po_pp_sub_view":
check_authority('037',"view");

// print_r($_GET);
// print_r($_POST);
// echo $PHP_ap_num;

# 取出該筆 製造令記錄 ID
if(!$op = $order->get($PHP_id)){
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

if(isset($PHP_sr))
{
	$back_str="?PHP_action=do_po_pp_add_search&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_pa_num=".$PHP_pa_num."&PHP_item=".$PHP_item;
	$op['back_str']=$back_str;
}

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['order_num'].".jpg")){
	$op['pic_url'] = $style_dir.$op['order_num'].".jpg";
} else {
	$op['pic_url'] = $no_img;
}

if( $PHP_item == 'lots' || $PHP_item == 'l' ) {
	// 取出  BOM  主料記錄 --------------------------------------------------------------
	$op['bom_lots_list'] = $po->get_lots_use($op['order_num']);
} else {
	// 取出  BOM  副料記錄 --------------------------------------------------------------
	$op['bom_acc_list'] = $po->get_acc_use($op['order_num']);
}

$op['mat_cate'] = $PHP_item;
$op['item'] = $PHP_item;
$op['sup_code'] = $PHP_sup_code;
$op['ap_num'] = $PHP_pa_num;
if(isset($PHP_pa_num))
{
	$op['pa_num'] = $PHP_pa_num;
	$op['po_num'] = 'PO'.substr($PHP_pa_num,2);
}


$parm_madd= array(
	'cust'  		=>	$ord['cust'],
	'dept'			=>	$ord['dept'],
	'sup_code'		=>	'',
	'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'ap_date'		=>	$TODAY,
	'ap_special'	=>	3,
);

$op['ap'] = $parm_madd;
$op['wi_num'] = $PHP_ord_num;
$op['ap_supl'] = $ap_supl;

page_display($op, '037', $TPL_PO_VIEW_PP_SUB);		
break;

















# Extra 合併採購
case "po_ex_add_search":
check_authority('037',"view");
	
$op['dept_id'] = $PHP_dept;
$op['cust'] = $PHP_cust;
$op['item'] = $PHP_item;
$op['pa_num'] = $PHP_pa_num;

page_display($op, '037', $TPL_PO_EX_ADD_SCH);
break;



# Extra 合併採購 Search
case "do_po_ex_add_search": //  先找出製造令.......
check_authority('037',"view");

if (!$op = $bom->search_cfm()) {
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

for ($i=0; $i< sizeof($op['wi']); $i++)
{
	$op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
	$op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
}

$op['msg']= $bom->msg->get(2);				
$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_pa_num=".$PHP_pa_num;
$op['back_str']=$back_str;

$op['dept_id'] = $PHP_dept_code;
$op['cust'] = $PHP_cust;
$op['pa_num'] = $PHP_pa_num;
$op['item'] = $PHP_item;

page_display($op, '037', $TPL_PO_EX_ADD_SCH);
break;



# PO VIEW
case "po_wi_view":
check_authority('037',"view");
// print_r($_GET);
// print_r($_POST);
// 將 製造令 完整show out ------
// wi 主檔

if (!$op = $wi->get_all($PHP_id)) { //取出該筆 製造令記錄 ID
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

// 取出 size_scale ----------------------------------------------
$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];
	//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
$sizing = explode(",", $size_A['size']);

	// 做出 size table-------------------------------------------
$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
$op['show_breakdown'] = $reply['html'];
$op['total'] = $reply['total'];
//-----------------------------------------------------------------
// echo $PHP_item;
if( $PHP_item == 'l' ) {
	// 取出  BOM  主料記錄 --------------------------------------------------------------
	$op['lots_use'] = $po->get_lots_det($op['wi']['id']);
} else {
	// 取出  BOM  副料記錄 --------------------------------------------------------------
	$op['acc_use'] = $po->get_acc_det($op['wi']['id']);
}


//  取出  BOM  主料記錄 -------get_lots_det-------------------------------------------------------
// $op['lots_use'] = $po->get_lots_det($op['wi']['id'],1);  //取出該筆 bom 內ALL主料記錄
// print_r($op['lots_use']);
//  取出  BOM  副料記錄 --------------------------------------------------------------
// $op['acc_use'] = $po->get_acc_det($op['wi']['id'],1);  //取出該筆 bom 內ALL副料記錄

//  取出  BOM  工廠採購主料記錄 --------------------------------------------------------------
// $op['fty_lots_use'] = $apply->get_lots_det($op['wi']['id'],1);  //取出該筆 bom 內ALL主料記錄

//  取出  BOM  工廠採購副料記錄 --------------------------------------------------------------
// $op['fty_acc_use'] = $apply->get_acc_det($op['wi']['id'],1);  //取出該筆 bom 內ALL副料記錄

//  取出  BOM  客戶提供主料記錄 --------------------------------------------------------------
// $op['cust_lots_use'] = $apply->get_lots_det($op['wi']['id'],2);  //取出該筆 bom 內ALL主料記錄

//  取出  BOM  客戶提供副料記錄 --------------------------------------------------------------
// $op['cust_acc_use'] = $apply->get_acc_det($op['wi']['id'],2);  //取出該筆 bom 內ALL副料記錄

//  取出  BOM  預先採購記錄 --------------------------------------------------------------
$op['pp_mat'] = $po->get_unadd_pp($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄

for($i=0; $i<sizeof($op['pp_mat']); $i++)
{
	if ($op['pp_mat'][$i]['mat_cat'] =='a') $op['pp_acc_mk'] = 1;
	if ($op['pp_mat'][$i]['mat_cat'] =='l') $op['pp_lots_mk'] = 1;
}

// 取出  BOM  Disable計錄中己採購主料者 --------------------------------------------------------------
$op['dis_lots_NONE']= '';
$op['dis_lots'] = $bom->get_dis_lots($op['wi']['id']);  //取出該筆 bom 內記錄
$num_lots = count($op['dis_lots']);
if (!$num_lots){	$op['dis_lots_NONE'] = "1";}

// 取出  BOM  Disable計錄中己採購副料者 --------------------------------------------------------------
$op['dis_acc_NONE']= '';
$op['dis_acc'] = $bom->get_dis_acc($op['wi']['id']);  //取出該筆 bom 內記錄
$num_acc = count($op['dis_acc']);
if (!$num_acc){	$op['dis_acc_NONE'] = "1";}

$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];   // 判定進入身份的指標
if($user_team == 'PM') $op['fty_flag'] = 1;

$op['msg'] = $wi->msg->get(2);
if (isset($PHP_msg)) $op['msg'][] = $PHP_msg;
if (!isset($PHP_sr)) $PHP_sr =1;

$back_str="?PHP_action=do_po_ex_add_search&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_item=".$PHP_item."&PHP_pa_num=".$PHP_pa_num;
$op['item'] = $PHP_item;
$op['pa_num'] = $PHP_pa_num;
$op['back_str']=$back_str;
$op['id']=$PHP_id;
// print_r($op);
page_display($op, '037', $TPL_PO_VIEW_WI_SUB);		
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_ship_det:
# 取得PO的物料出貨細節
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_ship_det":

$PHP_aply_num=$_GET['PHP_po'];
$PHP_mat_cat=$_GET['mat_cat'];
$PHP_special=$_GET['special'];

$op = $po->get($PHP_aply_num,$log_where);
$op['update_msg']=$_GET['PHP_message'];

if (isset($op['ap_det']))
{
	$op['ap_det2']=$po->grout_ap($op['ap_det']);
}
if (isset($op['ap_spec']))
{
	$op['ap_spec2']=$po->grout_ap($op['ap_spec']);
}

if (isset($PHP_SCH_num))
{
	$back_str="?PHP_action=po_search&PHP_dept_code=".$PHP_dept_code."&SCH_del=".$SCH_del."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
} else if ( isset ($PHP_add_back) && $PHP_add_back ) {
	if (isset($op['ap_det2'][0]['ord_num']))
	{
		$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
	} else {
		$wi_id = $wi->get_field($op['ap_spec'][0]['ord_num'],'id');
	}
	$back_str="?PHP_action=po_bom_view&PHP_id=".$wi_id['id']."&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_sr_startno=";
} else {
	$back_str="?PHP_action=po_search&PHP_dept_code=&PHP_SCH_num=&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=&SCH_del=";
}

$op['back_str']=$back_str;
$op['rmk_item'] = $arry2->select($PO_ITEM,'','PHP_item','select','');

$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++) {
	if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
}

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

if (isset($PHP_message)) $op['msg'][]=$PHP_message;
if (!isset($PHP_rev)) $PHP_rev='';
if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

page_display($op, '037', $TPL_PO_TRACK_SHOW);
break;





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_ex_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_ex_view":
check_authority('037',"view");
// print_r($_GET);
$op['wi'] = $wi->get($PHP_id);
$op['smpl'] = $order->get($op['wi']['smpl_id']);

$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
$T_wiqty = $wiqty->search(1,$where_str);

$num_colors = count($T_wiqty);
$op['num_colors'] = $num_colors;

$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];

$sizing = explode(",", $size_A['size']);

// 做出 size table-------------------------------------------
$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
$op['show_breakdown'] = $reply['html'];
$op['total'] = $reply['total'];   // 總件數
//-----------------------------------------------------------------

// 相片的URL決定 ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
	$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
	$op['wi']['pic_url'] = $no_img;
}
// echo $PHP_bom_code;
if( $PHP_mat_cat == 'l' ) {
	// 取出  BOM  主料記錄 --------------------------------------------------------------
	$op['bom_lots_list'] = $po->get_lots_det($op['wi']['id']);
} else {
	// 取出  BOM  副料記錄 --------------------------------------------------------------
	$op['bom_acc_list'] = $po->get_acc_det($op['wi']['id']);
}
// print_r($op['bom_acc_list']);
if(isset($PHP_sr_startno))
{
	$back_str="?PHP_action=apply_search_bom&PHP_sr=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
	$op['back_str']=$back_str;
}

$op['mat_id'] = $PHP_bom_id;
$op['mat_cate'] = $PHP_mat_cat;
$op['mat_code'] = $PHP_bom_code;

if(isset($PHP_pa_num))
{
	$op['pa_num'] = $PHP_pa_num;
	$op['po_num'] = 'PO'.substr($PHP_pa_num,2);
	$ap_rec = $apply->get($PHP_pa_num);
	$op['ap_det'] = $ap_rec['ap_det'];
	$op['ap'] = $ap_rec['ap'];
}
// print_r($op);
page_display($op, '037', $TPL_APLY_EX_VIEW_BOM);
break;		






#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_ex_view": job 037 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_ex_view":
check_authority('037',"view");
// print_r($_GET);
// print_r($_POST);
// echo $PHP_ap_num;

$log_where = "AND item <> 'special'";	
$op = $po->get($PHP_aply_num,$log_where);

if (isset($op['ap_det'])) {
	$op['ap_det2']=$po->grout_ap($op['ap_det']);
}

if (isset($PHP_SCH_num)) {
	$back_str = "?PHP_action=po_search&PHP_dept_code=".$PHP_dept_code."&SCH_del=".$SCH_del."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
} else if ( isset ($PHP_add_back) && $PHP_add_back ) {
	if (isset($op['ap_det2'][0]['ord_num']))
	{
		$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
	} else {
		$wi_id = $wi->get_field($op['ap_spec'][0]['ord_num'],'id');
	}
	$back_str = "?PHP_action=po_bom_view&PHP_id=".$wi_id['id']."&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_sr_startno=";
} else {
	$back_str = "?PHP_action=po_search&PHP_dept_code=&PHP_SCH_num=&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=&SCH_del=";
}

$op['apb_flag'] = $po->check_po_reopen($op['ap']['ap_num']);
$op['back_str']=$back_str;
$op['rmk_item'] = $arry2->select($PO_ITEM,'','PHP_item','select','');

$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++) {
	if ($op['ap']['usance'] == $ary[$i])
		$op['ap']['usance'] = $usance[$i];
}

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way'] = $dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

if (isset($PHP_message)) $op['msg'][]=$PHP_message;
if (!isset($PHP_rev)) $PHP_rev='';
if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] = $op['ap']['revise']+1;

// print_r($op);
if ($PHP_rev == 1)
{
	page_display($op, '037', $TPL_PO_EX_SHOW_REV);
}else{
	page_display($op, '037', $TPL_PO_EX_SHOW);
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_ex_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_ex_edit":
check_authority('037',"edit");

$where_str="AND( item like '%special%')";		
$op = $po->get($PHP_aply_num,$where_str);
// print_r($op);
// 組合畫面List
if (isset($op['ap_det']))
{
	$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
}
// print_r($op['ap_det']);
$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");

for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
$op['back_str']=$PHP_back_str;


page_display($op, '037', $TPL_APLY_EX_EDIT);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_apply_ex_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apply_ex_edit":
check_authority('037',"add");	

$wi_id = $PHP_wi_id;
if ($PHP_ap_num == '')
{

	if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

	$parm_madd= array(
		'cust'			=>	$ord['cust'],
		'dept'			=>	$ord['dept'],
		'sup_code'		=>	$PHP_vendor,
		'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		'ap_date'		=>	$TODAY,
		'ap_special'	=>	3,
	);
	
	$ap_supl = $PHP_vendor;

	$head="PA".date('y')."-";	//A+日期+部門=請購單開頭
	$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//取得請購的最後編號
	$new_id = $apply->add($parm_madd);	//新增AP資料庫
	$PHP_ap_num=$parm_madd['ap_num'];
	//建po編號
	$tmp_num = substr($parm_madd['ap_num'],2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_ap_num);

} else {

	$po_num = "PO".substr($PHP_ap_num,2);
	
}

$parm_dadd = array( 
	'ap_num'	=>	$PHP_ap_num,
	'bom_id'	=>	$PHP_bom_id,
	'eta'		=>	$PHP_eta,
	'qty'		=>	$PHP_qty,
	'unit'		=>	$PHP_unit,
	'mat_cat'	=>	$PHP_mat_cat,

	'order_num'	=>	$PHP_order_num,
	'wi_id'	    =>	$wi_id,
	'mat_id'	=>	$PHP_mat_id,
	'used_id'	=>	$PHP_used_id,
	'color'	    =>	$PHP_color,
	'size'	    =>	$PHP_size,
);

$f1 = $po->add_pp($parm_dadd);	

$message = "Successfully appended PO : ".$po_num;
$op['msg'][] = $message;
$log->log_add(0,"037A",$message);

if( isset($PHP_id) )
	$wi_rec['id'] = $PHP_id;
else
	$wi_rec= $wi->get($PHP_wi_id);

if( isset($PHP_rev) )
$redir_str='po.php?PHP_action=apply_ex_revise&PHP_aply_num='.$PHP_ap_num.'&PHP_used_id='.$PHP_used_id."&PHP_bom_code=".$PHP_mat_code."&PHP_item=".$PHP_mat_cat."&PHP_pa_num=".$PHP_ap_num."&PHP_vendor=".$PHP_vendor."&PHP_ord_num=".$PHP_wi_num."&ap_supl=".$ap_supl."&PHP_rev=".$PHP_rev;
else
$redir_str='po.php?PHP_action=apply_ex_edit&PHP_aply_num='.$PHP_ap_num.'&PHP_used_id='.$PHP_used_id."&PHP_bom_code=".$PHP_mat_code."&PHP_item=".$PHP_mat_cat."&PHP_pa_num=".$PHP_ap_num."&PHP_vendor=".$PHP_vendor."&PHP_ord_num=".$PHP_wi_num."&ap_supl=".$ap_supl;
redirect_page($redir_str);




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_ex_revise":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_ex_revise":
check_authority('037',"edit");
// print_r($_GET);
$op = $po->get($PHP_aply_num);
// print_r($op['ap_det']);
if (isset($op['ap_det'])) {

	$op['ap_det2'][0] = $op['ap_det'][0];
	$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
    $op['ap_det2'][0]['s_etd'] = $op['ap_det2'][0]['o_etd'][0] = $order->get_partial_etd($op['ap_det'][0]['ord_num']);

	$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
    
	$units = $op['ap_det'][0]['unit'];
	if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

	$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
	$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');
    
    # print_r($op['ap_det']);
    
	$k=1;
	for ($i=1; $i<sizeof($op['ap_det']); $i++) {
		$mk=0;
		$x=1;	$order_add=0;
		for ($j=0; $j< sizeof($op['ap_det2']); $j++) {
			if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta']) {
                
				$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
				$op['ap_det2'][$j]['amount'] = $op['ap_det'][$i]['amount'] +$op['ap_det2'][$j]['amount'];
				$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
				for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++) {
					if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num']) {
						$order_add =1;
						break;
					}
				}
				if ($order_add == 0){
                   
                    $op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
                    $op['ap_det2'][$j]['s_etd'] = $op['ap_det2'][$j]['o_etd'][] = $order->get_partial_etd($op['ap_det'][$i]['ord_num']);
                    
                }
				$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
				$x++;
				$mk = 1;
			}
		}
		
		if ($mk == 0) {

			$op['ap_det2'][$k] = $op['ap_det'][$i];
            
			$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
            $op['ap_det2'][$k]['s_etd'] = $op['ap_det2'][$k]['o_etd'][0] =  $order->get_partial_etd($op['ap_det'][$i]['ord_num']);
            
			$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
			$units = $op['ap_det'][$i]['unit'];
			if ($op['ap_det'][$i]['prc_unit']){$prc_units = $op['ap_det'][$i]['prc_unit']; }else{$prc_units = $op['ap_det'][$i]['unit'];}
			$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
			$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');
			$k++;

		}
	}
}

if (isset($op['ap_spec'])) {
	for ($i=0; $i<sizeof($op['ap_spec']); $i++) {
		$units = $op['ap_spec'][$i]['unit'];
		if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}
		$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
		$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');
        $op['ap_spec'][$i]['s_etd'] = $op['ap_spec'][$i]['o_etd'] =  $order->get_partial_etd($op['ap_spec'][$i]['ord_num']);
	}
}

$tmp_num = substr($PHP_ap_num,2);
$po_num = "PO".$tmp_num;

$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
$op['FINL_select'] = $arry2->select($DIST,$op['ap']['finl_dist'],'PHP_finl_dist','select',"finl_show(this)");

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$re_dm_way = re_dm_way($op['ap']['dm_way']);
if ( $re_dm_way[0] == 2 ){
    $op['ap']['dm_way'] = 'Input T/T of shipment separately the BEFORE & AFTER';
    $op['ap']['before'] = $re_dm_way['before'];
    $op['ap']['after'] = $re_dm_way['after'];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","chk_dm_way()");
$op['ap']['special'] = $PHP_new_special;
$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');

$op['revise'] = 1;
// print_r($op);
page_display($op, '037', $TPL_PO_EX_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_ex_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_ex_submit":
check_authority('037',"edit");	

if(isset($PHP_CURRENCY))
{
    if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

    $apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
    
    # 修改 TT 付款
    if ( !empty($PHP_before) && !empty($PHP_after) ) {
        $PHP_dmway = $PHP_before .'|'. $PHP_after;
    }
    
    $apply->update_2fields('dm_way','finl_dist',$PHP_dmway,$PHP_finl_dist,$PHP_id);	//Update 幣別+final distinction

    $parm = array(
        'field_name'    =>	'dm_way',
        'field_value'	=>	$PHP_dmway,
        'id'            =>	$PHP_sup_id
    );
    
    $supl->update_field($parm);

    $parm_log = array(
        'ap_num'	=>	$PHP_aply_num,
        'item'		=>	'special',
        'des'       =>	$PHP_des,
        'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
        'k_date'	=>	$TODAY,
    );
    
    if ($PHP_des <> $PHP_old_des && $PHP_des <> 'no_des')
    {
        $f1=$apply->add_log($parm_log);		
    }

}

$f1=$apply->update_2fields('fob', 'fob_area', $PHP_fob, $PHP_fob_area, $PHP_id); // Price term

$tmp_num = substr($PHP_aply_num,2);
$po_num = "PO".$tmp_num;
$f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);


$f1=$apply->update_2fields('status', 'po_sub_date', '8', $TODAY, $PHP_id);
$f1=$apply->update_fields('po_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);


$PHP_po_num = "PO".substr($PHP_aply_num,2);
if ($f1) {
    $message="Successfully submit PO :".$PHP_po_num;
    $op['msg'][]=$message;
    $log->log_add(0,"037E",$message);
}else{
    $op['msg']= $apply->msg->get(2);
    $message=$op['msg'][0];
}

$redir_str = "po.php?PHP_action=po_ex_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_add_back=".$PHP_add_back;
redirect_page($redir_str);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_ex_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_ex_revise":
check_authority('037',"edit");

$f1=$apply->update_2fields('apv_user', 'apv_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('cfm_user', 'cfm_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('submit_user', 'submit_date','','0000-00-00', $PHP_id);

$f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $PHP_id);

$f1=$apply->update_fields('status',6, $PHP_aply_num);
$f1=$apply->update_fields('revise',($PHP_rev_time+1), $PHP_aply_num);		

$where_str="AND( item like '%special%')";		
$op = $po->get($PHP_aply_num,$where_str);
			
//組合畫面List
if (isset($op['ap_det']))
{
	$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
}

$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($FACTORY,$op['ap']['arv_area'],'PHP_FACTORY','select','');
for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

$op['back_str']='';
$op['revise']=1;
// print_r($op);
page_display($op, '037', $TPL_APLY_EX_EDIT);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_apply_match_ex":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apply_match_ex":
check_authority('037',"view");

// 'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],	

$parm = array(
	'ap_id'		=>	$PHP_ap_id,
	'wi_id'		=>	$PHP_wi_id,
	'used_id'   =>	$PHP_used_id,
	'mat_cat'   =>	$PHP_mat_cat,
	'color'     =>	$PHP_color,
	'qty'       =>	$PHP_qty,
	'ap_num'    =>	$PHP_ap_num,
	'size'      =>	$PHP_size,
	'k_date'	=>	$TODAY
);

if( $bom_id = $po->add_bom_by_po($parm) ) {

	$_SESSION['msg'] = "Success Match Pre-Purchase PO#: PO".substr($PHP_ap_num,2);

} else {

	$_SESSION['msg'] = "ERROR !!!  Pre-Purchase PO#: PO".substr($PHP_ap_num,2);
	
}

$redir_str = 'index2.php?PHP_action=apply_wi_view&PHP_id='.$PHP_wi_id."&".$PHP_back_str;
redirect_page($redir_str);		
break;

#*********************************************************************************************************************



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_pp_view": job 037 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_pp_view":
check_authority('037',"view");
// print_r($_GET);
// print_r($_POST);
// echo $PHP_ap_num;

$op = $po->get_pp($PHP_aply_num);

if (isset($op['ap_det'])) {
	$op['ap_det2']=$po->grout_ap($op['ap_det']);
}

if (isset($PHP_SCH_num)) {
	$back_str = "?PHP_action=po_search&PHP_dept_code=".$PHP_dept_code."&SCH_del=".$SCH_del."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
} else if ( isset ($PHP_add_back) && $PHP_add_back ) {
	if (isset($op['ap_det2'][0]['ord_num']))
	{
		$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
	} else {
		$wi_id = $wi->get_field($op['ap_spec'][0]['ord_num'],'id');
	}
	$back_str = "?PHP_action=po_bom_view&PHP_id=".$wi_id['id']."&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_sr_startno=";
} else {
	$back_str = "?PHP_action=po_search&PHP_dept_code=&PHP_SCH_num=&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=&SCH_del=";
}

$op['back_str']=$back_str;
$op['rmk_item'] = $arry2->select($PO_ITEM,'','PHP_item','select','');

$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++) {
	if ($op['ap']['usance'] == $ary[$i])
		$op['ap']['usance'] = $usance[$i];
}

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way'] = $dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

if (isset($PHP_message)) $op['msg'][]=$PHP_message;
if (!isset($PHP_rev)) $PHP_rev='';
if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] = $op['ap']['revise']+1;


if ($PHP_rev == 1)
{
	page_display($op, '037', $TPL_PO_PP_SHOW_REV);
}else{
	page_display($op, '037', $TPL_PO_PP_SHOW);
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_pp_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_pp_edit":
check_authority('037',"edit");

$where_str="AND( item like '%special%')";		
$op = $po->get_pp($PHP_aply_num,$where_str);
// print_r($op);
// 組合畫面List
if (isset($op['ap_det']))
{
	$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
}
// print_r($op['ap_det']);
$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");

for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
$op['back_str']=$PHP_back_str;

// print_r($op);
page_display($op, '037', $TPL_APLY_PP_EDIT);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_apply_pp_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apply_pp_edit":
check_authority('037',"add");	

if ($PHP_ap_num == '')
{

	if(!$ord = $wi->get(0,$PHP_wi_num)){    //取出該筆 製造令記錄 ID
		$op['msg']= $wi->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

	$parm_madd= array(
		'cust'			=>	$ord['cust'],
		'dept'			=>	$ord['dept'],
		'sup_code'		=>	$PHP_vendor,
		'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		'ap_date'		=>	$TODAY,
		'ap_special'	=>	3,
	);
	
	$ap_supl = $PHP_vendor;

	$head="PA".date('y')."-";	//A+日期+部門=請購單開頭
	$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//取得請購的最後編號
	$new_id = $apply->add($parm_madd);	//新增AP資料庫
	$PHP_ap_num=$parm_madd['ap_num'];
	//建po編號
	$tmp_num = substr($parm_madd['ap_num'],2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_ap_num);
	
} else {

	$po_num = "PO".substr($PHP_ap_num,2);
	
}

$parm_dadd = array( 
	'ap_num'	=>	$PHP_ap_num,
	'used_id'	=>	$PHP_used_id,
	'qty'		=>	$PHP_qty,
	'color'		=>	$PHP_color,
	'unit'		=>	$PHP_unit,
	'mat_cat'	=>	$PHP_mat_cat
);

$f1 = $po->add_pp($parm_dadd);	

$message = "Successfully appended PO : ".$po_num;
$op['msg'][] = $message;
$log->log_add(0,"037A",$message);

if( isset($PHP_id) )
	$wi_rec['id'] = $PHP_id;
else
	$wi_rec= $wi->get(0,$PHP_wi_num);


if( isset($PHP_rev) )
$redir_str='po.php?PHP_action=apply_pp_revise&PHP_aply_num='.$PHP_ap_num.'&PHP_used_id='.$PHP_used_id."&PHP_bom_code=".$PHP_mat_code."&PHP_item=".$PHP_mat_cat."&PHP_pa_num=".$PHP_ap_num."&PHP_vendor=".$PHP_vendor."&PHP_ord_num=".$PHP_wi_num."&ap_supl=".$ap_supl."&PHP_rev=".$PHP_rev;
else
$redir_str='po.php?PHP_action=apply_pp_edit&PHP_aply_num='.$PHP_ap_num.'&PHP_used_id='.$PHP_used_id."&PHP_bom_code=".$PHP_mat_code."&PHP_item=".$PHP_mat_cat."&PHP_pa_num=".$PHP_ap_num."&PHP_vendor=".$PHP_vendor."&PHP_ord_num=".$PHP_wi_num."&ap_supl=".$ap_supl;
redirect_page($redir_str);




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_pp_revise":			job 037
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_pp_revise":
check_authority('037',"edit");
// print_r($_GET);
$op = $po->get_pp($PHP_ap_num);
// print_r($op['ap_det']);
if (isset($op['ap_det'])) {

	$op['ap_det2'][0] = $op['ap_det'][0];
	$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
    $op['ap_det2'][0]['s_etd'] = $op['ap_det2'][0]['o_etd'][0] = $order->get_partial_etd($op['ap_det'][0]['ord_num']);

	$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
    
	$units = $op['ap_det'][0]['unit'];
	if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

	$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
	$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');
    
    # print_r($op['ap_det']);
    
	$k=1;
	for ($i=1; $i<sizeof($op['ap_det']); $i++) {
		$mk=0;
		$x=1;	$order_add=0;
		for ($j=0; $j< sizeof($op['ap_det2']); $j++) {
			if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta']) {
                
				$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
				$op['ap_det2'][$j]['amount'] = $op['ap_det'][$i]['amount'] +$op['ap_det2'][$j]['amount'];
				$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
				for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++) {
					if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num']) {
						$order_add =1;
						break;
					}
				}
				if ($order_add == 0){
                   
                    $op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
                    $op['ap_det2'][$j]['s_etd'] = $op['ap_det2'][$j]['o_etd'][] = $order->get_partial_etd($op['ap_det'][$i]['ord_num']);
                    
                }
				$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
				$x++;
				$mk = 1;
			}
		}
		
		if ($mk == 0) {

			$op['ap_det2'][$k] = $op['ap_det'][$i];
            
			$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
            $op['ap_det2'][$k]['s_etd'] = $op['ap_det2'][$k]['o_etd'][0] =  $order->get_partial_etd($op['ap_det'][$i]['ord_num']);
            
			$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
			$units = $op['ap_det'][$i]['unit'];
			if ($op['ap_det'][$i]['prc_unit']){$prc_units = $op['ap_det'][$i]['prc_unit']; }else{$prc_units = $op['ap_det'][$i]['unit'];}
			$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
			$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');
			$k++;

		}
	}
}

if (isset($op['ap_spec'])) {
	for ($i=0; $i<sizeof($op['ap_spec']); $i++) {
		$units = $op['ap_spec'][$i]['unit'];
		if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}
		$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
		$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');
        $op['ap_spec'][$i]['s_etd'] = $op['ap_spec'][$i]['o_etd'] =  $order->get_partial_etd($op['ap_spec'][$i]['ord_num']);
	}
}

$tmp_num = substr($PHP_ap_num,2);
$po_num = "PO".$tmp_num;

$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
$op['FINL_select'] = $arry2->select($DIST,$op['ap']['finl_dist'],'PHP_finl_dist','select',"finl_show(this)");

for ($i=0; $i< 4; $i++) {
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$re_dm_way = re_dm_way($op['ap']['dm_way']);
if ( $re_dm_way[0] == 2 ){
    $op['ap']['dm_way'] = 'Input T/T of shipment separately the BEFORE & AFTER';
    $op['ap']['before'] = $re_dm_way['before'];
    $op['ap']['after'] = $re_dm_way['after'];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","chk_dm_way()");
$op['ap']['special'] = $PHP_new_special;
$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');

$op['revise'] = 1;

page_display($op, '037', $TPL_PO_PP_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "po_pp_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_pp_submit":
check_authority('037',"edit");	

if(isset($PHP_CURRENCY))
{
    if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

    $apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
    
    # 修改 TT 付款
    if ( !empty($PHP_before) && !empty($PHP_after) ) {
        $PHP_dmway = $PHP_before .'|'. $PHP_after;
    }
    
    $apply->update_2fields('dm_way','finl_dist',$PHP_dmway,$PHP_finl_dist,$PHP_id);	//Update 幣別+final distinction

    $parm = array(
        'field_name'    =>	'dm_way',
        'field_value'	=>	$PHP_dmway,
        'id'            =>	$PHP_sup_id
    );
    
    $supl->update_field($parm);

    $parm_log = array(
        'ap_num'	=>	$PHP_aply_num,
        'item'		=>	'special',
        'des'       =>	$PHP_des,
        'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
        'k_date'	=>	$TODAY,
    );
    
    if ($PHP_des <> $PHP_old_des && $PHP_des <> 'no_des')
    {
        $f1=$apply->add_log($parm_log);		
    }

}

$f1=$apply->update_2fields('fob', 'fob_area', $PHP_fob, $PHP_fob_area, $PHP_id); // Price term

$tmp_num = substr($PHP_aply_num,2);
$po_num = "PO".$tmp_num;
$f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);


$f1=$apply->update_2fields('status', 'po_sub_date', '8', $TODAY, $PHP_id);
$f1=$apply->update_fields('po_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);


$PHP_po_num = "PO".substr($PHP_aply_num,2);
if ($f1) {
    $message="Successfully submit PO :".$PHP_po_num;
    $op['msg'][]=$message;
    $log->log_add(0,"037E",$message);
}else{
    $op['msg']= $apply->msg->get(2);
    $message=$op['msg'][0];
}

$redir_str = "po.php?PHP_action=po_pp_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_add_back=".$PHP_add_back;
redirect_page($redir_str);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_pp_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apply_pp_revise":
check_authority('037',"edit");

$f1=$apply->update_2fields('apv_user', 'apv_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('cfm_user', 'cfm_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('submit_user', 'submit_date','','0000-00-00', $PHP_id);

$f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $PHP_id);
$f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $PHP_id);

$f1=$apply->update_fields('status',6, $PHP_aply_num);
$f1=$apply->update_fields('revise',($PHP_rev_time+1), $PHP_aply_num);		

$where_str="AND( item like '%special%')";		
$op = $po->get_pp($PHP_aply_num,$where_str);
			
//組合畫面List
if (isset($op['ap_det']))
{
	$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
}

$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
$op['FACTORY_select'] = $arry2->select($FACTORY,$op['ap']['arv_area'],'PHP_FACTORY','select','');
for ($i=0; $i< 4; $i++)
{
	if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

$op['back_str']='';
$op['revise']=1;
// print_r($op);
page_display($op, '037', $TPL_APLY_PP_EDIT);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_apply_match_pp":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apply_match_pp":
check_authority('037',"view");

// 'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],	

$parm = array(
	'ap_id'		=>	$PHP_ap_id,
	'wi_id'		=>	$PHP_wi_id,
	'used_id'   =>	$PHP_used_id,
	'mat_cat'   =>	$PHP_mat_cat,
	'color'     =>	$PHP_color,
	'qty'       =>	$PHP_qty,
	'ap_num'    =>	$PHP_ap_num,
	'size'      =>	$PHP_size,
	'k_date'	=>	$TODAY
);

if( $bom_id = $po->add_bom_by_po($parm) ) {

	$_SESSION['msg'] = "Success Match Pre-Purchase PO#: PO".substr($PHP_ap_num,2);

} else {

	$_SESSION['msg'] = "ERROR !!!  Pre-Purchase PO#: PO".substr($PHP_ap_num,2);
	
}

$redir_str = 'index2.php?PHP_action=apply_wi_view&PHP_id='.$PHP_wi_id."&".$PHP_back_str;
redirect_page($redir_str);		
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "upload_file":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_file":
		check_authority('037',"add");
		
		$S_ROOT = dirname(__FILE__);
		$Flag = substr(($S_ROOT),2,1);
		
		$filename = $_FILES['PHP_pi_file']['name'];		
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
		$upload = new Upload;																
		if ($_FILES['PHP_pi_file']['size'] < $upload->max_file_size && $_FILES['PHP_pi_file']['size'] > 0)
		{
		if ($f_check == 1){   // 上傳檔的副檔名為有效時 -----

			// upload pattern file to server
			$today = $GLOBALS['TODAY'];
			$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
			$parm = array(	"po_id"		=>  $PHP_id,
							"file_des"	=>	$PHP_desp,
							"file_user"	=>	$user_name,
							"file_date"	=>	$today
						);
			
			$A = $fils->get_name_id('ap_file');
			$pttn_name = $PHP_ap_num."_".$A.".".$ext;  // 組合檔名
			$parm['file_name'] = $pttn_name;
			
			$str_long=strlen($pttn_name);

			$upload->uploadFile($S_ROOT.$Flag.'pi_file'.$Flag, 'other', 16, $pttn_name );

			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$A = $fils->upload_pi_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}			
			$message = "UPLOAD file of : ".$PHP_num;
			$log->log_add(0,"53E",$message);
		} else {  // 上傳檔的副檔名  是  exe 時 -----

			$message = "upload file is incorrect format ! Please re-send.";
		}
		}else{  //上傳檔名重覆時
			$message = "Upload file is too big";
		}
	
		}else{
			$message="You don't pick any file or add file descript.";
		}	
			
		redirect_page($PHP_SELF."?PHP_action=po_view&PHP_aply_num=".$PHP_ap_num);
		
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "file_del":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "file_del":
	check_authority('037',"add");
	$t = $po->file_del($PHP_file_id);
	if($t) $log->log_add(0,"039D","delete pi file on ap_file id:$PHP_file_id");
	redirect_page($PHP_SELF."?PHP_action=po_view&PHP_aply_num=".$PHP_ap_num);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_revise":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_revise":
		check_authority('037',"edit");		
	$f1=$apply->update_2fields('apv_user', 'apv_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('cfm_user', 'cfm_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('submit_user', 'submit_date','','0000-00-00', $PHP_id);

	$f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $PHP_id);


	$f1=$apply->update_fields('status',6, $PHP_aply_num);
	$f1=$apply->update_fields('revise',($PHP_rev_time+1), $PHP_aply_num);		

	$where_str="AND( item like '%special%')";		
	$op = $apply->get($PHP_aply_num,$where_str);
				
//組合畫面List
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
		}

/*
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
*/		
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($FACTORY,$op['ap']['arv_area'],'PHP_FACTORY','select','');
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] === $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

		$op['back_str']='';
		$op['revise']=1;
		page_display($op, '037', $TPL_APLY_EDIT);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "unlock_apb":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "unlock_apb":
check_authority('037',"add");
echo $po->unlock_apb($PHP_ap_id,$PHP_ap_num);
break;

		
}   // end case ---------

?>
