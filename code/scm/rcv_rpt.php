<?php
session_start();

session_register	('sch_parm');
session_register	('sch_stock_parm');
require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];
$add_det = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14);

require_once "init.object.php";


$op = array();


switch ($PHP_action) {
//=======================================================


#++++++++++++++    Fabric +++++++++++++++++++  2009/01/01  +++++++++++++++++
#		 job 91    主料開發 
#++++++++++++++++++++++++++++++++++++++++++++  2009/01/01  +++++++++++++++++
#		case "fabric":		job 91
#		case "fabric_search": job 91
#		case "fabric_add":
#		case "do_fabric_add":
#		case "fabric_view":
#		case "fabric_edit":
#		case "do_fabric_edit":
#		case "fabric_del":
#		case "fabric_excel":
###################################################

//-----------------------------------------------------------------------------------
//		JOB  9-1   研究開發 [ 布料開發]
//		case "fabric":
//-----------------------------------------------------------------------------------
    case "lots_rpt":
		check_authority('044',"view");				
		$dept=$GLOBALS['SCACHE']['ADMIN']['dept'];
		
		$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
		$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
		$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
		$op['manager_flag']=1;
		for ($i=0; $i<sizeof($SHIP); $i++)
		{
			if ($dept==$SHIP[$i])
			{
				$op['manager_flag']=0;
				$op['fty']= $dept;
			}
		}		
		$where_str = "where supl_cat = 'FABRIC' ORDER BY vndr_no";
		$sup=$supl->get_fields('vndr_no',$where_str);
		$where_str = "where supl_cat = 'FABRIC' ORDER BY vndr_no";
		$sup_name=$supl->get_fields('supl_s_name',$where_str);

		if(!$sup)$sup=array('');
		if(!$sup_name) $sup_name=array();
		$op['sup_no'] = $arry2->select($sup_name,"","PHP_sup","select","",$sup);


		$where_str="order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}

		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 



				
		if (isset($PHP_msg))$op['msg'][]=$PHP_msg;

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
 $test = array();

		page_display($op, '044', $TPL_LOTS_RPT);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "lots_add_search": JOB58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_add_search":
 
		check_authority('044',"view");	

		if(isset($PHP_po))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"PHP_po"				=>  $PHP_po,
													"PHP_sup"				=>  $PHP_sup,
													"PHP_ship"			=>	$PHP_ship,
													"PHP_cust"			=>	$PHP_cust,													
													"PHP_sr_startno"	=>	$PHP_sr_startno,
													"PHP_action"			=>	$PHP_action
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
			}


		$op = $rcv_rpt->add_search(1);
		

		page_display($op, '044', $TPL_LR_PO_LIST);
		break;	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_add_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_add_view":
		check_authority('044',"add");	
 //echo $PHP_aply_num;
	$log_where = "AND item <> 'special'";	
	$op = $po->get($PHP_aply_num,$log_where);
	if (isset($op['ap_det']))
	{
				$op['ap_det2']=$po->grout_ap($op['ap_det']);
	}
	
	

	$ary=array('A','B','C','D','E','F','G','H','I');
	for ($i=0; $i<sizeof($ary); $i++)
	{
		if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
	}
				
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}

	
	if (isset($PHP_message)) $op['msg'][]=$PHP_message;
	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
	page_display($op, '044', $TPL_LR_ADD_SHOW);	
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_add":
		check_authority('044',"add");	
 //echo $PHP_aply_num;
 		$fab_rec = $lots->get('', $PHP_lots_code);
		$op['lr']	 = array(
												'po_num'		=>	$PHP_po,
												'cust'			=>	$PHP_cust,
												'lots_code'	=>	$PHP_lots_code,
												'color'			=>	$PHP_color,
												'po_cat'		=>	$PHP_po_cat,
												'po_spare'	=>	$PHP_po_spare,
												'supl_no'		=>	$PHP_supl_no,
												'dept'			=>	$PHP_fty,
												'op_date'		=>	date('Y-m-d'),
												'name'			=>	$fab_rec['lots_name'],
												'width'			=>	$fab_rec['width'],
												'unit'			=>	$PHP_unit,
												);
												
		$op['ap_num']	= $PHP_ap;
		$op['unit']	= $arry2->select($LOTS_PRICE_UNIT,$PHP_unit,'PHP_unit','select','');
		$op['add_det'] = $add_det;
	page_display($op, '044', $TPL_LR_ADD);	
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_lots_rpt_add":
		check_authority('044',"add");	
 //echo $PHP_aply_num;
 		if(!$PHP_roll || !$PHP_org_yd || !$PHP_yd || !$PHP_width)
 		{
 			$message = "Please input roll no, dyed lot, width or yardage first";
 			if(!$PHP_id)
 			{
				$fab_rec = $lots->get('', $PHP_lots_code);
				$op['lr']	 = array(
												'po_num'		=>	$PHP_po,
												'cust'			=>	$PHP_cust,
												'lots_code'	=>	$PHP_lots_code,
												'color'			=>	$PHP_color,
												'po_cat'		=>	$PHP_po_cat,
												'po_spare'	=>	$PHP_po_spare,
												'dept'			=>	$PHP_fty,
												'supl_no'		=>	$PHP_supl_no,
												'op_date'		=>	date('Y-m-d'),
												'name'			=>	$fab_rec['lots_name'],
												'width'			=>	$fab_rec['width']
												);	 				
				$op['unit']	= $arry2->select($LOTS_PRICE_UNIT,$PHP_unit,'PHP_unit','select','');

 			}else{
 				$op = $rcv_rpt->get($PHP_id);
				$op['unit']	= $arry2->select($LOTS_PRICE_UNIT,$op['lr']['unit'],'PHP_unit','select','');
 			}
 			$op['msg'][] = $message;
			$op['add_det'] = $add_det;
 			page_display($op, '044', $TPL_LR_ADD);
 			break;
 		}
 		
 		if(!$PHP_id)
 		{
			$parm	 = array(
												'po_num'		=>	$PHP_po,
												'cust'			=>	$PHP_cust,
												'lots_code'	=>	$PHP_lots_code,
												'color'			=>	$PHP_color,
												'po_cat'		=>	$PHP_po_cat,
												'po_spare'	=>	$PHP_po_spare,
												'op_date'		=>	date('Y-m-d'),
												'qty'				=>	$PHP_qty,
												'dept'			=>	$PHP_fty,
												'supl_no'		=>	$PHP_supl_no,
												'unit'			=>	$PHP_unit,
												); 			
			$head="FI".date('y')."-";	//RP+日期=驗收單開頭
			$fr_num = $parm['fr_num'] = $apply->get_no($head,'fr_num','fab_rpt');	//取得驗收的最後編號
			$f1 = $rcv_rpt->add($parm);			
			$message = "Success append  Fabric Inspection Report [".$fr_num."]";	
			$log->log_add(0,"58A",$message);
			$PHP_id = $f1;									
 		}else{
 			$parm = array('value'	=>	$PHP_qty , 'field'	=>	'qty', 'id'	=>	$PHP_id);
 			$f1 = $rcv_rpt->update_field($parm);	
 			$parm = array('value'	=> $PHP_unit, 'field'	=>'unit'	, 'id'	=>	$PHP_id);
 			$f1 = $rcv_rpt->update_field($parm);	

 		}
 		
 		if(strstr($PHP_rmk,'&#'))	$PHP_rmk = $ch_cov->check_cov($PHP_rmk);
		$parm = array(	'roll_no'		=>	$PHP_roll,
										'dyed_lot'	=>	$PHP_dyed,
										'fab_width'	=>	$PHP_width,
										'org_yd'		=>	$PHP_org_yd,
										'rmk'				=>	$PHP_rmk,
										'fab_yd'		=>	$PHP_yd,
										'fr_id'			=>	$PHP_id,
										);
		$parm['defect_1'] = $parm['defect_2'] = $parm['defect_3'] = $parm['defect_4'] = '';			
		$parm['ttl_point_1'] = $parm['ttl_point_2'] = $parm['ttl_point_3'] = $parm['ttl_point_4'] = 0;
 		for($i=0; $i<sizeof($PHP_ad1); $i++)
 		{
 			$parm['defect_1'] .= $PHP_ad1[$i].',';
 			$parm['defect_2'] .= $PHP_ad2[$i].',';
 			$parm['defect_3'] .= $PHP_ad3[$i].',';
 			$parm['defect_4'] .= $PHP_ad4[$i].',';
 			$parm['ttl_point_1'] += $PHP_ad1[$i];
 			$parm['ttl_point_2'] += ($PHP_ad2[$i] * 2);
 			$parm['ttl_point_3'] += ($PHP_ad3[$i] * 3);
 			$parm['ttl_point_4'] += ($PHP_ad4[$i] * 4);
 		}
 		$parm['point_h_yd'] = ($parm['ttl_point_1'] + $parm['ttl_point_2'] + $parm['ttl_point_3'] + $parm['ttl_point_4'])*36/$PHP_width/$PHP_yd*100;

		$f1 = $rcv_rpt->add_det($parm);

		$op = $rcv_rpt->get($PHP_id);
		
		$rcv_rpt->avg_point($PHP_id,$op['lr_det']);  //計算point平均
												
		$op['msg'][] = "Success append roll no : [".$PHP_roll."] on Fabric Inspection Report [".$op['lr']['fr_num']."]";
		$op['add_det'] = $add_det;

		$op['unit']	= $arry2->select($LOTS_PRICE_UNIT,$op['lr']['unit'],'PHP_unit','select','');

	if(isset($PHP_from_edit))
	{
		page_display($op, '044', $TPL_LR_EDIT);	
	}else{
		page_display($op, '044', $TPL_LR_ADD);	
	}		
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_lots_rpt_edit":
		check_authority('044',"add");	

 		if(strstr($PHP_rmk,'&#'))	$PHP_rmk = $ch_cov->check_cov($PHP_rmk);
		$parm = array(	'roll_no'		=>	$PHP_roll,
										'dyed_lot'	=>	$PHP_dyed,
										'fab_width'	=>	$PHP_width,
										'org_yd'		=>	$PHP_org_yd,
										'fab_yd'		=>	$PHP_yd,
										'rmk'				=>	$PHP_rmk,
										'id'				=>	$PHP_det_id
										);
		$parm['defect_1'] = $parm['defect_2'] = $parm['defect_3'] = $parm['defect_4'] = '';			
		$parm['ttl_point_1'] = $parm['ttl_point_2'] = $parm['ttl_point_3'] = $parm['ttl_point_4'] = 0;
 		for($i=0; $i<sizeof($PHP_d1); $i++)
 		{
 			$parm['defect_1'] .= $PHP_d1[$i].',';
 			$parm['defect_2'] .= $PHP_d2[$i].',';
 			$parm['defect_3'] .= $PHP_d3[$i].',';
 			$parm['defect_4'] .= $PHP_d4[$i].',';
 			$parm['ttl_point_1'] += $PHP_d1[$i];
 			$parm['ttl_point_2'] += ($PHP_d2[$i] * 2);
 			$parm['ttl_point_3'] += ($PHP_d3[$i] * 3);
 			$parm['ttl_point_4'] += ($PHP_d4[$i] * 4);
 		}
 		$parm['point_h_yd'] = ($parm['ttl_point_1'] + $parm['ttl_point_2'] + $parm['ttl_point_3'] + $parm['ttl_point_4'])*36/$PHP_width/$PHP_yd*100;

		$f1 = $rcv_rpt->edit_det($parm);

 		$parm = array('value'	=> $PHP_qty, 'field'	=>	'qty', 'id'	=>	$PHP_id);
 		$f1 = $rcv_rpt->update_field($parm);	
		$parm = array('value'	=> $PHP_unit, 'field'	=>'unit'	, 'id'	=>	$PHP_id);
		$f1 = $rcv_rpt->update_field($parm);	


		$op = $rcv_rpt->get($PHP_id);
		$rcv_rpt->avg_point($PHP_id,$op['lr_det']);  //計算point平均

		$op['unit']	= $arry2->select($LOTS_PRICE_UNIT,$op['lr']['unit'],'PHP_unit','select','');
	
														
		$op['msg'][] = "Success edit roll no : [".$PHP_roll."] on Fabric Inspection Report [".$op['lr']['fr_num']."]";
		$op['add_det'] = $add_det;
	if(isset($PHP_from_edit))
	{
		page_display($op, '044', $TPL_LR_EDIT);	
	}else{
		page_display($op, '044', $TPL_LR_ADD);	
	}
	break;	
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_del":
		check_authority('044',"add");	

		$f1 = $rcv_rpt->del_det($PHP_det_id,$PHP_id);

		if($f1 == 'FULL_DEL')
		{
			$message = 'Success del receiving fabrit report : ['.$PHP_lr_num.']';
			$log->log_add(0,"58D",$message);	
			$redir_str = 'rcv_rpt.php?PHP_action=lots_rpt&PHP_msg='.$message;
			redirect_page($redir_str);

		}

		$op = $rcv_rpt->get($PHP_id);
		$rcv_rpt->avg_point($PHP_id,$op['lr_det']);  //計算point平均
		$op['unit']	= $arry2->select($LOTS_PRICE_UNIT,$op['lr']['unit'],'PHP_unit','select','');
														
		$op['msg'][] = "Success del roll no : [".$PHP_roll."] on Fabric Inspection Report [".$op['lr']['fr_num']."]";
		$op['add_det'] = $add_det;
	if(isset($PHP_from_edit))
	{
		page_display($op, '044', $TPL_LR_EDIT);	
	}else{
		page_display($op, '044', $TPL_LR_ADD);	
	}
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_add_fish":
		check_authority('044',"add");	


 		$parm = array('value'	=> $PHP_qty, 'field'	=>'qty'	, 'id'	=>	$PHP_id);
 		$f1 = $rcv_rpt->update_field($parm);	
		$parm = array('value'	=> $PHP_unit, 'field'	=>'unit'	, 'id'	=>	$PHP_id);
		$f1 = $rcv_rpt->update_field($parm);	

		$sch_parm = array();
		$sch_parm = array(	"SCH_fab"				=>  '',
//													"PHP_sup"				=>  $PHP_sup,
												"PHP_ship"			=>	'',
												"PHP_cust"			=>	'',
												"SCH_num"				=>	'',
												"PHP_sr_startno"	=>	1,
												"PHP_action"			=>	''
											);


		$op = $rcv_rpt->get($PHP_id);
												
		$message = "Success Append Fabric Inspection Report [".$op['lr']['fr_num']."]";
		$op['msg'][] = $message;
		$op['add_det'] = $add_det;
	page_display($op, '044', $TPL_LR_SHOW);	
	break;		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "lots_add_search": JOB58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_search":
 
		check_authority('044',"view");	

		if(isset($SCH_fab))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"SCH_fab"				=>  $SCH_fab,
													"PHP_sup"				=>  $PHP_sup,
													"PHP_ship"			=>	$PHP_ship,
