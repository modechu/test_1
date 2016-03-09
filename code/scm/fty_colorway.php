<?php
#
#
#
session_start();
// echo $PHP_action.'<br>';
#
#
#
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/fty_colorway.class.php");
$FTY_COLORWAY = new FTY_COLORWAY();
if (!$FTY_COLORWAY->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
# Template
$TPL_FTY_COLORWAY_MAIN = 'fty_colorway_main.html';
$TPL_FTY_COLORWAY_LIST = 'fty_colorway_list.html';
$TPL_FTY_COLORWAY_VIEW = 'fty_colorway_view.html';
$TPL_FTY_COLORWAY_EDIT = 'fty_colorway_edit.html';
$TPL_FTY_COLORWAY_DETAIL = 'fty_colorway_detail.html';

#
#
#
$AUTH = '111';
#
#
#
switch ($PHP_action) {
#
#
#
#
#
#
# :main
default;
case "main":
check_authority($AUTH,"view");

if(in_array($_SESSION['SCACHE']['ADMIN']['dept'], $FACTORY)){
    $op['fty_select'] = $arry2->select(array($_SESSION['SCACHE']['ADMIN']['dept']), $_SESSION['SCACHE']['ADMIN']['dept'], "PHP_factory", "styled-select","");
}else{
    $op['fty_select'] = $arry2->select($FACTORY, '', "PHP_factory", "styled-select","");
}
    
$op['css'] = array( 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/fty_colorway.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' );

page_display($op,$AUTH,$TPL_FTY_COLORWAY_MAIN);
break;
#
#
#
#
#
#
# :search_colorway 
case "search_colorway":
check_authority($AUTH,"view");

$factory = $_GET['PHP_factory'] ? $_GET['PHP_factory'] : $_POST['PHP_factory'];
$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

$table = "s_order,wi";
$m_sql= "s_order.order_num,s_order.creator,s_order.etd,s_order.pdc_cut_sub_user,s_order.pdc_cut_sub_date,s_order.pdc_status";
if( $factory || $ord_num ) {
    $where_str = "WHERE s_order.order_num = wi.wi_num and wi.status = '2' and s_order.status >= '4' and s_order.status != '13' and s_order.m_status = '3' ";
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
// print_r($op);
foreach($op['pages'] as $k => $v){
    
    $op['pages'][$k]['pdc_cut_sub_user'] = big52uni(get_user_html($v['pdc_cut_sub_user']));
}

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/fty_colorway.js' , 'js/jquery.blockUI.js' );

page_display($op,$AUTH,$TPL_FTY_COLORWAY_LIST);
break;
#
#
#
#
#
#
# :view_colorway 
case "view_colorway":
check_authority($AUTH,"view");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if(!$op = $order->get_fields_array('id,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
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

$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['pdc_cut_sub_user'] = big52uni(get_user_html($op['pdc_cut_sub_user']));
$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/fty_colorway.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op,$AUTH,$TPL_FTY_COLORWAY_VIEW);
break;
#
#
#
#
#
#
# :edit_colorway 
case "edit_colorway":
check_authority($AUTH,"view");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if(!$op = $order->get_fields_array('id,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $op['msg'] = $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op = $op[0];

$size = $size_des->get($op['size']);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);
$HTML = $bom->get_wiqty_qty_edit($ord_num,$size);
$op['PHTML'] = $HTML[0];
$op['CHTML'] = $HTML[1];

$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/fty_colorway.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op,$AUTH,$TPL_FTY_COLORWAY_EDIT);
break;
#
#
#
#
#
#
# :detail_colorway 
case "detail_colorway":
check_authority($AUTH,"view");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if(!$op = $order->get_fields_array('id,order_num,cust,cust_ver,dept,style,qty,size,etd,status,revise,pdc_status,pdc_cut_sub_user,pdc_cut_sub_date,pdc_version,unit'," WHERE order_num = '".$ord_num."'")){
    $op['msg'] = $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op = $op[0];

$size = $size_des->get($op['size']);
$op['size_scale'] = $size['size_scale'];
$op['cust_iname'] = $cust->get_cust_name($op['cust'],$op['cust_ver']);
$HTML = $bom->get_wiqty_qty_edit($ord_num,$size);
$op['PHTML'] = $HTML[0];
$op['DHTML'] = $HTML[3];

$op['pic_url'] = _pic_url('big','120',3,'picture',$ord_num,'jpg','','');
$op['pdc_cut_sub_user'] = big52uni(get_user_html($op['pdc_cut_sub_user']));
$op['css'] = array( 'css/scm.css' , 'css/bom.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/fty_colorway.js' , 'js/jquery.blockUI.js' , 'js/bom.js' , 'js/open_detial.js' );

page_display($op,$AUTH,$TPL_FTY_COLORWAY_DETAIL);
break;
#
#
#
#
#
#
# :update_colorway 
case "update_colorway":
check_authority($AUTH,"edit");

if( !$op = $FTY_COLORWAY->update_colorway($_POST['PHP_color'],$_POST['PHP_qty']) ){
    $_SESSION['MSG'][] = "Error ! Can't Append records!";
} else {
    $_SESSION['MSG'][] = $message = "Successfully Append Records! ";
    $log->log_add(0,$AUTH."E",$message);
}

header("Location: ".$PHP_SELF."?PHP_action=view_colorway&PHP_ord_num=".$_POST['PHP_ord_num']);
break;
#
#
#
#
#
#
# :submit_colorway 
case "submit_colorway":
check_authority($AUTH,"edit");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if( !$op = $FTY_COLORWAY->submit_colorway($ord_num) ){
    $_SESSION['MSG'][] = "Error ! Can't Submit records! #:".$ord_num;
} else {
    $_SESSION['MSG'][] = $message = "Successfully Submit Records! #:".$ord_num;
    $log->log_add(0,$AUTH."E",$message);
}

header("Location: ".$PHP_SELF."?PHP_action=view_colorway&PHP_ord_num=".$_POST['PHP_ord_num']);
break;
#
#
#
#
#
#
# :revise_colorway 
case "revise_colorway":
check_authority($AUTH,"edit");

$ord_num = $_GET['PHP_ord_num'] ? $_GET['PHP_ord_num'] : $_POST['PHP_ord_num'];

if( !$op = $FTY_COLORWAY->revise_colorway($ord_num) ){
    $_SESSION['MSG'][] = "Error ! Can't Revise records! #:".$ord_num;
} else {
    $_SESSION['MSG'][] = $message = "Successfully Revise Records! #:".$ord_num;
    $log->log_add(0,$AUTH."E",$message);
}

header("Location: ".$PHP_SELF."?PHP_action=view_colorway&PHP_ord_num=".$_POST['PHP_ord_num']);
break;
} # CASE END
?>
