<?php

namespace Xhgui\Profiler\Test\Saver;

use Xhgui\Profiler\Saver\FileSaver;
use Xhgui\Profiler\Test\TestCase;

/**
 * @requires extension json
 * @property FileSaver $saver
 */
class FileSaverTest extends TestCase
{
    public function setUp()
    {
        $config = array(
            'save.handler.file' => array(
                'filename' => sys_get_temp_dir() . '/php-profiler-test-save.json',
            ),
        );
        $this->saver = $this->createSaver('file', $config);
    }

    public function testSaveEmpty()
    {
        $this->saver->save(array());
    }
}
