<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\ProfilerInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var ProfilerInterface */
    protected $profiler;

    protected function runProfiler($flags = array(), $options = array())
    {
        $this->profiler->enableWith($flags, $options);
        $data = $this->profiler->disable();
        $this->assertNotEmpty($data);

        return $data;
    }
}
