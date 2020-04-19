<?php

namespace Xhgui\Profiler;

use Exception;
use MongoDate;
use RuntimeException;
use Xhgui\Profiler\Profilers\ProfilerInterface;
use Xhgui\Profiler\Saver\SaverInterface;
use Xhgui_Config;
use Xhgui_Saver_Mongo;
use Xhgui_Util;

class Profiler
{
    /**
     * Profiler configuration.
     *
     * @var array
     * @see Xhgui_Config
     * @see https://raw.githubusercontent.com/perftools/xhgui/6dac03aaa37df4b42d949bf4f8455573bea44e03/config/config.default.php
     */
    private $config;

    /**
     * @var SaverInterface
     */
    private $saveHandler;

    /**
     * @var ProfilerInterface
     */
    private $profiler;

    /**
     * Result of config `profiler.enable` function execution.
     *
     * @var bool
     */
    private $shouldRun;

    /**
     * Simple state variable to hold the value of 'Is the profiler running or not?'
     *
     * @var bool
     */
    private $running;

    /**
     * Profiler constructor.
     *
     * @param array $config
     * @throws RuntimeException if unable to create profiler
     */
    public function __construct(array $config)
    {
        $this->config = array_replace($this->getDefaultConfig(), $config);
        $shouldRunFunction = $this->config['profiler.enable'];
        $this->shouldRun = is_callable($shouldRunFunction) ? $shouldRunFunction() : false;
        if (!$this->getProfiler()) {
            throw new RuntimeException('Unable to create profiler: no suitable profiler found');
        }
        if (!$this->getSaver()) {
            throw new RuntimeException('Unable to create profiler: unable to save data');
        }
    }

    /**
     * Enables profiling for the current request / CLI execution
     */
    public function enable()
    {
        $this->running = false;
        if (!$this->shouldRun) {
            return;
        }

        if (!isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
        }

        $profiler = $this->getProfiler();
        if (!$profiler) {
            return;
        }

        $profiler->enable($this->config['profiler.flags'], $this->config['profiler.options']);
        $this->running = true;
    }

    private function getProfiler()
    {
        if ($this->profiler === null) {
            $this->profiler = ProfilerFactory::create() ?: false;
        }

        return $this->profiler ?: null;
    }

    private function getSaver()
    {
        if ($this->saveHandler === null) {
            $this->saveHandler = SaverFactory::create($this->config['save.handler'], $this->config) ?: false;
        }

        return $this->saveHandler ?: null;
    }

    /**
     * Calls register_shutdown_function .
     * Registers this class' shutDown method as the shutdown handler
     *
     * @see Profiler::shutDown
     */
    public function registerShutdownHandler()
    {
        // do not register shutdown function if the profiler isn't running
        if (!$this->running) {
            return;
        }

        register_shutdown_function(array($this, 'shutDown'));
    }

    /**
     * @internal
     */
    public function shutDown()
    {
        // ignore_user_abort(true) allows your PHP script to continue executing, even if the user has terminated their request.
        // Further Reading: http://blog.preinheimer.com/index.php?/archives/248-When-does-a-user-abort.html
        // flush() asks PHP to send any data remaining in the output buffers. This is normally done when the script completes, but
        // since we're delaying that a bit by dealing with the xhprof stuff, we'll do it now to avoid making the user wait.
        ignore_user_abort(true);
        flush();

        try {
            $this->stop();
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Mostly copypasta from example header.php in XHGUI
     *
     * @param array $data
     * @return array
     */
    private function assembleProfilingData($data)
    {
        $uri = array_key_exists('REQUEST_URI', $_SERVER)
            ? $_SERVER['REQUEST_URI']
            : null;
        if (empty($uri) && isset($_SERVER['argv'])) {
            $cmd = basename($_SERVER['argv'][0]);
            $uri = $cmd . ' ' . implode(' ', array_slice($_SERVER['argv'], 1));
        }

        $time = array_key_exists('REQUEST_TIME', $_SERVER)
            ? $_SERVER['REQUEST_TIME']
            : time();
        $requestTimeFloat = explode('.', $_SERVER['REQUEST_TIME_FLOAT']);
        if (!isset($requestTimeFloat[1])) {
            $requestTimeFloat[1] = 0;
        }

        if ($this->saveHandler->getHandler() instanceof Xhgui_Saver_Mongo) {
            $requestTs = new MongoDate($time);
            $requestTsMicro = new MongoDate($requestTimeFloat[0], $requestTimeFloat[1]);
        } else {
            $requestTs = array('sec' => $time, 'usec' => 0);
            $requestTsMicro = array('sec' => $requestTimeFloat[0], 'usec' => $requestTimeFloat[1]);
        }

        $allowedServerKeys = array(
            'PHP_SELF',
            'SERVER_ADDR',
            'SERVER_NAME',
            'REQUEST_METHOD',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'QUERY_STRING',
            'DOCUMENT_ROOT',
            'HTTP_HOST',
            'HTTP_USER_AGENT',
            'HTTPS',
            'REMOTE_ADDR',
            'REMOTE_USER',
            'PHP_AUTH_USER',
            'PATH_INFO',
        );
        $serverMeta = array_intersect_key($_SERVER, array_flip($allowedServerKeys));

        $data['meta'] = array(
            'url' => $uri,
            'get' => $_GET,
            'env' => $_ENV,
            'SERVER' => $serverMeta,
            'simple_url' => Xhgui_Util::simpleUrl($uri),
            'request_ts' => $requestTs,
            'request_ts_micro' => $requestTsMicro,
            'request_date' => date('Y-m-d', $time),
        );

        return $data;
    }

    /**
     * @return array
     * @see Xhgui_Config
     */
    private function getDefaultConfig()
    {
        $file = $this->getDefaultConfigFile();
        if ($file) {
            Xhgui_Config::load($file);
        }

        $defaultShouldRunFunction =
            /**
             * Determine whether profiler should run.
             * This default implementation just disables the profiler.
             * Override this with your custom logic in your config
             * @return bool
             */
            function () {
                return false;
            };

        return array_replace(Xhgui_Config::all(),
            array(
                'profiler.enable' => $defaultShouldRunFunction,
                'profiler.flags' => array(),
                'profiler.options' => array(),
            )
        );
    }

    private function getDefaultConfigFile()
    {
        $paths = array(
            // aside the vendor
            dirname(dirname(dirname(__DIR__))) . '/perftools/xhgui-collector/config/config.default.php',
        );

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * This is an alias for method "enable"
     */
    public function start()
    {
        $this->enable();
    }

    /**
     * Stop profiling. Get currently collected data and save it
     */
    public function stop()
    {
        $data = $this->collectProfilingData();
        $this->saveProfilingData($data);
        $this->running = false;
    }

    /**
     * Returns collected profiling data
     *
     * @return array
     */
    private function collectProfilingData()
    {
        if (!$this->running) {
            return array();
        }
        $rawProfilingData = array('profile' => $this->profiler->disable());

        return $this->assembleProfilingData($rawProfilingData);
    }

    /**
     * Saves collected profiling data
     *
     * @param array $data
     */
    private function saveProfilingData(array $data = null)
    {
        if (!$data) {
            return;
        }

        $this->saveHandler->save($data);
    }

    /**
     * Tells, if profiler is running or not
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->running;
    }
}
