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

$op = array();




switch ($PHP_action) {
//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 101  �q ��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pre_order":
		check_authority(10,1,"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];		// �P�w�i�J������Team
		if($team == 'MD')
		{
			$dept_id = $user_dept;
			$where_str = " WHERE dept = '".$dept_id."' ";
		}


		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","get_dept(this)");  
		}

		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);
		// creat cust combo box
		// ���X �Ȥ�N��
		$where_str=$where_str." order by cust_s_name"; //��cust_s_name�Ƨ�
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
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','check_add(this)',$cust_def_vue); 

		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style'] =  $arry2->select($style_def,'','SCH_style','select','');  	


			// creat factory combo box
		  	
		page_display($op, 10, 1, $TPL_PRE_ORDER);			    	    
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pre_order_add":
		check_authority(10,1,"add");
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������

		// ���X�~�X...
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-1);		
 	
		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style'] =  $arry2->select($style_def,'','PHP_style','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,'pc','PHP_unit','select','');  	

		$where_str="WHERE dept = '".$PHP_dept_code."' order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['agent_select'] =  $arry2->select($cust_value,'','PHP_agent','select','',$cust_def_vue); 

		$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');


		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['msg']= $order->msg->get(2);
		$op['cust_id'] = $PHP_cust;
		$op['dept_id']	 = $PHP_dept_code;
		// pre  �q��s��....
		$op['order_precode'] = 'P'.$year_code.$PHP_cust."-xxx";
		// 2005/07/30 �[�J �� menu�i�J�s�W�᪺ back page �u�V �����C��
