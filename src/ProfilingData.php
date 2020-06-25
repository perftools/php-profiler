<?php

namespace Xhgui\Profiler;

use Xhgui_Util;

class ProfilingData
{
    /** @var array */
    private $profile;

    /** @var array */
    private $excludeEnv;

    public function __construct(array $profile, array $config = array())
    {
        $this->profile = $profile;
        $this->excludeEnv = isset($config['profiler.exclude-env']) ? (array)$config['profiler.exclude-env'] : array();
    }

    /**
     * Mostly copy-pasta from example header.php in XHGUI-collector
     *
     * @return array
     */
    public function getProfilingData()
    {
        $uri = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : null;
        if (empty($uri) && isset($_SERVER['argv'])) {
            $cmd = basename($_SERVER['argv'][0]);
            $uri = $cmd . ' ' . implode(' ', array_slice($_SERVER['argv'], 1));
        }

        $time = array_key_exists('REQUEST_TIME', $_SERVER) ? $_SERVER['REQUEST_TIME'] : time();
        $requestTimeFloat = explode('.', $_SERVER['REQUEST_TIME_FLOAT']);
        if (!isset($requestTimeFloat[1])) {
            $requestTimeFloat[1] = 0;
        }

        $requestTs = array('sec' => $time, 'usec' => 0);
        $requestTsMicro = array('sec' => $requestTimeFloat[0], 'usec' => $requestTimeFloat[1]);

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

        $meta = array(
            'url' => $uri,
            'get' => $_GET,
            'env' => $this->getEnvironment($_ENV),
            'SERVER' => $serverMeta,
            'simple_url' => Xhgui_Util::simpleUrl($uri),
            'request_ts' => $requestTs,
            'request_ts_micro' => $requestTsMicro,
            'request_date' => date('Y-m-d', $time),
        );

        $data = array(
            'profile' => $this->profile,
            'meta' => $meta,
        );

        return $data;
    }

    /**
     * @param array $env
     * @return array
     */
    private function getEnvironment($env)
    {
        foreach ($this->excludeEnv as $key) {
            unset($env[$key]);
        }

        return $env;
    }
}
