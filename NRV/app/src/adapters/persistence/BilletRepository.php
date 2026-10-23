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
            "SELECT billet.id, billet.reference, billet.token, billet.type_tarif, billet.prix, billet.soiree_id, utilisateur.nom, utilisateur.prenom FROM billet
             JOIN commande ON commande.id = billet.commande_id
             JOIN utilisateur ON utilisateur.id = commande.utilisateur_id
             WHERE commande.utilisateur_id = :utilisateur_id AND commande.statut = 'PAYEE' AND billet.soiree_id IS NOT NULL
             ORDER BY commande.date_commande DESC, billet.reference"
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

    public function findByToken(string $token): ?Billet
    {
        $stmt = $this->pdo->prepare(
            "SELECT billet.id, billet.reference, billet.token, billet.type_tarif, billet.prix, billet.soiree_id, utilisateur.nom, utilisateur.prenom FROM billet
             JOIN commande ON commande.id = billet.commande_id
             JOIN utilisateur ON utilisateur.id = commande.utilisateur_id
             WHERE billet.token = :token AND commande.statut = 'PAYEE' AND billet.soiree_id IS NOT NULL"
        );
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new Billet(
            $row['id'],
            $row['reference'],
            $row['token'],
            TypeTarif::from($row['type_tarif']),
            (float)$row['prix'],
            $this->soireeRepository->findById($row['soiree_id']),
            $row['prenom'] . ' ' . $row['nom']
        );
    }

    public function statistiquesParSoiree(): array
    {
        $stmt = $this->pdo->query(
            "SELECT soiree.id, soiree.nom, soiree.date, soiree.nb_places_vendues, lieu.nom AS lieu, lieu.nb_places, COALESCE(SUM(billet.prix) FILTER (WHERE commande.statut = 'PAYEE'), 0) AS recette FROM soiree
             JOIN lieu ON lieu.id = soiree.lieu_id
             LEFT JOIN billet ON billet.soiree_id = soiree.id
             LEFT JOIN commande ON commande.id = billet.commande_id
             GROUP BY soiree.id, soiree.nom, soiree.date, soiree.nb_places_vendues, lieu.nom, lieu.nb_places
             ORDER BY soiree.date, soiree.nom"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
