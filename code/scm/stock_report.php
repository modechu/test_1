<?php
#
#
#
session_start();
// echo $PHP_action.'<br>';
#
#
#
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/class.warehousing.php");
$WAREHOUSING = new WAREHOUSING();
if (!$WAREHOUSING->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
# Template
$TPL_STOCK_REPORT = 'stock_report.html';
$TPL_INVENTORY_STOCK = 'inventory_stock.html';
$TPL_INVENTORY_SEARCH = 'inventory_search.html';
#
#
#
$AUTH = '102';
#
#
#
switch ($PHP_action) {
#
#
#
#
#
#
# :main

default;
case "main":
	check_authority($AUTH,"view");
	
	$ver = $WAREHOUSING->get_ver( $Dept , $Item );
    if( !empty($ver) && $ver['confirm_user'] == '' ){
		
	}
	
	$op['inventory_fty_select'] = $arry2->select($FACTORY,$_SESSION['SCACHE']['ADMIN']['dept'],"PHP_inventory_factory","select","");
	$op['fty_select'] = $arry2->select($FACTORY,$_SESSION['SCACHE']['ADMIN']['dept'],"PHP_factory","select","");
	$op['stock_inventory_diff_fty_select'] = $arry2->select($FACTORY,$_SESSION['SCACHE']['ADMIN']['dept'],"PHP_stock_inventory_diff_factory","select","");
	
	page_display($op,$AUTH,$TPL_STOCK_REPORT);
break;
#
#
#
#
#
#
case "print_inventory":
	check_authority($AUTH,"view");
	
	$op['inventory'] = $WAREHOUSING->get_print_inventory($_POST['PHP_inventory_factory'], $_POST['PHP_mat'], $_POST['PHP_order']);
	
	$ary_title = array (
				'no'      	=>  "NO.",
				'ord'		=>  "ORD.",
				'mat_name' 	=>  "Name",
				'color'   	=>  "Color",
				'l_no'		=>  "D'L",
				'r_no'		=>  "R/no",
				'unit'		=>  "Unit",
				'storage'	=>  "Storage",
				'stock_qty'	=>  "Stock Q'ty",
				'remain_qty'=>  "Remain Q'ty"
				
	);

	include_once($config['root_dir']."/lib/class.pdf_inventory.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"Fabric":"Acc";
	$print_title = "Stock list 盤點表(".$f_a.")";
	$print_title2 = "FOR.".$_POST['PHP_mat'];
	$print_title2 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	// $pdf->hend_title($ary_title);

	$no = '1' ;
	$pdf->SetFont('Big5','',10);

	foreach($op["inventory"] as $key => $val) {
        if($val['po_num']<>''){
			if($no != 1)$pdf->ln();
			if ( ( $pdf->getY() + 6 ) >= 270 ) $pdf->AddPage();
			

			$height = 6;
			
			$pdf->SetFont('Arial','',8);
			
			$pdf->Cell(8,$height,$no,1,0,'C');
			$pdf->Cell(20,$height,$val['ord_num'],1,0,'C');
			$pdf->Cell(40,$height,$val['mat_name'],1,0,'C');
			
			$pdf->SetFont('big5','',8);

			$pdf->Cell(40,$height,$val['color'],1,0,'L');
			$pdf->Cell(10,$height,$val['l_no'],1,0,'C');
			$pdf->Cell(10,$height,$val['r_no'],1,0,'C');
			$pdf->Cell(10,$height,$val['unit'],1,0,'C');
			$pdf->Cell(15,$height,$val['storage'],1,0,'C');
			$pdf->Cell(18,$height,$val['qty'],1,0,'R');
			$pdf->Cell(20,$height,'',1,0,'R');
			
			$no++;
		}
    }

	if($no == 1){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}

	$pdf->Output("inventory.pdf",'D');
	
	print_r($_POST);
break;
#
#
#
#
#
case "print_stock_inventory_diff":
	check_authority($AUTH,"view");
	if(!$_POST['PHP_stock_inventory_diff_order']){
		echo "<script>alert('Input order num!!');history.go(-1);</script>";
		exit;
	}
	$op['inventory'] = $WAREHOUSING->get_print_stock_inventory_diff($_POST['PHP_stock_inventory_diff_factory'], $_POST['PHP_stock_inventory_diff_mat'], $_POST['PHP_stock_inventory_diff_order']);
	
	$ary_title = array (
				'no'      	=>  "NO.",
				'ord'		=>  "Order.",
				'mat_name' 	=>  "Name",
				'color'   	=>  "Color",
				'l_no'		=>  "Lot",
				'r_no'		=>  "Roll",
				'unit'		=>  "Unit",
				'storage'	=>  "Storage",
				'scm_qty'	=>  "SCM Q'ty",
				'final_qty'	=>  "Stock Q'ty",
	);

	include_once($config['root_dir']."/lib/class.pdf_stock_inventory_diff.php");
	
	$f_a = $_POST['PHP_stock_inventory_diff_mat']=="lots"?"Fabric":"Acc";
	$print_title = "庫存盤點差異表(".$f_a.")";
	$print_title2 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	// $pdf->hend_title($ary_title);

	$no = '1' ;
	$pdf->SetFont('Big5','',10);

	foreach($op["inventory"] as $key => $val) {
        if($val['po_num']<>''){
			if($no != 1)$pdf->ln();
			if ( ( $pdf->getY() + 6 ) >= 270 ) $pdf->AddPage();
			

			$height = 6;
			
			$pdf->SetFont('Arial','',8);
			
			$pdf->Cell(8,$height,$no,1,0,'C');
			$pdf->Cell(20,$height,$val['ord_num'],1,0,'C');
			$pdf->Cell(40,$height,$val['mat_name'],1,0,'C');
			
			$pdf->SetFont('big5','',8);

			$pdf->Cell(40,$height,$val['color'],1,0,'L');
			$pdf->Cell(10,$height,$val['l_no'],1,0,'C');
			$pdf->Cell(10,$height,$val['r_no'],1,0,'C');
			$pdf->Cell(10,$height,$val['unit'],1,0,'C');
			$pdf->Cell(15,$height,$val['storage'],1,0,'C');
			$pdf->Cell(18,$height,$val['qty'],1,0,'C');
			$pdf->Cell(20,$height,$val['cfm_qty'],1,0,'R');
			
			$no++;
		}
    }

	if($no == 1){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'Finish inventory first!',0,0,'L');
	}

	$pdf->Output("stock_inventory.pdf",'D');
	
	print_r($_POST);
break;
#
#
#
#
#
#
case "print_stock":

	check_authority($AUTH,"view");
	
	$op['stock'] = $WAREHOUSING->get_print_stock($_POST['PHP_factory'], $_POST['PHP_mat'], $_POST['PHP_begin_date'], $_POST['PHP_end_date']);
	/* print_r($op['stock']);
	exit; */
	$ary_title = array (
				'no'      	=>  "NO.",
				'mat_name' 	=>  "Name",
				'color'   	=>  "Color",
				'l_no'		=>  "D'L",
				'r_no'		=>  "R/no",
				'unit'		=>  "Unit",
				'storage'	=>  "Storage",
				'currency'	=>  "currency",
				'price'		=>  "price",
				'amount'	=>  "price"
				
	);

	include_once($config['root_dir']."/lib/class.pdf_stock.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "庫存日報表(".$_POST['PHP_factory'].$f_a.")";
	$print_title2 = "期間 ".$_POST['PHP_begin_date']."~".$_POST['PHP_end_date'];
	$print_title3 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('L','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);

	$pdf->ln();
	
	
	$no = 1 ;
	$pdf->SetFont('Big5','',10);

	foreach($op["stock"] as $key => $val) {
        if($val['po_num']<>''){
			if($no != 1)$pdf->ln();
			if ( ( $pdf->getY() + 6 ) >= 190 ){
				$pdf->AddPage();
				$pdf->ln();
			}

			$height = 6;
			
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(7,$height,$no,1,0,'C');
			$pdf->Cell(45,$height,$val['mat_name'],1,0,'C');
			
			$pdf->SetFont('big5','',8);

			$pdf->Cell(50,$height,$val['color'],1,0,'L');
			$pdf->Cell(10,$height,$val['size'],1,0,'C');
			$pdf->Cell(10,$height,$val['c_no'],1,0,'C');
			$pdf->Cell(10,$height,$val['l_no'],1,0,'C');
			$pdf->Cell(10,$height,$val['r_no'],1,0,'C');
			$pdf->Cell(15,$height,$val['qty'],1,0,'R');
			$pdf->Cell(12,$height,$val['unit'],1,0,'C');
			$pdf->Cell(18,$height,$val['storage'],1,0,'C');
			$pdf->Cell(20,$height,$val['currency'],1,0,'C');
			$pdf->Cell(20,$height,$val['prics'],1,0,'C');
			$pdf->Cell(20,$height,number_format($val['prics']*$val['qty'],'2','',''),1,0,'C');
			
			$no++;
		}
    }
	
	if($no == 1){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	$pdf->Output("stock.pdf",'D');
	
break;
#
#
#
#
#
#
case "check_inventory":

	check_authority($AUTH,"view");
	
	$ver = $WAREHOUSING->get_ver( $_POST['PHP_inventory_factory'], $_POST['PHP_mat'] );
    
	if( !empty($ver) && $ver['confirm_user'] == '' ){
		echo 0;
	}else{
		echo 1;
	}
	
break;
#
#
#
#
#
#
case "print_stock_detail":

	check_authority($AUTH,"view");
	if(empty($_POST['PHP_detail_begin_date']) or empty($_POST['PHP_detail_end_date'])){
		echo "<script>alert('請選擇日期區間');history.go(-1);</script>";
		exit;
	}
	$op['stock_detail'] = $WAREHOUSING->get_print_stock_detail($_POST['PHP_factory'], $_POST['PHP_mat'], $_POST['PHP_detail_begin_date'], $_POST['PHP_detail_end_date']);
	
	include_once($config['root_dir']."/lib/class.pdf_stock_detail.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "庫存明細帳(".$_POST['PHP_factory'].$f_a.")";
	$print_title2 = "期間 ".$_POST['PHP_detail_begin_date']."~".$_POST['PHP_detail_end_date'];
	$print_title3 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);

	$pdf->ln();
	
	
	$pdf->SetFont('Big5','',10);

	foreach($op["stock_detail"] as $k => $v) {
		$diff_qty = 0;
		if($v['inventory_i']){
			foreach($v['inventory_i'] as $key => $val){
				$diff_qty += $val['qty'];
				if ( ( $pdf->getY() + 6 ) >= 270 ){
					$pdf->AddPage();
					$pdf->ln();
				}
				
				$height = 6;
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(35,$height,$val['mat_name'],0,0,'C');
				$pdf->SetFont('big5','',8);
				$pdf->Cell(45,$height,$val['color'],0,0,'C');
				$pdf->Cell(10,$height,$val['size'],0,0,'C');
				$pdf->Cell(25,$height,substr($val['open_date'],0,10),0,0,'C');
				$pdf->Cell(25,$height,"驗收",0,0,'C');
				$pdf->Cell(25,$height,$val['qty'],0,0,'C');
				$pdf->ln();
			}
		}
		
		if($v['inventory_r']){
			foreach($v['inventory_r'] as $key => $val){
				$diff_qty -= $val['qty'];
				if ( ( $pdf->getY() + 6 ) >= 270 ){
					$pdf->AddPage();
					$pdf->ln();
				}
				
				$height = 6;
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(35,$height,$val['mat_name'],0,0,'C');
				$pdf->SetFont('big5','',8);
				$pdf->Cell(45,$height,$val['color'],0,0,'C');
				$pdf->Cell(10,$height,$val['size'],0,0,'C');
				$pdf->Cell(25,$height,substr($val['open_date'],0,10),0,0,'C');
				$pdf->Cell(25,$height,"發料",0,0,'C');
				$pdf->Cell(25,$height,$val['qty'],0,0,'C');
				$pdf->ln();
			}
		}
		
		if($v['inventory_b']){
			foreach($v['inventory_b'] as $key => $val){
				$diff_qty += $val['qty'];
				if ( ( $pdf->getY() + 6 ) >= 270 ){
					$pdf->AddPage();
					$pdf->ln();
				}
				
				$height = 6;
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(35,$height,$val['mat_name'],0,0,'C');
				$pdf->SetFont('big5','',8);
				$pdf->Cell(45,$height,$val['color'],0,0,'C');
				$pdf->Cell(10,$height,$val['size'],0,0,'C');
				$pdf->Cell(25,$height,substr($val['open_date'],0,10),0,0,'C');
				$pdf->Cell(25,$height,"退料",0,0,'C');
				$pdf->Cell(25,$height,$val['qty'],0,0,'C');
				$pdf->ln();
			}
		}
		# 小計
		$pdf->Cell(35,$height,"小計",0,0,'C');
		$pdf->Cell(45,$height,"期初數量",0,0,'C');
		$pdf->Cell(10,$height,$v['begin_qty'],0,0,'L');
		$pdf->Cell(25,$height,"異動數量",0,0,'C');
		$pdf->Cell(25,$height,$diff_qty,0,0,'L');
		$pdf->Cell(25,$height,"結存",0,0,'C');
		$pdf->Cell(20,$height,$v['begin_qty']+$diff_qty,0,0,'L');
		
		$pdf->ln();
		
		$y=$pdf->GetY();
		// $pdf->SetLineWidth(0.5);
		$pdf->Line(10,$pdf->GetY(),194,$pdf->GetY());
		// $pdf->SetLineWidth(0.1);
		$pdf->SetY($y);
	}
	
	if(empty($op["stock_detail"])){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	$pdf->Output("stock_detail.pdf",'D');
	
break;
#
#
#
#
#
#
case "print_stock_diff_detail":

	check_authority($AUTH,"view");
	if(empty($_POST['PHP_diff_detail_begin_date']) or empty($_POST['PHP_diff_detail_end_date'])){
			echo "<script>alert('請選擇日期區間');history.go(-1);</script>";
			exit;
	}
	$op['stock_detail'] = $WAREHOUSING->get_print_stock_diff_detail($_POST['PHP_factory'], $_POST['PHP_mat'], $_POST['PHP_diff_detail_begin_date'], $_POST['PHP_diff_detail_end_date']);
	//exit;
	//print_r($op['stock_detail']);
	//exit;
	include_once($config['root_dir']."/lib/class.pdf_stock_diff_detail.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "庫存異動表(".$_POST['PHP_factory'].$f_a.")";
	$print_title2 = "期間 ".$_POST['PHP_diff_detail_begin_date']."~".$_POST['PHP_diff_detail_end_date'];
	$print_title3 = "印列日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);

	$pdf->ln();
	
	
	$pdf->SetFont('Big5','',10);
	//print_r($op["stock_detail"]);
	//exit;
	$stock_detail_new = array();
	$tmp_date='';
	$tmp_matcat='';
	$tmp_qty=0;
	$tmp_key1='';
	$tmp_key2='';
	$tmp_matcat='';
	$tmp_matid='';
	$tmp_color='';
	$tmp_size='';
	$tmp_unit='';
	$tmp_matname='';

	foreach($op["stock_detail"] as $key1 => $val1)
	{
		foreach($val1 as $key2 => $val2)
		{
			for($j=0;$j<sizeof($val2);$j++)
			{
				if($tmp_date == substr($val2[$j]['open_date'],0,10))
				{
					if($tmp_key2==$key2)
					{
						if( $tmp_key1==$key1)
						{
							//日期相同
							$tmp_qty += $val2[$j]['qty'];
							
						}
						else
						{
							//日期相同，項目相同，顏色不同
							//加總完成
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_cat'] = $tmp_matcat;
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_id'] = $tmp_matid;
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['color'] = $tmp_color;
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['size'] = $tmp_size;
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['unit'] = $tmp_unit;
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_name'] = $tmp_matname;
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['open_date'] = $tmp_date;
							
							$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['qty'] = $tmp_qty;
							$tmp_qty = 0;
							$tmp_qty += $val2[$j]['qty'];
							$tmp_key1=$key1;
							$tmp_matcat=$val2[$j]['mat_cat'];
							$tmp_matid=$val2[$j]['mat_id'];
							$tmp_color=$val2[$j]['color'];
							$tmp_size=$val2[$j]['size'];
							$tmp_unit=$val2[$j]['unit'];
							$tmp_matname=$val2[$j]['mat_name'];
							
						}
					}
					else
					{
						//日期相同，項目不同
						//加總完成
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_cat'] = $tmp_matcat;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_id'] = $tmp_matid;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['color'] = $tmp_color;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['size'] = $tmp_size;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['unit'] = $tmp_unit;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_name'] = $tmp_matname;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['open_date'] = $tmp_date;
						
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['qty'] = $tmp_qty;
						$tmp_qty = 0;
						$tmp_qty += $val2[$j]['qty'];
						$tmp_key2=$key2;
						$tmp_matcat=$val2[$j]['mat_cat'];
						$tmp_matid=$val2[$j]['mat_id'];
						$tmp_color=$val2[$j]['color'];
						$tmp_size=$val2[$j]['size'];
						$tmp_unit=$val2[$j]['unit'];
						$tmp_matname=$val2[$j]['mat_name'];
					}		
				}
				else
				{
					//日期不同
					if($tmp_date !='')
					{
						//日期不同且日期不能等於空字串
						//加總完成
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_cat'] = $tmp_matcat;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_id'] = $tmp_matid;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['color'] = $tmp_color;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['size'] = $tmp_size;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['unit'] = $tmp_unit;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['mat_name'] = $tmp_matname;
						
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['open_date'] = $tmp_date;
						$stock_detail_new[$tmp_key1][$tmp_key2][$tmp_date]['qty'] = $tmp_qty;
						$tmp_qty = 0;
						$tmp_key2=$key2;
						$tmp_key1=$key1;
						$tmp_qty += $val2[$j]['qty'];
						$tmp_date = substr($val2[$j]['open_date'],0,10);
						$tmp_matcat=$val2[$j]['mat_cat'];
						$tmp_matid=$val2[$j]['mat_id'];
						$tmp_color=$val2[$j]['color'];
						$tmp_size=$val2[$j]['size'];
						$tmp_unit=$val2[$j]['unit'];
						$tmp_matname=$val2[$j]['mat_name'];
					}
					else
					{
						//初登場
						$tmp_date = substr($val2[$j]['open_date'],0,10);
						$tmp_qty = 0;
						$tmp_qty += $val2[$j]['qty'];
						$tmp_key2=$key2;
						$tmp_key1=$key1;
						$tmp_matcat=$val2[$j]['mat_cat'];
						$tmp_matid=$val2[$j]['mat_id'];
						$tmp_color=$val2[$j]['color'];
						$tmp_size=$val2[$j]['size'];
						$tmp_unit=$val2[$j]['unit'];
						$tmp_matname=$val2[$j]['mat_name'];
					}
				}
			}
		}
	}
	//print_r($stock_detail_new);
	//exit;
	foreach($stock_detail_new as $key => $val) {
		
			foreach($val['inventory_i'] as $ikey => $ival)
			{
				if ( ( $pdf->getY() + 6 ) >= 270 ){
					$pdf->AddPage();
					$pdf->ln();
				}
				
				$height = 6;
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(35,$height,$ival['mat_name'],0,0,'C');
				$pdf->SetFont('Big5','',8);
				$pdf->Cell(45,$height,$ival['color'],0,0,'C');
				$pdf->Cell(10,$height,$ival['size'],0,0,'C');
				$pdf->Cell(25,$height,$ikey,0,0,'C');
				$pdf->Cell(25,$height,"驗收",0,0,'C');
				$pdf->Cell(25,$height,$ival['qty'],0,0,'C');
				$pdf->ln();
			}
			foreach($val['inventory_r'] as $rkey => $rval)
			{
				if ( ( $pdf->getY() + 6 ) >= 270 ){
					$pdf->AddPage();
					$pdf->ln();
				}
				
				$height = 6;
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(35,$height,$rval['mat_name'],0,0,'C');
				$pdf->SetFont('Big5','',8);
				$pdf->Cell(45,$height,$rval['color'],0,0,'C');
				$pdf->Cell(10,$height,$rval['size'],0,0,'C');
				$pdf->Cell(25,$height,$rkey,0,0,'C');
				$pdf->Cell(25,$height,"發料",0,0,'C');
				$pdf->Cell(25,$height,$rval['qty'],0,0,'C');
				$pdf->ln();
			}
			foreach($val['inventory_b'] as $bkey => $bval)
			{
				if ( ( $pdf->getY() + 6 ) >= 270 ){
					$pdf->AddPage();
					$pdf->ln();
				}
				
				$height = 6;
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(35,$height,$bval['mat_name'],0,0,'C');
				$pdf->SetFont('Big5','',8);
				$pdf->Cell(45,$height,$bval['color'],0,0,'C');
				$pdf->Cell(10,$height,$bval['size'],0,0,'C');
				$pdf->Cell(25,$height,$bkey,0,0,'C');
				$pdf->Cell(25,$height,"退料",0,0,'C');
				$pdf->Cell(25,$height,$bval['qty'],0,0,'C');
				$pdf->ln();
			}
		
		$y=$pdf->GetY();
		// $pdf->SetLineWidth(0.5);
		$pdf->Line(10,$pdf->GetY(),194,$pdf->GetY());
		// $pdf->SetLineWidth(0.1);
		$pdf->SetY($y);
	}
	/* foreach($op["stock_detail"] as $key => $val) {
		$diff_qty = 0;
		if($val['inventory_i']){
			$diff_qty += $val['inventory_i']['qty'];
			if ( ( $pdf->getY() + 6 ) >= 270 ){
				$pdf->AddPage();
				$pdf->ln();
			}
			
			$height = 6;
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(35,$height,$val['inventory_i']['mat_name'],0,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(45,$height,$val['inventory_i']['color'],0,0,'C');
			$pdf->Cell(10,$height,$val['inventory_i']['size'],0,0,'C');
			$pdf->Cell(25,$height,substr($val['inventory_i']['open_date'],0,10),0,0,'C');
			$pdf->Cell(25,$height,"驗收",0,0,'C');
			$pdf->Cell(25,$height,$val['inventory_i']['qty'],0,0,'C');
			$pdf->ln();
		}
		
		if($val['inventory_r']){
			$diff_qty -= $val['inventory_r']['qty'];
			if ( ( $pdf->getY() + 6 ) >= 270 ){
				$pdf->AddPage();
				$pdf->ln();
			}
			
			$height = 6;
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(35,$height,$val['inventory_r']['mat_name'],0,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(45,$height,$val['inventory_r']['color'],0,0,'C');
			$pdf->Cell(10,$height,$val['inventory_r']['size'],0,0,'C');
			$pdf->Cell(25,$height,substr($val['inventory_r']['open_date'],0,10),0,0,'C');
			$pdf->Cell(25,$height,"發料",0,0,'C');
			$pdf->Cell(25,$height,$val['inventory_r']['qty'],0,0,'C');
			$pdf->ln();
		}
		
		if($val['inventory_b']){
			$diff_qty += $val['inventory_b']['qty'];
			if ( ( $pdf->getY() + 6 ) >= 270 ){
				$pdf->AddPage();
				$pdf->ln();
			}
			
			$height = 6;
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(35,$height,$val['inventory_b']['mat_name'],0,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(45,$height,$val['inventory_b']['color'],0,0,'C');
			$pdf->Cell(10,$height,$val['inventory_b']['size'],0,0,'C');
			$pdf->Cell(25,$height,substr($val['inventory_b']['open_date'],0,10),0,0,'C');
			$pdf->Cell(25,$height,"退料",0,0,'C');
			$pdf->Cell(25,$height,$val['inventory_b']['qty'],0,0,'C');
			$pdf->ln();
		}
		
		$y=$pdf->GetY();
		// $pdf->SetLineWidth(0.5);
		$pdf->Line(10,$pdf->GetY(),194,$pdf->GetY());
		// $pdf->SetLineWidth(0.1);
		$pdf->SetY($y);
	} */
	
	if(empty($op["stock_detail"])){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	$pdf->Output("庫存異動表.pdf",'D');
	
break;
#
#
#
#
#
#
case "print_stock_i_detail":

	check_authority($AUTH,"view");
	
	$op['inventory'] = $WAREHOUSING->get_print_stock_type_detail($_POST['PHP_factory'], "i", $_POST['PHP_mat'], $_POST['PHP_i_detail_begin_date'], $_POST['PHP_i_detail_end_date'], $_POST['PHP_i_order']);
	include_once($config['root_dir']."/lib/class.pdf_stock_i_detail.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "驗收明細表(".$_POST['PHP_factory'].$f_a.")";
	$print_title2 = "期間 ".$_POST['PHP_i_detail_begin_date']."~".$_POST['PHP_i_detail_end_date'];
	$print_title3 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);
	$pdf->ln();
	
	$pdf->SetFont('Big5','',10);
	foreach($op["inventory"] as $key => $val) {
		if ( ( $pdf->getY() + 6 ) >= 270 ){
			$pdf->AddPage();
			$pdf->ln();
		}
		
		$height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(20,$height,$val['ord_num'],0,0,'C');
		$pdf->Cell(40,$height,$val['mat_name'],0,0,'C');
		$pdf->SetFont('big5','',8);
		$pdf->Cell(55,$height,$val['color'],0,0,'C');
		$pdf->Cell(10,$height,$val['size'],0,0,'C');
		$pdf->Cell(10,$height,$val['r_no'],0,0,'C');
		$pdf->Cell(10,$height,$val['l_no'],0,0,'C');
		$pdf->Cell(20,$height,substr($val['open_date'],0,10),0,0,'C');
		/* $pdf->Cell(20,$height,"驗收",0,0,'C'); */
		$pdf->Cell(20,$height,$val['qty'],0,0,'C');
		$pdf->ln();
	}
	
	if(empty($op["inventory"])){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	$pdf->Output("驗收明細表.pdf",'D');
	
break;
#
#
#
#
#
#
case "print_stock_r_detail":

	check_authority($AUTH,"view");
	/* print_r($_POST);
	exit; */
	$op['inventory'] = $WAREHOUSING->get_print_stock_type_detail($_POST['PHP_factory'], "r", $_POST['PHP_mat'], $_POST['PHP_r_detail_begin_date'], $_POST['PHP_r_detail_end_date'], $_POST['PHP_r_order']);
	
	include_once($config['root_dir']."/lib/class.pdf_stock_r_detail.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "發料明細表(".$_POST['PHP_factory'].$f_a.")";
	$print_title2 = "期間 ".$_POST['PHP_r_detail_begin_date']."~".$_POST['PHP_r_detail_end_date'];
	$print_title3 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);

	$pdf->ln();
	
	
	$pdf->SetFont('Big5','',10);

	foreach($op["inventory"] as $key => $val) {
		
			if ( ( $pdf->getY() + 6 ) >= 270 ){
				$pdf->AddPage();
				$pdf->ln();
			}
			
			$height = 6;
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(20,$height,$val['ord_num'],0,0,'C');
			$pdf->Cell(40,$height,$val['mat_name'],0,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(55,$height,$val['color'],0,0,'C');
			$pdf->Cell(10,$height,$val['size'],0,0,'C');
			$pdf->Cell(10,$height,$val['r_no'],0,0,'C');
			$pdf->Cell(10,$height,$val['l_no'],0,0,'C');
			$pdf->Cell(20,$height,substr($val['open_date'],0,10),0,0,'C');
			/* $pdf->Cell(25,$height,"發料",0,0,'C'); */
			$pdf->Cell(20,$height,$val['qty'],0,0,'C');
			$pdf->ln();
		
	}
	
	if(empty($op["inventory"])){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	$pdf->Output("發料明細表.pdf",'D');
	
break;
#
#
#
#
#
#
case "print_stock_b_detail":

	check_authority($AUTH,"view");

	$op['inventory'] = $WAREHOUSING->get_print_stock_type_detail($_POST['PHP_factory'], "b", $_POST['PHP_mat'], $_POST['PHP_b_detail_begin_date'], $_POST['PHP_b_detail_end_date']);
	
	include_once($config['root_dir']."/lib/class.pdf_stock_b_detail.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "退料明細表(".$_POST['PHP_factory'].$f_a.")";
	$print_title2 = "期間 ".$_POST['PHP_b_detail_begin_date']."~".$_POST['PHP_b_detail_end_date'];
	$print_title3 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('P','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);

	$pdf->ln();
	
	
	$pdf->SetFont('Big5','',10);
	
	
	foreach($op["inventory"] as $key => $val) {
		
			if ( ( $pdf->getY() + 6 ) >= 270 ){
				$pdf->AddPage();
				$pdf->ln();
			}
			
			$height = 6;
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(35,$height,$val['mat_name'],0,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(45,$height,$val['color'],0,0,'C');
			$pdf->Cell(10,$height,$val['size'],0,0,'C');
			$pdf->Cell(10,$height,$val['r_no'],0,0,'C');
			$pdf->Cell(10,$height,$val['l_no'],0,0,'C');
			$pdf->Cell(25,$height,substr($val['open_date'],0,10),0,0,'C');
			$pdf->Cell(25,$height,"退料",0,0,'C');
			$pdf->Cell(25,$height,$val['qty'],0,0,'C');
			$pdf->ln();
		
	}

	if(empty($op["inventory"])){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	$pdf->Output("退料明細表.pdf",'D');
	
break;
#
#
#
#
#
case "print_month_lots_detail":

check_authority($AUTH,"view");

$op['lots_rcv_detail'] = $WAREHOUSING->get_print_lots_rcv_detail();

include_once($config['root_dir']."/lib/class.month_lots_detail.php");

	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "訂單別入庫數量統計表(".$f_a.")";
	$print_title2 = " 期間 ".$_POST['PHP_lots_detail_begin_date']."~".$_POST['PHP_lots_detail_end_date'];
	
	$pdf=new PDF_stock('L','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);

	$pdf->ln();
	
	
	$pdf->SetFont('Big5','',10);
	//print_r($op["lots_rcv_detail"] );
	//exit;
	foreach($op["lots_rcv_detail"] as $key => $val) {
		$cell_height = 6;
		
		
		if(count($val["order"]) > 1)
		{				
			foreach($val["order"] as $subkey => $subval)
			{
				if ( ( $pdf->getY() + 6 ) >= 190 )
				{
					$pdf->AddPage();
					$pdf->ln();
				}
				//$pdf->Cell(30,$cell_height,$key,'LTR',0,'C');
				if($subkey==0)
				{
					$pdf->Cell(30,$cell_height,$val['po_num'],'LTR',0,'C');
					$pdf->Cell(30,$cell_height,$subval['order_num'],1,0,'C');
					if($_POST['PHP_mat']=='lots')
					{
						$pdf->Cell(40,$cell_height,$val['lots_name'],'LTR',0,'C');
					}
					else
					{
						$pdf->Cell(40,$cell_height,$val['acc_name'],'LTR',0,'C');
					}
					$pdf->Cell(75,$cell_height,$val['color'],'LTR',0,'C');
					if($val['size']=='')
					{
						$pdf->Cell(20,$cell_height,$val['size'],'LTR',0,'C');
					}
					else
					{
						$pdf->Cell(20,$cell_height,$val['size'],1,0,'C');
					}
					//$_POST['PHP_mat']=="lots"?"主料":"副料";
					//change_unit_qty($val['po_unit'], $_POST['PHP_mat']=="lots"?"yd":"pc", $val['po_qty']);
					$qty = change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['qty']);
					//echo $qty;
					//exit;
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['qty']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['qty']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,$subval['qty']==''?'0'.' '.$val['unit']:$subval['qty'].' '.$val['unit'],1,0,'R');
					//$pdf->Cell(25,$cell_height,$val['po_qty']==''?'0'.' '.$val['unit']:$val['po_qty'].' '.$val['unit'],'LTR',0,'R');
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_qty']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$val['unit']:$subval['percentage_qty'].' '.$val['unit'],1,0,'R');
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_rcvqty_total']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$val['unit']:$subval['percentage_rcvqty_total'].' '.$val['unit'],1,0,'R');
				}
				elseif($subkey==(count($val["order"])-1))
				{
					$pdf->Cell(30,$cell_height,'','LBR',0,'C');
					$pdf->Cell(30,$cell_height,$subval['order_num'],1,0,'C');
					$pdf->Cell(40,$cell_height,'','LBR',0,'C');
					$pdf->Cell(75,$cell_height,'','LBR',0,'C');
					if($val['size']=='')
					{
						$pdf->Cell(20,$cell_height,$val['size'],'LBR',0,'C');
					}
					else
					{
						$pdf->Cell(20,$cell_height,$val['size'],1,0,'C');
					}
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['qty']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['qty']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,'','LBR',0,'C');
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_qty']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$val['unit']:$subval['percentage_qty'].' '.$val['unit'],1,0,'R');
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_rcvqty_total']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$val['unit']:$subval['percentage_rcvqty_total'].' '.$val['unit'],1,0,'R');
					
					//$pdf->Cell(25,$cell_height,$subval['percentage_qty'].$val['unit'],1,0,'C');
					//$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total'].$val['unit'],1,0,'C');
				}
				else
				{
					$pdf->Cell(30,$cell_height,'','LR',0,'C');
					$pdf->Cell(30,$cell_height,$subval['order_num'],1,0,'C');
					$pdf->Cell(40,$cell_height,'','LR',0,'C');
					$pdf->Cell(75,$cell_height,'','LR',0,'C');
					if($val['size']=='')
					{
						$pdf->Cell(20,$cell_height,$val['size'],'LR',0,'C');
					}
					else
					{
						$pdf->Cell(20,$cell_height,$val['size'],1,0,'C');
					}
					//$qty = change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['qty']);
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['qty']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['qty']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,'','LR',0,'C');
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_qty']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$val['unit']:$subval['percentage_qty'].' '.$val['unit'],1,0,'R');
					$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_rcvqty_total']),2);
					$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
					$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
					//$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$val['unit']:$subval['percentage_rcvqty_total'].' '.$val['unit'],1,0,'R');
					
				}
				$pdf->ln();
			}
		}
		else
		{
			foreach($val["order"] as $subkey => $subval)
			{
				if ( ( $pdf->getY() + 6 ) >= 190 )
				{
					$pdf->AddPage();
					$pdf->ln();
				}
				//$pdf->Cell(30,$cell_height,$key,'LTR',0,'C');
				$pdf->Cell(30,$cell_height,$val['po_num'],1,0,'C');
				$pdf->Cell(30,$cell_height,$val['order'][0]['order_num'],1,0,'C');
				if($_POST['PHP_mat']=='lots')
				{
					$pdf->Cell(40,$cell_height,$val['lots_name'],1,0,'C');
				}
				else
				{
					$pdf->Cell(40,$cell_height,$val['acc_name'],1,0,'C');
				}
				
				$pdf->Cell(75,$cell_height,$val['color'],1,0,'C');
				$pdf->Cell(20,$cell_height,$val['size'],1,0,'C');
				//$pdf->Cell(25,$cell_height,$val['po_qty'].' '.$val['unit'],1,0,'R');
				//$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$val['unit']:$subval['percentage_qty'].' '.$val['unit'],1,0,'R');
				//$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$val['unit']:$subval['percentage_rcvqty_total'].' '.$val['unit'],1,0,'R');
				//number_format($subval['qty']/$po_qty,5);
				$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['qty']),2);
				$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
				$pdf->Cell(25,$cell_height,$subval['qty']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');
				//$pdf->Cell(25,$cell_height,$subval['qty']==''?'0'.' '.$val['unit']:$subval['qty'].' '.$val['unit'],1,0,'R');
				$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_qty']),2);
				$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
				$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$unit:$qty .' '.$unit,1,0,'R');
				//$pdf->Cell(25,$cell_height,$subval['percentage_qty']==''?'0'.' '.$val['unit']:$subval['percentage_qty'].' '.$val['unit'],1,0,'R');
				$qty = number_format(change_unit_qty($val['unit'],$_POST['PHP_mat']=="lots"?"yd":"pc",$subval['percentage_rcvqty_total']),2);
				$unit=$_POST['PHP_mat']=="lots"?"yd":"pc";
				$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$unit:$qty.' '.$unit,1,0,'R');		
				//$pdf->Cell(25,$cell_height,$subval['percentage_rcvqty_total']==''?'0'.' '.$val['unit']:$subval['percentage_rcvqty_total'].' '.$val['unit'],1,0,'R');
				$pdf->ln();
			}
		}
		
	
	}
	
	if(empty($op["stock_detail"])){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	$output_name = "order_stockin_detail_".$_POST['PHP_lots_detail_begin_date']."_".$_POST['PHP_lots_detail_end_date'].".pdf";
	
	$pdf->Output($output_name,'D');
	
break;
#
#
#
#
#
#
case "print_order_mat_cost":
	check_authority($AUTH,"view");
	
	if(empty($_POST['PHP_order_mat_cost_begin_date']) or empty($_POST['PHP_order_mat_cost_end_date'])){
		echo "<script>alert('請選擇日期區間');history.go(-1);</script>";
		exit;
	}
	$op['order_cost'] = $WAREHOUSING->get_print_order_mat_cost($_POST['PHP_factory'], $_POST['PHP_mat'], $_POST['PHP_order_mat_cost_begin_date'], $_POST['PHP_order_mat_cost_end_date']);
	/* print_r($op['order_cost']);
	exit; */  
	include_once($config['root_dir']."/lib/class.pdf_order_cost.php");
	
	$f_a = $_POST['PHP_mat']=="lots"?"主料":"副料";
	$print_title = "原物料進銷存統計表(".$_POST['PHP_factory'].$f_a.")";
	$print_title2 = "期間 ".$_POST['PHP_order_mat_cost_begin_date']."~".$_POST['PHP_order_mat_cost_end_date'];
	$print_title3 = "列印日期：".date("Y-m-d H:i:s");
	
	$pdf=new PDF_stock('L','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	//$pdf->hend_title($ary_title);

	$pdf->ln();
	
	$pdf->SetFont('Big5','',10);
	/* print_r($op['order_cost']);
	exit;  */
	foreach($op["order_cost"] as $ord_num => $ord_val) {
		/* print_r($ord_val);
		echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<br>"; */
		if(sizeof($op["order_cost"][$ord_num]) > 0)
		{
			$ttl_ap_amount = $ttl_po_amount = $ttl_i_amount = $ttl_r_amount = $m= 0;
			$ttl_ap_qty = $ttl_po_qty = $ttl_i_qty = $ttl_r_qty = 0;
			foreach($ord_val as $key => $val) {
				
				if ( ( $pdf->getY() + 6 ) >= 190 ){
					$pdf->AddPage();
					$pdf->ln();
				}

				$height = 6;
				
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,7,$m==0?$ord_num:'',0,0,'C');
				$pdf->Cell(35,7,$val['mat_name'],0,0,'C');
				$pdf->SetFont('Big5','',8);
				$pdf->Cell(25,7,substr($val['color'],0,12),0,0,'C');
				//$pdf->Cell(25,7,$val['color'],0,0,'C');
				$pdf->Cell(25,7,$val['ap_qty']." ".$val['unit'],0,0,'C');
				$ttl_ap_qty += $val['ap_qty'];
				$ap_amount = number_format($val['ap_qty'] * change_unit_price($val['prc_unit'], $val['unit'], $val['prics']) * ($val['currency'] == 'NTD' ? 1 : $val['rate']), 0, '', '');
				$ttl_ap_amount += $ap_amount;
				$pdf->Cell(25,7,$ap_amount,0,0,'C');
				$pdf->Cell(25,7,$val['po_qty']." ".$val['po_unit'],0,0,'C');
				$ttl_po_qty += change_unit_qty($val['po_unit'], $_POST['PHP_mat']=="lots"?"yd":"pc", $val['po_qty']);
				$po_amount = number_format($val['po_amount'] * ($val['currency'] == 'NTD' ? 1 : $val['rate']), 2, '', '');
				$ttl_po_amount += $po_amount;
				$pdf->Cell(25,7,$po_amount,0,0,'C');
				$pdf->Cell(25,7,$val['i_qty']." ".$val['po_unit'],0,0,'C');
				/* echo $val['i_qty'];
				echo "=="; */
				$ttl_i_qty += $a = change_unit_qty($val['po_unit'], $_POST['PHP_mat']=="lots"?"yd":"pc", $val['i_qty']);
				/* echo $ttl_i_qty;
				echo ","; */
				
				$i_amount = number_format($val['i_qty'] * change_unit_price($val['prc_unit'], $val['unit'], $val['prics']) * ($val['currency'] == 'NTD' ? 1 : $val['rate']), 2, '', '');
				$ttl_i_amount += $i_amount;
				$pdf->Cell(25,7,$i_amount,0,0,'C');
				$pdf->Cell(25,7,$val['r_qty'],0,0,'C');
				$ttl_r_qty += change_unit_qty($val['po_unit'], $_POST['PHP_mat']=="lots"?"yd":"pc", $val['r_qty']);
				$r_amount = number_format($val['r_qty'] * change_unit_price($val['prc_unit'], $val['unit'], $val['prics']) * ($val['currency'] == 'NTD' ? 1 : $val['rate']), 2, '', '');
				$ttl_r_amount += $r_amount;
				$pdf->Cell(25,7,$r_amount,0,0,'C');
				
				$pdf->ln();
				$m++;
				/* echo $a;
				echo "+";  */ 
			}
			/* echo '='.$ttl_i_qty; */
			/* echo $ttl_i_qty;
			echo "<br>*****************************<br>";  */
			$pdf->SetFont('Big5','',10);
			$pdf->Cell(25,7,'小計',0,0,'C');
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(35,7,'',0,0,'C');
			$pdf->Cell(25,7,'',0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_ap_qty, 2, '.', ',')." ".($_POST['PHP_mat']=="lots"?"yd":"pc"),0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_ap_amount, 2, '.', ','),0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_po_qty, 2,'.', ',')." ".($_POST['PHP_mat']=="lots"?"yd":"pc"),0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_po_amount, 2, '.', ','),0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_i_qty, 2, '.', ',')." ".($_POST['PHP_mat']=="lots"?"yd":"pc"),0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_i_amount, 2, '.', ','),0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_r_qty, 2, '.', ',')." ".($_POST['PHP_mat']=="lots"?"yd":"pc"),0,0,'C');
			$pdf->Cell(25,7,number_format($ttl_r_amount, 2, '.', ','),0,0,'C');
			$pdf->ln();
			
			$y=$pdf->GetY();
			$pdf->Line(10,$pdf->GetY(),295,$pdf->GetY());
			$pdf->SetY($y);
		}
    }
	//exit;
	if($no == 1){
		$pdf->SetFont('Arial','',12);
		$pdf->ln();
		$pdf->Cell(40,$height,'No Records',0,0,'L');
	}
	
	$pdf->Output("原物料進銷存統計表.pdf",'D');
	
break;
#
#
#
#
#
#
} # CASE END
?>
