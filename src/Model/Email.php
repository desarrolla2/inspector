<?php

namespace App\Model;

class Email
{
    public function __construct(private string $email)
    {
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
