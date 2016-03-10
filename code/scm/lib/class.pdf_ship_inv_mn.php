<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shidoc_inv_mn extends PDF_Chinese 	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;


//Page header
function Header() {

	global $print_title;
	global $print_title1;
	global $print_title2;
	global $mark;
	$main_rec = $_SESSION['main_rec'];

	$cmpy['name'] = $GLOBALS['SHIP_TO']['CA']['Name'];
	$cmpy['oth1'] = "TEL : ".$GLOBALS['SHIP_TO']['CA']['TEL']."  FAX : ".$GLOBALS['SHIP_TO']['CA']['FAX'];
	$cmpy['oth2'] = $GLOBALS['SHIP_TO']['CA']['Addr'];

	
  $this->SetFont('Arial','B',14);
	$this->SetXY(5,5);
	$this->Cell(200, 10,$cmpy['name'],0,0,'C');
	$this->SetXY(5,11);

	$this->SetFont('Arial','',8);
	$this->cell(200, 5,$cmpy['oth1'],0,0,'C');
	$this->SetXY(5,14);

	$this->cell(200, 5,$cmpy['oth2'],0,0,'C');
		
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,150,$mark,30);
	//title
	$this->SetXY(10,20);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','',6);
	$this->Cell(90, 7,$print_title1,0,0,'L',0);	
	$this->SetFont('Arial','B',14);
	$this->Cell(90, 7,$print_title,0,0,'L',0);

	//date
	$this->SetXY(180,22);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,28,200,28);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,29,200,29);
	$this->ln(5);
	$this->SetXY(10,31);
	$this->hend_title($main_rec);

}

