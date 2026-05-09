<?php

namespace Xhgui\Profiler\RequestContext;

interface RequestContextInterface
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return array
     */
    public function getQuery();

    /**
     * @return array
     */
    public function getEnv();

    /**
     * Returns the captured server snapshot.
     *
     * Implementations must include REQUEST_TIME_FLOAT because profiling
     * metadata timestamps are derived from it.
     *
     * @return array
     */
    public function getServer();
}
