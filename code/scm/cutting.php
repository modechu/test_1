<?php
session_start();
session_register('sch_parm');

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

include_once($config['root_dir']."/lib/pdtion.class.php");
$pdtion = new PDTION();
if (!$pdtion->init($mysql,"log")) { print "error!! cannot initialize database for pdtion class"; exit; }
include_once($config['root_dir']."/lib/pdt_finish.class.php");
$pdt_finish = new PDT_FINISH();
if (!$pdt_finish->init($mysql,"log")) { print "error!! cannot initialize database for pdt_finish class"; exit; }

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];

//�t��Message�R���q
$tmp = $para->get(0,'msg_del');
$msg_del = $tmp['set_value'];

//�t�δ��ܰѼƨ��o
session_register	('pre_su');
$para_cm = $para->get(0,'top_su');
$pre_su['t'] = $para_cm['set_value'];
$para_cm = $para->get(0,'button_su');
$pre_su['b'] = $para_cm['set_value'];

$op = array();

//$ACC = $acc->get_acc_name();
$AUTH = '027';
// echo $PHP_action;
switch ($PHP_action) {

//=======================================================
//----------------------------------------------------------------
//			job 11  cust
//----------------------------------------------------------------
case "cutting":
check_authority($AUTH,"view");

//---------------------------------- �P�_�O�_�u�t�H���i�J ---------------
$sch_parm = array();
$where_str = $manager = $dept_id = $fty_select ='';
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �q�w�i�J����������

for ($i=0; $i<count($GLOBALS['FACTORY']);$i++){
	if($user_dept == $GLOBALS['FACTORY'][$i]){
		$dept_id = $GLOBALS['FACTORY'][$i];   // �p�G�O�u�t�����H���i�J dept_id ���w�Ӥu�t---
	}
}

if (!$dept_id) {    // ���O�u�t�H���i�J��
	$manager = 1;
	$fty_select = $arry2->select($FACTORY,"","PHP_factory","select","");  //�u�t select���
} else {
	$where_str = " WHERE factory = '".$dept_id."' ";
}

$op['manager_flag'] = $manager;
$op['fty_id'] = $dept_id;
$op['fty_select'] = $fty_select;

//---------------------------------- �P�_�O�_�u�t�H���i�J ---------------

$op['msg'] = $order->msg->get(2);		
// creat cust combo box

$where_str ="";

// ���X �����Ȥ�N��
$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
for ($i=0; $i< sizeof($cust_def); $i++)
{
	$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
}

$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

page_display($op, $AUTH, $TPL_CUTTING);	
break;



//----------------------------------------------------------------
//			job 11  cust
//----------------------------------------------------------------
case "cutting_search":
check_authority($AUTH,"view");

// �q���ƦC��
if(isset($PHP_order_num))
{			
	$sch_parm = array(	
		"PHP_order_num"		=>  $PHP_order_num,
		"PHP_cust"			=>	$PHP_cust,
		"PHP_ref"			=>	$PHP_ref,
		"PHP_factory"		=>	$PHP_factory,
		"PHP_sr_startno"	=>	$PHP_sr_startno,
		"PHP_finish"	    =>	$PHP_finish,
		"PHP_action"		=>	$PHP_action
	);
} else {
	if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
}

$PHP_order_num 		=   $sch_parm['PHP_order_num'];
$PHP_cust 			=	$sch_parm['PHP_cust'];
$PHP_ref 			=	$sch_parm['PHP_ref'];
$PHP_factory 		=	$sch_parm['PHP_factory'];
$PHP_sr_startno 	=	$sch_parm['PHP_sr_startno'];
$PHP_finish 	    =	$sch_parm['PHP_finish'];

//  �� ���X���� ���X�O���C��-------------+++++++++++++
$argv = array(
	"ord_num"	=>  $PHP_order_num,
	"k_date"	=>	$TODAY,
	"factory"	=>	$PHP_factory
);

//  �� ���X���� ���X�O���C��-------------+++++++++++++
$pd['daily'] = $cutting->search($argv,1);

if (!$op = $cutting->ord_search(2)) {   // �Ʋ��o�{�� ��mode =2 �Y�[�Jstatus>=7
	$op['msg']= $cutting->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// ���s�������q by mode 2011/12/26
foreach($op['sorder'] as $key => $val){
	$op['sorder'][$key]['qty_cut'] = $GLOBALS['cutting']->ord_cut_sum($val['order_num']);
}

// �ഫ�� html �� ���Ѽ�
$op['pdt'] = $pd['daily'];
$op['daily_records'] = count($op['pdt']);
$op['finish'] = $PHP_finish;
$op['order_num'] = $PHP_order_num;

$op['total_ord_qty'] = $op['total_cut_qty'] = $op['today_ord_qty'] = $op['today_cut_qty'] = $op['today_su']= 0;
$entry = count($op['sorder']);

for($i=0;$i<$entry;$i++){  // �p�� ����Ͳ��`�ƶq
	$op['total_ord_qty'] =	$op['total_ord_qty'] + $op['sorder'][$i]['p_qty'];
	$op['total_cut_qty'] =	$op['total_cut_qty'] + $op['sorder'][$i]['cut_qty'];
}
$entry = count($op['pdt']);

for($i=0;$i<$entry;$i++){  // �p�� ����Ͳ��`�ƶq
	// $op['today_ord_qty'] =	$op['today_ord_qty'] + $op['pdt'][$i]['p_qty'];
	$op['today_cut_qty'] =	$op['today_cut_qty'] + $op['pdt'][$i]['qty'];
	$op['today_su'] =	$op['today_su'] + $op['pdt'][$i]['su'];
}

$op['msg']= $order->msg->get(2);

page_display($op, $AUTH, $TPL_CUTTING_LIST);
break;



//=======================================================
case "add_cutting":
check_authority($AUTH,"add");

$today = $GLOBALS['TODAY'];
$parm = array(	
	"ord_num"	=>  $PHP_ord_num,									
	"qty"       =>  $PHP_qty,
	"k_date"	=>  $today,
	"factory"	=>  $PHP_factory,
	"p_id"	    =>  $PHP_p_id,
);

$redir_str = "cutting.php?PHP_action=cutting_search";
if (!$cutting->check($parm)) {
	$op['msg'] = $cutting->msg->get(2);
	redirect_page($redir_str);
	break;
}

$ie1 = $order->get_field_value('ie1','',$PHP_ord_num);
$ie2 = $order->get_field_value('ie2','',$PHP_ord_num);
$ie = $ie2 > 0 ? $ie2 : $ie1 ;
$su = set_su($ie,$PHP_qty);
$parm['su'] = $su;


// *********  �g�J cutting table *********************
if(!$F = $cutting->add($parm)){   // �s�W daily ��Ʈw
	$op['msg']= $cutting->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

# `order_partial`   ord_num cut_qty cut_su
$order->mdf_cut($parm,$ie);

# `pdtion`          order_num qty_cut
$qty = $pdtion->mdf_cut($parm);

# `pdt_finish`      order_num cutting cut_su
$pdt_finish->mdf_cut($parm,$ie,$qty);

# �O���ϥΪ̰ʺA
$message = "Add [ ".$PHP_ord_num." ] in CUTTING Q'ty:[".$PHP_qty."] to FTY:[".$parm['factory']."]";

$log->log_add(0,$AUTH."A",$message);

redirect_page($redir_str);
break;



//=======================================================
case "del_cutting":
check_authority($AUTH,"add");

$today = $GLOBALS['TODAY'];

$parm = array(	
	"ord_num"	=>  $PHP_ord_num,
    "p_id"	    =>  $PHP_p_id,
);

$redir_str = "cutting.php?PHP_action=cutting_search";
// *********  �R�� cutting table *********************
if(!$F = $cutting->del($PHP_daily_id)){  
	$op['msg']= $cutting->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$ie1 = $order->get_field_value('ie1','',$PHP_ord_num);
$ie2 = $order->get_field_value('ie2','',$PHP_ord_num);
$ie = $ie2 > 0 ? $ie2 : $ie1 ;

# `order_partial`   ord_num cut_qty cut_su
$order->mdf_cut($parm,$ie);

# `pdtion`          order_num qty_cut
$qty = $pdtion->mdf_cut($parm);

# `pdt_finish`      order_num cutting cut_su
$pdt_finish->mdf_cut($parm,$ie,$qty);


# �O���ϥΪ̰ʺA
$message = "delete cutting [ ".$PHP_ord_num." ] in Q'ty:[".$PHP_qty."] from FTY:[".$PHP_fty."]";

$log->log_add(0,$AUTH."D",$message);
redirect_page($redir_str);
break;
  
  
  
 //----------------------------------------------------------------
 //			job 11  cust
 //----------------------------------------------------------------
case "cutting_daily_search":
check_authority($AUTH,"view");		
// �q���ƦC��

if(isset($SCH_str))
{			
$sch_parm = array();
	$sch_parm = array(	
		"SCH_str"           =>  $SCH_str,
		"SCH_end"           =>	$SCH_end,
		"PHP_factory"       =>	$PHP_factory,
		"PHP_sr_startno"	=>	$PHP_sr_startno,
		"PHP_action"        =>	$PHP_action
	);
} else {
	if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
}

$op = $cutting->daily_search(1);

$op['msg']= $order->msg->get(2);
$op['fty'] = $sch_parm['PHP_factory'];
page_display($op, $AUTH, $TPL_CUTTING_DAILY_LIST);
break;



//----------------------------------------------------------------
//			job 11  cust
//----------------------------------------------------------------
case "cutting_daily_view":
check_authority($AUTH,"view");
	
// �q���ƦC��
if(!$PHP_date)$PHP_date = $TODAY;		


	//  ���� ���X�O���C��
	$argv = array(
        "ord_num"	=>  '',
        "k_date"	=>	$PHP_date,
        "factory"	=>	$PHP_factory,
	);
	$op['sorder'] = $cutting->search($argv,1);
	$op['ttl']['ord_qty'] = $op['ttl']['qty'] = $op['ttl']['qty_cut'] = 0;
	for($i=0;$i<sizeof($op['sorder']);$i++){  // �p�� ����Ͳ��`�ƶq
		$op['ttl']['qty'] += $op['sorder'][$i]['qty'];
		$op['ttl']['qty_cut'] += $op['sorder'][$i]['qty_cut'];
		$op['ttl']['ord_qty'] += $op['sorder'][$i]['ord_qty'];
	}

		$op['msg']= $order->msg->get(2);
$op['sch_date'] = $PHP_date;
page_display($op, $AUTH, $TPL_CUTTING_DAILY_VIEW);
break;	
	
	
 //----------------------------------------------------------------
 //			job 11  cust
 //----------------------------------------------------------------
case "cutting_ord_search":
check_authority($AUTH,"view");		
// �q���ƦC��

$op['sorder'] = $cutting->ord_cut_search($PHP_ord_num);
$op['ttl']['su'] = $op['ttl']['qty'] = 0;
for($i=0;$i<sizeof($op['sorder']);$i++){  // �p�� ����Ͳ��`�ƶq
    $op['ttl']['qty'] += $op['sorder'][$i]['qty'];
    $op['ttl']['su'] += $op['sorder'][$i]['su'];
}

$op['msg']= $order->msg->get(2);
$op['ord_num'] = $PHP_ord_num;

page_display($op, $AUTH, $TPL_CUTTING_ORD_LIST);
break;	
//-------------------------------------------------------------------------

}   // end case ---------

?>
