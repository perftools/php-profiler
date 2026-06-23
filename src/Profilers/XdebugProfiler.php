<?php

namespace Xhgui\Profiler\Profilers;

/**
 * @see https://xdebug.org/docs/execution_trace
 */
class XdebugProfiler extends AbstractProfiler
{
    const EXTENSION_NAME = 'xdebug';

    public function isSupported()
    {
        return extension_loaded(self::EXTENSION_NAME);
    }

    public function enable($flags = array(), $options = array())
    {
        $traceFile = xdebug_start_trace();
//        uprofiler_enable($this->combineFlags($flags, $this->getProfileFlagMap()), $options);
    }

    public function disable()
    {
        $traceFile = xdebug_stop_trace();

        return $this->readTrace($traceFile);
    }

    private function readTrace($traceFile)
    {
        return file_get_contents($traceFile);
    }
}
