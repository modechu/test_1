<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_EXC extends PDF_Chinese 	{


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
	global $ord_num;
	global $title_ary;
	$dt = decode_date(1);
  //Put watermark
   $this->SetFont('BIG5','B',50);
   $this->SetTextColor(230,210,210);
   $this->RotatedText(60,210,$ord_num,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title
	$this->SetXY(100,15);
	$this->SetTextColor(40,40,40);
  $this->SetFont('BIG5','I',9);
	$this->Cell(40, 7,$print_title2,0,0,'C',0);
	$this->SetFont('BIG5','B',14);
	$this->Cell(55, 7,$print_title,1,1,'C',0);

	$this->SetXY(180,18);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,200,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,200,26);
	$this->ln(5);

	//if(sizeof($title_ary['ord_nums']) > 1 || $title_ary['ord_num'] == '')
	//{
		$this->title_simpl($title_ary);
	//}else{
	//	$this->title($title_ary);
	//}

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

    $this->SetFont('Big5','I',8);
    //Page number
		$this->AliasNbPages('<nb>'); 

    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                          Created by : '.$creator."  Print Date : ".date('Y-m-d'),0,0,'C');
 
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

function title($field) {	//title
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
	$this->Cell(50, 5,$field['cust'],0,0,'L');
	$this->SetFont('BIG5','B',12);
	$this->Cell(20, 5,'訂單編號 : ',0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(23, 5,$field['ord_num'],0,0,'L');
	$this->Cell(13, 5,'ETD : ',0,0,'R');
	$this->Cell(25, 5,$field['etd'],0,0,'L');	
//	$this->ln();
	$this->SetFont('BIG5','B',12);
	$this->Cell(25, 5,'採購單編號 : ',0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(30, 5,$field['po_num'],0,0,'L');
	$this->ln();
	$this->ln();
	$this->SetFont('Arial','B',10);  
	$this->Cell(45, 5,'QTY   :    '.$field['qty'],0,0,'L');
	
	$this->Cell(50, 5,'SMPL. CFM :  '.$field['smpl_apv'],0,0,'L');	
	$this->Cell(50, 5,'SHIP DATE : '.$field['shp_date'],0,0,'L');
	$this->Cell(45, 5,'SHIP QTY :   '.$field['qty_shp'],0,0,'L');	
	$this->ln();	

	$this->Cell(45, 5,'FAB. ETA : '.$field['mat_etd'],0,0,'L');
	$this->Cell(50, 5,'FAB. RCVD :  '.$field['mat_shp'],0,0,'L');
	$this->Cell(50, 5,'ACC. ETA :  '.$field['m_acc_etd'],0,0,'L');
	$this->Cell(45, 5,'ACC. RCVD :  '.$field['m_acc_shp'],0,0,'L');
	$this->ln();	
	$this->ln();
	
}

function state($parm) { //異常
	$this->SetFont('BIG5','',10);
	$this->Cell(70,5,$parm['sta_name'],'LTB',0,'L');
	$this->Cell(60,5,$parm['org_name']." :   ".$parm['org_rec'],'TB',0,'L');
	if($parm['new_name'])
	{
		$this->Cell(60,5,$parm['new_name']." :   ".$parm['new_rec'],'RTB',0,'L');
	}else{
		$this->Cell(60,5,"    ",'RTB',0,'L');
	}

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
//creat ti's field


function exc_value($value,$sys) {
	$x=0;
	$sys_tmp=explode('<br>',$sys);
  for ($i=0; $i<sizeof($sys_tmp); $i++)
  {
		$this->SetFont('Big5','',9);
		if($sys_tmp[$i])$this->Cell(190,5,$sys_tmp[$i],'RL',1,'L');
		if($sys_tmp[$i])$x++;
				
	}
	if($sys)$this->Cell(190,5,'-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------','RL',1,'C');
	
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

function comm_value($comm,$x) {

	$this->SetFont('Big5','B',9);
	$this->Cell(190,5,$comm['dept_name']." -- [ ".$comm['comm_user'].' / '.$comm['comm_date'].' ] : ' ,'RL',0,'L');
	$this->ln();

  $tmp=explode('<br>',$comm['Comm']);
    for ($i=0; $i<sizeof($tmp); $i++)
    {
			if($x > 31)
			{
				$this->Cell(190,3,'','RLB',0,'L');
				$this->ln();
				$this->AddPage();		
				$this->fld_title('會辦單位意見');	
				$this->SetFont('Big5','B',9);
				$this->Cell(190,5,$comm['dept_name']." -- [ ".$comm['comm_user'].' / '.$comm['comm_date'].' ] : ' ,'RL',0,'L');
				$this->ln();		
				$x = 3;	
			}
			
			
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
	$x++;
	return $x;
}

function fld_title($title) {
		$this->SetFont('Big5','',12);
		$this->cell(190,7,$title,1,0,'C');
		$this->ln();
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




}  // end of class

?>
