<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_remun extends PDF_Chinese 	{


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
	$this->setXY(10,$Y+10);
	$this->hend_title($ary_title);
	$Y = $this->getY();
	$this->setXY(10,$Y);
	
}

function hend_title($parm)
{

	$this->SetFont('BIG5','B',12);
	$this->Cell(20, 10,'代工 : ',1,0,'R');
	$this->SetFont('BIG5','',12);
	$this->Cell(120, 10,$parm['sub'],1,0,'L');
	$this->SetFont('BIG5','B',12);
	$this->Cell(20, 10,'出口 : ',1,0,'R');
	$this->SetFont('BIG5','',12);
	$this->Cell(120, 10,$parm['ship'],1,0,'L');
	$this->ln();
	
	$this->SetFont('BIG5','B',12);
	$this->Cell(20, 10,'受款人 : ',1,0,'R');
	$this->SetFont('BIG5','',12);
	$this->Cell(75, 10,$parm['Name'],1,0,'L');
	
	$this->SetFont('BIG5','B',12);
	$this->Cell(25, 10,'受款銀行 : ',1,0,'R');
	$this->SetFont('BIG5','',12);
	$this->Cell(100, 10,$parm['bank'],1,0,'L');

	$this->SetFont('BIG5','B',12);
	$this->Cell(25, 10,'受款帳號 : ',1,0,'R');
	$this->SetFont('BIG5','',12);
	$this->Cell(35, 10,$parm['id'],1,0,'L');
	$this->ln();$this->ln();

//title	
	$this->SetFont('Arial','B',10);
	$this->Cell(20, 5,'ORDER #','TLR',0,'C');
	$this->Cell(41, 5,'Style #','TLR',0,'C');
	$this->Cell(15, 5,'Style','TLR',0,'C');
	$this->Cell(12, 5,'ORD','TLR',0,'C');
	$this->Cell(10, 5,'cal.','TLR',0,'C');
	$this->Cell(10, 5,'fnl.','TLR',0,'C');
	$this->Cell(0.3, 5,'','TLR',0,'C');
	
	$this->Cell(12, 5,'C.M.','TLR',0,'C');
	$this->Cell(15, 5,'Other','TLR',0,'C');
	$this->Cell(10, 5,'Acc','TLR',0,'C');
	$this->Cell(10, 5,'Exe.','TLR',0,'C');
	$this->Cell(15, 5,'Handling','TLR',0,'C');
	$this->Cell(15, 5,'@cost','TLR',0,'C');
	$this->Cell(12, 5,'Smpl','TLR',0,'C');
	$this->Cell(15, 5,'SHPed.','TLR',0,'C');
	$this->Cell(20, 5,'Cost','TLR',0,'C');
	$this->Cell(48, 5,'Remark','TLR',0,'C');
	$this->ln();
	
	$this->Cell(20, 5,'','BLR',0,'C');
	$this->Cell(41, 5,'','BLR',0,'C');
	$this->Cell(15, 5,'','BLR',0,'C');
	$this->Cell(12, 5,'Qty','BLR',0,'C');
	$this->Cell(10, 5,'IE','BLR',0,'C');
	$this->Cell(10, 5,'IE','BLR',0,'C');
	$this->Cell(0.3, 5,'','BLR',0,'C');
	
	$this->Cell(12, 5,'','BLR',0,'C');
	$this->Cell(15, 5,'cost','BLR',0,'C');
	$this->Cell(10, 5,'cost','BLR',0,'C');
	$this->Cell(10, 5,'cost','BLR',0,'C');
	$this->Cell(15, 5,'fee','BLR',0,'C');
	$this->Cell(15, 5,'','BLR',0,'C');
	$this->Cell(12, 5,'Qty','BLR',0,'C');
	$this->Cell(15, 5,'Qty','BLR',0,'C');
	$this->Cell(20, 5,'','BLR',0,'C');
	$this->Cell(48, 5,'','BLR',0,'C');	
	$this->ln();
//雙線	
	$this->Cell(20, 0.5,'',1,0,'R');
	$this->Cell(61, 0.5,'',1,0,'R');
	$this->Cell(15, 0.5,'',1,0,'R');
	$this->Cell(12, 0.5,'',1,0,'C');
	$this->Cell(10, 0.5,'',1,0,'C');
	$this->Cell(10, 0.5,'',1,0,'C');
	$this->Cell(0.3, 0.5,'',1,0,'C');

	$this->Cell(12, 0.5,'',1,0,'R');
	$this->Cell(15, 0.5,'',1,0,'R');
	$this->Cell(15, 0.5,'',1,0,'R');
	$this->Cell(12, 0.5,'',1,0,'R');
	$this->Cell(15, 0.5,'',1,0,'R');
	$this->Cell(20, 0.5,'',1,0,'R');
	$this->Cell(63, 0.5,'',1,0,'R');
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
    $this->SetFont('BIG5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                           Created by : '.$creator,0,0,'C');
}


