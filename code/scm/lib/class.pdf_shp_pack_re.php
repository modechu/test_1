<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_pack_RE extends PDF_Chinese 	{


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
	$this->SetXY(5,5);
	$this->Cell(290, 10,$cmpy['name'],0,0,'C');
	$this->SetXY(5,11);

	$this->SetFont('Arial','',8);
	$this->cell(290, 5,$cmpy['oth1'],0,0,'C');
	$this->SetXY(5,14);

	$this->cell(290, 5,$cmpy['oth2'],0,0,'C');

	//Logo
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(100,150,$mark,30);

	//title
	$this->SetXY(10,20);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','BU',14);
	$this->Cell(275, 7,$print_title,0,0,'C',0);

	$this->SetXY(10,28);
	
}

function hend_title($parm)
{
	global $pg_mk;
	
	$str_csg[2] =$str_csg[1] =$str_csg[3] =$str_csg[4]='';
	$tmp = explode(' ',$parm['consignee_addr']);
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($str_csg[1])+strlen($tmp[$i]);
		$len2 = strlen($str_csg[2])+strlen($tmp[$i]);
		$len3 = strlen($str_csg[3])+strlen($tmp[$i]);
		$len4 = strlen($str_csg[4])+strlen($tmp[$i]);
		
		if($len1 < 60 && strlen($str_csg[2]) <= 1)
		{
			$str_csg[1] = $str_csg[1].$tmp[$i]." ";
		}else if($len2 < 60 && strlen($str_csg[3]) <= 1){
			$str_csg[2] = $str_csg[2].$tmp[$i]." ";
		}else if($len3 < 60 && strlen($str_csg[4]) <= 1){
			$str_csg[3] = $str_csg[3].$tmp[$i]." ";
		}else{
			$str_csg[4] = $str_csg[4].$tmp[$i]." ";
		}
	}	
	
	$this->setX(5);
	$this->SetFont('Arial','',9);
	$this->Cell(40, 5,'CONSIGNEE :','TL',0,'L',0);
	$this->Cell(150, 5,$parm['consignee_name'],'TR',0,'L',0);
	$this->Cell(30, 5,"INVOICE NO.:",'TL',0,'L',0);
	$this->Cell(65, 5,$parm['inv_num'],'TR',1,'L',0);

	$this->setX(5);
	$this->Cell(40, 5,'','L',0,'L',0);
	$this->Cell(150, 5,$str_csg[1],'R',0,'L',0);
	$this->Cell(30, 5,"DATE:",'TL',0,'L',0);
	$this->Cell(65, 5,$parm['date'],'TR',1,'L',0);

	$this->setX(5);
	$this->Cell(40, 5,'','L',0,'L',0);
	$this->Cell(150, 5,$str_csg[2],'R',0,'L',0);
	$this->Cell(30, 5,"Payment Mode:",'TL',0,'L',0);
	$this->Cell(20, 5,'TLC','T',0,'C',0);
	$this->Cell(20, 5,'TT','T',0,'C',0);
	$this->Cell(25, 5,'','TR',1,'L',0);
	
	$this->setX(5);
	$this->Cell(40, 5,'','L',0,'L',0);
	$this->Cell(150, 5,$str_csg[3],'R',0,'L',0);
	$this->Cell(30, 5,"",'L',0,'L',0);
	$this->Cell(20, 5,$parm['TLC'],'1',0,'C',0);
	$this->Cell(20, 5,$parm['TT'],'1',0,'C',0);
	$this->Cell(25, 5,'','R',1,'L',0);

	$this->setX(5);
	$this->Cell(40, 5,'','L',0,'L',0);
	$this->Cell(150, 5,$str_csg[4],'R',0,'L',0);
	$this->Cell(95, 5,"",'LR',1,'L',0);

	$this->setX(5);	
	$this->Cell(95, 5,$parm['ship_term'].' PORT :                 '.$parm['ship_from'],1,0,'L',0);
	$this->Cell(95, 5,'COUNTRY OF ORIGIN :              '.$parm['country_org'],1,0,'C',0);
	$this->Cell(95, 5,"   TO :          ".$parm['ship_to'],1,1,'L',0);

	for($i=0; $i<sizeof($parm['des']); $i++)
	{		
		$this->setX(5);
		$des_name  = '';
		if($i == 0)$des_name = 'Product Description :';
		$this->Cell(40, 5,$des_name,'L',0,'L',0);
		$this->SetFont('Arial','B',9);
		$this->Cell(245, 5,$parm['des'][$i],'R',1,'L',0);
	}
	$this->setX(5);
	$this->Cell(285, 5,'','LR',1,'L',0);
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
    $this->Cell(0,10,$print_title2.'                   Created by : '.$creator,0,0,'R');
}


// field module 1 [ªí¦J 1 ]


