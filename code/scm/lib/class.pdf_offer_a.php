<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_offer_a extends PDF_Chinese 	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;


//Page header
function Header() {

	global $print_title;
	global $print_title2;
	global $PHP_id;

	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,110,$PHP_id,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title
	$this->SetXY(100,15);
	$this->SetTextColor(40,40,40);
    $this->SetFont('Arial','I',9);
	$this->Cell(40, 7,$print_title2,0,0,'C',0);
	$this->SetFont('Arial','B',14);
	$this->Cell(55, 7,$print_title,1,1,'C',0);
	//date
//    $this->SetFont('Arial','I',9);
	$this->SetXY(180,18);
//	$this->Cell(20, 7,$dt['date'],0,1,'C',0);
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
	global $creator;
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8

    $this->SetFont('Big5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                          Created by : '.$creator,0,0,'C');
}


// field module 1 [表匡 1 ]
function filed_1($x,$y,$w,$h,$h2,$title,$color,$field) {
   $this->SetLineWidth(.3);

	$this->SetFont('Big5','',10);
	$this->SetDrawColor($color); 
	$this->SetTextColor($color); 
	$this->SetXY($x,$y);
	$this->Cell($w, $h,'','1',0,'',0);
	$this->SetXY($x,$y);
	$this->Cell($w, $h2,$title,'B',0,'C');
	$this->SetXY($x,$y+0.5);
	$this->Cell($w, $h2,'','B',0,'C');
		// field body
	$this->SetTextColor(0,0,0); 
	if ($title == 'Merchandiser'){$this->SetFont('big5','IB',10);}else{$this->SetFont('Arial','IB',10);}
	$this->SetXY($x,$y+$h2);
	$this->Cell($w, $h-$h2,$field,'B',0,'C');

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


function hend_title($of)
{
	$this->filed_1(70,28,25,10,5,'Offer #','80,110,140',$of['num']);
	$this->filed_1(98,28,65,10,5,'Style#','80,110,140',$of['style']);
	$this->filed_1(165,28,30,10,5,'Factory','80,110,140',$of['fty']);

	$this->filed_1(70,42,45,10,5,'Customer','80,110,140',$of['buyer']);
	$this->filed_1(118,42,40,10,5,'Merchandiser','80,110,140',$of['merchan']);
	$this->filed_1(160,42,35,10,5,'Currency','80,110,140','$USD');
	
	$this->filed_1(70,55,45,10,5,'Agent','80,110,140',$of['agent']);
	$this->filed_1(118,55,30,10,5,'SPT.','80,110,140',$of['spt']);
	$this->filed_1(150,55,20,10,5,'I.E.','80,110,140',$of['ie']);	
	$this->filed_1(172,55,23,10,5,'Style','80,110,140',$of['style_type']);	
	
	//$pdf->filed_1(109,44,32,15,7,'Order type','80,110,140',$wi['smpl_type']);
}
function descript($name,$of)
{
	$str_long = strlen($of);
	$des ='';
	$str=0;
	$end=100;
	while($str_long > 100)
	{
		$des[] = substr($of,$str,$end);
		$str =$end;
		$end = $end+100;
		$str_long=$str_long-100;
	}
	$des[] = substr($of,$str,$end);
	$this->SetFont('Arial','B',10);	
	$this->Cell(35,5,$name,0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(150,5,$des[0],0,0,'L');	
	$this->ln();
	for($i=1; $i<sizeof($des); $i++)
	{
		$this->Cell(35,5,'',0,0,'L');
		$this->Cell(150,5,$des[$i],0,0,'L');	
		$this->ln();		
	}
}
//fabric用表格
function Table_title() {
	$this->setx(10); 
	$this->SetFont('Arial','B',10);	
	$this->cell(34,5,' ',1,0,'C');
	$this->cell(50,5,'ITEM ',1,0,'C');
	$this->cell(50,5,'Placement ',1,0,'C');
	$this->cell(15,5,'U/Price ',1,0,'C');
	$this->cell(3,5,'',1,0,'C');
	$this->cell(15,5,'YY/QTY',1,0,'C');
	$this->cell(3,5,'',1,0,'C');
	$this->cell(15,5,'COST',1,0,'C');
	$this->ln();	
	$this->setx(10); 
	$this->cell(34,0.5,'',1,0,'C');
	$this->cell(50,0.5,'',1,0,'C');
	$this->cell(50,0.5,'',1,0,'C');
	$this->cell(15,0.5,'',1,0,'C');
	$this->cell(3,0.5,'',1,0,'C');
	$this->cell(15,0.5,'',1,0,'C');
	$this->cell(3,0.5,'',1,0,'C');
	$this->cell(15,0.5,'',1,0,'C');
	$this->ln();

}
function Table_cost_a($data) {
	for ($i=0; $i<sizeof($data); $i++)
	{
	 if($data[$i]['i'])
	 {
		$this->SetFont('Arial','',8);	
		$this->cell(34,5,$data[$i]['cate'],1,0,'R');
		$this->cell(50,5,$data[$i]['i'],1,0,'L');
		$this->cell(50,5,$data[$i]['u'],1,0,'L');
		$this->SetFont('Arial','',10);	
		$this->cell(15,5,$data[$i]['p'],1,0,'R');
		$this->cell(3,5,'',1,0,'C');
		$this->cell(15,5,$data[$i]['q'],1,0,'R');
		$this->cell(3,5,'',1,0,'C');
		$this->cell(15,5,$data[$i]['c'],1,0,'R');
		$this->ln();	
	 }
	}
}

function Table_cost($name,$data) {
		$this->SetFont('Arial','',8);	
		$this->cell(34,5,$name,1,0,'R');
		$this->cell(136,5,"--------------------------------------------------------------------------------------------------------------------------------------------",1,0,'C');		
		$this->SetFont('Arial','',10);	
		$this->cell(15,5,$data,1,0,'R');
		$this->ln();	
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
