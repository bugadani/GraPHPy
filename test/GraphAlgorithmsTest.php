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
                new Graph(
                    [1, 2, 3], [
                    [1, 2],
                    [2, 3],
                    [1, 3]
                ], true
                )
            ],
            [
                true,
                new Graph(
                    [1, 2, 3], [
                    [1, 2],
                    [2, 3],
                    [3, 1]
                ], true
                )
            ],
            [
                true,
                new Graph(
                    [1, 2, 3], [
                    [1, 2],
                    [3, 3],
                    [3, 2]
                ], true
                )
            ],
            [
                true,
                new Graph(
                    [1, 2, 3], [
                    [1, 2],
                    [2, 3],
                    [3, 2]
                ], true
                )
            ],
            [
                true,
                new Graph(
                    [1, 2, 3], [
                    [2, 3],
                    [3, 2]
                ], true
                )
            ],
            [
                false,
                new Graph(
                    [56, 57, 58, 59, 60, 61, 62, 63, 64, 65], [
                    [56, 57],
                    [56, 58],
                    [57, 59],
                    [58, 63],
                    [58, 64],
                    [59, 60],
                    [59, 61],
                    [60, 62],
                    [61, 62],
                    [63, 65],
                    [64, 65],
                    [65, 62]
                ], true
                )
            ]
        ];
    }

    /**
     * @dataProvider cyclicProvider
     *
     * @param bool $isCyclic
     * @param Graph $graph
     */
    public function testCyclic($isCyclic, Graph $graph)
    {
        $this->assertEquals($isCyclic, GraphAlgorithms::containsCycle($graph));
    }

    public function testConnectedNotDirected()
    {
        $connected = new Graph(
            [1, 2, 3], [
                [1, 2],
                [2, 3]
            ]
        );
        $notConnected = new Graph([1, 2, 3], [[1, 2]]);

        $this->assertFalse(GraphAlgorithms::isConnected($notConnected));
        $this->assertTrue(GraphAlgorithms::isConnected($connected));
    }

    public function testConnectedDirected()
    {
        $empty = new Graph([1, 2], [], true);
        $connected = new Graph(
            [1, 2, 3], [
            [1, 2],
            [2, 3]
        ], true
        );
        $notConnected = new Graph([1, 2, 3], [[1, 2]], true);

        $this->assertFalse(GraphAlgorithms::isConnected($empty));
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

        $this->assertEquals(
            [
                $directed->getVertex(1),
                $directed->getVertex(3),
                $directed->getVertex(4),
                $directed->getVertex(2),
                $directed->getVertex(5)
            ],
            GraphAlgorithms::dfs($directed, 1)
        );

        $this->assertEquals(
            [
                $undirected->getVertex(1),
                $undirected->getVertex(3),
                $undirected->getVertex(4),
                $undirected->getVertex(2),
                $undirected->getVertex(5)
            ],
            GraphAlgorithms::dfs($undirected, 1)
        );

        $this->assertEquals(
            [
                $undirected->getVertex(5),
                $undirected->getVertex(2),
                $undirected->getVertex(4),
                $undirected->getVertex(3),
                $undirected->getVertex(1)
            ],
            GraphAlgorithms::dfs($undirected, 5)
        );

        $this->assertEquals(
            [
                $directed->getVertex(5)
            ],
            GraphAlgorithms::dfs($directed, 5)
        );

        $graph = new Graph([1, 2], [[1, 2]], true);

        $visited = [];

        GraphAlgorithms::dfs(
            $graph,
            2,
            function ($v) use (&$visited) {
                $visited[] = $v->label;

                return true;
            }
        );

        $this->assertEquals([2], $visited);
    }
}
