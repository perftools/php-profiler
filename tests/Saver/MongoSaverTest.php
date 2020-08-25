<?php

namespace Xhgui\Profiler\Test\Saver;

use Xhgui\Profiler\Saver\MongoSaver;
use Xhgui\Profiler\Test\TestCase;

/**
 * @property MongoSaver $saver
 */
class MongoSaverTest extends TestCase
{
    public function setUp()
    {
        $this->skipIfNoXhguiCollector();
        $config = array(
            'save.handler.mongodb' => array(
                'dsn' => 'mongodb://127.0.0.1:27017',
                'database' => getenv('XHGUI_MONGO_DB') ?: 'xhprof',
                'options' => array(),
            ),
        );

        $this->saver = $this->createSaver('mongodb', $config);
    }

    public function testSaveEmpty()
    {
        $this->saver->save(array());
    }
}
