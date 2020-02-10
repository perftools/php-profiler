<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\XHProf;

/**
 * @requires extension xhprof
 */
class XHProfTest extends TestCase
{
    public function testDefaults()
    {
        $profiler = new XHProf();
        $profiler->enableWith();
        $data = $profiler->disable();
        $this->assertNotEmpty($data);
    }
}
