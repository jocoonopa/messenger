<?php

declare(strict_types=1);

namespace Kerox\Messenger\Model;

use Kerox\Messenger\Exception\InvalidClassException;
use Kerox\Messenger\Exception\MessengerException;
use Kerox\Messenger\Helper\ValidatorTrait;
use Kerox\Messenger\Model\Message\AbstractAttachment;
use Kerox\Messenger\Model\Message\QuickReply;

class Message implements \JsonSerializable
{
    use ValidatorTrait;

    private const TYPE_TEXT = 'text';
    private const TYPE_ATTACHMENT = 'attachment';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \Kerox\Messenger\Model\Message\AbstractAttachment|string
     */
    protected $message;

    /**
     * @var \Kerox\Messenger\Model\Message\QuickReply[]
     */
    protected $quickReplies = [];

    /**
     * @var string
     */
    protected $metadata;

    /**
     * Message constructor.
     *
     * @param \Kerox\Messenger\Model\Message\AbstractAttachment|string $message
     *
     * @throws \Exception
     */
    public function __construct($message)
    {
        if (\is_string($message)) {
            $this->isValidString($message, 640);
            $this->type = self::TYPE_TEXT;
        } elseif ($message instanceof AbstractAttachment) {
            $this->type = self::TYPE_ATTACHMENT;
        } else {
            throw new MessengerException(sprintf('message must be a string or an instance of %s.', AbstractAttachment::class));
        }

        $this->message = $message;
    }

    /**
     * @param \Kerox\Messenger\Model\Message\AbstractAttachment|string $message
     *
     * @throws \Exception
     *
     * @return \Kerox\Messenger\Model\Message
     */
    public static function create($message): self
    {
        return new self($message);
    }

    /**
     * @param \Kerox\Messenger\Model\Message\QuickReply[] $quickReplies
     *
     * @throws \Exception
     *
     * @return \Kerox\Messenger\Model\Message
     */
    public function setQuickReplies(array $quickReplies): self
    {
        $this->isValidQuickReplies($quickReplies);

        $this->quickReplies = $quickReplies;

        return $this;
    }

    /**
     * @throws \Kerox\Messenger\Exception\MessengerException
     *
     * @return \Kerox\Messenger\Model\Message
     */
    public function addQuickReply(QuickReply $quickReply): self
    {
        $this->quickReplies[] = $quickReply;

        $this->isValidArray($this->quickReplies, 13, 1);

        return $this;
    }

    /**
     * @throws \Kerox\Messenger\Exception\MessengerException
     *
     * @return Message
     */
    public function setMetadata(string $metadata): self
    {
        $this->isValidString($metadata, 1000);

        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @throws \Kerox\Messenger\Exception\MessengerException
     */
    private function isValidQuickReplies(array $quickReplies): void
    {
        $this->isValidArray($quickReplies, 13, 1);

        foreach ($quickReplies as $quickReply) {
            if (!$quickReply instanceof QuickReply) {
                throw new InvalidClassException(sprintf('Array can only contain instance of %s.', QuickReply::class));
            }
        }
    }

    public function toArray(): array
    {
        $array = [
            $this->type => $this->message,
            'quick_replies' => $this->quickReplies,
            'metadata' => $this->metadata,
        ];

        return array_filter($array);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
