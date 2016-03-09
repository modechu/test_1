<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_prduction extends PDF_Chinese 	{


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
    $this->RotatedText(100,150,$mark,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title

	$this->SetXY(110,15);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','B',14);
	$this->Cell(85, 7,$print_title,1,0,'C',0);
    $this->SetFont('Arial','I',12);
	$this->Cell(90, 7,$print_title2,0,0,'R',0);
	
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
	$this->hend_title();
	$Y = $this->getY();
	$this->setXY(10,$Y);
	
}

function hend_title()
{
	$this->SetFont('BIG5','',10);
	$this->Cell(24, 5,'LINE','TLR',0,'C');
	$this->Cell(24, 5,'ORDER#','TLR',0,'C');
	$this->Cell(10, 5,'Style','TLR',0,'C');
	$this->Cell(47, 5,'Style#','TLR',0,'C');
	$this->Cell(22, 5,'人數','TLR',0,'C');
	$this->Cell(48, 5,'加班','TLR',0,'C');
	$this->Cell(13, 5,'IE','TLR',0,'C');
	$this->Cell(13, 5,'應產出','TLR',0,'C');
	$this->Cell(52, 5,'實際產出','TLR',0,'C');
	$this->Cell(13, 5,'日產出','TLR',0,'C');
	$this->Cell(13, 5,'效率(%)','TLR',1,'C');
	
	$this->Cell(24, 5,'','BLR',0,'C'); //LINE
	$this->Cell(24, 5,'','BLR',0,'C'); //ORDER#
	$this->Cell(10, 5,'','BLR',0,'C');	//Style
	$this->Cell(47, 5,'','BLR',0,'C');	//Style#
	$this->Cell(11, 5,'應有',1,0,'C'); //應有人數
	$this->Cell(11, 5,'出勤',1,0,'C'); //出勤人數
	$this->Cell(11, 5,'人數',1,0,'C'); //加班人數
	$this->Cell(11, 5,'小時',1,0,'C'); //加班小時
	$this->Cell(13, 5,'工作日',1,0,'C'); //加班工作日
	$this->Cell(13, 5,'Rate(%)',1,0,'C'); //加班率
	$this->Cell(13, 5,'','BLR',0,'C'); //IE
	$this->Cell(13, 5,'(PC)','BLR',0,'C');//應產出(PC)
	$this->Cell(13, 5,'Reg.',1,0,'C'); //實際產出(reg.)
	$this->Cell(13, 5,'OT.',1,0,'C'); //實際產出(overtime)
	$this->Cell(13, 5,'TTL.',1,0,'C'); //實際產出(total)
	$this->Cell(13, 5,'SU',1,0,'C'); //實際產出(total)
	$this->Cell(13, 5,'(su/人)','BLR',0,'C'); //工作日產出
	$this->Cell(13, 5,'','BLR',1,'C');//效率(%)

}

function pdt_value($parm)
{	
	$this->SetFont('Arial','',8);
	$this->Cell(24, 6,$parm['line'],1,0,'C'); //LINE
	$this->Cell(24, 6,$parm['ord_num'],1,0,'C'); //ORDER#
	$this->Cell(10, 6,$parm['ord_style'],1,0,'C');	//Style
	$this->Cell(47, 6,$parm['style'],1,0,'L');	//Style#
	$this->Cell(11, 6,$parm['worker'],1,0,'R'); //應有人數
	$this->Cell(11, 6,$parm['workers'],1,0,'R'); //出勤人數
	$this->Cell(11, 6,$parm['ot_wk'],1,0,'R'); //加班人數
	$this->Cell(11, 6,$parm['ot_hr'],1,0,'R'); //加班小時
	$this->Cell(13, 6,$parm['ot_day'],1,0,'R'); //加班工作日
	$this->Cell(13, 6,NUMBER_FORMAT($parm['ot_rate'],1).'%',1,0,'R'); //加班率
	$this->Cell(13, 6,$parm['ie'],1,0,'R'); //IE
	$this->Cell(13, 6,$parm['spt'],1,0,'R');//應產出(PC)
	$this->Cell(13, 6,$parm['work_qty'],1,0,'R'); //實際產出(reg.)
	$this->Cell(13, 6,$parm['over_qty'],1,0,'R'); //實際產出(overtime)
	$this->Cell(13, 6,$parm['qty'],1,0,'R'); //實際產出(total)
	$this->Cell(13, 6,$parm['su'],1,0,'R'); //實際產出(total)
	$this->Cell(13, 6,NUMBER_FORMAT($parm['d_out'],1),1,0,'R'); //工作日產出
	$this->Cell(13, 6,NUMBER_FORMAT($parm['out_rate'],0).'%',1,1,'R');//效率(%)
}

function pdt_ttl($parm)
{	
	$this->SetFont('Arial','',8);
	$this->Cell(105, 6,'TOTAL',1,0,'C'); //LINE
	$this->Cell(11, 6,$parm['reg_wk'],1,0,'R'); //應有人數
	$this->Cell(11, 6,$parm['t_wk'],1,0,'R'); //出勤人數
	$this->Cell(11, 6,$parm['ot_wk'],1,0,'R'); //加班人數
	$this->Cell(11, 6,$parm['ot_hr'],1,0,'R'); //加班小時
	$this->Cell(13, 6,$parm['ot_day'],1,0,'R'); //加班工作日
	$this->Cell(13, 6,NUMBER_FORMAT($parm['ot_rt'],1).'%',1,0,'R'); //加班率
	$this->Cell(13, 6,'',1,0,'R'); //IE
	$this->Cell(13, 6,$parm['spt'],1,0,'R');//應產出(PC)
	$this->Cell(13, 6,$parm['wk_qty'],1,0,'R'); //實際產出(reg.)
	$this->Cell(13, 6,$parm['ot_qty'],1,0,'R'); //實際產出(overtime)
	$this->Cell(13, 6,$parm['qty'],1,0,'R'); //實際產出(total)
	$this->Cell(13, 6,$parm['su'],1,0,'R'); //實際產出(total)
	$this->Cell(13, 6,NUMBER_FORMAT($parm['d_out'],1),1,0,'R'); //工作日產出
	$this->Cell(13, 6,NUMBER_FORMAT($parm['rate'],0).'%',1,1,'R');//效率(%)
}

function pdt_sub_ttl($parm)
{	
	$this->SetFont('Arial','',8);
	$this->Cell(105, 6,'SUB TOTAL',1,0,'C'); //LINE
	$this->Cell(11, 6,'',1,0,'R'); //應有人數
	$this->Cell(11, 6,'',1,0,'R'); //出勤人數
	$this->Cell(11, 6,'',1,0,'R'); //加班人數
	$this->Cell(11, 6,'',1,0,'R'); //加班小時
	$this->Cell(13, 6,'',1,0,'R'); //加班工作日
	$this->Cell(13, 6,'',1,0,'R'); //加班率
	$this->Cell(13, 6,'',1,0,'R'); //IE
	$this->Cell(13, 6,'',1,0,'R');//應產出(PC)
	$this->Cell(13, 6,$parm['r_qty'],1,0,'R'); //實際產出(reg.)
	$this->Cell(13, 6,$parm['o_qty'],1,0,'R'); //實際產出(overtime)
	$this->Cell(13, 6,$parm['qty'],1,0,'R'); //實際產出(total)
	$this->Cell(13, 6,$parm['su'],1,0,'R'); //實際產出(total)
	$this->Cell(13, 6,'',1,0,'R'); //工作日產出
	$this->Cell(13, 6,'',1,1,'R');//效率(%)
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
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                           ',0,0,'C');
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
