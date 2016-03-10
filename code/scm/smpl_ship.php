<?php
session_start();
session_register	('sch_parm');

##################  2004/11/10  ########################
#			monitor.php  ¼҄¸L{°�#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";
$op = array();

$P_LINE = array (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);


switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_ship":
 	check_authority(2,10,"view");

		$sales_dept_ary = get_sales_dept(); // ﮏA򯠧±�嶱뾇 [®�41;ԝK0] ------
		$op['dept_ary'] = $arry2->select($sales_dept_ary,"","PHP_dept","select","get_dept()");  
		$op['factory'] = $arry2->select($SAMPLER,'','SCH_fty','select','');
		$op['fty'] = $arry2->select($SAMPLER,'','PHP_fty','select',"get_dept()");

	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_ship_add":
 	check_authority(2,10,"add");
		$op['shp']['ship_num'] = "F".$PHP_fty.date('y').'-xxxx';
		$op['shp']['fty'] = $PHP_fty;
		$op['shp']['dept'] = $op['shp']['ship_to'] = $PHP_dept;
		$smpl_rec = $smpl_ship->get_smpl($PHP_dept, $PHP_fty);
		for($i=0; $i<sizeof($smpl_rec); $i++)
		{
			$smpl_value[] = $smpl_rec[$i]['num'];
			$smpl_key[] = $smpl_rec[$i]['num']." [ ". $smpl_rec[$i]['qty_done'] ." ]";
		}
		$op['shp']['sample'] = $arry2->select($smpl_key,'','PHP_smpl_num','select','get_smpl(this)',$smpl_value);
		
		
		
	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_ADD);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_add_det":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "ship_add_det":
 	check_authority(2,10,"add");

	  if(!$PHP_id)
	  {
			$head = "F".$PHP_fty.date('y').'-';
			$ship_num = $apply->get_no($head,'ship_num','fty_smpl_ship');	//ﮏA箃쀾ȥXªk㶿

 			$parm = array ('ship_num'		=>	$ship_num,
 										 'ship_to'		=>	$PHP_ship_to,
 										 'fty'				=>	$PHP_fty,
 										 'dept'				=>	$PHP_dept,
 										 'open_date'	=>	date('Y-m-d'),
 										 'open_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
 										 );
 			$PHP_id = $smpl_ship->add($parm);
			$op['msg']= $smpl_ship->msg->get(2);
			$log->log_add(0,"210A",$op['msg'][0]);
 		}else{
 			$parm = array ('field_name'		=>	'ship_to',
 										 'field_value'	=>	$PHP_ship_to,
 										 'id'						=>	$PHP_id
 										 );
 			$f1 = $smpl_ship->update_field_id($parm); 	 				
 		}

 		$parm = array ('size'				=>	$PHP_size,
 									 'qty'				=>	$PHP_qty,
 									 'f_ship_id'	=>	$PHP_id,
 									 'smpl_num'		=>	$PHP_smpl_num
 									 );
 		$f1 = $smpl_ship->add_det($parm);
 		
 	 		
		$op = $smpl_ship->get($PHP_id);
		
		$smpl_rec = $smpl_ship->get_smpl($PHP_dept, $PHP_fty);
		for($i=0; $i<sizeof($smpl_rec); $i++)
		{
			$smpl_value[] = $smpl_rec[$i]['num'];
			$smpl_key[] = $smpl_rec[$i]['num']." [ ". $smpl_rec[$i]['qty_done'] ." ]";
		}
		$op['shp']['sample'] = $arry2->select($smpl_key,'','PHP_smpl_num','select','get_smpl(this)',$smpl_value);
	
		$op['msg'][]= 'Successfully append shipping item in ['.$op['shp']['ship_num']."]";
	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_ADD);
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "del_det_ajax":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "del_det_ajax":
 		$f1 = $smpl_ship->del_det($PHP_id);
		echo  'Success Delete Shipping item';
	exit;
	break;		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "edit_det_ajax":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "edit_det_ajax":
 			$parm = array ('field_name'		=>	'ship_qty',
 										 'field_value'	=>	$PHP_qty,
 										 'id'						=>	$PHP_id
 										 );
		$smpl_ship->update_field_id($parm,'fty_smpl_ship_det');	
		
		echo  'Success EDIT Shipping item';
	exit;
	break;		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_ship_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_ship_add":
	check_authority(2,10,"add");

 		$parm = array ('field_name'		=>	'ship_to',
 									 'field_value'	=>	$PHP_ship_to,
 									 'id'						=>	$PHP_id
 									 );
 		$f1 = $smpl_ship->update_field_id($parm); 	
 			
		$op = $smpl_ship->get($PHP_id);
		if(sizeof($op['shp_det']) == 0)
		{
		
	 		$f1 = $smpl_ship->del($PHP_id);		
			$msg = 'Delete sample ship ['.$op['shp']['ship_num']."]";
			$log->log_add(0,"210D",$msg);
			$op['msg'][] = $msg;
			page_display($op, 2,10, $TPL_FTY_SMPL_SHIP);
			break;
			
		}

	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_SHOW);		
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_list":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_ship_list":
 	check_authority(2,10,"view");

		if(isset($SCH_ship_num))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"ship_num"		=>  $SCH_ship_num,
													"ship_to"			=>  $SCH_ship_to,
													"fty"					=>	$SCH_fty,
													"ship_str"		=>	$SCH_ship_str,
													"ship_fsh"		=>	$SCH_ship_fsh,
													"sr_startno"	=>	$PHP_sr_startno,
													"action"			=>	$PHP_action
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
			}
			
	 		$op = $smpl_ship->search(1);		
			$op['msg']= $smpl_ship->msg->get(2);
			
	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_LIST);
	break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_ship_view":
	check_authority(2,10,"view");

		$op = $smpl_ship->get($PHP_id);


	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_SHOW);		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_ship_edit":
	check_authority(2,10,"add");

		$op = $smpl_ship->get($PHP_id);

		$smpl_rec = $smpl_ship->get_smpl($op['shp']['dept'], $op['shp']['fty']);
		for($i=0; $i<sizeof($smpl_rec); $i++)
		{
			$smpl_value[] = $smpl_rec[$i]['num'];
			$smpl_key[] = $smpl_rec[$i]['num']." [ ". $smpl_rec[$i]['qty_done'] ." ]";
		}
		$op['shp']['sample'] = $arry2->select($smpl_key,'','PHP_smpl_num','select','get_smpl(this)',$smpl_value);

	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_ADD);		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_ship_submit":
	check_authority(2,10,"view");

		$f1 =	$smpl_ship->update_fty_ship($PHP_id);
		
		if(!$f1)
		{
			$op = $smpl_ship->get($PHP_id);
			$op['msg'][] = "SHIP QTY must small than SAMPLE QTY!";
			page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_SHOW);		
			break;	
			
		}

 		$parm = array ('field_name'		=>	'status',
 									 'field_value'	=>	2,
 									 'id'						=>	$PHP_id
 									 );
 		$f1 = $smpl_ship->update_field_id($parm); 	

 		$parm = array ('field_name'		=>	'sub_date',
 									 'field_value'	=>	date('Y-m-d'),
 									 'id'						=>	$PHP_id
 									 ); 									 
 		$f1 = $smpl_ship->update_field_id($parm); 	

 		$parm = array ('field_name'		=>	'sub_user',
 									 'field_value'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
 									 'id'						=>	$PHP_id
 									 ); 									 
 		$f1 = $smpl_ship->update_field_id($parm); 	

 	
		$op = $smpl_ship->get($PHP_id);
		$msg = "Successfully submit sample ship :[".$op['shp']['ship_num']."]";
		$op['msg'][] =$msg;
		$log->log_add(0,"210S",$msg);


	page_display($op, 2,10, $TPL_FTY_SMPL_SHIP_SHOW);		
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rcvd":
 	check_authority(2,11,"view");

		$sales_dept_ary = get_sales_dept(); // ﮏA򯠧±�嶱뾇 [®�41;ԝK0] ------
		$op['dept'] = $arry2->select($sales_dept_ary,"","PHP_dept","select");  
		$op['dept_search'] = $arry2->select($sales_dept_ary,"","SCH_dept","select");  

		for($i=0; $i<sizeof($sales_dept_ary); $i++)
		{
			if($sales_dept_ary[$i] == $GLOBALS['SCACHE']['ADMIN']['dept'])
			{
				$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept']."<input type=hidden name=PHP_dept value='".$GLOBALS['SCACHE']['ADMIN']['dept']."'>";
				$op['dept_search'] = "<B>".$GLOBALS['SCACHE']['ADMIN']['dept']."</B><input type=hidden name=SCH_dept value='".$GLOBALS['SCACHE']['ADMIN']['dept']."'>";

				break;
			}
		}
		$op['factory'] = $arry2->select($SAMPLER,'','SCH_fty','select','');
		$op['fty'] = $arry2->select($SAMPLER,'','PHP_fty','select');

	page_display($op, 2,11, $TPL_SMPL_RCVD);
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rcvd_add_list":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rcvd_add_list":
 	check_authority(2,11,"add");

		if(isset($PHP_ship))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"ship_num"		=>  $PHP_ship,
													"ship_to"			=>  $PHP_dept,
													"fty"					=>	$PHP_fty,
													"sr_startno"	=>	$PHP_sr_startno,
													"action"			=>	$PHP_action
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
			}
			
	 		$op = $smpl_ship->search(2);		
			$op['msg']= $smpl_ship->msg->get(2);
			
	page_display($op, 2,11, $TPL_SMPL_RCVD_ADD_LIST);
	break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rcv_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rcvd_add":
	check_authority(2,11,"view");

		$op = $smpl_ship->get($PHP_id);
		$op['shp']['rcv_num'] = 'R'.substr($op['shp']['ship_num'],1);

	page_display($op, 2,11, $TPL_SMPL_RCVD_ADD);		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rcv_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rcvd_add":
	check_authority(2,11,"view");

		$op = $smpl_ship->get($PHP_id);
		
		$rcv_num = 'R'.substr($op['shp']['ship_num'],1);
		$parm = array('rcv_num'			=>	'R'.substr($op['shp']['ship_num'],1),
									'rcv_o_date'	=>	date('Y-m-d'),
									'rcv_o_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
									'status'			=>	4,
									'id'					=>	$PHP_id
									);
 		$f1 = $smpl_ship->rcv_add($parm); 	
		foreach($PHP_qty as $key=> $value)
		{
	 		$parm = array ('field_name'		=>	'rcv_qty',
 										 'field_value'	=>	$value,
 										 'id'						=>	$key
 										 ); 									 
 			$f1 = $smpl_ship->update_field_id($parm,'fty_smpl_ship_det'); 	

		}

		$op = $smpl_ship->get($PHP_id);
		$msg = "Successfully Append sample receive :[".$op['shp']['rcv_num']."]";
		$op['msg'][] =$msg;
		$log->log_add(0,"211A",$msg);

	page_display($op, 2,11, $TPL_SMPL_RCVD_SHOW);		
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_list":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rcvd_search":
 	check_authority(2,11,"view");

		if(isset($SCH_rcv_num))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"rcv_num"			=>  $SCH_rcv_num,
													"dept"				=>  $SCH_dept,
													"fty"					=>	$SCH_fty,
													"rcv_str"			=>	$SCH_rcv_str,
													"rcv_fsh"			=>	$SCH_rcv_fsh,
													"sr_startno"	=>	$PHP_sr_startno,
													"action"			=>	$PHP_action
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
			}
			
	 		$op = $smpl_ship->search(3);		
			$op['msg']= $smpl_ship->msg->get(2);
			
	page_display($op, 2,11, $TPL_SMPL_RCVD_LIST);
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rcv_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rcvd_view":
	check_authority(2,11,"view");

		$op = $smpl_ship->get($PHP_id);
	
	page_display($op, 2,11, $TPL_SMPL_RCVD_SHOW);		
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rcv_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rcvd_edit":
	check_authority(2,11,"add");

		$op = $smpl_ship->get($PHP_id);
	
	page_display($op, 2,11, $TPL_SMPL_RCVD_EDIT);		
	break;			
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_rcvd_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rcvd_edit":
	check_authority(2,11,"edit");

		$op = $smpl_ship->get($PHP_id);	

		foreach($PHP_qty as $key=> $value)
		{
	 		$parm = array ('field_name'		=>	'rcv_qty',
 										 'field_value'	=>	$value,
 										 'id'						=>	$key
 										 ); 									 
 			$f1 = $smpl_ship->update_field_id($parm,'fty_smpl_ship_det'); 	

		}

		$op = $smpl_ship->get($PHP_id);

	page_display($op, 2,11, $TPL_SMPL_RCVD_SHOW);		
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rcvd_submit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "rcvd_submit":
	check_authority(2,10,"view");

		$f1 =	$smpl_ship->update_rcv($PHP_id);
		

 		$parm = array ('field_name'		=>	'status',
 									 'field_value'	=>	6,
 									 'id'						=>	$PHP_id
 									 );
 		$f1 = $smpl_ship->update_field_id($parm); 	

 		$parm = array ('field_name'		=>	'rcv_s_date',
 									 'field_value'	=>	date('Y-m-d'),
 									 'id'						=>	$PHP_id
 									 ); 									 
 		$f1 = $smpl_ship->update_field_id($parm); 	

 		$parm = array ('field_name'		=>	'rcv_s_user',
 									 'field_value'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
 									 'id'						=>	$PHP_id
 									 ); 									 
 		$f1 = $smpl_ship->update_field_id($parm); 	

 	
		$op = $smpl_ship->get($PHP_id);
		$msg = "Successfully submit sample receive :[".$op['shp']['rcv_num']."]";
		$op['msg'][] =$msg;
		$log->log_add(0,"210S",$msg);


	page_display($op, 2,10, $TPL_SMPL_RCVD_SHOW);		
	break;		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "s_ship":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "s_ship":
 	check_authority(2,12,"view");

		$where_str = $manager = $dept_id = '';
		$dept_ary = '';

		$sales_dept_ary = get_sales_dept(); //  [®�41;ԝK0] ------
		
		$user_dept = $_SESSION['SCACHE']['ADMIN']['dept']; 
		$user_team = $_SESSION['SCACHE']['ADMIN']['team_id'];
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){
				$dept_ary = "<B>".$GLOBALS['SCACHE']['ADMIN']['dept']."</B><input type=hidden name=PHP_dept value='".$GLOBALS['SCACHE']['ADMIN']['dept']."'>";  
			}
		}
		if (!$dept_ary) {    
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept","select","get_dept(this)");  
		} else {
//			$where_str = " WHERE dept = '".$dept_id."' ";
		}
		
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		if(($user_dept == 'HJ' ||$user_dept == 'LY' ) && $team<>'MD')
		{
		
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","get_dept(this)");  
			$where_str ='';

		}
		
		$op['ship_from'] = $op['dept_ary'] = $dept_ary;		
		
		$where_str=$where_str."order by cust_s_name";
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if(!$cust_def_vue = $cust->get_fields('cust_s_name',$where_str)){;  
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','check_add(this)',$cust_def_vue); 
 


		$op['fty'] = $arry2->select($SAMPLER,'','PHP_fty','select',"get_dept()");

	page_display($op, 2,12, $TPL_SMPL_SHIP);
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "s_ship_add":
 	check_authority(2,10,"add");
		$op['shp']['ship_num'] = "S".$PHP_dept.date('y').'-xxxx';
		$op['shp']['dept'] = $PHP_dept;
		$op['shp']['cust'] = $op['shp']['ship_to'] = $PHP_cust;
		$smpl_rec = $smpl_ship->get_smpl_ship($PHP_dept, $PHP_cust);
		for($i=0; $i<sizeof($smpl_rec); $i++)
		{
			$smpl_value[] = $smpl_rec[$i]['num'];
			$smpl_key[] = $smpl_rec[$i]['num']." [ ". $smpl_rec[$i]['qty'] ." ]";
		}
		$op['shp']['sample'] = $arry2->select($smpl_key,'','PHP_smpl_num','select','get_smpl(this)',$smpl_value);
		
		
		
	page_display($op, 2,10, $TPL_SMPL_SHIP_ADD);
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "s_ship_add_det":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "s_ship_add_det":
 	check_authority(2,12,"add");

	  if(!$PHP_id)
	  {
			$head = "S".$PHP_cust.date('y').'-';
			$ship_num = $apply->get_no($head,'ship_num','cust_smpl_ship');	//ﮏA箃쀾ȥXªk㶿

 			$parm = array ('ship_num'		=>	$ship_num,
 										 'ship_to'		=>	$PHP_ship_to,
 										 'cust'				=>	$PHP_cust,
 										 'dept'				=>	$PHP_dept,
 										 'open_date'	=>	date('Y-m-d'),
 										 'open_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
 										 );
 			$PHP_id = $smpl_ship->add_ship($parm);
			$op['msg']= $smpl_ship->msg->get(2);
			$log->log_add(0,"210A",$op['msg'][0]);
 		}else{
 			$parm = array ('field_name'		=>	'ship_to',
 										 'field_value'	=>	$PHP_ship_to,
 										 'id'						=>	$PHP_id
 										 );
 			$f1 = $smpl_ship->update_field_id($parm,'cust_smpl_ship'); 	 				
 		}

 		$parm = array ('size'				=>	$PHP_size,
 									 'qty'				=>	$PHP_qty,
 									 'c_ship_id'	=>	$PHP_id,
 									 'smpl_num'		=>	$PHP_smpl_num
 									 );
 		$f1 = $smpl_ship->add_ship_det($parm);
 		
 	 		
		$op = $smpl_ship->get_ship($PHP_id);
		
		$smpl_rec = $smpl_ship->get_smpl_ship($PHP_dept, $PHP_cust);
		for($i=0; $i<sizeof($smpl_rec); $i++)
		{
			$smpl_value[] = $smpl_rec[$i]['num'];
			$smpl_key[] = $smpl_rec[$i]['num']." [ ". $smpl_rec[$i]['qty'] ." ]";
		}
		$op['shp']['sample'] = $arry2->select($smpl_key,'','PHP_smpl_num','select','get_smpl(this)',$smpl_value);
	
		$op['msg'][]= 'Successfully append shipping item in ['.$op['shp']['ship_num']."]";
		
		if(isset($PHP_edit))
		{
			page_display($op, 2,12, $TPL_SMPL_SHIP_EDIT);
		}else{
			page_display($op, 2,12, $TPL_SMPL_SHIP_ADD);
		}
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "edit_ship_det_ajax":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "edit_ship_det_ajax":
 			$parm = array ('field_name'		=>	'ship_qty',
 										 'field_value'	=>	$PHP_qty,
 										 'id'						=>	$PHP_id
 										 );
		$smpl_ship->update_field_id($parm,'cust_smpl_ship_det');	
		
		echo  'Success EDIT Shipping item';
	exit;
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "del_det_ajax":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "del_ship_det_ajax":
 		$f1 = $smpl_ship->del_ship_det($PHP_id);
		echo  'Success Delete Shipping item';
	exit;
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_ship_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_s_ship_add":
	check_authority(2,12,"add");

 		$parm = array ('field_name'		=>	'ship_to',
 									 'field_value'	=>	$PHP_ship_to,
 									 'id'						=>	$PHP_id
 									 );
 		$f1 = $smpl_ship->update_field_id($parm,'cust_smpl_ship'); 	
 			
		$op = $smpl_ship->get_ship($PHP_id);
		if(sizeof($op['shp_det']) == 0)
		{
		
	 		$f1 = $smpl_ship->del_ship($PHP_id);		
			$msg = 'Delete sample ship ['.$op['shp']['ship_num']."]";
			$log->log_add(0,"210D",$msg);
			$op['msg'][] = $msg;
			page_display($op, 2,12, $TPL_SMPL_SHIP);
			break;
			
		}

	page_display($op, 2,12, $TPL_SMPL_SHIP_SHOW);		
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "s_ship_list":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "s_ship_list":
 	check_authority(2,12,"view");

		if(isset($SCH_ship_num))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"ship_num"		=>  $SCH_ship_num,
													"ship_to"			=>  $SCH_ship_to,
													"dept"				=>	$PHP_dept,
													"ship_str"		=>	$SCH_ship_str,
													"ship_fsh"		=>	$SCH_ship_fsh,
													"sr_startno"	=>	$PHP_sr_startno,
													"action"			=>	$PHP_action
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
			}
			
	 		$op = $smpl_ship->search_ship(1);		
			$op['msg']= $smpl_ship->msg->get(2);
			
	page_display($op, 2,12, $TPL_SMPL_SHIP_LIST);
	break;	
	
	
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "s_ship_view":
	check_authority(2,12,"view");

		$op = $smpl_ship->get_ship($PHP_id);


	page_display($op, 2,12, $TPL_SMPL_SHIP_SHOW);		
	break;	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fty_ship_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "s_ship_edit":
	check_authority(2,12,"add");

		$op = $smpl_ship->get_ship($PHP_id);

		$smpl_rec = $smpl_ship->get_smpl_ship($op['shp']['dept'], $op['shp']['cust']);
		for($i=0; $i<sizeof($smpl_rec); $i++)
		{
			$smpl_value[] = $smpl_rec[$i]['num'];
			$smpl_key[] = $smpl_rec[$i]['num']." [ ". $smpl_rec[$i]['qty'] ." ]";
		}
		$op['shp']['sample'] = $arry2->select($smpl_key,'','PHP_smpl_num','select','get_smpl(this)',$smpl_value);

	page_display($op, 2,12, $TPL_SMPL_SHIP_EDIT);		
	break;
	
	
			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "s_ship_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "s_ship_submit":
	check_authority(2,12,"view");

		$f1 =	$smpl_ship->update_sales_ship($PHP_id);
		
		if(!$f1)
		{
			$op = $smpl_ship->get_ship($PHP_id);
			$op['msg'][] = "SHIP QTY must small than RECEIVE QTY!";
			page_display($op, 2,12, $TPL_SMPL_SHIP_SHOW);		
			break;	
			
		}

 		$parm = array ('field_name'		=>	'status',
 									 'field_value'	=>	2,
 									 'id'						=>	$PHP_id
 									 );
 		$f1 = $smpl_ship->update_field_id($parm,'cust_smpl_ship'); 	

 		$parm = array ('field_name'		=>	'sub_date',
 									 'field_value'	=>	date('Y-m-d'),
 									 'id'						=>	$PHP_id
 									 ); 									 
 		$f1 = $smpl_ship->update_field_id($parm,'cust_smpl_ship'); 	

 		$parm = array ('field_name'		=>	'sub_user',
 									 'field_value'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
 									 'id'						=>	$PHP_id
 									 ); 									 
 		$f1 = $smpl_ship->update_field_id($parm,'cust_smpl_ship'); 	

 	
		$op = $smpl_ship->get_ship($PHP_id);
		$msg = "Successfully submit sample ship :[".$op['shp']['ship_num']."]";
		$op['msg'][] =$msg;
		$log->log_add(0,"212S",$msg);


	page_display($op, 2,12, $TPL_SMPL_SHIP_SHOW);		
	break;		
	
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "fty_ship_print":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "fty_ship_print":   //  .......
		check_authority(5,1,"view");	
		$op = $smpl_ship->get($PHP_id);

