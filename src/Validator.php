<?php declare(strict_types=1);

namespace helmet91;

use helmet91\entities\Rule;
use helmet91\entities\Session;
use helmet91\utils\DateIntervalOp;

class Validator
{
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(array $sessions) : bool
    {
        foreach ($sessions as $session)
        {
            if (!$this->validateSession($session))
            {
                return false;
            }
        }

        return true;
    }

    private function validateSession(Session $session) : bool
    {
        foreach ($this->rules as $rule)
        {
            if ($this->validateSessionByRule($session, $rule))
            {
                return true;
            }
        }

        return false;
    }

    private function validateSessionByRule(Session $session, Rule $rule) : bool
    {
        $left = $rule->getActionDuration();
        $right = $session->getEnd()->diff($session->getStart(), true);

        $operator = $this->getOperatorByRule($rule);

        return !DateIntervalOp::$operator($left, $right);
    }

    private function getOperatorByRule(Rule $rule) : string
    {
        $operator = "";

        if ($rule->getActionDurationRelation() == Rule::RELATION_MAX)
        {
            $operator = "lessThan";
        }

        if ($rule->getActionDurationRelation() == Rule::RELATION_MIN)
        {
            $operator = "greaterThan";
        }

        return $operator;
    }
}