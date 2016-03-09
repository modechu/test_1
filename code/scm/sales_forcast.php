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
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "sales_forecast":	job 84  �~�ȹw��
//	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "sales_forecast":
		check_authority(8,4,"view");
		$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
	
		$op['dept'] = $arry2->select($sales_dept_ary,'','PHP_dept','select','');  	
		$op['year_1'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year','select','');  	

		$op['msg'] = $order->msg->get(2);

//080725message�W�[		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
	
			page_display($op, 8, 4, $TPL_SF_SEARCH);    	    
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "sales_forecast":	job 84  �~�ȹw��
//	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "capacity_add":
	check_authority(8,1,"add");
//  �ˬd�ݬ� ��Ʈw�O�_�w�g �s�b ??
   if($c = $forecast->get($PHP_dept, $PHP_year)){
			$op['msg'][]= 'year :'.$PHP_year.'\'s forecast dept : [ '.$PHP_dept' ] is exist already !';
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	}

		$op['msg'] ='';

		$op['dept'] = $PHP_dept;
		$op['year'] = $PHP_year;
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['year1']){
			$op['msg'][] = "Error! you have to select one target YEAR at least !";
		}

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		page_display($op, 8, 1, $TPL_SF_ADD); 
		break;








//-------------------------------------------------------------------------

}   // end case ---------

?>