function hend_title($parm)
{
	$tmp = explode(' ',$parm['bill_addr2']);
	$bill_str1 = $bill_str2 = $bill_str3 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($bill_str1)+strlen($tmp[$i]);
		$len2 = strlen($bill_str2)+strlen($tmp[$i]);
		$len3 = strlen($bill_str3)+strlen($tmp[$i]);
		if($len1 < 45 && strlen($bill_str2) <= 1)
		{
			$bill_str1 = $bill_str1.$tmp[$i]." ";
		}else if($len2 < 45 && strlen($bill_str3) <= 1){
			$bill_str2 = $bill_str2.$tmp[$i]." ";
		}else{
			$bill_str3 = $bill_str3.$tmp[$i]." ";
		}
	}
	

	$this->SetFont('Arial','B',10);
	$this->Cell(25, 5,"INVOICE NO.:",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(75, 5,$parm['inv_num'],0,0,'L',0);

	$this->SetFont('Arial','B',10);
	$this->Cell(20, 5,"DATE:",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(70, 5,$parm['date'],0,0,'L',0);
	$this->ln();

	$this->SetFont('Arial','B',10);
	$this->Cell(25, 5,"INVOICE OF:",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(75, 5,$parm['des'],0,0,'L',0);

	$this->SetFont('Arial','B',10);
	$this->Cell(20, 5,"SHIP BY : ",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(70, 5,$parm['ship_by'],0,0,'L',0);
	$this->ln();

	$this->SetFont('Arial','B',10);
	$this->Cell(100, 5,"FOR ACCOUNT & RISK OF MESSRS.(BILL TO).:",0,0,'L',0);
	$this->ln();
/*	
	$this->SetFont('Arial','B',10);
	$this->Cell(20, 5,"SHIP BY : ",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(70, 5,$parm['ship_by'],0,0,'L',0);
	$this->ln();
*/	
	$this->SetFont('Arial','',9);
	$this->Cell(100, 5,$parm['bill_addr1'],0,0,'L',0);

	$this->SetFont('Arial','B',10);
	$this->Cell(20, 5,"PER : ",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(70, 5,$parm['via'],0,0,'L',0);
	$this->ln();
	
	$this->SetFont('Arial','',9);
	$this->Cell(100, 5,$bill_str1,0,0,'L',0);

	$this->SetFont('Arial','B',10);
	$this->Cell(40, 5,"ON OR ABOUT : ",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(50, 5,$parm['ship_date'],0,0,'L',0);
	$this->ln();
	
	$this->SetFont('Arial','',9);
	$this->Cell(100, 5,$bill_str2,0,0,'L',0);

	$this->SetFont('Arial','B',10);
	$this->Cell(20, 5,"FROM : ",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(70, 5,$parm['ship_from'],0,0,'L',0);
	$this->ln();		
	
	$this->SetFont('Arial','',9);
	$this->Cell(100, 5,$bill_str3,0,0,'L',0);

	$this->SetFont('Arial','B',10);
	$this->Cell(20, 5,"TO : ",0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(70, 5,$parm['ship_to'],0,0,'L',0);
	$this->ln();		

	$this->SetFont('Arial','B',10);
	$this->Cell(40, 5,"TERM OF PAYMENT :",0,0,'L',0);
	$this->SetFont('Arial','',9);
	if($parm['payment'] == 'L/C')
	{
		$this->Cell(60, 5,$parm['payment']."# ".$parm['lc_no']."   DATED:".$parm['lc_date'],0,0,'L',0);
	}else{
		$this->Cell(60, 5,$parm['payment'],0,0,'L',0);
	}

	$this->ln();
	
	if($parm['bank'])
	{
		$this->SetFont('Arial','B',10);
		$this->Cell(40, 5,"ISSUING BANK:",0,0,'L',0);
		$this->SetFont('Arial','',9);
		$this->Cell(60, 5,$parm['bank'],0,0,'L',0);
		$this->ln();
	}
	$yy = $this->GetY();
	$this->SetY(($yy+2));


}

function item_title($ship_term)
{
		$this->Cell(120, 5,'ITEM NO./DESCRIPTION','TB',0,'L',0);
		$this->Cell(25, 5,'Q\'TY','TB',0,'C',0);
		$this->Cell(25, 5,'UNIT PRICE','TB',0,'C',0);
		$this->Cell(25, 5,'AMOUNT','TB',0,'C',0);
		$this->ln();
		$this->SetFont('Arial','U',8);
		$this->Cell(170, 5,$ship_term,0,0,'R',0);
		$this->ln();	
		
}


function item_txt($parm,$con_mk)
{
		if($con_mk == 1)
		{
			$this->SetFont('Arial','',8);
			$this->Cell(195, 4,$parm['des'],0,0,'L',0);
			$this->ln();
			$this->Cell(195, 4,"BREAKDOWN OF FABRIC COMPOSITION BY CHIEF WEIGHT:".$parm['content'],0,0,'L',0);
			$this->ln();
			$this->SetFont('Arial','UB',9);
			$this->Cell(40, 4,'P.O. NO.',0,0,'C',0);
			$this->Cell(40, 4,'STYLE NO.',0,0,'C',0);
			$this->Cell(40, 4,'COLOR',0,0,'C',0);		
			$this->ln();
		}
		$this->SetFont('Arial','',8);
		$this->Cell(40, 4,$parm['cust_po'],0,0,'C',0);
		$this->Cell(40, 4,$parm['style_num'],0,0,'C',0);
		$this->Cell(40, 4,$parm['color'],0,0,'C',0);		
		$this->Cell(15, 4,NUMBER_FORMAT($parm['bk_qty'],0),0,0,'R',0);	
		$this->Cell(10, 4,'PCS',0,0,'C',0);	
		$this->Cell(12, 4,'@USD',0,0,'R',0);	
		$this->Cell(13, 4,NUMBER_FORMAT($parm['uprice'],2),0,0,'R',0);	
		$this->Cell(25, 4,"US$".NUMBER_FORMAT($parm['amount'],2),0,0,'R',0);	

		$this->ln();
		

}

function item_total($parm)
{
		$vl1 = $vl2 = '';
		$this->SetFont('Arial','B',8);
		$this->Cell(120,0.5,'',0,0,'L',0);
		$this->Cell(75,0.5,'','B',0,'C',0);
		$this->ln();
		$this->Cell(110, 4,'TOTAL:',0,0,'R',0);
		$this->Cell(25, 4,NUMBER_FORMAT($parm['qty'],0),0,0,'R',0);	
		$this->Cell(10, 4,'PCS',0,0,'C',0);	
		$this->Cell(50, 4,"US$".NUMBER_FORMAT($parm['amount'],2),0,0,'R',0);	

		$this->ln();
		for($i=0; $i<(strlen($parm['qty'])); $i++) $vl1 .= 'V';
		for($i=0;	$i<(strlen(NUMBER_FORMAT($parm['amount'],2,'',''))+2); $i++) $vl2 .= 'V';
		$this->Cell(110,4,'',0,0,'L',0);
		$this->Cell(25, 4,$vl1,0,0,'R',0);
		$this->Cell(10, 4,'VVV',0,0,'C',0);	
		$this->Cell(50, 4,$vl2,0,0,'R',0);			
		$this->ln();
		$this->Cell(175,4,'SAY TOTAL U.S. DOLLORS '.$parm['eng_amount'].'.',0,0,'L',0);
		$this->ln();
}
function inv_txt($title,$txt)
{
	if($title)
	{
		$this->SetFont('Arial','B',8);
		$this->Cell(175,7,$title,0,0,'L',0);
		$this->ln();
	}
	$this->SetFont('Arial','',8);
	for($i=0; $i<sizeof($txt); $i++)
	{
		$this->Cell(175,4,$txt[$i],0,0,'L',0);
		$this->ln();
	}	
}

function pack_txt($parm)
{
	$this->ln();
	$this->SetFont('Arial','B',8);
	$this->Cell(20,4,'TOTAL',0,0,'L',0);
	$this->SetFont('Arial','',8);
	$this->Cell(20,4,'PACKED.:',0,0,'L',0);
	$this->Cell(20,4,$parm['ctn'],0,0,'R',0);
	$this->Cell(20,4,'  CTNS',0,0,'L',0);
	$this->ln();

	$this->Cell(20,4,'',0,0,'L',0);
	$this->Cell(20,4,'N.N.W.:',0,0,'L',0);
	$this->Cell(20,4,$parm['nnw'],0,0,'R',0);
	$this->Cell(20,4,'  KGS',0,0,'L',0);
	$this->ln();

	$this->Cell(20,4,'',0,0,'L',0);
	$this->Cell(20,4,'N.W.:',0,0,'L',0);
	$this->Cell(20,4,$parm['nw'],0,0,'R',0);
	$this->Cell(20,4,'  KGS',0,0,'L',0);
	$this->ln();	

	$this->Cell(20,4,'',0,0,'L',0);
	$this->Cell(20,4,'G.W.:',0,0,'L',0);
	$this->Cell(20,4,$parm['gw'],0,0,'R',0);
	$this->Cell(20,4,'  KGS',0,0,'L',0);
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
	global $print_title2;
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8

    $this->SetFont('Big5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
  $this->Cell(50,10,date('Y/m/d h:i A'),0,0,'C');
  $this->Cell(90,10,'INVOICE',0,0,'C');
  $this->Cell(50,10,'Page '.$this->PageNo().' of <nb>',0,0,'C');
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
