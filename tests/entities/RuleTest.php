<?php declare(strict_types=1);

namespace helmet91\entities;

use DateInterval;
use Error;
use helmet91\Action;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    public function testCreatingRule() : void
    {
        $rule = Rule::init();

        $this->assertInstanceOf(Rule::class, $rule);
    }

    public function testIfConstructorInvocationThrows() : void
    {
        $this->expectException(Error::class);

        new Rule();
    }

    public function testSettingAction() : void
    {
        $rule = Rule::init()->withAction(Action::DRIVING);

        $this->assertEquals(Action::DRIVING, $rule->getAction());
    }

    public function testSettingActionDuration() : void
    {
        $rule = Rule::init()->withActionDuration(new DateInterval("PT10H"));

        $this->assertEquals(10, $rule->getActionDuration()->h);
    }

    public function testSettingActionDurationRelation() : void
    {
        $rule = Rule::init()->withActionDurationRelation(Rule::RELATION_MIN);
        $this->assertEquals(Rule::RELATION_MIN, $rule->getActionDurationRelation());

        $rule = Rule::init()->withActionDurationRelation(Rule::RELATION_MAX);
        $this->assertEquals(Rule::RELATION_MAX, $rule->getActionDurationRelation());
    }

    public function testSettingInstanceCount() : void
    {
        $rule = Rule::init()->withInstanceCount(2);
        $this->assertEquals(2, $rule->getInstanceCount());
    }

    public function testSettingCooldownPeriod() : void
    {
        $rule = Rule::init()->withCooldownPeriod(new DateInterval("P1W"));
        $this->assertEquals(7, $rule->getCooldownPeriod()->d);
    }

    public function testSettingEvaluationPeriod() : void
    {
        $rule = Rule::init()->withEvaluationPeriod(new DateInterval("P1D"));
        $this->assertEquals(1, $rule->getEvaluationPeriod()->d);
    }

    /**
     * @dataProvider providerForSettingSplittable
     */
    public function testSettingSplittable($flag) : void
    {
        $rule = Rule::init()->withSplittable($flag);
        $this->assertEquals($flag, $rule->getSplittable());
    }

    public function providerForSettingSplittable() : array
    {
        return [
            [true],
            [false],
            [[new DateInterval("PT15M")]],
        ];
    }
}
