<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_sales_analysis extends PDF_Chinese 	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;


//Page header
function Header() {

	global $print_title;
	global $mark;
	global $print_title2;
	global $ary_title;
	
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(50,150,$mark,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title

	$this->SetXY(110,15);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','B',14);
	$this->Cell(175, 7,$print_title,1,0,'C',0);
//  $this->SetFont('Arial','I',12);
//	$this->Cell(90, 7,$print_title2,0,0,'R',0);
	
	//date
 //   $this->SetFont('Arial','I',9);
	$this->SetXY(180,18);
//	$this->Cell(20, 7,$dt['date'],0,1,'C',0);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,290,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,290,26);
	$this->ln(5);
	$Y = $this->getY();
	$this->setXY(10,$Y+5);
	
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
    $this->SetFont('Arial','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>',0,0,'C');
}




function sales_title($mm)
{
	$this->setX(5);
	$mm_ary = array('JAN','FEB','MAR','APR','MAY','JUNE','JULY','AUG','SEP','OCT','NOV','DEC');
	$this->SetFont('Arial','B',10);
	$this->Cell(30,10,'SUMMARY',1,0,'C');
	$this->SetFont('Arial','B',8);
	$this->Cell(21,10,'UPDATE('.$mm.')',1,0,'C');
	$this->SetFont('Arial','B',10);
	for($i=0; $i<sizeof($mm_ary); $i++)$this->Cell(18,10,$mm_ary[$i],1,0,'C');
	$this->Cell(20,10,'TOTAL',1,1,'C');
}

function sales_det($title,$det,$total)
{
	$this->setX(5);
	$this->SetFont('BIG5','',8);
	$this->Cell(30,6,$title,1,0,'L');
	$this->SetFont('Arial','B',8);
	$this->Cell(21,6,NUMBER_FORMAT($det[0]),1,0,'R');
	for($i=1; $i<sizeof($det); $i++)$this->Cell(18,6,NUMBER_FORMAT($det[$i]),1,0,'R');
	$this->Cell(20,6,NUMBER_FORMAT($total),1,1,'R');
}















}  // end of class

?>
