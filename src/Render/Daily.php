<?php

namespace App\Render;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;

class Daily extends AbstractRender
{
    protected function renderHeader(OutputInterface $output)
    {
        $output->writeln('<info>Daily</info>');
    }

    public function execute(OutputInterface $output, array $commits)
    {
        $current = $this->dateService->getStartOfDay(
            $this->dateService->getNextBusinessDay(
                (new DateTime())->modify(
                    sprintf('-%d days', $this->parameterBag->get('app_days_to_show'),)
                )
            )
        );
        $commits = $this->filter($commits, $current, new \DateTime());
        $headers = ['user'];
        $users = $this->getUsers($commits);
        $rows = [];
        foreach ($users as $user) {
            $rows[$user] = [$user];
        }
        $now = new DateTime();
        while ($current <= $now) {
            $headers[] = $current->format('D d');
            $rows = $this->addRow(
                $commits,
                $this->dateService->getStartOfDay($current),
                $this->dateService->getEndOfDay($current),
                $users,
                $rows
            );
            $current = $this->dateService->getNextBusinessDay($current);
        }

        $this->render($output, $headers, $rows);
    }
}
