<?php

namespace Xhgui\Profiler\Test\Saver;

use Xhgui\Profiler\Saver\PdoSaver;
use Xhgui\Profiler\Test\TestCase;

/**
 * @requires extension pdo
 * @property PdoSaver $saver
 */
class PdoSaverTest extends TestCase
{
    public function setUp()
    {
        $this->skipIfNoXhguiCollector();
        $config = array(
            'save.handler.pdo' => array(
                'dsn' => sprintf('sqlite:%s/php-profiler-test-save.sqlite3', sys_get_temp_dir()),
                'user' => 'xhgui',
                'pass' => 'xhgui',
                'table' => 'xhgui',
            ),
        );
        $this->saver = $this->createSaver('pdo', $config);
    }

    public function testSaveEmpty()
    {
        $data = $this->getResource('session_meta.json');
        $data['profile'] = $this->getResource('tideways_xhprof_cpu_memory.json');

        $this->saver->save($data);
    }
}
