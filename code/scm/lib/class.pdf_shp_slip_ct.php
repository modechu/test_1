<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_slip_ct extends PDF_Chinese 	{


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

	$tmp = explode(' ',$main_rec['bill_addr']);
	$str1 = $str2 = $str3 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str1)+strlen($tmp[$i]);
		$len2 = strlen($str2)+strlen($tmp[$i]);
		$len3 = strlen($str3)+strlen($tmp[$i]);
		if($len1 < 40 && strlen($str2) <= 1)
		{
			$str1 = $str1.$tmp[$i]." ";
		}else if($len2 < 40 && strlen($str3) <= 1){
			$str2 = $str2.$tmp[$i]." ";
		}else{
			$str3 = $str3.$tmp[$i]." ";
		}
	}	


  $this->SetFont('Arial','B',14);
	$this->Cell(200, 10,$cmpy['name'],0,1,'C');

	$this->SetFont('Arial','',8);
	$this->cell(200, 5,$cmpy['oth1'],0,1,'C');

	$this->cell(200, 5,$cmpy['oth2'],0,1,'C');


    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(80,100,$mark,30);


	$this->SetY(35);
	$this->SetTextColor(40,40,40);

	$this->SetFont('Arial','',10);
	$this->Cell(90, 5,'FROM:'.$main_rec['ship_from'],0,0,'L',0);
	$this->Cell(100, 5,"TO: THE CATINOVICE NO.".$main_rec['inv_num'],0,1,'L',0);

	$this->Cell(90, 5,'TO:'.$main_rec['ship_to'],0,0,'L',0);
	$this->Cell(100, 5,"REMIT TO:".$main_rec['bill_to'],0,1,'L',0);

	if($str1)
	{
		$this->Cell(108, 5,"",0,0,'C',0);
		$this->Cell(100, 5,$str1,0,1,'L',0);
	}

	if($str2)
	{
		$this->Cell(108, 5,"",0,0,'C',0);
		$this->Cell(100, 5,$str2,0,1,'L',0);
	}
	
	if($str3)
	{
		$this->Cell(108, 5,"",0,0,'C',0);
		$this->Cell(100, 5,$str3,0,1,'L',0);
	}

	$this->SetFont('Arial','B',14);
	$this->Cell(200, 8,$print_title,0,1,'C',0);	
	$yy = $this->GetY();
	$this->SetY(($yy+3));
	$this->hend_title();
	
}

function hend_title()
{
	
	$this->SetFont('Arial','',9);

	$this->Cell(30,4,'','TLR',0,'C'); // cntr#
	$this->Cell(30,4,'','TLR',0,'C'); //dcscription
	$this->Cell(20,4,'PO','TLR',0,'C'); //po_num
	$this->Cell(25,4,'NO. OF','TLR',0,'C'); // NO. OF CARTONS
	$this->Cell(50,4,'CARTON ID','TLR',0,'C');	// CARTON ID number
	$this->Cell(20,4,'TOTAL','TLR',0,'C');	// TOTAL cartons
	$this->Cell(25,4,'TOTAL','TLR',1,'C');	// TOTAL cartons

	$this->Cell(30,4,'CTNR#','BLR',0,'C'); // cntr#
	$this->Cell(30,4,'DESCEIPTION','BLR',0,'C'); //dcscription
	$this->Cell(20,4,'NUMBER','BLR',0,'C'); //po_num
	$this->Cell(25,4,'CARTONS','BLR',0,'C'); // NO. OF CARTONS
	$this->Cell(50,4,'NUMBER','BLR',0,'C');	// CARTON ID number
	$this->Cell(20,4,'CARTONS','BLR',0,'C');	// TOTAL cartons
	$this->Cell(25,4,'UNITS/PCS','BLR',1,'C');	// TOTAL cartons



}

function ship_ord_det($parm,$car_mk,$carrier)
{	
	$this->SetFont('Arial','',7);
	$content = explode('|',$parm['content']);
	$this->ln();
	for($i=0; $i<sizeof($content); $i++) 
	{
		if($car_mk <> 0)$carrier='';
		$this->Cell(30,4,$carrier,0,0,'C'); // cntr#
		$this->Cell(185,4,$content[$i],0,1,'L');
	}
}

