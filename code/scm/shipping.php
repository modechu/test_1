<?php
session_start();
session_register	('ord_parm');
session_register	('add_sch');
session_register	('sch_parm');

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

session_register	('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

session_register	('CUST_DEL');
$para_cm = $para->get(0,'cust_del');
$CUST_DEL = $para_cm['set_value'] ;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$AUTH = '073';
$PGM = $_SESSION['ITEM']['ADMIN_PERM'];
$op = array();

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================

case "main":
check_authority($AUTH,$PGM[1]);

$_SESSION['SHIPPING']['ord_parm'] = array();

$where_str=" ORDER BY cust_s_name"; //��cust_s_name�Ƨ�
$cust_def = $cust->get_fields('cust_init_name',$where_str);//���X�Ȥ�²��
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
for ($i=0; $i< sizeof($cust_def); $i++)
	$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

if( !empty($user_dept) && ( $user_dept == 'HJ' || $user_dept == 'LY' ) )
{
	$op['fty'] = "<B>".$user_dept."</B> <input type=hidden name='PHP_fty' value='$user_dept'>";
} else {
	$op['fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');
}

$op['year'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year','select','');  	
$op['mon'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');  	

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op,$AUTH,'shipdoc_mian.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_sch":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_sch":
check_authority($AUTH,$PGM[1]);

if ( isset($SCH_inv) || ( isset($SCH_date_str) && isset($SCH_date_end)) || isset($PHP_cust) || isset($PHP_fty) ) {

	$SCH_des = (!empty($SCH_des))?$SCH_des:'';
	$_SESSION['sch_parm'] = array (
		'SCH_inv'			=>	$SCH_inv,
		'SCH_date_str'		=>	$SCH_date_str,
		'SCH_date_end'		=>	$SCH_date_end,
		'SCH_des'			=>	$SCH_des,
		'PHP_fty'			=>	$PHP_fty,
		'PHP_cust'			=>	$PHP_cust,
		'SCH_ord'			=>	$SCH_ord,
		'PHP_action'		=>	$PHP_action,
		'PHP_sr_startno'	=>	$PHP_sr_startno
	);

} else {

	if( isset($PHP_sr_startno) ) $_SESSION['sch_parm']['PHP_sr_startno'] = $PHP_sr_startno;

}

$shipping_doc->chk_shipping();

$op = $shipping_doc->search(1);

for ( $i=0; $i<sizeof($op['ship_doc']); $i++ ) {
	$op['ship_doc'][$i]['status'] = $shipping_doc->get_status($op['ship_doc'][$i]['status']);
	$str = $shipping_doc->get_data('ord_num,cust_po', $op['ship_doc'][$i]['id']);
	$op['ship_doc'][$i]['ord_num'] = $str['ord_num'];
	$op['ship_doc'][$i]['cust_po'] = $str['cust_po'];
}

if(isset($_GET['PHP_msg'])) $op['msg'][] = $_GET['PHP_msg'];

page_display($op,$AUTH,'ship_sch_list.html');
break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_sch_new":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_sch_new":
check_authority($AUTH,$PGM[1]);
//echo $_POST['PHP_action']."<br>";
//print_r($_POST);


if ( isset($SCH_inv) || ( isset($SCH_date_str) && isset($SCH_date_end)) || isset($PHP_cust) || isset($PHP_fty) ) {

	$SCH_des = (!empty($SCH_des))?$SCH_des:'';
	$_SESSION['sch_parm'] = array (
		'SCH_inv'			=>	$SCH_inv,
		'SCH_date_str'		=>	$SCH_date_str,
		'SCH_date_end'		=>	$SCH_date_end,
		'SCH_des'			=>	$SCH_des,
		'PHP_fty'			=>	$PHP_fty,
		'PHP_cust'			=>	$PHP_cust,
		'SCH_ord'			=>	$SCH_ord,
		'PHP_action'		=>	$PHP_action,
		'PHP_sr_startno'	=>	$PHP_sr_startno
	);

} else {

	if( isset($PHP_sr_startno) ) $_SESSION['sch_parm']['PHP_sr_startno'] = $PHP_sr_startno;

}

$shipping_doc->chk_shipping();
//exit;
$op = $shipping_doc->search_new(1);
//print_r($op);
for ( $i=0; $i<sizeof($op['ship_doc']); $i++ ) {
	$op['ship_doc'][$i]['status'] = $shipping_doc->get_status($op['ship_doc'][$i]['status']);
	//$str = $shipping_doc->get_data('ord_num,cust_po', $op['ship_doc'][$i]['id']);//�ª�
	$str = $shipping_doc->get_data_new('ord_num,po', $op['ship_doc'][$i]['id']);//�s��
	$op['ship_doc'][$i]['ord_num'] = $str['ord_num'];
	$op['ship_doc'][$i]['cust_po'] = $str['cust_po'];
}

if(isset($_GET['PHP_msg'])) $op['msg'][] = $_GET['PHP_msg'];
$op['main_page_action']=$PHP_action;
//print_r($op);

page_display($op,$AUTH,'ship_sch_list.html');
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_view":
check_authority($AUTH,$PGM[1]);

//print_r($_GET['PHP_main_page_action']);
if($_GET['PHP_main_page_action'] == 'ship_sch_new')
{
	//echo "new1<br>";
	$op = $shipping_doc->get_invoice_new($PHP_id);//�s��
	
	//print_r($op);
}
else
{
	//echo "old1<br>";
	//print_r($_GET);
	$op = $shipping_doc->get_invoice($PHP_id);//�ª�
}
//$op = $shipping_doc->get_invoice($PHP_id);//�ª�
//$op = $shipping_doc->get_invoice_new($PHP_id);//�s��
//print_r($op);
#���b���
$nd = 5;

$sd = '';
$op['ckdate'] = 0;
//print_r($op);
//echo increceMonthInDate2(date("Y-m").'-01',-1);
if ( $op['invoice']['ship_date'] < increceMonthInDate2(date("Y-m").'-01',-1) ) {
	
	$sd = date("Y-m").'-01';
} else {
	
	if ( date("d") > $nd ) {
	//echo "yes";
		$sd = date("Y-m").'-01';
	} else {
	//echo "no";
		$sd = increceMonthInDate2(date("Y-m").'-01',-1);
	}
}
//echo $sd;
# �t�κ޲z�̩M �|�p�H���i�H�L����ק�
if ( $op['invoice']['ship_date'] >= $sd || $_SESSION['USER']['ADMIN']['dept'] == 'SA' || $_SESSION['USER']['ADMIN']['dept'] == 'AC' ) {
	//echo "yes";
	$op['ckdate'] = 1;
} else {
	//echo "no";
	$op['ckdate'] = 0;
}

//print_r($op);
if($_GET['PHP_main_page_action'] == 'ship_sch_new')
{
	//print_r($op);
	//echo "new2<br>";
	page_display($op,$AUTH,'ship_invoice_view_new.html');//�s��
}
else
{
	//echo "old2<br>";
	page_display($op,$AUTH,'ship_invoice_view.html');//�ª�
}
//page_display($op,$AUTH,'ship_invoice_view.html');
//page_display($op,$AUTH,'ship_invoice_view_new.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_edit":
check_authority($AUTH,$PGM[3]);

$op = $shipping_doc->get_invoice_by_edit($PHP_id);
// print_r($op);
$ct = $cust->get_consignee('',$op['invoice']['invoice']['cust']);
$op['consignee_select'] = $arry2->select_id( $ct ,$op['invoice']['invoice']['consignee_id'],'PHP_consignee','consignee','select','') ;
$op['PHP_id'] = $PHP_id;

page_display($op,$AUTH,'ship_invoice_edit.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_attach":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_attach":
check_authority($AUTH,$PGM[3]);
if($_POST['PHP_new']=='new')
{
	//print_r($_POST);
	$op = $shipping_doc->get_invoice_by_edit($PHP_id);
	// print_r($op);
	$ct = $cust->get_consignee('',$op['invoice']['invoice']['cust']);
	$op['consignee'] = $ct[$op['invoice']['invoice']['consignee_id']];
	$op['PHP_id'] = $PHP_id;
	$op['PHP_new'] = $_POST['PHP_new'];

	page_display($op,$AUTH,'ship_invoice_attach.html');
}
else
{
	$op = $shipping_doc->get_invoice_by_edit($PHP_id);
	// print_r($op);
	$ct = $cust->get_consignee('',$op['invoice']['invoice']['cust']);
	$op['consignee'] = $ct[$op['invoice']['invoice']['consignee_id']];
	$op['PHP_id'] = $PHP_id;

	page_display($op,$AUTH,'ship_invoice_attach.html');
}
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "del_color_qty":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "del_color_qty":
check_authority($AUTH,$PGM[3]);
# �n�Ҽ{��p�G���R������ invoice �]�n��ۧR��

if($shipping_doc->del_color_qty($PHP_q_id)){
	echo 'ok';
	$_SESSION['MSG'][] = $mesg = "SUCCESS DELETE SHIPPING DOCUMENT ON INVOICE : [ ".$PHP_invoice." ]";
	$log->log_add(0,$AUTH."D",$mesg);
} else {
	echo 'error';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_update":
check_authority($AUTH,$PGM[3]);


$total = $amount = 0 ;

foreach( $_POST['PHP_ttl'] as $k => $v ) {
	$total += $_POST['PHP_ttl'][$k];
	$amount += $_POST['PHP_amount'][$k];
}

if ( $shipping_doc->update_shipping( $_POST['PHP_id'] , $_POST['PHP_invoice'] , $_POST['PHP_ship_date'] , $_POST['PHP_consignee'] , $total , $amount ) ) {
	foreach($PHP_q_id as $k => $v){
		if($v) {
			$shipping_doc->update_shipping_doc_qty($v,$_POST['PHP_qty'][$k],$_POST['PHP_ttl'][$k],$_POST['PHP_fob'][$k],$_POST['PHP_amount'][$k]);
		} else {
			$shipping_doc->add_shipping_doc_qty($_POST['PHP_id'],$_POST['PHP_ord'][$k],$_POST['PHP_cust_po'][$k],$_POST['PHP_w_id'][$k],$_POST['PHP_colorway'][$k],$_POST['PHP_qty'][$k],$_POST['PHP_ttl'][$k],$_POST['PHP_fob'][$k],$_POST['PHP_amount'][$k]);
		}
	}
	
	# charge
	foreach($PHP_charge_id as $k => $v){
		if($v) {
			$shipping_doc->update_shipping_doc_charge($v,$_POST['PHP_charge'][$k],$_POST['PHP_desc'][$k]);
		} else {
			$shipping_doc->add_shipping_doc_charge($_POST['PHP_id'],$_POST['PHP_charge'][$k],$_POST['PHP_desc'][$k]);
		}
	}	
	
	# file
	$S_ROOT = dirname(__FILE__);
	$Flag = substr(dirname($PHP_SELF),0,1);

	# �T�w�W���ɮפj�p
	$upload = new Upload;

	foreach($_FILES['PHP_file']['size'] as $k => $v ){
		if( $v == 0 || $v > $upload->max_file_size || $_FILES['PHP_file']['error'][$k]=='1' || $_FILES['PHP_file']['error'][$k]==='' ){
			$_SESSION['MSG'][] = $mesg = "ERROR [ ". $_FILES['PHP_file']['name'][$k]." ] UPLOAD FILE IS OVER 3M! ";
			$back_str ="shipping.php?PHP_action=ship_invoice_edit&PHP_id=".$_POST['PHP_id'];
			redirect_page($back_str);
		}
	}
	
	$file_key = 0 ;
	
	foreach($PHP_file_id as $k => $v){
	
		if($v) {

			$shipping_doc->update_shipping_doc_file($v,$_POST['PHP_desf'][$k]);
			
		} else {
		
			$filename = $_FILES['PHP_file']['name'][$file_key];
			
			if(!empty($filename) && file_exists($S_ROOT.$Flag."shipping_file".$Flag.$filename)){
				unlink($S_ROOT.$Flag."shipping_file".$Flag.$filename);
			}

			$upload->ArrUploadFile($S_ROOT.$Flag."shipping_file".$Flag, 'other', 20, $filename ,$file_key );

			if (!$upload){
				$_SESSION['MSG'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			$shipping_doc->add_shipping_doc_file($_POST['PHP_id'],$filename,$_POST['PHP_desf'][$k]);
			
			$file_key++;
			
		}	
	}	
	
	$_SESSION['MSG'][] = $mesg = "SUCCESS UPDATE DATA ON INVOICE:[ ".$_POST['PHP_invoice']." ]";
	$log->log_add(0,$AUTH."E",$mesg);
	
	$redir_str = 'shipping.php?PHP_action=ship_invoice_view&PHP_id='.$_POST['PHP_id']."&PHP_msg=".$mesg;
	
} else {

	$_SESSION['MSG'][] = $mesg = "�ק異��~ �нT�{ INVOICE NUMBER�I , �Ϊ��p���t�κ޲z�� ~ ";
	$redir_str = 'shipping.php?PHP_action=ship_invoice_edit&PHP_id='.$_POST['PHP_id']."&PHP_msg=".$mesg;
	
}

redirect_page($redir_str);


break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_attach_update":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_attach_update":
check_authority($AUTH,$PGM[3]);

	//print_r($_POST);
if ( $_POST['PHP_charge'] || $_FILES['PHP_file'] ) {

	# charge
	foreach($PHP_charge_id as $k => $v){
		if($v) {
			$shipping_doc->update_shipping_doc_charge($v,$_POST['PHP_charge'][$k],$_POST['PHP_desc'][$k]);
		} else {
			$shipping_doc->add_shipping_doc_charge($_POST['PHP_id'],$_POST['PHP_charge'][$k],$_POST['PHP_desc'][$k]);
		}
	}	
	
	# file
	$S_ROOT = dirname(__FILE__);
	$Flag = substr(dirname($PHP_SELF),0,1);

	# �T�w�W���ɮפj�p
	$upload = new Upload;

	foreach($_FILES['PHP_file']['size'] as $k => $v ){
		if( $v == 0 || $v > $upload->max_file_size || $_FILES['PHP_file']['error'][$k]=='1' || $_FILES['PHP_file']['error'][$k]==='' ){
			$_SESSION['MSG'][] = $mesg = "ERROR [ ". $_FILES['PHP_file']['name'][$k]." ] UPLOAD FILE IS OVER 3M! ";
			$back_str ="shipping.php?PHP_action=ship_invoice_edit&PHP_id=".$_POST['PHP_id'];
			redirect_page($back_str);
		}
	}
	
	$file_key = 0 ;
	
	foreach($PHP_file_id as $k => $v){
	
		if($v) {

			$shipping_doc->update_shipping_doc_file($v,$_POST['PHP_desf'][$k]);
			
		} else {
		
			$filename = $_FILES['PHP_file']['name'][$file_key];
			
			if(!empty($filename) && file_exists($S_ROOT.$Flag."shipping_file".$Flag.$filename)){
				unlink($S_ROOT.$Flag."shipping_file".$Flag.$filename);
			}

			$upload->ArrUploadFile($S_ROOT.$Flag."shipping_file".$Flag, 'other', 20, $filename ,$file_key );

			if (!$upload){
				$_SESSION['MSG'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			$shipping_doc->add_shipping_doc_file($_POST['PHP_id'],$filename,$_POST['PHP_desf'][$k]);
			
			$file_key++;
			
		}	
	}	
	
	$_SESSION['MSG'][] = $mesg = "SUCCESS UPDATE DATA ON INVOICE:[ ".$_POST['PHP_invoice']." ]";
	$log->log_add(0,$AUTH."E",$mesg);
	if($_POST['PHP_new']=='new')
	{
		$redir_str = 'shipping.php?PHP_action=ship_invoice_view&PHP_main_page_action=ship_sch_new&PHP_id='.$_POST['PHP_id']."&PHP_msg=".$mesg;
	}
	else
	{
		$redir_str = 'shipping.php?PHP_action=ship_invoice_view&PHP_id='.$_POST['PHP_id']."&PHP_msg=".$mesg;
	}
	
} else {
	if($_POST['PHP_new']=='new')
	{
		$_SESSION['MSG'][] = $mesg = "�ק異��~ �нT�{ INVOICE NUMBER�I , �Ϊ��p���t�κ޲z�� ~ ";
		$redir_str = 'shipping.php?PHP_action=ship_invoice_edit&PHP_id='.$_POST['PHP_id']."&PHP_msg=".$mesg;
	}
	else
	{
		$_SESSION['MSG'][] = $mesg = "�ק異��~ �нT�{ INVOICE NUMBER�I , �Ϊ��p���t�κ޲z�� ~ ";
		$redir_str = 'shipping.php?PHP_action=ship_invoice_edit&PHP_id='.$_POST['PHP_id']."&PHP_msg=".$mesg;
	}
}

redirect_page($redir_str);


break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "drop_charge":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "drop_charge":
check_authority($AUTH,$PGM[3]);

if( $shipping_doc->drop_charge($id) ) {
	$mesg = "SUCCESS DELETE SHIPPING DOCUMENT CHARGE ON INVOICE : [ ".$PHP_invoice." ]";
	$log->log_add(0,$AUTH."D",$mesg);
	echo 'ok';
} else {
	echo 'error';
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "drop_file":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "drop_file":
check_authority($AUTH,$PGM[3]);

$S_ROOT = dirname(__FILE__);
$Flag = substr(dirname($PHP_SELF),0,1);

$files = urldecode(uniDecode($files,'big-5'));//��X


if( $shipping_doc->drop_file($id) ) {
	$mesg = "SUCCESS DELETE SHIPPING DOCUMENT FILE (".$files.")  ON INVOICE : [ ".$PHP_invoice." ]";
	$log->log_add(0,$AUTH."D",$mesg);
	echo 'ok';
	
	unlink($S_ROOT.$Flag."shipping_file".$Flag.$files);
	
} else {
	echo 'error'.$id;
}


break;



#-------------------------------------------------------------------------------------
case "check_shipping_file":

$S_ROOT = dirname(__FILE__);
$Flag = substr(dirname($PHP_SELF),0,1);

if( !empty($file) && file_exists($S_ROOT.$Flag."shipping_file".$Flag.$file) ) {
	$file_status = 'in';
} else {
	$file_status = 'no';
}

echo $file_status.','.$file;

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ord_ship_sch":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_ship_sch":
check_authority($AUTH,$PGM[2]);

if(isset($PHP_fty)) {
	$_SESSION['add_sch']  = array();
	$_SESSION['SHIPPING']['ord_parm'] = array();
	$_SESSION['add_sch'] = array(
		'ord_parm'			=>	$_SESSION['SHIPPING']['ord_parm'],
		'PHP_fty'			=>	$PHP_fty,
		'PHP_cust'			=>	$PHP_cust,
		'PHP_num'			=>	$PHP_num,
		'PHP_action'		=>	$PHP_action,
		'PHP_sr_startno'	=>	$PHP_sr_startno
	);
} else {
	if(isset($PHP_sr_startno))$_SESSION['add_sch']['PHP_sr_startno'] = $PHP_sr_startno;
}		

$op = $shipping_doc->add_search(1,$_SESSION['SHIPPING']['ord_parm']);

foreach($op['ord'] as $k => $v){
	foreach($v as $key => $val){
		if( $key == 'cust_po' ) {
			$val = explode( '|' , $val);
			$str = '';
			foreach($val as $keys => $vals){
				$vals = explode( '/' , $vals);
				$str .= $vals[0].'<br>';
			}
			$op['ord'][$k]['cust_po'] = $str;
		}
	}
}

$op['ord_select'] = $_SESSION['SHIPPING']['ord_parm'];
$op['span'] = sizeof($op['ord_select']);

page_display($op,$AUTH,'ord_ship_sch_list.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ord_ship_add": //�W�[�h���q��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_ship_add":
check_authority($AUTH,$PGM[2]);
/* print_r($_GET);
exit; */
$parm = array(
	'id'	=>	$PHP_id,
	'num'	=>	$PHP_ord,
	'mks'	=>	$PHP_mks,
	'p_id'	=>	$PHP_p_id,
	'fty'	=>	$PHP_fty,
	'sort'	=>	$PHP_ord
);
// print_r($parm);
// print_r($_SESSION['add_sch']);
$_SESSION['add_sch']['PHP_cust'] = $PHP_cust;
$_SESSION['add_sch']['PHP_fty'] = $PHP_fty;
// $_SESSION['add_sch']['PHP_num'] = (!empty($PHP_num))?$PHP_num:'';

$ac = 0;
for( $i=0; $i<sizeof($_SESSION['SHIPPING']['ord_parm']); $i++ ) {
	if($_SESSION['SHIPPING']['ord_parm'][$i]['id'] == $PHP_id) $ac++;
}
if ( $ac === 0 ) $_SESSION['SHIPPING']['ord_parm'][] = $parm;

$op = $shipping_doc->add_search(1,$_SESSION['SHIPPING']['ord_parm']);

foreach( $op['ord'] as $k => $v ) {
	foreach( $v as $key => $val ) {
		if( $key == 'cust_po' ) {
			$val = explode( '|' , $val);
			$str = '';
			foreach($val as $keys => $vals){
				$vals = explode( '/' , $vals);
				$str .= $vals[0].'<br>';
			}
			$op['ord'][$k]['cust_po'] = $str;
		}
	}
}

$op['ord_select'] = $_SESSION['SHIPPING']['ord_parm'];
$op['span'] = sizeof($op['ord_select']);
//print_r($op);
page_display($op,$AUTH,'ord_ship_sch_list.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ord_ship_del": //�R����ܭq��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_ship_del":
check_authority($AUTH,$PGM[2]);

$parm = array();
for ( $i=0; $i<sizeof($_SESSION['SHIPPING']['ord_parm']); $i++ ) {
	if($_SESSION['SHIPPING']['ord_parm'][$i]['id'] != $PHP_id) $parm[] = $_SESSION['SHIPPING']['ord_parm'][$i];
}

$_SESSION['SHIPPING']['ord_parm'] = $parm;

$op = $shipping_doc->add_search(1,$_SESSION['SHIPPING']['ord_parm']);

foreach($op['ord'] as $k => $v){
	foreach($v as $key => $val){
		if( $key == 'cust_po' ) {
			$val = explode( '|' , $val);
			$str = '';
			foreach($val as $keys => $vals){
				$vals = explode( '/' , $vals);
				$str .= $vals[0].'<br>';
			}
			$op['ord'][$k]['cust_po'] = $str;
		}
	}
}

$op['ord_select'] = $_SESSION['SHIPPING']['ord_parm'];
$op['span'] = sizeof($op['ord_select']);

page_display($op,$AUTH,'ord_ship_sch_list.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ord_ship_check_colorway":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_ship_check_colorway": 
check_authority($AUTH,$PGM[2]);
//print_r($_POST);

$csgn = array();

# �q�渹�X�ƧǤp��j
$_SESSION['SHIPPING']['ord_parm'] = bubble_sort_s($_SESSION['SHIPPING']['ord_parm']);

$op['ord'] = $_SESSION['SHIPPING']['ord_parm'] = $shipping_doc->get_colorway($_SESSION['SHIPPING']['ord_parm']);

// $op['ord_select'] = $_SESSION['SHIPPING']['ord_parm'];
//print_r($op);
page_display($op,$AUTH,'ord_ship_check_colorway.html');
break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ord_ship_colorway_item_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_ship_colorway_item_add":
check_authority($AUTH,$PGM[2]);

$w_id = '';
$colorway = '';
$item = '';
$order_num = array();

foreach($_POST as $k => $v){

	# SQL APPEND
	if(ereg('PHP_color_',$k)){
		$color = explode('PHP_color_',$k);
		$shipping_doc->change_color_name($color[1],$v);
		$w_id = $color[1];
		$colorway = $v;
	}

	# SESSION APPEND
	foreach($_SESSION['SHIPPING']['ord_parm'] as $key => $val){
		foreach($val['colorway'] as $keys => $vals){
			if( $vals['w_id'] == $w_id ){
				$_SESSION['SHIPPING']['ord_parm'][$key]['colorway'][$keys]['colorway'] = $colorway;
				array_push($order_num,$_SESSION['SHIPPING']['ord_parm'][$key]['num']);
			}
		}
	}
}

# WRITE LOG , ORDER
$order_num = array_unique($order_num);
$order_str = '';
foreach($order_num as $ks => $vs){
	$order_str .= (($ks==0)?'':',').$vs;
}

$mesg = "SUCCESS APPEND SHIPPING DOCUMENT ~ COLORWAY ON ORDER:[ ".$order_str." ]";

$log->log_add(0,$AUTH.'A',$mesg);

$_SESSION['MSG'][] = $mesg = "SUCCESS APPEND SHIPPING DOCUMENT ~ COLORWAY <br> ON ORDER:[ ".$order_str." ]";

$redir_str = 'shipping.php?PHP_action=ord_ship_doc_add&PHP_msg='.$mesg;

redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ord_ship_doc_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_ship_doc_add":
check_authority($AUTH,$PGM[2]);

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

$_SESSION['SHIPPING']['ord_parm'] = $shipping_doc->get_colorway($_SESSION['SHIPPING']['ord_parm']);

$ct = $cust->get_consignee('',$_SESSION['SHIPPING']['ord_parm'][0]['cust']);
// echo $_SESSION['SHIPPING']['ord_parm'][0]['cust'];
// print_r($ct);
$op['consignee_select'] = $arry2->select_id( $ct ,$PHP_consignee,'PHP_consignee','consignee','select','') ;
$op['ship_date'] = $PHP_ship_date;
$op['invoice'] = $PHP_invoice;

$op['ord'] = $_SESSION['SHIPPING']['ord_parm'];

page_display($op,$AUTH,'ord_ship_doc_add.html');
break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "chk_ready":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "chk_ready":
check_authority($AUTH,$PGM[2]);
echo 'ok';
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ord_ship_doc_append":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_ship_doc_append":
check_authority($AUTH,$PGM[2]);

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

$_SESSION['SHIPPING']['ord_parm'] = $shipping_doc->get_colorway($_SESSION['SHIPPING']['ord_parm']);

$ct = $cust->get_consignee('',$_SESSION['SHIPPING']['ord_parm'][0]['cust']);
$op['consignee_select'] = $arry2->select_id( $ct ,$PHP_consignee,'PHP_consignee','consignee','select','') ;
$op['ship_date'] = $PHP_ship_date;
$op['invoice'] = $PHP_invoice;

$op['ord'] = $_SESSION['SHIPPING']['ord_parm'];

$TD = array();
$total = 0;
$amount = 0;
foreach( $_POST['PHP_ord'] as $k => $v ) {
	$TD[] = array(
		'num' 		=> $v ,
		'cust_po' 	=> $_POST['PHP_cust_po'][$k] ,
		'colorway' 	=> $_POST['PHP_colorway'][$k] ,
		'w_id' 		=> $_POST['PHP_w_id'][$k] ,
		'sqty' 		=> $_POST['PHP_qty'][$k] ,
		'qty' 		=> explode(',',$_POST['PHP_qty'][$k]) ,
		'ttl' 		=> $_POST['PHP_ttl'][$k] ,
		'fob' 		=> $_POST['PHP_fob'][$k] ,
		'amount' 	=> $_POST['PHP_amount'][$k] 
	);
	$total += $_POST['PHP_ttl'][$k];
	$amount += $_POST['PHP_amount'][$k];
}
$op['msg'] = '';
if( $ship_id = $shipping_doc->add_shipping($PHP_invoice,$PHP_consignee,$op['ord'][0]['cust'],$op['ord'][0]['factory'],$total,$amount,$PHP_ship_date) ) {
	foreach( $_POST['PHP_ord'] as $k => $v ) {
		if ( !$shipping_doc->add_shipping_doc_qty($ship_id,$v,$_POST['PHP_cust_po'][$k],$_POST['PHP_w_id'][$k],$_POST['PHP_colorway'][$k],$_POST['PHP_qty'][$k],$_POST['PHP_ttl'][$k],$_POST['PHP_fob'][$k],$_POST['PHP_amount'][$k]) ) {
			$op['msg'][] = $v.$_POST['PHP_cust_po'][$k].'QTY error!!~';
		}
	}
	// $mesg = "SUCCESS APPROVAL SHIPPING DOCUMENT ON INVOICE : [ ".$PHP_invoice." ]";
	$_SESSION['MSG'][] = $mesg = "SUCCESS APPEND SHIPPING DOCUMENT ON INVOICE : [ ".$PHP_invoice." ]";
	$log->log_add(0,$AUTH."A",$mesg);
	
	$redir_str = 'shipping.php?PHP_action=ship_invoice_view&PHP_id='.$ship_id;
	redirect_page($redir_str);
	
} else {
	$_SESSION['MSG'][] = 'Invoice ERROR~';
}

$op['td'] = $TD;
// print_r($op);
page_display($op,$AUTH,'ord_ship_doc_add.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "check_invoice_no":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "check_invoice_no":
check_authority($AUTH,$PGM[2]);

echo $shipping_doc->check_invoice_no($invoice);

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_submit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_submit":
check_authority($AUTH,$PGM[3]);

if(!$_POST['PHP_new'])
{
	if ( $shipping_doc->ship_invoice_submit($_POST['PHP_id']) ) {

		$_SESSION['MSG'][] = $mesg = "SUCCESS SUBMIT SHIPPING DOCUMENT ON INVOICE : [ ".$_POST['PHP_invoice']." ]";
		$log->log_add(0,$AUTH."E",$mesg);
		
		$back_str ="shipping.php?PHP_action=ship_sch";
		redirect_page($back_str);
		
	}
}
else
{
	if ( $shipping_doc->ship_invoice_submit($_POST['PHP_id']) ) {

		$_SESSION['MSG'][] = $mesg = "SUCCESS SUBMIT SHIPPING DOCUMENT ON INVOICE : [ ".$_POST['PHP_invoice']." ]";
		$log->log_add(0,$AUTH."E",$mesg);
		
		$back_str ="shipping.php?PHP_action=ship_sch_new";
		redirect_page($back_str);
		
	}
}

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_revise":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_revise":
check_authority($AUTH,$PGM[3]);
	//echo $PHP_id;
if( $shipping_doc->ship_invoice_revise($PHP_id) ){
	$_SESSION['MSG'][] = $mesg = "SUCCESS REVISE SHIPPING DOCUMENT ON INVOICE : [ ".$PHP_invoice." ]";
	$log->log_add(0,$AUTH."R",$mesg);
	if($PHP_flow == 'new_flow')
	{
		//$redirect_str ="invoice_import.php?PHP_action=edit_invoice&PHP_INV_NO=".$PHP_invoice ;
		$redirect_str ="invoice_import.php?PHP_action=edit_invoice&PHP_INV_NO=".$PHP_invoice ;
	}
	else
	{
		$redirect_str ="shipping.php?PHP_action=ship_invoice_edit&PHP_id=".$PHP_id;//&PHP_main_page_action=ship_sch_new
	}
	//$redirect_str ="shipping.php?PHP_action=ship_invoice_edit&PHP_id=".$PHP_id;
	//$redirect_str ="invoice_import.php?PHP_action=edit_invoice&PHP_INV_NO=".$PHP_invoice ;
} else {
	if($PHP_flow == 'new_flow')
	{
		$_SESSION['MSG'][] = $mesg = "ERROR !!!  ON INVOICE : [ ".$PHP_INVOICE." ]";
		$redirect_str ="shipping.php?PHP_action=ship_invoice_view&PHP_id=".$PHP_id."&PHP_main_page_action=ship_sch_new";
	}
	else
	{
		$_SESSION['MSG'][] = $mesg = "ERROR !!!  ON INVOICE : [ ".$PHP_INVOICE." ]";
		$redirect_str ="shipping.php?PHP_action=ship_invoice_view&PHP_id=".$PHP_id;
	}
} 

redirect_page($redirect_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_rmk":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_rmk":
check_authority($AUTH,$PGM[3]);

if( $shipping_doc->ship_rmk($PHP_id,$PHP_remark) ){
	$_SESSION['MSG'][] = $mesg = "SUCCESS APPEND SHIPPING DOCUMENT ON REMARK : [ ".$PHP_remark." ]";
	$log->log_add(0,$AUTH."E",$mesg);
	if($PHP_new == 'new')
	{
		$redirect_str ="shipping.php?PHP_action=ship_invoice_view&PHP_main_page_action=ship_sch_new&PHP_id=".$PHP_id;
		
	}
	else
	{
		$redirect_str ="shipping.php?PHP_action=ship_invoice_view&PHP_id=".$PHP_id;
	}
	
	
} else {
	if($PHP_new == 'new')
	{
		$_SESSION['MSG'][] = $mesg = "ERROR !!!  ON REMARK : [ ".$PHP_remark." ]";
		$redirect_str ="shipping.php?PHP_action=ship_invoice_view&PHP_main_page_action=ship_sch_new&PHP_id=".$PHP_id;
	}
	else
	{
		$_SESSION['MSG'][] = $mesg = "ERROR !!!  ON REMARK : [ ".$PHP_remark." ]";
		$redirect_str ="shipping.php?PHP_action=ship_invoice_view&PHP_id=".$PHP_id;
	}
	
}

redirect_page($redirect_str);




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "tiptop_mm_rpt":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "tiptop_mm_rpt":
check_authority($AUTH,$PGM[1]);

if(!$PHP_year)$PHP_year=date('Y');
// if(!$PHP_month)$PHP_month=date('m');

if(!$PHP_fty) {
	$message = "Please select factory first!";
	$redir_str='shipping.php?PHP_action=main&PHP_msg='.$message;
	redirect_page($redir_str);			
}

$check = $_POST['PHP_check'];

#�վ㵲�b�ɶ��A��201504�}�l�A���e�������Ƥ覡�O��������A201504�q01��25�I��A201505�q20150426~20150525
// $rec = $shipping_doc->get_mm_rpt_tiptop(($PHP_year.'-'.$PHP_month),$PHP_fty,$check);
if( $check == 'cust' ){
$rec = $shipping_doc->get_mm_rpt_cust($PHP_year,$PHP_month,$PHP_fty,$check);
}else{
$rec = $shipping_doc->get_mm_rpt_tiptop($PHP_year,$PHP_month,$PHP_fty,$check);
}

// 

// print_r($_POST);
// print_r($rec);
// exit;

require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

function HeaderingExcel($filename) {
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename" );
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
}

# HTTP headers
if( $check ){
	HeaderingExcel($PHP_fty.'_'.$PHP_year.'_'.$PHP_month.'.xls');
} else if( $check == 'cust' ){
	HeaderingExcel($PHP_fty.'_'.$PHP_year.'_'.$PHP_month.'_cust.xls');
} else {
	HeaderingExcel($PHP_fty.'_'.$PHP_year.'_'.$PHP_month.'_TIPTOP.xls');
}

# Creating a workbook
$workbook = new Workbook("-");

# Creating the first worksheet
$worksheet1 =& $workbook->add_worksheet('report');

$now = $GLOBALS['THIS_TIME'];

# �g�J title
$formatot =& $workbook->add_format();
$formatot->set_size(10);
$formatot->set_align('center');
$formatot->set_color('white');
$formatot->set_pattern(1);
$formatot->set_fg_color('navy');
$formatot->set_num_format(4);

$f3 =& $workbook->add_format(); //�m�k
$f3->set_size(10);
$f3->set_align('right');
$f3->set_num_format(4);

$f5 =& $workbook->add_format();  //�ǩ��զr�m��
$f5->set_size(10);
$f5->set_align('center');
$f5->set_color('white');
$f5->set_pattern(1);
$f5->set_fg_color('grey');

$f6 =& $workbook->add_format();  //�ǩ��զr�m�k
$f6->set_size(10);
$f6->set_color('white');
$f6->set_pattern(1);
$f6->set_align('right');
$f6->set_fg_color('grey');
$f6->set_num_format(4);

$f7 =& $workbook->add_format();  //�񩳥զr�m��
$f7->set_size(10);
$f7->set_align('center');
$f7->set_color('white');
$f7->set_pattern(1);
$f7->set_fg_color('green');

$f8 =& $workbook->add_format();  //�񩳥զr�m�k
$f8->set_size(10);
$f8->set_color('white');
$f8->set_pattern(1);
$f8->set_align('right');
$f8->set_fg_color('green');	 
$f8->set_num_format(4); 
#title

if( $check ){
    if( $check == 'check' ){
	$clumn = array( 12, 66, 12, 12, 10, 10, 10, 10);
        $title = array(
            'Invoice #',	#12
            'Buyer', 		#66
            'SCM #', 		#12
            'PO #', 		#12
            'SHIP Q\'TY', 	#10
            'U/PRICE',		#10
            'AMOUNT',		#10
            'CHARGE'		#10
        );
    } else if( $check == 'cust' ){
        $title = array(
            'Invoice #',	#12
            'Buyer', 		#66
            'SCM #', 		#12
            'PO #', 		#12
            'SHIP Q\'TY', 	#10
            'U/PRICE',		#10
            'AMOUNT',		#10
            'CHARGE',		#10
            'DUE DATE OF PAYMENT', 		#50
            'MERCHANDISER'  #66
        );
    }else{
        $clumn = array( 12, 66, 12, 12, 10, 10, 10, 10, 10);
        $title = array(
            'Invoice #',	#12
            'Buyer', 		#66
            'SCM #', 		#12
            'PO #', 		#12
            'SHIP Q\'TY', 	#10
            'U/PRICE',		#10
            'AMOUNT',		#10
            'CHARGE',		#10
            'SHIP DATE'     #10
        );
    }
	
	for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
	$worksheet1->write_string(0,1,'Summary Monthly Report For The Month Of ( '.$PHP_year.'-'.$PHP_month.' ) '.$PHP_fty.' '.$THIS_TIME );
	for ($i=0; $i< sizeof($title); $i++) $worksheet1->write_string(1,$i,$title[$i],$formatot);

	#���e
	$j=2;
	// echo sizeof($rec);
	for($i=0; $i<sizeof($rec); $i++)
	{

		$worksheet1->write_string($j,0,$rec[$i]['inv_num']);
		$worksheet1->write_string($j,1,$rec[$i]['buyer']);
		$worksheet1->write_string($j,2,$rec[$i]['ord_num']);
		$worksheet1->write_string($j,3,$rec[$i]['cust_po']);
		$worksheet1->write_number($j,4,$rec[$i]['ttl_qty'],$f3);
		$worksheet1->write_number($j,5,$rec[$i]['ship_fob'],$f3);
		$worksheet1->write_number($j,6,$rec[$i]['amount'],$f3);
		$worksheet1->write_number($j,7,$rec[$i]['charge'],$f3);
        if( $check == 'etd' ){
		$worksheet1->write_string($j,8,$rec[$i]['ship_date']);
        }
        if( $check == 'cust' ){
		$worksheet1->write_string($j,8,$rec[$i]['due_date']);
		$worksheet1->write_string($j,8,$rec[$i]['merchandiser']);
        }
		
		$j++;
	}
} else {
	$clumn = array( 6, 10, 9, 9, 12, 9, 7, 9, 9, 15, 5, 5, 6, 12, 9, 10, 10, 5, 8, 8, 10, 10, 11, 12);
	$title = array(
		'��O', 		#6
		'�X�f���', 	#10
		'�b�ګȤ�', 	#9
		'���~�P�O', 	#9
		'���ګȤ�s��', #12
		'�Ȥ�²��',		#9
		'�o���O',		#7
		'��ؤ���',		#9
		'�H���s��',		#9
		'�����s��',		#15
		'�|�O',			#5
		'���O',			#5
		'�ײv',			#6
		'���ڱ���s��',	#12
		'�w�����B',		#9
		'���ڻȦ�',		#10
		'�q��渹',		#10
		'����',			#5
		'�~�P',			#8
		'�ӫ~',			#8
		'�~�W�W��',		#10
		'��ڥX�f�ƶq',	#10
		'������B',		#11
		'��X�f�渹'	#12
	);
	
	for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
	$worksheet1->write_string(0,1,'Tiptop �ץX��� ( '.$PHP_year.'-'.$PHP_month.' ) '.$PHP_fty );
	$worksheet1->write_string(0,10,$THIS_TIME);
	for ($i=0; $i< sizeof($title); $i++) $worksheet1->write_string(1,$i,$title[$i],$formatot);

	#���e
	$j=2;
	// echo sizeof($rec);
	for($i=0; $i<sizeof($rec); $i++)
	{
		// $dept_v = $order->get_field_value('dept','',$rpt[$i]['ord_num'],'s_order');
		// $chief = $dept->get_fields("chief"," where dept_code='$dept_v'");
		// $emp_id = $user->get_fields("emp_id"," where login_id = '$chief[0]'");
		$dept = $apb->get_apb_dept($rec[$i]['dept']);
		# ���� 2012/07/10 �����p��
		// $pnum[$rec[$i]['ord_num'].$rec[$i]['ship_date']][] = $rec[$i]['ship_date']; 
        
		$worksheet1->write_string($j,0,'CR41'); #��O
		$worksheet1->write_string($j,1,$rec[$i]['ship_date']); #�X�f���
		$worksheet1->write_string($j,2,$rec[$i]['code']); #�b�ګȤ�
		$worksheet1->write_number($j,3,'2'); #���~�P�O
		$worksheet1->write_string($j,4,''); #���ګȤ�s��	//$rec[$i]['cust_init_name']
		$worksheet1->write_string($j,5,''); #�Ȥ�²��	//$rec[$i]['cust_s_name']
		$worksheet1->write_number($j,6,'99'); #�o���O
		$worksheet1->write_string($j,7,$PHP_fty=="LY"?'12':'11'); #��ؤ���
		$worksheet1->write_string($j,8,$rec[$i]['emp_id']); #�H���s��
		$worksheet1->write_string($j,9,$dept); #�����s��
		$worksheet1->write_string($j,10,'S299'); #�|�O
		$worksheet1->write_string($j,11,'USD'); #���O
		$worksheet1->write_number($j,12,$rate->get_rate('USD',$rec[$i]['ship_date']),$f3); #�ײv
		$worksheet1->write_string($j,13,'CR0003'); #���ڱ���s��
		$worksheet1->write_string($j,14,'0'); #�w�����B
		$worksheet1->write_number($j,15,'1509'); #���ڻȦ� // �ثn 1509 �x�� ?
		$worksheet1->write_string($j,16,''); #�q��渹	//$rec[$i]['ord_num']
		$worksheet1->write_number($j,17,'1'); #����  2012/07/10 �����p��  count($pnum[$rec[$i]['ord_num'].$rec[$i]['ship_date']])
		$worksheet1->write_string($j,18,( ($PHP_fty=='LY')?'AEJ0000':( ($PHP_fty=='CF')?'AEE0000':$dept) )); #�~�P
		$worksheet1->write_string($j,19,'',$f3); #�ӫ~
		$worksheet1->write_string($j,20,'����'); #�~�W�W��
		$worksheet1->write_number($j,21,$rec[$i]['ttl_qty'],$f3); #��ڥX�f�ƶq
		$amt = $rec[$i]['ttl_amt'] - $rec[$i]['charge'];
		$worksheet1->write_number($j,22,$amt,$f3); #������B
		$worksheet1->write_string($j,23,$rec[$i]['inv_num']); #��X�f�渹
		$j++;
	}
}

$workbook->close();
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "tiptop_mm_rpt":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "new_tiptop_mm_rpt":
check_authority($AUTH,$PGM[1]);


//print_r($_POST);
/* print_r($_POST);
exit; */
if(!$PHP_year)$PHP_year=date('Y');
// if(!$PHP_month)$PHP_month=date('m');

if(!$PHP_fty) {
	$message = "Please select factory first!";
	$redir_str='shipping.php?PHP_action=main&PHP_msg='.$message;
	redirect_page($redir_str);			
}

$check = $_POST['PHP_check'];
$newold = $_POST['PHP_newold'];

#�վ㵲�b�ɶ��A��201504�}�l�A���e�������Ƥ覡�O��������A201504�q01��25�I��A201505�q20150426~20150525
// $rec = $shipping_doc->get_mm_rpt_tiptop(($PHP_year.'-'.$PHP_month),$PHP_fty,$check);

if( $check == 'cust' ){
	$rec = $shipping_doc->get_mm_rpt_cust($PHP_year,$PHP_month,$PHP_fty,$check);
}
else
{
	$rec = $shipping_doc->get_mm_rpt_tiptop($PHP_year,$PHP_month,$PHP_fty,$check,$newold);
}

/* print_r($rec);
exit; */
// 
$hanger_id = 0;
foreach($rec as $rec_key => $rec_val)
{
	if(strtoupper($rec_val['color']) === 'HANGER')
	{
		$hanger_id = $rec_val['id'];
		$cust_po = $rec_val['cust_po'];
		$fob = $rec_val['ship_fob'];
		foreach($rec as $rec1_key => $rec1_val)
		{
			if($hanger_id == $rec1_val['id'] && $cust_po == $rec1_val['cust_po'])
			{
				if(strtoupper($rec1_val['color']) != 'HANGER')
				{
					//echo $rec1_val['inv_num'].':'.$rec1_val['ship_fob'].'*'.$fob.'<br>';
					$rec[$rec1_key]['ship_fob'] = $rec1_val['ship_fob'] + $fob;
					$rec[$rec1_key]['amount'] = ($rec1_val['ship_fob'] + $fob) * $rec1_val['ttl_qty'];
				}
			}
		}
		$hanger_id = 0;
		$cust_po = '';
		$fob = 0;
	}

}

$new_rec = array();
foreach($rec as $rec_key => $rec_val)
{
	if(strtoupper($rec_val['color']) != 'HANGER')
	{
		//unset($rec[$rec_key]);
		$new_rec[] = $rec_val;
	}
}


$rec = $new_rec;
/* print_r($new_rec);
exit; */
require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

function HeaderingExcel($filename) {
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename" );
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
}

# HTTP headers
if( $check ){
	HeaderingExcel($PHP_fty.'_'.$PHP_year.'_'.$PHP_month.'.xls');
} else if( $check == 'cust' ){
	HeaderingExcel($PHP_fty.'_'.$PHP_year.'_'.$PHP_month.'_cust.xls');
} else {
	HeaderingExcel($PHP_fty.'_'.$PHP_year.'_'.$PHP_month.'_TIPTOP.xls');
}

# Creating a workbook
$workbook = new Workbook("-");

# Creating the first worksheet
$worksheet1 =& $workbook->add_worksheet('report');

$now = $GLOBALS['THIS_TIME'];

# �g�J title
$formatot =& $workbook->add_format();
$formatot->set_size(10);
$formatot->set_align('center');
$formatot->set_color('white');
$formatot->set_pattern(1);
$formatot->set_fg_color('navy');
$formatot->set_num_format(4);

$f3 =& $workbook->add_format(); //�m�k
$f3->set_size(10);
$f3->set_align('right');
$f3->set_num_format(4);

$f5 =& $workbook->add_format();  //�ǩ��զr�m��
$f5->set_size(10);
$f5->set_align('center');
$f5->set_color('white');
$f5->set_pattern(1);
$f5->set_fg_color('grey');

$f6 =& $workbook->add_format();  //�ǩ��զr�m�k
$f6->set_size(10);
$f6->set_color('white');
$f6->set_pattern(1);
$f6->set_align('right');
$f6->set_fg_color('grey');
$f6->set_num_format(4);

$f7 =& $workbook->add_format();  //�񩳥զr�m��
$f7->set_size(10);
$f7->set_align('center');
$f7->set_color('white');
$f7->set_pattern(1);
$f7->set_fg_color('green');

$f8 =& $workbook->add_format();  //�񩳥զr�m�k
$f8->set_size(10);
$f8->set_color('white');
$f8->set_pattern(1);
$f8->set_align('right');
$f8->set_fg_color('green');	 
$f8->set_num_format(4); 
#title

if( $check ){
    if( $check == 'check' ){
	$clumn = array( 12, 66, 12, 12, 10, 10, 10, 10);
        $title = array(
            'Invoice #',	#12
            'Buyer', 		#66
            'SCM #', 		#12
            'PO #', 		#12
            'SHIP Q\'TY', 	#10
            'U/PRICE',		#10
            'AMOUNT',		#10
            'CHARGE'		#10
        );
    } else if( $check == 'cust' ){
        $title = array(
            'Invoice #',	#12
            'Buyer', 		#66
            'SCM #', 		#12
            'PO #', 		#12
            'SHIP Q\'TY', 	#10
            'U/PRICE',		#10
            'AMOUNT',		#10
            'CHARGE',		#10
            'DUE DATE OF PAYMENT', 		#50
            'MERCHANDISER'  #66
        );
    }else{
        $clumn = array( 12, 66, 12, 12, 10, 10, 10, 10, 10);
        $title = array(
            'Invoice #',	#12
            'Buyer', 		#66
            'SCM #', 		#12
            'PO #', 		#12
            'SHIP Q\'TY', 	#10
            'U/PRICE',		#10
            'AMOUNT',		#10
            'CHARGE',		#10
            'SHIP DATE'     #10
        );
    }
	
	for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
	$worksheet1->write_string(0,1,'Summary Monthly Report For The Month Of ( '.$PHP_year.'-'.$PHP_month.' ) '.$PHP_fty.' '.$THIS_TIME );
	for ($i=0; $i< sizeof($title); $i++) $worksheet1->write_string(1,$i,$title[$i],$formatot);

	#���e
	$j=2;
	// echo sizeof($rec);
	for($i=0; $i<sizeof($rec); $i++)
	{

		$worksheet1->write_string($j,0,$rec[$i]['inv_num']);
		$worksheet1->write_string($j,1,$rec[$i]['buyer']);
		$worksheet1->write_string($j,2,$rec[$i]['ord_num']);
		$worksheet1->write_string($j,3,$rec[$i]['cust_po']);
		$worksheet1->write_number($j,4,$rec[$i]['ttl_qty'],$f3);
		$worksheet1->write_number($j,5,$rec[$i]['ship_fob'],$f3);
		$worksheet1->write_number($j,6,$rec[$i]['amount'],$f3);
		$worksheet1->write_number($j,7,$rec[$i]['charge'],$f3);
        if( $check == 'etd' ){
		$worksheet1->write_string($j,8,$rec[$i]['ship_date']);
        }
        if( $check == 'cust' ){
		$worksheet1->write_string($j,8,$rec[$i]['due_date']);
		$worksheet1->write_string($j,8,$rec[$i]['merchandiser']);
        }
		
		$j++;
	}
} else {
	$clumn = array( 6, 10, 9, 9, 12, 9, 7, 9, 9, 15, 5, 5, 6, 12, 9, 10, 10, 5, 8, 8, 10, 10, 11, 12);
	$title = array(
		'��O', 		#6
		'�X�f���', 	#10
		'�b�ګȤ�', 	#9
		'���~�P�O', 	#9
		'���ګȤ�s��', #12
		'�Ȥ�²��',		#9
		'�o���O',		#7
		'��ؤ���',		#9
		'�H���s��',		#9
		'�����s��',		#15
		'�|�O',			#5
		'���O',			#5
		'�ײv',			#6
		'���ڱ���s��',	#12
		'�w�����B',		#9
		'���ڻȦ�',		#10
		'�q��渹',		#10
		'����',			#5
		'�~�P',			#8
		'�ӫ~',			#8
		'�~�W�W��',		#10
		'��ڥX�f�ƶq',	#10
		'������B',		#11
		'��X�f�渹'	#12
	);
	
	for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
	$worksheet1->write_string(0,1,'Tiptop �ץX��� ( '.$PHP_year.'-'.$PHP_month.' ) '.$PHP_fty );
	$worksheet1->write_string(0,10,$THIS_TIME);
	for ($i=0; $i< sizeof($title); $i++) $worksheet1->write_string(1,$i,$title[$i],$formatot);

	#���e
	$j=2;
	// echo sizeof($rec);
	for($i=0; $i<sizeof($rec); $i++)
	{
		// $dept_v = $order->get_field_value('dept','',$rpt[$i]['ord_num'],'s_order');
		// $chief = $dept->get_fields("chief"," where dept_code='$dept_v'");
		// $emp_id = $user->get_fields("emp_id"," where login_id = '$chief[0]'");
		$dept = $apb->get_apb_dept($rec[$i]['dept']);
		# ���� 2012/07/10 �����p��
		// $pnum[$rec[$i]['ord_num'].$rec[$i]['ship_date']][] = $rec[$i]['ship_date']; 
        
		$worksheet1->write_string($j,0,'CR41'); #��O
		$worksheet1->write_string($j,1,$rec[$i]['ship_date']); #�X�f���
		$worksheet1->write_string($j,2,$rec[$i]['code']); #�b�ګȤ�
		$worksheet1->write_number($j,3,'2'); #���~�P�O
		$worksheet1->write_string($j,4,''); #���ګȤ�s��	//$rec[$i]['cust_init_name']
		$worksheet1->write_string($j,5,''); #�Ȥ�²��	//$rec[$i]['cust_s_name']
		$worksheet1->write_number($j,6,'99'); #�o���O
		$worksheet1->write_string($j,7,$PHP_fty=="LY"?'12':'11'); #��ؤ���
		$worksheet1->write_string($j,8,$rec[$i]['emp_id']); #�H���s��
		$worksheet1->write_string($j,9,$dept); #�����s��
		$worksheet1->write_string($j,10,'S299'); #�|�O
		$worksheet1->write_string($j,11,'USD'); #���O
		$worksheet1->write_number($j,12,$rate->get_rate('USD',$rec[$i]['ship_date']),$f3); #�ײv
		$worksheet1->write_string($j,13,'CR0003'); #���ڱ���s��
		$worksheet1->write_string($j,14,'0'); #�w�����B
		$worksheet1->write_number($j,15,'1509'); #���ڻȦ� // �ثn 1509 �x�� ?
		$worksheet1->write_string($j,16,''); #�q��渹	//$rec[$i]['ord_num']
		$worksheet1->write_number($j,17,'1'); #����  2012/07/10 �����p��  count($pnum[$rec[$i]['ord_num'].$rec[$i]['ship_date']])
		$worksheet1->write_string($j,18,( ($PHP_fty=='LY')?'AEJ0000':( ($PHP_fty=='CF')?'AEE0000':$dept) )); #�~�P
		$worksheet1->write_string($j,19,'',$f3); #�ӫ~
		$worksheet1->write_string($j,20,'����'); #�~�W�W��
		$worksheet1->write_number($j,21,$rec[$i]['ttl_qty'],$f3); #��ڥX�f�ƶq
		$amt = $rec[$i]['ttl_amt'] - $rec[$i]['charge'];
		$worksheet1->write_number($j,22,$amt,$f3); #������B
		$worksheet1->write_string($j,23,$rec[$i]['inv_num']); #��X�f�渹
		$j++;
	}
}

$workbook->close();
break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "Invoice PDF Output":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "inv_pdf_out":

	include_once($config['root_dir']."/lib/class.pdf_invoice.php");//���F�n�ʺA�P�_�O�_���C��A�n��initial
	//print_r($_POST);
	//exit;
	$t_sql = 'select invoice_import.inv_no,';
	$t_sql .= 'shipping_doc.cust, ';
	$t_sql .= 'shipping_doc.factory,';
	$t_sql .= 'consignee.f_name,';
	$t_sql .= 'consignee.addr,';
	$t_sql .= 'invoice_import.po,';
	$t_sql .= 'invoice_import.ord_num,';
	$t_sql .= 'invoice_import.style,';
	
	$t_sql .= 'invoice_import.color,';
	$t_sql .= 'invoice_import.size,';
	$t_sql .= 'invoice_import.qty,';
	$t_sql .= 'invoice_import.fob,';
	$t_sql .= 'invoice_import.amount,';
	$t_sql .= 'invoice_import.ship_date ';
	$t_sql .= 'from invoice_import ';
	$t_sql .= 'left join shipping_doc on shipping_doc.id = invoice_import.shipping_doc_id ';
	$t_sql .= 'left join consignee on consignee.id = shipping_doc.consignee_id ';
	$t_sql .= 'where shipping_doc.inv_num="';
	$t_sql .= $_POST['PHP_invoice'];
	$t_sql .= '" ';
	$t_sql .= 'order by invoice_import.inv_no,invoice_import.po,invoice_import.ord_num,invoice_import.style,invoice_import.color';
	/* echo $t_sql;
	exit; */
	$inv_info = $shipping_doc->get_inv_info($t_sql);//����Invoice������T
	/* print_r($inv_info);
	exit;  */
	$inv_info_new = array();
	$inv_no='';
	$cust='';
	$f_name='';
	$addr='';
	$shipdate='';
	$po='';
	$style='';
	$color='';
	$size='';
	$qty=0;
	$amount=0;
	$ttl_qty=0;
	$ttl_amount=0;
	$fob = 0;
	//print_r($inv_info);
	//exit;
	foreach($inv_info as $inv_key => $inv_val)
	{
		if($inv_no == '')
		{
			$inv_no = $inv_val['inv_no'];
			$cust = $inv_val['cust'];
			$f_name = $inv_val['f_name'];
			//$addr = iconv('big5','utf-8',$inv_val['addr']);
			$addr = $inv_val['addr'];
			$shipdate = $inv_val['ship_date'];
			$factory = $inv_val['factory'];
			$po = $inv_val['po'];
			$style = $inv_val['style'];
			$color = $inv_val['color'];
			$size = $inv_val['size'];
			$fob = $inv_val['fob'];
		}
		if($po == $inv_val['po'])
		{
			//PO�ۦP
			if($style == $inv_val['style'])
			{
				//PO�ۦP�A�ڸ��ۦP
				if($color == $inv_val['color'])
				{
					//PO�ۦP�A�ڸ��ۦP�Acolor�ۦP
					if($ary_title == '' )
					{
						if($inv_val['color'] == 'nocolor')
						{
							$ary_title='nocolor';
						}
					}
					if($size == $inv_val['size'])
					{
						//PO�ۦP�A�ڸ��ۦP�Acolor�ۦP�Asize�ۦP
						if($ary_title == '' )
						{
							if($inv_val['size'] == 'nosize')
							{
								$ary_title='nosize';
							}
						}
						$qty = $qty + $inv_val['qty'];
						if(strtoupper($inv_val['color']) != 'HANGER')
						{
							$ttl_qty = $ttl_qty + $inv_val['qty'];
						}
					}
					else
					{
						//PO�ۦP�A�ڸ��ۦP�Acolor�ۦP�Asize���P
						$amount = $qty * $fob;
						$ttl_amount = $ttl_amount + $amount;
						if(strtoupper($inv_val['color']) != 'HANGER')
						{
							$ttl_qty = $ttl_qty + $inv_val['qty'];
						}
						$inv_info_new[$inv_no][$po][$style][$color][$size]['qty'] = $qty;
						$inv_info_new[$inv_no][$po][$style][$color][$size]['fob'] = $fob;
						$inv_info_new[$inv_no][$po][$style][$color][$size]['amount'] = $amount;
						$qty = $inv_val['qty'];
						$amount = 0;
						
						$po = $inv_val['po'];
						$style = $inv_val['style'];
						$color = $inv_val['color'];
						$size = $inv_val['size'];
						$fob = $inv_val['fob'];
						//$ttl_qty = 0;
					}
				}
				else
				{
					$amount = $qty * $fob;
					$ttl_amount = $ttl_amount + $amount;
					if(strtoupper($inv_val['color']) != 'HANGER')
					{
						$ttl_qty = $ttl_qty + $inv_val['qty'];
					}
					$inv_info_new[$inv_no][$po][$style][$color][$size]['qty'] = $qty;
					$inv_info_new[$inv_no][$po][$style][$color][$size]['fob'] = $fob;
					$inv_info_new[$inv_no][$po][$style][$color][$size]['amount'] = $amount;
					$qty = $inv_val['qty'];
					$amount = 0;
					
					$po = $inv_val['po'];
					$style = $inv_val['style'];
					$color = $inv_val['color'];
					$size = $inv_val['size'];
					$fob = $inv_val['fob'];
					
				}
				
			}
			else
			{
				//PO�ۦP�A�ڸ����P
				$amount = $qty * $fob;
				$ttl_amount = $ttl_amount + $amount;
				if(strtoupper($inv_val['color']) != 'HANGER')
				{
					$ttl_qty = $ttl_qty + $inv_val['qty'];
				}
				$inv_info_new[$inv_no][$po][$style][$color][$size]['qty'] = $qty;
				$inv_info_new[$inv_no][$po][$style][$color][$size]['fob'] = $fob;
				$inv_info_new[$inv_no][$po][$style][$color][$size]['amount'] = $amount;
				$qty = $inv_val['qty'];
				$amount = 0;
					
				$po = $inv_val['po'];
				$style = $inv_val['style'];
				$color = $inv_val['color'];
				$size = $inv_val['size'];
				$fob = $inv_val['fob'];
			}
		}
		else
		{
			//PO���P
			$amount = $qty * $fob;
			$ttl_amount = $ttl_amount + $amount;
			if(strtoupper($inv_val['color']) != 'HANGER')
			{
				$ttl_qty = $ttl_qty + $inv_val['qty'];
			}
			$inv_info_new[$inv_no][$po][$style][$color][$size]['qty'] = $qty;
			$inv_info_new[$inv_no][$po][$style][$color][$size]['fob'] = $fob;
			$inv_info_new[$inv_no][$po][$style][$color][$size]['amount'] = $amount;
			$qty = $inv_val['qty'];
			$amount = 0;
					
			$po = $inv_val['po'];
			$style = $inv_val['style'];
			$color = $inv_val['color'];
			$size = $inv_val['size'];
			$fob = $inv_val['fob'];
			
		}
		if($inv_key == (sizeof($inv_info)-1))
		{
			//�}�C�̫�@��
			$amount = $qty * $fob;
			$ttl_amount = $ttl_amount + $amount;

			$inv_info_new[$inv_no][$po][$style][$color][$size]['qty'] = $qty;
			$inv_info_new[$inv_no][$po][$style][$color][$size]['fob'] = $fob;
			$inv_info_new[$inv_no][$po][$style][$color][$size]['amount'] = $amount;

			$qty = 0;
			$amount = 0;

			$po = '';
			$style = '';
			$color = '';
			$size = '';
			$fob = 0;
		}
		
	}
	
	$op['inv_no'] = $inv_no;
	$op['cust'] = $cust;
	$op['f_name'] = $f_name;
	$op['addr'] = $addr;
	$op['shipdate'] = $shipdate;
	$op['inv_info'] = $inv_info_new;
	$op['factory'] = $factory;
	$op['ttl_qty'] = $ttl_qty;
	$op['ttl_amount'] = $ttl_amount;

	//�U���}�l��PDF

	$print_title = "INVOICE";
	$print_inv = $op['inv_no'] ;
	$print_consignee = $op['f_name'] ;
	$print_address = $op['addr'] ;
	$print_shipdate = $op['shipdate'];
	//print_r($op);
	/* echo $ary_title;
	exit; */
	switch($factory)
	{
		case 'CF':
			$print_factory = 'PALMPHIL GARMENTS PHILIPPINE CORP.';
		break;
		case 'LY':
			$print_factory = 'LI YUEN GARMENT CO., LTD.';
		break;
	}
	$print_title3 = "�C�L����G".date("Y-m-d H:i:s");
	$mark = $inv_no;
	//$ary_title//�ΨӧP�_�O�_���C�⦳�ؽX�A�A�ӽT�{PDF�O�_�n�C�X
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);

	$pdf->ln();
	

	$pdf->SetFont('Arial','',10);
	//print_r($op["inv_info"]);
	//exit;
	$tmp_inv_no='';
	$tmp_po='';
	$tmp_style='';
	$tmp_color='';
	foreach($inv_info_new as $key_inv => $val_inv) {

			$height = 6;
			foreach($val_inv as $key_po => $val_po)
			{
				if ( ( $pdf->getY() + 6 ) >= 270){
					$tmp_inv_no='';
					$tmp_po='';
					$tmp_style='';
					$tmp_color='';
					$pdf->AddPage();
					$pdf->ln();
				}
				foreach($val_po as $key_style => $val_style)
				{
					if($ary_title == '')
					{
						foreach($val_style as $key_color => $val_color)
						{
							foreach($val_color as $key_size => $val_size)
							{
								if($tmp_inv_no != $key_inv)
								{
									$tmp_inv_no = $key_inv;
									$pdf->Cell(20,$height,$key_inv,0,0,'C');
								}
								else
								{
									if($tmp_po != $key_po)
									{
										$pdf->Cell(20,$height,$key_inv,0,0,'C');
									}
									else
									{
										$pdf->Cell(20,$height,'',0,0,'C');
									}
									
								}
								if($tmp_po != $key_po)
								{
									$tmp_po = $key_po;
									$pdf->Cell(20,$height,$key_po,0,0,'C');
								}
								else
								{
									$pdf->Cell(20,$height,'',0,0,'C');
								}
								if($tmp_style != $key_style)
								{
									$tmp_style = $key_style;
									$pdf->Cell(30,$height,$key_style,0,0,'C');
								}
								else
								{
									$pdf->Cell(30,$height,'',0,0,'C');
								}
								if($tmp_color != $key_color)
								{
									if(strtoupper($key_color) == 'HANGER')
									{
										//$pdf->Cell(40,$height,'',0,0,'C');
										$pdf->Cell(40,$height,$key_color,0,0,'L');
									}
									else
									{
										$tmp_color = $key_color;
										$pdf->Cell(40,$height,$key_color,0,0,'L');
									}
								}
								else
								{
									$pdf->Cell(40,$height,'',0,0,'C');
								}
								$pdf->Cell(20,$height,$key_size,0,0,'C');
								if(strtoupper($key_color) == 'HANGER')
								{
									$pdf->Cell(20,$height,'('.$val_size['qty'].' PCS)',0,0,'L');
								}
								else
								{
									$pdf->Cell(20,$height,$val_size['qty'].' PCS',0,0,'L');
								}
								$pdf->Cell(20,$height,'US$ '.$val_size['fob'],0,0,'L');
								$pdf->Cell(25,$height,'US$ '.$val_size['amount'],0,0,'L');
								$pdf->ln();
							}
						}
					}
					else
					{
						if($ary_title == 'nocolor')
						{
							if($tmp_inv_no != $key_inv)
							{
								$tmp_inv_no = $key_inv;
								$pdf->Cell(20,$height,$key_inv,0,0,'C');
							}
							else
							{
								$pdf->Cell(20,$height,'',0,0,'C');
							}
							//$pdf->Cell(20,$height,$key_inv,0,0,'C');
							if($tmp_po != $key_po)
							{
								$tmp_po = $key_po;
								$pdf->Cell(30,$height,$key_po,0,0,'C');
							}
							else
							{
								$pdf->Cell(30,$height,'',0,0,'C');
							}
							//$pdf->Cell(20,$height,$key_po,0,0,'C');
							if($tmp_style != $key_style)
							{
								$tmp_style = $key_style;
								$pdf->Cell(50,$height,$key_style,0,0,'C');
							}
							else
							{
								$pdf->Cell(50,$height,'',0,0,'C');
							}
							//$pdf->Cell(30,$height,$key_style,0,0,'C');
							$pdf->Cell(20,$height,$val_style['nocolor']['nosize']['qty'].' PCS',0,0,'L');
							$pdf->Cell(20,$height,'US$ '.$val_style['nocolor']['nosize']['fob'],0,0,'L');
							$pdf->Cell(25,$height,'US$ '.$val_style['nocolor']['nosize']['amount'],0,0,'L');
							$pdf->ln();							
						}
						if($ary_title == 'nosize')
						{
							foreach($val_style as $key_color => $val_color)
							{
								if($tmp_inv_no != $key_inv)
								{
									$tmp_inv_no = $key_inv;
									$pdf->Cell(20,$height,$key_inv,0,0,'C');
								}
								else
								{
									$pdf->Cell(20,$height,'',0,0,'C');
								}
								//$pdf->Cell(20,$height,$key_inv,0,0,'C');
								if($tmp_po != $key_po)
								{
									$tmp_po = $key_po;
									$pdf->Cell(20,$height,$key_po,0,0,'C');
								}
								else
								{
									$pdf->Cell(20,$height,'',0,0,'C');
								}
								//$pdf->Cell(20,$height,$key_po,0,0,'C');
								if($tmp_style != $key_style)
								{
									$tmp_style = $key_style;
									$pdf->Cell(30,$height,$key_style,0,0,'C');
								}
								else
								{
									$pdf->Cell(30,$height,'',0,0,'C');
								}
								//$pdf->Cell(30,$height,$key_style,0,0,'C');
								if($tmp_color != $key_color)
								{
									if(strtoupper($key_color) == 'HANGER')
									{
										//$pdf->Cell(40,$height,'',0,0,'C');
										$pdf->Cell(40,$height,$key_color,0,0,'L');
									}
									else
									{
										$tmp_color = $key_color;
										$pdf->Cell(40,$height,$key_color,0,0,'L');
									}
								}
								else
								{
									$pdf->Cell(40,$height,'',0,0,'C');
								}
								//$pdf->Cell(40,$height,$key_color,0,0,'C');
								if(strtoupper($key_color) == 'HANGER')
								{
									$pdf->Cell(20,$height,'('.$val_color['nosize']['qty'].' PCS)',0,0,'L');
								}
								else
								{
									$pdf->Cell(20,$height,$val_color['nosize']['qty'].' PCS',0,0,'L');
								}
								$pdf->Cell(20,$height,'US$ '.$val_color['nosize']['fob'],0,0,'L');
								$pdf->Cell(25,$height,'US$ '.$val_color['nosize']['amount'],0,0,'L');
								$pdf->ln();
							}
						}
					}
				}
		
			} 
			//$pdf->ln();
			
		
	}
	
	if(empty($op["inv_info"])){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	if ( ( $pdf->getY() + 6 ) >= 270 )
	{
		$pdf->AddPage();
		$pdf->ln();
	}
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0,0,0);
	$pdf->Line(10,$pdf->getY(),194,$pdf->getY());
	$pdf->ln();
	
	
	
	
	if($ary_title == '')
	{
		//�C��ؽX���L
		$pdf->Cell(20,$height,'',0,0,'C');
		$pdf->Cell(20,$height,'',0,0,'C');
		$pdf->Cell(30,$height,'',0,0,'C');
		$pdf->Cell(40,$height,'',0,0,'C');
		$pdf->Cell(20,$height,'TOTAL :',0,0,'R');
		$pdf->Cell(20,$height,$ttl_qty.' PCS',0,0,'L');
		$pdf->Cell(20,$height,'',0,0,'C');
		$pdf->Cell(25,$height,'US$ '.$ttl_amount,0,0,'L');
		$pdf->ln();

	}
	else
	{
		if($ary_title == 'nocolor')
		{
			//���L�C��ؽX
			$pdf->Cell(20,$height,'',0,0,'C');
			$pdf->Cell(30,$height,'',0,0,'C');
			$pdf->Cell(50,$height,'TOTAL :',0,0,'R');
			$pdf->Cell(20,$height,$ttl_qty.' PCS',0,0,'L');
			$pdf->Cell(20,$height,'',0,0,'C');
			$pdf->Cell(25,$height,'US$ '.$ttl_amount,0,0,'L');
			$pdf->ln();						
		}
		if($ary_title == 'nosize')
		{
			//�u�L�C�⤣�L�ؽX
			$pdf->Cell(20,$height,'',0,0,'C');
			$pdf->Cell(20,$height,'',0,0,'C');
			$pdf->Cell(30,$height,'',0,0,'C');
			$pdf->Cell(40,$height,'TOTAL :',0,0,'R');
			$pdf->Cell(20,$height,$ttl_qty.' PCS',0,0,'L');
			$pdf->Cell(20,$height,'',0,0,'C');
			$pdf->Cell(25,$height,'US$ '.$ttl_amount,0,0,'L');
			$pdf->ln();
			/* foreach($val_style as $key_color => $val_color)
			{
				$pdf->Cell(20,$height,'',0,0,'C');
				$pdf->Cell(20,$height,'',0,0,'C');
				$pdf->Cell(30,$height,'',0,0,'C');
				$pdf->Cell(40,$height,'TOTAL :',0,0,'R');
				$pdf->Cell(20,$height,$ttl_qty.' PCS',0,0,'L');
				$pdf->Cell(20,$height,'',0,0,'C');
				$pdf->Cell(25,$height,'US$ '.$ttl_amount,0,0,'L');
				$pdf->ln();

			} */
		}
	}

	
	//$pdf->Cell(40,$height,$GLOBALS['SCACHE']['ADMIN']['login_id'],0,0,'C');
	$pdf->ln();
	if ( ( $pdf->getY() + 10 ) >= 180 )
	{
		$pdf->AddPage();
		$pdf->ln();
	} 
	//�Ȧ��T
	$pdf->Cell(200,$height,'SAY TOTAL U.S. DOLLARS',0,0,'L');
	$pdf->ln();
	$pdf->Cell(200,$height,umoney($ttl_amount),0,0,'L');
	$pdf->ln();
	$pdf->ln();
	$pdf->Cell(20,$height,'Beneficiary :',0,0,'R');
	$pdf->Cell(100,$height,' CARNIVAL INDUSTRIAL CORP',0,0,'L');
	$pdf->ln();
	$pdf->Cell(20,$height,'Advising Bank :',0,0,'R');
	$pdf->Cell(100,$height,' HUA NAN COMMERCIAL BANK,LTD.JEN AI ROAD BRANCH ',0,0,'L');
	$pdf->ln();
	$pdf->Cell(20,$height,'',0,0,'R');
	$pdf->Cell(100,$height,' NO.25,SEC 4,JEN AI ROAD,TAIPEI TAIWAN R.O.C',0,0,'L');
	$pdf->ln();
	$pdf->Cell(20,$height,'SWIFT CODE :',0,0,'R');
	$pdf->Cell(100,$height,' HNBKTWTP111',0,0,'L');
	$pdf->ln();
	$pdf->Cell(20,$height,'Account :',0,0,'R');
	$pdf->Cell(100,$height,' 111-51-000150-9',0,0,'L');
	$pdf->ln();
	$pdf->ln();
	//�u�t��T
	if($factory == 'CF')
	{
		$pdf->Cell(30,$height,'MANUFACTURER :',0,0,'R');
		$pdf->ln();
		$pdf->Cell(20,$height,'MID CODE :',0,0,'R');
		$pdf->Cell(100,$height,' PHPALGARCAB',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' PALMPHIL GARMENTS PHILIPPINE CORPORATION',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' BINARY ST., LIGHT INDUSTRY AND SCIENCE PARK 1,',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' DIEZMO, CABUYAO, LAGUNA, PHILIPPINES',0,0,'L');
		$pdf->ln();
	}
	if($factory == 'LY')
	{
		$pdf->Cell(30,$height,'MANUFACTURER :',0,0,'R');
		$pdf->ln();
		$pdf->Cell(20,$height,'MID CODE :',0,0,'R');
		$pdf->Cell(100,$height,' VNLIYUETAY',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' LI YUEN GARMENT CO., LTD',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' TRANG BANG INDUSTRIAL PARK,',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' ANTINH VILLAGE,',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' TRANG BANG DIST,',0,0,'L');
		$pdf->ln();
		$pdf->Cell(20,$height,'',0,0,'R');
		$pdf->Cell(100,$height,' TAY NINH PROVINCE, VIETNAM,',0,0,'L');
		$pdf->ln();
	}
	//�`�g�zñ�W��
	//$w =$pdf->GetStringWidth($print_title) +30;
	$pdf->Image('./images/president.png',120,$pdf->getY(),62);	//120�OX�b,$pdf->getY()�OY�b,62�O�ϫ��j�p
	$pdf->Output("INVOICE_".$inv_no.".pdf",'D');
	
//-------------------------------------------------------------------------

}   // end case ---------

?>
