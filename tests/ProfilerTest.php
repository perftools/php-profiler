<?php

namespace Xhgui\Profiler\Test;

use ReflectionMethod;
use Xhgui\Profiler\Config;
use Xhgui\Profiler\Exception\ProfilerException;
use Xhgui\Profiler\Profiler;
use Xhgui\Profiler\Test\Resources\TestProfilerStub;

class ProfilerTest extends TestCase
{
    public function testGetProfilingDataCachesInstance()
    {
        $profiler = new Profiler(new Config(array()));
        $method = new ReflectionMethod($profiler, 'getProfilingData');
        $method->setAccessible(true);

        $first = $method->invoke($profiler);
        $second = $method->invoke($profiler);

        $this->assertInstanceOf('Xhgui\\Profiler\\ProfilingData', $first);
        $this->assertSame($first, $second);
        $this->assertSame($first, $this->getPrivateProperty($profiler, 'profilingData'));
    }

    public function testDisableClearsStateWhenRequestContextIsMissing()
    {
        $profiler = new Profiler(array());
        $backendProfiler = new TestProfilerStub();

        $this->setPrivateProperty($profiler, 'profiler', $backendProfiler);
        $this->setPrivateProperty($profiler, 'running', true);
        $this->setPrivateProperty($profiler, 'requestContext', null);

        try {
            $profiler->disable();
            $this->fail('Expected missing request context to throw');
        } catch (ProfilerException $exception) {
            $this->assertSame(
                'Unable to disable profiler: Request context is missing',
                $exception->getMessage()
            );
        }

        $this->assertFalse($profiler->isRunning());
        $this->assertNull($this->getPrivateProperty($profiler, 'requestContext'));
        $this->assertSame(1, $backendProfiler->disableCalls);
    }
}
