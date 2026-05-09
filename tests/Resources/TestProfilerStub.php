<?php

namespace Xhgui\Profiler\Test\Resources;

use Xhgui\Profiler\Profilers\ProfilerInterface;

class TestProfilerStub implements ProfilerInterface
{
    public $disableCalls = 0;

    public function isSupported()
    {
        return true;
    }

    public function enable($flags = array(), $options = array())
    {
    }

    public function disable()
    {
        $this->disableCalls++;

        return array();
    }
}
