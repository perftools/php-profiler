<?php

namespace Xhgui\Profiler\Saver;

use Exception;

/**
 * Save to stack of savers
 */
class StackSaver implements SaverInterface
{
    /** @var array */
    private $savers;
    /** @var bool */
    private $saveAll;

    public function __construct(array $savers, $saveAll = false)
    {
        $this->savers = $savers;
        $this->saveAll = (bool)$saveAll;
    }

    public function isSupported()
    {
        return true;
    }

    public function save(array $data)
    {
        $result = false;
        foreach ($this->savers as $saver) {
            try {
                if (!$saver->save($data)) {
                    continue;
                }
                $result = true;
            } catch (Exception $e) {
                continue;
            }

            // if not save all, then break on first successful save
            if (!$this->saveAll) {
                break;
            }
        }

        return $result;
    }
}
