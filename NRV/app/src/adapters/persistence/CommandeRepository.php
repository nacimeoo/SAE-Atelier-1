<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use nrv\application\ports\spi\CommandeRepositoryInterface;
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
                        'ref' => strtoupper(substr(uniqid('NRV-'), 0, 10)),
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
}