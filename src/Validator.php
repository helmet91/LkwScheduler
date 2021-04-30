<?php declare(strict_types=1);

namespace helmet91;

use DateInterval;
use DateTime;
use helmet91\entities\Rule;
use helmet91\entities\Session;
use helmet91\utils\DateIntervalOp;

class Validator
{
    private array $rules;
    private array $sessions;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
        $this->sortRules();
    }

    public function validate(array $sessions) : bool
    {
        $this->setSessions($sessions);

        foreach ($this->rules as $rule)
        {
            if ($this->isRuleApplicable($rule))
            {
                if ($this->applyRuleToSessions($rule))
                {
                    return true;
                }
            }
        }

        return false;
    }

    private function applyRuleToSessions(Rule $rule) : bool
    {
        return $this->evaluateInstanceCount($rule) && ($this->evaluateActionDuration($rule) || $this->evaluatePartialActionDuration($rule));
    }

    private function evaluateActionDuration(Rule $rule) : bool
    {
        $firstSession = $this->firstSessionByAction($rule->getAction());

        $evaluationPeriodEnd = (clone $firstSession->getStart())->add($rule->getEvaluationPeriod());
        $totalActionDurationInEvaluationPeriod = new DateInterval("PT0S");

        foreach ($this->sessions as $session)
        {
            $totalActionDurationInEvaluationPeriod = $this->incrementTotalDuration($totalActionDurationInEvaluationPeriod, $session, $rule);
            $totalActionDurationInEvaluationPeriod = $this->cutExcessTime($session->getEnd(), $evaluationPeriodEnd, $totalActionDurationInEvaluationPeriod);

            $operator = $this->getOperatorByRule($rule);

            $actionDurationMatched = !DateIntervalOp::$operator($rule->getActionDuration(), $totalActionDurationInEvaluationPeriod);

            if ($session->getEnd() >= $evaluationPeriodEnd || $this->lastSessionByAction($rule->getAction()) == $session)
            {
                $totalActionDurationInEvaluationPeriod = new DateInterval("PT0S");
                $evaluationPeriodEnd->add($rule->getEvaluationPeriod());
            }

            if ($session->getAction() == $rule->getAction())
            {
                if (!$actionDurationMatched)
                {
                    return false;
                }
            }
        }

        return true;
    }

    private function evaluatePartialActionDuration(Rule $rule) : bool
    {
        $firstSession = $this->firstSessionByAction($rule->getAction());

        $evaluationPeriodEnd = (clone $firstSession->getStart())->add($rule->getEvaluationPeriod());
        $totalActionDurationInEvaluationPeriod = new DateInterval("PT0S");

        $splitSessionPartIndex = 0;

        foreach ($this->sessions as $session)
        {
            $totalActionDurationInEvaluationPeriod = $this->incrementTotalDuration($totalActionDurationInEvaluationPeriod, $session, $rule);
            $totalActionDurationInEvaluationPeriod = $this->cutExcessTime($session->getEnd(), $evaluationPeriodEnd, $totalActionDurationInEvaluationPeriod);

            $operator = $this->getOperatorByRule($rule);

            $actionDurationMatched = !DateIntervalOp::$operator($rule->getActionDuration(), $totalActionDurationInEvaluationPeriod);

            $partialActionDurationMatched = false;

            if ($rule->getActionDurationRelation() == Rule::RELATION_MIN && $session->getAction() == $rule->getAction())
            {
                $partialActionDurationMatched = $rule->getSplittable() === true;

                if (!$partialActionDurationMatched && is_array($rule->getSplittable()))
                {
                    $ruleSplittableParts = $rule->getSplittable();

                    if (isset($ruleSplittableParts[$splitSessionPartIndex]))
                    {
                        $splitSessionPartDuration = $ruleSplittableParts[$splitSessionPartIndex];

                        $partialActionDurationMatched = !DateIntervalOp::$operator($splitSessionPartDuration, $session->getDuration());
                        $splitSessionPartIndex++;
                    }

                    if (!$partialActionDurationMatched)
                    {
                        $splitSessionPartIndex = -1;
                    }
                }
            }

            if ($session->getEnd() >= $evaluationPeriodEnd || $this->lastSessionByAction($rule->getAction()) == $session)
            {
                $totalActionDurationInEvaluationPeriod = new DateInterval("PT0S");
                $evaluationPeriodEnd->add($rule->getEvaluationPeriod());

                $partialActionDurationMatched = $actionDurationMatched;
                $splitSessionPartIndex = 0;
            }

            if ($session->getAction() == $rule->getAction())
            {
                if (!$partialActionDurationMatched)
                {
                    return false;
                }
            }
        }

        return true;
    }

    private function evaluateInstanceCount(Rule $rule) : bool
    {
        $firstSession = $this->firstSessionByAction($rule->getAction());

        $cooldownPeriodEnd = (clone $firstSession->getStart())->add($rule->getCooldownPeriod());
        $totalActionDurationInCooldownPeriod = new DateInterval("PT0S");

        foreach ($this->sessions as $session)
        {
            $totalActionDurationInCooldownPeriod = $this->incrementTotalDuration($totalActionDurationInCooldownPeriod, $session, $rule);
            $totalActionDurationInCooldownPeriod = $this->cutExcessTime($session->getEnd(), $cooldownPeriodEnd, $totalActionDurationInCooldownPeriod);

            $count = ceil(DateIntervalOp::div($totalActionDurationInCooldownPeriod, $rule->getActionDuration()));

            $instanceCountMatched = ($rule->getInstanceCount() == 0 || $rule->getInstanceCount() >= $count);

            if ($session->getEnd() >= $cooldownPeriodEnd)
            {
                $cooldownPeriodEnd->add($rule->getCooldownPeriod());
            }

            if ($session->getAction() == $rule->getAction() && !$instanceCountMatched)
            {
                return false;
            }
        }

        return true;
    }

    private function incrementTotalDuration(DateInterval $totalDuration, Session $session, Rule $rule) : DateInterval
    {
        if ($session->getAction() == $rule->getAction())
        {
            $totalDuration = DateIntervalOp::add($totalDuration, $session->getDuration());
        }

        return $totalDuration;
    }

    private function cutExcessTime(DateTime $sessionEnd, DateTime $boundary, DateInterval $totalTime) : DateInterval
    {
        if ($sessionEnd >= $boundary)
        {
            $excessTime = $sessionEnd->diff($boundary, true);
            $totalTime = DateIntervalOp::sub($totalTime, $excessTime);
        }

        return $totalTime;
    }

    private function isRuleApplicable(Rule $rule) : bool
    {
        return (bool)$this->firstSessionByAction($rule->getAction());
    }

    private function firstSessionByAction(int $action) : Session|null
    {
        return $this->findSessionByAction($this->sessions, $action);
    }

    private function lastSessionByAction(int $action) : Session|null
    {
        return $this->findSessionByAction(array_reverse($this->sessions), $action);
    }

    private function findSessionByAction(array $sessions, int $action) : Session|null
    {
        foreach ($sessions as $session)
        {
            if ($session->getAction() == $action)
            {
                return $session;
            }
        }

        return null;
    }

    private function getOperatorByRule(Rule $rule) : string
    {
        return match ($rule->getActionDurationRelation())
        {
            Rule::RELATION_MAX => "lessThan",
            Rule::RELATION_MIN => "greaterThan",
            default => "",
        };
    }

    private function setSessions(array $sessions) : void
    {
        $this->sessions = $sessions;
        $this->sortSessions();
    }

    private function sortRules() : void
    {
        usort($this->rules, function($a, $b) {
            if ($a->getAction() != $b->getAction())
            {
                return $a->getAction() <=> $b->getAction();
            }

            switch ($a->getActionDurationRelation())
            {
                case Rule::RELATION_MIN:
                    return DateIntervalOp::lessThan($a->getActionDuration(), $b->getActionDuration()) ? -1 : 1;
                case Rule::RELATION_MAX:
                    return DateIntervalOp::lessThan($b->getActionDuration(), $a->getActionDuration()) ? -1 : 1;
            }

            return 0;
        });
    }

    private function sortSessions() : void
    {
        usort($this->sessions, function($a, $b) {
            return $a->getStart() <=> $b->getStart();
        });
    }
}