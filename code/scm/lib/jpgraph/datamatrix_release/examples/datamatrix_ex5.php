<?php

// Datamatrix example 5
// Use mostly default settings. Specifically select TEXT and 22x22 symbol size with a 10 pixel quiet zone
require_once('../datamatrix.inc.php');

$datastr = 'This is a 20x20 symbol';
$outputFile = 'dm_ex5.png';

// Create and set parameters for the encoder
$coder = DatamatrixFactory::Create(DMAT_20x20); 
$coder->SetEncoding(ENCODING_TEXT);

// Create the image backend (default)
$e = DatamatrixBackendFactory::Create($coder);

// By default the module width is 2 pixel so we increase it a bit
$e->SetModuleWidth(4);

// Set Quiet zone
$e->SetQuietZone(10);

// Set other than default colors (one, zero, background)
$e->SetColor('navy','white');

// Create the barcode from the given data string and write to output file
$res = $e->Stroke($datastr,$outputFile);
if( $res ) {
    echo "Wrote Datamatrix to file $outputFile\n";
}
else {
    echo "Could not write to file '$outputFile'\n";
}

?>
