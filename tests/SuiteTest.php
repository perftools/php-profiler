<?php

namespace Xhgui\Profiler\Test;

use RuntimeException;
use Xhgui\Profiler\Profiler;

/**
 * Test the full suite, i.e the library and integration
 */
class SuiteTest extends TestCase
{
    /** @var Profiler */
    private $xhguiProfiler;
    /** @var string */
    private $profileStorage;

    public function setUp()
    {
        $runId = sprintf('xhgui-test-%f-%04x', microtime(true), mt_rand(1, 0xffff));
        $this->profileStorage = sys_get_temp_dir() . '/php-profiler-' . $runId . '.json';
        $config = array(
            'profiler.enable' => function () {
                return true;
            },
            'save.handler' => 'file',
            'save.handler.filename' => $this->profileStorage,
        );

        try {
            $this->xhguiProfiler = new Profiler($config);
        } catch (RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    public function testEmptyRun()
    {
        $profiler = $this->xhguiProfiler;
        $profiler->enable();
        $profiler->stop();
        $this->assertFalse($profiler->isRunning());
        $profile = $this->readJsonFile($this->profileStorage);
        $this->assertExpectedProfilingData($profile);
    }
}
