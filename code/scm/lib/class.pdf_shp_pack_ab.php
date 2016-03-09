<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_pack extends PDF_Chinese 	{


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
//	$this->Cell(100, 6,$print_title1,0,0,'C');
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
	$this->SetFont('Arial','B',14);
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
	global $pg_mk;
	
	$str[2] =$str[1] =$str[3] =$str[4]='';
	$tmp = explode(' ',$parm['content']);
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str[1])+strlen($tmp[$i]);
		$len2 = strlen($str[2])+strlen($tmp[$i]);
		$len3 = strlen($str[3])+strlen($tmp[$i]);
		$len4 = strlen($str[4])+strlen($tmp[$i]);
		
		if($len1 < 100 && strlen($str[2]) <= 1)
		{
			$str[1] = $str[1].$tmp[$i]." ";
		}else if($len2 < 100 && strlen($str[3]) <= 1){
			$str[2] = $str[2].$tmp[$i]." ";
		}else if($len3 < 100 && strlen($str[4]) <= 1){
			$str[3] = $str[3].$tmp[$i]." ";
		}else{
			$str[4] = $str[4].$tmp[$i]." ";
		}
	}	
	
	$style =explode('/',$parm['style']);
	$this->SetFont('Arial','B',12);
	$this->Cell(40, 5,"INVOICE NO.:",0,0,'L',0);
	$this->Cell(30, 5,$parm['inv_num'],0,0,'L',0);
	$this->Cell(150, 5,'',0,0,'L',0);
	$this->Cell(20, 5,"DATE:",0,0,'L',0);
	$this->Cell(40, 5,$parm['date'],0,0,'L',0);
	$this->ln();
	$this->Cell(40, 5,"DESCRIPTION:",0,0,'L',0);
	$this->Cell(30, 5,$parm['des'],0,0,'L',0);
	$this->Cell(150, 5,'',0,0,'L',0);
	$this->Cell(20, 5,"STYLE#",0,0,'L',0);
	$this->Cell(40, 5,$style[0],0,0,'L',0);
	$this->ln();
	for($i=1; $i<sizeof($style); $i++)
	{
		$this->Cell(240, 5,"STYLE#",0,0,'L',0);
		$this->Cell(40, 5,$style[$i],0,1,'L',0);
		$pg_mk++;
	}
	$this->Cell(40, 5,"Content:",0,0,'L',0);
	$this->Cell(240, 5,$str[1],0,0,'L',0);
	$this->ln();	

	for($i=2; $i<sizeof($str); $i++)
	{
		if($str[$i])
		{
			$this->Cell(40, 5,"",0,0,'L',0);
			$this->Cell(240, 5,$str[$i],0,0,'L',0);
			$this->ln();	
			$pg_mk++;
		}
	}
	
	$yy =$this->GetY();
	
	$this->SetXY(8,($yy+3));

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


