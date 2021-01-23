<?php
require_once __DIR__ . '/../vendor/autoload.php';

const QUEUE_NAME = 'records';

$redis = new Redis();
$redis->connect('queue');

$redis->lPush(QUEUE_NAME, "hello world");

$redis->close();