<?php

require_once 'graph_model.php';

// Parse command-line arguments
$options = getopt("N:S:");
$N = isset($options["N"]) ? $options["N"] : null;
$S = isset($options["S"]) ? $options["S"] : null;

// Validate input
if (!isset($N) || !isset($S) || !is_numeric($N) || !is_numeric($S)) {
    die("Invalid input: Ensure values for N and S are present and numeric.\n");
}

// Create the graph
$N = intval($N);
$S = intval($S);
$model = new GraphModel($N, $S);
$graph = $model->graph;

// Display the generated graph
echo "Generated Graph:\n";
print_r($graph);

// Pick random start and end nodes
$start = array_rand($graph);
$end = array_rand($graph);

// Compute and display shortest path
echo "Shortest Path from $start to $end:\n";
print_r($model->shortestPath($start, $end));

// Compute and display eccentricities, radius, and diameter
echo "Eccentricities:\n";
print_r($model->computeEccentricities());
echo "Radius: " . $model->radius() . "\n";
echo "Diameter: " . $model->diameter() . "\n";
?>
