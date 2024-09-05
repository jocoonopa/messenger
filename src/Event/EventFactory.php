<?php

declare(strict_types=1);

namespace Kerox\Messenger\Event;

use Illuminate\Support\Arr;

class EventFactory
{
    public const EVENTS = [
        'message' => MessageEvent::class,
        'postback' => PostbackEvent::class,
        'optin' => OptinEvent::class,
        'account_linking' => AccountLinkingEvent::class,
        'delivery' => DeliveryEvent::class,
        'read' => ReadEvent::class,
        'payment' => PaymentEvent::class,
        'checkout_update' => CheckoutUpdateEvent::class,
        'pre_checkout' => PreCheckoutEvent::class,
        'take_thread_control' => TakeThreadControlEvent::class,
        'pass_thread_control' => PassThreadControlEvent::class,
        'request_thread_control' => RequestThreadControlEvent::class,
        'policy-enforcement' => PolicyEnforcementEvent::class,
        'app_roles' => AppRolesEvent::class,
        'reaction' => ReactionEvent::class,
        'referral' => ReferralEvent::class,
        'game_play' => GamePlayEvent::class,
    ];

    /**
     * @return \Kerox\Messenger\Event\AbstractEvent
     */
    public static function create(array $payload): ?AbstractEvent
    {
        foreach (array_keys($payload) as $key) {
            // 貼文的會有這種的:
            //
            // 'value' => [
            //      "item" => "comment",
            // ]
            //
            // 'value' => [
            //      "item" => "reaction",
            // ]
            //
            // 所以判斷要另外處理。
            if ($key === 'value') {
                $item = Arr::get($payload, 'value.item');

                switch ($item) {
                    case 'reaction':
                        return CommentReactionEvent::create($payload);
                        break;

                    case 'comment':
                        return CommentEvent::create($payload);
                        break;

                    default:
                        break;
                }
            }

            if (\array_key_exists($key, self::EVENTS)) {
                // CommentEvent::class
                $className = self::EVENTS[$key];
                if (isset($payload['message']['is_echo'])) {
                    $className = MessageEchoEvent::class;
                }

                return $className::create($payload);
            }
        }

        return RawEvent::create($payload);
    }
}
