<?php

namespace App\Render;

use App\Model\Commit;
use App\Service\DateService;
use DateTime;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractRender
{
    public function __construct(protected DateService $dateService, protected ParameterBagInterface $parameterBag)
    {
    }

    abstract public static function getDefaultPriority(): int;

    abstract public function execute(OutputInterface $output, array $commits): void;

    protected function addAverage(array $rows, bool $countZeros = true): array
    {
        foreach ($rows as $idx => $row) {
            $rows[$idx][] = $this->calculateAverage($row, $countZeros);
        }

        return $rows;
    }

    protected function addRow(array $commits, DateTime $startOfDay, DateTime $endOfDay, array $users, array $rows): array
    {
        $currentCommits = $this->filter($commits, $startOfDay, $endOfDay);
        foreach ($users as $user) {
            $rows[$user][] =
                array_reduce($currentCommits, function (int $carry, Commit $commit) use ($user) {
                    if ($commit->getUser() != $user) {
                        return $carry;
                    }

                    return $carry + $commit->getInserts();
                }, 0);
        }

        return $rows;
    }

    protected function calculateAverage(mixed $row, bool $countZeros = true): float
    {
        $count = count($row) - 1;
        if (!$countZeros) {
            $count = array_reduce($row, function (int $carry, $item) {
                if (!is_numeric($item)) {
                    return $carry;
                }
                if ($item <= 0) {
                    return $carry;
                }

                return $carry + 1;
            }, 0);
        }

        return round(
            array_reduce($row, function (int $carry, $item) {
                if (!is_numeric($item)) {
                    return $carry;
                }

                return $carry + $item;
            }, 0) / $count
        );
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
        foreach ($rows as $idx1 => $row) {
            foreach ($row as $idx2 => $cell) {
                $rows[$idx1][$idx2] = $this->createCell($cell);
            }
        }
        $this->renderHeader($output);
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($rows);

        $table->render();

        $output->writeln(['']);
    }

    abstract protected function renderHeader(OutputInterface $output);

    private function createCell(mixed $cell): TableCell
    {
        if (is_numeric($cell)) {
            return new TableCell(number_format($cell, 0), ['style' => new TableCellStyle(['align' => 'right',])]);
        }

        return new TableCell($cell);
    }
}
