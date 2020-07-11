<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

class UProfiler extends AbstractProfiler
{
    const EXTENSION_NAME = 'uprofiler';

    /** @var int */
    private $flags;

    /** @var array */
    private $options;

    public function __construct(array $config)
    {
        $this->flags = $this->combineFlags($config['profiler.flags'], $this->getProfileFlagMap());
        $this->options = $config['profiler.options'];
    }

    public function isSupported()
    {
        return extension_loaded(self::EXTENSION_NAME);
    }

    public function enable()
    {
        uprofiler_enable($this->flags, $this->options);
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
