<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shidoc_inv_jg extends PDF_Chinese 	{


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

	$this->SetXY(5,3);
	//title
	$this->SetFont('Arial','B',14);
	$this->Cell(203, 7,$print_title,'TLR',0,'C',0);
	$this->ln();
	
	$this->SetX(5);	
  $this->SetFont('Arial','B',14);
	$this->Cell(203, 7,$cmpy['name'],'LR',0,'C');
	$this->ln();

	$this->SetX(5);	
	$this->SetFont('Arial','',6);
	$this->cell(203, 4,$cmpy['oth1'],'LR',0,'C');
	$this->ln();

	$this->SetX(5);
	$this->cell(203, 4,$cmpy['oth2'],'LR',0,'C');
	$this->ln();
	$this->SetX(5);
	$this->cell(203, 4,$print_title1,'BLR',0,'L');
	$this->ln();

		
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,150,$mark,30);

//	$this->SetXY(90,20);
	$this->SetTextColor(40,40,40);


 // $this->SetFont('Arial','I',9);
//	$this->Cell(30, 7,'Page '.$this->PageNo().' / <nb>',0,0,'C',0);

	//$this->SetXY(5,25);

}

function hend_title($parm)
{
	$tmp = explode(' ',$parm['ship_addr2']);
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
	$yy = $this->GetY();
	$txt = explode( chr(13).chr(10), $parm['ship_det'] );
	$this->SetFont('Arial','',8);
	$this->SetX(5);
	$this->Cell(100, 5,"Shipper/Exporter:",'TLR',1,'L',0);	
	for($i=0; $i<6; $i++) 
	{
		$this->SetX(5);
		if(!isset($txt[$i]))$txt[$i] = '';
		$this->Cell(100, 4,$txt[$i],'LR',1,'L',0);
	}
	$this->SetX(5);
	$this->Cell(100, 4,'','BLR',1,'L',0);	

	$this->SetX(5);
	$this->Cell(100, 5,"For Account& Risk Messers:",'TLR',1,'L',0);	
	$this->SetX(5);
	$this->Cell(100, 4,$parm['ship_addr1'],'LR',1,'L',0);
	$this->SetX(5);
	$this->Cell(100, 4,$bill_str1,'LR',1,'L',0);
	$this->SetX(5);
	$this->Cell(100, 4,$bill_str2,'LR',1,'L',0);
	$this->SetX(5);
	$this->Cell(100, 4,$bill_str3,'LR',1,'L',0);
	$this->SetX(5);
	$this->Cell(100, 4,'','BLR',1,'L',0);	


	$txt = explode( chr(13).chr(10), $parm['nty_part'] );
	$this->SetX(5);
	$this->Cell(100, 5,"Notify:",'TLR',1,'L',0);	
	for($i=0; $i<6; $i++) 
	{
		$this->SetX(5);
		if(!isset($txt[$i]))$txt[$i] = '';
		$this->Cell(100, 4,$txt[$i],'LR',1,'L',0);
	}
	$this->SetX(5);
	$this->Cell(100, 4,'','BLR',1,'L',0);	

	$this->SetXY(105,$yy);
	$this->Cell(103, 5,"Invoice No & Date:",'TLR',1,'L',0);	
	$this->SetX(105);
	$this->Cell(103, 4, $parm['inv_num'],'LR',1,'L',0);
	$this->SetX(105);
	$this->Cell(103, 4, $parm['submit_date'],'LR',1,'L',0);
	$this->SetX(105);
	$this->Cell(103, 4, '','BLR',1,'L',0);

	$this->SetX(105);
	$this->Cell(60, 4, 'L/C NO & Date:','TLR',0,'L',0);
	$this->Cell(43, 4, 'Issuing Bank:','TLR',1,'L',0);
	$this->SetX(105);
	$this->Cell(60, 4, $parm['lc_no'].' '. $parm['lc_date'],'LR',0,'L',0);
	$this->Cell(43, 4, substr($parm['lc_bank'],0,20),'LR',1,'L',0);
	$this->SetX(105);
	$this->Cell(60, 4, 'Payment by '.$parm['payment'],'BLR',0,'L',0);
	$this->Cell(43, 4, substr($parm['lc_bank'],20),'BLR',1,'L',0);

	$this->SetX(105);
	$this->Cell(103, 5,"Remarks:",'TLR',1,'L',0);	
	$this->SetFont('Arial','B',8);
	$this->SetX(105);
	$this->Cell(103, 5,"SHIPPING MARK:",'LR',1,'L',0);	
	$this->SetFont('Arial','',8);
	$txt = explode( chr(13).chr(10), $parm['ship_mark'] );
	for($i=0; $i<8; $i++)
	{
		$cell_line ='LR';
		if($i == 7) $cell_line ='BLR';
		$this->SetX(105);
		if(!isset($txt[$i]))$txt[$i] = '';
		$this->Cell(103, 4,$txt[$i],$cell_line,1,'L',0);
		
	}

	$this->SetX(105);
	$this->Cell(60, 4, 'Port of Loading','TLR',0,'L',0);
	$this->Cell(43, 4, 'Final Destination:','TLR',1,'L',0);
	$this->SetX(105);
	$this->Cell(60, 4, $parm['ship_from'],'BLR',0,'L',0);
	$this->Cell(43, 4, substr($parm['ship_to'],0,20),'BLR',1,'L',0);

	$this->SetX(105);
	$this->Cell(60, 4, 'Carrier:','TL',0,'L',0);
	$this->Cell(43, 4, 'Sailing on or about','TR',1,'L',0);
	$this->SetX(105);
	$this->Cell(60, 4, $parm['carrier'],'L',0,'C',0);
	$this->Cell(43, 4, substr($parm['ship_date'],0,20),'R',1,'L',0);
	$this->SetX(105);
	$this->Cell(103, 4,'','BLR',1,'L',0);

	$this->SetX(5);
	$this->Cell(53, 4, 'Shipping Mark:','TLR',0,'L',0);
	$this->Cell(100, 4, 'Country of Origin:','TLR',0,'L',0);
	$this->Cell(50, 4, 'Incoterms:','TLR',1,'L',0);

	$this->SetX(5);
	$this->Cell(53, 4, 'Please see remarks.','BLR',0,'L',0);
	$this->Cell(100, 4,$parm['country_org'],'BLR',0,'L',0);
	$this->Cell(50, 4, $parm['ship_term'],'BLR',1,'L',0);

}

