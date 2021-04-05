<?php declare(strict_types=1);

namespace helmet91;

use helmet91\entities\Rule;
use helmet91\entities\Session;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testCreatingValidator() : void
    {
        $validator = new Validator(Rule::init());

        $this->assertInstanceOf(Validator::class, $validator);
    }

    public function testValidatingDrivingSessionNotExceeding9Hours() : void
    {
        $rule = Rule::init()
            ->withAction(Action::DRIVING)
            ->withActionDuration(new \DateInterval("PT9H"))
            ->withActionDurationRelation(Rule::RELATION_MAX);

        $validator = new Validator($rule);
        $session = new Session(Action::DRIVING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 09:00:00"));

        $this->assertTrue($validator->validate($session));
    }

    public function testValidatingDrivingSessionExceeding9Hours() : void
    {
        $rule = Rule::init()
            ->withAction(Action::DRIVING)
            ->withActionDuration(new \DateInterval("PT9H"))
            ->withActionDurationRelation(Rule::RELATION_MAX);

        $validator = new Validator($rule);
        $session = new Session(Action::DRIVING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 09:00:01"));

        $this->assertFalse($validator->validate($session));
    }

    public function testValidatingRestingSessionAtLeast11Hours() : void
    {
        $rule = Rule::init()
            ->withAction(Action::RESTING)
            ->withActionDuration(new \DateInterval("PT11H"))
            ->withActionDurationRelation(Rule::RELATION_MIN);

        $validator = new Validator($rule);
        $session = new Session(Action::RESTING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 11:00:00"));

        $this->assertTrue($validator->validate($session));
    }

    public function testValidatingRestingSessionLessThan11Hours() : void
    {
        $rule = Rule::init()
            ->withAction(Action::RESTING)
            ->withActionDuration(new \DateInterval("PT11H"))
            ->withActionDurationRelation(Rule::RELATION_MIN);

        $validator = new Validator($rule);
        $session = new Session(Action::RESTING, new \DateTime("2021-03-12 00:00:00"), new \DateTime("2021-03-12 10:59:59"));

        $this->assertFalse($validator->validate($session));
    }
}
