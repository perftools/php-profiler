<?php

namespace Xhgui\Profiler\Saver;

use Xhgui_Saver_Upload;

/**
 * @property Xhgui_Saver_Upload $saver
 */
class UploadSaver extends AbstractSaver
{
    public function isSupported()
    {
        if (!$this->saver instanceof Xhgui_Saver_Upload) {
            return false;
        }

        return function_exists('json_encode') && function_exists('curl_init');
    }
}
