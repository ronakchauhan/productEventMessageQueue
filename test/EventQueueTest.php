<?php declare(strict_types=1);

use App\EventQueue\EventQueue;
use PHPUnit\Framework\TestCase;

class EventQueueTest extends TestCase
{
    private const QUEUE_PATH = 'test_queue.txt';

    protected function setUp(): void
    {
        // Remove any existing queue file before each test
        if (file_exists(self::QUEUE_PATH)) {
            unlink(self::QUEUE_PATH);
        }
    }

    public function testEnqueueAndDequeue(): void
    {
        $queue = new EventQueue(self::QUEUE_PATH);

        // Enqueue a message
        $queue->enqueue('Message 1');
        $this->assertEquals('Message 1', $queue->dequeue());

        // Enqueue more messages
        $queue->enqueue('Message 2');
        $queue->enqueue('Message 3');

        // Dequeue messages in the same order they were enqueued
        $this->assertEquals('Message 2', $queue->dequeue());
        $this->assertEquals('Message 3', $queue->dequeue());

        // Dequeue from an empty queue should return null
        $this->assertNull($queue->dequeue());
    }

    public function testDequeueEmptyQueue(): void
    {
        $queue = new EventQueue(self::QUEUE_PATH);

        // Attempt to dequeue from an empty queue should return null
        $this->assertNull($queue->dequeue());
    }

    public function testEnqueueMultipleMessages(): void
    {
        $queue = new EventQueue(self::QUEUE_PATH);

        // Enqueue multiple messages
        $queue->enqueue('Message 1');
        $queue->enqueue('Message 2');
        $queue->enqueue('Message 3');

        // Dequeue messages in the same order they were enqueued
        $this->assertEquals('Message 1', $queue->dequeue());
        $this->assertEquals('Message 2', $queue->dequeue());
        $this->assertEquals('Message 3', $queue->dequeue());

        // Dequeue from an empty queue should return null
        $this->assertNull($queue->dequeue());
    }

    public function testDequeueWithConcurrentAccess(): void
    {
        // This test simulates concurrent access by multiple processes to the same queue file
        $queue = new EventQueue(self::QUEUE_PATH);

        // Enqueue a message in a separate process
        exec('php enqueue_message.php "Concurrent Message"', $output, $returnValue);

        // Sleep briefly to ensure the enqueued message is processed by the other process
        usleep(500000);

        // Attempt to dequeue the message in the current process
        $this->assertEquals('Concurrent Message', $queue->dequeue());
    }
}
