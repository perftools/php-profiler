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
        $config = array(
            'pdo' => array(
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
        $data = $this->getSample('session_meta.json');
        $data['profile'] = $this->getSample('tideways_xhprof_cpu_memory.json');

        $this->saver->save($data);
    }
}
