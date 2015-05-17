<?php

namespace GraPHPy;

class GraphAlgorithms
{

    /**
     * Traverse the graph using DFS to detect cycles
     * @param Graph $g
     * @param mixed $x0
     * @return boolean Whether the graph contains cycles
     */
    public static function containsCycle(Graph $g, $x0 = null)
    {
        //We are assuming that the graph is directed
        if (!$g->directed) {
            //If it is not, it is cyclic if it has edges
            return $g->size > 0;
        }

        $cyclic = false;
        $func = function (Vertex $v, array $discovered, array $recursionStack) use (&$cyclic) {
            $adjacentVertices = $v->getAdjacentVertices();

            //Loop edge
            if (in_array($v, $adjacentVertices)) {
                $cyclic = true;
            }

            //This is needed for directed graphs
            if (array_intersect($adjacentVertices, $recursionStack) !== []) {
                $cyclic = true;
            }

            //Stop traversing if the graph is cyclic
            return !$cyclic;
        };

        if ($x0 === null) {
            $vertices = $g->getSourceVertices();
            if (empty($vertices)) {
                //If the graph has no source vertices
                //the graph is cyclic
                return true;
            }

            //The graph should be checked from every source vertex
            //There may be cyclic subgraphs that could be missed otherwise
            foreach ($vertices as $vertex) {
                static::dfs($g, $vertex->label, $func);
            }
        } else {
            //A specific vertex is given - check the reachable subgraph only
            static::dfs($g, $x0, $func);
        }

        return $cyclic;
    }

    /**
     * @param Graph $g
     * @return boolean Whether the graph is connected
     */
    public static function isConnected(Graph $g)
    {
        // the null graph and singleton graph are considered connected
        if ($g->order <= 1) {
            return true;
        }

        // empty graphs on n>=2 nodes are disconnected
        if ($g->empty) {
            return false;
        }

        $vertices = $g->getVertices();

        $source = reset($vertices);
        $x0 = $source->label;

        if ($g->directed) {
            $undirectedG = new Graph();
            foreach ($vertices as $vertex) {
                $undirectedG->addVertex($vertex->label);
            }
            foreach ($vertices as $vertex) {
                foreach ($vertex->getDiscoveryEdges() as $edge) {
                    $undirectedG->addEdge($vertex->label, $edge->sink->label);
                }
            }

            $g = $undirectedG;
        }

        return count(self::dfs($g, $x0)) == $g->order;
    }

    /**
     * @param Graph $g
     * @param mixed $vertex
     * @param callable $callback A function to be called on each vertex
     *      If the function returns false, the graph is not traversed further
     * @return \GraPHPy\Vertex[]
     */
    public static function dfs(Graph $g, $vertex, $callback = null)
    {
        $stack = [[$g->getVertex($vertex)]];

        $callbackIsCallable = is_callable($callback);
        $discovered = [];

        $recursionStack = [];

        while (!empty($stack)) {
            $vertices = array_pop($stack);
            if (empty($vertices)) {
                array_pop($recursionStack);
            } else {
                $currentVertex = array_pop($vertices);
                array_push($stack, $vertices);

                if (in_array($currentVertex, $discovered)) {
                    continue;
                }
                $discovered[] = $currentVertex;

                if ($callbackIsCallable) {
                    if (!$callback($currentVertex, $discovered, $recursionStack)) {
                        break;
                    }
                }

                array_push($recursionStack, $currentVertex);
                array_push($stack, $currentVertex->getAdjacentVertices());
            }
        }

        return $discovered;
    }

    public static function reachable(Graph $g, $source, $sink)
    {
        $reachable = self::dfs($g, $source);
        return in_array($sink, $reachable, true);
    }

}
