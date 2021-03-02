<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-hal for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-hal/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-hal/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Hal\TestAsset;

use Laminas\Stdlib\JsonSerializable as JsonSerializableInterface;

class JsonSerializable implements JsonSerializableInterface
{
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return ['foo' => 'bar'];
    }
}
