<?php

namespace Xhgui\Profiler\Saver;

use MongoDate;
use Xhgui_Saver_Mongo;

/**
 * @property Xhgui_Saver_Mongo $saver
 */
class MongoSaver extends AbstractSaver
{
    public function isSupported()
    {
        if (!$this->saver instanceof Xhgui_Saver_Mongo) {
            return false;
        }

        return class_exists('MongoClient');
    }

    public function save(array $data)
    {
        $this->convertTimestamps($data['meta']);

        return parent::save($data);
    }

    private function convertTimestamps(array &$meta)
    {
        $ts = $meta['request_ts_micro'];
        $meta['request_ts'] = new MongoDate($ts['sec']);
        $meta['request_ts_micro'] = new MongoDate($ts['sec'], $ts['usec']);
    }
}
