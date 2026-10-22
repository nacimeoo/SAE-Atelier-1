<?php
declare(strict_types=1);

namespace nrv\application\dto;


class LignePanierDTO
{
    public function __construct(
        public string $soireeId,
        public string $typeTarif,
        public int $quantite
    ) {}

    public function toArray(): array
    {
        return [
            'soireeId' => $this->soireeId,
            'typeTarif' => $this->typeTarif,
            'quantite' => $this->quantite
        ];
    }

}
