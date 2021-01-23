<?php

use BotHelp\{Composer, Processor};

require_once __DIR__ . '/../vendor/autoload.php';

const TABLE_NAME = 'records';
const QUEUE_NAME = 'records';
const DSL = 'pgsql:host=postgres;port=5432;dbname=test;user=test;password=dev';
const REDIS_HOST = 'queue';

$db = new PDO(DSL);
$db->exec("create table if not exist " . TABLE_NAME . " (id serial not null primary key, client_id, record_id)");

$pool = new Pool(1, Composer::class);
for ($i = 0; $i < 1; $i++) {
    $pool->submit(new Processor(DSL, REDIS_HOST, QUEUE_NAME));
}
