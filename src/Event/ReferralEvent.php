<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Kerox\Messenger\Model\Callback\Referral;

final class ReferralEvent extends AbstractEvent
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
    public static function create(array $payload): self
    {
        $senderId = null;

        if (isset($payload['sender']) && isset($payload['sender']['id'])) {
            $senderId = $payload['sender']['id'];
        } else {
            $senderId = 'sender';
        }

        $recipientId = null;

        if (isset($payload['recipient']) && isset($payload['recipient']['id'])) {
            $recipientId = $payload['recipient']['id'];
        } else {
            $recipientId = 'no_recipient';
        }

        $timestamp = isset($payload['timestamp']) ? $payload['timestamp'] : '1520567363';

        $referral = Referral::create(isset($payload['referral']) ? $payload['referral'] : null);

        return new self($senderId, $recipientId, $timestamp, $referral);
    }
}
