<?php

namespace Xhgui\Profiler;

use Xhgui\Profiler\RequestContext\RequestContextInterface;

/**
 * @internal
 */
final class ProfilingData
{
    /** @var array */
    private $allowedServerKeys = array(
        'DOCUMENT_ROOT',
        'HTTPS',
        'HTTP_HOST',
        'HTTP_USER_AGENT',
        'PATH_INFO',
        'PHP_AUTH_USER',
        'PHP_SELF',
        'QUERY_STRING',
        'REMOTE_ADDR',
        'REMOTE_USER',
        'REQUEST_METHOD',
        'REQUEST_TIME',
        'REQUEST_TIME_FLOAT',
        'SERVER_ADDR',
        'SERVER_NAME',
        'UNIQUE_ID',
    );

    /** @var array */
    private $excludeEnv;
    /** @var callable|null */
    private $simpleUrl;
    /** @var callable|null */
    private $replaceUrl;
    /** @var bool */
    private $excludeAllEnv;

    public function __construct(Config $config)
    {
        $this->excludeEnv = isset($config['profiler.exclude-env']) ? (array)$config['profiler.exclude-env'] : array();
        $this->excludeAllEnv = isset($config['profiler.exclude-all-env']) ? $config['profiler.exclude-all-env'] : false;
        $this->simpleUrl = isset($config['profiler.simple_url']) ? $config['profiler.simple_url'] : null;
        $this->replaceUrl = isset($config['profiler.replace_url']) ? $config['profiler.replace_url'] : null;
    }

    /**
     * @param array $profile
     * @param RequestContextInterface $context
     * @return array
     */
    public function getProfilingData(array $profile, RequestContextInterface $context)
    {
        $url = $this->getUrl($context);
        $server = $context->getServer();
        list($sec, $usec) = $this->getRequestTime($server['REQUEST_TIME_FLOAT']);

        $meta = array(
            'url' => $url,
            'get' => $context->getQuery(),
            'env' => $this->getEnvironment($context->getEnv()),
            'SERVER' => $this->getServer($server),
            'simple_url' => $this->getSimpleUrl($url),
            'request_ts_micro' => array('sec' => $sec, 'usec' => $usec),
            // these are superfluous and should be dropped in the future
            'request_ts' => array('sec' => $sec, 'usec' => 0),
            'request_date' => date('Y-m-d', $sec),
        );

        $data = array(
            'profile' => $profile,
            'meta' => $meta,
        );

        return $data;
    }

    /**
     * @param array $env
     * @return array
     */
    private function getEnvironment(array $env)
    {
        if ($this->excludeAllEnv) {
            return array();
        }

        foreach ($this->excludeEnv as $key) {
            unset($env[$key]);
        }

        return $env;
    }

    /**
     * Creates a simplified URL given a standard URL.
     * Does the following transformations:
     *
     * - Remove numeric values after =.
     *
     * @param string $url
     * @return string
     */
    private function getSimpleUrl($url)
    {
        if (is_callable($this->simpleUrl)) {
            return call_user_func($this->simpleUrl, $url);
        }

        return preg_replace('/=\d+/', '', $url);
    }

    /**
     * @param RequestContextInterface $context
     * @return string
     */
    private function getUrl(RequestContextInterface $context)
    {
        $url = $context->getUrl();

        if (is_callable($this->replaceUrl)) {
            $url = call_user_func($this->replaceUrl, $url);
        }

        return $url;
    }

    /**
     * @param float $requestTime
     * @return array
     */
    private function getRequestTime($requestTime)
    {
        $parts = explode('.', sprintf('%.6F', $requestTime));

        $sec = $parts[0];
        $usec = isset($parts[1]) ? $parts[1] : 0;

        return array((int)$sec, (int)$usec);
    }

    /**
     * @param array $server
     * @return array
     */
    private function getServer(array $server)
    {
        return array_intersect_key($server, array_flip($this->allowedServerKeys));
    }
}
