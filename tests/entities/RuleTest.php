<?php declare(strict_types=1);

namespace helmet91\entities;

use PHPUnit\Framework\TestCase;
use helmet91\Action;

class RuleTest extends TestCase
{
    public function testCreatingRule() : void
    {
        $rule = Rule::init();

        $this->assertInstanceOf(Rule::class, $rule);
    }

    /**
     * @dataProvider providerTestAction
     */
    public function testAction($inputAction, $expectedAction) : void
    {
        $rule = Rule::init()->withAction($inputAction);

        $this->assertEquals($expectedAction, $rule->getAction());
    }

    public function providerTestAction() : array
    {
        return [
            [Action::RESTING, Action::RESTING],
            [Action::DRIVING, Action::DRIVING],
        ];
    }

    public function testActionDuration() : void
    {
        $rule = Rule::init()->withActionDuration(new \DateInterval("PT45M"));

        $this->assertEquals(45, $rule->getActionDuration()->i);
    }

    public function testRepetitionCount() : void
    {
        $rule = Rule::init()->withRepetitionCount(3);

        $this->assertEquals(3, $rule->getRepetitionCount());
    }

    public function testEvaluationPeriod() : void
    {
        $rule = Rule::init()->withEvaluationPeriod(new \DateInterval("P1W"));

        $this->assertEquals(7, $rule->getEvaluationPeriod()->d);
    }

    public function testCooldownPeriod() : void
    {
        $rule = Rule::init()->withCooldownPeriod(new \DateInterval("P2W"));

        $this->assertEquals(14, $rule->getCooldownPeriod()->d);
    }

    public function testSplittableAsBool() : void
    {
        $rule = Rule::init()->withSplittable(true);

        $this->assertTrue($rule->getSplittable());
    }

    public function testSplittableAsArray() : void
    {
        $rule = Rule::init()->withSplittable([new \DateInterval("PT15M"), new \DateInterval("PT30M")]);

        $this->assertEquals(30, $rule->getSplittable()[1]->i);
    }

    public function testDueAfter() : void
    {
        $rule = Rule::init()->withDueAfter(Action::DRIVING, new \DateInterval("P2D"));

        $this->assertEquals(Action::DRIVING, $rule->getDueAfter()["action"]);
        $this->assertEquals(2, $rule->getDueAfter()["period"]->d);
    }

    /**
     * @dataProvider providerForActionRelation
     */
    public function testActionRelation($inputRelation, $expectedRelation) : void
    {
        $rule = Rule::init()->withActionRelation($inputRelation);

        $this->assertEquals($expectedRelation, $rule->getActionRelation());
    }

    public function providerForActionRelation() : array
    {
        return [
            [Rule::RELATION_MIN, Rule::RELATION_MIN],
            [Rule::RELATION_MAX, Rule::RELATION_MAX],
        ];
    }
}
