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

    /** @var array */
    private $flags;

    public function __construct(array $flags = array())
    {
        $this->flags = $flags;
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
        $flags = $this->combineFlags($this->flags, $this->getProfileFlagMap());

        tideways_xhprof_enable($flags);
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
