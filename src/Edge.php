<?php

namespace GraPHPy;

use InvalidArgumentException;

/**
 * Class Edge
 * @package GraPHPy
 *
 * @property Vertex $source
 * @property Vertex $sink
 * @property int    $weight
 */
class Edge
{

    private $source;
    private $sink;
    private $weight;

    public function __construct(Vertex $a, Vertex $b, $weight = 1)
    {
        $this->source = $a;
        $this->sink = $b;
        $this->weight = $weight;
    }

    public function __get($k)
    {
        switch ($k) {
            case 'source':
            case 'sink':
            case 'weight':
                return $this->$k;
            default:
                throw new InvalidArgumentException("Invalid property: {$k}");
        }
    }

}
