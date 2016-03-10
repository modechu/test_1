<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
##################  2004/11/10  ########################
#			index.php  �D�{��
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [�Ͳ��s�y][�Ͳ�����]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";
/*
include_once($config['root_dir']."/lib/class.notify.php");
$notify = new NOTIFY();
if (!$notify->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
*/

$op = array();

$dept_ary = array( 1	=>	'LY',
									 2	=>	'HJ',
									 4	=>	'RD',
									 8	=>	'PM',
									 16	=>	'KA',
									 32	=>	'KB'
									 );

$sand_ary['HJ'][0] = 'kihwen';
$sand_ary['HJ'][1] = 'simon';
$sand_ary['LY'][0] = 'henry';
$sand_ary['LY'][1] = 'martin-kao';
$sand_ary['RD'][0] = 'acelex';
$sand_ary['PM'][0] = 'yankee';

session_register	('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

$debit_dept = array('HJ','LY','KA','KB','DA');


switch ($PHP_action) {
//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 101  �q ��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "debit":
		check_authority('070',"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();

		$op['msg'] = $order->msg->get(2);
		// creat cust combo box
		// ���X �Ȥ�N��
		$where_str=" order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		
		$op['dept_select'] =  $arry2->select($debit_dept,$GLOBALS['SCACHE']['ADMIN']['dept'],'PHP_dept','select'); 


//080725message�W�[		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
	if(isset($PHP_msg))$op['msg'][]=$PHP_msg;
			// creat factory combo box
		  	
		page_display($op,'070', $TPL_DEBIT);			    	    
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "dn_add":
		check_authority('070',"add");
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������

		if(($PHP_cust && $PHP_supl) || ($PHP_cust && $PHP_dept) || ($PHP_dept && $PHP_supl))
		{
			$msg = "Department and Customer and Supplier invite to choose once";
			$redir_str = 'dabit.php?PHP_action=debit&PHP_msg='.$msg;
			redirect_page($redir_str);

		}

		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['msg']= $order->msg->get(2);
		if($PHP_cust)
		{
			$op['dn_to'] = $PHP_cust;
			$op['check_to']	 = 1;
			$tmp = $cust->get_fields('cust_init_name'," WHERE cust_s_name='".$PHP_cust."'");
			$op['dn_to_name'] = $tmp[0];
	
		}else if($PHP_supl){
			$op['dn_to'] = $PHP_supl;
			$op['check_to']	 = 0;
			$tmp = $supl->get_fields('supl_s_name'," WHERE vndr_no='".$PHP_supl."'");			
			$op['dn_to_name'] = $tmp[0];

		}else{
			$op['dn_to'] = $PHP_dept;
			$op['check_to']	 = 2;
			$tmp = $dept->get_fields('dept_name'," WHERE dept_code='".$PHP_dept."'");			
			$op['dn_to_name'] = $tmp[0];
		}
		$op['date'] = $TODAY;
		$op['dept_id'] = $user_dept;
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,'','PHP_currency','select','');

		// 2005/07/30 �[�J �� menu�i�J�s�W�᪺ back page �u�V �����C��
page_display($op,'070',$TPL_DEBIT_ADD);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dn_search":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "dn_search":
		check_authority('070',"view");

					//�i�Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
			
			if(!isset($SCH_to))
			{
				if($SCH_supl)$SCH_to = $SCH_supl;
				if($PHP_cust)$SCH_to = $PHP_cust;
			}
			if(!isset($SCH_to))$SCH_to = '';
			if (!$op = $debit->search(1)) {  
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			for($i=0; $i<sizeof($op['dn']); $i++)
			{
				
				foreach($dept_ary as $key => $value)
				{
					if($op['dn'][$i]['oth_dept'] & $key)
					{						
						
						$where_str = "dn_id = ".$op['dn'][$i]['id']." AND comm_dept = '".$value."'";
						$comm_chk = $debit->get_comm_field('Comm', $where_str);
						if($comm_chk)
						{
							$op['dn'][$i][$value] = 2;
						}else{
							$op['dn'][$i][$value] = 1;
						}
					}else{
						$op['dn'][$i][$value] = 0;
					}
				}
				
				if($op['dn'][$i]['chk_to'] == 0)
				{
					$tmp = $supl->get_fields('supl_s_name'," WHERE vndr_no='".$op['dn'][$i]['dn_to']."'");			
					$op['dn'][$i]['dn_to_name'] = $tmp[0];

				}else if($op['dn'][$i]['chk_to'] == 1){
					$tmp = $cust->get_fields('cust_init_name'," WHERE cust_s_name='".$op['dn'][$i]['dn_to']."'");
					$op['dn'][$i]['dn_to_name'] = $tmp[0];
				}else{
					$tmp = $dept->get_fields('dept_name'," WHERE dept_code='".$op['dn'][$i]['dn_to']."'");			
						$op['dn'][$i]['dn_to_name'] = $tmp[0];				
				}

			}
			

		$op['msg']= $except->msg->get(2);
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		$op['back_str'] = "&SCH_ord=".$SCH_ord."&SCH_to=".$SCH_to;
		
		page_display($op,'070', $TPL_DEBIT_LIST);
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dn_show":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dn_show":

		check_authority('070',"view");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['back_str'] = "dabit.php?PHP_action=dn_search&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord."&SCH_to=".$SCH_to;

		$op['back_str2'] = "&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord."&SCH_to=".$SCH_to;

		page_display($op,'070', $TPL_DEBIT_SHOW);				
		break;
			


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dn_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dn_edit":

		check_authority('070',"add");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

				foreach($dept_ary as $key => $value)
				{
					$op['dn'][$value] = 0;
					if($op['dn']['oth_dept'] & $key)
					{			
						$op['dn'][$value] = 1;
						$where_str = "dn_id = ".$PHP_id." AND comm_dept = '".$value."'";
						$comm_chk = $except->get_comm_field('Comm', $where_str);
						if($comm_chk)$op['dn'][$value] = 2;
						
					}
				}

		$op['dn']['dn_des'] = str_replace("<br>",chr(13).chr(10), $op['dn']['dn_des'] );
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['dn']['currency'],'PHP_currency','select','');
		
		$op['back_str'] = $PHP_back_str;
		page_display($op,'070', $TPL_DEBIT_EDIT);				
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_dn_edit":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_dn_edit":
		check_authority('070',"add");	
		$PHP_oth_dept = 0;
		if(isset($PHP_team))
		{
			for($i=0; $i < 6; $i++)
			{
				if(isset($PHP_team[$i]))$PHP_oth_dept+=$PHP_team[$i];
			}
		}

		$PHP_dn_des = str_replace("'","\'",$PHP_dn_des);		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$PHP_dnc_des = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_dn_des);
		}

		if(strstr($PHP_dn_des,'&#'))  //�ഫ�c²��
		{
			$PHP_dn_des = $ch_cov->check_cov($PHP_dn_des);
		}	
		
		$parm = array(	
										"dn_des"			=>	$PHP_dn_des,
										'oth_dept'		=>	$PHP_oth_dept,
										'currency'		=>	$PHP_currency,
										'dn_price'		=>	$PHP_price,
										'cust_po'			=> 	$PHP_cust_po,
										'id'					=>	$PHP_id
			);

		$f1 = $debit->edit($parm);


			foreach($dept_ary as $key => $value)
			{
				$new_dept = $PHP_oth_dept & $key;
				$old_dept = $PHP_org_dept & $key;
				if($new_dept && !$old_dept)
				{
					$parm = array(	"dn_id"				=>	$PHP_id,
													"comm_dept"		=>	$value,
													"comm"				=>	'',																			
									);
					$f2 = $debit->add_comm($parm);

				//�ǰe���`���i�[�J�N���T��
				 $messg  = '<a href="dabit.php?PHP_action=dn_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&SCH_to=">'.$PHP_order.'</a>';
				 $notify->system_msg_send('10-6-A',$value,$PHP_order,$messg);
				
				}
				if(!$new_dept && $old_dept)
				{
					$where_str = "dn_id = ".$PHP_id." AND comm_dept = '".$value."'";
					$f4=$debit->comm_del($where_str);
				}
			}

		# �O���ϥΪ̰ʺA

	
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$message = "EDIT debit note On : [".$op['dn']['dn_num']."]";
		$log->log_add(0,"106E",$message);
		$op['msg'][] = $message;

		$op['back_str'] = $PHP_back_str;
		page_display($op,'070', $TPL_DEBIT_SHOW);				
		break;

