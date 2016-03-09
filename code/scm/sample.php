<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');

// echo $PHP_action;
// -------------------------------------------------------------------------------------

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$CFG = $_SESSION['ITEM']['ADMIN_PERM'] ;
$op = array();

$AUTH = '080';

switch ($PHP_action) {
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# cfm_smpl
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "cfm_smpl":
check_authority($AUTH,$CFG[1]);

if (!$op = $smpl_ord->search(2)) {  
	$op['msg']= $smpl_ord->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

for ($i=0; $i < sizeof($op['sorder']); $i++)
{
	if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/s_".$op['sorder'][$i]['num'].".jpg")){
		$op['sorder'][$i]['main_pic'] = "./smpl_pic/s_".$op['sorder'][$i]['num'].".jpg";
	} else {
		$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
	}
	$img_size = GetImageSize($op['sorder'][$i]['main_pic']);
	if ($img_size[0] < $img_size[1]) $op['sorder'][$i]['height'] = 1;

}
$op['msg']= $smpl_ord->msg->get(2);
$op['cgi']= $parm;
if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op,$AUTH,'smpl_cfm_list.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# smpl_cfm_view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "smpl_cfm_view":
check_authority($AUTH,$CFG[1]);
		
$op['order'] = $smpl_ord->get($PHP_id);  //取出該筆記錄
if (!$op['order']) {
	$op['msg'] = $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['order']['stype'] =  '';
$op['order']['smpl_code'] += 0;
foreach($SMPL_TYPE as $key => $val){
	$op['order']['stype'] .= ( !empty($op['order']['smpl_code']) && $op['order']['smpl_code'] & $val )? $key.' , ' : '' ;
}

if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
	$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
} else {
	$op['main_pic'] = "./images/graydot.gif";
}

$logs = $smpl_log->search50($op['order']['num']);
if (!$logs){
	$op['msg'] = $smpl_log->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);
	break;
}

$smpl=substr($op['order']['num'],0,9);
if (!$op['ord_link']= $order->smpl_search($smpl))
{
	$op['ord_link_non']=1;
}

$op['logs'] = $logs['log'];
$op['log_records'] = $logs['records'];

$parm = $op['order'];
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

if (substr($user_dept,0,1)=="H" || substr($user_dept,0,1)=="L")  //判斷使用者部門是否為工廠
{                                                                //以分隔修改等權限
	if ($user_dept == $op['order']['dept'])$op['dept_edit']=1;
	if ($user_dept =="H0" && substr($op['order']['dept'],0,1)=="H")$op['dept_edit']=1;
	if ($user_dept =="L0" && substr($op['order']['dept'],0,1)=="L")$op['dept_edit']=1;
} else {
	$op['dept_edit']=1;
}

// 加入返回前頁 --------------
$img_size = GetImageSize($op['main_pic']);
if ($img_size[0] < $img_size[1]) $op['height'] = 1;

// creat LOG SUBJ combo box
$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

$op['msg'] = $smpl_ord->msg->get(2);
$op['search'] = '1';
$op['user_e']=$GLOBALS['SCACHE']['ADMIN']['name'];
if(isset($PHP_msg))$op['msg'][] = $PHP_msg;

page_display($op,$AUTH,'smpl_cfm_show.html');	
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# do_cfm_smpl
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_cfm_smpl":
check_authority($AUTH,$CFG[1]);

$f1 = $smpl_ord->cfm($PHP_id);

$redir_str = "sample.php?PHP_action=cfm_smpl";
redirect_page($redir_str);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "copy_smpl_order":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "copy_smpl_order":
	check_authority("008","add");

	$dt = decode_date(1);
	$year_code = substr($dt['year'],-1);
	
	// 新訂單號碼
	$smpl_serious = $dept->get_smpl_num($year_code, $PHP_cust);
	$smpl_serious.="-1";
	
	//copy smpl_ord
	$smpl_id = $smpl_ord->copy_smpl_ord($PHP_id,$smpl_serious);
	//copy smpl_wi
	$new_wi_id = $smpl_ord->copy_smpl_wi($PHP_wi_id,$smpl_id,$smpl_serious,$dt);
	//copy smpl_ti
	$smpl_ord->copy_smpl_ti($PHP_wi_id,$new_wi_id);
	//copy smpl_lots_use
	$smpl_ord->copy_smpl_lots_use($PHP_num,$smpl_serious);
	//copy smpl_acc_use
	$smpl_ord->copy_smpl_acc_use($PHP_num,$smpl_serious);
	
	//$log->log_add(0,"008A","append $smpl_serious [copy $PHP_num]");
	
	$msg = "Successful copy sample order ".$PHP_num;
	$dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$redir_str = "index2.php?PHP_action=smpl_ord_edit&PHP_id=".$smpl_id."&PHP_cust=".$PHP_cust."&PHP_dept=".$dept."&PHP_msg=".$msg."&PHP_copy_flag=1";
	redirect_page($redir_str);			
break;

// -------------------------------------------------------------------------------------
}   // end case ---------
?>
