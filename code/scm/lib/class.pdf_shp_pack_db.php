<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_pack_db extends PDF_Chinese 	{


	var $B=0;
  var $I=0;
  var $U=0;
  var $HREF='';
  var $ALIGN='';
	var $angle=0;



//Page header
function Header() {

											
	$main_rec = $_SESSION['main_rec'];
	global $print_title;
	global $print_title1;	
	global $mark;
	global $print_title2;

	if($main_rec['fty'] == $GLOBALS['SCACHE']['ADMIN']['dept'])
	{
		$cmpy['name'] =	$GLOBALS['SHIP_TO'][$main_rec['fty']]['Name'];
		$cmpy['oth1'] = $GLOBALS['SHIP_TO'][$main_rec['fty']]['Addr'];
		$cmpy['oth2'] = "TEL : ".$GLOBALS['SHIP_TO'][$main_rec['fty']]['TEL']."  FAX : ".$GLOBALS['SHIP_TO'][$main_rec['fty']]['FAX'];
	}else{
		$cmpy['name'] = $GLOBALS['SHIP_TO']['CA']['Name'];
		$cmpy['oth1'] = "TEL : ".$GLOBALS['SHIP_TO']['CA']['TEL']."  FAX : ".$GLOBALS['SHIP_TO']['CA']['FAX'];
		$cmpy['oth2'] = $GLOBALS['SHIP_TO']['CA']['Addr'];
	}

  $this->SetFont('Arial','B',14);
	$this->SetXY(5,5);
	$this->Cell(290, 10,$cmpy['name'],0,0,'C');
	$this->SetXY(5,11);

	$this->SetFont('Arial','',8);
	$this->cell(290, 5,$cmpy['oth1'],0,0,'C');
	$this->SetXY(5,14);

	$this->cell(290, 5,$cmpy['oth2'],0,0,'C');


	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(100,150,$mark,30);
	//Logo
//	$w =$this->GetStringWidth($print_title) +30;
	//title

	$this->SetXY(10,20);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','',7);
	$this->Cell(100, 7,$print_title1,0,0,'L',0);
	$this->SetFont('Arial','',14);
	$this->Cell(85, 7,$print_title,0,0,'C',0);
  $this->SetFont('Arial','I',12);
	$this->Cell(90, 7,'Page '.$this->PageNo().' / <nb> ',0,0,'R',0);
	
	//date
 //   $this->SetFont('Arial','I',9);
	$this->SetXY(180,24);
//	$this->Cell(20, 7,$dt['date'],0,1,'C',0);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,27,290,27);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,28,290,28);
	$this->ln(5);
	
	$this->SetXY(10,32);
	$this->hend_title($main_rec);
	
}

function hend_title($parm)
{

	$con_addr1 = $con_addr2 = '';
	$tmp = explode(' ',$parm['consignee_addr']);
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($con_addr1)+strlen($tmp[$i]);
		$len2 = strlen($con_addr2)+strlen($tmp[$i]);
		if($len1 < 55 && strlen($con_addr2) <= 1)
		{
			$con_addr1 = $con_addr1.$tmp[$i]." ";			
		}else{
			$con_addr2 = $con_addr2.$tmp[$i]." ";
		}
	}	
	$this->SetFont('Arial','B',12);
	$this->Cell(160, 5,"CONSIGNEE:",0,0,'L',0);
	$this->Cell(40, 5,"INVOICE NO.:",0,0,'L',0);
	$this->Cell(60, 5,$parm['inv_num'],0,0,'L',0);
	$this->ln();

	$this->SetFont('Arial','B',12);
	$this->Cell(160, 5,$parm['consignee'],0,0,'L',0);
	$this->Cell(40, 5,"DATE:",0,0,'L',0);
	$this->Cell(60, 5,$parm['date'],0,0,'L',0);
	$this->ln();

	$this->SetFont('Arial','B',12);
	$this->Cell(160, 5,$con_addr1,0,0,'L',0);
	$this->Cell(40, 5,"L/C NO.",0,0,'L',0);
	$this->Cell(60, 5,$parm['lc_no']."DATED:".$parm['lc_date'],0,0,'L',0);
	$this->ln();

	$this->SetFont('Arial','B',12);
	$this->Cell(270, 5,$con_addr2,0,0,'L',0);
	$this->ln();
	$this->SetXY(8,49);

}

