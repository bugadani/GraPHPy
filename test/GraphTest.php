<?php

use GraPHPy\Graph;

class GraphTest extends PHPUnit_Framework_TestCase
{
    public function testVerticesGetCreated()
    {
        $graph = new Graph([1, 2, 3, 4], [], true);

        $this->assertCount(4, $graph->getVertices());
        $this->assertEquals(4, $graph->order);
        $this->assertTrue($graph->directed);

        return $graph;
    }

    /**
     * @depends testVerticesGetCreated
     * @param Graph $graph
     * @return \GraPHPy\Graph
     */
    public function testVerticesHaveCorrectLabels(Graph $graph)
    {
        $labels = [1, 2, 3, 4];
        foreach ($graph->getVertices() as $vertex) {
            $this->assertInstanceOf('\GraPHPy\Vertex', $vertex);
            $this->assertTrue(in_array($vertex->label, $labels));
            unset($labels[array_search($vertex->label, $labels, true)]);
        }

        //Check if all labels have been checked
        $this->assertEmpty($labels);

        return $graph;
    }

    /**
     * @depends testVerticesHaveCorrectLabels
     * @param Graph $graph
     * @return \GraPHPy\Graph
     */
    public function testEdges(Graph $graph)
    {
        $graph->addEdge(1, 2, 1);
        $graph->addEdge(2, 3, 2);

        $this->assertEquals(2, $graph->size);

        return $graph;
    }

    /**
     * @depends testEdges
     * @param Graph $graph
     */
    public function testEdgesCanBeTraversed(Graph $graph)
    {
        $vertex1 = $graph->getVertex(1);

        $v2 = $vertex1->getAdjacentVertices();

        $this->assertCount(1, $v2);
        $vertex2 = $v2[0];
        $this->assertEquals(2, $vertex2->label);

        $v3 = $vertex2->getAdjacentVertices();

        $this->assertCount(1, $v3);
        $vertex3 = $v3[0];
        $this->assertEquals(3, $vertex3->label);
    }
}
