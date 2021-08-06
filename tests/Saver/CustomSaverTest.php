<?php

namespace Xhgui\Profiler\Test\Saver;

use Xhgui\Profiler\Profiler;
use Xhgui\Profiler\Saver\FileSaver;
use Xhgui\Profiler\Saver\SaverInterface;
use Xhgui\Profiler\Test\TestCase;

/**
 * @requires extension json
 * @property FileSaver $saver
 */

class CustomSaverTest extends TestCase
{
    public function setCustomSaver()
    {
        $saver = new CustomSaver();
        $profiler = new Profiler(array());
        $profiler->setCustomSaver($saver);
        $profiler->start();
        try {
            $profiler->stop();
            $this->markTestIncomplete('Custom saver not executed');
        } catch (\Exception $e) {
            $this->assertEquals('CustomSaver executed', $e->getMessage());
        }
    }
}


class CustomSaver implements SaverInterface
{
    public function isSupported()
    {
        return true;
    }

    public function save(array $data)
    {
        throw new \Exception('CustomSaver executed');
    }
}
