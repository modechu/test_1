<?php
//include_once($config['root_dir']."/lib/chinese.php");
include_once($config['root_dir']."/lib/fpdf.php");
	define('FPDF_FONTPATH','font/');

//class PDF_fabric extends PDF_Chinese 	{
class PDF_fabric extends FPDF	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;

/*
//Page header
function Header() {

	global $print_title;
	global $PHP_id;

	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,210,$PHP_id,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title
	$this->SetFont('Arial','B',14);
	$this->SetXY(120,15);
	$this->SetTextColor(40,40,40);
	$this->Cell(45, 7,$print_title,1,1,'C',0);
	//date
    $this->SetFont('Arial','I',9);
	$this->SetXY(180,18);
	$this->Cell(20, 7,$dt['date'],0,1,'C',0);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,200,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,200,26);
	$this->ln(5);
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
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>',0,0,'C');
}

*/

// field module 1 [表匡 1 ]
function record($x,$y,$w1,$h1,$w2,$h2,$title,$field) {

	$this->SetFont('Arial','I',10);
	$this->SetTextColor(0,0,0); 
	$this->SetXY($x+6,$y+6);
	$this->Cell($w1, $h1, $title[0], 0, 1, 'L');
	$this->SetX($x+6);
	$this->Cell($w1, $h1, $title[1], 0, 1, 'L');
	$this->SetX($x+6);
	$this->Cell($w1, $h1, $title[2], 0, 1, 'L');
	$this->SetX($x+6);
	$this->Cell($w1, $h1, $title[3], 0, 0, 'L');
	$this->SetX($x+50);
	$this->Cell($w1, $h1, $title[4], 0, 1, 'L');
	$this->SetX($x+6);
	$this->Cell($w1, $h1, $title[5], 0, 1, 'L');

	$this->SetFont('Arial','BU',14);
	$this->SetTextColor(0,0,0); 
	$this->SetXY($x+20,$y+6);
	$this->Cell($w1, $h1, $field[0], 0, 1, 'L');
	$this->SetX($x+27);
	$this->SetFont('Arial','BU',10);
	$this->Cell($w1, $h1, $field[1], 0, 1, 'L');
	$this->SetX($x+27);
	$this->Cell($w1, $h1, $field[2], 0, 1, 'L');
	$this->SetX($x+18);
	$this->Cell($w1, $h1, $field[3], 0, 0, 'L');
	$this->SetX($x+63);
	$this->Cell($w1, $h1, $field[4], 0, 1, 'L');
	$this->SetX($x+21);
	$this->Cell($w1, $h1, $field[5], 0, 1, 'L');


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

//Colored table
function Table_1($x,$y,$header,$data,$w,$title,$R,$G,$B) {
    //Colors, line width and bold font
    $this->SetFillColor($R,$G,$B);
    $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);
    $this->SetXY($x,$y);
    // Header
	$this->Cell(array_sum($w),7,$title,0,0,'C');
    $this->Ln();
    $this->SetTextColor(255);
    $this->SetFont('','B');
	$this->SetX($x);

    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',1);
    $this->Ln();
    //Color and font restoration
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data

    foreach($data as $row)  {
		    $this->SetX($x);
		for($i=0;$i<count($header);$i++){
			$this->Cell($w[$i],6,$row[$i],1,0,'C',0);
		}
	$this->Ln();
    }
//    $this->Cell(array_sum($w),0,'','T');
}

//Colored table [表頭 再分 row ]  --- 主副料 table
function Table_2($x,$y,$header1,$header2,$header3,$data,$w1,$w2,$w3,$title,$R,$G,$B) {
    //Colors, line width and bold font
    $this->SetFillColor($R,$G,$B);
    $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);
    $this->SetXY($x,$y);
    // Header
	$this->Cell(array_sum($w1),7,$title,0,0,'C');
    $this->Ln();
    $this->SetTextColor(255);
	$this->SetX($x);

    for($i=0;$i<count($header1);$i++)
        $this->Cell($w1[$i],10,$header1[$i],1,0,'C',1);
	$y1 = $this->getY();
	$x1 = $this->getX();
	
	// 色組名稱 -----
    $this->SetTextColor(0,110,110);
    $this->SetFont('Arial','B',9);
    for($i=0;$i<count($header2);$i++)
        $this->Cell($w2[$i],5,$header2[$i],1,0,'C',0);
 
	// 最後的 total 欄
    $this->SetTextColor(0);
    $this->SetFillColor(200);
    $this->SetFont('Arial','B',10);
    $this->Cell(0.5,10,'',1,0,'C',0);
    $this->Cell(25,10,'Total',1,0,'C',1);
	
	
	$this->Ln(1);

	// 色組數量 -----
	$this->SetXY($x1,$y1+5);
    $this->SetFillColor(200);
	for($i=0;$i<count($header3);$i++)
        $this->Cell($w2[$i],5,$header3[$i],1,0,'C',1);

	$this->SetX($x);
    $this->Ln();

    //Color and font restoration
    $this->SetTextColor(0);
    $this->SetFont('','I',9);

	$w = array_merge_recursive($w1,$w2,'0.5','25');
//print_r($w);
//exit;
	// 用料 資料 Data...........
    foreach($data as $row)  {
		    $this->SetX($x);
		for($i=0;$i<count($header1)+count($header2)+2;$i++){
			settype($row[$i], "string");  
			$this->Cell($w[$i],5,$row[$i],1,0,'C',0);
		}
	$this->Ln();
    }
//    $this->Cell(array_sum($w),0,'','T');

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
