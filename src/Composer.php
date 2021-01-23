<?php

namespace BotHelp;

use Volatile;

class Composer extends Volatile
{
    /**
     * @var int
     */
    private $lastStoredId = 0;

    public function canStore(int $id)
    {
        return ($id - $this->lastStoredId) == 1;
    }

    public function stored(int $id) {
        $this->lastStoredId = $id;
    }
}