<?php
include_once($config['root_dir']."/lib/chinese.php");
define('FPDF_FONTPATH','font/');

class PDF_marker extends PDF_Chinese {

	var $B=0;
	var $I=0;
	var $U=0;
	var $HREF='';
	var $ALIGN='';
	var $angle=0;


//Page header
function Header() {
	global $op,$print_title,$PHP_id,$print_title2;

	$dt = decode_date(1);

  // Put watermark
  $this->SetFont('Arial','B',50);
  $this->SetTextColor(230,210,210);
  $this->RotatedText($_SESSION['PDF']['w']/2-50,$_SESSION['PDF']['h']/2+20,$op['mk_name'].'_'.$op['fab_type'].$op['mk']['combo'],30);

	// Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);

	// title
	$this->SetFont('Arial','I',9);
	$w1 = $this->GetStringWidth($print_title2)+20;
	$this->SetFont('Arial','B',14);
	$w2 = $this->GetStringWidth($print_title)+20;
	$w = $_SESSION['PDF']['w'] - ( $w2 + $w1 )-50;

	// line
	$this->SetXY($w,15);
	$this->SetTextColor(0,0,0);
	$this->SetFont('Arial','I',9);
	$this->Cell($w1, 7,$print_title2,0,0,'C',0);
	$this->SetFont('Arial','B',14);
	$this->Cell($w2, 7,$print_title,1,1,'C',0);
	$this->SetXY(180,18);
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,$_SESSION['PDF']['w']-40,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,$_SESSION['PDF']['w']-40,26);
	$this->ln(5);

	$this->hend_title($_SESSION['PDF']['head']);

	$img_size = GetImageSize($_SESSION['PDF']['head']['pic_url']);
	if ($img_size[0] > $img_size[1]){
		$this->Image($_SESSION['PDF']['head']['pic_url'],240,5,47,0);
	}else{
		$this->Image($_SESSION['PDF']['head']['pic_url'],240,5,0,47);
	}

	$this->ln(15);
	$_SESSION['PDF']['h_y'] = $this->GetY();
	$this->SetLineWidth('.'.$_SESSION['PDF']['line']);
	$this->Cell($_SESSION['PDF']['w'],$_SESSION['PDF']['lh'],'','T',1);
	$this->ln(3);
}



function hend_title($parm){
	$this->SetLineWidth('.'.$_SESSION['PDF']['line']);
	$this->filed_1(10,34,25,15,7,'Order #','0,0,0',$parm['mk_name']);
	$this->filed_1(37,34,25,15,7,'Fab Type','0,0,0',$parm['fab_type']);
	$this->filed_1(64,34,25,15,7,'unit','0,0,0',$parm['unit']);
	$this->filed_1(91,34,25,15,7,'Width','0,0,0',$parm['width'].$parm['unit2']);
	$this->filed_1(118,34,90,15,7,'Remark','0,0,0',html_entity_decode($parm['rmk']));
	$this->filed_1(210,34,25,15,7,'YY ('.$parm['unit'].')','0,0,0',number_format($parm['averages'], 2, '', ','));
}



// field module 1 [表匡 1 ]
function filed_1($x,$y,$w,$h,$h2,$title,$color,$field) {
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
	$this->SetFont('Big5','IB',10);
	$this->SetXY($x,$y+$h2);
	$this->Cell($w, $h-$h2,$field,'B',0,'C');
}

