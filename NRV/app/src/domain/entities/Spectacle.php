<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Spectacle
{
    public function __construct(
        private string $id,
        private string $titre,
        private string $artistes,
        private string $description,
        private string $style,
        private string $image,
        private ?string $urlVideo,
        private \DateTimeImmutable $heure,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getArtistes(): string
    {
        return $this->artistes;
    }

    public function getDescription(): string
    {
        return $this->description;
    }   

    public function getStyle(): string
    {
        return $this->style;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getUrlVideo(): ?string
    {
        return $this->urlVideo;
    }

    public function getHeure(): \DateTimeImmutable
    {
        return $this->heure;
    }

    
}
