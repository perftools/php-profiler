<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\Tideways;

/**
 * @requires extension tideways
 */
class TidewaysTest extends TestCase
{
    public function testLoad()
    {
        $profiler = new Tideways();
        $profiler->enableWith();
    }
}
