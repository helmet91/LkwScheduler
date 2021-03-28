<?php declare(strict_types=1);

namespace helmet91\entities;

use PHPUnit\Framework\TestCase;
use helmet91\Action;

class SessionTest extends TestCase
{
    private function createSession(int $action) : Session
    {
        $start = new \DateTime("2021-03-08 08:00:00");
        $end = new \DateTime("2021-03-08 10:00:00");

        return new Session($action, $start, $end);
    }

    private function createDriving() : Session
    {
        return $this->createSession(Action::DRIVING);
    }

    private function createResting() : Session
    {
        return $this->createSession(Action::RESTING);
    }

    public function testCreatingSession() : void
    {
        $session = $this->createDriving();

        $this->assertEquals(Action::DRIVING, $session->getAction());
    }

    public function testActionProperty() : void
    {
        $session = $this->createResting();

        $this->assertEquals(Action::RESTING, $session->getAction());
    }

    public function testStartProperty() : void
    {
        $session = $this->createResting();

        $this->assertEquals(new \DateTime("2021-03-08 08:00:00"), $session->getStart());
    }

    public function testEndProperty() : void
    {
        $session = $this->createResting();

        $this->assertEquals(new \DateTime("2021-03-08 10:00:00"), $session->getEnd());
    }
}
