<?php

namespace App\Model;

class User
{
    public function __construct(private string $name, private array $emails)
    {
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function addEmail(Email $email): void
    {
        $this->emails[] = $email;
    }

    /**
     * @return Email[]
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
