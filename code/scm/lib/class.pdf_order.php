<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_order extends PDF_Chinese 	{


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

	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,210,$mark,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title

	$this->SetXY(100,15);
	$this->SetTextColor(40,40,40);
    $this->SetFont('Arial','I',9);
	$this->Cell(55, 7,$print_title2,0,0,'C',0);
	$this->SetFont('Arial','B',14);

	$this->Cell(30, 7,$print_title,1,1,'C',0);
	//date
 //   $this->SetFont('Arial','I',9);
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
    $this->SetFont('BIG5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                           Created by : '.$creator,0,0,'C');
}


// field module 1 [ªí¦J 1 ]
function filed_1($x,$y,$w,$h,$h2,$title,$color,$field) {
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
	$this->SetFont('Arial','IB',10);
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

function base_data($data) {
    //Column widths

    $this->SetFont('Arial','B',10);
    $this->Cell(18,6,'ORDER # : ',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(26,6,$data['order_num'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(22,6,'Cust. PO# :',0,0,'R');
    $this->SetFont('Arial','',10);
    $cust_po = '';
    for($x=0; $x<sizeof($data['cust_po']); $x++)$cust_po .=$data['cust_po'][$x].',';
    
    $this->Cell(124,6,substr($cust_po,0,-1),1,0,'L');

    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);

    $this->SetFont('Arial','B',10);
    $this->Cell(18,6,'style No.: ',0,0,'R');
  if(strlen($data['style_num']) > 10)
  {
    $this->SetFont('Arial','',6);
  }else{
    $this->SetFont('Arial','',10); 
  }
    $this->Cell(26,6,$data['style_num'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(22,6,'Customer :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(30,6,$data['cust_iname'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(27,6,'customer ref. :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,$data['ref'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(17,6,'Team :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(15,6,$data['dept'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
    $this->SetFont('Arial','B',10);
    $this->Cell(18,6,'factory : ',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(26,6,$data['factory'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(22,6,'style type :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(30,6,$data['style'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(27,6,'Q\'ty :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,$data['qty']." ".$data['unit'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(17,6,'SU :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(15,6,$data['f_su'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);

    $this->SetFont('Arial','B',10);
    $this->Cell(18,6,'FOB @ : ',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(26,6,$data['uprice'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(22,6,'Quota Cat. :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(30,6,$data['quota'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(27,6,'smpl ord No. :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,$data['smpl_ord'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(17,6,'calc. IE :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(15,6,$data['ie1'],1,0,'L');
    
    $this->Ln();
		$y = $this->getY();
		$this->Line(10,($y+2),200,($y+2));
		$this->Ln();
		
    $this->SetFont('Arial','B',10);
    $this->Cell(18,6,'pattern # : ',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(26,6,$data['patt_num'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(22,6,'Agnet :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(30,6,$data['agent'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(27,6,'sample APV. :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,$data['smpl_apv'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(17,6,'final IE :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(15,6,$data['ie2'],1,0,'L');

    $this->Ln();
		$y = $this->getY();
		$this->Line(10,($y+2),200,($y+2));
		$this->Ln();
		
    $this->SetFont('Arial','B',10);
    $this->Cell(18,6,'ETD : ',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(26,6,$data['etd'],1,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(22,6,'ETP :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(54,6,$data['etp'],1,0,'L');

    $this->SetFont('Arial','B',10);
    $this->Cell(50,6,'est. Lead Time :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(20,6,$data['lead_time']."days",1,0,'L');

    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);

    $this->SetFont('Arial','B',10);
    $this->Cell(18,6,' ',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(26,6,'',0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(22,6,'',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(54,6,'',0,0,'L');

    $this->SetFont('Arial','B',10);
    $this->Cell(50,6,'Production Line :',0,0,'R');
    $this->SetFont('Arial','',10);
    if ($data['line_sex'] == 'F')
    {
    	$this->Cell(20,6,"Women",1,0,'L');
    }else{
  	}
    $this->Ln();

}


function cost_data($data) {
    //Column widths
    $this->SetFont('Arial','B',10);
    $this->Cell(165,6,'YY (estimate) :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['mat_useage']." / ".$data['lots_unit'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(165,6,'main fabric unit price :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['mat_u_cost'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(165,6,'Fusible unit price :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['fusible'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(165,6,'Interlining unit price :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['interline'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(165,6,'accessory cost per unit :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['acc_u_cost'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(165,6,'quota charge per unit [if any] :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['quota_fee'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(165,6,'commission per unit [if any] :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['comm_fee'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(165,6,'C.M. estimate per unit :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(25,6,$data['cm'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		
		$this->Cell(70,6,' ',0,0,'C');
		$this->Cell(50,6,' ---------------------- ',0,0,'C');
		$this->SetFont('Arial','B',10);
		$this->Cell(20,6,' special treatment cost  ',0,0,'C');
		$this->SetFont('Arial','',10);
		$this->Cell(60,6,'------------------------------- ',0,0,'C');
		$this->ln();		
		
		$this->SetFont('Arial','B',10);
    $this->Cell(155,6,'embroidery :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,$data['emb'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);		
		$this->SetFont('Arial','B',10);
    $this->Cell(155,6,'garment wash :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,$data['wash'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);		

		$this->SetFont('Arial','B',10);
    $this->Cell(155,6,'other treatment :',0,0,'R');
    $this->SetFont('Arial','',10);
    $oth = $data['oth'];
    if ($data['oth_treat'])$oth .= " ( for ".$data['oth_treat']." ) ";
    $this->Cell(35,6,$oth,1,0,'L');

    $this->Ln();
		$y = $this->getY();
		$this->Line(80,($y+2),200,($y+2));
		$this->Ln();
		
		$this->SetFont('Arial','B',10);
    $this->Cell(155,6,'estimate Total Sales of this order :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,"$".$data['sales'],1,0,'R');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);		
		$this->SetFont('Arial','B',10);
    $this->Cell(155,6,'estimate Total Cost of good sold :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,"$".$data['grand_cost'],1,0,'R');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(155,6,'unit cost :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,"@$".$data['unit_cost'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);
		$this->SetFont('Arial','B',10);
    $this->Cell(155,6,'sample cost :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,"    $".$data['smpl_fee'],1,0,'L');

    $this->Ln();
		$y = $this->getY();
		$this->Line(10,($y+2),200,($y+2));
		$this->Ln();

		$this->SetFont('Arial','B',10);
    $this->Cell(80,6,'estimate Gross Margin from this order :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,"$".$data['gm'],1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);

		$this->SetFont('Arial','B',10);
    $this->Cell(80,6,'G.M. rate :',0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(35,6,$data['gm_rate']."%",1,0,'L');
    $this->Ln();
		$y = $this->getY();
		$this->setY($y+1);	
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
