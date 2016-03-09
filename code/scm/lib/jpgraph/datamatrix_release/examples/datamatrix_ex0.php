<?php

// Datamatrix example 0
// Use mostly default settings. Will use ASCII encoding and automatic determine optimal size.
require_once('../datamatrix.inc.php');

$datastr = '123456';
$coder = DatamatrixFactory::Create(); 
$coder->SetEncoding(ENCODING_ASCII);
$coder->SetSize(DMAT_32x32);
$e = DatamatrixBackendFactory::Create($coder);

// By default the module width is 2 pixel so we increase it a bit
$e->SetModuleWidth(5);

// Create the barcode from the given data string and write to output file
$res = $e->Stroke($datastr);
if( !$res ) {
    $errno = $printer->GetError();
    die("Could not stream image. (error = $errno)");
}

?>
