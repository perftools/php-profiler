<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Profilers\ProfilerInterface;
use Xhgui\Profiler\Saver\SaverInterface;
use Xhgui\Profiler\SaverFactory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var SaverInterface */
    protected $saver;

    /** @var ProfilerInterface */
    protected $profiler;

    protected function runProfiler($flags = array(), $options = array())
    {
        $this->profiler->enable($flags, $options);
        $data = $this->profiler->disable();
        $this->assertNotEmpty($data);

        return $data;
    }

    protected function createSaver($saveHandler, array $config = array())
    {
        $saver = SaverFactory::create($saveHandler, $config);
        $this->assertNotNull($saver);

        return $saver;
    }
}