//---------------------------------- PDF開始------------------------------

include_once($config['root_dir']."/lib/class.pdf_smpl_ship.php");


$main = array('ship_date'	=>	$op['shp']['sub_date'],
							'ship_to'		=>	$op['shp']['ship_to']
							);

$print_title = $op['shp']['fty'];
//$print_title2 = "VER.".$op['po_ship']['ver'];
$creator = $op['shp']['sub_user'];
$mark = $op['shp']['ship_num'];
$pdf=new PDF_smpl_ship('P','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$ttl_qty = 0;
for($i=0; $i<sizeof($op['shp_det']); $i++)
{
	$pdf->txt($op['shp_det'][$i]);
	$ttl_qty += $op['shp_det'][$i]['ship_qty'];
}
$pdf->sum($ttl_qty);


$name=$op['shp']['ship_num'].'_ship.pdf';

$pdf->Output($name,'D');


break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "fty_ship_print":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "s_ship_print":   //  .......
		check_authority(5,1,"view");	
		$op = $smpl_ship->get_ship($PHP_id);

//---------------------------------- PDF開始------------------------------

include_once($config['root_dir']."/lib/class.pdf_smpl_ship.php");

if ($op['shp']['ship_to'] == $op['shp']['cust']) $op['shp']['ship_to'] = $op['shp']['cust_name'];

$main = array('ship_date'	=>	$op['shp']['sub_date'],
							'ship_to'		=>	$op['shp']['ship_to']
							);

$print_title = $op['shp']['dept'];
//$print_title2 = "VER.".$op['po_ship']['ver'];
$creator = $op['shp']['sub_user'];
$mark = $op['shp']['ship_num'];
$pdf=new PDF_smpl_ship('P','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$ttl_qty = 0;
for($i=0; $i<sizeof($op['shp_det']); $i++)
{
	$pdf->txt($op['shp_det'][$i]);
	$ttl_qty += $op['shp_det'][$i]['ship_qty'];
}
$pdf->sum($ttl_qty);


$name=$op['shp']['ship_num'].'_ship.pdf';

$pdf->Output($name,'D');


break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "smpl_rcvd_print":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_rcvd_print":   //  .......
		check_authority(5,1,"view");	
		$op = $smpl_ship->get($PHP_id);

//---------------------------------- PDF開始------------------------------

include_once($config['root_dir']."/lib/class.pdf_smpl_rcv.php");


$main = array('rcv_date'	=>	$op['shp']['rcv_s_date'],
							'shipper'		=>	$op['shp']['fty'],
							'receiver'	=>	$op['shp']['dept']
							);

$print_title = $op['shp']['fty'];
//$print_title2 = "VER.".$op['po_ship']['ver'];
$creator = $op['shp']['sub_user'];
$mark = $op['shp']['rcv_num'];
$pdf=new PDF_smpl_rcvd('P','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$ttl_qty = 0;
for($i=0; $i<sizeof($op['shp_det']); $i++)
{
	$pdf->txt($op['shp_det'][$i]);
	$ttl_qty += $op['shp_det'][$i]['rcv_qty'];
}
$pdf->sum($ttl_qty);


$name=$op['shp']['rcv_num'].'_ship.pdf';

$pdf->Output($name,'D');


break;
	
	
	
	
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "get_size_ajax":
		$smpl_rec = $smpl_ship->get_smpl_size($SCH_smpl);
		
		$size = $arry2->select($smpl_rec,'','PHP_size','select');
		
		echo $size;
		
		exit;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "get_ship_size_ajax":
		$smpl_rec = $smpl_ship->get_ship_size($SCH_smpl);
		
		$size = $arry2->select($smpl_rec,'','PHP_size','select');
		
		echo $size;
		
		exit;	
//-------------------------------------------------------------------------

}   // end case ---------

?>
