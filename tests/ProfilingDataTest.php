<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Config;
use Xhgui\Profiler\ProfilingData;

class ProfilingDataTest extends TestCase
{
    public function testExcludeAllEnv()
    {
        $config = new Config([
            'profiler.is-exclude-all-env' => true
        ]);
        $profilingData = new ProfilingData($config);

        $profile = ['example' => 'data'];
        $result = $profilingData->getProfilingData($profile);


        $this->assertEmpty($result['meta']['env']);
    }


}
