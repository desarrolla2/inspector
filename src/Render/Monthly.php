<?php

namespace App\Render;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;

class Monthly extends AbstractRender
{
    public static function getDefaultPriority(): int
    {
        return 80;
    }

    public function execute(OutputInterface $output, array $commits): void
    {
        $current = $this->dateService->getStartOfMonth(
            (new  DateTime())->modify(sprintf('-%d months', $this->parameterBag->get('app_months_to_show')))
        );
        $commits = $this->filter($commits, $current, new DateTime());
        $headers = ['user'];
        $users = $this->getUsers($commits);
        $rows = [];
        foreach ($users as $user) {
            $rows[$user] = ['user' => $user];
        }
        $now = new DateTime();
        while ($current <= $now) {
            $headers[] = $current->format('M');
            $rows = $this->addRow(
                $commits,
                $this->dateService->getStartOfMonth($current),
                $this->dateService->getEndOfMonth($current),
                $users,
                $rows
            );
            $current->modify('+1 month');
        }
        $headers[] = 'Avg';
        $rows = $this->addAverage($rows, false);


        $this->render($output, $headers, $rows);
    }

    protected function renderHeader(OutputInterface $output): void
    {
        $output->writeln(sprintf('<info>Monthly</info> (%d)', $this->parameterBag->get('app_months_to_show')));
    }
}
