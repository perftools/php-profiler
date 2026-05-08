<?php

namespace Xhgui\Profiler\RequestContext;

final class RequestContext implements RequestContextInterface
{
    /** @var string */
    private $url;

    /** @var array */
    private $query;

    /** @var array */
    private $env;

    /** @var array */
    private $server;

    /**
     * @param string $url
     * @param array $query
     * @param array $env
     * @param array $server
     */
    private function __construct($url, array $query, array $env, array $server)
    {
        $this->url = (string) $url;
        $this->query = $query;
        $this->env = $env;
        $this->server = $server;
    }

    /**
     * @param string $url
     * @param array $get
     * @param array $env
     * @param array $server
     * @return self
     */
    public static function fromHttp($url, array $get, array $env, array $server)
    {
        return new self($url, $get, $env, $server);
    }

    /**
     * @param string $url
     * @param array $env
     * @param array $server
     * @return self
     */
    public static function fromCli($url, array $env, array $server)
    {
        return new self($url, array(), $env, $server);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function getServer()
    {
        return $this->server;
    }
}
