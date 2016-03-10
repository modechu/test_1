<?php
/*=======================================================================
// File:	WINDROSE_EX2.PHP
// Description:	Example 2 of how to use windrose plots
// Created: 	2003-09-26
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id$
// Description:
// This example shows a free Windrose plot, i.e. a plot where 
// the directions can be an arbitraty angle.
// Every aspect of the plot is customizable. But this example uses
// just the default values to illustrate the simplicity of using the 
// basic functionlity.
//========================================================================
*/
require_once ('../jpgraph.php');
require_once ('../jpgraph_windrose.php');

//---------------------------------------------------------
// Data can be specified using both ordinal index of the axis 
// as well as the direction label.
//---------------------------------------------------------
$data = array(
    '45.9' => array(3,2,1,2,2),
    355 => array(1,1,1.5,2),
    180 => array(1,1,1.5,2),
    150 => array(1,2,1,3),
    'S' => array(2,3,5,1),
    );

// Add some labels for  afew of the directions
$labels = array(355=>"At\nHome base",180=>"Probe\n123",150=>"Power\nplant");

//---------------------------------------------------------
// First create a new windrose graph with a title
//---------------------------------------------------------
$graph = new WindroseGraph(500,500);
$graph->title->Set('Windrose example 2');
$graph->title->SetFont(FF_VERDANA,FS_BOLD,14);
$graph->title->SetColor('navy');

//----------------------------------------------------------------
// Create the free windrose plot. 
//----------------------------------------------------------------
$wp = new WindrosePlot($data);
$wp->SetType(WINDROSE_TYPEFREE);

// Add a few labels
$wp->SetLabels($labels);

// Add some "arbitrary" text to the center
$wp->scale->SetZeroLabel("SOx\n8%");

// Finally add it to the graph
$graph->Add($wp);

//----------------------------------------------------------------
// Send the graph to the browser
//----------------------------------------------------------------
$graph->Stroke();

?>

