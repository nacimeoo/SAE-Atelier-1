<?php
declare(strict_types=1);

namespace nrv\application\ports\api;
use nrv\application\dto\UtilisateurDTO;

interface ServiceUtilisateurInterface
{
    public function inscrire(string $nom, string $prenom, string $email, string $password): void;
    public function connecter(string $email, string $password): UtilisateurDTO;
}