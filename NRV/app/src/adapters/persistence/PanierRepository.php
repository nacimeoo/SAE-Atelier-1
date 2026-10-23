<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\domain\entities\Panier;
use nrv\domain\entities\LignePanier;
use nrv\application\ports\spi\PanierRepositoryInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\TypeTarif;
use nrv\application\exceptions\PanierNotFoundException;



class PanierRepository implements PanierRepositoryInterface
{
    private PDO $pdo;
    private SoireeRepositoryInterface $soireeRepository;

    public function __construct(PDO $pdo, SoireeRepositoryInterface $soireeRepository)
    {
        $this->pdo = $pdo;
        $this->soireeRepository = $soireeRepository;

    }

    public function findById(string $id): Panier
    {
        $stmt = $this->pdo->prepare('SELECT * FROM panier WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $panierData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$panierData) {
            throw new PanierNotFoundException("Panier not found");
        }

        $stmt = $this->pdo->prepare('SELECT * FROM ligne_panier WHERE panier_id = :panier_id');
        $stmt->execute(['panier_id' => $id]);
        $lignesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $lignesCommande = [];
        foreach ($lignesData as $ligne) {
            $soiree = $this->soireeRepository->findById($ligne['soiree_id']);
            $lignesCommande[] = new LignePanier(
                $ligne['id'],
                (int)$ligne['quantite'],    
                TypeTarif::from($ligne['type_tarif']),
                $soiree,
                
            );
        }

        return new Panier(
            $panierData['id'],
            $panierData['utilisateur_id'],
            $lignesCommande
        );
    }

    public function create(Panier $panier): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO panier (id, utilisateur_id) VALUES (:id, :utilisateur_id)');
        $stmt->execute([
            'id' => $panier->getId(),
            'utilisateur_id' => $panier->getUtilisateurId()
        ]);
    }

    public function ajouterLignePanier(Panier $panier): void
    {   
        if (empty($panier->getLignesCommande())) {
            return;
        }

        foreach ($panier->getLignesCommande() as $ligne) {
            $stmt = $this->pdo->prepare('SELECT * FROM ligne_panier WHERE panier_id = :panier_id AND soiree_id = :soiree_id AND type_tarif = :type_tarif');
            $stmt->execute([
                'panier_id' => $panier->getId(),
                'soiree_id' => $ligne->getSoiree()->getId(),
                'type_tarif' => $ligne->getTypeTarif()->value
            ]);
            $LignePanier = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($LignePanier) {
                $stmt = $this->pdo->prepare('UPDATE ligne_panier SET quantite = :quantite WHERE panier_id = :panier_id AND soiree_id = :soiree_id AND type_tarif = :type_tarif');
                $stmt->execute([
                    'quantite' => $ligne->getQuantite(),
                    'panier_id' => $panier->getId(),
                    'soiree_id' => $ligne->getSoiree()->getId(),
                    'type_tarif' => $ligne->getTypeTarif()->value
                ]);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO ligne_panier (panier_id, soiree_id, type_tarif, quantite) VALUES (:panier_id, :soiree_id, :type_tarif, :quantite)');
                $stmt->execute([
                    'panier_id' => $panier->getId(),
                    'soiree_id' => $ligne->getSoiree()->getId(),
                    'type_tarif' => $ligne->getTypeTarif()->value,
                    'quantite' => $ligne->getQuantite()
                ]);
            }
        }
    }

    public function findByUtilisateurId(string $utilisateurId): ?Panier
    {
        $stmt = $this->pdo->prepare('SELECT * FROM panier WHERE utilisateur_id = :utilisateur_id');
        $stmt->execute(['utilisateur_id' => $utilisateurId]);
        $panierData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$panierData) {
            return null;
        }

        return $this->findById($panierData['id']);
    }

}