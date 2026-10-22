<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\ports\api\ServicePanierInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
class GetPanierAction
{
    public function __construct(
        private ServicePanierInterface $servicePanier
    ) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $utilisateurId = $rq->getAttribute('utilisateurId');
        $panier = $this->servicePanier->consulterPanier($utilisateurId);

        $links = [
            'self'    => ['href' => '/panier'],
            'ajouter' => ['href' => '/panier/lignes', 'method' => 'POST'],
        ];
        if ($panier->nbBillets > 0) {
            $links['valider'] = ['href' => '/commandes', 'method' => 'POST'];
        }

        $data = [
            'type'   => 'resource',
            'panier' => [
                'id'     => $panier->id,
                'lignes' => array_map(fn($ligne) => [
                    'id'            => $ligne->id,
                    'type'          => $ligne->passId !== null ? 'pass' : 'soiree',
                    'libelle'       => $ligne->libelle,
                    'date'          => $ligne->date,
                    'type_tarif'    => $ligne->typeTarif,
                    'quantite'      => $ligne->quantite,
                    'prix_unitaire' => $ligne->prixUnitaire,
                    'sous_total'    => $ligne->sousTotal,
                    'remise'        => $ligne->remise,
                    '_links'        => $ligne->soireeId !== ''
                        ? ['soiree' => ['href' => "/soirees/{$ligne->soireeId}"]]
                        : [],
                ], $panier->lignes),
                'nb_billets'    => $panier->nbBillets,
                'total_remise'  => $panier->totalRemise,
                'montant_total' => $panier->montantTotal,
            ],
            '_links' => $links,
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withHeader('Access-Control-Allow-Origin', '*')
                  ->withStatus(200);
    }
}