<?php
declare(strict_types=1);

namespace nrv\application\dto;


class LignePanierDTO
{
    public function __construct(
        public string $soireeId,
        public string $typeTarif,
        public int $quantite,
        public ?string $id = null,
        public ?string $passId = null,
        public string $libelle = '',
        public ?string $date = null,
        public float $prixUnitaire = 0.0,
        public float $sousTotal = 0.0,
        public float $remise = 0.0
    ) {}

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'soireeId'     => $this->soireeId,
            'passId'       => $this->passId,
            'libelle'      => $this->libelle,
            'date'         => $this->date,
            'typeTarif'    => $this->typeTarif,
            'quantite'     => $this->quantite,
            'prixUnitaire' => $this->prixUnitaire,
            'sousTotal'    => $this->sousTotal,
            'remise'       => $this->remise,
        ];
    }

}
