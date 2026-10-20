<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Lieu
{
    public function __construct(
        private string $id,
        private string $nom,
        private string $adresse,
        private int $nbPlaces,
    ){}

    public function getId(): string 
    { 
        return $this->id; 
    }

    public function getNom(): string 
    { 
        return $this->nom; 
    }
    public function getAdresse(): string 
    { 
        return $this->adresse; 
    }
    public function getNbPlaces(): int 
    { 
        return $this->nbPlaces; 
    }
}