function ship_title($size)
{
	//290
	$fld_size = 110 / sizeof($size);
	
	$this->SetFont('Arial','B',9);
	$this->SetX(8);
	$this->Cell(30,4,'CARTON','TLR',0,'C');
	$this->Cell(25,4,'','TLR',0,'C');
	$this->Cell(20,4,'','TLR',0,'C');
	$this->Cell(5,4,'','TLR',0,'C');
	$this->Cell(0.3,4,'','TLR',0,'C');
	$this->Cell(110,6,'RATIO OR CONTENT PER CARTON','TRL',0,'C');
	$this->Cell(0.3,4,'','TLR',0,'C');
	$this->Cell(18,4,'TOTAL','TLR',0,'C');
	$this->Cell(16,4,'Q\'TY PER','TLR',0,'C');
	$this->Cell(18,4,'TOTAL','TLR',0,'C');
	$this->Cell(16,4,'TOTAL','TLR',0,'C');
	$this->Cell(14,4,'TOTAL','TLR',0,'C');
	$this->Cell(14,4,'TOTAL','TLR',0,'C');
	$this->ln();
	$this->SetX(8);
	$this->Cell(30,4,'RANGES','LR',0,'C');
	$this->Cell(25,4,'STYLE','LR',0,'C');
	$this->Cell(20,4,'COLOR','LR',0,'C');
	$this->Cell(5,4,'FIT','LR',0,'C');
	$this->Cell(0.3,4,'','LR',0,'C');
	for ($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,2,'',0,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(18,4,'CARTONS','LR',0,'C');
	$this->Cell(16,4,'CTNS','LR',0,'C');
	$this->Cell(18,4,'QUANTITY','LR',0,'C');
	$this->Cell(16,4,'N.N.W. IN','LR',0,'C');
	$this->Cell(14,4,'N.W. IN','LR',0,'C');
	$this->Cell(14,4,'G.W. IN','LR',0,'C');
	$this->ln();
	$this->SetX(8);
	
	$this->Cell(15,4,'FROM',1,0,'C');
	$this->Cell(15,4,'TO',1,0,'C');
	$this->Cell(25,4,'','LRB',0,'C');
	$this->Cell(20,4,'','LRB',0,'C');
	$this->Cell(5,4,'','LRB',0,'C');
	$this->Cell(0.3,4,'','BLR',0,'C');
	for ($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,4,$size[$i],1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(18,4,'','LRB',0,'C');
	$this->Cell(16,4,'(PCS)','LRB',0,'C');
	$this->Cell(18,4,'','LRB',0,'C');
	$this->Cell(16,4,'KGS','LRB',0,'C');
	$this->Cell(14,4,'KGS','LRB',0,'C');
	$this->Cell(14,4,'KGS','LRB',0,'C');	
	$this->ln();
}


function ship_txt($parm,$in_box)
{
	//290
	$fld_size = 110 / sizeof($parm['size']);
	$this->SetFont('Arial','',7);
	$ctn = explode('-',$parm['cnt']);
	$this->SetX(8);
	$ctn_num = $ctn[1] - $ctn[0] + 1;
	
	if($parm['f_mk'] == 1)
	{
		$this->Cell(15,4,$ctn[0],'TLR',0,'C');
		$this->Cell(15,4,$ctn[1],'TLR',0,'C');
	}else{
		$this->Cell(15,4,'','LR',0,'C');
		$this->Cell(15,4,'','LR',0,'C');	
	}
	
	if($parm['ord_row'])
	{
		$this->Cell(25,4,$parm['style_num'],'TLR',0,'C');
	}else{
		$this->Cell(25,4,$parm['style_num'],'LR',0,'C');
	}
	if($parm['color_row'])
	{
		$this->Cell(20,4,$parm['color'],'TLR',0,'C');
		$this->Cell(5,4,$parm['item'],1,0,'C');
		$this->Cell(0.3,4,'','TLR',0,'C');
	}else{
		$this->Cell(20,4,$parm['color'],'LR',0,'C');
		$this->Cell(5,4,$parm['item'],1,0,'C');
		$this->Cell(0.3,4,'','LR',0,'C');
	}
	
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
		$this->Cell(18,4,$parm['cnum'],'TLR',0,'C');
		$this->Cell(16,4,$parm['qty'],'TLR',0,'C');
		$this->Cell(18,4,NUMBER_FORMAT(($parm['qty'] * $ctn_num ),0),'TLR',0,'C');	
		$this->Cell(16,4,NUMBER_FORMAT(($parm['nnw'] * $parm['cnum']),2),'TLR',0,'R');
		$this->Cell(14,4,NUMBER_FORMAT(($parm['nw'] * $parm['cnum']),2),'TLR',0,'R');
		$this->Cell(14,4,NUMBER_FORMAT(($parm['gw'] * $parm['cnum']),2),'TLR',0,'R');	

	}else{
		$this->Cell(18,4,'','LR',0,'C');
		$this->Cell(16,4,'','LR',0,'C');
		$this->Cell(18,4,'','LR',0,'C');	
		$this->Cell(16,4,NUMBER_FORMAT(($parm['nnw'] * $ctn_num),2),'TLR',0,'R');
		$this->Cell(14,4,NUMBER_FORMAT(($parm['nw'] * $ctn_num),2),'TLR',0,'R');
		$this->Cell(14,4,NUMBER_FORMAT(($parm['gw'] * $ctn_num),2),'TLR',0,'R');	
	}


	
	
	
	$this->ln();
}


function ship_txt_mix($parm)
{
	//290
	$fld_size = 110 / sizeof($parm['size']);
	$this->SetFont('Arial','',7);
	$ctn = explode('-',$parm['cnt']);
	$this->SetX(8);
	$ctn_num = $ctn[1] - $ctn[0] + 1;
	
	if($parm['f_mk'] == 1)
	{
		$this->Cell(15,4,$ctn[0],'TLR',0,'C');
		$this->Cell(15,4,$ctn[1],'TLR',0,'C');
	}else{
		$this->Cell(15,4,'','LR',0,'C');
		$this->Cell(15,4,'','LR',0,'C');	
	}

	$this->Cell(25,4,substr($parm['style_num'],0,18),'TLR',0,'C');
	$this->Cell(20,4,substr($parm['color'],0,18),'TLR',0,'C');
	$this->Cell(5,4,substr($parm['item'],0,18),'TLR',0,'C');
	$this->Cell(0.3,4,'','TLR',0,'C');
	$this->SetFont('Arial','',8);
	for ($i=0; $i<sizeof($parm['size_qty']); $i++)$this->Cell($fld_size,4,$parm['size_qty'][$i],'TLR',0,'C');
	
	$this->Cell(0.3,4,'','TLR',0,'C');

	if($parm['f_mk'] == 1)
	{
		$this->Cell(18,4,$parm['cnum'],'TLR',0,'C');
		$this->Cell(16,4,$parm['qty'],'TLR',0,'C');
		$this->Cell(18,4,NUMBER_FORMAT(($parm['qty'] * $ctn_num ),0),'TLR',0,'C');	
		$this->Cell(16,4,NUMBER_FORMAT(($parm['nnw'] * $parm['cnum']),2),'TLR',0,'R');
		$this->Cell(14,4,NUMBER_FORMAT(($parm['nw'] * $parm['cnum']),2),'TLR',0,'R');
		$this->Cell(14,4,NUMBER_FORMAT(($parm['gw'] * $parm['cnum']),2),'TLR',0,'R');	

	}else{
		$this->Cell(18,4,'','LR',0,'C');
		$this->Cell(16,4,'','LR',0,'C');
		$this->Cell(18,4,'','LR',0,'C');	
		$this->Cell(16,4,'','LR',0,'R');
		$this->Cell(14,4,'','LR',0,'R');
		$this->Cell(14,4,'','LR',0,'R');	
	}	
	$this->ln();
	$this->SetX(8);
	
//²Ä¤G¦C	
	$this->Cell(15,4,'','BLR',0,'C');
	$this->Cell(15,4,'','BLR',0,'C');
	$this->Cell(25,4,substr($parm['style_num'],18),'BLR',0,'C');
	$this->Cell(20,4,substr($parm['color'],18),'BLR',0,'C');
	$this->Cell(5,4,'','BLR',0,'C');
	$this->SetFont('Arial','',8);
	$this->Cell(0.3,4,'','BLR',0,'C');
	for ($i=0; $i<sizeof($parm['size_qty']); $i++)$this->Cell($fld_size,4,'','BLR',0,'C');
	$this->Cell(0.3,4,'','BLR',0,'C');
	
	$this->Cell(18,4,'','BLR',0,'C');
	$this->Cell(16,4,'','BLR',0,'C');
	$this->Cell(18,4,'','BLR',0,'C');
	$this->Cell(16,4,'','BLR',0,'R');
	$this->Cell(14,4,'','BLR',0,'R');
	$this->Cell(14,4,'','BLR',0,'R');	
	$this->ln();	
}






function sub_total($parm)
{
	$fld_size = 110 / sizeof($parm['size']);
	
	$this->SetFont('Arial','',8);
	$this->SetX(8);
	$this->Cell(80,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(110,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(80,4,'SUB-TOTAL >>',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	for ($i=0; $i<sizeof($parm['size']); $i++)$this->Cell($fld_size,4,$parm['size'][$i],1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	$this->Cell(18,4,$parm['ctn'],1,0,'C');
	$this->Cell(16,4,'',1,0,'C');
	$this->Cell(18,4,$parm['qty'],1,0,'C');
	$this->Cell(16,4,NUMBER_FORMAT($parm['nnw'],2),1,0,'R');
	$this->Cell(14,4,NUMBER_FORMAT($parm['nw'],2),1,0,'R');
	$this->Cell(14,4,NUMBER_FORMAT($parm['gw'],2),1,0,'R');
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(80,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(110,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->ln();
}

function gd_total($parm)
{
	$fld_size = 110 / sizeof($parm['size']);
	$this->SetFont('Arial','',8);
	


	$this->SetX(8);
	$this->Cell(80,4,'',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(110,4,'',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(18,4,'',1,0,'C');
	$this->Cell(16,4,'',1,0,'C');
	$this->Cell(18,4,'',1,0,'C');
	$this->Cell(16,4,'',1,0,'C');
	$this->Cell(14,4,'',1,0,'C');
	$this->Cell(14,4,'',1,0,'C');
	$this->ln();	

	$this->SetX(8);
	$this->Cell(80,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(110,0.5,'',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(80,4,'GRAND TOTAL >>',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	for ($i=0; $i<sizeof($parm['size']); $i++)$this->Cell($fld_size,4,$parm['size'][$i],1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	$this->Cell(18,4,$parm['ctn'],1,0,'C');
	$this->Cell(16,4,'',1,0,'C');
	$this->Cell(18,4,$parm['qty'],1,0,'C');
	$this->Cell(16,4,NUMBER_FORMAT($parm['nnw'],2),1,0,'R');
	$this->Cell(14,4,NUMBER_FORMAT($parm['nw'],2),1,0,'R');
	$this->Cell(14,4,NUMBER_FORMAT($parm['gw'],2),1,0,'R');
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(80,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	$this->Cell(110,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->ln();
}


function break_title($size)
{
	//290
	$fld_size = 140 / sizeof($size);
	$this->SetFont('Arial','B',9);
	$this->SetX(8);
	$this->Cell(35,4,'PO#',1,0,'C');
	$this->Cell(45,4,'STYLE',1,0,'C');
	$this->Cell(40,4,'COLOR/SIZE',1,0,'C');
	$this->Cell(5,4,'FIT',1,0,'C');
	for ($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,4,$size[$i],1,0,'C');
	
	$this->Cell(20,4,'TOTAL',1,0,'C');
	$this->ln();

}

function break_txt($parm)
{
	//290
	$fld_size = 140 / sizeof($parm['qty']);
	$this->SetFont('Arial','',7);
	$this->SetX(8);
	$this->Cell(35,4,$parm['po_num'],1,0,'C');
	$this->Cell(45,4,$parm['style_num'],1,0,'C');
	$this->Cell(40,4,$parm['color'],1,0,'C');
	$this->Cell(5,4,$parm['item'],1,0,'C');
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
	$fld_size = 140 / sizeof($parm['g_size_qty']);
	$this->SetFont('Arial','',8);
	$this->SetX(8);
	$this->Cell(125,4,'GRAND TOTAL >> ',0,0,'C');
	for ($i=0; $i<sizeof($parm['g_size_qty']); $i++)$this->Cell($fld_size,4,NUMBER_FORMAT($parm['g_size_qty'][$i],0),0,0,'C');	
	$this->Cell(20,4,NUMBER_FORMAT($parm['g_qty'],0),0,0,'C');
	$this->ln();
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$yy = $this->GetY();
	$this->Line(8,$yy,292,$yy);
	$yy += 0.5;
	$this->Line(8,$yy,292,$yy);

}

function end_ship($parm,$fty)
{
		$this->SetFont('Arial','',9);
		
		$this->SetX(8);
		$this->Cell(90,4,'TOTAL NUMBER OF CARTON : ',0,0,'L');
		$this->Cell(20,4,$parm['ctn'],0,0,'C');
		$this->Cell(15,4,'CTNS',0,0,'L');
		$this->Cell(65,4,'',0,0,'L');
		if($fty == $GLOBALS['SCACHE']['ADMIN']['dept'])
		{
			$this->Cell(100,4,$GLOBALS['SHIP_TO'][$fty]['Name'],0,0,'L');
		}else{
			$this->Cell(100,4,$GLOBALS['SHIP_TO']['CA']['Name'],0,0,'L');
		}

		$this->ln();
		
		$this->SetX(8);
		$this->Cell(90,4,'TOTAL NET NET WEIGHT : ',0,0,'L');
		$this->Cell(20,4,$parm['nnw'],0,0,'C');
		$this->Cell(15,4,'KGS',0,0,'L');
		$this->ln();
		
		$this->SetX(8);
		$this->Cell(90,4,'TOTAL NET WEIGHT : ',0,0,'L');
		$this->Cell(20,4,$parm['nw'],0,0,'C');
		$this->Cell(15,4,'KGS',0,0,'L');
		$this->ln();	
		
		$this->SetX(8);
		$this->Cell(90,4,'TOTAL GROSS WEIGHT : ',0,0,'L');
		$this->Cell(20,4,$parm['gw'],0,0,'C');
		$this->Cell(15,4,'KGS',0,0,'L');
		$this->ln();	
		$this->ln();
		$this->ln();
		
		$this->SetFont('Arial','B',9);
		$this->SetX(8);
		$this->Cell(90,4,'SHIPPING MARK : ',0,0,'L');
		$this->ln();					

		$yy = $this->GetY();
		$this->SetFont('Arial','',9);
		$this->SetX(8);
		$this->Cell(90,4,'PLEASE SEE INVOICE SHEET ',0,0,'L');
		$this->Line(190,$yy,270,$yy);

		$this->ln();					

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
