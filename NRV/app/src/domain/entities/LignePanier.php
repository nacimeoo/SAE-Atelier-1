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
    ){}

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

}