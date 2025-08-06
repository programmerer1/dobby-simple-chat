<?php

declare(strict_types=1);

namespace App\Service;

class Validator
{
    public function __construct(public readonly Response $response) {}

    public function validatePromt(string $promt): void
    {
        if (strlen($promt) < 3 || strlen($promt) > 3000) {
            $this->response->send(message: 'Error: It must be at least 3 characters and no more than 1000 characters long.');
        }

        return;
    }

    public function validateBody(mixed $body): void
    {
        if (empty($body['promt']) || !is_string($body['promt'])) {
            $this->response->send(message: 'Error: Incorrect request - 1');
        }

        return;
    }

    public function checkJsonRequest(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        if (str_contains($contentType, 'application/json') || str_contains($accept, 'application/json')) {
            return;
        }

        $this->response->send(message: 'Error: Must be a JSON request.');
    }
}
