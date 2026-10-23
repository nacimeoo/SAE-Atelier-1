<?php
declare(strict_types=1);

use nrv\domain\entities\Lieu;
use PHPUnit\Framework\TestCase;

class LieuTest extends TestCase
{
    public function testGetters(): void
    {
        $lieu = new Lieu('l1', "L'Autre Canal", '45 Bd d\'Austrasie, Nancy', 500);
        $this->assertSame('l1', $lieu->getId());
        $this->assertSame("L'Autre Canal", $lieu->getNom());
        $this->assertSame('45 Bd d\'Austrasie, Nancy', $lieu->getAdresse());
        $this->assertSame(500, $lieu->getNbPlaces());
    }
}
