<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_ship extends PDF_Chinese 	{


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
	global $print_title1;
	global $print_title3;
	global $ary_title;
	
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(100,100,$mark,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +40;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title

	$this->SetXY(120,10);
	$this->SetTextColor(40,40,40);
	$this->SetFont('BIG5','B',13);
	$this->Cell(150, 7,$print_title1,0,0,'L',0);



	$this->SetXY(120,15);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','B',13);
	$this->Cell(150, 7,$print_title,0,0,'L',0);
	
	//date
	$this->SetXY(180,18);
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
	$this->hend_title();
	
}

function hend_title()
{

//title	
	$this->SetX(10);
	$this->SetFont('arial','B',10);
	$this->Cell(25, 5,'Invoice','LRT',0,'C');
	$this->Cell(25, 5,'Invoice No.','LRT',0,'C');	
	$this->Cell(21, 5,'Commodity','LRT',0,'C');	
	$this->Cell(25, 5,'Carnival','LRT',0,'C');	
	$this->Cell(36, 5,'CCC P.O.','LRT',0,'C');
	$this->Cell(36, 5,'CCC Style','LRT',0,'C');
	$this->Cell(23, 5,'Shipped','LRT',0,'C');
	$this->Cell(15, 5,'FOB','LRT',0,'C');
	$this->Cell(25, 5,'FOB','LRT',0,'C');
	$this->Cell(15, 5,'CMT','LRT',0,'C');
	$this->Cell(25, 5,'CMT','LRT',0,'C');
	$this->ln();

	$this->SetX(10);
	$this->SetFont('arial','B',10);
	$this->Cell(25, 5,'Date','LRB',0,'C');
	$this->Cell(25, 5,'','LRB',0,'C');	
	$this->Cell(21, 5,'','LRB',0,'C');	
	$this->Cell(25, 5,'P.O. NO.','LRB',0,'C');	
	$this->Cell(36, 5,'NO.','LRB',0,'C');
	$this->Cell(36, 5,'NO.','LRB',0,'C');
	$this->Cell(23, 5,'Quantity','LRB',0,'C');
	$this->Cell(15, 5,'Price','LRB',0,'C');
	$this->Cell(25, 5,'Amount','LRB',0,'C');
	$this->Cell(15, 5,'Price','LRB',0,'C');
	$this->Cell(25, 5,'Amount','LRB',0,'C');
	$this->ln();		
//雙線	
	$this->SetX(10);
	$this->SetFont('arial','B',10);
	$this->Cell(25, 0.5,'','LRB',0,'C');
	$this->Cell(25, 0.5,'','LRB',0,'C');	
	$this->Cell(21, 0.5,'','LRB',0,'C');	
	$this->Cell(25, 0.5,'','LRB',0,'C');	
	$this->Cell(36, 0.5,'','LRB',0,'C');
	$this->Cell(36, 0.5,'','LRB',0,'C');
	$this->Cell(23, 0.5,'','LRB',0,'C');
	$this->Cell(15, 0.5,'','LRB',0,'C');
	$this->Cell(25, 0.5,'','LRB',0,'C');
	$this->Cell(15, 0.5,'','LRB',0,'C');
	$this->Cell(25, 0.5,'','LRB',0,'C');
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
	global $creator;
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                           Created by : '.$creator,0,0,'C');
}


// field module 1 [表匡 1 ]


function ship_table($fld)
{
	$this->SetX(10);
	$this->SetFont('arial','',10);
	$this->Cell(25, 5,$fld['inv_date'],1,0,'C');
	$this->Cell(25, 5,$fld['inv_num'],1,0,'C');	
	$this->Cell(21, 5,$fld['style'],1,0,'C');	
	$this->Cell(25, 5,$fld['ord_num'],1,0,'C');	
	$this->SetFont('arial','',8);
	$this->Cell(36, 5,$fld['ref'],1,0,'L');
	$this->Cell(36, 5,$fld['style_num'],1,0,'L');
	$this->SetFont('arial','',10);
	$this->Cell(23, 5,$fld['qty']."PCS",1,0,'R');
	$this->Cell(15, 5,"$".$fld['uprice'],1,0,'R');
	$this->Cell(25, 5,"$".$fld['fob_amt'],1,0,'R');
	$this->Cell(15, 5,"$".$fld['cm'],1,0,'R');
	$this->Cell(25, 5,"$".$fld['cm_amt'],1,0,'R');
	$this->ln();


}

function ship_ttl($qty,$fob,$cm,$tt_name='')
{
	$this->Cell(168, 0.5,'',1,0,'L');
	$this->Cell(23, 0.5,'',1,0,'R');
	$this->Cell(40, 0.5,'',1,0,'R');
	$this->Cell(40, 0.5,'',1,0,'R');
	$this->ln();

	$this->SetX(10);
	$this->SetFont('arial','',10);
	$this->Cell(168, 5,$tt_name,1,0,'L');
	$this->Cell(23, 5,$qty."PCS",1,0,'R');
	$this->Cell(40, 5,"$".$fob,1,0,'R');
	$this->Cell(40, 5,"$".$cm,1,0,'R');
	$this->ln();



}

function mang_chk($coster)
{
	$this->SetY(180);
	$this->SetFont('BIG5','',10);
	$this->Cell(70, 7,'核准 : '.$coster['apv_user'],0,0,'C');
	$this->Cell(70, 7,'單位主管 : '.$coster['cfm_user'],0,0,'C');
	$this->Cell(70, 7,'製表 : '.$coster['submit_user'],0,0,'C');
	$this->Cell(70, 7,'製表日期 : '.$coster['submit_date'],0,0,'C');

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
