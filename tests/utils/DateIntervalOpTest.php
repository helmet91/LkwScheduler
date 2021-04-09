<?php declare(strict_types=1);

use helmet91\utils\DateIntervalOp;
use PHPUnit\Framework\TestCase;

class DateIntervalOpTest extends TestCase
{
    private DateInterval $a;
    private DateInterval $b;

    protected function setUp(): void
    {
        $this->a = new \DateInterval("P1WT1H5M12S");
        $this->b = new \DateInterval("P7DT2H5M3S");
    }

    public function testAddition() : void
    {
        $this->assertEquals(14, DateIntervalOp::add($this->a, $this->b)->d);
        $this->assertEquals(3, DateIntervalOp::add($this->a, $this->b)->h);
        $this->assertEquals(10, DateIntervalOp::add($this->a, $this->b)->i);
        $this->assertEquals(15, DateIntervalOp::add($this->a, $this->b)->s);
    }

    public function testSubtraction() : void
    {
        $this->assertEquals(59, DateIntervalOp::sub($this->b, $this->a)->i);
        $this->assertEquals(51, DateIntervalOp::sub($this->b, $this->a)->s);
    }

    /**
     * @dataProvider providerForLessThanComparison
     */
    public function testLessThanComparison($intervalPair, $expectedResult) : void
    {
        $this->assertEquals($expectedResult, DateIntervalOp::lessThan($this->{$intervalPair[0]}, $this->{$intervalPair[1]}));
    }

    public function providerForLessThanComparison() : array
    {
        return [
            [['a', 'b'], true],
            [['b', 'a'], false],
            [['b', 'b'], false],
        ];
    }

    /**
     * @dataProvider providerForGreaterThanComparison
     */
    public function testGreaterThanComparison($intervalPair, $expectedResult) : void
    {
        $this->assertEquals($expectedResult, DateIntervalOp::greaterThan($this->{$intervalPair[0]}, $this->{$intervalPair[1]}));
    }

    public function providerForGreaterThanComparison() : array
    {
        return [
            [['a', 'b'], false],
            [['b', 'a'], true],
            [['b', 'b'], false],
        ];
    }

    /**
     * @dataProvider providerForDivisionTest
     */
    public function testDividingIntervals($intervalPair, $expectedResult) : void
    {
        $a = new \DateInterval($intervalPair[0]);
        $b = new \DateInterval($intervalPair[1]);

        $this->assertEquals($expectedResult, DateIntervalOp::div($a, $b));
    }

    public function providerForDivisionTest() : array
    {
        return [
            [["P1D", "PT2H"], 12],
            [["PT1H", "PT30M"], 2],
            [["PT30M", "PT1H"], 0.5],
        ];
    }
}
