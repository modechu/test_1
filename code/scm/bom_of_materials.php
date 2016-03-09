<?php

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
require_once($config['root_dir']."/lib/class.monitor.php");
$monitor = new MONITOR();
$monitor->init($mysql);

include_once($config['root_dir']."/lib/bom_of_materials.class.php");
$BOM_OF_MATERIALS = new BOM_OF_MATERIALS();
if (!$BOM_OF_MATERIALS->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
//BOM OF MATERIALS
# BOM OF MATERIALS 2013-05-28
include_once($config['root_dir']."/lib/utf8_materials.class.php");
$UTF8_MATERIALS = new UTF8_MATERIALS();
if (!$UTF8_MATERIALS->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}
#
#
#
$TPL_BOM_OF_MATERIALS_MAIN = "bom_of_materials_main.html";
$TPL_BOM_OF_MATERIALS_LIST = "bom_of_materials_list.html";
$TPL_BOM_OF_MATERIALS_VIEW = "bom_of_materials_view.html";
$TPL_BOM_OF_MATERIALS_DETAIL = "bom_of_materials_detail.html";
$TPL_BOM_OF_MATERIALS_EDIT = "bom_of_materials_edit.html";

$TPL_BOM_MAT_RELEASE_SEARCH = "bom_mat_release_search.html";
$TPL_BOM_MAT_RELEASE_LIST = "bom_mat_release_list.html";
$TPL_BOM_REQU_VIEW = "bom_requ_view.html";

$TPL_REQUI_NOTIFY_LIST = "requi_notify_list.html";
$TPL_REQUI_NOTIFY_SHOW = "requi_notify_show.html";
$TPL_REQUI_NOTIFY_EDIT = "requi_notify_edit.html";

$TPL_REQUI_NOTIFY_CFM_LIST = "requi_notify_cfm_list.html";
$TPL_REQUI_NOTIFY_CFM_SHOW = "requi_notify_cfm_show.html";
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '088';
#
#
#
// echo $PHP_action.'<br>';
switch ($PHP_action) {
#
#
#
case "main":
check_authority($AUTH,"view");

if(in_array($_SESSION['SCACHE']['ADMIN']['dept'], $FACTORY)){
    $op['fty_select'] = $arry2->select(array($_SESSION['SCACHE']['ADMIN']['dept']), $_SESSION['SCACHE']['ADMIN']['dept'], "PHP_factory", "styled-select","");
}else{
    $op['fty_select'] = $arry2->select($FACTORY, '', "PHP_factory", "styled-select","");
}
    
$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' );

page_display($op,$AUTH,$TPL_BOM_OF_MATERIALS_MAIN);
break;
#
#
#
case "search_bom_of_materials":
check_authority($AUTH,"view");

$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

$table = "s_order,wi,cust";
$m_sql= "s_order.order_num,cust.cust_init_name,s_order.creator,s_order.etd,s_order.pdc_status,s_order.pdc_cut_sub_user,s_order.pdc_cut_sub_date,s_order.pdc_bom_sub_user,s_order.pdc_bom_sub_date,s_order.factory,wi.id";
if( $factory || $ord_num ) {
    $where_str = "WHERE s_order.order_num = wi.wi_num and s_order.pdc_status >= '2' and wi.cust = cust.cust_s_name and wi.cust_ver = cust.ver ";
    $where_str .= !empty($factory) ? " AND `s_order`.`factory` = '".$factory."' " : "";
    $where_str .= !empty($ord_num) ? " AND `s_order`.`order_num` LIKE '%".$ord_num."%' " : "";
    unset($_SESSION['PAGE'][$PHP_action]);
} else {
    if( !empty($_SESSION['PAGE'][$PHP_action]['where_str']) ){
        $where_str = $_SESSION['PAGE'][$PHP_action]['where_str'];
    } else {
        header("Location: ".$PHP_SELF."?PHP_action=main");
    }
}
$op = $Search->page_sorting($table,$m_sql,$PHP_action,$where_str,10);
foreach($op['pages'] as $k => $v){
    $op['pages'][$k]['pdc_bom_sub_user'] = big52uni(get_user_html($v['pdc_bom_sub_user']));
}

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' );			

page_display($op,$AUTH,$TPL_BOM_OF_MATERIALS_LIST);
break;



case "view_bom_of_materials":
check_authority($AUTH,"view");

$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if(!$op = $order->get_fields_array('id,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_bom_sub_user,pdc_bom_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $op['msg'] = $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op = $op[0];

$size = $size_des->get($op['size']);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);
$HTML = $bom->get_wiqty_qty_view($ord_num,$size);
$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];
$bom_utf8 = $UTF8_MATERIALS->get_bom_materials($ord_num);
$op['BHTML'] = $BOM_OF_MATERIALS->get_bom_view($PHP_id,$size,$HTML[2],$bom_utf8);
$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;

$op['pdc_bom_sub_user'] = big52uni(get_user_html($op['pdc_bom_sub_user']));

$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op,$AUTH,$TPL_BOM_OF_MATERIALS_VIEW);		    	    
break;



case "detail_bom_of_materials":
check_authority($AUTH,"view");

$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if(!$op = $order->get_fields_array('id,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_bom_sub_user,pdc_bom_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $op['msg'] = $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op = $op[0];

$size = $size_des->get($op['size']);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);
$HTML = $bom->get_wiqty_qty_view($ord_num,$size);
$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];
$bom_utf8 = $UTF8_MATERIALS->get_bom_materials($ord_num);
$op['BHTML'] = $BOM_OF_MATERIALS->get_bom_detail($PHP_id,$size,$HTML[2],$bom_utf8);
$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;

$op['pdc_bom_sub_user'] = big52uni(get_user_html($op['pdc_bom_sub_user']));

$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op,$AUTH,$TPL_BOM_OF_MATERIALS_DETAIL);		    	    
break;



