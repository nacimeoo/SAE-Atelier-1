<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

interface BilletRepositoryInterface
{
    public function findPayesByUtilisateurId(string $utilisateurId): array;
}
