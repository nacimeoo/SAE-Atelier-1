<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use nrv\application\exceptions\SoireeNotFoundException;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Spectacle;
use PDO;

class SoireeRepository implements SoireeRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findById(string $id): Soiree
    {
        $stmt = $this->pdo->prepare(
            'SELECT s.id, s.nom, s.thematique, s.date, s.heure_debut,
                    s.tarif_normal, s.tarif_reduit, s.nb_places_vendues,
                    l.id AS lieu_id, l.nom AS lieu_nom, l.adresse AS lieu_adresse,
                    l.nb_places AS lieu_nb_places
             FROM soiree s
             JOIN lieu l ON l.id = s.lieu_id
             WHERE s.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new SoireeNotFoundException("existe pas : $id");
        }

        $lieu = new Lieu(
            $row['lieu_id'],
            $row['lieu_nom'],
            $row['lieu_adresse'],
            (int) $row['lieu_nb_places']
        );

        $soiree = new Soiree(
            $row['id'],
            $row['nom'],
            $row['thematique'],
            new \DateTimeImmutable($row['date']),
            new \DateTimeImmutable($row['date'] . ' ' . $row['heure_debut']),
            (float) $row['tarif_normal'],
            (float) $row['tarif_reduit'],
            $lieu,
            [],
            (int) $row['nb_places_vendues']
        );

        $stmt = $this->pdo->prepare(
            'SELECT sp.id, sp.titre, sp.artistes, sp.description, sp.style, sp.url_video, sp.heure,
                    (SELECT i.url FROM image i WHERE i.spectacle_id = sp.id LIMIT 1) AS image
             FROM spectacle sp
             WHERE sp.soiree_id = :id
             ORDER BY sp.heure'
        );
        $stmt->execute(['id' => $id]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $sp) {
            $soiree->ajouterSpectacle(new Spectacle(
                $sp['id'],
                $sp['titre'],
                $sp['artistes'],
                $sp['description'],
                $sp['style'],
                $sp['image'],
                $sp['url_video'],
                new \DateTimeImmutable($row['date'] . ' ' . $sp['heure']),
                $row['id'],
                new \DateTimeImmutable($row['date'])
            ));
        }

        return $soiree;
    }
}
