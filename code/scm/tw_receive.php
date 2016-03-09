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
#			index.php  主程式
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [生產製造][生產產能]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/po_supl.class.php");

$PO_SUPL = new PO_SUPL();
if (!$PO_SUPL->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();



// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd":
check_authority('077',"view");

$dept=$GLOBALS['SCACHE']['ADMIN']['dept'];

$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");

$op['manager_flag']=1;
$where_str ='';
// $where_str=" where status =12 AND rcv_rmk = 0";
$where_str=" where status =12 AND rcv_rmk = 0";
for ($i=0; $i<sizeof($SHIP); $i++)
{
    if ($dept==$SHIP[$i])
    {	
        //$where_str=$where_str." and arv_area = '".$dept."' AND rcv_rmk = 0";
        $op['manager_flag']=0;
        $op['fty']= $dept;
    }
}		
// $where_str = "where status =12 AND rcv_rmk = 0 ORDER BY sup_code";
$where_str = "ORDER BY vndr_no";
$sup=$supl->get_fields('vndr_no',$where_str);
// $where_str = ", supl where supl.vndr_no = ap.sup_code AND status =12 AND rcv_rmk = 0 ORDER BY sup_code";
$sup_name=$supl->get_fields('supl_s_name',$where_str);

if(!$sup)$sup=array('');
if(!$sup_name) $sup_name=array();
$op['sup_no'] = $arry2->select($sup_name,"","PHP_sup","select","",$sup);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;


$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

$op['rcvd'] = $tw_receive->get_receive_check_list();

page_display($op, '077', $TPL_TW_RCVD);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_add_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_add_search":
check_authority('077',"add");	

$PHP_ship = (!empty($_POST['SCH_ship_num'])?$_POST['SCH_ship_num']:(!empty($_GET['SCH_ship_num'])?$_GET['SCH_ship_num']:''));
$PHP_po = (!empty($_POST['SCH_po'])?$_POST['SCH_po']:(!empty($_GET['SCH_po'])?$_GET['SCH_po']:''));
$PHP_carrier = (!empty($_POST['SCH_carrier'])?$_POST['SCH_carrier']:(!empty($_GET['SCH_carrier'])?$_GET['SCH_carrier']:''));
$PHP_bl = (!empty($_POST['SCH_bl'])?$_POST['SCH_bl']:(!empty($_GET['SCH_bl'])?$_GET['SCH_bl']:''));
$PHP_sr_startno = (!empty($_POST['PHP_sr_startno'])?$_POST['PHP_sr_startno']:(!empty($_GET['PHP_sr_startno'])?$_GET['PHP_sr_startno']:''));

$parm = array("PHP_ship"	=>	$PHP_ship,
			  "PHP_po"		=>	$PHP_po,
			  "PHP_carrier"	=>	$PHP_carrier,
			  "PHP_bl"		=>	$PHP_bl,
			  "PHP_sr_startno"	=>	$PHP_sr_startno,
			  "PHP_action"	=>	"rcvd_add_search"
			 );

if($PHP_po <> ''){
    $op = $tw_receive->search_po();
}else{
    $op = $tw_receive->search_inv($parm);
}

$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_carrier=".$PHP_carrier."&PHP_bl=".$PHP_bl;

page_display($op, '077', $TPL_TW_RCV_PO_LIST);
break;		
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_add":

check_authority('077',"add");

$op = $tw_receive->get_shipping($PHP_id);

$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['rcv_num'] = "RP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_carrier=".$PHP_carrier."&PHP_bl=".$PHP_bl."&PHP_sr_startno=".$PHP_sr_startno;

page_display($op, '077', $TPL_TW_RCV_ADD);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_rcv_add": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_rcv_add":
	check_authority('077',"view");		
	
	$parm = array (	
		"ship_num"	=>	$PHP_ship_id,
		"rcv_date"	=>	$TODAY,
		"rcv_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		"ship"		=>	$PHP_dist,
		"dept"		=>	$PHP_dept
	);
	
	$head="RP".date('y')."-";	//RP+日期=驗收單開頭
	$parm['rcv_num'] = $apply->get_no($head,'rcv_num','receive');	//取得驗收的最後編號
	$f1 = $tw_receive->add($parm);

	if (isset($PHP_qty))
	{
		foreach($PHP_qty as $ship_link_id => $qty)
		{
			if ($qty > 0)
			{
				$parm_det = array(
					"rcv_num"		=>	$parm['rcv_num'],
					"ship_link_id"	=>	$ship_link_id,
					"inv_num"		=>	$PHP_inv[$ship_link_id],
					"sup_no"		=>	$tw_receive->get_ap_field("sup_code", $PHP_ap_num[$ship_link_id]),
					"color"			=>	$PHP_color[$ship_link_id],
					"qty"			=>	$qty,
					"ap_num"		=>	$PHP_ap_num[$ship_link_id],
					"mat_cat"		=>	$PHP_mat_cat[$ship_link_id],
					"mat_id"		=>	$PHP_mat_id[$ship_link_id],
					"size"			=>	$PHP_size[$ship_link_id]
				);
				$rcv_det_id = $tw_receive->add_det($parm_det);		
					
				#寫入 rcv_po_link
				foreach($PHP_link_qty[$ship_link_id] as $po_id=>$qty){
					$f3 = $tw_receive->add_link($rcv_det_id, $po_id, $qty, $PHP_ord_num[$ship_link_id][$po_id]);
				}
			}
		}
	}

$message = "successfully append Receive On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"077A",$message);

redirect_page($PHP_SELF."?PHP_action=rcvd_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1");

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_search":
check_authority('077',"view");	
			
if($SCH_order){
    $op = $tw_receive->search(2);
}elseif($SCH_po){
    $op = $tw_receive->search(1);
}else{
    $op = $tw_receive->search(0);
}
	
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_po=".$SCH_po."&SCH_order=".$SCH_order;
$op['msg'] = $tw_receive->msg->get(2);
if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
page_display($op, '077', $TPL_TW_RCV_LIST);
break;				
    
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_view":
check_authority('077',"view");

$op = $tw_receive->get($PHP_rcv_num);

if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_po=".$SCH_po."&SCH_order=".$SCH_order."&PHP_sr_startno=".$PHP_sr_startno;
if(!empty($PHP_resize)) $op['resize'] = 1;

page_display($op, '077', $TPL_TW_RCV_SHOW);
break;


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_edit":
check_authority('077',"view");

$op = $tw_receive->get($PHP_rcv_num);

$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;

page_display($op, '077', $TPL_TW_RCV_EDIT);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_rcvd_edit":
 
		check_authority('077',"view");				
		if($PHP_del_mk == 1)
		{
			$message = "Receiver [".$PHP_rcv_num."] was deleted.";
			$redir_str = "tw_receive.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
			redirect_page($redir_str);
			
		}
		
		if (!isset($PHP_qty))$PHP_qty=array();
		foreach ($PHP_qty as $det_id=>$qty)
		{
			$f1 = $tw_receive->update_field_id('qty',$qty,$det_id,'receive_det');
					
			foreach($PHP_link_qty[$det_id] as $link_id=>$link_qty)
			{
				$f1 = $tw_receive->update_link_qty($link_id,$link_qty);
				//$f1 = $tw_receive->update_rcv_qty('rcv_qty',$rcv_qty[$j], $id[$j],'ap_det');	
			}
		}
$op = $tw_receive->get($PHP_rcv_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Receive # : ".$PHP_rcv_num;
$log->log_add(0,"077E",$message);

$op['msg'][]=$message;
page_display($op, '077', $TPL_TW_RCV_SHOW);

break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "del_det_ajax": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "del_det_ajax":

	$link_row = $tw_receive->get_link_field('po_id, qty','rcv_po_link','rcv_id ='.$PHP_id);
	$f1 = $tw_receive->del_link($PHP_id);
	
	if(substr($PHP_mat_code,0,1) == 'F'){
		$ord_rec = $tw_receive->get_ord_rec($link_row,'l','ap_det');						
		$f1 = $tw_receive->back_ord_rcvd('l',$ord_rec);
	}else{
		$ord_rec = $tw_receive->get_ord_rec($link_row,'a','ap_det');			
		$f1 = $tw_receive->back_ord_rcvd('a',$ord_rec);
	}
				
	$f1 = $tw_receive->del_det($PHP_id);
	$f2 = $tw_receive->chk_del_full($PHP_rcv_num);
	$message = "Success delete material #:".$PHP_mat_code." On receive #:".$PHP_rcv_num;
	$log->log_add(0,"077E",$message);

	echo $message."|".$f2;
	exit;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_submit":
 
		check_authority('077',"edit");				

		$f1 = $tw_receive->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'receive');
		$f1 = $tw_receive->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'receive');
		$f1 = $tw_receive->update_field_id('status',2,$PHP_id,'receive');
		if ($PHP_ver == 0)$tw_receive->update_field_id('version',1,$PHP_id,'receive');
		
		//$f1 = $tw_receive->change_currency($PHP_rcv_num);
		//$f1 = $tw_receive->check_ord_rcvd($PHP_rcv_num,$PHP_ord_num,$PHP_mat_cat);	
		//$f1 = $tw_receive->check_po_rcvd($PHP_rcv_num);
		
		/* for($i=0; $i<sizeof($PHP_ord_num); $i++)
		{
			if($PHP_mat_cat == 'l')
			{
				$mat_date = $order->get_field_value('mat_shp','',$PHP_ord_num[$i],'pdtion');
				if($mat_date && $mat_date <> '0000-00-00')
				{
					$tw_receive->add_ord_cost($PHP_ord_num[$i],'l');
				}
			}else{		
				$macc_date = $order->get_field_value('m_acc_shp','',$PHP_ord_num[$i],'pdtion');
				$acc_date = $order->get_field_value('acc_shp','',$PHP_ord_num[$i],'pdtion');
				if(($macc_date && $macc_date <> '0000-00-00') && ($acc_date && $acc_date <> '0000-00-00'))			
				{
					$tw_receive->add_ord_cost($PHP_ord_num[$i],'a');
				}
			}
	
		} */

