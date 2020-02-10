<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\XHProf;

/**
 * @requires extension xhprof
 */
class XHProfTest extends TestCase
{
    public function testLoad()
    {
        $profiler = new XHProf();
        $profiler->enableWith();
    }
}
