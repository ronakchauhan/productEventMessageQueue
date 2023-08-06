<?php declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Product\Product;
use App\Storage\Reader;
use App\Storage\Writer;
use App\EventQueue\EventQueue;

if ($argc !== 4) {
    echo "Usage: php {$argv[0]} <id> <name> <price>" . PHP_EOL;
    exit(1);
}

$id = (int) $argv[1];
$name = $argv[2];
$price = (float) $argv[3];

$reader = new Reader();
$productData = $reader->read("product_$id");
$productArray = json_decode($productData, true);

$product = new Product($productArray['id'], $productArray['name'], $productArray['price']);
if ($name !== $product->getName()) {
    $product->setName($name);
}

if ($price !== $product->getPrice()) {
    $product->setPrice($price);
}

$writer = new Writer();
$writer->update("product_$id", json_encode($product->toArray()));
$nameUpdate = "";
$priceUpdate = "";

if ($productArray['name'] !== $name) {
    $nameUpdate = $productArray['name'] . " => " . $name;
}

if ($productArray['price'] !== $name) {
    $priceUpdate = number_format($productArray['price'], 2) . " => " . number_format($price, 2);;
}

// Enqueue the event
$eventQueue = new EventQueue('storage/event_queue.txt');
$eventQueue->enqueue("ProductUpdated $id name: $nameUpdate, price: $priceUpdate");

echo 'Product updated: ' . implode(', ', $product->getChangedFields(new Product($id, $name, $price))) . PHP_EOL;