$op = $tw_receive->get($PHP_rcv_num);
$op['back_str'] = $PHP_back_str;

$message = "Successfully Submitted Receive # : ".$PHP_rcv_num;
$op['msg'][]="Successfully Submitted Receive # : ".$PHP_rcv_num;
$log->log_add(0,"077E",$message);

page_display($op, '077', $TPL_TW_RCV_SHOW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_close":
 
		check_authority('077',"add");				
		$f1 = $tw_receive->po_close($PHP_po_num,$PHP_special);
//		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc".$SCH_fab;

		$message = "Successfully Close PO # : ".$PHP_po_num;
//		$op['msg'][]="Successfully Submitted Receive # : ".$PHP_rcv_num;
		$log->log_add(0,"077C",$message);
		echo $message;
//		page_display($op, '077', $TPL_RCV_SHOW);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_revise":
check_authority('077',"view");

$f1 = $tw_receive->update_field_id('rcv_sub_user','',$PHP_id,'receive');
$f1 = $tw_receive->update_field_id('rcv_sub_date','',$PHP_id,'receive');
$f1 = $tw_receive->update_field_id('status',0,$PHP_id,'receive');
$f1 = $tw_receive->update_field_id('version',($PHP_ver+1),$PHP_id,'receive');
// $f1 = $tw_receive->back_po_rcvd($PHP_rcv_num);
//$f1 = $tw_receive->back_ord_rcvd($PHP_rcv_num);
// $f1 = $tw_receive->check_ord_un_rcvd($PHP_rcv_num);

$op = $tw_receive->get($PHP_rcv_num);
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_po=".$SCH_po."&SCH_order=".$SCH_order."&PHP_sr_startno=".$PHP_sr_startno;
$op['revise'] = 1;
$msg = "Receive  #:".$PHP_rcv_num." Revised.";
$log->log_add(0,"077",$msg);
$op['msg'][] = $msg;

$toler = explode('|',$op['rcv']['toler']);
$op['ap']['toleri'] = $toler[0];
$op['ap']['tolern'] = $toler[1];

page_display($op, '077', $TPL_TW_RCV_EDIT);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_log_add":
 
		check_authority('077',"view");	
					
if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

	$parm= array(	'rcv_num'		=>	$PHP_rcv_num,
								'item'			=>	'RCV-Rmk.',
								'des'				=>	$PHP_des,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$tw_receive->add_log($parm);
	
		if ($f1)
		{
			$message="Successfully add log :".$PHP_rcv_num;
			$op['msg'][]=$message;
	}else{
		$op['msg']= $apply->msg->get(2);
	}	
		$op = $tw_receive->get($PHP_rcv_num);
		$op['back_str'] = $PHP_back_str;
		page_display($op, '077', $TPL_RCV_SHOW);
		break;		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_print":
 
		check_authority('077',"view");				
		$op = $tw_receive->get($PHP_rcv_num);
		$submit_user=$user->get(0,$op['rcv']['rcv_sub_user']);
		if ($submit_user['name'])$op['rcv']['rcv_sub_user'] = $submit_user['name'];
		$create_user=$user->get(0,$op['rcv']['rcv_user']);
		if ($create_user['name'])$op['rcv']['rcv_user'] = $create_user['name'];

	
		if (substr($op['rcv_det'][0]['mat_code'],0,1) =='A') {$mat_cat='Accessory';}else{$mat_cat='Fabric';}

		$where_str = " WHERE dept_code = '".$op['rcv']['dept']."'";

		$dept_name=$dept->get_fields('dept_name',$where_str);
		if (!isset($dept_name[0]))$dept_name[0] = $op['rcv']['dept'];
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_rcvd.php");

$print_title="Receive";
$print_title2 = "VER.".$op['rcv']['version']."   for   ".$mat_cat;
$creator = $op['rcv']['rcv_user'];
$mark = $op['rcv']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',14);

$ary_title = array (	'rcv_num'		=>	$op['rcv']['rcv_num'],
							'sup_code'		=>	$op['rcv']['sup_no'],
							'sup_name'		=>	$op['rcv']['s_name'],
							'dept'				=>	$dept_name[0],
							'sub_date'		=>	$op['rcv']['rcv_sub_date'],
							'sub_user'		=>	$op['rcv']['rcv_sub_user'],
							'inv_nun'			=>	$op['rcv']['inv_num'],
				);
$pdf->hend_title($ary_title);

//列印王安採購單碼
$pdf->ln();
$pdf->SetFont('Arial','',10);
$pdf->setX(13);
$pdf->Cell(150,5,"(".$op['rcv']['ann_num'].")",0,0,'L');
$pdf->ln();
 $pdf->SetFont('Arial','B',14);




$pdf->rcv_title();
$pdf->ln();
$total =0;$x=0;$po_total=0;$rcv_total=0;
//$cur='';$cur_mk=0;
$unt='';$unt_mk=0;


for ($i=0; $i< sizeof($op['rcv_det']); $i++)
{
/*
	if ($op['rcv']['currency'] <> $cur)	//確認Currency是否都相同
	{
		if($cur<>"") $cur_mk=1;
		$cur = $op['rcv']['currency'];
	}
*/	
	if ($op['rcv_det'][$i]['unit'] <> $unt) //確認Unit是否都相同
	{
		if($unt<>"") $unt_mk=1;
		$unt = $op['rcv_det'][$i]['unit'];
	}		

	if (substr($op['rcv_det'][0]['mat_code'],0,1) =='A')
	{
		$where_str = " WHERE acc_code = '".$op['rcv_det'][$i]['mat_code']."'";
		$mat_name = $acc->get_fields('acc_name',$where_str);
	}else{
		$where_str = " WHERE lots_code = '".$op['rcv_det'][$i]['mat_code']."'";
		$mat_name = $lots->get_fields('lots_name',$where_str);
	}
	$rcv_det_ary = array(	'ord_num'		=>	$op['rcv_det'][$i]['ord_num'][0],
												'mat_code'	=>	$op['rcv_det'][$i]['mat_code'],
												'mat_name'	=>	$mat_name[0],
												'color'			=>	$op['rcv_det'][$i]['color'],
												'po_num'		=>	$op['rcv']['po_num'],
												'po_qty'		=>	NUMBER_FORMAT($op['rcv_det'][$i]['po']['po_qty'],2),
												'eta'				=>	$op['rcv_det'][$i]['po']['eta'],
												'price'			=>	NUMBER_FORMAT($op['rcv_det'][$i]['price'],2),
												'currency'	=>	$op['rcv']['currency'],
												'qty'				=>	NUMBER_FORMAT($op['rcv_det'][$i]['qty'],2),
												'unit'			=>	$op['rcv_det'][$i]['unit'],
											);

	$rcv_det_ary['amount'] = $op['rcv_det'][$i]['qty'] * $op['rcv_det'][$i]['price'];
	$total +=$rcv_det_ary['amount'];
	$po_total+=$op['rcv_det'][$i]['po']['po_qty'];
	$rcv_total+=$op['rcv_det'][$i]['qty'];
	$rcv_det_ary['amount'] = NUMBER_FORMAT($rcv_det_ary['amount'],2);
//	$x+=sizeof($op['rcv_det'][$i]['ord_num']);

	if (sizeof($op['rcv_det'][$i]['ord_num']) > 1)
	{		
		$pdf->rcv_det_mix($rcv_det_ary,$op['rcv_det'][$i]['ord_num']);
	}else{
		$pdf->rcv_det($rcv_det_ary);
	}
}
$pdf->Cell(166,5,'Sub-Total  :  ',1,0,'R');
if ($unt_mk == 1)
{
	$pdf->Cell(50,5,'',1,0,'R');
	$pdf->Cell(53,5,'',1,0,'R');
}else{
	$pdf->Cell(50,5,NUMBER_FORMAT($po_total,2)." ".$op['rcv_det'][0]['unit'],1,0,'R');
	$pdf->Cell(47,5,NUMBER_FORMAT($rcv_total,2)." ".$op['rcv_det'][0]['unit'],1,0,'R');
}
//$pdf->Cell(46,5,$op['rcv']['currency']."$".NUMBER_FORMAT($total,2),1,0,'R'); //金額加總的小計(稅前)
$pdf->ln();
$pdf->SetFont('Big5','',10);
$v_cost =0;$rmk0='';$rmk1='';
$v_cost = $total * ($vant / 100);



if (isset($op['rcv_log'][0]['des']))$rmk0 = $op['rcv_log'][0]['des'];
//$pdf->Cell(14,5,'Remark','RTL',0,'C');
//$pdf->Cell(249,5,$rmk0,'RTL',0,'L');
//$pdf->Cell(20,5,'Vat. : ',1,0,'R');
//$pdf->Cell(46,5,$op['rcv']['currency']."$".NUMBER_FORMAT($v_cost,2),1,0,'R'); //稅金

		$len = mb_strlen($rmk0,'big-5');
		
		if($len >= 90)
		{
			$tmp1 = mb_substr($rmk0,0,90,'big-5'); 
			$tmp2 = mb_substr($rmk0,90,$len,'big-5');
			$pdf->Cell(14,5,'Remark','RTL',0,'C');
			$pdf->Cell(249,5,$tmp1,'RTL',1,'L');
			$pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$tmp2,'RL',1,'L');
		}else{
			$pdf->Cell(14,5,'Remark','RTL',0,'C');
			$pdf->Cell(249,5,$rmk0,'RTL',1,'L');
		}

$pdf->SetFont('big5','',10);

//$pdf->ln();

$total +=$v_cost;
//if (isset($op['rcv_log'][1]['des']))$rmk1 = $op['rcv_log'][1]['des'];
//$pdf->Cell(14,5,'','RL',0,'C');
//$pdf->Cell(249,5,$rmk1,'RL',0,'L');
//$pdf->SetFont('big5','',10);
//$pdf->Cell(20,5,'Total : ',1,0,'R');
//$pdf->Cell(46,5,$op['rcv']['currency']."$".NUMBER_FORMAT($total,2),1,0,'R'); //稅後總計


if (isset($op['rcv_log']))
{
	for ($i=1; $i<sizeof($op['rcv_log']); $i++)
	{
		$len = mb_strlen($op['rcv_log'][$i]['des'],'big-5');
		
		if($len >= 90)
		{
			$tmp1 = mb_substr($op['rcv_log'][$i]['des'],0,90,'big-5'); 
			$tmp2 = mb_substr($op['rcv_log'][$i]['des'],90,$len,'big-5');
			$pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$tmp1,'RL',0,'L');
			$pdf->ln();
			$pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$tmp2,'RL',0,'L');
			$pdf->ln();			
		}else{
		  $pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$op['rcv_log'][$i]['des'],'RL',0,'L');
			$pdf->ln();
		}
	}
}
$pdf->Cell(14,0,'','RLB',0,'C');
$pdf->Cell(249,0,'','RLB',0,'C');
$pdf->ln();

