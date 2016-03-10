<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shidoc_ab1 extends PDF_Chinese 	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;


//Page header
function Header() {
	global $mark;

// marker		
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,150,$mark,30);
	//title
	$this->SetXY(90,5);
	$this->Image('./images/tu.jpg',10,5,20);	
	$this->SetLineWidth(0.5);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','B',16);
	$this->SetX(30);	
	$this->Cell(110, 20,"TU1 PACKING NOTE",0,0,'C',0);	
	$this->SetFont('Arial','',8);
	$xx = $this->GetX();
	$this->Cell(25, 5,"  DC use only : ",1,0,'L',0);
	$this->ln();

	$this->SetX($xx);
	$this->Cell(25, 5,"  App no ",1,0,'L',0);
	$this->ln();
	$this->SetX($xx);
	$this->Cell(25, 5,"  Time ",1,0,'L',0);
	$this->ln();
	$this->SetX($xx);
	$this->Cell(25, 5,"  Door ",1,0,'L',0);
	$this->ln();	
	$yy = $this->GetY();
	$this->SetXY(10,($yy+2));
  //$this->SetFont('Arial','I',9);
	//$this->Cell(30, 7,'Page '.$this->PageNo().' / <nb>',0,0,'C',0);

}

function base_data($shp)
{
	$MM2 = $GLOBALS['MM2'];
	$tmp = explode('-',$shp['cfm_date']);
	$shp['cfm_date'] = $tmp[2].",".$MM2[$tmp[1]].",".$tmp[0];
	
	$tmp = explode(' ',$shp['fnl_addr']);
	$str1 = $str2 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str1)+strlen($tmp[$i]);
		$len2 = strlen($str2)+strlen($tmp[$i]);
		if($len1 < 45 && strlen($str2) <= 1)
		{
			$str1 = $str1.$tmp[$i]." ";		
		}else{
			$str2 = $str2.$tmp[$i]." ";
		}
	}

	$this->SetFont('Arial','',10);
	$this->Cell(65, 5,"FROM (Supplier name & address)",0,0,'L',0);
	$this->Cell(85, 5,"TO",0,0,'L',0);
	$this->SetFont('Arial','B',14);
	$this->Cell(50, 10,"INITIAL",0,0,'L',0);
	$this->ln();
	$yy = $this->GetY();
	$this->SetY(($yy-5));
	
	$this->SetFont('Arial','',10);
	$this->Cell(65, 5,"CARNIVALINDUSTRIA CORP.",0,0,'L',0);
	$this->Cell(85, 5,$shp['fnl_name'],0,0,'L',0);
	$this->Cell(50, 5,"",0,0,'L',0);
	$this->ln();

	$this->SetFont('Arial','',10);
	$this->Cell(65, 5,"7FL., 25, JEN AI ROAD SEC .4, ",0,0,'L',0);
	$this->Cell(85, 5,$str1,0,0,'L',0);
	$this->Cell(50, 5,"PURCHASE",0,0,'L',0);
	$this->ln();

	$this->SetFont('Arial','',10);
	$this->Cell(65, 5,"TAIPEI, TAIWAN ROC, ",0,0,'L',0);
	$this->Cell(85, 5,$str2,0,0,'L',0);
	$this->Cell(50, 5,"ORDER",0,0,'L',0);
	$this->ln();


	$tmp = explode(' ',$shp['fnl_des']);
	$str1 = $str2 = $str3 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str1)+strlen($tmp[$i]);
		$len2 = strlen($str2)+strlen($tmp[$i]);
		if($len1 < 20 && strlen($str2) <= 1)
		{
			$str1 = $str1.$tmp[$i]." ";		
		}else if($len2 < 20 && strlen($str3) <= 1){
			$str2 = $str2.$tmp[$i]." ";
		}else{
			$str3 = $str3.$tmp[$i]." ";
		}
	}
	$this->ln();
	$this->ln();	
	$yy = $this->GetY();	
	$this->SetFont('Arial','',8);
	$this->Cell(40, 5,"SUPPLIER INVOICE NU ",1,0,'L',0);
	$this->Cell(50, 5,$shp['inv_num'],1,0,'L',0);
	$xx = $this->GetX();
	$this->ln();
	$this->Cell(40, 5,"SUPPLIER INVOICE DA ",1,0,'L',0);
	$this->Cell(50, 5,$shp['cfm_date'],1,0,'L',0);
	$this->ln();
	$this->Cell(40, 5,"ITEM STYLE NUMBER ",1,0,'L',0);
	$this->Cell(50, 5,$shp['fnl_style'],1,0,'L',0);
	$this->ln();
	$this->Cell(40, 5,"TU PURCHASE ORDER ",1,0,'L',0);
	$this->Cell(50, 5,$shp['fnl_po'],1,0,'L',0);
	$this->ln();
	$this->Cell(40, 5,"DEL DU   ",1,0,'L',0);
	$this->Cell(50, 5,"",1,0,'L',0);
	$this->ln();
	$this->Cell(40, 5,"",'TLR',0,'L',0);
	$this->Cell(50, 5,$str1,'TLR',0,'L',0);
	$this->ln();	
	$this->Cell(40, 5,"",'LR',0,'L',0);
	$this->Cell(50, 5,$str2,'LR',0,'L',0);
	$this->ln();	
	$this->Cell(40, 5,"DESCRIPTION",'LR',0,'L',0);
	$this->Cell(50, 5,$str3,'LR',0,'L',0);
	$this->ln();	
	$this->Cell(40, 5,"COLOR   ",1,0,'L',0);
	$this->Cell(50, 5,$shp['color'],1,0,'L',0);
	$this->ln();	

	$this->Cell(40, 5,"DEPT NUMBER ",1,0,'L',0);
	$this->Cell(50, 5,$shp['fnl_dept'],1,0,'L',0);
	$this->ln();	
	$this->Cell(40, 5,"DEPT DESCRIPTION   ",1,0,'L',0);
	$this->Cell(50, 5,$shp['dept_des'],1,0,'L',0);
	$this->ln();	
	
	
	$que_ary = array('N','Y');

	$this->SetXY(($xx),$yy);
	$this->Cell(80, 5,"CONTAINER NUMBER ",'TLR',0,'L',0);
	$this->ln();	
	$this->SetX(($xx));
	$this->Cell(80, 5,$shp['ctn_num'],'BLR',0,'C',0);
	$this->ln();	
	$this->SetX(($xx));
	$this->Cell(80, 5,"SEAL NUMBER ",'TLR',0,'L',0);
	$this->ln();	
	$this->SetX(($xx));
	$this->Cell(80, 5,$shp['seal'],'BLR',0,'C',0);
	$this->ln();	

	$this->SetX(($xx));
	$this->SetFont('Arial','',6);
	$this->Cell(80, 10,'Please indicate the appropriate response','TLR',0,'L',0);
	$this->ln();	

	
	$this->SetX(($xx));
	$this->SetFont('Arial','',8);
