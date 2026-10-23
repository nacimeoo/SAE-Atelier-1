<?php

declare(strict_types=1);

namespace nrv\application\dto;

use nrv\application\dto\LignePanierDTO;
use nrv\domain\entities\Panier;

class PanierDTO
{
    public function __construct(
        public ?string $id,
        public array $lignes,
        public int $nbBillets = 0,
        public float $totalRemise = 0.0,
        public float $montantTotal = 0.0
    ) {}

       public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'lignes'       => array_map(fn($ligne) => $ligne->toArray(), $this->lignes),
            'nbBillets'    => $this->nbBillets,
            'totalRemise'  => $this->totalRemise,
            'montantTotal' => $this->montantTotal,
        ];
    }

    public static function fromEntity(Panier $panier): self
    {
        $lignesDTO = array_map(fn($ligne) => new LignePanierDTO(
                $ligne->getSoiree()?->getId() ?? '',
                $ligne->getTypeTarif()->value,
                $ligne->getQuantite(),
                $ligne->getId(),
                $ligne->getPass()?->getId(),
                $ligne->estPass() ? $ligne->getPass()->getLibelle() : $ligne->getSoiree()->getNom(),
                $ligne->getSoiree()?->getDate()->format('Y-m-d'),
                $ligne->getPrixUnitaire(),
                $ligne->getPrixTotal(),
                $ligne->getRemise()
        ), $panier->getLignesCommande());

        return new self($panier->getId(), $lignesDTO,$panier->getNbBillet(),$panier->getRemiseTotal(),$panier->getTotal());
    }

    public static function vide(): self
    {
        return new self(null, []);
    }
}
