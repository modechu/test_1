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
$TPL_BOM_MAT_RELEASE_SEARCH = "bom_mat_release_search.html";
$TPL_BOM_MAT_RELEASE_LIST = "bom_mat_release_list.html";
$TPL_BOM_REQU_VIEW = "bom_requ_view.html";
$TPL_BOM_OF_MATERIALS_MAIN = "bom_of_materials_main.html";
$TPL_BOM_OF_MATERIALS_LIST = "bom_of_materials_list.html";
$TPL_BOM_OF_MATERIALS_VIEW = "bom_of_materials_view.html";
$TPL_BOM_OF_MATERIALS_DETAIL = "bom_of_materials_detail.html";
$TPL_BOM_OF_MATERIALS_EDIT = "bom_of_materials_edit.html";

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
$AUTH = '089';
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

$op['fty_select'] =  $arry2->select(get_factory_group(),$_SESSION['SCACHE']['ADMIN']['dept'], "PHP_factory","styled-select",'');

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

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' );

page_display($op,$AUTH,"bom_mat_release_mian.html");		    	    
break;



case "bom_mat_release_append_list":
check_authority($AUTH,"view");

$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$mat = $_GET['PHP_mat'] ? $_GET['PHP_mat'] : $_POST['PHP_mat'];

$table = "s_order,wi,cust";
$m_sql= "s_order.order_num,cust.cust_init_name,s_order.creator,s_order.etd,s_order.pdc_status,s_order.pdc_cut_sub_user,s_order.pdc_cut_sub_date,s_order.pdc_bom_sub_user,s_order.pdc_bom_sub_date,s_order.factory,wi.id";
if( $factory || $ord_num ) {
    $where_str = "WHERE s_order.order_num = wi.wi_num AND wi.cust = cust.cust_s_name AND wi.cust_ver = cust.ver ";
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

$op['PHP_mat'] = $mat;

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' );

page_display($op, $AUTH, "bom_mat_release_append_list.html");
break;



case "bom_mat_release_search":
check_authority($AUTH,"view");

global $MySQL;
$MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$send_num = $_GET['PHP_send'] ? $_GET['PHP_send'] : $_POST['PHP_send'];
$mat = $_GET['PHP_mat'] ? $_GET['PHP_mat'] : $_POST['PHP_mat'];

$table = "requi_notify,requi_notify_bom";

$m_sql= "requi_notify.id,requi_notify.ord_num,requi_notify.fty,requi_notify.line,requi_notify.status,requi_notify.version,requi_notify_bom.wi_id";
if( $factory || $ord_num || $send_num ) {
    $where_str = "WHERE `requi_notify`.`id` = `requi_notify_bom`.`rn_num` AND `requi_notify`.`fty` = '".$factory."' ";
    // $where_str .= !empty($factory) ? " AND `requi_notify`.`fty` = '".$factory."' " : "";
    $where_str .= !empty($ord_num) ? " AND `requi_notify`.`ord_num` LIKE '%".$ord_num."%' " : "";
    $where_str .= !empty($send_num) ? " AND `requi_notify`.`id` LIKE '%".str_replace('RN','',$send_num)."%' " : "";
    unset($_SESSION['PAGE'][$PHP_action]);
} else {
    if( !empty($_SESSION['PAGE'][$PHP_action]['where_str']) ){
        $where_str = $_SESSION['PAGE'][$PHP_action]['where_str'];
    } else {
        header("Location: ".$PHP_SELF."?PHP_action=main");
    }
}

$op = $Search->page_sorting($table,$m_sql,$PHP_action,$where_str,10,'','',' GROUP BY `requi_notify`.`id` ');

$MySQL->po_disconnect();

foreach($op['pages'] as $k => $v){
    $op['pages'][$k]['pdc_bom_sub_user'] = big52uni(get_user_html($v['pdc_bom_sub_user']));
    $op['pages'][$k]['line'] = $monitor->get_line_name($op['pages'][$k]['line']);
}

$op['PHP_mat'] = $mat;

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' );

page_display($op, $AUTH, "bom_mat_release_search.html");
break;



case "bom_requi_append":
check_authority($AUTH,"add");

$PHP_mat = $_GET['PHP_mat'] ? $_GET['PHP_mat'] : $_POST['PHP_mat'];
$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$line = $_GET['PHP_line'] ? $_GET['PHP_line'] : $_POST['PHP_line'];

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

$send_qty = $UTF8_MATERIALS->get_send_qty($ord_num);
$HTML = $BOM_OF_MATERIALS->get_wiqty_qty_send($ord_num,$size,$send_qty);
$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];

