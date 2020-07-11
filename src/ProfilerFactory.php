<?php

namespace Xhgui\Profiler;

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
            new Profilers\TidewaysXHProf($config),
            new Profilers\Tideways($config),
            new Profilers\UProfiler($config),
            new Profilers\XHProf($config),
        );

        $available = array_filter($adapters, function (ProfilerInterface $adapter) {
            return $adapter->isSupported();
        });

        return current($available) ?: null;
    }
}
