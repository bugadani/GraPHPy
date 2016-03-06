<?php

namespace GraPHPy;

/**
 * Simple class that represents a mathematical graph
 *
 * @property int $order
 * @property int $size
 * @property bool $directed
 * @property bool $empty
 */
class Graph
{

    /**
     * An array with vertices
     * Structure: [label => Vertex]
     *
     * @var Vertex[]
     */
    private $vertices = [];

    /**
     * A map with the structure of sourceVertexLabel => Edge object
     *
     * @var Edge[][]
     */
    private $edges = [];
    private $size = 0;

    /**
     * Whether the graph is directed
     *
     * @var bool
     */
    private $directed;
    private $sourceVertices = [];
    private $sinkVertices = [];

    public function __construct(array $vertices = [], array $edges = [], $directed = false)
    {
        $this->directed = (bool)$directed;
        array_walk($vertices, [$this, 'addVertex']);
        foreach ($edges as $edge) {
            list($source, $sink) = $edge;
            $this->getVertex($source)->addOutEdgeToId($sink);
        }
    }

    private function updateSource(Vertex $v)
    {
        if ($v->isSource) {
            $this->sourceVertices[$v->label] = $v;
        } else {
            unset($this->sourceVertices[$v->label]);
        }
    }

    private function updateSink(Vertex $v)
    {
        if ($v->isSink) {
            $this->sinkVertices[$v->label] = $v;
        } else {
            unset($this->sinkVertices[$v->label]);
        }
    }

    public function addEdge(Edge $e)
    {
        $this->edges[$e->source->label][] = $e;

        $this->updateSource($e->source);
        $this->updateSource($e->sink);

        $this->updateSink($e->source);
        $this->updateSink($e->sink);

        $this->size++;
    }

    /**
     * @param $vertex
     *
     * @return Vertex
     */
    public function getVertex($vertex)
    {
        if (!isset($this->vertices[$vertex])) {
            throw new \OutOfBoundsException("Vertex {$vertex} is not in the graph");
        }

        return $this->vertices[$vertex];
    }

    public function __get($k)
    {
        switch ($k) {
            case 'order':
                return count($this->vertices);
            case 'size':
                return $this->directed ? $this->size : ($this->size / 2);
            case 'directed':
                return $this->directed;
            case 'empty':
                return $this->size == 0;
            default:
                throw new \InvalidArgumentException("Invalid property: {$k}");
        }
    }

    /**
     * @param $vertex
     */
    public function addVertex($vertex)
    {
        if (!isset($this->vertices[$vertex])) {
            $this->vertices[$vertex] = new Vertex($this, $vertex);
            $this->edges[$vertex] = [];
        }
    }

    /**
     * @param $vertexLabel
     */
    public function removeVertex($vertexLabel)
    {
        if (!isset($this->vertices[$vertexLabel])) {
            return;
        }

        $vertex = $this->vertices[$vertexLabel];
        $edgesFrom = $vertex->getDiscoveryEdges();
        $edgesTo = $vertex->getInwardEdges();

        $deleteEdge = function(Edge $e) {
            $e->source->removeEdge($e);
            $e->sink->removeEdge($e);
            $key = array_search($e, $this->edges[$e->source->label], true);
            unset($this->edges[$e->source->label][$key]);
        };

        //Delete edges to vertex
        foreach ($edgesTo as $edge) {
            $deleteEdge($edge);
            $this->updateSource($edge->source);
            $this->updateSink($edge->source);
        }

        //Delete edges from vertex
        foreach ($edgesFrom as $edge) {
            $deleteEdge($edge);
            $this->updateSource($edge->sink);
            $this->updateSink($edge->sink);
        }

        unset($this->edges[$vertexLabel]);

        $this->size -= count($edgesTo);
        $this->size -= count($edgesFrom);

        $sourceKey = array_search($vertex, $this->sourceVertices, true);
        $sinkKey = array_search($vertex, $this->sinkVertices, true);

        if ($sourceKey !== false) {
            unset($this->sourceVertices[$sourceKey]);
        }
        if ($sinkKey !== false) {
            unset($this->sinkVertices[$sinkKey]);
        }

        //delete the vertex
        unset($this->vertices[$vertexLabel]);
    }

    /**
     * @return Vertex[]
     */
    public function getVertices()
    {
        return array_values($this->vertices);
    }

    /**
     * @return Vertex[]
     */
    public function getSourceVertices()
    {
        return $this->sourceVertices;
    }

    /**
     * @return Vertex[]
     */
    public function getSinkVertices()
    {
        return $this->sinkVertices;
    }

    public function getEdgesFromVertex(Vertex $v)
    {
        return $this->edges[$v->label];
    }

    public function getEdgesToVertex(Vertex $v)
    {
        return $v->getInwardEdges();
    }

    public function getEdges()
    {
        return $this->edges;
    }
}
