<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Kerox\Messenger\Model\Callback\Postback;

final class PostbackEvent extends AbstractEvent
{
    public const NAME = 'postback';

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var \Kerox\Messenger\Model\Callback\Postback
     */
    protected $postback;

    /**
     * PostbackEvent constructor.
     */
    public function __construct(string $senderId, string $recipientId, int $timestamp, Postback $postback)
    {
        parent::__construct($senderId, $recipientId);

        $this->timestamp = $timestamp;
        $this->postback = $postback;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getPostback(): Postback
    {
        return $this->postback;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return \Kerox\Messenger\Event\PostbackEvent
     */
    public static function create(array $payload): self
    {
        $senderId = isset($payload['sender']['id']) ? $payload['sender']['id'] : '';
        $recipientId = isset($payload['recipient']['id']) ? $payload['recipient']['id'] : '';
        $timestamp = isset($payload['timestamp']) ? $payload['timestamp'] : '';
        $postback = isset($payload['postback']) ? $payload['postback'] : '';

        $postback = Postback::create($postback);

        return new self($senderId, $recipientId, $timestamp, $postback);
    }
}
