<?php

namespace BotHelp;

use PDO;
use PhpAmqpLib\Message\AMQPMessage;
use Redis;
use Threaded;

class Processor extends Threaded
{
    private $dsl;

    private $redisHost;

    private $queueName;


    public function __construct(string $dsl, string $redisHost, string $queueName)
    {
        $this->dsl = $dsl;
        $this->redisHost = $redisHost;
        $this->queueName = $queueName;
    }


    public function run()
    {
        $db = new PDO($this->dsl);
        $redis = new Redis();
        $redis->connect($this->redisHost);

        while (true) {
            $this->process($db, $redis);
        }
    }

    private function process(PDO $db, Redis $redis) {
        /** @var AMQPMessage $msg */

        while (!($msg = $redis->rPop($this->queueName))) {}

        $parsed = json_decode($msg);
        if ($this->worker->canStore($parsed->id)) {
            
        }
    }
}