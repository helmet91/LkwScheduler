<?php declare(strict_types=1);

namespace helmet91;

use helmet91\entities\Rule;
use helmet91\entities\Session;
use helmet91\utils\DateIntervalOp;

class Validator
{
    private Rule $rule;

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    public function validate(Session $session) : bool
    {
        $left = $this->rule->getActionDuration();
        $right = $session->getEnd()->diff($session->getStart(), true);

        $operator = $this->getOperatorByRule();

        return !DateIntervalOp::$operator($left, $right);
    }

    private function getOperatorByRule() : string
    {
        $operator = "";

        if ($this->rule->getActionDurationRelation() == Rule::RELATION_MAX)
        {
            $operator = "lessThan";
        }

        if ($this->rule->getActionDurationRelation() == Rule::RELATION_MIN)
        {
            $operator = "greaterThan";
        }

        return $operator;
    }
}