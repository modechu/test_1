<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_smpl_rcvd extends PDF_Chinese 	{


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
	//global $print_title2;
	global $main;
	
	$print_title = "樣品驗收明細[".$mark."]";



	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('BIG5','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(70,100,$mark,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +10;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title

	$this->SetXY(110,15);
	$this->SetTextColor(40,40,40);
	$this->SetFont('BIG5','B',12);
	$this->Cell(90, 7,$print_title,1,0,'C',0);
	
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,200,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,200,26);
	$yy = $this->GetY();
	$this->SetY(($yy+12));	
	$this->top($main);
	$yy = $this->GetY();
	$this->SetY(($yy+8));	
	$this->title();
	$yy = $this->GetY();
	$this->SetY(($yy+5));	

}


function top($parm)
{	
	if($parm['receiver'] == 'KA') $parm['receiver'] = '貿易業務A組';
	if($parm['receiver'] == 'KB') $parm['receiver'] = '貿易業務B組';

	if($parm['shipper'] == 'HJ') $parm['shipper'] = '湖嘉廠';
	if($parm['shipper'] == 'LY') $parm['shipper'] = '立元廠';
	if($parm['shipper'] == 'WX') $parm['shipper'] = '無錫廠';
	if($parm['shipper'] == 'CL') $parm['shipper'] = '中壢廠';
	
	$this->SetFont('BIG5','',12);
	$this->Cell(20,5,'Receiver : ',0,0,'L');
	$this->Cell(50,5,$parm['receiver'],0,0,'L');
	$this->Cell(18,5,'Shipper : ',0,0,'L');
	$this->Cell(40,5,$parm['shipper'],0,0,'L');
	$this->Cell(28,5,'Receiver date : ',0,0,'L');
	$this->Cell(50,5,$parm['rcv_date'],0,0,'L');	

}

function title()
{	
	
	$this->SetFont('Arial','B',10);
	$this->Cell(50,5,'Sample# ',1,0,'C');
	$this->Cell(50,5,'Style#',1,0,'C');
	$this->Cell(30,5,'Style ',1,0,'C');
	$this->Cell(30,5,'Size',1,0,'C');	
	$this->Cell(30,5,'QTY',1,0,'C');	

}

function txt($parm)
{	
	
	$this->SetFont('Arial','B',10);
	$this->Cell(50,5,$parm['smpl_num'],1,0,'C');
	$this->Cell(50,5,$parm['ref'],1,0,'C');
	$this->Cell(30,5,$parm['style'],1,0,'C');
	$this->Cell(30,5,$parm['size'],1,0,'C');	
	$this->Cell(30,5,$parm['ship_qty']." ",1,1,'R');	
}

function sum($qty)
{	
	
	$this->SetFont('Arial','B',10);
	$this->Cell(160,5,"TOTAL : ",1,0,'R');
	$this->Cell(30,5,NUMBER_FORMAT($qty,0)." ",1,1,'R');	
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
    $this->SetFont('BIG5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                           Created by : '.$creator,0,0,'C');
}







// table founctions----------






//---------- table with Muti cell ----------------	
var $widths;
var $aligns;


	


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
