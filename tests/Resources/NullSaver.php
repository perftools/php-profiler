<?php

namespace Xhgui\Profiler\Test\Resources;

use Exception;
use Xhgui\Profiler\Saver\SaverInterface;

class NullSaver implements SaverInterface
{
    public function isSupported()
    {
        return true;
    }

    public function save(array $data)
    {
        throw new Exception('NullSaver executed');
    }
}