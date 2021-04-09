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

    public static function sub(\DateInterval $left, \DateInterval $right) : \DateInterval
    {
        $date1 = new \DateTime();
        $date2 = clone $date1;

        $date1->add($left);
        $date1->sub($right);

        return $date2->diff($date1);
    }

    public static function lessThan(\DateInterval $left, \DateInterval $right) : bool
    {
        list($date1, $date2) = self::addIntervalsToDates($left, $right);

        return $date1 < $date2;
    }

    public static function greaterThan(\DateInterval $left, \DateInterval $right) : bool
    {
        list($date1, $date2) = self::addIntervalsToDates($left, $right);

        return $date1 > $date2;
    }

    private static function addIntervalsToDates(\DateInterval $left, \DateInterval $right) : array
    {
        $date1 = new \DateTime("@0");
        $date2 = clone $date1;

        $date1->add($left);
        $date2->add($right);

        return [$date1, $date2];
    }

    public static function div(\DateInterval $left, \DateInterval $right) : float
    {
        list($date1, $date2) = self::addIntervalsToDates($left, $right);

        return $date1->getTimestamp() / $date2->getTimestamp();
    }
}