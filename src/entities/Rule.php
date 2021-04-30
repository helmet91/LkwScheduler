<?php declare(strict_types=1);

namespace helmet91\entities;

use DateInterval;

class Rule
{
    const RELATION_MIN = 1;
    const RELATION_MAX = 2;

    private int $action;
    private DateInterval $actionDuration;
    private int $actionDurationRelation;
    private int $instanceCount = 0;
    private DateInterval $cooldownPeriod;
    private DateInterval $evaluationPeriod;
    private bool|array $splittable = true;

    private function __construct()
    {
        $this->cooldownPeriod = new DateInterval("PT0S");
    }

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

    public function getActionDuration() : DateInterval
    {
        return $this->actionDuration;
    }

    public function withActionDuration(DateInterval $duration) : self
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

    public function getInstanceCount() : int
    {
        return $this->instanceCount;
    }

    public function withInstanceCount(int $count) : self
    {
        $this->instanceCount = $count;
        return $this;
    }

    public function getCooldownPeriod() : DateInterval
    {
        return $this->cooldownPeriod;
    }

    public function withCooldownPeriod(DateInterval $period) : self
    {
        $this->cooldownPeriod = $period;
        return $this;
    }

    public function getEvaluationPeriod() : DateInterval
    {
        return $this->evaluationPeriod;
    }

    public function withEvaluationPeriod(DateInterval $period) : self
    {
        $this->evaluationPeriod = $period;
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
}