//-------------------------------------------------------------------------
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dn_comm_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dn_comm_edit":

		check_authority('070',"edit");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
		
		if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'yankee') $op['dept'] = 'PM';

		for($i=0; $i<sizeof($op['comm']); $i++)
			$op['comm'][$i]['Comm'] = str_replace("<br>",chr(13).chr(10), $op['comm'][$i]['Comm'] );

		$op['back_str'] = $PHP_back_str;
		page_display($op,'070', $TPL_DEBIT_COMM);				
		break;
		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_dn_comm_edit":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_dn_comm_edit":
		check_authority('070',"edit");	
		$message='';	
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


	if(isset($PHP_comm))
	{
		$PHP_comm = str_replace("'","\'",$PHP_comm);		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$PHP_comm = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_comm);
		}

		if(strstr($PHP_comm,'&#'))  //�ഫ�c²��
		{
			$PHP_comm = $ch_cov->check_cov($PHP_comm);
		}	

		$parm = array(	
										'Comm'			=>	$PHP_comm,
										'id'				=>	$PHP_comm_id,
										'comm_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'comm_date'	=>	$TODAY,

			);

		$f1 = $debit->edit_comm($parm);
	
		$message = "ADD ".$PHP_comm_dept." Comment On : [".$op['dn']['dn_num']."]";
		$log->log_add(0,"106C",$message);
	}

		# �O���ϥΪ̰ʺA

	
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

