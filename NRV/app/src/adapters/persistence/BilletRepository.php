<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\domain\entities\Billet;
use nrv\domain\entities\TypeTarif;

class BilletRepository implements BilletRepositoryInterface
{
    public function __construct(
        private PDO $pdo,
        private SoireeRepositoryInterface $soireeRepository
    ) {}

    public function findPayesByUtilisateurId(string $utilisateurId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT b.id, b.reference, b.token, b.type_tarif, b.prix, b.soiree_id,
                    u.nom, u.prenom
             FROM billet b
             JOIN commande c ON c.id = b.commande_id
             JOIN utilisateur u ON u.id = c.utilisateur_id
             WHERE c.utilisateur_id = :utilisateur_id
               AND c.statut = 'PAYEE'
               AND b.soiree_id IS NOT NULL
             ORDER BY c.date_commande DESC, b.reference"
        );
        $stmt->execute(['utilisateur_id' => $utilisateurId]);

        $billets = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $billets[] = new Billet(
                $row['id'],
                $row['reference'],
                $row['token'],
                TypeTarif::from($row['type_tarif']),
                (float)$row['prix'],
                $this->soireeRepository->findById($row['soiree_id']),
                $row['prenom'] . ' ' . $row['nom']
            );
        }
        return $billets;
    }
}
