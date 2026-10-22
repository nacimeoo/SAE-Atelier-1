<?php
declare(strict_types=1);

namespace nrv\domain\entities;
use nrv\domain\entities\LignePanier;

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

    public function ajouterLignePanier(LignePanier $lignePanier): void
    {
        foreach ($this->lignesCommande as $ligne) {
            if ($ligne->getSoiree()?->getId() === $lignePanier->getSoiree()?->getId()
                && $ligne->getTypeTarif() === $lignePanier->getTypeTarif()) {
                $ligne->ajouterQuantite($lignePanier->getQuantite());
                return;
            }
        }

        $this->lignesCommande[] = $lignePanier;
    }


}