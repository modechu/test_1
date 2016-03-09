<?php

// Datamatrix example 1
// Use mostly default settings. Will use ASCII encoding and automatic determine optimal size.
require_once('../datamatrix.inc.php');

$datastr = '123456';
$outputFile = 'dm_ex1.png';

$coder = DatamatrixFactory::Create(); 
$e = DatamatrixBackendFactory::Create($coder);

// By default the module width is 2 pixel so we increase it a bit
$e->SetModuleWidth(5);

// Create the barcode from the given data string and write to output file
$res = $e->Stroke($datastr,$outputFile);
if( $res ) {
    echo "Wrote Datamatrix to file $outputFile\n";
}
else {
    echo "Could not write to file '$outputFile'\n";
}




?>
