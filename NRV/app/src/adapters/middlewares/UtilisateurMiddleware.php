<?php
declare(strict_types=1);

namespace nrv\adapters\middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;

class UtilisateurMiddleware implements MiddlewareInterface
{
    public function __construct(private string $jwtSecret) {}

    public function process(ServerRequestInterface $rq, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $rq->getHeaderLine('Authorization');

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $m)) {
            throw new HttpUnauthorizedException($rq, "token manquant");
        }

        try {
            $payload = JWT::decode($m[1], new Key($this->jwtSecret, 'HS256'));
        } catch (\Throwable $e) {
            throw new HttpUnauthorizedException($rq, "token invalide ou expiré");
        }

        return $handler->handle(
            $rq->withAttribute('utilisateurId', $payload->sub)
               ->withAttribute('role', $payload->role)
        );
    }
}