$op['line_select']  = $arry2->select_id($monitor->get_line_id($op['factory']),'','PHP_line','','styled-select','');
$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;
$op['mat'] = $PHP_mat;

$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op, $AUTH, "bom_requi_append.html");
break;



case "do_bom_requi_append":
check_authority($AUTH,"add");

$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$line = $_GET['PHP_line'] ? $_GET['PHP_line'] : $_POST['PHP_line'];
$wi_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];
$mat = $_GET['PHP_mat'] ? $_GET['PHP_mat'] : $_POST['PHP_mat'];

$parm = array (
    "ord_num"   =>	$ord_num,
    "fty"       =>	$factory,
    "line"      =>	$line
);

if( !$id = $UTF8_MATERIALS->add_requi_notify($parm) ){
    $_SESSION['MSG'][] = "Error ! Can't Append records!";
    redirect_page($PHP_SELF."?PHP_action=main");
} else {
    if( !$sts = $UTF8_MATERIALS->add_requi_notify_bom($id,$wi_id,$_POST['PHP_qty']) ){
        $_SESSION['MSG'][] = "Error ! Can't Append records!";
        redirect_page($PHP_SELF."?PHP_action=main");
    } else {
        $msg = $_SESSION['MSG'][] = "Success Append RN# RN".$id;
        $log->log_add(0,$AUTH."A",$msg);
        redirect_page($PHP_SELF."?PHP_action=bom_requi_set_material&PHP_rn_num=".$id."&PHP_ord_num=".$ord_num."&PHP_factory=".$factory."&PHP_line=".$line."&PHP_id=".$wi_id."&PHP_mat=".$mat);
    }
}
break;



case "bom_requi_set_material":
check_authority($AUTH,"view");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$line = $_GET['PHP_line'] ? $_GET['PHP_line'] : $_POST['PHP_line'];
$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];
$mat = $_GET['PHP_mat'] ? $_GET['PHP_mat'] : $_POST['PHP_mat'];

// $op['rn_id'] = get_insert_id("requi_notify","1");
// $op['line'] = $monitor->get_line_name($_POST['PHP_line']);

if(!$op = $order->get_fields_array('id,factory,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_bom_sub_user,pdc_bom_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $_SESSION['MSG'][] = "Error !!";
    redirect_page($PHP_SELF."?PHP_action=main");
}

$op = $op[0];

$op['rn_num']  = $rn_num;
$op['line'] = $monitor->get_line_name($line);
$op['line_id'] = $line;

$size = $size_des->get($op['size']);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);

$utf_bom_qty = $UTF8_MATERIALS->get_bom_materials_qty($rn_num);

#帶入該分批數量的配比
$HTML = $BOM_OF_MATERIALS->get_wiqty_qty_send_material($ord_num,$size,$utf_bom_qty);

$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];
$op['send_qty'] = $HTML[4];
$bom_utf8 = $UTF8_MATERIALS->get_bom_materials($ord_num);
$op['BHTML'] = $BOM_OF_MATERIALS->get_bom_added_qty($PHP_id,$size,$HTML[2],$bom_utf8);
$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;

$op['pdc_bom_sub_user'] = big52uni(get_user_html($op['pdc_bom_sub_user']));
$op['mat'] = $_GET['PHP_mat'];



$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );
// print_r($_POST);
// print_r($_GET);
page_display($op, $AUTH, "bom_requi_set_material.html");
break;



case "do_added_send_material":
check_authority($AUTH,"add");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];
$line = $_GET['PHP_line'] ? $_GET['PHP_line'] : $_POST['PHP_line'];
$PHP_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];

if( !$sts = $UTF8_MATERIALS->added_send_material($rn_num,$_POST['PHP_bom_id'],$_POST['PHP_qty']) ){
    $_SESSION['MSG'][] = "Error ! Can't Append records!";
    redirect_page($PHP_SELF."?PHP_action=main");
} else {
    $msg = $_SESSION['MSG'][] = "Success Added RN# RN".$rn_num;
    $log->log_add(0,$AUTH."A",$msg);
    redirect_page($PHP_SELF."?PHP_action=bom_requi_view_material&PHP_rn_num=".$rn_num);
}
break;



case "bom_requi_view_material":
check_authority($AUTH,"view");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
$utf_rn_num = $UTF8_MATERIALS->get_rn_num($rn_num);
$factory = $utf_rn_num['fty'];
$ord_num = $utf_rn_num['ord_num'];
$line = $utf_rn_num['line'];

