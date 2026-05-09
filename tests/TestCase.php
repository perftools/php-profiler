<?php

namespace Xhgui\Profiler\Test;

use Xhgui\Profiler\Config;
use Xhgui\Profiler\RequestContext\RequestContext;
use Xhgui\Profiler\RequestContext\RequestContextInterface;
use Xhgui\Profiler\Profilers\ProfilerInterface;
use Xhgui\Profiler\Saver\SaverInterface;
use Xhgui\Profiler\SaverFactory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var SaverInterface */
    protected $saver;

    /** @var ProfilerInterface */
    protected $profiler;

    protected function getResource($resourceName)
    {
        return $this->readJsonFile(__DIR__ . '/Resources/' . $resourceName);
    }

    protected function runProfiler($flags = array(), $options = array())
    {
        $this->profiler->enable($flags, $options);
        $data = $this->profiler->disable();
        $this->assertNotEmpty($data);

        return $data;
    }

    protected function createSaver($saveHandler, array $config = array())
    {
        $saver = SaverFactory::create($saveHandler, new Config($config));
        $this->assertNotNull($saver);

        return $saver;
    }

    /**
     * @param array $context
     * @return RequestContextInterface
     */
    protected function createRequestContextObject(array $context = array())
    {
        $defaults = array(
            'url' => '/test?id=42',
            'get' => array('id' => '42'),
            'env' => array(),
            'server' => array(
                'DOCUMENT_ROOT' => '/var/www',
                'PHP_SELF' => '/index.php',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_TIME' => 1234,
                'REQUEST_TIME_FLOAT' => 1234.56789,
            ),
        );
        $context = array_replace($defaults, $context);
        $context['server'] = isset($context['server']) && is_array($context['server'])
            ? array_replace($defaults['server'], $context['server'])
            : $defaults['server'];

        return RequestContext::fromHttp(
            array_key_exists('url', $context) ? $context['url'] : null,
            isset($context['get']) && is_array($context['get']) ? $context['get'] : array(),
            isset($context['env']) && is_array($context['env']) ? $context['env'] : array(),
            isset($context['server']) && is_array($context['server']) ? $context['server'] : array()
        );
    }

    protected function readJsonFile($filename)
    {
        $this->assertFileExists($filename);
        $contents = file_get_contents($filename);
        $this->assertNotEmpty($contents);
        $result = json_decode($contents, true);
        $this->assertNotEmpty($result);

        return $result;
    }

    protected function skipIfNoXhguiCollector()
    {
        if (!class_exists('\Xhgui_Saver')) {
            $this->markTestSkipped('Optional dependency perftools/xhgui-collector missing');
        }
    }

    protected function assertExpectedProfilingData(array $data)
    {
        $this->assertArrayHasKey('profile', $data);
        $this->assertArrayHasKey('meta', $data);
        $this->assertExpectedProfileMeta($data['meta']);
    }

    private function assertExpectedProfileMeta(array $meta)
    {
        $this->assertArrayHasKey('url', $meta);
        $this->assertArrayHasKey('simple_url', $meta);
        $this->assertArrayHasKey('request_ts', $meta);
        $this->assertArrayHasKey('request_ts_micro', $meta);
        $this->assertArrayHasKey('request_date', $meta);
        $this->assertArrayHasKey('get', $meta);
        $this->assertArrayHasKey('env', $meta);
        $this->assertArrayHasKey('SERVER', $meta);

        $server = $meta['SERVER'];
        $this->assertArrayHasKey('PHP_SELF', $server);
        $this->assertArrayHasKey('DOCUMENT_ROOT', $server);
        $this->assertArrayHasKey('REQUEST_TIME_FLOAT', $server);
        $this->assertArrayHasKey('REQUEST_TIME', $server);

        $ts = $meta['request_ts'];
        $this->assertArrayHasKey('sec', $ts, 'meta.request_ts.sec');
        $this->assertArrayHasKey('usec', $ts, 'meta.request_ts.usec');

        $ts = $meta['request_ts_micro'];
        $this->assertArrayHasKey('sec', $ts, 'meta.request_ts_micro.sec');
        $this->assertArrayHasKey('usec', $ts, 'meta.request_ts_micro.usec');
    }
}
