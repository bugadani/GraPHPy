<?php

namespace GraPHPy;

use InvalidArgumentException;

/**
 * Class Vertex
 * @package GraPHPy
 *
 * @property $label
 */
class Vertex
{

    /**
     * @var Graph
     */
    private $graph;
    private $label;

    public function __construct(Graph $g, $label)
    {
        $this->graph = $g;
        $this->label = $label;
    }

    public function __get($k)
    {
        switch ($k) {
            case 'label':
                return $this->$k;
            default:
                throw new InvalidArgumentException("Invalid property: {$k}");
        }
    }

    /**
     * @return Edge[]
     */
    public function getDiscoveryEdges()
    {
        return $this->graph->getEdgesFromVertex($this);
    }

    /**
     * @return Edge[]
     */
    public function getInwardEdges()
    {
        return $this->graph->getEdgesToVertex($this);
    }

    /**
     * @return Vertex[]
     */
    public function getAdjacentVertices()
    {
        return array_map(
            function (Edge $edge) {
                return $edge->sink;
            },
            $this->getDiscoveryEdges()
        );
    }

    /**
     * @return Vertex[]
     */
    public function getReachingVertices()
    {
        return array_map(
            function (Edge $edge) {
                return $edge->source;
            },
            $this->getInwardEdges()
        );
    }

    public function __toString()
    {
        return (string)$this->label;
    }
}
