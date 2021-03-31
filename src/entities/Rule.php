<?php declare(strict_types=1);

namespace helmet91\entities;

class Rule
{
    const RELATION_MIN = '>';
    const RELATION_MAX = '<';

    private int $action;
    private \DateInterval $actionDuration;
    private string $actionRelation;
    private int $repetitionCount;
    private \DateInterval $evaluationPeriod;
    private \DateInterval $cooldownPeriod;
    private bool|array $splittable;
    private array $dueAfter;

    private function __construct() {}

    public static function init() : self
    {
        return new self();
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

    public function getActionRelation() : string
    {
        return $this->actionRelation;
    }

    public function withActionRelation(string $relation) : self
    {
        $this->actionRelation = $relation;

        return $this;
    }

    public function getRepetitionCount() : int
    {
        return $this->repetitionCount;
    }

    public function withRepetitionCount(int $count) : self
    {
        $this->repetitionCount = $count;
        return $this;
    }

    public function getEvaluationPeriod() : \DateInterval
    {
        return $this->evaluationPeriod;
    }

    public function withEvaluationPeriod(\DateInterval $period) : self
    {
        $this->evaluationPeriod = $period;

        return $this;
    }

    public function getCooldownPeriod() : \DateInterval
    {
        return $this->cooldownPeriod;
    }

    public function withCooldownPeriod(\DateInterval $period) : self
    {
        $this->cooldownPeriod = $period;

        return $this;
    }
    public function getSplittable() : bool|array
    {
        return $this->splittable;
    }

    public function withSplittable(bool|array $splittable) : self
    {
        $this->splittable = $splittable;

        return $this;
    }

    public function getDueAfter() : array
    {
        return $this->dueAfter;
    }

    public function withDueAfter(int $action, \DateInterval $period) : self
    {
        $this->dueAfter = ["action" => $action, "period" => $period];
        return $this;
    }
}