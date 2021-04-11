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
    private array $result;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(array $sessions) : bool
    {
        $this->setSessions($sessions);

        foreach ($this->rules as $rule)
        {
            if ($this->isRuleApplicable($rule))
            {
                $this->applyRuleToSessions($rule);
            }
        }

        return $this->evaluateResult();
    }

    private function applyRuleToSessions(Rule $rule) : void
    {
        $firstSession = $this->firstSessionByAction($rule->getAction());

        $cooldownPeriodEnd = (clone $firstSession->getStart())->add($rule->getCooldownPeriod());
        $evaluationPeriodEnd = (clone $firstSession->getStart())->add($rule->getEvaluationPeriod());
        $totalActionDurationInEvaluationPeriod = new DateInterval("PT0S");
        $totalActionDurationInCooldownPeriod = clone $totalActionDurationInEvaluationPeriod;

        foreach ($this->sessions as $i => $session)
        {
            if ($this->isSessionAlreadyEvaluated($i))
            {
                continue;
            }

            if ($session->getAction() == $rule->getAction())
            {
                $sessionDuration = $session->getDuration();

                $totalActionDurationInEvaluationPeriod = DateIntervalOp::add($totalActionDurationInEvaluationPeriod, $sessionDuration);
                $totalActionDurationInCooldownPeriod = DateIntervalOp::add($totalActionDurationInCooldownPeriod, $sessionDuration);
            }

            $evaluationPeriodHasEnded = $session->getEnd() >= $evaluationPeriodEnd;
            $totalActionDurationInEvaluationPeriod = $this->cutExcessTime($session->getEnd(), $evaluationPeriodEnd, $totalActionDurationInEvaluationPeriod);

            $operator = $this->getOperatorByRule($rule);

            $actionDurationMatched = !DateIntervalOp::$operator($rule->getActionDuration(), $totalActionDurationInEvaluationPeriod);

            if ($evaluationPeriodHasEnded)
            {
                $totalActionDurationInEvaluationPeriod = new DateInterval("PT0S");
                $evaluationPeriodEnd->add($rule->getEvaluationPeriod());
            }

            $cooldownPeriodHasEnded = $session->getEnd() >= $cooldownPeriodEnd;
            $totalActionDurationInCooldownPeriod = $this->cutExcessTime($session->getEnd(), $cooldownPeriodEnd, $totalActionDurationInCooldownPeriod);

            $count = ceil(DateIntervalOp::div($totalActionDurationInCooldownPeriod, $rule->getActionDuration()));

            $instanceCountMatched = ($rule->getInstanceCount() == 0 || $rule->getInstanceCount() >= $count);

            if ($cooldownPeriodHasEnded)
            {
                $cooldownPeriodEnd->add($rule->getCooldownPeriod());
            }

            if ($session->getAction() == $rule->getAction())
            {
                $this->result[$i] = ($actionDurationMatched && $instanceCountMatched);
            }
        }
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
        foreach ($this->sessions as $session)
        {
            if ($session->getAction() == $action)
            {
                return $session;
            }
        }

        return null;
    }

    private function isSessionAlreadyEvaluated(int $index) : bool
    {
        return (isset($this->result[$index]) && $this->result[$index]);
    }

    private function evaluateResult() : bool
    {
        foreach ($this->result as $result)
        {
            if (!$result)
            {
                return false;
            }
        }

        return true;
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

    private function sortSessions() : void
    {
        usort($this->sessions, function($a, $b) {
            return $a->getStart() <=> $b->getStart();
        });
    }
}