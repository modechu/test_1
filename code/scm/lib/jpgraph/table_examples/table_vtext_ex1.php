<?php
include '../jpgraph.php';
include '../jpgraph_canvas.php';
include '../jpgraph_table.php';

$graph = new CanvasGraph(430,600);

// Setup the basic table
$data = array( 
    array('GROUP 1O',        'w631','w632','w633','w634','w635','w636'),
    array('Critical (sum)',13,17,15,8,3,9),
    array('High (sum)',34,35,26,20,22,16),
    array('Low (sum)',41,43,49,45,51,47),
    array('Sum:',88,95,90,73,76,72)
    );


$table = new GTextTable();
$table->Set($data);
$table->SetAlign('right');
$table->SetFont(FF_TIMES,FS_NORMAL,11);
$table->SetCellFont(0,0,FF_TIMES,FS_BOLD,14);
$table->SetRowTextOrientation(0,90);
$table->SetCellAlign(0,0,'center','center');
$graph->Add($table);
$graph->Stroke();

?>