$pdf->ln();
$pdf->SetFont('Big5','',10);
$pdf->Cell(10,5,' ','0',0,'C');
$pdf->Cell(40,5,'',0,0,'L');

$pdf->Cell(60,5,'APPROVAL : ','0',0,'C');	//PO Approval

$pdf->Cell(60,5,'CONFIRM :',0,0,'L');//PO Confirm
$pdf->Cell(50,5,'Bursary :',0,0,'L');//PO Submit
	
$pdf->Cell(60,5,'Receive :'.$op['rcv']['rcv_sub_user'],0,0,'L');//PA submit


$name=$op['rcv']['rcv_num'].'_rcvd.pdf';
$pdf->Output($name,'D');

page_display($op, '077', $TPL_RCV_SHOW);
break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "recv_qty_show": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "recv_qty_show":
check_authority('077',"view");

$op['rcvd']=$tw_receive->get_revd_det($PHP_ship_link_id);

page_display($op, '077', $TPL_RCV_RECV_SHOW);

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcv_rpt": JOB31 驗收報表主頁
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcv_rpt":
 
		check_authority('077',"view");		

		$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
		$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
		$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");


		page_display($op, '077', $TPL_RCVD_RPT);
		break;	

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_rpt_used": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_rpt_used":
 
		check_authority('077',"view");	
		if(!$PHP_str && (!$PHP_year || !$PHP_month)) 
		{
			$op['msg'][] ="Please Select Year, Month or choice Start Date.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, '077', $TPL_RCVD_RPT);
			break;
		}
		if(!isset($PHP_cat1))
		{
			$op['msg'][] ="Please Select Fabric or Accessory.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, '077', $TPL_RCVD_RPT);
			break;
		}
					
		if(!$PHP_str)
		{
			$e_day = getDaysInMonth($PHP_month,$PHP_year);
			$PHP_str = $PHP_year."-".$PHP_month."-01";
			$PHP_fsh = $PHP_year."-".$PHP_month."-".$e_day;
		}else{
			if(!$PHP_fsh) $PHP_fsh = $TODAY;
		}
		
		$op['title2'] = "起 ".$PHP_str." 迄 ".$PHP_fsh;
		if ($PHP_cat1=="F"){$mat = "主料";}else{$mat = "副料";}
		if ($PHP_ship){$PHP_ship2 = $PHP_ship;}else{$PHP_ship2 = 'HJ & LY';}
		$op['title1'] = "[".$PHP_ship2."] ".$mat." 彙總報表";
		$parm = array("str"		=>	$PHP_str,
									"end"		=>	$PHP_fsh,
									"ship"	=>	$PHP_ship,
									"cat"		=>	$PHP_cat1,
									);
		$rcv = $tw_receive->search_rpt_used($parm);
		$rpt = array();
		$tmp_mat = '';
		$j=-1;
		$total_qty=$total_cost=0;
		
	if($rcv)
	{
		for ($i=0; $i<sizeof($rcv); $i++)
		{
			if($tmp_mat <> $rcv[$i]['mat_code'])
			{
				if($j > -1)
				{
					$rpt[$j]['avg_price'] = number_format(($rpt[$j]['cost'] / $rpt[$j]['qty']),2,'.','');
					$rpt[$j]['qty'] = number_format($rpt[$j]['qty'],2,'.','');
				}
				$j++;
				$rpt[$j] =$rcv[$i];
				$rpt[$j]['qty'] = $rcv[$i]['rcv_qty'];
				$tmp_mat = $rcv[$i]['mat_code'];								
			}else{				
				$rpt[$j]['cost']+=$rcv[$i]['cost'];
				$rpt[$j]['qty']+=$rcv[$i]['rcv_qty'];
			}		
			$total_qty+=$rcv[$i]['rcv_qty'];
			$total_cost+=$rcv[$i]['cost'];
		}
		$rpt[$j]['avg_price'] = number_format(($rpt[$j]['cost'] / $rpt[$j]['qty']),2,'.','');
		$rpt[$j]['qty'] = number_format($rpt[$j]['qty'],2,'.','');
	}
		$op['rpt'] = $rpt;
		$op['sch'] = $parm;
		$op['total_qty'] = $total_qty;
		$op['total_cost'] = $total_cost;
		
		if(!isset($PHP_pdf) && !isset($PHP_excel))
		{
			page_display($op, '077', $TPL_RCV_RPT_USED);
			break;			
		}elseif(isset($PHP_pdf)){
			include_once($config['root_dir']."/lib/class.pdf_rpt.php");

			$print_title = $op['title1'];
			$print_title2 = $op['title2'];
			$creator = $GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]";
			$mark = $PHP_ship2;
			$pdf=new PDF_rpt('P','mm','A4');
			$pdf->AddBig5Font();
			$pdf->Open();
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',14);
			// [表匡] 訂單基本資料
			$title_ary = array ('物料 #','物料名稱','驗收量','單價','成本');
			$title_size = array (30,70,30,30,30);
			$rpt_ail = array ('c','L','R','R','R');

			$pdf->rpt_title($title_ary,$title_size);
			$cg = 0;
			for($i=0; $i<sizeof($rpt); $i++)
			{
				if ($cg > 34)
				{
					$pdf->AddPage();		
					$pdf->rpt_title($title_ary,$title_size);
					$cg = 0;
				}
				$cg++;
				$rpt[$i]['qty'] = number_format($rpt[$i]['qty'],2,'.',',');
				$rpt[$i]['cost'] = number_format($rpt[$i]['cost'],2,'.',',');
				$rpt[$i]['avg_price'] = number_format($rpt[$i]['avg_price'],2,'.',',');
				$rpt_data = array($rpt[$i]['mat_code'],$rpt[$i]['mat_name'],$rpt[$i]['qty'].' '.$rpt[$i]['unit'],
										$rpt[$i]['avg_price'],$rpt[$i]['cost']);
				$pdf->rpt_data($rpt_data,$title_size,$rpt_ail);
			}

			$total_qty = number_format($total_qty,2,'.',',');
			$total_cost = number_format($total_cost,2,'.',',');
			$rpt_data = array('TOTAL','',$total_qty,'',$total_cost);
			$pdf->rpt_total($rpt_data,$title_size,$rpt_ail);


			$name=$PHP_ship.'rpt_used.pdf';
			$pdf->Output($name,'D');
			break;	
		}else{
			require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
			require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  	function HeaderingExcel($filename) {
			  header("Content-type: application/vnd.ms-excel");
			  header("Content-Disposition: attachment; filename=$filename" );
			  header("Expires: 0");
			  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			  header("Pragma: public");
		 	}
	 		 // HTTP headers
	 		 HeaderingExcel('order.apved.xls');
	 
	 		 // Creating a workbook
	 		 $workbook = new Workbook("-");

	  		// Creating the first worksheet
	 		 $worksheet1 =& $workbook->add_worksheet('order list');

			 $now = $GLOBALS['THIS_TIME'];		
			// 寫入 title

	 		 // Format for the headings
			$formatot =& $workbook->add_format();
	  	$formatot->set_size(10);
	  	$formatot->set_align('center');
	  	$formatot->set_color('white');
	  	$formatot->set_pattern();
	  	$formatot->set_fg_color('navy');
	  	
	 		$f3 =& $workbook->add_format(); //置右數字
	  	$f3->set_size(10);
	  	$f3->set_align('right');
	  	$f3->set_num_format(3);

	 		$f4 =& $workbook->add_format();  //灰底白字置右數字
	  	$f4->set_size(10);
	  	$f4->set_color('white');
	  	$f4->set_pattern(1);
	 	  $f4->set_align('right');
	 	  $f4->set_num_format(3);
	  	$f4->set_fg_color('grey');
  		$f4->set_num_format(3);

	 		$f5 =& $workbook->add_format();  //灰底白字置中
	  	$f5->set_size(10);
	  	$f5->set_color('white');
	  	$f5->set_pattern(1);
	 	  $f5->set_align('center');
	 	  $f5->set_num_format(3);
	  	$f5->set_fg_color('grey');

	  	$worksheet1->set_column(0,0,15);
	  	$worksheet1->set_column(0,1,30);
	  	$worksheet1->set_column(0,2,15);
	  	$worksheet1->set_column(0,3,15);
	  	$worksheet1->set_column(0,4,15);  


	  $worksheet1->write_string(0,1,$op['title1']." [ ".$op['title2']."]");
	  $worksheet1->write(1,1,"printed:".$GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]");

	  $worksheet1->write_string(2,0,"物料#",$formatot);
	  $worksheet1->write_string(2,1,"物料名稱",$formatot);
	  $worksheet1->write_string(2,2,"驗收量",$formatot);
	  $worksheet1->write_string(2,3,"單價",$formatot);
	  $worksheet1->write_string(2,4,"成本",$formatot);
	  	

	for ($i=0; $i<sizeof($rpt); $i++){
	  $worksheet1->write($i+3,0,$rpt[$i]['mat_code']);
	  $worksheet1->write($i+3,1,$rpt[$i]['mat_name']);
	  $worksheet1->write($i+3,2,$rpt[$i]['qty']." ".$rpt[$i]['unit'],$f3);
	  $worksheet1->write_number($i+3,3,$rpt[$i]['avg_price'],$f3);
	  $worksheet1->write_number($i+3,4,$rpt[$i]['cost'],$f3);
	
	}
  $worksheet1->write($i+3,0,'Total',$f5);
  $worksheet1->write($i+3,1,'',$f5);
  $worksheet1->write_number($i+3,2,$total_qty,$f4);
  $worksheet1->write_number($i+3,3,'',$f4);
  $worksheet1->write_number($i+3,4,$total_cost,$f4);

  $workbook->close();

	break;


		}
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_rpt_used": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_rpt_dtl":
 
		check_authority('077',"view");	
		if(!$PHP_str && (!$PHP_year || !$PHP_month)) 
		{
			$op['msg'][] ="Please Select Year, Month or choice Start Date.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, '077', $TPL_RCVD_RPT);
			break;
		}
		if(!isset($PHP_cat1))
		{
			$op['msg'][] ="Please Select Fabric or Accessory.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, '077', $TPL_RCVD_RPT);
			break;
		}
					
		if(!$PHP_str)
		{
			$e_day = getDaysInMonth($PHP_month,$PHP_year);
			$PHP_str = $PHP_year."-".$PHP_month."-01";
			$PHP_fsh = $PHP_year."-".$PHP_month."-".$e_day;
		}else{
			if(!$PHP_fsh) $PHP_fsh = $TODAY;
		}
		
		$op['title2'] = "起 ".$PHP_str." 迄 ".$PHP_fsh;
		if ($PHP_cat1=="F"){$mat = "主料";}else{$mat = "副料";}
		if ($PHP_ship){$PHP_ship2 = $PHP_ship;}else{$PHP_ship2 = 'HJ & LY';}
		$op['title1'] = "[".$PHP_ship2."] ".$mat." 驗收列表 ";
		
		if(!isset($PHP_price))$PHP_price = 0;
		$parm = array("str"		=>	$PHP_str,
									"end"		=>	$PHP_fsh,
									"ship"	=>	$PHP_ship,
									"cat"		=>	$PHP_cat1,
									"ord"		=>	$PHP_ord,
									"price"	=>	$PHP_price
									);
		$rcv = $tw_receive->search_rpt_dtl($parm);
		$rpt = array();		
		$j=0;
		$sub_cost=$sub_qty=$total_qty=$total_cost=0;
		$ary_unit= array();
	if($rcv)
	{
	
		$rcv = bubble_sort($rcv);
		$tmp_ord = $rcv[0]['ord_num'];
		$tmp_unit = '';
		$x = 0;
		$ary_qty[0] = 0;
	  $ary_unit[0] = $rcv[0]['unit'];					

		for ($i=0; $i<sizeof($rcv); $i++)
		{			
			if($tmp_ord <> $rcv[$i]['ord_num'])
			{		
				$rpt[$j]['ord_num'] = 'Sub-Total';
				$rpt[$j]['rcv_qty'] = '';
			  for($k=0; $k<sizeof($ary_unit); $k++)
			  {
					$rpt[$j]['rcv_qty'] .= NUMBER_FORMAT($ary_qty[$k],2,'.',',').$ary_unit[$k]."+";
  			}
				$rpt[$j]['rcv_qty'] = substr($rpt[$j]['rcv_qty'],0,-1);
		//		$rpt[$j]['rcv_qty'] = $sub_qty;
				
				$rpt[$j]['cost'] = $sub_cost;
				$rpt[$j]['ln_mk'] = 1;
//				$sub_qty=$sub_cost=0;	
				$j++;
				$tmp_ord = $rcv[$i]['ord_num'];									
			}
			
//			$total_qty+=$rcv[$i]['rcv_qty'];
			$total_cost+=$rcv[$i]['cost'];				
			
			$tmp_x = -1;
			for($k=0; $k<sizeof($ary_unit); $k++)
			{
				if($ary_unit[$k] ==$rcv[$i]['unit']) 
				{
					$tmp_x = $k;
					break;
				}					
			}
			
			if($tmp_x > -1)
			{
				$ary_qty[$tmp_x]+= $rcv[$i]['rcv_qty'];	
									
			}else{
				$x++;
				$ary_qty[$x] = $rcv[$i]['rcv_qty'];
	 			$ary_unit[$x] = $rcv[$i]['unit'];						
			} // end if($tmp_j > -1)
	
		
			
			if(isset($rcv[$i]['price']))$rcv[$i]['price'] = number_format($rcv[$i]['price'],2,'.','');
			if(isset($rcv[$i]['qty']))  $rcv[$i]['rcv_qty'] = number_format($rcv[$i]['rcv_qty'],2,'.','');
			if(isset($rcv[$i]['qty']))  $rcv[$i]['cost'] = number_format($rcv[$i]['cost'],2,'.','');			
			$rpt[$j] = $rcv[$i];	
			$rpt[$j]['ln_mk'] = 0;		
//			$sub_qty +=$rcv[$i]['rcv_qty'];
			$sub_cost+=$rcv[$i]['cost'];
			$j++;
			
		}
	}