if(!$op = $order->get_fields_array('id,factory,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_bom_sub_user,pdc_bom_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $_SESSION['MSG'][] = "Error !!";
    redirect_page($PHP_SELF."?PHP_action=main");
}

$op = $op[0];
$wi_id = $wi->get_field($ord_num,'id');
$PHP_id = $wi_id[0];

$utf_rn_num = $UTF8_MATERIALS->get_rn_num($rn_num);
// print_r($utf_rn_num);
$op['status'] = $utf_rn_num['status'];
$op['version'] = $utf_rn_num['version'];
$op['sub_date'] = $utf_rn_num['sub_date'];
$op['sub_user'] = big52uni(get_user_html($utf_rn_num['sub_user']));
$op['cfm_date'] = $utf_rn_num['cfm_date'];
$op['cfm_user'] = big52uni(get_user_html($utf_rn_num['cfm_user']));
$op['rn_num'] = $rn_num;
$op['line'] = $monitor->get_line_name($line);

$size = $size_des->get($op['size']);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);

$utf_bom_qty = $UTF8_MATERIALS->get_bom_materials_qty($rn_num);
// $HTML = $bom->get_wiqty_qty_view($ord_num,$size);
#帶入該分批數量的配比
$HTML = $BOM_OF_MATERIALS->get_wiqty_qty_view_material($ord_num,$size,$utf_bom_qty);

$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];
$op['send_qty'] = $HTML[4];
$bom_utf8 = $UTF8_MATERIALS->get_bom_materials($ord_num);
$bom_utf8_qty = $UTF8_MATERIALS->get_bom_qty($rn_num);
// print_r($bom_utf8_qty);
$op['BHTML'] = $BOM_OF_MATERIALS->get_bom_view_qty($PHP_id,$bom_utf8,$bom_utf8_qty);

$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;

$op['pdc_bom_sub_user'] = big52uni(get_user_html($op['pdc_bom_sub_user']));
$op['mat'] = $_GET['PHP_mat'];

$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op, $AUTH, "bom_requi_view_material.html");
break;



case "bom_requi_edit_material":
check_authority($AUTH,"edit");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
$utf_rn_num = $UTF8_MATERIALS->get_rn_num($rn_num);
$factory = $utf_rn_num['fty'];
$ord_num = $utf_rn_num['ord_num'];
$line = $utf_rn_num['line'];

if(!$op = $order->get_fields_array('id,factory,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_bom_sub_user,pdc_bom_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $_SESSION['MSG'][] = "Error !!";
    redirect_page($PHP_SELF."?PHP_action=main");
}

$op = $op[0];
$wi_id = $wi->get_field($ord_num,'id');
$PHP_id = $wi_id[0];

$op['status'] = $utf_rn_num['status'];
$op['version'] = $utf_rn_num['version'];
$size = $size_des->get($op['size']);
$op['rn_num'] = $rn_num;
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);

$send_qty = $UTF8_MATERIALS->get_send_qty($ord_num);
$utf_bom_qty = $UTF8_MATERIALS->get_bom_materials_qty($rn_num);
$HTML = $BOM_OF_MATERIALS->get_wiqty_qty_edit_material($ord_num,$size,$send_qty,$utf_bom_qty);
$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];

$op['line_select']  = $arry2->select_id($monitor->get_line_id($op['factory']),$line,'PHP_line','','styled-select','');
$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;
$op['mat'] = $PHP_mat;

$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op, $AUTH, "bom_requi_edit_material.html");
break;



case "do_bom_requi_edit_material":
check_authority($AUTH,"edit");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
$line = $_GET['PHP_line'] ? $_GET['PHP_line'] : $_POST['PHP_line'];
$wi_id = $_GET['PHP_id'] ? $_GET['PHP_id'] : $_POST['PHP_id'];

$parm = array (
    "rn_num"    =>	$rn_num,
    "line"      =>	$line
);

if( !$id = $UTF8_MATERIALS->edit_requi_notify_line($parm) ){
    $_SESSION['MSG'][] = "Error ! Can't Update records!";
    redirect_page($PHP_SELF."?PHP_action=main");
} else {
    if( !$sts = $UTF8_MATERIALS->edit_requi_notify_bom($id,$wi_id,$_POST['PHP_qty']) ){
        $_SESSION['MSG'][] = "Error ! Can't Update records!";
        redirect_page($PHP_SELF."?PHP_action=main");
    } else {
        $msg = $_SESSION['MSG'][] = "Success Update RN# RN".$id;
        $log->log_add(0,$AUTH."A",$msg);
        redirect_page($PHP_SELF."?PHP_action=bom_requi_update_material&PHP_rn_num=".$rn_num);
    }
}
break;



case "bom_requi_update_material":
check_authority($AUTH,"edit");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
$utf_rn_num = $UTF8_MATERIALS->get_rn_num($rn_num);
$factory = $utf_rn_num['fty'];
$ord_num = $utf_rn_num['ord_num'];
$line = $utf_rn_num['line'];

