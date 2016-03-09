<?php
include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_bom extends PDF_Chinese 	{


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
	global $PHP_id;
	global $smpl;

	$dt = decode_date(1);
    //Put watermark
    $this->SetFont('Arial','B',50);
    $this->SetTextColor(230,210,210);
    $this->RotatedText(60,210,$PHP_id,30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title
	$this->SetXY(100,15);
	$this->SetTextColor(40,40,40);
    $this->SetFont('Arial','I',9);
	$this->Cell(40, 7,$print_title2,0,0,'C',0);
	$this->SetFont('Arial','B',14);
	$this->Cell(60, 7,$print_title,1,1,'C',0);
	
	$this->SetLineWidth(1);
	$this->SetXY(177,3);
	$this->Cell(30, 7,$smpl['smpl_type'],1,1,'C',0);
	//date
//    $this->SetFont('Arial','I',9);
	$this->SetXY(180,18);
//	$this->Cell(20, 7,$dt['date'],0,1,'C',0);
	//line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,200,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,200,26);
	$this->ln(5);

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
    $this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                          Created by : '.$creator,0,0,'C');
}


// field module 1 [表匡 1 ]
function filed_1($x,$y,$w,$h,$h2,$title,$color,$field) {
   $this->SetLineWidth(.3);

	$this->SetFont('Big5','',10);
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
	$this->SetFont('Arial','IB',10);
	$this->SetXY($x,$y+$h2);
	$this->Cell($w, $h-$h2,$field,'B',0,'C');

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
function ti_name_fields($name) {
//	$this->SetFillColor(177,176,154);
//	$this->SetTextColor(0);
	$this->setx(10);
    $this->SetLineWidth(.5);

    $this->SetFont('Arial','B');
	$this->Cell(20,8,$name,'LTB',0,'L');
	$this->Cell(170,8,'','RTB',0,'R');
	$this->ln();
}

function ti_value_fields($value) {
	$this->SetTextColor(0);
	$y=0;
  $this->SetFont('Big5');
  $tmp=explode('<br>',$value);
    for ($i=0; $i<sizeof($tmp); $i++)
    {
			$l1=0;
			$l2=54;
			$cut = 0;
				$str_long = mb_strlen($tmp[$i],'big-5');
				while ($str_long > 1)
				{
	//				echo $str_long;
					$str_long = $str_long / 54;
					
					$cut ++; 
				}
				for ($z=0; $z < $cut; $z++)
				{
					$tmp_ti = mb_substr($tmp[$i],$l1,54,'big-5'); 
					
					$tmp_ti = str_replace("&#33031;","脅",$tmp_ti);
					$this->setx(10);
					$this->Cell(5,5,'','L',0,'R');
					$this->Cell(185,5,$tmp_ti,'R',0,'L');
					$this->ln();
					$y++;
					$l1=$l2;
					$l2=$l2+54;
					
				}
				
	}
	return $y;
}



//Colored table
function Table_1($x,$y,$header,$data,$w,$title,$R,$G,$B,$base_size='') {
    //Colors, line width and bold font
//   $this->SetFillColor($R,$G,$B);
//   $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);
    $this->SetXY($x,$y);
    // Header
	$this->Cell(array_sum($w),7,$title,0,0,'C');
    $this->Ln();
    $this->SetFont('','B');
	$this->SetX($x);
	$this->SetFont('Arial','B',10);
    for($i=0;$i<count($header);$i++)
    {
        if($header[$i] == $base_size)
        {
        	$this->SetFont('Arial','BI',10);
        	$this->Cell($w[$i],7,'['.$header[$i].']',1,0,'C');
        }else{
        	$this->SetFont('Arial','B',10);
        	$this->Cell($w[$i],7,$header[$i],1,0,'C');
    	}
        
    }
    $this->Cell(($w[$i]+3),7,'SUM',1,0,'C');
    $this->Ln();
   	$this->SetX($x);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],0.5,'',1,0,'C');
    $this->Cell(($w[$i]+3),0.5,'',1,0,'C');

    //Color and font restoration
    $this->SetTextColor(0);
    $this->SetFont('');
    $this->SetLineWidth(.3);
    // Data
	$tmp=0;
	$this->SetFont('Arial','',10);
    foreach($data as $row)  {
		    $this->SetX($x);
		for($i=0;$i<count($header);$i++){
			if ($i == 0)
			{
				if (strlen($row[$i]) > 18)
				{
					$this->SetFont('big5','',6);
				}else if(strlen($row[$i]) > 8){
					$this->SetFont('big5','',8);
				}else{
					$this->SetFont('big5','',10);
				}
			}else{
				$this->SetFont('Arial','',10);
			}
			
			$this->Cell($w[$i],6,$row[$i],1,0,'C',0);
			if ($i > 0)$tmp=$tmp+$row[$i];
		}		
		$this->Cell(($w[$i]+3),6,$tmp,1,0,'R',0);
		$tmp=0;
	$this->Ln();
    }
//    $this->Cell(array_sum($w),0,'','T');
}

