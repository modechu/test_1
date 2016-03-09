<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_inv_ct extends PDF_Chinese 	{


	var $B=0;
  var $I=0;
  var $U=0;
  var $HREF='';
  var $ALIGN='';
	var $angle=0;



//Page header
function Header() {

											
	$main_rec = $_SESSION['main_rec'];
	global $print_title;
	global $print_title1;
	global $mark;
	global $print_title2;

	if($main_rec['fty'] == $GLOBALS['SCACHE']['ADMIN']['dept'])
	{
		$cmpy['name'] =	$GLOBALS['SHIP_TO'][$main_rec['fty']]['Name'];
		$cmpy['oth1'] = $GLOBALS['SHIP_TO'][$main_rec['fty']]['Addr'];
		$cmpy['oth2'] = "TEL : ".$GLOBALS['SHIP_TO'][$main_rec['fty']]['TEL']."  FAX : ".$GLOBALS['SHIP_TO'][$main_rec['fty']]['FAX'];
	}else{
		$cmpy['name'] = $GLOBALS['SHIP_TO']['CA']['Name'];
		$cmpy['oth1'] = "TEL : ".$GLOBALS['SHIP_TO']['CA']['TEL']."  FAX : ".$GLOBALS['SHIP_TO']['CA']['FAX'];
		$cmpy['oth2'] = $GLOBALS['SHIP_TO']['CA']['Addr'];
	}

  $this->SetFont('Arial','B',14);
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
    $this->RotatedText(100,150,$mark,30);
	//Logo
//	$w =$this->GetStringWidth($print_title) +30;
	//title

	$this->SetY(20);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','B',12);
	$this->Cell(10, 5,'',0,0,'C',0);
	$this->Cell(190, 5,$print_title,0,1,'C',0);
	$this->SetFont('Arial','',7);
	$this->Cell(10, 5,$print_title1,0,0,'L',0);
	$this->SetFont('Arial','B',12);
	$this->Cell(190, 5,"*************************",0,1,'C',0);

//  $this->SetFont('Arial','I',12);
//	$this->Cell(90, 7,'Page '.$this->PageNo().' / <nb> ',0,0,'R',0);
	
	//date
 //   $this->SetFont('Arial','I',9);
	$this->SetXY(180,24);
//	$this->Cell(20, 7,$dt['date'],0,1,'C',0);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
//	$this->Line(10,27,290,27);
//	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
//	$this->Line(10,28,290,28);
//	$this->ln(5);
	
	$this->SetY(32);
	$this->hend_title($main_rec);
	$this->SetY(70);
	
}

function hend_title($parm)
{
	$this->SetFont('Arial','B',10);
	$this->Cell(62, 5,"INVOICE NO.:",0,0,'L',0);
	$this->Cell(90, 5,$parm['inv_num'],0,0,'L',0);
	$this->Cell(20, 5,"DATE:",0,0,'L',0);
	$this->Cell(30, 5,$parm['inv_date'],0,1,'L',0);

	$this->Cell(62, 5,"INVOICE OF:",0,0,'L',0);
	$this->Cell(143, 5,$parm['pack'],'B',1,'L',0);

	$this->Cell(62, 5,"FOR ACCOUNT&RIST OF MESSRS.",0,0,'L',0);
	$this->Cell(143, 5,$parm['bill_to'],'B',1,'L',0);

	$this->Cell(62, 5,"",0,0,'L',0);
	$this->Cell(143, 5,$parm['bill_addr'],'B',1,'L',0);

	$this->Cell(40, 5,"PER S.S.",0,0,'L',0);
	$this->Cell(70, 5,$parm['ship_via'],'B',0,'L',0);
	$this->Cell(45, 5,"SAILING ON OR ABOUT:",0,0,'L',0);
	$this->Cell(50, 5,$parm['date'],'B',1,'C',0);

	$this->Cell(40, 5,"FROM",0,0,'L',0);
	$this->Cell(70, 5,$parm['ship_from'],'B',0,'L',0);
	$this->Cell(30, 5,"TO:",0,0,'R',0);
	$this->Cell(50, 5,$parm['ship_to'],'B',1,'L',0);

	$this->Cell(40, 5,"L/C NO:",0,0,'L',0);
	$this->Cell(70, 5,$parm['lc_num']."DATED".$parm['lc_date'],'B',0,'L',0);
	$this->Cell(45, 5,"ETA DEST:",0,0,'R',0);
	$this->Cell(35, 5,$parm['eta'],'B',1,'L',0);

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
    $this->SetFont('BIG5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
  $this->Cell(50,10,date('Y/m/d h:i A'),0,0,'C');
	$this->Cell(100, 7,$this->PageNo().' of <nb> page',0,0,'C',0);
  $this->Cell(50,10,$print_title2,0,0,'R');
}


// field module 1 [ªí¦J 1 ]


function ship_title($term)
{
	
	$this->SetFont('Arial','',9);

	$this->Cell(85,4,'MARK&NUMBER',1,0,'C'); // c/NO.
	$this->Cell(70,4,'DESCRIPTION OF GOODS',1,0,'C'); //dcscription of goods
	$this->Cell(50,4,'QUANTITY',1,1,'C'); //quantity
	$this->Cell(190,4,$term,0,1,'R'); //quantity

}

function ship_mark($parm)
{
	$ship_det = explode(chr(13).chr(10),$parm['ship_det']);
	$ship_mark = explode(chr(13).chr(10),$parm['ship_mark']);
	$str2 =$str1 =$str3 ='';
	$tmp = explode(' ',$parm['ship_addr']);
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str1)+strlen($tmp[$i]);
		$len2 = strlen($str2)+strlen($tmp[$i]);
		$len3 = strlen($str3)+strlen($tmp[$i]);
		
		if($len1 < 35 && strlen($str2) <= 1)
		{
			$str1 = $str1.$tmp[$i]." ";
		}else if($len2 < 35 && strlen($str3) <= 1){
			$str2 = $str2.$tmp[$i]." ";
		}else{
			$str3 = $str3.$tmp[$i]." ";
		}
	}	
	

	$this->SetFont('Arial','',8);
	$this->Cell(60,5,'SHIPPING MARKS:',0,1,'L'); 
	$this->ln();
	$this->Cell(60,5,'FROM:',0,1,'L'); 
	for ($i=0; $i<sizeof($ship_det); $i++)$this->Cell(55,5,$ship_det[$i],0,1,'L'); 
	$this->Cell(60,5,'TO:',0,1,'L'); 
	$this->Cell(60,5,$parm['ship_to'],0,1,'L'); 
	$this->Cell(60,5,$str1,0,1,'L'); 
	if($str2)$this->Cell(60,5,$str2,0,1,'L'); 
	if($str3)$this->Cell(60,5,$str3,0,1,'L');
	for ($i=0; $i<sizeof($ship_mark); $i++)$this->Cell(55,5,$ship_mark[$i],0,1,'L'); 
	
	
}


