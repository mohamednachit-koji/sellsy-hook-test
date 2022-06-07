<?php

declare(strict_types=1);

namespace App\Application\Http;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    protected Request $request;

    protected Response $response;

    protected array $args;

    public function __construct(
        protected readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws HttpException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (\Exception $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage(), $e);
        }
    }

    /**
     * @throws HttpException
     */
    abstract protected function action(): Response;

    protected function getFormData(): array|object
    {
        return $this->request->getParsedBody();
    }

    /**
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name): mixed
    {
        return $this->args[$name]
            ?? throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
    }

    protected function queryParam(string $name): array|string|null
    {
        $value = $this->request->getQueryParams()[$name] ?? null;
        if (empty($value)) {
            return null;
        }
        return $value;
    }

    /**
     * @throws HttpException
     */
    protected function respondWithData(array|object|null $data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    /**
     * @throws HttpException
     */
    protected function respond(ActionPayload $payload): Response
    {
        try {
            $json = json_encode($payload, JSON_PRETTY_PRINT + JSON_THROW_ON_ERROR + JSON_ERROR_UTF8 + JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $e) {
            throw new HttpInternalServerErrorException(request: $this->request, previous: $e);
        }
        $this->response->getBody()->write($json);

        return $this->response
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withStatus($payload->statusCode);
    }
}
