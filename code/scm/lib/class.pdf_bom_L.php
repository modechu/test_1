<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_bom_L extends PDF_Chinese 	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;


//Page header
function Header() {

	global $print_title;
	global $PHP_id;
	global $print_title2;
	global $ary_title;
	
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(100,150,$PHP_id,30);
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
	$this->hend_title($ary_title);
	$Y = $this->getY();
	$this->setXY(10,$Y+20);
	
}

function hend_title($parm)
{

	$this->filed_1(10,28,35,15,7,$parm['prt_ord'],'80,110,140',$parm['PHP_id']);
	$this->filed_1(48,28,40,15,7,'Customer','80,110,140',$parm['cust']);
	//$pdf->filed_1(109,28,32,15,7,'季節','80,110,140','');
	$this->filed_1(91,28,45,15,7,'Customer ref.','80,110,140',$parm['ref']);


	$this->filed_1(139,28,25,15,7,'Dept.','80,110,140',$parm['dept']);
	$this->filed_1(167,28,40,15,7,'Size / Style','80,110,140',$parm['size_scale'].' /  '.$parm['style']);
	$this->filed_1(210,28,30,15,7,'Q\'ty','80,110,140',$parm['qty'].' '.$parm['unit']);
	$this->filed_1(243,28,23,15,7,'ETD','80,110,140',$parm['etd']);

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
   
    foreach($data as $row)  {
		    $this->SetX($x);
		    $tmp=0;
		for($i=0;$i<count($header);$i++){
			if ($i == 0)
			{
				if (strlen($row[$i]) > 18)
				{
					$this->SetFont('big5','',6);
				}else if(strlen($row[$i]) > 8){
					$this->SetFont('big5','',8);
				}else{
					$this->SetFont('big5','',10);
				}
			}else{
				$this->SetFont('Arial','',10);
			}
			
			$this->Cell($w[$i],6,$row[$i],1,0,'C',0);
			if ($i > 0)$tmp=$tmp+$row[$i];
		}		
		$this->Cell(($w[$i]+3),6,$tmp,1,0,'R',0);
		$tmp=0;
	$this->Ln();
    }
//    $this->Cell(array_sum($w),0,'','T');
}



function Table_2_title($title,$header1,$w1,$use_title,$R,$G,$B)
{
	    //Colors, line width and bold font
    $this->SetFillColor($R,$G,$B);
    $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);
    
	$this->SetFont('Arial','B',10);
    // Header
	$this->Cell(267,7,$title,0,0,'C');
  $this->Ln();
	$this->SetX(10);	
  for($i=0;$i<count($header1);$i++)
  {
     if($header1[$i] == 'size')
     {
     	$this->SetFont('Arial','',7);
     }else{
   		$this->SetFont('Arial','B',10);
    	}
      $this->Cell($w1[$i],10,$header1[$i],1,0,'C');
	}
	$y1 = $this->getY();
	$x1 = $this->getX();
	
	$this->SetTextColor(0,110,110);
  $this->SetFont('Arial','',7);
  $j=0;
  foreach($use_title as $key => $value)for($i=0;$i<count($use_title[$key]['name']);$i++)$j++;
	$color_size = 145/$j;
  foreach($use_title as $key => $value)
  {
    for($i=0;$i<count($use_title[$key]['name']);$i++)
    {
				if (strlen($use_title[$key]['name'][$i]) > 8) $use_title[$key]['name'][$i] = substr($use_title[$key]['name'][$i],0,8)."...";
        $this->Cell($color_size,5,$use_title[$key]['name'][$i],1,0,'C',0);
 		}
 	}

	// 最後的 total 欄
  $this->SetTextColor(0);
  $this->SetFillColor(200);
  $this->SetFont('Arial','B',10);
  $this->Cell(0.5,10,'',1,0,'C',0);
  $this->Cell(15,10,'Total',1,0,'C',1);
	
	
	$this->Ln(1);



	// 色組名稱 -----
  $this->SetXY($x1,$y1+5);
  $this->SetTextColor(0,110,110);
  $this->SetFont('Arial','',7);
  foreach($use_title as $key => $value)
  {
    for($i=0;$i<count($use_title[$key]['qty']);$i++)    
        $this->Cell($color_size,5,$use_title[$key]['qty'][$i].' pc',1,0,'C',0); 		
 	}
  $this->Ln();
    
    //雙線
//	$this->SetX($x);
  $this->Cell(283,0.5,'',1,0,'C');

  //Color and font restoration
  $this->SetTextColor(0);
  $this->SetFont('','I',9);

}



//Colored table [表頭 再分 row ]  --- 主副料 table
function Table_2($row,$w1) {
	$this->setX(10);
	$this->SetFont('BIG5','',8);
	if(!isset($row['size']))$row['size']='';
	if(isset($row['acc_code']))$this->Cell($w1[0],5,$row['acc_code'],1,0,'L',0);
	if(isset($row['lots_code']))$this->Cell($w1[0],5,$row['lots_code'],1,0,'L',0);
	$this->Cell($w1[1],5,$row['use_for'],1,0,'L',0);
	$this->Cell($w1[2],5,$row['est_1'].$row['unit'],1,0,'R',0);	
	$this->Cell($w1[3],5,$row['color'],1,0,'R',0);	
	$this->Cell($w1[4],5,$row['size'],1,0,'L',0);
	$this->Cell($w1[5],5,'',1,0,'L',0);
	$this->SetFont('Arial','B',8);
	$ttl_qty = 0;
	$qty = explode(',',$row['qty']);
	$size_qty = 145/sizeof($qty);
	for($i=0; $i<sizeof($qty); $i++)
	{
		$this->Cell($size_qty,5,$qty[$i],1,0,'C',0);
		$ttl_qty += $qty[$i];
	}
	$this->Cell(15,5,$ttl_qty,1,0,'R',0);
	$this->Ln();
	$this->SetFont('BIG5','',8);
	$this->Cell(267.5,5,$row['des_m'],1,0,'L',0);
  $this->SetFillColor(200);
	$this->Cell(15,5,'',1,0,'C',1);
	$this->Ln();
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
