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
            ->withActionDurationRelation(Rule::RELATION_MAX);
    }

    public function testValidatingDailyRestingSessionsAtLeast11Hours() : void
    {
        $rule = $this->getSingle11HResting();

        $validator = new Validator([$rule]);
        $session1 = new Session(Action::RESTING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 11:00:00"));
        $session2 = new Session(Action::RESTING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 11:00:00"));

        $this->assertTrue($validator->validate([$session1, $session2]));
    }

    public function testValidatingDailyRestingSessionLessThan11Hours() : void
    {
        $rule = $this->getSingle11HResting();

        $validator = new Validator([$rule]);
        $session1 = new Session(Action::RESTING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 11:00:00"));
        $session2 = new Session(Action::RESTING, new \DateTime("2021-03-13 00:00:00"), new \DateTime("2021-03-13 10:59:59"));

        $this->assertFalse($validator->validate([$session1, $session2]));
    }

    private function getSingle11HResting() : Rule
    {
        return Rule::init()
            ->withAction(Action::RESTING)
            ->withActionDuration(new \DateInterval("PT11H"))
            ->withActionDurationRelation(Rule::RELATION_MIN);
    }
}
