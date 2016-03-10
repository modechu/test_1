<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_pack_jg1 extends PDF_Chinese 	{


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
	global $mark;
	global $print_title2;
/*	
	$main_rec['fty'] = 'LY';
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
*/
  $this->SetFont('Arial','B',12);
	$this->SetXY(5,5);
	$this->Cell(290, 6,'JONES APPAREL GROUP / PACKING LIST COVER SHEET',0,1,'C');
	$this->Cell(290, 5,'Shipping To:  Jones Apparel Canada',0,1,'C');

	$this->SetFont('Arial','',8);
	$this->cell(290, 4,'388 Applewood Cresscent',0,1,'C');
	$this->cell(290, 4,'Vaughan, Ontario L4K 4B4',0,1,'C');
	$this->cell(290, 4,'Attn: Alvin Li',0,1,'C');

	$this->SetY(38);
	$this->SetFont('Arial','B',12);
	$this->cell(60, 5,"Note:",0,0,'R');
	$this->SetFont('Arial','',12);
	$this->cell(220, 5,"Please complete the below information.   This sheet is now a required element of JAG's PL.",0,1,'L');
	$this->SetFont('Arial','B',11);
	$this->cell(280, 5,"Postal Code must be entered except from the following countries: Hong Kong, Macau, Mauritius, or UAE.",0,1,'C');


	$dt = decode_date(1);
    //Put watermark
  $this->SetFont('Arial','B',50);
  $this->SetTextColor(230,210,210);
  $this->RotatedText(100,150,$mark,30);
  $this->SetTextColor(0,0,0);

	
	$this->SetY(52);
//	$this->hend_title($main_rec);
	
}

function main_txt($name1,$parm1,$name2,$parm2)
{
	//全長 280

	$tmp = explode(' ',$parm1['addr']);
	$str1 = $str2 = $str3 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str1)+strlen($tmp[$i]);
		$len2 = strlen($str2)+strlen($tmp[$i]);
		$len3 = strlen($str3)+strlen($tmp[$i]);
		if($len1 < 32 && strlen($str2) <= 1)
		{
			$str1 = $str1.$tmp[$i]." ";
		}else if($len2 < 32 && strlen($str3) <= 1){
			$str2 = $str2.$tmp[$i]." ";
		}else{
			$str3 = $str3.$tmp[$i]." ";
		}
	}	
	
	$tmp = explode(' ',$parm2['addr']);
	$str4 = $str5 = $str6 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len4 = strlen($str4)+strlen($tmp[$i]);
		$len5 = strlen($str5)+strlen($tmp[$i]);
		$len6 = strlen($str6)+strlen($tmp[$i]);
		if($len4 < 32 && strlen($str5) <= 1)
		{
			$str4 = $str4.$tmp[$i]." ";
		}else if($len5 < 32 && strlen($str6) <= 1){
			$str5 = $str5.$tmp[$i]." ";
		}else{
			$str6 = $str6.$tmp[$i]." ";
		}
	}		
	
	if($name1 == 'Container Stuffing Location')
	{
		$this->SetFont('Arial','B',10);
		$this->Cell(135, 6,$name1,0,0,'L',0);
		$this->Cell(10, 6,"",0,0,'L',0);
		$this->Cell(135, 6,'',0,1,'L',0);

		$this->SetFont('Arial','B',8);
		$this->Cell(135, 6,'(name and address of the physical location where the goods were stuffed into the containe)',0,0,'L',0);
		$this->SetFont('Arial','B',10);
		$this->Cell(10, 6,"",0,0,'L',0);
		$this->Cell(135, 6,$name2,0,1,'L',0);
	}else{
		$this->SetFont('Arial','B',10);
		$this->Cell(135, 4,$name1,0,0,'L',0);
		$this->Cell(10, 4,"",0,0,'L',0);
		$this->Cell(135, 4,$name2,0,1,'L',0);
	
	}

	$this->SetFont('Arial','',10);
	$this->Cell(25, 6,'Name:',0,0,'L',0);
	$this->Cell(110, 6,$parm1['name'],'B',0,'C',0);
	$this->Cell(10, 6,"",0,0,'L',0);
	$this->Cell(25, 6,'Name:',0,0,'L',0);
	$this->Cell(110, 6,$parm2['name'],'B',1,'C',0);

	$this->Cell(25, 6,'Address:',0,0,'L',0);
	$this->Cell(110, 6,$str1,'B',0,'C',0);
	$this->Cell(10, 6,"",0,0,'L',0);
	$this->Cell(25, 6,'Address:',0,0,'L',0);
	$this->Cell(110, 6,$str4,'B',1,'C',0);

	$this->Cell(25, 6,'Address:',0,0,'L',0);
	$this->Cell(110, 6,$str2,'B',0,'C',0);
	$this->Cell(10, 6,"",0,0,'L',0);
	$this->Cell(25, 6,'Address:',0,0,'L',0);
	$this->Cell(110, 6,$str5,'B',1,'C',0);

	$this->Cell(25, 6,'Address:',0,0,'L',0);
	$this->Cell(110, 6,$str3,'B',0,'C',0);
	$this->Cell(10, 6,"",0,0,'L',0);
	$this->Cell(25, 6,'Address:',0,0,'L',0);
	$this->Cell(110, 6,$str6,'B',1,'C',0);

	$this->Cell(25, 6,'City',0,0,'L',0);
	$this->Cell(45, 6,$parm1['city'],'B',0,'C',0);
	$this->Cell(25, 6,'State/Province',0,0,'L',0);
	$this->Cell(40, 6,$parm1['state'],'B',0,'C',0);
	$this->Cell(10, 6,"",0,0,'L',0);
	$this->Cell(25, 6,'City',0,0,'L',0);
	$this->Cell(45, 6,$parm2['city'],'B',0,'C',0);
	$this->Cell(25, 6,'State/Province',0,0,'L',0);
	$this->Cell(40, 6,$parm2['state'],'B',1,'C',0);

	$this->Cell(25, 6,'Postal Code',0,0,'L',0);
	$this->Cell(40, 6,$parm1['postal'],'B',0,'C',0);
	$this->Cell(20, 6,'Country',0,0,'L',0);
	$this->Cell(50, 6,$parm1['country'],'B',0,'C',0);
	$this->Cell(10, 6,"",0,0,'L',0);
	$this->Cell(25, 6,'Postal Code',0,0,'L',0);
	$this->Cell(40, 6,$parm2['postal'],'B',0,'C',0);
	$this->Cell(20, 6,'Country',0,0,'L',0);
	$this->Cell(50, 6,$parm2['country'],'B',1,'C',0);
	
	
	$yy = $this->GetY();
	$this->SetY(($yy+3));
}

function hend_title($parm)
{
// 長度:203

	$this->SetFont('Arial','B',8);
	$this->Cell(100, 5,"JAG Packing List",'TL',0,'L',0);
	$this->Cell(33, 5,"Factory Name:",'T',0,'L',0);
	$this->Cell(70, 5,$GLOBALS['SHIP_TO'][$parm['fty']]['Name'],'TR',1,'L',0);

	$this->SetFont('Arial','',8);
	$this->Cell(100, 5,"Email to: Shipments@inv.com",'BL',0,'L',0);
	$this->Cell(33, 5,"Contact Email/Phone#:",'B',0,'L',0);
	$this->Cell(70, 5,$parm['submit_mail'],'BR',1,'L',0);



	$yy = $this->GetY();
	$this->SetY(($yy+2));


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
    $this->SetFont('Arial','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,$print_title2."    ".date('Y/m/d h:i A'),0,0,'R');
}


// field module 1 [表匡 1 ]




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
