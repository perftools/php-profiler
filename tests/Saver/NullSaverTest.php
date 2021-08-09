<?php

namespace Xhgui\Profiler\Test\Saver;

use Exception;
use Xhgui\Profiler\Profiler;
use Xhgui\Profiler\Test\Resources\NullSaver;
use Xhgui\Profiler\Test\TestCase;

class NullSaverTest extends TestCase
{
    public function setCustomSaver()
    {
        $saver = new NullSaver();
        $profiler = new Profiler(array());
        $profiler->setSaver($saver);
        $profiler->start();
        try {
            $profiler->stop();
            $this->markTestIncomplete('Custom saver not executed');
        } catch (Exception $e) {
            $this->assertEquals('CustomSaver executed', $e->getMessage());
        }
    }
}
