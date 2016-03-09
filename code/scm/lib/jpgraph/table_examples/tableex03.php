<?php
include '../jpgraph.php';
include '../jpgraph_canvas.php';
include '../jpgraph_table.php';

$data = array( array('2007'),
               array('','Q1','','','Q2'),
	       array('','Jan','Feb','Mar','Apr','May','Jun'),
	       array('Min','15.2', '12.5', '9.9', '70.0', '22.4','21.5'),
	       array('Max','23.9', '14.2', '18.6', '71.3','66.8','42.6'));

$graph = new CanvasGraph(290,140);

// Setup the basic table
$table = new GTextTable();
$table->Set($data);
$table->SetAlign('right');
$table->SetFont(FF_ARIAL,FS_NORMAL,11);
$table->SetBorder('black',2);
$table->SetMinColWidth(40);

// Setup top row with the year title
$table->MergeCells(0,0,0,6);
$table->SetRowFont(0,FF_ARIAL,FS_BOLD,16);
$table->SetRowColor(0,'white');
$table->SetCellFillColor(0,0,'peru');

// Setup quarter header
$table->MergeCells(1,1,1,3);
$table->MergeCells(1,4,1,6);
$table->SetRowAlign(1,'center');
$table->SetRowColor(1,'navy');
$table->SetRowFillColor(1,'tan');
$table->SetRowGrid(2,'',0); // Turn off the gridline just under the top row

// Setup row and column headers
$table->SetRowColor(2,'navy');
$table->SetRowFillColor(2,'tan');
$table->SetCellColor(3,0,'navy');
$table->SetCellColor(4,0,'navy');
$table->SetCellFillColor(3,0,'tan');
$table->SetCellFillColor(4,0,'tan');

// Finally make the figures slightly smaller
$table->SetFont(3,1,4,6,FF_ARIAL,FS_NORMAL,9);

$graph->Add($table);
$graph->Stroke();

?>

