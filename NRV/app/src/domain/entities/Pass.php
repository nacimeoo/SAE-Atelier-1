<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Pass
{
    public function __construct(
        private string $id,
        private string $libelle,
        private float $tarifNormal,
        private float $tarifReduit,
        private array $soirees,
    ){}

    public function getId(): string
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function getTarif(): float
    {
        return $type === TypeTarif::REDUIT ? $this->tarifReduit : $this->tarifNormal;    
    }

    public function getSoirees(): array
    {
        return $this->soirees;
    }

}