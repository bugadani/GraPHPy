<?php

namespace GraPHPy;

/**
 * Simple class that represents a mathematical graph
 *
 * @property int $order
 * @property int $size
 * @property bool $directed
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
     * @var bool
     */
    private $directed;
    private $sourceVertices = [];

    public function __construct(array $vertices = [], array $edges = [], $directed = false)
    {
        $this->directed = (bool)$directed;
        array_walk($vertices, [$this, 'addVertex']);
        foreach ($edges as $source => $sink) {
            $this->addEdge($source, $sink);
        }
    }

    public function __get($k)
    {
        switch ($k) {
            case 'order':
                return count($this->vertices);
            case 'size':
            case 'directed':
                return $this->$k;
            default:
                throw new \InvalidArgumentException("Invalid property: {$k}");
        }
    }

    /**
     * @param $v1
     * @param $v2
     * @param int $weight
     */
    public function addEdge($v1, $v2, $weight = 1)
    {
        $vertex1 = $this->getVertex($v1);
        $vertex2 = $this->getVertex($v2);

        if ($this->directed) {
            unset($this->sourceVertices[$v1]);
        } else {
            $this->edges[$v2][] = new Edge($vertex2, $vertex1, $weight);
        }
        $this->edges[$v1][] = new Edge($vertex1, $vertex2, $weight);
        $this->size++;
    }

    /**
     * @param $vertex
     * @return Vertex
     */
    public function getVertex($vertex)
    {
        if (!isset($this->vertices[$vertex])) {
            throw new \OutOfBoundsException("Vertex {$vertex} is not in the graph");
        }
        return $this->vertices[$vertex];
    }

    /**
     * @param $vertex
     */
    public function addVertex($vertex)
    {
        if (isset($this->vertices[$vertex])) {
            return;
        }

        $this->vertices[$vertex] = new Vertex($this, $vertex);
        $this->edges[$vertex] = [];

        if ($this->directed) {
            $this->sourceVertices[$vertex] = $this->vertices[$vertex];
        }
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

    public function getEdgesFromVertex(Vertex $v)
    {
        return $this->edges[$v->label];
    }

}
