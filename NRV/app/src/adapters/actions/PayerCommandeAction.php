<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\exceptions\CommandeNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\api\ServiceCommandeInterface;
use nrv\domain\exceptions\CommandeDejaPayeeException;
use nrv\domain\exceptions\PlacesInsuffisantesException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\ValidatorBuilder as v;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class PayerCommandeAction
{
    public function __construct(private ServiceCommandeInterface $serviceCommande) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $commandeId = $args['id'] ?? '';
        if (!v::uuid()->isValid($commandeId)) {
            throw new HttpBadRequestException($rq, "id mauvais: $commandeId");
        }

        $utilisateurId = $rq->getAttribute('utilisateurId');
        $body = $rq->getParsedBody() ?? [];

        try {
            $commande = $this->serviceCommande->payerCommande(
                $utilisateurId,
                $commandeId,
                isset($body['numero_carte']) ? (string) $body['numero_carte'] : null,
                isset($body['expiration']) ? (string) $body['expiration'] : null,
                isset($body['cvc']) ? (string) $body['cvc'] : null
            );
        } catch (ValidationException $e) {
            return $this->erreur($rs, 400, implode(' ', $e->getErrors()), $e->getErrors());
        } catch (CommandeNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        } catch (CommandeDejaPayeeException | PlacesInsuffisantesException $e) {
            return $this->erreur($rs, 409, $e->getMessage());
        }

        $data = [
            'type'     => 'resource',
            'message'  => 'Paiement accepté',
            'commande' => $commande->toArray(),
            '_links'   => [
                'self'    => ['href' => "/commandes/{$commande->id}"],
                'billets' => ['href' => '/billets'],
            ],
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(200);
    }

    private function erreur(Response $rs, int $statut, string $message, array $errors = []): Response
    {
        $rs->getBody()->write(json_encode(
            ['type' => 'error', 'message' => $message, 'errors' => $errors],
            JSON_UNESCAPED_UNICODE
        ));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus($statut);
    }
}