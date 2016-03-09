<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shidoc_pack_th extends PDF_Chinese 	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;


//Page header
function Header() {
	global $mark;
		
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,150,$mark,30);

}

function hend_title($parm)
{
//85
		$this->Cell(205, 5,'',1,1,'L',1);
		//$this->SetXY(90,5);
		$this->Image('./images/tommyEUR.jpg',3,5,52);	

		$this->SetFont('Arial','B',12);
		$this->Cell(37, 5,'FACTORY NAME',1,0,'L',0);
		$this->SetFont('Arial','',8);
		$this->Cell(70, 5,$parm['fty_name'],1,0,'L',0); 
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'C',0);
		$this->Cell(16, 5,'',1,1,'C',0);

		$this->SetFont('Arial','B',12);
		$this->Cell(37, 5,'CITY NAME',1,0,'L',0);
		$this->SetFont('Arial','',8);
		$this->Cell(168, 5,$parm['city_name'],1,1,'L',0); 
		
		$this->SetFont('Arial','B',12);
		$this->Cell(37, 5,'COUNTRY',1,0,'C',0);
		$this->SetFont('Arial','',8);
		$this->Cell(19, 5,$parm['country_org'],1,0,'C',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'C',0);
		$this->Cell(16, 5,'',1,1,'C',0);	
		
		$this->Cell(205, 3,'',1,1,'L',1);

		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Consolidator Date',1,0,'L',0);
		$this->Cell(19, 5,$parm['ship_date'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(30, 5,'P/L Quantity:',1,0,'L',0);
//		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,NUMBER_FORMAT($parm['qty']),1,0,'R',0);
		$this->Cell(13, 5,'PCS',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);	

		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Invoice#:',1,0,'L',0);
		$this->Cell(19, 5,$parm['inv_num'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(30, 5,'Cnt Dimensions:',1,0,'L',0);
		$this->Cell(24, 5,$parm['cbm'],1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);	
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'No. Of Cartons:',1,0,'L',0);
		$this->Cell(19, 5,$parm['cnum'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'Destination:',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(24, 5,$parm['ship_to'],1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);		
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Country of Origin:',1,0,'L',0);
		$this->Cell(19, 5,$parm['country_org'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'Tot.Wgt:',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'GW',1,0,'C',0);
		$this->Cell(13, 5,NUMBER_FORMAT($parm['gw']),1,0,'R',0);
		$this->Cell(13, 5,'KGS',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);		
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Ship Mode:',1,0,'L',0);
		$this->Cell(19, 5,$parm['ship_by'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'NW',1,0,'C',0);
		$this->Cell(13, 5,NUMBER_FORMAT($parm['nw']),1,0,'R',0);
		$this->Cell(13, 5,'KGS',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);				

		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'PO NO.:',1,0,'L',0);
		$this->Cell(19, 5,$parm['po_num'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'Packing:',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(37, 5,$parm['pack'],1,0,'C',0);
		$this->Cell(16, 5,'',1,1,'C',0);
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Customer PO NO.:',1,0,'L',0);
		$this->Cell(19, 5,$parm['customer_po'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);	
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Style',1,0,'L',0);
		$this->Cell(19, 5,$parm['style_num'],1,0,'L',0); 
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);			
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Season/Division:',1,0,'L',0);
		$this->Cell(30, 5,$parm['season_division'],1,0,'L',0); 
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);				

		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Style description:',1,0,'L',0);
		$this->Cell(70, 5,$parm['des'],1,0,'L',0); 
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);	
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 5,'Fabric content',1,0,'L',0);
		$this->Cell(70, 5,$parm['fabric_content'],1,0,'L',0); 
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);	
		
		$this->SetFont('Arial','',8);
		$this->Cell(37, 0.5,'',1,0,'L',0);
		$this->Cell(70, 0.5,'',1,0,'L',0); 
		$this->Cell(15, 0.5,'',1,0,'C',0); 
		$this->Cell(15, 0.5,'',1,0,'L',0);
		$this->Cell(15, 0.5,'',1,0,'C',0);
		$this->Cell(11, 0.5,'',1,0,'C',0);
		$this->Cell(13, 0.5,'',1,0,'R',0);
		$this->Cell(13, 0.5,'',1,0,'L',0);
		$this->Cell(16, 0.5,'',1,1,'C',0);			
		
		$this->SetFont('Arial','B',12);
		$this->Cell(37, 5,'Company Code:',1,0,'L',0);
		$this->SetFont('Arial','',8);
		$this->Cell(30, 5,$parm['company_code'],1,0,'L',0); 
		$this->Cell(40, 5,'',1,0,'C',0);
		$this->Cell(15, 5,'',1,0,'C',0); 
		$this->Cell(15, 5,'',1,0,'L',0);
		$this->Cell(15, 5,'',1,0,'C',0);
		$this->Cell(11, 5,'',1,0,'C',0);
		$this->Cell(13, 5,'',1,0,'R',0);
		$this->Cell(13, 5,'',1,0,'L',0);
		$this->Cell(16, 5,'',1,1,'C',0);			
		
}


function item_title()
{
		$this->SetDrawColor(0,0,0);
		$this->SetTextColor(255,255,255);
		$this->SetFont('Arial','B',10);
		$this->Cell(37, 4,'UCC 128',1,0,'C',1);// UCC 128 Label Number
		$this->Cell(19, 4,'Carton',1,0,'C',1); // Carton Content Label Number
		$this->Cell(11, 4,'Color',1,0,'C',1); // Color code
		$this->Cell(40, 4,'Color',1,0,'C',1);// color name
		$this->Cell(15, 4,'total',1,0,'C',1); // total pcs
		$this->Cell(15, 4,'Pcs/Ctn',1,0,'C',1);// PCS/CTN
		$this->Cell(15, 4,'Carton',1,0,'C',1);// carton
		$this->Cell(11, 4,'Size',1,0,'C',1);// size
		$this->Cell(13, 4,'GW',1,0,'C',1);// gw
		$this->Cell(13, 4,'NW',1,0,'C',1);// nw
		$this->Cell(16, 4,'Carton',1,1,'C',1);// carton number

		$this->Cell(37, 4,'Label Number',1,0,'C',1);// UCC 128 Label Number
		$this->Cell(19, 4,'Content',1,0,'C',1); // Carton Content Label Number
		$this->Cell(11, 4,'Code',1,0,'C',1); // Color code
		$this->Cell(40, 4,'Name',1,0,'C',1);// color name
		$this->Cell(15, 4,'pcs',1,0,'C',1); // total pcs
		$this->Cell(15, 4,'',1,0,'C',1);// PCS/CTN
		$this->Cell(15, 4,'',1,0,'C',1);// carton
		$this->Cell(11, 4,'',1,0,'C',1);// size
		$this->Cell(13, 4,'',1,0,'C',1);// gw
		$this->Cell(13, 4,'',1,0,'C',1);// nw
		$this->Cell(16, 4,'Number',1,1,'C',1);// carton number

		$this->Cell(37, 4,'',1,0,'C',1);// UCC 128 Label Number
		$this->Cell(19, 4,'Label',1,0,'C',1); // Carton Content Label Number
		$this->Cell(11, 4,'',1,0,'C',1); // Color code
		$this->Cell(40, 4,'',1,0,'C',1);// color name
		$this->Cell(15, 4,'',1,0,'C',1); // total pcs
		$this->Cell(15, 4,'',1,0,'C',1);// PCS/CTN
		$this->Cell(15, 4,'',1,0,'C',1);// carton
		$this->Cell(11, 4,'',1,0,'C',1);// size
		$this->Cell(13, 4,'',1,0,'C',1);// gw
		$this->Cell(13, 4,'',1,0,'C',1);// nw
		$this->Cell(16, 4,'',1,1,'C',1);// carton number		
		
		$this->Cell(37, 4,'',1,0,'C',1);// UCC 128 Label Number
		$this->Cell(19, 4,'Number',1,0,'C',1); // Carton Content Label Number
		$this->Cell(11, 4,'',1,0,'C',1); // Color code
		$this->Cell(40, 4,'',1,0,'C',1);// color name
		$this->Cell(15, 4,'',1,0,'C',1); // total pcs
		$this->Cell(15, 4,'',1,0,'C',1);// PCS/CTN
		$this->Cell(15, 4,'',1,0,'C',1);// carton
		$this->Cell(11, 4,'',1,0,'C',1);// size
		$this->Cell(13, 4,'',1,0,'C',1);// gw
		$this->Cell(13, 4,'',1,0,'C',1);// nw
		$this->Cell(16, 4,'',1,1,'C',1);// carton number			
		
}


function item($parm,$size_qty)
{
		$qty = $parm['qty'] * $parm['cnum'];
		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','',8);
		$this->Cell(37, 4,$parm['ucc'],'TLR',0,'C',0);// UCC 128 Label Number
		$this->Cell(19, 4,'','TLR',0,'C',0); // Carton Content Label Number
		$this->Cell(11, 4,$parm['item'],'TLR',0,'C',0); // Color code
		$this->SetFont('Arial','',7);
		$this->Cell(40, 4,$parm['color'],'TLR',0,'C',0);// color name
		$this->SetFont('Arial','',8);
		$this->Cell(15, 4,NUMBER_FORMAT($qty),'TLR',0,'R',0); // total pcs
		$this->Cell(15, 4,NUMBER_FORMAT($size_qty[0]['qty']),'TLR',0,'R',0);// PCS/CTN
		$this->Cell(15, 4,NUMBER_FORMAT($parm['cnum']),'TLR',0,'R',0);// carton
		$this->Cell(11, 4,$size_qty[0]['size'],'TLR',0,'R',0);// size
		$this->Cell(13, 4,NUMBER_FORMAT(($parm['gw'] * $parm['cnum'])),'TLR',0,'R',0);// gw
		$this->Cell(13, 4,NUMBER_FORMAT(($parm['nw'] * $parm['cnum'])),'TLR',0,'R',0);// nw
		$this->Cell(16, 4,$parm['cnt'],'TLR',1,'R',0);// carton number

		for($i=1; $i<sizeof($size_qty); $i++)
		{
			$this->Cell(37, 4,'','LR',0,'C',0);// UCC 128 Label Number
			$this->Cell(19, 4,'','LR',0,'C',0); // Carton Content Label Number
			$this->Cell(11, 4,'','LR',0,'C',0); // Color code
			$this->Cell(40, 4,'','LR',0,'C',0);// color name
			$this->Cell(15, 4,'','LR',0,'R',0); // total pcs
			$this->Cell(15, 4,NUMBER_FORMAT($size_qty[$i]['qty']),'TLR',0,'R',0);// PCS/CTN
			$this->Cell(15, 4,'','LR',0,'R',0);// carton
			$this->Cell(11, 4,$size_qty[$i]['size'],'TLR',0,'R',0);// size
			$this->Cell(13, 4,'','LR',0,'C',0);// gw
			$this->Cell(13, 4,'','LR',0,'C',0);// nw
			$this->Cell(16, 4,'','LR',1,'C',0);// carton number			
		}
		
		$this->Cell(37, 0.1,'','BLR',0,'C');// UCC 128 Label Number
		$this->Cell(19, 0.1,'','BLR',0,'C'); // Carton Content Label Number
		$this->Cell(11, 0.1,'','BLR',0,'C'); // Color code
		$this->Cell(40, 0.1,'','BLR',0,'C');// color name
		$this->Cell(15, 0.1,'','BLR',0,'C'); // total pcs
		$this->Cell(15, 0.1,'','BLR',0,'C');// PCS/CTN
		$this->Cell(15, 0.1,'','BLR',0,'C');// carton
		$this->Cell(11, 0.1,'','BLR',0,'C');// size
		$this->Cell(13, 0.1,'','BLR',0,'C');// gw
		$this->Cell(13, 0.1,'','BLR',0,'C');// nw
		$this->Cell(16, 0.1,'','BLR',1,'C');// carton number			
		
}




function assort_title()
{
		$this->SetDrawColor(0,0,0);
		$this->SetTextColor(255,255,255);
		$this->SetFont('Arial','',9);
		$this->Cell(56, 4,'Color Assortment Summary',1,0,'L',1);// Color Assortment Summary
		$this->Cell(11, 4,'Color',1,0,'C',1); // Color code
		$this->Cell(40, 4,'Color',1,0,'C',1);// color OLOR DESCRIPTIO
		$this->Cell(15, 4,'size',1,0,'C',1); // Size
		$this->Cell(15, 4,'',1,0,'C',1);
		$this->Cell(15, 4,'Qty',1,0,'C',1);// Qty
		$this->Cell(11, 4,'total',1,0,'C',1);// total
		$this->Cell(13, 4,'',1,0,'C',1);
		$this->Cell(13, 4,'',1,0,'C',1);
		$this->Cell(16, 4,'',1,1,'C',1);

		$this->Cell(37, 4,'',1,0,'C',1);// Color Assortment Summary
		$this->Cell(19, 4,'',1,0,'C',1); 
		$this->Cell(11, 4,'Code',1,0,'C',1); // Color code
		$this->Cell(40, 4,'COLOR DESCRIPTION',1,0,'C',1);// color OLOR DESCRIPTIO
		$this->Cell(15, 4,'',1,0,'C',1); // Size
		$this->Cell(15, 4,'',1,0,'C',1);
		$this->Cell(15, 4,'',1,0,'C',1);// Qty
		$this->Cell(11, 4,'',1,0,'C',1);// total
		$this->Cell(13, 4,'',1,0,'C',1);
		$this->Cell(13, 4,'',1,0,'C',1);
		$this->Cell(16, 4,'',1,1,'C',1);	
}

function assort($parm)
{
	for($i=0; $i<sizeof($parm['size']); $i++)
	{
		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','',8);
		$this->Cell(37, 4,'',1,0,'C',0);// Color Assortment Summary
		$this->Cell(19, 4,'',1,0,'C',0); 
		$this->Cell(11, 4,$parm['item'],1,0,'C',0); // Color code
		$this->SetFont('Arial','',7);
		$this->Cell(40, 4,$parm['color'],1,0,'C',0);// color name
		$this->SetFont('Arial','',8);
		$this->Cell(15, 4,'',1,0,'R',0); 
		$this->Cell(15, 4,$parm['size'][$i],1,0,'C',0);// size
		$this->Cell(15, 4,'',1,0,'R',0);
		$this->Cell(11, 4,NUMBER_FORMAT($parm['qty'][$i]),1,0,'R',0);// qty
		$this->Cell(13, 4,'',1,0,'R',0);
		$this->Cell(13, 4,'',1,0,'R',0);
		$this->Cell(16, 4,'',1,1,'C',0);
	}
		$this->Cell(37, 4,'',1,0,'C',0);// Color Assortment Summary
		$this->Cell(19, 4,'',1,0,'C',0); 
		$this->Cell(11, 4,'',1,0,'C',0); // Color code
		$this->Cell(40, 4,'',1,0,'C',0);// color name
		$this->Cell(15, 4,'',1,0,'R',0); 
		$this->Cell(15, 4,'',1,0,'C',0);// size
		$this->Cell(26, 4,'SUBTOTAL',1,0,'R',0);
//		$this->Cell(11, 4,'',1,0,'R',0);// qty
		$this->Cell(13, 4,NUMBER_FORMAT($parm['ttl_qty']),1,0,'R',0);
		$this->Cell(13, 4,'',1,0,'R',0);
		$this->Cell(16, 4,'',1,1,'C',0);

}

function ttl($qty)
{

		$this->Cell(37, 4,'Grand Total',1,0,'R',0);// Color Assortment Summary
		$this->Cell(19, 4,'',1,0,'C',0); 
		$this->Cell(11, 4,'',1,0,'C',0); // Color code
		$this->Cell(40, 4,'',1,0,'C',0);// color name
		$this->Cell(15, 4,'',1,0,'R',0); 
		$this->Cell(15, 4,'',1,0,'C',0);// size
		$this->Cell(15, 4,'',1,0,'R',0);
		$this->Cell(11, 4,NUMBER_FORMAT($qty),1,0,'R',0);// qty
		$this->Cell(13, 4,NUMBER_FORMAT($qty),1,0,'R',0);
		$this->Cell(13, 4,'',1,0,'R',0);
		$this->Cell(16, 4,'',1,1,'C',0);

		$this->Cell(205, 2,'',1,1,'C',1);
	
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
	global $name;
	
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8

    $this->SetFont('Big5','',8);
    //Page number
		$this->AliasNbPages('<nb>'); 
    $this->Cell(68,10,date('D/M/Y H:i'),0,0,'C');
    $this->Cell(68,10,'PACCK LIST Qy',0,0,'C');
    $this->Cell(68,10,$name,0,0,'C');
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




}  // end of class

?>
