<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

/**
 * A simple & straight-to-the-point PHP profiling extension
 *
 * @see https://github.com/NoiseByNorthwest/php-spx
 */
class SpxProfiler implements ProfilerInterface
{
    const EXTENSION_NAME = 'spx';

    public function isSupported()
    {
        return extension_loaded(self::EXTENSION_NAME);
    }

    public function enable($flags = array(), $options = array())
    {
        spx_profiler_start();
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        return spx_profiler_stop();
    }
}
