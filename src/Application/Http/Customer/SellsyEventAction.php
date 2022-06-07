<?php

namespace App\Application\Http\Customer;

use App\Application\Http\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class SellsyEventAction extends Action
{
    public function __construct(
        LoggerInterface $logger,

    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        if (!isset($data['notif'])) {
            throw new HttpBadRequestException($this->request, 'notif attribute not sent by sellsy webhook');
        }
        return $this->respondWithData($data);
    }
}