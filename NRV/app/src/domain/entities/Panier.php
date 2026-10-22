<?php

declare(strict_types=1);

namespace nrv\domain\entities;

use nrv\domain\entities\LignePanier;

class Panier
{
    public function __construct(
        private string $id,
        private string $utilisateurId,
        private array $lignesCommande = [],
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUtilisateurId(): string
    {
        return $this->utilisateurId;
    }

    public function getLignesCommande(): array
    {
        return $this->lignesCommande;
    }

    public function ajouterLignePanier(LignePanier $lignePanier): void
    {
        foreach ($this->lignesCommande as $ligne) {
            if (
                $ligne->getSoiree()?->getId() === $lignePanier->getSoiree()?->getId()
                && $ligne->getTypeTarif() === $lignePanier->getTypeTarif()
            ) {
                $ligne->ajouterQuantite($lignePanier->getQuantite());
                return;
            }
        }

        $this->lignesCommande[] = $lignePanier;
    }

    public function estVide(): bool
    {
        return count($this->lignesCommande) === 0;
    }

    public function getNbBillet():int
    {
        $nb = 0;
        foreach ($this->lignesCommande as $ligne) {
            $nb += $ligne->getQuantite();
        }
        return $nb;
    }

    public function getRemiseTotal(): float
    {
        $total = 0.0;
        foreach ($this->lignesCommande as $ligne) {
            $total += $ligne->getRemise();
        }
        return round($total, 2);
    }

    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->lignesCommande as $ligne) {
            $total += $ligne->getPrixTotal();
        }
        return round($total, 2);
    }
}
