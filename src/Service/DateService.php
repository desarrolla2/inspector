<?php

namespace App\Service;

use DateTime;


class DateService
{
    public function getEndOfDay(DateTime $date): DateTime
    {
        $date = clone $date;
        $date->setTime(23, 59, 39);

        return $date;
    }

    public function getEndOfMonth(DateTime $date): DateTime
    {
        $date = $this->getStartOfMonth(clone $date);
        $date->modify('last day of this month');

        return $this->getEndOfDay($date);
    }

    public function getEndOfWeek(DateTime $date): DateTime
    {
        $date = $this->getStartOfWeek($date);
        $date->modify('+6 days');

        return $this->getEndOfDay($date);
    }

    public function getEndOfYear(\DateTime $date): \DateTime
    {
        $date = (new \DateTime())->setDate($date->format('Y'), 12, 31);

        return $this->getEndOfDay($date);
    }

    public function getNextBusinessDate(DateTime $date): DateTime
    {
        $date = clone $date;
        $date->modify('+1 day');
        if (!$this->isBusinessDay($date)) {
            return $this->getNextBusinessDate($date);
        }

        return $date;
    }

    public function getPreviousBusinessDate(DateTime $date): DateTime
    {
        $date = clone $date;
        $date->modify('-1 day');
        if (!$this->isBusinessDay($date)) {
            return $this->getPreviousBusinessDate($date);
        }

        return $date;
    }

    public function getStartOfDay(DateTime $date): DateTime
    {
        $date = clone $date;
        $date->setTime(0, 0, 0);

        return $date;
    }

    public function getStartOfMonth(DateTime $date): DateTime
    {
        $date = clone $date;
        $date->modify('first day of this month');

        return $this->getStartOfDay($date);
    }

    public function getStartOfWeek(DateTime $date): DateTime
    {
        $date = clone $date;
        if (0 == $date->format('w')) {
            $date->modify('-6 days');
        }
        if (1 == $date->format('w')) {
            return $this->getStartOfDay($date);
        }
        $date->modify(sprintf('-%d days', ($date->format('w') - 1)));

        return $this->getStartOfDay($date);
    }

    public function getStartOfYear(\DateTime $date): \DateTime
    {
        $date = (new \DateTime())->setDate($date->format('Y'), 1, 1);

        return $this->getStartOfDay($date);
    }

    public function isBusinessDay(DateTime $date): bool
    {
        return $date->format('N') < 6;
    }
}
