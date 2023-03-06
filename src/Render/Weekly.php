<?php

namespace App\Render;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;

class Weekly extends AbstractRender
{
    public static function getDefaultPriority(): int
    {
        return 75;
    }

    public function execute(OutputInterface $output, array $commits): void
    {
        $current = $this->dateService->getStartOfWeek(
            (new  DateTime())->modify(sprintf('-%d weeks', $this->parameterBag->get('app_weeks_to_show')))
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
            $headers[] = $current->format('d/m');
            $rows = $this->addRow(
                $commits,
                $this->dateService->getStartOfWeek($current),
                $this->dateService->getEndOfWeek($current),
                $users,
                $rows
            );
            $current->modify('+7 days');
        }
        $headers[] = 'Avg';
        $rows = $this->addAverage($rows, false);

        $this->render($output, $headers, $rows);
    }

    protected function renderHeader(OutputInterface $output)
    {
        $output->writeln('<info>Weekly</info>');
    }
}
