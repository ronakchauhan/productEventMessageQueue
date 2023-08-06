<?php declare(strict_types=1);

namespace App\EventQueue;

class EventWorker
{
    private EventQueue $eventQueue;

    public function __construct(EventQueue $eventQueue)
    {
        $this->eventQueue = $eventQueue;
    }

    public function processMessages(): string
    {
        $output = [];
        while (true) {
            $message = $this->eventQueue->dequeue();

            if ($message === null) {
                break; // No more messages in the queue, exit the loop
            }

            $output[] = $this->processEventMessage($message);
        }

        return trim(implode(PHP_EOL, $output));
    }

    public function processEventMessage(string $message): string
    {
        if ($message === '') {
            return ''; // Empty message, handle gracefully
        }

        $eventData = explode(' ', $message);

        if (count($eventData) < 2) {
            throw new \RuntimeException('Invalid event message: ' . $message);
        }

        $eventType = array_shift($eventData);
        $productId = (int) array_shift($eventData);

        switch ($eventType) {
            case 'ProductCreated':
                [$name, $price] = $eventData;
                return trim("Product created: $productId $name $price");

            case 'ProductDeleted':
                return trim("Product deleted: $productId");

            case 'ProductUpdated':
                return trim($this->formatProductUpdatedEvent($productId, $eventData));

            default:
                throw new \RuntimeException('Invalid event type: ' . $eventType);
        }
    }

    public function formatProductUpdatedEvent(int $productId, array $eventData): string
    {
        return 'Product updated: ' . $productId . ' ' . implode(' ', $eventData);
    }
}
