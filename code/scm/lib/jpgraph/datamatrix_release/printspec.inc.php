<?php
/*=======================================================================
// File: 	PRINTSPEC.INC.PHP
// Description:	Define the common print specification
// Created: 	2006-08-23
// Ver:		$Id: printspec.inc.php 767 2006-09-24 14:32:06Z ljp $
//
// Copyright (c) 2006 Aditus Consulting. All rights reserved.
//========================================================================
*/


DEFINE("DM_TYPE_140",1);
DEFINE("DM_TYPE_200",2);


class PrintSpecification {
    var $iType = -1;
    var $iData = array();
    var $iDataLen = 0;
    var $iSize = array();
    var $iErrLevel = 0 ;
    var $iMatrix = array();
    var $iEncoding = 0;

    function PrintSpecification($aType,$aData,$aMat,$aEncoding,$aErrLevel=-1) {
	$this->iType = $aType;
	$this->iData = $aData;
	$this->iDataLen = count($aData);
	$this->iMatrix = $aMat;
	$this->iSize[0] = count($aMat);
	$this->iSize[1] = count($aMat[0]);
	$this->iEncoding = $aEncoding;
	$this->iErrLevel = $aErrLevel;
    }
}

?>
