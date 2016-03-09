<?php
//=============================================================================
// File:	ODO_TUTEX08.PHP
// Description: Example 8 for odometer graphs
// Created:	2002-02-22
// Author:      Johan Persson (johanp@aditus.nu)
// Version:	$Id$
// 
// Comment:
// Example file for odometer graph. This examples demonstrates the simplest
// possible graph using all default values for colors, sizes etc.
//
// Copyright (C) 2002 Johan Persson. All rights reserved.
//=============================================================================
include ("../jpgraph.php");
include ("../jpgraph_odo.php");

//---------------------------------------------------------------------
// Create a new odometer graph (width=250, height=200 pixels)
//---------------------------------------------------------------------
$graph = new OdoGraph(250,180);

//---------------------------------------------------------------------
// Add drop shadow for graph
//---------------------------------------------------------------------
$graph->SetShadow();

//---------------------------------------------------------------------
// Now we need to create an odometer to add to the graph.
// By default the scale will be 0 to 100
//---------------------------------------------------------------------
$odo = new Odometer(ODO_HALF); 
$odo->SetColor("lightyellow");
$odo->scale->Set(0,100);

//$odo->caption->Set('Figure 1. This is a test');
//$odo->caption->SetMargin(5);

//---------------------------------------------------------------------
// Add color indications
//---------------------------------------------------------------------
$odo->AddIndication(0,40,"green");
$odo->AddIndication(80,100,"red");

//---------------------------------------------------------------------
// Set display value for the odometer
//---------------------------------------------------------------------
$odo->needle->Set(100);

//---------------------------------------------------------------------
// Add drop shadow for needle
//---------------------------------------------------------------------
//$odo->needle->SetShadow();

//---------------------------------------------------------------------
// Add the odometer to the graph
//---------------------------------------------------------------------
$graph->Add($odo);

//---------------------------------------------------------------------
// ... and finally stroke and stream the image back to the browser
//---------------------------------------------------------------------
$graph->Stroke();

// EOF
?>