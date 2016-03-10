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

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();




switch ($PHP_action) {
//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd":
 
		check_authority(3,1,"view");				
		$dept=$GLOBALS['SCACHE']['ADMIN']['dept'];
		
		$op['fty']= $arry2->select($SHIP,"","PHP_ship","select","search_ship(this,1)");
		$op['manager_flag']=1;
		
		$where_str=" where status =12";
		for ($i=0; $i<sizeof($SHIP); $i++)
		{
			if ($dept==$SHIP[$i])
			{
				$where_str=$where_str." and arv_area = '".$dept."' AND rcv_rmk = 0";
				$op['manager_flag']=0;
				$op['fty']= $dept;
			}
		}		
		$sup=$apply->get_fields('DISTINCT sup_code',$where_str);
		if(!$sup)$sup=array();
		$op['sup_no'] = $arry2->select($sup,"","PHP_sup","select","");
				
		if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		page_display($op, 3, 1, $TPL_RCVD);
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "get_supplier": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "get_supplier":
 
		$where_str=" where status =12";		
		if ($PHP_item==1) $where_str=$where_str." and arv_area = '".$PHP_ship."' AND rcv_rmk = 0";	
		if ($PHP_item==2) $where_str=$where_str." and ship_name = '".$PHP_ship."' AND rcv_rmk = 0";
		$sup=$apply->get_fields('DISTINCT sup_code',$where_str);
		if(!$sup)$sup=array();
		$select_sup = $arry2->select($sup,"","PHP_sup","select","");
				
		echo $select_sup;
		exit;
//		break;		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_add_search":
 
		check_authority(5,7,"view");				
		$redirect = "receive.php?PHP_action=rcvd&PHP_msg=Please choice SHIP and Supplier";
		if ($PHP_ship=='' && $PHP_ship2=='') redirect_page($redirect);
		if (!isset($PHP_sup) || $PHP_sup=='') redirect_page($redirect);
		if(!$PHP_ship)$PHP_ship = $PHP_ship2;
		$op = $receive->search_po($PHP_sup,$PHP_ship);
		$op['date'] = $TODAY;
		$op['sup_no'] = $PHP_sup;
		$op['ship'] = $PHP_ship;
		$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$sup_value = $supl->get('',$PHP_sup);
		$op['sup_name'] = $sup_value['supl_s_name'];

		$op['rcv_num'] = "RP".date('y')."-xxxx";

		$op['tax'] =  $arry2->select($tax,'應稅','PHP_tax','select','');	//稅率狀態下拉式
		$op['cancel'] =  $arry2->select($cancel,'','PHP_cancel','select','');	//抵扣下拉式
		$op['inv_style'] =  $arry2->select($inv_style,'三聯式','PHP_inv_style','select','');	//發票格式下拉式	
		
		$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

		page_display($op, 5, 7, $TPL_RCV_ADD);
		break;		
		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_rcv_add":
 
		check_authority(5,7,"view");		
		if($PHP_inv_num){$inv_date = $TODAY;}else{$inv_date = '0000-00-00';}
		$parm = array (	"sup_no"	=>	$PHP_sup_no,
										"rcv_date"	=>	$TODAY,
										"rcv_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"inv_num"		=>	$PHP_inv_num,
										"inv_date"	=>	$inv_date,
//										"inv_style"	=>	$PHP_inv_style,
										"ship"			=>	$PHP_ship,
										"dept"			=>	$PHP_dept,
//										"abate"			=>	$PHP_abate,
//										"cancel"		=>	$PHP_cancel,
//										"tax"				=>	$PHP_tax
									 );
		$head="RP".date('y')."-";	//RP+日期=驗收單開頭
		$parm['rcv_num'] = $apply->get_no($head,'rcv_num','receive');	//取得驗收的最後編號
		$f1 = $receive->add($parm);
	if (isset($PHP_mat_code))
	{
		for ($i = 0; $i < sizeof($PHP_mat_code); $i++)
		{
			if ($PHP_rcv[$i] > 0)
			{
				$parm_det = array(	"rcv_num"		=>	$parm['rcv_num'],
														"po_id"			=>	$PHP_ids[$i],
														"mat_code"	=>	$PHP_mat_code[$i],
														"qty"				=>	$PHP_rcv[$i],
														"unit"			=>	$PHP_po_unit[$i],
														"price"			=>	$PHP_price[$i],
														"color"			=>	$PHP_color[$i],
														"po_cat"		=>	'0',
													);
				$f1 = $receive->add_det($parm_det);		
				$id = explode('|',$PHP_ids[$i]);								
				$s_total=0;
				$tmp=0;
				for ($j=0; $j< sizeof($id); $j++)		//取得每項的請購量,並加總
				{
					$where_str = "id = ".$id[$j];
					$row[$j]=$po->get_det_field('po_qty','ap_det',$where_str);
					$s_total = $s_total + $row[$j]['po_qty'];
				}		
				for ($j=0; $j< (sizeof($id)-1); $j++)		//計算每項採購量(平分)
				{
					$rcv_qty[$j] = $PHP_rcv[$i] * ($row[$j]['po_qty'] / $s_total);
					$tmp = $tmp + $rcv_qty[$j];
				}	
				$tmp_qty =  $PHP_rcv[$i] - $tmp;
				$rcv_qty[$j] = $tmp_qty;
				for ($j=0; $j< sizeof($id); $j++)		//加入DB
					$f1=$receive->update_rcv_qty('rcv_qty',$rcv_qty[$j], $id[$j],'ap_det');			
			}
		}
	 }
	 
	 if (isset($PHP_s_mat_code))
	{
		for ($i = 0; $i < sizeof($PHP_s_mat_code); $i++)
		{
			if ($PHP_s_rcv[$i] > 0)
			{
				$parm_det = array(	"rcv_num"		=>	$parm['rcv_num'],
														"po_id"			=>	$PHP_s_id[$i],
														"mat_code"	=>	$PHP_s_mat_code[$i],
														"qty"				=>	$PHP_s_rcv[$i],
														"unit"			=>	$PHP_s_po_unit[$i],
														"price"			=>	$PHP_s_price[$i],
														"color"			=>	$PHP_color[$i],
														"po_cat"		=>	'1',
													);
				$f1 = $receive->add_det($parm_det);		
				$f1 = $receive->update_rcv_qty('rcv_qty',$PHP_s_rcv[$i], $PHP_s_id[$i],'ap_special');	
							
			}
		}
	 }
	 $op = $receive->get($parm['rcv_num']);
		$op['back_str'] = "&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=";

		page_display($op, 5, 7, $TPL_RCV_SHOW);
		break;		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_search":
 
		check_authority(5,7,"view");				
		$op = $receive->search(1);
		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc".$SCH_fab;
		page_display($op, 5, 7, $TPL_RCV_LIST);
		break;				
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_view":
 
		check_authority(5,7,"view");				
		$op = $receive->get($PHP_rcv_num);
		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc".$SCH_fab;
		page_display($op, 5, 7, $TPL_RCV_SHOW);
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_edit":
 
		check_authority(5,7,"view");				
		$op = $receive->get($PHP_rcv_num);
		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc".$SCH_fab;
//		$op['tax'] =  $arry2->select($tax,$op['rcv']['tax'],'PHP_tax','select','');	//稅率狀態下拉式
//		$op['cancel'] =  $arry2->select($cancel,$op['rcv']['cancel'],'PHP_cancel','select','');	//抵扣下拉式
//		$op['inv_style'] =  $arry2->select($inv_style,$op['rcv']['inv_style'],'PHP_inv_style','select','');	//發票格式下拉式	



		page_display($op, 5, 7, $TPL_RCV_EDIT);
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_rcvd_edit":
 
		check_authority(5,7,"view");				
		
		if($PHP_inv_date == '0000-00-00' && $PHP_inv_num)
		{
			$inv_date = $TODAY;
		}else{			
			$inv_date = $PHP_inv_date;
		}
		$parm = array (	
										"rcv_date"	=>	$TODAY,
										"rcv_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"inv_num"		=>	$PHP_inv_num,
										"inv_date"	=>	$inv_date,
//										"inv_style"	=>	$PHP_inv_style,
//										"abate"			=>	$PHP_abate,
//										"cancel"		=>	$PHP_cancel,
//										"tax"				=>	$PHP_tax,
										"rcv_num"		=>	$PHP_rcv_num
									 );
		$f1 = $receive->edit($parm);
		if (!isset($PHP_qty))$PHP_qty=array();
		for ($i = 0; $i < sizeof($PHP_qty); $i++)
		{
			if (isset($PHP_qty[$i]) && isset($PHP_rcv_qty[$i]))
			{
				$qty = $PHP_qty[$i] - $PHP_rcv_qty[$i];
				if ($qty  <> 0)
				{
					$f1 = $receive->update_field_id('qty',$PHP_qty[$i],$PHP_det_id[$i],'receive_det');
		
					if ($PHP_po_cat[$i] == '0')
					{
						$id = explode('|',$PHP_po_id[$i]);								
						$s_total=0;
						$tmp=0;
						for ($j=0; $j< sizeof($id); $j++)		//取得每項的請購量,並加總
						{
							$where_str = "id = ".$id[$j];
							$row[$j]=$po->get_det_field('po_qty','ap_det',$where_str);
							$s_total = $s_total + $row[$j]['po_qty'];
						}		
						for ($j=0; $j< (sizeof($id)-1); $j++)		//計算每項採購量(平分)
						{
							$rcv_qty[$j] = $qty * ($row[$j]['po_qty'] / $s_total);
							$tmp = $tmp + $rcv_qty[$j];
						}	
						$tmp_qty =  $qty - $tmp;
						$rcv_qty[$j] = $tmp_qty;
						for ($j=0; $j< sizeof($id); $j++)		//加入DB
							$f1=$receive->update_rcv_qty('rcv_qty',$rcv_qty[$j], $id[$j],'ap_det');						
					}else{			
						$f1 = $receive->update_rcv_qty('rcv_qty',$qty,$PHP_po_id[$i],'ap_special');	
					}
				}
			}
		}
	 
		
		$op = $receive->get($PHP_rcv_num);
		$op['back_str'] = $PHP_back_str;
		$message = "Successfully Edit Receive # : ".$PHP_rcv_num;
		$log->log_add(0,"57E",$message);

		$op['msg'][]=$message;
		if ($PHP_revise == 1)
		{		
			page_display($op, 5, 7, $TPL_RCV_REVISE);
		}else{
			page_display($op, 5, 7, $TPL_RCV_SHOW);
		}
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "del_det_ajax":
		
				if ($PHP_po_cat == '0')
				{
					$id = explode('|',$PHP_po_id);								
					$s_total=0;
					$tmp=0;
					for ($j=0; $j< sizeof($id); $j++)		//取得每項的請購量,並加總
					{
						$where_str = "id = ".$id[$j];
						$row[$j]=$po->get_det_field('po_qty','ap_det',$where_str);
						$s_total = $s_total + $row[$j]['po_qty'];
					}		
					for ($j=0; $j< (sizeof($id)-1); $j++)		//計算每項採購量(平分)
					{
						$rcv_qty[$j] = $PHP_qty * ($row[$j]['po_qty'] / $s_total);
						$tmp = $tmp + $rcv_qty[$j];
					}	
					$tmp_qty =  $PHP_qty - $tmp;
					$rcv_qty[$j] = $tmp_qty;
					for ($j=0; $j< sizeof($id); $j++)		//加入DB
						$f1=$receive->update_rcv_qty('rcv_qty',($rcv_qty[$j]*-1), $id[$j],'ap_det');						
				}else{			
					$f1 = $receive->update_rcv_qty('rcv_qty',($PHP_qty*-1),$PHP_po_id,'ap_special');	
				}
				$f1 = $receive->del_det($PHP_id);
		$message = "Success delete material #:".$PHP_mat_code."On receive #:".$PHP_rcv_num;
		$log->log_add(0,"57E",$message);

		echo $message;
		exit;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_submit":
 
		check_authority(5,7,"view");				
		
		$f1 = $receive->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'receive');
		$f1 = $receive->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'receive');
		$f1 = $receive->update_field_id('status',2,$PHP_id,'receive');
		if ($PHP_ver == 0)$receive->update_field_id('version',1,$PHP_id,'receive');
		$f1 = $receive->check_po_rcvd($PHP_rcv_num);
		$f1 = $receive->check_ord_rcvd($PHP_rcv_num);
		$op = $receive->get($PHP_rcv_num);
		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc".$SCH_fab;

		$message = "Successfully Submitted Receive # : ".$PHP_rcv_num;
		$op['msg'][]="Successfully Submitted Receive # : ".$PHP_rcv_num;
		$log->log_add(0,"57E",$message);

		page_display($op, 5, 7, $TPL_RCV_SHOW);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_revise":
 
		check_authority(5,7,"view");				
		$f1 = $receive->update_field_id('rcv_sub_user','',$PHP_id,'receive');
		$f1 = $receive->update_field_id('rcv_sub_date','',$PHP_id,'receive');
		$f1 = $receive->update_field_id('status',0,$PHP_id,'receive');
		$f1 = $receive->update_field_id('version',($PHP_ver+1),$PHP_id,'receive');
		$f1 = $receive->back_po_rcvd($PHP_rcv_num);
		$f1 = $receive->back_ord_rcvd($PHP_rcv_num);


		$op = $receive->get($PHP_rcv_num);
		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc".$SCH_fab;
