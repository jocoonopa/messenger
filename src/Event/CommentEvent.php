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

        if (! static::shouldCreate($payload)) {
            return null;
        }

        $recipientId = static::resolveRecipientId($payload);

        $object = Arr::get($payload, 'object');

        $timestamp = Arr::get($value, 'created_time', now()->timestamp);

        switch ($object) {
            case 'page':
                $comment = Comment::create($value);
                break;

            case 'instagram':
                $comment = Comment::create([
                    'comment_id' => Arr::get($value, 'id'),
                    'message' => Arr::get($value, 'text'),
                    'post_id' => Arr::get($value, 'media.id'),
                    'is_live' => Arr::get($payload, 'field') === 'live_comments',
                ]);
                break;

            default:
                return null;
                break;
        }

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

    protected static function resolveRecipientId(array $payload)
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

    protected static function shouldCreate(array $payload): bool
    {
        $object = Arr::get($payload, 'object');

        $field = Arr::get($payload, 'field');

        switch ($object) {
            case 'instagram':
                return in_array($field, [
                    'comments', 'live_comments',
                ]);
                break;

            case 'page':
                return $field === 'feed' &&
                    Arr::get($payload, 'value.item') === 'comment';
                break;

            default:
                return false;
                break;
        }
    }
}
