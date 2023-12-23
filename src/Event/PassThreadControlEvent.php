<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Illuminate\Support\Arr;
use Kerox\Messenger\Model\Callback\PassThreadControl;

class PassThreadControlEvent extends AbstractEvent
{
    public const NAME = 'pass_thread_control';

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var \Kerox\Messenger\Model\Callback\PassThreadControl
     */
    protected $passThreadControl;

    /**
     * PassThreadControlEvent constructor.
     */
    public function __construct(
        string $senderId,
        string $recipientId,
        int $timestamp,
        PassThreadControl $passThreadControl
    ) {
        parent::__construct($senderId, $recipientId);

        $this->timestamp = $timestamp;
        $this->passThreadControl = $passThreadControl;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getPassThreadControl(): PassThreadControl
    {
        return $this->passThreadControl;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return \Kerox\Messenger\Event\PassThreadControlEvent
     */
    public static function create(array $payload): ?self
    {
        $senderId = Arr::get($payload, 'sender.id');
        $recipientId = Arr::get($payload, 'recipient.id');
        $timestamp = Arr::get($payload, 'timestamp');
        $passThreadControl = PassThreadControl::create(Arr::get($payload, 'pass_thread_control'));

        if (blank($senderId)) {
            return null;
        }

        return new self($senderId, $recipientId, $timestamp, $passThreadControl);
    }
}