//��|��N����J������ǰe�T�����إ߲��`���i��
	if($op['com_mk'] == 1)
	{
			$messg  = '<br><br>���߳��i : <a href="dabit.php?PHP_action=dn_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&SCH_to=">'.$op['dn']['dn_num'].'</a><br>';
			$messg .= '�|����N���v�[�J���� �A �гtSUBMIT �A �P�ɦC�L�X�� �e�W�Ů֥�<br><br>';
			$messg = $notify->cov_html($messg);  //message add

			$parm2 = array(	"tuser"			=>  $op['dn']['dn_user_id'],
											"title"			=>	'���߳��i ['.$op['dn']['dn_num'].'] �v�����|����N���[�J',
											"msg"				=>	$messg,	
											"fuser"			=>	'System MSG',
											"send_date"	=>	$TODAY,
											);
			$f3=$notify->add($parm2);  //message add
		}

		if($message)$op['msg'][] = $message;



		$op['back_str'] = $PHP_back_str;
		page_display($op,'070', $TPL_DEBIT_SHOW);				
		break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_list_qty":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dn_print":

		check_authority('070',"view");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $debit->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			$op['dn']['dn_price'] = NUMBER_FORMAT($op['dn']['dn_price'],2);
//---------------------------------- ��Ʈw�@�~���� ------------------------------

include_once($config['root_dir']."/lib/class.pdf_debit.php");

$print_title = "DEBIT NOTE";

$print_title2 = "Date:".$op['dn']['dn_date'];
$creator = $op['dn']['dn_user'];

$ord_num = $op['dn']['dn_num'];

$title_ary = $op['dn'];


$pdf=new PDF_DEBIT();
$pdf->AddBig5Font();

$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->SetCreator('angel');

$x=1;

$pdf->fld_title('event');
$r = $pdf->exc_value($op['dn']['dn_des']);
$x+=$r;

if(isset($op['comm']))
{
$pdf->fld_title('Other comments');
$x++;
for($i=0; $i<sizeof($op['comm']); $i++)
{
		if($x > 31)
		{
			$pdf->Cell(190,3,'','RLB',0,'L');
			$pdf->AddPage();		
			$pdf->fld_titlel('Other comments');	
			$x = 2;	
		}
		$x = $pdf->comm_value($op['comm'][$i],$x);
}
}
		if($x > 36)
		{
			$pdf->AddPage();		
			$x = 1;	
		}