// field module 1 [表匡 1 ]


function rem_table($rem)
{

	$tmp_rmk = array();
	
	$l2=40;
	$l1=0;
	$cut = 0;
	if($rem['exc_id'] > 0)$rem['rmk'] = '異常報告 '.$rem['rmk'];
	$str_long = mb_strlen($rem['rmk'],'big-5');
	$_bcmod = bcmod($str_long,$l2);
	$cut = $str_long / $l2;	
	for ($z=0; $z < $cut; $z++)
	{						
		$tmp_rmk[] = mb_substr($rem['rmk'],$l1,$l2,'big-5'); 
		$l1=$l1+$l2;
	}
	
	if(!isset($tmp_rmk[0]))$tmp_rmk[0]='';
		$this->SetFont('BIG5','',10);
		$this->Cell(20, 7,$rem['ord_num'],'LR',0,'C');
		$this->SetFont('BIG5','',8);
		$this->Cell(41, 7,$rem['style_num'],'LR',0,'L');	
		// $this->MultiCell(41,3,$rem['style_num'],0,'L',0);
		$this->SetFont('BIG5','',10);
		$this->Cell(15, 7,$rem['style_des'],'LR',0,'L');

		$this->SetFont('Arial','',10);
		$this->Cell(12, 7,$rem['ord_qty'],'LR',0,'R');
		$this->Cell(10, 7,$rem['ie1'],'LR',0,'R');
		$this->Cell(10, 7,$rem['ie2'],'LR',0,'R');
		$this->Cell(0.3, 7,'','LR',0,'R');
		
		$this->Cell(12, 7,$rem['cm_cost'],'LR',0,'R');
		$this->Cell(15, 7,$rem['oth_cost'],'LR',0,'R');
		$this->Cell(10, 7,$rem['acc_cost'],'LR',0,'R');
		$this->Cell(10, 7,$rem['exc_cost'],'LR',0,'R');
		$this->Cell(15, 7,$rem['handling_fee'],'LR',0,'R');
		$this->Cell(15, 7,$rem['a_cost'],'LR',0,'R');
		$this->Cell(12, 7,$rem['smpl'],'LR',0,'R');
		$this->Cell(15, 7,$rem['rem_qty'],'LR',0,'R');
		$this->Cell(20, 7,$rem['cost'],'LR',0,'R');
		$this->SetFont('BIG5','',8);
		$this->Cell(48, 7,$tmp_rmk[0],'LR',0,'L');	
		$this->ln();

	
	for($i=1; $i < sizeof($tmp_rmk); $i++)
	{
		$this->SetFont('BIG5','',10);
		$this->Cell(20, 7,'','LR',0,'C');
		$this->SetFont('BIG5','',8);
		$this->Cell(61, 7,'','LR',0,'L');	
		$this->SetFont('BIG5','',10);
		$this->Cell(15, 7,'','LR',0,'L');

		$this->SetFont('Arial','',10);
		$this->Cell(12, 7,'','LR',0,'R');
		$this->Cell(10, 7,'','LR',0,'R');
		$this->Cell(10, 7,'','LR',0,'R');
		$this->Cell(12, 7,'','LR',0,'R');
		$this->Cell(15, 7,'','LR',0,'R');
		$this->Cell(15, 7,'','LR',0,'R');
		$this->Cell(12, 7,'','LR',0,'R');
		$this->Cell(15, 7,'','LR',0,'R');
		$this->Cell(20, 7,'','LR',0,'R');
		$this->SetFont('BIG5','',8);
		$this->Cell(63, 7,$tmp_rmk[$i],'LR',0,'L');	
		$this->ln();
	}
//雙線	
	$this->Cell(20, 0.1,'',1,0,'R');
	$this->Cell(61, 0.1,'',1,0,'R');
	$this->Cell(15, 0.1,'',1,0,'R');
	$this->Cell(12, 0.1,'',1,0,'C');
	$this->Cell(10, 0.1,'',1,0,'C');
	$this->Cell(10, 0.1,'',1,0,'C');

	$this->Cell(12, 0.1,'',1,0,'R');
	$this->Cell(15, 0.1,'',1,0,'R');
	$this->Cell(15, 0.1,'',1,0,'R');
	$this->Cell(12, 0.1,'',1,0,'R');
	$this->Cell(15, 0.1,'',1,0,'R');
	$this->Cell(20, 0.1,'',1,0,'R');
	$this->Cell(63, 0.1,'',1,0,'R');
	$this->ln();	
	
}

function mang_chk($sub_user,$cfm_user,$apv_user)
{
	$this->SetY(180);
	$this->SetFont('BIG5','',10);
	$this->Cell(100, 7,'審批 : '.$apv_user,0,0,'C');
	$this->Cell(90, 7,'複核 : '.$cfm_user,0,0,'C');
	$this->Cell(90, 7,'申請人 : '.$sub_user,0,0,'C');

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
