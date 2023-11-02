<?php

namespace App\Render;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;

class Yearly extends AbstractRender
{
    public static function getDefaultPriority(): int
    {
        return 70;
    }

    public function execute(OutputInterface $output, array $commits): void
    {
        $current = $this->dateService->getStartOfYear(
            (new  DateTime())->modify(sprintf('-%d years', $this->parameterBag->get('app_years_to_show')))
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
            $headers[] = $current->format('Y');
            $rows = $this->addRow(
                $commits,
                $this->dateService->getStartOfYear($current),
                $this->dateService->getEndOfYear($current),
                $users,
                $rows
            );
            $current->modify('+1 year');
        }
        $headers[] = 'Avg';
        $rows = $this->addAverage($rows, false);


        $this->render($output, $headers, $rows);
    }

    protected function renderHeader(OutputInterface $output): void
    {
        $output->writeln(sprintf('<info>Yearly</info> (%d)', $this->parameterBag->get('app_years_to_show')));
    }
}
