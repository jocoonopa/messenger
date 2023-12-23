<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Illuminate\Support\Arr;
use Kerox\Messenger\Model\Callback\Reaction;

class ReactionEvent extends AbstractEvent
{
    public const NAME = 'reaction';

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var \Kerox\Messenger\Model\Callback\Reaction
     */
    protected $reaction;

    public function __construct(string $senderId, string $recipientId, int $timestamp, Reaction $reaction)
    {
        parent::__construct($senderId, $recipientId);

        $this->timestamp = $timestamp;
        $this->reaction = $reaction;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getReaction(): Reaction
    {
        return $this->reaction;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return \Kerox\Messenger\Event\ReactionEvent
     */
    public static function create(array $payload): ?self
    {
        $senderId = Arr::get($payload, 'sender.id');
        $recipientId = Arr::get($payload, 'recipient.id');
        $timestamp = Arr::get($payload, 'timestamp');
        $reaction = Reaction::create(Arr::get($payload, 'reaction'));

        if (blank($senderId)) {
            return null;
        }

        return new self($senderId, $recipientId, $timestamp, $reaction);
    }
}
