<?php
require_once('../jpgraph.php');
require_once('../jpgraph_canvas.php');
require_once('../jpgraph_pdf417.php');

// Simplest possible PDF417 barcode using just default values

$data = 'PDF-417';

// Create a new encoder and backend to generate PNG images
$backend = PDF417BackendFactory::Create(BACKEND_IMAGE,new PDF417Barcode());
$backend->Stroke($data);

?>
