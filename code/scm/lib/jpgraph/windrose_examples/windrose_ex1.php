<?php
/*=======================================================================
// File:	WINDROSE_EX1.PHP
// Description:	Example 1 of how to use windrose plots
// Created: 	2003-09-26
//=======================================================================
*/
require_once ('../jpgraph.php');
require_once ('../jpgraph_windrose.php');
		
//---------------------------------------------------------
// Data can be specified using both ordinal index of the axis 
// as well as the direction label
//---------------------------------------------------------
$data = array(
    0 => array(1,1,2.5,4),
    1 => array(3,4,1,4),
    'wsw' => array(1,5,5,3),
    'N' => array(2,7,5,4,2),
    15 => array(2,7,12));

//---------------------------------------------------------
// First create a new windrose graph with a title
//---------------------------------------------------------
$graph = new WindroseGraph(400,400);

//---------------------------------------------------------
// Setup title 
//---------------------------------------------------------
$graph->title->Set('Windrose example 1');
$graph->title->SetFont(FF_VERDANA,FS_BOLD,12);
$graph->title->SetColor('navy');

//----------------------------------------------------------------
// Create the windrose plot. (Each graph may have multiple plots)
// The default plot will have 16 compass axis.
//----------------------------------------------------------------
$wp = new WindrosePlot($data);
$wp->SetRadialGridStyle('solid');
$graph->Add($wp);

//----------------------------------------------------------------
// Send the graph to the browser
//----------------------------------------------------------------
$graph->Stroke();
?>

