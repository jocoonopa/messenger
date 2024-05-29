<?php

declare(strict_types=1);

namespace Kerox\Messenger\Model\Callback;

use Illuminate\Support\Arr;

class Optin
{
    /**
     * Webhook data example:
     * ```php
     *  "optin" => [
     *       "type" => "notification_messages"
     *       "payload" => "optin"
     *       "notification_messages_token" => "7127008960039333853"
     *       "notification_messages_frequency" => "DAILY"
     *       "token_expiry_timestamp" => 2145916800000
     *       "user_token_status" => "REFRESHED"
     *       "notification_messages_timezone" => "Asia/Taipei"
     *       "title" => "最新消息和促銷資訊"
     *   ]
     * ```
     *
     * notification_messages_status 唯有當用戶停止或恢復行銷訊息時，此欄位才會存在。
     * {STOP NOTIFICATIONS,RESUME NOTIFICATIONS}
     *
     */
    public function __construct(
        protected string|null $payload,
        protected string $title,
        protected string $type,
        protected string $notificationMessagesToken,
        protected string $notificationMessagesFrequency,
        protected int $tokenExpiryTimestamp,
        protected string $userTokenStatus,
        protected string|null $notificationMessagesTimezone,
        protected string|null $notificationMessagesStatus = null,
    )
    {
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNotificationMessagesToken(): string
    {
        return $this->notificationMessagesToken;
    }

    public function getNotificationMessagesFrequency(): string
    {
        return $this->notificationMessagesFrequency;
    }

    public function getTokenExpiryTimestamp(): int
    {
        return $this->tokenExpiryTimestamp;
    }

    public function getUserTokenStatus(): string
    {
        return $this->userTokenStatus;
    }

    public function getNotificationMessagesTimezone(): string|null
    {
        return $this->notificationMessagesTimezone;
    }

    public function getNotificationMessagesStatus(): string|null
    {
        return $this->notificationMessagesStatus;
    }

    /**
     * @return \Kerox\Messenger\Model\Callback\Optin
     */
    public static function create(array $callbackData): self
    {
        return new self(
            payload: Arr::get($callbackData, 'payload'),
            title: Arr::get($callbackData, 'title'),
            type: Arr::get($callbackData, 'type'),
            notificationMessagesToken: Arr::get($callbackData, 'notification_messages_token'),
            notificationMessagesFrequency: Arr::get($callbackData, 'notification_messages_frequency'),
            tokenExpiryTimestamp: Arr::get($callbackData, 'token_expiry_timestamp'),
            userTokenStatus: Arr::get($callbackData, 'user_token_status'),
            notificationMessagesTimezone: Arr::get($callbackData, 'notification_messages_timezone'),
            notificationMessagesStatus: Arr::get($callbackData, 'notification_messages_status'),
        );
    }
}