/*		
		$rpt[$j]['ord_num'] = 'sub_total';
		$rpt[$j]['qty'] = $sub_qty;
		$rpt[$j]['cost'] = $sub_cost;
		$rpt[$j]['ln_mk'] = 1;
*/		
		$op['rpt'] = $rpt;
		$op['sch'] = $parm;
		$op['total_qty'] = $total_qty;
		$op['total_cost'] = $total_cost;

		$op['total_qty'] = '';
	  for($k=0; $k<sizeof($ary_unit); $k++)
	  {
	  	
			$op['total_qty'] .= NUMBER_FORMAT($ary_qty[$k],2,'.',',').$ary_unit[$k]."+";
		}
		$op['total_qty'] = substr($op['total_qty'],0,-1);
		
		
		if(!isset($PHP_pdf) && !isset($PHP_excel))
		{
			page_display($op, '077', $TPL_RCV_RPT_DTL);
			break;					
		}else if(isset($PHP_pdf)){
			include_once($config['root_dir']."/lib/class.pdf_rpt.php");

			$print_title = $op['title1'];
			$print_title2 = $op['title2'];
			$creator = $GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]";
			$mark = $PHP_ship2;
			$pdf=new PDF_rpt('P','mm','A4');
			$pdf->AddBig5Font();
			$pdf->Open();
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',14);
			// [表匡] 訂單基本資料
			if($PHP_price == 1)
			{
				$title_ary = array ('訂單 #','驗收單 #','驗收日','收料單位','驗收部門','物料#','驗收量','單價','成本');
				$title_size = array (20,20,20,15,15,25,25,25,25);
				$rpt_ail = array ('C','C','C','C','C','C','R','R','R');
				$total_size = array(60,80,50);
				$total_ail = array ('R','R','R');
			}else{			
				$title_ary = array ('訂單 #','驗收單 #','驗收日','收料單位','驗收部門','物料 #','驗收量');
				$title_size = array (25,25,30,20,20,35,35);
				$rpt_ail = array ('C','C','C','C','C','C','R');
				$total_size = array(80,110);
				$total_ail = array ('R','R','C');
			}
			$pdf->rpt_title($title_ary,$title_size);
			$cg = 0;
			for($i=0; $i<sizeof($rpt); $i++)
			{
				if ($cg > 34)
				{
					$pdf->AddPage();		
					$pdf->rpt_title($title_ary,$title_size);
					$cg = 0;
				}
				$cg++;
				$rpt[$i]['qty'] = number_format($rpt[$i]['rcv_qty'],2,'.',',');
				$rpt[$i]['cost'] = number_format($rpt[$i]['cost'],2,'.',',');
				if($rpt[$i]['ln_mk'] == 1)
				{
					if($PHP_price == 1)
					{
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_qty'],$rpt[$i]['cost']);
					}else{
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_qty']);
					}
					$pdf->rpt_total($rpt_data,$total_size,$total_ail);					
				}else{
					
					if($PHP_price == 1)
					{
						if(isset($rpt[$i]['price']))$rpt[$i]['price'] = number_format($rpt[$i]['price'],2,'.',',');
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_num'],$rpt[$i]['rcv_sub_date'],
															$rpt[$i]['ship'],$rpt[$i]['dept'],$rpt[$i]['mat_code'],$rpt[$i]['rcv_qty'].' '.$rpt[$i]['unit'],
															$rpt[$i]['price'],$rpt[$i]['cost']);
					}else{
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_num'],$rpt[$i]['rcv_sub_date'],
															$rpt[$i]['ship'],$rpt[$i]['dept'],$rpt[$i]['mat_code'],$rpt[$i]['rcv_qty'].' '.$rpt[$i]['unit']
															);
					}

					$pdf->rpt_data($rpt_data,$title_size,$rpt_ail);
				}
			}

			$total_qty = number_format($total_qty,2,'.',',');
			$total_cost = number_format($total_cost,2,'.',',');
			if($PHP_price == 1)
			{
				$rpt_data = array('TOTAL',$op['total_qty'],$total_cost);
			}else{
				$rpt_data = array('TOTAL',$op['total_qty']);
			}
			$pdf->rpt_total($rpt_data,$total_size,$total_ail);


			$name=$PHP_ship.'rpt_list.pdf';
			$pdf->Output($name,'D');
			break;				
		}else{
			require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
			require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  	function HeaderingExcel($filename) {
			  header("Content-type: application/vnd.ms-excel");
			  header("Content-Disposition: attachment; filename=$filename" );
			  header("Expires: 0");
			  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			  header("Pragma: public");
		 	}
	 		 // HTTP headers
	 		 HeaderingExcel('rcvd_detial.xls');
	 
	 		 // Creating a workbook
	 		 $workbook = new Workbook("-");

	  		// Creating the first worksheet
	 		 $worksheet1 =& $workbook->add_worksheet('Receive list');

			 $now = $GLOBALS['THIS_TIME'];		
			// 寫入 title

	 		 // Format for the headings
			$formatot =& $workbook->add_format();
	  	$formatot->set_size(10);
	  	$formatot->set_align('center');
	  	$formatot->set_color('white');
	  	$formatot->set_pattern();
	  	$formatot->set_fg_color('navy');
	  	
	 		$f3 =& $workbook->add_format(); //置右數字有小數點
	  	$f3->set_size(10);
	  	$f3->set_align('right');
	  	$f3->set_num_format(4);

	 		$f2 =& $workbook->add_format(); //置右數字整數
	  	$f2->set_size(10);
	  	$f2->set_align('right');
	  	$f2->set_num_format(3);


	 		$f4 =& $workbook->add_format();  //灰底白字置右數字有小數點二位
	  	$f4->set_size(10);
	  	$f4->set_color('white');
	  	$f4->set_pattern(1);
	 	  $f4->set_align('right');
	 	  $f4->set_num_format(4);
	  	$f4->set_fg_color('grey');

	 		$f6 =& $workbook->add_format();  //灰底白字置右數字整數
	  	$f6->set_size(10);
	  	$f6->set_color('white');
	  	$f6->set_pattern(1);
	 	  $f6->set_align('right');
	 	  $f6->set_num_format(4);
	  	$f6->set_fg_color('grey');


	 		$f5 =& $workbook->add_format();  //灰底白字置中
	  	$f5->set_size(10);
	  	$f5->set_color('white');
	  	$f5->set_pattern(1);
	 	  $f5->set_align('center');
	  	$f5->set_fg_color('grey');

	  	$worksheet1->set_column(0,0,10);
	  	$worksheet1->set_column(0,1,10);	  	
	  	$worksheet1->set_column(0,2,12);
	  	$worksheet1->set_column(0,3,10);
	  	$worksheet1->set_column(0,4,8);  
	  	$worksheet1->set_column(0,5,15);
	  	$worksheet1->set_column(0,6,15);
	  	$worksheet1->set_column(0,7,15);
	  	$worksheet1->set_column(0,8,15);


	  $worksheet1->write_string(0,1,$op['title1']." [ ".$op['title2']."]");
	  $worksheet1->write(1,1,"printed:".$GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]");

	  $worksheet1->write_string(2,0,"訂單#",$formatot);
	  $worksheet1->write_string(2,1,"驗收單#",$formatot);
	  $worksheet1->write_string(2,2,"驗收日",$formatot);
	  $worksheet1->write_string(2,3,"收料單位",$formatot);
	  $worksheet1->write_string(2,4,"驗收部門",$formatot);
	  $worksheet1->write_string(2,5,"物料#",$formatot);
	  $worksheet1->write_string(2,6,"驗收量",$formatot);
  	if ($PHP_price == 1)
  	{
	  	$worksheet1->write_string(2,7,"單價",$formatot);
	  	$worksheet1->write_string(2,8,"成本",$formatot);
	  }
	  	

	for ($i=0; $i<sizeof($rpt); $i++){
		if($rpt[$i]['ln_mk'] == 1)
		{
	  	$worksheet1->write($i+3,0,$rpt[$i]['ord_num'],$f5);
		  for($j=1; $j < 6; $j++) $worksheet1->write($i+3,$j,'',$f5);
	  	$worksheet1->write_string($i+3,6,$rpt[$i]['rcv_qty'],$f6);
	  	if ($PHP_price == 1)
	  	{
	  		$worksheet1->write_string($i+3,7,'',$f5);
	  		$worksheet1->write_number($i+3,8,$rpt[$i]['cost'],$f4);
			}
		}else{
	  	$worksheet1->write($i+3,0,$rpt[$i]['ord_num']);
	  	$worksheet1->write($i+3,1,$rpt[$i]['rcv_num']);
	  	$worksheet1->write($i+3,2,$rpt[$i]['rcv_sub_date']);
	  	$worksheet1->write($i+3,3,$rpt[$i]['ship']);
	  	$worksheet1->write($i+3,4,$rpt[$i]['dept']);
	  	$worksheet1->write($i+3,5,$rpt[$i]['mat_code']);
	  	$worksheet1->write($i+3,6,$rpt[$i]['rcv_qty']." ".$rpt[$i]['unit'],$f2);
	  	if ($PHP_price == 1)
	  	{
	  		$worksheet1->write_number($i+3,7,$rpt[$i]['price'],$f3);
	  		$worksheet1->write_number($i+3,8,$rpt[$i]['cost'],$f3);
	  	}
		}
	}
  $worksheet1->write($i+3,0,'Total',$f5);
  for($j=1; $j < 6; $j++) $worksheet1->write($i+3,$j,'',$f5);
  $worksheet1->write_string($i+3,6,$op['total_qty'],$f6);
 	if ($PHP_price == 1)
 	{
  	$worksheet1->write_number($i+3,7,'',$f4);
  	$worksheet1->write_number($i+3,8,$total_cost,$f4);
	}
  $workbook->close();

	break;
		}
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "rev_po_det":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rev_po_det":
check_authority('077',"view");

