<?php
/**
 * © UTS Group 2021
 */


namespace BotHelp;


class Worker extends \Worker
{
    /**
     * @var Composer
     */
    private $composer;

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }


    public function canStore(int $id)
    {
        return $this->synchronized(function($id) {
            return $this->composer->canStore($id);
        }, $id);
    }

    public function stored(int $id) {
        return $this->synchronized(function($id) {
            $this->composer->stored($id);
        }, $id);
    }
}