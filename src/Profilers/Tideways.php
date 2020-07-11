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

    /** @var int */
    private $flags;

    /** @var array */
    private $options;

    public function __construct(array $config)
    {
        $this->flags = $this->combineFlags($config['profiler.flags'], $this->getProfileFlagMap());
        $this->options = $config['profiler.options'];
    }

    public function isSupported()
    {
        return extension_loaded(self::EXTENSION_NAME);
    }

    public function enable()
    {
        tideways_enable($this->flags, $this->options);
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
