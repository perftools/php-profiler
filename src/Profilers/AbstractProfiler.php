<?php

namespace Xhgui\Profiler\Profilers;

abstract class AbstractProfiler
{
    /**
     * Enable the specific profiler
     *
     * @param array $flags
     * @param array $options
     */
    abstract public function enableWith($flags = array(), $options = array());

    /**
     * Disable (stop) the profiler. Return the collected data
     *
     * @return array
     */
    abstract public function disable();

    /**
     * Map generic Xhgui\Profiler\ProfilingFlags to {SPECIFIC_PROFILER_NAME_HERE} implementation
     *
     * @return array - array with the structure [generic_flag => specific_profiler_flag],
     *                                      e.g. [ProfilingFlags::CPU => XHPROF_FLAGS_CPU]
     */
    abstract protected function getProfileFlagMap();

    /**
     * Combines flags using bitwise OR
     *
     * @param $flags
     * @return int
     */
    protected function combineFlags($flags)
    {
        $combinedFlag = 0;

        $flagMap = $this->getProfileFlagMap();
        foreach ($flags as $flag) {
            $mappedFlag = array_key_exists($flag, $flagMap) ? $flagMap[$flag] : $flag;
            $combinedFlag |= $mappedFlag;
        }

        return $combinedFlag;
    }
}
