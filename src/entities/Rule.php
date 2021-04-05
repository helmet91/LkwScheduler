<?php declare(strict_types=1);

namespace helmet91\entities;

class Rule
{
    const RELATION_MIN = 1;
    const RELATION_MAX = 2;

    private int $action;
    private \DateInterval $actionDuration;
    private int $actionDurationRelation;

    private function __construct() {}

    public static function init() : self
    {
        return new Rule();
    }

    public function getAction() : int
    {
        return $this->action;
    }

    public function withAction(int $action) : self
    {
        $this->action = $action;
        return $this;
    }

    public function getActionDuration() : \DateInterval
    {
        return $this->actionDuration;
    }

    public function withActionDuration(\DateInterval $duration) : self
    {
        $this->actionDuration = $duration;
        return $this;
    }

    public function getActionDurationRelation() : int
    {
        return $this->actionDurationRelation;
    }

    public function withActionDurationRelation(int $relation) : self
    {
        $this->actionDurationRelation = $relation;
        return $this;
    }
}