<?php

namespace Xhgui\Profiler\Test\Profiler;

use Xhgui\Profiler\Profilers\SpxProfiler;
use Xhgui\Profiler\Test\TestCase;

/**
 * @requires extension spx
 */
class SpxProfilerTest extends TestCase
{
    public function setUp()
    {
        $this->profiler = new SpxProfiler();
    }

    public function testDefaults()
    {
        $data = $this->runProfiler();
        $this->assertCount(13, $data);
    }
}
