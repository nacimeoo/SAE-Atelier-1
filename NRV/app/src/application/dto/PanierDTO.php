<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\application\dto\LignePanierDTO;
use nrv\domain\entities\Panier;
class PanierDTO
{
    public function __construct(
        public string $id,
        public array $lignes
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'lignes' => array_map(fn($ligne) => $ligne->toArray(), $this->lignes)
        ];
    }

    public static function fromEntity(Panier $panier): self
    {
        $lignesDTO = array_map(fn($ligne) => new LignePanierDTO(
            $ligne->getSoiree()?->getId() ?? '',
            $ligne->getTypeTarif()->value,
            $ligne->getQuantite()
        ), $panier->getLignesCommande());

        return new self($panier->getId(), $lignesDTO);
    }

}
