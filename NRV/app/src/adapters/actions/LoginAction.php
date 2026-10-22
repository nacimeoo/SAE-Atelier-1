<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\exceptions\IdentifiantsInvalidesException;
use nrv\application\ports\api\ServiceUtilisateurInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class LoginAction
{
    public function __construct(private ServiceUtilisateurInterface $serviceUtilisateur) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $body = $rq->getParsedBody() ?? [];
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($email === '' || $password === '') {
            throw new HttpBadRequestException($rq, "email et mot de passe obligatoires");
        }

        try {
            $auth = $this->serviceUtilisateur->connecter($email, $password);
        } catch (IdentifiantsInvalidesException $e) {
            throw new HttpUnauthorizedException($rq, $e->getMessage());
        }

        $rs->getBody()->write(json_encode([
            'type'  => 'resource',
            'token' => $auth->token,
            'user'  => ['nom' => $auth->nom, 'prenom' => $auth->prenom, 'role' => $auth->role],
        ], JSON_UNESCAPED_UNICODE));

        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(200);
    }
}
