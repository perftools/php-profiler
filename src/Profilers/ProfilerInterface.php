<?php

namespace Xhgui\Profiler\Profilers;

interface ProfilerInterface
{
    /**
     * @return bool
     */
    public function isSupported();

    /**
     * Enable profiling.
     */
    public function enable();

    /**
     * Disable (stop) the profiler. Return the collected data.
     *
     * @return array
     */
    public function disable();
}
