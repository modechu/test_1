<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_po extends PDF_Chinese 	{


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
  	if($print_title == '特殊採購　異常報告') 
		{
			$this->RotatedText(60,210,$mark,30);
		}else{
    	$this->RotatedText(100,150,$mark,30);
    }
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	if($open_dept == 'HJ')
	{
		$this->Image('./images/logo10-hj.jpg',10,8,90);
	}else if($open_dept == 'LY'){
		$this->Image('./images/logo10-ly.jpg',10,8,90);
	}else{
		$this->Image('./images/logo10.jpg',10,8,90);
	}	

	$this->SetTextColor(0,0,40);
	$this->SetFont('Arial','B',12);
	$this->SetXY(240,5);
	$this->Cell(50, 10,date('Y-m-d h:m:s'),0,0,'R');
	
	
	//title
  if ($print_title == 'SPECIAL Purchase Application')
  {
		$this->SetXY(110,15);
		$this->SetTextColor(255,255,255);
		$this->SetFont('Arial','B',16);
		$this->Cell(90, 10,$print_title,1,0,'C',1);
		$this->SetFont('BIG5','B',16);
		$this->Cell(20, 10,'(異常)',1,0,'L',1);

    $this->SetFont('Arial','I',12);
		$this->Cell(70, 10,$print_title2,0,0,'R',1);
		$this->SetTextColor(40,40,40);
	}else if($print_title == '特殊採購　異常報告'){
		$this->SetXY(100,15);
		$this->SetTextColor(40,40,40);
 	 	$this->SetFont('BIG5','I',9);
		$this->Cell(40, 7,$print_title2,0,0,'C',0);
		$this->SetFont('BIG5','B',14);
		$this->Cell(55, 7,$print_title,1,1,'C',0);	
	}else{
		$this->SetXY(110,15);
		$this->SetTextColor(40,40,40);
		$this->SetFont('Arial','B',14);
		$this->Cell(85, 7,$print_title,1,0,'C',0);
    $this->SetFont('Arial','I',12);
		$this->Cell(90, 7,$print_title2,0,0,'R',0);

	}
	
	//date
 //   $this->SetFont('Arial','I',9);
	$this->SetXY(180,18);
//	$this->Cell(20, 7,$dt['date'],0,1,'C',0);
	if($print_title == '特殊採購　異常報告') 
	{
		//line
		$this->SetLineWidth(0.5);
		$this->SetDrawColor(100,100,100);
		$this->Line(10,25,200,25);
		$this->ln(1);
		$this->SetLineWidth(0.1);
		$this->SetDrawColor(0,0,0);
		$this->Line(10,26,200,26);
		$this->ln(5);
		$this->SetXY(10,22);
	}else{
	//line
		$this->SetLineWidth(0.5);
		$this->SetDrawColor(100,100,100);
		$this->Line(10,25,290,25);
		$this->ln(1);
		$this->SetLineWidth(0.1);
		$this->SetDrawColor(0,0,0);	
		$this->Line(10,26,290,26);
		$this->ln(5);
	}
}


