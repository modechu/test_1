<?php
include_once($config['root_dir']."/lib/chinese.php");
define('FPDF_FONTPATH','font/');

class PDF_transport extends PDF_Chinese {

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

  //Put watermark
  $this->SetFont('Arial','B',50);
  $this->SetTextColor(230,210,210);
  $this->RotatedText($_SESSION['PDF']['w']/2,$_SESSION['PDF']['h']/2,$op['trn_num'],30);
	//Logo
	$w =$this->GetStringWidth($print_title) +30;
	$this->Image('./images/logo10.jpg',10,8,90);
	//title
	$this->SetXY(100,15);
	$this->SetTextColor(40,40,40);
	$this->SetFont('Arial','I',9);
	$this->Cell(40, $_SESSION['PDF']['lh'],$print_title2,0,0,'C',0);
	$this->SetFont('Arial','B',14);
	$this->Cell(45, $_SESSION['PDF']['lh'],$print_title,1,1,'C',0);

	// line
	$this->SetLineWidth(0.5);
	$this->SetDrawColor(100,100,100);
	$this->Line(10,25,200,25);
	$this->ln(1);
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,26,200,26);

	$this->SetFont('Big5','',10);

	$this->ln();
	$this->Cell(30, $_SESSION['PDF']['lh'],'轉運單號碼#：','0',0,'R');
	$this->Cell($_SESSION['PDF']['w']-30, $_SESSION['PDF']['lh'],$op['trn_num'],'0',1,'L');
	$this->Cell(30, $_SESSION['PDF']['lh'],'訂單號碼#：','0',0,'R');
	$this->Cell($_SESSION['PDF']['w']-30, $_SESSION['PDF']['lh'],$op['ord_num'],'0',1,'L');
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(0,0,0);
	$this->Line(10,$this->gety(),200,$this->gety());
	$this->ln(3);
	$_SESSION['PDF']['h_y'] = $this->GetY()-0.5;
}

function org_fty(){
	global $op;

	$this->SetFont('Big5','',10);
	$this->Cell(30, $_SESSION['PDF']['lh'],'轉出單位：','TLR',0,'R');
	$this->Cell($_SESSION['PDF']['w']-30, $_SESSION['PDF']['lh'],$op['org_fty']['Name'],'TR',1,'L');
	$x = $this->getx();
	$y = $this->gety();
	$this->Cell(30, $_SESSION['PDF']['lh'],'地址：','LR',0,'R');
	$this->MultiCell($_SESSION['PDF']['w']-30, $_SESSION['PDF']['lh'],$op['org_fty']['Addr'],'TR','L');
	$w =$this->GetStringWidth($op['org_fty']['Addr']);
	if($w > ( $_SESSION['PDF']['w'] - 30 )){
		$this->ln();
		$this->setxy($x,$y);
		$sc = ceil($w / ( $_SESSION['PDF']['w']-30 ) );
		for($i=0;$i < $sc;$i++){
			$this->Cell(30, $_SESSION['PDF']['lh'],'','LR',1,'R');
		}
	}

	$this->Cell(30, $_SESSION['PDF']['lh'],'電話：','LBR',0,'R');
	$this->Cell(70, $_SESSION['PDF']['lh'],$op['org_fty']['TEL'],'TBR',0,'L');
	$this->Cell(30, $_SESSION['PDF']['lh'],'傳真：','TBR',0,'R');
	$this->Cell($_SESSION['PDF']['w']-130, $_SESSION['PDF']['lh'],$op['org_fty']['FAX'],'TBR',1,'L');
	$this->ln(3);
}

function material(){
	global $op;

	$this->Cell($_SESSION['PDF']['w']-50, $_SESSION['PDF']['lh'],'Material #','TLRB',0,'C');
	$this->Cell(30, $_SESSION['PDF']['lh'],'Color/cat.','TRB',0,'C');
	$this->Cell(20, $_SESSION['PDF']['lh'],'Qty','TRB',1,'C');

	foreach($op['mat_list'] as $key => $val){
		if(empty($val['mat_name']))$val['mat_name']='';
		$this->Cell($_SESSION['PDF']['w']-50, $_SESSION['PDF']['lh'],$val['mat_code'].' '.$val['mat_name'],'TLRB',0,'L');
		$this->Cell(30, $_SESSION['PDF']['lh'],$val['color'],'TRB',0,'L');
		$this->Cell(20, $_SESSION['PDF']['lh'],$val['qty'].' '.$val['unit'],'TRB',1,'R');
		$w =$this->GetStringWidth($val['note']);
		$sc = ceil($w / ( $_SESSION['PDF']['w']) );
		if(($this->gety() +( ($sc + 1 )* $_SESSION['PDF']['lh'])) > $_SESSION['PDF']['h']){ #判斷換頁 PS
			$this->AddPage();
		}

		$this->MultiCell($_SESSION['PDF']['w'], $_SESSION['PDF']['lh'],$val['note'],'LTBR','L');
		$this->Line(10,$this->gety()-.3,200,$this->gety()-.3);
	}
	$this->ln(3);
}

