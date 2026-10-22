<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\domain\entities\Panier;

interface PanierRepositoryInterface
{
    public function findById(string $id): Panier;
    public function create(Panier $panier): void;
    public function ajouterLignePanier(Panier $panier): void;
    public function findByUtilisateurId(string $utilisateurId): ?Panier;


}
