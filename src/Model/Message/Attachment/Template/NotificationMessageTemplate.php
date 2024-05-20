<?php

declare(strict_types=1);

namespace Kerox\Messenger\Model\Message\Attachment\Template;

use Kerox\Messenger\Exception\InvalidTypeException;
use Kerox\Messenger\Model\Message\Attachment\AbstractTemplate;

/**
 * @ref https://developers.facebook.com/docs/messenger-platform/marketing-messages
 */
class NotificationMessageTemplate extends AbstractTemplate
{
    // 將選擇接收訊息按鈕文字設為「允許傳送訊息」
    public const CTA_ALLOW = 'ALLOW';

    // 將選擇接收訊息按鈕文字設為「取得訊息」
    public const CTA_GET = 'GET';

    // 將選擇接收訊息按鈕文字設為「取得更新」，這也是未設定 notification_messages_cta_text 時的預設值
    public const CTA_GET_UPDATES = 'GET_UPDATES';

    // 將選擇接收訊息按鈕文字設為「選擇接收訊息」
    public const CTA_OPT_IN = 'OPT_IN';

    // 將選擇接收訊息按鈕文字設為「訂閱以接收訊息」
    public const CTA_SIGN_UP = 'SIGN_UP';

    /**
     * Receipt constructor.
     *
     * @param \Kerox\Messenger\Model\Message\Attachment\Template\Element\ReceiptElement[] $elements
     *
     * @throws \Kerox\Messenger\Exception\MessengerException
     */
    public function __construct(
        protected string $payload,
        protected string|null $title = null,
        protected string|null $imageUrl = null,
        protected string $timezone = 'Asia/Taipei',
        protected string $ctaText = self::CTA_GET_UPDATES,
        protected array $elements = [],
        protected string $imageRatio = GenericTemplate::IMAGE_RATIO_HORIZONTAL,
    ) {
        if (filled($this->elements)) {
            $this->isValidArray($this->elements, 5);
        }

        $this->validateImageRatio($this->imageRatio);

        $this->validateCta($this->ctaText);

        $this->validateTitle($this->title);

        parent::__construct();
    }

    /**
     * @throws \Kerox\Messenger\Exception\MessengerException
     *
     * @param \Kerox\Messenger\Model\Message\Attachment\Template\Element\GenericElement[] $elements
     *
     * @return \Kerox\Messenger\Model\Message\Attachment\Template\NotificationMessageTemplate
     */
    public static function create(
        string $payload,
        string $title,
        string $imageUrl,
        string $timezone,
        string $ctaText,
        array $elements,
        string $imageRatio,
    ): self {
        return new self(
            $payload,
            $title,
            $imageUrl,
            $timezone,
            $ctaText,
            $elements,
            $imageRatio,
        );
    }

    protected function validateCta($ctaText)
    {
        $allowedCtas = [
            self::CTA_ALLOW,
            self::CTA_GET,
            self::CTA_GET_UPDATES,
            self::CTA_OPT_IN,
            self::CTA_SIGN_UP,
        ];

        if (! in_array($ctaText, $allowedCtas)) {
            $message = sprintf(
                'cta text must be either "%s". %s is not valid',
                implode(', ', $allowedCtas),
                $ctaText,
            );

            throw new InvalidTypeException($message);
        }
    }

    protected function validateImageRatio($imageRatio)
    {
        $allowRations = [
            GenericTemplate::IMAGE_RATIO_HORIZONTAL,
            GenericTemplate::IMAGE_RATIO_SQUARE,
        ];

        if (! in_array($imageRatio, $allowRations)) {
            $message = sprintf(
                'image_ratio must be either "%s". %s is not valid',
                implode(', ', $allowRations),
                $imageRatio,
            );

            throw new InvalidTypeException($message);
        }
    }

    protected function validateTitle($title)
    {
        if (mb_strlen((string) $title) > 65) {
            throw new InvalidTypeException('title must be less than 65 characters.');
        }
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        $array += [
            'payload' => [
                'template_type' => AbstractTemplate::TYPE_NOTIFICATION_MESSAGES,
                'notification_messages_timezone' => $this->timezone,
                'elements' => $this->elements,
                'image_aspect_ratio' => $this->imageRatio,
                'notification_messages_cta_text' => $this->ctaText,
                'title' => $this->title,
                'image_url' => $this->imageUrl,
                'payload' => $this->payload,
            ],
        ];

        return $this->arrayFilter($array);
    }
}
