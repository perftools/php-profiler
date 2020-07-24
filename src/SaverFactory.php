<?php

namespace Xhgui\Profiler;

use Xhgui\Profiler\Saver\SaverInterface;
use Xhgui_Saver;
use Xhgui_Saver_Interface;

final class SaverFactory
{
    /**
     * @param string $saveHandler
     * @param array $config
     * @return SaverInterface|null
     */
    public static function create($saveHandler, array $config = array())
    {
        $config = self::migrateConfig($config, $saveHandler);
        $saver = Xhgui_Saver::factory($config);

        return static::getAdapter($saver);
    }

    /**
     * Prepare config for Xhgui_Saver specific to $saveHandler
     *
     * @param array $config
     * @param string $saveHandler
     * @return array
     */
    private static function migrateConfig(array $config, $saveHandler)
    {
        switch ($saveHandler) {
            case Profiler::SAVER_FILE:
                if (isset($config['save.handler.file']['filename']) && !isset($config['save.handler.filename'])) {
                    $config['save.handler.filename'] = $config['save.handler.file']['filename'];
                }
                break;
            case Profiler::SAVER_UPLOAD:
                if (isset($config['save.handler.upload']['uri']) && !isset($config['save.handler.upload.uri'])) {
                    $config['save.handler.upload.uri'] = $config['save.handler.upload']['uri'];
                }
                if (isset($config['save.handler.upload.timeout']) && !isset($config['save.handler.upload']['timeout'])) {
                    $config['save.handler.upload.timeout'] = $config['save.handler.upload']['timeout'];
                }
                if (!empty($config['save.handler.upload']['token'])) {
                    $config['save.handler.upload.uri'] .= '?token=' . $config['save.handler.upload']['token'];
                }
                break;
            case Profiler::SAVER_MONGODB:
                if (isset($config['save.handler.mongodb']['dsn'])) {
                    $config['db.host'] = $config['save.handler.mongodb']['dsn'];
                }
                if (isset($config['save.handler.mongodb']['database'])) {
                    $config['db.db'] = $config['save.handler.mongodb']['database'];
                }
                if (isset($config['save.handler.mongodb']['options'])) {
                    $config['db.options'] = $config['save.handler.mongodb']['options'];
                }
                break;
            case Profiler::SAVER_PDO:
                if (isset($config['save.handler.pdo'])) {
                    $config['pdo'] = $config['save.handler.pdo'];
                }
                break;
        }

        $config['save.handler'] = $saveHandler;

        return $config;
    }

    private static function getAdapter(Xhgui_Saver_Interface $saver)
    {
        $adapters = array(
            new Saver\FileSaver($saver),
            new Saver\PdoSaver($saver),
            new Saver\MongoSaver($saver),
            new Saver\UploadSaver($saver),
        );

        $available = array_filter($adapters, function (SaverInterface $adapter) {
            return $adapter->isSupported();
        });

        return current($available) ?: null;
    }
}
