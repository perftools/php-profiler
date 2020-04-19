<?php

namespace Xhgui\Profiler\Test\Saver;

use Xhgui\Profiler\Saver\MongoSaver;
use Xhgui\Profiler\Test\TestCase;

/**
 * @requires extension mongodb
 * @property MongoSaver $saver
 */
class MongoSaverTest extends TestCase
{
    public function setUp()
    {
        $config = array(
            'db.host' => 'mongodb://127.0.0.1:27017',
            'db.db' => getenv('XHGUI_MONGO_DB') ?: 'xhprof',
            'db.options' => array(),
        );
        $this->saver = $this->createSaver('mongodb', $config);
    }

    public function testSaveEmpty()
    {
        $this->saver->save(array());
    }
}
