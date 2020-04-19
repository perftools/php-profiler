<?php

namespace Xhgui\Profiler\Saver;

use Exception;
use ReflectionClass;
use Xhgui_Saver_File;

/**
 * @property Xhgui_Saver_File $saver
 */
class FileSaver extends AbstractSaver
{
    public function isSupported()
    {
        if (!$this->saver instanceof Xhgui_Saver_File) {
            return false;
        }
        if (!function_exists('json_encode')) {
            return false;
        }

        try {
            return is_writable(dirname($this->getSaveFile()));
        } catch (Exception $e) {
            return false;
        }
    }

    private function getSaveFile()
    {
        $rc = new ReflectionClass($this->saver);
        $property = $rc->getProperty('_file');
        $property->setAccessible(true);

        return $property->getValue($this->saver);
    }
}
