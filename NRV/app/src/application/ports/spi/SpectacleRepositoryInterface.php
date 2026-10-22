<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use toubilib\domain\entities\Spectacle;

interface SpectacleRepositoryInterface
{
    public function findAll(?string $date = null, ?string $style = null): array;
    public function findStyle(): array;
}