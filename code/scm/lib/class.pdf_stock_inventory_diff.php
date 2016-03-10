<?php
include_once($config['root_dir']."/lib/chinese--u8.php");
define('FPDF_FONTPATH','font/');

class PDF_stock extends PDF_Chinese 	{

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
	$this->RotatedText(100,150,$mark,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,60);
	
	//title
	$this->SetXY(90,15);
	$this->SetTextColor(40,40,40);
	$this->SetFont('big5','B',14);
	$this->Cell(60, 7,$print_title,1,0,'C',0);
	$this->SetXY(150,15);
	$this->SetFont('big5','I',8);
	$this->Cell(40, 7,$print_title2,0,0,'C',0);
	$this->SetFont('Arial','I',10);
	$this->SetXY(180,18);
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,201,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,201,26);
	$this->ln(8);
	$this->hend_title($ary_title);
}

function hend_title($parm)
{
	$this->Cell(8,7,$parm['no'],1,0,'C');
	$this->Cell(20,7,$parm['ord'],1,0,'C');
	$this->Cell(40,7,$parm['mat_name'],1,0,'C');
	$this->Cell(40,7,$parm['color'],1,0,'C');
	$this->Cell(10,7,$parm['l_no'],1,0,'C');
	$this->Cell(10,7,$parm['r_no'],1,0,'C');
	$this->Cell(10,7,$parm['unit'],1,0,'C');
	$this->Cell(15,7,$parm['storage'],1,0,'C');
	$this->Cell(18,7,$parm['scm_qty'],1,0,'C');
	$this->Cell(20,7,$parm['final_qty'],1,0,'C');
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
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                           ',0,0,'C');
}


// field module 1 [表匡 1 ]
function filed_1($x,$y,$w,$h,$h2,$title,$color,$field,$alin='C') {
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
	if ($title == '單位' || $title == '供應商名稱'){
		$this->SetFont('big5','IB',8);
	}else if($title == 'Invoice'){
		$this->SetFont('Arial','I',10);
	}else{
		$this->SetFont('big5','IB',10);
	}
	
	// $this->SetFont('Big5','IB',12);
	$this->SetXY($x,$y+$h2);
	$this->Cell($w, $h-$h2,$field,'B',0,$alin);
}



function rcv_title()
{
	$this->SetFont('Arial','B',10);
	$this->Cell(21,10,'Order #',1,0,'C');
	$this->Cell(25,10,'Material #',1,0,'C');
	$this->Cell(60,10,'Name',1,0,'C');
	$this->Cell(60,10,'Color/cat.',1,0,'C');

//	$this->Cell(45,10,'Name',1,0,'C');
//	$this->Cell(40,10,'Color/cat.',1,0,'C');


	$this->Cell(22,10,'PO #',1,0,'C');
	$this->Cell(28,10,'PO Q\'ty',1,0,'C');
//	$this->Cell(25,10,'U/Price',1,0,'C');
	$this->Cell(28,10,'Q\'ty',1,0,'C');	
	$this->Cell(19,10,'ETA',1,0,'C');
//	$this->Cell(27,10,'Amount',1,0,'C');
	//雙線
	$this->ln();
	$this->Cell(21,0.5,'',1,0,'C');
	$this->Cell(25,0.5,'',1,0,'C');
	$this->Cell(60,0.5,'',1,0,'C');
	$this->Cell(60,0.5,'',1,0,'C');
	$this->Cell(22,0.5,'',1,0,'C');
	$this->Cell(28,0.5,'',1,0,'C');
//	$this->Cell(25,0.5,'',1,0,'C');
	$this->Cell(28,0.5,'',1,0,'C');	
	$this->Cell(19,0.5,'',1,0,'C');
//	$this->Cell(27,0.5,'',1,0,'C');
}

function rcv_det($rcv)
{
	$this->SetFont('Arial','',9);	
	$this->Cell(21,10,$rcv['ord_num'],1,0,'C');
	$this->Cell(25,10,$rcv['mat_code'],1,0,'C');
	$this->SetFont('big5','',9);
	$this->Cell(60,10,$rcv['mat_name'],1,0,'L');
	$this->Cell(60,10,$rcv['color'],1,0,'L');
	$this->SetFont('Arial','',9);	
	$this->Cell(22,10,$rcv['po_num'],1,0,'C');
	$this->Cell(28,10,$rcv['po_qty'].' '.$rcv['unit'],1,0,'R');
//	$this->Cell(25,10,$rcv['currency'].'@$ '.$rcv['price'],1,0,'R');
	$this->Cell(28,10,$rcv['qty'].' '.$rcv['unit'],1,0,'R');	
	$this->Cell(19,10,$rcv['eta'],1,0,'R');
//	$this->Cell(27,10,$rcv['currency'].'$ '.$rcv['amount'],1,0,'R');
	$this->ln();
}

