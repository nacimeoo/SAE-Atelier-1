<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Commande;

class CommandeDTO
{
    public function __construct(
        public string $id,
        public string $statut,
        public float $montantTotal,
        public int $nbBillets
    ) {}

    public static function fromEntity(Commande $commande, int $nbBillets): self
    {
        return new self(
            $commande->getId(),
            $commande->getStatut()->value,
            $commande->getMontantTotal(),
            $nbBillets
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'statut'        => $this->statut,
            'montant_total' => $this->montantTotal,
            'nb_billets'    => $this->nbBillets,
        ];
    }
}