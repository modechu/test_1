<?php
	require('chinese.php');
	require('fpdf.php');
        define('FPDF_FONTPATH','font/');
	
class order_pdf{
	var $pdf;
	function init($op) {
	$this->pdf= new PDF_Chinese('P','pt','A4');
	$pdf=$this->pdf;
	$pdf->AddBig5Font();
	$pdf->AliasNbPages();
	$pdf->Open();	
	$pdf->AddPage();
	$pdf->text(250,60,"Cost analysis");
	$pdf->ln();
	$today=date('Y-m-d');
	$pdf->SetFont('Arial','',14);
	$pdf->text(50,90,"Order# : ".$op['ord_base']['order_num']."               ETD : ".$op['ord_base']['ETD']."               Print date : ".$today);
	$pdf->ln();
	$pdf->SetFont('Arial','B',60);
	$pdf->waterline($op['ord_base']['order_num']);
	$pdf->SetFont('Big5','',14);
	$pdf->SetY(110);
	$pdf->cell(550,20,'Order record :',1,0,'L');
	$pdf->ln();	
	$long1=array('Customer name ',$op['ord_base']['cust'],'Agent',$op['ord_base']['agent']);
	$size1= array(125,150,125,150);
	$pdf->full_fields($long1,$size1,20);
	$pdf->ln();
	$long2=array('Goods name ',$op['ord_base']['name'],'Style #',$op['ord_base']['style_num']);
	$size2= array(125,150,125,150);
	$pdf->full_fields($long2,$size2,20);
	$pdf->ln();
	$long3=array('Amount/dz ',$op['ord_base']['amount'],'Season',$op['ord_base']['season']);
	$size3= array(125,150,125,150);
	$pdf->full_fields($long3,$size3,20);
	$pdf->ln();
	$long4=array('Extra work area ',$op['ord_base']['source'],'Export area',$op['ord_base']['ex_area']);
	$size4= array(125,150,125,150);
	$pdf->full_fields($long4,$size4,20);
	$pdf->ln();
	$long5=array('factory name ',$op['ord_base']['vendor'],'Payment_term',$op['ord_base']['ex_area']);
	$size5= array(125,150,125,150);
	$pdf->full_fields($long5,$size5,20);
	$pdf->ln();
	if ($op['ord_base']['gmr'])
	{
		$long6=array('Mark UP ',$op['ord_base']['gmr']."%",'Currency',$op['ord_base']['currency']);
	}else{
		$long6=array('Mark UP ',$op['ord_base']['gm'],'Currency',$op['ord_base']['currency']);		
	}
	$size6= array(125,150,125,150);
	$pdf->full_fields($long6,$size6,20);
	$pdf->ln();
	$long7=array('Quantity / dz',$op['ord_base']['quantity'],' ',' ');
	$size7= array(125,150,125,150);
	$pdf->full_fields($long7,$size7,20);
	$pdf->ln();

	$pdf->ln();

	
	
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,'price(unit)',1,0,'R');
	$pdf->Cell(100,20,"USA$".$op['ord_base']['price'],1,0,'R');
	$pdf->ln();
	
