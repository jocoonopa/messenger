<?php

declare(strict_types=1);

namespace Kerox\Messenger\Model\Callback;

class Message
{
    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var string|null
     */
    protected $text;

    /**
     * @var string|null
     */
    protected $quickReply;

    /**
     * @var array
     */
    protected $attachments;

    /**
     * @var string|null
     */
    protected $replyTo;

    /**
     * @var array
     */
    protected $entities;

    /**
     * @var array
     */
    protected $traits;

    /**
     * @var array
     */
    protected $detectedLocales;

    /**
     * Message constructor.
     *
     * @param string $text
     * @param string $quickReply
     */
    public function __construct(
        string $messageId,
        ?string $text = null,
        ?string $quickReply = null,
        array $attachments = [],
        $replyTo = null,
        array $entities = [],
        array $traits = [],
        array $detectedLocales = []
    ) {
        $this->messageId = $messageId;
        $this->text = $text;
        $this->quickReply = $quickReply;
        $this->attachments = $attachments;
        $this->replyTo = $replyTo;
        $this->entities = $entities;
        $this->traits = $traits;
        $this->detectedLocales = $detectedLocales;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function setText($value)
    {
        $this->text = (string) $value;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function hasText(): bool
    {
        return $this->text !== null && $this->text !== '';
    }

    public function getQuickReply(): ?string
    {
        return $this->quickReply;
    }

    public function hasQuickReply(): bool
    {
        return $this->quickReply !== null && $this->quickReply !== '';
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function hasReplyTo(): bool
    {
        return !empty($this->replyTo);
    }

    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }

    public function isReply(): bool
    {
        return $this->replyTo !== null;
    }

    public function setEntities($entities): array
    {
        $this->entities = $entities;

        return $this;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function hasEntities(): bool
    {
        return !empty($this->entities);
    }

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function hasTraits(): bool
    {
        return !empty($this->traits);
    }

    public function getDetectedLocales(): array
    {
        return $this->detectedLocales;
    }

    public function hasDetectedLocales(): bool
    {
        return !empty($this->detectedLocales);
    }

    /**
     * @return \Kerox\Messenger\Model\Callback\Message
     */
    public static function create(array $callbackData)
    {
        $text = $callbackData['text'] ?? null;
        $attachments = $callbackData['attachments'] ?? [];
        $quickReply = $callbackData['quick_reply']['payload'] ?? null;

        // << FB reply to >>
        // [
        //      "mid" => "{mid}",
        //      "text" => "supersport",
        //      "reply_to" => [
        //          "mid" => "{mid}",
        //      ],
        // ]
        // ------------------------------------------------
        // << Instagram reply to >>
        // [
        //     "mid" => "{mid}"
        //     "text" => "reply event"
        //     "reply_to" => [
        //         "story" => [
        //             "url" => "{url}"
        //             "id" => "18171245791202044"
        //         ]
        //     ]
        // ]

        $replyTo = $callbackData['reply_to'] ?? null;
        $entities = $callbackData['nlp']['entities'] ?? [];
        $traits = $callbackData['nlp']['traits'] ?? [];
        $detectedLocales = $callbackData['nlp']['detected_locales'] ?? [];

        return new self(
            $callbackData['mid'],
            $text,
            $quickReply,
            $attachments,
            $replyTo,
            $entities,
            $traits,
            $detectedLocales
        );
    }
}
