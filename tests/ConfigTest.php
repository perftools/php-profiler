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

    public function testLoadConfig()
    {
        $config = new Config();
        $config->load(__DIR__ . '/Resources/config_saver.php');
        $this->assertEquals(Profiler::SAVER_UPLOAD, $config['save.handler']);
    }

    public function testFromFile()
    {
        $config = Config::create();
        $this->assertEquals(Profiler::SAVER_STACK, $config['save.handler']);
    }

    /**
     * @expectedException \Xhgui\Profiler\Exception\ProfilerException
     * @expectedExceptionMessage Config did not return an array
     */
    public function testBadConfig()
    {
        $config = new Config();
        $config->load(__DIR__ . '/Resources/config_bad_return.php');
    }
}
