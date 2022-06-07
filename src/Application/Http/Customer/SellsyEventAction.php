<?php

namespace App\Application\Http\Customer;

use App\Application\Http\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

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
        $this->logger->critical(json_encode($data));
        if (isset($data['notif'])) {
            $this->logger->critical("notif found " . gettype($data['notif']));
        } elseif (isset($data['notif']['relatedid'])) {
            $this->logger->critical("related id was found");
        } else {
            $this->logger->critical("nothing was found");
        }
        return $this->respondWithData([]);
    }
}