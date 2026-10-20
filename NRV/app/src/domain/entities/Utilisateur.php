<?php
declare(strict_types=1);

namespace nrv\domain\entities;

use nrv\domain\entities\Role;

class Utilisateur
{
    public function __construct(
        private string $id,
        private string $nom,
        private string $prenom,
        private string $email,
        private string $passwordHash,
        private Role $role = Role::CLIENT,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}
    