//ñ�W
$pdf->SetY(248);
$pdf->cell(50,4,'�`�g�z : ',0,0,'L');
$pdf->cell(50,4,'���`�g�z : '.$op['dn']['apv_user'],0,0,'L');
$pdf->cell(50,4,'Authorized : ',0,0,'L');
//$pdf->cell(40,4,'Apply by : �~�P�~�ȳ�',0,0,'L');
$pdf->cell(40,4,'Apply by : '.$op['dn']['dn_user']."(".$op['dn']['dept'].")",0,0,0,'L');
$pdf->ln();
$pdf->ln();
$pdf->cell(50,4,'',0,0,'L');
$pdf->cell(50,4,'(�d���k�ݵ��w)',0,0,'L');
$pdf->cell(35,4,'',0,0,'L');
//$pdf->cell(40,4,'Apply by : '.$op['dn']['dn_user']."(".$op['dn']['dept'].")",0,0,0,'L');

//�Ƶ�
$pdf->SetFont('Big5','',7);
$pdf->ln();
$pdf->ln();
$pdf->cell(190,5,'* ��������Ƶo2�餺��g�A�g�������|�쨣��e���`�g�z�ǡA�e�`�g�z��ñ�A�H�����дڪ���',0,0,'L');
$pdf->ln();
$pdf->SetY(267);

$pdf->cell(190,5,'* �Ͳ�����������X���`���i��ƫ��C��g��|ĳ���קi',0,0,'L');
$pdf->SetY(270);
$pdf->cell(190,5,'* ������줣�ŨϥήɡA�Цۦ��H�K�����g',0,0,'L');

$name=$op['dn']['dn_num'].'_debit.pdf';
$pdf->Output($name,'D');
break;				
		break;		
	
	
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dn_submit":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dn_submit":
		check_authority('070',"add");	
		


		$parm = array(	
										'id'					=>	$PHP_id,
										'submit_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'submit_date'	=>	$TODAY,

			);

		$f1 = $debit->send_submit($parm);


		# �O���ϥΪ̰ʺA

	
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$message = "Submit debit_note On Order: [".$op['dn']['dn_num']."]";
		$log->log_add(0,"106S",$message);
		$op['msg'][] = $message;


//��|��N����J������ǰe�T�����إ߲��`���i��

		$messg  = '<a href="dabit.php?PHP_action=dn_show_apv&PHP_id='.$PHP_id.'&PHP_sr_dn=0">'.$op['dn']['dn_num'].'</a>';
		$notify->system_msg_send('10-6-S','GM',$op['dn']['dn_num'],$messg);


		$op['back_str'] = $PHP_back_str;
		page_display($op,'070', $TPL_DEBIT_SHOW);				
		break;
	
	
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dn_show_apv":

		check_authority(10,3,"view");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


		$op['back_str'] = "index2.php?PHP_action=order_apv&PHP_sr_dn=".$PHP_sr_dn;
		page_display($op, 10, 3, $TPL_DEBIT_SHOW_APV);				
		break;		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_dn_apv":

		check_authority(10,3,"view");


		$parm = array(	
										'id'					=>	$PHP_id,
										'submit_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'submit_date'	=>	$TODAY,

			);

		$f1 = $debit->send_apv($parm);


		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$message = "Approval Debit Note On: [".$op['dn']['dn_num']."]";
		$log->log_add(0,"106S",$message);


		$op['back_str'] = $PHP_back_str;
		$op['msg'][] = $message;
		page_display($op, 10, 3, $TPL_DEBIT_SHOW_APV);				
		break;				
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "reject_dn_apv":

		check_authority(10,3,"view");
		$f1 = $debit->update_fields('status', '5', $PHP_id);

		$argv2 = array(	"dn_id"	=>	$PHP_id,
										"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"des"			=>	"APV REJECT : for - ".$PHP_detail
						);
		$debit->add_log($argv2);
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$message = "Reject Debit note On : [".$op['dn']['dn_num']."]";
		$log->log_add(0,"106S",$message);


		$op['back_str'] = $PHP_back_str;
		$op['msg'][] = $message;
		page_display($op, 10, 3, $TPL_DEBIT_SHOW_APV);				
		break;						
		
		
		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_dn_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_dn_add":
		check_authority('070',"add");	
		$oth_dept=0;	
		if(isset($PHP_team))
		{
			for($i=0; $i < 6; $i++)
			{
				if(isset($PHP_team[$i]))$oth_dept+=$PHP_team[$i];
			}
		}
		$hend = 'DN'.date('y').'-';
		$dn_num = $debit->get_no($hend,'dn_num','debit');

		$PHP_dn_des = str_replace("'","\'",$PHP_dn_des);		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$PHP_dn_des = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_dn_des);
		}			
	
		if(strstr($PHP_dn_des,'&#'))  //�ഫ�c²��
		{
			$PHP_dn_des = $ch_cov->check_cov($PHP_dn_des);
		}	
		
	$ord_num = substr($PHP_order,0,-1);

		$parm = array(	"ord_num"			=>	$ord_num,
										"dn_num"			=>	$dn_num,
										"dept"				=>	$PHP_dept,
										"dn_to"				=>	$PHP_dn_to,										
										'chk_to'			=>	$PHP_chk_to,
										"dn_date"			=>	$TODAY,										
										"dn_des"			=>	$PHP_dn_des,
										"dn_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"currency"		=>	$PHP_currency,
										"price"				=>	$PHP_price,
										'oth_dept'		=>	$oth_dept,
										"po_num"			=>	$PHP_po,
										"cust_po"			=>	$PHP_cust_po
			);


		$f1_id = $debit->add($parm);