page_display($op, 10, 1,$TPL_PRE_ORDER_ADD);		    	    
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_pre_order_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_pre_order_add":
		check_authority(10,1,"add");		

		// �s�s �s�@�O ���X-----------------------------------------------------------
		// ���X�~�X... �H��J�ɤ��~���~�X[����]
			$dt = decode_date(1);
			$year_code = substr($dt['year'],-1);
		//  �s�s �q �� ���X �]�P�ɧ�sdept�ɤ���num��[csv]
		$pre_ord_num = $dept->get_order_num($PHP_dept, $year_code,$PHP_cust);  

		$ie1 = number_format(($PHP_spt/$GLOBALS['IE_TIME']),2,'.','');
		$su =  number_format(($PHP_qty * $ie1),0,'','');

		$parm = array(	"pre_ord"			=>	$pre_ord_num,
										"dept"				=>	$PHP_dept,
										"cust"				=>	$PHP_cust,
										"style"				=>	$PHP_style,
										"qty"					=>	$PHP_qty,
										"unit"				=>	$PHP_unit,
										"spt"					=>	$PHP_spt,		
										"etd"					=>	$PHP_etd,
										"agent"				=> 	$PHP_agent,				
										"pic"					=>	$PHP_pic,
										"pic_upload"	=>	$PHP_pic_upload,
										"creator"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"open_date"		=>	$dt['date'],			
										"ie"					=>	$ie1,
										"su"					=>	$su,
										"fty"					=>	$PHP_fty
			);

		// check �g�J order ��	���		
			$op['order'] = $parm;		
		//------------------ add to DB	
		$f1 = $pre_ord->add($parm);

		if (!$f1) {  // �S�����\��J��Ʈ�
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
			break;

		}   // end if (!$F1)--------- ���\��J ---------------------------------




		# �O���ϥΪ̰ʺA

		$message = "append order: [".$parm['pre_ord']."]";
		$log->log_add(0,"101A",$message);
		$op['msg'][] = $message;
		
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($f1)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		$op['back_str'] = "pre_order.php?PHP_action=pre_order_search&PHP_sr_startno=0&SCH_per_order=&PHP_cust=&SCH_style=&SCH_str=&SCH_end";
		page_display($op, 10, 1, $TPL_PRE_ORDER_SHOW);				
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_search":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pre_order_search":
		check_authority(10,1,"view");

					//�i�Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
			if (!$op = $pre_ord->search(1)) {  
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['msg']= $pre_ord->msg->get(2);
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		$op['back_str'] = "&SCH_per_order=".$SCH_per_order."&PHP_cust=".$PHP_cust."&SCH_style=".$SCH_style."&SCH_str=".$SCH_str."&SCH_end=".$SCH_end;
		
		page_display($op, 10, 1, $TPL_PRE_ORDER_LIST);
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "pre_order_view":

		check_authority(1,5,"add");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		$op['back_str'] = "pre_order.php?PHP_action=pre_order_search&PHP_sr_startno=".$PHP_sr_startno."&SCH_per_order=".$SCH_per_order."&PHP_cust=".$PHP_cust."&SCH_style=".$SCH_style."&SCH_str=".$SCH_str."&SCH_end=".$SCH_end;
		page_display($op, 10, 1, $TPL_PRE_ORDER_SHOW);				
		break;
		
		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_edit":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "pre_order_edit":

		check_authority(1,5,"add");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";
		
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style'] =  $arry2->select($style_def,$op['order']['style'],'PHP_style','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,'pc','PHP_unit','select','');  	

		$where_str="WHERE dept = '".$op['order']['dept']."' order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['agent_select'] =  $arry2->select($cust_value,$op['order']['agent'],'PHP_agent','select','',$cust_def_vue); 
		$op['factory'] = $arry2->select($FACTORY,$op['order']['fty'],'PHP_fty','select','');

		$op['back_str'] = $PHP_back_str;
		page_display($op, 10, 1, $TPL_PRE_ORDER_EDIT);				
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_pre_order_edit":
		check_authority(10,1,"add");		
		
		$ie1 = number_format(($PHP_spt/$GLOBALS['IE_TIME']),2,'.','');
		$su =  number_format(($PHP_qty * $ie1),0,'','');

		$parm = array(	
										"style"					=>	$PHP_style,
										"qty"						=>	$PHP_qty,
										"unit"					=>	$PHP_unit,
										"spt"						=>	$PHP_spt,		
										"etd"						=>	$PHP_etd,
										"agent"					=> 	$PHP_agent,				
										"pic"						=>	$PHP_pic,
										"pic_upload"		=>	$PHP_pic_upload,
										"last_updater"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"last_update"		=>	$dt['date'],			
										"ie"						=>	$ie1,
										"su"						=>	$su,
										"id"						=>	$PHP_id,
										"pre_ord"				=>	$PHP_pre_ord,
										"fty"						=>	$PHP_fty
			);
		//------------------ add to DB	
		$f1 = $pre_ord->edit($parm);

		if (!$f1) {  // �S�����\��J��Ʈ�
				$op['msg'] = $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
			break;

		}   // end if (!$F1)--------- ���\��J ---------------------------------




		# �O���ϥΪ̰ʺA

		$message = "Update pre-order: [".$parm['pre_ord']."]";
		$log->log_add(0,"101E",$message);
		$op['msg'][] = $message;
		
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($f1)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		$op['back_str'] = $PHP_back_str;
		page_display($op, 10, 1, $TPL_PRE_ORDER_SHOW);				
		break;

//-------------------------------------------------------------------------
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_submit":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "pre_order_submit":

		check_authority(1,5,"add");


		$parm = array(	
										"submit_user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"submit_date"		=>	date('Y-m-d'),			
										"id"						=>	$PHP_id,
			);
			$pre_ord->send_submit($parm);

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";		
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		$message = "Submit pre-order: [".$op['order']['pre_ord']."]";
		$log->log_add(0,"101E",$message);
		$op['msg'][] = $message;

		$op['back_str'] = $PHP_back_str;
		page_display($op, 10, 1, $TPL_PRE_ORDER_SHOW);				
		break;
		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_submit":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "pre_order_qty":

		check_authority(1,5,"add");


		$parm = array(	"ord_qty"				=>	$PHP_ord_qty,
										"last_updater"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"last_update"		=>	date('Y-m-d'),			
										"id"						=>	$PHP_id,
			);
			$pre_ord->add_ord_qty($parm);

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";		
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		$message = "Write-off Qty : [".$PHP_ord_qty."] on pre-order: [".$op['order']['pre_ord']."]";
		$log->log_add(0,"101E",$message);
		$op['msg'][] = $message;

		$op['back_str'] = $PHP_back_str;
		page_display($op, 10, 1, $TPL_PRE_ORDER_SHOW);				
		break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_list_qty":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "pre_list_qty":

		check_authority(1,5,"add");


		$parm = array(	"ord_qty"				=>	$PHP_ord_qty,
										"last_updater"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"last_update"		=>	date('Y-m-d'),			
										"id"						=>	$PHP_id,
			);
			$pre_ord->add_ord_qty($parm);


		$message = "Write-off Qty : [".$PHP_ord_qty."] on pre-order: [".$PHP_pre_ord."]";


		$log->log_add(0,"101E",$message);
