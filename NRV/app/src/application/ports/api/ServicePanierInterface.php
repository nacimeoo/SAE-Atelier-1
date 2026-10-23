<?php

declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\PanierDTO;


interface ServicePanierInterface
{
    public function ajouterBilletAuPanier(string $UtilisateurId, ?string $soireeId, ?string $typeTarif, ?int $quantite): PanierDTO;
    public function consulterPanier(string $utilisateurId): PanierDTO;
}
