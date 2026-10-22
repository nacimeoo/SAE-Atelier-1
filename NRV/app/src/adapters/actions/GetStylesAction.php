<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\ports\api\ServiceSpectacleInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetStylesAction
{
    private ServiceSpectacleInterface $service;

    public function __construct(ServiceSpectacleInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $styles = $this->service->listerStyles();
        $response->getBody()->write(json_encode(['styles' => $styles]));
        return $response->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(200);
    }
}