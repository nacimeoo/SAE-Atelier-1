<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\ports\api\ServiceBilletInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBilletsAction
{
    public function __construct(
        private ServiceBilletInterface $serviceBillet
    ) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $utilisateurId = $rq->getAttribute('utilisateurId');
        $billets = $this->serviceBillet->listerBilletsUtilisateur($utilisateurId);

        $data = [
            'type'    => 'collection',
            'count'   => count($billets),
            'billets' => array_map(fn($b) => $b->toArray(), $billets),
            '_links'  => ['self' => ['href' => '/billets']],
        ];

        $rs->getBody()->write(json_encode($data));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(200);
    }
}
