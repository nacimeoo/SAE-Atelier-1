<?php
declare(strict_types=1);

namespace nrv\application\dto;

class UtilisateurDTO
{
    public function __construct(
        public string $token,
        public string $nom,
        public string $prenom,
        public string $role
    ) {}

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'role' => $this->role
        ];
    }
}