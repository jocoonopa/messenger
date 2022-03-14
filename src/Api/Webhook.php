<?php

declare(strict_types=1);

namespace Kerox\Messenger\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Kerox\Messenger\Model\Callback\Entry;
use Kerox\Messenger\Request\WebhookRequest;
use Kerox\Messenger\Response\WebhookResponse;
use Psr\Http\Message\ServerRequestInterface;

class Webhook extends AbstractApi
{
    /**
     * @var string
     */
    protected $appSecret;

    /**
     * @var string
     */
    protected $verifyToken;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $decodedBody;

    /**
     * @var \Kerox\Messenger\Model\Callback\Entry[]
     */
    protected $hydratedEntries;

    /**
     * Webhook constructor.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(
        string $appSecret,
        string $verifyToken,
        string $pageToken,
        ClientInterface $client,
        ?ServerRequestInterface $request = null
    ) {
        parent::__construct($pageToken, $client);

        $this->appSecret = $appSecret;
        $this->verifyToken = $verifyToken;
        $this->request = $request ?: ServerRequest::fromGlobals();
    }

    public function isValidToken(): bool
    {
        if ($this->request->getMethod() !== 'GET') {
            return false;
        }

        $params = $this->request->getQueryParams();
        if (!isset($params['hub_verify_token'])) {
            return false;
        }

        return $params['hub_mode'] === 'subscribe' && $params['hub_verify_token'] === $this->verifyToken;
    }

    public function challenge(): ?string
    {
        $params = $this->request->getQueryParams();

        return $params['hub_challenge'] ?? null;
    }

    public function subscribe(): WebhookResponse
    {
        $request = new WebhookRequest($this->pageToken);
        $response = $this->client->post('me/subscribed_apps', $request->build());

        return new WebhookResponse($response);
    }

    /**
     * @throws \Exception
     */
    public function isValidCallback(): bool
    {
        if (!$this->isValidHubSignature()) {
            return false;
        }

        $decodedBody = $this->getDecodedBody();

        $object = $decodedBody['object'] ?? null;
        $entry = $decodedBody['entry'] ?? null;

        return in_array($object, ['page', 'instagram']) && $entry !== null;
    }

    public function getBody(): string
    {
        if ($this->body === null) {
            $this->body = (string) $this->request->getBody();
        }

        return $this->body;
    }

    /**
     * @throws \Exception
     */
    public function getDecodedBody(): array
    {
        if ($this->decodedBody === null) {
            $decodedBody = json_decode($this->getBody(), true);
            if ($decodedBody === null || json_last_error() !== \JSON_ERROR_NONE) {
                $decodedBody = [];
            }

            $this->decodedBody = $decodedBody;
        }

        return $this->decodedBody;
    }

    /**
     * @throws \Exception
     *
     * @return \Kerox\Messenger\Model\Callback\Entry[]
     */
    public function getCallbackEntries(): array
    {
        return $this->getHydratedEntries();
    }

    /**
     * @throws \Exception
     */
    public function getCallbackEvents(): array
    {
        $events = [];
        foreach ($this->getHydratedEntries() as $hydratedEntry) {
            /** @var \Kerox\Messenger\Model\Callback\Entry $hydratedEntry */
            $events = array_merge($events, $hydratedEntry->getEvents());
        }

        return $events;
    }

    /**
     * @throws \Exception
     *
     * @return \Kerox\Messenger\Model\Callback\Entry[]
     */
    private function getHydratedEntries(): array
    {
        if ($this->hydratedEntries === null) {
            $decodedBody = $this->getDecodedBody();

            $hydrated = [];
            foreach ($decodedBody['entry'] as $entry) {
                $hydrated[] = Entry::create($entry);
            }

            $this->hydratedEntries = $hydrated;
        }

        return $this->hydratedEntries;
    }

    private function isValidHubSignature(): bool
    {
        $headers = $this->request->getHeader('X-Hub-Signature');
        $content = $this->getBody();

        if (empty($headers)) {
            return false;
        }

        [$algorithm, $hash] = explode('=', $headers[0]);

        return hash_equals(hash_hmac($algorithm, $content, $this->appSecret), $hash);
    }
}
