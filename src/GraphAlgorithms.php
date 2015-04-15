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
        $func = function (Vertex $v, array $discovered) use (&$cyclic) {
            $cyclic |= in_array($v, $discovered);

            //Stop traversing if the graph is cyclic
            return !$cyclic;
        };

        if ($x0 === null) {
            $vertices = $g->getSourceVertices();
            if (empty($vertices)) {
                //If the graph has no source vertices but is connected
                //the graph is cyclic
                return static::isConnected($g);
            }

            //The graph should be checked from every source vertex
            //There may be cyclic subgraphs that could be missed otherwise
            foreach (array_keys($vertices) as $vertex) {
                static::dfs($g, $vertex, $func);
            }
        } else {
            //A specific vertex is given - check the reachable subgraph only
            static::dfs($g, $x0, $func);
        }

        return $cyclic;
    }

    /**
     * @param Graph $g
     * @param mixed $x0
     * @return boolean Whether the graph is connected
     */
    public static function isConnected(Graph $g, $x0 = null)
    {
        if ($x0 === null) {
            if ($g->directed) {
                $vertices = $g->getSourceVertices();
            } else {
                $vertices = $g->getVertices();
            }

            $x0 = reset($vertices);
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
        $stack = new \SplStack();
        $stack->push($g->getVertex($vertex));

        $callbackIsCallable = is_callable($callback);
        $discovered = [];

        while (!$stack->isEmpty()) {
            $v = $stack->pop();

            if (in_array($v, $discovered)) {
                continue;
            }
            $discovered[] = $v;
            if ($callbackIsCallable) {
                if (!$callback($v, $discovered)) {
                    break;
                }
            }

            foreach ($v->getAdjacentVertecies() as $adj) {
                $stack->push($adj);
            }
        }

        return $discovered;
    }

}