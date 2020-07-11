<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

class UProfiler extends AbstractProfiler
{
    const EXTENSION_NAME = 'uprofiler';

    /** @var array */
    private $flags;

    /** @var array */
    private $options;

    public function __construct(array $flags, array $options)
    {
        $this->flags = $flags;
        $this->options = $options;
    }

    public function isSupported()
    {
        return extension_loaded(self::EXTENSION_NAME);
    }

    public function enable()
    {
        $flags = $this->combineFlags($this->flags, $this->getProfileFlagMap());

        uprofiler_enable($flags, $this->options);
    }

    public function disable()
    {
        return uprofiler_disable();
    }

    private function getProfileFlagMap()
    {
        return array(
            ProfilingFlags::CPU => UPROFILER_FLAGS_CPU,
            ProfilingFlags::MEMORY => UPROFILER_FLAGS_MEMORY,
            ProfilingFlags::NO_BUILTINS => UPROFILER_FLAGS_NO_BUILTINS,
            ProfilingFlags::NO_SPANS => 0,
        );
    }
}
