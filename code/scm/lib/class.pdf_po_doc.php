<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_po_doc extends PDF_Chinese 	{


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
	global $open_dept;

	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(100,150,$mark,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
//	$this->SetXY(90,15);
	if($open_dept == 'HJ')
	{
		$this->Image('./images/logo-hj.jpg',60,8,90);
	}else if($open_dept == 'LY'){
		$this->Image('./images/logo-ly.jpg',60,8,90);
	}else{
		$this->Image('./images/logo11.jpg',60,8,90);
	}
	//title

	$this->SetXY(60,30);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','',8);
	if($open_dept == 'HJ')
	{
		$this->Cell(85, 7,$GLOBALS['DIST_TO']['HJ']['Addr'],0,0,'C',0);
	}else if($open_dept == 'LY'){	
		$this->Cell(85, 7,$GLOBALS['DIST_TO']['LY']['Addr'],0,0,'C',0);
	}else{
	$this->Cell(85, 7,"7th Floor, No.25, Jen-Ai road, section 4, Taipei, Taiwan. Phone: 8862-2711-3171",0,0,'C',0);
	}	
	$this->ln();
	$this->SetXY(60,38);
    $this->SetFont('Arial','BU',16);
	$this->Cell(90, 7,$print_title2,0,0,'C',0);
	$Y = $this->GetY();
	$this->SetXY(10,$Y+10);

/*	
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
*/	
}

function hend_title($parm)
{

	$this->filed_1(10,28,35,15,7,"PO #",'80,110,140',$parm['po_num']);
	$this->filed_1(48,28,80,15,7,'Supplier','80,110,140',$parm['supplier'],'L');
	//$pdf->filed_1(109,28,32,15,7,'季節','80,110,140','');
	$this->filed_1(131,28,45,15,7,'Dept.','80,110,140',$parm['dept']);
	$this->filed_1(178,28,45,15,7,'Currency','80,110,140',$parm['currency']);
	$this->filed_1(225,28,45,15,7,'Date','80,110,140',$parm['ap_date']);

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
	if ($title == 'Dept.' || $title == 'Supplier'){$this->SetFont('big5','IB',12);}else{$this->SetFont('Arial','IB',12);}
	$this->SetXY($x,$y+$h2);
	$this->Cell($w, $h-$h2,$field,'B',0,$alin);

}

function supl_show($supl)
{
	$this->SetFont('Arial','B',10);
	$this->Cell(20,5,'Messers: ',0,0,'L');
	$this->SetFont('big5','B',12);
	$this->Cell(145,5,$supl['supl_f_name'],0,0,'L');
	$this->ln();
	$this->SetFont('Arial','B',10);
	$this->Cell(20,5,'Address: ',0,0,'L');
	$this->SetFont('big5','',10);
	$this->MultiCell(145,5,$supl['cntc_addr'],0,'L',0);
	$this->ln();
	$this->SetFont('Arial','B',10);
	$this->Cell(20,5,'Tel No: ',0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(145,5,$supl['cntc_phone'],0,0,'L');
	$this->ln();
	$this->SetFont('Arial','B',10);
	$this->Cell(20,5,'Attention: ',0,0,'L');
	$this->SetFont('big5','',10);
	$this->Cell(145,5,$supl['cntc_person1'],0,0,'L');
	$this->ln();

}
function mater_title()
{
	$this->cell(19,5,'Our ref. #',1,0,'C');
	$this->cell(21,5,'Name',1,0,'C');
	$this->cell(22,5,'your article #',1,0,'C');
	$this->cell(45,5,'Contents',1,0,'C');
	$this->cell(23,5,'Color',1,0,'C');
	$this->cell(19,5,'Q\'ty',1,0,'C');
	$this->cell(24,5,'Price',1,0,'C');
	$this->cell(22,5,'AMOUNT',1,0,'C');
}
function mater($po,$currency,$fob,$fob_area)
{
	$pp_mk=0;
	
	//修改呈現的數字為"xxx,xxx,xxx.xx"
	$po['prics'] = NUMBER_FORMAT($po['prics'],2);
	$po['po_qty'] = NUMBER_FORMAT($po['po_qty'],2);
	$po['amount'] = NUMBER_FORMAT($po['amount'],2);
	
	//段行
	$i = 0;
	$contents = "content : ".$po['con1']."Construction : ".$po['con2'];
	if (strlen($po['con1']) <= 30){
			$cons[$i] = $po['con1'];
	}else{
		$cons[$i] = substr($po['con1'],0,30);
		$i++;
		$cons[$i] = substr($po['con1'],30);
	}
	$i++;
	if (strlen($po['con2']) <= 32){
		$cons[$i] = $po['con2'];
	}else{
		$cons[$i] = substr($po['con2'],0,32);
		$i++;
		$cons[$i] = substr($po['con2'],32);			
	}
	
	$contents = ''; 
	if (isset($po['width']) && $po['width']) $contents = "C.Width : ".$po['width'];
	if (isset($po['weight']) && $po['weight']) $contents = $contents."  Weight : ".$po['weight'];
	
	if ($contents <> ''){
		$i++;			
		$cons[$i] = $contents;
	}
	if (isset($po['finish']) &&$po['finish']){
		$i++;
		if (strlen($po['finish']) < 28){
			$cons[$i] = "Finish : ".$po['finish'];
		}else{
			$cons[$i] = "Finish : ".substr($po['finish'],0,28);
			$i++;
			$cons[$i] = substr($po['finish'],28);
		}			
	}
	$color1 = substr($po['color'],0,12);
	$color2 = substr($po['color'],12);
	$mile1 = substr($po['mile'],0,12);
	$mile2 = substr($po['mile'],12);
	$name1 = substr($po['mile_name'],0,12);
	$name2 = substr($po['mile_name'],12);
		
	//PRINT
	$this->SetFont('Arial','',8);
	$this->cell(19,5,$po['mat_code'],'LR',0,'C');
	$this->SetFont('BIG5','',7);
	$this->cell(21,5,$name1,'LR',0,'L');
	$this->cell(22,5,$mile1,'LR',0,'L');
	$this->cell(45,5,$cons[0],'LR',0,'L');
	$this->cell(23,5,$color1,'LR',0,'L');
	$this->SetFont('Arial','',7);
	$this->cell(19,5,$po['po_qty']." ".$po['po_unit'],'LR',0,'R');
	$this->SetFont('BIG5','',7);
	$this->cell(24,5,$fob." ".$fob_area,'LR',0,'R');
	$this->SetFont('Arial','',7);
	$this->cell(22,5,'','LR',0,'R');
	$this->ln();
	
	
	if ($i == 1){	
		$this->SetFont('BIG5','',7);
		$this->cell(19,5,'','LRB',0,'C');
		$this->cell(21,5,$name2,'LRB',0,'L');
		$this->cell(22,5,$mile2,'LRB',0,'L');
		$this->cell(45,5,$cons[1],'LRB',0,'L');
		$this->cell(23,5,$color2,'LRB',0,'L');
		$this->SetFont('Arial','',7);
		$this->cell(19,5,'','LRB',0,'R');
		if($pp_mk==0)
		{
			$this->cell(24,5,$currency."$ ".$po['prics']."/".$po['prc_unit'],'LRB',0,'R');
			$this->cell(22,5,$currency."$ ".$po['amount'],'LRB',0,'R');
			$pp_mk=1;
		}else{
			$this->cell(24,5,'','LRB',0,'R');
			$this->cell(22,5,'','LRB',0,'R');
		}
	}else{
		if ($cons[1]<>''){
			$this->SetFont('BIG5','',7);
			$this->cell(19,4,'','LR',0,'C');
			$this->cell(21,4,$name2,'LR',0,'L');
			$this->cell(22,4,$mile2,'LR',0,'L');
			$this->cell(45,4,$cons[1],'LR',0,'L');
			$this->cell(23,4,$color2,'LR',0,'L');
			$this->SetFont('BIG5','',7);
			$this->cell(19,4,'','LR',0,'R');
			if($pp_mk==0)
			{
				$this->cell(24,4,$currency."$ ".$po['prics']."/".$po['prc_unit'],'LR',0,'R');
				$this->cell(22,4,$currency."$ ".$po['amount'],'LR',0,'R');
				$pp_mk=1;
			}else{
				$this->cell(24,4,'','LR',0,'R');
				$this->cell(22,4,'','LR',0,'R');
			}	
			$this->ln();
		}
		for ($j=2; $j<(sizeof($cons)-1); $j++)
		{
			$this->SetFont('BIG5','',7);
			$this->cell(19,4,'','LR',0,'C');
			$this->cell(21,4,'','LR',0,'L');
			$this->cell(22,4,'','LR',0,'L');
			$this->cell(45,4,$cons[$j],'LR',0,'L');
			$this->SetFont('Arial','',7);
			$this->cell(23,4,'','LR',0,'L');
			$this->cell(19,4,'','LR',0,'R');
			if($pp_mk==0)
			{
				$this->cell(24,4,$currency."$ ".$po['prics']."/".$po['prc_unit'],'LR',0,'R');
				$this->cell(22,4,$currency."$ ".$po['amount'],'LR',0,'R');
				$pp_mk=1;
			}else{
				$this->cell(24,4,'','LR',0,'R');
				$this->cell(22,4,'','LR',0,'R');
			}				
		$this->ln();			
		}
		$this->SetFont('BIG5','',7);
		$this->cell(19,4,'','LRB',0,'C');
		$this->cell(21,4,'','LRB',0,'L');
		$this->cell(22,4,'','LRB',0,'L');
		$this->cell(45,4,$cons[$j],'LRB',0,'L');
		$this->SetFont('Arial','',7);
		$this->cell(23,4,'','LRB',0,'L');
		$this->cell(19,4,'','LRB',0,'R');
		if($pp_mk==0)
		{
			$this->cell(24,4,$currency."$ ".$po['prics']."/".$po['prc_unit'],'LRB',0,'R');
			$this->cell(22,4,$currency."$ ".$po['amount'],'LRB',0,'R');
			$pp_mk=1;
		}else{
			$this->cell(24,4,'','LRB',0,'R');
			$this->cell(22,4,'','LRB',0,'R');
		}			
		
	}
	$this->ln();
	$this->SetFont('BIG5','',7);
	
	$this->MultiCell(195,5,"Please mark in each row：".str_replace("|","、",$po['orders']),1,'L');
	$y=$this->GetY();
	$this->SetLineWidth(0.3);
	$this->Line(10.1,$this->GetY(),205,$this->GetY());
	$this->SetLineWidth(0.1);
	$this->SetY($y);
	return ($i+1);
}


function oth_cost($po,$currency)
{
	//修改呈現的數字為"xxx,xxx,xxx.xx"
		$po['cost'] = NUMBER_FORMAT($po['cost'],2);

	$this->SetFont('big5','',9);
	$this->Cell(130,5,$po['item'],1);
	$this->SetFont('Arial','',7);
	$this->Cell(19,5,'',1,0,'L');
	$this->Cell(46,5,$currency."$".$po['cost'],1,0,'R');
	$this->ln();
}


function mk_show($log,$item,$ord='',$x=0)
{
	$this->SetFont('Arial','',10);
	$this->cell(20, 5,$item."    : ",0,0,'L');
	$j =$x;
	if($ord <> '')
	{
		$cut = strlen($ord);
		$cut =$cut +($cut / 2) +4;
		$this->SetFont('Arial','B',8);
		$this->cell(50, 5,"Please Marked our Production P.O.#",0,0,'L');
		$this->SetFont('Arial','BU',8);
		if(strlen($ord) > 64)
		{
			$this->cell($cut, 5,substr($ord,0,64),0,0,'L');
			$this->ln();
			$this->SetFont('Arial','B',8);
			$this->cell(20, 5,"     ",0,0,'L');
			$this->SetFont('Arial','BU',8);
			$this->cell(95, 5,substr($ord,64),0,0,'L');
			$j++;
		}else{
			$this->cell($cut, 5,$ord,0,0,'L');
		}
		$this->SetFont('Arial','B',8);
		$this->ln();
		$this->cell(20, 5,"     ",0,0,'L');
		$this->cell(75, 5,"on Out-side Packing and Shiping documents before shipping",0,0,'L');
		$this->ln();			
		$j++;
		$y=$this->GetY();		
		if ($y > 240)
		{
			$this->AddPage();
			$j=0;		
		}
	}
	$this->SetFont('Big5','',8);
	for ($i=0; $i < sizeof($log); $i++)
	{
		if ($log[$i]['item'] == $item)
		{
			
			$tmp_des = explode("<br>", $log[$i]['des'] );
			for($g=0; $g < sizeof($tmp_des); $g++)
			{
				$l1=0;
				$l2=122;
				$cut = 0;
				$str_long = mb_strlen($tmp_des[$g],'big-5');
				$_bcmod = bcmod($str_long,$l2);
				$cut = $str_long / $l2;
				
				for ($z=0; $z < $cut; $z++)
				{				
					$tmp_ti = mb_substr($tmp_des[$g],$l1,122,'big-5'); 					  
					$this->SetFont('Arial','',10);
					if ($j == 0) $this->cell(20, 5,$item."    : ",0,0,'L');
					$this->SetFont('Big5','',8);
					if ($j == $x || $j == 0)
					{
						$this->MultiCell(165, 5,$tmp_ti,0,'L');
						$this->ln();			
					}else{
						$this->cell(20, 5,"  ",0,0,'L');
						$this->MultiCell(165, 5,$tmp_ti,0,'L');
						$this->ln();								
					}						
					$l1=$l2;
					$l2=$l2+122;	
					$j++;
					$y=$this->GetY();					
					if ($y > 240)
					{
						$this->AddPage();
						$j=0;		
					}
				}
				
			}
			

		
			
		}
	}
	if($i==0)$this->ln();
		return $j;
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
