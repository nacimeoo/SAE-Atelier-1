<?php
declare(strict_types=1);
namespace nrv\application\ports\spi;
use nrv\domain\entities\Commande;

interface CommandeRepositoryInterface {
    public function creerCommande(string $commandeId, string $utilisateurId, float $montant, array $lignesPanier): void;
    public function viderPanier(string $panierId): void;
    public function findById(string $commandeId): ?Commande;
    public function compterBillets(string $commandeId): array;
    public function enregistrerPaiement(Commande $commande, array $billetsParSoiree): void;
}