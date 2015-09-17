<?php

namespace GraPHPy;

/**
 * Simple class that represents a mathematical graph
 *
 * @property int  $order
 * @property int  $size
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
    private $size  = 0;

    /**
     * Whether the graph is directed
     *
     * @var bool
     */
    private $directed;
    private $sourceVertices = [];
    private $sinkVertices   = [];

    public function __construct(array $vertices = [], array $edges = [], $directed = false)
    {
        $this->directed = (bool)$directed;
        array_walk($vertices, [$this, 'addVertex']);
        foreach ($edges as $edge) {
            list($source, $sink) = $edge;
            $this->addEdge($source, $sink);
        }
    }

    /**
     * @param     $v1
     * @param     $v2
     * @param int $weight
     */
    public function addEdge($v1, $v2, $weight = 1)
    {
        $vertex1 = $this->getVertex($v1);
        $vertex2 = $this->getVertex($v2);

        $this->edges[ $v1 ][] = new Edge($vertex1, $vertex2, $weight);
        if ($this->directed) {
            if (!isset($this->sinkVertices[ $v1 ])) {
                $this->sourceVertices[ $v1 ] = $vertex1;
            }

            if ($v1 !== $v2) {
                if (!isset($this->sourceVertices[ $v2 ])) {
                    $this->sinkVertices[ $v2 ] = $vertex2;
                }
                unset($this->sourceVertices[ $v2 ]);
                unset($this->sinkVertices[ $v1 ]);
            }
        } else {
            $this->edges[ $v2 ][] = new Edge($vertex2, $vertex1, $weight);
        }
        $this->size++;
    }

    /**
     * @param $vertex
     *
     * @return Vertex
     */
    public function getVertex($vertex)
    {
        if (!isset($this->vertices[ $vertex ])) {
            throw new \OutOfBoundsException("Vertex {$vertex} is not in the graph");
        }

        return $this->vertices[ $vertex ];
    }

    public function __get($k)
    {
        switch ($k) {
            case 'order':
                return count($this->vertices);
            case 'size':
            case 'directed':
                return $this->$k;
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
        if (isset($this->vertices[ $vertex ])) {
            return;
        }

        $this->vertices[ $vertex ] = new Vertex($this, $vertex);
        $this->edges[ $vertex ]    = [];
    }

    /**
     * @param $vertexLabel
     */
    public function removeVertex($vertexLabel)
    {
        if (!isset($this->vertices[ $vertexLabel ])) {
            return;
        }

        $vertex    = $this->vertices[ $vertexLabel ];
        $edgesFrom = $this->edges[ $vertexLabel ];
        $edgesTo   = $this->getEdgesToVertex($vertex);

        /** @var Vertex[] $verticesReachable */
        $verticesReachable = array_map(
            function (Edge $e) {
                return $e->sink;
            },
            $edgesFrom
        );

        /** @var Vertex[] $verticesReaching */
        $verticesReaching = array_map(
            function (Edge $e) {
                return $e->source;
            },
            $edgesTo
        );

        $this->size -= count($edgesFrom);
        $this->size -= count($edgesTo);

        //delete edges to and from vertex
        unset($this->edges[ $vertexLabel ]);
        foreach ($verticesReaching as $v) {
            $this->edges[ $v->label ] = array_filter(
                $this->edges[ $v->label ],
                function (Edge $e) use ($vertex) {
                    return $e->sink !== $vertex;
                }
            );
        }

        //mark source and sink vertices if necessary

        foreach ($verticesReaching as $v) {
            if (empty($this->edges[ $v->label ])) {
                $this->sinkVertices[ $v->label ] = $v;
            }
        }

        foreach ($verticesReachable as $v) {
            if ($v->getInwardEdges() === []) {
                $this->sourceVertices[ $v->label ] = $v;
            }
        }

        unset($this->sourceVertices[ $vertexLabel ]);
        unset($this->sinkVertices[ $vertexLabel ]);

        //delete the vertex
        unset($this->vertices[ $vertexLabel ]);
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
        return $this->edges[ $v->label ];
    }

    public function getEdgesToVertex(Vertex $v)
    {
        $return = [];
        foreach ($this->edges as $vertex => $edges) {
            foreach ($edges as $edge) {
                if ($edge->sink === $v) {
                    $return[] = $edge;
                }
            }
        }

        return $return;
    }

}