case "edit_bom_of_materials":
check_authority($AUTH,"add");

$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if(!$op = $order->get_fields_array('id,factory,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_bom_sub_user,pdc_bom_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $op['msg'] = $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op = $op[0];

$size = $size_des->get($op['size']);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);

$bom_utf8 = $UTF8_MATERIALS->get_bom_materials($ord_num);
$HTML = $BOM_OF_MATERIALS->get_wiqty_qty_view($ord_num,$size);
$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];
$op['BHTML'] = $BOM_OF_MATERIALS->get_bom_edit($PHP_id,$size,$HTML[2],$bom_utf8);
$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;

$op['pdc_bom_sub_user'] = big52uni(get_user_html($op['pdc_bom_sub_user']));

$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op,$AUTH, $TPL_BOM_OF_MATERIALS_EDIT);	    
break;



case "do_bom_update":
check_authority($AUTH,"add");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];

foreach($PHP_lots_name as $bom_id => $bom_name){
    $lots_parm = array(
        "po_bom_id"	=>	$PHP_lots_po_bom_id[$bom_id],
        "ord_num"	=>	$ord_num,
        "fty"		=>	$factory,
        "mat_code"	=>	$PHP_lots_code[$bom_id],
        "bom_id"	=>	$bom_id,
        "mat_name"	=>	$bom_name,
        "mat_cat"	=>	"l",
        "mat_id"	=>	$PHP_lots_id[$bom_id],
        "color"		=>	$PHP_lots_color[$bom_id],
        "o_color"	=>	$PHP_lots_o_color[$bom_id],
        "size"		=>	$PHP_lots_size[$bom_id],
        "consump"	=>	$PHP_lots_consump[$bom_id],
        "loss"		=>	$PHP_lots_loss[$bom_id],
        "qty"		=>	$PHP_lots_qty[$bom_id],
        "o_qty"		=>	$PHP_lots_o_qty[$bom_id],
        "unit"		=>	$PHP_lots_unit[$bom_id]
    );
    if($PHP_lots_po_bom_id[$bom_id]){
        $UTF8_MATERIALS->bom_edit($lots_parm);
    }else{
        $UTF8_MATERIALS->bom_add($lots_parm);
    }
}

foreach($PHP_acc_name as $bom_id => $bom_name){
    $acc_parm = array(
        "po_bom_id"	=>	$PHP_acc_po_bom_id[$bom_id],
        "ord_num"	=>	$ord_num,
        "fty"		=>	$factory,
        "mat_code"	=>	$PHP_acc_code[$bom_id],
        "bom_id"	=>	$bom_id,
        "mat_name"	=>	$bom_name,
        "mat_cat"	=>	"a",
        "mat_id"	=>	$PHP_acc_id[$bom_id],
        "color"		=>	$PHP_acc_color[$bom_id],
        "o_color"	=>	$PHP_acc_o_color[$bom_id],
        "size"		=>	$PHP_acc_size[$bom_id],
        "consump"	=>	$PHP_acc_consump[$bom_id],
        "loss"		=>	$PHP_acc_loss[$bom_id],
        "qty"		=>	$PHP_acc_qty[$bom_id],
        "o_qty"		=>	$PHP_acc_o_qty[$bom_id],
        "unit"		=>	$PHP_acc_unit[$bom_id]
    );
    if($PHP_acc_po_bom_id[$bom_id]){
        $UTF8_MATERIALS->bom_edit($acc_parm);
    }else{
        $UTF8_MATERIALS->bom_add($acc_parm);
    }
}

