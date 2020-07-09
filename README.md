# PHP Profiler

A PHP profiling library based on [XHGUI Data Collector][1].

This project replaces `header.php` approach from xhgui-collector with object based approach.

Supported profilers:
 - [Tideways XHProf] v5.x - PHP >= 7.0
 - [Tideways] v4.x - PHP >= 7.0
 - [UProfiler] - PHP >= 5.3, < PHP 7.0
 - [XHProf] - PHP >= 5.3, < PHP 7.0

[XHProf]: https://pecl.php.net/package/xhprof
[Tideways]: https://tideways.io/profiler/downloads
[Tideways XHProf]: https://github.com/tideways/php-xhprof-extension
[UProfiler]: https://github.com/FriendsOfPHP/uprofiler

This profiling library will auto-detect what you have installed and use that.

The preference order is currently as follows:
1. `tideways_xhprof`
1. `tideways`
1. `uprofiler`
1. `xhprof`

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

### Tideways (4.x)

```
curl -sSfL https://github.com/tideways/php-xhprof-extension/archive/v4.1.6.tar.gz | tar zx
cd php-xhprof-extension-4.1.6/
phpize
./configure
make
make install
echo extension=/usr/local/lib/php/pecl/20160303/tideways.so | tee /usr/local/etc/php/7.1/conf.d/ext-tideways.ini
```

### Tideways XHProf (5.+)

To install [tideways_xhprof], see their [installation documentation][tideways-xhprof-install].

[tideways_xhprof]: https://github.com/tideways/php-profiler-extension
[tideways-xhprof-install]: https://github.com/tideways/php-xhprof-extension#installation

Alternatively on `brew` (macOS) you can use packages from [kabel/pecl] or [glensc/tap] taps:

```
brew install glensc/tap/php@7.1-tideways-xhprof
brew install kabel/pecl/php@7.2-tideways-xhprof
brew install kabel/pecl/php@7.3-tideways-xhprof
brew install kabel/pecl/php-tideways-xhprof
```

[kabel/pecl]: https://github.com/kabel/homebrew-pecl
[glensc/tap]: https://github.com/glensc/homebrew-tap

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
			\Xhgui\Profiler\ProfilingFlags::NO_SPANS,
		),

		// Saver to use.
		// Please note that 'pdo' and 'mongo' savers are deprecated
		// Prefer 'upload' or 'file' saver.
		'save.handler' => \Xhgui\Profiler\Profiler::SAVER_UPLOAD,

		// File handler.
		// Appends jsonlines formatted data to path specified in 'save.handler.filename'
		'save.handler.filename' => '/tmp/xhgui.data.' . microtime(true) . '_' . substr(md5($url), 0, 6),

		// Saving profile data by upload is only recommended with HTTPS
		// endpoints that have IP whitelists applied.
		'save.handler.upload.uri' => 'https://example.com/run/import',
		'save.handler.upload.timeout' => 3,

		// For MongoDB
		'db.host' => 'mongodb://127.0.0.1:27017',
		'db.db' => 'xhprof',
		// Allows you to pass additional options like replicaSet to MongoClient.
		// 'username', 'password' and 'db' (where the user is added)
		'db.options' => array(),

		// For PDO
		'pdo' => array(
			'dsn' => 'sqlite:/tmp/xhgui.sqlite3',
			'user' => null,
			'pass' => null,
			'table' => 'results'
		),

		'profiler.options' => array(),

		// Environment variables to exclude from profiling data
		'profiler.exclude-env' => array(
			'APP_DATABASE_PASSWORD',
			'PATH',
		),

		/**
		 * Determine whether profiler should run.
		 * This default implementation just disables the profiler.
		 * Override this with your custom logic in your config
		 * @return bool
		 */
		'profiler.enable' => function () {
			return false;
		},
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

## Advanced Usage

You might want to control capture and sending yourself, perhaps modify data before sending.

```php
/** @var \Xhgui\Profiler\Profiler $profiler */
// start profiling
$profiler->enable();

// run program
foo();

// stop profiler
$profiler_data = $profiler->disable();

// send $profiler_data to saver
$profiler->save($profiler_data);
```

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
