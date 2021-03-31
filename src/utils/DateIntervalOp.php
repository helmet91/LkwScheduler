<?php declare(strict_types=1);

namespace helmet91\utils;

class DateIntervalOp
{
    public static function add(\DateInterval $left, \DateInterval $right) : \DateInterval
    {
        $date1 = new \DateTime();
        $date2 = clone $date1;

        $date1->add($left);
        $date1->add($right);

        return $date1->diff($date2, true);
    }

    public static function lessThan(\DateInterval $left, \DateInterval $right) : bool
    {
        $date1 = new \DateTime();
        $date2 = clone $date1;

        $date1->add($left);
        $date2->add($right);

        return $date1 < $date2;
    }
}