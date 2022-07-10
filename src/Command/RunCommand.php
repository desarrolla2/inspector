<?php

namespace App\Command;

use App\Model\Commit;
use App\Render\Daily;
use App\Render\Monthly;
use App\Render\Weekly;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:run',
    description: 'Add a short description for your command',
)]
class RunCommand extends Command
{
    public function __construct( private ParameterBagInterface $parameterBag, private Daily $daily, private Weekly $weekly, private Monthly $monthly)
    {
        return parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(
            ['/usr/bin/git', 'log', '-5000', '--pretty=hash:%H%n email:%ae%n timestamp:%at%n date:%as%n subject:%s"', '--shortstat', '--no-merges',],
            $this->parameterBag->get('app_target_directory')
        );
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $lines = $this->getLines($process->getOutput());

        $commits = $this->createCommits($lines);

        $this->daily->execute($output, $commits);
        $this->weekly->execute($output, $commits);
        $this->monthly->execute($output, $commits);

        return Command::SUCCESS;
    }

    protected function normalizeEmail(string $email): string
    {
        if (in_array($email, ['dgonzalez@strongholdam.com', 'daniel@devtia.com', 'daniel@desarrolla2.com'])) {
            return 'dgonzalez@strongholdam.com';
        }
        if (in_array($email, ['acebrian@strongholdam.com', 'alvaro@devtia.com'])) {
            return 'acebrian@strongholdam.com';
        }
        if (in_array($email, ['amontealegre@strongholdam.com', '100672060+AntonioMontealegre@users.noreply.github.com',])) {
            return 'amontealegre@strongholdam.com';
        }

        if (in_array($email, ['smartin@strongholdam.com', 'sergyzen@gmail.com',])) {
            return 'smartin@strongholdam.com';
        }

        return $email;
    }

    protected function createCommits(array $lines): array
    {
        $total = count($lines) - 6;
        $commits = [];
        for ($line = 0; $line < $total; $line += 6) {
            while (!str_starts_with($lines[$line], 'hash')) {
                $line++;
            }
            $commits[] = new Commit(
                str_replace('hash:', '', $lines[$line]),
                $this->normalizeEmail(str_replace('email:', '', $lines[$line + 1])),
                trim($lines[$line + 4]),
                $this->getDateFromTimestamp((int) str_replace('timestamp:', '', $lines[$line + 2])),
                (int) trim(str_replace('ins', '', $this->findFirstByRegex($lines[$line + 5], '#[\d]+ ins#')))
            );
        }

        return $commits;
    }

    private function getDateFromTimestamp(int $timestamp): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        return $date;
    }

    private function findFirstByRegex(string $string, string $regex)
    {
        if (preg_match($regex, $string, $match)) {
            return $match[0];
        }

        return false;
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
}
