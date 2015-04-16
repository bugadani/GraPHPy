<?php
use GraPHPy\Graph;
use GraPHPy\GraphAlgorithms;

class GraphAlgorithmsTest extends PHPUnit_Framework_TestCase
{

    public function cyclicProvider()
    {
        return [
            [
                false,
                new Graph([1, 2, 3], [
                    [1, 2],
                    [2, 3],
                    [1, 3]
                ], true)
            ],
            [
                true,
                new Graph([1, 2, 3], [
                    [1, 2],
                    [2, 3],
                    [3, 1]
                ], true)
            ],
            [
                true,
                new Graph([1, 2, 3], [
                    [1, 2],
                    [3, 3],
                    [3, 2]
                ], true)
            ]
        ];
    }

    /**
     * @dataProvider cyclicProvider
     * @param bool $isCyclic
     * @param Graph $graph
     */
    public function testCyclic($isCyclic, Graph $graph)
    {
        $this->assertEquals($isCyclic, GraphAlgorithms::containsCycle($graph));
    }

    public function testConnectedNotDirected()
    {
        $connected = new Graph([1, 2, 3], [
            [1, 2],
            [2, 3]
        ]);
        $notConnected = new Graph([1, 2, 3], [[1, 2]]);

        $this->assertFalse(GraphAlgorithms::isConnected($notConnected));
        $this->assertTrue(GraphAlgorithms::isConnected($connected));
    }

    public function testConnectedDirected()
    {
        $connected = new Graph([1, 2, 3], [
            [1, 2],
            [2, 3]
        ], true);
        $notConnected = new Graph([1, 2, 3], [[1, 2]], true);

        $this->assertFalse(GraphAlgorithms::isConnected($notConnected));
        $this->assertTrue(GraphAlgorithms::isConnected($connected));
    }

    public function testDFS()
    {
        $edges = [
            [1, 2],
            [1, 3],
            [2, 5],
            [3, 3],
            [3, 4],
            [4, 2]
        ];
        $undirected = new Graph([1, 2, 3, 4, 5], $edges);
        $directed = new Graph([1, 2, 3, 4, 5], $edges, true);

        $this->assertEquals([
            $directed->getVertex(1),
            $directed->getVertex(3),
            $directed->getVertex(4),
            $directed->getVertex(2),
            $directed->getVertex(5)
        ], GraphAlgorithms::dfs($directed, 1));

        $this->assertEquals([
            $undirected->getVertex(1),
            $undirected->getVertex(3),
            $undirected->getVertex(4),
            $undirected->getVertex(2),
            $undirected->getVertex(5)
        ], GraphAlgorithms::dfs($undirected, 1));

        $this->assertEquals([
            $undirected->getVertex(5),
            $undirected->getVertex(2),
            $undirected->getVertex(4),
            $undirected->getVertex(3),
            $undirected->getVertex(1)
        ], GraphAlgorithms::dfs($undirected, 5));

        $this->assertEquals([
            $directed->getVertex(5)
        ], GraphAlgorithms::dfs($directed, 5));
    }
}
