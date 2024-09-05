<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Illuminate\Support\Arr;
use Kerox\Messenger\Model\Callback\CommentReaction;

class CommentReactionEvent extends AbstractEvent
{
    public const NAME = 'comment_reaction';

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var string|null
     */
    protected $postId;

    /**
     * @var string|null
     */
    protected $verb;

    /**
     * @var \Kerox\Messenger\Model\Callback\CommentReaction
     */
    protected $reaction;

    public function __construct(string $senderId, string $recipientId, int $timestamp, string|null $postId, string|null $verb, CommentReaction $reaction)
    {
        parent::__construct($senderId, $recipientId);

        $this->timestamp = $timestamp;

        $this->reaction = $reaction;

        $this->postId = $postId;

        $this->verb = $verb;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getReaction(): CommentReaction
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
        $value = Arr::get($payload, 'value');

        $senderId = Arr::get($value, 'from.id');

        if (blank($senderId)) {
            return null;
        }

        $recipientId = static::resolveRecipientId($payload);

        $timestamp = Arr::get($value, 'created_time', now()->timestamp);

        $postId = Arr::get($value, 'post_id', now()->timestamp);

        $verb = Arr::get($value, 'verb', now()->timestamp);

        $reaction = CommentReaction::create($payload);

        if (blank($senderId)) {
            return null;
        }

        return new self($senderId, $recipientId, $timestamp, $postId, $verb, $reaction);
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

    /**
     * @return string
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param string $postId
     *
     * @return self
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param string $verb
     *
     * @return self
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
    }
}
