<?php

namespace Xhgui\Profiler\Test\Profiler;

use Xhgui\Profiler\Profilers\XdebugProfiler;
use Xhgui\Profiler\Test\TestCase;

/**
 * @requires extension xdebug
 */
class XdebugProfilerTest extends TestCase
{
    public function setUp()
    {
        $this->profiler = new XdebugProfiler();
    }

    public function testDefaults()
    {
        $data = $this->runProfiler();
        $this->assertCount(13, $data);
    }
}
