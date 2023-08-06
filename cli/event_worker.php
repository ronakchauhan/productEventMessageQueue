<?php declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\EventQueue\EventQueue;
use App\EventQueue\EventWorker;

$queuePath = 'storage/event_queue.txt';
$eventQueue = new EventQueue($queuePath);
$eventWorker = new EventWorker($eventQueue);

echo $eventWorker->processMessages() . PHP_EOL;
