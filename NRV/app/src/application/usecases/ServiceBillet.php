<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\BilletDTO;
use nrv\application\ports\api\ServiceBilletInterface;
use nrv\application\ports\spi\BilletRepositoryInterface;

class ServiceBillet implements ServiceBilletInterface
{
    public function __construct(private BilletRepositoryInterface $billetRepository) {}

    public function listerBilletsUtilisateur(string $utilisateurId): array
    {
        $billets = $this->billetRepository->findPayesByUtilisateurId($utilisateurId);
        $dto = [];

        foreach ($billets as $billet) {
            $soiree = $billet->getSoiree();
            $dto[] = new BilletDTO(
                $billet->getId(),
                $billet->getReference(),
                $billet->getAcheteur(),
                $soiree->getNom(),
                $soiree->getDate()->format('Y-m-d'),
                $soiree->getHeureDebut()->format('H:i'),
                $soiree->getLieu()->getNom(),
                $billet->getTypeTarif()->value,
                $billet->getPrix()
            );
        }
        return $dto;
    }
}
