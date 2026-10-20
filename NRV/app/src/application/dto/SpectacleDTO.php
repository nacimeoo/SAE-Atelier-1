<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Spectacle;

class SpectacleDTO
{
    public function __construct(
        public string $id,
        public string $titre,
        public string $date,
        public string $heure,
        public string $image,
        public string $soireeId,
    ) {
    }

    public static function fromEntity(Spectacle $s): self
    {
        return new self(
            $s->getId(),
            $s->getTitre(),
            $s->getDate()->format('Y-m-d'),
            $s->getHeure()->format('H:i'),
            $s->getImage(),
            $s->getSoireeId(),
        );
    }
}