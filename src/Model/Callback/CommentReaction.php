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
     * payload 範例如下:
     *
     * [
            "entry" => [
                [
                    "id" => "100263333353904",
                    "time" => 1725506823,
                    "changes" => [
                        [
                            "value" => [
                                "from" => [
                                    "id" => "481333334987702",
                                    "name" => "Winfuture Losenow",
                                ],
                                "post_id" => "10026555653904_445555741589471",
                                "created_time" => 1725506822,
                                "item" => "reaction",
                                "parent_id" => "10026555653904_445555741589471",
                                "reaction_type" => "haha",
                                "verb" => "add",
                            ],
                            "field" => "feed",
                        ],
                    ],
                ],
            ],
            "object" => "page",
        ]
     *
     * @return \Kerox\Messenger\Model\Callback\CommentReaction
     */
    public static function create(array $callbackData): self
    {
        return new self(Arr::get($callbackData, 'value.reaction_type', self::REACTION_LIKE));
    }
}
