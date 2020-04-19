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

    protected function getSample($sampleName)
    {
        $file = __DIR__ . '/Resources/' . $sampleName;
        $this->assertFileExists($file);
        $contents = file_get_contents($file);
        $this->assertNotEmpty($contents);
        $sample = json_decode($contents, true);
        $this->assertNotEmpty($sample);

        return $sample;
    }

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
