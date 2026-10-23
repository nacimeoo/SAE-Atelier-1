<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\exceptions\EmailDejaUtiliseException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\api\ServiceUtilisateurInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;

class RegisterAction
{
    public function __construct(private ServiceUtilisateurInterface $serviceUtilisateur) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $body = $rq->getParsedBody() ?? [];
        $nom = trim((string) ($body['nom'] ?? ''));
        $prenom = trim((string) ($body['prenom'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        try {
            $this->serviceUtilisateur->inscrire($nom, $prenom, $email, $password);
        } catch (ValidationException $e) {
            $rs->getBody()->write(json_encode(['type' => 'error', 'message' => implode(' ', $e->getErrors()), 'errors' => $e->getErrors()]));
            return $rs->withHeader('Content-Type', 'application/json')
                      ->withHeader('Access-Control-Allow-Origin', '*')
                      ->withStatus(400);
        } catch (EmailDejaUtiliseException $e) {
            throw new HttpException($rq, $e->getMessage(), 409);
        }

        $rs->getBody()->write(json_encode([
            'type' => 'resource', 
            'message' => 'Compte créé.', 
            '_links' => ['login' => ['href' => '/login', 'method' => 'POST']]]));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(201);
    }
}
