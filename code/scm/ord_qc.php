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
session_register	('sch_parm');



include_once($config['root_dir']."/lib/class.transport.php");
$transport = new TRANSPORT();
if (!$transport->init($mysql,"log")) { print "error!! cannot initialize database for ORDER SHIFT class"; exit; }


switch ($PHP_action) {
//= start case ====================================================

//-------------------------------------------------------------------------------------
//			 job 35   IE �O��
//-------------------------------------------------------------------------------------
    case "ord_qc":
    	check_authority('036',"view");
				
		// creat cust combo box
		$op['factory'] = $arry2->select($FACTORY,'','SCH_factory','select','');  	
		for($i=0; $i<sizeof($FACTORY); $i++)
		{
			if ( $GLOBALS['SCACHE']['ADMIN']['dept'] == $FACTORY[$i])
			{
				$op['factory'] = $FACTORY[$i]."<input type=hidden name='SCH_factory' value = '".$FACTORY[$i]."' >";
				break;
			}
		}
		$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 

//080725message�W�[		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
	
		$op['msg'] = $order->msg->get(2);
		page_display($op, '036', $TPL_ORD_QC);		    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 412search�q��
#		case "ord_qc_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ord_qc_search":
		check_authority('036',"view");
		
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������

		if(isset($SCH_order_num))
		{			
			$sch_parm = array();
			$sch_parm = array(	
												"order_num"		=>  $SCH_order_num,
												"cust"				=>	$SCH_cust,
												"ref"					=>	$SCH_ref,
												"factory"			=>	$SCH_factory,
												"sr_startno"	=>	$PHP_sr_startno,
												"action"			=>	$PHP_action
				);
				
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
			}
			$PHP_order_num = $sch_parm['order_num'];
			$PHP_cust = $sch_parm['cust'];
			$PHP_ref = $sch_parm['ref'];
			$PHP_factory = $sch_parm['factory'];
			$PHP_sr_startno = $sch_parm['sr_startno'];

			
			if (!$op = $order->ord_search(3)) {   // �w�����X�� ��mode =3 �Y�[�Jstatus > 7
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			for($i=0; $i<sizeof($op['sorder']); $i++)
			{
				$op['sorder'][$i]['qc'] = $order->get_qc_file($op['sorder'][$i]['order_num']);
			}

			$op['msg'] = $order->msg->get(2);
			if(isset($PHP_msg))$op['msg'][] = $PHP_msg;			
			page_display($op, '036', $TPL_ORD_QC_LIST); 
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 412�W��QC��
#		case "upload_ord_qc_file":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_ord_qc_file":
		check_authority('036',"add");

		$filename = $_FILES['PHP_qc_file']['name'];		

		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));
		$sec_name = array('xls','XLS','pdf','PDF','doc','DOC');
		$f_check = 0; //�P�_���ɦW�O�_�s�b
		for ($i=0; $i<sizeof( $sec_name); $i++)
		{
			if ($sec_name[$i] == $ext)
			{
				$f_check = 1;
				break;
			}
		}	

		if ($filename) 
		{				
			if ($_FILES['PHP_qc_file']['size'] < 2072864 && $_FILES['PHP_qc_file']['size'] > 0)//�P�_�j�p��2M��
			{			
				if ($f_check == 1){   // �W���ɪ����ɦW�� xls,pdf,doc �� -----

					$today = $GLOBALS['TODAY'];
					$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
					$parm = array(	"file_name"		=>  $PHP_num,				
													"num"					=>  $PHP_num,
													"file_user"		=>	$user_name,
													"file_date"		=>	$today
												);

					// upload pattern file to server
					$A = $fils->get_name_id('ord_qc_file');
					$ext = strtolower($ext); //��p�g
					$pttn_name = $PHP_num."_".date('Ymd')."_".$A.".".$ext; //��´�ɦW
					$parm['file_name'] = $pttn_name;
					$str_long=strlen($pttn_name);
					$upload = new Upload;
					$upload->setMaxSize(2072864);
					$fu1 = $upload->uploadFile(dirname($PHP_SELF).'/ord_qc_file/', 'other', 16, $pttn_name );				
					if (!$upload){
						$op['msg'][] = $upload;
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
					if (!$A = $fils->upload_ord_qc_file($parm)){
						$op['msg'] = $fils->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
					$message = "UPLOAD file of ".$PHP_num;
					$log->log_add(0,"412E",$message);
				} else {  // �W���ɪ����ɦW  �O  ��L �� -----
					$message = "upload file is incorrect format ! Please re-send.";
				}
			}else{  //�W���ɮפӤj
				$message = "upload file is too big!!";
			}
		}else{ //�S���ɮ�
			$message="You don't pick any file or add any file description.";
		}	

			
		$redir_str=$PHP_SELF.'?PHP_action=ord_qc_search&PHP_msg='.$message;
		redirect_page($redir_str);
		break;
//=======================================================
    case "do_ord_qc_file_del":		
		check_authority('036',"add");	

		$f1 = $fils->del_file('ord_qc_file',$PHP_id);
		if (!$f1) {
			$op['msg'] = $fils->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if(file_exists($GLOBALS['config']['root_dir']."/ord_qc_file/".$PHP_file_name)){
			unlink("./ord_qc_file/".$PHP_file_name);
		}

		$message = "Delete QC FILE ON ORDER :[".$PHP_ord_num."] �C";
		$log->log_add(0,"412D",$message);

		$redir_str=$PHP_SELF.'?PHP_action=ord_qc_search&PHP_msg='.$message;

		redirect_page($redir_str);
		break;

//= end case ======================================================
}

?>