if(!$op = $order->get_fields_array('id,factory,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_bom_sub_user,pdc_bom_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $_SESSION['MSG'][] = "Error !!";
    redirect_page($PHP_SELF."?PHP_action=main");
}

$op = $op[0];
$wi_id = $wi->get_field($ord_num,'id');
$PHP_id = $wi_id[0];

$op['status'] = $utf_rn_num['status'];
$op['version'] = $utf_rn_num['version'];
$size = $size_des->get($op['size']);
$op['rn_num'] = $rn_num;
$op['line'] = $monitor->get_line_name($line);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);

$utf_bom_qty = $UTF8_MATERIALS->get_bom_materials_qty($rn_num);

#帶入該分批數量的配比
$HTML = $BOM_OF_MATERIALS->get_wiqty_qty_send_material($ord_num,$size,$utf_bom_qty);

$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];
$op['send_qty'] = $HTML[4];
$bom_utf8 = $UTF8_MATERIALS->get_bom_materials($ord_num);
// print_r($bom_utf8);
$op['BHTML'] = $BOM_OF_MATERIALS->get_bom_added_qty($PHP_id,$size,$HTML[2],$bom_utf8);
$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['wi_id'] = $PHP_id;

$op['pdc_bom_sub_user'] = big52uni(get_user_html($op['pdc_bom_sub_user']));
$op['mat'] = $_GET['PHP_mat'];

$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/bom_of_materials.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op, $AUTH, "bom_requi_update_material.html");
break;



case "do_send_material_submit":
check_authority($AUTH,"add");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
if($f1 = $UTF8_MATERIALS->do_send_material_submit($rn_num)){
    $msg = $_SESSION['MSG'][] = "Successfully Submit RN# RN".$rn_num;
    $log->log_add(0,$AUTH."S",$msg);
    redirect_page($PHP_SELF."?PHP_action=bom_requi_view_material&PHP_rn_num=".$rn_num);
}

break;



case "do_send_material_confirm":
check_authority($AUTH,"admin");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
if($f1 = $UTF8_MATERIALS->do_send_material_confirm($rn_num)){
    $msg = $_SESSION['MSG'][] = "Successfully Confim RN# RN".$rn_num;
    $log->log_add(0,$AUTH."S",$msg);

    $messg  = '<a href="bom_mat_release.php?PHP_action=bom_requi_view_material&PHP_rn_num='.$rn_num.'">RN'.$rn_num.'</a>';
    $notify->system_msg_send('089-S','SA',$rn_num,$messg);
    redirect_page($PHP_SELF."?PHP_action=bom_requi_view_material&PHP_rn_num=".$rn_num);
}

break;



case "do_send_material_revise":
check_authority($AUTH,"admin");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
if($f1 = $UTF8_MATERIALS->do_send_material_revise($rn_num)){
    $msg = $_SESSION['MSG'][] = "Successfully Revise RN# RN".$rn_num;
    $log->log_add(0,$AUTH."R",$msg);
    redirect_page($PHP_SELF."?PHP_action=bom_requi_view_material&PHP_rn_num=".$rn_num);
}

break;



case "do_send_material_delete":
check_authority($AUTH,"admin");

$rn_num = $_GET['PHP_rn_num'] ? $_GET['PHP_rn_num'] : $_POST['PHP_rn_num'];
if($f1 = $UTF8_MATERIALS->do_send_material_delete($rn_num)){
    $msg = $_SESSION['MSG'][] = "Successfully Delete RN# RN".$rn_num;
    $log->log_add(0,$AUTH."D",$msg);
    redirect_page($PHP_SELF."?PHP_action=bom_mat_release_search");
}

break;















	
	
	case "do_requi_nofity_add":
		check_authority($AUTH,"add");
		
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





















































case "main2":
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
$m_sql= "s_order.order_num,cust.cust_init_name,s_order.creator,s_order.etd,s_order.pdc_status,s_order.pdc_cut_sub_user,s_order.pdc_cut_sub_date,s_order.factory,wi.id";
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
    $op['pages'][$k]['pdc_cut_sub_user'] = big52uni(get_user_html($v['pdc_cut_sub_user']));
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
        $msg = "Success update ". $ord_num;
        $_SESSION['MSG'][] = $msg;
        $log->log_add(0,$AUTH."E",$msg);
    } else {
        $_SESSION['MSG'][] = "Error ! Can't update records!";
    }
} else {
    $_SESSION['MSG'][] = "Error ! Can't update records!";
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
		
		page_display($op, $AUTH, $TPL_REQUI_NOTIFY_LIST);
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