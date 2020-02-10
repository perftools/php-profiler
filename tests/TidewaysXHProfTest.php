<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\TidewaysXHProf;
use Xhgui\Profiler\ProfilingFlags;

/**
 * @requires extension tideways_xhprof
 */
class TidewaysXHProfTest extends TestCase
{
    public function setUp()
    {
        $this->profiler = new TidewaysXHProf();
    }

    public function testDefaults()
    {
        $data = $this->runProfiler();
        $this->assertCount(3, $data);
    }

    public function testNoFlags()
    {
        $flags = array(
            ProfilingFlags::NO_BUILTINS,
        );
        $data = $this->runProfiler($flags);
        $this->assertCount(2, $data);
    }
}