/*
	$messg  = '<br><br>���`���i : <a href="exception.php?PHP_action=exc_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a><br>';
	$messg .= '���I���q��s���s�����`���i<br><br>';
	$messg = $notify->cov_html($messg);  //message add
*/
		if(isset($PHP_team))
		{
			for($i=0; $i < 6; $i++)
			{
				if(isset($PHP_team[$i]))
				{
					
					$parm = array(	"dn_id"			=>	$f1_id,
													"comm_dept"		=>	$dept_ary[$PHP_team[$i]],
													"comm"				=>	'',																			
									);
					$f2 = $debit->add_comm($parm);
					
					$send_dept = $dept_ary[$PHP_team[$i]];

					//�ǰe���`���i�[�J�N���T��
					$messg  = '<a href="dabit.php?PHP_action=dn_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&SCH_to=">'.$PHP_order.'</a>';
					$notify->system_msg_send('10-6-A',$send_dept,$PHP_order,$messg);

				}

			
			}
		}
	
	

		# �O���ϥΪ̰ʺA

		$message = "Append Debit note On Order: [".$ord_num."]";
		$log->log_add(0,"106A",$message);
		$op['msg'][] = $message;


	
	$ord_num = explode(',',$PHP_order);
	for($i=0; $i<sizeof($ord_num); $i++)
	{
		$argv2 = array(	"order_num"	=>	$ord_num[$i],
										"user"	  	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"des"		    =>	' <a href="javascript:open_dn('.$f1_id.')">���� </a> by '. $GLOBALS['SCACHE']['ADMIN']['name']
						);

		if (!$order_log->add($argv2)) {
			$op['msg'] = $order_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}		
	}
	

		if($PHP_po) 
		{
			$PHP_aply_num = "PA".substr($PHP_po,2);
		  $parm= array(	'ap_num'		=>	$PHP_aply_num,
										'item'			=>	'Document',
										'des'				=>	' <a href="javascript:open_dn('.$f1_id.')">���� </a> by '. $GLOBALS['SCACHE']['ADMIN']['name'],
										'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'k_date'		=>	date('Y-m-d')
				);
			$fp1=$apply->add_log($parm);
			
			
		}

	
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op=$debit->get($f1_id)) {
				$op['msg']= $debit->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


		$op['back_str'] = "dabit.php?PHP_action=dn_search&PHP_sr_startno=0&SCH_ord=&SCH_to=";
		page_display($op,'070', $TPL_DEBIT_SHOW);				
		break;
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "dn_show":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dn_del":

		check_authority('070',"view");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			$op=$debit->del($PHP_id);

		$message = "Delete Debit note On : [".$PHP_dn_num."]";
		$log->log_add(0,"106D",$message);



		$redir_str = "dabit.php?PHP_action=dn_search&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord."&SCH_to=".$SCH_to."&PHP_msg=".$message;
		redirect_page($redir_str);
		
}   // end case ---------

?>
