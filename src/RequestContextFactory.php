<?php

namespace Xhgui\Profiler;

use Xhgui\Profiler\Exception\ProfilerException;
use Xhgui\Profiler\RequestContext\Provider\DefaultProvider;
use Xhgui\Profiler\RequestContext\Provider\RequestContextProviderInterface;

/**
 * @internal
 */
final class RequestContextFactory
{
    /**
     * @param Config $config
     * @return RequestContextProviderInterface
     */
    public static function create(Config $config)
    {
        $provider = isset($config['profiler.request_context_provider'])
            ? $config['profiler.request_context_provider']
            : null;

        if ($provider === null) {
            return new DefaultProvider();
        }

        if (!$provider instanceof RequestContextProviderInterface) {
            throw new ProfilerException('Request context provider must implement RequestContextProviderInterface');
        }

        return $provider;
    }
}
