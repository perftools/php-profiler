<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Config;
use Xhgui\Profiler\Profiler;

class ConfigTest extends TestCase
{
    public function testDefaults()
    {
        $config = new Config();
        $this->assertEquals(Profiler::SAVER_STACK, $config['save.handler']);
    }
}
