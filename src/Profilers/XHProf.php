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
    public function getProfileFlagMap()
    {
        /*
         * This is disabled on PHP 5.5+ as it causes a segfault
         *
         * @see https://github.com/perftools/xhgui-collector/commit/d1236d6422bfc42ac212befd0968036986885ccd
         */
        $noBuiltins = PHP_MAJOR_VERSION === 5 && PHP_MINOR_VERSION > 4 ? 0 : XHPROF_FLAGS_NO_BUILTINS;

        return array(
            ProfilingFlags::CPU => XHPROF_FLAGS_CPU,
            ProfilingFlags::MEMORY => XHPROF_FLAGS_MEMORY,
            ProfilingFlags::NO_BUILTINS => $noBuiltins,
        );
    }
}
