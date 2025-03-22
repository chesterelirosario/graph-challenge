(ns main
  (:require [graph]))

;; Test
(def random-graph (graph/create-graph 4 4))
random-graph

(def random-start (rand-nth (keys random-graph)))
(def random-end (rand-nth (keys random-graph)))
random-start
random-end
(graph/shortest-path random-graph random-start random-end)

(def random-node (rand-nth (keys random-graph)))
random-node
(graph/eccentricity random-graph random-node)

(graph/compute-eccentricities random-graph)
(graph/radius random-graph)
(graph/diameter random-graph)