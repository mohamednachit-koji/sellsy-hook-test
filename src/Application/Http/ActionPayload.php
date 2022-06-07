<?php

declare(strict_types=1);

namespace App\Application\Http;

use JsonSerializable;

final class ActionPayload implements JsonSerializable
{
    public function __construct(
        public readonly int $statusCode = 200,
        public readonly array|object|null $data = null,
        public readonly ?ActionError $error = null,
    ) {
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $payload = [
            'status_code' => $this->statusCode,
        ];

        if ($this->data !== null) {
            $payload['data'] = $this->data;
        } elseif ($this->error !== null) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
