<?php

use BotHelp\{Composer, Processor};

require_once __DIR__ . '/../vendor/autoload.php';

const TABLE_NAME = 'events';
const QUEUE_NAME = 'events';
const DSL = 'pgsql:host=postgres;port=5432;dbname=test;user=test;password=dev';
const REDIS_HOST = 'queue';
const THREADS_AMOUNT = 16;

$db = new PDO(DSL);
$res = $db->exec("create table if not exists " . TABLE_NAME . " (id serial not null primary key, account_id int not null, event_id int not null)");
if ($res === false) {
    echo "Error table creation " . print_r($db->errorInfo(), true);
}
$res = $db->exec("delete from " . TABLE_NAME);
if ($res === false) {
    echo "Error table clearing " . print_r($db->errorInfo(), true);
}

$composer = new Composer();
$pool = new Pool(THREADS_AMOUNT, \BotHelp\Worker::class, [$composer]);
for ($i = 0; $i < THREADS_AMOUNT; $i++) {
    $pool->submit(new Processor(DSL, REDIS_HOST, QUEUE_NAME, TABLE_NAME));
}

while ($pool->collect());
$pool->shutdown();