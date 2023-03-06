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

    protected function addAverage(array $rows, bool $countZeros): array
    {
        foreach ($rows as $idx => $row) {
            $rows[$idx]['average'] = $this->calculateAverage($row, $countZeros);
        }

        return $rows;
    }

    protected function addRow(array $commits, DateTime $startOfDay, DateTime $endOfDay, array $users, array $rows): array
    {
        $currentCommits = $this->filter($commits, $startOfDay, $endOfDay);
        foreach ($users as $user) {
            $rows[$user]['dates'][$startOfDay->format('d/m/Y')] =
                array_reduce($currentCommits, function (int $carry, Commit $commit) use ($user) {
                    if ($commit->getUser() != $user) {
                        return $carry;
                    }

                    return $carry + $commit->getInserts();
                }, 0);
        }

        return $rows;
    }

    private function calculateAverage(mixed $row, bool $countZeros): float
    {
        $total = array_reduce($row['dates'], function (int $carry, $item) {
            return $carry + $item;
        }, 0);
        $count = count($row['dates']);
        if (!$countZeros) {
            $count = array_reduce($row['dates'], function (int $carry, $item) {
                if (0 < $item) {
                    return $carry + 1;
                }

                return $carry;
            }, 0);
        }

        return round($total / $count);
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
        $this->renderHeader($output);
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($this->getTableRows($rows));

        $table->render();

        $output->writeln(['']);
    }

    abstract protected function renderHeader(OutputInterface $output);

    private function cell(mixed $value): TableCell
    {
        if (is_numeric($value)) {
            return new TableCell(number_format($value, 0), ['style' => new TableCellStyle(['align' => 'right',])]);
        }

        return new TableCell($value);
    }

    private function getTableRows(array $rows): array
    {
        $rows = $this->sort($rows);
        $tableRows = [];
        foreach ($rows as $idx1 => $row) {
            $tableRows[$idx1]['user'] = $this->cell($row['user']);
            foreach ($row['dates'] as $idxDate => $value) {
                $tableRows[$idx1][$idxDate] = $this->cell($value);
            }
            $tableRows[$idx1]['average'] = $this->cell($row['average']);
        }

        return $tableRows;
    }

    private function sort(array $rows): array
    {
        usort($rows, function (array $row1, array $row2) {
            if ($row1['average'] == $row2['average']) {
                return 0;
            }

            return ($row1['average'] > $row2['average']) ? -1 : 1;
        });

        return $rows;
    }
}
