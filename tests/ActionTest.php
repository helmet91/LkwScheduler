<?php declare(strict_types=1);

namespace helmet91;

use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function testDriving() : void
    {
        $this->assertEquals(1, Action::DRIVING);
    }

    public function testResting() : void
    {
        $this->assertEquals(2, Action::RESTING);
    }
}
