<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_rcv_rpt extends PDF_Chinese 	{


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
	global $mark;
	global $ary_title;
	
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
	$this->Cell(100, 7,$print_title,1,0,'C',0);

	$this->SetFont('Arial','I',12);
	$this->Cell(75, 7,$print_title2,0,0,'R',0);
	
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
	$this->hend_title($ary_title);
	$Y = $this->getY();
	$this->setXY(10,$Y+15);
	
}

function hend_title($parm)
{
	$order_num = $order_num2 = '';
	if(Sizeof($parm['ord_num']) <= 4)
	{
		for($i=0; $i<Sizeof($parm['ord_num']); $i++)$order_num .= $parm['ord_num'][$i].',';
		$order_num = substr($order_num,0,-1);
	}else{
		for($i=0; $i<4; $i++)$order_num .= $parm['ord_num'][$i].',';
		$order_num = substr($order_num,0,-1);
		for($i=4; $i<Sizeof($parm['ord_num']); $i++)$order_num2 .= $parm['ord_num'][$i].',';
		$order_num2 = substr($order_num2,0,-1);

	}

	$this->filed_1(10,28,42,15,7,"CUST. ",'80,110,140',$parm['cust_name']);	
//	$this->filed_1(62,28,38,15,7,'INSP DATE','80,110,140',$parm['op_date']);
	$this->filed_1(54,28,70,15,7,'ORTICLE','80,110,140',$parm['name'],'L');
	$this->filed_1(126,28,50,15,7,'COLOR','80,110,140',$parm['color'],'L');
	$this->filed_1(178,28,27,15,7,'QUANTITY','80,110,140',$parm['qty']);  
  $this->filed_1(207,28,83,15,7,'ORDER NO','80,110,140',$order_num);

	if($order_num2)$this->filed_1(10,45,280,15,7,'ORDER NO','80,110,140',$order_num2);

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


// field module 1 [表匡 1 ]
function filed_1($x,$y,$w,$h,$h2,$title,$color,$field,$alin='C') {
	$this->SetFont('Big5','',12);
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
	if ($title == 'Dept.' || $title == 'Supplier Name'){$this->SetFont('big5','IB',10);}else{$this->SetFont('Arial','IB',12);}
//	$this->SetFont('Big5','IB',12);
	$this->SetXY($x,$y+$h2);
	$this->Cell($w, $h-$h2,$field,'B',0,$alin);

}

function rpt_title($rcv)
{
	
	$this->SetFont('Arial','',10);
	$this->Cell(20,7,' ROLL NO : ','TBL',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(50,7,$rcv['roll'],'TB',0,'L');

	$this->SetFont('Arial','',10);
	$this->Cell(22,7,' DYED LOT : ','TBL',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(188,7,$rcv['dyed_lot'],'TBR',1,'L');

	$this->SetFont('Arial','',10);
	$this->Cell(24,7,' Org. WIDTH : ','TBL',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(46,7,$rcv['org_w'],'TB',0,'L');

	$this->SetFont('Arial','',10);
	$this->Cell(16,7,' WIDTH : ','TBL',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(54,7,$rcv['width'],'TB',0,'L');

	$this->SetFont('Arial','',10);
	$this->Cell(30,7,' Org. YARDAGE : ','TBL',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(40,7,$rcv['ord_y'],'TB',0,'L');

	$this->SetFont('Arial','',10);
	$this->Cell(22,7,' YARDAGE : ','TBL',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(48,7,$rcv['yd'],'TBR',1,'L');
	
	$this->SetFont('Arial','B',8);
	$this->Cell(280,6,'DEFECTS',1,1,'C');

	$ww= 280/16;
	$this->SetFont('Arial','',8);
	$this->Cell(8,4,'','TLR',0,'C');
	$this->Cell($ww,4,'broken','TLR',0,'C');// broken end
	$this->Cell($ww,4,'broken','TLR',0,'C');// broken pick
	$this->Cell($ww,4,'coaseend','TLR',0,'C');// coaseend end
	$this->Cell($ww,4,'coaseend','TLR',0,'C');// coaseend pick
	$this->Cell($ww,4,'thin/thick','TLR',0,'C');// thin/thick place
	$this->Cell(($ww-2),4,'hole','TLR',0,'C');// hole
	$this->Cell($ww,4,'neps','TLR',0,'C');// neps/konts
	$this->Cell($ww,4,'weft','TLR',0,'C');// weft bar
	$this->Cell($ww,4,'miss','TLR',0,'C');// miss pick
	$this->Cell($ww,4,'unprinted','TLR',0,'C');// unprinted dot
	$this->Cell(($ww-2),4,'slub','TLR',0,'C');// slub
	$this->Cell($ww,4,'foreign','TLR',0,'C');// foreign fiber/fly
	$this->Cell(($ww-2),4,'stain','TLR',0,'C');// stain
	$this->Cell($ww,4,'crease','TLR',0,'C');// crease
	$this->Cell(($ww-2),4,'float','TLR',0,'C');// float
	$this->Cell($ww,4,'total','TLR',1,'C');// total point
	
	$this->Cell(8,4,'','BLR',0,'C');
	$this->Cell($ww,4,'end','BLR',0,'C');// broken end
	$this->Cell($ww,4,'pick','BLR',0,'C');// broken pick
	$this->Cell($ww,4,'end','BLR',0,'C');// broken end
	$this->Cell($ww,4,'pick','BLR',0,'C');// broken pick
	$this->Cell($ww,4,'place','BLR',0,'C');// thin/thick place
	$this->Cell(($ww-2),4,'','BLR',0,'C');// hole
	$this->Cell($ww,4,'/konts','BLR',0,'C');// neps/konts
	$this->Cell($ww,4,'bar','BLR',0,'C');// weft bar
	$this->Cell($ww,4,'pick','BLR',0,'C');// miss pick
	$this->Cell($ww,4,'dot','BLR',0,'C');// unprinted dot
	$this->Cell(($ww-2),4,'','BLR',0,'C');// slub
	$this->Cell($ww,4,'fiber/fly','BLR',0,'C');// foreign fiber/fly
	$this->Cell(($ww-2),4,'','BLR',0,'C');// stain
	$this->Cell($ww,4,'','BLR',0,'C');// crease
	$this->Cell(($ww-2),4,'stain','BLR',0,'C');// float
	$this->Cell($ww,4,'point','BLR',1,'C');// total point




	

}

function rpt_det($rpt)
{
	$ww= 280/16;
	$this->SetFont('Arial','B',8);	
	$this->Cell(8,5,'1',1,0,'C');
	for($i=0; $i < 15; $i++)
	{
		$ww2 = $ww;
		if($i == 5 || $i == 10 || $i == 12 || $i == 14)$ww2 = $ww-2;
		$this->Cell($ww2,5,$rpt['defect_1'][$i],1,0,'C');
	}
	$this->Cell($ww,5,$rpt['ttl_point_1'],1,1,'C');

	$this->Cell(8,5,'2',1,0,'C');
	for($i=0; $i< 15; $i++)
	{
		$ww2 = $ww;
		if($i == 5 || $i == 10 || $i == 12 || $i == 14)$ww2 = $ww-2;
		
		$this->Cell($ww2,5,$rpt['defect_2'][$i],1,0,'C');
	}
	$this->Cell($ww,5,$rpt['ttl_point_2'],1,1,'C');
	
	$this->Cell(8,5,'3',1,0,'C');
	for($i=0; $i< 15; $i++)
	{
		$ww2 = $ww;
		if($i == 5 || $i == 10 || $i == 12 || $i == 14)$ww2 = $ww-2;

		$this->Cell($ww2,5,$rpt['defect_3'][$i],1,0,'C');
	}
	$this->Cell($ww,5,$rpt['ttl_point_3'],1,1,'C');

	$this->Cell(8,5,'4',1,0,'C');
	for($i=0; $i< 15; $i++)
	{
		$ww2 = $ww;
		if($i == 5 || $i == 10 || $i == 12 || $i == 14)$ww2 = $ww-2;

		$this->Cell($ww2,5,$rpt['defect_4'][$i],1,0,'C');
	}
	$this->Cell($ww,5,$rpt['ttl_point_4'],1,1,'C');
	
	$this->SetFont('Arial','B',9);	
		$this->Cell(78,7,'Different Yardage : '.NUMBER_FORMAT($rpt['yd_diff'],2),1,0,'C');
	$this->Cell(202,7,'POINTS/100.SQ YD : '.$rpt['point_h_yd'],1,1,'C');
	
}


function rpt_rmk($rmk,$l_mk)
{
	//98	
	$tmp = explode('<br>',$rmk);
	$j=0;
	
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$str_long = mb_strlen($tmp[$i],'big-5');
		$_bcmod = bcmod($str_long,98);
		$l1 = 0;
		while($str_long > 98)
		{
			$abc = mb_substr($tmp[$i],$l1,98,'big-5'); 
			
			$det[] = $abc;
			$l1 += 98;
			$str_long -= 98;
		}
		$abc = mb_substr($tmp[$i],$l1,98,'big-5'); 
		$det[] = $abc;
		$j++;
	}
	$this->SetFont('BIG5','',8);
	for($i=0; $i<sizeof($det); $i++)
	{
		$l_mk++;
		if($l_mk > 26)
		{
			$this->Cell(280,0.5,'','BLR',1,'C');
			$this->AddPage();	
			$this->Cell(280,0.5,'','TLR',1,'C');
			$l_mk = 0;
		}
		$this->Cell(280,5,$det[$i],'LR',1,'L');
	}
	$this->Cell(280,0.5,'','BLR',1,'C');
	$y = $this->GetY();
	$this->SetY($y+3);
	
	return $l_mk;
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
