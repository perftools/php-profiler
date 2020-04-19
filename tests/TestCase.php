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

    protected function getResource($resourceName)
    {
        return $this->readJsonFile(__DIR__ . '/Resources/' . $resourceName);
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

    protected function readJsonFile($filename)
    {
        $this->assertFileExists($filename);
        $contents = file_get_contents($filename);
        $this->assertNotEmpty($contents);
        $result = json_decode($contents, true);
        $this->assertNotEmpty($result);

        return $result;
    }

    protected function assertExpectedProfilingData(array $data)
    {
        $this->assertArrayHasKey('profile', $data);
        $this->assertArrayHasKey('meta', $data);
    }
}
