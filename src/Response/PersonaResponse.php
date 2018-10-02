<?php

namespace Kerox\Messenger\Response;

use Kerox\Messenger\Model\Persona;

class PersonaResponse extends AbstractResponse
{
    /**
     * @var Persona
     */
    protected $persona;

    /**
     * @var bool
     */
    protected $success;

    /**
     * @param array $response
     */
    protected function parseResponse(array $response): void
    {
        $this->persona = new Persona($response);
        $this->success = $response['success'] ?? null;
    }

    public function getPersona(): Persona
    {
        return $this->persona;
    }
}
