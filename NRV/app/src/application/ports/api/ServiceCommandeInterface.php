<?php
declare(strict_types=1);
namespace nrv\application\ports\api;

interface ServiceCommandeInterface {
    public function validerPanier(string $utilisateurId): string;
}