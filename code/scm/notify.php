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
#			index.php  ￥Dμ{|!
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.notify.php");

$TPL_NOTIFY_BACKUP = 'notify_backup.html';

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";
$notify = new NOTIFY();
if (!$notify->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$op = array();


// echo $PHP_action;

switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "notify":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "notify":
check_authority("048","view");
$op=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id']);
if(!empty($op['notify'])){
	if (!count($op['notify']))$op['record_NONE']=1;
	for ($i=0; $i<sizeof($op['notify']); $i++){
		$op['notify'][$i]['msg'] = str_replace( chr(13).chr(10), "<br>", $op['notify'][$i]['msg'] );
	}
}
if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'yankee')$op['bc_flag'] = 1;

page_display($op, "048", $TPL_NOTIFY);
break;
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "notify_backup":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "notify_backup":
check_authority("048","view");

$op=$notify->search($_SESSION['SCACHE']['ADMIN']['login_id']);
if(!empty($op['notify'])){
	if (!count($op['notify']))$op['record_NONE']=1;
	for ($i=0; $i<sizeof($op['notify']); $i++){
		$op['notify'][$i]['msg'] = str_replace( chr(13).chr(10), "<br>", $op['notify'][$i]['msg'] );
	}
}

if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'yankee')$op['bc_flag'] = 1;
page_display($op,"048",$TPL_NOTIFY_BACKUP);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_notify_mark":	 	JOB 53A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_notify_mark":
check_authority("048","view");	
$parm = array (	'field_name' 	=>	'read',
				'field_value'	=>	1,
				'id'			=>	$PHP_id
				);
if($msgr <> 'backup' )echo $return=$notify->update_field($parm);
exit;
break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "message_send":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "message_send":
check_authority("048","view");
if(isset($PHP_user_id)) $op['nty']['tuser'] = $PHP_user_id;
$dept=$dept->get_fields('dept_code');	
$op['dept_select'] =  $arry2->select($dept,'','PHP_dept','select',"get_dept(this)");
$op['user_select'] = "--NONE--";

page_display($op, "048", $TPL_NOTIFY_SEND);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_user_ajx":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_user_ajx":
$where_str = " WHERE dept = '".$SCH_dept."' AND active  = 'Y' ORDER BY name";
$user_name = $user->get_fields('name',$where_str);	
$user_id = $user->get_fields('login_id',$where_str);

$user_select = $arry2->select($user_name,'','PHP_user_name','select',"get_user(this)",$user_id);
header('Content-Type:text/html;charset=big5');
echo $user_select;
exit;
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_message_send":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_message_send":
check_authority("048","view");
if($GLOBALS['SCACHE']['ADMIN']['name'] == 'yankee') $GLOBALS['SCACHE']['ADMIN']['email'] = 'jack@tp.carnival.com.tw';
if(strstr($PHP_msg,'&#'))
{
	$PHP_msg = $ch_cov->check_cov($PHP_msg);
}

if(strstr($PHP_title,'&#'))
{
	$PHP_title = $ch_cov->check_cov($PHP_title);
}

$PHP_pop = ( !empty($PHP_pop) )? $PHP_pop : 'n' ;
$PHP_backup = ( !empty($PHP_backup) )? $PHP_backup : 0 ;
#$PHP_msg = nl2br(str_replace('\\','',$PHP_msg));
if(!empty($PHP_msg)){
	$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	$msg .= '<html xmlns="http://www.w3.org/1999/xhtml">';
	$msg .= '<head>';
	$msg .= '<meta http-equiv="Content-Type" content="text/html; charset=utf8" />';
	$msg .= '<link rel=stylesheet type="text/css" href="./bom.css">';
	$msg .= '<title>MSG</title>';
	$msg .= '<style type="text/css"><!--';
	$msg .= '.style5 {	font-size: 12px;	color: #666666;}';
	$msg .= '-->';
	$msg .= '</style>';
	$msg .= '</head>';
	$msg .= '<body>';
	$msg .= '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">';
	$msg .= '<tr>';
	$msg .= '<td><img src="./images/Logo4_bar.gif" width="600" height="50" /></td>';
	$msg .= '</tr>';
	$msg .= '<tr>';
	$msg .= '<td>'.nl2br($PHP_msg).'</td>';
	$msg .= '</tr>';
	$msg .= '  <tr>';
	$msg .= '<td height="16" align="center" bgcolor="#CCCCCC"><span class="gry10 style5">CARNIVAL, COPY Right and All Right Reserved.</span></td>';
	$msg .= '</tr>';
	$msg .= '</table>';
	$msg .= '</body>';
	$msg .= '</html>';
}

