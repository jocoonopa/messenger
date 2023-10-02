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
    public function __construct(string $senderId, string $recipientId, int $timestamp, Comment $comment)
    {
        parent::__construct($senderId, $recipientId);

        $this->timestamp = $timestamp;
        $this->comment = $comment;
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

        $field =  Arr::get($payload, 'payload');

        if (! $this->shouldCreate()) {
            return null;
        }

        $recipientId = $object === 'page' ? Arr::first(explode('_', Arr::get($value, 'post_id'))) : $this->resolveRecipientId($payload);

        $timestamp =  Arr::get($value, 'created_time');

        $comment = Comment::create($value);

        return new self($senderId, $recipientId, $timestamp, $comment);
    }

    /**
     * @return \Kerox\Messenger\Model\Callback\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param \Kerox\Messenger\Model\Callback\Comment $comment
     *
     * @return self
     */
    public function setComment(Comment $comment)
    {
        $this->comment = $comment;

        return $this;
    }

    protected function resolveRecipientId(array $payload)
    {
        $object = Arr::get($payload, 'object');

        if ($object === 'page') {
            return Arr::first(explode('_', Arr::get($payload, 'value.post_id', '')));
        }

        if ($object === 'instagram') {
            return Arr::get($payload, 'recipient_id', '');
        }

        return '';
    }

    protected function shouldCreate(array $payload): bool
    {
        $object = Arr::get($payload, 'object');

        switch ($object) {
            case 'instagram':
                return Arr::get($payload, 'field') === 'comment';
                break;

            case 'page':
                return Arr::get($payload, 'field') === 'feed' &&
                    Arr::get($payload, 'value.item') === 'comment';
                break;

            default:
                return false;
                break;
        }
    }
}
