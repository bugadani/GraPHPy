<?php

use GraPHPy\Graph;

class VertexTest extends PHPUnit_Framework_TestCase
{
    public function testAdjacentVertices()
    {
        $graph = new Graph([1, 2, 3], [
            [1, 2],
            [1, 3],
            [2, 3]
        ], true);

        $this->assertCount(2, $graph->getVertex(1)->getAdjacentVertices());
        $this->assertCount(1, $graph->getVertex(2)->getAdjacentVertices());
        $this->assertCount(0, $graph->getVertex(3)->getAdjacentVertices());

        $this->assertSame("2", (string)$graph->getVertex(2));

        $undirected = new Graph([1, 2, 3], [
            [1, 2],
            [1, 3],
            [2, 3]
        ]);

        $this->assertCount(2, $undirected->getVertex(1)->getAdjacentVertices());
        $this->assertCount(2, $undirected->getVertex(2)->getAdjacentVertices());
        $this->assertCount(2, $undirected->getVertex(3)->getAdjacentVertices());

        $undirected = new Graph([1], [
            [1, 1]
        ]);

        $this->assertCount(1, $undirected->getVertex(1)->getAdjacentVertices());
    }

    public function testDiscoveryEdges()
    {
        $graph = new Graph([1, 2, 3], [
            [1, 2],
            [1, 3],
            [2, 3]
        ], true);

        $this->assertCount(2, $graph->getVertex(1)->getDiscoveryEdges());
        $this->assertCount(1, $graph->getVertex(2)->getDiscoveryEdges());
        $this->assertCount(0, $graph->getVertex(3)->getDiscoveryEdges());

        $undirected = new Graph([1, 2, 3], [
            [1, 2],
            [1, 3],
            [2, 3]
        ]);

        $this->assertCount(2, $undirected->getVertex(1)->getDiscoveryEdges());
        $this->assertCount(2, $undirected->getVertex(2)->getDiscoveryEdges());
        $this->assertCount(2, $undirected->getVertex(3)->getDiscoveryEdges());
    }
}
