<?php

namespace Xhgui\Profiler\Saver;

use Exception;

class FileSaver implements SaverInterface
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function isSupported()
    {
        if (!function_exists('json_encode')) {
            return false;
        }

        try {
            return is_writable(dirname($this->file));
        } catch (Exception $e) {
            return false;
        }
    }

    public function save(array $data)
    {
        $fileName = $this->file;
        $json = json_encode($data);

        return file_put_contents($fileName, $json . PHP_EOL, FILE_APPEND);
    }
}
