<?php
declare(strict_types=1);

use nrv\domain\entities\Pass;
use nrv\domain\entities\TypeTarif;
use PHPUnit\Framework\TestCase;

class PassTest extends TestCase
{
    public function testTarifs(): void
    {
        $pass = new Pass('p1', 'Pass 3 soirées', 100.0, 70.0, []);
        $this->assertSame(100.0, $pass->getTarif());
        $this->assertSame(70.0, $pass->getTarif(TypeTarif::REDUIT));
    }

    public function testPrixAvecRemise(): void
    {
        $pass = new Pass('p1', 'Pass 3 soirées', 100.0, 70.0, []);
        $this->assertSame(97.0, $pass->getPrix());
        $this->assertSame(67.9, $pass->getPrix(TypeTarif::REDUIT));
    }
}