	$pdf->Cell(100,20,'Fabric Cost',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,"USA$".$op['fab_est'],1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();

	$pdf->Cell(100,20,'Accessory Cost',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,"USA$".$op['acc_est'],1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();

	$pdf->Cell(100,20,'Custom Cost',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,"USA$".$op['ord_base']['custom_est'],1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();

	if (!isset($op['ord_exw']['msg']))
	{
	for ($i=0; $i < sizeof($op['ord_exw']); $i++)
	{
			$pdf->Cell(100,20,$op['ord_exw'][$i]['ex_work'],1,0,'L');
			$pdf->Cell(100,20,'',1,0,'L');
			$pdf->Cell(100,20,'',1,0,'R');
			$pdf->Cell(150,20,"USA$".$op['ord_exw'][$i]['u_price'],1,0,'R');
			$pdf->Cell(100,20,'',1,0,'R');
			$pdf->ln();
	}
	}

	$pdf->Cell(100,20,'C.M',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,"USA$".$op['ord_base']['cm'],1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();
	
	$pdf->Cell(100,20,'other',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,"USA$".$op['ord_base']['other_cost'],1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();
	
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'QTA#',1,0,'L');
	$pdf->Cell(100,20,"USA$".$op['ord_base']['qta'],1,0,'R');
	$pdf->Cell(150,20,'',1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();

	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'Overhead',1,0,'L');
	$pdf->Cell(100,20,"USA$".$op['ord_base']['overhead'],1,0,'R');
	$pdf->Cell(150,20,'',1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();

	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'Commission',1,0,'L');
	$pdf->Cell(100,20,"USA$".$op['ord_base']['commission'],1,0,'R');
	$pdf->Cell(150,20,'',1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();
	
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'Carton',1,0,'L');
	$pdf->Cell(100,20,"USA$".$op['ord_base']['carton'],1,0,'R');
	$pdf->Cell(150,20,'',1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();
	
	$pdf->Cell(100,20,'Total cost',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,"USA$".$op['total_cost'],1,0,'R');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->ln();
	
	$pdf->Cell(100,20,'Gross Margew',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,'',1,0,'R');
	$pdf->Cell(100,20,"USA$".$op['gro_m'],1,0,'R');
	$pdf->ln();
	
	$pdf->Cell(100,20,'G. M. %',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'L');
	$pdf->Cell(100,20,'',1,0,'R');
	$pdf->Cell(150,20,'',1,0,'R');
	$pdf->Cell(100,20,$op['gm2']."%",1,0,'R');
	$pdf->ln();

//page 2
	$pdf->AddPage();
	$pdf->SetFont('Big5','',18);
	$pdf->text(250,60,"Cost analysis");
	$pdf->ln();
	$today=date('Y-m-d');
	$pdf->SetFont('Arial','',14);
	$pdf->text(50,90,"Order# : ".$op['ord_base']['order_num']."               ETD : ".$op['ord_base']['ETD']."               Print date : ".$today);
	$pdf->ln();
	$pdf->SetFont('Arial','B',60);
	$pdf->waterline($op['ord_base']['order_num']);
	$pdf->SetFont('Big5','',14);
	$pdf->SetY(110);
	$pdf->cell(550,20,'Fabric :',1,0,'L');
	$pdf->ln();
	$long9=array('fab. name','construct','weight','width','component','price','unit','yy','supplier');
	$size9= array(90,60,50,50,60,60,40,40,100);	
	$pdf->full_fields_c($long9,$size9,20);
	$pdf->ln();
	$j=0;
	if (isset($op['ord_fab']['msg']))
	{
		$pdf->cell(550,20,$op['ord_fab']['msg'],1,0,'L');
		$pdf->ln();	
	}else{
	for ($i=0; $i< sizeof($op['ord_fab']); $i++)
	{
		$j++;
		$long10=array($op['ord_fab'][$i]['fab_name'],$op['ord_fab'][$i]['fab_cons'],$op['ord_fab'][$i]['fab_weight'],$op['ord_fab'][$i]['fab_width'],$op['ord_fab'][$i]['fab_comp']
		             ,$op['ord_fab'][$i]['u_price'],$op['ord_fab'][$i]['unit'],$op['ord_fab'][$i]['yy'],$op['ord_fab'][$i]['supl']);
		$size10= array(90,60,50,50,60,60,40,40,100);	
		$pdf->full_fields_c($long10,$size10,20);
		$pdf->ln();
		if ($j == 25)
		{
			$pdf->AddPage();
			$pdf->SetFont('Big5','',18);
			$pdf->text(250,60,"Cost analysis");
			$pdf->ln();
			$today=date('Y-m-d');
			$pdf->SetFont('Arial','',14);
			$pdf->text(50,90,"Order# : ".$op['ord_base']['order_num']."               ETD : ".$op['ord_base']['ETD']."               Print date : ".$today);
			$pdf->ln();
			$pdf->SetFont('Arial','B',60);
			$pdf->waterline($op['ord_base']['order_num']);
			$pdf->SetFont('Big5','',14);
			$pdf->SetY(110);
			$pdf->cell(550,20,'Fabric :',1,0,'L');
			$pdf->ln();
			$pdf->full_fields_c($long9,$size9,20);
			$pdf->ln();
			$j=0;
		}
	
	}
}
	$pdf->ln();
	$pdf->cell(550,20,'Accories :',1,0,'L');
	$pdf->ln();
	$long9=array('acc. name','spac','unit','price','yy','supplier');
	$size9= array(100,120,50,70,70,140);	
	$pdf->full_fields_c($long9,$size9,20);
	$pdf->ln();
	if (isset($op['ord_acc']['msg']))
	{
		$pdf->cell(550,20,$op['ord_acc']['msg'],1,0,'L');
		$pdf->ln();	
	}else{
	for ($i=0; $i< sizeof($op['ord_acc']); $i++)
	{
		$j++;
		$long10=array($op['ord_acc'][$i]['acc_name'],$op['ord_acc'][$i]['spec'],$op['ord_acc'][$i]['unit']
		             ,$op['ord_acc'][$i]['u_price'],$op['ord_acc'][$i]['yy'],$op['ord_acc'][$i]['supl']);
		$size10= array(100,120,50,70,70,140);	
		$pdf->full_fields_c($long10,$size10,20);
		$pdf->ln();		
		if ($j == 25)
		{
			$pdf->AddPage();
			$pdf->SetFont('Big5','',18);
			$pdf->text(250,60,"Cost analysis");
			$pdf->ln();
			$today=date('Y-m-d');
			$pdf->SetFont('Arial','',14);
			$pdf->text(50,90,"Order# : ".$op['ord_base']['order_num']."               ETD : ".$op['ord_base']['ETD']."               Print date : ".$today);
			$pdf->ln();
			$pdf->SetFont('Arial','B',60);
			$pdf->waterline($op['ord_base']['order_num']);
			$pdf->SetFont('Big5','',14);
			$pdf->SetY(110);
			$pdf->cell(550,20,'Fabric :',1,0,'L');
			$pdf->ln();
			$pdf->full_fields_c($long9,$size9,20);
			$pdf->ln();
			$j=0;
		}
	
	}
	}
	$pdf->ln();
	$pdf->cell(550,20,'Extra work :',1,0,'L');
	$pdf->ln();
	$long9=array('ex-work','unit','price','supplier');
	$size9= array(170,60,120,200);	
	$pdf->full_fields_c($long9,$size9,20);
	$pdf->ln();
	if (isset($op['ord_exw']['msg']))
	{
		$pdf->cell(550,20,$op['ord_exw']['msg'],1,0,'L');
		$pdf->ln();	
	}else{	
	for ($i=0; $i< sizeof($op['ord_exw']); $i++)
	{
		$j++;
		$long10=array($op['ord_exw'][$i]['ex_work'],$op['ord_exw'][$i]['unit'],$op['ord_exw'][$i]['u_price']
		             ,$op['ord_exw'][$i]['supl']);
		$size10= array(170,60,120,200);	
		$pdf->full_fields_c($long10,$size10,20);
		$pdf->ln();			
		if ($j == 25)
		{
			$pdf->AddPage();
			$pdf->SetFont('Big5','',18);
			$pdf->text(250,60,"Cost analysis");
			$pdf->ln();
			$today=date('Y-m-d');
			$pdf->SetFont('Arial','',14);
			$pdf->text(50,90,"Order# : ".$op['ord_base']['order_num']."               ETD : ".$op['ord_base']['ETD']."               Print date : ".$today);
			$pdf->ln();
			$pdf->SetFont('Arial','B',60);
			$pdf->waterline($op['ord_base']['order_num']);
			$pdf->SetFont('Big5','',14);
			$pdf->SetY(110);
			$pdf->cell(550,20,'Fabric :',1,0,'L');
			$pdf->ln();
			$pdf->full_fields_c($long9,$size9,20);
			$pdf->ln();
			$j=0;
		}
	
	}
	}


// page3
	$pdf->AddPage();
	$pdf->SetFont('Big5','',18);
	$pdf->text(250,60,"Order memorandum");
	$pdf->ln();
	$pdf->SetY(80);
	$long1=array('order # : '.$op['ord_base']['order_num'],'customer style : '.$op['ord_base']['style_num'],'print date : '.$today);
	$size1= array(200,200,150);
	$pdf->full_fields_c($long1,$size1,20);
	$pdf->ln();	
	$long2=array('customer name : '.$op['ord_base']['cust'],'extra work area : '.$op['ord_base']['source'],'order date : '.$op['ord_base']['apv_date']);
	$size2= array(200,200,150);
	$pdf->full_fields_c($long2,$size2,20);
	$pdf->ln();	
	$long3=array('Goods name : '.$op['ord_base']['name'],'vendor : '.$op['ord_base']['vendor'],'season : '.$op['ord_base']['season']);
	$size3= array(200,200,150);
	$pdf->full_fields_c($long3,$size3,20);
	$pdf->ln();	
	$long4=array('specifications : ','quota : ','conversion rate : ');
	$size4= array(200,200,150);
	$pdf->full_fields_c($long4,$size4,20);
	$pdf->ln();
	$pdf->ln();
	
	$pdf->cell(550,20,'Fabric :',1,0,'L');
	$pdf->ln();
	$long9=array('fab. name','construct','weight','width','component','yy','supplier');
	$size9= array(120,60,60,60,60,60,130);	
	$pdf->full_fields_c($long9,$size9,20);
	$pdf->ln();
	$j=0;
	if (isset($op['ord_fab']['msg']))
	{
		$pdf->cell(550,20,$op['ord_fab']['msg'],1,0,'L');
		$pdf->ln();	
	}else{	
	for ($i=0; $i< sizeof($op['ord_fab']); $i++)
	{
		$j++;
		$long10=array($op['ord_fab'][$i]['fab_name'],$op['ord_fab'][$i]['fab_cons'],$op['ord_fab'][$i]['fab_weight'],$op['ord_fab'][$i]['fab_width'],$op['ord_fab'][$i]['fab_comp']
		             ,$op['ord_fab'][$i]['yy'],$op['ord_fab'][$i]['supl']);
		$size10= array(120,60,60,60,60,60,130);	
		$pdf->full_fields_c($long10,$size10,20);
		$pdf->ln();
		if ($j == 21)
		{
			$pdf->AddPage();
			$pdf->SetFont('Big5','',18);
			$pdf->text(250,60,"Order memorandum");
			$pdf->ln();
			$today=date('Y-m-d');
			$pdf->SetFont('Arial','',14);
			$pdf->SetFont('Arial','B',60);
			$pdf->waterline($op['ord_base']['order_num']);
			$pdf->SetFont('Big5','',14);
			$pdf->SetY(110);
			$pdf->cell(550,20,'Fabric :',1,0,'L');
			$pdf->ln();
			$pdf->full_fields_c($long1,$size1,20);
			$pdf->ln();	
			$pdf->full_fields_c($long2,$size2,20);
			$pdf->ln();
			$pdf->full_fields_c($long3,$size3,20);
			$pdf->ln();
			$pdf->full_fields_c($long4,$size4,20);
			$pdf->ln();
			$pdf->full_fields_c($long9,$size9,20);
			$pdf->ln();
			$j=0;
		}
	
	}
	}
	
	$pdf->ln();
	$long9=array('cm. ETD','vendor ETD','contract amount','pirce','export area',);
	$size9= array(110,110,110,110,110);	
	$pdf->full_fields_c($long9,$size9,20);
	$pdf->ln();
	$long10=array($op['ord_base']['ETD'],'',$op['ord_base']['amount'],$op['ord_base']['price'],$op['ord_base']['ex_area'],);
	$size10= array(110,110,110,110,110);	
	$pdf->full_fields_c($long10,$size10,20);
	$pdf->ln();
	$pdf->cell(220,20,'Total :',0,0,'R');
	$pdf->cell(110,20,$op['ord_base']['amount'],0,0,'C');
	$pdf->cell(110,20,"US$".$op['ord_base']['price'],0,0,'C');
	$pdf->ln();
	
	$pdf->ln();
	$pdf->cell(550,20,'Edit record :',1,0,'L');
	$pdf->ln();
	$long9=array('ETD','Edit reason','Table','Field','Un_change','Change','Date','User');
	$size9= array(60,90,50,70,80,80,60,60);	
	$pdf->full_fields_c($long9,$size9,20);
	$pdf->ln();
	if (isset($op['edit_rec']['msg']))
	{
		$pdf->cell(550,20,$op['edit_rec']['msg'],1,0,'L');
		$pdf->ln();
	}else{
	for ($i=0; $i < sizeof ($op['edit_rec']); $i++)
	{
		$j++;
		$long10=array($op['edit_rec'][$i]['ETD'],$op['edit_rec'][$i]['reason'],$op['edit_rec'][$i]['edt_table'],$op['edit_rec'][$i]['field'],
		              $op['edit_rec'][$i]['un_change'],$op['edit_rec'][$i]['edt_change'],$op['edit_rec'][$i]['edt_date'],$op['edit_rec'][$i]['user']);
		$size10= array(60,90,50,70,80,80,60,60);	
		$pdf->full_fields_c($long10,$size10,20);
		$pdf->ln();
		if ($j == 21)
		{
			$pdf->AddPage();
			$pdf->SetFont('Big5','',18);
			$pdf->text(250,60,"Order memorandum");
			$pdf->ln();
			$today=date('Y-m-d');
			$pdf->SetFont('Arial','',14);
			$pdf->SetFont('Arial','B',60);
			$pdf->waterline($op['ord_base']['order_num']);
			$pdf->SetFont('Big5','',14);
			$pdf->SetY(110);
			$pdf->cell(550,20,'Fabric :',1,0,'L');
			$pdf->ln();
			$pdf->full_fields_c($long1,$size1,20);
			$pdf->ln();	
			$pdf->full_fields_c($long2,$size2,20);
			$pdf->ln();
			$pdf->full_fields_c($long3,$size3,20);
			$pdf->ln();
			$pdf->full_fields_c($long4,$size4,20);
			$pdf->ln();
			$pdf->full_fields_c($long9,$size9,20);
			$pdf->ln();
			$j=0;
		}
	
	}
	}
		$pdf->Output();	
	
	} // end func

	
}
?>