function ship_title($size,$po,$unit)
{
	//290
	$fld_size = 98 / sizeof($size);
	$this->setX(5);
	$this->SetFont('Arial','',9);
	$this->Cell(49,4,'PURCHASE ORDER : ',1,0,'C');
	$this->Cell(42,4,$po,1,0,'C');
	$this->Cell(194,4,'','R',1,'C');
	$this->setX(5);
	$this->Cell(19,4,'CTN NO','TLR',0,'C');
	$this->Cell(30,4,'STYLE #','TLR',0,'C');
	$this->Cell(12,4,'SUB','TLR',0,'C');
	$this->Cell(30,4,'COLOUR','TLR',0,'C');
	$this->Cell(98,4,'Assortment',1,0,'C');
	$this->Cell(18,4,'UNIT QTY',1,0,'C');
	$this->Cell(13,4,'TTL',1,0,'C');  // TTL CNTS
	$this->Cell(13,4,'TOTAL',1,0,'C');	// TTL UNITS
	$this->Cell(13,4,'N.W.',1,0,'C'); //N.W KGS
	$this->Cell(13,4,'G.W',1,0,'C');  //G.W KGS
	$this->Cell(26,4,'MEAS',1,1,'C');  //MEAS CM

	$this->setX(5);
	$this->Cell(19,4,'','BLR',0,'C'); 	//CTN NO
	$this->Cell(30,4,'','BLR',0,'C');		//STYLE #
	$this->Cell(12,4,'','BLR',0,'C');		//SUB
	$this->Cell(30,4,'','BLR',0,'C');		//COLOUR
	for($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,4,$size[$i],'TB',0,'C');
	$this->Cell(18,4,'PER CTN',1,0,'C');			//UNIT QTY per ctn
	$this->Cell(13,4,'CTNS',1,0,'C');  // TTL CNTS
	$this->Cell(13,4,'UNITS',1,0,'C');	// TTL UNITS
	$this->Cell(13,4,'KGS',1,0,'C'); //N.W KGS
	$this->Cell(13,4,'KGS',1,0,'C');  //G.W KGS
	$this->Cell(26,4,$unit,1,1,'C');  //MEAS CM

}


function ship_txt($parm,$style_num)
{
	//290
	$fld_size = 98 / sizeof($parm['size']);
	$this->SetFont('Arial','',7);
	$this->setX(5);
	$this->Cell(19,4,$parm['cnt'],'LR',0,'C'); 	//CTN NO
	$this->Cell(30,4,$style_num,'LR',0,'C');		//STYLE #
	$this->Cell(12,4,$parm['fit'],'LR',0,'C');		//SUB
	$this->Cell(30,4,$parm['color'],'LR',0,'C');		//COLOUR
	for($i=0; $i<sizeof($parm['size']); $i++)$this->Cell($fld_size,4,$parm['size_qty'][$i],0,0,'C');
	$this->Cell(18,4,$parm['s_qty'],'LR',0,'C');			//UNIT QTY per ctn
	$this->Cell(13,4,$parm['s_cnum'],'LR',0,'C');  // TTL CNTS
	$this->Cell(13,4,($parm['s_cnum'] * $parm['s_qty']),'LR',0,'C');	// TTL UNITS
	$this->Cell(13,4,number_format(($parm['s_cnum'] * $parm['nw']),2),'LR',0,'C'); //N.W KGS
	$this->Cell(13,4,number_format(($parm['s_cnum'] * $parm['gw']),2),'LR',0,'C');  //G.W KGS
	$this->Cell(26,4,$parm['ctn_l'].'x'.$parm['ctn_w'].'x'.$parm['ctn_h'],'LR',1,'C');  //MEAS CM
}

