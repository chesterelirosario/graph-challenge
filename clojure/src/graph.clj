(ns graph)

(defn create-graph [n s]
  (when (< s (dec n))
    (throw (Exception. "S must be at least N-1 to ensure the graph connectivity.")))
  
  (when (> s (* n (dec n)))
    (throw (Exception. "S exceeds the number of maximum possible edges: N(N-1).")))

  (let [nodes (shuffle (range 1 (inc n)))
        graph (zipmap (range 1 (inc n)) (repeat {}))
        connected [(first nodes)]]

    ;; Ensure connectivity for all nodes
    (loop [i 1, graph graph, connected connected]
      (if (< i n)
        (let [from (rand-nth connected)
              to (nth nodes i)
              weight (inc (rand-int 5))]
          (recur (inc i) (update graph from assoc to weight) (conj connected to)))
        
        ;; Add remaining edges
        (loop [graph graph]
          (if (< (reduce + (map count (vals graph))) s)
            (let [from (inc (rand-int n))
                  to (inc (rand-int n))
                  weight (inc (rand-int 5))]
              (if (and (not= from to) (not (contains? (graph from) to)))
                (recur (update graph from assoc to weight))
                (recur graph)))
            graph))))))

(defn shortest-path-result [distances end previous]
  {:distance (distances end)
   :path (if (= (distances end) ##Inf) []
             (reverse (loop [path [], at end]
                        (if (nil? at) path
                            (recur (conj path at) (previous at))))))})

(defn shortest-path [graph start end]
  (let [nodes (keys graph)
        distances (into {} (map #(vector % ##Inf) nodes))
        previous (into {} (map #(vector % nil) nodes))
        queue (sorted-set [0 start])]

    (loop [distances (assoc distances start 0)
           previous previous
           queue queue]
      (if (empty? queue)
        (shortest-path-result distances end previous)

        (let [[curr-dist current] (first queue)
              queue (disj queue [curr-dist current])]

          (if (= current end)
            (shortest-path-result distances end previous)

            ;; Explore neighbors
            (letfn [(update-state [[dists prev q] [neighbor weight]]
                      (let [new-dist (+ curr-dist weight)]
                        (if (< new-dist (dists neighbor))
                          [(assoc dists neighbor new-dist)
                           (assoc prev neighbor current)
                           (conj q [new-dist neighbor])]
                          [dists prev q])))]

              (let [[new-distances new-previous new-queue]
                    (reduce update-state [distances previous queue] (graph current))]
                (recur new-distances new-previous new-queue)))))))))

(defn eccentricity [graph node]
  (let [nodes (keys graph)
        max-distance
        (reduce
         (fn [max-dist target]
           (if (not= node target)
             (let [{:keys [distance]} (shortest-path graph node target)]
               (if (not= distance ##Inf)
                 (max max-dist distance)
                 max-dist))
             max-dist))
         0
         nodes)]
    (if (= max-distance 0) ##Inf max-distance)))

(defn compute-eccentricities [graph]
  (into {} (map (fn [node] [node (eccentricity graph node)]) (keys graph))))

(defn radius [graph]
  (apply min (vals (compute-eccentricities graph))))

(defn diameter [graph]
  (apply max (vals (compute-eccentricities graph))))