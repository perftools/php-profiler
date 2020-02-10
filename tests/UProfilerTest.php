<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\UProfiler;

/**
 * @requires extension uprofiler
 */
class UProfilerTest extends TestCase
{
    public function testLoad()
    {
        $profiler = new UProfiler();
        $profiler->enableWith();
    }
}
