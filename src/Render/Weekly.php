<?php

namespace App\Render;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;

class Weekly extends AbstractRender
{
    public function execute(OutputInterface $output, array $commits)
    {
        $current = $this->dateService->getStartOfWeek((new  DateTime())->modify('-10 weeks'));
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
            $startOfWeek = $this->dateService->getStartOfWeek($current);
            $endOfWeek = $this->dateService->getEndOfWeek($current);
            $current->modify('+7 days');
            $rows = $this->addRow($commits, $startOfWeek, $endOfWeek, $users, $rows);
        }


        $this->render($output, $headers, $rows);
    }
}