//		$op['msg'][] = $message;

			$redir_str = 'pre_order.php?PHP_action=pre_order_search'.$PHP_back_str."&PHP_msg=".$message;
			redirect_page($redir_str);
			
		break;		
	
	
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_edit":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "revise_pre_order":

		check_authority(1,5,"add");

		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";
		
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style'] =  $arry2->select($style_def,$op['order']['style'],'PHP_style','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,'pc','PHP_unit','select','');  	

		$where_str="WHERE dept = '".$op['order']['dept']."' order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['agent_select'] =  $arry2->select($cust_value,$op['order']['agent'],'PHP_agent','select','',$cust_def_vue); 
		$op['factory'] = $arry2->select($FACTORY,$op['order']['fty'],'PHP_fty','select','');


		page_display($op, 10, 1, $TPL_PRE_ORDER_REVISE);				
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_pre_order_revise":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_pre_order_revise":
		check_authority(10,1,"add");		
		
		$ie1 = number_format(($PHP_spt/$GLOBALS['IE_TIME']),2,'.','');
		$su =  number_format(($PHP_qty * $ie1),0,'','');

		$parm = array(	
										"style"					=>	$PHP_style,
										"qty"						=>	$PHP_qty,
										"unit"					=>	$PHP_unit,
										"spt"						=>	$PHP_spt,		
										"etd"						=>	$PHP_etd,
										"agent"					=> 	$PHP_agent,				
										"pic"						=>	$PHP_pic,
										"pic_upload"		=>	$PHP_pic_upload,
										"last_updater"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"last_update"		=>	$dt['date'],			
										"ie"						=>	$ie1,
										"su"						=>	$su,
										"id"						=>	$PHP_id,
										"pre_ord"				=>	$PHP_pre_ord,
										"revise"				=>	($PHP_revise+1),
										"fty"						=>	$PHP_fty

			);
		//------------------ add to DB	
		$f1 = $pre_ord->edit($parm,1);

		if (!$f1) {  // �S�����\��J��Ʈ�
				$op['msg'] = $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
			break;

		}   // end if (!$F1)--------- ���\��J ---------------------------------




		# �O���ϥΪ̰ʺA

		$message = "Update pre-order: [".$parm['pre_ord']."]";
		$log->log_add(0,"101E",$message);
		$op['msg'][] = $message;
		
		// ���X�ӵ� order ��� ------------------------------------------------- 
			if (!$op['order']=$pre_ord->get($f1)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/preord_pic/".$op['order']['pre_ord'].".jpg")){
			$op['main_pic'] = "./preord_pic/".$op['order']['pre_ord'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 10, 1, $TPL_PRE_ORDER_REV_SHOW);				
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 job 81-4-1    SHIPPed 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "mm_per_order_show":

	check_authority(8,2,"view");
		$string = $PHP_year.'-'.$PHP_month;
		$op['per_order'] = array();

		if (!$result = $pre_ord->get_by_mm($PHP_fty,$PHP_year,$PHP_month)) {   
			$op['msg']= $pre_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ttl_rem_qty']=$op['ttl_qty']=$op['ttl_su'] = 0;
		$j=0;

	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i ���X�f�O�� ����
			$op['pre_order'][$i] = $result[$i];
			$op['pre_order'][$i]['rem_qty'] = $op['pre_order'][$i]['qty'] - $op['pre_order'][$i]['ord_qty'];
			if($op['pre_order'][$i]['rem_qty'] < 0)$op['pre_order'][$i]['rem_qty'] = 0;
			$op['ttl_qty']+=$result[$i]['qty'];
			$op['ttl_su']+=$result[$i]['su'];
			
			$op['ttl_rem_qty']+=$op['pre_order'][$i]['rem_qty'];
			
			
		
		}
	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['mon'] = $PHP_month;
		    	    
	page_display($op, 8, 2, $TPL_ETP_MON_PRE_ORD);
	break;	
			
		
		
		
		
		
}   // end case ---------

?>
