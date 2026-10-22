<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\SpectacleDTO;

interface ServiceSpectacleInterface
{
    public function listerSpectacles(?string $date = null, ?string $style = null): array;
    public function listerStyles(): array;
}