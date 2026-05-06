<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Config;
use Xhgui\Profiler\ProfilingData;

class ProfilingDataTest extends TestCase
{
    public function testExcludeAllEnv()
    {
        // 'REQUEST_TIME_FLOAT' isn't available before 5.4.0
        // https://www.php.net/manual/en/reserved.variables.server.php
        if (!isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
        }

        $_ENV['TEST_EXCLUDE_ENV'] = 'TEST';

        $config = new Config(array(
            'profiler.exclude-all-env' => true,
        ));
        $profilingData = new ProfilingData($config);

        $profile = array('example' => 'data');
        $result = $profilingData->getProfilingData($profile);

        $this->assertEmpty($result['meta']['env']);
    }

    public function testNotExcludeAllEnv()
    {
        $_ENV['TEST_EXCLUDE_ENV'] = 'TEST';

        $config = new Config(array(
            'profiler.exclude-all-env' => false,
        ));
        $profilingData = new ProfilingData($config);

        $profile = array('example' => 'data');
        $result = $profilingData->getProfilingData($profile);

        $this->assertEquals('TEST', $result['meta']['env']['TEST_EXCLUDE_ENV']);
    }
}
