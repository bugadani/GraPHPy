<?php

namespace GraPHPy;

use InvalidArgumentException;

/**
 * Class Vertex
 * @package GraPHPy
 *
 * @property $label
 * @property $isSource
 * @property $isSink
 */
class Vertex
{
    /**
     * @var Graph
     */
    private $graph;
    private $label;
    private $inEdges = [];
    private $outEdges = [];

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
            case 'inEdges':
            case 'outEdges':
                return count($this->$k);
            case 'isSource':
                return empty($this->inEdges);
            case 'isSink':
                return empty($this->outEdges);
            default:
                throw new InvalidArgumentException("Invalid property: {$k}");
        }
    }

    public function addInEdgeFrom(Vertex $vertex, $weight = 1)
    {
        $edge = new Edge($vertex, $this, $weight);
        $this->inEdges[] = $edge;
        $vertex->outEdges[] = $edge;

        $this->graph->addEdge($edge);

        if (!$this->graph->directed) {
            $edge = new Edge($this, $vertex, $weight);
            $vertex->inEdges[] = $edge;
            $this->outEdges[] = $edge;

            $this->graph->addEdge($edge);
        }
    }

    public function addInEdgeFromId($vertexId, $weight = 1)
    {
        $this->addInEdgeFrom($this->graph->getVertex($vertexId), $weight);
    }

    public function addOutEdgeTo(Vertex $vertex, $weight = 1)
    {
        $edge = new Edge($this, $vertex, $weight);
        $this->outEdges[] = $edge;
        $vertex->inEdges[] = $edge;

        $this->graph->addEdge($edge);

        if (!$this->graph->directed) {
            $edge = new Edge($vertex, $this, $weight);
            $vertex->outEdges[] = $edge;
            $this->inEdges[] = $edge;

            $this->graph->addEdge($edge);
        }
    }

    public function addOutEdgeToId($vertexId, $weight = 1)
    {
        $this->addOutEdgeTo($this->graph->getVertex($vertexId), $weight);
    }

    /**
     * @return Edge[]
     */
    public function getDiscoveryEdges()
    {
        return $this->outEdges;
    }

    /**
     * @return Edge[]
     */
    public function getInwardEdges()
    {
        return $this->inEdges;
    }

    /**
     * @return Vertex[]
     */
    public function getAdjacentVertices()
    {
        return array_unique(
            array_values(
                array_map(
                    function (Edge $edge) {
                        return $edge->sink;
                    },
                    $this->outEdges
                )
            )
        );
    }

    /**
     * @return Vertex[]
     */
    public function getReachingVertices()
    {
        return array_unique(
            array_values(
                array_map(
                    function (Edge $edge) {
                        return $edge->source;
                    },
                    $this->inEdges
                )
            )
        );
    }

    public function removeEdgesTo(Vertex $vertex)
    {
        $this->outEdges = array_filter(
            $this->outEdges,
            function (Edge $e) use ($vertex) {
                return $e->sink !== $vertex;
            }
        );
    }

    public function removeEdgesFrom(Vertex $vertex)
    {
        $this->inEdges = array_filter(
            $this->inEdges,
            function (Edge $e) use ($vertex) {
                return $e->source !== $vertex;
            }
        );
    }

    public function __toString()
    {
        return (string)$this->label;
    }

    public function removeEdge(Edge $e)
    {
        $inKey = array_search($e, $this->inEdges, true);
        $outKey = array_search($e, $this->outEdges, true);

        if ($inKey !== false) {
            unset($this->inEdges[$inKey]);
        }
        if ($outKey !== false) {
            unset($this->outEdges[$outKey]);
        }
    }
}
