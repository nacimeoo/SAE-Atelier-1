<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\domain\entities\Spectacle;
use PDO;


class SpectacleRepository implements SpectacleRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

    }

    public function findAll(?string $date = null, ?string $style = null): array
    {
        $sql = 'SELECT *, soiree.date AS date_soiree FROM spectacle 
                JOIN soiree ON soiree.id = spectacle.soiree_id';
        
        $params = [];

        if ($date !== null) {
            $sql .= ' AND soiree.date = :date';
            $params['date'] = $date;
        }
        if ($style !== null) {
            $sql .= ' AND spectacle.style = :style';
            $params['style'] = $style;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $spectacles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $spectacle = new Spectacle(
                $row['id'],
                $row['titre'],
                $row['artistes'],
                $row['description'],
                $row['style'],
                $row['image'],
                $row['url_video'],
                new \DateTimeImmutable($row['heure']),
                $row['soiree_id'],
                new \DateTimeImmutable($row['date_soiree'])
            );
            $spectacles[] = $spectacle;
        }
        return $spectacles;
    }
}