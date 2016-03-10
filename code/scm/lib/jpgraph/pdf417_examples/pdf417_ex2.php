<?php
require_once('../jpgraph.php');
require_once('../jpgraph_canvas.php');
require_once('../jpgraph_pdf417.php');

$data = 'PDFf-417';

// Setup some symbolic names for barcode specification

$columns = 8;	// Use 8 data (payload) columns
$errlevel = 2;	// Use error level 2 (minimum recommended)
$modwidth = 2;	// Setup module width (in pixels)
$height = 3;	// Height factor

// Create a new encoder and backend to generate PNG images
$encoder = new PDF417Barcode($columns,$errlevel);
$e = PDF417BackendFactory::Create(BACKEND_IMAGE,$encoder);

if( $e ) {
    $e->SetModuleWidth($modwidth);
    $e->SetHeight($height);    
    $r = $e->Stroke($data);
}
else {
    die('Cant create PDF417 encoder.'); 
}
?>
