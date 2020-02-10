<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\Tideways;

/**
 * @requires extension tideways
 */
class TidewaysTest extends TestCase
{
    public function testDefaults()
    {
        $profiler = new Tideways();
        $profiler->enableWith();
        $data = $profiler->disable();
        $this->assertNotEmpty($data);
    }
}
