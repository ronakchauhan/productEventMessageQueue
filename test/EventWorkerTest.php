<?php declare(strict_types=1);

namespace App\Tests\EventQueue;

use PHPUnit\Framework\TestCase;
use App\EventQueue\EventWorker;
use App\EventQueue\EventQueue;

class EventWorkerTest extends TestCase
{
    public function testProcessMessagesWithEmptyQueue(): void
    {
        $eventQueue = $this->createMock(EventQueue::class);
        $eventQueue->expects($this->once())
            ->method('dequeue')
            ->willReturn(null);

        $eventWorker = new EventWorker($eventQueue);
        $result = $eventWorker->processMessages();

        $this->assertSame('', $result);
    }

    public function testProcessMessagesWithMultipleMessages(): void
    {
        $queueData = [
            'ProductCreated 1 Product1 100.00',
            'ProductUpdated 2 name: OldProduct price: 50.00 => 75.00',
            'ProductDeleted 3',
        ];

        $eventQueue = $this->createMock(EventQueue::class);
        $eventQueue->expects($this->exactly(4))
            ->method('dequeue')
            ->willReturnOnConsecutiveCalls(...$queueData);

        $eventWorker = new EventWorker($eventQueue);
        $result = $eventWorker->processMessages();

        $expectedResult = "Product created: 1 Product1 100.00\n" .
            "Product updated: 2 name: OldProduct price: 50.00 => 75.00\n" .
            "Product deleted: 3";
        $this->assertSame($expectedResult, $result);
    }

    public function testProcessMessagesWithInvalidEventMessage(): void
    {
        $invalidMessage = 'InvalidMessage';
        $eventQueue = $this->createMock(EventQueue::class);
        $eventQueue->expects($this->once())
            ->method('dequeue')
            ->willReturn($invalidMessage);

        $eventWorker = new EventWorker($eventQueue);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid event message: ' . $invalidMessage);

        $eventWorker->processMessages();
    }

    // Test individual methods

    public function testProcessEventMessageWithEmptyMessage(): void
    {
        $eventWorker = new EventWorker($this->createMock(EventQueue::class));
        $message = '';
        $result = $eventWorker->processEventMessage($message);
        $this->assertSame('', $result);
    }

    public function testProcessEventMessageWithValidProductCreatedEvent(): void
    {
        $eventWorker = new EventWorker($this->createMock(EventQueue::class));
        $message = 'ProductCreated 1 Product1 100.00';
        $result = $eventWorker->processEventMessage($message);
        $this->assertSame('Product created: 1 Product1 100.00', $result);
    }

    public function testProcessEventMessageWithValidProductDeletedEvent(): void
    {
        $eventWorker = new EventWorker($this->createMock(EventQueue::class));
        $message = 'ProductDeleted 2';
        $result = $eventWorker->processEventMessage($message);
        $this->assertSame('Product deleted: 2', $result);
    }

    public function testProcessEventMessageWithValidProductUpdatedEvent(): void
    {
        $eventWorker = new EventWorker($this->createMock(EventQueue::class));
        $message = 'ProductUpdated 3 name: OldProduct price: 50.00 => 75.00';
        $result = $eventWorker->processEventMessage($message);
        $this->assertSame('Product updated: 3 name: OldProduct price: 50.00 => 75.00', $result);
    }

    public function testProcessEventMessageWithInvalidEventMessage(): void
    {
        $eventWorker = new EventWorker($this->createMock(EventQueue::class));
        $message = 'InvalidMessage';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid event message: ' . $message);
        $eventWorker->processEventMessage($message);
    }

    public function testFormatProductUpdatedEvent(): void
    {
        $eventWorker = new EventWorker($this->createMock(EventQueue::class));
        $productId = 1;
        $eventData = ['name:', 'OldProduct', 'price:', '50.00', '=>', '75.00'];
        $result = $eventWorker->formatProductUpdatedEvent($productId, $eventData);
        $this->assertSame('Product updated: 1 name: OldProduct price: 50.00 => 75.00', $result);
    }
}
