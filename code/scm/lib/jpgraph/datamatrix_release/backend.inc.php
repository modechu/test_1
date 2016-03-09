<?php
/*=======================================================================
// File: 	BACKEND.INC.PHP
// Description:	All various output backends available
// Created: 	2006-08-23
// Ver:		$Id: backend.inc.php 811 2006-12-07 05:58:21Z ljp $
//
// Copyright (c) 2006 Aditus Consulting. All rights reserved.
//========================================================================
*/

DEFINE('BACKEND_ASCII',0);
DEFINE('BACKEND_IMAGE',1);
DEFINE('BACKEND_PS',2);
DEFINE('BACKEND_EPS',3);

class BackendMatrix {
    var $iDM = NULL;
    var $iModWidth=2;
    var $iInv=false;
    var $iQuietZone = 0 ;
    var $iError = 0 ;
    function BackendMatrix(&$aDataMatrixSpec) {
	$this->iDM =& $aDataMatrixSpec;
    }

    function Stroke($aData) {
    }

    function SetModuleWidth($aW) {
	$this->iModWidth = $aW;
    }

    function SetQuietZone($aW) {
	$this->iQuietZone = $aW;
    }

    function SetTilde($aFlg=true) {
	$this->iDM->SetTilde($aFlg);
    }

    function SetInvert($aFlg=true) {
	$this->iInv = $aFlg;
    }

    function GetError() {
	return $this->iError;
    }
}

require_once('rgb_colors.inc.php');

class BackendMatrix_IMAGE extends BackendMatrix {
    var $iColor = array(array(0,0,0),array(255,255,255),array(255,255,255));
    var $iRGB = null; 
    var $iImgFormat = 'png',$iQualityJPEG=75;

    function BackendMatrix_IMAGE(&$aDataMatrixSpec) {
	parent::BackendMatrix($aDataMatrixSpec);
	$this->iRGB =& new Datamatrix_RGB();
    }

    function SetSize($aShapeIdx) { 
	$this->iDM->SetSize($aShapeIdx);
    }

    function SetColor($aOne,$aZero,$aBackground=array(255,255,255)) {
	$this->iColor[0] = $aOne;
	$this->iColor[1] = $aZero;
	$this->iColor[2] = $aBackground;
    }

    // Specify image format. Note depending on your installation
    // of PHP not all formats may be supported.
    function SetImgFormat($aFormat,$aQuality=75) {		
	$this->iQualityJPEG = $aQuality;
	$this->iImgFormat = $aFormat;
    }

    function PrepareImgFormat() {		
	$format = strtolower($this->iImgFormat);
	if( $format == 'jpg' ) {
	    $format = 'jpeg';
	}
	$tst = true;
	$supported = imagetypes();
	if( $format=="auto" ) {
	    if( $supported & IMG_PNG )
		$this->iImgFormat="png";
	    elseif( $supported & IMG_JPG )
		$this->iImgFormat="jpeg";
	    elseif( $supported & IMG_GIF )
		$this->iImgFormat="gif";
	    elseif( $supported & IMG_WBMP )
		$this->iImgFormat="wbmp";
	    else {
		$this->iError = -15;
		return false;
	    }
	}
	else {
	    if( $format=="jpeg" || $format=="png" || $format=="gif" ) {
		if( $format=="jpeg" && !($supported & IMG_JPG) )
		    $tst=false;
		elseif( $format=="png" && !($supported & IMG_PNG) ) 
		    $tst=false;
		elseif( $format=="gif" && !($supported & IMG_GIF) ) 	
		    $tst=false;
		elseif( $format=="wbmp" && !($supported & IMG_WBMP) ) 	
		    $tst=false;
		else {
		    $this->iImgFormat=$format;
		}
	    }
	    else 
		$tst=false;
	    if( !$tst ) {
		$this->iError = -15;
		return false;
	    }
	}
	return true;
    }	

