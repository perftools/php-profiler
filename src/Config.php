<?php

namespace Xhgui\Profiler;

use ArrayAccess;
use Xhgui\Profiler\Exception\ProfilerException;

class Config implements ArrayAccess
{
    /** @var array */
    private $config;

    public function __construct(array $config = array())
    {
        $this->config = $this->getDefaultConfig();
        if ($config) {
            $this->merge($config);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }

    /**
     * Load a config file, merge with the currently loaded configuration.
     */
    public function load($filename)
    {
        if (!file_exists($filename)) {
            throw new ProfilerException("File does not exist: $filename");
        }
        $config = require $filename;
        $this->merge($config);
    }

    private function merge(array $config)
    {
        $this->config = array_replace($this->config, $config);
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * @return array
     */
    private function getDefaultConfig()
    {
        return array(
            'save.handler' => Profiler::SAVER_STACK,
            'save.handler.stack' => array(
                'savers' => array(
                    Profiler::SAVER_UPLOAD,
                    Profiler::SAVER_FILE,
                ),
                'saveAll' => false,
            ),
            'save.handler.file' => array(
                'filename' => sys_get_temp_dir() . '/xhgui.data.jsonl',
            ),
            'profiler.enable' => function () {
                return true;
            },
            'profiler.flags' => array(
                ProfilingFlags::CPU,
                ProfilingFlags::MEMORY,
                ProfilingFlags::NO_BUILTINS,
                ProfilingFlags::NO_SPANS,
            ),
            'profiler.options' => array(),
            'profiler.exclude-env' => array(),
            'profiler.simple_url' => function ($url) {
                return preg_replace('/=\d+/', '', $url);
            },
            'profiler.replace_url' => null,
        );
    }
}
