<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\ports\api\ServiceBilletInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBilletParTokenAction
{
    public function __construct(
        private ServiceBilletInterface $serviceBillet
    ) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $billet = $this->serviceBillet->getBilletParToken($args['token']);
            $data = ['type' => 'resource', 'billet' => $billet->toArray()];
            $status = 200;
        } catch (\Exception $e) {
            $data = ['type' => 'error', 'error' => $e->getMessage()];
            $status = 404;
        }

        $rs->getBody()->write(json_encode($data));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus($status);
    }
}
