<?php
declare(strict_types=1);

namespace nrv\application\dto;

use DateTimeImmutable;
use nrv\domain\entities\Soiree;
use nrv\application\dto\SpectacleDTO;


class SoireeDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $nom,
        public readonly string $thematique,
        public readonly DateTimeImmutable $date,
        public readonly DateTimeImmutable $heureDebut,
        public readonly string $lieuNom,
        public readonly string $lieuAdresse,
        public readonly float $tarif,
        public readonly int $placesRestantes,
        public readonly array $spectacles
    ) {}

    public static function fromEntity(Soiree $s): self
    {
        return new self(
            $s->getId(),
            $s->getNom(),
            $s->getThematique(),
            $s->getDate(),
            $s->getHeureDebut(),
            $s->getLieu()->getNom(),
            $s->getLieu()->getAdresse(),
            $s->getTarif(),
            $s->getPlacesRestantes(),
            array_map(fn($sp) => new SpectacleDTO(
                $sp->getId(),
                $sp->getTitre(),
                $sp->getArtistes(),
                $sp->getDescription(),
                $sp->getStyle(),
                $sp->getImage(),
                $sp->getUrlVideo(),
                $sp->getHeure()->format('H:i'),
                $sp->getSoireeId(),
                $sp->getDate()->format('Y-m-d')
            ), $s->getSpectacles())
        );
    }
}
