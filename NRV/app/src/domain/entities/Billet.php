<?php
declare(strict_types=1);

namespace nrv\domain\entities;

use nrv\domain\entities\TypeTarif;

class Billet
{
    public function __construct(
        private string $id,
        private string $reference,
        private string $token,
        private TypeTarif $typeTarif,
        private float $prix,
        private Soiree $soiree,
    ) {}


    public function getId(): string
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTypeTarif(): TypeTarif
    {
        return $this->typeTarif;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function getSoiree(): Soiree
    {
        return $this->soiree;
    }

    

}
    