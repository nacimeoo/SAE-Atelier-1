<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Panier
{
    public function __construct(
        private string $id,
        private string $utilisateurId,
        private array $lignesCommande = [],
    ){}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUtilisateurId(): string
    {
        return $this->utilisateurId;
    }

    public function getLignesCommande(): array
    {
        return $this->lignesCommande;   
    }


}