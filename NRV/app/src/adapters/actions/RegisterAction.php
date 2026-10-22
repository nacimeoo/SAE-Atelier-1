<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\exceptions\EmailDejaUtiliseException;
use nrv\application\ports\api\ServiceUtilisateurInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\ValidatorBuilder as v;
use Slim\Exception\HttpBadRequestException;
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

        if ($nom === '' || $prenom === '') {
            throw new HttpBadRequestException($rq, "nom et prénom obligatoires");
        }
        if (!v::email()->isValid($email)) {
            throw new HttpBadRequestException($rq, "email invalide");
        }
        if (strlen($password) < 8) {
            throw new HttpBadRequestException($rq, "mot de passe trop court (8 caractères minimum)");
        }

        try {
            $this->serviceUtilisateur->inscrire($nom, $prenom, $email, $password);
        } catch (EmailDejaUtiliseException $e) {
            throw new HttpException($rq, $e->getMessage(), 409);
        }

        $rs->getBody()->write(json_encode(['type' => 'resource', 'message' => 'Compte créé.']));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(201);
    }
}
