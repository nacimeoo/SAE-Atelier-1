<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\ports\api\ServiceCommandeInterface;
use nrv\application\ports\spi\PanierRepositoryInterface;
use nrv\application\ports\spi\CommandeRepositoryInterface;
use Ramsey\Uuid\Uuid;

class ServiceCommande implements ServiceCommandeInterface
{
    public function __construct(
        private PanierRepositoryInterface $panierRepo,
        private CommandeRepositoryInterface $commandeRepo
    ) {}

    public function validerPanier(string $utilisateurId): string
    {
        $panier = $this->panierRepo->findByUtilisateurId($utilisateurId);
        if (!$panier || $panier->estVide()) {
            throw new \Exception("Le panier est vide ou introuvable.");
        }

        $commandeId = Uuid::uuid4()->toString();
        $montantTotal = $panier->getTotal();

        $this->commandeRepo->creerCommande($commandeId, $utilisateurId, $montantTotal, $panier->getLignesCommande());

        $this->commandeRepo->viderPanier($panier->getId());

        return $commandeId;
    }
}