function gd_total($parm)
{
	$this->SetFont('Arial','B',8);
	$this->setX(5);
	$this->Cell(19,4,'',1,0,'C');
	$this->Cell(30,4,'',1,0,'C');
	$this->Cell(12,4,'',1,0,'C');
	$this->Cell(30,4,'',1,0,'C');
	$this->Cell(98,4,'GRAND TOTAL',1,0,'C');
	$this->Cell(18,4,'',1,0,'C');
	$this->Cell(13,4,number_format($parm['cnum']),1,0,'C');	// TTL CNTS
	$this->Cell(13,4,number_format($parm['qty']),1,0,'C');  // TTL UNITS
	$this->Cell(13,4,number_format($parm['nw'],2),1,0,'C'); //N.W KGS
	$this->Cell(13,4,number_format($parm['gw'],2),1,0,'C');  //G.W KGS
	$this->Cell(26,4,'',1,1,'C');  //MEAS CM
	
	$this->setX(5);
	$this->Cell(19,4,'','LR',0,'C');
	$this->Cell(30,4,'','LR',0,'C');
	$this->Cell(12,4,'','LR',0,'C');
	$this->Cell(30,4,'','LR',0,'C');
	$this->Cell(98,4,'','LR',0,'C');
	$this->Cell(18,4,'','LR',0,'C');
	$this->Cell(13,4,'','LR',0,'C');	// TTL CNTS
	$this->Cell(13,4,'','LR',0,'C');  // TTL UNITS
	$this->Cell(13,4,'N.W.','LR',0,'C'); //N.W KGS
	$this->Cell(13,4,'G.W.','LR',0,'C');  //G.W KGS
	$this->Cell(26,4,'','LR',1,'C');  //MEAS CM
	
	$this->setX(5);
	$this->Cell(19,4,'','BLR',0,'C');
	$this->Cell(30,4,'','BLR',0,'C');
	$this->Cell(12,4,'','BLR',0,'C');
	$this->Cell(30,4,'','BLR',0,'C');
	$this->Cell(98,4,'','BLR',0,'C');
	$this->Cell(18,4,'','BLR',0,'C');
	$this->Cell(13,4,'','BLR',0,'C');	// TTL CNTS
	$this->Cell(13,4,'UNITS','BLR',0,'C');  // TTL UNITS
	$this->Cell(13,4,'KGS','BLR',0,'C'); //N.W KGS
	$this->Cell(13,4,'KGS','BLR',0,'C');  //G.W KGS
	$this->Cell(26,4,'','BLR',1,'C');  //MEAS CM	
	$this->ln();
	$this->Cell(215,4,'TOTAL N.W :',0,0,'R');
	$this->Cell(26,4,number_format($parm['nw'],2),0,0,'R');
	$this->Cell(13,4,'KGS',0,1,'R');
	$this->Cell(215,4,'TOTAL G.W :',0,0,'R');
	$this->Cell(26,4,number_format($parm['gw'],2),0,0,'R');
	$this->Cell(13,4,'KGS',0,1,'R');	
	$this->Cell(215,4,'TOTAL MEASUREMENT :',0,0,'R');
	$this->Cell(26,4,number_format($parm['cbm'],2),0,0,'R');
	$this->Cell(13,4,$parm['unit'],0,1,'R');
	
}


function break_title($size)
{
	//290
	$fld_size = 110 / sizeof($size);
	$this->SetFont('Arial','B',9);
	
	$this->setX(5);
	$this->Cell(215,4,'SUMMARY PER SIZE',0,1,'C');	
	$this->setX(5);
	$this->Cell(75,4,'PO/COLOUR/TOTAL','TB',0,'C');
	for ($i=0; $i<sizeof($size); $i++)$this->Cell($fld_size,4,$size[$i],'TB',0,'C');	
	$this->Cell(30,4,'TOTAL','TB',1,'C');
	$this->ln();

}

function break_txt($parm,$po)
{
	//290
	$fld_size = 110 / sizeof($parm['size_qty']);
	$this->SetFont('Arial','',7);
	$this->setX(5);
	$this->Cell(20,4,$po,0,0,'R');
	$this->Cell(35,4,$parm['color'],0,0,'C');
	$this->Cell(20,4,$parm['fit'],0,0,'L');
	$this->SetFont('Arial','',8);
	for ($i=0; $i<sizeof($parm['size_qty']); $i++)
	{
		if($parm['size_qty'][$i] > 0)
		{
			$this->Cell($fld_size,4,NUMBER_FORMAT($parm['size_qty'][$i],0),0,0,'C');
		}else{
			$this->Cell($fld_size,4,'',0,0,'C');
		}
	}
	
	$this->Cell(20,4,NUMBER_FORMAT($parm['qty'],0).' PCS',0,1,'R');
}

function break_total($parm)
{
	//290
	$fld_size = 110 / sizeof($parm['size_qty']);
	$this->SetFont('Arial','',8);
	$this->SetX(5);
	$this->Cell(75,4,' ','T',0,'C');
	for ($i=0; $i<sizeof($parm['size_qty']); $i++)$this->Cell($fld_size,4,NUMBER_FORMAT($parm['size_qty'][$i],0),'T',0,'C');	
	$this->Cell(20,4,NUMBER_FORMAT($parm['qty'],0).' PCS','T',0,'R');
	$this->Cell(10,4,'','T',1,'R');
}

function ship_mark($main,$second,$side,$size)
{
		$this->SetFont('Arial','BU',9);
		
		$this->SetX(5);
		$this->Cell(70,12,'MAIN MARK',0,0,'L');
		$this->Cell(70,12,'SECONDARY MARK',0,0,'L');
		$this->Cell(70,12,'SIDE MARK',0,1,'L');
				
		$this->SetFont('Arial','',8);
		for($i=0; $i<$size; $i++)
		{
			$main_det = $second_det = $side_det = '';
			if(isset($main[$i]))$main_det = $main[$i];
			if(isset($second[$i]))$second_det = $second[$i];
			if(isset($side[$i]))$side_det = $side[$i];
			
			$this->SetX(5);
			$this->Cell(70,4,$main_det,0,0,'L');
			$this->Cell(70,4,$second_det,0,0,'L');
			$this->Cell(70,4,$side_det,0,1,'L');
		}

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