function now_height($level=1,$height=0){
	if($_SESSION['PDF']['h'] < $this->gety() + ($_SESSION['PDF']['lh'] * $level ) + $height ){
		$this->AddPage();
	}
}

function remark(){
	global $op;

	$this->Cell($_SESSION['PDF']['w'], $_SESSION['PDF']['lh'],'Remark Records','LTRB',1,'C');
	$this->now_height();
	$this->Cell(25, $_SESSION['PDF']['lh'],'Date','TLRB',0,'C');
	$this->now_height();
	$this->Cell($_SESSION['PDF']['w']-50, $_SESSION['PDF']['lh'],'History records','TRB',0,'C');
	$this->now_height();
	$this->Cell(25, $_SESSION['PDF']['lh'],'by','TRB',1,'C');

	foreach($op['tran_log'] as $key => $val){
		$x = $this->getx();
		$y = $this->gety();
		$xx = $x + ($_SESSION['PDF']['w'] - 25);

		$w =$this->GetStringWidth($val['des']);
		$sc = ceil($w / ( $_SESSION['PDF']['w'] - 50 ) );
		if(($y +($sc * $_SESSION['PDF']['lh'])) > $_SESSION['PDF']['h']){ #判斷換頁 PS
			$this->AddPage();
			$x = $this->getx();
			$y = $this->gety();
			$xx = $x + ($_SESSION['PDF']['w'] - 25);
		}
		$this->Cell(25, $_SESSION['PDF']['lh'],$val['k_date'],'LTR',0,'C');
		$this->MultiCell($_SESSION['PDF']['w'] - 50, $_SESSION['PDF']['lh'],$val['des'],'LTR','TL');
		$this->setxy($xx,$y);
		$this->Cell(25, $_SESSION['PDF']['lh'],$val['user'],'TR',1,'C');

		if($w > ( $_SESSION['PDF']['w'] - 50 )){
			$this->ln();
			$this->setxy($x,$y);
			$sc = ceil($w / ( $_SESSION['PDF']['w'] - 50 ) );
			for($i=0;$i < $sc;$i++){
				$this->Cell(25, $_SESSION['PDF']['lh'],'','LR',0,'R');
				$this->setx($this->getx() + $_SESSION['PDF']['w'] - 50);
				$this->Cell(25, $_SESSION['PDF']['lh'],'','R',1,'R');

			}
		}
		$this->Cell($_SESSION['PDF']['w'],0,'','T',1,'R');
	}
	$this->ln(3);
}

function new_fty(){
	global $op;

	$this->Line(10,$this->gety(),200,$this->gety());

	$w =$this->GetStringWidth($op['new_fty']['Addr']);
	$sc = ceil($w / ( $_SESSION['PDF']['w'] - 30 ) );
	if(($this->gety() +( ( $sc + 3 )* $_SESSION['PDF']['lh'])) > $_SESSION['PDF']['h']){ #判斷換頁 PS
		$this->AddPage();
	}

	$this->ln(3);
	$this->SetFont('Big5','',10);
	$this->Cell(30, $_SESSION['PDF']['lh'],'轉入單位：','TLR',0,'R');
	$this->Cell($_SESSION['PDF']['w']-30, $_SESSION['PDF']['lh'],$op['new_fty']['Name'],'TR',1,'L');
	$x = $this->getx();
	$y = $this->gety();
	$this->Cell(30, $_SESSION['PDF']['lh'],'地址：','LR',0,'R');
	$this->MultiCell($_SESSION['PDF']['w']-30, $_SESSION['PDF']['lh'],$op['new_fty']['Addr'],'TR','L');
	$w =$this->GetStringWidth($op['new_fty']['Addr']);
	if($w > ( $_SESSION['PDF']['w'] - 30 )){
		$this->ln();
		$this->setxy($x,$y);
		$sc = ceil($w / ( $_SESSION['PDF']['w']-30 ) );
		for($i=0;$i < $sc;$i++){
			$this->Cell(30, $_SESSION['PDF']['lh'],'','LR',1,'R');
		}
	}

	$this->Cell(30, $_SESSION['PDF']['lh'],'電話：','LBR',0,'R');
	$this->Cell(70, $_SESSION['PDF']['lh'],$op['new_fty']['TEL'],'TBR',0,'L');
	$this->Cell(30, $_SESSION['PDF']['lh'],'傳真：','TBR',0,'R');
	$this->Cell($_SESSION['PDF']['w']-130, $_SESSION['PDF']['lh'],$op['new_fty']['FAX'],'TBR',1,'L');
	$this->ln(6);
	$this->Cell($_SESSION['PDF']['w'],3,'Submit by：'.$op['sub_user'].' / '.$op['sub_date'],'',0,'R');
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
	 $parm=$_SESSION['parm'];
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
