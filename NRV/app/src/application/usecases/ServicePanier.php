<?php
declare(strict_types=1);

namespace nrv\application\usecases;
use nrv\application\ports\api\ServicePanierInterface;
use nrv\application\ports\spi\PanierRepositoryInterface;
use nrv\domain\entities\Panier;
use Ramsey\Uuid\Uuid;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\domain\entities\LignePanier;
use nrv\application\dto\PanierDTO;
use nrv\domain\entities\TypeTarif;
use nrv\application\dto\LignePanierDTO;
use nrv\application\exceptions\SoireeNotFoundException;
use nrv\application\exceptions\InsufficientSeatsException;
use nrv\application\exceptions\PanierNotFoundException;



class ServicePanier implements ServicePanierInterface
{
    public function __construct(
        private PanierRepositoryInterface $panierRepository, private SoireeRepositoryInterface $soireeRepository
    ) {}

    public function ajouterBilletAuPanier(string $panierId, string $soireeId, string $typeTarif, int $quantite): PanierDTO
    {

        // TODO remplacer par l'utilisateur authentifié
        $utilisateurId = 'd9a83e5d-db15-4fc2-b338-37feac7b6d0c';

        $tarif = TypeTarif::tryFrom($typeTarif);
        if ($tarif === null) {
            throw new \InvalidArgumentException("Type de tarif invalide : $typeTarif");
        }

        try {
            $soiree = $this->soireeRepository->findById($soireeId);
        } catch (SoireeNotFoundException $e) {
            throw new SoireeNotFoundException("Soirée avec l'ID $soireeId non trouvée.");
        }

        if ($quantite > $soiree->getPlacesRestantes()) {
            throw new InsufficientSeatsException("Quantité demandée ($quantite) dépasse les places disponibles pour la soirée.");
        }

        try {
            $panier = $this->panierRepository->findById($panierId);
        } catch (PanierNotFoundException $e) {
            $panier = new Panier($panierId, $utilisateurId);
            $this->panierRepository->create($panier);
        }

        $lignePanier = new LignePanier(Uuid::uuid4()->toString(), $quantite, $tarif, $soiree);

        $panier->ajouterLignePanier($lignePanier);

        $this->panierRepository->ajouterLignePanier($panier);

        return PanierDTO::fromEntity($panier);
    }
}