$op['rcvd'] = $tw_receive->get_rcv_det($PHP_bom_id,$PHP_ord_num);

page_display($op, '077', $TPL_RCV_PO_SHOW );
break;				


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "add_ord_tmp":
 
		check_authority('077',"view");	
		$tw_receive->add_order();	
		break;					
	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_file":
		check_authority('077',"add");
		
		$filename = $_FILES['PHP_pttn']['name'];		
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

		if(strstr($PHP_desp,'&#'))
		{
			$PHP_desp = $ch_cov->check_cov($PHP_desp);
		}			

		$f_check = 0;
		for ($i=0; $i<sizeof( $GLOBALS['VALID_TYPES']); $i++)
		{
			if ($GLOBALS['VALID_TYPES'][$i] == $ext)
			{
				$f_check = 1;
				break;
			}
		}		
		
		if ($filename && $PHP_desp)
		{
																		
		if ($_FILES['PHP_pttn']['size'] < 2072864 && $_FILES['PHP_pttn']['size'] > 0)
		{
		if ($f_check == 1){   // 上傳檔的副檔名為有效時 -----

			// upload pattern file to server
			$today = $GLOBALS['TODAY'];
			$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
			$parm = array(	"id"					=>  $PHP_id,
											"file_des"		=>	$PHP_desp,
											"file_user"		=>	$user_name,
											"file_date"		=>	$today
			);
			
			$A = $fils->get_name_id('receive_file');
			$pttn_name = $PHP_num."_".$A.".".$ext;  // 組合檔名
			$parm['file_name'] = $pttn_name;
			
			$str_long=strlen($pttn_name);
			$upload = new Upload;
			
			$upload->setMaxSize(2072864);
			$upload->uploadFile(dirname($PHP_SELF).'/receive_file/', 'other', 16, $pttn_name );
			$upload->setMaxSize(2072864);
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$A = $fils->upload_receive_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}			
			$message = "UPLOAD file of : ".$PHP_num;
			$log->log_add(0,"077E",$message);
		} else {  // 上傳檔的副檔名  是  exe 時 -----

			$message = "upload file is incorrect format ! Please re-send.";
		}
		}else{  //上傳檔名重覆時
			$message = "Upload file is too big";
		}
	
		}else{
			$message="You don't pick any file or add file descript.";
		}	
			
		$op = $tw_receive->get($PHP_num);
