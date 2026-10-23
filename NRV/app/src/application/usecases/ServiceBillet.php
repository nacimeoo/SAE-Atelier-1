<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\BilletDTO;
use nrv\application\ports\api\ServiceBilletInterface;
use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\application\dto\JaugeDTO;

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
                $billet->getToken(),
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

    public function getBilletParToken(string $token): BilletDTO
    {
        $billet = $this->billetRepository->findByToken($token);
        if ($billet === null) {
            throw new \Exception('Billet introuvable');
        }
        $soiree = $billet->getSoiree();
        return new BilletDTO(
            $billet->getId(),
            $billet->getReference(),
            $billet->getToken(),
            $billet->getAcheteur(),
            $soiree->getNom(),
            $soiree->getDate()->format('Y-m-d'),
            $soiree->getHeureDebut()->format('H:i'),
            $soiree->getLieu()->getNom(),
            $billet->getTypeTarif()->value,
            $billet->getPrix()
        );
    }

    public function listerJaugesSoirees(): array
    {
        $soirees = $this->billetRepository->statistiquesParSoiree();
        $dto = [];

        foreach ($soirees as $soiree) {
            $dto[] = new JaugeDTO(
                $soiree['id'],
                $soiree['nom'],
                $soiree['date'],
                $soiree['lieu'],
                (int)$soiree['nb_places_vendues'],
                (int)$soiree['nb_places'],
                (float)$soiree['recette']
            );
        }
        return $dto;
    }
}