if($PHP_tuser == 'system_admin')
{
	$parm = array(
	"tuser"			=>  'yankee',
	"title"			=>	$PHP_title,
	"msg"				=>	$PHP_msg,	
	"backup"		=>	$PHP_backup,
	"pop"				=>	$PHP_pop,
	"fuser"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	"send_date"	=>	$TODAY,
	);

	$f1=$notify->add($parm);  //message add

	//mail		
	$subject = "[from:SCM]".$PHP_title;

	// mail to mode
	$to = 'mode@tp.carnival.com.tw';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
	$headers .= "From: ".$GLOBALS['SCACHE']['ADMIN']['name']." <".$GLOBALS['SCACHE']['ADMIN']['email'].">\r\nReply-To: Mode <mode@tp.carnival.com.tw>";
	mail("$to", "$subject", "$msg", "$headers");
	//mail to yankee			
	$to = 'jack@tp.carnival.com.tw';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
	$headers .= "From: ".$GLOBALS['SCACHE']['ADMIN']['name']." <".$GLOBALS['SCACHE']['ADMIN']['email'].">\r\nReply-To: jack <jack@tp.carnival.com.tw>";
	mail("$to", "$subject", "$msg", "$headers");

} else if ($PHP_tuser == 'all_users') {
	$where_str = " WHERE active  = 'Y'";
	if($PHP_dept) $where_str = $where_str." AND dept= '".$PHP_dept."' ";
	$tuser_p = $user->get_fields('login_id',$where_str);
	for($i=0; $i< sizeof($tuser_p); $i++)
	{
		$parm = array(
		"tuser"			=>  $tuser_p[$i],
		"title"			=>	$PHP_title,
		"msg"				=>	$PHP_msg,	
		"backup"		=>	$PHP_backup,
		"pop"				=>	$PHP_pop,
		"fuser"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		"send_date"	=>	$TODAY,
		);				
		$f1=$notify->add($parm); // message add			
	}
	$PHP_tuser = $PHP_dept."all users";

}else{
	$tuser_p = explode(';',$PHP_tuser);
	for($i=0; $i< sizeof($tuser_p); $i++)
	{
		$parm = array(
		"tuser"			=>  $tuser_p[$i],
		"title"			=>	$PHP_title,
		"msg"				=>	$PHP_msg,	
		"backup"		=>	$PHP_backup,
		"pop"				=>	$PHP_pop,
		"fuser"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		"send_date"	=>	$TODAY,
		);				
		$f1=$notify->add($parm); // message add
		//mail
		$mail_user['email'] = '';
		if (isset($PHP_mail))
		{							
			$subject = "[from:SCM]".$PHP_title;

			if($tuser_p[$i] == 'yankee')
			{
				 $to = 'jack@tp.carnival.com.tw';
			}else{
				$mail_user=$user->get(0,$tuser_p[$i]);
				$to = $mail_user['email'];
			}
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
			$headers .= "From: ".$GLOBALS['SCACHE']['ADMIN']['name']." <".$GLOBALS['SCACHE']['ADMIN']['email'].">\r\nReply-To: ".$mail_user['email'];
			mail("$to", "$subject", "$msg", "$headers");
		}//end if (isset($PHP_mail))
		
	}//end for($i=0; $i< sizeof($tuser_p); $i++)
}

if($msgr=='read'){
	$PHP_action='notify';
	$TPL = $TPL_NOTIFY;
} else {
	$PHP_action='notify_backup';
	$TPL = $TPL_NOTIFY_BACKUP;
}

$op=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id']);
if (!count($op['notify']))$op['record_NONE']=1;
for ($i=0; $i<sizeof($op['notify']); $i++){
	$op['notify'][$i]['msg'] = str_replace( chr(13).chr(10), "<br>", $op['notify'][$i]['msg'] );
}	
if($f1) {
	$op['msg'][]="success send message to ".$PHP_tuser;
	if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'yankee')$op['bc_flag'] = 1;
	page_display($op, "048", $TPL);
} else {
	$op['msg']=$notify->msg->get(2);
	$op['nty']=$parm;
	page_display($op, "048", $TPL_NOTIFY_SEND);
}
			
	break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "do_notify_del":
check_authority("048","view");
$j=0;
if (isset($PHP_id))
{
	foreach($PHP_id as $key => $value)
	{				
		$f1=$notify->del($key,$msgr);		
		$j++;
	}	
}

if(empty($msgr))$msgr='';
$PHP_sr_startno=0;# 如果刪除到分頁的最後一比會造成錯誤，所以有刪除都把頁數設到0
$op=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id']);

if (!count($op['notify']))$op['record_NONE']=1;
for ($i=0; $i<sizeof($op['notify']); $i++){
	$op['notify'][$i]['msg'] = str_replace( chr(13).chr(10), "<br>", $op['notify'][$i]['msg'] );
}

$op['msg'][]="success delete $j message ";
if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'yankee')$op['bc_flag'] = 1;

if($msgr=='read'){
	$TPL = $TPL_NOTIFY;
}else{
	$TPL = $TPL_NOTIFY_BACKUP;
}

page_display($op, "048", $TPL);			    	    
break;

//====================================================

//-------------------------------------------------------------------------

}   // end case ---------

?>