//echo $PHP_num;
		$op['back_str'] = $PHP_back_str;		

		$op['msg'][] = $message;
		page_display($op, '077', $TPL_RCV_SHOW);
		
		break;	


//=======================================================
    case "do_file_del":
		
	//	check_authority(3,1,"edit");	

		$f1 = $fils->del_file($PHP_talbe,$PHP_id);
		if (!$f1) {
			$op['msg'] = $fils->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if(file_exists($GLOBALS['config']['root_dir']."/receive_file/".$PHP_file_name)){
			unlink("./receive_file/".$PHP_file_name);
		}

		$op = $tw_receive->get($PHP_rcv_num);
		if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
		page_display($op, '077', $TPL_RCV_SHOW);
		
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_cfm_search":	2012/2/4	//台北採購 cfm 工廠驗收
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "rcvd_cfm_search":

	check_authority('076',"view");	

	if (!$op = $tw_receive->cfm_search()) {  
		$op['msg']= $tw_receive->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	$op['msg']= $tw_receive->msg->get(2);

	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

	page_display($op, '73', $TPL_TW_RCV_CFM_LIST);
	break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_cfm_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_cfm_view":
	check_authority('076',"view");

	$op = $tw_receive->get($PHP_rcv_num);

	if(isset($PHP_sr_startno)) $op['back_str'] = "&PHP_sr_startno=".$PHP_sr_startno;
		
	page_display($op, '076', $TPL_TW_RCV_CFM_SHOW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_cfm":	2011/11/22
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_cfm":

	check_authority('076',"add");	
	
	$f1 = $tw_receive->update_field_id('rcv_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'receive');
	$f1 = $tw_receive->update_field_id('rcv_cfm_date',$TODAY,$PHP_id,'receive');
	$f1 = $tw_receive->update_field_id('status','4',$PHP_id,'receive');
	
	if (!$op = $tw_receive->cfm_search()) {  
				$op['msg']= $tw_receive->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
	$op['msg']= $tw_receive->msg->get(2);

	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

	page_display($op, '076', $TPL_RCV_CFM_LIST);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_rcvd_cfm":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_rcvd_cfm":
	check_authority('076',"add");	

	if(strstr($PHP_detail,'&#')) $PHP_detail = $ch_cov->check_cov($PHP_detail);
	
	$f1=$tw_receive->update_field_id('status', '13', $PHP_id);
	$f1=$tw_receive->update_field_id('au_date', $TODAY, $PHP_id);
	$f1=$tw_receive->update_field_id('au_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_id);
	
	$parm= array('rcv_num'		=>	$PHP_rcv_num,
				'item'			=>	'RCV REJ-CFM',
				'des'			=>	$PHP_detail,
				'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
				'k_date'		=>	date('Y-m-d')
			);
	$f1=$tw_receive->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully reject Rcvd :".$PHP_rcv_num;
		$op['msg'][]=$message;
		$log->log_add(0,"076E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "receive.php?PHP_action=rcvd_cfm_view&PHP_rcv_num=".$PHP_rcv_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "get_rcv_det":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_rcv_det":
	check_authority('076',"view");

	$op['rcvd'] = $tw_receive->get_tw_rcvd_qty($_GET['PHP_po_nums'], $_GET['PHP_mat_id'], $_GET['PHP_color'], $_GET['PHP_size']);
	
	page_display($op, '076', "tw_rcv_rcvd_show.html");
break;

//-------------------------------------------------------------------------

}   // end case ---------

?>