function assortment(){
	global $op,$T_wiqty,$size_A;

	$this->SetFont('Big5','',10);
	$p_w = 10;
	$this->sety($_SESSION['PDF']['h_y']);
	$this->ln();

	$Color_w = $this->GetStringWidth('配色 Color \ Size')+$p_w;
	foreach($T_wiqty as $key){
		$w = $this->GetStringWidth($key['colorway']);
		$Color_w = ($Color_w > $w)? $Color_w+$p_w : $w+$p_w ;
	}

	$Size = explode(',',$size_A['size']);
	$s_count = count($Size);
	$a_w = ( $_SESSION['PDF']['w'] - $Color_w ) / $s_count ;


	$this->Cell($_SESSION['PDF']['w'], $_SESSION['PDF']['lh'],'配碼 ( Assortment )','TLR',1,'C');
	$this->Cell($_SESSION['PDF']['w'], .5,'','TLR',1,'C');
	$this->Cell($Color_w, $_SESSION['PDF']['lh'],'配色 Color   \   Size','TLR',0,'C');
	for( $i = 0 ; $i < $s_count ; $i ++ )
	$this->Cell($a_w, $_SESSION['PDF']['lh'],$Size[$i],'TLR',0,'C');
	$this->ln();
	foreach($T_wiqty as $key){
		$this->Cell($Color_w, $_SESSION['PDF']['lh'],$key['colorway'],'TLR',0,'L');
		$Size = explode(',',$key['qty']);
		for( $i = 0 ; $i < $s_count ; $i ++ )
		$this->Cell($a_w, $_SESSION['PDF']['lh'],$Size[$i],'TLR',0,'C');
		$this->ln();
	}
	$this->Cell($_SESSION['PDF']['w'], $_SESSION['PDF']['lh'],'','T',1,'C');
	$_SESSION['PDF']['h_y'] = $this->GetY();
}

function marker_list(){
	global $op,$T_wiqty,$size_A;

	$this->SetFont('Big5','',10);
	$p_w = 2;
	$this->sety($_SESSION['PDF']['h_y']);

	$Color_w = $this->GetStringWidth('配色 Color \ Size')+$p_w;
	foreach($T_wiqty as $key){

		$w = $this->GetStringWidth($key['colorway']);
		$Color_w = ($Color_w > $w)? $Color_w+$p_w : $w+$p_w ;
	}

	$other = 25;
	$a_w = $_SESSION['PDF']['w'] - $Color_w - ($other*6) ;
	$A_title = array('Mk #','Colorway','Marker Assortment','Level','Length '.$op['unit'],$op['unit'],'qty','YY '.$op['unit']);

	$this->Cell($other, $_SESSION['PDF']['lh'],$A_title[0],'TL',0,'C');
	$this->Cell($Color_w, $_SESSION['PDF']['lh'],$A_title[1],'TL',0,'C');
	$this->Cell($a_w, $_SESSION['PDF']['lh'],$A_title[2],'TL',0,'C');
	$this->Cell($other, $_SESSION['PDF']['lh'],$A_title[3],'TL',0,'C');
	$this->Cell($other, $_SESSION['PDF']['lh'],$A_title[4],'TL',0,'C');
	$this->Cell($other, $_SESSION['PDF']['lh'],$A_title[5],'TL',0,'C');
	$this->Cell($other, $_SESSION['PDF']['lh'],$A_title[6],'TL',0,'C');
	$this->Cell($other, $_SESSION['PDF']['lh'],$A_title[7],'TLR',1,'C');
	$this->Cell($_SESSION['PDF']['w'],.5,'','TLR',1,'C');

	foreach($op['mks'] as $key){
		if(empty($key['clothes']))$key['clothes']=0;
		if(empty($key['estimate']))$key['estimate']=0;
		if(empty($key['averages']))$key['averages']=0;
		$clothes = number_format($key['clothes'], 2, '', ',');
		$estimate = number_format($key['estimate'], 2, '', ',');
		$averages = number_format($key['averages'], 2, '', ',');

		$this->Cell($other, $_SESSION['PDF']['lh'],$key['mk_name'].$key['combo'].$key['mk_num'],'TL',0,'C');
		$this->Cell($Color_w, $_SESSION['PDF']['lh'],$key['colorway'][0],'TL',0,'L');
		$this->Cell($a_w, $_SESSION['PDF']['lh'],$key['asmts'][0],'TL',0,'L');
		$this->Cell($other, $_SESSION['PDF']['lh'],$key['level'][0],'TL',0,'C');
		$this->Cell($other, $_SESSION['PDF']['lh'],"$key[length]",'TL',0,'C');
		$this->Cell($other, $_SESSION['PDF']['lh'],$clothes,'TL',0,'C');
		$this->Cell($other, $_SESSION['PDF']['lh'],$estimate,'TL',0,'C');
		$this->Cell($other, $_SESSION['PDF']['lh'],$averages,'TLR',1,'C');
		if(is_array($key['colorway'])){
			foreach($key['colorway'] as $keys => $val){
				if($keys <> 0){
					$this->Cell($other, $_SESSION['PDF']['lh'],'','L',0,'C');
					$this->Cell($Color_w, $_SESSION['PDF']['lh'],$key['colorway'][$keys],'TL',0,'L');
					$this->Cell($a_w, $_SESSION['PDF']['lh'],$key['asmts'][$keys],'TL',0,'L');
					$this->Cell($other, $_SESSION['PDF']['lh'],$key['level'][$keys],'TL',0,'C');
					$this->Cell($other, $_SESSION['PDF']['lh'],'','L',0,'C');
					$this->Cell($other, $_SESSION['PDF']['lh'],'','L',0,'C');
					$this->Cell($other, $_SESSION['PDF']['lh'],'','L',0,'C');
					$this->Cell($other, $_SESSION['PDF']['lh'],'','LR',1,'C');
				}
			}
		}
	}
	$this->Cell($_SESSION['PDF']['w'], .5,'','TLR',1,'C');
	$this->Cell($other*3+$Color_w+$a_w, $_SESSION['PDF']['lh'],'Total：','TLB',0,'R');

	if(empty($op['clothes']))$op['clothes']=0;
	if(empty($op['estimate']))$op['estimate']=0;
	if(empty($op['averages']))$op['averages']=0;
	$clothes = number_format($op['clothes'], 2, '', ',');
	$estimate = number_format($op['estimate'], 2, '', ',');
	$averages = number_format($op['averages'], 2, '', ',');

	$this->Cell($other, $_SESSION['PDF']['lh'],$clothes,'TLB',0,'C');
	$this->Cell($other, $_SESSION['PDF']['lh'],$estimate,'TLB',0,'C');
	$this->Cell($other, $_SESSION['PDF']['lh'],$averages,'TLBR',0,'C');
	$this->ln(0);
}

