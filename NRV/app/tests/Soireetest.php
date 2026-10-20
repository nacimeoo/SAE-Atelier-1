<?php
declare(strict_types=1);

use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Spectacle;
use PHPUnit\Framework\TestCase;

class SoireeTest extends TestCase
{
    private function creerSoiree(int $capacite, int $vendues): Soiree
    {
        $lieu = new Lieu('l1', "L'Autre Canal", 'Nancy', $capacite);
        return new Soiree('s1', 'Nuit de fer', 'Soirée métal', '2026-11-06', '19:30', 32.0, 22.0, $vendues, $lieu);
    }

    public function testPlacesRestantes(): void
    {
        $soiree = $this->creerSoiree(500, 288);
        $this->assertSame(212, $soiree->getPlacesRestantes());
        $this->assertFalse($soiree->estComplete());
    }

    public function testSoireeComplete(): void
    {
        $soiree = $this->creerSoiree(300, 300);
        $this->assertSame(0, $soiree->getPlacesRestantes());
        $this->assertTrue($soiree->estComplete());
    }

    public function testAjouterSpectacle(): void
    {
        $soiree = $this->creerSoiree(500, 0);
        $soiree->ajouterSpectacle(new Spectacle('sp1', 'Iron Wolves', 'Iron Wolves', 'desc', 'Métal', null, '22:15'));
        $this->assertCount(1, $soiree->getSpectacles());
    }
}
