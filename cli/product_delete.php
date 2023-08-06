<?php declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Storage\Reader;
use App\Storage\Writer;
use App\EventQueue\EventQueue;

if ($argc !== 2) {
    echo "Usage: php {$argv[0]} <id>" . PHP_EOL;
    exit(1);
}

$id = (int) $argv[1];

$reader = new Reader();
$productData = $reader->read("product_$id");

$writer = new Writer();
$writer->delete("product_$id");

// Enqueue the event
$eventQueue = new EventQueue('storage/event_queue.txt');
$eventQueue->enqueue("ProductDeleted $id");

echo "Product deleted: $id";

