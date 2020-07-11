<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

/**
 * v5 (tideways_xhprof)
 *
 * @see https://github.com/tideways/php-profiler-extension
 */
class TidewaysXHProf extends AbstractProfiler
{
    const EXTENSION_NAME = 'tideways_xhprof';

    /** @var int */
    private $flags;

    public function __construct(array $config)
    {
        $this->flags = $this->combineFlags($config['profiler.flags'], $this->getProfileFlagMap());
    }

    public function isSupported()
    {
        return extension_loaded(self::EXTENSION_NAME);
    }

    /**
     * @see https://github.com/tideways/php-xhprof-extension#usage
     */
    public function enable()
    {
        tideways_xhprof_enable($this->flags);
    }

    public function disable()
    {
        return tideways_xhprof_disable();
    }

    private function getProfileFlagMap()
    {
        return array(
            ProfilingFlags::CPU => TIDEWAYS_XHPROF_FLAGS_CPU,
            ProfilingFlags::MEMORY => TIDEWAYS_XHPROF_FLAGS_MEMORY,
            ProfilingFlags::NO_BUILTINS => TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS,
            ProfilingFlags::NO_SPANS => 0,
        );
    }
}
