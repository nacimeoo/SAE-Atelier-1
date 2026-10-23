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
use nrv\application\validators\PanierValidator;



class ServicePanier implements ServicePanierInterface
{
    public function __construct(
        private PanierRepositoryInterface $panierRepository, private SoireeRepositoryInterface $soireeRepository,
        private PanierValidator $panierValidator = new PanierValidator()
    ) {}

    public function ajouterBilletAuPanier(string $utilisateurId, ?string $soireeId, ?string $typeTarif, ?int $quantite): PanierDTO
    {
        $this->panierValidator->validate($soireeId, $typeTarif, $quantite);

        $tarif = TypeTarif::tryFrom($typeTarif);

        try {
            $soiree = $this->soireeRepository->findById($soireeId);
        } catch (SoireeNotFoundException $e) {
            throw new SoireeNotFoundException("Soirée avec l'ID $soireeId non trouvée.");
        }

        if ($quantite > $soiree->getPlacesRestantes()) {
            throw new InsufficientSeatsException("Quantité demandée ($quantite) dépasse les places disponibles pour la soirée.");
        }

    
        $panier = $this->panierRepository->findByUtilisateurId($utilisateurId);
        if (!$panier) {
            $panier = new Panier(Uuid::uuid4()->toString(), $utilisateurId);
            $this->panierRepository->create($panier);
        }

        $lignePanier = new LignePanier(Uuid::uuid4()->toString(), $quantite, $tarif, $soiree);

        $panier->ajouterLignePanier($lignePanier);

        $this->panierRepository->ajouterLignePanier($panier);

        return PanierDTO::fromEntity($panier);
    }

    public function consulterPanier(string $utilisateurId): PanierDTO
    {
        $panier = $this->panierRepository->findByUtilisateurId($utilisateurId);

        if ($panier === null) {
            return PanierDTO::vide();
        }

        return PanierDTO::fromEntity($panier);
    }
}
