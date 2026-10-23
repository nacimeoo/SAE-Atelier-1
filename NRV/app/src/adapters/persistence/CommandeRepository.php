<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use nrv\application\ports\spi\CommandeRepositoryInterface;
use nrv\domain\entities\Commande;
use nrv\domain\entities\StatutCommande;
use nrv\domain\exceptions\CommandeDejaPayeeException;
use nrv\domain\exceptions\PlacesInsuffisantesException;
use PDO;
use Ramsey\Uuid\Uuid;

class CommandeRepository implements CommandeRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function creerCommande(string $commandeId, string $utilisateurId, float $montant, array $lignesPanier): void
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO commande (id, utilisateur_id, date_commande, montant_total, statut) VALUES (:id, :uid, NOW(), :montant, 'EN_ATTENTE')");
            $stmt->execute(['id' => $commandeId, 'uid' => $utilisateurId, 'montant' => $montant]);

            $stmtBillet = $this->pdo->prepare("INSERT INTO billet (id, reference, token, type_tarif, prix, commande_id, soiree_id) VALUES (:id, :ref, :token, :tarif, :prix, :cmd, :soiree)");
            
            foreach ($lignesPanier as $ligne) {
                for ($i = 0; $i < $ligne->getQuantite(); $i++) {
                    $stmtBillet->execute([
                        'id' => Uuid::uuid4()->toString(),
                        'ref' => 'NRV-' . strtoupper(bin2hex(random_bytes(4))),
                        'token' => bin2hex(random_bytes(16)),
                        'tarif' => $ligne->getTypeTarif()->value,
                        'prix' => $ligne->getPrixUnitaire(),
                        'cmd' => $commandeId,
                        'soiree' => $ligne->getSoiree()?->getId()
                    ]);
                }
            }
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function viderPanier(string $panierId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM ligne_panier WHERE panier_id = :id");
        $stmt->execute(['id' => $panierId]);
        
        $stmt2 = $this->pdo->prepare("DELETE FROM panier WHERE id = :id");
        $stmt2->execute(['id' => $panierId]);
    }

    public function findById(string $commandeId): ?Commande
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, utilisateur_id, date_commande, montant_total, statut FROM commande WHERE id = :id"
        );
        $stmt->execute(['id' => $commandeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new Commande(
            $row['id'],
            $row['utilisateur_id'],
            new \DateTimeImmutable($row['date_commande']),
            (float) $row['montant_total'],
            StatutCommande::from($row['statut'])
        );
    }

    public function compterBillets(string $commandeId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT soiree_id, COUNT(*) AS nb FROM billet
             WHERE commande_id = :id AND soiree_id IS NOT NULL
             GROUP BY soiree_id"
        );
        $stmt->execute(['id' => $commandeId]);

        $resultat = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $resultat[$row['soiree_id']] = (int) $row['nb'];
        }
        return $resultat;
    }

    public function enregistrerPaiement(Commande $commande, array $billetsParSoiree): void
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE soiree SET nb_places_vendues = soiree.nb_places_vendues + :nb1
                 FROM lieu
                 WHERE soiree.id = :id AND lieu.id = soiree.lieu_id
                   AND soiree.nb_places_vendues + :nb2 <= lieu.nb_places"
            );
            foreach ($billetsParSoiree as $soireeId => $nb) {
                $stmt->execute(['nb1' => $nb, 'nb2' => $nb, 'id' => $soireeId]);
                if ($stmt->rowCount() === 0) {
                    throw new PlacesInsuffisantesException("nombre depasse les palce restant.");
                }
            }
            $stmt = $this->pdo->prepare(
                "UPDATE commande SET statut = :statut WHERE id = :id AND statut = 'EN_ATTENTE'"
            );
            $stmt->execute(['statut' => $commande->getStatut()->value, 'id' => $commande->getId()]);
            if ($stmt->rowCount() === 0) {
                throw new CommandeDejaPayeeException("La commande {$commande->getId()} est deja payer.");
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

}