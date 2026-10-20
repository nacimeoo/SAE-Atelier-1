<?php
declare(strict_types=1);

namespace nrv\domain\entities;

use nrv\domain\entities\Lieu;
use nrv\domain\entities\TypeTarif;

class Soiree
{
    public function __construct(
        private string $id,
        private string $nom,
        private string $thematique,
        private \DateTimeImmutable $date,
        private \DateTimeImmutable $heureDebut,
        private float $tarifNormal,
        private float $tarifReduit,
        private Lieu $lieu,
        private array $spectacles = [],
        private int $nbPlacesVendues = 0,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getThematique(): string
    {
        return $this->thematique;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getHeureDebut(): \DateTimeImmutable
    {
        return $this->heureDebut;
    }

    public function getTarif(): float
    {
        return $type === TypeTarif::REDUIT ? $this->tarifReduit : $this->tarifNormal;    
    }

    public function getLieu(): Lieu
    {
        return $this->lieu;
    }

    public function getSpectacles(): array
    {
        return $this->spectacles;
    }

    public function getNbPlacesVendues(): int
    {
        return $this->nbPlacesVendues;
    }


}
    