//													"PHP_cust"			=>	$PHP_cust,
													"SCH_num"				=>	$SCH_num,
													"SCH_po"				=>	$SCH_po,
													"PHP_sr_startno"	=>	$PHP_sr_startno,
													"PHP_action"			=>	$PHP_action
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
			}


		$op = $rcv_rpt->search(1);
		$op['msg']= $rcv_rpt->msg->get(2);
		page_display($op, '044', $TPL_LR_LIST);
		break;		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_view":
		check_authority('044',"view");	

		$op = $rcv_rpt->get($PHP_id);
												
		$op['add_det'] = $add_det;
	page_display($op, '044', $TPL_LR_SHOW);	
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_edit":
		check_authority('044',"edit");	

		$op = $rcv_rpt->get($PHP_id);
												
		$op['add_det'] = $add_det;
		$op['unit']	= $arry2->select($LOTS_PRICE_UNIT,$op['lr']['unit'],'PHP_unit','select','');

	page_display($op, '044', $TPL_LR_EDIT);	
	break;			
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_lots_rpt_edit_finish":
		check_authority('044',"edit");	


 		$parm = array('value'	=> $PHP_qty, 'field'	=>	'qty', 'id'	=>	$PHP_id);
 		$f1 = $rcv_rpt->update_field($parm);	
		$parm = array('value'	=> $PHP_unit, 'field'	=>'unit'	, 'id'	=>	$PHP_id);
		$f1 = $rcv_rpt->update_field($parm);	

		$op = $rcv_rpt->get($PHP_id);

											
		$message = "Success Update Fabric Inspection Report [".$op['lr']['fr_num']."]";
		$log->log_add(0,"58E",$message);

		$op['msg'][] = $message;
		$op['add_det'] = $add_det;
	page_display($op, '044', $TPL_LR_SHOW);	
	break;			
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lots_rpt_add":			job 58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_submit":
		check_authority('044',"edit");	


 		$parm = array('value'	=>	'2' , 'field'	=>	'status', 'id'	=>	$PHP_id);
 		$f1 = $rcv_rpt->update_field($parm);	

 		$parm = array('value'	=> date('Y-m-d'), 'field'	=>	'sub_date', 'id'	=>	$PHP_id);
 		$f1 = $rcv_rpt->update_field($parm);	

 		$parm = array('value'	=> $GLOBALS['SCACHE']['ADMIN']['login_id'], 'field'	=>	'sub_user', 'id'	=>	$PHP_id);
 		$f1 = $rcv_rpt->update_field($parm);	

		$op = $rcv_rpt->get($PHP_id);

												
		$message = "Success SUBMIT Fabric Inspection Report [".$op['lr']['fr_num']."]";
		$log->log_add(0,"58E",$message);

		$op['msg'][] = $message;
		$op['add_det'] = $add_det;
	page_display($op, '044', $TPL_LR_SHOW);	
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lots_rpt_pdf":
 
		check_authority('044',"view");				
		$op = $rcv_rpt->get($PHP_id);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_rcv_rpt.php");

