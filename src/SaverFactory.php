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
        switch ($config['save.handler']) {
            case Profiler::SAVER_FILE:
                $saver = new Saver\FileSaver($config['save.handler.file']['filename']);
                break;
            default:
                // create via xhgui-collector
                $config = self::migrateConfig($config, $saveHandler);
                $legacySaver = Xhgui_Saver::factory($config);
                $saver = static::getAdapter($legacySaver);
                break;
        }

        if (!$saver || !$saver->isSupported()) {
            return null;
        }

        return $saver;
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
                if (isset($config['save.handler.mongodb']['dsn']) && !isset($config['db.host'])) {
                    $config['db.host'] = $config['save.handler.mongodb']['dsn'];
                }
                if (isset($config['save.handler.mongodb']['database']) && !isset($config['db.db'])) {
                    $config['db.db'] = $config['save.handler.mongodb']['database'];
                }
                if (isset($config['save.handler.mongodb']['options']) && !isset($config['db.options'])) {
                    $config['db.options'] = $config['save.handler.mongodb']['options'];
                }
                break;
            case Profiler::SAVER_PDO:
                if (isset($config['save.handler.pdo']) && !isset($config['pdo'])) {
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
