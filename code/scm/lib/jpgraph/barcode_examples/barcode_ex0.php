<?php
// ==============================================
// Output Image using Code 39 using only default values
// ==============================================

include "../jpgraph.php";
include "../jpgraph_canvas.php";
include "jpgraph_barcode.php";

$encoder = BarcodeFactory::Create(ENCODING_CODE39);
$e = BackendFactory::Create(BACKEND_IMAGE,$encoder);
$e->Stroke('31252');

?>