<?php
declare(strict_types=1);

namespace nrv\application\ports\api;
use nrv\application\dto\PanierDTO;


interface ServicePanierInterface
{
    public function ajouterBilletAuPanier(string $panierId, string $soireeId, string $typeTarif, int $quantite): PanierDTO;
}