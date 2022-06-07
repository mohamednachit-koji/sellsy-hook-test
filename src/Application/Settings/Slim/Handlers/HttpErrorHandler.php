<?php

declare(strict_types=1);

namespace App\Application\Settings\Slim\Handlers;

use App\Application\Http\ActionError;
use App\Application\Http\ActionErrorType;
use App\Application\Http\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

final class HttpErrorHandler extends SlimErrorHandler
{
    public function hasException(): bool
    {
        return isset($this->exception);
    }

    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = 500;
        $type = ActionErrorType::SERVER_ERROR;
        $description = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();

            if ($exception instanceof HttpNotFoundException) {
                $type = ActionErrorType::RESOURCE_NOT_FOUND;
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $type = ActionErrorType::NOT_ALLOWED;
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $type = ActionErrorType::UNAUTHENTICATED;
            } elseif ($exception instanceof HttpForbiddenException) {
                $type = ActionErrorType::INSUFFICIENT_PRIVILEGES;
            } elseif ($exception instanceof HttpBadRequestException) {
                $type = ActionErrorType::BAD_REQUEST;
            } elseif ($exception instanceof HttpNotImplementedException) {
                $type = ActionErrorType::NOT_IMPLEMENTED;
            }
        }

        if (
            !($exception instanceof HttpException)
            && $exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $description = $exception->getMessage();
        }

        $error = new ActionError($type, $description);
        $payload = new ActionPayload($statusCode, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT + JSON_THROW_ON_ERROR);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