$print_title="Fabric Inspection Report : [".$op['lr']['fr_num']."]";
$print_title2="INSP DATE : [".$op['lr']['op_date']."]";
$creator = $op['lr']['sub_user_name'];
$mark = $op['lr']['fr_num'];
$ary_title = $op['lr'];

$pdf=new PDF_rcv_rpt('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',14);

$mk = 0; // 26
for($i=0; $i<sizeof($op['lr_det']); $i++)
{
	$mk+=10;
	if($mk > 26)
	{
		$pdf->AddPage();	
		$mk = 0;
	}
	$parm = array('roll'			=>	$op['lr_det'][$i]['roll_no'],
								'dyed_lot'	=>	$op['lr_det'][$i]['dyed_lot'],
								'org_w'			=>	$op['lr']['width'],
								'width'			=>	$op['lr_det'][$i]['fab_width'],
								'ord_y'			=>	$op['lr_det'][$i]['org_yd'],
								'yd'				=>	$op['lr_det'][$i]['fab_yd'],
								);
	$pdf->rpt_title($parm);
	$pdf->rpt_det($op['lr_det'][$i]);
	if($op['lr_det'][$i]['rmk'])
	{
		$mk = $pdf->rpt_rmk($op['lr_det'][$i]['rmk_view'],$mk);
	}else{
		$y = $pdf->GetY();
		$pdf->SetY($y+3);
	}
}
							

$name=$op['lr']['fr_num'].'_rpt.pdf';
$pdf->Output($name,'D');

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "lots_add_search": JOB58
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "lr_ord_search":
 
		check_authority('044',"view");	

		if(isset($SCH_ord))
		{
			
			$sch_parm = array();
			$sch_parm = array(	"SCH_ord"				=>  $SCH_ord,
				);
			}


		$op['lr'] = $rcv_rpt->search_by_ord();
		$op['msg']= $rcv_rpt->msg->get(2);
		$op['order'] = $SCH_ord;
		page_display($op, '044', $TPL_LR_ORD_LIST);
		break;			
					
//-------------------------------------------------------------------------

}   // end case ---------

?>
