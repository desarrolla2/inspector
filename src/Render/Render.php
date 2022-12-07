<?php

namespace App\Render;

use Symfony\Component\Console\Output\OutputInterface;

class Render
{
    public function __construct(private iterable $renders = [])
    {
    }

    public function execute(OutputInterface $output, array $commits): void
    {
        /** @var AbstractRender $render */
        foreach ($this->renders as $render) {
            $render->execute($output, $commits);
        }
    }
}
