<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\UProfiler;

/**
 * @requires extension uprofiler
 */
class UProfilerTest extends TestCase
{
    public function testDefaults()
    {
        $profiler = new UProfiler();
        $profiler->enableWith();
        $data = $profiler->disable();
        $this->assertNotEmpty($data);
    }
}
