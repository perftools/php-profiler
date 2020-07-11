<?php

namespace Xhgui\Profiler;

use RuntimeException;
use Xhgui\Profiler\Profilers\ProfilerInterface;

final class ProfilerFactory
{
    /**
     * Creates Profiler instance that can be used.
     *
     * If you're running multiple (which you shouldn't!),
     * It will test them in this preference order:
     * 2) tideways_xhprof
     * 2) tideways
     * 1) uprofiler
     * 3) xhprof
     *
     * @return ProfilerInterface|null
     */
    public static function create(array $config)
    {
        $adapters = array(
            Profiler::PROFILER_TIDEWAYS_XHPROF => function ($config) {
                return new Profilers\TidewaysXHProf($config);
            },
            Profiler::PROFILER_TIDEWAYS => function ($config) {
                return new Profilers\Tideways($config);
            },
            Profiler::PROFILER_UPROFILER => function ($config) {
                return new Profilers\UProfiler($config);
            },
            Profiler::PROFILER_XHPROF => function ($config) {
                return new Profilers\XHProf($config);
            },
        );

        if (isset($config['profiler'])) {
            $profiler = $config['profiler'];
            if (!isset($adapters[$profiler])) {
                throw new RuntimeException("Specified profiler '$profiler' is not supported");
            }

            $adapters = array(
                $profiler => $adapters[$profiler],
            );
        }

        foreach ($adapters as $profiler => $factory) {
            $adapter = $factory($config);
            if ($adapter->isSupported()) {
                return $adapter;
            }
        }

        return null;
    }
}
