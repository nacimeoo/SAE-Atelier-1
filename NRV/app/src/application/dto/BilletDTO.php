<?php
declare(strict_types=1);

namespace nrv\application\dto;

class BilletDTO
{
    public function __construct(
        public string $id,
        public string $reference,
        public string $token,
        public string $acheteur,
        public string $libelle,
        public ?string $date,
        public ?string $heure,
        public ?string $lieu,
        public string $typeTarif,
        public float $prix
    ) {}

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'reference'  => $this->reference,
            'token'      => $this->token,
            'acheteur'   => $this->acheteur,
            'libelle'    => $this->libelle,
            'date'       => $this->date,
            'heure'      => $this->heure,
            'lieu'       => $this->lieu,
            'type_tarif' => $this->typeTarif,
            'prix'       => $this->prix,
            '_links'     => ['self' => ['href' => '/billets/' . $this->token]],
        ];
    }
}
