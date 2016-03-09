<?php
require_once('../jpgraph.php');
require_once('../jpgraph_canvas.php');
require_once('../jpgraph_pdf417.php');

$right = chr(16);
$left = chr(17);
$up = chr(24);
$down = chr(25);
$space = chr(32);

$data = 'RIGHT' . $right . 'LEFT' . $left . 'UP' . $up . 'DOWN' . $down . 'SPACE' . $space;

// Setup some symbolic names for barcode specification
// All of these have default values but putting them here
// makes it easy to remember what to change.

$columns = 6;	// Use 8 data (payload) columns
$errlevel = 4;	// Use error level 4
$modwidth = 2;	// Setup module width (in pixels)
$height = 2;	// Height factor (=2)
$showtext = false;  // Show human readable string

// Create a new encoder and backend to generate PNG images
$encoder = new PDF417Barcode($columns,$errlevel);
$e = PDF417BackendFactory::Create(BACKEND_IMAGE,$encoder);

if( $e ) {
    $e->SetModuleWidth($modwidth);
    $e->SetHeight($height); 
    $e->NoText(!$showtext);  
    $e->SetColor('black','white'); // This is the same as default
    $r = $e->Stroke($data);
}
else {
    die('Cant create PDF417 encoder.'); 
}
?>
