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

    private $tableName;

    public function __construct(string $dsl, string $redisHost, string $queueName, string $tableName)
    {
        $this->dsl = $dsl;
        $this->redisHost = $redisHost;
        $this->queueName = $queueName;
        $this->tableName = $tableName;
    }


    public function run()
    {
        $db = new PDO($this->dsl);
        $stmt = $db->prepare("insert into " . $this->tableName . " (account_id, event_id) values (:account, :event)");

        $redis = new Redis();
        $redis->connect($this->redisHost);

        while (true) {
            $this->process($stmt, $redis);
        }
    }

    private function process(\PDOStatement $stmt, Redis $redis) {
        while (!($msg = $redis->rPop($this->queueName))) {}

        $parsedMsg = json_decode($msg, true);
        sleep(rand(0, 3));

        while (!$this->worker->canStore($parsedMsg['index'])) {
            usleep(1000);
        }

        $stmt->execute([
            'account' => $parsedMsg['record']['account_id'],
            'event' => $parsedMsg['record']['event_id']]);
        echo sprintf("Processed account %d event %d\n", $parsedMsg['record']['account_id'], $parsedMsg['record']['event_id']);
        $this->worker->stored($parsedMsg['index']);
    }
}