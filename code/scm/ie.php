<?php
##################  2004/11/10  ########################
#			index.php  �D�{��
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [�Ͳ��s�y][�Ͳ�����]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();

//�uú���o
session_register('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ym-cm');
$FTY_CM['YM'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

session_register	('pdt_range');
$para_cm = $para->get(0,'pdt_range');
$pdt_range = $para_cm['set_value'];

session_register	('schedule_var');
$para_cm = $para->get(0,'schedule_var');
$schedule_var = $para_cm['set_value'];

session_register	('sch_finish_rate');
$para_cm = $para->get(0,'sch_finish_rate');
$sch_finish_rate = $para_cm['set_value'];

include_once($config['root_dir']."/lib/class.monitor.php");
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for monitor class"; exit; }
include_once($config['root_dir']."/lib/class.transport.php");
$transport = new TRANSPORT();
if (!$transport->init($mysql,"log")) { print "error!! cannot initialize database for ORDER SHIFT class"; exit; }
include_once($config['root_dir']."/lib/pdtion.class.php");
$pdtion = new PDTION();
if (!$pdtion->init($mysql,"log")) { print "error!! cannot initialize database for pdtion class"; exit; }
include_once($config['root_dir']."/lib/pdt_finish.class.php");
$pdt_finish = new PDT_FINISH();
if (!$pdt_finish->init($mysql,"log")) { print "error!! cannot initialize database for pdt_finish class"; exit; }

echo $PHP_action;
switch ($PHP_action) {
//= start case ====================================================

//-------------------------------------------------------------------------------------
//			 job 35   IE �O��
//-------------------------------------------------------------------------------------
case "IE_record":
check_authority('025',"view");
$op['msg'] = $order->msg->get(2);		

// creat cust combo box
$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');  	
$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
for ($i=0; $i< sizeof($cust_def); $i++)
{
$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
}
$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

//080725message�W�[		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op, '025', $TPL_IE);		    	    
break;



//=======================================================
case "ie_search":
check_authority('025',"view");

//print_r($_GET);
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������

if(isset($PHP_order_num))
{
	$sch_parm = array();
	$sch_parm = array(	
        "PHP_order_num"		    =>  $PHP_order_num,
        "PHP_cust"				=>	$PHP_cust,
        "PHP_ref"				=>	$PHP_ref,
        "PHP_factory"			=>	$PHP_factory,
        'SCH_style'				=>	$SCH_style,
        'SCH_sample'			=>	$SCH_sample,
        "PHP_sr_startno"	    =>	$PHP_sr_startno,
        "PHP_action"			=>	$PHP_action
	);
}else{
	if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
}

if (!$op = $order->search(2)) {   // �Ȩ��X s_order tabel���� ��� ����pdtion
	$op['msg']= $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

for ($i=0; $i<sizeof($op['sorder']); $i++)
{
	if (substr($user_dept,0,1)=="H" || substr($user_dept,0,1)=="L")
	{
		if ($user_dept == $op['sorder'][$i]['dept'])$op['sorder'][$i]['dept_edit']=1;
		if ($user_dept =="H0" && substr($op['sorder'][$i]['dept'],0,1)=="H")$op['sorder'][$i]['dept_edit']=1;
		if ($user_dept =="L0" && substr($op['sorder'][$i]['dept'],0,1)=="L")$op['sorder'][$i]['dept_edit']=1;
	}else{
		$op['sorder'][$i]['dept_edit']=1;
	}
	$op['sorder'][$i]['final_check'] = $order->check_ftycm($op['sorder'][$i]['order_num']);
    $op['sorder'][$i]['statuss'] = get_ord_status($op['sorder'][$i]['status']);
	if(file_exists($GLOBALS['config']['root_dir']."/ord_ie/".$op['sorder'][$i]['order_num']."_ie2.xls")){
		$op['sorder'][$i]['ie2_file'] = $op['sorder'][$i]['order_num']."_ie2.xls";
	} else {
	}				
}
// print_r($op['sorder']);
// $op['cgi']= $parm; final_check
$op['PHP_sr_startno'] = $PHP_sr_startno;
$op['msg'] = $order->msg->get(2);
if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
// print_r($op);
page_display($op, '025', $TPL_IE_LIST);
break;



//=======================================================
case "ie_upload":
check_authority('025',"add");

$S_ROOT = dirname(__FILE__);
$Flag = substr(($S_ROOT),2,1);

$ext = preg_replace("/.*\.([^.]+)$/","\\1",  $_FILES['PHP_up_file']['name']);
$filename = $PHP_ord_num.'.'.$ext;
// echo $S_ROOT.$Flag."ie_analysis".$Flag.$filename;
if(!empty($filename) && file_exists($S_ROOT.$Flag."ie_analysis".$Flag.$filename)){
	unlink($S_ROOT.$Flag."ie_analysis".$Flag.$filename);
}

$upload = new Upload;

if ( $_FILES['PHP_up_file']['size'] > $upload->max_file_size ){
	$mesg = "UPLOAD FILE IS OVER 3M!";
	$redir_str = "ie.php?PHP_action=ie_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_msg=".$mesg;	
	redirect_page($back_str);
} else {

	$upload->UploadFiles($S_ROOT.$Flag."ie_analysis".$Flag, 'other', 20, $filename );

	if (!$upload){
		$op['msg'][] = $upload;
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	
	$order->update_ie_file(array('ie_file'=>$ext,'order_num'=>$PHP_ord_num));

	$mesg = "SUCCESSFULLY UPDATE IE ANALYSIS , ON ORDER :".$PHP_ord_num;
	$log->log_add(0,"025E",$mesg);
	
}

$redir_str = "ie.php?PHP_action=ie_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_msg=".$mesg;			
redirect_page($redir_str);

break;


//=======================================================
case "ie_cancel":
check_authority('025',"edit");

$S_ROOT = dirname(__FILE__);
$Flag = substr(($S_ROOT),2,1);

$filename = $PHP_ord_num.'.'.$ie_file;
// echo $S_ROOT.$Flag."ie_analysis".$Flag.$filename;
if(!empty($filename) && file_exists($S_ROOT.$Flag."ie_analysis".$Flag.$filename)){
	unlink($S_ROOT.$Flag."ie_analysis".$Flag.$filename);
}

$order->update_ie_file(array('ie_file'=>'','order_num'=>$PHP_ord_num));

$mesg = "SUCCESSFULLY CANCEL IE ANALYSIS , ON ORDER :".$PHP_ord_num;
$log->log_add(0,"025E",$mesg);

$redir_str = "ie.php?PHP_action=ie_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_msg=".$mesg;			
redirect_page($redir_str);

break;



//=======================================================
case "download":
check_authority('025',"view");
header("Content-type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header("Pragma:public");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Content-Disposition: attachment; filename=".$_GET['fname']."");
header("Pragma: no-cache");
header("Expires: 0");
readfile("./".$_GET['fdir']."/".$_GET['fname']); 
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "upload_order_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "upload_order_pattern":
check_authority('025',"add");
$op['back_str'] = $PHP_back_str;
$filename = $_FILES['PHP_pttn']['name'];
$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

// if ($ext == "mdl"){   // �W���ɪ����ɦW�� mdl �� -----
	#pttn �[�J��Ʈw

    $num = $order->get_order_ptn_num_ext($PHP_id);
    $pttn_name = $PHP_num."-".$num.'.'.$ext;  // ���w�� mdl ���ɦW
	$parm = array (
		"ord_id"			=>	$PHP_id,
		"file_name"			=>	$pttn_name,
		"file_des"			=>	$PHP_des,
		"file_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		"file_date"			=>	date("Y-m-d H:i:s")
	);
	
	$f1 = $order->add_ord_pttn($parm);
	
	// upload pattern file to server
	
    
	$upload = new Upload;
	$S_ROOT = dirname(__FILE__);
	$Flag = substr(($S_ROOT),2,1);
	//$upload->uploadFile(dirname($PHP_SELF).'/order_pttn/', 'pattern', 16, $pttn_name );
	$upload->uploadFiles($S_ROOT.$Flag.'order_pttn'.$Flag, 'pattern', 16, $pttn_name );
	
	if (!$upload){
		$op['msg'][] = $upload;				
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	$A=$order->update_field('ptn_upload', $GLOBALS['TODAY'], $PHP_id); //�O��pttn�W�Ǥ��			
	$message = "UPLOAD Pattern of ".$pttn_name;
	$log->log_add(0,"025E",$message);
// } else {  // �W���ɪ����ɦW  ���O  mdl �� -----

	// $message = "upload file is incorrect format ! Please re-send.";
// }

$redir_str = "ie.php?PHP_action=ie_record_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
redirect_page($redir_str);
	/*	
//----------------------------------------------------
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		$op['back_str'] = $PHP_back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

		$log->log_add(0,"025E",$message);

		$op['msg'][] = $message;
			page_display($op, '025', $TPL_IE_REC_SHOW); 		
		break;
*/
//=======================================================
    case "order_pttn_resend":
		check_authority('025',"add");
		$A=$order->update_field('ptn_upload', '0000-00-00', $PHP_id); //�M��pttn�W�Ǥ��		
		$A=$order->update_field('paper_ptn','0', $PHP_id); //�O����paper pattern	

		$message = "DEL exist pattern for resend for ".$PHP_num;
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"025E",$message);

		$redir_str = "ie.php?PHP_action=ie_record_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);

/*
//----------------------------------------------------			
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_order_num=".$PHP_order_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_style=".$SCH_style."&SCH_sample=".$SCH_sample;

		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

		$message = "DEL exist pattern for resend for #".$PHP_num;
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"025E",$message);

		$op['msg'][] = $message;
		page_display($op, '025', $TPL_IE_REC_SHOW); 
		break;
*/

//=======================================================
    case "order_pttn_paper":
		check_authority('025',"add");
				
		$A=$order->update_field('ptn_upload', $TODAY, $PHP_id); //�W�Ǥ��	
		$A=$order->update_field('paper_ptn','1', $PHP_id); //�O����paper pattern	
		
		$op['order'] = $order->get($PHP_id);
		$message = "Upload Paper Pattern for order ".$op['order']['order_num'];
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"025P",$message);
	
		$redir_str = "ie.php?PHP_action=ie_record_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);

//----------------------------------------------------			
/*
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_order_num=".$PHP_order_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_style=".$SCH_style."&SCH_sample=".$SCH_sample;

		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

		$message = "Upload Paper Pattern for order #".$op['order']['order_num'];
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"025P",$message);

		$op['msg'][] = $message;
		page_display($op, '025', $TPL_IE_REC_SHOW); 
		break;
*/		
//=======================================================
case "ie_record_view":
check_authority('025',"view");
		
/*
		$op['cgi'] = array(	"cgiget"	=>  $cgiget,
							"cgino"		=>  $cgino,
							"cgi_1"		=>	$cgi_1,
							"cgi_2"		=>	$cgi_2,
							"cgi_3"		=>	$cgi_3,
							"cgi_4"		=>	$cgi_4,
							"cgi_5"		=>	$cgi_5,
							"cgi_6"		=>	$cgi_6,
							"cgi_7"		=>	$cgi_7,
				);
*/		
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
        
        $op['combine'] = $order->get_combine($op['order']['combine']);
        
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op = $order->orgainzation_ord($op);
		// print_r($op);
/*
		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		// �[�J��^�e�� 2005/05/05
//		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_style=".$cgi_6."&SCH_sample=".$cgi_7;

//		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');
*/
$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];	
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
if($user_team == 'SU' || $user_dept == 'SA')	$op['relod_flag'] = 1;

$op['msg'] = $order->msg->get(2);
if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
page_display($op, '025', $TPL_IE_REC_SHOW); 
break;

//=======================================================
    case "add_ie1":

		check_authority('025',"add");
//		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_style=".$cgi_6."&SCH_sample=".$cgi_7;
			// �q���ƦC��
//			$parm = array(	"dept"			=>  $PHP_id,
//							"ord_num"		=>  $PHP_ord_num,
//							"cust"			=>	$PHP_ie_time1,
//				);

			if (!$op = $order->check_add_ie($PHP_ie_time1)) {   // �ˬd��J���O�_�� ���
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
				break;
			}
		

			$PHP_ord_num = $order->get_field_value('order_num',$PHP_id);

			// �ȼg�J �зǤu��[���] �|���p�J ie ��--- [ �T�{�ɦA���p�J ]---
			if (!$op = $order->update_field('ie_time1', $PHP_ie_time1, $PHP_id)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			# �O���ϥΪ̰ʺA
			$message = "added [".$PHP_ord_num."] IE SPT ";
			$log->log_add(0,"025A",$message);

		
		$redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message;			
		redirect_page($redir_str);
		break;


//=======================================================
    case "cfm_ie1":
		
		check_authority('025',"edit");

//		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_style=".$cgi_6."&SCH_sample=".$cgi_7;

			
			if (!$op = $order->check_add_ie($PHP_ie_time1)) {   // �ˬd��J���O�_�� ���
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
				break;
			}



			$PHP_ord_num = $order->get_field_value('order_num',$PHP_id);
			$PHP_qty = $order->get_field_value('qty',$PHP_id);
			
			// �g�J IE  ---   �g�J  S_ORDER
			// ���p�� ie1 ����........
			$ie1 = number_format(($PHP_ie_time1/$GLOBALS['IE_TIME']),2,'.','');
			// �A�p�� su ����........
			$su =  number_format(($PHP_qty * $ie1),0,'','');

			
			$parm = array(	"id"					=>  $PHP_id,
											"su"					=>  $su,							
											"ie1"					=>  $ie1,
											"ie_time1"		=>	$PHP_ie_time1,
				);


			// �g�J �зǤu��[���] �� ie ��--- [ �g�J��ӭ� ]---
			if (!$op = $order->update_ie($parm)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}				


//�g�J�uú
			$cm2 = number_format(($ie1 * $FTY_CM[$PHP_fty]),2,'.','');
			if (!$op = $order->update_field('fty_cm', $cm2, $PHP_id)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			$ord_rec = $order->get($PHP_id);
			for($i=0; $i<sizeof($ord_rec['ps_qty']); $i++)
			{
				$su =  number_format(($ord_rec['ps_qty'][$i] * $ie1),0,'','');
				$order->update_partial('p_su', $su, $ord_rec['ps_id'][$i]);
			}
			
				# �O���ϥΪ̰ʺA
			$message = "CFM [".$PHP_ord_num."] IE SPT ";			
			$log->log_add(0,"025E",$message);
		


/*			
			$ord_pdt=$order->get_pdtion($PHP_ord_num, $PHP_fty);
			if ($PHP_status >= 4 && $PHP_status <> 5)
			{	//ETP~ETD��SU							 
				 $etp_su=decode_mon_yy_su($ord_pdt['etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($PHP_fty, $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($su,$PHP_etp,$PHP_etd,$PHP_fty,'pre_schedule'); //����C��SU���x�s
				 $order->update_pdtion_field('etp_su', $div, $ord_pdt['id']);  //�x�s�setp_su
			}
			if($PHP_status >= 7){
				 //ETS~ETF��SU(�u�t�Ƶ{)
				 $fty_su=decode_mon_yy_su($ord_pdt['fty_su']);//���X�v�s�bfty-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($fty_su); $i++)//�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($PHP_fty, $fty_su[$i]['year'], $fty_su[$i]['mon'], 'schedule', $fty_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($su,$ord_pdt['ets'],$ord_pdt['etf'],$PHP_fty,'schedule');//����C��SU���x�s
				 $order->update_pdtion_field('fty_su', $div, $ord_pdt['id']);//�x�s�sfty_su
				 
			}
			if($PHP_status > 7){				 
				 //Out-put��SU
				 $tmp_div='';
				 $out_su=decode_mon_yy_su($ord_pdt['out_su']);//���X�v�s�bfty-su�ñNsu�ȻP�~,����}
		 		 if ($PHP_style=='PS' || $PHP_style=='BS' || $PHP_style=='BZ' || $PHP_style=='DR' || $PHP_style=='JK' || $PHP_style=='PS-J' || $PHP_style=='PS-P' || $PHP_style=='PS-S' || $PHP_style=='VS' || $PHP_style=='SS'){
					$tmp_ie=2;			
			 	 }else{
					$tmp_ie=1;			
				 }
				 for ($i=0; $i<sizeof($out_su); $i++)//�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($PHP_fty, $out_su[$i]['year'], $out_su[$i]['mon'], 'actual', $out_su[$i]['su']);			 		
				 	$tmp_qty=number_format(($out_su[$i]['su'] / $tmp_ie),0,'','');
				 	$tmp_su=number_format(($tmp_qty * $ie1),0,'','');
				 	$f1=$capaci->update_su($PHP_fty, $out_su[$i]['year'], $out_su[$i]['mon'], 'actual', $tmp_su);
				 	$tmp_div=$tmp_div.$out_su[$i]['year'].$out_su[$i]['mon'].$tmp_su.",";
				 }
				 $div=substr($tmp_div,0,-1);
				 $f1=$order->update_pdtion_field('out_su', $div, $ord_pdt['id']);//�x�s�sfty_su
				 $day_out=$daily->order_daily_out($PHP_ord_num);
				 for ($i=0; $i<sizeof($day_out); $i++)
				 {
					$parm1['field_name']='su';
					$parm1['id']=$day_out[$i]['id'];
					$parm1['field_value']=number_format(($day_out[$i]['qty'] * $ie1),0,'','');
					$f1=$daily->update_field($parm1);
				 }	
				 $ship_out=$shipping->order_ship_out($PHP_ord_num);
				 for ($i=0; $i<sizeof($ship_out); $i++)
				 {
					$parm1['field_name']='su';
					$parm1['id']=$ship_out[$i]['id'];
					$parm1['field_value']=number_format(($ship_out[$i]['qty'] * $ie1),0,'','');
					$f1=$shipping->update_field($parm1);
				 }				
			}
*/
/*
			$ord_rec = $order->get($PHP_id);
		// �p��  gm rate -----------------------------
			$unit_cost = ($ord_rec['mat_u_cost']* $ord_rec['mat_useage'])+ $ord_rec['interline']+ $ord_rec['fusible']+ $ord_rec['acc_u_cost'] + $ord_rec['quota_fee'] + $ord_rec['comm_fee'] + $ord_rec['cm'] + $ord_rec['emb'] + $ord_rec['wash'] + $ord_rec['oth'];
			$grand_cost = $unit_cost*$ord_rec['qty'] + $ord_rec['smpl_fee'];
			$sales = $ord_rec['uprice']*$ord_rec['qty'];
			$gm = $sales - $grand_cost;
			if ($sales){
				$rate = ($gm/ $sales)*100;
			}else{
				$rate =0;
			}
			$gmr = number_format($rate, 2, '.', ',');
			if (!$op = $order->update_field('gmr', $gmr, $PHP_id)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}			
*/


			if(!isset($PHP_back))$PHP_back = 'ie_search';
			$redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message;
			redirect_page($redir_str);

		break;

//=======================================================
case "add_ie2":
check_authority('025',"add");
##--***** �s�W���Ƴ��� start
//		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_style=".$cgi_6."&SCH_sample=".$cgi_7;
##--*****�s�W���Ƴ��� end
// �q���ƦC��

if (!$op = $order->check_add_ie($PHP_ie_time2)) {   // �ˬd��J���O�_�� ���
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
    break;
}

$PHP_ord_num = $order->get_field_value('order_num',$PHP_id);			
// �ȼg�J �зǤu��[���] �|���p�J ie ��--- [ �T�{�ɦA���p�J ]---
if (!$op = $order->update_field('ie_time2', $PHP_ie_time2, $PHP_id)) { 
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

# �O���ϥΪ̰ʺA
$message = "Writing[".$PHP_ord_num."] final IE SPT ";
$log->log_add(0,"025A",$message);

$ie_time1 = $order->get_field_value('ie_time1',$PHP_id);
$qty = $order->get_field_value('qty',$PHP_id);
$rang1 = $ie_time1 +($ie_time1 * $ACT_FNL_IE);
$rang2 = $ie_time1 -($ie_time1 * $ACT_FNL_IE);

$ord_rec = $order->get($PHP_id);

if($rang1 >= $PHP_ie_time2 && $rang2 <= $PHP_ie_time2){
    $redir_str = "ie.php?PHP_action=cfm_ie2&PHP_id=".$PHP_id."&PHP_ord_num=".$PHP_ord_num."&PHP_ie_time2=".$PHP_ie_time2."&PHP_qty=".$qty."&PHP_fty=".$ord_rec['factory']."&PHP_auto=1";
    redirect_page($redir_str);
}	


if(isset($PHP_back) && $PHP_back == 'order_apv'){
    $redir_str = "index2.php?PHP_action=".$PHP_back."&PHP_msg=".$message;
}else{
    $redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message;
}

redirect_page($redir_str);
break;



//=======================================================
case "cfm_ie2":
if(!isset($PHP_auto))  check_authority('025',"edit");

if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

if (!$op = $order->check_add_ie($PHP_ie_time2)) {   // �ˬd��J���O�_�� ���
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
    break;
}

$PHP_ord_num = $order->get_field_value('order_num',$PHP_id);

// ���p�� ie ����
$ie = number_format(($PHP_ie_time2/$GLOBALS['IE_TIME']),2,'.','');
// �p�� su ����........
$su = set_su($ie,$PHP_qty);

// �g�J �зǤu��[���] �� ie ��--- [ �g�J��ӭ� ]---
if (!$op = $order->update_2fields('ie_time2', 'ie2', $PHP_ie_time2, $ie, $PHP_id)) { 
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

// �g�J�uú
$cm2 = number_format(($ie2 * $FTY_CM[$PHP_fty]),2,'.','');
if (!$op = $order->update_field('fty_cm', $cm2, $PHP_id)) {
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}


# `s_order`               `su`
$order->mdf_ie($PHP_ord_num,$ie,$su,$PHP_ie_time2);

# `cutting_out`           `su`
$cutting->mdf_ie($PHP_ord_num,$ie);

# `saw_out_put`           `su`
$monitor->mdf_ie($PHP_ord_num,$ie);

# `shipping`              `su`
$shipping->mdf_ie($PHP_ord_num,$ie);

# `pdt_finish`            `su`,`cut_su`,`saw_su`,`pack_su` ��TABLE�ݰ_�ӬO�n��X�q��Ҧ��Ͳ����q���������A�A�ثe�ݰ_�ӬO�S���A�ΡG�������ƶq�aSU
$pdt_finish->mdf_ie($PHP_ord_num,$ie);

# `schedule`              `su`
$partial_arr = $schedule->mdf_ie($PHP_ord_num);

# `order_partial`         `p_su`,`p_etp_su`,`p_fty_su`,`ext_su`  �ʤ֭��s�p�� ext_period �Ͳ��ѼơG���~��FTY��SU�A�ۦP�ݭn���Ʈɶ��A�p��SU
# ���� partial ��� `schedule` ���G�� `partial` ����϶�
// $pdtion_arr = $order->mdf_partial_ie($partial_arr,$ie);

# `pdtion`                `etp_su �q��`,`fty_su �Ʋ�`,`out_su ���X`,`ext_su`  `ext_su` �w���� Partial �p��  ���p��Ӷ��q�` su�G���~��SU�ݭn�����Ʈɶ��A�p��SU
# ���� pdtion 
// $pdtion->mdf_ie($pdtion_arr,$ie);


# �O���ϥΪ̰ʺA
$message = "cfm [".$PHP_ord_num."] final IE SPT ";
$log->log_add(0,"025E",$message);

if(isset($PHP_back) && $PHP_back == 'order_apv') {
    $redir_str = "index2.php?PHP_action=".$PHP_back."&PHP_msg=".$message;
} else {
    $redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message;
}
// exit;
redirect_page($redir_str);

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 025A upload_calc_ie
#		case "upload_calc_ie":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "upload_calc_ie":
check_authority('025',"add");

$S_ROOT = dirname(__FILE__);
$Flag = substr(($S_ROOT),2,1);

//		$op['back_str'] = $PHP_back_str;
$filename = $_FILES['PHP_file']['name'];
$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

if ($ext == "xls" || $ext == "XLS" || $ext == "xlsx" || $ext == "XLSX"){   // �W���ɪ����ɦW�� xls �� -----

	// upload pattern file to server
	$pttn_name = $PHP_num.'_ie1.xls';  // ���w�� xls ���ɦW
	$upload = new Upload;
	// $upload->uploadFile(dirname($PHP_SELF).'/ord_ie/', 'other', 16, $pttn_name );
	$upload->UploadFiles($S_ROOT.$Flag."ord_ie".$Flag, 'other', 20, $pttn_name  );
	if (!$upload){
		$op['msg'][] = $upload;				
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
//			$A=$order->update_field('ptn_upload', $GLOBALS['TODAY'], $PHP_id); //�O��pttn�W�Ǥ��			
	$message = "UPLOAD calc. IE of ".$PHP_num;
	$log->log_add(0,"025E",$message);
} else {  // �W���ɪ����ɦW  ���O  mdl �� -----

	$message = "upload file is incorrect format ! Please re-send EXCEL file.";
}


$redir_str = "ie.php?PHP_action=ie_record_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
redirect_page($redir_str);


//=======================================================
    case "ie1_resend":
		check_authority('025',"add");
				
	if(file_exists($GLOBALS['config']['root_dir']."/ord_ie/".$PHP_num."_ie1.xls")){		
			unlink("./ord_ie/".$PHP_num."_ie1.xls");
		}
		$message = "DEL exist pattern for resend for ".$PHP_num;
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"025E",$message);

		$redir_str = "ie.php?PHP_action=ie_record_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 025A upload_final_ie
#		case "upload_final_ie":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "upload_final_ie":
check_authority('025',"add");

$S_ROOT = dirname(__FILE__);
$Flag = substr(($S_ROOT),2,1);

//		$op['back_str'] = $PHP_back_str;
$filename = $_FILES['PHP_file']['name'];
$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

if ($ext == "xls" || $ext == "XLS" || $ext == "xlsx" || $ext == "XLSX"){   // �W���ɪ����ɦW�� xls �� -----

	// upload pattern file to server
	$pttn_name = $PHP_num.'_ie2.xls';  // ���w�� xls ���ɦW
	$upload = new Upload;
	
	// $upload->uploadFile($S_ROOT.$Flag.'/ord_ie/'.$Flag, 'other', 16, $pttn_name );
	
	$upload->UploadFiles($S_ROOT.$Flag."ord_ie".$Flag, 'other', 20, $pttn_name  );

	if (!$upload){
		$op['msg'][] = $upload;				
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
//			$A=$order->update_field('ptn_upload', $GLOBALS['TODAY'], $PHP_id); //�O��pttn�W�Ǥ��			
	$message = "UPLOAD final IE of ".$PHP_num;
	$log->log_add(0,"025E",$message);
} else {  // �W���ɪ����ɦW  ���O  mdl �� -----

	$message = "upload file is incorrect format ! Please re-send EXCEL file.";
}

$redir_str = "ie.php?PHP_action=ie_record_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
redirect_page($redir_str);


//=======================================================
    case "ie2_resend":
		check_authority('025',"add");
				
	if(file_exists($GLOBALS['config']['root_dir']."/ord_ie/".$PHP_num."_ie2.xls")){		
			unlink("./ord_ie/".$PHP_num."_ie2.xls");
		}
		$message = "DEL exist pattern for resend for ".$PHP_num;
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"025E",$message);

		$redir_str = "ie.php?PHP_action=ie_record_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redir_str);





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 35X    �Ͳ��O���ন excel ��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "ie_excel":

		if(!$admin->is_power('025',"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
/*
		$parm = array(	"dept"		=>  $PHP_dept_code,
						"ord_num"	=>  $PHP_order_num,
						"cust"		=>	$PHP_cust,
						"ref"		=>	$PHP_ref,
						"fty"		=>	$PHP_factory,
				);
*/
		if (!$ord = $order->search(1,'',1000)) {  // 2005/11/24 �[�J�ĤT�ӰѼ� ���ܷj�M�j�p
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = $ord['max_no'];

	

	
	require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
	require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  function HeaderingExcel($filename) {
		  header("Content-type: application/vnd.ms-excel");
		  header("Content-Disposition: attachment; filename=$filename" );
		  header("Expires: 0");
		  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		  header("Pragma: public");
		  }

	  // HTTP headers
	  HeaderingExcel('order.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('order list');

		$now = $GLOBALS['THIS_TIME'];

	// �g�J title

	  // Format for the headings
	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern();
	  $formatot->set_fg_color('navy');

	  $f2 =& $workbook->add_format();
	  $f2->set_size(10);
	  $f2->set_align('center');
	  $f2->set_color('navy');
	  $f2->set_pattern();
	  $f2->set_fg_color('white');

	  $worksheet1->set_column(0,0,10);
	  $worksheet1->set_column(0,1,5);
	  $worksheet1->set_column(0,2,5);
	  $worksheet1->set_column(0,3,8);
	  $worksheet1->set_column(0,4,3);
	  $worksheet1->set_column(0,5,8);
	  $worksheet1->set_column(0,6,6);
	  $worksheet1->set_column(0,7,6);
	  $worksheet1->set_column(0,8,14);
	  $worksheet1->set_column(0,9,14);
	  $worksheet1->set_column(0,10,5);
	  $worksheet1->set_column(0,11,0);
	  $worksheet1->set_column(0,12,14);
	  $worksheet1->set_column(0,13,14);
	  $worksheet1->set_column(0,14,14);
	  $worksheet1->set_column(0,15,14);
	  $worksheet1->set_column(0,16,0);
	  $worksheet1->set_column(0,17,4);
	  $worksheet1->set_column(0,18,8);
	  $worksheet1->set_column(0,19,10);
	  $worksheet1->set_column(0,20,10);
	  $worksheet1->set_column(0,21,10);
	  $worksheet1->set_column(0,22,10);
	  $worksheet1->set_column(0,23,6);
	  $worksheet1->set_column(0,24,15);

	  $worksheet1->write_string(0,1,"Order List");
	  $worksheet1->write(0,3,$now);

	  $worksheet1->write_string(1,0,"Order#",$formatot);
	  $worksheet1->write_string(1,1,"cust",$formatot);
	  $worksheet1->write_string(1,2,"style",$formatot);
	  $worksheet1->write_string(1,3,"Q'ty",$formatot);
	  $worksheet1->write_string(1,4,"unit",$formatot);
	  $worksheet1->write_string(1,5,"SU",$formatot);
	  $worksheet1->write_string(1,6,"SPT",$formatot);
	  $worksheet1->write_string(1,7,"IE",$formatot);
	  $worksheet1->write_string(1,8,"ETD",$formatot);
	  $worksheet1->write_string(1,9,"ETP",$formatot);
	  $worksheet1->write_string(1,10,"FTY",$formatot);
	  $worksheet1->write_string(1,11,"",$formatot);
	  $worksheet1->write_string(1,12,"open date",$formatot);
	  $worksheet1->write_string(1,13,"order apv",$formatot);
	  $worksheet1->write_string(1,14,"smpl apv",$formatot);
	  $worksheet1->write_string(1,15,"patt upload",$formatot);
	  $worksheet1->write_string(1,16,"",$formatot);
	  $worksheet1->write_string(1,17,"dept.",$formatot);
	  $worksheet1->write_string(1,18,"in charged",$formatot);
	  $worksheet1->write_string(1,19,"style#",$formatot);
	  $worksheet1->write_string(1,20,"smpl order#",$formatot);
	  $worksheet1->write_string(1,21,"pattn #",$formatot);
	  $worksheet1->write_string(1,22,"Quota cat.",$formatot);
	  $worksheet1->write_string(1,23,"revise",$formatot);
	  $worksheet1->write_string(1,24,"status",$formatot);


	  // Format for the numbers
	  $formatnum =& $workbook->add_format();
	  $formatnum->set_size(10);
	  $formatnum->set_align('center');
	  $formatnum->set_color('black');
//	  $formatnum->set_pattern('bold');
	  $formatnum->set_fg_color('B8D7D8');


	for ($i=0;$i<sizeof($ord['sorder']);$i++){

			$status_des = get_ord_status($ord['sorder'][$i]['status']);
	  
	  $worksheet1->write($i+2,0,$ord['sorder'][$i]['order_num'],$formatnum);
	  $worksheet1->write($i+2,1,$ord['sorder'][$i]['cust'],$f2);
	  $worksheet1->write($i+2,2,$ord['sorder'][$i]['style'],$f2);
	  $worksheet1->write($i+2,3,$ord['sorder'][$i]['qty']);
	  $worksheet1->write($i+2,4,$ord['sorder'][$i]['unit']);
	  $worksheet1->write($i+2,5,$ord['sorder'][$i]['su']);
	  $worksheet1->write($i+2,6,$ord['sorder'][$i]['ie_time1']);
	  $worksheet1->write($i+2,7,$ord['sorder'][$i]['ie1']);
	  $worksheet1->write($i+2,8,$ord['sorder'][$i]['etd'],$f2);
	  $worksheet1->write($i+2,9,$ord['sorder'][$i]['etp'],$f2);
	  $worksheet1->write($i+2,10,$ord['sorder'][$i]['factory'],$f2);
	  $worksheet1->write($i+2,11,'',$formatot);
	  $worksheet1->write($i+2,12,$ord['sorder'][$i]['opendate'],$f2);
	  $worksheet1->write($i+2,13,$ord['sorder'][$i]['apv_date'],$f2);
	  $worksheet1->write($i+2,14,$ord['sorder'][$i]['smpl_apv'],$f2);
	  $worksheet1->write($i+2,15,$ord['sorder'][$i]['ptn_upload'],$f2);
	  $worksheet1->write($i+2,16,'',$formatot);
	  $worksheet1->write($i+2,17,$ord['sorder'][$i]['dept'],$f2);
	  $worksheet1->write($i+2,18,$ord['sorder'][$i]['creator'],$f2);
	  $worksheet1->write($i+2,19,$ord['sorder'][$i]['style_num'],$f2);
	  $worksheet1->write($i+2,20,$ord['sorder'][$i]['smpl_ord'],$f2);
	  $worksheet1->write($i+2,21,$ord['sorder'][$i]['patt_num'],$f2);
	  $worksheet1->write($i+2,22,$ord['sorder'][$i]['quota'],$f2);
	  $worksheet1->write($i+2,23,$ord['sorder'][$i]['revise'],$f2);
	  $worksheet1->write($i+2,24,$status_des,$f2);
	
	}


  $workbook->close();


	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "file_del":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "file_del":
check_authority('025',"add");
$t = $order->file_del($PHP_file_id);
if($t){
    $log->log_add(0,"039D","delete ord ptn file on order id:$PHP_id");
    $msg = "Successfully delete file";
	
	# update s_order �� 
	$order->check_ord_ptn($PHP_id);
}
redirect_page($PHP_SELF."?PHP_action=ie_record_view&PHP_id=$PHP_id&PHP_msg=$msg");
break;
//= end case ======================================================

//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reset_ie":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "reset_ie":
check_authority('025',"admin");

// �g�J �зǤu��[���] �� ie ��--- [ �g�J��ӭ� ]---
if (!$op = $order->reset_ie($_GET['PHP_id'])) { 
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

$argv2 = array(	
    "order_num"	=>	$PHP_ord_num,
    "user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
    "des"		=>	"REOPEN Calc. IE SPT - ".$_GET['PHP_time']
);

if (!$order_log->add($argv2)) {
    $op['msg'] = $order_log->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}

# �O���ϥΪ̰ʺA
$message = "Reset [".$PHP_ord_num."] Calc. IE SPT ";
$log->log_add(0,"025R",$message);

if(!isset($PHP_back))$PHP_back = 'ie_search';
//$redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message."&PHP_sr_startno=".$_GET['PHP_sr_startno'];
$redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message."&PHP_sr_startno=1";
redirect_page($redir_str);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reset_ie_final":			job 53  #MODE MDF
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "reset_ie_final":
check_authority('025',"admin");

if (!$op = $order->reset_ie_final($_GET['PHP_id'])) { 
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

$argv2 = array(	
    "order_num"	=>	$PHP_ord_num,
    "user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
    "des"		=>	"REOPEN Final IE SPT - ".$_GET['PHP_time']
);

if (!$order_log->add($argv2)) {
    $op['msg'] = $order_log->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}


$PHP_ord_num = $order->get_field_value('order_num',$_GET['PHP_id']);
$ie_time = $order->get_field_value('ie_time1',$_GET['PHP_id']);

// �p�� ie1 ����........
$ie = set_ie($ie_time);

# `cutting_out`           `su`
$cutting->mdf_ie($PHP_ord_num,$ie);

# `saw_out_put`           `su`
$monitor->mdf_ie($PHP_ord_num,$ie);

# `shipping`              `su`
$shipping->mdf_ie($PHP_ord_num,$ie);

# `pdt_finish`            `su`,`cut_su`,`saw_su`,`pack_su` ��TABLE�ݰ_�ӬO�n��X�q��Ҧ��Ͳ����q���������A�A�ثe�ݰ_�ӬO�S���A�ΡG�������ƶq�aSU
$pdt_finish->mdf_ie($PHP_ord_num,$ie);

# `schedule`              `su`
$partial_arr = $schedule->mdf_ie($PHP_ord_num);

# `order_partial`         `p_su`,`p_etp_su`,`p_fty_su`,`ext_su`  �ʤ֭��s�p�� ext_period �Ͳ��ѼơG���~��FTY��SU�A�ۦP�ݭn���Ʈɶ��A�p��SU
# ���� partial ��� `schedule` ���G�� `partial` ����϶�
// $pdtion_arr = $order->mdf_partial_ie($partial_arr,$ie);

# `pdtion`                `etp_su �q��`,`fty_su �Ʋ�`,`out_su ���X`,`ext_su`  `ext_su` �w���� Partial �p��  ���p��Ӷ��q�` su�G���~��SU�ݭn�����Ʈɶ��A�p��SU
# ���� pdtion 
// $pdtion->mdf_ie($pdtion_arr,$ie);



# �O���ϥΪ̰ʺA
$message = "Reset [".$PHP_ord_num."] Final IE SPT ";
$log->log_add(0,"025R",$message);

$redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message."&PHP_sr_startno=1";
redirect_page($redir_str);
break;



//=======================================================
# �л\ �uú
case "mdf_ie":
check_authority('025',"admin");

if (!$op = $order->check_add_ie($PHP_ie_time3)) {   // �ˬd��J���O�_�� ���
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
    break;
}

$PHP_ord_num = $order->get_field_value('order_num',$PHP_id);
$PHP_qty = $order->get_field_value('qty',$PHP_id);
$PHP_fty = $order->get_field_value('factory',$PHP_id);

// �p�� ie1 ����........
$ie = set_ie($PHP_ie_time3);
// �p�� su ����........
$su = set_su($ie,$PHP_qty);

// echo $PHP_ie_time3.'<br>';
// echo $ie1.'<br>';

// �g�J�uú
$cm2 = number_format(($ie * $FTY_CM[$PHP_fty]),2,'.','');
if (!$op = $order->update_field('fty_cm', $cm2, $PHP_id)) {
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

# `s_order`               `su`
$order->mdf_ie($PHP_ord_num,$ie,$su,$PHP_ie_time3);

# `cutting_out`           `su`
$cutting->mdf_ie($PHP_ord_num,$ie);

# `saw_out_put`           `su`
$monitor->mdf_ie($PHP_ord_num,$ie);

# `shipping`              `su`
$shipping->mdf_ie($PHP_ord_num,$ie);

# `schedule`              `su`
$partial_arr = $schedule->mdf_ie($PHP_ord_num);

# `pdt_finish`            `su`,`cut_su`,`saw_su`,`pack_su` ��TABLE�ݰ_�ӬO�n��X�q��Ҧ��Ͳ����q���������A�A�ثe�ݰ_�ӬO�S���A�ΡG�������ƶq�aSU
// $pdt_finish->mdf_ie($PHP_ord_num,$ie);

# `order_partial`         `p_su`,`p_etp_su`,`p_fty_su`,`ext_su`  �ʤ֭��s�p�� ext_period �Ͳ��ѼơG���~��FTY��SU�A�ۦP�ݭn���Ʈɶ��A�p��SU
# ���� partial ��� `schedule` ���G�� `partial` ����϶�
// $pdtion_arr = $order->mdf_partial_ie($partial_arr,$ie);

# `pdtion`                `etp_su �q��`,`fty_su �Ʋ�`,`out_su ���X`,`ext_su`  `ext_su` �w���� Partial �p��  ���p��Ӷ��q�` su�G���~��SU�ݭn�����Ʈɶ��A�p��SU
# ���� pdtion 
// $pdtion->mdf_ie($pdtion_arr,$ie);

# �O���ϥΪ̰ʺA
$message = "Writing[".$PHP_ord_num."] Final IE SPT ";
$log->log_add(0,"025A",$message);

$redir_str = "ie.php?PHP_action=ie_search&PHP_msg=".$message;
redirect_page($redir_str);

break;


}

?>
