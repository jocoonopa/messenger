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
        $object = Arr::get($payload, 'object');

        /**
         * 若 webhook JSON 的最外層是 "object": "instagram" → 是 IG 留言，沒有 item
         * 若 "object": "page" 且 field 是 "feed" → 是 FB 留言，有 item
         *
         * @jocoonopa 2025-04-07
         */
        $item = $object === 'instagram' ? Arr::get($payload, 'field') : Arr::get($payload, 'value.item');

        switch ($item) {
            case 'reaction':
                return CommentReactionEvent::create($payload);
                break;

            case 'comments':
            case 'comment':
                return CommentEvent::create($payload);
                break;

            default:
                break;
        }

        foreach ($payload as $key => $value) {
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
