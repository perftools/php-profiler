<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

class UProfiler extends AbstractProfiler
{
    /**
     * {@inheritdoc}
     */
    public function enableWith($flags = array(), $options = array())
    {
        uprofiler_enable($this->combineFlags($flags), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        return uprofiler_disable();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileFlagMap()
    {
        return array(
            ProfilingFlags::CPU => UPROFILER_FLAGS_CPU,
            ProfilingFlags::MEMORY => UPROFILER_FLAGS_MEMORY,
            ProfilingFlags::NO_BUILTINS => UPROFILER_FLAGS_NO_BUILTINS,
        );
    }
}
