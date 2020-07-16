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
        switch ($saveHandler) {
            case Profiler::SAVER_FILE:
                $saverConfig = array_merge(array(
                    'filename' => null,
                ), $config['save.handler.file']);
                $saver = new Saver\FileSaver($saverConfig['filename']);
                break;
            case Profiler::SAVER_UPLOAD:
                $saverConfig = array_merge(array(
                    'uri' => null,
                    'token' => null,
                    'timeout' => 3,
                ), $config['save.handler.upload']);
                $saver = new Saver\UploadSaver($saverConfig['uri'], $saverConfig['token'], $saverConfig['timeout']);
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
        );

        $available = array_filter($adapters, function (SaverInterface $adapter) {
            return $adapter->isSupported();
        });

        return current($available) ?: null;
    }
}
