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
#			monitor.php  ￥Dμ{|!
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];
$TPL_EXPENSE_COMPARE = "expense_compare.html";
$TPL_EXPENSE_COMPARE_DET = "expense_compare_det.html";
$TPL_EXPENSE_COMPARE_DET_FOB = "expense_compare_det_fob.html";


require_once "init.object.php";
$op = array();

$P_LINE = array (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

// echo $PHP_action;
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "expense":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "expense":
 	check_authority('059',"view");
		$op['year'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year','select','');  	
		$op['month'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');  
		$op['month_str'] = $arry2->select($MONTH_WORK,'','PHP_month_str','select','');
		$op['month_end'] = $arry2->select($MONTH_WORK,'','PHP_month_end','select','');	
		// $dept_ary = array('K0','PM','DA','RD','J1');
		$dept_dis_ary = array('DA','KA','LY','CF','RD','PROJECT','SALES','PM');
		$op['dept'] = $arry2->select($dept_dis_ary,'','PHP_dept','select','',$dept_ary);
/*
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------	
		$op['sales_dept'] = $arry2->select($sales_dept_ary,'','PHP_dept','select','');
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		for($i=0; $i<sizeof($sales_dept_ary); $I++)
		{
			if($user_dept == $sales_dept_ary[$i])
			{
				$op['sales_dept'] = "<B>".$user_dept."</B><input type='hidden' name='PHP_dept' value='".$user_dept."'>";
			}
		}
*/	
		$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	

		$where_str="order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for($i=0; $i<sizeof($cust_def); $i++)$cust_key[$i] = $cust_def_vue[$i].' - '.$cust_def[$i];
		$op['cust_select'] =  $arry2->select($cust_key,'','PHP_cust','select','',$cust_def_vue); 


//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
	if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
	page_display($op,'059', $TPL_EXPENSE);    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "expense_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "expense_add":
 	check_authority('059',"add");
	if(!$PHP_year)$PHP_year = date('Y');

	if(!$PHP_dept)
	{
		$msg = "Please select dept first!!";
		$redirect="expense.php?PHP_action=expense&PHP_msg=".$msg;
		redirect_page($redirect);
	}

	if(!$expense->check($PHP_year,$PHP_dept))
	{
		$msg = '['.$PHP_dept.']['.$PHP_year.'] is exist, please check again';
		$redirect="expense.php?PHP_action=expense&PHP_msg=".$msg;
		redirect_page($redirect);
	}
	
	$op['mm_ary']=array('01','02','03','04','05','06','07','08','09','10','11','12');
	$op['txt'] = array('cost','salary','rent','sationery','travel','freight','postal','fix','ad','water','insurance',
										 'communicat','donate','taxes','bed_dabt','dpereciation','other_1','book','food',
										 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
										 'sample','bank','pack','labor_2','health','martrial','share','prize','retire_1',
										 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safty');
	
 
	
	
	
	$op['yy'] = $PHP_year;
	$op['dept'] = $PHP_dept;

	page_display($op,'059', $TPL_EX_FORCAST_ADD);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_expense_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_expense_add":
 	check_authority('059',"add");
 	 foreach($PHP_f_salary as $key => $value)
 	 {
 		$parm = array ('yy'						=>	$PHP_yy,										'mm'				=>	$key,
 									 'type'					=>	'F',					 							'dept'			=>	$PHP_dept,
 									 'cost'				=>	$PHP_f_cost[$key],				
 									 'salary'				=>	$PHP_f_salary[$key],				'rent'			=>	$PHP_f_rent[$key],
 									 'sationery'		=>	$PHP_f_sationery[$key],			'travel'		=>	$PHP_f_travel[$key],
 									 'freight'			=>	$PHP_f_freight[$key],				'postal'		=>	$PHP_f_postal[$key],
 									 'fix'					=>	$PHP_f_fix[$key],						'ad'				=>	$PHP_f_ad[$key],
 									 'water'				=>	$PHP_f_water[$key],					'insurance'	=>	$PHP_f_insurance[$key],
 									 'communicat'		=>	$PHP_f_communicat[$key],		'donate'		=>	$PHP_f_donate[$key],
 									 'taxes'				=>	$PHP_f_taxes[$key],					'bed_dabt'	=>	$PHP_f_bed_dabt[$key],
 									 'depreciation'	=>	$PHP_f_dpereciation[$key],	'other_1'		=>	$PHP_f_other_1[$key],
 									 'book'					=>	$PHP_f_book[$key],					'food'			=>	$PHP_f_food[$key],
 									 'welfare'			=>	$PHP_f_welfare[$key],				'rd'				=>	$PHP_f_rd[$key],
 									 'teach'				=>	$PHP_f_teach[$key],					'medicine'	=>	$PHP_f_medicine[$key],
 									 'commission'		=>	$PHP_f_commission[$key],		'labor_1'		=>	$PHP_f_labor_1[$key],
 									 'other_2'			=>	$PHP_f_other_2[$key],				'other_3'		=>	$PHP_f_other_3[$key],
 									 'customs'			=>	$PHP_f_customs[$key],				'sample'		=>	$PHP_f_sample[$key],
 									 'bank'					=>	$PHP_f_bank[$key],					'pack'			=>	$PHP_f_pack[$key],
 									 'labor_2'			=>	$PHP_f_labor_2[$key],				'health'		=>	$PHP_f_health[$key],
 									 'material'			=>	$PHP_f_martrial[$key],			'share'			=>	$PHP_f_share[$key],
 									 'prize'				=>	$PHP_f_prize[$key],					'retire_1'	=>	$PHP_f_retire_1[$key],
 									 'reach_cost'		=>	$PHP_f_reach_cost[$key],		'build'			=>	$PHP_f_build[$key],
 									 'retire_2'			=>	$PHP_f_retire_2[$key],			'cloth_fix'	=>	$PHP_f_cloth_fix[$key],
 									 'work_cloth'		=>	$PHP_f_work_cloth[$key],		'manage'		=>	$PHP_f_manage[$key],
 									 'safety'				=>	$PHP_f_safty[$key],				 								
 									 );
 		$f1 = $expense->add($parm);
 		
  	$parm = array ('yy'						=>	$PHP_yy,	'mm'					=>	$key,		'type'				=>	'E',
  					 			 'dept'					=>	$PHP_dept,'cost'			=>	'','salary'			=>	'',			'rent'				=>	'',
 									 'sationery'		=>	'',				'travel'			=>	'',			'freight'			=>	'',
 									 'postal'				=>	'',				'fix'					=>	'',			'ad'					=>	'', 									 
 									 'water'				=>	'',				'insurance'		=>	'',			'communicat'	=>	'',
 									 'donate'				=>	'',				'taxes'				=>	'',			'bed_dabt'		=>	'', 									 
 									 'depreciation'	=>	'',				'other_1'			=>	'',			'book'				=>	'',
 									 'food'					=>	'',				'welfare'			=>	'',			'rd'					=>	'', 									 
 									 'teach'				=>	'',				'medicine'		=>	'',			'commission'	=>	'',
 									 'labor_1'			=>	'',				'other_2'			=>	'',			'other_3'			=>	'', 									 
 									 'customs'			=>	'',				'sample'			=>	'',			'bank'				=>	'',
 									 'pack'					=>	'',				'labor_2'			=>	'',			'health'			=>	'', 									 
 									 'material'			=>	'',				'share'				=>	'',			'prize'				=>	'',
 									 'retire_1'			=>	'',				'reach_cost'	=>	'',			'build'				=>	'', 									 
 									 'retire_2'			=>	'',				'cloth_fix'		=>	'',			'work_cloth'	=>	'',
 									 	'manage'			=>	'',				'safety'				=>	'',	 									 			 								
 									 );		
 		$f1 = $expense->add($parm);
 	}
 		$msg = 'SUCCESS APPEND EXPENSE ON YEAR : ['.$PHP_yy.'] DEPT :['.$PHP_dept.']';
 		if ($f1) $log->log_add(0,"84A",$msg);
 		
		$op = $expense->search_forecast($PHP_yy,$PHP_dept);
		
		$op['msg'][]= $msg;
		$op['mm_ary']=array('01','02','03','04','05','06','07','08','09','10','11','12');
		

	page_display($op,'059', $TPL_EX_FORCAST_VIEW);
	break;		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "expense_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "expense_update":
 	check_authority('059',"add");
	if(!$PHP_year)$PHP_year = date('Y');

	if(!$PHP_dept)
	{
		$msg = "Please select department first!!";
		$redirect="expense.php?PHP_action=expense&PHP_msg=".$msg;
		redirect_page($redirect);
	}
	
	if($expense->check($PHP_year,$PHP_dept))
	{
		$msg = 'YEAR : ['.$PHP_year.'] DEPT :['.$PHP_dept.'] is not exist, please append these first.';
		$redirect="expense.php?PHP_action=expense&PHP_msg=".$msg;
		redirect_page($redirect);
	}
	
		$op = $expense->search_forecast($PHP_year,$PHP_dept);
	
	$op['yy'] = $PHP_year;
//	$op['mm'] = $PHP_month;

	$op['mm_ary']=array('01','02','03','04','05','06','07','08','09','10','11','12');
	$op['txt'] = array('cost','salary','rent','sationery','travel','freight','postal','fix','ad','water','insurance',
										 'communicat','donate','taxes','bed_dabt','dpereciation','other_1','book','food',
										 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
										 'sample','bank','pack','labor_2','health','martrial','share','prize','retire_1',
										 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safty');

	page_display($op,'059', $TPL_EX_FORCAST_EDIT);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_expense_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_expense_update":
 	check_authority('059',"add");
 	foreach($PHP_f_salary as $key => $value)
 	{
 		$parm = array ('cost'				=>	$PHP_f_cost[$key],'salary'				=>	$PHP_f_salary[$key],				'rent'			=>	$PHP_f_rent[$key],
 									 'sationery'		=>	$PHP_f_sationery[$key],			'travel'		=>	$PHP_f_travel[$key],
 									 'freight'			=>	$PHP_f_freight[$key],				'postal'		=>	$PHP_f_postal[$key],
 									 'fix'					=>	$PHP_f_fix[$key],						'ad'				=>	$PHP_f_ad[$key],
 									 'water'				=>	$PHP_f_water[$key],					'insurance'	=>	$PHP_f_insurance[$key],
 									 'communicat'		=>	$PHP_f_communicat[$key],		'donate'		=>	$PHP_f_donate[$key],
 									 'taxes'				=>	$PHP_f_taxes[$key],					'bed_dabt'	=>	$PHP_f_bed_dabt[$key],
 									 'depreciation'	=>	$PHP_f_dpereciation[$key],	'other_1'		=>	$PHP_f_other_1[$key],
 									 'book'					=>	$PHP_f_book[$key],					'food'			=>	$PHP_f_food[$key],
 									 'welfare'			=>	$PHP_f_welfare[$key],				'rd'				=>	$PHP_f_rd[$key],
 									 'teach'				=>	$PHP_f_teach[$key],					'medicine'	=>	$PHP_f_medicine[$key],
 									 'commission'		=>	$PHP_f_commission[$key],		'labor_1'		=>	$PHP_f_labor_1[$key],
 									 'other_2'			=>	$PHP_f_other_2[$key],				'other_3'		=>	$PHP_f_other_3[$key],
 									 'customs'			=>	$PHP_f_customs[$key],				'sample'		=>	$PHP_f_sample[$key],
 									 'bank'					=>	$PHP_f_bank[$key],					'pack'			=>	$PHP_f_pack[$key],
 									 'labor_2'			=>	$PHP_f_labor_2[$key],				'health'		=>	$PHP_f_health[$key],
 									 'material'			=>	$PHP_f_martrial[$key],			'share'			=>	$PHP_f_share[$key],
 									 'prize'				=>	$PHP_f_prize[$key],					'retire_1'	=>	$PHP_f_retire_1[$key],
 									 'reach_cost'		=>	$PHP_f_reach_cost[$key],		'build'			=>	$PHP_f_build[$key],
 									 'retire_2'			=>	$PHP_f_retire_2[$key],			'cloth_fix'	=>	$PHP_f_cloth_fix[$key],
 									 'work_cloth'		=>	$PHP_f_work_cloth[$key],		'manage'		=>	$PHP_f_manage[$key],
 									 'safety'				=>	$PHP_f_safty[$key],				 	'id'				=>	$PHP_f_id[$key]							
 									 );
 		$f1 = $expense->edit($parm);
 	}
 		
 		$msg = 'SUCCESS UPDATE FORECAST ON YEAR :['.$PHP_year.'] DEPT:['.$PHP_dept.']';
 		if ($f1) $log->log_add(0,"84A",$msg);
 		
		$op = $expense->search_forecast($PHP_year,$PHP_dept);
		
		$op['msg'][]= $msg;
		$op['mm_ary']=array('01','02','03','04','05','06','07','08','09','10','11','12');
		

	page_display($op,'059', $TPL_EX_FORCAST_VIEW);
	break;		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rel_exp_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rel_exp_update":
 	check_authority('059',"add");
	if(!$PHP_year)$PHP_year = date('Y');

	if(!$PHP_dept)
	{
		$msg = "Please select department first!!";
		$redirect="expense.php?PHP_action=expense&PHP_msg=".$msg;
		redirect_page($redirect);
	}
	if(!$PHP_month)
	{
		$msg = "Please select month first!!";
		$redirect="expense.php?PHP_action=expense&PHP_msg=".$msg;
		redirect_page($redirect);
	}	
	
	if($expense->check($PHP_year,$PHP_dept))
	{
		$msg = 'YEAR : ['.$PHP_year.'] DEPT :['.$PHP_dept.'] is not exist, please append these first.';
		$redirect="expense.php?PHP_action=expense&PHP_msg=".$msg;
		redirect_page($redirect);
	}
	
		$op = $expense->get($PHP_year,$PHP_month,$PHP_dept);
	
	$op['yy'] = $PHP_year;

	$op['mm_ary']=array('01','02','03','04','05','06','07','08','09','10','11','12');
	$op['txt'] = array('cost','salary','rent','sationery','travel','freight','postal','fix','ad','water','insurance',
										 'communicat','donate','taxes','bed_dabt','dpereciation','other_1','book','food',
										 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
										 'sample','bank','pack','labor_2','health','martrial','share','prize','retire_1',
										 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safty');

	page_display($op,'059', $TPL_EXPENSE_EDIT);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_rel_exp_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rel_exp_update":
 	check_authority('059',"add");

 		
  	$parm = array ('cost'				=>	$PHP_e_cost,'salary'				=>	$PHP_e_salary,				'rent'			=>	$PHP_e_rent,
 									 'sationery'		=>	$PHP_e_sationery,			'travel'		=>	$PHP_e_travel,
 									 'freight'			=>	$PHP_e_freight,				'postal'		=>	$PHP_e_postal,
 									 'fix'					=>	$PHP_e_fix,						'ad'				=>	$PHP_e_ad,
 									 'water'				=>	$PHP_e_water,					'insurance'	=>	$PHP_e_insurance,
 									 'communicat'		=>	$PHP_e_communicat,		'donate'		=>	$PHP_e_donate,
 									 'taxes'				=>	$PHP_e_taxes,					'bed_dabt'	=>	$PHP_e_bed_dabt,
 									 'depreciation'	=>	$PHP_e_dpereciation,	'other_1'		=>	$PHP_e_other_1,
 									 'book'					=>	$PHP_e_book,					'food'			=>	$PHP_e_food,
 									 'welfare'			=>	$PHP_e_welfare,				'rd'				=>	$PHP_e_rd,
 									 'teach'				=>	$PHP_e_teach,					'medicine'	=>	$PHP_e_medicine,
 									 'commission'		=>	$PHP_e_commission,		'labor_1'		=>	$PHP_e_labor_1,
 									 'other_2'			=>	$PHP_e_other_2,				'other_3'		=>	$PHP_e_other_3,
 									 'customs'			=>	$PHP_e_customs,				'sample'		=>	$PHP_e_sample,
 									 'bank'					=>	$PHP_e_bank,					'pack'			=>	$PHP_e_pack,
 									 'labor_2'			=>	$PHP_e_labor_2,				'health'		=>	$PHP_e_health,
 									 'material'			=>	$PHP_e_martrial,			'share'			=>	$PHP_e_share,
 									 'prize'				=>	$PHP_e_prize,					'retire_1'	=>	$PHP_e_retire_1,
 									 'reach_cost'		=>	$PHP_e_reach_cost,		'build'			=>	$PHP_e_build,
 									 'retire_2'			=>	$PHP_e_retire_2,			'cloth_fix'	=>	$PHP_e_cloth_fix,
 									 'work_cloth'		=>	$PHP_e_work_cloth,		'manage'		=>	$PHP_e_manage,
 									 'safety'				=>	$PHP_e_safty,				 	'id'				=>	$PHP_e_id,							
 									 );		
 		$f1 = $expense->edit($parm);
	
 		$msg = 'SUCCESS UPDATE EXPENSE ON YEAR :['.$PHP_year.'] DEPT:['.$PHP_dept.']';
 		if ($f1) $log->log_add(0,"84A",$msg);
 		
		$op = $expense->get($PHP_year,$PHP_month,$PHP_dept);
		
		$op['msg'][]= $msg;
		$op['mm_ary']=array('01','02','03','04','05','06','07','08','09','10','11','12');
		

	page_display($op,'059', $TPL_EXPENSE_VIEW);
	break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_expense_search": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_expense_search":
 	check_authority('059',"view");
	if(!$PHP_year)$PHP_year = date('Y');
	if(!$PHP_month && $PHP_year == date('Y')) $PHP_month = date('m');
	if(!$PHP_month && $PHP_year <> date('Y')) $PHP_month = '12';

		$txt = array('cost','salary','rent','stationery','travel','freight','postal','fix','ad','water','insurance',
								 'communicat','donate','taxes','bed_dabt','depreciation','other_1','book','food',
								 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
								 'sample','bank','pack','labor_2','health','material','share','prize','retire_1',
								 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safety', 'ttl');	
	
	
	if(!$PHP_dept)
	{
		$op['dp'][1] = $expense->search($PHP_year,'K0',$PHP_month);
		$op['dp'][2] = $expense->search($PHP_year,'RD',$PHP_month);
		$op['dp'][3] = $expense->search($PHP_year,'PM',$PHP_month);
		$op['dp'][4] = $expense->search($PHP_year,'J1',$PHP_month);
	
		for($i=0; $i<sizeof($op['dp'][1]['f']); $i++)
		{
			
			for($j=0; $j<sizeof($txt); $j++)
			{
				if(!isset($op['dp'][4]['e'][$i][$txt[$j]]))$op['dp'][4]['e'][$i][$txt[$j]] = 0;
				if(!isset($op['dp'][4]['f'][$i][$txt[$j]]))$op['dp'][4]['f'][$i][$txt[$j]] = 0;
				$op['dp'][0]['e'][$i][$txt[$j]] = $op['dp'][1]['e'][$i][$txt[$j]] + $op['dp'][2]['e'][$i][$txt[$j]]+ $op['dp'][3]['e'][$i][$txt[$j]]+ $op['dp'][4]['e'][$i][$txt[$j]];	
				$op['dp'][0]['f'][$i][$txt[$j]] = $op['dp'][1]['f'][$i][$txt[$j]] + $op['dp'][2]['f'][$i][$txt[$j]]+ $op['dp'][3]['f'][$i][$txt[$j]]+ $op['dp'][4]['e'][$i][$txt[$j]];
				
			}
			
		}
		if(isset($op['dp'][1]['sub_f']))
		{
			foreach($op['dp'][1]['sub_f'] as $key => $value)
			{
				if(!isset($op['dp'][4]['sub_f'][$key]))$op['dp'][4]['sub_f'][$key] = 0;
				$op['dp'][0]['sub_f'][$key] = $op['dp'][1]['sub_f'][$key] + $op['dp'][2]['sub_f'][$key]+ $op['dp'][3]['sub_f'][$key]+ $op['dp'][4]['sub_f'][$key];
			}
		}
		if(isset($op['dp'][1]['sub_e']))
		{
			foreach($op['dp'][1]['sub_e'] as $key => $value)
			{
					if(!isset($op['dp'][4]['sub_e'][$key]))$op['dp'][4]['sub_e'][$key] = 0;
				$op['dp'][0]['sub_e'][$key] = $op['dp'][1]['sub_e'][$key] + $op['dp'][2]['sub_e'][$key]+ $op['dp'][3]['sub_e'][$key]+ $op['dp'][4]['sub_e'][$key];
			}
		}
		if(isset($op['dp'][1]['diff']))
		{
			foreach($op['dp'][1]['diff'] as $key => $value)
			{
				if(!isset($op['dp'][4]['diff'][$key]))$op['dp'][4]['diff'][$key] = 0;
				$op['dp'][0]['diff'][$key] = $op['dp'][1]['diff'][$key] + $op['dp'][2]['diff'][$key]+ $op['dp'][3]['diff'][$key]+ $op['dp'][4]['diff'][$key];
			}
		}
		$op['dp_ary'] = array('ALL','K0','RD','PM','J1');
	}else{
		$op['dp'][0] = $expense->search($PHP_year,$PHP_dept,$PHP_month);
		$op['dp_ary'][0] = $PHP_dept;
	}
	
	$op['yy'] = $PHP_year;
	$op['mm_ary']=array('01','02','03','04','05','06','07','08','09','10','11','12');
	$op['dept'] = $PHP_dept;
	$op['mm'] = $PHP_month;
	page_display($op,'059', $TPL_EXPENSE_LIST);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "expense_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "expense_print":
 	check_authority('059',"view");
	if(!$PHP_year)$PHP_year = date('Y');
	if(!$PHP_month && $PHP_year == date('Y')) $PHP_month = date('m');
	if(!$PHP_month && $PHP_year <> date('Y')) $PHP_month = '12';
		$txt = array('cost','salary','rent','stationery','travel','freight','postal','fix','ad','water','insurance',
								 'communicat','donate','taxes','bed_dabt','depreciation','other_1','book','food',
								 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
								 'sample','bank','pack','labor_2','health','material','share','prize','retire_1',
								 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safety', 'ttl');	
	$mm_ary=array('01','02','03','04','05','06','07','08','09','10','11','12');

	if($PHP_month <= '04')$print_mm = array('01','02','03','04');
	if($PHP_month == '05')$print_mm = array('02','03','04','05');
	if($PHP_month == '06')$print_mm = array('03','04','05','06');
	if($PHP_month == '07')$print_mm = array('04','05','06','07');
	if($PHP_month == '08')$print_mm = array('05','06','07','08');
	if($PHP_month == '09')$print_mm = array('06','07','08','09');
	if($PHP_month == '10')$print_mm = array('07','08','09','10');
	if($PHP_month == '11')$print_mm = array('08','09','10','11');
	if($PHP_month == '12')$print_mm = array('09','10','11','12');
	if(!$PHP_dept)
	{
		$op['dp'][1] = $expense->search($PHP_year,'K0',$PHP_month);
		$op['dp'][2] = $expense->search($PHP_year,'RD',$PHP_month);
		$op['dp'][3] = $expense->search($PHP_year,'PM',$PHP_month);
	
		for($i=0; $i<sizeof($op['dp'][3]['f']); $i++)
		{
			
			for($j=0; $j<sizeof($txt); $j++)
			{
				
				$op['dp'][0]['e'][$i][$txt[$j]] = $op['dp'][1]['e'][$i][$txt[$j]] + $op['dp'][2]['e'][$i][$txt[$j]]+ $op['dp'][3]['e'][$i][$txt[$j]];	
				$op['dp'][0]['f'][$i][$txt[$j]] = $op['dp'][1]['f'][$i][$txt[$j]] + $op['dp'][2]['f'][$i][$txt[$j]]+ $op['dp'][3]['f'][$i][$txt[$j]];
				
			}
			
		}
		if(isset($op['dp'][1]['sub_f']))foreach($op['dp'][1]['sub_f'] as $key => $value)$op['dp'][0]['sub_f'][$key] = $op['dp'][1]['sub_f'][$key] + $op['dp'][2]['sub_f'][$key]+ $op['dp'][3]['sub_f'][$key];
		if(isset($op['dp'][1]['sub_e']))foreach($op['dp'][1]['sub_e'] as $key => $value)$op['dp'][0]['sub_e'][$key] = $op['dp'][1]['sub_e'][$key] + $op['dp'][2]['sub_e'][$key]+ $op['dp'][3]['sub_e'][$key];
		if(isset($op['dp'][1]['diff']))foreach($op['dp'][1]['diff'] as $key => $value)$op['dp'][0]['diff'][$key] = $op['dp'][1]['diff'][$key] + $op['dp'][2]['diff'][$key]+ $op['dp'][3]['diff'][$key];
		$op['dp_ary'] = array('ALL','SALES','PM','DA','RD','PROJECT');
		$PHP_dept ='ALL';
	}else{
		$op['dp'][0] = $expense->search($PHP_year,$PHP_dept,$PHP_month);
		if($PHP_dept == 'K0') $PHP_dept ='SALES';
		$op['dp_ary'][0] = $PHP_dept;
	}


//列印	
include_once($config['root_dir']."/lib/class.pdf_expense.php");

$print_title = "Forcast & Expense report";
$print_title2 = "FY : ".$PHP_year;
if($PHP_dept)$print_title2 .= "     DEPT : ".$PHP_dept;

$mark = 'Forcast & Expense report';


$pdf=new PDF_expense('P','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',14);

for($j=0; $j<sizeof($op['dp']); $j++)
{
	if ($j > 0)
	{
			$x = 10;
			$pdf->AddPage();
			$x = $pdf->exp_title();
	}
	$x = $pdf->exp_title();

	$pdf->SetXY($x,30);
	$pdf->SetFont('Arial','B',8);
	$pdf->cell(51,5,'uptoday('.$PHP_month.')  DEPT :'.$op['dp_ary'][$j],1,1,'C');
	$x = $pdf->ext_ttl_value($op['dp'][$j]['sub_f'],'forecast',$x);
	$x = $pdf->ext_ttl_value($op['dp'][$j]['sub_e'],'expense',$x);
	$x = $pdf->ext_ttl_value($op['dp'][$j]['diff'],'difference',$x);
	$pdf->SetLineWidth(0.5);
	$pdf->Line($x,30,$x,261);
	$pdf->SetLineWidth(0.1);
  $k = 0;
	for($i=0; $i<sizeof($mm_ary); $i++)
	{
		if(isset($print_mm[$k]) && $mm_ary[$i] == $print_mm[$k])
		{
			$pdf->SetXY($x,30);
			$pdf->cell(30,5,$mm_ary[$i],0,1,'C');
			$x = $pdf->ext_value($op['dp'][$j]['f'][$i],'forecast',$x);
			$x = $pdf->ext_value($op['dp'][$j]['e'][$i],'expense',$x);	
			$pdf->SetLineWidth(0.5);
			$pdf->Line($x,30,$x,261);
			$pdf->SetLineWidth(0.1);
			$k++;
		}
		
/*		
		if($i == 3 || $i == 8)
		{
			$x = 10;
			$pdf->AddPage();
			$x = $pdf->exp_title();
		}
*/	
	}
}
$f_name = "[".$PHP_year.']';
if($PHP_dept)$f_name .= "[".$PHP_dept.']';
$name=$f_name.'expense.pdf';
$pdf->Output($name,'D');	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sales_analysis":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "sales_analysis":

 	check_authority('059',"view");
	if(!$PHP_year)$PHP_year = date('Y');
	if(!$PHP_month && $PHP_year == date('Y')) $PHP_month = date('m');
	if(!$PHP_month && $PHP_year <> date('Y')) $PHP_month = '12';
	
	$mm_ary = array('01','02','03','04','05','06','07','08','09','10','11','12');
	$mm = $PHP_month;
	$op = $expense->sales_analysis($PHP_year,$PHP_month);
		
//今年
	$yy_str = $PHP_year.'-01-01';
	$yy_end = $PHP_year.'-12-31';
	$ord = $expense->get_ord_qty('LY', $yy_str,$yy_end, '');
	$ord_p = $order->get_one_etd_ord_prc_full('LY', $yy_str,$yy_end, '');
	$op['now_ord_p_ttl'] = $op['now_ord_ttl'] = $op['now_ord_p'][0] = $op['now_ord'][0] = 0;
	for($i=1; $i<=sizeof($mm_ary); $i++)
	{
			$j = $i-1;
			$yymm = $PHP_year."-".$mm_ary[$j];
			$op['now_ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
			if($mm >= $mm_ary[$j]) $op['now_ord'][0] += $op['now_ord'][$i];
			$op['now_ord_ttl'] += $op['now_ord'][$i];
			$op['now_ord_p'][$i]	= (!isset($ord_p[$yymm])) ? 0 : $ord_p[$yymm];		
			
			if($mm >= $mm_ary[$j]) $op['now_ord_p'][0] +=$op['now_ord_p'][$i];
			$op['now_ord_p_ttl'] +=$op['now_ord_p'][$i];
	}
	
//前一年	
	$yy_str = ($PHP_year-1).'-01-01';
	$yy_end = ($PHP_year-1).'-12-31';
	$ord = $expense->get_ord_qty('LY', $yy_str,$yy_end, '');
	$ord_p = $order->get_one_etd_ord_prc_full('LY', $yy_str,$yy_end, '');
	$op['bfr_ord_p_ttl'] = $op['bfr_ord_ttl'] = $op['bfr_ord_p'][0] = $op['bfr_ord'][0] = 0;
	for($i=1; $i<=sizeof($mm_ary); $i++)
	{
			$j = $i-1;
			$yymm =  ($PHP_year-1)."-".$mm_ary[$j];
			$op['bfr_ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
			if($mm >= $mm_ary[$j]) $op['bfr_ord'][0] += $op['bfr_ord'][$i];
			$op['bfr_ord_ttl'] += $op['bfr_ord'][$i];
			$op['bfr_ord_p'][$i]	= (!isset($ord_p[$yymm])) ? 0 : $ord_p[$yymm];		
			if($mm >= $mm_ary[$j]) $op['bfr_ord_p'][0] +=$op['bfr_ord_p'][$i];
			$op['bfr_ord_p_ttl'] +=$op['bfr_ord_p'][$i];
	}	
	
	
	
//接單預算
		$where_str = " WHERE method='forecast' AND fty='LY' AND year='".$PHP_year."' ";
		$fcst = $fcst2->search(0,$where_str);	
		for ($j=0; $j<13; $j++){
			$op['fcst_q'][$j] = 0;
			$op['fcst_f'][$j] = 0;
		}	
		$op['fcst_q_ttl'] =$op['fcst_f_ttl'] = 0;
		for ($i=0; $i<$fcst['max_no']; $i++){
			$fcst['fcst'][$i]['q'] = csv2array($fcst['fcst'][$i]['qty']);
			$fcst['fcst'][$i]['f'] = csv2array($fcst['fcst'][$i]['fcst']);			
			$fcst['fcst'][$i]['c'] = csv2array($fcst['fcst'][$i]['cm']);
			for ($j=1; $j<=12; $j++){
				$k = $j-1;
				$op['fcst_q'][$j] += $fcst['fcst'][$i]['q'][$k];
				if($j< 12)  $comm = $fcst['fcst'][$i]['f'][$k] * ($fcst['fcst'][$i]['c'][$k]/100);
				if($j == 12)$comm = $fcst['fcst'][$i]['c'][$k];
				$op['fcst_f'][$j] += $fcst['fcst'][$i]['f'][$k] +$comm ;
				if($mm >= $mm_ary[$k]) $op['fcst_q'][0] +=$fcst['fcst'][$i]['q'][$k];
				if($mm >= $mm_ary[$k]) $op['fcst_f'][0] +=$fcst['fcst'][$i]['f'][$k]+$comm ;				
			}		
			$op['fcst_q_ttl'] += $fcst['fcst'][$i]['q'][12];
			$op['fcst_f_ttl'] += $fcst['fcst'][$i]['f'][12];
		}
//計算差異		
		for($i=0; $i<sizeof($op['fcst_f']); $i++)
		{
			$op['diff_q'][$i] = $op['now_ord'][$i] - $op['fcst_q'][$i];
			$op['diff_f'][$i] = $op['now_ord_p'][$i] - $op['fcst_f'][$i];
			$op['diff_e'][$i] = $op['now']['E'][$i] - $op['now']['F'][$i];
		}
		$op['diff_q_ttl'] = $op['now_ord_ttl'] - $op['fcst_q_ttl'];
		$op['diff_f_ttl'] = $op['now_ord_p_ttl'] - $op['fcst_f_ttl'];
		$op['diff_e_ttl'] = $op['now_ttl']['E'] - $op['now_ttl']['F'];
		
	$mm_eng = array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
	$op['now_yy'] = $PHP_year;
	$op['bfr_yy'] = ($PHP_year-1);
	$op['mm'] = $mm_eng[($PHP_month-1)];
	$op['mm_mk'] = $PHP_month;
	$op['mm_ary']=array('uptoday','01','02','03','04','05','06','07','08','09','10','11','12','total');
	page_display($op,'059', $TPL_EXPENSE_ANALISIS);
	break;
	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "sales_analysis_print": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "sales_analysis_print":
 
		check_authority('059',"view");				
	if(!$PHP_year)$PHP_year = date('Y');
	$mm_ary = array('01','02','03','04','05','06','07','08','09','10','11','12');
	$mm_eng = array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
	$mm = $PHP_month;
	$op = $expense->sales_analysis($PHP_year,$PHP_month);
		
//今年
	$yy_str = $PHP_year.'-01-01';
	$yy_end = $PHP_year.'-12-31';
	$ord = $expense->get_ord_qty('LY', $yy_str,$yy_end, '');
	$ord_p = $order->get_one_etd_ord_prc_full('LY', $yy_str,$yy_end, '');
	$op['now_ord_p_ttl'] = $op['now_ord_ttl'] = $op['now_ord_p'][0] = $op['now_ord'][0] = 0;
	for($i=1; $i<=sizeof($mm_ary); $i++)
	{
			$j = $i-1;
			$yymm = $PHP_year."-".$mm_ary[$j];
			$op['now_ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
			if($mm >= $mm_ary[$j]) $op['now_ord'][0] += $op['now_ord'][$i];
			$op['now_ord_ttl'] += $op['now_ord'][$i];
			$op['now_ord_p'][$i]	= (!isset($ord_p[$yymm])) ? 0 : $ord_p[$yymm];		
			
			if($mm >= $mm_ary[$j]) $op['now_ord_p'][0] +=$op['now_ord_p'][$i];
			$op['now_ord_p_ttl'] +=$op['now_ord_p'][$i];
	}
	
//前一年	
	$yy_str = ($PHP_year-1).'-01-01';
	$yy_end = ($PHP_year-1).'-12-31';
	$ord = $expense->get_ord_qty('LY', $yy_str,$yy_end, '');
	$ord_p = $order->get_one_etd_ord_prc_full('LY', $yy_str,$yy_end, '');
	$op['bfr_ord_p_ttl'] = $op['bfr_ord_ttl'] = $op['bfr_ord_p'][0] = $op['bfr_ord'][0] = 0;
	for($i=1; $i<=sizeof($mm_ary); $i++)
	{
			$j = $i-1;
			$yymm =  ($PHP_year-1)."-".$mm_ary[$j];
			$op['bfr_ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
			if($mm >= $mm_ary[$j]) $op['bfr_ord'][0] += $op['bfr_ord'][$i];
			$op['bfr_ord_ttl'] += $op['bfr_ord'][$i];
			$op['bfr_ord_p'][$i]	= (!isset($ord_p[$yymm])) ? 0 : $ord_p[$yymm];		
			if($mm >= $mm_ary[$j]) $op['bfr_ord_p'][0] +=$op['bfr_ord_p'][$i];
			$op['bfr_ord_p_ttl'] +=$op['bfr_ord_p'][$i];
	}	

//接單預算
		$where_str = " WHERE method='forecast' AND fty='LY' AND year='".$PHP_year."' ";
		$fcst = $fcst2->search(0,$where_str);	
		for ($j=0; $j<13; $j++){
			$op['fcst_q'][$j] = 0;
			$op['fcst_f'][$j] = 0;
		}	
		$op['fcst_q_ttl'] =$op['fcst_f_ttl'] = 0;
		for ($i=0; $i<$fcst['max_no']; $i++){
			$fcst['fcst'][$i]['q'] = csv2array($fcst['fcst'][$i]['qty']);
			$fcst['fcst'][$i]['f'] = csv2array($fcst['fcst'][$i]['fcst']);			
			$fcst['fcst'][$i]['c'] = csv2array($fcst['fcst'][$i]['cm']);
			for ($j=1; $j<=12; $j++){
				$k = $j-1;
				$op['fcst_q'][$j] += $fcst['fcst'][$i]['q'][$k];
				if($j< 12)  $comm = $fcst['fcst'][$i]['f'][$k] * ($fcst['fcst'][$i]['c'][$k]/100);
				if($j == 12)$comm = $fcst['fcst'][$i]['c'][$k];
				$op['fcst_f'][$j] += $fcst['fcst'][$i]['f'][$k] +$comm ;
				if($mm >= $mm_ary[$k]) $op['fcst_q'][0] +=$fcst['fcst'][$i]['q'][$k];
				if($mm >= $mm_ary[$k]) $op['fcst_f'][0] +=$fcst['fcst'][$i]['f'][$k]+$comm ;				
			}		
			$op['fcst_q_ttl'] += $fcst['fcst'][$i]['q'][12];
			$op['fcst_f_ttl'] += $fcst['fcst'][$i]['f'][12];
		}

//計算差異		
		for($i=0; $i<sizeof($op['fcst_f']); $i++)
		{
			$op['diff_q'][$i] = $op['now_ord'][$i] - $op['fcst_q'][$i];
			$op['diff_f'][$i] = $op['now_ord_p'][$i] - $op['fcst_f'][$i];
			$op['diff_e'][$i] = $op['now']['E'][$i] - $op['now']['F'][$i];
		}
		$op['diff_q_ttl'] = $op['now_ord_ttl'] - $op['fcst_q_ttl'];
		$op['diff_f_ttl'] = $op['now_ord_p_ttl'] - $op['fcst_f_ttl'];
		$op['diff_e_ttl'] = $op['now_ttl']['E'] - $op['now_ttl']['F'];

	$this_mm = $mm_eng[($PHP_month-1)];
	$bfr_yy = ($PHP_year-1);
	
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_sales_analysis.php");

$mark = $print_title = $PHP_year." JAN~".$this_mm." sales analysis";
$pdf=new PDF_sales_analysis('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',14);


$pdf->sales_title($this_mm);
$pdf->sales_det($bfr_yy.' 訂單(SU)',$op['bfr_ord'],$op['bfr_ord_ttl']);
$pdf->sales_det($PHP_year.' 訂單(SU)',$op['now_ord'],$op['now_ord_ttl']);
$pdf->sales_det($PHP_year.' 預算',$op['fcst_q'],$op['fcst_q_ttl']);
$pdf->sales_det($PHP_year.' 差異',$op['diff_q'],$op['diff_q_ttl']);
$pdf->ln();
$pdf->sales_det($bfr_yy.' FOB產值(US$)',$op['bfr_ord_p'],$op['bfr_ord_p_ttl']);
$pdf->sales_det($PHP_year.' FOB產值(US$)',$op['now_ord_p'],$op['now_ord_p_ttl']);
$pdf->sales_det($PHP_year.' 預算',$op['fcst_f'],$op['fcst_f_ttl']);
$pdf->sales_det($PHP_year.' 差異',$op['diff_f'],$op['diff_f_ttl']);
$pdf->ln();
$pdf->ln();
$pdf->sales_det($bfr_yy.' 費用(NT$)',$op['bfr']['E'],$op['bfr_ttl']['E']);
$pdf->sales_det($PHP_year.' 費用(NT$)',$op['now']['E'],$op['now_ttl']['E']);
$pdf->sales_det($PHP_year.' 費用預算(NT$)',$op['now']['F'],$op['now_ttl']['F']);
$pdf->sales_det($PHP_year.' 費用差異',$op['diff_e'],$op['diff_e_ttl']);
$name=$print_title.'.pdf';
$pdf->Output($name,'D');




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sales_compare":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "sales_compare":
 	check_authority('059',"view");
	if(!$PHP_month_str)$PHP_month_str = '01';
	if(!$PHP_month_end)$PHP_month_str = '12';
	$mm_ttl = '';
	for($i=$PHP_month_str; $i<=$PHP_month_end; $i++)
	{			
		if($i < 10 && $i != $PHP_month_str)$tmp_mm = '0'.$i;
		if($i >= 10 || $i == $PHP_month_str)$tmp_mm = $i;
		$mm_ary[] = $tmp_mm;
	}	

		
//今年
	$yy_str = date('Y').'-'.$PHP_month_str.'-01';
	$yy_end = date('Y').'-'.$PHP_month_end.'-31';
	$ord = $expense->get_ord_qty('LY', $yy_str,$yy_end, '');
	$ord_p = $order->get_one_etd_ord_prc_full('LY', $yy_str,$yy_end, '');
	$op['now_ord_ttl'] = $op['now_ord_p_ttl'] = 0;
	for($i=0; $i<sizeof($mm_ary); $i++)
	{
			$yymm = date('Y')."-".$mm_ary[$i];
			$op['now_ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
			$op['now_ord_ttl'] += $op['now_ord'][$i];

			$op['now_ord_p'][$i]	= (!isset($ord_p[$yymm])) ? 0 : $ord_p[$yymm];				
			$op['now_ord_p_ttl'] +=$op['now_ord_p'][$i];
	}
	
	
//接單預算
		$where_str = " WHERE method='forecast' AND fty='LY' AND year='".date('Y')."' ";
		$fcst = $fcst2->search(0,$where_str);	

		$op['fcst_q_ttl'] = $op['fcst_f_ttl'] = 0;
 
		for ($i=0; $i<$fcst['max_no']; $i++){
			$fcst['fcst'][$i]['q'] = csv2array($fcst['fcst'][$i]['qty']);
			$fcst['fcst'][$i]['f'] = csv2array($fcst['fcst'][$i]['fcst']);			
			$fcst['fcst'][$i]['c'] = csv2array($fcst['fcst'][$i]['cm']);
			$k = 0;
			for ($j=($PHP_month_str-1); $j<$PHP_month_end; $j++){				
				if(!isset($op['fcst_f'][$k]))$op['fcst_f'][$k] = 0;
				if(!isset($op['fcst_q'][$k]))$op['fcst_q'][$k] = 0;
				
				$op['fcst_q'][$k] += $fcst['fcst'][$i]['q'][$j];
				$op['fcst_q_ttl'] += $fcst['fcst'][$i]['q'][$j];
				
				$comm = $fcst['fcst'][$i]['f'][$j] * ($fcst['fcst'][$i]['c'][$j]/100);  				
				$op['fcst_f'][$k] += $fcst['fcst'][$i]['f'][$j] +$comm ;
				$op['fcst_f_ttl'] += $fcst['fcst'][$i]['f'][$j] +$comm ;
				$k++;
			}		
		}
		
//差異
	for($i=0; $i<sizeof($mm_ary); $i++)
	{
		if(!isset($op['fcst_f'][$i]))$op['fcst_f'][$i] = 0;
		if(!isset($op['fcst_q'][$i]))$op['fcst_q'][$i] = 0;
		$op['diff_q'][$i] =  $op['now_ord'][$i] - $op['fcst_q'][$i];
		$op['diff_f'][$i] =  $op['now_ord_p'][$i] - $op['fcst_f'][$i];
	}
	$op['diff_q_ttl'] = $op['now_ord_ttl'] - $op['fcst_q_ttl'];
	$op['diff_f_ttl'] = $op['now_ord_p_ttl'] - $op['fcst_f_ttl'];
	
		
	$op['now_yy'] = date('Y');
	$op['mm_ary']=$mm_ary;
	$op['colspan'] = sizeof($mm_ary);
	$op['width'] = sizeof($mm_ary) * 80 +200;
	$op['mm_ttl'] = $PHP_month_str.'-'.$PHP_month_end;
	page_display($op,'059', $TPL_EXPENSE_COMPARE);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sales_compare_det":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "sales_compare_det":

 	check_authority('059',"view");
 	$mm = explode('-',$PHP_month);
 	if(!isset($PHP_year)) $PHP_year=date('Y');
 	$op['diff_qty_ttl'] = 0;
 	if(!isset($mm[1]))
 	{
		$op['show'][0] = $expense->get_compare_det('LY', $PHP_month, $PHP_year);
		$op['mm_ary'][0] = $PHP_month;
	}else{
		$j = 0;
		for($i=$mm[0]; $i<=$mm[1]; $i++)
		{
			if($i < 10 && $i <> $mm[0]){$mm_mk = '0'.$i;}else{$mm_mk = $i;}	
			$op['mm_ary'][] = $mm_mk;
			
			$show[$j] = $expense->get_compare_det('LY', $mm_mk, $PHP_year);	
			$j++;
		}
		$op['show'][0] = $show[0];
		for($j = 1; $j<sizeof($show); $j++)
		{
			for($k=0; $k<sizeof($op['show'][0]['det']); $k++) 
			{
				$op['show'][$j]['det'][$k]['cust'] = $op['show'][0]['det'][$k]['cust'];
				$op['show'][$j]['det'][$k]['cust_iname'] = $op['show'][0]['det'][$k]['cust_iname'];
				$op['show'][$j]['det'][$k]['ord_qty'] = $op['show'][$j]['det'][$k]['fcst_qty'] = 0;
			}			
			for($i=0; $i<sizeof($show[$j]['det']); $i++)
			{
				$det_mk = 0;
				for($k=0; $k<sizeof($op['show'][0]['det']); $k++)
				{
				
					if($op['show'][0]['det'][$k]['cust'] == $show[$j]['det'][$i]['cust'])
					{
						$op['show'][$j]['det'][$k] = $show[$j]['det'][$i];
						$det_mk = 1;
						break;						
					}
				}
				if($det_mk == 0)
				{
					
					$op['show'][0]['det'][$k]['cust'] = $show[$j]['det'][$i]['cust'];
					$op['show'][0]['det'][$k]['cust_iname'] = $show[$j]['det'][$i]['cust_iname'];
					$op['show'][0]['det'][$k]['ord_qty'] = $op['show'][0]['det'][$k]['fcst_qty'] = 0;
					$op['show'][$j]['det'][$k] = $show[$j]['det'][$i];
				}				
			}
			$op['show'][$j]['ord_qty_ttl'] = $show[$j]['ord_qty_ttl'];
			$op['show'][$j]['fcst_qty_ttl'] = $show[$j]['fcst_qty_ttl'];
		}
		
	}
	
//差異	
	for($j = 0; $j<sizeof($op['show']); $j++)
	{
		for($i=0; $i<sizeof($op['show'][0]['det']); $i++)
		{			
			if(!isset($op['show'][$j]['det'][$i]['fcst_qty']))$op['show'][$j]['det'][$i]['fcst_qty'] = 0;
			if(!isset($op['show'][$j]['det'][$i]['ord_qty']))$op['show'][$j]['det'][$i]['ord_qty'] = 0;
			if(!isset($op['diff_qty'][$i])) $op['diff_qty'][$i] = 0;
			$op['diff_qty'][$i] +=  $op['show'][$j]['det'][$i]['ord_qty'] - $op['show'][$j]['det'][$i]['fcst_qty'];
			$op['diff_qty_ttl'] += ($op['show'][$j]['det'][$i]['ord_qty'] - $op['show'][$j]['det'][$i]['fcst_qty']);
			
		}		
	}	

	$op['yy'] = date('Y');
	$op['mm']=$PHP_month;
	page_display($op,'059', $TPL_EXPENSE_COMPARE_DET);
	break;
	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "sales_compare_det_fob":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "sales_compare_det_fob":

 	check_authority('059',"view");
 	$mm = explode('-',$PHP_month);
 	if(!isset($PHP_year)) $PHP_year=date('Y');
 	$op['diff_fob_ttl'] = 0;
 	if(!isset($mm[1]))
 	{
		$op['show'][0] = $expense->get_compare_det('LY', $PHP_month, $PHP_year);
		$op['mm_ary'][0] = $PHP_month;
	}else{
		$j = 0;
		for($i=$mm[0]; $i<=$mm[1]; $i++)
		{
			if($i < 10 && $i <> $mm[0]){$mm_mk = '0'.$i;}else{$mm_mk = $i;}	
			$op['mm_ary'][] = $mm_mk;
			
			$show[$j] = $expense->get_compare_det('LY', $mm_mk, $PHP_year);	
			$j++;
		}
		$op['show'][0] = $show[0];
		for($j = 1; $j<sizeof($show); $j++)
		{
			for($k=0; $k<sizeof($op['show'][0]['det']); $k++) 
			{
				$op['show'][$j]['det'][$k]['cust'] = $op['show'][0]['det'][$i]['cust'];
				$op['show'][$j]['det'][$k]['cust_iname'] = $op['show'][0]['det'][$i]['cust_iname'];
				$op['show'][$j]['det'][$k]['ord_fob'] = $op['show'][$j]['det'][$k]['fcst_fob'] = 0;
			}			
			for($i=0; $i<sizeof($show[$j]['det']); $i++)
			{
				$det_mk = 0;
				for($k=0; $k<sizeof($op['show'][0]['det']); $k++)
				{
				
					if($op['show'][0]['det'][$k]['cust'] == $show[$j]['det'][$i]['cust'])
					{
						$op['show'][$j]['det'][$k] = $show[$j]['det'][$i];
						$det_mk = 1;
						break;						
					}
				}
				if($det_mk == 0)
				{
					
					$op['show'][0]['det'][$k]['cust'] = $show[$j]['det'][$i]['cust'];
					$op['show'][0]['det'][$k]['cust_iname'] = $show[$j]['det'][$i]['cust_iname'];
					$op['show'][0]['det'][$k]['ord_fob'] = $op['show'][0]['det'][$k]['fcst_fob'] = 0;
					$op['show'][$j]['det'][$k] = $show[$j]['det'][$i];
				}				
			}
			$op['show'][$j]['ord_fob_ttl'] = $show[$j]['ord_fob_ttl'];
			$op['show'][$j]['fcst_fob_ttl'] = $show[$j]['fcst_fob_ttl'];
		}		
	}

		for($j = 0; $j<sizeof($op['show']); $j++)
		{
			for($i=0; $i<sizeof($op['show'][$j]['det']); $i++)
			{			
				if(!isset($op['show'][$j]['det'][$i]['fcst_fob']))$op['show'][$j]['det'][$i]['fcst_fob'] = 0;
				if(!isset($op['show'][$j]['det'][$i]['ord_fob']))$op['show'][$j]['det'][$i]['ord_fob'] = 0;
				if(!isset($op['diff_fob'][$i])) $op['diff_fob'][$i] = 0;
				$op['diff_fob'][$i] +=  $op['show'][$j]['det'][$i]['ord_fob'] - $op['show'][$j]['det'][$i]['fcst_fob'];
				$op['diff_fob_ttl'] += ($op['show'][$j]['det'][$i]['ord_fob'] - $op['show'][$j]['det'][$i]['fcst_fob']);
			}		
		}	

	$op['yy'] = date('Y');
	$op['mm']=$PHP_month;
	page_display($op,'059', $TPL_EXPENSE_COMPARE_DET_FOB);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_expense_search": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "expense_view_mm":
 	check_authority('059',"view");
	if(!$PHP_year)$PHP_year = date('Y');
	if(!$PHP_month && $PHP_year == date('Y')) $PHP_month = date('m');
	if(!$PHP_month && $PHP_year <> date('Y')) $PHP_month = '12';

		$txt = array('cost','salary','rent','stationery','travel','freight','postal','fix','ad','water','insurance',
								 'communicat','donate','taxes','bed_dabt','depreciation','other_1','book','food',
								 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
								 'sample','bank','pack','labor_2','health','material','share','prize','retire_1',
								 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safety', 'ttl');	
	
	
		$dp[1] = $expense->get($PHP_year,$PHP_month,'K0');
		$dp[2] = $expense->get($PHP_year,$PHP_month,'RD');
		$dp[3] = $expense->get($PHP_year,$PHP_month,'PM');
		
		$op['dp'][1]['e'][0] = $dp[1]['e'];
		$op['dp'][1]['f'][0] = $dp[1]['f'];	
		$op['dp'][1]['d'][0] = $dp[1]['d'];
		if(isset($dp[1]['sub_f']))$op['dp'][1]['sub_f'] = $dp[1]['sub_f'];
		if(isset($dp[1]['sub_e']))$op['dp'][1]['sub_e'] = $dp[1]['sub_e'];
		if(isset($dp[1]['diff']))$op['dp'][1]['diff'] = $dp[1]['diff'];

		$op['dp'][2]['e'][0] = $dp[2]['e'];
		$op['dp'][2]['f'][0] = $dp[2]['f'];
		$op['dp'][2]['d'][0] = $dp[2]['d'];	
		if(isset($dp[2]['sub_f']))$op['dp'][2]['sub_f'] = $dp[2]['sub_f'];
		if(isset($dp[2]['sub_e']))$op['dp'][2]['sub_e'] = $dp[2]['sub_e'];
		if(isset($dp[2]['diff']))$op['dp'][2]['diff'] = $dp[2]['diff'];

		$op['dp'][3]['e'][0] = $dp[3]['e'];
		$op['dp'][3]['f'][0] = $dp[3]['f'];	
		$op['dp'][3]['d'][0] = $dp[3]['d'];
		if(isset($dp[3]['sub_f']))$op['dp'][3]['sub_f'] = $dp[3]['sub_f'];
		if(isset($dp[3]['sub_e']))$op['dp'][3]['sub_e'] = $dp[3]['sub_e'];
		if(isset($dp[3]['diff']))$op['dp'][3]['diff'] = $dp[3]['diff'];
		
		for($i=0; $i<sizeof($op['dp'][3]['f']); $i++)
		{
			
			for($j=0; $j<sizeof($txt); $j++)
			{
				
				$op['dp'][0]['e'][$i][$txt[$j]] = $op['dp'][1]['e'][$i][$txt[$j]] + $op['dp'][2]['e'][$i][$txt[$j]]+ $op['dp'][3]['e'][$i][$txt[$j]];	
				$op['dp'][0]['f'][$i][$txt[$j]] = $op['dp'][1]['f'][$i][$txt[$j]] + $op['dp'][2]['f'][$i][$txt[$j]]+ $op['dp'][3]['f'][$i][$txt[$j]];
				$op['dp'][0]['d'][$i][$txt[$j]] = $op['dp'][1]['d'][$i][$txt[$j]] + $op['dp'][2]['d'][$i][$txt[$j]]+ $op['dp'][3]['d'][$i][$txt[$j]];
			}
			
		}
		if(isset($op['dp'][1]['sub_f']))foreach($op['dp'][1]['sub_f'] as $key => $value)$op['dp'][0]['sub_f'][$key] = $op['dp'][1]['sub_f'][$key] + $op['dp'][2]['sub_f'][$key]+ $op['dp'][3]['sub_f'][$key];
		if(isset($op['dp'][1]['sub_e']))foreach($op['dp'][1]['sub_e'] as $key => $value)$op['dp'][0]['sub_e'][$key] = $op['dp'][1]['sub_e'][$key] + $op['dp'][2]['sub_e'][$key]+ $op['dp'][3]['sub_e'][$key];
		if(isset($op['dp'][1]['diff']))foreach($op['dp'][1]['diff'] as $key => $value)$op['dp'][0]['diff'][$key] = $op['dp'][1]['diff'][$key] + $op['dp'][2]['diff'][$key]+ $op['dp'][3]['diff'][$key];
		$op['dp_ary'] = array('ALL','K0','RD','PM');
	
	$op['yy'] = $PHP_year;
	$op['mm_ary'][0]=$PHP_month;
//	$op['dept'] = $PHP_dept;
	$op['mm'] = $PHP_month;
	$TPL_EXPENSE_LIST_DET = "expense_list_det.html";
	page_display($op,'059', $TPL_EXPENSE_LIST_DET);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "season_analysis":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "season_analysis":
 	check_authority('059',"add");
		if(!$PHP_year) $PHP_year = date('Y');
 		$top = array('BS','BZ','JK','DR','VS');
 		$down = array('PT','SH','SK','SO');
 		$style = array('BS','BZ','JK','DR','VS','TSUM','PT','SH','SK','SO','BSUM','SUM');
 		$cust_det = array();
 		
 		$rpt = $report->get_season_fob($PHP_year);
//取得客戶陣列 

 		for($i = 0; $i<sizeof($rpt); $i++)
 		{
 			$mk = 0;
 			for($j=0; $j<sizeof($cust_det); $j++)
 			{
 				if($cust_det[$j] == $rpt[$i]['cust'])$mk = 1;
 			}
 			
 			if($mk == 0)
 			{
 				$cust_det[] = $rpt[$i]['cust'];
 				for( $j=0; $j<sizeof($style); $j++) $TTL_T[$rpt[$i]['cust']][$style[$j]] = 0; 
 				for( $j=0; $j<sizeof($style); $j++) $SS[$rpt[$i]['cust']][$style[$j]] = 0;		
 				for( $j=0; $j<sizeof($style); $j++) $FW[$rpt[$i]['cust']][$style[$j]] = 0;		
 				for( $j=0; $j<sizeof($style); $j++) $BTS[$rpt[$i]['cust']][$style[$j]] = 0;	
 				for( $j=0; $j<sizeof($style); $j++) $HD[$rpt[$i]['cust']][$style[$j]] = 0;	
 			}
 		}
//加總的整理 		
 		for($i = 0; $i<sizeof($rpt); $i++)
 			$TTL_T[$rpt[$i]['cust']][$rpt[$i]['style']] += $rpt[$i]['fob'];

	 	$op['ttl_t'] = $report->group_fob_record($TTL_T,$top,$down,$style);
	

//SEASON : S/S 加總整理	
		$op['ss_sum'] = 0;
		$rpt = $report->get_season_fob($PHP_year,'S/S');
 		for($i = 0; $i<sizeof($rpt); $i++)
 	  {
 			$SS[$rpt[$i]['cust']][$rpt[$i]['style']] += $rpt[$i]['fob'];
 			$op['ss_sum'] += $rpt[$i]['fob'];
 		}
	 	$op['ss'] = $report->group_fob_record($SS,$top,$down,$style);


//SEASON : F/W 加總整理	
		$op['fw_sum'] = 0;
		$rpt = $report->get_season_fob($PHP_year,'F/W');
 		for($i = 0; $i<sizeof($rpt); $i++)
 	  {
 			$FW[$rpt[$i]['cust']][$rpt[$i]['style']] += $rpt[$i]['fob'];
 			$op['fw_sum'] += $rpt[$i]['fob'];
 		}
	 	$op['fw'] = $report->group_fob_record($FW,$top,$down,$style); 		


//SEASON : BTS 加總整理	
		$op['bts_sum'] = 0;
		$rpt = $report->get_season_fob($PHP_year,'BTS');
 		for($i = 0; $i<sizeof($rpt); $i++)
 	  {
 			$BTS[$rpt[$i]['cust']][$rpt[$i]['style']] += $rpt[$i]['fob'];
 			$op['bts_sum'] += $rpt[$i]['fob'];
 		}
	 	$op['bts'] = $report->group_fob_record($BTS,$top,$down,$style); 	
 	
 	
//SEASON : H-day. 加總整理	
		$op['hd_sum'] = 0;
		$rpt = $report->get_season_fob($PHP_year,'H-day.');
 		for($i = 0; $i<sizeof($rpt); $i++)
 	  {
 			$HD[$rpt[$i]['cust']][$rpt[$i]['style']] += $rpt[$i]['fob'];
 			$op['hd_sum'] += $rpt[$i]['fob'];
 		}
	 	$op['hd'] = $report->group_fob_record($HD,$top,$down,$style);  		

 		$op['cust'] = $cust_det;
		$op['year'] = $PHP_year;
 		$op['style'] = $style;
 		$op['size'] = sizeof($style)+1;
 

	

	page_display($op,'059', $TPL_SEASON_ANALYSIS);
	break;		
	
	
//-------------------------------------------------------------------------

}   // end case ---------

?>
