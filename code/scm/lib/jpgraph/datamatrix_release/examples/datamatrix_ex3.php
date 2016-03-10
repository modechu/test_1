<?php

// Datamatrix example 3
// Use mostly default settings. Specifically select BASE256_ENCODING and 44x44 symbol size
require_once('../datamatrix.inc.php');

$datastr = '123456';
$outputFile = 'dm_ex3.png';

// Create and set parameters for the encoder
$coder = DatamatrixFactory::Create(DMAT_44x44); 
$coder->SetEncoding(ENCODING_BASE256);

// Create the image backend (default)
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
