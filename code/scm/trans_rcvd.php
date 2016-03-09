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
    case "trans_add_search":
 
		check_authority(5,7,"view");	

		$parm = array("trans_num"	=>	$PHP_trans_num,
									"ship"			=>	$PHP_ship,
									"ord_num"		=>	$PHP_ord_num
									);
		$op = $trans->rcvd_add_search();
	

		$op['back_str'] = "&PHP_trans_num=".$PHP_trans_num."&PHP_ship=".$PHP_ship."&PHP_ord_num=".$PHP_ord_num;
		page_display($op, 5, 7, $TPL_RCV_TRANS_ADD_LIST);
		break;		
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_add":
 
		check_authority(5,7,"view");	
		$op = $trans->get_rev($PHP_trn_num);

		$op['date'] = $TODAY;
		$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

		$op['rcv_num'] = "RT".substr($op['trans']['trn_num'],2);
		

		$op['back_str'] = "&PHP_trans_num=".$PHP_trans_num."&PHP_ship=".$PHP_ship."&PHP_ord_num=".$PHP_ord_num."&PHP_sr_startno=".$PHP_sr_startno;
		page_display($op, 5, 7, $TPL_RCV_TRANS_ADD);
		break;		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_rcv_add":
 
		check_authority(5,7,"view");		

		$parm = array (	
										"rcv_date"	=>	$TODAY,
										"rcv_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"rcv_num"		=>	$PHP_rcv_num,
										"id"				=>	$PHP_id
									 );
									 
		$f1 = $trans->rcv_add($parm);

		foreach($PHP_rcv as $key => $value)	
		{
			if($value > 0) $f1 = $trans->rcv_det_add($value,$key);
		}

  $op = $trans->get_rev($PHP_num);
	$op['back_str'] = "&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&SCH_supl=";
	$message = "successfully append Receive On : ".$parm['rcv_num'];
	$op['msg'][]= $message;
	$log->log_add(0,"57A",$message);
	page_display($op, 5, 7, $TPL_RCV_TRANS_SHOW);
	break;		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_view":
 
		check_authority(5,7,"view");				
		$op = $trans->get_rev($PHP_trn_num);
		if(isset($PHP_sr_no))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_no=".$PHP_sr_no;
		page_display($op, 5, 7, $TPL_RCV_TRANS_SHOW);
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_edit":
 
		check_authority(5,7,"view");	
		$op = $trans->get_rev($PHP_trn_num);
		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_no=".$PHP_sr_no;

		page_display($op, 5, 7, $TPL_RCV_TRANS_EDIT);
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_rcv_edit":
 
		check_authority(5,7,"view");				
//echo $PHP_trn_num;
		foreach($PHP_rcv as $key => $value)	
		{
			if($value > 0) $f1 = $trans->rcv_det_add($value,$key);
		}
		
		$op = $trans->get_rev($PHP_trn_num);
		$op['back_str'] = $PHP_back_str;

		$message = "Successfully Edit Transport Receive # : ".$op['trans']['rcv_num'];
		$log->log_add(0,"57E",$message);

		$op['msg'][]=$message;
		if ($PHP_revise == 1)
		{		
			page_display($op, 5, 7, $TPL_RCV_TRANS_SHOW_REV);
		}else{
			page_display($op, 5, 7, $TPL_RCV_TRANS_SHOW);
		}
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_submit":
 
		check_authority(5,7,"edit");				
		if($PHP_ver == 0) $PHP_ver = 1;
		
		$parm = array (	
										"rcv_date"	=>	$TODAY,
										"rcv_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'rcv_rev'		=>	$PHP_ver,
										"status"		=>	6,
										"id"				=>	$PHP_id,
									 );
									 
		$f1 = $trans->rcv_submit($parm);
		$op = $trans->get_rev($PHP_trn_num);
		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_no=".$PHP_sr_no;

		$message = "Successfully Submitted Receive # : ".$op['trans']['rcv_num'];
		$op['msg'][]=$message;
		$log->log_add(0,"57E",$message);

		page_display($op, 5, 7, $TPL_RCV_TRANS_SHOW);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_revise":
 
		check_authority(5,7,"view");				
		$parm = array (	
										"rcv_date"	=>	'',
										"rcv_user"	=>	'',
										'rcv_rev'		=>	($PHP_ver+1),
										"status"		=>	4,
										"id"				=>	$PHP_id,
									 );
									 
		$f1 = $trans->rcv_submit($parm);


		$op = $trans->get_rev($PHP_trn_num);
