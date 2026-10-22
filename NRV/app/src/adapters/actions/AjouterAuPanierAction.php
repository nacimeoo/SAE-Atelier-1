<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\exceptions\SoireeNotFoundException;
use nrv\application\ports\api\ServicePanierInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Respect\Validation\ValidatorBuilder as v;
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
        $soireeId = $body['soiree_id'] ?? null;
        $typeTarif = $body['type_tarif'] ?? null;
        $quantite = isset($body['quantite']) ? (int)$body['quantite'] : null;

        if (!v::uuid()->isValid($soireeId)) {
            throw new HttpBadRequestException($rq, "id de soirée invalide : $soireeId");
        }

        if (!v::in(['NORMAL', 'REDUIT'], true)->isValid($typeTarif)) {
            throw new HttpBadRequestException($rq, "type de tarif invalide : $typeTarif");
        }

        if ($quantite <= 0) {
            throw new HttpBadRequestException($rq, "quantité invalide : $quantite");
        }

        try {
            $panierDTO = $this->servicePanier->ajouterBilletAuPanier($UtilisateurId, $soireeId, $typeTarif, $quantite);
        } catch (SoireeNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        } catch (InsufficientSeatsException $e) {
            $rs->getBody()->write(json_encode(['type' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE));
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
        ];

        $rs->getBody()->write(json_encode($data));
        
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(201);
        
        
    }
}
