<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Config;
use Xhgui\Profiler\ProfilingData;

class ProfilingDataTest extends TestCase
{
    public function testExcludeAllEnv()
    {
        $_ENV['TEST_EXCLUDE_ENV'] = 'TEST';

        $config = new Config([
            'profiler.is-exclude-all-env' => true,
        ]);
        $profilingData = new ProfilingData($config);

        $profile = ['example' => 'data'];
        $result = $profilingData->getProfilingData($profile);


        $this->assertEmpty($result['meta']['env']);
    }

    public function testNotExcludeAllEnv()
    {
        $_ENV['TEST_EXCLUDE_ENV'] = 'TEST';

        $config = new Config([
            'profiler.is-exclude-all-env' => false,
        ]);
        $profilingData = new ProfilingData($config);

        $profile = ['example' => 'data'];
        $result = $profilingData->getProfilingData($profile);

        $this->assertEquals('TEST', $result['meta']['env']['TEST_EXCLUDE_ENV']);
    }


}
