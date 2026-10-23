<?php

declare(strict_types=1);

namespace nrv\application\ports\api;
use nrv\application\dto\CommandeDTO;

interface ServiceCommandeInterface
{
    public function validerPanier(string $utilisateurId): string;
    public function payerCommande(string $utilisateurId,string $commandeId, ?string $numeroCarte, ?string $expiration,?string $cvc): CommandeDTO;
}
