<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_pack_mn extends PDF_Chinese 	{


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



  $this->SetFont('Arial','B',14);
	$this->SetXY(10,5);
	$yy = 5;
	$this->Cell(185, 25,'Big M, Inc. Packing List','LB',0,'C');
	$xx = $this->GetX();	

	$m_chk = '';
	if($main_rec['method'] == 'Garments on Hangers(GOH)') $m_chk = 'X';
	$this->SetFont('Arial','',8);
	$this->cell(8, 4,$m_chk,1,0,'C');
	$this->cell(90, 4,'Garments on Hangers(GOH)','R',0,'L');
	$this->ln();
	$this->SetX($xx);
	$this->cell(98, 2,'','R',0,'C');
	$this->ln();
	$this->SetX($xx);

	$m_chk = '';
	if($main_rec['method'] == 'Carton to Hang(CTH)') $m_chk = 'X';	
	$this->cell(8, 4,$m_chk,1,0,'C');
	$this->cell(90, 4,'Carton to Hang(CTH)','R',0,'L');
	$this->ln();
	$this->SetX($xx);
	$this->cell(98, 2,'','R',0,'C');
	$this->ln();
	$this->SetX($xx);
	
	$m_chk = '';
	if($main_rec['method'] == 'Flat Pack') $m_chk = 'X';	
	$this->cell(8, 4,$m_chk,1,0,'C');
	$this->cell(90, 4,'Flat Pack','R',0,'L');
	$this->ln();
	$this->SetX($xx);
	$this->cell(98, 2,'','R',0,'C');
	$this->ln();
	$this->SetX($xx);
	$m_chk = '';
	if($main_rec['method'] == 'Pre-Ticketed') $m_chk = 'X';	
	$this->cell(8, 4,$m_chk,1,0,'L');
	$this->cell(90, 4,'Pre-Ticketed','R',0,'L');
	$this->ln();
	$this->SetX($xx);
	$this->cell(98, 3,'','BR',0,'C');
	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(100,150,$mark,30);

	$yy = $this->GetY();
	$this->SetY(($yy+3));
	
}

