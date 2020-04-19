<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profiler;

/**
 * Test the full suite, i.e the library and integration
 */
class SuiteTest extends TestCase
{
    /** @var Profiler */
    private $xhguiProfiler;

    public function setUp()
    {
        $config = array(
            'profiler.enable' => function () {
                return true;
            },
            'save.handler' => 'file',
            'save.handler.filename' => sys_get_temp_dir() . '/php-profiler-test-save.json',
        );
        $this->xhguiProfiler = new Profiler($config);
    }

    public function testEmptyRun()
    {
        $profiler = $this->xhguiProfiler;
        $profiler->enable();
        $profiler->stop();
        $this->assertFalse($profiler->isRunning());
    }
}
