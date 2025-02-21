<?php

use Xhgui\Profiler\Exception\ProfilerException;
use Xhgui\Profiler\Profiler;

/**
 * Plugin to capture profiling data using for XHGui.
 *
 * @author Elan Ruusamäe <glen@delfi.ee>
 *
 * Example:
 * $config = new Zend_Config(array(
 *    // ...
 * ));
 * $controller->registerPlugin(new XhguiProfilerPlugin($config), 150);
 */
class XhguiProfilerPlugin extends Zend_Controller_Plugin_Abstract
{
    /** @var Zend_Config */
    private $config;

    /** @var Profiler */
    private $profiler;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->startProfiler();
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
    }

    private function startProfiler()
    {
        try {
            $this->getProfiler()->start();
        } catch (ProfilerException $e) {
            error_log('Profiler error: ' . $e->getMessage());
        }
    }

    /**
     * @return Profiler
     */
    private function getProfiler()
    {
        if ($this->profiler !== null) {
            return $this->profiler;
        }

        return $this->profiler = new Profiler($this->config->toArray());
    }
}
