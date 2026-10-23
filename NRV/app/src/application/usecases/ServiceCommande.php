<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\CommandeDTO;
use nrv\application\exceptions\CommandeNotFoundException;
use nrv\application\ports\api\ServiceCommandeInterface;
use nrv\application\ports\spi\PanierRepositoryInterface;
use nrv\application\ports\spi\CommandeRepositoryInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\validators\PaiementValidator;
use Ramsey\Uuid\Uuid;

class ServiceCommande implements ServiceCommandeInterface
{
    public function __construct(
        private PanierRepositoryInterface $panierRepo,
        private CommandeRepositoryInterface $commandeRepo,
        private SoireeRepositoryInterface $soireeRepo,
        private PaiementValidator $paiementValidator = new PaiementValidator()
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

    public function payerCommande(string $utilisateurId,string $commandeId,?string $numeroCarte,?string $expiration,?string $cvc): CommandeDTO {
        $this->paiementValidator->validate($numeroCarte, $expiration, $cvc);
        $commande = $this->commandeRepo->findById($commandeId);
        if ($commande === null || $commande->getUtilisateurId() !== $utilisateurId) {
            throw new CommandeNotFoundException("Commande introuvable : $commandeId");
        }
        $commande->payer();                      
        $billetsParSoiree = $this->commandeRepo->compterBillets($commandeId);
        foreach ($billetsParSoiree as $soireeId => $nb) {
            $soiree = $this->soireeRepo->findById($soireeId);
            $soiree->vendrePlaces($nb);         
        }
        $this->commandeRepo->enregistrerPaiement($commande, $billetsParSoiree);
        return CommandeDTO::fromEntity($commande, array_sum($billetsParSoiree));
    }
}