function ship_txt($parm)
{
	$ctn = explode('-',$parm['cnt']);
	$this->Cell(60,4,'',0,0,'C'); // cntr#
	$this->Cell(20,4,$parm['cust_po'],0,0,'C'); //po_num
	if($parm['f_mk'] == 1)
	{
		$this->Cell(10,4,$ctn[0],0,0,'C'); //po_num
		$this->Cell(15,4,$ctn[1],0,0,'C'); //po_num
	}else{
		$this->Cell(10,4,'',0,0,'C'); //po_num
		$this->Cell(15,4,'',0,0,'C'); //po_num
	}
	$this->Cell(50,4,$parm['ctn_id'],0,0,'C');	// CARTON ID number
	if($parm['f_mk'] == 1)
	{
		$this->Cell(20,4,NUMBER_FORMAT($parm['cnum'],0),0,0,'R');	// TOTAL cartons
	}else{
		$this->Cell(20,4,'',0,0,'R');	// TOTAL cartons
	}
	$this->Cell(25,4,NUMBER_FORMAT(($parm['s_cnum'] * $parm['s_qty']),0),0,1,'R');	// TOTAL cartons


}

function ship_total($parm)
{
	//205
	$this->SetFont('Arial','',7);
	$y = $this->GetY();
	$this->Line(5, $y, 205, $y);
	$y+=3;
	$this->Cell(60,5,'TOTAL:',0,0,'C'); // cntr#
	$this->Cell(95,5,'',0,0,'C'); //po_num
	$this->Cell(20,5,$parm['cnum'],0,0,'R');	// TOTAL cartons
	$this->Cell(25,5,NUMBER_FORMAT($parm['qty'],0),0,1,'R');	// TOTAL cartons
	$y = $this->GetY();
	$this->Line(5, $y, 205, $y);
	$y+=0.5;
	$this->Line(5, $y, 205, $y);



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









function weight($parm)
{
	//205
	$this->SetFont('Arial','',7);
	$this->ln();
	$this->Cell(30,4,'TOTAL:',0,0,'C');
	$this->Cell(15,4,'N.N.W.',0,0,'L');
	$this->Cell(20,4,NUMBER_FORMAT($parm['nnw'],2),0,0,'R'); // net net weight
	$this->Cell(15,4,'KGS',0,1,'R');

	$this->Cell(30,4,'',0,0,'C');
	$this->Cell(15,4,'N.W.',0,0,'L'); 
	$this->Cell(20,4,NUMBER_FORMAT($parm['nw'],2),0,0,'R');	// net weight
	$this->Cell(15,4,'KGS',0,1,'R'); 

	$this->Cell(30,4,'',0,0,'C');
	$this->Cell(15,4,'G.W.',0,0,'L'); 
	$this->Cell(20,4,NUMBER_FORMAT($parm['gw'],2),0,0,'R');	// Gross weight
	$this->Cell(15,4,'KGS',0,1,'R'); 
}








function gd_total($parm)
{
	$fld_size = 110 / sizeof($parm['size']);
	$this->SetFont('Arial','',8);
	


	$this->SetX(8);
	$this->Cell(80,4,'',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(110,4,'',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(18,4,'',1,0,'C');
	$this->Cell(16,4,'',1,0,'C');
	$this->Cell(18,4,'',1,0,'C');
	$this->Cell(16,4,'',1,0,'C');
	$this->Cell(14,4,'',1,0,'C');
	$this->Cell(14,4,'',1,0,'C');
	$this->ln();	

	$this->SetX(8);
	$this->Cell(80,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	
	$this->Cell(110,0.5,'',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(80,4,'GRAND TOTAL >>',1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	for ($i=0; $i<sizeof($parm['size']); $i++)$this->Cell($fld_size,4,$parm['size'][$i],1,0,'C');
	$this->Cell(0.3,4,'',1,0,'C');
	$this->Cell(18,4,$parm['ctn'],1,0,'C');
	$this->Cell(16,4,'',1,0,'C');
	$this->Cell(18,4,$parm['qty'],1,0,'C');
	$this->Cell(16,4,NUMBER_FORMAT($parm['nnw'],2),1,0,'R');
	$this->Cell(14,4,NUMBER_FORMAT($parm['nw'],2),1,0,'R');
	$this->Cell(14,4,NUMBER_FORMAT($parm['gw'],2),1,0,'R');
	$this->ln();
	
	$this->SetX(8);
	$this->Cell(80,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	$this->Cell(110,0.5,'',1,0,'C');
	$this->Cell(0.3,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(18,0.5,'',1,0,'C');
	$this->Cell(16,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
	$this->Cell(14,0.5,'',1,0,'C');
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
