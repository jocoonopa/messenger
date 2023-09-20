<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Illuminate\Support\Arr;
use Kerox\Messenger\Model\Callback\Comment;

final class CommentEvent extends AbstractEvent
{
    public const NAME = 'comment';

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var \Kerox\Messenger\Model\Callback\Comment
     */
    protected $comment;

    /**
     * MessageEvent constructor.
     */
    public function __construct(string $senderId, string $recipientId, int $timestamp, Comment $message)
    {
        parent::__construct($senderId, $recipientId);

        $this->timestamp = $timestamp;
        $this->message = $message;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return \Kerox\Messenger\Event\CommentEvent
     */
    public static function create(array $payload): ?self
    {
        $value = Arr::get($payload, 'value');

        $senderId = Arr::get($value, 'from.id');

        if (blank($senderId)) {
            return null;
        }

        $recipientId = Arr::first(explode('_', Arr::get($value, 'post_id')));
        $timestamp =  Arr::get($value, 'created_time');

        $comment = Comment::create($value);

        return new self($senderId, $recipientId, $timestamp, $comment);
    }
}
