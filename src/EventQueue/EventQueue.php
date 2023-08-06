<?php declare(strict_types=1);

namespace App\EventQueue;

class EventQueue
{
    private string $queuePath;

    public function __construct(string $queuePath)
    {
        $this->queuePath = $queuePath;
    }

    public function enqueue(string $message): void
    {
        file_put_contents($this->queuePath, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function dequeue(): ?string
    {
        $handle = fopen($this->queuePath, 'r+');

        if (!$handle) {
            throw new \RuntimeException('Failed to open the event queue.');
        }

        if (flock($handle, LOCK_EX)) {
            $messages = file($this->queuePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if (empty($messages)) {
                flock($handle, LOCK_UN);
                fclose($handle);
                return null; // No more messages in the queue
            }

            $message = array_shift($messages);
            file_put_contents($this->queuePath, implode(PHP_EOL, $messages) . PHP_EOL);

            flock($handle, LOCK_UN);
            fclose($handle);

            return $message;
        } else {
            fclose($handle);
            throw new \RuntimeException('Failed to acquire lock for dequeueing the message.');
        }
    }
}
