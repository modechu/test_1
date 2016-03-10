<?php
// ==============================================
// Output Image using Code 128
// ==============================================

include "../jpgraph.php";
include "../jpgraph_canvas.php";
include "jpgraph_barcode.php";

$encoder = BarcodeFactory::Create(ENCODING_CODE128);
$e = BackendFactory::Create(BACKEND_IMAGE,$encoder);
$e->SetModuleWidth(2);
$e->SetHeight(70);
$e->Stroke('3125134772');

?>