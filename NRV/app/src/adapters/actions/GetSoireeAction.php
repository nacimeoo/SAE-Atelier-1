<?php
declare(strict_types=1);

namespace nrv\adapters\actions;

use nrv\application\exceptions\SoireeNotFoundException;
use nrv\application\ports\api\ServiceSoireeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Respect\Validation\ValidatorBuilder as v;


class GetSoireeAction
{

    
    public function __construct(
        private ServiceSoireeInterface $serviceSoiree
    ) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'] ?? '';

        if (!v::uuid()->isValid($id)) {
            throw new HttpBadRequestException($rq, "id invalide : $id");
        }

        try {
            $soiree = $this->serviceSoiree->afficherSoiree($id);
        } catch (SoireeNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $data = [
            'type'   => 'resource',
            'soiree' => [
                'id'          => $soiree->id,
                'nom'         => $soiree->nom,
                'thematique'  => $soiree->thematique,
                'date'        => $soiree->date->format('Y-m-d'),
                'heure_debut' => $soiree->heureDebut->format('H:i'),
                'lieu'        => [
                    'nom'     => $soiree->lieuNom,
                    'adresse' => $soiree->lieuAdresse,
                ],
                'tarif'       => $soiree->tarif,
                'places_restantes' => $soiree->placesRestantes,
                'spectacles'  => array_map(fn($sp) => [
                    'id'          => $sp->id,
                    'titre'       => $sp->titre,
                    'artistes'    => $sp->artistes,
                    'description' => $sp->description,
                    'style'       => $sp->style,
                    'url_video'   => $sp->urlVideo,
                    'heure'       => $sp->heure,
                ], $soiree->spectacles),
            ],
            '_links' => [
                'self'       => ['href' => "/soirees/{$soiree->id}"],
                'spectacles' => ['href' => '/spectacles'],
            ],
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
