<?php

namespace App\Render;

use App\Model\Commit;
use App\Service\DateService;
use DateTime;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRender
{
    public function __construct(protected DateService $dateService)
    {
    }

    protected function filter(array $commits, DateTime $from, DateTime $to): array
    {
        return array_filter($commits, function (Commit $commit) use ($from, $to) {
            return $from <= $commit->getDate() && $commit->getDate() < $to;
        });
    }

    protected function getUsers(array $commits): array
    {
        $users = [];
        foreach ($commits as $commit) {
            $users[$commit->getUser()] = $commit->getUser();
        }
        sort($users);

        return $users;
    }

    protected function render(OutputInterface $output, array $headers, array $rows): void
    {
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($rows);
        $table->render();
    }

    protected function addRow(array $commits, DateTime $startOfDay, DateTime $endOfDay, array $users, array $rows): array
    {
        $currentCommits = $this->filter($commits, $startOfDay, $endOfDay);
        foreach ($users as $user) {
            $rows[$user][] = number_format(
                array_reduce($currentCommits, function (int $carry, Commit $commit) use ($user) {
                    if ($commit->getUser() != $user) {
                        return $carry;
                    }

                    return $carry + $commit->getInserts();
                }, 0),
                0
            );
        }

        return $rows;
    }
}
