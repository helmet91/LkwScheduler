<?php declare(strict_types=1);

namespace helmet91;

use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    private function createSession(int $action) : Session
    {
        $start = new \DateTime("2021-03-08 08:00:00");
        $end = new \DateTime("2021-03-08 10:00:00");

        return new Session($action, $start, $end);
    }

    public function testCreatingSession() : void
    {
        $session = $this->createSession(Session::DRIVING);

        $this->assertEquals(Session::DRIVING, $session->getAction());
    }

    public function testActionProperty() : void
    {
        $session = $this->createSession(Session::RESTING);

        $this->assertEquals(Session::RESTING, $session->getAction());
    }

    public function testStartProperty() : void
    {
        $session = $this->createSession(Session::RESTING);

        $this->assertEquals(new \DateTime("2021-03-08 08:00:00"), $session->getStart());
    }

    public function testEndProperty() : void
    {
        $session = $this->createSession(Session::RESTING);

        $this->assertEquals(new \DateTime("2021-03-08 10:00:00"), $session->getEnd());
    }
}
