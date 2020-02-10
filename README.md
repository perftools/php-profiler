# PHP Profiler

A PHP profiling library based on [XHGUI Data Collector][1].

Supported profilers:
 - XHProf - PHP >= 5.3, < PHP 7.0
 - Tideways - PHP >= 7.0
 - UProfiler - PHP >= 5.3, < PHP 7.0

[XHProf]: https://pecl.php.net/package/xhprof
[Tideways]: https://github.com/tideways/php-xhprof-extension
[UProfiler]: https://github.com/FriendsOfPHP/uprofiler

This profiling library will auto-detect what you have installed and use that.

The preference order is currently as follows:
1. uprofiler
1. tideways
1. xhprof

DON'T RELY ON THIS PREFERENCE ORDER IN PRODUCTION ENVIRONMENTS.

You shouldn't have more than one profiler installed in production.

## Installing profilers

### Mongo

For PHP 5:
```
pecl install mongo
```

for PHP 7:
```
pecl install mongodb
composer require alcaeus/mongo-php-adapter
```

### XHProf

```
pecl install xhprof-beta
```

### Tideways

```
curl -sSfL https://github.com/tideways/php-xhprof-extension/archive/v4.1.6.tar.gz | tar zx
cd php-xhprof-extension-4.1.6/
phpize
./configure
make
make install
echo extension=/usr/local/lib/php/pecl/20160303/tideways.so | tee /usr/local/etc/php/7.1/conf.d/ext-tideways.ini
```

## Usage

In order to profile your application, add it as a dependency, then
configure it and choose a place to start profiling from.

Most likely you'll have something like

```php
<?php
// inside some bootstrapper or other "early central point in execution"
try {
	$profilerConfig = array(
		'profiler.flags' => array(
			\Xhgui\Profiler\ProfilingFlags::CPU,
			\Xhgui\Profiler\ProfilingFlags::MEMORY,
			\Xhgui\Profiler\ProfilingFlags::NO_BUILTINS,
		),
		'profiler.options' => array(),
	);
	/**
	 * The constructor will throw an exception if the environment
	 * isn't fit for profiling (extensions missing, other problems)
	 */
	$profiler = new \Xhgui\Profiler\Profiler($profilerConfig);

	// The profiler itself checks whether it should be enabled
	// for request (executes lambda function from config)
	$profiler->enable();

	// shutdown handler collects and stores the data.
	$profiler->registerShutdownHandler();
} catch (Exception $e){
	// throw away or log error about profiling instantiation failure
}
```

## Configuration

Most of the configuration is xhgui standard and used by Xhgui saver.
See [XHGUI Data Collector packagist page][1] for references.

Configuration unique to this package:

| Configuration option | type  | description                                      | default value |
|----------------------|-------|--------------------------------------------------|---------------|
| profiler.flags       | array | Array of [ProfilingFlags][2]                     | empty array   |
| profiler.options     | array | Array of options to pass to profiler enable call | empty array   |

What you'll likely need to configure when instructing an app to use this profiling:

 - save handler type
 - save handler properties (for mongodb, the access credentials, host, db)
 - profiler.flags -- whether to use CPU profiling, memory profiling
 - profiler.enable -- closure which determines whether profiling should run

## Run description

When Profiler object constructed, it determines that requirements are in place, whether
profiling should run, which save handler to construct and constructs the save handler.
In case of failures, it will throw an exception.

`enable` will detect an available profiler and call its enable function with the current
configuration.

`registerShutdownHandler` will ensure profer is running and then call
`register_shutdown_handler`. It will register a shutdown handler that provides the
calls for finishing profiling and storing the data.

[1]: https://packagist.org/packages/perftools/xhgui-collector
[2]: src/ProfilingFlags.php
