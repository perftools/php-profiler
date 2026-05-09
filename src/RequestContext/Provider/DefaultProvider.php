<?php

namespace Xhgui\Profiler\RequestContext\Provider;

use Xhgui\Profiler\RequestContext\RequestContext;

/**
 * @internal
 */
class DefaultProvider implements RequestContextProviderInterface
{
    public function capture()
    {
        $server = $_SERVER;

        // 'REQUEST_TIME_FLOAT' isn't available before 5.4.0
        // https://www.php.net/manual/en/reserved.variables.server.php
        if (!isset($server['REQUEST_TIME_FLOAT'])) {
            $server['REQUEST_TIME_FLOAT'] = microtime(true);
        }
        if (!isset($server['REQUEST_TIME'])) {
            $server['REQUEST_TIME'] = (int) $server['REQUEST_TIME_FLOAT'];
        }

        if (array_key_exists('REQUEST_URI', $server)) {
            return RequestContext::fromHttp(
                $server['REQUEST_URI'],
                $_GET,
                $_ENV,
                $server
            );
        }

        return RequestContext::fromCli(
            $this->getCommand(isset($server['argv']) ? $server['argv'] : array()),
            $_ENV,
            $server
        );
    }

    /**
     * @param array $argv
     * @return string
     */
    private function getCommand(array $argv)
    {
        if (!isset($argv[0])) {
            return '';
        }

        $cmd = basename($argv[0]);
        $args = array_slice($argv, 1);

        if (!$args) {
            return $cmd;
        }

        return $cmd . ' ' . implode(' ', $args);
    }
}
