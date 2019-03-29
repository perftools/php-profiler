<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

class XHProf extends AbstractProfiler
{
    /**
     * {@inheritdoc}
     */
    public function enableWith($flags = array(), $options = array())
    {
        if (PHP_MAJOR_VERSION === 5 && PHP_MINOR_VERSION > 4) {
            $flags[] = XHPROF_FLAGS_NO_BUILTINS;
        }

        xhprof_enable($this->combineFlags($flags), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        return xhprof_disable();
    }

    /**
     * {@inheritdoc}
     */
    protected function getProfileFlagMap()
    {
        return array(
            ProfilingFlags::CPU => XHPROF_FLAGS_CPU,
            ProfilingFlags::MEMORY => XHPROF_FLAGS_MEMORY,
        );
    }
}
