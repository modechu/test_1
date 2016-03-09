<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');

session_register	('sch_parm');
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
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$op = array();

$P_LINE = array (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

session_register('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$para_cm = $para->get(0,'cf-cm');
$FTY_CM['CF'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

session_register('sp_date');
$sp_date = '2010-04-01';
// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "Remun":
 	check_authority('030',"view");
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$sales_dept_ary = get_full_sales_dept(); // 取出 業務的部門 [不含K0] ------

 		if ($user_dept =='HJ' || $user_dept=='LY' || $user_dept=='CF')
 		{
			$op['FTY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
 		}else{
			$op['FTY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');
 		}
 		$op['YEAR_select'] = $arry2->select($YEAR_WORK,'','PHP_year','select','');
 		$op['MONTH_select'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');

		$op['DEPT_select'] = $arry2->select($sales_dept_ary,"","PHP_dept","select","");  
		$op['sp_date'] = $sp_date;
		
		
	page_display($op, '030', $TPL_REMUN);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "remun_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "remun_add":
	check_authority('030',"add");
	if(!$PHP_FTY  )
	{
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------

		if ($user_dept =='HJ' || $user_dept=='LY' || $user_dept=='CF')
		{
			$op['FTY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
		}else{
			$op['FTY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');
		}
		
		$op['YEAR_select'] = $arry2->select($YEAR_WORK,'','PHP_year','select','');
		$op['MONTH_select'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');
		$op['DEPT_select'] = $arry2->select($sales_dept_ary,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept","select","");  

		$op['msg'][] = "Please select Factory first.";
		page_display($op, '030', $TPL_REMUN);
		break;
	}
	if(!$PHP_year) $PHP_year= date('Y');
	if(!$PHP_month) $PHP_month = date('m');
	$s_date = $PHP_year."-".$PHP_month."-01";
	$e_date = $PHP_year."-".$PHP_month."-31";
	$rem_date = $PHP_year."-".$PHP_month;
	$op['remun'] = $cost->search_output($s_date,$e_date,$PHP_FTY,$rem_date,$PHP_dept);
	$op['fty'] = $PHP_FTY;
	$op['mm'] = $rem_date;
	$op['dept'] = $PHP_dept;
	if(!$op['remun']) $op['msg'][] = "No Records.";
	$op['sp_date'] = $sp_date;
	
	$op['is_factory'] = is_dept($GLOBALS['SCACHE']['ADMIN']['dept']);
	//print_r($op);
	page_display($op, '030', $TPL_REMUN_ADD);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_remun_add":
		$parm = array('ord_num'			=>	$PHP_order,
									'acc_cost'		=>	$PHP_acc,
									'oth_cost'		=>	$PHP_oth,
									'exc_cost'		=>	$PHP_exc,
									'smpl'				=>	$PHP_smpl,
									'rem_qty'			=>	$PHP_qty,
									'out_month'		=>	$PHP_mm,
									'rem_id'			=>	$PHP_renum,
									'fty'					=>	$PHP_fty,
									'style'				=>	$PHP_style,
									'submit_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
									'rmk'					=>	$PHP_rmk,
									'dept'				=>	$PHP_dept
									);
		$s_cost = $PHP_cm + $PHP_acc + $PHP_oth + $PHP_exc;
		$t_cost = $s_cost * ($PHP_qty+$PHP_smpl);
		
		if(!$PHP_renum)	
		{
			if($PHP_fty == 'HJ') $head ='HJAP-'.date('y'); 
			if($PHP_fty == 'LY') $head ='LYAP-'.date('y');
			if($PHP_fty == 'CF') $head ='CFAP-'.date('y');
			$parm['num']=$cost->get_no($head,'num','remun');	//取得代工費申請的最後編號
			$id = $cost->add_main($parm);
			$parm['rem_id'] = $id;			
		}
		$id = $cost->add_det($parm);
		if (!$PHP_smpl)$PHP_smpl='&nbsp;';
		$message = "success add Cutting & Making for : ".$PHP_order."remid=".$parm['rem_id'];
		$log->log_add(0,"030A",$message);

		echo $message."|".$PHP_order."|".$PHP_style."|".$PHP_cm."|".$PHP_oth."|".$PHP_acc."|".$PHP_exc."|".$s_cost."|".$PHP_smpl."|";
		echo number_format($PHP_qty,0,'',',')."|".number_format($t_cost,2,'.',',')."|".$PHP_rmk."|";
		echo "<input type='image' src='images/del.png'  onclick=\"del_cost('".$parm['rem_id']."','$id','$PHP_ord_id',this,'$PHP_order')\">|";	
		echo $parm['rem_id'];		
	break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "remun_del":
		$f1 = $cost->del_remun($PHP_id,$PHP_rem_id,$PHP_ord_num);
		$message = "success deletet Cutting & Making |".$f1;
		$log->log_add(0,"030A",$message);

		echo $message;		
	break;
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "remun_view":
	check_authority('030',"view");
	$op = $cost->get($PHP_renum);

	if(!isset($PHP_FTY))$PHP_FTY='';
	if(!isset($PHP_year))$PHP_year='';
	if(!isset($PHP_month))$PHP_month='';
	if(isset($op['remun']))
	{
		$op['yy'] = substr($op['remun']['out_month'],0,4);
		$op['mm'] = substr($op['remun']['out_month'],4,2);
		$op['comp'] = $REMUNER[$op['remun']['fty']];
	}
		
/*		
		if(isset($PHP_sr_startno))
		{
			$back_str = "&PHP_FTY=".$PHP_FTY."&PHP_year=".$PHP_year."&PHP_month=".$PHP_month."&PHP_sr_startno=".$PHP_sr_startno;
		}else{
			if (!isset($PHP_back_str))$PHP_back_str='';
			$back_str =$PHP_back_str;
		}
		
*/

	$op['back_str'] = "&back=";
	$op['sp_date'] = $sp_date;
	$op['is_factory'] = is_dept($GLOBALS['SCACHE']['ADMIN']['dept']);
	$op['account_dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
	//print_r($GLOBALS['SCACHE']['ADMIN']['dept']);
	//print_r($op);
	page_display($op, '030', $TPL_REMUN_SHOW);		
	break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_file":
		check_authority('030',"add");
		
		$filename = $_FILES['PHP_pttn']['name'];	
		// echo $_FILES['PHP_pttn']['size'];	
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));
		
		$f_check = 0;
		$valid_type = array('pdf','jpg','gif','JPG','GIF','PNG','png');
		for ($i=0; $i<sizeof( $valid_type); $i++)
		{
			if ($valid_type[$i] == $ext)
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
			$parm = array(	"id"					=>  $PHP_renum,
											"file_des"		=>	$PHP_desp,
											"file_user"		=>	$user_name,
											"file_date"		=>	$today
			);
			
			$A = $fils->get_name_id('remun_file');			
			$pttn_name = $PHP_num."_".$A.".".$ext;  // 組合檔名
			$parm['file_name'] = $pttn_name;
//			$fils->	update_file($pttn_name,$A);
			
			$str_long=strlen($pttn_name);
			$upload = new Upload;
			
			$upload->setMaxSize(2072864);
			//$upload->uploadFile(dirname($PHP_SELF).'/cm_file/', 'other', 16, $pttn_name );
			
			$upload->uploadFile(dirname(__FILE__).'\cm_file\\', 'other', 16, $pttn_name );
			$upload->setMaxSize(2072864);
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if (!$A = $fils->upload_cm_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$message = "UPLOAD file of #".$PHP_num;
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

		
			
		$op = $cost->get($PHP_renum);
		
		if(!isset($PHP_FTY))$PHP_FTY='';
		if(!isset($PHP_year))$PHP_year='';
		if(!isset($PHP_month))$PHP_month='';
		if(isset($op['remun']))
		{
			$op['yy'] = substr($op['remun']['out_month'],0,4);
			$op['mm'] = substr($op['remun']['out_month'],4,2);
			$op['comp'] = $REMUNER[$op['remun']['fty']];
		}

		$op['back_str'] = $PHP_back_str;
		$op['msg'][] = $message;
		$op['sp_date'] = $sp_date;
		page_display($op, '030', $TPL_REMUN_SHOW);		
	break;	


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "remun_search":
check_authority('030',"view");

if(isset($PHP_year))
{
	$sch_parm = array();
	$sch_parm = array(
		"fty"			=>  $PHP_FTY,
		"year"			=>  $PHP_year,
		"month"			=>	$PHP_month,
		"sr_startno"	=>	$PHP_sr_startno,
		"action"		=>	$PHP_action
	);
}else{
	if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
}


$op = $cost->search_remun(1);		
$op['sp_date'] = $sp_date;
//		$back_str = "&PHP_FTY=".$PHP_FTY."&PHP_year=".$PHP_year."&PHP_month=".$PHP_month;
//		$op['back_str'] = $back_str;
page_display($op, '030', $TPL_REMUN_LIST);		
break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "remun_edit":
check_authority('030',"add");

$op = $cost->get($PHP_rem_id);
$op['back_str'] = $PHP_back_str;
$op['sp_date'] = $sp_date;

page_display($op, '030', $TPL_REMUN_EDIT);		
break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_remun_edit":
		$parm = array('ord_num'		=>	$PHP_order,
									'acc_cost'	=>	$PHP_acc,
									'oth_cost'	=>	$PHP_oth,
									'exc_cost'	=>	$PHP_exc,
									'smpl'			=>	$PHP_smpl,
									'rem_qty'		=>	$PHP_qty,
									'id'				=>	$PHP_id,
									'rmk'				=>	$PHP_rmk,
									);

		$id = $cost->edit_det($parm);
		$message = "success Update Cutting & Making for : ".$PHP_order;
		$log->log_add(0,"030E",$message);

		echo $message;		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "remun_submit":
check_authority('030',"add");

$f1 = $cost->submit_remun($PHP_rem_id,$GLOBALS['SCACHE']['ADMIN']['login_id']);
$op = $cost->get($PHP_rem_id);
$op['back_str'] = $PHP_back_str;
$op['comp'] = $REMUNER[$op['remun']['fty']];

$message = "success submit Cutting & Making for : ".$op['remun']['num'];
$log->log_add(0,"030S",$message);
$op['sp_date'] = $sp_date;

page_display($op, '030', $TPL_REMUN_SHOW);		
break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "remun_print":
check_authority('030',"view");

$op = $cost->get($PHP_renum);

include_once($config['root_dir']."/lib/class.pdf_remun.php");

$print_title="Cutting & Making";
$print_title2="NUM :".$op['remun']['num'];
$creator = $op['remun']['submit_user']." [ ".$op['remun']['submit_date']." ] ";
$mark = $op['remun']['num'];
$ary_title = $REMUNER[$op['remun']['fty']];
$pdf=new PDF_remun('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1,20);

for ($i=0; $i<sizeof($op['rem_det']); $i++)
{
    $op['rem_det'][$i]['qty'] = number_format($op['rem_det'][$i]['qty'],0,'',',');
    $op['rem_det'][$i]['rem_qty'] = number_format($op['rem_det'][$i]['rem_qty'],0,'',',');
    $op['rem_det'][$i]['ord_qty'] = number_format($op['rem_det'][$i]['ord_qty'],0,'',',');
    $op['rem_det'][$i]['cost'] = number_format($op['rem_det'][$i]['cost'],2,'',',');
    //$op['rem_det'][$i]['acc_cost'] += $op['rem_det'][$i]['oth_cost'] + $op['rem_det'][$i]['exc_cost'];
    $pdf->rem_table($op['rem_det'][$i]);
}

//$op['total_cost'] = number_format($op['total_cost'],2,'',',');


$pdf->SetFont('Arial','',10);
$pdf->Cell(20, 7,'Total',1,0,'C');
$pdf->Cell(41, 7,'',1,0,'L');
$pdf->Cell(27, 7,number_format($op['total_ord_qty'],0,'',','),1,0,'R');
$pdf->Cell(97.3, 7,'',1,0,'L');

$pdf->Cell(12, 7,number_format($op['total_smpl'],0,'',','),1,0,'R');
$pdf->Cell(15, 7,number_format($op['total_qty'],0,'',','),1,0,'R');
$pdf->Cell(20, 7,number_format($op['total_cost'],2,'',','),1,0,'R');

$pdf->Cell(48, 7,'',1,0,'L');

$pdf->mang_chk($op['remun']['submit_user'],$op['remun']['cfm_user'],$op['remun']['apv_user']);

//$pdf->hend_title($ary_title);
		
$name=$op['remun']['num'].'_rem.pdf';
$pdf->Output($name,'D');		
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_cm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "cfm_cm":
check_authority('030',"view");

$op = $cost->search_remun(2);			
$op['sp_date'] = $sp_date;

page_display($op, '030', $TPL_REMUN_CFM_LIST);		
break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_cm_view":
 	check_authority('030',"view");
		$op = $cost->get($PHP_renum);		
		$op['is_factory'] = is_dept($GLOBALS['SCACHE']['ADMIN']['dept']);
		$op['yy'] = substr($op['remun']['out_month'],0,4);
		$op['mm'] = substr($op['remun']['out_month'],4,2);
		$op['comp'] = $REMUNER[$op['remun']['fty']];
		
		$back_str = "&PHP_sr_cm=".$PHP_sr_cm;
		$op['back_str'] = $back_str;
		$op['sp_date'] = $sp_date;
		$op['account_dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
		//print_r($op);
		page_display($op, '030', $TPL_REMUN_CFM_SHOW);		
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_cfm_cm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_cfm_cm":
check_authority('030',"edit");

$f1 = $cost->cfm_remun($PHP_rem_id,$GLOBALS['SCACHE']['ADMIN']['login_id']);
$op = $cost->get($PHP_rem_id);
$op['back_str'] = $PHP_back_str;
$op['comp'] = $REMUNER[$op['remun']['fty']];

$message = "success confirm Cutting & Making for : ".$op['remun']['num'];
$log->log_add(0,"030S",$message);

//傳送工繳待核可意見訊息
// $messg  = '<a href="cost.php?PHP_action=rev_cm_view&PHP_renum='.$PHP_rem_id.'&PHP_sr_cost=0">'.$op['remun']['num'].'</a>';
// $notify->system_msg_send('030-R','DA',$op['remun']['num'],$messg);

$messg  = '<a href="cost.php?PHP_action=apv_cm_view&PHP_renum='.$PHP_rem_id.'&PHP_sr_cost=0">'.$op['remun']['num'].'</a>';
$notify->system_msg_send('4-6-V','GM',$op['remun']['num'],$messg);


$op['sp_date'] = $sp_date;
page_display($op, '030', $TPL_REMUN_CFM_SHOW);		
break;	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "reject_cm_cfm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "reject_cm_cfm":
 	check_authority('030',"edit");
 		$f1 = $cost->reject_remun($PHP_rem_id);
		$parm = array('rem_id'		=>	$PHP_rem_id,
									'log_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
									'k_time'		=>	date('Y-m-d'),
									'des'				=>	"CFM-Rejeck for :".$PHP_detail
									);
		 $f1 = $cost->add_cm_log($parm);
	
	
	
		$op = $cost->get($PHP_rem_id);
		$op['back_str'] = $PHP_back_str;
		$op['comp'] = $REMUNER[$op['remun']['fty']];

		$message = "success reject Cutting & Making for : ".$op['remun']['num'];
		$log->log_add(0,"030S",$message);
		$op['sp_date'] = $sp_date;
		page_display($op, '030', $TPL_REMUN_CFM_SHOW);		
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "apv_cm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apv_cm":
check_authority('067',"view");

$op = $cost->search_remun(3);		
$op['sp_date'] = $sp_date;

page_display($op, '067', $TPL_REMUN_APV_LIST);		
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "rev_cm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rev_cm":
check_authority('067',"view");

$op = $cost->search_remun(3);		
$op['sp_date'] = $sp_date;

page_display($op, '067', $TPL_REMUN_REV_LIST);		
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "rev_cm_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rev_cm_view":
check_authority('075',"view");

$op = $cost->get($PHP_renum);		

$op['yy'] = substr($op['remun']['out_month'],0,4);
$op['mm'] = substr($op['remun']['out_month'],4,2);
$op['comp'] = $REMUNER[$op['remun']['fty']];

$back_str = "&PHP_sr_cm=".$PHP_sr_startno;
$op['back_str'] = $back_str;
$op['sp_date'] = $sp_date;

page_display($op, '075', $TPL_REMUN_REV_SHOW);		
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "do_rev_cm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_rev_cm":
check_authority('075',"view");

$f1 = $cost->rev_remun($PHP_rem_id,$GLOBALS['SCACHE']['ADMIN']['login_id']);
$f1 = $cost->update_rem_fields('sc_mk', 1, $PHP_rem_id, 'remun_det');
$f1 = $cost->ord_cm_cost($PHP_rem_id);

$op = $cost->get($PHP_rem_id);
$op['back_str'] = $PHP_back_str;
$op['comp'] = $REMUNER[$op['remun']['fty']];

// $message = "success review Cutting & Making for : ".$op['remun']['num'];
$message = "success approval Cutting & Making for : ".$op['remun']['num'];
$log->log_add(0,"075S",$message);
$op['sp_date'] = $sp_date;

// 傳送工繳待核可意見訊息
// $messg  = '<a href="cost.php?PHP_action=apv_cm_view&PHP_renum='.$PHP_rem_id.'&PHP_sr_cost=0">'.$op['remun']['num'].'</a>';
// $notify->system_msg_send('4-6-V','GM',$op['remun']['num'],$messg);

page_display($op, '075', $TPL_REMUN_REV_SHOW);		
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "reject_cm_rev":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "reject_cm_rev":
check_authority('075',"view");
$f1 = $cost->reject_remun($PHP_rem_id);
$parm = array(
	'rem_id'	=>	$PHP_rem_id,
	'log_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'k_time'	=>	date('Y-m-d'),
	'des'		=>	"APV-Rejeck for :".$PHP_detail
);

$f1 = $cost->add_cm_log($parm);

$op = $cost->get($PHP_rem_id);
$op['back_str'] = $PHP_back_str;
$op['comp'] = $REMUNER[$op['remun']['fty']];

$message = "success reject Cutting & Making for : ".$op['remun']['num'];
$log->log_add(0,"075S",$message);
$op['sp_date'] = $sp_date;

page_display($op, '075', $TPL_REMUN_REV_SHOW);
break;			




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "apv_cm_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apv_cm_view":
check_authority('067',"view");
$op = $cost->get($PHP_renum);		

$op['yy'] = substr($op['remun']['out_month'],0,4);
$op['mm'] = substr($op['remun']['out_month'],4,2);
$op['comp'] = $REMUNER[$op['remun']['fty']];

$back_str = "&PHP_sr_cm=".$PHP_sr_startno;
$op['back_str'] = $back_str;
$op['sp_date'] = $sp_date;
page_display($op, '067', $TPL_REMUN_APV_SHOW);		
break;		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_apv_cm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apv_cm":
check_authority('067',"view");
$f1 = $cost->apv_remun($PHP_rem_id,$GLOBALS['SCACHE']['ADMIN']['login_id']);
$f1 = $cost->update_rem_fields('sc_mk', 1, $PHP_rem_id, 'remun_det');
# Confim 已經修正過  
// $f1 = $cost->ord_cm_cost($PHP_rem_id);

$op = $cost->get($PHP_rem_id);
$op['back_str'] = $PHP_back_str;
$op['comp'] = $REMUNER[$op['remun']['fty']];

$message = "success approval Cutting & Making for : ".$op['remun']['num'];
$log->log_add(0,"067S",$message);
$op['sp_date'] = $sp_date;
page_display($op, '067', $TPL_REMUN_APV_SHOW);		
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "reject_cm_apv":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "reject_cm_apv":
check_authority('067',"view");
$f1 = $cost->reject_remun($PHP_rem_id);
$parm = array('rem_id'		=>	$PHP_rem_id,
							'log_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
							'k_time'		=>	date('Y-m-d'),
							'des'				=>	"APV-Rejeck for :".$PHP_detail
							);
 $f1 = $cost->add_cm_log($parm);



$op = $cost->get($PHP_rem_id);
$op['back_str'] = $PHP_back_str;
$op['comp'] = $REMUNER[$op['remun']['fty']];

$message = "success approval Cutting & Making for : ".$op['remun']['num'];
$log->log_add(0,"067S",$message);
$op['sp_date'] = $sp_date;
page_display($op, '067', $TPL_REMUN_APV_SHOW);		
break;			





	
	
	
	
	
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost":
 	check_authority('031',"view");
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];		// 判定進入身份的Team

 		if ($user_dept =='HJ' || $user_dept=='LY' || $user_dept=='CF')
 		{
			$op['FTY_select'] = "<b>".$user_dept."</b><input type=hidden name='PHP_FTY' value='$user_dept'>";
 		}else{
			$op['FTY_select'] = $arry2->select($FACTORY,'','PHP_FTY','select','');
 		}
 		$op['YEAR_select'] = $arry2->select($YEAR_WORK,'','PHP_year','select','');
 		$op['MONTH_select'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');


		$where_str = $dept_id = '';
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		

		if ($team<>'MD' ) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			//業務部門 select選單
			$op['dept'] = $arry2->select($sales_dept_ary,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept","select","");  
		} else {
//			$where_str = " WHERE dept = '".$dept_id."' ";	
			$op['dept'] = 	 "<b>".$user_dept."</b><input type=hidden name='PHP_dept' value='$user_dept'>";	
		}



		$op['msg'] = $order->msg->get(2);
		// creat cust combo box
		// 取出 客戶代號
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
		if(isset($PHP_msg)) $op['msg'][]=$PHP_msg;

	page_display($op, '031', $TPL_SALESCOST);
	break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_add":
 	check_authority('031',"add");
 	  if(!$PHP_FTY || !$PHP_cust || !$PHP_dept)
 	  {
			$message = 'Please select factory, customer and department.';
			$redirect_str ="cost.php?PHP_action=cost&PHP_msg=".$message;
			redirect_page($redirect_str); 		    	    

		}
		if(!$PHP_year) $PHP_year= date('Y');
		if(!$PHP_month) $PHP_month = date('m');
		$s_date = $PHP_year."-".$PHP_month."-01";
		$e_date = $PHP_year."-".$PHP_month."-31";
		$rem_date = $PHP_year."-".$PHP_month;
		$parm = array( 's_date'		=> $s_date,
									 'e_date'		=>	$e_date,
									 'out_date'	=>	$rem_date,
									 'fty'			=>	$PHP_FTY,
									 'cust'			=>	$PHP_cust,
									 'dept'			=>	$PHP_dept
									 );
		$op['scost'] = $cost->search_cm($parm);
		$op['fty'] = $PHP_FTY;
		$op['mm'] = $rem_date;
		$op['dt'] = $parm;
		page_display($op, '031', $TPL_SALESCOST_ADD);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_cost_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_cost_add":

		$parm_main = array( 'cust'			=>	$PHP_cust,
												'out_month'	=>	$PHP_out_date,
												'fty'				=>	$PHP_fty,
												'dept'			=>	$PHP_dept,
												'open_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
												'open_date'	=>	date('Y-m-d'),
												'inv'				=>	$PHP_inv,
												);

		$parm_det = array('cost_id'			=>	$PHP_cost,
											'rem_id'			=>	$PHP_rem_id,
											'etd'					=>	$PHP_etd,
											'qty'					=>	$PHP_qty,
											'quota'				=>	$PHP_quota,
											'fob'					=>	$PHP_fob,
											'fab_cost'		=>	$PHP_fab_cost,
											'yy'					=>	$PHP_yy,
											'acc_cost'		=>	$PHP_acc_cost,
											'acc_f_cost'	=>	$PHP_fty_cost,
											'smpl_cost'		=>	$PHP_smpl,
											'comm'				=>	$PHP_comm,
											'cm'					=>	$PHP_cm,
											'remark'			=>	$PHP_remark,
									);
		if(!$PHP_cost)	
		{
			$num_hend = $PHP_fty."-".date('y').date('m').'-';
			$parm_main['num']=$cost->get_cost_no($num_hend,'sc_num','salescost');	//取得代工費申請的最後編號
			
			$id = $cost->add_cost_main($parm_main);
			$parm_det['cost_id'] = $id;			
		}
		$id = $cost->add_cost_det($parm_det);
		
		$rem_id = explode(',',$PHP_rem_id);
		for($i=0; $i<sizeof($rem_id); $i++)
		{
			$f1 = $cost->update_fields('sc_mk', 2, $rem_id[$i], 'remun_det');
		}
		
		$total = $PHP_qty * $PHP_fob;
		$fab_total = $PHP_fab_cost * $PHP_yy;
		$u_cost = $fab_total + $PHP_acc_cost + $PHP_fty_cost + $PHP_comm + $PHP_cm;
		$t_cost = $u_cost * $PHP_qty;
		$gross = $total - $t_cost;
		$gross_rate = $gross / $total * 100;
		
		$message = "success add sales cost for : ".$PHP_ord_num;
		$log->log_add(0,"47A",$message);

		echo $message."|".$PHP_ord_num."|".$PHP_style."|".$PHP_etd."|";
		echo "<P align=right>".number_format($PHP_qty,0,'',',')."pc</P>|<small>".$PHP_quota."</small>|";
		echo "<P align=right>".$PHP_fob."</P>|<P align=right>".number_format($total,2,'.',',')."</P>|";
		echo "<P align=right>".number_format($PHP_fab_cost,2,'.',',')."</P>|<P align=right>".number_format($PHP_yy,2,'.',',')."</P>|";
		echo "<P align=right>".number_format($total,2,'.',',')."</P>|<P align=right>".number_format($PHP_acc_cost,2,'.',',')."</P>|";
		echo "<P align=right>".number_format($PHP_fty_cost,2,'.',',')."</P>|<P align=right>".number_format($PHP_cm,2,'.',',')."</P>|";
		echo "<P align=right>".number_format($PHP_comm,2,'.',',')."</P>|<P align=right>".number_format($u_cost,2,'.',',')."</P>|";
		echo "<P align=right>".number_format($t_cost,2,'.',',')."</P>|<P align=right>".number_format($PHP_smpl,2,'.',',')."</P>|";
		echo "<P align=right>".number_format($gross,2,'.',',')."</P>|<P align=right>".number_format($gross_rate,2,'.',',')."%</P>|";
		echo $PHP_remark."|";
		echo "<input type='image' src='images/del.png'  onclick=\"del_cost('".$parm_det['cost_id']."','$id','$PHP_ord_id','$PHP_rem_id',this)\">|";	
		echo $parm_det['cost_id'];		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "cost_del":
		$f1 = $cost->del_cost($PHP_id,$PHP_cost_id);
		$f2 = $cost->update_fields('sc_mk', 1, $PHP_rem_id, 'remun_det');
		$message = "success deletet sales cost |".$f1;
		$log->log_add(0,"47A",$message);

		echo $message;		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_view":
 	check_authority('031',"view");
		$op = $cost->get_cost($PHP_cost);
		if(isset($PHP_sr_startno))
		{
			$back_str = "&PHP_FTY=".$PHP_FTY."&PHP_year=".$PHP_year."&PHP_month=".$PHP_month."&PHP_cust=".$PHP_cust."&PHP_dept=".$PHP_dept."&PHP_sr_startno=".$PHP_sr_startno;
		}else{
			if (!isset($PHP_back_str))$PHP_back_str='';
			$back_str = $PHP_back_str;
		}
		$op['back_str'] = $back_str;


		page_display($op, '031', $TPL_SALESCOST_SHOW);
		
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_add_view":
 	check_authority('031',"view");
		$f1 = $cost->update_fields('inv', $PHP_inv, $PHP_cost, 'salescost');
		$op = $cost->get_cost($PHP_cost);
		if(isset($PHP_sr_startno))
		{
			$back_str = "&PHP_FTY=".$PHP_FTY."&PHP_year=".$PHP_year."&PHP_month=".$PHP_month."&PHP_cust=".$PHP_cust."&PHP_dept=".$PHP_dept."&PHP_sr_startno=".$PHP_sr_startno;
		}else{
			if (!isset($PHP_back_str))$PHP_back_str='';
			$back_str = $PHP_back_str;
		}
		$op['back_str'] = $back_str;


		page_display($op, '031', $TPL_SALESCOST_SHOW);
		
	break;	




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_search":
 	check_authority('031',"view");

		$op = $cost->search_cost(1);
		$back_str = "&PHP_FTY=".$PHP_FTY."&PHP_year=".$PHP_year."&PHP_month=".$PHP_month."&PHP_cust=".$PHP_cust."&PHP_dept=".$PHP_dept;
		$op['back_str'] = $back_str;
		page_display($op, '031', $TPL_SALESCOST_LIST);
		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_edit":
 	check_authority('031',"add");

		$op = $cost->get_cost($PHP_cost);
		$op['back_str'] = $PHP_back_str;

		page_display($op, '031', $TPL_SALESCOST_edit);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_cost_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_cost_edit":


		$parm_det = array('etd'					=>	$PHP_etd,
											'qty'					=>	$PHP_qty,
											'quota'				=>	$PHP_quota,
											'fob'					=>	$PHP_fob,
											'fab_cost'		=>	$PHP_fab_cost,
											'yy'					=>	$PHP_yy,
											'acc_cost'		=>	$PHP_acc_cost,
											'acc_f_cost'	=>	$PHP_fty_cost,
											'smpl_cost'		=>	$PHP_smpl,
											'comm'				=>	$PHP_comm,
											'cm'					=>	$PHP_cm,
											'id'					=>	$PHP_id,
									);

		$id = $cost->edit_cost_det($parm_det);

		
		$message = "success edit sales cost for : ".$PHP_ord_num;
		$log->log_add(0,"47E",$message);

		echo $message;	
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_submit":
 	check_authority('031',"add");

		//submit記錄填入 : 狀態,日期及 submit的人
		$cost->cost_update_fld('status',2,$PHP_cost);
		$cost->cost_update_fld('submit_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_cost);
		$cost->cost_update_fld('submit_date',$TODAY,$PHP_cost);

		$op = $cost->get_cost($PHP_cost);

		$op['back_str'] = $PHP_back_str;
		$message = "success submit [ ".$op['cost']['out_month']." ] sales cost for  cust : ".$op['cost']['cust'].", fty : ".$op['cost']['fty'];
		$log->log_add(0,"47S",$message);

		$op['msg'][] = $message;
		page_display($op, '031', $TPL_SALESCOST_SHOW);
		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_cfm":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_cfm":
 	check_authority('031',"view");

		$op = $cost->search_cost(2);
		page_display($op, '031', $TPL_SALESCOST_CFM_LIST);
		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_cfm_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_cfm_view":
 	check_authority('031',"add");

		$op = $cost->get_cost($PHP_cost);
		$op['now_pp'] = $PHP_sr_cost;
		page_display($op, '031', $TPL_SALESCOST_CFM_SHOW);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_cost_cfm":
 	check_authority('031',"edit");

		$cost->cost_update_fld('status',4,$PHP_cost);
		$cost->cost_update_fld('cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_cost);
		$cost->cost_update_fld('cfm_date',$TODAY,$PHP_cost);

		$op = $cost->get_cost($PHP_cost);
		$message = "success confirm [ ".$op['cost']['out_month']." ] sales cost for  cust : ".$op['cost']['cust'].", fty : ".$op['cost']['fty'];
		$log->log_add(0,"48S",$message);



		$op['msg'][] = $message;
		$op['now_pp'] = $PHP_now_pp;
		page_display($op, '031', $TPL_SALESCOST_CFM_SHOW);
		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_cfm_rjt":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_cfm_rjt":
 	check_authority('031',"edit");

		$cost->cost_update_fld('status',1,$PHP_cost);
		$op = $cost->get_cost($PHP_cost);
		
		$parm = array(	'cost_id'		=>	$PHP_cost,
										'log_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'k_time'		=>	$TODAY,
										'subj'			=>	'CFM-Reject',
										'des'				=>	$PHP_des,
										);
		$cost->add_cost_log($parm);
		$message = "success CFM-reject [ ".$op['cost']['out_month']." ] sales cost for  cust : ".$op['cost']['cust'].", fty : ".$op['cost']['fty'];
		$log->log_add(0,"48S",$message);

		$op['msg'][] = $message;
		$op['now_pp'] = $PHP_now_pp;
		page_display($op, '031', $TPL_SALESCOST_CFM_SHOW);
		
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_apv":
 	check_authority('067',"view");

		$op = $cost->search_cost(3);
		page_display($op, '067', $TPL_SALESCOST_APV_LIST);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_cfm_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_apv_view":
 	check_authority('067',"add");

		$op = $cost->get_cost($PHP_cost);
		$op['now_pp'] = $PHP_sr_cost;
		page_display($op, '067', $TPL_SALESCOST_APV_SHOW);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_cost_apv":
 	check_authority('067',"add");

		$cost->cost_update_fld('status',6,$PHP_cost);
		$cost->cost_update_fld('apv_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_cost);
		$cost->cost_update_fld('apv_date',$TODAY,$PHP_cost);

		$op = $cost->get_cost($PHP_cost);
		$message = "success Approval [ ".$op['cost']['out_month']." ] sales cost for  cust : ".$op['cost']['cust'].", fty : ".$op['cost']['fty'];
		$log->log_add(0,"49S",$message);

		$op['msg'][] = $message;
		$op['now_pp'] = $PHP_now_pp;
		page_display($op, '067', $TPL_SALESCOST_APV_SHOW);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_cfm_rjt":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_apv_rjt":
 	check_authority('067',"add");

		$cost->cost_update_fld('status',1,$PHP_cost);
		$op = $cost->get_cost($PHP_cost);
		
		$parm = array(	'cost_id'		=>	$PHP_cost,
										'log_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'k_time'		=>	$TODAY,
										'subj'			=>	'APV-Reject',
										'des'				=>	$PHP_des,
										);
		$cost->add_cost_log($parm);
		$message = "success APV-reject [ ".$op['cost']['out_month']." ] sales cost for  cust : ".$op['cost']['cust'].", fty : ".$op['cost']['fty'];
		$log->log_add(0,"49S",$message);

		$op['msg'][] = $message;
		$op['now_pp'] = $PHP_now_pp;
		page_display($op, '067', $TPL_SALESCOST_CFM_SHOW);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_revise":
 	check_authority('031',"add");

		$op = $cost->get_cost($PHP_cost);
		$cost->cost_update_fld('status',0,$PHP_cost);
		$cost->cost_update_fld('version',($PHP_ver+1),$PHP_cost);
		$op['rev'] = 1;
		page_display($op, '031', $TPL_SALESCOST_edit);
		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_rev_view":
 	check_authority('031',"add");
		$f1 = $cost->update_fields('inv', $PHP_inv, $PHP_cost, 'salescost');		
		$op = $cost->get_cost($PHP_cost,1);

		page_display($op, '031', $TPL_SALESCOST_SHOW_REV);
		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_cost":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_print":
 	check_authority('031',"view");
		$op = $cost->get_cost($PHP_cost,1);
		
include_once($config['root_dir']."/lib/class.pdf_cost.php");

$print_title="Sales Cost Analysis";
$print_title2="INV# : ".$op['cost']['inv']."          Dept : ".$op['cost']['dept_name']."    Cust : ".$op['cost']['cust_init']."    FTY : ".$op['cost']['fty'];
$print_title3="Version : ".$op['cost']['version'];
$creator = $op['cost']['submit_user']." [ ".$op['cost']['submit_date']." ] ";
$mark = $op['cost']['out_month'];
$pdf=new PDF_cost('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1,20);


for ($i=0; $i<sizeof($op['scost']); $i++)
{
		$op['scost'][$i]['rem_qty']      = number_format($op['scost'][$i]['rem_qty'],0,'',',');
		$op['scost'][$i]['uprice']       = number_format($op['scost'][$i]['uprice'],2,'.',',');
		$op['scost'][$i]['o_in_cost']    = number_format($op['scost'][$i]['o_in_cost'],2,'.',',');
		$op['scost'][$i]['mat_u_cost']   = number_format($op['scost'][$i]['mat_u_cost'],2,'.',',');
		$op['scost'][$i]['mat_useage']   = number_format($op['scost'][$i]['mat_useage'],2,'.',',');
		$op['scost'][$i]['o_mat']        = number_format($op['scost'][$i]['o_mat'],2,'.',',');
		$op['scost'][$i]['o_acc']       = number_format($op['scost'][$i]['o_acc'],2,'.',',');
		$op['scost'][$i]['fty_acc_cost'] = number_format($op['scost'][$i]['fty_acc_cost'],2,'.',',');
		$op['scost'][$i]['fty_cm']       = number_format($op['scost'][$i]['cm_cost'],2,'.',',');
		$op['scost'][$i]['comm_fee']     = number_format($op['scost'][$i]['comm_fee'],2,'.',',');
		$op['scost'][$i]['o_a_cost']     = number_format($op['scost'][$i]['o_a_cost'],2,'.',',');
		$op['scost'][$i]['o_cost']       = number_format($op['scost'][$i]['o_cost'],2,'.',',');
		$op['scost'][$i]['o_gross']      = number_format($op['scost'][$i]['o_gross'],2,'.',',');
		$op['scost'][$i]['o_gross_rate'] = number_format($op['scost'][$i]['o_gross_rate'],2,'.',',');


		$op['scost'][$i]['qty']          = number_format($op['scost'][$i]['qty'],0,'',',');
		$op['scost'][$i]['fob']          = number_format($op['scost'][$i]['fob'],2,'.',',');
		$op['scost'][$i]['c_in_cost']    = number_format($op['scost'][$i]['c_in_cost'],2,'.',',');
		$op['scost'][$i]['fab_cost']     = number_format($op['scost'][$i]['fab_cost'],2,'.',',');
		$op['scost'][$i]['yy']           = number_format($op['scost'][$i]['yy'],2,'.',',');
		$op['scost'][$i]['c_fab']        = number_format($op['scost'][$i]['c_fab'],2,'.',',');
		$op['scost'][$i]['acc_cost']     = number_format($op['scost'][$i]['acc_cost'],2,'.',',');
		$op['scost'][$i]['acc_f_cost']   = number_format($op['scost'][$i]['acc_f_cost'],2,'.',',');
		$op['scost'][$i]['cm']           = number_format($op['scost'][$i]['cm'],2,'.',',');
		$op['scost'][$i]['comm']         = number_format($op['scost'][$i]['comm'],2,'.',',');
		$op['scost'][$i]['c_a_cost']     = number_format($op['scost'][$i]['c_a_cost'],2,'.',',');
		$op['scost'][$i]['c_cost']       = number_format($op['scost'][$i]['c_cost'],2,'.',',');
		$op['scost'][$i]['c_gross']      = number_format($op['scost'][$i]['c_gross'],2,'.',',');
		$op['scost'][$i]['c_gross_rate'] = number_format($op['scost'][$i]['c_gross_rate'],2,'.',',');
		$op['scost'][$i]['smpl_cost']    = number_format($op['scost'][$i]['smpl_cost'],2,'.',',');


		$op['scost'][$i]['d_qty']          = number_format($op['scost'][$i]['d_qty'],0,'',',');
		$op['scost'][$i]['d_fob']          = number_format($op['scost'][$i]['d_fob'],2,'.',',');
		$op['scost'][$i]['d_in_cost']    = number_format($op['scost'][$i]['d_in_cost'],2,'.',',');
		$op['scost'][$i]['d_cm']           = number_format($op['scost'][$i]['d_cm'],2,'.',',');



		$pdf->cost_table($op['scost'][$i]);
}
  $pdf->mang_chk($op['cost']);

//$pdf->hend_title($ary_title);		
		
$name=$op['cost']['out_month'].'_cost.pdf';
$pdf->Output($name,'D');		
break;



case "cm2excel":
$op = $cost->cm2excel($_POST);
// print_r($_POST);
// print_r($op);
$is_factory = is_dept($GLOBALS['SCACHE']['ADMIN']['dept']);
$rec = $op;
//print_r($op);
//exit;
$filename = 'CM_'.$_POST['PHP_FTY'].'_'.$_POST['PHP_year'];
require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");
// exit;
function HeaderingExcel($filename) {
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename" );
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
}

# HTTP headers
$Fty = $_POST['PHP_FTY'];
$Year = $_POST['PHP_year'];
HeaderingExcel($Fty.'_'.$Year.'.xls');

# Creating a workbook
$workbook = new Workbook("-");

# Creating the first worksheet
$worksheet1 =& $workbook->add_worksheet('report');

$now = $GLOBALS['THIS_TIME'];

# 寫入 title
$formatot =& $workbook->add_format();
$formatot->set_size(10);
$formatot->set_align('center');
$formatot->set_color('white');
$formatot->set_pattern(1);
$formatot->set_fg_color('navy');
$formatot->set_num_format(4);

$f3 =& $workbook->add_format(); //置右
$f3->set_size(10);
$f3->set_align('right');
$f3->set_num_format(4);

$f5 =& $workbook->add_format();  //灰底白字置中
$f5->set_size(10);
$f5->set_align('center');
$f5->set_color('white');
$f5->set_pattern(1);
$f5->set_fg_color('grey');

$f6 =& $workbook->add_format();  //灰底白字置右
$f6->set_size(10);
$f6->set_color('white');
$f6->set_pattern(1);
$f6->set_align('right');
$f6->set_fg_color('grey');
$f6->set_num_format(4);

$f7 =& $workbook->add_format();  //綠底白字置中
$f7->set_size(10);
$f7->set_align('center');
$f7->set_color('white');
$f7->set_pattern(1);
$f7->set_fg_color('green');

$f8 =& $workbook->add_format();  //綠底白字置右
$f8->set_size(10);
$f8->set_color('white');
$f8->set_pattern(1);
$f8->set_align('right');
$f8->set_fg_color('green');	 
$f8->set_num_format(4); 


$f9 =& $workbook->add_format();  //
$f9->set_size(10);
$f9->set_color('white');
$f9->set_pattern(1);
$f9->set_align('right');
$f9->set_fg_color('orange');
$f9->set_num_format(4);
/*
if($is_factory)
{
	#title
	$clumn = array( 11, 10, 6, 12, 12, 8, 10, 10, 12, 12, 6, 10, 10, 10, 20);

	#16
	$title = array(
		'ORDER#', 		    #11
		'ORD QTY', 	        #10
		'Fnl. IE', 	        #6
		'Style #',          #12
		'Style',		    #12
		'C.M.',		        #8
		'Other Cost',		#10
		'Acc Cost',		    #10
		'Exception Cost',   #12
		'Handling Fee',     #12
		'@Cost',			#6
		'Smpl. Q\'ty',		#10
		'SHPed. Q\'ty',	    #10
		'Cost',		        #10
		'Remark'		    #20
	);
}
else
{
	#title
	$clumn = array( 11, 10, 6, 6, 12, 12, 8, 10, 10, 12, 12, 6, 10, 10, 10, 20);

	#16
	$title = array(
		'ORDER#', 		    #11
		'ORD QTY', 	        #10
		'Cal. IE', 	        #6
		'Fnl. IE', 	        #6
		'Style #',          #12
		'Style',		    #12
		'C.M.',		        #8
		'Other Cost',		#10
		'Acc Cost',		    #10
		'Exception Cost',   #12
		'Handling Fee',     #12
		'@Cost',			#6
		'Smpl. Q\'ty',		#10
		'SHPed. Q\'ty',	    #10
		'Cost',		        #10
		'Remark'		    #20
	);
}
*/
	#title
	$clumn = array( 11, 10, 6, 6, 12, 12, 8, 10, 10, 12, 12, 6, 10, 10, 10, 20);

	#16
	$title = array(
		'ORDER#', 		    #11
		'ORD QTY', 	        #10
		'Cal. IE', 	        #6
		'Fnl. IE', 	        #6
		'Style #',          #12
		'Style',		    #12
		'C.M.',		        #8
		'Other Cost',		#10
		'Acc Cost',		    #10
		'Exception Cost',   #12
		'Handling Fee',     #12
		'@Cost',			#6
		'Smpl. Q\'ty',		#10
		'SHPed. Q\'ty',	    #10
		'Cost',		        #10
		'Remark'		    #20
	);


for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
$worksheet1->write_string(0,1,'CM Export ( '.$Fty.' ) '.$Year );
$worksheet1->write_string(0,10,$THIS_TIME);
for ($o=0; $o< sizeof($title); $o++){ $worksheet1->write_string(1,$o,$title[$o],$formatot);}

#內容
$j=2;
$total_ord_qty = $total_smpl = $total_qty = $total_cost = 0;
// echo sizeof($rec);
for($i=0; $i<sizeof($rec); $i++)
{
    // $dept_v = $order->get_field_value('dept','',$rpt[$i]['ord_num'],'s_order');
    // $chief = $dept->get_fields("chief"," where dept_code='$dept_v'");
    // $emp_id = $user->get_fields("emp_id"," where login_id = '$chief[0]'");
    // $dept = $apb->get_apb_dept($rec[$i]['dept']);
    # 項次 2012/07/10 取消計次
    // $pnum[$rec[$i]['ord_num'].$rec[$i]['ship_date']][] = $rec[$i]['ship_date']; 

    $worksheet1->write_string($j,0,'C.M. :',$formatot);
    $worksheet1->write_string($j,1,$rec[$i]['remun']['num'],$formatot);
	$worksheet1->write_string($j,2,'',$formatot);
	$worksheet1->write_string($j,3,'',$formatot);
	$worksheet1->write_string($j,4,'',$formatot);
	$worksheet1->write_string($j,5,'',$formatot);
	$worksheet1->write_string($j,6,'',$formatot);
	$worksheet1->write_string($j,7,'',$formatot);
	$worksheet1->write_string($j,8,'',$formatot);
	$worksheet1->write_string($j,9,'',$formatot);
	$worksheet1->write_string($j,10,'',$formatot);
	$worksheet1->write_string($j,11,'',$formatot);
	$worksheet1->write_string($j,12,'',$formatot);
	$worksheet1->write_string($j,13,'',$formatot);
	$worksheet1->write_string($j,14,'',$formatot);
	$worksheet1->write_string($j,15,'',$formatot);
	/*
	if($is_factory)
	{
		$worksheet1->write_string($j,2,'',$formatot);
		$worksheet1->write_string($j,3,'',$formatot);
		$worksheet1->write_string($j,4,'',$formatot);
		$worksheet1->write_string($j,5,'',$formatot);
		$worksheet1->write_string($j,6,'',$formatot);
		$worksheet1->write_string($j,7,'',$formatot);
		$worksheet1->write_string($j,8,'',$formatot);
		$worksheet1->write_string($j,9,'',$formatot);
		$worksheet1->write_string($j,10,'',$formatot);
		$worksheet1->write_string($j,11,'',$formatot);
		$worksheet1->write_string($j,12,'',$formatot);
		$worksheet1->write_string($j,13,'',$formatot);
		$worksheet1->write_string($j,14,'',$formatot);
	}
	else
	{
		$worksheet1->write_string($j,2,'',$formatot);
		$worksheet1->write_string($j,3,'',$formatot);
		$worksheet1->write_string($j,4,'',$formatot);
		$worksheet1->write_string($j,5,'',$formatot);
		$worksheet1->write_string($j,6,'',$formatot);
		$worksheet1->write_string($j,7,'',$formatot);
		$worksheet1->write_string($j,8,'',$formatot);
		$worksheet1->write_string($j,9,'',$formatot);
		$worksheet1->write_string($j,10,'',$formatot);
		$worksheet1->write_string($j,11,'',$formatot);
		$worksheet1->write_string($j,12,'',$formatot);
		$worksheet1->write_string($j,13,'',$formatot);
		$worksheet1->write_string($j,14,'',$formatot);
		$worksheet1->write_string($j,15,'',$formatot);
	}
    */

    $j++;
    // for ($o=0; $o< sizeof($clumn); $o++){ $worksheet1->set_column(0,$o,$clumn[$o]);}
    for ($o=0; $o< sizeof($title); $o++){ $worksheet1->write_string($j,$o,$title[$o],$f5);}
    $j++;

    
    // 'ORDER#', 		    #10 1
    // 'ORD QTY', 	        #8  2
    // 'Cal. IE', 	        #6  3
    // 'Fnl. IE', 	        #6  4
    // 'Style #',           #10 5
    // 'Style',		        #10 6
    // 'C.M.',		        #8  7
    // 'Other Cost',		#10 8
    // 'Acc Cost',		    #10 9
    // 'Exception Cost',    #12 10
    // 'Handling Fee',      #12 11
    // '@Cost',			    #6  12
    // 'Smpl. Q\'ty',		#10 13
    // 'SHPed. Q\'ty',	    #10 14
    // 'Cost',		        #6  15
    // 'Remark'		        #12 16
    
    
    for($s=0; $s<sizeof($rec[$i]['rem_det']); $s++){
        $worksheet1->write_string($j,0,$rec[$i]['rem_det'][$s]['ord_num']);
        $worksheet1->write_string($j,1,$rec[$i]['rem_det'][$s]['ord_qty']);
		if($is_factory)
		{
			if($rec[$i]['rem_det'][$s]['dept'] == $GLOBALS['SCACHE']['ADMIN']['dept'])
			{
				$worksheet1->write_number($j,2,$rec[$i]['rem_det'][$s]['ie1']);
			}
			else
			{
				//$worksheet1->write_number($j,2,$rec[$i]['rem_det'][$s]['ie1']);
				$worksheet1->write_string($j,2,'');
			}
		}
		else
		{
			$worksheet1->write_number($j,2,$rec[$i]['rem_det'][$s]['ie1']);
		}
		$worksheet1->write_number($j,3,$rec[$i]['rem_det'][$s]['ie2']);
		$worksheet1->write_string($j,4,$rec[$i]['rem_det'][$s]['style_num']);
		$worksheet1->write_string($j,5,$rec[$i]['rem_det'][$s]['style_des']);
		$worksheet1->write_number($j,6,$rec[$i]['rem_det'][$s]['fty_cm']);
		$worksheet1->write_number($j,7,$rec[$i]['rem_det'][$s]['oth_cost']);
		$worksheet1->write_number($j,8,$rec[$i]['rem_det'][$s]['acc_cost']);
		$worksheet1->write_number($j,9,$rec[$i]['rem_det'][$s]['exc_cost']);
		$worksheet1->write_number($j,10,$rec[$i]['rem_det'][$s]['handling_fee']);
		$worksheet1->write_number($j,11,$rec[$i]['rem_det'][$s]['a_cost']);
		$worksheet1->write_number($j,12,$rec[$i]['rem_det'][$s]['smpl']);
		$worksheet1->write_number($j,13,$rec[$i]['rem_det'][$s]['rem_qty']);
		$worksheet1->write_number($j,14,$rec[$i]['rem_det'][$s]['cost']);
		$worksheet1->write_string($j,15,$rec[$i]['rem_det'][$s]['rmk']);
		/*
		if($is_factory)
		{
			$worksheet1->write_number($j,2,$rec[$i]['rem_det'][$s]['ie2']);
			$worksheet1->write_string($j,3,$rec[$i]['rem_det'][$s]['style_num']);
			$worksheet1->write_string($j,4,$rec[$i]['rem_det'][$s]['style_des']);
			$worksheet1->write_number($j,5,$rec[$i]['rem_det'][$s]['fty_cm']);
			$worksheet1->write_number($j,6,$rec[$i]['rem_det'][$s]['oth_cost']);
			$worksheet1->write_number($j,7,$rec[$i]['rem_det'][$s]['acc_cost']);
			$worksheet1->write_number($j,8,$rec[$i]['rem_det'][$s]['exc_cost']);
			$worksheet1->write_number($j,9,$rec[$i]['rem_det'][$s]['handling_fee']);
			$worksheet1->write_number($j,10,$rec[$i]['rem_det'][$s]['a_cost']);
			$worksheet1->write_number($j,11,$rec[$i]['rem_det'][$s]['smpl']);
			$worksheet1->write_number($j,12,$rec[$i]['rem_det'][$s]['rem_qty']);
			$worksheet1->write_number($j,13,$rec[$i]['rem_det'][$s]['cost']);
			$worksheet1->write_string($j,14,$rec[$i]['rem_det'][$s]['rmk']);
		}
		else
		{
			$worksheet1->write_number($j,2,$rec[$i]['rem_det'][$s]['ie1']);
			$worksheet1->write_number($j,3,$rec[$i]['rem_det'][$s]['ie2']);
			$worksheet1->write_string($j,4,$rec[$i]['rem_det'][$s]['style_num']);
			$worksheet1->write_string($j,5,$rec[$i]['rem_det'][$s]['style_des']);
			$worksheet1->write_number($j,6,$rec[$i]['rem_det'][$s]['fty_cm']);
			$worksheet1->write_number($j,7,$rec[$i]['rem_det'][$s]['oth_cost']);
			$worksheet1->write_number($j,8,$rec[$i]['rem_det'][$s]['acc_cost']);
			$worksheet1->write_number($j,9,$rec[$i]['rem_det'][$s]['exc_cost']);
			$worksheet1->write_number($j,10,$rec[$i]['rem_det'][$s]['handling_fee']);
			$worksheet1->write_number($j,11,$rec[$i]['rem_det'][$s]['a_cost']);
			$worksheet1->write_number($j,12,$rec[$i]['rem_det'][$s]['smpl']);
			$worksheet1->write_number($j,13,$rec[$i]['rem_det'][$s]['rem_qty']);
			$worksheet1->write_number($j,14,$rec[$i]['rem_det'][$s]['cost']);
			$worksheet1->write_string($j,15,$rec[$i]['rem_det'][$s]['rmk']);
		}
		*/
        
        // $worksheet1->write_string($j,16,$rec[$i]['rem_det'][$s]['ord_num']);
        $j++;
    }
    
    $worksheet1->write_string($j,0,'Total :',$f7);
    $worksheet1->write_number($j,1,$rec[$i]['total_ord_qty'],$f8);
	$worksheet1->write_string($j,2,'',$f7);
	$worksheet1->write_string($j,3,'',$f7);
	$worksheet1->write_string($j,4,'',$f7);
	$worksheet1->write_string($j,5,'',$f7);
	$worksheet1->write_string($j,6,'',$f7);
	$worksheet1->write_string($j,7,'',$f7);
	$worksheet1->write_string($j,8,'',$f7);
	$worksheet1->write_string($j,9,'',$f7);
	$worksheet1->write_string($j,10,'',$f7);
	$worksheet1->write_string($j,11,'',$f7);
	$worksheet1->write_number($j,12,$rec[$i]['total_smpl'],$f8);
	$worksheet1->write_number($j,13,$rec[$i]['total_qty'],$f8);
	$worksheet1->write_number($j,14,$rec[$i]['total_cost'],$f8);
	$worksheet1->write_string($j,15,'',$f7);
	/*
	if($is_factory)
	{
		$worksheet1->write_string($j,2,'',$f7);
		$worksheet1->write_string($j,3,'',$f7);
		$worksheet1->write_string($j,4,'',$f7);
		$worksheet1->write_string($j,5,'',$f7);
		$worksheet1->write_string($j,6,'',$f7);
		$worksheet1->write_string($j,7,'',$f7);
		$worksheet1->write_string($j,8,'',$f7);
		$worksheet1->write_string($j,9,'',$f7);
		$worksheet1->write_string($j,10,'',$f7);
		$worksheet1->write_number($j,11,$rec[$i]['total_smpl'],$f8);
		$worksheet1->write_number($j,12,$rec[$i]['total_qty'],$f8);
		$worksheet1->write_number($j,13,$rec[$i]['total_cost'],$f8);
		$worksheet1->write_string($j,14,'',$f7);
	}
	else
	{
		$worksheet1->write_string($j,2,'',$f7);
		$worksheet1->write_string($j,3,'',$f7);
		$worksheet1->write_string($j,4,'',$f7);
		$worksheet1->write_string($j,5,'',$f7);
		$worksheet1->write_string($j,6,'',$f7);
		$worksheet1->write_string($j,7,'',$f7);
		$worksheet1->write_string($j,8,'',$f7);
		$worksheet1->write_string($j,9,'',$f7);
		$worksheet1->write_string($j,10,'',$f7);
		$worksheet1->write_string($j,11,'',$f7);
		$worksheet1->write_number($j,12,$rec[$i]['total_smpl'],$f8);
		$worksheet1->write_number($j,13,$rec[$i]['total_qty'],$f8);
		$worksheet1->write_number($j,14,$rec[$i]['total_cost'],$f8);
		$worksheet1->write_string($j,15,'',$f7);
	}
    */

    $j++;
    $j++;
    
    $total_ord_qty += $rec[$i]['total_ord_qty'];
    $total_smpl += $rec[$i]['total_smpl'];
    $total_qty += $rec[$i]['total_qty'];
    $total_cost += $rec[$i]['total_cost'];
}
$worksheet1->write_string($j,0,'Sub Total :',$f9);
$worksheet1->write_number($j,1,$total_ord_qty,$f9);
$worksheet1->write_string($j,2,'',$f9);
$worksheet1->write_string($j,3,'',$f9);
$worksheet1->write_string($j,4,'',$f9);
$worksheet1->write_string($j,5,'',$f9);
$worksheet1->write_string($j,6,'',$f9);
$worksheet1->write_string($j,7,'',$f9);
$worksheet1->write_string($j,8,'',$f9);
$worksheet1->write_string($j,9,'',$f9);
$worksheet1->write_string($j,10,'',$f9);
$worksheet1->write_string($j,11,'',$f9);
$worksheet1->write_number($j,12,$total_smpl,$f9);
$worksheet1->write_number($j,13,$total_qty,$f9);
$worksheet1->write_number($j,14,$total_cost,$f9);
$worksheet1->write_string($j,15,'',$f9);
/*
if($is_factory)
{
	$worksheet1->write_string($j,2,'',$f9);
	$worksheet1->write_string($j,3,'',$f9);
	$worksheet1->write_string($j,4,'',$f9);
	$worksheet1->write_string($j,5,'',$f9);
	$worksheet1->write_string($j,6,'',$f9);
	$worksheet1->write_string($j,7,'',$f9);
	$worksheet1->write_string($j,8,'',$f9);
	$worksheet1->write_string($j,9,'',$f9);
	$worksheet1->write_string($j,10,'',$f9);
	$worksheet1->write_number($j,11,$total_smpl,$f9);
	$worksheet1->write_number($j,12,$total_qty,$f9);
	$worksheet1->write_number($j,13,$total_cost,$f9);
	$worksheet1->write_string($j,14,'',$f9);

}
else
{
	$worksheet1->write_string($j,2,'',$f9);
	$worksheet1->write_string($j,3,'',$f9);
	$worksheet1->write_string($j,4,'',$f9);
	$worksheet1->write_string($j,5,'',$f9);
	$worksheet1->write_string($j,6,'',$f9);
	$worksheet1->write_string($j,7,'',$f9);
	$worksheet1->write_string($j,8,'',$f9);
	$worksheet1->write_string($j,9,'',$f9);
	$worksheet1->write_string($j,10,'',$f9);
	$worksheet1->write_string($j,11,'',$f9);
	$worksheet1->write_number($j,12,$total_smpl,$f9);
	$worksheet1->write_number($j,13,$total_qty,$f9);
	$worksheet1->write_number($j,14,$total_cost,$f9);
	$worksheet1->write_string($j,15,'',$f9);
}
*/


$workbook->close();

break;
//-------------------------------------------------------------------------

}   // end case ---------

?>
