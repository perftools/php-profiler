<?php

namespace Xhgui\Profiler\RequestContext\Provider;

use Xhgui\Profiler\RequestContext\RequestContextInterface;

interface RequestContextProviderInterface
{
    /**
     * Capture request-scoped profiler metadata.
     *
     * Implementations should return a request-context object whose request time
     * and server snapshot describe the same request.
     *
     * @return RequestContextInterface
     */
    public function capture();
}
