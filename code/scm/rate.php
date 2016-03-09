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
#		case "rate":	 	JOB 15V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "rate":

		check_authority("007","view");
/*
		if (!$op = $rate->search(1)) {	//�ײv�C��
			$op['msg']= $rate->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,'','PHP_currency','select',''); 	//���O�U�Ԧ�
		$op['CURRENCY_select2'] = $arry2->select($CURRENCY,'','PHP_currency1','select',''); //���O�U�Ԧ�
*/
		$op['year_select'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year','select',''); 	// �~���U�Ԧ�
		$op['month_select'] = $arry2->select($MONTH_WORK,date('m'),'PHP_month','select',''); 	//����U�Ԧ�
		$op['today']=date('Y-m-d');
		$op['msg'] = $rate->msg->get(2);
		
		page_display($op, "007", $TPL_RATE_SEARCH);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_rate_search":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rate_search":
		$mm = $PHP_year.'-'.$PHP_month;
		$mm_rate = $rate->get_mm_rate($mm);
		$mm_day = getDaysInMonth($PHP_month,$PHP_year);
		
		if(!$mm_rate)
		{
			$op['year_select'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year','select',''); 	// �~���U�Ԧ�
			$op['month_select'] = $arry2->select($MONTH_WORK,date('m'),'PHP_month','select',''); 	//����U�Ԧ�
			$op['today']=date('Y-m-d');
			$op['msg'][] = "This Month is not exist.";		
			page_display($op, "007", $TPL_RATE_SEARCH);
		break;
		}else{
			$op['rate'] = $mm_rate;
			$op['action_mk'] = 'do_rate_edit';
		}
		page_display($op, "007", $TPL_RATE_VIEW);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rate_add":
		$mm = $PHP_year.'-'.$PHP_month;
		$mm_rate = $rate->get_mm_rate($mm);
		$mm_day = getDaysInMonth($PHP_month,$PHP_year);
		
		if(!$mm_rate)
		{
			for($i=0; $i<$mm_day; $i++)
			{
				$op['rate'][$i]['i'] = $i;
				$op['rate'][$i]['date'] = $PHP_year.'-'.$PHP_month.'-'.($i+1);						
			}
			$op['action_mk'] = 'do_rate_add';
		}else{
			$op['rate'] = $mm_rate;
			$op['action_mk'] = 'do_rate_edit';
		}
		page_display($op, "007", $TPL_RATE_ADD);
	break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rate_add":

		check_authority("007","add");

		for($i=0; $i<sizeof($PHP_date); $i++)
		{
			$parm = array(	'rate_date'	=>	$PHP_date[$i],
											'USD'				=>	$PHP_usd[$i],
											'HKD'				=>	$PHP_hkd[$i],
											'GBP'				=>	$PHP_gbp[$i],
											'JPY'				=>	$PHP_jpy[$i],
											'EUR'				=>	$PHP_eur[$i],
											'RMB'				=>	$PHP_rmb[$i],
											);
			$f1 = $rate->add_in($parm);	//�s�W�ײv		
			$parm_2 = array('rate_date'	=>	$PHP_date[$i],
											'USD'				=>	$PHP_usd[$i],
											'HKD'				=>	$PHP_hkd[$i],
											'GBP'				=>	$PHP_gbp[$i],
											'JPY'				=>	$PHP_jpy[$i],
											'EUR'				=>	$PHP_eur[$i],
											'RMB'				=>	$PHP_rmb[$i],
											);
			$f1 = $rate->add_out($parm);	//�s�W�ײv										
		}		
	
		if ($f1) {			
			$tmp_date = explode('-',$PHP_date[0]);
			$log->log_add(0,"15A","APPEND RATE: [".$tmp_date[0]."-".$tmp_date[1]."]" );	# �O���ϥΪ̰ʺA
		}	

		
		
		$op['msg'][] = "APPEND RATE: [".$tmp_date[0]."-".$tmp_date[1]."]";
		$mm = $tmp_date[0]."-".$tmp_date[1];
		$op['rate'] = $rate->get_mm_rate($mm);
			
		$op['today']=date('Y-m-d');
		page_display($op, "007", $TPL_RATE_VIEW);
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rate_edit":

		check_authority("007","add");

		for($i=0; $i<sizeof($PHP_date); $i++)
		{
			$parm = array(	'rate_date'	=>	$PHP_date[$i],
											'USD'				=>	$PHP_usd[$i],
											'HKD'				=>	$PHP_hkd[$i],
											'GBP'				=>	$PHP_gbp[$i],
											'JPY'				=>	$PHP_jpy[$i],
											'EUR'				=>	$PHP_eur[$i],
											'RMB'				=>	$PHP_rmb[$i],
											);
			$f1 = $rate->edit_in($parm);	//�s�W�ײv		
			$parm_2 = array('rate_date'	=>	$PHP_date[$i],
											'USD'				=>	$PHP_usd[$i],
											'HKD'				=>	$PHP_hkd[$i],
											'GBP'				=>	$PHP_gbp[$i],
											'JPY'				=>	$PHP_jpy[$i],
											'EUR'				=>	$PHP_eur[$i],
											'RMB'				=>	$PHP_rmb[$i],
											);
			$f1 = $rate->edit_out($parm);	//�s�W�ײv										
		}		
		
		if ($f1) {			
			$tmp_date = explode('-',$PHP_date[0]);
			$log->log_add(0,"15A","APPEND RATE: [".$tmp_date[0]."-".$tmp_date[1]."]" );	# �O���ϥΪ̰ʺA
					
		}

		
		
		$op['msg'][] = "APPEND RATE: [".$tmp_date[0]."-".$tmp_date[1]."]";
		$mm = $tmp_date[0]."-".$tmp_date[1];
		$op['rate'] = $rate->get_mm_rate($mm);
		$op['today']=date('Y-m-d');
		page_display($op, "007", $TPL_RATE_VIEW);
		break;
		
		

/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "rate_search":	 	JOB 15V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rate_search":
 
		check_authority("007","view");
		$cgi_link="&PHP_year=".$PHP_year."&PHP_month=".$PHP_month."&PHP_currency=".$PHP_currency;
		$cgi= array( "PHP_year"		=>$PHP_year,
					 "PHP_month"	=>$PHP_month,
					 "PHP_currency"	=>$PHP_currency					
		);
		if (!$op = $rate->search(0)) {	//�ײv�C��
			$op['msg']= $parts->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA" || $GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ||  $GLOBALS['SCACHE']['ADMIN']['dept'] == "AC" ||  $GLOBALS['SCACHE']['ADMIN']['dept'] == "MS") { 
			$op['manager_flag'] = 1;
		}
		$op['cgi_link'] = $cgi_link;
		$op['cgi']=$cgi;
		$op['msg']= $rate->msg->get(2);
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$PHP_currency,'PHP_currency','select',''); 
		$op['CURRENCY_select2'] = $arry2->select($CURRENCY,'','PHP_currency1','select',''); 
		$op['year_select'] = $arry2->select($YEAR_WORK,$PHP_year,'PHP_year','select',''); 
		$op['month_select'] = $arry2->select($MONTH_WORK,$PHP_month,'PHP_month','select',''); 
		$op['today']=date('Y-m-d');

		page_display($op, "007", $TPL_RATE);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rate_update":

		check_authority("007","edit");

		$op['rates'] = $rate->get($PHP_id);	//���o�ײv�Ӷ����e
		$op['msg'][] = "�ק� �ײv �O���G".$op['rates']['date'].$op['rates']['currency'];
		$cgi= array( "PHP_year"		=>$PHP_year,
					 "PHP_month"	=>$PHP_month,
					 "PHP_currency"	=>$PHP_currency					
		);

		// �ǤJ�������Ѽ�
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end	
		$op['cgi']=$cgi;		
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$PHP_currency,'PHP_currency','select',''); 
		$op['CURRENCY_select2'] = $arry2->select($CURRENCY,$op['rates']['currency'],'PHP_currency1','select',''); 
		$op['year_select'] = $arry2->select($YEAR_WORK,$PHP_year,'PHP_year','select',''); 
		$op['month_select'] = $arry2->select($MONTH_WORK,$PHP_month,'PHP_month','select',''); 
		$op['today']=date('Y-m-d');
		page_display($op, "007", $TPL_RATE_UPDATE);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_dept_update":			JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rate_update":
		check_authority("007","edit");

		$argv = array(	"id"			=>	$PHP_id,
						"in"			=>	$PHP_in,
						"out"			=>	$PHP_out
					);
		$cgi= array( "PHP_year"		=>$PHP_year,
					 "PHP_month"	=>$PHP_month,
					 "PHP_currency"	=>$PHP_currency					
					);		
		$op['rates'] = $rate->get($PHP_id);			
		if (!$rate->update($argv)) {	//��s�ײv		
		$op['msg'] = $rate->msg->get(2);		
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;	// �ǤJ�������Ѽ�
#####2006.11.14�W�[�O���ثe���� end	
		$op['CURRENCY_select2'] = $arry2->select($CURRENCY,$op['rates']['currency'],'PHP_currency1','select','');
		$op['cgi']=$cgi;
		page_display($op, 1, 4, $TPL_RATE_UPDATE);
		break;
		}		
		$message = " �ק� �ײv �O��".$op['rates']['date'].$op['rates']['currency'];
		$log->log_add(0,"15E",$message);# �O���ϥΪ̰ʺA

		if (!$op = $rate->search(0)) {
			$op['msg']= $rate->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['cgi']=$cgi;		
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$PHP_currency,'PHP_currency','select',''); 
		$op['CURRENCY_select2'] = $arry2->select($CURRENCY,'','PHP_currency1','select',''); 
		$op['year_select'] = $arry2->select($YEAR_WORK,$PHP_year,'PHP_year','select',''); 
		$op['month_select'] = $arry2->select($MONTH_WORK,$PHP_month,'PHP_month','select',''); 
		$op['today']=date('Y-m-d');
		$op['msg'][] = $message;
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end	

		page_display($op, 1, 4, $TPL_RATE);
		break;
		*/
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rate_update":

		check_authority("007","edit");

		$op['rate'] = $rate->get($PHP_date);	//���o�ײv�Ӷ����e

		// �ǤJ�������Ѽ�
