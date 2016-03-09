<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shidoc_bill extends PDF_Chinese 	{


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
	global $mark;

// marker		
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,150,$mark,30);
	//title
	$this->SetXY(90,10);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','B',14);
	$this->Cell(90, 7,$print_title,0,0,'L',0);

  $this->SetFont('Arial','I',9);
	$this->Cell(30, 7,'Page '.$this->PageNo().' / <nb>',0,0,'C',0);
	//date
	$this->SetXY(180,12);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,18,200,18);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,19,200,19);
	$this->ln(5);
	$this->SetXY(10,21);

}

function base_data($shp)
{
	$tmp = explode(' ',$GLOBALS['SHIP_TO'][$shp['fty']]['Addr']);
	$fty1 = $fty2 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($fty1)+strlen($tmp[$i]);
		$len2 = strlen($fty2)+strlen($tmp[$i]);
		if($len1 < 100 && strlen($fty2) <= 1)
		{
			$fty1 = $fty1.$tmp[$i]." ";		
		}else{
			$fty2 = $fty2.$tmp[$i]." ";
		}
	}

	$this->SetFont('Arial','BU',10);
	$this->Cell(155, 7,"SHIPPER / EXPORTER",'TLR',0,'L',0);
	$this->ln();
	$this->SetFont('Arial','',9);
	$this->Cell(155, 5,$GLOBALS['SHIP_TO'][$shp['fty']]['Name'],'LR',0,'L',0);
	$this->ln();
	$this->Cell(155, 5,$fty1,'LR',0,'L',0);
	$this->ln();
  if($fty2 != '')	
  {
		$this->Cell(155, 5,$fty2,'LR',0,'L',0);
		$this->ln();
  }

	$this->Cell(155, 0.5,'','BLR',0,'L',0);
	$this->ln();
	$yy = $this->GetY();
	$this->SetY(($yy+5));
	
	$this->SetFont('Arial','BU',10);
	$this->Cell(155, 7,"CONSIGNEE",'TLR',0,'L',0);
	$this->ln();
	$this->SetFont('Arial','',9);
	$this->Cell(155, 5,$shp['consignee_name'],'BLR',0,'L',0);
	$this->ln();		
	$yy = $this->GetY();
	$this->SetY(($yy+5));
	
	$cust1 = $cust2 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($cust1)+strlen($tmp[$i]);
		$len2 = strlen($cust2)+strlen($tmp[$i]);
		if($len1 < 100 && strlen($cust2) <= 1)
		{
			$cust1 = $cust1.$tmp[$i]." ";		
		}else{
			$cust2 = $cust2.$tmp[$i]." ";
		}
	}	
	$this->SetFont('Arial','BU',10);
	$this->Cell(155, 7,"NOTIFY PARTY#",1,0,'L',0);
	$this->ln();	
	$this->SetFont('BIG5','',9);
	$this->Cell(155, 5,"TO ORDER OF ".$shp['cust_name'],'LR',0,'L',0);
	$this->ln();	
	$this->SetFont('Arial','',9);
	$this->Cell(155, 5,$cust1,'LR',0,'L',0);
	$this->ln();
  if($fty2 != '')	
  {
		$this->Cell(155, 5,$cust2,'LR',0,'L',0);
		$this->ln();
  }
	$this->Cell(155, 0.5,'','BLR',0,'L',0);
	$this->ln();
	$yy = $this->GetY();
	$this->SetY(($yy+5));

	$this->Cell(55, 5,'PORT OF LOADING : ',0,0,'L',0);
	if($shp['fty'] == 'LY')
	{
		$this->Cell(100, 5,'HOCHIMINH, VIETNAM',0,0,'L',0);
	}else{
		$this->Cell(100, 5,'SHANGHAI CHINA',0,0,'L',0);	
	}
	$this->ln();

	$this->Cell(55, 5,'PORT OF DISCHARGE : ',0,0,'L',0);
	$this->Cell(100, 5,$shp['dis_port'],0,0,'L',0);	
	$this->ln();
	$this->Cell(55, 5,'PORT OF DELIVERY : ',0,0,'L',0);
	$this->ln();
	$yy = $this->GetY();
	$this->SetY(($yy+5));
		
	$this->SetFont('Arial','BU',10);
	$this->Cell(60, 5,'DESCRIPTION OF GOODS : ',0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(50, 5,'invoice no : ',0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(50, 5,$shp['inv_num'],0,0,'L',0);
	$this->ln();		
	$this->Cell(60, 5,$shp['des'],0,0,'L',0);
	$this->Cell(50, 5,'L/C# : '.$shp['lc_no'],0,0,'L',0);
	$this->SetFont('Arial','',9);
	$this->Cell(50, 5,'DATED : '.$shp['lc_date'],0,0,'L',0);
	$this->ln();


}



function item_txt($parm,$i)
{
		$this->SetFont('Arial','',8);
		$this->Cell(195, 4,$parm['des'],0,0,'L',0);
		$this->ln();
		$this->Cell(15, 4,'CONTENT: ',0,0,'L',0);
		$this->Cell(180, 4,$parm['content'],0,0,'L',0);
		$this->ln();
		if($i == 0)
		{
			$this->SetFont('Arial','UB',8);
			$this->Cell(10, 5,'',0,0,'C',0);
			$this->Cell(40, 5,'P.O. NO.',0,0,'C',0);
			$this->Cell(40, 5,'STYLE NO.',0,0,'C',0);
			$this->Cell(40, 5,'COLOR',0,0,'C',0);	
			$this->Cell(40, 5,'QUATITY',0,0,'C',0);		
			$this->ln();
		}
		$this->SetFont('Arial','',8);
		$this->Cell(10, 5,'',0,0,'C',0);
		$this->Cell(40, 5,$parm['cust_po'],0,0,'C',0);
		$this->Cell(40, 5,$parm['style_num'],0,0,'C',0);
		$this->Cell(40, 5,$parm['color'],0,0,'C',0);		
		$this->Cell(30, 5,NUMBER_FORMAT($parm['bk_qty'],0),0,0,'R',0);	
		$this->Cell(10, 5,'Pcs',0,0,'R',0);
		$this->ln();
		

}

function item_total($parm)
{
		$vl1 = $vl2 = '';
		$this->SetFont('Arial','B',8);
		$this->Cell(130,0.5,'',0,0,'L',0);
		$this->Cell(40,1,'','TB',0,'C',0);
		$this->ln();
		$this->Cell(130, 4,'',0,0,'R',0);
		$this->Cell(30, 4,NUMBER_FORMAT($parm['qty'],0),0,0,'R',0);	
		$this->Cell(10, 4,'PCS',0,0,'R',0);	
		$this->ln();
		$str = "TOTAL : ".NUMBER_FORMAT($parm['qty'],0)."PCS = ".NUMBER_FORMAT($parm['ctn'],0)." CTNS = ";
		$str.= NUMBER_FORMAT($parm['gw'],2)." KGS(G.W) = ".NUMBER_FORMAT($parm['cbm'],2)." CBM";
		$this->Cell(170, 4,$str,0,0,'L',0);	
		$this->ln();		
		$yy = $this->GetY();
		$this->SetY(($yy+5));
		$this->Cell(70, 4,'FREIGHT : COLLECT',0,0,'L',0);	
		$this->Cell(100, 4,'CONTAINER # : '.$parm['ctn_num'],0,0,'L',0);	
		$this->ln();
		$this->Cell(70, 4,'CLEAN ON BOARD : '.$parm['ship_date'],0,0,'L',0);	
		$this->Cell(100, 4,'SEAL # : '.$parm['seal'],0,0,'L',0);	
		$this->ln();
		$yy = $this->GetY();
		$this->SetY(($yy+5));
		$this->SetFont('Arial','BU',8);
		$this->Cell(170, 4,'SHIPPING MARKS : ',0,0,'L',0);	
		$this->ln();
		$this->SetFont('Arial','',8);
		$this->Cell(170, 4,'SAME AS INVOICE ',0,0,'L',0);	
		
		
}
function inv_txt($title,$txt)
{
	$this->SetFont('Arial','B',8);
	$this->Cell(175,7,$title,0,0,'L',0);
	$this->ln();
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
    $this->Cell(0,10,$print_title2.'                   Created by : '.$creator,0,0,'R');
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
