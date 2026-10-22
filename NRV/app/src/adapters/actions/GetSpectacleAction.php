<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\ports\api\ServiceSpectacleInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\ValidatorBuilder as v;
use Slim\Exception\HttpBadRequestException;

class GetSpectacleAction
{
    private ServiceSpectacleInterface $service;

    public function __construct(ServiceSpectacleInterface $service)
    {
        $this->service = $service;
    }
    

    public function __invoke(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $date = $params['date'] ?? null;
        $style = $params['style'] ?? null;

        if ($date !== null && !v::date('Y-m-d')->isValid($date)) {
            throw new HttpBadRequestException($request, 'Invalid date format. Expected format: YYYY-MM-DD');
        }

        if ($style !== null && (!is_string($style) || trim($style) === '')) {
            throw new HttpBadRequestException($request, 'Invalid style format. Expected a non-empty string.');
        }

        $dtos = $this->service->listerSpectacles($date, $style);

        $data = [];
        foreach ($dtos as $dto) {
            $data[] = $dto->toArray();
        }

        $response->getBody()->write(json_encode(['spectacles' => $data], JSON_UNESCAPED_UNICODE));
        
        return $response->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withStatus(200);
    }
        

        
}