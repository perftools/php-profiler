<?php

namespace Xhgui\Profiler\Profilers;

abstract class AbstractProfiler implements ProfilerInterface
{
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