//		$op['tax'] =  $arry2->select($tax,$op['rcv']['tax'],'PHP_tax','select','');	//稅率狀態下拉式
//		$op['cancel'] =  $arry2->select($cancel,$op['rcv']['cancel'],'PHP_cancel','select','');	//抵扣下拉式
//		$op['inv_style'] =  $arry2->select($inv_style,$op['rcv']['inv_style'],'PHP_inv_style','select','');	//發票格式下拉式	
		$op['revise'] = 1;
		$msg = "Receive  #:".$PHP_rcv_num." Revised.";
		$log->log_add(0,"57E",$msg);
		$op['msg'][] = $msg;
		page_display($op, 5, 7, $TPL_RCV_EDIT);
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_log_add":
 
		check_authority(5,7,"view");				

	$parm= array(	'rcv_num'		=>	$PHP_rcv_num,
								'item'			=>	'RCV-Rmk.',
								'des'				=>	$PHP_des,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$receive->add_log($parm);
	
		if ($f1)
		{
			$message="Successfully add log :".$PHP_rcv_num;
			$op['msg'][]=$message;
	}else{
		$op['msg']= $apply->msg->get(2);
	}	
		$op = $receive->get($PHP_rcv_num);
		$op['back_str'] = $PHP_back_str;
		page_display($op, 5, 7, $TPL_RCV_SHOW);
		break;		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_print":
 
		check_authority(5,7,"view");				
		$op = $receive->get($PHP_rcv_num);
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
$pdf->ln(); $pdf->ln();
$pdf->rcv_title();
$pdf->ln();
$total =0;$x=0;$po_total=0;$rcv_total=0;
$cur='';$cur_mk=0;$unt='';$unt_mk=0;
for ($i=0; $i< sizeof($op['rcv_det']); $i++)
{
	if ($op['rcv_det'][$i]['po']['currency'] <> $cur)	//確認Currency是否都相同
	{
		if($cur<>"") $cur_mk=1;
		$cur = $op['rcv_det'][$i]['po']['currency'];
	}
	
	if ($op['rcv_det'][$i]['unit'] <> $unt) //確認Unit是否都相同
	{
		if($unt<>"") $unt_mk=1;
		$unt = $op['rcv_det'][$i]['po']['currency'];
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
												'po_num'		=>	$op['rcv_det'][$i]['po']['po_num'],
												'po_qty'		=>	NUMBER_FORMAT($op['rcv_det'][$i]['po']['po_qty'],2),
												'eta'				=>	$op['rcv_det'][$i]['po']['eta'],
												'price'			=>	NUMBER_FORMAT($op['rcv_det'][$i]['price'],2),
												'currency'	=>	$op['rcv_det'][$i]['po']['currency'],
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
$pdf->Cell(134,5,'Sub-Total  :  ',1,0,'R');
if ($unt_mk == 1)
{
	$pdf->Cell(50,5,'',1,0,'R');
	$pdf->Cell(50,5,'',1,0,'R');
}else{
	$pdf->Cell(50,5,NUMBER_FORMAT($po_total,2)." ".$op['rcv_det'][0]['unit'],1,0,'R');
	$pdf->Cell(50,5,NUMBER_FORMAT($rcv_total,2)." ".$op['rcv_det'][0]['unit'],1,0,'R');
}
if ($cur_mk == 1)
{
	$pdf->Cell(46,5,'',1,0,'R');
}else{
	$pdf->Cell(46,5,$cur."@$".NUMBER_FORMAT($total,2),1,0,'R');
}
$pdf->ln();

$v_cost =0;$rmk0='';$rmk1='';
$v_cost = $total * ($vant / 100);
if (isset($op['rcv_log'][0]['des']))$rmk0 = $op['rcv_log'][0]['des'];
$pdf->Cell(14,5,'Remark','RTL',0,'C');
$pdf->Cell(200,5,$rmk0,'RTL',0,'L');
$pdf->SetFont('big5','',10);
$pdf->Cell(20,5,'Vat. : ',1,0,'R');
if ($cur_mk == 1)
{
	$pdf->Cell(46,5,'',1,0,'R');
}else{
	$pdf->Cell(46,5,$cur."@$".NUMBER_FORMAT($v_cost,2),1,0,'R');
}

$pdf->ln();

$total +=$v_cost;
if (isset($op['rcv_log'][1]['des']))$rmk1 = $op['rcv_log'][1]['des'];
$pdf->Cell(14,5,'','RL',0,'C');
$pdf->Cell(200,5,$rmk1,'RL',0,'L');
$pdf->SetFont('big5','',10);
$pdf->Cell(20,5,'Total : ',1,0,'R');
if ($cur_mk == 1)
{
	$pdf->Cell(46,5,'',1,0,'R');
}else{
	$pdf->Cell(46,5,$cur."@$".NUMBER_FORMAT($total,2),1,0,'R');
}
$pdf->ln();
if (isset($op['rcv_log']))
{
	for ($i=2; $i<sizeof($op['rcv_log']); $i++)
	{
		$pdf->Cell(14,5,'','RL',0,'C');
		$pdf->Cell(266,5,$op['rcv_log'][$i]['des'],'RL',0,'C');
		$pdf->ln();
	}
}
$pdf->Cell(14,0,'','RLB',0,'C');
$pdf->Cell(266,0,'','RLB',0,'C');
$pdf->ln();

$pdf->ln();
$pdf->SetFont('Big5','',10);
$pdf->Cell(40,5,' ','0',0,'C');
$pdf->Cell(50,5,'',0,0,'L');

$pdf->Cell(50,5,'APPROVAL : ','0',0,'C');	//PO Approval

$pdf->Cell(50,5,'CONFIRM :',0,0,'L');//PO Confirm
$pdf->Cell(50,5,'Bursary :',0,0,'L');//PO Submit
	
$pdf->Cell(60,5,'Receive :'.$op['rcv']['rcv_sub_user'],0,0,'L');//PA submit


$name=$op['rcv']['rcv_num'].'_rcvd.pdf';
$pdf->Output($name,'D');

		page_display($op, 5, 7, $TPL_RCV_SHOW);
		break;
//-------------------------------------------------------------------------

}   // end case ---------

?>
