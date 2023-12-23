<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Kerox\Messenger\Model\Callback\Referral;

use Illuminate\Support\Arr;

class ReferralEvent extends AbstractEvent
{
    public const NAME = 'referral';

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var \Kerox\Messenger\Model\Callback\Referral
     */
    protected $referral;

    /**
     * ReferralEvent constructor.
     */
    public function __construct(string $senderId, string $recipientId, int $timestamp, Referral $referral)
    {
        parent::__construct($senderId, $recipientId);

        $this->timestamp = $timestamp;
        $this->referral = $referral;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getReferral(): Referral
    {
        return $this->referral;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return \Kerox\Messenger\Event\ReferralEvent
     */
    public static function create(array $payload): ?self
    {
        $senderId = Arr::get($payload, 'sender.id');

        if (blank($senderId)) {
            return null;
        }

        $recipientId = Arr::get($payload, 'recipient.id');

        $timestamp = Arr::get($payload, 'timestamp', time());

        $referral = Referral::create(Arr::get($payload, 'referral'));

        return new self($senderId, $recipientId, $timestamp, $referral);
    }
}
