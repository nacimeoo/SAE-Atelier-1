<?php
declare(strict_types=1);

namespace nrv\adapters\middlewares;

use nrv\domain\entities\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;

class RoleMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $rq, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($rq->getAttribute('role') !== Role::GESTIONNAIRE->value) {
            throw new HttpForbiddenException($rq, "accès réservé aux gestionnaires");
        }

        return $handler->handle($rq);
    }
}
