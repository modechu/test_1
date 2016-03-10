<?php
require_once('../jpgraph.php');
require_once('../jpgraph_canvas.php');
require_once('../jpgraph_pdf417.php');

$data = 'PDF-417';

// Setup some symbolic names for barcode specification

$columns = 8;	// Use 8 data (payload) columns
$errlevel = 4;	// Use error level 4
$modwidth = 0.8;// Setup module width (in PS points)
$height = 3;	// Height factor (=2)
$showtext = true;  // Show human readable string

// Create a new encoder and backend to generate PNG images
$encoder = new PDF417Barcode($columns,$errlevel);
$e = PDF417BackendFactory::Create(BACKEND_PS,$encoder);

if( $e ) {
    $e->SetModuleWidth($modwidth);
    $e->SetHeight($height); 
    $e->NoText(!$showtext);  
    $e->SetColor('black','yellow');
    $r = $e->Stroke($data);
    echo nl2br(htmlspecialchars($r));
}
else {
    die('Cant create PDF417 encoder.'); 
}
?>