function item_title()
{
//total 203 ³æ®æ¥­§¡15
		$this->SetX(5);
		$this->Cell(23, 4,'','TLR',0,'C',0);    			 //P.O.
		$this->Cell(12, 4,'','TLR',0,'C',0);					//Item#
		$this->Cell(27, 4,'','TLR',0,'C',0);					//Material
		$this->Cell(13, 4,'Quantity','TLR',0,'C',0); 	// quantity units
		$this->Cell(10, 4,'Fabric','TLR',0,'C',0);	 	//fabric price
		$this->Cell(18, 4,'CM, CMT or','TLR',0,'C',0);//cm, cmt or fob price
		$this->Cell(10, 4,'Sub','TLR',0,'C',0);				//sub total
		$this->Cell(12, 4,'Hanger','TLR',0,'C',0);		//hanger unit price
		$this->Cell(12, 4,'Belt','TLR',0,'C',0);			//belt unit price
		$this->Cell(15, 4,'Accessory','TLR',0,'C',0);	//accessory unit price
		$this->Cell(10, 4,'Sub','TLR',0,'C',0);				//sub total
		$this->Cell(18, 4,'Total','TLR',0,'C',0);			//total unit price
		$this->Cell(23, 4,'Total','TLR',1,'C',0);			//total vaule

		$this->SetX(5);
		$this->Cell(23, 4,'P.O.','BLR',0,'C',0);    			 //P.O.
		$this->Cell(12, 4,'Item#','BLR',0,'C',0);					//Item#
		$this->Cell(27, 4,'Material','BLR',0,'C',0);					//Material
		$this->Cell(13, 4,'Units','BLR',0,'C',0); 	// quantity units
		$this->Cell(10, 4,'Price','BLR',0,'C',0);	 	//fabric price
		$this->Cell(18, 4,'FOB price','BLR',0,'C',0);//cm, cmt or fob price
		$this->Cell(10, 4,'Total','BLR',0,'C',0);				//sub total
		$this->Cell(39, 4,'Unit Price','TBLR',0,'C',0);		//hanger unit price
		$this->Cell(10, 4,'Total','BLR',0,'C',0);				//sub total
		$this->Cell(18, 4,'Unit Price','BLR',0,'C',0);			//total unit price
		$this->Cell(23, 4,'Value','BLR',1,'C',0);			//total vaule
}


