<?php

namespace BotHelp;

use Worker;

class Composer extends Worker
{
    private $lastStoredId = 0;

    public function canStore(int $id)
    {
        return $this->synchronized(function($id) {
            return ($id - $this->lastStoredId) == 1;
        }, $id);
    }

    public function stored(int $id) {
        // здесь синхронизация, в принципе, не обязательна
        // т.к. сюда дойти может только один поток
        // но для избежания ожиданий из-за кеша процессора
        // лучше поставить
        $this->synchronized(function($id) {
            $this->lastStoredId = $id;
        }, $id);
    }
}