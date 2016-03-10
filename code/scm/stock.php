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
$TPL_STOCK = 'stock.html';
#
#
#
$AUTH = '086';
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
case "stock":
check_authority($AUTH,"view");
	
	$M_now_num = isset($_GET['PHP_sr_startno'])? $_GET['PHP_sr_startno'] : '' ;
	$M_mat = isset($_GET['PHP_mat'])? $_GET['PHP_mat'] : "";
	$M_mat_name = isset($_GET['PHP_mat_name'])? $_GET['PHP_mat_name'] : "";
	
	if($_GET['PHP_fty']){
		$fty = $_GET['PHP_fty'];
	}elseif( $_SESSION['SCACHE']['ADMIN']['dept'] == "LY" || $_SESSION['SCACHE']['ADMIN']['dept'] == "CF" ){
		$fty = $_SESSION['SCACHE']['ADMIN']['dept'];
	}else{
		$fty = "LY";
	}
		
	$parm = array(
		'action'        => "stock" , 
		'fty'			=> $fty,
		'mat'			=> $M_mat,
		'mat_name'		=> $M_mat_name,
		'sr_startno'    => $M_now_num , 
		'page_num'      => 20 
	);
	
	if(isset($M_mat)){
		$op = $WAREHOUSING->stock_search($parm);
	}
	$op['fty_select'] = $arry2->select($FACTORY,$fty,"PHP_factory","select","");
	if( $_SESSION['SCACHE']['ADMIN']['dept'] == "LY" || $_SESSION['SCACHE']['ADMIN']['dept'] == "CF" ){
		$op['fty_select'] = $_SESSION['SCACHE']['ADMIN']['dept'];
	}
	
	$mat_ary_key = array("Fab", "Acc");
	$mat_ary_val = array("lots", "acc");
	$op['mat_select'] = $arry2->select($mat_ary_key,$M_mat,"PHP_mat","select","",$mat_ary_val);
	
	$op['SCH_fty'] = $fty;
	$op['SCH_mat'] = $M_mat;
	$op['SCH_mat_name'] = $M_mat_name;
	
	if(in_array(strtolower($_SESSION['SCACHE']['ADMIN']['login_id']), $_SESSION['STOCK_USER'])){
		$utf_field['utf_field'] = $WAREHOUSING->get_utf_field_name($_SESSION['SCACHE']['ADMIN']['dept'], $AUTH);
	}

	$utf_field_size = sizeof($utf_field['utf_field']);
	for( $i=0; $i<$utf_field_size; $i++){
		$op[$utf_field['utf_field'][$i]['field_name']] = $utf_field['utf_field'][$i]['field_value'];
	}
	
	page_display($op,$AUTH,$TPL_STOCK);
break;
#
#
#
#
#
#
case "do_storage_edit":
	check_authority($AUTH,"edit");
	
	$id = $_GET['PHP_stock_id'];
	$mat = $_GET['PHP_mat'];
	$storage = $_GET['PHP_storage'];
	echo $WAREHOUSING->update_storage( $id, $mat, $storage );
	
break;
#
#
#
#
#
case "stock_print":
	check_authority($AUTH,"view");
	
	$op['stock'] = $WAREHOUSING->get_stock2pdf($_GET['PHP_fty'], $_GET['PHP_mat']);
	
	include_once($config['root_dir']."/lib/class.pdf_stock.php");
	
	$print_title = "STOCK LIST";
	$print_title2 = "FOR.".$_GET['PHP_mat'];
	
	$pdf=new PDF_stock('L','mm','A4');
	$pdf->AddBig5Font();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(1);
	$pdf->hend_title($ary_title);

	$pdf->SetFont('big5','',10);

	$pdf->ln();
	$pdf->SetFont('big5','B',8);
	$pdf->Cell(7,7,'No#',1,0,'C');
	$pdf->Cell(45,7,'Name',1,0,'C');
	$pdf->Cell(45,7,'Color',1,0,'C');
	$pdf->Cell(10,7,'Size',1,0,'C');
	$pdf->Cell(10,7,'C/no',1,0,'C');
	$pdf->Cell(10,7,'D/L',1,0,'C');
	$pdf->Cell(10,7,'R/no',1,0,'C');
	$pdf->Cell(15,7,'Qty',1,0,'C');
	$pdf->Cell(10,7,'Unit',1,0,'C');
	$pdf->Cell(20,7,'Storage',1,0,'C');
	$pdf->ln();

	$no = '1' ;
	$pdf->SetFont('Big5','',10);

	foreach($op["stock"] as $key => $val) {
        if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		$height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(7,$height,$no,1,0,'C');
		$pdf->Cell(45,$height,$val['mat_name'],1,0,'C');
		
		$pdf->SetFont('big5','',8);

		$pdf->Cell(45,$height,$val['color'],1,0,'L');
		$pdf->Cell(10,$height,$val['size'],1,0,'C');
		$pdf->Cell(10,$height,$val['c_no'],1,0,'C');
		$pdf->Cell(10,$height,$val['l_no'],1,0,'C');
		$pdf->Cell(10,$height,$val['r_no'],1,0,'C');
		$pdf->Cell(15,$height,$val['qty'],1,0,'R');
		$pdf->Cell(10,$height,$val['unit'],1,0,'c');
		$pdf->Cell(20,$height,$val['storage'],1,0,'C');
		
		$no++;
    }


// $pdf->ln();
// # 粗線
// $y=$pdf->GetY();
// $pdf->SetLineWidth(0.5);
// $pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
// $pdf->SetLineWidth(0.1);
// $pdf->SetY($y);

// $pdf->SetLineWidth(0.5);
// $pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
// $pdf->SetLineWidth(0.1);
// $pdf->SetY($y);

// $pdf->ln();
// $pdf->ln();

// $pdf->SetFont('big5','B',10);
// $pdf->Cell(118,7,"核准：",0,0,'R');
// $pdf->Cell(21,7,"",0,0,'L');
// $pdf->Cell(21,7,"覆核：",0,0,'R');
// $pdf->Cell(21,7,"",0,0,'L');
// $pdf->Cell(21,7,"主管：",0,0,'R');
// $pdf->Cell(21,7,"",0,0,'L');
// $pdf->Cell(21,7,"經辦：",0,0,'R');
// $pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

// $pdf->SetFont('Arial','',8);
// $pdf->SetX();
// $pdf->SetY(195);
// $pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


// $pdf->SetY($oy);
// $pdf->SetFont('big5','B',8);
// for($s=0;$s<sizeof($op['apb_log']);$s++){
	// $pdf->SetX(20);
	// $pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
// }

$pdf->Output("aaa.pdf",'D');
	
break;
#
#
#
#
#
} # CASE END
?>
