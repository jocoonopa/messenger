<?php

declare(strict_types=1);

namespace Kerox\Messenger\Model\Callback;

use Illuminate\Support\Arr;

class Comment
{
    /**
     * @var string
     */
    protected $commentId;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $postId;

    /**
     * @var string|null
     */
    protected $permalinkUrl;

    /**
     * Message constructor.
     *
     * @param string $text
     * @param string $quickReply
     */
    public function __construct(
        $commentId,
        $message,
        $postId,
        $permalinkUrl,
    ) {
        $this->commentId = $commentId;
        $this->message = $message;
        $this->postId = $postId;
        $this->permalinkUrl = $permalinkUrl;
    }

    /**
     * @return \Kerox\Messenger\Model\Callback\Message
     */
    public static function create(array $callbackData)
    {
        return new self(
            commentId: Arr::get($callbackData, 'comment_id'),
            message: Arr::get($callbackData, 'message'),
            postId: Arr::get($callbackData, 'post_id'),
            permalinkUrl: Arr::get($callbackData, 'post.permalink_url'),
        );
    }

    /**
     * @return string
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * @param string $commentId
     *
     * @return self
     */
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param string|null $postId
     *
     * @return self
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPermalinkUrl()
    {
        return $this->permalinkUrl;
    }

    /**
     * @param string|null $permalinkUrl
     *
     * @return self
     */
    public function setPermalinkUrl($permalinkUrl)
    {
        $this->permalinkUrl = $permalinkUrl;

        return $this;
    }
}