$msg = "Success update ". $ord_num;
$_SESSION['MSG'][] = $msg;
$log->log_add(0,$AUTH."E",$msg);

redirect_page($PHP_SELF."?PHP_action=view_bom_of_materials&PHP_ord_num=".$ord_num."&PHP_id=".$PHP_id);
break;



case "confirm_bom_of_materials":
check_authority($AUTH,"edit");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];

if( $ord_num ){
    if($BOM_OF_MATERIALS->confirm_bom($ord_num)){
        $msg = "Success Submit ". $ord_num;
        $_SESSION['MSG'][] = $msg;
        $log->log_add(0,$AUTH."E",$msg);
    } else {
        $_SESSION['MSG'][] = "Error ! Can't Submit records!";
    }
} else {
    $_SESSION['MSG'][] = "Error ! Can't Submit records!";
    redirect_page($PHP_SELF."?PHP_action=main");
}

redirect_page($PHP_SELF."?PHP_action=view_bom_of_materials&PHP_ord_num=".$ord_num."&PHP_id=".$PHP_id);
break;



case "revise_bom_of_materials":
check_authority($AUTH,"edit");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];

if( $ord_num ){
    if($BOM_OF_MATERIALS->revise_bom($ord_num)){
        $msg = "Success reopen ". $ord_num;
        $_SESSION['MSG'][] = $msg;
        $log->log_add(0,$AUTH."E",$msg);
    } else {
        $_SESSION['MSG'][] = "Error ! Can't reopen records!";
    }
} else {
    $_SESSION['MSG'][] = "Error ! Can't reopen records!";
    redirect_page($PHP_SELF."?PHP_action=main");
}

redirect_page($PHP_SELF."?PHP_action=view_bom_of_materials&PHP_ord_num=".$ord_num."&PHP_id=".$PHP_id);
break;



case "bom_mat_release_search":
check_authority('089',"view");

$now_num = empty($_GET['M_now_num']) ? 0 : $_GET['M_now_num'];
if(!empty($_GET['PHP_fty_sch'])){
    $fty = $_GET['PHP_fty_sch'];
}else{
    $fty = "LY";
}

$parm = array(
    "action"	=>	"bom_mat_release_search",
    "ord_num"	=> $_GET['PHP_wi_num'],
    "fty"	 	=> $fty,
    "PHP_mat"   => $_GET['PHP_mat'],
    "now_num"   => $now_num,
    "page_num"  => 10
);
// print_r($parm);
if(!$op = $UTF8_MATERIALS->search($parm)){
    $op['record_NONE'] = 1;
}
// print_r($op);
$op['PHP_mat'] = $_GET['PHP_mat'];
$op['back_str'] = "PHP_fty_sch=".$fty."&PHP_wi_num=".$_GET['PHP_wi_num']."&M_now_num=".$now_num;
// $op['dept_id'] = get_dept_id();

// 如果是 manager 進入時...
// if (substr($op['dept_id'],0,7) == "<select"){
    // $op['manager_flag'] = 1;
// }

page_display($op, '089', $TPL_BOM_MAT_RELEASE_LIST);
break;
	
	case "mat_release":
		check_authority('089',"view");

		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // 判定進入身份的指標(team)
		$sales_dept_ary = get_sales_dept();// 取出 業務的部門 [不含K0] ------
		
		$op['dept_id'] = $dept_id;
		
		$op['manager_flag'] = 1;
		$manager_v = 1;
		$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","styled-select",""); 
 		
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
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust',"styled-select",'',$cust_def_vue); 
		$op['fty_select'] =  $arry2->select($FACTORY,'','PHP_fty_sch',"styled-select",''); 
		for ($i=0; $i< sizeof($FACTORY); $i++)
		{
			if ($user_dept == $FACTORY[$i]) $op['fty_select'] = $user_dept."<input type='hidden' name='PHP_fty_sch' value='$user_dept'>";
		}

		$op['msg']= $wi->msg->get(2);
		
		$send_num = $UTF8_MATERIALS->get_send_num("LY");
		$begin_date = "2015-03-01";
		$end_date = date("Y-m-d");
		$schedule_num = $UTF8_MATERIALS->get_ord_num4set_mat_list("LY", $begin_date, $end_date);
		
		$op['ord_list'] = array();
		foreach($schedule_num as $k1 => $schedule_val){
			$chk_match = false;
			foreach($send_num as $k2 => $send_val){
				if($schedule_val['ord_num'] == $send_val['ord_num']){
					$chk_match = true;
					break;
				}
			}
			if(!$chk_match){
				if(!array_key_exists($schedule_val['ord_num'], $op['ord_list'])){
					$op['ord_list'][] = $schedule_val['ord_num'];
				}
			}
		}
		$op['begin_date'] = $begin_date;
		$op['end_date'] = $end_date;
		
		page_display($op, '089', $TPL_BOM_MAT_RELEASE_SEARCH);		    	    
	break;
	


	
	case "bom_requ_view":
		check_authority('089',"view");
		
		$bom_utf8 = $UTF8_MATERIALS->get_bom_materials($PHP_bom_num);
		
		if(!$op['wi']  = $wi->get(0,$PHP_bom_num,$new_link_id)){
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

		// 相片的URL決定 ------------------------------------------------------
		$style_dir	= "./picture/";  
		$no_img		= "./images/graydot.gif";
		if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
			$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
		} else {
			$op['wi']['pic_url'] = $no_img;
		}
