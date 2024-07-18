<?php

namespace App\Command;

use App\Model\Commit;
use App\Model\Email;
use App\Model\User;
use App\Render\Render;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'app:run',
    description: 'Show commits per user in a weekly, monthly and daily view.',
)]
class RunCommand extends Command
{
    private array $users;

    public function __construct(private Render $render)
    {
        return parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('target', InputArgument::REQUIRED, 'Target directory to analyze');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->calculateUsers();

        $process = new Process(
            ['/usr/bin/git', 'log', '--pretty=hash:%H%n email:%ae%n timestamp:%at%n date:%as%n subject:%s"', '--shortstat', '--no-merges', '--', ':(exclude)*.lock', ':(exclude)migrations/*'],
            $input->getArgument('target')
        );

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $lines = $this->getLines($process->getOutput());
        $commits = $this->calculateCommits($lines);

        $this->render->execute($output, $commits);

        return Command::SUCCESS;
    }

    /** @return Commit[] */
    private function calculateCommits(array $lines): array
    {
        $total = count($lines) - 6;
        $commits = [];
        for ($line = 0; $line < $total; $line += 6) {
            while (!str_starts_with($lines[$line], 'hash')) {
                $line++;
            }
            if (str_contains($lines[$line + 4], '[cs]')) {
                continue;
            }
            $commits[] = new Commit(
                str_replace('hash:', '', $lines[$line]),
                $this->getUserName(str_replace('email:', '', $lines[$line + 1])),
                trim($lines[$line + 4]),
                $this->getDateFromTimestamp((int) str_replace('timestamp:', '', $lines[$line + 2])),
                (int) trim(str_replace('ins', '', $this->findFirstByRegex($lines[$line + 5], '#[\d]+ ins#')))
            );
        }

        return $commits;
    }

    private function calculateUsers(): void
    {
        $rows = Yaml::parseFile('config/users.yaml');
        foreach ($rows['users'] as $row) {
            $emails = [];
            foreach ($row['emails'] as $emailAsString) {
                $emails[] = new Email($emailAsString);
            }
            $this->users[] = new User($row['name'], $emails);
        }
    }

    private function findFirstByRegex(string $string, string $regex): bool|string
    {
        if (preg_match($regex, $string, $match)) {
            return $match[0];
        }

        return false;
    }

    private function getDateFromTimestamp(int $timestamp): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        return $date;
    }

    private function getLines(string $body): array
    {
        $lines = explode("\n", $body);
        foreach ($lines as $idx => $line) {
            $lines[$idx] = trim($line);
            if ($lines[$idx] === '') {
                unset($lines[$idx]);
            }
        }

        return array_values($lines);
    }

    private function getUserName(string $emailAsString): string
    {
        /** @var User $user */
        foreach ($this->users as $user) {
            foreach ($user->getEmails() as $email) {
                if ($email->getEmail() == $emailAsString) {
                    return $user->getName();
                }
            }
        }

        return $emailAsString;
    }
}
