<?php

use Xhgui\Profiler\Profiler;

function register_xhgui_plugin(Zend_Controller_Front $controller)
{
    $config = new Zend_Config(array(
        /**
         * Determine whether the profiler should run.
         * This default implementation just disables the profiler.
         * Override this with your custom logic in your config
         * @return bool
         */
        'profiler.enable' => function () {
            return true;
        },

        // Saver to use.
        // Please note that 'pdo' and 'mongo' savers are deprecated
        // Prefer 'upload' or 'file' saver.
        'save.handler' => Profiler::SAVER_UPLOAD,

        // Saving profile data by upload is only recommended with HTTPS
        // endpoints that have IP whitelists applied.
        // https://github.com/perftools/php-profiler#upload-saver
        'save.handler.upload' => array(
            'url' => 'https://example.com/run/import',
            // The timeout option is in seconds and defaults to 3 if unspecified.
            'timeout' => 3,
            // the token must match 'upload.token' config in XHGui
            'token' => 'token',
        ),
    ));

    $controller->registerPlugin(new XhguiProfilerPlugin($config), 150);
}