if($_GET['PHP_mat'] == 'lots'){
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
}
		

if($_GET['PHP_mat'] == 'acc'){
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
}

$op['PHP_mat'] = $_GET['PHP_mat'];
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


		/* //  取出  BOM  加購記錄 --------------------------------------------------------------
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
		*/
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

		$bom_utf8_size = sizeof($bom_utf8);
		$bom_lots_size = sizeof($op['bom_lots_list']);
		$bom_acc_size = sizeof($op['bom_acc_list']);

		# 編碼轉換，並且取出 驗收庫存量
		for($x=0;$x<$bom_lots_size;$x++){
			$op['bom_lots_list'][$x]['comp'] = iconv("big5","UTF-8",$op['bom_lots_list'][$x]['comp']);
			$op['bom_lots_list'][$x]['color'] = iconv("big5","UTF-8",$op['bom_lots_list'][$x]['color']);
			$op['bom_lots_list'][$x]['mat_id'] = $UTF8_MATERIALS->get_field("id", "where lots_code = '".$op['bom_lots_list'][$x]['lots_code']."'", "lots");
		}
		for($x=0;$x<$bom_acc_size;$x++){
			$op['bom_acc_list'][$x]['cons'] = iconv("big5","UTF-8",$op['bom_acc_list'][$x]['cons']);
			$op['bom_acc_list'][$x]['color'] = iconv("big5","UTF-8",$op['bom_acc_list'][$x]['color']);
			$op['bom_acc_list'][$x]['mat_id'] = $UTF8_MATERIALS->get_field("id", "where acc_code = '".$op['bom_acc_list'][$x]['acc_code']."'", "acc");
		}

		/*$ord_row = $order->get_fields_array("id, size", "where order_num='".$PHP_bom_num."'","s_order");
		# 取 中壢 marker 用量
		if ($op['mks'] = $Marker->get(2,0,$ord_row[0]['id'],1,null,'',1)) {
			foreach($op['mks'] as $keys => $vals){
				if(is_array($vals)){
					foreach($vals as $key => $val){
						if($key === 'ord_id'){
							$op['mks'][$keys]['marker'] = $Marker->marker_list($val,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo']);
							$op['mks'][$keys]['averages']  = $Marker->marker_list2(2,0,$val,1,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo'],$ord_row[0]['size']);
						}

						if($key === 'unit_type'){
							$op['mks'][$keys]['unit'] = ($val)? $op['unit'] = $GLOBALS['unit_type2'][$val] : '';
						}
						if($key === 'fab_type'){
							if( $val <> 0 ){
								$op['mks'][$keys]['fab_t'] = $GLOBALS['fab_type'][$val];
							}else{
								$op['mks'][$keys]['fab_t'] = '';
							}
						}
						if($key === 'remark'){
							$op['mks'][$keys]['remark'] = $Marker->rmk_list($val);
						}
					}
				}
			}
		}

		# 取 工廠 marker 用量
		if ($op['fty_mks'] = $fty_marker->get(2,0,$ord_row[0]['id'],1,null,'',1)) {
			foreach($op['fty_mks'] as $keys => $vals){
				if(is_array($vals)){
					foreach($vals as $key => $val){
						if($key === 'ord_id'){
							$op['fty_mks'][$keys]['marker'] = $fty_marker->marker_list($val,$op['fty_mks'][$keys]['fab_type'],$op['fty_mks'][$keys]['combo']);
							$op['fty_mks'][$keys]['averages']  = $fty_marker->marker_list2(2,0,$val,1,$op['fty_mks'][$keys]['fab_type'],$op['fty_mks'][$keys]['combo'],$ord_row[0]['size']);
						}

						if($key === 'unit_type'){
							$op['fty_mks'][$keys]['unit'] = ($val)? $op['unit'] = $GLOBALS['unit_type2'][$val] : '';
						}
						if($key === 'fab_type'){
							if( $val <> 0 ){
								$op['fty_mks'][$keys]['fab_t'] = $GLOBALS['fab_type'][$val];
							}else{
								$op['fty_mks'][$keys]['fab_t'] = '';
							}
						}
						if($key === 'remark'){
							$op['fty_mks'][$keys]['remark'] = $fty_marker->rmk_list($val);
						}
					}
				}
			}
		}*/
		
		$sort_lots_bom_id = array();

		# 將已翻釋的資料取代原本的BOM資料
		for($i=0;$i<$bom_utf8_size;$i++){
			if($bom_utf8[$i]['mat_cat'] == 'l'){
				for($j=0;$j<=$bom_lots_size;$j++){
					if($op['bom_lots_list'][$j]['id'] == $bom_utf8[$i]['bom_id']){
						$op['bom_lots_list'][$j]['po_bom_id'] = $bom_utf8[$i]['id'];
						$op['bom_lots_list'][$j]['lots_name'] = $bom_utf8[$i]['mat_name'];
						$op['bom_lots_list'][$j]['mat_cat'] = "l";
						$op['bom_lots_list'][$j]['color'] = $bom_utf8[$i]['color'];
						$op['bom_lots_list'][$j]['o_color'] = $bom_utf8[$i]['o_color'];
						$op['bom_lots_list'][$j]['est_1'] = $bom_utf8[$i]['consump'];
						$op['bom_lots_list'][$j]['loss'] = $bom_utf8[$i]['loss'];
						$op['bom_lots_list'][$j]['ord_qty'] = $bom_utf8[$i]['qty'];
						$op['bom_lots_list'][$j]['trans_sub_date'] = $bom_utf8[$i]['trans_sub_date'];
						$op['bom_lots_list'][$j]['requi'] = $UTF8_MATERIALS->get_requi_det($op['smpl']['factory'], "l", $op['bom_lots_list'][$j]['id']);
						
						break;
					}
				}
			}else{
				for($k=0;$k<$bom_acc_size;$k++){
					if($op['bom_acc_list'][$k]['id'] == $bom_utf8[$i]['bom_id']){
						$op['bom_acc_list'][$k]['po_bom_id'] = $bom_utf8[$i]['id'];
						$op['bom_acc_list'][$k]['acc_name'] = $bom_utf8[$i]['mat_name'];
						$op['bom_acc_list'][$k]['mat_cat'] = "a";
						$op['bom_acc_list'][$k]['color'] = $bom_utf8[$i]['color'];
						$op['bom_acc_list'][$k]['o_color'] = $bom_utf8[$i]['o_color'];
						$op['bom_acc_list'][$k]['est_1'] = $bom_utf8[$i]['consump'];
						$op['bom_acc_list'][$k]['loss'] = $bom_utf8[$i]['loss'];
						$op['bom_acc_list'][$k]['ord_qty'] = $bom_utf8[$i]['qty'];
						$op['bom_acc_list'][$k]['trans_sub_date'] = $bom_utf8[$i]['trans_sub_date'];
						// $po_num = $UTF8_MATERIALS->get_po_num($op['bom_acc_list'][$k]['id'], "a", $op['bom_acc_list'][$k]['mat_id'], $op['bom_acc_list'][$k]['o_color'], $op['bom_acc_list'][$k]['size']);
						$requi_det = $UTF8_MATERIALS->get_requi_det($op['smpl']['factory'], "a", $op['bom_acc_list'][$k]['id']);
						for( $m=0; $m<sizeof($requi_det); $m++){
							$op['bom_acc_list'][$k]['requi'][] = $requi_det[$m];
						}
						
						break;
					}
				}
			}
		}
		
		for($j=0;$j<=$bom_lots_size;$j++){
			for($k=0;$k<sizeof($op['mks']);$k++){
				if($op['bom_lots_list'][$j]['lots_used_id'] == $op['mks'][$k]['lots_code']){
					$op['bom_lots_list'][$j]['tp_averages'] = $op['mks'][$k]['averages'];
				}
			}
			for($k=0;$k<sizeof($op['fty_mks']);$k++){
				if($op['bom_lots_list'][$j]['lots_used_id'] == $op['fty_mks'][$k]['lots_code']){
					$op['bom_lots_list'][$j]['fty_averages'] = $op['fty_mks'][$k]['averages'];
				}
			}
		}
		
		// print_r($op['bom_lots_list']);exit;
		// sort($op['bom_lots_list']);
		// array_multisort($sort_lots_bom_id, SORT_ASC, $op['bom_lots_list']);
		// sort($op['bom_acc_list']);
		
		$line = $monitor->get_line("LY");
		for($i=0; $i < sizeof($line); $i++)
		{
			if( $line[$i]['line'] <> 'contractor' ) {
				if($line[$i]['sc'] == 0 )$line_value[] = $line[$i]['line']."  (".$line[$i]['line_style'].")";
				if($line[$i]['sc'] == 0 )$line_key[] = $line[$i]['line'];
			}
		}
		$line_select = $arry2->select($line_value,'','PHP_line',"styled-select","",$line_key);
		$op['line_select'] = $line_select;
		$op['mat'] = $_GET['PHP_mat'];
		$op['back_str'] = "&PHP_fty_sch=".$_GET['PHP_fty_sch']."&PHP_mat=".$_GET['PHP_mat']."&PHP_wi_num=".$_GET['PHP_wi_num']."&M_now_num=".$_GET['M_now_num'];
		
		page_display($op, '089', $TPL_BOM_REQU_VIEW);
		
	break;
	
	
	case "do_requi_nofity_add":
		check_authority('089',"add");
		
		$parm = array (
					"ord_num"		=>	$PHP_bom_num,
					"fty"			=>	$PHP_fty,
					"line"			=>	$PHP_line,
					"create_date"	=>	date('Y-m-d H:i:s'),
					"create_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
					"status"		=>	0
					);
		$head="RN".date('y')."-";	//RP+日期=驗收單開頭
		$parm['rn_num'] = $UTF8_MATERIALS->get_no($head,'rn_num','requi_notify');
		
		$rtn_flag = $UTF8_MATERIALS->add_main($parm);
		
		if($rtn_flag){
			
				foreach($PHP_qty as $po_bom_id => $val_ary){
					foreach($val_ary as $key1 => $val1){
						foreach($val1 as $key2 => $qty){
							if($qty > 0){
								$det_parm = array (
											"rn_num"		=>	$parm['rn_num'],
											"po_bom_id"		=>	$po_bom_id,
											"bl_num"		=>	$PHP_bl_num[$po_bom_id][$key1][$key2],
											"po_num"		=>	$PHP_po_num[$po_bom_id][$key1][$key2],
											"bom_id"		=>	$PHP_bom_id[$po_bom_id],
											"mat_cat"		=>	"l",
											"mat_id"		=>	$PHP_mat_id[$po_bom_id],
											"mat_name"		=>	$PHP_mat_name[$po_bom_id],
											"color"			=>	$PHP_color[$po_bom_id],
											"o_color"		=>	$PHP_o_color[$po_bom_id],
											"size"			=>	$PHP_size[$po_bom_id],
											"r_no"			=>	$PHP_r_no[$po_bom_id][$key1][$key2],
											"l_no"			=>	$PHP_l_no[$po_bom_id][$key1][$key2],
											"qty"			=>	$qty,
											"stock_inventory_id"	=>	$PHP_stock_inventory_id[$po_bom_id][$key1][$key2],
											"storage"		=>	$PHP_storage[$po_bom_id][$key1][$key2],
											);
								$rtn_flag = $UTF8_MATERIALS->add_det($det_parm);
							}
						}
					}
				}
			}
		
		
		$log->log_add(0,"089A","Append RN# ".$parm['rn_num']);
		
		redirect_page($PHP_SELF."?PHP_action=mat_notice_view&PHP_rn_num=".$parm['rn_num']);
	
	break;
	
	
	case "requi_notify":
		check_authority('090',"view");
		
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$op['fty_select'] =  $arry2->select($FACTORY,'','sch_fty',"styled-select",''); 
		for ($i=0; $i< sizeof($FACTORY); $i++)
		{
			if ($user_dept == $FACTORY[$i]) $op['fty_select'] = $user_dept."<input type='hidden' name='sch_fty' value='$user_dept'>";
		}
		
		page_display($op, '043', "requi_notify.html");
		
	break;
	
	
	case "mat_notice_search":
		check_authority('090',"view");
		
		$sch_parm = array(
						"rn_num"		=>	$_GET['sch_rn_num'],
						"ord_num"		=>	$_GET['sch_ord_num'],
						"fty"			=>	$_GET['sch_fty'],
						"PHP_sr_startno"=>	$_GET['PHP_sr_startno'],
						"PHP_action"	=>	"mat_notice_search"
						);
		
		$op = $UTF8_MATERIALS->mat_notice_search($sch_parm);
		$op['back_str'] = "&sch_rn_num=".$_GET['sch_rn_num']."&sch_ord_num=".$_GET['sch_ord_num']."&sch_fty=".$_GET['sch_fty'];
		
		page_display($op, '089', $TPL_REQUI_NOTIFY_LIST);
	break;
	
	
	case "mat_notice_view":
		check_authority('090',"view");
		
		$op = $UTF8_MATERIALS->get_mat_notice($_GET['PHP_rn_num']);
		$det_size = sizeof($op['notice_det']);
		
		$op['back_str'] = "&sch_rn_num=".$_GET['sch_rn_num']."&sch_ord_num=".$_GET['sch_ord_num']."&sch_fty=".$_GET['sch_fty']."&PHP_sr_startno=".$_GET['PHP_sr_startno'];
		if($_GET['PHP_msg'])
			$op['msg'][] = $_GET['PHP_msg'];
		
		page_display($op, '090', $TPL_REQUI_NOTIFY_SHOW);
	break;
	
	
	case "notice_log_add":
		check_authority('090',"add");
		
		$parm= array(
				'rn_num'		=>	$_POST['PHP_rn_num'],
				'des'			=>	$_POST['PHP_des'],
				'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
				'k_date'		=>	date('Y-m-d  H:i:s')
				);

		$f1 = $UTF8_MATERIALS->add_log($parm);
		$op = $UTF8_MATERIALS->get_mat_notice($_POST['PHP_rn_num']);
		if($f1){
			$op['msg'][] = "Successfully add log :".$_POST['PHP_rn_num'];
		}
		
		page_display($op, '090', $TPL_REQUI_NOTIFY_SHOW);
	break;
	
	
	case "notice_submit":
		check_authority('090',"add");
		
		$parm = array(
					"rn_num"	=>	$_POST['PHP_rn_num'],
					"sub_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
					"sub_date"	=>	date('Y-m-d H:i:s')
					);
		$f1 = $UTF8_MATERIALS->submit_notice($parm);
		if($f1){
			$log->log_add(0,"090S","Submit ".$_POST['PHP_rn_num']);
			$msg = "Successfully submit ".$_POST['PHP_rn_num'];
			redirect_page($PHP_SELF."?PHP_action=mat_notice_view&PHP_rn_num=".$_POST['PHP_rn_num']."&PHP_msg=".$msg);
		}
		
		// page_display($op, '090', $TPL_REQUI_NOTIFY_SHOW);
	break;
	
	
	case "notice_revise":
		check_authority('090',"edit");
		
		$f1 = $UTF8_MATERIALS->revise_notice($_GET['PHP_rn_num']);
		if($f1){
			$op = $UTF8_MATERIALS->get_mat_notice($_GET['PHP_rn_num']);
		}else{
			echo "error";
		}
		
		$line = $monitor->get_line($op['notice']['fty']);
		for($i=0; $i < sizeof($line); $i++)
		{
			if( $line[$i]['line'] <> 'contractor' ) {
				if($line[$i]['sc'] == 0 )$line_value[] = $line[$i]['line']."  (".$line[$i]['line_style'].")";
				if($line[$i]['sc'] == 0 )$line_key[] = $line[$i]['line'];
			}
		}
		
		$line_select = $arry2->select($line_value,$op['notice']['line'],'PHP_line',"styled-select",'',$line_key);
		$op['line_select'] = $line_select;
		
		$op['back_str'] = "&sch_rn_num=".$_GET['sch_rn_num']."&sch_ord_num=".$_GET['sch_ord_num']."&sch_fty=".$_GET['sch_fty']."&PHP_sr_startno=".$_GET['PHP_sr_startno'];
		
		
		page_display($op, '090', $TPL_REQUI_NOTIFY_EDIT);
	break;
	
	
	case "notice_edit":
		check_authority('090',"edit");
		
		$op = $UTF8_MATERIALS->get_mat_notice($_GET['PHP_rn_num']);
		
		$det_size = sizeof($op['notice_det']);
		$ver = $UTF8_MATERIALS->get_ver($op['notice']['fty'], $op['notice_det'][0]['mat_cat']);
		
		for($i=0;$i<$det_size;$i++){
			$op['notice_det'][$i]['stock_qty'] = $UTF8_MATERIALS->get_stock_qty_by_inventory_id($ver['ver'], $op['notice_det'][$i]['mat_cat'], $op['notice_det'][$i]['stock_inventory_id']);
			$op['notice_det'][$i]['pre_send_qty'] = $UTF8_MATERIALS->get_pre_set_qty($op['notice_det'][$i]['stock_inventory_id'], $op['notice_det'][$i]['id']);
			$op['notice_det'][$i]['stock_qty'] = $op['notice_det'][$i]['stock_qty'] - $op['notice_det'][$i]['pre_send_qty'];
		}
		
		$line = $monitor->get_line($op['notice']['fty']);
		for($i=0; $i < sizeof($line); $i++)
		{
			if( $line[$i]['line'] <> 'contractor' ) {
				if($line[$i]['sc'] == 0 )$line_value[] = $line[$i]['line']."  (".$line[$i]['line_style'].")";
				if($line[$i]['sc'] == 0 )$line_key[] = $line[$i]['line'];
			}
		}
		
		$line_select = $arry2->select($line_value,$op['notice']['line'],'PHP_line',"styled-select",'',$line_key);
		$op['line_select'] = $line_select;
		
		page_display($op, '090', $TPL_REQUI_NOTIFY_EDIT);
	break;
	
	
	case "do_notice_edit":
		check_authority('090',"edit");
		
		$update_parm = array();
		foreach($_POST['PHP_qty'] as $id => $qty){
			$update_parm[$id] = $qty;
		}
		
		$rtn_flag = $UTF8_MATERIALS->notice_edit($update_parm, $_POST['PHP_line'], $_POST['PHP_rn_num']);
		
		if($rtn_flag){
			$msg = "Success update ". $_POST['PHP_rn_num'];
			$log->log_add(0,"090E",$msg);
			redirect_page($PHP_SELF."?PHP_action=mat_notice_view&PHP_rn_num=".$_POST['PHP_rn_num']."&PHP_msg=".$msg);
		}
		
	break;
	
	
	case "do_notice_mat_del":
		
		echo $UTF8_MATERIALS->notice_mat_del($_GET['PHP_det_id'], $_GET['PHP_rn_num']);
		
	break;
	
	
	case "uncfm_search":
		check_authority('098',"view");
		
		$parm = array(
					"fty"			=>	$GLOBALS['SCACHE']['ADMIN']['dept'],
					"PHP_action"	=>	"uncfm_search",
					"PHP_sr_startno"=>	$_GET['PHP_sr_startno']
					);
		$op = $UTF8_MATERIALS->uncfm_search($parm);
		
		page_display($op, '098', $TPL_REQUI_NOTIFY_CFM_LIST);
	break;
	
	
	case "mat_notice_cfm_view":
		check_authority('098',"view");
		
		$op = $UTF8_MATERIALS->get_mat_notice($_GET['PHP_rn_num']);
		$det_size = sizeof($op['notice_det']);
		for($i=0;$i<$det_size;$i++){
			$op['notice_det'][$i]['stock_qty'] = $UTF8_MATERIALS->get_stock_qty($op['notice']['fty'], $op['notice_det'][$i]['bl_num'], $op['notice_det'][$i]['r_no'], $op['notice_det'][$i]['l_no'], $op['notice_det'][$i]['mat_cat'], $op['notice_det'][$i]['mat_id'], $op['notice_det'][$i]['o_color'], $op['notice_det'][$i]['size']);
		}
		$op['back_str'] = "&PHP_sr_startno=".$_GET['PHP_sr_startno'];
		if($_GET['PHP_msg'])
			$op['msg'][] = $_GET['PHP_msg'];
		
		page_display($op, '090', $TPL_REQUI_NOTIFY_CFM_SHOW);
	break;
	
	
	case "notice_cfm":
		check_authority('098',"add");
		
		$parm = array(
					"rn_num"	=>	$_POST['PHP_rn_num'],
					"cfm_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
					"cfm_date"	=>	date('Y-m-d H:i:s')
					);
		$f1 = $UTF8_MATERIALS->cfm_notice($parm);
		if($f1){
			$log->log_add(0,"090S","Submit ".$_POST['PHP_rn_num']);
			$msg = "Successfully CFM ".$_POST['PHP_rn_num'];
			redirect_page($PHP_SELF."?PHP_action=uncfm_search".$_POST['PHP_back_str']."&PHP_msg=".$msg);
		}
		
	break;
	
	
	case "send_detail_view":
		check_authority('098',"view");
		
		$op['wi'] = $wi->get(0,$_GET['PHP_ord_num']);
		$op['lots'] = $UTF8_MATERIALS->send_det_lots_view($op['wi']['id'], $op['wi']['wi_num']);
		$op['acc'] = $UTF8_MATERIALS->send_det_acc_view($op['wi']['id'], $op['wi']['wi_num']);
		
		page_display($op, '084', "send_det_show.html");
	break;
	
	
	
}

?>