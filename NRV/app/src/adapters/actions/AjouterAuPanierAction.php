<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\exceptions\SoireeNotFoundException;
use nrv\application\ports\api\ServicePanierInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\exceptions\InsufficientSeatsException;


class AjouterAuPanierAction
{

    public function __construct(
        private ServicePanierInterface $servicePanier
    ) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $body = $rq->getParsedBody();
        $UtilisateurId = $rq->getAttribute('utilisateurId');
        $soireeId = isset($body['soiree_id']) ? (string)$body['soiree_id'] : null;
        $typeTarif = isset($body['type_tarif']) ? (string)$body['type_tarif'] : null;
        $quantite = isset($body['quantite']) ? (int)$body['quantite'] : null;

        try {
            $panierDTO = $this->servicePanier->ajouterBilletAuPanier($UtilisateurId, $soireeId, $typeTarif, $quantite);
        } catch (ValidationException $e) {
            $rs->getBody()->write(json_encode(['type' => 'error', 'message' => implode(' ', $e->getErrors()), 'errors' => $e->getErrors()]));
            return $rs->withHeader('Content-Type', 'application/json')
                      ->withHeader('Access-Control-Allow-Origin', '*')
                      ->withStatus(400);
        } catch (SoireeNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        } catch (InsufficientSeatsException $e) {
            $rs->getBody()->write(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
            return $rs->withHeader('Content-Type', 'application/json')
                      ->withHeader('Access-Control-Allow-Origin', '*')
                      ->withStatus(422);
        }

        $data = [
            'type'   => 'resource',
            'panier' => [
                'id'          => $panierDTO->id,
                'lignes'      => array_map(fn($ligne) => [
                    'soiree_id'   => $ligne->soireeId,
                    'type_tarif'  => $ligne->typeTarif,
                    'quantite'    => $ligne->quantite,
                ], $panierDTO->lignes),
            ],
            '_links' => [
                'self'    => ['href' => '/panier'],
                'valider' => ['href' => '/commandes', 'method' => 'POST'],
                'soiree'  => ['href' => "/soirees/$soireeId"],
            ],
        ];

        $rs->getBody()->write(json_encode($data));
        
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(201);
        
        
    }
}
