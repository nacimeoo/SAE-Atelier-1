<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\ports\api\ServiceBilletInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetJaugesAction
{
    public function __construct(
        private ServiceBilletInterface $serviceBillet
    ) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $jauges = $this->serviceBillet->listerJaugesSoirees();

        $data = [
            'type'    => 'collection',
            'count'   => count($jauges),
            'soirees' => array_map(fn($j) => $j->toArray(), $jauges),
            '_links'  => ['self' => ['href' => '/gestion/jauges']],
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(200);
    }
}