    function Stroke($aData,$aFileName='',$aDebug=false) { 
	
	// Check the chosen graphic format
	if( !$this->PrepareImgFormat() ) {
	    return false;
	}

	$pspec = $this->iDM->Enc($aData);
	if( $pspec === false ) {
	    $this->iError = $this->iDM->iError;
	    return false;
	}

	$mat = $pspec->iMatrix;
	$m = $this->iModWidth; 

	$h = $pspec->iSize[0]*$m + 2*$this->iQuietZone;
	$w = $pspec->iSize[1]*$m + 2*$this->iQuietZone;

	$img = @imagecreatetruecolor($w,$h);
	if( !$img ) {
	    $this->iError = -12;
	    return false;
	}

	$canvas_color  = $this->iRGB->Allocate($img,'white');
	$one_color  = $this->iRGB->Allocate($img,$this->iColor[0]);
	$zero_color = $this->iRGB->Allocate($img,$this->iColor[1]);
	$bkg_color  = $this->iRGB->Allocate($img,$this->iColor[2]);

	if( $canvas_color === false || $one_color === false || $zero_color === false || $bkg_color === false ) {
	    $this->iError = -13;
	    return false;
	}

	imagefilledrectangle($img,0,0,$w-1,$h-1,$canvas_color);
	imagefilledrectangle($img,0,0,$w-1,$h-1,$bkg_color);

	for($i=0; $i < $pspec->iSize[0]-1; ++$i ) {
	    for($j=1; $j < $pspec->iSize[1]; ++$j ) {
		$bit = $mat[$i][$j] == 1 ? $one_color : $zero_color;
		if( $m == 1 ) {
		    imagesetpixel($img,$j+$this->iQuietZone,$i+$this->iQuietZone,$bit);
		}
		else {
		    imagefilledrectangle($img,$j*$m+$this->iQuietZone,$i*$m+$this->iQuietZone,
					 $j*$m+$m-1+$this->iQuietZone,$i*$m+$m-1+$this->iQuietZone,$bit);
		}
	    }
	}

	// Left alignment line
	imagefilledrectangle($img,$this->iQuietZone,$this->iQuietZone,$this->iQuietZone+$m-1,$h-$this->iQuietZone-1,$one_color);

	// Bottom alignment line
	imagefilledrectangle($img,$this->iQuietZone,$h-$this->iQuietZone-$m,$w-$this->iQuietZone-1,$h-$this->iQuietZone-1,$one_color);

	if( headers_sent($file,$lineno) ) {
	    $txt = sprintf('<table border="1"><tr><td style="color:darkred; font-size:1.2em;"><b>Datamatrix Error:</b> 
HTTP headers have already been sent.<br>Caused by output from file <b>%s</b> at line <b>%d</b>.</td></tr><tr><td><b>Explanation:</b><br>HTTP headers have already been sent back to the browser indicating the data as text before the library got a chance to send it\'s image HTTP header to this browser. This makes it impossible for the Datamatrix library to send back image data to the browser (since that would be interpretated as text by the browser and show up as junk text).<p>Most likely you have some text in your script before the call to <i>DatamatrixBackend::Stroke()</i>. If this texts gets sent back to the browser the browser will assume that all data is plain text. Look for any text (even spaces and newlines) that might have been sent back to the browser. <p>For example it is a common mistake to leave a blank line before the opening "<b>&lt;?php</b>".</td></tr></table>',$file,$lineno);
	    die($txt);
	}

	if( $aFileName == '' ) {
	    header("Content-type: image/$this->iImgFormat");
	    switch( $this->iImgFormat ) {
		case 'png':
		    $res = @imagepng($img);
		    break;
		case 'jpeg':
		    $res = @imagejpeg($img,NULL,$this->iQualityJPEG);
		    break;
		case 'gif':
		    $res = @imagegif($img);
		    break;
		case 'wbmp':
		    $res = @imagewbmp($img);
		    break;
	    }
	}
	else {
	    switch( $this->iImgFormat ) {
		case 'png':
		    $res = @imagepng($img,$aFileName);
		    break;
		case 'jpeg':
		    $res = @imagejpeg($img,$aFileName,$this->iQualityJPEG);
		    break;
		case 'gif':
		    $res = @imagegif($img,$aFileName);
		    break;
		case 'wbmp':
		    $res = @imagewbmp($img,$aFileName);
		    break;
	    }
	}
	return $res ;
    }
}

class BackendMatrix_ASCII extends BackendMatrix {

    function BackendMatrix_ASCII(&$aDataMatrixSpec) {
	parent::BackendMatrix($aDataMatrixSpec);
    }

    function PrintMatrix($mat,$inv=false,$width=1,$aOne='1',$aZero='0') {
	
	if( $width > 1 ) {
	    $m = count($mat);
	    $n = count($mat[0]);
	    $newmat = array();
	    for($i=0; $i < $m; ++$i ) 
		for($j=0; $j < $n; ++$j ) 
		    for($k=0; $k < $width; ++$k ) 
			for($l=0; $l < $width; ++$l ) 
			    $newmat[$i*$width+$k][$j*$width+$l] = $mat[$i][$j];
	    $mat = $newmat;
	}

	$m = count($mat);
	$n = count($mat[0]);
	for($i=0; $i < $m; ++$i ) {
	    for($j=0; $j < $n; ++$j ) {
		if( !$inv ) {
		    echo $mat[$i][$j] ? $aOne : $aZero;
		}
		else {
		    if( $mat[$i][$j] ) 
			echo $aZero;
		    else
			echo $aOne;
		}
	    }
	    echo "\n";
	}
	echo "\n";
    }

    function PrintBits($bits,$chunk=4) {
	$n = count($bits);
	$cnt=0;$nibs=0;
	for( $i=0; $i < $n; ++$i ) {
	    echo $bits[$i];
	    $cnt ++;
	    if( $cnt == $chunk ) {
		echo " ";
		$cnt = 0 ;
		$nibs++;
		if( $nibs == 22 ) {
		    $nibs=0;
		    echo "\n";
		}
	    }
	}
    }

    function Stroke($aData,$aDebug=false) {
	$pspec = $this->iDM->Enc($aData);
	if( $aDebug ) {
	    echo "-------------- BACKEND ASCII ---------------\n";
	    echo "Matrix size: {$pspec->iSize[0]}*{$pspec->iSize[1]}\n"; 
	    if( $pspec->iType == DM_TYPE_140 ) {
		$errlevels = array('None','ECC_050','ECC_080','ECC_100','ECC_140');
		echo "Error 000 - 140 level: {$errlevels[$pspec->iErrLevel]}\n";
	    }
	}
	$this->PrintMatrix($pspec->iMatrix,$this->iInv,$this->iModWidth,'X','_');
    }
}

class DatamatrixBackendFactory {
    function Create(&$aDMSpec,$aBackend=BACKEND_IMAGE) {
	switch( $aBackend ) {
	    case BACKEND_ASCII:
		return new BackendMatrix_ASCII($aDMSpec);
		break;
	    case BACKEND_IMAGE:
		return new BackendMatrix_Image($aDMSpec);
		break;
	    case BACKEND_PS:
		return new BackendMatrix_PS($aDMSpec);
		break;
	    case BACKEND_EPS:
		return new BackendMatrix_PS($aDMSpec,true);
		break;
	    default:
		return false;
	}
    }
}

?>