function item_txt($parm)
{
		$fob = $parm['ship_fob'] - $parm['hanger'] - $parm['belt'];
		$sub_total = $parm['hanger'] + $parm['belt'];
		$this->SetX(5);
		$this->Cell(23, 4,$parm['cust_po'],1,0,'C',0);    			 //P.O.
		$this->Cell(12, 4,$parm['item'],1,0,'C',0);					//Item#
		$this->Cell(27, 4,$parm['color'],1,0,'C',0);					//Material
		$this->Cell(13, 4,$parm['bk_qty'],1,0,'C',0); 	// quantity units
		$this->Cell(10, 4,'',1,0,'C',0);	 	//fabric price
		$this->Cell(18, 4,$fob,1,0,'C',0);//cm, cmt or fob price
		$this->Cell(10, 4,$fob,1,0,'C',0);				//sub total
		$this->Cell(12, 4,$parm['hanger'],'TLR',0,'C',0);		//hanger unit price
		$this->Cell(12, 4,$parm['belt'],'TLR',0,'C',0);			//belt unit price
		$this->Cell(15, 4,'','TLR',0,'C',0);	//accessory unit price
		$this->Cell(10, 4,$sub_total,1,0,'C',0);				//sub total
		$this->Cell(18, 4,$parm['ship_fob'],1,0,'C',0);			//total unit price
		$this->Cell(23, 4,'US$'.NUMBER_FORMAT(($parm['ship_fob']*$parm['bk_qty']),2),1,1,'R',0);			//total vaule
		

}

function item_total($parm)
{

		$this->SetX(5);
		$this->Cell(23, 4,'',1,0,'C',0);    			 //P.O.
		$this->Cell(12, 4,'',1,0,'C',0);					//Item#
		$this->Cell(27, 4,'',1,0,'C',0);					//Material
		$this->Cell(13, 4,NUMBER_FORMAT($parm['qty'],0),1,0,'R',0); 	// quantity units
		$this->Cell(10, 4,'',1,0,'C',0);	 	//fabric price
		$this->Cell(18, 4,'',1,0,'C',0);//cm, cmt or fob price
		$this->Cell(10, 4,'0',1,0,'C',0);				//sub total
		$this->Cell(12, 4,'','TLR',0,'C',0);		//hanger unit price
		$this->Cell(12, 4,'','TLR',0,'C',0);			//belt unit price
		$this->Cell(15, 4,'','TLR',0,'C',0);	//accessory unit price
		$this->Cell(10, 4,'0',1,0,'C',0);				//sub total
		$this->Cell(41, 4,'',1,1,'C',0);			//total unit price + total vaule

		$this->SetX(5);
		$this->Cell(163, 4,'Total Invoice value for customer : ','TBL',0,'C',0);    			 //P.O.
		$this->Cell(40, 4,"US$".NUMBER_FORMAT($parm['amount'],2),'TBR',1,'R',0);			//total vaule

//		$this->Cell(175,4,'SAY TOTAL U.S. DOLLORS '.$parm['eng_amount'].'.',0,0,'L',0);
//		$this->ln();
}
function inv_txt($title,$txt)
{
	$this->SetFont('Arial','',8);
	$this->SetX(5);
	$this->Cell(203,7,$title,'TLR',1,'L',0);
	for($i=0; $i<sizeof($txt); $i++)
	{
		$cell_line ='LR';
		if($i == (sizeof($txt)-1)) $cell_line ='BLR';
		$this->SetX(5);
		$this->Cell(203,4,$txt[$i],$cell_line,0,'L',0);
		$this->ln();
	}	
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
    $this->Cell(60,10,date('Y/m/d h:i A').'  P '.$this->PageNo().' / <nb>',0,0,'L');
		$this->Cell(40,10,'INV REG',0,0,'C');
    $this->Cell(100,10,$print_title2.'  JONES CND REG ',0,0,'R');
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