function hend_title($parm)
{
	$tmp = explode('-',$parm['ship_date']);

	if($tmp[1] == '00')
	{
		$parm['ship_date'] = '';
	}else{
		$parm['ship_date'] = $tmp[2]."-".$GLOBALS['MM2'][$tmp[1]];
	}

	$this->SetFont('Arial','B',10);
	$this->Cell(180, 5,"(One P.O. per packing list: use multiple forms, if needed)",'TLR',0,'L',0);
	$this->Cell(103, 5,"Purchase ORDER NUMBER",'TLR',0,'L',0);	
	$this->ln();
	$this->SetFont('Arial','',10);
	$this->Cell(180, 5,"",'BLR',0,'L',0);
	$this->Cell(103, 5,$parm['cust_po'],'BLR',0,'L',0);	
	$this->ln();

	$this->SetFont('Arial','B',10);
	$this->Cell(180, 5,"VENDOR NAME : ",'TLR',0,'L',0);
	$this->Cell(103, 5,"CARRIER : ",'TLR',0,'L',0);	
	$this->ln();
	$this->SetFont('Arial','',10);
	$this->Cell(180, 5,$parm['vendor'],'BLR',0,'L',0);
	$this->Cell(103, 5,$parm['carrier'],'BLR',0,'L',0);	
	$this->ln();
	
	$this->SetFont('Arial','B',10);
	$this->Cell(80, 5,"START SHIP DATD ",'TLR',0,'L',0);
	$this->Cell(100, 5,"CANCEL DATE",'TLR',0,'L',0);	
	$this->Cell(103, 5,'ANTICIPATED DELIVERY DATE','TLR',0,'L',0);	
	$this->ln();
	$this->SetFont('Arial','',10);
	$this->Cell(80, 5,$parm['ship_date'],'BLR',0,'C',0);
	$this->Cell(100, 5,'','BLR',0,'L',0);
	$this->Cell(103, 5,"    ".$parm['ship_date'],'BLR',0,'L',0);	
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
	global $foot;
	$inv = explode('-',$foot['inv_num']);
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('BIG5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
  $this->Cell(50,10,date('Y/m/d h:i A'),0,0,'C');
  $this->Cell(180,10,$foot['po'],0,0,'C');
  $this->Cell(50,10,$inv[0]." ".$foot['cust']." sea ".$foot['ver']."-".$this->PageNo(),0,0,'C');
}


// field module 1 [ªí¦J 1 ]


function item_title()
{
	//290
	
	$this->SetFont('Arial','B',8);


	$this->Cell(25,4,'','TLR',0,'C');
	$this->Cell(25,4,'','TLR',0,'C');
	$this->Cell(30,4,'BIGM COLOR','TLR',0,'C');
	$this->Cell(100,4,'','TRL',0,'C');
	$this->Cell(20,4,'','TLR',0,'C');
	$this->Cell(21,4,'','TLR',0,'C');
	$this->Cell(20,4,'','TLR',0,'C');
	$this->Cell(21,4,'','TLR',0,'C');
	$this->Cell(21,4,'','TLR',0,'C');
	$this->ln();
	
	$this->Cell(25,4,'MFG STYLE','LR',0,'C');
	$this->Cell(25,4,'BIG M STYLE','LR',0,'C');
	$this->Cell(30,4,'Code/Name','LR',0,'C');
	$this->Cell(100,4,'SIZE BREAKDOWN    All Department','RL',0,'C');
	$this->Cell(20,4,'#of Pre-','LR',0,'C');
	$this->Cell(21,4,'#of','LR',0,'C');
	$this->Cell(20,4,'#of','LR',0,'C');
	$this->Cell(21,4,'','LR',0,'C');
	$this->Cell(21,4,'','LR',0,'C');
	$this->ln();
	
	$this->Cell(25,4,'NUMBER','BLR',0,'C');
	$this->Cell(25,4,'NUMBER','BLR',0,'C');
	$this->Cell(30,4,'(e.g., 03- Black)','BLR',0,'C');
	$this->Cell(100,4,'per pre-pack(if applicable)','BRL',0,'C');
	$this->Cell(20,4,'packs','BLR',0,'C');
	$this->Cell(21,4,'Pieces','BLR',0,'C');
	$this->Cell(20,4,'Cartons','BLR',0,'C');
	$this->Cell(21,4,'N.W.','BLR',0,'C');
	$this->Cell(21,4,'G.W.','BLR',0,'C');
	$this->ln();
}


function item_txt($parm,$cbm)
{
	//290
	$cust_po = $color = ''; 
	$x = 9;
	$qty = $ctn =$nw =$gw = 0;
	for($i=0; $i<sizeof($parm); $i++)
	{
		
		$x++;
		$fld_size = 100 / sizeof($parm[$i]['size']);
		$cnum = $parm[$i]['e_cnt'] - $parm[$i]['s_cnt'] +1;
		$s_qty = $cnum * $parm[$i]['qty'];
		
		$qty += $s_qty;
		$ctn += $cnum;
		$nw  += ($parm[$i]['nw'] * $cnum);
		$gw  += ($parm[$i]['gw'] * $cnum);
		
		$po_mk = 0;
		$cl1 = $cl2 = 'BLR';
		if($x < 30)
		{
			if(isset($parm[($i+1)]['style_num']) && $parm[($i)]['style_num'] == $parm[($i+1)]['style_num'])
			{
				$cl1 ='LR';
				$parm[$i]['style_num'] = '';
				if(isset($parm[($i+1)]['color']) && $parm[($i)]['color'] == $parm[($i+1)]['color'])
				{
			 		 $cl2 ='LR';	
					 $parm[$i]['color']	='';		
				}			
			}
		}
			$this->Cell(25,5,'',$cl1,0,'C');
			$this->Cell(25,5,$parm[$i]['style_num'],$cl1,0,'C');
			$this->Cell(30,5,$parm[$i]['color'],$cl2,0,'C');
			for($j=0; $j<sizeof($parm[$i]['size_qty']); $j++)
			{
				$this->Cell($fld_size,5,$parm[$i]['size_qty'][$j],1,0,'R');
			}
			if($parm[$i]['f_mk'] == 1)
			{
				$this->Cell(20,5,NUMBER_FORMAT($parm[$i]['qty'],0),'TLR',0,'R');
				$this->Cell(21,5,NUMBER_FORMAT($s_qty,0),'TLR',0,'R');
				$this->Cell(20,5,NUMBER_FORMAT($cnum,0),'TLR',0,'R');
				$this->Cell(21,5,NUMBER_FORMAT(($parm[$i]['ctn_nw'] *$parm[$i]['cnum']),2),'TLR',0,'R');
				$this->Cell(21,5,NUMBER_FORMAT(($parm[$i]['ctn_gw'] *$parm[$i]['cnum']),2),'TLR',0,'R');
			}else{
				$this->Cell(20,5,'','LR',0,'R');
				$this->Cell(21,5,'','LR',0,'R');
				$this->Cell(20,5,'','LR',0,'R');
				$this->Cell(21,5,'','LR',0,'R');
				$this->Cell(21,5,'','LR',0,'R');
				
			}
			$this->ln();

		 if($x >= 30)
		 {
		 		$this->AddPage();
		 		$this->item_title();
		 		$x = 0;
		 }
	}
			$this->Cell(50,5,'carton dimension',1,0,'C');
			$this->Cell(130,5,$cbm,1,0,'C');

			$this->Cell(20,5,'',1,0,'C');
			$this->Cell(21,5,NUMBER_FORMAT($qty,0),1,0,'R');
			$this->Cell(20,5,NUMBER_FORMAT($ctn,0),1,0,'R');
			$this->Cell(21,5,NUMBER_FORMAT($nw,2),1,0,'R');
			$this->Cell(21,5,NUMBER_FORMAT($gw,2),1,0,'R');
			$this->ln();
			
			$this->Cell(180,5,'',0,0,'C');
			$this->Cell(20,5,'Total',0,0,'C');
			$this->Cell(21,5,'Total',0,0,'C');
			$this->Cell(20,5,'Total',0,0,'C');
			$this->Cell(21,5,'Total',0,0,'C');
			$this->Cell(21,5,'Total',0,0,'C');
			$this->ln();			
	
		$x += 2;
		return $x;
}

function break_item($parm,$ship_mk,$x)
{
	//290
	$ship_mk = explode(chr(13).chr(10),$ship_mk);
	for($i=0; $i<sizeof($parm); $i++)
	{
		$x++;
		$pg_mk = 0;
		$fld_size = 90 / sizeof($parm[$i]['size']);
		if($x >= 30)
		{
		 	$this->AddPage();
			$this->SetFont('Arial','B',9);
			$this->Cell(60,4,'COLOR/SIZE BREAKDOWN :',1,0,'C');
			for ($j=0; $j<sizeof($parm[$i]['size']); $j++)$this->Cell($fld_size,4,$parm[$i]['size'][$j],1,0,'C');	
			$this->Cell(15,4,'',1,0,'C');
			$this->Cell(10,4,'',0,0,'C');
			$this->Cell(30,4,'SHIPPING MARK : ',0,0,'L');
			$this->ln();		 		
			$x = 0;			
		}
		if($parm[$i]['tb_mk'] == 1 || $parm[$i]['tb_mk'] == 2 || $parm[$i]['tb_mk'] == 5)
		{
			$this->SetFont('Arial','B',9);
			$this->Cell(60,4,'COLOR/SIZE BREAKDOWN :',1,0,'C');
			for ($j=0; $j<sizeof($parm[$i]['size']); $j++)$this->Cell($fld_size,4,$parm[$i]['size'][$j],1,0,'C');	
			$this->Cell(15,4,'',1,0,'C');
			$this->Cell(10,4,'',0,0,'C');
			if($i == 0)$this->Cell(30,4,'SHIPPING MARK : ',0,0,'L');
			$this->ln();
			$x++;
		}
		$this->SetFont('Arial','',8);
		$this->Cell(25,4,$parm[$i]['style_num'],1,0,'C');
		$this->Cell(35,4,$parm[$i]['color'],1,0,'C');
		for ($j=0; $j<sizeof($parm[$i]['qty']); $j++)$this->Cell($fld_size,4,$parm[$i]['qty'][$j],1,0,'R');	
		$this->Cell(15,4,$parm[$i]['ttl_qty'],1,0,'R');	
		$this->Cell(10,4,'',0,0,'C');
		if(isset($ship_mk[$i]))$this->Cell(30,4,$ship_mk[$i],0,0,'L');		
		$this->ln();
		
		if($parm[$i]['tb_mk'] == 3 || $parm[$i]['tb_mk'] == 5)
		{
			$this->SetFont('Arial','B',9);
			$this->Cell(60,4,'Total',1,0,'C');
			for ($j=0; $j<sizeof($parm[$i]['g_size_qty']); $j++)$this->Cell($fld_size,4,$parm[$i]['g_size_qty'][$j],1,0,'R');	
			$this->Cell(15,4,$parm[$i]['g_qty'],1,0,'R');
			$this->ln();
			$x++;
		}				
	}
	if($i < sizeof($ship_mk))
	{
		$yy = $this->GetY();
		$this->SetY($yy-4);
		$x--;
		for($j=($i); $j<sizeof($ship_mk); $j++)
		{
			$x++;
			if($x >= 30)
			{
		 		$this->AddPage();
				$this->SetFont('Arial','B',9);
				$this->Cell(175,4,'',0,0,'L');
				$this->Cell(30,4,'SHIPPING MARK : ',0,0,'L');
				$this->ln();		 		
				$x = 0;			
			}			
			$this->SetFont('Arial','',8);
			$this->Cell(175,4,'',0,0,'L');
			$this->Cell(30,4,$ship_mk[$j],0,0,'L');
			$this->ln();
			
		}
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

}  // end of class

?>
