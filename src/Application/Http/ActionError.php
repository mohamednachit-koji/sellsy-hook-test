<?php

declare(strict_types=1);

namespace App\Application\Http;

use JsonSerializable;

final class ActionError implements JsonSerializable
{
    public function __construct(
        private readonly ActionErrorType $type,
        private readonly ?string $description
    ) {
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type->value,
            'description' => $this->description,
        ];
    }
}
