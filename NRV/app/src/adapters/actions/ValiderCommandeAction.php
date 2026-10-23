<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\ports\api\ServiceCommandeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ValiderCommandeAction
{
    public function __construct(private ServiceCommandeInterface $serviceCommande) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $utilisateurId = $rq->getAttribute('utilisateurId');
        
        try {
            $commandeId = $this->serviceCommande->validerPanier($utilisateurId);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $rs->getBody()->write(json_encode([
            'type' => 'resource',
            'message' => 'Commande créée avec succès',
            'commande_id' => $commandeId,
            '_links' => [
                'self' => ['href' => '/commandes', 'method' => 'POST'],
                'billets' => ['href' => '/billets'],
                'panier' => ['href' => '/panier']
            ]
        ]));
        
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(201);
    }
}