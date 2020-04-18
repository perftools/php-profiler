<?php

namespace Xhgui\Profiler\Saver;

use Xhgui_Saver_Interface;

interface SaverInterface extends Xhgui_Saver_Interface
{
    /**
     * @return bool
     */
    public function isSupported();

    /**
     * @return Xhgui_Saver_Interface
     */
    public function getHandler();
}
