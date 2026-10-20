<?php
declare(strict_types=1);

namespace nrv\domain\entities;

use nrv\domain\entities\StatutCommande;

class Commande
{
    public function __construct(
        private string $id,
        private string $utilisateurId,
        private \DateTimeImmutable $dateCommande,
        private float $montantTotal,
        private StatutCommande $statut = StatutCommande::EN_ATTENTE,
        private array $billets = [],
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUtilisateurId(): string
    {
        return $this->utilisateurId;
    }

    public function getDateCommande(): \DateTimeImmutable
    {
        return $this->dateCommande;
    }

    public function getMontantTotal(): float
    {
        return $this->montantTotal;
    }

    public function getStatut(): StatutCommande
    {
        return $this->statut;
    }

    public function getBillets(): array
    {
        return $this->billets;
    }
}
    