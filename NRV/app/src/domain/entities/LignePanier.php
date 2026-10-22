<?php

declare(strict_types=1);

namespace nrv\domain\entities;

class LignePanier
{
    public function __construct(
        private string $id,
        private int $quantite,
        private TypeTarif $typeTarif,
        private ?Soiree $soiree = null,
        private ?Pass $pass = null,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getTypeTarif(): TypeTarif
    {
        return $this->typeTarif;
    }

    public function getSoiree(): ?Soiree
    {
        return $this->soiree;
    }

    public function getPass(): ?Pass
    {
        return $this->pass;
    }

    public function ajouterQuantite(int $quantite): void
    {
        $this->quantite += $quantite;
    }

    public function estpass(): bool
    {
        return $this->pass !== null;
    }

    public function getPrixUnitaire(): float
    {
        if ($this->pass !== null) {
            return $this->pass->getPrix($this->typeTarif);
        }
        return round($this->soiree->getTarif($this->typeTarif), 2);
    }

    public function getPrixTotal(): float
    {
        return round($this->getPrixUnitaire() * $this->quantite, 2);
    }

    

    public function getRemise(): float
    {
        if ($this->pass === null) {
            return 0.0;
        }
        $prixSansRemise = $this->pass->getTarif($this->typeTarif) * $this->quantite;
        return round($prixSansRemise - $this->getPrixTotal(), 2);
    }
}
