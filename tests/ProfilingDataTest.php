<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Config;
use Xhgui\Profiler\ProfilingData;

class ProfilingDataTest extends TestCase
{
    public function testExcludeAllEnv()
    {
        $config = new Config(array(
            'profiler.exclude-all-env' => true,
        ));
        $profilingData = new ProfilingData($config);

        $profile = array('example' => 'data');
        $result = $profilingData->getProfilingData($profile, $this->createRequestContextObject(array(
            'env' => array('TEST_EXCLUDE_ENV' => 'TEST'),
        )));

        $this->assertEmpty($result['meta']['env']);
    }

    public function testNotExcludeAllEnv()
    {
        $config = new Config(array(
            'profiler.exclude-all-env' => false,
        ));
        $profilingData = new ProfilingData($config);

        $profile = array('example' => 'data');
        $result = $profilingData->getProfilingData($profile, $this->createRequestContextObject(array(
            'env' => array('TEST_EXCLUDE_ENV' => 'TEST'),
        )));

        $this->assertEquals('TEST', $result['meta']['env']['TEST_EXCLUDE_ENV']);
    }
}
