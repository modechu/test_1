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
$op = array();

$P_LINE = array (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);

// echo $PHP_action;
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "para_set":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "para_set":
 	check_authority('094',"add");

	page_display($op,'094', $TPL_SET);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "para_set":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "para_list":
 	check_authority('094',"add");
		$op['para'] = $para->search();
		if (!isset($op['para'][0])) $op['record_NONE'] = 1;
	page_display($op,'094', $TPL_PARA);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_para_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_para_add":
 	check_authority('094',"add");
 		$parm = array ('set_name'		=>	$PHP_set_name,
                       'set_value'	    =>	$PHP_set_value,
                       'memo'			=>	$PHP_memo
                       );
 		$f1 = $para->add($parm);
		$op['para'] = $para->search();
		$op['msg']= $para->msg->get(2);
		if ($f1) $log->log_add(0,"83A",$op['msg'][0]);
		if (!isset($op['para'][0])) $op['record_NONE'] = 1;
	page_display($op,'094', $TPL_PARA);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "update_para":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "update_para":
	check_authority('094',"add");

		$op['para'] = $para->get($PHP_id);
	//	$op['msg'][] = "Update Record :".$op['para']['memo'];
	//	$msg_YES = 1;
	page_display($op,'094', $TPL_PARA_UPDATE);		
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_para_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_para_update":
 	check_authority('094',"edit");
 		$parm = array ('id'					=>	$PHP_id,
 									 'set_value'	=>	$PHP_set_value,
 									 'memo'				=>	$PHP_memo
 									 );
 		$f1 = $para->update($parm);
		$op['para'] = $para->search();
		$op['msg'][]= 'Success Update Para. Set on :'.$PHP_memo;
		if ($f1) $log->log_add(0,"83E",$op['msg'][0]);
		if (!isset($op['para'][0])) $op['record_NONE'] = 1;
	page_display($op,'094', $TPL_PARA);
	break;		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "para_del":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "para_del":
 	check_authority('094',"del");
 		$f1 = $para->del($PHP_id);
		$op['para'] = $para->search();
		$op['msg'][]= 'Success Delete Para. Set on :'.$PHP_memo;
		if ($f1) $log->log_add(0,"83D",$op['msg'][0]);
		if (!isset($op['para'][0])) $op['record_NONE'] = 1;
	page_display($op,'094', $TPL_PARA);
	break;		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "mesg_list":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "mesg_list":
	check_authority('094',"add");
		$msgs = $op['mesg'] = $para->search_mesg();
		if (!isset($op['mesg'][0])) $op['record_NONE'] = 1;

	page_display($op,'094', $TPL_MESG_LIST);
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "mesg_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "mesg_show":
 	check_authority('094',"add");
		$op['mesg'] = $para->get_mesg($PHP_id);
		$op['mesg']['set_text'] = str_replace( chr(13).chr(10), "<br>", $op['mesg']['set_text'] );
		
	page_display($op,'094', $TPL_MESG_SHOW);
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "mesg_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "mesg_update":
 	check_authority('094',"add");
		$op['mesg'] = $para->get_mesg($PHP_id);
 		$dept=$dept->get_fields('dept_code');		
		$op['dept_select'] =  $arry2->select($dept,'','PHP_dept','select',"get_dept(this)");
		$op['user_select'] = "--NONE--";
		
	page_display($op,'094', $TPL_MESG_EDIT);
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "mesg_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_mesg_update":
 	check_authority('094',"add");
 	
 		$parm = array ('set_user'		=>	$PHP_set_user,
 									 'set_title'	=>	$PHP_set_title,
 									 'set_text'		=>	$PHP_set_text,
 									 'id'					=>	$PHP_id,
 									 );
		$f1 = $para->edit_mesg($parm);
		$op['mesg'] = $para->get_mesg($PHP_id);
		$op['mesg']['set_text'] = str_replace( chr(13).chr(10), "<br>", $op['mesg']['set_text'] );
		
	page_display($op,'094', $TPL_MESG_SHOW);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "mesg_list":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "mesg_del":
 	check_authority('094',"add");
		
		$para->del_mesg($PHP_id);

		$op['mesg'] = $para->search_mesg();
		$op['msg'][] = 'Successfully delete job ['.$PHP_job.']';
	page_display($op,'094', $TPL_MESG_LIST);
	break;


//-------------------------------------------------------------------------

}   // end case ---------

?>
