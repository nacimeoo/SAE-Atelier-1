<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Spectacle;

class SpectacleDTO
{
    public function __construct(
        public string $id,
        public string $titre,
        public string $artistes,
        public string $description,
        public string $style,
        public ?string $image,
        public ?string $urlVideo,
        public string $heure,
        public string $soireeId,
        public string $date,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'artistes' => $this->artistes,
            'description' => $this->description,
            'style' => $this->style,
            'image' => $this->image,
            'urlVideo' => $this->urlVideo,
            'heure' => $this->heure,
            'soireeId' => $this->soireeId,
            'date' => $this->date
        ];
    }

    
}