//		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
		$op['revise'] = 1;
		$msg = "Receive  #:".$op['trans']['rcv_num']." Revised.";
		$log->log_add(0,"57E",$msg);
		$op['msg'][] = $msg;
		page_display($op, 5, 7, $TPL_RCV_TRANS_EDIT);
		break;
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_print":
 
		check_authority(5,7,"view");				
		$op = $trans->get_rev($PHP_trn_num);
//print_r($op);
	
		if (substr($op['trans_det'][0]['mat_code'],0,1) =='A') {$mat_cat='Accessory';}else{$mat_cat='Fabric';}

		$where_str = " WHERE dept_code = '".$op['trans']['org_fty']."'";
		$dept_name=$dept->get_fields('dept_name',$where_str);
		if (isset($dept_name[0]))$op['trans']['org_fty'] = $dept_name[0];
		
		$where_str = " WHERE dept_code = '".$op['trans']['new_fty']."'";
		$dept_name=$dept->get_fields('dept_name',$where_str);
		if (isset($dept_name[0]))$op['trans']['new_fty'] = $dept_name[0];
		
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_tran_rcvd.php");

$print_title="Receive";
$print_title2 = "VER.".$op['trans']['rcv_num']."   for   ".$mat_cat;
$creator = $op['trans']['rcv_user'];
$mark = $op['trans']['rcv_num'];

$pdf=new PDF_trans_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',14);

$ary_title = array (	'rcv_num'		=>	$op['trans']['rcv_num'],
							'sub_date'		=>	$op['trans']['rcv_sub_date'],
							'sub_user'		=>	$op['trans']['rcv_sub_user'],
							'org_fty'			=>	$op['trans']['org_fty'],
							'new_fty'			=>	$op['trans']['new_fty'],
							'ord_num'			=>	$op['trans']['ord_num']

				);
$pdf->hend_title($ary_title);
$pdf->ln(); $pdf->ln();
$pdf->rcv_title();
$pdf->ln();
$total =0;$x=0;$po_total=0;$rcv_total=0;
//$cur='';$cur_mk=0;
$unt='';$unt_mk=0;

for ($i=0; $i< sizeof($op['trans_det']); $i++)
{
	$rcv_det_ary = array(	
												'mat_code'	=>	$op['trans_det'][$i]['mat_code'],
												'mat_name'	=>	$op['trans_det'][$i]['mat_name'],
												'color'			=>	$op['trans_det'][$i]['color'],
												'trn_qty'		=>	NUMBER_FORMAT($op['trans_det'][$i]['qty'],2),
												'rcv_qty'		=>	NUMBER_FORMAT($op['trans_det'][$i]['rcv_qty'],2),
												'unit'			=>	$op['trans_det'][$i]['unit'],
											);
	$pdf->rcv_det($rcv_det_ary);
	

		if(!isset($u_rcv_qty[$op['trans_det'][$i]['unit']]))
		{
			$u_rcv_qty[$op['trans_det'][$i]['unit']] = 0;
			$u_trn_qty[$op['trans_det'][$i]['unit']] = 0;
			
		}
		$u_rcv_qty[$op['trans_det'][$i]['unit']] += $op['trans_det'][$i]['rcv_qty'];
		$u_trn_qty[$op['trans_det'][$i]['unit']] += $op['trans_det'][$i]['qty'];

		

	
}

$trn_qty = $rcv_qty = '';
foreach ($u_rcv_qty as $key => $value)
{
	$rcv_qty .= 	NUMBER_FORMAT($value,2)." ".$key."+";
}
$rcv_qty = substr($rcv_qty,0,-1);

foreach ($u_trn_qty as $key => $value)
{
	$trn_qty .= 	NUMBER_FORMAT($value,2)." ".$key."+";
}
$trn_qty = substr($trn_qty,0,-1);

$pdf->Cell(178,5,'Sub-Total  :  ',1,0,'R');
$pdf->Cell(48,5,$trn_qty,1,0,'R');
$pdf->Cell(48,5,$rcv_qty,1,0,'R');

