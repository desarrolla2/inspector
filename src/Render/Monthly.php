<?php

namespace App\Render;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;

class Monthly extends AbstractRender
{
    public function execute(OutputInterface $output, array $commits)
    {
        $current = $this->dateService->getStartOfMonth((new  DateTime())->modify('-6 months'));
        $commits = $this->filter($commits, $current, new DateTime());
        $headers = ['user'];
        $users = $this->getUsers($commits);
        $rows = [];
        foreach ($users as $user) {
            $rows[$user] = [$user];
        }
        $now = new DateTime();
        while ($current <= $now) {
            $headers[] = $current->format('d/m');
            $startOfWeek = $this->dateService->getStartOfMonth($current);
            $endOfWeek = $this->dateService->getEndOfMonth($current);
            $current->modify('+1 month');
            $rows = $this->addRow($commits, $startOfWeek, $endOfWeek, $users, $rows);
        }


        $this->render($output, $headers, $rows);
    }
}
