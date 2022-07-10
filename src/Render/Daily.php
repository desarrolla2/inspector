<?php

namespace App\Render;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;

class Daily extends AbstractRender
{
    public function execute(OutputInterface $output, array $commits)
    {
        $current = $this->dateService->getStartOfDay($this->dateService->getNextBusinessDay((new DateTime())->modify('-15 days')));
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
            $startOfDay = $this->dateService->getStartOfDay($current);
            $endOfDay = $this->dateService->getEndOfDay($current);
            $current = $this->dateService->getNextBusinessDay($current);
            $rows = $this->addRow($commits, $startOfDay, $endOfDay, $users, $rows);
        }

        $this->render($output, $headers, $rows);
    }
}
