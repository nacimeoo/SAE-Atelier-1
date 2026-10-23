<?php
declare(strict_types=1);
namespace nrv\application\ports\spi;

interface CommandeRepositoryInterface {
    public function creerCommande(string $commandeId, string $utilisateurId, float $montant, array $lignesPanier): void;
    public function viderPanier(string $panierId): void;
}