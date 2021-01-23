<?php

namespace BotHelp;

use Worker;

class Composer extends Worker
{
    private $lastStoredId;

    public function canStore(int $id)
    {
        $this->synchronized(function($id) {
            return ($id - $this->lastStoredId) == 1;
        }, $id);
    }
}