function now_height($level=1,$height=0){
	if($_SESSION['PDF']['h'] < $this->gety() + ($_SESSION['PDF']['lh'] * $level ) + $height ){
		$this->AddPage();
	}
}


function RotatedText($x,$y,$txt,$angle) {
    //Text rotated around its origin
    $this->Rotate($angle,$x,$y);
    $this->Text($x,$y,$txt);
    $this->Rotate(0);
}

function Rotate($angle,$x=-1,$y=-1){
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

	$this->setx(10);
	$this->SetLineWidth('.'.$_SESSION['PDF']['line']);
	$this->Cell($_SESSION['PDF']['w'],.5,'','T',0);

	//Position at 1.5 cm from bottom
	$this->SetY(-10);
	//Arial italic 8
	$this->SetFont('BIG5','I',8);
	//Page number
	$this->AliasNbPages('<nb>');
	$this->Cell(0,10,'Page '.$this->PageNo().' / <nb>                           Created by : '.$creator,0,0,'C');
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


function add_page($x){
	 $parm=$_SESSION['PDF']['head'];
	 $img_size = GetImageSize($parm['pic_url']);
	 $this->AddPage();
	 $this->hend_title($parm);
	 $this->SetXY($x,80);
		if ($img_size[0] > $img_size[1])
		{
			$this->Image($parm['pic_url'],10,28,40,0);
		}else{
			$this->Image($parm['pic_url'],10,28,0,40);
		}
		return 0;
}




//---------- table with Muti cell ----------------
var $widths;
var $aligns;

function SetWidths($w){
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a){
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data){
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

function CheckPageBreak($h){
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt){
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
