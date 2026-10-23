<?php
declare(strict_types=1);

namespace nrv\application\dto;

class JaugeDTO
{
    public function __construct(
        public string $id,
        public string $nom,
        public string $date,
        public string $lieu,
        public int $placesVendues,
        public int $capacite,
        public float $recette
    ) {}

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'nom'            => $this->nom,
            'date'           => $this->date,
            'lieu'           => $this->lieu,
            'places_vendues' => $this->placesVendues,
            'capacite'       => $this->capacite,
            'taux_remplissage' => $this->capacite > 0
                ? round($this->placesVendues / $this->capacite * 100, 1)
                : 0,
            'recette'        => $this->recette,
        ];
    }
}
