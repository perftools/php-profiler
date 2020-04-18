<?php

namespace Xhgui\Profiler\Profilers;

interface ProfilerInterface
{
    /**
     * @return bool
     */
    public function isSupported();

    /**
     * Enable the specific profiler
     *
     * @param array $flags
     * @param array $options
     */
    public function enableWith($flags = array(), $options = array());

    /**
     * Disable (stop) the profiler. Return the collected data
     *
     * @return array
     */
    public function disable();

    /**
     * Map generic Xhgui\Profiler\ProfilingFlags to {SPECIFIC_PROFILER_NAME_HERE} implementation
     *
     * @return array - array with the structure [generic_flag => specific_profiler_flag],
     *                                      e.g. [ProfilingFlags::CPU => XHPROF_FLAGS_CPU]
     */
    public function getProfileFlagMap();
}