$pdf->ln();

$pdf->SetFont('Big5','',10);
$rmk0='';
if (isset($op['rcv_log'][0]['des'])) $rmk0 = $op['rcv_log'][0]['des'];
$pdf->Cell(14,7,'Remark','RTL',0,'C');
$pdf->Cell(260,7,$rmk0,'RTL',0,'L');
$pdf->SetFont('big5','',10);

$pdf->ln();

if (isset($op['rcv_log']))
{
	for ($i=1; $i<sizeof($op['rcv_log']); $i++)
	{

		$pdf->Cell(14,7,'','RL',0,'C');
		$pdf->Cell(260,7,$op['rcv_log'][$i]['des'],'RL',0,'C');
		$pdf->ln();
	}
}
$pdf->Cell(14,0,'','RLB',0,'C');
$pdf->Cell(260,0,'','RLB',0,'C');
$pdf->ln();
$pdf->ln();
$pdf->ln();
$pdf->SetFont('Big5','',10);
$pdf->Cell(10,10,' ','0',0,'C');
$pdf->Cell(40,10,'',0,0,'L');

$pdf->Cell(80,10,'APPROVAL : ','0',0,'C');	//PO Approval

$pdf->Cell(80,10,'CONFIRM :',0,0,'L');//PO Confirm
	
$pdf->Cell(70,10,'Receive :'.$op['trans']['rcv_sub_user'],0,0,'L');//PA submit



$name=$op['trans']['rcv_num'].'_rcvd.pdf';

$pdf->Output($name,'D');

		page_display($op, 5, 7, $TPL_RCV_SHOW);
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "recv_qty_show": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "recv_qty_show":
 
		check_authority(5,7,"view");		
		$table = 0;
		if ($PHP_table =='ap_special') $table=2;
		$op['rcvd']=$receive->get_revd_det($PHP_id,$table);
		$op['unit'] = $PHP_unit;
		page_display($op, 5, 7, $TPL_RCV_RECV_SHOW);
		break;	
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcv_rpt": JOB31 驗收報表主頁
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcv_rpt":
 
		check_authority(5,7,"view");		

		$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
		$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
		$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");


		page_display($op, 5, 7, $TPL_RCVD_RPT);
		break;	

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_rpt_used": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_rpt_used":
 
		check_authority(5,7,"view");	
		if(!$PHP_str && (!$PHP_year || !$PHP_month)) 
		{
			$op['msg'][] ="Please Select Year, Month or choice Start Date.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, 5, 7, $TPL_RCVD_RPT);
			break;
		}
		if(!isset($PHP_cat1))
		{
			$op['msg'][] ="Please Select Fabric or Accessory.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,"","PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, 5, 7, $TPL_RCVD_RPT);
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
		$rcv = $receive->search_rpt_used($parm);
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
			page_display($op, 5, 7, $TPL_RCV_RPT_USED);
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
 
		check_authority(5,7,"view");	
		if(!$PHP_str && (!$PHP_year || !$PHP_month)) 
		{
			$op['msg'][] ="Please Select Year, Month or choice Start Date.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, 5, 7, $TPL_RCVD_RPT);
			break;
		}
		if(!isset($PHP_cat1))
		{
			$op['msg'][] ="Please Select Fabric or Accessory.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, 5, 7, $TPL_RCVD_RPT);
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
		$rcv = $receive->search_rpt_dtl($parm);
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
			page_display($op, 5, 7, $TPL_RCV_RPT_DTL);
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
#		 case "receive": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rev_po_det":
 
		check_authority(5,7,"view");	
		if($PHP_special == 0)
		{					
			$op['rcvd'] = $receive->get_rcv_po_det($PHP_id,$PHP_mat);
		}else if($PHP_special == 1){
			if($PHP_mat == 'l') $op['rcvd'] = $receive->get_rcv_pp_lot($PHP_id);
			if($PHP_mat == 'a') $op['rcvd'] = $receive->get_rcv_pp_acc($PHP_id);
		}else{
			$op['rcvd'] = $receive->get_rcv_po_ext($PHP_id);
		}
		

		page_display($op, 5, 7, $TPL_RCV_PO_SHOW );
		break;				
		
//-------------------------------------------------------------------------

}   // end case ---------

?>