//Colored table [表頭 再分 row ]  --- 主副料 table
function Table_title($header1,$w1,$title,$R,$G,$B) {
    //Colors, line width and bold font
    $this->SetFillColor($R,$G,$B);
    $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);

    // Header
    $this->SetTextColor(0);
	$this->Cell(array_sum($w1),7,$title,0,0,'C');
    $this->Ln();
    $this->SetTextColor(255);

    for($i=0;$i<count($header1);$i++)
        $this->Cell($w1[$i],8,$header1[$i],1,0,'C',1);
    $this->Ln();

}

//Colored table [表頭 再分 row ]  --- 主副料 table
function Table_value($data,$w1,$w2) {
    $this->SetDrawColor(100,100,100);
    $this->SetLineWidth(.3);
    $this->SetTextColor(0);

    for($i=0;$i<count($w1);$i++)
    {
    	if ($w2[$i]=='est_1')
    	{
    		$this->Cell($w1[$i],5,$data[$w2[$i]]."/".$data['unit'],1,0,'R');    		
    	}else{
    		$this->Cell($w1[$i],5,$data[$w2[$i]],1,0,'L');
    	}
        
    }
     $this->Ln();
}
function hend_title($PHP_id,$wi,$smpl,$total,$prt_ord,$print='')
{

	global $stype;
	global $des_fab;
	
	$this->filed_1(70,28,35,15,7,$prt_ord,'80,110,140',$PHP_id);
	$this->filed_1(106,28,45,15,7,'Customer','80,110,140',$smpl['cust_iname']);
	$this->filed_1(152,28,48,15,7,'Customer ref.','80,110,140',$smpl['ref']);

	$this->filed_1(70,45,25,15,7,'Style cat.','80,110,140',$smpl['style_cat']);
	$this->filed_1(96,45,15,15,7,'Dept.','80,110,140',$smpl['dept']);
	$this->filed_1(112,45,48,15,7,'Size / Style','80,110,140',$smpl['size_scale'].' /  '.$smpl['style']);
	$this->filed_1(161,45,15,15,7,'Q\'ty','80,110,140',$total.' '.$wi['unit']);
	$this->filed_1(177,45,23,15,7,'ETD','80,110,140',$wi['etd']);

	$this->ln();
	$this->ln(2);
	
	$this->Cell(60,6,'',0,0,'');
	$this->SetFont('Arial','',9);
	$this->Cell(18,6,'Order #',1,0,'C');	
	$this->Cell(.5,6,'',1,0,'');
	$this->SetFont('Arial','B',9);	
	$this->Cell(111.5,6,$smpl['orders'],1,0,'L');
	
	$this->ln();
	$this->ln(2);
	
	$this->Cell(60,6,'',0,0,'');
	$this->SetFont('Big5','B',8);
	$this->Cell(18,6,'GARMENT FINISH : '.$stype,0,0,'L');	
	
	$this->ln(4);
	
	$this->Cell(60,6,'',0,0,'');
	$this->SetFont('Big5','B',8);
	$this->Cell(18,6,'面布 ( Shell ) : '.$des_fab,0,0,'L');	
	
	$this->ln();
	$this->ln(2);
	//$pdf->filed_1(109,44,32,15,7,'Order type','80,110,140',$wi['smpl_type']);
}
function Table_acc_title($title){
	$this->SetDrawColor(100,100,100);
  $this->SetFillColor(100,85,85);
  $this->SetLineWidth(.3);
	$this->SetFont('Arial','B',10);
  $this->setx(10); 
	$this->cell(40,5,'ACC. #',1,0,'C');
	$this->cell(125,5,'Placement',1,0,'C');	
	$this->cell(27,5,'Consum./Unit ',1,0,'C');
	$this->ln();
  $this->setx(10); 
	$this->cell(40,0.5,'',1,0,'C');
	$this->cell(115,0.5,'',1,0,'C');	
	$this->cell(27,0.5,' ',1,0,'C');

	$this->SetTextColor(0);
	
}
function Table_acc($acc,$num){
	$num++;
  $this->setx(10); 
	$this->SetFont('Arial','B',8);	
	$this->cell(5,5,$num,1,0,'C');
	$this->SetFont('Big5','',8);
//	$this->SetDrawColor(100,100,100);
//  $this->SetFillColor(100,85,85);
//  $this->SetLineWidth(.3);
//  $this->SetTextColor(0);	
	$this->cell(35,5,$acc['acc_code'],1,0,'L');
	$this->cell(125,5,$acc['use_for'],1,0,'L');
	$this->cell(27,5,$acc['est_1']."/".$acc['unit'],1,0,'R');	
	$this->ln();
  $this->setx(10); 

	$this->SetFont('Arial','B',10);	
	if ($acc['acc_name'] == 'other')
	{
		$acc_name='OTHER ACCESSORY : ';
		$this->cell(40,5,$acc_name,'TBL',0,'L');
		$i=40;
	}else{
		$acc_name=$acc['acc_name'].':';
//		$acc_name=strtoupper($acc_name);
		$this->cell(30,5,$acc_name,'TBL',0,'L');
		$i=30;
		
	}
	$this->SetFont('Arial','B',8);	
	$long=167-$i;
	$this->cell(25,5,'Construction : ','TB',0,'R');
	$this->SetFont('Big5','',8);
	//$long=190-
	$this->cell($long,5,$acc['specify'],'TBR',0,'L');
	
}
//fabric用表格
function Table_fab_title() {
	$this->setx(10); 
	$this->SetFont('Arial','B',10);	
	$this->cell(25,5,'Fabric # ',1,0,'C');
	$this->cell(35,5,'Fabric Name ',1,0,'C');
	$this->cell(105,5,'Placement ',1,0,'C');
	$this->cell(27,5,'Consum./Unit ',1,0,'C');
	$this->ln();	
	$this->setx(10); 
	$this->cell(25,0.5,'',1,0,'C');
	$this->cell(35,0.5,'',1,0,'C');
	$this->cell(105,0.5,'',1,0,'C');	
	$this->cell(27,0.5,' ',1,0,'C');
	$this->ln();

}
function Table_fab($data,$num) {
	$tmp_str='';
	$num++;
	$long = 0;
	$x=$y=0;
    $this->SetDrawColor(100,100,100);
    $this->SetFillColor(100,85,85);
    $this->SetLineWidth(.3);
    $this->setx(10); 
	$this->SetFont('Arial','B',8);	
	$this->cell(5,5,$num,1,0,'C');
	$this->SetFont('Big5','',7);	

	$data['width'] = str_replace("'","’",$data['width']);
	$data['weight'] = str_replace("'","’",$data['weight']);

	$this->cell(20,5,$data['lots_code'],1,0,'L');
	$this->cell(35,5,$data['lots_name'],1,0,'L');
	$this->cell(105,5,$data['use_for'],1,0,'L');
	$this->cell(27,5,$data['est_1']."/".$data['unit'],1,0,'R');
	$this->ln();
	$this->setx(10); 
	if ($data['des']) $x++;
	if ($data['comp']) $x++;
//	if ($data['specify']) $x++;		
	if ($x > 0) $long=170 / $x;	
	for ($i=0; $i<$x; $i++)	$longs[$i]=$long;
	$longs[$x]=170-($long*$i);
	
	$this->cell(0.5,5,'','TBL',0,'C');
	$this->SetFont('Arial','B',8);	
	
	

	if ($data['weight'])
	{
		$this->SetFont('Arial','B',8);	
		$this->cell(10,5,'weight : ','TB',0,'C');
		$this->SetFont('Big5','',8);
		$this->cell(11,5,$data['weight'],'TB',0,'L');
	}else{
		$this->SetFont('Arial','B',8);	
		$this->cell(11,5,'c.width : ','TB',0,'C');
		$this->SetFont('Big5','',8);
		$this->cell(10,5,$data['width'],'TB',0,'L');

	}

	if ($data['comp'])
	{
		$this->SetFont('Arial','B',8);	
		$this->cell(12,5,'Content : ','TB',0,'C');
		$this->SetFont('Big5','',8);
		$this->cell(($longs[$y]-12),5,$data['comp'],'TB',0,'L');
		$y++;
	}

/*
	if ($data['specify'])
	{
		$this->SetFont('Arial','B',8);	
		$this->cell(18,5,'construction : ','TB',0,'C');
		$this->SetFont('Big5','',8);		
		$this->cell(($longs[$y]-18),5,$data['specify'],'TB',0,'L');
		$y++;
	}
*/
	
	if ($data['des'])
	{
		$this->SetFont('Arial','B',8);	
		$this->cell(10,5,"Supplier's # : ",'TB',0,'C');
		$this->SetFont('Big5','',8);		
		$this->cell(($longs[$y]-10),5,$data['des'],'TB',0,'L');
		$y++;
	}


	if ($x == 0)
	{
		$this->cell(170,5,' ','TB',0,'C');
	}
	$this->cell(0.5,5,'','TBR',0,'C');
     $this->Ln();
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
