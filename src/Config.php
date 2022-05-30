<?php

namespace Xhgui\Profiler;

use ArrayAccess;

class Config implements ArrayAccess
{
    /** @var array */
    private $config;

    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }
}
