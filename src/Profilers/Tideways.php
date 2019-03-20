<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

class Tideways extends AbstractProfiler
{
    /**
     * {@inheritdoc}
     */
    public function enableWith($flags = array(), $options = array())
    {
        if (!in_array(TIDEWAYS_FLAGS_NO_SPANS, $flags, true)) {
            $flags[] = TIDEWAYS_FLAGS_NO_SPANS;
        }

        tideways_enable($this->combineFlags($flags), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        return tideways_disable();
    }

    /**
     * {@inheritdoc}
     */
    protected function getProfileFlagMap()
    {
        return array(
            ProfilingFlags::CPU => TIDEWAYS_FLAGS_CPU,
            ProfilingFlags::MEMORY => TIDEWAYS_FLAGS_MEMORY,
        );
    }
}
