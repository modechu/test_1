<?php
// ==============================================
// Output Image using Code Interleaved 2 of 5
// ==============================================

include "../jpgraph.php";
include "../jpgraph_canvas.php";
include "jpgraph_barcode.php";

$encoder = BarcodeFactory::Create(ENCODING_CODEI25);
$e = BackendFactory::Create(BACKEND_IMAGE,$encoder);
$e->SetModuleWidth(2);
$e->Stroke('1234');

?>