function ship_ord_det($parm)
{
	
	$str[2] =$str[1] =$str[3] ='';
	$tmp = explode(' ',$parm['content']);
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str[1])+strlen($tmp[$i]);
		$len2 = strlen($str[2])+strlen($tmp[$i]);
		$len3 = strlen($str[3])+strlen($tmp[$i]);
		
		if($len1 < 80 && strlen($str[2]) <= 1)
		{
			$str[1] = $str[1].$tmp[$i]." ";
		}else if($len2 < 80 && strlen($str[3]) <= 1){
			$str[2] = $str[2].$tmp[$i]." ";
		}else{
			$str[3] = $str[3].$tmp[$i]." ";
		}
	}	

	$this->SetFont('Arial','',8);
	$content = explode(',',$parm['content']);
	for($i=1; $i<=sizeof($str); $i++) 
	{		
		if($str[$i])
		{
			$this->SetX(60);
			$this->Cell(150,5,$str[$i],0,1,'L');
		}
	}
	$this->SetX(60);
	$this->Cell(20,5,'PO#',0,0,'L');
	$this->Cell(40,5,'STY#',0,0,'L');
	$this->Cell(35,5,'CASE#',0,1,'L');

	$this->SetX(60);
	$this->Cell(20,5,$parm['cust_po'],0,0,'L');
	$this->Cell(40,5,$parm['style'],0,0,'L');
	$this->Cell(35,5,$parm['case'],0,1,'L');

}

function ship_txt($parm)
{
	//290
	$this->SetX(60);
	$this->Cell(20,5,'CARTON I/D#',0,0,'L');
	$this->Cell(40,5,$parm['ctn_id'],0,0,'L');
	$this->Cell(30,5,NUMBER_FORMAT(($parm['s_qty']*$parm['s_cnum']),0),0,0,'R');
	$this->Cell(5,5,'PCS',0,0,'C');
	$this->Cell(20,5,'US$'.$parm['ship_fob'],0,0,'R'); //quantity
	$this->Cell(25,5,'US$'.NUMBER_FORMAT(($parm['s_qty']*$parm['ship_fob']*$parm['s_cnum']),2),0,1,'R'); //quantity


}

function ship_total($name,$qty,$amount)
{
	//205
	$this->SetFont('Arial','',8);

	$this->SetX(60);
	$this->Cell(20,5,'',0,0,'L');
	$this->Cell(40,5,$name,0,0,'L');
	$this->Cell(30,5,NUMBER_FORMAT($qty,0),0,0,'R');
	$this->Cell(5,5,'PCS',0,0,'C');
	$this->Cell(20,5,'',0,0,'R'); //quantity
	$this->Cell(25,5,'US$'.NUMBER_FORMAT($amount,2),0,1,'R'); //quantity

}

function det_txt($txt)
{
	$tmp = explode(chr(13).chr(10),$txt);

	$this->SetFont('Arial','',8);
	foreach($tmp as $key	=>	$value) $this->Cell(200,5,$value,0,1,'L');

}

function end_ship($parm,$fty)
{

		
		$this->Cell(60,4,'TOTAL',0,1,'L');
		
		$this->Cell(60,4,'',0,0,'L');
		$this->Cell(30,4,'N.N.W. : ',0,0,'L');
		$this->Cell(20,4,$parm['nnw'],0,0,'C');
		$this->Cell(15,4,'KGS',0,0,'L');
		$this->ln();
		
		$this->Cell(60,4,'',0,0,'L');
		$this->Cell(30,4,'N.W. : ',0,0,'L');
		$this->Cell(20,4,$parm['nw'],0,0,'C');
		$this->Cell(15,4,'KGS',0,0,'L');
		$this->ln();	
		
		$this->Cell(60,4,'',0,0,'L');
		$this->Cell(30,4,'G.W. : ',0,0,'L');
		$this->Cell(20,4,$parm['gw'],0,0,'C');
		$this->Cell(15,4,'KGS',0,0,'L');
		$this->ln();	
		$this->ln();
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
