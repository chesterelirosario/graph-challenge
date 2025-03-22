<?php

class GraphModel
{
    public array $graph = [];

    public function __construct(int $n, int $s)
    {
        $this->graph = $this->create($n, $s);
    }

    private function create(int $n, int $s): array
    {
        if ($s < $n - 1) {
            die("S must be at least N-1 to ensure the graph connectivity.\n");
        }
    
        if ($s > ($n * ($n - 1))) {
            die("S exceeds the number of maximum possible edges: N(N-1).\n");
        }
    
        $graph = array_fill(1, $n, []);
        $nodes = range(1, $n);
        shuffle($nodes);
        $connected = [$nodes[0]];
    
        // Ensure connectivity for all nodes
        for ($i = 1; $i < $n; $i++) {
            $from = $connected[array_rand($connected)];
            $to = $nodes[$i];
            $graph[$from][$to] = rand(1, 5);
            $connected[] = $to;
        }
    
        // Add remaining edges
        while (array_sum(array_map('count', $graph)) < $s) {
            $from = rand(1, $n);
            $to = rand(1, $n);
    
            // Add new edge if the nodes are equal and does not exist yet
            if ($from != $to && !isset($graph[$from][$to])) {
                $graph[$from][$to] = rand(1, 5);
            }
        }
    
        return $graph;
    }

    public function shortestPath($start, $end): array
    {
        $distances = [];
        $previous = [];
        $queue = new SplPriorityQueue();

        // Initialise distances and priority queue
        foreach ($this->graph as $node => $edges) {
            $distances[$node] = INF;
            $previous[$node] = null;
        }
        $distances[$start] = 0;
        $queue->insert($start, 0);

        while (!$queue->isEmpty()) {
            $current = $queue->extract();

             // Stop if the destination is reached
            if ($current == $end) break;

            // Explore node neighbors
            foreach ($this->graph[$current] as $neighbor => $weight) {
                $distance = $distances[$current] + $weight;
                if ($distance < $distances[$neighbor]) {
                    $distances[$neighbor] = $distance;
                    $previous[$neighbor] = $current;
                    $queue->insert($neighbor, -$distance);
                }
            }
        }

        // Reconstruct shortest path
        $path = [];
        for ($at = $end; $at !== null; $at = $previous[$at]) {
            array_unshift($path, $at);
        }

        return [
            'distance' => $distances[$end],
            'path' => $distances[$end] === INF ? [] : $path
        ];
    }

    public function eccentricity(int $node): int|float
    {
        $maxDistance = 0;

        foreach (array_keys($this->graph) as $target) {
            if ($node !== $target) {
                $result = $this->shortestPath($node, $target);
                if ($result['distance'] !== INF) {
                    $maxDistance = max($maxDistance, $result['distance']);
                }
            }
        }

        return $maxDistance === 0 ? INF : $maxDistance;
    }

    public function computeEccentricities(): array
    {
        $eccentricities = [];

        foreach (array_keys($this->graph) as $node) {
            $eccentricities[] = $this->eccentricity($node);
        }

        return $eccentricities;
    }

    public function radius(): int|float
    {
        return min($this->computeEccentricities());
    }

    public function diameter(): int|float
    {
        return max($this->computeEccentricities());
    }
}