//		$op['cgi']=$cgi;		
		$op['today']=date('Y-m-d');
		$tmp = explode('-',$PHP_date);
		$op['year'] = $tmp[0];
		$op['month'] = $tmp[1];
		page_display($op, "007", $TPL_RATE_EDIT);

		break;	
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rate_update":

		check_authority("007","edit");

		 

		$parm = array('field_name' 	=>	$PHP_currency,
									'field_value'	=>	$PHP_in,
									'date'				=>	$PHP_date,
									);
		$rate->update_field($parm,'rate_in');							

		$parm = array('field_name' 	=>	$PHP_currency,
									'field_value'	=>	$PHP_out,
									'date'				=>	$PHP_date,
									);
		$rate->update_field($parm,'rate_out');	
		
		$op['rate'] = $rate->get($PHP_date);	//���o�ײv�Ӷ����e

		// �ǤJ�������Ѽ�
//		$op['cgi']=$cgi;	
		$message = "Update [".$PHP_date."] Rate : [".$PHP_currency."] ON [".$TODAY."]";
		$log->log_add(0,"15E",$message );	# �O���ϥΪ̰ʺA

		$op['msg'][]= $message;
		$op['today']=date('Y-m-d');
		$tmp = explode('-',$PHP_date);
		$op['year'] = $tmp[0];
		$op['month'] = $tmp[1];
		page_display($op, "007", $TPL_RATE_EDIT);
		break;				
//-------------------------------------------------------------------------

}   // end case ---------

?>
