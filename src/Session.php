<?php declare(strict_types=1);

namespace helmet91;

class Session
{
    const DRIVING = 1;
    const RESTING = 2;

    private int $action;
    private \DateTime $start;
    private \DateTime $end;

    public function __construct(int $action, \DateTime $start, \DateTime $end)
    {
        $this->action = $action;
        $this->start = $start;
        $this->end = $end;
    }

    public function getAction() : int
    {
        return $this->action;
    }

    public function getStart() : \DateTime
    {
        return $this->start;
    }

    public function getEnd() : \DateTime
    {
        return $this->end;
    }
}