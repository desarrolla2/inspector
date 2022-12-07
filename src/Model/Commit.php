<?php

namespace App\Model;

use DateTime;

class Commit
{
    public function __construct(private string $hash, private string $user, private string $subject, private DateTime $date, private int $inserts)
    {
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getInserts(): int
    {
        return $this->inserts;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
