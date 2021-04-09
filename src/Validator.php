<?php declare(strict_types=1);

namespace helmet91;

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
            $this->applyRuleToSessions($rule);
        }

        return $this->evaluateResult();
    }

    private function applyRuleToSessions(Rule $rule) : void
    {
        $firstSession = $this->firstSessionOfAction($rule->getAction());

        if ($firstSession)
        {
            $cooldownPeriodEnd = $this->dateIntervalToSegment($firstSession->getStart(), $rule->getCooldownPeriod())[1];
            $evaluationPeriodEnd = $this->dateIntervalToSegment($firstSession->getStart(), $rule->getEvaluationPeriod())[1];
            $totalActionDurationInEvaluationPeriod = new \DateInterval("PT0S");
            $totalActionDurationInCooldownPeriod = clone $totalActionDurationInEvaluationPeriod;

            foreach ($this->sessions as $i => $session)
            {
                if ($this->isSessionAlreadyEvaluated($i))
                {
                    break;
                }

                if ($session->getAction() == $rule->getAction())
                {
                    $sessionDuration = $session->getEnd()->diff($session->getStart(), true);

                    $totalActionDurationInEvaluationPeriod = DateIntervalOp::add($totalActionDurationInEvaluationPeriod, $sessionDuration);
                    $totalActionDurationInCooldownPeriod = DateIntervalOp::add($totalActionDurationInCooldownPeriod, $sessionDuration);
                }

                $evaluationPeriodHasEnded = $session->getEnd() >= $evaluationPeriodEnd;

                if ($evaluationPeriodHasEnded)
                {
                    $surplus = $session->getEnd()->diff($evaluationPeriodEnd, true);

                    $totalActionDurationInEvaluationPeriod = DateIntervalOp::sub($totalActionDurationInEvaluationPeriod, $surplus);
                }

                $operator = $this->getOperatorByRule($rule);

                $actionDurationMatched = !DateIntervalOp::$operator($rule->getActionDuration(), $totalActionDurationInEvaluationPeriod);

                if ($evaluationPeriodHasEnded)
                {
                    $totalActionDurationInEvaluationPeriod = new \DateInterval("PT0S");
                    $evaluationPeriodEnd->add($rule->getEvaluationPeriod());
                }

                $cooldownPeriodHasEnded = $session->getEnd() >= $cooldownPeriodEnd;

                if ($cooldownPeriodHasEnded)
                {
                    $surplus = $session->getEnd()->diff($cooldownPeriodEnd, true);

                    $totalActionDurationInCooldownPeriod = DateIntervalOp::sub($totalActionDurationInCooldownPeriod, $surplus);
                }

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
    }

    private function firstSessionOfAction(int $action) : Session|null
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

    private function dateIntervalToSegment(\DateTime $date, \DateInterval $interval) : array
    {
        $start = clone $date;
        $end = (clone $date)->add($interval);

        return [$start, $end];
    }

    private function setSessions(array $sessions) : void
    {
        $this->sessions = $sessions;
        $this->sortSessions($this->sessions);
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

    private function sortSessions(array &$sessions) : void
    {
        usort($sessions, function($a, $b) {
            return $a->getStart() <=> $b->getStart();
        });
    }
}