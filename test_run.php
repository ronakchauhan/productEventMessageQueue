<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

// Create the product
shell_exec('php cli/product_create.php 42 Staubsauger 100.00');

// Update the product
shell_exec('php cli/product_update.php 42 NewStaubsauger 150.00');

// Delete the product
shell_exec('php cli/product_delete.php 42');

// Process the events
$processedMessages = "";
$processedMessages = shell_exec('php -f cli/event_worker.php');

$expectedOutput = "Product created: 42 Staubsauger 100.00\nProduct updated: 42 name: Staubsauger => NewStaubsauger, price: 100.00 => 150.00\nProduct deleted: 42\n";

if (trim($processedMessages) == trim($expectedOutput)) {
    echo 'It works!' . PHP_EOL;
} else {
    echo 'Something went wrong!' . PHP_EOL;
}
