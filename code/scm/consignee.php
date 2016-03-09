<?php
session_start();
session_register	('sch_parm');




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";
//工繳取得
session_register	('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

//系統提示參數取得
session_register	('alt_value');
$para_cm = $para->get(0,'una-production_etd');
$alt_value['una_prd'] = $para_cm['set_value'];
$para_cm = $para->get(0,'un-submit_etd');
$alt_value['un_sub'] = $para_cm['set_value'];
$para_cm = $para->get(0,'un-schedule_etd');
$alt_value['un_schld'] = $para_cm['set_value'];
$para_cm = $para->get(0,'unfinish_etd');
$alt_value['un_fish'] = $para_cm['set_value'];
$para_cm = $para->get(0,'un-eta_etd');
$alt_value['un_eta'] = $para_cm['set_value'];
$para_cm = $para->get(0,'un-smplapv_etd');
$alt_value['un_smpl'] = $para_cm['set_value'];
$para_cm = $para->get(0,'no_patten');
$alt_value['no_pttn'] = $para_cm['set_value'];
$para_cm = $para->get(0,'smpl_apv_undo');
$alt_value['smpl_apv_undo'] = $para_cm['set_value'];
$para_cm = $para->get(0,'none_wiws');
$alt_value['none_wiws'] = $para_cm['set_value'];

//系統Message刪除量
$tmp = $para->get(0,'msg_del');
$msg_del = $tmp['set_value'];

//訂單ETD,ETP限制
$tmp = $para->get(0,'ord_append_ETP');
$ord_append_ETP_limit = increceDaysInDate($TODAY,$tmp['set_value']);
$tmp = $para->get(0,'ord_append_ETD');
$ord_append_ETD_limit = increceDaysInDate($TODAY,$tmp['set_value']);

$ord_submit_ETP_limit = $TODAY;
$tmp = $para->get(0,'ord_submit_ETD');
$ord_submit_ETD_limit = increceDaysInDate($TODAY,$tmp['set_value']);

session_register	('FTY_ETD_LIMIT');
$FTY_ETD_LIMIT = '2008-11-31';


//系統提示參數取得
session_register	('pre_su');
$para_cm = $para->get(0,'top_su');
$pre_su['t'] = $para_cm['set_value'];
$para_cm = $para->get(0,'button_su');
$pre_su['b'] = $para_cm['set_value'];



$op = array();

//$ACC = $acc->get_acc_name();


// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================

 
 
 //----------------------------------------------------------------
 //			job 11  cust
 //----------------------------------------------------------------
    case "consignee":
 		check_authority("006","view");
		$in_id = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$in_dept = '';
		$where_str ='';
		$manager ='';
		$dept_select = array();
		

		$sales_dept = get_sales_dept(); // 取出 業務的部門 [不含K0] ------


	
		// for ($i=0; $i<count($sales_dept);$i++){
			// if($in_id == $sales_dept[$i]){
				// $in_dept = $sales_dept[$i];   // 如果是業務部進入 則dept_code 指定該業務部---
			// }
		// }
		
		if (!$in_dept) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			$dept_select = $arry2->select($sales_dept,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept_code","select","");  //業務部門 select選單
			$dept_search = $arry2->select($sales_dept,$GLOBALS['SCACHE']['ADMIN']['dept'],"SCH_dept","select","");  //業務部門 select選單
		} else {
//			$where_str = " WHERE dept = '".$in_dept."' ";
			$dept_search = '<b>'.$in_id.'</b><input type=hidden name=SCH_dept value='.$in_id.'>';
		}


		$where_str .= " order by cust_s_name";			
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 



		//   因為 search 將 $op重置 所以再帶入其它參數
		$op['dept_code'] = $in_dept;
		$op['dept_select'] = $dept_select;
		$op['manager_flag'] = $manager;
		$op['dept_search'] =	$dept_search ;
		$op['msg'] = $cust->msg->get(2);

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
		
		page_display($op, "006", $TPL_CONSIGNEE_SEARCH);		    	    
		break;




//----------------------------------------------------------------
//			job 11  cust
//----------------------------------------------------------------
case "consignee_search":
check_authority("006","view");

if(isset($SCH_cust))
{
	
	$sch_parm = array();
	$sch_parm = array(
		"cust"			=>  $SCH_cust,
		"name"			=>  $SCH_name,
		"f_name"		=>  $SCH_f_name,
		"dept"			=>	$SCH_dept,
		"sr_startno"	=>	$PHP_sr_startno,
		"action"		=>	$PHP_action
	);
	
}else{
	if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
}

if (!$op = $cust->search_csge(2)) {    // 叫出所屬部門的全部客戶記錄
	$op['msg']= $cust->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['msg'] = $cust->msg->get(2);
page_display($op, "006", $TPL_CONSIGNEE);		    	    
break;


//=======================================================
case "consignee_add":
check_authority("006","add");
if ($PHP_dept_code ==""){
	$op['msg'][] = "sorry! Please choice dept. first!";

	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;

	$redir_str = $PHP_SELF.'?PHP_action=cust';
	redirect_page($redir_str);

	break;

}

//		$op['country_select'] = $arry2->select($COUNTRY,"","PHP_cust_country","select","");  //業務部門 select選單

$where_str = " order by cust_s_name";			
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
for ($i=0; $i< sizeof($cust_def); $i++)
{
	$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
}
$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

$op['dept_code'] = $PHP_dept_code;
page_display($op, "006", $TPL_CONSIGNEE_ADD);			    	    
break;



//=======================================================
    case "consignee_add_sub":
		check_authority("006","add");


		$op['cust_select'] =  $PHP_cust."<input type=hidden name='PHP_cust' value='".$PHP_cust."'>"; 
		$op['dept_code'] = $PHP_dept;
		page_display($op, "006", $TPL_CONSIGNEE_ADD_SUB);			    	    
		break;

//=======================================================
case "do_consignee_add":
check_authority("006","add");

$parm = array(	
"dept"		=>	mysql_real_escape_string($PHP_dept),
"cust"		=>	mysql_real_escape_string($PHP_cust),
"s_name"	=>	mysql_real_escape_string($PHP_s_name),
"f_name"	=>	mysql_real_escape_string($PHP_f_name),
"tel"		=>	mysql_real_escape_string($PHP_tel),
"addr"		=>	mysql_real_escape_string($PHP_addr),
"cont"		=>	mysql_real_escape_string($PHP_cont)
);

// $op['csge'] = $parm;

$f1 = $cust->add_csge($parm);
if ($f1) {  // 成功輸入資料時

    $message= "Append consignee:".$PHP_s_name." for dept.: [".$PHP_dept."] cust :[".$PHP_cust."]";

    # 記錄使用者動態
    $log->log_add(0,"16A",$message);

    if (!$op = $cust->search_csge(0)) {    // 叫出所屬部門的全部客戶記錄
        $op['msg']= $cust->msg->get(2);
        $layout->assign($op);
        $layout->display($TPL_ERROR);  		    
        break;
    }

    //   因為 search 將 $op重置 所以再帶入其它參數
    $op['back_str'] = "&SCH_cust=&SCH_name=&SCH_dept=";
    $op['msg'] = $cust->msg->get(2);
    $op['msg'][] = $message;		
    page_display($op, "006", $TPL_CONSIGNEE);	    	    
    break;
} else {  // 當沒有成功輸入新增user的欄位時....
    $op['csge'] = $parm;
    $where_str = "order by cust_s_name";			
    $cust_def = $cust->get_fields('cust_init_name',$where_str);
    $cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
    for ($i=0; $i< sizeof($cust_def); $i++)
    {
        $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
    }
    $op['cust_select'] =  $arry2->select($cust_value,$PHP_cust,'PHP_cust','select','',$cust_def_vue); 
    $op['dept_code'] = $parm['dept'];
    $op['msg'] = $cust->msg->get(2);
    page_display($op, "006", $TPL_CONSIGNEE_ADD);    	    
    break;
}



//=======================================================
case "do_consignee_add_sub":
check_authority("006","add");

$parm = array(	
"dept"		=>	mysql_real_escape_string($PHP_dept),
"cust"		=>	mysql_real_escape_string($PHP_cust),
"s_name"	=>	mysql_real_escape_string($PHP_s_name),
"f_name"	=>	mysql_real_escape_string($PHP_f_name),
"tel"		=>	mysql_real_escape_string($PHP_tel),
"addr"		=>	mysql_real_escape_string($PHP_addr),
"cont"		=>	mysql_real_escape_string($PHP_cont)
);

//				$op['csge'] = $parm;

$f1 = $cust->add_csge($parm);
$message= "Append consignee:".$PHP_s_name." for dept.: [".$PHP_dept."] cust :[".$PHP_cust."]";
# 記錄使用者動態
$log->log_add(0,"16A",$message);
echo "<script>window.close();</script>";  
break;



//========================================================================================	
case "consignee_view":
check_authority("006","view");
$op['csge'] = $cust->get_csge($PHP_id);  //取出該筆記錄

if (!$op['csge']) {
	$op['msg'] = $cust->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['msg'] = $cust->msg->get(2);

page_display($op, "006", $TPL_CONSIGNEE_VIEW);	    	    
break;

//=======================================================
case "consignee_edit":
check_authority("006","edit");
$op['csge'] = $cust->get_csge($PHP_id);  //取出該筆記錄
print_r($op);
if (!$op['csge']) {
	$op['msg'] = $cust->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}


$op['msg'] = $cust->msg->get(2);

page_display($op, "006", $TPL_CONSIGNEE_EDIT);
break;

//=======================================================
case "do_consignee_edit":
check_authority("006","edit");
$parm = array(
	"f_name"	=>	mysql_real_escape_string($PHP_f_name),
	"tel"		=>	mysql_real_escape_string($PHP_tel),
	"addr"		=>	mysql_real_escape_string($PHP_addr),
	"cont"		=>	mysql_real_escape_string($PHP_cont),
	"code"		=>	mysql_real_escape_string($PHP_code),
	"id"		=>	$PHP_id
);

if (!$cust->edit_csge($parm)) {
	$op['msg'] = $cust->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;	    
}


$op['csge'] = $cust->get_csge($PHP_id);  //取出該筆記錄

			# 記錄使用者動態
$message = "Update Consignee:[".$op['csge']['s_name']."]context‧";
$log->log_add(0,"16E",$message);

$op['msg'] = $cust->msg->get(2);
$op['msg'][] = $message;

page_display($op, "006", $TPL_CONSIGNEE_VIEW);    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "cust_del":

						// 必需要 manager login 才能真正的刪除 user
		if(!$admin->is_power(1,1,"view") && !($GLOBALS['SCACHE']['ADMIIN']['id'] == 'SA')){ 
			$op['msg'][] = "sorry! 您沒有這項權限!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['cust'] = $cust->get($PHP_id);
		
		if (!$cust->del($PHP_id)) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# 記錄使用者動態

		$message = "Delete customer:[".$op['cust']['cust_s_name']."] Record。";
		$log->log_add(0,"11D",$message);

	//--------------------------------------------------2005/0908

		$in_id = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$in_dept = '';
		$where_str ='';
		$manager ='';
		$dept_select = array();

		$sales_dept = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		if ($in_id=='HJ' || $in_id=='CL' || $in_id=='WX' || $in_id	=='LY')
		{
			$in_dept=$in_id	;
		}else{ for ($i=0; $i<count($sales_dept);$i++){
			// if($in_id == $sales_dept[$i]){
				// $in_dept = $sales_dept[$i];   // 如果是業務部進入 則dept_code 指定該業務部---
			// }
		}
		
		}
		if (!$in_dept) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			$dept_select = $arry2->select($sales_dept,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept_code","select","");  //部門別select選單
		} else {
			$where_str = " WHERE dept = '".$in_dept."' ";
		}

			if (!$op = $cust->search(0,$where_str)) {    // 叫出所屬部門的全部客戶記錄
				$op['msg']= $cust->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		//   因為 search 將 $op重置 所以再帶入其它參數
		$op['dept_code'] = $in_dept;
		$op['dept_select'] = $dept_select;
		$op['manager_flag'] = $manager;

		$op['msg'] = $cust->msg->get(2);
		$op['msg'][] = $message;
			unset($argv);  // 刪除 參數			
			page_display($op, 1, 1, $TPL_CUST); 		    	    
		break;

  
  
  
  

	
//-------------------------------------------------------------------------

}   // end case ---------

?>
