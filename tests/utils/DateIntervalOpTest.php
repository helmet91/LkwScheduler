<?php declare(strict_types=1);

use helmet91\utils\DateIntervalOp;
use PHPUnit\Framework\TestCase;

class DateIntervalOpTest extends TestCase
{
    private DateInterval $left;
    private DateInterval $right;

    protected function setUp(): void
    {
        $this->left = new \DateInterval("P1WT1H5M12S");
        $this->right = new \DateInterval("P7DT2H5M3S");
    }

    public function testAddition() : void
    {
        $this->assertEquals(14, DateIntervalOp::add($this->left, $this->right)->d);
        $this->assertEquals(3, DateIntervalOp::add($this->left, $this->right)->h);
        $this->assertEquals(10, DateIntervalOp::add($this->left, $this->right)->i);
        $this->assertEquals(15, DateIntervalOp::add($this->left, $this->right)->s);
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
            [['left', 'right'], true],
            [['right', 'left'], false],
            [['right', 'right'], false],
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
            [['left', 'right'], false],
            [['right', 'left'], true],
            [['right', 'right'], false],
        ];
    }
}
