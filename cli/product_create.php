<?php declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Product\Product;
use App\Storage\Writer;
use App\EventQueue\EventQueue;

if ($argc !== 4) {
    echo "Usage: php {$argv[0]} <id> <name> <price>" . PHP_EOL;
    exit(1);
}

$id = (int) $argv[1];
$name = $argv[2];
$price = (float) $argv[3];

$product = new Product($id, $name, $price);
$writer = new Writer();
$writer->create("product_$id", json_encode($product->toArray()));

$eventQueue = new EventQueue('storage/event_queue.txt');
$floatPrice = number_format($price, 2);
$eventQueue->enqueue("ProductCreated $id $name $floatPrice");

echo "Product created: {$product->getId()} {$product->getName()} {$product->getPrice()}" . PHP_EOL;