/*
	if($shp['que1'] != 'Y')$shp['que1'] = 'N';
	if($shp['que2'] != 'Y')$shp['que2'] = 'N';
	if($shp['que3'] != 'Y')$shp['que3'] = 'N';
	if($shp['que4'] != 'Y')$shp['que4'] = 'N';
	if($shp['que5'] != 'Y')$shp['que5'] = 'N';
*/		
	$this->Cell(60, 5,'HANGING GAREMENTS','L',0,'L',0);
	$this->Cell(20, 5,$shp['que1'],'R',0,'C',0);
	$this->ln();	
	
	$this->SetX(($xx));	
	$this->Cell(60, 5,'BOXED GARMENTS ON HANGER','L',0,'L',0);
	$this->Cell(20, 5,$shp['que2'],'R',0,'C',0);
	$this->ln();	

	$this->SetX(($xx));	
	$this->Cell(60, 5,'LOOSE CARTONS','L',0,'L',0);
	$this->Cell(20, 5,$shp['que3'],'R',0,'C',0);
	$this->ln();		

	$this->SetX(($xx));	
	$this->Cell(60, 5,'PALLETISED CARTONS','L',0,'L',0);
	$this->Cell(20, 5,$shp['que4'],'R',0,'C',0);
	$this->ln();	

	$this->SetX(($xx));	
	$this->Cell(60, 5,'ACCESSORIES','LB',0,'L',0);
	$this->Cell(20, 5,$shp['que5'],'RB',0,'C',0);
	$this->ln();	
	$this->ln();	
}