function title_simpl($field) {	//title
	$y = $this->GetY();
	$this->SetY(($y+5));
	$this->SetFont('BIG5','B',12);
	$this->Cell(30, 5,'異常報告 # : ',0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(58, 5,$field['ex_num'],0,0,'L');
	$this->ln();
	
	$this->SetFont('BIG5','B',12);
	$this->Cell(12, 5,'客戶 : ',0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(58, 5,$field['cust'],0,0,'L');
	$this->SetFont('BIG5','B',12);
	$this->Cell(30, 5,'採購單編號 : ',0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(60, 5,$field['po_num'],0,0,'L');
	$this->ln();
	$this->SetFont('BIG5','B',12);
	$this->Cell(20, 5,'訂單編號 : ',0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(60, 5,$field['ord_num'],0,0,'L');
	$this->ln();	
	$this->ln();	

}


function exc_value($value) {
	$x=0;
	
  $tmp=explode('<br>',$value);
    for ($i=0; $i<sizeof($tmp); $i++)
    {
			$l1=0;
			$l2=58;
			$cut = 0;
				$str_long = mb_strlen($tmp[$i],'big-5');
				$_bcmod = bcmod($str_long,$l2);
				$cut = $str_long / $l2;

				for ($z=0; $z < $cut; $z++)
				{									
					$tmp_state = mb_substr($tmp[$i],$l1,58,'big-5'); 					
				  $this->SetFont('Big5','',9);
					$this->Cell(190,5,$tmp_state,'RL',0,'L');
					$this->ln();
					$x++;
					$l1=$l2;
					$l2=$l2+58;					
				}
				
	}
	$this->Cell(190,3,'','RLB',0,'L');
	$this->ln();
	$x = $x + 2;
	return $x;
}

function fld_title($title) {
		$this->SetFont('Big5','',12);
		$this->cell(190,7,$title,1,0,'C');
		$this->ln();
}

function hend_title($parm)
{

	if (isset($parm['po_num']))	$this->filed_1(10,28,25,15,7,"PO #",'80,110,140',$parm['po_num']);
	if (isset($parm['pa_num']))	$this->filed_1(10,28,25,15,7,"PA #",'80,110,140',$parm['pa_num']);
	$this->filed_1(37,28,70,15,7,'Supplier','80,110,140',$parm['supplier'],'L');
	//$pdf->filed_1(109,28,32,15,7,'季節','80,110,140','');
	$this->filed_1(109,28,30,15,7,'Dept.','80,110,140',$parm['dept']);
	$this->filed_1(141,28,30,15,7,'Currency','80,110,140',$parm['currency']);
	$this->filed_1(173,28,25,15,7,'Date','80,110,140',$parm['ap_date']);
	$this->filed_1(200,28,90,15,7,'Payment','80,110,140',$parm['dm_way']);

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
	if ($title == 'Dept.' || $title == 'Supplier' || $title == 'Payment'){$this->SetFont('big5','IB',12);}else{$this->SetFont('Arial','IB',12);}
	$this->SetXY($x,$y+$h2);
	$this->Cell($w, $h-$h2,$field,'B',0,$alin);

}

function po_title()
{
	$this->SetFont('Arial','B',10);
	$this->Cell(20,10,'Order #',1,0,'C');
	$this->Cell(20,10,'Material #',1,0,'C');
	$this->Cell(37,10,'Name',1,0,'C');
	$this->Cell(88,10,'Constrution',1,0,'C');
	$this->Cell(30,10,'Color/cat.',1,0,'C');
	$this->Cell(21,10,'U/Price',1,0,'C');
	$this->Cell(23,10,'Q\'ty',1,0,'C');	
	$this->Cell(22,10,'Amount',1,0,'C');
	$this->Cell(19,10,'ETD',1,0,'C');
	//雙線
	$this->ln();
	$this->Cell(20,0.5,'',1,0,'C');
	$this->Cell(20,0.5,'',1,0,'C');
	$this->Cell(37,0.5,'',1,0,'C');
	$this->Cell(88,0.5,'',1,0,'C');
	$this->Cell(30,0.5,'',1,0,'C');
	$this->Cell(21,0.5,'',1,0,'C');
	$this->Cell(23,0.5,'',1,0,'C');	
	$this->Cell(22,0.5,'',1,0,'C');
	$this->Cell(19,0.5,'',1,0,'C');
}

function po_det($po)
{
	$this->SetFont('Arial','',9);	
	$this->Cell(20,5,$po['ord_num'],1);
	$this->SetFont('big5','',8);
	$this->Cell(20,5,$po['mat_code'],1);	
	$this->Cell(37,5,$po['mat_name'],1);	
	$this->Cell(88,5,$po['con1']."  ".$po['con2'],1);
	$this->Cell(30,5,$po['color'],1);
	$this->SetFont('Arial','',9);
	$this->Cell(21,5,$po['prics']."/".$po['prc_unit'],1,0,'R');
	$this->Cell(23,5,$po['po_qty']." ".$po['po_unit'],1,0,'R');	
	$this->Cell(22,5,$po['amount'],1,0,'R');
	$this->Cell(19,5,$po['eta'],1,0,'L');

}

function po_det_mix($po,$ords)
{
	$j=0;
	if(mb_strlen($po['con1'],'big-5') > 50)
	{
		$con[$i] =substr($po['con1'],0,50);
		$j++;
		$con[$i] =substr($po['con1'],50);
		$j++;
	}else{
		$con[$i] =$po['con1'];
		$j++;
	}
	if(mb_strlen($po['con2'],'big-5') > 50)
	{
		$con[$i] =substr($po['con2'],0,50);
		$j++;
		$con[$i] =substr($po['con2'],50);
		$j++;
	}else{
		$con[$i] =$po['con2'];
		$j++;
	}	

	$this->SetFont('Arial','',8);
	$this->Cell(20,5,$ords[0],1);
	$this->SetFont('big5','',8);
	$this->Cell(20,5,$po['mat_code'],'RL');
	$this->Cell(37,5,$po['mat_name'],'RL');
	$this->Cell(88,5,$con[0],'RL');
	$this->Cell(30,5,$po['color'],'RL');
	$this->SetFont('Arial','',9);
	$this->Cell(21,5,$po['prics']."/".$po['prc_unit'],'RL',0,'R');
	$this->Cell(23,5,$po['po_qty']." ".$po['po_unit'],'RL',0,'R');	
	$this->Cell(22,5,$po['amount'],'RL',0,'R');
	$this->Cell(19,5,$po['eta'],'RL',0,'L');
	$this->ln();
	if(sizeof($ords) > sizeof($con))$j = sizeof($ords);
	for ($i=1; $i< ($j-1); $i++)
	{
			$this->SetFont('Arial','',9);
			if(isset($ords[$i]))
			{
				$this->Cell(20,5,$ords[$i],1);
			}else{
				$this->Cell(20,5,'',1);
			}
			$this->Cell(20,5,'','RL');
			$this->Cell(37,5,'','RL');
			if(isset($con[$i]))		
			{	
				$this->SetFont('big5','',8);
				$this->Cell(88,5,$con[$i],'RL');
				$this->SetFont('Arial','',9);
			}else{
				$this->Cell(88,5,'','RL');
			}
			$this->Cell(30,5,'','RL');
			$this->Cell(21,5,'','RL');
			$this->Cell(23,5,'','RL');	
			$this->Cell(22,5,'','RL');		
			$this->Cell(19,5,'','RL');
			$this->ln();
	}
	$this->SetFont('Arial','',9);
	$this->Cell(20,5,$ords[$i],1);
	$this->Cell(20,5,'','RLB');
	$this->Cell(37,5,'','RLB');			
	$this->Cell(88,5,'','RLB');
	$this->Cell(30,5,'','RLB');
	$this->Cell(21,5,'','RLB');
	$this->Cell(23,5,'','RLB');
	$this->Cell(22,5,'','RLB');	
	$this->Cell(19,5,'','RLB');
}





function pa_det($po)
{
	/*
	$this->SetFont('Arial','',9);
	$this->Cell(20,5,$po['ord_num'],1);
	$this->SetFont('big5','',8);
	$this->Cell(20,5,$po['mat_code'],1);
	$this->Cell(37,5,$po['mat_name'],1);
	$this->Cell(88,5,$po['con1']."  ".$po['con2'],1);
	$this->Cell(30,5,$po['color'],1);
	$this->SetFont('Arial','',9);
	$this->Cell(21,5,$po['prics']."/".$po['prc_unit'],1,0,'R');
	$this->Cell(23,5,$po['ap_qty']." ".$po['po_unit'],1,0,'R');	
	$this->Cell(22,5,$po['amount'],1,0,'R');
	$this->Cell(19,5,$po['eta'],1,0,'L');
	*/
	$x = $this->getx();
	$y = $this->gety();
	$xx = $x + 165;
	$this->SetFont('big5','',8);
	$w =$this->GetStringWidth($po['con1']."  ".$po['con2']);
	$sc = ceil($w / 88 );
	if(($y +($sc * $_SESSION['PDF']['lh'])) > $_SESSION['PDF']['h']){ #判斷換頁 PS
		$this->AddPage();
		$x = $this->getx();
		$y = $this->gety();
		$xx = $x + 165; 
	}	

	$this->SetFont('Arial','',9);
	$this->Cell(20,5,$po['ord_num'],'LTR');
	$this->SetFont('big5','',8);
	$this->Cell(20,5,$po['mat_code'],'TR');
	$this->Cell(37,5,$po['mat_name'],'TR');
	$this->MultiCell(88,5,$po['con1']."  ".$po['con2'],'TR','L');
	$this->setxy($xx,$y);
	$this->Cell(30,5,$po['color'],'TR');
	$this->SetFont('Arial','',9);
	$this->Cell(21,5,$po['prics']."/".$po['prc_unit'],'TR',0,'R');
	$this->Cell(23,5,$po['ap_qty']." ".$po['po_unit'],'TR',0,'R');	
	$this->Cell(22,5,$po['amount'],'TR',0,'R');
	$this->Cell(19,5,$po['po_eta'],'TR',1,'L');

	if($w > 88){
		$this->ln();
		$this->setxy($x,$y);
		$sc = ceil($w / 88 );
		for($i=0;$i < $sc;$i++){
			$this->Cell(20, $_SESSION['PDF']['lh'],'','LR',0);
			$this->Cell(20, $_SESSION['PDF']['lh'],'','R',0);
			$this->Cell(37, $_SESSION['PDF']['lh'],'','R',0);
			$this->setx($this->getx() + 88);
			$this->Cell(30, $_SESSION['PDF']['lh'],'','LR');
			$this->Cell(21, $_SESSION['PDF']['lh'],'','R',0);
			$this->Cell(23, $_SESSION['PDF']['lh'],'','R',0);	
			$this->Cell(22, $_SESSION['PDF']['lh'],'','R',0);
			$this->Cell(19, $_SESSION['PDF']['lh'],'','R',1);
		}
	}
	$this->Cell(280,0,'','T',1);	
}

function pa_det_mix($po,$ords)
{
	
	$j=0;
	if(mb_strlen($po['con1'],'big-5') > 50)
	{
		$tmp = $po['con1'];
		$con[$j] =mb_substr($tmp,0,50,'big-5');
		$j++;
		$con[$j] =mb_substr($tmp,50,'big-5');
		$j++;
	}else{
		$con[$j] =$po['con1'];
		$j++;
	}
	
	if(mb_strlen($po['con2'],'big-5') > 50)
	{
		$tmp = $po['con2'];
		$con[$j] =mb_substr($tmp,0,50,'big-5');
		$j++;
		$con[$j] =mb_substr($tmp,50,'big-5');
		$j++;
	}else{
		$con[$j] =$po['con2'];
		$j++;
	}	
	$tmp = $po['color'];
	$color[0] =substr($tmp,0,14);
	$color[1] =substr($tmp,14);
	
	$this->SetFont('Arial','',9);
	$this->Cell(20,5,$ords[0],'RL');
	$this->SetFont('big5','',8);
	$this->Cell(20,5,$po['mat_code'],'RL');
	$this->Cell(37,5,$po['mat_name'],'RL');
	$this->Cell(88,5,$con[0],'RL');
	$this->Cell(30,5,$color[0],'RL');
	$this->SetFont('Arial','',9);
	$this->Cell(21,5,$po['prics']."/".$po['prc_unit'],'RL',0,'R');
	$this->Cell(23,5,$po['ap_qty']." ".$po['po_unit'],'RL',0,'R');	
	$this->Cell(22,5,$po['amount'],'RL',0,'R');
	$this->Cell(19,5,$po['po_eta'],'RL',0,'L');
	$this->ln();
	if(sizeof($ords) > sizeof($con))$j = sizeof($ords);
	if($j < 2) $j = 2;
	for ($i=1; $i< $j; $i++)
	{
		$this->SetFont('Arial','',9);
		if(!isset($color[$i]))	$color[$i] = '';
		if(isset($ords[$i]))
		{
			$this->Cell(20,5,$ords[$i],'RL');
		}else{
			$this->Cell(20,5,'','RL');
		}
		$this->Cell(20,5,'','RL');
		$this->Cell(37,5,'','RL');	
		$this->SetFont('big5','',8);		
		if(isset($con[$i]))		
		{	
			$this->Cell(88,5,$con[$i],'RL');
		}else{
			$this->Cell(88,5,'','RL');
		}		
		$this->Cell(30,5,$color[$i],'RL');
		$this->SetFont('Arial','',9);
		$this->Cell(21,5,'','RL');
		$this->Cell(23,5,'','RL');
		$this->Cell(22,5,'','RL');	
		$this->Cell(19,5,'','RL');
		$this->ln();
	}
	$this->Cell(20,0.5,'','RLB');
	$this->Cell(20,0.5,'','RLB');
	$this->Cell(37,0.5,'','RLB');		
	$this->Cell(88,0.5,'','RLB');
	$this->Cell(30,0.5,'','RLB');
	$this->Cell(21,0.5,'','RLB');
	$this->Cell(23,0.5,'','RLB');
	$this->Cell(22,0.5,'','RLB');	
	$this->Cell(19,0.5,'','RLB');
	$i++;
	return  $i;
}



function oth_cost($po)
{
	$this->SetFont('big5','',9);
	$this->Cell(239,5,$po['item'],1);
	$this->SetFont('Arial','',9);
	$this->Cell(22,5,$po['cost'],1,0,'R');
	$this->Cell(19,5,'',1,0,'L');
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