function RotatedText($x,$y,$txt,$angle) {
    //Text rotated around its origin
    $this->Rotate($angle,$x,$y);
    $this->Text($x,$y,$txt);
    $this->Rotate(0);
}

function Rotate($angle,$x=-1,$y=-1)
{
    if($x==-1)
        $x=$this->x;
    if($y==-1)
        $y=$this->y;
    if($this->angle!=0)
        $this->_out('Q');
    $this->angle=$angle;
    if($angle!=0)
    {
        $angle*=M_PI/180;
        $c=cos($angle);
        $s=sin($angle);
        $cx=$x*$this->k;
        $cy=($this->h-$y)*$this->k;
        $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    }
}


//Page footer
function Footer() {
	global $creator;
	global $print_title2;

	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('BIG5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,$print_title2.'                   Created by : '.$creator,0,0,'R');
}


// field module 1 [ªí¦J 1 ]


function ship_title($size,$unit)
{
	//290
	$fld_size = 84 / sizeof($size);
	
	$this->SetFont('Arial','B',8);
	$this->SetX(8);
	$this->Cell(13,4,'','TLR',0,'C');//po
	$this->Cell(13,4,'STYLE','TLR',0,'C');//style
	$this->Cell(18,4,'CTN NO.','TLR',0,'C');//carton
	$this->Cell(7,4,'','TLR',0,'C');//ppk
	$this->SetFont('Arial','B',6);
	$this->Cell(10,4,'TTL',1,0,'C');//ttl packs per ctn
	$this->SetFont('Arial','B',8);
	$this->Cell(18,4,'','TLR',0,'C');//color
	
	$this->Cell(0.3,4,'','TLR',0,'C');	
	$this->Cell(84,6,'SIZE','TRL',0,'C');
	$this->Cell(0.3,4,'','TLR',0,'C');
	
	$this->Cell(7,4,'PCS',1,0,'C');// PCS PER CTN
	$this->Cell(13,4,'TTL','TLR',0,'C'); // TTL CTN
	$this->Cell(13,4,'TTL','TLR',0,'C'); // TTL PCS
	
	$this->Cell(9,4,'NNW',1,0,'C');// NNW PER CTN
	$this->Cell(9,4,'NW',1,0,'C');// NW PER CTN
	$this->Cell(9,4,'GW',1,0,'C');// GW PER CTN
	
	$this->Cell(36,4,'TTL',1,0,'C'); // TTL NNW/NW/GW

	$this->Cell(27,4,'CARTON SIZE',1,0,'C'); // CARTON SIZE L/W/H
	
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(13,4,'P.O NO.','LR',0,'C');//po
	$this->Cell(13,4,'NO.','LR',0,'C');//style
	$this->Cell(18,4,'','LR',0,'C');//carton
	$this->Cell(7,4,'PPK','LR',0,'C');//ppk
	$this->SetFont('Arial','B',6);
	$this->Cell(10,4,'PACKS',1,0,'C');//ttl packs per ctn
	$this->SetFont('Arial','B',8);
	$this->Cell(18,4,'COLOR','LR',0,'C');//color
	$this->Cell(0.3,4,'','LR',0,'C');
	for ($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,2,'',0,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');

	$this->Cell(7,4,'PER',1,0,'C');// PCS PER CTN
	$this->Cell(13,4,'','LR',0,'C'); // TTL CTN
	$this->Cell(13,4,'','LR',0,'C'); // TTL PCS

	$this->Cell(27,4,'PER',1,0,'C');// NNW-NW-GW PER CTN

	$this->Cell(12,4,'NNW','LR',0,'C'); // TTL NNW
	$this->Cell(12,4,'NW','LR',0,'C'); // TTL NW
	$this->Cell(12,4,'GW','LR',0,'C'); // TTL GW

	$this->Cell(27,4,'('.$unit.')',1,0,'C'); // CARTON SIZE L/W/H

	$this->ln();
	
	$this->SetX(8);
	
	$this->Cell(13,4,'','BLR',0,'C');//po
	$this->Cell(13,4,'','BLR',0,'C');//style
	$this->Cell(9,4,'FROM',1,0,'C');//carton
	$this->Cell(9,4,'TO',1,0,'C');//carton
	$this->Cell(7,4,'','BLR',0,'C');//ppk
	$this->SetFont('Arial','B',6);
	$this->Cell(10,4,'PER CTN',1,0,'C');//ttl packs per ctn
	$this->SetFont('Arial','B',8);
	$this->Cell(18,4,'','BLR',0,'C');//color
		
	$this->Cell(0.3,4,'','BLR',0,'C');
	for ($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,4,$size[$i],1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');

	$this->Cell(7,4,'CTN',1,0,'C');// PCS PER CTN
	$this->Cell(13,4,'CTN',1,0,'C'); // TTL CTN
	$this->Cell(13,4,'CTN',1,0,'C'); // TTL PCS

	$this->Cell(27,4,'CTN(KG)',1,0,'C');// NNW-NW-GW PER CTN

	$this->Cell(36,4,'(KG)',1,0,'C');// NNW-NW-GW PER CTN

	$this->Cell(9,4,'L',1,0,'C');// NNW PER CTN
	$this->Cell(9,4,'W',1,0,'C');// NW PER CTN
	$this->Cell(9,4,'H',1,0,'C');// GW PER CTN

	$this->ln();
}


function ship_txt($parm,$in_box)
{
	//290
	$fld_size = 84 / sizeof($parm['size']);
	$this->SetFont('Arial','',7);
	$ctn = explode('-',$parm['cnt']);
	$this->SetX(8);
	$ctn_num = $ctn[1] - $ctn[0] + 1;
	if(!isset($parm['size_box'][0]))$parm['size_box'][0] = 1;
	
	$this->Cell(13,4,$parm['cust_po'],1,0,'C');//po
	$this->Cell(13,4,$parm['style_num'],1,0,'C');//style
	if($parm['f_mk'] == 1)
	{
		$this->Cell(9,4,$ctn[0],'TLR',0,'C');//carton
		$this->Cell(9,4,$ctn[1],'TLR',0,'C');//carton
	}else{
		$this->Cell(9,4,'','LR',0,'C');//carton
		$this->Cell(9,4,'','LR',0,'C');//carton	
	}
	$this->Cell(7,4,$parm['ppk'],1,0,'C');//ppk
	$this->Cell(10,4,$parm['size_box'][0],1,0,'C');//ttl packs per ctn
	$this->Cell(18,4,$parm['color'],1,0,'C');//color
	$this->Cell(0.3,4,'',1,0,'C');

	
	$this->SetFont('Arial','',8);
	if($in_box == 0)
	{
		for ($i=0; $i<sizeof($parm['size_qty']); $i++)$this->Cell($fld_size,4,$parm['size_qty'][$i],1,0,'C');
	}else{

		for ($i=0; $i<sizeof($parm['size_qty']); $i++)$this->Cell($fld_size,4,$parm['size_box'][$i],1,0,'C');
	}
	$this->Cell(0.3,4,'',1,0,'C');
	if($parm['f_mk'] == 1)
	{
		$this->Cell(7,4,$parm['s_qty'],'TLR',0,'C');
		$this->Cell(13,4,$ctn_num,'TLR',0,'C');
		$this->Cell(13,4,NUMBER_FORMAT(($parm['s_qty'] * $ctn_num ),0),'TLR',0,'C');

		$this->Cell(9,4,$parm['ctn_nnw'],'TLR',0,'C');// NNW PER CTN
		$this->Cell(9,4,$parm['ctn_nw'],'TLR',0,'C');// NW PER CTN
		$this->Cell(9,4,$parm['ctn_gw'],'TLR',0,'C');// GW PER CTN
	
	
		$this->Cell(12,4,NUMBER_FORMAT(($parm['ctn_nnw'] * $ctn_num),0),'TLR',0,'R');
		$this->Cell(12,4,NUMBER_FORMAT(($parm['ctn_nw'] * $ctn_num),0),'TLR',0,'R');
		$this->Cell(12,4,NUMBER_FORMAT(($parm['ctn_gw'] * $ctn_num),0),'TLR',0,'R');	

		$this->Cell(9,4,$parm['ctn_l'],'TLR',0,'C');// carton size l
		$this->Cell(9,4,$parm['ctn_w'],'TLR',0,'C');// carton size w
		$this->Cell(9,4,$parm['ctn_h'],'TLR',0,'C');// carton size h
	}else{
		$this->Cell(7,4,'','LR',0,'C');
		$this->Cell(13,4,'','LR',0,'C');
		$this->Cell(13,4,'','LR',0,'C');

		$this->Cell(9,4,'','LR',0,'C');// NNW PER CTN
		$this->Cell(9,4,'','LR',0,'C');// NW PER CTN
		$this->Cell(9,4,'','LR',0,'C');// GW PER CTN
	
	
		$this->Cell(12,4,'','LR',0,'R');
		$this->Cell(12,4,'','LR',0,'R');
		$this->Cell(12,4,'','LR',0,'R');	

		$this->Cell(9,4,'','LR',0,'C');// carton size l
		$this->Cell(9,4,'','LR',0,'C');// carton size w
		$this->Cell(9,4,'','LR',0,'C');// carton size h
	}	


	$this->ln();
}


function ship_txt_mix($parm)
{
	//290
	$fld_size = 84 / sizeof($parm['size']);
	$this->SetFont('Arial','',7);
	$ctn = explode('-',$parm['cnt']);
	$this->SetX(8);
	$ctn_num = $ctn[1] - $ctn[0] + 1;

	if(!isset($parm['size_box'][0]))$parm['size_box'][0] = 1;
	
	$this->Cell(13,4,$parm['cust_po'],'TLR',0,'C');//po
	$this->Cell(13,4,$parm['style_num'],'TLR',0,'C');//style
	if($parm['f_mk'] == 1)
	{
		$this->Cell(9,4,$ctn[0],'TLR',0,'C');//carton
		$this->Cell(9,4,$ctn[1],'TLR',0,'C');//carton
	}else{
		$this->Cell(9,4,'','LR',0,'C');//carton
		$this->Cell(9,4,'','LR',0,'C');//carton	
	}
	$this->Cell(7,4,$parm['ppk'],'TLR',0,'C');//ppk
	$this->Cell(10,4,$parm['size_box'][0],'TLR',0,'C');//ttl packs per ctn
	$this->Cell(18,4,substr($parm['color'],0,10),'TLR',0,'C');//color
	$this->Cell(0.3,4,'','TLR',0,'C');

	

	$this->SetFont('Arial','',8);
	for ($i=0; $i<sizeof($parm['size_qty']); $i++)$this->Cell($fld_size,4,$parm['size_qty'][$i],'TLR',0,'C');
	
	$this->Cell(0.3,4,'','TLR',0,'C');
	if($parm['f_mk'] == 1)
	{
		$this->Cell(7,4,$parm['qty'],'TLR',0,'C');
		$this->Cell(13,4,$ctn_num,'TLR',0,'C');
		$this->Cell(13,4,NUMBER_FORMAT(($parm['qty'] * $ctn_num ),0),'TLR',0,'C');

		$this->Cell(9,4,$parm['ctn_nnw'],'TLR',0,'C');// NNW PER CTN
		$this->Cell(9,4,$parm['ctn_nw'],'TLR',0,'C');// NW PER CTN
		$this->Cell(9,4,$parm['ctn_gw'],'TLR',0,'C');// GW PER CTN
	
	
		$this->Cell(12,4,NUMBER_FORMAT(($parm['ctn_nnw'] * $ctn_num),0),'TLR',0,'R');
		$this->Cell(12,4,NUMBER_FORMAT(($parm['ctn_nw'] * $ctn_num),0),'TLR',0,'R');
		$this->Cell(12,4,NUMBER_FORMAT(($parm['ctn_gw'] * $ctn_num),0),'TLR',0,'R');	

		$this->Cell(9,4,$parm['ctn_l'],'TLR',0,'C');// carton size l
		$this->Cell(9,4,$parm['ctn_w'],'TLR',0,'C');// carton size w
		$this->Cell(9,4,$parm['ctn_h'],'TLR',0,'C');// carton size h
	}else{
		$this->Cell(7,4,'','LR',0,'C');
		$this->Cell(13,4,'','LR',0,'C');
		$this->Cell(13,4,'','LR',0,'C');

		$this->Cell(9,4,'','LR',0,'C');// NNW PER CTN
		$this->Cell(9,4,'','LR',0,'C');// NW PER CTN
		$this->Cell(9,4,'','LR',0,'C');// GW PER CTN
	
	
		$this->Cell(12,4,'','LR',0,'R');
		$this->Cell(12,4,'','LR',0,'R');
		$this->Cell(12,4,'','LR',0,'R');	

		$this->Cell(9,4,'','LR',0,'C');// carton size l
		$this->Cell(9,4,'','LR',0,'C');// carton size w
		$this->Cell(9,4,'','LR',0,'C');// carton size h
	}	

	$this->ln();
	$this->SetX(8);
	

	if($parm['f_mk'] == 1){$cell_line == 'BLR';}else{$cell_line == 'LR';}
	$this->Cell(13,4,'','BLR',0,'C');//po
	$this->Cell(13,4,'','BLR',0,'C');//style
	$this->Cell(9,4,'',$cell_line,0,'C');//carton
	$this->Cell(9,4,'',$cell_line,0,'C');//carton
	$this->Cell(7,4,'','BLR',0,'C');//ppk
	$this->Cell(10,4,'','BLR',0,'C');//ttl packs per ctn
	$this->Cell(18,4,substr($parm['color'],10),'BLR',0,'C');//color
	$this->Cell(0.3,4,'','BLR',0,'C');

	$this->SetFont('Arial','',8);
	for ($i=0; $i<sizeof($parm['size_qty']); $i++)$this->Cell($fld_size,4,'','BLR',0,'C');
	$this->Cell(0.3,4,'','BLR',0,'C');
	
	$this->Cell(7,4,'',$cell_line,0,'C');
	$this->Cell(13,4,'',$cell_line,0,'C');
	$this->Cell(13,4,'',$cell_line,0,'C');

	$this->Cell(9,4,'',$cell_line,0,'C');// NNW PER CTN
	$this->Cell(9,4,'',$cell_line,0,'C');// NW PER CTN
	$this->Cell(9,4,'',$cell_line,0,'C');// GW PER CTN

	
	$this->Cell(12,4,'',$cell_line,0,'R');
	$this->Cell(12,4,'',$cell_line,0,'R');
	$this->Cell(12,4,'',$cell_line,0,'R');	

	$this->Cell(9,4,'',$cell_line,0,'C');// carton size l
	$this->Cell(9,4,'',$cell_line,0,'C');// carton size w
	$this->Cell(9,4,'',$cell_line,0,'C');// carton size h

	$this->ln();	
}






function sub_total($parm)
{
	$fld_size = 84 / sizeof($parm['size']);
	
	$this->SetFont('Arial','',8);
	$this->SetX(8);
	$this->Cell(79,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(84,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(20,0.5,'',1,0,'C');
	$this->Cell(13,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');	
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(79,4,'SUB-TOTAL >>',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	for ($i=0; $i<sizeof($parm['size']); $i++)$this->Cell($fld_size,4,$parm['size'][$i],1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	$this->Cell(20,4,$parm['ctn'],1,0,'R');
	$this->Cell(13,4,$parm['qty'],1,0,'R');
	$this->Cell(27,4,'',1,0,'R');
	$this->Cell(12,4,NUMBER_FORMAT($parm['nnw'],2),1,0,'R');
	$this->Cell(12,4,NUMBER_FORMAT($parm['nw'],2),1,0,'R');
	$this->Cell(12,4,NUMBER_FORMAT($parm['gw'],2),1,0,'R');
	$this->Cell(27,4,'',1,0,'R');	
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(79,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(84,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(20,0.5,'',1,0,'C');
	$this->Cell(13,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');	
	$this->ln();
}

function gd_total($parm)
{
	$fld_size = 84 / sizeof($parm['size']);
	$this->SetFont('Arial','',8);
	
	$this->SetX(8);
	$this->Cell(79,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(84,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(20,0.5,'',1,0,'C');
	$this->Cell(13,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');	
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(79,4,'GRAND TOTAL >>',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	for ($i=0; $i<sizeof($parm['size']); $i++)$this->Cell($fld_size,4,$parm['size'][$i],1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	$this->Cell(20,4,$parm['ctn'],1,0,'C');
	$this->Cell(13,4,$parm['qty'],1,0,'C');
	$this->Cell(27,4,'',1,0,'R');
	$this->Cell(12,4,NUMBER_FORMAT($parm['nnw'],2),1,0,'R');
	$this->Cell(12,4,NUMBER_FORMAT($parm['nw'],2),1,0,'R');
	$this->Cell(12,4,NUMBER_FORMAT($parm['gw'],2),1,0,'R');
	$this->Cell(27,4,'',1,0,'R');
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(79,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(84,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(20,0.5,'',1,0,'C');
	$this->Cell(13,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(12,0.5,'',1,0,'C');
	$this->Cell(27,0.5,'',1,0,'R');	
	$this->ln();	
	

	
}


function break_title($size)
{
	//290
	$fld_size = 100 / sizeof($size);
	$this->SetFont('Arial','B',9);
	$this->SetX(8);
	$this->Cell(35,4,'PO#',1,0,'C');
	$this->Cell(35,4,'STYLE',1,0,'C');
	$this->Cell(45,4,'COLOR/SIZE',1,0,'C');
	for ($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,4,$size[$i],1,0,'C');
	
	$this->Cell(20,4,'TOTAL',1,0,'C');
	$this->ln();

}

function break_txt($parm)
{
	//290
	$fld_size = 100 / sizeof($parm['qty']);
	$this->SetFont('Arial','',7);
	$this->SetX(8);
	$this->Cell(35,4,$parm['po_num'],1,0,'C');
	$this->Cell(35,4,$parm['style_num'],1,0,'C');
	$this->Cell(45,4,$parm['color'],1,0,'C');
	$this->SetFont('Arial','',8);
	for ($i=0; $i<sizeof($parm['qty']); $i++)
	{
		if($parm['qty'][$i] > 0)
		{
			$this->Cell($fld_size,4,NUMBER_FORMAT($parm['qty'][$i],0),1,0,'C');
		}else{
			$this->Cell($fld_size,4,'',1,0,'C');
		}
	}
	
	$this->Cell(20,4,NUMBER_FORMAT($parm['ttl_qty'],0),1,0,'C');
	$this->ln();

}

function break_total($parm)
{
	//290
	$fld_size = 100 / sizeof($parm['g_size_qty']);
	$this->SetFont('Arial','',8);
	$this->SetX(8);
	$this->Cell(115,4,'GRAND TOTAL >> ',0,0,'C');
	for ($i=0; $i<sizeof($parm['g_size_qty']); $i++)$this->Cell($fld_size,4,NUMBER_FORMAT($parm['g_size_qty'][$i],0),0,0,'C');	
	$this->Cell(20,4,NUMBER_FORMAT($parm['g_qty'],0),0,0,'C');
	$this->ln();
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$yy = $this->GetY();
	$this->Line(8,$yy,242,$yy);
	$yy += 0.5;
	$this->Line(8,$yy,242,$yy);

}

function end_ship($parm,$cnt_size,$fty)
{
		$this->SetFont('Arial','',9);
		
		$this->Cell(60,4,'TOTAL N.N.W : ',0,0,'L');
		$this->Cell(40,4,$parm['nnw'],0,0,'C');
		$this->Cell(60,4,'KGS',0,0,'L');
		$this->SetFont('Arial','B',9);
		$this->Cell(60,4,'SHIPPING MARK :',0,0,'L');
		$this->ln();
		$this->SetFont('Arial','',9);		
		$this->Cell(60,4,'TOTAL N.W. : ',0,0,'L');
		$this->Cell(40,4,$parm['nw'],0,0,'C');
		$this->Cell(60,4,'KGS',0,0,'L');
		$this->Cell(60,4,'PLEASE SEE INVOICE',0,0,'L');
		$this->ln();	
		
		$this->Cell(60,4,'TOTAL G.W. : ',0,0,'L');
		$this->Cell(40,4,$parm['gw'],0,0,'C');
		$this->Cell(60,4,'KGS',0,0,'L');
		$this->ln();	

		
		for($i=0; $i<sizeof($cnt_size); $i++)
		{

			$this->Cell(60,4,'MEASUREMENT : ',0,0,'L');
			$this->Cell(40,4,$cnt_size[$i],0,0,'C');
			$this->Cell(60,4,'CMS',0,0,'L');
			$this->ln();	

		}		
	

}

// table founctions----------
function LoadData($file) {
    //Read file lines
    $lines=file($file);
    $data=array();
    foreach($lines as $line)
        $data[]=explode(';',chop($line));
    return $data;
}

//Simple table
function BasicTable($header,$data) {
    //Header
    foreach($header as $col)
        $this->Cell(40,7,$col,1);
    $this->Ln();
    //Data
    foreach($data as $row)   {
        foreach($row as $col)
            $this->Cell(40,6,$col,1);
        $this->Ln();
    }
}

//Better table
function ImprovedTable($header,$data) {
    //Column widths
    $w=array(40,35,40,45);
    //Header
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    //Data
    foreach($data as $row)  {
        $this->Cell($w[0],6,$row[0],'LR');
        $this->Cell($w[1],6,$row[1],'LR');
        $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
        $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
        $this->Ln();
    }
    //Closure line
    $this->Cell(array_sum($w),0,'','T');
}



//---------- table with Muti cell ----------------	
var $widths;
var $aligns;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}






	


//-------------------------------------------------------------	
	function WriteHTML($html)  {
        //HTML parser
        $html=str_replace("\n",' ',$html);
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)  {
            if($i%2==0)  {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN == 'center')
                    $this->Cell(0,5,$e,0,1,'C');
                else
                    $this->Write(5,$e);
            } else  {
                //Tag
                if($e{0}=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else  {
                    //Extract properties
                    $a2=split(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $prop=array();
                    foreach($a2 as $v)
                        if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                            $prop[strtoupper($a3[1])]=$a3[2];
					
					$this->OpenTag($tag,$prop);
                }
            }
        }
    }

    function OpenTag($tag,$prop)  {
        //Opening tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')  {
            if( $prop['WIDTH'] != '' )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

    function CloseTag($tag) {
        //Closing tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

    function SetStyle($tag,$enable) {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
            if($this->$s>0)
                $style.=$s;
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt) {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

function mat_det($x,$hdader1,$bom_lots,$ll){

    $this->ln();
	$ll=$ll+2;
	if ($ll > 25)
	{	    
		 $ll=0;
		 $this->Open();
		 $this->AddPage();
		 $this->hend_title($parm);
		//	 $y=$y+20;
		$this->SetXY($x,50);
	}else{
		$this->SetX($x);
	}
    $long=array(30,53,100);
    $this->SetFont('Arial','B',10);
    for ($i=0; $i<sizeof($hdader1); $i++)
    {
    	$this->cell($long[$i],5,$hdader1[$i],1,0,'C');
    }
    $this->ln();
    $this->SetX($x);
    for ($i=0; $i<sizeof($hdader1); $i++)
    {
    	$this->cell($long[$i],0.5,'',1,0,'C');
    }

    $this->ln();
	
    for ($i=0; $i<sizeof($bom_lots); $i++)
    {
		$ll++;
		if ($ll > 25)
		{	    
			 $ll=0;
			 $this->Open();
			 $this->AddPage();
			 $this->hend_title($parm);
		//	 $y=$y+20;
			$this->SetXY($x,50);
			$this->SetFont('Arial','B',10);
    		for ($i=0; $i<sizeof($hdader1); $i++)
    		{
    			$this->cell($long[$i],5,$hdader1[$i],1,0,'C');    			
    		}
    		$this->ln();
    		$this->SetX($x);    		    
 		  	for ($i=0; $i<sizeof($hdader1); $i++)
    		{
    			$this->cell($long[$i],0.5,'',1,0,'C');
    		}
    		$this->ln();
    		$this->SetX($x);
		}else{
			$this->SetX($x);
		}
		$this->SetFont('BIG5','',8);
    	$this->cell($long[0],5,$bom_lots[$i][0],1,0,'L');
    	$this->cell($long[1],5,$bom_lots[$i][1],1,0,'L');
    	$this->cell($long[2],5,$bom_lots[$i][2],1,0,'L');
    	$this->ln();
    }
    return $ll;
}


}  // end of class

?>
