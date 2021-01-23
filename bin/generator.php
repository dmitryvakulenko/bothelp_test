<?php
require_once __DIR__ . '/../vendor/autoload.php';

const QUEUE_NAME = 'events';
const MAX_EVENTS = 10000;

$redis = new Redis();
$redis->connect('queue');
$redis->del(QUEUE_NAME);

$totalAmount = 0;
while ($totalAmount < MAX_EVENTS) {
    for ($account = 1; $account <= 1000; $account++) {
        $eventsAmount = rand(1, 10);
        for ($event = 1; $event <= $eventsAmount; $event++) {
            $record = [
                'account_id' => $account,
                'event_id' => $event
            ];

            $msg = [
                'index' => 1 + $totalAmount++, // индекс должен начинаться с 1
                'record' => $record
            ];

            $res = $redis->lPush(QUEUE_NAME, json_encode($msg));
            if ($res === false) {
                echo "Error inserting records";
                die;
            }

            if ($totalAmount >= MAX_EVENTS) {
                break 3;
            }
        }
    }
}

$redis->close();