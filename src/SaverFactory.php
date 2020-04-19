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
        $config['save.handler'] = $saveHandler;
        $saver = Xhgui_Saver::factory($config);

        return static::getAdapter($saver);
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
