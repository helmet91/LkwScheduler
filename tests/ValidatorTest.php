<?php declare(strict_types=1);

namespace helmet91;

use helmet91\entities\Rule;
use helmet91\entities\Session;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testCreatingValidator() : void
    {
        $validator = new Validator([Rule::init()]);

        $this->assertInstanceOf(Validator::class, $validator);
    }

    public function testValidatingDailyDrivingSessionsNotExceeding9Hours() : void
    {
        $rule = $this->getSingle9HDriving();

        $validator = new Validator([$rule]);
        $session1 = new Session(Action::DRIVING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 09:00:00"));
        $session2 = new Session(Action::DRIVING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 09:00:00"));

        $this->assertTrue($validator->validate([$session1, $session2]));
    }

    public function testValidatingDailyDrivingSessionExceeding9Hours() : void
    {
        $rule = $this->getSingle9HDriving();

        $validator = new Validator([$rule]);
        $session1 = new Session(Action::DRIVING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 09:00:01"));
        $session2 = new Session(Action::DRIVING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 09:00:00"));

        $this->assertFalse($validator->validate([$session1, $session2]));
    }

    private function getSingle9HDriving() : Rule
    {
        return Rule::init()
            ->withAction(Action::DRIVING)
            ->withActionDuration(new \DateInterval("PT9H"))
            ->withActionDurationRelation(Rule::RELATION_MAX)
            ->withEvaluationPeriod(new \DateInterval("P1D"));
    }

    public function testValidatingDailyRestingSessionsAtLeast11Hours() : void
    {
        $rule = $this->getSingle11HResting();

        $validator = new Validator([$rule]);
        $session1 = new Session(Action::RESTING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-13 11:00:00"));

        $this->assertTrue($validator->validate([$session1]));
    }

    public function testValidatingDailyRestingSessionLessThan11Hours() : void
    {
        $rule = $this->getSingle11HResting();

        $validator = new Validator([$rule]);
        $session2 = new Session(Action::RESTING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 10:59:59"));

        $this->assertFalse($validator->validate([$session2]));
    }

    private function getSingle11HResting() : Rule
    {
        return Rule::init()
            ->withAction(Action::RESTING)
            ->withActionDuration(new \DateInterval("PT11H"))
            ->withActionDurationRelation(Rule::RELATION_MIN)
            ->withEvaluationPeriod(new \DateInterval("P1D"));
    }

    public function testValidatingDailyDrivingSessions10HTwicePerWeek() : void
    {
        $rule9H = $this->getSingle9HDriving();
        $rule10H = $this->getSingle10HDriving();

        $validator = new Validator([$rule9H, $rule10H]);

        $sessions = [
            new Session(Action::DRIVING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 10:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-14 00:00:00"), new \DateTime("2021-03-14 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-15 00:00:00"), new \DateTime("2021-03-15 10:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-16 00:00:00"), new \DateTime("2021-03-16 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-17 00:00:00"), new \DateTime("2021-03-17 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-18 00:00:00"), new \DateTime("2021-03-18 09:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-19 00:00:00"), new \DateTime("2021-03-19 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-20 00:00:00"), new \DateTime("2021-03-20 10:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-21 00:00:00"), new \DateTime("2021-03-21 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-22 00:00:00"), new \DateTime("2021-03-22 10:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-23 00:00:00"), new \DateTime("2021-03-23 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-24 00:00:00"), new \DateTime("2021-03-24 09:00:00")),
            new Session(Action::DRIVING, new \DateTime("2021-03-25 00:00:00"), new \DateTime("2021-03-25 09:00:00")),
        ];

        $this->assertTrue($validator->validate($sessions));
    }

    public function testValidatingDailyDrivingSessions10HThreeTimesPerWeek() : void
    {
        $rule9H = $this->getSingle9HDriving();
        $rule10H = $this->getSingle10HDriving();

        $validator = new Validator([$rule9H, $rule10H]);

        $sessions = [
            new Session(Action::DRIVING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 09:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-12 09:00:00"), new \DateTime("2021-03-13 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 10:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-13 10:00:00"), new \DateTime("2021-03-14 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-14 00:00:00"), new \DateTime("2021-03-14 09:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-14 09:00:00"), new \DateTime("2021-03-15 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-15 00:00:00"), new \DateTime("2021-03-15 10:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-15 10:00:00"), new \DateTime("2021-03-16 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-16 00:00:00"), new \DateTime("2021-03-16 10:00:00")),
        ];

        $this->assertFalse($validator->validate($sessions));
    }

    private function getSingle10HDriving() : Rule
    {
        return Rule::init()
            ->withAction(Action::DRIVING)
            ->withActionDuration(new \DateInterval("PT10H"))
            ->withActionDurationRelation(Rule::RELATION_MAX)
            ->withInstanceCount(2)
            ->withCooldownPeriod(new \DateInterval("P1W"))
            ->withEvaluationPeriod(new \DateInterval("P1D"));
    }

    public function testValidatingWeeklyDrivingSessions56HPerWeek() : void
    {
        $rule = Rule::init()
            ->withAction(Action::DRIVING)
            ->withActionDuration(new \DateInterval("PT56H"))
            ->withActionDurationRelation(Rule::RELATION_MAX)
            ->withInstanceCount(1)
            ->withCooldownPeriod(new \DateInterval("P1W"))
            ->withEvaluationPeriod(new \DateInterval("P1W"));

        $validator = new Validator([$rule]);

        $sessions = [
            new Session(Action::DRIVING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 10:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-12 10:00:00"), new \DateTime("2021-03-13 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 10:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-13 10:00:00"), new \DateTime("2021-03-14 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-14 00:00:00"), new \DateTime("2021-03-14 10:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-14 10:00:00"), new \DateTime("2021-03-15 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-15 00:00:00"), new \DateTime("2021-03-15 10:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-15 10:00:00"), new \DateTime("2021-03-16 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-16 00:00:00"), new \DateTime("2021-03-16 10:00:00")),
            new Session(Action::RESTING, new \DateTime("2021-03-16 10:00:00"), new \DateTime("2021-03-17 00:00:00")),

            new Session(Action::DRIVING, new \DateTime("2021-03-17 00:00:00"), new \DateTime("2021-03-17 06:00:00")),
        ];

        $this->assertTrue($validator->validate($sessions));
    }
}
