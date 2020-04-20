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
        // https://github.com/NoiseByNorthwest/php-spx#available-parameters
        putenv('SPX_ENABLED=1');
        putenv('SPX_REPORT=full');
        putenv('SPX_AUTO_START=0');
        putenv('SPX_TRACE_FILE=/tmp/spx-SPX_TRACE_FILE');
        spx_profiler_start();
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        spx_profiler_stop();

        $key = 'spx-full-20200420_114745-rocinante-7902-16807';

        return $this->readProfile($key);
    }

    private function readProfile($key)
    {
        $profileDir = ini_get('spx.data_dir');
        $json = json_decode(file_get_contents("{$profileDir}/{$key}.json"), true);

        return array_fill(0, $json['call_count'], array());

        return $json;
    }
}
