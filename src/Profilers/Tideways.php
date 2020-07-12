<?php

namespace Xhgui\Profiler\Profilers;

use Xhgui\Profiler\ProfilingFlags;

/**
 * v4 (tideways)
 *
 * @see https://github.com/tideways/php-profiler-extension
 */
class Tideways extends AbstractProfiler
{
    const EXTENSION_NAME = 'tideways';

    /** @var array */
    private $flags;

    /** @var array */
    private $options;

    public function __construct(array $flags = array(), array $options = array())
    {
        $this->flags = $flags;
        $this->options = $options;
    }

    public function isSupported()
    {
        return extension_loaded(self::EXTENSION_NAME);
    }

    public function enable()
    {
        $flags = $this->combineFlags($this->flags, $this->getProfileFlagMap());

        tideways_enable($flags, $this->options);
    }

    public function disable()
    {
        return tideways_disable();
    }

    private function getProfileFlagMap()
    {
        return array(
            ProfilingFlags::CPU => TIDEWAYS_FLAGS_CPU,
            ProfilingFlags::MEMORY => TIDEWAYS_FLAGS_MEMORY,
            ProfilingFlags::NO_BUILTINS => TIDEWAYS_FLAGS_NO_BUILTINS,
            ProfilingFlags::NO_SPANS => TIDEWAYS_FLAGS_NO_SPANS,
        );
    }
}
