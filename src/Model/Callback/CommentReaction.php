<?php

declare(strict_types=1);

namespace Kerox\Messenger\Model\Callback;

use Illuminate\Support\Arr;

class CommentReaction
{
    public const REACTION_SMILE = 'smile';
    public const REACTION_ANGRY = 'angry';
    public const REACTION_SAD = 'sad';
    public const REACTION_WOW = 'wow';
    public const REACTION_LOVE = 'love';
    public const REACTION_LIKE = 'like';
    public const REACTION_DISLIKE = 'dislike';
    public const REACTION_OTHER = 'other';

    public const ACTION_REACT = 'react';
    public const ACTION_UNREACT = 'unreact';

    /**
     * @var string
     */
    protected $reaction;

    /**
     * Reaction constructor.
     */
    public function __construct(string $reaction)
    {
        $this->reaction = $reaction;
    }

    public function getReaction(): string
    {
        return $this->reaction;
    }

    /**
     * @return \Kerox\Messenger\Model\Callback\Reaction
     */
    public static function create(array $callbackData): self
    {
        return new self(Arr::get($callbackData, 'reaction_type', 'like'));
    }
}