function rcv_det_mix($rcv,$ords)
{
	
	$this->SetFont('Arial','',9);	
	$this->Cell(21,5,$ords[0],'RL',0,'C');
	$this->Cell(25,5,$rcv['mat_code'],'RL',0,'C');
	$this->SetFont('big5','',9);
	$this->Cell(60,5,$rcv['mat_name'],'RL',0,'L');
	$this->Cell(60,5,$rcv['color'],'RL',0,'L');
	$this->SetFont('Arial','',9);	
	$this->Cell(22,5,$rcv['po_num'],'RL',0,'C');
	$this->Cell(28,5,$rcv['po_qty'].' '.$rcv['unit'],'RL',0,'R');
//	$this->Cell(25,5,$rcv['currency'].'@$ '.$rcv['price'],'RL',0,'R');
	$this->Cell(28,5,$rcv['qty'].' '.$rcv['unit'],'RL',0,'R');	
	$this->Cell(19,5,$rcv['eta'],'RL',0,'R');
//	$this->Cell(27,5,$rcv['currency'].'$'.$rcv['amount'],'RL',0,'R');
	$this->ln();
	for ($i=1; $i< (sizeof($ords)-1); $i++)
	{
		$this->Cell(21,5,$ords[$i],'RL',0,'C');
		$this->Cell(25,5,'','RL');
		$this->Cell(60,5,'','RL');
		$this->Cell(60,5,'','RL');
		$this->Cell(22,5,'','RL');
		$this->Cell(28,5,'','RL');
//		$this->Cell(25,5,'','RL');
		$this->Cell(28,5,'','RL');	
		$this->Cell(19,5,'','RL');
//		$this->Cell(27,5,'','RL');
		$this->ln();
	}
	$this->SetFont('Arial','',9);
		$this->Cell(21,5,$ords[$i],'RLB',0,'C');
		$this->Cell(25,5,'','RLB');
		$this->Cell(60,5,'','RLB');
		$this->Cell(60,5,'','RLB');
		$this->Cell(22,5,'','RLB');
		$this->Cell(28,5,'','RLB');
//		$this->Cell(25,5,'','RLB');
		$this->Cell(28,5,'','RLB');	
		$this->Cell(19,5,'','RLB');
//		$this->Cell(27,5,'','RLB');
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
/*
//Colored table
function Table_1($x,$y,$header,$data,$w,$title,$R,$G,$B,$base_size) {
    //Colors, line width and bold font
    $this->SetFillColor($R,$G,$B);
    $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);
    $this->SetXY($x,$y);
    // Header
	$this->Cell(array_sum($w),7,$title,0,0,'C');
    $this->Ln();
//    $this->SetTextColor(255);
    $this->SetFont('','B');
	$this->SetX($x);
	
	$this->SetFont('Arial','B',10);
    for($i=0;$i<count($header);$i++)
    {
        if($header[$i] == $base_size)
        {
        	$this->SetFont('Arial','BI',10);
        	$this->Cell($w[$i],7,'['.$header[$i].']',1,0,'C');
        }else{
        	$this->SetFont('Arial','B',10);
	        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    	}
    }
    $this->Cell(($w[$i]+3),7,'SUM',1,0,'C');
    $this->SetFont('Arial','',10);
    $this->Ln();
	$this->SetX($x);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],0.5,'',1,0,'C');
    $this->Cell(($w[$i]+3),0.5,'',1,0,'C');
    $this->Ln();

    //Color and font restoration
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
 //  $this->SetFont('Big5','',10);
   $tmp=0;
    foreach($data as $row)  {
		    $this->SetX($x);
		for($i=0;$i<count($header);$i++){
			if (strlen($row[$i]) > 18)
			{
				$this->SetFont('Arial','',6);
			}else if(strlen($row[$i]) > 8){
				$this->SetFont('Arial','',8);
			}else{
				$this->SetFont('Arial','',10);
			}
			$this->Cell($w[$i],6,$row[$i],1,0,'C',0);
			$tmp=$tmp+$row[$i];
		}
		
		$this->Cell(($w[$i]+3),6,$tmp,1,0,'C',0);
		$tmp=0;
	$this->Ln();
    }
//    $this->Cell(array_sum($w),0,'','T');
}


//Colored table [表頭 再分 row ]  --- 主副料 table
function Table_2($x,$y,$header1,$header2,$header3,$data,$w1,$w2,$w3,$title,$R,$G,$B,$ll) {
	$parm=$_SESSION['parm'];
	$ll=$ll+2;
	$img_size = GetImageSize($parm['pic_url']);
	if ($ll > 25)
	{	    
			 $ll=0;
			 $this->Open();
			 $this->AddPage();
			 $this->hend_title($parm);
			 $this->SetXY($x,80);		
			if ($img_size[0] > $img_size[1])
			{
				$this->Image($parm['pic_url'],10,28,40,0);
			}else{
				$this->Image($parm['pic_url'],10,28,0,40);
			}

		//	 $y=$y+20;
			
	}else{
			$this->SetXY($x,$y);
	}
    //Colors, line width and bold font
    $this->SetFillColor($R,$G,$B);
    $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);
    
	$this->SetFont('Arial','B',10);
    // Header
	$this->Cell(array_sum($w1),7,$title,0,0,'C');
    $this->Ln();
//    $this->SetTextColor(255);
	$this->SetX($x);
	
    for($i=0;$i<count($header1);$i++)
        $this->Cell($w1[$i],10,$header1[$i],1,0,'C');
	$y1 = $this->getY();
	$x1 = $this->getX();
	
	// 色組名稱 -----
    $this->SetTextColor(0,110,110);
    $this->SetFont('Arial','B',9);
    for($i=0;$i<count($header2);$i++)
    {
				if (strlen($header2[$i]) > 9) $header2[$i] = substr($header2[$i],0,9)."...";
        $this->Cell($w2[$i],5,$header2[$i],1,0,'C',0);
 		}
	// 最後的 total 欄
    $this->SetTextColor(0);
    $this->SetFillColor(200);
    $this->SetFont('Arial','B',10);
    $this->Cell(0.5,10,'',1,0,'C',0);
    $this->Cell(20,10,'Total',1,0,'C',1);
	
	
	$this->Ln(1);

	// 色組數量 -----
	$this->SetXY($x1,$y1+5);
    $this->SetFillColor(200);
  
	for($i=0;$i<count($header3);$i++)
				
        $this->Cell($w2[$i],5,$header3[$i],1,0,'C',1);

	$this->SetX($x);
    $this->Ln();
    
    //雙線
	$this->SetX($x);
    for($i=0;$i<count($header1);$i++)
        $this->Cell($w1[$i],0.5,'',1,0,'C');
    for($i=0;$i<count($header2);$i++)
        $this->Cell($w2[$i],0.5,'',1,0,'C',0);
    $this->Cell(0.5,0.5,'',1,0,'C',0);
    $this->Cell(20,0.5,'',1,0,'C',1);
    $this->Ln();

    //Color and font restoration
    $this->SetTextColor(0);
    $this->SetFont('','I',9);

	$w = array_merge_recursive($w1,$w2,'0.5','20');
//print_r($w);
//exit;
	// 用料 資料 Data...........
	$this->SetFont('Big5','',8);
    foreach($data as $row)  {
		$ll++;
		
		if ($ll > 25)
		{	    
			 $ll=0;
			 $this->Open();
			 $this->AddPage();
			 $this->hend_title($parm);
			if ($img_size[0] > $img_size[1])
			{
				$this->Image($parm['pic_url'],10,28,40,0);
			}else{
				$this->Image($parm['pic_url'],10,28,0,40);
			}

		//	 $y=$y+20;
			$this->SetXY($x,80);
			    for($i=0;$i<count($header1);$i++)
        $this->Cell($w1[$i],10,$header1[$i],1,0,'C');
	$y1 = $this->getY();
	$x1 = $this->getX();
	
	// 色組名稱 -----
    $this->SetTextColor(0,110,110);
    $this->SetFont('Arial','B',9);
    for($i=0;$i<count($header2);$i++)
    {
     		if (strlen($header2[$i])> 9) $header2[$i] = substr($header2[$i],0,9)."...";
        $this->Cell($w2[$i],5,$header2[$i],1,0,'C',0);
     }
 
	// 最後的 total 欄
    $this->SetTextColor(0);
    $this->SetFillColor(200);
    $this->SetFont('Arial','B',10);
    $this->Cell(0.5,10,'',1,0,'C',0);
    $this->Cell(20,10,'Total',1,0,'C',1);
	
	
	$this->Ln(1);

	// 色組數量 -----
	$this->SetXY($x1,$y1+5);
    $this->SetFillColor(200);
	for($i=0;$i<count($header3);$i++)
        $this->Cell($w2[$i],5,$header3[$i],1,0,'C',1);

	$this->SetX($x);
    $this->Ln();
    
    //雙線
	$this->SetX($x);
    for($i=0;$i<count($header1);$i++)
        $this->Cell($w1[$i],0.5,'',1,0,'C');
    for($i=0;$i<count($header2);$i++)
        $this->Cell($w2[$i],0.5,'',1,0,'C',0);
    $this->Cell(0.5,0.5,'',1,0,'C',0);
    $this->Cell(20,0.5,'',1,0,'C',1);
    $this->Ln();

    //Color and font restoration
    $this->SetTextColor(0);
    $this->SetFont('','I',9);

//	$w = array_merge_recursive($w1,$w2,'0.5',20);

	$this->SetX($x);
		}else{
			$this->SetX($x);
		}
		

		for($i=0;$i<count($header1)+count($header2)+2;$i++){
			if (strlen($row[$i]) > 34 )
			{
				$this->SetFont('BIG5','',6);
			}else{
				$this->SetFont('BIG5','',8);
			}
			settype($row[$i], "string");  
			$this->Cell($w[$i],5,$row[$i],1,0,'C',0);
		}
	$this->Ln();

    }
//    $this->Cell(array_sum($w),0,'','T');
	return $ll;
}

*/


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