function item_title($unit)
{
		$this->SetLineWidth(0.2);
		$this->SetFont('Arial','',8);
		$this->Cell(33, 4,'','TLR',0,'C',0);
		$this->Cell(33, 4,'Item - Initial pack ','TLR',0,'C',0);
		$this->Cell(33, 4,'Number of initial','TLR',0,'C',0);
		$this->Cell(33, 4,'Total Number of','TLR',0,'C',0);
		$this->Cell(33, 4,'Total Number of','TLR',0,'C',0);
		$this->Cell(33, 4,'Carton','TLR',0,'C',0);
		$this->ln();
		$this->Cell(33, 4,'','LR',0,'C',0);
		$this->Cell(33, 4,'number','LR',0,'C',0);
		$this->Cell(33, 4,'packs per carton','LR',0,'C',0);
		$this->Cell(33, 4,'cartons','LR',0,'C',0);
		$this->Cell(33, 4,'Initial Packs','LR',0,'C',0);
		$this->Cell(33, 4,'dimensions','LR',0,'C',0);
		$this->ln();
		$this->Cell(33, 4,'Carton Number','BLR',0,'C',0);
		$this->Cell(33, 4,'','BLR',0,'C',0);
		$this->Cell(33, 4,'(A)','BLR',0,'C',0);
		$this->Cell(33, 4,'(B)','BLR',0,'C',0);
		$this->Cell(33, 4,'(AXB)','BLR',0,'C',0);
		$this->Cell(33, 4,'('.$unit.')','BLR',0,'C',0);
		$this->ln();
}


function item_txt($parm)
{
		$ctn_count = $parm['e_cnt'] - $parm['s_cnt'] + 1;
		$ttl_qty = $ctn_count * $parm['s_qty'];
		if($parm['ctn_l'] == 0 && $parm['ctn_w']== 0 && $parm['ctn_h'] == 0)
		{
			$dim = '"';
		}else{
			$dim = $parm['ctn_l']."X".$parm['ctn_w']."X".$parm['ctn_h'];
		}
		$this->SetFont('Arial','',8);

		$this->Cell(33, 4,$parm['cnt'],1,0,'L',0);
		$this->Cell(33, 4,'',1,0,'C',0);
		$this->Cell(33, 4,NUMBER_FORMAT($parm['s_qty'],0),1,0,'R',0);
		$this->Cell(33, 4,NUMBER_FORMAT($ctn_count,0),1,0,'R',0);
		$this->Cell(33, 4,NUMBER_FORMAT($ttl_qty,0),1,0,'R',0);
		$this->Cell(33, 4,$dim,1,0,'C',0);
		$this->ln();
		

}

function item_total($parm,$ctn)
{
		$this->SetFillColor(157,149,150);
		$this->Cell(33, 4,'',0,0,'C',0);
		$this->Cell(33, 4,'Total',1,0,'C',0);
		$this->Cell(33, 4,'',1,0,'C',1);

		$this->ln();	
}

function sum_title()
{
		$this->ln();
		$this->SetLineWidth(0.2);
		$this->SetFont('Arial','',8);
		$this->Cell(100, 4,'TOTAL OF ALL INITIAL PACKS',1,0,'C',0);
		$this->ln();
		$this->Cell(30, 4,'SIZE',1,0,'C',0);
		$this->Cell(70, 4,'TOTAL QUANTITY OF SINGLES PER SIZE',1,0,'C',0);
		$this->ln();
}

function sum_txt($size,$qty)
{
		$this->SetLineWidth(0.2);
		$this->SetFont('Arial','',8);
		$this->Cell(30, 4,$size,1,0,'C',0);
		$this->Cell(70, 4,NUMBER_FORMAT($qty,0),1,0,'C',0);
		$this->ln();
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
	global $foot;
	$inv = explode('-',$foot['inv_num']);
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8

    $this->SetFont('Big5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
  $this->Cell(50,10,date('Y/m/d h:i A'),0,0,'C');
  $this->Cell(50,10,$inv[0]." ".$foot['cust']." sea ".$foot['ver']."-".$this->PageNo(),0,0,'C');
  $this->Cell(70,10,'buer pl '.$foot['po'],0,0,'C');

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
