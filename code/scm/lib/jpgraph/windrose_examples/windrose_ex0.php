<?php
/*=======================================================================
// File:	WINDROSE_EX0.PHP
// Description:	Example 0 of how to use windrose plots
// Created: 	2003-09-28
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id$
// Description:
// This example shows a regular Windrose plot, i.e. a plot where 
// the directions is one of the 16 compass angles.
// Every aspect of the plot is customizable. But this example uses
// just the default values to illustrate the simplicity of using the 
// basic functionlity.
//========================================================================
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
    'WSW' => array(1,5,5,3),
    'N' => array(2,3,8,1,1),
    15 => array(2,3,5));

//---------------------------------------------------------
// First create a new windrose graph with a title
//---------------------------------------------------------
$graph = new WindroseGraph(400,400);
$graph->title->Set('Windrose example 0');

//----------------------------------------------------------------
// Create the windrose plot. 
//----------------------------------------------------------------
$wp = new WindrosePlot($data);
$wp->SetSize(0.5);
$graph->Add($wp);

//----------------------------------------------------------------
// Send the graph to the browser
//----------------------------------------------------------------
$graph->Stroke();

?>

