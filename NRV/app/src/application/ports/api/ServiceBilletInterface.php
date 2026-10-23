<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\BilletDTO;

interface ServiceBilletInterface
{
    public function listerBilletsUtilisateur(string $utilisateurId): array;
    public function getBilletParToken(string $token): BilletDTO;
    public function listerJaugesSoirees(): array;

}
